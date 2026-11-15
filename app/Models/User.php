<?php
/**
 * موديل إدارة المستخدمين والموظفين والصلاحيات - User & RBAC Model
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

class User {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * جلب جميع المستخدمين مع بيانات الأدوار والفروع
     */
    public static function getAll(): array {
        self::init();
        $sql = "
            SELECT 
                u.id, u.branch_id, u.role_id, u.username, u.full_name,
                u.phone, u.email, u.is_active, u.last_login_at, u.created_at,
                r.name AS role_name, r.slug AS role_slug,
                b.name AS branch_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN branches b ON u.branch_id = b.id
            ORDER BY u.id ASC
        ";
        return self::$db->fetchAll($sql);
    }

    /**
     * البحث عن مستخدم بواسطة المعرف
     */
    public static function findById(int $id): ?array {
        self::init();
        $sql = "
            SELECT 
                u.*,
                r.name AS role_name, r.slug AS role_slug,
                b.name AS branch_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN branches b ON u.branch_id = b.id
            WHERE u.id = ?
            LIMIT 1
        ";
        return self::$db->fetchOne($sql, [$id]);
    }

    /**
     * البحث عن مستخدم باسم المستخدم
     */
    public static function findByUsername(string $username): ?array {
        self::init();
        return self::$db->fetchOne("SELECT * FROM users WHERE username = ? LIMIT 1", [trim($username)]);
    }

    /**
     * إنشاء مستخدم جديد في النظام
     */
    public static function create(array $data): int {
        self::init();

        $username = trim($data['username'] ?? '');
        if (empty($username)) {
            throw new Exception("اسم المستخدم مطلوب ولا يمكن تركه فارغاً.");
        }

        $existing = self::findByUsername($username);
        if ($existing) {
            throw new Exception("اسم المستخدم ({$username}) مستخدم بالفعل، يُرجى اختيار اسم آخر.");
        }

        $password = $data['password'] ?? '';
        if (strlen($password) < 6) {
            throw new Exception("كلمة المرور يجب ألا تقل عن 6 أحرف أو أرقام.");
        }

        $userId = self::$db->insert('users', [
            'branch_id'     => !empty($data['branch_id']) ? (int)$data['branch_id'] : 1,
            'role_id'       => (int)($data['role_id'] ?? 4),
            'username'      => $username,
            'password_hash' => Auth::hashPassword($password),
            'full_name'     => trim($data['full_name'] ?? $username),
            'phone'         => !empty($data['phone']) ? trim($data['phone']) : null,
            'email'         => !empty($data['email']) ? trim($data['email']) : null,
            'is_active'     => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);

        Logger::audit('user_created', 'users', $userId, null, [
            'username' => $username,
            'role_id'  => $data['role_id'] ?? 4
        ]);

        return $userId;
    }

    /**
     * تعديل بيانات مستخدم موجود
     */
    public static function update(int $id, array $data): bool {
        self::init();

        $user = self::findById($id);
        if (!$user) {
            throw new Exception("المستخدم غير موجود.");
        }

        $username = trim($data['username'] ?? $user['username']);
        if ($username !== $user['username']) {
            $existing = self::findByUsername($username);
            if ($existing && $existing['id'] != $id) {
                throw new Exception("اسم المستخدم ({$username}) مستخدم من قبل حساب آخر.");
            }
        }

        $updateData = [
            'branch_id' => !empty($data['branch_id']) ? (int)$data['branch_id'] : $user['branch_id'],
            'role_id'   => !empty($data['role_id']) ? (int)$data['role_id'] : $user['role_id'],
            'username'  => $username,
            'full_name' => trim($data['full_name'] ?? $user['full_name']),
            'phone'     => !empty($data['phone']) ? trim($data['phone']) : null,
            'email'     => !empty($data['email']) ? trim($data['email']) : null,
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : $user['is_active']
        ];

        // تحديث كلمة المرور فقط إذا تم إدخال كلمة مرور جديدة
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                throw new Exception("كلمة المرور الجديدة يجب ألا تقل عن 6 أحرف أو أرقام.");
            }
            $updateData['password_hash'] = Auth::hashPassword($data['password']);
        }

        $res = self::$db->update('users', $updateData, 'id = ?', [$id]);

        Logger::audit('user_updated', 'users', $id, $user, $updateData);

        return $res;
    }

    /**
     * تفعيل / تعطيل حساب المستخدم
     */
    public static function toggleStatus(int $id): bool {
        self::init();

        // منع تعطيل حساب المستخدم الحالي الذي قام بتسجيل الدخول
        if (Auth::user()['id'] == $id) {
            throw new Exception("لا يمكنك تعطيل حسابك الشخصي المسجل به حالياً.");
        }

        $user = self::findById($id);
        if (!$user) {
            throw new Exception("المستخدم غير موجود.");
        }

        $newStatus = $user['is_active'] ? 0 : 1;
        $res = self::$db->update('users', ['is_active' => $newStatus], 'id = ?', [$id]);

        Logger::audit('user_status_toggled', 'users', $id, null, ['is_active' => $newStatus]);

        return $res;
    }

    /**
     * استرجاع كافة الأدوار الوظيفية
     */
    public static function getRoles(): array {
        self::init();
        return self::$db->fetchAll("SELECT * FROM roles ORDER BY id ASC");
    }

    /**
     * استرجاع كافة فروع المنشأة
     */
    public static function getBranches(): array {
        self::init();
        return self::$db->fetchAll("SELECT * FROM branches WHERE is_active = 1 ORDER BY id ASC");
    }
}
