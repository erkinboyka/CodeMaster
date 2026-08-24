<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'id' => 1,
                'title' => 'HTML + CSS',
                'instructor' => 'Алексей Петров',
                'description' => 'Полный курс по верстке: от основ HTML5 до продвинутого CSS3, Flexbox, Grid и адаптивного дизайна.',
                'category' => 'frontend',
                'level' => 'Начальный',
                'image_url' => '/images/courses/html-css.jpg',
            ],
            [
                'id' => 2,
                'title' => 'JavaScript',
                'instructor' => 'Мария Иванова',
                'description' => 'Изучаем JavaScript с нуля: переменные, функции, объекты, DOM, события, асинхронность, ES6+.',
                'category' => 'frontend',
                'level' => 'Начальный',
                'image_url' => '/images/courses/javascript.jpg',
            ],
            [
                'id' => 3,
                'title' => 'PHP',
                'instructor' => 'Дмитрий Козлов',
                'description' => 'Серверное программирование на PHP: синтаксис, OOP, работа с БД, файлами, сессиями и API.',
                'category' => 'backend',
                'level' => 'Начальный',
                'image_url' => '/images/courses/php.jpg',
            ],
            [
                'id' => 4,
                'title' => 'Laravel',
                'instructor' => 'Дмитрий Козлов',
                'description' => 'Фреймворк Laravel: роутинг, контроллеры, модели, миграции, авторизация, API, очереди и тестирование.',
                'category' => 'backend',
                'level' => 'Средний',
                'image_url' => '/images/courses/laravel.jpg',
            ],
            [
                'id' => 5,
                'title' => 'MySQL',
                'instructor' => 'Елена Сидорова',
                'description' => 'Реляционные базы данных: SQL, нормализация, индексы, хранимые процедуры, оптимизация запросов.',
                'category' => 'backend',
                'level' => 'Начальный',
                'image_url' => '/images/courses/mysql.jpg',
            ],
            [
                'id' => 6,
                'title' => 'PostgreSQL',
                'instructor' => 'Елена Сидорова',
                'description' => 'Продвинутая работа с PostgreSQL: расширенные типы данных, CTE, оконные функции, JSON, оптимизация.',
                'category' => 'backend',
                'level' => 'Продвинутый',
                'image_url' => '/images/courses/postgresql.jpg',
            ],
            [
                'id' => 7,
                'title' => 'C++',
                'instructor' => 'Сергей Волков',
                'description' => 'Программирование на C++: типы данных, управление памятью, OOP, шаблоны, STL, работа с файлами.',
                'category' => 'backend',
                'level' => 'Средний',
                'image_url' => '/images/courses/cpp.jpg',
            ],
            [
                'id' => 8,
                'title' => 'Python',
                'instructor' => 'Анна Новикова',
                'description' => 'Python с нуля: синтаксис, OOP, модули, работа с файлами, веб-парсинг, основы веб-разработки.',
                'category' => 'backend',
                'level' => 'Начальный',
                'image_url' => '/images/courses/python.jpg',
            ],
            [
                'id' => 9,
                'title' => 'Java',
                'instructor' => 'Сергей Волков',
                'description' => 'Java: типы данных, OOP, коллекции, исключения, многопоточность, работа с JDBC, Spring основы.',
                'category' => 'backend',
                'level' => 'Средний',
                'image_url' => '/images/courses/java.jpg',
            ],
            [
                'id' => 10,
                'title' => 'C#',
                'instructor' => 'Сергей Волков',
                'description' => 'C# и .NET: синтаксис, OOP, LINQ, Entity Framework, ASP.NET Core основы.',
                'category' => 'backend',
                'level' => 'Средний',
                'image_url' => '/images/courses/csharp.jpg',
            ],
            [
                'id' => 11,
                'title' => 'Git',
                'instructor' => 'Алексей Петров',
                'description' => 'Система контроля версий Git: коммиты, ветки, слияния, ребейзинг, GitHub/GitLab, командная работа.',
                'category' => 'devops',
                'level' => 'Начальный',
                'image_url' => '/images/courses/git.jpg',
            ],
            [
                'id' => 12,
                'title' => 'DevOps',
                'instructor' => 'Ольга Морозова',
                'description' => 'DevOps практики: CI/CD, автоматизация, мониторинг, инфраструктура как код, Site Reliability Engineering.',
                'category' => 'devops',
                'level' => 'Продвинутый',
                'image_url' => '/images/courses/devops.jpg',
            ],
            [
                'id' => 13,
                'title' => 'UI/UX Design',
                'instructor' => 'Ирина Кузнецова',
                'description' => 'Дизайн интерфейсов: исследование пользователей, прототипирование, Figma, дизайн-системы, доступность.',
                'category' => 'design',
                'level' => 'Начальный',
                'image_url' => '/images/courses/uiux.jpg',
            ],
            [
                'id' => 14,
                'title' => 'React',
                'instructor' => 'Мария Иванова',
                'description' => 'Библиотека React: компоненты, JSX, хуки, роутинг, управление состоянием, оптимизация, тестирование.',
                'category' => 'frontend',
                'level' => 'Средний',
                'image_url' => '/images/courses/react.jpg',
            ],
            [
                'id' => 15,
                'title' => 'Node.js',
                'instructor' => 'Анна Новикова',
                'description' => 'Серверный JavaScript: Node.js, Express, REST API, аутентификация, работа с БД, WebSocket.',
                'category' => 'backend',
                'level' => 'Средний',
                'image_url' => '/images/courses/nodejs.jpg',
            ],
            [
                'id' => 16,
                'title' => 'TypeScript',
                'instructor' => 'Мария Иванова',
                'description' => 'Типизация JavaScript: типы, интерфейсы, дженерики, декораторы, конфигурация, интеграция с React/Node.',
                'category' => 'frontend',
                'level' => 'Средний',
                'image_url' => '/images/courses/typescript.jpg',
            ],
            [
                'id' => 17,
                'title' => 'Docker',
                'instructor' => 'Ольга Морозова',
                'description' => 'Контейнеризация: Dockerfile, Docker Compose, сети, тома, оптимизация образов,.registry, безопасность.',
                'category' => 'devops',
                'level' => 'Средний',
                'image_url' => '/images/courses/docker.jpg',
            ],
            [
                'id' => 18,
                'title' => 'Kubernetes',
                'instructor' => 'Ольга Морозова',
                'description' => 'Оркестрация контейнеров: поды, деплойменты, сервисы, конфигурации, мониторинг, безопасность в K8s.',
                'category' => 'devops',
                'level' => 'Продвинутый',
                'image_url' => '/images/courses/kubernetes.jpg',
            ],
            [
                'id' => 19,
                'title' => 'Mobile Development',
                'instructor' => 'Алексей Петров',
                'description' => 'Мобильная разработка: React Native / Flutter, навигация, UI-компоненты, работа с API, публикация в stores.',
                'category' => 'frontend',
                'level' => 'Средний',
                'image_url' => '/images/courses/mobile.jpg',
            ],
            [
                'id' => 20,
                'title' => 'English A1',
                'instructor' => 'Ирина Кузнецова',
                'description' => 'Английский язык для разработчиков: базовая грамматика, чтение документации, IT-терминология.',
                'category' => 'other',
                'level' => 'Начальный',
                'image_url' => '/images/courses/english.jpg',
            ],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(
                ['id' => $course['id']],
                array_merge($course, [
                    'materials_title' => 'Материалы курса',
                    'materials_url' => '/materials/' . $course['id'],
                ])
            );
        }
    }
}
