<?php
/**
 * متحكم المبيعات وسجل الفواتير - Sales Invoices Controller
 * WAM Tech POS & ERP
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();
Auth::requirePermission('sales.view');

$db = Database::getInstance();

// Filters
$dateFrom = $_GET['date_from'] ?? date('Y-m-d');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');
$paymentMethod = $_GET['payment_method'] ?? '';
$search = trim($_GET['search'] ?? '');

$where = ["DATE(s.created_at) >= ? AND DATE(s.created_at) <= ?"];
$params = [$dateFrom, $dateTo];

if (!empty($paymentMethod)) {
    $where[] = "s.payment_method = ?";
    $params[] = $paymentMethod;
}

if (!empty($search)) {
    $where[] = "(s.invoice_number LIKE ? OR c.name LIKE ? OR c.phone LIKE ?)";
    $s = '%' . $search . '%';
    $params[] = $s;
    $params[] = $s;
    $params[] = $s;
}

$whereClause = implode(' AND ', $where);

// 1. Fetch Invoices
$sql = "
    SELECT s.*, 
           c.name AS customer_name, c.phone AS customer_phone,
           u.full_name AS cashier_name,
           st.name AS store_name,
           sh.shift_number
    FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.id
    LEFT JOIN users u ON s.user_id = u.id
    LEFT JOIN stores st ON s.store_id = st.id
    LEFT JOIN shifts sh ON s.shift_id = sh.id
    WHERE {$whereClause}
    ORDER BY s.id DESC
    LIMIT 200
";
$sales = $db->fetchAll($sql, $params);

// 2. Fetch Period Sales Statistics
$statsSql = "
    SELECT 
        COUNT(*) AS total_invoices,
        COALESCE(SUM(s.grand_total), 0) AS total_revenue,
        COALESCE(SUM(s.tax_amount), 0) AS total_tax,
        COALESCE(SUM(s.discount_amount), 0) AS total_discount,
        COALESCE(SUM(CASE WHEN s.payment_method = 'cash' THEN s.grand_total ELSE 0 END), 0) AS cash_revenue,
        COALESCE(SUM(CASE WHEN s.payment_method = 'card' THEN s.grand_total ELSE 0 END), 0) AS card_revenue,
        COALESCE(SUM(CASE WHEN s.payment_method = 'instapay' THEN s.grand_total ELSE 0 END), 0) AS instapay_revenue,
        COALESCE(SUM(CASE WHEN s.payment_method = 'wallet' THEN s.grand_total ELSE 0 END), 0) AS wallet_revenue,
        COALESCE(SUM(CASE WHEN s.payment_method = 'credit' OR s.remaining_amount > 0 THEN s.remaining_amount ELSE 0 END), 0) AS credit_revenue
    FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.id
    WHERE {$whereClause}
";
$stats = $db->fetchOne($statsSql, $params);

View::render('modules/sales/sales_list', [
    'sales'         => $sales,
    'stats'         => $stats,
    'dateFrom'      => $dateFrom,
    'dateTo'        => $dateTo,
    'paymentMethod' => $paymentMethod,
    'search'        => $search
]);

