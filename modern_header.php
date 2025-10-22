<!DOCTYPE html>
<html lang="ar" dir="rtl" data-bs-theme="light">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo isset($page_title) ? $page_title : 'WAM Tech POS System'; ?></title>

  <!-- ═══════════════════════════════════════════════════════
         أحدث المكتبات - Latest Libraries (October 2025)
         ═══════════════════════════════════════════════════════ -->

  <!-- Bootstrap 5.3.2 RTL - أحدث إصدار مع دعم RTL مدمج -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet"
    integrity="sha384-nU14brUcp6StFntEOOEBvcJm4huWjB0OcIeQ3fltAfSmuZFrkAif0T+UtNGlKKQv" crossorigin="anonymous">

  <!-- Font Awesome 6.5.1 - أحدث إصدار -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Google Fonts - Cairo (أفضل خط عربي عصري) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">

  <!-- Chart.js 4.4.0 - للرسوم البيانية -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">

  <!-- Animate.css 4.1.1 - للرسوم المتحركة -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

  <!-- Our Modern Admin RTL CSS -->
  <link href="css/modern-admin-rtl.css" rel="stylesheet">

  <!-- Dark Mode CSS (if exists) -->
  <?php if (file_exists('css/dark-mode.css')): ?>
    <link href="css/dark-mode.css" rel="stylesheet">
  <?php endif; ?>

  <!-- Custom Theme Colors (optional) -->
  <style>
    /* يمكن تخصيص الألوان هنا لكل صفحة */
    <?php if (isset($custom_primary_color)): ?> :root {
      --admin-primary: <?php echo $custom_primary_color; ?>;
    }

    <?php endif; ?>
  </style>

  <!-- Additional Page-Specific CSS -->
  <?php if (isset($additional_css)): ?>
    <?php foreach ($additional_css as $css_file): ?>
      <link href="<?php echo $css_file; ?>" rel="stylesheet">
    <?php endforeach; ?>
  <?php endif; ?>
</head>

<body>

  <!-- ═══════════════════════════════════════════════════════
     Modern Admin Navbar
     ═══════════════════════════════════════════════════════ -->
  <nav class="admin-navbar">
    <div class="container-fluid">
      <div class="d-flex align-items-center justify-content-between w-100">
        <!-- Brand -->
        <a href="index.php" class="admin-navbar-brand">
          <img src="img/logo.png" alt="Logo">
          <span>WAM Tech POS</span>
        </a>

        <!-- Navbar Items -->
        <div class="admin-navbar-nav">
          <!-- Search (Optional) -->
          <div class="admin-nav-item d-none d-md-block">
            <div class="position-relative">
              <input type="search" class="form-control form-control-sm"
                placeholder="بحث..." style="width: 250px; padding-right: 35px;">
              <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-2 text-muted"></i>
            </div>
          </div>

          <!-- Notifications -->
          <div class="admin-nav-item dropdown">
            <a href="#" class="admin-nav-link" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-bell"></i>
              <span class="admin-nav-badge">5</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end admin-dropdown-menu">
              <div class="admin-dropdown-header">
                لديك 5 إشعارات جديدة
              </div>
              <a href="#" class="admin-dropdown-item">
                <div class="admin-dropdown-icon bg-primary bg-opacity-10 text-primary">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="fw-semibold">طلب جديد #1234</div>
                  <small class="text-muted">منذ دقيقتين</small>
                </div>
              </a>
              <a href="#" class="admin-dropdown-item">
                <div class="admin-dropdown-icon bg-success bg-opacity-10 text-success">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="fw-semibold">تم إتمام عملية بيع</div>
                  <small class="text-muted">منذ 5 دقائق</small>
                </div>
              </a>
              <a href="#" class="admin-dropdown-item">
                <div class="admin-dropdown-icon bg-warning bg-opacity-10 text-warning">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="fw-semibold">تنبيه مخزون منخفض</div>
                  <small class="text-muted">منذ 15 دقيقة</small>
                </div>
              </a>
              <div class="admin-dropdown-footer">
                <a href="#">عرض جميع الإشعارات</a>
              </div>
            </div>
          </div>

          <!-- Messages -->
          <div class="admin-nav-item dropdown">
            <a href="#" class="admin-nav-link" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-envelope"></i>
              <span class="admin-nav-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end admin-dropdown-menu">
              <div class="admin-dropdown-header">
                لديك 3 رسائل جديدة
              </div>
              <a href="#" class="admin-dropdown-item">
                <div class="admin-dropdown-icon">
                  <img src="img/avatar-default.png" alt="User" class="rounded-circle" width="40">
                </div>
                <div class="flex-grow-1">
                  <div class="fw-semibold">أحمد محمد</div>
                  <small class="text-muted">مرحباً، لدي استفسار...</small>
                </div>
              </a>
              <div class="admin-dropdown-footer">
                <a href="#">عرض جميع الرسائل</a>
              </div>
            </div>
          </div>

          <!-- Settings -->
          <div class="admin-nav-item">
            <a href="settings.php" class="admin-nav-link">
              <i class="fas fa-cog"></i>
            </a>
          </div>

          <!-- Theme Toggle -->
          <div class="admin-nav-item">
            <button class="admin-nav-link border-0 bg-transparent" id="themeToggle">
              <i class="fas fa-moon" id="themeIcon"></i>
            </button>
          </div>

          <!-- User Profile -->
          <div class="admin-nav-item dropdown">
            <div class="admin-user-profile" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="img/avatar-default.png" alt="User" class="admin-user-avatar">
              <div class="admin-user-info">
                <div class="admin-user-name"><?php echo isset($_SESSION['name']) ? $_SESSION['name'] : 'المستخدم'; ?></div>
                <div class="admin-user-role">مدير النظام</div>
              </div>
              <i class="fas fa-chevron-down text-white ms-2"></i>
            </div>
            <div class="dropdown-menu dropdown-menu-end admin-dropdown-menu">
              <a href="profile.php" class="admin-dropdown-item">
                <i class="fas fa-user me-2"></i>
                الملف الشخصي
              </a>
              <a href="settings.php" class="admin-dropdown-item">
                <i class="fas fa-cog me-2"></i>
                الإعدادات
              </a>
              <div class="dropdown-divider"></div>
              <a href="login.php" class="admin-dropdown-item text-danger">
                <i class="fas fa-sign-out-alt me-2"></i>
                تسجيل الخروج
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <!-- ═══════════════════════════════════════════════════════
     Modern Sidebar
     ═══════════════════════════════════════════════════════ -->
  <aside class="admin-sidebar" id="adminSidebar">
    <ul class="admin-sidebar-menu">
      <!-- Dashboard -->
      <li class="admin-sidebar-item">
        <a href="index.php" class="admin-sidebar-link active">
          <i class="fas fa-tachometer-alt"></i>
          <span class="admin-sidebar-text">لوحة التحكم</span>
        </a>
      </li>

      <!-- Sales -->
      <li class="admin-sidebar-item">
        <a href="#salesMenu" class="admin-sidebar-link" data-bs-toggle="collapse" aria-expanded="false">
          <i class="fas fa-shopping-cart"></i>
          <span class="admin-sidebar-text">المبيعات</span>
          <i class="fas fa-chevron-left admin-sidebar-arrow ms-auto"></i>
        </a>
        <div class="collapse admin-submenu" id="salesMenu">
          <ul class="list-unstyled">
            <li class="admin-submenu-item">
              <a href="add_saleInvoice.php" class="admin-submenu-link">
                فاتورة مبيعات جديدة
              </a>
            </li>
            <li class="admin-submenu-item">
              <a href="saleInvoice.php" class="admin-submenu-link">
                جميع الفواتير
              </a>
            </li>
            <li class="admin-submenu-item">
              <a href="saleInvoice-report.php" class="admin-submenu-link">
                تقرير المبيعات
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- Purchases -->
      <li class="admin-sidebar-item">
        <a href="#purchaseMenu" class="admin-sidebar-link" data-bs-toggle="collapse" aria-expanded="false">
          <i class="fas fa-shopping-bag"></i>
          <span class="admin-sidebar-text">المشتريات</span>
          <i class="fas fa-chevron-left admin-sidebar-arrow ms-auto"></i>
        </a>
        <div class="collapse admin-submenu" id="purchaseMenu">
          <ul class="list-unstyled">
            <li class="admin-submenu-item">
              <a href="add_purchase.php" class="admin-submenu-link">
                فاتورة مشتريات جديدة
              </a>
            </li>
            <li class="admin-submenu-item">
              <a href="purchase.php" class="admin-submenu-link">
                جميع المشتريات
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- Products -->
      <li class="admin-sidebar-item">
        <a href="#productsMenu" class="admin-sidebar-link" data-bs-toggle="collapse" aria-expanded="false">
          <i class="fas fa-cube"></i>
          <span class="admin-sidebar-text">المنتجات</span>
          <i class="fas fa-chevron-left admin-sidebar-arrow ms-auto"></i>
        </a>
        <div class="collapse admin-submenu" id="productsMenu">
          <ul class="list-unstyled">
            <li class="admin-submenu-item">
              <a href="add_item.php" class="admin-submenu-link">
                إضافة منتج جديد
              </a>
            </li>
            <li class="admin-submenu-item">
              <a href="items.php" class="admin-submenu-link">
                جميع المنتجات
              </a>
            </li>
            <li class="admin-submenu-item">
              <a href="categories.php" class="admin-submenu-link">
                التصنيفات
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- Customers -->
      <li class="admin-sidebar-item">
        <a href="#customersMenu" class="admin-sidebar-link" data-bs-toggle="collapse" aria-expanded="false">
          <i class="fas fa-users"></i>
          <span class="admin-sidebar-text">العملاء</span>
          <i class="fas fa-chevron-left admin-sidebar-arrow ms-auto"></i>
        </a>
        <div class="collapse admin-submenu" id="customersMenu">
          <ul class="list-unstyled">
            <li class="admin-submenu-item">
              <a href="add_customer.php" class="admin-submenu-link">
                إضافة عميل جديد
              </a>
            </li>
            <li class="admin-submenu-item">
              <a href="customers.php" class="admin-submenu-link">
                جميع العملاء
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- Suppliers -->
      <li class="admin-sidebar-item">
        <a href="#suppliersMenu" class="admin-sidebar-link" data-bs-toggle="collapse" aria-expanded="false">
          <i class="fas fa-truck"></i>
          <span class="admin-sidebar-text">الموردين</span>
          <i class="fas fa-chevron-left admin-sidebar-arrow ms-auto"></i>
        </a>
        <div class="collapse admin-submenu" id="suppliersMenu">
          <ul class="list-unstyled">
            <li class="admin-submenu-item">
              <a href="add_supplier.php" class="admin-submenu-link">
                إضافة مورد جديد
              </a>
            </li>
            <li class="admin-submenu-item">
              <a href="suppliers.php" class="admin-submenu-link">
                جميع الموردين
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- Inventory -->
      <li class="admin-sidebar-item">
        <a href="#inventoryMenu" class="admin-sidebar-link" data-bs-toggle="collapse" aria-expanded="false">
          <i class="fas fa-warehouse"></i>
          <span class="admin-sidebar-text">المخزون</span>
          <i class="fas fa-chevron-left admin-sidebar-arrow ms-auto"></i>
        </a>
        <div class="collapse admin-submenu" id="inventoryMenu">
          <ul class="list-unstyled">
            <li class="admin-submenu-item">
              <a href="store-inventory.php" class="admin-submenu-link">
                جرد المخزون
              </a>
            </li>
            <li class="admin-submenu-item">
              <a href="stores.php" class="admin-submenu-link">
                المخازن
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- Reports -->
      <li class="admin-sidebar-item">
        <a href="#reportsMenu" class="admin-sidebar-link" data-bs-toggle="collapse" aria-expanded="false">
          <i class="fas fa-chart-bar"></i>
          <span class="admin-sidebar-text">التقارير</span>
          <i class="fas fa-chevron-left admin-sidebar-arrow ms-auto"></i>
        </a>
        <div class="collapse admin-submenu" id="reportsMenu">
          <ul class="list-unstyled">
            <li class="admin-submenu-item">
              <a href="saleInvoice-report.php" class="admin-submenu-link">
                تقرير المبيعات
              </a>
            </li>
            <li class="admin-submenu-item">
              <a href="purchInvoice-report.php" class="admin-submenu-link">
                تقرير المشتريات
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- Settings -->
      <li class="admin-sidebar-item">
        <a href="settings.php" class="admin-sidebar-link">
          <i class="fas fa-cog"></i>
          <span class="admin-sidebar-text">الإعدادات</span>
        </a>
      </li>
    </ul>
  </aside>

  <!-- Sidebar Toggle for Mobile -->
  <button class="admin-sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
  </button>

  <!-- Main Content Area -->
  <main class="admin-main" id="adminMain">
    <div class="admin-container">