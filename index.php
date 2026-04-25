<?php
error_reporting(E_ALL);
ini_set('display_errors', (getenv('APP_ENV') === 'development') ? '1' : '0');

// Главный входной файл приложения CodeMaster
define('APP_INIT', true);
header('Content-Type: text/html; charset=utf-8');

// Подключаем конфигурацию и функции

require_once 'config.php';
require_once 'functions.php';
require_once 'handlers.php';

// Инициализация базы данных при первом запуске
if (!isset($_SESSION['db_initialized'])) {
    try {
        initDatabase();
        $_SESSION['db_initialized'] = true;
    } catch (Exception $e) {
        error_log('[TF_INIT] ' . $e->getMessage());
        die("Ошибка инициализации базы данных.");
    }
}

try {
    ensureCoursesSchema(getDBConnection());
    ensureLessonsSchema(getDBConnection());
    ensurePracticeSchema(getDBConnection());
    ensureCourseExamsSchema(getDBConnection());
    ensureCertificatesSchema(getDBConnection());
    ensureContestsSchema(getDBConnection());
    ensureCommunitySchema(getDBConnection());
    ensureVacancyCurrencySchema(getDBConnection());
    ensureUserPortfolioSchema(getDBConnection());
    ensureSkillAssessmentSchema(getDBConnection());
    ensureInterviewsSchema(getDBConnection());
} catch (Exception $e) {

}

$action = $_GET['action'] ?? null;
if ($action === null || $action === '') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = rtrim($path, '/');
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $pathAfterBase = $path;
    if ($base !== '' && strpos($path, $base) === 0) {
        $pathAfterBase = substr($path, strlen($base));
    }
    $pathAfterBase = ltrim($pathAfterBase, '/');
    $script = basename($_SERVER['SCRIPT_NAME']);
    if ($pathAfterBase !== '' && $pathAfterBase !== $script) {
        http_response_code(404);
        require 'templates/404.php';
        exit;
    }
}

routeRequest();

