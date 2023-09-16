<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];

$idval		= $adController->encrypt_decrypt(2,urldecode($_REQUEST['sd']),0);
$idval 		= $adController->encrypt_decrypt(2,$idval,0);
$idval2		= $adController->encrypt_decrypt(1,$_REQUEST['sd'],0);

$query		= "SELECT * FROM suppliers WHERE storeid='$storeid' AND id='$idval'";
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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=UPDATE_SUPPLIER?></h2>
					</div>
					<div class="box-content">                                                                                        
                                            <form class="form-horizontal" id="add-sup">
                                                <input type="hidden" value="<?=$idval2?>" name="nd" id="nd">
						  <input type="hidden" value="updateSupplier" name="f">
						  <fieldset>
							<h4><?=LANGUAGE_1?></h4>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=SUPPLIER_NAME?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead" name='name' id='name' value="<?=$dataCat['name_en']?>">
								<span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							
							<h4><?=LANGUAGE_2?></h4>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=SUPPLIER_NAME?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead" name='name_ar'value="<?=$dataCat['name_ar']?>" >
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div> 
                                                        <br>

							<div class="control-group">
							  <label class="control-label"><?=COM_REG?> :</label>
							  <div class="controls">
								<input type="text"  name='com_reg' class="span6 typeahead" value="<?=$dataCat['com_reg']?>"  >
							  </div>
							</div>
                                                        
							<div class="control-group">
							  <label class="control-label"><?=TAX_NO?> :</label>
							  <div class="controls">
								<input type="text"  name='tax_no' class="span6 typeahead" value="<?=$dataCat['tax_no']?>" >
							  </div>
							</div>
                                                        
                                                        <div class="control-group">
							  <label class="control-label"><?=PHONE?> :</label>
							  <div class="controls">
								<input type="text"  name='phone' class="span6 typeahead" id='phone' onkeypress="return isNumber(event)"  maxlength="15" value="<?=$dataCat['phone']?>">&nbsp; *
							  </div>
							</div>
                                                        <div class="control-group">
							  <label class="control-label" for="typeahead"><?=EMAIL?> : </label>
							  <div class="controls">
                                                              <input type="email" class="span6 typeahead"  name='email' id='email' value="<?=$dataCat['email']?>"> 
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>    
                                                        
							<div class="control-group">
							  <label class="control-label"><?=ADDRESS?> :</label>
							  <div class="controls">
                                                              <input type="text" class="span6 typeahead"  name='address' id='address' value="<?=$dataCat['address']?>"> 
							  </div>
							</div> 
                                                        
							<div class="form-actions">
                                                            
                                                            <button type="button" id='submit-supplier' class="btn btn-primary"><?=SAVE?></button>
                                                            <button type="reset" class="btn"><?=CANCEL?></button>
							</div>
							<p>&nbsp;</p>
							<p class='error-red'>
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
