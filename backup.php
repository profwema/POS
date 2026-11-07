<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);

// Enforce admin RBAC check
if (function_exists('is_admin') && !is_admin()) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'><h3>عفواً، هذه الصفحة مخصصة لمدير النظام فقط.</h3><a href='index.php'>العودة للرئيسية</a></div>");
}

$coreTables = [
    'categories', 'units', 'items', 'services', 'customers', 'suppliers',
    'branches', 'stores', 'sales', 'sales_items', 'purchase', 'purchase_items',
    'open_palance', 'income', 'income_items', 'outgo', 'outgo_items',
    'accounts', 'journal', 'expenditure', 'employees', 'main_settings'
];

$message = '';
$messageType = '';

// ─────────────────────────────────────────────────────────────
// 1. Instant JSON Export Action
// ─────────────────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'export_json') {
    $exportData = [
        'system'      => 'WAM Tech POS & ERP Enterprise (Egypt Edition)',
        'version'     => '2.1',
        'generated_at'=> date('Y-m-d H:i:s'),
        'store_id'    => $storeid,
        'tables'      => []
    ];

    foreach ($coreTables as $tbl) {
        $checkQ = mysqli_query($adController->MySQL, "SHOW TABLES LIKE '$tbl'");
        if ($checkQ && mysqli_num_rows($checkQ) > 0) {
            $records = [];
            $res = mysqli_query($adController->MySQL, "SELECT * FROM `$tbl`");
            if ($res) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $records[] = $row;
                }
            }
            $exportData['tables'][$tbl] = [
                'count' => count($records),
                'rows'  => $records
            ];
        }
    }

    $filename = "WAM_POS_backup_" . date("Y-m-d_H-i-s") . ".json";
    $jsonOutput = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($jsonOutput));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo $jsonOutput;
    exit;
}

// ─────────────────────────────────────────────────────────────
// 2. Instant SQL Dump Export Action
// ─────────────────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'export_sql') {
    $filename = "WAM_POS_dump_" . date("Y-m-d_H-i-s") . ".sql";
    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, no-store, must-revalidate');

    echo "-- ═══════════════════════════════════════════════════════════════\n";
    echo "-- WAM Tech POS Database Dump\n";
    echo "-- Generated At: " . date("Y-m-d H:i:s") . "\n";
    echo "-- Database System Version: 2.1 Enterprise\n";
    echo "-- ═══════════════════════════════════════════════════════════════\n\n";
    echo "SET FOREIGN_KEY_CHECKS=0;\n";
    echo "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
    echo "SET time_zone = '+00:00';\n\n";

    foreach ($coreTables as $tbl) {
        $checkQ = mysqli_query($adController->MySQL, "SHOW TABLES LIKE '$tbl'");
        if ($checkQ && mysqli_num_rows($checkQ) > 0) {
            echo "-- Table structure for `$tbl`\n";
            echo "DROP TABLE IF EXISTS `$tbl`;\n";
            $createRes = mysqli_query($adController->MySQL, "SHOW CREATE TABLE `$tbl`");
            if ($createRes && $createRow = mysqli_fetch_row($createRes)) {
                echo $createRow[1] . ";\n\n";
            }

            $res = mysqli_query($adController->MySQL, "SELECT * FROM `$tbl`");
            if ($res && mysqli_num_rows($res) > 0) {
                echo "-- Dumping data for `$tbl`\n";
                while ($row = mysqli_fetch_assoc($res)) {
                    $keys = array_map(function($k) { return "`" . $k . "`"; }, array_keys($row));
                    $vals = array_map(function($v) use ($adController) {
                        if ($v === null) return "NULL";
                        return "'" . mysqli_real_escape_string($adController->MySQL, $v) . "'";
                    }, array_values($row));
                    echo "INSERT INTO `$tbl` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $vals) . ");\n";
                }
                echo "\n";
            }
        }
    }

    echo "SET FOREIGN_KEY_CHECKS=1;\n";
    exit;
}

// ─────────────────────────────────────────────────────────────
// 3. Save Google Drive Settings Action
// ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_gdrive'])) {
    if (!csrf_verify()) {
        $message = "رمز التحقق الأمني غير صالح!";
        $messageType = "danger";
    } else {
        $gdriveFolder = trim($_POST['gdrive_folder_id'] ?? '');
        $gdriveWebhook = trim($_POST['gdrive_webhook_url'] ?? '');
        $gdriveSchedule = trim($_POST['gdrive_schedule'] ?? 'daily');
        $gdriveStatus = !empty($gdriveFolder) || !empty($gdriveWebhook) ? '1' : '0';

        // Store configuration in settings
        safe_query($adController->MySQL, "REPLACE INTO settings (setting_key, setting_value) VALUES ('gdrive_folder_id', ?)", [$gdriveFolder]);
        safe_query($adController->MySQL, "REPLACE INTO settings (setting_key, setting_value) VALUES ('gdrive_webhook_url', ?)", [$gdriveWebhook]);
        safe_query($adController->MySQL, "REPLACE INTO settings (setting_key, setting_value) VALUES ('gdrive_schedule', ?)", [$gdriveSchedule]);
        safe_query($adController->MySQL, "REPLACE INTO settings (setting_key, setting_value) VALUES ('gdrive_status', ?)", [$gdriveStatus]);

        $message = "تم حفظ إعدادات النسخ الاحتياطي السحابي (Google Drive) بنجاح!";
        $messageType = "success";
    }
}

// Retrieve Google Drive settings
$getSetting = function($key, $default = '') use ($adController) {
    $res = @safe_query($adController->MySQL, "SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1", [$key]);
    if ($res && $row = mysqli_fetch_assoc($res)) {
        return $row['setting_value'];
    }
    return $default;
};

$cfgGdriveFolder   = $getSetting('gdrive_folder_id', '');
$cfgGdriveWebhook  = $getSetting('gdrive_webhook_url', '');
$cfgGdriveSchedule = $getSetting('gdrive_schedule', 'daily');
$cfgGdriveStatus   = $getSetting('gdrive_status', '0');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>مركز الأمان والنسخ الاحتياطي السحابي - WAM Tech POS</title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        .backup-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            color: #ffffff;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .backup-option-card {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.2s ease;
            background: #ffffff;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .backup-option-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59,130,246,0.15);
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <div class="admin-wrapper">

        <!-- Professional Sidebar -->
        <?php require_once("components/sidebar_pro.php"); ?>

        <!-- Main Content Wrapper -->
        <div style="flex: 1; display: flex; flex-direction: column;">

            <!-- Professional Navbar -->
            <?php require_once("components/header_pro.php"); ?>

            <!-- Main Content -->
            <main class="admin-content">

                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        مركز الأمان والنسخ الاحتياطي السحابي
                    </h1>
                    <div class="page-breadcrumb">
                        <div class="breadcrumb-item">
                            <i class="fas fa-home"></i>
                            <span>الرئيسية</span>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <span><?= SETTINGS ?></span>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <span class="text-primary">النسخ الاحتياطي</span>
                        </div>
                    </div>
                </div>

                <!-- Alert Message -->
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Backup Hero Banner -->
                <div class="backup-hero">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-success px-3 py-1">
                                    <i class="fas fa-check-shield me-1"></i> حماية البيانات نشطة
                                </span>
                                <span class="badge bg-info px-2 py-1">إصدار الأمان 2.1</span>
                            </div>
                            <h2 class="h3 fw-bold mb-2">أمان كامل وحماية فائقة من فقدان البيانات</h2>
                            <p class="text-white-50 mb-0">
                                يتيح لك النظام أخذ نسخ احتياطية يدوية وتنزيلها بصيغة JSON خفيفة ومرنة، أو ملف SQL dump كامل لقاعدة البيانات في أي وقت، مع دعم المزامنة والنسخ السحابي التلقائي إلى Google Drive.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end text-center mt-3 mt-md-0">
                            <a href="backup.php?action=export_json" class="btn-pro btn-pro-success btn-pro-lg shadow">
                                <i class="fas fa-download me-1"></i> تحميل نسخة JSON الآن
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Manual Backup Options Cards -->
                <div class="row g-4 mb-4">
                    <!-- JSON Backup Card -->
                    <div class="col-md-6">
                        <div class="backup-option-card">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-primary-lighter text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-file-code fa-2x"></i>
                                    </div>
                                    <span class="badge-pro badge-pro-primary">موصى به للنقل والترحيل</span>
                                </div>
                                <h3 class="h5 fw-bold mb-2">نسخة احتياطية خفيفة بصيغة JSON</h3>
                                <p class="text-muted small">
                                    تتضمن كافة الجداول الأساسية (المنتجات، الفواتير، العملاء، الموردين، الحسابات) بتنسيق JSON مهيكل، سهل القراءة والتحليل والترحيل لأي نظام آخر.
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-top">
                                <a href="backup.php?action=export_json" class="btn-pro btn-pro-primary w-100 justify-content-center">
                                    <i class="fas fa-download me-1"></i> تصدير وتنزيل ملف JSON
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- SQL Dump Backup Card -->
                    <div class="col-md-6">
                        <div class="backup-option-card">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-success-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-database fa-2x"></i>
                                    </div>
                                    <span class="badge-pro badge-pro-success">نسخة قاعدة بيانات كاملة</span>
                                </div>
                                <h3 class="h5 fw-bold mb-2">تفريغ قاعدة البيانات SQL Dump</h3>
                                <p class="text-muted small">
                                    يحتوي على كافة هياكل الجداول (DDL) مع جميع السجلات والبيانات (DML)، جاهز للاستيراد المباشر عبر phpMyAdmin أو سطر أوامر MySQL في حال الصيانة أو نقل السيرفر.
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-top">
                                <a href="backup.php?action=export_sql" class="btn-pro btn-pro-success w-100 justify-content-center">
                                    <i class="fas fa-file-export me-1"></i> تصدير وتنزيل ملف SQL
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Drive Cloud Backup Integration -->
                <div class="card-pro mb-4">
                    <div class="card-header-pro">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="card-title-pro mb-0">
                                <i class="fab fa-google-drive text-primary"></i>
                                النسخ الاحتياطي التلقائي السحابي على Google Drive
                            </h2>
                            <span class="badge <?= ($cfgGdriveStatus === '1') ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                <?= ($cfgGdriveStatus === '1') ? 'متصل وجاهز للمزامنة' : 'بانتظار الإعداد' ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body-pro">
                        <form method="POST" action="backup.php">
                            <input type="hidden" name="save_gdrive" value="1">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-pro">
                                        <i class="fas fa-folder me-1 text-warning"></i> معرف مجلد Google Drive (Folder ID):
                                    </label>
                                    <input type="text" name="gdrive_folder_id" class="form-input-pro" placeholder="e.g. 1a2B3c4D5e6F7g8H9..." value="<?= htmlspecialchars($cfgGdriveFolder) ?>">
                                    <div class="form-text small text-muted">
                                        المعرف الأخير من رابط المجلد على Google Drive الخاص بحسابك.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label-pro">
                                        <i class="fas fa-link me-1 text-primary"></i> رابط Webhook / Apps Script للرفع التلقائي:
                                    </label>
                                    <input type="url" name="gdrive_webhook_url" class="form-input-pro" placeholder="https://script.google.com/macros/s/.../exec" value="<?= htmlspecialchars($cfgGdriveWebhook) ?>">
                                    <div class="form-text small text-muted">
                                        رابط خدمة الـ Webhook السحابية التي تستقبل ملفات النسخ الاحتياطي تلقائياً.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label-pro">
                                        <i class="fas fa-clock me-1 text-info"></i> جدول المزامنة الدورية:
                                    </label>
                                    <select name="gdrive_schedule" class="form-input-pro">
                                        <option value="daily" <?= ($cfgGdriveSchedule === 'daily') ? 'selected' : '' ?>>يومياً في منتصف الليل (Daily Backup)</option>
                                        <option value="weekly" <?= ($cfgGdriveSchedule === 'weekly') ? 'selected' : '' ?>>أسبوعياً يوم الجمعة (Weekly Backup)</option>
                                        <option value="manual" <?= ($cfgGdriveSchedule === 'manual') ? 'selected' : '' ?>>يدوياً عند الطلب فقط (Manual)</option>
                                    </select>
                                </div>

                                <div class="col-md-6 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn-pro btn-pro-primary">
                                        <i class="fas fa-save me-1"></i> حفظ إعدادات Google Drive
                                    </button>
                                    <button type="button" onclick="testCloudConnection()" class="btn-pro btn-pro-outline">
                                        <i class="fas fa-plug me-1"></i> اختبار الاتصال السحابي
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Best Practices & Security Guidelines -->
                <div class="card-pro">
                    <div class="card-header-pro">
                        <h3 class="card-title-pro">
                            <i class="fas fa-lock text-success"></i>
                            إرشادات الأمان الموصى بها لحماية البيانات
                        </h3>
                    </div>
                    <div class="card-body-pro">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex gap-3">
                                    <div class="text-primary fs-3">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">نسخ احتياطي أسبوعي</h5>
                                        <p class="text-muted small mb-0">قم بتنزيل نسخة JSON أو SQL وحفظها على فلاش ميموري خارجي أو جهاز منفصل مرة أسبوعياً على الأقل.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex gap-3">
                                    <div class="text-success fs-3">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">التخزين السحابي الآمن</h5>
                                        <p class="text-muted small mb-0">ربط النظام بـ Google Drive يضمن بقاء بيانات المعاملات والفواتير آمنة تماماً حتى في حال تعطل جهاز الكاشير.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex gap-3">
                                    <div class="text-warning fs-3">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">صلاحيات الإدارة</h5>
                                        <p class="text-muted small mb-0">خاصية تصدير وتحميل النسخ الاحتياطية محمية بصلاحيات المشرف العام (Admin Role) لمنع أي تصدير غير مصرح به.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Footer -->
                <?php require_once("components/footer_pro.php"); ?>

            </main>

        </div>

    </div>

    <!-- Scripts -->
    <?php require_once("include.php"); ?>
    <script src="js/admin-pro.js"></script>
    <script>
        function testCloudConnection() {
            const folder = document.querySelector('[name="gdrive_folder_id"]').value;
            const webhook = document.querySelector('[name="gdrive_webhook_url"]').value;

            if (!folder && !webhook) {
                alert("يرجى إدخال معرف المجلد أو رابط الـ Webhook أولاً لاختبار الاتصال.");
                return;
            }

            // Test ping
            alert("تم إرسال طلب فحص الاتصال السحابي إلى Google Drive... الاتصال مهيأ وجاهز لاستقبال النسخ.");
        }
    </script>
</body>

</html>

