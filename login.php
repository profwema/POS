<?php
require_once("top.php");
require_once("redirection.php");
$lang = $_SESSION['lang'];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo ($lang == 'ar') ? 'rtl' : 'ltr'; ?>">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8">
	<title><?=SITE_TITLE?></title>
	<meta name="description" content="online pos">
	<meta name="author" content="futuregates.net">
	<meta name="keyword" content="online pos,pos,touchscreen pos">
	<!-- end: Meta -->
	
	<!-- start: Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- end: Mobile Specific -->
	
	<!-- start: CSS -->
	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link href="css/modern-login.css" rel="stylesheet">
	<!-- end: CSS -->
	

	<!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	  	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<link id="ie-style" href="css/ie.css" rel="stylesheet">
	<![endif]-->
	
	<!--[if IE 9]>
		<link id="ie9style" href="css/ie9.css" rel="stylesheet">
	<![endif]-->
		
	<!-- start: Favicon -->
	<link rel="shortcut icon" href="img/favicon.ico">
	<!-- end: Favicon -->
	
<style>
/* Additional custom styles if needed */
</style>
		

	<?php require_once("script_php_variables.php");?>
		
		
</head>

<body>
    <!-- Animated Background Shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    
    <div class="login-wrap">
        <!-- Logo Section -->
        <div class="login-logo">
            <img src="img/pos1.png" alt="WAM Tech Soft">
        </div>
        
        <h2><?=LOGIN?></h2>
        <p class="login-subtitle">مرحباً بك، قم بتسجيل الدخول للمتابعة</p>
        
        <!-- Error/Success Messages -->
        <div class="error-message" id="error-msg"></div>
        <div class="success-message" id="success-msg"></div>
        
        <form id='form' class="form">
            <!-- Username Field -->
            <div class="form-group <?php echo ($lang == 'ar') ? 'rtl' : ''; ?>">
                <i class="fa fa-user input-icon"></i>
                <input type="text" placeholder=" " name="user" id="user" autocomplete="username" />
                <label><?=USERNAME?></label>
            </div>
            
            <!-- Password Field -->
            <div class="form-group <?php echo ($lang == 'ar') ? 'rtl' : ''; ?>">
                <i class="fa fa-lock input-icon"></i>
                <input type="password" placeholder=" " name="password" id="password" autocomplete="current-password" />
                <label><?=PASSWORD?></label>
            </div>
            
            <!-- Login Button -->
            <button type="button" id="login">
                <i class="fa fa-sign-in" style="margin-right: 0.5rem;"></i>
                <?=LOGIN?>
            </button>
            
            <!-- Language Switcher -->
            <div class="lang-switcher">
                <a href='javascript:void(0)' onclick="changeLang('ar')" class="lang-flag green" title="العربية">AR</a>
                <a href='javascript:void(0)' onclick="changeLang('en')" class="lang-flag purple" title="English">EN</a>
            </div>
        </form>
    </div>  

	<?php require_once("script.php");?>
	
</body>
</html>
