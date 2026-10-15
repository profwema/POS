<?php
error_reporting(E_ERROR);
// Disable strict exception throwing to match legacy error handling behavior
mysqli_report(MYSQLI_REPORT_OFF);
//date_default_timezone_set('Asia/Riyadh');
date_default_timezone_set('Africa/Cairo');

    $MySQL = mysqli_connect("localhost","root","",'wamtech_db'); 
    if(!$MySQL)
    {
	//echo 'error';
	//$MySQL = mysqli_connect("178.238.228.170","root","R1HeK0D8a7R6CIqo",'wamtech_db'); 
    }
    if($MySQL) {
        mysqli_set_charset($MySQL, 'utf8mb4');
    }

    $mysqli_Handle = mysqli_connect("localhost","root","",'wamtech_db_update');     

    if(!$mysqli_Handle)
    {
	//echo 'error';
	$mysqli_Handle = mysqli_connect("178.238.228.170","root","R1HeK0D8a7R6CIqo",'wamtech_db_update'); 
    }
    if($mysqli_Handle) {
        mysqli_set_charset($mysqli_Handle, 'utf8mb4');
    }


function updateUrl()
{
	
    //$updateUrl= 'http://178.238.228.170/wam-pos/';  // online
    $updateUrl= '../wam-pos/';  // local

    return($updateUrl);
}

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
 
function getMacAddress($iv)
{
	
    return $iv;
}

?>
