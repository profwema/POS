<?php
/**
 * شاشة إدارة الأقسام والتصنيفات ووحدات القياس
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 */
$pageTitle = "إدارة التصنيفات ووحدات القياس";
?>

<div class="categories-units-page">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-tags" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                التصنيفات وأقسام المنتجات ووحدات القياس
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                تنظيم شجرة الأقسام، تصنيف المنتجات، وضبط وحدات البيع والشراء
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= BASE_URL ?>items.php" class="btn btn-outline">
                <i class="fas fa-boxes"></i>
                قائمة المنتجات
            </a>
            <button type="button" class="btn btn-outline" onclick="openCreateUnitModal()" style="border-color: var(--brand-blue); color: var(--brand-blue);">
                <i class="fas fa-ruler-combined"></i>
                إضافة وحدة قياس
            </button>
            <button type="button" class="btn btn-primary" onclick="openCreateCategoryModal()">
                <i class="fas fa-plus-circle"></i>
                إضافة تصنيف جديد
            </button>
        </div>
    </div>

    <!-- Layout: 2 Columns (Categories on right, Units on left) -->
    <div style="display: grid; grid-template-columns: 2fr 1.2fr; gap: 1.5rem; align-items: start;">
        
        <!-- SECTION 1: Categories -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 800; color: var(--text-main);">
                    <i class="fas fa-folder-open" style="color: var(--brand-amber); margin-left: 0.4rem;"></i>
                    أقسام وتصنيفات المنتجات (<?= count($categories) ?>)
                </div>
                <button type="button" class="btn btn-outline btn-sm" onclick="openCreateCategoryModal()">
                    <i class="fas fa-plus"></i> إضافة قسم
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-pro data-table">
                    <thead>
                        <tr>
                            <th>الكود</th>
                            <th>القسم / التصنيف</th>
                            <th>عدد الأصناف</th>
                            <th>الحالة</th>
                            <th style="text-align: center;">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td style="font-family: var(--font-mono); color: var(--brand-amber); font-weight: 700;">
                                <?= htmlspecialchars($cat['code'] ?: '---') ?>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="<?= htmlspecialchars($cat['icon']) ?>" style="color: var(--brand-blue);"></i>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </div>
                                <?php if (!empty($cat['description'])): ?>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                        <?= htmlspecialchars($cat['description']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>items.php?category_id=<?= $cat['id'] ?>" class="badge badge-blue" style="text-decoration: none;">
                                    <?= $cat['items_count'] ?> صنف
                                </a>
                            </td>
                            <td>
                                <?php if ($cat['is_active']): ?>
                                    <span class="badge badge-emerald">نشط</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">معطل</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-outline btn-sm" onclick="openEditCategoryModal(<?= htmlspecialchars(json_encode($cat)) ?>)">
                                    <i class="fas fa-edit"></i> تعديل
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION 2: Units of Measure -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 800; color: var(--text-main);">
                    <i class="fas fa-balance-scale" style="color: var(--brand-emerald); margin-left: 0.4rem;"></i>
                    وحدات القياس (<?= count($units) ?>)
                </div>
                <button type="button" class="btn btn-outline btn-sm" onclick="openCreateUnitModal()">
                    <i class="fas fa-plus"></i> إضافة وحدة
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-pro data-table">
                    <thead>
                        <tr>
                            <th>الوحدة</th>
                            <th>الرمز</th>
                            <th>الأصناف</th>
                            <th style="text-align: center;">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($units as $u): ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--text-main);">
                                <?= htmlspecialchars($u['name']) ?>
                            </td>
                            <td>
                                <span class="badge" style="background: rgba(2, 132, 199, 0.1); color: var(--brand-blue); font-family: var(--font-mono); font-weight: 700;">
                                    <?= htmlspecialchars($u['symbol']) ?>
                                </span>
                            </td>
                            <td style="color: var(--brand-blue); font-weight: 800;">
                                <?= $u['items_count'] ?> صنف
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-outline btn-sm" onclick="openEditUnitModal(<?= htmlspecialchars(json_encode($u)) ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL 1: إضافة / تعديل قسم ================= -->
<div id="catModal" class="modal-backdrop" style="display: none;">
    <div class="card modal-dialog" style="max-width: 480px;">
        <div class="modal-header">
            <h3 id="catModalTitle" class="modal-title">
                <i class="fas fa-folder-plus" style="color: var(--brand-amber);"></i>
                <span>إضافة قسم جديد</span>
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeCatModal()" title="إغلاق">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="catForm" method="POST" action="<?= BASE_URL ?>categories.php">
            <input type="hidden" name="action" id="catFormAction" value="create_category">
            <input type="hidden" name="id" id="catId" value="">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">

            <div class="form-group">
                <label class="form-label required">اسم القسم / التصنيف</label>
                <input type="text" name="name" id="catNameInput" class="form-control" placeholder="مثال: عصائر ومشروبات طبيعية" required>
            </div>

            <div class="form-group">
                <label class="form-label">كود القسم التعريفي</label>
                <input type="text" name="code" id="catCodeInput" class="form-control" placeholder="CAT-JUICE" style="font-family: var(--font-mono); text-transform: uppercase;">
            </div>

            <div class="form-group">
                <label class="form-label">أيقونة القسم (FontAwesome)</label>
                <input type="text" name="icon" id="catIconInput" class="form-control" placeholder="fas fa-tag" value="fas fa-tag">
            </div>

            <div class="form-group">
                <label class="form-label">الوصف التوضيحي</label>
                <textarea name="description" id="catDescInput" class="form-control" rows="2" placeholder="وصف للأصناف التابعة لهذا القسم"></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-main); font-weight: 700;">
                    <input type="checkbox" name="is_active" id="catIsActive" value="1" checked>
                    <span>قسم نشط ومتاح في فلاتر النظام</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <button type="button" class="btn btn-outline" onclick="closeCatModal()">إلغاء</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ القسم
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: إضافة / تعديل وحدة قياس ================= -->
<div id="unitModal" class="modal-backdrop" style="display: none;">
    <div class="card modal-dialog" style="max-width: 440px;">
        <div class="modal-header">
            <h3 id="unitModalTitle" class="modal-title">
                <i class="fas fa-ruler-combined" style="color: var(--brand-blue);"></i>
                <span>إضافة وحدة قياس</span>
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeUnitModal()" title="إغلاق">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="unitForm" method="POST" action="<?= BASE_URL ?>categories.php">
            <input type="hidden" name="action" id="unitFormAction" value="create_unit">
            <input type="hidden" name="id" id="unitId" value="">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">

            <div class="form-group">
                <label class="form-label required">اسم الوحدة بالكامل</label>
                <input type="text" name="name" id="unitNameInput" class="form-control" placeholder="مثال: شيكارة / كيس" required>
            </div>

            <div class="form-group">
                <label class="form-label required">رمز الوحدة المختصر</label>
                <input type="text" name="symbol" id="unitSymbolInput" class="form-control" placeholder="مثال: كيس" required>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <button type="button" class="btn btn-outline" onclick="closeUnitModal()">إلغاء</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ الوحدة
                </button>
            </div>
        </form>
    </div>
</div>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateCategoryModal() {
    document.getElementById('catModalTitle').innerHTML = '<i class="fas fa-folder-plus" style="color: var(--brand-amber); margin-left: 0.5rem;"></i> إضافة قسم جديد';
    document.getElementById('catFormAction').value = 'create_category';
    document.getElementById('catId').value = '';
    document.getElementById('catForm').reset();
    document.getElementById('catIsActive').checked = true;
    document.getElementById('catModal').style.display = 'flex';
}

function openEditCategoryModal(cat) {
    document.getElementById('catModalTitle').innerHTML = '<i class="fas fa-edit" style="color: #38bdf8; margin-left: 0.5rem;"></i> تعديل قسم: ' + cat.name;
    document.getElementById('catFormAction').value = 'update_category';
    document.getElementById('catId').value = cat.id;
    document.getElementById('catNameInput').value = cat.name;
    document.getElementById('catCodeInput').value = cat.code || '';
    document.getElementById('catIconInput').value = cat.icon || 'fas fa-tag';
    document.getElementById('catDescInput').value = cat.description || '';
    document.getElementById('catIsActive').checked = (cat.is_active == 1);
    document.getElementById('catModal').style.display = 'flex';
}

function closeCatModal() {
    document.getElementById('catModal').style.display = 'none';
}

function openCreateUnitModal() {
    document.getElementById('unitModalTitle').innerHTML = '<i class="fas fa-ruler-combined" style="color: #38bdf8; margin-left: 0.5rem;"></i> إضافة وحدة قياس';
    document.getElementById('unitFormAction').value = 'create_unit';
    document.getElementById('unitId').value = '';
    document.getElementById('unitForm').reset();
    document.getElementById('unitModal').style.display = 'flex';
}

function openEditUnitModal(u) {
    document.getElementById('unitModalTitle').innerHTML = '<i class="fas fa-edit" style="color: #38bdf8; margin-left: 0.5rem;"></i> تعديل وحدة: ' + u.name;
    document.getElementById('unitFormAction').value = 'update_unit';
    document.getElementById('unitId').value = u.id;
    document.getElementById('unitNameInput').value = u.name;
    document.getElementById('unitSymbolInput').value = u.symbol;
    document.getElementById('unitModal').style.display = 'flex';
}

function closeUnitModal() {
    document.getElementById('unitModal').style.display = 'none';
}
</script>

