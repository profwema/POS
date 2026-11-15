-- ============================================================================
-- WAM Tech POS & ERP - Phase 4: General Accounting & Financial Ledger Migration
-- Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
-- Database: wamtech_pos_db
-- Engine: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. جدول شجرة ودليل الحسابات المالي (Chart of Accounts)
CREATE TABLE IF NOT EXISTS `chart_of_accounts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `account_code` VARCHAR(50) UNIQUE NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `parent_id` INT NULL,
    `type` ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
    `nature` ENUM('debit', 'credit') NOT NULL,
    `level` INT NOT NULL DEFAULT 1,
    `is_parent` TINYINT(1) NOT NULL DEFAULT 0,
    `opening_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `current_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `description` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_coa_parent` FOREIGN KEY (`parent_id`) REFERENCES `chart_of_accounts`(`id`) ON DELETE RESTRICT,
    INDEX `idx_coa_parent` (`parent_id`),
    INDEX `idx_coa_type` (`type`),
    INDEX `idx_coa_code` (`account_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. جدول قيود اليومية العامة المزدوجة (Journal Entries - Double-Entry Ledger)
CREATE TABLE IF NOT EXISTS `journal_entries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `entry_number` VARCHAR(50) UNIQUE NOT NULL,
    `entry_date` DATE NOT NULL,
    `doc_reference` VARCHAR(100) NULL,
    `description` TEXT NOT NULL,
    `total_debit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_credit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `is_balanced` TINYINT(1) NOT NULL DEFAULT 1,
    `is_posted` TINYINT(1) NOT NULL DEFAULT 1,
    `source_type` ENUM('manual', 'pos_sale', 'purchase', 'sale_return', 'purchase_return', 'payment_voucher', 'receipt_voucher') NOT NULL DEFAULT 'manual',
    `source_id` INT NULL,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_je_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
    INDEX `idx_je_date` (`entry_date`),
    INDEX `idx_je_posted` (`is_posted`),
    INDEX `idx_je_source` (`source_type`, `source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. جدول أطراف قيود اليومية (Journal Entry Lines / Items)
CREATE TABLE IF NOT EXISTS `journal_entry_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `entry_id` INT NOT NULL,
    `account_id` INT NOT NULL,
    `debit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `credit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `description` VARCHAR(255) NULL,
    CONSTRAINT `fk_jei_entry` FOREIGN KEY (`entry_id`) REFERENCES `journal_entries`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_jei_account` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts`(`id`) ON DELETE RESTRICT,
    INDEX `idx_jei_acc` (`account_id`),
    INDEX `idx_jei_entry` (`entry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. جدول سندات القبض والصرف المالي (Financial Vouchers)
CREATE TABLE IF NOT EXISTS `financial_vouchers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `voucher_number` VARCHAR(50) UNIQUE NOT NULL,
    `voucher_type` ENUM('receipt', 'payment') NOT NULL,
    `voucher_date` DATE NOT NULL,
    `account_id` INT NOT NULL,
    `target_account_id` INT NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `payment_method` ENUM('cash', 'card', 'bank_transfer', 'instapay', 'check') NOT NULL DEFAULT 'cash',
    `received_from_or_paid_to` VARCHAR(150) NOT NULL,
    `journal_entry_id` INT NULL,
    `notes` TEXT NULL,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_fv_acc` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_fv_target` FOREIGN KEY (`target_account_id`) REFERENCES `chart_of_accounts`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_fv_je` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_fv_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
    INDEX `idx_fv_type` (`voucher_type`),
    INDEX `idx_fv_date` (`voucher_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. تحديث وتأكيد صلاحيات موديول الحسابات
INSERT INTO `permissions` (`module`, `slug`, `name`, `description`) VALUES
('accounting', 'accounting.view', 'عرض الحسابات واليومية', 'استعراض القيود والحسابات العامة'),
('accounting', 'accounting.chart', 'إدارة شجرة الحسابات', 'إضافة وتعديل حسابات الدليل الشجري'),
('accounting', 'accounting.journal', 'إضافة قيود يومية يدوية', 'تسجيل قيود محاسبية مزدوجة متزنة'),
('accounting', 'accounting.vouchers', 'إصدار سندات القبض والصرف', 'تحرير إيصالات استلام وسداد مالي'),
('accounting', 'accounting.reports', 'القوائم الختامية والميزان', 'استخراج ميزان المراجعة وقائمة الدخل')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ربط الصلاحيات بالمدير العام
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, `id` FROM `permissions` WHERE `module` = 'accounting'
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

SET FOREIGN_KEY_CHECKS = 1;
