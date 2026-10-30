<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? 'ar';
$storeid  = intval($_SESSION['storeid'] ?? 1);
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
							<span>نقطة البيع والعمليات</span>
						</div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item">
							<span class="text-primary"><?= DELIVERY_COST ?></span>
						</div>
					</div>
				</div>

				<!-- Delivery Card -->
				<div class="card-pro">
					<div class="card-header-pro">
						<div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
							<h2 class="card-title-pro">
								<i class="fas fa-list me-1"></i>
								قائمة تكاليف التوصيل
							</h2>
							<a class="btn-pro btn-pro-success" href="delivery_cost.php">
								<i class="fas fa-plus me-1"></i>
								تحديد / تعديل تكلفة التوصيل
							</a>
						</div>
					</div>
					<div class="card-body-pro">
						<div class="table-responsive">
							<table class="table-pro datatable">
								<thead>
									<tr>
										<th style="width: 80px;" class="text-center">#</th>
										<th><i class="fas fa-building me-1"></i><?= BRANCH ?></th>
										<th style="width: 200px;"><i class="fas fa-money-bill-wave me-1"></i><?= DELIVERY_COST ?></th>
										<th class="text-center" style="width: 140px;"><i class="fas fa-cog me-1"></i><?= EDITING ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$query = "SELECT d.*, b.name_ar, b.name_en 
									          FROM delivery d
									          LEFT JOIN branches b ON b.id = d.branchid AND b.storeid = '$storeid'
									          WHERE d.storeid = '$storeid'
									          ORDER BY d.id DESC";
									$res   = mysqli_query($adController->MySQL, $query);

									if ($res) {
										while ($data = mysqli_fetch_assoc($res)) {
											$branchName = !empty($data['name_ar']) ? $data['name_ar'] : ($data['name_en'] ?? 'كافة الفروع');
											$idval      = urlencode($adController->encrypt_decrypt(1, $data['id'], 0));
											$tableName  = urlencode($adController->encrypt_decrypt(1, 'delivery', 0));
											$amount     = number_format(floatval($data['amount']), 2);

											echo "<tr>";
											echo "<td class='text-center'><strong>#" . intval($data['id']) . "</strong></td>";
											echo "<td><strong>" . htmlspecialchars($branchName) . "</strong></td>";
											echo "<td style='text-align:left; direction:ltr; font-weight:bold; color:#059669;'>" . $amount . "</td>";
											echo "<td class='text-center'>
													<div class='d-flex gap-1 justify-content-center'>
														<a class='btn-pro btn-pro-outline btn-pro-sm' href='delivery_cost.php?d=$idval' title='" . EDIT . "'>
															<i class='fas fa-edit'></i>
														</a>
														<button class='btn-pro btn-pro-danger btn-pro-sm' onclick='deleteData(\"$tableName\",\"$idval\",\"\");' title='" . DELETE . "'>
															<i class='fas fa-trash'></i>
														</button>
													</div>
												  </td>";
											echo "</tr>";
										}
									}
									?>
								</tbody>
							</table>
						</div>
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
