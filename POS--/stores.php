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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=STORES?></h2>
					</div>
					<div class="box-content">
						<form class="form-horizontal" id="update-stores">
						  <input type="hidden" value="storesUpdate" name="f">
						  <fieldset>
							
                                                      
                                                        <?php
                                                        
                                                            $query	 	= "SELECT * FROM stores WHERE storeid='$storeid' ";
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
                                                                            <select name="branch[]"  id='branch' data-rel="chosen" class="chzn-select" width:80%"> 
                                                                                  <option value=''>Select Branch </option>            
                                                                                    <?php       
                                                                                         $queryy 	= "SELECT * FROM branches WHERE storeid='$storeid' ORDER BY name_en ASC";
                                                                                         $ress   	= mysqli_query($adController->MySQL,$queryy) or die(mysqli_error($adController->MySQL));
                                                                                         while($data= mysqli_fetch_assoc($ress))
                                                                                         {
                                                                                             $id   = $data['id'];
                                                                                             $name = $data['name_'.$language];
                                                                                             $sel	="";               
                                                                                             if($dataCat['branch'] == $id)
                                                                                             {                                                                            
                                                                                                 $sel = " selected='selected' ";
                                                                                             }
                                                                                             echo "<option value='$id'$sel>$name</option>";
                                                                                         }
                                                                                        ?>                                                                                    
                                                                            </select>
                                                                    </div>
                                                                  </div>
                                                        <?php
                                                            }
                                                            while($dataCat      = mysqli_fetch_assoc($res));
                                                        ?>
                                                      
                                                      
                                                        
                                                      
                                                      
							
							<div class="form-actions">
							  <button type="button" id='submit-stores' class="btn btn-primary"><?=SAVE?></button>
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
