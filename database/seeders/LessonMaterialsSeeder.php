<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;

class LessonMaterialsSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            'Структура HTML-документа' => ['materials_title' => 'MDN: HTML-документ', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Learn/HTML/Introduction_to_HTML/Getting_started', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_html_structure/embed'],
            'Семантические теги HTML5' => ['materials_title' => 'MDN: Семантические теги', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Glossary/Semantics', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_html_semantics/embed'],
            'Таблицы, списки и мультимедиа' => ['materials_title' => 'MDN: Таблицы HTML', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Learn/HTML/Tables/Basics', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_html_tables/embed'],
            'Формы и валидация HTML5' => ['materials_title' => 'MDN: HTML Формы', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Learn/Forms', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_html_forms/embed'],
            'CSS-селекторы и каскадность' => ['materials_title' => 'MDN: CSS Селекторы', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_selectors', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_css_selectors/embed'],
            'Box Model и позиционирование' => ['materials_title' => 'MDN: Box Model', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Learn/CSS/Building_blocks/The_box_model', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_css_boxmodel/embed'],
            'Flexbox: компоновка элементов' => ['materials_title' => 'MDN: Flexbox', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Learn/CSS/CSS_layout/Flexbox', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_css_flexbox/embed'],
            'CSS Grid: двумерная сетка' => ['materials_title' => 'MDN: CSS Grid', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_grid_layout', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_css_grid/embed'],
            'Адаптивный дизайн и медиа-запросы' => ['materials_title' => 'MDN: Медиа-запросы', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Web/CSS/Media_Queries', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_css_responsive/embed'],
            'CSS-анимации и трансформации' => ['materials_title' => 'MDN: CSS Transitions', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_transitions', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_css_animations/embed'],

            'Переменные, типы данных и операторы' => ['materials_title' => 'MDN: JavaScript типы', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Data_structures', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_js_variables/embed'],
            'Строки, массивы и объекты' => ['materials_title' => 'MDN: Массивы JS', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Global_Objects/Array', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_js_arrays/embed'],
            'Функции и области видимости' => ['materials_title' => 'MDN: Функции JS', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Functions', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_js_functions/embed'],
            'Работа с DOM' => ['materials_title' => 'MDN: DOM API', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Web/API/Document_Object_Model', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_js_dom/embed'],
            'Асинхронность: Promises и async/await' => ['materials_title' => 'MDN: Promises', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Global_Objects/Promise', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_js_async/embed'],
            'Обработка ошибок и отладка' => ['materials_title' => 'MDN: Try/Catch', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Statements/try...catch', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_js_errors/embed'],
            'Модули ES6 и работа с JSON' => ['materials_title' => 'MDN: ES6 Модули', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Statements/import', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_js_modules/embed'],
            'Работа с формами и валидация на JS' => ['materials_title' => 'MDN: FormData', 'materials_url' => 'https://developer.mozilla.org/ru/docs/Web/API/FormData', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_js_forms/embed'],

            'Синтаксис PHP и переменные' => ['materials_title' => 'PHP: Синтаксис', 'materials_url' => 'https://www.php.net/manual/ru/language.types.php', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_php_syntax/embed'],
            'Массивы и строки' => ['materials_title' => 'PHP: Массивы', 'materials_url' => 'https://www.php.net/manual/ru/language.types.array.php', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_php_arrays/embed'],
            'Функции и области видимости' => ['materials_title' => 'PHP: Функции', 'materials_url' => 'https://www.php.net/manual/ru/functions.userland.php', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_php_functions/embed'],
            'ООП в PHP' => ['materials_title' => 'PHP: ООП', 'materials_url' => 'https://www.php.net/manual/ru/oop.php', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_php_oop/embed'],
            'Работа с MySQL через PDO' => ['materials_title' => 'PHP: PDO', 'materials_url' => 'https://www.php.net/manual/ru/book.pdo.php', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_php_pdo/embed'],
            'Интерфейсы и абстрактные классы' => ['materials_title' => 'PHP: Интерфейсы', 'materials_url' => 'https://www.php.net/manual/ru/language.oop5.interfaces.php', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_php_interfaces/embed'],
            'Исключения и обработка ошибок' => ['materials_title' => 'PHP: Исключения', 'materials_url' => 'https://www.php.net/manual/ru/language.exceptions.php', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_php_exceptions/embed'],
            'Composer и автозагрузка' => ['materials_title' => 'Composer Docs', 'materials_url' => 'https://getcomposer.org/doc/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_php_composer/embed'],

            'MVC архитектура и маршрутизация' => ['materials_title' => 'Laravel: Routing', 'materials_url' => 'https://laravel.com/docs/11.x/routing', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_laravel_routing/embed'],
            'Eloquent ORM и миграции' => ['materials_title' => 'Laravel: Eloquent', 'materials_url' => 'https://laravel.com/docs/11.x/eloquent', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_laravel_eloquent/embed'],
            'Middleware и авторизация' => ['materials_title' => 'Laravel: Middleware', 'materials_url' => 'https://laravel.com/docs/11.x/middleware', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_laravel_middleware/embed'],
            'Blade шаблоны и компоненты' => ['materials_title' => 'Laravel: Blade', 'materials_url' => 'https://laravel.com/docs/11.x/blade', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_laravel_blade/embed'],
            'Валидация и запросы (Form Requests)' => ['materials_title' => 'Laravel: Validation', 'materials_url' => 'https://laravel.com/docs/11.x/validation', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_laravel_validation/embed'],
            'Работа с API и JSON' => ['materials_title' => 'Laravel: JSON', 'materials_url' => 'https://laravel.com/docs/11.x/responses#json-responses', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_laravel_api/embed'],

            'DDL: создание баз данных и таблиц' => ['materials_title' => 'MySQL: DDL', 'materials_url' => 'https://dev.mysql.com/doc/refman/8.0/en/sql-statements.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_mysql_ddl/embed'],
            'DML: SELECT, INSERT, UPDATE, DELETE' => ['materials_title' => 'MySQL: DML', 'materials_url' => 'https://dev.mysql.com/doc/refman/8.0/en/dml-statements.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_mysql_dml/embed'],
            'JOINы и подзапросы' => ['materials_title' => 'MySQL: JOIN', 'materials_url' => 'https://dev.mysql.com/doc/refman/8.0/en/join.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_mysql_joins/embed'],
            'Индексы и оптимизация' => ['materials_title' => 'MySQL: Optimization', 'materials_url' => 'https://dev.mysql.com/doc/refman/8.0/en/optimization.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_mysql_optimization/embed'],
            'Транзакции и блокировки' => ['materials_title' => 'MySQL: Transactions', 'materials_url' => 'https://dev.mysql.com/doc/refman/8.0/en/transactions.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_mysql_transactions/embed'],
            'Процедуры, триггеры и представления' => ['materials_title' => 'MySQL: Stored Programs', 'materials_url' => 'https://dev.mysql.com/doc/refman/8.0/en/stored-programs.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_mysql_stored/embed'],
            'Хранилища данных и нормализация' => ['materials_title' => 'MySQL: Storage Engines', 'materials_url' => 'https://dev.mysql.com/doc/refman/8.0/en/storage-engines.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_mysql_storage/embed'],

            'Введение в PostgreSQL' => ['materials_title' => 'PostgreSQL Docs', 'materials_url' => 'https://www.postgresql.org/docs/current/tutorial.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_pg_basics/embed'],
            'SELECT, JOIN, подзапросы' => ['materials_title' => 'PostgreSQL Queries', 'materials_url' => 'https://www.postgresql.org/docs/current/tutorial-join.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_pg_joins/embed'],
            'JSONB: хранение и запросы' => ['materials_title' => 'PostgreSQL JSONB', 'materials_url' => 'https://www.postgresql.org/docs/current/functions-json.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_pg_jsonb/embed'],
            'Оконные функции и CTE' => ['materials_title' => 'PostgreSQL Window', 'materials_url' => 'https://www.postgresql.org/docs/current/tutorial-window.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_pg_windows/embed'],
            'Индексы и оптимизация' => ['materials_title' => 'PostgreSQL Performance', 'materials_url' => 'https://www.postgresql.org/docs/current/indexes.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_pg_indexes/embed'],

            'Основы C++' => ['materials_title' => 'cppreference.com', 'materials_url' => 'https://ru.cppreference.com/w/cpp', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_cpp_basics/embed'],
            'Функции и ссылки' => ['materials_title' => 'C++ Functions', 'materials_url' => 'https://ru.cppreference.com/w/cpp/language/functions', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_cpp_functions/embed'],
            'ООП в C++' => ['materials_title' => 'C++ OOP', 'materials_url' => 'https://ru.cppreference.com/w/cpp/language/classes', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_cpp_oop/embed'],
            'Управление памятью' => ['materials_title' => 'C++ Memory', 'materials_url' => 'https://ru.cppreference.com/w/cpp/memory', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_cpp_memory/embed'],
            'STL: контейнеры и алгоритмы' => ['materials_title' => 'C++ STL', 'materials_url' => 'https://ru.cppreference.com/w/cpp/container', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_cpp_stl/embed'],
            'Шаблоны и метапрограммирование' => ['materials_title' => 'C++ Templates', 'materials_url' => 'https://ru.cppreference.com/w/cpp/language/templates', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_cpp_templates/embed'],

            'Синтаксис Python' => ['materials_title' => 'Python Docs', 'materials_url' => 'https://docs.python.org/3/tutorial/index.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_py_syntax/embed'],
            'Строки и collections' => ['materials_title' => 'Python Collections', 'materials_url' => 'https://docs.python.org/3/tutorial/datastructures.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_py_collections/embed'],
            'Функции и декораторы' => ['materials_title' => 'Python Functions', 'materials_url' => 'https://docs.python.org/3/tutorial/controlflow.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_py_functions/embed'],
            'ООП в Python' => ['materials_title' => 'Python OOP', 'materials_url' => 'https://docs.python.org/3/tutorial/classes.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_py_oop/embed'],
            'Модули и пакеты' => ['materials_title' => 'Python Modules', 'materials_url' => 'https://docs.python.org/3/tutorial/modules.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_py_modules/embed'],
            'Асинхронное программирование' => ['materials_title' => 'Python asyncio', 'materials_url' => 'https://docs.python.org/3/library/asyncio.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_py_async/embed'],
            'Работа с файлами и ошибки' => ['materials_title' => 'Python Files', 'materials_url' => 'https://docs.python.org/3/tutorial/inputoutput.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_py_files/embed'],

            'Основы Java' => ['materials_title' => 'Oracle Java Tutorials', 'materials_url' => 'https://docs.oracle.com/javase/tutorial/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_java_basics/embed'],
            'ООП в Java' => ['materials_title' => 'Java OOP', 'materials_url' => 'https://docs.oracle.com/javase/tutorial/java/javaOO/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_java_oop/embed'],
            'Коллекции Java' => ['materials_title' => 'Java Collections', 'materials_url' => 'https://docs.oracle.com/javase/8/docs/technotes/collections/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_java_collections/embed'],
            'Многопоточность' => ['materials_title' => 'Java Concurrency', 'materials_url' => 'https://docs.oracle.com/javase/tutorial/essential/concurrency/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_java_threads/embed'],
            'Spring Boot' => ['materials_title' => 'Spring Boot Guide', 'materials_url' => 'https://spring.io/guides', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_java_spring/embed'],

            'Основы C#' => ['materials_title' => 'Microsoft C# Docs', 'materials_url' => 'https://learn.microsoft.com/ru-ru/dotnet/csharp/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_cs_basics/embed'],
            'ООП и LINQ' => ['materials_title' => 'C# LINQ', 'materials_url' => 'https://learn.microsoft.com/ru-ru/dotnet/csharp/linq/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_cs_linq/embed'],
            'Async/Await в C#' => ['materials_title' => 'C# Async', 'materials_url' => 'https://learn.microsoft.com/ru-ru/dotnet/csharp/async', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_cs_async/embed'],
            'Entity Framework Core' => ['materials_title' => 'EF Core Docs', 'materials_url' => 'https://learn.microsoft.com/ru-ru/ef/core/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_cs_efcore/embed'],
            'ASP.NET Core MVC' => ['materials_title' => 'ASP.NET Core', 'materials_url' => 'https://learn.microsoft.com/ru-ru/aspnet/core/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_cs_aspnet/embed'],

            'Основы Git: установка, конфигурация, базовые команды' => ['materials_title' => 'Git Book', 'materials_url' => 'https://git-scm.com/book/ru/v2', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_git_basics/embed'],
            'Ветвление и слияние' => ['materials_title' => 'Git Branching', 'materials_url' => 'https://git-scm.com/book/ru/v2/%D0%92%D0%B5%D1%82%D0%B2%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2-Git', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_git_branching/embed'],
            'Удалённые репозитории и работа в команде' => ['materials_title' => 'Git Remote', 'materials_url' => 'https://git-scm.com/book/ru/v2/%D0%A0%D0%B0%D0%B1%D0%BE%D1%82%D0%B0-%D1%81-%D1%83%D0%B4%D0%B0%D0%BB%D1%91%D0%BD%D0%BD%D1%8B%D0%BC%D0%B8-%D1%80%D0%B5%D0%BF%D0%BE%D0%B7%D0%B8%D1%82%D0%BE%D1%80%D0%B8%D1%8F%D0%BC%D0%B8', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_git_remote/embed'],
            'Продвинутые техники Git' => ['materials_title' => 'Git Advanced', 'materials_url' => 'https://git-scm.com/book/ru/v2/Продвинутое-ветвление', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_git_advanced/embed'],

            'Основы DevOps: принципы, культурные изменения, CI/CD' => ['materials_title' => 'DevOps Roadmap', 'materials_url' => 'https://roadmap.sh/devops', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_devops_basics/embed'],
            'Автоматизация сборки: Jenkins, GitHub Actions, GitLab CI' => ['materials_title' => 'GitHub Actions Docs', 'materials_url' => 'https://docs.github.com/en/actions', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_devops_ci/embed'],
            'Мониторинг и логирование: Prometheus, Grafana, ELK' => ['materials_title' => 'Prometheus Docs', 'materials_url' => 'https://prometheus.io/docs/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_devops_monitoring/embed'],
            'Инфраструктура как код: Terraform, Ansible, CloudFormation' => ['materials_title' => 'Terraform Docs', 'materials_url' => 'https://developer.hashicorp.com/terraform/docs', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_devops_iac/embed'],

            'Основы React: компоненты, JSX, пропсы' => ['materials_title' => 'React Docs', 'materials_url' => 'https://react.dev/learn', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_react_basics/embed'],
            'Хуки: useState, useEffect, useRef, useContext' => ['materials_title' => 'React Hooks', 'materials_url' => 'https://react.dev/reference/react/hooks', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_react_hooks/embed'],
            'Управление состоянием: Context API, Redux, Zustand' => ['materials_title' => 'Redux Toolkit', 'materials_url' => 'https://redux-toolkit.js.org/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_react_state/embed'],
            'Роутинг и работа с API: React Router, fetch, axios' => ['materials_title' => 'React Router', 'materials_url' => 'https://reactrouter.com/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_react_router/embed'],

            'Основы Node.js: модули, fs, path, events' => ['materials_title' => 'Node.js Docs', 'materials_url' => 'https://nodejs.org/en/learn', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_node_basics/embed'],
            'Express.js: маршруты, middleware, обработка ошибок' => ['materials_title' => 'Express Docs', 'materials_url' => 'https://expressjs.com/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_node_express/embed'],
            'Работа с базами данных: MongoDB, Mongoose, Prisma' => ['materials_title' => 'Mongoose Docs', 'materials_url' => 'https://mongoosejs.com/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_node_db/embed'],
            'REST API и аутентификация: JWT, OAuth, rate limiting' => ['materials_title' => 'JWT.io', 'materials_url' => 'https://jwt.io/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_node_api/embed'],

            'Основы TypeScript: типы, интерфейсы, union types' => ['materials_title' => 'TypeScript Handbook', 'materials_url' => 'https://www.typescriptlang.org/docs/handbook/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_ts_basics/embed'],
            'Продвинутые типы: дженерики, conditional types, utility types' => ['materials_title' => 'TS Generics', 'materials_url' => 'https://www.typescriptlang.org/docs/handbook/2/generics.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_ts_advanced/embed'],
            'Классы и модули в TypeScript' => ['materials_title' => 'TS Classes', 'materials_url' => 'https://www.typescriptlang.org/docs/handbook/2/classes.html', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_ts_classes/embed'],
            'TypeScript с React: типизация пропсов, хуков, событий' => ['materials_title' => 'TS React Guide', 'materials_url' => 'https://react-typescript-cheatsheet.netlify.app/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_ts_react/embed'],

            'Основы Docker: контейнеры vs виртуальные машины, образы' => ['materials_title' => 'Docker Docs', 'materials_url' => 'https://docs.docker.com/get-started/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_docker_basics/embed'],
            'Написание Dockerfile: инструкции, многоэтапная сборка' => ['materials_title' => 'Dockerfile Reference', 'materials_url' => 'https://docs.docker.com/engine/reference/builder/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_dockerfile/embed'],
            'Docker Compose: оркестрация нескольких сервисов' => ['materials_title' => 'Compose Docs', 'materials_url' => 'https://docs.docker.com/compose/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_docker_compose/embed'],
            'Docker networking, volumes и безопасность' => ['materials_title' => 'Docker Networking', 'materials_url' => 'https://docs.docker.com/network/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_docker_advanced/embed'],

            'Архитектура Kubernetes: master/worker, компоненты кластера' => ['materials_title' => 'K8s Docs', 'materials_url' => 'https://kubernetes.io/docs/concepts/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_k8s_arch/embed'],
            'Pods, ReplicaSets и Deployments' => ['materials_title' => 'K8s Pods', 'materials_url' => 'https://kubernetes.io/docs/concepts/workloads/pods/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_k8s_pods/embed'],
            'Services, Ingress и ConfigMaps/Secrets' => ['materials_title' => 'K8s Services', 'materials_url' => 'https://kubernetes.io/docs/concepts/services-networking/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_k8s_services/embed'],
            'Persistent Volumes, StatefulSets и мониторинг' => ['materials_title' => 'K8s Storage', 'materials_url' => 'https://kubernetes.io/docs/concepts/storage/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_k8s_storage/embed'],

            'Обзор мобильной разработки: нативная vs кроссплатформенная' => ['materials_title' => 'Mobile Dev Guide', 'materials_url' => 'https://roadmap.sh/mobile', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_mobile_overview/embed'],
            'React Native: компоненты, стили, навигация' => ['materials_title' => 'React Native Docs', 'materials_url' => 'https://reactnative.dev/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_mobile_rn/embed'],
            'Flutter: виджеты, состояние, Material Design' => ['materials_title' => 'Flutter Docs', 'materials_url' => 'https://docs.flutter.dev/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_mobile_flutter/embed'],
            'Публикация приложений: App Store, Google Play' => ['materials_title' => 'Google Play Console', 'materials_url' => 'https://developer.android.com/distribute', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_mobile_publish/embed'],

            'Алфавит, произношение и простые фразы' => ['materials_title' => 'BBC Learning English', 'materials_url' => 'https://www.bbc.co.uk/learningenglish', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_eng_alphabet/embed'],
            'Артикли, местоимения и глагол to be' => ['materials_title' => 'English Grammar', 'materials_url' => 'https://www.englishclub.com/grammar/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_eng_grammar/embed'],
            'Present Simple, Present Continuous' => ['materials_title' => 'English Tenses', 'materials_url' => 'https://www.englishclub.com/grammar/tenses/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_eng_tenses/embed'],
            'Базовая лексика: семья, работа, еда, транспорт' => ['materials_title' => 'Vocabulary', 'materials_url' => 'https://www.englishclub.com/vocabulary/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_eng_vocab/embed'],

            'Основы UX: принципы пользовательского опыта, исследования' => ['materials_title' => 'NNGroup', 'materials_url' => 'https://www.nngroup.com/articles/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_ux_basics/embed'],
            'Теория цвета и типографика' => ['materials_title' => 'Color Theory', 'materials_url' => 'https://www.canva.com/colors/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_ux_color/embed'],
            'Прототипирование в Figma' => ['materials_title' => 'Figma Tutorial', 'materials_url' => 'https://help.figma.com/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_ux_figma/embed'],
            'Дизайн-системы и UI-компоненты' => ['materials_title' => 'Design Systems', 'materials_url' => 'https://www.designsystems.com/', 'presentation_url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_ux_designsystems/embed'],
        ];

        $updated = 0;
        foreach ($materials as $title => $data) {
            $lesson = Lesson::where('title', $title)->first();
            if ($lesson) {
                $lesson->update($data);
                $updated++;
            }
        }

        $this->command->info("Updated {$updated} lessons with materials and presentations.");
    }
}
