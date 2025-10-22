<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];


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
			<div class="row-fluid sortable">
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=JOPTITLE?></h2>
					</div>
					<div class="box-content">
						<form class="form-horizontal" id="update-jobTitle">
						  <input type="hidden" value="jobTitleUpdate" name="f">
						  <fieldset>
							
                                                      
                                                        <?php
                                                        
                                                            $query	 	= "SELECT * FROM jobtitle WHERE storeid='$storeid' ";
                                                            $res		= mysqli_query($adController->MySQL,$query);
                                                            do
                                                            {

                                                                     $id                    = $dataCat['id'];
                                                                     $name                  = $dataCat['name_'.$language];

                                                        ?>
                                                                
                                                                    <div class="control-group">
<!--                                                                    <label class="control-label" for="typeahead">< ?=SHIFT?> : </label>-->
                                                                    <div class="controls">
                                                                            <input type="hidden" value='<?=$dataCat['id']?>' name='id[]'/>
                                                                            <input type="text" class="span3 typeahead"  name='name_en[]' id='name' value="<?=$dataCat['name_en']?>" placeholder='english name'>
                                                                            &nbsp;&nbsp;
                                                                            <input type="text" class="span3 typeahead"  name='name_ar[]' id='name' value="<?=$dataCat['name_ar']?>" placeholder='عربى'>
                                                                            
                                                                            &nbsp;&nbsp;

                                                                        

                                                                          
                                                                    </div>
                                                                  </div>
                                                        <?php
                                                            }while($dataCat      = mysqli_fetch_assoc($res));
                                                        ?>
                                                      
                                                      
                                                        
                                                      
                                                      
							
							<div class="form-actions">
							  <button type="button" id='submit-jobTitle' class="btn btn-primary"><?=SAVE?></button>
							  <button type="reset" class="btn"><?=CANCEL?></button>
							</div>
							<p>&nbsp;</p>
							<p class='error-red'>
								&nbsp;
								
							</p>

						  </fieldset>
						</form>   

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
