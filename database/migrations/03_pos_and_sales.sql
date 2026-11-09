-- ============================================================================
-- WAM Tech POS & ERP - Phase 2: POS Engine, Shifts & Sales Migration
-- Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
-- Database: wamtech_pos_db
-- Engine: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. جدول العملاء (Customers)
CREATE TABLE IF NOT EXISTS `customers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(50) NULL,
    `email` VARCHAR(100) NULL,
    `address` VARCHAR(255) NULL,
    `tax_number` VARCHAR(50) NULL,
    `credit_limit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `current_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `points` INT NOT NULL DEFAULT 0,
    `notes` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_cust_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. جدول الورديات والعُهد النقدية (Cashier Shifts)
CREATE TABLE IF NOT EXISTS `shifts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL DEFAULT 1,
    `store_id` INT NOT NULL DEFAULT 2,
    `user_id` INT NOT NULL,
    `shift_number` VARCHAR(50) UNIQUE NOT NULL,
    `opening_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `closing_balance` DECIMAL(15,2) NULL,
    `actual_cash` DECIMAL(15,2) NULL,
    `difference` DECIMAL(15,2) NULL,
    `total_sales_cash` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_sales_card` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_sales_instapay` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_sales_wallet` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_sales_credit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `sales_count` INT NOT NULL DEFAULT 0,
    `status` ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    `notes` TEXT NULL,
    `opened_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `closed_at` TIMESTAMP NULL,
    CONSTRAINT `fk_shift_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_shift_store` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`) ON DELETE RESTRICT,
    INDEX `idx_shift_status` (`status`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. جدول فواتير المبيعات الرئيسي (Sales Invoices)
CREATE TABLE IF NOT EXISTS `sales` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL DEFAULT 1,
    `store_id` INT NOT NULL DEFAULT 2,
    `shift_id` INT NULL,
    `customer_id` INT NOT NULL DEFAULT 1,
    `user_id` INT NOT NULL,
    `invoice_number` VARCHAR(50) UNIQUE NOT NULL,
    `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `discount_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `grand_total` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `paid_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `remaining_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `payment_method` ENUM('cash', 'card', 'instapay', 'wallet', 'credit', 'mixed') NOT NULL DEFAULT 'cash',
    `status` ENUM('completed', 'held', 'cancelled', 'returned') NOT NULL DEFAULT 'completed',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sale_shift` FOREIGN KEY (`shift_id`) REFERENCES `shifts`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_sale_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_sale_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_sale_store` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`) ON DELETE RESTRICT,
    INDEX `idx_sale_inv` (`invoice_number`),
    INDEX `idx_sale_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. جدول بنود فواتير المبيعات (Sale Items)
CREATE TABLE IF NOT EXISTS `sale_items` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `sale_id` INT NOT NULL,
    `item_id` INT NOT NULL,
    `batch_id` INT NULL,
    `unit_price` DECIMAL(15,2) NOT NULL,
    `cost_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `quantity` DECIMAL(12,3) NOT NULL DEFAULT 1.000,
    `tax_rate` DECIMAL(5,2) NOT NULL DEFAULT 14.00,
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `discount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_price` DECIMAL(15,2) NOT NULL,
    `is_scale` TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT `fk_si_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_si_item` FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_si_batch` FOREIGN KEY (`batch_id`) REFERENCES `item_batches`(`id`) ON DELETE SET NULL,
    INDEX `idx_si_sale` (`sale_id`),
    INDEX `idx_si_item` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. جدول سجلات سداد الفواتير (Sale Payments - للدفع المتعدد والإلكتروني)
CREATE TABLE IF NOT EXISTS `sale_payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sale_id` INT NOT NULL,
    `method` ENUM('cash', 'card', 'instapay', 'wallet', 'credit') NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `reference_number` VARCHAR(100) NULL,
    `notes` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sp_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`) ON DELETE CASCADE,
    INDEX `idx_sp_sale` (`sale_id`),
    INDEX `idx_sp_method` (`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. جدول الفواتير والسلات المعلقة للكاشير (Held Carts)
CREATE TABLE IF NOT EXISTS `held_carts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `shift_id` INT NULL,
    `hold_reference` VARCHAR(50) NOT NULL,
    `customer_id` INT NULL,
    `cart_data` JSON NOT NULL,
    `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_hc_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. جدول متسلسلات أرقام الفواتير للقفل المتزامن (Invoice Sequences)
CREATE TABLE IF NOT EXISTS `invoice_sequences` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `prefix` VARCHAR(20) NOT NULL,
    `date_key` VARCHAR(8) NOT NULL,
    `last_number` INT NOT NULL DEFAULT 0,
    UNIQUE KEY `uk_seq` (`prefix`, `date_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

