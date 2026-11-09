-- ============================================================================
-- WAM Tech POS & ERP - Phase 1: Inventory & Products Migration
-- Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
-- Database: wamtech_pos_db
-- Engine: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. جدول التصنيفات والأقسام (Categories)
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) UNIQUE NULL,
    `name` VARCHAR(150) NOT NULL,
    `description` VARCHAR(255) NULL,
    `icon` VARCHAR(50) DEFAULT 'fas fa-tag',
    `parent_id` INT NULL DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_cat_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. جدول وحدات القياس (Units of Measure)
CREATE TABLE IF NOT EXISTS `units` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `symbol` VARCHAR(20) NOT NULL,
    `is_base` TINYINT(1) DEFAULT 1,
    `conversion_rate` DECIMAL(12,4) DEFAULT 1.0000,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. جدول المخازن والمستودعات (Stores / Warehouses)
CREATE TABLE IF NOT EXISTS `stores` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL DEFAULT 1,
    `code` VARCHAR(50) UNIQUE NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `location` VARCHAR(255) NULL,
    `manager_name` VARCHAR(150) NULL,
    `phone` VARCHAR(50) NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_stores_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. جدول الأصناف والمنتجات الرئيسي (Items / Products)
CREATE TABLE IF NOT EXISTS `items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NULL,
    `unit_id` INT NOT NULL,
    `barcode` VARCHAR(100) UNIQUE NOT NULL,
    `sku` VARCHAR(100) NULL,
    `name` VARCHAR(255) NOT NULL,
    `cost_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `retail_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `wholesale_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `min_wholesale_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `min_sale_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `tax_rate` DECIMAL(5,2) NOT NULL DEFAULT 14.00,
    `reorder_level` DECIMAL(12,2) NOT NULL DEFAULT 5.00,
    `is_scale` TINYINT(1) NOT NULL DEFAULT 0,
    `scale_plu` VARCHAR(20) NULL,
    `has_expiry` TINYINT(1) NOT NULL DEFAULT 0,
    `image_path` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_items_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_items_unit` FOREIGN KEY (`unit_id`) REFERENCES `units`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. جدول تشغيلات وتواريخ الصلاحية (Item Batches / Lots)
CREATE TABLE IF NOT EXISTS `item_batches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `item_id` INT NOT NULL,
    `store_id` INT NOT NULL,
    `batch_number` VARCHAR(100) NOT NULL,
    `expiry_date` DATE NOT NULL,
    `quantity` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `cost_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_batch_item` FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_batch_store` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`) ON DELETE RESTRICT,
    INDEX `idx_batch_exp` (`expiry_date`),
    INDEX `idx_batch_item_store` (`item_id`, `store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. جدول حركات المخزون الموحدة والمؤرخة (Stock Moves - Single Source of Truth)
CREATE TABLE IF NOT EXISTS `stock_moves` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `item_id` INT NOT NULL,
    `store_id` INT NOT NULL,
    `batch_id` INT NULL,
    `move_type` ENUM(
        'opening',
        'purchase',
        'sale',
        'purchase_return',
        'sale_return',
        'transfer_in',
        'transfer_out',
        'inventory_adjustment',
        'damaged'
    ) NOT NULL,
    `reference_type` VARCHAR(50) NULL,
    `reference_id` INT NULL,
    `quantity` DECIMAL(12,2) NOT NULL,
    `unit_cost` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `balance_after` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `notes` TEXT NULL,
    `created_by` INT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sm_item` FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sm_store` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_sm_batch` FOREIGN KEY (`batch_id`) REFERENCES `item_batches`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_sm_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_sm_item_store` (`item_id`, `store_id`),
    INDEX `idx_sm_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. جدول أذونات تحويل المخزون بين المستودعات (Store Transfers)
CREATE TABLE IF NOT EXISTS `store_transfers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `transfer_number` VARCHAR(50) UNIQUE NOT NULL,
    `from_store_id` INT NOT NULL,
    `to_store_id` INT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved',
    `total_items` INT NOT NULL DEFAULT 0,
    `notes` TEXT NULL,
    `created_by` INT NOT NULL,
    `approved_by` INT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `approved_at` TIMESTAMP NULL,
    CONSTRAINT `fk_tr_from` FOREIGN KEY (`from_store_id`) REFERENCES `stores`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_tr_to` FOREIGN KEY (`to_store_id`) REFERENCES `stores`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_tr_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_tr_approver` FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. بنود أذونات تحويل المخزون (Store Transfer Items)
CREATE TABLE IF NOT EXISTS `store_transfer_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `transfer_id` INT NOT NULL,
    `item_id` INT NOT NULL,
    `batch_id` INT NULL,
    `quantity` DECIMAL(12,2) NOT NULL,
    `unit_cost` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `notes` VARCHAR(255) NULL,
    CONSTRAINT `fk_tri_transfer` FOREIGN KEY (`transfer_id`) REFERENCES `store_transfers`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tri_item` FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_tri_batch` FOREIGN KEY (`batch_id`) REFERENCES `item_batches`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

