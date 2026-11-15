-- ============================================================================
-- WAM Tech POS & ERP - Phase 3: Supply Chain, Purchases & Returns Migration
-- Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
-- Database: wamtech_pos_db
-- Engine: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. جدول الموردين (Suppliers)
CREATE TABLE IF NOT EXISTS `suppliers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `company_name` VARCHAR(150) NULL,
    `contact_person` VARCHAR(100) NULL,
    `phone` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NULL,
    `address` VARCHAR(255) NULL,
    `tax_number` VARCHAR(50) NULL,
    `commercial_registry` VARCHAR(50) NULL,
    `opening_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `current_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `credit_limit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `payment_terms_days` INT NOT NULL DEFAULT 30,
    `notes` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sup_phone` (`phone`),
    INDEX `idx_sup_name` (`name`),
    INDEX `idx_sup_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. جدول فواتير المشتريات الرئيسي (Purchase Invoices)
CREATE TABLE IF NOT EXISTS `purchases` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_number` VARCHAR(50) UNIQUE NOT NULL,
    `supplier_invoice_number` VARCHAR(50) NULL,
    `supplier_id` INT NOT NULL,
    `store_id` INT NOT NULL,
    `branch_id` INT NOT NULL DEFAULT 1,
    `invoice_date` DATE NOT NULL,
    `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `discount_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `grand_total` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `paid_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `remaining_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `payment_status` ENUM('paid', 'partial', 'unpaid') NOT NULL DEFAULT 'unpaid',
    `payment_method` ENUM('cash', 'card', 'bank_transfer', 'credit') NOT NULL DEFAULT 'credit',
    `notes` TEXT NULL,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_pur_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_pur_store` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_pur_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
    INDEX `idx_pur_date` (`invoice_date`),
    INDEX `idx_pur_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. جدول بنود فاتورة المشتريات (Purchase Items)
CREATE TABLE IF NOT EXISTS `purchase_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `purchase_id` INT NOT NULL,
    `item_id` INT NOT NULL,
    `quantity` DECIMAL(12,2) NOT NULL,
    `unit_cost` DECIMAL(15,2) NOT NULL,
    `tax_percent` DECIMAL(5,2) NOT NULL DEFAULT 14.00,
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_cost` DECIMAL(15,2) NOT NULL,
    `batch_number` VARCHAR(100) NULL,
    `expire_date` DATE NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_pi_purchase` FOREIGN KEY (`purchase_id`) REFERENCES `purchases`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pi_item` FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE RESTRICT,
    INDEX `idx_pi_item` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. جدول عروض أسعار العملاء (Quotations)
CREATE TABLE IF NOT EXISTS `quotations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `quotation_number` VARCHAR(50) UNIQUE NOT NULL,
    `customer_id` INT NOT NULL DEFAULT 1,
    `store_id` INT NOT NULL DEFAULT 2,
    `branch_id` INT NOT NULL DEFAULT 1,
    `quotation_date` DATE NOT NULL,
    `expiry_date` DATE NULL,
    `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `discount_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `grand_total` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('draft', 'sent', 'accepted', 'rejected', 'converted') NOT NULL DEFAULT 'draft',
    `converted_sale_id` INT NULL,
    `converted_at` DATETIME NULL,
    `notes` TEXT NULL,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_quo_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_quo_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
    INDEX `idx_quo_status` (`status`),
    INDEX `idx_quo_date` (`quotation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. جدول بنود عرض السعر (Quotation Items)
CREATE TABLE IF NOT EXISTS `quotation_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `quotation_id` INT NOT NULL,
    `item_id` INT NOT NULL,
    `quantity` DECIMAL(12,2) NOT NULL,
    `unit_price` DECIMAL(15,2) NOT NULL,
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_price` DECIMAL(15,2) NOT NULL,
    `notes` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_qi_quotation` FOREIGN KEY (`quotation_id`) REFERENCES `quotations`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_qi_item` FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. جدول مرتجعات المبيعات (Sales Returns)
CREATE TABLE IF NOT EXISTS `sales_returns` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `return_number` VARCHAR(50) UNIQUE NOT NULL,
    `sale_id` INT NULL,
    `customer_id` INT NOT NULL DEFAULT 1,
    `store_id` INT NOT NULL DEFAULT 2,
    `return_date` DATE NOT NULL,
    `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_refund` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `refund_method` ENUM('cash', 'card', 'instapay', 'credit_balance') NOT NULL DEFAULT 'cash',
    `reason` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sr_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_sr_store` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_sr_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
    INDEX `idx_sr_date` (`return_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. جدول بنود مرتجع المبيعات (Sales Return Items)
CREATE TABLE IF NOT EXISTS `sales_return_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `return_id` INT NOT NULL,
    `item_id` INT NOT NULL,
    `quantity` DECIMAL(12,2) NOT NULL,
    `unit_price` DECIMAL(15,2) NOT NULL,
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_refund` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `condition_state` ENUM('good', 'damaged') NOT NULL DEFAULT 'good',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sri_return` FOREIGN KEY (`return_id`) REFERENCES `sales_returns`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sri_item` FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. جدول مرتجعات المشتريات للموردين (Purchase Returns)
CREATE TABLE IF NOT EXISTS `purchase_returns` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `return_number` VARCHAR(50) UNIQUE NOT NULL,
    `purchase_id` INT NULL,
    `supplier_id` INT NOT NULL,
    `store_id` INT NOT NULL,
    `return_date` DATE NOT NULL,
    `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `refund_status` ENUM('credited_to_balance', 'refunded_cash') NOT NULL DEFAULT 'credited_to_balance',
    `reason` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_pr_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_pr_store` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_pr_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
    INDEX `idx_pr_date` (`return_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. جدول بنود مرتجع المشتريات (Purchase Return Items)
CREATE TABLE IF NOT EXISTS `purchase_return_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `return_id` INT NOT NULL,
    `item_id` INT NOT NULL,
    `quantity` DECIMAL(12,2) NOT NULL,
    `unit_cost` DECIMAL(15,2) NOT NULL,
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_cost` DECIMAL(15,2) NOT NULL,
    `batch_number` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_pri_return` FOREIGN KEY (`return_id`) REFERENCES `purchase_returns`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pri_item` FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. تحديث جدول الصلاحيات ليشمل الموردين والمردودات وعروض الأسعار
INSERT INTO `permissions` (`module`, `slug`, `name`, `description`) VALUES
('suppliers', 'suppliers.view', 'عرض قائمة الموردين', 'استعراض بيانات الموردين وأرصدتهم'),
('suppliers', 'suppliers.manage', 'إضافة وتعديل الموردين', 'تسجيل مورد جديد وتعديل بيانات الاتصال'),
('suppliers', 'suppliers.statement', 'كشف حساب وأعمار ديون المورد', 'الاطلاع على الحركات المالية وأعمار المديونيات')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ربط الصلاحيات الجديدة بالمدير العام
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, `id` FROM `permissions` WHERE `module` IN ('suppliers', 'purchases', 'sales')
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

SET FOREIGN_KEY_CHECKS = 1;
