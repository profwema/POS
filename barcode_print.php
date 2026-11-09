<?php
/**
 * طباعة ملصقات الباركود للمنتجات والموازين - Barcode Label Printing
 * WAM Tech POS & ERP
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();
Auth::requirePermission('inventory.view');

$selectedItemId = !empty($_GET['item_id']) ? (int)$_GET['item_id'] : null;
$selectedItem = null;

if ($selectedItemId) {
    $selectedItem = Item::findById($selectedItemId);
}

$allItems = Item::getAll(['is_active' => 1], 500);

View::render('modules/inventory/barcode_print', [
    'selectedItem' => $selectedItem,
    'allItems'     => $allItems
]);

