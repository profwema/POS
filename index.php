<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WAM Tech POS - <?= DASHBOARD ?></title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
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
                            <span class="text-primary">لوحة التحكم</span>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="fas fa-shopping-cart stat-card-icon"></i>
                            <div class="stat-card-value" id="total-sales">0</div>
                            <div class="stat-card-label">إجمالي المبيعات (ريال)</div>
                            <div class="mt-2 small">
                                <i class="fas fa-arrow-up me-1"></i> +12.5% من الشهر الماضي
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card success">
                            <i class="fas fa-chart-line stat-card-icon"></i>
                            <div class="stat-card-value" id="total-profit">0</div>
                            <div class="stat-card-label">صافي الربح (ريال)</div>
                            <div class="mt-2 small">
                                <i class="fas fa-arrow-up me-1"></i> +8.3% من الشهر الماضي
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card warning">
                            <i class="fas fa-file-invoice stat-card-icon"></i>
                            <div class="stat-card-value" id="total-invoices">0</div>
                            <div class="stat-card-label">عدد الفواتير</div>
                            <div class="mt-2 small">
                                <i class="fas fa-arrow-up me-1"></i> +5.2% من الشهر الماضي
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card info">
                            <i class="fas fa-users stat-card-icon"></i>
                            <div class="stat-card-value" id="new-customers">0</div>
                            <div class="stat-card-label">العملاء الجدد</div>
                            <div class="mt-2 small">
                                <i class="fas fa-arrow-up me-1"></i> +15.7% من الشهر الماضي
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="card-pro">
                    <div class="card-header-pro">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="card-title-pro">
                                <i class="fas fa-bolt"></i>
                                إجراءات سريعة
                            </h2>
                        </div>
                    </div>
                    <div class="card-body-pro">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="add_saleInvoice.php" class="btn-pro btn-pro-primary">
                                <i class="fas fa-plus-circle me-1"></i>
                                فاتورة مبيعات جديدة
                            </a>
                            <a href="add_purchase.php" class="btn-pro btn-pro-success">
                                <i class="fas fa-shopping-bag me-1"></i>
                                فاتورة مشتريات
                            </a>
                            <a href="add_customer.php" class="btn-pro btn-pro-outline">
                                <i class="fas fa-user-plus me-1"></i>
                                إضافة عميل جديد
                            </a>
                            <a href="add_item.php" class="btn-pro btn-pro-outline">
                                <i class="fas fa-cube me-1"></i>
                                إضافة منتج
                            </a>
                            <a href="saleInvoice-report.php" class="btn-pro btn-pro-outline">
                                <i class="fas fa-chart-bar me-1"></i>
                                تقرير المبيعات
                            </a>
                            <a href="store-inventory.php" class="btn-pro btn-pro-outline">
                                <i class="fas fa-warehouse me-1"></i>
                                جرد المخزون
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row g-3 mb-4 mt-4">
                    <div class="col-lg-6">
                        <div class="card-pro">
                            <div class="card-header-pro">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="card-title-pro">
                                        <i class="fas fa-chart-line text-primary"></i>
                                        مبيعات آخر 7 أيام
                                    </h3>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn-pro btn-pro-primary btn-pro-sm">أسبوع</button>
                                        <button class="btn-pro btn-pro-outline btn-pro-sm">شهر</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body-pro">
                                <canvas id="salesChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card-pro">
                            <div class="card-header-pro">
                                <h3 class="card-title-pro">
                                    <i class="fas fa-chart-pie text-success"></i>
                                    أفضل المنتجات مبيعاً
                                </h3>
                            </div>
                            <div class="card-body-pro">
                                <canvas id="productsChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Card -->
                <div class="card-pro">
                    <div class="card-header-pro">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="card-title-pro">
                                <i class="fas fa-clock"></i>
                                النشاطات الأخيرة
                            </h2>
                            <a href="#" class="btn-pro btn-pro-primary btn-pro-sm">
                                عرض الكل
                                <i class="fas fa-arrow-left ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body-pro">
                        <ul class="list-unstyled mb-0">
                            <li class="border-bottom py-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-success-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <div class="fw-semibold">تم إتمام عملية بيع جديدة</div>
                                            <span class="badge-pro badge-pro-success">مكتمل</span>
                                        </div>
                                        <div class="text-muted small">فاتورة رقم <span class="fw-semibold">#12345</span> بقيمة <span class="fw-semibold text-success">2,500 ريال</span></div>
                                        <div class="text-muted small mt-1">
                                            <i class="fas fa-clock me-1"></i> منذ 5 دقائق
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="border-bottom py-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary-lighter text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <div class="fw-semibold">تم إضافة عميل جديد</div>
                                            <span class="badge-pro badge-pro-info">جديد</span>
                                        </div>
                                        <div class="text-muted small">أحمد محمد - <span class="fw-semibold">0501234567</span></div>
                                        <div class="text-muted small mt-1">
                                            <i class="fas fa-clock me-1"></i> منذ 15 دقيقة
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="border-bottom py-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-warning-light text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <div class="fw-semibold">تنبيه: مخزون منخفض</div>
                                            <span class="badge-pro badge-pro-warning">تحذير</span>
                                        </div>
                                        <div class="text-muted small">منتج (كيبورد لوجيتك) متبقي <span class="fw-semibold text-warning">5 وحدات</span> فقط</div>
                                        <div class="text-muted small mt-1">
                                            <i class="fas fa-clock me-1"></i> منذ 30 دقيقة
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="py-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-success-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <div class="fw-semibold">تم استلام شحنة جديدة</div>
                                            <span class="badge-pro badge-pro-success">مستلم</span>
                                        </div>
                                        <div class="text-muted small">فاتورة شراء <span class="fw-semibold">#8765</span> - <span class="fw-semibold">50 منتج</span></div>
                                        <div class="text-muted small mt-1">
                                            <i class="fas fa-clock me-1"></i> منذ ساعة
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
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
        // Sales Chart
        const salesCtx = document.getElementById('salesChart');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
                    datasets: [{
                        label: 'المبيعات (ريال)',
                        data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Products Chart
        const productsCtx = document.getElementById('productsChart');
        if (productsCtx) {
            new Chart(productsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['منتج A', 'منتج B', 'منتج C', 'منتج D', 'منتج E'],
                    datasets: [{
                        data: [300, 250, 200, 150, 100],
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
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Animate counters
        function animateCounter(id, target) {
            const element = document.getElementById(id);
            if (!element) return;

            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current).toLocaleString('ar-SA');
            }, 20);
        }

        // Initialize counters after page load
        window.addEventListener('load', () => {
            animateCounter('total-sales', 42350);
            animateCounter('total-profit', 18750);
            animateCounter('total-invoices', 1256);
            animateCounter('new-customers', 342);
        });
    </script>
</body>

</html>