<!-- ═══════════════════════════════════════════════════════════════
   PROFESSIONAL ADMIN SIDEBAR
   Modern, collapsible sidebar with organized menu structure
   ═══════════════════════════════════════════════════════════════ -->
<aside class="admin-sidebar">
  <!-- Sidebar Brand -->
  <div class="sidebar-brand">
    <img src="img/pos1.png" alt="Logo" class="sidebar-brand-logo">
    <span class="sidebar-brand-text">WAM Tech POS</span>
  </div>

  <!-- Sidebar Navigation -->
  <nav class="sidebar-nav">

    <!-- Dashboard -->
    <div class="nav-item">
      <a href="index.php" class="nav-link">
        <i class="fas fa-tachometer-alt nav-icon"></i>
        <span class="nav-text"><?= DASHBOARD ?></span>
      </a>
    </div>

    <!-- Section: البيانات الأساسية -->
    <div class="nav-section-title">البيانات الأساسية</div>

    <!-- الملفات -->
    <div class="nav-item">
      <a href="#" class="nav-link" data-toggle="submenu" aria-expanded="false">
        <i class="fas fa-folder-open nav-icon"></i>
        <span class="nav-text"><?= FILES ?></span>
        <i class="fas fa-chevron-down ms-auto" style="font-size: 0.75rem;"></i>
      </a>
      <ul class="nav-submenu">
        <li><a href="categories.php" class="nav-link">
            <i class="fas fa-tags nav-icon"></i>
            <span class="nav-text"><?= CATEGORIES ?></span>
          </a></li>
        <li><a href="units.php" class="nav-link">
            <i class="fas fa-balance-scale nav-icon"></i>
            <span class="nav-text"><?= UNITS ?></span>
          </a></li>
        <li><a href="items.php" class="nav-link">
            <i class="fas fa-cube nav-icon"></i>
            <span class="nav-text"><?= ITEMS ?></span>
          </a></li>
        <li><a href="customers.php" class="nav-link">
            <i class="fas fa-users nav-icon"></i>
            <span class="nav-text"><?= CUSTOMERS ?></span>
          </a></li>
        <li><a href="suppliers.php" class="nav-link">
            <i class="fas fa-truck nav-icon"></i>
            <span class="nav-text"><?= SUPPLIERS ?></span>
          </a></li>
        <li><a href="employees.php" class="nav-link">
            <i class="fas fa-user-tie nav-icon"></i>
            <span class="nav-text"><?= EMPLOYEES ?></span>
          </a></li>
        <li><a href="branches.php" class="nav-link">
            <i class="fas fa-code-branch nav-icon"></i>
            <span class="nav-text"><?= BRANCHES ?></span>
          </a></li>
        <li><a href="stores.php" class="nav-link">
            <i class="fas fa-warehouse nav-icon"></i>
            <span class="nav-text"><?= STORES ?></span>
          </a></li>
        <li><a href="jobTitle.php" class="nav-link">
            <i class="fas fa-briefcase nav-icon"></i>
            <span class="nav-text"><?= JOPTITLE ?></span>
          </a></li>
        <li><a href="delivery_cost.php" class="nav-link">
            <i class="fas fa-shipping-fast nav-icon"></i>
            <span class="nav-text"><?= DELIVERY_COST ?></span>
          </a></li>
        <li><a href="shifts.php" class="nav-link">
            <i class="fas fa-clock nav-icon"></i>
            <span class="nav-text"><?= SHIFTS ?></span>
          </a></li>
      </ul>
    </div>

    <!-- Section: العمليات -->
    <div class="nav-section-title">العمليات</div>

    <!-- المبيعات -->
    <div class="nav-item">
      <a href="#" class="nav-link" data-toggle="submenu" aria-expanded="false">
        <i class="fas fa-shopping-cart nav-icon text-success"></i>
        <span class="nav-text"><?= SALES ?></span>
        <i class="fas fa-chevron-down ms-auto" style="font-size: 0.75rem;"></i>
      </a>
      <ul class="nav-submenu">
        <li><a href="quotations.php" class="nav-link">
            <i class="fas fa-file-invoice nav-icon"></i>
            <span class="nav-text"><?= QUOTATIONS ?></span>
          </a></li>
        <li><a href="saleInvoice.php" class="nav-link">
            <i class="fas fa-receipt nav-icon"></i>
            <span class="nav-text"><?= SALES ?></span>
          </a></li>
        <li><a href="salesRet.php" class="nav-link">
            <i class="fas fa-undo nav-icon"></i>
            <span class="nav-text"><?= RETURNED_SALES ?></span>
          </a></li>
        <li><a href="saleInvoice-report.php" class="nav-link">
            <i class="fas fa-chart-line nav-icon"></i>
            <span class="nav-text"><?= INVOICE_REPORT ?></span>
          </a></li>
        <li><a href="saleItem-report.php" class="nav-link">
            <i class="fas fa-chart-bar nav-icon"></i>
            <span class="nav-text"><?= ITEMS_REPORT ?></span>
          </a></li>
      </ul>
    </div>

    <!-- المشتريات -->
    <div class="nav-item">
      <a href="#" class="nav-link" data-toggle="submenu" aria-expanded="false">
        <i class="fas fa-shopping-bag nav-icon text-info"></i>
        <span class="nav-text"><?= PURCHASES ?></span>
        <i class="fas fa-chevron-down ms-auto" style="font-size: 0.75rem;"></i>
      </a>
      <ul class="nav-submenu">
        <li><a href="purchase.php" class="nav-link">
            <i class="fas fa-file-invoice-dollar nav-icon"></i>
            <span class="nav-text"><?= PURCHASES ?></span>
          </a></li>
        <li><a href="purchaseRet.php" class="nav-link">
            <i class="fas fa-undo nav-icon"></i>
            <span class="nav-text"><?= RETURNED_PURCHASES ?></span>
          </a></li>
        <li><a href="purchInvoice-report.php" class="nav-link">
            <i class="fas fa-chart-line nav-icon"></i>
            <span class="nav-text"><?= INVOICE_REPORT ?></span>
          </a></li>
        <li><a href="purchItem-erport.php" class="nav-link">
            <i class="fas fa-chart-bar nav-icon"></i>
            <span class="nav-text"><?= ITEMS_REPORT ?></span>
          </a></li>
      </ul>
    </div>

    <!-- المخازن -->
    <div class="nav-item">
      <a href="#" class="nav-link" data-toggle="submenu" aria-expanded="false">
        <i class="fas fa-boxes nav-icon text-warning"></i>
        <span class="nav-text"><?= STORED ?></span>
        <i class="fas fa-chevron-down ms-auto" style="font-size: 0.75rem;"></i>
      </a>
      <ul class="nav-submenu">
        <li><a href="item-movement.php" class="nav-link">
            <i class="fas fa-exchange-alt nav-icon"></i>
            <span class="nav-text"><?= ITEM_MOVEMENT ?></span>
          </a></li>
        <li><a href="store-inventory.php" class="nav-link">
            <i class="fas fa-clipboard-list nav-icon"></i>
            <span class="nav-text"><?= STORES_INVENTORY ?></span>
          </a></li>
        <li><a href="open-palance.php" class="nav-link">
            <i class="fas fa-balance-scale nav-icon"></i>
            <span class="nav-text"><?= OPENING_PALANCE ?></span>
          </a></li>
        <li><a href="incoming-entry.php" class="nav-link">
            <i class="fas fa-arrow-down nav-icon"></i>
            <span class="nav-text"><?= INCOMING_ENTRY ?></span>
          </a></li>
        <li><a href="outgoing-entry.php" class="nav-link">
            <i class="fas fa-arrow-up nav-icon"></i>
            <span class="nav-text"><?= OUTGOING_ENTRY ?></span>
          </a></li>
        <li><a href="stores-transfer.php" class="nav-link">
            <i class="fas fa-truck-moving nav-icon"></i>
            <span class="nav-text"><?= STORE_TRANSFAIR ?></span>
          </a></li>
      </ul>
    </div>

    <!-- Section: المالية -->
    <div class="nav-section-title">المالية</div>

    <!-- الحسابات -->
    <div class="nav-item">
      <a href="#" class="nav-link" data-toggle="submenu" aria-expanded="false">
        <i class="fas fa-calculator nav-icon text-danger"></i>
        <span class="nav-text"><?= F_ACCOUNTS ?></span>
        <i class="fas fa-chevron-down ms-auto" style="font-size: 0.75rem;"></i>
      </a>
      <ul class="nav-submenu">
        <li><a href="accounts.php" class="nav-link">
            <i class="fas fa-money-bill-wave nav-icon"></i>
            <span class="nav-text"><?= ACCOUNTS ?></span>
          </a></li>
        <li><a href="budget.php" class="nav-link">
            <i class="fas fa-chart-pie nav-icon"></i>
            <span class="nav-text"><?= PLBS ?></span>
          </a></li>
        <li><a href="journal.php" class="nav-link">
            <i class="fas fa-book nav-icon"></i>
            <span class="nav-text"><?= JOURNAL ?></span>
          </a></li>
        <li><a href="account-statement.php" class="nav-link">
            <i class="fas fa-file-alt nav-icon"></i>
            <span class="nav-text"><?= ACCOUNT_STATEMENT ?></span>
          </a></li>
      </ul>
    </div>

    <!-- Section: النظام -->
    <div class="nav-section-title">النظام</div>

    <!-- الإعدادات -->
    <div class="nav-item">
      <a href="#" class="nav-link" data-toggle="submenu" aria-expanded="false">
        <i class="fas fa-cog nav-icon text-secondary"></i>
        <span class="nav-text"><?= SETTINGS ?></span>
        <i class="fas fa-chevron-down ms-auto" style="font-size: 0.75rem;"></i>
      </a>
      <ul class="nav-submenu">
        <li><a href="settings.php" class="nav-link">
            <i class="fas fa-sliders-h nav-icon"></i>
            <span class="nav-text"><?= GENERALSET ?></span>
          </a></li>
        <li><a href="permissions.php" class="nav-link">
            <i class="fas fa-user-lock nav-icon"></i>
            <span class="nav-text"><?= PERMISSIONS ?></span>
          </a></li>
        <li><a href="updates.php" class="nav-link">
            <i class="fas fa-sync-alt nav-icon"></i>
            <span class="nav-text"><?= UPDATES ?></span>
          </a></li>
      </ul>
    </div>

  </nav>
</aside>

<!-- Mobile Overlay CSS -->
<style>
  .sidebar-overlay.show {
    display: block !important;
  }
</style>