<?php
/**
 * شاشة إنشاء عرض سعر جديد للعميل
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "إنشاء عرض سعر جديد";
?>

<div class="quotation-create-page">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-file-signature" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                إنشاء عرض سعر جديد للعميل
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                تجهيز عرض سعر رسمي للعميل مع تحديد مدة الصلاحية وإمكانية التحويل المباشر لفاتورة بيع لاحقاً
            </p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>quotations.php" class="btn btn-outline">
                <i class="fas fa-arrow-right"></i>
                العودة لعروض الأسعار
            </a>
        </div>
    </div>

    <form id="quotationForm" onsubmit="submitQuotation(event)">
        <!-- Header Metadata Card -->
        <div class="card" style="margin-bottom: 1.5rem; padding: 1.5rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1.25rem; color: var(--text-main); border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                <i class="fas fa-user" style="color: #38bdf8; margin-left: 0.5rem;"></i>
                بيانات العميل وفترة الصلاحية
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">
                        العميل <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="customer_id" id="customerSelect" class="form-control" required>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>">
                                <?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['phone'] ?? 'بدون هاتف') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">المستودع المصدر</label>
                    <select name="store_id" id="storeSelect" class="form-control">
                        <?php foreach ($stores as $st): ?>
                            <option value="<?= $st['id'] ?>" <?= ($st['id'] == 2) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($st['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">تاريخ العرض</label>
                    <input type="date" name="quotation_date" id="quotationDate" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">صالح حتى تاريخ</label>
                    <input type="date" name="expiry_date" id="expiryDate" class="form-control" value="<?= date('Y-m-d', strtotime('+15 days')) ?>" required>
                </div>
            </div>
        </div>

        <!-- Items Card -->
        <div class="card" style="margin-bottom: 1.5rem; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--text-main);">
                    <i class="fas fa-list-ol" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                    الأصناف والأسعار المقترحة
                </h3>
                <button type="button" class="btn btn-sm btn-outline" onclick="addQuoRow()">
                    <i class="fas fa-plus"></i> إضافة صنف
                </button>
            </div>

            <div class="table-responsive">
                <table class="table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="min-width: 250px;">الصنف والباركود</th>
                            <th style="width: 140px;">الكمية</th>
                            <th style="width: 150px;">سعر الوحدة المقترح</th>
                            <th style="width: 130px;">الضريبة (14%)</th>
                            <th style="width: 160px;">الإجمالي بالضريبة</th>
                            <th style="min-width: 150px;">ملاحظات البند</th>
                            <th style="width: 50px; text-align: center;">حذف</th>
                        </tr>
                    </thead>
                    <tbody id="quoItemsTableBody">
                        <!-- Injected Rows -->
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1rem; text-align: left;">
                <button type="button" class="btn btn-outline btn-sm" onclick="addQuoRow()">
                    <i class="fas fa-plus"></i> إضافة سطر جديد
                </button>
            </div>
        </div>

        <!-- Totals & Notes -->
        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1.5rem;">
            <div class="card" style="padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main);">
                    <i class="fas fa-sticky-note" style="color: #a78bfa; margin-left: 0.5rem;"></i>
                    شروط وملاحظات العرض للعميل
                </h3>
                <textarea id="quoNotes" rows="4" class="form-control" placeholder="شروط التسليم، مدة التوريد، طريقة الدفع، أو أي شروط خاصة تظهر للعميل في عرض السعر..."></textarea>
            </div>

            <div class="card" style="padding: 1.5rem; background: rgba(148, 163, 184, 0.04);">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; color: var(--text-main); border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                    <i class="fas fa-calculator" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                    إجمالي قيمة العرض
                </h3>

                <div style="display: flex; justify-content: space-between; margin-bottom: 0.65rem;">
                    <span style="color: var(--text-muted); font-size: 0.9rem;">المجموع الفرعي:</span>
                    <strong id="quoSubtotal" style="font-size: 0.95rem; color: var(--text-main);">0.00 ج.م</strong>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 0.65rem;">
                    <span style="color: var(--text-muted); font-size: 0.9rem;">ضريبة القيمة المضافة (14%):</span>
                    <strong id="quoTax" style="font-size: 0.95rem; color: #38bdf8;">0.00 ج.م</strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.65rem;">
                    <span style="color: var(--text-muted); font-size: 0.9rem;">خصم خاص للعميل:</span>
                    <div style="width: 130px;">
                        <input type="number" step="0.01" min="0" id="quoDiscount" class="form-control form-control-sm" value="0.00" oninput="calcQuoTotals()" style="font-weight: 700;">
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 0.75rem; margin-top: 0.75rem; margin-bottom: 1.5rem;">
                    <strong style="font-size: 1.1rem; color: var(--text-main);">الإجمالي النهائي للعرض:</strong>
                    <strong id="quoGrandTotal" style="font-size: 1.25rem; color: var(--brand-amber);">0.00 ج.م</strong>
                </div>

                <button type="submit" id="submitQuoBtn" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.85rem; font-size: 1rem; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <i class="fas fa-check-circle"></i>
                    حفظ وإصدار عرض السعر
                </button>
            </div>
        </div>
    </form>
</div>

<script>
const quoItems = <?= json_encode(array_map(function($i) {
    return [
        'id'           => (int)$i['id'],
        'name'         => $i['name'],
        'barcode'      => $i['barcode'],
        'retail_price' => (float)$i['retail_price'],
        'unit_name'    => $i['unit_name'] ?? 'قطعة'
    ];
}, $items), JSON_UNESCAPED_UNICODE) ?>;

let quoRowCount = 0;

function addQuoRow() {
    quoRowCount++;
    const tbody = document.getElementById('quoItemsTableBody');

    let options = `<option value="">-- اختر صنفاً --</option>`;
    quoItems.forEach(i => {
        options += `<option value="${i.id}" data-price="${i.retail_price}">${i.name} (${i.barcode})</option>`;
    });

    const tr = document.createElement('tr');
    tr.id = `quoRow_${quoRowCount}`;
    tr.innerHTML = `
        <td>
            <select class="form-control form-control-sm quo-item-select" onchange="onQuoItemSelect(${quoRowCount})" required>
                ${options}
            </select>
        </td>
        <td>
            <input type="number" step="0.01" min="0.01" class="form-control form-control-sm quo-qty" value="1.00" oninput="updateQuoRowTotal(${quoRowCount})" required>
        </td>
        <td>
            <input type="number" step="0.01" min="0" class="form-control form-control-sm quo-price" value="0.00" oninput="updateQuoRowTotal(${quoRowCount})" required>
        </td>
        <td>
            <select class="form-control form-control-sm quo-tax-rate" onchange="updateQuoRowTotal(${quoRowCount})">
                <option value="14" selected>14%</option>
                <option value="0">0%</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm quo-total" readonly value="0.00" style="background:rgba(148,163,184,0.08); font-weight:700; color:var(--brand-amber);">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm quo-item-notes" placeholder="ملاحظات البند">
        </td>
        <td style="text-align: center;">
            <button type="button" class="btn btn-sm btn-outline" onclick="removeQuoRow(${quoRowCount})" style="color:#ef4444; border-color:transparent;">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
}

function onQuoItemSelect(id) {
    const row = document.getElementById(`quoRow_${id}`);
    const sel = row.querySelector('.quo-item-select');
    const opt = sel.options[sel.selectedIndex];
    const price = parseFloat(opt.getAttribute('data-price')) || 0.0;

    row.querySelector('.quo-price').value = price.toFixed(2);
    updateQuoRowTotal(id);
}

function updateQuoRowTotal(id) {
    const row = document.getElementById(`quoRow_${id}`);
    if (!row) return;

    const qty = parseFloat(row.querySelector('.quo-qty').value) || 0.0;
    const price = parseFloat(row.querySelector('.quo-price').value) || 0.0;
    const taxRate = parseFloat(row.querySelector('.quo-tax-rate').value) || 0.0;

    const sub = qty * price;
    const tax = sub * (taxRate / 100);
    const total = sub + tax;

    row.querySelector('.quo-total').value = total.toFixed(2);
    calcQuoTotals();
}

function removeQuoRow(id) {
    const row = document.getElementById(`quoRow_${id}`);
    if (row) {
        row.remove();
        calcQuoTotals();
    }
}

function calcQuoTotals() {
    let subtotal = 0.0;
    let totalTax = 0.0;

    const rows = document.querySelectorAll('#quoItemsTableBody tr');
    rows.forEach(r => {
        const qty = parseFloat(r.querySelector('.quo-qty').value) || 0.0;
        const price = parseFloat(r.querySelector('.quo-price').value) || 0.0;
        const taxRate = parseFloat(r.querySelector('.quo-tax-rate').value) || 0.0;

        const sub = qty * price;
        const tax = sub * (taxRate / 100);

        subtotal += sub;
        totalTax += tax;
    });

    const discount = parseFloat(document.getElementById('quoDiscount').value) || 0.0;
    const grand = Math.max(0, subtotal + totalTax - discount);

    document.getElementById('quoSubtotal').textContent = subtotal.toFixed(2) + ' ج.م';
    document.getElementById('quoTax').textContent = totalTax.toFixed(2) + ' ج.م';
    document.getElementById('quoGrandTotal').textContent = grand.toFixed(2) + ' ج.م';
}

document.addEventListener('DOMContentLoaded', () => {
    addQuoRow();
    addQuoRow();
});

async function submitQuotation(e) {
    e.preventDefault();

    const rows = document.querySelectorAll('#quoItemsTableBody tr');
    if (rows.length === 0) {
        alert('يُرجى إضافة بند واحد على الأقل في عرض السعر.');
        return;
    }

    const items = [];
    let hasInvalid = false;

    rows.forEach(r => {
        const itemId = parseInt(r.querySelector('.quo-item-select').value);
        const qty = parseFloat(r.querySelector('.quo-qty').value);
        const price = parseFloat(r.querySelector('.quo-price').value);
        const notes = r.querySelector('.quo-item-notes').value.trim();

        if (!itemId || qty <= 0) {
            hasInvalid = true;
            return;
        }

        items.push({
            item_id: itemId,
            quantity: qty,
            unit_price: price,
            notes: notes
        });
    });

    if (hasInvalid || items.length === 0) {
        alert('يرجى التأكد من اختيار الصنف وإدخال كمية صحيحة لجميع البنود.');
        return;
    }

    const payload = {
        customer_id: document.getElementById('customerSelect').value,
        store_id: document.getElementById('storeSelect').value,
        quotation_date: document.getElementById('quotationDate').value,
        expiry_date: document.getElementById('expiryDate').value,
        discount_amount: parseFloat(document.getElementById('quoDiscount').value) || 0.0,
        notes: document.getElementById('quoNotes').value,
        items: items
    };

    const submitBtn = document.getElementById('submitQuoBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري حفظ عرض السعر...';

    try {
        const res = await apiRequest('<?= BASE_URL ?>quotations.php?action=store', 'POST', payload);
        if (res.success) {
            alert(`✅ ${res.message}`);
            window.location.href = '<?= BASE_URL ?>quotations.php';
        } else {
            alert(`❌ خطأ: ${res.message}`);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> حفظ وإصدار عرض السعر';
        }
    } catch (err) {
        console.error(err);
        alert('حدث خطأ في الاتصال بالخادم.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> حفظ وإصدار عرض السعر';
    }
}
</script>
