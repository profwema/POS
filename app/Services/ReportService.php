<?php
/**
 * خدمات التقارير التحليلية والضرائب المصرية والإقرارات - Report & Tax Service
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

class ReportService {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * استخراج إقرار ضريبة القيمة المضافة المصري (نموذج 10 المعتمد)
     * ضريبة المبيعات (المخرجات) - ضريبة المشتريات (المدخلات) = صافي الضريبة الواجبة السداد
     */
    public static function getVatDeclaration(string $dateFrom, string $dateTo): array {
        self::init();

        // 1. مبيعات الفترة الخاضعة للضريبة والمعفاة
        $salesData = self::$db->fetchOne("
            SELECT 
                COUNT(*) AS invoices_count,
                COALESCE(SUM(subtotal), 0) AS total_subtotal,
                COALESCE(SUM(tax_amount), 0) AS total_tax,
                COALESCE(SUM(grand_total), 0) AS total_grand,
                COALESCE(SUM(CASE WHEN tax_amount > 0 THEN subtotal ELSE 0 END), 0) AS taxable_sales,
                COALESCE(SUM(CASE WHEN tax_amount = 0 THEN subtotal ELSE 0 END), 0) AS exempt_sales
            FROM sales
            WHERE DATE(created_at) BETWEEN ? AND ?
        ", [$dateFrom, $dateTo]) ?: [];

        // 2. مردودات مبيعات الفترة
        $salesReturnsData = self::$db->fetchOne("
            SELECT 
                COUNT(*) AS returns_count,
                COALESCE(SUM(subtotal), 0) AS returns_subtotal,
                COALESCE(SUM(tax_amount), 0) AS returns_tax,
                COALESCE(SUM(total_refund), 0) AS returns_grand
            FROM sales_returns
            WHERE return_date BETWEEN ? AND ?
        ", [$dateFrom, $dateTo]) ?: [];

        // صافي ضريبة المبيعات (ضريبة المخرجات)
        $outputVatGross = (float)($salesData['total_tax'] ?? 0);
        $outputVatReturned = (float)($salesReturnsData['returns_tax'] ?? 0);
        $netOutputVat = round($outputVatGross - $outputVatReturned, 2);

        // 3. مشتريات الفترة الخاضعة للضريبة والمعفاة
        $purchasesData = self::$db->fetchOne("
            SELECT 
                COUNT(*) AS purchases_count,
                COALESCE(SUM(subtotal), 0) AS total_subtotal,
                COALESCE(SUM(tax_amount), 0) AS total_tax,
                COALESCE(SUM(grand_total), 0) AS total_grand,
                COALESCE(SUM(CASE WHEN tax_amount > 0 THEN subtotal ELSE 0 END), 0) AS taxable_purchases,
                COALESCE(SUM(CASE WHEN tax_amount = 0 THEN subtotal ELSE 0 END), 0) AS exempt_purchases
            FROM purchases
            WHERE invoice_date BETWEEN ? AND ?
        ", [$dateFrom, $dateTo]) ?: [];

        // 4. مردودات مشتريات الفترة للموردين
        $purchaseReturnsData = self::$db->fetchOne("
            SELECT 
                COUNT(*) AS returns_count,
                COALESCE(SUM(subtotal), 0) AS returns_subtotal,
                COALESCE(SUM(tax_amount), 0) AS returns_tax,
                COALESCE(SUM(total_amount), 0) AS returns_grand
            FROM purchase_returns
            WHERE return_date BETWEEN ? AND ?
        ", [$dateFrom, $dateTo]) ?: [];

        // صافي ضريبة المشتريات (ضريبة المدخلات القابلة للخصم)
        $inputVatGross = (float)($purchasesData['total_tax'] ?? 0);
        $inputVatReturned = (float)($purchaseReturnsData['returns_tax'] ?? 0);
        $netInputVat = round($inputVatGross - $inputVatReturned, 2);

        // صافي الضريبة المستحقة السداد لمصلحة الضرائب المصرية
        $netTaxPayable = round($netOutputVat - $netInputVat, 2);

        // بيانات المنشأة الضريبية
        $companyName = Helpers::setting('company_name', 'شركة النظم الحديثة للتجارة والتوزيع');
        $taxNumber = Helpers::setting('tax_number', '123-456-789');
        $commercialRegistry = Helpers::setting('commercial_registry', '987654');

        return [
            'date_from'             => $dateFrom,
            'date_to'               => $dateTo,
            'company_name'          => $companyName,
            'tax_number'            => $taxNumber,
            'commercial_registry'   => $commercialRegistry,
            'sales'                 => [
                'invoices_count'    => (int)($salesData['invoices_count'] ?? 0),
                'taxable_sales'     => (float)($salesData['taxable_sales'] ?? 0),
                'exempt_sales'      => (float)($salesData['exempt_sales'] ?? 0),
                'gross_tax'         => $outputVatGross,
                'returned_tax'      => $outputVatReturned,
                'net_output_vat'    => $netOutputVat
            ],
            'purchases'             => [
                'purchases_count'   => (int)($purchasesData['purchases_count'] ?? 0),
                'taxable_purchases' => (float)($purchasesData['taxable_purchases'] ?? 0),
                'exempt_purchases'  => (float)($purchasesData['exempt_purchases'] ?? 0),
                'gross_tax'         => $inputVatGross,
                'returned_tax'      => $inputVatReturned,
                'net_input_vat'     => $netInputVat
            ],
            'net_tax_payable'       => $netTaxPayable
        ];
    }

    /**
     * تقرير مبيعات وأرباح الأصناف (Item Profitability & Margin Analysis)
     */
    public static function getItemProfitability(string $dateFrom, string $dateTo, ?int $categoryId = null, ?string $search = null): array {
        self::init();

        $where = ["DATE(s.created_at) BETWEEN ? AND ?"];
        $params = [$dateFrom, $dateTo];

        if (!empty($categoryId)) {
            $where[] = "i.category_id = ?";
            $params[] = $categoryId;
        }

        if (!empty($search)) {
            $s = '%' . trim($search) . '%';
            $where[] = "(i.name LIKE ? OR i.barcode LIKE ?)";
            $params[] = $s;
            $params[] = $s;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT 
                i.id AS item_id,
                i.name AS item_name,
                i.barcode,
                i.cost_price,
                i.retail_price,
                c.name AS category_name,
                u.name AS unit_name,
                COALESCE(SUM(si.quantity), 0) AS total_sold_qty,
                COALESCE(SUM(si.total_price), 0) AS total_revenue,
                COALESCE(SUM(si.quantity * i.cost_price), 0) AS total_cost,
                COALESCE(SUM(si.total_price - (si.quantity * i.cost_price)), 0) AS total_profit
            FROM sale_items si
            JOIN sales s ON si.sale_id = s.id
            JOIN items i ON si.item_id = i.id
            LEFT JOIN categories c ON i.category_id = c.id
            LEFT JOIN units u ON i.unit_id = u.id
            WHERE {$whereClause}
            GROUP BY i.id, i.name, i.barcode, i.cost_price, i.retail_price, c.name, u.name
            ORDER BY total_profit DESC
        ";

        $items = self::$db->fetchAll($sql, $params);

        $totals = [
            'sold_qty'     => 0.0,
            'total_revenue'=> 0.0,
            'total_cost'   => 0.0,
            'total_profit' => 0.0
        ];

        foreach ($items as &$item) {
            $rev = (float)$item['total_revenue'];
            $prof = (float)$item['total_profit'];
            $margin = ($rev > 0) ? round(($prof / $rev) * 100, 2) : 0.0;
            $item['profit_margin'] = $margin;

            $totals['sold_qty'] += (float)$item['total_sold_qty'];
            $totals['total_revenue'] += $rev;
            $totals['total_cost'] += (float)$item['total_cost'];
            $totals['total_profit'] += $prof;
        }
        unset($item);

        $overallMargin = ($totals['total_revenue'] > 0) ? round(($totals['total_profit'] / $totals['total_revenue']) * 100, 2) : 0.0;
        $totals['overall_margin'] = $overallMargin;

        return [
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
            'items'     => $items,
            'totals'    => $totals
        ];
    }

    /**
     * تقرير حركة المخزون وتحليل الأصناف الراكدة وسريعة الدوران وحد الطلب
     */
    public static function getInventoryMovement(string $dateFrom, string $dateTo, ?int $storeId = null): array {
        self::init();

        $storeId = $storeId ?: 2;

        // 1. الأصناف الأكثر مبيعاً (Fast-Moving Items - Top 10)
        $topSold = self::$db->fetchAll("
            SELECT 
                i.id, i.name, i.barcode, i.retail_price,
                c.name AS category_name,
                COALESCE(SUM(si.quantity), 0) AS sold_qty,
                COALESCE(SUM(si.total_price), 0) AS total_revenue
            FROM sale_items si
            JOIN sales s ON si.sale_id = s.id
            JOIN items i ON si.item_id = i.id
            LEFT JOIN categories c ON i.category_id = c.id
            WHERE DATE(s.created_at) BETWEEN ? AND ? AND s.store_id = ?
            GROUP BY i.id, i.name, i.barcode, i.retail_price, c.name
            ORDER BY sold_qty DESC
            LIMIT 10
        ", [$dateFrom, $dateTo, $storeId]);

        // 2. الأصناف الراكدة (التي لم تباع إطلاقاً خلال الفترة)
        $deadStock = self::$db->fetchAll("
            SELECT 
                i.id, i.name, i.barcode, i.cost_price, i.retail_price,
                c.name AS category_name,
                (SELECT COALESCE(balance_after, 0) FROM stock_moves sm WHERE sm.item_id = i.id AND sm.store_id = ? ORDER BY sm.id DESC LIMIT 1) AS current_stock
            FROM items i
            LEFT JOIN categories c ON i.category_id = c.id
            WHERE i.is_active = 1
              AND i.id NOT IN (
                  SELECT DISTINCT si.item_id 
                  FROM sale_items si 
                  JOIN sales s ON si.sale_id = s.id 
                  WHERE DATE(s.created_at) BETWEEN ? AND ? AND s.store_id = ?
              )
            ORDER BY current_stock DESC
            LIMIT 20
        ", [$storeId, $dateFrom, $dateTo, $storeId]);

        // 3. تنبيهات النواقص وحد إعادة الطلب (Reorder Level Alerts)
        $reorderAlerts = self::$db->fetchAll("
            SELECT 
                i.id, i.name, i.barcode, i.reorder_level, i.cost_price, i.retail_price,
                c.name AS category_name,
                COALESCE((SELECT balance_after FROM stock_moves sm WHERE sm.item_id = i.id AND sm.store_id = ? ORDER BY sm.id DESC LIMIT 1), 0) AS current_stock
            FROM items i
            LEFT JOIN categories c ON i.category_id = c.id
            WHERE i.is_active = 1
            HAVING current_stock <= i.reorder_level
            ORDER BY current_stock ASC
            LIMIT 30
        ", [$storeId]);

        return [
            'date_from'      => $dateFrom,
            'date_to'        => $dateTo,
            'store_id'       => $storeId,
            'top_sold'       => $topSold,
            'dead_stock'     => $deadStock,
            'reorder_alerts' => $reorderAlerts
        ];
    }

    /**
     * كشف حساب العميل التفصيلي (Customer Statement of Account)
     */
    public static function getCustomerStatement(int $customerId, string $dateFrom, string $dateTo): array {
        self::init();

        $customer = Customer::findById($customerId);
        if (!$customer) {
            throw new Exception("العميل غير موجود.");
        }

        // 1. حساب الرصيد السابق قبل تاريخ الفترة
        $prevSales = (float)self::$db->fetchValue("
            SELECT COALESCE(SUM(remaining_amount), 0) FROM sales 
            WHERE customer_id = ? AND DATE(created_at) < ?
        ", [$customerId, $dateFrom]);

        $prevReturns = (float)self::$db->fetchValue("
            SELECT COALESCE(SUM(total_refund), 0) FROM sales_returns 
            WHERE customer_id = ? AND return_date < ? AND refund_method = 'credit_balance'
        ", [$customerId, $dateFrom]);

        $openingPeriodBalance = (float)$customer['opening_balance'] + ($prevSales - $prevReturns);

        // 2. حركات فواتير المبيعات خلال الفترة
        $sales = self::$db->fetchAll("
            SELECT 
                s.id,
                DATE(s.created_at) AS trans_date,
                s.invoice_number AS trans_ref,
                'sale' AS trans_type,
                'فاتورة مبيعات' AS trans_label,
                s.grand_total,
                s.paid_amount,
                s.remaining_amount AS debit,
                0.00 AS credit,
                s.payment_method AS notes
            FROM sales s
            WHERE s.customer_id = ? AND DATE(s.created_at) BETWEEN ? AND ?
            ORDER BY s.created_at ASC, s.id ASC
        ", [$customerId, $dateFrom, $dateTo]);

        // 3. حركات مرتجعات المبيعات بالآجل
        $returns = self::$db->fetchAll("
            SELECT 
                sr.id,
                sr.return_date AS trans_date,
                sr.return_number AS trans_ref,
                'sale_return' AS trans_type,
                'مرتجع مبيعات' AS trans_label,
                sr.total_refund AS grand_total,
                sr.total_refund AS paid_amount,
                0.00 AS debit,
                sr.total_refund AS credit,
                sr.reason AS notes
            FROM sales_returns sr
            WHERE sr.customer_id = ? AND sr.return_date BETWEEN ? AND ? AND sr.refund_method = 'credit_balance'
            ORDER BY sr.return_date ASC, sr.id ASC
        ", [$customerId, $dateFrom, $dateTo]);

        $transactions = array_merge($sales, $returns);
        usort($transactions, function($a, $b) {
            $cmp = strcmp($a['trans_date'], $b['trans_date']);
            return $cmp !== 0 ? $cmp : ($a['id'] <=> $b['id']);
        });

        $runningBalance = $openingPeriodBalance;
        $totalSales = 0.0;
        $totalPaid = 0.0;

        foreach ($transactions as &$tr) {
            $deb = (float)$tr['debit'];
            $cred = (float)$tr['credit'];
            $runningBalance += ($deb - $cred);
            $tr['balance_after'] = $runningBalance;

            if ($tr['trans_type'] === 'sale') {
                $totalSales += (float)$tr['grand_total'];
                $totalPaid += (float)$tr['paid_amount'];
            }
        }
        unset($tr);

        return [
            'customer'              => $customer,
            'date_from'             => $dateFrom,
            'date_to'               => $dateTo,
            'opening_period_balance'=> $openingPeriodBalance,
            'closing_period_balance'=> $runningBalance,
            'total_sales'           => $totalSales,
            'total_paid'            => $totalPaid,
            'transactions'          => $transactions
        ];
    }
}
