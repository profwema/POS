<?php
require_once('db.conn.php');

if (isset($_POST['username'])) {
    $username = trim($_POST['username']);

    if ($username === '') {
        echo '<span style="color:red">يرجى إدخال اسم المستخدم</span>';
        exit;
    }

    $sql_check = safe_query($MySQL, "SELECT id FROM employees WHERE user = ?", [$username]);

    if ($sql_check && mysqli_num_rows($sql_check) > 0) {
        echo '<span style="color:red">اسم المستخدم غير متاح</span>';
    } else {
        echo 'OK';
    }
}
?>