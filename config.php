    <?php

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    } else {
        session_set_cookie_params(0, '/; samesite=Lax', '', $isHttps, true);
    }
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    ini_set('default_charset', 'UTF-8');
    if (function_exists('mb_internal_encoding')) {
        mb_internal_encoding('UTF-8');
    }
    header('Content-Type: text/html; charset=UTF-8');
    require_once __DIR__ . '/includes/i18n.php';

    $env = static function (string $key, $default = '') {
        $val = getenv($key);
        if ($val === false || $val === '') {
            return $default;
        }
        return $val;
    };

    $config = [
        // Настройки приложения
        'APP_NAME' => $env('APP_NAME', 'CodeMaster'),
        'APP_VERSION' => $env('APP_VERSION', '1.0.0'),
        'APP_URL' => $env('APP_URL', 'http://localhost/CodeMaster'),

        // Настройки базы данных
        'DB_HOST' => $env('DB_HOST', '127.0.0.1'),
        'DB_PORT' => (int) $env('DB_PORT', 3306),
        'DB_USER' => $env('DB_USER', 'root'),
        'DB_PASS' => $env('DB_PASS', ''),
        'DB_NAME' => $env('DB_NAME', 'talentflow'),

        // Настройки PostgreSQL (для SQL-тренажеров). Оставьте PG_DB пустым, чтобы отключить.
        'PG_HOST' => $env('PG_HOST', 'localhost'),
        'PG_PORT' => (int) $env('PG_PORT', 5432),
        'PG_USER' => $env('PG_USER', 'postgres'),
        'PG_PASS' => $env('PG_PASS', 'postgres'),
        'PG_DB' => $env('PG_DB', 'ITsphere360_pg'),

        // OAuth / reCAPTCHA
        'GOOGLE_CLIENT_ID' => $env('GOOGLE_CLIENT_ID', '460861424063-pv64ainjg03scatfivjc6vbim8fgnmrk.apps.googleusercontent.com'),
        'GOOGLE_SECRET' => $env('GOOGLE_SECRET', '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'),
        'RECAPTCHA_SITE_KEY' => $env('RECAPTCHA_SITE_KEY', '6LdrLZIsAAAAALxvIpT8UetQjOLYlo9R1skgGGtH'),
        'RECAPTCHA_SECRET_KEY' => $env('RECAPTCHA_SECRET_KEY', '6LdrLZIsAAAAAERwpO7IyNICIfhJYjJUcENxb_9n'),

        // Judge0 (локальный/удаленный сервис проверки кода)
        'JUDGE0_URL' => $env('JUDGE0_URL', 'https://ce.judge0.com'),
        'JUDGE0_TOKEN' => $env('JUDGE0_TOKEN', ''),

        // Gemini API
        'GEMINI_API_KEY' => $env('GEMINI_API_KEY', 'AIzaSyCY2KvRsSL5bHtJVJ88cYT7qntQlIpGJNs'),
        'GEMINI_API_KEY_2' => $env('GEMINI_API_KEY_2', 'AIzaSyBDN5LDK3aNuStTiUZ8_RMWvEEN8THP7p4'),
        'GEMINI_API_KEY_3' => $env('GEMINI_API_KEY_3', 'AIzaSyDabdiwCxyW0243Spvj6xph5ggm7Zf500s'),
        'GEMINI_API_KEY_4' => $env('GEMINI_API_KEY_4', 'AIzaSyDmcARN4HwIuPI_gyfXVIXLK2eEXufJOvY'),
        'GEMINI_API_KEY_5' => $env('GEMINI_API_KEY_5', 'AIzaSyBji6lhFBHF0D9V9YdYXdE2qBjRceZHMQQ'),
        'GEMINI_API_KEY_6' => $env('GEMINI_API_KEY_6', 'AIzaSyBuTTXWP2CfAI8o0bOKchzXcui16pLrKVM'),
        'GEMINI_MODEL' => $env('GEMINI_MODEL', 'gemini-2.5-flash'),

        // Настройки безопасности
        'SESSION_LIFETIME' => 86400,
        'MAX_LOGIN_ATTEMPTS' => 5,
        'LOCKOUT_TIME' => 900,

        // Настройки загрузки файлов
        'UPLOAD_DIR' => __DIR__ . '/uploads/',
        'MAX_FILE_SIZE' => 5242880,
        'ALLOWED_EXTENSIONS' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],

        // Настройки почты (для уведомлений)
        'MAIL_FROM' => 'noreply@ITsphere360.com',
        'MAIL_FROM_NAME' => 'ITsphere360',

        // Настройки пагинации
        'ITEMS_PER_PAGE' => 10,
    ];

    if (!function_exists('tfGetCsrfToken')) {
        function tfGetCsrfToken(): string
        {
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            return (string) $_SESSION['csrf_token'];
        }
    }

    if (!function_exists('tfEnsureCsrfCookie')) {
        function tfEnsureCsrfCookie(): void
        {
            $token = tfGetCsrfToken();
            $params = [
                'expires' => time() + 3600 * 24,
                'path' => '/',
                'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
                'httponly' => false,
                'samesite' => 'Lax'
            ];
            setcookie('XSRF-TOKEN', $token, $params);
        }
    }

    tfEnsureCsrfCookie();

    if (!function_exists('tfDefine')) {
        function tfDefine(string $name, $value): void
        {
            if (!defined($name)) {
                define($name, $value);
            }
        }
    }

    // Настройки базы данных
    tfDefine('DB_HOST', $config['DB_HOST']);
    tfDefine('DB_PORT', (int) $config['DB_PORT']);
    tfDefine('DB_USER', $config['DB_USER']);
    tfDefine('DB_PASS', $config['DB_PASS']);
    tfDefine('DB_NAME', $config['DB_NAME']);

    // Настройки PostgreSQL (для SQL-тренажеров). Оставьте PG_DB пустым, чтобы отключить.
    tfDefine('PG_HOST', $config['PG_HOST']);
    tfDefine('PG_PORT', (int) $config['PG_PORT']);
    tfDefine('PG_USER', $config['PG_USER']);
    tfDefine('PG_PASS', $config['PG_PASS']);
    tfDefine('PG_DB', $config['PG_DB']);

    // Настройки приложения
    tfDefine('APP_NAME', $config['APP_NAME']);
    tfDefine('APP_VERSION', $config['APP_VERSION']);
    tfDefine('APP_URL', $config['APP_URL']);

    // OAuth / reCAPTCHA
    // Fill these values in Google Cloud Console and reCAPTCHA admin console.
    tfDefine('GOOGLE_CLIENT_ID', $config['GOOGLE_CLIENT_ID']);
    tfDefine('GOOGLE_SECRET', $config['GOOGLE_SECRET']);
    tfDefine('RECAPTCHA_SITE_KEY', $config['RECAPTCHA_SITE_KEY']);
    tfDefine('RECAPTCHA_SECRET_KEY', $config['RECAPTCHA_SECRET_KEY']);

    // Judge0 (локальный/удаленный сервис проверки кода)
    tfDefine('JUDGE0_URL', $config['JUDGE0_URL']);
    tfDefine('JUDGE0_TOKEN', $config['JUDGE0_TOKEN']);

    // Настройки безопасности
    tfDefine('SESSION_LIFETIME', (int) $config['SESSION_LIFETIME']); // 24 часа
    tfDefine('MAX_LOGIN_ATTEMPTS', (int) $config['MAX_LOGIN_ATTEMPTS']);
    tfDefine('LOCKOUT_TIME', (int) $config['LOCKOUT_TIME']); // 15 минут

    // Настройки загрузки файлов
    tfDefine('UPLOAD_DIR', $config['UPLOAD_DIR']);
    tfDefine('MAX_FILE_SIZE', (int) $config['MAX_FILE_SIZE']); // 5MB
    tfDefine('ALLOWED_EXTENSIONS', $config['ALLOWED_EXTENSIONS']);

    // Настройки почты (для уведомлений)
    tfDefine('MAIL_FROM', $config['MAIL_FROM']);
    tfDefine('MAIL_FROM_NAME', $config['MAIL_FROM_NAME']);

    // Настройки пагинации
    tfDefine('ITEMS_PER_PAGE', (int) $config['ITEMS_PER_PAGE']);
    // Gemini API
    tfDefine('GEMINI_API_KEY', $config['GEMINI_API_KEY']);
    tfDefine('GEMINI_API_KEY_2', $config['GEMINI_API_KEY_2']);
    tfDefine('GEMINI_API_KEY_3', $config['GEMINI_API_KEY_3']);
    tfDefine('GEMINI_API_KEY_4', $config['GEMINI_API_KEY_4']);
    tfDefine('GEMINI_API_KEY_5', $config['GEMINI_API_KEY_5']);
    tfDefine('GEMINI_API_KEY_6', $config['GEMINI_API_KEY_6']);
    tfDefine('GEMINI_MODEL', $config['GEMINI_MODEL']);


    // Временная зона
    date_default_timezone_set('Europe/Moscow');

    // Обработка ошибок
    $appEnv = getenv('APP_ENV') ?: 'production';
    error_reporting(E_ALL);
    ini_set('display_errors', $appEnv === 'development' ? '1' : '0');
    ini_set('display_startup_errors', $appEnv === 'development' ? '1' : '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/logs/error.log');

    // Проверка и создание необходимых директорий
    $dirs = [UPLOAD_DIR, __DIR__ . '/logs/'];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
