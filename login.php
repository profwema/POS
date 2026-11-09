<?php
/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * بوابة تسجيل الدخول - Login Controller & Entry Point
 * 
 * هندسة وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
 */

require_once __DIR__ . '/config/config.php';

// إذا كان المستخدم مسجل دخوله بالفعل، نوجهه مباشرة
if (Auth::check()) {
    if (Auth::isCashier()) {
        header('Location: ' . BASE_URL . 'pos.php');
    } else {
        header('Location: ' . BASE_URL . 'index.php');
    }
    exit();
}

$errorMessage = null;

// معالجة طلب تسجيل الدخول
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::requireCsrf();

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $errorMessage = 'يُرجى إدخال اسم المستخدم وكلمة المرور.';
    } else {
        $result = Auth::attempt($username, $password);
        if ($result['success']) {
            if (Auth::isCashier()) {
                header('Location: ' . BASE_URL . 'pos.php');
            } else {
                header('Location: ' . BASE_URL . 'index.php');
            }
            exit();
        } else {
            $errorMessage = $result['message'];
        }
    }
}

// عرض واجهة تسجيل الدخول
View::render('modules/auth/login', [
    'pageTitle'    => 'تسجيل الدخول | ' . APP_NAME,
    'errorMessage' => $errorMessage
], 'blank');

