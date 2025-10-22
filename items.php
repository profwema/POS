<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= LANG;
$storeid	= $_SESSION['storeid'];

if (isset($_REQUEST["catid"]))
	$_SESSION['catItems'] = $_REQUEST["catid"];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>WAM Tech POS - <?= ITEMS ?></title>

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
						<i class="fas fa-boxes text-primary me-2"></i>
						<?= ITEMS ?>
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
							<span class="text-primary"><?= ITEMS ?></span>
						</div>
					</div>
				</div>

				<!-- Items Card -->
				<div class="card-pro">
					<div class="card-header-pro">
						<div class="d-flex justify-content-between align-items-center">
							<h2 class="card-title-pro">
								<i class="fas fa-list"></i>
								قائمة المنتجات
							</h2>
							<a class="btn-pro btn-pro-success" href="add_item.php">
								<i class="fas fa-plus me-1"></i>
								إضافة منتج جديد
							</a>
						</div>
					</div>
					<div class="card-body-pro">

						<!-- Filter Section -->
						<div class="mb-4">
							<form action="<?= $pgName ?>" id='form' method="POST">
								<div class="row g-3">
									<div class="col-md-4">
										<div class="form-group-pro">
											<label class="form-label-pro">
												<i class="fas fa-filter me-1"></i>
												<?= CATEGORY ?>
											</label>
											<select onchange="this.form.submit()" class="form-input-pro select2" name="catid">
												<option value="">-- جميع التصنيفات --</option>
												<?php
												$query = "SELECT * FROM categories WHERE storeid='$storeid'";
												$res = mysqli_query($adController->MySQL, $query);
												while ($data = mysqli_fetch_assoc($res)) {
													$name	= $data["name_" . $language];
													$sel	= "";

													if (isset($_SESSION['catItems']) && $_SESSION['catItems'] == $data['id']) {
														$sel = " selected='true' ";
													}

													echo "<option value='$data[id]' $sel>$name</option>";
												}
												?>
											</select>
										</div>
									</div>
								</div>
							</form>
						</div>

						<!-- Table Section -->
						<div class="table-responsive">
							<table class="table-pro datatable">
								<thead>
									<tr>
										<th class="text-center">#</th>
										<th class="text-center"><i class="fas fa-image me-1"></i><?= IMAGE ?></th>
										<th><i class="fas fa-cube me-1"></i><?= ITEM_NAME ?></th>
										<th><i class="fas fa-tags me-1"></i><?= CATEGORY ?></th>
										<th class="text-center"><i class="fas fa-toggle-on me-1"></i><?= ENABLED ?></th>
										<th class="text-center"><i class="fas fa-barcode me-1"></i><?= BARCODE ?></th>
										<th class="text-center"><i class="fas fa-cog me-1"></i><?= EDITING ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$catSearch = '';
									if (isset($_SESSION['catItems'])) {
										if ($_SESSION['catItems'] != '') {
											$catSearch = " AND catid = " . $_SESSION['catItems'] . " ";
										}
									}

									$i = 0;
									$query = "SELECT * FROM items WHERE storeid='$storeid' $catSearch GROUP BY item_thread";
									$res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
									while ($data = mysqli_fetch_assoc($res)) {
										$i++;

										$query		= "SELECT * FROM categories WHERE id='$data[catid]' AND storeid='$storeid'";
										$resCat		= mysqli_query($adController->MySQL, $query);
										$dataCat 	= mysqli_fetch_assoc($resCat);

										$name	 	= $data["name_$language"];
										if ($name == "") {
											if ($language == "ar")
												$name	= $data['name_en'];
											else
												$name	= $data['name_ar'];
										}

										$category	= $dataCat['name_' . $language];
										$enabled	= $data["enabled"];
										$is_service	= $data["is_service"];

										if ($enabled == "1")
											$enabled = YES;
										else
											$enabled = NO;

										if ($is_service == "1")
											$is_service = YES;
										else
											$is_service = NO;

										$dirVal		= urlencode($adController->encrypt_decrypt(1, DIR_ITEM_NAME, 0));
										$idval		= urlencode($adController->encrypt_decrypt(1, $data['item_thread'], 0));
										$tableName	= urlencode($adController->encrypt_decrypt(1, 'items', 0));
										$secondIdval = urlencode($adController->encrypt_decrypt(1, $idval, 0));

										$thumb	= $data['image'];

										echo "<tr>";
										echo "<td class='text-center'><strong>$i</strong></td>";
										echo "<td class='text-center'>";
										if ($thumb != "")
											echo "<img src='$thumb' class='rounded' style='width: 50px; height: 50px; object-fit: cover;'>";
										else
											echo "<i class='fas fa-image text-muted' style='font-size: 30px;'></i>";
										echo "</td>";
										echo "<td><strong>$name</strong></td>";
										echo "<td>$category</td>";
										echo "<td class='text-center'>
											<span class='badge-pro " . ($enabled == YES ? "badge-pro-success" : "badge-pro-danger") . "'>
												$enabled
											</span>
										</td>";
										echo "<td class='text-center'>
											<a class='btn-pro btn-pro-outline btn-pro-sm' href='barcode_item.php?sd=$secondIdval' title='طباعة الباركود'>
												<i class='fas fa-barcode'></i>  
											</a>
										</td>";
										echo "<td class='text-center'>
											<div class='d-flex gap-1 justify-content-center'>
												<a class='btn-pro btn-pro-outline btn-pro-sm' href='edit_item.php?sd=$secondIdval' title='تعديل'>
													<i class='fas fa-edit'></i>  
												</a>
												<button class='btn-pro btn-pro-danger btn-pro-sm' onclick='javascript:deleteData(\"$tableName\",\"$idval\",\"$dirVal\");' title='حذف'>
													<i class='fas fa-trash'></i> 
												</button>
											</div>
										</td>";
										echo "</tr>";
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