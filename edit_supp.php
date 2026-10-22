<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= $_SESSION['lang'];
$storeid	= $_SESSION['storeid'];

$idval		= $adController->encrypt_decrypt(2, urldecode($_REQUEST['sd']), 0);
$idval 		= $adController->encrypt_decrypt(2, $idval, 0);
$idval2		= $adController->encrypt_decrypt(1, $_REQUEST['sd'], 0);

$query		= "SELECT * FROM suppliers WHERE storeid='$storeid' AND id='$idval'";
$res		= mysqli_query($adController->MySQL, $query);
$dataCat	= mysqli_fetch_assoc($res);
$suppName   = htmlspecialchars(!empty($dataCat['name_ar']) ? $dataCat['name_ar'] : ($dataCat['name_en'] ?? ''));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>WAM Tech POS - <?= UPDATE_SUPPLIER ?></title>
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
						<i class="fas fa-truck text-warning me-2"></i>
						<?= UPDATE_SUPPLIER ?>
					</h1>
					<div class="page-breadcrumb">
						<div class="breadcrumb-item"><i class="fas fa-home"></i><span>الرئيسية</span></div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item"><a href="suppliers.php"><?= SUPPLIERS ?></a></div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item"><span class="text-primary"><?= EDIT ?></span></div>
					</div>
				</div>
				<div class="card-pro">
					<div class="card-header-pro">
						<h2 class="card-title-pro"><i class="fas fa-info-circle"></i> تعديل بيانات المورد</h2>
					</div>
					<div class="card-body-pro">
						<form id="add-sup">
							<input type="hidden" value="<?= $idval2 ?>" name="nd" id="nd">
							<input type="hidden" value="updateSupplier" name="f">
							
							<div class="mb-4">
								<div class="form-group-pro">
									<label class="form-label-pro"><i class="fas fa-truck me-1"></i><?= SUPPLIER_NAME ?> <span class="text-danger">*</span></label>
									<input type="text" class="form-input-pro" name='name_ar' id='name' value="<?= $suppName ?>" required>
								</div>
							</div>

							<hr class="my-4">
							<div class="row g-3">
								<div class="col-md-6">
									<div class="form-group-pro">
										<label class="form-label-pro"><i class="fas fa-building me-1"></i><?= COM_REG ?></label>
										<input type="text" class="form-input-pro" name='com_reg' value="<?= htmlspecialchars($dataCat['com_reg'] ?? '') ?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group-pro">
										<label class="form-label-pro"><i class="fas fa-file-invoice me-1"></i><?= TAX_NO ?></label>
										<input type="text" class="form-input-pro" name='tax_no' value="<?= htmlspecialchars($dataCat['tax_no'] ?? '') ?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group-pro">
										<label class="form-label-pro"><i class="fas fa-phone me-1"></i><?= PHONE ?></label>
										<input type="text" class="form-input-pro" name='phone' id='phone' onkeypress="return isNumber(event)" maxlength="15" value="<?= htmlspecialchars($dataCat['phone'] ?? '') ?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group-pro">
										<label class="form-label-pro"><i class="fas fa-envelope me-1"></i><?= EMAIL ?></label>
										<input type="email" class="form-input-pro" name='email' id='email' value="<?= htmlspecialchars($dataCat['email'] ?? '') ?>">
									</div>
								</div>
								<div class="col-12">
									<div class="form-group-pro">
										<label class="form-label-pro"><i class="fas fa-map-marker-alt me-1"></i><?= ADDRESS ?></label>
										<input type="text" class="form-input-pro" name='address' id='address' value="<?= htmlspecialchars($dataCat['address'] ?? '') ?>">
									</div>
								</div>
							</div>
							<div class="d-flex gap-2 mt-4">
								<button type="button" id='submit-supplier' class="btn-pro btn-pro-success">
									<i class="fas fa-save me-1"></i><?= SAVE ?>
								</button>
								<button type="reset" class="btn-pro btn-pro-outline">
									<i class="fas fa-times me-1"></i><?= CANCEL ?>
								</button>
								<a href="suppliers.php" class="btn-pro btn-pro-outline ms-auto">
									<i class="fas fa-arrow-right me-1"></i>العودة للقائمة
								</a>
							</div>
							<p class='error-red mt-3'>&nbsp;</p>
						</form>
					</div>
				</div>
			</main>
		</div>
	</div>
	<?php require_once("footer.php"); ?>
</body>
</html>
