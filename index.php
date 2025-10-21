<?php
//print("<script>location.replace('sales-report.php');</script>");
//die;
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>" dir="<?php echo ($_SESSION['lang'] == 'ar') ? 'rtl' : 'ltr'; ?>">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
	<?php require_once("header.php");?>
	<link href="css/dashboard-modern.css" rel="stylesheet">
		
</head>

<body>
		<?php require_once("header_top.php");?>
    
    <div class="container-fluid-full">
        <div class="row-fluid">				
			<?php require_once("left_menu.php");?>
			
            <div id="content" class="span10">
                <!-- Welcome Section -->
                <div class="welcome-section" style="margin-bottom: 2rem;">
                    <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">
                        <i class="fa fa-dashboard" style="color: #6366f1;"></i> 
                        <?=DASHBOARD?>
                    </h1>
                    <p style="color: #64748b; font-size: 1rem;">
                        مرحباً بك، <?=$_SESSION['name']?> - نظرة عامة على أداء نظام نقاط البيع
                    </p>
                </div>

                <!-- Statistics Cards -->
                <div class="dashboard-stats">
                    <div class="stat-card primary slide-up">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="fa fa-shopping-cart"></i>
                            </div>
                        </div>
                        <div class="stat-card-body">
                            <h3>إجمالي المبيعات</h3>
                            <div class="stat-card-value">
                                <span id="total-sales">0</span>
                                <small>ريال</small>
                            </div>
                            <div class="stat-card-footer">
                                <span class="stat-trend up">
                                    <i class="fa fa-arrow-up"></i> 12.5%
                                </span>
                                <span class="stat-period">مقارنة بالشهر الماضي</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card success slide-up" style="animation-delay: 0.1s;">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="fa fa-line-chart"></i>
                            </div>
                        </div>
                        <div class="stat-card-body">
                            <h3>صافي الربح</h3>
                            <div class="stat-card-value">
                                <span id="total-profit">0</span>
                                <small>ريال</small>
                            </div>
                            <div class="stat-card-footer">
                                <span class="stat-trend up">
                                    <i class="fa fa-arrow-up"></i> 8.3%
                                </span>
                                <span class="stat-period">مقارنة بالشهر الماضي</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card warning slide-up" style="animation-delay: 0.2s;">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="fa fa-file-text"></i>
                            </div>
                        </div>
                        <div class="stat-card-body">
                            <h3>عدد الفواتير</h3>
                            <div class="stat-card-value">
                                <span id="total-invoices">0</span>
                                <small>فاتورة</small>
                            </div>
                            <div class="stat-card-footer">
                                <span class="stat-trend up">
                                    <i class="fa fa-arrow-up"></i> 5.2%
                                </span>
                                <span class="stat-period">مقارنة بالشهر الماضي</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card info slide-up" style="animation-delay: 0.3s;">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-card-body">
                            <h3>العملاء الجدد</h3>
                            <div class="stat-card-value">
                                <span id="new-customers">0</span>
                                <small>عميل</small>
                            </div>
                            <div class="stat-card-footer">
                                <span class="stat-trend up">
                                    <i class="fa fa-arrow-up"></i> 15.7%
                                </span>
                                <span class="stat-period">مقارنة بالشهر الماضي</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="box" style="margin-bottom: 2rem;">
                    <div class="box-header">
                        <h2>
                            <i class="fa fa-bolt"></i> 
                            إجراءات سريعة
                        </h2>
                    </div>
                    <div class="box-content">
                        <div class="quick-actions">
                            <a href="add_saleInvoice.php" class="quick-action-btn">
                                <i class="fa fa-plus-circle"></i>
                                <span>فاتورة مبيعات جديدة</span>
                            </a>
                            <a href="add_purchase.php" class="quick-action-btn">
                                <i class="fa fa-shopping-bag"></i>
                                <span>فاتورة مشتريات</span>
                            </a>
                            <a href="add_customer.php" class="quick-action-btn">
                                <i class="fa fa-user-plus"></i>
                                <span>إضافة عميل جديد</span>
                            </a>
                            <a href="add_item.php" class="quick-action-btn">
                                <i class="fa fa-cube"></i>
                                <span>إضافة منتج</span>
                            </a>
                            <a href="saleInvoice-report.php" class="quick-action-btn">
                                <i class="fa fa-bar-chart"></i>
                                <span>تقرير المبيعات</span>
                            </a>
                            <a href="store-inventory.php" class="quick-action-btn">
                                <i class="fa fa-warehouse"></i>
                                <span>جرد المخزون</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row-fluid">
                    <div class="span6">
                        <div class="chart-card slide-up">
                            <div class="chart-card-header">
                                <h3 class="chart-card-title">
                                    <i class="fa fa-line-chart"></i>
                                    مبيعات آخر 7 أيام
                                </h3>
                                <div class="chart-card-actions">
                                    <button class="chart-filter-btn active">أسبوع</button>
                                    <button class="chart-filter-btn">شهر</button>
                                </div>
                            </div>
                            <canvas id="salesChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="span6">
                        <div class="chart-card slide-up" style="animation-delay: 0.1s;">
                            <div class="chart-card-header">
                                <h3 class="chart-card-title">
                                    <i class="fa fa-pie-chart"></i>
                                    أفضل المنتجات مبيعاً
                                </h3>
                            </div>
                            <canvas id="productsChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="box slide-up" style="animation-delay: 0.2s;">
                    <div class="box-header">
                        <h2>
                            <i class="fa fa-clock-o"></i> 
                            النشاطات الأخيرة
                        </h2>
                    </div>
                    <div class="box-content">
                        <ul class="activity-list">
                            <li class="activity-item">
                                <div class="activity-icon success">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">تم إتمام عملية بيع جديدة</div>
                                    <div class="activity-description">فاتورة رقم #12345 بقيمة 2,500 ريال</div>
                                </div>
                                <div class="activity-time">منذ 5 دقائق</div>
                            </li>
                            <li class="activity-item">
                                <div class="activity-icon primary">
                                    <i class="fa fa-user"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">تم إضافة عميل جديد</div>
                                    <div class="activity-description">أحمد محمد - 0501234567</div>
                                </div>
                                <div class="activity-time">منذ 15 دقيقة</div>
                            </li>
                            <li class="activity-item">
                                <div class="activity-icon warning">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">تنبيه: مخزون منخفض</div>
                                    <div class="activity-description">منتج (كيبورد لوجيتك) متبقي 5 وحدات فقط</div>
                                </div>
                                <div class="activity-time">منذ 30 دقيقة</div>
                            </li>
                            <li class="activity-item">
                                <div class="activity-icon success">
                                    <i class="fa fa-shopping-cart"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">تم استلام شحنة جديدة</div>
                                    <div class="activity-description">فاتورة شراء #8765 - 50 منتج</div>
                                </div>
                                <div class="activity-time">منذ ساعة</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>       
    <div class="clearfix"></div>
    
	
	<?php require_once("footer.php");?>
</body>
</html>
