<?php
/**
 * سكربت الفحص والتحقق الآلي للمرحلة الخامسة (Phase 5 Verification)
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 */

require_once __DIR__ . '/../config/config.php';

echo "=== بدء فحص مكونات المرحلة الخامسة (Phase 5 Verification) ===\n\n";

// 1. فحص توفر الكلاسات والخدمات
$classes = ['ReportService', 'BackupService', 'User', 'Auth', 'Helpers', 'Logger', 'Security'];
foreach ($classes as $cls) {
    if (class_exists($cls)) {
        echo "[OK] الكلاس {$cls} موجود وتم تحميله بنجاح.\n";
    } else {
        echo "[FAIL] فشل تحميل الكلاس {$cls}!\n";
    }
}

// 2. فحص مجلد النسخ الاحتياطي والصلاحيات
$backupDir = BackupService::getBackupDir();
if (is_dir($backupDir)) {
    echo "[OK] مجلد النسخ الاحتياطي موجود: {$backupDir}\n";
} else {
    echo "[FAIL] تعذر إنشاء أو الوصول لمجلد النسخ الاحتياطي!\n";
}

// 3. فحص ملفات العرض (Views)
$views = [
    'views/modules/reports/reports_index.php',
    'views/modules/reports/vat_declaration.php',
    'views/modules/reports/item_profitability.php',
    'views/modules/reports/inventory_movement.php',
    'views/modules/reports/customer_statement.php',
    'views/modules/settings/index.php',
    'views/modules/users/users_list.php'
];

foreach ($views as $view) {
    $fullPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $view);
    if (file_exists($fullPath)) {
        echo "[OK] ملف العرض موجود: {$view}\n";
    } else {
        echo "[FAIL] ملف العرض مفقود: {$view}\n";
    }
}

// 4. فحص ملفات المتحكمات (Controllers)
$controllers = [
    'reports.php',
    'settings.php',
    'users.php'
];

foreach ($controllers as $ctrl) {
    $fullPath = ROOT_PATH . DIRECTORY_SEPARATOR . $ctrl;
    if (file_exists($fullPath)) {
        echo "[OK] ملف المتحكم موجود: {$ctrl}\n";
    } else {
        echo "[FAIL] ملف المتحكم مفقود: {$ctrl}\n";
    }
}

echo "\n=== انتهى فحص ملفات المرحلة الخامسة بنجاح ===\n";
