<?php
/**
 * موديل شجرة ودليل الحسابات ودفتر الأستاذ - Chart of Accounts & General Ledger Model
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

class Account {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * استرجاع الدليل الشجري للحسابات بالكامل بتسلسل هرمي
     */
    public static function getAllTree(): array {
        self::init();

        $sql = "
            SELECT a.*,
                   p.name AS parent_name, p.account_code AS parent_code
            FROM chart_of_accounts a
            LEFT JOIN chart_of_accounts p ON a.parent_id = p.id
            ORDER BY a.account_code ASC
        ";
        $accounts = self::$db->fetchAll($sql);

        // بناء الشجرة الهرمية
        $lookup = [];
        $tree = [];

        foreach ($accounts as $acc) {
            $acc['children'] = [];
            $lookup[$acc['id']] = $acc;
        }

        foreach ($lookup as $id => &$acc) {
            if (!empty($acc['parent_id']) && isset($lookup[$acc['parent_id']])) {
                $lookup[$acc['parent_id']]['children'][] = &$acc;
            } else {
                $tree[] = &$acc;
            }
        }
        unset($acc);

        return [
            'tree' => $tree,
            'all'  => $accounts
        ];
    }

    /**
     * استرجاع قائمة مسطحة بالحسابات القابلة للتقييد (Sub-Accounts)
     */
    public static function getFlatList(bool $postingOnly = true): array {
        self::init();

        $where = "is_active = 1";
        if ($postingOnly) {
            $where .= " AND is_parent = 0";
        }

        $sql = "
            SELECT * FROM chart_of_accounts 
            WHERE {$where} 
            ORDER BY account_code ASC
        ";
        return self::$db->fetchAll($sql);
    }

    /**
     * استرجاع حساب محدد بمعرفه
     */
    public static function findById(int $id): ?array {
        self::init();
        $sql = "
            SELECT a.*, p.name AS parent_name, p.account_code AS parent_code
            FROM chart_of_accounts a
            LEFT JOIN chart_of_accounts p ON a.parent_id = p.id
            WHERE a.id = ?
        ";
        return self::$db->fetchOne($sql, [$id]);
    }

    /**
     * استرجاع حساب بكوده المحاسبي
     */
    public static function findByCode(string $code): ?array {
        self::init();
        return self::$db->fetchOne("SELECT * FROM chart_of_accounts WHERE account_code = ?", [trim($code)]);
    }

    /**
     * إنشاء حساب جديد داخل الدليل الشجري
     */
    public static function create(array $data): int {
        self::init();

        $parentId = !empty($data['parent_id']) ? (int)$data['parent_id'] : null;
        $level = 1;
        $type = $data['type'] ?? 'asset';
        $nature = $data['nature'] ?? 'debit';

        if ($parentId) {
            $parent = self::findById($parentId);
            if ($parent) {
                $level = (int)$parent['level'] + 1;
                $type = $parent['type'];
                $nature = $parent['nature'];
                // تحديث الحساب الأب ليصبح تجميعياً
                self::$db->update('chart_of_accounts', ['is_parent' => 1], 'id = :id', [':id' => $parentId]);
            }
        }

        $openingBalance = (float)($data['opening_balance'] ?? 0.0);

        $insertData = [
            'account_code'    => trim($data['account_code'] ?? ''),
            'name'            => trim($data['name'] ?? ''),
            'parent_id'       => $parentId,
            'type'            => $type,
            'nature'          => $nature,
            'level'           => $level,
            'is_parent'       => 0,
            'opening_balance' => $openingBalance,
            'current_balance' => $openingBalance,
            'description'     => trim($data['description'] ?? ''),
            'is_active'       => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ];

        $id = (int)self::$db->insert('chart_of_accounts', $insertData);
        Logger::audit('account_created', 'chart_of_accounts', $id, null, $insertData);
        return $id;
    }

    /**
     * تحديث بيانات الحساب
     */
    public static function update(int $id, array $data): bool {
        self::init();

        $old = self::findById($id);
        if (!$old) return false;

        $updateData = [
            'name'        => trim($data['name'] ?? $old['name']),
            'description' => trim($data['description'] ?? $old['description']),
            'is_active'   => isset($data['is_active']) ? (int)$data['is_active'] : $old['is_active']
        ];

        $affected = self::$db->update('chart_of_accounts', $updateData, 'id = :id', [':id' => $id]);
        Logger::audit('account_updated', 'chart_of_accounts', $id, $old, $updateData);
        return $affected > 0;
    }

    /**
     * استخراج كشف حساب دفتر الأستاذ للحساب (General Ledger Account Statement)
     */
    public static function getAccountStatement(int $accountId, ?string $dateFrom = null, ?string $dateTo = null): array {
        self::init();

        $account = self::findById($accountId);
        if (!$account) {
            throw new Exception("الحساب المحاسبي غير موجود.");
        }

        $dateFrom = $dateFrom ?: date('Y-01-01');
        $dateTo = $dateTo ?: date('Y-m-d');

        // 1. حساب الرصيد السابق قبل تاريخ الفترة
        $prevMoves = self::$db->fetchOne("
            SELECT 
                COALESCE(SUM(debit), 0) AS total_prev_debit,
                COALESCE(SUM(credit), 0) AS total_prev_credit
            FROM journal_entry_items jei
            JOIN journal_entries je ON jei.entry_id = je.id
            WHERE jei.account_id = ? AND je.entry_date < ? AND je.is_posted = 1
        ", [$accountId, $dateFrom]);

        $prevDebit = (float)$prevMoves['total_prev_debit'];
        $prevCredit = (float)$prevMoves['total_prev_credit'];

        if ($account['nature'] === 'debit') {
            $openingPeriodBalance = (float)$account['opening_balance'] + ($prevDebit - $prevCredit);
        } else {
            $openingPeriodBalance = (float)$account['opening_balance'] + ($prevCredit - $prevDebit);
        }

        // 2. حركات الفترة المحددة
        $sql = "
            SELECT 
                je.id AS entry_id,
                je.entry_number,
                je.entry_date,
                je.doc_reference,
                je.description AS entry_desc,
                jei.description AS line_desc,
                jei.debit,
                jei.credit
            FROM journal_entry_items jei
            JOIN journal_entries je ON jei.entry_id = je.id
            WHERE jei.account_id = ? AND je.entry_date BETWEEN ? AND ? AND je.is_posted = 1
            ORDER BY je.entry_date ASC, je.id ASC
        ";
        $entries = self::$db->fetchAll($sql, [$accountId, $dateFrom, $dateTo]);

        // 3. احتساب الرصيد التراكمي
        $runningBalance = $openingPeriodBalance;
        $totalDebitPeriod = 0.0;
        $totalCreditPeriod = 0.0;

        foreach ($entries as &$entry) {
            $debit = (float)$entry['debit'];
            $credit = (float)$entry['credit'];

            $totalDebitPeriod += $debit;
            $totalCreditPeriod += $credit;

            if ($account['nature'] === 'debit') {
                $runningBalance += ($debit - $credit);
            } else {
                $runningBalance += ($credit - $debit);
            }
            $entry['balance_after'] = $runningBalance;
        }
        unset($entry);

        return [
            'account'               => $account,
            'date_from'             => $dateFrom,
            'date_to'               => $dateTo,
            'opening_period_balance'=> $openingPeriodBalance,
            'closing_period_balance'=> $runningBalance,
            'total_debit'           => $totalDebitPeriod,
            'total_credit'          => $totalCreditPeriod,
            'transactions'          => $entries
        ];
    }
}
