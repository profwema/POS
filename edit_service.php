<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];

$query          = "SELECT * FROM services WHERE storeid='$storeid'";
$res            = mysqli_query($adController->MySQL,$query);
$data           = mysqli_fetch_assoc($res);
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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=EDIT_SERVICE?></h2>
					</div>
					<div class="box-content">
						<form class="form-horizontal" id="add-service">
						  <input type="hidden" value="updateService" name="f">
                                                  <input type="hidden" value="<?=$_REQUEST['sd']?>" name="id">
						  <fieldset>
							<h4><?=LANGUAGE_1?></h4>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=SERVICE_NAME_EN?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead"  name='name' id='name' value="<?=$data['name_en']?>"> &nbsp; *
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							
							<h4><?=LANGUAGE_2?></h4>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=SERVICE_NAME_AR?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead"  name='name_ar'   value="<?=$data['name_ar']?>"  >
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="form-actions">
							  <button type="button" id='submit-service' class="btn btn-primary"><?=SAVE?></button>
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
