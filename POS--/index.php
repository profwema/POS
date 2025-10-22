<?php
//print("<script>location.replace('sales-report.php');</script>");
//die;
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
require_once("dashboard_stats.php");

// Get all statistics
$totalSales = getTotalSales($MySQL);
$totalPurchases = getTotalPurchases($MySQL);
$totalCustomers = getTotalCustomers($MySQL);
$totalSuppliers = getTotalSuppliers($MySQL);
$totalItems = getTotalItems($MySQL);
$totalEmployees = getTotalEmployees($MySQL);
$todaySales = getTodaySales($MySQL);
$todaySalesCount = getTodaySalesCount($MySQL);
$monthSales = getMonthSales($MySQL);
$monthPurchases = getMonthPurchases($MySQL);
$lowStockItems = getLowStockItems($MySQL);
$pendingQuotations = getPendingQuotations($MySQL);
$totalProfit = getTotalProfit($MySQL);
$recentSales = getRecentSales($MySQL);
$topSellingItems = getTopSellingItems($MySQL);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <!-- start: Meta -->
    <meta charset="utf-8">
    <title>WAM Tech Soft</title>
    <?php require_once("header.php"); ?>

</head>

<body>
    <?php require_once("header_top.php"); ?>

    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php"); ?>

            <div id="content" class="span10">
                <div>
                    <!-- Dashboard Header -->
                    <div class="box span12">
                        <div class="box-header" data-original-title>
                            <h2><i class="halflings-icon dashboard"></i><span class="break"></span>
                                <a href="#"><?= DASHBOARD ?></a>
                            </h2>
                        </div>
                    </div>

                    <!-- Statistics Cards Row 1 -->
                    <div class="row-fluid">
                        <!-- Total Sales Card -->
                        <div class="span4">
                            <div class="dashboard-stat-card stat-card-green">
                                <div class="stat-icon">
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-title">إجمالي المبيعات</div>
                                    <div class="stat-value"><?php echo $totalSales; ?></div>
                                    <div class="stat-description">Total Sales Amount</div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Purchases Card -->
                        <div class="span4">
                            <div class="dashboard-stat-card stat-card-blue">
                                <div class="stat-icon">
                                    <i class="fa fa-shopping-cart"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-title">إجمالي المشتريات</div>
                                    <div class="stat-value"><?php echo $totalPurchases; ?></div>
                                    <div class="stat-description">Total Purchases Amount</div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Profit Card -->
                        <div class="span4">
                            <div class="dashboard-stat-card stat-card-orange">
                                <div class="stat-icon">
                                    <i class="fa fa-line-chart"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-title">صافي الأرباح</div>
                                    <div class="stat-value"><?php echo $totalProfit; ?></div>
                                    <div class="stat-description">Net Profit</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Cards Row 2 -->
                    <div class="row-fluid">
                        <!-- Today's Sales Card -->
                        <div class="span3">
                            <div class="dashboard-stat-card stat-card-teal">
                                <div class="stat-icon">
                                    <i class="fa fa-calendar-check-o"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-title">مبيعات اليوم</div>
                                    <div class="stat-value"><?php echo $todaySales; ?></div>
                                    <div class="stat-description"><?php echo $todaySalesCount; ?> معاملة</div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Customers Card -->
                        <div class="span3">
                            <div class="dashboard-stat-card stat-card-purple">
                                <div class="stat-icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-title">العملاء</div>
                                    <div class="stat-value"><?php echo $totalCustomers; ?></div>
                                    <div class="stat-description">Total Customers</div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Items Card -->
                        <div class="span3">
                            <div class="dashboard-stat-card stat-card-blue">
                                <div class="stat-icon">
                                    <i class="fa fa-cubes"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-title">المنتجات</div>
                                    <div class="stat-value"><?php echo $totalItems; ?></div>
                                    <div class="stat-description">Total Items</div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Suppliers Card -->
                        <div class="span3">
                            <div class="dashboard-stat-card stat-card-red">
                                <div class="stat-icon">
                                    <i class="fa fa-truck"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-title">الموردون</div>
                                    <div class="stat-value"><?php echo $totalSuppliers; ?></div>
                                    <div class="stat-description">Total Suppliers</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alerts Row -->
                    <div class="row-fluid">
                        <!-- Low Stock Alert Card -->
                        <div class="span6">
                            <div class="dashboard-stat-card stat-card-red">
                                <div class="stat-icon">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-title">تنبيه المخزون المنخفض</div>
                                    <div class="stat-value"><?php echo $lowStockItems; ?></div>
                                    <div class="stat-description">منتجات تحتاج إعادة طلب</div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Quotations Card -->
                        <div class="span6">
                            <div class="dashboard-stat-card stat-card-orange">
                                <div class="stat-icon">
                                    <i class="fa fa-file-text-o"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-title">عروض الأسعار المعلقة</div>
                                    <div class="stat-value"><?php echo $pendingQuotations; ?></div>
                                    <div class="stat-description">Pending Quotations</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Section -->
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="box">
                                <div class="box-header">
                                    <h2><i class="fa fa-bolt"></i> اختصارات سريعة</h2>
                                </div>
                                <div class="box-content">
                                    <div class="row-fluid">
                                        <div class="span2">
                                            <a href="add_saleInvoice.php" class="quick-action-btn bg-green">
                                                <i class="fa fa-plus-circle"></i>
                                                <span>فاتورة مبيعات جديدة</span>
                                            </a>
                                        </div>
                                        <div class="span2">
                                            <a href="add_purchase.php" class="quick-action-btn bg-blue">
                                                <i class="fa fa-shopping-cart"></i>
                                                <span>فاتورة شراء جديدة</span>
                                            </a>
                                        </div>
                                        <div class="span2">
                                            <a href="add_customer.php" class="quick-action-btn bg-purple">
                                                <i class="fa fa-user-plus"></i>
                                                <span>إضافة عميل</span>
                                            </a>
                                        </div>
                                        <div class="span2">
                                            <a href="add_item.php" class="quick-action-btn bg-orange">
                                                <i class="fa fa-cube"></i>
                                                <span>إضافة منتج</span>
                                            </a>
                                        </div>
                                        <div class="span2">
                                            <a href="store-inventory.php" class="quick-action-btn bg-blue-sky">
                                                <i class="fa fa-archive"></i>
                                                <span>جرد المخزون</span>
                                            </a>
                                        </div>
                                        <div class="span2">
                                            <a href="saleInvoice-report.php" class="quick-action-btn bg-red">
                                                <i class="fa fa-bar-chart"></i>
                                                <span>تقارير المبيعات</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity and Top Items Row -->
                    <div class="row-fluid">
                        <!-- Recent Sales -->
                        <div class="span6">
                            <div class="recent-activity">
                                <h3 style="margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                                    <i class="fa fa-history"></i> آخر المبيعات
                                </h3>
                                <?php if (!empty($recentSales)): ?>
                                    <?php foreach ($recentSales as $sale): ?>
                                        <div class="activity-item">
                                            <div class="activity-icon" style="background-color: #1ABB9C; color: white;">
                                                <i class="fa fa-shopping-bag"></i>
                                            </div>
                                            <div class="activity-content">
                                                <div class="activity-title">
                                                    فاتورة #<?php echo $sale['invoiceNo']; ?> -
                                                    <?php echo $sale['custname'] ? $sale['custname'] : 'عميل نقدي'; ?>
                                                </div>
                                                <div class="activity-time">
                                                    <strong><?php echo number_format($sale['totalNet'], 2); ?></strong> -
                                                    <?php echo date('d/m/Y H:i', strtotime($sale['date'])); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p style="text-align: center; color: #999; padding: 20px;">لا توجد مبيعات حديثة</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Top Selling Items -->
                        <div class="span6">
                            <div class="recent-activity">
                                <h3 style="margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                                    <i class="fa fa-star"></i> المنتجات الأكثر مبيعاً
                                </h3>
                                <?php if (!empty($topSellingItems)): ?>
                                    <?php foreach ($topSellingItems as $item): ?>
                                        <div class="activity-item">
                                            <div class="activity-icon" style="background-color: #F39C12; color: white;">
                                                <i class="fa fa-cube"></i>
                                            </div>
                                            <div class="activity-content">
                                                <div class="activity-title">
                                                    <?php echo $item['item_name']; ?>
                                                </div>
                                                <div class="activity-time">
                                                    الكمية: <strong><?php echo $item['total_quantity']; ?></strong> -
                                                    المبيعات: <strong><?php echo number_format($item['total_sales'], 2); ?></strong>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p style="text-align: center; color: #999; padding: 20px;">لا توجد بيانات</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Overview -->
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="chart-container">
                                <div class="chart-header">
                                    <i class="fa fa-calendar"></i> نظرة عامة على الشهر الحالي
                                </div>
                                <div class="row-fluid" style="margin-top: 20px;">
                                    <div class="span6">
                                        <div style="padding: 20px; background: #f8f9fa; border-radius: 6px; text-align: center;">
                                            <i class="fa fa-money" style="font-size: 30px; color: #1ABB9C; margin-bottom: 10px;"></i>
                                            <h4 style="margin: 10px 0;">مبيعات الشهر</h4>
                                            <h2 style="color: #1ABB9C; margin: 5px 0;"><?php echo $monthSales; ?></h2>
                                        </div>
                                    </div>
                                    <div class="span6">
                                        <div style="padding: 20px; background: #f8f9fa; border-radius: 6px; text-align: center;">
                                            <i class="fa fa-shopping-cart" style="font-size: 30px; color: #3498DB; margin-bottom: 10px;"></i>
                                            <h4 style="margin: 10px 0;">مشتريات الشهر</h4>
                                            <h2 style="color: #3498DB; margin: 5px 0;"><?php echo $monthPurchases; ?></h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>


    <?php require_once("footer.php"); ?>
</body>

</html>