<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid  = (int)($_SESSION['storeid'] ?? 1);
$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';
$pgName   = basename(__FILE__);

// Filters
$selectedCustomer = (int)($_REQUEST['customer'] ?? 0);
$selectedBranch   = (int)($_REQUEST['branch'] ?? 0);
$fr_date          = trim($_REQUEST['fr_date'] ?? date('Y-m-01'));
$to_date          = trim($_REQUEST['to_date'] ?? date('Y-m-d'));

$grossTotal   = 0.0;
$discTotal    = 0.0;
$vatTotal     = 0.0;
$salesTotal   = 0.0;
$paidTotal    = 0.0;
$leftTotal    = 0.0;
$reSalesTotal = 0.0;
$salesCount   = 0;
$retCount     = 0;

$conditionSales = "";
$conditionRet   = "";

if ($selectedCustomer > 0) {
    $conditionSales .= " AND s.customer = '$selectedCustomer'";
    $conditionRet   .= " AND sr.customer = '$selectedCustomer'";
    $qCust = "SELECT name_ar, name_en FROM customers WHERE id='$selectedCustomer' AND (storeid='$storeid' OR storeid='0') LIMIT 1";
    $rCust = mysqli_query($adController->MySQL, $qCust);
    $dCust = mysqli_fetch_assoc($rCust);
    $custName = ($language == 'ar' ? ($dCust['name_ar'] ?? '') : ($dCust['name_en'] ?? '')) ?: ($dCust['name_ar'] ?? 'الكل');
} else {
    $custName = 'الكل';
}

if ($selectedBranch > 0) {
    $conditionSales .= " AND s.branch = '$selectedBranch'";
    $conditionRet   .= " AND sr.branch = '$selectedBranch'";
    $qBr = "SELECT name_ar, name_en FROM branches WHERE id='$selectedBranch' AND (storeid='$storeid' OR storeid='0') LIMIT 1";
    $rBr = mysqli_query($adController->MySQL, $qBr);
    $dBr = mysqli_fetch_assoc($rBr);
    $branchName = ($language == 'ar' ? ($dBr['name_ar'] ?? '') : ($dBr['name_en'] ?? '')) ?: ($dBr['name_ar'] ?? 'الكل');
} else {
    $branchName = 'الكل';
}

if (!empty($fr_date)) {
    $safeFDate = mysqli_real_escape_string($adController->MySQL, $fr_date);
    $conditionSales .= " AND s.invoice_date >= '$safeFDate 00:00:00'";
    $conditionRet   .= " AND sr.invoice_date >= '$safeFDate 00:00:00'";
}
if (!empty($to_date)) {
    $safeTDate = mysqli_real_escape_string($adController->MySQL, $to_date);
    $conditionSales .= " AND s.invoice_date <= '$safeTDate 23:59:59'";
    $conditionRet   .= " AND sr.invoice_date <= '$safeTDate 23:59:59'";
}

// Fetch Sales Invoices
$salesList = [];
$querySales = "SELECT s.*, 
                      c.name_ar AS cust_name_ar, c.name_en AS cust_name_en, 
                      b.name_ar AS branch_name_ar, b.name_en AS branch_name_en
               FROM sales s
               LEFT JOIN customers c ON c.id = s.customer AND (c.storeid = '$storeid' OR c.storeid = '0')
               LEFT JOIN branches b ON b.id = s.branch AND (b.storeid = '$storeid' OR b.storeid = '0')
               WHERE (s.storeid = '$storeid' OR s.storeid = '0') AND s.state = '1' $conditionSales
               ORDER BY s.id DESC";
$resSales = mysqli_query($adController->MySQL, $querySales);
if ($resSales) {
    while ($row = mysqli_fetch_assoc($resSales)) {
        $salesList[] = $row;
        $salesCount++;
        $gross     = round((float)($row['gross_total'] ?? 0), 2);
        $dis_added = round((float)($row['dis_added'] ?? 0), 2);
        $vat_all   = round((float)($row['vat_all'] ?? 0), 2);
        $all_total = round((float)($row['all_total'] ?? 0), 2);
        $isCash    = ((int)($row['paymentType'] ?? 0) === 1 || (float)($row['left'] ?? 0) <= 0);
        $paid      = $isCash ? $all_total : ((float)($row['cash'] ?? 0) + (float)($row['visa'] ?? 0) + (float)($row['mada'] ?? 0));
        $left      = (float)($row['left'] ?? ($all_total - $paid));

        $grossTotal += $gross;
        $discTotal  += $dis_added;
        $vatTotal   += $vat_all;
        $salesTotal += $all_total;
        $paidTotal  += $paid;
        $leftTotal  += $left;
    }
}

// Fetch Returned Sales Invoices
$retList = [];
$queryRet = "SELECT sr.*, 
                    c.name_ar AS cust_name_ar, c.name_en AS cust_name_en, 
                    b.name_ar AS branch_name_ar, b.name_en AS branch_name_en
             FROM sales_ret sr
             LEFT JOIN customers c ON c.id = sr.customer AND (c.storeid = '$storeid' OR c.storeid = '0')
             LEFT JOIN branches b ON b.id = sr.branch AND (b.storeid = '$storeid' OR b.storeid = '0')
             WHERE (sr.storeid = '$storeid' OR sr.storeid = '0') AND sr.state = '1' $conditionRet
             ORDER BY sr.id DESC";
$resRet = mysqli_query($adController->MySQL, $queryRet);
if ($resRet) {
    while ($rowRet = mysqli_fetch_assoc($resRet)) {
        $retList[] = $rowRet;
        $retCount++;
        $all_total = round((float)($rowRet['all_total'] ?? 0), 2);
        $reSalesTotal += $all_total;
    }
}

$netSalesTotal = $salesTotal - $reSalesTotal;

// Refresh PDF Jasper cache in report_saleinvoice & report_saleinvoice_all
mysqli_query($adController->MySQL, "DELETE FROM report_saleinvoice");
mysqli_query($adController->MySQL, "DELETE FROM report_saleinvoice_all");

foreach ($salesList as $row) {
    $invNo    = mysqli_real_escape_string($adController->MySQL, $row['invoicenumber'] ?? '');
    $invDate  = mysqli_real_escape_string($adController->MySQL, $row['invoice_date'] ?? '');
    $custTxt  = ($language == 'ar' ? ($row['cust_name_ar'] ?? '') : ($row['cust_name_en'] ?? '')) ?: ($row['cust_name_ar'] ?? '-----');
    $safeCust = mysqli_real_escape_string($adController->MySQL, $custTxt);
    $gross    = (float)($row['gross_total'] ?? 0);
    $dis_add  = (float)($row['dis_added'] ?? 0);
    $vat_all  = (float)($row['vat_all'] ?? 0);
    $all_tot  = (float)($row['all_total'] ?? 0);
    $isCash   = ((int)($row['paymentType'] ?? 0) === 1 || (float)($row['left'] ?? 0) <= 0);
    $paid     = $isCash ? $all_tot : ((float)($row['cash'] ?? 0) + (float)($row['visa'] ?? 0) + (float)($row['mada'] ?? 0));
    $left     = (float)($row['left'] ?? ($all_tot - $paid));

    $qIns = "INSERT INTO `report_saleinvoice` 
            (`invoice_date`, `invoicenumber`, `supplier`, `gross_total`, `dis_added`, `vat_all`, `all_total`, `paid`, `left`, `cost`, `profit`) 
            VALUES ('$invDate', '$invNo', '$safeCust', '$gross', '$dis_add', '$vat_all', '$all_tot', '$paid', '$left', '', '')";
    mysqli_query($adController->MySQL, $qIns);
}

$safeFdate   = mysqli_real_escape_string($adController->MySQL, $fr_date);
$safeTdate   = mysqli_real_escape_string($adController->MySQL, $to_date);
$safeBranch  = mysqli_real_escape_string($adController->MySQL, $branchName);
$safeCustName = mysqli_real_escape_string($adController->MySQL, $custName);
$allGrossFmt = flout_format($grossTotal);
$allDiscFmt  = flout_format($discTotal);
$allVatFmt   = flout_format($vatTotal);
$allSalesFmt = flout_format($salesTotal);
$allReSalesF = flout_format($reSalesTotal);
$netFmt      = flout_format($netSalesTotal);
$allPaidFmt  = flout_format($paidTotal);
$allLeftFmt  = flout_format($leftTotal);

$qSummary = "INSERT INTO `report_saleinvoice_all` 
             (`from`, `to`, `branch`, `supplier`, `grossTotal`, `discTotal`, `vatTotal`, `salesTotal`, `reSalesTotal`, `netTotal`, `payTotal`, `leftTotal`) 
             VALUES ('$safeFdate', '$safeTdate', '$safeBranch', '$safeCustName', '$allGrossFmt', '$allDiscFmt', '$allVatFmt', '$allSalesFmt', '$allReSalesF', '$netFmt', '$allPaidFmt', '$allLeftFmt')";
mysqli_query($adController->MySQL, $qSummary);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تقرير فواتير المبيعات الشامل - ERP WAM Tech</title>
    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <style>
        .filter-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
            padding: 20px;
            margin-bottom: 24px;
        }
        .stat-card-clean {
            background: #fff;
            border-radius: 12px;
            padding: 16px 20px;
            border: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            transition: transform 0.2s;
        }
        .stat-card-clean:hover {
            transform: translateY(-2px);
        }
        .stat-card-clean .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .stat-card-clean .stat-label {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 4px;
        }
        .stat-card-clean .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
        }
        .table-pro-wrap {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
            padding: 20px;
            margin-bottom: 24px;
        }
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        @media print {
            .admin-sidebar, .admin-navbar, .filter-card, .btn-no-print {
                display: none !important;
            }
            .admin-content {
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php require_once("components/sidebar_pro.php"); ?>
        <div style="flex: 1; display: flex; flex-direction: column;">
            <?php require_once("components/header_pro.php"); ?>
            <main class="admin-content">
                
                <!-- Page Breadcrumbs & Actions -->
                <div class="page-header d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="page-title fs-4 fw-bold mb-1">
                            <i class="fas fa-file-invoice text-success me-2"></i>
                            تقرير فواتير المبيعات الشامل
                        </h1>
                        <div class="page-breadcrumb text-muted fs-7">
                            <a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-home"></i> الرئيسية</a>
                            <span class="mx-2">/</span>
                            <a href="saleInvoice.php" class="text-decoration-none text-muted">المبيعات</a>
                            <span class="mx-2">/</span>
                            <span class="text-success fw-semibold">تقرير فواتير المبيعات</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 btn-no-print">
                        <a href="reports/saleInvoiceReport.php" target="_blank" class="btn btn-outline-danger">
                            <i class="fas fa-file-pdf me-1"></i> طباعة PDF
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="fas fa-print me-1"></i> طباعة فورية
                        </button>
                    </div>
                </div>

                <!-- Filter Card -->
                <div class="filter-card btn-no-print">
                    <form method="GET" action="<?=htmlspecialchars($pgName)?>" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">العميل</label>
                            <select name="customer" class="form-select">
                                <option value="">-- كافة العملاء --</option>
                                <?php
                                $qC = "SELECT id, name_ar, name_en FROM customers WHERE (storeid='$storeid' OR storeid='0') ORDER BY name_ar ASC";
                                $rC = mysqli_query($adController->MySQL, $qC);
                                if ($rC) {
                                    while ($c = mysqli_fetch_assoc($rC)) {
                                        $cName = ($language == 'ar' ? $c['name_ar'] : $c['name_en']) ?: $c['name_ar'];
                                        $sel = ($selectedCustomer == $c['id']) ? 'selected' : '';
                                        echo "<option value='{$c['id']}' $sel>" . htmlspecialchars($cName) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">الفرع</label>
                            <select name="branch" class="form-select">
                                <option value="">-- كافة الفروع --</option>
                                <?php
                                $qB = "SELECT id, name_ar, name_en FROM branches WHERE (storeid='$storeid' OR storeid='0') ORDER BY name_ar ASC";
                                $rB = mysqli_query($adController->MySQL, $qB);
                                if ($rB) {
                                    while ($b = mysqli_fetch_assoc($rB)) {
                                        $bName = ($language == 'ar' ? $b['name_ar'] : $b['name_en']) ?: $b['name_ar'];
                                        $sel = ($selectedBranch == $b['id']) ? 'selected' : '';
                                        echo "<option value='{$b['id']}' $sel>" . htmlspecialchars($bName) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold fs-7 text-secondary">من تاريخ</label>
                            <input type="date" name="fr_date" class="form-control" value="<?=htmlspecialchars($fr_date)?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold fs-7 text-secondary">إلى تاريخ</label>
                            <input type="date" name="to_date" class="form-control" value="<?=htmlspecialchars($to_date)?>">
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> تصفية
                            </button>
                            <a href="<?=htmlspecialchars($pgName)?>" class="btn btn-light" title="إعادة ضبط">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- KPI / Totals Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">إجمالي فواتير المبيعات</div>
                                <div class="stat-value text-primary"><?=flout_format($salesTotal)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1"><?=$salesCount?> فاتورة بيع</div>
                            </div>
                            <div class="stat-icon bg-primary-subtle text-primary">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">إجمالي المرتجعات</div>
                                <div class="stat-value text-danger"><?=flout_format($reSalesTotal)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1"><?=$retCount?> إشعار مرتجع</div>
                            </div>
                            <div class="stat-icon bg-danger-subtle text-danger">
                                <i class="fas fa-undo-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">صافي المبيعات الفعلي</div>
                                <div class="stat-value text-success"><?=flout_format($netSalesTotal)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1">بعد استبعاد المرتجعات</div>
                            </div>
                            <div class="stat-icon bg-success-subtle text-success">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">المتبقي / الآجل على العملاء</div>
                                <div class="stat-value text-warning"><?=flout_format($leftTotal)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1">المحصل: <?=flout_format($paidTotal)?> <?=$currency?></div>
                            </div>
                            <div class="stat-icon bg-warning-subtle text-warning">
                                <i class="fas fa-user-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secondary Financial KPI Strip -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-white border rounded-3 d-flex justify-content-between align-items-center">
                            <span class="text-secondary fs-7"><i class="fas fa-calculator text-muted me-1"></i> إجمالي قبل الضريبة والخصم:</span>
                            <span class="fw-bold text-dark"><?=flout_format($grossTotal)?> <?=$currency?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white border rounded-3 d-flex justify-content-between align-items-center">
                            <span class="text-secondary fs-7"><i class="fas fa-percentage text-danger me-1"></i> إجمالي ضريبة القيمة المضافة 14%:</span>
                            <span class="fw-bold text-danger"><?=flout_format($vatTotal)?> <?=$currency?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white border rounded-3 d-flex justify-content-between align-items-center">
                            <span class="text-secondary fs-7"><i class="fas fa-tag text-info me-1"></i> إجمالي الخصومات الممنوحة:</span>
                            <span class="fw-bold text-info"><?=flout_format($discTotal)?> <?=$currency?></span>
                        </div>
                    </div>
                </div>

                <!-- Sales Invoices Table -->
                <div class="table-pro-wrap">
                    <div class="section-header">
                        <h2 class="fs-5 fw-bold mb-0 text-dark">
                            <i class="fas fa-file-invoice text-success me-2"></i>
                            فواتير المبيعات الصادرة
                        </h2>
                        <span class="badge bg-light text-success border px-3 py-2">عدد الفواتير: <?=$salesCount?></span>
                    </div>

                    <div class="table-responsive">
                        <table id="salesReportTable" class="table table-hover align-middle table-striped table-bordered text-center w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>رقم الفاتورة</th>
                                    <th>التاريخ</th>
                                    <th>العميل</th>
                                    <th>الفرع</th>
                                    <th>الإجمالي قبل الضريبة</th>
                                    <th>الضريبة 14%</th>
                                    <th>الخصم</th>
                                    <th>إجمالي الفاتورة</th>
                                    <th>طريقة السداد</th>
                                    <th>المحصل</th>
                                    <th>المتبقي</th>
                                    <th class="btn-no-print">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $idx = 0;
                                foreach ($salesList as $row):
                                    $idx++;
                                    $invNo    = $row['invoicenumber'] ?? '';
                                    $invDate  = substr($row['invoice_date'] ?? '', 0, 16);
                                    $cName    = ($language == 'ar' ? ($row['cust_name_ar'] ?? '') : ($row['cust_name_en'] ?? '')) ?: ($row['cust_name_ar'] ?? 'عميل نقدي');
                                    $bName    = ($language == 'ar' ? ($row['branch_name_ar'] ?? '') : ($row['branch_name_en'] ?? '')) ?: ($row['branch_name_ar'] ?? 'الفرع الرئيسي');
                                    $gross    = (float)($row['gross_total'] ?? 0);
                                    $vatAll   = (float)($row['vat_all'] ?? 0);
                                    $disAdd   = (float)($row['dis_added'] ?? 0);
                                    $allTot   = (float)($row['all_total'] ?? 0);
                                    $isCash   = ((int)($row['paymentType'] ?? 0) === 1 || (float)($row['left'] ?? 0) <= 0);
                                    $paid     = $isCash ? $allTot : ((float)($row['cash'] ?? 0) + (float)($row['visa'] ?? 0) + (float)($row['mada'] ?? 0));
                                    $left     = (float)($row['left'] ?? ($allTot - $paid));
                                ?>
                                <tr>
                                    <td><?=$idx?></td>
                                    <td>
                                        <a href="edit_saleInvoice.php?sd=<?=urlencode($invNo)?>" class="fw-bold text-primary text-decoration-none">
                                            <?=htmlspecialchars($invNo)?>
                                        </a>
                                    </td>
                                    <td class="text-muted fs-7"><?=htmlspecialchars($invDate)?></td>
                                    <td class="fw-semibold text-dark"><?=htmlspecialchars($cName)?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?=htmlspecialchars($bName)?></span></td>
                                    <td><?=flout_format($gross)?></td>
                                    <td class="text-danger"><?=flout_format($vatAll)?></td>
                                    <td class="text-info"><?=flout_format($disAdd)?></td>
                                    <td class="fw-bold text-dark"><?=flout_format($allTot)?></td>
                                    <td>
                                        <?php if ($isCash): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">نقدي</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">آجل</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-success"><?=flout_format($paid)?></td>
                                    <td class="<?=$left > 0 ? 'text-danger fw-bold' : 'text-muted'?>"><?=flout_format($left)?></td>
                                    <td class="btn-no-print">
                                        <div class="btn-group btn-group-sm">
                                            <a href="reports/sales.php?sd=<?=urlencode($invNo)?>" target="_blank" class="btn btn-outline-secondary" title="طباعة الفاتورة A4">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="edit_saleInvoice.php?sd=<?=urlencode($invNo)?>" class="btn btn-outline-primary" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Returns Invoices Table -->
                <?php if (!empty($retList)): ?>
                <div class="table-pro-wrap">
                    <div class="section-header">
                        <h2 class="fs-5 fw-bold mb-0 text-danger">
                            <i class="fas fa-undo-alt me-2"></i>
                            مرتجع فواتير المبيعات
                        </h2>
                        <span class="badge bg-danger-subtle text-danger border px-3 py-2">عدد المرتجعات: <?=$retCount?></span>
                    </div>

                    <div class="table-responsive">
                        <table id="retReportTable" class="table table-hover align-middle table-striped table-bordered text-center w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>رقم الإشعار</th>
                                    <th>فاتورة البيع الأصلية</th>
                                    <th>التاريخ</th>
                                    <th>العميل</th>
                                    <th>الفرع</th>
                                    <th>الإجمالي قبل الضريبة</th>
                                    <th>الضريبة 14%</th>
                                    <th>إجمالي المرتجع</th>
                                    <th>التسوية</th>
                                    <th class="btn-no-print">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rIdx = 0;
                                foreach ($retList as $rowRet):
                                    $rIdx++;
                                    $invNo   = $rowRet['invoicenumber'] ?? '';
                                    $sInv    = $rowRet['salesInvoice'] ?? '---';
                                    $invDate = substr($rowRet['invoice_date'] ?? '', 0, 16);
                                    $cName   = ($language == 'ar' ? ($rowRet['cust_name_ar'] ?? '') : ($rowRet['cust_name_en'] ?? '')) ?: ($rowRet['cust_name_ar'] ?? 'عميل نقدي');
                                    $bName   = ($language == 'ar' ? ($rowRet['branch_name_ar'] ?? '') : ($rowRet['branch_name_en'] ?? '')) ?: ($rowRet['branch_name_ar'] ?? 'الفرع الرئيسي');
                                    $gross   = (float)($rowRet['gross_total'] ?? 0);
                                    $vatAll  = (float)($rowRet['vat_all'] ?? 0);
                                    $allTot  = (float)($rowRet['all_total'] ?? 0);
                                    $isCash  = ((int)($rowRet['paymentType'] ?? 0) === 1);
                                ?>
                                <tr>
                                    <td><?=$rIdx?></td>
                                    <td>
                                        <a href="edit_salesRet.php?sd=<?=urlencode($invNo)?>" class="fw-bold text-danger text-decoration-none">
                                            <?=htmlspecialchars($invNo)?>
                                        </a>
                                    </td>
                                    <td><?=htmlspecialchars($sInv)?></td>
                                    <td class="text-muted fs-7"><?=htmlspecialchars($invDate)?></td>
                                    <td class="fw-semibold text-dark"><?=htmlspecialchars($cName)?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?=htmlspecialchars($bName)?></span></td>
                                    <td><?=flout_format($gross)?></td>
                                    <td class="text-danger"><?=flout_format($vatAll)?></td>
                                    <td class="fw-bold text-danger"><?=flout_format($allTot)?></td>
                                    <td>
                                        <?php if ($isCash): ?>
                                            <span class="badge bg-success-subtle text-success">استرداد نقدي</span>
                                        <?php else: ?>
                                            <span class="badge bg-info-subtle text-info">تخفيض رصيد العميل</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="btn-no-print">
                                        <a href="reports/reSales.php?sd=<?=urlencode($invNo)?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="طباعة">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <?php require_once("footer.php"); ?>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            var dtLang = {
                "sProcessing": "جاري التحميل...",
                "sLengthMenu": "عرض _MENU_ سجل لكل صفحة",
                "sZeroRecords": "لم يتم العثور على أية نتائج",
                "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ سجل",
                "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                "sInfoFiltered": "(منتقاة من مجموع _MAX_ سجل)",
                "sSearch": "بحث سريع:",
                "oPaginate": {
                    "sFirst": "الأول",
                    "sPrevious": "السابق",
                    "sNext": "التالي",
                    "sLast": "الأخير"
                }
            };

            $('#salesReportTable').DataTable({
                language: dtLang,
                dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
                buttons: [
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel text-success me-1"></i> تصدير Excel', className: 'btn btn-outline-success btn-sm' },
                    { extend: 'print', text: '<i class="fas fa-print me-1"></i> طباعة الجدول', className: 'btn btn-outline-secondary btn-sm' }
                ],
                order: [[0, 'asc']]
            });

            if ($('#retReportTable').length) {
                $('#retReportTable').DataTable({
                    language: dtLang,
                    dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
                    buttons: [
                        { extend: 'excelHtml5', text: '<i class="fas fa-file-excel text-success me-1"></i> تصدير Excel', className: 'btn btn-outline-success btn-sm' },
                        { extend: 'print', text: '<i class="fas fa-print me-1"></i> طباعة الجدول', className: 'btn btn-outline-secondary btn-sm' }
                    ],
                    order: [[0, 'asc']]
                });
            }
        });
    </script>
</body>
</html>
