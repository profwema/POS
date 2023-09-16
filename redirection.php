<?php
$pageName = basename($_SERVER['PHP_SELF']);

$freepages = array("login.php","register.php","forgot_password.php");

if(!isset($_SESSION['userlogged']))
{
	if(!in_array($pageName,$freepages))
	{
		print("<script>location.replace('login.php');</script>");
	}
}
else
{
	if(in_array($pageName,$freepages))
	{
		print("<script>location.replace('.');</script>");
	}
}

?>