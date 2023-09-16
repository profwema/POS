<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language = $_SESSION['lang'];
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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=ADD_EXPENDITURE?></h2>
					</div>
					<div class="box-content">
						<form class="form-horizontal" id="add-expenditure">
						  <input type="hidden" value="addExpenditure" name="f">
						  <fieldset>
							<h4><?=ENGLISH?></h4>
							<div class="control-group">
							  <label class="control-label"><?=BRANCH?> :</label>
							  <div class="controls">
								  <select name="branch" id='branch-items'>
									<option value=''><?=GENERAL?></option>
									<?php
										$query = "SELECT * FROM branches WHERE storeid='$storeid' ORDER BY name_en ASC";
										$res   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
										while($data= mysqli_fetch_assoc($res))
										{
											$id   = $data['id'];
											$name = $data['name_'.$language];
											echo "<option value='$id'>$name</option>";

										}
									?>
								  </select>
							  </div>
							  
							</div>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=DESCRIPTION?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead"  name='desc_en' id='desc_en'> &nbsp; *
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label"><?=COST?> :</label>
							  <div class="controls">
								<input type="text" name='cost' id="cost" class="float-val span6 typeahead"> &nbsp; *
							  </div>
							</div>
							<div class="control-group">
								<label class="control-label"><?=EXPENDITURE_DATE?></label>
								<div class="controls">
								  <input type="text" class="input-xlarge datepicker datepicker-nobar" name="dated" id="dated" value="<?=date("m/d/Y")?>" > &nbsp; *
								</div>
							</div>
							
							<hr>
							<h4><?=ARABIC?></h4>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=DESCRIPTION?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead"  name='desc_ar' id='desc_ar'>
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							
							<div class="form-actions">
							  <button type="button" id='submit-expenditure' class="btn btn-primary"><?=SAVE?></button>
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
