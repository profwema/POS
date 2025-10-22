<?php
include('db.php');

if(isSet($_POST['username']))
{
$username = $_POST['username'];



$sql_check = mysqli_query($adController->MySQL,$adController->MySQLi_Handle,"SELECT id FROM users WHERE user='$username'") or die(mysqli_error($adController->MySQL));

if(mysqli_num_rows($sql_check))
{
echo '<span style = "color:red">اسم المستخدم غير متاح </span>';
}
else
{
echo 'OK';
}

}

?>