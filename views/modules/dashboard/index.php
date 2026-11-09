<?php
/**
 * لوحة التحكم الرئيسية - Dashboard View
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 */
?>

<!-- Welcome Banner -->
<div style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.15) 0%, rgba(245, 158, 11, 0.12) 100%); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.75rem 2rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem; flex-wrap: wrap;">
            <span class="badge badge-emerald">المرحلة 0: النواة 100%</span>
            <span class="badge badge-emerald">المرحلة 1: المخازن والأصناف 100%</span>
            <span class="badge badge-emerald">المرحلة 2: محرك الكاشير POS والورديات 100%</span>
        </div>
        <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
            مرحباً بك، <?= Security::escape($currentUser['full_name'] ?? 'مدير النظام') ?> 👋
        </h2>
        <p style="color: var(--text-muted); margin-bottom: 0; font-size: 0.92rem;">
            منظومة WAM Tech POS & ERP تعمل بكامل كفاءتها تحت مظلة وهوية <strong>«المبرمج | Almubarmig»</strong>.
        </p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="<?= BASE_URL ?>sales.php" class="btn btn-outline" style="border-color: rgba(255,255,255,0.2);">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>سجل المبيعات</span>
        </a>
        <a href="<?= BASE_URL ?>pos.php" class="btn btn-warning" style="padding: 0.75rem 1.4rem; font-size: 0.95rem; background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-color: #10b981; color: #fff;">
            <i class="fas fa-bolt"></i>
            <span>فتح شاشة الكاشير السريعة (POS)</span>
        </a>
    </div>
</div>

<!-- Stat Cards Row (Live Sales & Valuation) -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon emerald">
            <i class="fas fa-receipt"></i>
        </div>
        <div>
            <div class="stat-val"><?= Helpers::formatCurrency($todayStats['total_revenue'] ?? 0) ?></div>
            <div class="stat-title">إجمالي مبيعات اليوم (<?= (int)($todayStats['invoices_count'] ?? 0) ?> فاتورة)</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-boxes"></i>
        </div>
        <div>
            <div class="stat-val"><?= number_format($inventoryValuation['total_items_count']) ?> صنف</div>
            <div class="stat-title">الأصناف المتوفرة بالمخازن</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">
            <i class="fas fa-wallet"></i>
        </div>
        <div>
            <div class="stat-val"><?= Helpers::formatCurrency($inventoryValuation['total_cost_valuation']) ?></div>
            <div class="stat-title">تقييم المخزون بسعر الشراء</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(139, 92, 246, 0.15); color: #a78bfa;">
            <i class="fas fa-chart-line"></i>
        </div>
        <div>
            <div class="stat-val"><?= Helpers::formatCurrency($inventoryValuation['total_retail_valuation']) ?></div>
            <div class="stat-title">قيمة البيع (ربح متوقع +<?= $inventoryValuation['profit_margin_pct'] ?>%)</div>
        </div>
    </div>
</div>

<!-- Main 2-Column Section -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem; align-items: start;">
    
    <!-- Left Column: Architecture Roadmap & Progress -->
    <div class="card-pro">
        <div class="card-pro-header">
            <h3>
                <i class="fas fa-tasks" style="color: var(--brand-blue);"></i>
                <span>مخطط الإنجاز المعماري - نظام WAM Tech POS & ERP</span>
            </h3>
            <span class="badge badge-emerald">Phase 2 Completed</span>
        </div>
        <div style="padding: 1.5rem;">
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                
                <!-- Phase 0 -->
                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-check-circle" style="color: var(--brand-emerald); font-size: 1.3rem;"></i>
                        <div>
                            <div style="font-weight: 700; color: var(--text-main);">المرحلة 0: النواة التأسيسية وهيكل الحماية والبراند</div>
                            <div style="font-size: 0.82rem; color: var(--text-muted);">قاعدة wamtech_pos_db، تشفير Bcrypt، حماية CSRF، صلاحيات RBAC، هوية المبرمج</div>
                        </div>
                    </div>
                    <span class="badge badge-emerald">مكتمل 100%</span>
                </div>

                <!-- Phase 1 -->
                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-check-circle" style="color: var(--brand-emerald); font-size: 1.3rem;"></i>
                        <div>
                            <div style="font-weight: 700; color: var(--text-main);">المرحلة 1: مديول إدارة المخازن والأصناف والموازين</div>
                            <div style="font-size: 0.82rem; color: var(--text-muted);">جدول stock_moves، الأصناف، كارت الصنف، باركود الموازين (20/99)، طباعة الملصقات الحرارية</div>
                        </div>
                    </div>
                    <span class="badge badge-emerald">مكتمل 100%</span>
                </div>

                <!-- Phase 2 -->
                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-check-circle" style="color: var(--brand-emerald); font-size: 1.3rem;"></i>
                        <div>
                            <div style="font-weight: 700; color: var(--text-main);">المرحلة 2: محرك نقطة البيع السريعة (POS) والورديات</div>
                            <div style="font-size: 0.82rem; color: var(--text-muted);">شاشة الكاشير السريعة، الورديات والعُهد، الدفع المتعدد (ميزة/إنستاباي/محافظ)، والإيصال الحراري 80mm</div>
                        </div>
                    </div>
                    <span class="badge badge-emerald">مكتمل 100%</span>
                </div>

                <!-- Phase 3 -->
                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-arrow-circle-left" style="color: var(--brand-amber); font-size: 1.3rem;"></i>
                        <div>
                            <div style="font-weight: 700; color: var(--text-main);">المرحلة 3: المبيعات والمشتريات وسلاسل الإمداد</div>
                            <div style="font-size: 0.82rem; color: var(--text-muted);">عروض الأسعار، مرتجعات المبيعات، أوامر الشراء، فواتير المشتريات، كشوف العملاء والموردين، وأعمار الديون</div>
                        </div>
                    </div>
                    <span class="badge badge-amber">المرحلة القادمة</span>
                </div>

                <!-- Phase 4 -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-circle-notch" style="color: var(--text-muted); font-size: 1.2rem;"></i>
                        <div>
                            <div style="font-weight: 700; color: var(--text-muted);">المرحلة 4: المحاسبة المالية والقيود وشجرة الحسابات</div>
                            <div style="font-size: 0.82rem; color: var(--text-muted);">اليومية العامة المتوازنة، المصروفات، ميزان المراجعة، قائمة الدخل، ومركز النسخ الاحتياطي</div>
                        </div>
                    </div>
                    <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-muted);">مجدول</span>
                </div>

            </div>
        </div>
    </div>

    <!-- Right Column: Shift Status & Quick Shortcuts -->
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        
        <!-- Cashier Shift Box -->
        <div class="card-pro">
            <div class="card-pro-header">
                <h3>
                    <i class="fas fa-user-clock" style="color: var(--brand-amber);"></i>
                    <span>حالة وردية الكاشير</span>
                </h3>
            </div>
            <div style="padding: 1.25rem;">
                <?php if ($activeShift): ?>
                    <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 8px; padding: 0.85rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                            <span style="font-size: 0.8rem; color: var(--text-muted);">رقم الوردية:</span>
                            <strong style="color: #34d399; font-family: monospace;"><?= htmlspecialchars($activeShift['shift_number']) ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">العهدة الافتتاحية:</span>
                            <strong style="color: var(--text-main); font-weight: 800;"><?= Helpers::formatCurrency($activeShift['opening_balance']) ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">المبيعات النقدية:</span>
                            <strong style="color: var(--brand-emerald); font-weight: 800;"><?= Helpers::formatCurrency($activeShift['total_sales_cash']) ?></strong>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>pos.php" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fas fa-barcode"></i> الدخول إلى شاشة البيع
                    </a>
                <?php else: ?>
                    <div style="text-align: center; color: var(--text-muted); padding: 1rem 0;">
                        <i class="fas fa-lock" style="font-size: 2rem; color: #94a3b8; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                        <p style="font-size: 0.85rem; margin-bottom: 1rem;">لا توجد وردية كاشير مفتوحة حالياً</p>
                        <a href="<?= BASE_URL ?>pos.php" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center;">
                            <i class="fas fa-play"></i> بدء وردية جديدة
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Inventory Alerts Box -->
        <div class="card-pro">
            <div class="card-pro-header">
                <h3>
                    <i class="fas fa-bell" style="color: #38bdf8;"></i>
                    <span>تنبيهات المخزون</span>
                </h3>
            </div>
            <div style="padding: 1.25rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.65rem;">
                    <span style="color: var(--text-muted); font-size: 0.85rem;">أصناف تحت حد الطلب:</span>
                    <strong style="color: <?= ($lowStockCount > 0) ? '#f87171' : '#10b981' ?>;"><?= $lowStockCount ?> صنف</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span style="color: var(--text-muted); font-size: 0.85rem;">تشغيلات تنتهي قريباً:</span>
                    <strong style="color: <?= ($expiringCount > 0) ? '#fbbf24' : '#10b981' ?>;"><?= $expiringCount ?> تشغيلة</strong>
                </div>

                <a href="<?= BASE_URL ?>items.php" class="btn btn-outline" style="width: 100%; justify-content: center;">
                    <i class="fas fa-boxes"></i> فتح شاشة الأصناف
                </a>
            </div>
        </div>

    </div>
</div>

<!-- Recent Sales Invoices Table -->
<div class="card-pro">
    <div class="card-pro-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>
            <i class="fas fa-history" style="color: #10b981;"></i>
            <span>أحدث فواتير المبيعات الصادرة بالمنظومة</span>
        </h3>
        <a href="<?= BASE_URL ?>sales.php" class="btn btn-outline btn-sm">
            عرض كافة الفواتير (<?= (int)($todayStats['invoices_count'] ?? 0) ?>)
        </a>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>رقم الفاتورة</th>
                    <th>التاريخ والوقت</th>
                    <th>العميل</th>
                    <th>الكاشير</th>
                    <th>المبلغ الإجمالي</th>
                    <th>طريقة الدفع</th>
                    <th style="text-align: center; width: 120px;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentSales)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                        لا توجد فواتير مبيعات مسجلة بعد.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($recentSales as $sale): 
                    $methodBadge = match($sale['payment_method']) {
                        'cash'     => ['كاش', 'badge-emerald'],
                        'card'     => ['كارت/ميزة', 'badge-sky'],
                        'instapay' => ['إنستاباي', 'badge-amber'],
                        'wallet'   => ['محفظة كاش', 'badge-purple'],
                        'credit'   => ['آجل', 'badge-rose'],
                        default    => ['متعدد', 'badge-sky']
                    };
                ?>
                <tr>
                    <td style="font-family: monospace; font-weight: 700; color: var(--brand-amber);">
                        <?= htmlspecialchars($sale['invoice_number']) ?>
                    </td>
                    <td style="color: var(--text-muted); font-size: 0.82rem;">
                        <?= date('Y-m-d h:i A', strtotime($sale['created_at'])) ?>
                    </td>
                    <td style="font-weight: 700; color: var(--text-main);">
                        <?= htmlspecialchars($sale['customer_name'] ?? 'عميل نقدي') ?>
                    </td>
                    <td style="color: var(--text-main); font-size: 0.85rem; font-weight: 600;">
                        <?= htmlspecialchars($sale['cashier_name'] ?? 'كاشير') ?>
                    </td>
                    <td style="font-weight: 800; color: var(--brand-blue); font-family: var(--font-mono);">
                        <?= Helpers::formatCurrency($sale['grand_total']) ?>
                    </td>
                    <td>
                        <span class="badge <?= $methodBadge[1] ?>">
                            <?= $methodBadge[0] ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <a href="<?= BASE_URL ?>receipt.php?id=<?= $sale['id'] ?>" target="_blank" class="btn btn-outline btn-sm" title="طباعة الإيصال الحراري" style="padding: 4px 8px; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
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
