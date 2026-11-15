<?php
/**
 * شاشة إدارة مردودات المبيعات والمشتريات
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "إدارة مردودات المبيعات والمشتريات";
?>

<div class="returns-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-undo-alt" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                إدارة مردودات المبيعات والمشتريات
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                معالجة إرجاع بضائع العملاء وتوريدها للمخزن، وتسجيل إشعارات خصم مرتجعات الموردين وتسوية الأرصدة
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" onclick="openSaleReturnModal()" style="border-color: #10b981; color: #10b981;">
                <i class="fas fa-undo"></i>
                إصدار مرتجع مبيعات
            </button>
            <button type="button" class="btn btn-primary" onclick="openPurchaseReturnModal()" style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);">
                <i class="fas fa-dolly"></i>
                إصدار مرتجع مشتريات لمورد
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
        <button type="button" class="btn <?= ($activeTab === 'sales') ? 'btn-primary' : 'btn-outline' ?>" onclick="switchTab('sales')" id="tabBtnSales" style="border-radius: var(--radius-md);">
            <i class="fas fa-shopping-cart" style="margin-left: 4px;"></i>
            مرتجعات المبيعات (<?= count($salesReturns) ?>)
        </button>
        <button type="button" class="btn <?= ($activeTab === 'purchases') ? 'btn-primary' : 'btn-outline' ?>" onclick="switchTab('purchases')" id="tabBtnPurchases" style="border-radius: var(--radius-md);">
            <i class="fas fa-truck-loading" style="margin-left: 4px;"></i>
            مرتجعات المشتريات (<?= count($purchaseReturns) ?>)
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
        <form method="GET" action="<?= BASE_URL ?>returns.php" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: end;">
            <input type="hidden" name="tab" id="filterTabInput" value="<?= htmlspecialchars($activeTab) ?>">

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">بحث سريع (رقم الإشعار، الاسم، الهاتف)</label>
                <input type="text" name="search" class="form-control" placeholder="اكتب للبحث..." value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    تصفية
                </button>
                <a href="<?= BASE_URL ?>returns.php?tab=<?= $activeTab ?>" class="btn btn-outline" title="إعادة ضبط">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- TAB 1: Sales Returns -->
    <div id="salesTabContent" style="<?= ($activeTab === 'sales') ? 'display: block;' : 'display: none;' ?>">
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-responsive">
                <table class="table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th>رقم إشعار المرتجع</th>
                            <th>فاتورة البيع الأصلية</th>
                            <th>العميل</th>
                            <th>المستودع المستلم</th>
                            <th>تاريخ المرتجع</th>
                            <th>المبلغ المسترد</th>
                            <th>طريقة الاسترداد</th>
                            <th>سبب الإرجاع</th>
                            <th>الموظف المسؤول</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($salesReturns)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                                    <i class="fas fa-box-open fa-3x" style="opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                                    لا توجد مرتجعات مبيعات مسجلة في هذه الفترة.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($salesReturns as $sr): ?>
                                <tr>
                                    <td>
                                        <span style="font-family: monospace; font-weight: 700; color: #10b981; font-size: 0.95rem;">
                                            <?= htmlspecialchars($sr['return_number']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($sr['original_invoice_number'])): ?>
                                            <span style="font-family: monospace; font-size: 0.85rem; color: #38bdf8;">
                                                <?= htmlspecialchars($sr['original_invoice_number']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: var(--text-muted); font-size: 0.8rem;">مرتجع مباشر</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-main);">
                                            <?= htmlspecialchars($sr['customer_name'] ?? 'عميل نقدي') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: var(--text-main);">
                                            <?= htmlspecialchars($sr['store_name'] ?? 'صالة العرض') ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                                        <?= Helpers::formatDate($sr['return_date']) ?>
                                    </td>
                                    <td style="font-weight: 800; font-size: 1rem; color: #ef4444;">
                                        <?= Helpers::formatCurrency($sr['total_refund']) ?>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                            <?= ($sr['refund_method'] === 'cash') ? 'كاش نقدي' : (($sr['refund_method'] === 'credit_balance') ? 'رصيد دائن بالآجل' : $sr['refund_method']) ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.82rem; color: var(--text-muted);">
                                        <?= htmlspecialchars($sr['reason'] ?: '-') ?>
                                    </td>
                                    <td style="font-size: 0.82rem; color: var(--text-muted);">
                                        <?= htmlspecialchars($sr['created_by_name'] ?? 'المدير') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: Purchase Returns -->
    <div id="purchasesTabContent" style="<?= ($activeTab === 'purchases') ? 'display: block;' : 'display: none;' ?>">
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-responsive">
                <table class="table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th>رقم إشعار المرتجع</th>
                            <th>فاتورة الشراء الأصلية</th>
                            <th>المورد</th>
                            <th>المستودع المصدر</th>
                            <th>تاريخ الإرجاع</th>
                            <th>إجمالي قيمة المرتجع</th>
                            <th>تسوية الحساب</th>
                            <th>سبب الإرجاع</th>
                            <th>المسؤول</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($purchaseReturns)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                                    <i class="fas fa-dolly-flatbed fa-3x" style="opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                                    لا توجد مرتجعات مشتريات مسجلة للموردين في هذه الفترة.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($purchaseReturns as $pr): ?>
                                <tr>
                                    <td>
                                        <span style="font-family: monospace; font-weight: 700; color: #38bdf8; font-size: 0.95rem;">
                                            <?= htmlspecialchars($pr['return_number']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($pr['original_purchase_number'])): ?>
                                            <span style="font-family: monospace; font-size: 0.85rem; color: var(--brand-amber);">
                                                <?= htmlspecialchars($pr['original_purchase_number']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: var(--text-muted); font-size: 0.8rem;">مرتجع مباشر</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-main);">
                                            <?= htmlspecialchars($pr['supplier_name'] ?? 'مورد غير محدد') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: var(--text-main);">
                                            <?= htmlspecialchars($pr['store_name'] ?? 'المستودع الرئيسي') ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                                        <?= Helpers::formatDate($pr['return_date']) ?>
                                    </td>
                                    <td style="font-weight: 800; font-size: 1rem; color: #10b981;">
                                        <?= Helpers::formatCurrency($pr['total_amount']) ?>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                                            <i class="fas fa-check" style="margin-left: 3px;"></i>
                                            <?= ($pr['refund_status'] === 'credited_to_balance') ? 'خصم من مديونية المورد' : 'استرداد نقدي' ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.82rem; color: var(--text-muted);">
                                        <?= htmlspecialchars($pr['reason'] ?: '-') ?>
                                    </td>
                                    <td style="font-size: 0.82rem; color: var(--text-muted);">
                                        <?= htmlspecialchars($pr['created_by_name'] ?? 'المدير') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: New Sale Return (Explicitly Hidden) -->
<div id="saleReturnModal" class="modal-backdrop" style="display: none;">
    <div class="card" style="max-width: 700px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-main);">
                <i class="fas fa-undo" style="color: #10b981; margin-left: 0.5rem;"></i>
                تسجيل مرتجع مبيعات جديد
            </h3>
            <button type="button" onclick="closeSaleReturnModal()" class="btn btn-outline btn-sm" style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form onsubmit="submitSaleReturn(event)">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">العميل</label>
                    <select id="srCustomerSelect" class="form-control">
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">المستودع المستلم</label>
                    <select id="srStoreSelect" class="form-control">
                        <?php foreach ($stores as $st): ?>
                            <option value="<?= $st['id'] ?>" <?= ($st['id'] == 2) ? 'selected' : '' ?>><?= htmlspecialchars($st['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">طريقة الاسترداد المالي</label>
                    <select id="srRefundMethod" class="form-control">
                        <option value="cash">كاش نقدي من الخزينة</option>
                        <option value="credit_balance">إضافة لرصيد العميل (آجل)</option>
                        <option value="card">إرجاع بنكي / فيزا</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">سبب الإرجاع</label>
                    <input type="text" id="srReason" class="form-control" placeholder="مثال: رغبة العميل، عيب تصنيع...">
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <strong style="font-size: 0.95rem; color: var(--text-main);">الأصناف المرتجعة</strong>
                    <button type="button" class="btn btn-sm btn-outline" onclick="addSaleReturnRow()">
                        <i class="fas fa-plus"></i> إضافة صنف
                    </button>
                </div>

                <table class="table" style="font-size: 0.88rem; margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th>الصنف</th>
                            <th style="width: 100px;">الكمية</th>
                            <th style="width: 120px;">سعر الإرجاع</th>
                            <th style="width: 120px;">حالة الصنف</th>
                            <th style="width: 40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="srItemsTbody">
                        <!-- Items -->
                    </tbody>
                </table>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <button type="button" onclick="closeSaleReturnModal()" class="btn btn-outline">إلغاء</button>
                <button type="submit" id="srSubmitBtn" class="btn btn-primary" style="background: #10b981; border-color: #10b981;">
                    <i class="fas fa-check-circle"></i>
                    تأكيد المرتجع واسترجاع المخزون
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: New Purchase Return (Explicitly Hidden) -->
<div id="purchaseReturnModal" class="modal-backdrop" style="display: none;">
    <div class="card" style="max-width: 700px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-main);">
                <i class="fas fa-dolly" style="color: #38bdf8; margin-left: 0.5rem;"></i>
                تسجيل مرتجع مشتريات لمورد
            </h3>
            <button type="button" onclick="closePurchaseReturnModal()" class="btn btn-outline btn-sm" style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form onsubmit="submitPurchaseReturn(event)">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">المورد <span style="color:#ef4444;">*</span></label>
                    <select id="prSupplierSelect" class="form-control" required>
                        <option value="">-- اختر المورد --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">المستودع المصدر</label>
                    <select id="prStoreSelect" class="form-control">
                        <?php foreach ($stores as $st): ?>
                            <option value="<?= $st['id'] ?>" <?= ($st['id'] == 1) ? 'selected' : '' ?>><?= htmlspecialchars($st['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">تسوية القيمة</label>
                    <select id="prRefundStatus" class="form-control">
                        <option value="credited_to_balance">خصم فوري من مديونية المورد</option>
                        <option value="refunded_cash">استرداد نقدي كاش من المورد</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">سبب الإرجاع</label>
                    <input type="text" id="prReason" class="form-control" placeholder="تلف، عدم مطابقة للمواصفات...">
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <strong style="font-size: 0.95rem; color: var(--text-main);">الأصناف المرتجعة للمورد</strong>
                    <button type="button" class="btn btn-sm btn-outline" onclick="addPurchaseReturnRow()">
                        <i class="fas fa-plus"></i> إضافة صنف
                    </button>
                </div>

                <table class="table" style="font-size: 0.88rem; margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th>الصنف</th>
                            <th style="width: 100px;">الكمية</th>
                            <th style="width: 130px;">تكلفة الشراء</th>
                            <th style="width: 40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="prItemsTbody">
                        <!-- Items -->
                    </tbody>
                </table>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <button type="button" onclick="closePurchaseReturnModal()" class="btn btn-outline">إلغاء</button>
                <button type="submit" id="prSubmitBtn" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i>
                    تأكيد المرتجع وخصم المخزون والمديونية
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const retItems = <?= json_encode(array_map(function($i) {
    return [
        'id'           => (int)$i['id'],
        'name'         => $i['name'],
        'barcode'      => $i['barcode'],
        'cost_price'   => (float)$i['cost_price'],
        'retail_price' => (float)$i['retail_price']
    ];
}, $items), JSON_UNESCAPED_UNICODE) ?>;

function switchTab(tab) {
    document.getElementById('filterTabInput').value = tab;
    if (tab === 'sales') {
        document.getElementById('salesTabContent').style.display = 'block';
        document.getElementById('purchasesTabContent').style.display = 'none';
        document.getElementById('tabBtnSales').className = 'btn btn-primary';
        document.getElementById('tabBtnPurchases').className = 'btn btn-outline';
    } else {
        document.getElementById('salesTabContent').style.display = 'none';
        document.getElementById('purchasesTabContent').style.display = 'block';
        document.getElementById('tabBtnSales').className = 'btn btn-outline';
        document.getElementById('tabBtnPurchases').className = 'btn btn-primary';
    }
}

// Modal open/close
function openSaleReturnModal() {
    document.getElementById('saleReturnModal').style.display = 'flex';
    if (document.querySelectorAll('#srItemsTbody tr').length === 0) {
        addSaleReturnRow();
    }
}
function closeSaleReturnModal() {
    document.getElementById('saleReturnModal').style.display = 'none';
}

function openPurchaseReturnModal() {
    document.getElementById('purchaseReturnModal').style.display = 'flex';
    if (document.querySelectorAll('#prItemsTbody tr').length === 0) {
        addPurchaseReturnRow();
    }
}
function closePurchaseReturnModal() {
    document.getElementById('purchaseReturnModal').style.display = 'none';
}

let srCount = 0;
function addSaleReturnRow() {
    srCount++;
    let opts = `<option value="">-- اختر الصنف --</option>`;
    retItems.forEach(i => {
        opts += `<option value="${i.id}" data-price="${i.retail_price}">${i.name} (${i.barcode})</option>`;
    });

    const tr = document.createElement('tr');
    tr.id = `srRow_${srCount}`;
    tr.innerHTML = `
        <td><select class="form-control form-control-sm sr-item-select" onchange="onSrItemChange(${srCount})" required>${opts}</select></td>
        <td><input type="number" step="0.01" min="0.01" class="form-control form-control-sm sr-qty" value="1.00" required></td>
        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm sr-price" value="0.00" required></td>
        <td>
            <select class="form-control form-control-sm sr-cond">
                <option value="good">سليم (يعاد للمخزن)</option>
                <option value="damaged">تالف (لا يعاد للمخزن)</option>
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline" onclick="this.closest('tr').remove()" style="color:#ef4444; border:none;"><i class="fas fa-trash"></i></button>
        </td>
    `;
    document.getElementById('srItemsTbody').appendChild(tr);
}

function onSrItemChange(id) {
    const row = document.getElementById(`srRow_${id}`);
    const sel = row.querySelector('.sr-item-select');
    const opt = sel.options[sel.selectedIndex];
    row.querySelector('.sr-price').value = parseFloat(opt.getAttribute('data-price')) || 0.0;
}

let prCount = 0;
function addPurchaseReturnRow() {
    prCount++;
    let opts = `<option value="">-- اختر الصنف --</option>`;
    retItems.forEach(i => {
        opts += `<option value="${i.id}" data-cost="${i.cost_price}">${i.name} (${i.barcode})</option>`;
    });

    const tr = document.createElement('tr');
    tr.id = `prRow_${prCount}`;
    tr.innerHTML = `
        <td><select class="form-control form-control-sm pr-item-select" onchange="onPrItemChange(${prCount})" required>${opts}</select></td>
        <td><input type="number" step="0.01" min="0.01" class="form-control form-control-sm pr-qty" value="1.00" required></td>
        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm pr-cost" value="0.00" required></td>
        <td>
            <button type="button" class="btn btn-sm btn-outline" onclick="this.closest('tr').remove()" style="color:#ef4444; border:none;"><i class="fas fa-trash"></i></button>
        </td>
    `;
    document.getElementById('prItemsTbody').appendChild(tr);
}

function onPrItemChange(id) {
    const row = document.getElementById(`prRow_${id}`);
    const sel = row.querySelector('.pr-item-select');
    const opt = sel.options[sel.selectedIndex];
    row.querySelector('.pr-cost').value = parseFloat(opt.getAttribute('data-cost')) || 0.0;
}

async function submitSaleReturn(e) {
    e.preventDefault();
    const rows = document.querySelectorAll('#srItemsTbody tr');
    if (rows.length === 0) return alert('يرجى إضافة صنف مرتجع.');

    const items = [];
    rows.forEach(r => {
        const id = parseInt(r.querySelector('.sr-item-select').value);
        const qty = parseFloat(r.querySelector('.sr-qty').value);
        const price = parseFloat(r.querySelector('.sr-price').value);
        const cond = r.querySelector('.sr-cond').value;
        if (id && qty > 0) items.push({ item_id: id, quantity: qty, unit_price: price, condition_state: cond });
    });

    const payload = {
        customer_id: document.getElementById('srCustomerSelect').value,
        store_id: document.getElementById('srStoreSelect').value,
        refund_method: document.getElementById('srRefundMethod').value,
        reason: document.getElementById('srReason').value,
        items: items
    };

    const btn = document.getElementById('srSubmitBtn');
    btn.disabled = true;

    try {
        const res = await apiRequest('<?= BASE_URL ?>returns.php?action=store_sale_return', 'POST', payload);
        if (res.success) {
            alert(`✅ ${res.message}`);
            window.location.href = '<?= BASE_URL ?>returns.php?tab=sales';
        } else {
            alert(`❌ خطأ: ${res.message}`);
            btn.disabled = false;
        }
    } catch (err) {
        console.error(err);
        alert('حدث خطأ أثناء معالجة الطلب');
        btn.disabled = false;
    }
}

async function submitPurchaseReturn(e) {
    e.preventDefault();
    const supId = document.getElementById('prSupplierSelect').value;
    if (!supId) return alert('يرجى اختيار المورد.');

    const rows = document.querySelectorAll('#prItemsTbody tr');
    if (rows.length === 0) return alert('يرجى إضافة صنف مرتجع.');

    const items = [];
    rows.forEach(r => {
        const id = parseInt(r.querySelector('.pr-item-select').value);
        const qty = parseFloat(r.querySelector('.pr-qty').value);
        const cost = parseFloat(r.querySelector('.pr-cost').value);
        if (id && qty > 0) items.push({ item_id: id, quantity: qty, unit_cost: cost });
    });

    const payload = {
        supplier_id: supId,
        store_id: document.getElementById('prStoreSelect').value,
        refund_status: document.getElementById('prRefundStatus').value,
        reason: document.getElementById('prReason').value,
        items: items
    };

    const btn = document.getElementById('prSubmitBtn');
    btn.disabled = true;

    try {
        const res = await apiRequest('<?= BASE_URL ?>returns.php?action=store_purchase_return', 'POST', payload);
        if (res.success) {
            alert(`✅ ${res.message}`);
            window.location.href = '<?= BASE_URL ?>returns.php?tab=purchases';
        } else {
            alert(`❌ خطأ: ${res.message}`);
            btn.disabled = false;
        }
    } catch (err) {
        console.error(err);
        alert('حدث خطأ أثناء معالجة الطلب');
        btn.disabled = false;
    }
}
</script>
