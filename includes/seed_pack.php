<?php

function tfSeedLearningPack(PDO $pdo)
{
    ensureCoursesSchema($pdo);
    ensureLessonsSchema($pdo);
    ensurePracticeSchema($pdo);

    $created = [];

    $titleAdjectives = [
        'Базовый',
        'Практический',
        'Спринт',
        'Фокус',
        'Легкий',
        'Сильный',
        'Быстрый',
        'Четкий',
        'Стартовый',
        'Рабочий',
        'Упорный',
        'Гибкий',
        'Чистый',
        'Сбалансированный',
        'Плотный',
        'Собранный',
        'Динамичный',
        'Продвинутый',
        'Уверенный',
        'Системный'
    ];
    $titleThemes = [
        'разогрев',
        'ритм',
        'раунд',
        'набор',
        'модуль',
        'тренинг',
        'пакет',
        'заход',
        'интенсив',
        'практикум'
    ];
    $makeUniqueSuffix = function (int $index) use ($titleAdjectives, $titleThemes): string {
        $adjCount = count($titleAdjectives);
        $themeCount = count($titleThemes);
        $adj = $titleAdjectives[$index % $adjCount];
        $theme = $titleThemes[intdiv($index, $adjCount) % $themeCount];
        return $adj . ' ' . $theme;
    };
    $makeUniqueContext = function (int $index) use ($makeUniqueSuffix): string {
        return 'Контекст: ' . $makeUniqueSuffix($index) . '.';
    };

    $practiceCatalog = [
        [
            'key' => 'api_window_peak',
            'title' => 'Пиковая нагрузка API',
            'prompt' => "Ввод: n, затем n меток времени в секундах (по возрастанию).\nВывод: максимум запросов в любом окне длиной 60 секунд.",
            'tests' => [
                ['stdin' => "8\n1 10 20 30 55 61 62 120\n", 'expected' => "5"],
                ['stdin' => "5\n1 2 3 70 71\n", 'expected' => "3"],
            ]
        ],
        [
            'key' => 'metric_spike',
            'title' => 'Индекс аномального скачка',
            'prompt' => "Ввод: n, порог k, затем n значений метрики.\nВывод: индекс (1-based) первого элемента, где |a[i]-a[i-1]| > k, иначе -1.",
            'tests' => [
                ['stdin' => "6 10\n100 102 105 130 131 132\n", 'expected' => "4"],
                ['stdin' => "5 20\n10 12 15 19 18\n", 'expected' => "-1"],
            ]
        ],
        [
            'key' => 'merge_intervals_count',
            'title' => 'Слияние интервалов бронирований',
            'prompt' => "Ввод: n, затем n интервалов [l r].\nВывод: количество интервалов после слияния пересекающихся.",
            'tests' => [
                ['stdin' => "4\n1 3\n2 6\n8 10\n15 18\n", 'expected' => "3"],
                ['stdin' => "3\n1 4\n4 5\n7 9\n", 'expected' => "2"],
            ]
        ],
        [
            'key' => 'uptime_percent',
            'title' => 'Процент доступности сервиса',
            'prompt' => "Ввод: n, затем n значений 0/1 (1 - сервис доступен).\nВывод: процент доступности с точностью до 2 знаков.",
            'tests' => [
                ['stdin' => "6\n1 1 1 0 1 0\n", 'expected' => "66.67"],
                ['stdin' => "4\n1 1 1 1\n", 'expected' => "100.00"],
            ]
        ],
        [
            'key' => 'top_endpoint',
            'title' => 'Самый частый endpoint',
            'prompt' => "Ввод: n, затем n строк endpoint.\nВывод: endpoint с максимальной частотой; при равенстве - лексикографически минимальный.",
            'tests' => [
                ['stdin' => "6\n/api/users\n/api/orders\n/api/users\n/api/health\n/api/users\n/api/orders\n", 'expected' => "/api/users"],
                ['stdin' => "4\n/a\n/b\n/a\n/b\n", 'expected' => "/a"],
            ]
        ],
        [
            'key' => 'jwt_time_check',
            'title' => 'Проверка JWT по времени',
            'prompt' => "Ввод: now, iat, exp.\nВывод: NOT_YET если now < iat, EXPIRED если now >= exp, иначе VALID.",
            'tests' => [
                ['stdin' => "1700000100 1700000000 1700003600\n", 'expected' => "VALID"],
                ['stdin' => "1700004000 1700000000 1700003600\n", 'expected' => "EXPIRED"],
            ]
        ],
        [
            'key' => 'queue_sim',
            'title' => 'Симуляция очереди задач',
            'prompt' => "Ввод: q операций: PUSH x или POP.\nДля каждой POP вывести извлеченное значение, либо EMPTY.",
            'tests' => [
                ['stdin' => "6\nPUSH 10\nPUSH 20\nPOP\nPOP\nPOP\nPUSH 7\n", 'expected' => "10\n20\nEMPTY"],
                ['stdin' => "5\nPOP\nPUSH 1\nPUSH 2\nPOP\nPOP\n", 'expected' => "EMPTY\n1\n2"],
            ]
        ],
        [
            'key' => 'range_sum_queries',
            'title' => 'Сумма на диапазоне',
            'prompt' => "Ввод: n, массив, q запросов l r (1-based).\nВывод: сумму на каждом диапазоне.",
            'tests' => [
                ['stdin' => "5\n2 4 6 8 10\n3\n1 3\n2 5\n4 4\n", 'expected' => "12\n28\n8"],
                ['stdin' => "4\n1 1 1 1\n2\n1 4\n2 3\n", 'expected' => "4\n2"],
            ]
        ],
        [
            'key' => 'rollback_version',
            'title' => 'Версия для rollback',
            'prompt' => "Ввод: n версий (целые), затем failed_version.\nВывод: максимальную версию < failed_version, иначе 0.",
            'tests' => [
                ['stdin' => "5\n101 103 105 107 109\n106\n", 'expected' => "105"],
                ['stdin' => "4\n10 20 30 40\n10\n", 'expected' => "0"],
            ]
        ],
        [
            'key' => 'config_hash',
            'title' => 'Контрольный хеш конфигурации',
            'prompt' => "Ввод: строка s.\nВычислите hash = (hash*131 + code(c)) mod 1000000007.\nВывод: hash.",
            'tests' => [
                ['stdin' => "abc\n", 'expected' => "1677554"],
                ['stdin' => "A\n", 'expected' => "65"],
            ]
        ],
    ];

    $contestTemplates = [
        [
            'title' => 'A + B',
            'difficulty' => 'easy',
            'make' => function ($k) {
                $a = 10 + $k;
                $b = 20 - $k;
                return [
                    'statement' => 'Даны два целых числа A и B. Выведите их сумму.',
                    'input' => 'Одна строка: A B',
                    'output' => 'Одно число: A + B',
                    'tests' => [['in' => "$a $b", 'out' => (string) ($a + $b)], ['in' => (-$k) . ' ' . ($k + 3), 'out' => '3']],
                ];
            }
        ],
        [
            'title' => 'Палиндром строки',
            'difficulty' => 'easy',
            'make' => function ($k) {
                $s1 = str_repeat('a', 2) . str_repeat('b', $k % 3) . str_repeat('a', 2);
                $s2 = 'abca';
                return [
                    'statement' => 'Дана строка S. Выведите YES, если она палиндром, иначе NO.',
                    'input' => 'Одна строка S',
                    'output' => 'YES или NO',
                    'tests' => [['in' => $s1, 'out' => 'YES'], ['in' => $s2, 'out' => 'NO']],
                ];
            }
        ],
        [
            'title' => 'Максимум массива',
            'difficulty' => 'easy',
            'make' => function ($k) {
                $arr = [$k, 3, 7, -2, 5];
                $max = max($arr);
                return [
                    'statement' => 'Дано N и массив из N чисел. Найдите максимум.',
                    'input' => "N\narray",
                    'output' => "max",
                    'tests' => [['in' => "5\n" . implode(' ', $arr), 'out' => (string) $max], ['in' => "3\n-1 -5 -2", 'out' => '-1']],
                ];
            }
        ],
        [
            'title' => 'Чётные и нечётные',
            'difficulty' => 'easy',
            'make' => function ($k) {
                $arr = [$k, $k + 1, $k + 2, $k + 3, $k + 4];
                $even = count(array_filter($arr, fn($v) => $v % 2 === 0));
                $odd = count($arr) - $even;
                return [
                    'statement' => 'Дано N чисел. Выведите два числа: количество чётных и количество нечётных.',
                    'input' => "N\narray",
                    'output' => "even odd",
                    'tests' => [['in' => "5\n" . implode(' ', $arr), 'out' => $even . ' ' . $odd], ['in' => "3\n2 4 6", 'out' => '3 0']],
                ];
            }
        ],
        [
            'title' => 'Среднее значение',
            'difficulty' => 'medium',
            'make' => function ($k) {
                $arr = [$k, 2, 3, 4];
                $avg = number_format(array_sum($arr) / count($arr), 2, '.', '');
                return [
                    'statement' => 'Дано N чисел. Выведите среднее с точностью до 2 знаков.',
                    'input' => "N\nN чисел",
                    'output' => 'Среднее',
                    'tests' => [['in' => "4\n" . implode(' ', $arr), 'out' => $avg], ['in' => "2\n10 0", 'out' => '5.00']],
                ];
            }
        ],
        [
            'title' => 'НОД двух чисел',
            'difficulty' => 'medium',
            'make' => function ($k) {
                $a = 6 + $k;
                $b = 9 + $k;
                $g = function ($x, $y) use (&$g) {
                    return $y === 0 ? $x : $g($y, $x % $y);
                };
                $gcd = $g($a, $b);
                return [
                    'statement' => 'Даны a и b. Выведите gcd(a,b).',
                    'input' => 'a b',
                    'output' => 'gcd',
                    'tests' => [['in' => "$a $b", 'out' => (string) $gcd], ['in' => "7 13", 'out' => '1']],
                ];
            }
        ],
        [
            'title' => 'Частоты символов',
            'difficulty' => 'medium',
            'make' => function ($k) {
                $s = ($k % 2 === 0) ? 'aba' : 'xyz';
                $out = ($s === 'aba') ? "a:2\nb:1\nc:0" : "x:1\ny:1\nz:1";
                return [
                    'statement' => 'Дана строка S. Выведите частоту каждой буквы латинского алфавита (a..z) в формате a:count.',
                    'input' => "S",
                    'output' => "26 строк",
                    'tests' => [['in' => $s, 'out' => $out], ['in' => "abc", 'out' => "a:1\nb:1\nc:1"]],
                ];
            }
        ],
        [
            'title' => 'Две суммы',
            'difficulty' => 'medium',
            'make' => function ($k) {
                $kval = 9 + $k;
                return [
                    'statement' => 'Дан массив и число K. Найдите, есть ли пара элементов с суммой K.',
                    'input' => "N K\narray",
                    'output' => "YES/NO",
                    'tests' => [['in' => "5 {$kval}\n1 4 5 3 2", 'out' => 'YES'], ['in' => "4 100\n1 2 3 4", 'out' => 'NO']],
                ];
            }
        ],
        [
            'title' => 'Баланс скобок',
            'difficulty' => 'hard',
            'make' => function ($k) {
                $s = ($k % 2 === 0) ? "{[()()]}" : "([)]";
                $out = ($k % 2 === 0) ? "YES" : "NO";
                return [
                    'statement' => 'Дана строка из символов ()[]{}. Определите, является ли она корректной скобочной последовательностью.',
                    'input' => "S",
                    'output' => "YES/NO",
                    'tests' => [['in' => $s, 'out' => $out], ['in' => "()", 'out' => 'YES']],
                ];
            }
        ],
        [
            'title' => 'Путь в лабиринте',
            'difficulty' => 'hard',
            'make' => function ($k) {
                $grid = ($k % 2 === 0)
                    ? "2 2\n0 0\n1 0"
                    : "3 3\n0 1 0\n0 1 0\n0 0 0";
                $ans = ($k % 2 === 0) ? '2' : '4';
                return [
                    'statement' => 'Дана матрица 0/1, где 0 — проход, 1 — стена. Найдите минимальное число шагов от (0,0) до (n-1,m-1).',
                    'input' => "N M\nmatrix",
                    'output' => "min_steps",
                    'tests' => [['in' => $grid, 'out' => $ans], ['in' => "1 1\n0", 'out' => '0']],
                ];
            }
        ],
    ];

    $contestTaskPool = [];
    $contestTaskIndex = 0;
    for ($round = 1; $round <= 20; $round++) {
        foreach ($contestTemplates as $tpl) {
            $make = $tpl['make'];
            $payload = $make($round);
            $uniqueTitle = $makeUniqueSuffix($contestTaskIndex);
            $contestTaskPool[] = [
                'title' => $tpl['title'] . ' — ' . $uniqueTitle,
                'difficulty' => $tpl['difficulty'],
                'statement' => $payload['statement'] . ' ' . $makeUniqueContext($contestTaskIndex),
                'input' => $payload['input'],
                'output' => $payload['output'],
                'tests' => $payload['tests'],
            ];
            $contestTaskIndex++;
        }
    }
    $sqlPracticeCatalog = [
        [
            'title' => 'MRR активных подписок',
            'prompt' => "Посчитайте месячную выручку (mrr) по активным подпискам.",
            'setup_sql' => [
                "DROP TABLE IF EXISTS subscriptions",
                "CREATE TABLE subscriptions (id INT, user_id INT, plan VARCHAR(20), price_monthly INT, status VARCHAR(20))",
                "INSERT INTO subscriptions VALUES (1,101,'pro',49,'active'),(2,102,'team',99,'active'),(3,103,'pro',49,'canceled')"
            ],
            'expected_sql' => "SELECT SUM(price_monthly) AS mrr FROM subscriptions WHERE status = 'active'"
        ],
        [
            'title' => 'Просроченные инциденты SLA',
            'prompt' => "Выведите id инцидентов со статусом open и минутами ответа > 30.",
            'setup_sql' => [
                "DROP TABLE IF EXISTS incidents",
                "CREATE TABLE incidents (id INT, status VARCHAR(20), response_minutes INT)",
                "INSERT INTO incidents VALUES (1,'open',45),(2,'closed',20),(3,'open',15),(4,'open',75)"
            ],
            'expected_sql' => "SELECT id FROM incidents WHERE status = 'open' AND response_minutes > 30 ORDER BY id"
        ],
        [
            'title' => 'Конверсия по источникам',
            'prompt' => "Для каждой source посчитайте visits и purchases.",
            'setup_sql' => [
                "DROP TABLE IF EXISTS events",
                "CREATE TABLE events (id INT, source VARCHAR(20), event_type VARCHAR(20))",
                "INSERT INTO events VALUES (1,'ads','visit'),(2,'ads','purchase'),(3,'seo','visit'),(4,'seo','visit'),(5,'seo','purchase')"
            ],
            'expected_sql' => "SELECT source, SUM(event_type = 'visit') AS visits, SUM(event_type = 'purchase') AS purchases FROM events GROUP BY source ORDER BY source"
        ],
        [
            'title' => 'Пользователи без платежей',
            'prompt' => "Выведите пользователей, у которых нет записей в payments.",
            'setup_sql' => [
                "DROP TABLE IF EXISTS users",
                "DROP TABLE IF EXISTS payments",
                "CREATE TABLE users (id INT, email VARCHAR(60))",
                "CREATE TABLE payments (id INT, user_id INT, amount INT)",
                "INSERT INTO users VALUES (1,'a@x.com'),(2,'b@x.com'),(3,'c@x.com')",
                "INSERT INTO payments VALUES (10,1,100),(11,1,50),(12,3,70)"
            ],
            'expected_sql' => "SELECT u.id, u.email FROM users u LEFT JOIN payments p ON p.user_id = u.id WHERE p.id IS NULL ORDER BY u.id"
        ],
        [
            'title' => 'Топ товары по выручке',
            'prompt' => "Выведите 3 товара с максимальной суммой quantity*price.",
            'setup_sql' => [
                "DROP TABLE IF EXISTS order_items",
                "CREATE TABLE order_items (id INT, product VARCHAR(50), quantity INT, price INT)",
                "INSERT INTO order_items VALUES (1,'SSD',2,80),(2,'RAM',4,30),(3,'SSD',1,90),(4,'CPU',1,220),(5,'RAM',2,35)"
            ],
            'expected_sql' => "SELECT product, SUM(quantity * price) AS revenue FROM order_items GROUP BY product ORDER BY revenue DESC, product ASC LIMIT 3"
        ],
        [
            'title' => 'Активность по неделям',
            'prompt' => "Посчитайте число логинов по week_start (поле уже содержит дату начала недели).",
            'setup_sql' => [
                "DROP TABLE IF EXISTS logins",
                "CREATE TABLE logins (id INT, user_id INT, week_start DATE)",
                "INSERT INTO logins VALUES (1,10,'2026-01-05'),(2,11,'2026-01-05'),(3,10,'2026-01-12')"
            ],
            'expected_sql' => "SELECT week_start, COUNT(*) AS total_logins FROM logins GROUP BY week_start ORDER BY week_start"
        ],
    ];
    $topics = [
        'htmlcss' => [
            'HTML структура и семантика',
            'Теги текста и медиа',
            'Формы и валидация',
            'Таблицы и списки',
            'Основы CSS и селекторы',
            'Каскад и специфичность',
            'Box model и позиционирование',
            'Flexbox',
            'Grid',
            'Типографика',
            'Цвет и фон',
            'Анимации и переходы',
            'Адаптивность и media queries',
            'БЭМ и архитектура CSS',
            'CSS переменные и calc()',
            'Стилизация форм',
            'Доступность (a11y)',
            'SVG и иконки',
            'Оптимизация верстки',
            'Мини-проект лендинга'
        ],
        'javascript' => [
            'Введение и инструменты',
            'Переменные и типы',
            'Операторы и сравнения',
            'Условия и циклы',
            'Функции и области',
            'Массивы и методы',
            'Объекты и прототипы',
            'DOM и события',
            'Формы и валидация',
            'Fetch и API',
            'Промисы',
            'Async/await',
            'Модули и сборка',
            'Ошибки и отладка',
            'LocalStorage',
            'Работа со временем',
            'ООП в JS',
            'Тестирование базовое',
            'Мини‑проект',
            'Оптимизация'
        ],
        'php' => [
            'Установка и PHP CLI',
            'Синтаксис и типы',
            'Условия и циклы',
            'Функции',
            'Массивы',
            'Строки',
            'Файлы и формы',
            'Сессии и куки',
            'PDO и база данных',
            'CRUD и валидация',
            'Ошибки и исключения',
            'Безопасность',
            'Шаблоны и маршруты',
            'ООП',
            'Автозагрузка',
            'Composer',
            'REST API',
            'Авторизация',
            'Тестирование',
            'Итоговый проект'
        ],
        'laravel' => [
            'Установка и структура',
            'Маршруты',
            'Контроллеры',
            'Blade шаблоны',
            'Миграции',
            'Eloquent модели',
            'Связи',
            'Валидация',
            'Авторизация',
            'Middleware',
            'API ресурсы',
            'Очереди',
            'События',
            'Файлы и хранение',
            'Тестирование',
            'Сервис‑контейнер',
            'Локализация',
            'Оптимизация',
            'Деплой',
            'Итоговый проект'
        ],
        'sql' => [
            'Введение и таблицы',
            'SELECT базовый',
            'WHERE',
            'ORDER BY LIMIT',
            'Агрегации',
            'GROUP BY HAVING',
            'JOIN',
            'Подзапросы',
            'CTE',
            'Оконные функции',
            'Индексы',
            'Транзакции',
            'Нормализация',
            'Изменение схемы',
            'INSERT UPDATE DELETE',
            'Представления',
            'Права доступа',
            'Оптимизация запросов',
            'Практика отчетов',
            'Проект'
        ],
        'nosql' => [
            'Документные БД',
            'Коллекции и документы',
            'CRUD операции',
            'Схемы и валидация',
            'Индексы',
            'Агрегации',
            'Репликация',
            'Шардирование',
            'Консистентность',
            'Кэширование',
            'Key‑value',
            'Column‑family',
            'Graph DB',
            'Поиск',
            'Транзакции',
            'Безопасность',
            'Мониторинг',
            'Проектирование',
            'Миграции данных',
            'Итоговый проект'
        ],
        'programming' => [
            'Среда и Hello World',
            'Типы данных',
            'Ввод/вывод',
            'Условия',
            'Циклы',
            'Функции',
            'Массивы/списки',
            'Строки',
            'Коллекции',
            'Структуры/классы',
            'ООП: инкапсуляция',
            'ООП: наследование',
            'ООП: полиморфизм',
            'Ошибки и исключения',
            'Файлы',
            'Алгоритмы и сложность',
            'Рекурсия',
            'Библиотеки/пакеты',
            'Тестирование и отладка',
            'Мини‑проект'
        ],
        'git' => [
            'Что такое Git',
            'init/clone',
            'add/commit',
            'status/log',
            'Ветки',
            'merge',
            'rebase',
            'Конфликты',
            'stash',
            'reset/checkout',
            'tags',
            'remote',
            'pull/push',
            'fork и PR',
            'gitignore',
            'workflows',
            'hooks',
            'безопасность',
            'best practices',
            'Итоговый сценарий'
        ],
        'devops' => [
            'Введение и роли',
            'Linux базовые',
            'Сети и DNS',
            'HTTP',
            'CI/CD',
            'Docker',
            'Docker Compose',
            'Kubernetes базовые',
            'IaC',
            'Облака',
            'Мониторинг',
            'Логирование',
            'Безопасность',
            'Резервное копирование',
            'Наблюдаемость',
            'Секреты',
            'Скалирование',
            'Cost optimization',
            'Incident response',
            'Проект'
        ],
        'design' => [
            'Введение в дизайн',
            'Композиция',
            'Типографика',
            'Цвет',
            'Сетка',
            'Иконки',
            'UI компоненты',
            'UX исследования',
            'Персоны',
            'User flow',
            'Wireframes',
            'Prototyping',
            'Дизайн‑системы',
            'Доступность',
            'Мобильный UI',
            'Графика',
            'Брендинг',
            'Хенд‑офф',
            'Тестирование',
            'Проект'
        ],
        'mobile' => [
            'Платформы и стек',
            'UI основы',
            'Навигация',
            'Состояние',
            'Сети',
            'Хранение',
            'Авторизация',
            'Push',
            'Камера/медиа',
            'Геолокация',
            'Фоновые задачи',
            'Оптимизация',
            'Тестирование',
            'Сборка',
            'Публикация',
            'Cross‑platform',
            'Гайдлайны',
            'Безопасность',
            'Analytics',
            'Проект'
        ],
        'desktop' => [
            'Платформы и стек',
            'UI toolkit',
            'События и формы',
            'Меню и панели',
            'Работа с файлами',
            'Многопоточность',
            'IPC',
            'Настройки',
            'Локализация',
            'Плагины',
            'Локальная БД',
            'Обновления',
            'Установка',
            'Логи',
            'Тестирование',
            'Оптимизация',
            'Безопасность',
            'UX для desktop',
            'Скриптование',
            'Проект'
        ],
        'english_a1' => [
            'Alphabet & greetings',
            'Present Simple',
            'To be',
            'Family',
            'Numbers & time',
            'Food',
            'Daily routine',
            'There is/are',
            'Prepositions',
            'Past Simple (was/were)',
            'Shopping',
            'Weather',
            'Hobbies',
            'Imperatives',
            'Questions',
            'Modal can',
            'Comparatives',
            'Places in town',
            'Future (going to)',
            'Review'
        ],
        'english_a2' => [
            'Present Continuous',
            'Past Simple regular',
            'Past Simple irregular',
            'Count/uncount',
            'Much/many',
            'Present Perfect basics',
            'Travel',
            'Health',
            'Work & study',
            'Phrasal verbs',
            'Articles',
            'Relative clauses',
            'Future will',
            'Conditionals 0/1',
            'Passive basics',
            'Reported speech basics',
            'Adverbs',
            'Comparatives & superlatives',
            'Narratives',
            'Review'
        ],
        'english_b1' => [
            'Past Perfect',
            'Present Perfect vs Past',
            'Conditionals 1/2',
            'Passive voice',
            'Gerunds/infinitives',
            'Modals of deduction',
            'Reported speech',
            'Relative clauses',
            'Linking words',
            'Present perfect continuous',
            'Future forms',
            'Writing emails',
            'Presentations',
            'Debate',
            'Vocabulary expansion',
            'Phrasal verbs B1',
            'Collocations',
            'Reading strategies',
            'Listening strategies',
            'Review'
        ]
    ];
    $starterCode = function ($lang) {
        switch ($lang) {
            case 'python':
                return "def solve():\n    # TODO\n    pass\n\nif __name__ == '__main__':\n    solve()\n";
            case 'js':
                return "function solve() {\n  // TODO\n}\n\nsolve();\n";
            case 'cpp':
                return "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    // TODO\n    return 0;\n}\n";
            case 'c':
                return "#include <stdio.h>\n\nint main() {\n    // TODO\n    return 0;\n}\n";
            case 'csharp':
                return "using System;\nclass Program {\n    static void Main() {\n        // TODO\n    }\n}\n";
            case 'java':
                return "import java.util.*;\npublic class Main {\n    public static void main(String[] args) {\n        // TODO\n    }\n}\n";
            case 'mysql':
            case 'pgsql':
                return "SELECT * FROM table_name;";
            default:
                return '';
        }
    };

    $buildContent = function ($courseTitle, $topic) {
        $goal = "Разобрать тему «{$topic}» и применить в контексте курса «{$courseTitle}».";
        return "Цели:\n- {$goal}\n\nПлан:\n1) Ключевые понятия и терминология\n2) Примеры и типичные ошибки\n3) Практика и закрепление\n\nТеория:\n- Почему тема важна в реальных проектах\n- Связь с предыдущими уроками\n- Какие навыки формирует\n\nПрактика:\n- Мини‑задача по теме\n- Самопроверка по чек‑листу\n- Короткий вывод результата\n\nРезультат:\n- Вы сможете применять «{$topic}» на практике.";
    };

    $buildLessons = function ($courseTitle, $trackKey, $practiceLang = '') use ($topics, $buildContent, $practiceCatalog, $sqlPracticeCatalog, $starterCode) {
        $list = $topics[$trackKey] ?? [];
        $lessons = [];
        foreach ($list as $idx => $topic) {
            $lesson = [
                'title' => 'Урок ' . ($idx + 1) . '. ' . $topic,
                'type' => 'article',
                'content' => $buildContent($courseTitle, $topic),
                'video_url' => '',
                'materials_title' => '',
                'materials_url' => ''
            ];

            if ($practiceLang === 'mysql' || $practiceLang === 'pgsql') {
                $task = $sqlPracticeCatalog[$idx % count($sqlPracticeCatalog)];
                $lesson['practice'] = [
                    'language' => $practiceLang,
                    'title' => $task['title'] . ' — ' . $topic,
                    'prompt' => "Тема урока: «{$topic}».\n" . $task['prompt'],
                    'starter_code' => $starterCode($practiceLang),
                    'tests' => [
                        [
                            'setup_sql' => $task['setup_sql'],
                            'expected_sql' => $task['expected_sql']
                        ]
                    ]
                ];
            } elseif ($practiceLang !== '') {
                $task = $practiceCatalog[$idx % count($practiceCatalog)];
                $lesson['practice'] = [
                    'language' => $practiceLang,
                    'title' => $task['title'] . ' — ' . $topic,
                    'prompt' => "Тема урока: «{$topic}».\n" . $task['prompt'],
                    'starter_code' => $starterCode($practiceLang),
                    'tests' => $task['tests']
                ];
            }

            $lessons[] = $lesson;
        }
        return $lessons;
    };
    $rotateArray = function (array $items, int $shift): array {
        $count = count($items);
        if ($count <= 1) {
            return $items;
        }
        $shift = $shift % $count;
        if ($shift < 0) {
            $shift += $count;
        }
        if ($shift === 0) {
            return $items;
        }
        return array_merge(array_slice($items, $shift), array_slice($items, 0, $shift));
    };

    $examBasePool = function ($trackKey) {
        $vocab = [
            'htmlcss' => ['семантика', 'селектор', 'flexbox', 'grid', 'box‑model', 'media query'],
            'javascript' => ['let/const', 'функция', 'промис', 'async/await', 'DOM', 'fetch'],
            'php' => ['массив', 'PDO', 'сессия', 'валидация', 'исключение', 'namespace'],
            'laravel' => ['route', 'controller', 'middleware', 'migration', 'Eloquent', 'Blade'],
            'sql' => ['SELECT', 'JOIN', 'GROUP BY', 'индекс', 'транзакция', 'CTE'],
            'nosql' => ['документ', 'коллекция', 'шардирование', 'репликация', 'консистентность', 'индекс'],
            'programming' => ['тип данных', 'цикл', 'функция', 'класс', 'исключение', 'алгоритм'],
            'git' => ['commit', 'branch', 'merge', 'rebase', 'stash', 'reset'],
            'devops' => ['CI/CD', 'Docker', 'Kubernetes', 'мониторинг', 'IaC', 'секреты'],
            'design' => ['типографика', 'сетка', 'UX', 'wireframe', 'color', 'accessibility'],
            'mobile' => ['state', 'navigation', 'storage', 'push', 'lifecycle', 'performance'],
            'desktop' => ['UI toolkit', 'threads', 'IPC', 'installer', 'updates', 'settings'],
            'english_a1' => ['to be', 'present simple', 'numbers', 'prepositions', 'questions', 'can'],
            'english_a2' => ['present continuous', 'past simple', 'much/many', 'present perfect', 'future will', 'conditionals'],
            'english_b1' => ['past perfect', 'passive', 'gerund', 'reported speech', 'linking words', 'collocations']
        ];
        return $vocab[$trackKey] ?? ['термин', 'понятие', 'пример'];
    };

    $buildExamQuestions = function ($courseTitle, $trackKey, $questionCount = 30) use ($topics, $examBasePool, $rotateArray) {
        $topicPool = array_values(array_unique(array_filter(array_map('trim', (array) ($topics[$trackKey] ?? [])))));
        $vocab = array_values(array_unique(array_filter(array_map('trim', (array) $examBasePool($trackKey)))));
        if (empty($topicPool)) {
            $topicPool = $vocab;
        }
        if (empty($topicPool)) {
            $topicPool = ['Ключевая тема'];
        }
        while (count($topicPool) < 4) {
            $topicPool[] = 'Дополнительный модуль ' . (count($topicPool) + 1);
        }
        if (empty($vocab)) {
            $vocab = ['практика', 'теория', 'проект', 'ревью'];
        }

        $questionTemplates = [
            "Какой модуль курса «%s» лучше всего покрывает тему «%s»?",
            "Где в программе «%s» изучается «%s» наиболее подробно?",
            "Для закрепления термина «%s» в курсе «%s» нужно выбрать модуль:",
            "Какой раздел курса «%s» напрямую связан с понятием «%s»?"
        ];

        $questions = [];
        $poolSize = count($topicPool);
        for ($i = 0; $i < $questionCount; $i++) {
            $correctTopic = $topicPool[$i % $poolSize];
            $keyword = $vocab[$i % count($vocab)];

            if ($i % 10 === 0) {
                $isFalseStatement = ($i % 20 === 10);
                $statementTopic = $topicPool[($i + 3) % $poolSize];
                $questions[] = [
                    'type' => 'true_false',
                    'question' => $isFalseStatement
                        ? "Утверждение: модуль «{$statementTopic}» не относится к курсу «{$courseTitle}»."
                        : "Утверждение: модуль «{$correctTopic}» входит в программу курса «{$courseTitle}».",
                    'correct_answer' => $isFalseStatement ? false : true
                ];
                continue;
            }

            $distractors = [];
            $cursor = 1;
            while (count($distractors) < 3 && $cursor < ($poolSize + 8)) {
                $candidate = $topicPool[($i + $cursor * 2) % $poolSize];
                if ($candidate !== $correctTopic && !in_array($candidate, $distractors, true)) {
                    $distractors[] = $candidate;
                }
                $cursor++;
            }
            foreach (['Маркетинг и продажи', 'История IT', 'Нерелевантный раздел'] as $fallback) {
                if (count($distractors) >= 3) {
                    break;
                }
                if ($fallback !== $correctTopic && !in_array($fallback, $distractors, true)) {
                    $distractors[] = $fallback;
                }
            }

            $options = array_slice(array_values(array_unique(array_merge([$correctTopic], $distractors))), 0, 4);
            $options = $rotateArray($options, $i % max(1, count($options)));
            $tpl = $questionTemplates[$i % count($questionTemplates)];
            $questionText = ($i % 2 === 0)
                ? sprintf($tpl, $courseTitle, $keyword)
                : sprintf($tpl, $courseTitle, $correctTopic);

            $questions[] = [
                'type' => 'true_false',
                'question' => $questionText,
                'options' => $options,
                'correct_answer' => $correctTopic
            ];
        }

        return $questions;
    };
    $courseSpecs = [
        ['title' => 'HTML+CSS', 'track' => 'htmlcss', 'category' => 'frontend', 'skills' => ['HTML', 'CSS', 'Flexbox', 'Grid'], 'practice' => ''],
        ['title' => 'JavaScript', 'track' => 'javascript', 'category' => 'frontend', 'skills' => ['JavaScript', 'DOM', 'Async'], 'practice' => 'js'],
        ['title' => 'PHP', 'track' => 'php', 'category' => 'backend', 'skills' => ['PHP', 'PDO', 'OOP'], 'practice' => ''],
        ['title' => 'Laravel', 'track' => 'laravel', 'category' => 'backend', 'skills' => ['Laravel', 'Eloquent', 'Blade'], 'practice' => ''],
        ['title' => 'MySQL', 'track' => 'sql', 'category' => 'backend', 'skills' => ['MySQL', 'SQL', 'Indexes'], 'practice' => 'mysql'],
        ['title' => 'PostgreSQL', 'track' => 'sql', 'category' => 'backend', 'skills' => ['PostgreSQL', 'SQL', 'CTE'], 'practice' => 'pgsql'],
        ['title' => 'NoSQL', 'track' => 'nosql', 'category' => 'backend', 'skills' => ['MongoDB', 'NoSQL', 'Sharding'], 'practice' => ''],
        ['title' => 'C++', 'track' => 'programming', 'category' => 'backend', 'skills' => ['C++', 'STL', 'Algorithms'], 'practice' => 'cpp'],
        ['title' => 'Python', 'track' => 'programming', 'category' => 'backend', 'skills' => ['Python', 'OOP', 'Scripts'], 'practice' => 'python'],
        ['title' => 'C', 'track' => 'programming', 'category' => 'backend', 'skills' => ['C', 'Pointers', 'Memory'], 'practice' => 'c'],
        ['title' => 'C#', 'track' => 'programming', 'category' => 'backend', 'skills' => ['C#', '.NET', 'OOP'], 'practice' => 'csharp'],
        ['title' => 'Java', 'track' => 'programming', 'category' => 'backend', 'skills' => ['Java', 'Collections', 'OOP'], 'practice' => 'java'],
        ['title' => 'Git', 'track' => 'git', 'category' => 'devops', 'skills' => ['Git', 'Branching', 'Merge'], 'practice' => ''],
        ['title' => 'DevOps', 'track' => 'devops', 'category' => 'devops', 'skills' => ['CI/CD', 'Docker', 'Kubernetes'], 'practice' => ''],
        ['title' => 'Design', 'track' => 'design', 'category' => 'design', 'skills' => ['UX', 'UI', 'Typography'], 'practice' => ''],
        ['title' => 'Mobile-dev', 'track' => 'mobile', 'category' => 'other', 'skills' => ['Mobile', 'UI', 'API'], 'practice' => ''],
        ['title' => 'Desktop-dev', 'track' => 'desktop', 'category' => 'other', 'skills' => ['Desktop', 'UI', 'Threads'], 'practice' => ''],
        ['title' => 'English-A1', 'track' => 'english_a1', 'category' => 'other', 'skills' => ['English', 'A1', 'Grammar'], 'practice' => ''],
        ['title' => 'English-A2', 'track' => 'english_a2', 'category' => 'other', 'skills' => ['English', 'A2', 'Grammar'], 'practice' => ''],
        ['title' => 'English-B1', 'track' => 'english_b1', 'category' => 'other', 'skills' => ['English', 'B1', 'Grammar'], 'practice' => ''],
    ];

    foreach ($courseSpecs as $spec) {
        $courseTitle = $spec['title'];
        $trackKey = $spec['track'];
        $lessons = $buildLessons($courseTitle, $trackKey, $spec['practice']);
        $exam = [
            'time_limit_minutes' => 60,
            'pass_percent' => 70,
            'questions' => $buildExamQuestions($courseTitle, $trackKey)
        ];
        $courseDef = [
            'title' => $courseTitle,
            'instructor' => 'ITsphere360 Academy',
            'description' => "Полный курс по теме «{$courseTitle}». 20 уроков, практика и итоговый экзамен.",
            'category' => $spec['category'],
            'image_url' => 'https://placehold.co/600x400/4f46e5/ffffff?text=' . rawurlencode($courseTitle),
            'skills' => $spec['skills'],
            'lessons' => $lessons,
            'exam' => $exam,
        ];
        $created[] = tfSeedCourse($pdo, $courseDef);
    }
    $buildRoadmapMaterials = function (array $spec, string $topic): array {
        $title = strtolower((string) ($spec['title'] ?? ''));
        $primary = 'https://roadmap.sh';
        if (str_contains($title, 'python')) {
            $primary = 'https://docs.python.org/3/';
        } elseif (str_contains($title, 'c++')) {
            $primary = 'https://en.cppreference.com/w/';
        } elseif (str_contains($title, 'mysql')) {
            $primary = 'https://dev.mysql.com/doc/';
        } elseif (str_contains($title, 'postgres')) {
            $primary = 'https://www.postgresql.org/docs/';
        } elseif (str_contains($title, 'javascript')) {
            $primary = 'https://developer.mozilla.org/en-US/docs/Web/JavaScript';
        } elseif (str_contains($title, 'laravel')) {
            $primary = 'https://laravel.com/docs';
        } elseif (str_contains($title, 'php')) {
            $primary = 'https://www.php.net/docs.php';
        } elseif (str_contains($title, 'git')) {
            $primary = 'https://git-scm.com/doc';
        } elseif (str_contains($title, 'devops')) {
            $primary = 'https://kubernetes.io/docs/home/';
        } elseif (str_contains($title, 'html') || str_contains($title, 'css')) {
            $primary = 'https://developer.mozilla.org/en-US/docs/Web';
        }
        return [
            ['title' => "Официальная документация: {$topic}", 'url' => $primary],
            ['title' => "Практический разбор по теме «{$topic}»", 'url' => 'https://www.youtube.com/results?search_query=' . rawurlencode($topic . ' tutorial')],
            ['title' => "Чеклист для self-review: {$topic}", 'url' => 'https://roadmap.sh']
        ];
    };

    $buildRoadmapMiniQuestions = function (string $topic): array {
        return [
            [
                'question' => "Какова ключевая цель изучения темы «{$topic}»?",
                'options' => ['Понять базовые принципы и применить их в задаче', 'Запомнить только определения', 'Изучить историю технологии', 'Избежать практики'],
                'correct_answer' => 'Понять базовые принципы и применить их в задаче'
            ],
            [
                'question' => "Что нужно сделать после изучения материалов по теме «{$topic}»?",
                'options' => ['Пройти мини-тест и проверить понимание', 'Сразу пропустить к следующему модулю', 'Удалить конспекты', 'Только посмотреть видео'],
                'correct_answer' => 'Пройти мини-тест и проверить понимание'
            ],
            [
                'question' => "Какой результат показывает, что тема «{$topic}» освоена?",
                'options' => ['Вы решаете типовую практическую задачу по теме', 'Вы знаете только перевод термина', 'Вы прошли без чтения материалов', 'Вы не делали проверку'],
                'correct_answer' => 'Вы решаете типовую практическую задачу по теме'
            ],
            [
                'question' => "Где чаще всего применяется тема «{$topic}»?",
                'options' => ['В реальных рабочих сценариях проекта', 'Только в теоретических статьях', 'Только в школьных курсах', 'Нигде'],
                'correct_answer' => 'В реальных рабочих сценариях проекта'
            ],
            [
                'question' => "Какой источник наиболее надежен для изучения «{$topic}»?",
                'options' => ['Официальная документация и проверенные гайды', 'Случайные комментарии без примеров', 'Только мемы', 'Старые непроверенные заметки'],
                'correct_answer' => 'Официальная документация и проверенные гайды'
            ],
        ];
    };

    $buildRoadmapFinalQuestions = function (string $roadmapTitle, array $trackTopics, int $count = 30) use ($rotateArray): array {
        $pool = array_values(array_unique(array_filter(array_map('trim', $trackTopics))));
        if (empty($pool)) {
            $pool = ['Основная тема'];
        }
        while (count($pool) < 4) {
            $pool[] = 'Дополнительная тема ' . (count($pool) + 1);
        }
        $size = count($pool);
        $questions = [];
        for ($i = 0; $i < $count; $i++) {
            $correct = $pool[$i % $size];
            $distractors = [];
            $cursor = 1;
            while (count($distractors) < 3 && $cursor < ($size + 8)) {
                $candidate = $pool[($i + $cursor * 3) % $size];
                if ($candidate !== $correct && !in_array($candidate, $distractors, true)) {
                    $distractors[] = $candidate;
                }
                $cursor++;
            }
            $options = array_slice(array_values(array_unique(array_merge([$correct], $distractors))), 0, 4);
            $options = $rotateArray($options, $i % max(1, count($options)));
            $questions[] = [
                'question' => "В экзамене роадмапа «{$roadmapTitle}»: какой модуль покрывает тему «{$correct}»?",
                'options' => $options,
                'correct_answer' => $correct
            ];
        }
        return $questions;
    };

    // Roadmaps
    ensureRoadmapTables($pdo);
    foreach ($courseSpecs as $spec) {
        $roadmapTitle = 'Roadmap: ' . $spec['title'];
        $description = "Роадмап по теме {$spec['title']}: материалы, мини-тест (5 вопросов) и финальный экзамен (30 вопросов).";
        $stmt = $pdo->prepare("SELECT id FROM roadmap_list WHERE title = ? LIMIT 1");
        $stmt->execute([$roadmapTitle]);
        $roadmapId = (int) ($stmt->fetchColumn() ?: 0);
        if ($roadmapId > 0) {
            $stmt = $pdo->prepare("UPDATE roadmap_list SET description = ? WHERE id = ?");
            $stmt->execute([$description, $roadmapId]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO roadmap_list (title, description) VALUES (?, ?)");
            $stmt->execute([$roadmapTitle, $description]);
            $roadmapId = (int) $pdo->lastInsertId();
        }

        // Пересобираем ноды/уроки/вопросы по текущему шаблону сидера.
        $stmt = $pdo->prepare("DELETE FROM roadmap_nodes WHERE roadmap_title = ?");
        $stmt->execute([$roadmapTitle]);

        $nodeIds = [];
        $trackTopics = $topics[$spec['track']] ?? [];
        $cols = 5;
        $xBase = 80;
        $yBase = 80;
        $xStep = 380;
        $yStep = 240;
        foreach ($trackTopics as $idx => $topic) {
            $row = (int) floor($idx / $cols);
            $col = $idx % $cols;
            if ($row % 2 === 1) {
                $col = ($cols - 1) - $col;
            }
            $x = $xBase + $col * $xStep;
            $y = $yBase + $row * $yStep + (($col % 2) * 20);
            $isExam = ($idx === count($trackTopics) - 1) ? 1 : 0;
            $materials = $buildRoadmapMaterials($spec, $topic);

            $stmt = $pdo->prepare("
                INSERT INTO roadmap_nodes (title, roadmap_title, topic, materials, x, y, deps, is_exam)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $topic,
                $roadmapTitle,
                $topic,
                tfSafeJson($materials, JSON_UNESCAPED_UNICODE),
                $x,
                $y,
                tfSafeJson([], JSON_UNESCAPED_UNICODE),
                $isExam
            ]);
            $nodeIds[] = (int) $pdo->lastInsertId();
        }

        foreach ($nodeIds as $idx => $nodeId) {
            if ($idx > 0) {
                $deps = tfSafeJson([$nodeIds[$idx - 1]], JSON_UNESCAPED_UNICODE);
                $stmt = $pdo->prepare("UPDATE roadmap_nodes SET deps = ? WHERE id = ?");
                $stmt->execute([$deps, $nodeId]);
            }

            $topic = $trackTopics[$idx] ?? ('Узел ' . ($idx + 1));
            $isExam = ($idx === count($trackTopics) - 1);
            $lessonMaterials = $buildRoadmapMaterials($spec, $topic);
            $stmt = $pdo->prepare("
                INSERT INTO roadmap_lessons (node_id, title, video_url, description, materials, order_index)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $nodeId,
                'Урок ' . ($idx + 1) . '. ' . $topic,
                '',
                $isExam
                ? "Финальное повторение по роадмапу «{$roadmapTitle}». После изучения материалов выполните итоговый экзамен из 30 вопросов."
                : "Практический модуль «{$topic}». Изучите материалы, подтвердите прохождение и сдайте мини-тест из 5 вопросов.",
                tfSafeJson($lessonMaterials, JSON_UNESCAPED_UNICODE),
                1
            ]);

            $quizQuestions = $isExam
                ? $buildRoadmapFinalQuestions($roadmapTitle, $trackTopics, 30)
                : $buildRoadmapMiniQuestions($topic);
            foreach ($quizQuestions as $q) {
                $stmt = $pdo->prepare("INSERT INTO roadmap_quiz_questions (node_id, question, options, correct_answer) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $nodeId,
                    (string) ($q['question'] ?? ''),
                    tfSafeJson(array_values((array) ($q['options'] ?? [])), JSON_UNESCAPED_UNICODE),
                    (string) ($q['correct_answer'] ?? '')
                ]);
            }
        }
        $created[] = ['ok' => true, 'created' => true, 'type' => 'roadmap', 'id' => $roadmapId, 'title' => $roadmapTitle];
    }
    // Contests
    ensureContestsSchema($pdo);
    $contestTemplates = [
        [
            'title' => 'A + B',
            'difficulty' => 'easy',
            'make' => function ($k) {
                $a = 10 + $k;
                $b = 20 - $k;
                return [
                    'statement' => 'Даны два целых числа A и B. Выведите их сумму.',
                    'input' => 'Одна строка: A B',
                    'output' => 'Одно число: A + B',
                    'tests' => [['in' => "$a $b", 'out' => (string) ($a + $b)], ['in' => (-$k) . ' ' . ($k + 3), 'out' => '3']],
                ];
            }
        ],
        [
            'title' => 'Палиндром строки',
            'difficulty' => 'easy',
            'make' => function ($k) {
                $s1 = str_repeat('a', 2) . str_repeat('b', $k % 3) . str_repeat('a', 2);
                $s2 = 'abca';
                return [
                    'statement' => 'Дана строка S. Выведите YES, если она палиндром, иначе NO.',
                    'input' => 'Одна строка S',
                    'output' => 'YES или NO',
                    'tests' => [['in' => $s1, 'out' => 'YES'], ['in' => $s2, 'out' => 'NO']],
                ];
            }
        ],
        [
            'title' => 'Максимум массива',
            'difficulty' => 'easy',
            'make' => function ($k) {
                $arr = [$k, 3, 7, -2, 5];
                $max = max($arr);
                return [
                    'statement' => 'Дано N и массив из N чисел. Найдите максимум.',
                    'input' => "N\narray",
                    'output' => "max",
                    'tests' => [['in' => "5\n" . implode(' ', $arr), 'out' => (string) $max], ['in' => "3\n-1 -5 -2", 'out' => '-1']],
                ];
            }
        ],
        [
            'title' => 'Чётные и нечётные',
            'difficulty' => 'easy',
            'make' => function ($k) {
                $arr = [$k, $k + 1, $k + 2, $k + 3, $k + 4];
                $even = count(array_filter($arr, fn($v) => $v % 2 === 0));
                $odd = count($arr) - $even;
                return [
                    'statement' => 'Дано N чисел. Выведите два числа: количество чётных и количество нечётных.',
                    'input' => "N\narray",
                    'output' => "even odd",
                    'tests' => [['in' => "5\n" . implode(' ', $arr), 'out' => $even . ' ' . $odd], ['in' => "3\n2 4 6", 'out' => '3 0']],
                ];
            }
        ],
        [
            'title' => 'Среднее значение',
            'difficulty' => 'medium',
            'make' => function ($k) {
                $arr = [$k, 2, 3, 4];
                $avg = number_format(array_sum($arr) / count($arr), 2, '.', '');
                return [
                    'statement' => 'Дано N чисел. Выведите среднее с точностью до 2 знаков.',
                    'input' => "N\nN чисел",
                    'output' => 'Среднее',
                    'tests' => [['in' => "4\n" . implode(' ', $arr), 'out' => $avg], ['in' => "2\n10 0", 'out' => '5.00']],
                ];
            }
        ],
        [
            'title' => 'НОД двух чисел',
            'difficulty' => 'medium',
            'make' => function ($k) {
                $a = 6 + $k;
                $b = 9 + $k;
                $g = function ($x, $y) use (&$g) {
                    return $y === 0 ? $x : $g($y, $x % $y);
                };
                $gcd = $g($a, $b);
                return [
                    'statement' => 'Даны a и b. Выведите gcd(a,b).',
                    'input' => 'a b',
                    'output' => 'gcd',
                    'tests' => [['in' => "$a $b", 'out' => (string) $gcd], ['in' => "7 13", 'out' => '1']],
                ];
            }
        ],
        [
            'title' => 'Частоты символов',
            'difficulty' => 'medium',
            'make' => function ($k) {
                $s = ($k % 2 === 0) ? 'aba' : 'xyz';
                $out = ($s === 'aba') ? "a:2\nb:1\nc:0" : "x:1\ny:1\nz:1";
                return [
                    'statement' => 'Дана строка S. Выведите частоту каждой буквы латинского алфавита (a..z) в формате a:count.',
                    'input' => "S",
                    'output' => "26 строк",
                    'tests' => [['in' => $s, 'out' => $out], ['in' => "abc", 'out' => "a:1\nb:1\nc:1"]],
                ];
            }
        ],
        [
            'title' => 'Две суммы',
            'difficulty' => 'medium',
            'make' => function ($k) {
                $kval = 9 + $k;
                return [
                    'statement' => 'Дан массив и число K. Найдите, есть ли пара элементов с суммой K.',
                    'input' => "N K\narray",
                    'output' => "YES/NO",
                    'tests' => [['in' => "5 {$kval}\n1 4 5 3 2", 'out' => 'YES'], ['in' => "4 100\n1 2 3 4", 'out' => 'NO']],
                ];
            }
        ],
        [
            'title' => 'Баланс скобок',
            'difficulty' => 'hard',
            'make' => function ($k) {
                $s = ($k % 2 === 0) ? "{[()()]}" : "([)]";
                $out = ($k % 2 === 0) ? "YES" : "NO";
                return [
                    'statement' => 'Дана строка из символов ()[]{}. Определите, является ли она корректной скобочной последовательностью.',
                    'input' => "S",
                    'output' => "YES/NO",
                    'tests' => [['in' => $s, 'out' => $out], ['in' => "()", 'out' => 'YES']],
                ];
            }
        ],
        [
            'title' => 'Путь в лабиринте',
            'difficulty' => 'hard',
            'make' => function ($k) {
                $grid = ($k % 2 === 0)
                    ? "2 2\n0 0\n1 0"
                    : "3 3\n0 1 0\n0 1 0\n0 0 0";
                $ans = ($k % 2 === 0) ? '2' : '4';
                return [
                    'statement' => 'Дана матрица 0/1, где 0 — проход, 1 — стена. Найдите минимальное число шагов от (0,0) до (n-1,m-1).',
                    'input' => "N M\nmatrix",
                    'output' => "min_steps",
                    'tests' => [['in' => $grid, 'out' => $ans], ['in' => "1 1\n0", 'out' => '0']],
                ];
            }
        ],
    ];

    $contestTaskPool = [];
    $contestTaskIndex = 0;
    for ($round = 1; $round <= 20; $round++) {
        foreach ($contestTemplates as $tpl) {
            $make = $tpl['make'];
            $payload = $make($round);
            $uniqueTitle = $makeUniqueSuffix($contestTaskIndex);
            $contestTaskPool[] = [
                'title' => $tpl['title'] . ' — ' . $uniqueTitle,
                'difficulty' => $tpl['difficulty'],
                'statement' => $payload['statement'] . ' ' . $makeUniqueContext($contestTaskIndex),
                'input' => $payload['input'],
                'output' => $payload['output'],
                'tests' => $payload['tests'],
            ];
            $contestTaskIndex++;
        }
    }

    $algoTemplates = [
        [
            'title' => 'Суммы на отрезках',
            'difficulty' => 'medium',
            'make' => function ($k) {
                $arr = [$k, $k + 1, $k + 2, $k + 3, $k + 4];
                $sum1 = array_sum(array_slice($arr, 0, 3));
                $sum2 = array_sum(array_slice($arr, 1, 4));
                return [
                    'statement' => 'Дан массив и Q запросов сумм на отрезке. Для каждого запроса [l, r] (1-индексация) найдите сумму элементов.',
                    'input' => "N Q\narray\nQ строк l r",
                    'output' => "Суммы для каждого запроса",
                    'tests' => [
                        ['in' => "5 2\n" . implode(' ', $arr) . "\n1 3\n2 5", 'out' => $sum1 . "\n" . $sum2],
                        ['in' => "3 1\n1 2 3\n1 3", 'out' => "6"],
                    ],
                ];
            }
        ],
        [
            'title' => 'Максимальная сумма окна',
            'difficulty' => 'medium',
            'make' => function ($k) {
                $arr = [$k, 2, 5, 1, $k + 3];
                $kwin = 3;
                $s1 = $arr[0] + $arr[1] + $arr[2];
                $s2 = $arr[1] + $arr[2] + $arr[3];
                $s3 = $arr[2] + $arr[3] + $arr[4];
                $max = max($s1, $s2, $s3);
                return [
                    'statement' => 'Дан массив и число K. Найдите максимальную сумму среди всех подотрезков длины K.',
                    'input' => "N K\narray",
                    'output' => "Максимальная сумма",
                    'tests' => [
                        ['in' => "5 {$kwin}\n" . implode(' ', $arr), 'out' => (string) $max],
                        ['in' => "4 2\n1 2 3 4", 'out' => "7"],
                    ],
                ];
            }
        ],
        [
            'title' => 'Две суммы',
            'difficulty' => 'medium',
            'make' => function ($k) {
                $arr = [$k, 6, 1, 3, 2];
                $targetYes = $k + 6;
                return [
                    'statement' => 'Дан массив и число K. Определите, существует ли пара элементов с суммой K.',
                    'input' => "N K\narray",
                    'output' => "YES или NO",
                    'tests' => [
                        ['in' => "5 {$targetYes}\n" . implode(' ', $arr), 'out' => "YES"],
                        ['in' => "4 100\n1 2 3 4", 'out' => "NO"],
                    ],
                ];
            }
        ],
        [
            'title' => 'Длина подстроки без повторов',
            'difficulty' => 'medium',
            'make' => function ($k) {
                $s = ($k % 2 === 0) ? 'abcaefg' : 'pwwkew';
                $max = 0;
                $start = 0;
                $pos = [];
                $len = strlen($s);
                for ($i = 0; $i < $len; $i++) {
                    $ch = $s[$i];
                    if (isset($pos[$ch]) && $pos[$ch] >= $start) {
                        $start = $pos[$ch] + 1;
                    }
                    $pos[$ch] = $i;
                    $max = max($max, $i - $start + 1);
                }
                return [
                    'statement' => 'Дана строка. Найдите длину самой длинной подстроки без повторяющихся символов.',
                    'input' => "S",
                    'output' => "Длина",
                    'tests' => [
                        ['in' => $s, 'out' => (string) $max],
                        ['in' => "aaaa", 'out' => "1"],
                    ],
                ];
            }
        ],
        [
            'title' => 'Проверка анаграмм',
            'difficulty' => 'easy',
            'make' => function ($k) {
                $a = ($k % 2 === 0) ? 'listen' : 'hello';
                $b = ($k % 2 === 0) ? 'silent' : 'bellow';
                $out = ($k % 2 === 0) ? 'YES' : 'NO';
                return [
                    'statement' => 'Даны две строки. Определите, являются ли они анаграммами.',
                    'input' => "S T",
                    'output' => "YES или NO",
                    'tests' => [
                        ['in' => $a . "\n" . $b, 'out' => $out],
                        ['in' => "abc\nab", 'out' => "NO"],
                    ],
                ];
            }
        ],
        [
            'title' => 'Скобочная последовательность',
            'difficulty' => 'easy',
            'make' => function ($k) {
                $s = ($k % 2 === 0) ? "{[()()]}" : "([)]";
                $out = ($k % 2 === 0) ? 'YES' : 'NO';
                return [
                    'statement' => 'Дана строка из символов ()[]{}. Определите, является ли она корректной скобочной последовательностью.',
                    'input' => "S",
                    'output' => "YES или NO",
                    'tests' => [
                        ['in' => $s, 'out' => $out],
                        ['in' => "()", 'out' => "YES"],
                    ],
                ];
            }
        ],
        [
            'title' => 'Компоненты связности',
            'difficulty' => 'medium',
            'make' => function ($k) {
                if ($k % 2 === 0) {
                    $n = 5;
                    $edges = [[1, 2], [2, 3], [4, 5]];
                } else {
                    $n = 6;
                    $edges = [[1, 2], [2, 3], [4, 5]];
                }
                $adj = array_fill(0, $n + 1, []);
                foreach ($edges as $e) {
                    $adj[$e[0]][] = $e[1];
                    $adj[$e[1]][] = $e[0];
                }
                $visited = array_fill(0, $n + 1, false);
                $components = 0;
                for ($v = 1; $v <= $n; $v++) {
                    if ($visited[$v]) {
                        continue;
                    }
                    $components++;
                    $queue = [$v];
                    $visited[$v] = true;
                    for ($qi = 0; $qi < count($queue); $qi++) {
                        $cur = $queue[$qi];
                        foreach ($adj[$cur] as $to) {
                            if (!$visited[$to]) {
                                $visited[$to] = true;
                                $queue[] = $to;
                            }
                        }
                    }
                }
                $lines = [];
                foreach ($edges as $e) {
                    $lines[] = $e[0] . ' ' . $e[1];
                }
                $input = $n . ' ' . count($edges) . "\n" . implode("\n", $lines);
                return [
                    'statement' => 'Дан неориентированный граф. Найдите количество компонент связности.',
                    'input' => "N M\nM ребер (u v)",
                    'output' => "Количество компонент",
                    'tests' => [
                        ['in' => $input, 'out' => (string) $components],
                        ['in' => "3 0\n", 'out' => "3"],
                    ],
                ];
            }
        ],
        [
            'title' => 'Кратчайший путь в решетке',
            'difficulty' => 'hard',
            'make' => function ($k) {
                $grid = ($k % 2 === 0)
                    ? [
                        [0, 0, 0],
                        [1, 1, 0],
                        [0, 0, 0],
                    ]
                    : [
                        [0, 1, 0],
                        [0, 1, 0],
                        [0, 0, 0],
                    ];
                $n = count($grid);
                $m = count($grid[0]);
                $dist = array_fill(0, $n, array_fill(0, $m, -1));
                $queue = [[0, 0]];
                $dist[0][0] = 0;
                $dirs = [[1, 0], [-1, 0], [0, 1], [0, -1]];
                for ($qi = 0; $qi < count($queue); $qi++) {
                    [$x, $y] = $queue[$qi];
                    foreach ($dirs as $d) {
                        $nx = $x + $d[0];
                        $ny = $y + $d[1];
                        if ($nx < 0 || $ny < 0 || $nx >= $n || $ny >= $m) {
                            continue;
                        }
                        if ($grid[$nx][$ny] === 1 || $dist[$nx][$ny] !== -1) {
                            continue;
                        }
                        $dist[$nx][$ny] = $dist[$x][$y] + 1;
                        $queue[] = [$nx, $ny];
                    }
                }
                $out = (string) $dist[$n - 1][$m - 1];
                $rows = [];
                foreach ($grid as $row) {
                    $rows[] = implode(' ', $row);
                }
                return [
                    'statement' => 'Дана матрица 0/1, где 0 — проход, 1 — стена. Найдите минимальное число шагов от (0,0) до (n-1,m-1), или -1 если пути нет.',
                    'input' => "N M\nmatrix",
                    'output' => "Минимальные шаги или -1",
                    'tests' => [
                        ['in' => $n . ' ' . $m . "\n" . implode("\n", $rows), 'out' => $out],
                        ['in' => "2 2\n0 1\n1 0", 'out' => "-1"],
                    ],
                ];
            }
        ],
        [
            'title' => 'Длина LIS',
            'difficulty' => 'hard',
            'make' => function ($k) {
                $arr = [$k, 2, 8, 6, 3, 6, 9];
                $n = count($arr);
                $dp = array_fill(0, $n, 1);
                $best = 1;
                for ($i = 0; $i < $n; $i++) {
                    for ($j = 0; $j < $i; $j++) {
                        if ($arr[$j] < $arr[$i]) {
                            $dp[$i] = max($dp[$i], $dp[$j] + 1);
                        }
                    }
                    $best = max($best, $dp[$i]);
                }
                return [
                    'statement' => 'Дан массив. Найдите длину наибольшей возрастающей подпоследовательности.',
                    'input' => "N\narray",
                    'output' => "Длина LIS",
                    'tests' => [
                        ['in' => $n . "\n" . implode(' ', $arr), 'out' => (string) $best],
                        ['in' => "5\n5 4 3 2 1", 'out' => "1"],
                    ],
                ];
            }
        ],
        [
            'title' => 'Рюкзак 0/1',
            'difficulty' => 'hard',
            'make' => function ($k) {
                $weights = [2, 3, 4, 5];
                $values = [3 + ($k % 3), 4, 5, 6];
                $w = 8 + ($k % 3);
                $n = count($weights);
                $dp = array_fill(0, $w + 1, 0);
                for ($i = 0; $i < $n; $i++) {
                    for ($cap = $w; $cap >= $weights[$i]; $cap--) {
                        $dp[$cap] = max($dp[$cap], $dp[$cap - $weights[$i]] + $values[$i]);
                    }
                }
                return [
                    'statement' => 'Есть N предметов с весами и ценностями. Найдите максимальную суммарную ценность при ограничении по весу W (0/1 рюкзак).',
                    'input' => "N W\nweights\nvalues",
                    'output' => "Максимальная ценность",
                    'tests' => [
                        ['in' => $n . ' ' . $w . "\n" . implode(' ', $weights) . "\n" . implode(' ', $values), 'out' => (string) $dp[$w]],
                        ['in' => "3 4\n2 2 3\n3 4 5", 'out' => "7"],
                    ],
                ];
            }
        ],
    ];

    $contestTaskPool = [];
    $contestTaskIndex = 0;
    for ($round = 1; $round <= 20; $round++) {
        foreach ($algoTemplates as $tpl) {
            $make = $tpl['make'];
            $payload = $make($round);
            $uniqueTitle = $makeUniqueSuffix($contestTaskIndex);
            $contestTaskPool[] = [
                'title' => $tpl['title'] . ' — ' . $uniqueTitle,
                'difficulty' => $tpl['difficulty'],
                'statement' => $payload['statement'] . ' ' . $makeUniqueContext($contestTaskIndex),
                'input' => $payload['input'],
                'output' => $payload['output'],
                'tests' => $payload['tests'],
            ];
            $contestTaskIndex++;
        }
    }

    for ($i = 1; $i <= 20; $i++) {
        $contestTitle = sprintf('Контест %02d', $i);
        $stmt = $pdo->prepare("SELECT id FROM contests WHERE title = ? LIMIT 1");
        $stmt->execute([$contestTitle]);
        $contestId = (int) ($stmt->fetchColumn() ?: 0);
        $createdContest = false;

        if ($contestId <= 0) {
            $stmt = $pdo->prepare("INSERT INTO contests (title, slug, description, is_active, created_at) VALUES (?, ?, ?, 1, NOW())");
            $slug = 'contest-' . $i;
            $stmt->execute([$contestTitle, $slug, "Практический контест №{$i} из 10 задач."]);
            $contestId = (int) $pdo->lastInsertId();
            $createdContest = true;
        }

        $stmt = $pdo->prepare("DELETE FROM contest_tasks WHERE contest_id = ?");
        $stmt->execute([$contestId]);

        $order = 1;
        $taskTarget = 10;
        $offset = ($i - 1) * $taskTarget;
        $tasksForContest = array_slice($contestTaskPool, $offset, $taskTarget);
        foreach ($tasksForContest as $task) {
            $stmt = $pdo->prepare("
                INSERT INTO contest_tasks (contest_id, title, difficulty, statement, input_spec, output_spec, starter_cpp, starter_python, tests_json, order_num, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $contestId,
                $task['title'],
                $task['difficulty'],
                $task['statement'],
                $task['input'],
                $task['output'],
                "#include <bits/stdc++.h>\nusing namespace std;\nint main(){\n    // TODO\n    return 0;\n}\n",
                "def solve():\n    # TODO\n    pass\n\nif __name__ == '__main__':\n    solve()\n",
                tfSafeJson($task['tests'], JSON_UNESCAPED_UNICODE),
                $order++
            ]);
        }

        $created[] = [
            'ok' => true,
            'created' => $createdContest,
            'type' => 'contest',
            'id' => $contestId,
            'title' => $contestTitle,
            'tasks_total' => count($tasksForContest)
        ];
    }

    return $created;
}
function tfGetInterviewProblemsData(): array
{
    $titleAdjectives = [
        'Базовый',
        'Практический',
        'Спринт',
        'Фокус',
        'Легкий',
        'Сильный',
        'Быстрый',
        'Четкий',
        'Стартовый',
        'Рабочий',
        'Упорный',
        'Гибкий',
        'Чистый',
        'Сбалансированный',
        'Плотный',
        'Собранный',
        'Динамичный',
        'Продвинутый',
        'Уверенный',
        'Системный'
    ];
    $titleThemes = [
        'разогрев',
        'ритм',
        'раунд',
        'набор',
        'модуль',
        'тренинг',
        'пакет',
        'заход',
        'интенсив',
        'практикум'
    ];
    $makeUniqueSuffix = static function (int $index) use ($titleAdjectives, $titleThemes): string {
        $adjCount = count($titleAdjectives);
        $themeCount = count($titleThemes);
        $adj = $titleAdjectives[$index % $adjCount];
        $theme = $titleThemes[intdiv($index, $adjCount) % $themeCount];
        return $adj . ' ' . $theme;
    };
    $makeUniqueContext = static function (int $index) use ($makeUniqueSuffix): string {
        return 'Контекст: ' . $makeUniqueSuffix($index) . '.';
    };
    $templates = [
        [
            'title' => 'Сумма двух чисел',
            'difficulty' => 'Easy',
            'category' => 'Math',
            'statement' => 'Даны два целых числа A и B. Выведите их сумму.',
            'input' => "A B",
            'output' => "A+B",
            'tests' => [
                ['in' => "2 3", 'out' => "5"],
                ['in' => "-4 9", 'out' => "5"],
            ],
        ],
        [
            'title' => 'Палиндром строки',
            'difficulty' => 'Easy',
            'category' => 'String',
            'statement' => 'Дана строка S. Выведите YES, если она палиндром, иначе NO.',
            'input' => "S",
            'output' => "YES/NO",
            'tests' => [
                ['in' => "level", 'out' => "YES"],
                ['in' => "abca", 'out' => "NO"],
            ],
        ],
        [
            'title' => 'Максимум массива',
            'difficulty' => 'Easy',
            'category' => 'Array',
            'statement' => 'Дано N и массив из N чисел. Найдите максимум.',
            'input' => "N\narray",
            'output' => "max",
            'tests' => [
                ['in' => "5\n1 7 3 2 0", 'out' => "7"],
                ['in' => "4\n-1 -5 -2 -3", 'out' => "-1"],
            ],
        ],
        [
            'title' => 'Количество чётных',
            'difficulty' => 'Easy',
            'category' => 'Array',
            'statement' => 'Дано N чисел. Выведите количество чётных.',
            'input' => "N\narray",
            'output' => "count",
            'tests' => [
                ['in' => "5\n1 2 3 4 5", 'out' => "2"],
                ['in' => "3\n2 4 6", 'out' => "3"],
            ],
        ],
        [
            'title' => 'Реверс строки',
            'difficulty' => 'Easy',
            'category' => 'String',
            'statement' => 'Дана строка. Выведите её в обратном порядке.',
            'input' => "S",
            'output' => "reverse",
            'tests' => [
                ['in' => "hello", 'out' => "olleh"],
                ['in' => "a", 'out' => "a"],
            ],
        ],
        [
            'title' => 'НОД двух чисел',
            'difficulty' => 'Medium',
            'category' => 'Math',
            'statement' => 'Даны a и b. Выведите gcd(a,b).',
            'input' => "a b",
            'output' => "gcd",
            'tests' => [
                ['in' => "12 18", 'out' => "6"],
                ['in' => "7 13", 'out' => "1"],
            ],
        ],
        [
            'title' => 'Сумма 1..n',
            'difficulty' => 'Easy',
            'category' => 'Math',
            'statement' => 'Дано n. Выведите сумму 1..n.',
            'input' => "n",
            'output' => "sum",
            'tests' => [
                ['in' => "10", 'out' => "55"],
                ['in' => "1", 'out' => "1"],
            ],
        ],
        [
            'title' => 'Количество уникальных',
            'difficulty' => 'Medium',
            'category' => 'Array',
            'statement' => 'Дано N и массив. Выведите количество уникальных элементов.',
            'input' => "N\narray",
            'output' => "count",
            'tests' => [
                ['in' => "5\n1 2 2 3 3", 'out' => "3"],
                ['in' => "4\n5 5 5 5", 'out' => "1"],
            ],
        ],
        [
            'title' => 'Среднее значение',
            'difficulty' => 'Medium',
            'category' => 'Math',
            'statement' => 'Дано N чисел. Выведите среднее с точностью до 2 знаков.',
            'input' => "N\narray",
            'output' => "avg",
            'tests' => [
                ['in' => "4\n1 2 3 4", 'out' => "2.50"],
                ['in' => "2\n10 0", 'out' => "5.00"],
            ],
        ],
        [
            'title' => 'Фибоначчи',
            'difficulty' => 'Hard',
            'category' => 'DP',
            'statement' => 'Дано n. Выведите n-е число Фибоначчи.',
            'input' => "n",
            'output' => "F(n)",
            'tests' => [
                ['in' => "7", 'out' => "13"],
                ['in' => "0", 'out' => "0"],
            ],
        ],
    ];

    $problems = [];
    $id = 1;
    $problemIndex = 0;
    for ($round = 1; $round <= 20; $round++) {
        foreach ($templates as $tpl) {
            $uniqueTitle = $makeUniqueSuffix($problemIndex);
            $problems[] = [
                'id' => $id++,
                'title' => $tpl['title'] . ' — ' . $uniqueTitle,
                'difficulty' => $tpl['difficulty'],
                'category' => $tpl['category'],
                'acceptance' => '—',
                'companies' => ['Amazon', 'Google', 'Meta'],
                'statement' => $tpl['statement'] . ' ' . $makeUniqueContext($problemIndex),
                'input' => $tpl['input'],
                'output' => $tpl['output'],
                'starter_cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    // your code\n    return 0;\n}\n",
                'starter_python' => "def solve():\n    # your code\n    pass\n\nif __name__ == '__main__':\n    solve()\n",
                'tests' => $tpl['tests'],
            ];
            $problemIndex++;
        }
    }
    return $problems;
}

function tfSeedPackStarterCode(string $language): string
{
    switch ($language) {
        case 'cpp':
            return "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n    // your code\n    return 0;\n}\n";
        case 'python':
            return "def solve():\n    # your code\n    pass\n\nif __name__ == '__main__':\n    solve()\n";
        case 'c':
            return "#include <stdio.h>\n\nint main(void) {\n    // your code\n    return 0;\n}\n";
        case 'csharp':
            return "using System;\n\npublic class Program {\n    public static void Main() {\n        // your code\n    }\n}\n";
        case 'java':
            return "import java.io.*;\nimport java.util.*;\n\npublic class Main {\n    public static void main(String[] args) throws Exception {\n        // your code\n    }\n}\n";
        default:
            return '';
    }
}

function tfSeedPackCompaniesByCategory(string $category): array
{
    $map = [
        'Array' => ['Amazon', 'Meta', 'Google'],
        'Hash Table' => ['Amazon', 'Uber', 'Yandex'],
        'Sliding Window' => ['TikTok', 'Meta', 'Booking'],
        'Stack' => ['Microsoft', 'Adobe', 'Bloomberg'],
        'Intervals' => ['Meta', 'Amazon', 'Stripe'],
        'Binary Search' => ['Google', 'Apple', 'Databricks'],
        'Graph' => ['Google', 'Meta', 'Microsoft'],
        'Shortest Path' => ['Uber', 'Google', 'Bolt'],
        'Dynamic Programming' => ['Amazon', 'Google', 'ByteDance'],
        'Greedy' => ['Meta', 'Netflix', 'Atlassian'],
    ];
    return $map[$category] ?? ['Amazon', 'Google', 'Meta'];
}

function tfSeedPackRealProblemsCatalog(): array
{
    static $catalog = null;
    if (is_array($catalog)) {
        return $catalog;
    }

    $catalog = [
        [
            'title' => 'Two Sum',
            'difficulty' => 'easy',
            'category' => 'Array',
            'statement' => 'Дан массив nums и число target. Выведите индексы двух элементов (0-based), сумма которых равна target.',
            'input' => "n target\nnums[0] nums[1] ... nums[n-1]",
            'output' => 'i j',
            'tests' => [
                ['in' => "4 9\n2 7 11 15", 'out' => '0 1'],
                ['in' => "5 6\n3 2 4 1 5", 'out' => '1 2'],
            ],
        ],
        [
            'title' => 'Valid Parentheses',
            'difficulty' => 'easy',
            'category' => 'Stack',
            'statement' => 'Дана строка из символов ()[]{}. Выведите YES, если скобочная последовательность корректна, иначе NO.',
            'input' => 's',
            'output' => 'YES или NO',
            'tests' => [
                ['in' => '()[]{}', 'out' => 'YES'],
                ['in' => '([)]', 'out' => 'NO'],
            ],
        ],
        [
            'title' => 'Merge Intervals',
            'difficulty' => 'medium',
            'category' => 'Intervals',
            'statement' => 'Даны интервалы [l, r]. Слейте пересекающиеся и выведите итоговые интервалы по строкам.',
            'input' => "n\nl1 r1\n...\nln rn",
            'output' => "merged intervals (one per line)",
            'tests' => [
                ['in' => "4\n1 3\n2 6\n8 10\n15 18", 'out' => "1 6\n8 10\n15 18"],
                ['in' => "5\n1 4\n4 5\n7 8\n6 7\n10 12", 'out' => "1 5\n6 8\n10 12"],
            ],
        ],
        [
            'title' => 'Longest Substring Without Repeating Characters',
            'difficulty' => 'medium',
            'category' => 'Sliding Window',
            'statement' => 'Дана строка s. Выведите длину самой длинной подстроки без повторяющихся символов.',
            'input' => 's',
            'output' => 'length',
            'tests' => [
                ['in' => 'abcabcbb', 'out' => '3'],
                ['in' => 'pwwkew', 'out' => '3'],
            ],
        ],
        [
            'title' => 'Product of Array Except Self',
            'difficulty' => 'medium',
            'category' => 'Array',
            'statement' => 'Для каждого i выведите произведение всех элементов массива, кроме nums[i]. Деление использовать нельзя.',
            'input' => "n\nnums",
            'output' => 'n integers',
            'tests' => [
                ['in' => "4\n1 2 3 4", 'out' => '24 12 8 6'],
                ['in' => "5\n-1 1 0 -3 3", 'out' => '0 0 9 0 0'],
            ],
        ],
        [
            'title' => 'Top K Frequent Elements',
            'difficulty' => 'medium',
            'category' => 'Hash Table',
            'statement' => 'Выведите k самых частых элементов. При равной частоте сортируйте по значению по возрастанию.',
            'input' => "n k\nnums",
            'output' => 'k integers',
            'tests' => [
                ['in' => "6 2\n1 1 1 2 2 3", 'out' => '1 2'],
                ['in' => "8 3\n4 4 4 6 6 1 1 2", 'out' => '4 1 6'],
            ],
        ],
        [
            'title' => 'Sliding Window Maximum',
            'difficulty' => 'hard',
            'category' => 'Sliding Window',
            'statement' => 'Даны массив и окно длины k. Выведите максимум в каждом окне.',
            'input' => "n k\nnums",
            'output' => 'window maximums',
            'tests' => [
                ['in' => "8 3\n1 3 -1 -3 5 3 6 7", 'out' => '3 3 5 5 6 7'],
                ['in' => "5 2\n9 8 7 6 5", 'out' => '9 8 7 6'],
            ],
        ],
        [
            'title' => 'Daily Temperatures',
            'difficulty' => 'medium',
            'category' => 'Stack',
            'statement' => 'Для каждого дня выведите через сколько дней будет более высокая температура. Если не будет, выведите 0.',
            'input' => "n\ntemperatures",
            'output' => 'n integers',
            'tests' => [
                ['in' => "8\n73 74 75 71 69 72 76 73", 'out' => '1 1 4 2 1 1 0 0'],
                ['in' => "3\n30 40 50", 'out' => '1 1 0'],
            ],
        ],
        [
            'title' => 'Binary Search',
            'difficulty' => 'easy',
            'category' => 'Binary Search',
            'statement' => 'Дан отсортированный массив и target. Выведите индекс target или -1.',
            'input' => "n target\nsorted nums",
            'output' => 'index or -1',
            'tests' => [
                ['in' => "6 9\n-1 0 3 5 9 12", 'out' => '4'],
                ['in' => "5 2\n1 3 5 7 9", 'out' => '-1'],
            ],
        ],
        [
            'title' => 'Search in Rotated Sorted Array',
            'difficulty' => 'medium',
            'category' => 'Binary Search',
            'statement' => 'Дан отсортированный по возрастанию массив, циклически сдвинутый на неизвестный шаг. Выведите индекс target или -1.',
            'input' => "n target\nnums",
            'output' => 'index or -1',
            'tests' => [
                ['in' => "7 0\n4 5 6 7 0 1 2", 'out' => '4'],
                ['in' => "7 3\n4 5 6 7 0 1 2", 'out' => '-1'],
            ],
        ],
        [
            'title' => 'Find Minimum in Rotated Sorted Array',
            'difficulty' => 'medium',
            'category' => 'Binary Search',
            'statement' => 'Дан циклически сдвинутый отсортированный массив без дубликатов. Выведите минимальный элемент.',
            'input' => "n\nnums",
            'output' => 'minimum value',
            'tests' => [
                ['in' => "5\n3 4 5 1 2", 'out' => '1'],
                ['in' => "6\n11 13 15 17 2 5", 'out' => '2'],
            ],
        ],
        [
            'title' => 'Kth Largest Element in an Array',
            'difficulty' => 'medium',
            'category' => 'Array',
            'statement' => 'Даны массив и число k. Выведите k-й по величине элемент.',
            'input' => "n k\nnums",
            'output' => 'value',
            'tests' => [
                ['in' => "6 2\n3 2 1 5 6 4", 'out' => '5'],
                ['in' => "5 1\n-1 -2 -3 -4 -5", 'out' => '-1'],
            ],
        ],
        [
            'title' => 'Number of Islands',
            'difficulty' => 'medium',
            'category' => 'Graph',
            'statement' => 'Дана карта из 0 и 1. Подсчитайте количество островов (4-связность).',
            'input' => "n m\nrow1\n...\nrown",
            'output' => 'count',
            'tests' => [
                ['in' => "4 5\n11000\n11000\n00100\n00011", 'out' => '3'],
                ['in' => "3 3\n111\n111\n111", 'out' => '1'],
            ],
        ],
        [
            'title' => 'Connected Components in Undirected Graph',
            'difficulty' => 'medium',
            'category' => 'Graph',
            'statement' => 'Дан неориентированный граф. Выведите количество компонент связности.',
            'input' => "n m\nu1 v1\n...\num vm",
            'output' => 'components',
            'tests' => [
                ['in' => "5 3\n1 2\n2 3\n4 5", 'out' => '2'],
                ['in' => "4 0", 'out' => '4'],
            ],
        ],
        [
            'title' => 'Course Schedule',
            'difficulty' => 'medium',
            'category' => 'Graph',
            'statement' => 'Дан ориентированный граф зависимостей курсов. Выведите YES, если можно завершить все курсы (граф ацикличен), иначе NO.',
            'input' => "n m\nu1 v1\n...\num vm",
            'output' => 'YES or NO',
            'tests' => [
                ['in' => "4 3\n0 1\n1 2\n2 3", 'out' => 'YES'],
                ['in' => "2 2\n0 1\n1 0", 'out' => 'NO'],
            ],
        ],
        [
            'title' => 'Dijkstra Shortest Path',
            'difficulty' => 'hard',
            'category' => 'Shortest Path',
            'statement' => 'Для ориентированного графа с неотрицательными весами найдите кратчайшее расстояние от s до t.',
            'input' => "n m s t\nu v w (m lines)",
            'output' => 'distance or -1',
            'tests' => [
                ['in' => "5 6 1 5\n1 2 2\n1 3 5\n2 3 1\n2 4 2\n3 5 5\n4 5 1", 'out' => '5'],
                ['in' => "4 2 1 4\n1 2 3\n2 3 2", 'out' => '-1'],
            ],
        ],
        [
            'title' => 'Shortest Path in Grid',
            'difficulty' => 'medium',
            'category' => 'Graph',
            'statement' => 'Дана матрица 0/1 (0 - проход, 1 - стена). Найдите минимальное число шагов от (0,0) до (n-1,m-1), или -1.',
            'input' => "n m\nrow1\n...\nrown",
            'output' => 'minimum steps or -1',
            'tests' => [
                ['in' => "3 3\n000\n110\n000", 'out' => '4'],
                ['in' => "2 2\n01\n10", 'out' => '-1'],
            ],
        ],
        [
            'title' => 'Topological Sort (Lexicographically Smallest)',
            'difficulty' => 'hard',
            'category' => 'Graph',
            'statement' => 'Постройте топологический порядок. При нескольких вариантах выводите лексикографически минимальный. Если есть цикл, выведите IMPOSSIBLE.',
            'input' => "n m\nu1 v1\n...\num vm",
            'output' => 'order or IMPOSSIBLE',
            'tests' => [
                ['in' => "4 3\n1 2\n1 3\n3 4", 'out' => '1 2 3 4'],
                ['in' => "3 3\n1 2\n2 3\n3 1", 'out' => 'IMPOSSIBLE'],
            ],
        ],
        [
            'title' => 'Coin Change',
            'difficulty' => 'medium',
            'category' => 'Dynamic Programming',
            'statement' => 'Даны номиналы монет и сумма amount. Выведите минимальное количество монет для набора amount, либо -1.',
            'input' => "n amount\ncoins",
            'output' => 'minimum count or -1',
            'tests' => [
                ['in' => "3 11\n1 2 5", 'out' => '3'],
                ['in' => "2 3\n2 4", 'out' => '-1'],
            ],
        ],
        [
            'title' => 'House Robber',
            'difficulty' => 'medium',
            'category' => 'Dynamic Programming',
            'statement' => 'Даны суммы денег в домах вдоль улицы. Нельзя брать два соседних дома. Выведите максимум.',
            'input' => "n\nnums",
            'output' => 'max sum',
            'tests' => [
                ['in' => "4\n1 2 3 1", 'out' => '4'],
                ['in' => "5\n2 7 9 3 1", 'out' => '12'],
            ],
        ],
        [
            'title' => 'Longest Increasing Subsequence',
            'difficulty' => 'medium',
            'category' => 'Dynamic Programming',
            'statement' => 'Дан массив. Выведите длину наибольшей строго возрастающей подпоследовательности.',
            'input' => "n\nnums",
            'output' => 'length',
            'tests' => [
                ['in' => "8\n10 9 2 5 3 7 101 18", 'out' => '4'],
                ['in' => "5\n5 4 3 2 1", 'out' => '1'],
            ],
        ],
        [
            'title' => 'Partition Equal Subset Sum',
            'difficulty' => 'medium',
            'category' => 'Dynamic Programming',
            'statement' => 'Проверьте, можно ли разбить массив на два подмножества с одинаковой суммой.',
            'input' => "n\nnums",
            'output' => 'YES or NO',
            'tests' => [
                ['in' => "4\n1 5 11 5", 'out' => 'YES'],
                ['in' => "4\n1 2 3 5", 'out' => 'NO'],
            ],
        ],
        [
            'title' => 'Decode Ways',
            'difficulty' => 'medium',
            'category' => 'Dynamic Programming',
            'statement' => 'Дана строка цифр. Подсчитайте количество способов декодирования (1->A ... 26->Z).',
            'input' => 's',
            'output' => 'count',
            'tests' => [
                ['in' => '12', 'out' => '2'],
                ['in' => '226', 'out' => '3'],
            ],
        ],
        [
            'title' => 'Maximum Subarray',
            'difficulty' => 'easy',
            'category' => 'Dynamic Programming',
            'statement' => 'Найдите максимальную сумму непрерывного подмассива.',
            'input' => "n\nnums",
            'output' => 'max sum',
            'tests' => [
                ['in' => "9\n-2 1 -3 4 -1 2 1 -5 4", 'out' => '6'],
                ['in' => "1\n-5", 'out' => '-5'],
            ],
        ],
        [
            'title' => 'Edit Distance',
            'difficulty' => 'hard',
            'category' => 'Dynamic Programming',
            'statement' => 'Даны две строки. Найдите расстояние Левенштейна между ними.',
            'input' => "a\nb",
            'output' => 'distance',
            'tests' => [
                ['in' => "horse\nros", 'out' => '3'],
                ['in' => "intention\nexecution", 'out' => '5'],
            ],
        ],
        [
            'title' => 'Longest Common Subsequence',
            'difficulty' => 'medium',
            'category' => 'Dynamic Programming',
            'statement' => 'Даны две строки. Выведите длину их наибольшей общей подпоследовательности.',
            'input' => "a\nb",
            'output' => 'length',
            'tests' => [
                ['in' => "abcde\nace", 'out' => '3'],
                ['in' => "abc\ndef", 'out' => '0'],
            ],
        ],
        [
            'title' => '0/1 Knapsack',
            'difficulty' => 'hard',
            'category' => 'Dynamic Programming',
            'statement' => 'Есть n предметов с весами и ценностями и рюкзак емкости W. Выведите максимальную ценность.',
            'input' => "n W\nweights\nvalues",
            'output' => 'max value',
            'tests' => [
                ['in' => "4 7\n1 3 4 5\n1 4 5 7", 'out' => '9'],
                ['in' => "3 4\n2 2 3\n3 4 5", 'out' => '7'],
            ],
        ],
        [
            'title' => 'Unique Paths with Obstacles',
            'difficulty' => 'medium',
            'category' => 'Dynamic Programming',
            'statement' => 'Подсчитайте число путей от верхнего левого до правого нижнего угла в сетке, если 1 означает препятствие.',
            'input' => "n m\nrow1\n...\nrown",
            'output' => 'count',
            'tests' => [
                ['in' => "3 3\n000\n010\n000", 'out' => '2'],
                ['in' => "2 2\n11\n00", 'out' => '0'],
            ],
        ],
        [
            'title' => 'Trapping Rain Water',
            'difficulty' => 'hard',
            'category' => 'Greedy',
            'statement' => 'Дана карта высот. Выведите общий объем удержанной воды.',
            'input' => "n\nheights",
            'output' => 'water amount',
            'tests' => [
                ['in' => "12\n0 1 0 2 1 0 1 3 2 1 2 1", 'out' => '6'],
                ['in' => "6\n4 2 0 3 2 5", 'out' => '9'],
            ],
        ],
        [
            'title' => 'Evaluate Reverse Polish Notation',
            'difficulty' => 'medium',
            'category' => 'Stack',
            'statement' => 'Вычислите выражение в обратной польской нотации. Деление целочисленное с усечением к нулю.',
            'input' => "n\ntokens",
            'output' => 'value',
            'tests' => [
                ['in' => "5\n2 1 + 3 *", 'out' => '9'],
                ['in' => "5\n4 13 5 / +", 'out' => '6'],
            ],
        ],
        [
            'title' => 'Min Stack Operations',
            'difficulty' => 'medium',
            'category' => 'Stack',
            'statement' => "Реализуйте стек с операциями PUSH x, POP и MIN. Для каждой MIN выведите минимальный элемент, либо EMPTY.",
            'input' => "q\ncommands",
            'output' => 'one line for each MIN',
            'tests' => [
                ['in' => "8\nPUSH -2\nPUSH 0\nPUSH -3\nMIN\nPOP\nMIN\nPOP\nMIN", 'out' => "-3\n-2\n-2"],
                ['in' => "3\nMIN\nPUSH 1\nMIN", 'out' => "EMPTY\n1"],
            ],
        ],
        [
            'title' => 'Car Fleet',
            'difficulty' => 'medium',
            'category' => 'Greedy',
            'statement' => 'Есть target, позиции машин и скорости. Подсчитайте число флотов, доезжающих до target.',
            'input' => "target n\npositions\nspeeds",
            'output' => 'fleets',
            'tests' => [
                ['in' => "12 5\n10 8 0 5 3\n2 4 1 1 3", 'out' => '3'],
                ['in' => "10 3\n6 4 2\n1 1 1", 'out' => '3'],
            ],
        ],
    ];

    return $catalog;
}

function tfGetInterviewProblemsDataRich(): array
{
    $definitions = tfSeedPackRealProblemsCatalog();
    $problems = [];
    $id = 1;
    $acceptanceBase = [
        'easy' => 63.6,
        'medium' => 48.4,
        'hard' => 34.8,
    ];

    foreach ($definitions as $index => $item) {
        $difficultyRaw = strtolower((string) ($item['difficulty'] ?? 'easy'));
        $difficulty = $difficultyRaw === 'hard' ? 'Hard' : ($difficultyRaw === 'medium' ? 'Medium' : 'Easy');
        $base = $acceptanceBase[$difficultyRaw] ?? 45.0;
        $delta = (($index % 7) - 3) * 0.7;
        $acceptance = number_format(max(24.0, min(79.0, $base + $delta)), 1) . '%';
        $category = (string) ($item['category'] ?? 'General');

        $problems[] = [
            'id' => $id++,
            'title' => (string) ($item['title'] ?? ''),
            'difficulty' => $difficulty,
            'category' => $category,
            'acceptance' => $acceptance,
            'companies' => tfSeedPackCompaniesByCategory($category),
            'statement' => (string) ($item['statement'] ?? ''),
            'input' => (string) ($item['input'] ?? ''),
            'output' => (string) ($item['output'] ?? ''),
            'starter_cpp' => tfSeedPackStarterCode('cpp'),
            'starter_python' => tfSeedPackStarterCode('python'),
            'starter_c' => tfSeedPackStarterCode('c'),
            'starter_csharp' => tfSeedPackStarterCode('csharp'),
            'starter_java' => tfSeedPackStarterCode('java'),
            'tests' => (array) ($item['tests'] ?? []),
        ];
    }

    return $problems;
}

function tfGetContestTasksCatalogRich(): array
{
    $definitions = tfSeedPackAbramyanProblemsCatalog();
    $tasks = [];
    foreach ($definitions as $item) {
        $difficulty = strtolower((string) ($item['difficulty'] ?? 'easy'));
        if (!in_array($difficulty, ['easy', 'medium', 'hard'], true)) {
            $difficulty = 'easy';
        }
        $tasks[] = [
            'title' => (string) ($item['title'] ?? ''),
            'difficulty' => $difficulty,
            'statement' => (string) ($item['statement'] ?? ''),
            'input' => (string) ($item['input'] ?? ''),
            'output' => (string) ($item['output'] ?? ''),
            'tests' => (array) ($item['tests'] ?? []),
        ];
    }
    return $tasks;
}

function tfSeedPackAbramyanProblemsCatalog(): array
{
    return [
        [
            'title' => 'Абрамян Begin1. Периметр квадрата',
            'difficulty' => 'easy',
            'statement' => 'Дана сторона квадрата a. Найдите его периметр P = 4*a.',
            'input' => 'a',
            'output' => 'P',
            'tests' => [['in' => '3', 'out' => '12'], ['in' => '11', 'out' => '44']],
        ],
        [
            'title' => 'Абрамян Begin2. Площадь круга',
            'difficulty' => 'easy',
            'statement' => 'Дан радиус круга r. Найдите площадь S = pi*r^2. Используйте pi = 3.141593, вывод с 6 знаками после точки.',
            'input' => 'r',
            'output' => 'S (6 знаков после точки)',
            'tests' => [['in' => '1', 'out' => '3.141593'], ['in' => '2', 'out' => '12.566372']],
        ],
        [
            'title' => 'Абрамян Begin3. Прямоугольник',
            'difficulty' => 'easy',
            'statement' => 'Даны стороны прямоугольника a и b. Найдите площадь S и периметр P.',
            'input' => 'a b',
            'output' => 'S P',
            'tests' => [['in' => '3 4', 'out' => '12 14'], ['in' => '5 7', 'out' => '35 24']],
        ],
        [
            'title' => 'Абрамян Begin4. Длина окружности',
            'difficulty' => 'easy',
            'statement' => 'Дан диаметр d. Найдите длину окружности L = pi*d. Используйте pi = 3.141593 и 6 знаков после точки.',
            'input' => 'd',
            'output' => 'L (6 знаков после точки)',
            'tests' => [['in' => '1', 'out' => '3.141593'], ['in' => '10', 'out' => '31.415930']],
        ],
        [
            'title' => 'Абрамян Begin5. Куб',
            'difficulty' => 'easy',
            'statement' => 'Дана длина ребра куба a. Найдите объем V = a^3 и площадь поверхности S = 6*a^2.',
            'input' => 'a',
            'output' => 'V S',
            'tests' => [['in' => '2', 'out' => '8 24'], ['in' => '5', 'out' => '125 150']],
        ],
        [
            'title' => 'Абрамян Begin6. Параллелепипед',
            'difficulty' => 'easy',
            'statement' => 'Даны измерения прямоугольного параллелепипеда a, b, c. Найдите объем V и площадь поверхности S.',
            'input' => 'a b c',
            'output' => 'V S',
            'tests' => [['in' => '2 3 4', 'out' => '24 52'], ['in' => '1 5 10', 'out' => '50 130']],
        ],
        [
            'title' => 'Абрамян Integer1. Метры из сантиметров',
            'difficulty' => 'easy',
            'statement' => 'Дано расстояние в сантиметрах. Найдите количество полных метров.',
            'input' => 'cm',
            'output' => 'm',
            'tests' => [['in' => '345', 'out' => '3'], ['in' => '99', 'out' => '0']],
        ],
        [
            'title' => 'Абрамян Integer2. Тонны из килограммов',
            'difficulty' => 'easy',
            'statement' => 'Дана масса в килограммах. Найдите количество полных тонн.',
            'input' => 'kg',
            'output' => 't',
            'tests' => [['in' => '2500', 'out' => '2'], ['in' => '999', 'out' => '0']],
        ],
        [
            'title' => 'Абрамян Integer3. Килобайты из байтов',
            'difficulty' => 'easy',
            'statement' => 'Дано количество байтов. Найдите количество полных килобайтов (1 KB = 1024 байта).',
            'input' => 'bytes',
            'output' => 'kb',
            'tests' => [['in' => '2048', 'out' => '2'], ['in' => '1536', 'out' => '1']],
        ],
        [
            'title' => 'Абрамян Integer4. Частное и остаток',
            'difficulty' => 'easy',
            'statement' => 'Даны целые A и B (B > 0). Найдите целую часть от деления A/B и остаток.',
            'input' => 'A B',
            'output' => 'Q R',
            'tests' => [['in' => '17 5', 'out' => '3 2'], ['in' => '100 9', 'out' => '11 1']],
        ],
        [
            'title' => 'Абрамян Boolean1. Условие A>2 и B<=3',
            'difficulty' => 'easy',
            'statement' => 'Проверить истинность высказывания: A > 2 и B <= 3. Вывести YES или NO.',
            'input' => 'A B',
            'output' => 'YES/NO',
            'tests' => [['in' => '5 3', 'out' => 'YES'], ['in' => '2 1', 'out' => 'NO']],
        ],
        [
            'title' => 'Абрамян Boolean2. Нечетное двузначное',
            'difficulty' => 'easy',
            'statement' => 'Проверить, является ли целое число N нечетным двузначным числом.',
            'input' => 'N',
            'output' => 'YES/NO',
            'tests' => [['in' => '35', 'out' => 'YES'], ['in' => '40', 'out' => 'NO']],
        ],
        [
            'title' => 'Абрамян Boolean3. Ровно одно положительное',
            'difficulty' => 'easy',
            'statement' => 'Даны три числа A, B, C. Проверить, что ровно одно из них положительное.',
            'input' => 'A B C',
            'output' => 'YES/NO',
            'tests' => [['in' => '1 -2 -3', 'out' => 'YES'], ['in' => '1 2 -5', 'out' => 'NO']],
        ],
        [
            'title' => 'Абрамян If1. Максимум из двух',
            'difficulty' => 'easy',
            'statement' => 'Даны два числа. Выведите большее.',
            'input' => 'A B',
            'output' => 'max',
            'tests' => [['in' => '7 3', 'out' => '7'], ['in' => '-4 1', 'out' => '1']],
        ],
        [
            'title' => 'Абрамян If2. Знак числа',
            'difficulty' => 'easy',
            'statement' => 'Дано число X. Выведите 1, если X > 0; 0, если X = 0; -1, если X < 0.',
            'input' => 'X',
            'output' => '-1/0/1',
            'tests' => [['in' => '0', 'out' => '0'], ['in' => '-10', 'out' => '-1']],
        ],
        [
            'title' => 'Абрамян If3. Среднее из трех',
            'difficulty' => 'medium',
            'statement' => 'Даны три различных числа. Выведите среднее (не минимальное и не максимальное).',
            'input' => 'A B C',
            'output' => 'median',
            'tests' => [['in' => '3 9 5', 'out' => '5'], ['in' => '10 -1 4', 'out' => '4']],
        ],
        [
            'title' => 'Абрамян Case1. День недели',
            'difficulty' => 'easy',
            'statement' => 'Дан номер дня недели N (1..7). Выведите название дня на русском.',
            'input' => 'N',
            'output' => 'Понедельник/.../Воскресенье',
            'tests' => [['in' => '1', 'out' => 'Понедельник'], ['in' => '7', 'out' => 'Воскресенье']],
        ],
        [
            'title' => 'Абрамян Case2. Дней в месяце',
            'difficulty' => 'easy',
            'statement' => 'Дан номер месяца M (1..12), невисокосный год. Выведите количество дней в месяце.',
            'input' => 'M',
            'output' => 'days',
            'tests' => [['in' => '2', 'out' => '28'], ['in' => '11', 'out' => '30']],
        ],
        [
            'title' => 'Абрамян For1. Сумма от A до B',
            'difficulty' => 'easy',
            'statement' => 'Даны целые A и B (A <= B). Найдите сумму всех целых чисел от A до B включительно.',
            'input' => 'A B',
            'output' => 'sum',
            'tests' => [['in' => '1 5', 'out' => '15'], ['in' => '-2 2', 'out' => '0']],
        ],
        [
            'title' => 'Абрамян For2. Факториал',
            'difficulty' => 'easy',
            'statement' => 'Дано целое N (0 <= N <= 12). Найдите N!.',
            'input' => 'N',
            'output' => 'N!',
            'tests' => [['in' => '0', 'out' => '1'], ['in' => '6', 'out' => '720']],
        ],
        [
            'title' => 'Абрамян While1. Количество цифр',
            'difficulty' => 'easy',
            'statement' => 'Дано целое положительное N. Найдите количество цифр в числе.',
            'input' => 'N',
            'output' => 'count',
            'tests' => [['in' => '7', 'out' => '1'], ['in' => '12345', 'out' => '5']],
        ],
        [
            'title' => 'Абрамян While2. Обратное число',
            'difficulty' => 'medium',
            'statement' => 'Дано целое положительное N. Выведите число, полученное записью цифр N в обратном порядке.',
            'input' => 'N',
            'output' => 'reversed',
            'tests' => [['in' => '12340', 'out' => '4321'], ['in' => '9005', 'out' => '5009']],
        ],
        [
            'title' => 'Абрамян Array1. Минимум и максимум',
            'difficulty' => 'easy',
            'statement' => 'Дан массив из N целых чисел. Выведите минимальный и максимальный элементы.',
            'input' => "N\na1 a2 ... aN",
            'output' => 'min max',
            'tests' => [['in' => "5\n3 7 -2 9 0", 'out' => '-2 9'], ['in' => "4\n4 4 4 4", 'out' => '4 4']],
        ],
        [
            'title' => 'Абрамян Array2. Количество положительных',
            'difficulty' => 'easy',
            'statement' => 'Дан массив из N целых чисел. Подсчитайте количество положительных элементов.',
            'input' => "N\na1 a2 ... aN",
            'output' => 'count',
            'tests' => [['in' => "6\n-1 2 0 3 -5 7", 'out' => '3'], ['in' => "3\n-2 -1 0", 'out' => '0']],
        ],
        [
            'title' => 'Абрамян Array3. Второй максимум',
            'difficulty' => 'medium',
            'statement' => 'Дан массив из N чисел. Найдите второй по величине различный элемент. Если его нет, выведите NO.',
            'input' => "N\na1 a2 ... aN",
            'output' => 'second_max или NO',
            'tests' => [['in' => "5\n1 7 3 7 2", 'out' => '3'], ['in' => "4\n5 5 5 5", 'out' => 'NO']],
        ],
        [
            'title' => 'Абрамян Matrix1. Суммы строк',
            'difficulty' => 'medium',
            'statement' => 'Дана матрица N x M. Для каждой строки выведите сумму ее элементов.',
            'input' => "N M\nmatrix",
            'output' => 'N строк (суммы)',
            'tests' => [['in' => "2 3\n1 2 3\n4 5 6", 'out' => "6\n15"], ['in' => "1 4\n7 0 -2 5", 'out' => '10']],
        ],
        [
            'title' => 'Абрамян Matrix2. Сумма главной диагонали',
            'difficulty' => 'medium',
            'statement' => 'Дана квадратная матрица N x N. Выведите сумму элементов главной диагонали.',
            'input' => "N\nmatrix",
            'output' => 'sum',
            'tests' => [['in' => "3\n1 2 3\n4 5 6\n7 8 9", 'out' => '15'], ['in' => "2\n-1 4\n2 3", 'out' => '2']],
        ],
        [
            'title' => 'Абрамян String1. Количество гласных',
            'difficulty' => 'easy',
            'statement' => 'Дана строка из латинских букв. Подсчитайте количество гласных (a, e, i, o, u, y), без учета регистра.',
            'input' => 'S',
            'output' => 'count',
            'tests' => [['in' => 'Abramyan', 'out' => '3'], ['in' => 'BCDF', 'out' => '0']],
        ],
        [
            'title' => 'Абрамян String2. Палиндром',
            'difficulty' => 'easy',
            'statement' => 'Дана строка S. Проверьте, является ли она палиндромом (без преобразований).',
            'input' => 'S',
            'output' => 'YES/NO',
            'tests' => [['in' => 'level', 'out' => 'YES'], ['in' => 'contest', 'out' => 'NO']],
        ],
        [
            'title' => 'Абрамян String3. Замена пробелов',
            'difficulty' => 'easy',
            'statement' => 'Дана строка. Замените каждый пробел символом "_" и выведите результат.',
            'input' => 'S',
            'output' => 'modified S',
            'tests' => [['in' => 'ab cd ef', 'out' => 'ab_cd_ef'], ['in' => 'one', 'out' => 'one']],
        ],
        [
            'title' => 'Абрамян String4. Количество слов',
            'difficulty' => 'medium',
            'statement' => 'Дана строка, слова разделены одним или несколькими пробелами. Выведите количество слов.',
            'input' => 'S',
            'output' => 'count',
            'tests' => [['in' => ' one  two   three ', 'out' => '3'], ['in' => 'single', 'out' => '1']],
        ],
        [
            'title' => 'Абрамян Proc1. НОД',
            'difficulty' => 'medium',
            'statement' => 'Даны A и B. Выведите НОД(A, B).',
            'input' => 'A B',
            'output' => 'gcd',
            'tests' => [['in' => '48 18', 'out' => '6'], ['in' => '17 13', 'out' => '1']],
        ],
        [
            'title' => 'Абрамян Proc2. НОК',
            'difficulty' => 'medium',
            'statement' => 'Даны A и B. Выведите НОК(A, B).',
            'input' => 'A B',
            'output' => 'lcm',
            'tests' => [['in' => '12 18', 'out' => '36'], ['in' => '7 5', 'out' => '35']],
        ],
        [
            'title' => 'Абрамян Minmax1. Индекс минимального',
            'difficulty' => 'medium',
            'statement' => 'Дан массив из N чисел. Выведите индекс (1-based) первого минимального элемента.',
            'input' => "N\na1 a2 ... aN",
            'output' => 'index',
            'tests' => [['in' => "5\n3 1 2 1 4", 'out' => '2'], ['in' => "4\n9 8 7 6", 'out' => '4']],
        ],
        [
            'title' => 'Абрамян Minmax2. Индекс максимального',
            'difficulty' => 'medium',
            'statement' => 'Дан массив из N чисел. Выведите индекс (1-based) первого максимального элемента.',
            'input' => "N\na1 a2 ... aN",
            'output' => 'index',
            'tests' => [['in' => "5\n3 9 2 9 4", 'out' => '2'], ['in' => "3\n-5 -1 -7", 'out' => '2']],
        ],
        [
            'title' => 'Абрамян Series1. Сумма N чисел',
            'difficulty' => 'easy',
            'statement' => 'Дано N, затем N чисел. Найдите их сумму.',
            'input' => "N\nx1 x2 ... xN",
            'output' => 'sum',
            'tests' => [['in' => "4\n1 2 3 4", 'out' => '10'], ['in' => "3\n-1 5 -2", 'out' => '2']],
        ],
        [
            'title' => 'Абрамян Series2. Среднее N чисел',
            'difficulty' => 'easy',
            'statement' => 'Дано N, затем N чисел. Найдите среднее арифметическое. Вывод с 6 знаками после точки.',
            'input' => "N\nx1 x2 ... xN",
            'output' => 'avg',
            'tests' => [['in' => "4\n1 2 3 4", 'out' => '2.500000'], ['in' => "2\n5 6", 'out' => '5.500000']],
        ],
    ];
}

function tfSeedContestsRich(PDO $pdo, bool $forceRebuild = true): array
{
    ensureContestsSchema($pdo);

    $contestSpecs = [
        ['title' => 'Контест 01: Абрамян Begin/Integer', 'description' => 'Базовые задачи Абрамяна: Begin и Integer.'],
        ['title' => 'Контест 02: Абрамян Boolean/If/Case', 'description' => 'Логика и ветвления: Boolean, If, Case.'],
        ['title' => 'Контест 03: Абрамян For/While', 'description' => 'Циклы и арифметические серии: For и While.'],
        ['title' => 'Контест 04: Абрамян Array', 'description' => 'Задачи на массивы и обработку последовательностей.'],
        ['title' => 'Контест 05: Абрамян Matrix', 'description' => 'Матрицы: суммы, диагонали и базовые обходы.'],
        ['title' => 'Контест 06: Абрамян String', 'description' => 'Строки: подсчеты, палиндромы, преобразования.'],
        ['title' => 'Контест 07: Абрамян Proc/Minmax', 'description' => 'Процедуры, НОД/НОК, задачи Minmax.'],
        ['title' => 'Контест 08: Абрамян Series', 'description' => 'Серии и комбинированная практика задачника Абрамяна.'],
    ];
    $taskCatalog = tfGetContestTasksCatalogRich();
    $contestCount = count($contestSpecs);
    if ($contestCount <= 0 || empty($taskCatalog)) {
        return [];
    }

    $totalTasks = count($taskCatalog);
    $basePerContest = intdiv($totalTasks, $contestCount);
    $remainder = $totalTasks % $contestCount;
    if ($basePerContest <= 0) {
        $basePerContest = 1;
    }

    $selectBySlug = $pdo->prepare("SELECT id FROM contests WHERE slug = ? LIMIT 1");
    $insertContest = $pdo->prepare("INSERT INTO contests (title, slug, description, is_active, created_at) VALUES (?, ?, ?, 1, NOW())");
    $updateContest = $pdo->prepare("UPDATE contests SET title = ?, description = ?, is_active = 1, updated_at = NOW() WHERE id = ?");
    $countTasks = $pdo->prepare("SELECT COUNT(*) FROM contest_tasks WHERE contest_id = ?");
    $selectTaskTitles = $pdo->prepare("SELECT title FROM contest_tasks WHERE contest_id = ? ORDER BY order_num ASC, id ASC");
    $deleteTasks = $pdo->prepare("DELETE FROM contest_tasks WHERE contest_id = ?");
    $deleteContestSubmissions = $pdo->prepare("DELETE FROM contest_submissions WHERE contest_id = ?");
    $deleteContestResults = $pdo->prepare("DELETE FROM contest_results WHERE contest_id = ?");
    $insertTask = $pdo->prepare("
        INSERT INTO contest_tasks (contest_id, title, difficulty, statement, input_spec, output_spec, starter_cpp, starter_python, tests_json, order_num, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $created = [];
    $cursor = 0;
    for ($i = 0; $i < $contestCount; $i++) {
        $slug = 'contest-' . ($i + 1);
        $spec = $contestSpecs[$i];
        $tasksForThisContest = $basePerContest + ($i < $remainder ? 1 : 0);
        $slice = array_slice($taskCatalog, $cursor, $tasksForThisContest);
        $cursor += $tasksForThisContest;
        if (empty($slice)) {
            continue;
        }

        $selectBySlug->execute([$slug]);
        $contestId = (int) ($selectBySlug->fetchColumn() ?: 0);
        $createdContest = false;
        if ($contestId <= 0) {
            $insertContest->execute([
                (string) $spec['title'],
                $slug,
                (string) $spec['description']
            ]);
            $contestId = (int) $pdo->lastInsertId();
            $createdContest = true;
        } else {
            $updateContest->execute([
                (string) $spec['title'],
                (string) $spec['description'],
                $contestId
            ]);
        }

        if ($contestId <= 0) {
            continue;
        }

        $countTasks->execute([$contestId]);
        $currentTasks = (int) ($countTasks->fetchColumn() ?: 0);
        $needsRebuild = $forceRebuild || $currentTasks === 0;
        if (!$needsRebuild) {
            if ($currentTasks !== count($slice)) {
                $needsRebuild = true;
            } else {
                $selectTaskTitles->execute([$contestId]);
                $currentTitles = array_map(
                    static fn($v) => (string) $v,
                    (array) $selectTaskTitles->fetchAll(PDO::FETCH_COLUMN)
                );
                $expectedTitles = array_map(
                    static fn(array $task): string => (string) ($task['title'] ?? ''),
                    $slice
                );
                if ($currentTitles !== $expectedTitles) {
                    $needsRebuild = true;
                }
            }
        }
        if ($needsRebuild) {
            $deleteContestSubmissions->execute([$contestId]);
            $deleteContestResults->execute([$contestId]);
            $deleteTasks->execute([$contestId]);
            $order = 1;
            foreach ($slice as $task) {
                $tests = tfSafeJson((array) ($task['tests'] ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if ($tests === false) {
                    $tests = '[]';
                }
                $insertTask->execute([
                    $contestId,
                    (string) ($task['title'] ?? ''),
                    (string) ($task['difficulty'] ?? 'easy'),
                    (string) ($task['statement'] ?? ''),
                    (string) ($task['input'] ?? ''),
                    (string) ($task['output'] ?? ''),
                    tfSeedPackStarterCode('cpp'),
                    tfSeedPackStarterCode('python'),
                    $tests,
                    $order++,
                ]);
            }
        }

        $created[] = [
            'ok' => true,
            'created' => $createdContest,
            'type' => 'contest',
            'id' => $contestId,
            'title' => (string) $spec['title'],
            'tasks_total' => count($slice),
        ];
    }

    if ($forceRebuild) {
        $stmt = $pdo->query("SELECT id, slug FROM contests WHERE slug LIKE 'contest-%'");
        $rows = $stmt ? $stmt->fetchAll() : [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $slug = (string) ($row['slug'] ?? '');
                $id = (int) ($row['id'] ?? 0);
                if ($id <= 0 || !preg_match('/^contest-(\d+)$/', $slug, $m)) {
                    continue;
                }
                $num = (int) ($m[1] ?? 0);
                if ($num > $contestCount) {
                    $disable = $pdo->prepare("UPDATE contests SET is_active = 0, updated_at = NOW() WHERE id = ?");
                    $disable->execute([$id]);
                }
            }
        }
    }

    return $created;
}

function tfSeedPackCourseSpecsRich(): array
{
    return [
        ['title' => 'HTML+CSS', 'track' => 'htmlcss', 'category' => 'frontend', 'skills' => ['HTML5', 'CSS3', 'Flexbox', 'Grid', 'Accessibility'], 'practice' => '', 'instructor' => 'Ирина Смирнова, Frontend Lead'],
        ['title' => 'JavaScript', 'track' => 'javascript', 'category' => 'frontend', 'skills' => ['JavaScript', 'DOM', 'Async/Await', 'Fetch API', 'Debugging'], 'practice' => 'js', 'instructor' => 'Александр Белов, Senior JS Engineer'],
        ['title' => 'PHP', 'track' => 'php', 'category' => 'backend', 'skills' => ['PHP 8', 'OOP', 'PDO', 'API', 'Security'], 'practice' => '', 'instructor' => 'Марат Юсупов, Backend Architect'],
        ['title' => 'Laravel', 'track' => 'laravel', 'category' => 'backend', 'skills' => ['Laravel', 'Eloquent', 'Blade', 'Queues', 'Testing'], 'practice' => '', 'instructor' => 'Марат Юсупов, Backend Architect'],
        ['title' => 'MySQL', 'track' => 'sql', 'category' => 'backend', 'skills' => ['SQL', 'Joins', 'Indexes', 'Transactions', 'EXPLAIN'], 'practice' => 'mysql', 'instructor' => 'Никита Орлов, Data Engineer'],
        ['title' => 'PostgreSQL', 'track' => 'sql', 'category' => 'backend', 'skills' => ['SQL', 'CTE', 'Window Functions', 'Indexes', 'Locks'], 'practice' => 'pgsql', 'instructor' => 'Никита Орлов, Data Engineer'],
        ['title' => 'NoSQL', 'track' => 'nosql', 'category' => 'backend', 'skills' => ['MongoDB', 'Redis', 'Data Modeling', 'Replication', 'Sharding'], 'practice' => '', 'instructor' => 'Егор Литвинов, Platform Engineer'],
        ['title' => 'C++', 'track' => 'programming', 'category' => 'backend', 'skills' => ['C++', 'STL', 'Algorithms', 'Memory', 'Complexity'], 'practice' => 'cpp', 'instructor' => 'Дмитрий Соколов, Systems Engineer'],
        ['title' => 'Python', 'track' => 'programming', 'category' => 'backend', 'skills' => ['Python', 'Data Structures', 'OOP', 'Testing', 'Scripting'], 'practice' => 'python', 'instructor' => 'Алина Громова, Senior Python Engineer'],
        ['title' => 'C', 'track' => 'programming', 'category' => 'backend', 'skills' => ['C', 'Pointers', 'Memory Layout', 'Arrays', 'Optimization'], 'practice' => 'c', 'instructor' => 'Дмитрий Соколов, Systems Engineer'],
        ['title' => 'C#', 'track' => 'programming', 'category' => 'backend', 'skills' => ['C#', '.NET', 'LINQ', 'OOP', 'Async'], 'practice' => 'csharp', 'instructor' => 'Ольга Руденко, .NET Lead'],
        ['title' => 'Java', 'track' => 'programming', 'category' => 'backend', 'skills' => ['Java', 'Collections', 'OOP', 'JVM Basics', 'Concurrency'], 'practice' => 'java', 'instructor' => 'Павел Лебедев, Java Architect'],
        ['title' => 'Git', 'track' => 'git', 'category' => 'devops', 'skills' => ['Git', 'Branching', 'Rebase', 'Conflict Resolution', 'PR Flow'], 'practice' => '', 'instructor' => 'Антон Захаров, Staff Engineer'],
        ['title' => 'DevOps', 'track' => 'devops', 'category' => 'devops', 'skills' => ['Linux', 'CI/CD', 'Docker', 'Kubernetes', 'Observability'], 'practice' => '', 'instructor' => 'Евгений Климов, DevOps Lead'],
        ['title' => 'Design', 'track' => 'design', 'category' => 'design', 'skills' => ['UX', 'UI', 'Typography', 'Design Systems', 'Figma'], 'practice' => '', 'instructor' => 'Ксения Андреева, Product Designer'],
        ['title' => 'Mobile-dev', 'track' => 'mobile', 'category' => 'other', 'skills' => ['Mobile UI', 'State', 'Networking', 'Performance', 'Release'], 'practice' => '', 'instructor' => 'Артем Никонов, Mobile Lead'],
        ['title' => 'Desktop-dev', 'track' => 'desktop', 'category' => 'other', 'skills' => ['Desktop UI', 'IPC', 'Threads', 'Packaging', 'Diagnostics'], 'practice' => '', 'instructor' => 'Илья Чистов, Desktop Engineer'],
        ['title' => 'English-A1', 'track' => 'english_a1', 'category' => 'other', 'skills' => ['Basic Grammar', 'Speaking', 'Listening', 'Vocabulary'], 'practice' => '', 'instructor' => 'Sofia Karimova, ESL Teacher'],
        ['title' => 'English-A2', 'track' => 'english_a2', 'category' => 'other', 'skills' => ['Grammar A2', 'Communication', 'Reading', 'Writing'], 'practice' => '', 'instructor' => 'Sofia Karimova, ESL Teacher'],
        ['title' => 'English-B1', 'track' => 'english_b1', 'category' => 'other', 'skills' => ['Intermediate Grammar', 'Fluency', 'Presentations', 'Business Writing'], 'practice' => '', 'instructor' => 'Mark Bennett, English Coach'],
    ];
}

function tfSeedPackTrackBlueprintsTechRich(): array
{
    return [
        'htmlcss' => [
            'summary' => 'Практический трек по современной верстке интерфейсов с упором на семантику, доступность и адаптивность.',
            'modules' => [
                'Семантический HTML и структура страницы',
                'Формы, валидация и UX-паттерны',
                'CSS каскад, специфичность и архитектура',
                'Flexbox для адаптивных интерфейсов',
                'Grid для сложных сеток',
                'Typography и визуальная иерархия',
                'Анимации, transition и micro-interactions',
                'Доступность (a11y) и ARIA',
                'Performance: критический CSS и оптимизация',
                'Capstone: адаптивная страница профиля',
            ],
            'scenarios' => [
                'верстка карточек вакансий с разными типами контента',
                'создание формы отклика на вакансию с live-валидацией',
                'адаптация интерфейса под мобильные устройства и планшеты',
                'подготовка лендинга под Lighthouse и accessibility checklist',
            ],
            'deliverables' => [
                'рабочий UI-модуль в Figma-to-code формате',
                'адаптивный экран с корректной семантикой',
                'пакет компонентов с переиспользуемыми классами',
            ],
            'resources' => [
                'https://developer.mozilla.org/en-US/docs/Web/HTML',
                'https://developer.mozilla.org/en-US/docs/Web/CSS',
                'https://web.dev/learn/css/',
            ],
            'keywords' => ['семантика', 'a11y', 'flexbox', 'grid', 'responsive', 'performance'],
        ],
        'javascript' => [
            'summary' => 'Трек по JavaScript для frontend-приложений: от базового синтаксиса до работы с API и архитектуры кода.',
            'modules' => [
                'Переменные, типы данных и контроль потока',
                'Функции, области видимости и замыкания',
                'Массивы, объекты и методы коллекций',
                'DOM API и обработка событий',
                'Fetch API и работа с REST',
                'Promises и async/await',
                'Модули, импорт/экспорт и сборка',
                'Обработка ошибок и debugging workflow',
                'Тестирование UI-логики',
                'Capstone: интерактивный dashboard вакансий',
            ],
            'scenarios' => [
                'обновление ленты вакансий без перезагрузки страницы',
                'валидация и отправка формы регистрации',
                'обработка таймаутов и сетевых ошибок API',
                'разделение проекта на модули для командной разработки',
            ],
            'deliverables' => [
                'модульный JS-код с обработкой ошибок',
                'интерактивный компонент с динамическими данными',
                'минимальный набор тестов для ключевой логики',
            ],
            'resources' => [
                'https://developer.mozilla.org/en-US/docs/Web/JavaScript',
                'https://javascript.info/',
                'https://web.dev/learn/javascript/',
            ],
            'keywords' => ['closure', 'promise', 'async/await', 'DOM', 'fetch', 'module'],
        ],
        'php' => [
            'summary' => 'Курс по PHP 8 для backend-разработки: архитектура, работа с БД, безопасность и API.',
            'modules' => [
                'PHP 8 синтаксис и типизация',
                'Функции, массивы и стандартная библиотека',
                'ООП: классы, интерфейсы, трейты',
                'HTTP, формы и валидация',
                'PDO и безопасная работа с SQL',
                'Сессии, авторизация и роли',
                'Обработка ошибок и логирование',
                'REST API и JSON контракты',
                'Безопасность: XSS, CSRF, SQL Injection',
                'Capstone: backend для системы откликов',
            ],
            'scenarios' => [
                'создание endpoint для профиля кандидата',
                'реализация безопасной регистрации и логина',
                'обработка бизнес-правил при создании вакансий',
                'расследование ошибок по логам production',
            ],
            'deliverables' => [
                'набор backend-endpoint с PDO',
                'модель авторизации с проверками роли',
                'документация API и тестовые запросы',
            ],
            'resources' => [
                'https://www.php.net/docs.php',
                'https://www.php-fig.org/psr/',
                'https://owasp.org/www-project-top-ten/',
            ],
            'keywords' => ['pdo', 'session', 'csrf', 'validation', 'api', 'exception'],
        ],
        'laravel' => [
            'summary' => 'Фреймворк-трек Laravel: MVC, Eloquent, middleware, очереди и production-подход.',
            'modules' => [
                'Структура Laravel и lifecycle запроса',
                'Маршруты и контроллеры',
                'Blade и компонентный UI',
                'Миграции и сиды',
                'Eloquent модели и связи',
                'Form Request валидация',
                'Аутентификация и authorization policies',
                'Очереди, jobs и events',
                'Feature тесты и CI integration',
                'Capstone: Laravel API + admin panel',
            ],
            'scenarios' => [
                'проектирование CRUD для админ-панели',
                'реализация очередей уведомлений по email',
                'оптимизация N+1 запросов в профиле',
                'настройка тестов для критичных flow',
            ],
            'deliverables' => [
                'приложение на Laravel с migration-first подходом',
                'покрытие ключевых endpoint feature-тестами',
                'документация deployment checklist',
            ],
            'resources' => [
                'https://laravel.com/docs',
                'https://laracasts.com/',
                'https://readouble.com/laravel/',
            ],
            'keywords' => ['eloquent', 'blade', 'middleware', 'queue', 'policy', 'migration'],
        ],
        'sql' => [
            'summary' => 'Курс по SQL для аналитики и backend-разработки: запросы, оптимизация и моделирование данных.',
            'modules' => [
                'SELECT, WHERE, ORDER BY в реальных отчётах',
                'JOIN стратегии и типичные ошибки',
                'Агрегации и бизнес-метрики',
                'CTE и читаемые запросы',
                'Оконные функции',
                'Индексы и план выполнения',
                'Транзакции и уровни изоляции',
                'Нормализация и проектирование схемы',
                'Оптимизация медленных запросов',
                'Capstone: аналитический SQL-пакет',
            ],
            'scenarios' => [
                'подготовка дашборда по воронке найма',
                'поиск просроченных SLA инцидентов',
                'оптимизация отчета, который выполняется более 5 секунд',
                'построение сводных таблиц для продуктовой команды',
            ],
            'deliverables' => [
                'набор production-ready SQL запросов',
                'таблица индексов с обоснованием',
                'мини-проект аналитической витрины',
            ],
            'resources' => [
                'https://dev.mysql.com/doc/',
                'https://www.postgresql.org/docs/',
                'https://use-the-index-luke.com/',
            ],
            'keywords' => ['join', 'cte', 'window', 'index', 'transaction', 'explain'],
        ],
        'nosql' => [
            'summary' => 'NoSQL трек: проектирование документов, индексация, кэширование и масштабирование.',
            'modules' => [
                'Документная модель и границы агрегатов',
                'CRUD и паттерны запросов',
                'Индексы и профилирование',
                'Aggregation Pipeline',
                'Репликация и отказоустойчивость',
                'Шардирование и выбор shard key',
                'Redis как cache и data structure store',
                'Согласованность и модели чтения',
                'Мониторинг и capacity planning',
                'Capstone: каталог профилей и рекомендаций',
            ],
            'scenarios' => [
                'модель профиля пользователя без жесткой схемы',
                'ускорение API через Redis cache',
                'подготовка репликации для high-availability',
                'анализ затрат при росте данных',
            ],
            'deliverables' => [
                'схема коллекций и индексов',
                'pipeline отчетов по активности',
                'план масштабирования NoSQL-сервиса',
            ],
            'resources' => [
                'https://www.mongodb.com/docs/',
                'https://redis.io/docs/latest/',
                'https://www.cockroachlabs.com/docs/stable/',
            ],
            'keywords' => ['document', 'index', 'replication', 'sharding', 'cache', 'consistency'],
        ],
        'programming' => [
            'summary' => 'Базовый алгоритмический трек для инженерного мышления и решения интервью-задач.',
            'modules' => [
                'Синтаксис {lang} и базовые конструкции',
                'Строки, массивы и коллекции в {lang}',
                'Функции и декомпозиция',
                'Сложность алгоритмов и Big O',
                'Хеш-таблицы и множества',
                'Стек, очередь и двоичный поиск',
                'Графы и обходы',
                'Динамическое программирование',
                'Тестирование и отладка',
                'Capstone: набор задач для интервью в {lang}',
            ],
            'scenarios' => [
                'решение задач под ограничение по времени',
                'поиск регрессии через минимальные тест-кейсы',
                'переписывание решения с O(n^2) на O(n log n)',
                'подготовка к live-coding интервью',
            ],
            'deliverables' => [
                'портфолио решенных задач с объяснениями',
                'библиотека шаблонов решений',
                'чеклист для код-ревью алгоритмов',
            ],
            'resources' => [
                'https://cp-algorithms.com/',
                'https://neetcode.io/roadmap',
                'https://leetcode.com/problemset/',
            ],
            'keywords' => ['array', 'hash', 'graph', 'dp', 'complexity', 'debugging'],
        ],
        'git' => [
            'summary' => 'Инженерный курс по Git workflow: история изменений, ветвление, ревью и release management.',
            'modules' => [
                'Модель данных Git и объекты',
                'Ежедневный цикл add/commit/log',
                'Ветки и feature workflow',
                'Merge и разбор конфликтов',
                'Rebase и чистая история',
                'Cherry-pick, revert и hotfix',
                'Remote, fork и pull request',
                'Git hooks и автоматизация',
                'Release tags и versioning',
                'Capstone: командный workflow в Git',
            ],
            'scenarios' => [
                'работа в команде с десятками pull request',
                'быстрый hotfix в production ветку',
                'разрешение сложного merge-конфликта',
                'подготовка релиза по тегу и changelog',
            ],
            'deliverables' => [
                'готовый git workflow для команды',
                'чеклист code-review и merge policy',
                'набор автоматических хуков проверки',
            ],
            'resources' => [
                'https://git-scm.com/doc',
                'https://www.atlassian.com/git/tutorials',
                'https://ohshitgit.com/',
            ],
            'keywords' => ['branch', 'merge', 'rebase', 'remote', 'hook', 'release'],
        ],
        'devops' => [
            'summary' => 'Курс DevOps-практик: CI/CD, контейнеризация, оркестрация, мониторинг и инцидент-менеджмент.',
            'modules' => [
                'Linux, shell и автоматизация задач',
                'Сети, DNS и HTTP',
                'CI/CD pipeline и quality gates',
                'Docker и контейнерные best practices',
                'Kubernetes: deployments и services',
                'Secrets, config и безопасность',
                'Observability: metrics, logs, traces',
                'Скалирование и cost optimization',
                'Incident response и postmortem',
                'Capstone: production deployment pipeline',
            ],
            'scenarios' => [
                'разворачивание сервиса с нуля в CI/CD',
                'анализ деградации производительности в кластере',
                'ротация секретов без даунтайма',
                'реакция на продакшн-инцидент с SLA',
            ],
            'deliverables' => [
                'рабочий pipeline deployment',
                'набор алертов и метрик',
                'playbook для инцидентов',
            ],
            'resources' => [
                'https://kubernetes.io/docs/home/',
                'https://docs.docker.com/',
                'https://prometheus.io/docs/introduction/overview/',
            ],
            'keywords' => ['ci/cd', 'docker', 'kubernetes', 'monitoring', 'secrets', 'incident'],
        ],
        'design' => [
            'summary' => 'Дизайн-трек: от UX-исследований до системных UI-компонентов и handoff в разработку.',
            'modules' => [
                'UX research и постановка гипотез',
                'User flow и информационная архитектура',
                'Wireframes и low-fidelity прототипы',
                'Типографика и визуальная иерархия',
                'Цвет, контраст и accessibility',
                'Компоненты и дизайн-система',
                'Мобильный и desktop адаптив',
                'Прототипирование в Figma',
                'Handoff, спецификации и токены',
                'Capstone: дизайн страницы вакансии',
            ],
            'scenarios' => [
                'проектирование onboarding flow для кандидатов',
                'улучшение конверсии формы отклика',
                'подготовка UI-kit для команды разработки',
                'валидирование решений на usability-тестах',
            ],
            'deliverables' => [
                'прототип высокого уровня детализации',
                'дизайн-система с компонентами',
                'handoff пакет для разработки',
            ],
            'resources' => [
                'https://www.nngroup.com/articles/',
                'https://material.io/design',
                'https://www.figma.com/community',
            ],
            'keywords' => ['ux', 'ui', 'wireframe', 'typography', 'component', 'handoff'],
        ],
        'mobile' => [
            'summary' => 'Мобильная разработка: архитектура приложения, состояние, сетевой слой и публикация.',
            'modules' => [
                'Архитектура мобильного приложения',
                'UI-навигация и lifecycle',
                'State management',
                'Работа с API и offline-first',
                'Локальное хранилище и кеш',
                'Push-уведомления и background tasks',
                'Медиа, камера и permissions',
                'Performance profiling',
                'Сборка, подпись и релиз',
                'Capstone: мобильный клиент ITsphere360',
            ],
            'scenarios' => [
                'настройка оффлайн-режима для ленты задач',
                'оптимизация времени старта приложения',
                'интеграция push для вакансий',
                'подготовка релиза в сторах',
            ],
            'deliverables' => [
                'MVP мобильного приложения',
                'чеклист стабильности и производительности',
                'документация релизного процесса',
            ],
            'resources' => [
                'https://developer.android.com/docs',
                'https://developer.apple.com/documentation',
                'https://reactnative.dev/docs/getting-started',
            ],
            'keywords' => ['navigation', 'state', 'api', 'offline', 'performance', 'release'],
        ],
        'desktop' => [
            'summary' => 'Desktop-трек: архитектура нативного/кроссплатформенного приложения, IPC и поставка обновлений.',
            'modules' => [
                'Архитектура desktop приложений',
                'UI toolkit и компонентный подход',
                'События, команды и state',
                'Файловая система и безопасность',
                'Многопоточность и async задачи',
                'IPC и взаимодействие модулей',
                'Локальная БД и миграции',
                'Логирование и диагностика',
                'Пакетирование и автообновления',
                'Capstone: desktop приложение для рекрутера',
            ],
            'scenarios' => [
                'долгие фоновые операции без фризов UI',
                'надежное хранение пользовательских данных',
                'диагностика падений через логи',
                'доставка обновлений без переустановки',
            ],
            'deliverables' => [
                'рабочий desktop MVP',
                'архитектура модулей и IPC схема',
                'инструкции по релизу и обновлению',
            ],
            'resources' => [
                'https://learn.microsoft.com/en-us/dotnet/desktop/',
                'https://www.electronjs.org/docs/latest/',
                'https://doc.qt.io/',
            ],
            'keywords' => ['threads', 'ipc', 'storage', 'logging', 'packaging', 'updates'],
        ],
    ];
}

function tfSeedPackTrackBlueprintsEnglishRich(): array
{
    return [
        'english_a1' => [
            'summary' => 'English A1: базовые конструкции для повседневного общения и учебного процесса.',
            'modules' => [
                'Greetings и самопрезентация',
                'To be и простые предложения',
                'Present Simple в ежедневных действиях',
                'Questions и короткие ответы',
                'Numbers, dates и время',
                'Family, work и hobbies vocabulary',
                'There is/There are и prepositions',
                'Can/Can not и requests',
                'Basic listening и pronunciation',
                'Capstone: короткое интервью A1',
            ],
            'scenarios' => [
                'представиться на первом созвоне',
                'рассказать про рабочий день',
                'задать базовые вопросы коллеге',
                'понять простую инструкцию на английском',
            ],
            'deliverables' => [
                'самопрезентация на 1-2 минуты',
                'словарь из 200 базовых слов',
                'мини-диалог для собеседования',
            ],
            'resources' => [
                'https://learnenglish.britishcouncil.org/',
                'https://www.cambridgeenglish.org/learning-english/',
                'https://www.perfect-english-grammar.com/',
            ],
            'keywords' => ['greetings', 'to be', 'present simple', 'questions', 'vocabulary', 'listening'],
        ],
        'english_a2' => [
            'summary' => 'English A2: уверенное повседневное общение, простые тексты и рабочая коммуникация.',
            'modules' => [
                'Present Continuous и текущие действия',
                'Past Simple: regular и irregular verbs',
                'Future forms: will и going to',
                'Comparatives и superlatives',
                'Countable/uncountable nouns',
                'Present Perfect basics',
                'Travel и health vocabulary',
                'Email writing basics',
                'Dialogues и role-play',
                'Capstone: рабочий разговор A2',
            ],
            'scenarios' => [
                'описание опыта и прошлых задач',
                'планирование встречи на неделю',
                'короткая переписка с HR',
                'разговор о целях обучения',
            ],
            'deliverables' => [
                'короткое email-письмо на английском',
                'устный рассказ о прошлой работе',
                'словарь на 400 слов A2',
            ],
            'resources' => [
                'https://www.englishpage.com/',
                'https://www.bbc.co.uk/learningenglish/',
                'https://www.examenglish.com/A2/',
            ],
            'keywords' => ['past simple', 'present perfect', 'future', 'comparatives', 'emails', 'dialogue'],
        ],
        'english_b1' => [
            'summary' => 'English B1: уверенное общение в рабочих контекстах, презентации и аргументация.',
            'modules' => [
                'Past Perfect и narrative tenses',
                'Conditionals 1/2 и гипотезы',
                'Passive voice в деловой речи',
                'Reported speech и summaries',
                'Gerunds и infinitives',
                'Business vocabulary и collocations',
                'Email и meeting communication',
                'Presentations и storytelling',
                'Debate и аргументация',
                'Capstone: mock interview B1',
            ],
            'scenarios' => [
                'обсуждение решений команды на встрече',
                'презентация фичи для стейкхолдеров',
                'переписка с международной командой',
                'ответы на вопросы на собеседовании',
            ],
            'deliverables' => [
                'структурированная презентация на 5 минут',
                'рабочая переписка без шаблонных ошибок',
                'подготовленный mock interview script',
            ],
            'resources' => [
                'https://www.businessenglishpod.com/',
                'https://learnenglish.britishcouncil.org/skills/speaking/b1-speaking',
                'https://www.oxfordlearnersdictionaries.com/',
            ],
            'keywords' => ['conditionals', 'passive', 'reported speech', 'presentation', 'debate', 'business english'],
        ],
    ];
}

function tfSeedPackTrackBlueprintsRich(): array
{
    return array_merge(
        tfSeedPackTrackBlueprintsTechRich(),
        tfSeedPackTrackBlueprintsEnglishRich()
    );
}

function tfSeedPackRoadmapPathByTrackRich(string $track): string
{
    $map = [
        'htmlcss' => 'frontend',
        'javascript' => 'javascript',
        'php' => 'php',
        'laravel' => 'laravel',
        'sql' => 'sql',
        'nosql' => 'mongodb',
        'programming' => 'computer-science',
        'git' => 'git-github',
        'devops' => 'devops',
        'design' => 'ux-design',
        'mobile' => 'android',
        'desktop' => 'backend',
        'english_a1' => '',
        'english_a2' => '',
        'english_b1' => '',
    ];
    $slug = trim((string) ($map[$track] ?? ''));
    if ($slug === '') {
        return 'https://roadmap.sh/';
    }
    return 'https://roadmap.sh/' . rawurlencode($slug);
}

function tfSeedPackModulesForCourseRich(array $blueprint, string $courseTitle): array
{
    $modules = array_values((array) ($blueprint['modules'] ?? []));
    $keywords = array_values(array_filter(array_map('trim', (array) ($blueprint['keywords'] ?? []))));
    if (empty($keywords)) {
        $keywords = ['практика', 'кейсы', 'разбор ошибок', 'проект', 'ревью'];
    }
    $result = [];
    foreach ($modules as $module) {
        $text = trim((string) $module);
        if ($text === '') {
            continue;
        }
        $result[] = str_replace('{lang}', $courseTitle, $text);
    }
    $result = array_values(array_unique($result));
    $targetModules = 20;
    $idx = 1;
    while (count($result) < $targetModules) {
        $kw = $keywords[($idx - 1) % count($keywords)];
        $extra = "Практикум {$idx}: {$kw} ({$courseTitle})";
        if (!in_array($extra, $result, true)) {
            $result[] = $extra;
        }
        $idx++;
    }
    if (count($result) > $targetModules) {
        $result = array_slice($result, 0, $targetModules);
    }
    return $result;
}

function tfSeedPackCodePracticeCatalogRich(): array
{
    $raw = tfSeedPackRealProblemsCatalog();
    $list = [];
    foreach ($raw as $item) {
        $tests = [];
        foreach ((array) ($item['tests'] ?? []) as $test) {
            $tests[] = [
                'stdin' => (string) ($test['in'] ?? ''),
                'expected' => (string) ($test['out'] ?? ''),
            ];
        }
        if (empty($tests)) {
            continue;
        }
        $list[] = [
            'title' => (string) ($item['title'] ?? 'Practice Task'),
            'prompt' => trim(
                (string) ($item['statement'] ?? '') . "\n\n"
                . "Формат ввода:\n" . (string) ($item['input'] ?? '') . "\n\n"
                . "Формат вывода:\n" . (string) ($item['output'] ?? '')
            ),
            'tests' => $tests,
        ];
    }
    return $list;
}

function tfSeedPackSqlPracticeCatalogRich(): array
{
    return [
        [
            'title' => 'Retention по когортам',
            'prompt' => "Для каждой когорты (месяц регистрации) посчитайте число пользователей, вернувшихся в следующий месяц.",
            'setup_sql' => [
                "DROP TABLE IF EXISTS users",
                "DROP TABLE IF EXISTS logins",
                "CREATE TABLE users (id INT, signup_month DATE)",
                "CREATE TABLE logins (user_id INT, login_month DATE)",
                "INSERT INTO users VALUES (1,'2026-01-01'),(2,'2026-01-01'),(3,'2026-02-01')",
                "INSERT INTO logins VALUES (1,'2026-02-01'),(2,'2026-03-01'),(3,'2026-03-01')"
            ],
            'expected_sql' => "SELECT u.signup_month, COUNT(DISTINCT l.user_id) AS retained_next_month FROM users u LEFT JOIN logins l ON l.user_id = u.id AND l.login_month = DATE_ADD(u.signup_month, INTERVAL 1 MONTH) GROUP BY u.signup_month ORDER BY u.signup_month"
        ],
        [
            'title' => 'SLA breach rate',
            'prompt' => "Посчитайте долю инцидентов с response_minutes > 30 в процентах по каждой команде.",
            'setup_sql' => [
                "DROP TABLE IF EXISTS incidents",
                "CREATE TABLE incidents (id INT, team VARCHAR(20), response_minutes INT)",
                "INSERT INTO incidents VALUES (1,'api',20),(2,'api',45),(3,'core',15),(4,'core',50),(5,'core',80)"
            ],
            'expected_sql' => "SELECT team, ROUND(100.0 * SUM(response_minutes > 30) / COUNT(*), 2) AS breach_rate FROM incidents GROUP BY team ORDER BY team"
        ],
        [
            'title' => 'Выручка по неделям',
            'prompt' => "Выведите weekly_revenue по полю week_start.",
            'setup_sql' => [
                "DROP TABLE IF EXISTS orders",
                "CREATE TABLE orders (id INT, week_start DATE, amount INT)",
                "INSERT INTO orders VALUES (1,'2026-02-02',120),(2,'2026-02-02',80),(3,'2026-02-09',200)"
            ],
            'expected_sql' => "SELECT week_start, SUM(amount) AS weekly_revenue FROM orders GROUP BY week_start ORDER BY week_start"
        ],
        [
            'title' => 'Пользователи без активности',
            'prompt' => "Найдите пользователей, у которых нет ни одного события в events.",
            'setup_sql' => [
                "DROP TABLE IF EXISTS users",
                "DROP TABLE IF EXISTS events",
                "CREATE TABLE users (id INT, email VARCHAR(60))",
                "CREATE TABLE events (id INT, user_id INT, event_name VARCHAR(30))",
                "INSERT INTO users VALUES (1,'a@x.com'),(2,'b@x.com'),(3,'c@x.com')",
                "INSERT INTO events VALUES (1,1,'login'),(2,1,'apply'),(3,3,'login')"
            ],
            'expected_sql' => "SELECT u.id, u.email FROM users u LEFT JOIN events e ON e.user_id = u.id WHERE e.id IS NULL ORDER BY u.id"
        ],
        [
            'title' => 'Top вакансии по откликам',
            'prompt' => "Выведите 3 вакансии с максимальным числом откликов.",
            'setup_sql' => [
                "DROP TABLE IF EXISTS applications",
                "CREATE TABLE applications (id INT, vacancy_id INT)",
                "INSERT INTO applications VALUES (1,101),(2,101),(3,103),(4,101),(5,102),(6,103)"
            ],
            'expected_sql' => "SELECT vacancy_id, COUNT(*) AS applies FROM applications GROUP BY vacancy_id ORDER BY applies DESC, vacancy_id ASC LIMIT 3"
        ],
        [
            'title' => 'Rolling 3-day average',
            'prompt' => "Для daily_metrics посчитайте 3-дневное скользящее среднее по metric_value.",
            'setup_sql' => [
                "DROP TABLE IF EXISTS daily_metrics",
                "CREATE TABLE daily_metrics (d DATE, metric_value INT)",
                "INSERT INTO daily_metrics VALUES ('2026-03-01',10),('2026-03-02',20),('2026-03-03',30),('2026-03-04',40)"
            ],
            'expected_sql' => "SELECT d, ROUND(AVG(metric_value) OVER (ORDER BY d ROWS BETWEEN 2 PRECEDING AND CURRENT ROW), 2) AS rolling_avg_3d FROM daily_metrics ORDER BY d"
        ],
    ];
}

function tfSeedPackUtf8Trim(string $text): string
{
    $text = trim((string) preg_replace('/\s+/u', ' ', $text));
    return $text;
}

function tfSeedPackUtf8Limit(string $text, int $limit): string
{
    $text = tfSeedPackUtf8Trim($text);
    if ($text === '' || $limit <= 0) {
        return '';
    }
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text, 'UTF-8') <= $limit) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $limit, 'UTF-8'));
    }
    if (strlen($text) <= $limit) {
        return $text;
    }
    return rtrim(substr($text, 0, $limit));
}

function tfSeedPackBuildYouTubeRuQuery(string $courseTitle, string $topic, int $lessonIndex = 0): string
{
    $title = tfSeedPackUtf8Limit($courseTitle, 60);
    $topicText = tfSeedPackUtf8Limit($topic, 80);
    $lessonPart = $lessonIndex > 0 ? ' урок ' . $lessonIndex : '';
    return tfSeedPackUtf8Trim($title . ' ' . $topicText . $lessonPart . ' русский');
}

function tfSeedPackBuildYouTubeRuSearchUrl(string $courseTitle, string $topic, int $lessonIndex = 0): string
{
    $query = tfSeedPackBuildYouTubeRuQuery($courseTitle, $topic, $lessonIndex);
    return 'https://www.youtube.com/embed?listType=search&list=' . rawurlencode($query);
}

function tfSeedPackBuildYouTubeRuResultsUrl(string $courseTitle, string $topic, int $lessonIndex = 0): string
{
    $query = tfSeedPackBuildYouTubeRuQuery($courseTitle, $topic, $lessonIndex);
    return 'https://www.youtube.com/results?' . http_build_query(
        ['search_query' => $query],
        '',
        '&',
        PHP_QUERY_RFC3986
    );
}

function tfSeedPackShuffleOptions(array $options, string $seed): array
{
    $options = array_values(array_filter($options, static function ($value) {
        return $value !== '' && $value !== null;
    }));
    $count = count($options);
    if ($count < 2) {
        return $options;
    }

    $hash = crc32($seed);
    for ($i = $count - 1; $i > 0; $i--) {
        $hash = ($hash * 1103515245 + 12345) & 0x7fffffff;
        $j = $hash % ($i + 1);
        if ($i !== $j) {
            $tmp = $options[$i];
            $options[$i] = $options[$j];
            $options[$j] = $tmp;
        }
    }

    return $options;
}

function tfSeedPackBuildCourseExamRich(string $courseTitle, array $modules, array $keywords): array
{
    $pool = array_values(array_unique(array_filter(array_map('trim', $modules))));
    if (empty($pool)) {
        $pool = ['Базовый модуль'];
    }
    while (count($pool) < 4) {
        $pool[] = 'Дополнительный модуль ' . (count($pool) + 1);
    }

    $keywordPool = array_values(array_unique(array_filter(array_map('trim', $keywords))));
    if (empty($keywordPool)) {
        $keywordPool = ['практика', 'архитектура', 'отладка', 'quality'];
    }

    $questions = [];
    $size = count($pool);
    for ($i = 0; $i < 30; $i++) {
        $correct = $pool[$i % $size];
        if ($i % 5 === 4) {
            $statementTrue = ($i % 10 !== 9);
            $topic = $statementTrue ? $correct : $pool[($i + 2) % $size];
            $questions[] = [
                'type' => 'true_false',
                'question' => $statementTrue
                    ? "Тема «{$topic}» входит в программу курса «{$courseTitle}»."
                    : "Тема «{$topic}» не относится к курсу «{$courseTitle}».",
                'options' => ['Верно', 'Неверно'],
                'correct_answer' => $statementTrue ? 'Верно' : 'Неверно',
            ];
            continue;
        }

        if ($i % 8 === 7) {
            $secondary = $pool[($i + 1) % $size];
            $multiOptions = array_slice(array_values(array_unique(array_merge(
                [$correct, $secondary],
                array_slice($pool, ($i + 2) % $size, 2)
            ))), 0, 4);
            $multiOptions = tfSeedPackShuffleOptions($multiOptions, $courseTitle . '|' . $correct . '|multi|' . $i);
            $questions[] = [
                'type' => 'mc_multi',
                'question' => "Select all modules related to \"{$courseTitle}\" topic \"{$correct}\".",
                'options' => $multiOptions,
                'correct_answers' => [$correct, $secondary],
            ];
            continue;
        }

        $distractors = [];
        $cursor = 1;
        while (count($distractors) < 3 && $cursor < ($size + 8)) {
            $candidate = $pool[($i + $cursor * 2) % $size];
            if ($candidate !== $correct && !in_array($candidate, $distractors, true)) {
                $distractors[] = $candidate;
            }
            $cursor++;
        }
        foreach (['Маркетинг', 'Случайная тема', 'Не относится к курсу'] as $fallback) {
            if (count($distractors) >= 3) {
                break;
            }
            if ($fallback !== $correct && !in_array($fallback, $distractors, true)) {
                $distractors[] = $fallback;
            }
        }
        $options = array_slice(array_values(array_unique(array_merge([$correct], $distractors))), 0, 4);
        if (count($options) > 1) {
            $shift = $i % count($options);
            $options = array_merge(array_slice($options, $shift), array_slice($options, 0, $shift));
        }

        $options = tfSeedPackShuffleOptions($options, $courseTitle . "|" . $correct . "|" . $i);
        $keyword = $keywordPool[$i % count($keywordPool)];
        $templates = [
            "В каком модуле курса «{$courseTitle}» изучается «{$keyword}»?",
            "Какой раздел курса «{$courseTitle}» лучше всего покрывает тему «{$correct}»?",
            "Для практического кейса по «{$keyword}» в «{$courseTitle}» нужно выбрать:",
            "Какой модуль является ключевым для темы «{$correct}» в этом курсе?",
        ];
        $templates = array_merge($templates, [
            "Which module in \"{$courseTitle}\" best supports \"{$correct}\"?",
            "Pick the section that most directly covers \"{$keyword}\" in \"{$courseTitle}\".",
            "For the theme \"{$correct}\", which module should be taken first?",
            "Where would you focus a review if \"{$keyword}\" is weak?",
        ]);

        $questions[] = [
            'type' => 'mc_single',
            'question' => $templates[$i % count($templates)],
            'options' => $options,
            'correct_answer' => $correct,
        ];
    }

    return $questions;
}

function tfSeedPackBuildCourseLessonQuizRich(string $courseTitle, string $topic, array $modules, array $keywords, int $index): array
{
    $pool = array_values(array_unique(array_filter(array_map('trim', $modules))));
    if (empty($pool)) {
        $pool = ['Base module'];
    }
    while (count($pool) < 4) {
        $pool[] = 'Extra module ' . (count($pool) + 1);
    }
    $keywordPool = array_values(array_unique(array_filter(array_map('trim', $keywords))));
    if (empty($keywordPool)) {
        $keywordPool = ['practice', 'debugging', 'quality', 'delivery'];
    }
    $size = count($pool);
    $correct = $topic;
    $options = tfSeedPackShuffleOptions([
        $correct,
        $pool[($index + 1) % $size],
        $pool[($index + 2) % $size],
        $pool[($index + 3) % $size],
    ], $courseTitle . '|' . $topic . '|lesson-quiz');
    $keyword = $keywordPool[$index % count($keywordPool)];

    return [
        [
            'question' => "Mini-check: which module reinforces \"{$topic}\" in \"{$courseTitle}\"?",
            'options' => $options,
            'correct_index' => array_search($correct, $options, true) + 1,
        ],
        [
            'question' => "What is the expected outcome after the topic \"{$topic}\"?",
            'options' => [
                "Apply {$topic} in a real scenario",
                "Memorize terms without practice",
                "Skip hands-on work",
                "Focus on unrelated topics",
            ],
            'correct_index' => 1,
        ],
        [
            'question' => "Which focus is most important for \"{$topic}\"?",
            'options' => [
                "Practice and {$keyword}",
                "Only theory",
                "Long reading without verification",
                "Skipping review",
            ],
            'correct_index' => 1,
        ],
        [
            'question' => "Select all statements that are true for \"{$topic}\".",
            'options' => [
                "Requires hands-on practice",
                "Can be skipped without impact",
                "Uses {$keyword} in real tasks",
                "Never needs review",
            ],
            'correct_options' => [1, 3],
        ],
    ];
}

function tfSeedPackBuildCourseLessonsRich(string $courseTitle, string $track, string $practiceLang = ''): array
{
    $blueprints = tfSeedPackTrackBlueprintsRich();
    $blueprint = (array) ($blueprints[$track] ?? []);
    $modules = tfSeedPackModulesForCourseRich($blueprint, $courseTitle);
    $scenarios = array_values((array) ($blueprint['scenarios'] ?? []));
    $deliverables = array_values((array) ($blueprint['deliverables'] ?? []));
    $resources = array_values((array) ($blueprint['resources'] ?? []));
    if (empty($scenarios)) {
        $scenarios = ['практическая задача в рабочем проекте'];
    }
    if (empty($deliverables)) {
        $deliverables = ['рабочий модуль по теме'];
    }
    if (empty($resources)) {
        $resources = ['https://roadmap.sh/'];
    }

    $codeTasks = tfSeedPackCodePracticeCatalogRich();
    $sqlTasks = tfSeedPackSqlPracticeCatalogRich();
    $lessons = [];

    foreach ($modules as $idx => $topic) {
        $scenario = $scenarios[$idx % count($scenarios)];
        $deliverable = $deliverables[$idx % count($deliverables)];
        $resource = $resources[$idx % count($resources)];
        $youtubeEmbedUrl = tfSeedPackBuildYouTubeRuSearchUrl($courseTitle, $topic, $idx + 1);
        $youtubeResultsUrl = tfSeedPackBuildYouTubeRuResultsUrl($courseTitle, $topic, $idx + 1);
        $lesson = [
            'title' => 'Модуль ' . ($idx + 1) . '. ' . $topic,
            'type' => 'article',
            'content' => trim(
                "Цель:\nОсвоить тему «{$topic}» в контексте курса «{$courseTitle}».\n\n"
                . "Реальный кейс:\n{$scenario}.\n\n"
                . "Что делаем на модуле:\n"
                . "1) Разбираем ключевые концепции и граничные случаи.\n"
                . "2) Смотрим production-подход и типовые ошибки.\n"
                . "3) Фиксируем решение через практическое задание.\n\n"
                . "Ожидаемый результат:\n{$deliverable}.\n\n"
                . "Checklist:\n"
                . "- Понимаю терминологию и ограничения темы.\n"
                . "- Могу реализовать типовую задачу без подсказок.\n"
                . "- Могу объяснить trade-offs выбранного решения.\n\n"
                . "Рекомендуемые ссылки:\n"
                . "- Документация: {$resource}\n"
                . "- Видео на русском (YouTube): ссылка в блоке «Материалы»"
            ),
            'video_url' => $youtubeEmbedUrl,
            'materials_title' => 'YouTube: видео на русском по теме',
            'materials_url' => $youtubeResultsUrl,
        ];

        if ($practiceLang === 'mysql' || $practiceLang === 'pgsql') {
            $task = $sqlTasks[$idx % count($sqlTasks)];
            $lesson['practice'] = [
                'language' => $practiceLang,
                'title' => $task['title'] . ' — ' . $topic,
                'prompt' => "Контекст модуля: «{$topic}».\n" . $task['prompt'],
                'starter_code' => tfSeedPackStarterCode($practiceLang),
                'tests' => [
                    [
                        'setup_sql' => (array) ($task['setup_sql'] ?? []),
                        'expected_sql' => (string) ($task['expected_sql'] ?? ''),
                    ]
                ],
            ];
        } elseif (in_array($practiceLang, ['cpp', 'python', 'c', 'csharp', 'java', 'js'], true) && !empty($codeTasks)) {
            $task = $codeTasks[$idx % count($codeTasks)];
            $lesson['practice'] = [
                'language' => $practiceLang,
                'title' => $task['title'] . ' — ' . $topic,
                'prompt' => "Контекст модуля: «{$topic}».\n" . (string) ($task['prompt'] ?? ''),
                'starter_code' => tfSeedPackStarterCode($practiceLang),
                'tests' => (array) ($task['tests'] ?? []),
            ];
        }

        $lessons[] = $lesson;

        if ($idx % 3 === 1) {
            $quizLesson = [
                "title" => "Quiz: " . $topic,
                "type" => "quiz",
                "content" => "Quick check on {$topic}.",
                "video_url" => "",
                "materials_title" => "",
                "materials_url" => "",
                "questions" => tfSeedPackBuildCourseLessonQuizRich($courseTitle, $topic, $modules, (array) ($blueprint["keywords"] ?? []), $idx),
            ];
            $lessons[] = $quizLesson;
        }
    }

    return $lessons;
}

function tfSeedPackUpsertLessonQuiz(PDO $pdo, int $lessonId, array $quizData): void
{
    $stmt = $pdo->prepare("DELETE FROM quiz_questions WHERE lesson_id = ?");
    $stmt->execute([$lessonId]);

    if (empty($quizData)) {
        return;
    }

    foreach ($quizData as $q) {
        if (!is_array($q)) {
            continue;
        }
        $question = trim((string) ($q['question'] ?? ''));
        $options = $q['options'] ?? [];
        if ($question === '' || !is_array($options) || count($options) < 2) {
            continue;
        }

        $correctIndex = (int) ($q['correct_index'] ?? 0);
        $correctOptionsRaw = $q['correct_options'] ?? null;
        $correctOptions = [];
        if (is_array($correctOptionsRaw)) {
            foreach ($correctOptionsRaw as $opt) {
                if (is_numeric($opt)) {
                    $idx = (int) $opt;
                    if ($idx >= 1 && $idx <= count($options)) {
                        $correctOptions[] = $idx;
                    }
                } else {
                    $pos = array_search((string) $opt, $options, true);
                    if ($pos !== false) {
                        $correctOptions[] = $pos + 1;
                    }
                }
            }
        }

        $correctOptions = array_values(array_unique(array_filter($correctOptions)));
        if (empty($correctOptions)) {
            if ($correctIndex < 1 || $correctIndex > count($options)) {
                $correctAnswer = (string) ($q['correct_answer'] ?? '');
                $pos = array_search($correctAnswer, $options, true);
                $correctIndex = $pos === false ? 1 : ($pos + 1);
            }
        } else {
            $correctIndex = (int) ($correctOptions[0] ?? 1);
        }

        $correctOptionsCsv = !empty($correctOptions) ? implode(',', $correctOptions) : null;
        $stmt = $pdo->prepare("INSERT INTO quiz_questions (lesson_id, question_text, correct_option, correct_options) VALUES (?, ?, ?, ?)");
        $stmt->execute([$lessonId, $question, $correctIndex, $correctOptionsCsv]);
        $questionId = (int) $pdo->lastInsertId();
        $order = 1;
        foreach ($options as $opt) {
            $optText = trim((string) $opt);
            if ($optText === '') {
                continue;
            }
            $stmt = $pdo->prepare("INSERT INTO quiz_options (question_id, option_text, option_order) VALUES (?, ?, ?)");
            $stmt->execute([$questionId, $optText, $order++]);
        }
    }
}

function tfUpsertCourseRich(PDO $pdo, array $courseDef): array
{
    ensureCoursesSchema($pdo);
    ensureLessonsSchema($pdo);
    ensurePracticeSchema($pdo);
    ensureQuizSchema($pdo);
    if (function_exists('ensureCourseExamsSchema')) {
        ensureCourseExamsSchema($pdo);
    }

    $title = trim((string) ($courseDef['title'] ?? ''));
    if ($title === '') {
        return ['ok' => false, 'error' => 'missing_title'];
    }

    $stmt = $pdo->prepare("SELECT id FROM courses WHERE title = ? LIMIT 1");
    $stmt->execute([$title]);
    $courseId = (int) ($stmt->fetchColumn() ?: 0);
    $created = false;

    $pdo->beginTransaction();
    try {
        if ($courseId <= 0) {
            $insert = $pdo->prepare("
                INSERT INTO courses (title, instructor, description, category, image_url, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $insert->execute([
                $title,
                (string) ($courseDef['instructor'] ?? 'ITsphere360 Academy'),
                (string) ($courseDef['description'] ?? ''),
                (string) ($courseDef['category'] ?? 'other'),
                (string) ($courseDef['image_url'] ?? ''),
            ]);
            $courseId = (int) $pdo->lastInsertId();
            $created = true;
        } else {
            $update = $pdo->prepare("
                UPDATE courses
                SET instructor = ?, description = ?, category = ?, image_url = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $update->execute([
                (string) ($courseDef['instructor'] ?? 'ITsphere360 Academy'),
                (string) ($courseDef['description'] ?? ''),
                (string) ($courseDef['category'] ?? 'other'),
                (string) ($courseDef['image_url'] ?? ''),
                $courseId,
            ]);

            $lessonStmt = $pdo->prepare("SELECT id FROM lessons WHERE course_id = ?");
            $lessonStmt->execute([$courseId]);
            $lessonIds = array_map('intval', (array) $lessonStmt->fetchAll(PDO::FETCH_COLUMN));
            if (!empty($lessonIds)) {
                $ph = implode(',', array_fill(0, count($lessonIds), '?'));
                $delPractice = $pdo->prepare("DELETE FROM lesson_practice_tasks WHERE lesson_id IN ({$ph})");
                $delPractice->execute($lessonIds);
            }

            $pdo->prepare("DELETE FROM lessons WHERE course_id = ?")->execute([$courseId]);
            $pdo->prepare("DELETE FROM course_skills WHERE course_id = ?")->execute([$courseId]);
            $pdo->prepare("DELETE FROM course_exams WHERE course_id = ?")->execute([$courseId]);
        }

        $skills = array_values(array_unique(array_filter(array_map('trim', (array) ($courseDef['skills'] ?? [])))));
        $insertSkill = $pdo->prepare("INSERT INTO course_skills (course_id, skill_name, skill_level) VALUES (?, ?, 0)");
        foreach ($skills as $skill) {
            $insertSkill->execute([$courseId, $skill]);
        }

        $insertLesson = $pdo->prepare("
            INSERT INTO lessons (course_id, title, type, content, video_url, materials_title, materials_url, order_num, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $insertPractice = $pdo->prepare("
            INSERT INTO lesson_practice_tasks (lesson_id, language, title, prompt, starter_code, tests_json, is_required)
            VALUES (?, ?, ?, ?, ?, ?, 1)
        ");

        $lessons = (array) ($courseDef['lessons'] ?? []);
        $order = 0;
        foreach ($lessons as $lesson) {
            if (!is_array($lesson)) {
                continue;
            }
            $lessonTitle = trim((string) ($lesson['title'] ?? ''));
            if ($lessonTitle === '') {
                continue;
            }

            $insertLesson->execute([
                $courseId,
                $lessonTitle,
                (string) ($lesson['type'] ?? 'article'),
                (string) ($lesson['content'] ?? ''),
                (string) ($lesson['video_url'] ?? ''),
                (string) ($lesson['materials_title'] ?? ''),
                (string) ($lesson['materials_url'] ?? ''),
                $order++,
            ]);
            $lessonId = (int) $pdo->lastInsertId();

            if (($lesson['type'] ?? '') === 'quiz') {
                tfSeedPackUpsertLessonQuiz($pdo, $lessonId, (array) ($lesson['questions'] ?? []));
            }

            $practice = (array) ($lesson['practice'] ?? []);
            $lang = (string) ($practice['language'] ?? '');
            if (!in_array($lang, ['cpp', 'python', 'c', 'csharp', 'java', 'js', 'mysql', 'pgsql', 'fill'], true)) {
                continue;
            }
            $tests = $practice['tests'] ?? [];
            if (!is_array($tests) || empty($tests)) {
                continue;
            }
            $testsJson = tfSafeJson($tests, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($testsJson === false) {
                $testsJson = '[]';
            }
            $insertPractice->execute([
                $lessonId,
                $lang,
                (string) ($practice['title'] ?? ''),
                (string) ($practice['prompt'] ?? ''),
                (string) ($practice['starter_code'] ?? ''),
                $testsJson,
            ]);
        }

        $exam = (array) ($courseDef['exam'] ?? []);
        $questions = (array) ($exam['questions'] ?? []);
        if (!empty($questions)) {
            $examJson = tfSafeJson($questions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($examJson === false) {
                $examJson = '[]';
            }
            $insertExam = $pdo->prepare("
                INSERT INTO course_exams (course_id, exam_json, time_limit_minutes, pass_percent, shuffle_questions, shuffle_options, created_at)
                VALUES (?, ?, ?, ?, TRUE, TRUE, NOW())
            ");
            $insertExam->execute([
                $courseId,
                $examJson,
                max(20, min(180, (int) ($exam['time_limit_minutes'] ?? 60))),
                max(40, min(100, (int) ($exam['pass_percent'] ?? 70))),
            ]);
        }

        $pdo->commit();
        return ['ok' => true, 'created' => $created, 'course_id' => $courseId, 'type' => 'course', 'title' => $title];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['ok' => false, 'error' => 'exception', 'message' => 'Ошибка запроса.', 'type' => 'course', 'title' => $title];
    }
}

function tfSeedCoursesRich(PDO $pdo): array
{
    $specs = tfSeedPackCourseSpecsRich();
    $blueprints = tfSeedPackTrackBlueprintsRich();
    $result = [];

    foreach ($specs as $spec) {
        $title = (string) ($spec['title'] ?? '');
        $track = (string) ($spec['track'] ?? '');
        $blueprint = (array) ($blueprints[$track] ?? []);
        $modules = tfSeedPackModulesForCourseRich($blueprint, $title);
        $keywords = (array) ($blueprint['keywords'] ?? []);
        $summary = (string) ($blueprint['summary'] ?? 'Практический курс с упором на реальные задачи.');

        $courseDef = [
            'title' => $title,
            'instructor' => (string) ($spec['instructor'] ?? 'ITsphere360 Academy'),
            'description' => $summary . ' Курс включает ' . count($modules) . ' модулей, практику и финальный экзамен.',
            'category' => (string) ($spec['category'] ?? 'other'),
            'image_url' => 'https://placehold.co/600x400/1f2937/ffffff?text=' . rawurlencode($title),
            'skills' => (array) ($spec['skills'] ?? []),
            'lessons' => tfSeedPackBuildCourseLessonsRich($title, $track, (string) ($spec['practice'] ?? '')),
            'exam' => [
                'time_limit_minutes' => 70,
                'pass_percent' => 70,
                'questions' => tfSeedPackBuildCourseExamRich($title, $modules, $keywords),
            ],
        ];

        $result[] = tfUpsertCourseRich($pdo, $courseDef);
    }

    return $result;
}

function tfSeedPackBuildRoadmapMaterialsRich(string $track, string $courseTitle, string $topic, array $resources): array
{
    $roadmapUrl = tfSeedPackRoadmapPathByTrackRich($track);
    $docUrl = (string) ($resources[0] ?? 'https://roadmap.sh/');
    $practiceUrl = (string) ($resources[1] ?? 'https://roadmap.sh/');
    $youtubeUrl = tfSeedPackBuildYouTubeRuResultsUrl($courseTitle, $topic, 1);

    return [
        ['title' => "Официальная документация по теме «{$topic}»", 'url' => $docUrl],
        ['title' => "Практический гайд: {$topic}", 'url' => $practiceUrl],
        ['title' => "Roadmap и материалы: {$topic}", 'url' => $roadmapUrl],
        ['title' => "Видео-разбор: {$topic}", 'url' => $youtubeUrl],
    ];
}

function tfSeedPackBuildRoadmapMiniQuizRich(string $roadmapTitle, string $topic, array $allTopics, int $index): array
{
    $topics = array_values(array_unique(array_filter(array_map('trim', $allTopics))));
    while (count($topics) < 4) {
        $topics[] = 'Дополнительная тема ' . (count($topics) + 1);
    }
    $size = count($topics);
    $correct = $topic;
    $alt1 = $topics[($index + 1) % $size];
    $alt2 = $topics[($index + 2) % $size];
    $alt3 = $topics[($index + 3) % $size];

    $options = array_values(array_unique([$correct, $alt1, $alt2, $alt3]));
    $options = tfSeedPackShuffleOptions($options, $roadmapTitle . "|" . $topic . "|" . $index);
    if (count($options) > 1) {
        $shift = $index % count($options);
        $options = array_merge(array_slice($options, $shift), array_slice($options, 0, $shift));
    }

    $questions = [
        [
            'question' => "Какой модуль в роадмапе «{$roadmapTitle}» покрывает тему «{$topic}»?",
            'options' => $options,
            'correct_answer' => $correct,
        ],
        [
            'question' => "Что является ожидаемым результатом после модуля «{$topic}»?",
            'options' => [
                'Уметь применять тему в рабочем кейсе',
                'Запомнить только термины без практики',
                'Пропустить задания и перейти дальше',
                'Изучить нерелевантные темы',
            ],
            'correct_answer' => 'Уметь применять тему в рабочем кейсе',
        ],
        [
            'question' => "С какого источника лучше начать изучение «{$topic}»?",
            'options' => [
                'Официальная документация и проверенные гайды',
                'Случайные комментарии без примеров',
                'Устаревшие заметки без обновлений',
                'Только короткие посты без кода',
            ],
            'correct_answer' => 'Официальная документация и проверенные гайды',
        ],
        [
            'question' => "Тема «{$topic}» влияет на итоговый результат роадмапа «{$roadmapTitle}».",
            'options' => ['Верно', 'Неверно'],
            'correct_answer' => 'Верно',
        ],
    ];
    $prev = $topics[($index - 1 + $size) % $size];
    $next = $topics[($index + 1) % $size];
    $questions = array_merge($questions, [
        [
            "question" => "Which module should be reviewed before \"{$topic}\" in \"{$roadmapTitle}\"?",
            "options" => tfSeedPackShuffleOptions([$prev, $topic, $next, $topics[($index + 2) % $size]], $roadmapTitle . "|rev|" . $index),
            "correct_answer" => $prev,
        ],
        [
            "question" => "What is the best next focus after \"{$topic}\"?",
            "options" => tfSeedPackShuffleOptions([$next, $prev, $topics[($index + 2) % $size], $topics[($index + 3) % $size]], $roadmapTitle . "|next|" . $index),
            "correct_answer" => $next,
        ],
        [
            "question" => "The topic \"{$topic}\" is optional for mastering \"{$roadmapTitle}\".",
            "options" => ["True", "False"],
            "correct_answer" => "False",
        ],
    ]);
    return $questions;
}

function tfSeedPackBuildRoadmapFinalQuizRich(string $roadmapTitle, array $allTopics, int $count = 24): array
{
    $topics = array_values(array_unique(array_filter(array_map('trim', $allTopics))));
    if (empty($topics)) {
        $topics = ['Основной модуль'];
    }
    while (count($topics) < 4) {
        $topics[] = 'Дополнительный модуль ' . (count($topics) + 1);
    }
    $size = count($topics);
    $questions = [];

    for ($i = 0; $i < $count; $i++) {
        $correct = $topics[$i % $size];
        $distractors = [];
        $cursor = 1;
        while (count($distractors) < 3 && $cursor < ($size + 8)) {
            $candidate = $topics[($i + $cursor * 2) % $size];
            if ($candidate !== $correct && !in_array($candidate, $distractors, true)) {
                $distractors[] = $candidate;
            }
            $cursor++;
        }
        $options = array_slice(array_values(array_unique(array_merge([$correct], $distractors))), 0, 4);
        if (count($options) > 1) {
            $shift = $i % count($options);
            $options = array_merge(array_slice($options, $shift), array_slice($options, 0, $shift));
        }
        $options = tfSeedPackShuffleOptions($options, $roadmapTitle . "|" . $correct . "|" . $i);
        $templates = [
            "Финальный экзамен «{$roadmapTitle}»: какой модуль отвечает за тему «{$correct}»?",
            "Какой раздел нужно выбрать для проверки навыка «{$correct}»?",
            "Какой модуль в «{$roadmapTitle}» является ключевым для результата по теме «{$correct}»?",
            "Где в программе «{$roadmapTitle}» закрепляется тема «{$correct}»?",
        ];
        $templates = array_merge($templates, [
            "Where in \"{$roadmapTitle}\" would you validate \"{$correct}\"?",
            "Which block is most critical for \"{$correct}\" mastery?",
            "Select the checkpoint that best represents \"{$correct}\".",
            "Which module should be prioritized if \"{$correct}\" is weak?",
        ]);
        $questions[] = [
            'question' => $templates[$i % count($templates)],
            'options' => $options,
            'correct_answer' => $correct,
        ];
    }

    return $questions;
}

function tfSeedRoadmapsRich(PDO $pdo): array
{
    ensureRoadmapTables($pdo);
    $specs = tfSeedPackCourseSpecsRich();
    $blueprints = tfSeedPackTrackBlueprintsRich();
    $created = [];

    foreach ($specs as $spec) {
        $title = (string) ($spec['title'] ?? '');
        $track = (string) ($spec['track'] ?? '');
        $blueprint = (array) ($blueprints[$track] ?? []);
        $modules = tfSeedPackModulesForCourseRich($blueprint, $title);
        if (empty($modules)) {
            continue;
        }
        $resources = array_values((array) ($blueprint['resources'] ?? []));
        if (empty($resources)) {
            $resources = ['https://roadmap.sh/'];
        }

        $roadmapTitle = 'Roadmap: ' . $title;
        $description = "Практический роадмап по {$title}: " . count($modules) . " модулей, мини-тесты и финальный экзамен.";

        $stmt = $pdo->prepare("SELECT id FROM roadmap_list WHERE title = ? LIMIT 1");
        $stmt->execute([$roadmapTitle]);
        $roadmapId = (int) ($stmt->fetchColumn() ?: 0);
        if ($roadmapId > 0) {
            $upd = $pdo->prepare("UPDATE roadmap_list SET description = ? WHERE id = ?");
            $upd->execute([$description, $roadmapId]);
        } else {
            $ins = $pdo->prepare("INSERT INTO roadmap_list (title, description) VALUES (?, ?)");
            $ins->execute([$roadmapTitle, $description]);
            $roadmapId = (int) $pdo->lastInsertId();
        }

        $pdo->prepare("DELETE FROM roadmap_nodes WHERE roadmap_title = ?")->execute([$roadmapTitle]);

        $topics = $modules;
        $topics[] = 'Итоговый экзамен и capstone';
        $nodeIds = [];
        $cols = 4;
        $xBase = 80;
        $yBase = 80;
        $xStep = 400;
        $yStep = 250;

        foreach ($topics as $idx => $topic) {
            $row = (int) floor($idx / $cols);
            $col = $idx % $cols;
            if ($row % 2 === 1) {
                $col = ($cols - 1) - $col;
            }
            $x = $xBase + $col * $xStep;
            $y = $yBase + $row * $yStep + (($col % 2) * 20);
            $isExam = ($idx === count($topics) - 1) ? 1 : 0;
            $materials = tfSeedPackBuildRoadmapMaterialsRich($track, $title, $topic, $resources);

            $insNode = $pdo->prepare("
                INSERT INTO roadmap_nodes (title, roadmap_title, topic, materials, x, y, deps, is_exam)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insNode->execute([
                $topic,
                $roadmapTitle,
                $topic,
                tfSafeJson($materials, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                $x,
                $y,
                tfSafeJson([], JSON_UNESCAPED_UNICODE),
                $isExam
            ]);
            $nodeIds[] = (int) $pdo->lastInsertId();
        }

        foreach ($nodeIds as $idx => $nodeId) {
            if ($idx > 0) {
                $deps = tfSafeJson([$nodeIds[$idx - 1]], JSON_UNESCAPED_UNICODE);
                $updDeps = $pdo->prepare("UPDATE roadmap_nodes SET deps = ? WHERE id = ?");
                $updDeps->execute([$deps, $nodeId]);
            }

            $topic = $topics[$idx] ?? ('Узел ' . ($idx + 1));
            $isExam = ($idx === count($topics) - 1);
            $materials = tfSeedPackBuildRoadmapMaterialsRich($track, $title, $topic, $resources);

            $insLesson = $pdo->prepare("
                INSERT INTO roadmap_lessons (node_id, title, video_url, description, materials, order_index)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $insLesson->execute([
                $nodeId,
                'Шаг ' . ($idx + 1) . '. ' . $topic,
                tfSeedPackBuildYouTubeRuSearchUrl($title, $topic, $idx + 1),
                $isExam
                    ? "Финальный этап роадмапа «{$roadmapTitle}». Повторите ключевые темы и пройдите итоговый экзамен."
                    : "Практический шаг по теме «{$topic}». Изучите материалы и подтвердите понимание через мини-тест.",
                tfSafeJson($materials, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                1
            ]);

            $quiz = $isExam
                ? tfSeedPackBuildRoadmapFinalQuizRich($roadmapTitle, $modules, 24)
                : tfSeedPackBuildRoadmapMiniQuizRich($roadmapTitle, $topic, $modules, $idx);

            $insQ = $pdo->prepare("INSERT INTO roadmap_quiz_questions (node_id, question, options, correct_answer) VALUES (?, ?, ?, ?)");
            foreach ($quiz as $q) {
                $insQ->execute([
                    $nodeId,
                    (string) ($q['question'] ?? ''),
                    tfSafeJson(array_values((array) ($q['options'] ?? [])), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    (string) ($q['correct_answer'] ?? ''),
                ]);
            }
        }

        $created[] = ['ok' => true, 'created' => true, 'type' => 'roadmap', 'id' => $roadmapId, 'title' => $roadmapTitle];
    }

    return $created;
}

function tfSeedLearningPackRich(PDO $pdo)
{
    ensureCoursesSchema($pdo);
    ensureLessonsSchema($pdo);
    ensurePracticeSchema($pdo);
    ensureContestsSchema($pdo);
    ensureRoadmapTables($pdo);
    if (function_exists('ensureCourseExamsSchema')) {
        ensureCourseExamsSchema($pdo);
    }

    $courses = tfSeedCoursesRich($pdo);
    $roadmaps = tfSeedRoadmapsRich($pdo);
    $contests = tfSeedContestsRich($pdo, true);

    return array_merge($courses, $roadmaps, $contests);
}

