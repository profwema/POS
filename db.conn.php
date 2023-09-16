<?php
error_reporting(E_ERROR);
//date_default_timezone_set('Asia/Riyadh');
date_default_timezone_set('Africa/Cairo');

    //$MySQL = mysqli_connect("localhost","root","",'wamtech_db') ; 

    $MySQL = mysqli_connect("localhost","root","",'wamtech_db') ; 
    if(!$MySQL)
    {
	//echo 'error';
	//$MySQL = mysqli_connect("178.238.228.170","root","R1HeK0D8a7R6CIqo",'wamtech_db') ; 
    }
    $sSQL= 'SET NAMES utf8'; 
    mysqli_query($MySQL,$sSQL) ;

    $mysqli_Handle = mysqli_connect("localhost","root","",'wamtech_db_update') ;     

    if(!$mysqli_Handle)
    {
	//echo 'error';
	$mysqli_Handle = mysqli_connect("178.238.228.170","root","R1HeK0D8a7R6CIqo",'wamtech_db_update') ; 
    }
    $sSQL= 'SET NAMES utf8'; 
    mysqli_query($mysqli_Handle,$sSQL) ;


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
