<?php
/**
 * شاشة إقرار ضريبة القيمة المضافة المصرية (نموذج 10)
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "إقرار ضريبة القيمة المضافة (نموذج 10)";
$v = $vatData;
?>

<div class="vat-declaration-page">
    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-file-invoice-dollar" style="color: #10b981; margin-left: 0.5rem;"></i>
                إقرار ضريبة القيمة المضافة (نموذج 10 المعتمد)
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                وفقاً لأحكام قانون الضريبة على القيمة المضافة المصري رقم 67 لسنة 2016 وتعديلاته
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" onclick="window.print()">
                <i class="fas fa-print"></i>
                طباعة الإقرار الضريبي
            </button>
            <a href="<?= BASE_URL ?>reports.php" class="btn btn-outline">
                <i class="fas fa-arrow-right"></i>
                مركز التقارير
            </a>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
        <form method="GET" action="<?= BASE_URL ?>reports.php" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
            <input type="hidden" name="type" value="vat">

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">الفترة الضريبية من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.82rem;">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i>
                احتساب الإقرار الضريبي
            </button>
        </form>
    </div>

    <!-- Official Tax Declaration Document Form -->
    <div class="card" style="padding: 2rem; border-top: 5px solid #10b981;">
        <!-- Header Document Info -->
        <div style="border-bottom: 2px solid var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 style="margin: 0; font-size: 1.35rem; font-weight: 800; color: var(--text-main);">
                        <?= htmlspecialchars($v['company_name']) ?>
                    </h2>
                    <div style="display: flex; gap: 1.5rem; font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem; flex-wrap: wrap;">
                        <div>
                            <strong>رقم التسجيل الضريبي الموحد:</strong>
                            <span style="font-family: monospace; color: #38bdf8; font-weight: 700;"><?= htmlspecialchars($v['tax_number']) ?></span>
                        </div>
                        <div>
                            <strong>السجل التجاري:</strong>
                            <span style="font-family: monospace;"><?= htmlspecialchars($v['commercial_registry']) ?></span>
                        </div>
                        <div>
                            <strong>نسبة الضريبة العامة:</strong>
                            <span style="font-weight: 700; color: #10b981;">14%</span>
                        </div>
                    </div>
                </div>

                <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.75rem 1.25rem; border-radius: var(--radius-md); text-align: center;">
                    <div style="font-size: 0.75rem; color: var(--text-muted);">الفترة المحاسبية للإقرار</div>
                    <strong style="color: #10b981; font-size: 0.95rem;">
                        <?= Helpers::formatDate($dateFrom) ?> إلى <?= Helpers::formatDate($dateTo) ?>
                    </strong>
                </div>
            </div>
        </div>

        <!-- Section 1: Output VAT (Sales) -->
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(56, 189, 248, 0.08); padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 0.75rem;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #38bdf8;">
                    أولاً: الضريبة على المبيعات (ضريبة المخرجات)
                </h3>
                <span class="badge" style="background: rgba(56, 189, 248, 0.2); color: #38bdf8;">
                    عدد فواتير المبيعات: <?= number_format($v['sales']['invoices_count']) ?>
                </span>
            </div>

            <table class="table" style="margin-bottom: 0; font-size: 0.9rem;">
                <thead>
                    <tr>
                        <th>البيان الضريبي للمبيعات</th>
                        <th style="width: 200px; text-align: left;">القيمة الخاضعة (بدون ضريبة)</th>
                        <th style="width: 200px; text-align: left;">فئة الضريبة</th>
                        <th style="width: 220px; text-align: left; color: #38bdf8;">مبلغ الضريبة المحصلة</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>مبيعات سلع وخدمات خاضعة للنسبة العامة (14%)</td>
                        <td style="text-align: left; font-weight: 600;"><?= Helpers::formatCurrency($v['sales']['taxable_sales']) ?></td>
                        <td style="text-align: left;">14%</td>
                        <td style="text-align: left; font-weight: 700; color: #38bdf8;"><?= Helpers::formatCurrency($v['sales']['gross_tax']) ?></td>
                    </tr>
                    <tr>
                        <td>مبيعات سلع وخدمات معفاة من الضريبة (أرز، مواد خام...)</td>
                        <td style="text-align: left; font-weight: 600;"><?= Helpers::formatCurrency($v['sales']['exempt_sales']) ?></td>
                        <td style="text-align: left;">0% (معفى)</td>
                        <td style="text-align: left; color: var(--text-muted);">-</td>
                    </tr>
                    <tr style="color: #ef4444;">
                        <td>يخصم: ضريبة مرتجعات المبيعات المستردة للعملاء</td>
                        <td style="text-align: left;">-</td>
                        <td style="text-align: left;">14%</td>
                        <td style="text-align: left; font-weight: 700;">(<?= Helpers::formatCurrency($v['sales']['returned_tax']) ?>)</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr style="background: rgba(56, 189, 248, 0.08); font-weight: 800;">
                        <td colspan="3" style="text-align: left;">إجمالي صافي ضريبة المبيعات (المخرجات):</td>
                        <td style="text-align: left; color: #38bdf8; font-size: 1.1rem;"><?= Helpers::formatCurrency($v['sales']['net_output_vat']) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Section 2: Input VAT (Purchases) -->
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(245, 158, 11, 0.08); padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 0.75rem;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #f59e0b;">
                    ثانياً: الضريبة على المشتريات (ضريبة المدخلات القابلة للخصم)
                </h3>
                <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b;">
                    عدد فواتير الشراء: <?= number_format($v['purchases']['purchases_count']) ?>
                </span>
            </div>

            <table class="table" style="margin-bottom: 0; font-size: 0.9rem;">
                <thead>
                    <tr>
                        <th>البيان الضريبي للمشتريات</th>
                        <th style="width: 200px; text-align: left;">القيمة بدون ضريبة</th>
                        <th style="width: 200px; text-align: left;">فئة الضريبة</th>
                        <th style="width: 220px; text-align: left; color: #f59e0b;">مبلغ ضريبة المدخلات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>مشتريات بضائع محلية خاضعة للنسبة العامة (14%)</td>
                        <td style="text-align: left; font-weight: 600;"><?= Helpers::formatCurrency($v['purchases']['taxable_purchases']) ?></td>
                        <td style="text-align: left;">14%</td>
                        <td style="text-align: left; font-weight: 700; color: #f59e0b;"><?= Helpers::formatCurrency($v['purchases']['gross_tax']) ?></td>
                    </tr>
                    <tr>
                        <td>مشتريات معفاة من الضريبة</td>
                        <td style="text-align: left; font-weight: 600;"><?= Helpers::formatCurrency($v['purchases']['exempt_purchases']) ?></td>
                        <td style="text-align: left;">0%</td>
                        <td style="text-align: left; color: var(--text-muted);">-</td>
                    </tr>
                    <tr style="color: #ef4444;">
                        <td>يخصم: ضريبة مرتجعات المشتريات للموردين</td>
                        <td style="text-align: left;">-</td>
                        <td style="text-align: left;">14%</td>
                        <td style="text-align: left; font-weight: 700;">(<?= Helpers::formatCurrency($v['purchases']['returned_tax']) ?>)</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr style="background: rgba(245, 158, 11, 0.08); font-weight: 800;">
                        <td colspan="3" style="text-align: left;">إجمالي صافي ضريبة المشتريات القابلة للخصم (المدخلات):</td>
                        <td style="text-align: left; color: #f59e0b; font-size: 1.1rem;"><?= Helpers::formatCurrency($v['purchases']['net_input_vat']) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Section 3: Final Tax Settlement -->
        <div style="background: rgba(148, 163, 184, 0.08); border: 2px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; font-size: 1rem;">
                <span style="color: var(--text-muted);">صافي ضريبة المبيعات (المخرجات):</span>
                <strong style="color: #38bdf8;"><?= Helpers::formatCurrency($v['sales']['net_output_vat']) ?></strong>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; font-size: 1rem;">
                <span style="color: var(--text-muted);">يخصم: صافي ضريبة المشتريات (المدخلات):</span>
                <strong style="color: #f59e0b;">(<?= Helpers::formatCurrency($v['purchases']['net_input_vat']) ?>)</strong>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 2px solid var(--border-color); padding-top: 1rem; margin-top: 1rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: <?= ($v['net_tax_payable'] >= 0) ? '#10b981' : '#38bdf8' ?>;">
                        <?= ($v['net_tax_payable'] >= 0) ? 'صافي الضريبة المستحقة للسداد لمصلحة الضرائب:' : 'رصيد دائن للمنشأة يرحل للفترة القادمة:' ?>
                    </h3>
                    <small style="color: var(--text-muted);">تسدد بنكياً أو عبر بوابة السداد الإلكتروني لوزارة المالية</small>
                </div>
                <strong style="font-size: 1.8rem; font-weight: 900; color: <?= ($v['net_tax_payable'] >= 0) ? '#10b981' : '#38bdf8' ?>;">
                    <?= Helpers::formatCurrency(abs($v['net_tax_payable'])) ?>
                </strong>
            </div>
        </div>

        <!-- Document Signatures Footer -->
        <div style="display: flex; justify-content: space-between; margin-top: 3rem; padding-top: 2rem; border-top: 1px dashed var(--border-color); text-align: center; color: var(--text-muted); font-size: 0.85rem;">
            <div style="width: 200px;">
                <p>إعداد المحاسب القانوني</p>
                <div style="height: 40px;"></div>
                <p>.......................................</p>
            </div>
            <div style="width: 200px;">
                <p>المدير المالي للمنشأة</p>
                <div style="height: 40px;"></div>
                <p>.......................................</p>
            </div>
            <div style="width: 200px;">
                <p>خاتم المنشأة التجاري</p>
                <div style="height: 40px;"></div>
                <p>.......................................</p>
            </div>
        </div>
    </div>
</div>
