<?php
/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * كلاس الأمان والتشفير ومكافحة الثغرات (Security & Cryptography)
 * 
 * هندسة وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
 */

class Security {

    /**
     * توليد رمز CSRF وحفظه في الجلسة
     */
    public static function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * اسم بديل لتوليد رمز CSRF
     */
    public static function generateCsrf(): string {
        return self::csrfToken();
    }

    /**
     * توليد حقل مخفي لرموز CSRF لاستخدامه داخل النماذج
     */
    public static function csrfInput(): string {
        $token = self::csrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * التحقق من صحة رمز CSRF المرسل مع الطلب
     */
    public static function validateCsrf(?string $token = null): bool {
        if ($token === null) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        }

        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * التحقق الصارم ورفض الطلب فوراً إذا كان الرمز غير صالح
     */
    public static function requireCsrf(): void {
        if (!self::validateCsrf()) {
            http_response_code(403);
            if (Helpers::isAjax()) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'message' => 'انتهت صلاحية جلسة الأمان (Invalid or Expired CSRF Token)، يُرجى تحديث الصفحة والمحاولة ثانية.'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                die("خطأ أمني: رمز التحقق من الطلب (CSRF Token) غير صالح أو منتهي الصلاحية.");
            }
            exit();
        }
    }

    /**
     * اسم بديل للتحقق الصارم من CSRF
     */
    public static function verifyCsrf(): void {
        self::requireCsrf();
    }

    /**
     * تنظيف وتعقيم النصوص من وسوم XSS
     */
    public static function escape(?string $string): string {
        return htmlspecialchars((string)$string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * تشفير نصوص حساسة بخوارزمية AES-256-CBC
     */
    public static function encrypt(string $data): string {
        $key = hash('sha256', APP_KEY);
        $ivLength = openssl_cipher_iv_length(APP_CIPHER);
        $iv = openssl_random_pseudo_bytes($ivLength);
        
        $encrypted = openssl_encrypt($data, APP_CIPHER, $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * فك تشفير النصوص المشفرة بـ AES-256-CBC
     */
    public static function decrypt(string $data): ?string {
        $data = base64_decode($data);
        $key = hash('sha256', APP_KEY);
        $ivLength = openssl_cipher_iv_length(APP_CIPHER);
        
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        
        $decrypted = openssl_decrypt($encrypted, APP_CIPHER, $key, 0, $iv);
        return $decrypted !== false ? $decrypted : null;
    }

    /**
     * توليد سلسلة عشوائية آمنة
     */
    public static function randomString(int $length = 16): string {
        return bin2hex(random_bytes((int)ceil($length / 2)));
    }
}

