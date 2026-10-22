<?php
error_reporting(E_ERROR);
// Disable strict exception throwing to match legacy error handling behavior
mysqli_report(MYSQLI_REPORT_OFF);
//date_default_timezone_set('Asia/Riyadh');
date_default_timezone_set('Africa/Cairo');

require_once(__DIR__ . '/config.php');

$MySQL = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME); 
if ($MySQL) {
    mysqli_set_charset($MySQL, 'utf8mb4');
}

$mysqli_Handle = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_UPDATE_NAME);     
if ($mysqli_Handle) {
    mysqli_set_charset($mysqli_Handle, 'utf8mb4');
}


if (!function_exists('updateUrl')) {
    function updateUrl()
    {
        //$updateUrl= 'http://178.238.228.170/wam-pos/';  // online
        $updateUrl= '../wam-pos/';  // local
        return($updateUrl);
    }
}

if (!function_exists('getMacLinux')) {
    function getMacLinux() {
      exec('netstat -ie', $result);
      if(is_array($result)) {
        $iface = array();
        foreach($result as $key => $line) {
          if($key > 0) {
            $tmp = str_replace(" ", "", substr($line, 0, 10));
            if($tmp <> "") {
              $macpos = strpos($line, "HWaddr");
              if($macpos !== false) {
                $iface[] = array('iface' => $tmp, 'mac' => strtolower(substr($line, $macpos+7, 17)));
              }
            }
          }
        }
        return $iface[0]['mac'];
      } else {
        return "notfound";
      }
    }
}
 
if (!function_exists('getMacAddress')) {
    function getMacAddress($iv)
    {
        return $iv;
    }
}

/**
 * Safe prepared statement query executor to prevent SQL Injection
 *
 * @param mysqli $conn MySQLi connection resource
 * @param string $query SQL statement with ? parameter placeholders
 * @param array $params Array of parameters to bind
 * @return mysqli_result|bool Result set or boolean status
 */
if (!function_exists('safe_query')) {
    function safe_query($conn, $query, $params = [])
    {
        if (!$conn instanceof mysqli) {
            return false;
        }

        if (empty($params)) {
            return mysqli_query($conn, $query);
        }

        if (function_exists('mysqli_execute_query')) {
            return mysqli_execute_query($conn, $query, $params);
        }

        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            return false;
        }

        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return ($result !== false) ? $result : true;
    }
}

?>
