<?php
/**
 * موديل المخازن والمستودعات - Store Model
 * WAM Tech POS & ERP
 */

class Store {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    public static function getAll(bool $activeOnly = false): array {
        self::init();
        $sql = "SELECT s.*, b.name AS branch_name,
                       (SELECT COUNT(DISTINCT item_id) FROM stock_moves WHERE store_id = s.id) AS distinct_items_count
                FROM stores s
                LEFT JOIN branches b ON s.branch_id = b.id ";
        if ($activeOnly) {
            $sql .= "WHERE s.is_active = 1 ";
        }
        $sql .= "ORDER BY s.id ASC";
        return self::$db->fetchAll($sql);
    }

    public static function findById(int $id): ?array {
        self::init();
        $res = self::$db->fetch("SELECT s.*, b.name AS branch_name 
                                 FROM stores s 
                                 LEFT JOIN branches b ON s.branch_id = b.id 
                                 WHERE s.id = ?", [$id]);
        return $res ?: null;
    }

    public static function getStoreStats(int $storeId): array {
        self::init();
        // Compute total quantity and valuation from the latest balances in stock_moves
        $sql = "
            SELECT 
                COUNT(DISTINCT sm.item_id) AS total_items,
                COALESCE(SUM(latest_sm.balance_after), 0) AS total_stock_qty,
                COALESCE(SUM(latest_sm.balance_after * i.cost_price), 0) AS total_cost_value,
                COALESCE(SUM(latest_sm.balance_after * i.retail_price), 0) AS total_retail_value
            FROM items i
            INNER JOIN (
                SELECT sm1.item_id, sm1.balance_after
                FROM stock_moves sm1
                INNER JOIN (
                    SELECT item_id, MAX(id) as max_id
                    FROM stock_moves
                    WHERE store_id = ?
                    GROUP BY item_id
                ) latest ON sm1.id = latest.max_id
                WHERE sm1.store_id = ? AND sm1.balance_after > 0
            ) latest_sm ON i.id = latest_sm.item_id
        ";
        $row = self::$db->fetch($sql, [$storeId, $storeId]);
        return $row ?: [
            'total_items' => 0,
            'total_stock_qty' => 0,
            'total_cost_value' => 0,
            'total_retail_value' => 0
        ];
    }

    public static function create(array $data): int {
        self::init();
        return self::$db->insert('stores', [
            'branch_id'    => $data['branch_id'] ?? 1,
            'code'         => strtoupper(trim($data['code'])),
            'name'         => trim($data['name']),
            'location'     => $data['location'] ?? null,
            'manager_name' => $data['manager_name'] ?? null,
            'phone'        => $data['phone'] ?? null,
            'is_active'    => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);
    }

    public static function update(int $id, array $data): bool {
        self::init();
        return self::$db->update('stores', [
            'branch_id'    => $data['branch_id'] ?? 1,
            'code'         => strtoupper(trim($data['code'])),
            'name'         => trim($data['name']),
            'location'     => $data['location'] ?? null,
            'manager_name' => $data['manager_name'] ?? null,
            'phone'        => $data['phone'] ?? null,
            'is_active'    => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ], 'id = ?', [$id]);
    }
}

