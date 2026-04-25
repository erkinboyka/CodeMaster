<?php
if (!defined('FUNCTIONS_LOADED')) {
    define('FUNCTIONS_LOADED', true);

    // Подключение к базе данных
    function getDBConnection()
    {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            error_log('[TF_DB] ' . $e->getMessage());
            die("Ошибка подключения к базе данных.");
        }
    }

    // ИниС†иалиР·аС†ия базы данных и создание таблиц
    function initDatabase()
    {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        // Создание базы данных
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE " . DB_NAME);

        // Таблица пользователей (создаём ПЕРВОЙ)
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        role ENUM('seeker', 'recruiter', 'admin') DEFAULT 'seeker',
        title VARCHAR(255),
        location VARCHAR(255),
        bio TEXT,
        avatar VARCHAR(255),
        google_locale VARCHAR(32),
        is_verified BOOLEAN DEFAULT FALSE,
        is_blocked BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        failed_login_attempts INT DEFAULT 0,
        locked_until TIMESTAMP NULL,
        INDEX idx_email (email),
        INDEX idx_role (role)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Остальные таблицы...
        // Таблица навыков пользователей
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS user_skills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        skill_name VARCHAR(100) NOT NULL,
        skill_level INT DEFAULT 0,
        category ENUM('technical', 'soft') DEFAULT 'technical',
        endorsements INT DEFAULT 0,
        is_verified TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_skill_name (skill_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица опыта работы
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS user_experience (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        position VARCHAR(255) NOT NULL,
        company VARCHAR(255) NOT NULL,
        start_date VARCHAR(50),
        end_date VARCHAR(50),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица образования
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS user_education (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        degree VARCHAR(255) NOT NULL,
        institution VARCHAR(255) NOT NULL,
        start_date VARCHAR(50),
        end_date VARCHAR(50),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица портфолио
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS user_portfolio (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        category VARCHAR(100),
        image_url VARCHAR(500),
        github_url VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица курсов
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        instructor VARCHAR(255) NOT NULL,
        description TEXT,
        category ENUM('frontend', 'backend', 'design', 'devops', 'other') DEFAULT 'frontend',
        level ENUM('Начальный', 'Средний', 'Продвинутый') DEFAULT 'Начальный',
        progress INT DEFAULT 0,
        image_url VARCHAR(500),
        materials_title VARCHAR(255),
        materials_url VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_category (category),
        INDEX idx_level (level)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица уроков
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS lessons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        type ENUM('video', 'article', 'quiz') DEFAULT 'video',
        content TEXT,
        video_url VARCHAR(500),
        materials_title VARCHAR(255),
        materials_url VARCHAR(500),
        completed BOOLEAN DEFAULT FALSE,
        order_num INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_course_id (course_id),
        INDEX idx_order (order_num)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица вопросов для квизов
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS quiz_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lesson_id INT NOT NULL,
        question_text TEXT NOT NULL,
        correct_option INT,
        correct_options TEXT NULL,
        hint TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_lesson_id (lesson_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица вариантов ответов
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS quiz_options (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_id INT NOT NULL,
        option_text TEXT NOT NULL,
        option_order INT DEFAULT 0,
        INDEX idx_question_id (question_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица мини-тестов по урокам (JSON)
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS lesson_tests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lesson_id INT NOT NULL,
        test_json LONGTEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_lesson_id (lesson_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица финальных экзаменов по курсам (JSON)
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS course_exams (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        exam_json LONGTEXT NOT NULL,
        time_limit_minutes INT DEFAULT 60,
        pass_percent INT DEFAULT 70,
        shuffle_questions BOOLEAN DEFAULT TRUE,
        shuffle_options BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_course_id (course_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица навыков курсов
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS course_skills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        skill_name VARCHAR(100) NOT NULL,
        skill_level INT DEFAULT 0,
        INDEX idx_course_id (course_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица результатов курсов пользователей
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS user_course_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        course_id INT NOT NULL,
        progress INT DEFAULT 0,
        completed BOOLEAN DEFAULT FALSE,
        started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        completed_at TIMESTAMP NULL,
        UNIQUE KEY unique_user_course (user_id, course_id),
        INDEX idx_user_id (user_id),
        INDEX idx_course_id (course_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица результатов уроков пользователей
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS user_lesson_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        lesson_id INT NOT NULL,
        completed BOOLEAN DEFAULT FALSE,
        completed_at TIMESTAMP NULL,
        UNIQUE KEY unique_user_lesson (user_id, lesson_id),
        INDEX idx_user_id (user_id),
        INDEX idx_lesson_id (lesson_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица вакансий
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS vacancies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        company VARCHAR(255) NOT NULL,
        location VARCHAR(255),
        type ENUM('remote', 'office', 'hybrid') DEFAULT 'remote',
        salary_min INT,
        salary_max INT,
        salary_currency VARCHAR(3) DEFAULT 'TJS',
        description TEXT,
        company_description TEXT,
        verified BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_type (type),
        INDEX idx_salary (salary_min, salary_max)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица навыков вакансий
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS vacancy_skills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vacancy_id INT NOT NULL,
        skill_name VARCHAR(100) NOT NULL,
        INDEX idx_vacancy_id (vacancy_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица требований вакансий
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS vacancy_requirements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vacancy_id INT NOT NULL,
        requirement_text TEXT NOT NULL,
        INDEX idx_vacancy_id (vacancy_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица плюсов вакансий
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS vacancy_pluses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vacancy_id INT NOT NULL,
        plus_text TEXT NOT NULL,
        INDEX idx_vacancy_id (vacancy_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица обязанностей вакансий
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS vacancy_responsibilities (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vacancy_id INT NOT NULL,
        responsibility_text TEXT NOT NULL,
        INDEX idx_vacancy_id (vacancy_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица откликов пользователей
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS user_applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        vacancy_id INT NOT NULL,
        status ENUM('applied', 'interview', 'offer', 'rejected') DEFAULT 'applied',
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица сертификатов
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        course_id INT NOT NULL,
        cert_hash VARCHAR(100) DEFAULT NULL,
        certificate_name VARCHAR(255) NOT NULL,
        issuer VARCHAR(255),
        issue_date VARCHAR(50),
        certificate_url VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Roadmap: узлы
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS roadmap_nodes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        roadmap_title VARCHAR(255) DEFAULT 'Основной',
        topic VARCHAR(255) DEFAULT NULL,
        materials TEXT,
        x INT NOT NULL DEFAULT 0,
        y INT NOT NULL DEFAULT 0,
        deps TEXT,
        is_exam TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_roadmap_title (roadmap_title)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Roadmap: уроки
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS roadmap_lessons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        node_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        video_url VARCHAR(500),
        description TEXT,
        materials TEXT,
        order_index INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_node_id (node_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Roadmap: вопросы теста
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS roadmap_quiz_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        node_id INT NOT NULL,
        question TEXT NOT NULL,
        options TEXT NOT NULL,
        correct_answer VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_node_id (node_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Roadmap: прогресс
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS roadmap_user_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        node_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_user_node (user_id, node_id),
        INDEX idx_user_id (user_id),
        INDEX idx_node_id (node_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Roadmap: сертификаты
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS roadmap_certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        node_id INT NOT NULL,
        cert_hash VARCHAR(100) NOT NULL,
        issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_roadmap_cert_hash (cert_hash),
        UNIQUE KEY uniq_roadmap_user_node (user_id, node_id),
        INDEX idx_user_id (user_id),
        INDEX idx_node_id (node_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица активности пользователей
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS user_activities (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        activity_type ENUM('course', 'vacancy', 'application', 'lesson', 'certificate') DEFAULT 'course',
        activity_text TEXT NOT NULL,
        activity_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_time (activity_time)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица уведомлений
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        message TEXT NOT NULL,
        notification_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_read BOOLEAN DEFAULT FALSE,
        INDEX idx_user_id (user_id),
        INDEX idx_read (is_read)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица чата с AI
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS chat_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        sender ENUM('user', 'ai') DEFAULT 'user',
        message_text TEXT NOT NULL,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_sender (sender)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица сессий
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS sessions (
        id VARCHAR(128) PRIMARY KEY,
        user_id INT NOT NULL,
        session_data TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIMESTAMP NULL,
        INDEX idx_expires (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Дополнительные схемы (вызываем после создания базовых таблиц)
        $pdo2 = getDBConnection();
        ensureUserProfileSchema($pdo2);
        ensureUserPortfolioSchema($pdo2);
        ensureSkillAssessmentSchema($pdo2);
        ensurePracticeSchema($pdo2);
        ensureRoadmapTables($pdo2);
        ensureVacancyChatTables($pdo2);
    }

    // ИСПРАВЛЕНА: Безопасное создание таблиц чата и отзывов без ошибки 150
    function ensureVacancyChatTables($pdo)
    {
        // Проверяем существование таблицы users
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() === 0) {
            return; // Таблица ещё не создана — выходим
        }

        // Создаём таблицу платформенных отзывов БЕЗ внешнего ключа в основном определении
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS platform_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL UNIQUE,
        rating TINYINT NOT NULL,
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица чатов по вакансиям — без внешних ключей
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS vacancy_chats (
        id INT AUTO_INCREMENT PRIMARY KEY,
        application_id INT NOT NULL,
        sender_id INT NOT NULL,
        message_text TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_application_id (application_id),
        INDEX idx_sender_id (sender_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Таблица документов по вакансиям — без внешних ключей
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS vacancy_documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        application_id INT NOT NULL,
        uploader_id INT NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        original_name VARCHAR(255) NOT NULL,
        mime_type VARCHAR(100),
        size_bytes INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_application_id (application_id),
        INDEX idx_uploader_id (uploader_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        // Добавляем недостающие столбцы в таблицы вакансий и откликов
        try {
            $stmt = $pdo->prepare("
            SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'vacancies'
        ");
            $stmt->execute();
            $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('owner_id', $cols, true)) {
                $pdo->exec("ALTER TABLE vacancies ADD COLUMN owner_id INT NULL");
                $pdo->exec("ALTER TABLE vacancies ADD INDEX idx_owner_id (owner_id)");
            }
        } catch (Throwable $e) {
        }

        try {
            $stmt = $pdo->prepare("
            SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'user_applications'
        ");
            $stmt->execute();
            $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('employment_status', $cols, true)) {
                $pdo->exec("ALTER TABLE user_applications ADD COLUMN employment_status ENUM('pending','successful','unsuccessful') DEFAULT 'pending'");
            }
            if (!in_array('employment_updated_at', $cols, true)) {
                $pdo->exec("ALTER TABLE user_applications ADD COLUMN employment_updated_at TIMESTAMP NULL");
            }
        } catch (Throwable $e) {
        }
    }

    // ИСПРАВЛЕНА: Проверка существования таблицы перед запросом
    function getCurrentUser()
    {
        if (!isLoggedIn()) {
            return null;
        }

        $pdo = getDBConnection();

        // Проверяем существование таблицы users
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() === 0) {
            initDatabase(); // ИниС†иалиР·ирСѓРµм БД если таблиц нет
            // После инициализации повторно получаем соединение
            $pdo = getDBConnection();
        }

        // Теперь безопасно работаем с таблицами
        ensureVacancyChatTables($pdo);
        ensureUserProfileSchema($pdo);
        ensureUserPortfolioSchema($pdo);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND email = ?");
        $stmt->execute([$_SESSION['user_id'], $_SESSION['user_email']]);
        $user = $stmt->fetch();

        if ($user) {
            $user = tfDecorateUserCountry($user);
            // Получаем навыки пользователя
            $stmt = $pdo->prepare("SELECT * FROM user_skills WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $user['skills'] = $stmt->fetchAll();

            // Получаем опыт работы
            $stmt = $pdo->prepare("SELECT * FROM user_experience WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user['id']]);
            $user['experience'] = $stmt->fetchAll();

            // Получаем образование
            $stmt = $pdo->prepare("SELECT * FROM user_education WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user['id']]);
            $user['education'] = $stmt->fetchAll();

            // Получаем портфолио
            $stmt = $pdo->prepare("SELECT * FROM user_portfolio WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user['id']]);
            $user['portfolio'] = $stmt->fetchAll();

            // Получаем отклики
            $stmt = $pdo->prepare("
            SELECT ua.*, v.title as vacancy_title, v.company
            FROM user_applications ua
            JOIN vacancies v ON ua.vacancy_id = v.id
            WHERE ua.user_id = ?
            ORDER BY ua.applied_at DESC
        ");
            $stmt->execute([$user['id']]);
            $user['applications'] = $stmt->fetchAll();

            $stmt = $pdo->prepare("
            SELECT ua.*, v.title as vacancy_title, v.company
            FROM user_applications ua
            JOIN vacancies v ON ua.vacancy_id = v.id
            WHERE ua.user_id = ? AND ua.employment_status = 'successful'
            ORDER BY ua.employment_updated_at DESC, ua.applied_at DESC
            LIMIT 1
        ");
            $stmt->execute([$user['id']]);
            $user['current_job'] = $stmt->fetch();

            // Активность
            $stmt = $pdo->prepare("
            SELECT
                id,
                user_id,
                activity_type,
                CASE
                    WHEN activity_type = 'lesson' AND activity_text IN ('0', '1') THEN 'Завершил урок'
                    ELSE activity_text
                END AS activity_text,
                activity_time
            FROM user_activities
            WHERE user_id = ?
            ORDER BY activity_time DESC
            LIMIT 10
        ");
            $stmt->execute([$user['id']]);
            $activities = $stmt->fetchAll();

            try {
                $stmt = $pdo->prepare("
                SELECT message_text, created_at
                FROM vacancy_chats
                WHERE sender_id = ?
                ORDER BY created_at DESC
                LIMIT 5
            ");
                $stmt->execute([$user['id']]);
                foreach ($stmt->fetchAll() as $messageRow) {
                    $activities[] = [
                        'id' => null,
                        'user_id' => $user['id'],
                        'activity_type' => 'message',
                        'activity_text' => t('dashboard_internal_message_prefix', 'Внутреннее сообщение') . ': ' . mb_substr((string) ($messageRow['message_text'] ?? ''), 0, 180),
                        'activity_time' => (string) ($messageRow['created_at'] ?? ''),
                    ];
                }
            } catch (Throwable $e) {
            }

            try {
                $stmt = $pdo->prepare("
                SELECT message_text, sent_at
                FROM chat_messages
                WHERE user_id = ? AND sender = 'user'
                ORDER BY sent_at DESC
                LIMIT 5
            ");
                $stmt->execute([$user['id']]);
                foreach ($stmt->fetchAll() as $chatRow) {
                    $activities[] = [
                        'id' => null,
                        'user_id' => $user['id'],
                        'activity_type' => 'message',
                        'activity_text' => t('dashboard_ai_message_prefix', 'Сообщение AI-тьютору') . ': ' . mb_substr((string) ($chatRow['message_text'] ?? ''), 0, 180),
                        'activity_time' => (string) ($chatRow['sent_at'] ?? ''),
                    ];
                }
            } catch (Throwable $e) {
            }

            usort($activities, static function (array $a, array $b): int {
                $ta = strtotime((string) ($a['activity_time'] ?? '')) ?: 0;
                $tb = strtotime((string) ($b['activity_time'] ?? '')) ?: 0;
                return $tb <=> $ta;
            });
            $user['activities'] = array_slice($activities, 0, 10);

            foreach ($user['activities'] as &$activity) {
                $activity['activity_text'] = translateActivityMessage(
                    normalizeMojibakeText((string) ($activity['activity_text'] ?? ''))
                );
            }
            unset($activity);

            $stmt = $pdo->prepare("
            SELECT c.*, co.title as course_title
            FROM certificates c
            JOIN courses co ON c.course_id = co.id
            WHERE c.user_id = ?
            ORDER BY c.created_at DESC
        ");
            $stmt->execute([$user['id']]);
            $user['certificates'] = $stmt->fetchAll();

            $user['solved_tasks'] = 0;
            $user['passed_contests'] = 0;
            try {
                $stmt = $pdo->query("SHOW TABLES LIKE 'practice_submissions'");
                if ($stmt->rowCount() > 0) {
                    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT task_id) FROM practice_submissions WHERE user_id = ? AND passed = 1");
                    $stmt->execute([$user['id']]);
                    $user['solved_tasks'] += (int) $stmt->fetchColumn();
                }
                $stmt = $pdo->query("SHOW TABLES LIKE 'contest_submissions'");
                if ($stmt->rowCount() > 0) {
                    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT task_id) FROM contest_submissions WHERE user_id = ? AND status = 'accepted'");
                    $stmt->execute([$user['id']]);
                    $user['solved_tasks'] += (int) $stmt->fetchColumn();

                    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT contest_id) FROM contest_submissions WHERE user_id = ? AND status = 'accepted'");
                    $stmt->execute([$user['id']]);
                    $user['passed_contests'] = (int) $stmt->fetchColumn();
                }
            } catch (Throwable $e) {
            }

            foreach ($user['certificates'] as &$cert) {
                $cert['certificate_name'] = normalizeMojibakeText((string) ($cert['certificate_name'] ?? ''));
                $cert['issuer'] = normalizeMojibakeText((string) ($cert['issuer'] ?? ''));
                $cert['course_title'] = normalizeMojibakeText((string) ($cert['course_title'] ?? ''));
            }
            unset($cert);

            // Получаем непрочитанные уведомления
            tfPruneNotifications($pdo, (int) $user['id'], 15);
            $stmt = $pdo->prepare("
            SELECT * FROM notifications
            WHERE user_id = ? AND is_read = FALSE
            ORDER BY notification_time DESC
        ");
            $stmt->execute([$user['id']]);
            $user['unread_notifications'] = $stmt->rowCount();

            $stmt = $pdo->prepare("
            SELECT * FROM notifications
            WHERE user_id = ?
            ORDER BY notification_time DESC
            LIMIT 20
        ");
            $stmt->execute([$user['id']]);
            $user['notifications'] = $stmt->fetchAll();

            foreach ($user['notifications'] as &$notification) {
                $notification['message'] = normalizeMojibakeText((string) ($notification['message'] ?? ''));
            }
            unset($notification);

            $stmt = $pdo->prepare("SELECT rating, comment, created_at FROM platform_reviews WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $user['review'] = $stmt->fetch();

            return $user;
        }
        return null;
    }

    // Публичный профиль пользователя по ID (без сессионной привязки)
    function getUserProfileById($userId)
    {
        $pdo = getDBConnection();

        // Проверяем существование таблицы users
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() === 0) {
            return null; // Таблица ещё не создана
        }

        ensureUserProfileSchema($pdo);
        ensureUserSkillsSchema($pdo);
        ensureUserPortfolioSchema($pdo);
        ensureUserCvCustomizationSchema($pdo);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user) {
            return null;
        }
        $user = tfDecorateUserCountry($user);

        $stmt = $pdo->prepare("SELECT * FROM user_skills WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $user['skills'] = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM user_experience WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user['id']]);
        $user['experience'] = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM user_education WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user['id']]);
        $user['education'] = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM user_portfolio WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user['id']]);
        $user['portfolio'] = $stmt->fetchAll();

        $stmt = $pdo->prepare("
        SELECT c.*, co.title as course_title
        FROM certificates c
        JOIN courses co ON c.course_id = co.id
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC
    ");
        $stmt->execute([$user['id']]);
        $user['certificates'] = $stmt->fetchAll();

        $user['solved_tasks'] = 0;
        $user['passed_contests'] = 0;
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE 'practice_submissions'");
            if ($stmt->rowCount() > 0) {
                $stmt = $pdo->prepare("SELECT COUNT(DISTINCT task_id) FROM practice_submissions WHERE user_id = ? AND passed = 1");
                $stmt->execute([$user['id']]);
                $user['solved_tasks'] += (int) $stmt->fetchColumn();
            }
            $stmt = $pdo->query("SHOW TABLES LIKE 'contest_submissions'");
            if ($stmt->rowCount() > 0) {
                $stmt = $pdo->prepare("SELECT COUNT(DISTINCT task_id) FROM contest_submissions WHERE user_id = ? AND status = 'accepted'");
                $stmt->execute([$user['id']]);
                $user['solved_tasks'] += (int) $stmt->fetchColumn();

                $stmt = $pdo->prepare("SELECT COUNT(DISTINCT contest_id) FROM contest_submissions WHERE user_id = ? AND status = 'accepted'");
                $stmt->execute([$user['id']]);
                $user['passed_contests'] = (int) $stmt->fetchColumn();
            }
        } catch (Throwable $e) {
        }

        foreach ($user['certificates'] as &$cert) {
            $cert['certificate_name'] = normalizeMojibakeText((string) ($cert['certificate_name'] ?? ''));
            $cert['issuer'] = normalizeMojibakeText((string) ($cert['issuer'] ?? ''));
            $cert['course_title'] = normalizeMojibakeText((string) ($cert['course_title'] ?? ''));
        }
        unset($cert);

        $stmt = $pdo->prepare("
        SELECT ua.*, v.title as vacancy_title, v.company
        FROM user_applications ua
        JOIN vacancies v ON ua.vacancy_id = v.id
        WHERE ua.user_id = ?
        ORDER BY ua.applied_at DESC
    ");
        $stmt->execute([$user['id']]);
        $user['applications'] = $stmt->fetchAll();

        $stmt = $pdo->prepare("
        SELECT ua.*, v.title as vacancy_title, v.company
        FROM user_applications ua
        JOIN vacancies v ON ua.vacancy_id = v.id
        WHERE ua.user_id = ? AND ua.employment_status = 'successful'
        ORDER BY ua.employment_updated_at DESC, ua.applied_at DESC
        LIMIT 1
    ");
        $stmt->execute([$user['id']]);
        $user['current_job'] = $stmt->fetch();

        $stmt = $pdo->prepare("
        SELECT id, activity_type, activity_text, activity_time
        FROM user_activities
        WHERE user_id = ?
        ORDER BY activity_time DESC
        LIMIT 20
    ");
        $stmt->execute([$user['id']]);
        $user['activities'] = $stmt->fetchAll();

        foreach ($user['activities'] as &$activity) {
            $activity['activity_text'] = translateActivityMessage(
                normalizeMojibakeText((string) ($activity['activity_text'] ?? ''))
            );
        }
        unset($activity);

        $user['cv_customization'] = [];
        try {
            $stmt = $pdo->prepare("SELECT settings_json FROM user_cv_customizations WHERE user_id = ? LIMIT 1");
            $stmt->execute([(int) $user['id']]);
            $row = $stmt->fetch();
            if (is_array($row)) {
                $rawSettings = (string) ($row['settings_json'] ?? '');
                if ($rawSettings !== '') {
                    $decoded = json_decode($rawSettings, true);
                    if (is_array($decoded)) {
                        $user['cv_customization'] = $decoded;
                    }
                }
            }
        } catch (Throwable $e) {
        }

        return $user;
    }

    // Хеширование пароля
    function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    // Проверка пароля
    function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    // Валидация email
    function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Валидация пароля
    function isValidPassword($password)
    {
        // Минимальная длина 8 символов
        if (strlen($password) < 8)
            return false;
        // Должна быть хотя бы одна цифра
        if (!preg_match('/[0-9]/', $password))
            return false;
        // Должна быть хотя бы одна заглавная буква
        if (!preg_match('/[A-Z]/', $password))
            return false;
        return true;
    }

    // Генерация безопасного пароля
    function generateSecurePassword($length = 12)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
        $password = '';
        $password .= $chars[random_int(0, 25)];
        $password .= $chars[random_int(26, 51)];
        $password .= $chars[random_int(52, 61)];
        $password .= $chars[random_int(62, strlen($chars) - 1)];
        for ($i = 4; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return str_shuffle($password);
    }

    // Сила пароля
    function getPasswordStrength($password)
    {
        if (empty($password))
            return 0;
        $strength = 0;
        $length = strlen($password);
        if ($length >= 8)
            $strength += 25;
        if ($length >= 12)
            $strength += 25;
        if (preg_match('/[a-z]/', $password))
            $strength += 12.5;
        if (preg_match('/[A-Z]/', $password))
            $strength += 12.5;
        if (preg_match('/[0-9]/', $password))
            $strength += 12.5;
        if (preg_match('/[^a-zA-Z0-9]/', $password))
            $strength += 12.5;
        return $strength;
    }

    // Форматирование даты
    function formatDate($dateString)
    {
        $date = new DateTime($dateString);
        return $date->format('d F Y');
    }

    // Аватар по имени
    function getAvatarUrl($name)
    {
        $firstChar = mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8');
        return "https://placehold.co/150x150/4f46e5/ffffff?text=$firstChar";
    }

    // Уровень навыка текстом
    function getLevelText($level)
    {
        if ($level < 40)
            return "Начальный";
        if ($level < 60)
            return "Средний";
        if ($level < 80)
            return "Продвинутый";
        return "Эксперт";
    }

    // Тип вакансии текстом
    function getVacancyTypeText($type)
    {
        $types = ['remote' => "Удаленно", 'office' => "Р’ офисе", 'hybrid' => "Гибрид"];
        return $types[$type] ?? $type;
    }

    // Статус заявки текстом
    function getApplicationStatusText($status)
    {
        $statuses = ['applied' => "Откликнулся", 'interview' => "Собеседование", 'offer' => "Оффер", 'rejected' => "Отказ"];
        return $statuses[$status] ?? $status;
    }

    // Класс статуса заявки
    function getApplicationStatusClass($status)
    {
        $classes = ['applied' => 'status-applied', 'interview' => 'status-interview', 'offer' => 'status-offer', 'rejected' => 'status-rejected'];
        return $classes[$status] ?? '';
    }

    // Расчет баллов пользователя
    function calculateUserPoints($user)
    {
        $pdo = getDBConnection();
        $points = 0;

        // Завершенные курсы
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM user_course_progress WHERE user_id = ? AND completed = TRUE");
        $stmt->execute([$user['id']]);
        $completedCourses = $stmt->fetch()['count'];
        $points += $completedCourses * 50;

        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM certificates WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $certificates = $stmt->fetch()['count'];
        $points += $certificates * 100;

        // Навыки
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM user_skills WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $skillsCount = $stmt->fetch()['count'];
        $points += $skillsCount * 20;

        $stmt = $pdo->prepare("SELECT start_date, end_date FROM user_experience WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $experiences = $stmt->fetchAll();
        $totalMonths = 0;
        foreach ($experiences as $exp) {
            $startDate = new DateTime($exp['start_date']);
            $endDate = $exp['end_date'] ? new DateTime($exp['end_date']) : new DateTime();
            $diff = $startDate->diff($endDate);
            $totalMonths += $diff->y * 12 + $diff->m;
        }
        $points += min($totalMonths, 60) * 5;

        $stmt = $pdo->prepare("SELECT degree FROM user_education WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $educations = $stmt->fetchAll();
        foreach ($educations as $edu) {
            if (strpos($edu['degree'], "бакалавр") !== false)
                $points += 50;
            if (strpos($edu['degree'], "магистр") !== false)
                $points += 75;
            if (strpos($edu['degree'], "кандидат") !== false)
                $points += 100;
        }

        return [
            'points' => $points,
            'completed_courses' => $completedCourses,
            'certificates' => $certificates,
            'skills_count' => $skillsCount,
            'total_experience' => formatExperienceMonths($totalMonths)
        ];
    }

    function formatExperienceMonths($months)
    {
        if (function_exists('t')) {
            if ($months < 12)
                return t('exp_less_1_year');
            if ($months < 36)
                return t('exp_1_3_years');
            return t('exp_3_plus_years');
        }
        if ($months < 12)
            return "Менее 1 года";
        if ($months < 36)
            return "1-3 года";
        return "3+ года";
    }

    function i18nFormat($template, array $vars = [])
    {
        $result = $template ?? '';
        foreach ($vars as $key => $value) {
            $result = str_replace('{' . $key . '}', (string) $value, $result);
        }
        return $result;
    }

    // ... остальные функции (tfMojibakeScore, tfTryDecodeCp1251Mojibake, tfDecodeCp1251MojibakeChunk, normalizeMojibakeText,
// tfBuildNotificationPatternFromTemplate, translateNotificationMessage, translateActivityMessage, tfAddNotification,
// tfPruneNotifications, getTopUsers, uiValue, formatYearsLabel, ensureUserSkillsSchema, ensureQuizSchema,
// ensureSkillAssessmentSchema, ensureUserProfileSchema, ensureUserPortfolioSchema, getActivityHeatmap,
// ensureVacancyCurrencySchema, tfIsSafeMethod, tfExtractCsrfToken, tfValidateCsrfToken, tfEnforceCsrf,
// tfValidateRequiredFields) остаются без изменений ...

    // Проверка авторизации
    function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
    }

    // Остальные вспомогательные функции (без изменений)
    function tfMojibakeScore($text)
    {
        if (!is_string($text) || $text === '') {
            return PHP_INT_MIN;
        }
        if (!preg_match('//u', $text)) {
            return PHP_INT_MIN;
        }
        $cyr = preg_match_all('/[\p{Cyrillic}]/u', $text, $m1);
        $latin = preg_match_all('/[A-Za-z]/', $text, $m2);
        $digits = preg_match_all('/[0-9]/', $text, $m3);
        $spaces = preg_match_all('/[ \t\r\n]/', $text, $m4);
        $nbsp = substr_count($text, "\u{00A0}");
        $qmarks = substr_count($text, '?');
        $bad = preg_match_all('/[\x{00C3}\x{00D0}\x{2018}\x{201A}\x{00A2}]/u', $text, $m5);
        $rep = preg_match_all('/\x{FFFD}|\x{043F}\x{0457}\x{0403}/u', $text, $m7);
        $weirdCyr = preg_match_all('/[\x{201A}\x{0401}\x{00B0}\x{040A}\x{040C}\x{040F}]/u', $text, $m6);
        $mojiSeq = substr_count($text, "\u{0432}\u{0420}\u{201A}") + substr_count($text, "\u{0420}\x{040E}\u{00A0}") + substr_count($text, "\u{0413}\u{201A}\u{00A0}");
        return ($cyr * 3 + $latin + $digits + (int) ($spaces / 4)) - ($bad * 8 + $rep * 20 + $weirdCyr * 12 + $nbsp * 2 + $qmarks * 3 + $mojiSeq * 15);
    }

    function tfTryDecodeCp1251Mojibake($text)
    {
        if (!is_string($text) || $text === '' || !preg_match('//u', $text) || !function_exists('mb_ord')) {
            return null;
        }
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($chars) || empty($chars)) {
            return null;
        }
        $bytes = '';
        foreach ($chars as $ch) {
            $code = mb_ord($ch, 'UTF-8');
            if (!is_int($code)) {
                return null;
            }
            if ($code <= 0x7F) {
                $bytes .= chr($code);
                continue;
            }
            if ($code >= 0x0410 && $code <= 0x044F) {
                $bytes .= chr($code - 0x350);
                continue;
            }
            if ($code === 0x0401) {
                $bytes .= chr(0xA8);
                continue;
            }
            if ($code === 0x0451) {
                $bytes .= chr(0xB8);
                continue;
            }
            if ($code === 0x2019) {
                $bytes .= chr(0x92);
                continue;
            }
            if ($code === 0x2116) {
                $bytes .= chr(0xB9);
                continue;
            }
            return null;
        }
        if ($bytes === '' || !preg_match('//u', $bytes)) {
            return null;
        }
        return $bytes;
    }

    function tfDecodeCp1251MojibakeChunk($chunk)
    {
        if (!is_string($chunk) || $chunk === '' || !preg_match('//u', $chunk) || !function_exists('iconv')) {
            return $chunk;
        }
        $chars = preg_split('//u', $chunk, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($chars) || empty($chars)) {
            return $chunk;
        }
        $bytes = '';
        foreach ($chars as $ch) {
            $b = @iconv('UTF-8', 'Windows-1251//IGNORE', $ch);
            if (!is_string($b) || $b === '' || strlen($b) !== 1) {
                return $chunk;
            }
            $bytes .= $b;
        }
        if ($bytes === '' || !preg_match('//u', $bytes)) {
            return $chunk;
        }
        return $bytes;
    }

    function normalizeMojibakeText($text)
    {
        if (!is_string($text) || $text === '') {
            return $text;
        }
        if (!preg_match('//u', $text) || !function_exists('iconv')) {
            return $text;
        }
        $best = $text;
        $bestScore = tfMojibakeScore($text);
        $encodings = ['Windows-1251', 'Windows-1252', 'ISO-8859-1', 'KOI8-R'];
        $current = $text;
        $segmented = preg_replace_callback(
            '/(?:[\x{0420}\x{040E}\x{00A0}\x{0421}][^\s]){2,}/u',
            static function ($m) {
                return tfDecodeCp1251MojibakeChunk((string) ($m[0] ?? ''));
            },
            $text
        );
        if ($segmented === null) {
            $segmented = $text;
        }
        $microPattern = '/(?:\x{0420}\x{040E}\x{00A0}|\x{0421})[\x{00A0}\x{201A}\x{201E}\x{201C}\x{201D}\x{2018}\x{2019}\x{2013}\x{2014}\x{00A8}\x{0401}\x{0451}\x{0402}\x{0403}\x{040A}\x{040C}\x{040F}]/u';
        $microFixed = preg_replace_callback(
            $microPattern,
            static function ($m) {
                return tfDecodeCp1251MojibakeChunk((string) ($m[0] ?? ''));
            },
            $segmented
        );
        if ($microFixed === null) {
            $microFixed = $segmented;
        }
        if (is_string($microFixed) && $microFixed !== '' && $microFixed !== $segmented) {
            $best = $microFixed;
            $bestScore = tfMojibakeScore($microFixed);
            $current = $microFixed;
        }
        if (is_string($segmented) && $segmented !== '' && $segmented !== $text) {
            $segmentedScore = tfMojibakeScore($segmented);
            $rsCount = preg_match_all('/[\x{0420}\x{040E}\x{00A0}\x{0421}]/u', $text, $m1);
            $rsDecoded = preg_match_all('/[\x{0420}\x{040E}\x{00A0}\x{0421}]/u', $segmented, $m2);
            $lowerCyr = preg_match_all('/[\x{0420}\x{040E}\x{00A0}\x{0421}\x{0430}-\x{044F}\x{2018}]/u', $text, $m3);
            $lowerCyrDecoded = preg_match_all('/[\x{0420}\x{040E}\x{00A0}\x{0421}\x{0430}-\x{044F}\x{2018}]/u', $segmented, $m4);
            if (
                $segmentedScore > $bestScore + 1 ||
                ($rsCount > 6 && $rsDecoded < $rsCount && $lowerCyrDecoded >= $lowerCyr)
            ) {
                $best = $segmented;
                $bestScore = $segmentedScore;
                $current = $segmented;
            }
        }
        $strictCandidate = tfTryDecodeCp1251Mojibake($text);
        if (is_string($strictCandidate) && $strictCandidate !== '' && $strictCandidate !== $text) {
            $strictScore = tfMojibakeScore($strictCandidate);
            $rsCount = preg_match_all('/[\x{0420}\x{040E}\x{00A0}\x{0421}]/u', $text, $m1);
            $rsDecoded = preg_match_all('/[\x{0420}\x{040E}\x{00A0}\x{0421}]/u', $strictCandidate, $m2);
            $lowerCyr = preg_match_all('/[\x{0420}\x{040E}\x{00A0}\x{0421}\x{0430}-\x{044F}\x{2018}]/u', $text, $m3);
            $lowerCyrDecoded = preg_match_all('/[\x{0420}\x{040E}\x{00A0}\x{0421}\x{0430}-\x{044F}\x{2018}]/u', $strictCandidate, $m4);
            if (
                $strictScore > $bestScore + 1 ||
                ($rsCount > 6 && $rsDecoded < $rsCount && $lowerCyrDecoded >= $lowerCyr)
            ) {
                $best = $strictCandidate;
                $bestScore = $strictScore;
                $current = $strictCandidate;
            }
        }
        for ($pass = 0; $pass < 2; $pass++) {
            $passBest = $current;
            $passBestScore = tfMojibakeScore($current);
            foreach ($encodings as $enc) {
                $chars = preg_split('//u', $current, -1, PREG_SPLIT_NO_EMPTY);
                if (!is_array($chars) || empty($chars)) {
                    continue;
                }
                $bytes = '';
                $ok = true;
                foreach ($chars as $ch) {
                    if (strlen($ch) === 1 && ord($ch) <= 0x7F) {
                        $bytes .= $ch;
                        continue;
                    }
                    $chunk = @iconv('UTF-8', $enc . '//IGNORE', $ch);
                    if (!is_string($chunk) || $chunk === '' || strlen($chunk) !== 1) {
                        $ok = false;
                        break;
                    }
                    $bytes .= $chunk;
                }
                if (!$ok || $bytes === '' || !preg_match('//u', $bytes)) {
                    continue;
                }
                $candidate = $bytes;
                $score = tfMojibakeScore($candidate);
                if ($score > $passBestScore + 4) {
                    $passBest = $candidate;
                    $passBestScore = $score;
                }
            }
            if ($passBestScore <= tfMojibakeScore($current) + 2) {
                break;
            }
            $current = $passBest;
            if ($passBestScore > $bestScore) {
                $best = $passBest;
                $bestScore = $passBestScore;
            }
        }
        return $best;
    }

    function tfBuildNotificationPatternFromTemplate($template, array $vars)
    {
        if (!is_string($template) || $template === '') {
            return null;
        }
        $quoted = preg_quote($template, '/');
        foreach ($vars as $var) {
            $placeholder = preg_quote('{' . $var . '}', '/');
            $quoted = str_replace($placeholder, '(.+?)', $quoted);
        }
        return '/^' . $quoted . '$/u';
    }

    function tLang($targetLang, $key, $default = null)
    {
        if (!isset($GLOBALS['I18N']) || !is_array($GLOBALS['I18N'])) {
            return $default ?? $key;
        }
        if (isset($GLOBALS['I18N'][$targetLang][$key])) {
            return $GLOBALS['I18N'][$targetLang][$key];
        }
        return $default ?? $key;
    }

    function translateNotificationMessageForLang($message, $targetLang)
    {
        if (!is_string($message) || $message === '') {
            return $message;
        }
        $targetLang = in_array($targetLang, ['ru', 'en', 'tg'], true)
            ? $targetLang
            : (function_exists('currentLang') ? currentLang() : 'ru');
        $message = normalizeMojibakeText($message);
        $candidateMessages = [$message];
        $fixedConnector = preg_replace('/\bРР†\b/u', 'РР†', $message);
        if (is_string($fixedConnector) && $fixedConnector !== '' && $fixedConnector !== $message) {
            $candidateMessages[] = $fixedConnector;
        }
        $rules = [
            [
                'key' => 'notif_welcome_back',
                'vars' => ['name'],
            ],
            [
                'key' => 'notif_lesson_completed',
                'vars' => ['lesson'],
            ],
            [
                'key' => 'notif_application_sent',
                'vars' => ['title', 'company'],
            ],
            [
                'key' => 'notif_hired',
                'vars' => ['title', 'company'],
            ],
            [
                'key' => 'notif_rejected',
                'vars' => ['title', 'company'],
            ],
            [
                'key' => 'notif_course_created',
                'vars' => ['course'],
            ]
        ];
        foreach ($candidateMessages as $candidateMessage) {
            foreach ($rules as $rule) {
                $templates = [];
                if (function_exists('t')) {
                    $templates[] = t($rule['key']);
                }
                if (isset($GLOBALS['I18N']) && is_array($GLOBALS['I18N'])) {
                    foreach (['ru', 'en', 'tg'] as $lang) {
                        if (isset($GLOBALS['I18N'][$lang][$rule['key']]) && is_string($GLOBALS['I18N'][$lang][$rule['key']])) {
                            $templates[] = $GLOBALS['I18N'][$lang][$rule['key']];
                        }
                    }
                }
                $templates = array_values(array_unique(array_filter($templates, 'is_string')));
                foreach ($templates as $template) {
                    $pattern = tfBuildNotificationPatternFromTemplate($template, $rule['vars']);
                    if (!$pattern) {
                        continue;
                    }
                    if (!preg_match($pattern, $candidateMessage, $matches)) {
                        continue;
                    }
                    $vars = [];
                    foreach ($rule['vars'] as $index => $name) {
                        $vars[$name] = $matches[$index + 1] ?? '';
                    }
                    $defaultTemplate = function_exists('t') ? t($rule['key']) : $rule['key'];
                    return i18nFormat(tLang($targetLang, $rule['key'], $defaultTemplate), $vars);
                }
            }
        }
        $fallbackPatterns = [
            'notif_hired' => '/^Вас приняли на вакансию\s+"(.+?)"\s+\S+\s+(.+)$/u',
            'notif_application_sent' => '/^Ваша заявка на вакансию\s+"(.+?)"\s+\S+\s+(.+?)\s+отправлена$/u',
            'notif_rejected' => '/^По вакансии\s+"(.+?)"\s+\S+\s+(.+?)\s+принято решение:\s*отказ$/u',
        ];
        foreach ($candidateMessages as $candidateMessage) {
            foreach ($fallbackPatterns as $key => $pattern) {
                if (!preg_match($pattern, $candidateMessage, $matches)) {
                    continue;
                }
                $title = trim((string) ($matches[1] ?? ''));
                $company = trim((string) ($matches[2] ?? ''));
                if ($title === '' || $company === '') {
                    continue;
                }
                $defaultTemplate = function_exists('t') ? t($key) : $key;
                return i18nFormat(tLang($targetLang, $key, $defaultTemplate), [
                    'title' => $title,
                    'company' => $company,
                ]);
            }
        }
        foreach ($candidateMessages as $candidateMessage) {
            if (substr_count($candidateMessage, '?') < 3) {
                continue;
            }
            if (!preg_match('/["«](.+?)["»]\s+в\s+(.+)$/u', $candidateMessage, $matches)) {
                continue;
            }
            $title = trim((string) ($matches[1] ?? ''));
            $company = trim((string) ($matches[2] ?? ''));
            if ($title === '' || $company === '') {
                continue;
            }
            $lower = mb_strtolower($candidateMessage);
            $key = (mb_strpos($lower, 'отказ') !== false) ? 'notif_rejected' : 'notif_hired';
            $defaultTemplate = function_exists('t') ? t($key) : $key;
            return i18nFormat(tLang($targetLang, $key, $defaultTemplate), [
                'title' => $title,
                'company' => $company,
            ]);
        }
        return $message;
    }

    function translateNotificationMessage($message)
    {
        if (!function_exists('t') || !is_string($message) || $message === '') {
            return $message;
        }
        $message = normalizeMojibakeText($message);
        $candidateMessages = [$message];
        $fixedConnector = preg_replace('/\bв\b/u', 'в', $message);
        if (is_string($fixedConnector) && $fixedConnector !== '' && $fixedConnector !== $message) {
            $candidateMessages[] = $fixedConnector;
        }
        $rules = [
            [
                'key' => 'notif_welcome_back',
                'vars' => ['name'],
            ],
            [
                'key' => 'notif_lesson_completed',
                'vars' => ['lesson'],
            ],
            [
                'key' => 'notif_application_sent',
                'vars' => ['title', 'company'],
            ],
            [
                'key' => 'notif_hired',
                'vars' => ['title', 'company'],
            ],
            [
                'key' => 'notif_rejected',
                'vars' => ['title', 'company'],
            ],
            [
                'key' => 'notif_course_created',
                'vars' => ['course'],
            ]
        ];
        foreach ($candidateMessages as $candidateMessage) {
            foreach ($rules as $rule) {
                $templates = [];
                if (function_exists('currentLang')) {
                    $templates[] = t($rule['key']);
                }
                if (isset($GLOBALS['I18N']) && is_array($GLOBALS['I18N'])) {
                    foreach (['ru', 'en', 'tg'] as $lang) {
                        if (isset($GLOBALS['I18N'][$lang][$rule['key']]) && is_string($GLOBALS['I18N'][$lang][$rule['key']])) {
                            $templates[] = $GLOBALS['I18N'][$lang][$rule['key']];
                        }
                    }
                }
                $templates = array_values(array_unique(array_filter($templates, 'is_string')));
                foreach ($templates as $template) {
                    $pattern = tfBuildNotificationPatternFromTemplate($template, $rule['vars']);
                    if (!$pattern) {
                        continue;
                    }
                    if (!preg_match($pattern, $candidateMessage, $matches)) {
                        continue;
                    }
                    $vars = [];
                    foreach ($rule['vars'] as $index => $name) {
                        $vars[$name] = $matches[$index + 1] ?? '';
                    }
                    return i18nFormat(t($rule['key']), $vars);
                }
            }
        }
        $fallbackPatterns = [
            'notif_hired' => '/^Вас приняли на вакансию\s+"(.+?)"\s+\S+\s+(.+)$/u',
            'notif_application_sent' => '/^Ваша заявка на вакансию\s+"(.+?)"\s+\S+\s+(.+?)\s+отправлена$/u',
            'notif_rejected' => '/^По вакансии\s+"(.+?)"\s+\S+\s+(.+?)\s+принято решение:\s*отказ$/u',
        ];
        foreach ($candidateMessages as $candidateMessage) {
            foreach ($fallbackPatterns as $key => $pattern) {
                if (!preg_match($pattern, $candidateMessage, $matches)) {
                    continue;
                }
                $title = trim((string) ($matches[1] ?? ''));
                $company = trim((string) ($matches[2] ?? ''));
                if ($title === '' || $company === '') {
                    continue;
                }
                return i18nFormat(t($key), [
                    'title' => $title,
                    'company' => $company,
                ]);
            }
        }
        foreach ($candidateMessages as $candidateMessage) {
            if (substr_count($candidateMessage, '?') < 3) {
                continue;
            }
            if (!preg_match('/["«](.+?)["»]\s+в\s+(.+)$/u', $candidateMessage, $matches)) {
                continue;
            }
            $title = trim((string) ($matches[1] ?? ''));
            $company = trim((string) ($matches[2] ?? ''));
            if ($title === '' || $company === '') {
                continue;
            }
            $lower = mb_strtolower($candidateMessage);
            $key = (mb_strpos($lower, 'отказ') !== false) ? 'notif_rejected' : 'notif_hired';
            return i18nFormat(t($key), [
                'title' => $title,
                'company' => $company,
            ]);
        }
        return $message;
    }

    function translateActivityMessageForLang($message, $targetLang)
    {
        if (!is_string($message) || $message === '') {
            return $message;
        }
        $targetLang = in_array($targetLang, ['ru', 'en', 'tg'], true)
            ? $targetLang
            : (function_exists('currentLang') ? currentLang() : 'ru');
        $message = normalizeMojibakeText($message);
        $candidateMessages = [$message];
        $normalizedSpace = preg_replace('/\s+/u', ' ', trim($message));
        if (is_string($normalizedSpace) && $normalizedSpace !== '' && $normalizedSpace !== $message) {
            $candidateMessages[] = $normalizedSpace;
        }
        $translatedNotification = translateNotificationMessageForLang($message, $targetLang);
        if (is_string($translatedNotification) && $translatedNotification !== $message) {
            return $translatedNotification;
        }
        $rules = [
            [
                'key' => 'activity_started_learning',
                'vars' => [],
            ],
            [
                'key' => 'activity_lesson_completed',
                'vars' => ['lesson'],
            ],
            [
                'key' => 'activity_applied_vacancy',
                'vars' => ['title', 'company'],
            ],
        ];
        foreach ($candidateMessages as $candidateMessage) {
            foreach ($rules as $rule) {
                $templates = [];
                if (function_exists('t')) {
                    $templates[] = t($rule['key']);
                }
                if (isset($GLOBALS['I18N']) && is_array($GLOBALS['I18N'])) {
                    foreach (['ru', 'en', 'tg'] as $langCode) {
                        if (isset($GLOBALS['I18N'][$langCode][$rule['key']])) {
                            $templates[] = (string) $GLOBALS['I18N'][$langCode][$rule['key']];
                        }
                    }
                }
                $templates = array_values(array_unique(array_filter($templates, 'is_string')));
                foreach ($templates as $template) {
                    $pattern = tfBuildNotificationPatternFromTemplate($template, $rule['vars']);
                    if (!$pattern) {
                        continue;
                    }
                    if (!preg_match($pattern, $candidateMessage, $matches)) {
                        continue;
                    }
                    $vars = [];
                    foreach ($rule['vars'] as $index => $name) {
                        $vars[$name] = trim((string) ($matches[$index + 1] ?? ''));
                    }
                    $defaultTemplate = function_exists('t') ? t($rule['key']) : $rule['key'];
                    return i18nFormat(tLang($targetLang, $rule['key'], $defaultTemplate), $vars);
                }
            }
        }
        foreach ($candidateMessages as $candidateMessage) {
            if ($candidateMessage === '0' || $candidateMessage === '1') {
                $defaultTemplate = function_exists('t')
                    ? t('activity_lesson_completed_short', 'Completed lesson')
                    : 'Completed lesson';
                return tLang($targetLang, 'activity_lesson_completed_short', $defaultTemplate);
            }
            if (preg_match('/^Завершил урок\s*:?[\s]*(.+)?$/u', $candidateMessage, $m)) {
                $lesson = trim((string) ($m[1] ?? ''));
                if ($lesson === '') {
                    $defaultTemplate = function_exists('t')
                        ? t('activity_lesson_completed_short', 'Completed lesson')
                        : 'Completed lesson';
                    return tLang($targetLang, 'activity_lesson_completed_short', $defaultTemplate);
                }
                $defaultTemplate = function_exists('t')
                    ? t('activity_lesson_completed', 'Completed lesson: {lesson}')
                    : 'Completed lesson: {lesson}';
                return i18nFormat(tLang($targetLang, 'activity_lesson_completed', $defaultTemplate), ['lesson' => $lesson]);
            }
        }
        return $message;
    }

    function translateActivityMessage($message)
    {
        if (!function_exists('t') || !is_string($message) || $message === '') {
            return $message;
        }
        $message = normalizeMojibakeText($message);
        $candidateMessages = [$message];
        $normalizedSpace = preg_replace('/\s+/u', ' ', trim($message));
        if (is_string($normalizedSpace) && $normalizedSpace !== '' && $normalizedSpace !== $message) {
            $candidateMessages[] = $normalizedSpace;
        }
        $translatedNotification = translateNotificationMessage($message);
        if (is_string($translatedNotification) && $translatedNotification !== $message) {
            return $translatedNotification;
        }
        $rules = [
            [
                'key' => 'activity_started_learning',
                'vars' => [],
            ],
            [
                'key' => 'activity_lesson_completed',
                'vars' => ['lesson'],
            ],
            [
                'key' => 'activity_applied_vacancy',
                'vars' => ['title', 'company'],
            ],
        ];
        foreach ($candidateMessages as $candidateMessage) {
            foreach ($rules as $rule) {
                $templates = [t($rule['key'])];
                if (isset($GLOBALS['I18N']) && is_array($GLOBALS['I18N'])) {
                    foreach (['ru', 'en', 'tg'] as $langCode) {
                        if (isset($GLOBALS['I18N'][$langCode][$rule['key']])) {
                            $templates[] = (string) $GLOBALS['I18N'][$langCode][$rule['key']];
                        }
                    }
                }
                $templates = array_values(array_unique(array_filter($templates, 'is_string')));
                foreach ($templates as $template) {
                    $pattern = tfBuildNotificationPatternFromTemplate($template, $rule['vars']);
                    if (!$pattern) {
                        continue;
                    }
                    if (!preg_match($pattern, $candidateMessage, $matches)) {
                        continue;
                    }
                    $vars = [];
                    foreach ($rule['vars'] as $index => $name) {
                        $vars[$name] = trim((string) ($matches[$index + 1] ?? ''));
                    }
                    return i18nFormat(t($rule['key']), $vars);
                }
            }
        }
        foreach ($candidateMessages as $candidateMessage) {
            if ($candidateMessage === '0' || $candidateMessage === '1') {
                return t('activity_lesson_completed_short', 'Completed lesson');
            }
            if (preg_match('/^Завершил урок\s*:?[\s]*(.+)?$/u', $candidateMessage, $m)) {
                $lesson = trim((string) ($m[1] ?? ''));
                if ($lesson === '') {
                    return t('activity_lesson_completed_short', 'Completed lesson');
                }
                return i18nFormat(t('activity_lesson_completed', 'Completed lesson: {lesson}'), ['lesson' => $lesson]);
            }
        }
        return $message;
    }

    function tfAddNotification(PDO $pdo, $userId, $message, $keep = 15)
    {
        $userId = (int) $userId;
        $message = normalizeMojibakeText((string) $message);
        $keep = (int) $keep;
        if ($keep < 1) {
            $keep = 1;
        }
        if ($keep > 100) {
            $keep = 100;
        }
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, notification_time) VALUES (?, ?, NOW())");
        $stmt->execute([$userId, $message]);
        tfPruneNotifications($pdo, $userId, $keep);
    }

    function tfHasRecentNotification(PDO $pdo, $userId, $message, $withinSeconds = 86400)
    {
        $userId = (int) $userId;
        $message = normalizeMojibakeText((string) $message);
        $withinSeconds = (int) $withinSeconds;
        if ($withinSeconds < 1) {
            return false;
        }
        $since = date('Y-m-d H:i:s', time() - $withinSeconds);
        $stmt = $pdo->prepare("
            SELECT 1
            FROM notifications
            WHERE user_id = ? AND message = ? AND notification_time >= ?
            LIMIT 1
        ");
        $stmt->execute([$userId, $message, $since]);
        return (bool) $stmt->fetchColumn();
    }

    function tfDedupNotificationMessage(PDO $pdo, $userId, $message)
    {
        $userId = (int) $userId;
        $message = normalizeMojibakeText((string) $message);
        $stmt = $pdo->prepare("
            DELETE FROM notifications
            WHERE user_id = ?
              AND message = ?
              AND id NOT IN (
                  SELECT id FROM (
                      SELECT id
                      FROM notifications
                      WHERE user_id = ? AND message = ?
                      ORDER BY notification_time DESC, id DESC
                      LIMIT 1
                  ) t
              )
        ");
        $stmt->execute([$userId, $message, $userId, $message]);
    }

    function tfPruneNotifications(PDO $pdo, $userId, $keep = 15)
    {
        $userId = (int) $userId;
        $keep = (int) $keep;
        if ($keep < 1) {
            $keep = 1;
        }
        if ($keep > 100) {
            $keep = 100;
        }
        $sql = "
    DELETE FROM notifications
    WHERE user_id = :uid
    AND id NOT IN (
        SELECT id FROM (
            SELECT id
            FROM notifications
            WHERE user_id = :uid2
            ORDER BY notification_time DESC, id DESC
            LIMIT {$keep}
        ) t
    )
    ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':uid' => $userId, ':uid2' => $userId]);
    }

    function getTopUsers($limit = 10)
    {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM users WHERE is_blocked = FALSE AND role IN ('seeker','recruiter') ORDER BY created_at DESC");
        $users = $stmt->fetchAll();
        $usersWithPoints = [];
        foreach ($users as $user) {
            $pointsData = calculateUserPoints($user);
            $usersWithPoints[] = array_merge($user, $pointsData);
        }
        usort($usersWithPoints, function ($a, $b) {
            return $b['points'] - $a['points'];
        });
        $topUsers = array_slice($usersWithPoints, 0, $limit);
        foreach ($topUsers as $index => &$user) {
            $user['position'] = $index + 1;
            $user['avatar'] = $user['avatar'] ?? getAvatarUrl($user['name']);
            $user = tfDecorateUserCountry($user);
            $stmt = $pdo->prepare("SELECT * FROM user_skills WHERE user_id = ? LIMIT 3");
            $stmt->execute([$user['id']]);
            $user['skills'] = $stmt->fetchAll();
        }
        unset($user);
        return $topUsers;
    }

    function uiValue($value, $noneText = null)
    {
        if ($value === 0 || $value === '0' || $value === 0.0) {
            if ($noneText === null && function_exists('t')) {
                $noneText = t('common_none');
            }
            if ($noneText === null) {
                $noneText = "нет";
            }
            return $noneText;
        }
        return $value;
    }

    function formatYearsLabel($count, $noneText = null)
    {
        if ($count === 0 || $count === '0') {
            if ($noneText === null && function_exists('t')) {
                $noneText = t('common_none');
            }
            if ($noneText === null) {
                $noneText = "нет";
            }
            return $noneText;
        }
        $yearsLabel = function_exists('t') ? t('common_years') : "лет";
        return $count . " " . $yearsLabel;
    }

    function ensureUserSkillsSchema($pdo)
    {
        try {
            $stmt = $pdo->prepare("
            SELECT COUNT(*) as cnt
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'user_skills'
            AND COLUMN_NAME = 'is_verified'
        ");
            $stmt->execute();
            $exists = (int) ($stmt->fetch()['cnt'] ?? 0);
            if ($exists === 0) {
                $pdo->exec("ALTER TABLE user_skills ADD COLUMN is_verified TINYINT(1) DEFAULT 0");
            }
            $pdo->exec("ALTER TABLE user_skills MODIFY skill_level INT DEFAULT 0");
            $pdo->exec("UPDATE user_skills SET skill_level = 0 WHERE COALESCE(is_verified, 0) = 0");
        } catch (Exception $e) {
        }
    }

    function ensureQuizSchema(PDO $pdo)
    {
        $columns = [
            'correct_options' => "correct_options TEXT NULL",
        ];
        $stmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'quiz_questions'
    ");
        $stmt->execute([DB_NAME]);
        $existing = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
        foreach ($columns as $name => $definition) {
            if (!isset($existing[$name])) {
                $pdo->exec("ALTER TABLE quiz_questions ADD COLUMN {$definition}");
            }
        }
    }

    function ensureSkillAssessmentSchema(PDO $pdo)
    {
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS skill_assessment_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        skill_id INT NOT NULL,
        max_round TINYINT UNSIGNED DEFAULT 0,
        max_percent INT DEFAULT 0,
        status ENUM('not_started', 'in_progress', 'surrendered', 'completed') DEFAULT 'not_started',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_skill_assessment_progress_user_skill (user_id, skill_id),
        INDEX idx_skill_assessment_progress_user (user_id),
        INDEX idx_skill_assessment_progress_skill (skill_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS skill_assessment_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        skill_id INT NOT NULL,
        round_no TINYINT UNSIGNED NOT NULL,
        difficulty ENUM('easy', 'medium', 'hard') NOT NULL,
        score INT DEFAULT 0,
        total_questions INT DEFAULT 0,
        percent INT DEFAULT 0,
        passed TINYINT(1) DEFAULT 0,
        surrendered TINYINT(1) DEFAULT 0,
        attempt_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_skill_assessment_attempts_user (user_id),
        INDEX idx_skill_assessment_attempts_skill (skill_id),
        INDEX idx_skill_assessment_attempts_date (attempt_date),
        UNIQUE KEY uniq_skill_assessment_attempt_round (user_id, skill_id, round_no)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
        try {
            $stmt = $pdo->prepare("
            SELECT INDEX_NAME, COUNT(*) AS cnt
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'skill_assessment_attempts'
            AND INDEX_NAME IN ('uniq_skill_assessment_attempt_day', 'uniq_skill_assessment_attempt_round')
            GROUP BY INDEX_NAME
        ");
            $stmt->execute();
            $rows = $stmt->fetchAll();
            $legacyIdx = 0;
            $roundIdx = 0;
            foreach ((array) $rows as $row) {
                $indexName = (string) ($row['INDEX_NAME'] ?? '');
                $cnt = (int) ($row['cnt'] ?? 0);
                if ($indexName === 'uniq_skill_assessment_attempt_day') {
                    $legacyIdx = $cnt;
                } elseif ($indexName === 'uniq_skill_assessment_attempt_round') {
                    $roundIdx = $cnt;
                }
            }
            if ($legacyIdx > 0 || $roundIdx === 0) {
                // Keep only one attempt per user+skill+round before creating unique key.
                $pdo->exec("
            DELETE a1
            FROM skill_assessment_attempts a1
            JOIN skill_assessment_attempts a2
            ON a1.user_id = a2.user_id
            AND a1.skill_id = a2.skill_id
            AND a1.round_no = a2.round_no
            AND (
                a1.passed < a2.passed
                OR (a1.passed = a2.passed AND a1.percent < a2.percent)
                OR (a1.passed = a2.passed AND a1.percent = a2.percent AND a1.id > a2.id)
            )
            ");
                if ($legacyIdx > 0) {
                    $pdo->exec("ALTER TABLE skill_assessment_attempts DROP INDEX uniq_skill_assessment_attempt_day");
                }
                if ($roundIdx === 0) {
                    $pdo->exec("ALTER TABLE skill_assessment_attempts ADD UNIQUE KEY uniq_skill_assessment_attempt_round (user_id, skill_id, round_no)");
                }
            }
        } catch (Throwable $e) {
        }
    }

    function ensureUserProfileSchema($pdo)
    {
        $required = [
            'social_linkedin' => "VARCHAR(500) NULL",
            'social_github' => "VARCHAR(500) NULL",
            'social_telegram' => "VARCHAR(500) NULL",
            'social_website' => "VARCHAR(500) NULL",
            'country_code' => "CHAR(2) NULL",
            'country_name' => "VARCHAR(120) NULL",
            'google_locale' => "VARCHAR(32) NULL",
        ];
        foreach ($required as $column => $definition) {
            try {
                $stmt = $pdo->prepare("
                SELECT COUNT(*) as cnt
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'users'
                AND COLUMN_NAME = ?
            ");
                $stmt->execute([$column]);
                $exists = (int) ($stmt->fetch()['cnt'] ?? 0);
                if ($exists === 0) {
                    $pdo->exec("ALTER TABLE users ADD COLUMN {$column} {$definition}");
                }
            } catch (Throwable $e) {
            }
        }
    }

    function tfCountryList(): array
    {
        static $countries = null;
        if (is_array($countries)) {
            return $countries;
        }
        $path = __DIR__ . '/includes/countries.php';
        $countries = is_file($path) ? (require $path) : [];
        return is_array($countries) ? $countries : [];
    }

    function tfCountryFlag(string $code): string
    {
        $code = strtoupper(trim($code));
        if (!preg_match('/^[A-Z]{2}$/', $code)) {
            return '';
        }
        $base = 127397;
        $flag = '';
        foreach (str_split($code) as $char) {
            $point = $base + ord($char);
            if (function_exists('mb_chr')) {
                $flag .= mb_chr($point, 'UTF-8');
            } else {
                $flag .= html_entity_decode('&#' . $point . ';', ENT_NOQUOTES, 'UTF-8');
            }
        }
        return $flag;
    }

    function tfCountryFlagUrl(string $code): string
    {
        $code = strtoupper(trim($code));
        if (!preg_match('/^[A-Z]{2}$/', $code)) {
            return '';
        }
        return 'https://flagcdn.com/w20/' . strtolower($code) . '.png';
    }

    function tfNormalizeCountryLookupText(string $text): string
    {
        $text = normalizeMojibakeText($text);
        $text = preg_replace('/[\x{1F1E6}-\x{1F1FF}]{2}/u', ' ', $text);
        $text = preg_replace('/[^\p{L}\p{N}\s\-(),.]/u', ' ', $text);
        $text = preg_replace('/\s+/u', ' ', trim((string) $text));
        return mb_strtolower($text, 'UTF-8');
    }

    function tfResolveCountryByText(string $text): array
    {
        $lookup = tfNormalizeCountryLookupText($text);
        if ($lookup === '') {
            return [];
        }
        foreach (tfCountryList() as $code => $name) {
            $candidate = tfNormalizeCountryLookupText((string) $name);
            if ($candidate === '') {
                continue;
            }
            if ($lookup === $candidate || str_contains($lookup, $candidate) || str_contains($candidate, $lookup)) {
                return [
                    'country_code' => strtoupper((string) $code),
                    'country_name' => (string) $name,
                ];
            }
        }
        return [];
    }

    function tfResolveCountryFromLocale(string $locale): array
    {
        $locale = trim($locale);
        if ($locale === '') {
            return [];
        }
        if (preg_match('/[_-]([A-Za-z]{2})\b/', $locale, $m)) {
            $code = strtoupper((string) $m[1]);
            $countries = tfCountryList();
            if (isset($countries[$code])) {
                return [
                    'country_code' => $code,
                    'country_name' => (string) $countries[$code],
                ];
            }
        }

        $languageHints = [
            'tg' => 'TJ',
            'ru' => 'RU',
            'en' => 'US',
            'uz' => 'UZ',
            'kk' => 'KZ',
            'ky' => 'KG',
            'de' => 'DE',
            'fr' => 'FR',
            'es' => 'ES',
            'pt' => 'PT',
            'it' => 'IT',
            'tr' => 'TR',
            'ar' => 'SA',
            'fa' => 'IR',
            'hi' => 'IN',
            'ja' => 'JP',
            'ko' => 'KR',
            'zh' => 'CN',
        ];
        $normalizedLocale = strtolower(str_replace('_', '-', $locale));
        if (preg_match('/^([a-z]{2,3})$/', $normalizedLocale, $m)) {
            $language = $m[1];
            $hintCode = $languageHints[$language] ?? '';
            if ($hintCode !== '') {
                $resolved = tfResolveCountryByCode($hintCode);
                if (!empty($resolved)) {
                    return $resolved;
                }
            }
        }
        return [];
    }

    function tfResolveCountryForGoogleAccount(array $profile = [], string $browserLocale = ''): array
    {
        $googleLocale = trim((string) ($profile['locale'] ?? ''));
        $browserLocale = trim((string) $browserLocale);

        $candidates = array_filter([
            $googleLocale,
            $browserLocale,
        ], static function ($value) {
            return is_string($value) && trim($value) !== '';
        });

        foreach ($candidates as $candidate) {
            $resolved = tfResolveCountryFromLocale((string) $candidate);
            if (!empty($resolved)) {
                return [
                    'google_locale' => $googleLocale,
                    'country_code' => (string) ($resolved['country_code'] ?? ''),
                    'country_name' => (string) ($resolved['country_name'] ?? ''),
                ];
            }

            $resolved = tfResolveCountryByText((string) $candidate);
            if (!empty($resolved)) {
                return [
                    'google_locale' => $googleLocale,
                    'country_code' => (string) ($resolved['country_code'] ?? ''),
                    'country_name' => (string) ($resolved['country_name'] ?? ''),
                ];
            }
        }

        return [
            'google_locale' => $googleLocale,
            'country_code' => '',
            'country_name' => '',
        ];
    }

    function tfResolveCountryByCode(string $code): array
    {
        $code = strtoupper(trim($code));
        if (!preg_match('/^[A-Z]{2}$/', $code)) {
            return [];
        }
        $countries = tfCountryList();
        if (!isset($countries[$code])) {
            return [];
        }
        return [
            'country_code' => $code,
            'country_name' => (string) $countries[$code],
        ];
    }

    function tfDecorateUserCountry(array $user): array
    {
        $code = strtoupper(trim((string) ($user['country_code'] ?? '')));
        $name = trim((string) ($user['country_name'] ?? ''));
        if ($code !== '' && $name === '') {
            $resolvedByCode = tfResolveCountryByCode($code);
            if (!empty($resolvedByCode)) {
                $name = (string) ($resolvedByCode['country_name'] ?? $name);
                $user['country_name'] = $name;
            }
        }
        if ($code === '' || $name === '') {
            $resolved = tfResolveCountryFromLocale((string) ($user['google_locale'] ?? ''));
            if (empty($resolved)) {
                $resolved = tfResolveCountryByText((string) ($user['location'] ?? ''));
            }
            if (!empty($resolved)) {
                $code = (string) ($resolved['country_code'] ?? $code);
                $name = (string) ($resolved['country_name'] ?? $name);
                $user['country_code'] = $code;
                $user['country_name'] = $name;
            }
        }
        $user['country_flag'] = $code !== '' ? tfCountryFlag($code) : '';
        $user['country_flag_url'] = $code !== '' ? tfCountryFlagUrl($code) : '';
        $user['country_label'] = $name;
        return $user;
    }

    function getContestActivityHeatmap(int $userId, int $days = 140): array
    {
        $pdo = getDBConnection();
        ensureContestsSchema($pdo);
        $days = max(28, min(365, $days));
        $startTs = strtotime('-' . ($days - 1) . ' days');
        $startTs = $startTs ?: time();
        $map = [];
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', $startTs + ($i * 86400));
            $map[$date] = ['count' => 0, 'items' => []];
        }

        try {
            $startDate = date('Y-m-d 00:00:00', $startTs);
            $stmt = $pdo->prepare("
                SELECT
                    cs.status,
                    cs.created_at,
                    cs.updated_at,
                    ct.title AS task_title,
                    c.title AS contest_title
                FROM contest_submissions cs
                LEFT JOIN contest_tasks ct ON ct.id = cs.task_id
                LEFT JOIN contests c ON c.id = cs.contest_id
                WHERE cs.user_id = ?
                  AND (
                    cs.created_at >= ?
                    OR cs.updated_at >= ?
                  )
                ORDER BY cs.updated_at DESC, cs.id DESC
            ");
            $stmt->execute([$userId, $startDate, $startDate]);
            $rows = $stmt->fetchAll() ?: [];
            foreach ($rows as $row) {
                $taskTitle = normalizeMojibakeText((string) ($row['task_title'] ?? 'Task'));
                $contestTitle = normalizeMojibakeText((string) ($row['contest_title'] ?? 'Contest'));
                $status = (string) ($row['status'] ?? 'rejected');
                $events = [
                    [
                        'time' => (string) ($row['created_at'] ?? ''),
                        'text' => $status === 'accepted'
                            ? "Accepted: {$contestTitle} / {$taskTitle}"
                            : "Submitted: {$contestTitle} / {$taskTitle}",
                    ],
                ];
                $updatedAt = (string) ($row['updated_at'] ?? '');
                $createdAt = (string) ($row['created_at'] ?? '');
                if ($updatedAt !== '' && substr($updatedAt, 0, 10) !== substr($createdAt, 0, 10)) {
                    $events[] = [
                        'time' => $updatedAt,
                        'text' => $status === 'accepted'
                            ? "Solved: {$contestTitle} / {$taskTitle}"
                            : "Updated attempt: {$contestTitle} / {$taskTitle}",
                    ];
                }
                foreach ($events as $event) {
                    $time = (string) ($event['time'] ?? '');
                    if ($time === '') {
                        continue;
                    }
                    $date = date('Y-m-d', strtotime($time));
                    if (!isset($map[$date])) {
                        continue;
                    }
                    $map[$date]['count']++;
                    $map[$date]['items'][] = [
                        'time' => date('H:i', strtotime($time)),
                        'text' => translateActivityMessage((string) ($event['text'] ?? '')),
                    ];
                }
            }
        } catch (Throwable $e) {
        }

        return $map;
    }

    function ensureUserCvCustomizationSchema($pdo)
    {
        try {
            $pdo->exec("
            CREATE TABLE IF NOT EXISTS user_cv_customizations (
                user_id INT NOT NULL PRIMARY KEY,
                settings_json LONGTEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_updated_at (updated_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        } catch (Throwable $e) {
        }
    }

    function ensureUserPortfolioSchema($pdo)
    {
        try {
            $stmt = $pdo->prepare("
            SELECT COUNT(*) as cnt
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'user_portfolio'
            AND COLUMN_NAME = 'github_url'
        ");
            $stmt->execute();
            $exists = (int) ($stmt->fetch()['cnt'] ?? 0);
            if ($exists === 0) {
                $pdo->exec("ALTER TABLE user_portfolio ADD COLUMN github_url VARCHAR(500) NULL");
            }
        } catch (Throwable $e) {
        }
    }

    function getActivityHeatmap($userId, $days = 84)
    {
        $pdo = getDBConnection();
        $days = max(28, min(365, (int) $days));
        $startDate = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
        $stmt = $pdo->prepare("
        SELECT DATE(activity_time) as day, COUNT(*) as cnt
        FROM user_activities
        WHERE user_id = ? AND activity_time >= ?
        GROUP BY DATE(activity_time)
    ");
        $stmt->execute([$userId, $startDate]);
        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $row) {
            $map[$row['day']] = (int) $row['cnt'];
        }
        return [
            'start' => $startDate,
            'days' => $days,
            'map' => $map
        ];
    }

    function ensureVacancyCurrencySchema($pdo)
    {
        try {
            $stmt = $pdo->prepare("
            SELECT COUNT(*) as cnt
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'vacancies'
            AND COLUMN_NAME = 'salary_currency'
        ");
            $stmt->execute();
            $exists = (int) ($stmt->fetch()['cnt'] ?? 0);
            if ($exists === 0) {
                $pdo->exec("ALTER TABLE vacancies ADD COLUMN salary_currency VARCHAR(3) DEFAULT 'TJS' AFTER salary_max");
            }
        } catch (Exception $e) {
        }
    }

    function tfIsSafeMethod(): bool
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        return in_array($method, ['GET', 'HEAD', 'OPTIONS'], true);
    }

    function tfExtractCsrfToken(): string
    {
        $headerToken = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_SERVER['HTTP_X_XSRF_TOKEN'] ?? '');
        if ($headerToken !== '') {
            return $headerToken;
        }
        $postToken = (string) ($_POST['_csrf'] ?? '');
        if ($postToken !== '') {
            return $postToken;
        }
        return '';
    }

    function tfValidateCsrfToken(string $token): bool
    {
        $sessionToken = (string) ($_SESSION['csrf_token'] ?? '');
        if ($sessionToken === '' || $token === '') {
            return false;
        }
        return hash_equals($sessionToken, $token);
    }

    function tfEnforceCsrf(bool $isAjax = false): void
    {
        if (tfIsSafeMethod()) {
            return;
        }
        $token = tfExtractCsrfToken();
        if (tfValidateCsrfToken($token)) {
            return;
        }
        http_response_code(403);
        if ($isAjax) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'message' => 'CSRF token missing or invalid.']);
        } else {
            echo 'CSRF token missing or invalid.';
        }
        exit;
    }

    function tfValidateRequiredFields(array $data, array $fields): array
    {
        $missing = [];
        foreach ($fields as $field) {
            $value = $data[$field] ?? null;
            if ($value === null || (is_string($value) && trim($value) === '')) {
                $missing[] = $field;
            }
        }
        return $missing;
    }

    function tfSafeJson($data, int $flags = 0): string
    {
        $safeFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS;
        $flags = $flags | $safeFlags;
        $encoded = json_encode($data, $flags);
        return $encoded !== false ? $encoded : 'null';
    }

    // Дополнительные функции для практики и контестов
    function ensurePracticeSchema(PDO $pdo)
    {
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS lesson_practice_tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lesson_id INT NOT NULL,
        language ENUM('python', 'cpp', 'c', 'csharp', 'java', 'js', 'mysql', 'pgsql', 'fill') NOT NULL,
        title VARCHAR(255) DEFAULT NULL,
        prompt LONGTEXT DEFAULT NULL,
        starter_code LONGTEXT DEFAULT NULL,
        tests_json LONGTEXT DEFAULT NULL,
        is_required TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_lesson_lang (lesson_id, language),
        INDEX idx_lesson_id (lesson_id),
        INDEX idx_lang (language)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $stmt = $pdo->prepare("
        SELECT COLUMN_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'lesson_practice_tasks' AND COLUMN_NAME = 'language'
        LIMIT 1
    ");
        $stmt->execute([DB_NAME]);
        $colType = (string) ($stmt->fetchColumn() ?: '');
        if (
            $colType !== ''
            && (
                stripos($colType, "'c'") === false
                || stripos($colType, "'csharp'") === false
                || stripos($colType, "'java'") === false
                || stripos($colType, "'mysql'") === false
                || stripos($colType, "'pgsql'") === false
                || stripos($colType, "'fill'") === false
            )
        ) {
            $pdo->exec("ALTER TABLE lesson_practice_tasks MODIFY language ENUM('python', 'cpp', 'c', 'csharp', 'java', 'js', 'mysql', 'pgsql', 'fill') NOT NULL");
        }

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS practice_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        task_id INT NOT NULL,
        code LONGTEXT NOT NULL,
        passed TINYINT(1) DEFAULT 0,
        stdout LONGTEXT DEFAULT NULL,
        stderr LONGTEXT DEFAULT NULL,
        details_json LONGTEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_task (user_id, task_id),
        INDEX idx_task (task_id),
        INDEX idx_passed (passed)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
        ensureContestsSchema($pdo);
    }

    function ensureContestsSchema(PDO $pdo)
    {
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS contests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) DEFAULT NULL,
        description TEXT,
        is_active TINYINT(1) DEFAULT 1,
        starts_at DATETIME NULL,
        ends_at DATETIME NULL,
        duration_minutes INT DEFAULT NULL,
        is_locked TINYINT(1) DEFAULT 0,
        locked_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_contests_slug (slug),
        INDEX idx_contests_active (is_active)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS contest_tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        contest_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'easy',
        statement LONGTEXT,
        input_spec TEXT,
        output_spec TEXT,
        time_limit_sec INT DEFAULT 3,
        memory_limit_kb INT DEFAULT 262144,
        starter_cpp LONGTEXT,
        starter_python LONGTEXT,
        tests_json LONGTEXT,
        tags_json LONGTEXT,
        order_num INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_contest_tasks_contest (contest_id),
        INDEX idx_contest_tasks_order (order_num)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS interview_prep_tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        source_type ENUM('contest_task') DEFAULT 'contest_task',
        source_task_id INT DEFAULT NULL,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) DEFAULT NULL,
        difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'easy',
        category VARCHAR(100) DEFAULT 'General',
        statement LONGTEXT,
        input_spec TEXT,
        output_spec TEXT,
        starter_cpp LONGTEXT,
        starter_python LONGTEXT,
        tests_json LONGTEXT,
        sort_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_interview_prep_source (source_type, source_task_id),
        INDEX idx_interview_prep_active (is_active),
        INDEX idx_interview_prep_order (sort_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS task_progress (
        user_id INT NOT NULL,
        task_id INT NOT NULL,
        status ENUM('todo', 'in_progress', 'solved') DEFAULT 'todo',
        last_opened_at TIMESTAMP NULL,
        solved_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id, task_id),
        INDEX idx_task_progress_task (task_id),
        INDEX idx_task_progress_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS task_timer_states (
        user_id INT NOT NULL,
        task_id INT NOT NULL,
        remaining_seconds INT DEFAULT 900,
        is_running TINYINT(1) DEFAULT 0,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id, task_id),
        INDEX idx_task_timer_task (task_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS task_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(20) NOT NULL,
        task_id INT NOT NULL,
        owner_id INT NOT NULL,
        code_snapshot LONGTEXT,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_task_sessions_code (code),
        INDEX idx_task_sessions_task (task_id),
        INDEX idx_task_sessions_owner (owner_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS task_session_members (
        session_id INT NOT NULL,
        user_id INT NOT NULL,
        role ENUM('owner', 'member') DEFAULT 'member',
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (session_id, user_id),
        INDEX idx_task_session_members_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS contest_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        contest_id INT NOT NULL,
        task_id INT NOT NULL,
        language ENUM('cpp', 'python', 'c', 'csharp', 'java') DEFAULT 'cpp',
        code LONGTEXT,
        status ENUM('accepted', 'rejected') DEFAULT 'rejected',
        points_awarded INT DEFAULT 0,
        checks_passed INT DEFAULT 0,
        checks_total INT DEFAULT 0,
        details_json LONGTEXT DEFAULT NULL,
        attempts INT DEFAULT 0,
        wrong_attempts INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_contest_submission_user_task (user_id, task_id),
        INDEX idx_contest_submission_user (user_id),
        INDEX idx_contest_submission_contest (contest_id),
        INDEX idx_contest_submission_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS contest_results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        contest_id INT NOT NULL,
        user_id INT NOT NULL,
        points INT DEFAULT 0,
        solved_count INT DEFAULT 0,
        rank_pos INT DEFAULT 0,
        snapshot_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_contest_result_user (contest_id, user_id),
        INDEX idx_contest_results_contest (contest_id),
        INDEX idx_contest_results_rank (contest_id, rank_pos)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $stmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ?
        AND TABLE_NAME = 'contests'
    ");
        $stmt->execute([DB_NAME]);
        $existing = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
        $columns = [
            'starts_at' => "DATETIME NULL",
            'ends_at' => "DATETIME NULL",
            'duration_minutes' => "INT DEFAULT NULL",
            'is_locked' => "TINYINT(1) DEFAULT 0",
            'locked_at' => "TIMESTAMP NULL",
        ];
        foreach ($columns as $col => $def) {
            if (!isset($existing[$col])) {
                $pdo->exec("ALTER TABLE contests ADD COLUMN {$col} {$def}");
            }
        }

        $stmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ?
        AND TABLE_NAME = 'contest_tasks'
    ");
        $stmt->execute([DB_NAME]);
        $taskCols = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
        if (!isset($taskCols['tags_json'])) {
            $pdo->exec("ALTER TABLE contest_tasks ADD COLUMN tags_json LONGTEXT");
        }
        if (!isset($taskCols['time_limit_sec'])) {
            $pdo->exec("ALTER TABLE contest_tasks ADD COLUMN time_limit_sec INT DEFAULT 3 AFTER output_spec");
        }
        if (!isset($taskCols['memory_limit_kb'])) {
            $pdo->exec("ALTER TABLE contest_tasks ADD COLUMN memory_limit_kb INT DEFAULT 262144 AFTER time_limit_sec");
        }

        $stmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ?
        AND TABLE_NAME = 'interview_prep_tasks'
    ");
        $stmt->execute([DB_NAME]);
        $prepCols = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
        $prepAdditions = [
            'source_type' => "ENUM('contest_task') DEFAULT 'contest_task'",
            'source_task_id' => "INT DEFAULT NULL",
            'slug' => "VARCHAR(255) DEFAULT NULL",
            'category' => "VARCHAR(100) DEFAULT 'General'",
            'starter_cpp' => "LONGTEXT",
            'starter_python' => "LONGTEXT",
            'sort_order' => "INT DEFAULT 0",
            'is_active' => "TINYINT(1) DEFAULT 1",
        ];
        foreach ($prepAdditions as $col => $def) {
            if (!isset($prepCols[$col])) {
                $pdo->exec("ALTER TABLE interview_prep_tasks ADD COLUMN {$col} {$def}");
            }
        }

        $stmt = $pdo->prepare("
        SELECT COLUMN_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'contest_submissions' AND COLUMN_NAME = 'language'
        LIMIT 1
    ");
        $stmt->execute([DB_NAME]);
        $contestLangCol = (string) ($stmt->fetchColumn() ?: '');
        if (
            $contestLangCol !== ''
            && (
                stripos($contestLangCol, "'c'") === false
                || stripos($contestLangCol, "'csharp'") === false
                || stripos($contestLangCol, "'java'") === false
            )
        ) {
            $pdo->exec("ALTER TABLE contest_submissions MODIFY language ENUM('cpp', 'python', 'c', 'csharp', 'java') DEFAULT 'cpp'");
        }
        $stmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ?
          AND TABLE_NAME = 'contest_submissions'
    ");
        $stmt->execute([DB_NAME]);
        $contestCols = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
        if (!isset($contestCols['details_json'])) {
            $pdo->exec("ALTER TABLE contest_submissions ADD COLUMN details_json LONGTEXT DEFAULT NULL AFTER checks_total");
        }
        ensureCommunitySchema($pdo);
    }

    function ensureInterviewsSchema(PDO $pdo)
    {
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS interviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        question_count INT DEFAULT 1,
        status ENUM('created','completed') DEFAULT 'created',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_interviews_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS interview_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        interview_id INT NOT NULL,
        code VARCHAR(20) NOT NULL,
        code_snapshot LONGTEXT,
        boards_snapshot LONGTEXT,
        remaining_seconds INT DEFAULT 0,
        is_running TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_interview_sessions_code (code),
        INDEX idx_interview_sessions_interview (interview_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS interview_participants (
        session_id INT NOT NULL,
        user_id INT NOT NULL,
        role ENUM('owner','member') DEFAULT 'member',
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (session_id, user_id),
        INDEX idx_interview_participants_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS interview_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id INT NOT NULL,
        user_id INT NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_interview_messages_session (session_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS interview_ai_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        mode VARCHAR(32) NOT NULL DEFAULT 'mock',
        title VARCHAR(255) NOT NULL,
        context_json LONGTEXT NULL,
        output_text LONGTEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_interview_ai_sessions_user (user_id),
        INDEX idx_interview_ai_sessions_updated (updated_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $pdo->exec("
    CREATE TABLE IF NOT EXISTS interview_ai_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id INT NOT NULL,
        sender ENUM('user','ai') NOT NULL,
        message_text LONGTEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_interview_ai_messages_session (session_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        $hasColumn = static function (string $table, string $column) use ($pdo): bool {
            $safeTable = preg_replace('/[^a-z0-9_]/i', '', $table);
            $safeColumn = preg_replace('/[^a-z0-9_]/i', '', $column);
            if ($safeTable === '' || $safeColumn === '') {
                return false;
            }
            $stmt = $pdo->query("SHOW COLUMNS FROM `{$safeTable}` LIKE " . $pdo->quote($safeColumn));
            return (bool) ($stmt && $stmt->fetch());
        };

        $ensureColumn = static function (string $table, string $column, string $definition) use ($pdo, $hasColumn): void {
            if ($hasColumn($table, $column)) {
                return;
            }
            $safeTable = preg_replace('/[^a-z0-9_]/i', '', $table);
            $safeColumn = preg_replace('/[^a-z0-9_]/i', '', $column);
            if ($safeTable === '' || $safeColumn === '') {
                return;
            }
            $pdo->exec("ALTER TABLE `{$safeTable}` ADD COLUMN `{$safeColumn}` {$definition}");
        };

        $ensureIndex = static function (string $table, string $index, string $definition) use ($pdo): void {
            $safeTable = preg_replace('/[^a-z0-9_]/i', '', $table);
            $safeIndex = preg_replace('/[^a-z0-9_]/i', '', $index);
            if ($safeTable === '' || $safeIndex === '') {
                return;
            }
            $stmt = $pdo->query("SHOW INDEX FROM `{$safeTable}` WHERE Key_name = " . $pdo->quote($safeIndex));
            if ($stmt && $stmt->fetch()) {
                return;
            }
            $pdo->exec("ALTER TABLE `{$safeTable}` ADD {$definition}");
        };

        $ensureColumn('interviews', 'question_count', "INT DEFAULT 1 AFTER `title`");
        $ensureColumn('interviews', 'status', "ENUM('created','completed') DEFAULT 'created' AFTER `question_count`");
        $ensureColumn('interviews', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`");
        $ensureIndex('interviews', 'idx_interviews_user', "INDEX `idx_interviews_user` (`user_id`)");

        $ensureColumn('interview_sessions', 'boards_snapshot', "LONGTEXT NULL AFTER `code_snapshot`");
        $ensureColumn('interview_sessions', 'remaining_seconds', "INT DEFAULT 0 AFTER `boards_snapshot`");
        $ensureColumn('interview_sessions', 'is_running', "TINYINT(1) DEFAULT 0 AFTER `remaining_seconds`");
        $ensureColumn('interview_sessions', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`");
        $ensureIndex('interview_sessions', 'uniq_interview_sessions_code', "UNIQUE KEY `uniq_interview_sessions_code` (`code`)");
        $ensureIndex('interview_sessions', 'idx_interview_sessions_interview', "INDEX `idx_interview_sessions_interview` (`interview_id`)");

        $ensureColumn('interview_participants', 'role', "ENUM('owner','member') DEFAULT 'member' AFTER `user_id`");
        $ensureColumn('interview_participants', 'joined_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `role`");
        $ensureIndex('interview_participants', 'idx_interview_participants_user', "INDEX `idx_interview_participants_user` (`user_id`)");

        $ensureIndex('interview_messages', 'idx_interview_messages_session', "INDEX `idx_interview_messages_session` (`session_id`)");
        $ensureIndex('interview_ai_sessions', 'idx_interview_ai_sessions_user', "INDEX `idx_interview_ai_sessions_user` (`user_id`)");
        $ensureIndex('interview_ai_sessions', 'idx_interview_ai_sessions_updated', "INDEX `idx_interview_ai_sessions_updated` (`updated_at`)");
        $ensureIndex('interview_ai_messages', 'idx_interview_ai_messages_session', "INDEX `idx_interview_ai_messages_session` (`session_id`)");
    }

    function tfSnapshotContestResults(PDO $pdo, int $contestId): void
    {
        $rows = function_exists('tfBuildContestRatingSummary')
            ? tfBuildContestRatingSummary($pdo, $contestId, 2000, false)
            : [];
        if (empty($rows)) {
            return;
        }
        $insert = $pdo->prepare("
        INSERT INTO contest_results (contest_id, user_id, points, solved_count, rank_pos, snapshot_at)
        VALUES (?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
            points = VALUES(points),
            solved_count = VALUES(solved_count),
            rank_pos = VALUES(rank_pos),
            snapshot_at = VALUES(snapshot_at)
    ");
        $rank = 1;
        foreach ($rows as $row) {
            $insert->execute([
                $contestId,
                (int) ($row['id'] ?? 0),
                (int) ($row['contest_points'] ?? 0),
                (int) ($row['solved_count'] ?? 0),
                $rank
            ]);
            $rank++;
        }
    }

    function tfAutoLockExpiredContests(PDO $pdo): void
    {
        $stmt = $pdo->query("
        SELECT id
        FROM contests
        WHERE is_locked = 0
        AND ends_at IS NOT NULL
        AND ends_at <= NOW()
    ");
        $expired = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        if (empty($expired)) {
            return;
        }
        foreach ($expired as $contestId) {
            $contestId = (int) $contestId;
            if ($contestId <= 0)
                continue;
            tfSnapshotContestResults($pdo, $contestId);
            $upd = $pdo->prepare("UPDATE contests SET is_locked = 1, is_active = 0, locked_at = NOW() WHERE id = ? AND is_locked = 0");
            $upd->execute([$contestId]);
        }
    }

function ensureCommunitySchema(PDO $pdo)
{
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS community_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content LONGTEXT NOT NULL,
        likes_count INT NOT NULL DEFAULT 0,
        views_count INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_community_posts_created (created_at),
        INDEX idx_community_posts_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS community_comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_community_comments_post (post_id),
        INDEX idx_community_comments_user (user_id),
        INDEX idx_community_comments_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS community_post_likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_community_post_like (post_id, user_id),
        INDEX idx_community_post_like_post (post_id),
        INDEX idx_community_post_like_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $stmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'community_posts'
    ");
    $stmt->execute([DB_NAME]);
    $cols = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
    if (!isset($cols['likes_count'])) {
        $pdo->exec("ALTER TABLE community_posts ADD COLUMN likes_count INT NOT NULL DEFAULT 0");
    }
    if (!isset($cols['views_count'])) {
        $pdo->exec("ALTER TABLE community_posts ADD COLUMN views_count INT NOT NULL DEFAULT 0");
    }
}

    function ensureCertificatesSchema(PDO $pdo)
    {
        $stmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'certificates'
    ");
        $stmt->execute([DB_NAME]);
        $existingCols = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
        if (!isset($existingCols['cert_hash'])) {
            $pdo->exec("ALTER TABLE certificates ADD COLUMN cert_hash VARCHAR(100) DEFAULT NULL");
        }
        $stmt = $pdo->prepare("
        SELECT INDEX_NAME
        FROM INFORMATION_SCHEMA.STATISTICS
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'certificates'
    ");
        $stmt->execute([DB_NAME]);
        $indexes = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
        if (!isset($indexes['uniq_cert_hash'])) {
            $pdo->exec("ALTER TABLE certificates ADD UNIQUE KEY uniq_cert_hash (cert_hash)");
        }
        $pdo->exec("
    UPDATE certificates
    SET cert_hash = REPLACE(UUID(), '-', '')
    WHERE cert_hash IS NULL OR cert_hash = ''
    ");
    }

    function ensureRoadmapTables($pdo)
    {
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS roadmap_list (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL UNIQUE,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS roadmap_nodes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        roadmap_title VARCHAR(255) DEFAULT 'Основной',
        topic VARCHAR(255) DEFAULT NULL,
        materials TEXT,
        x INT NOT NULL DEFAULT 0,
        y INT NOT NULL DEFAULT 0,
        deps TEXT,
        is_exam TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_roadmap_title (roadmap_title)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS roadmap_lessons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        node_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        video_url VARCHAR(500),
        description TEXT,
        materials TEXT,
        order_index INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_node_id (node_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS roadmap_quiz_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        node_id INT NOT NULL,
        question TEXT NOT NULL,
        options TEXT NOT NULL,
        correct_answer VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_node_id (node_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS roadmap_user_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        node_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_user_node (user_id, node_id),
        INDEX idx_user_id (user_id),
        INDEX idx_node_id (node_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS roadmap_certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        node_id INT NOT NULL,
        cert_hash VARCHAR(100) NOT NULL,
        issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_roadmap_cert_hash (cert_hash),
        UNIQUE KEY uniq_roadmap_user_node (user_id, node_id),
        INDEX idx_user_id (user_id),
        INDEX idx_node_id (node_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

        try {
            $stmt = $pdo->prepare("
            SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'roadmap_nodes'
        ");
            $stmt->execute();
            $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('roadmap_title', $existing, true)) {
                $pdo->exec("ALTER TABLE roadmap_nodes ADD COLUMN roadmap_title VARCHAR(255) DEFAULT 'Основной'");
            }
            if (!in_array('topic', $existing, true)) {
                $pdo->exec("ALTER TABLE roadmap_nodes ADD COLUMN topic VARCHAR(255) DEFAULT NULL");
            }
            if (!in_array('materials', $existing, true)) {
                $pdo->exec("ALTER TABLE roadmap_nodes ADD COLUMN materials TEXT");
            }
        } catch (Throwable $e) {
        }

        try {
            $stmt = $pdo->prepare("
            SELECT INDEX_NAME
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'roadmap_nodes'
        ");
            $stmt->execute();
            $indexes = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
            if (!isset($indexes['idx_roadmap_title'])) {
                $pdo->exec("ALTER TABLE roadmap_nodes ADD INDEX idx_roadmap_title (roadmap_title)");
            }
        } catch (Throwable $e) {
        }

        try {
            $stmt = $pdo->prepare("
            SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'roadmap_user_progress'
        ");
            $stmt->execute();
            $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('lesson_done', $existing, true)) {
                $pdo->exec("ALTER TABLE roadmap_user_progress ADD COLUMN lesson_done TINYINT(1) DEFAULT 0");
            }
            if (!in_array('quiz_score', $existing, true)) {
                $pdo->exec("ALTER TABLE roadmap_user_progress ADD COLUMN quiz_score INT DEFAULT 0");
            }
            if (!in_array('quiz_total', $existing, true)) {
                $pdo->exec("ALTER TABLE roadmap_user_progress ADD COLUMN quiz_total INT DEFAULT 0");
            }
            if (!in_array('completed_at', $existing, true)) {
                $pdo->exec("ALTER TABLE roadmap_user_progress ADD COLUMN completed_at TIMESTAMP NULL DEFAULT NULL");
            }
            if (!in_array('updated_at', $existing, true)) {
                $pdo->exec("ALTER TABLE roadmap_user_progress ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
            }
        } catch (Throwable $e) {
        }

        try {
            // Keep only one certificate per user+exam before adding unique index.
            $pdo->exec("
        DELETE rc1
        FROM roadmap_certificates rc1
        INNER JOIN roadmap_certificates rc2
        ON rc1.user_id = rc2.user_id
        AND rc1.node_id = rc2.node_id
        AND rc1.id > rc2.id
        ");
            $pdo->exec("
        UPDATE roadmap_certificates
        SET cert_hash = UPPER(SUBSTRING(REPLACE(UUID(), '-', ''), 1, 16))
        WHERE cert_hash IS NULL OR cert_hash = ''
        ");
            // Resolve hash collisions before unique index creation.
            $pdo->exec("
        UPDATE roadmap_certificates rc
        INNER JOIN (
            SELECT cert_hash, MIN(id) AS keep_id
            FROM roadmap_certificates
            WHERE cert_hash IS NOT NULL AND cert_hash <> ''
            GROUP BY cert_hash
            HAVING COUNT(*) > 1
        ) dup ON dup.cert_hash = rc.cert_hash
        SET rc.cert_hash = CONCAT(rc.cert_hash, '-', rc.id)
        WHERE rc.id <> dup.keep_id
        ");
            $stmt = $pdo->prepare("
            SELECT INDEX_NAME
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'roadmap_certificates'
        ");
            $stmt->execute();
            $indexes = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
            if (!isset($indexes['uniq_roadmap_cert_hash'])) {
                $pdo->exec("ALTER TABLE roadmap_certificates ADD UNIQUE KEY uniq_roadmap_cert_hash (cert_hash)");
            }
            if (!isset($indexes['uniq_roadmap_user_node'])) {
                $pdo->exec("ALTER TABLE roadmap_certificates ADD UNIQUE KEY uniq_roadmap_user_node (user_id, node_id)");
            }
        } catch (Throwable $e) {
        }
    }

    function ensureAiTables($pdo)
    {
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS lesson_tests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lesson_id INT NOT NULL,
        test_json LONGTEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_lesson_id (lesson_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS course_exams (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        exam_json LONGTEXT NOT NULL,
        time_limit_minutes INT DEFAULT 60,
        pass_percent INT DEFAULT 70,
        shuffle_questions BOOLEAN DEFAULT TRUE,
        shuffle_options BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_course_id (course_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    }

    function ensureCourseExamsSchema(PDO $pdo)
    {
        $pdo->exec("
    CREATE TABLE IF NOT EXISTS course_exams (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        exam_json LONGTEXT NOT NULL,
        time_limit_minutes INT DEFAULT 60,
        pass_percent INT DEFAULT 70,
        shuffle_questions BOOLEAN DEFAULT TRUE,
        shuffle_options BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_course_id (course_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
        $stmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'course_exams'
    ");
        $stmt->execute();
        $existing = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
        $required = [
            'exam_json' => "exam_json LONGTEXT NOT NULL",
            'time_limit_minutes' => "time_limit_minutes INT DEFAULT 60",
            'pass_percent' => "pass_percent INT DEFAULT 70",
            'shuffle_questions' => "shuffle_questions TINYINT(1) DEFAULT 1",
            'shuffle_options' => "shuffle_options TINYINT(1) DEFAULT 1",
            'created_at' => "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        ];
        foreach ($required as $column => $definition) {
            if (!isset($existing[$column])) {
                $pdo->exec("ALTER TABLE course_exams ADD COLUMN {$definition}");
            }
        }
    }

    function ensureCoursesSchema(PDO $pdo)
    {
        $columns = [
            'instructor' => "instructor VARCHAR(255) NOT NULL DEFAULT ''",
            'description' => "description TEXT",
            'category' => "category ENUM('frontend', 'backend', 'design', 'devops', 'other') DEFAULT 'frontend'",
            'level' => "level ENUM('Начальный', 'Средний', 'Продвинутый') DEFAULT 'Начальный'",
            'progress' => "progress INT DEFAULT 0",
            'image_url' => "image_url VARCHAR(500)",
            'materials_title' => "materials_title VARCHAR(255)",
            'materials_url' => "materials_url VARCHAR(500)",
            'created_at' => "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            'updated_at' => "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        ];
        $stmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'courses'
    ");
        $stmt->execute([DB_NAME]);
        $existing = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
        foreach ($columns as $name => $definition) {
            if (!isset($existing[$name])) {
                $pdo->exec("ALTER TABLE courses ADD COLUMN {$definition}");
            }
        }
    }

    function ensureLessonsSchema(PDO $pdo)
    {
        $columns = [
            'materials_title' => "materials_title VARCHAR(255)",
            'materials_url' => "materials_url VARCHAR(500)",
        ];
        $stmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'lessons'
    ");
        $stmt->execute([DB_NAME]);
        $existing = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
        foreach ($columns as $name => $definition) {
            if (!isset($existing[$name])) {
                $pdo->exec("ALTER TABLE lessons ADD COLUMN {$definition}");
            }
        }
        if (isset($existing['duration'])) {
            $pdo->exec("ALTER TABLE lessons DROP COLUMN duration");
        }
    }

}
