<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Vacancy;
use App\Models\RoadmapNode;
use App\Models\Certificate;
use App\Models\Notification;
use App\Models\UserActivity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
        ]);

        $courses = $this->seedCourses();
        $user = $this->seedUser();
        $this->seedLessons($courses);
        $this->seedVacancies();
        $this->seedRoadmapNodes();
        $this->seedCourseExams($courses);
        $this->seedCertificates($courses, $user);
        $this->seedNotifications($user);
        $this->seedUserActivities($user);
        $this->seedCommunityPosts($user);
    }

    private function seedCourses(): array
    {
        $courses = [
            [
                'title' => 'HTML+CSS',
                'instructor' => 'Александр Белов',
                'description' => 'Изучите основы веб-разработки с нуля. Курс охватывает HTML5, CSS3, Flexbox, Grid, адаптивную вёрстку и семантическую разметку.',
                'category' => 'frontend',
                'level' => 'Начальный',
                'image_url' => '/images/courses/html-css.jpg',
                'materials_title' => 'Материалы по HTML+CSS',
                'materials_url' => '/materials/html-css',
            ],
            [
                'title' => 'JavaScript',
                'instructor' => 'Мария Петрова',
                'description' => 'Полный курс по JavaScript: переменные, функции, ООП, асинхронность, работа с DOM, события и современные возможности ES6+.',
                'category' => 'frontend',
                'level' => 'Начальный',
                'image_url' => '/images/courses/javascript.jpg',
                'materials_title' => 'Материалы по JavaScript',
                'materials_url' => '/materials/javascript',
            ],
            [
                'title' => 'PHP',
                'instructor' => 'Марат Юсупов',
                'description' => 'Изучение PHP с основ до продвинутых концепций: работа с формами, базами данных, сессиями, файлами и безопасность.',
                'category' => 'backend',
                'level' => 'Начальный',
                'image_url' => '/images/courses/php.jpg',
                'materials_title' => 'Материалы по PHP',
                'materials_url' => '/materials/php',
            ],
            [
                'title' => 'Laravel',
                'instructor' => 'Марат Юсупов',
                'description' => 'Фреймворк Laravel от основ до продвинутых тем: маршрутизация, Eloquent ORM, миграции, queues, авторизация и API.',
                'category' => 'backend',
                'level' => 'Средний',
                'image_url' => '/images/courses/laravel.jpg',
                'materials_title' => 'Материалы по Laravel',
                'materials_url' => '/materials/laravel',
            ],
            [
                'title' => 'MySQL',
                'instructor' => 'Никита Орлов',
                'description' => 'Реляционная СУБД MySQL: создание баз данных, запросы JOIN, индексы, транзакции, оптимизация и хранимые процедуры.',
                'category' => 'backend',
                'level' => 'Средний',
                'image_url' => '/images/courses/mysql.jpg',
                'materials_title' => 'Материалы по MySQL',
                'materials_url' => '/materials/mysql',
            ],
            [
                'title' => 'PostgreSQL',
                'instructor' => 'Никита Орлов',
                'description' => 'Мощная реляционная СУБД PostgreSQL: расширенные типы данных, CTE, оконные функции, JSON и расширения.',
                'category' => 'backend',
                'level' => 'Средний',
                'image_url' => '/images/courses/postgresql.jpg',
                'materials_title' => 'Материалы по PostgreSQL',
                'materials_url' => '/materials/postgresql',
            ],
            [
                'title' => 'C++',
                'instructor' => 'Дмитрий Козлов',
                'description' => 'Программирование на C++: типы данных, управление памятью, STL, шаблоны, полиморфизм и многопоточность.',
                'category' => 'backend',
                'level' => 'Средний',
                'image_url' => '/images/courses/cpp.jpg',
                'materials_title' => 'Материалы по C++',
                'materials_url' => '/materials/cpp',
            ],
            [
                'title' => 'Python',
                'instructor' => 'Анна Смирнова',
                'description' => 'Курс по Python: синтаксис, структуры данных, ООП, декораторы, генераторы, работа с библиотеками и веб-фреймворками.',
                'category' => 'backend',
                'level' => 'Начальный',
                'image_url' => '/images/courses/python.jpg',
                'materials_title' => 'Материалы по Python',
                'materials_url' => '/materials/python',
            ],
            [
                'title' => 'Java',
                'instructor' => 'Сергей Иванов',
                'description' => 'Объектно-ориентированное программирование на Java: синтаксис, коллекции, потоки, Spring Boot и разработка приложений.',
                'category' => 'backend',
                'level' => 'Средний',
                'image_url' => '/images/courses/java.jpg',
                'materials_title' => 'Материалы по Java',
                'materials_url' => '/materials/java',
            ],
            [
                'title' => 'C#',
                'instructor' => 'Алексей Новиков',
                'description' => 'Язык C# и платформа .NET: основы синтаксиса, LINQ, async/await, Entity Framework и разработка на ASP.NET Core.',
                'category' => 'backend',
                'level' => 'Средний',
                'image_url' => '/images/courses/csharp.jpg',
                'materials_title' => 'Материалы по C#',
                'materials_url' => '/materials/csharp',
            ],
            [
                'title' => 'Git',
                'instructor' => 'Олег Сидоров',
                'description' => 'Система контроля версий Git: установка, основные команды, ветвление, слияние, работа с удалёнными репозиториями и GitHub.',
                'category' => 'devops',
                'level' => 'Начальный',
                'image_url' => '/images/courses/git.jpg',
                'materials_title' => 'Материалы по Git',
                'materials_url' => '/materials/git',
            ],
            [
                'title' => 'DevOps',
                'instructor' => 'Павел Федоров',
                'description' => 'Основы DevOps: CI/CD, автоматизация деплоя, мониторинг, инфраструктура как код и культура командной работы.',
                'category' => 'devops',
                'level' => 'Продвинутый',
                'image_url' => '/images/courses/devops.jpg',
                'materials_title' => 'Материалы по DevOps',
                'materials_url' => '/materials/devops',
            ],
            [
                'title' => 'UI/UX Design',
                'instructor' => 'Ксения Андреева',
                'description' => 'Дизайн пользовательских интерфейсов: исследования, прототипирование, Figma, дизайн-системы и юзабилити-тестирование.',
                'category' => 'design',
                'level' => 'Начальный',
                'image_url' => '/images/courses/ui-ux.jpg',
                'materials_title' => 'Материалы по UI/UX',
                'materials_url' => '/materials/ui-ux',
            ],
            [
                'title' => 'React',
                'instructor' => 'Андрей Кузнецов',
                'description' => 'Библиотека React: компоненты, хуки, состояния, маршрутизация, управление состоянием Redux и серверный рендеринг.',
                'category' => 'frontend',
                'level' => 'Средний',
                'image_url' => '/images/courses/react.jpg',
                'materials_title' => 'Материалы по React',
                'materials_url' => '/materials/react',
            ],
            [
                'title' => 'Node.js',
                'instructor' => 'Игорь Волков',
                'description' => 'Серверный JavaScript с Node.js: модули, HTTP-сервер, Express.js, базы данных, REST API и WebSocket.',
                'category' => 'backend',
                'level' => 'Средний',
                'image_url' => '/images/courses/nodejs.jpg',
                'materials_title' => 'Материалы по Node.js',
                'materials_url' => '/materials/nodejs',
            ],
            [
                'title' => 'TypeScript',
                'instructor' => 'Елена Морозова',
                'description' => 'Надёжное программирование на TypeScript: типы, интерфейсы, дженерики, утилиты типов и интеграция с React и Node.js.',
                'category' => 'frontend',
                'level' => 'Средний',
                'image_url' => '/images/courses/typescript.jpg',
                'materials_title' => 'Материалы по TypeScript',
                'materials_url' => '/materials/typescript',
            ],
            [
                'title' => 'Docker',
                'instructor' => 'Павел Федоров',
                'description' => 'Контейнеризация с Docker: Dockerfile, docker-compose, сети, volumes, оптимизация образов и деплой.',
                'category' => 'devops',
                'level' => 'Средний',
                'image_url' => '/images/courses/docker.jpg',
                'materials_title' => 'Материалы по Docker',
                'materials_url' => '/materials/docker',
            ],
            [
                'title' => 'Kubernetes',
                'instructor' => 'Павел Федоров',
                'description' => 'Оркестрация контейнеров Kubernetes: поды, сервисы, деплойменты, конфигурации, масштабирование и мониторинг.',
                'category' => 'devops',
                'level' => 'Продвинутый',
                'image_url' => '/images/courses/kubernetes.jpg',
                'materials_title' => 'Материалы по Kubernetes',
                'materials_url' => '/materials/kubernetes',
            ],
            [
                'title' => 'Mobile Development',
                'instructor' => 'Роман Попов',
                'description' => 'Разработка мобильных приложений: кроссплатформенная разработка с Flutter, нативная с Kotlin и Swift, публикация в маркетах.',
                'category' => 'other',
                'level' => 'Средний',
                'image_url' => '/images/courses/mobile.jpg',
                'materials_title' => 'Материалы по Mobile',
                'materials_url' => '/materials/mobile',
            ],
            [
                'title' => 'English A1',
                'instructor' => 'София Каримова',
                'description' => 'Базовый курс английского языка для IT-специалистов: грамматика, лексика, чтение документации и технический словарный запас.',
                'category' => 'other',
                'level' => 'Начальный',
                'image_url' => '/images/courses/english.jpg',
                'materials_title' => 'Материалы по English',
                'materials_url' => '/materials/english',
            ],
        ];

        $coursesData = [];
        foreach ($courses as $courseData) {
            $course = Course::create($courseData);
            $coursesData[] = $course;
        }

        return $coursesData;
    }

    private function seedLessons(array $courses): void
    {
        $lessonTemplates = [
            ['title' => 'Введение', 'type' => 'video', 'content' => 'Обзор курса, структура и цели обучения.'],
            ['title' => 'Основы', 'type' => 'article', 'content' => 'Базовые концепции и ключевые определения темы.'],
            ['title' => 'Практика', 'type' => 'video', 'content' => 'Решение практических задач с объяснением каждого шага.'],
            ['title' => 'Продвинутые темы', 'type' => 'article', 'content' => 'Углублённое изучение сложных аспектов темы.'],
            ['title' => 'Итоговый тест', 'type' => 'quiz', 'content' => 'Тестовая проверка по пройденному материалу.'],
        ];

        foreach ($courses as $course) {
            foreach ($lessonTemplates as $index => $template) {
                $slug = Str::slug($course->title);
                Lesson::create([
                    'course_id' => $course->id,
                    'title' => $template['title'],
                    'type' => $template['type'],
                    'content' => $template['content'],
                    'video_url' => $template['type'] === 'video' ? "/videos/{$slug}/lesson-" . ($index + 1) . ".mp4" : null,
                    'materials_title' => "Материалы: {$template['title']}",
                    'materials_url' => "/materials/{$slug}/lesson-" . ($index + 1),
                    'completed' => false,
                    'order_num' => $index + 1,
                ]);
            }
        }
    }

    private function seedUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Тестовый Пользователь',
                'password' => bcrypt('password'),
                'role' => 'seeker',
                'is_verified' => true,
                'ai_coins' => 100,
                'title' => 'Junior Frontend Developer',
                'bio' => 'Начинающий разработчик, изучаю фронтенд-технологии.',
                'location' => 'Душанбе, Таджикистан',
                'country_code' => 'TJ',
                'country_name' => 'Таджикистан',
            ]
        );
    }

    private function seedVacancies(): void
    {
        if (Vacancy::count() > 0) {
            return;
        }

        $vacancies = [
            [
                'title' => 'Junior Frontend Developer',
                'company' => 'TechHub Dushanbe',
                'location' => 'Душанбе',
                'type' => 'remote',
                'salary_min' => 8000,
                'salary_max' => 15000,
                'salary_currency' => 'TJS',
                'description' => 'Ищем начинающего фронтенд-разработчика для работы над SPA-приложениями. Вы будете участвовать в разработке пользовательских интерфейсов для наших клиентов.',
                'company_description' => 'Технологическая компания, специализирующаяся на веб-разработке и мобильных приложениях.',
                'verified' => true,
                'owner_id' => null,
            ],
            [
                'title' => 'PHP Developer',
                'company' => 'WebPro Solutions',
                'location' => 'Душанбе',
                'type' => 'office',
                'salary_min' => 12000,
                'salary_max' => 25000,
                'salary_currency' => 'TJS',
                'description' => 'Требуется PHP-разработчик с опытом работы с Laravel. Разработка и поддержка серверной части веб-приложений.',
                'company_description' => 'Компания разрабатывает корпоративные системы управления и CRM-решения для бизнеса.',
                'verified' => true,
                'owner_id' => null,
            ],
            [
                'title' => 'Backend Developer (Python)',
                'company' => 'DataFlow Systems',
                'location' => 'Худжанд',
                'type' => 'hybrid',
                'salary_min' => 15000,
                'salary_max' => 30000,
                'salary_currency' => 'TJS',
                'description' => 'Разработчик бэкенда на Python для создания REST API и обработки данных. Опыт с Django или FastAPI приветствуется.',
                'company_description' => 'Компания занимается разработкой решений для анализа данных и машинного обучения.',
                'verified' => true,
                'owner_id' => null,
            ],
            [
                'title' => 'React Frontend Developer',
                'company' => 'Digital Studio',
                'location' => 'Душанбе',
                'type' => 'remote',
                'salary_min' => 18000,
                'salary_max' => 35000,
                'salary_currency' => 'TJS',
                'description' => 'Ищем опытного React-разработчика для создания современных веб-приложений. Знание TypeScript и Redux обязательны.',
                'company_description' => 'Студия цифровых продуктов, работающая с клиентами из Европы и США.',
                'verified' => true,
                'owner_id' => null,
            ],
            [
                'title' => 'DevOps Engineer',
                'company' => 'CloudTech TJ',
                'location' => 'Душанбе',
                'type' => 'office',
                'salary_min' => 20000,
                'salary_max' => 40000,
                'salary_currency' => 'TJS',
                'description' => 'Настройка и поддержка CI/CD пайплайнов, контейнеризация приложений с Docker, управление инфраструктурой на Kubernetes.',
                'company_description' => 'Облачный провайдер, предоставляющий услуги хостинга и DevOps-консалтинга.',
                'verified' => false,
                'owner_id' => null,
            ],
            [
                'title' => 'Junior Java Developer',
                'company' => 'InnoSoft Group',
                'location' => 'Душанбе',
                'type' => 'office',
                'salary_min' => 10000,
                'salary_max' => 20000,
                'salary_currency' => 'TJS',
                'description' => 'Позиция для начинающего Java-разработчика. Разработка микросервисов на Spring Boot, работа с PostgreSQL.',
                'company_description' => 'Компания разрабатывает Enterprise-приложения для банков и финансовых организаций.',
                'verified' => true,
                'owner_id' => null,
            ],
            [
                'title' => 'UI/UX Designer',
                'company' => 'CreativeMinds',
                'location' => 'Душанбе',
                'type' => 'remote',
                'salary_min' => 12000,
                'salary_max' => 28000,
                'salary_currency' => 'TJS',
                'description' => 'Дизайнер интерфейсов для веб и мобильных приложений. Работа в Figma, создание прототипов и дизайн-систем.',
                'company_description' => 'Дизайн-студия, создающая уникальные цифровые продукты и бренды.',
                'verified' => true,
                'owner_id' => null,
            ],
            [
                'title' => 'Full-Stack Developer',
                'company' => 'StartUp Lab',
                'location' => 'Душанбе',
                'type' => 'hybrid',
                'salary_min' => 22000,
                'salary_max' => 45000,
                'salary_currency' => 'TJS',
                'description' => 'Full-stack разработчик для стартап-проекта. React/Next.js на фронте, Node.js/NestJS на бэке, PostgreSQL.',
                'company_description' => 'Инкубатор стартапов, разрабатывающий инновационные технологические решения.',
                'verified' => false,
                'owner_id' => null,
            ],
            [
                'title' => 'Mobile Developer (Flutter)',
                'company' => 'AppWorks',
                'location' => 'Курган-Тюбе',
                'type' => 'remote',
                'salary_min' => 14000,
                'salary_max' => 32000,
                'salary_currency' => 'TJS',
                'description' => 'Разработка кроссплатформенных мобильных приложений с Flutter. Интеграция с REST API и push-уведомлениями.',
                'company_description' => 'Компания специализируется на разработке мобильных приложений для малого и среднего бизнеса.',
                'verified' => true,
                'owner_id' => null,
            ],
            [
                'title' => 'Database Administrator',
                'company' => 'DataGuard',
                'location' => 'Душанбе',
                'type' => 'office',
                'salary_min' => 16000,
                'salary_max' => 35000,
                'salary_currency' => 'TJS',
                'description' => 'Администратор баз данных MySQL и PostgreSQL. Оптимизация запросов, настройка репликации и бэкапов.',
                'company_description' => 'Компания предоставляет услуги по управлению данными и информационной безопасности.',
                'verified' => true,
                'owner_id' => null,
            ],
        ];

        foreach ($vacancies as $vacancyData) {
            Vacancy::create($vacancyData);
        }
    }

    private function seedRoadmapNodes(): void
    {
        if (RoadmapNode::count() > 0) {
            return;
        }

        $nodes = [
            ['title' => 'HTML Basics', 'topic' => 'Вёрстка', 'col' => 0, 'row' => 0, 'deps' => null, 'is_exam' => false, 'course_id' => 41],
            ['title' => 'HTML Forms', 'topic' => 'Вёрстка', 'col' => 1, 'row' => 0, 'deps' => [1], 'is_exam' => false, 'course_id' => 41],
            ['title' => 'HTML Semantics', 'topic' => 'Вёрстка', 'col' => 2, 'row' => 0, 'deps' => [1], 'is_exam' => false, 'course_id' => 41],
            ['title' => 'HTML Exam', 'topic' => 'Экзамен', 'col' => 3, 'row' => 0, 'deps' => [2, 3], 'is_exam' => true, 'course_id' => null],
            ['title' => 'CSS Fundamentals', 'topic' => 'Стили', 'col' => 0, 'row' => 1, 'deps' => [4], 'is_exam' => false, 'course_id' => 41],
            ['title' => 'CSS Box Model', 'topic' => 'Стили', 'col' => 1, 'row' => 1, 'deps' => [5], 'is_exam' => false, 'course_id' => 41],
            ['title' => 'CSS Flexbox', 'topic' => 'Стили', 'col' => 2, 'row' => 1, 'deps' => [6], 'is_exam' => false, 'course_id' => 41],
            ['title' => 'CSS Grid', 'topic' => 'Стили', 'col' => 3, 'row' => 1, 'deps' => [7], 'is_exam' => false, 'course_id' => 41],
            ['title' => 'JavaScript Basics', 'topic' => 'Программирование', 'col' => 0, 'row' => 2, 'deps' => [8], 'is_exam' => false, 'course_id' => 42],
            ['title' => 'JS Functions & Scope', 'topic' => 'Программирование', 'col' => 1, 'row' => 2, 'deps' => [9], 'is_exam' => false, 'course_id' => 42],
            ['title' => 'JS Arrays & Objects', 'topic' => 'Программирование', 'col' => 2, 'row' => 2, 'deps' => [10], 'is_exam' => false, 'course_id' => 42],
            ['title' => 'JS DOM Manipulation', 'topic' => 'Программирование', 'col' => 3, 'row' => 2, 'deps' => [11], 'is_exam' => false, 'course_id' => 42],
            ['title' => 'JS Events', 'topic' => 'Программирование', 'col' => 0, 'row' => 3, 'deps' => [12], 'is_exam' => false, 'course_id' => 42],
            ['title' => 'JS Async / Promises', 'topic' => 'Программирование', 'col' => 1, 'row' => 3, 'deps' => [13], 'is_exam' => false, 'course_id' => 42],
            ['title' => 'JS ES6+ Features', 'topic' => 'Программирование', 'col' => 2, 'row' => 3, 'deps' => [14], 'is_exam' => false, 'course_id' => 42],
            ['title' => 'JS Exam', 'topic' => 'Экзамен', 'col' => 3, 'row' => 3, 'deps' => [15, 16], 'is_exam' => true, 'course_id' => null],
            ['title' => 'React Basics', 'topic' => 'Фреймворки', 'col' => 0, 'row' => 4, 'deps' => [17], 'is_exam' => false, 'course_id' => 54],
            ['title' => 'React Hooks', 'topic' => 'Фреймворки', 'col' => 1, 'row' => 4, 'deps' => [18], 'is_exam' => false, 'course_id' => 54],
            ['title' => 'React Router & State', 'topic' => 'Фреймворки', 'col' => 2, 'row' => 4, 'deps' => [19], 'is_exam' => false, 'course_id' => 54],
            ['title' => 'Frontend Final Exam', 'topic' => 'Экзамен', 'col' => 3, 'row' => 4, 'deps' => [20], 'is_exam' => true, 'course_id' => null],
        ];

        foreach ($nodes as $node) {
            RoadmapNode::create([
                'title' => $node['title'],
                'course_id' => $node['course_id'],
                'roadmap_title' => 'Frontend Developer',
                'topic' => $node['topic'],
                'materials' => json_encode(["https://example.com/materials/" . Str::slug($node['title'])]),
                'x' => $node['col'] * 250,
                'y' => $node['row'] * 120,
                'deps' => json_encode($node['deps']),
                'is_exam' => $node['is_exam'],
            ]);
        }
    }

    private function seedCertificates(array $courses, User $user): void
    {
        $certificateNames = [
            'Сертификат по HTML+CSS',
            'Сертификат по JavaScript',
            'Сертификат по PHP',
        ];

        for ($i = 0; $i < 3; $i++) {
            if (!isset($courses[$i])) {
                continue;
            }

            $slug = Str::slug($courses[$i]->title);

            Certificate::create([
                'user_id' => $user->id,
                'course_id' => $courses[$i]->id,
                'cert_hash' => Str::random(32),
                'certificate_name' => $certificateNames[$i],
                'issuer' => 'CodeMaster Academy',
                'issue_date' => now()->subDays(30 - $i * 10),
                'certificate_url' => "/certificates/{$slug}",
            ]);
        }
    }

    private function seedNotifications(User $user): void
    {
        $notifications = [
            'Добро пожаловать на платформу CodeMaster! Начните изучение курсов уже сегодня.',
            'Новое задание доступно в курсе "JavaScript". Перейдите к уроку.',
            'Поздравляем! Вы успешно прошли тест по курсу "HTML+CSS".',
            'Новая вакансия соответствует вашему профилю: Junior Frontend Developer.',
            'Не забудьте обновить своё резюме в профиле для привлечения работодателей.',
        ];

        foreach ($notifications as $index => $message) {
            Notification::create([
                'user_id' => $user->id,
                'message' => $message,
                'notification_time' => now()->subHours(count($notifications) - $index),
                'is_read' => $index < 2,
            ]);
        }
    }

    private function seedUserActivities(User $user): void
    {
        $activities = [
            ['activity_type' => 'course', 'activity_text' => 'Начал изучение курса "HTML+CSS"'],
            ['activity_type' => 'lesson', 'activity_text' => 'Прошёл урок "Введение" в курсе "HTML+CSS"'],
            ['activity_type' => 'certificate', 'activity_text' => 'Сдал тест "Основы" в курсе "HTML+CSS"'],
            ['activity_type' => 'course', 'activity_text' => 'Начал изучение курса "JavaScript"'],
            ['activity_type' => 'lesson', 'activity_text' => 'Прошёл урок "Основы" в курсе "JavaScript"'],
            ['activity_type' => 'vacancy', 'activity_text' => 'Просмотрел вакансию "Junior Frontend Developer"'],
            ['activity_type' => 'certificate', 'activity_text' => 'Получил сертификат по курсу "HTML+CSS"'],
            ['activity_type' => 'course', 'activity_text' => 'Обновил профиль и добавил описание'],
            ['activity_type' => 'lesson', 'activity_text' => 'Прошёл урок "Практика" в курсе "JavaScript"'],
            ['activity_type' => 'application', 'activity_text' => 'Завершил узел "HTML Basics" в roadmap "Frontend Developer"'],
        ];

        foreach ($activities as $index => $activity) {
            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => $activity['activity_type'],
                'activity_text' => $activity['activity_text'],
                'activity_time' => now()->subDays(count($activities) - $index),
            ]);
        }
    }

    private function seedCommunityPosts(User $user): void
    {
        $posts = [
            [
                'title' => 'Как изучать Algorithm в 2026 году?',
                'content' => 'Привет всем! Хочу начать изучать алгоритмы и структуры данных. Какие ресурсы вы рекомендуете? LeetCode, HackerRank, или что-то другое? Какой порядок изучения тем?',
                'likes_count' => 24,
                'views_count' => 156,
            ],
            [
                'title' => 'Опыт работы с Laravel 12',
                'content' => 'Недавно обновился на Laravel 12 и хочу поделиться впечатлениями. Новые фичи非常令人印象深刻. Особенно понравилась новая система маршрутизации и улучшенная работа с очередями. Кто уже попробовал?',
                'likes_count' => 31,
                'views_count' => 203,
            ],
            [
                'title' => 'Советы по прохождению собеседования',
                'content' => 'Поделитесь советами по подготовке к техническому собеседованию. Какие темы чаще всего спрашивают? Как лучше готовиться к алгоритмическим задачам?',
                'likes_count' => 18,
                'views_count' => 127,
            ],
            [
                'title' => 'React vs Vue.js в 2026',
                'content' => 'Стоит ли переходить с Vue на React? Или Vue всё ещё актуален? Работаю с Vue уже 3 года, но всё больше вакансий требуют React. Что думаете?',
                'likes_count' => 42,
                'views_count' => 312,
            ],
            [
                'title' => 'Мой путь из новичка в разработку',
                'content' => 'Хочу рассказать свою историю. Полгода назад я не знал что такое HTML. Прошёл курсы на CodeMaster, делал практики, участвовал в контестах. Теперь работаю junior frontend developer! Спасибо платформе за отличный старт.',
                'likes_count' => 56,
                'views_count' => 421,
            ],
        ];

        foreach ($posts as $post) {
            \App\Models\CommunityPost::create([
                'user_id' => $user->id,
                'title' => $post['title'],
                'content' => $post['content'],
                'likes_count' => $post['likes_count'],
                'views_count' => $post['views_count'],
            ]);
        }
    }

    private function seedCourseExams($courses): void
    {
        foreach ($courses as $index => $course) {
            $courseId = $index + 1;
            $questions = $this->generateExamQuestions($course['title'], 30);
            \App\Models\CourseExam::create([
                'course_id' => $courseId,
                'exam_json' => json_encode([
                    'title' => $course['title'] . ' - Итоговый экзамен',
                    'questions' => $questions,
                    'time_limit_minutes' => 70,
                    'pass_percent' => 70,
                ]),
                'time_limit_minutes' => 70,
                'pass_percent' => 70,
                'shuffle_questions' => true,
                'shuffle_options' => true,
            ]);
        }
    }

    private function generateExamQuestions($courseTitle, $count): array
    {
        $templates = [
            'В каком модуле курса "' . $courseTitle . '" изучается {topic}?',
            'Какая тема относится к модулю {topic}?',
            'Для решения задачи {task} лучше всего подходит {module}?',
            'Правильно ли утверждение: {statement}?',
            'Какой из вариантов является правильным ответом на вопрос о {topic}?',
        ];

        $topics = [
            'HTML+CSS' => ['HTML структура', 'CSS Flexbox', 'Grid Layout', 'Адаптивность', 'Семантика'],
            'JavaScript' => ['Переменные', 'Функции', 'Async/Await', 'DOM', 'ES6+'],
            'PHP' => ['Основы синтаксиса', 'PDO', 'Сессии', 'OOP', 'Безопасность'],
            'Laravel' => ['Маршрутизация', 'Eloquent', 'Blade', 'Миграции', 'API Resources'],
            'MySQL' => ['SELECT', 'JOIN', 'Индексы', 'Транзакции', 'Нормализация'],
            'PostgreSQL' => ['CTE', 'Window Functions', 'JSONB', 'Индексы', 'Партиционирование'],
            'C++' => ['Указатели', 'STL', 'Шаблоны', 'Умные указатели', 'RAII'],
            'Python' => ['Списки', 'Декораторы', 'Asyncio', 'Классы', 'Типизация'],
            'Java' => ['Коллекции', 'Потоки', 'Generics', 'Lambdas', 'Многопоточность'],
            'C#' => ['LINQ', 'Async/Await', 'Делегаты', 'События', 'Nullable типы'],
            'Git' => ['Коммиты', 'Ветвление', 'Merge/Rebase', 'Конфликты', 'Remote'],
            'DevOps' => ['Docker', 'Kubernetes', 'CI/CD', 'IaC', 'Мониторинг'],
            'UI/UX Design' => ['Типографика', 'Цвет', 'Прототипирование', 'UX-исследование', 'Доступность'],
            'React' => ['Компоненты', 'Hooks', 'State', 'Context', 'Performance'],
            'Node.js' => ['Event Loop', 'Streams', 'Express', 'Микросервисы', 'Тестирование'],
            'TypeScript' => ['Типы', 'Интерфейсы', 'Generics', 'Utility Types', 'Нарrowing'],
            'Docker' => ['Images', 'Containers', 'Compose', 'Volumes', 'Networks'],
            'Kubernetes' => ['Pods', 'Services', 'Deployments', 'ConfigMaps', 'Ingress'],
            'Mobile Development' => ['Навигация', 'State', 'API', 'Push', 'Testing'],
            'English A1' => ['Present Simple', 'Past Simple', 'To Be', 'Articles', 'Prepositions'],
        ];

        $courseTopics = $topics[$courseTitle] ?? ['Базовые понятия', 'Практика', 'Продвинутые темы', 'Проекты', 'Экзамен'];
        $questions = [];

        for ($i = 0; $i < $count; $i++) {
            $topic = $courseTopics[$i % count($courseTopics)];
            $template = $templates[$i % count($templates)];
            $questionText = str_replace(['{topic}', '{task}', '{module}', '{statement}'], [$topic, $topic, $topic, $topic], $template);

            $options = [
                $topic . ' (правильный)',
                'Другой вариант 1',
                'Другой вариант 2',
                'Другой вариант 3',
            ];
            shuffle($options);
            $correctIndex = array_search($topic . ' (правильный)', $options);

            $questions[] = [
                'question' => $questionText,
                'options' => $options,
                'correct' => $correctIndex,
                'type' => 'mc_single',
            ];
        }

        return $questions;
    }
}
