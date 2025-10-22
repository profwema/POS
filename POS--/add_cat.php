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
			<div>
				<div class="box span12">
					<div class="box-header" data-original-title>
                                            <h2><i class="halflings-icon edit"></i><span class="break"></span>
                                                <a href="categories.php"><?=CATEGORIES?></a> >> <?=ADD_NEW?>
                                            </h2>
                                        </div>
					<div class="box-content">
						<form class="form-horizontal" id="add-cat">
						  <input type="hidden" value="addCat" name="f">
						  <fieldset>
							<h4><?=LANGUAGE_1?></h4>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=CAT_NAME?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead"  name='name' id='name'> &nbsp; *
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="fileInput"><?=IMAGE?></label>
							  <div class="controls">
								<div class="uploader" id="uniform-fileInput"><input class="input-file uniform_on" id="fileInput" name="file[]" type="file" accept="image/*"><span class="filename" style="-webkit-user-select: none;">No file selected</span><span class="action" style="-webkit-user-select: none;">Choose File</span></div>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label"><?=BRANCH?> :</label>
							  <div class="controls">
								  <select name="branch[]" data-rel="chosen" id='branch' multiple data-rel="chosen">
									<?php
										$query = "SELECT * FROM branches  ORDER BY name_en ASC";
										$res   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
										while($data= mysqli_fetch_assoc($res))
										{
											$id   = $data['id'];
											$name = $data['name_'.$language];
											echo "<option value='$id'>$name</option>";

										}
									?>
								  </select> <label class='hint'>( <?=LEAVE_BLANK_FOR_ALL?> )</label>
							  </div>
							  
							</div>
							
							
							<div class="control-group">
								<label class="control-label"><?=SHOW_TO_CASHIER?></label>
								<div class="controls">
								  <label class="checkbox inline">
									<input type="checkbox" value="1" name="show_to_cashier" checked="true">
								  </label>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label"><?=ENABLED?></label>
								<div class="controls">
								   <label class="checkbox inline">
									<input type="checkbox" value="1" name="enabled" checked="true">
								  </label>
								</div>
							</div>

							<hr>
							<h4><?=LANGUAGE_2?></h4>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=CAT_NAME?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead"  name='name_ar'    >
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							
							<div class="form-actions">
							  <button type="button" id='submit-cat' class="btn btn-primary"><?=SAVE?></button>
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
