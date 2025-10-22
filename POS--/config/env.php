 <?php

  // تحميل اختياري لـ phpdotenv إن وُجد عبر Composer
  $autoload = __DIR__ . '/../vendor/autoload.php';
  if (is_file($autoload)) {
    require_once $autoload;
    if (class_exists('Dotenv\\Dotenv')) {
      try {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->safeLoad();
      } catch (Throwable $e) {
        // تجاهل أي أخطاء تحميل .env من phpdotenv
      }
    }
  }

  // بسيط لتحميل متغيرات البيئة من ملف .env عند عدم توفر phpdotenv
  if (!function_exists('load_env')) {
    function load_env($path)
    {
      if (!is_file($path) || !is_readable($path)) return;
      $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      foreach ($lines as $line) {
        if (strpos(ltrim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) continue;
        $key = trim($parts[0]);
        $val = trim($parts[1]);
        $val = trim($val, "\"' ");
        if ($key === '') continue;
        if (getenv($key) === false) {
          putenv($key . '=' . $val);
          $_ENV[$key] = $val;
          $_SERVER[$key] = $val;
        }
      }
    }
  }

  $envFile = __DIR__ . '/../.env';
  if (!getenv('APP_ENV')) { // حمّل يدويًا فقط عند غياب phpdotenv
    load_env($envFile);
  }

  function env($key, $default = null)
  {
    $v = getenv($key);
    return $v === false ? $default : $v;
  }
