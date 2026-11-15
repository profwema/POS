<?php
/**
 * كشف حساب المورد التفصيلي - Supplier Statement of Account
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$sup = $statement['supplier'];
$pageTitle = "كشف حساب: " . htmlspecialchars($sup['name']);
?>

<div class="supplier-statement-page">
    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-file-invoice-dollar" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                كشف حساب المورد التفصيلي
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                سجل الحركات المالية المتبادلة، التوريدات، المدفوعات النقدية، المرتجعات، والرصيد التراكمي
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" onclick="window.print()">
                <i class="fas fa-print"></i>
                طباعة كشف الحساب
            </button>
            <a href="<?= BASE_URL ?>suppliers.php" class="btn btn-outline">
                <i class="fas fa-arrow-right"></i>
                دليل الموردين
            </a>
        </div>
    </div>

    <!-- Supplier Profile & Statement Header -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: center;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <div class="stat-icon blue" style="width: 44px; height: 44px; font-size: 1.2rem;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h2 style="margin: 0; font-size: 1.3rem; font-weight: 800; color: var(--text-main);">
                            <?= htmlspecialchars($sup['name']) ?>
                        </h2>
                        <?php if (!empty($sup['company_name'])): ?>
                            <span style="color: var(--text-muted); font-size: 0.85rem;"><?= htmlspecialchars($sup['company_name']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 1rem; font-size: 0.85rem; color: var(--text-muted);">
                    <div>
                        <i class="fas fa-phone-alt" style="color: var(--brand-amber); margin-left: 4px;"></i>
                        <strong>الهاتف:</strong> <?= htmlspecialchars($sup['phone']) ?>
                    </div>
                    <?php if (!empty($sup['tax_number'])): ?>
                        <div>
                            <i class="fas fa-receipt" style="color: #38bdf8; margin-left: 4px;"></i>
                            <strong>الرقم الضريبي:</strong> <?= htmlspecialchars($sup['tax_number']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($sup['address'])): ?>
                        <div>
                            <i class="fas fa-map-marker-alt" style="color: #ef4444; margin-left: 4px;"></i>
                            <strong>العنوان:</strong> <?= htmlspecialchars($sup['address']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Current Standing Box -->
            <div style="background: rgba(148, 163, 184, 0.08); padding: 1.25rem; border-radius: var(--radius-md); text-align: center; border: 1px solid var(--border-color);">
                <div style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 0.25rem;">الرصيد الدائن الحالي المستحق للمورد</div>
                <div style="font-size: 1.6rem; font-weight: 900; color: <?= ($sup['current_balance'] > 0) ? '#ef4444' : '#10b981' ?>;">
                    <?= Helpers::formatCurrency($sup['current_balance']) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">
                    سقف الائتمان: <?= Helpers::formatCurrency($sup['credit_limit']) ?> | أجل: <?= (int)$sup['payment_terms_days'] ?> يوم
                </div>
            </div>
        </div>

        <!-- Period Filter -->
        <hr style="border: none; border-top: 1px solid var(--border-color); margin: 1.25rem 0;">
        <form method="GET" action="<?= BASE_URL ?>suppliers.php" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
            <input type="hidden" name="action" value="statement">
            <input type="hidden" name="id" value="<?= $sup['id'] ?>">

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

    <!-- Period Metrics Grid -->
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
            <div class="stat-icon amber">
                <i class="fas fa-cart-arrow-down"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($statement['total_purchases']) ?></div>
                <div class="stat-title">مشتريات الفترة</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon emerald">
                <i class="fas fa-money-check-alt"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($statement['total_paid']) ?></div>
                <div class="stat-title">المدفوعات المسددة للمورد</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(168, 85, 247, 0.12); color: #a855f7;">
                <i class="fas fa-undo"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($statement['total_returns']) ?></div>
                <div class="stat-title">إجمالي المردودات للمورد</div>
            </div>
        </div>
    </div>

    <!-- Statement Transactions Ledger -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 110px;">التاريخ</th>
                        <th style="width: 150px;">رقم السند / المرجع</th>
                        <th>نوع الحركة والبيان</th>
                        <th style="color: #ef4444; width: 140px;">مدين (خصم / سداد)</th>
                        <th style="color: #10b981; width: 140px;">دائن (توريد بضاعة)</th>
                        <th style="color: var(--brand-amber); width: 150px;">الرصيد التراكمي</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Opening Period Row -->
                    <tr style="background: rgba(148, 163, 184, 0.05); font-weight: 700;">
                        <td><?= Helpers::formatDate($dateFrom) ?></td>
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
                        <td>بداية احتساب كشف الحساب</td>
                    </tr>

                    <?php if (empty($statement['transactions'])): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                                لا توجد حركات مالية مسجلة لهذا المورد خلال هذه الفترة.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($statement['transactions'] as $tr): ?>
                            <tr>
                                <td style="font-size: 0.85rem; color: var(--text-muted);">
                                    <?= Helpers::formatDate($tr['trans_date']) ?>
                                </td>
                                <td>
                                    <span style="font-family: monospace; font-weight: 700; color: #38bdf8;">
                                        <?= htmlspecialchars($tr['trans_ref']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($tr['trans_type'] === 'purchase'): ?>
                                        <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                                            <i class="fas fa-truck-loading" style="margin-left: 3px;"></i>
                                            <?= htmlspecialchars($tr['trans_label']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                                            <i class="fas fa-undo" style="margin-left: 3px;"></i>
                                            <?= htmlspecialchars($tr['trans_label']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 700; color: #10b981;">
                                    <?php
                                    $debit = ($tr['trans_type'] === 'purchase') ? (float)$tr['paid_amount'] : (float)$tr['grand_total'];
                                    echo ($debit > 0) ? Helpers::formatCurrency($debit) : '-';
                                    ?>
                                </td>
                                <td style="font-weight: 700; color: #ef4444;">
                                    <?php
                                    $credit = ($tr['trans_type'] === 'purchase') ? (float)$tr['grand_total'] : 0.0;
                                    echo ($credit > 0) ? Helpers::formatCurrency($credit) : '-';
                                    ?>
                                </td>
                                <td style="font-weight: 800; font-size: 0.95rem; color: var(--text-main);">
                                    <?= Helpers::formatCurrency($tr['balance_after']) ?>
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-muted);">
                                    <?= htmlspecialchars($tr['notes'] ?? '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background: rgba(148, 163, 184, 0.08); font-weight: 800; font-size: 1rem;">
                        <td colspan="5" style="text-align: left; padding: 1rem;">
                            الرصيد الختامي المستحق للمورد في نهاية الفترة:
                        </td>
                        <td style="color: <?= ($statement['closing_period_balance'] > 0) ? '#ef4444' : '#10b981' ?>; font-size: 1.15rem; padding: 1rem;">
                            <?= Helpers::formatCurrency($statement['closing_period_balance']) ?>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
