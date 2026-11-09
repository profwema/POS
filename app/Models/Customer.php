<?php
/**
 * موديل العملاء - Customer Model
 * WAM Tech POS & ERP
 */

class Customer {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    public static function getAll(string $search = '', int $limit = 50): array {
        self::init();
        if (!empty($search)) {
            $s = '%' . trim($search) . '%';
            $sql = "SELECT * FROM customers 
                    WHERE is_active = 1 AND (name LIKE ? OR phone LIKE ? OR tax_number LIKE ?) 
                    ORDER BY id ASC LIMIT ?";
            return self::$db->fetchAll($sql, [$s, $s, $s, $limit]);
        }
        return self::$db->fetchAll("SELECT * FROM customers WHERE is_active = 1 ORDER BY id ASC LIMIT ?", [$limit]);
    }

    public static function findById(int $id): ?array {
        self::init();
        return self::$db->fetchOne("SELECT * FROM customers WHERE id = ?", [$id]);
    }

    public static function findByPhone(string $phone): ?array {
        self::init();
        return self::$db->fetchOne("SELECT * FROM customers WHERE phone = ? LIMIT 1", [trim($phone)]);
    }

    public static function create(array $data): int {
        self::init();
        return self::$db->insert('customers', [
            'name'            => trim($data['name']),
            'phone'           => !empty($data['phone']) ? trim($data['phone']) : null,
            'email'           => !empty($data['email']) ? trim($data['email']) : null,
            'address'         => !empty($data['address']) ? trim($data['address']) : null,
            'tax_number'      => !empty($data['tax_number']) ? trim($data['tax_number']) : null,
            'credit_limit'    => (float)($data['credit_limit'] ?? 0.0),
            'current_balance' => (float)($data['current_balance'] ?? 0.0),
            'points'          => (int)($data['points'] ?? 0),
            'notes'           => $data['notes'] ?? null,
            'is_active'       => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);
    }

    public static function update(int $id, array $data): bool {
        self::init();
        return self::$db->update('customers', [
            'name'         => trim($data['name']),
            'phone'        => !empty($data['phone']) ? trim($data['phone']) : null,
            'email'        => !empty($data['email']) ? trim($data['email']) : null,
            'address'      => !empty($data['address']) ? trim($data['address']) : null,
            'tax_number'   => !empty($data['tax_number']) ? trim($data['tax_number']) : null,
            'credit_limit' => (float)($data['credit_limit'] ?? 0.0),
            'notes'        => $data['notes'] ?? null,
            'is_active'    => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ], 'id = ?', [$id]);
    }

    public static function updateBalance(int $customerId, float $amountChange): bool {
        self::init();
        $stmt = self::$db->getPdo()->prepare("UPDATE customers SET current_balance = current_balance + ? WHERE id = ?");
        return $stmt->execute([$amountChange, $customerId]);
    }

    public static function addPoints(int $customerId, int $points): bool {
        self::init();
        $stmt = self::$db->getPdo()->prepare("UPDATE customers SET points = points + ? WHERE id = ?");
        return $stmt->execute([$points, $customerId]);
    }
}

