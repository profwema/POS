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
  <title>WAM Tech POS - Professional Dashboard</title>

  <!-- Base Styles -->
  <?php require_once("header.php"); ?>

  <!-- Professional Admin CSS -->
  <link href="css/admin-pro.css" rel="stylesheet">
</head>

<body>

  <div class="admin-wrapper">

    <!-- Sidebar -->
    <?php require_once("components/sidebar_pro.php"); ?>

    <!-- Main Content Wrapper -->
    <div style="flex: 1; display: flex; flex-direction: column;">

      <!-- Navbar -->
      <?php require_once("components/header_pro.php"); ?>

      <!-- Main Content -->
      <main class="admin-content">

        <!-- Page Header -->
        <div class="page-header">
          <h1 class="page-title">
            <i class="fas fa-tachometer-alt text-primary me-2"></i>
            لوحة التحكم
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

        <!-- Stats Cards Row -->
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <div class="stat-card">
              <i class="fas fa-shopping-cart stat-card-icon"></i>
              <div class="stat-card-value">42,350</div>
              <div class="stat-card-label">إجمالي المبيعات (ريال)</div>
              <div class="mt-2 small">
                <i class="fas fa-arrow-up me-1"></i> +12.5% من الشهر الماضي
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card success">
              <i class="fas fa-chart-line stat-card-icon"></i>
              <div class="stat-card-value">18,750</div>
              <div class="stat-card-label">صافي الربح (ريال)</div>
              <div class="mt-2 small">
                <i class="fas fa-arrow-up me-1"></i> +8.3% من الشهر الماضي
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card warning">
              <i class="fas fa-file-invoice stat-card-icon"></i>
              <div class="stat-card-value">1,256</div>
              <div class="stat-card-label">عدد الفواتير</div>
              <div class="mt-2 small">
                <i class="fas fa-arrow-up me-1"></i> +5.2% من الشهر الماضي
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card danger">
              <i class="fas fa-users stat-card-icon"></i>
              <div class="stat-card-value">342</div>
              <div class="stat-card-label">عدد العملاء</div>
              <div class="mt-2 small">
                <i class="fas fa-arrow-up me-1"></i> +15 عميل جديد
              </div>
            </div>
          </div>
        </div>

        <!-- Cards Row -->
        <div class="row g-3 mb-4">
          <!-- Recent Orders Card -->
          <div class="col-lg-8">
            <div class="card-pro">
              <div class="card-header-pro">
                <div class="d-flex justify-content-between align-items-center">
                  <h2 class="card-title-pro">
                    <i class="fas fa-shopping-bag"></i>
                    آخر الطلبات
                  </h2>
                  <a href="saleInvoice.php" class="btn-pro btn-pro-primary btn-pro-sm">
                    عرض الكل
                    <i class="fas fa-arrow-left"></i>
                  </a>
                </div>
              </div>
              <div class="card-body-pro">
                <div class="table-responsive">
                  <table class="table-pro">
                    <thead>
                      <tr>
                        <th>رقم الفاتورة</th>
                        <th>العميل</th>
                        <th>التاريخ</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                        <th class="text-center">الإجراءات</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><strong>#INV-12345</strong></td>
                        <td>أحمد محمد</td>
                        <td>
                          <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            منذ 5 دقائق
                          </small>
                        </td>
                        <td><strong>1,250 ريال</strong></td>
                        <td><span class="badge-pro badge-pro-success">مكتمل</span></td>
                        <td class="text-center">
                          <button class="btn-pro btn-pro-outline btn-pro-sm">
                            <i class="fas fa-eye"></i>
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td><strong>#INV-12344</strong></td>
                        <td>فاطمة علي</td>
                        <td>
                          <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            منذ 15 دقيقة
                          </small>
                        </td>
                        <td><strong>890 ريال</strong></td>
                        <td><span class="badge-pro badge-pro-warning">قيد المعالجة</span></td>
                        <td class="text-center">
                          <button class="btn-pro btn-pro-outline btn-pro-sm">
                            <i class="fas fa-eye"></i>
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td><strong>#INV-12343</strong></td>
                        <td>خالد سعيد</td>
                        <td>
                          <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            منذ 30 دقيقة
                          </small>
                        </td>
                        <td><strong>2,150 ريال</strong></td>
                        <td><span class="badge-pro badge-pro-success">مكتمل</span></td>
                        <td class="text-center">
                          <button class="btn-pro btn-pro-outline btn-pro-sm">
                            <i class="fas fa-eye"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions Card -->
          <div class="col-lg-4">
            <div class="card-pro">
              <div class="card-header-pro">
                <h2 class="card-title-pro">
                  <i class="fas fa-bolt"></i>
                  إجراءات سريعة
                </h2>
              </div>
              <div class="card-body-pro">
                <div class="d-flex flex-column gap-2">
                  <a href="add_saleInvoice.php" class="btn-pro btn-pro-primary">
                    <i class="fas fa-plus-circle"></i>
                    فاتورة مبيعات جديدة
                  </a>
                  <a href="add_purchase.php" class="btn-pro btn-pro-success">
                    <i class="fas fa-shopping-bag"></i>
                    فاتورة مشتريات جديدة
                  </a>
                  <a href="add_customer.php" class="btn-pro btn-pro-outline">
                    <i class="fas fa-user-plus"></i>
                    إضافة عميل جديد
                  </a>
                  <a href="add_item.php" class="btn-pro btn-pro-outline">
                    <i class="fas fa-box"></i>
                    إضافة منتج جديد
                  </a>
                </div>
              </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="card-pro mt-3">
              <div class="card-header-pro">
                <h2 class="card-title-pro">
                  <i class="fas fa-exclamation-triangle text-warning"></i>
                  تنبيهات المخزون
                </h2>
              </div>
              <div class="card-body-pro">
                <div class="d-flex flex-column gap-3">
                  <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning-light text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                      <i class="fas fa-cube"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="fw-semibold">منتج A</div>
                      <div class="text-muted small">الكمية المتبقية: 5</div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-3">
                    <div class="bg-danger-light text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                      <i class="fas fa-cube"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="fw-semibold">منتج B</div>
                      <div class="text-muted small">الكمية المتبقية: 2</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer-pro">
                <a href="store-inventory.php" class="text-decoration-none small">
                  عرض جميع المنتجات
                  <i class="fas fa-arrow-left ms-1"></i>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Welcome Message Card -->
        <div class="card-pro">
          <div class="card-body-pro">
            <div class="d-flex align-items-center gap-4">
              <div class="flex-shrink-0">
                <div class="bg-primary-lighter text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                  <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                </div>
              </div>
              <div class="flex-grow-1">
                <h3 class="mb-2">🎉 مرحباً بك في التصميم الاحترافي الجديد!</h3>
                <p class="text-muted mb-3">
                  تم تطوير لوحة التحكم بتصميم عصري ومحترف مستوحى من أفضل Dashboard Templates العالمية.
                  التصميم يتضمن: نظام ألوان متناسق، مكونات قابلة لإعادة الاستخدام، Dark/Light Mode، وتصميم Responsive كامل.
                </p>
                <div class="d-flex gap-2">
                  <button class="btn-pro btn-pro-primary">
                    <i class="fas fa-rocket me-1"></i>
                    ابدأ الآن
                  </button>
                  <button class="btn-pro btn-pro-outline">
                    <i class="fas fa-book me-1"></i>
                    قراءة المزيد
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <?php require_once("components/footer_pro.php"); ?>

      </main>

    </div>

  </div>

  <!-- Scripts -->
  <?php require_once("include.php"); ?>
  <script src="js/admin-pro.js"></script>

</body>

</html>