<?php
/**
 * متحكم فواتير المشتريات والتوريد - Purchases Controller
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();
Auth::requirePermission('purchases.view');

$db = Database::getInstance();
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

// 1. معالجة حفظ فاتورة شراء جديدة (AJAX / POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'store') {
    Auth::requirePermission('purchases.create');
    Security::verifyCsrf();

    try {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!$payload) {
            $payload = $_POST;
        }

        $result = PurchaseService::createPurchase($payload, Auth::id());
        Helpers::jsonResponse($result);
    } catch (Throwable $e) {
        Helpers::jsonResponse([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
    exit;
}

// 2. عرض تفاصيل فاتورة شراء بالكامل (AJAX / JSON)
if ($action === 'view_details') {
    $id = (int)($_GET['id'] ?? 0);
    $purchase = PurchaseService::getPurchaseDetails($id);
    if (!$purchase) {
        Helpers::jsonResponse(['success' => false, 'message' => 'الفاتورة غير موجودة'], 404);
    }
    Helpers::jsonResponse(['success' => true, 'purchase' => $purchase]);
    exit;
}

// 3. شاشة تسجيل فاتورة توريد جديدة
if ($action === 'create') {
    Auth::requirePermission('purchases.create');
    
    // جلب قائمة الموردين والمخازن والأصناف
    $suppliers = Supplier::getAll(['is_active' => 1]);
    $stores = Store::getAll();
    $items = Item::getAll(['is_active' => 1], 500);

    View::render('modules/purchases/purchase_create', [
        'pageTitle' => 'تسجيل فاتورة مشتريات جديدة',
        'suppliers' => $suppliers,
        'stores'    => $stores,
        'items'     => $items
    ]);
    exit;
}

// 4. القائمة الافتراضية (سجل المشتريات مع الفلاتر والإحصائيات)
$dateFrom = $_GET['date_from'] ?? date('Y-m-01'); // من أول الشهر الحالي
$dateTo = $_GET['date_to'] ?? date('Y-m-d');
$supplierId = !empty($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;
$paymentStatus = $_GET['payment_status'] ?? '';
$search = trim($_GET['search'] ?? '');

$filters = [
    'date_from'      => $dateFrom,
    'date_to'        => $dateTo,
    'supplier_id'    => $supplierId,
    'payment_status' => $paymentStatus,
    'search'         => $search
];

$purchases = PurchaseService::getPurchases($filters, 200);
$stats = PurchaseService::getPurchasesSummary($filters);
$suppliers = Supplier::getAll(['is_active' => 1]);

View::render('modules/purchases/purchases_list', [
    'pageTitle'     => 'سجل فواتير المشتريات والتوريد',
    'purchases'     => $purchases,
    'stats'         => $stats,
    'suppliers'     => $suppliers,
    'dateFrom'      => $dateFrom,
    'dateTo'        => $dateTo,
    'supplierId'    => $supplierId,
    'paymentStatus' => $paymentStatus,
    'search'        => $search
]);
