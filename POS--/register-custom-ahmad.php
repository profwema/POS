<?php
require_once("top.php");
require_once("redirection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8">
	<title><?=REGISTRATION?></title>
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
		
	<?php require_once("script_php_variables.php");?>
		
</head>

<body>
		<div class="container-fluid-full">
		<div class="row-fluid">
					
			<div class="row-fluid">
				<div class="login-box">
					<div class="icons">
						&nbsp;
					</div>
					<h2><?=CREATE_ACCOUNT?></h2>
					<form class="form-horizontal" id="form" action="controller.php?f=createnew" method="post" enctype="multipart/form-data">
						<fieldset>
							
							<div class="input-prepend" title="Name">
								<span class="add-on"><i class="halflings-icon user"></i></span>
								<input class="input-large span10" name="name" id="name" type="text" placeholder="<?=NAME?>"/>
							</div>
							<div class="clearfix"></div>

							<div class="input-prepend" title="Username">
								<span class="add-on"><i class="halflings-icon user"></i></span>
								<input class="input-large span10" name="email" id="emailid" type="text" placeholder="<?=EMAIL?>"/>&nbsp;<label id='email-avl'>&nbsp;</label>

							</div>
							<div class="clearfix"></div>


							<div class="input-prepend" title="Password">
								<span class="add-on"><i class="halflings-icon lock"></i></span>
								<input class="input-large span10" name="password" id="password" type="password" placeholder="<?=PASSWORD?>"/>
							</div>
							<div class="clearfix"></div>

							<div class="input-prepend" title="Logo">
								<span class="add-on"><?=LOGO?></span>
								<input class="input-large span10" name="logo" id="logo" type="file"/>
							</div>
							<div class="clearfix"></div>

							<div class="input-prepend" title="Logo">
								<button type="button" class="btn btn-primary" id='create-account'><?=REGISTER?></button>
							</div>
							<div class="clearfix"></div>
							
							
					</form>
					<hr>
					<h3><a href='login.php'><?=ALREADY_HAVE_ACCOUNT?></a></h3>
				</div><!--/span-->
			</div><!--/row-->
			

	</div><!--/.fluid-container-->
	
		</div><!--/fluid-row-->
	
	<!-- start: JavaScript-->

		<?php require_once("script.php");?>
	<!-- end: JavaScript-->
	
</body>
</html>
