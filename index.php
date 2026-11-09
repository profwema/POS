<?php
/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * بوابة لوحة التحكم الرئيسية - Main Dashboard Controller
 * 
 * هندسة وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
 */

require_once __DIR__ . '/config/config.php';

// اشتراط تسجيل الدخول
Auth::requireLogin();

// إذا كان المستخدم كاشير فقط، نوجهه مباشرة إلى شاشة نقطة البيع
if (Auth::isCashier()) {
    header('Location: ' . BASE_URL . 'pos.php');
    exit();
}

$db = Database::getInstance();

// جلب إحصائيات النواة التأسيسية
$totalUsers = $db->fetchColumn("SELECT COUNT(*) FROM `users` WHERE `is_active` = 1");
$totalSettings = $db->fetchColumn("SELECT COUNT(*) FROM `system_settings`");

// عرض لوحة التحكم الرئيسية
View::render('modules/dashboard/index', [
    'pageTitle'     => 'لوحة التحكم المالية والإدارية',
    'currentUser'   => Auth::user(),
    'totalUsers'    => $totalUsers,
    'totalSettings' => $totalSettings
], 'main');

