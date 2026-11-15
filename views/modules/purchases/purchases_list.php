<?php
/**
 * شاشة سجل فواتير المشتريات والتوريد
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "سجل فواتير المشتريات والتوريد";
?>

<div class="purchases-list-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-truck-loading" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                سجل فواتير المشتريات والتوريد
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                إدارة أوامر الشراء، فواتير الموردين، متابعة مديونيات التوريد، وتحديث متوسط تكلفة الأصناف
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= BASE_URL ?>purchases.php?action=create" class="btn btn-primary" style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);">
                <i class="fas fa-plus-circle"></i>
                تسجيل فاتورة مشتريات جديدة
            </a>
        </div>
    </div>

    <!-- Purchases Metrics Grid -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <div class="stat-val"><?= number_format($stats['total_count'] ?? 0) ?></div>
                <div class="stat-title">عدد فواتير المشتريات</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon amber">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($stats['total_amount'] ?? 0) ?></div>
                <div class="stat-title">إجمالي المشتريات</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon emerald">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($stats['total_paid'] ?? 0) ?></div>
                <div class="stat-title">المبالغ المسددة نقداً وبنكياً</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($stats['total_remaining'] ?? 0) ?></div>
                <div class="stat-title">متبقي آجل (مديونية للموردين)</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
        <form method="GET" action="<?= BASE_URL ?>purchases.php" style="display: grid; grid-template-columns: 1.5fr 1.2fr 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">بحث سريع (رقم الفاتورة، هاتف المورد)</label>
                <input type="text" name="search" class="form-control" placeholder="PUR-202611... أو اسم المورد" value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">المورد</label>
                <select name="supplier_id" class="form-control select2">
                    <option value="">جميع الموردين</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['id'] ?>" <?= ($supplierId == $sup['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sup['name']) ?> (<?= htmlspecialchars($sup['phone']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">حالة السداد</label>
                <select name="payment_status" class="form-control">
                    <option value="">كافة الحالات</option>
                    <option value="paid" <?= ($paymentStatus === 'paid') ? 'selected' : '' ?>>مسددة بالكامل</option>
                    <option value="partial" <?= ($paymentStatus === 'partial') ? 'selected' : '' ?>>سداد جزئي</option>
                    <option value="unpaid" <?= ($paymentStatus === 'unpaid') ? 'selected' : '' ?>>غير مسددة (آجلة)</option>
                </select>
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
                <button type="submit" class="btn btn-primary" title="تصفية النتائج">
                    <i class="fas fa-filter"></i>
                    تصفية
                </button>
                <a href="<?= BASE_URL ?>purchases.php" class="btn btn-outline" title="إعادة ضبط">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Purchases Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 140px;">رقم الفاتورة</th>
                        <th>المورد</th>
                        <th>المستودع المستلم</th>
                        <th>تاريخ الفاتورة</th>
                        <th>الإجمالي</th>
                        <th>المدفوع</th>
                        <th>المتبقي</th>
                        <th>حالة السداد</th>
                        <th>طريقة الدفع</th>
                        <th>المسؤول</th>
                        <th style="text-align: center; width: 100px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($purchases)): ?>
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                                <i class="fas fa-inbox fa-3x" style="opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                                لا توجد فواتير مشتريات مسجلة مطابقة لمعايير البحث الحالية.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($purchases as $p): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: #38bdf8; font-family: monospace; font-size: 0.95rem;">
                                        <?= htmlspecialchars($p['invoice_number']) ?>
                                    </div>
                                    <?php if (!empty($p['supplier_invoice_number'])): ?>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);" title="رقم فاتورة المورد الدفترية">
                                            دفتر: <?= htmlspecialchars($p['supplier_invoice_number']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-main);">
                                        <?= htmlspecialchars($p['supplier_name'] ?? 'مورد غير محدد') ?>
                                    </div>
                                    <?php if (!empty($p['company_name'])): ?>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                                            <?= htmlspecialchars($p['company_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.15); color: var(--text-main);">
                                        <i class="fas fa-warehouse" style="margin-left: 4px;"></i>
                                        <?= htmlspecialchars($p['store_name'] ?? 'المستودع الرئيسي') ?>
                                    </span>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);">
                                    <?= Helpers::formatDate($p['invoice_date']) ?>
                                </td>
                                <td style="font-weight: 800; color: var(--text-main);">
                                    <?= Helpers::formatCurrency($p['grand_total']) ?>
                                </td>
                                <td style="font-weight: 700; color: #10b981;">
                                    <?= Helpers::formatCurrency($p['paid_amount']) ?>
                                </td>
                                <td style="font-weight: 700; color: <?= ((float)$p['remaining_amount'] > 0) ? '#ef4444' : 'var(--text-muted)' ?>;">
                                    <?= Helpers::formatCurrency($p['remaining_amount']) ?>
                                </td>
                                <td>
                                    <?php if ($p['payment_status'] === 'paid'): ?>
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                                            <i class="fas fa-check-circle" style="margin-left: 3px;"></i> مسددة بالكامل
                                        </span>
                                    <?php elseif ($p['payment_status'] === 'partial'): ?>
                                        <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
                                            <i class="fas fa-clock" style="margin-left: 3px;"></i> سداد جزئي
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);">
                                            <i class="fas fa-exclamation-circle" style="margin-left: 3px;"></i> غير مسددة (آجل)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: var(--text-muted);">
                                        <?php
                                        $methods = [
                                            'cash'          => 'كاش نقدي',
                                            'card'          => 'بطاقة بنكية',
                                            'bank_transfer' => 'تحويل بنكي',
                                            'credit'        => 'آجل على الحساب'
                                        ];
                                        echo $methods[$p['payment_method']] ?? $p['payment_method'];
                                        ?>
                                    </span>
                                </td>
                                <td style="font-size: 0.82rem; color: var(--text-muted);">
                                    <?= htmlspecialchars($p['created_by_name'] ?? 'المدير') ?>
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="btn btn-sm btn-outline" onclick="viewPurchaseDetails(<?= $p['id'] ?>)" title="استعراض بنود الفاتورة">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Purchase Details Modal (Explicitly Hidden) -->
<div id="purchaseDetailsModal" class="modal-backdrop" style="display: none;">
    <div class="card" style="max-width: 800px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 1.5rem; position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="stat-icon blue" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <h3 id="modalInvNumber" style="margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--text-main);">تفاصيل فاتورة المشتريات</h3>
                    <div id="modalInvDate" style="font-size: 0.8rem; color: var(--text-muted);">تاريخ الفاتورة: --</div>
                </div>
            </div>
            <button type="button" onclick="closePurchaseModal()" class="btn btn-outline btn-sm" style="border-radius: 50%; width: 34px; height: 34px; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div id="modalContentLoading" style="text-align: center; padding: 2rem;">
            <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--brand-amber);"></i>
            <p style="margin-top: 0.5rem; color: var(--text-muted);">جاري تحميل البنود والبيانات...</p>
        </div>

        <div id="modalContentBody" style="display: none;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; background: rgba(148, 163, 184, 0.08); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem;">
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">اسم المورد:</span>
                    <strong id="modalSupName" style="color: var(--text-main); font-size: 0.95rem;">--</strong>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">المستودع المستلم:</span>
                    <strong id="modalStoreName" style="color: var(--text-main); font-size: 0.95rem;">--</strong>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">حالة السداد:</span>
                    <span id="modalPayStatus" class="badge">--</span>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">طريقة الدفع:</span>
                    <strong id="modalPayMethod" style="color: var(--text-main); font-size: 0.95rem;">--</strong>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive" style="margin-bottom: 1.25rem;">
                <table class="table" style="margin-bottom: 0; font-size: 0.88rem;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصنف</th>
                            <th>الكمية الموردة</th>
                            <th>تكلفة الوحدة</th>
                            <th>الضريبة (14%)</th>
                            <th>إجمالي البند</th>
                            <th>الباتش / الصلاحية</th>
                        </tr>
                    </thead>
                    <tbody id="modalItemsTbody">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>

            <!-- Invoice Totals Summary -->
            <div style="display: flex; justify-content: flex-end;">
                <div style="width: 280px; background: rgba(148, 163, 184, 0.08); padding: 1rem; border-radius: var(--radius-md);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem; font-size: 0.88rem;">
                        <span style="color: var(--text-muted);">المجموع الفرعي:</span>
                        <strong id="modalSubtotal">0.00 ج.م</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem; font-size: 0.88rem;">
                        <span style="color: var(--text-muted);">ضريبة القيمة المضافة:</span>
                        <strong id="modalTax">0.00 ج.م</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem; font-size: 0.88rem;">
                        <span style="color: var(--text-muted);">الخصم:</span>
                        <strong id="modalDiscount">0.00 ج.م</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px dashed var(--border-color); padding-top: 0.5rem; margin-top: 0.5rem; font-size: 1rem; color: var(--brand-amber);">
                        <strong>الإجمالي الكلي:</strong>
                        <strong id="modalGrandTotal">0.00 ج.م</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 0.35rem; font-size: 0.88rem; color: #10b981;">
                        <span>المدفوع:</span>
                        <strong id="modalPaid">0.00 ج.م</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 0.35rem; font-size: 0.88rem; color: #ef4444;">
                        <span>المتبقي:</span>
                        <strong id="modalRemaining">0.00 ج.م</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function viewPurchaseDetails(id) {
    const modal = document.getElementById('purchaseDetailsModal');
    const loading = document.getElementById('modalContentLoading');
    const body = document.getElementById('modalContentBody');

    modal.style.display = 'flex';
    loading.style.display = 'block';
    body.style.display = 'none';

    try {
        const res = await apiRequest(`<?= BASE_URL ?>purchases.php?action=view_details&id=${id}`);
        if (!res.success) {
            alert(res.message || 'تعذر تحميل بيانات الفاتورة');
            closePurchaseModal();
            return;
        }

        const p = res.purchase;
        document.getElementById('modalInvNumber').textContent = `فاتورة توريد: ${p.invoice_number}`;
        document.getElementById('modalInvDate').textContent = `التاريخ: ${p.invoice_date} | رقم الدفتر: ${p.supplier_invoice_number || 'غير مسجل'}`;
        document.getElementById('modalSupName').textContent = p.supplier_name;
        document.getElementById('modalStoreName').textContent = p.store_name || 'المستودع الرئيسي';
        document.getElementById('modalPayStatus').textContent = p.payment_status === 'paid' ? 'مسددة بالكامل' : (p.payment_status === 'partial' ? 'سداد جزئي' : 'آجلة غير مسددة');
        document.getElementById('modalPayMethod').textContent = p.payment_method;

        // Items
        const tbody = document.getElementById('modalItemsTbody');
        tbody.innerHTML = '';
        (p.items || []).forEach((item, idx) => {
            const exp = item.expire_date ? `<br><small style="color:#f59e0b;">صلاحية: ${item.expire_date}</small>` : '';
            const batch = item.batch_number ? `<span class="badge" style="font-size:0.7rem;">${item.batch_number}</span>` : '-';
            const row = `
                <tr>
                    <td>${idx + 1}</td>
                    <td><strong>${item.item_name}</strong><br><small style="color:var(--text-muted);">${item.barcode}</small></td>
                    <td><strong>${Number(item.quantity).toLocaleString()}</strong> ${item.unit_name || ''}</td>
                    <td>${Number(item.unit_cost).toFixed(2)} ج.م</td>
                    <td>${Number(item.tax_amount).toFixed(2)} ج.م</td>
                    <td style="font-weight:700; color:#38bdf8;">${Number(item.total_cost).toFixed(2)} ج.م</td>
                    <td>${batch} ${exp}</td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });

        document.getElementById('modalSubtotal').textContent = Number(p.subtotal).toFixed(2) + ' ج.م';
        document.getElementById('modalTax').textContent = Number(p.tax_amount).toFixed(2) + ' ج.م';
        document.getElementById('modalDiscount').textContent = Number(p.discount_amount).toFixed(2) + ' ج.م';
        document.getElementById('modalGrandTotal').textContent = Number(p.grand_total).toFixed(2) + ' ج.م';
        document.getElementById('modalPaid').textContent = Number(p.paid_amount).toFixed(2) + ' ج.م';
        document.getElementById('modalRemaining').textContent = Number(p.remaining_amount).toFixed(2) + ' ج.م';

        loading.style.display = 'none';
        body.style.display = 'block';
    } catch (err) {
        console.error(err);
        alert('حدث خطأ أثناء جلب تفاصيل الفاتورة');
        closePurchaseModal();
    }
}

function closePurchaseModal() {
    document.getElementById('purchaseDetailsModal').style.display = 'none';
}
</script>
