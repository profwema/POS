<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$storeid  = intval($_SESSION['storeid'] ?? 1);
$pgName   = basename(__FILE__);

// Clear Jasper temporary tables
mysqli_query($adController->MySQL, "TRUNCATE TABLE report_open_palance");
mysqli_query($adController->MySQL, "TRUNCATE TABLE report_open_palance_all");

if (isset($_REQUEST["store"])) {
    $_SESSION['store'] = trim($_REQUEST["store"]);
}
$selectedStore = $_SESSION['store'] ?? '';

// Fetch active opening balance date
$queryDate = "SELECT * FROM open_palance_date WHERE storeid='$storeid' OR storeid='0' ORDER BY id DESC LIMIT 1";
$resDate   = mysqli_query($adController->MySQL, $queryDate);
$dataDate  = $resDate ? mysqli_fetch_assoc($resDate) : null;
$open_palance_date = !empty($dataDate['date']) ? date("Y-m-d", strtotime($dataDate['date'])) : date("Y-m-d");
$_SESSION['open_palance_date'] = $open_palance_date;

// Fetch store list
$storesList = [];
$resStores = mysqli_query($adController->MySQL, "SELECT id, name_ar, name_en, defualt FROM stores WHERE storeid='$storeid' OR storeid='0' ORDER BY id ASC");
if ($resStores) {
    while ($st = mysqli_fetch_assoc($resStores)) {
        $storesList[] = $st;
        if ($selectedStore === '' && ($st['defualt'] ?? 0) == 1) {
            $selectedStore = $st['id'];
            $_SESSION['store'] = $selectedStore;
        }
    }
}

// Fetch open balance items with single optimized JOIN query
$storeSearch = "";
if (!empty($selectedStore)) {
    $cleanStore = intval($selectedStore);
    $storeSearch = " AND op.store = '$cleanStore' ";
}

$query = "SELECT op.*, 
                 s.name_ar AS store_name_ar, s.name_en AS store_name_en,
                 i.barcode, i.intimate_stock, i.name_ar AS item_name_ar, i.name_en AS item_name_en,
                 u.name_ar AS unit_name_ar, u.name_en AS unit_name_en
          FROM open_palance op
          LEFT JOIN stores s ON s.id = op.store
          LEFT JOIN items i ON i.id = op.itemid
          LEFT JOIN units u ON u.id = i.unit
          WHERE (op.storeid = '$storeid' OR op.storeid = '0') $storeSearch
          ORDER BY op.id DESC";
$res = mysqli_query($adController->MySQL, $query);

$itemsRows = [];
$kpiTotalItems = 0;
$kpiTotalQty   = 0;
$kpiTotalValue = 0;

if ($res) {
    $i = 1;
    while ($data = mysqli_fetch_assoc($res)) {
        $barcode   = $data["barcode"] ?? '';
        $count     = floatval($data["intimate_stock"] ?? 1);
        $itemName  = ($language == 'en' && !empty($data['item_name_en'])) ? $data['item_name_en'] : (!empty($data['item_name_ar']) ? $data['item_name_ar'] : 'صنف');
        $unitName  = ($language == 'en' && !empty($data['unit_name_en'])) ? $data['unit_name_en'] : (!empty($data['unit_name_ar']) ? $data['unit_name_ar'] : 'وحدة');
        $stName    = ($language == 'en' && !empty($data['store_name_en'])) ? $data['store_name_en'] : (!empty($data['store_name_ar']) ? $data['store_name_ar'] : 'المخزن');

        $quantity  = floatval($data['quantity'] ?? 0);
        $cost      = floatval($data['cost'] ?? 0);
        $discount  = floatval($data['discount'] ?? 0);

        $preTotal  = $quantity * $cost;
        $discValue = $preTotal * $discount / 100;
        $discTotal = $preTotal - $discValue;

        $kpiTotalItems++;
        $kpiTotalQty   += $quantity;
        $kpiTotalValue += $discTotal;

        $idval     = urlencode($adController->encrypt_decrypt(1, $data['id'], 0));
        $tableName = urlencode($adController->encrypt_decrypt(1, 'open_palance', 0));

        $itemsRows[] = [
            'id'        => $data['id'],
            'seq'       => $i,
            'store'     => $stName,
            'barcode'   => $barcode,
            'item'      => $itemName,
            'unit'      => $unitName,
            'quantity'  => $quantity,
            'cost'      => $cost,
            'discount'  => $discount,
            'total'     => $discTotal,
            'idval'     => $idval,
            'tableName' => $tableName
        ];

        safe_query($adController->MySQL, 
            "INSERT INTO `report_open_palance` (`id`, `store`, `barcode`, `item`, `unit`, `count`, `quantity`, `cost`, `discount`, `total`) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$i, $stName, $barcode, $itemName, $unitName, $count, $quantity, $cost, $discount, $discTotal]);
        $i++;
    }
}

// Update report summary table
$currStoreLabel = 'كل المخازن';
if (!empty($selectedStore)) {
    foreach ($storesList as $st) {
        if ($st['id'] == $selectedStore) {
            $currStoreLabel = $st['name_' . $language] ?? $st['name_ar'];
            break;
        }
    }
}

safe_query($adController->MySQL, 
    "INSERT INTO `report_open_palance_all` (`store`, `date`, `total`) VALUES (?, ?, ?)",
    [$currStoreLabel, $open_palance_date, $kpiTotalValue]);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الأرصدة الافتتاحية للمخازن | WAM Tech ERP</title>
    <link rel="stylesheet" href="css/admin-pro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <style>
        .kpi-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .edit-input {
            width: 90px;
            text-align: center;
            padding: 4px 6px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-family: monospace;
            font-weight: bold;
        }
        .edit-input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
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
                            <i class="fas fa-balance-scale text-primary me-2"></i> الأرصدة الافتتاحية للمخازن
                        </h2>
                        <p class="text-muted mb-0">إدخال واعتماد أرصدة أول المدة للمستودعات وتعديل أسعار التكلفة الافتتاحية</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="reports/openPalance.php" target="_blank" class="btn btn-outline-danger">
                            <i class="fas fa-file-pdf me-1"></i> طباعة تقرير الأرصدة (PDF)
                        </a>
                        <a href="add_open_palance.php" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> إضافة رصيد افتتاحي جديد
                        </a>
                    </div>
                </div>

                <!-- KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">الأصناف المسجلة</div>
                                <div class="fs-4 fw-bold text-dark font-monospace"><?= number_format($kpiTotalItems) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#e0f2fe; color:#0284c7;">
                                <i class="fas fa-cubes"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">إجمالي كميات الرصيد</div>
                                <div class="fs-4 fw-bold text-success font-monospace"><?= number_format($kpiTotalQty, 2) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#dcfce7; color:#16a34a;">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">إجمالي القيمة التقديرية</div>
                                <div class="fs-4 fw-bold text-primary font-monospace"><?= number_format($kpiTotalValue, 2) ?> <small class="fs-6 text-muted">ج.م</small></div>
                            </div>
                            <div class="kpi-icon" style="background:#fef3c7; color:#d97706;">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">تاريخ الرصيد الافتتاحي</div>
                                <div class="fs-5 fw-bold text-dark font-monospace"><?= htmlspecialchars($open_palance_date) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#f1f5f9; color:#475569;">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Controls & Filter Bar -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-3">
                        <div class="row g-3 align-items-center">
                            <!-- Store Filter -->
                            <div class="col-md-5">
                                <form action="<?= $pgName ?>" method="GET" class="d-flex align-items-center gap-2">
                                    <label class="fw-bold text-secondary text-nowrap mb-0">
                                        <i class="fas fa-warehouse text-primary me-1"></i> تصفية بالمخزن:
                                    </label>
                                    <select name="store" class="form-select" onchange="this.form.submit()">
                                        <option value="">-- كل المخازن --</option>
                                        <?php
                                        foreach ($storesList as $st) {
                                            $stName = $st['name_' . $language] ?? $st['name_ar'];
                                            $sel = ($selectedStore !== '' && $selectedStore == $st['id']) ? 'selected' : '';
                                            echo "<option value='{$st['id']}' $sel>" . htmlspecialchars($stName) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </form>
                            </div>

                            <!-- Opening Balance Date Update -->
                            <div class="col-md-7 d-flex align-items-center justify-content-md-end gap-2">
                                <label class="fw-bold text-secondary text-nowrap mb-0">
                                    <i class="fas fa-clock text-info me-1"></i> اعتماد تاريخ الرصيد:
                                </label>
                                <input type="date" id="openPalanceDateInput" class="form-control" style="max-width:180px;" value="<?= htmlspecialchars($open_palance_date) ?>">
                                <button type="button" id="btnUpdateDate" class="btn btn-outline-primary text-nowrap">
                                    <i class="fas fa-save me-1"></i> تحديث التاريخ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Open Balance Table -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-list text-secondary me-2"></i> بيان الأرصدة الافتتاحية للمستودع: <span class="text-primary"><?= htmlspecialchars($currStoreLabel) ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table id="openPalanceTable" class="table table-hover align-middle table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 45px;">#</th>
                                        <th>المخزن</th>
                                        <th>الباركود</th>
                                        <th>اسم الصنف</th>
                                        <th>الوحدة</th>
                                        <th class="text-center" style="width: 110px;">الكمية</th>
                                        <th class="text-center" style="width: 110px;">التكلفة (ج.م)</th>
                                        <th class="text-center" style="width: 90px;">الخصم %</th>
                                        <th class="text-end" style="width: 120px;">الإجمالي (ج.م)</th>
                                        <th class="text-center" style="width: 110px;">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($itemsRows as $r): ?>
                                        <tr id="row-<?= $r['id'] ?>">
                                            <td class="text-center text-muted"><?= $r['seq'] ?></td>
                                            <td><?= htmlspecialchars($r['store']) ?></td>
                                            <td>
                                                <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                                    <?= htmlspecialchars($r['barcode']) ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold text-dark"><?= htmlspecialchars($r['item']) ?></td>
                                            <td><span class="text-secondary"><?= htmlspecialchars($r['unit']) ?></span></td>
                                            <td class="text-center">
                                                <input type="number" step="0.01" class="edit-input item-qty" id="qty-<?= $r['id'] ?>" value="<?= $r['quantity'] ?>">
                                            </td>
                                            <td class="text-center">
                                                <input type="number" step="0.01" class="edit-input item-price" id="price-<?= $r['id'] ?>" value="<?= $r['cost'] ?>">
                                            </td>
                                            <td class="text-center">
                                                <input type="number" step="0.01" class="edit-input item-disc" id="disc-<?= $r['id'] ?>" value="<?= $r['discount'] ?>">
                                            </td>
                                            <td class="text-end fw-bold text-primary font-monospace" id="total-<?= $r['id'] ?>">
                                                <?= number_format($r['total'], 2) ?>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-success btn-save-row" data-id="<?= $r['id'] ?>" title="حفظ التعديل">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" data-id="<?= $r['id'] ?>" data-table="<?= $r['tableName'] ?>" data-idval="<?= $r['idval'] ?>" title="حذف البند">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td colspan="5" class="text-end">الإجمالي العام للأرصدة:</td>
                                        <td class="text-center text-success font-monospace" id="summaryQty"><?= number_format($kpiTotalQty, 2) ?></td>
                                        <td colspan="2"></td>
                                        <td class="text-end text-primary font-monospace" id="summaryVal"><?= number_format($kpiTotalValue, 2) ?> ج.م</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
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
            var table = $('#openPalanceTable').DataTable({
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
                order: [[0, 'asc']],
                pageLength: 25
            });

            // Live calculation on inline input change
            $(document).on('input', '.item-qty, .item-price, .item-disc', function() {
                var id = $(this).attr('id').split('-')[1];
                var q = parseFloat($('#qty-' + id).val()) || 0;
                var p = parseFloat($('#price-' + id).val()) || 0;
                var d = parseFloat($('#disc-' + id).val()) || 0;
                var preTotal = q * p;
                var discVal = preTotal * d / 100;
                var total = preTotal - discVal;
                $('#total-' + id).text(total.toFixed(2));
            });

            // Save inline edit
            $(document).on('click', '.btn-save-row', function() {
                var id = $(this).data('id');
                var q = $('#qty-' + id).val();
                var p = $('#price-' + id).val();
                var d = $('#disc-' + id).val();
                var btn = $(this);

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: {
                        'f': 'update_openPalance',
                        'q': q,
                        'p': p,
                        'd': d,
                        'id': id
                    },
                    success: function(resp) {
                        btn.prop('disabled', false).html('<i class="fas fa-check"></i>');
                        if ($.trim(resp) === '1' || parseInt(resp) > 0) {
                            btn.removeClass('btn-outline-success').addClass('btn-success');
                            setTimeout(function() {
                                btn.removeClass('btn-success').addClass('btn-outline-success');
                            }, 1500);
                        } else {
                            alert('حدث خطأ أثناء حفظ التعديل: ' + resp);
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html('<i class="fas fa-check"></i>');
                        alert('تعذر الاتصال بالخادم لحفظ التعديل.');
                    }
                });
            });

            // Delete row
            $(document).on('click', '.btn-delete-row', function() {
                if (!confirm('هل أنت متأكد من رغبتك في حذف هذا البند من الأرصدة الافتتاحية؟')) {
                    return;
                }
                var id = $(this).data('id');
                var tableName = $(this).data('table');
                var idval = $(this).data('idval');
                var row = $('#row-' + id);

                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: {
                        'f': 'delete',
                        't': tableName,
                        'id': idval
                    },
                    success: function(resp) {
                        if ($.trim(resp) === '1' || parseInt(resp) > 0) {
                            row.fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            alert('تعذر حذف البند: ' + resp);
                        }
                    },
                    error: function() {
                        alert('حدث خطأ في الاتصال بالخادم.');
                    }
                });
            });

            // Update opening balance date
            $('#btnUpdateDate').on('click', function() {
                var d = $('#openPalanceDateInput').val();
                if (!d) {
                    alert('يرجى اختيار تاريخ صحيح.');
                    return;
                }
                var btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري التحديث...');

                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: {
                        'f': 'updatepalDate',
                        'date': d
                    },
                    success: function(resp) {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> تحديث التاريخ');
                        if ($.trim(resp) === '1' || parseInt(resp) > 0) {
                            alert('تم تحديث تاريخ الرصيد الافتتاحي واعتماده بنجاح.');
                            location.reload();
                        } else {
                            alert('حدث خطأ أثناء اعتماد التاريخ: ' + resp);
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> تحديث التاريخ');
                        alert('تعذر الاتصال بالخادم.');
                    }
                });
            });
        });
    </script>
</body>
</html>
