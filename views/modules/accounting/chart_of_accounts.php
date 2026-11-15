<?php
/**
 * شاشة شجرة ودليل الحسابات المالي
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "شجرة ودليل الحسابات المالي";
?>

<div class="accounting-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-sitemap" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                شجرة ودليل الحسابات المالي (Chart of Accounts)
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                الهيكل المالي التأسيسي: الأصول، الخصوم، حقوق الملكية، الإيرادات، والمصروفات
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-primary" onclick="openAddAccountModal()" style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);">
                <i class="fas fa-plus-circle"></i>
                إضافة حساب جديد
            </button>
        </div>
    </div>

    <!-- Accounting Tabs Nav -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; flex-wrap: wrap;">
        <a href="<?= BASE_URL ?>accounting.php?tab=chart" class="btn btn-primary" style="border-radius: var(--radius-md);">
            <i class="fas fa-sitemap" style="margin-left: 4px;"></i> شجرة الحسابات
        </a>
        <a href="<?= BASE_URL ?>accounting.php?tab=journal" class="btn btn-outline" style="border-radius: var(--radius-md);">
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

    <!-- Accounts Tree Table Card -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 140px;">كود الحساب</th>
                        <th>اسم الحساب المحاسبي</th>
                        <th>النوع والتصنيف</th>
                        <th>طبيعة الحساب</th>
                        <th>المستوى</th>
                        <th>الرصيد الافتتاحي</th>
                        <th>الرصيد الحالي</th>
                        <th>قابل للتقييد</th>
                        <th style="text-align: center; width: 130px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($allAccounts)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                                لا توجد حسابات مسجلة في شجرة الحسابات.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($allAccounts as $acc): ?>
                            <?php 
                            $indent = ($acc['level'] - 1) * 22; 
                            $isParent = (bool)$acc['is_parent'];
                            $typeLabels = [
                                'asset'     => ['الأصول', 'badge-primary', '#38bdf8'],
                                'liability' => ['الخصوم', 'badge-danger', '#ef4444'],
                                'equity'    => ['حقوق الملكية', 'badge-warning', '#f59e0b'],
                                'revenue'   => ['الإيرادات', 'badge-success', '#10b981'],
                                'expense'   => ['المصروفات', 'badge-secondary', '#a855f7']
                            ];
                            $typeInfo = $typeLabels[$acc['type']] ?? ['عام', '', '#94a3b8'];
                            ?>
                            <tr style="<?= $isParent ? 'background: rgba(148, 163, 184, 0.04); font-weight: 700;' : '' ?>">
                                <td>
                                    <span style="font-family: monospace; font-size: 0.95rem; font-weight: 800; color: <?= $typeInfo[2] ?>;">
                                        <?= htmlspecialchars($acc['account_code']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="padding-right: <?= $indent ?>px; display: flex; align-items: center; gap: 0.5rem;">
                                        <?php if ($isParent): ?>
                                            <i class="fas fa-folder-open" style="color: var(--brand-amber); font-size: 0.85rem;"></i>
                                        <?php else: ?>
                                            <i class="fas fa-file-invoice" style="color: var(--text-muted); font-size: 0.8rem;"></i>
                                        <?php endif; ?>
                                        <span style="color: var(--text-main);">
                                            <?= htmlspecialchars($acc['name']) ?>
                                        </span>
                                    </div>
                                    <?php if (!empty($acc['description'])): ?>
                                        <div style="padding-right: <?= $indent + 22 ?>px; font-size: 0.75rem; color: var(--text-muted);">
                                            <?= htmlspecialchars($acc['description']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: <?= $typeInfo[2] ?>;">
                                        <?= $typeInfo[0] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="font-size: 0.72rem; <?= ($acc['nature'] === 'debit') ? 'background: rgba(56, 189, 248, 0.15); color: #38bdf8;' : 'background: rgba(16, 185, 129, 0.15); color: #10b981;' ?>">
                                        <?= ($acc['nature'] === 'debit') ? 'مدين (Debit)' : 'دائن (Credit)' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: var(--text-muted); font-family: monospace;">
                                        L<?= (int)$acc['level'] ?>
                                    </span>
                                </td>
                                <td style="font-size: 0.88rem; color: var(--text-muted);">
                                    <?= Helpers::formatCurrency($acc['opening_balance']) ?>
                                </td>
                                <td style="font-weight: 800; font-size: 0.95rem; color: <?= ((float)$acc['current_balance'] >= 0) ? 'var(--text-main)' : '#ef4444' ?>;">
                                    <?= Helpers::formatCurrency($acc['current_balance']) ?>
                                </td>
                                <td>
                                    <?php if (!$isParent): ?>
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 0.72rem;">
                                            <i class="fas fa-check"></i> نعم (فرعي)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(148, 163, 184, 0.15); color: var(--text-muted); font-size: 0.72rem;">
                                            حساب أب
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 0.4rem; justify-content: center;">
                                        <?php if (!$isParent): ?>
                                            <a href="<?= BASE_URL ?>accounting.php?tab=statement&account_id=<?= $acc['id'] ?>" class="btn btn-sm btn-outline" title="دفتر الأستاذ للحساب" style="color: #38bdf8; border-color: rgba(56, 189, 248, 0.3);">
                                                <i class="fas fa-book-open"></i>
                                            </a>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-outline" onclick="openAddSubAccountModal(<?= $acc['id'] ?>, '<?= htmlspecialchars(addslashes($acc['account_code'])) ?>', '<?= htmlspecialchars(addslashes($acc['name'])) ?>')" title="إضافة حساب فرعي تحته">
                                            <i class="fas fa-plus"></i>
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

<!-- Modal: Add New Account (Explicitly Hidden) -->
<div id="addAccountModal" class="modal-backdrop" style="display: none;">
    <div class="card" style="max-width: 600px; width: 100%; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-main);">
                <i class="fas fa-folder-plus" style="color: #38bdf8; margin-left: 0.5rem;"></i>
                إضافة حساب جديد في الدليل الشجري
            </h3>
            <button type="button" onclick="closeAddAccountModal()" class="btn btn-outline btn-sm" style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="<?= BASE_URL ?>accounting.php">
            <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
            <input type="hidden" name="action" value="store_account">

            <div class="form-group">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">الحساب الأب (المجموعة الرئيسية)</label>
                <select name="parent_id" id="accountParentSelect" class="form-control" onchange="onParentChange()">
                    <option value="">-- بدون حساب أب (حساب رئيسي مستوى 1) --</option>
                    <?php foreach ($allAccounts as $p): ?>
                        <option value="<?= $p['id'] ?>" data-code="<?= $p['account_code'] ?>" data-type="<?= $p['type'] ?>" data-nature="<?= $p['nature'] ?>">
                            <?= htmlspecialchars($p['account_code']) ?> - <?= htmlspecialchars($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">كود الحساب <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="account_code" id="accountCodeInput" class="form-control" placeholder="مثال: 1107" required style="font-family: monospace; font-weight: 700;">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">اسم الحساب <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="مثال: بنك مصر حساب جاري" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">نوع الحساب</label>
                    <select name="type" id="accountTypeSelect" class="form-control">
                        <option value="asset">الأصول (Assets)</option>
                        <option value="liability">الخصوم (Liabilities)</option>
                        <option value="equity">حقوق الملكية (Equity)</option>
                        <option value="revenue">الإيرادات (Revenues)</option>
                        <option value="expense">المصروفات (Expenses)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">طبيعة الحساب</label>
                    <select name="nature" id="accountNatureSelect" class="form-control">
                        <option value="debit">مدين (Debit)</option>
                        <option value="credit">دائن (Credit)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">الرصيد الافتتاحي</label>
                <input type="number" step="0.01" name="opening_balance" class="form-control" value="0.00">
            </div>

            <div class="form-group">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">الوصف والبيان</label>
                <input type="text" name="description" class="form-control" placeholder="بيان استخدام الحساب...">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 1rem;">
                <button type="button" onclick="closeAddAccountModal()" class="btn btn-outline">إلغاء</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    حفظ الحساب
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddAccountModal() {
    document.getElementById('accountParentSelect').value = '';
    document.getElementById('accountCodeInput').value = '';
    document.getElementById('addAccountModal').style.display = 'flex';
}
function closeAddAccountModal() {
    document.getElementById('addAccountModal').style.display = 'none';
}

function openAddSubAccountModal(parentId, parentCode, parentName) {
    document.getElementById('accountParentSelect').value = parentId;
    document.getElementById('accountCodeInput').value = parentCode + '';
    onParentChange();
    document.getElementById('addAccountModal').style.display = 'flex';
}

function onParentChange() {
    const sel = document.getElementById('accountParentSelect');
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) {
        const type = opt.getAttribute('data-type');
        const nature = opt.getAttribute('data-nature');
        if (type) document.getElementById('accountTypeSelect').value = type;
        if (nature) document.getElementById('accountNatureSelect').value = nature;
    }
}
</script>
