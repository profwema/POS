<?php
/**
 * شاشة أذونات تحويل المخزون بين المستودعات
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 */
$pageTitle = "تحويلات المخزون بين المستودعات";
?>

<div class="transfers-management-page">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-exchange-alt" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                أذونات تحويل المخزون بين المستودعات
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                نقل البضائع والأصناف بين المستودع الرئيسي ونقاط البيع والفروع
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= BASE_URL ?>items.php" class="btn btn-outline">
                <i class="fas fa-boxes"></i>
                قائمة الأصناف
            </a>
            <button type="button" class="btn btn-primary" onclick="openNewTransferModal()">
                <i class="fas fa-plus-circle"></i>
                إذن تحويل جديد
            </button>
        </div>
    </div>

    <!-- Transfers List Card -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 800; color: var(--text-main);">
            سجل أذونات التحويل السابقة
        </div>

        <div class="table-responsive">
            <table class="table-pro data-table">
                <thead>
                    <tr>
                        <th>رقم الإذن</th>
                        <th>من مستودع (المصدر)</th>
                        <th>إلى مستودع (المستلم)</th>
                        <th>عدد الأصناف</th>
                        <th>الحالة</th>
                        <th>المسؤول</th>
                        <th>تاريخ ووقت التحويل</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transfers)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                            <i class="fas fa-dolly" style="font-size: 2.5rem; opacity: 0.4; margin-bottom: 0.5rem;"></i>
                            <p style="margin-bottom: 0.5rem;">لا توجد أذونات تحويل سابقة مسجلة في المنظومة</p>
                            <button type="button" class="btn btn-outline btn-sm" onclick="openNewTransferModal()">
                                <i class="fas fa-plus"></i> إنشاء أول إذن تحويل الآن
                            </button>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($transfers as $tr): ?>
                    <tr>
                        <td style="font-family: var(--font-mono); font-weight: 700; color: var(--brand-amber);">
                            <?= htmlspecialchars($tr['transfer_number']) ?>
                        </td>
                        <td style="font-weight: 700; color: var(--text-main);">
                            <i class="fas fa-arrow-circle-right" style="color: var(--brand-danger); margin-left: 0.35rem;"></i>
                            <?= htmlspecialchars($tr['from_store_name']) ?>
                        </td>
                        <td style="font-weight: 700; color: var(--text-main);">
                            <i class="fas fa-arrow-circle-left" style="color: var(--brand-emerald); margin-left: 0.35rem;"></i>
                            <?= htmlspecialchars($tr['to_store_name']) ?>
                        </td>
                        <td style="color: var(--brand-blue); font-weight: 800;">
                            <?= $tr['total_items'] ?> صنف
                        </td>
                        <td>
                            <span class="badge badge-emerald">معتمد ومنفذ</span>
                        </td>
                        <td style="color: var(--text-main); font-weight: 600;">
                            <?= htmlspecialchars($tr['created_by_name'] ?? 'مدير النظام') ?>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.82rem; font-weight: 600;">
                            <?= date('Y-m-d h:i A', strtotime($tr['created_at'])) ?>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.82rem;">
                            <?= htmlspecialchars($tr['notes'] ?: '---') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL: إذن تحويل جديد ================= -->
<div id="transferModal" class="modal-backdrop" style="display: none;">
    <div class="card modal-dialog" style="max-width: 780px;">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-exchange-alt" style="color: var(--brand-amber);"></i>
                <span>إنشاء إذن تحويل مخزني جديد</span>
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeTransferModal()" title="إغلاق">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="transferForm" method="POST" action="<?= BASE_URL ?>transfers.php">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label required">المستودع المصدر (من)</label>
                    <select name="from_store_id" id="fromStoreSelect" class="form-control" required>
                        <option value="">اختر المستودع المصدر...</option>
                        <?php foreach ($stores as $st): ?>
                            <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label required">المستودع المستلم (إلى)</label>
                    <select name="to_store_id" id="toStoreSelect" class="form-control" required>
                        <option value="">اختر المستودع المستلم...</option>
                        <?php foreach ($stores as $st): ?>
                            <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Items Dynamic Table -->
            <div style="margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label class="form-label required" style="margin: 0;">الأصناف والكميات المطلوب تحويلها</label>
                    <button type="button" class="btn btn-outline btn-sm" onclick="addTransferRow()" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
                        <i class="fas fa-plus"></i> إضافة سطر صنف
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="data-table" style="font-size: 0.85rem;">
                        <thead>
                            <tr>
                                <th style="width: 55%;">الصنف</th>
                                <th style="width: 35%;">الكمية المحولة</th>
                                <th style="width: 10%; text-align: center;">إلغاء</th>
                            </tr>
                        </thead>
                        <tbody id="transferItemsBody">
                            <!-- Injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">ملاحظات التحويل أو سبب النقل</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="مثال: تغذية صالة العرض بالفرع الأول للأصناف سريعة الدوران"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <button type="button" class="btn btn-outline" onclick="closeTransferModal()">إلغاء</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> تأكيد واعتماد التحويل
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const availableItems = <?= json_encode(array_map(function($it) {
    return [
        'id' => $it['id'],
        'name' => $it['name'],
        'barcode' => $it['barcode'],
        'symbol' => $it['unit_symbol']
    ];
}, $items)) ?>;

function openNewTransferModal() {
    document.getElementById('transferForm').reset();
    document.getElementById('transferItemsBody').innerHTML = '';
    addTransferRow(); // Add one initial empty row
    document.getElementById('transferModal').style.display = 'flex';
}

function closeTransferModal() {
    document.getElementById('transferModal').style.display = 'none';
}

function addTransferRow() {
    const tbody = document.getElementById('transferItemsBody');
    const tr = document.createElement('tr');

    let optionsHtml = '<option value="">-- اختر الصنف --</option>';
    availableItems.forEach(it => {
        optionsHtml += `<option value="${it.id}">${it.name} (${it.symbol}) - [${it.barcode}]</option>`;
    });

    tr.innerHTML = `
        <td>
            <select name="item_ids[]" class="form-control" required style="font-size: 0.85rem;">
                ${optionsHtml}
            </select>
        </td>
        <td>
            <input type="number" step="0.01" min="0.01" name="quantities[]" class="form-control" placeholder="1.00" required style="font-weight: 700; color: #38bdf8;">
        </td>
        <td style="text-align: center;">
            <button type="button" class="btn btn-outline btn-sm" onclick="removeTransferRow(this)" style="color: #f87171; border-color: rgba(248,113,113,0.3); padding: 4px 8px;">
                <i class="fas fa-times"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
}

function removeTransferRow(btn) {
    const tbody = document.getElementById('transferItemsBody');
    if (tbody.children.length > 1) {
        btn.closest('tr').remove();
    } else {
        showToast('يجب أن يحتوي الإذن على صنف واحد على الأقل.', 'error');
    }
}
</script>

