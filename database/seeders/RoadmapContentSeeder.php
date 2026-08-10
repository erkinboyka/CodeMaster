<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\RoadmapNode;
use App\Models\RoadmapLesson;
use App\Models\RoadmapQuizQuestion;

class RoadmapContentSeeder extends Seeder
{
    public function run(): void
    {
        $nodes = RoadmapNode::all();
        $roadmaps = $nodes->groupBy('roadmap_title');

        foreach ($roadmaps as $title => $roadmapNodes) {
            foreach ($roadmapNodes as $node) {
                $this->seedNodeLessons($node);
                $this->seedNodeQuizzes($node);
            }
        }

        $lessonCount = RoadmapLesson::count();
        $quizCount = RoadmapQuizQuestion::count();
        $this->command->info("Created {$lessonCount} roadmap lessons and {$quizCount} quiz questions.");
    }

    private function seedNodeLessons(RoadmapNode $node): void
    {
        $content = $this->getNodeContent($node->title, $node->topic);
        if (!$content) return;

        foreach ($content['lessons'] as $i => $lesson) {
            RoadmapLesson::create([
                'node_id' => $node->id,
                'title' => $lesson['title'],
                'description' => $lesson['description'],
                'materials' => $lesson['materials'] ?? '',
                'video_url' => $lesson['video_url'] ?? null,
                'order_index' => $i + 1,
            ]);
        }
    }

    private function seedNodeQuizzes(RoadmapNode $node): void
    {
        $content = $this->getNodeContent($node->title, $node->topic);
        if (empty($content['quizzes'])) return;

        foreach ($content['quizzes'] as $q) {
            RoadmapQuizQuestion::create([
                'node_id' => $node->id,
                'question' => $q['question'],
                'options' => json_encode($q['options']),
                'correct_answer' => $q['correct'],
            ]);
        }
    }

    private function getNodeContent(string $title, string $topic): ?array
    {
        $map = [
            // BACKEND DEVELOPER
            'Основы серверной разработки' => [
                'lessons' => [
                    ['title' => 'Как работает веб-сервер', 'description' => 'Веб-сервер — программа, принимающая HTTP-запросы от браузеров и возвращающая ответы (HTML, JSON, файлы). Клиент-серверная архитектура: браузер (клиент) отправляет запрос → сервер обрабатывает → возвращает результат. Статические сайты отдают готовые файлы (HTML, CSS, JS). Динамические — генерируют контент на лету (PHP, Python, Node.js). Серверное ПО: Apache — гибкий, модульный; Nginx — быстрый прокси и балансировщик; PHP-FPM — менеджер процессов PHP через FastCGI.', 'materials' => 'Apache vs Nginx: отличия, CGI и FastCGI протоколы, PHP-FPM настройка, Основы HTTP-серверов'],
                    ['title' => 'Основы HTTP', 'description' => 'HTTP (HyperText Transfer Protocol) — протокол передачи данных в вебе. Запрос: метод (GET — получить, POST — отправить, PUT — обновить, DELETE — удалить), URL, заголовки (Content-Type, Authorization), тело (для POST/PUT). Ответ: статус-код (200 — ОК, 404 — Не найдено, 500 — Ошибка сервера), заголовки, тело. Cookies и сессии позволяют серверу «помнить» пользователя между запросами.', 'materials' => 'HTTP/1.1 vs HTTP/2 vs HTTP/3, REST vs GraphQL, Справочник HTTP-методов, Курсы по веб-разработке'],
                ],
                'quizzes' => [
                    ['question' => 'Какой HTTP-метод используется для создания ресурса?', 'options' => ['GET', 'POST', 'DELETE', 'PATCH'], 'correct' => 'POST'],
                    ['question' => 'Что означает статус-код 404?', 'options' => ['Ошибка сервера', 'Не найдено', 'Unauthorized', 'OK'], 'correct' => 'Не найдено'],
                    ['question' => 'Какой заголовок указывает формат ответа?', 'options' => ['Content-Type', 'Accept', 'Authorization', 'Cache-Control'], 'correct' => 'Content-Type'],
            ],
            ],
            'PHP' => [
                'lessons' => [
                    ['title' => 'Синтаксис PHP', 'description' => 'PHP — серверный язык, встраиваемый в HTML. Переменные начинаются с $ ($name = \"Иван\"). Типы: string, int, float, bool, array, null. Вывод: echo — строки, var_dump() — подробная информация, print_r() — читаемый вывод массивов. Операторы: +, -, *, /, ==, ===, &&, ||. Код в тегах <?php ... ?>. Комментарии: // однострочный, /* многострочный */.', 'materials' => 'Официальная документация PHP, PEP 8 стили кодирования, Различия echo vs print'],
                    ['title' => 'Функции и массивы', 'description' => 'Функции: function getSum($a, $b) { return $a + $b; }. Параметры со значениями по умолчанию ($value = null). Массивы: $arr = [1, 2, 3] или $user = [\"name\" => \"Иван\"]. Функции массивов: array_map() — применение к каждому элементу, array_filter() — фильтрация, array_reduce() — свёртка в одно значение, count() — подсчёт, array_merge() — объединение.', 'materials' => 'Список функций массивов PHP, array_map vs array_filter, Примеры array_reduce'],
                ],
                'quizzes' => [
                    ['question' => 'Как объявить переменную в PHP?', 'options' => ['var', 'let', '$', '@'], 'correct' => '$'],
                    ['question' => 'Какая функция выводит данные на экран?', 'options' => ['console.log', 'echo', 'print_r', 'var_dump'], 'correct' => 'echo'],
                    ['question' => 'Что делает функция count()?', 'options' => ['Считает символы', 'Возвращает размер массива', 'Сортирует массив', 'Объединяет массивы'], 'correct' => 'Возвращает размер массива'],
            ],
            ],
            'MySQL' => [
                'lessons' => [
                    ['title' => 'Основы SQL', 'description' => 'SQL — язык запросов для реляционных БД. CRUD: INSERT INTO users (name) VALUES (\"Иван\") — создание; SELECT * FROM users WHERE id = 1 — чтение; UPDATE users SET name = \"Петр\" WHERE id = 1 — обновление; DELETE FROM users WHERE id = 1 — удаление. Типы данных: INT — целые, VARCHAR(255) — строки, TEXT — длинные строки, DATE — даты. DDL (CREATE, ALTER, DROP) — структура БД, DML (SELECT, INSERT, UPDATE, DELETE) — данные.', 'materials' => 'Основы SQL для начинающих, DDL vs DML, Типы данных MySQL'],
                    ['title' => 'Запросы и фильтрация', 'description' => 'WHERE — фильтрация (WHERE age > 18 AND status = \"active\"). ORDER BY — сортировка (ORDER BY name ASC/DESC). LIMIT — ограничение (LIMIT 10 OFFSET 20 — пагинация). GROUP BY — группировка для агрегации: SELECT category, COUNT(*) FROM products GROUP BY category. HAVING — фильтрация после группировки (HAVING count > 5). Агрегаты: COUNT(*), SUM(amount), AVG(price), MAX/MIN. Операторы: BETWEEN, IN, LIKE, IS NULL.', 'materials' => 'Агрегатные функции SQL, GROUP BY, Пагинация запросов'],
                ],
                'quizzes' => [
                    ['question' => 'Какой оператор выбирает данные из таблицы?', 'options' => ['INSERT', 'SELECT', 'UPDATE', 'DELETE'], 'correct' => 'SELECT'],
                    ['question' => 'Что делает WHERE?', 'options' => ['Сортирует', 'Фильтрует строки', 'Группирует', 'Соединяет таблицы'], 'correct' => 'Фильтрует строки'],
                    ['question' => 'Какая функция считает количество строк?', 'options' => ['SUM()', 'COUNT()', 'AVG()', 'MAX()'], 'correct' => 'COUNT()'],
            ],
            ],
            'HTTP / REST' => [
                'lessons' => [
                    ['title' => 'Принципы REST', 'description' => 'REST (Representational State Transfer) — архитектурный стиль API. Принципы: 1) Ресурсы имеют URI (/api/users); 2) Клиент-сервер — разделение ответственности; 3) Stateless — сервер не хранит состояние; 4) Кэширование — ускорение ответов; 5) Единообразие интерфейса — стандартные HTTP-методы. URL: /users — коллекция, /users/123 — ресурс, /users/123/orders — вложенный ресурс.', 'materials' => 'REST vs GraphQL vs gRPC, HATEOAS, RESTful URL-структура'],
                    ['title' => 'Статус-коды HTTP', 'description' => 'Статус-коды: 2xx — успех (200 OK, 201 Created, 204 No Content), 3xx — перенаправление (301 Moved Permanently, 304 Not Modified), 4xx — ошибка клиента (400 Bad Request, 401 Unauthorized, 403 Forbidden, 404 Not Found, 422 Unprocessable Entity), 5xx — ошибка сервера (500 Internal Server Error, 502 Bad Gateway, 503 Service Unavailable). CORS — механизм разрешения кросс-доменных запросов.', 'materials' => 'Полный справочник HTTP-статус-кодов, CORS — объяснение, Идемпотентность HTTP-методов'],
                ],
                'quizzes' => [
                    ['question' => 'Какой статус-код означает успешное создание?', 'options' => ['200', '201', '204', '301'], 'correct' => '201'],
                    ['question' => 'Что такое stateless?', 'options' => ['Сохранение состояния', 'Без сохранения состояния', 'Кэширование', 'Сжатие'], 'correct' => 'Без сохранения состояния'],
                    ['question' => 'Какой метод HTTP для обновления ресурса?', 'options' => ['GET', 'POST', 'PUT', 'PATCH'], 'correct' => 'PUT'],
            ],
            ],
            'PHP OOP' => [
                'lessons' => [
                    ['title' => 'Классы и объекты', 'description' => 'ООП — парадигма, основанная на объектах. Класс — чертёж объекта, объект — экземпляр. Свойства (переменные) и методы (функции) класса. Конструктор __construct() вызывается при создании: new User(\"Иван\"). Модификаторы: public — везде, protected — в классе и наследниках, private — только внутри. Инкапсуляция — сокрытие реализации через геттеры/сеттеры. Статические методы принадлежат классу (self::count++).', 'materials' => 'Модификаторы доступа в PHP, Конструкторы и деструкторы, Статические методы'],
                    ['title' => 'Наследование и интерфейсы', 'description' => 'Наследование (extends) — класс-наследник получает всё от родителя: class Admin extends User { public function getRole() { return \"admin\"; } }. Абстрактный класс (abstract) — нельзя создать объект: abstract class Shape { abstract public function area(); }. Интерфейс (interface) — контракт методов: interface Drawable { public function draw(); }. Trait — повторное использование кода: trait SoftDeletable { public function softDelete() { ... } }. Полиморфизм — разные типы через единый интерфейс.', 'materials' => 'SOLID принципы в PHP, abstract class vs interface, Trait в PHP'],
                ],
                'quizzes' => [
                    ['question' => 'Какой модификатор скрывает данные отнаружнего доступа?', 'options' => ['public', 'private', 'protected', 'static'], 'correct' => 'private'],
                    ['question' => 'Что делает abstract class?', 'options' => ['Нельзя создать объект', 'Нельзя наследовать', 'Нельзя переопределить', 'Нельзя использовать'], 'correct' => 'Нельзя создать объект'],
                    ['question' => 'Как реализовать интерфейс в PHP?', 'options' => ['extends', 'implements', 'uses', 'requires'], 'correct' => 'implements'],
            ],
            ],
            'SQL Advanced' => [
                'lessons' => [
                    ['title' => 'JOINы', 'description' => 'JOIN — объединение строк из двух таблиц по связанному столбцу. INNER JOIN — только совпадающие строки из обеих таблиц: SELECT u.name, o.id FROM users u INNER JOIN orders o ON u.id = o.user_id. LEFT JOIN — все из левой + совпадающие из правой (NULL если нет). RIGHT JOIN — наоборот. FULL JOIN — все из обеих. CROSS JOIN — декартово произведение. Self-JOIN — таблица сама с собой (иерархия сотрудников).', 'materials' => 'Визуальный справочник SQL JOIN, Подзапросы в SQL, WHERE vs HAVING'],
                    ['title' => 'Индексы и оптимизация', 'description' => 'Индексы ускоряют поиск (как оглавление в книге). B-tree — основной тип, подходит для диапазонов (BETWEEN, >, <). Hash — быстрый точный поиск (=), не для диапазонов. Составные — по нескольким столбцам (INDEX(name, age)). EXPLAIN показывает план выполнения: как обходится таблица, используются ли индексы. Оптимизация: SELECT конкретных столбцов, кэширование, нормализация БД.', 'materials' => 'EXPLAIN в MySQL, B-tree vs Hash индексы, Нормализация БД'],
                ],
                'quizzes' => [
                    ['question' => 'Какой JOIN возвращает все строки из левой таблицы?', 'options' => ['INNER', 'LEFT', 'RIGHT', 'CROSS'], 'correct' => 'LEFT'],
                    ['question' => 'Что показывает EXPLAIN?', 'options' => ['Данные', 'План выполнения запроса', 'Ошибки', 'Статистику'], 'correct' => 'План выполнения запроса'],
                    ['question' => 'Какой тип индекса лучше для диапазонных запросов?', 'options' => ['Hash', 'B-tree', 'GIN', 'GiST'], 'correct' => 'B-tree'],
            ],
            ],
            'Composer / Packages' => [
                'lessons' => [
                    ['title' => 'Основы Composer', 'description' => 'Composer — менеджер зависимостей PHP. composer require vendor/package — установка пакета. composer update — обновление (по composer.lock). composer install — установка из lock-файла. PSR-4 — автозагрузка по namespace: \"autoload\": {\"psr-4\": {\"App\\\\\": \"app/\"}}. composer.lock фиксирует точные версии. Packagist — репозиторий пакетов. SemVer: MAJOR.MINOR.PATCH — major ломает совместимость, minor добавляет, patch исправляет.', 'materials' => 'Документация Composer, Packagist — поиск пакетов, SemVer — версионирование'],
                    ['title' => 'Создание пакетов', 'description' => 'Структура пакета: src/ — код, composer.json — конфигурация, README.md — документация. Service Provider — точка интеграции в Laravel (регистрация сервисов). Facade — статический интерфейс (Cache::get(), Auth::check()). Dependency Injection — зависимости через конструктор, а не создание внутри. Публикация: GitHub → packagist.org → composer require vendor/package.', 'materials' => 'Service Provider в Laravel, Dependency Injection, Facade в Laravel'],
                ],
                'quizzes' => [
                    ['question' => 'Как установить пакет через Composer?', 'options' => ['npm install', 'composer require', 'pip install', 'gem install'], 'correct' => 'composer require'],
                    ['question' => 'Что такое PSR-4?', 'options' => ['Тестирование', 'Автозагрузка по namespace', 'Кэширование', 'Логирование'], 'correct' => 'Автозагрузка по namespace'],
                    ['question' => 'Где публикуются PHP-пакеты?', 'options' => ['GitHub', 'npm', 'Packagist', 'PyPI'], 'correct' => 'Packagist'],
            ],
            ],
            'Laravel' => [
                'lessons' => [
                    ['title' => 'Маршруты и контроллеры', 'description' => 'Route::get(\"/users\", [UserController::class, \"index\"]) — GET-запрос на /users. Route::resource(\"users\", UserController::class) — все CRUD-маршруты автоматически. Middleware — промежуточные обработчики (аутентификация, CSRF): Route::middleware(\"auth\"). Named routes: Route::get(\"/profile\", ...)->name(\"profile\") — ссылка по имени route(\"profile\"). Resource controllers — контроллеры с методами index, store, show, update, destroy.', 'materials' => 'Маршрутизация в Laravel, Middleware — типы и создание, Resource Controllers'],
                    ['title' => 'Blade шаблоны', 'description' => 'Blade — шаблонизатор Laravel. Переменные: {{ $var }} — с экранированием, {!! $html !!}$ — без. Условия: @if($cond) ... @elseif ... @else ... @endif. Циклы: @foreach($items as $item) ... @endforeach. Наследование: @extends(\"layouts.app\"), @section(\"content\") ... @section, @yield(\"content\"). Компоненты: @component(\"alert\") ... @endcomponent. Директивы: @csrf — токен, @method(\"PUT\") — фиктивный метод, @auth — проверка авторизации.', 'materials' => 'Blade — полный справочник, Компоненты и слоты, Шаблонное наследование'],
                ],
                'quizzes' => [
                    ['question' => 'Как объявить маршрут в Laravel?', 'options' => ['app.get()', 'Route::get()', '@route()', 'HTTP.get()'], 'correct' => 'Route::get()'],
                    ['question' => 'Что делает @csrf в Blade?', 'options' => ['Добавляет стили', 'Защита от CSRF', 'Подключает JS', 'Логин'], 'correct' => 'Защита от CSRF'],
                    ['question' => 'Какой метод создаёт ресурсный маршрут?', 'options' => ['Route::get()', 'Route::resource()', 'Route::api()', 'Route::group()'], 'correct' => 'Route::resource()'],
            ],
            ],
            'Eloquent ORM' => [
                'lessons' => [
                    ['title' => 'Модели и связи', 'description' => 'Eloquent ORM — ActiveRecord-паттерн. Модель отражает таблицу (User → users). Связи: hasOne (один к одному), hasMany (один ко многим), belongsTo (обратная), belongsToMany (многие ко многим через промежуточную таблицу), morphMany (полиморфные). Eager Loading: User::with(\"posts\") — загружает связанные данные одним запросом, решая N+1 проблему (отдельный запрос на каждый элемент цикла).', 'materials' => 'Связи в Eloquent, Eager Loading и N+1, Полиморфные связи'],
                    ['title' => 'Запросы к БД', 'description' => 'Query Builder: User::where(\"age\", \">\", 18)->orderBy(\"name\")->paginate(15). Методы: where, orWhere, whereIn, whereBetween, whereNull. Агрегации: User::count(), User::sum(\"balance\"). Scopes — именованные области: User::active()->get() (scopeActive в модели). Пагинация: paginate() — со ссылками, simplePaginate() — без, cursorPaginate() — курсорная. Chunking: User::chunk(100, fn($users) => ...) — обработка больших выборок.', 'materials' => 'Query Builder в Laravel, Scopes, Типы пагинации'],
                ],
                'quizzes' => [
                    ['question' => 'Какая связь "один к одному"?', 'options' => ['hasMany', 'hasOne', 'belongsTo', 'belongsToMany'], 'correct' => 'hasOne'],
                    ['question' => 'Что такое N+1 проблема?', 'options' => ['Много запросов в цикле', 'Нет соединения', 'Дубли данных', 'Ошибка типа'], 'correct' => 'Много запросов в цикле'],
                    ['question' => 'Как пагинировать результат?', 'options' => ['->all()', '->paginate(15)', '->chunk()', '->limit()'], 'correct' => '->paginate(15)'],
            ],
            ],
            'Authentication' => [
                'lessons' => [
                    ['title' => 'Авторизация в Laravel', 'description' => 'Auth facade, Guard, Provider, Remember me, password reset.', 'materials' => 'Laravel Breeze, Fortify'],
                    ['title' => 'API токены', 'description' => 'Sanctum, Passport, JWT, OAuth2, token-based auth.', 'materials' => 'Stateful vs stateless auth'],
                ],
                'quizzes' => [
                    ['question' => 'Как проверить авторизован ли пользователь?', 'options' => ['Auth::check()', 'Auth::user()', 'Auth::id()', 'Auth::guest()'], 'correct' => 'Auth::check()'],
                    ['question' => 'Что такое Sanctum?', 'options' => ['ORM', 'Пакет для API-аутентификации', 'Шаблонизатор', 'Тестирование'], 'correct' => 'Пакет для API-аутентификации'],
                    ['question' => 'Какой метод хеширует пароль?', 'options' => ['md5()', 'hash()', 'Hash::make()', 'bcrypt()'], 'correct' => 'Hash::make()'],
            ],
            ],
            'Migrations & Seeds' => [
                'lessons' => [
                    ['title' => 'Миграции', 'description' => 'Schema::create, Blueprint, модификация таблиц, откат.', 'materials' => 'php artisan make:migration'],
                    ['title' => 'Сидеры и фабрики', 'description' => 'DatabaseSeeder, factory(), faker, массовое создание данных.', 'materials' => 'Model::factory()->count(100)->create()'],
                ],
                'quizzes' => [
                    ['question' => 'Как создать миграцию?', 'options' => ['php artisan migrate', 'php artisan make:migration', 'php artisan db:seed', 'php artisan cache:clear'], 'correct' => 'php artisan make:migration'],
                    ['question' => 'Что делает down() в миграции?', 'options' => ['Применяет', 'Откатывает', 'Удаляет БД', 'Очищает кэш'], 'correct' => 'Откатывает'],
                    ['question' => 'Как создать 100 записей через фабрику?', 'options' => ['Model::create(100)', 'Model::factory()->count(100)->create()', 'Model::seed(100)', 'Model::insert(100)'], 'correct' => 'Model::factory()->count(100)->create()'],
            ],
            ],

            // FRONTEND DEVELOPER
            'Основы интернета' => [
                'lessons' => [
                    ['title' => 'Как работает интернет', 'description' => 'Интернет — глобальная сеть компьютеров, связанных по протоколу TCP/IP. Когда вы вводите URL, DNS (Domain Name System) преобразует доменное имя (example.com) в IP-адрес (93.184.216.34), чтобы серверы нашли друг друга. HTTP/HTTPS — протоколы передачи данных: клиент (браузер) отправляет запрос → сервер обрабатывает → возвращает HTML, CSS, JSON. Порт 443 используется для HTTPS (шифрование через SSL/TLS). Браузерный движок (Blink, Gecko, WebKit) парсит HTML в DOM-дерево, применяет CSS и рендерит страницу.', 'materials' => 'TCP/IP — модель, DNS — как работает, OSI модель, HTTP/HTTPS — разница'],
                    ['title' => 'Инструменты веб-разработчика', 'description' => 'Chrome DevTools (F12) — встроенные инструменты для отладки. Elements — просмотр и редактирование DOM/CSS в реальном времени. Console — выполнение JavaScript-кода, вывод ошибок и логов. Network — мониторинг всех запросов (заголовки, тело ответа, время загрузки, статус-коды). Performance — профилирование производительности (CPU, память, FPS). Lighthouse — автоматический аудит производительности, a11y, SEO. Firefox Inspector аналогичен, но имеет уникальные функции (CSS Grid overlay).', 'materials' => 'Chrome DevTools — документация, Firefox Developer Tools, Lighthouse аудит'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое DNS?', 'options' => ['Протокол передачи файлов', 'Система доменных имён', 'Язык программирования', 'База данных'], 'correct' => 'Система доменных имён'],
                    ['question' => 'Какой порт используется для HTTPS?', 'options' => ['80', '443', '3000', '8080'], 'correct' => '443'],
                    ['question' => 'Что делает браузерный движок?', 'options' => ['Запускает Python', 'Рендерит HTML/CSS', 'Компилирует C++', 'Управляет БД'], 'correct' => 'Рендерит HTML/CSS'],
            ],
            ],
            'HTML' => [
                'lessons' => [
                    ['title' => 'Основы HTML', 'description' => 'HTML — язык разметки для структуры веб-страниц. Минимальный HTML: <!DOCTYPE html><html><head><meta charset=\"UTF-8\"><title>...</title></head><body>...</body></html>. Теги: <h1>-<h6> — заголовки, <p> — абзац, <a href=\"...\"> — ссылка, <img src=\"...\" alt=\"...\"> — изображение, <div> — блочный контейнер, <span> — строчный. Атрибуты дают тегам свойства (class, id, style, href).', 'materials' => 'MDN HTML — справочник, HTML Academy — обучение, Структура HTML-документа'],
                    ['title' => 'Семантический HTML', 'description' => 'Семантические теги описывают смысл контента. header — шапка, nav — навигация, main — основной контент (один на страницу), article — независимый контент (статья), section — тематическая секция, aside — боковая панель, footer — подвал, figure — медиа с подписью figcaption. Преимущества: SEO (поисковики понимают структуру), доступность (скринридеры используют семантику), поддержка (код понятен разработчикам).', 'materials' => 'MDN: Семантический HTML, HTML5 Doctor, WAI-ARIA — доступность'],
                ],
                'quizzes' => [
                    ['question' => 'Какой тег является корневым элементом?', 'options' => ['<body>', '<html>', '<head>', '<document>'], 'correct' => '<html>'],
                    ['question' => 'Для чего тег <nav>?', 'options' => ['Навигация', 'Название', 'Видео', 'Навигатор'], 'correct' => 'Навигация'],
                    ['question' => 'Сколько тегов <main> допускается?', 'options' => ['Неограниченно', 'Один', 'Два', 'Три'], 'correct' => 'Один'],
            ],
            ],
            'CSS' => [
                'lessons' => [
                    ['title' => 'Селекторы и каскадность', 'description' => 'CSS-селекторы определяют к каким элементам применять стили. Типовые: element (h1, p), .class (класс), #id (идентификатор). Комбинированные: .nav .item — вложенный, .nav > item — прямой потомок. Псевдоклассы: :hover, :focus, :nth-child(2n). Специфичность: inline (1000) > #id (100) > .class (10) > element (1). !important переопределяет. CSS-каскад — более специфичные стили перебивают менее специфичные.', 'materials' => 'Справочник CSS-селекторов, Специфичность CSS, Каскад и наследование'],
                    ['title' => 'Box Model', 'description' => 'Каждый элемент — прямоугольная коробка. Content — содержимое. Padding — внутренний отступ. Border — рамка. Margin — внешний отступ. Box-sizing: content-box (по умолчанию) vs border-box (width = content + padding + border — удобнее). Рекомендация: * { box-sizing: border-box; }. Display: block (вся ширина, перенос), inline (по ширине содержимого), inline-block (как inline + padding/margin). Position: static, relative, absolute, fixed, sticky.', 'materials' => 'MDN: Box Model, CSS Tricks: Box Sizing, Свойства position'],
                ],
                'quizzes' => [
                    ['question' => 'Какой селектор выбирает элемент по классу?', 'options' => ['#', '.', '@', '*'], 'correct' => '.'],
                    ['question' => 'Что делает box-sizing: border-box?', 'options' => ['Добавляет рамку', 'Включает padding/border в width', 'Удаляет отступы', 'Скругляет углы'], 'correct' => 'Включает padding/border в width'],
                    ['question' => 'Какое свойство задаёт внешний отступ?', 'options' => ['padding', 'margin', 'border', 'outline'], 'correct' => 'margin'],
            ],
            ],
            'Терминал и CLI' => [
                'lessons' => [
                    ['title' => 'Основы терминала', 'description' => 'Терминал — текстовый интерфейс управления. Команды: pwd — текущая директория, ls/dir — список файлов, cd — смена директории, mkdir — создание папки, rm — удаление, cp — копирование, mv — перемещение, cat — содержимое файла, grep — поиск. Pipe (|) — передача вывода: ls | grep \".php\". Редакторы: nano (простой), vim (мощный).', 'materials' => 'Learn the Command Line, Linux Journey, Bash Reference'],
                    ['title' => 'Package managers', 'description' => 'npm — менеджер пакетов для JavaScript. npm init -y — создать package.json. npm install react — установить пакет. npm install -D jest — devDependency (только для разработки). package.json — конфигурация: зависимости, скрипты, мета-информация. node_modules — папка с пакетами (не коммитится). .gitignore — исключения из git. npm audit — проверка уязвимостей. Yarn и pnpm — альтернативы npm.', 'materials' => 'npm Docs, Yarn — альтернатива npm, pnpm — быстрый менеджер пакетов'],
                ],
                'quizzes' => [
                    ['question' => 'Какая команда показывает текущую директорию?', 'options' => ['ls', 'cd', 'pwd', 'cat'], 'correct' => 'pwd'],
                    ['question' => 'Что делает npm install?', 'options' => ['Удаляет пакеты', 'Устанавливает зависимости', 'Запускает сервер', 'Компилирует код'], 'correct' => 'Устанавливает зависимости'],
                    ['question' => 'Как удалить файл через терминал?', 'options' => ['del', 'rm', 'remove', 'unlink'], 'correct' => 'rm'],
            ],
            ],
            'CSS Flexbox' => [
                'lessons' => [
                    ['title' => 'Flex-контейнер', 'description' => 'Flexbox — система раскладки. display: flex — flex-контейнер. flex-direction: row (горизонтально), column (вертикально). justify-content — по главной оси: flex-start, center, space-between, space-around. align-items — по поперечной: flex-start, center, stretch. gap — отступ между элементами. flex-wrap: wrap — перенос на новую строку. Центрирование: display: flex; justify-content: center; align-items: center;.', 'materials' => 'Flexbox Froggy — игра, MDN Flexbox, CSS-Tricks: Flexbox Guide'],
                    ['title' => 'Flex-элементы', 'description' => 'flex-grow — растяжение (0 — не растягивается, 1 — растягивается). flex-shrink — сжатие. flex-basis — начальный размер. flex: 1 1 0 — короткая запись. order — порядок элементов (по умолчанию 0). align-self — выравнивание конкретного элемента по поперечной оси (переопределяет align-items). Свободное пространство распределяется между элементами с flex-grow > 0.', 'materials' => 'MDN: Свойства flex-элементов, Flexbox — интерактивные примеры, Центрирование'],
                ],
                'quizzes' => [
                    ['question' => 'Какое свойство задаёт направление элементов?', 'options' => ['justify-content', 'flex-direction', 'align-items', 'flex-wrap'], 'correct' => 'flex-direction'],
                    ['question' => 'Как выровнять по центру по поперечной оси?', 'options' => ['justify-content: center', 'align-items: center', 'text-align: center', 'margin: auto'], 'correct' => 'align-items: center'],
                    ['question' => 'Что делает flex: 1?', 'options' => ['Один элемент', 'Растягивает на доступное пространство', 'Фиксирует ширину', 'Скрывает элемент'], 'correct' => 'Растягивает на доступное пространство'],
            ],
            ],
            'CSS Grid' => [
                'lessons' => [
                    ['title' => 'Определение сетки', 'description' => 'CSS Grid — двумерная раскладка. display: grid. grid-template-columns: 200px 1fr 2fr — три колонки. repeat(3, 1fr) — три равные. minmax(200px, 1fr) — от 200px до заполнения. auto-fill/auto-fit — автоматическое заполнение. gap — отступы. Единица fr — доля свободного пространства. grid-template-rows: auto 1fr auto — строки. Подходит для сложных макетов: галереи, дашборды.', 'materials' => 'Grid Garden — игра, MDN: CSS Grid, CSS-Tricks: Grid Guide'],
                    ['title' => 'Размещение элементов', 'description' => 'grid-column: 1 / 3 — колонки с 1 по 3. grid-row: 1 / span 2 — 2 строки с первой. grid-area: header — именованная область. grid-template-areas: \"header header\" \"sidebar main\" \"footer footer\" — визуальный макет. grid-column-start/end, grid-row-start/end — точное позиционирование. Grid лучше для двумерных макетов, Flexbox — для одномерных.', 'materials' => 'CSS Grid — размещение, Grid Areas, Grid vs Flexbox'],
                ],
                'quizzes' => [
                    ['question' => 'Как создать 3 равных колонки?', 'options' => ['grid-template-columns: 3px', 'grid-template-columns: repeat(3, 1fr)', 'grid-columns: 3', 'display: grid 3'], 'correct' => 'grid-template-columns: repeat(3, 1fr)'],
                    ['question' => 'Что такое fr?', 'options' => ['Пиксели', 'Доля свободного пространства', 'Процент', 'Порядок'], 'correct' => 'Доля свободного пространства'],
                    ['question' => 'Как задать именованную область?', 'options' => ['grid-area: name', 'grid-template-areas: "name"', 'area: name', 'grid-name: name'], 'correct' => 'grid-template-areas: "name"'],
            ],
            ],
            'Git' => [
                'lessons' => [
                    ['title' => 'Основы Git', 'description' => 'Git — система контроля версий. git init — инициализация, git add . — добавить в staging, git commit -m \"описание\" — сохранить. git status — состояние файлов, git log — история коммитов, git diff — разница. Working Directory → Staging Area → Repository. .gitignore — исключения (node_modules, .env). git clone — клонировать репозиторий.', 'materials' => 'Git-scm — документация, GitHub Guides, Learn Git Branching — курс'],
                    ['title' => 'Ветвление', 'description' => 'Ветки — изолированные линии разработки. git branch feature — создать, git checkout feature — переключиться, git checkout -b feature — создать и переключиться. git merge feature — слить ветку. Git Flow: main (продакшен), develop (разработка), feature/*, release/*, hotfix/*. Конфликты слияния — Git не может автоматически объединить. git rebase — перемещение коммитов (чистая история). git stash — временно сохранить изменения.', 'materials' => 'Git Branching — визуальный гайд, Git Flow, Разрешение конфликтов'],
                ],
                'quizzes' => [
                    ['question' => 'Как сохранить изменения?', 'options' => ['git save', 'git commit', 'git push', 'git store'], 'correct' => 'git commit'],
                    ['question' => 'Что делает git merge?', 'options' => ['Удаляет ветку', 'Объединяет ветки', 'Переключает ветку', 'Откатывает коммит'], 'correct' => 'Объединяет ветки'],
                    ['question' => 'Как создать новую ветку?', 'options' => ['git branch dev', 'git new dev', 'git create dev', 'git checkout dev'], 'correct' => 'git branch dev'],
            ],
            ],

            // MORE COMMON NODES
            'Linux / Terminal' => [
                'lessons' => [
                    ['title' => 'Файловая система Linux', 'description' => '/, /home, /etc, /var, /usr, права доступа, chmod, chown.', 'materials' => 'ls -la, файловые атрибуты'],
                    ['title' => 'Процессы и сервисы', 'description' => 'ps, top, kill, systemctl, cron, systemd.', 'materials' => 'systemctl, journalctl'],
                ],
                'quizzes' => [
                    ['question' => 'Как изменить права доступа?', 'options' => ['chmod', 'chown', 'chgrp', 'chperm'], 'correct' => 'chmod'],
                    ['question' => 'Как показать работающие процессы?', 'options' => ['ls', 'ps', 'top', 'Both ps and top'], 'correct' => 'Both ps and top'],
                    ['question' => 'Где находятся системные конфиги?', 'options' => ['/home', '/etc', '/var', '/tmp'], 'correct' => '/etc'],
                ],
            ],
            'Docker' => [
                'lessons' => [
                    ['title' => 'Основы Docker', 'description' => 'Docker — контейнеризация. Контейнер — изолированная среда выполнения (лёгкая, быстрее VM). Docker image — шаблон для контейнера (read-only слои). Dockerfile: FROM node:18, WORKDIR /app, COPY . ., RUN npm install, EXPOSE 3000, CMD [\"npm\", \"start\"]. docker build -t myapp . — сборка. docker run -p 3000:3000 myapp — запуск с пробросом порта. Контейнеры изолированы, воспроизводимы, портируемы.', 'materials' => 'Docker Docs — документация, Docker Hub — хранилище образов, Dockerfile best practices'],
                    ['title' => 'Docker Compose', 'description' => 'Docker Compose — запуск нескольких контейнеров. docker-compose.yml: services: web: build: ., ports: \"3000:3000\", db: image: mysql:8. volumes — сохранение данных (db-data:/var/lib/mysql). networks — изолированные сети. depends_on — порядок запуска. docker-compose up -d — запуск в фоне, docker-compose down — остановка. Удобно для dev-окружений: web + database + cache одной командой.', 'materials' => 'Docker Compose — документация, Примеры docker-compose.yml, Compose vs Kubernetes'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Docker image?', 'options' => ['Запущенный контейнер', 'Шаблон для контейнера', 'Файл конфигурации', 'Сеть'], 'correct' => 'Шаблон для контейнера'],
                    ['question' => 'Как запустить контейнер в фоне?', 'options' => ['docker run', 'docker run -d', 'docker start', 'docker exec'], 'correct' => 'docker run -d'],
                    ['question' => 'Что делает EXPOSE в Dockerfile?', 'options' => ['Открывает порт', 'Запускает контейнер', 'Копирует файлы', 'Удаляет образ'], 'correct' => 'Открывает порт'],
            ],
            ],
            'Kubernetes' => [
                'lessons' => [
                    ['title' => 'Архитектура K8s', 'description' => 'Kubernetes — оркестрация контейнеров. Master-ноды: API Server (входная точка), etcd (хранилище), Scheduler (распределение), Controller Manager. Worker-ноды: kubelet (агент), kube-proxy (сеть). kubectl — CLI для управления (kubectl get pods, kubectl apply -f manifest.yaml). Минифика — однонодовый кластер для разработки. Автоматическое масштабирование, самовосстановление, обновления без даунтайма.', 'materials' => 'Kubernetes Docs, kubectl Cheat Sheet, Minikube — локальный кластер'],
                    ['title' => 'Ресурсы K8s', 'description' => 'Pod — минимальная единица (1+ контейнеров с общей сетью). Deployment — управление репликами и обновлениями (3 реплики, кассетные обновления). Service — стабильная точка доступа к Pod-ам (ClusterIP, NodePort, LoadBalancer). ConfigMap — конфигурация, Secret — секреты. Ingress — маршрутизация HTTP-трафика извне. PersistentVolume — постоянное хранилище. YAML-манифесты: apiVersion, kind, metadata, spec.', 'materials' => 'Kubernetes Concepts, YAML-манифесты, Deployment vs StatefulSet'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Pod?', 'options' => ['Контейнер или группа контейнеров', 'Нода', 'Сеть', 'Диск'], 'correct' => 'Контейнер или группа контейнеров'],
                    ['question' => 'Как применить манифест?', 'options' => ['kubectl create', 'kubectl apply', 'kubectl run', 'kubectl start'], 'correct' => 'kubectl apply'],
                    ['question' => 'Для чего Service в K8s?', 'options' => ['Хранение данных', 'Доступ к Podам извне', 'Логирование', 'Мониторинг'], 'correct' => 'Доступ к Podам извне'],
                ],
            ],
            'TypeScript' => [
                'lessons' => [
                    ['title' => 'Типы данных', 'description' => 'TypeScript — JavaScript с статической типизацией. Базовые: string, number, boolean, null, undefined. Составные: number[] — массив, [string, number] — кортеж, enum Color — перечисление. any — отключает проверку (избегайте!), unknown — безопаснее (требует проверки). Интерфейс: interface User { id: number; name: string; email?: string; }. Type alias: type ID = string | number. Type inference — авто-вывод типа.', 'materials' => 'TypeScript Playground, TypeScript Handbook, Справочник по типам'],
                    ['title' => 'Интерфейсы и дженерики', 'description' => 'Дженерики — параметризация типов: function identity<T>(arg: T): T { return arg; }. interface ApiResponse<T> { data: T; status: number; }. Утилиты: Partial<T> — все опциональные, Required<T> — все обязательные, Pick<T, \"id\"> — выбирает поля, Omit<T, \"password\"> — исключает. Record<string, number> — тип объекта. Условные типы: T extends U ? X : Y. Readonly<T> — только для чтения.', 'materials' => 'TypeScript Generics, Utility Types, Conditional Types'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое interface?', 'options' => ['Класс', 'Описание структуры объекта', 'Функция', 'Переменная'], 'correct' => 'Описание структуры объекта'],
                    ['question' => 'Что делает <T> в дженерике?', 'options' => ['Создаёт массив', 'Задаёт тип параметр', 'Удаляет тип', 'Конвертирует тип'], 'correct' => 'Задаёт тип параметр'],
                    ['question' => 'Какой тип безопаснее any?', 'options' => ['void', 'never', 'unknown', 'object'], 'correct' => 'unknown'],
            ],
            ],
            'React' => [
                'lessons' => [
                    ['title' => 'Компоненты и JSX', 'description' => 'React — библиотека UI. Компоненты — функции, возвращающие JSX: const App = () => { return <div><h1>Привет</h1></div>; }. JSX — расширение JS: className вместо class, htmlFor вместо for. Пропсы — данные от родителя: <User name=\"Иван\" age={25} />. Дети: <Card><p>Текст</p></Card>. Virtual DOM — виртуальное DOM-дерево для эффективных обновлений (обновляются только изменившиеся элементы).', 'materials' => 'React Docs — документация, JSX в деталях, Virtual DOM — как работает'],
                    ['title' => 'Хуки', 'description' => 'Хуки — функции для состояния и других возможностей React. useState: const [count, setCount] = useState(0) — управление состоянием. useEffect: useEffect(() => { fetchData(); }, [deps]) — побочные эффекты (запросы API). useRef: const ref = useRef(null) — доступ к DOM. useContext — доступ к контексту. useCallback/memo — мемоизация. Правила: только на верхнем уровне, только в функциональных компонентах.', 'materials' => 'React Hooks — справочник, useState и useEffect, Правила хуков'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое JSX?', 'options' => ['Язык стилей', 'JavaScript XML', 'JSON расширение', 'Тип данных'], 'correct' => 'JavaScript XML'],
                    ['question' => 'Для чего useState?', 'options' => ['Эффекты', 'Состояние компонента', 'Роутинг', 'Формы'], 'correct' => 'Состояние компонента'],
                    ['question' => 'Когда вызывается useEffect?', 'options' => ['После рендера', 'До рендера', 'При клике', 'При загрузке страницы'], 'correct' => 'После рендера'],
            ],
            ],
            'Vue.js' => [
                'lessons' => [
                    ['title' => 'Основы Vue', 'description' => 'Vue.js — прогрессивный фреймворк. const app = Vue.createApp({ data() { return { message: \"Привет\" } } }).mount(\"#app\"). Директивы: v-bind:/ :attr — привязка атрибутов, v-on:/@event — события, v-model — двустороннее связывание, v-if/v-else-if/v-else — условный рендер, v-for — цикл. Реактивность — авто-обновление DOM. computed — вычисляемые свойства. watch — отслеживание изменений. Options vs Composition API.', 'materials' => 'Vue.js Guide — документация, Vue Mastery — курсы, Composition vs Options API'],
                    ['title' => 'Компоненты Vue', 'description' => 'Компоненты — переиспользуемые блоки UI (template, script, style в SFC). Props: props: { name: { type: String, required: true } }. Events: $emit(\"update\", data) — отправка родителю. Slots: <slot></slot>, именованные <slot name=\"header\">. Provide/Inject — передача данных глубоко в дерево. Жизненный цикл: beforeCreate → created → mounted → updated → destroyed. Vue Router — маршрутизация. Pinia — управление состоянием.', 'materials' => 'Vue Components — документация, Vue Router, Pinia — state management'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Vue?', 'options' => ['Фреймворк для UI', 'База данных', 'Текстовый редактор', 'Браузер'], 'correct' => 'Фреймворк для UI'],
                    ['question' => 'Как связать данные с шаблоном?', 'options' => ['v-bind', 'v-model', 'v-if', 'v-for'], 'correct' => 'v-model'],
                    ['question' => 'Что делает v-if?', 'options' => ['Цикл', 'Условный рендер', 'Привязка', 'Событие'], 'correct' => 'Условный рендер'],
            ],
            ],
            'Node.js' => [
                'lessons' => [
                    ['title' => 'Основы Node.js', 'description' => 'Node.js — серверный JavaScript (движок V8). Модули: require(\"fs\") — файлы, require(\"path\") — пути, require(\"events\") — события. npm — менеджер пакетов. Event Loop — асинхронный неблокирующий ввод-вывод. CommonJS: const fs = require(\"fs\"). ES Modules: import fs from \"fs\" (\"type\": \"module\" в package.json). __dirname, __filename, process — глобальные объекты. Node.js подходит для API, реалтайм-приложений, микросервисов.', 'materials' => 'Node.js Docs, Node.js guides, npm Docs'],
                    ['title' => 'Express.js', 'description' => 'Express.js — минималистичный фреймворк для Node.js. const app = express(); app.get(\"/\", (req, res) => res.send(\"Привет\")); app.listen(3000). Маршруты: app.get/post/put/delete(path, handler). Middleware: app.use(express.json()) — парсинг JSON, app.use(cors()) — CORS. req.params — параметры (/user/:id), req.query — query (?page=1), req.body — тело. Router — модульная маршрутизация. Error handling: (err, req, res, next).', 'materials' => 'Express.js Guide, Middleware в Express, Express Router'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Node.js?', 'options' => ['Браузер', 'Среда выполнения JS на сервере', 'База данных', 'Фреймворк'], 'correct' => 'Среда выполнения JS на сервере'],
                    ['question' => 'Как создать сервер в Express?', 'options' => ['app.listen()', 'app.run()', 'app.start()', 'app.serve()'], 'correct' => 'app.listen()'],
                    ['question' => 'Что делает middleware?', 'options' => ['Обрабатывает запрос до маршрута', 'Создаёт базу данных', 'Генерирует HTML', 'Управляет сессиями'], 'correct' => 'Обрабатывает запрос до маршрута'],
            ],
            ],
            'REST API' => [
                'lessons' => [
                    ['title' => 'Проектирование API', 'description' => 'REST API — обмен данными клиент-сервер. URL: /api/users — коллекция, /api/users/123 — ресурс. Методы: GET (получить), POST (создать), PUT (замена), PATCH (обновление), DELETE (удалить). Пагинация: ?page=2&limit=20 или курсорная. Фильтрация: ?status=active&sort=-created_at. Формат: { \"data\": [...], \"meta\": { \"total\": 100 } }. Валидация входных данных. Версионирование: /api/v1/users.', 'materials' => 'RESTful API Design, JSON:API, Postman — тестирование API'],
                    ['title' => 'Документация API', 'description' => 'OpenAPI (Swagger) — стандарт документации: описание эндпоинтов, параметров, ответов. Автогенерация из аннотаций. Postman — тестирование: коллекции, среды, автоматизация. API-ключи — простая аутентификация (X-API-Key). Rate Limiting — ограничение запросов для предотвращения злоупотреблений. CORS — кросс-доменные запросы. Версионирование: /api/v1/users. Логирование и мониторинг API-запросов.', 'materials' => 'OpenAPI Specification, Postman Learning Center, Rate Limiting — практики'],
                ],
                'quizzes' => [
                    ['question' => 'Какой URL для получения списка пользователей?', 'options' => ['/getUsers', '/api/users', '/users/list', '/fetch/users'], 'correct' => '/api/users'],
                    ['question' => 'Какой статус при успешном создании?', 'options' => ['200', '201', '204', '301'], 'correct' => '201'],
                    ['question' => 'Что такое rate limiting?', 'options' => ['Лимит памяти', 'Ограничение количества запросов', 'Размер файла', 'Время ответа'], 'correct' => 'Ограничение количества запросов'],
                ],
            ],
            'Адаптивный дизайн' => [
                'lessons' => [
                    ['title' => 'Медиа-запросы', 'description' => 'Адаптивный дизайн — подстройка под разные экраны. @media (max-width: 768px) { /* стили */ }. Mobile-first: сначала мобильные, потом min-width для планшетов/десктопов. Breakpoints: 320px — телефон, 768px — планшет, 1024px — десктоп, 1440px — большой экран. viewport: <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">.', 'materials' => 'MDN: Responsive Design, Google: Responsive Basics, Breakpoints — стандарты'],
                    ['title' => 'Адаптивные изображения', 'description' => 'srcset — разные версии для разных экранов: <img srcset=\"small.jpg 480w, large.jpg 1024w\">. picture — разные изображения для арт-дирекции. loading=\"lazy\" — отложенная загрузка при скролле. WebP — формат лучше JPG/PNG на 25-34%. object-fit: cover/contain — подгонка. max-width: 100% — предотвращение выхода за контейнер.', 'materials' => 'MDN: Responsive Images, WebP — преимущества, Lazy Loading'],
                ],
                'quizzes' => [
                    ['question' => 'Как задать стиль для экранов < 768px?', 'options' => ['@media (max-width: 768px)', '@screen (768px)', '@viewport (768px)', '@device (768px)'], 'correct' => '@media (max-width: 768px)'],
                    ['question' => 'Что такое mobile-first?', 'options' => ['Десктоп сначала', 'Мобильные стили сначала', 'Только мобильные', 'Нет стилей'], 'correct' => 'Мобильные стили сначала'],
                    ['question' => 'Что делает loading="lazy"?', 'options' => ['Отложенная загрузка', 'Кэширование', 'Сжатие', 'Удаление'], 'correct' => 'Отложенная загрузка'],
                ],
            ],
            'Доступность (a11y)' => [
                'lessons' => [
                    ['title' => 'Основы a11y', 'description' => 'Доступность (a11y) — интерфейсы для всех, включая людей с инвалидностью. WCAG: воспринимаемость, работоспособность, понятность, надёжность. ARIA: role=\"button\", aria-label, aria-hidden — семантика для скринридеров. Клавиатурная навигация: Tab, Enter, Escape — все элементы доступны. Контрастность: минимум 4.5:1 для текста. Alt-тексты для изображений обязательны. Тестирование: Lighthouse, axe, NVDA/VoiceOver.', 'materials' => 'WCAG 2.1, WAI-ARIA, A11y Project, WebAIM'],
                    ['title' => 'Тестирование доступности', 'description' => 'Lighthouse — автоматический аудит a11y в Chrome DevTools (вкладка "Lighthouse", категория "Доступность"). axe DevTools — расширение для обнаружения проблем с доступностью в реальном времени. Контрастность: минимум 4.5:1 для обычного текста, 3:1 для крупного (18px+). NVDA (Windows) и VoiceOver (macOS) — скринридеры для проверки, как интерфейс звучит для незрячих пользователей. Семантические теги (button вместо div с onclick) обеспечивают правильную роль для скринридеров.', 'materials' => 'Lighthouse a11y, axe DevTools, Контрастность WCAG, NVDA — скачивание'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое ARIA?', 'options' => ['Язык стилей', 'Расширение доступности', 'База данных', 'Фреймворк'], 'correct' => 'Расширение доступности'],
                    ['question' => 'Зачем нужен alt у изображений?', 'options' => ['SEO', 'Для скринридеров', 'Кэширование', 'Сжатие'], 'correct' => 'Для скринридеров'],
                    ['question' => 'Какой инструмент проверяет a11y?', 'options' => ['ESLint', 'Lighthouse', 'Webpack', 'Babel'], 'correct' => 'Lighthouse'],
                ],
            ],
            'JavaScript Основы' => [
                'lessons' => [
                    ['title' => 'Переменные и типы', 'description' => 'let — переменная с блочной областью видимости (можно менять). const — константа (нельзя переприсвоить, но объекты/массивы мутируются). var — устаревший вариант (function scope, hoisting). Примитивы: string ("привет"), number (42, 3.14, NaN), boolean (true/false), null (намеренное отсутствие), undefined (не задано), symbol (уникальный ID), bigint (большие числа). Операторы: + - * / %, === (строгое равенство, без приведения типов) vs == (с приведением), && || !, тернарный (условие ? да : нет). typeof определяет тип: typeof 42 === "number".', 'materials' => 'MDN: Типы данных, type coercion, Strict vs Loose Equality'],
                    ['title' => 'Функции', 'description' => 'Function Declaration — поднимается (hoisting), можно вызвать до объявления: function greet(name) { return `Привет, ${name}`; }. Function Expression — не поднимается: const greet = function(name) { ... }. Arrow Function — короткий синтаксис: const greet = (name) => `Привет, ${name}`. Closure — функция запоминает переменные из внешней области: function counter() { let count = 0; return () => ++count; }. Scope — область видимости: global, function, block. IIFE — немедленный вызов: (() => { ... })().', 'materials' => 'Arrow Functions, Closures — объяснение, Scope — типы, IIFE — паттерн'],
                ],
                'quizzes' => [
                    ['question' => 'Чем let отличается от var?', 'options' => ['Ничем', 'Block scope', 'Function scope', 'Global scope'], 'correct' => 'Block scope'],
                    ['question' => 'Что такое closure?', 'options' => ['Закрытие окна', 'Функция + её переменные', 'Цикл', 'Объект'], 'correct' => 'Функция + её переменные'],
                    ['question' => 'Что вернёт typeof null?', 'options' => ['null', 'undefined', 'object', 'boolean'], 'correct' => 'object'],
                ],
            ],
            'Асинхронный JS' => [
                'lessons' => [
                    ['title' => 'Promises', 'description' => 'Promise — объект, представляющий результат асинхронной операции. Три состояния: pending (выполняется), fulfilled (успех), rejected (ошибка). Создание: new Promise((resolve, reject) => { ... resolve(data); }). Цепочка: promise.then(data => ...).catch(err => ...).finally(() => ...) — выполнится всегда. Promise.all([p1, p2]) — все промисы параллельно, возвращает массив результатов. Promise.race — первый завершённый. Promise.allSettled — все результаты (включая ошибки). Промисификация: util.promisify(convertCallback).', 'materials' => 'MDN: Promise, Промисификация, Promise.all vs Promise.allSettled'],
                    ['title' => 'async/await', 'description' => 'async/await — синтаксический сахар над промисами. async функция всегда возвращает промис. await — приостанавливает выполнение до завершения промиса: const data = await fetch(url).then(r => r.json()). Обработка ошибок: try/catch: try { const res = await fetch(url); } catch (err) { console.error(err); }. Параллельные запросы: Promise.all([fetch(url1), fetch(url2)]). Не используйте await в цикле for — используйте Promise.all для параллелизации. async/await делает асинхронный код читаемым как синхронный.', 'materials' => 'Async/Await — гайд, Try/Catch с async, Параллельные запросы'],
                ],
                'quizzes' => [
                    ['question' => 'Что делает await?', 'options' => ['Ждёт результат промиса', 'Создаёт промис', 'Удаляет промис', 'Кэширует'], 'correct' => 'Ждёт результат промиса'],
                    ['question' => 'Что такое Promise.all?', 'options' => ['Выполняет все промисы параллельно', 'Выполняет по очереди', 'Удаляет промисы', 'Кэширует'], 'correct' => 'Выполняет все промисы параллельно'],
                    ['question' => 'Где нельзя использовать await?', 'options' => ['В async функции', 'В обычной функции', 'В forEach', 'Both B and C'], 'correct' => 'Both B and C'],
                ],
            ],
            'ES6+ Фичи' => [
                'lessons' => [
                    ['title' => 'Деструктуризация и spread', 'description' => 'Деструктуризация — извлечение значений из массивов/объектов: const [a, b] = [1, 2]; const { name, age } = user. Значения по умолчанию: const { role = "user" } = user. Spread (...) — распаковка: const arr2 = [...arr1]; const obj2 = { ...obj1, extra: true }. Rest — сбор оставшихся: const [first, ...rest] = arr; function fn({ id, ...data }). Template literals — строки с интерполяцией: `Привет, ${name}!`. Shorthand: { name, age } вместо { name: name, age: age }.', 'materials' => 'MDN: Деструктуризация, Spread syntax, Rest parameters'],
                    ['title' => 'Модули и итераторы', 'description' => 'ES Modules — система импорта/экспорта: export const PI = 3.14; export default function() { ... }. import { PI } from "./math.js"; import calc from "./calc.js". Named exports — именованные, default — основной. Dynamic import(): const mod = await import("./lazy.js") — ленивая загрузка. Symbol — уникальные идентификаторы (Symbol.iterator). Iterator protocol — объект с методом next(), возвращающий { value, done }. for...of — перебор итерируемых объектов (массивы, строки, Map, Set).', 'materials' => 'ES Modules — гайд, Symbol — документация, Iterator protocol, for...of vs for...in'],
                ],
                'quizzes' => [
                    ['question' => 'Что делает ... spread?', 'options' => ['Распаковывает массив/объект', 'Создаёт массив', 'Удаляет элемент', 'Сортирует'], 'correct' => 'Распаковывает массив/объект'],
                    ['question' => 'Как импортировать модуль?', 'options' => ['require()', 'import', 'include', 'load'], 'correct' => 'import'],
                    ['question' => 'Что такое template literal?', 'options' => ['Обычная строка', 'Строка с ${} интерполяцией', 'Регулярное выражение', 'Комментарий'], 'correct' => 'Строка с ${} интерполяцией'],
                ],
            ],
            'CSS Анимации' => [
                'lessons' => [
                    ['title' => 'Transition', 'description' => 'CSS transitions — плавное изменение свойств при их изменении. transition-property: какое свойство анимировать (background-color, transform, opacity, all). transition-duration: 0.3s — время анимации. transition-timing-function: ease (медленнее к концу), linear (равномерно), ease-in (медленнее в начале), ease-out (медленнее в конце), cubic-bezier() — кастомная кривая. transition-delay: 0.1s — задержка перед стартом. Компактная запись: transition: background-color 0.3s ease 0.1s. Переход срабатывает при hover, focus, классе, JS.', 'materials' => 'MDN: CSS Transitions, Timing functions — визуализация, cubic-bezier.com'],
                    ['title' => 'Animation и keyframes', 'description' => '@keyframes — определение промежуточных состояний: @keyframes slide { from { transform: translateX(-100%); } to { transform: translateX(0); } }. animation-name: slide; animation-duration: 0.5s; animation-iteration-count: infinite; animation-direction: alternate; animation-fill-mode: forwards. transform: rotate(45deg) scale(1.2) translateX(50px) — вращение, масштаб, сдвиг. Transform-origin — точка вращения. will-change: transform — подсказка браузеру для оптимизации.', 'materials' => 'MDN: CSS Animations, Animate.css — библиотека, Transform — справочник'],
                ],
                'quizzes' => [
                    ['question' => 'Что делает transition?', 'options' => ['Анимирует изменение свойств', 'Создаёт ключевые кадры', 'Удаляет элемент', 'Изменяет DOM'], 'correct' => 'Анимирует изменение свойств'],
                    ['question' => 'Как задать бесконечную анимацию?', 'options' => ['animation-iteration-count: infinite', 'animation-loop: true', 'animation: forever', 'repeat: infinite'], 'correct' => 'animation-iteration-count: infinite'],
                    ['question' => 'Что такое @keyframes?', 'options' => ['Функция', 'Определение промежуточных состояний', 'Стиль', 'Селектор'], 'correct' => 'Определение промежуточных состояний'],
                ],
            ],
            'Препроцессоры (Sass)' => [
                'lessons' => [
                    ['title' => 'Переменные и вложенность', 'description' => 'Sass/SCSS — расширения CSS с переменными, вложенностью и функциями. Переменные: $primary: #38bdf8; $spacing: 16px. Вложенность: .nav { .item { &:hover { color: $primary; } } } — & ссылается на родителя (.nav .item:hover). @import или @use для модульности. Интерполяция: #{$variable}-suffix. Расширение Sass: математика ($w * 2), функции darken()/lighten(),条件ная логика @if/@else. SCSS — синтаксис с точкой с запятой (совместим с CSS).', 'materials' => 'Sass — документация, Sass vs SCSS, Migration guide'],
                    ['title' => 'Миксины и функции', 'description' => '@mixin — переиспользуемый блок стилей: @mixin flex-center { display: flex; justify-content: center; align-items: center; }. Использование: @include flex-center;. @extend — наследование: .button-primary { @extend .button; background: blue; }. @function — возврат значения: @function spacing($n) { @return $n * 8px; }. Условная логика: @if $type == "primary" { ... } @else { ... }. Циклы @for, @each, @while для генерации повторяющихся стилей.', 'materials' => 'Sass @mixin, Sass @extend vs @mixin, Sass Functions'],
                ],
                'quizzes' => [
                    ['question' => 'Как объявить переменную в Sass?', 'options' => ['--var', '$var', '@var', 'var()'], 'correct' => '$var'],
                    ['question' => 'Что делает @mixin?', 'options' => ['Создаёт переиспользуемый блок стилей', 'Импортирует файл', 'Удаляет стили', 'Генерирует CSS'], 'correct' => 'Создаёт переиспользуемый блок стилей'],
                    ['question' => 'Что такое & в Sass?', 'options' => ['Родительский селектор', 'Все элементы', 'Новый селектор', 'Комментарий'], 'correct' => 'Родительский селектор'],
                ],
            ],
            'TypeScript' => [
                'lessons' => [
                    ['title' => 'Типы и интерфейсы', 'description' => 'TypeScript — JavaScript с статической типизацией (ловит ошибки до запуска). Базовые типы: string, number, boolean, null, undefined. Составные: number[] — массив, [string, number] — кортеж, enum Color { Red, Blue } — перечисление. any — отключает проверку (избегайте!), unknown — безопаснее (требует проверки типа). Interface: interface User { id: number; name: string; email?: string; } — описывает структуру объекта. Type alias: type ID = string | number — объединение типов. Type inference — TypeScript сам выводит тип.', 'materials' => 'TypeScript Playground, TypeScript Handbook, Any vs Unknown'],
                    ['title' => 'Дженерики и утилиты', 'description' => 'Дженерики — параметризация типов (как дженерический тип в Java): function identity<T>(arg: T): T { return arg; }. interface ApiResponse<T> { data: T; status: number; } — ответ API с любым типом данных. Утилиты: Partial<T> — все поля опциональные, Required<T> — все обязательные, Pick<User, "id" | "name"> — выбирает поля, Omit<User, "password"> — исключает. Record<string, number> — объект с фиксированными типами ключей и значений. Условные типы: T extends U ? X : Y.', 'materials' => 'TypeScript Generics, Utility Types, Conditional Types'],
                ],
                'quizzes' => [
                    ['question' => 'Зачем нужен TypeScript?', 'options' => ['Ускорение кода', 'Статическая типизация', 'Кэширование', 'Компиляция'], 'correct' => 'Статическая типизация'],
                    ['question' => 'Что такое interface?', 'options' => ['Класс', 'Описание структуры', 'Функция', 'Переменная'], 'correct' => 'Описание структуры'],
                    ['question' => 'Что делает Partial<T>?', 'options' => ['Делает все поля обязательными', 'Делает все поля опциональными', 'Удаляет тип', 'Конвертирует тип'], 'correct' => 'Делает все поля опциональными'],
                ],
            ],
            'React Router' => [
                'lessons' => [
                    ['title' => 'Маршрутизация', 'description' => 'React Router — клиентская маршрутизация для SPA. BrowserRouter — обёртка для маршрутизации: <BrowserRouter><Routes><Route path="/" element={<Home />} /><Route path="/about" element={<About />} /></Routes></BrowserRouter>. Link — навигация без перезагрузки: <Link to="/about">О нас</Link>. NavLink — Link с активным стилем (className={({isActive}) => isActive ? "active" : ""}). Navigate — программный редирект: <Navigate to="/login" replace />. Вложенные маршруты: <Route path="/users/:id" element={<UserProfile />}> с вложенными Route.', 'materials' => 'React Router v6 — документация, Параметры маршрутов, Вложенные маршруты'],
                    ['title' => 'Навигация', 'description' => 'useNavigate — программная навигация: const navigate = useNavigate(); navigate("/about"); navigate(-1) — назад. useParams — чтение параметров URL: const { id } = useParams();. useLocation — текущий location: location.pathname, location.search. useSearchParams — работа с query-параметрами: const [params, setParams] = useSearchParams(); params.get("page"). Outlet — рендеринг дочерних маршрутов: <Outlet />. Lazy routes: lazy={() => import("./About")}. Error boundaries для 404.', 'materials' => 'useNavigate, useParams, useLocation, Outlet — паттерны'],
                ],
                'quizzes' => [
                    ['question' => 'Как объявить маршрут?', 'options' => ['<Route path="/" element={<Home />} />', '<Link to="/" />', '<Navigate to="/" />', '<Redirect to="/" />'], 'correct' => '<Route path="/" element={<Home />} />'],
                    ['question' => 'Как перейти программно?', 'options' => ['window.location', 'useNavigate()', 'history.push()', 'navigate()'], 'correct' => 'useNavigate()'],
                    ['question' => 'Что такое Outlet?', 'options' => ['Дочерние маршруты', 'Навигация', 'Футер', 'Хедер'], 'correct' => 'Дочерние маршруты'],
                ],
            ],
            'State Management' => [
                'lessons' => [
                    ['title' => 'Context API', 'description' => 'React Context — передача данных через дерево компонентов без пропс-дриллинга. const ThemeContext = createContext("light"); — создание контекста. Provider — оборачивает дерево: <ThemeContext.Provider value={theme}>...</ThemeContext.Provider>. useContext(ThemeContext) — чтение в дочернем компоненте. Подходит для тем, языка, текущего пользователя (глобальные данные). Не подходит для часто меняющихся данных (перерендеривает всех потребителей). Redux / Zustand — альтернативы для сложного состояния.', 'materials' => 'React Context — документация, Когда использовать Context, Паттерны Context'],
                    ['title' => 'Redux и Zustand', 'description' => 'Redux — предсказуемое хранилище состояния. Store — единый источник правды. Actions — объекты { type: "INCREMENT", payload: 5 }. Reducers — чистые функции: (state, action) => newState. dispatch(action) — отправка действия. Selectors — извлечение данных из store. Redux Toolkit — современный Redux (createSlice, createAsyncThunk). Zustand — минималистичная альтернатива: const useStore = create((set) => ({ count: 0, inc: () => set(s => ({count: s.count+1})) })). Меньше бойлерплейта.', 'materials' => 'Redux Toolkit — документация, Zustand — гайд, Redux vs Zustand'],
                ],
                'quizzes' => [
                    ['question' => 'Когда использовать Context?', 'options' => ['Для всего', 'Для глобальных данных', 'Для форм', 'Для стилей'], 'correct' => 'Для глобальных данных'],
                    ['question' => 'Что такое store в Redux?', 'options' => ['Магазин', 'Глобальное состояние приложения', 'База данных', 'Кэш'], 'correct' => 'Глобальное состояние приложения'],
                    ['question' => 'Как изменить состояние в Zustand?', 'options' => ['setState()', 'useStore()', 'dispatch()', 'update()'], 'correct' => 'useStore()'],
                ],
            ],
            'Next.js / Nuxt' => [
                'lessons' => [
                    ['title' => 'SSR и SSG', 'description' => 'SSR (Server-Side Rendering) — рендеринг HTML на сервере при каждом запросе (getServerSideProps). Подходит для динамических данных (новости, профили). SSG (Static Site Generation) — генерация HTML при сборке (getStaticProps). Быстрая загрузка, но данные могут устареть. ISR (Incremental Static Regeneration) — гибрид: статика + обновление каждые N секунд (revalidate: 60). CSR (Client-Side Rendering) — рендеринг в браузере (обычный React). Next.js поддерживает все три подхода. Nuxt 3 использует Vue с аналогичными концепциями.', 'materials' => 'Next.js: SSR vs SSG, ISR — документация, Nuxt 3 rendering modes'],
                    ['title' => 'Файловая маршрутизация', 'description' => 'Next.js App Router (pages/ или app/): файл pages/about.js → маршрут /about. Динамические: pages/[id].js → /users/123. Вложенные: pages/dashboard/settings.js → /dashboard/settings. layout.js — общая обёртка (header/footer). loading.js — скелетон при загрузке. error.js — обработка ошибок. Server Components — компоненты без "use client" работают на сервере (нет JS на клиенте). API Routes: pages/api/users.js → /api/users.', 'materials' => 'Next.js App Router, File-based routing, Server Components'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое SSR?', 'options' => ['Рендеринг на сервере', 'Рендеринг на клиенте', 'Статика', 'Кэширование'], 'correct' => 'Рендеринг на сервере'],
                    ['question' => 'Какой фреймворк использует файловую маршрутизацию?', 'options' => ['React (обычный)', 'Next.js', 'Vue (обычный)', 'Angular'], 'correct' => 'Next.js'],
                    ['question' => 'Что такое ISR?', 'options' => ['Incremental Static Regeneration', 'International Server Routing', 'Internal State Reset', 'Inline Script Rendering'], 'correct' => 'Incremental Static Regeneration'],
                ],
            ],
            'Тестирование' => [
                'lessons' => [
                    ['title' => 'Unit тесты', 'description' => 'Unit-тесты — тестирование отдельных функций/компонентов в изоляции. Jest — тестовый фреймворк (describe() — группа, it()/test() — тест, expect() — проверка). AAA Pattern: Arrange (подготовка), Act (действие), Assert (проверка). Моки: jest.fn() — поддельная функция, jest.mock() — мок модуля. Spy: jest.spyOn(obj, "method") — отслеживание вызовов. Vitest — быстрая альтернатива (Vite-based, совместим с Jest API). Тесты должны быть быстрыми, изолированными и воспроизводимыми.', 'materials' => 'Jest — документация, Vitest — гайд, Testing patterns'],
                    ['title' => 'Интеграционные тесты', 'description' => 'Testing Library — тестирование React-компонентов: render(<App />), screen.getByRole("button"), fireEvent.click(). userEvent — более реалистичные события пользователя. Cypress — E2E тестирование в браузере: cy.visit("/"), cy.get("input").type("hello"), cy.get("button").click(). Playwright — кросс-браузерные E2E тесты (Chromium, Firefox, WebKit). Компонентные тесты: тестирование отдельных компонентов с реальными зависимостями.', 'materials' => 'Testing Library — документация, Cypress — гайд, Playwright — начало работы'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое unit test?', 'options' => ['Тестирование модуля', 'Тестирование UI', 'Тестирование БД', 'Тестирование сети'], 'correct' => 'Тестирование модуля'],
                    ['question' => 'Какой инструмент для E2E тестов?', 'options' => ['Jest', 'Cypress', 'ESLint', 'Webpack'], 'correct' => 'Cypress'],
                    ['question' => 'Что делает describe()?', 'options' => ['Описывает тест', 'Группирует тесты', 'Запускает тест', 'Удаляет тест'], 'correct' => 'Группирует тесты'],
                ],
            ],
            'Build Tools' => [
                'lessons' => [
                    ['title' => 'Webpack и Vite', 'description' => 'Webpack — bundler, собирающий модули в файлы. Entry: точка входа (index.js). Output: куда собирать (dist/bundle.js). Loaders: обработка файлов (babel-loader для JS, css-loader для CSS, file-loader для изображений). Plugins: MiniCssExtractPlugin, HtmlWebpackPlugin. HMR (Hot Module Replacement) — горячая замена без перезагрузки. Vite — быстрый bundler (ESBuild для dev, Rollup для prod). HMR мгновенный. Proxy для API. Vite быстрее Webpack в 10-100 раз.', 'materials' => 'Webpack — документация, Vite — гайд, Сравнение Webpack vs Vite'],
                    ['title' => 'Babel и SWC', 'description' => 'Babel — транспилирование Modern JS → Legacy (ES6→ES5). @babel/preset-env — автоматическое определение нужных полифилов. .babelrc: { "presets": ["@babel/preset-env", "@babel/preset-react"] }. Browserslist: > 1%, last 2 versions — целевые браузеры. SWC — Rust-based транспилировщик (в 20-70 раз быстрее Babel). Используется в Next.js, Vite, Parcel. polyfill.io — загрузка полифилов для старых браузеров.', 'materials' => 'Babel — документация, SWC — гайд, Browserslist — настройка'],
                ],
                'quizzes' => [
                    ['question' => 'Что делает bundler?', 'options' => ['Объединяет модули в файлы', 'Тестирует код', 'Линтует код', 'Деплойит'], 'correct' => 'Объединяет модули в файлы'],
                    ['question' => 'Какой bundler быстрее?', 'options' => ['Webpack', 'Vite', 'Rollup', 'Parcel'], 'correct' => 'Vite'],
                    ['question' => 'Что такое HMR?', 'options' => ['Hot Module Replacement', 'HTTP Module Request', 'High Memory Runtime', 'Hybrid Model Rendering'], 'correct' => 'Hot Module Replacement'],
                ],
            ],
            'Performance' => [
                'lessons' => [
                    ['title' => 'Метрики производительности', 'description' => 'Core Web Vitals — ключевые метрики Google: LCP (Largest Contentful Paint) — время загрузки основного контента (< 2.5с). FID (First Input Delay) — задержка первого взаимодействия (< 100мс). CLS (Cumulative Layout Shift) — визуальная стабильность (< 0.1). INP (Interaction to Next Paint) — отклик на взаимодействия. Lighthouse — автоматический аудит (оценки 0-100). Performance API — замеры: performance.mark(), performance.measure(). Navigation Timing API — время загрузки страницы.', 'materials' => 'Core Web Vitals — документация, Lighthouse scoring, Performance API'],
                    ['title' => 'Оптимизация', 'description' => 'Lazy loading — отложенная загрузка: <img loading="lazy">, React.lazy() для компонентов. Code splitting — разделение кода по маршрутам: dynamic import(). CDN — доставка статики с ближайшего сервера. Кэширование: Cache-Control, ETag, Service Workers. Изображения: WebP (на 25% меньше JPG), srcset для разных экранов, сжатие через imagemin. Prefetch: <link rel="prefetch"> — предзагрузка следующей страницы. Preload: <link rel="preload"> — приоритетная загрузка критических ресурсов.', 'materials' => 'Web Performance — гайд, Lazy loading, CDN — как работает, Service Workers'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое LCP?', 'options' => ['Largest Contentful Paint', 'Lowest Connection Point', 'Load Complete Page', 'Last CSS Property'], 'correct' => 'Largest Contentful Paint'],
                    ['question' => 'Что такоеCLS?', 'options' => ['Cumulative Layout Shift', 'CSS Layout System', 'Central Loading Screen', 'Component Layout Structure'], 'correct' => 'Cumulative Layout Shift'],
                    ['question' => 'Как ускорить загрузку изображений?', 'options' => ['Увеличить размер', 'Lazy loading + WebP', 'Убрать alt', 'Без стилей'], 'correct' => 'Lazy loading + WebP'],
            ],
            ],
            'Web Security' => [
                'lessons' => [
                    ['title' => 'Основы безопасности', 'description' => 'XSS (Cross-Site Scripting) — внедрение вредоносного JS в страницы. Защита: sanitize ввод (DOMPurify), CSP (Content Security Policy) — разрешённые источники скриптов. CSRF (Cross-Site Request Forgery) — поддельные запросы от имени пользователя. Защита: CSRF-токены в формах, SameSite cookie. SQL Injection — внедрение SQL-кода в запросы. Защита: prepared statements (PDO, Eloquent). OWASP Top 10 — список самых частых уязвимостей. CORS — политика кросс-доменных запросов (Access-Control-Allow-Origin).', 'materials' => 'OWASP Top 10, XSS — типы и защита, CSRF — объяснение, CSP — настройка'],
                    ['title' => 'Безопасная аутентификация', 'description' => 'JWT (JSON Web Token) — компактный токен для авторизации: header.payload.signature. Хранение: HttpOnly cookie (безопаснее) vs localStorage. OAuth 2.0 — делегированная авторизация (Google, GitHub). HTTPS — шифрование трафика (обязательно для прода). Пароли: bcrypt/argon2 — медленное хеширование (никогда SHA-1/MD5). MFA (Multi-Factor Authentication) — второй фактор (TOTP, SMS). Ротация токенов и refresh tokens для безопасности сессий.', 'materials' => 'JWT — как работает, OAuth 2.0 — гайд, bcrypt vsargon2, HTTPS — зачем нужен'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое XSS?', 'options' => ['Cross-Site Scripting', 'External Style Sheet', 'XML Schema Standard', 'Cross Server Search'], 'correct' => 'Cross-Site Scripting'],
                    ['question' => 'Как защититься от SQL injection?', 'options' => ['Проверка длины', 'Prepared statements', 'Экранирование', 'Both B and C'], 'correct' => 'Both B and C'],
                    ['question' => 'Что такое CORS?', 'options' => ['Cross-Origin Resource Sharing', 'Central Origin Request System', 'Cross OS Runtime', 'Cache Origin Response'], 'correct' => 'Cross-Origin Resource Sharing'],
                ],
            ],
            'SEO Basics' => [
                'lessons' => [
                    ['title' => 'SEO оптимизация', 'description' => 'SEO (Search Engine Optimization) — оптимизация для поисковых систем. Meta теги: title (заголовок в поиске, 50-60 символов), description (описание, 150-160 символов), keywords (устарел). Canonical URL: <link rel="canonical"> — дубликаты страниц (避免惩罚). Open Graph: og:title, og:image, og:description — превью в соцсетях. Sitemap.xml — список страниц для индексации. Robots.txt — разрешения/запреты для ботов. Структура URL: /blog/my-article — чистые, без ?id=123.', 'materials' => 'Google SEO — гайд, Meta теги — справочник, Open Graph — протокол'],
                    ['title' => 'Технический SEO', 'description' => 'Schema.org — структурированные данные (JSON-LD): Product, Article, FAQ — расширенные сниппеты в Google. Hreflang — языковые версии (hreflang="ru"). Canonical — предотвращение дублирования контента. Core Web Vitals — влияют на ранжирование. Mobile-first indexing — Google индексирует мобильную версию. AMP — упрощённые страницы для мобильных (устаревает). Google Search Console — мониторинг индексации, ошибок, позиций. PageSpeed Insights — проверка скорости.', 'materials' => 'Schema.org — документация, Google Search Console, hreflang — настройка, AMP — за и против'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое meta description?', 'options' => ['Описание для поисковиков', 'Описание для людей', 'Комментарий', 'Заголовок'], 'correct' => 'Описание для поисковиков'],
                    ['question' => 'Зачем нужен sitemap.xml?', 'options' => ['Для стилей', 'Для индексации страниц', 'Для скриптов', 'Для шрифтов'], 'correct' => 'Для индексации страниц'],
                    ['question' => 'Что такое canonical URL?', 'options' => ['Основной URL страницы', 'Удалённый URL', 'Редирект', 'Кэш'], 'correct' => 'Основной URL страницы'],
                ],
            ],
            'PWA' => [
                'lessons' => [
                    ['title' => 'Progressive Web App', 'description' => 'PWA — веб-приложения с нативным опытом. Service Worker — фоновый скрипт, работающий даже когда страница закрыта. Кэширует ресурсы (CSS, JS, изображения) для мгновенной загрузки и работы оффлайн. Манифест (manifest.json) — описывает приложение (имя, иконки, фоновый цвет, start_url). Регистрация: navigator.serviceWorker.register("/sw.js"). Стратегии кэширования: Cache-first (кэш → сеть), Network-first (сеть → кэш), Stale-while-revalidate. Workbox — библиотека Google для управления кэшированием.', 'materials' => 'PWA — документация, Service Workers — гайд, Workbox, Manifest.json — поле'],
                    ['title' => 'Push уведомления', 'description' => 'Push API — доставка уведомлений когда приложение закрыто. Подписка: registration.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: vapidKey }). Notification API: new Notification("Заголовок", { body: "Текст", icon: "/icon.png" }). Firebase Cloud Messaging (FCM) — серверная часть для отправки push. VAPID ключи — аутентификация push-сервера. Обработка клика: notification.onclick — открытие страницы. Важно: запрос разрешения при первом использовании.', 'materials' => 'Push API — документация, Notification API, FCM — настройка, VAPID ключи'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Service Worker?', 'options' => ['Фоновый скрипт', 'Веб-воркер', 'Расширение', 'Плагин'], 'correct' => 'Фоновый скрипт'],
                    ['question' => 'Зачем нужен манифест PWA?', 'options' => ['Для стилей', 'Описывает приложение', 'Для тестов', 'Для логов'], 'correct' => 'Описывает приложение'],
                    ['question' => 'Что такое cache-first стратегия?', 'options' => ['Сначала кэш, потом сеть', 'Сначала сеть, потом кэш', 'Только кэш', 'Только сеть'], 'correct' => 'Сначала кэш, потом сеть'],
                ],
            ],
            'CSS-in-JS' => [
                'lessons' => [
                    ['title' => 'Styled Components', 'description' => 'CSS-in-JS — стилизация через JavaScript. Styled Components: const Button = styled.button`background: blue; color: white; &:hover { background: darkblue; }`. Динамические стили: styled.button`background: ${props => props.primary ? "blue" : "gray"}`. Глобальные стили: createGlobalStyle`body { margin: 0; }`. Автоматические уникальные классы (без конфликтов). TypeScript поддержка. Альтернативы: Emotion (performance), Stitches (tokens). Кэширование стилей для оптимизации.', 'materials' => 'Styled Components — документация, Emotion — гайд, Сравнение CSS-in-JS библиотек'],
                    ['title' => 'Tailwind CSS', 'description' => 'Tailwind — utility-first CSS (классы вместо кастомных стилей). <div class="flex items-center gap-4 p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition">. Конфигурация: tailwind.config.js — тема, цвета, размеры. JIT (Just-In-Time) — генерация только нужных стилей. Кастомизация: extend: { colors: { primary: "#38bdf8" } }. Directive: @apply — композиция утилит. PostCSS — плагин для обработки. PurgeCSS — удаление неиспользуемых стилей в продакшене.', 'materials' => 'Tailwind CSS — документация, Tailwind Play — онлайн- Playground, PostCSS — гайд'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое CSS-in-JS?', 'options' => ['CSS в HTML', 'Стили в JavaScript', 'JS в CSS', 'Новый CSS'], 'correct' => 'Стили в JavaScript'],
                    ['question' => 'Что такое Tailwind?', 'options' => ['Фреймворк', 'Utility-first CSS', 'Препроцессор', 'Библиотека'], 'correct' => 'Utility-first CSS'],
                    ['question' => 'Как добавить hover в Tailwind?', 'options' => [':hover', 'hover:', '@hover', 'hover-'], 'correct' => 'hover:'],
                ],
            ],
            'Базы данных (Advanced)' => [
                'lessons' => [
                    ['title' => 'Оптимизация запросов', 'description' => 'Индексы — ускоряют поиск (B-tree для диапазонов, hash для точных совпадений). Составной индекс: INDEX(a, b) — для запросов по a и a+b. EXPLAIN показывает план выполнения (type: ALL = full scan = плохо). Avoid SELECT * — выбирайте только нужные колонки. N+1 проблема: 1 запрос на список + N запросов на关联 данные → eager loading (with()). Кэширование результатов запросов (Redis, file). Pagination —LIMIT/OFFSET vs курсорная пагинация.', 'materials' => 'EXPLAIN — разбор плана, Составные индексы, N+1 problem'],
                    ['title' => 'Транзакции и изоляция', 'description' => 'Транзакция — группа операций, выполняемых атомарно (все или ничего). ACID: Atomicity (атомарность), Consistency (согласованность), Isolation (изоляция), Durability (долговечность). Уровни изоляции: READ UNCOMMITTED (грязные чтения), READ COMMITTED (только зафиксированные), REPEATABLE READ (одинаковые чтения), SERIALIZABLE (последовательная). Deadlock — взаимная блокировка (два процесса ждут друг друга). Locking:悲观锁 (FOR UPDATE) vs 乐观锁 (version column).', 'materials' => 'ACID — объяснение, Уровни изоляции — сравнение, Deadlock — примеры'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое ACID?', 'options' => ['Atomicity, Consistency, Isolation, Durability', 'Алгоритмы, Классы, Интерфейсы, Данные', 'Автоматическое кэширование', 'Асинхронные вызовы'], 'correct' => 'Atomicity, Consistency, Isolation, Durability'],
                    ['question' => 'Что такое deadlock?', 'options' => ['Одновременное завершение', 'Взаимная блокировка потоков', 'Быстрое завершение', 'Блокировка памяти'], 'correct' => 'Взаимная блокировка потоков'],
                    ['question' => 'Какой уровень изоляции гарантирует отсутствие phantom reads?', 'options' => ['READ UNCOMMITTED', 'READ COMMITTED', 'REPEATABLE READ', 'SERIALIZABLE'], 'correct' => 'SERIALIZABLE'],
                ],
            ],
            'REST API (Laravel)' => [
                'lessons' => [
                    ['title' => 'API Resources', 'description' => "JsonResource — преобразование модели Eloquent в JSON: UserResource::make(\$user). Условные поля: \$this->when(\$request->user(), fn() => ['email' => \$this->email]). Resource Collection: UserResource::collection(\$users) — коллекция с пагинацией. Кастомизация: with() — мета-данные в ответе. Nested Resources: CommentResource::make(\$comment)->with('author'). Авторизация: \$this->when(\$user->can('view', \$resource)). Автоматическая пагинация: \$this->whenPaginated().", 'materials' => 'Laravel API Resources — документация, Conditional Fields, Nested Resources'],
                    ['title' => 'Формат ответов', 'description' => 'Стандартный формат: { "data": [...], "meta": { "current_page": 1, "last_page": 5 } }. Пагинация: paginate(20) — постраничная, simplePaginate() — без подсчёта, cursorPaginate() — курсорная (для бесконечного скролла). Envelope format: { "success": true, "data": {...}, "message": "..." }. HTTP-коды: 200 OK, 201 Created, 204 No Content, 400 Bad Request, 401 Unauthorized, 403 Forbidden, 404 Not Found, 422 Validation Error, 500 Server Error. API Resources автоматически оборачивают данные в { "data": ... }.', 'materials' => 'Pagination — типы, HTTP Status Codes, Envelope format — практики'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое API Resource в Laravel?', 'options' => ['Модель данных', 'Преобразователь модели в JSON', 'Маршрут', 'Контроллер'], 'correct' => 'Преобразователь модели в JSON'],
                    ['question' => 'Какой HTTP-код означает успешный ответ без тела?', 'options' => ['200', '201', '204', '304'], 'correct' => '204'],
                    ['question' => 'Какой метод пагинации использует курсор?', 'options' => ['->paginate()', '->simplePaginate()', '->cursorPaginate()', '->chunk()'], 'correct' => '->cursorPaginate()'],
                ],
            ],
            'Queue & Jobs' => [
                'lessons' => [
                    ['title' => 'Основы очередей', 'description' => 'Очереди — отложенное выполнение задач (email, обработка изображений, экспорт). Dispatch: dispatch(new SendEmailJob($user)). Job класс: implements ShouldQueue, handle() — логика. onQueue(\'emails\') — приоритетные очереди. delay(now()->addMinutes(5)) — отложенный запуск. Драйверы: sync (синхронно, для тестов), database (БД), redis (быстро), sqs (AWS), beanstalkd. Тегирование: Queue::bulk([...]) — пакетная отправка.', 'materials' => 'Laravel Queues — документация, Драйверы очередей, Job batching'],
                    ['title' => 'Обработка и retries', 'description' => 'retryAfter(60) — время до повторной попытки (в секундах). tries(3) — максимум попыток. maxExceptions(3) — максимум исключений (включая дожидание). backoff([30, 60]) — увеличение задержки между попытками. failed() — вызывается при провале: запись в failed_jobs таблицу. failed(): JobFailedException — уведомление. Ручной retry: php artisan queue:retry all. Очистка: php artisan queue:flush. Horizon — мониторинг очередей в реальном времени.', 'materials' => 'Retries & Backoff, Failed Jobs, Horizon — документация'],
                ],
                'quizzes' => [
                    ['question' => 'Как отправить задачу в очередь?', 'options' => ['Queue::push()', 'dispatch()', 'Job::run()', 'Queue::add()'], 'correct' => 'dispatch()'],
                    ['question' => 'Что такое Horizon?', 'options' => ['Мониторинг очередей Laravel', 'Dashboard для React', 'CI/CD инструмент', 'Пакет для авторизации'], 'correct' => 'Мониторинг очередей Laravel'],
                    ['question' => 'Какой драйвер очередей использует Redis?', 'options' => ['sync', 'database', 'redis', 'sqs'], 'correct' => 'redis'],
                ],
            ],
            'Testing (PHPUnit)' => [
                'lessons' => [
                    ['title' => 'Unit тесты', 'description' => 'PHPUnit — тестовый фреймворк для PHP. TestCase: класс наследуется от TestCase. Методы: testXxx() или #[Test]. Assertions: assertEquals (равенство), assertTrue/assertFalse, assertNull, assertInstanceOf, assertCount. Mock: $mock = $this->mock(Service::class); $mock->expects("method")->andReturn($value). Stub: stub метода с возвращаемым значением. setUp()/tearDown — подготовка/очистка. Файл: phpunit.xml, директория tests/Unit/.', 'materials' => 'PHPUnit — документация, Assertions — справочник, Mocks vs Stubs'],
                    ['title' => 'Feature тесты', 'description' => 'Feature тесты — тестирование HTTP-запросов и полного цикла. $this->get("/api/users") — GET-запрос. $this->postJson("/api/users", $data) — POST с JSON. assertStatus(200) — проверка статус-кода. assertJson(["name" => "Иван"]) — проверка JSON. assertJsonStructure(["id", "name"]) — наличие ключей. RefreshDatabase — очистка БД после теста. DatabaseTransactions — откат транзакций (быстрее). ActingAs($user) — аутентификация пользователя.', 'materials' => 'HTTP Tests — документация, Assertions для JSON, Testing Exceptions'],
                ],
                'quizzes' => [
                    ['question' => 'Какой метод проверяет равенство значений?', 'options' => ['assertEqual', 'assertEquals', 'assertSame', 'assertTrue'], 'correct' => 'assertEquals'],
                    ['question' => 'Какой трейт очищает базу данных после теста?', 'options' => ['CleanDatabase', 'RefreshDatabase', 'ResetDatabase', 'ClearTables'], 'correct' => 'RefreshDatabase'],
                    ['question' => 'Как выполнить GET-запрос в Feature тесте?', 'options' => ['$this->get()', '$this->request()', 'Http::get()', '$this->fetch()'], 'correct' => '$this->get()'],
                ],
            ],
            'Redis / Cache' => [
                'lessons' => [
                    ['title' => 'Кэширование в Laravel', 'description' => 'Cache facade — единый API: Cache::put("key", $value, 600) — 10 минут. Cache::remember("key", 600, fn() => expensiveQuery()) — кэш или вычисление. Cache::tags(["users", "posts"]) — тегирование для группового сброса. Cache::flush() — очистка всего кэша. Cache::forget("key") — удаление ключа. Драйверы: file (файлы), redis (быстро), memcached, database (БД). Кэширование в моделях: Cache::remember("user.{$id}", 600, fn() => User::find($id)).', 'materials' => 'Cache — документация, Tags, Cache Drivers — сравнение'],
                    ['title' => 'Redis как хранилище', 'description' => 'Redis — быстрое хранилище данных в памяти. Типы: Strings (set/get/incr), Hashes (hset/hget — объекты), Lists (lpush/rpush — очереди), Sets (sadd/smembers — уникальные), Sorted Sets (zadd — с приоритетом). Laravel Redis: Redis::set("key", "value"), Redis::hset("user:1", "name", "Иван"). Кэширование сессий, очередей (Redis Queue driver),.rate limiting. pub/sub — публикация/подписка на каналы. LRU eviction — автоматическое удаление старых данных.', 'materials' => 'Redis — типы данных, Redis с Laravel, pub/sub, LRU eviction'],
                ],
                'quizzes' => [
                    ['question' => 'Как кэшировать значение на 60 минут?', 'options' => ['cache()->put()', 'cache()->remember()', 'cache()->get()', 'cache()->set()'], 'correct' => 'cache()->remember()'],
                    ['question' => 'Какая структура Redis хранит данные с приоритетом?', 'options' => ['list', 'set', 'sorted set', 'hash'], 'correct' => 'sorted set'],
                    ['question' => 'Как очистить весь кэш?', 'options' => ['Cache::clear()', 'Cache::flush()', 'Cache::destroy()', 'Cache::reset()'], 'correct' => 'Cache::flush()'],
                ],
            ],
            'WebSockets' => [
                'lessons' => [
                    ['title' => 'Основы WebSocket', 'description' => 'WebSocket — протокол для постоянного двунаправленного соединения (в отличие от HTTP request/response). Используется для чатов, уведомлений, игр, совместного редактирования. Установка: HTTP upgrade → WebSocket (ws:// или wss:// для шифрования). События: onopen, onmessage, onclose, onerror. Laravel Websockets: package "beyondcode/laravel-websockets". Broadcast::channel("chat.{$id}") — авторизация канала.', 'materials' => 'WebSocket — протокол, Laravel Websockets — пакет, Pusher — сервис'],
                    ['title' => 'Broadcasting', 'description' => 'Laravel Broadcasting — отправка событий клиентам через WebSocket. Event class: implements ShouldBroadcast, broadcastOn() — канал, broadcastAs() — имя. Приватные каналы: channel("user.{id}") — требуют авторизации. Presence каналы: presence("online") — показывают кто онлайн. Laravel Echo — JS-клиент: Echo.private("chat.1").listen("MessageSent", (e) => ...). Бэкенды: Pusher (SaaS), Soketi (self-hosted), Laravel Reverb (official).', 'materials' => 'Broadcasting — документация, Private Channels, Laravel Echo, Reverb'],
                ],
                'quizzes' => [
                    ['question' => 'Какое соединение используется в WebSocket?', 'options' => ['Simplex', 'Half-duplex', 'Duplex', 'Multiplex'], 'correct' => 'Duplex'],
                    ['question' => 'Что такое приватный канал в Broadcasting?', 'options' => ['Открытый канал', 'Канал с авторизацией', 'Личный чат', 'Админский канал'], 'correct' => 'Канал с авторизацией'],
                    ['question' => 'Что такое Broadcasting в Laravel?', 'options' => ['Оповещение всех клиентов о событии', 'Отправка email', 'Логирование', 'Миграция'], 'correct' => 'Оповещение всех клиентов о событии'],
            ],
            ],
            'CI/CD' => [
                'lessons' => [
                    ['title' => 'Continuous Integration', 'description' => 'CI — автоматическая интеграция кода: при каждом push запускаются тесты, линтинг, сборка. GitHub Actions: .github/workflows/ci.yml — YAML-конфигурация. jobs: test (phpunit, pint), build (npm run build). Triggers: push, pull_request. Матрица: php 8.1/8.2, node 18/20. Артефакты: upload-artifact для сборки. Кэширование: composer install, npm install. Badges — статус CI в README. GitLab CI: .gitlab-ci.yml, stages, runners.', 'materials' => 'GitHub Actions — документация, GitLab CI, CI/CD best practices'],
                    ['title' => 'Deployment', 'description' => 'Zero-downtime деплой — обновление без простоя приложения. Laravel Forge — управление серверами (nginx, PHP-FPM, queues). Envoyer — zero-downtime: симлинки, atomic deployments. Docker deploy: docker-compose pull → up -d. Blue/Green deployment — два окружения, переключение. Rollback — откат к предыдущей версии. Staging → Production — тестирование перед прода. Ansible/Capistrano — автоматизация деплоя. Database migrations — php artisan migrate при деплое.', 'materials' => 'Laravel Forge, Envoyer, Docker deploy, Zero-downtime — паттерны'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое CI?', 'options' => ['Continuous Integration', 'Cloud Infrastructure', 'Code Inspection', 'Container Installation'], 'correct' => 'Continuous Integration'],
                    ['question' => 'Что такое zero-downtime деплой?', 'options' => ['Деплой без простоя приложения', 'Деплой за 0 секунд', 'Деплой без тестов', 'Деплой без изменений'], 'correct' => 'Деплой без простоя приложения'],
                    ['question' => 'Какая платформа предоставляет CI/CD от GitHub?', 'options' => ['GitHub Actions', 'GitHub Pages', 'GitHub Copilot', 'GitHub Codespaces'], 'correct' => 'GitHub Actions'],
            ],
            ],
            'Security' => [
                'lessons' => [
                    ['title' => 'OWASP Top 10', 'description' => 'OWASP Top 10 — список самых критических уязвимостей веб-приложений. A01: Broken Access Control — неправильная проверка прав. A02: Cryptographic Failures — слабое шифрование. A03: Injection — внедрение SQL/NoSQL/OS команд. A05: Security Misconfiguration — дефолтные пароли, отладка в проде. A07: XSS — внедрение скриптов. A08: Insecure Deserialization — небезопасная десериализация. A09: Known Vulnerabilities — использование уязвимых зависимостей. Защита: валидация, экранирование, prepared statements, авторизация.', 'materials' => 'OWASP Top 10 — документация, OWASP Cheat Sheets, Security Audit'],
                    ['title' => 'Защита приложений', 'description' => 'Rate Limiting — ограничение запросов: RateLimiter::attempt("login", 5, fn() => ...). Валидация: $request->validate(["email" => "required|email"]). Sanitization — очистка ввода (DOMPurify, strip_tags). CSP (Content Security Policy) — разрешённые источники скриптов/стилей. Security Headers: X-Frame-Options (clickjacking), HSTS (HTTPS), X-Content-Type-Options. TrustedProxy — доверие прокси-заголовкам. Middleware: CSRF protection (VerifyCsrfToken), Authentication.', 'materials' => 'Rate Limiting — документация, Security Headers, CSP — настройка, CSRF protection'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое CSRF?', 'options' => ['Cross-Site Request Forgery', 'Cross-Server File System', 'Client-Side Rendering Function', 'Code Security Framework'], 'correct' => 'Cross-Site Request Forgery'],
                    ['question' => 'Что такое XSS?', 'options' => ['Cross-Site Scripting', 'External Style Sheet', 'XML Schema Standard', 'Cross Server Search'], 'correct' => 'Cross-Site Scripting'],
                    ['question' => 'Зачем нужен rate limiting?', 'options' => ['Ускорение запросов', 'Ограничение количества запросов', 'Кэширование', 'Логирование'], 'correct' => 'Ограничение количества запросов'],
            ],
            ],
            'Microservices' => [
                'lessons' => [
                    ['title' => 'Принципы микросервисов', 'description' => 'Микросервисы — архитектура из мелких, независимых сервисов. Single Responsibility — каждый сервис отвечает за одну бизнес-область (users, payments, notifications). Bounded Context — границы контекста (DDD). Decentralized Data — у каждого сервиса своя БД (не общая!). Autonomy — независимое деплое и масштабирование. 12-Factor App — методология: код в git, зависимости явные, конфиг в env, stateless процессы. Монолит → микросервисы постепенно (Strangler Fig pattern).', 'materials' => 'Domain-Driven Design, 12-Factor App, Strangler Fig — паттерн, Монолит vs Микросервисы'],
                    ['title' => 'Связь между сервисами', 'description' => 'REST — синхронный HTTP (просто, но задержки). gRPC — быстрый RPC (protobuf, HTTP/2, streaming). Message Queues — асинхронный обмен (RabbitMQ, Kafka, Redis). Event-Driven — события (UserCreated → отправить email, обновить аналитику). API Gateway — единая точка входа (Kong, Traefik). Service Discovery — нахождение сервисов (Consul, etcd). Circuit Breaker — защита от каскадных сбоев (Hystrix, Resilience4j). Saga — управление распределёнными транзакциями.', 'materials' => 'gRPC — начало работы, Kafka vs RabbitMQ, API Gateway, Saga pattern'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое CQRS?', 'options' => ['Command Query Responsibility Segregation', 'Central Query Router Service', 'Cached Query Response System', 'Code Quality Rating Scale'], 'correct' => 'Command Query Responsibility Segregation'],
                    ['question' => 'Что такое Saga в микросервисах?', 'options' => ['Паттерн управления транзакциями', 'Паттерн кэширования', 'Паттерн авторизации', 'Паттерн логирования'], 'correct' => 'Паттерн управления транзакциями'],
                    ['question' => 'Что такое API Gateway?', 'options' => ['Входная точка для всех запросов', 'База данных', 'Система логирования', 'Тестовый фреймворк'], 'correct' => 'Входная точка для всех запросов'],
                ],
            ],
            'JavaScript OOP' => [
                'lessons' => [
                    ['title' => 'Классы в JavaScript', 'description' => 'class, constructor, методы, static, getters/setters.', 'materials' => 'Class sugar over prototype'],
                    ['title' => 'Прототипы', 'description' => '__proto__, prototype chain, Object.create, наследование.', 'materials' => 'Prototypal inheritance'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое class в JS?', 'options' => ['Новый тип данных', 'Синтаксический сахар над прототипами', 'Интерфейс', 'Модуль'], 'correct' => 'Синтаксический сахар над прототипами'],
                    ['question' => 'Как наследовать класс в JS?', 'options' => ['implements', 'extends', 'inherits', 'derives'], 'correct' => 'extends'],
                    ['question' => 'Что такое prototype?', 'options' => ['Шаблон для объекта', 'Экземпляр класса', 'Массив', 'Функция'], 'correct' => 'Шаблон для объекта'],
                ],
            ],
            'JS Async / Promises' => [
                'lessons' => [
                    ['title' => 'Promise API', 'description' => 'Promise.all, Promise.race, Promise.allSettled, Promise.any.', 'materials' => 'Комбинирование промисов'],
                    ['title' => 'async/await', 'description' => 'async функции, await, обработка ошибок try/catch, параллельные запросы.', 'materials' => 'Promise.all с async/await'],
                ],
                'quizzes' => [
                    ['question' => 'Что делает await?', 'options' => ['Создаёт промис', 'Приостанавливает выполнение до результата', 'Удаляет промис', 'Кэширует результат'], 'correct' => 'Приостанавливает выполнение до результата'],
                    ['question' => 'Что такое Promise.all?', 'options' => ['Выполняет все промисы параллельно', 'Выполняет по очереди', 'Выполняет первый успешный', 'Удаляет все промисы'], 'correct' => 'Выполняет все промисы параллельно'],
                    ['question' => 'Где нельзя использовать await?', 'options' => ['В async функции', 'В обычной функции', 'В коде модуля', 'В обработчике событий'], 'correct' => 'В обычной функции'],
                ],
            ],
            'Deploy' => [
                'lessons' => [
                    ['title' => 'Варианты деплоя', 'description' => 'VPS, PaaS (Heroku, Railway), static hosting, serverless.', 'materials' => 'Деплой Laravel на VPS, Docker'],
                    ['title' => 'Домены и SSL', 'description' => 'DNS, A/CNAME записи, SSL сертификаты, Let\'s Encrypt.', 'materials' => 'Certbot, wildcard сертификаты'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое VPS?', 'options' => ['Virtual Private Server', 'Very Private Storage', 'Virtual Page System', 'Video Processing Server'], 'correct' => 'Virtual Private Server'],
                    ['question' => 'Как получить бесплатный SSL?', 'options' => ['Купить', 'Let\'s Encrypt', 'Создать свой', 'Найти'], 'correct' => 'Let\'s Encrypt'],
                    ['question' => 'Что такое serverless?', 'options' => ['Без серверов', 'Серверы без управления', 'Только фронтенд', 'Локальная разработка'], 'correct' => 'Серверы без управления'],
                ],
            ],
            'Bash Scripting' => [
                'lessons' => [
                    ['title' => 'Основы Bash', 'description' => 'Shebang, переменные, условия, циклы, файловые операции.', 'materials' => '$?, $0, $1, $$'],
                    ['title' => 'Продвинутый Bash', 'description' => 'Функции, массивы, строковые операции, trap, cron.', 'materials' => 'sed, awk, grep в скриптах'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое shebang?', 'options' => ['Комментарий', 'Строка #!/bin/bash в начале скрипта', 'Переменная', 'Функция'], 'correct' => 'Строка #!/bin/bash в начале скрипта'],
                    ['question' => 'Что означает $0?', 'options' => ['Последний аргумент', 'Имя скрипта', 'Код возврата', 'Текущая директория'], 'correct' => 'Имя скрипта'],
                    ['question' => 'Как запустить bash скрипт?', 'options' => ['run script.sh', 'bash script.sh', 'exec script.sh', 'start script.sh'], 'correct' => 'bash script.sh'],
                ],
            ],
            'Networking' => [
                'lessons' => [
                    ['title' => 'OSI модель', 'description' => '7 уровней: Physical, Data Link, Network, Transport, Session, Presentation, Application.', 'materials' => 'TCP/IP vs OSI'],
                    ['title' => 'Сетевые инструменты', 'description' => 'ping, traceroute, nslookup, netstat, curl, wget.', 'materials' => 'Порты, протоколы'],
                ],
                'quizzes' => [
                    ['question' => 'На каком уровне OSI работает HTTP?', 'options' => ['Level 4', 'Level 5', 'Level 6', 'Level 7'], 'correct' => 'Level 7'],
                    ['question' => 'Что делает команда ping?', 'options' => ['Проверяет связь с хостом', 'Сканирует порты', 'Загружает файлы', 'Логинит на сервер'], 'correct' => 'Проверяет связь с хостом'],
                    ['question' => 'Какой протокол используется для передачи файлов?', 'options' => ['HTTP', 'FTP', 'SMTP', 'DNS'], 'correct' => 'FTP'],
                ],
            ],
            'Git Advanced' => [
                'lessons' => [
                    ['title' => 'Продвинутый Git', 'description' => 'Rebase, cherry-pick, bisect, reflog, interactive rebase.', 'materials' => 'Git reset vs revert vs restore'],
                    ['title' => 'Git hooks и workflow', 'description' => 'pre-commit, commit-msg, Git Flow, trunk-based development.', 'materials' => 'Husky, lint-staged'],
                ],
                'quizzes' => [
                    ['question' => 'Что делает git rebase?', 'options' => ['Перемещает коммиты на другую базу', 'Создаёт новую ветку', 'Удаляет репозиторий', 'Откатывает изменения'], 'correct' => 'Перемещает коммиты на другую базу'],
                    ['question' => 'Что такое git stash?', 'options' => ['Скрывает изменения', 'Сохраняет изменения во временном хранилище', 'Удаляет ветку', 'Мержит ветки'], 'correct' => 'Сохраняет изменения во временном хранилище'],
                    ['question' => 'Что такое Git Flow?', 'options' => ['Workflow для ветвления', 'CI/CD платформа', 'Редактор кода', 'База данных'], 'correct' => 'Workflow для ветвления'],
                ],
            ],
            'Docker Compose' => [
                'lessons' => [
                    ['title' => 'Основы Docker Compose', 'description' => 'docker-compose.yml, сервисы, сети, volumes, зависимости.', 'materials' => 'docker-compose up/down'],
                    ['title' => 'Продвинутый Compose', 'description' => 'Profiles, extends, depends_on, healthcheck, multi-stage builds.', 'materials' => 'Раздельные compose файлы для dev/prod'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Docker Compose?', 'options' => ['Инструмент для нескольких контейнеров', 'Редактор Dockerfile', 'Registry образов', 'CLI Docker'], 'correct' => 'Инструмент для нескольких контейнеров'],
                    ['question' => 'Что делает depends_on?', 'options' => ['Определяет порядок запуска контейнеров', 'Связывает порты', 'Копирует файлы', 'Удаляет контейнеры'], 'correct' => 'Определяет порядок запуска контейнеров'],
                    ['question' => 'Зачем нужны volumes в Compose?', 'options' => ['Для передачи данных между контейнерами и хостом', 'Для маршрутизации', 'Для логирования', 'Для тестирования'], 'correct' => 'Для передачи данных между контейнерами и хостом'],
                ],
            ],
            'CI/CD Pipelines' => [
                'lessons' => [
                    ['title' => 'GitHub Actions', 'description' => 'workflows, jobs, steps, triggers, secrets, артефакты.', 'materials' => 'YAML синтаксис, marketplace actions'],
                    ['title' => 'Jenkins и GitLab CI', 'description' => 'Jenkinsfile, .gitlab-ci.yml, stages, runners, артефакты.', 'materials' => 'Pipeline as code'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое GitHub Actions?', 'options' => ['Платформа для CI/CD', 'База данных', 'Редактор кода', 'Тестовый фреймворк'], 'correct' => 'Платформа для CI/CD'],
                    ['question' => 'Что такое workflow в CI/CD?', 'options' => ['Набор шагов для автоматизации', 'База данных', 'Мониторинг', 'Логирование'], 'correct' => 'Набор шагов для автоматизации'],
                    ['question' => 'Как хранить секреты в CI/CD?', 'options' => ['В коде', 'В переменных окружения / secrets', 'В README', 'В коммитах'], 'correct' => 'В переменных окружения / secrets'],
                ],
            ],
            'Terraform' => [
                'lessons' => [
                    ['title' => 'Основы Terraform', 'description' => 'HCL, providers, resources, variables, outputs.', 'materials' => 'terraform init, plan, apply, destroy'],
                    ['title' => 'Продвинутый Terraform', 'description' => 'Modules, state management, workspaces, provisioners.', 'materials' => 'Remote state, state locking'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое IaC?', 'options' => ['Infrastructure as Code', 'Internet as Cloud', 'Integrated Application Code', 'Internal API Cache'], 'correct' => 'Infrastructure as Code'],
                    ['question' => 'Что делает terraform plan?', 'options' => ['Показывает изменения до применения', 'Применяет изменения', 'Удаляет ресурсы', 'Инициализирует проект'], 'correct' => 'Показывает изменения до применения'],
                    ['question' => 'Зачем нужен remote state?', 'options' => ['Хранение состояния в облаке', 'Локальное кэширование', 'Удаление ресурсов', 'Мониторинг'], 'correct' => 'Хранение состояния в облаке'],
                ],
            ],
            'Ansible' => [
                'lessons' => [
                    ['title' => 'Основы Ansible', 'description' => 'Inventory, playbooks, модули, Facts, handlers.', 'materials' => 'YAML, SSH-подключение, ad-hoc commands'],
                    ['title' => 'Роли и Galaxy', 'description' => 'Структура ролей, galaxy, vault для секретов.', 'materials' => 'ansible-galaxy, ansible-vault'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Ansible?', 'options' => ['Контейнер', 'Инструмент конфигурации', 'База данных', 'Текстовый редактор'], 'correct' => 'Инструмент конфигурации'],
                    ['question' => 'Что такое playbook?', 'options' => ['Скрипт деплоя', 'Набор задач для хостов', 'Документация', 'Лог'], 'correct' => 'Набор задач для хостов'],
                    ['question' => 'Для чего ansible-vault?', 'options' => ['Шифрование секретов', 'Запуск контейнеров', 'Мониторинг', 'Тестирование'], 'correct' => 'Шифрование секретов'],
                ],
            ],
            'Monitoring' => [
                'lessons' => [
                    ['title' => 'Prometheus', 'description' => 'Метрики, scrape, PromQL, alertmanager.', 'materials' => 'Time-series база данных, exporters'],
                    ['title' => 'Grafana', 'description' => 'Дашборды, панели, datasource, алерты.', 'materials' => 'Визуализация метрик из Prometheus'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Prometheus?', 'options' => ['Система мониторинга и алертинга', 'База данных', 'CI/CD платформа', 'Редактор кода'], 'correct' => 'Система мониторинга и алертинга'],
                    ['question' => 'Для чего используется Grafana?', 'options' => ['Визуализация и дашборды', 'Кэширование', 'Авторизация', 'Логирование'], 'correct' => 'Визуализация и дашборды'],
                    ['question' => 'Что такое alerting?', 'options' => ['Оповещения при нарушении условий', 'Логирование ошибок', 'Кэширование данных', 'Резервное копирование'], 'correct' => 'Оповещения при нарушении условий'],
                ],
            ],
            'Logging (ELK)' => [
                'lessons' => [
                    ['title' => 'Elasticsearch', 'description' => 'Индексация, поиск, маппинги, аналитика.', 'materials' => 'REST API, Kibana'],
                    ['title' => 'Logstash и Beats', 'description' => 'Сбор, обработка и отправка логов, pipeline.', 'materials' => 'Filebeat, Metricbeat, Input/Filter/Output'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое ELK?', 'options' => ['Elasticsearch, Logstash, Kibana', 'Electronic Library Kit', 'Enterprise Linux Kernel', 'Enhanced Log Keeper'], 'correct' => 'Elasticsearch, Logstash, Kibana'],
                    ['question' => 'Для чего нужна Kibana?', 'options' => ['Визуализация логов', 'Сбор логов', 'Индексация', 'Кэширование'], 'correct' => 'Визуализация логов'],
                    ['question' => 'Что такое Filebeat?', 'options' => ['Легковесный сборщик логов', 'База данных', 'CI/CD инструмент', 'Фреймворк для тестов'], 'correct' => 'Легковесный сборщик логов'],
                ],
            ],
            'Cloud (AWS/GCP)' => [
                'lessons' => [
                    ['title' => 'Основы облака', 'description' => 'IaaS, PaaS, SaaS, регионы, зоны доступности.', 'materials' => 'AWS EC2, S3, IAM'],
                    ['title' => 'Контейнеры в облаке', 'description' => 'ECS, EKS, GKE, Cloud Run, Fargate.', 'materials' => 'Управляемые Kubernetes сервисы'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое S3?', 'options' => ['Объектное хранилище AWS', 'Виртуальный сервер', 'База данных', 'CDN'], 'correct' => 'Объектное хранилище AWS'],
                    ['question' => 'Что такое Lambda в AWS?', 'options' => ['Serverless функции', 'Виртуальный сервер', 'База данных', 'Очередь сообщений'], 'correct' => 'Serverless функции'],
                    ['question' => 'Что такое ECS?', 'options' => ['Elastic Container Service', 'Enterprise Cloud Storage', 'Encrypted Cache System', 'Event-Driven Compute Service'], 'correct' => 'Elastic Container Service'],
                ],
            ],
            'Secrets Management' => [
                'lessons' => [
                    ['title' => 'HashiCorp Vault', 'description' => 'Хранение секретов, динамические секреты, policies.', 'materials' => 'Secret engines, AppRole'],
                    ['title' => 'Best practices', 'description' => 'Environment variables, .env файлы, rotation, least privilege.', 'materials' => 'Паттерны управления секретами'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Vault?', 'options' => ['Система управления секретами', 'База данных', 'CI/CD инструмент', 'Мониторинг'], 'correct' => 'Система управления секретами'],
                    ['question' => 'Где НЕ стоит хранить секреты?', 'options' => ['Vault', '.env файл в репозитории', 'Environment variables', 'Специализированный сервис'], 'correct' => '.env файл в репозитории'],
                    ['question' => 'Что такое secret rotation?', 'options' => ['Периодическая смена секретов', 'Кэширование секретов', 'Шифрование', 'Удаление секретов'], 'correct' => 'Периодическая смена секретов'],
                ],
            ],
            'Service Mesh' => [
                'lessons' => [
                    ['title' => 'Концепция Service Mesh', 'description' => 'Сеть для микросервисов, управление трафиком, безопасность.', 'materials' => 'Istio, Linkerd, Consul Connect'],
                    ['title' => 'Возможности', 'description' => 'mTLS, circuit breaking, load balancing, observability.', 'materials' => 'Sidecar proxy паттерн'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое service mesh?', 'options' => ['Инфраструктурный слой для микросервисов', 'База данных', 'CI/CD платформа', 'Мониторинг'], 'correct' => 'Инфраструктурный слой для микросервисов'],
                    ['question' => 'Что такое sidecar?', 'options' => ['Прокси рядом с каждым сервисом', 'Основной сервер', 'Балансировщик', 'Логгер'], 'correct' => 'Прокси рядом с каждым сервисом'],
                    ['question' => 'Что такое mTLS?', 'options' => ['Mutual TLS - взаимная аутентификация', 'Modified TLS', 'Multi-tenant Lock System', 'Managed Transport Layer Security'], 'correct' => 'Mutual TLS - взаимная аутентификация'],
                ],
            ],
            'GitOps' => [
                'lessons' => [
                    ['title' => 'Принципы GitOps', 'description' => 'Git как источник правды, declarative infrastructure, pull-based deploy.', 'materials' => 'OpenGitOps, 12-factor'],
                    ['title' => 'Реализация', 'description' => 'ArgoCD, Flux, синхронизация, health checks.', 'materials' => 'Kubernetes + GitOps'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое GitOps?', 'options' => ['Операционная модель на основе Git', 'Редактор кода', 'CI/CD инструмент', 'База данных'], 'correct' => 'Операционная модель на основе Git'],
                    ['question' => 'Что такое ArgoCD?', 'options' => ['GitOps доставка для Kubernetes', 'CI/CD платформа', 'База данных', 'Мониторинг'], 'correct' => 'GitOps доставка для Kubernetes'],
                    ['question' => 'Pull-based deploy означает?', 'options' => ['Контейнеры сами забирают изменения', 'Разработчик пушит изменения', 'Автоматический релиз', 'Ручной деплой'], 'correct' => 'Контейнеры сами забирают изменения'],
                ],
            ],
            'SRE Practices' => [
                'lessons' => [
                    ['title' => 'SLO/SLA/SLI', 'description' => 'Service Level Indicators, Objectives, Agreements.', 'materials' => 'Error budgets, uptime SLA'],
                    ['title' => 'Incident Management', 'description' => 'Триаж, эскалация, постмортем, документирование.', 'materials' => 'PagerDuty, runbooks'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое SLI?', 'options' => ['Service Level Indicator - метрика качества', 'System Level Integration', 'Secure Login Interface', 'Software Lifecycle Index'], 'correct' => 'Service Level Indicator - метрика качества'],
                    ['question' => 'Что такое error budget?', 'options' => ['Допустимый уровень ошибок', 'Бюджет на исправления', 'Запас памяти', 'Лимит запросов'], 'correct' => 'Допустимый уровень ошибок'],
                    ['question' => 'Что такое postmortem?', 'options' => ['Анализ инцидента после его разрешения', 'Предотвращение инцидентов', 'Мониторинг', 'Деплой'], 'correct' => 'Анализ инцидента после его разрешения'],
                ],
            ],
            'Security Hardening' => [
                'lessons' => [
                    ['title' => 'Hardening серверов', 'description' => 'Отключение лишних сервисов, SSH ключи, firewall, обновления.', 'materials' => 'UFW, iptables, fail2ban'],
                    ['title' => 'Аудит безопасности', 'description' => 'Сканирование уязвимостей, CIS Benchmark, penetration testing.', 'materials' => 'Nessus, OpenVAS, Lynis'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое CIS Benchmark?', 'options' => ['Стандарт безопасности для систем', 'Бенчмаркинг производительности', 'Тестовый фреймворк', 'CI/CD стандарт'], 'correct' => 'Стандарт безопасности для систем'],
                    ['question' => 'Как усилить SSH?', 'options' => ['Отключить пароли, использовать ключи, изменить порт', 'Использовать FTP', 'Отключить аутентификацию', 'Использовать HTTP'], 'correct' => 'Отключить пароли, использовать ключи, изменить порт'],
                    ['question' => 'Что такое vulnerability scanning?', 'options' => ['Автоматический поиск уязвимостей', 'Ручная проверка кода', 'Тестирование производительности', 'Мониторинг сети'], 'correct' => 'Автоматический поиск уязвимостей'],
                ],
            ],
            'Chaos Engineering' => [
                'lessons' => [
                    ['title' => 'Принципы Chaos', 'description' => 'Проведение экспериментов для выявления слабых мест.', 'materials' => 'Principles of Chaos Engineering'],
                    ['title' => 'Практика', 'description' => 'Chaos Monkey, Litmus, steady state hypothesis, blast radius.', 'materials' => 'Kubernetes chaos инструменты'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Chaos Engineering?', 'options' => ['Эксперименты для улучшения отказоустойчивости', 'Тестирование скорости', 'Логирование', 'Мониторинг'], 'correct' => 'Эксперименты для улучшения отказоустойчивости'],
                    ['question' => 'Что такое blast radius?', 'options' => ['Область влияния эксперимента', 'Радиус сети', 'Объем памяти', 'Количество серверов'], 'correct' => 'Область влияния эксперимента'],
                    ['question' => 'Что такое steady state?', 'options' => ['Нормальная работа системы до эксперимента', 'Состояние после сбоя', 'Режим восстановления', 'Выключенное состояние'], 'correct' => 'Нормальная работа системы до эксперимента'],
                ],
            ],
            'Virtual Environments' => [
                'lessons' => [
                    ['title' => 'venv и virtualenv', 'description' => 'Создание виртуальных окружений, изоляция зависимостей.', 'materials' => 'python -m venv, requirements.txt'],
                    ['title' => 'Poetry', 'description' => 'Управление зависимостями, pyproject.toml, lock файл.', 'materials' => 'poetry add, poetry install'],
                ],
                'quizzes' => [
                    ['question' => 'Зачем нужны виртуальные окружения?', 'options' => ['Изоляция зависимостей проектов', 'Ускорение Python', 'Кэширование', 'Тестирование'], 'correct' => 'Изоляция зависимостей проектов'],
                    ['question' => 'Как активировать venv?', 'options' => ['activate', 'source venv/bin/activate', 'venv start', 'python activate'], 'correct' => 'source venv/bin/activate'],
                    ['question' => 'Что такое pyproject.toml?', 'options' => ['Файл конфигурации проекта Python', 'Файл стилей', 'Тестовый файл', 'README'], 'correct' => 'Файл конфигурации проекта Python'],
                ],
            ],
            'REST API (FastAPI)' => [
                'lessons' => [
                    ['title' => 'Основы FastAPI', 'description' => 'Path параметры, query параметры, тело запроса, валидация.', 'materials' => 'Pydantic, автоматическая документация'],
                    ['title' => 'Зависимости и тестирование', 'description' => 'Dependency injection, TestClient, асинхронные тесты.', 'materials' => 'pytest-asyncio, httpx'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое FastAPI?', 'options' => ['Асинхронный фреймворк для API', 'База данных', 'Тестовый фреймворк', 'CLI инструмент'], 'correct' => 'Асинхронный фреймворк для API'],
                    ['question' => 'Что такое Pydantic?', 'options' => ['Библиотека валидации данных', 'ORM', 'CLI', 'Тестовый фреймворк'], 'correct' => 'Библиотека валидации данных'],
                    ['question' => 'Где найти автоматическую документацию FastAPI?', 'options' => ['/docs', '/api', '/documentation', '/swagger'], 'correct' => '/docs'],
                ],
            ],
            'Django REST Framework' => [
                'lessons' => [
                    ['title' => 'DRF serializers', 'description' => 'Сериализация/десериализация, валидация, вложенные сериализаторы.', 'materials' => 'ModelSerializer, HyperlinkedModelSerializer'],
                    ['title' => 'ViewSets и роутеры', 'description' => 'ModelViewSet, ReadOnlyModelViewSet, DefaultRouter.', 'materials' => 'CRUD из коробки'],
                ],
                'quizzes' => [
                    ['question' => 'Что делает serializer в DRF?', 'options' => ['Преобразует модели в JSON и обратно', 'Создаёт маршруты', 'Логирует ошибки', 'Кэширует данные'], 'correct' => 'Преобразует модели в JSON и обратно'],
                    ['question' => 'Что такое ViewSet?', 'options' => ['Набор логики для CRUD операций', 'Маршрут', 'Шаблон', 'Форма'], 'correct' => 'Набор логики для CRUD операций'],
                    ['question' => 'Зачем нужен Router в DRF?', 'options' => ['Автоматическая генерация URL для ViewSets', 'Маршрутизация сети', 'Подключение к БД', 'Тестирование'], 'correct' => 'Автоматическая генерация URL для ViewSets'],
                ],
            ],
            'Testing (pytest)' => [
                'lessons' => [
                    ['title' => 'Основы pytest', 'description' => 'Функции-тесты, assert, conftest, fixtures, parametrize.', 'materials' => 'pytest.ini, markers'],
                    ['title' => 'Продвинутый pytest', 'description' => 'monkeypatch, моки, плагины, coverage.', 'materials' => 'pytest-cov, pytest-mock'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое fixture в pytest?', 'options' => ['Предустановленные данные/окружение для теста', 'Тестовая функция', 'Плагин', 'Конфигурация'], 'correct' => 'Предустановленные данные/окружение для теста'],
                    ['question' => 'Что делает monkeypatch?', 'options' => ['Подменяет объекты во время теста', 'Патчит файлы', 'Кэширует данные', 'Логирует ошибки'], 'correct' => 'Подменяет объекты во время теста'],
                    ['question' => 'Как запустить тесты по имени?', 'options' => ['pytest -k test_name', 'pytest --run test_name', 'pytest test_name', 'pytest --only test_name'], 'correct' => 'pytest -k test_name'],
                ],
            ],
            'Data Science' => [
                'lessons' => [
                    ['title' => 'NumPy', 'description' => 'Массивы, индексация, Broadcasting, линейная алгебра.', 'materials' => 'ndarray, vectorized operations'],
                    ['title' => 'Pandas', 'description' => 'DataFrame, Series, фильтрация, группировка, объединение.', 'materials' => 'read_csv, groupby, merge'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое ndarray в NumPy?', 'options' => ['N-мерный массив', 'Таблица', 'Граф', 'Строка'], 'correct' => 'N-мерный массив'],
                    ['question' => 'Что такое DataFrame?', 'options' => ['Табличная структура данных', 'Массив', 'Граф', 'Строка'], 'correct' => 'Табличная структура данных'],
                    ['question' => 'Как прочитать CSV в Pandas?', 'options' => ['pd.read_csv()', 'pd.load_csv()', 'pd.open_csv()', 'pd.import_csv()'], 'correct' => 'pd.read_csv()'],
                ],
            ],
            'Machine Learning' => [
                'lessons' => [
                    ['title' => 'Основы ML', 'description' => 'Supervised/unsupervised, feature engineering, train/test split.', 'materials' => 'Scikit-learn, метрики качества'],
                    ['title' => 'Модели', 'description' => 'Linear regression, decision trees, random forest, neural networks.', 'materials' => 'Параметры и гиперпараметры'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое supervised learning?', 'options' => ['Обучение с учителем (с метками)', 'Обучение без учителя', 'Подкрепление', 'Генерация данных'], 'correct' => 'Обучение с учителем (с метками)'],
                    ['question' => 'Зачем нужен train/test split?', 'options' => ['Для оценки качества модели', 'Для ускорения обучения', 'Для кэширования', 'Для логирования'], 'correct' => 'Для оценки качества модели'],
                    ['question' => 'Что такое overfitting?', 'options' => ['Модель слишком хорошо запомнила данные', 'Модель слишком простая', 'Модель не обучилась', 'Модель слишком быстрая'], 'correct' => 'Модель слишком хорошо запомнила данные'],
                ],
            ],
            'Celery / Async' => [
                'lessons' => [
                    ['title' => 'Основы Celery', 'description' => 'Задачи, workers, broker, результаты, планирование.', 'materials' => 'Redis/RabbitMQ как broker'],
                    ['title' => 'Продвинутые возможности', 'description' => 'Chains, groups, chords, rate limiting, retries.', 'materials' => 'Celery Beat, Flower'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Celery?', 'options' => ['Очередь задач для Python', 'ORM', 'Тестовый фреймворк', 'CLI'], 'correct' => 'Очередь задач для Python'],
                    ['question' => 'Что такое broker в Celery?', 'options' => ['Сервис для хранения задач', 'Клиент', 'Воркер', 'Результат'], 'correct' => 'Сервис для хранения задач'],
                    ['question' => 'Что такое Flower?', 'options' => ['Веб-мониторинг для Celery', 'База данных', 'CI/CD инструмент', 'Редактор кода'], 'correct' => 'Веб-мониторинг для Celery'],
                ],
            ],
            'Docker для Python' => [
                'lessons' => [
                    ['title' => 'Dockerfile для Python', 'description' => 'FROM python, WORKDIR, COPY, RUN pip install, CMD.', 'materials' => 'Кэширование pip, multi-stage builds'],
                    ['title' => 'Docker Compose для Python', 'description' => 'Compose с Python + Redis + PostgreSQL.', 'materials' => 'Окружение для разработки'],
                ],
                'quizzes' => [
                    ['question' => 'Как кэшировать pip в Dockerfile?', 'options' => ['COPY requirements.txt перед install', 'RUN pip cache', 'USE cache', 'ENABLE pip'], 'correct' => 'COPY requirements.txt перед install'],
                    ['question' => 'Зачем нужен .dockerignore?', 'options' => ['Исключить файлы из контейнера', 'Для стилей', 'Для тестов', 'Для логов'], 'correct' => 'Исключить файлы из контейнера'],
                    ['question' => 'Что такое multi-stage build?', 'options' => ['Сборка в несколько этапов для уменьшения образа', 'Много контейнеров', 'Несколько Dockerfile', 'Большой образ'], 'correct' => 'Сборка в несколько этапов для уменьшения образа'],
                ],
            ],
            'ML Frameworks' => [
                'lessons' => [
                    ['title' => 'PyTorch', 'description' => 'Tensors, autograd, нейросети, Dataset/DataLoader.', 'materials' => 'torch.nn, optim'],
                    ['title' => 'TensorFlow', 'description' => 'Tensors, Keras API, обучение моделей, SavedModel.', 'materials' => 'tf.keras, TensorBoard'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Tensor?', 'options' => ['Многомерный массив для вычислений', 'Функция', 'Класс', 'Переменная'], 'correct' => 'Многомерный массив для вычислений'],
                    ['question' => 'Что такое autograd в PyTorch?', 'options' => ['Автоматическое дифференцирование', 'Автозагрузка', 'Автотестирование', 'Автоматизация'], 'correct' => 'Автоматическое дифференцирование'],
                    ['question' => 'PyTorch vs TensorFlow: основное отличие?', 'options' => ['PyTorch - динамический граф, TensorFlow - статический', 'Нет отличий', 'PyTorch для Java', 'TensorFlow для Python'], 'correct' => 'PyTorch - динамический граф, TensorFlow - статический'],
                ],
            ],
            'Data Pipelines' => [
                'lessons' => [
                    ['title' => 'ETL процессы', 'description' => 'Extract, Transform, Load, оркестрация, расписание.', 'materials' => 'Apache Airflow, Prefect'],
                    ['title' => 'Потоковая обработка', 'description' => 'Stream processing, Kafka, Spark Streaming.', 'materials' => 'Event-time vs processing-time'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое ETL?', 'options' => ['Extract, Transform, Load', 'Error, Time, Log', 'Enable, Test, Launch', 'Encode, Transfer, Load'], 'correct' => 'Extract, Transform, Load'],
                    ['question' => 'Что такое Apache Airflow?', 'options' => ['Оркестратор пайплайнов', 'База данных', 'CI/CD платформа', 'Фреймворк для тестов'], 'correct' => 'Оркестратор пайплайнов'],
                    ['question' => 'Batch processing означает?', 'options' => ['Обработка данных порциями', 'Потоковая обработка', 'Ручная обработка', 'Обработка в реальном времени'], 'correct' => 'Обработка данных порциями'],
                ],
            ],
            'Design Tokens' => [
                'lessons' => [
                    ['title' => 'Что такое Design Tokens', 'description' => 'Атомарные значения дизайна: цвета, типографика, отступы.', 'materials' => 'Переменные дизайна'],
                    ['title' => 'Система токенов', 'description' => 'Primitive, semantic, component токены, иерархия.', 'materials' => 'Style Dictionary, Figma Tokens'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Design Tokens?', 'options' => ['Атомарные значения дизайна', 'Иконки', 'Шрифты', 'Компоненты'], 'correct' => 'Атомарные значения дизайна'],
                    ['question' => 'Что такое semantic tokens?', 'options' => ['Токены с назначением', 'Случайные цвета', 'Только цвета', 'Шрифты'], 'correct' => 'Токены с назначением'],
                    ['question' => 'Что такое Style Dictionary?', 'options' => ['Инструмент для трансформации токенов', 'Шрифт', 'Фреймворк', 'Библиотека'], 'correct' => 'Инструмент для трансформации токенов'],
                ],
            ],
            'Handoff для разработчиков' => [
                'lessons' => [
                    ['title' => 'Figma Dev Mode', 'description' => 'Инструменты для разработчиков в Figma, inspected.', 'materials' => 'CSS вывод, spacing, размеры'],
                    ['title' => 'Документация', 'description' => 'Спецификации, Storybook, notation, asset export.', 'materials' => 'Export assets, styleguide'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Dev Mode в Figma?', 'options' => ['Режим для разработчиков с CSS и размерами', 'Режим для дизайнеров', 'Режим для тестов', 'Режим для продакшена'], 'correct' => 'Режим для разработчиков с CSS и размерами'],
                    ['question' => 'Что такое handoff?', 'options' => ['Передача дизайна разработчикам', 'Отправка email', 'Тестирование', 'Деплой'], 'correct' => 'Передача дизайна разработчикам'],
                    ['question' => 'Что такое Storybook?', 'options' => ['Документация компонентов', 'База данных', 'CI/CD', 'Редактор'], 'correct' => 'Документация компонентов'],
                ],
            ],
            'Design Systems' => [
                'lessons' => [
                    ['title' => 'Atomic Design', 'description' => 'Атомы, молекулы, организмы, шаблоны, страницы.', 'materials' => 'Brad Frost Atomic Design'],
                    ['title' => 'Управление системой', 'description' => 'Версионирование, governance, обновление, adoption.', 'materials' => 'Change management в дизайне'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Atomic Design?', 'options' => ['Методология проектирования компонентов', 'Фреймворк', 'Язык стилей', 'Библиотека иконок'], 'correct' => 'Методология проектирования компонентов'],
                    ['question' => 'Что такое атом в Atomic Design?', 'options' => ['Мельчайший элемент интерфейса', 'Страница', 'Шаблон', 'Компонент'], 'correct' => 'Мельчайший элемент интерфейса'],
                    ['question' => 'Зачем нужна governance в Design System?', 'options' => ['Управление изменениями и версионирование', 'Создание стилей', 'Тестирование', 'Деплой'], 'correct' => 'Управление изменениями и версионирование'],
                ],
            ],
            'Portfolio' => [
                'lessons' => [
                    ['title' => 'Создание портфолио', 'description' => 'Выбор проектов, структура, описание процесса.', 'materials' => 'Лучшие портфолио дизайнеров'],
                    ['title' => 'Презентация работ', 'description' => 'Case study, процесс + результат, инсайты.', 'materials' => 'Behance, Dribbble, LinkedIn'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое case study?', 'options' => ['Детальный разбор проекта', 'Список навыков', 'Резюме', 'Сертификат'], 'correct' => 'Детальный разбор проекта'],
                    ['question' => 'Где размещать портфолио дизайнера?', 'options' => ['Behance/Dribbble + свой сайт', 'Только GitHub', 'Только LinkedIn', 'Только в PDF'], 'correct' => 'Behance/Dribbble + свой сайт'],
                    ['question' => 'Что важнее в портфолио?', 'options' => ['Процесс + результат', 'Только результат', 'Только количество', 'Только красивые картинки'], 'correct' => 'Процесс + результат'],
                ],
            ],
            'Flutter / Dart' => [
                'lessons' => [
                    ['title' => 'Язык Dart', 'description' => 'Типы, null safety, async/await, классы, миксины.', 'materials' => 'Dart pad, dart vm'],
                    ['title' => 'Основы Flutter', 'description' => 'Widget tree, Material/Cupertino, hot reload, state.', 'materials' => 'flutter create, pub.dev'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Flutter?', 'options' => ['Кроссплатформенный фреймворк от Google', 'Язык программирования', 'База данных', 'CI/CD'], 'correct' => 'Кроссплатформенный фреймворк от Google'],
                    ['question' => 'Что такое hot reload?', 'options' => ['Быстрое обновление UI без перезапуска', 'Перезапуск приложения', 'Кэширование', 'Деплой'], 'correct' => 'Быстрое обновление UI без перезапуска'],
                    ['question' => 'Что такое widget в Flutter?', 'options' => ['Строительный блок UI', 'Функция', 'Переменная', 'Класс данных'], 'correct' => 'Строительный блок UI'],
                ],
            ],
            'Components & Navigation' => [
                'lessons' => [
                    ['title' => 'Навигация', 'description' => 'Navigator, маршруты, передача данных, анимации.', 'materials' => 'Named routes, push/pop'],
                    ['title' => 'Компоненты', 'description' => 'StatelessWidget, StatefulWidget, Key, lifecycle.', 'materials' => 'go_router, auto_route'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Navigator?', 'options' => ['Менеджер навигации между экранами', 'Компонент UI', 'Функция', 'База данных'], 'correct' => 'Менеджер навигации между экранами'],
                    ['question' => 'Что такое go_router?', 'options' => ['Декларативный роутинг для Flutter', 'HTTP клиент', 'ORM', 'Тестовый фреймворк'], 'correct' => 'Декларативный роутинг для Flutter'],
                    ['question' => 'Что такое StatelessWidget?', 'options' => ['Виджет без состояния', 'Виджет с состоянием', 'Функция', 'Класс'], 'correct' => 'Виджет без состояния'],
                ],
            ],
            'Native APIs' => [
                'lessons' => [
                    ['title' => 'React Native Bridge', 'description' => 'Связь JS и нативного кода, native modules.', 'materials' => 'Turbo Modules, JSI'],
                    ['title' => 'Platform Channels', 'description' => 'MethodChannel, EventChannel, передача данных.', 'materials' => 'Платформо-специфичный код'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Platform Channels?', 'options' => ['Мост между Flutter и нативным кодом', 'Каналы связи', 'Навигация', 'Компоненты'], 'correct' => 'Мост между Flutter и нативным кодом'],
                    ['question' => 'Что такое MethodChannel?', 'options' => ['Вызов методов нативного кода из Dart', 'Событийный канал', 'HTTP клиент', 'ORM'], 'correct' => 'Вызов методов нативного кода из Dart'],
                    ['question' => 'Что такое EventChannel?', 'options' => ['Поток событий от нативного кода', 'Вызов методов', 'Навигация', 'Компоненты'], 'correct' => 'Поток событий от нативного кода'],
                ],
            ],
            'Firebase' => [
                'lessons' => [
                    ['title' => 'Firebase Core', 'description' => 'Firestore, Auth, Storage, инициализация.', 'materials' => 'FirebaseOptions, Firebase.initializeApp()'],
                    ['title' => 'Push и Crashlytics', 'description' => 'FCM токены, push уведомления, краш-репорты.', 'materials' => 'firebase_messaging, firebase_crashlytics'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Firestore?', 'options' => ['NoSQL облачная база данных от Google', 'Реляционная БД', 'Кэш', 'Файловое хранилище'], 'correct' => 'NoSQL облачная база данных от Google'],
                    ['question' => 'Для чего нужен Crashlytics?', 'options' => ['Сбор краш-репортов', 'Кэширование', 'Авторизация', 'Навигация'], 'correct' => 'Сбор краш-репортов'],
                    ['question' => 'Что такое FCM?', 'options' => ['Firebase Cloud Messaging', 'Firebase Cache Manager', 'Firebase Code Module', 'Firebase Config Manager'], 'correct' => 'Firebase Cloud Messaging'],
                ],
            ],
            'REST API / GraphQL' => [
                'lessons' => [
                    ['title' => 'HTTP клиенты', 'description' => 'dio, http пакет, interceptors, авторизация.', 'materials' => 'Dio, interceptors, retry'],
                    ['title' => 'GraphQL', 'description' => 'Запросы, мутации, подписки, кэширование.', 'materials' => 'graphql_flutter, ferry'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое GraphQL?', 'options' => ['Язык запросов для API', 'База данных', 'CI/CD', 'Тестовый фреймворк'], 'correct' => 'Язык запросов для API'],
                    ['question' => 'Зачем нужны interceptors?', 'options' => ['Перехват и модификация запросов', 'Навигация', 'Компоненты', 'Логирование'], 'correct' => 'Перехват и модификация запросов'],
                    ['question' => 'REST vs GraphQL?', 'options' => ['REST - несколько эндпоинтов, GraphQL - один', 'Нет отличий', 'GraphQL - несколько эндпоинтов', 'REST - один эндпоинт'], 'correct' => 'REST - несколько эндпоинтов, GraphQL - один'],
                ],
            ],
            'Offline Storage' => [
                'lessons' => [
                    ['title' => 'Локальные хранилища', 'description' => 'Hive, SharedPreferences, SQLite, Isar.', 'materials' => 'Hive vs Isar vs SQLite'],
                    ['title' => 'Синхронизация', 'description' => 'Offline-first подход, синхронизация с сервером, конфликты.', 'materials' => 'Optimistic updates'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Hive?', 'options' => ['Быстрое key-value хранилище для Flutter', 'База данных SQL', 'HTTP клиент', 'Тестовый фреймворк'], 'correct' => 'Быстрое key-value хранилище для Flutter'],
                    ['question' => 'Offline-first означает?', 'options' => ['Приложение работает без интернета', 'Только онлайн', 'Кэширование', 'Логирование'], 'correct' => 'Приложение работает без интернета'],
                    ['question' => 'Что такое SQLite?', 'options' => ['Встроенная реляционная БД', 'Key-value хранилище', 'HTTP клиент', 'Тестовый фреймворк'], 'correct' => 'Встроенная реляционная БД'],
                ],
            ],
            'Testing' => [
                'lessons' => [
                    ['title' => 'Unit тесты', 'description' => 'Тестирование логики, mock, fake, stub.', 'materials' => 'mockito, build_runner'],
                    ['title' => 'Widget тесты', 'description' => 'Тестирование виджетов, find, verify, pump.', 'materials' => 'flutter_test, golden test'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Widget test?', 'options' => ['Тестирование виджетов Flutter', 'Unit тест', 'E2E тест', 'Linter'], 'correct' => 'Тестирование виджетов Flutter'],
                    ['question' => 'Что такое mockito?', 'options' => ['Библиотека для моков', 'ORM', 'CI/CD', 'CLI'], 'correct' => 'Библиотека для моков'],
                    ['question' => 'Что такое golden test?', 'options' => ['Сравнение скриншотов UI', 'Unit тест', 'E2E тест', 'Линтер'], 'correct' => 'Сравнение скриншотов UI'],
            ],
            ],
            'Push Notifications' => [
                'lessons' => [
                    ['title' => 'Firebase Messaging', 'description' => 'FCM токены, foreground/background обработка.', 'materials' => 'firebase_messaging, token management'],
                    ['title' => 'OneSignal', 'description' => 'Сегментация, веб push, аналитика.', 'materials' => 'OneSignal SDK, segments'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое FCM token?', 'options' => ['Уникальный идентификатор устройства для push', 'API ключ', 'Пароль', 'Сессия'], 'correct' => 'Уникальный идентификатор устройства для push'],
                    ['question' => 'Зачем нужна сегментация?', 'options' => ['Отправка уведомлений целевой аудитории', 'Кэширование', 'Логирование', 'Тестирование'], 'correct' => 'Отправка уведомлений целевой аудитории'],
                    ['question' => 'Что такое foreground обработка?', 'options' => ['Обработка push когда приложение открыто', 'Обработка в фоне', 'Отправка email', 'Логирование'], 'correct' => 'Обработка push когда приложение открыто'],
                ],
            ],
            'App Store Deploy' => [
                'lessons' => [
                    ['title' => 'Google Play', 'description' => 'Play Console, APK/AAB, внутреннее тестирование.', 'materials' => 'Play Console, signed bundles'],
                    ['title' => 'App Store', 'description' => 'App Store Connect, TestFlight, review process.', 'materials' => 'Apple Developer Program'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое AAB?', 'options' => ['Android App Bundle', 'Android Archive Bundle', 'App Activity Bundle', 'Application Binary Bundle'], 'correct' => 'Android App Bundle'],
                    ['question' => 'Что такое TestFlight?', 'options' => ['Платформа бета-тестирования iOS приложений', 'Облачное хранилище', 'CI/CD инструмент', 'Редактор кода'], 'correct' => 'Платформа бета-тестирования iOS приложений'],
                    ['question' => 'Зачем нужен review в App Store?', 'options' => ['Проверка приложения на соответствие guidelines', 'Тестирование', 'Логирование', 'Деплой'], 'correct' => 'Проверка приложения на соответствие guidelines'],
                ],
            ],
            'Performance' => [
                'lessons' => [
                    ['title' => 'Оптимизация React Native', 'description' => 'Hermes, flat lists, memoization, avoid re-renders.', 'materials' => 'Flipper, React DevTools'],
                    ['title' => 'Оптимизация Flutter', 'description' => 'const constructors, RepaintBoundary, DevTools.', 'materials' => 'Flutter DevTools, timeline'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Hermes?', 'options' => ['JS движок для React Native', 'HTTP клиент', 'ORM', 'Тестовый фреймворк'], 'correct' => 'JS движок для React Native'],
                    ['question' => 'Зачем нужны const constructor в Flutter?', 'options' => ['Пересоздание виджетов не требуется', 'Для стилей', 'Для навигации', 'Для тестов'], 'correct' => 'Пересоздание виджетов не требуется'],
                    ['question' => 'Что такое DevTools?', 'options' => ['Инструменты для профилирования и отладки', 'База данных', 'CI/CD', 'Редактор кода'], 'correct' => 'Инструменты для профилирования и отладки'],
                ],
            ],
            'CI/CD (Fastlane)' => [
                'lessons' => [
                    ['title' => 'Fastlane', 'description' => 'Автоматизация сборки, скриншоты, деплой в маркеты.', 'materials' => 'Fastfile, Match, Supply'],
                    ['title' => 'EAS Build', 'description' => 'Expo Application Services, облако сборки React Native.', 'materials' => 'eas.json, build profiles'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Fastlane?', 'options' => ['Инструмент автоматизации для iOS/Android', 'База данных', 'CI/CD платформа', 'Редактор кода'], 'correct' => 'Инструмент автоматизации для iOS/Android'],
                    ['question' => 'Что такое EAS Build?', 'options' => ['Облачная сборка Expo приложений', 'Локальная сборка', 'Тестирование', 'Логирование'], 'correct' => 'Облачная сборка Expo приложений'],
                    ['question' => 'Что такое Match в Fastlane?', 'options' => ['Управление сертификатами и профилями', 'Тестирование', 'Деплой', 'Мониторинг'], 'correct' => 'Управление сертификатами и профилями'],
                ],
            ],
            'Memory Management' => [
                'lessons' => [
                    ['title' => 'Указатели и ссылки', 'description' => 'Raw указатели, ссылки, const, void*, арифметика указателей.', 'materials' => 'Stack vs heap, new/delete'],
                    ['title' => 'Умные указатели', 'description' => 'unique_ptr, shared_ptr, weak_ptr, make_shared.', 'materials' => 'RAII, ownership semantics'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое RAII?', 'options' => ['Resource Acquisition Is Initialization', 'Random Access In Immediate Interval', 'Runtime Application Interface', 'Rapid Application Integration'], 'correct' => 'Resource Acquisition Is Initialization'],
                    ['question' => 'В чём разница unique_ptr vs shared_ptr?', 'options' => ['unique_ptr - единственный владелец, shared_ptr - общий', 'Нет разницы', 'unique_ptr быстрее', 'shared_ptr не использует подсчёт'], 'correct' => 'unique_ptr - единственный владелец, shared_ptr - общий'],
                    ['question' => 'Что делает delete?', 'options' => ['Освобождает память и вызывает деструктор', 'Удаляет переменную', 'Очищает экран', 'Завершает программу'], 'correct' => 'Освобождает память и вызывает деструктор'],
                ],
            ],
            'C++ Templates' => [
                'lessons' => [
                    ['title' => 'Шаблоны функций', 'description' => 'template<typename T>, автоматический вывод типов, специализация.', 'materials' => 'Function templates, type deduction'],
                    ['title' => 'Шаблоны классов', 'description' => 'Шаблонные классы, неявная инстанциация, variadic templates.', 'materials' => 'std::vector как шаблонный класс'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое template в C++?', 'options' => ['Параметризованный код', 'Макрос', 'Класс', 'Функция'], 'correct' => 'Параметризованный код'],
                    ['question' => 'Что такое SFINAE?', 'options' => ['Substitution Failure Is Not An Error', 'Simple Function Naming And Evaluation', 'Standard Framework For Internal Application', 'System For Internal Network Access'], 'correct' => 'Substitution Failure Is Not An Error'],
                    ['question' => 'Что такое variadic templates?', 'options' => ['Шаблоны с переменным числом параметров', 'Шаблоны с фиксированным числом параметров', 'Макросы', 'Классы'], 'correct' => 'Шаблоны с переменным числом параметров'],
                ],
            ],
            'STL' => [
                'lessons' => [
                    ['title' => 'Контейнеры', 'description' => 'vector, list, deque, map, set, unordered_map.', 'materials' => 'Sequence vs associative контейнеры'],
                    ['title' => 'Итераторы', 'description' => 'input, output, forward, bidirectional, random access итераторы.', 'materials' => 'begin(), end(), range-based for'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое STL?', 'options' => ['Standard Template Library', 'System Template Language', 'Standard Type Library', 'System Type Linker'], 'correct' => 'Standard Template Library'],
                    ['question' => 'Какой контейнер обеспечивает O(1) доступ по индексу?', 'options' => ['list', 'vector', 'set', 'map'], 'correct' => 'vector'],
                    ['question' => 'Что такое итератор?', 'options' => ['Объект для обхода контейнера', 'Контейнер', 'Функция', 'Класс'], 'correct' => 'Объект для обхода контейнера'],
                ],
            ],
            'Data Structures' => [
                'lessons' => [
                    ['title' => 'Линейные структуры', 'description' => 'Стек, очередь, дек, связный список.', 'materials' => 'std::stack, std::queue, std::list'],
                    ['title' => 'Деревья и графы', 'description' => 'BST, AVL, красно-чёрные деревья, графы.', 'materials' => 'Обход в глубину и ширину'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое стек?', 'options' => ['LIFO структура данных', 'FIFO структура данных', 'Дерево', 'Граф'], 'correct' => 'LIFO структура данных'],
                    ['question' => 'Что такое BST?', 'options' => ['Binary Search Tree', 'Basic System Tool', 'Binary Storage Tree', 'Buffer System Thread'], 'correct' => 'Binary Search Tree'],
                    ['question' => 'Что такое BFS?', 'options' => ['Breadth-First Search', 'Basic File System', 'Binary First Search', 'Buffer First Search'], 'correct' => 'Breadth-First Search'],
            ],
            ],
            'Algorithms' => [
                'lessons' => [
                    ['title' => 'Сортировки', 'description' => 'Bubble sort, quick sort, merge sort, heap sort.', 'materials' => 'std::sort, time complexity'],
                    ['title' => 'Поиск и графы', 'description' => 'Binary search, Dijkstra, BFS, DFS.', 'materials' => 'std::find, алгоритмы на графах'],
                ],
                'quizzes' => [
                    ['question' => 'Какая сложность quick sort в среднем?', 'options' => ['O(n)', 'O(n log n)', 'O(n^2)', 'O(log n)'], 'correct' => 'O(n log n)'],
                    ['question' => 'Что такое Dijkstra?', 'options' => ['Алгоритм поиска кратчайшего пути', 'Сортировка', 'Структура данных', 'Шаблон'], 'correct' => 'Алгоритм поиска кратчайшего пути'],
                    ['question' => 'Что такое binary search?', 'options' => ['Бинарный поиск в отсортированном массиве', 'Линейный поиск', 'Поиск в графе', 'Поиск в дереве'], 'correct' => 'Бинарный поиск в отсортированном массиве'],
            ],
            ],
            'Design Patterns' => [
                'lessons' => [
                    ['title' => 'Порождающие паттерны', 'description' => 'Singleton, Factory, Abstract Factory, Builder, Prototype.', 'materials' => 'Когда применять каждый паттерн'],
                    ['title' => 'Структурные и поведенческие', 'description' => 'Adapter, Decorator, Observer, Strategy, Command.', 'materials' => 'Gang of Four, SOLID'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Singleton?', 'options' => ['Паттерн: один экземпляр класса', 'Фабрика', 'Декоратор', 'Наблюдатель'], 'correct' => 'Паттерн: один экземпляр класса'],
                    ['question' => 'Что такое Observer?', 'options' => ['Паттерн: подписка на события', 'Фабрика', 'Адаптер', 'Стратегия'], 'correct' => 'Паттерн: подписка на события'],
                    ['question' => 'Что такое Factory?', 'options' => ['Паттерн: создание объектов через фабричный метод', 'Синглтон', 'Декоратор', 'Команда'], 'correct' => 'Паттерн: создание объектов через фабричный метод'],
            ],
            ],
            'Multithreading' => [
                'lessons' => [
                    ['title' => 'Потоки в C++', 'description' => 'std::thread, std::async, join, detach.', 'materials' => 'Потоки и планировщик ОС'],
                    ['title' => 'Синхронизация', 'description' => 'std::mutex, lock_guard, condition_variable, atomic.', 'materials' => 'Data races, deadlocks'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое std::thread?', 'options' => ['Поток выполнения', 'Мьютекс', 'Атомарная переменная', 'Условие'], 'correct' => 'Поток выполнения'],
                    ['question' => 'Что такое mutex?', 'options' => ['Взаимное исключение для синхронизации', 'Поток', 'Шаблон', 'Контейнер'], 'correct' => 'Взаимное исключение для синхронизации'],
                    ['question' => 'Что такое race condition?', 'options' => ['Гонка данных при параллельном доступе', 'Сортировка', 'Поиск', 'Условная компиляция'], 'correct' => 'Гонка данных при параллельном доступе'],
                ],
            ],
            'Build Systems' => [
                'lessons' => [
                    ['title' => 'CMake', 'description' => 'CMakeLists.txt, targets, libraries, find_package.', 'materials' => 'cmake_minimum_required, add_executable'],
                    ['title' => 'Конкурентные сборки', 'description' => 'ccache, Ninja, parallel builds.', 'materials' => 'make vs ninja vs CMake generators'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое CMake?', 'options' => ['Генератор систем сборки', 'Компилятор', 'Редактор кода', 'Отладчик'], 'correct' => 'Генератор систем сборки'],
                    ['question' => 'Что такое ccache?', 'options' => ['Кэширование сборки для ускорения', 'Компилятор', 'Линковщик', 'Отладчик'], 'correct' => 'Кэширование сборки для ускорения'],
                    ['question' => 'Что такое Ninja?', 'options' => ['Быстрая система сборки', 'Пакетный менеджер', 'Редактор', 'Тестовый фреймворк'], 'correct' => 'Быстрая система сборки'],
                ],
            ],
            'STL Algorithms' => [
                'lessons' => [
                    ['title' => 'Алгоритмы STL', 'description' => 'sort, find, transform, accumulate, for_each.', 'materials' => '<algorithm> header, ranges'],
                    ['title' => 'Оптимизация и композиция', 'description' => 'Лямбды с алгоритмами, pipe, view adapters.', 'materials' => 'C++20 ranges, view::transform'],
                ],
                'quizzes' => [
                    ['question' => 'Что делает std::sort?', 'options' => ['Сортирует диапазон элементов', 'Находит элемент', 'Трансформирует данные', 'Копирует элементы'], 'correct' => 'Сортирует диапазон элементов'],
                    ['question' => 'Что делает std::transform?', 'options' => ['Применяет функцию к каждому элементу', 'Сортирует', 'Фильтрует', 'Копирует'], 'correct' => 'Применяет функцию к каждому элементу'],
                    ['question' => 'Что такое лямбда в C++?', 'options' => ['Анонимная функция', 'Класс', 'Шаблон', 'Макрос'], 'correct' => 'Анонимная функция'],
                ],
            ],
            'Modern C++ (17/20)' => [
                'lessons' => [
                    ['title' => 'C++17', 'description' => 'Structured bindings, std::optional, std::variant, filesystem.', 'materials' => 'if constexpr, fold expressions'],
                    ['title' => 'C++20', 'description' => 'Concepts, ranges, coroutines, modules, three-way comparison.', 'materials' => 'Requires expressions, co_await'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое structured bindings?', 'options' => ['Развёртывание структур при инициализации', 'Шаблоны', 'Контейнеры', 'Лямбды'], 'correct' => 'Развёртывание структур при инициализации'],
                    ['question' => 'Что такое concepts в C++20?', 'options' => ['Ограничения на шаблонные параметры', 'Классы', 'Функции', 'Контейнеры'], 'correct' => 'Ограничения на шаблонные параметры'],
                    ['question' => 'Что такое ranges в C++20?', 'options' => ['Диапазоны с адаптерами для алгоритмов', 'Новые контейнеры', 'Шаблоны', 'Лямбды'], 'correct' => 'Диапазоны с адаптерами для алгоритмов'],
                ],
            ],
            'Game Engines' => [
                'lessons' => [
                    ['title' => 'Unreal Engine', 'description' => 'Blueprint (визуальное программирование), C++ интеграция, UE Blueprint, материалы.', 'materials' => 'Unreal Editor, Gameplay Framework'],
                    ['title' => 'SFML', 'description' => '2D графика, аудио, окна, события.', 'materials' => 'sf::RenderWindow, sf::Sprite'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Unreal Engine?', 'options' => ['Движок для 3D игр от Epic Games', 'Язык программирования', 'База данных', 'Текстовый редактор'], 'correct' => 'Движок для 3D игр от Epic Games'],
                    ['question' => 'Что такое SFML?', 'options' => ['Библиотека для 2D игр на C++', '3D движок', 'Тестовый фреймворк', 'Компилятор'], 'correct' => 'Библиотека для 2D игр на C++'],
                    ['question' => 'Что такое игровой цикл?', 'options' => ['Основной цикл обработки ввода, обновления, рендера', 'Цикл for', 'Цикл while', 'Рекурсия'], 'correct' => 'Основной цикл обработки ввода, обновления, рендера'],
                ],
            ],
            'Competitive Programming' => [
                'lessons' => [
                    ['title' => 'Основы CP', 'description' => 'Ввод/вывод, time complexity, простые задачи.', 'materials' => 'Codeforces, AtCoder'],
                    ['title' => 'Типы задач', 'description' => 'Графы, DP, жадные алгоритмы, бинарный поиск.', 'materials' => 'Соревновательное программирование'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое time complexity?', 'options' => ['Оценка времени работы алгоритма', 'Время компиляции', 'Время выполнения программы', 'Время загрузки'], 'correct' => 'Оценка времени работы алгоритма'],
                    ['question' => 'Какие структуры данных часто используются в CP?', 'options' => ['Стек, очередь, дерево отрезков', 'Только массивы', 'Только строки', 'Только файлы'], 'correct' => 'Стек, очередь, дерево отрезков'],
                    ['question' => 'Что такое greedy алгоритм?', 'options' => ['Жадный алгоритм: локально оптимальный выбор', 'Случайный алгоритм', 'Рекурсивный алгоритм', 'Линейный алгоритм'], 'correct' => 'Жадный алгоритм: локально оптимальный выбор'],
                ],
            ],
            'Open Source Projects' => [
                'lessons' => [
                    ['title' => 'Вклад в Open Source', 'description' => 'CONTRIBUTING.md, issue tracking, PR workflow.', 'materials' => 'GitHub contribution guide'],
                    ['title' => 'Поиск проектов', 'description' => 'good first issue, forks, stars, активность проекта.', 'materials' => 'GitHub Explore, Up For Grabs'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое CONTRIBUTING.md?', 'options' => ['Файл с инструкциями для контрибьюторов', 'README файла', 'Лицензия', 'Changelog'], 'correct' => 'Файл с инструкциями для контрибьюторов'],
                    ['question' => 'Что такое forks?', 'options' => ['Копия чужого репозитория для изменений', 'Ветка', 'Коммит', 'Тег'], 'correct' => 'Копия чужого репозитория для изменений'],
                    ['question' => 'Что такое Pull Request?', 'options' => ['Запрос на слияние изменений', 'Запрос на удаление', 'Запрос на клонирование', 'Запрос на тестирование'], 'correct' => 'Запрос на слияние изменений'],
                ],
            ],

            // ═══════════════════════════════════════════════════════
            // FRONTEND DEVELOPER — exact node titles from FrontendRoadmapSeeder
            // ═══════════════════════════════════════════════════════
            'HTML5 Семантика' => [
                'lessons' => [
                    ['title' => 'Семантические теги', 'description' => 'header, nav, main, article, section, aside, footer, figure, figcaption.', 'materials' => 'MDN: Semantic HTML, HTML5 Doctor'],
                    ['title' => 'Роль семантики', 'description' => 'Доступность, SEO, структура документа, screen readers.', 'materials' => 'WAI-ARIA roles, логические блоки'],
                ],
                'quizzes' => [
                    ['question' => 'Для чего семантический HTML?', 'options' => ['Для красоты', 'Для доступности и SEO', 'Для скорости', 'Для совместимости'], 'correct' => 'Для доступности и SEO'],
                    ['question' => 'Какой тег для основного контента?', 'options' => ['<div>', '<main>', '<section>', '<span>'], 'correct' => '<main>'],
                    ['question' => 'Сколько тегов <main> допускается?', 'options' => ['Неограниченно', 'Один', 'Два', 'Три'], 'correct' => 'Один'],
            ],
            ],
            'HTML Формы' => [
                'lessons' => [
                    ['title' => 'Основы форм', 'description' => 'input, select, textarea, button, label, fieldset.', 'materials' => 'MDN: Forms Guide, HTML Academy'],
                    ['title' => 'Валидация форм', 'description' => 'required, pattern, type attributes, JavaScript валидация.', 'materials' => 'Constraint Validation API'],
                ],
                'quizzes' => [
                    ['question' => 'Для чего тег <label>?', 'options' => ['Заголовок', 'Связь текста с полем ввода', 'Кнопка', 'Изображение'], 'correct' => 'Связь текста с полем ввода'],
                    ['question' => 'Как сделать поле обязательным?', 'options' => ['required', 'mandatory', 'needed', 'important'], 'correct' => 'required'],
                    ['question' => 'Что такое fieldset?', 'options' => ['Группа полей формы', 'Таблица', 'Список', 'Блок'], 'correct' => 'Группа полей формы'],
            ],
            ],
            'Таблицы и списки' => [
                'lessons' => [
                    ['title' => 'HTML таблицы', 'description' => 'table, tr, td, th, thead, tbody, caption, colspan/rowspan.', 'materials' => 'MDN: HTML Tables'],
                    ['title' => 'Списки', 'description' => 'ul, ol, dl, вложенные списки, кастомные маркеры.', 'materials' => 'MDN: HTML Lists'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое <th>?', 'options' => ['Обычная ячейка', 'Заголовочная ячейка', 'Строка', 'Столбец'], 'correct' => 'Заголовочная ячейка'],
                    ['question' => 'Для чего ul vs ol?', 'options' => ['Ненумерованный vs нумерованный список', 'Одинаковые', 'Таблицы', 'Формы'], 'correct' => 'Ненумерованный vs нумерованный список'],
                    ['question' => 'Что делает colspan?', 'options' => ['Объединяет ячейки по горизонтали', 'Объединяет по вертикали', 'Удаляет строку', 'Добавляет столбец'], 'correct' => 'Объединяет ячейки по горизонтали'],
            ],
            ],
            'CSS Box Model' => [
                'lessons' => [
                    ['title' => 'Структура Box Model', 'description' => 'content, padding, border, margin, box-sizing.', 'materials' => 'MDN: Box Model, CSS Tricks'],
                    ['title' => 'Типы блоков', 'description' => 'block, inline, inline-block, content-box vs border-box.', 'materials' => 'display property, box-sizing'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое box-sizing: border-box?', 'options' => ['Padding/border включены в width', 'Только content', 'Без отступов', 'С рамкой'], 'correct' => 'Padding/border включены в width'],
                    ['question' => 'Что такое margin?', 'options' => ['Внутренний отступ', 'Внешний отступ', 'Рамка', 'Содержимое'], 'correct' => 'Внешний отступ'],
                    ['question' => 'Какое свойство для рамки?', 'options' => ['margin', 'padding', 'border', 'outline'], 'correct' => 'border'],
            ],
            ],
            'Linux основы' => [
                'lessons' => [
                    ['title' => 'Файловая система', 'description' => '/, /home, /etc, /var, /usr, /tmp, права доступа.', 'materials' => 'ls, cd, pwd, mkdir, rm, cp, mv'],
                    ['title' => 'Права доступа', 'description' => 'chmod, chown, rwx, цифровые права, директории.', 'materials' => 'chmod 755, chown user:group'],
                ],
                'quizzes' => [
                    ['question' => 'Что означает chmod 755?', 'options' => ['Владелец rwx, группа rx, другие rx', 'Все rwx', 'Только чтение', 'Нет доступа'], 'correct' => 'Владелец rwx, группа rx, другие rx'],
                    ['question' => 'Где находятся конфиги?', 'options' => ['/home', '/etc', '/var', '/tmp'], 'correct' => '/etc'],
                    ['question' => 'Какая команда показывает файлы?', 'options' => ['cd', 'ls', 'pwd', 'cat'], 'correct' => 'ls'],
            ],
            ],
            'Bash Скрипты' => [
                'lessons' => [
                    ['title' => 'Основы Bash', 'description' => 'Shebang, переменные, условия if/case, циклы for/while.', 'materials' => 'Bash Tutorial, Advanced Bash'],
                    ['title' => 'Продвинутый Bash', 'description' => 'sed/awk, trap, cron, обработка аргументов, функции.', 'materials' => 'xargs, пайпы, exit codes'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое shebang?', 'options' => ['Комментарий', '#!/bin/bash интерпретатор', 'Переменная', 'Функция'], 'correct' => '#!/bin/bash интерпретатор'],
                    ['question' => 'Что делает $1 в Bash?', 'options' => ['Первый аргумент скрипта', 'Номер строки', 'Код возврата', 'Имя файла'], 'correct' => 'Первый аргумент скрипта'],
                    ['question' => 'Как запустить скрипт?', 'options' => ['run script', 'bash script.sh', 'execute script', 'start script'], 'correct' => 'bash script.sh'],
            ],
            ],
            'Медиа-запросы' => [
                'lessons' => [
                    ['title' => 'Основы медиа-запросов', 'description' => '@media, min-width, max-width, mobile-first.', 'materials' => 'MDN: Media Queries, Can I Use'],
                    ['title' => 'Breakpoints и типы', 'description' => 'print, screen, orientation, aspect-ratio.', 'materials' => 'Размеры экранов, responsive breakpoints'],
                ],
                'quizzes' => [
                    ['question' => 'Как задать стиль для экранов < 768px?', 'options' => ['@media (max-width: 768px)', '@screen (768px)', '@viewport (768px)', '@device (768px)'], 'correct' => '@media (max-width: 768px)'],
                    ['question' => 'Что такое mobile-first?', 'options' => ['Десктоп сначала', 'Мобильные стили сначала', 'Только мобильные', 'Нет стилей'], 'correct' => 'Мобильные стили сначала'],
                    ['question' => 'Для чего min-width?', 'options' => ['Минимальная ширина экрана', 'Максимальная ширина', 'Высота', 'Ширина элемента'], 'correct' => 'Минимальная ширина экрана'],
            ],
            ],
            'JavaScript DOM' => [
                'lessons' => [
                    ['title' => 'Основы DOM', 'description' => 'document, querySelector, createElement, appendChild.', 'materials' => 'MDN: DOM Introduction, DOM Enlightenment'],
                    ['title' => 'События', 'description' => 'addEventListener, event delegation, bubbling/capturing.', 'materials' => 'JavaScript.info: DOM, Event reference'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое DOM?', 'options' => ['Объектная модель документа', 'Язык стилей', 'Протокол', 'База данных'], 'correct' => 'Объектная модель документа'],
                    ['question' => 'Для чего querySelector?', 'options' => ['Поиск элемента по CSS-селектору', 'Создание элемента', 'Удаление элемента', 'Копирование'], 'correct' => 'Поиск элемента по CSS-селектору'],
                    ['question' => 'Что такое event delegation?', 'options' => ['Делегирование событий родителю', 'Создание событий', 'Удаление событий', 'Кэширование'], 'correct' => 'Делегирование событий родителю'],
            ],
            ],
            'Node.js Основы' => [
                'lessons' => [
                    ['title' => 'Введение в Node.js', 'description' => 'V8, модули, fs, path, events, event loop.', 'materials' => 'CommonJS vs ES Modules, Node.js docs'],
                    ['title' => 'Express.js', 'description' => 'Маршруты, middleware, REST API, шаблоны.', 'materials' => 'Express.js guide, req/res/next'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Node.js?', 'options' => ['Браузер', 'Среда выполнения JS на сервере', 'База данных', 'Фреймворк'], 'correct' => 'Среда выполнения JS на сервере'],
                    ['question' => 'Что такое event loop?', 'options' => ['Цикл обработки событий', 'Цикл for', 'Цикл жизни', 'Цикл запросов'], 'correct' => 'Цикл обработки событий'],
                    ['question' => 'Для чего middleware в Express?', 'options' => ['Обработка запроса до маршрута', 'Создание БД', 'Генерация HTML', 'Управление сессиями'], 'correct' => 'Обработка запроса до маршрута'],
            ],
            ],
            'HTML/CSS Экзамен' => [],

            // Backend Developer — mismatched titles
            'Docker для PHP' => [
                'lessons' => [
                    ['title' => 'Dockerfile для PHP', 'description' => 'Многостадийные сборки, php-fpm, extensions, кэширование.', 'materials' => 'docker-compose.yml, php image'],
                    ['title' => 'Docker Compose для PHP', 'description' => 'MySQL, Redis, Nginx, volumes, hot-reload.', 'materials' => 'depends_on, healthcheck'],
                ],
                'quizzes' => [
                    ['question' => 'Зачем Docker для PHP?', 'options' => ['Изоляция окружения', 'Ускорение разработки', 'Оба варианта', 'Ни один'], 'correct' => 'Оба варианта'],
                    ['question' => 'Что такое php-fpm?', 'options' => ['FastCGI Process Manager для PHP', 'Фреймворк', 'ORM', 'CLI'], 'correct' => 'FastCGI Process Manager для PHP'],
                    ['question' => 'Для чего depends_on?', 'options' => ['Зависимости запуска сервисов', 'Удаление контейнеров', 'Кэширование', 'Логирование'], 'correct' => 'Зависимости запуска сервисов'],
            ],
            ],

            // ═══════════════════════════════════════════════════════
            // FULLSTACK DEVELOPER — exact titles from AllRoadmapsSeeder
            // ═══════════════════════════════════════════════════════
            'HTML / CSS' => [
                'lessons' => [
                    ['title' => 'Основы HTML/CSS', 'description' => 'Теги, атрибуты, селекторы, Box Model, позиционирование.', 'materials' => 'MDN HTML, MDN CSS'],
                    ['title' => 'Семантический HTML', 'description' => 'header, nav, main, article, footer, семантические теги.', 'materials' => 'Доступность, SEO оптимизация'],
                ],
                'quizzes' => [
                    ['question' => 'Какой тег для навигации?', 'options' => ['<nav>', '<menu>', '<links>', '<navigation>'], 'correct' => '<nav>'],
                    ['question' => 'Что делает display: flex?', 'options' => ['Включает гибкую раскладку', 'Скрывает элемент', 'Создаёт таблицу', 'Удаляет стили'], 'correct' => 'Включает гибкую раскладку'],
                    ['question' => 'Какой CSS-селектор для класса?', 'options' => ['#', '.', '@', '*'], 'correct' => '.'],
                ],
            ],
            'Responsive Design' => [
                'lessons' => [
                    ['title' => 'Медиа-запросы', 'description' => '@media, min-width, max-width, mobile-first approach.', 'materials' => 'Breakpoints, viewport'],
                    ['title' => 'Адаптивные изображения', 'description' => 'srcset, picture, art direction, lazy loading.', 'materials' => '<img loading="lazy">, WebP'],
                ],
                'quizzes' => [
                    ['question' => 'Как задать стиль для экранов < 768px?', 'options' => ['@media (max-width: 768px)', '@screen (768px)', '@viewport (768px)', '@device (768px)'], 'correct' => '@media (max-width: 768px)'],
                    ['question' => 'Что такое mobile-first?', 'options' => ['Десктоп сначала', 'Мобильные стили сначала', 'Только мобильные', 'Нет стилей'], 'correct' => 'Мобильные стили сначала'],
                    ['question' => 'Что делает loading="lazy"?', 'options' => ['Отложенная загрузка', 'Кэширование', 'Сжатие', 'Удаление'], 'correct' => 'Отложенная загрузка'],
            ],
            ],
            'React / Vue' => [
                'lessons' => [
                    ['title' => 'Основы React', 'description' => 'Компоненты, JSX, пропсы, состояние, хуки.', 'materials' => 'React docs, Virtual DOM'],
                    ['title' => 'Основы Vue', 'description' => 'CreateApp, template, directives, реактивность.', 'materials' => 'Vue docs, Options/Composition API'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое JSX?', 'options' => ['Язык стилей', 'JavaScript XML', 'JSON расширение', 'Тип данных'], 'correct' => 'JavaScript XML'],
                    ['question' => 'Для чего useState?', 'options' => ['Эффекты', 'Состояние компонента', 'Роутинг', 'Формы'], 'correct' => 'Состояние компонента'],
                    ['question' => 'Что такое Vue?', 'options' => ['Фреймворк для UI', 'База данных', 'Текстовый редактор', 'Браузер'], 'correct' => 'Фреймворк для UI'],
                ],
            ],
            'Auth & JWT' => [
                'lessons' => [
                    ['title' => 'JWT аутентификация', 'description' => 'JSON Web Token, header, payload, signature, expiration.', 'materials' => 'jwt.io, Laravel Sanctum'],
                    ['title' => 'OAuth2 и токены', 'description' => 'Access/refresh tokens, stateless auth, API keys.', 'materials' => 'OAuth2 flow, token rotation'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое JWT?', 'options' => ['JSON Web Token', 'JavaScript Web Tool', 'Java Workflow Technology', 'Just Write Text'], 'correct' => 'JSON Web Token'],
                    ['question' => 'Из чего состоит JWT?', 'options' => ['Header, Payload, Signature', 'Username, Password', 'Key, Value', 'GET, POST'], 'correct' => 'Header, Payload, Signature'],
                    ['question' => 'Для чего refresh token?', 'options' => ['Обновление access token', 'Удаление токена', 'Кэширование', 'Логирование'], 'correct' => 'Обновление access token'],
            ],
            ],
            'Performance & SEO' => [
                'lessons' => [
                    ['title' => 'Производительность веба', 'description' => 'LCP, FID, CLS, Core Web Vitals, lazy loading.', 'materials' => 'Lighthouse, PageSpeed Insights'],
                    ['title' => 'SEO оптимизация', 'description' => 'Meta теги, структура URL, sitemap, robots.txt.', 'materials' => 'Open Graph, Schema.org'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое LCP?', 'options' => ['Largest Contentful Paint', 'Lowest Connection Point', 'Load Complete Page', 'Last CSS Property'], 'correct' => 'Largest Contentful Paint'],
                    ['question' => 'Зачем нужен sitemap.xml?', 'options' => ['Для стилей', 'Для индексации страниц', 'Для скриптов', 'Для шрифтов'], 'correct' => 'Для индексации страниц'],
                    ['question' => 'Что такое lazy loading?', 'options' => ['Отложенная загрузка', 'Кэширование', 'Сжатие', 'Удаление'], 'correct' => 'Отложенная загрузка'],
            ],
            ],

            // ═══════════════════════════════════════════════════════
            // DEVOPS ENGINEER — mismatched titles
            // ═══════════════════════════════════════════════════════
            'Linux Fundamentals' => [
                'lessons' => [
                    ['title' => 'Файловая система Linux', 'description' => 'Linux использует иерархическую файловую систему. / — корень, /home — домашние директории пользователей, /etc — конфигурационные файлы, /var — логи и временные данные, /usr — установленное ПО, /tmp — временные файлы. Права доступа: chmod 755 (rwxr-xr-x) — владелец: полный доступ, группа/остальные: чтение и запуск. chown user:group file — смена владельца. ls -la — подробный список с правами. Файловые атрибуты: immutable (chattr +i), append-only.', 'materials' => 'Linux file system hierarchy, chmod — права доступа, chown — смена владельца'],
                    ['title' => 'Процессы и сервисы', 'description' => 'ps aux — список процессов, top/htop — мониторинг в реальном времени, kill PID — завершение. systemctl start/stop/restart/status nginx — управление сервисами. systemd — система инициализации (заменяет init). journalctl -u nginx — просмотр логов сервиса. crontab -e — планировщик задач (0 2 * * * /path/to/script.sh — каждый день в 2:00). nice/renice — приоритет процессов.进程管理: foreground vs background (./script &).', 'materials' => 'systemctl — управление сервисами, crontab — планировщик, process management'],
                ],
                'quizzes' => [
                    ['question' => 'Как изменить права доступа?', 'options' => ['chmod', 'chown', 'chgrp', 'chperm'], 'correct' => 'chmod'],
                    ['question' => 'Как показать работающие процессы?', 'options' => ['ls', 'ps', 'top', 'Both ps and top'], 'correct' => 'Both ps and top'],
                    ['question' => 'Где находятся системные конфиги?', 'options' => ['/home', '/etc', '/var', '/tmp'], 'correct' => '/etc'],
                ],
            ],

            // ═══════════════════════════════════════════════════════
            // PYTHON DEVELOPER — exact titles from AllRoadmapsSeeder
            // ═══════════════════════════════════════════════════════
            'Python Основы' => [
                'lessons' => [
                    ['title' => 'Синтаксис Python', 'description' => 'Python — интерпретируемый язык с динамической типизацией. Переменные: name = "Иван" (без объявления типа). Типы: str, int, float, bool, list, dict, tuple, set, None. Вывод: print("Hello"), f-строки: f"Привет, {name}". Операторы: + - * / //(целочисленное), %(остаток), **(степень). PEP 8 — стиль кодирования: 4 пробела, 79 символов в строке. Комментарии: # однострочный, """многострочный""". input() — ввод, type() — определение типа.', 'materials' => 'Python — документация, PEP 8 — стиль, Data types — обзор'],
                    ['title' => 'Структуры данных', 'description' => 'list — изменяемый список: arr = [1, 2, 3], arr.append(4), arr[0]. Tuple — неизменяемый: tup = (1, 2). Dict — словарь: d = {"key": "value"}, d["key"], d.get("key", default). Set — множество уникальных: s = {1, 2, 3}, s.add(4). List comprehension: [x**2 for x in range(10) if x > 5]. Dict comprehension: {k: v for k, v in items}. Генераторы: (x**2 for x in range(10)) — ленивые (экономят память). enumerate(), zip(), map(), filter().', 'materials' => 'Lists, Dicts, Sets — документация, Comprehensions, Generators'],
                ],
                'quizzes' => [
                    ['question' => 'Как объявить переменную в Python?', 'options' => ['var', 'let', 'int', 'Присваивание'], 'correct' => 'Присваивание'],
                    ['question' => 'Что такое list comprehension?', 'options' => ['Цикл', 'Сокращённое создание списков', 'Функция', 'Класс'], 'correct' => 'Сокращённое создание списков'],
                    ['question' => 'Что такое PEP 8?', 'options' => ['Язык', 'Стиль кодирования Python', 'Библиотека', 'Протокол'], 'correct' => 'Стиль кодирования Python'],
            ],
            ],
            'Python OOP' => [
                'lessons' => [
                    ['title' => 'Классы Python', 'description' => 'class User: def __init__(self, name): self.name = name. self — ссылка на экземпляр (аналог this в JS). Методы: def greet(self): return f"Привет, {self.name}". @property — геттер: @property def age(self): return self._age. @staticmethod — без доступа к self. @classmethod — cls вместо self (фабричные методы). __str__ — строковое представление. __repr__ — для разработчиков. dunder методы: __len__, __eq__, __add__.', 'materials' => 'Classes — документация, @property, Magic methods, __init__'],
                    ['title' => 'Наследование и полиморфизм', 'description' => 'class Animal: def speak(self): ... class Dog(Animal): def speak(self): return "Гав!". super().__init__() — вызов родительского конструктора. MRO (Method Resolution Order) — порядок поиска методов. Duck typing: "Если утка ходает как утка..." — тип определяется поведением, не классом. ABC (Abstract Base Class) — абстрактные классы с @abstractmethod. Protocol — структурная типизация. Mixins — повторное использование кода (class TimestampMixin).', 'materials' => 'Inheritance — документация, ABC, Protocols, MRO — порядок'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое self?', 'options' => ['Глобальная переменная', 'Ссылка на экземпляр класса', 'Статический метод', 'Константа'], 'correct' => 'Ссылка на экземпляр класса'],
                    ['question' => 'Что такое duck typing?', 'options' => ['Утинная типизация', 'Тип определяется поведением', 'Жёсткая типизация', 'Проверка типов'], 'correct' => 'Тип определяется поведением'],
                    ['question' => 'Для чего @property?', 'options' => ['Создание свойства', 'Декорирование класса', 'Тестирование', 'Логирование'], 'correct' => 'Создание свойства'],
            ],
            ],
            'Standard Library' => [
                'lessons' => [
                    ['title' => 'os и sys', 'description' => 'os — работа с файловой системой: os.path.exists("file.txt"), os.listdir("."), os.makedirs("dir", exist_ok=True), os.environ["HOME"]. pathlib — ООП-подход к путям: Path("dir/file.txt").read_text(), Path(".").glob("*.py"). sys — системные функции: sys.argv — аргументы CLI, sys.exit(0) — завершение, sys.platform — ОС. subprocess — запуск внешних команд: subprocess.run(["ls", "-la"], capture_output=True).', 'materials' => 'os — документация, pathlib — гайд, sys — модуль, subprocess'],
                    ['title' => 'json и re', 'description' => 'json — парсинг/сериализация JSON: json.loads(\'{"key": "value"}\'), json.dumps(data, ensure_ascii=False, indent=2). re — регулярные выражения: re.search(r"\\d+", "abc123") — поиск, re.sub(r"\\s+", " ", text) — замена, re.findall(r"\\w+", text) — все совпадения. Шаблоны: r"\\d" (цифра), r"\\w" (буква/цифра), r"\\s" (пробел), + (1+), * (0+), ? (0 или 1), {n} (n раз).', 'materials' => 'json — документация, re — регулярные выражения, Regex cheatsheet'],
                ],
                'quizzes' => [
                    ['question' => 'Что делает os.path.exists?', 'options' => ['Проверяет существование файла', 'Создаёт файл', 'Удаляет файл', 'Читает файл'], 'correct' => 'Проверяет существование файла'],
                    ['question' => 'Для чего модуль re?', 'options' => ['Регулярные выражения', 'Математика', 'Логирование', 'JSON'], 'correct' => 'Регулярные выражения'],
                    ['question' => 'Что такое pathlib?', 'options' => ['Модуль для путей файлов', 'Библиотека для рисования', 'CLI', 'Тестирование'], 'correct' => 'Модуль для путей файлов'],
            ],
            ],
            'Django' => [
                'lessons' => [
                    ['title' => 'Основы Django', 'description' => 'Django — "включённая батарея" фреймворк. MTV (Model-Template-View): Models — данные (ORM), Views — логика (функции/классы), Templates — HTML-шаблоны (Jinja2-подобные). python manage.py startproject myapp — создание проекта. python manage.py startapp blog — создание приложения. urls.py — маршрутизация: path("posts/", views.post_list). settings.py — конфигурация: INSTALLED_APPS, DATABASES, MIDDLEWARE. Django Admin — автоматическая админка.', 'materials' => 'Django — документация, MTV pattern, Django Admin'],
                    ['title' => 'ORM Django', 'description' => 'QuerySet — ленивые запросы: Post.objects.filter(author=user).order_by("-created_at")[:10]. Миграции: python manage.py makemigrations → python manage.py migrate. Связи: OneToOneField, ForeignKey (Many-to-One), ManyToManyField. Агрегации: annotate(count=Count("comments")), aggregate(total=Sum("amount")). select_related — JOIN (ForeignKey), prefetch_related — отдельные запросы (ManyToMany). F() — обращение к полю в запросе.', 'materials' => 'Django ORM — документация, QuerySet API, Миграции, F expressions'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое MTV?', 'options' => ['Model-Template-View', 'Music TV', 'My Total Value', 'Multi-Thread Virtual'], 'correct' => 'Model-Template-View'],
                    ['question' => 'Как создать миграцию?', 'options' => ['python manage.py migrate', 'python manage.py makemigrations', 'python manage.py db:migrate', 'django migrate'], 'correct' => 'python manage.py makemigrations'],
                    ['question' => 'Для чего select_related?', 'options' => ['Оптимизация связанных запросов', 'Создание связей', 'Удаление записей', 'Тестирование'], 'correct' => 'Оптимизация связанных запросов'],
            ],
            ],
            'Flask' => [
                'lessons' => [
                    ['title' => 'Основы Flask', 'description' => 'Flask — микрофреймворк (минимум по умолчанию, расширения по необходимости). from flask import Flask; app = Flask(__name__). @app.route("/") def index(): return "Hello". Шаблоны Jinja2: render_template("index.html", title="Home"). request — данные запроса: request.form["name"], request.args.get("page", 1). Response — кастомные ответы: return jsonify({"data": ...}), 201. Blueprints — модульная структура: bp = Blueprint("blog", __name__).', 'materials' => 'Flask — документация, Jinja2, Blueprints, Request/Response'],
                    ['title' => 'Расширения Flask', 'description' => 'Flask-SQLAlchemy — ORM: db.session.add(user), db.session.commit(). Flask-Migrate — миграции: flask db init, flask db migrate. Flask-Login — аутентификация: login_required, current_user. Flask-Mail — отправка email. Flask-RESTful — REST API: Resource, reqparse. Flask-CORS — кросс-доменные запросы. Flask-Session — серверные сессии. Расширения добавляются через init_app().', 'materials' => 'Flask-SQLAlchemy, Flask-Login, Flask-Migrate, Extensions — каталог'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Flask?', 'options' => ['Микрофреймворк для веба', 'ORM', 'CLI', 'Тестирование'], 'correct' => 'Микрофреймворк для веба'],
                    ['question' => 'Что такое blueprint?', 'options' => ['Чертёж', 'Модульная структура приложения', 'Шаблон', 'Команда'], 'correct' => 'Модульная структура приложения'],
                    ['question' => 'Что такое Jinja2?', 'options' => ['Шаблонизатор', 'ORM', 'CLI', 'База данных'], 'correct' => 'Шаблонизатор'],
            ],
            ],
            'SQLAlchemy' => [
                'lessons' => [
                    ['title' => 'Core и ORM', 'description' => 'SQLAlchemy — ORM и toolkit для Python. Core: engine = create_engine("sqlite:///db.sqlite3"), table = Table("users", metadata, Column("id", Integer)). ORM: class User(Base): __tablename__ = "users"; id = Column(Integer, primary_key=True). Session — работа с БД: session.query(User).filter_by(name="Иван").first(). Связи: posts = relationship("Post", backref="author"). Декларативный стиль: Base = declarative_base().', 'materials' => 'SQLAlchemy ORM — документация, Core — гайд, relationships'],
                    ['title' => 'Продвинутый SQLAlchemy', 'description' => 'Eager loading — загрузка связанных данных сразу: session.query(User).options(joinedload(User.posts)). Subqueries: session.query(func.count(Post.id)).subquery(). Транзакции: session.begin() — контекст. Connection pooling — переиспользование соединений (pool_size, max_overflow). Scoped sessions — потокобезопасные сессии. bulk_insert_mappings — массовая вставка. async SQLAlchemy — асинхронная работа (create_async_engine).', 'materials' => 'Eager loading, Subqueries, Connection pooling, Async SQLAlchemy'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое SQLAlchemy?', 'options' => ['Язык стилей', 'ORM и toolkit для Python', 'CLI', 'Тестирование'], 'correct' => 'ORM и toolkit для Python'],
                    ['question' => 'Для чего Session?', 'options' => ['Сессия пользователя', 'Сессия для работы с БД', 'HTTP сессия', 'Кэш'], 'correct' => 'Сессия для работы с БД'],
                    ['question' => 'Что такое eager loading?', 'options' => ['Отложенная загрузка', 'Загрузка связанных данных сразу', 'Удаление данных', 'Создание данных'], 'correct' => 'Загрузка связанных данных сразу'],
            ],
            ],

            // ═══════════════════════════════════════════════════════
            // UI/UX DESIGNER — exact titles from AllRoadmapsSeeder
            // ═══════════════════════════════════════════════════════
            'Design Fundamentals' => [
                'lessons' => [
                    ['title' => 'Основы дизайна', 'description' => 'Баланс, контраст, повторение, выравнивание, близость.', 'materials' => 'Gestalt принципы, визуальная иерархия'],
                    ['title' => 'Цвет и типографика', 'description' => 'Цветовые модели, гармония, иерархия шрифтов.', 'materials' => 'HSL, RGB, цветовые палитры'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое визуальная иерархия?', 'options' => ['Иерархия файлов', 'Упорядочивание элементов по важности', 'Навигация', 'Стиль'], 'correct' => 'Упорядочивание элементов по важности'],
                    ['question' => 'Что такое контраст?', 'options' => ['Разница между элементами', 'Одинаковые элементы', 'Цвет фона', 'Шрифт'], 'correct' => 'Разница между элементами'],
                    ['question' => 'Какая цветовая модель для веба?', 'options' => ['CMYK', 'RGB', 'Pantone', 'HLS'], 'correct' => 'RGB'],
            ],
            ],
            'Figma' => [
                'lessons' => [
                    ['title' => 'Интерфейс Figma', 'description' => 'Frame, Auto Layout, Constraints, компоненты.', 'materials' => 'Figma basics, keyboard shortcuts'],
                    ['title' => 'Компоненты Figma', 'description' => 'Main component, instance, variant, design system.', 'materials' => 'Component properties, auto layout'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Frame в Figma?', 'options' => ['Рамка', 'Контейнер для элементов', 'Текст', 'Изображение'], 'correct' => 'Контейнер для элементов'],
                    ['question' => 'Что такое Auto Layout?', 'options' => ['Автоматическое размещение', 'Ручное размещение', 'Шрифт', 'Цвет'], 'correct' => 'Автоматическое размещение'],
                    ['question' => 'Для чего variant?', 'options' => ['Варианты компонента', 'Дублирование', 'Удаление', 'Экспорт'], 'correct' => 'Варианты компонента'],
            ],
            ],
            'Color Theory' => [
                'lessons' => [
                    ['title' => 'Основы цвета', 'description' => 'Цветовое колесо, гармония, комплементарные, аналоговые цвета.', 'materials' => 'Coolors, Adobe Color'],
                    ['title' => 'Цвет в UX', 'description' => 'Психология цвета, accessibility контраст, dark/light themes.', 'materials' => 'WCAG contrast ratio'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое комплементарные цвета?', 'options' => ['Одинаковые', 'Противоположные на цветовом колесе', 'Похожие', 'Чёрные'], 'correct' => 'Противоположные на цветовом колесе'],
                    ['question' => 'Какой коэффициент контраста для AA?', 'options' => ['2:1', '4.5:1', '7:1', '1:1'], 'correct' => '4.5:1'],
                    ['question' => 'Для чего психология цвета?', 'options' => ['Для красоты', 'Влияние на восприятие и поведение', 'Для тестов', 'Для логов'], 'correct' => 'Влияние на восприятие и поведение'],
            ],
            ],
            'Typography' => [
                'lessons' => [
                    ['title' => 'Основы типографики', 'description' => 'Шрифты, размеры, межстрочный интервал, вес.', 'materials' => 'Google Fonts, font pairing'],
                    ['title' => 'Типографика в вебе', 'description' => 'Веб-шрифты, load timeout, fallback, Variable fonts.', 'materials' => '@font-face, font-display'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое line-height?', 'options' => ['Высота шрифта', 'Межстрочный интервал', 'Ширина текста', 'Цвет текста'], 'correct' => 'Межстрочный интервал'],
                    ['question' => 'Для чего font-display?', 'options' => ['Отображение шрифта при загрузке', 'Удаление шрифта', 'Стилизация', 'Экспорт'], 'correct' => 'Отображение шрифта при загрузке'],
                    ['question' => 'Что такое kerning?', 'options' => ['Расстояние между буквами', 'Размер шрифта', 'Жирность', 'Курсив'], 'correct' => 'Расстояние между буквами'],
            ],
            ],
            'Components & Design Systems' => [
                'lessons' => [
                    ['title' => 'Material Design', 'description' => 'Компоненты, сетка, motion, elevation.', 'materials' => 'Material Design 3, dynamic color'],
                    ['title' => 'Ant Design', 'description' => 'React компоненты, темизация, enterprise UI.', 'materials' => 'antd, Design Pro'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое Material Design?', 'options' => ['Язык стилей', 'Система дизайна от Google', 'Фреймворк', 'Шрифт'], 'correct' => 'Система дизайна от Google'],
                    ['question' => 'Для чего design system?', 'options' => ['Единообразие UI', 'Тестирование', 'Деплой', 'Логирование'], 'correct' => 'Единообразие UI'],
                    ['question' => 'Что такое компонент?', 'options' => ['Переиспользуемый элемент UI', 'Файл', 'Модуль', 'Скрипт'], 'correct' => 'Переиспользуемый элемент UI'],
            ],
            ],
            'User Research' => [
                'lessons' => [
                    ['title' => 'Методы исследований', 'description' => 'Interviews, surveys, usability testing, card sorting.', 'materials' => 'UserTesting, Hotjar'],
                    ['title' => 'Анализ данных', 'description' => 'Personas, user journeys, empathy maps.', 'materials' => 'Jobs to be Done, story mapping'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое persona?', 'options' => ['Личность', 'Описательный образ типичного пользователя', 'Пароль', 'Аватар'], 'correct' => 'Описательный образ типичного пользователя'],
                    ['question' => 'Для чего card sorting?', 'options' => ['Организация контента', 'Сортировка карт', 'Тестирование', 'Дизайн'], 'correct' => 'Организация контента'],
                    ['question' => 'Что такое user journey?', 'options' => ['Путь пользователя', 'Навигация', 'URL', 'Карта сайта'], 'correct' => 'Путь пользователя'],
            ],
            ],
            'Wireframing' => [
                'lessons' => [
                    ['title' => 'Основы wireframes', 'description' => 'Низкая/высокая детализация, скелетоны, layout.', 'materials' => 'Balsamiq, Figma wireframes'],
                    ['title' => 'Wireframe vs Mockup', 'description' => 'Разница в fidelity, когда что использовать.', 'materials' => 'Low-fi vs hi-fi, прототипирование'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое wireframe?', 'options' => ['Проволочный каркас', 'Высокодетализированный дизайн', 'Код', 'Прототип'], 'correct' => 'Проволочный каркас'],
                    ['question' => 'Low-fi wireframe означает?', 'options' => ['Низкая детализация', 'Высокая детализация', 'Готовый дизайн', 'Код'], 'correct' => 'Низкая детализация'],
                    ['question' => 'Зачем wireframes?', 'options' => ['Для красоты', 'Быстрая визуализация структуры', 'Для тестов', 'Для деплоя'], 'correct' => 'Быстрая визуализация структуры'],
            ],
            ],
            'Prototyping' => [
                'lessons' => [
                    ['title' => 'Прототипирование в Figma', 'description' => 'Smart animate, interactions, overlay, scrolling.', 'materials' => 'Figma prototyping, interactions'],
                    ['title' => 'Тестирование прототипов', 'description' => 'Usability тестирование, сбор фидбека, итерации.', 'materials' => 'Maze, Useberry'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое прототип?', 'options' => ['Код', 'Интерактивная модель продукта', 'Дизайн', 'Документ'], 'correct' => 'Интерактивная модель продукта'],
                    ['question' => 'Для чего smart animate?', 'options' => ['Анимация между состояниями', 'Удаление', 'Копирование', 'Экспорт'], 'correct' => 'Анимация между состояниями'],
                    ['question' => 'Зачем тестировать прототипы?', 'options' => ['Для красоты', 'Проверка usability до разработки', 'Для деплоя', 'Для логов'], 'correct' => 'Проверка usability до разработки'],
            ],
            ],
            'User Testing' => [
                'lessons' => [
                    ['title' => 'Usability тестирование', 'description' => 'Модерация, сценарии, метрики (SUS, task success rate).', 'materials' => 'UserTesting.com, Lookback'],
                    ['title' => 'A/B тестирование', 'description' => 'Гипотезы, контрольная группа, статистическая значимость.', 'materials' => 'Optimizely, Google Optimize'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое usability?', 'options' => ['Красота', 'Удобство использования', 'Скорость', 'Безопасность'], 'correct' => 'Удобство использования'],
                    ['question' => 'Для чего A/B тестирование?', 'options' => ['Сравнение двух вариантов', 'Тестирование API', 'Логирование', 'Кэширование'], 'correct' => 'Сравнение двух вариантов'],
                    ['question' => 'Что такое SUS?', 'options' => ['System Usability Scale', 'Secure User System', 'Simple UI Standard', 'Smart User Service'], 'correct' => 'System Usability Scale'],
            ],
            ],
            'Accessibility' => [
                'lessons' => [
                    ['title' => 'WCAG стандарты', 'description' => 'Perceivable, Operable, Understandable, Robust.', 'materials' => 'WCAG 2.1, level A/AA/AAA'],
                    ['title' => 'Практика a11y', 'description' => 'Semantic HTML, ARIA, keyboard navigation, screen readers.', 'materials' => 'axe, Lighthouse, NVDA'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое WCAG?', 'options' => ['Web Content Accessibility Guidelines', 'Web Coding And Graphics', 'Wide Content Access Guide', 'Web Cache And Graphics'], 'correct' => 'Web Content Accessibility Guidelines'],
                    ['question' => 'Для чего ARIA?', 'options' => ['Улучшение доступности', 'Стилизация', 'Логирование', 'Тестирование'], 'correct' => 'Улучшение доступности'],
                    ['question' => 'Что такое screen reader?', 'options' => ['Экранное чтение', 'Читатель экрана для незрячих', 'Браузер', 'Редактор'], 'correct' => 'Читатель экрана для незрячих'],
            ],
            ],
            'Motion Design' => [
                'lessons' => [
                    ['title' => 'Принципы motion', 'description' => '12 принципов анимации, timing, easing, choreography.', 'materials' => 'Lottie, After Effects'],
                    ['title' => 'Анимация в UI', 'description' => 'Micro-interactions, page transitions, loading states.', 'materials' => 'Framer Motion, CSS animations'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое micro-interaction?', 'options' => ['Маленькая анимация для feedback', 'Большая анимация', 'Страница', 'Компонент'], 'correct' => 'Маленькая анимация для feedback'],
                    ['question' => 'Для чего Lottie?', 'options' => ['Анимации из After Effects в вебе', 'Логирование', 'Тестирование', 'CLI'], 'correct' => 'Анимации из After Effects в вебе'],
                    ['question' => 'Что такое easing?', 'options' => ['Замедление/ускорение анимации', 'Цвет', 'Шрифт', 'Размер'], 'correct' => 'Замедление/ускорение анимации'],
            ],
            ],

            // ═══════════════════════════════════════════════════════
            // MOBILE DEVELOPER — exact titles from AllRoadmapsSeeder
            // ═══════════════════════════════════════════════════════
            'JavaScript' => [
                'lessons' => [
                    ['title' => 'Основы JavaScript', 'description' => 'Переменные, типы данных, операторы, функции, условия, циклы.', 'materials' => 'MDN JavaScript reference'],
                    ['title' => 'DOM и события', 'description' => 'querySelector, addEventListener, event delegation.', 'materials' => 'DOM API, bubbling/capturing'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое DOM?', 'options' => ['Объектная модель документа', 'Язык стилей', 'Протокол', 'База данных'], 'correct' => 'Объектная модель документа'],
                    ['question' => 'Что делает addEventListener?', 'options' => ['Удаляет элемент', 'Добавляет обработчик события', 'Создаёт элемент', 'Кэширует данные'], 'correct' => 'Добавляет обработчик события'],
                    ['question' => 'Что такое event delegation?', 'options' => ['Делегирование событий', 'Создание событий', 'Удаление событий', 'Кэширование событий'], 'correct' => 'Делегирование событий'],
            ],
            ],
            'React Native' => [
                'lessons' => [
                    ['title' => 'Основы React Native', 'description' => 'View, Text, Image, StyleSheet, cross-platform.', 'materials' => 'Expo, react-native-cli'],
                    ['title' => 'Нативные компоненты', 'description' => 'ScrollView, FlatList, TouchableOpacity, Modal.', 'materials' => 'Platform API, responsive design'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое React Native?', 'options' => ['Нативное приложение', 'Кросс-платформенные мобильные приложения на React', 'Веб-приложение', 'CLI'], 'correct' => 'Кросс-платформенные мобильные приложения на React'],
                    ['question' => 'Что такое Expo?', 'options' => ['Экспорт', 'Инструментарий для быстрой разработки RN', 'Браузер', 'Текстовый редактор'], 'correct' => 'Инструментарий для быстрой разработки RN'],
                    ['question' => 'Для чего FlatList?', 'options' => ['Отображение списка с виртуализацией', 'Картинка', 'Форма', 'Навигация'], 'correct' => 'Отображение списка с виртуализацией'],
            ],
            ],

            // ═══════════════════════════════════════════════════════
            // C++ DEVELOPER — exact titles from AllRoadmapsSeeder
            // ═══════════════════════════════════════════════════════
            'C++ Basics' => [
                'lessons' => [
                    ['title' => 'Синтаксис C++', 'description' => 'Переменные, типы данных, операторы, функции, ввод/вывод.', 'materials' => 'iostream, cin/cout'],
                    ['title' => 'Управляющие конструкции', 'description' => 'if/else, switch, for, while, break, continue.', 'materials' => 'Циклы, условия'],
                ],
                'quizzes' => [
                    ['question' => 'Как вывести данные в C++?', 'options' => ['print()', 'cout <<', 'echo()', 'System.out.println'], 'correct' => 'cout <<'],
                    ['question' => 'Как объявить переменную?', 'options' => ['var x', 'int x', 'let x', 'dim x'], 'correct' => 'int x'],
                    ['question' => 'Что такое #include?', 'options' => ['Подключение заголовочного файла', 'Объявление переменной', 'Функция', 'Комментарий'], 'correct' => 'Подключение заголовочного файла'],
            ],
            ],
            'C++ OOP' => [
                'lessons' => [
                    ['title' => 'Классы в C++', 'description' => 'class, public/private/protected, конструкторы, деструкторы.', 'materials' => 'Разделение интерфейса и реализации'],
                    ['title' => 'Наследование и полиморфизм', 'description' => 'virtual, override, pure virtual, абстрактные классы.', 'materials' => 'Vtable, RTTI'],
                ],
                'quizzes' => [
                    ['question' => 'Что делает virtual в C++?', 'options' => ['Виртуальная память', 'Полиморфизм через vtable', 'Виртуальные файлы', 'Виртуальный сервер'], 'correct' => 'Полиморфизм через vtable'],
                    ['question' => 'Что такое pure virtual function?', 'options' => ['Функция без реализации', 'Виртуальная функция', 'Статическая функция', 'Шаблонная функция'], 'correct' => 'Функция без реализации'],
                    ['question' => 'Когда вызывается деструктор?', 'options' => ['При создании объекта', 'При уничтожении объекта', 'При вызове метода', 'Никогда'], 'correct' => 'При уничтожении объекта'],
            ],
            ],


            // ===== ЭКЗАМЕНЫ =====
            'Экзамен: Backend' => [
                'lessons' => [
                    ['title' => 'Финальный экзамен Backend', 'description' => 'Комплексный экзамен по серверной разработке: PHP, MySQL, Laravel, HTTP/REST, OOP. Время: 60 минут, проходной балл: 70%.', 'materials' => 'Все темы Backend Developer'],
                ],
                'quizzes' => [
                    ['question' => 'Какой HTTP-метод для создания ресурса?', 'options' => ['POST', 'GET', 'DELETE', 'PUT'], 'correct' => 'POST'],
                    ['question' => 'Что такое N+1 проблема?', 'options' => ['Много запросов в цикле', 'Нет соединения', 'Дубли данных', 'Ошибка типа'], 'correct' => 'Много запросов в цикле'],
                    ['question' => 'Какой статус-код означает Unauthorized?', 'options' => ['401', '403', '404', '500'], 'correct' => '401'],
                    ['question' => 'Для чего используется Trait?', 'options' => ['Повторное использование кода', 'Наследование', 'Абстракция', 'Интерфейс'], 'correct' => 'Повторное использование кода'],
                    ['question' => 'Что такое eager loading?', 'options' => ['Загрузка связей одним запросом', 'Ленивая загрузка', 'Кэширование', 'Пагинация'], 'correct' => 'Загрузка связей одним запросом'],
                    ['question' => 'Какой JOIN все строки из обеих таблиц?', 'options' => ['FULL JOIN', 'INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN'], 'correct' => 'FULL JOIN'],
                    ['question' => 'Для чего нужен Composer?', 'options' => ['Управление зависимостями PHP', 'Тестирование', 'Деплой', 'Логирование'], 'correct' => 'Управление зависимостями PHP'],
                    ['question' => 'Что такое PSR-4?', 'options' => ['Автозагрузка по namespace', 'Тестирование', 'Кэширование', 'Логирование'], 'correct' => 'Автозагрузка по namespace'],
                    ['question' => 'Для чего нужен middleware?', 'options' => ['Промежуточные обработчики', 'Модели', 'Миграции', 'Шаблоны'], 'correct' => 'Промежуточные обработчики'],
                    ['question' => 'Как создать модель в Laravel?', 'options' => ['php artisan make:model', 'php artisan create:model', 'php artisan new:model', 'php artisan generate:model'], 'correct' => 'php artisan make:model'],
                    ['question' => 'Что такое JWT?', 'options' => ['JSON Web Token', 'Протокол шифрования', 'База данных', 'Фреймворк'], 'correct' => 'JSON Web Token'],
                    ['question' => 'Какой индекс для поиска по тексту?', 'options' => ['FULLTEXT', 'B-tree', 'Hash', 'Composite'], 'correct' => 'FULLTEXT'],
                    ['question' => 'Что такое scopes в Eloquent?', 'options' => ['Именованные запросы', 'Области видимости', 'Пакеты', 'Миграции'], 'correct' => 'Именованные запросы'],
                    ['question' => 'Для чего artisan migrate:rollback?', 'options' => ['Откат миграции', 'Удаление БД', 'Создание БД', 'Обновление пакетов'], 'correct' => 'Откат миграции'],
                    ['question' => 'Какой статус для созданного ресурса?', 'options' => ['201', '200', '204', '301'], 'correct' => '201'],
                ],
            ],
            'Экзамен: Frontend' => [
                'lessons' => [
                    ['title' => 'Финальный экзамен Frontend', 'description' => 'Комплексный экзамен по фронтенд-разработке: HTML, CSS, JavaScript, Git. Время: 50 минут, проходной балл: 70%.', 'materials' => 'Все темы Frontend Developer'],
                ],
                'quizzes' => [
                    ['question' => 'Какой тег для заголовка первого уровня?', 'options' => ['<h1>', '<header>', '<title>', '<head>'], 'correct' => '<h1>'],
                    ['question' => 'Что делает box-sizing: border-box?', 'options' => ['Включает padding/border в width', 'Исключает', 'Удаляет', 'Добавляет'], 'correct' => 'Включает padding/border в width'],
                    ['question' => 'Что такое замыкание?', 'options' => ['Функция с доступом к внешней области', 'Цикл', 'Массив', 'Объект'], 'correct' => 'Функция с доступом к внешней области'],
                    ['question' => 'Как сменить директорию?', 'options' => ['cd', 'ls', 'pwd', 'cat'], 'correct' => 'cd'],
                    ['question' => 'Для чего атрибут alt?', 'options' => ['Описание изображения', 'Стилизация', 'Скрипты', 'Размер'], 'correct' => 'Описание изображения'],
                    ['question' => 'Какое свойство задаёт направление flex?', 'options' => ['flex-direction', 'flex-wrap', 'justify-content', 'align-items'], 'correct' => 'flex-direction'],
                    ['question' => 'Что такое семантический HTML?', 'options' => ['Теги с осмысленным именем', 'Красивый код', 'Быстрый код', 'Короткий код'], 'correct' => 'Теги с осмысленным именем'],
                    ['question' => 'Как посмотреть историю коммитов?', 'options' => ['git log', 'git history', 'git show', 'git list'], 'correct' => 'git log'],
                    ['question' => 'Что такое CSS каскадность?', 'options' => ['Приоритет правил стилей', 'Порядок файлов', 'Размер шрифта', 'Цвет фона'], 'correct' => 'Приоритет правил стилей'],
                    ['question' => 'Как задать колонки в Grid?', 'options' => ['grid-template-columns', 'grid-columns', 'columns', 'grid-layout'], 'correct' => 'grid-template-columns'],
                    ['question' => 'Что такое DOM?', 'options' => ['Объектная модель документа', 'Язык', 'Фреймворк', 'Сервер'], 'correct' => 'Объектная модель документа'],
                    ['question' => 'Для чего npm?', 'options' => ['Управление пакетами JS', 'Язык', 'Фреймворк', 'База данных'], 'correct' => 'Управление пакетами JS'],
                    ['question' => 'Что такое медиа-запросы?', 'options' => ['CSS-правила для разных экранов', 'JavaScript', 'HTML', 'Сервер'], 'correct' => 'CSS-правила для разных экранов'],
                    ['question' => 'Какой HTTP статус для 404?', 'options' => ['404', '200', '500', '301'], 'correct' => '404'],
                    ['question' => 'Что такое DNS?', 'options' => ['Система доменных имён', 'Протокол файлов', 'База данных', 'ОС'], 'correct' => 'Система доменных имён'],
                ],
            ],
            'Экзамен: Fullstack' => [
                'lessons' => [
                    ['title' => 'Финальный экзамен Fullstack', 'description' => 'Комплексный экзамен по фуллстек-разработке. Время: 60 минут, проходной балл: 70%.', 'materials' => 'Все темы Fullstack Developer'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое REST?', 'options' => ['Архитектурный стиль API', 'Язык', 'Фреймворк', 'База данных'], 'correct' => 'Архитектурный стиль API'],
                    ['question' => 'Как работает CORS?', 'options' => ['Политика кросс-доменных запросов', 'Шифрование', 'Кэширование', 'Балансировка'], 'correct' => 'Политика кросс-доменных запросов'],
                    ['question' => 'Для чего Docker?', 'options' => ['Контейнеризация приложений', 'Тестирование', 'Логирование', 'Кэширование'], 'correct' => 'Контейнеризация приложений'],
                    ['question' => 'Что такое ORM?', 'options' => ['Объектно-реляционное отображение', 'Язык', 'Фреймворк', 'Сервер'], 'correct' => 'Объектно-реляционное отображение'],
                    ['question' => 'Для чего Git?', 'options' => ['Контроль версий', 'Тестирование', 'Деплой', 'Логирование'], 'correct' => 'Контроль версий'],
                    ['question' => 'Что такое SSR?', 'options' => ['Серверный рендеринг', 'Клиентский рендеринг', 'Статика', 'Кэширование'], 'correct' => 'Серверный рендеринг'],
                    ['question' => 'Для чего WebSocket?', 'options' => ['Двусторонняя связь в реальном времени', 'Только GET-запросы', 'Кэширование', 'Файлы'], 'correct' => 'Двусторонняя связь в реальном времени'],
                    ['question' => 'Что такое rate limiting?', 'options' => ['Ограничение количества запросов', 'Кэширование', 'Шифрование', 'Балансировка'], 'correct' => 'Ограничение количества запросов'],
                    ['question' => 'Для чего CI/CD?', 'options' => ['Автоматизация сборки и деплоя', 'Ручное тестирование', 'Логирование', 'Кэширование'], 'correct' => 'Автоматизация сборки и деплоя'],
                    ['question' => 'Что такое CDN?', 'options' => ['Сеть доставки контента', 'База данных', 'Фреймворк', 'Сервер'], 'correct' => 'Сеть доставки контента'],
                    ['question' => 'Какой статус-код для редиректа?', 'options' => ['301', '200', '404', '500'], 'correct' => '301'],
                    ['question' => 'Что такое microservices?', 'options' => ['Мелкие сервисы', 'Монолит', 'База данных', 'Фреймворк'], 'correct' => 'Мелкие сервисы'],
                    ['question' => 'Для чего Kubernetes?', 'options' => ['Оркестрация контейнеров', 'Тестирование', 'Логирование', 'Кэширование'], 'correct' => 'Оркестрация контейнеров'],
                    ['question' => 'Что такое lazy loading?', 'options' => ['Загрузка при скролле', 'Все сразу', 'Кэширование', 'Сжатие'], 'correct' => 'Загрузка при скролле'],
                    ['question' => 'Какой статус для Unauthorized?', 'options' => ['401', '403', '404', '500'], 'correct' => '401'],
                ],
            ],
            'Экзамен: DevOps' => [
                'lessons' => [
                    ['title' => 'Финальный экзамен DevOps', 'description' => 'Комплексный экзамен по DevOps: Linux, Docker, CI/CD, мониторинг. Время: 60 минут, проходной балл: 70%.', 'materials' => 'Все темы DevOps Engineer'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое контейнер?', 'options' => ['Изолированная среда приложения', 'Виртуальная машина', 'Файл', 'Сеть'], 'correct' => 'Изолированная среда приложения'],
                    ['question' => 'Для чего Docker Compose?', 'options' => ['Управление несколькими контейнерами', 'Один контейнер', 'Registry', 'Сеть'], 'correct' => 'Управление несколькими контейнерами'],
                    ['question' => 'Что такое CI/CD pipeline?', 'options' => ['Автоматизация этапов сборки', 'Ручной ввод', 'Логирование', 'Кэширование'], 'correct' => 'Автоматизация этапов сборки'],
                    ['question' => 'Для чего Kubernetes?', 'options' => ['Оркестрация контейнеров', 'Тестирование', 'Логирование', 'Кэширование'], 'correct' => 'Оркестрация контейнеров'],
                    ['question' => 'Что такое IaC?', 'options' => ['Infrastructure as Code', 'Internet as Cable', 'Internal as Cloud', 'Input as Control'], 'correct' => 'Infrastructure as Code'],
                    ['question' => 'Для чего Prometheus?', 'options' => ['Мониторинг и метрики', 'Кэширование', 'Логирование', 'Тестирование'], 'correct' => 'Мониторинг и метрики'],
                    ['question' => 'Что такое load balancer?', 'options' => ['Балансировка нагрузки между серверами', 'Кэширование', 'Шифрование', 'Логирование'], 'correct' => 'Балансировка нагрузки между серверами'],
                    ['question' => 'Для чего reverse proxy?', 'options' => ['Проксирование запросов к бэкенду', 'Кэширование', 'Шифрование', 'Логирование'], 'correct' => 'Проксирование запросов к бэкенду'],
                    ['question' => 'Что такое blue-green deployment?', 'options' => ['Деплой без downtime', 'Ручной деплой', 'Тестирование', 'Откат'], 'correct' => 'Деплой без downtime'],
                    ['question' => 'Какой порт для HTTPS?', 'options' => ['443', '80', '8080', '3000'], 'correct' => '443'],
                    ['question' => 'Что такое Nginx?', 'options' => ['Веб-сервер и reverse proxy', 'База данных', 'Язык', 'Фреймворк'], 'correct' => 'Веб-сервер и reverse proxy'],
                    ['question' => 'Для чего Dockerfile?', 'options' => ['Сборка Docker образа', 'Конфигурация сети', 'Скрипт деплоя', 'Логи'], 'correct' => 'Сборка Docker образа'],
                    ['question' => 'Что такое CANARY deploy?', 'options' => ['Постепенный rollout', 'Мгновенный деплой', 'Откат', 'Тестирование'], 'correct' => 'Постепенный rollout'],
                    ['question' => 'Для чего Grafana?', 'options' => ['Визуализация метрик', 'Кэширование', 'Логирование', 'Тестирование'], 'correct' => 'Визуализация метрик'],
                    ['question' => 'Что такое rolling update?', 'options' => ['Обновление по одному контейнеру', 'Все сразу', 'Откат', 'Тестирование'], 'correct' => 'Обновление по одному контейнеру'],
                ],
            ],
            'Экзамен: Python' => [
                'lessons' => [
                    ['title' => 'Финальный экзамен Python', 'description' => 'Комплексный экзамен по Python. Время: 60 минут, проходной балл: 70%.', 'materials' => 'Все темы Python Developer'],
                ],
                'quizzes' => [
                    ['question' => 'Какой отступ в Python?', 'options' => ['4 пробела или tab', '2 пробела', 'Любой', 'Нет отступов'], 'correct' => '4 пробела или tab'],
                    ['question' => 'Что такое self?', 'options' => ['Ссылка на экземпляр класса', 'Имя класса', 'Параметр функции', 'Модуль'], 'correct' => 'Ссылка на экземпляр класса'],
                    ['question' => 'Для чего __init__?', 'options' => ['Конструктор класса', 'Деструктор', 'Метод класса', 'Функция'], 'correct' => 'Конструктор класса'],
                    ['question' => 'Для чего модуль os?', 'options' => ['Работа с ОС', 'Математика', 'Сеть', 'Тестирование'], 'correct' => 'Работа с ОС'],
                    ['question' => 'Что такое list comprehension?', 'options' => ['Список с условием в одной строке', 'Функция', 'Класс', 'Модуль'], 'correct' => 'Список с условием в одной строке'],
                    ['question' => 'Что такое ORM в Django?', 'options' => ['Объектно-реляционное отображение', 'Язык', 'Шаблонизатор', 'Сервер'], 'correct' => 'Объектно-реляционное отображение'],
                    ['question' => 'Для чего Flask?', 'options' => ['Веб-приложения', 'База данных', 'ОС', 'Язык'], 'correct' => 'Веб-приложения'],
                    ['question' => 'Для чего декоратор @app.route?', 'options' => ['Маршрутизация URL', 'Авторизация', 'Кэширование', 'Логирование'], 'correct' => 'Маршрутизация URL'],
                    ['question' => 'Для чего модуль json?', 'options' => ['Парсинг JSON', 'Работа с БД', 'Сеть', 'Файлы'], 'correct' => 'Парсинг JSON'],
                    ['question' => 'Что такое Django admin?', 'options' => ['Управление данными через веб', 'Логирование', 'Тестирование', 'Деплой'], 'correct' => 'Управление данными через веб'],
                    ['question' => 'Что такое SQLAlchemy session?', 'options' => ['Сессия для работы с БД', 'Веб-сессия', 'Файл', 'Настройки'], 'correct' => 'Сессия для работы с БД'],
                    ['question' => 'Какой синтаксис для функции?', 'options' => ['def name():', 'function name() {}', 'func name()', 'sub name()'], 'correct' => 'def name():'],
                    ['question' => 'Что такое pip?', 'options' => ['Менеджер пакетов Python', 'Язык', 'Фреймворк', 'Сервер'], 'correct' => 'Менеджер пакетов Python'],
                    ['question' => 'Что такое virtualenv?', 'options' => ['Виртуальное окружение', 'Виртуальная машина', 'Контейнер', 'Сервер'], 'correct' => 'Виртуальное окружение'],
                    ['question' => 'Какой оператор возведения в степень?', 'options' => ['**', '^', 'pow', '^^'], 'correct' => '**'],
                ],
            ],
            'Экзамен: UI/UX Design' => [
                'lessons' => [
                    ['title' => 'Финальный экзамен UI/UX', 'description' => 'Комплексный экзамен по UI/UX дизайну. Время: 45 минут, проходной балл: 70%.', 'materials' => 'Все темы UI/UX Designer'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое визуальная иерархия?', 'options' => ['Порядок важности элементов', 'Цветовая палитра', 'Шрифт', 'Размер'], 'correct' => 'Порядок важности элементов'],
                    ['question' => 'Что такое auto layout в Figma?', 'options' => ['Автоматическое расположение', 'Ручное расположение', 'Анимация', 'Экспорт'], 'correct' => 'Автоматическое расположение'],
                    ['question' => 'Что такое kerning?', 'options' => ['Расстояние между символами', 'Размер шрифта', 'Жирность', 'Наклон'], 'correct' => 'Расстояние между символами'],
                    ['question' => 'Что такое user persona?', 'options' => ['Типичный образ пользователя', 'Аватар', 'Логин', 'Пароль'], 'correct' => 'Типичный образ пользователя'],
                    ['question' => 'Что такое wireframe?', 'options' => ['Схематичный макет страницы', 'Готовый дизайн', 'Код', 'Прототип'], 'correct' => 'Схематичный макет страницы'],
                    ['question' => 'Что такое design token?', 'options' => ['Переменная для дизайна', 'Иконка', 'Шрифт', 'Цвет'], 'correct' => 'Переменная для дизайна'],
                    ['question' => 'Что такое комплементарные цвета?', 'options' => ['Противоположные в цветовом круге', 'Похожие', 'Тёплые', 'Холодные'], 'correct' => 'Противоположные в цветовом круге'],
                    ['question' => 'Что такое ARIA?', 'options' => ['Атрибуты доступности', 'Язык', 'Фреймворк', 'Библиотека'], 'correct' => 'Атрибуты доступности'],
                    ['question' => 'Что такое negative space?', 'options' => ['Пустое пространство между элементами', 'Цвет фона', 'Шрифт', 'Изображение'], 'correct' => 'Пустое пространство между элементами'],
                    ['question' => 'Что такое A/B тестирование?', 'options' => ['Сравнение двух вариантов', 'Один вариант', 'Документация', 'Код'], 'correct' => 'Сравнение двух вариантов'],
                    ['question' => 'Что такое easing?', 'options' => ['Ускорение/замедление анимации', 'Цвет', 'Размер', 'Позиция'], 'correct' => 'Ускорение/замедление анимации'],
                    ['question' => 'Для чего line-height?', 'options' => ['Междустрочный интервал', 'Размер шрифта', 'Цвет', 'Ширина'], 'correct' => 'Междустрочный интервал'],
                    ['question' => 'Что такое high-fidelity mockup?', 'options' => ['Детализированный макет', 'Схематичный макет', 'Код', 'Документ'], 'correct' => 'Детализированный макет'],
                    ['question' => 'Для чего user journey map?', 'options' => ['Визуализация пути пользователя', 'Карта мира', 'Навигация', 'Маршрут'], 'correct' => 'Визуализация пути пользователя'],
                    ['question' => 'Что такое micro-interactions?', 'options' => ['Маленькие анимации при действиях', 'Большие анимации', 'Звуки', 'Эффекты'], 'correct' => 'Маленькие анимации при действиях'],
                ],
            ],
            'Экзамен: Mobile' => [
                'lessons' => [
                    ['title' => 'Финальный экзамен Mobile', 'description' => 'Комплексный экзамен по мобильной разработке. Время: 50 минут, проходной балл: 70%.', 'materials' => 'Все темы Mobile Developer'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое React Native?', 'options' => ['Кроссплатформенная мобильная разработка', 'Веб-фреймворк', 'Язык', 'База данных'], 'correct' => 'Кроссплатформенная мобильная разработка'],
                    ['question' => 'Какой язык во Flutter?', 'options' => ['Dart', 'JavaScript', 'Kotlin', 'Swift'], 'correct' => 'Dart'],
                    ['question' => 'Какой компонент для скроллинга?', 'options' => ['ScrollView', 'View', 'Text', 'Image'], 'correct' => 'ScrollView'],
                    ['question' => 'Как создать стили в React Native?', 'options' => ['StyleSheet.create()', 'CSS файл', 'style', 'Styled Components'], 'correct' => 'StyleSheet.create()'],
                    ['question' => 'Что такое hot reload?', 'options' => ['Обновление кода без перезапуска', 'Перезапуск', 'Компиляция', 'Загрузка данных'], 'correct' => 'Обновление кода без перезапуска'],
                    ['question' => 'Какой формат для Android?', 'options' => ['AAB / APK', 'IPA', 'Dart Package', 'React Bundle'], 'correct' => 'AAB / APK'],
                    ['question' => 'Какой формат для iOS?', 'options' => ['IPA', 'AAB / APK', 'Dart Package', 'React Bundle'], 'correct' => 'IPA'],
                    ['question' => 'Для чего Expo?', 'options' => ['Платформа для React Native', 'Для Flutter', 'Для Android', 'Тестирование'], 'correct' => 'Платформа для React Native'],
                    ['question' => 'Для чего push-уведомления?', 'options' => ['Для доставки сообщений', 'Для хранения', 'Для авторизации', 'Для оплаты'], 'correct' => 'Для доставки сообщений'],
                    ['question' => 'Что такое deep linking?', 'options' => ['Ссылка на контент приложения', 'Ссылка на сайт', 'API вызов', 'Тип навигации'], 'correct' => 'Ссылка на контент приложения'],
                    ['question' => 'Какой компонент для списка?', 'options' => ['FlatList', 'ScrollView', 'List', 'Array'], 'correct' => 'FlatList'],
                    ['question' => 'Как запустить Flutter?', 'options' => ['flutter run', 'dart run', 'flutter start', 'dart start'], 'correct' => 'flutter run'],
                    ['question' => 'Для чего CocoaPods?', 'options' => ['Менеджер зависимостей iOS', 'Для Android', 'Для Flutter', 'Тестирование'], 'correct' => 'Менеджер зависимостей iOS'],
                    ['question' => 'Для чего Gradle?', 'options' => ['Система сборки Android', 'Для iOS', 'Для Flutter', 'Тестирование'], 'correct' => 'Система сборки Android'],
                    ['question' => 'Что такое Firebase?', 'options' => ['Платформа для мобильных приложений', 'Только аналитика', 'Только push', 'Только тестирование'], 'correct' => 'Платформа для мобильных приложений'],
                ],
            ],
            'Экзамен: C++' => [
                'lessons' => [
                    ['title' => 'Финальный экзамен C++', 'description' => 'Комплексный экзамен по C++: основы, OOP, память, STL. Время: 50 минут, проходной балл: 70%.', 'materials' => 'Все темы C++ Developer'],
                ],
                'quizzes' => [
                    ['question' => 'Что такое компиляция?', 'options' => ['Преобразование кода в исполняемый файл', 'Запуск', 'Редактирование', 'Кэширование'], 'correct' => 'Преобразование кода в исполняемый файл'],
                    ['question' => 'Для чего #include?', 'options' => ['Подключение заголовочных файлов', 'Импорт модулей', 'Экспорт', 'Удаление'], 'correct' => 'Подключение заголовочных файлов'],
                    ['question' => 'Что такое виртуальная функция?', 'options' => ['Для переопределения в наследниках', 'Быстрая', 'Статическая', 'Шаблон'], 'correct' => 'Для переопределения в наследниках'],
                    ['question' => 'Что такое абстрактный класс?', 'options' => ['С чисто виртуальными функциями', 'Обычный класс', 'Структура', 'Namespace'], 'correct' => 'С чисто виртуальными функциями'],
                    ['question' => 'Для чего namespace?', 'options' => ['Изоляция имён', 'Память', 'Производительность', 'Безопасность'], 'correct' => 'Изоляция имён'],
                    ['question' => 'Что такое pointer?', 'options' => ['Указатель на адрес памяти', 'Переменная', 'Функция', 'Класс'], 'correct' => 'Указатель на адрес памяти'],
                    ['question' => 'Что такое reference?', 'options' => ['Ссылка на переменную', 'Указатель', 'Копия', 'Константа'], 'correct' => 'Ссылка на переменную'],
                    ['question' => 'Для чего static?', 'options' => ['Постоянное значение', 'Быстрое выполнение', 'Приватность', 'Наследование'], 'correct' => 'Постоянное значение'],
                    ['question' => 'Что такое template?', 'options' => ['Шаблон для generic кода', 'HTML шаблон', 'Функция', 'Класс'], 'correct' => 'Шаблон для generic кода'],
                    ['question' => 'Для чего STL?', 'options' => ['Готовые структуры данных', 'Тестирование', 'Логирование', 'Кэширование'], 'correct' => 'Готовые структуры данных'],
                    ['question' => 'Что такое smart pointer?', 'options' => ['Автоматическое управление памятью', 'Быстрый указатель', 'Указатель для чтения', 'Указатель на структуру'], 'correct' => 'Автоматическое управление памятью'],
                    ['question' => 'Что такое RAII?', 'options' => ['Ресурс = инициализация', 'Быстрый алгоритм', 'Тип данных', 'Паттерн'], 'correct' => 'Ресурс = инициализация'],
                    ['question' => 'Для чего const?', 'options' => ['Неизменяемые значения', 'Быстрое выполнение', 'Приватность', 'Наследование'], 'correct' => 'Неизменяемые значения'],
                    ['question' => 'Что такое overload?', 'options' => ['Функции с разными параметрами', 'Перегрузка памяти', 'Быстрое выполнение', 'Ошибка'], 'correct' => 'Функции с разными параметрами'],
                    ['question' => 'Для чего virtual destructor?', 'options' => ['Корректное удаление через указатель базового типа', 'Быстрое удаление', 'Память', 'Производительность'], 'correct' => 'Корректное удаление через указатель базового типа'],
                ],
            ],


        ];

        $normalized = trim($title);
        return $map[$normalized] ?? null;
    }
}
