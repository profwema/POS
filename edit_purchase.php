<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';

// Resolve invoice number (support encrypted and plain)
$rawSd = trim((string)($_REQUEST['sd'] ?? ''));
$decrypted = $adController->encrypt_decrypt(2, urldecode($rawSd), 0);
$invoice = !empty($decrypted) ? $decrypted : $rawSd;
$invoiceEsc = mysqli_real_escape_string($adController->MySQL, $invoice);

// Fetch purchase invoice
$query   = "SELECT * FROM purchase WHERE invoicenumber='$invoiceEsc' AND (storeid='$storeid' OR storeid='0') LIMIT 1";
$resP    = mysqli_query($adController->MySQL, $query);
$dataPur = ($resP && mysqli_num_rows($resP) > 0) ? mysqli_fetch_assoc($resP) : null;

if (!$dataPur) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'><h3>عفواً، فاتورة المشتريات غير موجودة أو تم حذفها.</h3><a href='purchase.php'>العودة لقائمة الفواتير</a></div>");
}

// Fetch invoice items
$queryItemsSaved = "SELECT pi.*, i.name_ar, i.name_en, i.barcode, u.name_ar AS unit_ar 
                    FROM purchase_items pi 
                    LEFT JOIN items i ON pi.itemid = i.id 
                    LEFT JOIN units u ON i.unit = u.id 
                    WHERE pi.invoice_No='$invoiceEsc'";
$resSaved = mysqli_query($adController->MySQL, $queryItemsSaved);
$savedItems = [];
if ($resSaved) {
    while ($row = mysqli_fetch_assoc($resSaved)) {
        $savedItems[] = $row;
    }
}

// Pre-fetch all items for item selector dropdown
$queryItemsFast = "SELECT i.id, i.barcode, i.name_ar, i.name_en, i.vat, i.price, i.discount, u.name_ar AS unit_ar, u.name_en AS unit_en 
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

$invDate = !empty($dataPur['invoice_date']) ? date("Y-m-d", strtotime($dataPur['invoice_date'])) : date("Y-m-d");
$currentBranch = (int)$dataPur['branch'];
$currentSupplier = (int)$dataPur['supplier'];
$currentPayType = (int)$dataPur['paymentType'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تعديل فاتورة مشتريات #<?= htmlspecialchars($invoice) ?> - WAM Tech POS</title>

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
            border-radius: 10px;
            padding: 20px;
        }
    </style>
</head>

<body>

    <div class="admin-wrapper">

        <!-- Professional Sidebar -->
        <?php require_once("components/sidebar_pro.php"); ?>

        <!-- Main Content Wrapper -->
        <div style="flex: 1; display: flex; flex-direction: column;">

            <!-- Professional Navbar -->
            <?php require_once("components/header_pro.php"); ?>

            <!-- Main Content -->
            <main class="admin-content">

                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-edit text-primary me-2"></i>
                        تعديل فاتورة مشتريات <span class="font-monospace text-primary">#<?= htmlspecialchars($invoice) ?></span>
                    </h1>
                    <div class="page-breadcrumb">
                        <div class="breadcrumb-item">
                            <i class="fas fa-home"></i>
                            <span>الرئيسية</span>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <a href="purchase.php" class="text-decoration-none"><?= PURCHASES ?></a>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <span class="text-primary"><?= EDIT ?></span>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="card-pro">
                    <div class="card-header-pro">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="card-title-pro mb-0">
                                <i class="fas fa-file-invoice text-primary"></i>
                                تفاصيل ومحتويات الفاتورة
                            </h2>
                            <div class="d-flex gap-2">
                                <a href="reports/purchase.php?sd=<?= urlencode($invoice) ?>" target="_blank" class="btn-pro btn-pro-outline btn-pro-sm">
                                    <i class="fas fa-print me-1"></i> طباعة الفاتورة
                                </a>
                                <a href="purchase.php" class="btn-pro btn-pro-outline btn-pro-sm">
                                    <i class="fas fa-arrow-left me-1"></i> العودة للقائمة
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-pro">

                        <form id="purchase-form" method="POST" action="controller.php" autocomplete="off">
                            <input type="hidden" name="f" value="editPurchase">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                            <!-- Header Fields -->
                            <div class="row g-3 mb-4">
                                <!-- Invoice Number -->
                                <div class="col-md-3">
                                    <label class="form-label-pro">
                                        <i class="fas fa-hashtag text-muted me-1"></i> <?= INVOICE_NUMBER ?>:
                                    </label>
                                    <input type="text" name="invoice" id="invoice" class="form-input-pro font-monospace fw-bold bg-light" value="<?= htmlspecialchars($invoice) ?>" readonly>
                                </div>

                                <!-- Invoice Date -->
                                <div class="col-md-3">
                                    <label class="form-label-pro">
                                        <i class="fas fa-calendar-alt text-muted me-1"></i> <?= INVOICE_DATE ?>:
                                    </label>
                                    <input type="date" name="purchase_expiry" id="purchase_expiry" class="form-input-pro" value="<?= htmlspecialchars($invDate) ?>" required>
                                </div>

                                <!-- Supplier -->
                                <div class="col-md-3">
                                    <label class="form-label-pro">
                                        <i class="fas fa-truck text-muted me-1"></i> <?= SUPPLIER ?>:
                                    </label>
                                    <select name="supplier" id="supplier" class="form-input-pro select2" required>
                                        <option value="">-- اختر المورد --</option>
                                        <?php
                                        $querySup = "SELECT id, name_ar, name_en, phone FROM suppliers WHERE storeid='$storeid' OR storeid='0' ORDER BY name_ar ASC";
                                        $resSup = mysqli_query($adController->MySQL, $querySup);
                                        if ($resSup) {
                                            while ($sRow = mysqli_fetch_assoc($resSup)) {
                                                $sTitle = !empty($sRow['name_ar']) ? $sRow['name_ar'] : $sRow['name_en'];
                                                $sPhone = !empty($sRow['phone']) ? " ({$sRow['phone']})" : "";
                                                $selected = ($currentSupplier === (int)$sRow['id']) ? 'selected' : '';
                                                echo "<option value='{$sRow['id']}' $selected>" . htmlspecialchars($sTitle . $sPhone) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Supplier Invoice No -->
                                <div class="col-md-3">
                                    <label class="form-label-pro">
                                        <i class="fas fa-file-alt text-muted me-1"></i> <?= SUPPLIER_INVOICE ?>:
                                    </label>
                                    <input type="text" name="supplier_invoice" id="supplier_invoice" class="form-input-pro font-monospace" placeholder="رقم فاتورة المورد الورقية" value="<?= htmlspecialchars($dataPur['supplier_invoice'] ?? '') ?>">
                                </div>

                                <!-- Branch -->
                                <div class="col-md-3">
                                    <label class="form-label-pro">
                                        <i class="fas fa-code-branch text-muted me-1"></i> <?= BRANCH ?>:
                                    </label>
                                    <select name="branch" id="branch" class="form-input-pro" onchange="filterStoresByBranch(this.value)">
                                        <?php
                                        $queryBr = "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' OR storeid='0' ORDER BY name_ar ASC";
                                        $resBr2 = mysqli_query($adController->MySQL, $queryBr);
                                        if ($resBr2) {
                                            while ($bRow = mysqli_fetch_assoc($resBr2)) {
                                                $bTitle = !empty($bRow['name_ar']) ? $bRow['name_ar'] : $bRow['name_en'];
                                                $selected = ($currentBranch === (int)$bRow['id']) ? 'selected' : '';
                                                echo "<option value='{$bRow['id']}' $selected>" . htmlspecialchars($bTitle) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Payment Type -->
                                <div class="col-md-3">
                                    <label class="form-label-pro">
                                        <i class="fas fa-money-check-alt text-muted me-1"></i> <?= PAYMENT_TYPE ?>:
                                    </label>
                                    <select name="payType" id="payType" class="form-input-pro fw-semibold">
                                        <option value="1" <?= ($currentPayType === 1) ? 'selected' : '' ?>>نقدي (كاش - مسدد فوراً من الصندوق)</option>
                                        <option value="4" <?= ($currentPayType !== 1) ? 'selected' : '' ?>>آجل (على الحساب - مستحق للمورد)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Invoice Items Table Section -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="h6 fw-bold mb-0 text-dark">
                                        <i class="fas fa-boxes text-warning me-1"></i>
                                        أصناف وبنود فاتورة المشتريات
                                    </h3>
                                    <button type="button" class="btn-pro btn-pro-outline btn-pro-sm" onclick="addNewItemRow()">
                                        <i class="fas fa-plus me-1"></i> إضافة بند جديد
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table-pro table-compact" id="itemsTable">
                                        <thead>
                                            <tr class="table-light">
                                                <th style="width: 14%;">المستودع المستلم</th>
                                                <th style="width: 25%;">الصنف / الباركود</th>
                                                <th style="width: 9%;" class="text-center">الكمية</th>
                                                <th style="width: 10%;" class="text-center">سعر الشراء</th>
                                                <th style="width: 8%;" class="text-center">خصم %</th>
                                                <th style="width: 10%;" class="text-center">قبل الضريبة</th>
                                                <th style="width: 8%;" class="text-center">ضريبة %</th>
                                                <th style="width: 8%;" class="text-center">قيمة الضريبة</th>
                                                <th style="width: 10%;" class="text-center">الإجمالي</th>
                                                <th style="width: 4%;" class="text-center">حذف</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTableBody">
                                            <!-- Dynamically populated or loaded from existing saved items -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Bottom Totals & Submission -->
                            <div class="row g-4 align-items-start">
                                <div class="col-lg-6">
                                    <div class="p-3 bg-light rounded border">
                                        <h5 class="fw-bold mb-2 small text-muted">
                                            <i class="fas fa-info-circle me-1 text-info"></i> تعليمات تعديل الفاتورة
                                        </h5>
                                        <ul class="text-muted small mb-0 pe-3">
                                            <li>عند حفظ التعديل، يتم تصحيح حركات المخزون والقيود المحاسبية السابقة وإعادة تسجيل البنود المعدلة تلقائياً.</li>
                                            <li>تأكد من اختيار المستودع الصحيح لكل بند لتسوية رصيد المخزن بدقة.</li>
                                        </ul>
                                    </div>

                                    <div class="error-red mt-3 text-danger fw-bold"></div>
                                </div>

                                <!-- Totals Summary Box -->
                                <div class="col-lg-6">
                                    <div class="totals-summary-card shadow-sm">
                                        <table class="table table-borderless mb-0">
                                            <tr>
                                                <td class="fw-semibold text-muted py-1">الإجمالي قبل الضريبة:</td>
                                                <td class="text-end py-1">
                                                    <input type="text" name="gross_total" id="total-before-vat" class="form-input-pro text-end font-monospace py-1 fw-bold" value="<?= number_format((float)($dataPur['gross_total'] ?? 0), 2, '.', '') ?>" readonly style="max-width: 160px; display: inline-block;"> <?= $currency ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted py-1">إجمالي ضريبة القيمة المضافة:</td>
                                                <td class="text-end py-1">
                                                    <input type="text" name="vat_all" id="total-vat-value" class="form-input-pro text-end font-monospace py-1 fw-bold text-warning" value="<?= number_format((float)($dataPur['vat_all'] ?? 0), 2, '.', '') ?>" readonly style="max-width: 160px; display: inline-block;"> <?= $currency ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted py-1">الإجمالي شامل الضريبة:</td>
                                                <td class="text-end py-1">
                                                    <input type="text" id="total-after-vat" class="form-input-pro text-end font-monospace py-1 fw-bold" value="0.00" readonly style="max-width: 160px; display: inline-block;"> <?= $currency ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted py-1">خصم إضافي عام:</td>
                                                <td class="text-end py-1">
                                                    <input type="number" step="0.01" name="final_discount" id="final_discount" class="form-input-pro text-end font-monospace py-1" value="<?= number_format((float)($dataPur['dis_added'] ?? 0), 2, '.', '') ?>" style="max-width: 160px; display: inline-block;" oninput="calcTotals()"> <?= $currency ?>
                                                </td>
                                            </tr>
                                            <tr class="border-top">
                                                <td class="fw-bold fs-5 text-primary py-2">الصافي النهائي المعدل:</td>
                                                <td class="text-end py-2">
                                                    <input type="text" name="totalAll" id="totalAll" class="form-input-pro text-end font-monospace py-1 fs-5 fw-bold text-primary" value="<?= number_format((float)($dataPur['all_total'] ?? 0), 2, '.', '') ?>" readonly style="max-width: 160px; display: inline-block;"> <?= $currency ?>
                                                </td>
                                            </tr>
                                        </table>

                                        <div class="d-flex gap-2 justify-content-end mt-3 pt-3 border-top">
                                            <a href="purchase.php" class="btn-pro btn-pro-outline">
                                                <i class="fas fa-times me-1"></i> إلغاء
                                            </a>
                                            <button type="button" id="submit-purchase" class="btn-pro btn-pro-primary btn-pro-lg px-4 shadow">
                                                <i class="fas fa-save me-1"></i> حفظ وتحديث الفاتورة
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>

                <!-- Professional Footer -->
                <?php require_once("components/footer_pro.php"); ?>

            </main>

        </div>

    </div>

    <!-- Scripts -->
    <?php require_once("include.php"); ?>
    <script src="js/admin-pro.js"></script>

    <script>
        const allStores = <?= json_encode($storesCache, JSON_UNESCAPED_UNICODE) ?>;
        const allItems = <?= json_encode($itemsCache, JSON_UNESCAPED_UNICODE) ?>;
        const initialItems = <?= json_encode($savedItems, JSON_UNESCAPED_UNICODE) ?>;
        let rowIndex = 0;

        function getStoresForBranch(branchId) {
            branchId = parseInt(branchId) || 0;
            return allStores.filter(s => s.branch === branchId || s.branch === 0);
        }

        function filterStoresByBranch(branchId) {
            const stores = getStoresForBranch(branchId);
            document.querySelectorAll('.store-select').forEach(sel => {
                const currentVal = sel.value;
                sel.innerHTML = '<option value="">-- اختر المخزن --</option>';
                stores.forEach(st => {
                    const opt = document.createElement('option');
                    opt.value = st.id;
                    opt.textContent = st.name;
                    if (st.id == currentVal) opt.selected = true;
                    sel.appendChild(opt);
                });
                if (!sel.value && stores.length > 0) {
                    sel.value = stores[0].id;
                }
            });
        }

        function addNewItemRow(presetData = null) {
            const currentBranch = document.getElementById('branch').value;
            const stores = getStoresForBranch(currentBranch);

            const selectedItemId = presetData ? parseInt(presetData.itemid) : 0;
            const selectedStoreId = presetData ? parseInt(presetData.store) : 0;
            const qtyVal = presetData ? parseFloat(presetData.quantity) : 1;
            const priceVal = presetData ? parseFloat(presetData.cost) : 0;
            const discVal = presetData ? parseFloat(presetData.discount) : 0;
            const vatVal = presetData ? parseFloat(presetData.tax) : 14;

            let storeOptionsHtml = '<option value="">-- اختر المخزن --</option>';
            stores.forEach(st => {
                const sel = (selectedStoreId === st.id) ? 'selected' : '';
                storeOptionsHtml += `<option value="${st.id}" ${sel}>${st.name}</option>`;
            });

            let itemOptionsHtml = '<option value="">-- اختر الصنف أو امسح الباركود --</option>';
            allItems.forEach(item => {
                const sel = (selectedItemId === item.id) ? 'selected' : '';
                itemOptionsHtml += `<option value="${item.id}" ${sel} data-price="${item.price}" data-vat="${item.vat}" data-disc="${item.disc}">${item.barcode} | ${item.name} (${item.unit})</option>`;
            });

            const tr = document.createElement('tr');
            tr.id = `row-${rowIndex}`;
            tr.className = 'item-row';
            tr.innerHTML = `
                <td>
                    <select name="store[]" class="form-input-pro py-1 store-select" required>
                        ${storeOptionsHtml}
                    </select>
                </td>
                <td>
                    <select name="barcode[]" class="form-input-pro py-1 item-select" onchange="onItemSelect(this, ${rowIndex})" required>
                        ${itemOptionsHtml}
                    </select>
                </td>
                <td>
                    <input type="number" step="1" min="1" name="item_qty[]" id="qty-${rowIndex}" class="form-input-pro text-center py-1 fw-bold" value="${qtyVal}" oninput="calcRow(${rowIndex})" required>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="item_price[]" id="price-${rowIndex}" class="form-input-pro text-center py-1" value="${priceVal.toFixed(2)}" oninput="calcRow(${rowIndex})" required>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" max="100" name="item_disc[]" id="disc-${rowIndex}" class="form-input-pro text-center py-1" value="${discVal.toFixed(2)}" oninput="calcRow(${rowIndex})">
                </td>
                <td>
                    <input type="text" name="item_total_after_dis[]" id="tot-b-vat-${rowIndex}" class="form-input-pro text-center py-1 bg-light font-monospace" value="0.00" readonly>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="item_vat[]" id="vat-${rowIndex}" class="form-input-pro text-center py-1" value="${vatVal.toFixed(2)}" oninput="calcRow(${rowIndex})">
                </td>
                <td>
                    <input type="text" name="item_vat_value[]" id="vat-val-${rowIndex}" class="form-input-pro text-center py-1 bg-light font-monospace text-warning" value="0.00" readonly>
                </td>
                <td>
                    <input type="text" name="item-total[]" id="tot-row-${rowIndex}" class="form-input-pro text-center py-1 bg-light font-monospace fw-bold text-primary" value="0.00" readonly>
                </td>
                <td class="text-center">
                    <button type="button" class="btn-pro btn-pro-danger btn-pro-sm" onclick="removeRow(${rowIndex})" title="حذف هذا البند">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            document.getElementById('itemsTableBody').appendChild(tr);

            calcRow(rowIndex);
            rowIndex++;
        }

        function onItemSelect(selectEl, idx) {
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            if (!selectedOpt || !selectedOpt.value) return;

            const price = parseFloat(selectedOpt.getAttribute('data-price')) || 0;
            const vat = parseFloat(selectedOpt.getAttribute('data-vat')) || 14;
            const disc = parseFloat(selectedOpt.getAttribute('data-disc')) || 0;

            document.getElementById(`price-${idx}`).value = price.toFixed(2);
            document.getElementById(`vat-${idx}`).value = vat.toFixed(2);
            document.getElementById(`disc-${idx}`).value = disc.toFixed(2);

            calcRow(idx);
        }

        function removeRow(idx) {
            const row = document.getElementById(`row-${idx}`);
            if (row) {
                row.remove();
                calcTotals();
            }
        }

        function calcRow(idx) {
            const qty = parseFloat(document.getElementById(`qty-${idx}`)?.value) || 0;
            const price = parseFloat(document.getElementById(`price-${idx}`)?.value) || 0;
            const discPercent = parseFloat(document.getElementById(`disc-${idx}`)?.value) || 0;
            const vatPercent = parseFloat(document.getElementById(`vat-${idx}`)?.value) || 0;

            const baseTotal = qty * price;
            const discAmount = baseTotal * (discPercent / 100);
            const totalAfterDisc = baseTotal - discAmount;
            const vatAmount = totalAfterDisc * (vatPercent / 100);
            const totalWithVat = totalAfterDisc + vatAmount;

            if (document.getElementById(`tot-b-vat-${idx}`)) {
                document.getElementById(`tot-b-vat-${idx}`).value = totalAfterDisc.toFixed(2);
            }
            if (document.getElementById(`vat-val-${idx}`)) {
                document.getElementById(`vat-val-${idx}`).value = vatAmount.toFixed(2);
            }
            if (document.getElementById(`tot-row-${idx}`)) {
                document.getElementById(`tot-row-${idx}`).value = totalWithVat.toFixed(2);
            }

            calcTotals();
        }

        function calcTotals() {
            let totalBeforeVat = 0;
            let totalVatValue = 0;
            let totalAfterVat = 0;

            document.querySelectorAll('#itemsTableBody tr').forEach(tr => {
                const bVatInput = tr.querySelector('[name="item_total_after_dis[]"]');
                const vatValInput = tr.querySelector('[name="item_vat_value[]"]');
                const totRowInput = tr.querySelector('[name="item-total[]"]');

                if (bVatInput && vatValInput && totRowInput) {
                    totalBeforeVat += parseFloat(bVatInput.value) || 0;
                    totalVatValue += parseFloat(vatValInput.value) || 0;
                    totalAfterVat += parseFloat(totRowInput.value) || 0;
                }
            });

            const finalDisc = parseFloat(document.getElementById('final_discount')?.value) || 0;
            const grandTotal = totalAfterVat - finalDisc;

            document.getElementById('total-before-vat').value = totalBeforeVat.toFixed(2);
            document.getElementById('total-vat-value').value = totalVatValue.toFixed(2);
            document.getElementById('total-after-vat').value = totalAfterVat.toFixed(2);
            document.getElementById('totalAll').value = Math.max(0, grandTotal).toFixed(2);
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Populate saved items
            if (initialItems && initialItems.length > 0) {
                initialItems.forEach(item => addNewItemRow(item));
            } else {
                addNewItemRow();
            }

            // Submit purchase update via AJAX
            document.getElementById("submit-purchase").addEventListener("click", function() {
                const supplier = document.getElementById("supplier").value;
                if (!supplier) {
                    alert("يرجى اختيار المورد أولاً.");
                    document.getElementById("supplier").focus();
                    return;
                }

                const rows = document.querySelectorAll('#itemsTableBody tr');
                let hasValidItem = false;
                rows.forEach(r => {
                    const itemSel = r.querySelector('.item-select');
                    if (itemSel && itemSel.value) hasValidItem = true;
                });

                if (!hasValidItem) {
                    alert("يرجى اختيار صنف واحد على الأقل في الفاتورة.");
                    return;
                }

                const submitBtn = document.getElementById("submit-purchase");
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري تحديث الفاتورة...';

                const form = document.getElementById("purchase-form");
                const formData = new FormData(form);

                fetch("controller.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ وتحديث الفاتورة';

                    if (data.success === "1" || data.success === 1) {
                        alert("تم تحديث فاتورة الشراء بنجاح!");
                        location.replace("purchase.php");
                    } else {
                        const errMsg = data.error || "حدث خطأ غير متوقع أثناء الحفظ.";
                        document.querySelector(".error-red").textContent = errMsg;
                        alert("خطأ: " + errMsg);
                    }
                })
                .catch(err => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ وتحديث الفاتورة';
                    console.error("Purchase update error:", err);
                    alert("حدث خطأ في الاتصال بالخادم.");
                });
            });
        });
    </script>
</body>

</html>
