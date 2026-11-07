<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$storeid  = intval($_SESSION['storeid'] ?? 1);
$pgName   = basename(__FILE__);

// Fetch incoming entries
$query = "SELECT inc.*, b.name_ar AS branch_name_ar, b.name_en AS branch_name_en,
                 COALESCE((SELECT SUM(quantity) FROM income_items ii WHERE ii.income_No = inc.income_No AND ii.storeid = inc.storeid), 0) AS total_qty
          FROM income inc
          LEFT JOIN branches b ON b.id = inc.branch
          WHERE (inc.storeid = '$storeid' OR inc.storeid = '0') AND inc.state = '1' 
          ORDER BY inc.date DESC, inc.id DESC";
$res = mysqli_query($adController->MySQL, $query);

$incomeRows = [];
$kpiTotal = 0;
$kpiPurchases = 0;
$kpiTransfers = 0;
$kpiManual    = 0;

if ($res) {
    while ($data = mysqli_fetch_assoc($res)) {
        $kpiTotal++;
        $branchName = ($language == 'en' && !empty($data['branch_name_en'])) ? $data['branch_name_en'] : (!empty($data['branch_name_ar']) ? $data['branch_name_ar'] : '---');

        $invoice       = $data["income_No"] ?? '';
        $type          = $data["invoice_type"] ?? '';
        $purch_invoice = $data["invoice_No"] ?? '';
        $date          = !empty($data['date']) ? date('Y-m-d', strtotime($data['date'])) : '---';
        $totalQty      = (float)($data['total_qty'] ?? 0);

        $idval        = urlencode($adController->encrypt_decrypt(1, $data['id'], 0));
        $invoiceTable = urlencode($adController->encrypt_decrypt(1, 'income', 0));
        $detailTable  = urlencode($adController->encrypt_decrypt(1, 'income_items', 0));
        $secondIdval  = urlencode($adController->encrypt_decrypt(1, $idval, 0));

        $badgeClass = 'bg-secondary-subtle text-secondary';
        $typeLabel  = 'إذن إضافة يدوي';
        if ($type === 'PURCHASE') {
            $typeLabel = 'فاتورة مشتريات';
            $badgeClass = 'bg-primary-subtle text-primary';
            $kpiPurchases++;
        } elseif ($type === 'SALE_RETURN' || $type === 'SALES_RET') {
            $typeLabel = 'مرتجع مبيعات';
            $badgeClass = 'bg-warning-subtle text-dark';
            $kpiPurchases++;
        } elseif ($type === 'STORE_TRANSFAIR') {
            $typeLabel = 'تحويل مخزني';
            $badgeClass = 'bg-info-subtle text-info';
            $kpiTransfers++;
        } else {
            $kpiManual++;
        }

        $incomeRows[] = [
            'id'            => $data['id'],
            'invoice'       => $invoice,
            'typeLabel'     => $typeLabel,
            'badgeClass'    => $badgeClass,
            'purch_invoice' => $purch_invoice,
            'branch'        => $branchName,
            'date'          => $date,
            'totalQty'      => $totalQty,
            'idval'         => $idval,
            'invoiceTable'  => $invoiceTable,
            'detailTable'   => $detailTable,
            'secondIdval'   => $secondIdval,
            'isLinked'      => !empty($purch_invoice)
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أذونات الإضافة والوارد المخزني | WAM Tech ERP</title>
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
                            <i class="fas fa-arrow-down text-success me-2"></i> أذونات الإضافة والوارد المخزني
                        </h2>
                        <p class="text-muted mb-0">سجل وارد البضائع، المشتريات، والتحويلات الواردة للمستودعات</p>
                    </div>
                    <div>
                        <a href="add_income.php" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i> إنشاء إذن إضافة وارد
                        </a>
                    </div>
                </div>

                <!-- KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">إجمالي أذونات الوارد</div>
                                <div class="fs-4 fw-bold text-dark font-monospace"><?= number_format($kpiTotal) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#dcfce7; color:#16a34a;">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">وارد مشتريات ومرتجعات</div>
                                <div class="fs-4 fw-bold text-primary font-monospace"><?= number_format($kpiPurchases) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#e0f2fe; color:#0284c7;">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">تحويلات واردة</div>
                                <div class="fs-4 fw-bold text-info font-monospace"><?= number_format($kpiTransfers) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#e0e7ff; color:#4338ca;">
                                <i class="fas fa-truck-loading"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">أذونات إضافة يدوية</div>
                                <div class="fs-4 fw-bold text-secondary font-monospace"><?= number_format($kpiManual) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#f1f5f9; color:#475569;">
                                <i class="fas fa-pencil-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-list text-secondary me-2"></i> سجل حركة الوارد المخزني
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table id="incomeTable" class="table table-hover align-middle table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم إذن الوارد</th>
                                        <th>نوع الوارد</th>
                                        <th>المستند المرتبط</th>
                                        <th>الفرع</th>
                                        <th class="text-center">إجمالي الكمية</th>
                                        <th>التاريخ</th>
                                        <th class="text-center" style="width: 120px;">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($incomeRows as $row): ?>
                                        <tr id="row-<?= $row['id'] ?>">
                                            <td>
                                                <span class="badge bg-light text-success border font-monospace px-2 py-1 fs-6">
                                                    <?= htmlspecialchars($row['invoice']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?= $row['badgeClass'] ?> px-2 py-1">
                                                    <?= htmlspecialchars($row['typeLabel']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($row['purch_invoice']): ?>
                                                    <span class="font-monospace text-dark fw-bold"><?= htmlspecialchars($row['purch_invoice']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">مباشر (بدون ربط)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="text-secondary">
                                                    <i class="fas fa-code-branch me-1"></i> <?= htmlspecialchars($row['branch']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center font-monospace fw-bold text-success">
                                                <?= number_format($row['totalQty'], 2) ?>
                                            </td>
                                            <td class="font-monospace text-secondary"><?= htmlspecialchars($row['date']) ?></td>
                                            <td class="text-center">
                                                <?php if (!$row['isLinked']): ?>
                                                    <a href="edit_income.php?sd=<?= $row['secondIdval'] ?>" class="btn btn-sm btn-outline-info" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-income" 
                                                            data-table="<?= $row['invoiceTable'] ?>" 
                                                            data-detail="<?= $row['detailTable'] ?>" 
                                                            data-idval="<?= $row['idval'] ?>" 
                                                            data-id="<?= $row['id'] ?>" 
                                                            title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border px-2 py-1">
                                                        <i class="fas fa-link me-1"></i> مرتبط تلقائياً
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
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
            $('#incomeTable').DataTable({
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
                order: [[0, 'desc']],
                pageLength: 25
            });

            // Delete Incoming Note
            $(document).on('click', '.btn-delete-income', function() {
                if (!confirm('هل أنت متأكد من حذف إذن الإضافة الوارد؟ سيتم عكس تأثير الكميات من المخزن.')) {
                    return;
                }
                var invTable = $(this).data('table');
                var detTable = $(this).data('detail');
                var idval = $(this).data('idval');
                var id = $(this).data('id');
                var row = $('#row-' + id);

                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: {
                        'f': 'delete_invoice',
                        'i': invTable,
                        'd': detTable,
                        'id': idval
                    },
                    success: function(resp) {
                        if ($.trim(resp) === '1' || parseInt(resp) > 0) {
                            row.fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            alert('تعذر حذف الإذن: ' + resp);
                        }
                    },
                    error: function() {
                        alert('حدث خطأ في الاتصال بالخادم.');
                    }
                });
            });
        });
    </script>
</body>
</html>
