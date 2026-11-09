<?php
/**
 * موديل الأصناف والمنتجات - Item Model
 * WAM Tech POS & ERP
 */

class Item {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * جلب الأصناف مع الفلاتر والترقيم ورصيد المخزون اللحظي
     */
    public static function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        self::init();

        $params = [];
        $whereClauses = ["1=1"];

        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $whereClauses[] = "(i.name LIKE ? OR i.barcode LIKE ? OR i.sku LIKE ? OR i.scale_plu LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['category_id'])) {
            $whereClauses[] = "i.category_id = ?";
            $params[] = (int)$filters['category_id'];
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $whereClauses[] = "i.is_active = ?";
            $params[] = (int)$filters['is_active'];
        }

        if (!empty($filters['is_scale'])) {
            $whereClauses[] = "i.is_scale = 1";
        }

        $where = implode(' AND ', $whereClauses);

        // Subquery for current total stock across all stores (or specific store if passed)
        $storeFilterSql = "";
        $storeParams = [];
        if (!empty($filters['store_id'])) {
            $storeFilterSql = "AND sm.store_id = " . (int)$filters['store_id'];
        }

        $sql = "
            SELECT 
                i.*,
                c.name AS category_name,
                c.icon AS category_icon,
                u.name AS unit_name,
                u.symbol AS unit_symbol,
                COALESCE((
                    SELECT SUM(latest_balance.balance_after)
                    FROM (
                        SELECT sm.item_id, sm.balance_after
                        FROM stock_moves sm
                        INNER JOIN (
                            SELECT item_id, store_id, MAX(id) as max_id
                            FROM stock_moves
                            GROUP BY item_id, store_id
                        ) grouped_sm ON sm.id = grouped_sm.max_id
                        WHERE 1=1 {$storeFilterSql}
                    ) latest_balance
                    WHERE latest_balance.item_id = i.id
                ), 0) AS total_stock
            FROM items i
            LEFT JOIN categories c ON i.category_id = c.id
            LEFT JOIN units u ON i.unit_id = u.id
            WHERE {$where}
        ";

        if (!empty($filters['low_stock_only'])) {
            $sql .= " HAVING total_stock <= i.reorder_level ";
        }

        $sql .= " ORDER BY i.id DESC LIMIT {$limit} OFFSET {$offset}";

        return self::$db->fetchAll($sql, $params);
    }

    /**
     * إجمالي عدد الأصناف بناءً على الفلاتر
     */
    public static function count(array $filters = []): int {
        self::init();

        $params = [];
        $whereClauses = ["1=1"];

        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $whereClauses[] = "(i.name LIKE ? OR i.barcode LIKE ? OR i.sku LIKE ? OR i.scale_plu LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['category_id'])) {
            $whereClauses[] = "i.category_id = ?";
            $params[] = (int)$filters['category_id'];
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $whereClauses[] = "i.is_active = ?";
            $params[] = (int)$filters['is_active'];
        }

        if (!empty($filters['is_scale'])) {
            $whereClauses[] = "i.is_scale = 1";
        }

        $where = implode(' AND ', $whereClauses);

        $sql = "SELECT COUNT(*) FROM items i WHERE {$where}";
        return (int)self::$db->fetchValue($sql, $params);
    }

    /**
     * البحث بالمعرف ID
     */
    public static function findById(int $id): ?array {
        self::init();
        $sql = "
            SELECT 
                i.*,
                c.name AS category_name,
                u.name AS unit_name,
                u.symbol AS unit_symbol
            FROM items i
            LEFT JOIN categories c ON i.category_id = c.id
            LEFT JOIN units u ON i.unit_id = u.id
            WHERE i.id = ?
        ";
        $res = self::$db->fetch($sql, [$id]);
        return $res ?: null;
    }

    /**
     * البحث بالباركود المباشر
     */
    public static function findByBarcode(string $barcode): ?array {
        self::init();
        $sql = "
            SELECT 
                i.*,
                c.name AS category_name,
                u.name AS unit_name,
                u.symbol AS unit_symbol
            FROM items i
            LEFT JOIN categories c ON i.category_id = c.id
            LEFT JOIN units u ON i.unit_id = u.id
            WHERE i.barcode = ? OR i.sku = ?
            LIMIT 1
        ";
        $res = self::$db->fetch($sql, [$barcode, $barcode]);
        return $res ?: null;
    }

    /**
     * البحث الذكي: باركود عادي أو باركود ميزان وزني/سعري
     * يدعم بادئات الميزان المصري (20, 21, 22, 99)
     */
    public static function findByBarcodeOrScale(string $barcode): ?array {
        self::init();
        $barcode = trim($barcode);

        // 1. أولاً: فحص إن كان باركود ميزان إلكتروني
        $scaleInfo = Helpers::parseScaleBarcode($barcode);
        if ($scaleInfo['is_scale']) {
            // بحث عن الصنف بكود PLU الخاص بالميزان
            $plu = $scaleInfo['item_code'];
            // الصنف قد يكون مخزناً بكود PLU مطابق أو باركود يبدأ به
            $sql = "
                SELECT 
                    i.*,
                    c.name AS category_name,
                    u.name AS unit_name,
                    u.symbol AS unit_symbol
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                LEFT JOIN units u ON i.unit_id = u.id
                WHERE (i.scale_plu = ? OR i.barcode LIKE ? OR i.scale_plu = ?) AND i.is_scale = 1
                LIMIT 1
            ";
            $res = self::$db->fetch($sql, [$plu, $scaleInfo['prefix'] . $plu . '%', ltrim($plu, '0')]);
            if ($res) {
                // إرفاق بيانات الميزان المستخرجة
                $res['scale_data'] = $scaleInfo;
                $res['detected_weight'] = $scaleInfo['weight'];
                $res['calculated_price'] = $scaleInfo['weight'] > 0 
                    ? round($scaleInfo['weight'] * (float)$res['retail_price'], 2)
                    : (float)$res['retail_price'];
                return $res;
            }
        }

        // 2. فحص الباركود كصنف عادي
        return self::findByBarcode($barcode);
    }

    /**
     * أرصدة الصنف في كل مخزن
     */
    public static function getStoreStocks(int $itemId): array {
        self::init();
        $sql = "
            SELECT 
                s.id AS store_id,
                s.code AS store_code,
                s.name AS store_name,
                COALESCE(latest_sm.balance_after, 0) AS current_stock
            FROM stores s
            LEFT JOIN (
                SELECT sm1.store_id, sm1.balance_after
                FROM stock_moves sm1
                INNER JOIN (
                    SELECT store_id, MAX(id) as max_id
                    FROM stock_moves
                    WHERE item_id = ?
                    GROUP BY store_id
                ) latest ON sm1.id = latest.max_id
                WHERE sm1.item_id = ?
            ) latest_sm ON s.id = latest_sm.store_id
            WHERE s.is_active = 1
            ORDER BY s.id ASC
        ";
        return self::$db->fetchAll($sql, [$itemId, $itemId]);
    }

    /**
     * إجمالي رصيد الصنف الحالي في كل المستودعات
     */
    public static function getTotalStock(int $itemId): float {
        self::init();
        $sql = "
            SELECT COALESCE(SUM(latest.balance_after), 0)
            FROM (
                SELECT sm.balance_after
                FROM stock_moves sm
                INNER JOIN (
                    SELECT store_id, MAX(id) as max_id
                    FROM stock_moves
                    WHERE item_id = ?
                    GROUP BY store_id
                ) grouped ON sm.id = grouped.max_id
                WHERE sm.item_id = ?
            ) latest
        ";
        return (float)self::$db->fetchValue($sql, [$itemId, $itemId]);
    }

    /**
     * جلب تشغيلات الصنف وتواريخ الصلاحية
     */
    public static function getBatches(int $itemId, ?int $storeId = null): array {
        self::init();
        $params = [$itemId];
        $storeSql = "";
        if ($storeId !== null) {
            $storeSql = "AND b.store_id = ?";
            $params[] = $storeId;
        }

        $sql = "
            SELECT b.*, s.name AS store_name,
                   DATEDIFF(b.expiry_date, CURDATE()) AS days_remaining
            FROM item_batches b
            LEFT JOIN stores s ON b.store_id = s.id
            WHERE b.item_id = ? {$storeSql}
            ORDER BY b.expiry_date ASC
        ";
        return self::$db->fetchAll($sql, $params);
    }

    /**
     * سجل حركات الصنف الشامل (كارت الصنف)
     */
    public static function getMoveHistory(int $itemId, ?int $storeId = null, int $limit = 50): array {
        self::init();
        $params = [$itemId];
        $storeSql = "";
        if ($storeId !== null) {
            $storeSql = "AND sm.store_id = ?";
            $params[] = $storeId;
        }

        $sql = "
            SELECT 
                sm.*,
                s.name AS store_name,
                u.full_name AS user_name,
                b.batch_number,
                b.expiry_date
            FROM stock_moves sm
            LEFT JOIN stores s ON sm.store_id = s.id
            LEFT JOIN users u ON sm.created_by = u.id
            LEFT JOIN item_batches b ON sm.batch_id = b.id
            WHERE sm.item_id = ? {$storeSql}
            ORDER BY sm.id DESC
            LIMIT {$limit}
        ";
        return self::$db->fetchAll($sql, $params);
    }

    /**
     * إضافة صنف جديد
     */
    public static function create(array $data): int {
        self::init();
        return self::$db->insert('items', [
            'category_id'        => !empty($data['category_id']) ? (int)$data['category_id'] : null,
            'unit_id'            => (int)($data['unit_id'] ?? 1),
            'barcode'            => trim($data['barcode']),
            'sku'                => !empty($data['sku']) ? trim($data['sku']) : null,
            'name'               => trim($data['name']),
            'cost_price'         => (float)($data['cost_price'] ?? 0.0),
            'retail_price'       => (float)($data['retail_price'] ?? 0.0),
            'wholesale_price'    => (float)($data['wholesale_price'] ?? 0.0),
            'min_wholesale_price'=> (float)($data['min_wholesale_price'] ?? 0.0),
            'min_sale_price'     => (float)($data['min_sale_price'] ?? 0.0),
            'tax_rate'           => (float)($data['tax_rate'] ?? 14.00),
            'reorder_level'      => (float)($data['reorder_level'] ?? 5.0),
            'is_scale'           => !empty($data['is_scale']) ? 1 : 0,
            'scale_plu'          => !empty($data['scale_plu']) ? trim($data['scale_plu']) : null,
            'has_expiry'         => !empty($data['has_expiry']) ? 1 : 0,
            'notes'              => !empty($data['notes']) ? trim($data['notes']) : null,
            'is_active'          => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);
    }

    /**
     * تحديث بيانات صنف
     */
    public static function update(int $id, array $data): bool {
        self::init();
        return self::$db->update('items', [
            'category_id'        => !empty($data['category_id']) ? (int)$data['category_id'] : null,
            'unit_id'            => (int)($data['unit_id'] ?? 1),
            'barcode'            => trim($data['barcode']),
            'sku'                => !empty($data['sku']) ? trim($data['sku']) : null,
            'name'               => trim($data['name']),
            'cost_price'         => (float)($data['cost_price'] ?? 0.0),
            'retail_price'       => (float)($data['retail_price'] ?? 0.0),
            'wholesale_price'    => (float)($data['wholesale_price'] ?? 0.0),
            'min_wholesale_price'=> (float)($data['min_wholesale_price'] ?? 0.0),
            'min_sale_price'     => (float)($data['min_sale_price'] ?? 0.0),
            'tax_rate'           => (float)($data['tax_rate'] ?? 14.00),
            'reorder_level'      => (float)($data['reorder_level'] ?? 5.0),
            'is_scale'           => !empty($data['is_scale']) ? 1 : 0,
            'scale_plu'          => !empty($data['scale_plu']) ? trim($data['scale_plu']) : null,
            'has_expiry'         => !empty($data['has_expiry']) ? 1 : 0,
            'notes'              => !empty($data['notes']) ? trim($data['notes']) : null,
            'is_active'          => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ], 'id = ?', [$id]);
    }

    /**
     * حذف صنف (مع التحقق من عدم وجود حركات)
     */
    public static function delete(int $id): bool {
        self::init();
        $movesCount = self::$db->fetchValue("SELECT COUNT(*) FROM stock_moves WHERE item_id = ?", [$id]);
        if ($movesCount > 0) {
            // إذا كان له حركات يتم تعطيله بدلاً من حذفه للحفاظ على التكامل المالي
            self::$db->update('items', ['is_active' => 0], 'id = ?', [$id]);
            return true;
        }
        return self::$db->delete('items', 'id = ?', [$id]);
    }
}

