<?php
/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * كلاس سجلات النظام والرقابة الداخلية (Logger & Audit Trail)
 * 
 * هندسة وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
 */

class Logger {

    /**
     * تسجيل عملية في سجل التدقيق والرقابة (Audit Trail) في قاعدة البيانات
     */
    public static function audit(
        string $action,
        string $module,
        ?int $recordId = null,
        mixed $oldValues = null,
        mixed $newValues = null
    ): void {
        try {
            $db = Database::getInstance();
            $userId = Auth::id();

            $db->insert('audit_logs', [
                'user_id'    => $userId,
                'action'     => $action,
                'module'     => $module,
                'record_id'  => $recordId,
                'old_values' => $oldValues !== null ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
                'new_values' => $newValues !== null ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Throwable $e) {
            // تفادي إيقاف النظام في حال فشل تسجيل التدقيق، مع حفظه في ملف نصي كنسخة احتياطية
            self::error("Audit Log Failure: " . $e->getMessage());
        }
    }

    /**
     * تسجيل خطأ في ملف السجلات
     */
    public static function error(string $message, array $context = []): void {
        self::writeLog('ERROR', $message, $context);
    }

    /**
     * تسجيل معلومة تشغيلية
     */
    public static function info(string $message, array $context = []): void {
        self::writeLog('INFO', $message, $context);
    }

    /**
     * كتابة السجل في مجلد storage/logs
     */
    private static function writeLog(string $level, string $message, array $context = []): void {
        $logDir = ROOT_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $formatted = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;

        @file_put_contents($logFile, $formatted, FILE_APPEND | LOCK_EX);
    }
}

