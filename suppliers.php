<?php
/**
 * متحكم إدارة الموردين وكشوف الحسابات وأعمار الديون - Suppliers Controller
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();
Auth::requirePermission('suppliers.view');

$db = Database::getInstance();
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

// 1. إضافة مورد جديد (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'store') {
    Auth::requirePermission('suppliers.manage');
    Security::verifyCsrf();

    try {
        $supplierId = Supplier::create($_POST);
        if (Helpers::isAjax()) {
            Helpers::jsonResponse(['success' => true, 'id' => $supplierId, 'message' => 'تم إضافة المورد بنجاح']);
        }
        header('Location: ' . BASE_URL . 'suppliers.php?msg=created');
        exit;
    } catch (Throwable $e) {
        if (Helpers::isAjax()) {
            Helpers::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
        $error = $e->getMessage();
    }
}

// 2. تعديل مورد (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    Auth::requirePermission('suppliers.manage');
    Security::verifyCsrf();

    $id = (int)($_POST['id'] ?? 0);
    try {
        Supplier::update($id, $_POST);
        if (Helpers::isAjax()) {
            Helpers::jsonResponse(['success' => true, 'message' => 'تم تحديث بيانات المورد بنجاح']);
        }
        header('Location: ' . BASE_URL . 'suppliers.php?msg=updated');
        exit;
    } catch (Throwable $e) {
        if (Helpers::isAjax()) {
            Helpers::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
        $error = $e->getMessage();
    }
}

// 3. حذف أو تعطيل مورد (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    Auth::requirePermission('suppliers.manage');
    Security::verifyCsrf();

    $id = (int)($_POST['id'] ?? 0);
    try {
        Supplier::delete($id);
        if (Helpers::isAjax()) {
            Helpers::jsonResponse(['success' => true, 'message' => 'تم حذف أو تعطيل المورد بنجاح']);
        }
        header('Location: ' . BASE_URL . 'suppliers.php?msg=deleted');
        exit;
    } catch (Throwable $e) {
        if (Helpers::isAjax()) {
            Helpers::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}

// 4. تقرير أعمار ديون الموردين (Aging Report)
if ($action === 'aging') {
    Auth::requirePermission('suppliers.statement');
    $agingData = Supplier::getAgingReport();

    View::render('modules/suppliers/supplier_aging', [
        'pageTitle' => 'تقرير أعمار ديون الموردين (30، 60، 90، +120 يوماً)',
        'suppliers' => $agingData['suppliers'],
        'totals'    => $agingData['totals']
    ]);
    exit;
}

// 5. كشف حساب المورد التفصيلي (Statement)
if ($action === 'statement') {
    Auth::requirePermission('suppliers.statement');
    $id = (int)($_GET['id'] ?? 0);
    $dateFrom = $_GET['date_from'] ?? date('Y-01-01');
    $dateTo = $_GET['date_to'] ?? date('Y-m-d');

    try {
        $statement = Supplier::getStatement($id, $dateFrom, $dateTo);
        View::render('modules/suppliers/supplier_statement', [
            'pageTitle' => 'كشف حساب المورد التفصيلي: ' . ($statement['supplier']['name'] ?? ''),
            'statement' => $statement,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo
        ]);
        exit;
    } catch (Throwable $e) {
        die("خطأ في جلب كشف الحساب: " . htmlspecialchars($e->getMessage()));
    }
}

// 6. القائمة الافتراضية للموردين
$search = trim($_GET['search'] ?? '');
$status = isset($_GET['status']) ? (string)$_GET['status'] : '1';

$filters = [
    'search'    => $search,
    'is_active' => $status
];

$suppliers = Supplier::getAll($filters, 200);

// إحصائيات عامة
$totalSuppliersCount = count($suppliers);
$totalBalanceOwed = 0.0;
foreach ($suppliers as $s) {
    $totalBalanceOwed += (float)$s['current_balance'];
}

View::render('modules/suppliers/suppliers_list', [
    'pageTitle'          => 'دليل الموردين والحسابات الدائنة',
    'suppliers'          => $suppliers,
    'totalCount'         => $totalSuppliersCount,
    'totalBalanceOwed'   => $totalBalanceOwed,
    'search'             => $search,
    'status'             => $status
]);
