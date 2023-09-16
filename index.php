<?php
//print("<script>location.replace('sales-report.php');</script>");
//die;
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
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
                                <a href="#"><?=DASHBOARD?></a>
                            </h2>
                        </div>
                        <div class="box-content">
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
