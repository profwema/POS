<?php
/**
 * موديل إدارة الموردين وكشوف الحسابات وأعمار الديون - Supplier Model
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

class Supplier {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * استرجاع جميع الموردين مع الفلاتر والبحث
     */
    public static function getAll(array $filters = [], int $limit = 200): array {
        self::init();

        $where = ["1=1"];
        $params = [];

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $where[] = "s.is_active = ?";
            $params[] = (int)$filters['is_active'];
        }

        if (!empty($filters['search'])) {
            $s = '%' . trim($filters['search']) . '%';
            $where[] = "(s.name LIKE ? OR s.company_name LIKE ? OR s.phone LIKE ? OR s.tax_number LIKE ?)";
            $params[] = $s;
            $params[] = $s;
            $params[] = $s;
            $params[] = $s;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT s.*,
                   (SELECT COUNT(*) FROM purchases p WHERE p.supplier_id = s.id) AS total_purchases_count,
                   (SELECT COALESCE(SUM(p.grand_total), 0) FROM purchases p WHERE p.supplier_id = s.id) AS total_purchases_amount,
                   (SELECT COALESCE(SUM(p.remaining_amount), 0) FROM purchases p WHERE p.supplier_id = s.id AND p.payment_status IN ('unpaid', 'partial')) AS total_unpaid_purchases
            FROM suppliers s
            WHERE {$whereClause}
            ORDER BY s.id DESC
            LIMIT {$limit}
        ";

        return self::$db->fetchAll($sql, $params);
    }

    /**
     * استرجاع بيانات مورد محدد بمعرفه
     */
    public static function findById(int $id): ?array {
        self::init();
        $sql = "
            SELECT s.*,
                   (SELECT COUNT(*) FROM purchases p WHERE p.supplier_id = s.id) AS total_purchases_count,
                   (SELECT COALESCE(SUM(p.grand_total), 0) FROM purchases p WHERE p.supplier_id = s.id) AS total_purchases_amount,
                   (SELECT COALESCE(SUM(p.remaining_amount), 0) FROM purchases p WHERE p.supplier_id = s.id) AS total_unpaid_amount
            FROM suppliers s
            WHERE s.id = ?
        ";
        return self::$db->fetchOne($sql, [$id]);
    }

    /**
     * إنشاء مورد جديد
     */
    public static function create(array $data): int {
        self::init();

        $openingBalance = (float)($data['opening_balance'] ?? 0.0);
        $insertData = [
            'name'                => trim($data['name'] ?? ''),
            'company_name'        => trim($data['company_name'] ?? ''),
            'contact_person'      => trim($data['contact_person'] ?? ''),
            'phone'               => trim($data['phone'] ?? ''),
            'email'               => trim($data['email'] ?? ''),
            'address'             => trim($data['address'] ?? ''),
            'tax_number'          => trim($data['tax_number'] ?? ''),
            'commercial_registry' => trim($data['commercial_registry'] ?? ''),
            'opening_balance'     => $openingBalance,
            'current_balance'     => $openingBalance, // يبدأ الرصيد الحالي بالرصيد الافتتاحي
            'credit_limit'        => (float)($data['credit_limit'] ?? 0.0),
            'payment_terms_days'  => (int)($data['payment_terms_days'] ?? 30),
            'notes'               => trim($data['notes'] ?? ''),
            'is_active'           => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ];

        $id = (int)self::$db->insert('suppliers', $insertData);
        Logger::audit('supplier_created', 'suppliers', $id, null, $insertData);
        return $id;
    }

    /**
     * تحديث بيانات مورد
     */
    public static function update(int $id, array $data): bool {
        self::init();

        $old = self::findById($id);
        if (!$old) return false;

        $updateData = [
            'name'                => trim($data['name'] ?? $old['name']),
            'company_name'        => trim($data['company_name'] ?? $old['company_name']),
            'contact_person'      => trim($data['contact_person'] ?? $old['contact_person']),
            'phone'               => trim($data['phone'] ?? $old['phone']),
            'email'               => trim($data['email'] ?? $old['email']),
            'address'             => trim($data['address'] ?? $old['address']),
            'tax_number'          => trim($data['tax_number'] ?? $old['tax_number']),
            'commercial_registry' => trim($data['commercial_registry'] ?? $old['commercial_registry']),
            'credit_limit'        => (float)($data['credit_limit'] ?? $old['credit_limit']),
            'payment_terms_days'  => (int)($data['payment_terms_days'] ?? $old['payment_terms_days']),
            'notes'               => trim($data['notes'] ?? $old['notes']),
            'is_active'           => isset($data['is_active']) ? (int)$data['is_active'] : $old['is_active']
        ];

        $affected = self::$db->update('suppliers', $updateData, 'id = :id', [':id' => $id]);
        Logger::audit('supplier_updated', 'suppliers', $id, $old, $updateData);
        return $affected > 0;
    }

    /**
     * حذف أو تعطيل مورد
     */
    public static function delete(int $id): bool {
        self::init();

        $purchasesCount = (int)self::$db->fetchColumn("SELECT COUNT(*) FROM purchases WHERE supplier_id = ?", [$id]);
        if ($purchasesCount > 0) {
            // تعريفه كمعطل بدلاً من حذفه للحفاظ على التكامل التاريخي
            self::$db->update('suppliers', ['is_active' => 0], 'id = :id', [':id' => $id]);
            Logger::audit('supplier_deactivated', 'suppliers', $id);
            return true;
        }

        $res = self::$db->delete('suppliers', 'id = :id', [':id' => $id]);
        Logger::audit('supplier_deleted', 'suppliers', $id);
        return $res > 0;
    }

    /**
     * استخراج كشف حساب المورد التفصيلي (Supplier Statement of Account)
     */
    public static function getStatement(int $supplierId, ?string $dateFrom = null, ?string $dateTo = null): array {
        self::init();

        $supplier = self::findById($supplierId);
        if (!$supplier) {
            throw new Exception("المورد غير موجود.");
        }

        $dateFrom = $dateFrom ?: '2020-01-01';
        $dateTo = $dateTo ?: date('Y-m-d');

        // 1. حساب الرصيد السابق قبل تاريخ البداية
        $prevPurchases = (float)self::$db->fetchValue("
            SELECT COALESCE(SUM(remaining_amount), 0) FROM purchases 
            WHERE supplier_id = ? AND invoice_date < ?
        ", [$supplierId, $dateFrom]);

        $prevReturns = (float)self::$db->fetchValue("
            SELECT COALESCE(SUM(total_amount), 0) FROM purchase_returns 
            WHERE supplier_id = ? AND return_date < ?
        ", [$supplierId, $dateFrom]);

        $openingPeriodBalance = (float)$supplier['opening_balance'] + $prevPurchases - $prevReturns;

        // 2. حركات الفترة: فواتير المشتريات
        $purchases = self::$db->fetchAll("
            SELECT 
                p.id,
                p.invoice_date AS trans_date,
                p.invoice_number AS trans_ref,
                'purchase' AS trans_type,
                'فاتورة مشتريات' AS trans_label,
                p.grand_total,
                p.paid_amount,
                p.remaining_amount AS debit_or_credit,
                p.notes
            FROM purchases p
            WHERE p.supplier_id = ? AND p.invoice_date BETWEEN ? AND ?
            ORDER BY p.invoice_date ASC, p.id ASC
        ", [$supplierId, $dateFrom, $dateTo]);

        // 3. حركات الفترة: مرتجعات المشتريات
        $returns = self::$db->fetchAll("
            SELECT 
                pr.id,
                pr.return_date AS trans_date,
                pr.return_number AS trans_ref,
                'purchase_return' AS trans_type,
                'مرتجع مشتريات للمورد' AS trans_label,
                pr.total_amount AS grand_total,
                pr.total_amount AS paid_amount,
                (-1 * pr.total_amount) AS debit_or_credit,
                pr.reason AS notes
            FROM purchase_returns pr
            WHERE pr.supplier_id = ? AND pr.return_date BETWEEN ? AND ?
            ORDER BY pr.return_date ASC, pr.id ASC
        ", [$supplierId, $dateFrom, $dateTo]);

        // دمج الحركات وترتيبها زمنياً
        $allTransactions = array_merge($purchases, $returns);
        usort($allTransactions, function($a, $b) {
            $cmp = strcmp($a['trans_date'], $b['trans_date']);
            return $cmp !== 0 ? $cmp : ($a['id'] <=> $b['id']);
        });

        // احتساب الرصيد التراكمي
        $runningBalance = $openingPeriodBalance;
        $totalPurchasesPeriod = 0.0;
        $totalPaidPeriod = 0.0;
        $totalReturnsPeriod = 0.0;

        foreach ($allTransactions as &$tr) {
            if ($tr['trans_type'] === 'purchase') {
                $totalPurchasesPeriod += (float)$tr['grand_total'];
                $totalPaidPeriod += (float)$tr['paid_amount'];
                $runningBalance += (float)$tr['debit_or_credit'];
            } else {
                $totalReturnsPeriod += (float)$tr['grand_total'];
                $runningBalance += (float)$tr['debit_or_credit'];
            }
            $tr['balance_after'] = $runningBalance;
        }
        unset($tr);

        return [
            'supplier'              => $supplier,
            'date_from'             => $dateFrom,
            'date_to'               => $dateTo,
            'opening_period_balance'=> $openingPeriodBalance,
            'closing_period_balance'=> $runningBalance,
            'total_purchases'       => $totalPurchasesPeriod,
            'total_paid'            => $totalPaidPeriod,
            'total_returns'         => $totalReturnsPeriod,
            'transactions'          => $allTransactions
        ];
    }

    /**
     * تقرير أعمار ديون الموردين (Accounts Payable Aging Report)
     * تصنيف المديونيات غير المسددة حسب الفترة:
     * (1-30 يوم) - (31-60 يوم) - (61-90 يوم) - (+90 يوماً)
     */
    public static function getAgingReport(): array {
        self::init();

        $sql = "
            SELECT 
                s.id AS supplier_id,
                s.name AS supplier_name,
                s.company_name,
                s.phone,
                s.current_balance,
                s.payment_terms_days,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURRENT_DATE, p.invoice_date) BETWEEN 0 AND 30 THEN p.remaining_amount ELSE 0 END), 0) AS age_0_30,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURRENT_DATE, p.invoice_date) BETWEEN 31 AND 60 THEN p.remaining_amount ELSE 0 END), 0) AS age_31_60,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURRENT_DATE, p.invoice_date) BETWEEN 61 AND 90 THEN p.remaining_amount ELSE 0 END), 0) AS age_61_90,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURRENT_DATE, p.invoice_date) > 90 THEN p.remaining_amount ELSE 0 END), 0) AS age_over_90,
                COALESCE(SUM(p.remaining_amount), 0) AS total_outstanding
            FROM suppliers s
            LEFT JOIN purchases p ON s.id = p.supplier_id AND p.payment_status IN ('unpaid', 'partial')
            WHERE s.is_active = 1
            GROUP BY s.id, s.name, s.company_name, s.phone, s.current_balance, s.payment_terms_days
            HAVING total_outstanding > 0 OR s.current_balance > 0
            ORDER BY total_outstanding DESC, s.current_balance DESC
        ";

        $rows = self::$db->fetchAll($sql);

        $totals = [
            'age_0_30'         => 0.0,
            'age_31_60'        => 0.0,
            'age_61_90'        => 0.0,
            'age_over_90'      => 0.0,
            'total_outstanding'=> 0.0
        ];

        foreach ($rows as $row) {
            $totals['age_0_30'] += (float)$row['age_0_30'];
            $totals['age_31_60'] += (float)$row['age_31_60'];
            $totals['age_61_90'] += (float)$row['age_61_90'];
            $totals['age_over_90'] += (float)$row['age_over_90'];
            $totals['total_outstanding'] += (float)$row['total_outstanding'];
        }

        return [
            'suppliers' => $rows,
            'totals'    => $totals
        ];
    }
}
