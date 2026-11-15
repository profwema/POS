<?php
/**
 * كشف حساب العميل التفصيلي - Customer Statement of Account
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "كشف حساب العميل: " . ($statement['customer']['name'] ?? '');
$c = $statement['customer'] ?? [];
$trans = $statement['transactions'] ?? [];
?>

<div class="customer-statement-page">
    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-file-invoice" style="color: #a855f7; margin-left: 0.5rem;"></i>
                كشف حساب عميل تفصيلي
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                متابعة حركة المبيعات، السدادات، المرتجعات، والرصيد المتبقي المستحق
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" onclick="window.print()">
                <i class="fas fa-print"></i>
                طباعة كشف الحساب
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
            <input type="hidden" name="type" value="customer_statement">

            <div class="form-group" style="margin-bottom: 0; min-width: 220px; flex: 1;">
                <label class="form-label" style="font-size: 0.82rem;">اختر العميل</label>
                <select name="customer_id" class="form-control" required>
                    <?php foreach ($customers as $cust): ?>
                        <option value="<?= $cust['id'] ?>" <?= ($customerId == $cust['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cust['name']) ?> (<?= htmlspecialchars($cust['phone'] ?? '-') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
                <label class="form-label" style="font-size: 0.82rem;">من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
                <label class="form-label" style="font-size: 0.82rem;">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                عرض الكشف
            </button>
        </form>
    </div>

    <!-- Statement Document Header -->
    <div class="card" style="padding: 1.75rem; margin-bottom: 1.5rem; border-top: 4px solid #a855f7;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.25rem;">
            <div>
                <span class="badge badge-purple" style="font-size: 0.8rem; margin-bottom: 0.5rem;">حساب عميل</span>
                <h2 style="margin: 0; font-size: 1.4rem; font-weight: 800; color: var(--text-main);">
                    <?= htmlspecialchars($c['name'] ?? 'عميل غير محدد') ?>
                </h2>
                <div style="display: flex; gap: 1.5rem; margin-top: 0.5rem; font-size: 0.85rem; color: var(--text-muted); flex-wrap: wrap;">
                    <div><strong>الهاتف:</strong> <span style="font-family: monospace;"><?= htmlspecialchars($c['phone'] ?? 'غير مسجل') ?></span></div>
                    <div><strong>الرقم الضريبي:</strong> <span style="font-family: monospace;"><?= htmlspecialchars($c['tax_number'] ?? 'غير مسجل') ?></span></div>
                    <div><strong>الحد الائتماني:</strong> <?= Helpers::formatCurrency($c['credit_limit'] ?? 0) ?></div>
                </div>
            </div>

            <div style="text-align: left; background: rgba(168, 85, 247, 0.08); padding: 1rem 1.5rem; border-radius: var(--radius-md); border: 1px solid rgba(168, 85, 247, 0.2);">
                <div style="font-size: 0.8rem; color: var(--text-muted);">الفترة المحاسبية</div>
                <div style="font-weight: 700; color: #a855f7; font-size: 0.95rem;">
                    <?= Helpers::formatDate($dateFrom) ?> إلى <?= Helpers::formatDate($dateTo) ?>
                </div>
                <div style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-muted);">
                    تاريخ استخراج الكشف: <span style="font-family: monospace;"><?= date('Y-m-d H:i') ?></span>
                </div>
            </div>
        </div>

        <!-- 4 KPI Metrics -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
            <div style="background: var(--bg-surface); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <div style="font-size: 0.78rem; color: var(--text-muted);">رصيد أول الفترة (مدين/دائن)</div>
                <div style="font-size: 1.2rem; font-weight: 800; color: var(--text-main); margin-top: 0.25rem; font-family: monospace;">
                    <?= Helpers::formatCurrency($statement['opening_period_balance'] ?? 0) ?>
                </div>
            </div>

            <div style="background: var(--bg-surface); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <div style="font-size: 0.78rem; color: var(--text-muted);">إجمالي مشتريات العميل بالفترة</div>
                <div style="font-size: 1.2rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem; font-family: monospace;">
                    <?= Helpers::formatCurrency($statement['total_sales'] ?? 0) ?>
                </div>
            </div>

            <div style="background: var(--bg-surface); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <div style="font-size: 0.78rem; color: var(--text-muted);">إجمالي المسدد نقداً / دفعات</div>
                <div style="font-size: 1.2rem; font-weight: 800; color: #10b981; margin-top: 0.25rem; font-family: monospace;">
                    <?= Helpers::formatCurrency($statement['total_paid'] ?? 0) ?>
                </div>
            </div>

            <div style="background: var(--bg-surface); padding: 1rem; border-radius: var(--radius-md); border: 1px solid rgba(168, 85, 247, 0.4); border-right: 4px solid #a855f7;">
                <div style="font-size: 0.78rem; color: #a855f7; font-weight: 700;">رصيد نهاية الفترة المستحق</div>
                <div style="font-size: 1.3rem; font-weight: 800; color: #a855f7; margin-top: 0.25rem; font-family: monospace;">
                    <?= Helpers::formatCurrency($statement['closing_period_balance'] ?? 0) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Statement Transactions Table -->
    <div class="card" style="overflow: hidden;">
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--text-main);">
                سجل حركات الحساب التفصيلية
            </h3>
            <span class="badge badge-outline">
                <?= count($trans) ?> حركة مسجلة
            </span>
        </div>

        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
                <thead>
                    <tr style="background: var(--bg-surface); text-align: right;">
                        <th style="padding: 1rem; width: 45px;">#</th>
                        <th style="padding: 1rem; width: 110px;">التاريخ</th>
                        <th style="padding: 1rem;">رقم المرجع / الفاتورة</th>
                        <th style="padding: 1rem;">نوع الحركة</th>
                        <th style="padding: 1rem; text-align: left;">إجمالي العملية</th>
                        <th style="padding: 1rem; text-align: left;">المسدد</th>
                        <th style="padding: 1rem; text-align: left;">مدين (آجل)</th>
                        <th style="padding: 1rem; text-align: left;">دائن (مرتجع)</th>
                        <th style="padding: 1rem; text-align: left;">الرصيد التراكمي</th>
                        <th style="padding: 1rem;">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- الرصيد الافتتاحي أول الفترة -->
                    <tr style="background: rgba(255, 255, 255, 0.02); font-weight: 700; border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.85rem 1rem; color: var(--text-muted);">-</td>
                        <td style="padding: 0.85rem 1rem; font-family: monospace;"><?= $dateFrom ?></td>
                        <td style="padding: 0.85rem 1rem;" colspan="2">
                            <span class="badge badge-outline">رصيد أول المدة المنقول</span>
                        </td>
                        <td style="padding: 0.85rem 1rem; text-align: left;" colspan="4">-</td>
                        <td style="padding: 0.85rem 1rem; text-align: left; font-family: monospace; font-weight: 800; color: #a855f7;">
                            <?= Helpers::formatCurrency($statement['opening_period_balance'] ?? 0) ?>
                        </td>
                        <td style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.8rem;">رصيد سابق</td>
                    </tr>

                    <?php if (empty($trans)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                لا توجد حركات بيع أو سداد مسجلة للعميل خلال هذه الفترة المحددة.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($trans as $idx => $t): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.85rem;">
                                    <?= $idx + 1 ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; font-family: monospace; font-size: 0.85rem;">
                                    <?= $t['trans_date'] ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; font-family: monospace; font-weight: 700; color: #38bdf8;">
                                    <?= htmlspecialchars($t['trans_ref']) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <?php if ($t['trans_type'] === 'sale'): ?>
                                        <span class="badge badge-primary">فاتورة بيع</span>
                                    <?php else: ?>
                                        <span class="badge badge-amber">مرتجع مبيعات</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; font-family: monospace;">
                                    <?= number_format($t['grand_total'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; font-family: monospace; color: #10b981;">
                                    <?= number_format($t['paid_amount'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; font-family: monospace; color: #ef4444; font-weight: 700;">
                                    <?= number_format($t['debit'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; font-family: monospace; color: #10b981; font-weight: 700;">
                                    <?= number_format($t['credit'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: left; font-family: monospace; font-weight: 800; color: #a855f7;">
                                    <?= number_format($t['balance_after'], 2) ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; font-size: 0.82rem; color: var(--text-muted);">
                                    <?= htmlspecialchars($t['notes'] ?? '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot style="background: rgba(0,0,0,0.15); font-weight: 800; border-top: 2px solid var(--border-color);">
                    <tr>
                        <td colspan="8" style="padding: 1rem; text-align: right; color: var(--text-main);">
                            صافي الرصيد المستحق على العميل في نهاية الفترة:
                        </td>
                        <td style="padding: 1rem; text-align: left; font-family: monospace; color: #a855f7; font-size: 1.1rem;">
                            <?= Helpers::formatCurrency($statement['closing_period_balance'] ?? 0) ?>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    .app-sidebar, .app-header, .btn, form {
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
