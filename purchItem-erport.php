<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid  = (int)($_SESSION['storeid'] ?? 1);
$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';
$pgName   = basename(__FILE__);

// Filters
$selectedCat      = (int)($_REQUEST['catid'] ?? 0);
$selectedItem     = trim($_REQUEST['iid'] ?? '');
$selectedSupplier = (int)($_REQUEST['supplier'] ?? 0);
$selectedBranch   = (int)($_REQUEST['branch'] ?? 0);
$fr_date          = trim($_REQUEST['fr_date'] ?? date('Y-m-01'));
$to_date          = trim($_REQUEST['to_date'] ?? date('Y-m-d'));

$conditionPurch = "";
$conditionRet   = "";
$conditionItem  = "";

if ($selectedCat > 0) {
    $conditionItem .= " AND i.catid = '$selectedCat'";
    $qC = "SELECT name_ar, name_en FROM categories WHERE id='$selectedCat' AND storeid='$storeid' LIMIT 1";
    $rC = mysqli_query($adController->MySQL, $qC);
    $dC = mysqli_fetch_assoc($rC);
    $catName = ($language == 'ar' ? ($dC['name_ar'] ?? '') : ($dC['name_en'] ?? '')) ?: ($dC['name_ar'] ?? 'الكل');
} else {
    $catName = 'الكل';
}

if (!empty($selectedItem)) {
    $safeItemThread = mysqli_real_escape_string($adController->MySQL, $selectedItem);
    $conditionItem .= " AND i.item_thread = '$safeItemThread'";
    $qI = "SELECT name_ar, name_en FROM items WHERE item_thread='$safeItemThread' AND storeid='$storeid' LIMIT 1";
    $rI = mysqli_query($adController->MySQL, $qI);
    $dI = mysqli_fetch_assoc($rI);
    $itemName = ($language == 'ar' ? ($dI['name_ar'] ?? '') : ($dI['name_en'] ?? '')) ?: ($dI['name_ar'] ?? 'الكل');
} else {
    $itemName = 'الكل';
}

if ($selectedSupplier > 0) {
    $conditionPurch .= " AND p.supplier = '$selectedSupplier'";
    $conditionRet   .= " AND pr.supplier = '$selectedSupplier'";
    $qSupp = "SELECT name_ar, name_en FROM suppliers WHERE id='$selectedSupplier' AND storeid='$storeid' LIMIT 1";
    $rSupp = mysqli_query($adController->MySQL, $qSupp);
    $dSupp = mysqli_fetch_assoc($rSupp);
    $suppName = ($language == 'ar' ? ($dSupp['name_ar'] ?? '') : ($dSupp['name_en'] ?? '')) ?: ($dSupp['name_ar'] ?? 'الكل');
} else {
    $suppName = 'الكل';
}

if ($selectedBranch > 0) {
    $conditionPurch .= " AND p.branch = '$selectedBranch'";
    $conditionRet   .= " AND pr.branch = '$selectedBranch'";
    $qBr = "SELECT name_ar, name_en FROM branches WHERE id='$selectedBranch' AND storeid='$storeid' LIMIT 1";
    $rBr = mysqli_query($adController->MySQL, $qBr);
    $dBr = mysqli_fetch_assoc($rBr);
    $branchName = ($language == 'ar' ? ($dBr['name_ar'] ?? '') : ($dBr['name_en'] ?? '')) ?: ($dBr['name_ar'] ?? 'الكل');
} else {
    $branchName = 'الكل';
}

if (!empty($fr_date)) {
    $safeFDate = mysqli_real_escape_string($adController->MySQL, $fr_date);
    $conditionPurch .= " AND p.purchase_date >= '$safeFDate 00:00:00'";
    $conditionRet   .= " AND pr.purchase_date >= '$safeFDate 00:00:00'";
}
if (!empty($to_date)) {
    $safeTDate = mysqli_real_escape_string($adController->MySQL, $to_date);
    $conditionPurch .= " AND p.purchase_date <= '$safeTDate 23:59:59'";
    $conditionRet   .= " AND pr.purchase_date <= '$safeTDate 23:59:59'";
}

$grossTotal   = 0.0;
$discTotal    = 0.0;
$vatTotal     = 0.0;
$purchTotal   = 0.0;
$rePurchTotal = 0.0;
$purchItemRows = [];
$retItemRows   = [];

// Fetch Purchased Items Aggregation
$queryPurchItems = "SELECT 
                        pi.itemid,
                        i.barcode,
                        i.name_ar, i.name_en,
                        u.name_ar AS unit_ar, u.name_en AS unit_en,
                        SUM(pi.quantity) AS total_qty,
                        AVG(pi.cost) AS avg_price,
                        SUM(pi.cost * pi.quantity) AS gross_total,
                        SUM(pi.cost * pi.quantity * pi.discount / 100) AS total_disc,
                        SUM((pi.cost * pi.quantity - (pi.cost * pi.quantity * pi.discount / 100)) * pi.tax / 100) AS total_vat,
                        SUM((pi.cost * pi.quantity - (pi.cost * pi.quantity * pi.discount / 100)) * (1 + pi.tax / 100)) AS net_total
                    FROM purchase_items pi
                    JOIN purchase p ON p.invoicenumber = pi.invoice_No AND p.storeid = '$storeid' AND p.state = '1' $conditionPurch
                    JOIN items i ON i.id = pi.itemid AND i.storeid = '$storeid' $conditionItem
                    LEFT JOIN units u ON u.id = i.unit AND (u.storeid = '$storeid' OR u.storeid = '0')
                    WHERE pi.state = '1'
                    GROUP BY pi.itemid
                    ORDER BY i.name_ar ASC";
$resPurchItems = mysqli_query($adController->MySQL, $queryPurchItems);
if ($resPurchItems) {
    while ($r = mysqli_fetch_assoc($resPurchItems)) {
        $purchItemRows[] = $r;
        $grossTotal += (float)($r['gross_total'] ?? 0);
        $discTotal  += (float)($r['total_disc'] ?? 0);
        $vatTotal   += (float)($r['total_vat'] ?? 0);
        $purchTotal += (float)($r['net_total'] ?? 0);
    }
}

// Fetch Returned Items Aggregation
$queryRetPurch = "SELECT 
                    pri.itemid,
                    i.barcode,
                    i.name_ar, i.name_en,
                    u.name_ar AS unit_ar, u.name_en AS unit_en,
                    SUM(pri.quantity) AS total_qty,
                    AVG(pri.cost) AS avg_price,
                    SUM(pri.cost * pri.quantity) AS gross_total,
                    SUM(pri.cost * pri.quantity * pri.discount / 100) AS total_disc,
                    SUM((pri.cost * pri.quantity - (pri.cost * pri.quantity * pri.discount / 100)) * pri.tax / 100) AS total_vat,
                    SUM((pri.cost * pri.quantity - (pri.cost * pri.quantity * pri.discount / 100)) * (1 + pri.tax / 100)) AS net_total
                  FROM purchase_ret_items pri
                  JOIN purchaseRet pr ON pr.invoicenumber = pri.invoice_No AND pr.storeid = '$storeid' AND pr.state = '1' $conditionRet
                  JOIN items i ON i.id = pri.itemid AND i.storeid = '$storeid' $conditionItem
                  LEFT JOIN units u ON u.id = i.unit AND (u.storeid = '$storeid' OR u.storeid = '0')
                  WHERE pri.state = '1'
                  GROUP BY pri.itemid
                  ORDER BY i.name_ar ASC";
$resRetPurch = mysqli_query($adController->MySQL, $queryRetPurch);
if ($resRetPurch) {
    while ($rr = mysqli_fetch_assoc($resRetPurch)) {
        $retItemRows[] = $rr;
        $rePurchTotal += (float)($rr['net_total'] ?? 0);
    }
}

$netPurchTotal = $purchTotal - $rePurchTotal;

// Refresh PDF Jasper temporary cache in report_saleitem / report_saleitem_all
mysqli_query($adController->MySQL, "DELETE FROM report_saleitem");
mysqli_query($adController->MySQL, "DELETE FROM report_saleitem_all");

foreach ($purchItemRows as $rowPurch) {
    $itemId    = $rowPurch['itemid'];
    $barcode   = !empty($rowPurch['barcode']) ? $rowPurch['barcode'] : '-----';
    $name      = ($language == 'ar' ? $rowPurch['name_ar'] : $rowPurch['name_en']) ?: $rowPurch['name_ar'];
    $unit      = ($language == 'ar' ? ($rowPurch['unit_ar'] ?? '') : ($rowPurch['unit_en'] ?? '')) ?: ($rowPurch['unit_ar'] ?? '');
    $itsQty    = (float)$rowPurch['total_qty'];
    $priceItem = (float)$rowPurch['avg_price'];
    $grossItem = (float)$rowPurch['gross_total'];
    $discItem  = (float)$rowPurch['total_disc'];
    $vatItem   = (float)$rowPurch['total_vat'];
    $netItem   = (float)$rowPurch['net_total'];

    $safeName = mysqli_real_escape_string($adController->MySQL, $name);
    $safeUnit = mysqli_real_escape_string($adController->MySQL, $unit);
    $safeBar  = mysqli_real_escape_string($adController->MySQL, $barcode);

    $qIns = "INSERT INTO `report_saleitem` (`itemId`, `barcode`, `item`, `unit`, `quantity`, `price`, `discount`, `vat`, `total`, `cost`, `profit`) 
             VALUES ('$itemId', '$safeBar', '$safeName', '$safeUnit', '$itsQty', '$priceItem', '$discItem', '$vatItem', '$netItem', '', '')";
    mysqli_query($adController->MySQL, $qIns);
}

// Update return quantities in report_saleitem
foreach ($retItemRows as $rowRet) {
    $retItemId = $rowRet['itemid'];
    $retQty    = (float)$rowRet['total_qty'];
    $retNet    = (float)$rowRet['net_total'];
    $qUp = "UPDATE `report_saleitem` SET `retQuantity`='$retQty', `retTotal`='$retNet' WHERE `itemId`='$retItemId'";
    mysqli_query($adController->MySQL, $qUp);
}

$allGrossFmt  = flout_format($grossTotal);                
$allDiscFmt   = flout_format($discTotal);            
$allVatFmt    = flout_format($vatTotal);     
$allPurchFmt  = flout_format($purchTotal);
$allRePurchF  = flout_format($rePurchTotal); 
$netFmt       = flout_format($netPurchTotal);

$safeFdate  = mysqli_real_escape_string($adController->MySQL, $fr_date);
$safeTdate  = mysqli_real_escape_string($adController->MySQL, $to_date);
$safeBranch = mysqli_real_escape_string($adController->MySQL, $branchName);
$safeSupp   = mysqli_real_escape_string($adController->MySQL, $suppName);
$safeCat    = mysqli_real_escape_string($adController->MySQL, $catName);
$safeItem   = mysqli_real_escape_string($adController->MySQL, $itemName);

$querySummary = "INSERT INTO `report_saleitem_all` 
                 (`from`, `to`, `branch`, `customer`, `category`, `item`, `grossTotal`, `discTotal`, `vatTotal`, `salesTotal`, `reSalesTotal`, `netTotal`) 
                 VALUES ('$safeFdate','$safeTdate','$safeBranch','$safeSupp','$safeCat','$safeItem','$allGrossFmt','$allDiscFmt','$allVatFmt','$allPurchFmt','$allRePurchF','$netFmt')";
mysqli_query($adController->MySQL, $querySummary);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تقرير مشتريات الأصناف - ERP WAM Tech</title>
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
                            <i class="fas fa-boxes text-primary me-2"></i>
                            تقرير مشتريات الأصناف وحركتها
                        </h1>
                        <div class="page-breadcrumb text-muted fs-7">
                            <a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-home"></i> الرئيسية</a>
                            <span class="mx-2">/</span>
                            <a href="purchase.php" class="text-decoration-none text-muted">المشتريات</a>
                            <span class="mx-2">/</span>
                            <span class="text-primary fw-semibold">تقرير حركة الأصناف</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 btn-no-print">
                        <a href="reports/purchItemReport.php" target="_blank" class="btn btn-outline-danger">
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
                            <label class="form-label fw-semibold fs-7 text-secondary">القسم / التصنيف</label>
                            <select name="catid" class="form-select" onchange="this.form.submit()">
                                <option value="">-- كافة الأقسام --</option>
                                <?php
                                $qCat = "SELECT id, name_ar, name_en FROM categories WHERE storeid='$storeid' ORDER BY name_ar ASC";
                                $rCat = mysqli_query($adController->MySQL, $qCat);
                                if ($rCat) {
                                    while ($c = mysqli_fetch_assoc($rCat)) {
                                        $cName = ($language == 'ar' ? $c['name_ar'] : $c['name_en']) ?: $c['name_ar'];
                                        $sel = ($selectedCat == $c['id']) ? 'selected' : '';
                                        echo "<option value='{$c['id']}' $sel>" . htmlspecialchars($cName) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">الصنف المختار</label>
                            <select name="iid" class="form-select">
                                <option value="">-- كافة الأصناف --</option>
                                <?php
                                $catSearch = ($selectedCat > 0) ? " AND catid = '$selectedCat' " : "";
                                $queryItems = "SELECT i.id, i.item_thread, i.barcode, i.name_ar, i.name_en, u.name_ar AS unit_ar, u.name_en AS unit_en
                                               FROM items i
                                               LEFT JOIN units u ON u.id = i.unit AND (u.storeid = '$storeid' OR u.storeid = '0')
                                               WHERE i.storeid = '$storeid' $catSearch
                                               GROUP BY i.item_thread
                                               ORDER BY i.name_ar ASC";
                                $resItems = mysqli_query($adController->MySQL, $queryItems);
                                if ($resItems) {
                                    while ($it = mysqli_fetch_assoc($resItems)) {
                                        $bcode = !empty($it['barcode']) ? $it['barcode'] : '---';
                                        $iN    = ($language == 'ar' ? $it['name_ar'] : $it['name_en']) ?: $it['name_ar'];
                                        $uN    = ($language == 'ar' ? ($it['unit_ar'] ?? '') : ($it['unit_en'] ?? '')) ?: ($it['unit_ar'] ?? '');
                                        $txt   = "{$bcode} | {$iN}" . ($uN ? " ({$uN})" : "");
                                        $sel   = ($selectedItem === $it['item_thread']) ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($it['item_thread']) . "' $sel>" . htmlspecialchars($txt) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">المورد</label>
                            <select name="supplier" class="form-select">
                                <option value="">-- كافة الموردين --</option>
                                <?php
                                $qS = "SELECT id, name_ar, name_en FROM suppliers WHERE storeid='$storeid' ORDER BY name_ar ASC";
                                $rS = mysqli_query($adController->MySQL, $qS);
                                if ($rS) {
                                    while ($s = mysqli_fetch_assoc($rS)) {
                                        $sName = ($language == 'ar' ? $s['name_ar'] : $s['name_en']) ?: $s['name_ar'];
                                        $sel = ($selectedSupplier == $s['id']) ? 'selected' : '';
                                        echo "<option value='{$s['id']}' $sel>" . htmlspecialchars($sName) . "</option>";
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
                                $qB = "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' ORDER BY name_ar ASC";
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
                        <div class="col-md-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">من تاريخ</label>
                            <input type="date" name="fr_date" class="form-control" value="<?=htmlspecialchars($fr_date)?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">إلى تاريخ</label>
                            <input type="date" name="to_date" class="form-control" value="<?=htmlspecialchars($to_date)?>">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> عرض التقرير
                            </button>
                            <a href="<?=htmlspecialchars($pgName)?>" class="btn btn-light" title="إعادة ضبط">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">إجمالي مشتريات الأصناف</div>
                                <div class="stat-value text-primary"><?=flout_format($purchTotal)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1"><?=count($purchItemRows)?> صنف مشترى</div>
                            </div>
                            <div class="stat-icon bg-primary-subtle text-primary">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">إجمالي مرتجعات الأصناف</div>
                                <div class="stat-value text-danger"><?=flout_format($rePurchTotal)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1"><?=count($retItemRows)?> صنف مرتجع</div>
                            </div>
                            <div class="stat-icon bg-danger-subtle text-danger">
                                <i class="fas fa-dolly-flatbed"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">صافي تكلفة المشتريات</div>
                                <div class="stat-value text-success"><?=flout_format($netPurchTotal)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1">بعد استبعاد المرتجع</div>
                            </div>
                            <div class="stat-icon bg-success-subtle text-success">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">ضريبة القيمة المضافة 14%</div>
                                <div class="stat-value text-warning"><?=flout_format($vatTotal)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1">خصومات: <?=flout_format($discTotal)?> <?=$currency?></div>
                            </div>
                            <div class="stat-icon bg-warning-subtle text-warning">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchased Items Table -->
                <div class="table-pro-wrap">
                    <div class="section-header">
                        <h2 class="fs-5 fw-bold mb-0 text-dark">
                            <i class="fas fa-cubes text-primary me-2"></i>
                            تفاصيل الأصناف المشتراة
                        </h2>
                        <span class="badge bg-light text-primary border px-3 py-2">عدد الأصناف: <?=count($purchItemRows)?></span>
                    </div>

                    <div class="table-responsive">
                        <table id="purchItemsTable" class="table table-hover align-middle table-striped table-bordered text-center w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>اسم الصنف</th>
                                    <th>الوحدة</th>
                                    <th>الباركود</th>
                                    <th>الكمية المشتراة</th>
                                    <th>متوسط سعر الشراء</th>
                                    <th>الإجمالي قبل الضريبة</th>
                                    <th>إجمالي الخصم</th>
                                    <th>ضريبة 14%</th>
                                    <th>الإجمالي الصافي</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($purchItemRows as $rowPurch):
                                    $i++;
                                    $barcode   = !empty($rowPurch['barcode']) ? $rowPurch['barcode'] : '-----';
                                    $name      = ($language == 'ar' ? $rowPurch['name_ar'] : $rowPurch['name_en']) ?: $rowPurch['name_ar'];
                                    $unit      = ($language == 'ar' ? ($rowPurch['unit_ar'] ?? '') : ($rowPurch['unit_en'] ?? '')) ?: ($rowPurch['unit_ar'] ?? 'قطعة');
                                    $itsQty    = (float)$rowPurch['total_qty'];
                                    $priceItem = (float)$rowPurch['avg_price'];
                                    $grossItem = (float)$rowPurch['gross_total'];
                                    $discItem  = (float)$rowPurch['total_disc'];
                                    $vatItem   = (float)$rowPurch['total_vat'];
                                    $netItem   = (float)$rowPurch['net_total'];
                                ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td class="fw-bold text-dark text-start"><?=htmlspecialchars($name)?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?=htmlspecialchars($unit)?></span></td>
                                    <td><code><?=htmlspecialchars($barcode)?></code></td>
                                    <td class="fw-bold text-primary"><?=flout_format($itsQty)?></td>
                                    <td><?=flout_format($priceItem)?></td>
                                    <td><?=flout_format($grossItem)?></td>
                                    <td class="text-info"><?=flout_format($discItem)?></td>
                                    <td class="text-danger"><?=flout_format($vatItem)?></td>
                                    <td class="fw-bold text-success"><?=flout_format($netItem)?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Returned Items Table -->
                <?php if (!empty($retItemRows)): ?>
                <div class="table-pro-wrap">
                    <div class="section-header">
                        <h2 class="fs-5 fw-bold mb-0 text-danger">
                            <i class="fas fa-undo-alt me-2"></i>
                            تفاصيل الأصناف المرتجعة
                        </h2>
                        <span class="badge bg-danger-subtle text-danger border px-3 py-2">عدد الأصناف: <?=count($retItemRows)?></span>
                    </div>

                    <div class="table-responsive">
                        <table id="retItemsTable" class="table table-hover align-middle table-striped table-bordered text-center w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>اسم الصنف</th>
                                    <th>الوحدة</th>
                                    <th>الباركود</th>
                                    <th>الكمية المرتجعة</th>
                                    <th>متوسط السعر</th>
                                    <th>الإجمالي قبل الضريبة</th>
                                    <th>الخصم</th>
                                    <th>الضريبة 14%</th>
                                    <th>إجمالي المرتجع</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $j = 0;
                                foreach ($retItemRows as $rowRet):
                                    $j++;
                                    $barcode   = !empty($rowRet['barcode']) ? $rowRet['barcode'] : '-----';
                                    $name      = ($language == 'ar' ? $rowRet['name_ar'] : $rowRet['name_en']) ?: $rowRet['name_ar'];
                                    $unit      = ($language == 'ar' ? ($rowRet['unit_ar'] ?? '') : ($rowRet['unit_en'] ?? '')) ?: ($rowRet['unit_ar'] ?? 'قطعة');
                                    $itsQty    = (float)$rowRet['total_qty'];
                                    $priceItem = (float)$rowRet['avg_price'];
                                    $grossItem = (float)$rowRet['gross_total'];
                                    $discItem  = (float)$rowRet['total_disc'];
                                    $vatItem   = (float)$rowRet['total_vat'];
                                    $netItem   = (float)$rowRet['net_total'];
                                ?>
                                <tr>
                                    <td><?=$j?></td>
                                    <td class="fw-bold text-danger text-start"><?=htmlspecialchars($name)?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?=htmlspecialchars($unit)?></span></td>
                                    <td><code><?=htmlspecialchars($barcode)?></code></td>
                                    <td class="fw-bold text-danger"><?=flout_format($itsQty)?></td>
                                    <td><?=flout_format($priceItem)?></td>
                                    <td><?=flout_format($grossItem)?></td>
                                    <td><?=flout_format($discItem)?></td>
                                    <td><?=flout_format($vatItem)?></td>
                                    <td class="fw-bold text-danger"><?=flout_format($netItem)?></td>
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

            $('#purchItemsTable').DataTable({
                language: dtLang,
                dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
                buttons: [
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel text-success me-1"></i> تصدير Excel', className: 'btn btn-outline-success btn-sm' },
                    { extend: 'print', text: '<i class="fas fa-print me-1"></i> طباعة الجدول', className: 'btn btn-outline-secondary btn-sm' }
                ],
                order: [[0, 'asc']]
            });

            if ($('#retItemsTable').length) {
                $('#retItemsTable').DataTable({
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
