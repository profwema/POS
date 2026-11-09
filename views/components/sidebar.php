<?php
/**
 * القائمة الجانبية - Sidebar Component
 */
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
?>
<aside class="app-sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo-wrap">
            <img src="<?= BASE_URL ?>assets/brand/logo-horizontal-light.png" alt="المبرمج | Almubarmig" style="height: 34px; max-width: 155px; object-fit: contain;">
            <span class="badge badge-amber" style="font-size: 0.65rem;">Enterprise</span>
        </div>
        <div style="margin-top: 0.25rem;">
            <div class="app-title">WAM Tech POS & ERP</div>
            <div class="app-subtitle">إصدار السوق المصري v2.1</div>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li class="nav-header">الرئيسية ونقاط البيع</li>
        
        <li class="nav-item">
            <a href="<?= BASE_URL ?>index.php" class="nav-link <?= ($currentPage === 'index.php') ? 'active' : '' ?>">
                <i class="fas fa-chart-pie"></i>
                <span>لوحة التحكم المالية</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= BASE_URL ?>pos.php" class="nav-link <?= ($currentPage === 'pos.php') ? 'active' : '' ?>" style="color: #fbbf24;">
                <i class="fas fa-barcode" style="color: var(--brand-amber);"></i>
                <span>نقطة البيع السريعة (POS)</span>
                <span class="badge badge-amber">F1-F10</span>
            </a>
        </li>

        <li class="nav-header">إدارة العمليات التجارية</li>

        <li class="nav-item">
            <a href="<?= BASE_URL ?>items.php" class="nav-link <?= ($currentPage === 'items.php') ? 'active' : '' ?>">
                <i class="fas fa-boxes"></i>
                <span>المنتجات والمخازن</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= BASE_URL ?>sales.php" class="nav-link <?= ($currentPage === 'sales.php') ? 'active' : '' ?>">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>المبيعات والفواتير</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= BASE_URL ?>purchases.php" class="nav-link <?= ($currentPage === 'purchases.php') ? 'active' : '' ?>">
                <i class="fas fa-truck-loading"></i>
                <span>المشتريات والموردين</span>
            </a>
        </li>

        <li class="nav-header">المالية والحسابات العامة</li>

        <li class="nav-item">
            <a href="<?= BASE_URL ?>accounting.php" class="nav-link <?= ($currentPage === 'accounting.php') ? 'active' : '' ?>">
                <i class="fas fa-balance-scale"></i>
                <span>شجرة الحسابات واليومية</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= BASE_URL ?>reports.php" class="nav-link <?= ($currentPage === 'reports.php') ? 'active' : '' ?>">
                <i class="fas fa-file-excel"></i>
                <span>التقارير التحليلية والضرائب</span>
            </a>
        </li>

        <li class="nav-header">الإدارة والأمان</li>

        <li class="nav-item">
            <a href="<?= BASE_URL ?>settings.php" class="nav-link <?= ($currentPage === 'settings.php') ? 'active' : '' ?>">
                <i class="fas fa-sliders-h"></i>
                <span>إعدادات النظام العامة</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= BASE_URL ?>users.php" class="nav-link <?= ($currentPage === 'users.php') ? 'active' : '' ?>">
                <i class="fas fa-user-shield"></i>
                <span>المستخدمين والصلاحيات</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="font-weight: 800; color: #fff;">المبرمج | Almubarmig</span>
            <span style="color: #38bdf8;">v2.1 Stable</span>
        </div>
        <span style="font-size: 0.72rem; color: #64748b;">من الفكرة إلى نظام يعمل</span>
    </div>
</aside>

