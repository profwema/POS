<?php
/**
 * لوحة التحكم الرئيسية - Dashboard View
 */
?>

<!-- Welcome Banner -->
<div style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.12) 0%, rgba(245, 158, 11, 0.1) 100%); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.75rem 2rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
            <span class="badge badge-amber">المرحلة 0: النواة جاهزة ومفعلة</span>
            <span class="badge badge-emerald">MySQL 8 • InnoDB Active</span>
        </div>
        <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
            مرحباً بك، <?= Security::escape($currentUser['full_name'] ?? 'مدير النظام') ?> 👋
        </h2>
        <p style="color: var(--text-muted); margin-bottom: 0; font-size: 0.92rem;">
            النواة المعمارية النظيفة (Core Foundation) تعمل بكفاءة متناهية تحت مظلة وهوية براند <strong>«المبرمج | Almubarmig»</strong>.
        </p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>pos.php" class="btn btn-warning" style="padding: 0.75rem 1.4rem; font-size: 0.95rem;">
            <i class="fas fa-bolt"></i>
            <span>فتح شاشة نقطة البيع (POS)</span>
        </a>
    </div>
</div>

<!-- Stat Cards Row -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-users-cog"></i>
        </div>
        <div>
            <div class="stat-val"><?= (int)($totalUsers ?? 2) ?></div>
            <div class="stat-title">المستخدمين والموظفين</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">
            <i class="fas fa-sliders-h"></i>
        </div>
        <div>
            <div class="stat-val"><?= (int)($totalSettings ?? 13) ?></div>
            <div class="stat-title">إعدادات المنشأة المهيأة</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon emerald">
            <i class="fas fa-store"></i>
        </div>
        <div>
            <div class="stat-val">1</div>
            <div class="stat-title">الفروع النشطة</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div>
            <div class="stat-val">Bcrypt</div>
            <div class="stat-title">حماية وتشفير الجلسات</div>
        </div>
    </div>
</div>

<!-- Overview Card -->
<div class="card-pro">
    <div class="card-pro-header">
        <h3>
            <i class="fas fa-tasks" style="color: var(--brand-blue);"></i>
            <span>حالة الإنجاز المعماري - نظام WAM Tech POS & ERP</span>
        </h3>
        <span class="badge badge-blue">Foundation 100%</span>
    </div>
    <div class="card-pro-body">
        <div class="alert-box success" style="margin-top: 0;">
            <i class="fas fa-check-circle"></i>
            <div>
                <strong>اكتملت المرحلة 0 بنجاح فائق:</strong>
                تم تأسيس قاعدة بيانات <code>wamtech_pos_db</code> بمحرك <code>InnoDB</code> وجداول الفروع، المستخدمين، الأدوار، الصلاحيات، وإعدادات المنشأة وسجل التدقيق الداخلي، مع فئة الاتصال الآمن والمعاملات الذرية ونظام التصميم <code>admin-pro</code>.
            </div>
        </div>

        <h4 style="font-size: 1rem; font-weight: 800; margin: 1.5rem 0 1rem 0; color: var(--text-main);">
            الخطوات القادمة في خارطة الطريق الهندسية:
        </h4>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div style="padding: 1.25rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-body);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                    <strong style="color: var(--brand-blue);"><i class="fas fa-boxes"></i> المرحلة 1: موديول المخازن والأصناف</strong>
                    <span class="badge badge-amber">التالي مباشرة</span>
                </div>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin-bottom: 0;">
                    بناء جداول التصنيفات، الوحدات، المنتجات، باركود الموازين، وبطاقة الصنف التفصيلية، وجدول حركات المخزون <code>stock_moves</code>.
                </p>
            </div>

            <div style="padding: 1.25rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-body);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                    <strong style="color: var(--brand-emerald);"><i class="fas fa-bolt"></i> المرحلة 2: محرك نقطة البيع السريعة POS</strong>
                    <span class="badge badge-blue">مخطط</span>
                </div>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin-bottom: 0;">
                    شاشة الكاشير السريعة عبر <code>api.php</code>، اختصارات F1-F10، قارئ كاميرا الموبايل، والمدفوعات المصرية (ميزة، إنستاباي، كاش).
                </p>
            </div>
        </div>
    </div>
</div>

