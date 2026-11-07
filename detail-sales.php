<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid  = (int)($_SESSION['storeid'] ?? 1);
$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';
$pgName   = basename(__FILE__);

$orderid  = trim($_REQUEST['o'] ?? '');
$catid    = (int)($_REQUEST['catid'] ?? 0);
$itemId   = (int)($_REQUEST['iid'] ?? 0);
$branchid = (int)($_REQUEST['br'] ?? 0);
$empId    = (int)($_REQUEST['ei'] ?? 0);

$fDate = trim($_REQUEST['from_date'] ?? date('Y-m-d'));
$tDate = trim($_REQUEST['to_date'] ?? date('Y-m-d'));

$fromDateTime = date('Y-m-d 00:00:00', strtotime($fDate));
$toDateTime   = date('Y-m-d 23:59:59', strtotime($tDate));

$conditionArr = ["(o.storeid = '$storeid' OR o.storeid = '0')"];
$conditionArr[] = "o.specific_date >= '$fromDateTime'";
$conditionArr[] = "o.specific_date <= '$toDateTime'";

if ($orderid !== '') {
    $escOrder = mysqli_real_escape_string($adController->MySQL, $orderid);
    $conditionArr[] = "(o.hasvalue = '$escOrder' OR o.invoice_number = '$escOrder')";
}
if ($itemId > 0) {
    $conditionArr[] = "o.itemid = '$itemId'";
}
if ($catid > 0) {
    $conditionArr[] = "i.catid = '$catid'";
}
if ($branchid > 0) {
    $conditionArr[] = "o.branch = '$branchid'";
}
if ($empId > 0) {
    $conditionArr[] = "o.userid = '$empId'";
}

$whereClause = implode(" AND ", $conditionArr);

$query = "SELECT 
            o.id,
            o.dated,
            o.specific_date,
            o.hasvalue,
            o.invoice_number,
            o.qty,
            o.cost,
            o.discount,
            o.vat,
            o.payment_type,
            b.name_ar AS branch_ar,
            b.name_en AS branch_en,
            i.name_ar AS item_ar,
            i.name_en AS item_en,
            c.name_ar AS cat_ar,
            c.name_en AS cat_en,
            e.name_ar AS emp_ar,
            e.name_en AS emp_en
          FROM orders o
          LEFT JOIN branches b ON b.id = o.branch AND (b.storeid = '$storeid' OR b.storeid = '0')
          LEFT JOIN items i ON i.id = o.itemid AND (i.storeid = '$storeid' OR i.storeid = '0')
          LEFT JOIN categories c ON c.id = i.catid AND (c.storeid = '$storeid' OR c.storeid = '0')
          LEFT JOIN employees e ON e.id = o.userid AND (e.storeid = '$storeid' OR e.storeid = '0')
          WHERE $whereClause
          ORDER BY o.specific_date DESC, o.id DESC
          LIMIT 2500";

$res = mysqli_query($adController->MySQL, $query);

$orderRows    = [];
$finalTotal   = 0.0;
$cashTotal    = 0.0;
$cardTotal    = 0.0;
$madaTotal    = 0.0;
$totalQty     = 0.0;

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $orderRows[] = $row;
        $qty       = (float)($row['qty'] ?? 0);
        $cost      = (float)($row['cost'] ?? 0);
        $discount  = (float)($row['discount'] ?? 0);
        $vatRate   = (float)($row['vat'] ?? 0);
        
        $subtotal  = ($qty * $cost) - $discount;
        $vatAmount = ($vatRate > 0) ? (($subtotal / 100) * $vatRate) : 0;
        $lineTotal = $subtotal + $vatAmount;

        $finalTotal += $lineTotal;
        $totalQty   += $qty;

        $pType = (string)($row['payment_type'] ?? '');
        if ($pType === '1') {
            $cashTotal += $lineTotal;
        } elseif ($pType === '2') {
            $cardTotal += $lineTotal;
        } elseif ($pType === '3') {
            $madaTotal += $lineTotal;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تقرير تفاصيل المبيعات اليومية - ERP WAM Tech</title>
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
                
                <!-- Page Breadcrumbs -->
                <div class="page-header d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="page-title fs-4 fw-bold mb-1">
                            <i class="fas fa-receipt text-primary me-2"></i>
                            تقرير تفاصيل حركة المبيعات اليومية ونقاط البيع
                        </h1>
                        <div class="page-breadcrumb text-muted fs-7">
                            <a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-home"></i> الرئيسية</a>
                            <span class="mx-2">/</span>
                            <a href="saleInvoice.php" class="text-decoration-none text-muted">المبيعات</a>
                            <span class="mx-2">/</span>
                            <span class="text-primary fw-semibold">تفاصيل المبيعات اليومية</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 btn-no-print">
                        <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="fas fa-print me-1"></i> طباعة فورية
                        </button>
                    </div>
                </div>

                <!-- Filter Card -->
                <div class="filter-card btn-no-print">
                    <form method="GET" action="<?=htmlspecialchars($pgName)?>" class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold fs-7 text-secondary">رقم الطلب / الفاتورة</label>
                            <input type="text" name="o" class="form-control" value="<?=htmlspecialchars($orderid)?>" placeholder="رقم الطلب">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold fs-7 text-secondary">القسم / التصنيف</label>
                            <select name="catid" class="form-select">
                                <option value="">-- كافة الأقسام --</option>
                                <?php
                                $qCat = "SELECT id, name_ar, name_en FROM categories WHERE (storeid='$storeid' OR storeid='0') ORDER BY name_ar ASC";
                                $rCat = mysqli_query($adController->MySQL, $qCat);
                                if ($rCat) {
                                    while ($c = mysqli_fetch_assoc($rCat)) {
                                        $cName = ($language == 'ar' ? $c['name_ar'] : $c['name_en']) ?: $c['name_ar'];
                                        $sel = ($catid == $c['id']) ? 'selected' : '';
                                        echo "<option value='{$c['id']}' $sel>" . htmlspecialchars($cName) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold fs-7 text-secondary">الفرع</label>
                            <select name="br" class="form-select">
                                <option value="">-- كافة الفروع --</option>
                                <?php
                                $qB = "SELECT id, name_ar, name_en FROM branches WHERE (storeid='$storeid' OR storeid='0') ORDER BY name_ar ASC";
                                $rB = mysqli_query($adController->MySQL, $qB);
                                if ($rB) {
                                    while ($b = mysqli_fetch_assoc($rB)) {
                                        $bName = ($language == 'ar' ? $b['name_ar'] : $b['name_en']) ?: $b['name_ar'];
                                        $sel = ($branchid == $b['id']) ? 'selected' : '';
                                        echo "<option value='{$b['id']}' $sel>" . htmlspecialchars($bName) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold fs-7 text-secondary">الكاشير / الموظف</label>
                            <select name="ei" class="form-select">
                                <option value="">-- كافة الموظفين --</option>
                                <?php
                                $qEmp = "SELECT id, name_ar, name_en FROM employees WHERE (storeid='$storeid' OR storeid='0') ORDER BY name_ar ASC";
                                $rEmp = mysqli_query($adController->MySQL, $qEmp);
                                if ($rEmp) {
                                    while ($e = mysqli_fetch_assoc($rEmp)) {
                                        $eName = ($language == 'ar' ? $e['name_ar'] : $e['name_en']) ?: $e['name_ar'];
                                        $sel = ($empId == $e['id']) ? 'selected' : '';
                                        echo "<option value='{$e['id']}' $sel>" . htmlspecialchars($eName) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold fs-7 text-secondary">من تاريخ</label>
                            <input type="date" name="from_date" class="form-control" value="<?=htmlspecialchars($fDate)?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold fs-7 text-secondary">إلى تاريخ</label>
                            <input type="date" name="to_date" class="form-control" value="<?=htmlspecialchars($tDate)?>">
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-filter me-1"></i> تصفية النتائج
                            </button>
                            <a href="<?=htmlspecialchars($pgName)?>" class="btn btn-light px-3" title="إعادة ضبط">
                                <i class="fas fa-sync-alt me-1"></i> إعادة ضبط
                            </a>
                        </div>
                    </form>
                </div>

                <!-- KPI Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">إجمالي إيراد المبيعات</div>
                                <div class="stat-value text-primary"><?=flout_format($finalTotal)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1"><?=count($orderRows)?> بند مبيعات منفذ</div>
                            </div>
                            <div class="stat-icon bg-primary-subtle text-primary">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">التحصيل النقدي (كاش)</div>
                                <div class="stat-value text-success"><?=flout_format($cashTotal)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1">بالخزينة الرئيسية</div>
                            </div>
                            <div class="stat-icon bg-success-subtle text-success">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">التحصيل الإلكتروني (شبكة/فيزا)</div>
                                <div class="stat-value text-info"><?=flout_format($cardTotal + $madaTotal)?> <span class="fs-7 fw-normal text-muted"><?=$currency?></span></div>
                                <div class="fs-8 text-muted mt-1">مدفوعات نقاط البيع</div>
                            </div>
                            <div class="stat-icon bg-info-subtle text-info">
                                <i class="fas fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card-clean">
                            <div>
                                <div class="stat-label">إجمالي الكميات المباعة</div>
                                <div class="stat-value text-warning"><?=flout_format($totalQty)?> <span class="fs-7 fw-normal text-muted">قطعة</span></div>
                                <div class="fs-8 text-muted mt-1">حجم المبيعات الفعلي</div>
                            </div>
                            <div class="stat-icon bg-warning-subtle text-warning">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="table-pro-wrap">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="fs-5 fw-bold mb-0 text-dark">
                            <i class="fas fa-list-alt text-primary me-2"></i>
                            سجل العمليات اليومية المنفذة
                        </h2>
                        <span class="badge bg-light text-primary border px-3 py-2">عدد العمليات: <?=count($orderRows)?></span>
                    </div>

                    <div class="table-responsive">
                        <table id="detailSalesTable" class="table table-hover align-middle table-striped table-bordered text-center w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الوقت والتاريخ</th>
                                    <th>الفرع</th>
                                    <th>رقم الطلب / الفاتورة</th>
                                    <th>القسم</th>
                                    <th>الصنف المباع</th>
                                    <th>الكمية</th>
                                    <th>السعر</th>
                                    <th>الخصم</th>
                                    <th>الضريبة 14%</th>
                                    <th>الإجمالي الصافي</th>
                                    <th>طريقة الدفع</th>
                                    <th>الكاشير</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $dIdx = 0;
                                foreach ($orderRows as $row):
                                    $dIdx++;
                                    $qty       = (float)($row['qty'] ?? 0);
                                    $cost      = (float)($row['cost'] ?? 0);
                                    $discount  = (float)($row['discount'] ?? 0);
                                    $vatRate   = (float)($row['vat'] ?? 0);
                                    $subtotal  = ($qty * $cost) - $discount;
                                    $vatAmount = ($vatRate > 0) ? (($subtotal / 100) * $vatRate) : 0;
                                    $lineTotal = $subtotal + $vatAmount;

                                    $branchName = ($language == 'ar' ? ($row['branch_ar'] ?? '') : ($row['branch_en'] ?? '')) ?: ($row['branch_ar'] ?? '---');
                                    $itemName   = ($language == 'ar' ? ($row['item_ar'] ?? '') : ($row['item_en'] ?? '')) ?: ($row['item_ar'] ?? '---');
                                    $catName    = ($language == 'ar' ? ($row['cat_ar'] ?? '') : ($row['cat_en'] ?? '')) ?: ($row['cat_ar'] ?? '---');
                                    $empName    = ($language == 'ar' ? ($row['emp_ar'] ?? '') : ($row['emp_en'] ?? '')) ?: ($row['emp_ar'] ?? '---');
                                    $orderDisplay = $row['hasvalue'] ?: ($row['invoice_number'] ?: $row['id']);
                                    $dateDisplay  = !empty($row['specific_date']) ? date("Y-m-d H:i", strtotime($row['specific_date'])) : date("Y-m-d H:i", strtotime($row['dated'] ?? ''));

                                    $pType = (string)($row['payment_type'] ?? '');
                                    $pBadge = '<span class="badge bg-secondary-subtle text-secondary">أخرى</span>';
                                    if ($pType === '1') {
                                        $pBadge = '<span class="badge bg-success-subtle text-success">نقدي</span>';
                                    } elseif ($pType === '2') {
                                        $pBadge = '<span class="badge bg-primary-subtle text-primary">فيزا / ماستر</span>';
                                    } elseif ($pType === '3') {
                                        $pBadge = '<span class="badge bg-info-subtle text-info">مدى / إلكتروني</span>';
                                    }
                                ?>
                                <tr>
                                    <td><?=$dIdx?></td>
                                    <td class="text-muted fs-8"><?=$dateDisplay?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?=$branchName?></span></td>
                                    <td class="fw-bold text-primary">#<?=$orderDisplay?></td>
                                    <td><?=$catName?></td>
                                    <td class="fw-semibold text-dark text-start"><?=$itemName?></td>
                                    <td class="fw-bold text-dark"><?=flout_format($qty)?></td>
                                    <td><?=flout_format($cost)?></td>
                                    <td class="text-info"><?=flout_format($discount)?></td>
                                    <td class="text-danger"><?=flout_format($vatAmount)?></td>
                                    <td class="fw-bold text-success"><?=flout_format($lineTotal)?></td>
                                    <td><?=$pBadge?></td>
                                    <td class="text-muted fs-8"><?=$empName?></td>
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
            $('#detailSalesTable').DataTable({
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
    </script>
</body>
</html>
