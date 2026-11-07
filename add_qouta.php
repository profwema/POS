<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';

// Determine default branch and new quotation number
$defBranch = 1;
$resBr = mysqli_query($adController->MySQL, "SELECT id FROM branches WHERE (storeid='$storeid' OR storeid='0') AND status='1' LIMIT 1");
if ($resBr && $brRow = mysqli_fetch_assoc($resBr)) {
    $defBranch = (int)$brRow['id'];
}
$invoice = $adController->getNewInvoiceNo("quote", "invoicenumber", '44', $defBranch);

// Pre-fetch items catalog
$queryItemsFast = "SELECT i.id, i.barcode, i.name_ar, i.name_en, i.item_type, i.vat, i.price, i.discount, u.name_ar AS unit_ar, u.name_en AS unit_en 
                   FROM items i 
                   LEFT JOIN units u ON i.unit = u.id 
                   WHERE (i.storeid = '$storeid' OR i.storeid = '0') AND i.enabled = '1'
                   ORDER BY i.name_ar ASC";
$resItemsFast = mysqli_query($adController->MySQL, $queryItemsFast);
$itemsCache = [];
if ($resItemsFast) {
    while ($row = mysqli_fetch_assoc($resItemsFast)) {
        $bcode = !empty($row['barcode']) ? $row['barcode'] : "-----";
        $iName = !empty($row['name_ar']) ? $row['name_ar'] : (!empty($row['name_en']) ? $row['name_en'] : '---');
        $uName = !empty($row['unit_ar']) ? $row['unit_ar'] : (!empty($row['unit_en']) ? $row['unit_en'] : "قطعة");
        $itemsCache[] = [
            'id' => (int)$row['id'],
            'barcode' => $bcode,
            'name' => $iName,
            'unit' => $uName,
            'type' => (int)($row['item_type'] ?? 0),
            'vat' => (float)($row['vat'] ?? 14),
            'price' => (float)($row['price'] ?? 0),
            'disc' => (float)($row['discount'] ?? 0)
        ];
    }
}

// Pre-fetch stores
$storesCache = [];
$resStore = mysqli_query($adController->MySQL, "SELECT id, name_ar, name_en, branch FROM stores WHERE storeid='$storeid' OR storeid='0'");
if ($resStore) {
    while ($sRow = mysqli_fetch_assoc($resStore)) {
        $sName = !empty($sRow['name_ar']) ? $sRow['name_ar'] : $sRow['name_en'];
        $storesCache[] = [
            'id' => (int)$sRow['id'],
            'name' => $sName,
            'branch' => (int)$sRow['branch']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إنشاء عرض سعر جديد - ERP WAM Tech</title>
    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        .item-row:hover {
            background-color: rgba(59, 130, 246, 0.03);
        }
        .table-compact th, .table-compact td {
            padding: 8px 6px;
            vertical-align: middle;
        }
        .totals-summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
        }
        .form-section-title {
            font-size: 15px;
            font-weight: 700;
            color: #334155;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 16px;
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
                            <i class="fas fa-file-contract text-primary me-2"></i>
                            إنشاء وتجهيز عرض سعر جديد للعميل
                        </h1>
                        <div class="page-breadcrumb text-muted fs-7">
                            <a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-home"></i> الرئيسية</a>
                            <span class="mx-2">/</span>
                            <a href="quotations.php" class="text-decoration-none text-muted"><?=QUOTATIONS?></a>
                            <span class="mx-2">/</span>
                            <span class="text-primary fw-semibold"><?=ADD_NEW?></span>
                        </div>
                    </div>
                    <div>
                        <a href="quotations.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> العودة لقائمة العروض
                        </a>
                    </div>
                </div>

                <!-- Form -->
                <form id="quote-form" autocomplete="off">
                    <input type="hidden" name="f" value="addQuote">
                    <input type="hidden" name="csrf_token" value="<?=csrf_token()?>">

                    <!-- Header Card -->
                    <div class="card-pro mb-4 p-4">
                        <div class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-info-circle text-primary me-2"></i>معلومات العرض الأساسية</span>
                            <span class="badge bg-primary-subtle text-primary">عرض سعر مبدئي قابل للتحويل لفاتورة</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">الفرع <span class="text-danger">*</span></label>
                                <select name="branch" id="branch" class="form-select" required onchange="onBranchChange(this.value)">
                                    <?php
                                    $qBr = "SELECT id, name_ar, name_en FROM branches WHERE (storeid='$storeid' OR storeid='0') ORDER BY id ASC";
                                    $rBr = mysqli_query($adController->MySQL, $qBr);
                                    if ($rBr) {
                                        while ($b = mysqli_fetch_assoc($rBr)) {
                                            $bName = ($language == 'ar' ? $b['name_ar'] : $b['name_en']) ?: $b['name_ar'];
                                            $sel = ($b['id'] == $defBranch) ? 'selected' : '';
                                            echo "<option value='{$b['id']}' $sel>" . htmlspecialchars($bName) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">رقم عرض السعر</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-hashtag text-muted"></i></span>
                                    <input type="text" class="form-control fw-bold text-primary bg-light" name="invoice" id="invoice" value="<?=htmlspecialchars($invoice)?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">تاريخ العرض <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="purchase_expiry" id="purchase_expiry" value="<?=date('Y-m-d')?>" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">العميل <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="customer" id="customer" class="form-select" required>
                                        <option value="">-- اختر العميل --</option>
                                        <?php
                                        $qCust = "SELECT id, name_ar, name_en, phone FROM customers WHERE (storeid='$storeid' OR storeid='0') ORDER BY name_ar ASC";
                                        $rCust = mysqli_query($adController->MySQL, $qCust);
                                        if ($rCust) {
                                            while ($c = mysqli_fetch_assoc($rCust)) {
                                                $cName = ($language == 'ar' ? $c['name_ar'] : $c['name_en']) ?: $c['name_ar'];
                                                $phone = !empty($c['phone']) ? " ({$c['phone']})" : "";
                                                echo "<option value='{$c['id']}'>" . htmlspecialchars($cName . $phone) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <a href="customers.php" target="_blank" class="btn btn-outline-secondary" title="إضافة عميل">
                                        <i class="fas fa-user-plus"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">المرجع / السند</label>
                                <input type="text" class="form-control" name="supplier_invoice" id="supplier_invoice" placeholder="رقم طلب العميل أو مرجع المناقصة">
                            </div>
                        </div>
                    </div>

                    <!-- Items Table Card -->
                    <div class="card-pro mb-4 p-4">
                        <div class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-boxes text-primary me-2"></i>الأصناف والأسعار المندرجة بالعرض</span>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addNewRow()">
                                <i class="fas fa-plus me-1"></i> إضافة صنف
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-compact text-center align-middle" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 15%;">المخزن <span class="text-danger">*</span></th>
                                        <th style="width: 30%;">الصنف / الباركود <span class="text-danger">*</span></th>
                                        <th style="width: 10%;">الكمية <span class="text-danger">*</span></th>
                                        <th style="width: 12%;">سعر الوحدة <span class="text-danger">*</span></th>
                                        <th style="width: 9%;">الخصم %</th>
                                        <th style="width: 12%;">قبل الضريبة</th>
                                        <th style="width: 8%;">ضريبة %</th>
                                        <th style="width: 12%;">الإجمالي بالضريبة</th>
                                        <th style="width: 4%;">حذف</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <!-- Dynamic rows via JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-start mt-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addNewRow()">
                                <i class="fas fa-plus-circle me-1"></i> إضافة بند إضافي
                            </button>
                        </div>
                    </div>

                    <!-- Financial Summary & Submission -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6 offset-lg-6">
                            <div class="totals-summary-card">
                                <div class="form-section-title">
                                    <i class="fas fa-calculator text-primary me-2"></i>إجمالي عرض السعر التقديري
                                </div>

                                <table class="table table-borderless align-middle mb-0">
                                    <tr>
                                        <td class="text-secondary">الإجمالي قبل الخصم:</td>
                                        <td class="text-end fw-bold" id="gross_display">0.00 <?=$currency?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary">إجمالي الخصم الممنوح:</td>
                                        <td class="text-end fw-bold text-info" id="disc_display">0.00 <?=$currency?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary">ضريبة القيمة المضافة 14%:</td>
                                        <td class="text-end fw-bold text-danger" id="vat_display">0.00 <?=$currency?></td>
                                    </tr>
                                    <tr class="border-top border-2">
                                        <td class="fs-5 fw-bold text-dark pt-3">الصافي الإجمالي لعرض السعر:</td>
                                        <td class="fs-4 fw-bold text-success text-end pt-3" id="total_display">0.00 <?=$currency?></td>
                                    </tr>
                                </table>

                                <!-- Hidden Inputs -->
                                <input type="hidden" name="final_discount" id="final_discount" value="0.00">
                                <input type="hidden" name="totalAll" id="totalAll" value="0.00">

                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg fw-bold" id="btnSubmit">
                                        <i class="fas fa-check-circle me-1"></i> حفظ وإصدار عرض السعر
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </main>
        </div>
    </div>

    <?php require_once("footer.php"); ?>

    <script>
        const itemsCache = <?=json_encode($itemsCache, JSON_UNESCAPED_UNICODE)?>;
        const storesCache = <?=json_encode($storesCache, JSON_UNESCAPED_UNICODE)?>;
        let rowIndex = 0;

        function getStoresForBranch(branchId) {
            return storesCache.filter(s => s.branch == branchId || s.branch == 0);
        }

        function createStoreOptions(branchId) {
            const stores = getStoresForBranch(branchId);
            let opts = '<option value="">-- اختر المخزن --</option>';
            stores.forEach(s => {
                opts += `<option value="${s.id}">${s.name}</option>`;
            });
            return opts;
        }

        function createItemOptions() {
            let opts = '<option value="">-- اختر الصنف --</option>';
            itemsCache.forEach(it => {
                opts += `<option value="${it.id}" data-barcode="${it.barcode}" data-price="${it.price}" data-vat="${it.vat}" data-disc="${it.disc}" data-unit="${it.unit}">${it.barcode} | ${it.name} (${it.unit})</option>`;
            });
            return opts;
        }

        function addNewRow() {
            const currentBranch = document.getElementById('branch').value;
            const storeOpts = createStoreOptions(currentBranch);
            const itemOpts = createItemOptions();
            const idx = rowIndex++;

            const tr = document.createElement('tr');
            tr.id = `row-${idx}`;
            tr.className = 'item-row';
            tr.innerHTML = `
                <td>
                    <select name="store[]" class="form-select form-select-sm store-select" id="store-${idx}" required>
                        ${storeOpts}
                    </select>
                </td>
                <td>
                    <select name="barcode[]" class="form-select form-select-sm item-select" id="item-${idx}" required onchange="onItemChange(${idx})">
                        ${itemOpts}
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0.01" class="form-control form-control-sm text-center fw-bold" name="item_qty[]" id="qty-${idx}" value="1" required oninput="calculateRow(${idx})">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center" name="item_price[]" id="price-${idx}" value="0.00" required oninput="calculateRow(${idx})">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm text-center" name="item_disc[]" id="disc-${idx}" value="0" oninput="calculateRow(${idx})">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm text-center bg-light" id="subtotal-${idx}" value="0.00" readonly>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center" name="item_vat[]" id="vat-${idx}" value="14" oninput="calculateRow(${idx})">
                    <input type="hidden" id="vatval-${idx}" value="0.00">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm text-center fw-bold text-success bg-light" id="net-${idx}" value="0.00" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm p-1" onclick="removeRow(${idx})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;

            document.getElementById('itemsTableBody').appendChild(tr);

            const storeSelect = document.getElementById(`store-${idx}`);
            if (storeSelect.options.length > 1) {
                storeSelect.selectedIndex = 1;
            }
        }

        function removeRow(idx) {
            const tr = document.getElementById(`row-${idx}`);
            if (tr) {
                tr.remove();
                calculateTotals();
            }
        }

        function onItemChange(idx) {
            const itemSelect = document.getElementById(`item-${idx}`);
            const selectedOpt = itemSelect.options[itemSelect.selectedIndex];
            if (!selectedOpt || !selectedOpt.value) return;

            const price = parseFloat(selectedOpt.getAttribute('data-price')) || 0;
            const vat   = parseFloat(selectedOpt.getAttribute('data-vat')) || 14;
            const disc  = parseFloat(selectedOpt.getAttribute('data-disc')) || 0;

            document.getElementById(`price-${idx}`).value = price.toFixed(2);
            document.getElementById(`vat-${idx}`).value   = vat.toFixed(2);
            document.getElementById(`disc-${idx}`).value  = disc.toFixed(2);

            calculateRow(idx);
        }

        function calculateRow(idx) {
            const qty   = parseFloat(document.getElementById(`qty-${idx}`).value) || 0;
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

            calculateTotals();
        }

        function calculateTotals() {
            let totalGross = 0;
            let totalDisc  = 0;
            let totalVat   = 0;
            let grandTotal = 0;

            const rows = document.querySelectorAll('#itemsTableBody tr');
            rows.forEach(row => {
                const idMatch = row.id.match(/row-(\d+)/);
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

            document.getElementById('final_discount').value = totalDisc.toFixed(2);
            document.getElementById('totalAll').value       = grandTotal.toFixed(2);
        }

        function onBranchChange(branchId) {
            const rows = document.querySelectorAll('#itemsTableBody tr');
            rows.forEach(row => {
                const idMatch = row.id.match(/row-(\d+)/);
                if (idMatch) {
                    const idx = idMatch[1];
                    const storeSel = document.getElementById(`store-${idx}`);
                    if (storeSel) {
                        storeSel.innerHTML = createStoreOptions(branchId);
                        if (storeSel.options.length > 1) {
                            storeSel.selectedIndex = 1;
                        }
                    }
                }
            });

            $.ajax({
                url: 'controller.php',
                type: 'POST',
                data: {
                    'f': 'getNewInvoiceNo',
                    'table': 'quote',
                    'field': 'invoicenumber',
                    'type': '44',
                    'branch': branchId
                },
                success: function(inv) {
                    if (inv && inv.length > 2) {
                        document.getElementById('invoice').value = inv.trim();
                    }
                }
            });
        }

        // Form Submit
        document.getElementById('quote-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const rows = document.querySelectorAll('#itemsTableBody tr');
            if (rows.length === 0) {
                alert('يجب إضافة صنف واحد على الأقل إلى عرض السعر.');
                return;
            }

            const customer = document.getElementById('customer').value;
            if (!customer) {
                alert('يرجى اختيار العميل أولاً.');
                document.getElementById('customer').focus();
                return;
            }

            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري إصدار العرض...';

            $.ajax({
                url: 'controller.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if ($.trim(res) == '1') {
                        alert('تم إصدار عرض السعر بنجاح!');
                        window.location.href = 'quotations.php';
                    } else {
                        alert('تنبيه: ' + res);
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> حفظ وإصدار عرض السعر';
                    }
                },
                error: function(xhr) {
                    alert('حدث خطأ في الاتصال بالسيرفر: ' + xhr.statusText);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> حفظ وإصدار عرض السعر';
                }
            });
        });

        // Add 2 initial rows
        document.addEventListener('DOMContentLoaded', function() {
            addNewRow();
            addNewRow();
        });
    </script>
</body>
</html>
