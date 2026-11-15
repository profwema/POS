<?php
/**
 * شاشة سجل عروض أسعار العملاء
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "عروض أسعار العملاء";
?>

<div class="quotations-list-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-file-signature" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                عروض أسعار العملاء (Price Quotations)
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                إصدار عروض الأسعار، متابعة فترات الصلاحية، وتحويل العرض المعتمد فوراً إلى فاتورة بيع ومخصوم مخزنياً بنقرة زر
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= BASE_URL ?>quotations.php?action=create" class="btn btn-primary" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <i class="fas fa-plus-circle"></i>
                إنشاء عرض سعر جديد
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
        <form method="GET" action="<?= BASE_URL ?>quotations.php" style="display: grid; grid-template-columns: 2fr 1.2fr 1fr 1fr auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">بحث سريع (رقم العرض، اسم العميل، الهاتف)</label>
                <input type="text" name="search" class="form-control" placeholder="QUO-2026... أو اسم العميل" value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">الحالة</label>
                <select name="status" class="form-control">
                    <option value="">كافة الحالات</option>
                    <option value="draft" <?= ($status === 'draft') ? 'selected' : '' ?>>مسودة (Draft)</option>
                    <option value="sent" <?= ($status === 'sent') ? 'selected' : '' ?>>تم الإرسال للعميل</option>
                    <option value="accepted" <?= ($status === 'accepted') ? 'selected' : '' ?>>مقبول من العميل</option>
                    <option value="rejected" <?= ($status === 'rejected') ? 'selected' : '' ?>>مرفوض</option>
                    <option value="converted" <?= ($status === 'converted') ? 'selected' : '' ?>>محول لفاتورة بيع معتمدة</option>
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
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    تصفية
                </button>
                <a href="<?= BASE_URL ?>quotations.php" class="btn btn-outline" title="إعادة ضبط">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Quotations Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th>رقم العرض</th>
                        <th>العميل</th>
                        <th>تاريخ الإصدار</th>
                        <th>صالح حتى</th>
                        <th>المجموع قبل الضريبة</th>
                        <th>الضريبة والخصم</th>
                        <th>الإجمالي الصافي</th>
                        <th>حالة العرض</th>
                        <th>محرر العرض</th>
                        <th style="text-align: center; width: 170px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($quotations)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                                <i class="fas fa-file-signature fa-3x" style="opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                                لا توجد عروض أسعار مسجلة تطابق معايير البحث.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($quotations as $q): ?>
                            <tr>
                                <td>
                                    <span style="font-family: monospace; font-weight: 700; color: #38bdf8; font-size: 0.95rem;">
                                        <?= htmlspecialchars($q['quotation_number']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-main);">
                                        <?= htmlspecialchars($q['customer_name'] ?? 'عميل نقدي') ?>
                                    </div>
                                    <?php if (!empty($q['customer_phone'])): ?>
                                        <small style="color: var(--text-muted);"><?= htmlspecialchars($q['customer_phone']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);">
                                    <?= Helpers::formatDate($q['quotation_date']) ?>
                                </td>
                                <td style="font-size: 0.85rem; color: <?= (strtotime($q['expiry_date']) < time() && $q['status'] !== 'converted') ? '#ef4444' : 'var(--text-muted)' ?>;">
                                    <?= Helpers::formatDate($q['expiry_date']) ?>
                                </td>
                                <td><?= Helpers::formatCurrency($q['subtotal']) ?></td>
                                <td style="font-size: 0.82rem; color: var(--text-muted);">
                                    ضريبة: <?= Helpers::formatCurrency($q['tax_amount']) ?><br>
                                    خصم: <?= Helpers::formatCurrency($q['discount_amount']) ?>
                                </td>
                                <td style="font-weight: 800; font-size: 1rem; color: var(--brand-amber);">
                                    <?= Helpers::formatCurrency($q['grand_total']) ?>
                                </td>
                                <td>
                                    <?php if ($q['status'] === 'converted'): ?>
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                                            <i class="fas fa-check-double" style="margin-left: 3px;"></i> محول لفاتورة
                                        </span>
                                        <?php if (!empty($q['converted_invoice_number'])): ?>
                                            <div style="font-size: 0.72rem; color: #38bdf8; font-family: monospace; margin-top: 2px;">
                                                <?= htmlspecialchars($q['converted_invoice_number']) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php elseif ($q['status'] === 'accepted'): ?>
                                        <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                                            <i class="fas fa-thumbs-up" style="margin-left: 3px;"></i> مقبول من العميل
                                        </span>
                                    <?php elseif ($q['status'] === 'sent'): ?>
                                        <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                                            <i class="fas fa-paper-plane" style="margin-left: 3px;"></i> تم الإرسال
                                        </span>
                                    <?php elseif ($q['status'] === 'rejected'): ?>
                                        <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                                            <i class="fas fa-times-circle" style="margin-left: 3px;"></i> مرفوض
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(148, 163, 184, 0.15); color: var(--text-muted);">
                                            <i class="fas fa-edit" style="margin-left: 3px;"></i> مسودة
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.82rem; color: var(--text-muted);">
                                    <?= htmlspecialchars($q['creator_name'] ?? 'المدير') ?>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 0.4rem; justify-content: center;">
                                        <?php if ($q['status'] !== 'converted'): ?>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="convertQuotationToSale(<?= $q['id'] ?>)" title="تحويل فوري لفاتورة بيع معتمدة" style="background: #10b981; border-color: #10b981; font-size: 0.78rem; padding: 0.35rem 0.6rem;">
                                                <i class="fas fa-receipt"></i> تحويل لفاتورة
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-outline" onclick="viewQuotationDetails(<?= $q['id'] ?>)" title="استعراض بنود العرض">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Quotation Details (Explicitly Hidden) -->
<div id="quotationDetailsModal" class="modal-backdrop" style="display: none;">
    <div class="card" style="max-width: 750px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="stat-icon emerald" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div>
                    <h3 id="modalQuoNumber" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: var(--text-main);">تفاصيل عرض السعر</h3>
                    <div id="modalQuoDates" style="font-size: 0.8rem; color: var(--text-muted);">--</div>
                </div>
            </div>
            <button type="button" onclick="closeQuotationModal()" class="btn btn-outline btn-sm" style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div id="modalQuoBody">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; background: rgba(148, 163, 184, 0.08); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem;">
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">العميل:</span>
                    <strong id="modalQuoCustomer" style="color: var(--text-main);">--</strong>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">الحالة الحالية:</span>
                    <span id="modalQuoStatus" class="badge">--</span>
                </div>
            </div>

            <div class="table-responsive" style="margin-bottom: 1.25rem;">
                <table class="table" style="margin-bottom: 0; font-size: 0.88rem;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصنف</th>
                            <th>الكمية</th>
                            <th>سعر الوحدة</th>
                            <th>الضريبة (14%)</th>
                            <th>الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody id="modalQuoItemsTbody">
                        <!-- Items -->
                    </tbody>
                </table>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <div style="width: 260px; background: rgba(148, 163, 184, 0.08); padding: 1rem; border-radius: var(--radius-md);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem; font-size: 0.85rem;">
                        <span style="color: var(--text-muted);">المجموع الفرعي:</span>
                        <strong id="modalQuoSubtotal">0.00 ج.م</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem; font-size: 0.85rem;">
                        <span style="color: var(--text-muted);">الضريبة (14%):</span>
                        <strong id="modalQuoTax">0.00 ج.م</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem; font-size: 0.85rem;">
                        <span style="color: var(--text-muted);">الخصم:</span>
                        <strong id="modalQuoDiscount">0.00 ج.م</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px dashed var(--border-color); padding-top: 0.5rem; margin-top: 0.5rem; font-size: 1.05rem; color: var(--brand-amber);">
                        <strong>الإجمالي الصافي:</strong>
                        <strong id="modalQuoGrand">0.00 ج.م</strong>
                    </div>
                </div>
            </div>

            <div id="modalQuoConvertArea" style="margin-top: 1.25rem; text-align: left; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <!-- Convert button injected if not converted -->
            </div>
        </div>
    </div>
</div>

<script>
async function viewQuotationDetails(id) {
    const modal = document.getElementById('quotationDetailsModal');
    modal.style.display = 'flex';

    try {
        const res = await apiRequest(`<?= BASE_URL ?>quotations.php?action=view_details&id=${id}`);
        if (!res.success) {
            alert(res.message);
            closeQuotationModal();
            return;
        }

        const q = res.quotation;
        document.getElementById('modalQuoNumber').textContent = `عرض سعر: ${q.quotation_number}`;
        document.getElementById('modalQuoDates').textContent = `تاريخ الإصدار: ${q.quotation_date} | صالح حتى: ${q.expiry_date}`;
        document.getElementById('modalQuoCustomer').textContent = q.customer_name || 'عميل نقدي';
        document.getElementById('modalQuoStatus').textContent = q.status;

        const tbody = document.getElementById('modalQuoItemsTbody');
        tbody.innerHTML = '';
        (q.items || []).forEach((item, idx) => {
            const row = `
                <tr>
                    <td>${idx + 1}</td>
                    <td><strong>${item.item_name}</strong><br><small style="color:var(--text-muted);">${item.barcode}</small></td>
                    <td>${Number(item.quantity).toLocaleString()} ${item.unit_name || ''}</td>
                    <td>${Number(item.unit_price).toFixed(2)} ج.م</td>
                    <td>${Number(item.tax_amount).toFixed(2)} ج.م</td>
                    <td style="font-weight:700; color:var(--brand-amber);">${Number(item.total_price).toFixed(2)} ج.م</td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });

        document.getElementById('modalQuoSubtotal').textContent = Number(q.subtotal).toFixed(2) + ' ج.م';
        document.getElementById('modalQuoTax').textContent = Number(q.tax_amount).toFixed(2) + ' ج.م';
        document.getElementById('modalQuoDiscount').textContent = Number(q.discount_amount).toFixed(2) + ' ج.م';
        document.getElementById('modalQuoGrand').textContent = Number(q.grand_total).toFixed(2) + ' ج.م';

        const convertArea = document.getElementById('modalQuoConvertArea');
        if (q.status !== 'converted') {
            convertArea.innerHTML = `
                <button type="button" class="btn btn-primary" onclick="convertQuotationToSale(${q.id})" style="background:#10b981; border-color:#10b981;">
                    <i class="fas fa-check-circle"></i> تحويل فوري لفاتورة بيع معتمدة
                </button>
            `;
        } else {
            convertArea.innerHTML = `
                <div style="color:#10b981; font-weight:700;">
                    <i class="fas fa-check-double"></i> تم تحويل هذا العرض إلى الفاتورة رقم: ${q.converted_invoice_number}
                </div>
            `;
        }
    } catch (err) {
        console.error(err);
        alert('حدث خطأ أثناء تحميل بيانات عرض السعر');
        closeQuotationModal();
    }
}

function closeQuotationModal() {
    document.getElementById('quotationDetailsModal').style.display = 'none';
}

async function convertQuotationToSale(id) {
    if (!confirm('هل تريد تحويل عرض السعر إلى فاتورة بيع معتمدة وخصم البضاعة من المخزن الآن؟')) {
        return;
    }

    try {
        const res = await apiRequest('<?= BASE_URL ?>quotations.php?action=convert', 'POST', { id: id, store_id: 2 });
        if (res.success) {
            alert(`✅ ${res.message}`);
            window.location.href = '<?= BASE_URL ?>sales.php';
        } else {
            alert(`❌ فشل التحويل: ${res.message}`);
        }
    } catch (err) {
        console.error(err);
        alert('حدث خطأ في الاتصال بالخادم أثناء التحويل.');
    }
}
</script>
