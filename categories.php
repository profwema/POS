<?php
/**
 * متحكم الأقسام وتصنيفات المنتجات والوحدات - Categories & Units Controller
 * WAM Tech POS & ERP
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();
Auth::requirePermission('inventory.view');

// Handle POST: Create / Update Categories and Units
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::verifyCsrf();
    Auth::requirePermission('inventory.create');
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create_category') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                throw new Exception("اسم القسم مطلوب.");
            }
            $id = Category::create($_POST);
            Logger::audit('category_created', "إضافة تصنيف جديد: {$name}", 'categories', $id);
            Helpers::flash('success', "تم إنشاء التصنيف '{$name}' بنجاح.");
            Helpers::redirect('categories.php');
        }

        if ($action === 'update_category') {
            Auth::requirePermission('inventory.edit');
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            if (!$id || empty($name)) {
                throw new Exception("بيانات القسم غير مكتملة.");
            }
            Category::update($id, $_POST);
            Logger::audit('category_updated', "تحديث تصنيف: {$name} (ID: {$id})", 'categories', $id);
            Helpers::flash('success', "تم تحديث التصنيف '{$name}' بنجاح.");
            Helpers::redirect('categories.php');
        }

        if ($action === 'create_unit') {
            $name = trim($_POST['name'] ?? '');
            $symbol = trim($_POST['symbol'] ?? '');
            if (empty($name) || empty($symbol)) {
                throw new Exception("اسم الوحدة ورمزها مطلوبان.");
            }
            $id = Unit::create($_POST);
            Logger::audit('unit_created', "إضافة وحدة قياس جديدة: {$name} ({$symbol})", 'units', $id);
            Helpers::flash('success', "تم إنشاء وحدة القياس '{$name}' بنجاح.");
            Helpers::redirect('categories.php');
        }

        if ($action === 'update_unit') {
            Auth::requirePermission('inventory.edit');
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $symbol = trim($_POST['symbol'] ?? '');
            if (!$id || empty($name) || empty($symbol)) {
                throw new Exception("بيانات الوحدة غير مكتملة.");
            }
            Unit::update($id, $_POST);
            Logger::audit('unit_updated', "تحديث وحدة قياس: {$name} (ID: {$id})", 'units', $id);
            Helpers::flash('success', "تم تحديث وحدة القياس '{$name}' بنجاح.");
            Helpers::redirect('categories.php');
        }
    } catch (Exception $e) {
        Helpers::flash('error', $e->getMessage());
        Helpers::redirect('categories.php');
    }
}

// GET: Fetch Categories and Units
$categories = Category::getAll(false);
$units = Unit::getAll(false);

View::render('modules/inventory/categories_list', [
    'categories' => $categories,
    'units'      => $units
]);

