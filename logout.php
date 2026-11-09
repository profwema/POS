<?php
/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * تسجيل الخروج الآمن - Logout
 * 
 * هندسة وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
 */

require_once __DIR__ . '/config/config.php';

Auth::logout();
header('Location: ' . BASE_URL . 'login.php');
exit();

