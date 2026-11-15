-- ============================================================================
-- WAM Tech POS & ERP - Phase 3: Supply Chain Seed Data
-- Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
-- Database: wamtech_pos_db
-- ============================================================================

-- 1. الموردين المعتمدين في السوق المصري (Suppliers)
INSERT INTO `suppliers` (
    `id`, `name`, `company_name`, `contact_person`, `phone`, `email`, 
    `address`, `tax_number`, `commercial_registry`, `opening_balance`, 
    `current_balance`, `credit_limit`, `payment_terms_days`, `notes`, `is_active`
) VALUES
(1, 'شركة الأهرام للتجارة والتوزيع', 'الأهرام للسلع التموينية والزيوت', 'أ/ محمود الهواري', '01011223344', 'ahram@supply-eg.com', 'سوق العبور - مجمع المواد الغذائية، القاهرة', '100200300', '45678', 25000.00, 33500.00, 100000.00, 30, 'مورد رئيسي لزيوت الطعام والبقوليات والشاي', 1),
(2, 'مؤسسة النيل للمنتجات الغذائية والألبان', 'مجموعة النيل لمنتجات الألبان', 'م/ حسام الشاذلي', '01122334455', 'nile.dairy@supply-eg.com', 'المنطقة الصناعية الثالثة، السادس من أكتوبر', '200300400', '65432', 15000.00, 15000.00, 80000.00, 15, 'وكيل منتجات الألبان والأجبان الطازجة والعصائر', 1),
(3, 'الشركة الهندسية للأجهزة والتوريدات الإلكترونية', 'المهندسون المتحدون للتقنية', 'م/ كريم نبيل', '01233445566', 'eng.tech@supply-eg.com', 'شارع الطيران، مدينة نصر، القاهرة', '300400500', '89012', 0.00, 12400.00, 50000.00, 45, 'مورد طابعات الباركود والشواحن والكابلات', 1),
(4, 'مصنع الشرق للبلاستيك ومواد التعبئة', 'الشرق لمواد التغليف والتعبئة', 'أ/ سيد المنشاوي', '01555667788', 'sharq.pack@supply-eg.com', 'العاشر من رمضان، المنطقة B4', '400500600', '34567', 5000.00, 5000.00, 40000.00, 30, 'مورد أكياس التعبئة وبكر ورق الفواتير الحراري 80mm', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `current_balance` = VALUES(`current_balance`);

-- 2. فواتير المشتريات التجريبية (Purchases)
INSERT INTO `purchases` (
    `id`, `invoice_number`, `supplier_invoice_number`, `supplier_id`, `store_id`, `branch_id`,
    `invoice_date`, `subtotal`, `discount_amount`, `tax_amount`, `grand_total`, 
    `paid_amount`, `remaining_amount`, `payment_status`, `payment_method`, `notes`, `created_by`
) VALUES
(1, 'PUR-20261101-0001', 'INV-SUP-8841', 1, 1, 1, '2026-11-01', 12000.00, 500.00, 1610.00, 13110.00, 5000.00, 8110.00, 'partial', 'cash', 'توريد دفعة زيوت طعام وأرز وشاي للمستودع المركزي', 1),
(2, 'PUR-20261110-0002', 'TECH-INV-220', 3, 1, 1, '2026-11-10', 11000.00, 0.00, 1540.00, 12540.00, 0.00, 12540.00, 'unpaid', 'credit', 'توريد اكسسوارات وشواحن على الحساب - أجل 45 يوماً', 1),
(3, 'PUR-20261114-0003', 'MILK-9901', 2, 2, 1, '2026-11-14', 7000.00, 200.00, 952.00, 7752.00, 7752.00, 0.00, 'paid', 'bank_transfer', 'سداد نقدي عبر تحويل بنكي لتوريد ألبان جهينة لفرع الجمهورية', 1)
ON DUPLICATE KEY UPDATE `invoice_number` = VALUES(`invoice_number`);

-- 3. بنود فواتير الشراء (Purchase Items)
INSERT INTO `purchase_items` (
    `id`, `purchase_id`, `item_id`, `quantity`, `unit_cost`, `tax_percent`, `tax_amount`, `total_cost`, `batch_number`, `expire_date`
) VALUES
-- بنود فاتورة 1
(1, 1, 2, 50.00, 85.00, 14.00, 595.00, 4845.00, 'BATCH-SLITE-2026A', '2027-10-31'),
(2, 1, 3, 100.00, 32.00, 0.00, 0.00, 3200.00, 'BATCH-DOHA-2026', '2027-05-31'),
(3, 1, 4, 80.00, 50.00, 14.00, 560.00, 4560.00, 'BATCH-TEA-88', '2028-01-15'),

-- بنود فاتورة 2
(4, 2, 5, 200.00, 11.50, 14.00, 322.00, 2622.00, 'CAN-COCA-909', '2027-06-30'),
(5, 2, 6, 20.00, 120.00, 14.00, 336.00, 2736.00, 'BATCH-CHG-65W', NULL),

-- بنود فاتورة 3
(6, 3, 1, 180.00, 38.00, 14.00, 957.60, 7797.60, 'BATCH-MILK-NOV26', '2026-12-15')
ON DUPLICATE KEY UPDATE `total_cost` = VALUES(`total_cost`);

-- 4. عروض أسعار تجريبية للعملاء (Quotations)
INSERT INTO `quotations` (
    `id`, `quotation_number`, `customer_id`, `store_id`, `branch_id`,
    `quotation_date`, `expiry_date`, `subtotal`, `discount_amount`, `tax_amount`, 
    `grand_total`, `status`, `notes`, `created_by`
) VALUES
(1, 'QUO-20261112-0001', 1, 2, 1, '2026-11-12', '2026-11-26', 4200.00, 100.00, 574.00, 4674.00, 'sent', 'عرض سعر توريد مواد تموينية لجمعية خيرية', 1),
(2, 'QUO-20261114-0002', 2, 2, 1, '2026-11-14', '2026-11-28', 8500.00, 250.00, 1155.00, 9405.00, 'accepted', 'عرض سعر لفرع شركة التقنية جاهز للتحويل إلى فاتورة', 1)
ON DUPLICATE KEY UPDATE `quotation_number` = VALUES(`quotation_number`);

-- 5. بنود عروض الأسعار (Quotation Items)
INSERT INTO `quotation_items` (`id`, `quotation_id`, `item_id`, `quantity`, `unit_price`, `tax_amount`, `total_price`) VALUES
(1, 1, 2, 20.00, 100.00, 280.00, 2280.00),
(2, 1, 3, 50.00, 40.00, 0.00, 2000.00),
(3, 2, 1, 100.00, 45.00, 630.00, 5130.00),
(4, 2, 4, 60.00, 60.00, 504.00, 4104.00)
ON DUPLICATE KEY UPDATE `total_price` = VALUES(`total_price`);

-- 6. مرتجع مبيعات تجريبي (Sales Returns)
INSERT INTO `sales_returns` (
    `id`, `return_number`, `sale_id`, `customer_id`, `store_id`,
    `return_date`, `subtotal`, `tax_amount`, `total_refund`, `refund_method`, `reason`, `created_by`
) VALUES
(1, 'SRET-20261113-0001', 1, 1, 2, '2026-11-13', 90.00, 12.60, 102.60, 'cash', 'عبوة مكررة تم شراؤها بالخطأ وحالتها سليمة', 1)
ON DUPLICATE KEY UPDATE `return_number` = VALUES(`return_number`);

INSERT INTO `sales_return_items` (`id`, `return_id`, `item_id`, `quantity`, `unit_price`, `tax_amount`, `total_refund`, `condition_state`) VALUES
(1, 1, 1, 2.00, 45.00, 12.60, 102.60, 'good')
ON DUPLICATE KEY UPDATE `total_refund` = VALUES(`total_refund`);

-- 7. مرتجع مشتريات تجريبي للمورد (Purchase Returns)
INSERT INTO `purchase_returns` (
    `id`, `return_number`, `purchase_id`, `supplier_id`, `store_id`,
    `return_date`, `subtotal`, `tax_amount`, `total_amount`, `refund_status`, `reason`, `created_by`
) VALUES
(1, 'PRET-20261112-0001', 1, 1, 1, '2026-11-12', 680.00, 95.20, 775.20, 'credited_to_balance', 'تلف في 8 عبوات زيت أثناء فحص الاستلام', 1)
ON DUPLICATE KEY UPDATE `return_number` = VALUES(`return_number`);

INSERT INTO `purchase_return_items` (`id`, `return_id`, `item_id`, `quantity`, `unit_cost`, `tax_amount`, `total_cost`, `batch_number`) VALUES
(1, 1, 2, 8.00, 85.00, 95.20, 775.20, 'BATCH-SLITE-2026A')
ON DUPLICATE KEY UPDATE `total_cost` = VALUES(`total_cost`);
