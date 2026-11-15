<?php
/**
 * متحكم إعدادات النظام ومركز النسخ الاحتياطي - System Settings & Backup Controller
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();
Auth::requirePermission('settings.manage');

$db = Database::getInstance();
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$tab = $_GET['tab'] ?? 'general'; // 'general', 'tax', 'pos', 'backup'
$msg = $_GET['msg'] ?? '';

// 1. إنشاء نسخة احتياطية جديدة لقاعدة البيانات
if ($action === 'backup_create') {
    try {
        $res = BackupService::createBackup();
        header('Location: ' . BASE_URL . 'settings.php?tab=backup&msg=backup_created');
        exit;
    } catch (Throwable $e) {
        die("خطأ أثناء إنشاء النسخة الاحتياطية: " . htmlspecialchars($e->getMessage()));
    }
}

// 2. تنزيل نسخة احتياطية محددة
if ($action === 'backup_download') {
    $file = $_GET['file'] ?? '';
    if (!empty($file)) {
        BackupService::downloadBackup($file);
    }
    exit;
}

// 3. حذف ملف نسخة احتياطية
if ($action === 'backup_delete') {
    $file = $_GET['file'] ?? '';
    if (!empty($file)) {
        BackupService::deleteBackup($file);
        header('Location: ' . BASE_URL . 'settings.php?tab=backup&msg=backup_deleted');
        exit;
    }
}

// 4. حفظ وتحديث الإعدادات (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save_settings') {
    Security::verifyCsrf();

    $postedSettings = $_POST['settings'] ?? [];
    $group = $_POST['group'] ?? 'general';

    try {
        foreach ($postedSettings as $key => $value) {
            $key = trim($key);
            $val = is_string($value) ? trim($value) : (string)$value;

            $exists = $db->fetchOne("SELECT id FROM system_settings WHERE `key` = ?", [$key]);
            if ($exists) {
                $db->update('system_settings', ['value' => $val], '`key` = ?', [$key]);
            } else {
                $db->insert('system_settings', [
                    'key'        => $key,
                    'value'      => $val,
                    'group_name' => $group
                ]);
            }
        }

        Logger::audit('settings_updated', 'settings', null, null, [
            'group'    => $group,
            'settings' => $postedSettings
        ]);

        header('Location: ' . BASE_URL . 'settings.php?tab=' . urlencode($group) . '&msg=settings_saved');
        exit;
    } catch (Throwable $e) {
        die("خطأ أثناء حفظ الإعدادات: " . htmlspecialchars($e->getMessage()));
    }
}

// 5. جلب كافة الإعدادات الحالية من قاعدة البيانات
$settingsRows = $db->fetchAll("SELECT * FROM system_settings ORDER BY id ASC");
$settings = [];
foreach ($settingsRows as $row) {
    $settings[$row['key']] = $row['value'];
}

// 6. جلب قائمة ملفات النسخ الاحتياطي المخزنة
$backups = BackupService::getBackupsList();

// 7. عرض شاشة الإعدادات
View::render('modules/settings/index', [
    'pageTitle' => 'إعدادات النظام العامة ومركز النسخ الاحتياطي',
    'settings'  => $settings,
    'backups'   => $backups,
    'tab'       => $tab,
    'msg'       => $msg
]);
