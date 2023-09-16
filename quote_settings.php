<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];



$query		= "SELECT * FROM quote_settings WHERE storeid='$storeid' ";
$res		= mysqli_query($adController->MySQL,$query);
$dataCat	= mysqli_fetch_assoc($res);


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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=QUOTATION?> <?=SETTINGS?></h2>
					</div>
					<div class="box-content">                                                                                        
						<form class="form-horizontal" id="q-settings">
						  <input type="hidden" value="updateQSettings" name="f">
<!--                                                  <input type="hidden" value="<?=$dataCat['id']?>" name="nd">-->

                                                  <fieldset>      
                                                      <div class="control-group">
							  <label class="control-label" for="typeahead"><?=BARCODE?> : </label>
							  <div class="controls">
								<input type="checkbox"  value="1" name="barcode" <?php if(intval($dataCat['barcode']) == 1) echo "checked='true'";?>>
							  </div>
							</div>                                                        
                                                        <div class="control-group">
							  <label class="control-label" for="typeahead"><?=UNITS?> : </label>
							  <div class="controls">
								<input type="checkbox"  value="1" name="unit" <?php if(intval($dataCat['unit']) == 1) echo "checked='true'";?>>
							  </div>
							</div>
                                                        
                                                        <div class="control-group">
							  <label class="control-label" for="typeahead"><?=DISCRIPTION?> : </label>
							  <div class="controls">
								<input type="checkbox"  value="1" name="disc" <?php if(intval($dataCat['disc']) == 1) echo "checked='true'";?>>
							  </div>
							</div>
                                                        
                                                        <div class="control-group">
							  <label class="control-label" for="typeahead"><?=IMAGE?> : </label>
							  <div class="controls">
                                                              <input type="checkbox"  value="1" name="img" <?php if(intval($dataCat['img']) == 1) echo "checked='true'";?>>
							  </div>
							</div>
                                                        <div class="control-group">
							  <label class="control-label" for="typeahead"><?=VAT_VALUE?> : </label>
							  <div class="controls">
								<input type="checkbox"  value="1" name="vatValue" <?php if(intval($dataCat['vat_value']) == 1) echo "checked='true'";?>>
							  </div>
							</div> 
                                                      <div class="control-group">
							  <label class="control-label" for="typeahead"><?=VAT_RATIO?> : </label>
							  <div class="controls">
								<input type="checkbox"  value="1" name="vatRate" <?php if(intval($dataCat['vat_rate']) == 1) echo "checked='true'";?>>
							  </div>
							</div>   
                                
							<div class="form-actions">
							  <button type="button" id='submit-Qsettings' class="btn btn-primary"><?=SAVE?></button>
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
