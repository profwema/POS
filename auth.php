<?php
/**
 * WAM Tech POS - Role-Based Access Control (RBAC) System
 * Centralized authorization, permissions, and security guard.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Initialize and synchronize user permissions in session
 */
function auth_init_session($conn = null) {
    if (!isset($_SESSION['userlogged'])) {
        return;
    }

    // If permissions already loaded and user_type is set, skip unless forced
    if (isset($_SESSION['user_rights']) && isset($_SESSION['user_type']) && isset($_SESSION['role_name'])) {
        return;
    }

    $closeConn = false;
    if ($conn === null) {
        if (isset($GLOBALS['MySQL']) && $GLOBALS['MySQL'] instanceof mysqli) {
            $conn = $GLOBALS['MySQL'];
        } else {
            require_once(__DIR__ . "/db.conn.php");
            $conn = $MySQL;
        }
    }

    $userId = (int)$_SESSION['userlogged'];
    $stmt = safe_query(
        $conn,
        "SELECT e.*, ur.designation_ar, ur.designation_en, ur.view_items, ur.add_items, 
                ur.edit_items, ur.delete_items, ur.view_cat, ur.add_cat, ur.edit_cat, 
                ur.delete_cat, ur.view_emp, ur.add_emp, ur.edit_emp, ur.delete_emp, 
                ur.sales, ur.reports 
         FROM employees e 
         LEFT JOIN user_rights ur ON e.type = ur.id 
         WHERE e.id = ?",
        [$userId]
    );

    if ($stmt && $row = mysqli_fetch_assoc($stmt)) {
        $_SESSION['user_type']          = (int)$row['type'];
        $_SESSION['name']               = $row['name_ar'] ?: ($row['name_en'] ?: $row['user']);
        $_SESSION['user_name']          = $row['user'];
        $_SESSION['storeid']            = (int)$row['storeid'];
        $_SESSION['show_report']        = (string)$row['show_report'];
        $_SESSION['allow_price_change'] = (string)$row['allow_price_change'];
        $_SESSION['allow_dis_change']   = (string)$row['allow_dis_change'];
        $_SESSION['till_control']       = (string)$row['till_control'];
        $_SESSION['role_name']          = $row['designation_ar'] ?: ($row['designation_en'] ?: 'مستخدم');

        // Admin (type 1 or id 1) gets all privileges
        if ((int)$row['type'] === 1 || $userId === 1) {
            $_SESSION['role_name'] = 'المشرف العام';
            $_SESSION['user_rights'] = [
                'view_items'   => '1',
                'add_items'    => '1',
                'edit_items'   => '1',
                'delete_items' => '1',
                'view_cat'     => '1',
                'add_cat'      => '1',
                'edit_cat'     => '1',
                'delete_cat'   => '1',
                'view_emp'     => '1',
                'add_emp'      => '1',
                'edit_emp'     => '1',
                'delete_emp'   => '1',
                'sales'        => '1',
                'reports'      => '1',
            ];
        } else {
            $_SESSION['user_rights'] = [
                'view_items'   => (string)($row['view_items'] ?? '0'),
                'add_items'    => (string)($row['add_items'] ?? '0'),
                'edit_items'   => (string)($row['edit_items'] ?? '0'),
                'delete_items' => (string)($row['delete_items'] ?? '0'),
                'view_cat'     => (string)($row['view_cat'] ?? '0'),
                'add_cat'      => (string)($row['add_cat'] ?? '0'),
                'edit_cat'     => (string)($row['edit_cat'] ?? '0'),
                'delete_cat'   => (string)($row['delete_cat'] ?? '0'),
                'view_emp'     => (string)($row['view_emp'] ?? '0'),
                'add_emp'      => (string)($row['add_emp'] ?? '0'),
                'edit_emp'     => (string)($row['edit_emp'] ?? '0'),
                'delete_emp'   => (string)($row['delete_emp'] ?? '0'),
                'sales'        => (string)($row['sales'] ?? '1'),
                'reports'      => (string)($row['reports'] ?? '0'),
            ];
        }
    }
}

/**
 * Check if the logged in user is a Super Admin
 */
function is_admin(): bool {
    if (!isset($_SESSION['userlogged'])) {
        return false;
    }
    return ((int)($_SESSION['userlogged'] ?? 0) === 1 || (int)($_SESSION['user_type'] ?? 0) === 1);
}

/**
 * Check if the logged in user is a Manager (type 2) or Admin (type 1)
 */
function is_manager(): bool {
    if (is_admin()) {
        return true;
    }
    return ((int)($_SESSION['user_type'] ?? 0) === 2);
}

/**
 * Check if the logged in user is a Cashier (type 3)
 */
function is_cashier(): bool {
    return ((int)($_SESSION['user_type'] ?? 0) === 3);
}

/**
 * Check granular permission from user_rights
 */
function has_permission(string $perm): bool {
    if (is_admin()) {
        return true;
    }
    if (!isset($_SESSION['user_rights']) || !is_array($_SESSION['user_rights'])) {
        return false;
    }
    return (($_SESSION['user_rights'][$perm] ?? '0') === '1');
}

/**
 * Check if user can view reports
 */
function can_view_reports(): bool {
    if (is_admin()) {
        return true;
    }
    return (($_SESSION['show_report'] ?? '0') === '1' && has_permission('reports'));
}

/**
 * Check if user can access Accounting & Financials
 */
function can_access_accounting(): bool {
    return (is_admin() || is_manager());
}

/**
 * Check if user can access System & Global Settings
 */
function can_access_settings(): bool {
    return is_admin();
}

/**
 * Check if user can manage Employees / HR
 */
function can_manage_employees(): bool {
    return (is_admin() || has_permission('view_emp'));
}

/**
 * Central Page Access Checker
 * Returns true if the logged-in user is authorized to access $pageName
 */
function check_page_access(string $pageName): bool {
    // If not logged in, redirection.php handles redirect to login.php
    if (!isset($_SESSION['userlogged'])) {
        return false;
    }

    // Super Admin has unrestricted access to everything
    if (is_admin()) {
        return true;
    }

    $page = strtolower(basename($pageName));

    // 1. Settings & System Administration (Admin Only)
    $adminPages = [
        'settings.php',
        'quote_settings.php',
        'stores.php',
        'addstores.php',
        'branches.php',
        'add_branch.php',
        'edit_branch.php',
        'updates.php',
        'vat_flat.php',
        'new_user.php',
        'create_new_user_.php',
        'jobtitle.php',
        'shifts.php',
        'permissions.php',
    ];
    if (in_array($page, $adminPages, true)) {
        return is_admin();
    }

    // 2. Accounting & Finance (Admin & Manager Only)
    $accountingPages = [
        'accounts.php',
        'journal.php',
        'add_journal.php',
        'edit_journal.php',
        'open-palance.php',
        'add_open_palance.php',
        'budget.php',
        'account-statement.php',
        'incoming-entry.php',
        'outgoing-entry.php',
        'expenditure.php',
        'new_expenditure.php',
        'edit_exp.php',
    ];
    if (in_array($page, $accountingPages, true)) {
        return can_access_accounting();
    }

    // 3. Employees & HR
    if ($page === 'employees.php') {
        return has_permission('view_emp');
    }
    if ($page === 'add_employee.php') {
        return has_permission('add_emp');
    }
    if ($page === 'edit_emp.php') {
        return has_permission('edit_emp');
    }

    // 4. Items & Catalog Modification
    if ($page === 'add_item.php') {
        return has_permission('add_items');
    }
    if ($page === 'edit_item.php') {
        return has_permission('edit_items');
    }
    if ($page === 'add_cat.php') {
        return has_permission('add_cat');
    }
    if ($page === 'edit_cat.php') {
        return has_permission('edit_cat');
    }
    if (in_array($page, ['items.php', 'modern_items.php', 'barcode_item.php', 'item-movement.php', 'data-type.php'], true)) {
        return has_permission('view_items');
    }
    if (in_array($page, ['categories.php', 'units.php', 'addunits.php'], true)) {
        return has_permission('view_cat');
    }

    // 5. Reports
    $reportPages = [
        'saleinvoice-report.php',
        'saleitem-report.php',
        'purchinvoice-report.php',
        'purchitem-erport.php',
        'report-barcode.php',
        'store-inventory.php',
        'all_orders.php',
        'detail-sales.php',
    ];
    if (in_array($page, $reportPages, true)) {
        return can_view_reports();
    }

    // 6. Dashboard (index.php)
    // Cashiers / non-admin without report/accounting access are routed directly to POS
    if ($page === 'index.php' || $page === '') {
        if (is_cashier() && !can_view_reports() && !can_access_accounting()) {
            return false; // Will trigger redirect to pos.php
        }
    }

    return true;
}

/**
 * Enforce page access and redirect if unauthorized
 */
function enforce_page_access(string $pageName): void {
    if (!check_page_access($pageName)) {
        $target = is_cashier() ? 'pos.php' : 'index.php';
        $safePage = htmlspecialchars($pageName, ENT_QUOTES, 'UTF-8');
        print("<script>alert('عذراً، لا تملك الصلاحية اللازمة للوصول إلى هذه الصفحة ({$safePage}).'); location.replace('{$target}');</script>");
        exit();
    }
}
