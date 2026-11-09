<?php
/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * ملف الإعدادات العامة للنظام - General Configuration
 * 
 * هندسة وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
 * الشعار: من الفكرة إلى نظام يعمل
 * الموقع: https://almubarmig.com
 */

// بدء الجلسة في بيئة الويب فقط
if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) {
    // إعدادات جلسة آمنة
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// ضبط المنطقة الزمنية لجمهورية مصر العربية (توقيت القاهرة)
date_default_timezone_set('Africa/Cairo');

// بيئة التشغيل: development أو production
define('APP_ENV', 'development');

if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// المسارات الأساسية للنظام (Base Paths)
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app');
define('CORE_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'core');
define('VIEWS_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'views');
define('STORAGE_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'storage');
define('ASSETS_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'assets');

// الرابط الأساسي للمشروع على السيرفر المحلي WAMP
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = rtrim($protocol . $host . $scriptDir, '/') . '/';
// تثبيت الرابط الأساسي
define('BASE_URL', (basename(ROOT_PATH) === basename($scriptDir)) ? $baseUrl : 'http://localhost/myworks/POS/');

// بيانات هوية المنظومة والبراند (Brand & Identity)
define('APP_NAME', 'WAM Tech POS & ERP');
define('APP_VERSION', 'v2.1.0 Enterprise');
define('APP_EDITION', 'Egypt Market Edition');
define('BRAND_NAME', 'المبرمج | Almubarmig');
define('BRAND_FOUNDER', 'م/ وئام محمد السوداني');
define('BRAND_SLOGAN', 'من الفكرة إلى نظام يعمل');
define('BRAND_PHONE', '+201002283766');
define('BRAND_WEBSITE', 'https://almubarmig.com');

// ثوابت التجارة والسوق المصري (Egypt Commercial Standards)
define('APP_CURRENCY', 'ج.م');
define('APP_CURRENCY_CODE', 'EGP');
define('DEFAULT_VAT_PERCENT', 14.0); // ضريبة القيمة المضافة المصرية 14%
define('CURRENCY_DECIMALS', 2); // قروش

// مفتاح التشفير العام لعمليات OpenSSL AES-256-CBC
define('APP_KEY', 'wamtech_secret_key_al_mubarmig_2026_enterprise_pos');
define('APP_CIPHER', 'AES-256-CBC');

// التضمين التلقائي للنواة (Autoload Core Classes)
require_once CORE_PATH . DIRECTORY_SEPARATOR . 'Database.php';
require_once CORE_PATH . DIRECTORY_SEPARATOR . 'Security.php';
require_once CORE_PATH . DIRECTORY_SEPARATOR . 'Auth.php';
require_once CORE_PATH . DIRECTORY_SEPARATOR . 'Helpers.php';
require_once CORE_PATH . DIRECTORY_SEPARATOR . 'Logger.php';
require_once CORE_PATH . DIRECTORY_SEPARATOR . 'View.php';

// تحميل تلقائي لموديلات وخدمات التطبيق (Models & Services Autoloader)
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $class . '.php',
        APP_PATH . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . $class . '.php',
        CORE_PATH . DIRECTORY_SEPARATOR . $class . '.php'
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
