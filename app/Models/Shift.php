<?php
/**
 * موديل ورديات الكاشير والعُهد النقدية - Shift Model
 * WAM Tech POS & ERP
 */

class Shift {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * جلب الوردية المفتوحة حالياً للمستخدم
     */
    public static function getActiveShift(int $userId): ?array {
        self::init();
        $sql = "SELECT s.*, st.name AS store_name, u.full_name AS user_name 
                FROM shifts s 
                LEFT JOIN stores st ON s.store_id = st.id 
                LEFT JOIN users u ON s.user_id = u.id 
                WHERE s.user_id = ? AND s.status = 'open' 
                ORDER BY s.id DESC LIMIT 1";
        return self::$db->fetchOne($sql, [$userId]);
    }

    /**
     * فتح وردية كاشير جديدة
     */
    public static function openShift(int $userId, int $storeId = 2, float $openingBalance = 0.0, ?string $notes = null): array {
        self::init();

        $active = self::getActiveShift($userId);
        if ($active) {
            return $active;
        }

        $shiftNumber = 'SH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $id = self::$db->insert('shifts', [
            'branch_id'       => 1,
            'store_id'        => $storeId,
            'user_id'         => $userId,
            'shift_number'    => $shiftNumber,
            'opening_balance' => $openingBalance,
            'status'          => 'open',
            'notes'           => $notes,
            'opened_at'       => date('Y-m-d H:i:s')
        ]);

        return self::getActiveShift($userId);
    }

    /**
     * تسجيل قيمة سداد في حسابات الوردية فورياً
     */
    public static function recordSalePayment(int $shiftId, string $method, float $amount): void {
        self::init();

        $column = match ($method) {
            'cash'     => 'total_sales_cash',
            'card'     => 'total_sales_card',
            'instapay' => 'total_sales_instapay',
            'wallet'   => 'total_sales_wallet',
            'credit'   => 'total_sales_credit',
            default    => 'total_sales_cash'
        };

        $sql = "UPDATE shifts 
                SET {$column} = {$column} + ?, 
                    sales_count = sales_count + 1 
                WHERE id = ?";
        self::$db->query($sql, [$amount, $shiftId]);
    }

    /**
     * إغلاق الوردية وجرد النقدية وحساب العجز أو الزيادة
     */
    public static function closeShift(int $shiftId, float $actualCash, ?string $notes = null): array {
        self::init();

        $shift = self::$db->fetchOne("SELECT * FROM shifts WHERE id = ?", [$shiftId]);
        if (!$shift) {
            throw new Exception("الوردية غير موجودة.");
        }

        if ($shift['status'] === 'closed') {
            throw new Exception("هذه الوردية مغلقة بالفعل مسبقاً.");
        }

        // النقدية المتوقعة دفترياً = العهدة الافتتاحية + إجمالي مبيعات الكاش
        $expectedCash = (float)$shift['opening_balance'] + (float)$shift['total_sales_cash'];
        $difference = $actualCash - $expectedCash;

        self::$db->update('shifts', [
            'closing_balance' => $expectedCash,
            'actual_cash'     => $actualCash,
            'difference'      => $difference,
            'status'          => 'closed',
            'notes'           => $notes,
            'closed_at'       => date('Y-m-d H:i:s')
        ], 'id = ?', [$shiftId]);

        return [
            'shift_id'      => $shiftId,
            'shift_number'  => $shift['shift_number'],
            'opening_cash'  => (float)$shift['opening_balance'],
            'sales_cash'    => (float)$shift['total_sales_cash'],
            'expected_cash' => $expectedCash,
            'actual_cash'   => $actualCash,
            'difference'    => $difference
        ];
    }

    /**
     * تقرير ملخص الوردية الحالية
     */
    public static function getShiftSummary(int $shiftId): array {
        self::init();
        $shift = self::$db->fetchOne("
            SELECT s.*, u.full_name AS cashier_name, st.name AS store_name 
            FROM shifts s 
            LEFT JOIN users u ON s.user_id = u.id 
            LEFT JOIN stores st ON s.store_id = st.id 
            WHERE s.id = ?
        ", [$shiftId]);

        if (!$shift) {
            return [];
        }

        $expectedCash = (float)$shift['opening_balance'] + (float)$shift['total_sales_cash'];
        $totalSales = (float)$shift['total_sales_cash'] + 
                      (float)$shift['total_sales_card'] + 
                      (float)$shift['total_sales_instapay'] + 
                      (float)$shift['total_sales_wallet'] + 
                      (float)$shift['total_sales_credit'];

        return [
            'shift'         => $shift,
            'expected_cash' => $expectedCash,
            'total_sales'   => $totalSales
        ];
    }
}

