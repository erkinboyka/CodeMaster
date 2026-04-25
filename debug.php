<?php
$ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
if (!in_array($ip, ['127.0.0.1', '::1'], true)) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/config.php';

// Проверка подключения к БД (используем настройки из config.php)
try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;port=%d;charset=utf8mb4', DB_HOST, DB_NAME, (int) (defined('DB_PORT') ? DB_PORT : 3306));
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
echo "Подключение к БД успешно";
} catch (Exception $e) {
    echo "Ошибка БД.";
}

echo "<br>APP_ENV: " . htmlspecialchars(getenv('APP_ENV') ?: 'production');
echo "<br>APP_URL: " . htmlspecialchars((string) (defined('APP_URL') ? APP_URL : ''));

// Проверка сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['test'] = 'работает';
echo "<br>Сессия работает";

// Проверка прав на запись
if (is_writable('.')) {
    echo "<br>Папка доступна для записи";
} else {
    echo "<br>Нет прав на запись";
}
?>

