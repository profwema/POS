<?php
/**
 * شاشة مركز التقارير والضرائب - Reports Hub
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "مركز التقارير التحليلية والضرائب والرقابة";
?>

<div class="reports-hub-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                <i class="fas fa-chart-pie" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                مركز التقارير التحليلية والضرائب المصرية
            </h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">
                تقارير الأداء المالي، الإقرار الضريبي لضريبة القيمة المضافة، ربحية الأصناف، وحركة المخزون
            </p>
        </div>
    </div>

    <!-- Reports Hub Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
        <!-- Card 1: VAT Declaration -->
        <div class="card" style="padding: 1.75rem; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #10b981;">
            <div>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div class="stat-icon emerald" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--text-main);">
                            إقرار ضريبة القيمة المضافة (نموذج 10)
                        </h3>
                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; margin-top: 4px;">
                            معتمد مصلحة الضرائب المصرية
                        </span>
                    </div>
                </div>
                <p style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1.25rem;">
                    احتساب ضريبة المبيعات المحصلة (المخرجات) وخصم ضريبة المشتريات المسددة (المدخلات) وتحديد صافي الضريبة المستحقة للسداد بنسبة 14%.
                </p>
            </div>
            <a href="<?= BASE_URL ?>reports.php?type=vat" class="btn btn-primary" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); justify-content: center;">
                <i class="fas fa-calculator"></i>
                فتح الإقرار الضريبي
            </a>
        </div>

        <!-- Card 2: Item Profitability -->
        <div class="card" style="padding: 1.75rem; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #38bdf8;">
            <div>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div class="stat-icon blue" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--text-main);">
                            مبيعات وأرباح الأصناف وهامش الربحية
                        </h3>
                        <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; margin-top: 4px;">
                            تحليل هوامش الربح %
                        </span>
                    </div>
                </div>
                <p style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1.25rem;">
                    مقارنة إيرادات البيع بتكلفة الشراء لكل صنف لحساب مجمل الربح المحقق ونسبة هامش الربح وتحديد الأصناف الأكثر ربحية.
                </p>
            </div>
            <a href="<?= BASE_URL ?>reports.php?type=profitability" class="btn btn-primary" style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%); justify-content: center;">
                <i class="fas fa-coins"></i>
                عرض تقرير الربحية
            </a>
        </div>

        <!-- Card 3: Inventory Turnover & Movement -->
        <div class="card" style="padding: 1.75rem; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #f59e0b;">
            <div>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div class="stat-icon amber" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--text-main);">
                            حركة المخزون وسرعة الدوران
                        </h3>
                        <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; margin-top: 4px;">
                            الأصناف الراكدة ونواقص الرف
                        </span>
                    </div>
                </div>
                <p style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1.25rem;">
                    تحليل دوران الأصناف: قائمة الأصناف الأكثر مبيعاً، المنتجات الراكدة التي لم تتحرك، وتنبيهات الوصول لحد إعادة الطلب.
                </p>
            </div>
            <a href="<?= BASE_URL ?>reports.php?type=movement" class="btn btn-primary" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); justify-content: center;">
                <i class="fas fa-dolly"></i>
                عرض حركة المخزون
            </a>
        </div>

        <!-- Card 4: Customer Account Statement -->
        <div class="card" style="padding: 1.75rem; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #a855f7;">
            <div>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div class="stat-icon" style="background: rgba(168, 85, 247, 0.12); color: #a855f7; width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--text-main);">
                            كشف حساب العميل التفصيلي
                        </h3>
                        <span class="badge" style="background: rgba(168, 85, 247, 0.15); color: #a855f7; margin-top: 4px;">
                            متابعة الذمم المدينة
                        </span>
                    </div>
                </div>
                <p style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1.25rem;">
                    كشف حساب كامل لحركات مبيعات العميل، المدفوعات المسددة، المرتجعات، والرصيد المتبقي التراكمي مع إمكانية الطباعة.
                </p>
            </div>
            <a href="<?= BASE_URL ?>reports.php?type=customer_statement" class="btn btn-primary" style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); justify-content: center;">
                <i class="fas fa-file-invoice"></i>
                عرض كشف حساب عميل
            </a>
        </div>

        <!-- Card 5: Trial Balance Link -->
        <div class="card" style="padding: 1.75rem; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #38bdf8;">
            <div>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div class="stat-icon blue" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--text-main);">
                            ميزان المراجعة وقائمة الدخل
                        </h3>
                        <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; margin-top: 4px;">
                            التقارير المالية والختامية
                        </span>
                    </div>
                </div>
                <p style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1.25rem;">
                    الاطلاع على ميزان المراجعة بالأرصدة والمجاميع، التحقق من مطابقة القيود، وقائمة الدخل والأرباح الصافية.
                </p>
            </div>
            <a href="<?= BASE_URL ?>accounting.php?tab=trial_balance" class="btn btn-outline" style="justify-content: center;">
                <i class="fas fa-external-link-alt"></i>
                فتح ميزان المراجعة
            </a>
        </div>

        <!-- Card 6: Supplier Aging Link -->
        <div class="card" style="padding: 1.75rem; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #ef4444;">
            <div>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div class="stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444; width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--text-main);">
                            أعمار ديون الموردين (AP Aging)
                        </h3>
                        <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; margin-top: 4px;">
                            تحليل فترات السداد
                        </span>
                    </div>
                </div>
                <p style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1.25rem;">
                    تصنيف الفواتير والمديونيات المستحقة للموردين زمنياً (30، 60، 90، +120 يوماً) وإدارة التدفقات النقدية الخارجة.
                </p>
            </div>
            <a href="<?= BASE_URL ?>suppliers.php?action=aging" class="btn btn-outline" style="justify-content: center;">
                <i class="fas fa-external-link-alt"></i>
                فتح تقرير أعمار الديون
            </a>
        </div>
    </div>
</div>
