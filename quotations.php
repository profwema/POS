<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';
$pgName   = basename(__FILE__);

// Fetch Quotations
$query = "SELECT q.*, 
                 c.name_ar AS customer_name_ar, 
                 c.name_en AS customer_name_en, 
                 b.name_ar AS branch_name_ar,
                 b.name_en AS branch_name_en
          FROM quote q 
          LEFT JOIN customers c ON q.supplier = c.id AND (c.storeid = '$storeid' OR c.storeid = '0') 
          LEFT JOIN branches b ON q.branch = b.id AND (b.storeid = '$storeid' OR b.storeid = '0') 
          WHERE (q.storeid = '$storeid' OR q.storeid = '0') AND q.state = '1' 
          ORDER BY q.id DESC";

$res = mysqli_query($adController->MySQL, $query);

$quotes = [];
$totalQuotesValue = 0.0;
$quotesCount = 0;
$thisMonthCount = 0;
$currentMonth = date('Y-m');

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $quotes[] = $row;
        $quotesCount++;
        $val = (float)($row['all_total'] ?? 0);
        $totalQuotesValue += $val;
        if (strpos($row['purchase_date'] ?? '', $currentMonth) === 0) {
            $thisMonthCount++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إدارة عروض الأسعار - ERP WAM Tech</title>
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
                            <i class="fas fa-file-contract text-primary me-2"></i>
                            <?=QUOTATIONS?> - عروض الأسعار للعملاء
                        </h1>
                        <div class="page-breadcrumb text-muted fs-7">
                            <a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-home"></i> الرئيسية</a>
                            <span class="mx-2">/</span>
                            <span>المبيعات</span>
                            <span class="mx-2">/</span>
                            <span class="text-primary fw-semibold">عروض الأسعار</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 btn-no-print">
                        <a href="quote_settings.php" class="btn btn-outline-secondary">
                            <i class="fas fa-cog me-1"></i> إعدادات الطباعة
                        </a>
                        <a href="add_qouta.php" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> إنشاء عرض سعر جديد
                        </a>
                    </div>
                </div>

                <!-- KPI Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-4 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">إجمالي عروض الأسعار المسجلة</div>
                                <div class="stat-value text-primary"><?=$quotesCount?> <span class="fs-7 fw-normal text-muted">عرض</span></div>
                                <div class="fs-8 text-muted mt-1">منذ بداية العمل بالنظام</div>
                            </div>
                            <div class="stat-icon bg-primary-subtle text-primary">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">إجمالي القيمة التقديرية للعروض</div>
                                <div class="stat-value text-success"><?=flout_format($totalQuotesValue)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1">فرص بيع محتملة للعملاء</div>
                            </div>
                            <div class="stat-icon bg-success-subtle text-success">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">عروض الشهر الجاري</div>
                                <div class="stat-value text-info"><?=$thisMonthCount?> <span class="fs-7 fw-normal text-muted">عرض هذا الشهر</span></div>
                                <div class="fs-8 text-muted mt-1"><?=date('F Y')?></div>
                            </div>
                            <div class="stat-icon bg-info-subtle text-info">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="table-pro-wrap">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="fs-5 fw-bold mb-0 text-dark">
                            <i class="fas fa-list text-primary me-2"></i>
                            قائمة عروض الأسعار الصادرة
                        </h2>
                        <span class="badge bg-light text-primary border px-3 py-2">عدد العروض: <?=$quotesCount?></span>
                    </div>

                    <div class="table-responsive">
                        <table id="quotesTable" class="table table-hover align-middle table-striped table-bordered text-center w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>رقم عرض السعر</th>
                                    <th>تاريخ العرض</th>
                                    <th>العميل</th>
                                    <th>الفرع</th>
                                    <th>المرجع / السند</th>
                                    <th>إجمالي العرض</th>
                                    <th class="btn-no-print">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $idx = 0;
                                foreach ($quotes as $data):
                                    $idx++;
                                    $quoteId     = (int)$data['id'];
                                    $invoice     = htmlspecialchars($data["invoicenumber"] ?? '');
                                    $customer    = ($language == 'ar' ? ($data['customer_name_ar'] ?? '') : ($data['customer_name_en'] ?? '')) ?: ($data['customer_name_ar'] ?? 'عميل غير محدد');
                                    $branch      = ($language == 'ar' ? ($data['branch_name_ar'] ?? '') : ($data['branch_name_en'] ?? '')) ?: ($data['branch_name_ar'] ?? 'الفرع الرئيسي');
                                    $suppInvoice = htmlspecialchars($data["supplier_invoice"] ?? '---');
                                    $date        = substr($data['purchase_date'] ?? '', 0, 10);
                                    $total       = (float)($data['all_total'] ?? 0);

                                    $invoiceTable = urlencode($adController->encrypt_decrypt(1, 'quote', 0));
                                    $invVal       = urlencode($adController->encrypt_decrypt(1, $data["invoicenumber"], 0));
                                ?>
                                <tr>
                                    <td><?=$idx?></td>
                                    <td>
                                        <a href="edit_qouta.php?sd=<?=$quoteId?>" class="fw-bold text-primary text-decoration-none">
                                            <?=$invoice?>
                                        </a>
                                    </td>
                                    <td class="text-muted fs-7"><?=$date?></td>
                                    <td class="fw-semibold text-dark"><?=$customer?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?=$branch?></span></td>
                                    <td><span class="text-muted fs-8"><?=$suppInvoice?></span></td>
                                    <td class="fw-bold text-success"><?=flout_format($total)?> <?=$currency?></td>
                                    <td class="btn-no-print">
                                        <div class="btn-group btn-group-sm">
                                            <a href="print_quote.php?id=<?=$quoteId?>" target="_blank" class="btn btn-outline-secondary" title="طباعة عرض السعر">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="add_saleInvoice.php?from_quote=<?=$quoteId?>" class="btn btn-outline-success" title="تحويل لفاتورة بيع مباشرة">
                                                <i class="fas fa-exchange-alt me-1"></i> تحويل لفاتورة
                                            </a>
                                            <a href="edit_qouta.php?sd=<?=$quoteId?>" class="btn btn-outline-primary" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDeleteQuote('<?=$invoiceTable?>', '<?=$invVal?>', '<?=$invoice?>')" title="حذف">
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
            $('#quotesTable').DataTable({
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

        function confirmDeleteQuote(vt, id, invNo) {
            if (confirm("هل أنت متأكد من حذف عرض السعر رقم (" + invNo + ")؟")) {
                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: {
                        'f': 'delete_invoice',
                        'vt': vt,
                        'id': id,
                        'ot': '',
                        'csrf_token': '<?=csrf_token()?>'
                    },
                    success: function(res) {
                        if (parseInt($.trim(res)) > 0) {
                            alert("تم حذف عرض السعر بنجاح.");
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
