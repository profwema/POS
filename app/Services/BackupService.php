<?php
/**
 * خدمات النسخ الاحتياطي لقاعدة البيانات - Automated Database Backup Service
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

class BackupService {
    private static Database $db;

    private static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * المسار المعتمد لتخزين ملفات النسخ الاحتياطي
     */
    public static function getBackupDir(): string {
        $dir = STORAGE_PATH . DIRECTORY_SEPARATOR . 'backups';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /**
     * إنشاء نسخة احتياطية كاملة وشاملة لجميع الجداول والبيانات (.sql)
     */
    public static function createBackup(): array {
        self::init();
        $pdo = self::$db->getPdo();
        $backupDir = self::getBackupDir();

        $filename = 'backup_wamtech_pos_' . date('Ymd_His') . '.sql';
        $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);

        $sqlDump = "-- ============================================================================\n";
        $sqlDump .= "-- WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition) - Database Backup\n";
        $sqlDump .= "-- Brand: Almubarmig (Eng. Weaam Mohammed El-Sudani) - https://almubarmig.com\n";
        $sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sqlDump .= "-- Host: 127.0.0.1 | Database: wamtech_pos_db\n";
        $sqlDump .= "-- ============================================================================\n\n";
        $sqlDump .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $sqlDump .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $sqlDump .= "SET NAMES utf8mb4;\n\n";

        foreach ($tables as $table) {
            $sqlDump .= "-- --------------------------------------------------------\n";
            $sqlDump .= "-- Table structure for table `{$table}`\n";
            $sqlDump .= "-- --------------------------------------------------------\n";
            $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n";

            $createTableStmt = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
            $sqlDump .= $createTableStmt['Create Table'] . ";\n\n";

            // Data extraction
            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $sqlDump .= "-- Dumping data for table `{$table}`\n";
                $columns = array_keys($rows[0]);
                $quotedColumns = array_map(fn($c) => "`{$c}`", $columns);
                $columnsList = implode(', ', $quotedColumns);

                $insertHeader = "INSERT INTO `{$table}` ({$columnsList}) VALUES\n";
                $valuesList = [];

                foreach ($rows as $row) {
                    $escapedValues = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $escapedValues[] = 'NULL';
                        } else {
                            $escapedValues[] = $pdo->quote($val);
                        }
                    }
                    $valuesList[] = '(' . implode(', ', $escapedValues) . ')';
                }

                // Chunk into inserts of 100 rows
                $chunks = array_chunk($valuesList, 100);
                foreach ($chunks as $chunk) {
                    $sqlDump .= $insertHeader . implode(",\n", $chunk) . ";\n\n";
                }
            }
        }

        $sqlDump .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $sqlDump .= "-- Backup Completed Successfully.\n";

        file_put_contents($filepath, $sqlDump);

        Logger::audit('database_backup_created', 'system', null, null, [
            'filename' => $filename,
            'size'     => filesize($filepath)
        ]);

        return [
            'success'   => true,
            'filename'  => $filename,
            'filepath'  => $filepath,
            'size'      => filesize($filepath),
            'formatted' => self::formatSize(filesize($filepath)),
            'message'   => "تم إنشاء النسخة الاحتياطية بنجاح ({$filename})"
        ];
    }

    /**
     * استرجاع قائمة ملفات النسخ الاحتياطي المخزنة
     */
    public static function getBackupsList(): array {
        $backupDir = self::getBackupDir();
        $files = glob($backupDir . DIRECTORY_SEPARATOR . '*.sql');
        $backups = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $size = filesize($file);
            $mtime = filemtime($file);

            $backups[] = [
                'filename'       => $filename,
                'size'           => $size,
                'size_formatted' => self::formatSize($size),
                'created_at'     => date('Y-m-d H:i:s', $mtime),
                'age'            => self::timeAgo($mtime)
            ];
        }

        // ترتيب الأحدث أولاً
        usort($backups, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        return $backups;
    }

    /**
     * تنزيل ملف النسخة الاحتياطية إلى جهاز المستخدم
     */
    public static function downloadBackup(string $filename): void {
        $cleanFilename = basename($filename);
        $filepath = self::getBackupDir() . DIRECTORY_SEPARATOR . $cleanFilename;

        if (!file_exists($filepath) || !str_ends_with($cleanFilename, '.sql')) {
            die("الملف المطلوب غير موجود أو غير صالح.");
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $cleanFilename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }

    /**
     * حذف ملف نسخة احتياطية
     */
    public static function deleteBackup(string $filename): bool {
        $cleanFilename = basename($filename);
        $filepath = self::getBackupDir() . DIRECTORY_SEPARATOR . $cleanFilename;

        if (file_exists($filepath) && str_ends_with($cleanFilename, '.sql')) {
            unlink($filepath);
            Logger::audit('database_backup_deleted', 'system', null, null, ['filename' => $cleanFilename]);
            return true;
        }
        return false;
    }

    private static function formatSize(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    private static function timeAgo(int $timestamp): string {
        $diff = time() - $timestamp;
        if ($diff < 60) return 'منذ لحظات';
        if ($diff < 3600) return 'منذ ' . floor($diff / 60) . ' دقيقة';
        if ($diff < 86400) return 'منذ ' . floor($diff / 3600) . ' ساعة';
        return 'منذ ' . floor($diff / 86400) . ' يوم';
    }
}
