<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODERN ADMIN RTL - SIDEBAR MENU -->
<!-- ═══════════════════════════════════════════════════════════════ -->

<div id="sidebar-left" class="col-lg-2 bg-light border-end overflow-auto d-none d-lg-block" dir="rtl">
    <div id="sidebar-menu" class="p-3">

        <!-- Accordion Menu -->
        <div class="accordion accordion-flush" id="sidebarAccordion">

            <!-- 1. الملفات -->
            <div class="accordion-item border-0 mb-2">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed bg-white rounded shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filesMenu">
                        <i class="fas fa-folder-open text-primary me-2"></i>
                        <span class="fw-semibold"><?= FILES ?></span>
                    </button>
                </h2>
                <div id="filesMenu" class="accordion-collapse collapse" data-bs-parent="#sidebarAccordion">
                    <div class="accordion-body p-0">
                        <ul class="list-unstyled mb-0">
                            <li><a href="categories.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-tags text-muted me-2"></i><?= CATEGORIES ?></a></li>
                            <li><a href="units.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-balance-scale text-muted me-2"></i><?= UNITS ?></a></li>
                            <li><a href="items.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-cube text-muted me-2"></i><?= ITEMS ?></a></li>
                            <li><a href="customers.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-users text-muted me-2"></i><?= CUSTOMERS ?></a></li>
                            <li><a href="suppliers.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-truck text-muted me-2"></i><?= SUPPLIERS ?></a></li>
                            <li><a href="employees.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-user-tie text-muted me-2"></i><?= EMPLOYEES ?></a></li>
                            <li><a href="branches.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-code-branch text-muted me-2"></i><?= BRANCHES ?></a></li>
                            <li><a href="stores.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-warehouse text-muted me-2"></i><?= STORES ?></a></li>
                            <li><a href="jobTitle.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-briefcase text-muted me-2"></i><?= JOPTITLE ?></a></li>
                            <li><a href="delivery_cost.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-shipping-fast text-muted me-2"></i><?= DELIVERY_COST ?></a></li>
                            <li><a href="shifts.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-clock text-muted me-2"></i><?= SHIFTS ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 2. المبيعات -->
            <div class="accordion-item border-0 mb-2">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed bg-white rounded shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#salesMenu">
                        <i class="fas fa-shopping-cart text-success me-2"></i>
                        <span class="fw-semibold"><?= SALES ?></span>
                    </button>
                </h2>
                <div id="salesMenu" class="accordion-collapse collapse" data-bs-parent="#sidebarAccordion">
                    <div class="accordion-body p-0">
                        <ul class="list-unstyled mb-0">
                            <li><a href="quotations.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-file-invoice text-muted me-2"></i><?= QUOTATIONS ?></a></li>
                            <li><a href="saleInvoice.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-receipt text-muted me-2"></i><?= SALES ?></a></li>
                            <li><a href="salesRet.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-undo text-muted me-2"></i><?= RETURNED_SALES ?></a></li>
                            <li><a href="saleInvoice-report.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-chart-line text-muted me-2"></i><?= INVOICE_REPORT ?></a></li>
                            <li><a href="saleItem-report.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-chart-bar text-muted me-2"></i><?= ITEMS_REPORT ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 3. المشتريات -->
            <div class="accordion-item border-0 mb-2">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed bg-white rounded shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#purchasesMenu">
                        <i class="fas fa-shopping-bag text-info me-2"></i>
                        <span class="fw-semibold"><?= PURCHASES ?></span>
                    </button>
                </h2>
                <div id="purchasesMenu" class="accordion-collapse collapse" data-bs-parent="#sidebarAccordion">
                    <div class="accordion-body p-0">
                        <ul class="list-unstyled mb-0">
                            <li><a href="purchase.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-file-invoice-dollar text-muted me-2"></i><?= PURCHASES ?></a></li>
                            <li><a href="purchaseRet.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-undo text-muted me-2"></i><?= RETURNED_PURCHASES ?></a></li>
                            <li><a href="purchInvoice-report.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-chart-line text-muted me-2"></i><?= INVOICE_REPORT ?></a></li>
                            <li><a href="purchItem-erport.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-chart-bar text-muted me-2"></i><?= ITEMS_REPORT ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 4. المخازن -->
            <div class="accordion-item border-0 mb-2">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed bg-white rounded shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#storesMenu">
                        <i class="fas fa-boxes text-warning me-2"></i>
                        <span class="fw-semibold"><?= STORED ?></span>
                    </button>
                </h2>
                <div id="storesMenu" class="accordion-collapse collapse" data-bs-parent="#sidebarAccordion">
                    <div class="accordion-body p-0">
                        <ul class="list-unstyled mb-0">
                            <li><a href="item-movement.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-exchange-alt text-muted me-2"></i><?= ITEM_MOVEMENT ?></a></li>
                            <li><a href="store-inventory.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-clipboard-list text-muted me-2"></i><?= STORES_INVENTORY ?></a></li>
                            <li><a href="open-palance.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-balance-scale text-muted me-2"></i><?= OPENING_PALANCE ?></a></li>
                            <li><a href="incoming-entry.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-arrow-down text-muted me-2"></i><?= INCOMING_ENTRY ?></a></li>
                            <li><a href="outgoing-entry.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-arrow-up text-muted me-2"></i><?= OUTGOING_ENTRY ?></a></li>
                            <li><a href="stores-transfer.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-truck-moving text-muted me-2"></i><?= STORE_TRANSFAIR ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 5. الحسابات -->
            <div class="accordion-item border-0 mb-2">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed bg-white rounded shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#accountsMenu">
                        <i class="fas fa-calculator text-danger me-2"></i>
                        <span class="fw-semibold"><?= F_ACCOUNTS ?></span>
                    </button>
                </h2>
                <div id="accountsMenu" class="accordion-collapse collapse" data-bs-parent="#sidebarAccordion">
                    <div class="accordion-body p-0">
                        <ul class="list-unstyled mb-0">
                            <li><a href="accounts.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-money-bill-wave text-muted me-2"></i><?= ACCOUNTS ?></a></li>
                            <li><a href="budget.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-chart-pie text-muted me-2"></i><?= PLBS ?></a></li>
                            <li><a href="journal.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-book text-muted me-2"></i><?= JOURNAL ?></a></li>
                            <li><a href="account-statement.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-file-alt text-muted me-2"></i><?= ACCOUNT_STATEMENT ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 6. الإعدادات -->
            <div class="accordion-item border-0 mb-2">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed bg-white rounded shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#settingsMenu">
                        <i class="fas fa-cog text-secondary me-2"></i>
                        <span class="fw-semibold"><?= SETTINGS ?></span>
                    </button>
                </h2>
                <div id="settingsMenu" class="accordion-collapse collapse" data-bs-parent="#sidebarAccordion">
                    <div class="accordion-body p-0">
                        <ul class="list-unstyled mb-0">
                            <li><a href="settings.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-sliders-h text-muted me-2"></i><?= GENERALSET ?></a></li>
                            <li><a href="permissions.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-user-lock text-muted me-2"></i><?= PERMISSIONS ?></a></li>
                            <li><a href="updates.php" class="d-block py-2 px-3 text-decoration-none text-dark hover-bg-light"><i class="fas fa-sync-alt text-muted me-2"></i><?= UPDATES ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Custom CSS for Sidebar -->
<style>
    .hover-bg-light:hover {
        background-color: #f8f9fa !important;
        border-right: 3px solid #0d6efd;
    }

    .accordion-button:not(.collapsed) {
        background-color: #e7f1ff !important;
        color: #0d6efd !important;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0, 0, 0, .125);
    }

    #sidebar-left a.active {
        background-color: #e7f1ff;
        border-right: 3px solid #0d6efd;
        font-weight: 600;
    }
</style>