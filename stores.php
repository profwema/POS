<?php
/**
 * متحكم المستودعات والمخازن - Stores & Warehouses Controller
 * WAM Tech POS & ERP
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();
Auth::requirePermission('inventory.view');

// Handle POST Requests (Create, Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::verifyCsrf();
    Auth::requirePermission('inventory.create');
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create_store') {
            $name = trim($_POST['name'] ?? '');
            $code = strtoupper(trim($_POST['code'] ?? ''));

            if (empty($name) || empty($code)) {
                throw new Exception("اسم المستودع وكوده حقول إجبارية.");
            }

            $id = Store::create($_POST);
            Logger::audit('store_created', "إضافة مستودع جديد: {$name} ({$code})", 'stores', $id);
            Helpers::flash('success', "تم إنشاء المستودع '{$name}' بنجاح.");
            Helpers::redirect('stores.php');
        }

        if ($action === 'update_store') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');

            if (!$id || empty($name)) {
                throw new Exception("بيانات المستودع غير مكتملة.");
            }

            Store::update($id, $_POST);
            Logger::audit('store_updated', "تحديث بيانات مستودع: {$name} (ID: {$id})", 'stores', $id);
            Helpers::flash('success', "تم تحديث المستودع '{$name}' بنجاح.");
            Helpers::redirect('stores.php');
        }
    } catch (Exception $e) {
        Helpers::flash('error', $e->getMessage());
        Helpers::redirect('stores.php');
    }
}

// GET: List stores and their financial valuations
$stores = Store::getAll(false);
foreach ($stores as &$st) {
    $st['stats'] = Store::getStoreStats($st['id']);
}
unset($st);

View::render('modules/inventory/stores_list', [
    'stores' => $stores
]);

