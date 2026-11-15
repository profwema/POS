<?php
/**
 * شاشة دليل الموردين والحسابات الدائنة
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "دليل الموردين والحسابات الدائنة";
?>

<div class="suppliers-list-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-handshake" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                دليل الموردين والحسابات الدائنة
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                إدارة ملفات الموردين، أرصدة المديونيات، شروط الائتمان، واستخراج كشوف الحسابات وأعمار الديون
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= BASE_URL ?>suppliers.php?action=aging" class="btn btn-outline" style="border-color: #38bdf8; color: #38bdf8;">
                <i class="fas fa-hourglass-half"></i>
                تقرير أعمار الديون
            </a>
            <button type="button" class="btn btn-primary" onclick="openAddSupplierModal()" style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);">
                <i class="fas fa-user-plus"></i>
                إضافة مورد جديد
            </button>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-truck"></i>
            </div>
            <div>
                <div class="stat-val"><?= number_format($totalCount) ?></div>
                <div class="stat-title">عدد الموردين المسجلين</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($totalBalanceOwed) ?></div>
                <div class="stat-title">إجمالي المديونيات المستحقة للموردين</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
        <form method="GET" action="<?= BASE_URL ?>suppliers.php" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">بحث سريع (اسم المورد، الشركة، الهاتف، أو الرقم الضريبي)</label>
                <input type="text" name="search" class="form-control" placeholder="اكتب للبحث..." value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">الحالة</label>
                <select name="status" class="form-control">
                    <option value="" <?= ($status === '') ? 'selected' : '' ?>>جميع الحالات</option>
                    <option value="1" <?= ($status === '1') ? 'selected' : '' ?>>الموردين النشطين</option>
                    <option value="0" <?= ($status === '0') ? 'selected' : '' ?>>المعطلين</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    بحث
                </button>
                <a href="<?= BASE_URL ?>suppliers.php" class="btn btn-outline" title="إعادة ضبط">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Suppliers Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th>المورد / اسم المنشأة</th>
                        <th>بيانات الاتصال</th>
                        <th>الرقم الضريبي والسجل</th>
                        <th>الرصيد الدائن المستحق</th>
                        <th>حد الائتمان وأجل الدفع</th>
                        <th>إجمالي التعاملات</th>
                        <th>الحالة</th>
                        <th style="text-align: center; width: 140px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($suppliers)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                                <i class="fas fa-truck-moving fa-3x" style="opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                                لا يوجد موردون مسجلون يطابقون خيارات البحث.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($suppliers as $s): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">
                                        <?= htmlspecialchars($s['name']) ?>
                                    </div>
                                    <?php if (!empty($s['company_name'])): ?>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);">
                                            <?= htmlspecialchars($s['company_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($s['contact_person'])): ?>
                                        <div style="font-size: 0.75rem; color: #38bdf8;">
                                            <i class="fas fa-user-tie" style="margin-left: 3px;"></i>
                                            <?= htmlspecialchars($s['contact_person']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-family: monospace; font-size: 0.9rem; color: var(--text-main);">
                                        <i class="fas fa-phone-alt" style="color: var(--brand-amber); margin-left: 4px; font-size: 0.75rem;"></i>
                                        <?= htmlspecialchars($s['phone']) ?>
                                    </div>
                                    <?php if (!empty($s['address'])): ?>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                            <i class="fas fa-map-marker-alt" style="margin-left: 3px;"></i>
                                            <?= htmlspecialchars($s['address']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($s['tax_number'])): ?>
                                        <div style="font-size: 0.82rem; color: var(--text-muted);">
                                            ضريبي: <span style="font-family: monospace;"><?= htmlspecialchars($s['tax_number']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($s['commercial_registry'])): ?>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                                            سجل: <span style="font-family: monospace;"><?= htmlspecialchars($s['commercial_registry']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight: 800; font-size: 1rem; color: <?= ((float)$s['current_balance'] > 0) ? '#ef4444' : '#10b981' ?>;">
                                        <?= Helpers::formatCurrency($s['current_balance']) ?>
                                    </div>
                                    <small style="color: var(--text-muted); font-size: 0.72rem;">
                                        افتتاحي: <?= Helpers::formatCurrency($s['opening_balance']) ?>
                                    </small>
                                </td>
                                <td>
                                    <div style="font-size: 0.82rem; color: var(--text-main);">
                                        سقف: <?= Helpers::formatCurrency($s['credit_limit']) ?>
                                    </div>
                                    <small style="color: var(--text-muted); font-size: 0.72rem;">
                                        أجل الدفع: <?= (int)$s['payment_terms_days'] ?> يوم
                                    </small>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: var(--text-main);">
                                        <?= (int)$s['total_purchases_count'] ?> فواتير
                                    </span>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 3px;">
                                        قيمة: <?= Helpers::formatCurrency($s['total_purchases_amount']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($s['is_active']): ?>
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">نشط</span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">معطل</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 0.4rem; justify-content: center;">
                                        <a href="<?= BASE_URL ?>suppliers.php?action=statement&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline" title="كشف الحساب التفصيلي" style="color: #38bdf8; border-color: rgba(56, 189, 248, 0.3);">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline" onclick="openEditSupplierModal(<?= htmlspecialchars(json_encode($s)) ?>)" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="<?= BASE_URL ?>suppliers.php" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف أو تعطيل هذا المورد؟');">
                                            <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline" title="حذف/تعطيل" style="color: #ef4444; border-color: transparent;">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
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

<!-- Modal: Add Supplier (Explicitly Hidden) -->
<div id="addSupplierModal" class="modal-backdrop" style="display: none;">
    <div class="card" style="max-width: 650px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-main);">
                <i class="fas fa-user-plus" style="color: #38bdf8; margin-left: 0.5rem;"></i>
                إضافة مورد جديد
            </h3>
            <button type="button" onclick="closeAddSupplierModal()" class="btn btn-outline btn-sm" style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="<?= BASE_URL ?>suppliers.php">
            <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
            <input type="hidden" name="action" value="store">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">اسم المورد / الشهرة <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: شركة الأهرام">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">اسم الشركة الرسمي</label>
                    <input type="text" name="company_name" class="form-control" placeholder="اسم المنشأة المسجل">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">رقم الهاتف الرئيسي <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="phone" class="form-control" required placeholder="010XXXXXXXX">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">الشخص المسؤول / المندوب</label>
                    <input type="text" name="contact_person" class="form-control" placeholder="اسم مندوب التوريد">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">الرقم الضريبي (9 أرقام)</label>
                    <input type="text" name="tax_number" class="form-control" placeholder="100200300">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">السجل التجاري</label>
                    <input type="text" name="commercial_registry" class="form-control" placeholder="رقم السجل">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">الرصيد الافتتاحي (دائن / له)</label>
                    <input type="number" step="0.01" name="opening_balance" class="form-control" value="0.00">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">سقف الائتمان المسموح</label>
                    <input type="number" step="0.01" name="credit_limit" class="form-control" value="50000.00">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">أجل الدفع المعتاد (أيام)</label>
                    <input type="number" name="payment_terms_days" class="form-control" value="30">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" placeholder="supplier@example.com">
                </div>
            </div>

            <div class="form-group" style="margin-top: 0.5rem;">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">العنوان والموقع</label>
                <input type="text" name="address" class="form-control" placeholder="المحافظة - المدينة - الشارع">
            </div>

            <div class="form-group">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">ملاحظات إضافية</label>
                <textarea name="notes" rows="2" class="form-control"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 1rem;">
                <button type="button" onclick="closeAddSupplierModal()" class="btn btn-outline">إلغاء</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    حفظ المورد
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Supplier (Explicitly Hidden) -->
<div id="editSupplierModal" class="modal-backdrop" style="display: none;">
    <div class="card" style="max-width: 650px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-main);">
                <i class="fas fa-edit" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                تعديل بيانات المورد
            </h3>
            <button type="button" onclick="closeEditSupplierModal()" class="btn btn-outline btn-sm" style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="<?= BASE_URL ?>suppliers.php">
            <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="editSupplierId">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">اسم المورد <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" id="editSupplierName" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">اسم الشركة</label>
                    <input type="text" name="company_name" id="editSupplierCompany" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">رقم الهاتف <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="phone" id="editSupplierPhone" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">الشخص المسؤول</label>
                    <input type="text" name="contact_person" id="editSupplierContact" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">الرقم الضريبي</label>
                    <input type="text" name="tax_number" id="editSupplierTax" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">السجل التجاري</label>
                    <input type="text" name="commercial_registry" id="editSupplierCR" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">سقف الائتمان</label>
                    <input type="number" step="0.01" name="credit_limit" id="editSupplierLimit" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">أجل الدفع (أيام)</label>
                    <input type="number" name="payment_terms_days" id="editSupplierTerms" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">الحالة</label>
                    <select name="is_active" id="editSupplierStatus" class="form-control">
                        <option value="1">نشط</option>
                        <option value="0">معطل</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">البريد الإلكتروني</label>
                    <input type="email" name="email" id="editSupplierEmail" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">العنوان</label>
                <input type="text" name="address" id="editSupplierAddress" class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">ملاحظات</label>
                <textarea name="notes" id="editSupplierNotes" rows="2" class="form-control"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 1rem;">
                <button type="button" onclick="closeEditSupplierModal()" class="btn btn-outline">إلغاء</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    تحديث البيانات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddSupplierModal() {
    document.getElementById('addSupplierModal').style.display = 'flex';
}
function closeAddSupplierModal() {
    document.getElementById('addSupplierModal').style.display = 'none';
}

function openEditSupplierModal(s) {
    document.getElementById('editSupplierId').value = s.id;
    document.getElementById('editSupplierName').value = s.name;
    document.getElementById('editSupplierCompany').value = s.company_name || '';
    document.getElementById('editSupplierPhone').value = s.phone || '';
    document.getElementById('editSupplierContact').value = s.contact_person || '';
    document.getElementById('editSupplierTax').value = s.tax_number || '';
    document.getElementById('editSupplierCR').value = s.commercial_registry || '';
    document.getElementById('editSupplierLimit').value = s.credit_limit || 0;
    document.getElementById('editSupplierTerms').value = s.payment_terms_days || 30;
    document.getElementById('editSupplierStatus').value = s.is_active;
    document.getElementById('editSupplierEmail').value = s.email || '';
    document.getElementById('editSupplierAddress').value = s.address || '';
    document.getElementById('editSupplierNotes').value = s.notes || '';

    document.getElementById('editSupplierModal').style.display = 'flex';
}
function closeEditSupplierModal() {
    document.getElementById('editSupplierModal').style.display = 'none';
}
</script>
