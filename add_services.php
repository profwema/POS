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
	<title>WAM Tech POS - <?= ADD_SERVICE ?></title>
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
						<i class="fas fa-concierge-bell text-success me-2"></i><?= ADD_SERVICE ?>
					</h1>
					<div class="page-breadcrumb">
						<div class="breadcrumb-item"><i class="fas fa-home"></i><span>الرئيسية</span></div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item"><a href="services.php"><?= SERVICES ?></a></div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item"><span class="text-primary"><?= ADD_NEW ?></span></div>
					</div>
				</div>
				<div class="card-pro">
					<div class="card-header-pro">
						<h2 class="card-title-pro"><i class="fas fa-info-circle"></i> معلومات الخدمة</h2>
					</div>
					<div class="card-body-pro">
						<form id="add-service">
							<?= csrf_input() ?>
							<input type="hidden" value="addService" name="f">
							
							<div class="mb-4">
								<div class="form-group-pro">
									<label class="form-label-pro"><i class="fas fa-concierge-bell me-1"></i><?= SERVICE ?> <span class="text-danger">*</span></label>
									<input type="text" class="form-input-pro" name='name_ar' id='name' placeholder="أدخل اسم الخدمة" required>
								</div>
							</div>

							<div class="d-flex gap-2 mt-4">
								<button type="button" id='submit-service' class="btn-pro btn-pro-success">
									<i class="fas fa-save me-1"></i><?= SAVE ?>
								</button>
								<button type="reset" class="btn-pro btn-pro-outline">
									<i class="fas fa-times me-1"></i><?= CANCEL ?>
								</button>
								<a href="services.php" class="btn-pro btn-pro-outline ms-auto">
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
