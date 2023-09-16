<?php
error_reporting(E_ERROR);
require_once("db.conn.mah.php");
require_once("translate.php");

$query 	  = "SELECT * FROM dataversion";
$res	  = mysqli_query($adController->MySQL,$query);
$data	  = mysqli_fetch_assoc($res);

$u 	= HOSTED_URL."file_version.php?sid=".$data[storeid];
$dataSer= file_get_contents($u);

$json	= json_decode($dataSer,true);
$str	= "";
if(intval($json['dbversion']) > intval($data['dbversion']))
{
	$str .= "New data available and it is must you download it as soon as possible\n";
}

if(intval($json['designversion']) > intval($data['designversion']))
{
	$str .= "Crucial update available\n";
}

echo $str;

?>