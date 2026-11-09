<?php
/**
 * شاشة إدارة المستودعات والمخازن
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 */
$pageTitle = "إدارة المستودعات والمخازن";
?>

<div class="stores-management-page">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #fff; margin-bottom: 0.25rem;">
                <i class="fas fa-warehouse" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                المستودعات والمخازن
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                إدارة الفروع والمستودعات المركزية ومستودعات المعارض ونقاط البيع
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= BASE_URL ?>items.php" class="btn btn-outline">
                <i class="fas fa-boxes"></i>
                الأصناف والمنتجات
            </a>
            <a href="<?= BASE_URL ?>transfers.php" class="btn btn-outline">
                <i class="fas fa-exchange-alt"></i>
                أذونات التحويل
            </a>
            <button type="button" class="btn btn-primary" onclick="openCreateStoreModal()">
                <i class="fas fa-plus-circle"></i>
                إضافة مستودع جديد
            </button>
        </div>
    </div>

    <!-- Stores Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.25rem;">
        <?php foreach ($stores as $st): 
            $stats = $st['stats'];
        ?>
        <div class="card" style="padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; right: 0; width: 4px; height: 100%; background: <?= $st['is_active'] ? 'var(--brand-amber)' : '#f87171' ?>;"></div>

            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                    <div>
                        <span class="badge" style="font-family: monospace; background: rgba(56, 189, 248, 0.15); color: #38bdf8; font-size: 0.75rem; margin-bottom: 0.25rem; display: inline-block;">
                            <?= htmlspecialchars($st['code']) ?>
                        </span>
                        <h3 style="font-size: 1.15rem; font-weight: 700; color: #fff; margin: 0;">
                            <?= htmlspecialchars($st['name']) ?>
                        </h3>
                    </div>
                    <div>
                        <?php if ($st['is_active']): ?>
                            <span class="badge badge-emerald">نشط</span>
                        <?php else: ?>
                            <span class="badge badge-rose">معطل</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 1rem; line-height: 1.6;">
                    <?php if (!empty($st['location'])): ?>
                        <div><i class="fas fa-map-marker-alt" style="width: 16px; color: #94a3b8;"></i> <?= htmlspecialchars($st['location']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($st['manager_name'])): ?>
                        <div><i class="fas fa-user-tie" style="width: 16px; color: #94a3b8;"></i> المسؤول: <?= htmlspecialchars($st['manager_name']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($st['phone'])): ?>
                        <div><i class="fas fa-phone" style="width: 16px; color: #94a3b8;"></i> <?= htmlspecialchars($st['phone']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Store Stock Metrics -->
                <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.85rem; margin-bottom: 1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">الأصناف المتوفرة</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-top: 2px;">
                            <?= number_format($stats['total_items']) ?> صنف
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">إجمالي الوحدات</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #38bdf8; margin-top: 2px;">
                            <?= number_format($stats['total_stock_qty'], 1) ?>
                        </div>
                    </div>
                    <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 0.5rem;">
                        <div style="font-size: 0.75rem; color: var(--text-muted);">القيمة بسعر التكلفة</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: #fbbf24; margin-top: 2px;">
                            <?= Helpers::formatCurrency($stats['total_cost_value']) ?>
                        </div>
                    </div>
                    <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 0.5rem;">
                        <div style="font-size: 0.75rem; color: var(--text-muted);">القيمة بسعر البيع</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: #34d399; margin-top: 2px;">
                            <?= Helpers::formatCurrency($stats['total_retail_value']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Store Actions -->
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 0.85rem;">
                <a href="<?= BASE_URL ?>items.php?store_id=<?= $st['id'] ?>" class="btn btn-outline btn-sm">
                    <i class="fas fa-boxes"></i> عرض الأصناف
                </a>
                <button type="button" class="btn btn-outline btn-sm" onclick="openEditStoreModal(<?= htmlspecialchars(json_encode($st)) ?>)">
                    <i class="fas fa-edit"></i> تعديل
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ================= MODAL: إضافة / تعديل مستودع ================= -->
<div id="storeModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 500px; padding: 1.5rem; background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
            <h3 id="storeModalTitle" style="font-size: 1.15rem; font-weight: 700; color: #fff; margin: 0;">
                <i class="fas fa-warehouse" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                إضافة مستودع جديد
            </h3>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeStoreModal()" style="border: none; font-size: 1.1rem;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="storeForm" method="POST" action="<?= BASE_URL ?>stores.php">
            <input type="hidden" name="action" id="storeFormAction" value="create_store">
            <input type="hidden" name="id" id="storeId" value="">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">

            <div class="form-group">
                <label class="form-label required">اسم المستودع</label>
                <input type="text" name="name" id="storeNameInput" class="form-control" placeholder="مثال: مستودع العبور المركزي" required>
            </div>

            <div class="form-group">
                <label class="form-label required">كود المستودع التعريفي (فريد)</label>
                <input type="text" name="code" id="storeCodeInput" class="form-control" placeholder="STR-03" style="font-family: monospace; text-transform: uppercase;" required>
            </div>

            <div class="form-group">
                <label class="form-label">الموقع / العنوان</label>
                <input type="text" name="location" id="storeLocationInput" class="form-control" placeholder="مثال: المنطقة الصناعية - قطعة 44">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">مسؤول المستودع</label>
                    <input type="text" name="manager_name" id="storeManagerInput" class="form-control" placeholder="اسم الأمين / المدير">
                </div>
                <div class="form-group">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" id="storePhoneInput" class="form-control" placeholder="01xxxxxxxxx">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: #fff;">
                    <input type="checkbox" name="is_active" id="storeIsActive" value="1" checked>
                    مستودع نشط ويعمل بالنظام
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <button type="button" class="btn btn-outline" onclick="closeStoreModal()">إلغاء</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ المستودع
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateStoreModal() {
    document.getElementById('storeModalTitle').innerHTML = '<i class="fas fa-plus-circle" style="color: var(--brand-amber); margin-left: 0.5rem;"></i> إضافة مستودع جديد';
    document.getElementById('storeFormAction').value = 'create_store';
    document.getElementById('storeId').value = '';
    document.getElementById('storeForm').reset();
    document.getElementById('storeIsActive').checked = true;
    document.getElementById('storeModal').style.display = 'flex';
}

function openEditStoreModal(st) {
    document.getElementById('storeModalTitle').innerHTML = '<i class="fas fa-edit" style="color: #38bdf8; margin-left: 0.5rem;"></i> تعديل مستودع: ' + st.name;
    document.getElementById('storeFormAction').value = 'update_store';
    document.getElementById('storeId').value = st.id;
    document.getElementById('storeNameInput').value = st.name;
    document.getElementById('storeCodeInput').value = st.code;
    document.getElementById('storeLocationInput').value = st.location || '';
    document.getElementById('storeManagerInput').value = st.manager_name || '';
    document.getElementById('storePhoneInput').value = st.phone || '';
    document.getElementById('storeIsActive').checked = (st.is_active == 1);
    document.getElementById('storeModal').style.display = 'flex';
}

function closeStoreModal() {
    document.getElementById('storeModal').style.display = 'none';
}
</script>

