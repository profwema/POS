<?php
/**
 * موديل الأقسام والتصنيفات - Category Model
 * WAM Tech POS & ERP
 */

class Category {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    public static function getAll(bool $activeOnly = false): array {
        self::init();
        $sql = "SELECT c.*, 
                       (SELECT COUNT(*) FROM items WHERE category_id = c.id) AS items_count
                FROM categories c ";
        if ($activeOnly) {
            $sql .= "WHERE c.is_active = 1 ";
        }
        $sql .= "ORDER BY c.id ASC";
        return self::$db->fetchAll($sql);
    }

    public static function findById(int $id): ?array {
        self::init();
        $res = self::$db->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
        return $res ?: null;
    }

    public static function create(array $data): int {
        self::init();
        return self::$db->insert('categories', [
            'code'        => $data['code'] ?? null,
            'name'        => trim($data['name']),
            'description' => $data['description'] ?? null,
            'icon'        => $data['icon'] ?? 'fas fa-tag',
            'is_active'   => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);
    }

    public static function update(int $id, array $data): bool {
        self::init();
        return self::$db->update('categories', [
            'code'        => $data['code'] ?? null,
            'name'        => trim($data['name']),
            'description' => $data['description'] ?? null,
            'icon'        => $data['icon'] ?? 'fas fa-tag',
            'is_active'   => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ], 'id = ?', [$id]);
    }

    public static function delete(int $id): bool {
        self::init();
        // Check if items exist under this category
        $cnt = self::$db->fetchValue("SELECT COUNT(*) FROM items WHERE category_id = ?", [$id]);
        if ($cnt > 0) {
            throw new Exception("لا يمكن حذف القسم لوجود أصناف مرتبطة به ({$cnt} صنف). يرجى نقل الأصناف أولاً.");
        }
        return self::$db->delete('categories', 'id = ?', [$id]);
    }
}

