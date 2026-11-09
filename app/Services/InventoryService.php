<?php
/**
 * محرك وخدمات إدارة المخزون والمستودعات - Inventory Service
 * WAM Tech POS & ERP
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

class InventoryService {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * تسجيل حركة مخزنية واحدة بشكل ذري وحساب الرصيد اللحظي
     */
    public static function recordStockMove(
        int $itemId,
        int $storeId,
        string $moveType,
        float $quantity,
        float $unitCost = 0.0,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
        ?int $batchId = null,
        ?int $userId = null
    ): array {
        self::init();

        return self::$db->transaction(function() use ($itemId, $storeId, $moveType, $quantity, $unitCost, $referenceType, $referenceId, $notes, $batchId, $userId) {
            // 1. جلب آخر رصيد للصنف في هذا المخزن مع قفل السطر FOR UPDATE
            $stmt = self::$db->getPdo()->prepare("
                SELECT balance_after 
                FROM stock_moves 
                WHERE item_id = ? AND store_id = ? 
                ORDER BY id DESC 
                LIMIT 1 
                FOR UPDATE
            ");
            $stmt->execute([$itemId, $storeId]);
            $prevBalance = (float)($stmt->fetchColumn() ?: 0.0);

            // 2. حساب الرصيد الجديد
            $newBalance = $prevBalance + $quantity;

            // 3. التحقق من تكلفة الوحدة إذا لم تُمرر
            if ($unitCost <= 0.0) {
                $itemCost = self::$db->fetchValue("SELECT cost_price FROM items WHERE id = ?", [$itemId]);
                $unitCost = (float)($itemCost ?: 0.0);
            }

            // 4. إدراج الحركة المخزنية
            $moveId = self::$db->insert('stock_moves', [
                'item_id'        => $itemId,
                'store_id'       => $storeId,
                'batch_id'       => $batchId,
                'move_type'      => $moveType,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'quantity'       => $quantity,
                'unit_cost'      => $unitCost,
                'balance_after'  => $newBalance,
                'notes'          => $notes,
                'created_by'     => $userId ?: ($_SESSION['user_id'] ?? null)
            ]);

            // 5. إذا كانت مرتبطة بتشغيلة، نحدّث رصيد التشغيلة
            if ($batchId) {
                self::$db->getPdo()->prepare("
                    UPDATE item_batches 
                    SET quantity = quantity + ? 
                    WHERE id = ?
                ")->execute([$quantity, $batchId]);
            }

            return [
                'move_id'         => $moveId,
                'item_id'         => $itemId,
                'store_id'        => $storeId,
                'quantity'        => $quantity,
                'previous_balance'=> $prevBalance,
                'balance_after'   => $newBalance
            ];
        });
    }

    /**
     * تسوية جردية سريعة (Inventory Adjustment)
     */
    public static function adjustStock(int $itemId, int $storeId, float $actualQuantity, string $reason, ?int $userId = null): array {
        self::init();

        // جلب الرصيد الدفتري الحالي
        $stmt = self::$db->getPdo()->prepare("
            SELECT balance_after 
            FROM stock_moves 
            WHERE item_id = ? AND store_id = ? 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute([$itemId, $storeId]);
        $currentBalance = (float)($stmt->fetchColumn() ?: 0.0);

        $difference = $actualQuantity - $currentBalance;
        if (abs($difference) < 0.0001) {
            return [
                'success' => true,
                'message' => 'الرصيد الفعلي مطابق للرصيد الدفتري، لا توجد تسوية لازمة.',
                'balance' => $actualQuantity
            ];
        }

        $note = "تسوية جردية: الرصيد الدفتري ({$currentBalance}) - الفعلي ({$actualQuantity}) - السبب: {$reason}";

        $res = self::recordStockMove(
            $itemId,
            $storeId,
            'inventory_adjustment',
            $difference,
            0.0,
            'inventory_audit',
            null,
            $note,
            null,
            $userId
        );

        return [
            'success' => true,
            'difference' => $difference,
            'new_balance' => $res['balance_after'],
            'move_id' => $res['move_id']
        ];
    }

    /**
     * تحويل مخزني بين مستودعين بالكامل داخل ACID Transaction
     */
    public static function transferStock(int $fromStoreId, int $toStoreId, array $items, string $notes = '', ?int $userId = null): array {
        self::init();

        if ($fromStoreId === $toStoreId) {
            throw new Exception("لا يمكن التحويل من وإلى نفس المستودع.");
        }

        if (empty($items)) {
            throw new Exception("يجب تحديد صنف واحد على الأقل للتحويل.");
        }

        $userId = $userId ?: ($_SESSION['user_id'] ?? 1);

        return self::$db->transaction(function() use ($fromStoreId, $toStoreId, $items, $notes, $userId) {
            $transferNumber = 'TR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            // إنشاء إذن التحويل
            $transferId = self::$db->insert('store_transfers', [
                'transfer_number' => $transferNumber,
                'from_store_id'   => $fromStoreId,
                'to_store_id'     => $toStoreId,
                'status'          => 'approved',
                'total_items'     => count($items),
                'notes'           => $notes,
                'created_by'      => $userId,
                'approved_by'     => $userId,
                'approved_at'     => date('Y-m-d H:i:s')
            ]);

            foreach ($items as $item) {
                $itemId = (int)$item['item_id'];
                $qty = (float)$item['quantity'];
                $batchId = !empty($item['batch_id']) ? (int)$item['batch_id'] : null;

                if ($qty <= 0) {
                    continue;
                }

                // فحص توفر الرصيد في المخزن المصدر
                $stmt = self::$db->getPdo()->prepare("
                    SELECT balance_after 
                    FROM stock_moves 
                    WHERE item_id = ? AND store_id = ? 
                    ORDER BY id DESC 
                    LIMIT 1 
                    FOR UPDATE
                ");
                $stmt->execute([$itemId, $fromStoreId]);
                $avail = (float)($stmt->fetchColumn() ?: 0.0);

                if ($avail < $qty) {
                    $itemName = self::$db->fetchValue("SELECT name FROM items WHERE id = ?", [$itemId]);
                    throw new Exception("الرصيد غير كافٍ في المخزن المصدر للصنف: '{$itemName}'. المتاح: {$avail}، المطلوب: {$qty}");
                }

                $cost = (float)self::$db->fetchValue("SELECT cost_price FROM items WHERE id = ?", [$itemId]);

                // تسجيل خروج من المخزن المصدر (transfer_out)
                self::recordStockMove(
                    $itemId,
                    $fromStoreId,
                    'transfer_out',
                    -$qty,
                    $cost,
                    'store_transfer',
                    $transferId,
                    "تحويل صادر إلى إذن رقم {$transferNumber}",
                    $batchId,
                    $userId
                );

                // تسجيل دخول في المخزن المستلم (transfer_in)
                self::recordStockMove(
                    $itemId,
                    $toStoreId,
                    'transfer_in',
                    +$qty,
                    $cost,
                    'store_transfer',
                    $transferId,
                    "تحويل وارد من إذن رقم {$transferNumber}",
                    $batchId,
                    $userId
                );

                // إدراج تفاصيل البند في جدول التحويل
                self::$db->insert('store_transfer_items', [
                    'transfer_id' => $transferId,
                    'item_id'     => $itemId,
                    'batch_id'    => $batchId,
                    'quantity'    => $qty,
                    'unit_cost'   => $cost,
                    'notes'       => $item['notes'] ?? null
                ]);
            }

            return [
                'success' => true,
                'transfer_id' => $transferId,
                'transfer_number' => $transferNumber
            ];
        });
    }

    /**
     * تنبيهات الأصناف التي وصلت لحد إعادة الطلب
     */
    public static function getLowStockAlerts(?int $storeId = null): array {
        self::init();

        $storeFilter = $storeId ? "AND sm.store_id = " . (int)$storeId : "";

        $sql = "
            SELECT 
                i.id,
                i.barcode,
                i.name,
                i.reorder_level,
                c.name AS category_name,
                u.name AS unit_name,
                COALESCE(curr_stock.total_stock, 0) AS current_stock
            FROM items i
            LEFT JOIN categories c ON i.category_id = c.id
            LEFT JOIN units u ON i.unit_id = u.id
            INNER JOIN (
                SELECT sm.item_id, SUM(sm.balance_after) AS total_stock
                FROM stock_moves sm
                INNER JOIN (
                    SELECT item_id, store_id, MAX(id) as max_id
                    FROM stock_moves
                    GROUP BY item_id, store_id
                ) grouped ON sm.id = grouped.max_id
                WHERE 1=1 {$storeFilter}
                GROUP BY sm.item_id
            ) curr_stock ON i.id = curr_stock.item_id
            WHERE i.is_active = 1 AND curr_stock.total_stock <= i.reorder_level
            ORDER BY (curr_stock.total_stock - i.reorder_level) ASC
        ";

        return self::$db->fetchAll($sql);
    }

    /**
     * تنبيهات التشغيلات القريبة من انتهاء الصلاحية
     */
    public static function getExpiringBatchesAlerts(int $daysAhead = 30, ?int $storeId = null): array {
        self::init();

        $params = [$daysAhead];
        $storeFilter = "";
        if ($storeId) {
            $storeFilter = "AND b.store_id = ?";
            $params[] = $storeId;
        }

        $sql = "
            SELECT 
                b.id AS batch_id,
                b.batch_number,
                b.expiry_date,
                b.quantity,
                DATEDIFF(b.expiry_date, CURDATE()) AS days_remaining,
                i.id AS item_id,
                i.barcode,
                i.name AS item_name,
                s.name AS store_name
            FROM item_batches b
            INNER JOIN items i ON b.item_id = i.id
            INNER JOIN stores s ON b.store_id = s.id
            WHERE b.quantity > 0 
              AND b.expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
              {$storeFilter}
            ORDER BY b.expiry_date ASC
        ";

        return self::$db->fetchAll($sql, $params);
    }

    /**
     * تقييم المخزون المالي الإجمالي
     */
    public static function getInventoryValuation(?int $storeId = null): array {
        self::init();

        $storeFilter = $storeId ? "AND sm.store_id = " . (int)$storeId : "";

        $sql = "
            SELECT 
                COUNT(DISTINCT i.id) AS total_items_count,
                COALESCE(SUM(current_stock.stock_qty), 0) AS total_units_in_stock,
                COALESCE(SUM(current_stock.stock_qty * i.cost_price), 0) AS total_cost_valuation,
                COALESCE(SUM(current_stock.stock_qty * i.retail_price), 0) AS total_retail_valuation
            FROM items i
            INNER JOIN (
                SELECT sm.item_id, SUM(sm.balance_after) AS stock_qty
                FROM stock_moves sm
                INNER JOIN (
                    SELECT item_id, store_id, MAX(id) as max_id
                    FROM stock_moves
                    GROUP BY item_id, store_id
                ) grouped ON sm.id = grouped.max_id
                WHERE 1=1 {$storeFilter}
                GROUP BY sm.item_id
                HAVING stock_qty > 0
            ) current_stock ON i.id = current_stock.item_id
            WHERE i.is_active = 1
        ";

        $row = self::$db->fetch($sql);
        $costVal = (float)($row['total_cost_valuation'] ?? 0.0);
        $retailVal = (float)($row['total_retail_valuation'] ?? 0.0);
        $potentialProfit = $retailVal - $costVal;
        $profitMarginPct = ($costVal > 0) ? ($potentialProfit / $costVal) * 100 : 0.0;

        return [
            'total_items_count'      => (int)($row['total_items_count'] ?? 0),
            'total_units_in_stock'   => (float)($row['total_units_in_stock'] ?? 0),
            'total_cost_valuation'   => $costVal,
            'total_retail_valuation' => $retailVal,
            'potential_profit'       => $potentialProfit,
            'profit_margin_pct'      => round($profitMarginPct, 2)
        ];
    }
}

