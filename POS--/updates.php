<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];




$query		= "SELECT * FROM version";
$res		= mysqli_query($adController->MySQL,$query);
$data		= mysqli_fetch_assoc($res);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
	<?php require_once("script_php_variables.php");?>
	<?php require_once("header.php");?>	
</head>

<body>

		<?php require_once("header_top.php");?>
    

    		
    <div class="container-fluid-full">
	<div class="row-fluid">
			<?php require_once("left_menu.php");?>
	    <div id="content" class="span10">
		<div>
		    <div class="box span12">			
                        <div class="box-header" data-original-title>
                            <h2><i class="halflings-icon edit"></i><span class="break"></span>
                                <a href="#"><?=UPDATES?></a>
                            </h2>
                        </div>
                        <div class="box-content">

			    <div class="update">
				<img src="img/pos1.png">		
				<div class="left">
				    <div class="title">  WAM Tech Soft</div>
				    <span> Version : <label ><?=$data['version']?></label> 
                                        <input type="hidden" id="ver" value="<?=$data['serverId']?>">
				    </span>
                                    <div id='checkResult'>
                                        <button class="btn btn-primary" id='updareCheck'>  Chick for new updates</button>
                                    </div>

                                    <button class="btn btn-primary" id='updare' style="display: none"> Update Now</button>
                             
				</div>
				<div class="clearfix"></div>		
			    </div>
			</div> 
		    </div>
		</div>
	    </div>			
	</div>
    </div>
   
    <div class="clearfix"></div>
    
	
	<?php require_once("footer.php");?>
</body>
</html>
