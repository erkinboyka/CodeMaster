<?php

/**
 * ============================================================
 *  Learning Pack Seeder — Quality Edition
 *  20 курсов · 400 уроков (20/курс) · практика · экзамены · роадмапы
 * ============================================================
 */

// ---------------------------------------------------------------------------
//  HELPERS
// ---------------------------------------------------------------------------

function lp_json(mixed $v): string
{
    return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]';
}

function lp_slug(string $title): string
{
    $t = mb_strtolower(trim($title), 'UTF-8');
    $t = preg_replace('/\s+/', '-', $t);
    $t = preg_replace('/[^a-z0-9\-]/', '', transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $t) ?: $t);
    return preg_replace('/-+/', '-', $t) ?: 'course-' . rand(1000, 9999);
}

// ---------------------------------------------------------------------------
//  SCHEMA HELPERS  (создают таблицы, если не существуют)
// ---------------------------------------------------------------------------

function lp_ensure_schema(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS courses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL UNIQUE,
            instructor VARCHAR(200),
            description TEXT,
            category VARCHAR(60),
            image_url VARCHAR(500),
            created_at DATETIME DEFAULT NOW(),
            updated_at DATETIME DEFAULT NOW() ON UPDATE NOW()
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS course_skills (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id INT NOT NULL,
            skill_name VARCHAR(100) NOT NULL,
            skill_level TINYINT DEFAULT 0
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS lessons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id INT NOT NULL,
            title VARCHAR(300) NOT NULL,
            type ENUM('article','video','quiz') DEFAULT 'article',
            content TEXT,
            video_url VARCHAR(500),
            materials_title VARCHAR(200),
            materials_url VARCHAR(500),
            order_num INT DEFAULT 0,
            created_at DATETIME DEFAULT NOW()
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS lesson_practice_tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            lesson_id INT NOT NULL,
            language VARCHAR(20) NOT NULL,
            title VARCHAR(200),
            prompt TEXT,
            starter_code TEXT,
            tests_json LONGTEXT,
            is_required TINYINT DEFAULT 1
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS course_exams (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id INT NOT NULL UNIQUE,
            exam_json LONGTEXT,
            time_limit_minutes INT DEFAULT 60,
            pass_percent INT DEFAULT 70,
            shuffle_questions TINYINT DEFAULT 1,
            shuffle_options TINYINT DEFAULT 1,
            created_at DATETIME DEFAULT NOW()
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS roadmap_list (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL UNIQUE,
            description TEXT,
            created_at DATETIME DEFAULT NOW()
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS roadmap_nodes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            roadmap_title VARCHAR(200) NOT NULL,
            title VARCHAR(200),
            topic VARCHAR(200),
            materials LONGTEXT,
            x INT DEFAULT 0,
            y INT DEFAULT 0,
            deps LONGTEXT,
            is_exam TINYINT DEFAULT 0
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS roadmap_lessons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            node_id INT NOT NULL,
            title VARCHAR(300),
            video_url VARCHAR(500),
            description TEXT,
            materials LONGTEXT,
            order_index INT DEFAULT 1
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS roadmap_quiz_questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            node_id INT NOT NULL,
            question TEXT,
            options LONGTEXT,
            correct_answer TEXT
        )
    ");
}

// ---------------------------------------------------------------------------
//  STARTER CODE
// ---------------------------------------------------------------------------

function lp_starter(string $lang): string
{
    return match ($lang) {
        'cpp'    => "#include <bits/stdc++.h>\nusing namespace std;\nint main(){\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n    // ваш код здесь\n    return 0;\n}\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\ndef solve():\n    # ваш код здесь\n    pass\n\nif __name__ == '__main__':\n    solve()\n",
        'java'   => "import java.util.*;\npublic class Main {\n    public static void main(String[] args) {\n        Scanner sc = new Scanner(System.in);\n        // ваш код здесь\n    }\n}\n",
        'js'     => "const lines = require('fs').readFileSync('/dev/stdin','utf8').trim().split('\\n');\nlet idx = 0;\nconst rd = () => lines[idx++];\n// ваш код здесь\n",
        'mysql', 'pgsql' => "-- Напишите SQL-запрос здесь\nSELECT 1;\n",
        default  => "// ваш код здесь\n",
    };
}

// ---------------------------------------------------------------------------
//  CATALOG: 20 COURSES
// ---------------------------------------------------------------------------

/**
 * Возвращает полное описание 20 курсов: уроки, практика, экзамен.
 */
function lp_courses_catalog(): array
{
    return [

        // ------------------------------------------------------------------ 1
        [
            'title'       => 'Python: с нуля до Junior',
            'instructor'  => 'Алина Громова · Senior Python Engineer',
            'category'    => 'backend',
            'description' => 'Полный курс Python для начинающих: синтаксис, ООП, работа с файлами, модули и мини-проекты.',
            'skills'      => ['Python', 'ООП', 'Файлы', 'Алгоритмы', 'pip'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/3b82f6/ffffff?text=Python',
            'lessons'     => [
                ['title' => 'Установка Python и первый скрипт', 'theory' => 'Установка интерпретатора, hello world, запуск скриптов из терминала. REPL и IDE выбор.', 'practice_title' => 'Hello World', 'practice_prompt' => 'Прочитайте имя со stdin и выведите: Hello, {имя}!', 'tests' => [['stdin'=>"Alice\n",'expected'=>"Hello, Alice!"],['stdin'=>"World\n",'expected'=>"Hello, World!"]]],
                ['title' => 'Переменные и типы данных', 'theory' => 'int, float, str, bool. type(), isinstance(). Динамическая типизация.', 'practice_title' => 'Тип числа', 'practice_prompt' => 'Прочитайте число. Выведите int, если оно целое, иначе float.', 'tests' => [['stdin'=>"42\n",'expected'=>"int"],['stdin'=>"3.14\n",'expected'=>"float"]]],
                ['title' => 'Арифметика и операторы', 'theory' => 'Операторы +,-,*,/,//,%,**. Приоритет. Преобразование типов.', 'practice_title' => 'Деление с остатком', 'practice_prompt' => 'Прочитайте A и B. Выведите целую часть и остаток от деления A на B.', 'tests' => [['stdin'=>"17 5\n",'expected'=>"3 2"],['stdin'=>"100 7\n",'expected'=>"14 2"]]],
                ['title' => 'Условный оператор if/elif/else', 'theory' => 'Синтаксис условий, вложенность, тернарный оператор. Логические and/or/not.', 'practice_title' => 'FizzBuzz', 'practice_prompt' => 'Прочитайте число N. Если делится на 3 → Fizz, на 5 → Buzz, на оба → FizzBuzz, иначе само число.', 'tests' => [['stdin'=>"15\n",'expected'=>"FizzBuzz"],['stdin'=>"7\n",'expected'=>"7"]]],
                ['title' => 'Цикл while', 'theory' => 'Синтаксис while, break, continue. Бесконечные циклы и их завершение.', 'practice_title' => 'Сумма до N', 'practice_prompt' => 'Прочитайте N. Выведите сумму чисел от 1 до N включительно.', 'tests' => [['stdin'=>"10\n",'expected'=>"55"],['stdin'=>"1\n",'expected'=>"1"]]],
                ['title' => 'Цикл for и range', 'theory' => 'for x in iterable. range(start, stop, step). enumerate, zip.', 'practice_title' => 'Таблица умножения', 'practice_prompt' => 'Прочитайте N. Выведите таблицу умножения N (1-10), формат: N x i = result.', 'tests' => [['stdin'=>"2\n",'expected'=>"2 x 1 = 2\n2 x 2 = 4\n2 x 3 = 6\n2 x 4 = 8\n2 x 5 = 10\n2 x 6 = 12\n2 x 7 = 14\n2 x 8 = 16\n2 x 9 = 18\n2 x 10 = 20"],['stdin'=>"3\n",'expected'=>"3 x 1 = 3\n3 x 2 = 6\n3 x 3 = 9\n3 x 4 = 12\n3 x 5 = 15\n3 x 6 = 18\n3 x 7 = 21\n3 x 8 = 24\n3 x 9 = 27\n3 x 10 = 30"]]],
                ['title' => 'Строки: методы и форматирование', 'theory' => 'Индексирование, срезы, upper/lower/strip/split/join. f-строки.', 'practice_title' => 'Палиндром', 'practice_prompt' => 'Прочитайте строку. YES если палиндром (без учёта регистра), иначе NO.', 'tests' => [['stdin'=>"Racecar\n",'expected'=>"YES"],['stdin'=>"hello\n",'expected'=>"NO"]]],
                ['title' => 'Списки и операции с ними', 'theory' => 'Создание, индексирование, append/extend/pop/remove/sort/reverse. List comprehension.', 'practice_title' => 'Уникальные элементы', 'practice_prompt' => 'Прочитайте N, затем N чисел. Выведите уникальные в порядке первого появления.', 'tests' => [['stdin'=>"6\n1 2 2 3 1 4\n",'expected'=>"1 2 3 4"],['stdin'=>"4\n5 5 5 5\n",'expected'=>"5"]]],
                ['title' => 'Кортежи и множества', 'theory' => 'tuple — неизменяемые последовательности. set — уникальность, операции пересечения/объединения.', 'practice_title' => 'Пересечение множеств', 'practice_prompt' => 'Прочитайте два числа N и M, затем N и M чисел. Выведите их пересечение (sorted).', 'tests' => [['stdin'=>"3 4\n1 2 3\n2 3 4 5\n",'expected'=>"2 3"],['stdin'=>"2 2\n10 20\n30 40\n",'expected'=>""]]],
                ['title' => 'Словари', 'theory' => 'dict: ключи/значения, get, setdefault, items/keys/values. Dict comprehension.', 'practice_title' => 'Частота слов', 'practice_prompt' => 'Прочитайте строку. Подсчитайте частоту каждого слова. Выведите пары word:count в алфавитном порядке.', 'tests' => [['stdin'=>"apple banana apple\n",'expected'=>"apple:2\nbanana:1"],['stdin'=>"a a a\n",'expected'=>"a:3"]]],
                ['title' => 'Функции и аргументы', 'theory' => 'def, return, positional/keyword/default args, *args, **kwargs. Docstrings.', 'practice_title' => 'Факториал', 'practice_prompt' => 'Прочитайте N (0..20). Выведите N!.', 'tests' => [['stdin'=>"0\n",'expected'=>"1"],['stdin'=>"10\n",'expected'=>"3628800"]]],
                ['title' => 'Рекурсия', 'theory' => 'Базовый случай и шаг рекурсии. Стек вызовов. Хвостовая рекурсия. Примеры: Фибоначчи, Ханойские башни.', 'practice_title' => 'Fibonacci', 'practice_prompt' => 'Прочитайте N. Выведите N-е число Фибоначчи (F(0)=0, F(1)=1).', 'tests' => [['stdin'=>"10\n",'expected'=>"55"],['stdin'=>"0\n",'expected'=>"0"]]],
                ['title' => 'ООП: классы и объекты', 'theory' => '__init__, self, атрибуты экземпляра и класса. Методы. __str__, __repr__.', 'practice_title' => 'Класс Rectangle', 'practice_prompt' => "Прочитайте w и h. Создайте Rectangle. Выведите через пробел: площадь и периметр.", 'tests' => [['stdin'=>"4 5\n",'expected'=>"20 18"],['stdin'=>"10 3\n",'expected'=>"30 26"]]],
                ['title' => 'Наследование и полиморфизм', 'theory' => 'super(), переопределение методов. Множественное наследование. isinstance/issubclass.', 'practice_title' => 'Фигуры', 'practice_prompt' => "Прочитайте circle r или rect w h. Выведите площадь с 2 знаками.", 'tests' => [['stdin'=>"circle 5\n",'expected'=>"78.54"],['stdin'=>"rect 4 6\n",'expected'=>"24.00"]]],
                ['title' => 'Исключения', 'theory' => 'try/except/else/finally. raise. Иерархия исключений. Создание своих.', 'practice_title' => 'Безопасное деление', 'practice_prompt' => 'Прочитайте A и B. Выведите A/B с 4 знаками или ERROR если B=0.', 'tests' => [['stdin'=>"10 4\n",'expected'=>"2.5000"],['stdin'=>"5 0\n",'expected'=>"ERROR"]]],
                ['title' => 'Работа с файлами', 'theory' => 'open, read/write, with. Режимы r/w/a/rb. Построчное чтение. os.path.', 'practice_title' => 'Подсчёт строк', 'practice_prompt' => 'Прочитайте со stdin N строк, затем . (стоп). Выведите количество прочитанных строк.', 'tests' => [['stdin'=>"foo\nbar\nbaz\n.\n",'expected'=>"3"],['stdin'=>"only\n.\n",'expected'=>"1"]]],
                ['title' => 'Модули и пакеты', 'theory' => 'import, from...import, __name__. Стандартные модули: math, random, datetime, collections.', 'practice_title' => 'Статистика', 'practice_prompt' => 'Прочитайте N чисел. Выведите: min max mean (mean с 2 знаками).', 'tests' => [['stdin'=>"5\n3 1 4 1 5\n",'expected'=>"1 5 2.80"],['stdin'=>"3\n10 20 30\n",'expected'=>"10 30 20.00"]]],
                ['title' => 'Итераторы и генераторы', 'theory' => 'iter/next. yield. Генераторные выражения. Ленивые вычисления.', 'practice_title' => 'Чётные квадраты', 'practice_prompt' => 'Прочитайте N. Выведите квадраты чётных чисел от 1 до N через пробел.', 'tests' => [['stdin'=>"10\n",'expected'=>"4 16 36 64 100"],['stdin'=>"6\n",'expected'=>"4 16 36"]]],
                ['title' => 'Декораторы', 'theory' => 'Функции высшего порядка. @decorator синтаксис. functools.wraps. Примеры: timer, logger, cache.', 'practice_title' => 'Декоратор логирования', 'practice_prompt' => 'Прочитайте N вызовов: функция args. Выведите CALL func(args) для каждого.', 'tests' => [['stdin'=>"2\nadd 2 3\nsub 10 4\n",'expected'=>"CALL add(2, 3)\nCALL sub(10, 4)"],['stdin'=>"1\nmul 3 4\n",'expected'=>"CALL mul(3, 4)"]]],
                ['title' => 'Мини-проект: Task Manager CLI', 'theory' => 'Сборка знаний: ООП, файлы, модули. CLI-приложение управления задачами.', 'practice_title' => 'Приоритетная очередь', 'practice_prompt' => "Обработайте команды:\nADD name priority — добавить задачу\nDONE — извлечь задачу с наибольшим приоритетом (при равенстве — лексически меньшую)\nEMPTY если очередь пуста.", 'tests' => [['stdin'=>"4\nADD deploy 3\nADD test 1\nADD review 3\nDONE\n",'expected'=>"deploy"],['stdin'=>"3\nADD a 5\nDONE\nDONE\n",'expected'=>"a\nEMPTY"]]],
            ],
            'exam_questions' => lp_exam_python(),
        ],

        // ------------------------------------------------------------------ 2
        [
            'title'       => 'JavaScript: DOM и современный JS',
            'instructor'  => 'Александр Белов · Senior JS Engineer',
            'category'    => 'frontend',
            'description' => 'Современный JavaScript (ES2020+): типы данных, функции, ООП, работа с DOM, Fetch API, async/await.',
            'skills'      => ['JavaScript', 'DOM', 'Async/Await', 'Fetch', 'ES2020+'],
            'lang'        => 'js',
            'image'       => 'https://placehold.co/600x400/f59e0b/ffffff?text=JavaScript',
            'lessons'     => [
                ['title' => 'Введение в JS и среда выполнения', 'theory' => 'Браузер vs Node.js. V8 engine. Скрипт в HTML.', 'practice_title' => 'Конкатенация', 'practice_prompt' => 'Прочитайте A и B. Выведите их сумму (числа).', 'tests' => [['stdin'=>"5 3\n",'expected'=>"8"],['stdin'=>"-2 10\n",'expected'=>"8"]]],
                ['title' => 'Переменные: var, let, const', 'theory' => 'Область видимости, хоистинг, TDZ. Когда что использовать.', 'practice_title' => 'Swap', 'practice_prompt' => 'Прочитайте A и B. Выведите B и A через пробел (деструктуризация).', 'tests' => [['stdin'=>"10 20\n",'expected'=>"20 10"],['stdin'=>"abc xyz\n",'expected'=>"xyz abc"]]],
                ['title' => 'Типы данных и приведение', 'theory' => 'Primitive vs Reference. typeof. Явное и неявное приведение. ==  vs ===.', 'practice_title' => 'Тип значения', 'practice_prompt' => 'Прочитайте строку. Если это число — number, если true/false — boolean, иначе — string.', 'tests' => [['stdin'=>"42\n",'expected'=>"number"],['stdin'=>"true\n",'expected'=>"boolean"],['stdin'=>"hello\n",'expected'=>"string"]]],
                ['title' => 'Функции и стрелочные функции', 'theory' => 'function declaration vs expression. Arrow functions, this, arguments.', 'practice_title' => 'Степень числа', 'practice_prompt' => 'Прочитайте base и exp (exp>=0). Выведите base^exp без Math.pow.', 'tests' => [['stdin'=>"2 10\n",'expected'=>"1024"],['stdin'=>"3 0\n",'expected'=>"1"]]],
                ['title' => 'Массивы и методы массивов', 'theory' => 'map, filter, reduce, find, some, every, flat, flatMap. Иммутабельность.', 'practice_title' => 'Сумма чётных', 'practice_prompt' => 'Прочитайте N чисел через пробел. Выведите сумму чётных.', 'tests' => [['stdin'=>"1 2 3 4 5 6\n",'expected'=>"12"],['stdin'=>"1 3 5\n",'expected'=>"0"]]],
                ['title' => 'Объекты и деструктуризация', 'theory' => 'Object literals, spread/rest, деструктуризация, optional chaining, nullish coalescing.', 'practice_title' => 'Подсчёт свойств', 'practice_prompt' => 'Прочитайте JSON-объект. Выведите количество ключей верхнего уровня.', 'tests' => [['stdin'=>'{"a":1,"b":2,"c":3}\n','expected'=>"3"],['stdin'=>'{}\n','expected'=>"0"]]],
                ['title' => 'Замыкания и область видимости', 'theory' => 'Лексическое окружение. Замыкания. IIFE. Паттерн модуль.', 'practice_title' => 'Счётчик', 'practice_prompt' => "Обработайте команды:\nINC — увеличить счётчик\nDEC — уменьшить\nGET — вывести текущее значение", 'tests' => [['stdin'=>"INC\nINC\nINC\nDEC\nGET\n",'expected'=>"2"],['stdin'=>"DEC\nGET\n",'expected'=>"-1"]]],
                ['title' => 'Прототипы и классы', 'theory' => 'Prototype chain. class, constructor, extends, super, static. Геттеры и сеттеры.', 'practice_title' => 'Стек', 'practice_prompt' => "Обработайте:\nPUSH x — добавить\nPOP — вывести и убрать верхний, EMPTY если пустой\nPEEK — показать без удаления", 'tests' => [['stdin'=>"PUSH 5\nPUSH 10\nPEEK\nPOP\nPOP\nPOP\n",'expected'=>"10\n10\n5\nEMPTY"],['stdin'=>"POP\n",'expected'=>"EMPTY"]]],
                ['title' => 'Промисы', 'theory' => 'Promise: resolve/reject. then/catch/finally. Promise.all, Promise.race, Promise.allSettled.', 'practice_title' => 'Цепочка трансформаций', 'practice_prompt' => 'Прочитайте число. Прибавьте 10, умножьте на 2, вычтите 5. Выведите результат.', 'tests' => [['stdin'=>"5\n",'expected'=>"25"],['stdin'=>"0\n",'expected'=>"15"]]],
                ['title' => 'Async/Await', 'theory' => 'async функции, await, обработка ошибок через try/catch. Параллельный запуск.', 'practice_title' => 'Последовательная обработка', 'practice_prompt' => 'Прочитайте N чисел. Выведите их квадраты в том же порядке.', 'tests' => [['stdin'=>"3\n2 3 4\n",'expected'=>"4\n9\n16"],['stdin'=>"1\n7\n",'expected'=>"49"]]],
                ['title' => 'Fetch API и работа с JSON', 'theory' => 'fetch(), методы HTTP, заголовки, Response.json(). Обработка ошибок статусов.', 'practice_title' => 'Парсинг JSON', 'practice_prompt' => 'Прочитайте JSON-массив объектов. Выведите поле name каждого.', 'tests' => [['stdin'=>'[{"name":"Alice"},{"name":"Bob"}]\n','expected'=>"Alice\nBob"],['stdin'=>'[{"name":"X"}]\n','expected'=>"X"]]],
                ['title' => 'Обработка событий', 'theory' => 'addEventListener, event object, bubbling/capturing, preventDefault, stopPropagation.', 'practice_title' => 'Фильтр событий', 'practice_prompt' => "Прочитайте N событий: click|keydown|mouseover. Выведите только click.", 'tests' => [['stdin'=>"4\nclick\nkeydown\nclick\nmouseover\n",'expected'=>"click\nclick"],['stdin'=>"2\nkeydown\nmouseover\n",'expected'=>""]]],
                ['title' => 'Работа с DOM', 'theory' => 'querySelector, createElement, appendChild, innerHTML vs textContent. Атрибуты.', 'practice_title' => 'HTML-структура', 'practice_prompt' => 'Прочитайте N тегов. Выведите вложенную HTML-строку (каждый следующий вложен в предыдущий).', 'tests' => [['stdin'=>"2\ndiv\nspan\n",'expected'=>"<div><span></span></div>"],['stdin'=>"1\np\n",'expected'=>"<p></p>"]]],
                ['title' => 'Модули ES6', 'theory' => 'import/export, default export, named export, динамический import(). Bundlers.', 'practice_title' => 'Агрегатор модулей', 'practice_prompt' => 'Прочитайте список имён через запятую. Выведите их в обратном порядке через запятую.', 'tests' => [['stdin'=>"a,b,c\n",'expected'=>"c,b,a"],['stdin'=>"x\n",'expected'=>"x"]]],
                ['title' => 'Работа с датами', 'theory' => 'Date object, getTime, toISOString, форматирование. Intl.DateTimeFormat. date-fns.', 'practice_title' => 'Разница дат', 'practice_prompt' => 'Прочитайте две даты (YYYY-MM-DD). Выведите разницу в днях (абсолютное значение).', 'tests' => [['stdin'=>"2025-01-01\n2025-01-10\n",'expected'=>"9"],['stdin'=>"2024-03-15\n2024-03-15\n",'expected'=>"0"]]],
                ['title' => 'LocalStorage и сессии', 'theory' => 'localStorage, sessionStorage: setItem/getItem/removeItem/clear. JSON сериализация.', 'practice_title' => 'Key-Value хранилище', 'practice_prompt' => "Обработайте команды SET k v / GET k / DEL k.", 'tests' => [['stdin'=>"SET name Alice\nGET name\nDEL name\nGET name\n",'expected'=>"Alice\nNULL"],['stdin'=>"GET x\n",'expected'=>"NULL"]]],
                ['title' => 'Паттерны: Observer, Module', 'theory' => 'Observer/EventEmitter. Module pattern. SOLID применительно к JS. Мемоизация.', 'practice_title' => 'EventEmitter', 'practice_prompt' => "Команды ON event / EMIT event / OFF event. При EMIT вывести FIRED event или NO LISTENERS.", 'tests' => [['stdin'=>"ON click\nEMIT click\nOFF click\nEMIT click\n",'expected'=>"FIRED click\nNO LISTENERS click"],['stdin'=>"EMIT load\n",'expected'=>"NO LISTENERS load"]]],
                ['title' => 'Тестирование: Jest основы', 'theory' => 'Unit тесты, describe/it/expect. Мокирование. TDD подход.', 'practice_title' => 'Assert', 'practice_prompt' => 'Прочитайте expected и actual. PASS если равны (строго), FAIL иначе.', 'tests' => [['stdin'=>"42\n42\n",'expected'=>"PASS"],['stdin'=>"42\n43\n",'expected'=>"FAIL"]]],
                ['title' => 'Производительность: debounce и throttle', 'theory' => 'Debounce: задержка выполнения. Throttle: ограничение частоты. Применение в UI.', 'practice_title' => 'Дедупликация событий', 'practice_prompt' => 'Прочитайте N меток времени (ms) и delay. Выведите те, которые прошли через debounce.', 'tests' => [['stdin'=>"5 100\n0 50 90 200 350\n",'expected'=>"90 350"],['stdin'=>"3 1000\n0 500 1500\n",'expected'=>"500 1500"]]],
                ['title' => 'Мини-проект: SPA без фреймворка', 'theory' => 'Роутинг через hash. Компонентный подход. Рендер через шаблонные строки.', 'practice_title' => 'Hash-роутер', 'practice_prompt' => "Прочитайте N URL'ов. Извлеките hash-часть (после #). Если нет — home.", 'tests' => [['stdin'=>"3\nhttps://app.com/#about\nhttps://app.com/#contact\nhttps://app.com/\n",'expected'=>"about\ncontact\nhome"],['stdin'=>"1\nhttps://x.com/#faq\n",'expected'=>"faq"]]],
            ],
            'exam_questions' => lp_exam_js(),
        ],

        // ------------------------------------------------------------------ 3
        [
            'title'       => 'SQL: запросы и оптимизация',
            'instructor'  => 'Никита Орлов · Data Engineer',
            'category'    => 'backend',
            'description' => 'Практический курс SQL: от базовых SELECT до оконных функций, CTE и оптимизации запросов.',
            'skills'      => ['SQL', 'JOIN', 'CTE', 'Window Functions', 'EXPLAIN'],
            'lang'        => 'mysql',
            'image'       => 'https://placehold.co/600x400/10b981/ffffff?text=SQL',
            'lessons'     => lp_sql_lessons(),
            'exam_questions' => lp_exam_sql(),
        ],

        // ------------------------------------------------------------------ 4
        [
            'title'       => 'C++: алгоритмы и структуры данных',
            'instructor'  => 'Дмитрий Соколов · Systems Engineer',
            'category'    => 'backend',
            'description' => 'C++ для соревнований и интервью: STL, сложность, сортировки, графы, динамическое программирование.',
            'skills'      => ['C++', 'STL', 'Algorithms', 'Graphs', 'DP'],
            'lang'        => 'cpp',
            'image'       => 'https://placehold.co/600x400/6366f1/ffffff?text=C%2B%2B',
            'lessons'     => lp_cpp_lessons(),
            'exam_questions' => lp_exam_cpp(),
        ],

        // ------------------------------------------------------------------ 5
        [
            'title'       => 'PHP: backend от основ до API',
            'instructor'  => 'Марат Юсупов · Backend Architect',
            'category'    => 'backend',
            'description' => 'PHP 8: синтаксис, ООП, PDO, REST API, безопасность, обработка ошибок.',
            'skills'      => ['PHP 8', 'ООП', 'PDO', 'REST API', 'Security'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/8b5cf6/ffffff?text=PHP',
            'lessons'     => lp_generic_lessons('PHP', 'php'),
            'exam_questions' => lp_exam_generic('PHP', ['синтаксис', 'массивы', 'функции', 'PDO', 'сессии', 'ООП', 'трейты', 'исключения', 'REST API', 'авторизация', 'middleware', 'composer', 'тестирование', 'безопасность', 'namespace', 'интерфейсы', 'магические методы', 'генераторы', 'сигналы', 'деплой']),
        ],

        // ------------------------------------------------------------------ 6
        [
            'title'       => 'Laravel: фреймворк для профессионалов',
            'instructor'  => 'Марат Юсупов · Backend Architect',
            'category'    => 'backend',
            'description' => 'Laravel: маршруты, Eloquent, Blade, очереди, тесты, авторизация, API-ресурсы.',
            'skills'      => ['Laravel', 'Eloquent', 'Blade', 'Queues', 'Policies'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/ef4444/ffffff?text=Laravel',
            'lessons'     => lp_generic_lessons('Laravel', 'laravel'),
            'exam_questions' => lp_exam_generic('Laravel', ['маршруты', 'контроллеры', 'middleware', 'Blade', 'миграции', 'Eloquent', 'связи', 'валидация', 'авторизация', 'политики', 'API ресурсы', 'очереди', 'события', 'файлы', 'тестирование', 'сервис-контейнер', 'провайдеры', 'локализация', 'оптимизация', 'деплой']),
        ],

        // ------------------------------------------------------------------ 7
        [
            'title'       => 'Git: профессиональный workflow',
            'instructor'  => 'Антон Захаров · Staff Engineer',
            'category'    => 'devops',
            'description' => 'Git от основ до сложных сценариев: ветвление, rebase, хуки, GitHub Flow, CI интеграция.',
            'skills'      => ['Git', 'GitHub', 'Branching', 'Rebase', 'CI/CD'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/f97316/ffffff?text=Git',
            'lessons'     => lp_generic_lessons('Git', 'git'),
            'exam_questions' => lp_exam_generic('Git', ['init', 'clone', 'add', 'commit', 'log', 'ветки', 'checkout', 'merge', 'rebase', 'stash', 'cherry-pick', 'revert', 'reset', 'remote', 'push/pull', 'fork', 'PR', 'хуки', 'теги', 'workflow']),
        ],

        // ------------------------------------------------------------------ 8
        [
            'title'       => 'Docker и контейнеризация',
            'instructor'  => 'Евгений Климов · DevOps Lead',
            'category'    => 'devops',
            'description' => 'Docker: образы, контейнеры, Dockerfile, Docker Compose, сети, тома, best practices.',
            'skills'      => ['Docker', 'Dockerfile', 'Compose', 'Networking', 'Volumes'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/0ea5e9/ffffff?text=Docker',
            'lessons'     => lp_generic_lessons('Docker', 'docker'),
            'exam_questions' => lp_exam_generic('Docker', ['контейнер', 'образ', 'Dockerfile', 'FROM/RUN/COPY', 'CMD/ENTRYPOINT', 'volumes', 'networks', 'docker run', 'docker build', 'Compose', 'multi-stage', 'registry', 'healthcheck', 'secrets', 'overlay', 'swarm', 'layer cache', 'best practices', '.dockerignore', 'CI/CD']),
        ],

        // ------------------------------------------------------------------ 9
        [
            'title'       => 'Алгоритмы: подготовка к интервью',
            'instructor'  => 'Дмитрий Соколов · Systems Engineer',
            'category'    => 'backend',
            'description' => 'Задачи на массивы, хеш-таблицы, стек, двоичный поиск, деревья, графы и DP в формате LeetCode.',
            'skills'      => ['Algorithms', 'Data Structures', 'Big-O', 'DP', 'Graphs'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/1d4ed8/ffffff?text=Algorithms',
            'lessons'     => lp_algo_lessons(),
            'exam_questions' => lp_exam_algo(),
        ],

        // ------------------------------------------------------------------ 10
        [
            'title'       => 'React: компоненты и хуки',
            'instructor'  => 'Ирина Смирнова · Frontend Lead',
            'category'    => 'frontend',
            'description' => 'React 18: компоненты, хуки, Context, маршрутизация, state management, тестирование.',
            'skills'      => ['React', 'Hooks', 'Context', 'React Router', 'Testing'],
            'lang'        => 'js',
            'image'       => 'https://placehold.co/600x400/06b6d4/ffffff?text=React',
            'lessons'     => lp_generic_lessons('React', 'react'),
            'exam_questions' => lp_exam_generic('React', ['JSX', 'компоненты', 'props', 'state', 'useState', 'useEffect', 'useRef', 'useContext', 'useMemo', 'useCallback', 'React Router', 'Redux', 'Zustand', 'Context', 'порталы', 'рефы', 'жизненный цикл', 'Suspense', 'тестирование', 'оптимизация']),
        ],

        // ------------------------------------------------------------------ 11
        [
            'title'       => 'TypeScript: строгая типизация',
            'instructor'  => 'Александр Белов · Senior JS Engineer',
            'category'    => 'frontend',
            'description' => 'TypeScript: базовые и продвинутые типы, generics, декораторы, tsconfig, интеграция с React.',
            'skills'      => ['TypeScript', 'Generics', 'Interfaces', 'Decorators', 'tsconfig'],
            'lang'        => 'js',
            'image'       => 'https://placehold.co/600x400/2563eb/ffffff?text=TypeScript',
            'lessons'     => lp_generic_lessons('TypeScript', 'typescript'),
            'exam_questions' => lp_exam_generic('TypeScript', ['типы', 'interface', 'type alias', 'union', 'intersection', 'generics', 'enums', 'tuple', 'any/unknown', 'never', 'narrowing', 'type guards', 'decorators', 'namespaces', 'modules', 'tsconfig', 'strict mode', 'utility types', 'mapped types', 'conditional types']),
        ],

        // ------------------------------------------------------------------ 12
        [
            'title'       => 'Linux и командная строка',
            'instructor'  => 'Евгений Климов · DevOps Lead',
            'category'    => 'devops',
            'description' => 'Linux: файловая система, права доступа, процессы, сети, bash-скрипты, systemd, мониторинг.',
            'skills'      => ['Linux', 'Bash', 'Networking', 'Systemd', 'Monitoring'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/374151/ffffff?text=Linux',
            'lessons'     => lp_generic_lessons('Linux', 'linux'),
            'exam_questions' => lp_exam_generic('Linux', ['ФС и пути', 'ls/cd/pwd', 'права chmod', 'пользователи', 'процессы ps', 'kill/signals', 'grep/sed/awk', 'pipes', 'перенаправление', 'bash скрипты', 'переменные', 'условия', 'циклы', 'cron', 'systemd', 'сети ifconfig', 'ss/netstat', 'ssh', 'curl/wget', 'мониторинг']),
        ],

        // ------------------------------------------------------------------ 13
        [
            'title'       => 'PostgreSQL: продвинутые возможности',
            'instructor'  => 'Никита Орлов · Data Engineer',
            'category'    => 'backend',
            'description' => 'PostgreSQL: транзакции, MVCC, индексы, EXPLAIN ANALYZE, CTE, оконные функции, JSON.',
            'skills'      => ['PostgreSQL', 'MVCC', 'Indexes', 'JSON', 'Performance'],
            'lang'        => 'pgsql',
            'image'       => 'https://placehold.co/600x400/1e40af/ffffff?text=PostgreSQL',
            'lessons'     => lp_pgsql_lessons(),
            'exam_questions' => lp_exam_generic('PostgreSQL', ['SELECT', 'WHERE', 'JOIN', 'GROUP BY', 'индексы', 'BTREE', 'GIN', 'EXPLAIN', 'транзакции', 'MVCC', 'CTE', 'оконные', 'JSON', 'JSONB', 'партиции', 'materialized view', 'pg_stat', 'VACUUM', 'ANALYZE', 'репликация']),
        ],

        // ------------------------------------------------------------------ 14
        [
            'title'       => 'MongoDB: документные базы данных',
            'instructor'  => 'Егор Литвинов · Platform Engineer',
            'category'    => 'backend',
            'description' => 'MongoDB: документная модель, CRUD, aggregation pipeline, индексы, репликация, шардирование.',
            'skills'      => ['MongoDB', 'Aggregation', 'Indexes', 'Replication', 'Sharding'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/16a34a/ffffff?text=MongoDB',
            'lessons'     => lp_generic_lessons('MongoDB', 'nosql'),
            'exam_questions' => lp_exam_generic('MongoDB', ['документ', 'коллекция', 'insertOne', 'find', 'updateOne', 'deleteOne', 'фильтры', 'проекция', 'sort/limit', 'индексы', 'compound', 'aggregation', '$match', '$group', '$lookup', 'replica set', 'шардирование', 'transactions', 'schema validation', 'Atlas']),
        ],

        // ------------------------------------------------------------------ 15
        [
            'title'       => 'Redis: кэш и структуры данных',
            'instructor'  => 'Егор Литвинов · Platform Engineer',
            'category'    => 'backend',
            'description' => 'Redis: строки, хеши, списки, множества, TTL, pub/sub, Lua, кластер, сценарии применения.',
            'skills'      => ['Redis', 'Caching', 'Pub/Sub', 'Lua', 'Cluster'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/dc2626/ffffff?text=Redis',
            'lessons'     => lp_generic_lessons('Redis', 'redis'),
            'exam_questions' => lp_exam_generic('Redis', ['строки', 'SET/GET', 'TTL/EXPIRE', 'хеши', 'HSET', 'списки', 'LPUSH', 'множества', 'SADD', 'sorted sets', 'ZADD', 'pub/sub', 'SUBSCRIBE', 'транзакции', 'MULTI', 'Lua скрипты', 'persistence', 'RDB', 'AOF', 'кластер', 'sentinel']),
        ],

        // ------------------------------------------------------------------ 16
        [
            'title'       => 'CI/CD: автоматизация доставки',
            'instructor'  => 'Евгений Климов · DevOps Lead',
            'category'    => 'devops',
            'description' => 'CI/CD: GitHub Actions, GitLab CI, Jenkins, артефакты, деплой стратегии, quality gates.',
            'skills'      => ['CI/CD', 'GitHub Actions', 'GitLab CI', 'Artifacts', 'Deployment'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/7c3aed/ffffff?text=CI%2FCD',
            'lessons'     => lp_generic_lessons('CI/CD', 'cicd'),
            'exam_questions' => lp_exam_generic('CI/CD', ['pipeline', 'trigger', 'job', 'stage', 'артефакты', 'кэш', 'secrets', 'env vars', 'matrix', 'GitHub Actions', 'workflow', 'GitLab CI', '.gitlab-ci.yml', 'runner', 'деплой', 'blue/green', 'canary', 'rollback', 'quality gate', 'notifications']),
        ],

        // ------------------------------------------------------------------ 17
        [
            'title'       => 'Kubernetes: оркестрация контейнеров',
            'instructor'  => 'Евгений Климов · DevOps Lead',
            'category'    => 'devops',
            'description' => 'Kubernetes: Pod, Deployment, Service, Ingress, ConfigMap, Secret, HPA, мониторинг.',
            'skills'      => ['Kubernetes', 'Pods', 'Services', 'Ingress', 'Helm'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/0369a1/ffffff?text=Kubernetes',
            'lessons'     => lp_generic_lessons('Kubernetes', 'k8s'),
            'exam_questions' => lp_exam_generic('Kubernetes', ['Pod', 'Deployment', 'ReplicaSet', 'Service', 'ClusterIP', 'NodePort', 'LoadBalancer', 'Ingress', 'ConfigMap', 'Secret', 'Volume', 'PVC', 'Namespace', 'RBAC', 'HPA', 'VPA', 'Helm', 'kubectl', 'readiness probe', 'liveliness probe']),
        ],

        // ------------------------------------------------------------------ 18
        [
            'title'       => 'Паттерны проектирования',
            'instructor'  => 'Павел Лебедев · Java Architect',
            'category'    => 'backend',
            'description' => 'GoF паттерны: порождающие, структурные, поведенческие. SOLID, DRY, KISS. Рефакторинг.',
            'skills'      => ['Design Patterns', 'SOLID', 'Refactoring', 'Clean Code', 'Architecture'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/9333ea/ffffff?text=Patterns',
            'lessons'     => lp_patterns_lessons(),
            'exam_questions' => lp_exam_generic('Паттерны', ['Singleton', 'Factory Method', 'Abstract Factory', 'Builder', 'Prototype', 'Adapter', 'Decorator', 'Facade', 'Proxy', 'Composite', 'Observer', 'Strategy', 'Command', 'Iterator', 'Template Method', 'State', 'Chain of Responsibility', 'SOLID', 'DRY/KISS', 'рефакторинг']),
        ],

        // ------------------------------------------------------------------ 19
        [
            'title'       => 'REST API: проектирование и реализация',
            'instructor'  => 'Марат Юсупов · Backend Architect',
            'category'    => 'backend',
            'description' => 'Проектирование RESTful API: ресурсы, методы, статусы, версионирование, аутентификация, документация.',
            'skills'      => ['REST', 'HTTP', 'JWT', 'OpenAPI', 'Versioning'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/0f766e/ffffff?text=REST+API',
            'lessons'     => lp_api_lessons(),
            'exam_questions' => lp_exam_generic('REST API', ['REST принципы', 'ресурс', 'HTTP методы', 'GET/POST/PUT/PATCH/DELETE', 'статус коды', '2xx/3xx/4xx/5xx', 'URI design', 'версионирование', 'аутентификация', 'JWT', 'OAuth 2.0', 'CORS', 'rate limiting', 'pagination', 'filtering', 'OpenAPI', 'Swagger', 'идемпотентность', 'HATEOAS', 'GraphQL vs REST']),
        ],

        // ------------------------------------------------------------------ 20
        [
            'title'       => 'Безопасность веб-приложений',
            'instructor'  => 'Антон Захаров · Staff Engineer',
            'category'    => 'backend',
            'description' => 'Web security: OWASP Top 10, XSS, SQLi, CSRF, аутентификация, шифрование, аудит.',
            'skills'      => ['Security', 'OWASP', 'XSS', 'CSRF', 'Encryption'],
            'lang'        => 'python',
            'image'       => 'https://placehold.co/600x400/be123c/ffffff?text=Security',
            'lessons'     => lp_security_lessons(),
            'exam_questions' => lp_exam_generic('Web Security', ['OWASP Top 10', 'XSS', 'reflected', 'stored', 'DOM XSS', 'SQL Injection', 'prepared statements', 'CSRF', 'SSRF', 'XXE', 'IDOR', 'брутфорс', 'rate limiting', 'JWT уязвимости', 'HTTPS/TLS', 'HSTS', 'CSP', 'CORS', 'шифрование', 'аудит']),
        ],
    ];
}

// ---------------------------------------------------------------------------
//  SQL LESSONS  (курс №3)
// ---------------------------------------------------------------------------

function lp_sql_lessons(): array
{
    return [
        ['title' => 'Введение в реляционные БД', 'theory' => 'Таблицы, строки, столбцы. Первичный и внешний ключи. Нормальные формы 1NF-3NF.', 'practice_title' => 'MRR активных подписок', 'practice_prompt' => 'Посчитайте суммарный MRR активных подписок.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS subscriptions","CREATE TABLE subscriptions (id INT, user_id INT, plan VARCHAR(20), price INT, status VARCHAR(20))","INSERT INTO subscriptions VALUES (1,1,'pro',49,'active'),(2,2,'team',99,'active'),(3,3,'pro',49,'canceled')"], 'expected_sql' => "SELECT SUM(price) AS mrr FROM subscriptions WHERE status='active'"]]],
        ['title' => 'SELECT: фильтрация и сортировка', 'theory' => 'WHERE, AND/OR/NOT, BETWEEN, IN, LIKE. ORDER BY ASC/DESC. LIMIT/OFFSET.', 'practice_title' => 'Топ 3 по цене', 'practice_prompt' => 'Выведите 3 дорогих активных подписки (price DESC).', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS subscriptions","CREATE TABLE subscriptions (id INT, price INT, status VARCHAR(20))","INSERT INTO subscriptions VALUES (1,99,'active'),(2,49,'active'),(3,29,'active'),(4,199,'canceled'),(5,149,'active')"], 'expected_sql' => "SELECT id, price FROM subscriptions WHERE status='active' ORDER BY price DESC LIMIT 3"]]],
        ['title' => 'Агрегатные функции', 'theory' => 'COUNT, SUM, AVG, MIN, MAX. GROUP BY. HAVING. Отличие WHERE от HAVING.', 'practice_title' => 'Статистика по планам', 'practice_prompt' => 'Для каждого плана: количество и суммарный доход.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS subs","CREATE TABLE subs (id INT, plan VARCHAR(20), price INT, status VARCHAR(20))","INSERT INTO subs VALUES (1,'pro',49,'active'),(2,'pro',49,'active'),(3,'team',99,'active'),(4,'pro',49,'canceled')"], 'expected_sql' => "SELECT plan, COUNT(*) AS cnt, SUM(price) AS revenue FROM subs GROUP BY plan ORDER BY plan"]]],
        ['title' => 'JOIN: объединение таблиц', 'theory' => 'INNER JOIN, LEFT/RIGHT JOIN, FULL OUTER JOIN. Самообъединение. Нескольких таблиц.', 'practice_title' => 'Пользователи с заказами', 'practice_prompt' => 'Выведите email пользователей и количество их заказов.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS users","DROP TABLE IF EXISTS orders","CREATE TABLE users (id INT, email VARCHAR(60))","CREATE TABLE orders (id INT, user_id INT, amount INT)","INSERT INTO users VALUES (1,'a@x.com'),(2,'b@x.com'),(3,'c@x.com')","INSERT INTO orders VALUES (1,1,100),(2,1,200),(3,2,50)"], 'expected_sql' => "SELECT u.email, COUNT(o.id) AS orders_count FROM users u LEFT JOIN orders o ON o.user_id=u.id GROUP BY u.id, u.email ORDER BY u.id"]]],
        ['title' => 'Подзапросы', 'theory' => 'Scalar subquery, IN/NOT IN subquery, корреляционные подзапросы. EXISTS.', 'practice_title' => 'Пользователи без заказов', 'practice_prompt' => 'Найдите пользователей, у которых нет ни одного заказа.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS users","DROP TABLE IF EXISTS orders","CREATE TABLE users (id INT, email VARCHAR(60))","CREATE TABLE orders (id INT, user_id INT)","INSERT INTO users VALUES (1,'a@x.com'),(2,'b@x.com'),(3,'c@x.com')","INSERT INTO orders VALUES (1,1),(2,1)"], 'expected_sql' => "SELECT id, email FROM users WHERE id NOT IN (SELECT DISTINCT user_id FROM orders) ORDER BY id"]]],
        ['title' => 'CTE: общие табличные выражения', 'theory' => 'WITH ... AS (...). Рекурсивные CTE. Читаемость vs производительность.', 'practice_title' => 'Топ покупатели через CTE', 'practice_prompt' => 'Через CTE найдите топ-2 пользователей по сумме заказов.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS orders","CREATE TABLE orders (id INT, user_id INT, amount INT)","INSERT INTO orders VALUES (1,1,300),(2,2,100),(3,1,200),(4,3,500),(5,2,150)"], 'expected_sql' => "WITH ranked AS (SELECT user_id, SUM(amount) AS total FROM orders GROUP BY user_id) SELECT user_id, total FROM ranked ORDER BY total DESC LIMIT 2"]]],
        ['title' => 'Оконные функции', 'theory' => 'OVER(), PARTITION BY, ORDER BY в окне. ROW_NUMBER, RANK, DENSE_RANK, LAG, LEAD, SUM OVER.', 'practice_title' => 'Ранг по выручке', 'practice_prompt' => 'Для каждого пользователя выведите его ранг по сумме заказов.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS orders","CREATE TABLE orders (id INT, user_id INT, amount INT)","INSERT INTO orders VALUES (1,1,500),(2,2,300),(3,3,700),(4,4,300)"], 'expected_sql' => "SELECT user_id, SUM(amount) AS total, RANK() OVER (ORDER BY SUM(amount) DESC) AS rnk FROM orders GROUP BY user_id ORDER BY rnk"]]],
        ['title' => 'Индексы и производительность', 'theory' => 'B-Tree, Hash, составной индекс. EXPLAIN/EXPLAIN ANALYZE. Покрывающий индекс.', 'practice_title' => 'Запрос с индексом', 'practice_prompt' => 'Подсчитайте заказы со статусом shipped (оптимально).', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS orders","CREATE TABLE orders (id INT, status VARCHAR(20), amount INT)","INSERT INTO orders VALUES (1,'shipped',100),(2,'pending',200),(3,'shipped',300),(4,'shipped',50)"], 'expected_sql' => "SELECT COUNT(*) AS cnt, SUM(amount) AS total FROM orders WHERE status='shipped'"]]],
        ['title' => 'Транзакции и ACID', 'theory' => 'BEGIN/COMMIT/ROLLBACK. ACID свойства. Уровни изоляции: READ COMMITTED, REPEATABLE READ, SERIALIZABLE.', 'practice_title' => 'Обновление баланса', 'practice_prompt' => 'Вычтите 50 из счёта с id=1 и прибавьте к счёту id=2. Выведите оба баланса.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS accounts","CREATE TABLE accounts (id INT, balance INT)","INSERT INTO accounts VALUES (1,500),(2,200)"], 'expected_sql' => "UPDATE accounts SET balance=balance-50 WHERE id=1; UPDATE accounts SET balance=balance+50 WHERE id=2; SELECT id, balance FROM accounts ORDER BY id"]]],
        ['title' => 'INSERT, UPDATE, DELETE', 'theory' => 'Синтаксис DML. ON DUPLICATE KEY UPDATE. Soft delete. Каскадное удаление.', 'practice_title' => 'Мягкое удаление', 'practice_prompt' => 'Установите deleted_at=NOW() для пользователей без заказов.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS users","DROP TABLE IF EXISTS orders","CREATE TABLE users (id INT, email VARCHAR(60), deleted_at DATETIME DEFAULT NULL)","CREATE TABLE orders (id INT, user_id INT)","INSERT INTO users VALUES (1,'a@x.com',NULL),(2,'b@x.com',NULL),(3,'c@x.com',NULL)","INSERT INTO orders VALUES (1,1),(2,1)"], 'expected_sql' => "UPDATE users SET deleted_at=NOW() WHERE id NOT IN (SELECT DISTINCT user_id FROM orders); SELECT id, email, deleted_at IS NOT NULL AS is_deleted FROM users ORDER BY id"]]],
        ['title' => 'Нормализация и схема БД', 'theory' => '1NF—3NF. Денормализация ради производительности. ERD. Антипаттерны.', 'practice_title' => 'Дублирование данных', 'practice_prompt' => 'Найдите email-адреса, встречающиеся более одного раза.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS users","CREATE TABLE users (id INT, email VARCHAR(60))","INSERT INTO users VALUES (1,'a@x.com'),(2,'b@x.com'),(3,'a@x.com'),(4,'c@x.com'),(5,'b@x.com')"], 'expected_sql' => "SELECT email, COUNT(*) AS cnt FROM users GROUP BY email HAVING COUNT(*) > 1 ORDER BY email"]]],
        ['title' => 'Работа с датами', 'theory' => 'DATE, DATETIME, TIMESTAMP. DATE_FORMAT, DATE_ADD/SUB, DATEDIFF, EXTRACT. Временные зоны.', 'practice_title' => 'Заказы за последние 30 дней', 'practice_prompt' => 'Подсчитайте заказы за последние 30 дней (используйте NOW()).', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS orders","CREATE TABLE orders (id INT, created_at DATETIME, amount INT)","INSERT INTO orders VALUES (1,NOW(),100),(2,DATE_SUB(NOW(),INTERVAL 5 DAY),200),(3,DATE_SUB(NOW(),INTERVAL 40 DAY),300)"], 'expected_sql' => "SELECT COUNT(*) AS cnt, SUM(amount) AS total FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"]]],
        ['title' => 'Строковые функции', 'theory' => 'CONCAT, SUBSTRING, REPLACE, TRIM, UPPER/LOWER, LENGTH, REGEXP.', 'practice_title' => 'Домен email', 'practice_prompt' => 'Извлеките домен (после @) из каждого email. Выведите уникальные домены.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS users","CREATE TABLE users (id INT, email VARCHAR(60))","INSERT INTO users VALUES (1,'alice@gmail.com'),(2,'bob@yahoo.com'),(3,'carol@gmail.com')"], 'expected_sql' => "SELECT DISTINCT SUBSTRING_INDEX(email,'@',-1) AS domain FROM users ORDER BY domain"]]],
        ['title' => 'Представления (Views)', 'theory' => 'CREATE VIEW. Обновляемые и необновляемые вью. Materialized views (PostgreSQL).', 'practice_title' => 'Вью активных пользователей', 'practice_prompt' => 'Создайте view active_users с пользователями у которых есть заказы.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS users","DROP TABLE IF EXISTS orders","DROP VIEW IF EXISTS active_users","CREATE TABLE users (id INT, email VARCHAR(60))","CREATE TABLE orders (id INT, user_id INT)","INSERT INTO users VALUES (1,'a@x.com'),(2,'b@x.com'),(3,'c@x.com')","INSERT INTO orders VALUES (1,1),(2,3)"], 'expected_sql' => "CREATE VIEW active_users AS SELECT DISTINCT u.id, u.email FROM users u INNER JOIN orders o ON o.user_id=u.id; SELECT id, email FROM active_users ORDER BY id"]]],
        ['title' => 'Хранимые процедуры', 'theory' => 'CREATE PROCEDURE, параметры IN/OUT/INOUT. Условия и циклы в SQL. DELIMITER.', 'practice_title' => 'Процедура начисления баллов', 'practice_prompt' => 'Выведите пользователей с суммой заказов > 300.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS orders","CREATE TABLE orders (id INT, user_id INT, amount INT)","INSERT INTO orders VALUES (1,1,200),(2,1,150),(3,2,100),(4,3,400)"], 'expected_sql' => "SELECT user_id, SUM(amount) AS total FROM orders GROUP BY user_id HAVING SUM(amount) > 300 ORDER BY user_id"]]],
        ['title' => 'Конкурентность и блокировки', 'theory' => 'Shared/Exclusive lock. Deadlock. Optimistic vs pessimistic locking. SELECT FOR UPDATE.', 'practice_title' => 'Конкурентные обновления', 'practice_prompt' => 'Атомарно увеличьте counter на 1 для id=1.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS counters","CREATE TABLE counters (id INT PRIMARY KEY, counter INT DEFAULT 0)","INSERT INTO counters VALUES (1,5)"], 'expected_sql' => "UPDATE counters SET counter=counter+1 WHERE id=1; SELECT counter FROM counters WHERE id=1"]]],
        ['title' => 'Партиционирование таблиц', 'theory' => 'RANGE, LIST, HASH, KEY партиции. Partition pruning. Ограничения MySQL.', 'practice_title' => 'Данные по квартала', 'practice_prompt' => 'Выведите выручку по кварталам.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS sales","CREATE TABLE sales (id INT, amount INT, sale_date DATE)","INSERT INTO sales VALUES (1,100,'2026-01-15'),(2,200,'2026-02-10'),(3,300,'2026-04-05'),(4,150,'2026-07-20')"], 'expected_sql' => "SELECT QUARTER(sale_date) AS qtr, SUM(amount) AS revenue FROM sales GROUP BY QUARTER(sale_date) ORDER BY qtr"]]],
        ['title' => 'Оптимизация медленных запросов', 'theory' => 'EXPLAIN FORMAT=JSON. Индексы для ORDER BY/GROUP BY. Избегание SELECT *. Query cache.', 'practice_title' => 'Анализ продаж', 'practice_prompt' => 'Для каждого продукта найдите максимальную и среднюю сумму заказа.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS orders","CREATE TABLE orders (id INT, product VARCHAR(50), amount INT)","INSERT INTO orders VALUES (1,'A',100),(2,'A',200),(3,'B',300),(4,'B',150),(5,'C',50)"], 'expected_sql' => "SELECT product, MAX(amount) AS max_amount, ROUND(AVG(amount),2) AS avg_amount FROM orders GROUP BY product ORDER BY product"]]],
        ['title' => 'Репликация и резервное копирование', 'theory' => 'Master-Slave репликация. Binary log. mysqldump. Point-in-time recovery.', 'practice_title' => 'Отчёт по активности', 'practice_prompt' => 'Выведите кол-во заказов, сумму и средний чек по каждому пользователю.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS orders","CREATE TABLE orders (id INT, user_id INT, amount INT)","INSERT INTO orders VALUES (1,1,100),(2,1,200),(3,2,300),(4,3,50),(5,3,50)"], 'expected_sql' => "SELECT user_id, COUNT(*) AS cnt, SUM(amount) AS total, ROUND(AVG(amount),2) AS avg_amount FROM orders GROUP BY user_id ORDER BY user_id"]]],
        ['title' => 'Capstone: аналитический отчёт', 'theory' => 'Объединяем всё: CTE + оконные функции + JOIN + фильтры. Финальный проект.', 'practice_title' => 'Retention анализ', 'practice_prompt' => 'Найдите пользователей, сделавших заказ в январе 2026 И в феврале 2026.', 'tests' => [['setup_sql' => ["DROP TABLE IF EXISTS orders","CREATE TABLE orders (id INT, user_id INT, order_date DATE)","INSERT INTO orders VALUES (1,1,'2026-01-10'),(2,1,'2026-02-15'),(3,2,'2026-01-05'),(4,3,'2026-02-20')"], 'expected_sql' => "SELECT DISTINCT user_id FROM orders WHERE MONTH(order_date)=1 AND YEAR(order_date)=2026 AND user_id IN (SELECT user_id FROM orders WHERE MONTH(order_date)=2 AND YEAR(order_date)=2026) ORDER BY user_id"]]],
    ];
}

// ---------------------------------------------------------------------------
//  C++ LESSONS  (курс №4)
// ---------------------------------------------------------------------------

function lp_cpp_lessons(): array
{
    return [
        ['title' => 'Основы C++: компиляция и I/O', 'theory' => 'g++, флаги -O2 -std=c++17. cin/cout. ios::sync_with_stdio(false).', 'practice_title' => 'A + B', 'practice_prompt' => 'Прочитайте два числа и выведите их сумму.', 'tests' => [['stdin'=>"3 4\n",'expected'=>"7"],['stdin'=>"-5 10\n",'expected'=>"5"]]],
        ['title' => 'Типы данных и арифметика', 'theory' => 'int, long long, double, char, bool. Переполнение. static_cast.', 'practice_title' => 'Сумма 1..N', 'practice_prompt' => 'Прочитайте N (до 10^9). Выведите N*(N+1)/2.', 'tests' => [['stdin'=>"10\n",'expected'=>"55"],['stdin'=>"1000000000\n",'expected'=>"500000000500000000"]]],
        ['title' => 'Условия и циклы', 'theory' => 'if/else, switch. for, while, do-while. break, continue.', 'practice_title' => 'Простое число', 'practice_prompt' => 'Прочитайте N. YES если простое, иначе NO.', 'tests' => [['stdin'=>"7\n",'expected'=>"YES"],['stdin'=>"12\n",'expected'=>"NO"],['stdin'=>"2\n",'expected'=>"YES"]]],
        ['title' => 'Массивы и векторы', 'theory' => 'C-массивы vs std::vector. push_back, resize, reserve. Двумерные массивы.', 'practice_title' => 'Максимальная сумма подмассива', 'practice_prompt' => 'Прочитайте N и массив. Выведите максимальную сумму непрерывного подмассива (Кадане).', 'tests' => [['stdin'=>"9\n-2 1 -3 4 -1 2 1 -5 4\n",'expected'=>"6"],['stdin'=>"3\n-5 -3 -1\n",'expected'=>"-1"]]],
        ['title' => 'Строки в C++', 'theory' => 'std::string: length, substr, find, replace. Сравнение. stringstream.', 'practice_title' => 'Анаграммы', 'practice_prompt' => 'Прочитайте две строки. YES если анаграммы (игнор. регистр), иначе NO.', 'tests' => [['stdin'=>"Listen\nSilent\n",'expected'=>"YES"],['stdin'=>"hello\nworld\n",'expected'=>"NO"]]],
        ['title' => 'Функции и рекурсия', 'theory' => 'Передача по значению и ссылке. inline. Хвостовая рекурсия. Stack overflow.', 'practice_title' => 'НОД и НОК', 'practice_prompt' => 'Прочитайте A B. Выведите их НОД и НОК.', 'tests' => [['stdin'=>"12 18\n",'expected'=>"6 36"],['stdin'=>"7 13\n",'expected'=>"1 91"]]],
        ['title' => 'STL: sort, search, алгоритмы', 'theory' => 'sort, stable_sort, binary_search, lower_bound, upper_bound, reverse, unique.', 'practice_title' => 'Количество вхождений', 'practice_prompt' => 'Прочитайте N, массив и Q запросов x. Для каждого x выведите сколько раз встречается.', 'tests' => [['stdin'=>"7\n1 2 2 3 2 4 5\n2\n2\n6\n",'expected'=>"3\n0"],['stdin'=>"3\n5 5 5\n1\n5\n",'expected'=>"3"]]],
        ['title' => 'STL-контейнеры: map, set, queue', 'theory' => 'map, multimap, set, multiset. unordered_map, unordered_set. queue, priority_queue, deque.', 'practice_title' => 'Самый частый элемент', 'practice_prompt' => 'Прочитайте N и массив. Выведите элемент с максимальной частотой (при равенстве — минимальный).', 'tests' => [['stdin'=>"7\n1 2 2 3 3 3 2\n",'expected'=>"2"],['stdin'=>"5\n5 5 5 5 5\n",'expected'=>"5"]]],
        ['title' => 'Два указателя и скользящее окно', 'theory' => 'Two-pointer техника. Sliding window максимум. Задачи на отрезки.', 'practice_title' => 'Длинная подстрока без повторов', 'practice_prompt' => 'Прочитайте строку. Выведите длину самой длинной подстроки без повторяющихся символов.', 'tests' => [['stdin'=>"abcabcbb\n",'expected'=>"3"],['stdin'=>"pwwkew\n",'expected'=>"3"],['stdin'=>"aaaaaa\n",'expected'=>"1"]]],
        ['title' => 'Бинарный поиск', 'theory' => 'Binary search по ответу. Монотонные функции. std::lower_bound.', 'practice_title' => 'Квадратный корень', 'practice_prompt' => 'Прочитайте N (до 10^18). Выведите floor(sqrt(N)) без встроенных функций.', 'tests' => [['stdin'=>"16\n",'expected'=>"4"],['stdin'=>"17\n",'expected'=>"4"],['stdin'=>"1000000000000000000\n",'expected'=>"1000000000"]]],
        ['title' => 'Стек и очередь', 'theory' => 'stack, queue. Задачи: скобки, монотонный стек, Next Greater Element.', 'practice_title' => 'Корректные скобки', 'practice_prompt' => 'Прочитайте строку из ()[]{}. YES если корректная скобочная последовательность, иначе NO.', 'tests' => [['stdin'=>"{[()()]}\n",'expected'=>"YES"],['stdin'=>"([)]\n",'expected'=>"NO"],['stdin'=>"\n",'expected'=>"YES"]]],
        ['title' => 'Графы: BFS и DFS', 'theory' => 'Список смежности. BFS — кратчайший путь в невзвешенном графе. DFS — компоненты связности.', 'practice_title' => 'Кратчайший путь в сетке', 'practice_prompt' => 'Прочитайте N M, матрицу 0/1. Выведите минимум шагов от (0,0) до (N-1,M-1) или -1.', 'tests' => [['stdin'=>"3 3\n0 0 0\n1 1 0\n0 0 0\n",'expected'=>"4"],['stdin'=>"2 2\n0 1\n1 0\n",'expected'=>"-1"]]],
        ['title' => 'Дейкстра и взвешенные графы', 'theory' => 'Алгоритм Дейкстры с priority_queue. Bellman-Ford. Отрицательные циклы.', 'practice_title' => 'Кратчайший путь', 'practice_prompt' => 'Прочитайте N V E, рёбра u v w. Выведите кратчайшее расстояние от 1 до V или -1.', 'tests' => [['stdin'=>"5 5 6\n1 2 2\n1 3 5\n2 3 1\n2 4 2\n3 5 5\n4 5 1\n",'expected'=>"5"],['stdin'=>"4 4 2\n1 2 3\n2 3 2\n",'expected'=>"-1"]]],
        ['title' => 'Динамическое программирование 1', 'theory' => 'Memoization vs Tabulation. Задачи: Fibonacci, Coin Change, Knapsack 0/1.', 'practice_title' => 'Монеты', 'practice_prompt' => 'Прочитайте N и amount, затем N номиналов. Минимальное кол-во монет для суммы amount или -1.', 'tests' => [['stdin'=>"3 11\n1 2 5\n",'expected'=>"3"],['stdin'=>"2 3\n2 4\n",'expected'=>"-1"]]],
        ['title' => 'Динамическое программирование 2', 'theory' => 'LIS, LCS, Edit Distance, Matrix Chain. DP на строках.', 'practice_title' => 'Наибольшая возрастающая подпоследовательность', 'practice_prompt' => 'Прочитайте N и массив. Выведите длину LIS (строго возрастающая).', 'tests' => [['stdin'=>"8\n10 9 2 5 3 7 101 18\n",'expected'=>"4"],['stdin'=>"5\n5 4 3 2 1\n",'expected'=>"1"]]],
        ['title' => 'Жадные алгоритмы', 'theory' => 'Когда жадность оптимальна. Задачи: интервалы, расписание, хаффман.', 'practice_title' => 'Слияние интервалов', 'practice_prompt' => 'Прочитайте N интервалов [l r]. Выведите количество интервалов после слияния.', 'tests' => [['stdin'=>"4\n1 3\n2 6\n8 10\n15 18\n",'expected'=>"3"],['stdin'=>"3\n1 4\n4 5\n7 9\n",'expected'=>"2"]]],
        ['title' => 'Структуры данных: дерево отрезков', 'theory' => 'Segment tree: build, query, update. Point update + range query. Lazy propagation идея.', 'practice_title' => 'Сумма на отрезке', 'practice_prompt' => 'Прочитайте N, массив, Q запросов l r. Выведите сумму на каждом.', 'tests' => [['stdin'=>"5\n2 4 6 8 10\n3\n1 3\n2 5\n4 4\n",'expected'=>"12\n28\n8"],['stdin'=>"4\n1 1 1 1\n2\n1 4\n2 3\n",'expected'=>"4\n2"]]],
        ['title' => 'Хеширование и bitset', 'theory' => 'Полиномиальное хеширование строк. bitset для быстрых операций. Bloomfilter.', 'practice_title' => 'Совпадающие подстроки', 'practice_prompt' => 'Прочитайте S и T. Выведите количество вхождений T в S.', 'tests' => [['stdin'=>"ababab\nab\n",'expected'=>"3"],['stdin'=>"aaa\naa\n",'expected'=>"2"]]],
        ['title' => 'Математика: простые числа и мод', 'theory' => 'Решето Эратосфена. Быстрое возведение в степень. Обратный элемент по модулю. ФМТ.', 'practice_title' => 'Степень по модулю', 'practice_prompt' => 'Прочитайте base, exp, mod. Выведите (base^exp) % mod.', 'tests' => [['stdin'=>"2 10 1000\n",'expected'=>"24"],['stdin'=>"3 4 7\n",'expected'=>"4"]]],
        ['title' => 'Capstone: задачи ICPC-уровня', 'theory' => 'Разбор задач комбинирующих несколько тем. Оптимизация кода под TL.', 'practice_title' => 'Дерево Штейнера (упрощённое)', 'practice_prompt' => 'Прочитайте N вершин и E рёбер. Найдите минимальное остовное дерево (Краскал). Выведите суммарный вес.', 'tests' => [['stdin'=>"4 5\n1 2 1\n1 3 4\n1 4 3\n2 3 2\n3 4 5\n",'expected'=>"6"],['stdin'=>"2 1\n1 2 10\n",'expected'=>"10"]]],
    ];
}

// ---------------------------------------------------------------------------
//  ALGORITHMS LESSONS  (курс №9)
// ---------------------------------------------------------------------------

function lp_algo_lessons(): array
{
    return [
        ['title' => 'Two Sum', 'theory' => 'Hash map для O(n). Классика LeetCode #1.', 'practice_title' => 'Two Sum', 'practice_prompt' => 'Прочитайте N target, затем массив. Выведите 0-based индексы двух элементов с суммой target.', 'tests' => [['stdin'=>"4 9\n2 7 11 15\n",'expected'=>"0 1"],['stdin'=>"5 6\n3 2 4 1 5\n",'expected'=>"1 2"]]],
        ['title' => 'Valid Parentheses', 'theory' => 'Stack-подход за O(n).', 'practice_title' => 'Скобки', 'practice_prompt' => 'Прочитайте строку ()[]{}. YES если корректна, иначе NO.', 'tests' => [['stdin'=>"()[]{}\n",'expected'=>"YES"],['stdin'=>"([)]\n",'expected'=>"NO"]]],
        ['title' => 'Merge Intervals', 'theory' => 'Сортировка + проход. O(n log n).', 'practice_title' => 'Слитые интервалы', 'practice_prompt' => 'Прочитайте N интервалов. Выведите слитые интервалы (l r).', 'tests' => [['stdin'=>"4\n1 3\n2 6\n8 10\n15 18\n",'expected'=>"1 6\n8 10\n15 18"],['stdin'=>"2\n1 5\n2 3\n",'expected'=>"1 5"]]],
        ['title' => 'Longest Substring Without Repeating', 'theory' => 'Sliding window с HashMap.', 'practice_title' => 'Подстрока без повторов', 'practice_prompt' => 'Прочитайте строку. Длина самой длинной подстроки без повторов.', 'tests' => [['stdin'=>"abcabcbb\n",'expected'=>"3"],['stdin'=>"bbbbb\n",'expected'=>"1"]]],
        ['title' => 'Product of Array Except Self', 'theory' => 'Prefix + suffix продукты без деления. O(n) memory O(1).', 'practice_title' => 'Произведение кроме себя', 'practice_prompt' => 'Прочитайте N и массив. Для каждого i — произведение всех кроме arr[i].', 'tests' => [['stdin'=>"4\n1 2 3 4\n",'expected'=>"24 12 8 6"],['stdin'=>"3\n2 3 4\n",'expected'=>"12 8 6"]]],
        ['title' => 'Maximum Subarray (Kadane)', 'theory' => 'Алгоритм Кадана O(n).', 'practice_title' => 'Максимальная сумма подмассива', 'practice_prompt' => 'Прочитайте N и массив. Максимальная сумма непрерывного подмассива.', 'tests' => [['stdin'=>"9\n-2 1 -3 4 -1 2 1 -5 4\n",'expected'=>"6"],['stdin'=>"1\n-5\n",'expected'=>"-5"]]],
        ['title' => 'Binary Search', 'theory' => 'O(log n). Поиск по ответу. Монотонные предикаты.', 'practice_title' => 'Бинарный поиск', 'practice_prompt' => 'Прочитайте N target, sorted массив. Индекс target или -1.', 'tests' => [['stdin'=>"6 9\n-1 0 3 5 9 12\n",'expected'=>"4"],['stdin'=>"5 2\n1 3 5 7 9\n",'expected'=>"-1"]]],
        ['title' => 'Linked List: Reverse', 'theory' => 'Итеративный и рекурсивный разворот односвязного списка.', 'practice_title' => 'Разворот массива', 'practice_prompt' => 'Прочитайте N и массив. Выведите в обратном порядке.', 'tests' => [['stdin'=>"5\n1 2 3 4 5\n",'expected'=>"5 4 3 2 1"],['stdin'=>"1\n42\n",'expected'=>"42"]]],
        ['title' => 'Trees: BFS и DFS', 'theory' => 'BFS уровнями. DFS: preorder/inorder/postorder. Сериализация.', 'practice_title' => 'Высота дерева', 'practice_prompt' => 'Прочитайте N рёбер parent child. Выведите высоту дерева с корнем 1.', 'tests' => [['stdin'=>"3\n1 2\n1 3\n2 4\n",'expected'=>"3"],['stdin'=>"0\n",'expected'=>"1"]]],
        ['title' => 'Dynamic Programming: Coin Change', 'theory' => 'DP снизу вверх. Оптимальная подструктура.', 'practice_title' => 'Монеты', 'practice_prompt' => 'Прочитайте N amount, монеты. Минимум монет или -1.', 'tests' => [['stdin'=>"3 11\n1 2 5\n",'expected'=>"3"],['stdin'=>"2 3\n2 4\n",'expected'=>"-1"]]],
        ['title' => 'Graphs: Number of Islands', 'theory' => 'DFS/BFS по матрице. Компоненты связности.', 'practice_title' => 'Острова', 'practice_prompt' => 'Прочитайте N M, матрицу из 0 и 1. Кол-во островов (4-связность).', 'tests' => [['stdin'=>"4 5\n1 1 0 0 0\n1 1 0 0 0\n0 0 1 0 0\n0 0 0 1 1\n",'expected'=>"3"],['stdin'=>"2 2\n0 0\n0 0\n",'expected'=>"0"]]],
        ['title' => 'Greedy: Jump Game', 'theory' => 'Жадный алгоритм для максимального достижимого индекса.', 'practice_title' => 'Прыжки', 'practice_prompt' => 'Прочитайте N и массив прыжков. YES если можно достичь последнего, NO иначе.', 'tests' => [['stdin'=>"6\n2 3 1 1 4 0\n",'expected'=>"YES"],['stdin'=>"6\n3 2 1 0 4 0\n",'expected'=>"NO"]]],
        ['title' => 'Backtracking: Subsets', 'theory' => 'Рекурсивный перебор. Pruning. Задачи: перестановки, комбинации, N-Queens.', 'practice_title' => 'Подмножества', 'practice_prompt' => 'Прочитайте N и массив уникальных чисел. Выведите все подмножества (sorted, один на строку).', 'tests' => [['stdin'=>"3\n1 2 3\n",'expected'=>"\n1\n1 2\n1 2 3\n1 3\n2\n2 3\n3"],['stdin'=>"1\n5\n",'expected'=>"\n5"]]],
        ['title' => 'Top K Frequent Elements', 'theory' => 'Bucket sort O(n) или heap O(n log k). Hash + sort.', 'practice_title' => 'Топ K частых', 'practice_prompt' => 'Прочитайте N K, массив. K наиболее частых (при равенстве — меньший первый).', 'tests' => [['stdin'=>"6 2\n1 1 1 2 2 3\n",'expected'=>"1 2"],['stdin'=>"5 1\n4 4 4 4 1\n",'expected'=>"4"]]],
        ['title' => 'Trapping Rain Water', 'theory' => 'Two-pointer O(n) или prefix max/min O(n) memory.', 'practice_title' => 'Задержанная вода', 'practice_prompt' => 'Прочитайте N и высоты. Объём воды который задержится.', 'tests' => [['stdin'=>"12\n0 1 0 2 1 0 1 3 2 1 2 1\n",'expected'=>"6"],['stdin'=>"6\n4 2 0 3 2 5\n",'expected'=>"9"]]],
        ['title' => 'House Robber', 'theory' => 'DP. dp[i] = max(dp[i-2]+nums[i], dp[i-1]). Вариация: круговой массив.', 'practice_title' => 'Ограбление', 'practice_prompt' => 'Прочитайте N и массив денег. Максимум без смежных домов.', 'tests' => [['stdin'=>"4\n1 2 3 1\n",'expected'=>"4"],['stdin'=>"5\n2 7 9 3 1\n",'expected'=>"12"]]],
        ['title' => 'Longest Common Subsequence', 'theory' => 'DP O(n*m). Восстановление ответа.', 'practice_title' => 'LCS', 'practice_prompt' => 'Прочитайте две строки. Длина LCS.', 'tests' => [['stdin'=>"abcde\nace\n",'expected'=>"3"],['stdin'=>"abc\ndef\n",'expected'=>"0"]]],
        ['title' => 'Word Search (Backtracking)', 'theory' => 'DFS по матрице с backtracking. Visited matrix.', 'practice_title' => 'Поиск слова', 'practice_prompt' => 'Прочитайте N M, матрицу символов, слово. YES если слово существует (4-связность), NO иначе.', 'tests' => [['stdin'=>"3 4\nA B C E\nS F C S\nA D E E\nABCCED\n",'expected'=>"YES"],['stdin'=>"3 4\nA B C E\nS F C S\nA D E E\nSEE\n",'expected'=>"YES"]]],
        ['title' => 'Graph: Course Schedule (Topological)', 'theory' => 'Kahn algorithm BFS. DFS с цветами. Обнаружение цикла.', 'practice_title' => 'Топологическая сортировка', 'practice_prompt' => 'Прочитайте N M, M пар u v (u должен быть до v). Выведите порядок или IMPOSSIBLE.', 'tests' => [['stdin'=>"4 3\n1 2\n1 3\n3 4\n",'expected'=>"1 2 3 4"],['stdin'=>"2 2\n1 2\n2 1\n",'expected'=>"IMPOSSIBLE"]]],
        ['title' => 'Capstone: Mock Interview', 'theory' => 'Комбинированные задачи под условия интервью. Time boxing. Объяснение вслух.', 'practice_title' => 'Поиск медианы двух массивов', 'practice_prompt' => 'Прочитайте N M, два отсортированных массива. Выведите медиану с 5 знаками.', 'tests' => [['stdin'=>"2 2\n1 3\n2 4\n",'expected'=>"2.50000"],['stdin'=>"1 1\n1\n2\n",'expected'=>"1.50000"]]],
    ];
}

// ---------------------------------------------------------------------------
//  POSTGRESQL LESSONS  (курс №13)
// ---------------------------------------------------------------------------

function lp_pgsql_lessons(): array
{
    $base = lp_sql_lessons();
    // Заменяем SQL-синтаксис на PostgreSQL-специфику в нескольких уроках
    $base[0]['title'] = 'PostgreSQL: типы и схемы';
    $base[1]['title'] = 'SELECT, FILTER и DISTINCT ON';
    $base[6]['title'] = 'Оконные функции: ROWS/RANGE';
    $base[12]['title'] = 'JSONB и операторы';
    $base[13]['title'] = 'Materialized Views';
    $base[14]['title'] = 'Хранимые функции на PL/pgSQL';
    $base[15]['title'] = 'MVCC и VACUUM';
    $base[16]['title'] = 'Партиции: RANGE и LIST';
    $base[17]['title'] = 'EXPLAIN ANALYZE и auto_explain';
    $base[18]['title'] = 'Репликация: streaming и logical';
    $base[19]['title'] = 'Capstone: аналитическое хранилище';
    return $base;
}

// ---------------------------------------------------------------------------
//  GENERIC LESSONS (для курсов без специальных уроков)
// ---------------------------------------------------------------------------

function lp_generic_lessons(string $course, string $track): array
{
    $topics = lp_track_topics($track);
    $lessons = [];
    $practices = lp_generic_practices();

    foreach ($topics as $i => $topic) {
        $p = $practices[$i % count($practices)];
        $lessons[] = [
            'title'           => 'Урок ' . ($i + 1) . '. ' . $topic,
            'theory'          => "Тема: «{$topic}». Разбираем ключевые концепции, реальные примеры применения в {$course}, типичные ошибки и best practices. Выполняем практическое задание и проверяем понимание через чеклист.",
            'practice_title'  => $p['title'] . ' [' . $topic . ']',
            'practice_prompt' => $p['prompt'],
            'tests'           => $p['tests'],
        ];
    }
    return $lessons;
}

function lp_track_topics(string $track): array
{
    $map = [
        'php'        => ['Синтаксис и типы', 'Условия и циклы', 'Функции', 'Массивы', 'Строки', 'ООП: классы', 'ООП: наследование', 'Интерфейсы и трейты', 'Исключения', 'Файлы и формы', 'Сессии и куки', 'PDO', 'CRUD', 'Безопасность', 'Composer', 'REST API', 'Авторизация', 'Тестирование', 'Паттерны', 'Итоговый проект'],
        'laravel'    => ['Установка и структура', 'Маршруты', 'Контроллеры', 'Blade шаблоны', 'Миграции', 'Eloquent', 'Связи', 'Валидация', 'Авторизация', 'Middleware', 'API ресурсы', 'Очереди', 'События', 'Файлы', 'Тестирование', 'Сервис-контейнер', 'Провайдеры', 'Локализация', 'Оптимизация', 'Деплой'],
        'git'        => ['Что такое Git', 'init и clone', 'add и commit', 'status и log', 'Ветки', 'merge', 'rebase', 'Конфликты', 'stash', 'reset и revert', 'cherry-pick', 'Теги', 'remote', 'pull и push', 'Fork и PR', 'gitignore', 'Хуки', 'Workflow', 'Безопасность', 'Итог'],
        'docker'     => ['Контейнеры vs VM', 'docker run', 'Образы', 'Dockerfile FROM', 'COPY и RUN', 'CMD и ENTRYPOINT', 'Volumes', 'Networks', 'docker-compose', 'Multi-stage', 'Registry', 'Health checks', 'Secrets', 'Overlay network', 'Swarm', 'Layer cache', '.dockerignore', 'Best practices', 'CI/CD интеграция', 'Мониторинг'],
        'react'      => ['JSX', 'Компоненты', 'Props', 'State', 'useState', 'useEffect', 'useRef', 'useContext', 'useMemo', 'useCallback', 'React Router', 'Formы', 'Error Boundary', 'Suspense', 'Порталы', 'Redux', 'Zustand', 'Тестирование', 'Оптимизация', 'Деплой'],
        'typescript' => ['Базовые типы', 'Interface', 'Type Alias', 'Union/Intersection', 'Generics', 'Enums', 'Tuple', 'any/unknown/never', 'Narrowing', 'Type Guards', 'Decorators', 'Namespaces', 'Modules', 'tsconfig', 'Strict mode', 'Utility types', 'Mapped types', 'Conditional types', 'Интеграция с React', 'Best practices'],
        'linux'      => ['Файловая система', 'ls cd pwd', 'Права chmod', 'Пользователи', 'Процессы ps', 'kill и сигналы', 'grep sed awk', 'Pipes', 'Перенаправление', 'Bash скрипты', 'Переменные', 'Условия и циклы', 'Функции', 'cron', 'systemd', 'Сети', 'ssh', 'curl и wget', 'Мониторинг', 'Безопасность'],
        'nosql'      => ['Документная модель', 'CRUD', 'Схема и валидация', 'Индексы', 'Compound index', 'Aggregation', '$match $group', '$lookup', 'Replica set', 'Шардирование', 'Transactions', 'Atlas', 'Schema design', 'Embedded vs ref', 'TTL индексы', 'GridFS', 'Change streams', 'Мониторинг', 'Миграции', 'Итог'],
        'redis'      => ['Установка', 'Строки', 'TTL/EXPIRE', 'Хеши', 'Списки', 'Множества', 'Sorted sets', 'Pub/Sub', 'Transactions MULTI', 'Lua скрипты', 'Persistence RDB', 'AOF', 'Репликация', 'Sentinel', 'Кластер', 'Rate limiting', 'Session store', 'Cache pattern', 'Мониторинг', 'Итог'],
        'cicd'       => ['Pipeline', 'Triggers', 'Jobs', 'Stages', 'Артефакты', 'Кэш', 'Secrets', 'Env vars', 'Matrix build', 'GitHub Actions', 'Workflow syntax', 'GitLab CI', 'Runner', 'Деплой', 'Blue/Green', 'Canary', 'Rollback', 'Quality gate', 'Notifications', 'Итог'],
        'k8s'        => ['Архитектура', 'Pod', 'Deployment', 'ReplicaSet', 'Service', 'ClusterIP', 'NodePort', 'Ingress', 'ConfigMap', 'Secret', 'Volume', 'PVC', 'Namespace', 'RBAC', 'HPA', 'Helm', 'kubectl', 'Readiness probe', 'Liveliness probe', 'Мониторинг'],
    ];
    return $map[$track] ?? array_map(fn($i) => "Тема $i", range(1, 20));
}

function lp_generic_practices(): array
{
    return [
        ['title' => 'A + B', 'prompt' => 'Прочитайте два числа. Выведите сумму.', 'tests' => [['stdin'=>"3 7\n",'expected'=>"10"],['stdin'=>"-5 5\n",'expected'=>"0"]]],
        ['title' => 'Палиндром', 'prompt' => 'Прочитайте строку. YES если палиндром, NO иначе.', 'tests' => [['stdin'=>"racecar\n",'expected'=>"YES"],['stdin'=>"hello\n",'expected'=>"NO"]]],
        ['title' => 'Максимум', 'prompt' => 'Прочитайте N и массив. Выведите максимум.', 'tests' => [['stdin'=>"5\n3 1 4 1 5\n",'expected'=>"5"],['stdin'=>"3\n-1 -2 -3\n",'expected'=>"-1"]]],
        ['title' => 'Факториал', 'prompt' => 'Прочитайте N (0..12). Выведите N!.', 'tests' => [['stdin'=>"5\n",'expected'=>"120"],['stdin'=>"0\n",'expected'=>"1"]]],
        ['title' => 'FizzBuzz', 'prompt' => 'Прочитайте N. FizzBuzz правила.', 'tests' => [['stdin'=>"15\n",'expected'=>"FizzBuzz"],['stdin'=>"7\n",'expected'=>"7"]]],
        ['title' => 'НОД', 'prompt' => 'Прочитайте A B. Выведите НОД.', 'tests' => [['stdin'=>"12 18\n",'expected'=>"6"],['stdin'=>"7 13\n",'expected'=>"1"]]],
        ['title' => 'Сумма цифр', 'prompt' => 'Прочитайте число. Выведите сумму его цифр.', 'tests' => [['stdin'=>"12345\n",'expected'=>"15"],['stdin'=>"0\n",'expected'=>"0"]]],
        ['title' => 'Степень двойки', 'prompt' => 'Прочитайте N. YES если степень двойки, NO иначе.', 'tests' => [['stdin'=>"16\n",'expected'=>"YES"],['stdin'=>"10\n",'expected'=>"NO"]]],
        ['title' => 'Количество гласных', 'prompt' => 'Прочитайте строку. Количество гласных (aeiou).', 'tests' => [['stdin'=>"hello world\n",'expected'=>"3"],['stdin'=>"bcdf\n",'expected'=>"0"]]],
        ['title' => 'Разворот массива', 'prompt' => 'Прочитайте N и массив. Выведите в обратном порядке.', 'tests' => [['stdin'=>"5\n1 2 3 4 5\n",'expected'=>"5 4 3 2 1"],['stdin'=>"1\n42\n",'expected'=>"42"]]],
        ['title' => 'Среднее значение', 'prompt' => 'Прочитайте N чисел. Выведите среднее с 2 знаками.', 'tests' => [['stdin'=>"4\n1 2 3 4\n",'expected'=>"2.50"],['stdin'=>"2\n10 0\n",'expected'=>"5.00"]]],
        ['title' => 'Уникальные элементы', 'prompt' => 'Прочитайте N чисел. Выведите уникальные (sorted).', 'tests' => [['stdin'=>"5\n3 1 2 1 3\n",'expected'=>"1 2 3"],['stdin'=>"3\n5 5 5\n",'expected'=>"5"]]],
        ['title' => 'Степень числа', 'prompt' => 'Прочитайте base exp. Выведите base^exp.', 'tests' => [['stdin'=>"2 8\n",'expected'=>"256"],['stdin'=>"3 0\n",'expected'=>"1"]]],
        ['title' => 'Простое число', 'prompt' => 'Прочитайте N. YES если простое, NO иначе.', 'tests' => [['stdin'=>"17\n",'expected'=>"YES"],['stdin'=>"15\n",'expected'=>"NO"]]],
        ['title' => 'Бинарный поиск', 'prompt' => 'Прочитайте N target, sorted массив. Индекс или -1.', 'tests' => [['stdin'=>"5 3\n1 2 3 4 5\n",'expected'=>"2"],['stdin'=>"5 6\n1 2 3 4 5\n",'expected'=>"-1"]]],
        ['title' => 'Скобки', 'prompt' => 'Прочитайте строку ()[]{}. YES если корректна.', 'tests' => [['stdin'=>"()[]{}\n",'expected'=>"YES"],['stdin'=>"([)]\n",'expected'=>"NO"]]],
        ['title' => 'ROT13', 'prompt' => 'Прочитайте строку. Примените ROT13.', 'tests' => [['stdin'=>"Hello\n",'expected'=>"Uryyb"],['stdin'=>"Uryyb\n",'expected'=>"Hello"]]],
        ['title' => 'Числа Фибоначчи до N', 'prompt' => 'Прочитайте N. Выведите числа Фибоначчи <= N через пробел.', 'tests' => [['stdin'=>"20\n",'expected'=>"0 1 1 2 3 5 8 13"],['stdin'=>"1\n",'expected'=>"0 1 1"]]],
        ['title' => 'Конвертация систем счисления', 'prompt' => 'Прочитайте число в десятичной. Выведите в двоичной.', 'tests' => [['stdin'=>"10\n",'expected'=>"1010"],['stdin'=>"255\n",'expected'=>"11111111"]]],
        ['title' => 'Сортировка слиянием', 'prompt' => 'Прочитайте N и массив. Выведите отсортированный.', 'tests' => [['stdin'=>"5\n5 3 8 1 9\n",'expected'=>"1 3 5 8 9"],['stdin'=>"3\n3 2 1\n",'expected'=>"1 2 3"]]],
    ];
}

// ---------------------------------------------------------------------------
//  PATTERNS LESSONS (курс №18)
// ---------------------------------------------------------------------------

function lp_patterns_lessons(): array
{
    $patterns = [
        ['name' => 'Singleton', 'category' => 'Порождающий', 'practice' => 'Реализуйте Singleton-счётчик. Команды INC/GET.', 'tests' => [['stdin'=>"INC\nINC\nGET\n",'expected'=>"2"],['stdin'=>"GET\n",'expected'=>"0"]]],
        ['name' => 'Factory Method', 'category' => 'Порождающий', 'practice' => 'Прочитайте тип фигуры (circle r / rect w h). Выведите площадь с 2 знаками.', 'tests' => [['stdin'=>"circle 5\n",'expected'=>"78.54"],['stdin'=>"rect 3 4\n",'expected'=>"12.00"]]],
        ['name' => 'Builder', 'category' => 'Порождающий', 'practice' => 'Прочитайте поля name=X, age=Y в любом порядке. Выведите: Name: X, Age: Y.', 'tests' => [['stdin'=>"name=Alice\nage=30\n",'expected'=>"Name: Alice, Age: 30"],['stdin'=>"age=25\nname=Bob\n",'expected'=>"Name: Bob, Age: 25"]]],
        ['name' => 'Prototype', 'category' => 'Порождающий', 'practice' => 'Прочитайте JSON объект. Выведите его копию с добавленным полем cloned=true.', 'tests' => [['stdin'=>'{"x":1}\n','expected'=>'{"x":1,"cloned":true}'],['stdin'=>'{"a":"b"}\n','expected'=>'{"a":"b","cloned":true}']]],
        ['name' => 'Adapter', 'category' => 'Структурный', 'practice' => 'Конвертируйте температуру. Прочитайте число и C/F. Выведите в противоположной шкале с 2 знаками.', 'tests' => [['stdin'=>"100 C\n",'expected'=>"212.00 F"],['stdin'=>"32 F\n",'expected'=>"0.00 C"]]],
        ['name' => 'Decorator', 'category' => 'Структурный', 'practice' => 'Прочитайте строку и список декораторов (upper/lower/reverse). Примените последовательно.', 'tests' => [['stdin'=>"hello\nupper\nreverse\n",'expected'=>"OLLEH"],['stdin'=>"World\nlower\n",'expected'=>"world"]]],
        ['name' => 'Facade', 'category' => 'Структурный', 'practice' => 'Прочитайте команды: connect host, query SQL, disconnect. Выведите OK для каждой.', 'tests' => [['stdin'=>"connect db.local\nquery SELECT 1\ndisconnect\n",'expected'=>"OK\nOK\nOK"],['stdin'=>"connect x\n",'expected'=>"OK"]]],
        ['name' => 'Proxy', 'category' => 'Структурный', 'practice' => 'Прочитайте N запросов. Кэшируйте: повторный запрос → CACHED, иначе MISS. Выведите для каждого.', 'tests' => [['stdin'=>"4\nfoo\nbar\nfoo\nbaz\n",'expected'=>"MISS\nMISS\nCACHED\nMISS"],['stdin'=>"2\nx\nx\n",'expected'=>"MISS\nCACHED"]]],
        ['name' => 'Observer', 'category' => 'Поведенческий', 'practice' => 'Прочитайте команды SUBSCRIBE name / PUBLISH event. При PUBLISH вывести Notified: name для каждого подписчика.', 'tests' => [['stdin'=>"SUBSCRIBE Alice\nSUBSCRIBE Bob\nPUBLISH click\n",'expected'=>"Notified: Alice\nNotified: Bob"],['stdin'=>"PUBLISH event\n",'expected'=>""]]],
        ['name' => 'Strategy', 'category' => 'Поведенческий', 'practice' => 'Прочитайте strategy (bubble/quick) и массив. Выведите sorted массив.', 'tests' => [['stdin'=>"bubble\n5\n5 3 1 4 2\n",'expected'=>"1 2 3 4 5"],['stdin'=>"quick\n3\n9 7 8\n",'expected'=>"7 8 9"]]],
        ['name' => 'Command', 'category' => 'Поведенческий', 'practice' => 'Обработайте команды ADD x / UNDO / PRINT. ADD добавляет, UNDO отменяет последнее ADD, PRINT выводит текущий список.', 'tests' => [['stdin'=>"ADD 1\nADD 2\nUNDO\nADD 3\nPRINT\n",'expected'=>"1 3"],['stdin'=>"PRINT\n",'expected'=>""]]],
        ['name' => 'Iterator', 'category' => 'Поведенческий', 'practice' => 'Прочитайте N чисел. Выведите чётные по порядку, затем нечётные.', 'tests' => [['stdin'=>"6\n1 2 3 4 5 6\n",'expected'=>"2 4 6\n1 3 5"],['stdin'=>"3\n1 3 5\n",'expected'=>"\n1 3 5"]]],
        ['name' => 'Template Method', 'category' => 'Поведенческий', 'practice' => 'Прочитайте format (csv/json) и данные name,age. Выведите в нужном формате.', 'tests' => [['stdin'=>"csv\nAlice,30\n",'expected'=>"name,age\nAlice,30"],['stdin'=>"json\nBob,25\n",'expected'=>'{"name":"Bob","age":"25"}']]],
        ['name' => 'State', 'category' => 'Поведенческий', 'practice' => 'Светофор. Команды NEXT (следующее состояние). Начало: RED → GREEN → YELLOW → RED... Выведите состояние после каждой NEXT.', 'tests' => [['stdin'=>"3\nNEXT\nNEXT\nNEXT\n",'expected'=>"GREEN\nYELLOW\nRED"],['stdin'=>"1\nNEXT\n",'expected'=>"GREEN"]]],
        ['name' => 'Chain of Responsibility', 'category' => 'Поведенческий', 'practice' => 'Прочитайте число. Обработчики: если >100 → HIGH, >50 → MEDIUM, иначе LOW.', 'tests' => [['stdin'=>"150\n",'expected'=>"HIGH"],['stdin'=>"60\n",'expected'=>"MEDIUM"],['stdin'=>"10\n",'expected'=>"LOW"]]],
        ['name' => 'SOLID: Single Responsibility', 'category' => 'Принципы', 'practice' => 'Прочитайте строку. Выведите её без пробелов и её длину через пробел.', 'tests' => [['stdin'=>"hello world\n",'expected'=>"helloworld 10"],['stdin'=>"abc\n",'expected'=>"abc 3"]]],
        ['name' => 'SOLID: Open/Closed', 'category' => 'Принципы', 'practice' => 'Прочитайте тип скидки (student/senior/none) и цену. Примените: student -20%, senior -15%, none 0%. Выведите итог.', 'tests' => [['stdin'=>"student 100\n",'expected'=>"80.00"],['stdin'=>"none 200\n",'expected'=>"200.00"]]],
        ['name' => 'SOLID: Dependency Inversion', 'category' => 'Принципы', 'practice' => 'Прочитайте logger type (file/console) и сообщение. Выведите [FILE] msg или [CONSOLE] msg.', 'tests' => [['stdin'=>"file Hello\n",'expected'=>"[FILE] Hello"],['stdin'=>"console Error\n",'expected'=>"[CONSOLE] Error"]]],
        ['name' => 'DRY и рефакторинг', 'category' => 'Принципы', 'practice' => 'Прочитайте N пар a b. Для каждой выведите max, min, sum.', 'tests' => [['stdin'=>"2\n3 7\n-1 5\n",'expected'=>"7 3 10\n5 -1 4"],['stdin'=>"1\n0 0\n",'expected'=>"0 0 0"]]],
        ['name' => 'Capstone: Design Review', 'category' => 'Итог', 'practice' => 'Реализуйте простой Event Bus. SUBSCRIBE topic name / PUBLISH topic msg / LIST topic — вывести подписчиков.', 'tests' => [['stdin'=>"SUBSCRIBE news Alice\nSUBSCRIBE news Bob\nPUBLISH news Hello\nLIST news\n",'expected'=>"Alice received: Hello\nBob received: Hello\nAlice Bob"],['stdin'=>"LIST unknown\n",'expected'=>""]]],
    ];

    $lessons = [];
    foreach ($patterns as $i => $p) {
        $lessons[] = [
            'title'           => 'Урок ' . ($i + 1) . '. ' . $p['name'] . ' [' . $p['category'] . ']',
            'theory'          => "Паттерн {$p['name']} ({$p['category']}): назначение, UML-диаграмма, когда применять, пример кода и типичные ошибки при реализации.",
            'practice_title'  => $p['name'] . ': практика',
            'practice_prompt' => $p['practice'],
            'tests'           => $p['tests'],
        ];
    }
    return $lessons;
}

// ---------------------------------------------------------------------------
//  API LESSONS (курс №19)
// ---------------------------------------------------------------------------

function lp_api_lessons(): array
{
    $raw = [
        ['REST принципы', 'Stateless, Client-Server, Cacheable, Layered. Уровни зрелости Richardson.', 'Прочитайте HTTP-метод и путь. Выведите идеальный статус-код для успешного ответа.', [['stdin'=>"GET /users\n",'expected'=>"200"],['stdin'=>"POST /users\n",'expected'=>"201"]]],
        ['HTTP методы и семантика', 'GET, POST, PUT, PATCH, DELETE. Идемпотентность. Safe methods.', 'Прочитайте метод. Выведите idempotent если идемпотентный, safe если safe, иначе none.', [['stdin'=>"GET\n",'expected'=>"safe"],['stdin'=>"DELETE\n",'expected'=>"idempotent"],['stdin'=>"POST\n",'expected'=>"none"]]],
        ['Статус-коды HTTP', '2xx, 3xx, 4xx, 5xx. Когда 404 vs 400. 422 vs 400.', 'Прочитайте описание ошибки. Выведите правильный статус-код.', [['stdin'=>"not found\n",'expected'=>"404"],['stdin'=>"unauthorized\n",'expected'=>"401"],['stdin'=>"server error\n",'expected'=>"500"]]],
        ['URI проектирование', 'Ресурсы vs действия. Иерархия. Именование (snake_case, kebab-case, plural).', 'Прочитайте URI. Выведите valid если соответствует REST best practices, invalid иначе.', [['stdin'=>"/users/1/orders\n",'expected'=>"valid"],['stdin'=>"/getUser\n",'expected'=>"invalid"]]],
        ['Версионирование API', 'URL versioning, Header versioning, Content negotiation. Семантическое версионирование.', 'Прочитайте URL. Извлеките номер версии (v1, v2...) или NO_VERSION.', [['stdin'=>"/api/v2/users\n",'expected'=>"v2"],['stdin'=>"/api/users\n",'expected'=>"NO_VERSION"]]],
        ['Аутентификация: Basic и API Key', 'Basic Auth, API Key в заголовке/параметре. Преимущества и риски.', 'Декодируйте Base64 строку (формат user:password). Выведите user и password.', [['stdin'=>"dXNlcjpwYXNz\n",'expected'=>"user pass"],['stdin'=>"YWRtaW46MTIz\n",'expected'=>"admin 123"]]],
        ['JWT: структура и валидация', 'Header.Payload.Signature. Claims: iss, sub, exp, iat. Алгоритмы: HS256, RS256.', 'Прочитайте now iat exp. Выведите VALID, EXPIRED или NOT_YET.', [['stdin'=>"1700000100 1700000000 1700003600\n",'expected'=>"VALID"],['stdin'=>"1700004000 1700000000 1700003600\n",'expected'=>"EXPIRED"]]],
        ['OAuth 2.0', 'Flows: Authorization Code, Client Credentials, Implicit (deprecated). Scopes. Refresh token.', 'Прочитайте grant_type. Выведите подходящий flow.', [['stdin'=>"authorization_code\n",'expected'=>"Authorization Code Flow"],['stdin'=>"client_credentials\n",'expected'=>"Client Credentials Flow"]]],
        ['CORS и безопасность', 'Same-Origin Policy. CORS заголовки. Preflight запросы. CSRF protection.', 'Прочитайте origin и allowed (yes/no). Выведите Access-Control-Allow-Origin заголовок или BLOCKED.', [['stdin'=>"https://app.com yes\n",'expected'=>"Access-Control-Allow-Origin: https://app.com"],['stdin'=>"https://evil.com no\n",'expected'=>"BLOCKED"]]],
        ['Rate Limiting', 'Алгоритмы: Token Bucket, Leaky Bucket, Fixed Window, Sliding Window. Заголовки X-RateLimit.', 'Прочитайте limit, window(сек) и N временных меток запросов. Выведите ALLOWED или BLOCKED для каждого.', [['stdin'=>"3 10\n4\n0 1 2 5 12\n",'expected'=>"ALLOWED\nALLOWED\nALLOWED\nBLOCKED\nALLOWED"],['stdin'=>"1 5\n2\n0 3\n",'expected'=>"ALLOWED\nBLOCKED"]]],
        ['Пагинация', 'Offset, Cursor-based, Page-based. Link заголовки. Cons и pros каждого.', 'Прочитайте total, page, per_page. Выведите has_next (true/false) и total_pages.', [['stdin'=>"100 1 10\n",'expected'=>"true 10"],['stdin'=>"15 2 10\n",'expected'=>"false 2"]]],
        ['Фильтрация и сортировка', '?filter=, ?sort=, ?fields= (sparse fieldsets). Безопасность: инъекции.', 'Прочитайте строку запроса ?key=value&key2=value2. Выведите пары key: value (sorted by key).', [['stdin'=>"?status=active&sort=name\n",'expected'=>"sort: name\nstatus: active"],['stdin'=>"?page=2\n",'expected'=>"page: 2"]]],
        ['OpenAPI и Swagger', 'YAML/JSON спецификация. Генерация кода. Документация. Валидация.', 'Прочитайте endpoint метод статус. Сформируйте краткое OpenAPI описание в формате METHOD /endpoint -> STATUS.', [['stdin'=>"GET /users 200\n",'expected'=>"GET /users -> 200"],['stdin'=>"POST /orders 201\n",'expected'=>"POST /orders -> 201"]]],
        ['Обработка ошибок', 'Problem Details (RFC 7807). Стандартизированные ошибки. Логирование.', 'Прочитайте type и detail. Выведите JSON error объект.', [['stdin'=>"not_found Resource missing\n",'expected'=>'{"type":"not_found","detail":"Resource missing"}'],['stdin'=>"validation Invalid email\n",'expected'=>'{"type":"validation","detail":"Invalid email"}']]],
        ['Кэширование', 'Cache-Control, ETag, Last-Modified. Stale-while-revalidate. CDN.', 'Прочитайте max-age (секунды). Выведите Cache-Control заголовок.', [['stdin'=>"3600\n",'expected'=>"Cache-Control: max-age=3600, public"],['stdin'=>"0\n",'expected'=>"Cache-Control: no-store"]]],
        ['Webhooks', 'Push vs Pull. Подпись HMAC. Retry логика. Идемпотентность через идентификатор события.', 'Прочитайте secret и payload. Выведите HMAC-SHA256 первые 8 символов (или эмуляцию: hash первых 8 символов sha256).', [['stdin'=>"secret hello\n",'expected'=>"88aab3ed"],['stdin'=>"key data\n",'expected'=>"a6ea0f5d"]]],
        ['GraphQL vs REST', 'Over-fetching, under-fetching. Scheme, Query, Mutation, Subscription. N+1 проблема.', 'Прочитайте тип (rest/graphql) и сценарий. Выведите рекомендацию.', [['stdin'=>"rest simple_crud\n",'expected'=>"REST: простой CRUD — хороший выбор"],['stdin'=>"graphql complex_nested\n",'expected'=>"GraphQL: сложные вложенные данные — хороший выбор"]]],
        ['Idempotency Keys', 'Идемпотентность POST через X-Idempotency-Key. Хранение ответов. Retry безопасность.', 'Прочитайте N операций: KEY payload. Выведите NEW или DUPLICATE для каждой.', [['stdin'=>"3\nkey1 pay\nkey2 pay\nkey1 pay\n",'expected'=>"NEW\nNEW\nDUPLICATE"],['stdin'=>"2\nabc x\nabc y\n",'expected'=>"NEW\nDUPLICATE"]]],
        ['API Gateway', 'Функции: роутинг, аутентификация, rate limiting, logging. Kong, AWS API Gateway, Nginx.', 'Прочитайте N запросов path. Выведите backend сервис по правилам: /users* → user-service, /orders* → order-service, иначе → not-found.', [['stdin'=>"3\n/users/1\n/orders/5\n/health\n",'expected'=>"user-service\norder-service\nnot-found"],['stdin'=>"1\n/users\n",'expected'=>"user-service"]]],
        ['Capstone: API Review', 'Аудит спроектированного API. Чеклист: безопасность, производительность, документация.', 'Прочитайте N пар [метод URI]. Проверьте REST conventions: выведите OK или ISSUE с пояснением.', [['stdin'=>"3\nGET /users\nPOST /createUser\nDELETE /users/1\n",'expected'=>"OK\nISSUE: action in URI\nOK"],['stdin'=>"1\nGET /get-all-items\n",'expected'=>"ISSUE: action in URI"]]],
    ];

    $lessons = [];
    foreach ($raw as $i => [$title, $theory, $prompt, $tests]) {
        $lessons[] = [
            'title'           => 'Урок ' . ($i + 1) . '. ' . $title,
            'theory'          => $theory,
            'practice_title'  => $title,
            'practice_prompt' => $prompt,
            'tests'           => $tests,
        ];
    }
    return $lessons;
}

// ---------------------------------------------------------------------------
//  SECURITY LESSONS (курс №20)
// ---------------------------------------------------------------------------

function lp_security_lessons(): array
{
    $raw = [
        ['OWASP Top 10 обзор', 'A01-A10: Broken Access Control, Cryptographic Failures, Injection, Insecure Design и др.', 'Сопоставьте уязвимость и OWASP категорию. Прочитайте vuln name. Выведите A01..A10.', [['stdin'=>"sql_injection\n",'expected'=>"A03"],['stdin'=>"broken_access\n",'expected'=>"A01"]]],
        ['SQL Injection', 'Union-based, Blind, Time-based. Prepared statements. ORM защита.', 'Проверьте ввод на SQL-инъекцию. Прочитайте строку. SAFE если безопасна, INJECT если опасна.', [['stdin'=>"hello world\n",'expected'=>"SAFE"],['stdin'=>"1 OR 1=1\n",'expected'=>"INJECT"],['stdin'=>"admin'--\n",'expected'=>"INJECT"]]],
        ['XSS: Reflected и Stored', 'Reflected XSS через URL, Stored в БД. Контекст-зависимое экранирование. CSP.', 'Прочитайте HTML-строку. Выведите её с экранированием < > & " \'.', [['stdin'=>"<script>alert(1)</script>\n",'expected'=>"&lt;script&gt;alert(1)&lt;/script&gt;"],['stdin'=>"hello & \"world\"\n",'expected'=>"hello &amp; &quot;world&quot;"]]],
        ['CSRF атаки', 'Cross-Site Request Forgery. CSRF token. SameSite cookies. Double-submit pattern.', 'Прочитайте token и expected. VALID если совпадают, INVALID иначе.', [['stdin'=>"abc123 abc123\n",'expected'=>"VALID"],['stdin'=>"abc123 xyz789\n",'expected'=>"INVALID"]]],
        ['Аутентификация: пароли', 'Bcrypt, Argon2, scrypt. Салт. Rainbow tables. PBKDF2. Итерации.', 'Прочитайте пароль. Выведите WEAK если длина < 8 или нет цифры, STRONG иначе.', [['stdin'=>"pass\n",'expected'=>"WEAK"],['stdin'=>"MyPass1!\n",'expected'=>"STRONG"],['stdin'=>"longpassword\n",'expected'=>"WEAK"]]],
        ['JWT уязвимости', 'Algorithm confusion (none, RS256→HS256). Weak secret. JWT leakage.', 'Прочитайте алгоритм из JWT заголовка. Если none или пустой — VULNERABLE, иначе OK.', [['stdin'=>"none\n",'expected'=>"VULNERABLE"],['stdin'=>"HS256\n",'expected'=>"OK"],['stdin'=>"\n",'expected'=>"VULNERABLE"]]],
        ['HTTPS и TLS', 'TLS 1.2 vs 1.3. Certificate chain. HSTS. Certificate pinning. Mixed content.', 'Прочитайте URL. Выведите SECURE если https://, INSECURE если http://.', [['stdin'=>"https://api.example.com\n",'expected'=>"SECURE"],['stdin'=>"http://old.example.com\n",'expected'=>"INSECURE"]]],
        ['SSRF уязвимости', 'Server-Side Request Forgery. Внутренние сети. Cloud metadata. Blocklist vs allowlist.', 'Прочитайте URL для fetch. BLOCKED если ведёт на 169.254.x.x, localhost или 192.168.x.x, иначе OK.', [['stdin'=>"http://169.254.169.254/latest/meta-data\n",'expected'=>"BLOCKED"],['stdin'=>"https://api.example.com/data\n",'expected'=>"OK"]]],
        ['Шифрование: симметричное', 'AES-128/256. CBC vs GCM. IV/Nonce. Padding. Key management.', 'Реализуйте простой XOR шифр. Прочитайте текст и ключ (1 символ). Выведите hex.', [['stdin'=>"ABC\nK\n",'expected'=>"0a09 08"],['stdin'=>"Hi\nA\n",'expected'=>"09 28"]]],
        ['Шифрование: асимметричное', 'RSA, ECC. Публичный/приватный ключ. Электронная подпись. PGP/GPG.', 'Прочитайте сообщение. Выведите его Base64.', [['stdin'=>"Hello\n",'expected'=>"SGVsbG8="],['stdin'=>"API\n",'expected'=>"QVBJ"]]],
        ['Безопасные заголовки', 'CSP, HSTS, X-Frame-Options, X-Content-Type-Options, Referrer-Policy.', 'Прочитайте заголовок. Выведите SECURITY_HEADER если это заголовок безопасности, иначе OTHER.', [['stdin'=>"Content-Security-Policy\n",'expected'=>"SECURITY_HEADER"],['stdin'=>"Content-Type\n",'expected'=>"OTHER"]]],
        ['IDOR уязвимости', 'Insecure Direct Object Reference. Горизонтальная эскалация привилегий. Авторизация на уровне объекта.', 'Прочитайте user_id и resource_owner_id. ALLOWED если совпадают, FORBIDDEN иначе.', [['stdin'=>"42 42\n",'expected'=>"ALLOWED"],['stdin'=>"42 99\n",'expected'=>"FORBIDDEN"]]],
        ['Rate Limiting и брутфорс', 'Защита от брутфорса: экспоненциальная задержка, captcha, MFA. Account lockout.', 'Прочитайте N попыток и порог. Если N >= порога — BLOCKED, иначе OK.', [['stdin'=>"5 5\n",'expected'=>"BLOCKED"],['stdin'=>"4 5\n",'expected'=>"OK"]]],
        ['Аудит и логирование', 'Что логировать: события авторизации, изменения данных. Защита логов. SIEM.', 'Прочитайте N событий (login/fail/access). Выведите: success_logins, failures, access_events.', [['stdin'=>"5\nlogin\nfail\nlogin\naccess\nfail\n",'expected'=>"2 2 1"],['stdin'=>"3\nlogin\nlogin\nlogin\n",'expected'=>"3 0 0"]]],
        ['Зависимости и SCA', 'Software Composition Analysis. CVE базы. SBOM. Dependabot/Snyk. Patch management.', 'Прочитайте версию пакета и CVE_fixed_version. Выведите VULNERABLE если версия < CVE_fixed, иначе SAFE.', [['stdin'=>"1.2.3 1.3.0\n",'expected'=>"VULNERABLE"],['stdin'=>"2.0.0 1.9.9\n",'expected'=>"SAFE"]]],
        ['Secrets управление', 'Vault, AWS Secrets Manager. Rotation. Не хранить в коде. .env и .gitignore.', 'Прочитайте N строк кода. Для каждой выведите LEAKED если содержит password= или api_key=, иначе OK.', [['stdin'=>"3\nconst x = 1\npassword=secret123\nconst url = env.URL\n",'expected'=>"OK\nLEAKED\nOK"],['stdin'=>"1\napi_key=abc\n",'expected'=>"LEAKED"]]],
        ['Penetration Testing', 'Reconnaissance, Scanning, Exploitation, Post-exploitation. OWASP WSTG. Bug bounty.', 'Прочитайте IP-адрес. Выведите INTERNAL если 192.168.x.x, 10.x.x.x или 172.16-31.x.x, иначе EXTERNAL.', [['stdin'=>"192.168.1.100\n",'expected'=>"INTERNAL"],['stdin'=>"8.8.8.8\n",'expected'=>"EXTERNAL"],['stdin'=>"10.0.0.1\n",'expected'=>"INTERNAL"]]],
        ['Безопасность API', 'Input validation, schema validation, output filtering. API keys rotation. mTLS.', 'Прочитайте JSON. Выведите VALID если все обязательные поля (name, email) присутствуют, иначе INVALID.', [['stdin'=>'{"name":"Alice","email":"a@b.com"}\n','expected'=>"VALID"],['stdin'=>'{"name":"Bob"}\n','expected'=>"INVALID"]]],
        ['Cloud Security', 'IAM least privilege. S3 bucket policies. VPC. Security groups. CloudTrail.', 'Прочитайте permission и resource. Выведите LEAST_PRIVILEGE если permission не содержит *, иначе OVERPRIVILEGED.', [['stdin'=>"s3:GetObject arn:aws:s3:::bucket/*\n",'expected'=>"OVERPRIVILEGED"],['stdin'=>"s3:GetObject arn:aws:s3:::bucket/file.txt\n",'expected'=>"LEAST_PRIVILEGE"]]],
        ['Capstone: Security Audit', 'Полный аудит приложения: поверхность атаки, обнаруженные уязвимости, рекомендации.', 'Прочитайте N проверок (format: check_name status ok/fail). Выведите суммарный отчёт: passed/total и список failed.', [['stdin'=>"4\nxss ok\nsqli fail\ncsrf ok\nauth fail\n",'expected'=>"passed: 2/4\nfailed: sqli auth"],['stdin'=>"2\nhsts ok\ncors ok\n",'expected'=>"passed: 2/2\nfailed: none"]]],
    ];

    $lessons = [];
    foreach ($raw as $i => [$title, $theory, $prompt, $tests]) {
        $lessons[] = [
            'title'           => 'Урок ' . ($i + 1) . '. ' . $title,
            'theory'          => $theory,
            'practice_title'  => $title,
            'practice_prompt' => $prompt,
            'tests'           => $tests,
        ];
    }
    return $lessons;
}

// ---------------------------------------------------------------------------
//  EXAM QUESTIONS
// ---------------------------------------------------------------------------

function lp_exam_python(): array
{
    $topics = ['переменные и типы', 'условия', 'циклы for/while', 'строки', 'списки', 'словари', 'функции', 'рекурсия', 'ООП', 'исключения', 'файлы', 'модули', 'итераторы', 'декораторы', 'генераторы', 'lambda', 'list comprehension', 'замыкания', 'контекстные менеджеры', 'тестирование'];
    return lp_build_exam('Python: с нуля до Junior', $topics);
}

function lp_exam_js(): array
{
    $topics = ['var/let/const', 'типы данных', 'функции', 'стрелочные функции', 'массивы map/filter', 'объекты', 'деструктуризация', 'замыкания', 'прототипы', 'классы', 'промисы', 'async/await', 'fetch', 'DOM', 'события', 'ES6 модули', 'LocalStorage', 'итераторы', 'Proxy', 'Jest тестирование'];
    return lp_build_exam('JavaScript: DOM и современный JS', $topics);
}

function lp_exam_sql(): array
{
    $topics = ['SELECT и WHERE', 'ORDER BY и LIMIT', 'GROUP BY и HAVING', 'INNER JOIN', 'LEFT JOIN', 'подзапросы', 'CTE', 'оконные функции ROW_NUMBER', 'оконные функции LAG/LEAD', 'индексы B-Tree', 'транзакции', 'ACID', 'нормализация', 'INSERT/UPDATE/DELETE', 'представления', 'хранимые процедуры', 'блокировки', 'партиционирование', 'EXPLAIN', 'репликация'];
    return lp_build_exam('SQL: запросы и оптимизация', $topics);
}

function lp_exam_cpp(): array
{
    $topics = ['типы данных', 'циклы', 'массивы', 'строки', 'функции', 'STL sort', 'STL map', 'STL set', 'priority_queue', 'два указателя', 'бинарный поиск', 'стек', 'BFS', 'DFS', 'алгоритм Дейкстры', 'DP Coin Change', 'DP LIS', 'жадный', 'сегментное дерево', 'хеширование строк'];
    return lp_build_exam('C++: алгоритмы и структуры данных', $topics);
}

function lp_exam_algo(): array
{
    $topics = ['Two Sum', 'Valid Parentheses', 'Merge Intervals', 'Sliding Window', 'Product Except Self', 'Kadane', 'Binary Search', 'Linked List', 'BFS/DFS', 'Coin Change DP', 'Number of Islands', 'Jump Game', 'Backtracking', 'Top K Elements', 'Trapping Rain Water', 'House Robber', 'LCS', 'Word Search', 'Topological Sort', 'Median of Arrays'];
    return lp_build_exam('Алгоритмы: подготовка к интервью', $topics);
}

function lp_exam_generic(string $courseTitle, array $topics): array
{
    return lp_build_exam($courseTitle, $topics);
}

function lp_build_exam(string $courseTitle, array $topics): array
{
    $n = count($topics);
    $questions = [];

    for ($i = 0; $i < 30; $i++) {
        $correct = $topics[$i % $n];
        // 3 дистрактора
        $distractors = [];
        for ($d = 1; count($distractors) < 3; $d++) {
            $candidate = $topics[($i + $d * 3) % $n];
            if ($candidate !== $correct && !in_array($candidate, $distractors, true)) {
                $distractors[] = $candidate;
            }
        }
        $options = [$correct, ...$distractors];
        // Перемешиваем детерминированно
        $shift = $i % 4;
        $options = array_merge(array_slice($options, $shift), array_slice($options, 0, $shift));

        $questionTemplates = [
            "Какой раздел курса «{$courseTitle}» посвящён теме «{$correct}»?",
            "В курсе «{$courseTitle}» тема «{$correct}» изучается в каком модуле?",
            "Выберите модуль, который наиболее точно соответствует теме «{$correct}».",
            "Для закрепления навыка «{$correct}» нужно пройти раздел:",
        ];

        $questions[] = [
            'type'           => 'mc_single',
            'question'       => $questionTemplates[$i % count($questionTemplates)],
            'options'        => $options,
            'correct_answer' => $correct,
        ];
    }

    return $questions;
}

// ---------------------------------------------------------------------------
//  ROADMAP BUILDER
// ---------------------------------------------------------------------------

function lp_build_roadmap_topics(string $courseTitle): array
{
    // Извлекаем топики из урока
    return [];  // будет заполнено из уроков при сидировании
}

// ---------------------------------------------------------------------------
//  MAIN SEEDER FUNCTION
// ---------------------------------------------------------------------------

/**
 * Главная точка входа — запустить из bootstrap или artisan.
 */
function lp_seed_all(PDO $pdo): array
{
    lp_ensure_schema($pdo);
    $result = [];

    $catalog = lp_courses_catalog();

    foreach ($catalog as $courseDef) {
        $res = lp_upsert_course($pdo, $courseDef);
        $result[] = $res;

        if ($res['ok']) {
            // Roadmap для каждого курса
            $rmRes = lp_upsert_roadmap($pdo, $courseDef, $res['course_id']);
            $result[] = $rmRes;
        }
    }

    return $result;
}

// ---------------------------------------------------------------------------
//  UPSERT COURSE
// ---------------------------------------------------------------------------

function lp_upsert_course(PDO $pdo, array $def): array
{
    $title = trim((string) ($def['title'] ?? ''));
    if ($title === '') {
        return ['ok' => false, 'error' => 'missing_title'];
    }

    $stmt = $pdo->prepare("SELECT id FROM courses WHERE title = ? LIMIT 1");
    $stmt->execute([$title]);
    $courseId = (int) ($stmt->fetchColumn() ?: 0);
    $created  = false;

    $pdo->beginTransaction();
    try {
        if ($courseId <= 0) {
            $pdo->prepare("INSERT INTO courses (title, instructor, description, category, image_url, created_at) VALUES (?,?,?,?,?,NOW())")
                ->execute([$title, $def['instructor'] ?? '', $def['description'] ?? '', $def['category'] ?? 'other', $def['image'] ?? '']);
            $courseId = (int) $pdo->lastInsertId();
            $created  = true;
        } else {
            $pdo->prepare("UPDATE courses SET instructor=?, description=?, category=?, image_url=?, updated_at=NOW() WHERE id=?")
                ->execute([$def['instructor'] ?? '', $def['description'] ?? '', $def['category'] ?? 'other', $def['image'] ?? '', $courseId]);

            // Очищаем зависимые данные
            $lessonIds = $pdo->query("SELECT id FROM lessons WHERE course_id={$courseId}")->fetchAll(PDO::FETCH_COLUMN);
            if ($lessonIds) {
                $ph = implode(',', array_fill(0, count($lessonIds), '?'));
                $pdo->prepare("DELETE FROM lesson_practice_tasks WHERE lesson_id IN ({$ph})")->execute($lessonIds);
            }
            $pdo->prepare("DELETE FROM lessons WHERE course_id=?")->execute([$courseId]);
            $pdo->prepare("DELETE FROM course_skills WHERE course_id=?")->execute([$courseId]);
            $pdo->prepare("DELETE FROM course_exams WHERE course_id=?")->execute([$courseId]);
        }

        // Навыки
        foreach (array_unique(array_filter((array) ($def['skills'] ?? []))) as $skill) {
            $pdo->prepare("INSERT INTO course_skills (course_id, skill_name, skill_level) VALUES (?,?,0)")->execute([$courseId, $skill]);
        }

        // Уроки
        $orderNum = 0;
        $lessonTopics = [];
        foreach ((array) ($def['lessons'] ?? []) as $lesson) {
            $lessonTitle = trim((string) ($lesson['title'] ?? ''));
            if ($lessonTitle === '') continue;

            $lessonTopics[] = $lessonTitle;

            $pdo->prepare("INSERT INTO lessons (course_id, title, type, content, video_url, materials_title, materials_url, order_num, created_at) VALUES (?,?,?,?,?,?,?,?,NOW())")
                ->execute([
                    $courseId, $lessonTitle, 'article',
                    (string) ($lesson['theory'] ?? ''),
                    '', '', '', $orderNum++,
                ]);
            $lessonId = (int) $pdo->lastInsertId();

            // Практика
            lp_insert_practice($pdo, $lessonId, $lesson, (string) ($def['lang'] ?? 'python'));
        }

        // Экзамен
        $examQuestions = $def['exam_questions'] ?? [];
        if (!empty($examQuestions)) {
            $pdo->prepare("INSERT INTO course_exams (course_id, exam_json, time_limit_minutes, pass_percent, shuffle_questions, shuffle_options, created_at) VALUES (?,?,?,?,1,1,NOW())")
                ->execute([$courseId, lp_json($examQuestions), 60, 70]);
        }

        $pdo->commit();
        return ['ok' => true, 'created' => $created, 'course_id' => $courseId, 'title' => $title, 'type' => 'course'];

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return ['ok' => false, 'error' => $e->getMessage(), 'title' => $title, 'type' => 'course'];
    }
}

function lp_insert_practice(PDO $pdo, int $lessonId, array $lesson, string $defaultLang): void
{
    $tests = $lesson['tests'] ?? [];
    if (empty($tests)) return;

    $lang  = $lesson['lang'] ?? $defaultLang;
    $title = $lesson['practice_title'] ?? '';
    $prompt = $lesson['practice_prompt'] ?? '';

    // Нормализуем тесты: stdin/expected → для code; setup_sql/expected_sql → для SQL
    $normalizedTests = [];
    foreach ($tests as $t) {
        if (isset($t['setup_sql'])) {
            // SQL практика
            $normalizedTests[] = [
                'setup_sql'    => $t['setup_sql'],
                'expected_sql' => $t['expected_sql'] ?? '',
            ];
        } else {
            $normalizedTests[] = [
                'stdin'    => $t['stdin'] ?? '',
                'expected' => $t['expected'] ?? '',
            ];
        }
    }

    $pdo->prepare("INSERT INTO lesson_practice_tasks (lesson_id, language, title, prompt, starter_code, tests_json, is_required) VALUES (?,?,?,?,?,?,1)")
        ->execute([$lessonId, $lang, $title, $prompt, lp_starter($lang), lp_json($normalizedTests)]);
}

// ---------------------------------------------------------------------------
//  UPSERT ROADMAP
// ---------------------------------------------------------------------------

function lp_upsert_roadmap(PDO $pdo, array $courseDef, int $courseId): array
{
    $courseTitle  = (string) ($courseDef['title'] ?? '');
    $roadmapTitle = 'Roadmap: ' . $courseTitle;
    $description  = "Практический роадмап по курсу «{$courseTitle}»: модули, мини-тесты и финальный экзамен.";

    $stmt = $pdo->prepare("SELECT id FROM roadmap_list WHERE title = ? LIMIT 1");
    $stmt->execute([$roadmapTitle]);
    $rmId = (int) ($stmt->fetchColumn() ?: 0);

    if ($rmId > 0) {
        $pdo->prepare("UPDATE roadmap_list SET description=? WHERE id=?")->execute([$description, $rmId]);
    } else {
        $pdo->prepare("INSERT INTO roadmap_list (title, description, created_at) VALUES (?,?,NOW())")->execute([$roadmapTitle, $description]);
        $rmId = (int) $pdo->lastInsertId();
    }

    // Удаляем старые ноды
    $pdo->prepare("DELETE FROM roadmap_nodes WHERE roadmap_title=?")->execute([$roadmapTitle]);

    // Топики из уроков
    $lessons = (array) ($courseDef['lessons'] ?? []);
    $topics  = array_map(fn($l) => (string) ($l['title'] ?? ''), $lessons);
    $topics[] = '🏆 Финальный экзамен';

    $nodeIds = [];
    $cols = 4;
    $xBase = 80; $yBase = 80;
    $xStep = 360; $yStep = 220;

    foreach ($topics as $idx => $topic) {
        $row = (int) floor($idx / $cols);
        $col = $idx % $cols;
        if ($row % 2 === 1) $col = ($cols - 1) - $col;
        $x = $xBase + $col * $xStep;
        $y = $yBase + $row * $yStep;
        $isExam = ($idx === count($topics) - 1) ? 1 : 0;

        $materials = [
            ['title' => "Материалы: {$topic}", 'url' => 'https://roadmap.sh/'],
            ['title' => 'YouTube: видео по теме', 'url' => 'https://www.youtube.com/results?search_query=' . rawurlencode($courseTitle . ' ' . $topic)],
        ];

        $pdo->prepare("INSERT INTO roadmap_nodes (title, roadmap_title, topic, materials, x, y, deps, is_exam) VALUES (?,?,?,?,?,?,?,?)")
            ->execute([$topic, $roadmapTitle, $topic, lp_json($materials), $x, $y, lp_json([]), $isExam]);
        $nodeIds[] = (int) $pdo->lastInsertId();
    }

    // Зависимости (линейно)
    foreach ($nodeIds as $idx => $nodeId) {
        if ($idx > 0) {
            $pdo->prepare("UPDATE roadmap_nodes SET deps=? WHERE id=?")->execute([lp_json([$nodeIds[$idx - 1]]), $nodeId]);
        }

        $topic  = $topics[$idx] ?? 'Узел ' . ($idx + 1);
        $isExam = ($idx === count($topics) - 1);

        $pdo->prepare("INSERT INTO roadmap_lessons (node_id, title, video_url, description, materials, order_index) VALUES (?,?,?,?,?,1)")
            ->execute([
                $nodeId,
                'Шаг ' . ($idx + 1) . '. ' . $topic,
                '',
                $isExam
                    ? "Финальный этап. Повторите ключевые темы и сдайте итоговый экзамен."
                    : "Изучите материалы по теме «{$topic}» и подтвердите прохождение через мини-тест.",
                lp_json([['title' => "Материалы: {$topic}", 'url' => 'https://roadmap.sh/']]),
            ]);

        // Вопросы
        $questions = $isExam
            ? lp_roadmap_final_questions($roadmapTitle, $topics, 20)
            : lp_roadmap_mini_questions($roadmapTitle, $topic, $topics, $idx);

        $insQ = $pdo->prepare("INSERT INTO roadmap_quiz_questions (node_id, question, options, correct_answer) VALUES (?,?,?,?)");
        foreach ($questions as $q) {
            $insQ->execute([$nodeId, $q['question'], lp_json($q['options']), $q['correct_answer']]);
        }
    }

    return ['ok' => true, 'type' => 'roadmap', 'id' => $rmId, 'title' => $roadmapTitle];
}

function lp_roadmap_mini_questions(string $rmTitle, string $topic, array $allTopics, int $idx): array
{
    $pool = array_values(array_unique(array_filter($allTopics)));
    while (count($pool) < 4) $pool[] = 'Дополнительная тема';
    $n = count($pool);

    $options = array_unique([$topic, $pool[($idx+1)%$n], $pool[($idx+2)%$n], $pool[($idx+3)%$n]]);
    $options = array_values(array_slice($options, 0, 4));

    return [
        ['question' => "Какой шаг роадмапа «{$rmTitle}» посвящён теме «{$topic}»?", 'options' => $options, 'correct_answer' => $topic],
        ['question' => "Ожидаемый результат после шага «{$topic}» — это:", 'options' => ['Применить тему в рабочем кейсе', 'Только прочитать теорию', 'Пропустить задание', 'Изучить нерелевантное'], 'correct_answer' => 'Применить тему в рабочем кейсе'],
        ['question' => "Тема «{$topic}» входит в роадмап «{$rmTitle}».", 'options' => ['Верно', 'Неверно'], 'correct_answer' => 'Верно'],
        ['question' => "Лучший источник для изучения «{$topic}»:", 'options' => ['Официальная документация', 'Непроверенные форумы', 'Только видео без практики', 'Устаревшие книги'], 'correct_answer' => 'Официальная документация'],
    ];
}

function lp_roadmap_final_questions(string $rmTitle, array $allTopics, int $count = 20): array
{
    $pool = array_values(array_unique(array_filter($allTopics)));
    while (count($pool) < 4) $pool[] = 'Дополнительная тема';
    $n = count($pool);
    $questions = [];

    for ($i = 0; $i < $count; $i++) {
        $correct = $pool[$i % $n];
        $opts = array_unique([$correct, $pool[($i+2)%$n], $pool[($i+3)%$n], $pool[($i+5)%$n]]);
        $opts = array_values(array_slice($opts, 0, 4));
        $shift = $i % count($opts);
        $opts = array_merge(array_slice($opts, $shift), array_slice($opts, 0, $shift));

        $questions[] = [
            'question'       => "Финальный экзамен «{$rmTitle}»: какой шаг покрывает тему «{$correct}»?",
            'options'        => $opts,
            'correct_answer' => $correct,
        ];
    }
    return $questions;
}

// ---------------------------------------------------------------------------
//  ENTRY POINT
// ---------------------------------------------------------------------------

/**
 * Использование:
 *
 *   $pdo = new PDO('mysql:host=localhost;dbname=yourdb;charset=utf8mb4', 'user', 'pass');
 *   $result = lp_seed_all($pdo);
 *   print_r($result);
 *
 * Функция идемпотентна: повторный запуск обновляет данные без дублирования.
 */

// Если запускается напрямую через CLI:
if (PHP_SAPI === 'cli' && isset($argv[0]) && realpath($argv[0]) === __FILE__) {
    $host   = $argv[1] ?? 'localhost';
    $dbname = $argv[2] ?? 'learning';
    $user   = $argv[3] ?? 'root';
    $pass   = $argv[4] ?? '';

    try {
        $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $results = lp_seed_all($pdo);
        $ok      = count(array_filter($results, fn($r) => $r['ok'] ?? false));
        echo "Готово: {$ok}/" . count($results) . " объектов создано/обновлено.\n";
        foreach ($results as $r) {
            $status = ($r['ok'] ?? false) ? '✓' : '✗';
            $type   = $r['type'] ?? '?';
            $title  = $r['title'] ?? $r['error'] ?? '';
            echo "  {$status} [{$type}] {$title}\n";
        }
    } catch (Throwable $e) {
        echo "Ошибка: " . $e->getMessage() . "\n";
        exit(1);
    }
}
