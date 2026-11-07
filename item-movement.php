<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid   = (int)($_SESSION['storeid'] ?? 0);
$language  = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$pgName    = basename(__FILE__);

$selectedIid   = trim((string)($_REQUEST['iid'] ?? ''));
$fromDateV     = trim((string)($_REQUEST['from_date'] ?? ''));
$toDateV       = trim((string)($_REQUEST['to_date'] ?? ''));
$isFormActive  = ($selectedIid !== '');

if ($isFormActive) {
    safe_query($adController->MySQL, "DELETE FROM report_item_move");
    safe_query($adController->MySQL, "DELETE FROM report_item_move_sam");
    safe_query($adController->MySQL, "DELETE FROM report_item_move_all");
}

// Fetch list of items for the dropdown
$queryItemsList = "SELECT i.id, i.item_thread, i.barcode, i.name_ar, i.name_en, u.name_ar AS unit_ar, u.name_en AS unit_en 
                   FROM items i 
                   LEFT JOIN units u ON i.unit = u.id 
                   WHERE i.storeid='$storeid' OR i.storeid='0' 
                   ORDER BY i.name_ar ASC";
$resItemsList = mysqli_query($adController->MySQL, $queryItemsList);

// Temporary movement rows
$movementRows   = [];
$unitBreakdowns = [];
$itemHeaderInfo = null;
$totalInQty     = 0;
$totalOutQty    = 0;
$openingQty     = 0;
$finalTotalUnits= 0;

if ($isFormActive) {
    $itemIdEsc = mysqli_real_escape_string($adController->MySQL, $selectedIid);
    $conditionSel = " AND i.item_thread = '$itemIdEsc' ";
    $conditionArray = [];

    $fDateFormatted = '';
    if (!empty($fromDateV)) {
        $rawF = str_replace('/', '-', $fromDateV);
        $fDateFormatted = date("Y-m-d 00:00:00", strtotime($rawF));
        $conditionArray[] = "o.date >= '$fDateFormatted'";
    }

    $tDateFormatted = '';
    if (!empty($toDateV)) {
        $rawT = str_replace('/', '-', $toDateV);
        $tDateFormatted = date("Y-m-d 23:59:59", strtotime($rawT));
        $conditionArray[] = "o.date <= '$tDateFormatted'";
    }

    $condition = '';
    if (!empty($conditionArray)) {
        $condition = " AND " . implode(" AND ", $conditionArray);
    }

    // Temporary storage in memory
    $rawMovements = [];
    $itemsListArray = [];

    $queryAll = "SELECT i.id AS id, i.item_thread, i.name_$language AS name, i.unit, i.barcode, 
                        i.price, i.intimate_stock AS noOfQty, u.name_$language AS unit_name,
                        c.name_$language AS cat_name
                 FROM items i 
                 LEFT JOIN units u ON i.unit = u.id 
                 LEFT JOIN categories c ON i.catid = c.id
                 WHERE (i.storeid='$storeid' OR i.storeid='0') $conditionSel 
                 ORDER BY i.intimate_stock DESC";
    $resAll = mysqli_query($adController->MySQL, $queryAll);

    if ($resAll) {
        while ($dataAll = mysqli_fetch_assoc($resAll)) {
            $itemsListArray[] = $dataAll;
            if (!$itemHeaderInfo) {
                $itemHeaderInfo = [
                    'name'     => $dataAll['name'],
                    'barcode'  => $dataAll['barcode'] ?: '---',
                    'unit'     => $dataAll['unit_name'] ?: '---',
                    'category' => $dataAll['cat_name'] ?: '---',
                    'price'    => (float)($dataAll['price'] ?? 0)
                ];
            }
            $curId   = (int)$dataAll['id'];
            $noOfQty = (float)($dataAll['noOfQty'] ?? 1);
            $uName   = $dataAll['unit_name'] ?: '---';
            $iPrice  = (float)($dataAll['price'] ?? 0);

            // 1. Open Balance
            $queryO = "SELECT o.quantity AS qty, o.date AS date, o.cost, s.name_$language AS store_name 
                       FROM open_palance o 
                       LEFT JOIN stores s ON o.store = s.id 
                       WHERE o.itemid = '$curId' $condition AND (o.storeid='$storeid' OR o.storeid='0')";
            $resO = mysqli_query($adController->MySQL, $queryO);
            if ($resO) {
                while ($dataO = mysqli_fetch_assoc($resO)) {
                    $qty = (float)($dataO['qty'] ?? 0);
                    $cst = (float)($dataO['cost'] ?? $iPrice);
                    $rawMovements[] = [
                        'date'      => $dataO['date'] ?: date('Y-m-d H:i:s'),
                        'type'      => 'OPENING_PALANCE',
                        'typeName'  => 'رصيد افتتاحي',
                        'typeClass' => 'bg-info text-dark',
                        'store'     => $dataO['store_name'] ?: 'المخزن الرئيسي',
                        'unit'      => $uName,
                        'noOfQty'   => $noOfQty,
                        'qty'       => $qty,
                        'inQty'     => $qty,
                        'outQty'    => 0,
                        'cost'      => $cst,
                        'item_id'   => $curId
                    ];
                    $openingQty += $qty * $noOfQty;
                }
            }

            // 2. Income items (الوارد: مشتريات / مرتجع مبيعات / تحويل وارد / إذن إضافة)
            $queryInc = "SELECT o.quantity AS qty, o.date AS date, o.cost, o.invoice_Type AS type, o.invoice_No, s.name_$language AS store_name 
                         FROM income_items o 
                         LEFT JOIN stores s ON o.store = s.id 
                         WHERE o.itemid = '$curId' $condition AND o.state = '1' AND (o.storeid='$storeid' OR o.storeid='0')";
            $resInc = mysqli_query($adController->MySQL, $queryInc);
            if ($resInc) {
                while ($dataInc = mysqli_fetch_assoc($resInc)) {
                    $qty = (float)($dataInc['qty'] ?? 0);
                    $cst = (float)($dataInc['cost'] ?? $iPrice);
                    $rawType = $dataInc['type'] ?: 'INCOMING_ENTRY';
                    
                    $badgeClass = 'bg-success text-white';
                    $typeTitle = 'إذن إضافة وارد';
                    if ($rawType === 'PURCHASE') {
                        $typeTitle = 'فاتورة مشتريات';
                        $badgeClass = 'bg-primary text-white';
                    } elseif ($rawType === 'SALE_RETURN' || $rawType === 'SALES_RET') {
                        $typeTitle = 'مرتجع مبيعات';
                        $badgeClass = 'bg-warning text-dark';
                    } elseif ($rawType === 'STORE_TRANSFAIR') {
                        $typeTitle = 'تحويل مخزني وارد';
                        $badgeClass = 'bg-info text-white';
                    }

                    $rawMovements[] = [
                        'date'      => $dataInc['date'] ?: date('Y-m-d H:i:s'),
                        'type'      => $rawType,
                        'typeName'  => $typeTitle,
                        'typeClass' => $badgeClass,
                        'store'     => $dataInc['store_name'] ?: '---',
                        'unit'      => $uName,
                        'noOfQty'   => $noOfQty,
                        'qty'       => $qty,
                        'inQty'     => $qty,
                        'outQty'    => 0,
                        'cost'      => $cst,
                        'item_id'   => $curId
                    ];
                    $totalInQty += $qty * $noOfQty;
                }
            }

            // 3. Outgo items (المنصرف: مبيعات / مرتجع مشتريات / تحويل صادر / إذن صرف)
            $queryOut = "SELECT o.quantity AS qty, o.date AS date, o.cost, o.invoice_Type AS type, o.invoice_No, s.name_$language AS store_name 
                         FROM outgo_items o 
                         LEFT JOIN stores s ON o.store = s.id 
                         WHERE o.itemid = '$curId' $condition AND o.state = '1' AND (o.storeid='$storeid' OR o.storeid='0')";
            $resOut = mysqli_query($adController->MySQL, $queryOut);
            if ($resOut) {
                while ($dataOut = mysqli_fetch_assoc($resOut)) {
                    $qty = (float)($dataOut['qty'] ?? 0);
                    $cst = (float)($dataOut['cost'] ?? $iPrice);
                    $rawType = $dataOut['type'] ?: 'OUTGOING_ENTRY';

                    $badgeClass = 'bg-danger text-white';
                    $typeTitle = 'إذن صرف صادر';
                    if ($rawType === 'SALE' || $rawType === 'POS') {
                        $typeTitle = 'فاتورة مبيعات';
                        $badgeClass = 'bg-danger text-white';
                    } elseif ($rawType === 'PURCHASE_RETURN' || $rawType === 'PURCH_RET') {
                        $typeTitle = 'مرتجع مشتريات';
                        $badgeClass = 'bg-secondary text-white';
                    } elseif ($rawType === 'STORE_TRANSFAIR') {
                        $typeTitle = 'تحويل مخزني صادر';
                        $badgeClass = 'bg-dark text-white';
                    }

                    $rawMovements[] = [
                        'date'      => $dataOut['date'] ?: date('Y-m-d H:i:s'),
                        'type'      => $rawType,
                        'typeName'  => $typeTitle,
                        'typeClass' => $badgeClass,
                        'store'     => $dataOut['store_name'] ?: '---',
                        'unit'      => $uName,
                        'noOfQty'   => $noOfQty,
                        'qty'       => -$qty,
                        'inQty'     => 0,
                        'outQty'    => $qty,
                        'cost'      => $cst,
                        'item_id'   => $curId
                    ];
                    $totalOutQty += $qty * $noOfQty;
                }
            }
        }
    }

    // Sort all movements chronologically
    usort($rawMovements, function($a, $b) {
        return strtotime($a['date']) <=> strtotime($b['date']);
    });

    // Compute running balance & store in report cache
    $runningBalance = 0;
    foreach ($rawMovements as $idx => $m) {
        $change = $m['qty'] * $m['noOfQty'];
        $runningBalance += $change;
        $rowTotalCount = $m['qty'] * $m['noOfQty'];
        $totalCost = abs($m['qty']) * $m['cost'];

        $movementRows[] = [
            'seq'       => $idx + 1,
            'date'      => $m['date'],
            'typeName'  => $m['typeName'],
            'typeClass' => $m['typeClass'],
            'store'     => $m['store'],
            'unit'      => $m['unit'],
            'noOfQty'   => $m['noOfQty'],
            'inQty'     => $m['inQty'],
            'outQty'    => $m['outQty'],
            'running'   => $runningBalance,
            'cost'      => $m['cost'],
            'total'     => $totalCost
        ];

        safe_query($adController->MySQL, 
            "INSERT INTO `report_item_move` (`date`, `type`, `store`, `barcode`, `item`, `unit`, `count`, `quantity`, `totalCount`, `cost`, `total`) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $m['date'], 
                $m['typeName'], 
                $m['store'], 
                $itemHeaderInfo['barcode'] ?? '', 
                $itemHeaderInfo['name'] ?? '', 
                $m['unit'], 
                $m['noOfQty'], 
                $m['qty'], 
                $rowTotalCount, 
                $m['cost'], 
                $totalCost
            ]);
    }

    $finalTotalUnits = $runningBalance;

    // Unit Breakdown Calculation
    $remUnits = $finalTotalUnits;
    for ($i = 0; $i < count($itemsListArray); $i++) {
        $divisor = max(1, (int)($itemsListArray[$i]['noOfQty'] ?? 1));
        $uQty = intval($remUnits / $divisor);
        $remUnits = $remUnits % $divisor;
        $uName = $itemsListArray[$i]['unit_name'] ?: '---';
        $uCost = (float)($itemsListArray[$i]['price'] ?? 0);
        $uTotal = $uQty * $uCost;

        $unitBreakdowns[] = [
            'unit'    => $uName,
            'filling' => $divisor,
            'balance' => $uQty,
            'total'   => $uTotal
        ];

        safe_query($adController->MySQL, 
            "INSERT INTO `report_item_move_sam` (`unit`, `count`, `quantity`, `total`) VALUES (?, ?, ?, ?)",
            [$uName, $divisor, $uQty, $uTotal]);
    }

    safe_query($adController->MySQL, 
        "INSERT INTO `report_item_move_all` (`item`, `from`, `to`) VALUES (?, ?, ?)",
        [$itemHeaderInfo['name'] ?? 'الصنف', $fDateFormatted, $tDateFormatted]);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كارت حركة الصنف | WAM Tech ERP</title>
    <link rel="stylesheet" href="css/admin-pro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        .kpi-card {
            background: #fff;
            border-radius: 12px;
            padding: 18px;
            border: 1px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .item-info-banner {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #fff;
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 25px;
        }
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 6px 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
    </style>
</head>
<body class="admin-pro-body">
    <?php require_once("components/header_pro.php"); ?>
    
    <div class="admin-wrapper">
        <?php require_once("components/sidebar_pro.php"); ?>
        
        <main class="admin-main">
            <div class="container-fluid px-4 py-4">
                
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">
                            <i class="fas fa-history text-primary me-2"></i> كارت حركة الصنف
                        </h2>
                        <p class="text-muted mb-0">سجل حركة الصادر والوارد التراكمي وتتبع رصيد المخزون بدقة</p>
                    </div>
                    <div class="d-flex gap-2">
                        <?php if ($isFormActive): ?>
                            <a href="reports/item_move.php" target="_blank" class="btn btn-outline-danger">
                                <i class="fas fa-file-pdf me-1"></i> طباعة كارت الحركة (PDF)
                            </a>
                        <?php endif; ?>
                        <a href="store-inventory.php" class="btn btn-outline-secondary">
                            <i class="fas fa-boxes me-1"></i> جرد المخازن
                        </a>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <form action="<?= $pgName ?>" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label fw-bold text-secondary">
                                    <i class="fas fa-cube me-1"></i> اختر الصنف للمتابعة <span class="text-danger">*</span>
                                </label>
                                <select name="iid" id="iid" class="form-select select2-item" required>
                                    <option value="">-- ابحث بالاسم أو الباركود --</option>
                                    <?php
                                    if ($resItemsList) {
                                        while ($it = mysqli_fetch_assoc($resItemsList)) {
                                            $bCode = !empty($it['barcode']) ? $it['barcode'] : '---';
                                            $iName = !empty($it['name_ar']) ? $it['name_ar'] : (!empty($it['name_en']) ? $it['name_en'] : '---');
                                            $uName = !empty($it['unit_ar']) ? $it['unit_ar'] : (!empty($it['unit_en']) ? $it['unit_en'] : '---');
                                            $thVal = $it['item_thread'] ?: $it['id'];
                                            $sel = ($selectedIid !== '' && $selectedIid == $thVal) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($thVal) . "' $sel>" . htmlspecialchars("$bCode | $iName | $uName") . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary">
                                    <i class="fas fa-calendar-alt me-1"></i> من تاريخ
                                </label>
                                <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($fromDateV) ?>">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary">
                                    <i class="fas fa-calendar-check me-1"></i> إلى تاريخ
                                </label>
                                <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($toDateV) ?>">
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <i class="fas fa-search me-1"></i> استعراض الحركة
                                </button>
                                <a href="<?= $pgName ?>" class="btn btn-outline-secondary py-2" title="إلغاء">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($isFormActive && $itemHeaderInfo): ?>

                    <!-- Item Header Banner -->
                    <div class="item-info-banner">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <span class="badge bg-primary mb-2 px-3 py-1"><?= htmlspecialchars($itemHeaderInfo['category']) ?></span>
                                <h3 class="fw-bold mb-1"><?= htmlspecialchars($itemHeaderInfo['name']) ?></h3>
                                <div class="text-light opacity-75 small">
                                    <span class="me-3"><i class="fas fa-barcode me-1"></i> الباركود: <?= htmlspecialchars($itemHeaderInfo['barcode']) ?></span>
                                    <span><i class="fas fa-balance-scale me-1"></i> الوحدة الأساسية: <?= htmlspecialchars($itemHeaderInfo['unit']) ?></span>
                                </div>
                            </div>
                            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                <div class="small opacity-75 mb-1">الرصيد التراكمي الإجمالي المتوفر</div>
                                <div class="display-6 fw-bold text-success font-monospace">
                                    <?= number_format($finalTotalUnits, 2) ?>
                                    <small class="fs-6 text-light opacity-75"><?= htmlspecialchars($itemHeaderInfo['unit']) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KPI Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-xl-3 col-sm-6">
                            <div class="kpi-card">
                                <div>
                                    <div class="text-muted small fw-bold">رصيد أول المدة</div>
                                    <div class="fs-4 fw-bold text-dark font-monospace"><?= number_format($openingQty, 2) ?></div>
                                </div>
                                <div class="kpi-icon" style="background:#e0f2fe; color:#0369a1;">
                                    <i class="fas fa-hourglass-start"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="kpi-card">
                                <div>
                                    <div class="text-muted small fw-bold">إجمالي الوارد (+)</div>
                                    <div class="fs-4 fw-bold text-success font-monospace">+<?= number_format($totalInQty, 2) ?></div>
                                </div>
                                <div class="kpi-icon" style="background:#dcfce7; color:#15803d;">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="kpi-card">
                                <div>
                                    <div class="text-muted small fw-bold">إجمالي المنصرف (-)</div>
                                    <div class="fs-4 fw-bold text-danger font-monospace">-<?= number_format($totalOutQty, 2) ?></div>
                                </div>
                                <div class="kpi-icon" style="background:#fee2e2; color:#b91c1c;">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="kpi-card">
                                <div>
                                    <div class="text-muted small fw-bold">صافي الرصيد الحالي</div>
                                    <div class="fs-4 fw-bold text-primary font-monospace"><?= number_format($finalTotalUnits, 2) ?></div>
                                </div>
                                <div class="kpi-icon" style="background:#fef3c7; color:#b45309;">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Unit Breakdown Table -->
                    <?php if (!empty($unitBreakdowns)): ?>
                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="fas fa-layer-group text-primary me-2"></i> توزيع الرصيد الحالي حسب الوحدات والتعبئة
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0 text-center">
                                        <thead class="table-light">
                                            <tr>
                                                <th>الوحدة</th>
                                                <th>معامل التعبئة (كمية/وحدة)</th>
                                                <th>الرصيد المتاح من الوحدة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($unitBreakdowns as $ub): ?>
                                                <tr>
                                                    <td class="fw-bold"><?= htmlspecialchars($ub['unit']) ?></td>
                                                    <td class="font-monospace text-secondary"><?= htmlspecialchars((string)$ub['filling']) ?></td>
                                                    <td class="fw-bold text-primary font-monospace fs-6"><?= number_format($ub['balance']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Movement Ledger Table -->
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-list-check text-secondary me-2"></i> سجل حركات الصنف التفصيلية (Ledger)
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table id="movementTable" class="table table-hover align-middle table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th>التاريخ والوقت</th>
                                            <th>نوع الحركة / المستند</th>
                                            <th>المخزن</th>
                                            <th>الوحدة</th>
                                            <th class="text-center text-success">وارد (+)</th>
                                            <th class="text-center text-danger">منصرف (-)</th>
                                            <th class="text-center fw-bold text-primary">الرصيد التراكمي</th>
                                            <th class="text-end">سعر الوحدة</th>
                                            <th class="text-end">القيمة الإجمالية</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($movementRows as $m): ?>
                                            <tr>
                                                <td class="text-center text-muted"><?= $m['seq'] ?></td>
                                                <td class="font-monospace small"><?= htmlspecialchars($m['date']) ?></td>
                                                <td>
                                                    <span class="badge <?= $m['typeClass'] ?> px-2 py-1">
                                                        <?= htmlspecialchars($m['typeName']) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($m['store']) ?></td>
                                                <td><?= htmlspecialchars($m['unit']) ?></td>
                                                <td class="text-center font-monospace fw-bold text-success">
                                                    <?= $m['inQty'] > 0 ? '+' . number_format($m['inQty'], 2) : '-' ?>
                                                </td>
                                                <td class="text-center font-monospace fw-bold text-danger">
                                                    <?= $m['outQty'] > 0 ? '-' . number_format($m['outQty'], 2) : '-' ?>
                                                </td>
                                                <td class="text-center font-monospace fw-bold <?= $m['running'] >= 0 ? 'text-primary' : 'text-danger' ?>">
                                                    <?= number_format($m['running'], 2) ?>
                                                </td>
                                                <td class="text-end font-monospace"><?= number_format($m['cost'], 2) ?></td>
                                                <td class="text-end font-monospace fw-bold text-dark"><?= number_format($m['total'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php elseif ($isFormActive): ?>
                    <div class="alert alert-warning text-center py-4">
                        <i class="fas fa-exclamation-circle fs-3 d-block mb-2"></i>
                        لم يتم العثور على حركات مسجلة لهذا الصنف خلال الفترة المحددة.
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm rounded-3 text-center py-5">
                        <div class="card-body">
                            <i class="fas fa-hand-pointer text-muted fs-1 mb-3"></i>
                            <h4 class="fw-bold text-dark">يرجى اختيار صنف لعرض كارت الحركة</h4>
                            <p class="text-muted mb-0">اختر الصنف من القائمة أعلاه لعرض رصيده وحركاته التراكمية وسندات الصادر والوارد المرتبطة به.</p>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2-item').select2({
                dir: "rtl",
                width: '100%'
            });

            if ($('#movementTable').length) {
                $('#movementTable').DataTable({
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
                            text: '<i class="fas fa-print me-1"></i> طباعة كارت الحركة',
                            className: 'btn btn-outline-dark btn-sm'
                        }
                    ],
                    order: [[0, 'asc']],
                    pageLength: 25
                });
            }
        });
    </script>
</body>
</html>
