<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language = $_SESSION['lang'];

$idval		= $adController->encrypt_decrypt(2, urldecode($_REQUEST['sd']), 0);
$idval 		= $adController->encrypt_decrypt(2, $idval, 0);
$idval2		= $adController->encrypt_decrypt(1, $_REQUEST['sd'], 0);

$query		= "SELECT * FROM branches WHERE id='$idval'";
$res		= mysqli_query($adController->MySQL, $query);
$dataBranch	= mysqli_fetch_assoc($res);

$branchName   = htmlspecialchars(!empty($dataBranch['name_ar']) ? $dataBranch['name_ar'] : ($dataBranch['name_en'] ?? ''));
$branchStreet = htmlspecialchars(!empty($dataBranch['street_ar']) ? $dataBranch['street_ar'] : ($dataBranch['street_en'] ?? ''));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>WAM Tech POS - <?= EDIT ?> <?= BRANCH ?></title>
	<?php require_once("header.php"); ?>
	<link href="css/admin-pro.css" rel="stylesheet">
</head>

<body>
	<div class="admin-wrapper">
		<?php require_once("components/sidebar_pro.php"); ?>
		<div style="flex: 1; display: flex; flex-direction: column;">
			<?php require_once("components/header_pro.php"); ?>
			<main class="admin-content">
				<div class="page-header">
					<h1 class="page-title">
						<i class="fas fa-code-branch text-warning me-2"></i>
						<?= EDIT ?> <?= BRANCH ?>
					</h1>
					<div class="page-breadcrumb">
						<div class="breadcrumb-item"><i class="fas fa-home"></i><span>الرئيسية</span></div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item"><a href="branches.php"><?= BRANCHES ?></a></div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item"><span class="text-primary"><?= EDIT ?></span></div>
					</div>
				</div>
				<div class="card-pro">
					<div class="card-header-pro">
						<h2 class="card-title-pro"><i class="fas fa-info-circle"></i> تعديل بيانات الفرع</h2>
					</div>
					<div class="card-body-pro">
						<form id="add-branch">
							<input type="hidden" value="<?= $idval2 ?>" name="nd">
							<input type="hidden" value="updateBranch" name="f">

							<div class="mb-4">
								<div class="row g-3">
									<div class="col-md-6">
										<div class="form-group-pro">
											<label class="form-label-pro"><?= BRANCH_NAME ?> <span class="text-danger">*</span></label>
											<input type="text" class="form-input-pro" name='name_ar' id='name' value="<?= $branchName ?>" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group-pro">
											<label class="form-label-pro"><?= STREET ?> <span class="text-danger">*</span></label>
											<input type="text" class="form-input-pro" name='street_ar' id="street" value="<?= $branchStreet ?>" required>
										</div>
									</div>
								</div>
							</div>

							<div class="row g-3 mb-4">
								<div class="col-md-4">
									<div class="form-group-pro">
										<label class="form-label-pro"><?= COUNTRY ?> <span class="text-danger">*</span></label>
										<select name="country" class="form-input-pro select2" id='country' required>
											<option value="">--<?= SELECT_COUNTRY ?>--</option>
											<?php
											$query = "SELECT * FROM us_country_master ORDER BY cou_name_en ASC";
											$res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
											while ($data = mysqli_fetch_assoc($res)) {
												$id = $data['cou_id'];
												$name = $data['cou_name_' . $language];
												$selected = ($dataBranch['country'] == $data['cou_id']) ? "selected='true'" : "";
												echo "<option value='$id' $selected>$name</option>";
											}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group-pro">
										<label class="form-label-pro"><?= STATE ?> <span class="text-danger">*</span></label>
										<div id="state-control">
											<select name="state" id='state' class="form-input-pro select2" required>
												<option value="">--<?= SELECT_STATE ?>--</option>
												<?php
												if (!empty($dataBranch['country'])) {
													$query = "SELECT * FROM us_state_master WHERE cou_id='" . $dataBranch['country'] . "' ORDER BY st_name_en ASC";
													$res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
													while ($data = mysqli_fetch_assoc($res)) {
														$id = $data['st_id'];
														$name = $data['st_name_' . $language];
														$selected = ($dataBranch['state'] == $data['st_id']) ? "selected='true'" : "";
														echo "<option value='$id' $selected>$name</option>";
													}
												}
												?>
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group-pro">
										<label class="form-label-pro"><?= CITY ?> <span class="text-danger">*</span></label>
										<select name="city" id='city' class="form-input-pro select2" required>
											<option value="">--<?= SELECT_CITY ?>--</option>
											<?php
											if (!empty($dataBranch['state'])) {
												$query = "SELECT * FROM us_city_master WHERE st_id='" . $dataBranch['state'] . "' ORDER BY city_name_en ASC";
												$res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
												while ($data = mysqli_fetch_assoc($res)) {
													$id = $data['city_id'];
													$name = $data['city_name_' . $language];
													$selected = ($dataBranch['city'] == $data['city_id']) ? "selected='true'" : "";
													echo "<option value='$id' $selected>$name</option>";
												}
											}
											?>
										</select>
									</div>
								</div>
							</div>

							<div class="row g-3 mb-4">
								<div class="col-md-6">
									<div class="form-group-pro">
										<label class="form-label-pro"><i class="fas fa-phone me-1"></i><?= PHONE ?> <span class="text-danger">*</span></label>
										<input type="text" class="form-input-pro" name='phone' id='phone' onkeypress="return isNumber(event)" maxlength="15" value="<?= htmlspecialchars($dataBranch['phone'] ?? '') ?>" required>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group-pro">
										<label class="form-label-pro"><i class="fas fa-fax me-1"></i><?= FAX ?></label>
										<input type="text" class="form-input-pro" name='fax' onkeypress="return isNumber(event)" maxlength="15" value="<?= htmlspecialchars($dataBranch['fax'] ?? '') ?>">
									</div>
								</div>
							</div>

							<hr class="my-4">
							<div class="row g-3 mb-4">
								<div class="col-md-6">
									<div class="form-group-pro">
										<label class="form-label-pro"><?= COMMERCIAL_LICENCE_NUM ?></label>
										<input type="text" class="form-input-pro" name='crnum' value="<?= htmlspecialchars($dataBranch['crnum'] ?? '') ?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group-pro">
										<label class="form-label-pro"><?= TAX_NUMBER ?></label>
										<input type="text" class="form-input-pro" name='vatnumber' value="<?= htmlspecialchars($dataBranch['vatnumber'] ?? '') ?>">
									</div>
								</div>
							</div>

							<div class="d-flex gap-2 mt-4">
								<button type="button" id='submit-branch' class="btn-pro btn-pro-success">
									<i class="fas fa-save me-1"></i><?= SAVE ?>
								</button>
								<button type="reset" class="btn-pro btn-pro-outline">
									<i class="fas fa-times me-1"></i><?= CANCEL ?>
								</button>
								<a href="branches.php" class="btn-pro btn-pro-outline ms-auto">
									<i class="fas fa-arrow-right me-1"></i>العودة للقائمة
								</a>
							</div>
							<p class='error-red mt-3'>&nbsp;</p>
						</form>
					</div>
				</div>
				<?php require_once("components/footer_pro.php"); ?>
			</main>
		</div>
	</div>
	<?php require_once("include.php"); ?>
	<script src="js/admin-pro.js"></script>
</body>
</html>
