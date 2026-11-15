<?php
/**
 * شاشة ميزان المراجعة بالأرصدة والمجاميع
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "ميزان المراجعة بالأرصدة والمجاميع";
$tot = $trialBalance['totals'];
?>

<div class="trial-balance-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-balance-scale" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                ميزان المراجعة بالأرصدة والمجاميع (Trial Balance)
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                التقرير الختامي الرقابي للتحقق من اتزان القيود المزدوجة ومطابقة إجمالي الأرصدة المدينة والدائنة
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" onclick="window.print()">
                <i class="fas fa-print"></i>
                طباعة الميزان
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
        <a href="<?= BASE_URL ?>accounting.php?tab=trial_balance" class="btn btn-primary" style="border-radius: var(--radius-md);">
            <i class="fas fa-balance-scale" style="margin-left: 4px;"></i> ميزان المراجعة
        </a>
        <a href="<?= BASE_URL ?>accounting.php?tab=income_statement" class="btn btn-outline" style="border-radius: var(--radius-md);">
            <i class="fas fa-chart-line" style="margin-left: 4px;"></i> قائمة الدخل
        </a>
    </div>

    <!-- Filter & Balance Alert Bar -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <form method="GET" action="<?= BASE_URL ?>accounting.php" style="display: flex; gap: 1rem; align-items: end;">
                <input type="hidden" name="tab" value="trial_balance">

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size: 0.82rem;">حتى تاريخ</label>
                    <input type="date" name="as_of_date" class="form-control" value="<?= htmlspecialchars($asOfDate) ?>">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i>
                    تحديث الميزان
                </button>
            </form>

            <div>
                <?php if ($trialBalance['is_balanced']): ?>
                    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 0.6rem 1.25rem; border-radius: var(--radius-md); font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-check-circle fa-lg"></i>
                        <span>ميزان المراجعة متزن محاسبياً بنسبة 100% (المدين = الدائن)</span>
                    </div>
                <?php else: ?>
                    <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 0.6rem 1.25rem; border-radius: var(--radius-md); font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                        <span>تحذير: ميزان المراجعة غير متزن! يوجد فارق بين المدين والدائن.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Trial Balance Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table" style="margin-bottom: 0; font-size: 0.88rem;">
                <thead>
                    <tr style="text-align: center; border-bottom: 2px solid var(--border-color);">
                        <th rowspan="2" style="vertical-align: middle; width: 100px; text-align: right;">كود الحساب</th>
                        <th rowspan="2" style="vertical-align: middle; text-align: right;">اسم الحساب المحاسبي</th>
                        <th colspan="2" style="background: rgba(148, 163, 184, 0.05); border-left: 1px solid var(--border-color);">أرصدة أول المدة</th>
                        <th colspan="2" style="background: rgba(148, 163, 184, 0.08); border-left: 1px solid var(--border-color);">مجاميع حركات الفترة</th>
                        <th colspan="2" style="background: rgba(148, 163, 184, 0.12);">الأرصدة الختامية</th>
                        <th rowspan="2" style="vertical-align: middle; width: 60px;">دفتر</th>
                    </tr>
                    <tr style="text-align: center;">
                        <th style="color: #38bdf8; width: 110px;">مدين</th>
                        <th style="color: #10b981; width: 110px; border-left: 1px solid var(--border-color);">دائن</th>
                        <th style="color: #38bdf8; width: 110px;">مدين</th>
                        <th style="color: #10b981; width: 110px; border-left: 1px solid var(--border-color);">دائن</th>
                        <th style="color: #38bdf8; width: 120px; font-weight: 800;">رصيد مدين</th>
                        <th style="color: #10b981; width: 120px; font-weight: 800;">رصيد دائن</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($trialBalance['accounts'])): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                                لا توجد حسابات فرعية للتقييد.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($trialBalance['accounts'] as $a): ?>
                            <tr>
                                <td>
                                    <span style="font-family: monospace; font-weight: 700; color: #38bdf8;">
                                        <?= htmlspecialchars($a['account_code']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: var(--text-main); font-size: 0.9rem;">
                                        <?= htmlspecialchars($a['name']) ?>
                                    </strong>
                                </td>
                                <td><?= ($a['opening_debit'] > 0) ? Helpers::formatCurrency($a['opening_debit']) : '-' ?></td>
                                <td style="border-left: 1px solid var(--border-color);"><?= ($a['opening_credit'] > 0) ? Helpers::formatCurrency($a['opening_credit']) : '-' ?></td>
                                <td><?= ($a['total_period_debit'] > 0) ? Helpers::formatCurrency($a['total_period_debit']) : '-' ?></td>
                                <td style="border-left: 1px solid var(--border-color);"><?= ($a['total_period_credit'] > 0) ? Helpers::formatCurrency($a['total_period_credit']) : '-' ?></td>
                                <td style="font-weight: 700; color: #38bdf8;"><?= ($a['ending_debit'] > 0) ? Helpers::formatCurrency($a['ending_debit']) : '-' ?></td>
                                <td style="font-weight: 700; color: #10b981;"><?= ($a['ending_credit'] > 0) ? Helpers::formatCurrency($a['ending_credit']) : '-' ?></td>
                                <td style="text-align: center;">
                                    <a href="<?= BASE_URL ?>accounting.php?tab=statement&account_id=<?= $a['id'] ?>" class="btn btn-sm btn-outline" title="دفتر الأستاذ" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                                        <i class="fas fa-book-open"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background: rgba(148, 163, 184, 0.1); font-weight: 800; font-size: 0.95rem;">
                        <td colspan="2" style="text-align: left; padding: 1rem;">الإجمالي العام لميزان المراجعة:</td>
                        <td style="color: #38bdf8;"><?= Helpers::formatCurrency($tot['opening_debit']) ?></td>
                        <td style="color: #10b981; border-left: 1px solid var(--border-color);"><?= Helpers::formatCurrency($tot['opening_credit']) ?></td>
                        <td style="color: #38bdf8;"><?= Helpers::formatCurrency($tot['moves_debit']) ?></td>
                        <td style="color: #10b981; border-left: 1px solid var(--border-color);"><?= Helpers::formatCurrency($tot['moves_credit']) ?></td>
                        <td style="color: #38bdf8; font-size: 1.05rem;"><?= Helpers::formatCurrency($tot['ending_debit']) ?></td>
                        <td style="color: #10b981; font-size: 1.05rem;"><?= Helpers::formatCurrency($tot['ending_credit']) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
