<?php
/**
 * متحكم إدارة مرتجعات البيع والشراء - Returns Controller
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$activeTab = $_GET['tab'] ?? 'sales'; // 'sales' or 'purchases'

// 1. تسجيل مرتجع مبيعات جديد (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'store_sale_return') {
    Auth::requirePermission('sales.return');
    Security::verifyCsrf();

    try {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!$payload) {
            $payload = $_POST;
        }

        $result = ReturnService::processSaleReturn($payload, Auth::id());
        Helpers::jsonResponse($result);
    } catch (Throwable $e) {
        Helpers::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
    }
    exit;
}

// 2. تسجيل مرتجع مشتريات للمورد (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'store_purchase_return') {
    Auth::requirePermission('purchases.return');
    Security::verifyCsrf();

    try {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!$payload) {
            $payload = $_POST;
        }

        $result = ReturnService::processPurchaseReturn($payload, Auth::id());
        Helpers::jsonResponse($result);
    } catch (Throwable $e) {
        Helpers::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
    }
    exit;
}

// 3. القائمة الافتراضية للمرتجعات
$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');
$search = trim($_GET['search'] ?? '');

$filters = [
    'date_from' => $dateFrom,
    'date_to'   => $dateTo,
    'search'    => $search
];

$salesReturns = ReturnService::getSalesReturns($filters, 100);
$purchaseReturns = ReturnService::getPurchaseReturns($filters, 100);

$customers = Customer::getAll();
$suppliers = Supplier::getAll(['is_active' => 1]);
$stores = Store::getAll();
$items = Item::getAll(['is_active' => 1], 500);

View::render('modules/returns/returns_list', [
    'pageTitle'       => 'إدارة مردودات المبيعات والمشتريات',
    'salesReturns'    => $salesReturns,
    'purchaseReturns' => $purchaseReturns,
    'activeTab'       => $activeTab,
    'customers'       => $customers,
    'suppliers'       => $suppliers,
    'stores'          => $stores,
    'items'           => $items,
    'dateFrom'        => $dateFrom,
    'dateTo'          => $dateTo,
    'search'          => $search
]);
