<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];

$idval		= $adController->encrypt_decrypt(2,urldecode($_REQUEST['sd']),0);
$idval 		= $adController->encrypt_decrypt(2,$idval,0);
$idval2		= $adController->encrypt_decrypt(1,$_REQUEST['sd'],0);

$query		= "SELECT * FROM employees WHERE storeid='$storeid' AND id='$idval'";
$res		= mysqli_query($adController->MySQL,$query);
$dataCat	= mysqli_fetch_assoc($res);


$query 		= "SELECT * FROM images WHERE foreign_id='$dataCat[id]' AND `table`='employees'";
$resImage	= mysqli_query($adController->MySQL,$query);
$dataImage	= mysqli_fetch_assoc($resImage);

$img		= $adController->getDirectoryOnlyPath(DIR_EMP_NAME);
$thumb		= $img.$dataImage['thumb'];

if($data['store_branch']!="")						
	$branchArray	= explode(",",$dataCat['store_branch']);
else
{
	$query		  = "SELECT GROUP_CONCAT(id) AS br FROM branches WHERE storeid='$storeid'";
	$resBranch	  = mysqli_query($adController->MySQL,$query);
	$dataBranch 	  = mysqli_fetch_assoc($resBranch);
	$branchArray	  = explode(",",$dataBranch['br']);
}

$designationArray = explode(',',$dataCat['type']);
$services    = explode(',',$dataCat['services']);
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
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=UPDATE_EMPLOYEE?></h2>
					</div>
					<div class="box-content">
						<form class="form-horizontal" id="add-emp">
						  <input type="hidden" value="<?=$idval2?>" name="nd" id="nd">
						  <input type="hidden" value="updateEmp" name="f">
						  <fieldset>
							<h4><?=LANGUAGE_1?></h4>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=EMPLOYEE_NAME?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead"  name='name' id='name' value="<?=$dataCat['name_en']?>"> &nbsp; *
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="fileInput"><?=IMAGE?></label>
							  <div class="controls">
								<div class="uploader" id="uniform-fileInput"><input class="input-file uniform_on" id="fileInput" name="file[]" type="file" accept="image/*"><span class="filename" style="-webkit-user-select: none;">No file selected</span><span class="action" style="-webkit-user-select: none;">Choose File</span></div>
								&nbsp;<img src='<?=$thumb?>' class="thumb">
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=EMAIL?> : </label>
							  <div class="controls">
								 <input type="text" class="span6 typeahead"  name='email' id='email' value="<?=$dataCat['email']?>"> &nbsp; *
								 <span class="help-inline email-un">&nbsp;</span>
								 <label class='hint'>( <?=WILL_BE_USED_AS_LOGIN?> )</label>
							  </div>
							</div>
                               
                                                        <div class="control-group">
							  <label class="control-label" for="typeahead"><?=TILL_CONTROL?> : </label>
							  <div class="controls">
								<input type="checkbox"  value="1" name="till_control" <?php if(intval($dataCat['till_control']) == 1) echo "checked='true'";?>>
							  </div>
							</div>                                                        
                                                        <div class="control-group">
							  <label class="control-label" for="typeahead"><?=SHOW_REPORT?> : </label>
							  <div class="controls">
								<input type="checkbox"  value="1" name="show_report" <?php if(intval($dataCat['show_report']) == 1) echo "checked='true'";?>>
							  </div>
							</div>
                                                        
                                                        <div class="control-group">
							  <label class="control-label" for="typeahead"><?=ALLOW_PRICE_CHANGE?> : </label>
							  <div class="controls">
								<input type="checkbox"  value="1" name="allow_price_change" <?php if(intval($dataCat['allow_price_change']) == 1) echo "checked='true'";?>>
							  </div>
							</div>
                                                        
                                                        <div class="control-group">
							  <label class="control-label" for="typeahead"><?=CARD_PAYMENT?> : </label>
							  <div class="controls">
                                                              <input type="checkbox"  value="1" name="visa" <?php if(intval($dataCat['visa']) == 1) echo "checked='true'";?>>
							  </div>
							</div>
                                                        <div class="control-group">
							  <label class="control-label" for="typeahead"><?=SABAKAH?> : </label>
							  <div class="controls">
								<input type="checkbox"  value="1" name="mada" <?php if(intval($dataCat['mada']) == 1) echo "checked='true'";?>>
							  </div>
							</div>
                                                        
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=MOBILE?> : </label>
							  <div class="controls">
								 <input type="text" class="int-val span6 typeahead" maxlength="14"  name='mobile' id='mobile' value="<?=$dataCat['contact']?>">
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label"><?=BRANCH?> :</label>
							  <div class="controls">
								  <select name="branch" id='branch-items'>
									<?php
										$query = "SELECT * FROM branches WHERE storeid='$storeid' ORDER BY name_en ASC";
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
							  <label class="control-label"><?=DESIGNATION?> :</label>
							  <div class="controls" id="cat-list-data">
								  <select name="type" id='type'>
									<?php
										$query 	= "SELECT * FROM user_rights WHERE FIND_IN_SET($store_type_,type) ORDER BY designation_en ASC";
										$res   	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
										while($data= mysqli_fetch_assoc($res))
										{
											$id   = $data['id'];
											$name = $data['designation_'.$language];
											$selected	="";
											if(in_array($data['id'],$designationArray))
												$selected=" selected='true' ";
											$output[count($output)] = "<option value='$id' $selected>$name</option>";
								
										}
										echo implode(" ",$output);
									?>
								  </select>
							  </div>
							  
							</div>
                                                        
                                                        
							
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=PASSPORT_NUMBER?> : </label>
							  <div class="controls">
								 <input type="text" class="span6 typeahead"   maxlength="14" name='passport_num' id='passport_num' value="<?=$dataCat['passport_num']?>">
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="control-group">
								<label class="control-label"><?=PASSPORT_EXPIRY?></label>
								<div class="controls">
								  <input type="text" class="input-xlarge datepicker" name="passport-expiry"  value="<?=$adController->getDateForUpdate($dataCat['passport_expiry'])?>">
								</div>
							</div>
							<div class="anti-service control-group">
							  <label class="control-label" for="typeahead"><?=IQAMA_NUMBER?> : </label>
							  <div class="controls">
								<input type="text" class="int-val span6 typeahead" maxlength="14"  name='iqama_number' id='iqama_number' value="<?=$dataCat['iqama_num']?>">
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="control-group">
								<label class="control-label"><?=IQAMA_EXPIRY?></label>
								<div class="controls">
								  <input type="text" class="input-xlarge datepicker" name="iqama-expiry"  value="<?=$adController->getDateForUpdate($dataCat['iqama_expiry'])?>">
								</div>
							</div>
							<div class="anti-service control-group">
							  <label class="control-label" for="typeahead"><?=INSURANCE_NUMBER?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead" maxlength="14"  name='insurance_number' id='insurance_number' value="<?=$dataCat['insurance_num']?>">
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="control-group">
								<label class="control-label"><?=INSURANCE_EXPIRY?></label>
								<div class="controls">
								  <input type="text" class="input-xlarge datepicker" name="insurance-expiry"  value="<?=$adController->getDateForUpdate($dataCat['insurance_expiry'])?>">
								</div>
							</div>
							<div class="anti-service control-group">
							  <label class="control-label" for="typeahead"><?=MEDICAL_NUMBER?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead" maxlength="14"  name='medical_number' id='medical_number'  value="<?=$dataCat['medical_num']?>">
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="control-group">
								<label class="control-label"><?=MEDICAL_EXPIRY?></label>
								<div class="controls">
								  <input type="text" class="input-xlarge datepicker" name="medical-expiry"  value="<?=$adController->getDateForUpdate($dataCat['medical_expiry'])?>" >
								</div>
							</div>
							<div class="anti-service control-group">
							  <label class="control-label" for="typeahead"><?=LICENCE_NUMBER?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead" maxlength="14"  name='license_number' id='license_number'   value="<?=$dataCat['license_num']?>">
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="control-group">
								<label class="control-label"><?=LICENCE_EXPIRY?></label>
								<div class="controls">
								  <input type="text" class="input-xlarge datepicker" name="license-expiry" value="<?=$adController->getDateForUpdate($dataCat['license_expiry'])?>" >
								</div>
							</div>
							<h4><?=LANGUAGE_2?></h4>
							<div class="control-group">
							  <label class="control-label" for="typeahead"><?=EMPLOYEE_NAME?> : </label>
							  <div class="controls">
								<input type="text" class="span6 typeahead"  name='name_ar'     value="<?=$dataCat['name_ar']?>"  >
								 <span class="help-inline">&nbsp;</span>
							  </div>
							</div>
							<div class="form-actions">
							  <button type="button" id='submit-emp' class="btn btn-primary"><?=SAVE?></button>
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
