<?php
/**
 * متحكم إدارة المستخدمين والموظفين وحوكمة الصلاحيات - Users & RBAC Controller
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();
Auth::requirePermission('settings.users');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$msg = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';

// 1. إضافة مستخدم جديد (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'store') {
    Security::verifyCsrf();

    try {
        User::create($_POST);
        header('Location: ' . BASE_URL . 'users.php?msg=user_created');
        exit;
    } catch (Throwable $e) {
        header('Location: ' . BASE_URL . 'users.php?err=' . urlencode($e->getMessage()));
        exit;
    }
}

// 2. تعديل بيانات مستخدم (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    Security::verifyCsrf();
    $id = (int)($_POST['id'] ?? 0);

    try {
        User::update($id, $_POST);
        header('Location: ' . BASE_URL . 'users.php?msg=user_updated');
        exit;
    } catch (Throwable $e) {
        header('Location: ' . BASE_URL . 'users.php?err=' . urlencode($e->getMessage()));
        exit;
    }
}

// 3. تبديل حالة المستخدم (تفعيل / تعطيل)
if ($action === 'toggle_status') {
    $id = (int)($_GET['id'] ?? 0);

    try {
        User::toggleStatus($id);
        header('Location: ' . BASE_URL . 'users.php?msg=status_updated');
        exit;
    } catch (Throwable $e) {
        header('Location: ' . BASE_URL . 'users.php?err=' . urlencode($e->getMessage()));
        exit;
    }
}

// 4. استرجاع بيانات المستخدمين والأدوار والفروع
$users = User::getAll();
$roles = User::getRoles();
$branches = User::getBranches();

// 5. عرض الشاشة
View::render('modules/users/users_list', [
    'pageTitle' => 'إدارة المستخدمين والموظفين وحوكمة الصلاحيات',
    'users'     => $users,
    'roles'     => $roles,
    'branches'  => $branches,
    'msg'       => $msg,
    'error'     => $error
]);
