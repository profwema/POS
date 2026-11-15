<?php
/**
 * شاشة إعدادات النظام ومركز النسخ الاحتياطي - System Settings & Backup Hub
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "إعدادات النظام العامة والنسخ الاحتياطي";
?>

<div class="settings-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                <i class="fas fa-sliders-h" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                إعدادات المنظومة ومركز النسخ الاحتياطي
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                التحكم بالبيانات الرسمية للمنشأة، معايير الضرائب المصرية، إعدادات الكاشير، وحماية قواعد البيانات
            </p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>index.php" class="btn btn-outline">
                <i class="fas fa-arrow-right"></i>
                لوحة التحكم
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if ($msg === 'settings_saved'): ?>
        <div class="alert alert-success" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
            <span>تم حفظ وتحديث إعدادات النظام بنجاح وتطبيقها على كافة الشاشات.</span>
        </div>
    <?php elseif ($msg === 'backup_created'): ?>
        <div class="alert alert-success" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-database" style="font-size: 1.25rem;"></i>
            <span>تم توليد النسخة الاحتياطية لقاعدة البيانات وتخزينها بأمان بنجاح.</span>
        </div>
    <?php elseif ($msg === 'backup_deleted'): ?>
        <div class="alert alert-danger" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; border-radius: var(--radius-md); background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #ef4444; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-trash-alt" style="font-size: 1.25rem;"></i>
            <span>تم حذف ملف النسخة الاحتياطية بنجاح.</span>
        </div>
    <?php endif; ?>

    <!-- Settings Navigation Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; flex-wrap: wrap;">
        <a href="<?= BASE_URL ?>settings.php?tab=general" class="btn btn-outline <?= ($tab === 'general') ? 'active' : '' ?>" style="border-radius: 20px;">
            <i class="fas fa-building"></i>
            بيانات المنشأة والشركة
        </a>
        <a href="<?= BASE_URL ?>settings.php?tab=tax" class="btn btn-outline <?= ($tab === 'tax') ? 'active' : '' ?>" style="border-radius: 20px;">
            <i class="fas fa-file-invoice-dollar"></i>
            الضرائب المصرية والفواتير
        </a>
        <a href="<?= BASE_URL ?>settings.php?tab=pos" class="btn btn-outline <?= ($tab === 'pos') ? 'active' : '' ?>" style="border-radius: 20px;">
            <i class="fas fa-cash-register"></i>
            نقاط البيع والموازين
        </a>
        <a href="<?= BASE_URL ?>settings.php?tab=backup" class="btn btn-outline <?= ($tab === 'backup') ? 'active' : '' ?>" style="border-radius: 20px;">
            <i class="fas fa-database text-amber"></i>
            مركز النسخ الاحتياطي (Backups)
        </a>
    </div>

    <!-- TAB 1: General Settings -->
    <?php if ($tab === 'general'): ?>
        <div class="card" style="padding: 2rem;">
            <h3 style="margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-building text-primary"></i>
                البيانات التجارية والقانونية للمنشأة
            </h3>

            <form method="POST" action="<?= BASE_URL ?>settings.php">
                <?= Security::csrfInput() ?>
                <input type="hidden" name="action" value="save_settings">
                <input type="hidden" name="group" value="general">

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">اسم المنشأة / الشركة (المطبوع بالفاتورة) *</label>
                        <input type="text" name="settings[company_name]" class="form-control" value="<?= htmlspecialchars($settings['company_name'] ?? 'شركة النظم الحديثة للتجارة والتوزيع') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">رقم السجل التجاري</label>
                        <input type="text" name="settings[commercial_registry]" class="form-control" value="<?= htmlspecialchars($settings['commercial_registry'] ?? '987654') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">رقم الهاتف الرئيسي</label>
                        <input type="text" name="settings[company_phone]" class="form-control" value="<?= htmlspecialchars($settings['company_phone'] ?? '01002283766') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">العنوان الرسمي المعتمد</label>
                        <input type="text" name="settings[company_address]" class="form-control" value="<?= htmlspecialchars($settings['company_address'] ?? 'جمهورية مصر العربية') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">رمز العملة الرسمي</label>
                        <input type="text" name="settings[currency_symbol]" class="form-control" value="<?= htmlspecialchars($settings['currency_symbol'] ?? 'ج.م') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">كود العملة الدولي</label>
                        <input type="text" name="settings[currency_code]" class="form-control" value="<?= htmlspecialchars($settings['currency_code'] ?? 'EGP') ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">توقيع العلامة الهندسية وتطوير النظام</label>
                    <input type="text" name="settings[brand_signature]" class="form-control" value="<?= htmlspecialchars($settings['brand_signature'] ?? 'المبرمج | Almubarmig - م/ وئام محمد السوداني') ?>" readonly style="background: rgba(255,255,255,0.05); color: #38bdf8;">
                </div>

                <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                        <i class="fas fa-save"></i>
                        حفظ البيانات التجارية
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- TAB 2: Tax Settings -->
    <?php if ($tab === 'tax'): ?>
        <div class="card" style="padding: 2rem; border-top: 4px solid #10b981;">
            <h3 style="margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-invoice-dollar text-emerald"></i>
                إعدادات الضرائب المصرية وقانون القيمة المضافة
            </h3>

            <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); padding: 1.25rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <h4 style="margin: 0 0 0.5rem 0; font-size: 0.95rem; font-weight: 800; color: #10b981;">
                    <i class="fas fa-info-circle"></i> تنويه تشريعي وضريبي (قانون 67 لسنة 2016)
                </h4>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">
                    النسبة القياسية العامة لضريبة القيمة المضافة في مصر هي 14% على معظم السلع والخدمات التجارية. يتم احتساب الضريبة تلقائياً في الإقرار الضريبي الرسمي (نموذج 10) وطباعتها على إيصالات وفواتير المبيعات.
                </p>
            </div>

            <form method="POST" action="<?= BASE_URL ?>settings.php">
                <?= Security::csrfInput() ?>
                <input type="hidden" name="action" value="save_settings">
                <input type="hidden" name="group" value="tax">

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">رقم التسجيل الضريبي المصري الموحد (9 أرقام) *</label>
                        <input type="text" name="settings[tax_number]" class="form-control" value="<?= htmlspecialchars($settings['tax_number'] ?? '123456789') ?>" required style="font-family: monospace; font-size: 1.1rem; color: #38bdf8; font-weight: 700;">
                        <small style="color: var(--text-muted); font-size: 0.78rem;">الرقم المعتمد لدى مصلحة الضرائب المصرية ومنظومة الفاتورة الإلكترونية.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">نسبة ضريبة القيمة المضافة الافتراضية (%) *</label>
                        <input type="number" step="0.5" min="0" max="100" name="settings[vat_percent]" class="form-control" value="<?= htmlspecialchars($settings['vat_percent'] ?? '14') ?>" required style="font-family: monospace; font-size: 1.1rem; font-weight: 700;">
                        <small style="color: var(--text-muted); font-size: 0.78rem;">النسبة القانونية العامة (14%).</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">تفعيل الضريبة تلقائياً في فواتير الكاشير POS</label>
                        <select name="settings[enable_vat_on_pos]" class="form-control">
                            <option value="1" <?= (($settings['enable_vat_on_pos'] ?? '1') == '1') ? 'selected' : '' ?>>مفعل (إدراج الضريبة 14% تلقائياً)</option>
                            <option value="0" <?= (($settings['enable_vat_on_pos'] ?? '1') == '0') ? 'selected' : '' ?>>معطل (البيع بدون ضريبة افتراضية)</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                        <i class="fas fa-save"></i>
                        حفظ المعايير الضريبية
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- TAB 3: POS Settings -->
    <?php if ($tab === 'pos'): ?>
        <div class="card" style="padding: 2rem; border-top: 4px solid var(--brand-amber);">
            <h3 style="margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-cash-register text-amber"></i>
                إعدادات الكاشير ونقاط البيع السريعة وباركود الموازين
            </h3>

            <form method="POST" action="<?= BASE_URL ?>settings.php">
                <?= Security::csrfInput() ?>
                <input type="hidden" name="action" value="save_settings">
                <input type="hidden" name="group" value="pos">

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">تفعيل قراءة باركود الموازين الإلكترونية للوزن</label>
                        <select name="settings[enable_scale_barcodes]" class="form-control">
                            <option value="1" <?= (($settings['enable_scale_barcodes'] ?? '1') == '1') ? 'selected' : '' ?>>مفعل (استخراج كود الصنف والوزن تلقائياً)</option>
                            <option value="0" <?= (($settings['enable_scale_barcodes'] ?? '1') == '0') ? 'selected' : '' ?>>معطل</option>
                        </select>
                        <small style="color: var(--text-muted); font-size: 0.78rem;">مخصص لأقسام الجبن واللحوم والخضار والأسماك.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">بادئة باركود الوزن (Scale Barcode Prefix)</label>
                        <input type="text" name="settings[scale_barcode_prefix]" class="form-control" value="<?= htmlspecialchars($settings['scale_barcode_prefix'] ?? '20') ?>" style="font-family: monospace; font-size: 1.1rem; font-weight: 700;">
                        <small style="color: var(--text-muted); font-size: 0.78rem;">الافتراضي في مصر 20 أو 99 (يبدأ به الباركود المطبوع من الميزان).</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">السماح بالبيع بالسالب عند عدم توفر رصيد مخزني</label>
                        <select name="settings[allow_negative_sales]" class="form-control">
                            <option value="0" <?= (($settings['allow_negative_sales'] ?? '0') == '0') ? 'selected' : '' ?>>ممنوع (منع البيع وحماية دقة الجرد)</option>
                            <option value="1" <?= (($settings['allow_negative_sales'] ?? '0') == '1') ? 'selected' : '' ?>>مسموح (البيع مع رصيد سالب)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">عرض ورق الطابعة الحرارية الافتراضي</label>
                        <select name="settings[receipt_printer_width]" class="form-control">
                            <option value="80mm" <?= (($settings['receipt_printer_width'] ?? '80mm') == '80mm') ? 'selected' : '' ?>>80 مم (طابعة فواتير كاشير قياسية)</option>
                            <option value="58mm" <?= (($settings['receipt_printer_width'] ?? '80mm') == '58mm') ? 'selected' : '' ?>>58 مم (طابعة إيصالات صغيرة / بلوتوث)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label class="form-label">نص ذيل إيصال الكاشير الحراري (Footer Receipt Note)</label>
                    <textarea name="settings[receipt_footer_text]" class="form-control" rows="3"><?= htmlspecialchars($settings['receipt_footer_text'] ?? 'شكراً لزيارتكم • نظام نقاط البيع مهندس بواسطة براند المبرمج (01002283766)') ?></textarea>
                    <small style="color: var(--text-muted); font-size: 0.78rem;">يظهر في نهاية كل فاتورة بيع مطبوعة للعميل.</small>
                </div>

                <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                        <i class="fas fa-save"></i>
                        حفظ إعدادات الكاشير
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- TAB 4: Database Backup Center -->
    <?php if ($tab === 'backup'): ?>
        <div class="card" style="padding: 2rem; border-top: 4px solid #38bdf8; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1.3rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-database text-blue"></i>
                        مركز النسخ الاحتياطي لقاعدة البيانات (Automated SQL Dump)
                    </h3>
                    <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.85rem;">
                        توليد وتنزيل نسخ كاملة وشاملة لجميع الجداول والفواتير والحركات المالية بضغطة زر واحدة
                    </p>
                </div>

                <a href="<?= BASE_URL ?>settings.php?action=backup_create" class="btn btn-primary" style="padding: 0.85rem 1.75rem; font-weight: 700; background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);">
                    <i class="fas fa-download"></i>
                    إنشاء وتنزيل نسخة احتياطية الآن
                </a>
            </div>

            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <div style="font-size: 0.8rem; color: var(--text-muted);">إجمالي النسخ المخزنة</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem;">
                        <?= count($backups) ?> ملف
                    </div>
                </div>

                <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <div style="font-size: 0.8rem; color: var(--text-muted);">مسار تخزين النسخ الآمن</div>
                    <div style="font-size: 0.85rem; font-family: monospace; color: var(--text-main); margin-top: 0.5rem; word-break: break-all;">
                        storage/backups/
                    </div>
                </div>

                <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <div style="font-size: 0.8rem; color: var(--text-muted);">أحدث نسخة مسجلة</div>
                    <div style="font-size: 0.95rem; font-weight: 700; color: #10b981; margin-top: 0.35rem;">
                        <?= !empty($backups) ? htmlspecialchars($backups[0]['age']) : 'لا توجد نسخ سابقة' ?>
                    </div>
                </div>
            </div>

            <!-- Backups Table -->
            <div style="overflow: hidden; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <div style="padding: 1rem 1.25rem; background: var(--bg-surface); border-bottom: 1px solid var(--border-color);">
                    <h4 style="margin: 0; font-size: 1rem; font-weight: 700; color: var(--text-main);">
                        سجل ملفات النسخ الاحتياطي على الخادم
                    </h4>
                </div>

                <div class="table-responsive">
                    <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
                        <thead>
                            <tr style="background: var(--bg-surface); text-align: right;">
                                <th style="padding: 0.85rem 1rem;">اسم الملف</th>
                                <th style="padding: 0.85rem 1rem; text-align: center;">حجم الملف</th>
                                <th style="padding: 0.85rem 1rem; text-align: center;">تاريخ الإنشاء</th>
                                <th style="padding: 0.85rem 1rem; text-align: center;">منذ</th>
                                <th style="padding: 0.85rem 1rem; text-align: center; width: 180px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($backups)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                        <i class="fas fa-folder-open" style="font-size: 2.5rem; margin-bottom: 0.5rem; display: block; opacity: 0.3;"></i>
                                        لم يتم إنشاء نسخ احتياطية حتى الآن. انقر على "إنشاء وتنزيل نسخة احتياطية الآن" لحفظ بياناتك.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($backups as $bk): ?>
                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                        <td style="padding: 0.85rem 1rem; font-family: monospace; font-size: 0.9rem; color: #38bdf8;">
                                            <i class="fas fa-file-code" style="margin-left: 0.4rem; color: #a855f7;"></i>
                                            <?= htmlspecialchars($bk['filename']) ?>
                                        </td>
                                        <td style="padding: 0.85rem 1rem; text-align: center; font-family: monospace;">
                                            <?= htmlspecialchars($bk['size_formatted']) ?>
                                        </td>
                                        <td style="padding: 0.85rem 1rem; text-align: center; font-family: monospace; font-size: 0.85rem;">
                                            <?= htmlspecialchars($bk['created_at']) ?>
                                        </td>
                                        <td style="padding: 0.85rem 1rem; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
                                            <?= htmlspecialchars($bk['age']) ?>
                                        </td>
                                        <td style="padding: 0.85rem 1rem; text-align: center;">
                                            <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                                <a href="<?= BASE_URL ?>settings.php?action=backup_download&file=<?= urlencode($bk['filename']) ?>" class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; color: #10b981; border-color: rgba(16, 185, 129, 0.3);" title="تنزيل إلى جهازك">
                                                    <i class="fas fa-download"></i> تنزيل
                                                </a>
                                                <a href="<?= BASE_URL ?>settings.php?action=backup_delete&file=<?= urlencode($bk['filename']) ?>" class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; color: #ef4444; border-color: rgba(239, 68, 68, 0.3);" onclick="return confirm('هل أنت متأكد من رغبتك في حذف ملف النسخة الاحتياطية نهائياً؟')" title="حذف الملف">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.tab-nav-btn.active, .btn-outline.active {
    background: var(--brand-amber) !important;
    color: #000 !important;
    border-color: var(--brand-amber) !important;
    font-weight: 700;
}
</style>
