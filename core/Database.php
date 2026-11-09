<?php
/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * كلاس الاتصال وإدارة المعاملات الذرية لقاعدة البيانات (PDO Singleton)
 * 
 * هندسة وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
 */

class Database {
    private static ?Database $instance = null;
    private ?PDO $pdo = null;

    /**
     * منشئ الفئة الخاص بنمط Singleton
     */
    private function __construct() {
        $config = require ROOT_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
        
        $dsn = sprintf(
            "%s:host=%s;port=%d;dbname=%s;charset=%s",
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            // تسجيل الخطأ وتفادي كشف كلمات المرور في شاشة المستخدم
            if (class_exists('Logger')) {
                Logger::error("Database Connection Failed: " . $e->getMessage());
            }
            if (defined('APP_ENV') && APP_ENV === 'development') {
                die("خطأ في الاتصال بقاعدة البيانات: " . htmlspecialchars($e->getMessage()));
            } else {
                die("عذراً، حدث خطأ في الاتصال بقاعدة البيانات. يُرجى مراجعة مسؤول النظام.");
            }
        }
    }

    /**
     * استرجاع الكائن المفرد (Singleton Instance)
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * استرجاع كائن PDO الأصلي للعمليات المتقدمة
     */
    public function getPdo(): PDO {
        return $this->pdo;
    }

    /**
     * تنفيذ استعلام مجهز مع تمرير المعاملات (Safe Prepared Query)
     */
    public function query(string $sql, array $params = []): PDOStatement {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            Logger::error("SQL Execution Error: " . $e->getMessage() . " | SQL: " . $sql, $params);
            throw $e;
        }
    }

    /**
     * جلب كافة السجلات الناتجة
     */
    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * جلب سجل مفرد واحد فقط
     */
    public function fetchOne(string $sql, array $params = []): ?array {
        $result = $this->query($sql, $params)->fetch();
        return $result !== false ? $result : null;
    }

    /**
     * جلب قيمة حقل مفرد (Single Column)
     */
    public function fetchColumn(string $sql, array $params = [], int $column = 0): mixed {
        return $this->query($sql, $params)->fetchColumn($column);
    }

    /**
     * إدراج سجل جديد وإرجاع المعرف التلقائي
     */
    public function insert(string $table, array $data): int|string {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);
        
        $sql = sprintf(
            "INSERT INTO `%s` (`%s`) VALUES (%s)",
            $table,
            implode('`, `', $fields),
            implode(', ', $placeholders)
        );
        
        $params = [];
        foreach ($data as $key => $val) {
            $params[":$key"] = $val;
        }

        $this->query($sql, $params);
        return $this->pdo->lastInsertId();
    }

    /**
     * تحديث سجلات جدول بشرط مجهز
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int {
        $setClauses = [];
        $params = [];
        
        foreach ($data as $key => $val) {
            $setClauses[] = "`$key` = :set_$key";
            $params[":set_$key"] = $val;
        }

        foreach ($whereParams as $key => $val) {
            $params[$key] = $val;
        }

        $sql = sprintf("UPDATE `%s` SET %s WHERE %s", $table, implode(', ', $setClauses), $where);
        return $this->query($sql, $params)->rowCount();
    }

    /**
     * حذف سجلات من جدول بشرط مجهز
     */
    public function delete(string $table, string $where, array $whereParams = []): int {
        $sql = sprintf("DELETE FROM `%s` WHERE %s", $table, $where);
        return $this->query($sql, $whereParams)->rowCount();
    }

    /**
     * آخر معرف تم إدراجه
     */
    public function lastInsertId(): int|string {
        return $this->pdo->lastInsertId();
    }

    // =========================================================================
    // إدارة المعاملات الذرية المحاسبية (ACID Transactions Support)
    // =========================================================================

    public function beginTransaction(): bool {
        if (!$this->pdo->inTransaction()) {
            return $this->pdo->beginTransaction();
        }
        return true;
    }

    public function commit(): bool {
        if ($this->pdo->inTransaction()) {
            return $this->pdo->commit();
        }
        return false;
    }

    public function rollBack(): bool {
        if ($this->pdo->inTransaction()) {
            return $this->pdo->rollBack();
        }
        return false;
    }

    public function inTransaction(): bool {
        return $this->pdo->inTransaction();
    }

    /**
     * تنفيذ دالة كاملة داخل معاملة ذرية مع تراجع آلي عند أي خطأ
     */
    public function transaction(callable $callback): mixed {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Throwable $e) {
            $this->rollBack();
            Logger::error("Transaction Rolled Back: " . $e->getMessage());
            throw $e;
        }
    }
}

