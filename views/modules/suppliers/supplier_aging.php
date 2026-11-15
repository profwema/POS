<?php
/**
 * تقرير أعمار ديون الموردين - Accounts Payable Aging Report
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "تقرير أعمار ديون الموردين";
?>

<div class="supplier-aging-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-hourglass-half" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                تقرير أعمار ديون الموردين (Accounts Payable Aging)
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                تحليل الفواتير المستحقة غير المسددة للموردين وتصنيفها زمنياً وفق فترات الاستحقاق (30، 60، 90، +120 يوماً)
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" onclick="window.print()">
                <i class="fas fa-print"></i>
                طباعة التقرير
            </button>
            <a href="<?= BASE_URL ?>suppliers.php" class="btn btn-outline">
                <i class="fas fa-arrow-right"></i>
                دليل الموردين
            </a>
        </div>
    </div>

    <!-- Aging Summary Cards -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($totals['age_0_30']) ?></div>
                <div class="stat-title">1 - 30 يوماً (مستحقة حديثاً)</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($totals['age_31_60']) ?></div>
                <div class="stat-title">31 - 60 يوماً (شهر إلى شهرين)</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon amber">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($totals['age_61_90']) ?></div>
                <div class="stat-title">61 - 90 يوماً (شهرين إلى 3 أشهر)</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div class="stat-val" style="color: #ef4444;"><?= Helpers::formatCurrency($totals['age_over_90']) ?></div>
                <div class="stat-title">أكثر من 90 يوماً (متأخرات حرجة)</div>
            </div>
        </div>
    </div>

    <!-- Aging Detailed Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th>المورد والشركة</th>
                        <th>الهاتف</th>
                        <th>أجل السداد المعتمد</th>
                        <th>1 - 30 يوماً</th>
                        <th>31 - 60 يوماً</th>
                        <th>61 - 90 يوماً</th>
                        <th style="color: #ef4444;">+90 يوماً</th>
                        <th style="color: var(--brand-amber); text-align: left;">إجمالي المديونية المستحقة</th>
                        <th style="text-align: center; width: 100px;">كشف الحساب</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($suppliers)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                                <i class="fas fa-check-circle fa-3x" style="color: #10b981; margin-bottom: 1rem; display: block;"></i>
                                لا توجد مديونيات متأخرة مستحقة لأي مورد حالياً. كافة الفواتير مسددة بالكامل.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($suppliers as $s): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-main);">
                                        <?= htmlspecialchars($s['supplier_name']) ?>
                                    </div>
                                    <?php if (!empty($s['company_name'])): ?>
                                        <small style="color: var(--text-muted);"><?= htmlspecialchars($s['company_name']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td style="font-family: monospace; font-size: 0.88rem; color: var(--text-muted);">
                                    <?= htmlspecialchars($s['phone']) ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: var(--text-main);">
                                        <?= (int)$s['payment_terms_days'] ?> يوم
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: <?= ($s['age_0_30'] > 0) ? '#10b981' : 'var(--text-muted)' ?>;">
                                    <?= ($s['age_0_30'] > 0) ? Helpers::formatCurrency($s['age_0_30']) : '-' ?>
                                </td>
                                <td style="font-weight: 600; color: <?= ($s['age_31_60'] > 0) ? '#38bdf8' : 'var(--text-muted)' ?>;">
                                    <?= ($s['age_31_60'] > 0) ? Helpers::formatCurrency($s['age_31_60']) : '-' ?>
                                </td>
                                <td style="font-weight: 600; color: <?= ($s['age_61_90'] > 0) ? '#f59e0b' : 'var(--text-muted)' ?>;">
                                    <?= ($s['age_61_90'] > 0) ? Helpers::formatCurrency($s['age_61_90']) : '-' ?>
                                </td>
                                <td style="font-weight: 700; color: <?= ($s['age_over_90'] > 0) ? '#ef4444' : 'var(--text-muted)' ?>;">
                                    <?= ($s['age_over_90'] > 0) ? Helpers::formatCurrency($s['age_over_90']) : '-' ?>
                                </td>
                                <td style="font-weight: 800; font-size: 1rem; color: var(--brand-amber); text-align: left;">
                                    <?= Helpers::formatCurrency($s['total_outstanding'] ?: $s['current_balance']) ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="<?= BASE_URL ?>suppliers.php?action=statement&id=<?= $s['supplier_id'] ?>" class="btn btn-sm btn-outline" title="كشف الحساب">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background: rgba(148, 163, 184, 0.08); font-weight: 800; font-size: 0.95rem;">
                        <td colspan="3" style="text-align: left; padding: 1rem;">الإجمالي العام لكافة الموردين:</td>
                        <td style="color: #10b981; padding: 1rem;"><?= Helpers::formatCurrency($totals['age_0_30']) ?></td>
                        <td style="color: #38bdf8; padding: 1rem;"><?= Helpers::formatCurrency($totals['age_31_60']) ?></td>
                        <td style="color: #f59e0b; padding: 1rem;"><?= Helpers::formatCurrency($totals['age_61_90']) ?></td>
                        <td style="color: #ef4444; padding: 1rem;"><?= Helpers::formatCurrency($totals['age_over_90']) ?></td>
                        <td style="color: var(--brand-amber); font-size: 1.1rem; padding: 1rem; text-align: left;">
                            <?= Helpers::formatCurrency($totals['total_outstanding']) ?>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
