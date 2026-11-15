<?php
/**
 * متحكم عروض الأسعار والتحويل الفوري لفواتير بيع - Quotations Controller
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();
Auth::requirePermission('sales.quotes');

$db = Database::getInstance();
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

// 1. إنشاء عرض سعر جديد (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'store') {
    Security::verifyCsrf();

    try {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!$payload) {
            $payload = $_POST;
        }

        $items = $payload['items'] ?? [];
        $quotationId = Quotation::create($payload, $items, Auth::id());

        Helpers::jsonResponse([
            'success'      => true,
            'quotation_id' => $quotationId,
            'message'      => 'تم إنشاء وحفظ عرض السعر بنجاح.'
        ]);
    } catch (Throwable $e) {
        Helpers::jsonResponse([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
    exit;
}

// 2. تحويل عرض السعر إلى فاتورة بيع معتمدة بنقرة زر واحدة
if ($action === 'convert') {
    Security::verifyCsrf();

    $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
    $storeId = (int)($_POST['store_id'] ?? 2);

    try {
        $result = Quotation::convertToSale($id, Auth::id(), $storeId);
        if (Helpers::isAjax()) {
            Helpers::jsonResponse($result);
        }
        header('Location: ' . BASE_URL . 'sales.php?msg=converted&inv=' . urlencode($result['invoice_number']));
        exit;
    } catch (Throwable $e) {
        if (Helpers::isAjax()) {
            Helpers::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
        die("خطأ في التحويل: " . htmlspecialchars($e->getMessage()));
    }
}

// 3. عرض تفاصيل عرض السعر بالكامل (AJAX / JSON)
if ($action === 'view_details') {
    $id = (int)($_GET['id'] ?? 0);
    $quotation = Quotation::getFullQuotation($id);
    if (!$quotation) {
        Helpers::jsonResponse(['success' => false, 'message' => 'عرض السعر غير موجود'], 404);
    }
    Helpers::jsonResponse(['success' => true, 'quotation' => $quotation]);
    exit;
}

// 4. تحديث حالة عرض السعر
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_status') {
    Security::verifyCsrf();
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'draft';

    $ok = Quotation::updateStatus($id, $status);
    Helpers::jsonResponse(['success' => $ok, 'message' => 'تم تحديث حالة عرض السعر.']);
    exit;
}

// 5. شاشة إنشاء عرض سعر جديد
if ($action === 'create') {
    $customers = Customer::getAll();
    $stores = Store::getAll();
    $items = Item::getAll(['is_active' => 1], 500);

    View::render('modules/quotations/quotation_create', [
        'pageTitle' => 'إنشاء عرض سعر جديد للعميل',
        'customers' => $customers,
        'stores'    => $stores,
        'items'     => $items
    ]);
    exit;
}

// 6. القائمة الافتراضية لعروض الأسعار
$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo = $_GET['date_to'] ?? date('Y-m-d', strtotime('+30 days'));
$status = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

$filters = [
    'date_from' => $dateFrom,
    'date_to'   => $dateTo,
    'status'    => $status,
    'search'    => $search
];

$quotations = Quotation::getAll($filters, 200);

View::render('modules/quotations/quotations_list', [
    'pageTitle'  => 'عروض أسعار العملاء',
    'quotations' => $quotations,
    'dateFrom'   => $dateFrom,
    'dateTo'     => $dateTo,
    'status'     => $status,
    'search'     => $search
]);
