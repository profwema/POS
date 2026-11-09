<?php
/**
 * متحكم أذونات تحويل المخزون بين المستودعات - Inter-store Transfers Controller
 * WAM Tech POS & ERP
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();
Auth::requirePermission('inventory.view');

$db = Database::getInstance();

// Handle POST: Create Transfer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::verifyCsrf();
    Auth::requirePermission('inventory.edit');

    try {
        $fromStoreId = (int)($_POST['from_store_id'] ?? 0);
        $toStoreId = (int)($_POST['to_store_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');
        $itemIds = $_POST['item_ids'] ?? [];
        $quantities = $_POST['quantities'] ?? [];

        if (!$fromStoreId || !$toStoreId) {
            throw new Exception("يجب اختيار المستودع المصدر والمستودع المستلم.");
        }

        if (empty($itemIds) || !is_array($itemIds)) {
            throw new Exception("يجب إضافة صنف واحد على الأقل للتحويل.");
        }

        $items = [];
        foreach ($itemIds as $index => $itemId) {
            $qty = (float)($quantities[$index] ?? 0);
            if ($qty > 0) {
                $items[] = [
                    'item_id'  => (int)$itemId,
                    'quantity' => $qty
                ];
            }
        }

        if (empty($items)) {
            throw new Exception("الكميات المدخلة للتحويل غير صالحة.");
        }

        $result = InventoryService::transferStock($fromStoreId, $toStoreId, $items, $notes, Auth::id());
        Logger::audit('stock_transferred', "تحويل مخزني رقم {$result['transfer_number']} من مخزن {$fromStoreId} إلى {$toStoreId}", 'store_transfers', $result['transfer_id']);

        Helpers::flash('success', "تم تنفيذ التحويل المخزني رقم '{$result['transfer_number']}' بنجاح ونقل الأرصدة.");
        Helpers::redirect('transfers.php');
    } catch (Exception $e) {
        Helpers::flash('error', $e->getMessage());
        Helpers::redirect('transfers.php');
    }
}

// GET: Fetch transfers history
$transfersSql = "
    SELECT 
        t.*,
        sf.name AS from_store_name,
        st.name AS to_store_name,
        u.full_name AS created_by_name
    FROM store_transfers t
    LEFT JOIN stores sf ON t.from_store_id = sf.id
    LEFT JOIN stores st ON t.to_store_id = st.id
    LEFT JOIN users u ON t.created_by = u.id
    ORDER BY t.id DESC
    LIMIT 100
";
$transfers = $db->fetchAll($transfersSql);

$stores = Store::getAll(true);
$items = Item::getAll(['is_active' => 1], 300);

View::render('modules/inventory/transfers', [
    'transfers' => $transfers,
    'stores'    => $stores,
    'items'     => $items
]);

