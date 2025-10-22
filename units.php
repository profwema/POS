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
	<title>WAM Tech POS - <?= UNITS ?></title>

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
						<i class="fas fa-balance-scale text-primary me-2"></i>
						<?= UNITS ?>
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
							<span class="text-primary"><?= UNITS ?></span>
						</div>
					</div>
				</div>

				<!-- Units Card -->
				<div class="card-pro">
					<div class="card-header-pro">
						<h2 class="card-title-pro">
							<i class="fas fa-edit"></i>
							تحديث الوحدات
						</h2>
					</div>
					<div class="card-body-pro">
						<form id="update-units" class="form-horizontal">
							<input type="hidden" value="unitsUpdate" name="f">

							<?php
							$query = "SELECT * FROM units WHERE storeid='$storeid'";
							$res = mysqli_query($adController->MySQL, $query);
							do {
								$id = $dataCat['id'];
								$name = $dataCat['name_' . $language];
							?>

								<div class="form-group-pro mb-3">
									<div class="row g-3">
										<input type="hidden" value='<?= $dataCat['id'] ?>' name='id[]' />
										<div class="col-md-5">
											<input type="text" class="form-input-pro" name='name_en[]' value="<?= $dataCat['name_en'] ?>" placeholder='English name'>
										</div>
										<div class="col-md-5">
											<input type="text" class="form-input-pro" name='name_ar[]' value="<?= $dataCat['name_ar'] ?>" placeholder='الاسم بالعربي'>
										</div>
									</div>
								</div>

							<?php
							} while ($dataCat = mysqli_fetch_assoc($res));
							?>

							<div class="d-flex gap-2 mt-4">
								<button type="button" id='submit-units' class="btn-pro btn-pro-primary">
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