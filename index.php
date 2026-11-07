<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';

// 1. Live Sales and Invoices count
$salesQ = "SELECT 
            COALESCE(SUM(all_total), 0) AS total_sales,
            COUNT(*) AS total_invoices,
            COALESCE(SUM(`left`), 0) AS total_customer_debts
           FROM sales 
           WHERE storeid = '$storeid' OR storeid = '0'";
$salesRes = mysqli_query($adController->MySQL, $salesQ);
$salesData = ($salesRes && mysqli_num_rows($salesRes) > 0) ? mysqli_fetch_assoc($salesRes) : [];
$totalSales = (float)($salesData['total_sales'] ?? 0);
$totalInvoices = (int)($salesData['total_invoices'] ?? 0);
$totalCustomerDebts = (float)($salesData['total_customer_debts'] ?? 0);

// 2. Live Purchases and Supplier Payables
$purchQ = "SELECT 
            COALESCE(SUM(all_total), 0) AS total_purchases,
            COALESCE(SUM(`left`), 0) AS total_supplier_payables,
            COUNT(*) AS total_purch_invoices
           FROM purchase 
           WHERE storeid = '$storeid' OR storeid = '0'";
$purchRes = mysqli_query($adController->MySQL, $purchQ);
$purchData = ($purchRes && mysqli_num_rows($purchRes) > 0) ? mysqli_fetch_assoc($purchRes) : [];
$totalPurchases = (float)($purchData['total_purchases'] ?? 0);
$totalSupplierPayables = (float)($purchData['total_supplier_payables'] ?? 0);

// 3. Live Operating Expenses (المصروفات)
$expQ = "SELECT COALESCE(SUM(`cost`), 0) AS total_expenses 
         FROM expenditure 
         WHERE storeid = '$storeid' OR storeid = '0'";
$expRes = mysqli_query($adController->MySQL, $expQ);
$expData = ($expRes && mysqli_num_rows($expRes) > 0) ? mysqli_fetch_assoc($expRes) : [];
$totalExpenses = (float)($expData['total_expenses'] ?? 0);

// 4. Cost of Goods Sold (COGS) & Net Profit
$cogsQ = "SELECT COALESCE(SUM(quantity * cost), 0) AS total_cogs 
          FROM sales_items 
          WHERE storeid = '$storeid' OR storeid = '0'";
$cogsRes = mysqli_query($adController->MySQL, $cogsQ);
$cogsRow = ($cogsRes && mysqli_num_rows($cogsRes) > 0) ? mysqli_fetch_assoc($cogsRes) : [];
$totalCogs = (float)($cogsRow['total_cogs'] ?? 0);

if ($totalCogs > 0) {
    $netProfit = $totalSales - $totalCogs - $totalExpenses;
} else {
    // If unit costs are not fully populated in sales_items, calculate Net Operating Margin
    $netProfit = $totalSales - $totalPurchases - $totalExpenses;
}

// 5. Total Customers Count
$custQ = "SELECT COUNT(*) AS total_customers FROM customers WHERE storeid = '$storeid' OR storeid = '0'";
$custRes = mysqli_query($adController->MySQL, $custQ);
$custRow = ($custRes && mysqli_num_rows($custRes) > 0) ? mysqli_fetch_assoc($custRes) : [];
$totalCustomers = (int)($custRow['total_customers'] ?? 0);

// 6. Active Inventory Alerts Count
$alertItemsCount = 0;
$invAlertQ = "SELECT 
                COUNT(*) AS alert_count
              FROM items i
              WHERE (i.storeid = '$storeid' OR i.storeid = '0') 
                AND i.enabled = '1'
                AND (
                    (
                        COALESCE((SELECT SUM(o.quantity) FROM open_palance o WHERE o.itemid = i.id), 0) +
                        COALESCE((SELECT SUM(inc.quantity) FROM income_items inc WHERE inc.itemid = i.id AND inc.state = '1'), 0) -
                        COALESCE((SELECT SUM(outg.quantity) FROM outgo_items outg WHERE outg.itemid = i.id AND outg.state = '1'), 0)
                    ) <= CASE WHEN i.selt > 0 THEN i.selt ELSE 5 END
                    OR (i.expiry_date IS NOT NULL AND i.expiry_date != '0000-00-00' AND i.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 60 DAY))
                )";
$invAlertRes = @mysqli_query($adController->MySQL, $invAlertQ);
if ($invAlertRes && $invAlertRow = mysqli_fetch_assoc($invAlertRes)) {
    $alertItemsCount = (int)$invAlertRow['alert_count'];
}

// 7. Last 7 Days Daily Sales for Line Chart
$last7Days = [];
$arabicDays = [
    'Saturday'  => 'السبت',
    'Sunday'    => 'الأحد',
    'Monday'    => 'الإثنين',
    'Tuesday'   => 'الثلاثاء',
    'Wednesday' => 'الأربعاء',
    'Thursday'  => 'الخميس',
    'Friday'    => 'الجمعة'
];

for ($i = 6; $i >= 0; $i--) {
    $dateKey = date('Y-m-d', strtotime("-$i days"));
    $dayNameEn = date('l', strtotime("-$i days"));
    $dayNameAr = $arabicDays[$dayNameEn] ?? $dayNameEn;
    $last7Days[$dateKey] = [
        'label' => $dayNameAr,
        'total' => 0.0
    ];
}

$chart7Q = "SELECT 
                DATE(date) AS day_date, 
                SUM(all_total) AS day_total 
            FROM sales 
            WHERE date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
              AND (storeid = '$storeid' OR storeid = '0')
            GROUP BY DATE(date) 
            ORDER BY day_date ASC";
$chart7Res = mysqli_query($adController->MySQL, $chart7Q);
if ($chart7Res) {
    while ($cRow = mysqli_fetch_assoc($chart7Res)) {
        $d = $cRow['day_date'];
        if (isset($last7Days[$d])) {
            $last7Days[$d]['total'] = (float)$cRow['day_total'];
        }
    }
}

$chart7Labels = array_column(array_values($last7Days), 'label');
$chart7Values = array_column(array_values($last7Days), 'total');

// 8. Top 5 Best-Selling Products for Doughnut Chart
$topProductsLabels = [];
$topProductsValues = [];

$topPQ = "SELECT 
            i.name_ar, 
            i.name_en, 
            SUM(si.quantity) AS total_qty 
          FROM sales_items si 
          JOIN items i ON si.itemid = i.id 
          WHERE si.storeid = '$storeid' OR si.storeid = '0' 
          GROUP BY si.itemid 
          ORDER BY total_qty DESC 
          LIMIT 5";
$topPRes = mysqli_query($adController->MySQL, $topPQ);
if ($topPRes && mysqli_num_rows($topPRes) > 0) {
    while ($pRow = mysqli_fetch_assoc($topPRes)) {
        $pName = !empty($pRow['name_ar']) ? $pRow['name_ar'] : $pRow['name_en'];
        $topProductsLabels[] = $pName;
        $topProductsValues[] = (float)$pRow['total_qty'];
    }
} else {
    // Graceful default if sales_items has not yet recorded items
    $topProductsLabels = ['منتجات عامة'];
    $topProductsValues = [max(1, $totalInvoices)];
}

// 9. Recent Sales Activities (Last 5)
$recentSalesQ = "SELECT 
                    s.invoicenumber, 
                    s.date, 
                    s.all_total, 
                    s.`left`, 
                    c.name_ar AS cust_name 
                 FROM sales s 
                 LEFT JOIN customers c ON s.customer = c.id 
                 WHERE s.storeid = '$storeid' OR s.storeid = '0' 
                 ORDER BY s.date DESC 
                 LIMIT 5";
$recentSalesRes = mysqli_query($adController->MySQL, $recentSalesQ);
$recentSales = [];
if ($recentSalesRes && mysqli_num_rows($recentSalesRes) > 0) {
    while ($rRow = mysqli_fetch_assoc($recentSalesRes)) {
        $recentSales[] = $rRow;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WAM Tech POS - لوحة التحكم المالية والتشغيلية</title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        .stat-card-sublabel {
            font-size: 0.8rem;
            margin-top: 6px;
        }
        .debt-pill {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="admin-wrapper">

        <!-- Professional Sidebar -->
        <?php require_once("components/sidebar_pro.php"); ?>

        <!-- Main Content Wrapper -->
        <div style="flex: 1; display: flex; flex-direction: column;">

            <!-- Professional Navbar -->
            <?php require_once("components/header_pro.php"); ?>

            <!-- Main Content -->
            <main class="admin-content">

                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-tachometer-alt text-primary me-2"></i>
                        <?= DASHBOARD ?>
                    </h1>
                    <div class="page-breadcrumb">
                        <div class="breadcrumb-item">
                            <i class="fas fa-home"></i>
                            <span>الرئيسية</span>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <span class="text-primary">لوحة التحكم المالية والتشغيلية</span>
                        </div>
                    </div>
                </div>

                <!-- Primary KPI Financial Cards -->
                <div class="row g-3 mb-4">
                    <!-- Total Sales -->
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card">
                            <i class="fas fa-shopping-cart stat-card-icon text-primary"></i>
                            <div class="stat-card-value text-primary" id="total-sales">
                                <?= number_format($totalSales, 2) ?>
                            </div>
                            <div class="stat-card-label">إجمالي المبيعات (<?= $currency ?>)</div>
                            <div class="stat-card-sublabel text-muted">
                                <i class="fas fa-receipt me-1"></i> <strong><?= $totalInvoices ?></strong> فاتورة مبيعات
                            </div>
                        </div>
                    </div>

                    <!-- Net Profit -->
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card <?= ($netProfit >= 0) ? 'success' : 'border-danger' ?>">
                            <i class="fas fa-chart-line stat-card-icon <?= ($netProfit >= 0) ? 'text-success' : 'text-danger' ?>"></i>
                            <div class="stat-card-value <?= ($netProfit >= 0) ? 'text-success' : 'text-danger' ?>" id="total-profit">
                                <?= number_format($netProfit, 2) ?>
                            </div>
                            <div class="stat-card-label">صافي الأرباح التقديرية (<?= $currency ?>)</div>
                            <div class="stat-card-sublabel text-muted">
                                <i class="fas fa-calculator me-1"></i> بعد خصم التكاليف والمصروفات
                            </div>
                        </div>
                    </div>

                    <!-- Total Expenses -->
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card warning">
                            <i class="fas fa-wallet stat-card-icon text-warning"></i>
                            <div class="stat-card-value text-warning" id="total-expenses">
                                <?= number_format($totalExpenses, 2) ?>
                            </div>
                            <div class="stat-card-label">إجمالي المصروفات (<?= $currency ?>)</div>
                            <div class="stat-card-sublabel text-muted">
                                <a href="expenditure.php" class="text-decoration-none text-muted">
                                    <i class="fas fa-external-link-alt me-1"></i> إدارة المصروفات
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Receivables & Payables (المديونيات) -->
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card <?= ($totalCustomerDebts > 0 || $totalSupplierPayables > 0) ? 'info' : '' ?>">
                            <i class="fas fa-balance-scale stat-card-icon text-info"></i>
                            <div class="stat-card-value text-info fs-4">
                                <?= number_format($totalCustomerDebts, 2) ?>
                            </div>
                            <div class="stat-card-label">مديونيات العملاء المستحقة (<?= $currency ?>)</div>
                            <div class="stat-card-sublabel text-muted d-flex justify-content-between">
                                <span>مستحقات الموردين:</span>
                                <strong class="text-danger"><?= number_format($totalSupplierPayables, 2) ?> <?= $currency ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secondary KPI Bar (Quick Stats & Alerts) -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded border d-flex align-items-center justify-content-between shadow-sm">
                            <div>
                                <span class="text-muted small d-block">إجمالي المشتريات والتوريدات</span>
                                <h4 class="fw-bold mb-0 text-dark"><?= number_format($totalPurchases, 2) ?> <small class="fs-6"><?= $currency ?></small></h4>
                            </div>
                            <a href="purchase.php" class="btn-pro btn-pro-outline btn-pro-sm">
                                <i class="fas fa-truck"></i> المشتريات
                            </a>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded border d-flex align-items-center justify-content-between shadow-sm">
                            <div>
                                <span class="text-muted small d-block">قاعدة بيانات العملاء</span>
                                <h4 class="fw-bold mb-0 text-primary"><?= $totalCustomers ?> <small class="fs-6">عميل مسجل</small></h4>
                            </div>
                            <a href="customers.php" class="btn-pro btn-pro-outline btn-pro-sm">
                                <i class="fas fa-users"></i> العملاء
                            </a>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 rounded border d-flex align-items-center justify-content-between shadow-sm <?= ($alertItemsCount > 0) ? 'bg-warning-light border-warning' : 'bg-white' ?>">
                            <div>
                                <span class="text-muted small d-block">تنبيهات المخزون والصلاحية</span>
                                <h4 class="fw-bold mb-0 <?= ($alertItemsCount > 0) ? 'text-danger' : 'text-success' ?>">
                                    <?= $alertItemsCount ?> <small class="fs-6">تنبيه نشط</small>
                                </h4>
                            </div>
                            <a href="inventory_alerts.php" class="btn-pro <?= ($alertItemsCount > 0) ? 'btn-pro-danger' : 'btn-pro-success' ?> btn-pro-sm">
                                <i class="fas fa-bell me-1"></i> فحص التنبيهات
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Bar -->
                <div class="card-pro mb-4">
                    <div class="card-header-pro">
                        <h2 class="card-title-pro">
                            <i class="fas fa-bolt text-warning"></i>
                            الإجراءات والمعاملات السريعة
                        </h2>
                    </div>
                    <div class="card-body-pro">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="pos.php" class="btn-pro btn-pro-success">
                                <i class="fas fa-cash-register me-1"></i> نقطة البيع السريعة (POS)
                            </a>
                            <a href="add_saleInvoice.php" class="btn-pro btn-pro-primary">
                                <i class="fas fa-plus-circle me-1"></i> فاتورة بيع جديدة
                            </a>
                            <a href="add_purchase.php" class="btn-pro btn-pro-outline">
                                <i class="fas fa-truck-loading me-1"></i> فاتورة مشتريات
                            </a>
                            <a href="customer_statement.php" class="btn-pro btn-pro-outline">
                                <i class="fas fa-file-invoice-dollar me-1"></i> كشف حساب عميل
                            </a>
                            <a href="supplier_statement.php" class="btn-pro btn-pro-outline">
                                <i class="fas fa-file-invoice me-1"></i> كشف حساب مورد
                            </a>
                            <a href="inventory_alerts.php" class="btn-pro btn-pro-outline">
                                <i class="fas fa-exclamation-triangle me-1"></i> تنبيهات المخزون
                            </a>
                            <a href="backup.php" class="btn-pro btn-pro-outline">
                                <i class="fas fa-cloud-download-alt me-1"></i> النسخ الاحتياطي
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row g-3 mb-4">
                    <!-- 7 Days Sales Chart -->
                    <div class="col-lg-7">
                        <div class="card-pro">
                            <div class="card-header-pro">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="card-title-pro">
                                        <i class="fas fa-chart-line text-primary"></i>
                                        مبيعات آخر 7 أيام فعلية
                                    </h3>
                                    <span class="badge bg-light text-dark font-monospace border">
                                        <?= $currency ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body-pro">
                                <canvas id="salesChart" height="260"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Top Selling Products Doughnut Chart -->
                    <div class="col-lg-5">
                        <div class="card-pro">
                            <div class="card-header-pro">
                                <h3 class="card-title-pro">
                                    <i class="fas fa-chart-pie text-success"></i>
                                    أكثر المنتجات مبيعاً
                                </h3>
                            </div>
                            <div class="card-body-pro">
                                <canvas id="productsChart" height="260"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Card -->
                <div class="card-pro">
                    <div class="card-header-pro">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="card-title-pro">
                                <i class="fas fa-clock text-info"></i>
                                أحدث فواتير المبيعات المسجلة
                            </h2>
                            <a href="saleInvoice-report.php" class="btn-pro btn-pro-primary btn-pro-sm">
                                سجل المبيعات الكامل
                                <i class="fas fa-arrow-left ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body-pro p-0">
                        <div class="table-responsive">
                            <table class="table-pro mb-0">
                                <thead>
                                    <tr>
                                        <th>رقم الفاتورة</th>
                                        <th>التاريخ والوقت</th>
                                        <th>العميل</th>
                                        <th class="text-center">إجمالي الفاتورة</th>
                                        <th class="text-center">المتبقي (آجل)</th>
                                        <th class="text-center">الحالة</th>
                                        <th class="text-center">عرض</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentSales)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                لا توجد عمليات بيع مسجلة حتى الآن
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentSales as $rSale): ?>
                                            <tr>
                                                <td>
                                                    <span class="font-monospace fw-bold text-primary">
                                                        #<?= htmlspecialchars($rSale['invoicenumber']) ?>
                                                    </span>
                                                </td>
                                                <td class="small text-muted font-monospace"><?= htmlspecialchars($rSale['date']) ?></td>
                                                <td class="fw-semibold">
                                                    <?= htmlspecialchars($rSale['cust_name'] ?: 'عميل نقدي') ?>
                                                </td>
                                                <td class="text-center fw-bold text-success">
                                                    <?= number_format((float)$rSale['all_total'], 2) ?> <?= $currency ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ((float)$rSale['left'] > 0): ?>
                                                        <span class="badge bg-danger fs-6 px-2 py-1">
                                                            <?= number_format((float)$rSale['left'], 2) ?> <?= $currency ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-success small fw-semibold">0.00 (مسددة)</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ((float)$rSale['left'] <= 0): ?>
                                                        <span class="badge-pro badge-pro-success">مكتمل</span>
                                                    <?php else: ?>
                                                        <span class="badge-pro badge-pro-warning">آجل</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="saleInvoice.php?invoice=<?= urlencode($rSale['invoicenumber']) ?>" class="btn-pro btn-pro-outline btn-pro-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Professional Footer -->
                <?php require_once("components/footer_pro.php"); ?>

            </main>

        </div>

    </div>

    <!-- Scripts -->
    <?php require_once("include.php"); ?>
    <script src="js/admin-pro.js"></script>

    <!-- Chart.js for Dashboard Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Real Live 7 Days Sales Line Chart
        const salesCtx = document.getElementById('salesChart');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($chart7Labels, JSON_UNESCAPED_UNICODE) ?>,
                    datasets: [{
                        label: 'المبيعات (<?= $currency ?>)',
                        data: <?= json_encode($chart7Values) ?>,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) { return value.toLocaleString('ar-EG') + ' <?= $currency ?>'; }
                            }
                        }
                    }
                }
            });
        }

        // Real Top Products Doughnut Chart
        const productsCtx = document.getElementById('productsChart');
        if (productsCtx) {
            new Chart(productsCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($topProductsLabels, JSON_UNESCAPED_UNICODE) ?>,
                    datasets: [{
                        data: <?= json_encode($topProductsValues) ?>,
                        backgroundColor: [
                            'rgb(59, 130, 246)',
                            'rgb(16, 185, 129)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)',
                            'rgb(6, 182, 212)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    </script>
</body>

</html>
