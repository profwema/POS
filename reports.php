<?php
/**
 * متحكم مركز التقارير والضرائب المصرية والإقرارات - Reports & Tax Controller
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();
Auth::requirePermission('sales.view');

$type = $_GET['type'] ?? 'index';
$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');

// 1. تقرير الإقرار الضريبي لضريبة القيمة المضافة (نموذج 10)
if ($type === 'vat') {
    $vatData = ReportService::getVatDeclaration($dateFrom, $dateTo);

    View::render('modules/reports/vat_declaration', [
        'pageTitle' => 'إقرار ضريبة القيمة المضافة (نموذج 10)',
        'vatData'   => $vatData,
        'dateFrom'  => $dateFrom,
        'dateTo'    => $dateTo
    ]);
    exit;
}

// 2. تقرير مبيعات وأرباح الأصناف وهامش الربحية
if ($type === 'profitability') {
    $categoryId = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
    $search = trim($_GET['search'] ?? '');

    $profitData = ReportService::getItemProfitability($dateFrom, $dateTo, $categoryId, $search);
    $categories = Category::getAll();

    View::render('modules/reports/item_profitability', [
        'pageTitle'  => 'تقرير مبيعات وأرباح الأصناف وهامش الربحية',
        'items'      => $profitData['items'],
        'totals'     => $profitData['totals'],
        'categories' => $categories,
        'categoryId' => $categoryId,
        'search'     => $search,
        'dateFrom'   => $dateFrom,
        'dateTo'     => $dateTo
    ]);
    exit;
}

// 3. تقرير حركة المخزون والأصناف الراكدة وسريعة الدوران
if ($type === 'movement') {
    $storeId = !empty($_GET['store_id']) ? (int)$_GET['store_id'] : 2;
    $movementData = ReportService::getInventoryMovement($dateFrom, $dateTo, $storeId);
    $stores = Store::getAll();

    View::render('modules/reports/inventory_movement', [
        'pageTitle'      => 'تقرير حركة المخزون وتحليل دوران الأصناف',
        'movementData'   => $movementData,
        'stores'         => $stores,
        'storeId'        => $storeId,
        'dateFrom'       => $dateFrom,
        'dateTo'         => $dateTo
    ]);
    exit;
}

// 4. كشف حساب العميل التفصيلي
if ($type === 'customer_statement') {
    $customerId = !empty($_GET['customer_id']) ? (int)$_GET['customer_id'] : 1;
    $customers = Customer::getAll();

    try {
        $statement = ReportService::getCustomerStatement($customerId, $dateFrom, $dateTo);
        View::render('modules/reports/customer_statement', [
            'pageTitle'  => 'كشف حساب العميل: ' . ($statement['customer']['name'] ?? ''),
            'statement'  => $statement,
            'customers'  => $customers,
            'customerId' => $customerId,
            'dateFrom'   => $dateFrom,
            'dateTo'     => $dateTo
        ]);
        exit;
    } catch (Throwable $e) {
        die("خطأ: " . htmlspecialchars($e->getMessage()));
    }
}

// 5. الشاشة الرئيسية لمركز التقارير
View::render('modules/reports/reports_index', [
    'pageTitle' => 'مركز التقارير التحليلية والضرائب والرقابة',
    'dateFrom'  => $dateFrom,
    'dateTo'    => $dateTo
]);
