<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= Security::csrfToken() ?>">
    <title><?= Security::escape($pageTitle ?? 'لوحة التحكم') ?> | <?= APP_NAME ?></title>
    
    <!-- Google Fonts: Cairo, Plus Jakarta Sans, Fira Code -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Admin Pro Main Stylesheet (Dynamic Versioning to prevent browser cache) -->
    <?php
    $cssFile = ROOT_PATH . '/assets/css/admin-pro.css';
    $cssVer = file_exists($cssFile) ? filemtime($cssFile) : time();
    $jsFile = ROOT_PATH . '/assets/js/app.js';
    $jsVer = file_exists($jsFile) ? filemtime($jsFile) : time();
    ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin-pro.css?v=<?= $cssVer ?>">
    
    <style>
    /* Critical Structural Rules: Guaranteed Modals & Grids */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
        z-index: 1200;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
    }
    .stats-grid {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)) !important;
        gap: 1.25rem !important;
        margin-bottom: 1.75rem;
    }
    </style>
</head>
<body>

    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <?php View::component('sidebar'); ?>

        <!-- Main Body -->
        <div class="app-main">
            <!-- Header Navbar -->
            <?php View::component('header'); ?>

            <!-- Page Content Injection -->
            <main class="app-content">
                <?= $content ?? '' ?>
            </main>

            <!-- Footer -->
            <?php View::component('footer'); ?>
        </div>
    </div>

    <!-- Client Scripts -->
    <script src="<?= BASE_URL ?>assets/js/app.js?v=<?= $jsVer ?>"></script>
</body>
</html>

