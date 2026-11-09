-- ========================================================================
-- WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
-- البيانات الافتراضية الأولية: 01_core_seed.sql
-- مهندس وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
-- ========================================================================

-- 1. الفرع الرئيسي الافتراضي
INSERT INTO `branches` (`id`, `code`, `name`, `phone`, `address`, `is_active`) VALUES
(1, 'BR001', 'الفرع الرئيسي - المركز التجاري', '01002283766', 'القاهرة - جمهورية مصر العربية', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 2. الأدوار الوظيفية القياسية
INSERT INTO `roles` (`id`, `name`, `slug`, `description`) VALUES
(1, 'مدير النظام العام', 'admin', 'صلاحيات كاملة وغير مقيدة لكافة شاشات ووظائف النظام'),
(2, 'مدير الفرع', 'manager', 'إدارة العمليات اليومية والفواتير والتقارير داخل الفرع'),
(3, 'المحاسب المالي', 'accountant', 'إدارة الحسابات، قيود اليومية، وسندات الصرف والإيراد والمصروفات'),
(4, 'كاشير نقطة البيع', 'cashier', 'شاشة البيع السريعة، الورديات، وتسليم النقدية فقط'),
(5, 'أمين المستودع', 'storekeeper', 'حركة المخزون، الاستلام، أذون الصرف، والتحويل بين المخازن')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 3. الصلاحيات الأساسية للموديولات
INSERT INTO `permissions` (`id`, `module`, `slug`, `name`, `description`) VALUES
-- موديول الكاشير POS
(1, 'pos', 'pos.view', 'فتح شاشة الكاشير', 'إمكانية فتح شاشة نقطة البيع السريعة'),
(2, 'pos', 'pos.sell', 'إتمام عمليات البيع', 'إصدار الفواتير وتحصيل المبالغ'),
(3, 'pos', 'pos.discount', 'تطبيق خصم يدوي', 'تطبيق خصم استثنائي على الفاتورة'),
(4, 'pos', 'pos.shift_close', 'إغلاق الوردية', 'تسليم الوردية وحساب العجز والزيادة'),
-- موديول المبيعات
(5, 'sales', 'sales.view', 'استعراض فواتير المبيعات', 'الاطلاع على جدول الفواتير السابقة'),
(6, 'sales', 'sales.create', 'إصدار فاتورة بيع كبرى', 'إصدار فواتير بيع آجلة ونقدية'),
(7, 'sales', 'sales.return', 'إصدار مرتجع مبيعات', 'استرجاع بضاعة مباعة وتعديل الرصيد'),
(8, 'sales', 'sales.quotes', 'إدارة عروض الأسعار', 'إنشاء وتعديل وتحويل عروض الأسعار'),
-- موديول المشتريات
(9, 'purchases', 'purchases.view', 'استعراض فواتير المشتريات', 'عرض مشتريات الموردين السابقة'),
(10, 'purchases', 'purchases.create', 'إضافة فاتورة مشتريات', 'تسجيل بضائع جديدة في المخزن'),
(11, 'purchases', 'purchases.return', 'مرتجع مشتريات', 'إرجاع بضاعة للموردين'),
-- موديول المخازن
(12, 'inventory', 'inventory.view', 'عرض المخزون وبطاقة الصنف', 'الاطلاع على كميات الأصناف والأسعار'),
(13, 'inventory', 'inventory.manage', 'إضافة وتعديل الأصناف', 'إدخال أصناف جديدة وتعديل أسعارها'),
(14, 'inventory', 'inventory.transfer', 'التحويل بين المخازن', 'نقل بضاعة بين المستودعات'),
(15, 'inventory', 'inventory.stocktake', 'جرد المخازن والتسويات', 'إجراء جرد فعلي وتعديل الفروق'),
-- موديول الحسابات
(16, 'accounting', 'accounting.view', 'عرض شجرة الحسابات واليومية', 'استعراض الحسابات والقيود'),
(17, 'accounting', 'accounting.journal', 'إضافة قيود يومية', 'تسجيل القيود المزدوجة المتزنة'),
(18, 'accounting', 'accounting.expenses', 'تسجيل المصروفات', 'إثبات سندات الصرف والمصروفات اليومية'),
(19, 'accounting', 'accounting.reports', 'القوائم الختامية والميزانية', 'ميزان المراجعة وقائمة الدخل'),
-- موديول الإدارة
(20, 'settings', 'settings.manage', 'إعدادات النظام العامة', 'تعديل بيانات المنشأة والضريبة'),
(21, 'settings', 'settings.users', 'إدارة الموظفين والمستخدمين', 'إنشاء وتعديل مستخدمين وتحديد الصلاحيات'),
(22, 'settings', 'settings.backup', 'النسخ الاحتياطي', 'تنزيل ومزامنة قاعدة البيانات')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 4. إسناد كافة الصلاحيات لدور المدير العام (Admin)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, `id` FROM `permissions`
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

-- إسناد صلاحيات البيع فقط للكاشير (Cashier)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(4, 1), (4, 2), (4, 4)
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

-- 5. المستخدمون الافتراضيون
-- كلمة مرور المدير admin هي: admin123
-- كلمة مرور الكاشير cashier1 هي: 123456
INSERT INTO `users` (`id`, `branch_id`, `role_id`, `username`, `password_hash`, `full_name`, `phone`, `email`, `is_active`) VALUES
(1, 1, 1, 'admin', '$2y$10$JQDE0WP5cPOabImqs32wW.SZbioiG6I8ztLKfs8vyXh87xAWICoJO', 'مدير النظام (م/ وئام السوداني)', '01002283766', 'admin@almubarmig.com', 1),
(2, 1, 4, 'cashier1', '$2y$10$OGmKPyNH1ZYrjOwwaD36QuxDyfYFyttjmA3g63Dmd4A5nUtA1Gmxm', 'كاشير 1 - وردية النهار', '01000000000', 'cashier@almubarmig.com', 1)
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);

-- 6. إعدادات المنشأة العامة والسوق المصري
INSERT INTO `system_settings` (`key`, `value`, `group_name`, `description`) VALUES
('company_name', 'شركة النظم الحديثة للتجارة والتوزيع', 'general', 'اسم المنشأة التجاري الرسمي'),
('tax_number', '123456789', 'tax', 'رقم التسجيل الضريبي المصري الموحد (9 أرقام)'),
('commercial_registry', '987654', 'general', 'رقم السجل التجاري'),
('company_phone', '01002283766', 'general', 'رقم الهاتف الرئيسي للتواصل'),
('company_address', 'جمهورية مصر العربية', 'general', 'العنوان الرئيسي المقيد في الفاتورة'),
('vat_percent', '14', 'tax', 'نسبة ضريبة القيمة المضافة الافتراضية (%)'),
('currency_symbol', 'ج.م', 'general', 'رمز العملة الرسمية في الواجهات والطباعة'),
('currency_code', 'EGP', 'general', 'كود العملة المصرفي الدولي'),
('enable_scale_barcodes', '1', 'pos', 'تفعيل قراءة باركود الموازين الإلكترونية (1 نعم، 0 لا)'),
('scale_barcode_prefix', '20', 'pos', 'بادئة باركود الوزن للموازين (الافتراضي 20 أو 99)'),
('allow_negative_sales', '0', 'inventory', 'السماح بالبيع بالسالب عند عدم توفر رصيد مخزني'),
('receipt_footer_text', 'شكراً لزيارتكم • نظام نقاط البيع مهندس بواسطة براند المبرمج (01002283766)', 'pos', 'النص المطبوع في ذيل إيصال الكاشير الحراري'),
('brand_signature', 'المبرمج | Almubarmig', 'branding', 'علامة المطور الهندسية')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

