<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$storeid  = intval($_SESSION['storeid'] ?? 1);
$pgName   = basename(__FILE__);

// Fetch journal entries
$query = "SELECT j.*, b.name_ar AS branch_name_ar, b.name_en AS branch_name_en 
          FROM journal j
          LEFT JOIN branches b ON b.id = j.brarnch
          WHERE j.state = '1' 
          ORDER BY j.date DESC, j.id DESC";
$res = mysqli_query($adController->MySQL, $query);

$journalRows = [];
$kpiTotalJournals = 0;
$kpiTotalDebit    = 0;
$kpiTotalCredit   = 0;

if ($res) {
    while ($data = mysqli_fetch_assoc($res)) {
        $kpiTotalJournals++;
        $journalNo   = $data["journalNo"] ?? '';										
        $docNo       = $data["docNo"] ?? '';
        $disc        = $data["disc"] ?? '';
        $totalDebit  = floatval($data["totalDebit"] ?? 0);
        $totalCredit = floatval($data["totalCredit"] ?? 0);
        $date        = !empty($data['date']) ? date('Y-m-d', strtotime($data['date'])) : '---';

        $kpiTotalDebit  += $totalDebit;
        $kpiTotalCredit += $totalCredit;

        $branchName = ($language == 'en' && !empty($data['branch_name_en'])) ? $data['branch_name_en'] : (!empty($data['branch_name_ar']) ? $data['branch_name_ar'] : '---');

        $encNo = urlencode($adController->encrypt_decrypt(1, $journalNo, 0));

        $journalRows[] = [
            'id'          => $data['id'],
            'journalNo'   => $journalNo,
            'encNo'       => $encNo,
            'docNo'       => $docNo,
            'branch'      => $branchName,
            'disc'        => $disc,
            'totalDebit'  => $totalDebit,
            'totalCredit' => $totalCredit,
            'date'        => $date
        ];
    }
}
$isBalanced = abs($kpiTotalDebit - $kpiTotalCredit) < 0.05;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قيود اليومية العامة | WAM Tech ERP</title>
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
                            <i class="fas fa-book text-primary me-2"></i> دفتر وسجل قيود اليومية العامة
                        </h2>
                        <p class="text-muted mb-0">سجل القيود المحاسبية المزدوجة ومتابعة توازن الحركات المالية</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="accounts.php" class="btn btn-outline-secondary">
                            <i class="fas fa-sitemap me-1"></i> شجرة الحسابات
                        </a>
                        <a href="add_journal.php" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> إضافة قيد يومية جديد
                        </a>
                    </div>
                </div>

                <!-- KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">إجمالي القيود المسجلة</div>
                                <div class="fs-4 fw-bold text-dark font-monospace"><?= number_format($kpiTotalJournals) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#e0f2fe; color:#0284c7;">
                                <i class="fas fa-book-open"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">إجمالي المدين (Debit)</div>
                                <div class="fs-4 fw-bold text-success font-monospace"><?= number_format($kpiTotalDebit, 2) ?> <small class="fs-6 text-muted">ج.م</small></div>
                            </div>
                            <div class="kpi-icon" style="background:#dcfce7; color:#16a34a;">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">إجمالي الدائن (Credit)</div>
                                <div class="fs-4 fw-bold text-danger font-monospace"><?= number_format($kpiTotalCredit, 2) ?> <small class="fs-6 text-muted">ج.م</small></div>
                            </div>
                            <div class="kpi-icon" style="background:#fee2e2; color:#dc2626;">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">حالة التوازن العام</div>
                                <div class="fs-5 fw-bold <?= $isBalanced ? 'text-success' : 'text-danger' ?>">
                                    <i class="fas <?= $isBalanced ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-1"></i>
                                    <?= $isBalanced ? 'الدفاتر متزنة 100%' : 'يوجد فارق غير متزن' ?>
                                </div>
                            </div>
                            <div class="kpi-icon" style="background:<?= $isBalanced ? '#f0fdf4' : '#fff1f2' ?>; color:<?= $isBalanced ? '#16a34a' : '#e11d48' ?>;">
                                <i class="fas fa-scale-balanced"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-list text-secondary me-2"></i> سجل قيود اليومية العامة
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table id="journalTable" class="table table-hover align-middle table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم القيد</th>
                                        <th>رقم المستند</th>
                                        <th>الفرع</th>
                                        <th>البيان / الشرح</th>
                                        <th class="text-end text-success">إجمالي المدين</th>
                                        <th class="text-end text-danger">إجمالي الدائن</th>
                                        <th>التاريخ</th>
                                        <th class="text-center" style="width: 120px;">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($journalRows as $row): ?>
                                        <tr id="row-<?= $row['id'] ?>">
                                            <td>
                                                <span class="badge bg-light text-primary border font-monospace px-2 py-1 fs-6">
                                                    <?= htmlspecialchars($row['journalNo']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= $row['docNo'] ? htmlspecialchars($row['docNo']) : '<span class="text-muted">---</span>' ?>
                                            </td>
                                            <td>
                                                <span class="text-secondary">
                                                    <i class="fas fa-code-branch me-1"></i> <?= htmlspecialchars($row['branch']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($row['disc'] ?: '---') ?></td>
                                            <td class="text-end font-monospace fw-bold text-success">
                                                <?= number_format($row['totalDebit'], 2) ?>
                                            </td>
                                            <td class="text-end font-monospace fw-bold text-danger">
                                                <?= number_format($row['totalCredit'], 2) ?>
                                            </td>
                                            <td class="font-monospace text-secondary"><?= htmlspecialchars($row['date']) ?></td>
                                            <td class="text-center">
                                                <a href="edit_journal.php?no=<?= $row['encNo'] ?>" class="btn btn-sm btn-outline-info" title="تعديل القيد">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-journal" data-no="<?= $row['encNo'] ?>" data-id="<?= $row['id'] ?>" title="حذف القيد">
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
            $('#journalTable').DataTable({
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

            // Delete Journal Entry
            $(document).on('click', '.btn-delete-journal', function() {
                if (!confirm('هل أنت متأكد من رغبتك في حذف قيد اليومية هذا وجميع بنوده؟')) {
                    return;
                }
                var no = $(this).data('no');
                var id = $(this).data('id');
                var row = $('#row-' + id);

                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: {
                        'f': 'delete_journal',
                        'no': no
                    },
                    success: function(resp) {
                        if ($.trim(resp) === '1' || parseInt(resp) > 0) {
                            row.fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            alert('تعذر حذف قيد اليومية: ' + resp);
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
