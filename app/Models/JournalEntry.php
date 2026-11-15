<?php
/**
 * موديل قيود اليومية العامة المزدوجة - Journal Entry Model
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

class JournalEntry {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * توليد رقم قيد يومية متسلسل ذرياً
     * الصيغة: JV-YYYYMMDD-0001
     */
    public static function generateEntryNumber(): string {
        self::init();
        $dateKey = date('Ymd');
        $pdo = self::$db->getPdo();

        $stmt = $pdo->prepare("
            INSERT INTO invoice_sequences (prefix, date_key, last_number) 
            VALUES ('JV', ?, 1)
            ON DUPLICATE KEY UPDATE last_number = last_number + 1
        ");
        $stmt->execute([$dateKey]);

        $seqStmt = $pdo->prepare("SELECT last_number FROM invoice_sequences WHERE prefix = 'JV' AND date_key = ?");
        $seqStmt->execute([$dateKey]);
        $lastNumber = (int)$seqStmt->fetchColumn();

        return sprintf('JV-%s-%04d', $dateKey, $lastNumber);
    }

    /**
     * استرجاع قيود اليومية مع الفلاتر
     */
    public static function getAll(array $filters = [], int $limit = 200): array {
        self::init();

        $where = ["1=1"];
        $params = [];

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $where[] = "je.entry_date BETWEEN ? AND ?";
            $params[] = $filters['date_from'];
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['source_type'])) {
            $where[] = "je.source_type = ?";
            $params[] = $filters['source_type'];
        }

        if (!empty($filters['search'])) {
            $s = '%' . trim($filters['search']) . '%';
            $where[] = "(je.entry_number LIKE ? OR je.doc_reference LIKE ? OR je.description LIKE ?)";
            $params[] = $s;
            $params[] = $s;
            $params[] = $s;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT je.*,
                   u.full_name AS creator_name,
                   (SELECT COUNT(*) FROM journal_entry_items jei WHERE jei.entry_id = je.id) AS lines_count
            FROM journal_entries je
            LEFT JOIN users u ON je.created_by = u.id
            WHERE {$whereClause}
            ORDER BY je.entry_date DESC, je.id DESC
            LIMIT {$limit}
        ";

        return self::$db->fetchAll($sql, $params);
    }

    /**
     * استرجاع تفاصيل قيد يومية كامل مع كافة أطرافه
     */
    public static function findById(int $id): ?array {
        self::init();

        $sql = "
            SELECT je.*, u.full_name AS creator_name
            FROM journal_entries je
            LEFT JOIN users u ON je.created_by = u.id
            WHERE je.id = ?
        ";
        $entry = self::$db->fetchOne($sql, [$id]);
        if (!$entry) return null;

        $itemsSql = "
            SELECT jei.*,
                   a.account_code, a.name AS account_name, a.type AS account_type, a.nature AS account_nature
            FROM journal_entry_items jei
            JOIN chart_of_accounts a ON jei.account_id = a.id
            WHERE jei.entry_id = ?
            ORDER BY jei.id ASC
        ";
        $entry['items'] = self::$db->fetchAll($itemsSql, [$id]);

        return $entry;
    }

    /**
     * إنشاء قيد يومية محاسبي مزدوج ومتزن ذرياً
     * يشترط تساوي إجمالي المدين مع إجمالي الدائن بدقة متناهية
     */
    public static function create(array $data, array $items, int $userId): int {
        self::init();

        if (empty($items) || count($items) < 2) {
            throw new Exception("يجب أن يحتوي القيد المحاسبي على طرفين على الأقل (طرف مدين وطرف دائن).");
        }

        $totalDebit = 0.0;
        $totalCredit = 0.0;
        $processedLines = [];

        foreach ($items as $line) {
            $accountId = (int)($line['account_id'] ?? 0);
            $debit = (float)($line['debit'] ?? 0.0);
            $credit = (float)($line['credit'] ?? 0.0);
            $lineDesc = trim($line['description'] ?? '');

            if ($accountId <= 0) continue;
            if ($debit <= 0 && $credit <= 0) continue;

            $totalDebit += $debit;
            $totalCredit += $credit;

            $processedLines[] = [
                'account_id'  => $accountId,
                'debit'       => $debit,
                'credit'      => $credit,
                'description' => $lineDesc
            ];
        }

        // فحص اتزان القيد المحاسبي
        $diff = abs(round($totalDebit - $totalCredit, 2));
        if ($diff > 0.01) {
            throw new Exception("القيد غير متزن محاسبياً! إجمالي المدين: (" . number_format($totalDebit, 2) . " ج.م) لا يساوي إجمالي الدائن: (" . number_format($totalCredit, 2) . " ج.م). الفارق: " . number_format($diff, 2) . " ج.م.");
        }

        return self::$db->transaction(function() use ($data, $processedLines, $totalDebit, $totalCredit, $userId) {
            $entryNumber = self::generateEntryNumber();

            $entryId = (int)self::$db->insert('journal_entries', [
                'entry_number'  => $entryNumber,
                'entry_date'    => $data['entry_date'] ?? date('Y-m-d'),
                'doc_reference' => trim($data['doc_reference'] ?? ''),
                'description'   => trim($data['description'] ?? ''),
                'total_debit'   => $totalDebit,
                'total_credit'  => $totalCredit,
                'is_balanced'   => 1,
                'is_posted'     => 1,
                'source_type'   => $data['source_type'] ?? 'manual',
                'source_id'     => !empty($data['source_id']) ? (int)$data['source_id'] : null,
                'created_by'    => $userId
            ]);

            // إدراج السطور وتحديث أرصدة الحسابات
            foreach ($processedLines as $pLine) {
                $pLine['entry_id'] = $entryId;
                self::$db->insert('journal_entry_items', $pLine);

                // تحديث رصيد الحساب المالي
                $acc = self::$db->fetchOne("SELECT current_balance, nature FROM chart_of_accounts WHERE id = ? FOR UPDATE", [$pLine['account_id']]);
                if ($acc) {
                    $cur = (float)$acc['current_balance'];
                    if ($acc['nature'] === 'debit') {
                        $cur += ($pLine['debit'] - $pLine['credit']);
                    } else {
                        $cur += ($pLine['credit'] - $pLine['debit']);
                    }
                    self::$db->update('chart_of_accounts', ['current_balance' => $cur], 'id = :id', [':id' => $pLine['account_id']]);
                }
            }

            Logger::audit('journal_entry_created', 'journal_entries', $entryId, null, [
                'entry_number' => $entryNumber,
                'total'        => $totalDebit
            ]);

            return $entryId;
        });
    }
}
