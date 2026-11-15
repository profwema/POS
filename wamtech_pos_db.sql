-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 09, 2026 at 10:55 PM
-- Server version: 5.7.44
-- PHP Version: 8.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wamtech_pos_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_action` (`action`),
  KEY `idx_audit_module` (`module`),
  KEY `idx_audit_date` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'auth', 1, NULL, '{\"ip\": \"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-09 10:51:40'),
(2, NULL, 'sale_created', 'sales', 2, NULL, '{\"desc\": \"ط¥طµط¯ط§ط± ظپط§طھظˆط±ط© ظ…ط¨ظٹط¹ط§طھ INV-20261109-0002 ط¨ظ‚ظٹظ…ط© 423.20 ط¬.ظ…\", \"total\": 423.2, \"invoice\": \"INV-20261109-0002\"}', '127.0.0.1', '', '2026-11-09 20:14:08'),
(3, NULL, 'sale_created', 'sales', 3, NULL, '{\"desc\": \"ط¥طµط¯ط§ط± ظپط§طھظˆط±ط© ظ…ط¨ظٹط¹ط§طھ INV-20261109-0003 ط¨ظ‚ظٹظ…ط© 171.00 ط¬.ظ…\", \"total\": 171, \"invoice\": \"INV-20261109-0003\"}', '127.0.0.1', '', '2026-11-09 20:14:08');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
CREATE TABLE IF NOT EXISTS `branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_branch_code` (`code`),
  KEY `idx_branch_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `code`, `name`, `phone`, `address`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'BR001', 'ط§ظ„ظپط±ط¹ ط§ظ„ط±ط¦ظٹط³ظٹ - ط§ظ„ظ…ط±ظƒط² ط§ظ„طھط¬ط§ط±ظٹ', '01002283766', 'ط§ظ„ظ‚ط§ظ‡ط±ط© - ط¬ظ…ظ‡ظˆط±ظٹط© ظ…طµط± ط§ظ„ط¹ط±ط¨ظٹط©', 1, '2026-11-09 00:44:50', '2026-11-09 00:44:50');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'fas fa-tag',
  `parent_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_cat_parent` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `code`, `name`, `description`, `icon`, `parent_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'CAT-FOOD', 'ط£ط؛ط°ظٹط© ظˆظ…ط´ط±ظˆط¨ط§طھ', 'ط§ظ„ظ…ط´ط±ظˆط¨ط§طھ ط§ظ„ط؛ط§ط²ظٹط© ظˆط§ظ„ط¹طµط§ط¦ط± ظˆط§ظ„ظ…ظٹط§ظ‡ ط§ظ„ظ…ط¹ط¯ظ†ظٹط©', 'fas fa-wine-bottle', NULL, 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(2, 'CAT-GROCERY', 'ط¨ظ‚ط§ظ„ط© طھظ…ظˆظٹظ†ظٹط© ظˆط¬ط§ظپط©', 'ط§ظ„ط£ط±ط²طŒ ط§ظ„ط²ظٹظˆطھطŒ ط§ظ„ط³ظƒط±طŒ ط§ظ„ط¨ظ‚ظˆظ„ظٹط§طھ ظˆط§ظ„طھظˆط§ط¨ظ„', 'fas fa-shopping-basket', NULL, 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(3, 'CAT-DAIRY', 'ط£ظ„ط¨ط§ظ† ظˆط£ط¬ط¨ط§ظ† ط·ط§ط²ط¬ط©', 'ط§ظ„ط£ظ„ط¨ط§ظ†طŒ ط§ظ„ط£ط¬ط¨ط§ظ†طŒ ط§ظ„ط²ط¨ط¯ط© ظˆط§ظ„ظ‚ط´ط·ط©', 'fas fa-cheese', NULL, 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(4, 'CAT-VEG', 'ط®ط¶ط§ط± ظˆظپظˆط§ظƒظ‡ (ظ…ظٹط²ط§ظ†)', 'ط®ط¶ط§ط± ظˆظپظˆط§ظƒظ‡ ط·ط§ط²ط¬ط© طھط¨ط§ط¹ ط¨ط§ظ„ظ…ظٹط²ط§ظ† ط§ظ„ط¥ظ„ظƒطھط±ظˆظ†ظٹ', 'fas fa-apple-alt', NULL, 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(5, 'CAT-CLEAN', 'ظ…ظ†ط¸ظپط§طھ ظˆط¹ظ†ط§ظٹط© ظ…ظ†ط²ظ„ظٹط©', 'ظ…ط³ط§ط­ظٹظ‚ ط§ظ„ط؛ط³ظٹظ„طŒ ط§ظ„ظ…ط·ظ‡ط±ط§طھ ظˆظ…ط³طھظ„ط²ظ…ط§طھ ط§ظ„ظ†ط¸ط§ظپط©', 'fas fa-pump-soap', NULL, 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(6, 'CAT-ELEC', 'ط¥ظ„ظƒطھط±ظˆظ†ظٹط§طھ ظˆط§ظƒط³ط³ظˆط§ط±ط§طھ', 'ظƒط§ط¨ظ„ط§طھطŒ ط´ظˆط§ط­ظ†طŒ ط¨ط·ط§ط±ظٹط§طھ ظˆط§ظƒط³ط³ظˆط§ط±ط§طھ ط¥ظ„ظƒطھط±ظˆظ†ظٹط©', 'fas fa-plug', NULL, 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `points` int(11) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cust_phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `tax_number`, `credit_limit`, `current_balance`, `points`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ط¹ظ…ظٹظ„ ظ†ظ‚ط¯ظٹ ط¹ط§ظ… (Walk-in Customer)', '01000000000', 'cash@pos.local', 'ظ†ظ‚ط·ط© ط§ظ„ط¨ظٹط¹ - ط§ظ„ظپط±ط¹ 1', NULL, 0.00, 0.00, 0, NULL, 1, '2026-11-09 20:03:20', '2026-11-09 20:03:20'),
(2, 'ظ…/ ط­ط³ط§ظ… ظ…طµط·ظپظ‰ (ط´ط±ظƒط© ط§ظ„ظ†ظˆط± ظ„ظ„طھط¬ط§ط±ط©)', '01123456789', 'hossam@alnoor.com', 'ط§ظ„ظ‚ط§ظ‡ط±ط© - ظ…طµط± ط§ظ„ط¬ط¯ظٹط¯ط©', NULL, 15000.00, 0.00, 28, NULL, 1, '2026-11-09 20:03:20', '2026-11-09 20:14:08'),
(3, 'ط¯/ ط£ط­ظ…ط¯ ط¹ط§ط¯ظ„ ط§ظ„ط±ظپط§ط¹ظٹ', '01234567890', 'ahmed.refaie@gmail.com', 'ط§ظ„ظ‚ط§ظ‡ط±ط© - ظ…ط¯ظٹظ†ط© ظ†طµط±', NULL, 5000.00, 0.00, 10, NULL, 1, '2026-11-09 20:03:20', '2026-11-09 20:03:20');

-- --------------------------------------------------------

--
-- Table structure for table `held_carts`
--

DROP TABLE IF EXISTS `held_carts`;
CREATE TABLE IF NOT EXISTS `held_carts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `hold_reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `cart_data` json NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_hc_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_sequences`
--

DROP TABLE IF EXISTS `invoice_sequences`;
CREATE TABLE IF NOT EXISTS `invoice_sequences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prefix` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_key` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_number` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_seq` (`prefix`,`date_key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_sequences`
--

INSERT INTO `invoice_sequences` (`id`, `prefix`, `date_key`, `last_number`) VALUES
(1, 'INV', '20261109', 3);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `unit_id` int(11) NOT NULL,
  `barcode` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `retail_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `wholesale_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `min_wholesale_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `min_sale_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '14.00',
  `reorder_level` decimal(12,2) NOT NULL DEFAULT '5.00',
  `is_scale` tinyint(1) NOT NULL DEFAULT '0',
  `scale_plu` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_expiry` tinyint(1) NOT NULL DEFAULT '0',
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`),
  KEY `fk_items_category` (`category_id`),
  KEY `fk_items_unit` (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `category_id`, `unit_id`, `barcode`, `sku`, `name`, `cost_price`, `retail_price`, `wholesale_price`, `min_wholesale_price`, `min_sale_price`, `tax_rate`, `reorder_level`, `is_scale`, `scale_plu`, `has_expiry`, `image_path`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 5, '6221155010015', 'JOH-MILK-1L', 'ظ„ط¨ظ† ط¬ظ‡ظٹظ†ط© ظƒط§ظ…ظ„ ط§ظ„ط¯ط³ظ… 1 ظ„طھط±', 38.00, 45.00, 42.00, 41.00, 40.00, 14.00, 15.00, 0, NULL, 1, NULL, 'طµظ†ظپ ط؛ط°ط§ط¦ظٹ ظٹظˆظ…ظٹ ط³ط±ظٹط¹ ط§ظ„ط¯ظˆط±ط§ظ†', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(2, 2, 1, '6223000540021', 'SLITE-OIL-1.5', 'ط²ظٹطھ ط¹ط¨ط§ط¯ ط§ظ„ط´ظ…ط³ ط³ظ„ط§ظٹطھ 1.5 ظ„طھط±', 85.00, 100.00, 94.00, 92.00, 90.00, 14.00, 10.00, 0, NULL, 0, NULL, 'ط²ظٹطھ ط·ط¹ط§ظ… ظ†ظ‚ظٹ', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(3, 2, 1, '6221234560018', 'DOHA-RICE-1KG', 'ط£ط±ط² ظ…طµط±ظٹ ظپط§ط®ط± ط§ظ„ط¶ط­ظ‰ 1 ظƒط¬ظ…', 32.00, 40.00, 36.00, 35.00, 34.00, 0.00, 20.00, 0, NULL, 0, NULL, 'ط£ط±ط² ظ…ظ†ظ‚ظ‰ ظˆظ…ط¹ط¨ط£ ط¢ظ„ظٹط§ظ‹ - ظ…ط¹ظپظ‰ ظ…ظ† ط§ظ„ط¶ط±ظٹط¨ط©', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(4, 2, 2, '6221008000452', 'AROSA-TEA-250', 'ط´ط§ظٹ ط§ظ„ط¹ط±ظˆط³ط© ط£ط³ظˆط¯ ظ†ط§ط¹ظ… 250 ط¬ظ…', 50.00, 60.00, 56.00, 54.00, 52.00, 14.00, 12.00, 0, NULL, 0, NULL, 'ط´ط§ظٹ ط£ط³ظˆط¯ ظ…ظ…طھط§ط²', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(5, 1, 1, '5449000000996', 'COCA-CAN-330', 'ظƒظˆظƒط§ظƒظˆظ„ط§ ظ…ط´ط±ظˆط¨ ط؛ط§ط²ظٹ ظƒط§ظ†ط² 330 ظ…ظ„', 11.50, 15.00, 13.50, 13.00, 12.50, 14.00, 24.00, 0, NULL, 1, NULL, 'ظ…ط´ط±ظˆط¨ ط؛ط§ط²ظٹ ظ…ظ†ط¹ط´', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(6, 5, 1, '8001090123456', 'ARIEL-AUTO-2.5', 'ظ…ط³ط­ظˆظ‚ ط؛ط³ظٹظ„ ط£ظˆطھظˆظ…ط§طھظٹظƒ ط£ط±ظٹط§ظ„ ظ„ط§ظپظ†ط¯ط± 2.5 ظƒط¬ظ…', 175.00, 210.00, 195.00, 190.00, 185.00, 14.00, 8.00, 0, NULL, 0, NULL, 'ظ…ط³ط­ظˆظ‚ ظ…ظ†ط¸ظپ ظ„ظ„ظ…ظ„ط§ط¨ط³', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(7, 3, 4, '200000100000', 'CHEESE-ROOMY-KG', 'ط¬ط¨ظ†ط© ط±ظˆظ…ظٹ ظ‚ط¯ظٹظ…ط© ظپط§ط®ط±ط© (ظ…ظٹط²ط§ظ†/ظƒط¬ظ…)', 300.00, 360.00, 335.00, 330.00, 320.00, 14.00, 10.00, 1, '00001', 1, NULL, 'طµظ†ظپ ظ…ظٹط²ط§ظ† ط¨ط§ط±ظƒظˆط¯ ظˆط²ظ†ظٹ ظٹط¨ط¯ط£ ط¨ظ€ 20 ط£ظˆ 99', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(8, 4, 4, '200000200000', 'APPLE-RED-KG', 'طھظپط§ط­ ط£ط­ظ…ط± ط³ظƒط±ظٹ ط¥ظٹط·ط§ظ„ظٹ ط·ط§ط²ط¬ (ظ…ظٹط²ط§ظ†/ظƒط¬ظ…)', 70.00, 90.00, 82.00, 80.00, 75.00, 0.00, 15.00, 1, '00002', 0, NULL, 'ظپظˆط§ظƒظ‡ ط·ط§ط²ط¬ط© - طµظ†ظپ ظ…ظٹط²ط§ظ† ط¨ط§ط±ظƒظˆط¯ ظˆط²ظ†ظٹ', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(9, 3, 4, '200000300000', 'CHEESE-BRAMILY-KG', 'ط¬ط¨ظ†ط© ط¨ط±ط§ظ…ظٹظ„ظٹ ط¨ط§ظ„ظپظ„ظپظ„ ط§ظ„ط£ط®ط¶ط± (ظ…ظٹط²ط§ظ†/ظƒط¬ظ…)', 145.00, 180.00, 165.00, 160.00, 155.00, 14.00, 8.00, 1, '00003', 1, NULL, 'ط¬ط¨ظ† ط·ط¨ظٹط¹ظٹ ظ…ط¹طھظ‚', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(10, 6, 1, '6974123456789', 'ANK-CAB-TC', 'ظƒط§ط¨ظ„ ط´ط­ظ† ط³ط±ظٹط¹ ط§ظ†ظƒط± Anker Type-C 1m', 95.00, 150.00, 130.00, 125.00, 120.00, 14.00, 5.00, 0, NULL, 0, NULL, 'ط§ظƒط³ط³ظˆط§ط±ط§طھ ظˆط¶ظ…ط§ظ† ظ…ط¹طھظ…ط¯', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59');

-- --------------------------------------------------------

--
-- Table structure for table `item_batches`
--

DROP TABLE IF EXISTS `item_batches`;
CREATE TABLE IF NOT EXISTS `item_batches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `batch_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiry_date` date NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_batch_store` (`store_id`),
  KEY `idx_batch_exp` (`expiry_date`),
  KEY `idx_batch_item_store` (`item_id`,`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_batches`
--

INSERT INTO `item_batches` (`id`, `item_id`, `store_id`, `batch_number`, `expiry_date`, `quantity`, `cost_price`, `created_at`) VALUES
(1, 1, 1, 'BATCH-2026-MILK-A', '2026-12-24', 80.00, 38.00, '2026-11-09 00:50:59'),
(2, 1, 2, 'BATCH-2026-MILK-B', '2026-11-29', 20.00, 38.00, '2026-11-09 00:50:59'),
(3, 5, 2, 'BATCH-2026-COCA-01', '2027-05-08', 120.00, 11.50, '2026-11-09 00:50:59'),
(4, 7, 2, 'BATCH-2026-ROOMY-01', '2026-12-09', 25.50, 300.00, '2026-11-09 00:50:59');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_perm_module` (`module`),
  KEY `idx_perm_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `module`, `slug`, `name`, `description`, `created_at`) VALUES
(1, 'pos', 'pos.view', 'ظپطھط­ ط´ط§ط´ط© ط§ظ„ظƒط§ط´ظٹط±', 'ط¥ظ…ظƒط§ظ†ظٹط© ظپطھط­ ط´ط§ط´ط© ظ†ظ‚ط·ط© ط§ظ„ط¨ظٹط¹ ط§ظ„ط³ط±ظٹط¹ط©', '2026-11-09 00:44:50'),
(2, 'pos', 'pos.sell', 'ط¥طھظ…ط§ظ… ط¹ظ…ظ„ظٹط§طھ ط§ظ„ط¨ظٹط¹', 'ط¥طµط¯ط§ط± ط§ظ„ظپظˆط§طھظٹط± ظˆطھط­طµظٹظ„ ط§ظ„ظ…ط¨ط§ظ„ط؛', '2026-11-09 00:44:50'),
(3, 'pos', 'pos.discount', 'طھط·ط¨ظٹظ‚ ط®طµظ… ظٹط¯ظˆظٹ', 'طھط·ط¨ظٹظ‚ ط®طµظ… ط§ط³طھط«ظ†ط§ط¦ظٹ ط¹ظ„ظ‰ ط§ظ„ظپط§طھظˆط±ط©', '2026-11-09 00:44:50'),
(4, 'pos', 'pos.shift_close', 'ط¥ط؛ظ„ط§ظ‚ ط§ظ„ظˆط±ط¯ظٹط©', 'طھط³ظ„ظٹظ… ط§ظ„ظˆط±ط¯ظٹط© ظˆط­ط³ط§ط¨ ط§ظ„ط¹ط¬ط² ظˆط§ظ„ط²ظٹط§ط¯ط©', '2026-11-09 00:44:50'),
(5, 'sales', 'sales.view', 'ط§ط³طھط¹ط±ط§ط¶ ظپظˆط§طھظٹط± ط§ظ„ظ…ط¨ظٹط¹ط§طھ', 'ط§ظ„ط§ط·ظ„ط§ط¹ ط¹ظ„ظ‰ ط¬ط¯ظˆظ„ ط§ظ„ظپظˆط§طھظٹط± ط§ظ„ط³ط§ط¨ظ‚ط©', '2026-11-09 00:44:50'),
(6, 'sales', 'sales.create', 'ط¥طµط¯ط§ط± ظپط§طھظˆط±ط© ط¨ظٹط¹ ظƒط¨ط±ظ‰', 'ط¥طµط¯ط§ط± ظپظˆط§طھظٹط± ط¨ظٹط¹ ط¢ط¬ظ„ط© ظˆظ†ظ‚ط¯ظٹط©', '2026-11-09 00:44:50'),
(7, 'sales', 'sales.return', 'ط¥طµط¯ط§ط± ظ…ط±طھط¬ط¹ ظ…ط¨ظٹط¹ط§طھ', 'ط§ط³طھط±ط¬ط§ط¹ ط¨ط¶ط§ط¹ط© ظ…ط¨ط§ط¹ط© ظˆطھط¹ط¯ظٹظ„ ط§ظ„ط±طµظٹط¯', '2026-11-09 00:44:50'),
(8, 'sales', 'sales.quotes', 'ط¥ط¯ط§ط±ط© ط¹ط±ظˆط¶ ط§ظ„ط£ط³ط¹ط§ط±', 'ط¥ظ†ط´ط§ط، ظˆطھط¹ط¯ظٹظ„ ظˆطھط­ظˆظٹظ„ ط¹ط±ظˆط¶ ط§ظ„ط£ط³ط¹ط§ط±', '2026-11-09 00:44:50'),
(9, 'purchases', 'purchases.view', 'ط§ط³طھط¹ط±ط§ط¶ ظپظˆط§طھظٹط± ط§ظ„ظ…ط´طھط±ظٹط§طھ', 'ط¹ط±ط¶ ظ…ط´طھط±ظٹط§طھ ط§ظ„ظ…ظˆط±ط¯ظٹظ† ط§ظ„ط³ط§ط¨ظ‚ط©', '2026-11-09 00:44:50'),
(10, 'purchases', 'purchases.create', 'ط¥ط¶ط§ظپط© ظپط§طھظˆط±ط© ظ…ط´طھط±ظٹط§طھ', 'طھط³ط¬ظٹظ„ ط¨ط¶ط§ط¦ط¹ ط¬ط¯ظٹط¯ط© ظپظٹ ط§ظ„ظ…ط®ط²ظ†', '2026-11-09 00:44:50'),
(11, 'purchases', 'purchases.return', 'ظ…ط±طھط¬ط¹ ظ…ط´طھط±ظٹط§طھ', 'ط¥ط±ط¬ط§ط¹ ط¨ط¶ط§ط¹ط© ظ„ظ„ظ…ظˆط±ط¯ظٹظ†', '2026-11-09 00:44:50'),
(12, 'inventory', 'inventory.view', 'ط¹ط±ط¶ ط§ظ„ظ…ط®ط²ظˆظ† ظˆط¨ط·ط§ظ‚ط© ط§ظ„طµظ†ظپ', 'ط§ظ„ط§ط·ظ„ط§ط¹ ط¹ظ„ظ‰ ظƒظ…ظٹط§طھ ط§ظ„ط£طµظ†ط§ظپ ظˆط§ظ„ط£ط³ط¹ط§ط±', '2026-11-09 00:44:50'),
(13, 'inventory', 'inventory.manage', 'ط¥ط¶ط§ظپط© ظˆطھط¹ط¯ظٹظ„ ط§ظ„ط£طµظ†ط§ظپ', 'ط¥ط¯ط®ط§ظ„ ط£طµظ†ط§ظپ ط¬ط¯ظٹط¯ط© ظˆطھط¹ط¯ظٹظ„ ط£ط³ط¹ط§ط±ظ‡ط§', '2026-11-09 00:44:50'),
(14, 'inventory', 'inventory.transfer', 'ط§ظ„طھط­ظˆظٹظ„ ط¨ظٹظ† ط§ظ„ظ…ط®ط§ط²ظ†', 'ظ†ظ‚ظ„ ط¨ط¶ط§ط¹ط© ط¨ظٹظ† ط§ظ„ظ…ط³طھظˆط¯ط¹ط§طھ', '2026-11-09 00:44:50'),
(15, 'inventory', 'inventory.stocktake', 'ط¬ط±ط¯ ط§ظ„ظ…ط®ط§ط²ظ† ظˆط§ظ„طھط³ظˆظٹط§طھ', 'ط¥ط¬ط±ط§ط، ط¬ط±ط¯ ظپط¹ظ„ظٹ ظˆطھط¹ط¯ظٹظ„ ط§ظ„ظپط±ظˆظ‚', '2026-11-09 00:44:50'),
(16, 'accounting', 'accounting.view', 'ط¹ط±ط¶ ط´ط¬ط±ط© ط§ظ„ط­ط³ط§ط¨ط§طھ ظˆط§ظ„ظٹظˆظ…ظٹط©', 'ط§ط³طھط¹ط±ط§ط¶ ط§ظ„ط­ط³ط§ط¨ط§طھ ظˆط§ظ„ظ‚ظٹظˆط¯', '2026-11-09 00:44:50'),
(17, 'accounting', 'accounting.journal', 'ط¥ط¶ط§ظپط© ظ‚ظٹظˆط¯ ظٹظˆظ…ظٹط©', 'طھط³ط¬ظٹظ„ ط§ظ„ظ‚ظٹظˆط¯ ط§ظ„ظ…ط²ط¯ظˆط¬ط© ط§ظ„ظ…طھط²ظ†ط©', '2026-11-09 00:44:50'),
(18, 'accounting', 'accounting.expenses', 'طھط³ط¬ظٹظ„ ط§ظ„ظ…طµط±ظˆظپط§طھ', 'ط¥ط«ط¨ط§طھ ط³ظ†ط¯ط§طھ ط§ظ„طµط±ظپ ظˆط§ظ„ظ…طµط±ظˆظپط§طھ ط§ظ„ظٹظˆظ…ظٹط©', '2026-11-09 00:44:50'),
(19, 'accounting', 'accounting.reports', 'ط§ظ„ظ‚ظˆط§ط¦ظ… ط§ظ„ط®طھط§ظ…ظٹط© ظˆط§ظ„ظ…ظٹط²ط§ظ†ظٹط©', 'ظ…ظٹط²ط§ظ† ط§ظ„ظ…ط±ط§ط¬ط¹ط© ظˆظ‚ط§ط¦ظ…ط© ط§ظ„ط¯ط®ظ„', '2026-11-09 00:44:50'),
(20, 'settings', 'settings.manage', 'ط¥ط¹ط¯ط§ط¯ط§طھ ط§ظ„ظ†ط¸ط§ظ… ط§ظ„ط¹ط§ظ…ط©', 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ظ†ط´ط£ط© ظˆط§ظ„ط¶ط±ظٹط¨ط©', '2026-11-09 00:44:50'),
(21, 'settings', 'settings.users', 'ط¥ط¯ط§ط±ط© ط§ظ„ظ…ظˆط¸ظپظٹظ† ظˆط§ظ„ظ…ط³طھط®ط¯ظ…ظٹظ†', 'ط¥ظ†ط´ط§ط، ظˆطھط¹ط¯ظٹظ„ ظ…ط³طھط®ط¯ظ…ظٹظ† ظˆطھط­ط¯ظٹط¯ ط§ظ„طµظ„ط§ط­ظٹط§طھ', '2026-11-09 00:44:50'),
(22, 'settings', 'settings.backup', 'ط§ظ„ظ†ط³ط® ط§ظ„ط§ط­طھظٹط§ط·ظٹ', 'طھظ†ط²ظٹظ„ ظˆظ…ط²ط§ظ…ظ†ط© ظ‚ط§ط¹ط¯ط© ط§ظ„ط¨ظٹط§ظ†ط§طھ', '2026-11-09 00:44:50');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_role_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'ظ…ط¯ظٹط± ط§ظ„ظ†ط¸ط§ظ… ط§ظ„ط¹ط§ظ…', 'admin', 'طµظ„ط§ط­ظٹط§طھ ظƒط§ظ…ظ„ط© ظˆط؛ظٹط± ظ…ظ‚ظٹط¯ط© ظ„ظƒط§ظپط© ط´ط§ط´ط§طھ ظˆظˆط¸ط§ط¦ظپ ط§ظ„ظ†ط¸ط§ظ…', '2026-11-09 00:44:50'),
(2, 'ظ…ط¯ظٹط± ط§ظ„ظپط±ط¹', 'manager', 'ط¥ط¯ط§ط±ط© ط§ظ„ط¹ظ…ظ„ظٹط§طھ ط§ظ„ظٹظˆظ…ظٹط© ظˆط§ظ„ظپظˆط§طھظٹط± ظˆط§ظ„طھظ‚ط§ط±ظٹط± ط¯ط§ط®ظ„ ط§ظ„ظپط±ط¹', '2026-11-09 00:44:50'),
(3, 'ط§ظ„ظ…ط­ط§ط³ط¨ ط§ظ„ظ…ط§ظ„ظٹ', 'accountant', 'ط¥ط¯ط§ط±ط© ط§ظ„ط­ط³ط§ط¨ط§طھطŒ ظ‚ظٹظˆط¯ ط§ظ„ظٹظˆظ…ظٹط©طŒ ظˆط³ظ†ط¯ط§طھ ط§ظ„طµط±ظپ ظˆط§ظ„ط¥ظٹط±ط§ط¯ ظˆط§ظ„ظ…طµط±ظˆظپط§طھ', '2026-11-09 00:44:50'),
(4, 'ظƒط§ط´ظٹط± ظ†ظ‚ط·ط© ط§ظ„ط¨ظٹط¹', 'cashier', 'ط´ط§ط´ط© ط§ظ„ط¨ظٹط¹ ط§ظ„ط³ط±ظٹط¹ط©طŒ ط§ظ„ظˆط±ط¯ظٹط§طھطŒ ظˆطھط³ظ„ظٹظ… ط§ظ„ظ†ظ‚ط¯ظٹط© ظپظ‚ط·', '2026-11-09 00:44:50'),
(5, 'ط£ظ…ظٹظ† ط§ظ„ظ…ط³طھظˆط¯ط¹', 'storekeeper', 'ط­ط±ظƒط© ط§ظ„ظ…ط®ط²ظˆظ†طŒ ط§ظ„ط§ط³طھظ„ط§ظ…طŒ ط£ط°ظˆظ† ط§ظ„طµط±ظپطŒ ظˆط§ظ„طھط­ظˆظٹظ„ ط¨ظٹظ† ط§ظ„ظ…ط®ط§ط²ظ†', '2026-11-09 00:44:50');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `fk_rp_permission` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(4, 1),
(1, 2),
(4, 2),
(1, 3),
(1, 4),
(4, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT '1',
  `store_id` int(11) NOT NULL DEFAULT '2',
  `shift_id` int(11) DEFAULT NULL,
  `customer_id` int(11) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL,
  `invoice_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remaining_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('cash','card','instapay','wallet','credit','mixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `status` enum('completed','held','cancelled','returned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `fk_sale_shift` (`shift_id`),
  KEY `fk_sale_customer` (`customer_id`),
  KEY `fk_sale_user` (`user_id`),
  KEY `fk_sale_store` (`store_id`),
  KEY `idx_sale_inv` (`invoice_number`),
  KEY `idx_sale_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `branch_id`, `store_id`, `shift_id`, `customer_id`, `user_id`, `invoice_number`, `subtotal`, `tax_amount`, `discount_amount`, `grand_total`, `paid_amount`, `remaining_amount`, `payment_method`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, 1, 1, 'INV-20261109-0001', 380.00, 53.20, 10.00, 423.20, 423.20, 0.00, 'cash', 'completed', '', '2026-11-09 20:13:15', '2026-11-09 20:13:15'),
(2, 1, 2, 1, 1, 1, 'INV-20261109-0002', 380.00, 53.20, 10.00, 423.20, 423.20, 0.00, 'cash', 'completed', '', '2026-11-09 20:14:07', '2026-11-09 20:14:07'),
(3, 1, 2, 1, 2, 1, 'INV-20261109-0003', 150.00, 21.00, 0.00, 171.00, 171.00, 0.00, 'mixed', 'completed', '', '2026-11-09 20:14:08', '2026-11-09 20:14:08');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
CREATE TABLE IF NOT EXISTS `sale_items` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `quantity` decimal(12,3) NOT NULL DEFAULT '1.000',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '14.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(15,2) NOT NULL,
  `is_scale` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_si_batch` (`batch_id`),
  KEY `idx_si_sale` (`sale_id`),
  KEY `idx_si_item` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `item_id`, `batch_id`, `unit_price`, `cost_price`, `quantity`, `tax_rate`, `tax_amount`, `discount`, `total_price`, `is_scale`) VALUES
(1, 1, 2, NULL, 100.00, 85.00, 2.000, 14.00, 28.00, 0.00, 228.00, 0),
(2, 1, 7, NULL, 360.00, 300.00, 0.500, 14.00, 25.20, 0.00, 205.20, 1),
(3, 2, 2, NULL, 100.00, 85.00, 2.000, 14.00, 28.00, 0.00, 228.00, 0),
(4, 2, 7, NULL, 360.00, 300.00, 0.500, 14.00, 25.20, 0.00, 205.20, 1),
(5, 3, 5, NULL, 15.00, 11.50, 10.000, 14.00, 21.00, 0.00, 171.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `sale_payments`
--

DROP TABLE IF EXISTS `sale_payments`;
CREATE TABLE IF NOT EXISTS `sale_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `method` enum('cash','card','instapay','wallet','credit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sp_sale` (`sale_id`),
  KEY `idx_sp_method` (`method`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_payments`
--

INSERT INTO `sale_payments` (`id`, `sale_id`, `method`, `amount`, `reference_number`, `notes`, `created_at`) VALUES
(1, 1, 'cash', 450.00, NULL, NULL, '2026-11-09 20:13:15'),
(2, 2, 'cash', 450.00, NULL, NULL, '2026-11-09 20:14:08'),
(3, 3, 'card', 100.00, 'POS-CARD-9912', NULL, '2026-11-09 20:14:08'),
(4, 3, 'instapay', 71.00, 'IPN-20261109-112', NULL, '2026-11-09 20:14:08');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
CREATE TABLE IF NOT EXISTS `shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT '1',
  `store_id` int(11) NOT NULL DEFAULT '2',
  `user_id` int(11) NOT NULL,
  `shift_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `closing_balance` decimal(15,2) DEFAULT NULL,
  `actual_cash` decimal(15,2) DEFAULT NULL,
  `difference` decimal(15,2) DEFAULT NULL,
  `total_sales_cash` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_sales_card` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_sales_instapay` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_sales_wallet` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_sales_credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `sales_count` int(11) NOT NULL DEFAULT '0',
  `status` enum('open','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `opened_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `closed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shift_number` (`shift_number`),
  KEY `fk_shift_user` (`user_id`),
  KEY `fk_shift_store` (`store_id`),
  KEY `idx_shift_status` (`status`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `branch_id`, `store_id`, `user_id`, `shift_number`, `opening_balance`, `closing_balance`, `actual_cash`, `difference`, `total_sales_cash`, `total_sales_card`, `total_sales_instapay`, `total_sales_wallet`, `total_sales_credit`, `sales_count`, `status`, `notes`, `opened_at`, `closed_at`) VALUES
(1, 1, 2, 1, 'SH-20261109-F144', 500.00, 1400.00, 1400.00, 0.00, 900.00, 100.00, 71.00, 0.00, 0.00, 4, 'closed', 'ط¬ط±ط¯ ط§ظ„ظˆط±ط¯ظٹط© ظ…ط·ط§ط¨ظ‚ طھظ…ط§ظ…ط§ظ‹', '2026-11-09 20:12:34', '2026-11-09 20:14:08');

-- --------------------------------------------------------

--
-- Table structure for table `stock_moves`
--

DROP TABLE IF EXISTS `stock_moves`;
CREATE TABLE IF NOT EXISTS `stock_moves` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `move_type` enum('opening','purchase','sale','purchase_return','sale_return','transfer_in','transfer_out','inventory_adjustment','damaged') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance_after` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_sm_store` (`store_id`),
  KEY `fk_sm_batch` (`batch_id`),
  KEY `fk_sm_user` (`created_by`),
  KEY `idx_sm_item_store` (`item_id`,`store_id`),
  KEY `idx_sm_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_moves`
--

INSERT INTO `stock_moves` (`id`, `item_id`, `store_id`, `batch_id`, `move_type`, `reference_type`, `reference_id`, `quantity`, `unit_cost`, `balance_after`, `notes`, `created_by`, `created_at`) VALUES
(1, 1, 1, 1, 'opening', 'initial_setup', 1, 80.00, 38.00, 80.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ط¨ط¯ط§ظٹط© ط§ظ„طھط´ط؛ظٹظ„', 1, '2026-11-09 00:50:59'),
(2, 2, 1, NULL, 'opening', 'initial_setup', 1, 50.00, 85.00, 50.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ط¨ط¯ط§ظٹط© ط§ظ„طھط´ط؛ظٹظ„', 1, '2026-11-09 00:50:59'),
(3, 3, 1, NULL, 'opening', 'initial_setup', 1, 100.00, 32.00, 100.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ط¨ط¯ط§ظٹط© ط§ظ„طھط´ط؛ظٹظ„', 1, '2026-11-09 00:50:59'),
(4, 4, 1, NULL, 'opening', 'initial_setup', 1, 60.00, 50.00, 60.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ط¨ط¯ط§ظٹط© ط§ظ„طھط´ط؛ظٹظ„', 1, '2026-11-09 00:50:59'),
(5, 6, 1, NULL, 'opening', 'initial_setup', 1, 30.00, 175.00, 30.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ط¨ط¯ط§ظٹط© ط§ظ„طھط´ط؛ظٹظ„', 1, '2026-11-09 00:50:59'),
(6, 10, 1, NULL, 'opening', 'initial_setup', 1, 40.00, 95.00, 40.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ط¨ط¯ط§ظٹط© ط§ظ„طھط´ط؛ظٹظ„', 1, '2026-11-09 00:50:59'),
(7, 1, 2, 2, 'opening', 'initial_setup', 1, 20.00, 38.00, 20.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ظ…ط¹ط±ط¶ ظˆظ†ظ‚ط·ط© ط¨ظٹط¹ ط§ظ„ظپط±ط¹', 1, '2026-11-09 00:50:59'),
(8, 2, 2, NULL, 'opening', 'initial_setup', 1, 15.00, 85.00, 15.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ظ…ط¹ط±ط¶ ظˆظ†ظ‚ط·ط© ط¨ظٹط¹ ط§ظ„ظپط±ط¹', 1, '2026-11-09 00:50:59'),
(9, 3, 2, NULL, 'opening', 'initial_setup', 1, 25.00, 32.00, 25.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ظ…ط¹ط±ط¶ ظˆظ†ظ‚ط·ط© ط¨ظٹط¹ ط§ظ„ظپط±ط¹', 1, '2026-11-09 00:50:59'),
(10, 4, 2, NULL, 'opening', 'initial_setup', 1, 20.00, 50.00, 20.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ظ…ط¹ط±ط¶ ظˆظ†ظ‚ط·ط© ط¨ظٹط¹ ط§ظ„ظپط±ط¹', 1, '2026-11-09 00:50:59'),
(11, 5, 2, 3, 'opening', 'initial_setup', 1, 120.00, 11.50, 120.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ظ…ط¹ط±ط¶ ظˆظ†ظ‚ط·ط© ط¨ظٹط¹ ط§ظ„ظپط±ط¹', 1, '2026-11-09 00:50:59'),
(12, 7, 2, 4, 'opening', 'initial_setup', 1, 25.50, 300.00, 25.50, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ظ…ط¹ط±ط¶ ظˆظ†ظ‚ط·ط© ط¨ظٹط¹ ط§ظ„ظپط±ط¹ - ط¬ط¨ظ†ط© ظ…ظٹط²ط§ظ†', 1, '2026-11-09 00:50:59'),
(13, 8, 2, NULL, 'opening', 'initial_setup', 1, 45.00, 70.00, 45.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ظ…ط¹ط±ط¶ ظˆظ†ظ‚ط·ط© ط¨ظٹط¹ ط§ظ„ظپط±ط¹ - طھظپط§ط­ ظ…ظٹط²ط§ظ†', 1, '2026-11-09 00:50:59'),
(14, 9, 2, NULL, 'opening', 'initial_setup', 1, 18.00, 145.00, 18.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ظ…ط¹ط±ط¶ ظˆظ†ظ‚ط·ط© ط¨ظٹط¹ ط§ظ„ظپط±ط¹ - ط¬ط¨ظ†ط© ظ…ظٹط²ط§ظ†', 1, '2026-11-09 00:50:59'),
(15, 10, 2, NULL, 'opening', 'initial_setup', 1, 15.00, 95.00, 15.00, 'ط±طµظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ„ظ…ط¹ط±ط¶ ظˆظ†ظ‚ط·ط© ط¨ظٹط¹ ط§ظ„ظپط±ط¹', 1, '2026-11-09 00:50:59'),
(16, 2, 2, NULL, 'sale', 'sale_invoice', 1, -2.00, 85.00, 13.00, 'ظ…ط¨ظٹط¹ط§طھ ظپط§طھظˆط±ط© ظƒط§ط´ظٹط± ط±ظ‚ظ… INV-20261109-0001', 1, '2026-11-09 20:13:15'),
(17, 7, 2, NULL, 'sale', 'sale_invoice', 1, -0.50, 300.00, 25.00, 'ظ…ط¨ظٹط¹ط§طھ ظپط§طھظˆط±ط© ظƒط§ط´ظٹط± ط±ظ‚ظ… INV-20261109-0001', 1, '2026-11-09 20:13:15'),
(18, 2, 2, NULL, 'sale', 'sale_invoice', 2, -2.00, 85.00, 11.00, 'ظ…ط¨ظٹط¹ط§طھ ظپط§طھظˆط±ط© ظƒط§ط´ظٹط± ط±ظ‚ظ… INV-20261109-0002', 1, '2026-11-09 20:14:08'),
(19, 7, 2, NULL, 'sale', 'sale_invoice', 2, -0.50, 300.00, 24.50, 'ظ…ط¨ظٹط¹ط§طھ ظپط§طھظˆط±ط© ظƒط§ط´ظٹط± ط±ظ‚ظ… INV-20261109-0002', 1, '2026-11-09 20:14:08'),
(20, 5, 2, NULL, 'sale', 'sale_invoice', 3, -10.00, 11.50, 110.00, 'ظ…ط¨ظٹط¹ط§طھ ظپط§طھظˆط±ط© ظƒط§ط´ظٹط± ط±ظ‚ظ… INV-20261109-0003', 1, '2026-11-09 20:14:08');

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

DROP TABLE IF EXISTS `stores`;
CREATE TABLE IF NOT EXISTS `stores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT '1',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_stores_branch` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`id`, `branch_id`, `code`, `name`, `location`, `manager_name`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'STR-01', 'ط§ظ„ظ…ط³طھظˆط¯ط¹ ط§ظ„ط±ط¦ظٹط³ظٹ (ط§ظ„ظ…ط±ظƒط²ظٹ)', 'ظ…ط¨ظ†ظ‰ ط§ظ„ط¥ط¯ط§ط±ط© - ط§ظ„ظ…ظ†ط·ظ‚ط© ط§ظ„طµظ†ط§ط¹ظٹط©', 'ظ…/ ظˆط¦ط§ظ… ط§ظ„ط³ظˆط¯ط§ظ†ظٹ', '01002283766', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(2, 1, 'STR-02', 'طµط§ظ„ط© ط§ظ„ط¹ط±ط¶ ظˆظ†ظ‚ط·ط© ط§ظ„ط¨ظٹط¹ (ط§ظ„ظپط±ط¹ 1)', 'ط´ط§ط±ط¹ ط§ظ„ط¬ظ…ظ‡ظˆط±ظٹط© ط§ظ„ط±ط¦ظٹط³ظٹ', 'ظƒط§ط´ظٹط± ط§ظ„ظپط±ط¹', '01000000001', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59');

-- --------------------------------------------------------

--
-- Table structure for table `store_transfers`
--

DROP TABLE IF EXISTS `store_transfers`;
CREATE TABLE IF NOT EXISTS `store_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_store_id` int(11) NOT NULL,
  `to_store_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `total_items` int(11) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int(11) NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transfer_number` (`transfer_number`),
  KEY `fk_tr_from` (`from_store_id`),
  KEY `fk_tr_to` (`to_store_id`),
  KEY `fk_tr_user` (`created_by`),
  KEY `fk_tr_approver` (`approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store_transfer_items`
--

DROP TABLE IF EXISTS `store_transfer_items`;
CREATE TABLE IF NOT EXISTS `store_transfer_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tri_transfer` (`transfer_id`),
  KEY `fk_tri_item` (`item_id`),
  KEY `fk_tri_batch` (`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  KEY `idx_setting_key` (`key`),
  KEY `idx_setting_group` (`group_name`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `key`, `value`, `group_name`, `description`, `updated_at`) VALUES
(1, 'company_name', 'ط´ط±ظƒط© ط§ظ„ظ†ط¸ظ… ط§ظ„ط­ط¯ظٹط«ط© ظ„ظ„طھط¬ط§ط±ط© ظˆط§ظ„طھظˆط²ظٹط¹', 'general', 'ط§ط³ظ… ط§ظ„ظ…ظ†ط´ط£ط© ط§ظ„طھط¬ط§ط±ظٹ ط§ظ„ط±ط³ظ…ظٹ', '2026-11-09 00:44:50'),
(2, 'tax_number', '123456789', 'tax', 'ط±ظ‚ظ… ط§ظ„طھط³ط¬ظٹظ„ ط§ظ„ط¶ط±ظٹط¨ظٹ ط§ظ„ظ…طµط±ظٹ ط§ظ„ظ…ظˆط­ط¯ (9 ط£ط±ظ‚ط§ظ…)', '2026-11-09 00:44:50'),
(3, 'commercial_registry', '987654', 'general', 'ط±ظ‚ظ… ط§ظ„ط³ط¬ظ„ ط§ظ„طھط¬ط§ط±ظٹ', '2026-11-09 00:44:50'),
(4, 'company_phone', '01002283766', 'general', 'ط±ظ‚ظ… ط§ظ„ظ‡ط§طھظپ ط§ظ„ط±ط¦ظٹط³ظٹ ظ„ظ„طھظˆط§طµظ„', '2026-11-09 00:44:50'),
(5, 'company_address', 'ط¬ظ…ظ‡ظˆط±ظٹط© ظ…طµط± ط§ظ„ط¹ط±ط¨ظٹط©', 'general', 'ط§ظ„ط¹ظ†ظˆط§ظ† ط§ظ„ط±ط¦ظٹط³ظٹ ط§ظ„ظ…ظ‚ظٹط¯ ظپظٹ ط§ظ„ظپط§طھظˆط±ط©', '2026-11-09 00:44:50'),
(6, 'vat_percent', '14', 'tax', 'ظ†ط³ط¨ط© ط¶ط±ظٹط¨ط© ط§ظ„ظ‚ظٹظ…ط© ط§ظ„ظ…ط¶ط§ظپط© ط§ظ„ط§ظپطھط±ط§ط¶ظٹط© (%)', '2026-11-09 00:44:50'),
(7, 'currency_symbol', 'ط¬.ظ…', 'general', 'ط±ظ…ط² ط§ظ„ط¹ظ…ظ„ط© ط§ظ„ط±ط³ظ…ظٹط© ظپظٹ ط§ظ„ظˆط§ط¬ظ‡ط§طھ ظˆط§ظ„ط·ط¨ط§ط¹ط©', '2026-11-09 00:44:50'),
(8, 'currency_code', 'EGP', 'general', 'ظƒظˆط¯ ط§ظ„ط¹ظ…ظ„ط© ط§ظ„ظ…طµط±ظپظٹ ط§ظ„ط¯ظˆظ„ظٹ', '2026-11-09 00:44:50'),
(9, 'enable_scale_barcodes', '1', 'pos', 'طھظپط¹ظٹظ„ ظ‚ط±ط§ط،ط© ط¨ط§ط±ظƒظˆط¯ ط§ظ„ظ…ظˆط§ط²ظٹظ† ط§ظ„ط¥ظ„ظƒطھط±ظˆظ†ظٹط© (1 ظ†ط¹ظ…طŒ 0 ظ„ط§)', '2026-11-09 00:44:50'),
(10, 'scale_barcode_prefix', '20', 'pos', 'ط¨ط§ط¯ط¦ط© ط¨ط§ط±ظƒظˆط¯ ط§ظ„ظˆط²ظ† ظ„ظ„ظ…ظˆط§ط²ظٹظ† (ط§ظ„ط§ظپطھط±ط§ط¶ظٹ 20 ط£ظˆ 99)', '2026-11-09 00:44:50'),
(11, 'allow_negative_sales', '0', 'inventory', 'ط§ظ„ط³ظ…ط§ط­ ط¨ط§ظ„ط¨ظٹط¹ ط¨ط§ظ„ط³ط§ظ„ط¨ ط¹ظ†ط¯ ط¹ط¯ظ… طھظˆظپط± ط±طµظٹط¯ ظ…ط®ط²ظ†ظٹ', '2026-11-09 00:44:50'),
(12, 'receipt_footer_text', 'ط´ظƒط±ط§ظ‹ ظ„ط²ظٹط§ط±طھظƒظ… â€¢ ظ†ط¸ط§ظ… ظ†ظ‚ط§ط· ط§ظ„ط¨ظٹط¹ ظ…ظ‡ظ†ط¯ط³ ط¨ظˆط§ط³ط·ط© ط¨ط±ط§ظ†ط¯ ط§ظ„ظ…ط¨ط±ظ…ط¬ (01002283766)', 'pos', 'ط§ظ„ظ†طµ ط§ظ„ظ…ط·ط¨ظˆط¹ ظپظٹ ط°ظٹظ„ ط¥ظٹطµط§ظ„ ط§ظ„ظƒط§ط´ظٹط± ط§ظ„ط­ط±ط§ط±ظٹ', '2026-11-09 00:44:50'),
(13, 'brand_signature', 'ط§ظ„ظ…ط¨ط±ظ…ط¬ | Almubarmig', 'branding', 'ط¹ظ„ط§ظ…ط© ط§ظ„ظ…ط·ظˆط± ط§ظ„ظ‡ظ†ط¯ط³ظٹط©', '2026-11-09 00:44:50'),
(14, 'pos_default_customer', '1', 'pos', 'ظ…ط¹ط±ظپ ط§ظ„ط¹ظ…ظٹظ„ ط§ظ„ظ†ظ‚ط¯ظٹ ط§ظ„ط§ظپطھط±ط§ط¶ظٹ ط¹ظ†ط¯ ظپطھط­ ط´ط§ط´ط© ط§ظ„ط¨ظٹط¹', '2026-11-09 20:03:43'),
(15, 'pos_tax_enabled', '1', 'pos', 'طھظپط¹ظٹظ„ ط§ط­طھط³ط§ط¨ ط¶ط±ظٹط¨ط© ط§ظ„ظ‚ظٹظ…ط© ط§ظ„ظ…ط¶ط§ظپط© 14% طھظ„ظ‚ط§ط¦ظٹط§ظ‹ ظپظٹ ط§ظ„ط³ظ„ط©', '2026-11-09 20:03:43'),
(16, 'pos_allow_negative_stock', '0', 'pos', 'ط§ظ„ط³ظ…ط§ط­ ط¨ط§ظ„ط¨ظٹط¹ ط¨ط§ظ„ط³ط§ظ„ط¨ ط¹ظ†ط¯ ظ†ظپط§ط¯ ط§ظ„ظ…ط®ط²ظˆظ† (0=ظ…ظ…ظ†ظˆط¹طŒ 1=ظ…ط³ظ…ظˆط­)', '2026-11-09 20:03:43'),
(17, 'pos_camera_scanner', '1', 'pos', 'طھظپط¹ظٹظ„ ظ…ط³ط­ ط§ظ„ط¨ط§ط±ظƒظˆط¯ ط¹ط¨ط± ظƒط§ظ…ظٹط±ط§ ط§ظ„ظ…ظˆط¨ط§ظٹظ„/ط§ظ„ظˆظٹط¨ ظƒط§ظ…ظٹط±ط§', '2026-11-09 20:03:43'),
(18, 'pos_receipt_width', '80mm', 'pos', 'ط¹ط±ط¶ ط¨ظƒط±ط© ظˆط±ظ‚ ط§ظ„ط·ط§ط¨ط¹ط© ط§ظ„ط­ط±ط§ط±ظٹط© (80mm ط£ظˆ 58mm)', '2026-11-09 20:03:43'),
(19, 'pos_receipt_footer', 'ط¨ط±ظ…ط¬ط© ظˆطھط·ظˆظٹط±: ط§ظ„ظ…ط¨ط±ظ…ط¬ (01002283766) almubarmig.com', 'pos', 'ظ†طµ طھط°ظٹظٹظ„ ط§ظ„ط¥ظٹطµط§ظ„ ط§ظ„ط­ط±ط§ط±ظٹ', '2026-11-09 20:03:43');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
CREATE TABLE IF NOT EXISTS `units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_base` tinyint(1) DEFAULT '1',
  `conversion_rate` decimal(12,4) DEFAULT '1.0000',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `symbol`, `is_base`, `conversion_rate`, `is_active`, `created_at`) VALUES
(1, 'ظ‚ط·ط¹ط©', 'ظ‚', 1, 1.0000, 1, '2026-11-09 00:50:59'),
(2, 'ط¹ظ„ط¨ط© / ط¨ط§ظƒطھ', 'ط¹ظ„ط¨ط©', 1, 1.0000, 1, '2026-11-09 00:50:59'),
(3, 'ظƒط±طھظˆظ†ط©', 'ظƒط±طھظˆظ†', 0, 12.0000, 1, '2026-11-09 00:50:59'),
(4, 'ظƒظٹظ„ظˆط¬ط±ط§ظ…', 'ظƒط¬ظ…', 1, 1.0000, 1, '2026-11-09 00:50:59'),
(5, 'ظ„طھط±', 'ظ„طھط±', 1, 1.0000, 1, '2026-11-09 00:50:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT '1',
  `role_id` int(11) NOT NULL,
  `username` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_user_branch` (`branch_id`),
  KEY `idx_user_role` (`role_id`),
  KEY `idx_user_login` (`username`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `branch_id`, `role_id`, `username`, `password_hash`, `full_name`, `phone`, `email`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'admin', '$2y$10$JQDE0WP5cPOabImqs32wW.SZbioiG6I8ztLKfs8vyXh87xAWICoJO', 'ظ…ط¯ظٹط± ط§ظ„ظ†ط¸ط§ظ… (ظ…/ ظˆط¦ط§ظ… ط§ظ„ط³ظˆط¯ط§ظ†ظٹ)', '01002283766', 'admin@almubarmig.com', 1, '2026-11-09 12:51:40', '2026-11-09 00:44:50', '2026-11-09 10:51:40'),
(2, 1, 4, 'cashier1', '$2y$10$OGmKPyNH1ZYrjOwwaD36QuxDyfYFyttjmA3g63Dmd4A5nUtA1Gmxm', 'ظƒط§ط´ظٹط± 1 - ظˆط±ط¯ظٹط© ط§ظ„ظ†ظ‡ط§ط±', '01000000000', 'cashier@almubarmig.com', 1, NULL, '2026-11-09 00:44:50', '2026-11-09 00:44:50');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_cat_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `held_carts`
--
ALTER TABLE `held_carts`
  ADD CONSTRAINT `fk_hc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_items_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_items_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

--
-- Constraints for table `item_batches`
--
ALTER TABLE `item_batches`
  ADD CONSTRAINT `fk_batch_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_batch_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_sale_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `fk_sale_shift` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sale_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  ADD CONSTRAINT `fk_sale_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `fk_si_batch` FOREIGN KEY (`batch_id`) REFERENCES `item_batches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_si_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `fk_si_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD CONSTRAINT `fk_sp_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `fk_shift_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  ADD CONSTRAINT `fk_shift_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `stock_moves`
--
ALTER TABLE `stock_moves`
  ADD CONSTRAINT `fk_sm_batch` FOREIGN KEY (`batch_id`) REFERENCES `item_batches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sm_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sm_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  ADD CONSTRAINT `fk_sm_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stores`
--
ALTER TABLE `stores`
  ADD CONSTRAINT `fk_stores_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `store_transfers`
--
ALTER TABLE `store_transfers`
  ADD CONSTRAINT `fk_tr_approver` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tr_from` FOREIGN KEY (`from_store_id`) REFERENCES `stores` (`id`),
  ADD CONSTRAINT `fk_tr_to` FOREIGN KEY (`to_store_id`) REFERENCES `stores` (`id`),
  ADD CONSTRAINT `fk_tr_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `store_transfer_items`
--
ALTER TABLE `store_transfer_items`
  ADD CONSTRAINT `fk_tri_batch` FOREIGN KEY (`batch_id`) REFERENCES `item_batches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tri_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `fk_tri_transfer` FOREIGN KEY (`transfer_id`) REFERENCES `store_transfers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

-- ==================== PHASE 3: SUPPLY CHAIN ====================
-- ============================================================================
-- WAM Tech POS & ERP - Phase 3: Supply Chain, Purchases & Returns Migration
-- Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
-- Database: wamtech_pos_db
-- Engine: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. ط¬ط¯ظˆظ„ ط§ظ„ظ…ظˆط±ط¯ظٹظ† (Suppliers)
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

-- 2. ط¬ط¯ظˆظ„ ظپظˆط§طھظٹط± ط§ظ„ظ…ط´طھط±ظٹط§طھ ط§ظ„ط±ط¦ظٹط³ظٹ (Purchase Invoices)
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

-- 3. ط¬ط¯ظˆظ„ ط¨ظ†ظˆط¯ ظپط§طھظˆط±ط© ط§ظ„ظ…ط´طھط±ظٹط§طھ (Purchase Items)
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

-- 4. ط¬ط¯ظˆظ„ ط¹ط±ظˆط¶ ط£ط³ط¹ط§ط± ط§ظ„ط¹ظ…ظ„ط§ط، (Quotations)
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

-- 5. ط¬ط¯ظˆظ„ ط¨ظ†ظˆط¯ ط¹ط±ط¶ ط§ظ„ط³ط¹ط± (Quotation Items)
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

-- 6. ط¬ط¯ظˆظ„ ظ…ط±طھط¬ط¹ط§طھ ط§ظ„ظ…ط¨ظٹط¹ط§طھ (Sales Returns)
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

-- 7. ط¬ط¯ظˆظ„ ط¨ظ†ظˆط¯ ظ…ط±طھط¬ط¹ ط§ظ„ظ…ط¨ظٹط¹ط§طھ (Sales Return Items)
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

-- 8. ط¬ط¯ظˆظ„ ظ…ط±طھط¬ط¹ط§طھ ط§ظ„ظ…ط´طھط±ظٹط§طھ ظ„ظ„ظ…ظˆط±ط¯ظٹظ† (Purchase Returns)
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

-- 9. ط¬ط¯ظˆظ„ ط¨ظ†ظˆط¯ ظ…ط±طھط¬ط¹ ط§ظ„ظ…ط´طھط±ظٹط§طھ (Purchase Return Items)
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

-- 10. طھط­ط¯ظٹط« ط¬ط¯ظˆظ„ ط§ظ„طµظ„ط§ط­ظٹط§طھ ظ„ظٹط´ظ…ظ„ ط§ظ„ظ…ظˆط±ط¯ظٹظ† ظˆط§ظ„ظ…ط±ط¯ظˆط¯ط§طھ ظˆط¹ط±ظˆط¶ ط§ظ„ط£ط³ط¹ط§ط±
INSERT INTO `permissions` (`module`, `slug`, `name`, `description`) VALUES
('suppliers', 'suppliers.view', 'ط¹ط±ط¶ ظ‚ط§ط¦ظ…ط© ط§ظ„ظ…ظˆط±ط¯ظٹظ†', 'ط§ط³طھط¹ط±ط§ط¶ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ظˆط±ط¯ظٹظ† ظˆط£ط±طµط¯طھظ‡ظ…'),
('suppliers', 'suppliers.manage', 'ط¥ط¶ط§ظپط© ظˆطھط¹ط¯ظٹظ„ ط§ظ„ظ…ظˆط±ط¯ظٹظ†', 'طھط³ط¬ظٹظ„ ظ…ظˆط±ط¯ ط¬ط¯ظٹط¯ ظˆطھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط§ظ„ط§طھطµط§ظ„'),
('suppliers', 'suppliers.statement', 'ظƒط´ظپ ط­ط³ط§ط¨ ظˆط£ط¹ظ…ط§ط± ط¯ظٹظˆظ† ط§ظ„ظ…ظˆط±ط¯', 'ط§ظ„ط§ط·ظ„ط§ط¹ ط¹ظ„ظ‰ ط§ظ„ط­ط±ظƒط§طھ ط§ظ„ظ…ط§ظ„ظٹط© ظˆط£ط¹ظ…ط§ط± ط§ظ„ظ…ط¯ظٹظˆظ†ظٹط§طھ')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ط±ط¨ط· ط§ظ„طµظ„ط§ط­ظٹط§طھ ط§ظ„ط¬ط¯ظٹط¯ط© ط¨ط§ظ„ظ…ط¯ظٹط± ط§ظ„ط¹ط§ظ…
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, `id` FROM `permissions` WHERE `module` IN ('suppliers', 'purchases', 'sales')
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

SET FOREIGN_KEY_CHECKS = 1;


-- ============================================================================
-- WAM Tech POS & ERP - Phase 3: Supply Chain Seed Data
-- Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
-- Database: wamtech_pos_db
-- ============================================================================

-- 1. ط§ظ„ظ…ظˆط±ط¯ظٹظ† ط§ظ„ظ…ط¹طھظ…ط¯ظٹظ† ظپظٹ ط§ظ„ط³ظˆظ‚ ط§ظ„ظ…طµط±ظٹ (Suppliers)
INSERT INTO `suppliers` (
    `id`, `name`, `company_name`, `contact_person`, `phone`, `email`, 
    `address`, `tax_number`, `commercial_registry`, `opening_balance`, 
    `current_balance`, `credit_limit`, `payment_terms_days`, `notes`, `is_active`
) VALUES
(1, 'ط´ط±ظƒط© ط§ظ„ط£ظ‡ط±ط§ظ… ظ„ظ„طھط¬ط§ط±ط© ظˆط§ظ„طھظˆط²ظٹط¹', 'ط§ظ„ط£ظ‡ط±ط§ظ… ظ„ظ„ط³ظ„ط¹ ط§ظ„طھظ…ظˆظٹظ†ظٹط© ظˆط§ظ„ط²ظٹظˆطھ', 'ط£/ ظ…ط­ظ…ظˆط¯ ط§ظ„ظ‡ظˆط§ط±ظٹ', '01011223344', 'ahram@supply-eg.com', 'ط³ظˆظ‚ ط§ظ„ط¹ط¨ظˆط± - ظ…ط¬ظ…ط¹ ط§ظ„ظ…ظˆط§ط¯ ط§ظ„ط؛ط°ط§ط¦ظٹط©طŒ ط§ظ„ظ‚ط§ظ‡ط±ط©', '100200300', '45678', 25000.00, 33500.00, 100000.00, 30, 'ظ…ظˆط±ط¯ ط±ط¦ظٹط³ظٹ ظ„ط²ظٹظˆطھ ط§ظ„ط·ط¹ط§ظ… ظˆط§ظ„ط¨ظ‚ظˆظ„ظٹط§طھ ظˆط§ظ„ط´ط§ظٹ', 1),
(2, 'ظ…ط¤ط³ط³ط© ط§ظ„ظ†ظٹظ„ ظ„ظ„ظ…ظ†طھط¬ط§طھ ط§ظ„ط؛ط°ط§ط¦ظٹط© ظˆط§ظ„ط£ظ„ط¨ط§ظ†', 'ظ…ط¬ظ…ظˆط¹ط© ط§ظ„ظ†ظٹظ„ ظ„ظ…ظ†طھط¬ط§طھ ط§ظ„ط£ظ„ط¨ط§ظ†', 'ظ…/ ط­ط³ط§ظ… ط§ظ„ط´ط§ط°ظ„ظٹ', '01122334455', 'nile.dairy@supply-eg.com', 'ط§ظ„ظ…ظ†ط·ظ‚ط© ط§ظ„طµظ†ط§ط¹ظٹط© ط§ظ„ط«ط§ظ„ط«ط©طŒ ط§ظ„ط³ط§ط¯ط³ ظ…ظ† ط£ظƒطھظˆط¨ط±', '200300400', '65432', 15000.00, 15000.00, 80000.00, 15, 'ظˆظƒظٹظ„ ظ…ظ†طھط¬ط§طھ ط§ظ„ط£ظ„ط¨ط§ظ† ظˆط§ظ„ط£ط¬ط¨ط§ظ† ط§ظ„ط·ط§ط²ط¬ط© ظˆط§ظ„ط¹طµط§ط¦ط±', 1),
(3, 'ط§ظ„ط´ط±ظƒط© ط§ظ„ظ‡ظ†ط¯ط³ظٹط© ظ„ظ„ط£ط¬ظ‡ط²ط© ظˆط§ظ„طھظˆط±ظٹط¯ط§طھ ط§ظ„ط¥ظ„ظƒطھط±ظˆظ†ظٹط©', 'ط§ظ„ظ…ظ‡ظ†ط¯ط³ظˆظ† ط§ظ„ظ…طھط­ط¯ظˆظ† ظ„ظ„طھظ‚ظ†ظٹط©', 'ظ…/ ظƒط±ظٹظ… ظ†ط¨ظٹظ„', '01233445566', 'eng.tech@supply-eg.com', 'ط´ط§ط±ط¹ ط§ظ„ط·ظٹط±ط§ظ†طŒ ظ…ط¯ظٹظ†ط© ظ†طµط±طŒ ط§ظ„ظ‚ط§ظ‡ط±ط©', '300400500', '89012', 0.00, 12400.00, 50000.00, 45, 'ظ…ظˆط±ط¯ ط·ط§ط¨ط¹ط§طھ ط§ظ„ط¨ط§ط±ظƒظˆط¯ ظˆط§ظ„ط´ظˆط§ط­ظ† ظˆط§ظ„ظƒط§ط¨ظ„ط§طھ', 1),
(4, 'ظ…طµظ†ط¹ ط§ظ„ط´ط±ظ‚ ظ„ظ„ط¨ظ„ط§ط³طھظٹظƒ ظˆظ…ظˆط§ط¯ ط§ظ„طھط¹ط¨ط¦ط©', 'ط§ظ„ط´ط±ظ‚ ظ„ظ…ظˆط§ط¯ ط§ظ„طھط؛ظ„ظٹظپ ظˆط§ظ„طھط¹ط¨ط¦ط©', 'ط£/ ط³ظٹط¯ ط§ظ„ظ…ظ†ط´ط§ظˆظٹ', '01555667788', 'sharq.pack@supply-eg.com', 'ط§ظ„ط¹ط§ط´ط± ظ…ظ† ط±ظ…ط¶ط§ظ†طŒ ط§ظ„ظ…ظ†ط·ظ‚ط© B4', '400500600', '34567', 5000.00, 5000.00, 40000.00, 30, 'ظ…ظˆط±ط¯ ط£ظƒظٹط§ط³ ط§ظ„طھط¹ط¨ط¦ط© ظˆط¨ظƒط± ظˆط±ظ‚ ط§ظ„ظپظˆط§طھظٹط± ط§ظ„ط­ط±ط§ط±ظٹ 80mm', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `current_balance` = VALUES(`current_balance`);

-- 2. ظپظˆط§طھظٹط± ط§ظ„ظ…ط´طھط±ظٹط§طھ ط§ظ„طھط¬ط±ظٹط¨ظٹط© (Purchases)
INSERT INTO `purchases` (
    `id`, `invoice_number`, `supplier_invoice_number`, `supplier_id`, `store_id`, `branch_id`,
    `invoice_date`, `subtotal`, `discount_amount`, `tax_amount`, `grand_total`, 
    `paid_amount`, `remaining_amount`, `payment_status`, `payment_method`, `notes`, `created_by`
) VALUES
(1, 'PUR-20261101-0001', 'INV-SUP-8841', 1, 1, 1, '2026-11-01', 12000.00, 500.00, 1610.00, 13110.00, 5000.00, 8110.00, 'partial', 'cash', 'طھظˆط±ظٹط¯ ط¯ظپط¹ط© ط²ظٹظˆطھ ط·ط¹ط§ظ… ظˆط£ط±ط² ظˆط´ط§ظٹ ظ„ظ„ظ…ط³طھظˆط¯ط¹ ط§ظ„ظ…ط±ظƒط²ظٹ', 1),
(2, 'PUR-20261110-0002', 'TECH-INV-220', 3, 1, 1, '2026-11-10', 11000.00, 0.00, 1540.00, 12540.00, 0.00, 12540.00, 'unpaid', 'credit', 'طھظˆط±ظٹط¯ ط§ظƒط³ط³ظˆط§ط±ط§طھ ظˆط´ظˆط§ط­ظ† ط¹ظ„ظ‰ ط§ظ„ط­ط³ط§ط¨ - ط£ط¬ظ„ 45 ظٹظˆظ…ط§ظ‹', 1),
(3, 'PUR-20261114-0003', 'MILK-9901', 2, 2, 1, '2026-11-14', 7000.00, 200.00, 952.00, 7752.00, 7752.00, 0.00, 'paid', 'bank_transfer', 'ط³ط¯ط§ط¯ ظ†ظ‚ط¯ظٹ ط¹ط¨ط± طھط­ظˆظٹظ„ ط¨ظ†ظƒظٹ ظ„طھظˆط±ظٹط¯ ط£ظ„ط¨ط§ظ† ط¬ظ‡ظٹظ†ط© ظ„ظپط±ط¹ ط§ظ„ط¬ظ…ظ‡ظˆط±ظٹط©', 1)
ON DUPLICATE KEY UPDATE `invoice_number` = VALUES(`invoice_number`);

-- 3. ط¨ظ†ظˆط¯ ظپظˆط§طھظٹط± ط§ظ„ط´ط±ط§ط، (Purchase Items)
INSERT INTO `purchase_items` (
    `id`, `purchase_id`, `item_id`, `quantity`, `unit_cost`, `tax_percent`, `tax_amount`, `total_cost`, `batch_number`, `expire_date`
) VALUES
-- ط¨ظ†ظˆط¯ ظپط§طھظˆط±ط© 1
(1, 1, 2, 50.00, 85.00, 14.00, 595.00, 4845.00, 'BATCH-SLITE-2026A', '2027-10-31'),
(2, 1, 3, 100.00, 32.00, 0.00, 0.00, 3200.00, 'BATCH-DOHA-2026', '2027-05-31'),
(3, 1, 4, 80.00, 50.00, 14.00, 560.00, 4560.00, 'BATCH-TEA-88', '2028-01-15'),

-- ط¨ظ†ظˆط¯ ظپط§طھظˆط±ط© 2
(4, 2, 5, 200.00, 11.50, 14.00, 322.00, 2622.00, 'CAN-COCA-909', '2027-06-30'),
(5, 2, 6, 20.00, 120.00, 14.00, 336.00, 2736.00, 'BATCH-CHG-65W', NULL),

-- ط¨ظ†ظˆط¯ ظپط§طھظˆط±ط© 3
(6, 3, 1, 180.00, 38.00, 14.00, 957.60, 7797.60, 'BATCH-MILK-NOV26', '2026-12-15')
ON DUPLICATE KEY UPDATE `total_cost` = VALUES(`total_cost`);

-- 4. ط¹ط±ظˆط¶ ط£ط³ط¹ط§ط± طھط¬ط±ظٹط¨ظٹط© ظ„ظ„ط¹ظ…ظ„ط§ط، (Quotations)
INSERT INTO `quotations` (
    `id`, `quotation_number`, `customer_id`, `store_id`, `branch_id`,
    `quotation_date`, `expiry_date`, `subtotal`, `discount_amount`, `tax_amount`, 
    `grand_total`, `status`, `notes`, `created_by`
) VALUES
(1, 'QUO-20261112-0001', 1, 2, 1, '2026-11-12', '2026-11-26', 4200.00, 100.00, 574.00, 4674.00, 'sent', 'ط¹ط±ط¶ ط³ط¹ط± طھظˆط±ظٹط¯ ظ…ظˆط§ط¯ طھظ…ظˆظٹظ†ظٹط© ظ„ط¬ظ…ط¹ظٹط© ط®ظٹط±ظٹط©', 1),
(2, 'QUO-20261114-0002', 2, 2, 1, '2026-11-14', '2026-11-28', 8500.00, 250.00, 1155.00, 9405.00, 'accepted', 'ط¹ط±ط¶ ط³ط¹ط± ظ„ظپط±ط¹ ط´ط±ظƒط© ط§ظ„طھظ‚ظ†ظٹط© ط¬ط§ظ‡ط² ظ„ظ„طھط­ظˆظٹظ„ ط¥ظ„ظ‰ ظپط§طھظˆط±ط©', 1)
ON DUPLICATE KEY UPDATE `quotation_number` = VALUES(`quotation_number`);

-- 5. ط¨ظ†ظˆط¯ ط¹ط±ظˆط¶ ط§ظ„ط£ط³ط¹ط§ط± (Quotation Items)
INSERT INTO `quotation_items` (`id`, `quotation_id`, `item_id`, `quantity`, `unit_price`, `tax_amount`, `total_price`) VALUES
(1, 1, 2, 20.00, 100.00, 280.00, 2280.00),
(2, 1, 3, 50.00, 40.00, 0.00, 2000.00),
(3, 2, 1, 100.00, 45.00, 630.00, 5130.00),
(4, 2, 4, 60.00, 60.00, 504.00, 4104.00)
ON DUPLICATE KEY UPDATE `total_price` = VALUES(`total_price`);

-- 6. ظ…ط±طھط¬ط¹ ظ…ط¨ظٹط¹ط§طھ طھط¬ط±ظٹط¨ظٹ (Sales Returns)
INSERT INTO `sales_returns` (
    `id`, `return_number`, `sale_id`, `customer_id`, `store_id`,
    `return_date`, `subtotal`, `tax_amount`, `total_refund`, `refund_method`, `reason`, `created_by`
) VALUES
(1, 'SRET-20261113-0001', 1, 1, 2, '2026-11-13', 90.00, 12.60, 102.60, 'cash', 'ط¹ط¨ظˆط© ظ…ظƒط±ط±ط© طھظ… ط´ط±ط§ط¤ظ‡ط§ ط¨ط§ظ„ط®ط·ط£ ظˆط­ط§ظ„طھظ‡ط§ ط³ظ„ظٹظ…ط©', 1)
ON DUPLICATE KEY UPDATE `return_number` = VALUES(`return_number`);

INSERT INTO `sales_return_items` (`id`, `return_id`, `item_id`, `quantity`, `unit_price`, `tax_amount`, `total_refund`, `condition_state`) VALUES
(1, 1, 1, 2.00, 45.00, 12.60, 102.60, 'good')
ON DUPLICATE KEY UPDATE `total_refund` = VALUES(`total_refund`);

-- 7. ظ…ط±طھط¬ط¹ ظ…ط´طھط±ظٹط§طھ طھط¬ط±ظٹط¨ظٹ ظ„ظ„ظ…ظˆط±ط¯ (Purchase Returns)
INSERT INTO `purchase_returns` (
    `id`, `return_number`, `purchase_id`, `supplier_id`, `store_id`,
    `return_date`, `subtotal`, `tax_amount`, `total_amount`, `refund_status`, `reason`, `created_by`
) VALUES
(1, 'PRET-20261112-0001', 1, 1, 1, '2026-11-12', 680.00, 95.20, 775.20, 'credited_to_balance', 'طھظ„ظپ ظپظٹ 8 ط¹ط¨ظˆط§طھ ط²ظٹطھ ط£ط«ظ†ط§ط، ظپط­طµ ط§ظ„ط§ط³طھظ„ط§ظ…', 1)
ON DUPLICATE KEY UPDATE `return_number` = VALUES(`return_number`);

INSERT INTO `purchase_return_items` (`id`, `return_id`, `item_id`, `quantity`, `unit_cost`, `tax_amount`, `total_cost`, `batch_number`) VALUES
(1, 1, 2, 8.00, 85.00, 95.20, 775.20, 'BATCH-SLITE-2026A')
ON DUPLICATE KEY UPDATE `total_cost` = VALUES(`total_cost`);


-- ==================== PHASE 4: ACCOUNTING ====================
-- ============================================================================
-- WAM Tech POS & ERP - Phase 4: General Accounting & Financial Ledger Migration
-- Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
-- Database: wamtech_pos_db
-- Engine: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. ط¬ط¯ظˆظ„ ط´ط¬ط±ط© ظˆط¯ظ„ظٹظ„ ط§ظ„ط­ط³ط§ط¨ط§طھ ط§ظ„ظ…ط§ظ„ظٹ (Chart of Accounts)
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

-- 2. ط¬ط¯ظˆظ„ ظ‚ظٹظˆط¯ ط§ظ„ظٹظˆظ…ظٹط© ط§ظ„ط¹ط§ظ…ط© ط§ظ„ظ…ط²ط¯ظˆط¬ط© (Journal Entries - Double-Entry Ledger)
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

-- 3. ط¬ط¯ظˆظ„ ط£ط·ط±ط§ظپ ظ‚ظٹظˆط¯ ط§ظ„ظٹظˆظ…ظٹط© (Journal Entry Lines / Items)
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

-- 4. ط¬ط¯ظˆظ„ ط³ظ†ط¯ط§طھ ط§ظ„ظ‚ط¨ط¶ ظˆط§ظ„طµط±ظپ ط§ظ„ظ…ط§ظ„ظٹ (Financial Vouchers)
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

-- 5. طھط­ط¯ظٹط« ظˆطھط£ظƒظٹط¯ طµظ„ط§ط­ظٹط§طھ ظ…ظˆط¯ظٹظˆظ„ ط§ظ„ط­ط³ط§ط¨ط§طھ
INSERT INTO `permissions` (`module`, `slug`, `name`, `description`) VALUES
('accounting', 'accounting.view', 'ط¹ط±ط¶ ط§ظ„ط­ط³ط§ط¨ط§طھ ظˆط§ظ„ظٹظˆظ…ظٹط©', 'ط§ط³طھط¹ط±ط§ط¶ ط§ظ„ظ‚ظٹظˆط¯ ظˆط§ظ„ط­ط³ط§ط¨ط§طھ ط§ظ„ط¹ط§ظ…ط©'),
('accounting', 'accounting.chart', 'ط¥ط¯ط§ط±ط© ط´ط¬ط±ط© ط§ظ„ط­ط³ط§ط¨ط§طھ', 'ط¥ط¶ط§ظپط© ظˆطھط¹ط¯ظٹظ„ ط­ط³ط§ط¨ط§طھ ط§ظ„ط¯ظ„ظٹظ„ ط§ظ„ط´ط¬ط±ظٹ'),
('accounting', 'accounting.journal', 'ط¥ط¶ط§ظپط© ظ‚ظٹظˆط¯ ظٹظˆظ…ظٹط© ظٹط¯ظˆظٹط©', 'طھط³ط¬ظٹظ„ ظ‚ظٹظˆط¯ ظ…ط­ط§ط³ط¨ظٹط© ظ…ط²ط¯ظˆط¬ط© ظ…طھط²ظ†ط©'),
('accounting', 'accounting.vouchers', 'ط¥طµط¯ط§ط± ط³ظ†ط¯ط§طھ ط§ظ„ظ‚ط¨ط¶ ظˆط§ظ„طµط±ظپ', 'طھط­ط±ظٹط± ط¥ظٹطµط§ظ„ط§طھ ط§ط³طھظ„ط§ظ… ظˆط³ط¯ط§ط¯ ظ…ط§ظ„ظٹ'),
('accounting', 'accounting.reports', 'ط§ظ„ظ‚ظˆط§ط¦ظ… ط§ظ„ط®طھط§ظ…ظٹط© ظˆط§ظ„ظ…ظٹط²ط§ظ†', 'ط§ط³طھط®ط±ط§ط¬ ظ…ظٹط²ط§ظ† ط§ظ„ظ…ط±ط§ط¬ط¹ط© ظˆظ‚ط§ط¦ظ…ط© ط§ظ„ط¯ط®ظ„')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ط±ط¨ط· ط§ظ„طµظ„ط§ط­ظٹط§طھ ط¨ط§ظ„ظ…ط¯ظٹط± ط§ظ„ط¹ط§ظ…
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, `id` FROM `permissions` WHERE `module` = 'accounting'
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

SET FOREIGN_KEY_CHECKS = 1;


-- ============================================================================
-- WAM Tech POS & ERP - Phase 4: Chart of Accounts & Accounting Seed Data
-- Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
-- Database: wamtech_pos_db
-- ============================================================================

-- 1. ط¯ظ„ظٹظ„ ط§ظ„ط­ط³ط§ط¨ط§طھ ط§ظ„ط´ط¬ط±ظٹ ط§ظ„ظ…طµط±ظٹ ط§ظ„ظ…ط¹طھظ…ط¯ (Standard Chart of Accounts)
INSERT INTO `chart_of_accounts` (
    `id`, `account_code`, `name`, `parent_id`, `type`, `nature`, `level`, `is_parent`, 
    `opening_balance`, `current_balance`, `description`
) VALUES
-- ط§ظ„ظ…ط³طھظˆظ‰ 1: ط§ظ„ط­ط³ط§ط¨ط§طھ ط§ظ„ط±ط¦ظٹط³ظٹط© ط§ظ„ظƒط¨ط±ظ‰
(1, '1', 'ط§ظ„ط£طµظˆظ„', NULL, 'asset', 'debit', 1, 1, 0.00, 0.00, 'ظ…ط¬ظ…ظˆط¹ ط§ظ„ظ…ظˆط¬ظˆط¯ط§طھ ظˆط§ظ„ط£طµظˆظ„ ظˆط§ظ„ظ…ظˆط§ط±ط¯ ط§ظ„ط§ظ‚طھطµط§ط¯ظٹط©'),
(2, '2', 'ط§ظ„ط®طµظˆظ… ظˆط§ظ„ط§ظ„طھط²ط§ظ…ط§طھ', NULL, 'liability', 'credit', 1, 1, 0.00, 0.00, 'ط§ظ„طھط²ط§ظ…ط§طھ ط§ظ„ظ…ظ†ط´ط£ط© طھط¬ط§ظ‡ ط§ظ„ظ…ظˆط±ط¯ظٹظ† ظˆط§ظ„ط¬ظ‡ط§طھ ط§ظ„ط®ط§ط±ط¬ظٹط©'),
(3, '3', 'ط­ظ‚ظˆظ‚ ط§ظ„ظ…ظ„ظƒظٹط©', NULL, 'equity', 'credit', 1, 1, 0.00, 0.00, 'ط±ط£ط³ ط§ظ„ظ…ط§ظ„ ظˆط§ظ„ط£ط±ط¨ط§ط­ ط§ظ„ظ…ط¨ظ‚ط§ط© ظˆط­ظ‚ظˆظ‚ ط§ظ„ط´ط±ظƒط§ط،'),
(4, '4', 'ط§ظ„ط¥ظٹط±ط§ط¯ط§طھ', NULL, 'revenue', 'credit', 1, 1, 0.00, 0.00, 'ط¹ظˆط§ط¦ط¯ ط§ظ„ظ†ط´ط§ط· ط§ظ„طھط¬ط§ط±ظٹ ظˆط§ظ„ظ…ط¨ظٹط¹ط§طھ ظˆط§ظ„ط®ط¯ظ…ط§طھ'),
(5, '5', 'ط§ظ„ظ…طµط±ظˆظپط§طھ', NULL, 'expense', 'debit', 1, 1, 0.00, 0.00, 'طھظƒط§ظ„ظٹظپ ط§ظ„ظ†ط´ط§ط· ظˆظ…طµط±ظˆظپط§طھ ط§ظ„طھط´ط؛ظٹظ„ ظˆط§ظ„ط¥ط¯ط§ط±ط©'),

-- ط§ظ„ظ…ط³طھظˆظ‰ 2: ط§ظ„ظ…ط¬ظ…ظˆط¹ط§طھ ط§ظ„ظپط±ط¹ظٹط© ظ„ظ„ط£طµظˆظ„
(6, '11', 'ط§ظ„ط£طµظˆظ„ ط§ظ„ظ…طھط¯ط§ظˆظ„ط©', 1, 'asset', 'debit', 2, 1, 0.00, 0.00, 'ط§ظ„ظ†ظ‚ط¯ظٹط© ظˆط§ظ„ط¨ظ†ظˆظƒ ظˆط§ظ„ط¹ظ…ظ„ط§ط، ظˆط§ظ„ظ…ط®ط²ظˆظ†'),
(7, '12', 'ط§ظ„ط£طµظˆظ„ ط§ظ„ط«ط§ط¨طھط©', 1, 'asset', 'debit', 2, 1, 0.00, 0.00, 'ط§ظ„ظ…ط¹ط¯ط§طھ ظˆط§ظ„ط£ط¬ظ‡ط²ط© ظˆط§ظ„ط£ط«ط§ط« ظˆط§ظ„ط¯ظٹظƒظˆط±ط§طھ'),

-- ط§ظ„ظ…ط³طھظˆظ‰ 2: ط§ظ„ظ…ط¬ظ…ظˆط¹ط§طھ ط§ظ„ظپط±ط¹ظٹط© ظ„ظ„ط®طµظˆظ…
(8, '21', 'ط§ظ„ط®طµظˆظ… ط§ظ„ظ…طھط¯ط§ظˆظ„ط© (ظ‚طµظٹط±ط© ط§ظ„ط£ط¬ظ„)', 2, 'liability', 'credit', 2, 1, 0.00, 0.00, 'ط§ظ„ظ…ظˆط±ط¯ظˆظ† ظˆظ…ط³طھط­ظ‚ط§طھ ط§ظ„ط¶ط±ط§ط¦ط¨ ظˆط§ظ„ظ…طµط±ظˆظپط§طھ ط§ظ„ظ…ط³طھط­ظ‚ط©'),

-- ط§ظ„ظ…ط³طھظˆظ‰ 2: ط§ظ„ظ…ط¬ظ…ظˆط¹ط§طھ ط§ظ„ظپط±ط¹ظٹط© ظ„ط­ظ‚ظˆظ‚ ط§ظ„ظ…ظ„ظƒظٹط©
(9, '31', 'ط±ط£ط³ ط§ظ„ظ…ط§ظ„ ظˆط§ظ„ط§ط­طھظٹط§ط·ظٹط§طھ', 3, 'equity', 'credit', 2, 1, 0.00, 0.00, 'ط±ط£ط³ ظ…ط§ظ„ ط§ظ„ظ…ط´ط±ظˆط¹ ط§ظ„ظ…ط¯ظپظˆط¹ ظˆط§ظ„ط£ط±ط¨ط§ط­ ط§ظ„ظ…ط±ط­ظ„ط©'),

-- ط§ظ„ظ…ط³طھظˆظ‰ 2: ط§ظ„ظ…ط¬ظ…ظˆط¹ط§طھ ط§ظ„ظپط±ط¹ظٹط© ظ„ظ„ط¥ظٹط±ط§ط¯ط§طھ
(10, '41', 'ط¥ظٹط±ط§ط¯ط§طھ ط§ظ„ظ†ط´ط§ط· ط§ظ„طھط¬ط§ط±ظٹ', 4, 'revenue', 'credit', 2, 1, 0.00, 0.00, 'ظ…ط¨ظٹط¹ط§طھ ط§ظ„ظ…ظ†طھط¬ط§طھ ظˆط§ظ„ط®طµظˆظ…ط§طھ ط§ظ„ظ…ظƒطھط³ط¨ط©'),

-- ط§ظ„ظ…ط³طھظˆظ‰ 2: ط§ظ„ظ…ط¬ظ…ظˆط¹ط§طھ ط§ظ„ظپط±ط¹ظٹط© ظ„ظ„ظ…طµط±ظˆظپط§طھ
(11, '51', 'طھظƒظ„ظپط© ط§ظ„ط¨ط¶ط§ط¹ط© ط§ظ„ظ…ط¨ط§ط¹ط© (ط§ظ„ظ…ط¨ط§ط´ط±ط©)', 5, 'expense', 'debit', 2, 1, 0.00, 0.00, 'طھظƒظ„ظپط© ط§ظ„ظ…ط´طھط±ظٹط§طھ ط§ظ„ظ…ط¨ط§ط´ط±ط© ط§ظ„ظ…ط±طھط¨ط·ط© ط¨ط§ظ„ظ…ط¨ظٹط¹ط§طھ'),
(12, '52', 'ط§ظ„ظ…طµط±ظˆظپط§طھ ط§ظ„ط¹ظ…ظˆظ…ظٹط© ظˆط§ظ„طھط´ط؛ظٹظ„ظٹط©', 5, 'expense', 'debit', 2, 1, 0.00, 0.00, 'ط§ظ„ط±ظˆط§طھط¨طŒ ط§ظ„ط¥ظٹط¬ط§ط±طŒ ط§ظ„ظ…ط±ط§ظپظ‚ ظˆط§ظ„ظ…طµط§ط±ظٹظپ ط§ظ„ط¥ط¯ط§ط±ظٹط©'),

-- ط§ظ„ظ…ط³طھظˆظ‰ 3 ظˆ 4: ط§ظ„ط­ط³ط§ط¨ط§طھ ط§ظ„طھظپطµظٹظ„ظٹط© ط§ظ„ظ‚ط§ط¨ظ„ط© ظ„ظ„طھظ‚ظٹظٹط¯ (Posting Accounts)
-- ط­ط³ط§ط¨ط§طھ ط§ظ„ط£طµظˆظ„ ط§ظ„ظ…طھط¯ط§ظˆظ„ط© (11)
(13, '1101', 'ط§ظ„ط®ط²ظٹظ†ط© ط§ظ„ط±ط¦ظٹط³ظٹط© (ط§ظ„طµظ†ط¯ظˆظ‚ ط§ظ„ظ…ط±ظƒط²ظٹ)', 6, 'asset', 'debit', 3, 0, 50000.00, 65000.00, 'ط§ظ„ظ†ظ‚ط¯ظٹط© ط§ظ„ط³ط§ط¦ظ„ط© ط¨ط§ظ„ط®ط²ظٹظ†ط© ط§ظ„ظ…ط±ظƒط²ظٹط©'),
(14, '1102', 'ط¹ظ‡ط¯ط© ظƒط§ط´ظٹط± ظ†ظ‚ط§ط· ط§ظ„ط¨ظٹط¹ (POS Drawer)', 6, 'asset', 'debit', 3, 0, 2000.00, 2000.00, 'ط§ظ„ط³ظٹظˆظ„ط© ط§ظ„ظ…ط®طµطµط© ظ„ط¨ط¯ط§ظٹط© ط¯ط±ط¬ ط§ظ„ظƒط§ط´ظٹط±'),
(15, '1103', 'ط§ظ„ط¨ظ†ظƒ ط§ظ„ط£ظ‡ظ„ظٹ ط§ظ„ظ…طµط±ظٹ (ط­ط³ط§ط¨ ط¬ط§ط±ظٹ)', 6, 'asset', 'debit', 3, 0, 150000.00, 150000.00, 'ط§ظ„ط­ط³ط§ط¨ ط§ظ„ط¨ظ†ظƒظٹ ط§ظ„ط±ط¦ظٹط³ظٹ ظ„ظ„ط¹ظ…ظ„ظٹط§طھ ظˆط§ظ„طھط­ظˆظٹظ„ط§طھ'),
(16, '1104', 'ط­ط³ط§ط¨ ط§ظ„ط¹ظ…ظ„ط§ط، ظˆط§ظ„ظ…ط¯ظٹظ†ظˆظ† (ط§ظ„ط°ظ…ظ… ط§ظ„ظ…ط¯ظٹظ†ط©)', 6, 'asset', 'debit', 3, 0, 10000.00, 10000.00, 'ط¥ط¬ظ…ط§ظ„ظٹ ط£ط±طµط¯ط© ظ…ط¯ظٹظˆظ†ظٹط§طھ ط§ظ„ط¹ظ…ظ„ط§ط، ط§ظ„ط¢ط¬ظ„ط©'),
(17, '1105', 'ظ…ط®ط²ظˆظ† ط§ظ„ط¨ط¶ط§ط¦ط¹ ط¨ط§ظ„ظ…ط³طھظˆط¯ط¹ط§طھ', 6, 'asset', 'debit', 3, 0, 80000.00, 80000.00, 'ظ‚ظٹظ…ط© ط§ظ„ظ…ط®ط²ظˆظ† ط§ظ„ط³ظ„ط¹ظٹ ط§ظ„ظ…ط¹ط¯ ظ„ظ„ط¨ظٹط¹ ط¨ط§ظ„طھظƒظ„ظپط©'),
(18, '1106', 'ط¶ط±ظٹط¨ط© ط§ظ„ظ‚ظٹظ…ط© ط§ظ„ظ…ط¶ط§ظپط© - ظ…ط¯ط®ظ„ط§طھ (ظ…ط´طھط±ظٹط§طھ)', 6, 'asset', 'debit', 3, 0, 0.00, 0.00, 'ط¶ط±ظٹط¨ط© ط§ظ„ظ…ط´طھط±ظٹط§طھ ط§ظ„ظ‚ط§ط¨ظ„ط© ظ„ظ„ط®طµظ… ظˆط§ظ„طھط³ظˆظٹط©'),

-- ط­ط³ط§ط¨ط§طھ ط§ظ„ط£طµظˆظ„ ط§ظ„ط«ط§ط¨طھط© (12)
(19, '1201', 'ط£ط¬ظ‡ط²ط© ط§ظ„ط­ط§ط³ط¨ ظˆظ†ظ‚ط§ط· ط§ظ„ط¨ظٹط¹ ظˆط·ط§ط¨ط¹ط§طھ ط§ظ„ط¨ط§ط±ظƒظˆط¯', 7, 'asset', 'debit', 3, 0, 35000.00, 35000.00, 'ط§ظ„ط£ط¬ظ‡ط²ط© ط§ظ„ط¥ظ„ظƒطھط±ظˆظ†ظٹط© ظˆط£ظ†ط¸ظ…ط© ط§ظ„ظƒط§ط´ظٹط±'),
(20, '1202', 'ط§ظ„ط£ط«ط§ط« ظˆط§ظ„طھط¬ظ‡ظٹط²ط§طھ ظˆط±ظپظˆظپ ط§ظ„ط¹ط±ط¶', 7, 'asset', 'debit', 3, 0, 25000.00, 25000.00, 'ط±ظپظˆظپ ط§ظ„ط¹ط±ط¶ ظˆط§ظ„طھط¬ظ‡ظٹط²ط§طھ ط§ظ„ظ…ظƒطھط¨ظٹط©'),

-- ط­ط³ط§ط¨ط§طھ ط§ظ„ط®طµظˆظ… ط§ظ„ظ…طھط¯ط§ظˆظ„ط© (21)
(21, '2101', 'ط­ط³ط§ط¨ ط§ظ„ظ…ظˆط±ط¯ظˆظ† ظˆط§ظ„ط¯ط§ط¦ظ†ظˆظ† (ط§ظ„ط°ظ…ظ… ط§ظ„ط¯ط§ط¦ظ†ط©)', 8, 'liability', 'credit', 3, 0, 50900.00, 50900.00, 'ط¥ط¬ظ…ط§ظ„ظٹ ظ…ط¯ظٹظˆظ†ظٹط§طھ ط§ظ„طھظˆط±ظٹط¯ ط§ظ„ظ…ط³طھط­ظ‚ط© ظ„ظ„ظ…ظˆط±ط¯ظٹظ†'),
(22, '2102', 'ط¶ط±ظٹط¨ط© ط§ظ„ظ‚ظٹظ…ط© ط§ظ„ظ…ط¶ط§ظپط© - ظ…ط®ط±ط¬ط§طھ (ظ…ط¨ظٹط¹ط§طھ)', 8, 'liability', 'credit', 3, 0, 0.00, 0.00, 'ط¶ط±ظٹط¨ط© ط§ظ„ظ…ط¨ظٹط¹ط§طھ ط§ظ„ظ…ط³طھط­ظ‚ط© ظ„ظ…طµظ„ط­ط© ط§ظ„ط¶ط±ط§ط¦ط¨'),
(23, '2103', 'ظ…طµط±ظˆظپط§طھ ظ…ط³طھط­ظ‚ط© ظˆط£ظ…ط§ظ†ط§طھ', 8, 'liability', 'credit', 3, 0, 0.00, 0.00, 'ط±ظˆط§طھط¨ ط£ظˆ ظ…ط³طھط­ظ‚ط§طھ ظ…ط¤ط¬ظ„ط© ط§ظ„ط³ط¯ط§ط¯'),

-- ط­ط³ط§ط¨ط§طھ ط­ظ‚ظˆظ‚ ط§ظ„ظ…ظ„ظƒظٹط© (31)
(24, '3101', 'ط±ط£ط³ ط§ظ„ظ…ط§ظ„ ط§ظ„ظ…ط¯ظپظˆط¹', 9, 'equity', 'credit', 3, 0, 301100.00, 301100.00, 'ط±ط£ط³ ظ…ط§ظ„ ط§ظ„ظ…ظ†ط´ط£ط© ط§ظ„طھط£ط³ظٹط³ظٹ ط§ظ„ظ…ط¹طھظ…ط¯'),

-- ط­ط³ط§ط¨ط§طھ ط§ظ„ط¥ظٹط±ط§ط¯ط§طھ (41)
(25, '4101', 'ط¥ظٹط±ط§ط¯ط§طھ ظ…ط¨ظٹط¹ط§طھ ط§ظ„ط¨ط¶ط§ط¦ط¹', 10, 'revenue', 'credit', 3, 0, 0.00, 0.00, 'ط§ظ„ظ…ط¨ظٹط¹ط§طھ ط§ظ„ظ†ظ‚ط¯ظٹط© ظˆط§ظ„ط¥ظ„ظƒطھط±ظˆظ†ظٹط© ظˆط§ظ„ط¢ط¬ظ„ط©'),
(26, '4102', 'ط¥ظٹط±ط§ط¯ط§طھ ط®ط¯ظ…ط§طھ ط§ظ„ط´ط­ظ† ظˆط§ظ„طھظˆطµظٹظ„', 10, 'revenue', 'credit', 3, 0, 0.00, 0.00, 'ط¹ظˆط§ط¦ط¯ ط®ط¯ظ…ط© ط§ظ„ط¯ظ„ظٹظپط±ظٹ ظˆط§ظ„طھظˆطµظٹظ„ ظ„ظ„ظ…ظ†ط§ط²ظ„'),
(27, '4103', 'ط®طµظˆظ…ط§طھ ظ…ظƒطھط³ط¨ط© ظ…ظ† ط§ظ„ظ…ظˆط±ط¯ظٹظ†', 10, 'revenue', 'credit', 3, 0, 0.00, 0.00, 'ط§ظ„ط®طµظ… ط§ظ„طھط¬ط§ط±ظٹ ظˆط§ظ„ظ†ظ‚ط¯ظٹ ط§ظ„ظ…ظ…ظ†ظˆط­ ظ…ظ† ط§ظ„ظ…ظˆط±ط¯ظٹظ†'),

-- ط­ط³ط§ط¨ط§طھ ط§ظ„ظ…طµط±ظˆظپط§طھ (51 ظˆ 52)
(28, '5101', 'طھظƒظ„ظپط© ط§ظ„ط¨ط¶ط§ط¹ط© ط§ظ„ظ…ط¨ط§ط¹ط© (COGS)', 11, 'expense', 'debit', 3, 0, 0.00, 0.00, 'ط§ظ„طھظƒظ„ظپط© ط§ظ„ط´ط±ط§ط¦ظٹط© ط§ظ„ظ…ط¨ط§ط´ط±ط© ظ„ظ„ط£طµظ†ط§ظپ ط§ظ„ظ…ط¨ط§ط¹ط©'),
(29, '5201', 'ط±ظˆط§طھط¨ ظˆط£ط¬ظˆط± ط§ظ„ظ…ظˆط¸ظپظٹظ†', 12, 'expense', 'debit', 3, 0, 0.00, 0.00, 'ط£ط¬ظˆط± ط§ظ„ظƒط§ط´ظٹط± ظˆط§ظ„ظ…ط®ط²ظ†ط¬ظٹط© ظˆط§ظ„ط¥ط¯ط§ط±ظٹظٹظ†'),
(30, '5202', 'ط¥ظٹط¬ط§ط± ط§ظ„ظ…ظ‚ط±ط§طھ ظˆط§ظ„ظ…ط³طھظˆط¯ط¹ط§طھ', 12, 'expense', 'debit', 3, 0, 0.00, 0.00, 'ط¥ظٹط¬ط§ط± ط§ظ„ظپط±ط¹ ظˆط§ظ„ظ…ط³طھظˆط¯ط¹ ط§ظ„ظ…ط±ظƒط²ظٹ'),
(31, '5203', 'ظپظˆط§طھظٹط± ط§ظ„ظƒظ‡ط±ط¨ط§ط، ظˆط§ظ„ظ…ظٹط§ظ‡ ظˆط§ظ„ط¥ظ†طھط±ظ†طھ', 12, 'expense', 'debit', 3, 0, 0.00, 0.00, 'ظپظˆط§طھظٹط± ط§ظ„ظ…ط±ط§ظپظ‚ ط§ظ„ط´ظ‡ط±ظٹط©'),
(32, '5204', 'ظ…طµط±ظˆظپط§طھ ط§ظ„طµظٹط§ظ†ط© ظˆط§ظ„ظ†ط¸ط§ظپط©', 12, 'expense', 'debit', 3, 0, 0.00, 0.00, 'ط£ط¹ظ…ط§ظ„ ط§ظ„طµظٹط§ظ†ط© ط§ظ„ط¯ظˆط±ظٹط© ظˆط§ظ„ظ†ط¸ط§ظپط©'),
(33, '5205', 'ط£ط¯ظˆط§طھ ظ…ظƒطھط¨ظٹط© ظˆط¨ظƒط± ظپظˆط§طھظٹط± ط­ط±ط§ط±ظٹ 80mm', 12, 'expense', 'debit', 3, 0, 0.00, 0.00, 'ظ…ط³طھظ„ط²ظ…ط§طھ ط§ظ„ط·ط¨ط§ط¹ط© ظˆط§ظ„ط£ظƒظٹط§ط³ ظˆط§ظ„ط£ط¯ظˆط§طھ'),
(34, '5206', 'ط¹ظ…ظˆظ„ط§طھ ط§ظ„ط¯ظپط¹ ط§ظ„ط¥ظ„ظƒطھط±ظˆظ†ظٹ ظˆط§ظ„ط¨ظ†ظƒظٹ (ظ…ظٹط²ط©/ظپظٹط²ط§)', 12, 'expense', 'debit', 3, 0, 0.00, 0.00, 'ط¹ظ…ظˆظ„ط§طھ ظ†ظ‚ط§ط· ط§ظ„ط¨ظٹط¹ ظˆظ…ط§ظƒظٹظ†ط§طھ POS ط§ظ„ط¨ظ†ظƒظٹط©')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `current_balance` = VALUES(`current_balance`);

-- 2. ظ‚ظٹط¯ ط§ظپطھطھط§ط­ظٹ ظ…طھط²ظ† ظ†ظ…ظˆط°ط¬ظٹ (Balanced Opening Journal Entry)
-- ط¥ط¬ظ…ط§ظ„ظٹ ط§ظ„ظ…ط¯ظٹظ†: 352,000 ط¬.ظ… = ط¥ط¬ظ…ط§ظ„ظٹ ط§ظ„ط¯ط§ط¦ظ†: 352,000 ط¬.ظ…
INSERT INTO `journal_entries` (
    `id`, `entry_number`, `entry_date`, `doc_reference`, `description`, 
    `total_debit`, `total_credit`, `is_balanced`, `is_posted`, `source_type`, `created_by`
) VALUES
(1, 'JV-20261101-0001', '2026-11-01', 'OPENING-2026', 'ط§ظ„ظ‚ظٹط¯ ط§ظ„ط§ظپطھطھط§ط­ظٹ ظ„ط£ط±طµط¯ط© ط¨ط¯ط§ظٹط© ط§ظ„ظپطھط±ط© ط§ظ„ظ…ط§ظ„ظٹط© 2026', 352000.00, 352000.00, 1, 1, 'manual', 1)
ON DUPLICATE KEY UPDATE `entry_number` = VALUES(`entry_number`);

INSERT INTO `journal_entry_items` (`id`, `entry_id`, `account_id`, `debit`, `credit`, `description`) VALUES
-- ط£ط·ط±ط§ظپ ط§ظ„ظ…ط¯ظٹظ† (ط§ظ„ط£طµظˆظ„) = 352,000
(1, 1, 13, 50000.00, 0.00, 'ط¥ط«ط¨ط§طھ ط±طµظٹط¯ ط§ظ„ط®ط²ظٹظ†ط© ط§ظ„ط±ط¦ظٹط³ظٹط© ط§ظ„ط§ظپطھطھط§ط­ظٹ'),
(2, 1, 14, 2000.00, 0.00, 'ط¥ط«ط¨ط§طھ ط±طµظٹط¯ ط¹ظ‡ط¯ط© ط¯ط±ط¬ ط§ظ„ظƒط§ط´ظٹط±'),
(3, 1, 15, 150000.00, 0.00, 'ط¥ط«ط¨ط§طھ ط±طµظٹط¯ ط§ظ„ط¨ظ†ظƒ ط§ظ„ط£ظ‡ظ„ظٹ ط§ظ„ظ…طµط±ظٹ'),
(4, 1, 16, 10000.00, 0.00, 'ط¥ط«ط¨ط§طھ ظ…ط¯ظٹظˆظ†ظٹط§طھ ط§ظ„ط¹ظ…ظ„ط§ط، ط§ظ„ط§ظپطھطھط§ط­ظٹط©'),
(5, 1, 17, 80000.00, 0.00, 'ط¥ط«ط¨ط§طھ ط¨ط¶ط§ط¹ط© ط£ظˆظ„ ط§ظ„ظ…ط¯ط© ط¨ط§ظ„ظ…ط³طھظˆط¯ط¹ط§طھ'),
(6, 1, 19, 35000.00, 0.00, 'ط¥ط«ط¨ط§طھ ط£طµظˆظ„ ط£ط¬ظ‡ط²ط© ط§ظ„ظƒط§ط´ظٹط± ظˆط§ظ„ط¨ط§ط±ظƒظˆط¯'),
(7, 1, 20, 25000.00, 0.00, 'ط¥ط«ط¨ط§طھ ط§ظ„ط£ط«ط§ط« ظˆط±ظپظˆظپ ط§ظ„ط¹ط±ط¶'),

-- ط£ط·ط±ط§ظپ ط§ظ„ط¯ط§ط¦ظ† (ط§ظ„ط®طµظˆظ… ظˆط­ظ‚ظˆظ‚ ط§ظ„ظ…ظ„ظƒظٹط©) = 352,000
(8, 1, 21, 0.00, 50900.00, 'ط¥ط«ط¨ط§طھ ظ…ط¯ظٹظˆظ†ظٹط§طھ ط§ظ„ظ…ظˆط±ط¯ظٹظ† ط§ظ„ط§ظپطھطھط§ط­ظٹط© ط§ظ„ظ…ط³طھط­ظ‚ط©'),
(9, 1, 24, 0.00, 301100.00, 'ط¥ط«ط¨ط§طھ ط±ط£ط³ ط§ظ„ظ…ط§ظ„ ط§ظ„ظ…ط¯ظپظˆط¹ ط§ظ„طµط§ظپظٹ')
ON DUPLICATE KEY UPDATE `debit` = VALUES(`debit`), `credit` = VALUES(`credit`);

-- 3. ط³ظ†ط¯ط§طھ ظ…ط§ظ„ظٹط© طھط¬ط±ظٹط¨ظٹط© (Financial Vouchers)
INSERT INTO `financial_vouchers` (
    `id`, `voucher_number`, `voucher_type`, `voucher_date`, `account_id`, `target_account_id`, 
    `amount`, `payment_method`, `received_from_or_paid_to`, `notes`, `created_by`
) VALUES
-- ط³ظ†ط¯ طµط±ظپ ط¥ظٹط¬ط§ط± ط§ظ„ظ…ظ‚ط±: 5,000 ط¬.ظ…
(1, 'PV-20261102-0001', 'payment', '2026-11-02', 13, 30, 5000.00, 'cash', 'ظ…ط§ظ„ظƒ ط§ظ„ط¹ظ‚ط§ط± - ط£/ ظ…طµط·ظپظ‰ ظپظ‡ظ…ظٹ', 'ط³ط¯ط§ط¯ ط¥ظٹط¬ط§ط± ظ…ظ‚ط± ط§ظ„ظپط±ط¹ ظ„ط´ظ‡ط± ظ†ظˆظپظ…ط¨ط± 2026 ظ†ظ‚ط¯ط§ظ‹', 1),
-- ط³ظ†ط¯ ظ‚ط¨ط¶ طھط­طµظٹظ„ ظ…ط¯ظٹظˆظ†ظٹط© ط¹ظ…ظٹظ„: 3,500 ط¬.ظ…
(2, 'RV-20261105-0001', 'receipt', '2026-11-05', 13, 16, 3500.00, 'cash', 'ط§ظ„ط¹ظ…ظٹظ„: ط´ط±ظƒط© ط§ظ„ط£ظ…ظ„ ظ„ظ„طھظˆط±ظٹط¯ط§طھ', 'طھط­طµظٹظ„ ط¯ظپط¹ط© ظ†ظ‚ط¯ظٹط© طھط­طھ ط§ظ„ط­ط³ط§ط¨', 1),
-- ط³ظ†ط¯ طµط±ظپ ظپط§طھظˆط±ط© ظƒظ‡ط±ط¨ط§ط، ظˆط¥ظ†طھط±ظ†طھ: 1,200 ط¬.ظ…
(3, 'PV-20261112-0002', 'payment', '2026-11-12', 13, 31, 1200.00, 'cash', 'ط´ط±ظƒط© ط§ظ„ظƒظ‡ط±ط¨ط§ط، ظˆط§ظ„ظ…طµط±ظٹط© ظ„ظ„ط§طھطµط§ظ„ط§طھ', 'ط³ط¯ط§ط¯ ط§ط³طھظ‡ظ„ط§ظƒ ط§ظ„ظƒظ‡ط±ط¨ط§ط، ظˆط¨ط§ظ‚ط© ط§ظ„ط¥ظ†طھط±ظ†طھ ظ„ط´ظ‡ط± ط£ظƒطھظˆط¨ط±', 1)
ON DUPLICATE KEY UPDATE `voucher_number` = VALUES(`voucher_number`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


