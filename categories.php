<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>WAM Tech POS - <?= CATEGORIES ?></title>

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
						<i class="fas fa-tags text-primary me-2"></i>
						<?= CATEGORIES ?>
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
							<span class="text-primary"><?= CATEGORIES ?></span>
						</div>
					</div>
				</div>

				<!-- Categories Card -->
				<div class="card-pro">
					<div class="card-header-pro">
						<div class="d-flex justify-content-between align-items-center">
							<h2 class="card-title-pro">
								<i class="fas fa-list"></i>
								قائمة التصنيفات
							</h2>
							<a class="btn-pro btn-pro-success" href="add_cat.php">
								<i class="fas fa-plus me-1"></i>
								إضافة تصنيف جديد
							</a>
						</div>
					</div>
					<div class="card-body-pro">
						<div class="table-responsive">
							<table class="table-pro datatable">
								<thead>
									<tr>
										<th class="text-center"><i class="fas fa-image me-1"></i><?= IMAGE ?></th>
										<th><i class="fas fa-tags me-1"></i><?= CAT_NAME ?></th>
										<th><i class="fas fa-building me-1"></i><?= IN_BRANCH ?></th>
										<th class="text-center"><i class="fas fa-boxes me-1"></i><?= TOTAL_ITEMS ?></th>
										<th class="text-center"><i class="fas fa-eye me-1"></i><?= SHOW_TO_CASHIER ?></th>
										<th class="text-center"><i class="fas fa-toggle-on me-1"></i><?= ENABLED ?></th>
										<th class="text-center"><i class="fas fa-cog me-1"></i><?= EDITING ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$language 	= LANG;
									$storeid	= $_SESSION['storeid'];
									$query	 	= "SELECT * FROM categories";

									if (!($res = mysqli_query($adController->MySQL, $query)))
										echo mysqli_error($adController->MySQL);
									while ($data = mysqli_fetch_assoc($res)) {
										$arrayBranch = array();
										if ($data['store_branch'] != "")
											$query = "SELECT * FROM branches WHERE id IN ($data[store_branch])";
										$resBranch = mysqli_query($adController->MySQL, $query);
										while ($dataBranch = mysqli_fetch_assoc($resBranch)) {
											$idval = urlencode($adController->encrypt_decrypt(1, $dataBranch['id'], 0));
											$secondIdval = urlencode($adController->encrypt_decrypt(1, $idval, 0));
											$arrayBranch[count($arrayBranch)] = "<a href='edit_branch.php?sd=$secondIdval' target='_blank'>" . $dataBranch['name_' . $language] . "</a>";
										}

										$items 		= 0;
										$name	 	= $data["name_$language"];
										$totalItems	= $data["totalitems"];
										$branchName	= implode("<br>", $arrayBranch);
										$enabled	= $data["enabled"];
										$showtocashier = $data["showtocashier"];

										if ($enabled == "1")
											$enabled = YES;
										else
											$enabled = NO;

										if ($showtocashier == "1")
											$showtocashier = YES;
										else
											$showtocashier = NO;

										$dirVal		= urlencode($adController->encrypt_decrypt(1, DIR_CAT_NAME, 0));
										$idval		= urlencode($adController->encrypt_decrypt(1, $data['id'], 0));
										$tableName	= urlencode($adController->encrypt_decrypt(1, 'categories', 0));
										$secondIdval = urlencode($adController->encrypt_decrypt(1, $idval, 0));

										$query 		= "SELECT * FROM images WHERE foreign_id='$data[id]' AND `table`='categories'";
										$resImage	= mysqli_query($adController->MySQL, $query);
										$dataImage	= mysqli_fetch_assoc($resImage);
										$img		= $adController->getDirectoryOnlyPath(DIR_CAT_NAME);
										$thumb		= $img . $dataImage['thumb'];
										if ($dataImage['thumb'] == "")
											$thumb 	= NO_IMAGE;

										echo "<tr>";
										echo "<td class='text-center'>";
										if ($thumb != NO_IMAGE)
											echo "<img src='$thumb' class='rounded' style='width: 50px; height: 50px; object-fit: cover;'>";
										else
											echo "<i class='fas fa-image text-muted' style='font-size: 30px;'></i>";
										echo "</td>";
										echo "<td><strong>$name</strong></td>";
										echo "<td>$branchName</td>";
										echo "<td class='text-center'><strong>$items</strong></td>";
										echo "<td class='text-center'>
											<span class='badge-pro " . ($showtocashier == YES ? "badge-pro-success" : "badge-pro-danger") . "'>
												$showtocashier
											</span>
										</td>";
										echo "<td class='text-center'>
											<span class='badge-pro " . ($enabled == YES ? "badge-pro-success" : "badge-pro-danger") . "'>
												$enabled
											</span>
										</td>";
										echo "<td class='text-center'>
											<div class='d-flex gap-1 justify-content-center'>
												<a class='btn-pro btn-pro-outline btn-pro-sm' href='edit_cat.php?sd=$secondIdval' title='تعديل'>
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