<?php
/**
 * محرك وخدمات المردودات (مبيعات ومشتريات) - Returns & Refunds Service
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

class ReturnService {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * توليد متسلسل إشعار المرتجع (SRET / PRET)
     */
    public static function generateReturnNumber(string $prefix = 'SRET'): string {
        self::init();
        $dateKey = date('Ymd');
        $pdo = self::$db->getPdo();

        $stmt = $pdo->prepare("
            INSERT INTO invoice_sequences (prefix, date_key, last_number) 
            VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE last_number = last_number + 1
        ");
        $stmt->execute([$prefix, $dateKey]);

        $seqStmt = $pdo->prepare("SELECT last_number FROM invoice_sequences WHERE prefix = ? AND date_key = ?");
        $seqStmt->execute([$prefix, $dateKey]);
        $lastNumber = (int)$seqStmt->fetchColumn();

        return sprintf('%s-%s-%04d', $prefix, $dateKey, $lastNumber);
    }

    /**
     * معالجة مرتجع مبيعات من عميل (Sales Return)
     * استرجاع البضاعة للمخزن وتعديل الخزينة أو رصيد العميل
     */
    public static function processSaleReturn(array $data, int $userId): array {
        self::init();

        $items = $data['items'] ?? [];
        if (empty($items) || !is_array($items)) {
            throw new Exception("يجب تحديد صنف واحد على الأقل للمرتجع.");
        }

        $storeId = (int)($data['store_id'] ?? 2);
        $customerId = (int)($data['customer_id'] ?? 1);
        $saleId = !empty($data['sale_id']) ? (int)$data['sale_id'] : null;

        return self::$db->transaction(function() use ($data, $items, $storeId, $customerId, $saleId, $userId) {
            $returnNumber = self::generateReturnNumber('SRET');

            $subtotal = 0.0;
            $taxAmount = 0.0;
            $processedItems = [];

            foreach ($items as $item) {
                $itemId = (int)($item['item_id'] ?? 0);
                $qty = (float)($item['quantity'] ?? 0.0);
                $unitPrice = (float)($item['unit_price'] ?? 0.0);
                $condition = ($item['condition_state'] ?? 'good') === 'damaged' ? 'damaged' : 'good';

                if ($itemId <= 0 || $qty <= 0) continue;

                $lineSubtotal = round($qty * $unitPrice, 2);
                $lineTax = round($lineSubtotal * (14.0 / 100), 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $subtotal += $lineSubtotal;
                $taxAmount += $lineTax;

                $processedItems[] = [
                    'item_id'         => $itemId,
                    'quantity'        => $qty,
                    'unit_price'      => $unitPrice,
                    'tax_amount'      => $lineTax,
                    'total_refund'    => $lineTotal,
                    'condition_state' => $condition
                ];
            }

            if (empty($processedItems)) {
                throw new Exception("لم يتم تقديم أصناف صالحة للإرجاع.");
            }

            $totalRefund = round($subtotal + $taxAmount, 2);
            $refundMethod = $data['refund_method'] ?? 'cash';

            // 1. إدراج رأس إشعار مرتجع المبيعات
            $returnId = (int)self::$db->insert('sales_returns', [
                'return_number' => $returnNumber,
                'sale_id'       => $saleId,
                'customer_id'   => $customerId,
                'store_id'      => $storeId,
                'return_date'   => $data['return_date'] ?? date('Y-m-d'),
                'subtotal'      => $subtotal,
                'tax_amount'    => $taxAmount,
                'total_refund'  => $totalRefund,
                'refund_method' => $refundMethod,
                'reason'        => trim($data['reason'] ?? ''),
                'notes'         => trim($data['notes'] ?? ''),
                'created_by'    => $userId
            ]);

            // 2. إدراج البنود واسترجاع الكميات السليمة للمخزن
            foreach ($processedItems as $pItem) {
                $pItem['return_id'] = $returnId;
                self::$db->insert('sales_return_items', $pItem);

                $itemId = $pItem['item_id'];
                $qty = (float)$pItem['quantity'];

                if ($pItem['condition_state'] === 'good') {
                    // استرجاع الرصيد الحالي
                    $currentStock = (float)self::$db->fetchValue("
                        SELECT balance_after FROM stock_moves 
                        WHERE item_id = ? AND store_id = ? 
                        ORDER BY id DESC LIMIT 1
                    ", [$itemId, $storeId]);

                    $newBalance = $currentStock + $qty;

                    // قيد حركة مخزنية موجبة بنوع 'sale_return'
                    self::$db->insert('stock_moves', [
                        'item_id'        => $itemId,
                        'store_id'       => $storeId,
                        'move_type'      => 'sale_return',
                        'reference_type' => 'sales_returns',
                        'reference_id'   => $returnId,
                        'quantity'       => $qty,
                        'unit_cost'      => 0.00,
                        'balance_after'  => $newBalance,
                        'notes'          => 'إرجاع بضاعة سليمة من عميل بإشعار ' . $returnNumber,
                        'created_by'     => $userId
                    ]);
                }
            }

            // 3. تسوية حساب العميل في حال الاسترداد الآجل (إضافة رصيد دائن للعميل أو خصم من مديونيته)
            if ($refundMethod === 'credit_balance' && $customerId > 0) {
                $currentCustomerBalance = (float)self::$db->fetchValue("SELECT current_balance FROM customers WHERE id = ?", [$customerId]);
                self::$db->update('customers', [
                    'current_balance' => $currentCustomerBalance - $totalRefund
                ], 'id = :id', [':id' => $customerId]);
            }

            Logger::audit('sale_return_created', 'sales_returns', $returnId, null, [
                'return_number' => $returnNumber,
                'total_refund'  => $totalRefund,
                'customer_id'   => $customerId
            ]);

            return [
                'success'       => true,
                'return_id'     => $returnId,
                'return_number' => $returnNumber,
                'total_refund'  => $totalRefund,
                'message'       => "تم تسجيل مرتجع المبيعات رقم {$returnNumber} بنجاح."
            ];
        });
    }

    /**
     * معالجة مرتجع مشتريات لمورد (Purchase Return)
     * خصم البضاعة من المخزن وتسوية إشعار دائن لحساب المورد
     */
    public static function processPurchaseReturn(array $data, int $userId): array {
        self::init();

        $items = $data['items'] ?? [];
        if (empty($items) || !is_array($items)) {
            throw new Exception("يجب تحديد صنف واحد على الأقل للمرتجع.");
        }

        $supplierId = (int)($data['supplier_id'] ?? 0);
        if ($supplierId <= 0) {
            throw new Exception("يجب تحديد المورد المراد إرجاع البضاعة إليه.");
        }

        $supplier = Supplier::findById($supplierId);
        if (!$supplier) {
            throw new Exception("المورد غير موجود.");
        }

        $storeId = (int)($data['store_id'] ?? 1);
        $purchaseId = !empty($data['purchase_id']) ? (int)$data['purchase_id'] : null;

        return self::$db->transaction(function() use ($data, $items, $supplierId, $supplier, $storeId, $purchaseId, $userId) {
            $returnNumber = self::generateReturnNumber('PRET');

            $subtotal = 0.0;
            $taxAmount = 0.0;
            $processedItems = [];

            foreach ($items as $item) {
                $itemId = (int)($item['item_id'] ?? 0);
                $qty = (float)($item['quantity'] ?? 0.0);
                $unitCost = (float)($item['unit_cost'] ?? 0.0);
                $batchNumber = trim($item['batch_number'] ?? '');

                if ($itemId <= 0 || $qty <= 0) continue;

                // التحقق من توفر الرصيد الكافي للإرجاع بالمستودع
                $currentStock = (float)self::$db->fetchValue("
                    SELECT balance_after FROM stock_moves 
                    WHERE item_id = ? AND store_id = ? 
                    ORDER BY id DESC LIMIT 1
                ", [$itemId, $storeId]);

                if ($currentStock < $qty) {
                    $itemName = self::$db->fetchValue("SELECT name FROM items WHERE id = ?", [$itemId]);
                    throw new Exception("الرصيد المتوفر في المخزن من ({$itemName}) هو {$currentStock} ولا يكفي لإرجاع كمية {$qty}.");
                }

                $lineSubtotal = round($qty * $unitCost, 2);
                $lineTax = round($lineSubtotal * (14.0 / 100), 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $subtotal += $lineSubtotal;
                $taxAmount += $lineTax;

                $processedItems[] = [
                    'item_id'      => $itemId,
                    'quantity'     => $qty,
                    'unit_cost'    => $unitCost,
                    'tax_amount'   => $lineTax,
                    'total_cost'   => $lineTotal,
                    'batch_number' => $batchNumber ?: null
                ];
            }

            if (empty($processedItems)) {
                throw new Exception("لم يتم تقديم أصناف صالحة للإرجاع.");
            }

            $totalAmount = round($subtotal + $taxAmount, 2);
            $refundStatus = $data['refund_status'] ?? 'credited_to_balance';

            // 1. إدراج رأس إشعار مرتجع المشتريات
            $returnId = (int)self::$db->insert('purchase_returns', [
                'return_number' => $returnNumber,
                'purchase_id'   => $purchaseId,
                'supplier_id'   => $supplierId,
                'store_id'      => $storeId,
                'return_date'   => $data['return_date'] ?? date('Y-m-d'),
                'subtotal'      => $subtotal,
                'tax_amount'    => $taxAmount,
                'total_amount'  => $totalAmount,
                'refund_status' => $refundStatus,
                'reason'        => trim($data['reason'] ?? ''),
                'notes'         => trim($data['notes'] ?? ''),
                'created_by'    => $userId
            ]);

            // 2. إدراج البنود وخصم الكميات من المخزن عبر stock_moves
            foreach ($processedItems as $pItem) {
                $pItem['return_id'] = $returnId;
                self::$db->insert('purchase_return_items', $pItem);

                $itemId = $pItem['item_id'];
                $qty = (float)$pItem['quantity'];

                $currentStock = (float)self::$db->fetchValue("
                    SELECT balance_after FROM stock_moves 
                    WHERE item_id = ? AND store_id = ? 
                    ORDER BY id DESC LIMIT 1
                ", [$itemId, $storeId]);

                $newBalance = $currentStock - $qty;

                // تسجيل حركة مخزنية سالبة بنوع 'purchase_return'
                self::$db->insert('stock_moves', [
                    'item_id'        => $itemId,
                    'store_id'       => $storeId,
                    'move_type'      => 'purchase_return',
                    'reference_type' => 'purchase_returns',
                    'reference_id'   => $returnId,
                    'quantity'       => (-1 * $qty),
                    'unit_cost'      => $pItem['unit_cost'],
                    'balance_after'  => $newBalance,
                    'notes'          => 'إرجاع بضاعة للمورد بإشعار ' . $returnNumber,
                    'created_by'     => $userId
                ]);
            }

            // 3. خصم القيمة من مديونية المورد
            $newSupplierBalance = (float)$supplier['current_balance'] - $totalAmount;
            self::$db->update('suppliers', [
                'current_balance' => $newSupplierBalance
            ], 'id = :id', [':id' => $supplierId]);

            Logger::audit('purchase_return_created', 'purchase_returns', $returnId, null, [
                'return_number' => $returnNumber,
                'total_amount'  => $totalAmount,
                'supplier_id'   => $supplierId
            ]);

            return [
                'success'       => true,
                'return_id'     => $returnId,
                'return_number' => $returnNumber,
                'total_amount'  => $totalAmount,
                'message'       => "تم تسجيل مرتجع المشتريات رقم {$returnNumber} وخصم قيمته من مديونية المورد بنجاح."
            ];
        });
    }

    /**
     * استرجاع سجل مرتجعات المبيعات
     */
    public static function getSalesReturns(array $filters = [], int $limit = 100): array {
        self::init();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $where[] = "sr.return_date BETWEEN ? AND ?";
            $params[] = $filters['date_from'];
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $s = '%' . trim($filters['search']) . '%';
            $where[] = "(sr.return_number LIKE ? OR c.name LIKE ? OR c.phone LIKE ?)";
            $params[] = $s;
            $params[] = $s;
            $params[] = $s;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT sr.*,
                   c.name AS customer_name, c.phone AS customer_phone,
                   st.name AS store_name,
                   u.full_name AS created_by_name,
                   s.invoice_number AS original_invoice_number
            FROM sales_returns sr
            LEFT JOIN customers c ON sr.customer_id = c.id
            LEFT JOIN stores st ON sr.store_id = st.id
            LEFT JOIN users u ON sr.created_by = u.id
            LEFT JOIN sales s ON sr.sale_id = s.id
            WHERE {$whereClause}
            ORDER BY sr.return_date DESC, sr.id DESC
            LIMIT {$limit}
        ";

        return self::$db->fetchAll($sql, $params);
    }

    /**
     * استرجاع سجل مرتجعات المشتريات
     */
    public static function getPurchaseReturns(array $filters = [], int $limit = 100): array {
        self::init();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $where[] = "pr.return_date BETWEEN ? AND ?";
            $params[] = $filters['date_from'];
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $s = '%' . trim($filters['search']) . '%';
            $where[] = "(pr.return_number LIKE ? OR sup.name LIKE ? OR sup.phone LIKE ?)";
            $params[] = $s;
            $params[] = $s;
            $params[] = $s;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT pr.*,
                   sup.name AS supplier_name, sup.phone AS supplier_phone,
                   st.name AS store_name,
                   u.full_name AS created_by_name,
                   p.invoice_number AS original_purchase_number
            FROM purchase_returns pr
            LEFT JOIN suppliers sup ON pr.supplier_id = sup.id
            LEFT JOIN stores st ON pr.store_id = st.id
            LEFT JOIN users u ON pr.created_by = u.id
            LEFT JOIN purchases p ON pr.purchase_id = p.id
            WHERE {$whereClause}
            ORDER BY pr.return_date DESC, pr.id DESC
            LIMIT {$limit}
        ";

        return self::$db->fetchAll($sql, $params);
    }
}
