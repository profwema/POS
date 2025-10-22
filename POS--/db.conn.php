<?php
error_reporting(E_ERROR);
//date_default_timezone_set('Asia/Riyadh');
date_default_timezone_set('Africa/Cairo');

require_once(__DIR__ . '/config/env.php');

$dbHost = env('DB_HOST', 'localhost');
$dbPort = env('DB_PORT', '3306'); // غير مستخدم مباشرة في mysqli_connect التقليدي
$dbName = env('DB_DATABASE', 'wamtech_db');
$dbUser = env('DB_USERNAME', 'root');
$dbPass = env('DB_PASSWORD', '');

$MySQL = @mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if (!$MySQL) {
  // لا نعرض الخطأ للمستخدم، يمكن تسجيله لاحقًا عبر Monolog مثلاً
  // trigger_error('Database connection error (primary).', E_USER_WARNING);
}
$sSQL = 'SET NAMES utf8';
@mysqli_query($MySQL, $sSQL);

$updHost = env('DB_UPDATE_HOST', $dbHost);
$updName = env('DB_UPDATE_DATABASE', 'wamtech_db_update');
$updUser = env('DB_UPDATE_USERNAME', $dbUser);
$updPass = env('DB_UPDATE_PASSWORD', $dbPass);

$mysqli_Handle = @mysqli_connect($updHost, $updUser, $updPass, $updName);
if (!$mysqli_Handle) {
  // trigger_error('Database connection error (update).', E_USER_WARNING);
}
$sSQL = 'SET NAMES utf8';
@mysqli_query($mysqli_Handle, $sSQL);


function updateUrl()
{

  $updateUrl = env('APP_URL', '../wam-pos/');

  return ($updateUrl);
}

function getMacLinux()
{
  exec('netstat -ie', $result);
  if (is_array($result)) {
    $iface = array();
    foreach ($result as $key => $line) {
      if ($key > 0) {
        $tmp = str_replace(" ", "", substr($line, 0, 10));
        if ($tmp <> "") {
          $macpos = strpos($line, "HWaddr");
          if ($macpos !== false) {
            $iface[] = array('iface' => $tmp, 'mac' => strtolower(substr($line, $macpos + 7, 17)));
          }
        }
      }
    }
    return $iface[0]['mac'];
  } else {
    return "notfound";
  }
}

function getMacAddress($iv)
{

  return $iv;
}
