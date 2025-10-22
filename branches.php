<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>WAM Tech POS - <?= BRANCHES ?></title>

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
						<i class="fas fa-code-branch text-primary me-2"></i>
						<?= BRANCHES ?>
					</h1>
					<div class="page-breadcrumb">
						<div class="breadcrumb-item"><i class="fas fa-home"></i><span>الرئيسية</span></div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item"><span><?= FILES ?></span></div>
						<span class="breadcrumb-separator">/</span>
						<div class="breadcrumb-item"><span class="text-primary"><?= BRANCHES ?></span></div>
					</div>
				</div>

				<div class="card-pro">
					<div class="card-header-pro">
						<div class="d-flex justify-content-between align-items-center">
							<h2 class="card-title-pro"><i class="fas fa-list"></i>قائمة الفروع</h2>
							<a class="btn-pro btn-pro-success" href="add_branch.php">
								<i class="fas fa-plus me-1"></i>إضافة فرع جديد
							</a>
						</div>
					</div>
					<div class="card-body-pro">
						<div class="table-responsive">
							<table class="table-pro datatable">
								<thead>
									<tr>
										<th><i class="fas fa-building me-1"></i><?= BRANCH_NAME ?></th>
										<th><i class="fas fa-road me-1"></i><?= STREET ?></th>
										<th><i class="fas fa-city me-1"></i><?= CITY ?></th>
										<th><?= STATE ?></th>
										<th><?= COUNTRY ?></th>
										<th><i class="fas fa-phone me-1"></i><?= PHONE ?></th>
										<th><i class="fas fa-fax me-1"></i><?= FAX ?></th>
										<th class="text-center"><i class="fas fa-cog me-1"></i><?= EDITING ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$language = LANG;
									$storeid = $_SESSION['storeid'];
									$query = "SELECT * FROM branches WHERE storeid='$storeid'";
									$res = mysqli_query($adController->MySQL, $query);
									while ($data = mysqli_fetch_assoc($res)) {
										$branchName = $data["name_$language"];
										$street = $data["street_$language"];
										$city = $data["city"];
										$state = $data["stat"];
										$country = $data["cou"];
										$phone = $data["phone"];
										$fax = $data['fax'];

										$idval = urlencode($adController->encrypt_decrypt(1, $data['id'], 0));
										$tableName = urlencode($adController->encrypt_decrypt(1, 'branches', 0));
										$secondIdval = urlencode($adController->encrypt_decrypt(1, $idval, 0));

										echo "<tr>";
										echo "<td><strong>$branchName</strong></td>";
										echo "<td>$street</td>";
										echo "<td>$city</td>";
										echo "<td>$state</td>";
										echo "<td>$country</td>";
										echo "<td>$phone</td>";
										echo "<td>$fax</td>";
										echo "<td class='text-center'>
											<div class='d-flex gap-1 justify-content-center'>
												<a class='btn-pro btn-pro-outline btn-pro-sm' href='edit_branch.php?sd=$secondIdval'>
													<i class='fas fa-edit'></i>  
												</a>
												<button class='btn-pro btn-pro-danger btn-pro-sm' onclick='javascript:deleteData(\"$tableName\",\"$idval\",\"$tableName\");'>
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

				<?php require_once("components/footer_pro.php"); ?>
			</main>
		</div>
	</div>

	<?php require_once("include.php"); ?>
	<script src="js/admin-pro.js"></script>
</body>

</html>