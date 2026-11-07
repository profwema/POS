<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';
$pgName   = basename(__FILE__);

// Filters
$fdate      = trim($_GET['fr_date'] ?? '');
$tdate      = trim($_GET['to_date'] ?? '');
$customerId = (int)($_GET['customer'] ?? 0);
$branchId   = (int)($_GET['branch'] ?? 0);
$payTypeF   = trim($_GET['pay_type'] ?? 'all');

$conditions = ["s.storeid = '$storeid'", "s.state = '1'"];

if ($fdate !== '') {
    $safeF = mysqli_real_escape_string($adController->MySQL, $fdate);
    $conditions[] = "s.invoice_date >= '$safeF 00:00:00'";
}
if ($tdate !== '') {
    $safeT = mysqli_real_escape_string($adController->MySQL, $tdate);
    $conditions[] = "s.invoice_date <= '$safeT 23:59:59'";
}
if ($customerId > 0) {
    $conditions[] = "s.customer = '$customerId'";
}
if ($branchId > 0) {
    $conditions[] = "s.branch = '$branchId'";
}
if ($payTypeF === 'cash') {
    $conditions[] = "s.paymentType = '1'";
} elseif ($payTypeF === 'credit') {
    $conditions[] = "s.paymentType != '1'";
}

$whereClause = "WHERE " . implode(" AND ", $conditions);

// Fetch sales list
$query = "SELECT 
            s.*, 
            c.name_ar AS customer_name_ar, 
            c.name_en AS customer_name_en, 
            b.name_ar AS branch_name_ar,
            b.name_en AS branch_name_en
          FROM sales s 
          LEFT JOIN customers c ON s.customer = c.id AND (c.storeid = '$storeid' OR c.storeid = '0') 
          LEFT JOIN branches b ON s.branch = b.id AND (b.storeid = '$storeid' OR b.storeid = '0') 
          $whereClause 
          ORDER BY s.id DESC";

$res = mysqli_query($adController->MySQL, $query);

$sales = [];
$totalSales = 0.0;
$totalCash = 0.0;
$totalCredit = 0.0;
$totalVat = 0.0;
$invoicesCount = 0;

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $sales[] = $row;
        $allTot = (float)($row['all_total'] ?? 0);
        $vatTot = (float)($row['vat_all'] ?? 0);
        $paid   = (float)($row['cash'] ?? 0) + (float)($row['visa'] ?? 0) + (float)($row['mada'] ?? 0);
        $isCash = ((int)($row['paymentType'] ?? 0) === 1 || (float)($row['left'] ?? 0) <= 0);

        $totalSales += $allTot;
        $totalVat   += $vatTot;
        if ($isCash) {
            $totalCash += $allTot;
        } else {
            $totalCash   += $paid;
            $totalCredit += (float)($row['left'] ?? ($allTot - $paid));
        }
        $invoicesCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إدارة فواتير المبيعات - ERP WAM Tech</title>
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
                            <i class="fas fa-file-invoice text-success me-2"></i>
                            <?=SALES?> - فواتير المبيعات
                        </h1>
                        <div class="page-breadcrumb text-muted fs-7">
                            <a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-home"></i> الرئيسية</a>
                            <span class="mx-2">/</span>
                            <span>المبيعات</span>
                            <span class="mx-2">/</span>
                            <span class="text-primary fw-semibold">سجل فواتير البيع</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 btn-no-print">
                        <a href="pos.php" class="btn btn-outline-primary">
                            <i class="fas fa-cash-register me-1"></i> نقطة البيع السريعة POS
                        </a>
                        <a href="add_saleInvoice.php" class="btn btn-success">
                            <i class="fas fa-plus-circle me-1"></i> إضافة فاتورة بيع جديدة
                        </a>
                    </div>
                </div>

                <!-- KPI Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">إجمالي المبيعات</div>
                                <div class="stat-value text-primary"><?=flout_format($totalSales)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1"><?=$invoicesCount?> فاتورة بيع</div>
                            </div>
                            <div class="stat-icon bg-primary-subtle text-primary">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">المحصل (نقداً وإلكترونياً)</div>
                                <div class="stat-value text-success"><?=flout_format($totalCash)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1">مدفوعات مستلمة بالخزينة</div>
                            </div>
                            <div class="stat-icon bg-success-subtle text-success">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">المتبقي / الآجل على العملاء</div>
                                <div class="stat-value text-danger"><?=flout_format($totalCredit)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1">مديونيات مستحقة للتحصيل</div>
                            </div>
                            <div class="stat-icon bg-danger-subtle text-danger">
                                <i class="fas fa-user-clock"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">ضريبة القيمة المضافة 14%</div>
                                <div class="stat-value text-warning"><?=flout_format($totalVat)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1">مستحقة للإقرار الضريبي</div>
                            </div>
                            <div class="stat-icon bg-warning-subtle text-warning">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Box -->
                <div class="filter-card btn-no-print">
                    <form method="GET" action="<?=htmlspecialchars($pgName)?>" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">العميل</label>
                            <select name="customer" class="form-select">
                                <option value="">-- كافة العملاء --</option>
                                <?php
                                $qC = "SELECT id, name_ar, name_en FROM customers WHERE storeid='$storeid' ORDER BY name_ar ASC";
                                $rC = mysqli_query($adController->MySQL, $qC);
                                if ($rC) {
                                    while ($c = mysqli_fetch_assoc($rC)) {
                                        $cName = ($language == 'ar' ? $c['name_ar'] : $c['name_en']) ?: $c['name_ar'];
                                        $sel = ($customerId == $c['id']) ? 'selected' : '';
                                        echo "<option value='{$c['id']}' $sel>" . htmlspecialchars($cName) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold fs-7 text-secondary">الفرع</label>
                            <select name="branch" class="form-select">
                                <option value="">-- كافة الفروع --</option>
                                <?php
                                $qB = "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' ORDER BY name_ar ASC";
                                $rB = mysqli_query($adController->MySQL, $qB);
                                if ($rB) {
                                    while ($b = mysqli_fetch_assoc($rB)) {
                                        $bName = ($language == 'ar' ? $b['name_ar'] : $b['name_en']) ?: $b['name_ar'];
                                        $sel = ($branchId == $b['id']) ? 'selected' : '';
                                        echo "<option value='{$b['id']}' $sel>" . htmlspecialchars($bName) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold fs-7 text-secondary">طريقة الدفع</label>
                            <select name="pay_type" class="form-select">
                                <option value="all" <?=$payTypeF === 'all' ? 'selected' : ''?>>الكل</option>
                                <option value="cash" <?=$payTypeF === 'cash' ? 'selected' : ''?>>نقدي</option>
                                <option value="credit" <?=$payTypeF === 'credit' ? 'selected' : ''?>>آجل</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold fs-7 text-secondary">من تاريخ</label>
                            <input type="date" name="fr_date" class="form-control" value="<?=htmlspecialchars($fdate)?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold fs-7 text-secondary">إلى تاريخ</label>
                            <input type="date" name="to_date" class="form-control" value="<?=htmlspecialchars($tdate)?>">
                        </div>
                        <div class="col-md-1 d-flex gap-1">
                            <button type="submit" class="btn btn-primary w-100" title="تصفية">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="<?=htmlspecialchars($pgName)?>" class="btn btn-light" title="إلغاء التصفية">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Table Card -->
                <div class="table-pro-wrap">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="fs-5 fw-bold mb-0 text-dark">
                            <i class="fas fa-list-alt text-primary me-2"></i>
                            قائمة فواتير البيع المسجلة
                        </h2>
                        <span class="badge bg-light text-primary border px-3 py-2">عدد السجلات: <?=$invoicesCount?></span>
                    </div>

                    <div class="table-responsive">
                        <table id="salesTable" class="table table-hover align-middle table-striped table-bordered text-center w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>رقم الفاتورة</th>
                                    <th>التاريخ</th>
                                    <th>العميل</th>
                                    <th>الفرع</th>
                                    <th>الإجمالي قبل الضريبة</th>
                                    <th>ضريبة 14%</th>
                                    <th>الخصم</th>
                                    <th>إجمالي الفاتورة</th>
                                    <th>طريقة السداد</th>
                                    <th>المحصل</th>
                                    <th>المتبقي</th>
                                    <th class="btn-no-print">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $idx = 0;
                                foreach ($sales as $data):
                                    $idx++;
                                    $invoice      = htmlspecialchars($data["invoicenumber"] ?? '');
                                    $customer     = ($language == 'ar' ? ($data['customer_name_ar'] ?? '') : ($data['customer_name_en'] ?? '')) ?: ($data['customer_name_ar'] ?? 'عميل نقدي');
                                    $branch       = ($language == 'ar' ? ($data['branch_name_ar'] ?? '') : ($data['branch_name_en'] ?? '')) ?: ($data['branch_name_ar'] ?? 'الفرع الرئيسي');
                                    $date         = substr($data['invoice_date'] ?? '', 0, 16);
                                    $gross        = (float)($data['gross_total'] ?? 0);
                                    $vatAll       = (float)($data['vat_all'] ?? 0);
                                    $disAdd       = (float)($data['dis_added'] ?? 0);
                                    $allTotal     = (float)($data['all_total'] ?? 0);
                                    $isCash       = ((int)($data['paymentType'] ?? 0) === 1 || (float)($data['left'] ?? 0) <= 0);
                                    $paid         = $isCash ? $allTotal : ((float)($data['cash'] ?? 0) + (float)($data['visa'] ?? 0) + (float)($data['mada'] ?? 0));
                                    $left         = (float)($data['left'] ?? ($allTotal - $paid));

                                    $idval        = urlencode($adController->encrypt_decrypt(1, $data["invoicenumber"], 0));
                                    $invoiceTable = urlencode($adController->encrypt_decrypt(1, 'sales', 0));
                                    $otherTable   = urlencode($adController->encrypt_decrypt(1, 'outgo', 0));
                                ?>
                                <tr>
                                    <td><?=$idx?></td>
                                    <td>
                                        <a href="edit_saleInvoice.php?sd=<?=$idval?>" class="fw-bold text-primary text-decoration-none">
                                            <?=$invoice?>
                                        </a>
                                    </td>
                                    <td class="text-muted fs-7"><?=$date?></td>
                                    <td class="fw-semibold text-dark"><?=$customer?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?=$branch?></span></td>
                                    <td><?=flout_format($gross)?></td>
                                    <td class="text-danger"><?=flout_format($vatAll)?></td>
                                    <td class="text-info"><?=flout_format($disAdd)?></td>
                                    <td class="fw-bold text-dark"><?=flout_format($allTotal)?></td>
                                    <td>
                                        <?php if ($isCash): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">نقدي</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">آجل</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-success"><?=flout_format($paid)?></td>
                                    <td class="<?=$left > 0 ? 'text-danger fw-bold' : 'text-muted'?>"><?=flout_format($left)?></td>
                                    <td class="btn-no-print">
                                        <div class="btn-group btn-group-sm">
                                            <a href="reports/sales.php?sd=<?=urlencode($invoice)?>" target="_blank" class="btn btn-outline-secondary" title="طباعة فاتورة A4 (PDF)">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="edit_saleInvoice.php?sd=<?=$idval?>" class="btn btn-outline-primary" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDeleteSale('<?=$invoiceTable?>', '<?=$idval?>', '<?=$otherTable?>', '<?=$invoice?>')" title="حذف">
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
            $('#salesTable').DataTable({
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

        function confirmDeleteSale(vt, id, ot, invNo) {
            if (confirm("هل أنت متأكد من حذف فاتورة المبيعات رقم (" + invNo + ")؟\nسيتم إرجاع كميات الأصناف للمخازن وعكس القيود المحاسبية.")) {
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
                            alert("تم حذف الفاتورة بنجاح.");
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
