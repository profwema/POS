<?php
/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * كلاس المصادقة وحوكمة الصلاحيات (Authentication & RBAC)
 * 
 * هندسة وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
 */

class Auth {

    /**
     * تشفير كلمة المرور بخوارزمية Bcrypt الحديثة
     */
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * التحقق من مطابقة كلمة المرور مع دعم الترقية التلقائية إذا كانت قديمة
     */
    public static function verifyPassword(string $password, string $hash): bool {
        // فحص التوافق مع كلمات مرور MD5 القديمة للترقية السلسة
        if (strlen($hash) === 32 && ctype_xdigit($hash)) {
            return md5($password) === $hash;
        }
        return password_verify($password, $hash);
    }

    /**
     * تسجيل الدخول والتحقق من هوية المستخدم
     */
    public static function attempt(string $username, string $password): array {
        $db = Database::getInstance();
        
        $user = $db->fetchOne(
            "SELECT u.*, r.slug AS role_slug, r.name AS role_name, b.name AS branch_name 
             FROM `users` u
             LEFT JOIN `roles` r ON u.role_id = r.id
             LEFT JOIN `branches` b ON u.branch_id = b.id
             WHERE u.username = :user LIMIT 1",
            [':user' => trim($username)]
        );

        if (!$user) {
            return ['success' => false, 'message' => 'اسم المستخدم أو كلمة المرور غير صحيحة.'];
        }

        if (empty($user['is_active'])) {
            return ['success' => false, 'message' => 'هذا الحساب معطل حالياً. يُرجى مراجعة إدارة النظام.'];
        }

        if (!self::verifyPassword($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'اسم المستخدم أو كلمة المرور غير صحيحة.'];
        }

        // إذا كانت كلمة المرور قديمة بتشفير MD5، نقوم بترقيتها تلقائياً إلى Bcrypt فوراً
        if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT)) {
            $newHash = self::hashPassword($password);
            $db->update('users', ['password_hash' => $newHash], 'id = :id', [':id' => $user['id']]);
        }

        // تجديد معرف الجلسة لمنع هجمات Session Fixation
        session_regenerate_id(true);

        // تخزين بيانات المستخدم الآمنة في الجلسة
        $_SESSION['user_logged'] = true;
        $_SESSION['user_id']     = (int)$user['id'];
        $_SESSION['username']    = $user['username'];
        $_SESSION['full_name']   = $user['full_name'];
        $_SESSION['role_id']     = (int)$user['role_id'];
        $_SESSION['role_slug']   = $user['role_slug'];
        $_SESSION['role_name']   = $user['role_name'];
        $_SESSION['branch_id']   = (int)($user['branch_id'] ?? 1);
        $_SESSION['branch_name'] = $user['branch_name'] ?? 'الفرع الرئيسي';

        // تحديث وقت آخر تسجيل دخول
        $db->update('users', [
            'last_login_at' => date('Y-m-d H:i:s')
        ], 'id = :id', [':id' => $user['id']]);

        // توثيق تسجيل الدخول في سجل التدقيق
        Logger::audit('login', 'auth', $user['id'], null, ['ip' => $_SERVER['REMOTE_ADDR'] ?? '']);

        return ['success' => true, 'user' => $user];
    }

    /**
     * هل المستخدم مسجل دخوله حالياً؟
     */
    public static function check(): bool {
        return !empty($_SESSION['user_logged']) && !empty($_SESSION['user_id']);
    }

    /**
     * بيانات المستخدم الحالي في الجلسة
     */
    public static function user(): ?array {
        if (!self::check()) return null;
        return [
            'id'          => $_SESSION['user_id'],
            'username'    => $_SESSION['username'],
            'full_name'   => $_SESSION['full_name'],
            'role_id'     => $_SESSION['role_id'],
            'role_slug'   => $_SESSION['role_slug'],
            'role_name'   => $_SESSION['role_name'],
            'branch_id'   => $_SESSION['branch_id'],
            'branch_name' => $_SESSION['branch_name'],
        ];
    }

    public static function id(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public static function role(): ?string {
        return $_SESSION['role_slug'] ?? null;
    }

    public static function branchId(): int {
        return (int)($_SESSION['branch_id'] ?? 1);
    }

    public static function isAdmin(): bool {
        return self::role() === 'admin';
    }

    public static function isManager(): bool {
        return in_array(self::role(), ['admin', 'manager'], true);
    }

    public static function isAccountant(): bool {
        return in_array(self::role(), ['admin', 'manager', 'accountant'], true);
    }

    public static function isCashier(): bool {
        return self::role() === 'cashier';
    }

    /**
     * فحص صلاحيات المستخدم على وظيفة معينة
     */
    public static function hasPermission(string $permission): bool {
        if (self::isAdmin()) return true; // مسؤول النظام لديه كافة الصلاحيات

        $roleId = $_SESSION['role_id'] ?? 0;
        if (!$roleId) return false;

        $db = Database::getInstance();
        $count = $db->fetchColumn(
            "SELECT COUNT(*) FROM `role_permissions` rp
             JOIN `permissions` p ON rp.permission_id = p.id
             WHERE rp.role_id = :role_id AND p.slug = :perm",
            [':role_id' => $roleId, ':perm' => $permission]
        );

        return (int)$count > 0;
    }

    /**
     * جدار حماية الصفحات الحساسة (Route Guard) مع إغلاق ثغرة EAR
     */
    public static function requireLogin(): void {
        if (!self::check()) {
            if (Helpers::isAjax()) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'authenticated' => false,
                    'message' => 'انتهت الجلسة. يُرجى تسجيل الدخول أولاً.'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Location: ' . BASE_URL . 'login.php');
            }
            exit(); // إغلاق ثغرة Execution After Redirect (EAR)
        }
    }

    /**
     * اشتراط صلاحية أو دور معين
     */
    public static function requireRole(string|array $roles): void {
        self::requireLogin();
        $roles = (array)$roles;
        if (!in_array(self::role(), $roles, true) && !self::isAdmin()) {
            http_response_code(403);
            die("عذراً، ليس لديك صلاحية للوصول إلى هذه الشاشة أو تنفيذ هذا الإجراء.");
        }
    }

    /**
     * اشتراط صلاحية وظيفية دقيقة مع حماية الجلسة وقطع التنفيذ
     */
    public static function requirePermission(string|array $permissions): void {
        self::requireLogin();
        if (self::isAdmin()) return; // مدير النظام يتمتع بكافة الصلاحيات تلقائياً

        $perms = (array)$permissions;
        $hasAny = false;
        foreach ($perms as $perm) {
            if (self::hasPermission($perm)) {
                $hasAny = true;
                break;
            }
        }

        if (!$hasAny) {
            if (Helpers::isAjax()) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'عذراً، ليس لديك الصلاحية الكافية لتنفيذ هذا الإجراء.'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(403);
                die("عذراً، ليس لديك الصلاحية الكافية للوصول إلى هذه الشاشة أو تنفيذ هذا الإجراء.");
            }
            exit();
        }
    }

    /**
     * تسجيل الخروج وإنهاء الجلسة بالكامل
     */
    public static function logout(): void {
        if (self::check()) {
            Logger::audit('logout', 'auth', self::id());
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}

