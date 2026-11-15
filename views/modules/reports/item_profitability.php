<?php
/**
 * تقرير مبيعات وأرباح الأصناف وهامش الربحية
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "تقرير مبيعات وأرباح الأصناف وهامش الربحية";
?>

<div class="item-profitability-page">
    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-chart-line" style="color: #38bdf8; margin-left: 0.5rem;"></i>
                تقرير مبيعات وأرباح الأصناف وهامش الربحية
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                تحليل دقيق لإيرادات المبيعات مقابل تكلفة البضاعة المباعة ومجمل الأرباح ونسب الهوامش
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
            <input type="hidden" name="type" value="profitability">

            <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
                <label class="form-label" style="font-size: 0.82rem;">من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
                <label class="form-label" style="font-size: 0.82rem;">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0; min-width: 160px;">
                <label class="form-label" style="font-size: 0.82rem;">القسم / التصنيف</label>
                <select name="category_id" class="form-control">
                    <option value="">-- كافة الأقسام --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($categoryId == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 180px;">
                <label class="form-label" style="font-size: 0.82rem;">بحث عن صنف أو باركود</label>
                <input type="text" name="search" class="form-control" placeholder="اسم الصنف أو رقم الباركود..." value="<?= htmlspecialchars($search) ?>">
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i>
                    تطبيق الفلترة
                </button>
                <a href="<?= BASE_URL ?>reports.php?type=profitability" class="btn btn-outline" title="إعادة تعيين">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="card stat-card" style="padding: 1.25rem;">
            <div class="stat-icon blue">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">إجمالي الكميات المباعة</div>
                <div class="stat-value" style="color: #38bdf8;">
                    <?= number_format($totals['sold_qty'] ?? 0, 2) ?>
                </div>
                <span class="stat-sub">قطعة / وحدة</span>
            </div>
        </div>

        <div class="card stat-card" style="padding: 1.25rem;">
            <div class="stat-icon purple">
                <i class="fas fa-cash-register"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">إجمالي الإيرادات (المبيعات)</div>
                <div class="stat-value" style="color: #a855f7;">
                    <?= Helpers::formatCurrency($totals['total_revenue'] ?? 0) ?>
                </div>
                <span class="stat-sub">قيمة البيع المحصلة</span>
            </div>
        </div>

        <div class="card stat-card" style="padding: 1.25rem;">
            <div class="stat-icon amber">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">إجمالي تكلفة البضاعة المباعة</div>
                <div class="stat-value" style="color: #f59e0b;">
                    <?= Helpers::formatCurrency($totals['total_cost'] ?? 0) ?>
                </div>
                <span class="stat-sub">وفق متوسط التكلفة WAC</span>
            </div>
        </div>

        <div class="card stat-card" style="padding: 1.25rem; border-right: 4px solid #10b981;">
            <div class="stat-icon emerald">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">إجمالي مجمل الربح المحقق</div>
                <div class="stat-value" style="color: #10b981;">
                    <?= Helpers::formatCurrency($totals['total_profit'] ?? 0) ?>
                </div>
                <div style="margin-top: 0.25rem;">
                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight: 700;">
                        هامش الربح الإجمالي: <?= $totals['overall_margin'] ?? 0 ?>%
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card" style="overflow: hidden;">
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--text-main);">
                تفاصيل ربحية الأصناف (مرتبة حسب الربح التنازلي)
            </h3>
            <span class="badge badge-primary">
                <?= count($items) ?> صنف مباع
            </span>
        </div>

        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
                <thead>
                    <tr style="background: var(--bg-surface); text-align: right;">
                        <th style="padding: 1rem; width: 50px;">#</th>
                        <th style="padding: 1rem;">اسم الصنف</th>
                        <th style="padding: 1rem;">القسم</th>
                        <th style="padding: 1rem; text-align: center;">الوحدة</th>
                        <th style="padding: 1rem; text-align: left;">سعر التكلفة</th>
                        <th style="padding: 1rem; text-align: left;">سعر البيع</th>
                        <th style="padding: 1rem; text-align: center;">الكمية المباعة</th>
                        <th style="padding: 1rem; text-align: left;">إجمالي الإيراد</th>
                        <th style="padding: 1rem; text-align: left;">إجمالي التكلفة</th>
                        <th style="padding: 1rem; text-align: left;">مجمل الربح</th>
                        <th style="padding: 1rem; text-align: center;">الهامش %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.3;"></i>
                                لا توجد حركات بيع مسجلة للأصناف خلال الفترة المحددة.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $idx => $row): ?>
                            <?php
                            $margin = (float)$row['profit_margin'];
                            $marginColor = '#10b981'; // أخضر
                            $marginBg = 'rgba(16, 185, 129, 0.12)';
                            if ($margin < 15) {
                                $marginColor = '#ef4444'; // أحمر
                                $marginBg = 'rgba(239, 68, 68, 0.12)';
                            } elseif ($margin < 25) {
                                $marginColor = '#f59e0b'; // برتقالي
                                $marginBg = 'rgba(245, 158, 11, 0.12)';
                            }
                            ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.85rem;">
                                    <?= $idx + 1 ?>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <div style="font-weight: 700; color: var(--text-main);">
                                        <?= htmlspecialchars($row['item_name']) ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">
                                        <i class="fas fa-barcode"></i> <?= htmlspecialchars($row['barcode'] ?? '-') ?>
                                    </div>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <span class="badge badge-outline">
                                        <?= htmlspecialchars($row['category_name'] ?? 'عام') ?>
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center; font-size: 0.85rem; color: var(--text-muted);">
                                    <?= htmlspecialchars($row['unit_name'] ?? 'قطعة') ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; font-family: monospace; font-size: 0.9rem;">
                                    <?= number_format($row['cost_price'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; font-family: monospace; font-size: 0.9rem;">
                                    <?= number_format($row['retail_price'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 700;">
                                    <?= number_format($row['total_sold_qty'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; font-weight: 700; color: #a855f7; font-family: monospace;">
                                    <?= number_format($row['total_revenue'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; color: #f59e0b; font-family: monospace;">
                                    <?= number_format($row['total_cost'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; font-weight: 800; color: #10b981; font-family: monospace;">
                                    <?= number_format($row['total_profit'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center;">
                                    <span class="badge" style="background: <?= $marginBg ?>; color: <?= $marginColor ?>; font-weight: 700; font-family: monospace;">
                                        <?= $margin ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($items)): ?>
                    <tfoot style="background: rgba(0,0,0,0.15); font-weight: 800; border-top: 2px solid var(--border-color);">
                        <tr>
                            <td colspan="6" style="padding: 1rem; text-align: right; color: var(--text-main);">
                                الإجمالي العام للفترة
                            </td>
                            <td style="padding: 1rem; text-align: center; color: #38bdf8;">
                                <?= number_format($totals['sold_qty'], 2) ?>
                            </td>
                            <td style="padding: 1rem; text-align: left; color: #a855f7; font-family: monospace;">
                                <?= number_format($totals['total_revenue'], 2) ?>
                            </td>
                            <td style="padding: 1rem; text-align: left; color: #f59e0b; font-family: monospace;">
                                <?= number_format($totals['total_cost'], 2) ?>
                            </td>
                            <td style="padding: 1rem; text-align: left; color: #10b981; font-family: monospace;">
                                <?= number_format($totals['total_profit'], 2) ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <span class="badge badge-emerald" style="font-size: 0.85rem;">
                                    <?= $totals['overall_margin'] ?>%
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    .app-sidebar, .app-header, .btn, form, .stat-sub {
        display: none !important;
    }
    .app-main {
        margin: 0 !important;
        padding: 0 !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    body {
        background: #fff !important;
        color: #000 !important;
    }
    table {
        border-collapse: collapse !important;
        width: 100% !important;
    }
    th, td {
        border: 1px solid #ccc !important;
        color: #000 !important;
    }
}
</style>
