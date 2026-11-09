<?php
/**
 * محرك نقطة البيع السريعة والمبيعات - Fast POS Engine & Sales Service
 * WAM Tech POS & ERP
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

class PosService {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * توليد متسلسل رقم الفاتورة التالي بقفل ذري لمنع التصادم
     * الصيغة: INV-YYYYMMDD-0001
     */
    public static function generateInvoiceNumber(string $prefix = 'INV'): string {
        self::init();
        $dateKey = date('Ymd');
        $pdo = self::$db->getPdo();

        // قفل أو إنشاء سطر المتسلسل لليوم الحالي
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
     * تنفيذ وإنهاء عملية البيع في الكاشير بالكامل داخل معاملة ذرية (ACID Transaction)
     */
    public static function checkout(array $data, int $userId): array {
        self::init();

        // 1. التحقق من وجود وردية مفتوحة للكاشير
        $shift = Shift::getActiveShift($userId);
        if (!$shift) {
            throw new Exception("لا توجد وردية مفتوحة لهذا الكاشير. يرجى فتح وردية جديدة وبدء العهدة أولاً.");
        }

        // 2. التحقق من بنود السلة
        $items = $data['items'] ?? [];
        if (empty($items) || !is_array($items)) {
            throw new Exception("سلة المشتريات فارغة. يرجى إضافة أصناف قبل إتمام البيع.");
        }

        $customerId = !empty($data['customer_id']) ? (int)$data['customer_id'] : 1;
        $customer = Customer::findById($customerId);
        if (!$customer) {
            $customerId = 1;
            $customer = Customer::findById(1);
        }

        $storeId = (int)($shift['store_id'] ?? 2);
        $discountAmount = (float)($data['discount_amount'] ?? 0.0);
        $notes = trim($data['notes'] ?? '');

        // 3. فحص المعاملة الذرية
        return self::$db->transaction(function() use ($userId, $shift, $storeId, $customerId, $customer, $items, $discountAmount, $notes, $data) {
            $invoiceNumber = self::generateInvoiceNumber('INV');

            $subtotal = 0.0;
            $totalTax = 0.0;
            $processedItems = [];

            // السماح بالبيع بالسالب أم لا
            $allowNegative = (int)Helpers::setting('pos_allow_negative_stock', 0);

            // معالجة كل بند في السلة
            foreach ($items as $item) {
                $itemId = (int)$item['item_id'];
                $qty = (float)$item['quantity'];
                if ($qty <= 0) continue;

                // قفل الصنف والتأكد من بياناته
                $dbItem = self::$db->fetchOne("SELECT * FROM items WHERE id = ? FOR UPDATE", [$itemId]);
                if (!$dbItem) {
                    throw new Exception("الصنف رقم {$itemId} غير موجود بالنظام.");
                }

                // فحص الرصيد إذا كان البيع بالسالب ممنوعاً
                if (!$allowNegative) {
                    $avail = (float)self::$db->fetchValue("
                        SELECT balance_after 
                        FROM stock_moves 
                        WHERE item_id = ? AND store_id = ? 
                        ORDER BY id DESC 
                        LIMIT 1 
                        FOR UPDATE
                    ", [$itemId, $storeId]);

                    if ($avail < $qty) {
                        throw new Exception("الرصيد غير كافٍ للصنف: '{$dbItem['name']}'. المتاح بالمخزن: {$avail}، المطلوب: {$qty}");
                    }
                }

                $unitPrice = (float)($item['price'] ?? $dbItem['retail_price']);
                $costPrice = (float)$dbItem['cost_price'];
                $isScale = (int)($dbItem['is_scale'] ?? 0);

                // حساب الضريبة لكل بند
                $taxRate = (float)($dbItem['tax_rate'] ?? 14.00);
                $itemLineTotal = round($unitPrice * $qty, 2);
                $itemTax = round($itemLineTotal * ($taxRate / 100), 2);

                $subtotal += $itemLineTotal;
                $totalTax += $itemTax;

                $processedItems[] = [
                    'item_id'     => $itemId,
                    'name'        => $dbItem['name'],
                    'barcode'     => $dbItem['barcode'],
                    'unit_price'  => $unitPrice,
                    'cost_price'  => $costPrice,
                    'quantity'    => $qty,
                    'tax_rate'    => $taxRate,
                    'tax_amount'  => $itemTax,
                    'discount'    => 0.0,
                    'total_price' => $itemLineTotal + $itemTax,
                    'is_scale'    => $isScale
                ];
            }

            // الحسابات الإجمالية للفاتورة
            $grandTotal = max(0.0, round(($subtotal + $totalTax) - $discountAmount, 2));

            // معالجة المدفوعات (كاش، كارت، إنستاباي، محفظة، آجل)
            $payments = $data['payments'] ?? [];
            if (empty($payments)) {
                // إذا لم يُحدد، يعتبر كاش كامل الفاتورة
                $payments = [
                    ['method' => 'cash', 'amount' => $grandTotal]
                ];
            }

            $totalPaid = 0.0;
            $primaryMethod = 'cash';
            if (count($payments) > 1) {
                $primaryMethod = 'mixed';
            } elseif (count($payments) === 1) {
                $primaryMethod = $payments[0]['method'];
            }

            foreach ($payments as $p) {
                $totalPaid += (float)$p['amount'];
            }

            // فحص كفاية الدفع
            $remaining = max(0.0, round($grandTotal - $totalPaid, 2));
            $change = max(0.0, round($totalPaid - $grandTotal, 2));

            // إذا كان هناك متبقي ولم تكن طريقة الدفع بها آجل، نتحقق من الحد الائتماني
            $creditAmount = 0.0;
            foreach ($payments as $p) {
                if ($p['method'] === 'credit') {
                    $creditAmount += (float)$p['amount'];
                }
            }

            if ($remaining > 0) {
                // المتبقي يُعامل كآجل
                $creditAmount += $remaining;
            }

            if ($creditAmount > 0) {
                if ($customerId === 1) {
                    throw new Exception("لا يمكن تسجيل مبيعات بالآجل للعميل النقدي العام. يرجى اختيار عميل مسجل بالاسم.");
                }
                $newCustomerBal = (float)$customer['current_balance'] + $creditAmount;
                if ((float)$customer['credit_limit'] > 0 && $newCustomerBal > (float)$customer['credit_limit']) {
                    throw new Exception("تجاوز الحد الائتماني للعميل ({$customer['name']}). الحد المسموح: " . Helpers::formatCurrency($customer['credit_limit']) . "، الرصيد سيصبح: " . Helpers::formatCurrency($newCustomerBal));
                }
            }

            // 4. إدراج رأس الفاتورة في جدول sales
            $saleId = self::$db->insert('sales', [
                'branch_id'        => 1,
                'store_id'         => $storeId,
                'shift_id'         => $shift['id'],
                'customer_id'      => $customerId,
                'user_id'          => $userId,
                'invoice_number'   => $invoiceNumber,
                'subtotal'         => $subtotal,
                'tax_amount'       => $totalTax,
                'discount_amount'  => $discountAmount,
                'grand_total'      => $grandTotal,
                'paid_amount'      => min($totalPaid, $grandTotal),
                'remaining_amount' => $creditAmount,
                'payment_method'   => $primaryMethod,
                'status'           => 'completed',
                'notes'            => $notes,
                'created_at'       => date('Y-m-d H:i:s')
            ]);

            // 5. إدراج بنود الفاتورة وحسم الكميات من المخزن اللحظي
            foreach ($processedItems as $item) {
                self::$db->insert('sale_items', [
                    'sale_id'     => $saleId,
                    'item_id'     => $item['item_id'],
                    'batch_id'    => null,
                    'unit_price'  => $item['unit_price'],
                    'cost_price'  => $item['cost_price'],
                    'quantity'    => $item['quantity'],
                    'tax_rate'    => $item['tax_rate'],
                    'tax_amount'  => $item['tax_amount'],
                    'discount'    => $item['discount'],
                    'total_price' => $item['total_price'],
                    'is_scale'    => $item['is_scale']
                ]);

                // خصم المخزون اللحظي عبر InventoryService
                InventoryService::recordStockMove(
                    $item['item_id'],
                    $storeId,
                    'sale',
                    -$item['quantity'],
                    $item['cost_price'],
                    'sale_invoice',
                    $saleId,
                    "مبيعات فاتورة كاشير رقم {$invoiceNumber}",
                    null,
                    $userId
                );
            }

            // 6. إدراج سجلات السداد وتحديث حسابات الوردية
            foreach ($payments as $p) {
                $payMethod = $p['method'];
                $payAmt = (float)$p['amount'];
                $refNo = $p['reference_number'] ?? null;

                if ($payAmt <= 0) continue;

                self::$db->insert('sale_payments', [
                    'sale_id'          => $saleId,
                    'method'           => $payMethod,
                    'amount'           => $payAmt,
                    'reference_number' => $refNo,
                    'notes'            => $p['notes'] ?? null
                ]);

                // تحديث رصيد الوردية
                Shift::recordSalePayment($shift['id'], $payMethod, $payAmt);
            }

            // 7. تحديث رصيد العميل الآجل
            if ($creditAmount > 0) {
                Customer::updateBalance($customerId, $creditAmount);
            }

            // 8. احتساب نقاط الولاء (نقطة لكل 50 ج.م)
            if ($customerId > 1 && $grandTotal >= 50) {
                $earnedPoints = (int)floor($grandTotal / 50);
                Customer::addPoints($customerId, $earnedPoints);
            }

            // 9. تسجيل سجل التدقيق
            Logger::audit('sale_created', 'sales', (int)$saleId, null, [
                'invoice' => $invoiceNumber,
                'total'   => $grandTotal,
                'desc'    => "إصدار فاتورة مبيعات {$invoiceNumber} بقيمة " . Helpers::formatCurrency($grandTotal)
            ]);

            return [
                'success'        => true,
                'sale_id'        => $saleId,
                'invoice_number' => $invoiceNumber,
                'grand_total'    => $grandTotal,
                'total_paid'     => $totalPaid,
                'change'         => $change,
                'receipt_url'    => BASE_URL . 'receipt.php?id=' . $saleId
            ];
        });
    }

    /**
     * تعليق سلة المشتريات (Hold Cart) لخدمة عميل آخر بالانتظار
     */
    public static function holdCart(int $userId, ?int $shiftId, string $ref, ?int $customerId, array $cartData, float $total): int {
        self::init();
        return self::$db->insert('held_carts', [
            'user_id'        => $userId,
            'shift_id'       => $shiftId,
            'hold_reference' => $ref ?: 'معلقة #' . date('h:i A'),
            'customer_id'    => $customerId ?: 1,
            'cart_data'      => json_encode($cartData, JSON_UNESCAPED_UNICODE),
            'total_amount'   => $total
        ]);
    }

    /**
     * جلب الفواتير المعلقة الخاصة بالكاشير
     */
    public static function getHeldCarts(int $userId): array {
        self::init();
        $sql = "SELECT hc.*, c.name AS customer_name 
                FROM held_carts hc 
                LEFT JOIN customers c ON hc.customer_id = c.id 
                WHERE hc.user_id = ? 
                ORDER BY hc.id DESC";
        $rows = self::$db->fetchAll($sql, [$userId]);
        foreach ($rows as &$r) {
            $r['cart_data'] = json_decode($r['cart_data'], true);
        }
        return $rows;
    }

    /**
     * استرجاع وحذف السلة المعلقة
     */
    public static function resumeHeldCart(int $holdId): array {
        self::init();
        $cart = self::$db->fetchOne("SELECT * FROM held_carts WHERE id = ?", [$holdId]);
        if (!$cart) {
            throw new Exception("السلة المعلقة غير موجودة.");
        }
        self::$db->delete('held_carts', 'id = ?', [$holdId]);
        $cart['cart_data'] = json_decode($cart['cart_data'], true);
        return $cart;
    }

    public static function deleteHeldCart(int $holdId): bool {
        self::init();
        return (bool)self::$db->delete('held_carts', 'id = ?', [$holdId]);
    }
}
