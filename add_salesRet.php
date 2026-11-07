<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';

$selectedCustomer = (int)($_REQUEST['customer'] ?? 0);
$selectedSaleInv  = trim($_REQUEST['saleInvoice'] ?? '');

$saleHeader = null;
$saleItems  = [];

if (!empty($selectedSaleInv)) {
    $safeSaleInv = mysqli_real_escape_string($adController->MySQL, $selectedSaleInv);
    $qSale = "SELECT s.*, 
                     c.name_ar AS cust_name_ar, c.name_en AS cust_name_en,
                     b.name_ar AS branch_name_ar, b.name_en AS branch_name_en
              FROM sales s
              LEFT JOIN customers c ON s.customer = c.id
              LEFT JOIN branches b ON s.branch = b.id
              WHERE s.invoicenumber = '$safeSaleInv' AND (s.storeid='$storeid' OR s.storeid='0') AND s.state='1'
              LIMIT 1";
    $rSale = mysqli_query($adController->MySQL, $qSale);
    if ($rSale && $saleHeader = mysqli_fetch_assoc($rSale)) {
        $selectedCustomer = (int)$saleHeader['customer'];
        $saleBranchId     = (int)$saleHeader['branch'];

        // Generate return invoice number
        $newRetInv = $adController->getNewInvoiceNo("sales_ret", "invoicenumber", '12', $saleBranchId);

        // Fetch sold items
        $qItems = "SELECT si.*, i.name_ar, i.name_en, i.barcode, u.name_ar AS unit_ar, u.name_en AS unit_en, s.name_ar AS store_name_ar, s.name_en AS store_name_en
                   FROM sales_items si
                   JOIN items i ON si.itemid = i.id
                   LEFT JOIN units u ON i.unit = u.id
                   LEFT JOIN stores s ON si.store = s.id
                   WHERE si.invoice_No = '$safeSaleInv' AND (si.storeid='$storeid' OR si.storeid='0') AND si.state='1'";
        $rItems = mysqli_query($adController->MySQL, $qItems);
        if ($rItems) {
            while ($it = mysqli_fetch_assoc($rItems)) {
                $saleItems[] = [
                    'itemid' => (int)$it['itemid'],
                    'store'  => (int)$it['store'],
                    'store_name' => ($language == 'ar' ? $it['store_name_ar'] : $it['store_name_en']) ?: $it['store_name_ar'],
                    'name'   => ($language == 'ar' ? $it['name_ar'] : $it['name_en']) ?: $it['name_ar'],
                    'barcode' => !empty($it['barcode']) ? $it['barcode'] : '---',
                    'unit'   => ($language == 'ar' ? ($it['unit_ar'] ?? '') : ($it['unit_en'] ?? '')) ?: 'قطعة',
                    'qty'    => (float)$it['quantity'],
                    'price'  => (float)$it['cost'],
                    'disc'   => (float)$it['discount'],
                    'tax'    => (float)$it['tax']
                ];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إنشاء مرتجع مبيعات جديد - ERP WAM Tech</title>
    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        .step-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            border: 1px solid #eef2f6;
            padding: 24px;
            margin-bottom: 24px;
        }
        .form-section-title {
            font-size: 15px;
            font-weight: 700;
            color: #334155;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 16px;
        }
        .totals-summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
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
                            <i class="fas fa-undo text-danger me-2"></i>
                            إنشاء إشعار مرتجع مبيعات جديد
                        </h1>
                        <div class="page-breadcrumb text-muted fs-7">
                            <a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-home"></i> الرئيسية</a>
                            <span class="mx-2">/</span>
                            <a href="salesRet.php" class="text-decoration-none text-muted"><?=RETURNED_SALES?></a>
                            <span class="mx-2">/</span>
                            <span class="text-danger fw-semibold"><?=ADD_NEW?></span>
                        </div>
                    </div>
                    <div>
                        <a href="salesRet.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> العودة لقائمة المرتجعات
                        </a>
                    </div>
                </div>

                <!-- Step 1: Select Original Sale Invoice -->
                <div class="step-card">
                    <div class="form-section-title">
                        <i class="fas fa-search text-primary me-2"></i>الخطوة 1: اختيار فاتورة المبيعات المراد إرجاع أصناف منها
                    </div>

                    <form method="GET" action="add_salesRet.php" class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold fs-7 text-secondary">العميل</label>
                            <select name="customer" class="form-select" onchange="this.form.submit()">
                                <option value="">-- كافة العملاء --</option>
                                <?php
                                $qC = "SELECT id, name_ar, name_en, phone FROM customers WHERE (storeid='$storeid' OR storeid='0') ORDER BY name_ar ASC";
                                $rC = mysqli_query($adController->MySQL, $qC);
                                if ($rC) {
                                    while ($c = mysqli_fetch_assoc($rC)) {
                                        $cName = ($language == 'ar' ? $c['name_ar'] : $c['name_en']) ?: $c['name_ar'];
                                        $phone = !empty($c['phone']) ? " ({$c['phone']})" : "";
                                        $sel = ($selectedCustomer == $c['id']) ? 'selected' : '';
                                        echo "<option value='{$c['id']}' $sel>" . htmlspecialchars($cName . $phone) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold fs-7 text-secondary">رقم فاتورة المبيعات الأصلية <span class="text-danger">*</span></label>
                            <select name="saleInvoice" class="form-select" required onchange="this.form.submit()">
                                <option value="">-- اختر فاتورة المبيعات --</option>
                                <?php
                                $condCust = ($selectedCustomer > 0) ? " AND customer = '$selectedCustomer' " : "";
                                $qInvs = "SELECT invoicenumber, invoice_date, all_total FROM sales WHERE (storeid='$storeid' OR storeid='0') AND state='1' $condCust ORDER BY id DESC LIMIT 100";
                                $rInvs = mysqli_query($adController->MySQL, $qInvs);
                                if ($rInvs) {
                                    while ($invRow = mysqli_fetch_assoc($rInvs)) {
                                        $invN = $invRow['invoicenumber'];
                                        $invD = substr($invRow['invoice_date'] ?? '', 0, 10);
                                        $invT = flout_format($invRow['all_total'] ?? 0);
                                        $sel  = ($selectedSaleInv === $invN) ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($invN) . "' $sel>#{$invN} - بتاريخ ({$invD}) - إجمالي: {$invT} {$currency}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-file-import me-1"></i> تحميل الفاتورة
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 2: Return Details Form -->
                <?php if ($saleHeader): ?>
                <form id="saleRet-form" autocomplete="off">
                    <input type="hidden" name="f" value="addSaleRet">
                    <input type="hidden" name="csrf_token" value="<?=csrf_token()?>">
                    <input type="hidden" name="saleInvoice" value="<?=htmlspecialchars($selectedSaleInv)?>">
                    <input type="hidden" name="customer" value="<?=$selectedCustomer?>">
                    <input type="hidden" name="branch" value="<?=$saleBranchId?>">

                    <!-- Header Details Card -->
                    <div class="step-card">
                        <div class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-info-circle text-danger me-2"></i>بيانات إشعار المرتجع</span>
                            <span class="badge bg-danger-subtle text-danger">مرتجع لفاتورة بيع رقم: #<?=htmlspecialchars($selectedSaleInv)?></span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">رقم إشعار المرتجع</label>
                                <input type="text" class="form-control fw-bold text-danger bg-light" name="invoice" id="invoice" value="<?=htmlspecialchars($newRetInv)?>" readonly>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">تاريخ المرتجع <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="purchase_expiry" id="purchase_expiry" value="<?=date('Y-m-d')?>" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">العميل</label>
                                <input type="text" class="form-control bg-light fw-bold" value="<?=htmlspecialchars($saleHeader['cust_name_ar'] ?: 'عميل غير محدد')?>" readonly>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">الفرع</label>
                                <input type="text" class="form-control bg-light" value="<?=htmlspecialchars($saleHeader['branch_name_ar'] ?: 'الفرع الرئيسي')?>" readonly>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">رقم السند / المرجع</label>
                                <input type="text" class="form-control" name="document" value="<?=htmlspecialchars($saleHeader['document'] ?? '')?>" placeholder="المرجع">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-secondary">طريقة التسوية المالية <span class="text-danger">*</span></label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="payType" id="settle_cash" value="1" checked>
                                    <label class="btn btn-outline-success" for="settle_cash"><i class="fas fa-money-bill-wave me-1"></i> استرداد نقدي فوري للعميل (من الصندوق)</label>

                                    <input type="radio" class="btn-check" name="payType" id="settle_credit" value="0">
                                    <label class="btn btn-outline-info" for="settle_credit"><i class="fas fa-user-minus me-1"></i> تخفيض رصيد مديونية العميل (آجل)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table Card -->
                    <div class="step-card">
                        <div class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-boxes text-danger me-2"></i>الأصناف المبيعة في الفاتورة والكميات المراد إرجاعها</span>
                            <span class="badge bg-light text-secondary border">حدد الكمية المرتجعة لكل صنف (احذف الصنف الذي لا يرغب العميل بإرجاعه)</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-compact text-center align-middle" id="retItemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 28%;">الصنف / الباركود</th>
                                        <th style="width: 14%;">المخزن المستلم</th>
                                        <th style="width: 10%;">الكمية المبيعة</th>
                                        <th style="width: 10%;">الكمية المرتجعة <span class="text-danger">*</span></th>
                                        <th style="width: 10%;">سعر البيع</th>
                                        <th style="width: 8%;">الخصم %</th>
                                        <th style="width: 10%;">قبل الضريبة</th>
                                        <th style="width: 8%;">الضريبة 14%</th>
                                        <th style="width: 12%;">إجمالي المرتجع</th>
                                        <th style="width: 4%;">استبعاد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $rIdx = 0;
                                    foreach ($saleItems as $sItem):
                                        $rIdx++;
                                        $origQty  = $sItem['qty'];
                                        $price    = $sItem['price'];
                                        $disc     = $sItem['disc'];
                                        $tax      = $sItem['tax'];
                                        $subtotal = $origQty * $price * (1 - $disc / 100);
                                        $vatVal   = $subtotal * ($tax / 100);
                                        $netVal   = $subtotal + $vatVal;
                                    ?>
                                    <tr id="ret-row-<?=$rIdx?>" class="item-row">
                                        <td class="text-start">
                                            <input type="hidden" name="barcode[]" value="<?=$sItem['itemid']?>">
                                            <div class="fw-bold text-dark"><?=htmlspecialchars($sItem['name'])?></div>
                                            <div class="fs-8 text-muted">باركود: <code><?=htmlspecialchars($sItem['barcode'])?></code> | وحدة: <?=htmlspecialchars($sItem['unit'])?></div>
                                        </td>
                                        <td>
                                            <input type="hidden" name="store[]" value="<?=$sItem['store']?>">
                                            <span class="badge bg-secondary-subtle text-secondary"><?=htmlspecialchars($sItem['store_name'])?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border fw-bold"><?=flout_format($origQty)?></span>
                                            <input type="hidden" id="orig-qty-<?=$rIdx?>" value="<?=$origQty?>">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0.01" max="<?=$origQty?>" class="form-control form-control-sm text-center fw-bold text-danger" name="item_qty[]" id="qty-<?=$rIdx?>" value="<?=$origQty?>" required oninput="calculateRetRow(<?=$rIdx?>)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm text-center bg-light" name="item_price[]" id="price-<?=$rIdx?>" value="<?=$price?>" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm text-center bg-light" name="item_disc[]" id="disc-<?=$rIdx?>" value="<?=$disc?>" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-center bg-light" name="item_total_after_dis[]" id="subtotal-<?=$rIdx?>" value="<?=flout_format($subtotal)?>" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm text-center bg-light" name="item_vat[]" id="vat-<?=$rIdx?>" value="<?=$tax?>" readonly>
                                            <input type="hidden" id="vatval-<?=$rIdx?>" value="<?=$vatVal?>">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-center fw-bold text-danger bg-light" id="net-<?=$rIdx?>" value="<?=flout_format($netVal)?>" readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-outline-danger btn-sm p-1" onclick="removeRetRow(<?=$rIdx?>)" title="استبعاد هذا الصنف من المرتجع">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Financial Totals Card -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6 offset-lg-6">
                            <div class="totals-summary-card">
                                <div class="form-section-title">
                                    <i class="fas fa-calculator text-danger me-2"></i>إجمالي المبالغ المستردة
                                </div>

                                <table class="table table-borderless align-middle mb-0">
                                    <tr>
                                        <td class="text-secondary">الإجمالي قبل الضريبة:</td>
                                        <td class="text-end fw-bold" id="gross_display">0.00 <?=$currency?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary">إجمالي الخصم المسترجع:</td>
                                        <td class="text-end fw-bold text-info" id="disc_display">0.00 <?=$currency?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary">ضريبة القيمة المضافة 14%:</td>
                                        <td class="text-end fw-bold text-danger" id="vat_display">0.00 <?=$currency?></td>
                                    </tr>
                                    <tr class="border-top border-2">
                                        <td class="fs-5 fw-bold text-dark pt-3">الصافي الإجمالي للمرتجع:</td>
                                        <td class="fs-4 fw-bold text-danger text-end pt-3" id="total_display">0.00 <?=$currency?></td>
                                    </tr>
                                </table>

                                <!-- Hidden Inputs for Backend -->
                                <input type="hidden" name="gross_total" id="gross_total" value="0.00">
                                <input type="hidden" name="final_discount" id="final_discount" value="0.00">
                                <input type="hidden" name="vat_all" id="vat_all" value="0.00">
                                <input type="hidden" name="totalAll" id="totalAll" value="0.00">

                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-danger btn-lg fw-bold" id="btnSubmit">
                                        <i class="fas fa-check-circle me-1"></i> حفظ وترحيل مرتجع المبيعات
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <?php require_once("footer.php"); ?>

    <script>
        function calculateRetRow(idx) {
            const qtyInput = document.getElementById(`qty-${idx}`);
            const origQty  = parseFloat(document.getElementById(`orig-qty-${idx}`).value) || 0;
            let qty = parseFloat(qtyInput.value) || 0;

            if (qty > origQty) {
                alert(`تنبيه: لا يمكن إرجاع كمية (${qty}) أكبر من الكمية المبيعة بالفاتورة (${origQty})!`);
                qty = origQty;
                qtyInput.value = origQty;
            }

            const price = parseFloat(document.getElementById(`price-${idx}`).value) || 0;
            const disc  = parseFloat(document.getElementById(`disc-${idx}`).value) || 0;
            const vat   = parseFloat(document.getElementById(`vat-${idx}`).value) || 0;

            const gross = qty * price;
            const discAmount = gross * (disc / 100);
            const subtotal = Math.max(0, gross - discAmount);
            const vatAmount = subtotal * (vat / 100);
            const net = subtotal + vatAmount;

            document.getElementById(`subtotal-${idx}`).value = subtotal.toFixed(2);
            document.getElementById(`vatval-${idx}`).value   = vatAmount.toFixed(2);
            document.getElementById(`net-${idx}`).value      = net.toFixed(2);

            calculateRetTotals();
        }

        function removeRetRow(idx) {
            const tr = document.getElementById(`ret-row-${idx}`);
            if (tr) {
                tr.remove();
                calculateRetTotals();
            }
        }

        function calculateRetTotals() {
            let totalGross = 0;
            let totalDisc  = 0;
            let totalVat   = 0;
            let grandTotal = 0;

            const rows = document.querySelectorAll('#retItemsTable tbody tr');
            rows.forEach(row => {
                const idMatch = row.id.match(/ret-row-(\d+)/);
                if (idMatch) {
                    const idx = idMatch[1];
                    const qty      = parseFloat(document.getElementById(`qty-${idx}`)?.value) || 0;
                    const price    = parseFloat(document.getElementById(`price-${idx}`)?.value) || 0;
                    const disc     = parseFloat(document.getElementById(`disc-${idx}`)?.value) || 0;
                    const vatVal   = parseFloat(document.getElementById(`vatval-${idx}`)?.value) || 0;
                    const subtotal = parseFloat(document.getElementById(`subtotal-${idx}`)?.value) || 0;

                    const rowGross = qty * price;
                    const rowDisc  = rowGross * (disc / 100);

                    totalGross += rowGross;
                    totalDisc  += rowDisc;
                    totalVat   += vatVal;
                    grandTotal += (subtotal + vatVal);
                }
            });

            document.getElementById('gross_display').innerText = totalGross.toFixed(2) + ' <?=$currency?>';
            document.getElementById('disc_display').innerText  = totalDisc.toFixed(2) + ' <?=$currency?>';
            document.getElementById('vat_display').innerText   = totalVat.toFixed(2) + ' <?=$currency?>';
            document.getElementById('total_display').innerText = grandTotal.toFixed(2) + ' <?=$currency?>';

            document.getElementById('gross_total').value    = totalGross.toFixed(2);
            document.getElementById('final_discount').value = totalDisc.toFixed(2);
            document.getElementById('vat_all').value        = totalVat.toFixed(2);
            document.getElementById('totalAll').value       = grandTotal.toFixed(2);
        }

        // Form Submit
        const retForm = document.getElementById('saleRet-form');
        if (retForm) {
            retForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const rows = document.querySelectorAll('#retItemsTable tbody tr');
                if (rows.length === 0) {
                    alert('يجب تحديد صنف واحد على الأقل لإرجاعه.');
                    return;
                }

                const btn = document.getElementById('btnSubmit');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري حفظ المرتجع...';

                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res && res.success == 1) {
                            const inv = res.invoice;
                            if (confirm('تم حفظ إشعار مرتجع المبيعات رقم (' + inv + ') بنجاح!\n\nهل ترغب في طباعة إشعار المرتجع الآن؟')) {
                                window.open('reports/reSales.php?sd=' + encodeURIComponent(inv), '_blank');
                            }
                            window.location.href = 'salesRet.php';
                        } else {
                            alert('تنبيه: ' + (res.error || 'فشل حفظ المرتجع، يرجى مراجعة البيانات.'));
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> حفظ وترحيل مرتجع المبيعات';
                        }
                    },
                    error: function(xhr) {
                        alert('حدث خطأ في الاتصال بالسيرفر: ' + xhr.statusText);
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> حفظ وترحيل مرتجع المبيعات';
                    }
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                calculateRetTotals();
            });
        }
    </script>
</body>
</html>
