<?php
/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * كلاس الدوال المساعدة العامة (System Helpers)
 * 
 * هندسة وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
 */

class Helpers {

    /**
     * هل الطلب الحالي هو طلب AJAX؟
     */
    public static function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               (!empty($_SERVER['HTTP_ACCEPT']) && 
                str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));
    }

    /**
     * تنسيق المبالغ المالية بالجنيه المصري القياسي
     */
    public static function formatCurrency(float|int|string|null $amount, bool $showSymbol = true): string {
        $val = (float)($amount ?? 0);
        $formatted = number_format($val, CURRENCY_DECIMALS, '.', ',');
        return $showSymbol ? $formatted . ' ' . APP_CURRENCY : $formatted;
    }

    /**
     * تنسيق التواريخ للعرض (يوم/شهر/سنة)
     */
    public static function formatDate(?string $date, string $format = 'd/m/Y'): string {
        if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return '—';
        }
        $timestamp = strtotime($date);
        return $timestamp ? date($format, $timestamp) : '—';
    }

    /**
     * تنسيق التاريخ والوقت مع التوقيت العربي
     */
    public static function formatDateTime(?string $date): string {
        if (empty($date) || $date === '0000-00-00 00:00:00') {
            return '—';
        }
        $timestamp = strtotime($date);
        if (!$timestamp) return '—';
        
        $formatted = date('d/m/Y h:i', $timestamp);
        $ampm = date('A', $timestamp) === 'AM' ? 'ص' : 'م';
        return $formatted . ' ' . $ampm;
    }

    /**
     * إرسال استجابة JSON موحدة لنقاط الـ API
     */
    public static function jsonResponse(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    /**
     * فك شفرة باركود الموازين الإلكترونية (Weight-Embedded Barcode Decoder)
     * يدعم الصيغ القياسية للسوبرماركت واللحوم (التي تبدأ بـ 99 أو 20 أو 21)
     * مثال: 20 + كود الصنف (5 أرقام) + الوزن بالجرام (5 أرقام) + خانة التحقق (1 رقم)
     * 2010500012503 -> كود الصنف: 10500، الوزن: 1.250 كجم
     */
    public static function parseScaleBarcode(string $barcode): ?array {
        $barcode = trim($barcode);
        $len = strlen($barcode);

        // صيغة EAN-13 للموازين المدمجة (13 رقم)
        if ($len === 13) {
            $prefix = substr($barcode, 0, 2);
            if (in_array($prefix, ['20', '21', '22', '99'], true)) {
                $itemCode = substr($barcode, 2, 5);      // 5 أرقام لكود الصنف
                $weightGrams = (int)substr($barcode, 7, 5); // 5 أرقام للوزن بالجرامات
                $weightKg = round($weightGrams / 1000, 3); // تحويل لكيلوجرام

                return [
                    'is_scale'     => true,
                    'raw_barcode'  => $barcode,
                    'prefix'       => $prefix,
                    'item_code'    => $itemCode,
                    'weight_kg'    => $weightKg,
                    'weight_grams' => $weightGrams,
                ];
            }
        }

        return null;
    }

    /**
     * استرجاع قيمة إعداد من جدول الإعدادات العامة مع قيمة افتراضية
     */
    public static function setting(string $key, mixed $default = null): mixed {
        static $settingsCache = null;
        if ($settingsCache === null) {
            try {
                $db = Database::getInstance();
                $rows = $db->fetchAll("SELECT `key`, `value` FROM `system_settings`");
                $settingsCache = [];
                foreach ($rows as $r) {
                    $settingsCache[$r['key']] = $r['value'];
                }
            } catch (Exception $e) {
                return $default;
            }
        }
        return $settingsCache[$key] ?? $default;
    }
}

