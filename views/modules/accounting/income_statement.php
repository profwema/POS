<?php
/**
 * شاشة قائمة الدخل والأرباح والخسائر
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "قائمة الدخل والأرباح والخسائر";
$inc = $incomeStatement;
?>

<div class="income-statement-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-chart-line" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                قائمة الدخل والأرباح والخسائر (Income Statement)
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                التقرير المالي الختامي لقياس الأداء التشغيلي: الإيرادات، تكلفة البضاعة المباعة، مجمل الربح، والمصروفات
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" onclick="window.print()">
                <i class="fas fa-print"></i>
                طباعة قائمة الدخل
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
        <a href="<?= BASE_URL ?>accounting.php?tab=vouchers" class="btn btn-outline" style="border-radius: var(--radius-md);">
            <i class="fas fa-money-check-alt" style="margin-left: 4px;"></i> سندات القبض والصرف
        </a>
        <a href="<?= BASE_URL ?>accounting.php?tab=trial_balance" class="btn btn-outline" style="border-radius: var(--radius-md);">
            <i class="fas fa-balance-scale" style="margin-left: 4px;"></i> ميزان المراجعة
        </a>
        <a href="<?= BASE_URL ?>accounting.php?tab=income_statement" class="btn btn-primary" style="border-radius: var(--radius-md);">
            <i class="fas fa-chart-line" style="margin-left: 4px;"></i> قائمة الدخل
        </a>
    </div>

    <!-- Period Filter -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
        <form method="GET" action="<?= BASE_URL ?>accounting.php" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
            <input type="hidden" name="tab" value="income_statement">

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i>
                عرض قائمة الدخل للفترة
            </button>
        </form>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">
        <div class="stat-card">
            <div class="stat-icon emerald">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($inc['total_revenue']) ?></div>
                <div class="stat-title">إجمالي الإيرادات</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                <i class="fas fa-shopping-basket"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($inc['total_cogs']) ?></div>
                <div class="stat-title">تكلفة البضاعة المباعة (COGS)</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-percentage"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($inc['gross_profit']) ?></div>
                <div class="stat-title">مجمل الربح التجاري</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: <?= ($inc['net_income'] >= 0) ? 'rgba(16, 185, 129, 0.12)' : 'rgba(239, 68, 68, 0.12)' ?>; color: <?= ($inc['net_income'] >= 0) ? '#10b981' : '#ef4444' ?>;">
                <i class="fas <?= ($inc['net_income'] >= 0) ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' ?>"></i>
            </div>
            <div>
                <div class="stat-val" style="color: <?= ($inc['net_income'] >= 0) ? '#10b981' : '#ef4444' ?>;">
                    <?= Helpers::formatCurrency($inc['net_income']) ?>
                </div>
                <div class="stat-title">صافي <?= ($inc['net_income'] >= 0) ? 'الربح' : 'الخسارة' ?> التشغيلي</div>
            </div>
        </div>
    </div>

    <!-- Income Statement Detailed Structure Card -->
    <div class="card" style="padding: 1.75rem;">
        <!-- Section 1: Revenues -->
        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 0.75rem;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #10b981;">
                    <i class="fas fa-arrow-circle-up" style="margin-left: 0.4rem;"></i>
                    1. إيرادات النشاط التجاري والمبيعات
                </h3>
                <strong style="font-size: 1.15rem; color: #10b981;"><?= Helpers::formatCurrency($inc['total_revenue']) ?></strong>
            </div>

            <table class="table" style="margin-bottom: 0;">
                <tbody>
                    <?php if (empty($inc['revenue_accounts'])): ?>
                        <tr><td colspan="2" style="color: var(--text-muted); padding: 0.75rem;">لا توجد إيرادات مسجلة لهذه الفترة.</td></tr>
                    <?php else: ?>
                        <?php foreach ($inc['revenue_accounts'] as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['account_code']) ?> - <?= htmlspecialchars($r['name']) ?></td>
                                <td style="text-align: left; font-weight: 700;"><?= Helpers::formatCurrency($r['net_revenue']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Section 2: COGS -->
        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 0.75rem;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #f59e0b;">
                    <i class="fas fa-box" style="margin-left: 0.4rem;"></i>
                    2. يخصم: تكلفة البضاعة المباعة (Cost of Goods Sold)
                </h3>
                <strong style="font-size: 1.15rem; color: #f59e0b;">(<?= Helpers::formatCurrency($inc['total_cogs']) ?>)</strong>
            </div>

            <table class="table" style="margin-bottom: 0;">
                <tbody>
                    <?php if (empty($inc['cogs_accounts'])): ?>
                        <tr><td colspan="2" style="color: var(--text-muted); padding: 0.75rem;">لا توجد تكلفة مبيعات مباشرة مسجلة.</td></tr>
                    <?php else: ?>
                        <?php foreach ($inc['cogs_accounts'] as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['account_code']) ?> - <?= htmlspecialchars($c['name']) ?></td>
                                <td style="text-align: left; font-weight: 700;"><?= Helpers::formatCurrency($c['net_cogs']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Gross Profit Line -->
        <div style="background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.25); padding: 1rem 1.25rem; border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <strong style="font-size: 1.15rem; color: #38bdf8;">= مجمل الربح التجاري (Gross Profit):</strong>
            <strong style="font-size: 1.3rem; color: #38bdf8;"><?= Helpers::formatCurrency($inc['gross_profit']) ?></strong>
        </div>

        <!-- Section 3: Operating Expenses -->
        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 0.75rem;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #a855f7;">
                    <i class="fas fa-file-invoice-dollar" style="margin-left: 0.4rem;"></i>
                    3. يخصم: المصروفات العمومية والتشغيلية
                </h3>
                <strong style="font-size: 1.15rem; color: #a855f7;">(<?= Helpers::formatCurrency($inc['total_expenses']) ?>)</strong>
            </div>

            <table class="table" style="margin-bottom: 0;">
                <tbody>
                    <?php if (empty($inc['expense_accounts'])): ?>
                        <tr><td colspan="2" style="color: var(--text-muted); padding: 0.75rem;">لا توجد مصروفات عمومية مسجلة لهذه الفترة.</td></tr>
                    <?php else: ?>
                        <?php foreach ($inc['expense_accounts'] as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e['account_code']) ?> - <?= htmlspecialchars($e['name']) ?></td>
                                <td style="text-align: left; font-weight: 700;"><?= Helpers::formatCurrency($e['net_expense']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Final Net Income Line -->
        <div style="background: <?= ($inc['net_income'] >= 0) ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; border: 2px solid <?= ($inc['net_income'] >= 0) ? 'rgba(16, 185, 129, 0.4)' : 'rgba(239, 68, 68, 0.4)' ?>; padding: 1.25rem 1.5rem; border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
            <strong style="font-size: 1.25rem; color: <?= ($inc['net_income'] >= 0) ? '#10b981' : '#ef4444' ?>;">
                = صافي <?= ($inc['net_income'] >= 0) ? 'الربح' : 'الخسارة' ?> للفترة المالية (Net Income):
            </strong>
            <strong style="font-size: 1.6rem; color: <?= ($inc['net_income'] >= 0) ? '#10b981' : '#ef4444' ?>;">
                <?= Helpers::formatCurrency($inc['net_income']) ?>
            </strong>
        </div>
    </div>
</div>
