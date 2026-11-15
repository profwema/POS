<?php
/**
 * شاشة تسجيل فاتورة مشتريات وتوريد بضاعة جديدة
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "تسجيل فاتورة مشتريات وتوريد جديدة";
?>

<div class="purchase-create-page">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-file-invoice-dollar" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                تسجيل فاتورة مشتريات وتوريد بضاعة جديدة
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                إدخال بنود الشحنة الموردة، تحديث رصيد المستودع، وإعادة حساب متوسط التكلفة المرجح (WAC) تلقائياً
            </p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>purchases.php" class="btn btn-outline">
                <i class="fas fa-arrow-right"></i>
                العودة لسجل المشتريات
            </a>
        </div>
    </div>

    <form id="purchaseForm" onsubmit="submitPurchaseInvoice(event)">
        <!-- Invoice Metadata Card -->
        <div class="card" style="margin-bottom: 1.5rem; padding: 1.5rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1.25rem; color: var(--text-main); border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                <i class="fas fa-info-circle" style="color: #38bdf8; margin-left: 0.5rem;"></i>
                بيانات التوريد والمورد
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">
                        المورد <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="supplier_id" id="supplierSelect" class="form-control" required onchange="onSupplierChange()">
                        <option value="">-- اختر المورد --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>" data-balance="<?= $s['current_balance'] ?>" data-limit="<?= $s['credit_limit'] ?>">
                                <?= htmlspecialchars($s['name']) ?> (رصيده: <?= Helpers::formatCurrency($s['current_balance']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small id="supplierBalanceHelp" style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 4px;">
                        حدد المورد لعرض موقفه المالي الحالي
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">
                        المستودع المستلم <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="store_id" id="storeSelect" class="form-control" required>
                        <?php foreach ($stores as $st): ?>
                            <option value="<?= $st['id'] ?>" <?= ($st['id'] == 1) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($st['name']) ?> (<?= htmlspecialchars($st['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">تاريخ الفاتورة</label>
                    <input type="date" name="invoice_date" id="invoiceDate" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">رقم فاتورة المورد الدفترية</label>
                    <input type="text" name="supplier_invoice_number" id="supplierInvoiceNumber" class="form-control" placeholder="مثال: INV-9042">
                </div>
            </div>
        </div>

        <!-- Items Card -->
        <div class="card" style="margin-bottom: 1.5rem; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--text-main);">
                    <i class="fas fa-boxes" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                    بنود وأصناف الشحنة الموردة
                </h3>
                <button type="button" class="btn btn-sm btn-outline" onclick="addNewItemRow()" style="gap: 0.4rem;">
                    <i class="fas fa-plus"></i>
                    إضافة صنف آخر
                </button>
            </div>

            <div class="table-responsive">
                <table class="table" id="itemsTable" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="min-width: 250px;">الصنف والباركود</th>
                            <th style="width: 130px;">الكمية</th>
                            <th style="width: 140px;">سعر الشراء (للوحدة)</th>
                            <th style="width: 110px;">الضريبة %</th>
                            <th style="width: 130px;">رقم الباتش</th>
                            <th style="width: 140px;">تاريخ الصلاحية</th>
                            <th style="width: 140px;">الإجمالي بالضريبة</th>
                            <th style="width: 50px; text-align: center;">حذف</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <!-- Dynamic Rows Injected Here -->
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1rem; text-align: left;">
                <button type="button" class="btn btn-outline btn-sm" onclick="addNewItemRow()">
                    <i class="fas fa-plus"></i> إضافة سطر صنف جديد
                </button>
            </div>
        </div>

        <!-- Totals & Payment Card -->
        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1.5rem;">
            <!-- Notes -->
            <div class="card" style="padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main);">
                    <i class="fas fa-comment-alt" style="color: #a78bfa; margin-left: 0.5rem;"></i>
                    ملاحظات الفاتورة والتوريد
                </h3>
                <textarea name="notes" id="invoiceNotes" rows="4" class="form-control" placeholder="أي ملاحظات خاصة بالتسليم، رقم إذن الاستلام، أو تفاصيل النقل..."></textarea>
            </div>

            <!-- Financial Settlement Box -->
            <div class="card" style="padding: 1.5rem; background: rgba(148, 163, 184, 0.04);">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; color: var(--text-main); border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                    <i class="fas fa-calculator" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                    التسوية المالية وطرق السداد
                </h3>

                <div style="display: flex; justify-content: space-between; margin-bottom: 0.65rem;">
                    <span style="color: var(--text-muted); font-size: 0.9rem;">المجموع الفرعي (بدون ضريبة):</span>
                    <strong id="summarySubtotal" style="font-size: 0.95rem; color: var(--text-main);">0.00 ج.م</strong>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 0.65rem;">
                    <span style="color: var(--text-muted); font-size: 0.9rem;">إجمالي ضريبة القيمة المضافة (14%):</span>
                    <strong id="summaryTax" style="font-size: 0.95rem; color: #38bdf8;">0.00 ج.م</strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.65rem;">
                    <span style="color: var(--text-muted); font-size: 0.9rem;">الخصم المكتسب من المورد:</span>
                    <div style="width: 130px;">
                        <input type="number" step="0.01" min="0" id="discountInput" class="form-control form-control-sm" value="0.00" oninput="calculateTotals()" style="text-align: left; font-weight: 700;">
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 0.75rem; margin-top: 0.75rem;">
                    <strong style="font-size: 1.1rem; color: var(--text-main);">الإجمالي الصافي النهائي:</strong>
                    <strong id="summaryGrandTotal" style="font-size: 1.2rem; color: var(--brand-amber);">0.00 ج.م</strong>
                </div>

                <hr style="border: none; border-top: 1px dashed var(--border-color); margin: 1rem 0;">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div>
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">طريقة السداد</label>
                        <select id="paymentMethodSelect" class="form-control form-control-sm" onchange="onPaymentMethodChange()">
                            <option value="cash">كاش نقدي</option>
                            <option value="bank_transfer">تحويل بنكي</option>
                            <option value="card">بطاقة بنكية</option>
                            <option value="credit">آجل على الحساب</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">المبلغ المدفوع حالياً</label>
                        <input type="number" step="0.01" min="0" id="paidAmountInput" class="form-control form-control-sm" value="0.00" oninput="calculateTotals()" style="font-weight: 700; color: #10b981;">
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; background: rgba(239, 68, 68, 0.08); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm); margin-bottom: 1.25rem;">
                    <span style="font-size: 0.85rem; color: #ef4444; font-weight: 700;">المتبقي آجل (يضاف لمديونية المورد):</span>
                    <strong id="summaryRemaining" style="font-size: 1rem; color: #ef4444;">0.00 ج.م</strong>
                </div>

                <button type="submit" id="submitBtn" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.85rem; font-size: 1rem; background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);">
                    <i class="fas fa-check-circle"></i>
                    اعتماد الفاتورة وتوريد البضاعة للمخزن
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Raw Items Data JSON for dynamic row selection -->
<script>
const availableItems = <?= json_encode(array_map(function($i) {
    return [
        'id'         => (int)$i['id'],
        'name'       => $i['name'],
        'barcode'    => $i['barcode'],
        'cost_price' => (float)$i['cost_price'],
        'unit_name'  => $i['unit_name'] ?? 'قطعة'
    ];
}, $items), JSON_UNESCAPED_UNICODE) ?>;

let rowCount = 0;

function addNewItemRow() {
    rowCount++;
    const tbody = document.getElementById('itemsTableBody');

    let itemOptions = `<option value="">-- اختر صنفاً --</option>`;
    availableItems.forEach(item => {
        itemOptions += `<option value="${item.id}" data-cost="${item.cost_price}" data-barcode="${item.barcode}" data-unit="${item.unit_name}">${item.name} (${item.barcode})</option>`;
    });

    const tr = document.createElement('tr');
    tr.id = `itemRow_${rowCount}`;
    tr.innerHTML = `
        <td>
            <select class="form-control form-control-sm item-select" onchange="onItemSelect(${rowCount})" required>
                ${itemOptions}
            </select>
        </td>
        <td>
            <input type="number" step="0.01" min="0.01" class="form-control form-control-sm item-qty" value="1.00" oninput="updateRowTotal(${rowCount})" required>
        </td>
        <td>
            <input type="number" step="0.01" min="0" class="form-control form-control-sm item-cost" value="0.00" oninput="updateRowTotal(${rowCount})" required>
        </td>
        <td>
            <select class="form-control form-control-sm item-tax" onchange="updateRowTotal(${rowCount})">
                <option value="14" selected>14%</option>
                <option value="0">0% (معفى)</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm item-batch" placeholder="اختياري">
        </td>
        <td>
            <input type="date" class="form-control form-control-sm item-exp">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm item-total" readonly value="0.00" style="background:rgba(148,163,184,0.08); font-weight:700; color:#38bdf8;">
        </td>
        <td style="text-align: center;">
            <button type="button" class="btn btn-sm btn-outline" onclick="removeRow(${rowCount})" style="color:#ef4444; border-color:transparent;">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
}

function onItemSelect(id) {
    const row = document.getElementById(`itemRow_${id}`);
    const select = row.querySelector('.item-select');
    const selectedOption = select.options[select.selectedIndex];
    const cost = parseFloat(selectedOption.getAttribute('data-cost')) || 0.0;

    row.querySelector('.item-cost').value = cost.toFixed(2);
    updateRowTotal(id);
}

function updateRowTotal(id) {
    const row = document.getElementById(`itemRow_${id}`);
    if (!row) return;

    const qty = parseFloat(row.querySelector('.item-qty').value) || 0.0;
    const cost = parseFloat(row.querySelector('.item-cost').value) || 0.0;
    const taxRate = parseFloat(row.querySelector('.item-tax').value) || 0.0;

    const sub = qty * cost;
    const tax = sub * (taxRate / 100);
    const total = sub + tax;

    row.querySelector('.item-total').value = total.toFixed(2);
    calculateTotals();
}

function removeRow(id) {
    const row = document.getElementById(`itemRow_${id}`);
    if (row) {
        row.remove();
        calculateTotals();
    }
}

function calculateTotals() {
    let subtotal = 0.0;
    let totalTax = 0.0;

    const rows = document.querySelectorAll('#itemsTableBody tr');
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0.0;
        const cost = parseFloat(row.querySelector('.item-cost').value) || 0.0;
        const taxRate = parseFloat(row.querySelector('.item-tax').value) || 0.0;

        const lineSub = qty * cost;
        const lineTax = lineSub * (taxRate / 100);

        subtotal += lineSub;
        totalTax += lineTax;
    });

    const discount = parseFloat(document.getElementById('discountInput').value) || 0.0;
    const grandTotal = Math.max(0, subtotal + totalTax - discount);

    document.getElementById('summarySubtotal').textContent = subtotal.toFixed(2) + ' ج.م';
    document.getElementById('summaryTax').textContent = totalTax.toFixed(2) + ' ج.م';
    document.getElementById('summaryGrandTotal').textContent = grandTotal.toFixed(2) + ' ج.م';

    // Payment & Remaining
    const method = document.getElementById('paymentMethodSelect').value;
    let paidInput = document.getElementById('paidAmountInput');
    
    if (method === 'cash' || method === 'bank_transfer' || method === 'card') {
        if (parseFloat(paidInput.value) === 0 || paidInput.dataset.autoFilled === '1') {
            paidInput.value = grandTotal.toFixed(2);
            paidInput.dataset.autoFilled = '1';
        }
    } else if (method === 'credit') {
        if (paidInput.dataset.autoFilled === '1') {
            paidInput.value = '0.00';
            paidInput.dataset.autoFilled = '0';
        }
    }

    const paid = parseFloat(paidInput.value) || 0.0;
    const remaining = Math.max(0, grandTotal - paid);
    document.getElementById('summaryRemaining').textContent = remaining.toFixed(2) + ' ج.م';
}

function onPaymentMethodChange() {
    const method = document.getElementById('paymentMethodSelect').value;
    const paidInput = document.getElementById('paidAmountInput');
    const grandTotal = parseFloat(document.getElementById('summaryGrandTotal').textContent) || 0.0;

    if (method === 'credit') {
        paidInput.value = '0.00';
        paidInput.dataset.autoFilled = '0';
    } else {
        paidInput.value = grandTotal.toFixed(2);
        paidInput.dataset.autoFilled = '1';
    }
    calculateTotals();
}

function onSupplierChange() {
    const sel = document.getElementById('supplierSelect');
    const opt = sel.options[sel.selectedIndex];
    const help = document.getElementById('supplierBalanceHelp');

    if (!opt.value) {
        help.textContent = 'حدد المورد لعرض موقفه المالي الحالي';
        return;
    }

    const bal = Number(opt.getAttribute('data-balance')).toLocaleString();
    const limit = Number(opt.getAttribute('data-limit')).toLocaleString();
    help.innerHTML = `<span style="color:#38bdf8;">الرصيد الدائن الحالي للمورد: ${bal} ج.م</span> | <span style="color:var(--text-muted);">سقف الائتمان: ${limit} ج.م</span>`;
}

// Initialise with 2 empty item rows
document.addEventListener('DOMContentLoaded', () => {
    addNewItemRow();
    addNewItemRow();
});

// Submission
async function submitPurchaseInvoice(e) {
    e.preventDefault();

    const supplierId = document.getElementById('supplierSelect').value;
    if (!supplierId) {
        alert('يُرجى اختيار المورد أولاً.');
        return;
    }

    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (rows.length === 0) {
        alert('يُرجى إضافة بند واحد على الأقل في الفاتورة.');
        return;
    }

    const items = [];
    let hasInvalid = false;

    rows.forEach(row => {
        const itemId = parseInt(row.querySelector('.item-select').value);
        const qty = parseFloat(row.querySelector('.item-qty').value);
        const cost = parseFloat(row.querySelector('.item-cost').value);
        const tax = parseFloat(row.querySelector('.item-tax').value);
        const batch = row.querySelector('.item-batch').value.trim();
        const exp = row.querySelector('.item-exp').value;

        if (!itemId || qty <= 0) {
            hasInvalid = true;
            return;
        }

        items.push({
            item_id: itemId,
            quantity: qty,
            unit_cost: cost,
            tax_percent: tax,
            batch_number: batch,
            expire_date: exp
        });
    });

    if (hasInvalid || items.length === 0) {
        alert('يرجى التأكد من اختيار الصنف وإدخال كمية صحيحة أكبر من الصفر لجميع البنود.');
        return;
    }

    const payload = {
        supplier_id: supplierId,
        store_id: document.getElementById('storeSelect').value,
        invoice_date: document.getElementById('invoiceDate').value,
        supplier_invoice_number: document.getElementById('supplierInvoiceNumber').value,
        discount_amount: parseFloat(document.getElementById('discountInput').value) || 0.0,
        paid_amount: parseFloat(document.getElementById('paidAmountInput').value) || 0.0,
        payment_method: document.getElementById('paymentMethodSelect').value,
        notes: document.getElementById('invoiceNotes').value,
        items: items
    };

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري حفظ الفاتورة وتوريد المخزون...';

    try {
        const res = await apiRequest('<?= BASE_URL ?>purchases.php?action=store', 'POST', payload);
        if (res.success) {
            alert(`✅ ${res.message}`);
            window.location.href = '<?= BASE_URL ?>purchases.php';
        } else {
            alert(`❌ خطأ: ${res.message}`);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> اعتماد الفاتورة وتوريد البضاعة للمخزن';
        }
    } catch (err) {
        console.error(err);
        alert('حدث خطأ أثناء معالجة الطلب.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> اعتماد الفاتورة وتوريد البضاعة للمخزن';
    }
}
</script>
