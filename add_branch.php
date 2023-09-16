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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=NEW_BRANCH?></h2>
					</div>
					<div class="box-content">
						<form class="form-horizontal" id="add-branch">
						  <input type="hidden" value="addBranch" name="f">
						  <fieldset>
							<h4><?=LANGUAGE_1?></h4>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=BRANCH_NAME?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead"  name='name' id='name'> &nbsp; *
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label"><?=STREET?> :</label>
							  <div class="controls">
								<input type="text" name='street' class="span6 typeahead" id="street"> &nbsp; *
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label"><?=COUNTRY?> :</label>
							  <div class="controls">
								<select name="country" data-rel="chosen" id='country'>
									<option value="">--<?=SELECT_COUNTRY?>--</option>
									<?php
										$query = "SELECT * FROM us_country_master ORDER BY cou_name_en ASC";
										$res   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
										while($data= mysqli_fetch_assoc($res))
										{
                                                                                    $id   = $data['cou_id'];
                                                                                    $name = $data['cou_name_'.$language];
                                                                                    echo "<option value='$id'>$name</option>";
										}
									?>
								</select> &nbsp; *
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label"><?=STATE?> :</label>
							  <div class="controls" id="state-control">
								<select name="state" id='state' data-rel="chosen">
									<option value="">--<?=SELECT_STATE?>--</option>
								</select> &nbsp; *
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label"><?=CITY?> :</label>
							  <div class="controls">
								<select name="city" id='city'  data-rel="chosen">
									<option value="">--<?=SELECT_CITY?>--</option>
								</select> &nbsp; *
							  </div>
							</div>
							
							<div class="control-group">
							  <label class="control-label"><?=PHONE?> :</label>
							  <div class="controls">
								<input type="text"  name='phone' class="span6 typeahead" id='phone' onkeypress="return isNumber(event)"  maxlength="15">&nbsp; *
							  </div>
							</div>

							<div class="control-group">
							  <label class="control-label"><?=FAX?> :</label>
							  <div class="controls">
								<input type="text"  name='fax' class="span6 typeahead"  onkeypress="return isNumber(event)"  maxlength="15" >
							  </div>
							</div>
							<hr>

							<div class="control-group">
							  <label class="control-label"><?=COMMERCIAL_LICENCE_NUM?> :</label>
							  <div class="controls">
								<input type="text"  name='crnum' class="span6 typeahead"   >
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label"><?=COMMERCIAL_LICENCE_APP?> :</label>
							  <div class="controls">
								<input type="text"  name='crapp' class="span6 datepicker"   >
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label"><?=COMMERCIAL_LICENCE_EXP?> :</label>
							  <div class="controls">
								<input type="text"  name='crexp' class="span6 datepicker"   >
							  </div>
							</div>
							<hr>
							<div class="control-group">
							  <label class="control-label"><?=MUNICIPAL_LICENCE_NUM?> :</label>
							  <div class="controls">
								<input type="text"  name='munum' class="span6 typeahead"   >
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label"><?=MUNICIPAL_LICENCE_APP?> :</label>
							  <div class="controls">
								<input type="text"  name='muapp' class="span6 datepicker"   >
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label"><?=MUNICIPAL_LICENCE_EXP?> :</label>
							  <div class="controls">
								<input type="text"  name='muexp' class="span6 datepicker"   >
							  </div>
							</div>


							<hr>
							

							<hr>
							<h4><?=LANGUAGE_2?></h4>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=BRANCH_NAME?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead"  name='name_ar'    >
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=STREET?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead"  name='street_ar'    >
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							
							<div class="form-actions">
							  <button type="button" id='submit-branch' class="btn btn-primary"><?=SAVE?></button>
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
