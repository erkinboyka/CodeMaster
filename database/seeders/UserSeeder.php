<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@codemaster.com')->first();

        $demoUsers = [
            [
                'name' => 'Алишер Холиков',
                'email' => 'alisher@example.com',
                'password' => bcrypt('password'),
                'role' => 'seeker',
                'is_verified' => true,
                'ai_coins' => 250,
                'title' => 'Full-Stack Developer',
                'bio' => 'Опытный разработчик с 4+ лет опыта. Специализируюсь на Laravel, React и PostgreSQL. Люблю чистый код и архитектурные паттерны.',
                'location' => 'Душанбе, Таджикистан',
                'country_code' => 'TJ',
                'country_name' => 'Таджикистан',
                'github' => 'https://github.com/alisher-dev',
                'linkedin' => 'https://linkedin.com/in/alisher-dev',
                'website' => 'https://alisherdev.tj',
                'xp' => 15300,
                'ai_tokens' => 500,
                'level' => 18,
                'total_xp' => 15300,
                'streak_count' => 87,
                'longest_streak' => 120,
                'last_active_date' => now()->toDateString(),
            ],
            [
                'name' => 'Фируза Рустамова',
                'email' => 'firuza@example.com',
                'password' => bcrypt('password'),
                'role' => 'seeker',
                'is_verified' => true,
                'ai_coins' => 180,
                'title' => 'Frontend Developer (React)',
                'bio' => 'Фронтенд-разработчик, увлечённая UI/UX дизайном. Работаю с React, TypeScript и Tailwind CSS. Стремлюсь создавать доступные и красивые интерфейсы.',
                'location' => 'Худжанд, Таджикистан',
                'country_code' => 'TJ',
                'country_name' => 'Таджикистан',
                'github' => 'https://github.com/firuza-dev',
                'linkedin' => 'https://linkedin.com/in/firuza-dev',
                'xp' => 6600,
                'ai_tokens' => 300,
                'level' => 12,
                'total_xp' => 6600,
                'streak_count' => 34,
                'longest_streak' => 56,
                'last_active_date' => now()->toDateString(),
            ],
            [
                'name' => 'Бахром Мирзоев',
                'email' => 'bahrom@example.com',
                'password' => bcrypt('password'),
                'role' => 'seeker',
                'is_verified' => true,
                'ai_coins' => 120,
                'title' => 'Backend Developer (Python)',
                'bio' => 'Бэкенд-разработчик на Python/Django. Интересуюсь машинным обучением и анализом данных. 2 года коммерческого опыта.',
                'location' => 'Бохтар, Таджикистан',
                'country_code' => 'TJ',
                'country_name' => 'Таджикистан',
                'github' => 'https://github.com/bahrom-dev',
                'xp' => 10500,
                'ai_tokens' => 200,
                'level' => 15,
                'total_xp' => 10500,
                'streak_count' => 21,
                'longest_streak' => 45,
                'last_active_date' => now()->subDays(2)->toDateString(),
            ],
            [
                'name' => 'Марина Соколова',
                'email' => 'marina@example.com',
                'password' => bcrypt('password'),
                'role' => 'seeker',
                'is_verified' => true,
                'ai_coins' => 90,
                'title' => 'UI/UX Designer',
                'bio' => 'Дизайнер интерфейсов с опытом работы в Figma и Adobe XD. Создаю дизайн-системы и прототипы. Верстаю на HTML/CSS.',
                'location' => 'Москва, Россия',
                'country_code' => 'RU',
                'country_name' => 'Россия',
                'github' => 'https://github.com/marina-design',
                'linkedin' => 'https://linkedin.com/in/marina-design',
                'xp' => 2800,
                'ai_tokens' => 150,
                'level' => 8,
                'total_xp' => 2800,
                'streak_count' => 14,
                'longest_streak' => 30,
                'last_active_date' => now()->toDateString(),
            ],
            [
                'name' => 'Тимур Абдуллаев',
                'email' => 'timur@example.com',
                'password' => bcrypt('password'),
                'role' => 'seeker',
                'is_verified' => true,
                'ai_coins' => 350,
                'title' => 'DevOps Engineer',
                'bio' => 'DevOps-инженер с 5+ лет опыта. Docker, Kubernetes, CI/CD, AWS. Автоматизирую всё, что можно автоматизировать.',
                'location' => 'Дубай, ОАЭ',
                'country_code' => 'AE',
                'country_name' => 'ОАЭ',
                'github' => 'https://github.com/timur-devops',
                'linkedin' => 'https://linkedin.com/in/timur-devops',
                'website' => 'https://timurdevops.com',
                'xp' => 23100,
                'ai_tokens' => 800,
                'level' => 22,
                'total_xp' => 23100,
                'streak_count' => 156,
                'longest_streak' => 200,
                'last_active_date' => now()->toDateString(),
            ],
            [
                'name' => 'Сара Назарова',
                'email' => 'sara@example.com',
                'password' => bcrypt('password'),
                'role' => 'seeker',
                'is_verified' => false,
                'ai_coins' => 50,
                'title' => 'Junior Java Developer',
                'bio' => 'Начинающий Java-разработчик. Изучаю Spring Boot и микросервисную архитектуру. Ищу первую стажировку.',
                'location' => 'Казань, Россия',
                'country_code' => 'RU',
                'country_name' => 'Россия',
                'xp' => 1000,
                'ai_tokens' => 100,
                'level' => 5,
                'total_xp' => 1000,
                'streak_count' => 7,
                'longest_streak' => 14,
                'last_active_date' => now()->subDays(5)->toDateString(),
            ],
        ];

        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Тестовый Пользователь',
                'password' => bcrypt('password'),
                'role' => 'seeker',
                'is_verified' => true,
                'ai_coins' => 100,
                'title' => 'Junior Frontend Developer',
                'bio' => 'Начинающий разработчик, изучаю фронтенд-технологии. Прошёл курсы по HTML, CSS и JavaScript на CodeMaster.',
                'location' => 'Душанбе, Таджикистан',
                'country_code' => 'TJ',
                'country_name' => 'Таджикистан',
                'github' => 'https://github.com/testuser',
                'xp' => 4500,
                'ai_tokens' => 250,
                'level' => 10,
                'total_xp' => 4500,
                'streak_count' => 42,
                'longest_streak' => 67,
                'last_active_date' => now()->toDateString(),
            ]
        );

        foreach ($demoUsers as $userData) {
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }

        $allUsers = User::all();

        $this->seedSkills($allUsers);
        $this->seedEducation($allUsers);
        $this->seedExperience($allUsers);
        $this->seedPortfolio($allUsers);
        $this->seedVacancies();
        $this->seedVacancyRelations();
        $this->seedApplications($allUsers);
        $this->seedCertificates($allUsers);
        $this->seedNotifications($allUsers);
        $this->seedActivities($allUsers);
    }

    private function seedSkills($users): void
    {
        $levelMap = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3, 'master' => 4];

        $skillSets = [
            0 => [
                ['skill_name' => 'PHP', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 12, 'is_verified' => true],
                ['skill_name' => 'Laravel', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 15, 'is_verified' => true],
                ['skill_name' => 'MySQL', 'skill_level' => 2, 'category' => 'technical', 'endorsements' => 8, 'is_verified' => true],
                ['skill_name' => 'React', 'skill_level' => 2, 'category' => 'technical', 'endorsements' => 6, 'is_verified' => false],
                ['skill_name' => 'JavaScript', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 10, 'is_verified' => true],
                ['skill_name' => 'Git', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 7, 'is_verified' => false],
                ['skill_name' => 'Docker', 'skill_level' => 2, 'category' => 'technical', 'endorsements' => 4, 'is_verified' => false],
                ['skill_name' => 'HTML/CSS', 'skill_level' => 4, 'category' => 'technical', 'endorsements' => 20, 'is_verified' => true],
            ],
            1 => [
                ['skill_name' => 'React', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 14, 'is_verified' => true],
                ['skill_name' => 'TypeScript', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 10, 'is_verified' => true],
                ['skill_name' => 'JavaScript', 'skill_level' => 4, 'category' => 'technical', 'endorsements' => 18, 'is_verified' => true],
                ['skill_name' => 'HTML/CSS', 'skill_level' => 4, 'category' => 'technical', 'endorsements' => 22, 'is_verified' => true],
                ['skill_name' => 'Figma', 'skill_level' => 2, 'category' => 'soft', 'endorsements' => 5, 'is_verified' => false],
                ['skill_name' => 'Tailwind CSS', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 8, 'is_verified' => true],
            ],
            2 => [
                ['skill_name' => 'Python', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 11, 'is_verified' => true],
                ['skill_name' => 'Django', 'skill_level' => 2, 'category' => 'technical', 'endorsements' => 7, 'is_verified' => true],
                ['skill_name' => 'PostgreSQL', 'skill_level' => 2, 'category' => 'technical', 'endorsements' => 5, 'is_verified' => false],
                ['skill_name' => 'Machine Learning', 'skill_level' => 1, 'category' => 'technical', 'endorsements' => 3, 'is_verified' => false],
                ['skill_name' => 'Git', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 6, 'is_verified' => false],
            ],
            3 => [
                ['skill_name' => 'Figma', 'skill_level' => 4, 'category' => 'soft', 'endorsements' => 20, 'is_verified' => true],
                ['skill_name' => 'Adobe XD', 'skill_level' => 3, 'category' => 'soft', 'endorsements' => 12, 'is_verified' => true],
                ['skill_name' => 'HTML/CSS', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 8, 'is_verified' => false],
                ['skill_name' => 'UI Design', 'skill_level' => 4, 'category' => 'soft', 'endorsements' => 16, 'is_verified' => true],
                ['skill_name' => 'Prototyping', 'skill_level' => 3, 'category' => 'soft', 'endorsements' => 9, 'is_verified' => false],
            ],
            4 => [
                ['skill_name' => 'Docker', 'skill_level' => 4, 'category' => 'technical', 'endorsements' => 25, 'is_verified' => true],
                ['skill_name' => 'Kubernetes', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 18, 'is_verified' => true],
                ['skill_name' => 'AWS', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 14, 'is_verified' => true],
                ['skill_name' => 'CI/CD', 'skill_level' => 4, 'category' => 'technical', 'endorsements' => 20, 'is_verified' => true],
                ['skill_name' => 'Linux', 'skill_level' => 4, 'category' => 'technical', 'endorsements' => 22, 'is_verified' => true],
                ['skill_name' => 'Python', 'skill_level' => 2, 'category' => 'technical', 'endorsements' => 6, 'is_verified' => false],
                ['skill_name' => 'Terraform', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 10, 'is_verified' => true],
            ],
            5 => [
                ['skill_name' => 'Java', 'skill_level' => 2, 'category' => 'technical', 'endorsements' => 4, 'is_verified' => false],
                ['skill_name' => 'Spring Boot', 'skill_level' => 1, 'category' => 'technical', 'endorsements' => 2, 'is_verified' => false],
                ['skill_name' => 'SQL', 'skill_level' => 2, 'category' => 'technical', 'endorsements' => 3, 'is_verified' => false],
            ],
            6 => [
                ['skill_name' => 'HTML/CSS', 'skill_level' => 3, 'category' => 'technical', 'endorsements' => 8, 'is_verified' => true],
                ['skill_name' => 'JavaScript', 'skill_level' => 2, 'category' => 'technical', 'endorsements' => 5, 'is_verified' => false],
                ['skill_name' => 'React', 'skill_level' => 1, 'category' => 'technical', 'endorsements' => 2, 'is_verified' => false],
            ],
        ];

        foreach ($users as $i => $user) {
            $key = min($i, 6);
            $skills = $skillSets[$key] ?? $skillSets[0];
            foreach ($skills as $skill) {
                \App\Models\UserSkill::create(array_merge($skill, ['user_id' => $user->id]));
            }
        }
    }

    private function seedEducation($users): void
    {
        $eduData = [
            0 => ['degree' => 'Бакалавр', 'institution' => 'Таджикский Национальный Университет', 'field' => 'Информатика и вычислительная техника', 'start_date' => '2018-09-01', 'end_date' => '2022-06-30', 'description' => 'Факультет математики и информационных технологий. Красный диплом.'],
            1 => ['degree' => 'Бакалавр', 'institution' => 'Худжандский Государственный Университет', 'field' => 'Программная инженерия', 'start_date' => '2019-09-01', 'end_date' => '2023-06-30', 'description' => 'Факультет информационных технологий. Специализация — веб-разработка.'],
            2 => ['degree' => 'Бакалавр', 'institution' => 'Бохтарский Государственный Университет', 'field' => 'Прикладная математика', 'start_date' => '2020-09-01', 'end_date' => '2024-06-30', 'description' => 'Факультет математики и информационных технологий.'],
            3 => ['degree' => 'Бакалавр', 'institution' => 'Московский Политехнический Университет', 'field' => 'Дизайн', 'start_date' => '2017-09-01', 'end_date' => '2021-06-30', 'description' => 'Институт графического дизайна и графики. Специализация — цифровой дизайн.'],
            4 => ['degree' => 'Магистр', 'institution' => 'Дубайский Университет Науки и Технологий', 'field' => 'Информационные технологии', 'start_date' => '2016-09-01', 'end_date' => '2018-06-30', 'description' => 'Факультет компьютерных наук. Специализация — облачные вычисления.'],
            5 => ['degree' => 'Бакалавр', 'institution' => 'Казанский Федеральный Университет', 'field' => 'Информатика', 'start_date' => '2022-09-01', 'end_date' => null, 'description' => 'Факультет математики и информационных технологий. Сейчас на 3 курсе.'],
        ];

        foreach ($users as $i => $user) {
            $key = min($i, 5);
            $edu = $eduData[$key] ?? $eduData[0];
            \App\Models\UserEducation::create(array_merge($edu, ['user_id' => $user->id]));
        }
    }

    private function seedExperience($users): void
    {
        $expData = [
            0 => [
                ['position' => 'Full-Stack Developer', 'company' => 'TechHub Dushanbe', 'start_date' => '2023-03-01', 'end_date' => null, 'is_current' => true, 'description' => 'Разработка и поддержка веб-приложений на Laravel + React. Проектирование API, оптимизация производительности, код-ревью.'],
                ['position' => 'Junior PHP Developer', 'company' => 'WebPro Solutions', 'start_date' => '2022-01-15', 'end_date' => '2023-02-28', 'is_current' => false, 'description' => 'Разработка серверной части на PHP/Laravel. Работа с MySQL, REST API, интеграция с внешними сервисами.'],
            ],
            1 => [
                ['position' => 'Frontend Developer', 'company' => 'Digital Studio', 'start_date' => '2023-06-01', 'end_date' => null, 'is_current' => true, 'description' => 'Разработка SPA на React/TypeScript. Создание дизайн-систем, компонентная архитектура, доступность (a11y).'],
            ],
            2 => [
                ['position' => 'Python Developer', 'company' => 'DataFlow Systems', 'start_date' => '2024-01-10', 'end_date' => null, 'is_current' => true, 'description' => 'Разработка REST API на Django/FastAPI. Обработка данных, ETL-пайплайны, работа с PostgreSQL.'],
            ],
            3 => [
                ['position' => 'UI/UX Designer', 'company' => 'CreativeMinds Agency', 'start_date' => '2022-04-01', 'end_date' => null, 'is_current' => true, 'description' => 'Проектирование интерфейсов для мобильных и веб-приложений. Проведение исследований, создание прототипов.'],
            ],
            4 => [
                ['position' => 'Senior DevOps Engineer', 'company' => 'CloudTech International', 'start_date' => '2021-08-01', 'end_date' => null, 'is_current' => true, 'description' => 'Управление инфраструктурой AWS/GCP. Настройка CI/CD, мониторинг, автоматизация деплоя. Руководство командой из 3 инженеров.'],
                ['position' => 'DevOps Engineer', 'company' => 'InnoSoft Group', 'start_date' => '2019-03-01', 'end_date' => '2021-07-31', 'is_current' => false, 'description' => 'Контейнеризация приложений, настройка Docker/Kubernetes, автоматизация инфраструктуры.'],
            ],
            5 => [],
        ];

        foreach ($users as $i => $user) {
            $key = min($i, 5);
            $exps = $expData[$key] ?? [];
            foreach ($exps as $exp) {
                \App\Models\UserExperience::create(array_merge($exp, ['user_id' => $user->id]));
            }
        }
    }

    private function seedPortfolio($users): void
    {
        $portData = [
            0 => [
                ['title' => 'E-Commerce Platform', 'description' => 'Полнофункциональная платформа электронной коммерции на Laravel + React. Корзина, оплата, панель администратора.', 'url' => 'https://github.com/alisher-dev/ecommerce', 'category' => 'web', 'github_url' => 'https://github.com/alisher-dev/ecommerce'],
                ['title' => 'Real-Time Chat App', 'description' => 'Чат-приложение в реальном времени с WebSocket, авторизацией, файловым обменом.', 'url' => 'https://github.com/alisher-dev/chat-app', 'category' => 'web', 'github_url' => 'https://github.com/alisher-dev/chat-app'],
            ],
            1 => [
                ['title' => 'Portfolio Website', 'description' => 'Анимированный сайт-портфолио с Intersection Observer и плавными переходами.', 'url' => 'https://firuza-dev.github.io/portfolio', 'category' => 'web', 'github_url' => 'https://github.com/firuza-dev/portfolio'],
                ['title' => 'Task Manager UI Kit', 'description' => 'UI-Kit для приложения управления задачами. 30+ компонентов в Figma + React код.', 'url' => 'https://www.figma.com/community/file/task-manager', 'category' => 'soft'],
            ],
            2 => [
                ['title' => 'Data Pipeline', 'description' => 'ETL-пайплайн для обработки данных на Python. Apache Airflow, PostgreSQL, Pandas.', 'url' => 'https://github.com/bahrom-dev/data-pipeline', 'category' => 'technical', 'github_url' => 'https://github.com/bahrom-dev/data-pipeline'],
            ],
            3 => [
                ['title' => 'Finance App Design', 'description' => 'Дизайн-система и макеты для мобильного банкинг-приложения. 50+ экранов.', 'url' => 'https://www.figma.com/community/file/finance-app', 'category' => 'soft'],
                ['title' => 'Design System', 'description' => 'Универсальная дизайн-система с компонентами, токенами и документацией.', 'url' => 'https://www.figma.com/community/file/design-system', 'category' => 'soft'],
            ],
            4 => [
                ['title' => 'K8s Cluster Manager', 'description' => 'Инструмент автоматического управления Kubernetes кластерами. Go + Terraform.', 'url' => 'https://github.com/timur-devops/k8s-manager', 'category' => 'technical', 'github_url' => 'https://github.com/timur-devops/k8s-manager'],
                ['title' => 'CI/CD Pipeline Templates', 'description' => 'Набор шаблонов GitHub Actions для автоматизации деплоя.', 'url' => 'https://github.com/timur-devops/ci-templates', 'category' => 'technical', 'github_url' => 'https://github.com/timur-devops/ci-templates'],
            ],
            5 => [],
        ];

        foreach ($users as $i => $user) {
            $key = min($i, 5);
            $ports = $portData[$key] ?? [];
            foreach ($ports as $port) {
                \App\Models\UserPortfolio::create(array_merge($port, ['user_id' => $user->id]));
            }
        }
    }

    private function seedVacancies(): void
    {
        if (\App\Models\Vacancy::count() > 0) return;

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
            ],
        ];

        foreach ($vacancies as $v) {
            \App\Models\Vacancy::create($v);
        }
    }

    private function seedVacancyRelations(): void
    {
        $vacancies = \App\Models\Vacancy::all();
        if ($vacancies->isEmpty()) return;

        $skillsMap = [
            0 => ['HTML', 'CSS', 'JavaScript', 'React', 'Git'],
            1 => ['PHP', 'Laravel', 'MySQL', 'REST API', 'Git'],
            2 => ['Python', 'Django', 'PostgreSQL', 'REST API', 'Docker'],
            3 => ['React', 'TypeScript', 'Redux', 'CSS', 'REST API'],
            4 => ['Docker', 'Kubernetes', 'CI/CD', 'Linux', 'AWS', 'Terraform'],
            5 => ['Java', 'Spring Boot', 'PostgreSQL', 'REST API', 'Maven'],
            6 => ['Figma', 'Adobe XD', 'Prototyping', 'UI Design', 'HTML', 'CSS'],
            7 => ['React', 'Node.js', 'TypeScript', 'PostgreSQL', 'Docker', 'REST API'],
            8 => ['Flutter', 'Dart', 'REST API', 'Firebase', 'Git'],
            9 => ['MySQL', 'PostgreSQL', 'Bash', 'Docker', 'Monitoring'],
        ];

        $requirementsMap = [
            0 => ['Знание HTML/CSS/JavaScript', 'Опыт работы с React от 6 месяцев', 'Умение работать с Git', 'Базовое понимание REST API'],
            1 => ['Опыт работы с PHP от 2 лет', 'Знание Laravel', 'Умение работать с MySQL', 'Понимание OOP и паттернов проектирования'],
            2 => ['Опыт работы с Python от 1 года', 'Знание Django или FastAPI', 'Умение работать с PostgreSQL', 'Понимание REST API'],
            3 => ['Опыт работы с React от 2 лет', 'Знание TypeScript', 'Опыт с Redux/Zustand', 'Понимание принципов UI/UX'],
            4 => ['Опыт работы с Docker от 2 лет', 'Знание Kubernetes', 'Навыки настройки CI/CD', 'Знание Linux на продвинутом уровне'],
            5 => ['Знание Java основы', 'Понимание OOP', 'Желание учиться', 'Базовое знание SQL'],
            6 => ['Опыт работы с Figma от 1 года', 'Портфолио с работами', 'Понимание принципов UI/UX', 'Базовое знание HTML/CSS'],
            7 => ['Опыт работы с React и Node.js от 2 лет', 'Знание TypeScript', 'Опыт с PostgreSQL', 'Понимание микросервисной архитектуры'],
            8 => ['Опыт работы с Flutter от 1 года', 'Знание Dart', 'Опыт с REST API', 'Понимание навигации в мобильных приложениях'],
            9 => ['Опыт работы с MySQL/PostgreSQL от 2 лет', 'Знание SQL на продвинутом уровне', 'Навыки оптимизации запросов', 'Понимание репликации'],
        ];

        $responsibilitiesMap = [
            0 => ['Разработка пользовательских интерфейсов', 'Вёрстка по макетам', 'Оптимизация производительности', 'Участие в код-ревью'],
            1 => ['Разработка серверной части на PHP/Laravel', 'Проектирование баз данных', 'Написание тестов', 'Документация API'],
            2 => ['Разработка REST API на Python', 'Проектирование архитектуры', 'Оптимизация запросов', 'Работа с данными'],
            3 => ['Разработка компонентов на React', 'Создание дизайн-систем', 'Оптимизация рендеринга', 'Код-ревью'],
            4 => ['Настройка CI/CD пайплайнов', 'Управление инфраструктурой', 'Мониторинг сервисов', 'Автоматизация процессов'],
            5 => ['Разработка микросервисов', 'Написание тестов', 'Участие в код-ревью', 'Изучение новых технологий'],
            6 => ['Проектирование интерфейсов', 'Создание прототипов', 'Проведение исследований', 'Документирование设计系统'],
            7 => ['Full-stack разработка', 'Проектирование архитектуры', 'Менторство', 'Оптимизация производительности'],
            8 => ['Разработка мобильных приложений', 'Интеграция с API', 'Тестирование', 'Публикация в маркетах'],
            9 => ['Управление базами данных', 'Оптимизация производительности', 'Настройка бэкапов', 'Мониторинг'],
        ];

        $plusesMap = [
            0 => ['Опыт с TypeScript', 'Знание Tailwind CSS', 'Опыт работы в команде'],
            1 => ['Опыт с Redis', 'Знание Docker', 'Опыт с очередями (RabbitMQ, Kafka)'],
            2 => ['Опыт с Machine Learning', 'Знание Docker', 'Опыт с Apache Airflow'],
            3 => ['Опыт с Figma', 'Знание анимаций (Framer Motion)', 'Портфолио'],
            4 => ['Сертификация AWS/GCP', 'Опыт с Prometheus/Grafana', 'Знание Python'],
            5 => ['Опыт с Docker', 'Знание Git', 'Прохождение курсов на CodeMaster'],
            6 => ['Опыт с анимациями', 'Знание Framer Motion', 'Опыт с дизайн-системами'],
            7 => ['Опыт с GraphQL', 'Знание Kubernetes', 'Опыт со стартапами'],
            8 => ['Опыт с Firebase', 'Знание нативной разработки (Kotlin/Swift)', 'Опыт с push-уведомлениями'],
            9 => ['Опыт с MongoDB', 'Знание Kubernetes', 'Сертификация Oracle/PostgreSQL'],
        ];

        foreach ($vacancies as $i => $vacancy) {
            $key = $i % 10;

            foreach ($skillsMap[$key] as $skill) {
                \App\Models\VacancySkill::create(['vacancy_id' => $vacancy->id, 'skill_name' => $skill]);
            }
            foreach ($requirementsMap[$key] as $req) {
                \App\Models\VacancyRequirement::create(['vacancy_id' => $vacancy->id, 'requirement_text' => $req]);
            }
            foreach ($responsibilitiesMap[$key] as $resp) {
                \App\Models\VacancyResponsibility::create(['vacancy_id' => $vacancy->id, 'responsibility_text' => $resp]);
            }
            foreach ($plusesMap[$key] as $plus) {
                \App\Models\VacancyPlus::create(['vacancy_id' => $vacancy->id, 'plus_text' => $plus]);
            }
        }
    }

    private function seedApplications($users): void
    {
        $vacancies = \App\Models\Vacancy::all();
        if ($vacancies->isEmpty()) return;

        $statuses = ['pending', 'reviewing', 'accepted', 'rejected'];
        $empStatuses = ['actively_looking', 'open_to_offers'];

        $applications = [
            ['user_index' => 0, 'vacancy_index' => 1, 'status' => 'offer', 'emp' => 'successful'],
            ['user_index' => 1, 'vacancy_index' => 3, 'status' => 'interview', 'emp' => 'pending'],
            ['user_index' => 2, 'vacancy_index' => 2, 'status' => 'applied', 'emp' => 'pending'],
            ['user_index' => 4, 'vacancy_index' => 4, 'status' => 'rejected', 'emp' => 'unsuccessful'],
            ['user_index' => 6, 'vacancy_index' => 0, 'status' => 'applied', 'emp' => 'pending'],
            ['user_index' => 6, 'vacancy_index' => 6, 'status' => 'interview', 'emp' => 'pending'],
        ];

        foreach ($applications as $app) {
            $userId = $users[$app['user_index']]?->id;
            $vacancyId = $vacancies[$app['vacancy_index']]?->id;
            if (!$userId || !$vacancyId) continue;

            \App\Models\UserApplication::create([
                'user_id' => $userId,
                'vacancy_id' => $vacancyId,
                'status' => $app['status'],
                'employment_status' => $app['emp'],
                'applied_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }

    private function seedCertificates($users): void
    {
        $courses = \App\Models\Course::all();
        if ($courses->isEmpty()) return;

        $certNames = [
            'HTML+CSS: Основы вёрстки',
            'JavaScript: Полный курс',
            'PHP: Разработка веб-приложений',
            'React: Современные интерфейсы',
            'Python: Основы программирования',
            'Docker: Контейнеризация',
        ];

        $certPairs = [
            [0, 0], [0, 1], [1, 3], [1, 13], [2, 7], [4, 16], [6, 0], [6, 1],
        ];

        foreach ($certPairs as $pair) {
            $user = $users[$pair[0]] ?? null;
            $course = $courses[$pair[1]] ?? null;
            if (!$user || !$course) continue;

            $slug = \Illuminate\Support\Str::slug($course->title);
            \App\Models\Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'cert_hash' => \Illuminate\Support\Str::random(32),
                'certificate_name' => $certNames[$pair[1]] ?? $course->title . ' Certificate',
                'issuer' => 'CodeMaster Academy',
                'issue_date' => now()->subDays(rand(10, 120)),
                'certificate_url' => "/certificates/{$slug}",
            ]);
        }
    }

    private function seedNotifications($users): void
    {
        $messages = [
            'Добро пожаловать на платформу CodeMaster! Начните изучение курсов уже сегодня.',
            'Новое задание доступно в курсе "JavaScript". Перейдите к уроку.',
            'Поздравляем! Вы успешно прошли тест по курсу "HTML+CSS".',
            'Новая вакансия соответствует вашему профилю: Junior Frontend Developer.',
            'Не забудьте обновить своё резюме в профиле для привлечения работодателей.',
            'Рейтинг обновлён! Вы заняли место в топ-10 этой недели.',
            'Новый комментарий к вашему посту в сообществе.',
            'Приглашение на Peer Interview от коллеги.',
            'Новый курс "Kubernetes" уже доступен. Начните обучение!',
            'Ваш сертификат по курсу "React" готов к скачиванию.',
        ];

        foreach ($users as $user) {
            $count = rand(3, 7);
            for ($i = 0; $i < $count; $i++) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'message' => $messages[array_rand($messages)],
                    'notification_time' => now()->subHours(rand(1, 168)),
                    'is_read' => rand(0, 1),
                ]);
            }
        }
    }

    private function seedActivities($users): void
    {
        $activityTemplates = [
            ['activity_type' => 'course', 'activity_text' => 'Начал изучение курса "HTML+CSS"'],
            ['activity_type' => 'lesson', 'activity_text' => 'Прошёл урок "Введение" в курсе "JavaScript"'],
            ['activity_type' => 'certificate', 'activity_text' => 'Получил сертификат по курсу "HTML+CSS"'],
            ['activity_type' => 'vacancy', 'activity_text' => 'Просмотрел вакансию "Junior Frontend Developer"'],
            ['activity_type' => 'course', 'activity_text' => 'Начал изучение курса "React"'],
            ['activity_type' => 'lesson', 'activity_text' => 'Прошёл урок "Основы PHP" в курсе "PHP"'],
            ['activity_type' => 'application', 'activity_text' => 'Откликнулся на вакансию "PHP Developer"'],
            ['activity_type' => 'course', 'activity_text' => 'Завершил курс "JavaScript" с оценкой 95%'],
            ['activity_type' => 'certificate', 'activity_text' => 'Получил сертификат по курсу "React"'],
            ['activity_type' => 'application', 'activity_text' => 'Завершил узел "HTML Basics" в roadmap "Frontend Developer"'],
        ];

        foreach ($users as $user) {
            $count = rand(5, 8);
            $activities = collect($activityTemplates)->random($count);
            foreach ($activities as $i => $activity) {
                \App\Models\UserActivity::create([
                    'user_id' => $user->id,
                    'activity_type' => $activity['activity_type'],
                    'activity_text' => $activity['activity_text'],
                    'activity_time' => now()->subDays($count - $i),
                ]);
            }
        }
    }
}
