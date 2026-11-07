<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';
$pgName   = basename(__FILE__);

// Fetch Returned Sales
$query = "SELECT sr.*, 
                 c.name_ar AS customer_name_ar, 
                 c.name_en AS customer_name_en, 
                 b.name_ar AS branch_name_ar,
                 b.name_en AS branch_name_en
          FROM sales_ret sr 
          LEFT JOIN customers c ON sr.customer = c.id AND (c.storeid = '$storeid' OR c.storeid = '0') 
          LEFT JOIN branches b ON sr.branch = b.id AND (b.storeid = '$storeid' OR b.storeid = '0') 
          WHERE (sr.storeid = '$storeid' OR sr.storeid = '0') AND sr.state = '1' 
          ORDER BY sr.id DESC";

$res = mysqli_query($adController->MySQL, $query);

$returns = [];
$totalRetAmount = 0.0;
$totalCashRefund = 0.0;
$totalCreditReduction = 0.0;
$totalVatRet = 0.0;
$retCount = 0;

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $returns[] = $row;
        $retCount++;
        $allTot = (float)($row['all_total'] ?? 0);
        $vatTot = (float)($row['vat_all'] ?? 0);
        $isCash = ((int)($row['paymentType'] ?? 0) === 1);

        $totalRetAmount += $allTot;
        $totalVatRet    += $vatTot;
        if ($isCash) {
            $totalCashRefund += $allTot;
        } else {
            $totalCreditReduction += $allTot;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إدارة مرتجع المبيعات - ERP WAM Tech</title>
    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <style>
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
        @media print {
            .admin-sidebar, .admin-navbar, .btn-no-print {
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
                            <i class="fas fa-undo-alt text-danger me-2"></i>
                            <?=RETURNED_SALES?> - مرتجع فواتير المبيعات
                        </h1>
                        <div class="page-breadcrumb text-muted fs-7">
                            <a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-home"></i> الرئيسية</a>
                            <span class="mx-2">/</span>
                            <a href="saleInvoice.php" class="text-decoration-none text-muted">المبيعات</a>
                            <span class="mx-2">/</span>
                            <span class="text-danger fw-semibold">سجل المرتجعات</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 btn-no-print">
                        <a href="saleInvoice.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> فواتير المبيعات
                        </a>
                        <a href="add_salesRet.php" class="btn btn-danger">
                            <i class="fas fa-plus-circle me-1"></i> إنشاء إشعار مرتجع مبيعات
                        </a>
                    </div>
                </div>

                <!-- KPI Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">إجمالي قيمة المرتجعات</div>
                                <div class="stat-value text-danger"><?=flout_format($totalRetAmount)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1"><?=$retCount?> إشعار مرتجع مسجل</div>
                            </div>
                            <div class="stat-icon bg-danger-subtle text-danger">
                                <i class="fas fa-undo-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">مسترد نقداً للعميل</div>
                                <div class="stat-value text-primary"><?=flout_format($totalCashRefund)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1">منصرف من الصندوق / الخزينة</div>
                            </div>
                            <div class="stat-icon bg-primary-subtle text-primary">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">تخفيض مديونية العميل (آجل)</div>
                                <div class="stat-value text-info"><?=flout_format($totalCreditReduction)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1">تخفيض حساب العميل الجاري</div>
                            </div>
                            <div class="stat-icon bg-info-subtle text-info">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">ضريبة القيمة المضافة المستردة</div>
                                <div class="stat-value text-warning"><?=flout_format($totalVatRet)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1">14% مستبعدة من الإقرار</div>
                            </div>
                            <div class="stat-icon bg-warning-subtle text-warning">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="table-pro-wrap">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="fs-5 fw-bold mb-0 text-dark">
                            <i class="fas fa-list text-danger me-2"></i>
                            قائمة إشعارات مرتجع المبيعات
                        </h2>
                        <span class="badge bg-light text-danger border px-3 py-2">عدد السجلات: <?=$retCount?></span>
                    </div>

                    <div class="table-responsive">
                        <table id="retTable" class="table table-hover align-middle table-striped table-bordered text-center w-100">
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
                                    <th>طريقة التسوية</th>
                                    <th class="btn-no-print">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $idx = 0;
                                foreach ($returns as $data):
                                    $idx++;
                                    $invoice      = htmlspecialchars($data["invoicenumber"] ?? '');
                                    $salesInvoice = htmlspecialchars($data['salesInvoice'] ?? '---');
                                    $customer     = ($language == 'ar' ? ($data['customer_name_ar'] ?? '') : ($data['customer_name_en'] ?? '')) ?: ($data['customer_name_ar'] ?? 'عميل غير محدد');
                                    $branch       = ($language == 'ar' ? ($data['branch_name_ar'] ?? '') : ($data['branch_name_en'] ?? '')) ?: ($data['branch_name_ar'] ?? 'الفرع الرئيسي');
                                    $date         = substr($data['invoice_date'] ?? '', 0, 16);
                                    $gross        = (float)($data['gross_total'] ?? 0);
                                    $vatAll       = (float)($data['vat_all'] ?? 0);
                                    $allTotal     = (float)($data['all_total'] ?? 0);
                                    $isCash       = ((int)($data['paymentType'] ?? 0) === 1);

                                    $idval        = urlencode($adController->encrypt_decrypt(1, $data["invoicenumber"], 0));
                                    $invoiceTable = urlencode($adController->encrypt_decrypt(1, 'sales_ret', 0));
                                    $otherTable   = urlencode($adController->encrypt_decrypt(1, 'income', 0));
                                ?>
                                <tr>
                                    <td><?=$idx?></td>
                                    <td>
                                        <a href="edit_salesRet.php?sd=<?=$idval?>" class="fw-bold text-danger text-decoration-none">
                                            <?=$invoice?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="edit_saleInvoice.php?sd=<?=urlencode($salesInvoice)?>" class="text-primary text-decoration-none fw-semibold">
                                            <?=$salesInvoice?>
                                        </a>
                                    </td>
                                    <td class="text-muted fs-7"><?=$date?></td>
                                    <td class="fw-semibold text-dark"><?=$customer?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?=$branch?></span></td>
                                    <td><?=flout_format($gross)?></td>
                                    <td class="text-danger"><?=flout_format($vatAll)?></td>
                                    <td class="fw-bold text-danger"><?=flout_format($allTotal)?></td>
                                    <td>
                                        <?php if ($isCash): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">استرداد نقدي</span>
                                        <?php else: ?>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle">تخفيض رصيد العميل</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="btn-no-print">
                                        <div class="btn-group btn-group-sm">
                                            <a href="reports/reSales.php?sd=<?=urlencode($invoice)?>" target="_blank" class="btn btn-outline-secondary" title="طباعة إشعار المرتجع (PDF)">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="edit_salesRet.php?sd=<?=$idval?>" class="btn btn-outline-primary" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDeleteRet('<?=$invoiceTable?>', '<?=$idval?>', '<?=$otherTable?>', '<?=$invoice?>')" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

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
            $('#retTable').DataTable({
                language: {
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
                },
                dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
                buttons: [
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel text-success me-1"></i> تصدير Excel', className: 'btn btn-outline-success btn-sm' },
                    { extend: 'print', text: '<i class="fas fa-print me-1"></i> طباعة الجدول', className: 'btn btn-outline-secondary btn-sm' }
                ],
                order: [[0, 'asc']]
            });
        });

        function confirmDeleteRet(vt, id, ot, invNo) {
            if (confirm("هل أنت متأكد من حذف إشعار مرتجع المبيعات رقم (" + invNo + ")؟\nسيتم عكس حركات المخزون والقيود المحاسبية.")) {
                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: {
                        'f': 'delete_invoice',
                        'vt': vt,
                        'id': id,
                        'ot': ot,
                        'csrf_token': '<?=csrf_token()?>'
                    },
                    success: function(res) {
                        if (parseInt($.trim(res)) > 0) {
                            alert("تم حذف إشعار المرتجع بنجاح.");
                            location.reload();
                        } else {
                            alert("استجابة الحذف: " + res);
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert("حدث خطأ أثناء محاولة الحذف: " + xhr.statusText);
                    }
                });
            }
        }
    </script>
</body>
</html>
