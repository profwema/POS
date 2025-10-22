<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];

$idval		= $adController->encrypt_decrypt(2, urldecode($_REQUEST['sd']), 0);
$idval 		= $adController->encrypt_decrypt(2, $idval, 0);
$idval2		= $adController->encrypt_decrypt(1, $_REQUEST['sd'], 0);

$query	 	= "SELECT * FROM categories WHERE storeid='$storeid' AND id='$idval'";
$res		= mysqli_query($adController->MySQL, $query);
$dataCat	= mysqli_fetch_assoc($res);
$branchArray	= explode(",", $dataCat['store_branch']);

$query 		= "SELECT * FROM images WHERE foreign_id='$dataCat[id]' AND `table`='categories'";
$resImage	= mysqli_query($adController->MySQL, $query);
$dataImage	= mysqli_fetch_assoc($resImage);

$img		= $adController->getDirectoryOnlyPath(DIR_CAT_NAME);
$thumb		= $img . $dataImage['thumb'];

$enabled	= $dataCat['enabled'];
$show_to_cashier = $dataCat['showtocashier'];
if ($show_to_cashier == "1")
	$show_to_cashier = "checked='true'";
else
	$show_to_cashier = "";

if ($enabled == "1")
	$enabled = "checked='true'";
else
	$enabled = "";
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>WAM Tech POS - <?= EDIT ?> <?= CATEGORY ?></title>
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
						<i class="fas fa-edit text-warning me-2"></i><?= EDIT ?> <?= CATEGORY ?>
					</h1>
					<div class="page-breadcrumb">
						<div class="breadcrumb-item"><i class="fas fa-home"></i><span>الرئيسية</span></div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item"><a href="categories.php"><?= CATEGORIES ?></a></div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item"><span class="text-primary"><?= EDIT ?></span></div>
					</div>
				</div>
				<div class="card-pro">
					<div class="card-header-pro">
						<h2 class="card-title-pro"><i class="fas fa-info-circle"></i> تعديل معلومات التصنيف</h2>
					</div>
					<div class="card-body-pro">
						<form id="add-cat">
							<input type="hidden" value="<?= $idval2 ?>" name="nd">
							<input type="hidden" value="updateCat" name="f">
							<div class="mb-4">
								<h5 class="text-primary mb-3"><i class="fas fa-language me-2"></i><?= LANGUAGE_1 ?></h5>
								<div class="row g-3">
									<div class="col-md-6">
										<div class="form-group-pro">
											<label class="form-label-pro"><?= CAT_NAME ?> <span class="text-danger">*</span></label>
											<input type="text" class="form-input-pro" name='name' id='name' value="<?= $dataCat['name_en'] ?>" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group-pro">
											<label class="form-label-pro"><?= IMAGE ?></label>
											<input type="file" class="form-input-pro" name="file[]" accept="image/*">
											<?php if ($thumb != ""): ?>
												<div class="mt-2">
													<img src='<?= $thumb ?>' class='rounded' style='width: 100px; height: 100px; object-fit: cover;'>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group-pro mb-4">
								<label class="form-label-pro"><i class="fas fa-building me-1"></i><?= BRANCH ?></label>
								<select name="branch[]" class="form-input-pro select2" multiple>
									<?php
									$query = "SELECT * FROM branches WHERE storeid='$storeid' ORDER BY name_en ASC";
									$res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
									while ($data = mysqli_fetch_assoc($res)) {
										$id   = $data['id'];
										$name = $data['name_' . $language];
										$selected = in_array($data['id'], $branchArray) ? " selected='true' " : "";
										echo "<option value='$id' $selected>$name</option>";
									}
									?>
								</select>
								<small class="text-muted">(<?= LEAVE_BLANK_FOR_ALL ?>)</small>
							</div>
							<div class="row g-3 mb-4">
								<div class="col-md-6">
									<div class="form-check-pro">
										<input type="checkbox" class="form-check-input" id="show_to_cashier" name="show_to_cashier" value="1" <?= $show_to_cashier ?>>
										<label class="form-check-label" for="show_to_cashier">
											<i class="fas fa-eye me-1"></i><?= SHOW_TO_CASHIER ?>
										</label>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-check-pro">
										<input type="checkbox" class="form-check-input" id="enabled" name="enabled" value="1" <?= $enabled ?>>
										<label class="form-check-label" for="enabled">
											<i class="fas fa-toggle-on me-1"></i><?= ENABLED ?>
										</label>
									</div>
								</div>
							</div>
							<hr class="my-4">
							<div class="mb-4">
								<h5 class="text-primary mb-3"><i class="fas fa-language me-2"></i><?= LANGUAGE_2 ?></h5>
								<div class="form-group-pro">
									<label class="form-label-pro"><?= CAT_NAME ?></label>
									<input type="text" class="form-input-pro" name='name_ar' value="<?= $dataCat['name_ar'] ?>">
								</div>
							</div>
							<div class="d-flex gap-2 mt-4">
								<button type="button" id='submit-cat' class="btn-pro btn-pro-warning">
									<i class="fas fa-save me-1"></i><?= UPDATE ?>
								</button>
								<a href="categories.php" class="btn-pro btn-pro-outline ms-auto">
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