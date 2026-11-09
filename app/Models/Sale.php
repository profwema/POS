<?php
/**
 * موديل فواتير المبيعات - Sale Model
 * WAM Tech POS & ERP
 */

class Sale {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    public static function findById(int $id): ?array {
        self::init();
        $sql = "
            SELECT s.*, 
                   c.name AS customer_name, c.phone AS customer_phone, c.tax_number AS customer_tax_number,
                   u.full_name AS cashier_name,
                   st.name AS store_name,
                   sh.shift_number
            FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.id
            LEFT JOIN users u ON s.user_id = u.id
            LEFT JOIN stores st ON s.store_id = st.id
            LEFT JOIN shifts sh ON s.shift_id = sh.id
            WHERE s.id = ?
        ";
        return self::$db->fetchOne($sql, [$id]);
    }

    public static function findByInvoiceNumber(string $invoiceNumber): ?array {
        self::init();
        $sql = "
            SELECT s.*, 
                   c.name AS customer_name, c.phone AS customer_phone,
                   u.full_name AS cashier_name,
                   st.name AS store_name
            FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.id
            LEFT JOIN users u ON s.user_id = u.id
            LEFT JOIN stores st ON s.store_id = st.id
            WHERE s.invoice_number = ?
        ";
        return self::$db->fetchOne($sql, [trim($invoiceNumber)]);
    }

    public static function getItems(int $saleId): array {
        self::init();
        $sql = "
            SELECT si.*, 
                   i.name AS item_name, i.barcode, 
                   u.name AS unit_name, u.symbol AS unit_symbol
            FROM sale_items si
            LEFT JOIN items i ON si.item_id = i.id
            LEFT JOIN units u ON i.unit_id = u.id
            WHERE si.sale_id = ?
            ORDER BY si.id ASC
        ";
        return self::$db->fetchAll($sql, [$saleId]);
    }

    public static function getPayments(int $saleId): array {
        self::init();
        $sql = "SELECT * FROM sale_payments WHERE sale_id = ? ORDER BY id ASC";
        return self::$db->fetchAll($sql, [$saleId]);
    }

    public static function getFullInvoice(int $saleId): ?array {
        $sale = self::findById($saleId);
        if (!$sale) {
            return null;
        }

        $sale['items'] = self::getItems($saleId);
        $sale['payments'] = self::getPayments($saleId);
        return $sale;
    }

    public static function getRecentSales(int $limit = 20, ?int $shiftId = null): array {
        self::init();
        $params = [];
        $where = "1=1";
        if ($shiftId) {
            $where .= " AND s.shift_id = ?";
            $params[] = $shiftId;
        }

        $sql = "
            SELECT s.*, 
                   c.name AS customer_name,
                   u.full_name AS cashier_name
            FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.id
            LEFT JOIN users u ON s.user_id = u.id
            WHERE {$where}
            ORDER BY s.id DESC
            LIMIT {$limit}
        ";
        return self::$db->fetchAll($sql, $params);
    }
}

