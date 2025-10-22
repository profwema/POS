<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>WAM Tech POS - <?= ADD_NEW ?> <?= CATEGORY ?></title>

	<?php require_once("header.php"); ?>
	<link href="css/admin-pro.css" rel="stylesheet">
</head>

<body>

	<div class="admin-wrapper">
		<?php require_once("components/sidebar_pro.php"); ?>

		<div style="flex: 1; display: flex; flex-direction: column;">
			<?php require_once("components/header_pro.php"); ?>

			<main class="admin-content">

				<!-- Page Header -->
				<div class="page-header">
					<h1 class="page-title">
						<i class="fas fa-plus-circle text-success me-2"></i>
						<?= ADD_NEW ?> <?= CATEGORY ?>
					</h1>
					<div class="page-breadcrumb">
						<div class="breadcrumb-item"><i class="fas fa-home"></i><span>الرئيسية</span></div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item"><a href="categories.php"><?= CATEGORIES ?></a></div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item"><span class="text-primary"><?= ADD_NEW ?></span></div>
					</div>
				</div>

				<!-- Form Card -->
				<div class="card-pro">
					<div class="card-header-pro">
						<h2 class="card-title-pro">
							<i class="fas fa-info-circle"></i>
							معلومات التصنيف
						</h2>
					</div>
					<div class="card-body-pro">
						<form id="add-cat">
							<input type="hidden" value="addCat" name="f">

							<!-- Language 1 Section -->
							<div class="mb-4">
								<h5 class="text-primary mb-3">
									<i class="fas fa-language me-2"></i>
									<?= LANGUAGE_1 ?>
								</h5>

								<div class="row g-3">
									<div class="col-md-6">
										<div class="form-group-pro">
											<label class="form-label-pro">
												<?= CAT_NAME ?>
												<span class="text-danger">*</span>
											</label>
											<input type="text" class="form-input-pro" name='name' id='name' required>
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group-pro">
											<label class="form-label-pro"><?= IMAGE ?></label>
											<input type="file" class="form-input-pro" name="file[]" accept="image/*">
										</div>
									</div>
								</div>
							</div>

							<!-- Branch Selection -->
							<div class="form-group-pro mb-4">
								<label class="form-label-pro">
									<i class="fas fa-building me-1"></i>
									<?= BRANCH ?>
								</label>
								<select name="branch[]" class="form-input-pro select2" multiple>
									<?php
									$query = "SELECT * FROM branches ORDER BY name_en ASC";
									$res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
									while ($data = mysqli_fetch_assoc($res)) {
										$id   = $data['id'];
										$name = $data['name_' . $language];
										echo "<option value='$id'>$name</option>";
									}
									?>
								</select>
								<small class="text-muted">(<?= LEAVE_BLANK_FOR_ALL ?>)</small>
							</div>

							<!-- Checkboxes -->
							<div class="row g-3 mb-4">
								<div class="col-md-6">
									<div class="form-check-pro">
										<input type="checkbox" class="form-check-input" id="show_to_cashier" name="show_to_cashier" value="1" checked>
										<label class="form-check-label" for="show_to_cashier">
											<i class="fas fa-eye me-1"></i>
											<?= SHOW_TO_CASHIER ?>
										</label>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-check-pro">
										<input type="checkbox" class="form-check-input" id="enabled" name="enabled" value="1" checked>
										<label class="form-check-label" for="enabled">
											<i class="fas fa-toggle-on me-1"></i>
											<?= ENABLED ?>
										</label>
									</div>
								</div>
							</div>

							<!-- Divider -->
							<hr class="my-4" style="border-top: 2px solid rgba(0,0,0,0.1);">

							<!-- Language 2 Section -->
							<div class="mb-4">
								<h5 class="text-primary mb-3">
									<i class="fas fa-language me-2"></i>
									<?= LANGUAGE_2 ?>
								</h5>

								<div class="form-group-pro">
									<label class="form-label-pro"><?= CAT_NAME ?></label>
									<input type="text" class="form-input-pro" name='name_ar'>
								</div>
							</div>

							<!-- Buttons -->
							<div class="d-flex gap-2 mt-4">
								<button type="button" id='submit-cat' class="btn-pro btn-pro-success">
									<i class="fas fa-save me-1"></i>
									<?= SAVE ?>
								</button>
								<button type="reset" class="btn-pro btn-pro-outline">
									<i class="fas fa-times me-1"></i>
									<?= CANCEL ?>
								</button>
								<a href="categories.php" class="btn-pro btn-pro-outline ms-auto">
									<i class="fas fa-arrow-right me-1"></i>
									العودة للقائمة
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