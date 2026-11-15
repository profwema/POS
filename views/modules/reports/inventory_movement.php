<?php
/**
 * تقرير حركة المخزون وتحليل دوران الأصناف والأصناف الراكدة
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "تقرير حركة المخزون وتحليل دوران الأصناف";
$topSold = $movementData['top_sold'] ?? [];
$deadStock = $movementData['dead_stock'] ?? [];
$reorderAlerts = $movementData['reorder_alerts'] ?? [];
?>

<div class="inventory-movement-page">
    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-boxes" style="color: #f59e0b; margin-left: 0.5rem;"></i>
                تقرير حركة المخزون ودوران الأصناف
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                تحليل الأصناف الأكثر رواجاً، البضائع الراكدة، وتنبيهات النواقص وحد إعادة الطلب
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" onclick="window.print()">
                <i class="fas fa-print"></i>
                طباعة التقرير
            </button>
            <a href="<?= BASE_URL ?>reports.php" class="btn btn-outline">
                <i class="fas fa-arrow-right"></i>
                مركز التقارير
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
        <form method="GET" action="<?= BASE_URL ?>reports.php" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
            <input type="hidden" name="type" value="movement">

            <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
                <label class="form-label" style="font-size: 0.82rem;">من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
                <label class="form-label" style="font-size: 0.82rem;">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0; min-width: 180px;">
                <label class="form-label" style="font-size: 0.82rem;">المستودع / المخزن</label>
                <select name="store_id" class="form-control">
                    <?php foreach ($stores as $st): ?>
                        <option value="<?= $st['id'] ?>" <?= ($storeId == $st['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($st['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i>
                تحديث تحليل الحركة
            </button>
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="card stat-card" style="padding: 1.25rem; border-right: 4px solid #10b981;">
            <div class="stat-icon emerald">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">سريعة الدوران (Top 10)</div>
                <div class="stat-value" style="color: #10b981;">
                    <?= count($topSold) ?> صنف
                </div>
                <span class="stat-sub">الأعلى طلباً ومبيعاً</span>
            </div>
        </div>

        <div class="card stat-card" style="padding: 1.25rem; border-right: 4px solid #ef4444;">
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="fas fa-snowflake"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">أصناف راكدة (Dead Stock)</div>
                <div class="stat-value" style="color: #ef4444;">
                    <?= count($deadStock) ?> صنف
                </div>
                <span class="stat-sub">لم تباع إطلاقاً خلال الفترة</span>
            </div>
        </div>

        <div class="card stat-card" style="padding: 1.25rem; border-right: 4px solid #f59e0b;">
            <div class="stat-icon amber">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">نواقص تحتاج إعادة طلب</div>
                <div class="stat-value" style="color: #f59e0b;">
                    <?= count($reorderAlerts) ?> صنف
                </div>
                <span class="stat-sub">الرصيد أقل من حد الأمان</span>
            </div>
        </div>
    </div>

    <!-- Navigation Pills / Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; flex-wrap: wrap;">
        <button type="button" class="btn btn-outline active tab-nav-btn" onclick="switchSection('fast-moving', this)" style="border-radius: 20px;">
            <i class="fas fa-fire text-emerald"></i>
            الأصناف الأكثر مبيعاً (Top 10)
        </button>
        <button type="button" class="btn btn-outline tab-nav-btn" onclick="switchSection('dead-stock', this)" style="border-radius: 20px;">
            <i class="fas fa-snowflake text-danger"></i>
            البضاعة الراكدة بدون حركة (<?= count($deadStock) ?>)
        </button>
        <button type="button" class="btn btn-outline tab-nav-btn" onclick="switchSection('reorder-alerts', this)" style="border-radius: 20px;">
            <i class="fas fa-bell text-amber"></i>
            تنبيهات حد إعادة الطلب (<?= count($reorderAlerts) ?>)
        </button>
    </div>

    <!-- Section 1: Fast Moving -->
    <div id="section-fast-moving" class="section-pane card" style="overflow: hidden; margin-bottom: 2rem;">
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #10b981;">
                <i class="fas fa-fire" style="margin-left: 0.5rem;"></i>
                قائمة الأصناف الأكثر طلباً ومبيعاً خلال الفترة
            </h3>
        </div>
        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
                <thead>
                    <tr style="background: var(--bg-surface); text-align: right;">
                        <th style="padding: 1rem; width: 50px;">#</th>
                        <th style="padding: 1rem;">اسم الصنف</th>
                        <th style="padding: 1rem;">القسم</th>
                        <th style="padding: 1rem; text-align: left;">سعر البيع</th>
                        <th style="padding: 1rem; text-align: center;">الكمية المباعة</th>
                        <th style="padding: 1rem; text-align: left;">إجمالي الإيراد</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($topSold)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                لا توجد مبيعات مسجلة في هذا المستودع خلال الفترة.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($topSold as $idx => $item): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.85rem;">
                                    <?= $idx + 1 ?>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <div style="font-weight: 700; color: var(--text-main);">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">
                                        <i class="fas fa-barcode"></i> <?= htmlspecialchars($item['barcode'] ?? '-') ?>
                                    </div>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <span class="badge badge-outline">
                                        <?= htmlspecialchars($item['category_name'] ?? 'عام') ?>
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; font-family: monospace;">
                                    <?= number_format($item['retail_price'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center;">
                                    <span class="badge badge-emerald" style="font-size: 0.9rem; font-weight: 700;">
                                        <?= number_format($item['sold_qty'], 2) ?>
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; font-weight: 800; color: #10b981; font-family: monospace;">
                                    <?= Helpers::formatCurrency($item['total_revenue']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Dead Stock -->
    <div id="section-dead-stock" class="section-pane card" style="display: none; overflow: hidden; margin-bottom: 2rem;">
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #ef4444;">
                <i class="fas fa-snowflake" style="margin-left: 0.5rem;"></i>
                الأصناف الراكدة (مخزون لم يباع إطلاقاً خلال الفترة - رأس مال مجمد)
            </h3>
        </div>
        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
                <thead>
                    <tr style="background: var(--bg-surface); text-align: right;">
                        <th style="padding: 1rem; width: 50px;">#</th>
                        <th style="padding: 1rem;">اسم الصنف</th>
                        <th style="padding: 1rem;">القسم</th>
                        <th style="padding: 1rem; text-align: center;">الرصيد بالمخزن</th>
                        <th style="padding: 1rem; text-align: left;">سعر التكلفة</th>
                        <th style="padding: 1rem; text-align: left;">قيمة رأس المال المجمد</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deadStock)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                ممتاز! لا توجد أصناف راكدة بدون حركة في هذا المخزن.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($deadStock as $idx => $item): ?>
                            <?php $frozenValue = (float)$item['current_stock'] * (float)$item['cost_price']; ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.85rem;">
                                    <?= $idx + 1 ?>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <div style="font-weight: 700; color: var(--text-main);">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">
                                        <i class="fas fa-barcode"></i> <?= htmlspecialchars($item['barcode'] ?? '-') ?>
                                    </div>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <span class="badge badge-outline">
                                        <?= htmlspecialchars($item['category_name'] ?? 'عام') ?>
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 700; font-family: monospace;">
                                    <?= number_format($item['current_stock'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; font-family: monospace;">
                                    <?= number_format($item['cost_price'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; font-weight: 800; color: #ef4444; font-family: monospace;">
                                    <?= Helpers::formatCurrency($frozenValue) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 3: Reorder Alerts -->
    <div id="section-reorder-alerts" class="section-pane card" style="display: none; overflow: hidden; margin-bottom: 2rem;">
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #f59e0b;">
                <i class="fas fa-bell" style="margin-left: 0.5rem;"></i>
                تنبيهات نواقص الرف والأصناف التي بلغت حد إعادة الطلب
            </h3>
            <a href="<?= BASE_URL ?>purchases.php?action=create" class="btn btn-primary" style="padding: 0.4rem 0.85rem; font-size: 0.82rem;">
                <i class="fas fa-plus"></i>
                إنشاء أمر توريد / شراء
            </a>
        </div>
        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
                <thead>
                    <tr style="background: var(--bg-surface); text-align: right;">
                        <th style="padding: 1rem; width: 50px;">#</th>
                        <th style="padding: 1rem;">اسم الصنف</th>
                        <th style="padding: 1rem;">القسم</th>
                        <th style="padding: 1rem; text-align: center;">الرصيد الحالي</th>
                        <th style="padding: 1rem; text-align: center;">حد إعادة الطلب</th>
                        <th style="padding: 1rem; text-align: center;">الحالة والطلب المقترح</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reorderAlerts)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                رائع! لا توجد نواقص في المخزن، جميع الأرصدة فوق حد الأمان.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reorderAlerts as $idx => $item): ?>
                            <?php
                            $curr = (float)$item['current_stock'];
                            $reorder = (float)$item['reorder_level'];
                            $isOut = $curr <= 0;
                            ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.85rem;">
                                    <?= $idx + 1 ?>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <div style="font-weight: 700; color: var(--text-main);">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">
                                        <i class="fas fa-barcode"></i> <?= htmlspecialchars($item['barcode'] ?? '-') ?>
                                    </div>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <span class="badge badge-outline">
                                        <?= htmlspecialchars($item['category_name'] ?? 'عام') ?>
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 800; font-family: monospace; color: <?= $isOut ? '#ef4444' : '#f59e0b' ?>;">
                                    <?= number_format($curr, 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center; font-family: monospace;">
                                    <?= number_format($reorder, 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center;">
                                    <?php if ($isOut): ?>
                                        <span class="badge badge-danger">
                                            نفد المخزون بالكامل
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-amber">
                                            اقترب من النفاد
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function switchSection(sectionId, btn) {
    document.querySelectorAll('.section-pane').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-nav-btn').forEach(el => el.classList.remove('active'));

    const target = document.getElementById('section-' + sectionId);
    if (target) {
        target.style.display = 'block';
    }
    btn.classList.add('active');
}
</script>

<style>
.tab-nav-btn.active {
    background: var(--brand-amber) !important;
    color: #000 !important;
    border-color: var(--brand-amber) !important;
    font-weight: 700;
}
@media print {
    .app-sidebar, .app-header, .btn, form, .tab-nav-btn {
        display: none !important;
    }
    .section-pane {
        display: block !important;
        margin-bottom: 2rem !important;
        break-inside: avoid;
    }
    body {
        background: #fff !important;
        color: #000 !important;
    }
}
</style>
