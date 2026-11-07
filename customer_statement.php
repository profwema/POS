<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';

// Filter parameters
$selectedCustomer = (int)($_GET['customer_id'] ?? 0);
$fromDate         = trim($_GET['from_date'] ?? '');
$toDate           = trim($_GET['to_date'] ?? '');
$payStatus        = trim($_GET['status'] ?? 'all'); // 'all', 'due', 'paid'

// Build SQL conditions
$conditions = ["s.storeid = '$storeid'"];

if ($selectedCustomer > 0) {
    $conditions[] = "s.customer = '$selectedCustomer'";
}

if ($fromDate !== '') {
    $safeFrom = mysqli_real_escape_string($adController->MySQL, $fromDate);
    $conditions[] = "s.date >= '$safeFrom 00:00:00'";
}

if ($toDate !== '') {
    $safeTo = mysqli_real_escape_string($adController->MySQL, $toDate);
    $conditions[] = "s.date <= '$safeTo 23:59:59'";
}

if ($payStatus === 'due') {
    $conditions[] = "s.`left` > 0";
} elseif ($payStatus === 'paid') {
    $conditions[] = "s.`left` <= 0";
}

$whereClause = "WHERE " . implode(" AND ", $conditions);

// Fetch invoices
$query = "SELECT 
            s.*,
            c.name_ar AS cust_name_ar,
            c.name_en AS cust_name_en,
            c.phone AS cust_phone,
            b.name_ar AS branch_name
          FROM sales s
          LEFT JOIN customers c ON s.customer = c.id
          LEFT JOIN branches b ON s.branch = b.id
          $whereClause
          ORDER BY s.date DESC";

$res = mysqli_query($adController->MySQL, $query);

$invoices = [];
$totalInvoiced = 0.0;
$totalPaid = 0.0;
$totalDue = 0.0;
$invoiceCount = 0;

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $invoices[] = $row;
        $totalInvoiced += (float)$row['all_total'];
        $paidNow = (float)$row['cash'] + (float)$row['mada'] + (float)$row['visa'];
        $totalPaid += $paidNow;
        $totalDue += (float)$row['left'];
        $invoiceCount++;
    }
}

// Fetch selected customer metadata
$customerInfo = null;
if ($selectedCustomer > 0) {
    $custQ = "SELECT * FROM customers WHERE id = '$selectedCustomer' LIMIT 1";
    $custRes = mysqli_query($adController->MySQL, $custQ);
    if ($custRes) {
        $customerInfo = mysqli_fetch_assoc($custRes);
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>كشف حساب عميل - WAM Tech POS</title>

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
                        <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                        كشف حساب عميل تفصيلي
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
                            <span class="text-primary">كشف حساب عميل</span>
                        </div>
                    </div>
                </div>

                <!-- Print Header for PDF / Paper printing -->
                <div class="print-header">
                    <h2 class="fw-bold">كشف حساب عميل</h2>
                    <?php if ($customerInfo): ?>
                        <h4>العميل: <?= htmlspecialchars($customerInfo['name_ar'] ?: $customerInfo['name_en']) ?> | هاتف: <?= htmlspecialchars($customerInfo['phone']) ?></h4>
                    <?php endif; ?>
                    <p class="text-muted small">تاريخ التقرير: <?= date("Y-m-d H:i") ?> | العملة: <?= $currency ?></p>
                    <hr>
                </div>

                <!-- Filter Controls -->
                <div class="card-pro mb-4 filter-box no-print">
                    <div class="card-body-pro p-3">
                        <form method="GET" action="customer_statement.php" class="row g-3 align-items-end">
                            <!-- Customer Selection -->
                            <div class="col-md-4">
                                <label class="form-label-pro">
                                    <i class="fas fa-user me-1 text-primary"></i> العميل:
                                </label>
                                <select name="customer_id" class="form-input-pro select2" onchange="this.form.submit()">
                                    <option value="0">-- كافة العملاء --</option>
                                    <?php
                                    $allCustQ = "SELECT id, name_ar, name_en, phone FROM customers WHERE storeid='$storeid' OR storeid='0' ORDER BY name_ar ASC";
                                    $allCustRes = mysqli_query($adController->MySQL, $allCustQ);
                                    if ($allCustRes) {
                                        while ($cRow = mysqli_fetch_assoc($allCustRes)) {
                                            $cName = !empty($cRow['name_ar']) ? $cRow['name_ar'] : $cRow['name_en'];
                                            $cPhone = !empty($cRow['phone']) ? " - " . $cRow['phone'] : "";
                                            $sel = ($selectedCustomer === (int)$cRow['id']) ? 'selected' : '';
                                            echo "<option value='{$cRow['id']}' $sel>" . htmlspecialchars($cName . $cPhone) . "</option>";
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
                                    <option value="due" <?= ($payStatus === 'due') ? 'selected' : '' ?>>متبقي آجل فقط (مديونية)</option>
                                    <option value="paid" <?= ($payStatus === 'paid') ? 'selected' : '' ?>>مسدد بالكامل</option>
                                </select>
                            </div>

                            <!-- Submit button -->
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn-pro btn-pro-primary flex-grow-1">
                                    <i class="fas fa-search me-1"></i> عرض
                                </button>
                                <?php if ($selectedCustomer > 0 || $fromDate !== '' || $toDate !== '' || $payStatus !== 'all'): ?>
                                    <a href="customer_statement.php" class="btn-pro btn-pro-outline" title="إعادة ضبط">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- KPI Summary Cards -->
                <div class="row g-3 mb-4">
                    <!-- Total Invoiced -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <i class="fas fa-file-invoice stat-card-icon text-primary"></i>
                            <div class="stat-card-value text-primary">
                                <?= number_format($totalInvoiced, 2) ?> <span class="fs-6 fw-normal"><?= $currency ?></span>
                            </div>
                            <div class="stat-card-label">إجمالي الفواتير الصادرة</div>
                            <div class="mt-2 small text-muted">
                                عدد الفواتير: <strong><?= $invoiceCount ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Total Paid -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card success">
                            <i class="fas fa-check-circle stat-card-icon text-success"></i>
                            <div class="stat-card-value text-success">
                                <?= number_format($totalPaid, 2) ?> <span class="fs-6 fw-normal"><?= $currency ?></span>
                            </div>
                            <div class="stat-card-label">المبالغ المسددة والمحصلة</div>
                            <div class="mt-2 small text-muted">
                                كاش / كارت / تحويل
                            </div>
                        </div>
                    </div>

                    <!-- Total Due (Debt) -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card <?= ($totalDue > 0) ? 'border-danger' : '' ?>">
                            <i class="fas fa-hand-holding-usd stat-card-icon text-danger"></i>
                            <div class="stat-card-value text-danger">
                                <?= number_format($totalDue, 2) ?> <span class="fs-6 fw-normal"><?= $currency ?></span>
                            </div>
                            <div class="stat-card-label">الرصيد المتبقي (مديونية العميل)</div>
                            <div class="mt-2 small text-danger fw-semibold">
                                <?= ($totalDue > 0) ? 'مستحق التحصيل' : 'لا توجد مديونيات' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info or Invoices Count -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card info">
                            <i class="fas fa-user-circle stat-card-icon text-info"></i>
                            <div class="stat-card-value text-info fs-5">
                                <?= $customerInfo ? htmlspecialchars($customerInfo['name_ar'] ?: $customerInfo['name_en']) : 'كافة العملاء' ?>
                            </div>
                            <div class="stat-card-label">بيانات الحساب</div>
                            <div class="mt-2 small text-muted">
                                <?= $customerInfo ? 'الهاتف: ' . htmlspecialchars($customerInfo['phone']) : 'تقرير مجمع لكافة العملاء' ?>
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
                                سجل الفواتير وحركات الحساب
                                <span class="badge-pro badge-pro-primary ms-2"><?= $invoiceCount ?></span>
                            </h2>
                            <div class="d-flex gap-2 no-print">
                                <button onclick="window.print()" class="btn-pro btn-pro-outline btn-pro-sm" title="طباعة كشف الحساب">
                                    <i class="fas fa-print me-1"></i> طباعة كشف الحساب
                                </button>
                                <button onclick="exportStatementToExcel('statementTable', 'customer_statement')" class="btn-pro btn-pro-success btn-pro-sm" title="تصدير إلى Excel">
                                    <i class="fas fa-file-excel me-1"></i> تصدير Excel
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-pro p-0">
                        <div class="table-responsive">
                            <table class="table-pro" id="statementTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th>التاريخ والوقت</th>
                                        <th>رقم الفاتورة</th>
                                        <th>العميل</th>
                                        <th>الفرع</th>
                                        <th class="text-center">إجمالي الفاتورة</th>
                                        <th class="text-center">الخصم</th>
                                        <th class="text-center">المسدد</th>
                                        <th class="text-center">المتبقي (آجل)</th>
                                        <th class="text-center">طريقة السداد</th>
                                        <th class="text-center">حالة الفاتورة</th>
                                        <th class="text-center no-print">عرض</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($invoices)): ?>
                                        <tr>
                                            <td colspan="12" class="text-center py-5 text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-2 d-block text-muted"></i>
                                                لا توجد فواتير أو حركات مسجلة تطابق محددات البحث
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $i = 0;
                                        foreach ($invoices as $inv): 
                                            $i++;
                                            $invTotal = (float)$inv['all_total'];
                                            $invDiscount = (float)$inv['discount'];
                                            $paidCash = (float)$inv['cash'];
                                            $paidCard = (float)$inv['mada'];
                                            $paidVisa = (float)$inv['visa'];
                                            $paidTotal = $paidCash + $paidCard + $paidVisa;
                                            $invLeft = (float)$inv['left'];

                                            $payMethods = [];
                                            if ($paidCash > 0) $payMethods[] = 'كاش (' . number_format($paidCash, 2) . ')';
                                            if ($paidCard > 0) $payMethods[] = 'كارت/ميزة (' . number_format($paidCard, 2) . ')';
                                            if ($paidVisa > 0) $payMethods[] = 'إنستاباي/تحويل (' . number_format($paidVisa, 2) . ')';
                                            $payMethodsText = !empty($payMethods) ? implode(' + ', $payMethods) : 'آجل';

                                            $clientTitle = !empty($inv['cust_name_ar']) ? $inv['cust_name_ar'] : ($inv['cust_name_en'] ?? 'عميل نقدي');
                                        ?>
                                            <tr>
                                                <td class="text-center fw-bold"><?= $i ?></td>
                                                <td class="small text-muted font-monospace"><?= htmlspecialchars($inv['date']) ?></td>
                                                <td>
                                                    <a href="saleInvoice.php?invoice=<?= urlencode($inv['invoicenumber']) ?>" class="font-monospace fw-bold text-primary text-decoration-none">
                                                        #<?= htmlspecialchars($inv['invoicenumber']) ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold"><?= htmlspecialchars($clientTitle) ?></span>
                                                </td>
                                                <td class="small text-muted"><?= htmlspecialchars($inv['branch_name'] ?? 'الرئيسي') ?></td>
                                                <td class="text-center fw-bold">
                                                    <?= number_format($invTotal, 2) ?> <?= $currency ?>
                                                </td>
                                                <td class="text-center text-muted small">
                                                    <?= number_format($invDiscount, 2) ?>
                                                </td>
                                                <td class="text-center text-success fw-semibold">
                                                    <?= number_format($paidTotal, 2) ?> <?= $currency ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($invLeft > 0): ?>
                                                        <span class="badge bg-danger fs-6 px-2 py-1">
                                                            <?= number_format($invLeft, 2) ?> <?= $currency ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success-light text-success small">0.00</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="small text-muted text-center">
                                                    <?= htmlspecialchars($payMethodsText) ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($invLeft <= 0): ?>
                                                        <span class="badge-pro badge-pro-success">مسددة</span>
                                                    <?php elseif ($paidTotal > 0): ?>
                                                        <span class="badge-pro badge-pro-warning">مسدد جزئياً</span>
                                                    <?php else: ?>
                                                        <span class="badge-pro badge-pro-danger">غير مسددة</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center no-print">
                                                    <a href="saleInvoice.php?invoice=<?= urlencode($inv['invoicenumber']) ?>" class="btn-pro btn-pro-outline btn-pro-sm" title="عرض الفاتورة">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-light fw-bold">
                                        <td colspan="5" class="text-center">الإجمالي العام</td>
                                        <td class="text-center text-primary"><?= number_format($totalInvoiced, 2) ?> <?= $currency ?></td>
                                        <td>-</td>
                                        <td class="text-center text-success"><?= number_format($totalPaid, 2) ?> <?= $currency ?></td>
                                        <td class="text-center text-danger"><?= number_format($totalDue, 2) ?> <?= $currency ?></td>
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
        function exportStatementToExcel(tableID, filename = 'customer_statement') {
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

