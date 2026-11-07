<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';

// Filter parameters
$selectedBranch = isset($_GET['branch']) && $_GET['branch'] !== '' ? intval($_GET['branch']) : -1;
$fromDate       = trim($_GET['from_date'] ?? '');
$toDate         = trim($_GET['to_date'] ?? '');

// Pre-fetch branches for filter and table lookup
$branchesMap = [];
$qB = "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' ORDER BY id ASC";
$rB = mysqli_query($adController->MySQL, $qB);
if ($rB) {
    while ($bRow = mysqli_fetch_assoc($rB)) {
        $branchesMap[$bRow['id']] = ($language == 'ar' ? $bRow['name_ar'] : $bRow['name_en']) ?: $bRow['name_ar'];
    }
}

// Build query conditions
$whereConditions = ["storeid = '$storeid'"];

if ($selectedBranch >= 0) {
    if ($selectedBranch === 0) {
        $whereConditions[] = "(store_branch = '0' OR store_branch = '' OR store_branch IS NULL)";
    } else {
        $whereConditions[] = "FIND_IN_SET('$selectedBranch', store_branch)";
    }
}

if ($fromDate !== '') {
    $safeFrom = mysqli_real_escape_string($adController->MySQL, $fromDate);
    $whereConditions[] = "dated >= '$safeFrom 00:00:00'";
}

if ($toDate !== '') {
    $safeTo = mysqli_real_escape_string($adController->MySQL, $toDate);
    $whereConditions[] = "dated <= '$safeTo 23:59:59'";
}

$whereClause = "WHERE " . implode(" AND ", $whereConditions);

// Fetch expenditure records
$query = "SELECT * FROM expenditure $whereClause ORDER BY dated DESC, id DESC";
$res   = mysqli_query($adController->MySQL, $query);

$expenditures = [];
$filteredTotalCost = 0.0;
$recordsCount = 0;

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $expenditures[] = $row;
        $filteredTotalCost += (float)$row['cost'];
        $recordsCount++;
    }
}

// Calculate Global KPI stats
$today = date('Y-m-d');
$firstDayThisMonth = date('Y-m-01');

$kpiQ = "SELECT 
            SUM(cost) AS total_all,
            SUM(CASE WHEN DATE(dated) = '$today' THEN cost ELSE 0 END) AS total_today,
            SUM(CASE WHEN DATE(dated) >= '$firstDayThisMonth' THEN cost ELSE 0 END) AS total_month,
            COUNT(id) AS total_count
         FROM expenditure 
         WHERE storeid = '$storeid'";
$kpiRes = mysqli_query($adController->MySQL, $kpiQ);
$kpiData = $kpiRes ? mysqli_fetch_assoc($kpiRes) : [];

$kpiAll   = (float)($kpiData['total_all'] ?? 0);
$kpiToday = (float)($kpiData['total_today'] ?? 0);
$kpiMonth = (float)($kpiData['total_month'] ?? 0);
$kpiCount = (int)($kpiData['total_count'] ?? 0);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= defined('EXPENDITURE') ? EXPENDITURE : 'سجل المصروفات والنثريات' ?> - WAM Tech POS</title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        .kpi-card {
            border-radius: 12px;
            padding: 1.25rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            transition: transform 0.2s ease;
        }
        .kpi-card:hover {
            transform: translateY(-3px);
        }
        .kpi-card .kpi-icon {
            position: absolute;
            left: 15px;
            bottom: 15px;
            font-size: 2.5rem;
            opacity: 0.2;
        }
        .kpi-card .kpi-value {
            font-size: 1.6rem;
            font-weight: 700;
            margin-top: 0.4rem;
        }
        .bg-kpi-1 { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); }
        .bg-kpi-2 { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .bg-kpi-3 { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .bg-kpi-4 { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

        .filter-panel {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .badge-branch {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .badge-cost {
            font-size: 0.95rem;
            font-weight: 700;
            color: #b91c1c;
            background-color: #fef2f2;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #fecaca;
            display: inline-block;
        }

        @media print {
            .admin-sidebar, .admin-navbar, .page-header, .filter-panel, .no-print, .btn, .dataTables_filter, .dataTables_length, .dataTables_paginate, .dataTables_info {
                display: none !important;
            }
            .admin-content {
                padding: 0 !important;
                margin: 0 !important;
            }
            .card-pro {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <?php require_once("components/sidebar_pro.php"); ?>

        <div class="admin-content">
            <?php require_once("components/header_pro.php"); ?>

            <div class="container-fluid px-4 py-4">
                <!-- Page Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <h3 class="fw-bold mb-1" style="color: #1e293b;">
                            <i class="fas fa-receipt text-danger me-2"></i>
                            <?= defined('EXPENDITURE') ? EXPENDITURE : 'سجل المصروفات العامة والنثريات' ?>
                        </h3>
                        <p class="text-muted mb-0">إدارة ومتابعة سندات الصرف والمصروفات التشغيلية والنثرية بالفروع</p>
                    </div>
                    <div class="d-flex gap-2 no-print">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> طباعة السجل
                        </button>
                        <a href="new_expenditure.php" class="btn btn-primary" style="background:#2563eb; border-color:#2563eb;">
                            <i class="fas fa-plus-circle me-1"></i> <?= defined('ADD_EXPENDITURE') ? ADD_EXPENDITURE : 'إضافة مصروف جديد' ?>
                        </a>
                    </div>
                </div>

                <!-- KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="kpi-card bg-kpi-1">
                            <i class="fas fa-wallet kpi-icon"></i>
                            <div class="text-white-50 small fw-bold">إجمالي المصروفات (الكلي)</div>
                            <div class="kpi-value"><?= number_format($kpiAll, 2) ?> <small style="font-size:0.9rem;"><?= $currency ?></small></div>
                            <div class="small mt-1 text-white-50">إجمالي سندات الصرف المسجلة</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="kpi-card bg-kpi-2">
                            <i class="fas fa-calendar-alt kpi-icon"></i>
                            <div class="text-white-50 small fw-bold">مصروفات هذا الشهر</div>
                            <div class="kpi-value"><?= number_format($kpiMonth, 2) ?> <small style="font-size:0.9rem;"><?= $currency ?></small></div>
                            <div class="small mt-1 text-white-50"><?= date('F Y') ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="kpi-card bg-kpi-3">
                            <i class="fas fa-clock kpi-icon"></i>
                            <div class="text-white-50 small fw-bold">مصروفات اليوم</div>
                            <div class="kpi-value"><?= number_format($kpiToday, 2) ?> <small style="font-size:0.9rem;"><?= $currency ?></small></div>
                            <div class="small mt-1 text-white-50"><?= date('d/m/Y') ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="kpi-card bg-kpi-4">
                            <i class="fas fa-file-invoice-dollar kpi-icon"></i>
                            <div class="text-white-50 small fw-bold">عدد سندات الصرف</div>
                            <div class="kpi-value"><?= number_format($kpiCount) ?></div>
                            <div class="small mt-1 text-white-50">سند مصرفي مسجل</div>
                        </div>
                    </div>
                </div>

                <!-- Filter Panel -->
                <div class="filter-panel no-print">
                    <form method="GET" action="expenditure.php" id="filterForm" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-secondary small">
                                <i class="fas fa-code-branch me-1"></i> الفرع
                            </label>
                            <select name="branch" class="form-select select2">
                                <option value="-1">-- كافة الفروع --</option>
                                <option value="0" <?= $selectedBranch === 0 ? 'selected' : '' ?>>عام (كافة الفروع)</option>
                                <?php foreach ($branchesMap as $bId => $bName): ?>
                                    <option value="<?= $bId ?>" <?= $selectedBranch === $bId ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($bName) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-secondary small">
                                <i class="fas fa-calendar-day me-1"></i> من تاريخ
                            </label>
                            <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($fromDate) ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-secondary small">
                                <i class="fas fa-calendar-day me-1"></i> إلى تاريخ
                            </label>
                            <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($toDate) ?>">
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-filter me-1"></i> تطبيق الفلتر
                            </button>
                            <a href="expenditure.php" class="btn btn-outline-secondary" title="إعادة ضبط">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>

                        <!-- Quick Period Shortcuts -->
                        <div class="col-12 pt-2 border-top">
                            <span class="text-muted small me-2 fw-bold">فترات سريعة:</span>
                            <button type="button" class="btn btn-sm btn-outline-primary me-1 quick-date" data-range="today">اليوم</button>
                            <button type="button" class="btn btn-sm btn-outline-primary me-1 quick-date" data-range="this_month">هذا الشهر</button>
                            <button type="button" class="btn btn-sm btn-outline-primary me-1 quick-date" data-range="last_month">الشهر السابق</button>
                            <button type="button" class="btn btn-sm btn-outline-primary me-1 quick-date" data-range="this_year">هذا العام</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-date" data-range="all">كافة الفترات</button>
                            
                            <?php if ($fromDate !== '' || $toDate !== '' || $selectedBranch >= 0): ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle float-start mt-1">
                                    مجموع الفترة المحددة: <?= number_format($filteredTotalCost, 2) ?> <?= $currency ?> (<?= $recordsCount ?> سند)
                                </span>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Expenditures Table Card -->
                <div class="card card-pro border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-list text-primary me-2"></i> قائمة سندات ومصروفات التشغيل
                        </h5>
                        <div class="text-muted small">
                            إجمالي السجلات: <span class="fw-bold text-dark"><?= count($expenditures) ?></span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 datatable-export" id="expendituresTable" style="width:100%;">
                                <thead class="table-light text-secondary">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>الفرع</th>
                                        <th>بيان / وصف المصروف</th>
                                        <th style="width: 140px;">التاريخ</th>
                                        <th style="width: 150px; text-align: left;">المبلغ (<?= $currency ?>)</th>
                                        <th style="width: 110px; text-align: center;" class="no-print">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $idx = 1;
                                    foreach ($expenditures as $row):
                                        $idval = urlencode($adController->encrypt_decrypt(1, $row['id'], 0));
                                        $tableName = urlencode($adController->encrypt_decrypt(1, 'expenditure', 0));
                                        $secondIdval = urlencode($adController->encrypt_decrypt(1, $idval, 0));

                                        // Branch formatting
                                        $branchNames = [];
                                        $bRaw = trim($row['store_branch'] ?? '');
                                        if ($bRaw !== '' && $bRaw !== '0') {
                                            $bIds = explode(',', $bRaw);
                                            foreach ($bIds as $bId) {
                                                $bId = trim($bId);
                                                if (isset($branchesMap[$bId])) {
                                                    $branchNames[] = htmlspecialchars($branchesMap[$bId]);
                                                }
                                            }
                                        }
                                        $branchDisplay = !empty($branchNames) ? implode(', ', $branchNames) : 'عام (كافة الفروع)';

                                        $desc = ($language == 'ar' ? ($row['desc_ar'] ?: $row['desc_en']) : ($row['desc_en'] ?: $row['desc_ar'])) ?? '';
                                        $datedDisplay = !empty($row['dated']) ? date('Y-m-d', strtotime($row['dated'])) : '-';
                                        $cost = (float)($row['cost'] ?? 0);
                                    ?>
                                        <tr id="row-exp-<?= $row['id'] ?>">
                                            <td class="text-muted small"><?= $idx++ ?></td>
                                            <td>
                                                <span class="badge-branch">
                                                    <i class="fas fa-store-alt text-secondary me-1"></i>
                                                    <?= $branchDisplay ?>
                                                </span>
                                            </td>
                                            <td class="fw-semibold text-dark">
                                                <?= htmlspecialchars($desc) ?>
                                            </td>
                                            <td>
                                                <i class="far fa-calendar-alt text-muted me-1"></i>
                                                <?= $datedDisplay ?>
                                            </td>
                                            <td style="text-align: left;">
                                                <span class="badge-cost">
                                                    <?= number_format($cost, 2) ?>
                                                </span>
                                            </td>
                                            <td style="text-align: center;" class="no-print">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="edit_exp.php?sd=<?= $secondIdval ?>" class="btn btn-outline-primary" title="تعديل المصروف">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger btn-delete-exp" 
                                                            data-id="<?= $row['id'] ?>" 
                                                            data-name="<?= htmlspecialchars($desc, ENT_QUOTES) ?>" 
                                                            data-enc-id="<?= $idval ?>"
                                                            data-enc-table="<?= $tableName ?>"
                                                            title="حذف المصروف">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-light fw-bold">
                                        <td colspan="4" class="text-end">المجموع الإجمالي المعروض:</td>
                                        <td style="text-align: left; color: #b91c1c; font-size: 1.05rem;">
                                            <?= number_format($filteredTotalCost, 2) ?> <?= $currency ?>
                                        </td>
                                        <td class="no-print"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once("footer.php"); ?>
    
    <script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({ width: '100%', dir: 'rtl' });
        }

        // Initialize DataTable with export buttons
        if ($.fn.DataTable) {
            $('#expendituresTable').DataTable({
                responsive: true,
                language: {
                    url: 'js/datatables-arabic.json'
                },
                order: [[3, 'desc']],
                dom: "<'row mb-3'<'col-sm-6'B><'col-sm-6'f>>rt<'row mt-3'<'col-sm-6'i><'col-sm-6'p>>",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel me-1 text-success"></i> تصدير إكسيل',
                        className: 'btn btn-sm btn-outline-success border',
                        exportOptions: { columns: [0, 1, 2, 3, 4] }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-1 text-primary"></i> طباعة الجدول',
                        className: 'btn btn-sm btn-outline-primary border',
                        exportOptions: { columns: [0, 1, 2, 3, 4] }
                    }
                ]
            });
        }

        // Quick Period Shortcuts
        $('.quick-date').on('click', function() {
            var range = $(this).data('range');
            var now = new Date();
            var y = now.getFullYear();
            var m = String(now.getMonth() + 1).padStart(2, '0');
            var d = String(now.getDate()).padStart(2, '0');

            if (range === 'today') {
                $('input[name="from_date"]').val(y + '-' + m + '-' + d);
                $('input[name="to_date"]').val(y + '-' + m + '-' + d);
            } else if (range === 'this_month') {
                $('input[name="from_date"]').val(y + '-' + m + '-01');
                $('input[name="to_date"]').val(y + '-' + m + '-' + d);
            } else if (range === 'last_month') {
                var prevMonth = new Date(now.getFullYear(), now.getMonth(), 0);
                var lmY = prevMonth.getFullYear();
                var lmM = String(prevMonth.getMonth() + 1).padStart(2, '0');
                var lmD = String(prevMonth.getDate()).padStart(2, '0');
                $('input[name="from_date"]').val(lmY + '-' + lmM + '-01');
                $('input[name="to_date"]').val(lmY + '-' + lmM + '-' + lmD);
            } else if (range === 'this_year') {
                $('input[name="from_date"]').val(y + '-01-01');
                $('input[name="to_date"]').val(y + '-' + m + '-' + d);
            } else if (range === 'all') {
                $('input[name="from_date"]').val('');
                $('input[name="to_date"]').val('');
            }
            $('#filterForm').submit();
        });

        // Delete Expenditure Action with SweetAlert2
        $(document).on('click', '.btn-delete-exp', function() {
            var btn = $(this);
            var expId = btn.data('id');
            var expName = btn.data('name');
            var encId = btn.data('enc-id');
            var encTable = btn.data('enc-table');

            var confirmFn = function() {
                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: {
                        f: 'delete_data',
                        a: encTable,
                        b: encId,
                        csrf_token: '<?= htmlspecialchars(csrf_token()) ?>'
                    },
                    success: function(response) {
                        if ($.trim(response) === '1') {
                            $('#row-exp-' + expId).fadeOut(400, function() {
                                $(this).remove();
                            });
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم الحذف',
                                    text: 'تم حذف سند المصروف بنجاح',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('خطأ', 'تعذر حذف المصروف: قد لا تملك الصلاحية اللازمة', 'error');
                            } else {
                                alert('تعذر حذف المصروف');
                            }
                        }
                    },
                    error: function() {
                        alert('حدث خطأ في الاتصال بالسيرفر');
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'هل أنت متأكد من الحذف؟',
                    text: 'سيتم حذف المصروف: "' + expName + '" نهائياً!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        confirmFn();
                    }
                });
            } else {
                if (confirm('هل أنت متأكد من حذف سند المصروف: ' + expName + '؟')) {
                    confirmFn();
                }
            }
        });
    });
    </script>
</body>
</html>
