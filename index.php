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
$userId = (int)Auth::id();

// 1. إحصائيات عامة
$totalUsers = (int)$db->fetchColumn("SELECT COUNT(*) FROM `users` WHERE `is_active` = 1");
$totalStores = (int)$db->fetchColumn("SELECT COUNT(*) FROM `stores` WHERE `is_active` = 1");

// 2. إحصائيات مبيعات اليوم
$today = date('Y-m-d');
$todayStats = $db->fetchOne("
    SELECT 
        COUNT(*) AS invoices_count,
        COALESCE(SUM(grand_total), 0) AS total_revenue,
        COALESCE(SUM(tax_amount), 0) AS total_tax,
        COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN grand_total ELSE 0 END), 0) AS cash_revenue
    FROM sales
    WHERE DATE(created_at) = ?
", [$today]);

// 3. إحصائيات المخزون والتقييم المالي
$inventoryValuation = InventoryService::getInventoryValuation();
$lowStockAlerts = InventoryService::getLowStockAlerts();
$expiringAlerts = InventoryService::getExpiringBatchesAlerts(60);

// 4. أحدث الفواتير الصادرة
$recentSales = Sale::getRecentSales(6);

// 5. الوردية الحالية
$activeShift = Shift::getActiveShift($userId);

// عرض لوحة التحكم الرئيسية
View::render('modules/dashboard/index', [
    'pageTitle'          => 'لوحة التحكم المالية والإدارية',
    'currentUser'        => Auth::user(),
    'totalUsers'         => $totalUsers,
    'totalStores'        => $totalStores,
    'todayStats'         => $todayStats,
    'inventoryValuation' => $inventoryValuation,
    'lowStockCount'      => count($lowStockAlerts),
    'expiringCount'      => count($expiringAlerts),
    'recentSales'        => $recentSales,
    'activeShift'        => $activeShift
], 'main');
