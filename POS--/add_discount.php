<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];

$idval		= $adController->encrypt_decrypt(2,urldecode($_REQUEST['d']),0);

$query	 	= "SELECT * FROM discount WHERE id='$idval'";
$res		= mysqli_query($adController->MySQL,$query);
$dataDiscount	= mysqli_fetch_assoc($res);

$query		  = "SELECT GROUP_CONCAT(id) AS br FROM branches ";
$resBranch	  = mysqli_query($adController->MySQL,$query);
$dataBranch 	  = mysqli_fetch_assoc($resBranch);
$branchArray	  = explode(",",$dataBranch['br']);

$discount	= $dataCat['amount'];

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
						<h2><i class="halflings-icon money"></i><span class="break"></span><?=DISCOUNT?></h2>
					</div>
					<div class="box-content">
						<form class="form-horizontal" id="update-discount">
						  <input type="hidden" value="discountAdd" name="f">
						  <input type="hidden" value="<?=$_REQUEST['d']?>" name="vd">
						  <fieldset>
							<div class="control-group">
							  <label class="control-label"><?=BRANCH?> :</label>
							  <div class="controls">
								  <select name="branch" id='branch-items' class="span4">
									<?php
										$query = "SELECT * FROM branches  ORDER BY name_en ASC";
										$res   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
										while($data= mysqli_fetch_assoc($res))
										{
											$id   = $data['id'];
											$name = $data['name_'.$language];
											$selected	="";
											if(in_array($data['id'],$branchArray))
												$selected=" selected='true' ";


											echo "<option value='$id' $selected>$name</option>";

										}
									?>
								  </select>
							  </div>
							  
							</div>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=DISCOUNT?> : </label>
							  <div class="controls">
								<input type="text" class="int-val span4 typeahead" maxlength="4"  name='amount' id='amount' value="<?=$dataDiscount['amount']?>"> &nbsp; *
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=MIN_AMOUNT?> : </label>
							  <div class="controls">
								<input type="text" class="int-val span4 typeahead" maxlength="4"  name='minamt' id='minamt' value="<?=$dataDiscount['minamt']?>"> &nbsp; *
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<p>&nbsp;</p>
							<p class='error-red'>
								&nbsp;
								
							</p>
							<div class="form-actions">
							  <button type="button" id='submit-discount' class="btn btn-primary"><?=SAVE?></button>
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
