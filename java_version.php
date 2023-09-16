<?php
set_time_limit(0);
error_reporting(E_ERROR);
require_once("db.conn.mah.php");
require_once("translate.php");

$my			= $_REQUEST['my'];

$query			= "SELECT * FROM dataversion";
$res			= mysqli_query($adController->MySQL,$query);
$data			= mysqli_fetch_assoc($res);
$num			= mysqli_num_rows($res);


$iv			= $data['storehash'];

$output			= file_get_contents(HOSTED_URL."java_version_.php?ve=$my&fromserver=1&hs=$iv");
if($output == "")
	$output = "error";

echo $output;
exit();
?>