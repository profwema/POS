<!-- ═══════════════════════════════════════════════════════════════
   PROFESSIONAL ADMIN NAVBAR
   Modern, clean navbar with search, notifications, and user menu
   ═══════════════════════════════════════════════════════════════ -->
<?php
// Calculate live alerts for the notification bell
$headerAlertsCount = 0;
$headerAlertList = [];
if (isset($adController) && $adController->MySQL) {
    $hdrStoreId = (int)($_SESSION['storeid'] ?? 1);
    $hdrQuery = "SELECT 
                    i.id, i.name_ar, i.name_en, i.selt, i.expiry_date,
                    (
                        COALESCE((SELECT SUM(o.quantity) FROM open_palance o WHERE o.itemid = i.id), 0) +
                        COALESCE((SELECT SUM(inc.quantity) FROM income_items inc WHERE inc.itemid = i.id AND inc.state = '1'), 0) -
                        COALESCE((SELECT SUM(outg.quantity) FROM outgo_items outg WHERE outg.itemid = i.id AND outg.state = '1'), 0)
                    ) AS current_stock
                 FROM items i 
                 WHERE (i.storeid = '$hdrStoreId' OR i.storeid = '0') AND i.enabled = '1'
                 LIMIT 200";
    $hdrRes = @mysqli_query($adController->MySQL, $hdrQuery);
    if ($hdrRes) {
        $todayDt = new DateTime();
        while ($hItem = mysqli_fetch_assoc($hdrRes)) {
            $hStock = (float)$hItem['current_stock'];
            $hReorder = (float)$hItem['selt'];
            if ($hReorder <= 0) $hReorder = 5.0;

            $hName = !empty($hItem['name_ar']) ? $hItem['name_ar'] : $hItem['name_en'];

            if ($hStock <= 0) {
                $headerAlertsCount++;
                if (count($headerAlertList) < 4) {
                    $headerAlertList[] = [
                        'type' => 'out_of_stock',
                        'title' => 'نفاد المخزون',
                        'desc' => "المنتج ($hName) نفدت كميته (0)",
                        'icon' => 'fas fa-ban',
                        'bg' => 'bg-danger text-white',
                        'link' => 'inventory_alerts.php?type=out_of_stock'
                    ];
                }
            } elseif ($hStock <= $hReorder) {
                $headerAlertsCount++;
                if (count($headerAlertList) < 4) {
                    $headerAlertList[] = [
                        'type' => 'low_stock',
                        'title' => 'مخزون منخفض',
                        'desc' => "المنتج ($hName) متبقي $hStock فقط",
                        'icon' => 'fas fa-exclamation-triangle',
                        'bg' => 'bg-warning text-dark',
                        'link' => 'inventory_alerts.php?type=low_stock'
                    ];
                }
            } elseif (!empty($hItem['expiry_date']) && $hItem['expiry_date'] !== '0000-00-00') {
                try {
                    $expDt = new DateTime($hItem['expiry_date']);
                    $diffDays = (int)$todayDt->diff($expDt)->format('%r%a');
                    if ($diffDays <= 60) {
                        $headerAlertsCount++;
                        if (count($headerAlertList) < 4) {
                            $headerAlertList[] = [
                                'type' => 'expiry',
                                'title' => ($diffDays < 0) ? 'منتهي الصلاحية' : 'قرب انتهاء الصلاحية',
                                'desc' => "المنتج ($hName) صلاحيته: {$hItem['expiry_date']}",
                                'icon' => 'fas fa-hourglass-half',
                                'bg' => 'bg-danger text-white',
                                'link' => 'inventory_alerts.php?type=near_expiry'
                            ];
                        }
                    }
                } catch (\Throwable $t) {}
            }
        }
    }
}
?>
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
        <?php if ($headerAlertsCount > 0): ?>
          <span class="navbar-badge"><?= $headerAlertsCount ?></span>
        <?php endif; ?>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 330px;">
        <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
          <strong>تنبيهات المخزون</strong>
          <span class="badge-pro <?= ($headerAlertsCount > 0) ? 'badge-pro-danger' : 'badge-pro-success' ?>">
            <?= $headerAlertsCount ?>
          </span>
        </li>
        <li>
          <hr class="dropdown-divider m-0">
        </li>
        <?php if (empty($headerAlertList)): ?>
          <li class="px-3 py-4 text-center text-muted">
            <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
            <span class="small">لا توجد تنبيهات عاجلة، المخزون آمن</span>
          </li>
        <?php else: ?>
          <?php foreach ($headerAlertList as $alert): ?>
            <li>
              <a class="dropdown-item py-2 px-3 border-bottom" href="<?= htmlspecialchars($alert['link']) ?>">
                <div class="d-flex gap-3 align-items-center">
                  <div class="flex-shrink-0">
                    <div class="<?= $alert['bg'] ?> rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.9rem;">
                      <i class="<?= $alert['icon'] ?>"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-semibold text-truncate small"><?= htmlspecialchars($alert['title']) ?></div>
                    <div class="text-muted small text-truncate"><?= htmlspecialchars($alert['desc']) ?></div>
                  </div>
                </div>
              </a>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
        <li class="text-center py-2 bg-light">
          <a href="inventory_alerts.php" class="small text-decoration-none fw-bold text-primary">
            <i class="fas fa-external-link-alt me-1"></i> عرض كافة التنبيهات (<?= $headerAlertsCount ?>)
          </a>
        </li>
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
          <span class="navbar-user-name"><?= htmlspecialchars($_SESSION['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
          <span class="navbar-user-role"><?= htmlspecialchars($_SESSION['role_name'] ?? 'مستخدم', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
      </div>
      <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 220px;">
        <li class="dropdown-header text-center">
          <img src="img/emp/user.png" alt="User" class="rounded-circle mb-2" width="60" height="60">
          <div class="fw-bold"><?= htmlspecialchars($_SESSION['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
          <div class="text-muted small"><?= htmlspecialchars($_SESSION['role_name'] ?? 'مستخدم', ENT_QUOTES, 'UTF-8') ?></div>
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <?php if (function_exists('is_admin') && is_admin()): ?>
        <li><a class="dropdown-item" href="settings.php">
            <i class="fas fa-cog me-2 text-info"></i> الإعدادات
          </a></li>
        <?php endif; ?>
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
