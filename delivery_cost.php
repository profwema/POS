<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];

$idval		= $adController->encrypt_decrypt(2, urldecode($_REQUEST['d']), 0);

$query	 	= "SELECT * FROM delivery WHERE storeid='$storeid'";
$res		= mysqli_query($adController->MySQL, $query);
$dataDiscount	= mysqli_fetch_assoc($res);
$discount	= $dataCat['amount'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>WAM Tech POS - <?= DELIVERY_COST ?></title>

	<?php require_once("header.php"); ?>
	<link href="css/admin-pro.css" rel="stylesheet">
</head>

<body>

	<div class="admin-wrapper">

		<!-- Professional Sidebar -->
		<?php require_once("components/sidebar_pro.php"); ?>

		<!-- Main Content Wrapper -->
		<div style="flex: 1; display: flex; flex-direction: column;">

			<!-- Professional Navbar -->
			<?php require_once("components/header_pro.php"); ?>

			<!-- Main Content -->
			<main class="admin-content">

				<!-- Page Header -->
				<div class="page-header">
					<h1 class="page-title">
						<i class="fas fa-truck text-primary me-2"></i>
						<?= DELIVERY_COST ?>
					</h1>
					<div class="page-breadcrumb">
						<div class="breadcrumb-item">
							<i class="fas fa-home"></i>
							<span>الرئيسية</span>
						</div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item">
							<span><?= FILES ?></span>
						</div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item">
							<span class="text-primary"><?= DELIVERY_COST ?></span>
						</div>
					</div>
				</div>

				<!-- Delivery Cost Card -->
				<div class="card-pro" style="max-width: 600px;">
					<div class="card-header-pro">
						<h2 class="card-title-pro">
							<i class="fas fa-dollar-sign"></i>
							تحديث تكلفة التوصيل
						</h2>
					</div>
					<div class="card-body-pro">
						<form id="update-discount">
							<input type="hidden" value="discountAdd" name="f">

							<div class="form-group-pro">
								<label class="form-label-pro">
									<i class="fas fa-money-bill-wave me-1"></i>
									<?= DELIVERY_COST ?>
									<span class="text-danger">*</span>
								</label>
								<input type="text" class="form-input-pro float-val" maxlength="4" name='amount' id='amount' value="<?= $dataDiscount['amount'] ?>" placeholder="أدخل تكلفة التوصيل">
								<small class="text-muted">* حقل مطلوب</small>
							</div>

							<div class="d-flex gap-2 mt-4">
								<button type="button" id='submit-delivery' class="btn-pro btn-pro-primary">
									<i class="fas fa-save me-1"></i>
									<?= SAVE ?>
								</button>
								<button type="reset" class="btn-pro btn-pro-outline">
									<i class="fas fa-times me-1"></i>
									<?= CANCEL ?>
								</button>
							</div>

							<p class='error-red mt-3'>&nbsp;</p>

						</form>

					</div>
				</div>

				<!-- Professional Footer -->
				<?php require_once("components/footer_pro.php"); ?>

			</main>

		</div>

	</div>

	<!-- Scripts -->
	<?php require_once("include.php"); ?>
	<script src="js/admin-pro.js"></script>

</body>

</html>