<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language       = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid        = (int)($_SESSION['storeid'] ?? 1);
$currency       = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';
$enableNegative = (int)($_SESSION['enableNegative'] ?? 0);

// Default Branch
$defBranch = 1;
$resBr = mysqli_query($adController->MySQL, "SELECT id FROM branches WHERE (storeid='$storeid' OR storeid='0') AND status='1' LIMIT 1");
if ($resBr && $brRow = mysqli_fetch_assoc($resBr)) {
    $defBranch = (int)$brRow['id'];
}

// Check if converting from a Quotation
$fromQuoteId  = (int)($_GET['from_quote'] ?? 0);
$prefillCust  = 0;
$prefillDoc   = '';
$prefillItems = [];

if ($fromQuoteId > 0) {
    $qQ = "SELECT * FROM quote WHERE id='$fromQuoteId' AND (storeid='$storeid' OR storeid='0') LIMIT 1";
    $rQ = mysqli_query($adController->MySQL, $qQ);
    if ($rQ && $dataQ = mysqli_fetch_assoc($rQ)) {
        $prefillCust = (int)($dataQ['supplier'] ?? 0);
        $prefillDoc  = 'بناء على عرض سعر #' . ($dataQ['invoicenumber'] ?? '');
        if (!empty($dataQ['branch'])) {
            $defBranch = (int)$dataQ['branch'];
        }

        $qQi = "SELECT qi.*, i.name_ar, i.name_en, i.barcode, u.name_ar AS unit_ar, u.name_en AS unit_en 
                FROM quote_items qi 
                LEFT JOIN items i ON qi.itemid = i.id 
                LEFT JOIN units u ON i.unit = u.id 
                WHERE qi.purchase_id='$fromQuoteId' AND (qi.storeid='$storeid' OR qi.storeid='0')";
        $rQi = mysqli_query($adController->MySQL, $qQi);
        if ($rQi) {
            while ($qiRow = mysqli_fetch_assoc($rQi)) {
                $prefillItems[] = [
                    'itemid' => (int)$qiRow['itemid'],
                    'store'  => (int)$qiRow['store'],
                    'qty'    => (float)$qiRow['quantity'],
                    'price'  => (float)$qiRow['cost'],
                    'disc'   => (float)$qiRow['discount'],
                    'tax'    => (float)$qiRow['tax']
                ];
            }
        }
    }
}

$invoice = $adController->getNewInvoiceNo("sales", "invoicenumber", '11', $defBranch);

// Pre-fetch items list
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
    <title>إنشاء فاتورة بيع جديدة - ERP WAM Tech</title>
    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        .item-row:hover {
            background-color: rgba(16, 185, 129, 0.03);
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
                            <i class="fas fa-file-invoice-dollar text-success me-2"></i>
                            إنشاء فاتورة بيع وتوريد للعميل
                        </h1>
                        <div class="page-breadcrumb text-muted fs-7">
                            <a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-home"></i> الرئيسية</a>
                            <span class="mx-2">/</span>
                            <a href="saleInvoice.php" class="text-decoration-none text-muted"><?=SALES?></a>
                            <span class="mx-2">/</span>
                            <span class="text-success fw-semibold"><?=ADD_NEW?></span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="saleInvoice.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> العودة لسجل الفواتير
                        </a>
                        <a href="pos.php" class="btn btn-outline-primary">
                            <i class="fas fa-cash-register me-1"></i> نقطة البيع السريعة POS
                        </a>
                    </div>
                </div>

                <!-- Invoice Form -->
                <form id="sales-form" autocomplete="off">
                    <input type="hidden" name="f" value="addSale">
                    <input type="hidden" name="ajax" value="1">
                    <input type="hidden" name="csrf_token" value="<?=csrf_token()?>">

                    <!-- Header Details Card -->
                    <div class="card-pro mb-4 p-4">
                        <div class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-info-circle text-primary me-2"></i>بيانات الفاتورة والعميل الأساسية</span>
                            <span class="badge bg-success-subtle text-success">فاتورة ضريبية رسمية 14%</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">الفرع <span class="text-danger">*</span></label>
                                <select name="branch" id="branch" class="form-select" required onchange="onBranchChange(this.value)">
                                    <?php
                                    $qBr = "SELECT id, name_ar, name_en, status FROM branches WHERE (storeid='$storeid' OR storeid='0') ORDER BY id ASC";
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
                                <label class="form-label fw-semibold fs-7 text-secondary">رقم الفاتورة</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-hashtag text-muted"></i></span>
                                    <input type="text" class="form-control fw-bold text-primary bg-light" name="invoice" id="invoice" value="<?=htmlspecialchars($invoice)?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">تاريخ الفاتورة <span class="text-danger">*</span></label>
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
                                                $sel = ($c['id'] == $prefillCust) ? 'selected' : '';
                                                echo "<option value='{$c['id']}' $sel>" . htmlspecialchars($cName . $phone) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <a href="customers.php" target="_blank" class="btn btn-outline-secondary" title="إضافة عميل جديد">
                                        <i class="fas fa-user-plus"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">رقم السند / المرجع</label>
                                <input type="text" class="form-control" name="document" id="document" value="<?=htmlspecialchars($prefillDoc)?>" placeholder="رقم أمر الشراء / الإذن">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-7 text-secondary">خدمة التوصيل / الشحن (ج.م)</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="deliver" id="deliver" value="0.00" oninput="calculateTotals()">
                            </div>
                        </div>
                    </div>

                    <!-- Items Table Card -->
                    <div class="card-pro mb-4 p-4">
                        <div class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-boxes text-success me-2"></i>بنود وأصناف الفاتورة</span>
                            <button type="button" class="btn btn-sm btn-success" onclick="addNewRow()">
                                <i class="fas fa-plus me-1"></i> إضافة بند جديد
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-compact text-center align-middle" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 14%;">المخزن <span class="text-danger">*</span></th>
                                        <th style="width: 25%;">الصنف / الباركود <span class="text-danger">*</span></th>
                                        <th style="width: 8%;">رصيد المخزن</th>
                                        <th style="width: 9%;">الكمية <span class="text-danger">*</span></th>
                                        <th style="width: 10%;">سعر البيع <span class="text-danger">*</span></th>
                                        <th style="width: 8%;">الخصم %</th>
                                        <th style="width: 10%;">الإجمالي قبل الضريبة</th>
                                        <th style="width: 8%;">الضريبة %</th>
                                        <th style="width: 8%;">قيمة الضريبة</th>
                                        <th style="width: 10%;">الإجمالي الصافي</th>
                                        <th style="width: 4%;">حذف</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <!-- Dynamic rows inserted via JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-start mt-2">
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="addNewRow()">
                                <i class="fas fa-plus-circle me-1"></i> إضافة صنف إضافي
                            </button>
                        </div>
                    </div>

                    <!-- Totals and Payment Card -->
                    <div class="row g-4 mb-4">
                        <!-- Payment Options -->
                        <div class="col-lg-6">
                            <div class="card-pro p-4 h-100">
                                <div class="form-section-title">
                                    <i class="fas fa-credit-card text-primary me-2"></i>طرق التحصيل والسداد
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">نوع السداد الافتراضي</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="payment_mode" id="pay_cash" value="cash" checked onchange="onPaymentModeChange('cash')">
                                        <label class="btn btn-outline-success" for="pay_cash"><i class="fas fa-money-bill-wave me-1"></i> نقدي بالكامل</label>

                                        <input type="radio" class="btn-check" name="payment_mode" id="pay_visa" value="visa" onchange="onPaymentModeChange('visa')">
                                        <label class="btn btn-outline-primary" for="pay_visa"><i class="fas fa-credit-card me-1"></i> شبكة / فيزا</label>

                                        <input type="radio" class="btn-check" name="payment_mode" id="pay_credit" value="credit" onchange="onPaymentModeChange('credit')">
                                        <label class="btn btn-outline-warning" for="pay_credit"><i class="fas fa-hand-holding-usd me-1"></i> آجل على العميل</label>

                                        <input type="radio" class="btn-check" name="payment_mode" id="pay_split" value="split" onchange="onPaymentModeChange('split')">
                                        <label class="btn btn-outline-secondary" for="pay_split"><i class="fas fa-layer-group me-1"></i> دفع مركب / جزئي</label>
                                    </div>
                                </div>

                                <div class="row g-3 mt-1">
                                    <div class="col-md-4">
                                        <label class="form-label fs-7 text-secondary">المدفوع نقداً (ج.م)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" name="cash" id="cash_input" value="0.00" oninput="calculateBalance()">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fs-7 text-secondary">المدفوع شبكة/فيزا (ج.م)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" name="visa" id="visa_input" value="0.00" oninput="calculateBalance()">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fs-7 text-secondary">محفظة إلكترونية/مدى (ج.م)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" name="mada" id="mada_input" value="0.00" oninput="calculateBalance()">
                                    </div>
                                </div>

                                <div class="p-3 bg-light border rounded-3 mt-3 d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold text-secondary">المبلغ المتبقي / الآجل:</span>
                                    <span class="fs-5 fw-bold text-danger" id="left_display">0.00 <?=$currency?></span>
                                </div>
                                <input type="hidden" name="left" id="left_input" value="0.00">
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="col-lg-6">
                            <div class="totals-summary-card h-100">
                                <div class="form-section-title">
                                    <i class="fas fa-calculator text-primary me-2"></i>الملخص المالي للفاتورة
                                </div>

                                <table class="table table-borderless align-middle mb-0">
                                    <tr>
                                        <td class="text-secondary">الإجمالي قبل الخصم والضريبة:</td>
                                        <td class="text-end fw-bold" id="gross_display">0.00 <?=$currency?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary">إجمالي الخصم التجاري:</td>
                                        <td class="text-end fw-bold text-info" id="disc_display">0.00 <?=$currency?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary">ضريبة القيمة المضافة 14%:</td>
                                        <td class="text-end fw-bold text-danger" id="vat_display">0.00 <?=$currency?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary">خدمة الشحن / التوصيل:</td>
                                        <td class="text-end fw-bold text-secondary" id="deliver_display">0.00 <?=$currency?></td>
                                    </tr>
                                    <tr class="border-top border-2">
                                        <td class="fs-5 fw-bold text-dark pt-3">الصافي النهائي للفاتورة:</td>
                                        <td class="fs-4 fw-bold text-success text-end pt-3" id="total_display">0.00 <?=$currency?></td>
                                    </tr>
                                </table>

                                <!-- Hidden Inputs for Backend -->
                                <input type="hidden" name="gross_total" id="gross_total" value="0.00">
                                <input type="hidden" name="final_discount" id="final_discount" value="0.00">
                                <input type="hidden" name="vat_all" id="vat_all" value="0.00">
                                <input type="hidden" name="totalAll" id="totalAll" value="0.00">

                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-success btn-lg fw-bold" id="btnSubmit">
                                        <i class="fas fa-check-circle me-1"></i> حفظ وترحيل الفاتورة
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
        const prefillItems = <?=json_encode($prefillItems, JSON_UNESCAPED_UNICODE)?>;
        const enableNegative = <?=$enableNegative?>;
        let rowIndex = 0;

        function getStoresForBranch(branchId) {
            return storesCache.filter(s => s.branch == branchId || s.branch == 0);
        }

        function createStoreOptions(branchId, selectedStore = null) {
            const stores = getStoresForBranch(branchId);
            let opts = '<option value="">-- اختر المخزن --</option>';
            stores.forEach(s => {
                const sel = (selectedStore && s.id == selectedStore) ? 'selected' : '';
                opts += `<option value="${s.id}" ${sel}>${s.name}</option>`;
            });
            return opts;
        }

        function createItemOptions(selectedItem = null) {
            let opts = '<option value="">-- اختر الصنف أو امسح الباركود --</option>';
            itemsCache.forEach(it => {
                const sel = (selectedItem && it.id == selectedItem) ? 'selected' : '';
                opts += `<option value="${it.id}" ${sel} data-barcode="${it.barcode}" data-price="${it.price}" data-vat="${it.vat}" data-disc="${it.disc}" data-unit="${it.unit}">${it.barcode} | ${it.name} (${it.unit})</option>`;
            });
            return opts;
        }

        function addNewRow(preData = null) {
            const currentBranch = document.getElementById('branch').value;
            const storeOpts = createStoreOptions(currentBranch, preData ? preData.store : null);
            const itemOpts = createItemOptions(preData ? preData.itemid : null);
            const idx = rowIndex++;

            const qtyVal   = preData ? preData.qty : 1;
            const priceVal = preData ? preData.price : 0;
            const discVal  = preData ? preData.disc : 0;
            const vatVal   = preData ? preData.tax : 14;

            const tr = document.createElement('tr');
            tr.id = `row-${idx}`;
            tr.className = 'item-row';
            tr.innerHTML = `
                <td>
                    <select name="store[]" class="form-select form-select-sm store-select" id="store-${idx}" required onchange="checkStockBalance(${idx})">
                        ${storeOpts}
                    </select>
                </td>
                <td>
                    <select name="barcode[]" class="form-select form-select-sm item-select" id="item-${idx}" required onchange="onItemChange(${idx})">
                        ${itemOpts}
                    </select>
                </td>
                <td>
                    <span class="badge bg-light text-dark border" id="balance-badge-${idx}">-</span>
                    <input type="hidden" name="item_old[]" id="item_old-${idx}" value="0">
                </td>
                <td>
                    <input type="number" step="0.01" min="0.01" class="form-control form-control-sm text-center fw-bold" name="item_qty[]" id="qty-${idx}" value="${qtyVal}" required oninput="calculateRow(${idx})">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center" name="item_price[]" id="price-${idx}" value="${priceVal.toFixed(2)}" required oninput="calculateRow(${idx})">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm text-center" name="item_disc[]" id="disc-${idx}" value="${discVal.toFixed(2)}" oninput="calculateRow(${idx})">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm text-center bg-light" name="item_total_after_dis[]" id="subtotal-${idx}" value="0.00" readonly>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center" name="item_vat[]" id="vat-${idx}" value="${vatVal.toFixed(2)}" oninput="calculateRow(${idx})">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm text-center bg-light" name="item_vat_val[]" id="vatval-${idx}" value="0.00" readonly>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm text-center fw-bold text-success bg-light" name="item_net_total[]" id="net-${idx}" value="0.00" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm p-1" onclick="removeRow(${idx})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;

            document.getElementById('itemsTableBody').appendChild(tr);

            if (preData) {
                checkStockBalance(idx);
                calculateRow(idx);
            } else {
                const storeSelect = document.getElementById(`store-${idx}`);
                if (storeSelect.options.length > 1) {
                    storeSelect.selectedIndex = 1;
                }
            }
        }

            document.getElementById('itemsTableBody').appendChild(tr);

            // Auto-select first store if available
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

            checkStockBalance(idx);
            calculateRow(idx);
        }

        function checkStockBalance(idx) {
            const itemId = document.getElementById(`item-${idx}`)?.value;
            const storeId = document.getElementById(`store-${idx}`)?.value;
            const badge = document.getElementById(`balance-badge-${idx}`);

            if (!itemId || !storeId) {
                if (badge) badge.innerText = '-';
                return;
            }

            $.ajax({
                url: 'controller.php',
                type: 'POST',
                data: {
                    'f': 'getBalanceItem',
                    'itemId': itemId,
                    'store': storeId,
                    'oldQty': 0
                },
                success: function(bal) {
                    const balance = parseFloat(bal) || 0;
                    if (badge) {
                        badge.innerText = balance.toFixed(2);
                        if (balance <= 0) {
                            badge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle';
                        } else {
                            badge.className = 'badge bg-success-subtle text-success border border-success-subtle';
                        }
                    }
                }
            });
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

            const deliver = parseFloat(document.getElementById('deliver')?.value) || 0;
            grandTotal += deliver;

            document.getElementById('gross_display').innerText = totalGross.toFixed(2) + ' ' + '<?=$currency?>';
            document.getElementById('disc_display').innerText  = totalDisc.toFixed(2) + ' ' + '<?=$currency?>';
            document.getElementById('vat_display').innerText   = totalVat.toFixed(2) + ' ' + '<?=$currency?>';
            document.getElementById('deliver_display').innerText = deliver.toFixed(2) + ' ' + '<?=$currency?>';
            document.getElementById('total_display').innerText = grandTotal.toFixed(2) + ' ' + '<?=$currency?>';

            document.getElementById('gross_total').value    = totalGross.toFixed(2);
            document.getElementById('final_discount').value = totalDisc.toFixed(2);
            document.getElementById('vat_all').value        = totalVat.toFixed(2);
            document.getElementById('totalAll').value       = grandTotal.toFixed(2);

            // Re-apply payment mode logic
            const currentMode = document.querySelector('input[name="payment_mode"]:checked')?.value || 'cash';
            onPaymentModeChange(currentMode);
        }

        function onPaymentModeChange(mode) {
            const grandTotal = parseFloat(document.getElementById('totalAll').value) || 0;
            const cashInput  = document.getElementById('cash_input');
            const visaInput  = document.getElementById('visa_input');
            const madaInput  = document.getElementById('mada_input');

            if (mode === 'cash') {
                cashInput.value = grandTotal.toFixed(2);
                visaInput.value = '0.00';
                madaInput.value = '0.00';
            } else if (mode === 'visa') {
                cashInput.value = '0.00';
                visaInput.value = grandTotal.toFixed(2);
                madaInput.value = '0.00';
            } else if (mode === 'credit') {
                cashInput.value = '0.00';
                visaInput.value = '0.00';
                madaInput.value = '0.00';
            }
            calculateBalance();
        }

        function calculateBalance() {
            const grandTotal = parseFloat(document.getElementById('totalAll').value) || 0;
            const cash = parseFloat(document.getElementById('cash_input').value) || 0;
            const visa = parseFloat(document.getElementById('visa_input').value) || 0;
            const mada = parseFloat(document.getElementById('mada_input').value) || 0;

            const totalPaid = cash + visa + mada;
            const remaining = Math.max(0, grandTotal - totalPaid);

            document.getElementById('left_display').innerText = remaining.toFixed(2) + ' ' + '<?=$currency?>';
            document.getElementById('left_input').value = remaining.toFixed(2);
        }

        function onBranchChange(branchId) {
            // Update store options in all rows
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

            // Get new invoice number for the selected branch
            $.ajax({
                url: 'controller.php',
                type: 'POST',
                data: {
                    'f': 'getNewInvoiceNo',
                    'table': 'sales',
                    'field': 'invoicenumber',
                    'type': '11',
                    'branch': branchId
                },
                success: function(inv) {
                    if (inv && inv.length > 2) {
                        document.getElementById('invoice').value = inv.trim();
                    }
                }
            });
        }

        // Form Submission
        document.getElementById('sales-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const rows = document.querySelectorAll('#itemsTableBody tr');
            if (rows.length === 0) {
                alert('يجب إضافة صنف واحد على الأقل إلى الفاتورة.');
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
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري حفظ الفاتورة...';

            const formData = new FormData(this);

            $.ajax({
                url: 'controller.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res && res.success == 1) {
                        const inv = res.invoice;
                        if (confirm('تم حفظ فاتورة البيع رقم (' + inv + ') بنجاح!\n\nهل ترغب في طباعة الفاتورة الآن؟')) {
                            window.open('reports/sales.php?sd=' + encodeURIComponent(inv), '_blank');
                        }
                        window.location.href = 'saleInvoice.php';
                    } else {
                        alert('تنبيه: ' + (res.error || 'فشل حفظ الفاتورة، يرجى مراجعة البيانات.'));
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> حفظ وترحيل الفاتورة';
                    }
                },
                error: function(xhr) {
                    alert('حدث خطأ في الاتصال بالسيرفر: ' + xhr.statusText);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> حفظ وترحيل الفاتورة';
                }
            });
        });

        // Initialize with prefill items if from quote, or 2 default rows
        document.addEventListener('DOMContentLoaded', function() {
            if (prefillItems && prefillItems.length > 0) {
                prefillItems.forEach(item => addNewRow(item));
            } else {
                addNewRow();
                addNewRow();
            }
            calculateTotals();
        });
    </script>
</body>
</html>
