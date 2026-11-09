<?php
/**
 * الترويسة العلوية - Header Component
 */
$user = Auth::user();
?>
<header class="app-header">
    <div class="header-right">
        <button class="header-btn mobile-toggle" id="mobileMenuBtn" aria-label="Toggle Sidebar" style="display: none;">
            <i class="fas fa-bars"></i>
        </button>

        <div style="display: flex; align-items: center; gap: 0.65rem;">
            <span style="font-size: 0.88rem; font-weight: 700; color: var(--text-main);">
                <i class="fas fa-store" style="color: var(--brand-blue); margin-left: 4px;"></i>
                <?= Security::escape($user['branch_name'] ?? 'الفرع الرئيسي') ?>
            </span>
            <span class="badge badge-blue">
                <?= Security::escape($user['role_name'] ?? 'مستخدم') ?>
            </span>
        </div>
    </div>

    <div class="header-left">
        <!-- زر التبديل إلى نقطة البيع السريعة -->
        <a href="<?= BASE_URL ?>pos.php" class="btn btn-warning" style="padding: 0.45rem 1rem; font-size: 0.82rem;">
            <i class="fas fa-bolt"></i>
            <span>شاشة الكاشير (POS)</span>
        </a>

        <!-- جرس التنبيهات -->
        <button class="header-btn" title="التنبيهات الذكية">
            <i class="fas fa-bell"></i>
            <span class="badge-pill" id="alertsCount">0</span>
        </button>

        <!-- زر المظهر الليلي/النهاري -->
        <button class="header-btn" id="themeToggleBtn" title="تبديل المظهر">
            <i class="fas fa-moon"></i>
        </button>

        <!-- قائمة المستخدم وتسجيل الخروج -->
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="text-align: left;">
                <span style="display: block; font-size: 0.86rem; font-weight: 800; color: var(--text-main);">
                    <?= Security::escape($user['full_name'] ?? 'مدير النظام') ?>
                </span>
                <span style="display: block; font-size: 0.72rem; color: var(--text-muted);">
                    @<?= Security::escape($user['username'] ?? 'admin') ?>
                </span>
            </div>
            <a href="<?= BASE_URL ?>logout.php" class="header-btn" title="تسجيل الخروج" style="color: var(--brand-danger);">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</header>

