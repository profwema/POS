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

// Fetch return invoice
$query   = "SELECT * FROM purchase_ret WHERE invoicenumber='$invoiceEsc' AND (storeid='$storeid' OR storeid='0') LIMIT 1";
$resP    = mysqli_query($adController->MySQL, $query);
$dataPur = ($resP && mysqli_num_rows($resP) > 0) ? mysqli_fetch_assoc($resP) : null;

if (!$dataPur) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'><h3>عفواً، إذن مرتجع الشراء غير موجود أو تم حذفه.</h3><a href='purchaseRet.php'>العودة لقائمة المرتجعات</a></div>");
}

$purchInvoice = $dataPur['purchInvoice'] ?? '';
$purchInvEsc  = mysqli_real_escape_string($adController->MySQL, $purchInvoice);

// Fetch return items saved
$queryRetItems = "SELECT pri.*, i.name_ar, i.name_en, i.barcode, u.name_ar AS unit_ar, s.name_ar AS store_name 
                  FROM purchase_ret_items pri 
                  JOIN items i ON pri.itemid = i.id 
                  LEFT JOIN units u ON i.unit = u.id 
                  LEFT JOIN stores s ON pri.store = s.id 
                  WHERE pri.invoice_No='$invoiceEsc'";
$resRet = mysqli_query($adController->MySQL, $queryRetItems);
$savedRetItems = [];
if ($resRet) {
    while ($row = mysqli_fetch_assoc($resRet)) {
        $savedRetItems[] = $row;
    }
}

// Supplier & Branch info
$supId = (int)$dataPur['supplier'];
$resSup = mysqli_query($adController->MySQL, "SELECT name_ar, name_en FROM suppliers WHERE id='$supId' LIMIT 1");
$supplierName = ($resSup && $rowSup = mysqli_fetch_assoc($resSup)) ? (!empty($rowSup['name_ar']) ? $rowSup['name_ar'] : $rowSup['name_en']) : 'مورد عام';

$brId = (int)$dataPur['branch'];
$resBr = mysqli_query($adController->MySQL, "SELECT name_ar, name_en FROM branches WHERE id='$brId' LIMIT 1");
$branchName = ($resBr && $rowBr = mysqli_fetch_assoc($resBr)) ? (!empty($rowBr['name_ar']) ? $rowBr['name_ar'] : $rowBr['name_en']) : 'الفرع الرئيسي';

$invDate = !empty($dataPur['invoice_date']) ? date("Y-m-d", strtotime($dataPur['invoice_date'])) : date("Y-m-d");
$currentPayType = (int)$dataPur['paymentType'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تعديل مرتجع مشتريات #<?= htmlspecialchars($invoice) ?> - WAM Tech POS</title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
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
                        <i class="fas fa-edit text-warning me-2"></i>
                        تعديل إذن مرتجع مشتريات <span class="font-monospace text-primary">#<?= htmlspecialchars($invoice) ?></span>
                    </h1>
                    <div class="page-breadcrumb">
                        <div class="breadcrumb-item">
                            <i class="fas fa-home"></i>
                            <span>الرئيسية</span>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <a href="purchaseRet.php" class="text-decoration-none"><?= RETURNED_PURCHASES ?></a>
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
                                <i class="fas fa-undo-alt text-warning"></i>
                                بيانات إذن المرتجع التابع للفاتورة #<?= htmlspecialchars($purchInvoice) ?>
                            </h2>
                            <a href="purchaseRet.php" class="btn-pro btn-pro-outline btn-pro-sm">
                                <i class="fas fa-arrow-left me-1"></i> العودة للمرتجعات
                            </a>
                        </div>
                    </div>
                    <div class="card-body-pro">

                        <form id="purchaseRet-form" method="POST" action="controller.php" autocomplete="off">
                            <input type="hidden" name="f" value="editPurchaseRet">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="purchInvoice" value="<?= htmlspecialchars($purchInvoice) ?>">
                            <input type="hidden" name="supplier" value="<?= $supId ?>">
                            <input type="hidden" name="branch" value="<?= $brId ?>">

                            <!-- Header Fields -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label-pro">
                                        <i class="fas fa-hashtag text-muted me-1"></i> رقم إذن المرتجع:
                                    </label>
                                    <input type="text" name="invoice" id="invoice" class="form-input-pro font-monospace fw-bold bg-light" value="<?= htmlspecialchars($invoice) ?>" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label-pro">
                                        <i class="fas fa-calendar-alt text-muted me-1"></i> تاريخ المرتجع:
                                    </label>
                                    <input type="date" name="purchase_expiry" id="purchase_expiry" class="form-input-pro" value="<?= htmlspecialchars($invDate) ?>" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label-pro">
                                        <i class="fas fa-truck text-muted me-1"></i> المورد:
                                    </label>
                                    <input type="text" class="form-input-pro bg-light fw-semibold" value="<?= htmlspecialchars($supplierName) ?>" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label-pro">
                                        <i class="fas fa-file-alt text-muted me-1"></i> فاتورة المورد:
                                    </label>
                                    <input type="text" name="supplier_invoice" class="form-input-pro bg-light font-monospace" value="<?= htmlspecialchars($dataPur['supplier_invoice'] ?? '') ?>" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label-pro">
                                        <i class="fas fa-code-branch text-muted me-1"></i> الفرع:
                                    </label>
                                    <input type="text" class="form-input-pro bg-light" value="<?= htmlspecialchars($branchName) ?>" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label-pro">
                                        <i class="fas fa-hand-holding-usd text-muted me-1"></i> طريقة تسوية المرتجع:
                                    </label>
                                    <select name="payType" id="payType" class="form-input-pro fw-semibold">
                                        <option value="1" <?= ($currentPayType === 1) ? 'selected' : '' ?>>استرداد نقدي (إيداع في الصندوق)</option>
                                        <option value="4" <?= ($currentPayType !== 1) ? 'selected' : '' ?>>خصم من رصيد المورد (تخفيض المديونية)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Items Table -->
                            <div class="mb-4">
                                <h3 class="h6 fw-bold mb-2 text-dark">
                                    <i class="fas fa-boxes text-warning me-1"></i>
                                    أصناف المرتجع المسجلة:
                                </h3>

                                <div class="table-responsive">
                                    <table class="table-pro table-compact">
                                        <thead>
                                            <tr class="table-light">
                                                <th style="width: 25%;">الصنف / الباركود</th>
                                                <th style="width: 15%;">المستودع</th>
                                                <th style="width: 10%;" class="text-center">الكمية المرتجعة</th>
                                                <th style="width: 10%;" class="text-center">سعر الوحدة</th>
                                                <th style="width: 8%;" class="text-center">خصم %</th>
                                                <th style="width: 10%;" class="text-center">قبل الضريبة</th>
                                                <th style="width: 8%;" class="text-center">ضريبة %</th>
                                                <th style="width: 10%;" class="text-center">قيمة الضريبة</th>
                                                <th style="width: 10%;" class="text-center">الإجمالي</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rIdx = 0;
                                            foreach ($savedRetItems as $item): 
                                                $rIdx++;
                                                $itemId = (int)$item['itemid'];
                                                $storeId = (int)$item['store'];
                                                $storeTitle = !empty($item['store_name']) ? $item['store_name'] : 'المخزن الرئيسي';
                                                $itemName = !empty($item['name_ar']) ? $item['name_ar'] : $item['name_en'];
                                                $barcode = !empty($item['barcode']) ? $item['barcode'] : '---';
                                                $retQty = (float)$item['quantity'];
                                                $cost = (float)$item['cost'];
                                                $discount = (float)$item['discount'];
                                                $tax = (float)$item['tax'];

                                                $baseTot = $retQty * $cost;
                                                $discVal = $baseTot * ($discount / 100);
                                                $totBefVat = $baseTot - $discVal;
                                                $vatVal = $totBefVat * ($tax / 100);
                                                $totWithVat = $totBefVat + $vatVal;
                                            ?>
                                                <tr>
                                                    <td>
                                                        <input type="hidden" name="barcode[]" value="<?= $itemId ?>">
                                                        <div class="fw-semibold"><?= htmlspecialchars($itemName) ?></div>
                                                        <span class="text-muted small font-monospace"><?= htmlspecialchars($barcode) ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="store[]" value="<?= $storeId ?>">
                                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($storeTitle) ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="1" min="0" name="item_qty[]" id="qty-<?= $rIdx ?>" class="form-input-pro text-center py-1 fw-bold text-danger" value="<?= $retQty ?>" oninput="calcRetRow(<?= $rIdx ?>)">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="item_price[]" id="price-<?= $rIdx ?>" class="form-input-pro text-center py-1 bg-light font-monospace" value="<?= number_format($cost, 2, '.', '') ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="item_disc[]" id="disc-<?= $rIdx ?>" class="form-input-pro text-center py-1 bg-light font-monospace" value="<?= number_format($discount, 2, '.', '') ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="item_total_after_dis[]" id="tot-b-vat-<?= $rIdx ?>" class="form-input-pro text-center py-1 bg-light font-monospace" value="<?= number_format($totBefVat, 2, '.', '') ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="item_vat[]" id="vat-<?= $rIdx ?>" class="form-input-pro text-center py-1 bg-light font-monospace" value="<?= number_format($tax, 2, '.', '') ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="item_vat_value[]" id="vat-val-<?= $rIdx ?>" class="form-input-pro text-center py-1 bg-light font-monospace text-warning" value="<?= number_format($vatVal, 2, '.', '') ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="item-total[]" id="tot-row-<?= $rIdx ?>" class="form-input-pro text-center py-1 bg-light font-monospace fw-bold text-danger" value="<?= number_format($totWithVat, 2, '.', '') ?>" readonly>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Totals Summary Box -->
                            <div class="row g-4 align-items-start">
                                <div class="col-lg-6">
                                    <div class="p-3 bg-light rounded border">
                                        <h5 class="fw-bold mb-2 small text-muted">
                                            <i class="fas fa-info-circle me-1 text-info"></i> تنبيه تعديل المرتجع
                                        </h5>
                                        <p class="text-muted small mb-0">
                                            عند تعديل كميات المرتجع، يتم تحديث حركة المخزن المصروفة تلقائياً وتعديل القيد المحاسبي وحساب المورد بدقة.
                                        </p>
                                    </div>
                                    <div class="error-red mt-3 text-danger fw-bold"></div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="totals-summary-card shadow-sm">
                                        <table class="table table-borderless mb-0">
                                            <tr>
                                                <td class="fw-semibold text-muted py-1">إجمالي المرتجع قبل الضريبة:</td>
                                                <td class="text-end py-1">
                                                    <input type="text" name="gross_total" id="total-before-vat" class="form-input-pro text-end font-monospace py-1 fw-bold" value="<?= number_format((float)($dataPur['gross_total'] ?? 0), 2, '.', '') ?>" readonly style="max-width: 160px; display: inline-block;"> <?= $currency ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted py-1">ضريبة القيمة المضافة للمرتجع:</td>
                                                <td class="text-end py-1">
                                                    <input type="text" name="vat_all" id="total-vat-value" class="form-input-pro text-end font-monospace py-1 fw-bold text-warning" value="<?= number_format((float)($dataPur['vat_all'] ?? 0), 2, '.', '') ?>" readonly style="max-width: 160px; display: inline-block;"> <?= $currency ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted py-1">خصم إضافي:</td>
                                                <td class="text-end py-1">
                                                    <input type="number" step="0.01" name="final_discount" id="final_discount" class="form-input-pro text-end font-monospace py-1" value="<?= number_format((float)($dataPur['dis_added'] ?? 0), 2, '.', '') ?>" style="max-width: 160px; display: inline-block;" oninput="calcRetTotals()"> <?= $currency ?>
                                                </td>
                                            </tr>
                                            <tr class="border-top">
                                                <td class="fw-bold fs-5 text-danger py-2">صافي قيمة المرتجع المستردة:</td>
                                                <td class="text-end py-2">
                                                    <input type="text" name="totalAll" id="totalAll" class="form-input-pro text-end font-monospace py-1 fs-5 fw-bold text-danger" value="<?= number_format((float)($dataPur['all_total'] ?? 0), 2, '.', '') ?>" readonly style="max-width: 160px; display: inline-block;"> <?= $currency ?>
                                                </td>
                                            </tr>
                                        </table>

                                        <div class="d-flex gap-2 justify-content-end mt-3 pt-3 border-top">
                                            <a href="purchaseRet.php" class="btn-pro btn-pro-outline">
                                                <i class="fas fa-times me-1"></i> إلغاء
                                            </a>
                                            <button type="button" id="submit-purchaseRet" class="btn-pro btn-pro-primary btn-pro-lg px-4 shadow">
                                                <i class="fas fa-save me-1"></i> تحديث وحفظ إذن المرتجع
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
        function calcRetRow(idx) {
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

            calcRetTotals();
        }

        function calcRetTotals() {
            let totalBeforeVat = 0;
            let totalVatValue = 0;
            let totalAfterVat = 0;

            document.querySelectorAll('[name="item_total_after_dis[]"]').forEach((el, index) => {
                const vatValInput = document.querySelectorAll('[name="item_vat_value[]"]')[index];
                const totRowInput = document.querySelectorAll('[name="item-total[]"]')[index];

                if (el && vatValInput && totRowInput) {
                    totalBeforeVat += parseFloat(el.value) || 0;
                    totalVatValue += parseFloat(vatValInput.value) || 0;
                    totalAfterVat += parseFloat(totRowInput.value) || 0;
                }
            });

            const finalDisc = parseFloat(document.getElementById('final_discount')?.value) || 0;
            const grandTotal = totalAfterVat - finalDisc;

            document.getElementById('total-before-vat').value = totalBeforeVat.toFixed(2);
            document.getElementById('total-vat-value').value = totalVatValue.toFixed(2);
            document.getElementById('totalAfter-vat').value = totalAfterVat.toFixed(2);
            document.getElementById('totalAll').value = Math.max(0, grandTotal).toFixed(2);
        }

        document.addEventListener("DOMContentLoaded", function () {
            const submitBtn = document.getElementById("submit-purchaseRet");
            if (submitBtn) {
                submitBtn.addEventListener("click", function () {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري تحديث المرتجع...';

                    const form = document.getElementById("purchaseRet-form");
                    const formData = new FormData(form);

                    fetch("controller.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> تحديث وحفظ إذن المرتجع';

                        if (data.success === "1" || data.success === 1) {
                            alert("تم تحديث مرتجع المشتريات بنجاح!");
                            location.replace("purchaseRet.php");
                        } else {
                            const errMsg = data.error || "حدث خطأ أثناء حفظ المرتجع.";
                            document.querySelector(".error-red").textContent = errMsg;
                            alert("خطأ: " + errMsg);
                        }
                    })
                    .catch(err => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> تحديث وحفظ إذن المرتجع';
                        console.error("PurchaseRet error:", err);
                        alert("حدث خطأ في الاتصال بالخادم.");
                    });
                });
            }
        });
    </script>
</body>

</html>
