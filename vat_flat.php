<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];

$idval		= $adController->encrypt_decrypt(2,urldecode($_REQUEST['d']),0);

$query	 	= "SELECT * FROM vat_default WHERE storeid='$storeid'";
$res		= mysqli_query($adController->MySQL,$query);
$dataDiscount	= mysqli_fetch_assoc($res);

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
                                            <h2><i class="halflings-icon money"></i><span class="break"></span>&nbsp;</h2>
					</div>
					<div class="box-content">
						<form class="form-horizontal" id="update-discount">
						  <input type="hidden" value="vatAdd" name="f">
						  <fieldset>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=TAX?> : </label>
							  <div class="controls">
								<input type="text" class="float-val span4 typeahead" maxlength="4"  name='percentage' id='percentage' value="<?=intval($dataDiscount['percentage'])?>"> &nbsp; *
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							
							<p>&nbsp;</p>
							<p class='error-red'>
								&nbsp;
								
							</p>
							<div class="form-actions">
							  <button type="button" id='submit-vat' class="btn btn-primary"><?=SAVE?></button>
							  <button type="reset" class="btn"><?=CANCEL?></button>
							</div>
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
