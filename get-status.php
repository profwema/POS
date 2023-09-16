<?php
error_reporting(E_ERROR);
require_once("db.conn.php");

$storeid                = $_REQUEST['i'];
$query                  = "SELECT * FROM storeuser WHERE id='$storeid'";
$res                    = mysqli_query($adController->MySQL,$query);
$dataEn                 = mysqli_fetch_assoc($res);
echo   intval($dataEn['enabled']);
?>