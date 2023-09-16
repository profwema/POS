<?php
error_reporting(E_ERROR);
ini_set('display_errors', 1);
session_start();

if(!isset($_SESSION['lang']))
	$_SESSION['lang']="ar";

define("LANG",$_SESSION['lang']);
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


$store_type_    = $_SESSION['type_of_store'];

require_once("translate.php");
require_once("enc.php");
require_once("email.php");
require_once("Encryption.php");
require_once("resize-class.php");
define("SALOON","2");

        function flout_format($var)
        {
        $varSt= number_format($var, 2, '.', ',');
        return $varSt;
        }
        
?>