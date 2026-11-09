-- ============================================================================
-- WAM Tech POS & ERP - Phase 2: POS, Shifts & Sales Seed Data
-- Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
-- Database: wamtech_pos_db
-- ============================================================================

-- 1. العملاء الافتراضيين
INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `credit_limit`, `current_balance`, `points`, `is_active`) VALUES
(1, 'عميل نقدي عام (Walk-in Customer)', '01000000000', 'cash@pos.local', 'نقطة البيع - الفرع 1', 0.00, 0.00, 0, 1),
(2, 'م/ حسام مصطفى (شركة النور للتجارة)', '01123456789', 'hossam@alnoor.com', 'القاهرة - مصر الجديدة', 15000.00, 0.00, 25, 1),
(3, 'د/ أحمد عادل الرفاعي', '01234567890', 'ahmed.refaie@gmail.com', 'القاهرة - مدينة نصر', 5000.00, 0.00, 10, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 2. إعدادات نقطة البيع السريعة
INSERT INTO `system_settings` (`key`, `value`, `group_name`, `description`) VALUES
('pos_default_customer', '1', 'pos', 'معرف العميل النقدي الافتراضي عند فتح شاشة البيع'),
('pos_tax_enabled', '1', 'pos', 'تفعيل احتساب ضريبة القيمة المضافة 14% تلقائياً في السلة'),
('pos_allow_negative_stock', '0', 'pos', 'السماح بالبيع بالسالب عند نفاد المخزون (0=ممنوع، 1=مسموح)'),
('pos_camera_scanner', '1', 'pos', 'تفعيل مسح الباركود عبر كاميرا الموبايل/الويب كاميرا'),
('pos_receipt_width', '80mm', 'pos', 'عرض بكرة ورق الطابعة الحرارية (80mm أو 58mm)'),
('pos_receipt_footer', 'برمجة وتطوير: المبرمج (01002283766) almubarmig.com', 'pos', 'نص تذييل الإيصال الحراري')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
