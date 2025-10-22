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
	<title>WAM Tech POS - <?= SUPPLIERS ?></title>

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
						<?= SUPPLIERS ?>
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
							<span class="text-primary"><?= SUPPLIERS ?></span>
						</div>
					</div>
				</div>

				<!-- Suppliers Card -->
				<div class="card-pro">
					<div class="card-header-pro">
						<div class="d-flex justify-content-between align-items-center">
							<h2 class="card-title-pro">
								<i class="fas fa-list"></i>
								قائمة الموردين
							</h2>
							<a class="btn-pro btn-pro-success" href="add_supplier.php">
								<i class="fas fa-plus me-1"></i>
								إضافة مورد جديد
							</a>
						</div>
					</div>
					<div class="card-body-pro">
						<div class="table-responsive">
							<table class="table-pro datatable">
								<thead>
									<tr>
										<th><i class="fas fa-truck me-2"></i><?= SUPPLIER_NAME ?></th>
										<th><i class="fas fa-building me-2"></i><?= COM_REG ?></th>
										<th><i class="fas fa-file-invoice me-2"></i><?= TAX_NO ?></th>
										<th><i class="fas fa-envelope me-2"></i><?= EMAIL ?></th>
										<th><i class="fas fa-phone me-2"></i><?= MOBILE ?></th>
										<th class="text-center"><i class="fas fa-cog me-2"></i><?= EDITING ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$language 	= LANG;
									$storeid	= $_SESSION['storeid'];
									$query	 	= "SELECT * FROM suppliers WHERE storeid='$storeid'";
									$res 	 	= mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
									while ($data = mysqli_fetch_assoc($res)) {

										$name	 	= $data["name_$language"];
										$com_reg	= $data["com_reg"];
										$tax_no		= $data["tax_no"];
										$email		= $data["email"];
										$phone	    = $data['phone'];

										$idval		= urlencode($adController->encrypt_decrypt(1, $data['id'], 0));
										$tableName	= urlencode($adController->encrypt_decrypt(1, 'suppliers', 0));
										$secondIdval = urlencode($adController->encrypt_decrypt(1, $idval, 0));

										echo "<tr>";
										echo "<td><strong>$name</strong></td>";
										echo "<td>$com_reg</td>";
										echo "<td>$tax_no</td>";
										echo "<td>$email</td>";
										echo "<td>$phone</td>";
										echo "<td class='text-center'>
											<div class='d-flex gap-1 justify-content-center'>
												<a class='btn-pro btn-pro-outline btn-pro-sm' href='edit_supp.php?sd=$secondIdval' title='تعديل'>
													<i class='fas fa-edit'></i>  
												</a>
												<button class='btn-pro btn-pro-danger btn-pro-sm' onclick='javascript:deleteData(\"$tableName\",\"$idval\",5);' title='حذف'>
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