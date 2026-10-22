<?php
error_reporting(E_ERROR);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['lang'] = "ar";

if (!defined("LANG")) {
	define("LANG", "ar");
}
if(!isset($_SESSION['key']))
{
	$time			=md5(md5(time()));
	$_SESSION['key']	="#$^&*@**#".$time."@$#@@".$time."#$^&*@**#&$%_)@()*@()@@#*$()##$$@$#@@&(((*R((#)#)))$(#)";
	$_SESSION['key']	=substr($_SESSION['key'],0,32);
}


if(!isset($_SESSION['key_constant']))
{
	$_SESSION['key_constant']	="#$^&*@**#&$%_)@()*@()@@#*$()##$$@$#@@&(((*R((#)#)))$(#)(@3490ddhdhi339ujfjkdjdldsfjkfdojdkdffgfd";
	$_SESSION['key_constant']	=substr($_SESSION['key_constant'],0,32);
}


$store_type_    = $_SESSION['type_of_store'] ?? '';

require_once("translate.php");
require_once("enc.php");
require_once("email.php");
require_once("Encryption.php");
require_once("resize-class.php");
require_once(__DIR__ . "/auth.php");
if (isset($_SESSION['userlogged'])) {
    auth_init_session();
}
if (!defined("SALOON")) {
	define("SALOON","2");
}

function flout_format($var)
{
    $varSt = number_format((float)($var ?? 0), 2, '.', ',');
    return $varSt;
}

// ═══════════════════════════════════════════════════════════════
// CSRF Protection Helpers
// ═══════════════════════════════════════════════════════════════
if (empty($_SESSION['csrf_token'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token() {
        return $_SESSION['csrf_token'] ?? '';
    }
}

if (!function_exists('csrf_input')) {
    function csrf_input() {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('verify_csrf_token')) {
    function verify_csrf_token($token = null) {
        if ($token === null) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_REQUEST['csrf_token'] ?? '';
        }
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], (string)$token);
    }
}
        
?>