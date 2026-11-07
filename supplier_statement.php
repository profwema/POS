<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';

// Filter parameters
$selectedSupplier = (int)($_GET['supplier_id'] ?? 0);
$fromDate         = trim($_GET['from_date'] ?? '');
$toDate           = trim($_GET['to_date'] ?? '');
$payStatus        = trim($_GET['status'] ?? 'all'); // 'all', 'due', 'paid'

// Build SQL conditions
$conditions = ["p.storeid = '$storeid'"];

if ($selectedSupplier > 0) {
    $conditions[] = "p.supplier = '$selectedSupplier'";
}

if ($fromDate !== '') {
    $safeFrom = mysqli_real_escape_string($adController->MySQL, $fromDate);
    $conditions[] = "p.invoice_date >= '$safeFrom 00:00:00'";
}

if ($toDate !== '') {
    $safeTo = mysqli_real_escape_string($adController->MySQL, $toDate);
    $conditions[] = "p.invoice_date <= '$safeTo 23:59:59'";
}

if ($payStatus === 'due') {
    $conditions[] = "p.`left` > 0";
} elseif ($payStatus === 'paid') {
    $conditions[] = "p.`left` <= 0";
}

$whereClause = "WHERE " . implode(" AND ", $conditions);

// Fetch purchase invoices
$query = "SELECT 
            p.*,
            s.name_ar AS supp_name_ar,
            s.name_en AS supp_name_en,
            s.phone AS supp_phone,
            b.name_ar AS branch_name
          FROM purchase p
          LEFT JOIN suppliers s ON p.supplier = s.id
          LEFT JOIN branches b ON p.branch = b.id
          $whereClause
          ORDER BY p.invoice_date DESC";

$res = mysqli_query($adController->MySQL, $query);

$purchases = [];
$totalPurchased = 0.0;
$totalPaid = 0.0;
$totalPayable = 0.0;
$purchaseCount = 0;

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $purchases[] = $row;
        $allTotal = (float)$row['all_total'];
        $leftVal = (float)$row['left'];
        $paidVal = $allTotal - $leftVal;
        if ($paidVal < 0) $paidVal = 0;

        $totalPurchased += $allTotal;
        $totalPaid += $paidVal;
        $totalPayable += $leftVal;
        $purchaseCount++;
    }
}

// Fetch selected supplier metadata
$supplierInfo = null;
if ($selectedSupplier > 0) {
    $supQ = "SELECT * FROM suppliers WHERE id = '$selectedSupplier' LIMIT 1";
    $supRes = mysqli_query($adController->MySQL, $supQ);
    if ($supRes) {
        $supplierInfo = mysqli_fetch_assoc($supRes);
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>كشف حساب مورد - WAM Tech POS</title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        @media print {
            .admin-sidebar, .admin-navbar, .page-header, .filter-box, .no-print, .btn-pro {
                display: none !important;
            }
            .admin-content {
                padding: 0 !important;
            }
            .card-pro {
                border: none !important;
                box-shadow: none !important;
            }
            .print-header {
                display: block !important;
                margin-bottom: 20px;
                text-align: center;
            }
        }
        .print-header {
            display: none;
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
                        <i class="fas fa-truck-loading text-info me-2"></i>
                        كشف حساب مورد تفصيلي
                    </h1>
                    <div class="page-breadcrumb">
                        <div class="breadcrumb-item">
                            <i class="fas fa-home"></i>
                            <span>الرئيسية</span>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <span><?= F_ACCOUNTS ?></span>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <span class="text-primary">كشف حساب مورد</span>
                        </div>
                    </div>
                </div>

                <!-- Print Header for PDF / Paper printing -->
                <div class="print-header">
                    <h2 class="fw-bold">كشف حساب مورد</h2>
                    <?php if ($supplierInfo): ?>
                        <h4>المورد: <?= htmlspecialchars($supplierInfo['name_ar'] ?: $supplierInfo['name_en']) ?> | هاتف: <?= htmlspecialchars($supplierInfo['phone']) ?></h4>
                    <?php endif; ?>
                    <p class="text-muted small">تاريخ التقرير: <?= date("Y-m-d H:i") ?> | العملة: <?= $currency ?></p>
                    <hr>
                </div>

                <!-- Filter Controls -->
                <div class="card-pro mb-4 filter-box no-print">
                    <div class="card-body-pro p-3">
                        <form method="GET" action="supplier_statement.php" class="row g-3 align-items-end">
                            <!-- Supplier Selection -->
                            <div class="col-md-4">
                                <label class="form-label-pro">
                                    <i class="fas fa-truck me-1 text-info"></i> المورد:
                                </label>
                                <select name="supplier_id" class="form-input-pro select2" onchange="this.form.submit()">
                                    <option value="0">-- كافة الموردين --</option>
                                    <?php
                                    $allSupQ = "SELECT id, name_ar, name_en, phone FROM suppliers WHERE storeid='$storeid' OR storeid='0' ORDER BY name_ar ASC";
                                    $allSupRes = mysqli_query($adController->MySQL, $allSupQ);
                                    if ($allSupRes) {
                                        while ($sRow = mysqli_fetch_assoc($allSupRes)) {
                                            $sName = !empty($sRow['name_ar']) ? $sRow['name_ar'] : $sRow['name_en'];
                                            $sPhone = !empty($sRow['phone']) ? " - " . $sRow['phone'] : "";
                                            $sel = ($selectedSupplier === (int)$sRow['id']) ? 'selected' : '';
                                            echo "<option value='{$sRow['id']}' $sel>" . htmlspecialchars($sName . $sPhone) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- From Date -->
                            <div class="col-md-2">
                                <label class="form-label-pro">
                                    <i class="fas fa-calendar-alt me-1 text-muted"></i> من تاريخ:
                                </label>
                                <input type="date" name="from_date" class="form-input-pro" value="<?= htmlspecialchars($fromDate) ?>">
                            </div>

                            <!-- To Date -->
                            <div class="col-md-2">
                                <label class="form-label-pro">
                                    <i class="fas fa-calendar-alt me-1 text-muted"></i> إلى تاريخ:
                                </label>
                                <input type="date" name="to_date" class="form-input-pro" value="<?= htmlspecialchars($toDate) ?>">
                            </div>

                            <!-- Status -->
                            <div class="col-md-2">
                                <label class="form-label-pro">
                                    <i class="fas fa-filter me-1 text-muted"></i> حالة السداد:
                                </label>
                                <select name="status" class="form-input-pro">
                                    <option value="all" <?= ($payStatus === 'all') ? 'selected' : '' ?>>الكل</option>
                                    <option value="due" <?= ($payStatus === 'due') ? 'selected' : '' ?>>مستحق للمورد (مديونية)</option>
                                    <option value="paid" <?= ($payStatus === 'paid') ? 'selected' : '' ?>>مسدد بالكامل</option>
                                </select>
                            </div>

                            <!-- Submit button -->
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn-pro btn-pro-primary flex-grow-1">
                                    <i class="fas fa-search me-1"></i> عرض
                                </button>
                                <?php if ($selectedSupplier > 0 || $fromDate !== '' || $toDate !== '' || $payStatus !== 'all'): ?>
                                    <a href="supplier_statement.php" class="btn-pro btn-pro-outline" title="إعادة ضبط">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- KPI Summary Cards -->
                <div class="row g-3 mb-4">
                    <!-- Total Purchases -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <i class="fas fa-shopping-bag stat-card-icon text-primary"></i>
                            <div class="stat-card-value text-primary">
                                <?= number_format($totalPurchased, 2) ?> <span class="fs-6 fw-normal"><?= $currency ?></span>
                            </div>
                            <div class="stat-card-label">إجمالي فواتير المشتريات</div>
                            <div class="mt-2 small text-muted">
                                عدد الفواتير: <strong><?= $purchaseCount ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Total Paid to Supplier -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card success">
                            <i class="fas fa-money-bill-wave stat-card-icon text-success"></i>
                            <div class="stat-card-value text-success">
                                <?= number_format($totalPaid, 2) ?> <span class="fs-6 fw-normal"><?= $currency ?></span>
                            </div>
                            <div class="stat-card-label">إجمالي المسدد للمورد</div>
                            <div class="mt-2 small text-muted">
                                دفعات نقدية وبنكية
                            </div>
                        </div>
                    </div>

                    <!-- Total Payable (Debt to Supplier) -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card <?= ($totalPayable > 0) ? 'border-danger' : '' ?>">
                            <i class="fas fa-file-invoice-dollar stat-card-icon text-danger"></i>
                            <div class="stat-card-value text-danger">
                                <?= number_format($totalPayable, 2) ?> <span class="fs-6 fw-normal"><?= $currency ?></span>
                            </div>
                            <div class="stat-card-label">المستحق للمورد (مديونية قائمة)</div>
                            <div class="mt-2 small text-danger fw-semibold">
                                <?= ($totalPayable > 0) ? 'واجبة السداد' : 'لا توجد مستحقات' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Info -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card info">
                            <i class="fas fa-truck stat-card-icon text-info"></i>
                            <div class="stat-card-value text-info fs-5">
                                <?= $supplierInfo ? htmlspecialchars($supplierInfo['name_ar'] ?: $supplierInfo['name_en']) : 'كافة الموردين' ?>
                            </div>
                            <div class="stat-card-label">بيانات المورد</div>
                            <div class="mt-2 small text-muted">
                                <?= $supplierInfo ? 'الهاتف: ' . htmlspecialchars($supplierInfo['phone']) : 'تقرير مجمع لكافة الموردين' ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ledger Table Card -->
                <div class="card-pro">
                    <div class="card-header-pro">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h2 class="card-title-pro mb-0">
                                <i class="fas fa-list-alt text-primary"></i>
                                سجل فواتير الشراء والتوريدات
                                <span class="badge-pro badge-pro-primary ms-2"><?= $purchaseCount ?></span>
                            </h2>
                            <div class="d-flex gap-2 no-print">
                                <button onclick="window.print()" class="btn-pro btn-pro-outline btn-pro-sm" title="طباعة كشف الحساب">
                                    <i class="fas fa-print me-1"></i> طباعة كشف الحساب
                                </button>
                                <button onclick="exportSupplierStatementToExcel('supplierTable', 'supplier_statement')" class="btn-pro btn-pro-success btn-pro-sm" title="تصدير إلى Excel">
                                    <i class="fas fa-file-excel me-1"></i> تصدير Excel
                                </button>
                                <a href="add_purchase.php" class="btn-pro btn-pro-primary btn-pro-sm">
                                    <i class="fas fa-plus me-1"></i> فاتورة شراء جديدة
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-pro p-0">
                        <div class="table-responsive">
                            <table class="table-pro" id="supplierTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th>التاريخ</th>
                                        <th>رقم الفاتورة</th>
                                        <th>فاتورة المورد</th>
                                        <th>المورد</th>
                                        <th>الفرع</th>
                                        <th class="text-center">إجمالي الفاتورة</th>
                                        <th class="text-center">الخصم المكتسب</th>
                                        <th class="text-center">الضريبة</th>
                                        <th class="text-center">المسدد</th>
                                        <th class="text-center">المتبقي للمورد</th>
                                        <th class="text-center">طريقة الدفع</th>
                                        <th class="text-center">الحالة</th>
                                        <th class="text-center no-print">عرض</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($purchases)): ?>
                                        <tr>
                                            <td colspan="14" class="text-center py-5 text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-2 d-block text-muted"></i>
                                                لا توجد فواتير شراء مطابقة لمحددات البحث
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $i = 0;
                                        foreach ($purchases as $p): 
                                            $i++;
                                            $invTotal = (float)$p['all_total'];
                                            $discountVal = (float)$p['dis_added'];
                                            $vatVal = (float)$p['vat_all'];
                                            $leftVal = (float)$p['left'];
                                            $paidVal = $invTotal - $leftVal;
                                            if ($paidVal < 0) $paidVal = 0;

                                            $suppTitle = !empty($p['supp_name_ar']) ? $p['supp_name_ar'] : ($p['supp_name_en'] ?? 'مورد عام');
                                            $payTypeStr = ((int)$p['paymentType'] === 1) ? 'نقدي' : 'آجل';
                                        ?>
                                            <tr>
                                                <td class="text-center fw-bold"><?= $i ?></td>
                                                <td class="small text-muted font-monospace"><?= htmlspecialchars(substr($p['invoice_date'], 0, 10)) ?></td>
                                                <td>
                                                    <a href="purchase.php?invoice=<?= urlencode($p['invoicenumber']) ?>" class="font-monospace fw-bold text-primary text-decoration-none">
                                                        #<?= htmlspecialchars($p['invoicenumber']) ?>
                                                    </a>
                                                </td>
                                                <td class="font-monospace small text-muted">
                                                    <?= htmlspecialchars($p['supplier_invoice'] ?: '---') ?>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold"><?= htmlspecialchars($suppTitle) ?></span>
                                                </td>
                                                <td class="small text-muted"><?= htmlspecialchars($p['branch_name'] ?? 'الرئيسي') ?></td>
                                                <td class="text-center fw-bold">
                                                    <?= number_format($invTotal, 2) ?> <?= $currency ?>
                                                </td>
                                                <td class="text-center text-muted small">
                                                    <?= number_format($discountVal, 2) ?>
                                                </td>
                                                <td class="text-center text-muted small">
                                                    <?= number_format($vatVal, 2) ?>
                                                </td>
                                                <td class="text-center text-success fw-semibold">
                                                    <?= number_format($paidVal, 2) ?> <?= $currency ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($leftVal > 0): ?>
                                                        <span class="badge bg-danger fs-6 px-2 py-1">
                                                            <?= number_format($leftVal, 2) ?> <?= $currency ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success-light text-success small">0.00</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="small text-muted text-center">
                                                    <?= htmlspecialchars($payTypeStr) ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($leftVal <= 0): ?>
                                                        <span class="badge-pro badge-pro-success">مسددة</span>
                                                    <?php elseif ($paidVal > 0): ?>
                                                        <span class="badge-pro badge-pro-warning">مسدد جزئياً</span>
                                                    <?php else: ?>
                                                        <span class="badge-pro badge-pro-danger">غير مسددة</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center no-print">
                                                    <a href="purchase.php?invoice=<?= urlencode($p['invoicenumber']) ?>" class="btn-pro btn-pro-outline btn-pro-sm" title="عرض الفاتورة">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-light fw-bold">
                                        <td colspan="6" class="text-center">الإجمالي العام</td>
                                        <td class="text-center text-primary"><?= number_format($totalPurchased, 2) ?> <?= $currency ?></td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td class="text-center text-success"><?= number_format($totalPaid, 2) ?> <?= $currency ?></td>
                                        <td class="text-center text-danger"><?= number_format($totalPayable, 2) ?> <?= $currency ?></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
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
    <script>
        function exportSupplierStatementToExcel(tableID, filename = 'supplier_statement') {
            let table = document.getElementById(tableID);
            if (!table) return;

            let html = table.outerHTML;
            let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
            let downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);
            downloadLink.href = url;
            downloadLink.download = filename + '_' + new Date().toISOString().slice(0, 10) + '.xls';
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
</body>

</html>

