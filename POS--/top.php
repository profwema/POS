<?php
error_reporting(E_ERROR);
require_once(__DIR__ . '/config/env.php');
// التحكم في عرض الأخطاء وفق APP_DEBUG
$__app_debug = env('APP_DEBUG', 'true');
ini_set('display_errors', strtolower($__app_debug) === 'true' ? '1' : '0');

// إعدادات الكوكيز للجلسة قبل البدء بها لتعزيز الأمان
$__secure   = strtolower(env('SESSION_SECURE', 'false')) === 'true';
$__httpOnly = strtolower(env('SESSION_HTTP_ONLY', 'true')) === 'true';
$__sameSite = env('SESSION_SAME_SITE', 'Lax');
if (PHP_VERSION_ID >= 70300) {
	session_set_cookie_params([
		'lifetime' => 0,
		'path'     => '/',
		'domain'   => '',
		'secure'   => $__secure,
		'httponly' => $__httpOnly,
		'samesite' => $__sameSite
	]);
} else {
	$p = session_get_cookie_params();
	session_set_cookie_params(0, $p['path'] . '; samesite=' . $__sameSite, $p['domain'], $__secure, $__httpOnly);
}
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
require_once(__DIR__ . '/security/csrf.php');
require_once(__DIR__ . '/security/auth.php');

if (!isset($_SESSION['lang']))
	$_SESSION['lang'] = "ar";

define("LANG", $_SESSION['lang']);
if (!isset($_SESSION['key'])) {
	$time			= md5(md5(time()));
	$_SESSION['key']	= "#$^&*@**#" . $time . "@$#@@" . $time . "#$^&*@**#&$%_)@()*@()@@#*$()##$$@$#@@&(((*R((#)#)))$(#)";
	$_SESSION['key']	= substr($_SESSION['key'], 0, 32);
}


if (!isset($_SESSION['key_constant'])) {
	$_SESSION['key_constant']	= "#$^&*@**#&$%_)@()*@()@@#*$()##$$@$#@@&(((*R((#)#)))$(#)(@3490ddhdhi339ujfjkdjdldsfjkfdojdkdffgfd";
	$_SESSION['key_constant']	= substr($_SESSION['key_constant'], 0, 32);
}


$store_type_    = $_SESSION['type_of_store'];

require_once("translate.php");
require_once("enc.php");
require_once("email.php");
require_once("Encryption.php");
require_once("resize-class.php");
define("SALOON", "2");

// تجديد معرف الجلسة دوريًا لزيادة الأمان
if (!isset($_SESSION['__regenerated'])) {
	session_regenerate_id(true);
	$_SESSION['__regenerated'] = time();
} elseif (time() - (int)$_SESSION['__regenerated'] > 900) { // 15 دقيقة
	session_regenerate_id(true);
	$_SESSION['__regenerated'] = time();
}

// مهلة خمول الجلسة (تلقائيًا خروج بعد فترة عدم نشاط)
$__idleTtl = (int)env('SESSION_IDLE_TTL', '1800'); // 30 دقيقة افتراضيًا
if ($__idleTtl > 0) {
	$now = time();
	if (isset($_SESSION['__last_activity']) && ($now - (int)$_SESSION['__last_activity'] > $__idleTtl)) {
		session_unset();
		session_destroy();
		session_start();
	}
	$_SESSION['__last_activity'] = $now;
}

function flout_format($var)
{
	$varSt = number_format($var, 2, '.', ',');
	return $varSt;
}
