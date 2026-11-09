<?php
/**
 * موديل وحدات القياس - Unit Model
 * WAM Tech POS & ERP
 */

class Unit {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    public static function getAll(bool $activeOnly = false): array {
        self::init();
        $sql = "SELECT u.*, 
                       (SELECT COUNT(*) FROM items WHERE unit_id = u.id) AS items_count
                FROM units u ";
        if ($activeOnly) {
            $sql .= "WHERE u.is_active = 1 ";
        }
        $sql .= "ORDER BY u.id ASC";
        return self::$db->fetchAll($sql);
    }

    public static function findById(int $id): ?array {
        self::init();
        $res = self::$db->fetch("SELECT * FROM units WHERE id = ?", [$id]);
        return $res ?: null;
    }

    public static function create(array $data): int {
        self::init();
        return self::$db->insert('units', [
            'name'            => trim($data['name']),
            'symbol'          => trim($data['symbol']),
            'is_base'         => isset($data['is_base']) ? (int)$data['is_base'] : 1,
            'conversion_rate' => $data['conversion_rate'] ?? 1.0000,
            'is_active'       => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);
    }

    public static function update(int $id, array $data): bool {
        self::init();
        return self::$db->update('units', [
            'name'            => trim($data['name']),
            'symbol'          => trim($data['symbol']),
            'is_base'         => isset($data['is_base']) ? (int)$data['is_base'] : 1,
            'conversion_rate' => $data['conversion_rate'] ?? 1.0000,
            'is_active'       => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ], 'id = ?', [$id]);
    }

    public static function delete(int $id): bool {
        self::init();
        $cnt = self::$db->fetchValue("SELECT COUNT(*) FROM items WHERE unit_id = ?", [$id]);
        if ($cnt > 0) {
            throw new Exception("لا يمكن حذف الوحدة لوجود أصناف مرتبطة بها ({$cnt} صنف).");
        }
        return self::$db->delete('units', 'id = ?', [$id]);
    }
}

