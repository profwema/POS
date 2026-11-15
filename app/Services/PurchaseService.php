<?php
/**
 * محرك وخدمات المشتريات وسلاسل الإمداد - Purchase & Supply Chain Service
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

class PurchaseService {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * توليد متسلسل رقم فاتورة الشراء بشكل ذري لمنع التصادم
     * الصيغة: PUR-YYYYMMDD-0001
     */
    public static function generateInvoiceNumber(string $prefix = 'PUR'): string {
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
     * تسجيل فاتورة مشتريات جديدة ذرياً وتوريد البضاعة وإعادة احتساب متوسط التكلفة المرجح
     */
    public static function createPurchase(array $data, int $userId): array {
        self::init();

        $items = $data['items'] ?? [];
        if (empty($items) || !is_array($items)) {
            throw new Exception("يجب إضافة أصناف في فاتورة المشتريات.");
        }

        $supplierId = (int)($data['supplier_id'] ?? 0);
        if ($supplierId <= 0) {
            throw new Exception("يجب تحديد المورد.");
        }

        $supplier = Supplier::findById($supplierId);
        if (!$supplier) {
            throw new Exception("المورد المحدد غير موجود.");
        }

        $storeId = (int)($data['store_id'] ?? 1); // المستودع المستلم (الافتراضي: المستودع الرئيسي 1)

        return self::$db->transaction(function() use ($data, $items, $supplierId, $supplier, $storeId, $userId) {
            $invoiceNumber = self::generateInvoiceNumber('PUR');

            $subtotal = 0.0;
            $totalTax = 0.0;
            $processedItems = [];

            // 1. معالجة وتدقيق بنود الفاتورة
            foreach ($items as $item) {
                $itemId = (int)($item['item_id'] ?? 0);
                $qty = (float)($item['quantity'] ?? 0.0);
                $unitCost = (float)($item['unit_cost'] ?? 0.0);
                $taxPercent = isset($item['tax_percent']) ? (float)$item['tax_percent'] : 14.0;
                $batchNumber = trim($item['batch_number'] ?? '');
                $expireDate = !empty($item['expire_date']) ? $item['expire_date'] : null;

                if ($itemId <= 0 || $qty <= 0) continue;

                $lineSubtotal = round($qty * $unitCost, 2);
                $lineTax = round($lineSubtotal * ($taxPercent / 100), 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $subtotal += $lineSubtotal;
                $totalTax += $lineTax;

                $processedItems[] = [
                    'item_id'      => $itemId,
                    'quantity'     => $qty,
                    'unit_cost'    => $unitCost,
                    'tax_percent'  => $taxPercent,
                    'tax_amount'   => $lineTax,
                    'total_cost'   => $lineTotal,
                    'batch_number' => $batchNumber ?: null,
                    'expire_date'  => $expireDate
                ];
            }

            if (empty($processedItems)) {
                throw new Exception("لا توجد بنود صالحة في الفاتورة.");
            }

            $discountAmount = (float)($data['discount_amount'] ?? 0.0);
            $grandTotal = max(0, round($subtotal + $totalTax - $discountAmount, 2));

            $paidAmount = (float)($data['paid_amount'] ?? 0.0);
            $paidAmount = min($paidAmount, $grandTotal);
            $remainingAmount = max(0, round($grandTotal - $paidAmount, 2));

            // تحديد حالة السداد
            if ($remainingAmount <= 0.001) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'unpaid';
            }

            $paymentMethod = $data['payment_method'] ?? ($paymentStatus === 'unpaid' ? 'credit' : 'cash');

            // 2. إدراج رأس فاتورة المشتريات
            $purchaseId = (int)self::$db->insert('purchases', [
                'invoice_number'          => $invoiceNumber,
                'supplier_invoice_number' => trim($data['supplier_invoice_number'] ?? ''),
                'supplier_id'             => $supplierId,
                'store_id'                => $storeId,
                'branch_id'               => (int)($data['branch_id'] ?? 1),
                'invoice_date'            => $data['invoice_date'] ?? date('Y-m-d'),
                'subtotal'                => $subtotal,
                'discount_amount'         => $discountAmount,
                'tax_amount'              => $totalTax,
                'grand_total'             => $grandTotal,
                'paid_amount'             => $paidAmount,
                'remaining_amount'        => $remainingAmount,
                'payment_status'          => $paymentStatus,
                'payment_method'          => $paymentMethod,
                'notes'                   => trim($data['notes'] ?? ''),
                'created_by'              => $userId
            ]);

            // 3. إدراج البنود وتوريد المخزون وحساب متوسط التكلفة المرجح (WAC)
            foreach ($processedItems as $pItem) {
                $pItem['purchase_id'] = $purchaseId;
                self::$db->insert('purchase_items', $pItem);

                $itemId = $pItem['item_id'];
                $inQty = (float)$pItem['quantity'];
                $inCost = (float)$pItem['unit_cost'];

                // أ. فحص بيانات الصنف السابقة
                $itemRow = self::$db->fetchOne("SELECT * FROM items WHERE id = ? FOR UPDATE", [$itemId]);
                $oldCost = (float)($itemRow['cost_price'] ?? 0.0);

                // ب. استرجاع الرصيد الحالي بالمستودع
                $currentStoreStock = (float)self::$db->fetchValue("
                    SELECT balance_after FROM stock_moves 
                    WHERE item_id = ? AND store_id = ? 
                    ORDER BY id DESC LIMIT 1
                ", [$itemId, $storeId]);

                $newBalance = $currentStoreStock + $inQty;

                // ج. استرجاع إجمالي المخزون في جميع المستودعات قبل هذه الشحنة لحساب متوسط التكلفة المرجح العام
                $totalStockAllStores = (float)self::$db->fetchValue("
                    SELECT COALESCE(SUM(quantity), 0) FROM stock_moves WHERE item_id = ?
                ", [$itemId]);
                $totalStockAllStores = max(0, $totalStockAllStores);

                // د. حساب متوسط التكلفة المرجح (Weighted Average Cost):
                // New_Cost = ((Old_Stock * Old_Cost) + (In_Qty * In_Cost)) / (Old_Stock + In_Qty)
                if (($totalStockAllStores + $inQty) > 0) {
                    $newWeightedCost = (($totalStockAllStores * $oldCost) + ($inQty * $inCost)) / ($totalStockAllStores + $inQty);
                    $newWeightedCost = round($newWeightedCost, 2);
                    self::$db->update('items', ['cost_price' => $newWeightedCost], 'id = :id', [':id' => $itemId]);
                } else {
                    $newWeightedCost = $inCost;
                    self::$db->update('items', ['cost_price' => $newWeightedCost], 'id = :id', [':id' => $itemId]);
                }

                // هـ. إدارة الباتشات وتواريخ الصلاحية
                $batchId = null;
                if (!empty($pItem['batch_number']) && !empty($pItem['expire_date'])) {
                    $existingBatch = self::$db->fetchOne("
                        SELECT id, quantity FROM item_batches 
                        WHERE item_id = ? AND store_id = ? AND batch_number = ?
                    ", [$itemId, $storeId, $pItem['batch_number']]);

                    if ($existingBatch) {
                        $batchId = (int)$existingBatch['id'];
                        self::$db->update('item_batches', [
                            'quantity'   => (float)$existingBatch['quantity'] + $inQty,
                            'cost_price' => $inCost
                        ], 'id = :id', [':id' => $batchId]);
                    } else {
                        $batchId = (int)self::$db->insert('item_batches', [
                            'item_id'      => $itemId,
                            'store_id'     => $storeId,
                            'batch_number' => $pItem['batch_number'],
                            'expiry_date'  => $pItem['expire_date'],
                            'quantity'     => $inQty,
                            'cost_price'   => $inCost
                        ]);
                    }
                }

                // و. تسجيل حركة المخزون في stock_moves بنوع 'purchase'
                self::$db->insert('stock_moves', [
                    'item_id'        => $itemId,
                    'store_id'       => $storeId,
                    'batch_id'       => $batchId,
                    'move_type'      => 'purchase',
                    'reference_type' => 'purchases',
                    'reference_id'   => $purchaseId,
                    'quantity'       => $inQty,
                    'unit_cost'      => $inCost,
                    'balance_after'  => $newBalance,
                    'notes'          => 'توريد مشتريات بفاتورة رقم ' . $invoiceNumber,
                    'created_by'     => $userId
                ]);
            }

            // 4. تحديث رصيد المورد (إذا كان هناك متبقي آجل)
            if ($remainingAmount > 0) {
                $newSupplierBalance = (float)$supplier['current_balance'] + $remainingAmount;
                self::$db->update('suppliers', [
                    'current_balance' => $newSupplierBalance
                ], 'id = :id', [':id' => $supplierId]);
            }

            // 5. تسجيل التدقيق الرقابي
            Logger::audit('purchase_created', 'purchases', $purchaseId, null, [
                'invoice_number'   => $invoiceNumber,
                'supplier_name'    => $supplier['name'],
                'grand_total'      => $grandTotal,
                'paid_amount'      => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'payment_status'   => $paymentStatus
            ]);

            return [
                'success'        => true,
                'purchase_id'    => $purchaseId,
                'invoice_number' => $invoiceNumber,
                'grand_total'    => $grandTotal,
                'message'        => "تم إصدار فاتورة المشتريات رقم {$invoiceNumber} وتوريد البضاعة بنجاح."
            ];
        });
    }

    /**
     * استرجاع الفاتورة بالكامل شاملاً بيانات المورد والبنود
     */
    public static function getPurchaseDetails(int $id): ?array {
        self::init();

        $sql = "
            SELECT p.*,
                   s.name AS supplier_name, s.company_name, s.phone AS supplier_phone, s.tax_number AS supplier_tax_number,
                   st.name AS store_name,
                   u.full_name AS created_by_name
            FROM purchases p
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            LEFT JOIN stores st ON p.store_id = st.id
            LEFT JOIN users u ON p.created_by = u.id
            WHERE p.id = ?
        ";
        $purchase = self::$db->fetchOne($sql, [$id]);
        if (!$purchase) return null;

        $itemsSql = "
            SELECT pi.*,
                   i.name AS item_name, i.barcode,
                   u.name AS unit_name, u.symbol AS unit_symbol
            FROM purchase_items pi
            LEFT JOIN items i ON pi.item_id = i.id
            LEFT JOIN units u ON i.unit_id = u.id
            WHERE pi.purchase_id = ?
            ORDER BY pi.id ASC
        ";
        $purchase['items'] = self::$db->fetchAll($itemsSql, [$id]);

        return $purchase;
    }

    /**
     * استرجاع قائمة المشتريات بالفلاتر
     */
    public static function getPurchases(array $filters = [], int $limit = 200): array {
        self::init();

        $where = ["1=1"];
        $params = [];

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $where[] = "p.invoice_date BETWEEN ? AND ?";
            $params[] = $filters['date_from'];
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['supplier_id'])) {
            $where[] = "p.supplier_id = ?";
            $params[] = (int)$filters['supplier_id'];
        }

        if (!empty($filters['payment_status'])) {
            $where[] = "p.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['search'])) {
            $s = '%' . trim($filters['search']) . '%';
            $where[] = "(p.invoice_number LIKE ? OR p.supplier_invoice_number LIKE ? OR s.name LIKE ? OR s.phone LIKE ?)";
            $params[] = $s;
            $params[] = $s;
            $params[] = $s;
            $params[] = $s;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT p.*,
                   s.name AS supplier_name, s.company_name,
                   st.name AS store_name,
                   u.full_name AS created_by_name
            FROM purchases p
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            LEFT JOIN stores st ON p.store_id = st.id
            LEFT JOIN users u ON p.created_by = u.id
            WHERE {$whereClause}
            ORDER BY p.invoice_date DESC, p.id DESC
            LIMIT {$limit}
        ";

        return self::$db->fetchAll($sql, $params);
    }

    /**
     * استرجاع إحصائيات المشتريات للفترة
     */
    public static function getPurchasesSummary(array $filters = []): array {
        self::init();

        $where = ["1=1"];
        $params = [];

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $where[] = "p.invoice_date BETWEEN ? AND ?";
            $params[] = $filters['date_from'];
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['supplier_id'])) {
            $where[] = "p.supplier_id = ?";
            $params[] = (int)$filters['supplier_id'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT 
                COUNT(*) AS total_count,
                COALESCE(SUM(p.grand_total), 0) AS total_amount,
                COALESCE(SUM(p.paid_amount), 0) AS total_paid,
                COALESCE(SUM(p.remaining_amount), 0) AS total_remaining,
                COALESCE(SUM(p.tax_amount), 0) AS total_tax,
                COALESCE(SUM(p.discount_amount), 0) AS total_discount
            FROM purchases p
            WHERE {$whereClause}
        ";

        return self::$db->fetchOne($sql, $params) ?: [
            'total_count'     => 0,
            'total_amount'    => 0.0,
            'total_paid'      => 0.0,
            'total_remaining' => 0.0,
            'total_tax'       => 0.0,
            'total_discount'  => 0.0
        ];
    }
}
