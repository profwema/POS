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
(2, NULL, 'sale_created', 'sales', 2, NULL, '{\"desc\": \"إصدار فاتورة مبيعات INV-20261109-0002 بقيمة 423.20 ج.م\", \"total\": 423.2, \"invoice\": \"INV-20261109-0002\"}', '127.0.0.1', '', '2026-11-09 20:14:08'),
(3, NULL, 'sale_created', 'sales', 3, NULL, '{\"desc\": \"إصدار فاتورة مبيعات INV-20261109-0003 بقيمة 171.00 ج.م\", \"total\": 171, \"invoice\": \"INV-20261109-0003\"}', '127.0.0.1', '', '2026-11-09 20:14:08');

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
(1, 'BR001', 'الفرع الرئيسي - المركز التجاري', '01002283766', 'القاهرة - جمهورية مصر العربية', 1, '2026-11-09 00:44:50', '2026-11-09 00:44:50');

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
(1, 'CAT-FOOD', 'أغذية ومشروبات', 'المشروبات الغازية والعصائر والمياه المعدنية', 'fas fa-wine-bottle', NULL, 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(2, 'CAT-GROCERY', 'بقالة تموينية وجافة', 'الأرز، الزيوت، السكر، البقوليات والتوابل', 'fas fa-shopping-basket', NULL, 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(3, 'CAT-DAIRY', 'ألبان وأجبان طازجة', 'الألبان، الأجبان، الزبدة والقشطة', 'fas fa-cheese', NULL, 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(4, 'CAT-VEG', 'خضار وفواكه (ميزان)', 'خضار وفواكه طازجة تباع بالميزان الإلكتروني', 'fas fa-apple-alt', NULL, 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(5, 'CAT-CLEAN', 'منظفات وعناية منزلية', 'مساحيق الغسيل، المطهرات ومستلزمات النظافة', 'fas fa-pump-soap', NULL, 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(6, 'CAT-ELEC', 'إلكترونيات واكسسوارات', 'كابلات، شواحن، بطاريات واكسسوارات إلكترونية', 'fas fa-plug', NULL, 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59');

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
(1, 'عميل نقدي عام (Walk-in Customer)', '01000000000', 'cash@pos.local', 'نقطة البيع - الفرع 1', NULL, 0.00, 0.00, 0, NULL, 1, '2026-11-09 20:03:20', '2026-11-09 20:03:20'),
(2, 'م/ حسام مصطفى (شركة النور للتجارة)', '01123456789', 'hossam@alnoor.com', 'القاهرة - مصر الجديدة', NULL, 15000.00, 0.00, 28, NULL, 1, '2026-11-09 20:03:20', '2026-11-09 20:14:08'),
(3, 'د/ أحمد عادل الرفاعي', '01234567890', 'ahmed.refaie@gmail.com', 'القاهرة - مدينة نصر', NULL, 5000.00, 0.00, 10, NULL, 1, '2026-11-09 20:03:20', '2026-11-09 20:03:20');

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
(1, 1, 5, '6221155010015', 'JOH-MILK-1L', 'لبن جهينة كامل الدسم 1 لتر', 38.00, 45.00, 42.00, 41.00, 40.00, 14.00, 15.00, 0, NULL, 1, NULL, 'صنف غذائي يومي سريع الدوران', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(2, 2, 1, '6223000540021', 'SLITE-OIL-1.5', 'زيت عباد الشمس سلايت 1.5 لتر', 85.00, 100.00, 94.00, 92.00, 90.00, 14.00, 10.00, 0, NULL, 0, NULL, 'زيت طعام نقي', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(3, 2, 1, '6221234560018', 'DOHA-RICE-1KG', 'أرز مصري فاخر الضحى 1 كجم', 32.00, 40.00, 36.00, 35.00, 34.00, 0.00, 20.00, 0, NULL, 0, NULL, 'أرز منقى ومعبأ آلياً - معفى من الضريبة', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(4, 2, 2, '6221008000452', 'AROSA-TEA-250', 'شاي العروسة أسود ناعم 250 جم', 50.00, 60.00, 56.00, 54.00, 52.00, 14.00, 12.00, 0, NULL, 0, NULL, 'شاي أسود ممتاز', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(5, 1, 1, '5449000000996', 'COCA-CAN-330', 'كوكاكولا مشروب غازي كانز 330 مل', 11.50, 15.00, 13.50, 13.00, 12.50, 14.00, 24.00, 0, NULL, 1, NULL, 'مشروب غازي منعش', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(6, 5, 1, '8001090123456', 'ARIEL-AUTO-2.5', 'مسحوق غسيل أوتوماتيك أريال لافندر 2.5 كجم', 175.00, 210.00, 195.00, 190.00, 185.00, 14.00, 8.00, 0, NULL, 0, NULL, 'مسحوق منظف للملابس', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(7, 3, 4, '200000100000', 'CHEESE-ROOMY-KG', 'جبنة رومي قديمة فاخرة (ميزان/كجم)', 300.00, 360.00, 335.00, 330.00, 320.00, 14.00, 10.00, 1, '00001', 1, NULL, 'صنف ميزان باركود وزني يبدأ بـ 20 أو 99', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(8, 4, 4, '200000200000', 'APPLE-RED-KG', 'تفاح أحمر سكري إيطالي طازج (ميزان/كجم)', 70.00, 90.00, 82.00, 80.00, 75.00, 0.00, 15.00, 1, '00002', 0, NULL, 'فواكه طازجة - صنف ميزان باركود وزني', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(9, 3, 4, '200000300000', 'CHEESE-BRAMILY-KG', 'جبنة براميلي بالفلفل الأخضر (ميزان/كجم)', 145.00, 180.00, 165.00, 160.00, 155.00, 14.00, 8.00, 1, '00003', 1, NULL, 'جبن طبيعي معتق', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(10, 6, 1, '6974123456789', 'ANK-CAB-TC', 'كابل شحن سريع انكر Anker Type-C 1m', 95.00, 150.00, 130.00, 125.00, 120.00, 14.00, 5.00, 0, NULL, 0, NULL, 'اكسسوارات وضمان معتمد', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59');

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
(1, 'pos', 'pos.view', 'فتح شاشة الكاشير', 'إمكانية فتح شاشة نقطة البيع السريعة', '2026-11-09 00:44:50'),
(2, 'pos', 'pos.sell', 'إتمام عمليات البيع', 'إصدار الفواتير وتحصيل المبالغ', '2026-11-09 00:44:50'),
(3, 'pos', 'pos.discount', 'تطبيق خصم يدوي', 'تطبيق خصم استثنائي على الفاتورة', '2026-11-09 00:44:50'),
(4, 'pos', 'pos.shift_close', 'إغلاق الوردية', 'تسليم الوردية وحساب العجز والزيادة', '2026-11-09 00:44:50'),
(5, 'sales', 'sales.view', 'استعراض فواتير المبيعات', 'الاطلاع على جدول الفواتير السابقة', '2026-11-09 00:44:50'),
(6, 'sales', 'sales.create', 'إصدار فاتورة بيع كبرى', 'إصدار فواتير بيع آجلة ونقدية', '2026-11-09 00:44:50'),
(7, 'sales', 'sales.return', 'إصدار مرتجع مبيعات', 'استرجاع بضاعة مباعة وتعديل الرصيد', '2026-11-09 00:44:50'),
(8, 'sales', 'sales.quotes', 'إدارة عروض الأسعار', 'إنشاء وتعديل وتحويل عروض الأسعار', '2026-11-09 00:44:50'),
(9, 'purchases', 'purchases.view', 'استعراض فواتير المشتريات', 'عرض مشتريات الموردين السابقة', '2026-11-09 00:44:50'),
(10, 'purchases', 'purchases.create', 'إضافة فاتورة مشتريات', 'تسجيل بضائع جديدة في المخزن', '2026-11-09 00:44:50'),
(11, 'purchases', 'purchases.return', 'مرتجع مشتريات', 'إرجاع بضاعة للموردين', '2026-11-09 00:44:50'),
(12, 'inventory', 'inventory.view', 'عرض المخزون وبطاقة الصنف', 'الاطلاع على كميات الأصناف والأسعار', '2026-11-09 00:44:50'),
(13, 'inventory', 'inventory.manage', 'إضافة وتعديل الأصناف', 'إدخال أصناف جديدة وتعديل أسعارها', '2026-11-09 00:44:50'),
(14, 'inventory', 'inventory.transfer', 'التحويل بين المخازن', 'نقل بضاعة بين المستودعات', '2026-11-09 00:44:50'),
(15, 'inventory', 'inventory.stocktake', 'جرد المخازن والتسويات', 'إجراء جرد فعلي وتعديل الفروق', '2026-11-09 00:44:50'),
(16, 'accounting', 'accounting.view', 'عرض شجرة الحسابات واليومية', 'استعراض الحسابات والقيود', '2026-11-09 00:44:50'),
(17, 'accounting', 'accounting.journal', 'إضافة قيود يومية', 'تسجيل القيود المزدوجة المتزنة', '2026-11-09 00:44:50'),
(18, 'accounting', 'accounting.expenses', 'تسجيل المصروفات', 'إثبات سندات الصرف والمصروفات اليومية', '2026-11-09 00:44:50'),
(19, 'accounting', 'accounting.reports', 'القوائم الختامية والميزانية', 'ميزان المراجعة وقائمة الدخل', '2026-11-09 00:44:50'),
(20, 'settings', 'settings.manage', 'إعدادات النظام العامة', 'تعديل بيانات المنشأة والضريبة', '2026-11-09 00:44:50'),
(21, 'settings', 'settings.users', 'إدارة الموظفين والمستخدمين', 'إنشاء وتعديل مستخدمين وتحديد الصلاحيات', '2026-11-09 00:44:50'),
(22, 'settings', 'settings.backup', 'النسخ الاحتياطي', 'تنزيل ومزامنة قاعدة البيانات', '2026-11-09 00:44:50');

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
(1, 'مدير النظام العام', 'admin', 'صلاحيات كاملة وغير مقيدة لكافة شاشات ووظائف النظام', '2026-11-09 00:44:50'),
(2, 'مدير الفرع', 'manager', 'إدارة العمليات اليومية والفواتير والتقارير داخل الفرع', '2026-11-09 00:44:50'),
(3, 'المحاسب المالي', 'accountant', 'إدارة الحسابات، قيود اليومية، وسندات الصرف والإيراد والمصروفات', '2026-11-09 00:44:50'),
(4, 'كاشير نقطة البيع', 'cashier', 'شاشة البيع السريعة، الورديات، وتسليم النقدية فقط', '2026-11-09 00:44:50'),
(5, 'أمين المستودع', 'storekeeper', 'حركة المخزون، الاستلام، أذون الصرف، والتحويل بين المخازن', '2026-11-09 00:44:50');

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
(1, 1, 2, 1, 'SH-20261109-F144', 500.00, 1400.00, 1400.00, 0.00, 900.00, 100.00, 71.00, 0.00, 0.00, 4, 'closed', 'جرد الوردية مطابق تماماً', '2026-11-09 20:12:34', '2026-11-09 20:14:08');

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
(1, 1, 1, 1, 'opening', 'initial_setup', 1, 80.00, 38.00, 80.00, 'رصيد افتتاحي لبداية التشغيل', 1, '2026-11-09 00:50:59'),
(2, 2, 1, NULL, 'opening', 'initial_setup', 1, 50.00, 85.00, 50.00, 'رصيد افتتاحي لبداية التشغيل', 1, '2026-11-09 00:50:59'),
(3, 3, 1, NULL, 'opening', 'initial_setup', 1, 100.00, 32.00, 100.00, 'رصيد افتتاحي لبداية التشغيل', 1, '2026-11-09 00:50:59'),
(4, 4, 1, NULL, 'opening', 'initial_setup', 1, 60.00, 50.00, 60.00, 'رصيد افتتاحي لبداية التشغيل', 1, '2026-11-09 00:50:59'),
(5, 6, 1, NULL, 'opening', 'initial_setup', 1, 30.00, 175.00, 30.00, 'رصيد افتتاحي لبداية التشغيل', 1, '2026-11-09 00:50:59'),
(6, 10, 1, NULL, 'opening', 'initial_setup', 1, 40.00, 95.00, 40.00, 'رصيد افتتاحي لبداية التشغيل', 1, '2026-11-09 00:50:59'),
(7, 1, 2, 2, 'opening', 'initial_setup', 1, 20.00, 38.00, 20.00, 'رصيد افتتاحي لمعرض ونقطة بيع الفرع', 1, '2026-11-09 00:50:59'),
(8, 2, 2, NULL, 'opening', 'initial_setup', 1, 15.00, 85.00, 15.00, 'رصيد افتتاحي لمعرض ونقطة بيع الفرع', 1, '2026-11-09 00:50:59'),
(9, 3, 2, NULL, 'opening', 'initial_setup', 1, 25.00, 32.00, 25.00, 'رصيد افتتاحي لمعرض ونقطة بيع الفرع', 1, '2026-11-09 00:50:59'),
(10, 4, 2, NULL, 'opening', 'initial_setup', 1, 20.00, 50.00, 20.00, 'رصيد افتتاحي لمعرض ونقطة بيع الفرع', 1, '2026-11-09 00:50:59'),
(11, 5, 2, 3, 'opening', 'initial_setup', 1, 120.00, 11.50, 120.00, 'رصيد افتتاحي لمعرض ونقطة بيع الفرع', 1, '2026-11-09 00:50:59'),
(12, 7, 2, 4, 'opening', 'initial_setup', 1, 25.50, 300.00, 25.50, 'رصيد افتتاحي لمعرض ونقطة بيع الفرع - جبنة ميزان', 1, '2026-11-09 00:50:59'),
(13, 8, 2, NULL, 'opening', 'initial_setup', 1, 45.00, 70.00, 45.00, 'رصيد افتتاحي لمعرض ونقطة بيع الفرع - تفاح ميزان', 1, '2026-11-09 00:50:59'),
(14, 9, 2, NULL, 'opening', 'initial_setup', 1, 18.00, 145.00, 18.00, 'رصيد افتتاحي لمعرض ونقطة بيع الفرع - جبنة ميزان', 1, '2026-11-09 00:50:59'),
(15, 10, 2, NULL, 'opening', 'initial_setup', 1, 15.00, 95.00, 15.00, 'رصيد افتتاحي لمعرض ونقطة بيع الفرع', 1, '2026-11-09 00:50:59'),
(16, 2, 2, NULL, 'sale', 'sale_invoice', 1, -2.00, 85.00, 13.00, 'مبيعات فاتورة كاشير رقم INV-20261109-0001', 1, '2026-11-09 20:13:15'),
(17, 7, 2, NULL, 'sale', 'sale_invoice', 1, -0.50, 300.00, 25.00, 'مبيعات فاتورة كاشير رقم INV-20261109-0001', 1, '2026-11-09 20:13:15'),
(18, 2, 2, NULL, 'sale', 'sale_invoice', 2, -2.00, 85.00, 11.00, 'مبيعات فاتورة كاشير رقم INV-20261109-0002', 1, '2026-11-09 20:14:08'),
(19, 7, 2, NULL, 'sale', 'sale_invoice', 2, -0.50, 300.00, 24.50, 'مبيعات فاتورة كاشير رقم INV-20261109-0002', 1, '2026-11-09 20:14:08'),
(20, 5, 2, NULL, 'sale', 'sale_invoice', 3, -10.00, 11.50, 110.00, 'مبيعات فاتورة كاشير رقم INV-20261109-0003', 1, '2026-11-09 20:14:08');

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
(1, 1, 'STR-01', 'المستودع الرئيسي (المركزي)', 'مبنى الإدارة - المنطقة الصناعية', 'م/ وئام السوداني', '01002283766', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59'),
(2, 1, 'STR-02', 'صالة العرض ونقطة البيع (الفرع 1)', 'شارع الجمهورية الرئيسي', 'كاشير الفرع', '01000000001', 1, '2026-11-09 00:50:59', '2026-11-09 00:50:59');

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
(1, 'company_name', 'شركة النظم الحديثة للتجارة والتوزيع', 'general', 'اسم المنشأة التجاري الرسمي', '2026-11-09 00:44:50'),
(2, 'tax_number', '123456789', 'tax', 'رقم التسجيل الضريبي المصري الموحد (9 أرقام)', '2026-11-09 00:44:50'),
(3, 'commercial_registry', '987654', 'general', 'رقم السجل التجاري', '2026-11-09 00:44:50'),
(4, 'company_phone', '01002283766', 'general', 'رقم الهاتف الرئيسي للتواصل', '2026-11-09 00:44:50'),
(5, 'company_address', 'جمهورية مصر العربية', 'general', 'العنوان الرئيسي المقيد في الفاتورة', '2026-11-09 00:44:50'),
(6, 'vat_percent', '14', 'tax', 'نسبة ضريبة القيمة المضافة الافتراضية (%)', '2026-11-09 00:44:50'),
(7, 'currency_symbol', 'ج.م', 'general', 'رمز العملة الرسمية في الواجهات والطباعة', '2026-11-09 00:44:50'),
(8, 'currency_code', 'EGP', 'general', 'كود العملة المصرفي الدولي', '2026-11-09 00:44:50'),
(9, 'enable_scale_barcodes', '1', 'pos', 'تفعيل قراءة باركود الموازين الإلكترونية (1 نعم، 0 لا)', '2026-11-09 00:44:50'),
(10, 'scale_barcode_prefix', '20', 'pos', 'بادئة باركود الوزن للموازين (الافتراضي 20 أو 99)', '2026-11-09 00:44:50'),
(11, 'allow_negative_sales', '0', 'inventory', 'السماح بالبيع بالسالب عند عدم توفر رصيد مخزني', '2026-11-09 00:44:50'),
(12, 'receipt_footer_text', 'شكراً لزيارتكم • نظام نقاط البيع مهندس بواسطة براند المبرمج (01002283766)', 'pos', 'النص المطبوع في ذيل إيصال الكاشير الحراري', '2026-11-09 00:44:50'),
(13, 'brand_signature', 'المبرمج | Almubarmig', 'branding', 'علامة المطور الهندسية', '2026-11-09 00:44:50'),
(14, 'pos_default_customer', '1', 'pos', 'معرف العميل النقدي الافتراضي عند فتح شاشة البيع', '2026-11-09 20:03:43'),
(15, 'pos_tax_enabled', '1', 'pos', 'تفعيل احتساب ضريبة القيمة المضافة 14% تلقائياً في السلة', '2026-11-09 20:03:43'),
(16, 'pos_allow_negative_stock', '0', 'pos', 'السماح بالبيع بالسالب عند نفاد المخزون (0=ممنوع، 1=مسموح)', '2026-11-09 20:03:43'),
(17, 'pos_camera_scanner', '1', 'pos', 'تفعيل مسح الباركود عبر كاميرا الموبايل/الويب كاميرا', '2026-11-09 20:03:43'),
(18, 'pos_receipt_width', '80mm', 'pos', 'عرض بكرة ورق الطابعة الحرارية (80mm أو 58mm)', '2026-11-09 20:03:43'),
(19, 'pos_receipt_footer', 'برمجة وتطوير: المبرمج (01002283766) almubarmig.com', 'pos', 'نص تذييل الإيصال الحراري', '2026-11-09 20:03:43');

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
(1, 'قطعة', 'ق', 1, 1.0000, 1, '2026-11-09 00:50:59'),
(2, 'علبة / باكت', 'علبة', 1, 1.0000, 1, '2026-11-09 00:50:59'),
(3, 'كرتونة', 'كرتون', 0, 12.0000, 1, '2026-11-09 00:50:59'),
(4, 'كيلوجرام', 'كجم', 1, 1.0000, 1, '2026-11-09 00:50:59'),
(5, 'لتر', 'لتر', 1, 1.0000, 1, '2026-11-09 00:50:59');

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
(1, 1, 1, 'admin', '$2y$10$JQDE0WP5cPOabImqs32wW.SZbioiG6I8ztLKfs8vyXh87xAWICoJO', 'مدير النظام (م/ وئام السوداني)', '01002283766', 'admin@almubarmig.com', 1, '2026-11-09 12:51:40', '2026-11-09 00:44:50', '2026-11-09 10:51:40'),
(2, 1, 4, 'cashier1', '$2y$10$OGmKPyNH1ZYrjOwwaD36QuxDyfYFyttjmA3g63Dmd4A5nUtA1Gmxm', 'كاشير 1 - وردية النهار', '01000000000', 'cashier@almubarmig.com', 1, NULL, '2026-11-09 00:44:50', '2026-11-09 00:44:50');

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
