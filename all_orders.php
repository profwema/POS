<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid  = intval($_SESSION['storeid'] ?? 1);
$language = $_SESSION['lang'] ?? 'ar';
$pgName   = basename($_SERVER['PHP_SELF']);

// Filter inputs with safe sanitization
$orderid  = trim($_REQUEST['o'] ?? '');
$catid    = intval($_REQUEST['catid'] ?? 0);
$itemId   = intval($_REQUEST['iid'] ?? 0);
$branchid = intval($_REQUEST['br'] ?? 0);
$empId    = intval($_REQUEST['ei'] ?? 0);

$fDate    = trim($_REQUEST['from_date'] ?? date('Y-m-d'));
$tDate    = trim($_REQUEST['to_date'] ?? date('Y-m-d'));
$fTime    = isset($_REQUEST['from_time']) ? sprintf("%02d", intval($_REQUEST['from_time'])) : "00";
$tTime    = isset($_REQUEST['to_time']) ? sprintf("%02d", intval($_REQUEST['to_time'])) : "23";

$fromDateTime = "{$fDate} {$fTime}:00:00";
$toDateTime   = "{$tDate} {$tTime}:59:59";

if (strtotime($toDateTime) < strtotime($fromDateTime)) {
	$toDateTime = "{$fDate} 23:59:59";
	$tDate = $fDate;
	$tTime = "23";
}

$conditionArr = ["o.storeid = '$storeid'"];
$conditionArr[] = "o.specific_date >= '$fromDateTime'";
$conditionArr[] = "o.specific_date <= '$toDateTime'";

if ($orderid !== '') {
	$escOrder = mysqli_real_escape_string($adController->MySQL, $orderid);
	$conditionArr[] = "(o.hasvalue = '$escOrder' OR o.invoice_number = '$escOrder')";
}
if ($itemId > 0) {
	$conditionArr[] = "o.itemid = '$itemId'";
}
if ($catid > 0) {
	$conditionArr[] = "i.catid = '$catid'";
}
if ($branchid > 0) {
	$conditionArr[] = "o.branch = '$branchid'";
}
if ($empId > 0) {
	$conditionArr[] = "o.userid = '$empId'";
}

$whereClause = implode(" AND ", $conditionArr);

// Aggregated orders query
$query = "SELECT 
            o.id,
            o.dated,
            o.specific_date,
            o.hasvalue,
            o.invoice_number,
            o.qty,
            o.cost,
            o.discount,
            o.vat,
            o.payment_type,
            b.name_ar AS branch_ar,
            b.name_en AS branch_en,
            i.name_ar AS item_ar,
            i.name_en AS item_en,
            c.name_ar AS cat_ar,
            c.name_en AS cat_en,
            e.name_ar AS emp_ar,
            e.name_en AS emp_en
          FROM orders o
          LEFT JOIN branches b ON b.id = o.branch AND b.storeid = '$storeid'
          LEFT JOIN items i ON i.id = o.itemid AND i.storeid = '$storeid'
          LEFT JOIN categories c ON c.id = i.catid AND c.storeid = '$storeid'
          LEFT JOIN employees e ON e.id = o.userid AND e.storeid = '$storeid'
          WHERE $whereClause
          ORDER BY o.specific_date DESC, o.id DESC
          LIMIT 2500";

$res = mysqli_query($adController->MySQL, $query);

$ordersData   = [];
$finalTotal   = 0.0;
$cashTotal    = 0.0;
$cardTotal    = 0.0;
$totalQty     = 0.0;
$totalCount   = 0;

if ($res) {
	while ($row = mysqli_fetch_assoc($res)) {
		$qty       = floatval($row['qty']);
		$cost      = floatval($row['cost']);
		$discount  = floatval($row['discount']);
		$vatRate   = floatval($row['vat']);

		$subtotal  = ($qty * $cost) - $discount;
		$vatAmount = ($vatRate > 0) ? (($subtotal / 100) * $vatRate) : 0;
		$lineTotal = $subtotal + $vatAmount;

		$finalTotal += $lineTotal;
		$totalQty   += $qty;
		$totalCount++;

		$pType = strval($row['payment_type'] ?? '');
		if ($pType === '1') {
			$cashTotal += $lineTotal;
		} elseif ($pType === '2' || $pType === '3') {
			$cardTotal += $lineTotal;
		}

		$branchName = !empty($row['branch_ar']) ? $row['branch_ar'] : ($row['branch_en'] ?? '---');
		$itemName   = !empty($row['item_ar']) ? $row['item_ar'] : ($row['item_en'] ?? '---');
		$catName    = !empty($row['cat_ar']) ? $row['cat_ar'] : ($row['cat_en'] ?? '---');
		$empName    = !empty($row['emp_ar']) ? $row['emp_ar'] : ($row['emp_en'] ?? '---');
		$orderNum   = !empty($row['hasvalue']) ? $row['hasvalue'] : (!empty($row['invoice_number']) ? $row['invoice_number'] : $row['id']);
		$dateTime   = !empty($row['specific_date']) ? date("Y-m-d H:i", strtotime($row['specific_date'])) : date("Y-m-d H:i", strtotime($row['dated']));

		$ordersData[] = [
			'id'          => $row['id'],
			'datetime'    => $dateTime,
			'branch'      => $branchName,
			'order_num'   => $orderNum,
			'category'    => $catName,
			'item'        => $itemName,
			'employee'    => $empName,
			'qty'         => $qty,
			'price'       => $cost,
			'discount'    => $discount,
			'total'       => $lineTotal,
			'payment_type'=> ($pType === '1' ? 'نقداً' : ($pType === '2' ? 'شبكة / مدى' : ($pType === '3' ? 'فيزا / بطاقة' : 'أخرى')))
		];
	}
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>WAM Tech POS - سجل كافة الطلبات</title>

	<?php require_once("header.php"); ?>
	<link href="css/admin-pro.css" rel="stylesheet">
	<style>
		@media print {
			.admin-sidebar, .admin-navbar, .page-header, .filter-card, .btn-pro, .dataTables_filter, .dataTables_length, .dataTables_paginate, .dataTables_info {
				display: none !important;
			}
			.admin-content {
				margin: 0 !important;
				padding: 0 !important;
			}
			.card-pro {
				box-shadow: none !important;
				border: none !important;
			}
		}
	</style>
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
						<i class="fas fa-shopping-basket text-primary me-2"></i>
						سجل كافة الطلبات (All Orders)
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
							<span class="text-primary">سجل كافة الطلبات</span>
						</div>
					</div>
				</div>

				<!-- Stats KPI Cards -->
				<div class="row g-3 mb-4">
					<div class="col-12 col-sm-6 col-xl-3">
						<div class="card-pro p-3 text-center">
							<div class="text-muted small mb-1"><i class="fas fa-coins me-1"></i> إجمالي المبيعات</div>
							<div class="fs-4 fw-bold text-success"><?= number_format($finalTotal, 2) ?> <small class="fs-6">ريال</small></div>
						</div>
					</div>
					<div class="col-12 col-sm-6 col-xl-3">
						<div class="card-pro p-3 text-center">
							<div class="text-muted small mb-1"><i class="fas fa-money-bill-wave me-1 text-primary"></i> إجمالي نقداً</div>
							<div class="fs-4 fw-bold text-primary"><?= number_format($cashTotal, 2) ?> <small class="fs-6">ريال</small></div>
						</div>
					</div>
					<div class="col-12 col-sm-6 col-xl-3">
						<div class="card-pro p-3 text-center">
							<div class="text-muted small mb-1"><i class="fas fa-credit-card me-1 text-info"></i> إجمالي الشبكة / مدى</div>
							<div class="fs-4 fw-bold text-info"><?= number_format($cardTotal, 2) ?> <small class="fs-6">ريال</small></div>
						</div>
					</div>
					<div class="col-12 col-sm-6 col-xl-3">
						<div class="card-pro p-3 text-center">
							<div class="text-muted small mb-1"><i class="fas fa-boxes me-1 text-warning"></i> إجمالي الكميات / البنود</div>
							<div class="fs-4 fw-bold text-dark"><?= number_format($totalQty, 2) ?> <small class="fs-6">(<?= $totalCount ?> بند)</small></div>
						</div>
					</div>
				</div>

				<!-- Filters Form -->
				<div class="card-pro filter-card mb-4">
					<div class="card-header-pro">
						<h2 class="card-title-pro">
							<i class="fas fa-filter me-1"></i> خيارات الفلترة والبحث المتقدم
						</h2>
					</div>
					<div class="card-body-pro">
						<form action="<?= $pgName ?>" method="GET" id="filterForm">
							<div class="row g-3">
								<div class="col-12 col-md-3">
									<label class="form-label-pro"><i class="fas fa-calendar-alt me-1"></i><?= FROM_DATE ?></label>
									<input type="date" class="form-input-pro" name="from_date" value="<?= htmlspecialchars($fDate) ?>">
								</div>
								<div class="col-12 col-md-3">
									<label class="form-label-pro"><i class="fas fa-calendar-alt me-1"></i><?= TO_DATE ?></label>
									<input type="date" class="form-input-pro" name="to_date" value="<?= htmlspecialchars($tDate) ?>">
								</div>
								<div class="col-12 col-md-3">
									<label class="form-label-pro"><i class="fas fa-hashtag me-1"></i><?= ORDER_ID ?></label>
									<input type="text" class="form-input-pro" name="o" value="<?= htmlspecialchars($orderid) ?>" placeholder="رقم الطلب أو الفاتورة">
								</div>
								<div class="col-12 col-md-3">
									<label class="form-label-pro"><i class="fas fa-box me-1"></i><?= ITEMS ?></label>
									<select class="form-select-pro" name="iid">
										<option value="">-- كافة الأصناف --</option>
										<?php
										$qItems = "SELECT id, name_ar, name_en FROM items WHERE storeid='$storeid' ORDER BY name_ar ASC";
										$rItems = mysqli_query($adController->MySQL, $qItems);
										if ($rItems) {
											while ($it = mysqli_fetch_assoc($rItems)) {
												$itName = !empty($it['name_ar']) ? $it['name_ar'] : $it['name_en'];
												$sel = ($itemId == $it['id']) ? "selected" : "";
												echo "<option value='{$it['id']}' {$sel}>" . htmlspecialchars($itName) . "</option>";
											}
										}
										?>
									</select>
								</div>
								<div class="col-12 col-md-4">
									<label class="form-label-pro"><i class="fas fa-building me-1"></i><?= BRANCH ?></label>
									<select class="form-select-pro" name="br">
										<option value="">-- كافة الفروع --</option>
										<?php
										$qBr = "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' ORDER BY name_ar ASC";
										$rBr = mysqli_query($adController->MySQL, $qBr);
										if ($rBr) {
											while ($br = mysqli_fetch_assoc($rBr)) {
												$brName = !empty($br['name_ar']) ? $br['name_ar'] : $br['name_en'];
												$sel = ($branchid == $br['id']) ? "selected" : "";
												echo "<option value='{$br['id']}' {$sel}>" . htmlspecialchars($brName) . "</option>";
											}
										}
										?>
									</select>
								</div>
								<div class="col-12 col-md-4">
									<label class="form-label-pro"><i class="fas fa-user me-1"></i><?= CASHIER ?></label>
									<select class="form-select-pro" name="ei">
										<option value="">-- كافة المستخدمين / الكاشير --</option>
										<?php
										$qEmp = "SELECT id, name_ar, name_en FROM employees WHERE storeid='$storeid' ORDER BY name_ar ASC";
										$rEmp = mysqli_query($adController->MySQL, $qEmp);
										if ($rEmp) {
											while ($em = mysqli_fetch_assoc($rEmp)) {
												$emName = !empty($em['name_ar']) ? $em['name_ar'] : $em['name_en'];
												$sel = ($empId == $em['id']) ? "selected" : "";
												echo "<option value='{$em['id']}' {$sel}>" . htmlspecialchars($emName) . "</option>";
											}
										}
										?>
									</select>
								</div>
								<div class="col-12 col-md-4 d-flex align-items-end gap-2">
									<button type="submit" class="btn-pro btn-pro-primary flex-grow-1">
										<i class="fas fa-search me-1"></i> تصفية النتائج
									</button>
									<a href="<?= $pgName ?>" class="btn-pro btn-pro-outline">
										<i class="fas fa-undo me-1"></i> إعادة ضبط
									</a>
								</div>
							</div>
						</form>
					</div>
				</div>

				<!-- Orders Table Card -->
				<div class="card-pro">
					<div class="card-header-pro">
						<div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
							<h2 class="card-title-pro">
								<i class="fas fa-list me-1"></i>
								تفاصيل الطلبات المسجلة (<?= count($ordersData) ?> سجل)
							</h2>
							<div class="d-flex gap-2">
								<button type="button" class="btn-pro btn-pro-outline btn-pro-sm" onclick="window.print();">
									<i class="fas fa-print me-1"></i> طباعة التقرير
								</button>
							</div>
						</div>
					</div>
					<div class="card-body-pro">
						<div class="table-responsive">
							<table class="table-pro datatable">
								<thead>
									<tr>
										<th><i class="fas fa-clock me-1"></i><?= DATED ?></th>
										<th><i class="fas fa-building me-1"></i><?= BRANCH ?></th>
										<th><i class="fas fa-hashtag me-1"></i><?= ORDER_ID ?></th>
										<th><i class="fas fa-box me-1"></i><?= ITEMS ?></th>
										<th><i class="fas fa-user me-1"></i>الكاشير</th>
										<th class="text-center"><i class="fas fa-layer-group me-1"></i><?= QUANTITY ?></th>
										<th class="text-start"><i class="fas fa-tag me-1"></i><?= PRICE ?></th>
										<th class="text-start"><i class="fas fa-percent me-1"></i><?= DISCOUNT ?></th>
										<th class="text-start"><i class="fas fa-money-bill me-1"></i><?= TOTAL ?></th>
										<th class="text-center"><i class="fas fa-credit-card me-1"></i>الدفع</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($ordersData as $row): ?>
										<tr>
											<td><small class="text-muted"><?= htmlspecialchars($row['datetime']) ?></small></td>
											<td><strong><?= htmlspecialchars($row['branch']) ?></strong></td>
											<td><span class="badge-pro badge-pro-outline">#<?= htmlspecialchars($row['order_num']) ?></span></td>
											<td><?= htmlspecialchars($row['item']) ?></td>
											<td><small><?= htmlspecialchars($row['employee']) ?></small></td>
											<td class="text-center font-monospace fw-bold"><?= number_format($row['qty'], 2) ?></td>
											<td class="text-start font-monospace"><?= number_format($row['price'], 2) ?></td>
											<td class="text-start font-monospace text-danger"><?= number_format($row['discount'], 2) ?></td>
											<td class="text-start font-monospace fw-bold text-success"><?= number_format($row['total'], 2) ?></td>
											<td class="text-center"><span class="badge-pro badge-pro-info"><?= $row['payment_type'] ?></span></td>
										</tr>
									<?php endforeach; ?>
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
