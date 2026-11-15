<?php
/**
 * متحكم المالية والحسابات العامة والشجرة المحاسبية - General Accounting Controller
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();
Auth::requirePermission('accounting.view');

$db = Database::getInstance();
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$tab = $_GET['tab'] ?? 'chart'; // 'chart', 'journal', 'vouchers', 'trial_balance', 'income_statement', 'statement'

// 1. إضافة حساب جديد في الشجرة (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'store_account') {
    Auth::requirePermission('accounting.chart');
    Security::verifyCsrf();

    try {
        $id = Account::create($_POST);
        if (Helpers::isAjax()) {
            Helpers::jsonResponse(['success' => true, 'id' => $id, 'message' => 'تم إضافة الحساب بنجاح']);
        }
        header('Location: ' . BASE_URL . 'accounting.php?tab=chart&msg=account_created');
        exit;
    } catch (Throwable $e) {
        if (Helpers::isAjax()) {
            Helpers::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
        die("خطأ: " . htmlspecialchars($e->getMessage()));
    }
}

// 2. تسجيل قيد يومية يدوي متزن (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'store_journal') {
    Auth::requirePermission('accounting.journal');
    Security::verifyCsrf();

    try {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!$payload) {
            $payload = $_POST;
        }

        $items = $payload['items'] ?? [];
        $entryId = JournalEntry::create($payload, $items, Auth::id());

        Helpers::jsonResponse([
            'success'  => true,
            'entry_id' => $entryId,
            'message'  => 'تم حفظ وترحيل القيد المحاسبي بنجاح.'
        ]);
    } catch (Throwable $e) {
        Helpers::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
    }
    exit;
}

// 3. تحرير سند قبض أو صرف (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'store_voucher') {
    Auth::requirePermission('accounting.vouchers');
    Security::verifyCsrf();

    try {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!$payload) {
            $payload = $_POST;
        }

        $result = AccountingService::createVoucher($payload, Auth::id());
        Helpers::jsonResponse($result);
    } catch (Throwable $e) {
        Helpers::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
    }
    exit;
}

// 4. استعراض تفاصيل قيد يومية بالكامل (AJAX / JSON)
if ($action === 'view_journal') {
    $id = (int)($_GET['id'] ?? 0);
    $entry = JournalEntry::findById($id);
    if (!$entry) {
        Helpers::jsonResponse(['success' => false, 'message' => 'القيد غير موجود'], 404);
    }
    Helpers::jsonResponse(['success' => true, 'entry' => $entry]);
    exit;
}

// -------------------------------------------------------------
// التوجيه للتبويبات والواجهات
// -------------------------------------------------------------

// تبويب كشف حساب الأستاذ المساعد للحساب (General Ledger Statement)
if ($tab === 'statement') {
    $accountId = (int)($_GET['account_id'] ?? 0);
    $dateFrom = $_GET['date_from'] ?? date('Y-01-01');
    $dateTo = $_GET['date_to'] ?? date('Y-m-d');

    try {
        $statement = Account::getAccountStatement($accountId, $dateFrom, $dateTo);
        View::render('modules/accounting/account_statement', [
            'pageTitle' => 'دفتر الأستاذ: ' . $statement['account']['name'],
            'statement' => $statement,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo
        ]);
        exit;
    } catch (Throwable $e) {
        die("خطأ في جلب كشف الحساب: " . htmlspecialchars($e->getMessage()));
    }
}

// تبويب ميزان المراجعة (Trial Balance)
if ($tab === 'trial_balance') {
    Auth::requirePermission('accounting.reports');
    $asOfDate = $_GET['as_of_date'] ?? date('Y-m-d');
    $trialBalance = AccountingService::getTrialBalance($asOfDate);

    View::render('modules/accounting/trial_balance', [
        'pageTitle'    => 'ميزان المراجعة بالأرصدة والمجاميع',
        'trialBalance' => $trialBalance,
        'asOfDate'     => $asOfDate,
        'activeTab'    => 'trial_balance'
    ]);
    exit;
}

// تبويب قائمة الدخل (Income Statement)
if ($tab === 'income_statement') {
    Auth::requirePermission('accounting.reports');
    $dateFrom = $_GET['date_from'] ?? date('Y-01-01');
    $dateTo = $_GET['date_to'] ?? date('Y-m-d');
    $incomeStatement = AccountingService::getIncomeStatement($dateFrom, $dateTo);

    View::render('modules/accounting/income_statement', [
        'pageTitle'       => 'قائمة الدخل والأرباح والخسائر',
        'incomeStatement' => $incomeStatement,
        'dateFrom'        => $dateFrom,
        'dateTo'          => $dateTo,
        'activeTab'       => 'income_statement'
    ]);
    exit;
}

// تبويب سندات القبض والصرف (Vouchers)
if ($tab === 'vouchers') {
    Auth::requirePermission('accounting.vouchers');
    $vType = $_GET['type'] ?? '';
    $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
    $dateTo = $_GET['date_to'] ?? date('Y-m-d');
    $search = trim($_GET['search'] ?? '');

    $filters = [
        'voucher_type' => $vType,
        'date_from'    => $dateFrom,
        'date_to'      => $dateTo,
        'search'       => $search
    ];

    $vouchers = AccountingService::getVouchers($filters, 200);
    $cashAccounts = Account::getFlatList(true);

    View::render('modules/accounting/vouchers', [
        'pageTitle'    => 'سندات القبض والصرف المالي',
        'vouchers'     => $vouchers,
        'cashAccounts' => $cashAccounts,
        'dateFrom'     => $dateFrom,
        'dateTo'       => $dateTo,
        'vType'        => $vType,
        'search'       => $search,
        'activeTab'    => 'vouchers'
    ]);
    exit;
}

// تبويب قيود اليومية العامة (Journal Entries)
if ($tab === 'journal') {
    Auth::requirePermission('accounting.journal');
    $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
    $dateTo = $_GET['date_to'] ?? date('Y-m-d');
    $sourceType = $_GET['source_type'] ?? '';
    $search = trim($_GET['search'] ?? '');

    $filters = [
        'date_from'   => $dateFrom,
        'date_to'     => $dateTo,
        'source_type' => $sourceType,
        'search'      => $search
    ];

    $entries = JournalEntry::getAll($filters, 200);
    $postingAccounts = Account::getFlatList(true);

    View::render('modules/accounting/journal_entries', [
        'pageTitle'       => 'سجل قيود اليومية العامة المزدوجة',
        'entries'         => $entries,
        'postingAccounts' => $postingAccounts,
        'dateFrom'        => $dateFrom,
        'dateTo'          => $dateTo,
        'sourceType'      => $sourceType,
        'search'          => $search,
        'activeTab'       => 'journal'
    ]);
    exit;
}

// التبويب الافتراضي: شجرة ودليل الحسابات (Chart of Accounts)
Auth::requirePermission('accounting.chart');
$coaData = Account::getAllTree();
$parentAccounts = Account::getFlatList(false);

View::render('modules/accounting/chart_of_accounts', [
    'pageTitle'      => 'شجرة ودليل الحسابات المالي',
    'tree'           => $coaData['tree'],
    'allAccounts'    => $coaData['all'],
    'parentAccounts' => $parentAccounts,
    'activeTab'      => 'chart'
]);
