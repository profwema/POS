<?php
/**
 * متحكم الأصناف والمخزون - Items & Inventory Controller
 * WAM Tech POS & ERP
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();
Auth::requirePermission('inventory.view');

// Handle POST Requests (Create, Update, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::verifyCsrf();
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create_item') {
            Auth::requirePermission('inventory.create');

            $name = trim($_POST['name'] ?? '');
            $barcode = trim($_POST['barcode'] ?? '');

            if (empty($name) || empty($barcode)) {
                throw new Exception("اسم الصنف والباركود حقول إجبارية.");
            }

            // Check barcode uniqueness
            $existing = Item::findByBarcode($barcode);
            if ($existing) {
                throw new Exception("الباركود '{$barcode}' مستخدم بالفعل للصنف: '{$existing['name']}'. يرجى استخدام باركود فريد.");
            }

            $itemId = Item::create($_POST);

            // Check if initial opening stock was provided
            $openingStock = (float)($_POST['opening_stock'] ?? 0);
            $openingStoreId = (int)($_POST['opening_store_id'] ?? 1);
            if ($openingStock > 0 && $openingStoreId > 0) {
                $cost = (float)($_POST['cost_price'] ?? 0);
                InventoryService::recordStockMove(
                    $itemId,
                    $openingStoreId,
                    'opening',
                    $openingStock,
                    $cost,
                    'initial_setup',
                    $itemId,
                    'رصيد افتتاحي أولي عند إنشاء الصنف',
                    null,
                    Auth::id()
                );
            }

            Logger::audit('item_created', "إضافة صنف جديد: {$name} (باركود: {$barcode})", 'items', $itemId);
            Helpers::flash('success', "تمت إضافة الصنف '{$name}' بنجاح.");
            Helpers::redirect('items.php');
        }

        if ($action === 'update_item') {
            Auth::requirePermission('inventory.edit');

            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $barcode = trim($_POST['barcode'] ?? '');

            if (!$id || empty($name) || empty($barcode)) {
                throw new Exception("بيانات الصنف غير مكتملة.");
            }

            // Check barcode uniqueness if changed
            $existing = Item::findByBarcode($barcode);
            if ($existing && (int)$existing['id'] !== $id) {
                throw new Exception("الباركود '{$barcode}' مسجل لصنف آخر: '{$existing['name']}'.");
            }

            Item::update($id, $_POST);
            Logger::audit('item_updated', "تحديث بيانات الصنف: {$name} (ID: {$id})", 'items', $id);
            Helpers::flash('success', "تم تحديث بيانات الصنف '{$name}' بنجاح.");
            Helpers::redirect('items.php');
        }

        if ($action === 'delete_item') {
            Auth::requirePermission('inventory.delete');

            $id = (int)($_POST['id'] ?? 0);
            $item = Item::findById($id);
            if ($item) {
                Item::delete($id);
                Logger::audit('item_deleted', "حذف / إيقاف صنف: {$item['name']} (ID: {$id})", 'items', $id);
                Helpers::flash('success', "تم حذف أو تعطيل الصنف '{$item['name']}' بنجاح.");
            }
            Helpers::redirect('items.php');
        }
    } catch (Exception $e) {
        Helpers::flash('error', $e->getMessage());
        Helpers::redirect('items.php');
    }
}

// GET Request: Prepare filters, pagination & data
$filters = [
    'search'      => trim($_GET['search'] ?? ''),
    'category_id' => !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null,
    'store_id'    => !empty($_GET['store_id']) ? (int)$_GET['store_id'] : null,
    'is_active'   => 1
];

$filterType = $_GET['filter_type'] ?? '';
if ($filterType === 'scale') {
    $filters['is_scale'] = 1;
} elseif ($filterType === 'low_stock' || !empty($_GET['low_stock_only'])) {
    $filters['low_stock_only'] = 1;
}

// Pagination
$limit = 25;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$totalCount = Item::count($filters);
$totalPages = (int)ceil($totalCount / $limit);
$items = Item::getAll($filters, $limit, $offset);

// Statistics & Valuation
$valuation = InventoryService::getInventoryValuation();
$lowStockAlerts = InventoryService::getLowStockAlerts();
$expiringAlerts = InventoryService::getExpiringBatchesAlerts(60);

// Lookup Lists
$categories = Category::getAll();
$units = Unit::getAll();
$stores = Store::getAll();

View::render('modules/inventory/items_list', [
    'items'          => $items,
    'totalCount'     => $totalCount,
    'page'           => $page,
    'limit'          => $limit,
    'offset'         => $offset,
    'totalPages'     => $totalPages,
    'filters'        => $filters,
    'valuation'      => $valuation,
    'lowStockAlerts' => $lowStockAlerts,
    'expiringAlerts' => $expiringAlerts,
    'categories'     => $categories,
    'units'          => $units,
    'stores'         => $stores
]);

