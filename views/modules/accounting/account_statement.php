<?php
/**
 * كشف حساب دفتر الأستاذ للحساب المالي - General Ledger Statement
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$acc = $statement['account'];
$pageTitle = "دفتر الأستاذ: " . htmlspecialchars($acc['name']);
?>

<div class="account-statement-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-book-open" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                دفتر الأستاذ المساعد (General Ledger Statement)
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                كشف الحركات التفصيلية، القيود المدينة والدائنة، والرصيد التراكمي للحساب
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" onclick="window.print()">
                <i class="fas fa-print"></i>
                طباعة الكشف
            </button>
            <a href="<?= BASE_URL ?>accounting.php?tab=chart" class="btn btn-outline">
                <i class="fas fa-arrow-right"></i>
                شجرة الحسابات
            </a>
        </div>
    </div>

    <!-- Account Details Card -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: center;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <div class="stat-icon blue" style="width: 44px; height: 44px; font-size: 1.2rem;">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div>
                        <h2 style="margin: 0; font-size: 1.3rem; font-weight: 800; color: var(--text-main);">
                            <?= htmlspecialchars($acc['account_code']) ?> - <?= htmlspecialchars($acc['name']) ?>
                        </h2>
                        <span style="color: var(--text-muted); font-size: 0.85rem;">
                            النوع: <?= htmlspecialchars($acc['type']) ?> | الطبيعة: <?= ($acc['nature'] === 'debit') ? 'مدين (Debit)' : 'دائن (Credit)' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Current Balance Box -->
            <div style="background: rgba(148, 163, 184, 0.08); padding: 1.25rem; border-radius: var(--radius-md); text-align: center; border: 1px solid var(--border-color);">
                <div style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 0.25rem;">الرصيد الفعلي الحالي للحساب</div>
                <div style="font-size: 1.6rem; font-weight: 900; color: <?= ($acc['current_balance'] >= 0) ? 'var(--text-main)' : '#ef4444' ?>;">
                    <?= Helpers::formatCurrency($acc['current_balance']) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">
                    الرصيد الافتتاحي: <?= Helpers::formatCurrency($acc['opening_balance']) ?>
                </div>
            </div>
        </div>

        <!-- Period Filter -->
        <hr style="border: none; border-top: 1px solid var(--border-color); margin: 1.25rem 0;">
        <form method="GET" action="<?= BASE_URL ?>accounting.php" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
            <input type="hidden" name="tab" value="statement">
            <input type="hidden" name="account_id" value="<?= $acc['id'] ?>">

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">من تاريخ</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= htmlspecialchars($dateFrom) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= htmlspecialchars($dateTo) ?>">
            </div>

            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fas fa-filter"></i> عرض الفترة
            </button>
        </form>
    </div>

    <!-- Period Metrics -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($statement['opening_period_balance']) ?></div>
                <div class="stat-title">رصيد ما قبل الفترة</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(56, 189, 248, 0.12); color: #38bdf8;">
                <i class="fas fa-arrow-circle-up"></i>
            </div>
            <div>
                <div class="stat-val" style="color: #38bdf8;"><?= Helpers::formatCurrency($statement['total_debit']) ?></div>
                <div class="stat-title">إجمالي الحركات المدينة</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon emerald">
                <i class="fas fa-arrow-circle-down"></i>
            </div>
            <div>
                <div class="stat-val" style="color: #10b981;"><?= Helpers::formatCurrency($statement['total_credit']) ?></div>
                <div class="stat-title">إجمالي الحركات الدائنة</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon amber">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($statement['closing_period_balance']) ?></div>
                <div class="stat-title">رصيد نهاية الفترة</div>
            </div>
        </div>
    </div>

    <!-- Ledger Entries Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 110px;">التاريخ</th>
                        <th style="width: 140px;">رقم القيد</th>
                        <th style="width: 130px;">المرجع المستندي</th>
                        <th>شرح وبيان الحركة</th>
                        <th style="color: #38bdf8; width: 130px;">مدين</th>
                        <th style="color: #10b981; width: 130px;">دائن</th>
                        <th style="color: var(--brand-amber); width: 140px;">الرصيد بعد الحركة</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background: rgba(148, 163, 184, 0.05); font-weight: 700;">
                        <td><?= Helpers::formatDate($dateFrom) ?></td>
                        <td>-</td>
                        <td>-</td>
                        <td>
                            <i class="fas fa-flag" style="color: #38bdf8; margin-left: 5px;"></i>
                            رصيد منقول سابق لبداية الفترة
                        </td>
                        <td>-</td>
                        <td>-</td>
                        <td style="color: var(--brand-amber); font-weight: 800;">
                            <?= Helpers::formatCurrency($statement['opening_period_balance']) ?>
                        </td>
                    </tr>

                    <?php if (empty($statement['transactions'])): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                                لا توجد حركات مقيدة على هذا الحساب خلال الفترة المحددة.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($statement['transactions'] as $t): ?>
                            <tr>
                                <td style="font-size: 0.85rem; color: var(--text-muted);">
                                    <?= Helpers::formatDate($t['entry_date']) ?>
                                </td>
                                <td>
                                    <span style="font-family: monospace; font-weight: 700; color: #38bdf8;">
                                        <?= htmlspecialchars($t['entry_number']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: var(--text-muted); font-family: monospace;">
                                        <?= htmlspecialchars($t['doc_reference'] ?: '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-main); font-size: 0.9rem;">
                                        <?= htmlspecialchars($t['line_desc'] ?: $t['entry_desc']) ?>
                                    </div>
                                </td>
                                <td style="font-weight: 700; color: #38bdf8;">
                                    <?= ((float)$t['debit'] > 0) ? Helpers::formatCurrency($t['debit']) : '-' ?>
                                </td>
                                <td style="font-weight: 700; color: #10b981;">
                                    <?= ((float)$t['credit'] > 0) ? Helpers::formatCurrency($t['credit']) : '-' ?>
                                </td>
                                <td style="font-weight: 800; font-size: 0.95rem; color: var(--text-main);">
                                    <?= Helpers::formatCurrency($t['balance_after']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background: rgba(148, 163, 184, 0.08); font-weight: 800;">
                        <td colspan="4" style="text-align: left; padding: 1rem;">الرصيد الختامي بنهاية الفترة:</td>
                        <td colspan="3" style="color: var(--brand-amber); font-size: 1.15rem; padding: 1rem;">
                            <?= Helpers::formatCurrency($statement['closing_period_balance']) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
