<?php
/**
 * شاشة سجل فواتير المبيعات وتقارير الكاشير
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 */
$pageTitle = "سجل فواتير المبيعات";
?>

<div class="sales-list-page">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-file-invoice-dollar" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                سجل فواتير المبيعات ونقاط البيع
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                متابعة حركة المبيعات، الإيرادات اليومية، طرق السداد، وإعادة طباعة الإيصالات
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= BASE_URL ?>pos.php" class="btn btn-primary" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <i class="fas fa-bolt"></i>
                فتح نقطة البيع (POS)
            </a>
        </div>
    </div>

    <!-- Revenue Metrics Grid -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <div class="stat-val"><?= number_format($stats['total_invoices']) ?></div>
                <div class="stat-title">إجمالي الفواتير الصادرة</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon amber">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($stats['total_revenue']) ?></div>
                <div class="stat-title">إجمالي مبيعات الفترة</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon emerald">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($stats['cash_revenue']) ?></div>
                <div class="stat-title">مبيعات نقدية (كاش)</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                <i class="fas fa-credit-card"></i>
            </div>
            <div>
                <div class="stat-val"><?= Helpers::formatCurrency($stats['electronic_revenue']) ?></div>
                <div class="stat-title">مدفوعات إلكترونية (فيزا/إنستاباي)</div>
            </div>
        </div>
    </div>
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.12); color: #a78bfa;">
                <i class="fas fa-credit-card"></i>
            </div>
            <div>
                <div class="stat-value"><?= Helpers::formatCurrency($stats['card_revenue'] + $stats['instapay_revenue'] + $stats['wallet_revenue']) ?></div>
                <div class="stat-label">دفع إلكتروني (ميزة/إنستاباي/محافظ)</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(236, 72, 153, 0.12); color: #f472b6;">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div>
                <div class="stat-value"><?= Helpers::formatCurrency($stats['credit_revenue']) ?></div>
                <div class="stat-label">مبيعات آجلة على الحساب</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
        <form method="GET" action="<?= BASE_URL ?>sales.php" style="display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">بحث سريع (رقم الفاتورة، العميل، الهاتف)</label>
                <input type="text" name="search" class="form-control" placeholder="INV-20261109-0001 أو اسم العميل..." value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">طريقة الدفع</label>
                <select name="payment_method" class="form-control">
                    <option value="">كافة طرق السداد</option>
                    <option value="cash" <?= ($paymentMethod === 'cash') ? 'selected' : '' ?>>نقداً (كاش)</option>
                    <option value="card" <?= ($paymentMethod === 'card') ? 'selected' : '' ?>>بطاقة / ميزة POS</option>
                    <option value="instapay" <?= ($paymentMethod === 'instapay') ? 'selected' : '' ?>>إنستاباي (InstaPay)</option>
                    <option value="wallet" <?= ($paymentMethod === 'wallet') ? 'selected' : '' ?>>محفظة ذكية (Vodafone..)</option>
                    <option value="credit" <?= ($paymentMethod === 'credit') ? 'selected' : '' ?>>آجل على الحساب</option>
                    <option value="mixed" <?= ($paymentMethod === 'mixed') ? 'selected' : '' ?>>دفع متعدد (Mixed)</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="height: 42px;">
                    <i class="fas fa-filter"></i> تصفية
                </button>
                <a href="<?= BASE_URL ?>sales.php" class="btn btn-outline" style="height: 42px; display: inline-flex; align-items: center;" title="إعادة تعيين الفلاتر">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Invoices Table Card -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 800; color: var(--text-main);">
                <i class="fas fa-file-invoice" style="color: var(--brand-blue); margin-left: 0.4rem;"></i>
                قائمة الفواتير الصادرة (<?= count($sales) ?> فاتورة)
            </div>
            <div style="font-size: 0.82rem; color: var(--text-muted); font-weight: 600;">
                الفترة من <?= $dateFrom ?> إلى <?= $dateTo ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-pro data-table">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>التاريخ والوقت</th>
                        <th>العميل</th>
                        <th>الكاشير والوردية</th>
                        <th>المبلغ الإجمالي</th>
                        <th>الضريبة 14%</th>
                        <th>طريقة الدفع</th>
                        <th>حالة السداد</th>
                        <th style="text-align: center; width: 100px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sales)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                            <i class="fas fa-receipt" style="font-size: 2.5rem; opacity: 0.3; margin-bottom: 0.5rem;"></i>
                            <p style="margin-bottom: 0;">لم يتم إصدار فواتير مبيعات مطابقة لمعايير البحث في هذه الفترة</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($sales as $s): 
                        $methodBadge = match($s['payment_method']) {
                            'cash'     => ['كاش', 'badge-emerald'],
                            'card'     => ['كارت/ميزة', 'badge-blue'],
                            'instapay' => ['إنستاباي', 'badge-amber'],
                            'wallet'   => ['محفظة كاش', 'badge-purple'],
                            'credit'   => ['آجل', 'badge-danger'],
                            default    => ['متعدد', 'badge-blue']
                        };
                    ?>
                    <tr>
                        <td style="font-family: var(--font-mono); font-weight: 700; color: var(--brand-amber);">
                            <?= htmlspecialchars($s['invoice_number']) ?>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.82rem; font-weight: 600;">
                            <?= date('Y-m-d h:i A', strtotime($s['created_at'])) ?>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($s['customer_name'] ?? 'عميل نقدي') ?></div>
                            <?php if (!empty($s['customer_phone'])): ?>
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;"><?= htmlspecialchars($s['customer_phone']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 0.85rem;">
                            <div style="color: var(--text-main); font-weight: 700;"><?= htmlspecialchars($s['cashier_name'] ?? 'كاشير') ?></div>
                            <?php if (!empty($s['shift_number'])): ?>
                                <div style="font-size: 0.72rem; color: var(--brand-blue); font-family: var(--font-mono); font-weight: 700;"><?= htmlspecialchars($s['shift_number']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: 800; color: var(--brand-blue); font-size: 1.05rem; font-family: var(--font-mono);">
                            <?= Helpers::formatCurrency($s['grand_total']) ?>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.88rem; font-weight: 600;">
                            <?= Helpers::formatCurrency($s['tax_amount']) ?>
                        </td>
                        <td>
                            <span class="badge <?= $methodBadge[1] ?>">
                                <?= $methodBadge[0] ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($s['remaining_amount'] <= 0): ?>
                                <span class="badge badge-emerald">مسدد بالكامل</span>
                            <?php else: ?>
                                <span class="badge badge-rose">متبقي: <?= Helpers::formatCurrency($s['remaining_amount']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="<?= BASE_URL ?>receipt.php?id=<?= $s['id'] ?>" target="_blank" class="btn btn-outline btn-sm" title="معاينة وطباعة الإيصال الحراري" style="padding: 4px 8px; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
                                <i class="fas fa-print"></i> إيصال
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

