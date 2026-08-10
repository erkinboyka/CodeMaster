<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonQuiz;
use App\Models\LessonPracticeTask;
use App\Models\CourseExam;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseUpgradeSeeder extends Seeder
{
    public function run(): void
    {
        $this->upgradeLessons();
        $this->seedLessonQuizzes();
        $this->seedPracticeTasks();
        $this->upgradeCourseExams();
    }

    private function upgradeLessons(): void
    {
        $courses = Course::all();

        foreach ($courses as $course) {
            $lessons = $course->lessons()->orderBy('order_num')->get();
            $slug = Str::slug($course->title);

            $modules = $this->getModulesForCourse($course->title);
            $lessonCount = $lessons->count();

            foreach ($lessons as $index => $lesson) {
                $moduleIndex = min(intdiv($index, max(1, intdiv($lessonCount, count($modules)))), count($modules) - 1);
                $module = $modules[$moduleIndex];

                $lesson->update([
                    'description' => $this->getDescription($course->title, $lesson->title),
                    'audio_url' => $lesson->type === 'video' ? "/audio/{$slug}/lesson-" . ($index + 1) . ".mp3" : null,
                    'presentation_url' => "/presentations/{$slug}/lesson-" . ($index + 1) . ".pdf",
                    'duration_minutes' => $lesson->type === 'quiz' ? 10 : ($lesson->type === 'video' ? rand(15, 45) : rand(10, 30)),
                    'difficulty' => $index < 2 ? 'easy' : ($index < 4 ? 'medium' : 'hard'),
                    'module' => $module,
                ]);
            }
        }
    }

    private function seedLessonQuizzes(): void
    {
        $courses = Course::all();

        foreach ($courses as $course) {
            $lessons = $course->lessons()->where('type', '!=', 'quiz')->get();

            foreach ($lessons as $lesson) {
                $quizData = $this->getQuizData($course->title, $lesson->title);

                foreach ($quizData as $index => $q) {
                    LessonQuiz::create([
                        'lesson_id' => $lesson->id,
                        'question_text' => $q['question'],
                        'options_json' => $q['options'],
                        'correct_option' => $q['correct'],
                        'explanation' => $q['explanation'] ?? null,
                        'order_num' => $index + 1,
                    ]);
                }
            }
        }
    }

    private function seedPracticeTasks(): void
    {
        $courses = Course::all();

        foreach ($courses as $course) {
            $lessons = $course->lessons()->where('type', '!=', 'quiz')->get();

            foreach ($lessons as $lesson) {
                $tasks = $this->getPracticeTasks($course->title, $lesson->title);

                foreach ($tasks as $taskData) {
                    LessonPracticeTask::create([
                        'lesson_id' => $lesson->id,
                        'language' => $this->getLanguageForCourse($course->title),
                        'title' => $taskData['title'],
                        'prompt' => $taskData['prompt'],
                        'starter_code' => $taskData['starter_code'] ?? '',
                        'tests_json' => $taskData['tests'] ?? [],
                        'expected_output' => $taskData['expected'] ?? '',
                        'time_limit' => $taskData['time_limit'] ?? 30,
                        'hints' => $taskData['hints'] ?? null,
                        'difficulty' => $taskData['difficulty'] ?? 'medium',
                        'test_runner_json' => ['tests' => $taskData['tests'] ?? []],
                        'is_required' => true,
                    ]);
                }
            }
        }
    }

    private function upgradeCourseExams(): void
    {
        $courses = Course::all();

        foreach ($courses as $course) {
            $exam = CourseExam::where('course_id', $course->id)->first();
            if (!$exam) continue;

            $bankQuestions = $this->generateQuestionBank($course->title, 50);

            $exam->update([
                'question_bank_json' => ['questions' => $bankQuestions],
                'questions_per_exam' => 30,
            ]);
        }
    }

    private function getModulesForCourse(string $title): array
    {
        $modules = [
            'HTML+CSS' => ['Основы HTML', 'Семантика HTML', 'CSS Основы', 'CSS Layout', 'Практика'],
            'JavaScript' => ['Переменные и типы', 'Функции', 'Объекты и массивы', 'DOM и события', 'Асинхронность'],
            'PHP' => ['Синтаксис PHP', 'Работа с данными', 'Функции', 'OOP PHP', 'Безопасность'],
            'Laravel' => ['Основы', 'Маршрутизация', 'Eloquent ORM', 'Blade', 'API'],
            'MySQL' => ['Основы SQL', 'JOIN', 'Индексы', 'Транзакции', 'Оптимизация'],
            'PostgreSQL' => ['Основы', 'Расширенные запросы', 'JSONB', 'Оконные функции', 'Оптимизация'],
            'C++' => ['Основы', 'Управление памятью', 'STL', 'Шаблоны', 'Многопоточность'],
            'Python' => ['Синтаксис', 'Структуры данных', 'OOP', 'Модули', 'Асинхронность'],
            'Java' => ['Основы', 'OOP', 'Коллекции', 'Многопоточность', 'Spring Boot'],
            'C#' => ['Основы', 'LINQ', 'Async/Await', 'Entity Framework', 'ASP.NET Core'],
            'Git' => ['Установка', 'Основные команды', 'Ветвление', 'Merge', 'GitHub'],
            'DevOps' => ['CI/CD', 'Docker', 'Kubernetes', 'Мониторинг', 'IaC'],
            'UI/UX Design' => ['Исследования', 'Прототипирование', 'Figma', 'Дизайн-системы', 'Тестирование'],
            'React' => ['Компоненты', 'Хуки', 'Состояние', 'Маршрутизация', 'Оптимизация'],
            'Node.js' => ['Основы', 'Express.js', 'Базы данных', 'REST API', 'WebSocket'],
            'TypeScript' => ['Типы', 'Интерфейсы', 'Generics', 'Utility Types', 'Интеграция'],
            'Docker' => ['Установка', 'Dockerfile', 'Compose', 'Volumes', 'Сети'],
            'Kubernetes' => ['Поды', 'Сервисы', 'Деплойменты', 'Конфигурации', 'Масштабирование'],
            'Mobile Development' => ['Flutter Basics', 'Навигация', 'State', 'API', 'Публикация'],
            'English A1' => ['Грамматика', 'Лексика', 'Чтение', 'Письмо', 'Аудирование'],
        ];

        return $modules[$title] ?? ['Модуль 1', 'Модуль 2', 'Модуль 3', 'Модуль 4', 'Модуль 5'];
    }

    private function getDescription(string $courseTitle, string $lessonTitle): string
    {
        $descriptions = [
            'Введение' => "В этом уроке вы познакомитесь с основными концепциями и структурой курса. Мы рассмотрим ключевые определения и покажем, как будет организовано обучение.",
            'Основы' => "Изучение базовых концепций и ключевых определений. Вы научитесь основам и поймёте, как применять знания на практике.",
            'Практика' => "Решение практических задач с пошаговым объяснением. Закрепите полученные знания на реальных примерах.",
            'Продвинутые темы' => "Углублённое изучение сложных аспектов темы. Вы освоите продвинутые техники и паттерны.",
            'Итоговый тест' => "Проверьте свои знания по пройденному материалу. Тест поможет выявить слабые стороны и закрепить материал.",
        ];

        return $descriptions[$lessonTitle] ?? "Изучите тему «{$lessonTitle}» в контексте курса «{$courseTitle}». Урок содержит теоретический материал и практические задания.";
    }

    private function getLanguageForCourse(string $title): string
    {
        $languages = [
            'HTML+CSS' => 'html',
            'JavaScript' => 'javascript',
            'PHP' => 'php',
            'Laravel' => 'php',
            'C++' => 'cpp',
            'Python' => 'python',
            'Java' => 'java',
            'C#' => 'csharp',
            'Node.js' => 'javascript',
            'TypeScript' => 'javascript',
            'React' => 'javascript',
        ];

        return $languages[$title] ?? 'python';
    }

    private function getQuizData(string $courseTitle, string $lessonTitle): array
    {
        $quizSets = [
            'HTML+CSS' => [
                ['question' => 'Какой HTML тег используется для создания абзаца?', 'options' => ['<div>', '<p>', '<span>', '<br>'], 'correct' => 1, 'explanation' => 'Тег <p> определяет абзац текста.'],
                ['question' => 'Какое CSS свойство управляет внешними отступами элемента?', 'options' => ['padding', 'margin', 'border', 'spacing'], 'correct' => 1, 'explanation' => 'Margin создаёт пространство вокруг элемента.'],
                ['question' => 'Что делает display: flex?', 'options' => ['Скрывает элемент', 'Делает элемент гибким контейнером', 'Удаляет элемент', 'Меняет цвет'], 'correct' => 1, 'explanation' => 'Flexbox позволяет управлять расположением дочерних элементов.'],
                ['question' => 'Как правильно подключить CSS файл?', 'options' => ['<style src="style.css">', '<link rel="stylesheet" href="style.css">', '<css href="style.css">', '<script src="style.css">'], 'correct' => 1, 'explanation' => 'Тег link с rel="stylesheet" подключает внешние стили.'],
                ['question' => 'Какой селектор имеет наибольший приоритет?', 'options' => ['class (.class)', 'id (#id)', 'tag (div)', 'universal (*)'], 'correct' => 1, 'explanation' => 'ID-селекторы имеют наивысший приоритет среди обычных селекторов.'],
            ],
            'JavaScript' => [
                ['question' => 'Как объявить переменную в ES6?', 'options' => ['var', 'let', 'int', 'dim'], 'correct' => 1, 'explanation' => 'let и const — современные способы объявления переменных.'],
                ['question' => 'Что возвращает typeof null?', 'options' => ['"null"', '"undefined"', '"object"', '"boolean"'], 'correct' => 2, 'explanation' => 'Это известный баг JavaScript,(typeof null === "object").'],
                ['question' => 'Как создать стрелочную функцию?', 'options' => ['function => {}', '() => {}', 'fn() {}', '-> {}'], 'correct' => 1, 'explanation' => 'Стрелочные функции создаются через синтаксис () => {}.'],
                ['question' => 'Что делает метод map()?', 'options' => ['Фильтрует массив', 'Создаёт новый массив с результатами', 'Сортирует массив', 'Удаляет элементы'], 'correct' => 1, 'explanation' => 'map() создаёт новый массив, применяя функцию к каждому элементу.'],
                ['question' => 'Как работает async/await?', 'options' => ['Блокирует выполнение', 'Делает код асинхронным', 'Создаёт новый поток', 'Удаляет промис'], 'correct' => 1, 'explanation' => 'async/await позволяет писать асинхронный код в синхронном стиле.'],
            ],
            'PHP' => [
                ['question' => 'Как начинается PHP код?', 'options' => ['<?php', '<php>', '<?script>', '<?code>'], 'correct' => 0, 'explanation' => 'PHP код всегда начинается с <?php.'],
                ['question' => 'Как получить данные из формы через POST?', 'options' => ['$_GET["name"]', '$_POST["name"]', '$_REQUEST["name"]', '$_FORM["name"]'], 'correct' => 1, 'explanation' => 'Суперглобальный массив $_POST содержит POST-данные.'],
                ['question' => 'Что такое PDO?', 'options' => ['Фреймворк', 'Драйвер доступа к БД', 'Шаблонизатор', 'Тестовый фреймворк'], 'correct' => 1, 'explanation' => 'PDO — PHP Data Objects, унифицированный интерфейс для работы с БД.'],
                ['question' => 'Как начать сессию в PHP?', 'options' => ['session.start()', 'session_start()', 'startSession()', 'begin_session()'], 'correct' => 1, 'explanation' => 'session_start() запускает или возобновляет сессию.'],
                ['question' => 'Как безопасно хешировать пароль?', 'options' => ['md5()', 'sha1()', 'password_hash()', 'encrypt()'], 'correct' => 2, 'explanation' => 'password_hash() использует современные алгоритмы хеширования.'],
            ],
        ];

        $default = [
            ['question' => "Какое ключевое понятие изучается в уроке «{$lessonTitle}»?", 'options' => ['Теория', 'Практика', 'Наука', 'Искусство'], 'correct' => 0, 'explanation' => 'Основное понятие урока.'],
            ['question' => "Какой подход лучше использовать в «{$lessonTitle}»?", 'options' => ['Последовательный', 'Случайный', 'Игнорирование', 'Копирование'], 'correct' => 0, 'explanation' => 'Последовательный подход более надёжен.'],
            ['question' => "Что является результатом изучения «{$lessonTitle}»?", 'options' => ['Новые знания', 'Потеря времени', 'Забвение', 'Ничего'], 'correct' => 0, 'explanation' => 'Обучение всегда даёт новые знания.'],
            ['question' => "Как часто стоит практиковать «{$lessonTitle}»?", 'options' => ['Регулярно', 'Никогда', 'Раз в год', 'Когда захочется'], 'correct' => 0, 'explanation' => 'Регулярная практика — ключ к успеху.'],
            ['question' => "Какой главный вывод из «{$lessonTitle}»?", 'options' => ['Знание = сила', 'Незнание = сила', 'Практика не нужна', 'Теория бесполезна'], 'correct' => 0, 'explanation' => 'Знание — это всегда сила.'],
        ];

        return $quizSets[$courseTitle] ?? $default;
    }

    private function getPracticeTasks(string $courseTitle, string $lessonTitle): array
    {
        $tasks = [
            'HTML+CSS' => [
                [
                    'title' => 'Создайте визитку',
                    'prompt' => "Создайте HTML-страницу визитки с:\n- Имя и фамилия (h1)\n- Должность (h2)\n- Контактная информация (email, телефон)\n- Используйте semantic HTML теги",
                    'starter_code' => "<!DOCTYPE html>\n<html lang=\"ru\">\n<head>\n    <meta charset=\"UTF-8\">\n    <title>Визитка</title>\n</head>\n<body>\n    <!-- Ваш код здесь -->\n</body>\n</html>",
                    'tests' => [
                        ['input' => '', 'expected' => 'HTML', 'description' => 'Contains HTML tag'],
                        ['input' => '', 'expected' => '<h1>', 'description' => 'Contains h1 tag'],
                    ],
                    'expected' => 'HTML page with h1',
                    'time_limit' => 30,
                    'hints' => 'Используйте теги <h1>, <h2>, <p>, <a> для структуры информации.',
                    'difficulty' => 'easy',
                ],
                [
                    'title' => 'Flexbox Layout',
                    'prompt' => "Создайте CSS-layout с Flexbox:\n- Родительский контейнер с display: flex\n- 3 дочерних блока одинаковой ширины\n- Отступы между блоками 20px\n- Центрирование по вертикали",
                    'starter_code' => "<div class=\"container\">\n    <div class=\"item\">1</div>\n    <div class=\"item\">2</div>\n    <div class=\"item\">3</div>\n</div>",
                    'tests' => [
                        ['input' => '', 'expected' => 'display: flex', 'description' => 'Uses flexbox'],
                        ['input' => '', 'expected' => 'gap: 20px', 'description' => 'Has gap spacing'],
                    ],
                    'expected' => 'Flexbox layout with 3 items',
                    'time_limit' => 45,
                    'hints' => 'Используйте display: flex и gap для раскладки.',
                    'difficulty' => 'medium',
                ],
            ],
            'JavaScript' => [
                [
                    'title' => 'FizzBuzz',
                    'prompt' => "Напишите функцию fizzBuzz(n), которая:\n- Возвращает 'Fizz' если число делится на 3\n- Возвращает 'Buzz' если число делится на 5\n- Возвращает 'FizzBuzz' если делится и на 3, и на 5\n- Иначе возвращает само число",
                    'starter_code' => "function fizzBuzz(n) {\n    // Ваш код здесь\n}",
                    'tests' => [
                        ['input' => '3', 'expected' => 'Fizz', 'description' => 'Returns Fizz for 3'],
                        ['input' => '5', 'expected' => 'Buzz', 'description' => 'Returns Buzz for 5'],
                        ['input' => '15', 'expected' => 'FizzBuzz', 'description' => 'Returns FizzBuzz for 15'],
                        ['input' => '7', 'expected' => '7', 'description' => 'Returns number for 7'],
                    ],
                    'expected' => 'FizzBuzz implementation',
                    'time_limit' => 20,
                    'hints' => 'Используйте оператор % (остаток от деления) и условные операторы.',
                    'difficulty' => 'easy',
                ],
                [
                    'title' => 'Палиндром',
                    'prompt' => "Напишите функцию isPalindrome(str), которая проверяет, является ли строка палиндромом. Пробелы и знаки препинания не учитываются.",
                    'starter_code' => "function isPalindrome(str) {\n    // Ваш код здесь\n}",
                    'tests' => [
                        ['input' => 'racecar', 'expected' => 'true', 'description' => 'racecar is palindrome'],
                        ['input' => 'hello', 'expected' => 'false', 'description' => 'hello is not palindrome'],
                        ['input' => 'A man a plan a canal Panama', 'expected' => 'true', 'description' => 'Phrase is palindrome'],
                    ],
                    'expected' => 'Palindrome check function',
                    'time_limit' => 25,
                    'hints' => 'Сравните строку с её перевёрнутой версией.',
                    'difficulty' => 'medium',
                ],
            ],
            'Python' => [
                [
                    'title' => 'FizzBuzz на Python',
                    'prompt' => "Напишите функцию fizzBuzz(n), которая возвращаетFizzBuzz для числа n.",
                    'starter_code' => "def fizzBuzz(n):\n    # Ваш код здесь\n    pass",
                    'tests' => [
                        ['input' => '3', 'expected' => 'Fizz', 'description' => 'Returns Fizz for 3'],
                        ['input' => '5', 'expected' => 'Buzz', 'description' => 'Returns Buzz for 5'],
                        ['input' => '15', 'expected' => 'FizzBuzz', 'description' => 'Returns FizzBuzz for 15'],
                    ],
                    'expected' => 'FizzBuzz in Python',
                    'time_limit' => 15,
                    'hints' => 'Используйте оператор % и строковые методы.',
                    'difficulty' => 'easy',
                ],
                [
                    'title' => 'Сортировка списка',
                    'prompt' => "Напишите функцию sort_list(lst), которая сортирует список чисел по возрастанию без использования встроенных функций сортировки.",
                    'starter_code' => "def sort_list(lst):\n    # Ваш код здесь (bubble sort)\n    pass",
                    'tests' => [
                        ['input' => '[3,1,2]', 'expected' => '[1,2,3]', 'description' => 'Sorts [3,1,2]'],
                        ['input' => '[5,4,3,2,1]', 'expected' => '[1,2,3,4,5]', 'description' => 'Sorts reversed list'],
                    ],
                    'expected' => 'Sorted list',
                    'time_limit' => 30,
                    'hints' => 'Используйте алгоритм пузырьковой сортировки (bubble sort).',
                    'difficulty' => 'medium',
                ],
            ],
            'PHP' => [
                [
                    'title' => 'Калькулятор',
                    'prompt' => 'Напишите функцию calculate($a, $op, $b), которая выполняет арифметические операции: +, -, *, /',
                    'starter_code' => '<?php' . "\n" . 'function calculate(int $a, string $op, int $b): int {' . "\n" . '    // Ваш код здесь' . "\n" . '}' . "\n" . '?>',
                    'tests' => [
                        ['input' => '2+3', 'expected' => '5', 'description' => '2+3=5'],
                        ['input' => '10-4', 'expected' => '6', 'description' => '10-4=6'],
                        ['input' => '3*7', 'expected' => '21', 'description' => '3*7=21'],
                    ],
                    'expected' => 'Calculator result',
                    'time_limit' => 20,
                    'hints' => 'Используйте switch для проверки оператора.',
                    'difficulty' => 'easy',
                ],
            ],
        ];

        $default = [
            [
                'title' => 'Практическое задание',
                'prompt' => "Выполните практическое задание по теме «{$lessonTitle}».",
                'starter_code' => '# Ваш код здесь',
                'tests' => [['input' => '', 'expected' => 'OK', 'description' => 'Basic test']],
                'expected' => 'OK',
                'time_limit' => 30,
                'hints' => 'Начните с основ и постепенно усложняйте решение.',
                'difficulty' => 'medium',
            ],
        ];

        return $tasks[$courseTitle] ?? $default;
    }

    private function generateQuestionBank(string $courseTitle, int $count): array
    {
        $topics = [
            'HTML+CSS' => ['HTML структура', 'CSS Flexbox', 'Grid Layout', 'Адаптивность', 'Семантика', 'CSS Box Model', 'Анимации', 'Типографика', 'Цвета', 'Селекторы'],
            'JavaScript' => ['Переменные', 'Функции', 'Async/Await', 'DOM', 'ES6+', 'Замыкания', 'Прототипы', 'Event Loop', 'Массивы', 'Объекты'],
            'PHP' => ['Синтаксис', 'PDO', 'Сессии', 'OOP', 'Безопасность', 'Функции', 'Массивы', 'Строки', 'Файлы', 'Обработка ошибок'],
            'Python' => ['Списки', 'Декораторы', 'Asyncio', 'Классы', 'Типизация', 'Генераторы', 'Исключения', 'Модули', 'Файлы', 'Регулярные выражения'],
        ];

        $courseTopics = $topics[$courseTitle] ?? ['Тема 1', 'Тема 2', 'Тема 3', 'Тема 4', 'Тема 5', 'Тема 6', 'Тема 7', 'Тема 8', 'Тема 9', 'Тема 10'];
        $questions = [];

        $questionTemplates = [
            function($topic) { return "Какое ключевое понятие связано с темой «{$topic}»?"; },
            function($topic) { return "Какой инструмент лучше использовать для «{$topic}»?"; },
            function($topic) { return "Что является преимуществом подхода к «{$topic}»?"; },
            function($topic) { return "Какой паттерн лучше всего подходит для «{$topic}»?"; },
            function($topic) { return "Как часто стоит применять «{$topic}» на практике?"; },
            function($topic) { return "Какой основной принцип связан с «{$topic}»?"; },
            function($topic) { return "Как измерить эффективность «{$topic}»?"; },
            function($topic) { return "Какие альтернативы существуют для «{$topic}»?"; },
            function($topic) { return "Какой уровень сложности у «{$topic}»?"; },
            function($topic) { return "Как интегрировать «{$topic}» с другими компонентами?"; },
        ];

        for ($i = 0; $i < $count; $i++) {
            $topic = $courseTopics[$i % count($courseTopics)];
            $template = $questionTemplates[$i % count($questionTemplates)];

            $options = [
                "Правильный ответ для {$topic}",
                "Вариант A: альтернатива",
                "Вариант B: неправильный",
                "Вариант C: сомнительный",
            ];
            shuffle($options);
            $correctIndex = array_search("Правильный ответ для {$topic}", $options);

            $questions[] = [
                'question' => $template($topic),
                'options' => $options,
                'correct' => $correctIndex,
                'type' => 'mc_single',
            ];
        }

        return $questions;
    }
}
