<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid   = (int)($_SESSION['storeid'] ?? 0);
$language  = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$pgName    = basename(__FILE__);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    safe_query($adController->MySQL, "DELETE FROM report_inventory_sam");
    safe_query($adController->MySQL, "DELETE FROM report_inventory");
}

$selectedBr    = htmlspecialchars((string)($_REQUEST['br'] ?? ''));
$selectedStore = htmlspecialchars((string)($_REQUEST['store'] ?? ''));
$selectedCat   = htmlspecialchars((string)($_REQUEST['catid'] ?? ''));
$isSubmitted   = isset($_REQUEST['Submit']) || $_SERVER['REQUEST_METHOD'] === 'POST';

// Prepare Filters
$conditionItemArray = ["(i.storeid='$storeid' OR i.storeid='0')"];
$catid = (int)($selectedCat ?: 0);
$catName = 'كل الفئات';
if ($catid > 0) {
    $conditionItemArray[] = "i.catid = '$catid'";
    $resCat = safe_query($adController->MySQL, "SELECT name_ar, name_en FROM categories WHERE id=? LIMIT 1", [$catid]);
    if ($resCat && $dataCat = mysqli_fetch_assoc($resCat)) {
        $catName = !empty($dataCat['name_' . $language]) ? $dataCat['name_' . $language] : ($dataCat['name_ar'] ?? 'فئة');
    }
}
$conditionItem = implode(" AND ", $conditionItemArray);

$conditionSearch = "";
$branchid = (int)($selectedBr ?: 0);
$store    = (int)($selectedStore ?: 0);

$storeName = 'كل المخازن';
if ($store > 0) {
    $conditionSearch .= " AND o.store = '$store' ";
    $resStore = safe_query($adController->MySQL, "SELECT name_ar, name_en FROM stores WHERE id=? LIMIT 1", [$store]);
    if ($resStore && $dataStore = mysqli_fetch_assoc($resStore)) {
        $storeName = !empty($dataStore['name_' . $language]) ? $dataStore['name_' . $language] : ($dataStore['name_ar'] ?? 'مخزن');
    }
}

$branchName = 'كل الفروع';
if ($branchid > 0) {
    $conditionSearch .= " AND b.id = '$branchid' ";
    $resBr = safe_query($adController->MySQL, "SELECT name_ar, name_en FROM branches WHERE id=? LIMIT 1", [$branchid]);
    if ($resBr && $dataBr = mysqli_fetch_assoc($resBr)) {
        $branchName = !empty($dataBr['name_' . $language]) ? $dataBr['name_' . $language] : ($dataBr['name_ar'] ?? 'فرع');
    }
}

// Calculate Inventory Data
$inventoryRows = [];
$kpiTotalItems = 0;
$kpiTotalUnits = 0;
$kpiTotalValue = 0;
$kpiOutOfStock = 0;

if ($isSubmitted) {
    $queryThread = "SELECT i.item_thread, i.name_" . $language . " AS name 
                    FROM items i 
                    WHERE $conditionItem 
                    GROUP BY i.item_thread";
    $resThread = mysqli_query($adController->MySQL, $queryThread);

    if ($resThread) {
        while ($dataThread = mysqli_fetch_assoc($resThread)) {
            $total = 0;
            $itemThread = mysqli_real_escape_string($adController->MySQL, (string)$dataThread['item_thread']);
            $itemsListArray = [];

            $queryItems = "SELECT i.id AS id, i.item_thread, i.name_$language AS name, i.barcode AS barcode, 
                                  i.intimate_stock AS noOfQty, i.price, u.name_$language AS unit,
                                  COALESCE(
                                      (SELECT pi.cost FROM purchase_items pi WHERE pi.itemid = i.id ORDER BY pi.id DESC LIMIT 1),
                                      (SELECT op.cost FROM open_palance op WHERE op.itemid = i.id ORDER BY op.id DESC LIMIT 1),
                                      i.price,
                                      0
                                  ) AS item_cost
                           FROM items i 
                           LEFT JOIN units u ON i.unit = u.id 
                           WHERE i.item_thread = '$itemThread' 
                           ORDER BY i.intimate_stock DESC";
            $resItem = mysqli_query($adController->MySQL, $queryItems);
            if ($resItem) {
                while ($dataItem = mysqli_fetch_assoc($resItem)) {
                    $itemsListArray[] = $dataItem;
                    $itemId = (int)$dataItem['id'];

                    // 1. Open Balance
                    $queryOpen = "SELECT o.quantity AS qty 
                                  FROM open_palance o 
                                  INNER JOIN stores s ON o.store = s.id 
                                  INNER JOIN branches b ON s.branch = b.id 
                                  WHERE o.itemid = '$itemId' $conditionSearch AND (o.storeid='$storeid' OR o.storeid='0')";
                    $resO = mysqli_query($adController->MySQL, $queryOpen);
                    if ($resO) {
                        while ($dataO = mysqli_fetch_assoc($resO)) {
                            $noOfQty = (float)($dataItem['noOfQty'] ?? 1);
                            $qty     = (float)($dataO['qty'] ?? 0);
                            $total   += $qty * $noOfQty;
                        }
                    }

                    // 2. Income items
                    $queryInc = "SELECT o.quantity AS qty 
                                 FROM income_items o 
                                 INNER JOIN stores s ON o.store = s.id 
                                 INNER JOIN branches b ON s.branch = b.id 
                                 WHERE o.itemid = '$itemId' $conditionSearch AND o.state = '1' AND (o.storeid='$storeid' OR o.storeid='0')";
                    $resI = mysqli_query($adController->MySQL, $queryInc);
                    if ($resI) {
                        while ($dataI = mysqli_fetch_assoc($resI)) {
                            $noOfQty = (float)($dataItem['noOfQty'] ?? 1);
                            $qty     = (float)($dataI['qty'] ?? 0);
                            $total   += $qty * $noOfQty;
                        }
                    }

                    // 3. Outgo items
                    $queryOut = "SELECT o.quantity AS qty 
                                 FROM outgo_items o 
                                 INNER JOIN stores s ON o.store = s.id 
                                 INNER JOIN branches b ON s.branch = b.id 
                                 WHERE o.itemid = '$itemId' $conditionSearch AND o.state = '1' AND (o.storeid='$storeid' OR o.storeid='0')";
                    $resOut = mysqli_query($adController->MySQL, $queryOut);
                    if ($resOut) {
                        while ($dataOut = mysqli_fetch_assoc($resOut)) {
                            $noOfQty = (float)($dataItem['noOfQty'] ?? 1);
                            $qty     = (float)($dataOut['qty'] ?? 0) * -1;
                            $total   += $qty * $noOfQty;
                        }
                    }
                }
            }

            for ($i = 0; $i < count($itemsListArray); $i++) {
                $divisor = max(1, (int)($itemsListArray[$i]['noOfQty'] ?? 1));
                $palance = intval($total / $divisor);
                $total   = $total % $divisor;

                if ($palance != 0 || isset($_REQUEST['show_zeros'])) {
                    $barcode  = $itemsListArray[$i]['barcode'] ?? '---';
                    $itemName = $itemsListArray[$i]['name'] ?? '---';
                    $unitName = $itemsListArray[$i]['unit'] ?? '---';
                    $count    = $divisor;
                    $cost     = (float)($itemsListArray[$i]['item_cost'] ?? 0);
                    $totals   = $palance * $cost;

                    $kpiTotalItems++;
                    $kpiTotalUnits += $palance;
                    $kpiTotalValue += $totals;
                    if ($palance <= 0) {
                        $kpiOutOfStock++;
                    }

                    $inventoryRows[] = [
                        'thread'   => $itemsListArray[$i]['item_thread'],
                        'barcode'  => $barcode,
                        'name'     => $itemName,
                        'unit'     => $unitName,
                        'filling'  => $count,
                        'palance'  => $palance,
                        'cost'     => $cost,
                        'total'    => $totals
                    ];

                    safe_query($adController->MySQL, 
                        "INSERT INTO `report_inventory` (`store`, `barcode`, `item`, `unit`, `count`, `quantity`, `cost`, `discount`, `total`) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?)",
                        [$storeName, $barcode, $itemName, $unitName, $count, $palance, $cost, $totals]);
                }
            }
        }
    }

    safe_query($adController->MySQL, 
        "INSERT INTO `report_inventory_sam` (`pranch`, `store`, `category`, `totals`) VALUES (?, ?, ?, ?)",
        [$branchName, $storeName, $catName, $kpiTotalValue]);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جرد المستودعات وأرصدة المخازن | WAM Tech ERP</title>
    <link rel="stylesheet" href="css/admin-pro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <style>
        .kpi-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border: 1px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.2s;
        }
        .kpi-card:hover {
            transform: translateY(-3px);
        }
        .kpi-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        .kpi-title {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .kpi-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }
        .filter-panel {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #edf2f7;
            margin-bottom: 25px;
        }
        .table-panel {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #edf2f7;
        }
        .badge-stock {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .badge-positive { background: #dcfce7; color: #15803d; }
        .badge-zero { background: #fef3c7; color: #b45309; }
        .badge-negative { background: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body class="admin-pro-body">
    <?php require_once("components/header_pro.php"); ?>
    
    <div class="admin-wrapper">
        <?php require_once("components/sidebar_pro.php"); ?>
        
        <main class="admin-main">
            <div class="container-fluid px-4 py-4">
                
                <!-- Page Title -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">
                            <i class="fas fa-boxes text-primary me-2"></i> جرد المستودعات وأرصدة المخازن
                        </h2>
                        <p class="text-muted mb-0">عرض دقيق لأرصدة الأصناف المتوفرة، قيم التكلفة، وجرد الفروع والمخازن</p>
                    </div>
                    <div class="d-flex gap-2">
                        <?php if ($isSubmitted): ?>
                            <a href="reports/storeInvestory.php" target="_blank" class="btn btn-outline-danger">
                                <i class="fas fa-file-pdf me-1"></i> طباعة الجرد المعتمد (PDF)
                            </a>
                        <?php endif; ?>
                        <a href="item-movement.php" class="btn btn-primary">
                            <i class="fas fa-exchange-alt me-1"></i> كارت حركة الصنف
                        </a>
                    </div>
                </div>

                <!-- KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="kpi-title">أصناف بالجرد</div>
                                <div class="kpi-value"><?= number_format($kpiTotalItems) ?></div>
                            </div>
                            <div class="kpi-icon" style="background: #e0f2fe; color: #0284c7;">
                                <i class="fas fa-tags"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="kpi-title">إجمالي كميات المخزون</div>
                                <div class="kpi-value"><?= number_format($kpiTotalUnits, 2) ?></div>
                            </div>
                            <div class="kpi-icon" style="background: #dcfce7; color: #16a34a;">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="kpi-title">إجمالي تقييم التكلفة</div>
                                <div class="kpi-value"><?= number_format($kpiTotalValue, 2) ?> <small class="text-muted fs-6">ج.م</small></div>
                            </div>
                            <div class="kpi-icon" style="background: #fef3c7; color: #d97706;">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="kpi-title">أصناف نفد مخزونها</div>
                                <div class="kpi-value text-danger"><?= number_format($kpiOutOfStock) ?></div>
                            </div>
                            <div class="kpi-icon" style="background: #fee2e2; color: #dc2626;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Panel -->
                <div class="filter-panel">
                    <form action="<?= $pgName ?>" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-secondary">
                                <i class="fas fa-code-branch me-1"></i> <?= BRANCH ?>
                            </label>
                            <select name="br" id="br" class="form-select" onchange="this.form.submit()">
                                <option value="">-- كل الفروع --</option>
                                <?php
                                $queryB = "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' OR storeid='0'";
                                $resB = mysqli_query($adController->MySQL, $queryB);
                                if ($resB) {
                                    while ($dB = mysqli_fetch_assoc($resB)) {
                                        $nameB = !empty($dB['name_' . $language]) ? $dB['name_' . $language] : ($dB['name_ar'] ?? '');
                                        $sel = ($selectedBr !== '' && $selectedBr == $dB['id']) ? 'selected' : '';
                                        echo "<option value='{$dB['id']}' $sel>" . htmlspecialchars($nameB) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-secondary">
                                <i class="fas fa-warehouse me-1"></i> <?= STORES ?>
                            </label>
                            <select name="store" id="store" class="form-select">
                                <option value="">-- كل المخازن --</option>
                                <?php
                                $storeFilter = ($selectedBr !== '') ? " AND branch='" . (int)$selectedBr . "'" : "";
                                $queryS = "SELECT id, name_ar, name_en FROM stores WHERE (storeid='$storeid' OR storeid='0') $storeFilter";
                                $resS = mysqli_query($adController->MySQL, $queryS);
                                if ($resS) {
                                    while ($dS = mysqli_fetch_assoc($resS)) {
                                        $nameS = !empty($dS['name_' . $language]) ? $dS['name_' . $language] : ($dS['name_ar'] ?? '');
                                        $sel = ($selectedStore !== '' && $selectedStore == $dS['id']) ? 'selected' : '';
                                        echo "<option value='{$dS['id']}' $sel>" . htmlspecialchars($nameS) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-secondary">
                                <i class="fas fa-tags me-1"></i> <?= CATEGORY ?>
                            </label>
                            <select name="catid" id="catid" class="form-select">
                                <option value="">-- كل الفئات --</option>
                                <?php
                                $queryC = "SELECT id, name_ar, name_en FROM categories WHERE storeid='$storeid' OR storeid='0'";
                                $resC = mysqli_query($adController->MySQL, $queryC);
                                if ($resC) {
                                    while ($dC = mysqli_fetch_assoc($resC)) {
                                        $nameC = !empty($dC['name_' . $language]) ? $dC['name_' . $language] : ($dC['name_ar'] ?? '');
                                        $sel = ($selectedCat !== '' && $selectedCat == $dC['id']) ? 'selected' : '';
                                        echo "<option value='{$dC['id']}' $sel>" . htmlspecialchars($nameC) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" name="Submit" value="1" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-filter me-1"></i> عرض الجرد
                            </button>
                            <a href="<?= $pgName ?>" class="btn btn-outline-secondary py-2" title="إعادة ضبط">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Table Panel -->
                <div class="table-panel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-list-ul text-secondary me-2"></i> بيان الأرصدة الحالية بالمخازن
                        </h5>
                        <div class="text-muted small">
                            <i class="fas fa-map-marker-alt me-1"></i> الفرع: <strong><?= htmlspecialchars($branchName) ?></strong> | 
                            <i class="fas fa-warehouse me-1"></i> المستودع: <strong><?= htmlspecialchars($storeName) ?></strong> | 
                            <i class="fas fa-tag me-1"></i> الفئة: <strong><?= htmlspecialchars($catName) ?></strong>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="inventoryTable" class="table table-hover align-middle table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th><?= BARCODE ?></th>
                                    <th><?= ITEM_NAME ?></th>
                                    <th><?= UNITS ?></th>
                                    <th class="text-center"><?= FILLING ?></th>
                                    <th class="text-center"><?= PALANCE ?></th>
                                    <th class="text-end"><?= COST ?> (ج.م)</th>
                                    <th class="text-end"><?= TOTALS ?> (ج.م)</th>
                                    <th class="text-center" style="width: 100px;">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rowIdx = 0;
                                foreach ($inventoryRows as $row):
                                    $rowIdx++;
                                    $p = $row['palance'];
                                    $badgeClass = ($p > 0) ? 'badge-positive' : (($p == 0) ? 'badge-zero' : 'badge-negative');
                                ?>
                                    <tr>
                                        <td class="text-center text-muted"><?= $rowIdx ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark font-monospace border px-2 py-1">
                                                <?= htmlspecialchars((string)$row['barcode']) ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold text-dark">
                                            <?= htmlspecialchars((string)$row['name']) ?>
                                        </td>
                                        <td>
                                            <span class="text-secondary"><?= htmlspecialchars((string)$row['unit']) ?></span>
                                        </td>
                                        <td class="text-center"><?= htmlspecialchars((string)$row['filling']) ?></td>
                                        <td class="text-center">
                                            <span class="badge-stock <?= $badgeClass ?>">
                                                <?= number_format($p, 2) ?>
                                            </span>
                                        </td>
                                        <td class="text-end font-monospace"><?= number_format($row['cost'], 2) ?></td>
                                        <td class="text-end fw-bold text-primary font-monospace"><?= number_format($row['total'], 2) ?></td>
                                        <td class="text-center">
                                            <a href="item-movement.php?iid=<?= urlencode($row['thread']) ?>" class="btn btn-sm btn-outline-info" title="كارت حركة الصنف">
                                                <i class="fas fa-history"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td colspan="5" class="text-end">الإجمالي العام للجرد:</td>
                                    <td class="text-center text-success"><?= number_format($kpiTotalUnits, 2) ?></td>
                                    <td></td>
                                    <td class="text-end text-primary"><?= number_format($kpiTotalValue, 2) ?> ج.م</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#inventoryTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
                },
                dom: "<'row mb-3'<'col-md-6 d-flex align-items-center gap-2'B><'col-md-6'f>>" +
                     "<'row'<'col-12'tr>>" +
                     "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel text-success me-1"></i> تصدير إكسيل',
                        className: 'btn btn-outline-success btn-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-1"></i> طباعة الجدول',
                        className: 'btn btn-outline-dark btn-sm'
                    }
                ],
                order: [[2, 'asc']],
                pageLength: 25
            });
        });
    </script>
</body>
</html>
