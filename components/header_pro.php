<!-- ═══════════════════════════════════════════════════════════════
   PROFESSIONAL ADMIN NAVBAR
   Modern, clean navbar with search, notifications, and user menu
   ═══════════════════════════════════════════════════════════════ -->
<nav class="admin-navbar">
  <!-- Left Section -->
  <div class="navbar-left">
    <!-- Sidebar Toggle -->
    <button class="navbar-toggle" aria-label="Toggle Sidebar">
      <i class="fas fa-bars"></i>
    </button>

    <!-- Search Bar -->
    <div class="navbar-search">
      <i class="fas fa-search navbar-search-icon"></i>
      <input type="text" class="navbar-search-input" placeholder="بحث... (Ctrl+K)" dir="rtl">
    </div>
  </div>

  <!-- Right Section -->
  <div class="navbar-right">

    <!-- Quick POS Button -->
    <div class="navbar-item">
      <a href="pos.php" class="btn-pro btn-pro-success btn-pro-sm" title="نقطة البيع">
        <i class="fas fa-cash-register"></i>
        <span class="d-none d-md-inline">نقطة البيع</span>
      </a>
    </div>

    <!-- Notifications -->
    <div class="navbar-item">
      <button class="navbar-btn" data-action="notifications" data-bs-toggle="dropdown" aria-label="Notifications">
        <i class="fas fa-bell"></i>
        <span class="navbar-badge">3</span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 320px;">
        <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
          <strong>الإشعارات</strong>
          <span class="badge-pro badge-pro-primary">3</span>
        </li>
        <li>
          <hr class="dropdown-divider m-0">
        </li>
        <li>
          <a class="dropdown-item py-3" href="#">
            <div class="d-flex gap-3">
              <div class="flex-shrink-0">
                <div class="bg-warning-light text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
              </div>
              <div class="flex-grow-1">
                <div class="fw-semibold">مخزون منخفض</div>
                <div class="text-muted small">5 منتجات بحاجة لإعادة طلب</div>
                <div class="text-muted small mt-1">منذ 10 دقائق</div>
              </div>
            </div>
          </a>
        </li>
        <li>
          <hr class="dropdown-divider m-0">
        </li>
        <li>
          <a class="dropdown-item py-3" href="#">
            <div class="d-flex gap-3">
              <div class="flex-shrink-0">
                <div class="bg-success-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                  <i class="fas fa-check-circle"></i>
                </div>
              </div>
              <div class="flex-grow-1">
                <div class="fw-semibold">طلب جديد</div>
                <div class="text-muted small">تم استلام طلب جديد #12345</div>
                <div class="text-muted small mt-1">منذ 30 دقيقة</div>
              </div>
            </div>
          </a>
        </li>
        <li>
          <hr class="dropdown-divider m-0">
        </li>
        <li class="text-center py-2">
          <a href="#" class="small text-decoration-none">عرض كل الإشعارات</a>
        </li>
      </ul>
    </div>

    <!-- Language Switcher -->
    <div class="navbar-item">
      <button class="navbar-btn dropdown-toggle" data-bs-toggle="dropdown" aria-label="Language">
        <i class="fas fa-globe"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeLang('ar')">
            <i class="fas fa-check text-success me-2"></i> العربية
          </a></li>
        <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeLang('en')">
            English
          </a></li>
      </ul>
    </div>

    <!-- Dark Mode Toggle -->
    <div class="navbar-item">
      <button class="navbar-btn" id="themeToggle" aria-label="Toggle Theme">
        <i id="themeIcon" class="fas fa-moon"></i>
      </button>
    </div>

    <!-- User Menu -->
    <div class="navbar-item">
      <div class="navbar-user dropdown-toggle" data-bs-toggle="dropdown" aria-label="User Menu">
        <img src="img/emp/user.png" alt="User" class="navbar-avatar">
        <div class="navbar-user-info">
          <span class="navbar-user-name"><?= $_SESSION['name'] ?></span>
          <span class="navbar-user-role"><?= WELCOME ?></span>
        </div>
      </div>
      <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 220px;">
        <li class="dropdown-header text-center">
          <img src="img/emp/user.png" alt="User" class="rounded-circle mb-2" width="60" height="60">
          <div class="fw-bold"><?= $_SESSION['name'] ?></div>
          <div class="text-muted small">المدير</div>
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li><a class="dropdown-item" href="settings.php">
            <i class="fas fa-user-circle me-2 text-primary"></i> الملف الشخصي
          </a></li>
        <li><a class="dropdown-item" href="settings.php">
            <i class="fas fa-cog me-2 text-info"></i> الإعدادات
          </a></li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li>
          <span class="dropdown-item text-danger" id="logout" style="cursor:pointer">
            <i class="fas fa-power-off me-2"></i> <?= LOGOUT ?>
          </span>
        </li>
      </ul>
    </div>

  </div>
</nav>