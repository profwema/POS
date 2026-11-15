<?php
/**
 * موديل إدارة عروض الأسعار والتحويل لفواتير بيع - Quotation Model
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

class Quotation {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * استرجاع جميع عروض الأسعار مع الفلاتر
     */
    public static function getAll(array $filters = [], int $limit = 200): array {
        self::init();

        $where = ["1=1"];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "q.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $where[] = "q.quotation_date BETWEEN ? AND ?";
            $params[] = $filters['date_from'];
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['customer_id'])) {
            $where[] = "q.customer_id = ?";
            $params[] = (int)$filters['customer_id'];
        }

        if (!empty($filters['search'])) {
            $s = '%' . trim($filters['search']) . '%';
            $where[] = "(q.quotation_number LIKE ? OR c.name LIKE ? OR c.phone LIKE ?)";
            $params[] = $s;
            $params[] = $s;
            $params[] = $s;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT q.*,
                   c.name AS customer_name, c.phone AS customer_phone,
                   u.full_name AS creator_name,
                   s.invoice_number AS converted_invoice_number
            FROM quotations q
            LEFT JOIN customers c ON q.customer_id = c.id
            LEFT JOIN users u ON q.created_by = u.id
            LEFT JOIN sales s ON q.converted_sale_id = s.id
            WHERE {$whereClause}
            ORDER BY q.id DESC
            LIMIT {$limit}
        ";

        return self::$db->fetchAll($sql, $params);
    }

    /**
     * استرجاع بيانات عرض السعر بالمعرف
     */
    public static function findById(int $id): ?array {
        self::init();
        $sql = "
            SELECT q.*,
                   c.name AS customer_name, c.phone AS customer_phone, c.tax_number AS customer_tax_number, c.address AS customer_address,
                   u.full_name AS creator_name,
                   s.invoice_number AS converted_invoice_number
            FROM quotations q
            LEFT JOIN customers c ON q.customer_id = c.id
            LEFT JOIN users u ON q.created_by = u.id
            LEFT JOIN sales s ON q.converted_sale_id = s.id
            WHERE q.id = ?
        ";
        return self::$db->fetchOne($sql, [$id]);
    }

    /**
     * استرجاع بنود عرض السعر
     */
    public static function getItems(int $quotationId): array {
        self::init();
        $sql = "
            SELECT qi.*,
                   i.name AS item_name, i.barcode, i.retail_price,
                   u.name AS unit_name, u.symbol AS unit_symbol
            FROM quotation_items qi
            LEFT JOIN items i ON qi.item_id = i.id
            LEFT JOIN units u ON i.unit_id = u.id
            WHERE qi.quotation_id = ?
            ORDER BY qi.id ASC
        ";
        return self::$db->fetchAll($sql, [$quotationId]);
    }

    /**
     * استرجاع عرض السعر الكامل شاملاً البنود
     */
    public static function getFullQuotation(int $id): ?array {
        $quotation = self::findById($id);
        if (!$quotation) return null;
        $quotation['items'] = self::getItems($id);
        return $quotation;
    }

    /**
     * إنشاء عرض سعر جديد
     */
    public static function create(array $data, array $items, int $userId): int {
        self::init();

        if (empty($items)) {
            throw new Exception("يجب إضافة بند واحد على الأقل في عرض السعر.");
        }

        return self::$db->transaction(function() use ($data, $items, $userId) {
            // توليد كود عرض السعر QUO-YYYYMMDD-0001
            $dateKey = date('Ymd');
            $count = (int)self::$db->fetchColumn("SELECT COUNT(*) FROM quotations WHERE DATE(created_at) = CURRENT_DATE");
            $quotationNumber = sprintf('QUO-%s-%04d', $dateKey, $count + 1);

            $subtotal = 0.0;
            $taxAmount = 0.0;
            $processedItems = [];

            foreach ($items as $item) {
                $itemId = (int)($item['item_id'] ?? 0);
                $qty = (float)($item['quantity'] ?? 0.0);
                $price = (float)($item['unit_price'] ?? 0.0);
                $notes = trim($item['notes'] ?? '');

                if ($itemId <= 0 || $qty <= 0) continue;

                $lineSubtotal = round($qty * $price, 2);
                $taxRate = 14.0; // ضريبة قياسية 14%
                $lineTax = round($lineSubtotal * ($taxRate / 100), 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $subtotal += $lineSubtotal;
                $taxAmount += $lineTax;

                $processedItems[] = [
                    'item_id'    => $itemId,
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'tax_amount' => $lineTax,
                    'total_price'=> $lineTotal,
                    'notes'      => $notes
                ];
            }

            if (empty($processedItems)) {
                throw new Exception("لم يتم تحديد أصناف صالحة.");
            }

            $discount = (float)($data['discount_amount'] ?? 0.0);
            $grandTotal = max(0, round($subtotal + $taxAmount - $discount, 2));

            $quotationId = (int)self::$db->insert('quotations', [
                'quotation_number' => $quotationNumber,
                'customer_id'      => (int)($data['customer_id'] ?? 1),
                'store_id'         => (int)($data['store_id'] ?? 2),
                'branch_id'        => (int)($data['branch_id'] ?? 1),
                'quotation_date'   => $data['quotation_date'] ?? date('Y-m-d'),
                'expiry_date'      => !empty($data['expiry_date']) ? $data['expiry_date'] : date('Y-m-d', strtotime('+15 days')),
                'subtotal'         => $subtotal,
                'discount_amount'  => $discount,
                'tax_amount'       => $taxAmount,
                'grand_total'      => $grandTotal,
                'status'           => 'draft',
                'notes'            => trim($data['notes'] ?? ''),
                'created_by'       => $userId
            ]);

            foreach ($processedItems as $pItem) {
                $pItem['quotation_id'] = $quotationId;
                self::$db->insert('quotation_items', $pItem);
            }

            Logger::audit('quotation_created', 'quotations', $quotationId, null, [
                'number' => $quotationNumber,
                'total'  => $grandTotal
            ]);

            return $quotationId;
        });
    }

    /**
     * تحديث حالة عرض السعر
     */
    public static function updateStatus(int $id, string $status): bool {
        self::init();
        $allowed = ['draft', 'sent', 'accepted', 'rejected', 'converted'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $res = self::$db->update('quotations', ['status' => $status], 'id = :id', [':id' => $id]);
        Logger::audit('quotation_status_changed', 'quotations', $id, null, ['status' => $status]);
        return $res > 0;
    }

    /**
     * تحويل عرض السعر إلى فاتورة بيع معتمدة بنقرة زر واحدة
     * يُصدر فاتورة مبيعات ويخصم المخزون فوراً
     */
    public static function convertToSale(int $quotationId, int $userId, int $storeId = 2): array {
        self::init();

        $quotation = self::getFullQuotation($quotationId);
        if (!$quotation) {
            throw new Exception("عرض السعر غير موجود.");
        }

        if ($quotation['status'] === 'converted') {
            throw new Exception("تم تحويل عرض السعر بالفعل إلى الفاتورة رقم: " . $quotation['converted_invoice_number']);
        }

        if (empty($quotation['items'])) {
            throw new Exception("عرض السعر لا يحتوي على بنود قابلة للتحويل.");
        }

        return self::$db->transaction(function() use ($quotation, $userId, $storeId) {
            // توليد رقم فاتورة مبيعات INV-YYYYMMDD-XXXX
            $dateKey = date('Ymd');
            $pdo = self::$db->getPdo();
            $stmt = $pdo->prepare("
                INSERT INTO invoice_sequences (prefix, date_key, last_number) 
                VALUES ('INV', ?, 1)
                ON DUPLICATE KEY UPDATE last_number = last_number + 1
            ");
            $stmt->execute([$dateKey]);

            $seqStmt = $pdo->prepare("SELECT last_number FROM invoice_sequences WHERE prefix = 'INV' AND date_key = ?");
            $seqStmt->execute([$dateKey]);
            $lastNumber = (int)$seqStmt->fetchColumn();
            $invoiceNumber = sprintf('INV-%s-%04d', $dateKey, $lastNumber);

            // إنشاء فاتورة المبيعات
            $saleId = (int)self::$db->insert('sales', [
                'branch_id'       => (int)($quotation['branch_id'] ?? 1),
                'store_id'        => $storeId,
                'customer_id'     => (int)($quotation['customer_id'] ?? 1),
                'user_id'         => $userId,
                'invoice_number'  => $invoiceNumber,
                'subtotal'        => $quotation['subtotal'],
                'tax_amount'      => $quotation['tax_amount'],
                'discount_amount' => $quotation['discount_amount'],
                'grand_total'     => $quotation['grand_total'],
                'paid_amount'     => $quotation['grand_total'],
                'remaining_amount'=> 0.00,
                'payment_method'  => 'cash',
                'status'          => 'completed',
                'notes'           => 'محولة تلقائياً من عرض السعر: ' . $quotation['quotation_number']
            ]);

            // إضافة بنود الفاتورة وخصم المخزون عبر stock_moves
            foreach ($quotation['items'] as $item) {
                $itemId = (int)$item['item_id'];
                $qty = (float)$item['quantity'];
                $price = (float)$item['unit_price'];
                $lineTax = (float)$item['tax_amount'];
                $lineTotal = (float)$item['total_price'];

                self::$db->insert('sale_items', [
                    'sale_id'     => $saleId,
                    'item_id'     => $itemId,
                    'quantity'    => $qty,
                    'unit_price'  => $price,
                    'discount'    => 0.00,
                    'tax_amount'  => $lineTax,
                    'total_price' => $lineTotal
                ]);

                // استرجاع الرصيد الحالي للخصم
                $currentStock = (float)self::$db->fetchValue("
                    SELECT balance_after FROM stock_moves 
                    WHERE item_id = ? AND store_id = ? 
                    ORDER BY id DESC LIMIT 1
                ", [$itemId, $storeId]);

                $newBalance = $currentStock - $qty;

                // تسجيل حركة المخزون بنوع 'sale'
                self::$db->insert('stock_moves', [
                    'item_id'        => $itemId,
                    'store_id'       => $storeId,
                    'move_type'      => 'sale',
                    'reference_type' => 'sales',
                    'reference_id'   => $saleId,
                    'quantity'       => (-1 * $qty),
                    'unit_cost'      => 0.00,
                    'balance_after'  => $newBalance,
                    'notes'          => 'صرف مبيعات تلقائي محول من عرض سعر ' . $quotation['quotation_number'],
                    'created_by'     => $userId
                ]);
            }

            // تحديث حالة عرض السعر إلى converted
            self::$db->update('quotations', [
                'status'            => 'converted',
                'converted_sale_id' => $saleId,
                'converted_at'      => date('Y-m-d H:i:s')
            ], 'id = :id', [':id' => $quotation['id']]);

            Logger::audit('quotation_converted', 'quotations', $quotation['id'], null, [
                'sale_id'        => $saleId,
                'invoice_number' => $invoiceNumber
            ]);

            return [
                'success'        => true,
                'sale_id'        => $saleId,
                'invoice_number' => $invoiceNumber,
                'message'        => "تم تحويل عرض السعر إلى فاتورة بيع بنجاح (رقم: {$invoiceNumber})"
            ];
        });
    }
}
