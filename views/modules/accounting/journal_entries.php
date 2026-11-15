<?php
/**
 * شاشة قيود اليومية العامة المزدوجة
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "سجل قيود اليومية العامة";
?>

<div class="journal-entries-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-book" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                سجل قيود اليومية العامة (General Journal)
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                القيود المحاسبية المزدوجة المتزنة، القيود اليدوية، والقيود المولدة آلياً من المبيعات والمشتريات والسندات
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-primary" onclick="openNewJournalModal()" style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);">
                <i class="fas fa-plus-circle"></i>
                تسجيل قيد محاسبي يدوي
            </button>
        </div>
    </div>

    <!-- Accounting Tabs Nav -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; flex-wrap: wrap;">
        <a href="<?= BASE_URL ?>accounting.php?tab=chart" class="btn btn-outline" style="border-radius: var(--radius-md);">
            <i class="fas fa-sitemap" style="margin-left: 4px;"></i> شجرة الحسابات
        </a>
        <a href="<?= BASE_URL ?>accounting.php?tab=journal" class="btn btn-primary" style="border-radius: var(--radius-md);">
            <i class="fas fa-book" style="margin-left: 4px;"></i> قيود اليومية العامة
        </a>
        <a href="<?= BASE_URL ?>accounting.php?tab=vouchers" class="btn btn-outline" style="border-radius: var(--radius-md);">
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
            <input type="hidden" name="tab" value="journal">

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">بحث سريع (رقم القيد، المرجع، البيان)</label>
                <input type="text" name="search" class="form-control" placeholder="JV-2026... أو نص البيان" value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">مصدر القيد</label>
                <select name="source_type" class="form-control">
                    <option value="">جميع المصادر</option>
                    <option value="manual" <?= ($sourceType === 'manual') ? 'selected' : '' ?>>قيد يدوي</option>
                    <option value="pos_sale" <?= ($sourceType === 'pos_sale') ? 'selected' : '' ?>>مبيعات كاشير</option>
                    <option value="purchase" <?= ($sourceType === 'purchase') ? 'selected' : '' ?>>فاتورة مشتريات</option>
                    <option value="payment_voucher" <?= ($sourceType === 'payment_voucher') ? 'selected' : '' ?>>سند صرف</option>
                    <option value="receipt_voucher" <?= ($sourceType === 'receipt_voucher') ? 'selected' : '' ?>>سند قبض</option>
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
                <a href="<?= BASE_URL ?>accounting.php?tab=journal" class="btn btn-outline" title="إعادة ضبط">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Journal Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 140px;">رقم القيد</th>
                        <th>تاريخ القيد</th>
                        <th>المرجع المستندي</th>
                        <th>البيان والشرح العام</th>
                        <th>إجمالي المدين</th>
                        <th>إجمالي الدائن</th>
                        <th>الاتزان والترحيل</th>
                        <th>مصدر القيد</th>
                        <th>المسؤول</th>
                        <th style="text-align: center; width: 100px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($entries)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                                <i class="fas fa-book-open fa-3x" style="opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                                لا توجد قيود يومية مسجلة في هذه الفترة.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($entries as $e): ?>
                            <tr>
                                <td>
                                    <span style="font-family: monospace; font-weight: 700; color: #38bdf8; font-size: 0.95rem;">
                                        <?= htmlspecialchars($e['entry_number']) ?>
                                    </span>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);">
                                    <?= Helpers::formatDate($e['entry_date']) ?>
                                </td>
                                <td>
                                    <?php if (!empty($e['doc_reference'])): ?>
                                        <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: var(--text-main); font-family: monospace;">
                                            <?= htmlspecialchars($e['doc_reference']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-main); font-size: 0.9rem;">
                                        <?= htmlspecialchars($e['description']) ?>
                                    </div>
                                    <small style="color: var(--text-muted); font-size: 0.72rem;">
                                        <?= (int)$e['lines_count'] ?> أطراف قيد
                                    </small>
                                </td>
                                <td style="font-weight: 700; color: #38bdf8;">
                                    <?= Helpers::formatCurrency($e['total_debit']) ?>
                                </td>
                                <td style="font-weight: 700; color: #10b981;">
                                    <?= Helpers::formatCurrency($e['total_credit']) ?>
                                </td>
                                <td>
                                    <?php if ($e['is_balanced'] && $e['is_posted']): ?>
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 0.72rem;">
                                            <i class="fas fa-check-double"></i> متزن ومرحل
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; font-size: 0.72rem;">
                                            غير متزن
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: var(--text-muted); font-size: 0.72rem;">
                                        <?php
                                        $sources = [
                                            'manual'          => 'قيد يدوي',
                                            'pos_sale'        => 'مبيعات كاشير',
                                            'purchase'        => 'مشتريات',
                                            'payment_voucher' => 'سند صرف',
                                            'receipt_voucher' => 'سند قبض'
                                        ];
                                        echo $sources[$e['source_type']] ?? $e['source_type'];
                                        ?>
                                    </span>
                                </td>
                                <td style="font-size: 0.82rem; color: var(--text-muted);">
                                    <?= htmlspecialchars($e['creator_name'] ?? 'المدير') ?>
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="btn btn-sm btn-outline" onclick="viewJournalDetails(<?= $e['id'] ?>)" title="استعراض أطراف القيد">
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

<!-- Modal: New Manual Journal Entry (Explicitly Hidden) -->
<div id="newJournalModal" class="modal-backdrop" style="display: none;">
    <div class="card" style="max-width: 800px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-main);">
                <i class="fas fa-pen-nib" style="color: #38bdf8; margin-left: 0.5rem;"></i>
                تسجيل قيد يومية محاسبي مزدوج
            </h3>
            <button type="button" onclick="closeNewJournalModal()" class="btn btn-outline btn-sm" style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form onsubmit="submitJournalEntry(event)">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">تاريخ القيد <span style="color:#ef4444;">*</span></label>
                    <input type="date" id="jeDate" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">المرجع المستندي / رقم الوثيقة</label>
                    <input type="text" id="jeDocRef" class="form-control" placeholder="مثال: فاتورة رقم 804 أو إذن تحويل">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">البيان والشرح العام للقيد <span style="color:#ef4444;">*</span></label>
                <input type="text" id="jeDesc" class="form-control" placeholder="شرح موجز لسبب القيد المحاسبي..." required>
            </div>

            <!-- Lines Repeater -->
            <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <strong style="font-size: 0.95rem; color: var(--text-main);">أطراف القيد (المدين والدائن)</strong>
                    <button type="button" class="btn btn-sm btn-outline" onclick="addJeRow()">
                        <i class="fas fa-plus"></i> إضافة طرف
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table" style="font-size: 0.88rem; margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th style="min-width: 250px;">الحساب المحاسبي</th>
                                <th style="width: 140px; color: #38bdf8;">مدين (Debit)</th>
                                <th style="width: 140px; color: #10b981;">دائن (Credit)</th>
                                <th style="min-width: 150px;">شرح السطر</th>
                                <th style="width: 40px; text-align: center;"></th>
                            </tr>
                        </thead>
                        <tbody id="jeLinesTbody">
                            <!-- Injected Lines -->
                        </tbody>
                        <tfoot>
                            <tr style="background: rgba(148, 163, 184, 0.08); font-weight: 800;">
                                <td style="text-align: left;">الإجمالي:</td>
                                <td id="jeTotalDebit" style="color: #38bdf8;">0.00 ج.م</td>
                                <td id="jeTotalCredit" style="color: #10b981;">0.00 ج.م</td>
                                <td colspan="2" id="jeBalanceStatus" style="font-size: 0.85rem;">
                                    <span style="color: #ef4444;">غير متزن! الفارق: 0.00 ج.م</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <button type="button" onclick="closeNewJournalModal()" class="btn btn-outline">إلغاء</button>
                <button type="submit" id="jeSubmitBtn" class="btn btn-primary" disabled style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);">
                    <i class="fas fa-check-circle"></i>
                    حفظ وترحيل القيد
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: View Journal Details (Explicitly Hidden) -->
<div id="journalDetailsModal" class="modal-backdrop" style="display: none;">
    <div class="card" style="max-width: 750px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="stat-icon blue" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <h3 id="viewJeNumber" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: var(--text-main);">تفاصيل القيد المحاسبي</h3>
                    <div id="viewJeMeta" style="font-size: 0.8rem; color: var(--text-muted);">--</div>
                </div>
            </div>
            <button type="button" onclick="closeViewJournalModal()" class="btn btn-outline btn-sm" style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div style="background: rgba(148, 163, 184, 0.08); padding: 0.85rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem;">
            <strong style="color: var(--text-muted); font-size: 0.82rem; display: block;">البيان العام:</strong>
            <div id="viewJeDesc" style="color: var(--text-main); font-weight: 600; font-size: 0.95rem;">--</div>
        </div>

        <div class="table-responsive">
            <table class="table" style="font-size: 0.88rem; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>كود واسم الحساب</th>
                        <th style="width: 140px; color: #38bdf8;">مدين</th>
                        <th style="width: 140px; color: #10b981;">دائن</th>
                        <th>البيان التحليلي للطرف</th>
                    </tr>
                </thead>
                <tbody id="viewJeItemsTbody">
                    <!-- Lines -->
                </tbody>
                <tfoot>
                    <tr style="background: rgba(148, 163, 184, 0.08); font-weight: 800;">
                        <td colspan="2" style="text-align: left;">الإجمالي المتزن:</td>
                        <td id="viewJeTotDebit" style="color: #38bdf8;">--</td>
                        <td id="viewJeTotCredit" style="color: #10b981;">--</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
const postingAccounts = <?= json_encode(array_map(function($a) {
    return [
        'id'           => (int)$a['id'],
        'account_code' => $a['account_code'],
        'name'         => $a['name'],
        'nature'       => $a['nature']
    ];
}, $postingAccounts), JSON_UNESCAPED_UNICODE) ?>;

let jeRowCount = 0;

function openNewJournalModal() {
    document.getElementById('newJournalModal').style.display = 'flex';
    if (document.querySelectorAll('#jeLinesTbody tr').length === 0) {
        addJeRow();
        addJeRow();
    }
}
function closeNewJournalModal() {
    document.getElementById('newJournalModal').style.display = 'none';
}

function addJeRow() {
    jeRowCount++;
    let opts = `<option value="">-- اختر الحساب --</option>`;
    postingAccounts.forEach(a => {
        opts += `<option value="${a.id}">${a.account_code} - ${a.name}</option>`;
    });

    const tr = document.createElement('tr');
    tr.id = `jeRow_${jeRowCount}`;
    tr.innerHTML = `
        <td><select class="form-control form-control-sm je-acc-select" required>${opts}</select></td>
        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm je-debit" value="0.00" oninput="onDebitInput(${jeRowCount})" style="color:#38bdf8; font-weight:700;"></td>
        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm je-credit" value="0.00" oninput="onCreditInput(${jeRowCount})" style="color:#10b981; font-weight:700;"></td>
        <td><input type="text" class="form-control form-control-sm je-line-desc" placeholder="شرح اختياري"></td>
        <td style="text-align: center;">
            <button type="button" class="btn btn-sm btn-outline" onclick="this.closest('tr').remove(); updateJeBalance();" style="color:#ef4444; border:none;"><i class="fas fa-trash"></i></button>
        </td>
    `;
    document.getElementById('jeLinesTbody').appendChild(tr);
}

function onDebitInput(id) {
    const row = document.getElementById(`jeRow_${id}`);
    const deb = parseFloat(row.querySelector('.je-debit').value) || 0;
    if (deb > 0) row.querySelector('.je-credit').value = '0.00';
    updateJeBalance();
}

function onCreditInput(id) {
    const row = document.getElementById(`jeRow_${id}`);
    const cred = parseFloat(row.querySelector('.je-credit').value) || 0;
    if (cred > 0) row.querySelector('.je-debit').value = '0.00';
    updateJeBalance();
}

function updateJeBalance() {
    let totDebit = 0.0;
    let totCredit = 0.0;

    const rows = document.querySelectorAll('#jeLinesTbody tr');
    rows.forEach(r => {
        totDebit += parseFloat(r.querySelector('.je-debit').value) || 0;
        totCredit += parseFloat(r.querySelector('.je-credit').value) || 0;
    });

    document.getElementById('jeTotalDebit').textContent = totDebit.toFixed(2) + ' ج.م';
    document.getElementById('jeTotalCredit').textContent = totCredit.toFixed(2) + ' ج.م';

    const diff = Math.abs(totDebit - totCredit);
    const statusEl = document.getElementById('jeBalanceStatus');
    const submitBtn = document.getElementById('jeSubmitBtn');

    if (totDebit > 0 && diff < 0.01) {
        statusEl.innerHTML = `<span style="color:#10b981; font-weight:700;"><i class="fas fa-check-circle"></i> القيد متزن محاسبياً</span>`;
        submitBtn.disabled = false;
    } else {
        statusEl.innerHTML = `<span style="color:#ef4444; font-weight:700;"><i class="fas fa-exclamation-triangle"></i> غير متزن! الفارق: ${diff.toFixed(2)} ج.م</span>`;
        submitBtn.disabled = true;
    }
}

async function submitJournalEntry(e) {
    e.preventDefault();
    const rows = document.querySelectorAll('#jeLinesTbody tr');
    if (rows.length < 2) return alert('يجب إضافة طرفين على الأقل للقيد.');

    const items = [];
    rows.forEach(r => {
        const accId = parseInt(r.querySelector('.je-acc-select').value);
        const deb = parseFloat(r.querySelector('.je-debit').value) || 0;
        const cred = parseFloat(r.querySelector('.je-credit').value) || 0;
        const desc = r.querySelector('.je-line-desc').value.trim();

        if (accId && (deb > 0 || cred > 0)) {
            items.push({ account_id: accId, debit: deb, credit: cred, description: desc });
        }
    });

    const payload = {
        entry_date: document.getElementById('jeDate').value,
        doc_reference: document.getElementById('jeDocRef').value,
        description: document.getElementById('jeDesc').value,
        source_type: 'manual',
        items: items
    };

    const btn = document.getElementById('jeSubmitBtn');
    btn.disabled = true;

    try {
        const res = await apiRequest('<?= BASE_URL ?>accounting.php?action=store_journal', 'POST', payload);
        if (res.success) {
            alert(`✅ ${res.message}`);
            window.location.href = '<?= BASE_URL ?>accounting.php?tab=journal';
        } else {
            alert(`❌ خطأ: ${res.message}`);
            btn.disabled = false;
        }
    } catch (err) {
        console.error(err);
        alert('حدث خطأ أثناء حفظ القيد');
        btn.disabled = false;
    }
}

async function viewJournalDetails(id) {
    const modal = document.getElementById('journalDetailsModal');
    modal.style.display = 'flex';

    try {
        const res = await apiRequest(`<?= BASE_URL ?>accounting.php?action=view_journal&id=${id}`);
        if (!res.success) {
            alert(res.message);
            closeViewJournalModal();
            return;
        }

        const je = res.entry;
        document.getElementById('viewJeNumber').textContent = `قيد يومية: ${je.entry_number}`;
        document.getElementById('viewJeMeta').textContent = `التاريخ: ${je.entry_date} | المرجع: ${je.doc_reference || 'بدون مرجع'} | المحرر: ${je.creator_name || 'المدير'}`;
        document.getElementById('viewJeDesc').textContent = je.description;

        const tbody = document.getElementById('viewJeItemsTbody');
        tbody.innerHTML = '';
        (je.items || []).forEach((item, idx) => {
            const deb = Number(item.debit) > 0 ? `<strong style="color:#38bdf8;">${Number(item.debit).toFixed(2)} ج.م</strong>` : '-';
            const cred = Number(item.credit) > 0 ? `<strong style="color:#10b981;">${Number(item.credit).toFixed(2)} ج.م</strong>` : '-';
            const row = `
                <tr>
                    <td>${idx + 1}</td>
                    <td><strong>${item.account_code}</strong> - ${item.account_name}</td>
                    <td>${deb}</td>
                    <td>${cred}</td>
                    <td>${item.description || '-'}</td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });

        document.getElementById('viewJeTotDebit').textContent = Number(je.total_debit).toFixed(2) + ' ج.م';
        document.getElementById('viewJeTotCredit').textContent = Number(je.total_credit).toFixed(2) + ' ج.م';
    } catch (err) {
        console.error(err);
        alert('حدث خطأ أثناء جلب تفاصيل القيد');
        closeViewJournalModal();
    }
}

function closeViewJournalModal() {
    document.getElementById('journalDetailsModal').style.display = 'none';
}
</script>
