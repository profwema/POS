
<?php
require_once("top.php");
?>
<!DOCTYPE html>
<html lang="en">
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
	<link id="bootstrap-style" href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
	<link id="base-style" href="css/style.css" rel="stylesheet">
	<link id="base-style-responsive" href="css/style-responsive.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,latin-ext' rel='stylesheet' type='text/css'>
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
	
			<style type="text/css">
			body { background: url(img/bg-login.jpg) !important; }
		</style>
		
		
		
</head>

<body>
		<div class="container-fluid-full">
		<div class="row-fluid">
					
			<div class="row-fluid">
				<div class="login-box">
					<div class="icons">
						&nbsp;
					</div>
					<h2><?=LOGIN_TO_ACCOUNT?></h2>
					<form class="form-horizontal" id='form'>
						<fieldset>
							
							<div class="input-prepend" title="Username">
								<span class="add-on"><i class="halflings-icon user"></i></span>
								<input class="input-large span10" name="email" id="email" type="text" placeholder="<?=EMAIL?>"/>
							</div>
							<div class="clearfix"></div>

							<div class="button-login">	
								<button type="button" class="btn btn-primary" id="forgot"><?=LOGIN?></button>
							</div>
							<div class="clearfix"></div>
					</form>
					<hr>
					<h3><a href='forgot.php'><?=FORGOT_PASSWORD?></a></h3>
					<h3><a href='register.php'><?=CREATE_ACCOUNT?></a></h3>
				</div><!--/span-->
			</div><!--/row-->
			

	</div><!--/.fluid-container-->
	
		</div><!--/fluid-row-->
	
	<?php require_once("script.php");?>
	
</body>
</html>
