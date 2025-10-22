<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];



$query		= "SELECT * FROM main_settings WHERE storeid='$storeid' ";
$res		= mysqli_query($adController->MySQL, $query);
$dataCat	= mysqli_fetch_assoc($res);


?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
	<?php require_once("script_php_variables.php"); ?>
	<?php require_once("header.php"); ?>
</head>

<body>
	<?php require_once("header_top.php"); ?>

	<div class="container-fluid-full">
		<div class="row-fluid">
			<?php require_once("left_menu.php"); ?>
			<div id="content" class="span10">
				<div>
					<div class="box span12">
						<div class="box-header" data-original-title>
							<h2><i class="halflings-icon edit"></i><span class="break"></span><?= MAIM_SETTINGS ?></h2>
						</div>
						<div class="box-content">
							<form class="form-horizontal" id="settings" method="POST" action="#">
								<?php echo csrf_input(); ?>
								<input type="hidden" value="updateSettings" name="f">
								<!--                                                  <input type="hidden" value="<?= $dataCat['id'] ?>" name="nd">-->

								<fieldset>
									<h4><?= LANGUAGE_1 ?></h4>
									<div class="control-group">
										<label class="control-label" for="typeahead"><?= CUMPANY_NAME ?> : </label>
										<div class="controls">
											<input type="text" class="span6 typeahead" name='name' id='name' value="<?= $dataCat['name_en'] ?>" required data-required-msg="<?= REQUIRED ?>" aria-label="<?= CUMPANY_NAME ?> <?= LANGUAGE_1 ?>">
											<span class="help-inline">&nbsp;</span>
										</div>
									</div>

									<h4><?= LANGUAGE_2 ?></h4>
									<div class="control-group">
										<label class="control-label" for="typeahead"><?= CUMPANY_NAME ?> : </label>
										<div class="controls">
											<input type="text" class="span6 typeahead" name='name_ar' value="<?= $dataCat['name_ar'] ?>" required data-required-msg="<?= REQUIRED ?>" aria-label="<?= CUMPANY_NAME ?> <?= LANGUAGE_2 ?>">
											<span class="help-inline">&nbsp;</span>
										</div>
									</div>
									<br>
									<div class="control-group">
										<div class="salesOptions" style="width:45%">

											<label class="control-label" for="typeahead"><?= CUMPANY_ACTIVITY ?> : </label>
											<div class="controls">
												<textarea class="span6 typeahead"
													style="width: 90%"
													name="activity"
													id="activity" rows="10"><?= $dataCat['activity'] ?></textarea>
											</div>
										</div>
										<div class="salesOptions" style="width:45%">
											<label class="control-label" for="typeahead"><?= CUMPANY_LOGO ?> : </label>
											<div class="controls">
												<input type="url" class="span6 typeahead" name='logo' id='logo' value="<?= $dataCat['logo'] ?>" aria-label="<?= CUMPANY_LOGO ?>">
											</div>
											<div class="controls">
												<!--                                                                <input type="hidden" value="<?= $dataCat['logo'] ?>" name="logo">-->
												<img src='<?= $dataCat['logo'] ?>' class='image'>
											</div>
										</div>

									</div>
									<div class="control-group">
										<div class="salesOptions">
											<label class="control-label" for="typeahead"><?= SMOKING ?> : </label>
											<div class="controls">
												<input type="checkbox" value="1" name="smoking" <?php if (intval($dataCat['smoking']) == 1) echo "checked='true'"; ?>>
											</div>
										</div>
										<div class="salesOptions">
											<label class="control-label" for="typeahead"><?= ENABLE_NEGTIVE ?> : </label>
											<div class="controls">
												<input type="checkbox" value="1" name="enableNegative" <?php if (intval($dataCat['enableNegative']) == 1) echo "checked='true'"; ?>>
											</div>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label"><?= WEB_SITE ?> :</label>
										<div class="controls">
											<input type="text" name='site' class="span6 typeahead" value="<?= $dataCat['site'] ?>" aria-label="<?= WEB_SITE ?>">
										</div>
									</div>

									<div class="control-group">
										<label class="control-label"><?= TAX_NO ?> :</label>
										<div class="controls">
											<input type="text" name='tax_no' class="span6 typeahead" onkeypress="return isNumber(event)" value="<?= $dataCat['tax_no'] ?>" required data-required-msg="<?= REQUIRED ?>" aria-label="<?= TAX_NO ?>">&nbsp; *
										</div>
									</div>



									<div class="control-group">
										<label class="control-label"><?= PHONE ?> :</label>
										<div class="controls">
											<input type="text" name='phone' class="span6 typeahead" onkeypress="return isNumber(event)" id='phone' maxlength="15" value="<?= $dataCat['phone'] ?>" required data-required-msg="<?= REQUIRED ?>" aria-label="<?= PHONE ?>">&nbsp; *
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="typeahead"><?= EMAIL ?> : </label>
										<div class="controls">
											<input type="email" class="span6 typeahead" name='email' id='email' value="<?= $dataCat['email'] ?>" required data-required-msg="<?= REQUIRED ?>" data-email-msg="<?= INVALID_EMAIL ?>" aria-label="<?= EMAIL ?>"> &nbsp; *
											<span class="help-inline">&nbsp;</span>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label"><?= ADDRESS ?> :</label>
										<div class="controls">
											<input type="text" class="span6 typeahead" name='address' id='address' value="<?= $dataCat['address'] ?>" aria-label="<?= ADDRESS ?>">
										</div>
									</div>

									<div class="form-actions">
										<button type="button" id='submit-settings' class="btn btn-primary"><?= SAVE ?></button>
										<button type="reset" class="btn"><?= CANCEL ?></button>
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

	<?php require_once("footer.php"); ?>
</body>

</html>