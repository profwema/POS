<?php
/**
 * شاشة سندات القبض والصرف المالي
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "سندات القبض والصرف المالي";
?>

<div class="vouchers-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-money-check-alt" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                سندات القبض والصرف المالي (Financial Vouchers)
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                إثبات حركات تحصيل النقدية وسداد المصروفات والمديونيات مع توليد القيود المزدوجة المتزنة تلقائياً
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" onclick="openVoucherModal('receipt')" style="border-color: #10b981; color: #10b981;">
                <i class="fas fa-arrow-circle-down"></i>
                إصدار سند قبض (+)
            </button>
            <button type="button" class="btn btn-primary" onclick="openVoucherModal('payment')" style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);">
                <i class="fas fa-arrow-circle-up"></i>
                إصدار سند صرف (-)
            </button>
        </div>
    </div>

    <!-- Accounting Tabs Nav -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; flex-wrap: wrap;">
        <a href="<?= BASE_URL ?>accounting.php?tab=chart" class="btn btn-outline" style="border-radius: var(--radius-md);">
            <i class="fas fa-sitemap" style="margin-left: 4px;"></i> شجرة الحسابات
        </a>
        <a href="<?= BASE_URL ?>accounting.php?tab=journal" class="btn btn-outline" style="border-radius: var(--radius-md);">
            <i class="fas fa-book" style="margin-left: 4px;"></i> قيود اليومية العامة
        </a>
        <a href="<?= BASE_URL ?>accounting.php?tab=vouchers" class="btn btn-primary" style="border-radius: var(--radius-md);">
            <i class="fas fa-money-check-alt" style="margin-left: 4px;"></i> سندات القبض والصرف
        </a>
        <a href="<?= BASE_URL ?>accounting.php?tab=trial_balance" class="btn btn-outline" style="border-radius: var(--radius-md);">
            <i class="fas fa-balance-scale" style="margin-left: 4px;"></i> ميزان المراجعة
        </a>
        <a href="<?= BASE_URL ?>accounting.php?tab=income_statement" class="btn btn-outline" style="border-radius: var(--radius-md);">
            <i class="fas fa-chart-line" style="margin-left: 4px;"></i> قائمة الدخل
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
        <form method="GET" action="<?= BASE_URL ?>accounting.php" style="display: grid; grid-template-columns: 2fr 1.2fr 1fr 1fr auto; gap: 1rem; align-items: end;">
            <input type="hidden" name="tab" value="vouchers">

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">بحث سريع (رقم السند، اسم المستلم/المسدد، البيان)</label>
                <input type="text" name="search" class="form-control" placeholder="اكتب للبحث..." value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">نوع السند</label>
                <select name="type" class="form-control">
                    <option value="">كافة السندات</option>
                    <option value="receipt" <?= ($vType === 'receipt') ? 'selected' : '' ?>>سندات القبض (تحصيل +)</option>
                    <option value="payment" <?= ($vType === 'payment') ? 'selected' : '' ?>>سندات الصرف (سداد -)</option>
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
                <a href="<?= BASE_URL ?>accounting.php?tab=vouchers" class="btn btn-outline" title="إعادة ضبط">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Vouchers Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 140px;">رقم السند</th>
                        <th>نوع السند</th>
                        <th>تاريخ السند</th>
                        <th>المستلم منه / المدفوع له</th>
                        <th>حساب الخزينة / البنك</th>
                        <th>الحساب المقابل (البيان)</th>
                        <th>قيمة السند</th>
                        <th>طريقة الدفع</th>
                        <th>القيد المحاسبي المتولد</th>
                        <th>المسؤول</th>
                        <th style="text-align: center; width: 80px;">طباعة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vouchers)): ?>
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                                <i class="fas fa-receipt fa-3x" style="opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                                لا توجد سندات قبض أو صرف مسجلة في هذه الفترة.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($vouchers as $v): ?>
                            <tr>
                                <td>
                                    <span style="font-family: monospace; font-weight: 700; color: <?= ($v['voucher_type'] === 'receipt') ? '#10b981' : '#ef4444' ?>; font-size: 0.95rem;">
                                        <?= htmlspecialchars($v['voucher_number']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($v['voucher_type'] === 'receipt'): ?>
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                            <i class="fas fa-arrow-down" style="margin-left: 3px;"></i> سند قبض
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                                            <i class="fas fa-arrow-up" style="margin-left: 3px;"></i> سند صرف
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);">
                                    <?= Helpers::formatDate($v['voucher_date']) ?>
                                </td>
                                <td>
                                    <strong style="color: var(--text-main); font-size: 0.9rem;">
                                        <?= htmlspecialchars($v['received_from_or_paid_to']) ?>
                                    </strong>
                                    <?php if (!empty($v['notes'])): ?>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($v['notes']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-size: 0.85rem; color: var(--text-main);">
                                        <?= htmlspecialchars($v['source_account_code']) ?> - <?= htmlspecialchars($v['source_account_name']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 0.85rem; color: #38bdf8; font-weight: 600;">
                                        <?= htmlspecialchars($v['target_account_code']) ?> - <?= htmlspecialchars($v['target_account_name']) ?>
                                    </span>
                                </td>
                                <td style="font-weight: 800; font-size: 1rem; color: <?= ($v['voucher_type'] === 'receipt') ? '#10b981' : '#ef4444' ?>;">
                                    <?= Helpers::formatCurrency($v['amount']) ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: var(--text-muted);">
                                        <?= htmlspecialchars($v['payment_method']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($v['entry_number'])): ?>
                                        <span style="font-family: monospace; font-size: 0.85rem; color: #38bdf8;">
                                            <?= htmlspecialchars($v['entry_number']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.82rem; color: var(--text-muted);">
                                    <?= htmlspecialchars($v['created_by_name'] ?? 'المدير') ?>
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="btn btn-sm btn-outline" onclick="window.print()" title="طباعة السند">
                                        <i class="fas fa-print"></i>
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

<!-- Modal: New Financial Voucher (Explicitly Hidden) -->
<div id="voucherModal" class="modal-backdrop" style="display: none;">
    <div class="card" style="max-width: 650px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
            <h3 id="voucherModalTitle" style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-main);">
                تحرير سند مالي جديد
            </h3>
            <button type="button" onclick="closeVoucherModal()" class="btn btn-outline btn-sm" style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form onsubmit="submitVoucher(event)">
            <input type="hidden" id="modalVoucherType" value="payment">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">تاريخ السند <span style="color:#ef4444;">*</span></label>
                    <input type="date" id="vDate" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">طريقة السداد / التحصيل</label>
                    <select id="vPaymentMethod" class="form-control">
                        <option value="cash">كاش نقدي</option>
                        <option value="bank_transfer">تحويل بنكي</option>
                        <option value="instapay">إنستاباي InstaPay</option>
                        <option value="card">بطاقة بنكية / فيزا</option>
                        <option value="check">شيك مصرفي</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label id="lblReceivedPaid" class="form-label" style="font-size: 0.85rem; font-weight: 700;">
                    يُصرف إلى السيد / السيدة <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" id="vPerson" class="form-control" placeholder="اسم المستلم أو الجهه..." required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">حساب الصندوق / البنك <span style="color:#ef4444;">*</span></label>
                    <select id="vSourceAccount" class="form-control" required>
                        <?php foreach ($cashAccounts as $acc): ?>
                            <?php if (str_starts_with($acc['account_code'], '1101') || str_starts_with($acc['account_code'], '1102') || str_starts_with($acc['account_code'], '1103')): ?>
                                <option value="<?= $acc['id'] ?>"><?= htmlspecialchars($acc['account_code']) ?> - <?= htmlspecialchars($acc['name']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label id="lblTargetAccount" class="form-label" style="font-size: 0.85rem; font-weight: 700;">
                        الحساب المقابل (الخصم / الإيداع) <span style="color:#ef4444;">*</span>
                    </label>
                    <select id="vTargetAccount" class="form-control select2" required>
                        <option value="">-- اختر الحساب المقابل --</option>
                        <?php foreach ($cashAccounts as $acc): ?>
                            <option value="<?= $acc['id'] ?>">
                                <?= htmlspecialchars($acc['account_code']) ?> - <?= htmlspecialchars($acc['name']) ?> (<?= $acc['type'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">مبلغ السند (ج.م) <span style="color:#ef4444;">*</span></label>
                <input type="number" step="0.01" min="0.01" id="vAmount" class="form-control" placeholder="0.00" required style="font-size: 1.2rem; font-weight: 800; color: var(--brand-amber);">
            </div>

            <div class="form-group">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">البيان والملاحظات</label>
                <textarea id="vNotes" rows="2" class="form-control" placeholder="شرح تفاصيل الدفعة أو الغرض من السند..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 1rem;">
                <button type="button" onclick="closeVoucherModal()" class="btn btn-outline">إلغاء</button>
                <button type="submit" id="vSubmitBtn" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i>
                    اعتماد السند وترحيل القيد
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openVoucherModal(type) {
    document.getElementById('modalVoucherType').value = type;
    const title = document.getElementById('voucherModalTitle');
    const lblPerson = document.getElementById('lblReceivedPaid');
    const lblTarget = document.getElementById('lblTargetAccount');
    const submitBtn = document.getElementById('vSubmitBtn');

    if (type === 'receipt') {
        title.innerHTML = '<i class="fas fa-arrow-circle-down" style="color:#10b981; margin-left:0.5rem;"></i> تحرير سند قبض مالي (+)';
        lblPerson.innerHTML = 'استلمنا من السيد / المنشأة <span style="color:#ef4444;">*</span>';
        lblTarget.innerHTML = 'الحساب الدائن المقابل (عميل / إيراد) <span style="color:#ef4444;">*</span>';
        submitBtn.className = 'btn btn-primary';
        submitBtn.style.background = 'linear-gradient(135deg, #059669 0%, #10b981 100%)';
    } else {
        title.innerHTML = '<i class="fas fa-arrow-circle-up" style="color:#ef4444; margin-left:0.5rem;"></i> تحرير سند صرف مالي (-)';
        lblPerson.innerHTML = 'يُصرف للسيد / المنشأة <span style="color:#ef4444;">*</span>';
        lblTarget.innerHTML = 'الحساب المدين المقابل (مصروف / مورد) <span style="color:#ef4444;">*</span>';
        submitBtn.className = 'btn btn-primary';
        submitBtn.style.background = 'linear-gradient(135deg, #ef4444 0%, #f87171 100%)';
    }

    document.getElementById('voucherModal').style.display = 'flex';
}

function closeVoucherModal() {
    document.getElementById('voucherModal').style.display = 'none';
}

async function submitVoucher(e) {
    e.preventDefault();

    const type = document.getElementById('modalVoucherType').value;
    const amount = parseFloat(document.getElementById('vAmount').value) || 0;
    if (amount <= 0) return alert('يرجى إدخال مبلغ صحيح.');

    const targetAccount = document.getElementById('vTargetAccount').value;
    if (!targetAccount) return alert('يرجى اختيار الحساب المقابل.');

    const payload = {
        voucher_type: type,
        voucher_date: document.getElementById('vDate').value,
        payment_method: document.getElementById('vPaymentMethod').value,
        received_from_or_paid_to: document.getElementById('vPerson').value,
        account_id: document.getElementById('vSourceAccount').value,
        target_account_id: targetAccount,
        amount: amount,
        notes: document.getElementById('vNotes').value
    };

    const btn = document.getElementById('vSubmitBtn');
    btn.disabled = true;

    try {
        const res = await apiRequest('<?= BASE_URL ?>accounting.php?action=store_voucher', 'POST', payload);
        if (res.success) {
            alert(`✅ ${res.message}`);
            window.location.href = '<?= BASE_URL ?>accounting.php?tab=vouchers';
        } else {
            alert(`❌ خطأ: ${res.message}`);
            btn.disabled = false;
        }
    } catch (err) {
        console.error(err);
        alert('حدث خطأ أثناء حفظ السند.');
        btn.disabled = false;
    }
}
</script>
