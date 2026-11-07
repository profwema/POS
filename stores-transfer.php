<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$storeid  = intval($_SESSION['storeid'] ?? 1);
$pgName   = basename(__FILE__);

// Fetch store transfers with JOINs
$query = "SELECT st.*, 
                 sf.name_ar AS from_name_ar, sf.name_en AS from_name_en,
                 st_to.name_ar AS to_name_ar, st_to.name_en AS to_name_en,
                 COALESCE((SELECT SUM(quantity) FROM store_trans_items sti WHERE sti.trans_number = st.trans_number), 0) AS total_items_qty
          FROM store_trans st
          LEFT JOIN stores sf ON sf.id = st.storeFrom
          LEFT JOIN stores st_to ON st_to.id = st.storeTo
          WHERE (st.storeid = '$storeid' OR st.storeid = '0') AND st.state = '1'
          ORDER BY st.id DESC";
$res = mysqli_query($adController->MySQL, $query);

$transRows = [];
$kpiTotalTrans = 0;
$kpiTodayTrans = 0;
$kpiTotalQty   = 0;
$todayDate     = date('Y-m-d');

if ($res) {
    while ($data = mysqli_fetch_assoc($res)) {
        $invoice    = $data["trans_number"] ?? '';
        $from       = ($language == 'en' && !empty($data['from_name_en'])) ? $data['from_name_en'] : (!empty($data['from_name_ar']) ? $data['from_name_ar'] : '---');
        $to         = ($language == 'en' && !empty($data['to_name_en'])) ? $data['to_name_en'] : (!empty($data['to_name_ar']) ? $data['to_name_ar'] : '---');
        $trans_date = !empty($data['trans_date']) ? date('Y-m-d', strtotime($data['trans_date'])) : '---';
        $dated      = !empty($data['dated']) ? date('Y-m-d H:i', strtotime($data['dated'])) : '---';
        $totalQty   = (float)($data['total_items_qty'] ?? 0);
        $note       = $data['note'] ?? '';

        $kpiTotalTrans++;
        $kpiTotalQty += $totalQty;
        if ($trans_date === $todayDate) {
            $kpiTodayTrans++;
        }

        $idval       = urlencode($adController->encrypt_decrypt(1, $invoice, 0));
        $secondIdval = urlencode($adController->encrypt_decrypt(1, $idval, 0));

        $transRows[] = [
            'id'          => $data['id'],
            'invoice'     => $invoice,
            'from'        => $from,
            'to'          => $to,
            'trans_date'  => $trans_date,
            'dated'       => $dated,
            'totalQty'    => $totalQty,
            'note'        => $note,
            'idval'       => $idval,
            'secondIdval' => $secondIdval
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحويل بين المستودعات | WAM Tech ERP</title>
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
                            <i class="fas fa-truck-moving text-primary me-2"></i> التحويل بين المستودعات والفروع
                        </h2>
                        <p class="text-muted mb-0">سجل أذونات النقل والتحويل المخزني الداخلي بين الفروع والمستودعات</p>
                    </div>
                    <div>
                        <a href="add_storeTrans.php" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> إنشاء تحويل مخزني جديد
                        </a>
                    </div>
                </div>

                <!-- KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-4 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">إجمالي عمليات التحويل</div>
                                <div class="fs-4 fw-bold text-dark font-monospace"><?= number_format($kpiTotalTrans) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#e0f2fe; color:#0284c7;">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">تحويلات اليوم</div>
                                <div class="fs-4 fw-bold text-primary font-monospace"><?= number_format($kpiTodayTrans) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#dcfce7; color:#16a34a;">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">إجمالي الكميات المنقولة</div>
                                <div class="fs-4 fw-bold text-success font-monospace"><?= number_format($kpiTotalQty, 2) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#fef3c7; color:#d97706;">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-list text-secondary me-2"></i> سجل أذونات التحويل المخزني
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table id="transTable" class="table table-hover align-middle table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم التحويل</th>
                                        <th>من مخزن</th>
                                        <th>إلى مخزن</th>
                                        <th class="text-center">إجمالي الكمية</th>
                                        <th>تاريخ التحويل</th>
                                        <th>تاريخ الإنشاء</th>
                                        <th>ملاحظات</th>
                                        <th class="text-center" style="width: 120px;">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transRows as $row): ?>
                                        <tr id="row-<?= $row['id'] ?>">
                                            <td>
                                                <span class="badge bg-light text-primary border font-monospace px-2 py-1 fs-6">
                                                    <?= htmlspecialchars($row['invoice']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                                    <i class="fas fa-warehouse me-1"></i> <?= htmlspecialchars($row['from']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info px-2 py-1">
                                                    <i class="fas fa-warehouse me-1"></i> <?= htmlspecialchars($row['to']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center fw-bold text-success font-monospace">
                                                <?= number_format($row['totalQty'], 2) ?>
                                            </td>
                                            <td class="font-monospace text-secondary"><?= htmlspecialchars($row['trans_date']) ?></td>
                                            <td class="font-monospace text-muted small"><?= htmlspecialchars($row['dated']) ?></td>
                                            <td class="small text-muted"><?= htmlspecialchars($row['note'] ?: '---') ?></td>
                                            <td class="text-center">
                                                <a href="edit_storeTrans.php?sd=<?= $row['secondIdval'] ?>" class="btn btn-sm btn-outline-info" title="تعديل التحويل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-trans" data-idval="<?= $row['idval'] ?>" data-id="<?= $row['id'] ?>" title="حذف وإلغاء التحويل">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
            $('#transTable').DataTable({
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

            // Delete Transfer
            $(document).on('click', '.btn-delete-trans', function() {
                if (!confirm('هل أنت متأكد من رغبتك في حذف هذا التحويل المخزني؟ سيتم إلغاء أذونات الصادر والوارد المرتبطة به واسترجاع أرصدة المخازن.')) {
                    return;
                }
                var idval = $(this).data('idval');
                var id = $(this).data('id');
                var row = $('#row-' + id);

                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: {
                        'f': 'delete_trans',
                        'id': idval
                    },
                    success: function(resp) {
                        if ($.trim(resp) === '1' || parseInt(resp) > 0) {
                            row.fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            alert('تعذر حذف التحويل: ' + resp);
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
