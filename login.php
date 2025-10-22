<?php
require_once("top.php");
require_once("redirection.php");
$lang = $_SESSION['lang'];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo ($lang == 'ar') ? 'rtl' : 'ltr'; ?>" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= SITE_TITLE ?> - <?= LOGIN ?></title>
    <meta name="description" content="online pos">
    <meta name="author" content="futuregates.net">
    <meta name="keyword" content="online pos,pos,touchscreen pos">

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/admin-pro.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="shortcut icon" href="img/favicon.ico">

    <style>
        /* ═══════════════════════════════════════════════════════════════
	   PROFESSIONAL LOGIN PAGE STYLES
	   ═══════════════════════════════════════════════════════════════ */

        :root {
            --login-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --login-card-bg: rgba(255, 255, 255, 0.95);
            --login-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        [data-theme="dark"] {
            --login-bg: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            --login-card-bg: rgba(30, 30, 50, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--login-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background Shapes */
        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            animation: float 15s infinite ease-in-out;
        }

        .shape-1 {
            width: 400px;
            height: 400px;
            background: rgba(103, 126, 234, 0.5);
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 350px;
            height: 350px;
            background: rgba(118, 75, 162, 0.5);
            bottom: -100px;
            left: -100px;
            animation-delay: 3s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -30px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        /* Login Container */
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 450px;
            background: var(--login-card-bg);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: var(--login-shadow);
            padding: 50px 40px;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo */
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo img {
            width: 120px;
            height: auto;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.1));
            animation: pulse 2s infinite ease-in-out;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        /* Title */
        .login-title {
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .login-subtitle {
            text-align: center;
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 30px;
        }

        /* Form Groups */
        .form-group-login {
            position: relative;
            margin-bottom: 25px;
        }

        .form-group-login i {
            position: absolute;
            top: 50%;
            right: 16px;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 18px;
            transition: all 0.3s ease;
            z-index: 1;
        }

        [dir="ltr"] .form-group-login i {
            right: auto;
            left: 16px;
        }

        .form-group-login input {
            width: 100%;
            padding: 16px 50px;
            font-size: 15px;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group-login input:focus {
            border-color: var(--primary);
            background: var(--bg-secondary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .form-group-login input:focus+i {
            color: var(--primary);
        }

        /* Messages */
        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.show {
            display: block;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .success-message {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Language Switcher */
        .lang-switcher {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .lang-btn {
            padding: 8px 20px;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background: transparent;
            color: var(--text-primary);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .lang-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(59, 130, 246, 0.1);
        }

        /* Responsive */
        @media (max-width: 500px) {
            .login-container {
                padding: 40px 30px;
            }

            .login-title {
                font-size: 24px;
            }
        }
    </style>

    <?php require_once("script_php_variables.php"); ?>
</head>

<body>
    <!-- Animated Background Shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <!-- Login Container -->
    <div class="login-container">

        <!-- Logo -->
        <div class="login-logo">
            <img src="img/pos1.png" alt="WAM Tech POS">
        </div>

        <!-- Title -->
        <h1 class="login-title"><?= LOGIN ?></h1>
        <p class="login-subtitle">مرحباً بك، قم بتسجيل الدخول للمتابعة</p>

        <!-- Messages -->
        <div class="error-message" id="error-msg"></div>
        <div class="success-message" id="success-msg"></div>

        <!-- Login Form -->
        <form id='form'>

            <!-- Username Field -->
            <div class="form-group-login">
                <input
                    type="text"
                    name="user"
                    id="user"
                    placeholder="<?= USERNAME ?>"
                    autocomplete="username"
                    required />
                <i class="fas fa-user"></i>
            </div>

            <!-- Password Field -->
            <div class="form-group-login">
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="<?= PASSWORD ?>"
                    autocomplete="current-password"
                    required />
                <i class="fas fa-lock"></i>
            </div>

            <!-- Login Button -->
            <button type="button" id="login" class="btn-login">
                <i class="fas fa-sign-in-alt"></i>
                <span><?= LOGIN ?></span>
            </button>

            <!-- Language Switcher -->
            <div class="lang-switcher">
                <a href='javascript:void(0)' onclick="changeLang('ar')" class="lang-btn" title="العربية">
                    <i class="fas fa-language"></i> عربي
                </a>
                <a href='javascript:void(0)' onclick="changeLang('en')" class="lang-btn" title="English">
                    <i class="fas fa-language"></i> English
                </a>
            </div>

        </form>

    </div>

    <?php require_once("script.php"); ?>

</body>

</html>