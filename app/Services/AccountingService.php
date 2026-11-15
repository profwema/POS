<?php
/**
 * خدمات المحاسبة العامة والتقارير الختامية والسندات - General Accounting Service
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

class AccountingService {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * توليد متسلسل سند القبض أو الصرف
     */
    public static function generateVoucherNumber(string $type = 'payment'): string {
        self::init();
        $prefix = ($type === 'receipt') ? 'RV' : 'PV';
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
     * تحرير سند قبض أو صرف مالي مع إنشاء قيد محاسبي مزدوج تلقائياً
     */
    public static function createVoucher(array $data, int $userId): array {
        self::init();

        $type = ($data['voucher_type'] ?? 'payment') === 'receipt' ? 'receipt' : 'payment';
        $amount = (float)($data['amount'] ?? 0.0);
        if ($amount <= 0) {
            throw new Exception("يجب أن يكون مبلغ السند أكبر من الصفر.");
        }

        $accountId = (int)($data['account_id'] ?? 0); // حساب الخزينة أو البنك
        $targetAccountId = (int)($data['target_account_id'] ?? 0); // الحساب المقابل (مصروف، عميل، مورد)

        if ($accountId <= 0 || $targetAccountId <= 0) {
            throw new Exception("يجب تحديد حساب النقدية/البنك والحساب المقابل للسند.");
        }

        if ($accountId === $targetAccountId) {
            throw new Exception("لا يمكن أن يكون حساب الصرف وحساب القبض متطابقين.");
        }

        $sourceAcc = Account::findById($accountId);
        $targetAcc = Account::findById($targetAccountId);

        if (!$sourceAcc || !$targetAcc) {
            throw new Exception("أحد الحسابات المحددة غير موجود في شجرة الحسابات.");
        }

        return self::$db->transaction(function() use ($data, $type, $amount, $accountId, $targetAccountId, $sourceAcc, $targetAcc, $userId) {
            $voucherNumber = self::generateVoucherNumber($type);
            $voucherDate = $data['voucher_date'] ?? date('Y-m-d');
            $person = trim($data['received_from_or_paid_to'] ?? '');
            $notes = trim($data['notes'] ?? '');
            $paymentMethod = $data['payment_method'] ?? 'cash';

            // إنشاء القيد المحاسبي المزدوج المتزن
            $journalLines = [];
            $entryDesc = '';

            if ($type === 'payment') {
                // سند صرف: من حـ/ الحساب المقابل (مدين) إلى حـ/ الخزينة أو البنك (دائن)
                $entryDesc = "سند صرف رقم {$voucherNumber} إلى ({$person}) - " . ($notes ?: $targetAcc['name']);
                $journalLines = [
                    [
                        'account_id'  => $targetAccountId,
                        'debit'       => $amount,
                        'credit'      => 0.00,
                        'description' => "سداد إلى: {$person} - " . $targetAcc['name']
                    ],
                    [
                        'account_id'  => $accountId,
                        'debit'       => 0.00,
                        'credit'      => $amount,
                        'description' => "صرف من: {$sourceAcc['name']}"
                    ]
                ];
            } else {
                // سند قبض: من حـ/ الخزينة أو البنك (مدين) إلى حـ/ الحساب المقابل (دائن)
                $entryDesc = "سند قبض رقم {$voucherNumber} من ({$person}) - " . ($notes ?: $targetAcc['name']);
                $journalLines = [
                    [
                        'account_id'  => $accountId,
                        'debit'       => $amount,
                        'credit'      => 0.00,
                        'description' => "تحصيل وإيداع في: {$sourceAcc['name']}"
                    ],
                    [
                        'account_id'  => $targetAccountId,
                        'debit'       => 0.00,
                        'credit'      => $amount,
                        'description' => "تحصيل من: {$person} - " . $targetAcc['name']
                    ]
                ];
            }

            // إنشاء القيد المحاسبي
            $entryId = JournalEntry::create([
                'entry_date'    => $voucherDate,
                'doc_reference' => $voucherNumber,
                'description'   => $entryDesc,
                'source_type'   => ($type === 'receipt') ? 'receipt_voucher' : 'payment_voucher'
            ], $journalLines, $userId);

            // إدراج السند المالي
            $voucherId = (int)self::$db->insert('financial_vouchers', [
                'voucher_number'           => $voucherNumber,
                'voucher_type'             => $type,
                'voucher_date'             => $voucherDate,
                'account_id'               => $accountId,
                'target_account_id'        => $targetAccountId,
                'amount'                   => $amount,
                'payment_method'           => $paymentMethod,
                'received_from_or_paid_to' => $person,
                'journal_entry_id'         => $entryId,
                'notes'                    => $notes,
                'created_by'               => $userId
            ]);

            Logger::audit('voucher_created', 'financial_vouchers', $voucherId, null, [
                'voucher_number' => $voucherNumber,
                'type'           => $type,
                'amount'         => $amount,
                'journal_entry'  => $entryId
            ]);

            return [
                'success'        => true,
                'voucher_id'     => $voucherId,
                'voucher_number' => $voucherNumber,
                'journal_id'     => $entryId,
                'message'        => "تم إصدار " . (($type === 'receipt') ? "سند القبض" : "سند الصرف") . " رقم {$voucherNumber} وتسجيل قيده المحاسبي بنجاح."
            ];
        });
    }

    /**
     * استرجاع قائمة سندات القبض والصرف بالفلاتر
     */
    public static function getVouchers(array $filters = [], int $limit = 200): array {
        self::init();

        $where = ["1=1"];
        $params = [];

        if (!empty($filters['voucher_type'])) {
            $where[] = "fv.voucher_type = ?";
            $params[] = $filters['voucher_type'];
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $where[] = "fv.voucher_date BETWEEN ? AND ?";
            $params[] = $filters['date_from'];
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $s = '%' . trim($filters['search']) . '%';
            $where[] = "(fv.voucher_number LIKE ? OR fv.received_from_or_paid_to LIKE ? OR fv.notes LIKE ?)";
            $params[] = $s;
            $params[] = $s;
            $params[] = $s;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT fv.*,
                   a.name AS source_account_name, a.account_code AS source_account_code,
                   ta.name AS target_account_name, ta.account_code AS target_account_code,
                   u.full_name AS created_by_name,
                   je.entry_number
            FROM financial_vouchers fv
            JOIN chart_of_accounts a ON fv.account_id = a.id
            JOIN chart_of_accounts ta ON fv.target_account_id = ta.id
            LEFT JOIN users u ON fv.created_by = u.id
            LEFT JOIN journal_entries je ON fv.journal_entry_id = je.id
            WHERE {$whereClause}
            ORDER BY fv.voucher_date DESC, fv.id DESC
            LIMIT {$limit}
        ";

        return self::$db->fetchAll($sql, $params);
    }

    /**
     * استخراج ميزان المراجعة بالأرصدة والمجاميع (Trial Balance)
     * يفحص اتزان المدين والدائن لجميع الحسابات حتى تاريخ محدد
     */
    public static function getTrialBalance(?string $asOfDate = null): array {
        self::init();
        $asOfDate = $asOfDate ?: date('Y-m-d');

        $sql = "
            SELECT 
                a.id,
                a.account_code,
                a.name,
                a.type,
                a.nature,
                a.opening_balance,
                COALESCE(SUM(CASE WHEN je.entry_date <= ? AND je.is_posted = 1 THEN jei.debit ELSE 0 END), 0) AS total_period_debit,
                COALESCE(SUM(CASE WHEN je.entry_date <= ? AND je.is_posted = 1 THEN jei.credit ELSE 0 END), 0) AS total_period_credit
            FROM chart_of_accounts a
            LEFT JOIN journal_entry_items jei ON a.id = jei.account_id
            LEFT JOIN journal_entries je ON jei.entry_id = je.id
            WHERE a.is_parent = 0 AND a.is_active = 1
            GROUP BY a.id, a.account_code, a.name, a.type, a.nature, a.opening_balance
            ORDER BY a.account_code ASC
        ";

        $rows = self::$db->fetchAll($sql, [$asOfDate, $asOfDate]);

        $totalOpeningDebit = 0.0;
        $totalOpeningCredit = 0.0;
        $totalMovesDebit = 0.0;
        $totalMovesCredit = 0.0;
        $totalEndingDebit = 0.0;
        $totalEndingCredit = 0.0;

        foreach ($rows as &$row) {
            $op = (float)$row['opening_balance'];
            $movDeb = (float)$row['total_period_debit'];
            $movCred = (float)$row['total_period_credit'];

            if ($row['nature'] === 'debit') {
                $row['opening_debit'] = $op;
                $row['opening_credit'] = 0.0;
                $endingNet = $op + ($movDeb - $movCred);
            } else {
                $row['opening_debit'] = 0.0;
                $row['opening_credit'] = $op;
                $endingNet = $op + ($movCred - $movDeb);
            }

            if ($endingNet >= 0) {
                if ($row['nature'] === 'debit') {
                    $row['ending_debit'] = $endingNet;
                    $row['ending_credit'] = 0.0;
                } else {
                    $row['ending_debit'] = 0.0;
                    $row['ending_credit'] = $endingNet;
                }
            } else {
                // رصيد شاذ
                if ($row['nature'] === 'debit') {
                    $row['ending_debit'] = 0.0;
                    $row['ending_credit'] = abs($endingNet);
                } else {
                    $row['ending_debit'] = abs($endingNet);
                    $row['ending_credit'] = 0.0;
                }
            }

            $totalOpeningDebit += $row['opening_debit'];
            $totalOpeningCredit += $row['opening_credit'];
            $totalMovesDebit += $movDeb;
            $totalMovesCredit += $movCred;
            $totalEndingDebit += $row['ending_debit'];
            $totalEndingCredit += $row['ending_credit'];
        }
        unset($row);

        $isBalanced = (abs($totalEndingDebit - $totalEndingCredit) < 0.02);

        return [
            'as_of_date'   => $asOfDate,
            'accounts'     => $rows,
            'totals'       => [
                'opening_debit'  => $totalOpeningDebit,
                'opening_credit' => $totalOpeningCredit,
                'moves_debit'    => $totalMovesDebit,
                'moves_credit'   => $totalMovesCredit,
                'ending_debit'   => $totalEndingDebit,
                'ending_credit'  => $totalEndingCredit
            ],
            'is_balanced'  => $isBalanced
        ];
    }

    /**
     * استخراج قائمة الدخل والأرباح والخسائر (Income Statement)
     */
    public static function getIncomeStatement(?string $dateFrom = null, ?string $dateTo = null): array {
        self::init();
        $dateFrom = $dateFrom ?: date('Y-01-01');
        $dateTo = $dateTo ?: date('Y-m-d');

        // 1. حساب الإيرادات (المجموعة 4)
        $revenueAccounts = self::$db->fetchAll("
            SELECT a.id, a.account_code, a.name,
                   COALESCE(SUM(jei.credit - jei.debit), 0) AS net_revenue
            FROM chart_of_accounts a
            LEFT JOIN journal_entry_items jei ON a.id = jei.account_id
            LEFT JOIN journal_entries je ON jei.entry_id = je.id AND je.entry_date BETWEEN ? AND ? AND je.is_posted = 1
            WHERE a.type = 'revenue' AND a.is_parent = 0 AND a.is_active = 1
            GROUP BY a.id, a.account_code, a.name
            ORDER BY a.account_code ASC
        ", [$dateFrom, $dateTo]);

        $totalRevenue = 0.0;
        foreach ($revenueAccounts as $r) {
            $totalRevenue += (float)$r['net_revenue'];
        }

        // 2. حساب تكلفة المبيعات المباشرة (COGS - 51)
        $cogsAccounts = self::$db->fetchAll("
            SELECT a.id, a.account_code, a.name,
                   COALESCE(SUM(jei.debit - jei.credit), 0) AS net_cogs
            FROM chart_of_accounts a
            LEFT JOIN journal_entry_items jei ON a.id = jei.account_id
            LEFT JOIN journal_entries je ON jei.entry_id = je.id AND je.entry_date BETWEEN ? AND ? AND je.is_posted = 1
            WHERE a.account_code LIKE '51%' AND a.is_parent = 0 AND a.is_active = 1
            GROUP BY a.id, a.account_code, a.name
        ", [$dateFrom, $dateTo]);

        $totalCogs = 0.0;
        foreach ($cogsAccounts as $c) {
            $totalCogs += (float)$c['net_cogs'];
        }

        // مجمل الربح (Gross Profit)
        $grossProfit = $totalRevenue - $totalCogs;

        // 3. حساب المصروفات التشغيلية والعمومية (52)
        $expenseAccounts = self::$db->fetchAll("
            SELECT a.id, a.account_code, a.name,
                   COALESCE(SUM(jei.debit - jei.credit), 0) AS net_expense
            FROM chart_of_accounts a
            LEFT JOIN journal_entry_items jei ON a.id = jei.account_id
            LEFT JOIN journal_entries je ON jei.entry_id = je.id AND je.entry_date BETWEEN ? AND ? AND je.is_posted = 1
            WHERE a.type = 'expense' AND a.account_code NOT LIKE '51%' AND a.is_parent = 0 AND a.is_active = 1
            GROUP BY a.id, a.account_code, a.name
            ORDER BY a.account_code ASC
        ", [$dateFrom, $dateTo]);

        $totalExpenses = 0.0;
        foreach ($expenseAccounts as $e) {
            $totalExpenses += (float)$e['net_expense'];
        }

        // صافي الربح أو الخسارة التشغيلية
        $netIncome = $grossProfit - $totalExpenses;

        return [
            'date_from'        => $dateFrom,
            'date_to'          => $dateTo,
            'revenue_accounts' => $revenueAccounts,
            'total_revenue'    => $totalRevenue,
            'cogs_accounts'    => $cogsAccounts,
            'total_cogs'       => $totalCogs,
            'gross_profit'     => $grossProfit,
            'expense_accounts' => $expenseAccounts,
            'total_expenses'   => $totalExpenses,
            'net_income'       => $netIncome
        ];
    }
}
