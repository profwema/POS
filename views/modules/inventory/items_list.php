<?php
/**
 * شاشة إدارة الأصناف والمخزون - Items & Inventory View
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 */
$pageTitle = "إدارة المنتجات والمخزون";
?>

<div class="items-management-page">
    <!-- Header Section with Breadcrumbs and Quick Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-boxes" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                إدارة الأصناف والمنتجات والمخزون
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                متابعة أرصدة المخازن اللحظية، تسعير المنتجات، والباركود والموازين الإلكترونية
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="<?= BASE_URL ?>barcode_print.php" class="btn btn-outline">
                <i class="fas fa-barcode"></i>
                طباعة ملصقات الباركود
            </a>
            <a href="<?= BASE_URL ?>stores.php" class="btn btn-outline">
                <i class="fas fa-warehouse"></i>
                المستودعات (<?= count($stores) ?>)
            </a>
            <a href="<?= BASE_URL ?>transfers.php" class="btn btn-outline">
                <i class="fas fa-exchange-alt"></i>
                تحويلات المخازن
            </a>
            <button type="button" class="btn btn-primary" onclick="openCreateItemModal()">
                <i class="fas fa-plus-circle"></i>
                إضافة صنف جديد
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-box-open"></i>
            </div>
            <div>
                <div class="stat-val"><?= number_format($valuation['total_items_count']) ?></div>
                <div class="stat-title">إجمالي الأصناف بالمخازن</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon emerald">
                <i class="fas fa-cubes"></i>
            </div>
            <div>
                <div class="stat-val"><?= number_format($valuation['total_units_in_stock'], 2) ?></div>
                <div class="stat-title">إجمالي الوحدات في المستودعات</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon amber">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($valuation['total_cost_valuation']) ?></div>
                <div class="stat-title">تقييم المخزون بسعر الشراء</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($valuation['total_retail_valuation']) ?></div>
                <div class="stat-title">تقييم المخزون بسعر البيع (ربح +<?= $valuation['profit_margin_pct'] ?>%)</div>
            </div>
        </div>
    </div>

    <!-- Alerts Banner if any low stock or expiring -->
    <?php if (!empty($lowStockAlerts) || !empty($expiringAlerts)): ?>
    <div class="alert-banner alert-amber">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: #d97706;"></i>
            <div>
                <span class="alert-title">تنبيهات المخزون النشطة:</span>
                <span class="alert-text">
                    يوجد <strong style="color: var(--brand-danger); font-weight: 800;"><?= count($lowStockAlerts) ?></strong> أصناف قاربت على النفاد، و
                    <strong style="font-weight: 800;"><?= count($expiringAlerts) ?></strong> تشغيلات تقترب صلاحيتها من الانتهاء خلال 60 يوماً.
                </span>
            </div>
        </div>
        <a href="<?= BASE_URL ?>items.php?low_stock_only=1" class="btn-alert">
            <i class="fas fa-exclamation-circle"></i>
            عرض الأصناف النافذة فقط
        </a>
    </div>
    <?php endif; ?>

    <!-- Filter and Search Toolbar -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
        <form method="GET" action="<?= BASE_URL ?>items.php" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">بحث سريع (الاسم، الباركود، الكود، PLU)</label>
                <div style="position: relative;">
                    <input type="text" name="search" class="form-control" placeholder="اكتب اسم الصنف أو امسح الباركود..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>" autofocus>
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">القسم / التصنيف</label>
                <select name="category_id" class="form-control">
                    <option value="">كافة الأقسام</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (($filters['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?> (<?= $cat['items_count'] ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">المستودع</label>
                <select name="store_id" class="form-control">
                    <option value="">كافة المستودعات</option>
                    <?php foreach ($stores as $st): ?>
                    <option value="<?= $st['id'] ?>" <?= (($filters['store_id'] ?? '') == $st['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($st['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">النوع والمخزون</label>
                <select name="filter_type" class="form-control" onchange="this.form.submit()">
                    <option value="">الكل</option>
                    <option value="scale" <?= (!empty($filters['is_scale'])) ? 'selected' : '' ?>>أصناف الميزان فقط ⚖️</option>
                    <option value="low_stock" <?= (!empty($filters['low_stock_only'])) ? 'selected' : '' ?>>الواصلة لحد الطلب ⚠️</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="height: 42px;">
                    <i class="fas fa-filter"></i> تصفية
                </button>
                <?php if (!empty($filters['search']) || !empty($filters['category_id']) || !empty($filters['store_id']) || !empty($filters['low_stock_only']) || !empty($filters['is_scale'])): ?>
                <a href="<?= BASE_URL ?>items.php" class="btn btn-outline" style="height: 42px; display: inline-flex; align-items: center;" title="إعادة تعيين الفلاتر">
                    <i class="fas fa-undo"></i>
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Items Table Card -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 800; color: var(--text-main); font-size: 1.05rem;">
                <i class="fas fa-list-ul" style="color: var(--brand-blue); margin-left: 0.4rem;"></i>
                قائمة الأصناف (إجمالي النتائج: <?= number_format($totalCount) ?>)
            </div>
            <div style="font-size: 0.82rem; color: var(--text-muted); font-weight: 600;">
                الصفحة <?= $page ?> من <?= max(1, $totalPages) ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-pro data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>اسم الصنف والوحدة</th>
                        <th>الباركود / الكود</th>
                        <th>القسم</th>
                        <th>سعر التكلفة</th>
                        <th>سعر البيع (قطاعي)</th>
                        <th>سعر الجملة</th>
                        <th>الرصيد المتاح</th>
                        <th>الحالة</th>
                        <th style="text-align: center; width: 170px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                            <i class="fas fa-box-open" style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.4;"></i>
                            <p style="font-size: 1rem; margin-bottom: 0.5rem;">لم يتم العثور على أية أصناف مطابقة للبحث</p>
                            <button type="button" class="btn btn-outline btn-sm" onclick="openCreateItemModal()">
                                <i class="fas fa-plus"></i> إضافة أول صنف الآن
                            </button>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($items as $idx => $item): 
                        $stock = (float)$item['total_stock'];
                        $reorder = (float)$item['reorder_level'];
                        $isLow = $stock <= $reorder;
                        $isZero = $stock <= 0;
                    ?>
                    <tr>
                        <td style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;">
                            <?= ($offset + $idx + 1) ?>
                        </td>
                        <td>
                            <div class="item-title">
                                <?= htmlspecialchars($item['name']) ?>
                                <?php if ($item['is_scale']): ?>
                                    <span class="badge badge-emerald" style="font-size: 0.7rem;" title="صنف ميزان باركود وزني">
                                        ⚖️ ميزان
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 3px; font-weight: 500;">
                                الوحدة: <strong style="color: var(--text-main);"><?= htmlspecialchars($item['unit_name']) ?> (<?= htmlspecialchars($item['unit_symbol']) ?>)</strong>
                                <?php if ($item['tax_rate'] > 0): ?>
                                    | ضريبة: <strong style="color: var(--brand-blue);"><?= $item['tax_rate'] ?>%</strong>
                                <?php else: ?>
                                    | <strong style="color: var(--brand-emerald);">معفى</strong>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="barcode-tag">
                                <?= htmlspecialchars($item['barcode']) ?>
                            </span>
                            <?php if ($item['is_scale'] && !empty($item['scale_plu'])): ?>
                                <div style="font-size: 0.75rem; color: var(--brand-emerald); font-weight: 700; margin-top: 3px;">PLU: <?= htmlspecialchars($item['scale_plu']) ?></div>
                            <?php elseif (!empty($item['sku'])): ?>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 3px;">SKU: <?= htmlspecialchars($item['sku']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-blue" style="font-size: 0.78rem;">
                                <?= htmlspecialchars($item['category_name'] ?? 'عام') ?>
                            </span>
                        </td>
                        <td class="price-cost">
                            <?= Helpers::formatCurrency($item['cost_price']) ?>
                        </td>
                        <td class="price-retail">
                            <?= Helpers::formatCurrency($item['retail_price']) ?>
                        </td>
                        <td class="price-wholesale">
                            <?= Helpers::formatCurrency($item['wholesale_price']) ?>
                        </td>
                        <td>
                            <?php if ($isZero): ?>
                                <span class="badge badge-rose" style="font-weight: 700;">
                                    نافد (0.00 <?= $item['unit_symbol'] ?>)
                                </span>
                            <?php elseif ($isLow): ?>
                                <span class="badge badge-amber" style="font-weight: 700;" title="أقل من حد إعادة الطلب: <?= $reorder ?>">
                                    ⚠️ <?= number_format($stock, 2) ?> <?= $item['unit_symbol'] ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-emerald" style="font-weight: 700;">
                                    <?= number_format($stock, 2) ?> <?= $item['unit_symbol'] ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($item['is_active']): ?>
                                <span class="badge badge-emerald">نشط</span>
                            <?php else: ?>
                                <span class="badge badge-rose">معطل</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.35rem; justify-content: center;">
                                <button type="button" class="btn btn-outline btn-sm" onclick="showItemCard(<?= $item['id'] ?>)" title="كارت الصنف وسجل الحركات" style="padding: 4px 8px;">
                                    <i class="fas fa-file-invoice"></i>
                                </button>
                                <button type="button" class="btn btn-outline btn-sm" onclick="openQuickAdjustModal(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['name'])) ?>', <?= $stock ?>)" title="تسوية جردية سريعة" style="padding: 4px 8px; color: #fbbf24; border-color: rgba(251,191,36,0.3);">
                                    <i class="fas fa-sliders-h"></i>
                                </button>
                                <a href="<?= BASE_URL ?>barcode_print.php?item_id=<?= $item['id'] ?>" class="btn btn-outline btn-sm" title="طباعة ملصق الباركود" style="padding: 4px 8px; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
                                    <i class="fas fa-barcode"></i>
                                </a>
                                <button type="button" class="btn btn-outline btn-sm" onclick="openEditItemModal(<?= htmlspecialchars(json_encode($item)) ?>)" title="تعديل الصنف" style="padding: 4px 8px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-outline btn-sm" onclick="confirmDeleteItem(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['name'])) ?>')" title="حذف الصنف" style="padding: 4px 8px; color: #f87171; border-color: rgba(248,113,113,0.3);">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="color: var(--text-muted); font-size: 0.85rem;">
                عرض من <?= $offset + 1 ?> إلى <?= min($offset + $limit, $totalCount) ?> من إجمالي <?= $totalCount ?> صنف
            </div>
            <div style="display: flex; gap: 0.35rem;">
                <?php if ($page > 1): ?>
                    <a href="<?= BASE_URL ?>items.php?page=<?= $page - 1 ?>&<?= http_build_query(array_diff_key($filters, ['page' => 1])) ?>" class="btn btn-outline btn-sm">السابق</a>
                <?php endif; ?>

                <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
                    <a href="<?= BASE_URL ?>items.php?page=<?= $p ?>&<?= http_build_query(array_diff_key($filters, ['page' => 1])) ?>" class="btn btn-sm <?= ($p === $page) ? 'btn-primary' : 'btn-outline' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= BASE_URL ?>items.php?page=<?= $page + 1 ?>&<?= http_build_query(array_diff_key($filters, ['page' => 1])) ?>" class="btn btn-outline btn-sm">التالي</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ================= MODAL 1: إضافة / تعديل صنف ================= -->
<div id="itemModal" class="modal-backdrop" style="display: none;">
    <div class="card modal-dialog">
        <div class="modal-header">
            <h3 id="itemModalTitle" class="modal-title">
                <i class="fas fa-plus-circle" style="color: var(--brand-amber);"></i>
                <span>إضافة صنف جديد</span>
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeItemModal()" title="إغلاق">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="itemForm" method="POST" action="<?= BASE_URL ?>items.php">
            <input type="hidden" name="action" id="itemFormAction" value="create_item">
            <input type="hidden" name="id" id="itemId" value="">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <!-- اسم الصنف -->
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label required">اسم الصنف بالكامل</label>
                    <input type="text" name="name" id="itemName" class="form-control" placeholder="مثال: زيت عباد الشمس سلايت 1.5 لتر" required>
                </div>

                <!-- الباركود مع زر توليد تلقائي -->
                <div class="form-group">
                    <label class="form-label required">الباركود الدولي / المحلي</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="barcode" id="itemBarcode" class="form-control" placeholder="امسح الباركود أو ولد تلقائياً" required>
                        <button type="button" class="btn btn-outline" onclick="generateBarcode()" title="توليد باركود تلقائي مصري (622...)" style="white-space: nowrap;">
                            <i class="fas fa-magic"></i> توليد
                        </button>
                    </div>
                </div>

                <!-- الكود الإضافي SKU -->
                <div class="form-group">
                    <label class="form-label">كود الصنف الإضافي (SKU)</label>
                    <input type="text" name="sku" id="itemSku" class="form-control" placeholder="مثال: OIL-SLITE-15">
                </div>

                <!-- التصنيف -->
                <div class="form-group">
                    <label class="form-label required">القسم / التصنيف</label>
                    <select name="category_id" id="itemCategory" class="form-control" required>
                        <option value="">اختر القسم...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- وحدة القياس -->
                <div class="form-group">
                    <label class="form-label required">وحدة القياس</label>
                    <select name="unit_id" id="itemUnit" class="form-control" required>
                        <?php foreach ($units as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['symbol']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- خيار الميزان الإلكتروني -->
                <div class="form-group" style="grid-column: 1 / -1; background: rgba(16, 185, 129, 0.1); padding: 0.85rem 1rem; border-radius: 8px; border: 1.5px solid rgba(16, 185, 129, 0.35);">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--brand-emerald); font-weight: 800; margin: 0;">
                            <input type="checkbox" name="is_scale" id="itemIsScale" value="1" onchange="toggleScalePlu(this.checked)">
                            <span>صنف ميزان باركود وزني (أجبان، لحوم، فواكه، خضار) ⚖️</span>
                        </label>
                        <div id="scalePluWrap" style="display: none; align-items: center; gap: 0.5rem;">
                            <label class="form-label" style="margin: 0; font-size: 0.82rem; color: var(--text-main); font-weight: 700;">كود PLU للميزان (5 أرقام):</label>
                            <input type="text" name="scale_plu" id="itemScalePlu" class="form-control" placeholder="00001" maxlength="5" style="width: 100px; padding: 4px 8px; height: 32px; font-family: var(--font-mono); font-weight: 700;">
                        </div>
                    </div>
                </div>

                <!-- تسعير: سعر التكلفة -->
                <div class="form-group">
                    <label class="form-label required">سعر التكلفة (سعر الشراء)</label>
                    <div style="position: relative;">
                        <input type="number" step="0.01" min="0" name="cost_price" id="itemCostPrice" class="form-control" placeholder="0.00" oninput="calculateMargin()" required style="font-weight: 700;">
                        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem; font-weight: 700;">ج.م</span>
                    </div>
                </div>

                <!-- تسعير: سعر البيع القطاعي -->
                <div class="form-group">
                    <label class="form-label required">سعر البيع القطاعي (للجمهور)</label>
                    <div style="position: relative;">
                        <input type="number" step="0.01" min="0" name="retail_price" id="itemRetailPrice" class="form-control" placeholder="0.00" oninput="calculateMargin()" required style="color: var(--brand-blue); font-weight: 800;">
                        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--brand-blue); font-size: 0.8rem; font-weight: 800;">ج.م</span>
                    </div>
                </div>

                <!-- حاسبة هامش الربح الحي -->
                <div style="grid-column: 1 / -1; background: rgba(2, 132, 199, 0.08); padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid rgba(2, 132, 199, 0.25); display: flex; justify-content: space-between; align-items: center; font-size: 0.88rem; flex-wrap: wrap; gap: 0.5rem;">
                    <span style="color: var(--text-main); font-weight: 700;">مؤشر الربحية التقديرية:</span>
                    <div>
                        قيمة الربح: <strong id="profitValue" style="color: var(--brand-blue); font-weight: 800;">0.00 ج.م</strong>
                        <span style="margin: 0 0.5rem; color: var(--border-color);">|</span>
                        نسبة هامش الربح: <strong id="profitPercent" style="color: var(--brand-emerald); font-weight: 800;">0.0%</strong>
                    </div>
                </div>

                <!-- تسعير: سعر الجملة -->
                <div class="form-group">
                    <label class="form-label">سعر الجملة</label>
                    <div style="position: relative;">
                        <input type="number" step="0.01" min="0" name="wholesale_price" id="itemWholesalePrice" class="form-control" placeholder="0.00">
                        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;">ج.م</span>
                    </div>
                </div>

                <!-- تسعير: أقل سعر بيع مسموح (حماية من تلاعب الكاشير) -->
                <div class="form-group">
                    <label class="form-label">أقل سعر بيع مسموح (الحد الأدنى)</label>
                    <div style="position: relative;">
                        <input type="number" step="0.01" min="0" name="min_sale_price" id="itemMinSalePrice" class="form-control" placeholder="0.00">
                        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;">ج.م</span>
                    </div>
                </div>

                <!-- الضريبة وحد الطلب -->
                <div class="form-group">
                    <label class="form-label">ضريبة القيمة المضافة (%)</label>
                    <select name="tax_rate" id="itemTaxRate" class="form-control">
                        <option value="14.00">14% (النسبة القياسية المصرية)</option>
                        <option value="0.00">0% (معفى من الضريبة)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">حد إعادة الطلب (للتنبيه عند النقص)</label>
                    <input type="number" step="0.01" min="0" name="reorder_level" id="itemReorderLevel" class="form-control" value="5.00">
                </div>

                <!-- الرصيد الافتتاحي (يظهر فقط عند إضافة صنف جديد) -->
                <div id="openingStockSection" style="grid-column: 1 / -1; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; background: var(--bg-body); padding: 0.85rem 1rem; border-radius: 8px; border: 1.5px dashed var(--border-color);">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">الرصيد الافتتاحي الأولي (اختياري)</label>
                        <input type="number" step="0.01" min="0" name="opening_stock" id="itemOpeningStock" class="form-control" placeholder="0.00">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">المستودع المستلم للرصيد</label>
                        <select name="opening_store_id" id="itemOpeningStore" class="form-control">
                            <?php foreach ($stores as $st): ?>
                                <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- تفعيل الصنف -->
                <div class="form-group" style="grid-column: 1 / -1; margin-bottom: 0;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-main); font-weight: 700;">
                        <input type="checkbox" name="is_active" id="itemIsActive" value="1" checked>
                        <span>صنف نشط ومتاح للبيع في الكاشير</span>
                    </label>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <button type="button" class="btn btn-outline" onclick="closeItemModal()">إلغاء</button>
                <button type="submit" class="btn btn-primary" id="saveItemBtn">
                    <i class="fas fa-save"></i> حفظ الصنف
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: تسوية جردية سريعة ================= -->
<div id="quickAdjustModal" class="modal-backdrop" style="display: none;">
    <div class="card modal-dialog" style="max-width: 520px;">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-sliders-h" style="color: var(--brand-amber);"></i>
                <span>تسوية جردية سريعة للمخزون</span>
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeQuickAdjustModal()" title="إغلاق">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="quickAdjustForm" onsubmit="submitQuickAdjust(event)">
            <input type="hidden" id="adjustItemId" value="">
            
            <div style="margin-bottom: 1rem; background: var(--bg-body); padding: 0.85rem; border-radius: 8px; border: 1px solid var(--border-color);">
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">الصنف:</div>
                <div id="adjustItemName" style="font-weight: 800; color: var(--text-main); font-size: 1.05rem;"></div>
            </div>

            <div class="form-group">
                <label class="form-label required">المستودع المطلوب تسويته</label>
                <select id="adjustStoreId" class="form-control" required onchange="fetchCurrentStoreStock()">
                    <?php foreach ($stores as $st): ?>
                        <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">الرصيد الدفتري الحالي</label>
                    <input type="text" id="adjustCurrentStock" class="form-control" readonly style="background: var(--bg-body); color: var(--text-main); font-weight: 800;">
                </div>
                <div class="form-group">
                    <label class="form-label required">الرصيد الفعلي بعد الجرد</label>
                    <input type="number" step="0.01" id="adjustActualQty" class="form-control" required placeholder="0.00" style="color: var(--brand-blue); font-weight: 800; font-size: 1.15rem;">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label required">سبب التسوية الجردية</label>
                <select id="adjustReasonSelect" class="form-control" onchange="handleReasonChange(this.value)">
                    <option value="جرد دوري روتيني">جرد دوري روتيني</option>
                    <option value="تالف / كسر / منتهي الصلاحية">تالف / كسر / منتهي الصلاحية</option>
                    <option value="عجز بالمخزن أثناء الجرد">عجز بالمخزن أثناء الجرد</option>
                    <option value="زيادة غير مسجلة">زيادة غير مسجلة</option>
                    <option value="other">سبب آخر (كتابة يدوية)...</option>
                </select>
                <input type="text" id="adjustCustomReason" class="form-control" placeholder="اكتب سبب التسوية..." style="display: none; margin-top: 0.5rem;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <button type="button" class="btn btn-outline" onclick="closeQuickAdjustModal()">إلغاء</button>
                <button type="submit" class="btn btn-primary" id="adjustSubmitBtn">
                    <i class="fas fa-check-circle"></i> اعتماد التسوية
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 3: كارت الصنف وسجل الحركات ================= -->
<div id="itemCardModal" class="modal-backdrop" style="display: none;">
    <div class="card modal-dialog" style="max-width: 850px;">
        <div class="modal-header">
            <div>
                <h3 id="cardItemName" class="modal-title"></h3>
                <div id="cardItemBarcode" class="barcode-tag" style="margin-top: 4px;"></div>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeItemCardModal()" title="إغلاق">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Stock in Each Store -->
        <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.75rem;">
            <i class="fas fa-warehouse" style="color: var(--brand-blue); margin-left: 0.5rem;"></i>
            الأرصدة الحالية في المستودعات
        </h4>
        <div id="cardStoreStocks" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem; margin-bottom: 1.5rem;">
            <!-- Rendered by JS -->
        </div>

        <!-- Stock Movement History Table -->
        <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.75rem;">
            <i class="fas fa-history" style="color: var(--brand-emerald); margin-left: 0.5rem;"></i>
            سجل آخر حركات المخزون للصنف (Audit Trail)
        </h4>
        <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
            <table class="table-pro data-table" style="font-size: 0.82rem;">
                <thead>
                    <tr>
                        <th>التاريخ والوقت</th>
                        <th>المستودع</th>
                        <th>نوع الحركة</th>
                        <th>الكمية</th>
                        <th>الرصيد بعد الحركة</th>
                        <th>الملاحظات والمستخدم</th>
                    </tr>
                </thead>
                <tbody id="cardMovesTableBody">
                    <!-- Rendered by JS -->
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem;">
            <button type="button" class="btn btn-outline" onclick="closeItemCardModal()">إغلاق</button>
        </div>
    </div>
</div>

<script>
// Open Modal for Creating New Item
function openCreateItemModal() {
    document.getElementById('itemModalTitle').innerHTML = '<i class="fas fa-plus-circle" style="color: var(--brand-amber); margin-left: 0.5rem;"></i> إضافة صنف جديد';
    document.getElementById('itemFormAction').value = 'create_item';
    document.getElementById('itemId').value = '';
    document.getElementById('itemForm').reset();
    document.getElementById('itemIsActive').checked = true;
    document.getElementById('openingStockSection').style.display = 'grid';
    toggleScalePlu(false);
    calculateMargin();
    document.getElementById('itemModal').style.display = 'flex';
}

// Open Modal for Editing Item
function openEditItemModal(item) {
    document.getElementById('itemModalTitle').innerHTML = '<i class="fas fa-edit" style="color: #38bdf8; margin-left: 0.5rem;"></i> تعديل صنف: ' + item.name;
    document.getElementById('itemFormAction').value = 'update_item';
    document.getElementById('itemId').value = item.id;
    document.getElementById('itemName').value = item.name;
    document.getElementById('itemBarcode').value = item.barcode;
    document.getElementById('itemSku').value = item.sku || '';
    document.getElementById('itemCategory').value = item.category_id || '';
    document.getElementById('itemUnit').value = item.unit_id;
    document.getElementById('itemCostPrice').value = item.cost_price;
    document.getElementById('itemRetailPrice').value = item.retail_price;
    document.getElementById('itemWholesalePrice').value = item.wholesale_price || '';
    document.getElementById('itemMinSalePrice').value = item.min_sale_price || '';
    document.getElementById('itemTaxRate').value = item.tax_rate;
    document.getElementById('itemReorderLevel').value = item.reorder_level;
    document.getElementById('itemIsActive').checked = (item.is_active == 1);
    
    // Scale Settings
    const isScale = (item.is_scale == 1);
    document.getElementById('itemIsScale').checked = isScale;
    document.getElementById('itemScalePlu').value = item.scale_plu || '';
    toggleScalePlu(isScale);

    // Opening stock is hidden during edit
    document.getElementById('openingStockSection').style.display = 'none';

    calculateMargin();
    document.getElementById('itemModal').style.display = 'flex';
}

function closeItemModal() {
    document.getElementById('itemModal').style.display = 'none';
}

function toggleScalePlu(checked) {
    document.getElementById('scalePluWrap').style.display = checked ? 'flex' : 'none';
}

// Live Profit Margin Calculator
function calculateMargin() {
    const cost = parseFloat(document.getElementById('itemCostPrice').value) || 0;
    const retail = parseFloat(document.getElementById('itemRetailPrice').value) || 0;
    const diff = retail - cost;
    const pct = (cost > 0) ? ((diff / cost) * 100).toFixed(1) : 0;

    document.getElementById('profitValue').textContent = diff.toFixed(2) + ' ج.م';
    document.getElementById('profitPercent').textContent = pct + '%';
    document.getElementById('profitPercent').style.color = (diff >= 0) ? '#10b981' : '#f87171';
}

// Auto-generate barcode via API
async function generateBarcode() {
    try {
        const res = await apiRequest('api/inventory.php?action=generate_barcode');
        if (res.success && res.barcode) {
            document.getElementById('itemBarcode').value = res.barcode;
        }
    } catch (e) {
        showToast('تعذر توليد الباركود: ' + e.message, 'error');
    }
}

// Quick Stock Adjust
function openQuickAdjustModal(itemId, itemName, currentStock) {
    document.getElementById('adjustItemId').value = itemId;
    document.getElementById('adjustItemName').textContent = itemName;
    document.getElementById('adjustCurrentStock').value = currentStock.toFixed(2);
    document.getElementById('adjustActualQty').value = currentStock.toFixed(2);
    document.getElementById('quickAdjustModal').style.display = 'flex';
}

function closeQuickAdjustModal() {
    document.getElementById('quickAdjustModal').style.display = 'none';
}

function handleReasonChange(val) {
    document.getElementById('adjustCustomReason').style.display = (val === 'other') ? 'block' : 'none';
}

async function fetchCurrentStoreStock() {
    const itemId = document.getElementById('adjustItemId').value;
    const storeId = document.getElementById('adjustStoreId').value;
    try {
        const res = await apiRequest('api/inventory.php?action=get_item&id=' + itemId);
        if (res.success && res.data.store_stocks) {
            const match = res.data.store_stocks.find(s => s.store_id == storeId);
            const stock = match ? parseFloat(match.current_stock) : 0;
            document.getElementById('adjustCurrentStock').value = stock.toFixed(2);
            document.getElementById('adjustActualQty').value = stock.toFixed(2);
        }
    } catch (e) {
        console.error(e);
    }
}

async function submitQuickAdjust(e) {
    e.preventDefault();
    const itemId = document.getElementById('adjustItemId').value;
    const storeId = document.getElementById('adjustStoreId').value;
    const actualQty = document.getElementById('adjustActualQty').value;
    
    let reason = document.getElementById('adjustReasonSelect').value;
    if (reason === 'other') {
        reason = document.getElementById('adjustCustomReason').value.trim() || 'تسوية يدوية خاصة';
    }

    try {
        const res = await apiRequest('api/inventory.php', 'POST', {
            action: 'quick_adjust',
            item_id: itemId,
            store_id: storeId,
            actual_qty: actualQty,
            reason: reason,
            csrf_token: '<?= Security::generateCsrf() ?>'
        });

        if (res.success) {
            showToast('تمت التسوية الجردية وتحديث الرصيد بنجاح!', 'success');
            closeQuickAdjustModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(res.message || 'حدث خطأ أثناء التسوية', 'error');
        }
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// Show Item Card Modal
async function showItemCard(itemId) {
    try {
        const [itemRes, histRes] = await Promise.all([
            apiRequest('api/inventory.php?action=get_item&id=' + itemId),
            apiRequest('api/inventory.php?action=get_history&id=' + itemId)
        ]);

        if (!itemRes.success) {
            showToast('تعذر جلب تفاصيل الصنف', 'error');
            return;
        }

        const item = itemRes.data;
        document.getElementById('cardItemName').textContent = item.name;
        document.getElementById('cardItemBarcode').textContent = 'باركود: ' + item.barcode + ' | كود: ' + (item.sku || '---');

        // Render store stocks
        let storesHtml = '';
        if (item.store_stocks && item.store_stocks.length > 0) {
            item.store_stocks.forEach(st => {
                storesHtml += `
                    <div style="background: var(--bg-body); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.85rem;">
                        <div style="font-size: 0.82rem; color: var(--text-muted); font-weight: 600;">${st.store_name}</div>
                        <div style="font-size: 1.2rem; font-weight: 800; color: var(--brand-blue); margin-top: 4px;">
                            ${parseFloat(st.current_stock).toFixed(2)} <span style="font-size: 0.8rem; color: var(--text-main); font-weight: 600;">${item.unit_symbol}</span>
                        </div>
                    </div>
                `;
            });
        }
        document.getElementById('cardStoreStocks').innerHTML = storesHtml;

        // Render Moves
        let movesHtml = '';
        if (histRes.success && histRes.data.length > 0) {
            histRes.data.forEach(m => {
                const isPositive = parseFloat(m.quantity) > 0;
                const qtyColor = isPositive ? '#10b981' : '#f87171';
                const sign = isPositive ? '+' : '';
                movesHtml += `
                    <tr>
                        <td>${m.created_at}</td>
                        <td>${m.store_name}</td>
                        <td><span class="badge badge-blue">${m.move_type}</span></td>
                        <td style="font-weight: 800; color: ${qtyColor};">${sign}${parseFloat(m.quantity).toFixed(2)}</td>
                        <td style="font-weight: 800; color: var(--text-main);">${parseFloat(m.balance_after).toFixed(2)}</td>
                        <td>${m.notes || '---'} <span style="color: var(--text-muted); font-size: 0.75rem;">(${m.user_name || 'نظام'})</span></td>
                    </tr>
                `;
            });
        } else {
            movesHtml = '<tr><td colspan="6" style="text-align: center; color: var(--text-muted);">لا توجد حركات سابقة مسجلة لهذا الصنف.</td></tr>';
        }
        document.getElementById('cardMovesTableBody').innerHTML = movesHtml;

        document.getElementById('itemCardModal').style.display = 'flex';
    } catch (e) {
        showToast(e.message, 'error');
    }
}

function closeItemCardModal() {
    document.getElementById('itemCardModal').style.display = 'none';
}

function confirmDeleteItem(id, name) {
    if (confirm('هل أنت متأكد من رغبتك في حذف / إلغاء تفعيل الصنف: "' + name + '"؟')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= BASE_URL ?>items.php';
        
        const act = document.createElement('input');
        act.type = 'hidden'; act.name = 'action'; act.value = 'delete_item';
        form.appendChild(act);

        const itemId = document.createElement('input');
        itemId.type = 'hidden'; itemId.name = 'id'; itemId.value = id;
        form.appendChild(itemId);

        const csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = 'csrf_token'; csrf.value = '<?= Security::generateCsrf() ?>';
        form.appendChild(csrf);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>

