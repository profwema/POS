<?php
/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * شاشة نقطة البيع السريعة للكاشير - Fast POS
 * 
 * هندسة وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();

$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= Security::csrfToken() ?>">
    <title>شاشة الكاشير السريعة (POS) | <?= APP_NAME ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Fira+Code:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin-pro.css?v=2.1">
    
    <style>
        body { background-color: var(--bg-body); overflow-x: hidden; }
        .pos-header {
            background: linear-gradient(135deg, #070d18 0%, #0b1528 100%);
            color: #fff;
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid rgba(2, 132, 199, 0.4);
        }
    </style>
</head>
<body>

    <!-- POS Header -->
    <header class="pos-header">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <img src="<?= BASE_URL ?>assets/brand/logo-horizontal-light.png" alt="المبرمج | Almubarmig" style="height: 32px; object-fit: contain;">
            <span class="badge badge-amber" style="font-size: 0.72rem;">نقطة البيع السريعة (Fast POS)</span>
        </div>

        <div style="display: flex; align-items: center; gap: 1.25rem;">
            <span><i class="fas fa-store" style="color: var(--brand-blue);"></i> <?= Security::escape($user['branch_name'] ?? 'الفرع الرئيسي') ?></span>
            <span><i class="fas fa-user-circle" style="color: var(--brand-amber);"></i> <?= Security::escape($user['full_name'] ?? 'الكاشير') ?></span>
            
            <?php if (!Auth::isCashier()): ?>
                <a href="<?= BASE_URL ?>index.php" class="btn btn-outline" style="padding: 0.35rem 0.85rem; font-size: 0.78rem; color:#fff; border-color: rgba(255,255,255,0.2);">
                    <i class="fas fa-arrow-left"></i> لوحة التحكم
                </a>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>logout.php" class="btn btn-outline" style="padding: 0.35rem 0.85rem; font-size: 0.78rem; color:#f87171; border-color: rgba(239, 68, 68, 0.3);">
                <i class="fas fa-sign-out-alt"></i> خروج
            </a>
        </div>
    </header>

    <div style="padding: 2.5rem; max-width: 900px; margin: 3rem auto; text-align: center;">
        <div class="card-pro">
            <div class="card-pro-body" style="padding: 3rem 2rem;">
                <div style="width: 70px; height: 70px; background: rgba(2, 132, 199, 0.15); color: var(--brand-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1.5rem auto;">
                    <i class="fas fa-barcode"></i>
                </div>
                <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.75rem;">
                    جاهزية محرك نقطة البيع السريعة (POS Ready)
                </h2>
                <p style="color: var(--text-muted); font-size: 1rem; max-width: 600px; margin: 0 auto 1.75rem auto;">
                    النواة التأسيسية تم إنشاؤها بنجاح (المرحلة 0). سنقوم في الخطوة القادمة ببناء <strong>موديول المخازن والأصناف</strong>، لترتبط شاشة الكاشير فوراً بالأصناف وباركود الموازين الإلكترونية.
                </p>
                <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                    <?php if (!Auth::isCashier()): ?>
                        <a href="<?= BASE_URL ?>index.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> العودة للوحة التحكم
                        </a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>logout.php" class="btn btn-outline">
                        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

