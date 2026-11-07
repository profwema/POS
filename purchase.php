<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';
$pgName   = basename(__FILE__);

// Filters
$fdate      = trim($_GET['fr_date'] ?? '');
$tdate      = trim($_GET['to_date'] ?? '');
$supplierId = (int)($_GET['supplier'] ?? 0);
$branchId   = (int)($_GET['branch'] ?? 0);
$payTypeF   = trim($_GET['pay_type'] ?? 'all');

$conditions = ["p.storeid = '$storeid'", "p.state = '1'"];

if ($fdate !== '') {
    $safeF = mysqli_real_escape_string($adController->MySQL, $fdate);
    $conditions[] = "p.invoice_date >= '$safeF 00:00:00'";
}
if ($tdate !== '') {
    $safeT = mysqli_real_escape_string($adController->MySQL, $tdate);
    $conditions[] = "p.invoice_date <= '$safeT 23:59:59'";
}
if ($supplierId > 0) {
    $conditions[] = "p.supplier = '$supplierId'";
}
if ($branchId > 0) {
    $conditions[] = "p.branch = '$branchId'";
}
if ($payTypeF === 'cash') {
    $conditions[] = "p.paymentType = '1'";
} elseif ($payTypeF === 'credit') {
    $conditions[] = "p.paymentType != '1'";
}

$whereClause = "WHERE " . implode(" AND ", $conditions);

// Fetch purchases list
$query = "SELECT 
            p.*, 
            s.name_ar AS supplier_name_ar, 
            s.name_en AS supplier_name_en, 
            b.name_ar AS branch_name_ar,
            b.name_en AS branch_name_en
          FROM purchase p 
          LEFT JOIN suppliers s ON p.supplier = s.id AND (s.storeid = '$storeid' OR s.storeid = '0') 
          LEFT JOIN branches b ON p.branch = b.id AND (b.storeid = '$storeid' OR b.storeid = '0') 
          $whereClause 
          ORDER BY p.id DESC";

$res = mysqli_query($adController->MySQL, $query);

$purchases = [];
$totalPurchases = 0.0;
$totalCash = 0.0;
$totalCredit = 0.0;
$totalVat = 0.0;
$invoicesCount = 0;

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $purchases[] = $row;
        $allTot = (float)$row['all_total'];
        $vatTot = (float)$row['vat_all'];
        $isCash = ((int)$row['paymentType'] === 1);

        $totalPurchases += $allTot;
        $totalVat += $vatTot;
        if ($isCash) {
            $totalCash += $allTot;
        } else {
            $totalCredit += $allTot;
        }
        $invoicesCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WAM Tech POS - إدارة فواتير المشتريات</title>

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
                        <i class="fas fa-shopping-bag text-primary me-2"></i>
                        <?= PURCHASES ?> - فواتير المشتريات والتوريد
                    </h1>
                    <div class="page-breadcrumb">
                        <div class="breadcrumb-item">
                            <i class="fas fa-home"></i>
                            <span>الرئيسية</span>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <span><?= PURCHASES ?></span>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <span class="text-primary">سجل الفواتير</span>
                        </div>
                    </div>
                </div>

                <!-- KPI Summary Cards -->
                <div class="row g-3 mb-4">
                    <!-- Total Purchases -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <i class="fas fa-file-invoice-dollar stat-card-icon text-primary"></i>
                            <div class="stat-card-value text-primary">
                                <?= number_format($totalPurchases, 2) ?> <small class="fs-6"><?= $currency ?></small>
                            </div>
                            <div class="stat-card-label">إجمالي المشتريات</div>
                            <div class="mt-2 small text-muted">
                                عدد الفواتير: <strong><?= $invoicesCount ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Purchases -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card success">
                            <i class="fas fa-money-bill-wave stat-card-icon text-success"></i>
                            <div class="stat-card-value text-success">
                                <?= number_format($totalCash, 2) ?> <small class="fs-6"><?= $currency ?></small>
                            </div>
                            <div class="stat-card-label">المشتريات النقدية (مسدد)</div>
                            <div class="mt-2 small text-muted">
                                تم سدادها من الخزينة
                            </div>
                        </div>
                    </div>

                    <!-- Credit Purchases (Payable) -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card <?= ($totalCredit > 0) ? 'border-danger' : 'info' ?>">
                            <i class="fas fa-hand-holding-usd stat-card-icon text-danger"></i>
                            <div class="stat-card-value text-danger">
                                <?= number_format($totalCredit, 2) ?> <small class="fs-6"><?= $currency ?></small>
                            </div>
                            <div class="stat-card-label">المشتريات الآجلة (مستحق للموردين)</div>
                            <div class="mt-2 small text-muted">
                                واجبة السداد على الحساب
                            </div>
                        </div>
                    </div>

                    <!-- Total VAT Paid -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card warning">
                            <i class="fas fa-percentage stat-card-icon text-warning"></i>
                            <div class="stat-card-value text-warning">
                                <?= number_format($totalVat, 2) ?> <small class="fs-6"><?= $currency ?></small>
                            </div>
                            <div class="stat-card-label">ضريبة القيمة المضافة (14%)</div>
                            <div class="mt-2 small text-muted">
                                ضريبة مدخلات قابلة للخصم
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Card -->
                <div class="card-pro">
                    <div class="card-header-pro">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h2 class="card-title-pro mb-0">
                                <i class="fas fa-list-alt text-primary"></i>
                                قائمة فواتير الشراء
                                <span class="badge-pro badge-pro-primary ms-2"><?= $invoicesCount ?></span>
                            </h2>
                            <div class="d-flex gap-2 no-print">
                                <button onclick="window.print()" class="btn-pro btn-pro-outline btn-pro-sm" title="طباعة">
                                    <i class="fas fa-print me-1"></i> طباعة
                                </button>
                                <button onclick="exportPurchasesToExcel('purchasesTable', 'purchases_report')" class="btn-pro btn-pro-success btn-pro-sm" title="تصدير إلى Excel">
                                    <i class="fas fa-file-excel me-1"></i> تصدير Excel
                                </button>
                                <a href="add_purchase.php" class="btn-pro btn-pro-primary btn-pro-sm">
                                    <i class="fas fa-plus me-1"></i> فاتورة شراء جديدة
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-pro">

                        <!-- Filter Controls -->
                        <div class="filter-box mb-4 p-3 bg-light rounded border no-print">
                            <form method="GET" action="purchase.php" class="row g-2 align-items-end">
                                <!-- From Date -->
                                <div class="col-md-2">
                                    <label class="form-label-pro small mb-1">
                                        <i class="fas fa-calendar-alt text-muted me-1"></i> من تاريخ:
                                    </label>
                                    <input type="date" name="fr_date" class="form-input-pro py-1" value="<?= htmlspecialchars($fdate) ?>">
                                </div>

                                <!-- To Date -->
                                <div class="col-md-2">
                                    <label class="form-label-pro small mb-1">
                                        <i class="fas fa-calendar-alt text-muted me-1"></i> إلى تاريخ:
                                    </label>
                                    <input type="date" name="to_date" class="form-input-pro py-1" value="<?= htmlspecialchars($tdate) ?>">
                                </div>

                                <!-- Supplier -->
                                <div class="col-md-3">
                                    <label class="form-label-pro small mb-1">
                                        <i class="fas fa-truck text-muted me-1"></i> المورد:
                                    </label>
                                    <select name="supplier" class="form-input-pro py-1">
                                        <option value="0">-- كافة الموردين --</option>
                                        <?php
                                        $sQuery = "SELECT id, name_ar, name_en FROM suppliers WHERE storeid = '$storeid' OR storeid = '0' ORDER BY name_ar ASC";
                                        $sRes = mysqli_query($adController->MySQL, $sQuery);
                                        if ($sRes) {
                                            while ($sData = mysqli_fetch_assoc($sRes)) {
                                                $sTitle = !empty($sData['name_ar']) ? $sData['name_ar'] : $sData['name_en'];
                                                $selS = ($supplierId === (int)$sData['id']) ? 'selected' : '';
                                                echo "<option value='{$sData['id']}' $selS>" . htmlspecialchars($sTitle) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Payment Type -->
                                <div class="col-md-2">
                                    <label class="form-label-pro small mb-1">
                                        <i class="fas fa-money-check-alt text-muted me-1"></i> طريقة الدفع:
                                    </label>
                                    <select name="pay_type" class="form-input-pro py-1">
                                        <option value="all" <?= ($payTypeF === 'all') ? 'selected' : '' ?>>الكل</option>
                                        <option value="cash" <?= ($payTypeF === 'cash') ? 'selected' : '' ?>>نقدي (كاش)</option>
                                        <option value="credit" <?= ($payTypeF === 'credit') ? 'selected' : '' ?>>آجل (على الحساب)</option>
                                    </select>
                                </div>

                                <!-- Submit & Reset -->
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn-pro btn-pro-primary btn-pro-sm flex-grow-1">
                                        <i class="fas fa-search me-1"></i> تصفية
                                    </button>
                                    <?php if ($fdate !== '' || $tdate !== '' || $supplierId > 0 || $payTypeF !== 'all'): ?>
                                        <a href="purchase.php" class="btn-pro btn-pro-outline btn-pro-sm" title="إعادة تعيين">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table-pro" id="purchasesTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 40px;">#</th>
                                        <th>رقم الفاتورة</th>
                                        <th>فاتورة المورد</th>
                                        <th>المورد</th>
                                        <th>الفرع</th>
                                        <th>تاريخ الفاتورة</th>
                                        <th class="text-center">الإجمالي قبل الضريبة</th>
                                        <th class="text-center">الضريبة (14%)</th>
                                        <th class="text-center">الصافي النهائي</th>
                                        <th class="text-center">طريقة السداد</th>
                                        <th class="text-center no-print">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($purchases)): ?>
                                        <tr>
                                            <td colspan="11" class="text-center py-5 text-muted">
                                                <i class="fas fa-inbox fa-3x mb-2 d-block text-muted"></i>
                                                لا توجد فواتير مشتريات مطابقة للبحث
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $i = 0;
                                        foreach ($purchases as $p): 
                                            $i++;
                                            $invNumber = htmlspecialchars($p['invoicenumber'] ?? '');
                                            $suppName = !empty($p['supplier_name_ar']) ? $p['supplier_name_ar'] : ($p['supplier_name_en'] ?? 'مورد عام');
                                            $brName   = !empty($p['branch_name_ar']) ? $p['branch_name_ar'] : ($p['branch_name_en'] ?? 'الفرع الرئيسي');
                                            $isCash   = ((int)$p['paymentType'] === 1);

                                            $idval        = urlencode($adController->encrypt_decrypt(1, $p["invoicenumber"] ?? '', 0));
                                            $invoiceTable = urlencode($adController->encrypt_decrypt(1, 'purchase', 0));
                                            $otherTable   = urlencode($adController->encrypt_decrypt(1, 'income', 0));
                                        ?>
                                            <tr>
                                                <td class="text-center fw-bold text-muted"><?= $i ?></td>
                                                <td>
                                                    <a href="reports/purchase.php?sd=<?= urlencode($invNumber) ?>" target="_blank" class="font-monospace fw-bold text-primary text-decoration-none">
                                                        #<?= $invNumber ?>
                                                    </a>
                                                </td>
                                                <td class="font-monospace small text-muted">
                                                    <?= htmlspecialchars($p['supplier_invoice'] ?: '---') ?>
                                                </td>
                                                <td class="fw-semibold">
                                                    <?= htmlspecialchars($suppName) ?>
                                                </td>
                                                <td class="small text-muted">
                                                    <?= htmlspecialchars($brName) ?>
                                                </td>
                                                <td class="small font-monospace text-muted">
                                                    <?= htmlspecialchars(substr($p['invoice_date'], 0, 10)) ?>
                                                </td>
                                                <td class="text-center text-muted small">
                                                    <?= number_format((float)$p['gross_total'], 2) ?>
                                                </td>
                                                <td class="text-center text-warning fw-semibold small">
                                                    <?= number_format((float)$p['vat_all'], 2) ?>
                                                </td>
                                                <td class="text-center fw-bold text-primary">
                                                    <?= number_format((float)$p['all_total'], 2) ?> <?= $currency ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($isCash): ?>
                                                        <span class="badge-pro badge-pro-success">نقدي (كاش)</span>
                                                    <?php else: ?>
                                                        <span class="badge-pro badge-pro-warning">آجل (على الحساب)</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center no-print">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <!-- Print -->
                                                        <a href="reports/purchase.php?sd=<?= urlencode($invNumber) ?>" target="_blank" class="btn-pro btn-pro-outline btn-pro-sm" title="طباعة الفاتورة">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                        <!-- Edit -->
                                                        <a href="edit_purchase.php?sd=<?= $idval ?>" class="btn-pro btn-pro-primary btn-pro-sm" title="تعديل">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <!-- Delete -->
                                                        <button class="btn-pro btn-pro-danger btn-pro-sm" onclick="deleteInvoice('<?= $invoiceTable ?>', '<?= $idval ?>', '<?= $otherTable ?>');" title="حذف">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-light fw-bold">
                                        <td colspan="6" class="text-center">الإجمالي العام</td>
                                        <td>-</td>
                                        <td class="text-center text-warning"><?= number_format($totalVat, 2) ?> <?= $currency ?></td>
                                        <td class="text-center text-primary"><?= number_format($totalPurchases, 2) ?> <?= $currency ?></td>
                                        <td colspan="2"></td>
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
        function exportPurchasesToExcel(tableID, filename = 'purchases_report') {
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
