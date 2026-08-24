<?php

namespace Database\Seeders;

use App\Models\RoadmapNode;
use App\Models\RoadmapLesson;
use App\Models\RoadmapQuizQuestion;
use Illuminate\Database\Seeder;

class RoadmapContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedNodes();
        $this->seedLessons();
        $this->seedQuizQuestions();
    }

    private function seedNodes(): void
    {
        if (RoadmapNode::count() > 0) return;

        $nodes = [
            ['title' => 'HTML Basics', 'topic' => 'Вёрстка', 'col' => 0, 'row' => 0, 'deps' => null, 'is_exam' => false, 'course_id' => 1],
            ['title' => 'HTML Forms', 'topic' => 'Вёрстка', 'col' => 1, 'row' => 0, 'deps' => [1], 'is_exam' => false, 'course_id' => 1],
            ['title' => 'HTML Semantics', 'topic' => 'Вёрстка', 'col' => 2, 'row' => 0, 'deps' => [1], 'is_exam' => false, 'course_id' => 1],
            ['title' => 'HTML Exam', 'topic' => 'Экзамен', 'col' => 3, 'row' => 0, 'deps' => [2, 3], 'is_exam' => true, 'course_id' => null],
            ['title' => 'CSS Fundamentals', 'topic' => 'Стили', 'col' => 0, 'row' => 1, 'deps' => [4], 'is_exam' => false, 'course_id' => 1],
            ['title' => 'CSS Box Model', 'topic' => 'Стили', 'col' => 1, 'row' => 1, 'deps' => [5], 'is_exam' => false, 'course_id' => 1],
            ['title' => 'CSS Flexbox', 'topic' => 'Стили', 'col' => 2, 'row' => 1, 'deps' => [6], 'is_exam' => false, 'course_id' => 1],
            ['title' => 'CSS Grid', 'topic' => 'Стили', 'col' => 3, 'row' => 1, 'deps' => [7], 'is_exam' => false, 'course_id' => 1],
            ['title' => 'JavaScript Basics', 'topic' => 'Программирование', 'col' => 0, 'row' => 2, 'deps' => [8], 'is_exam' => false, 'course_id' => 2],
            ['title' => 'JS Functions & Scope', 'topic' => 'Программирование', 'col' => 1, 'row' => 2, 'deps' => [9], 'is_exam' => false, 'course_id' => 2],
            ['title' => 'JS Arrays & Objects', 'topic' => 'Программирование', 'col' => 2, 'row' => 2, 'deps' => [10], 'is_exam' => false, 'course_id' => 2],
            ['title' => 'JS DOM Manipulation', 'topic' => 'Программирование', 'col' => 3, 'row' => 2, 'deps' => [11], 'is_exam' => false, 'course_id' => 2],
            ['title' => 'JS Events', 'topic' => 'Программирование', 'col' => 0, 'row' => 3, 'deps' => [12], 'is_exam' => false, 'course_id' => 2],
            ['title' => 'JS Async / Promises', 'topic' => 'Программирование', 'col' => 1, 'row' => 3, 'deps' => [13], 'is_exam' => false, 'course_id' => 2],
            ['title' => 'JS ES6+ Features', 'topic' => 'Программирование', 'col' => 2, 'row' => 3, 'deps' => [14], 'is_exam' => false, 'course_id' => 2],
            ['title' => 'JS Exam', 'topic' => 'Экзамен', 'col' => 3, 'row' => 3, 'deps' => [15, 16], 'is_exam' => true, 'course_id' => null],
            ['title' => 'React Basics', 'topic' => 'Фреймворки', 'col' => 0, 'row' => 4, 'deps' => [17], 'is_exam' => false, 'course_id' => 14],
            ['title' => 'React Hooks', 'topic' => 'Фреймворки', 'col' => 1, 'row' => 4, 'deps' => [18], 'is_exam' => false, 'course_id' => 14],
            ['title' => 'React Router & State', 'topic' => 'Фреймворки', 'col' => 2, 'row' => 4, 'deps' => [19], 'is_exam' => false, 'course_id' => 14],
            ['title' => 'Frontend Final Exam', 'topic' => 'Экзамен', 'col' => 3, 'row' => 4, 'deps' => [20], 'is_exam' => true, 'course_id' => null],
        ];

        foreach ($nodes as $node) {
            RoadmapNode::create([
                'title' => $node['title'],
                'course_id' => $node['course_id'],
                'roadmap_title' => 'Frontend Developer',
                'topic' => $node['topic'],
                'materials' => json_encode(["https://example.com/materials/" . \Illuminate\Support\Str::slug($node['title'])]),
                'x' => $node['col'] * 250,
                'y' => $node['row'] * 120,
                'deps' => json_encode($node['deps']),
                'is_exam' => $node['is_exam'],
            ]);
        }
    }

    private function seedLessons(): void
    {
        $nodes = RoadmapNode::where('is_exam', false)->get();
        if ($nodes->isEmpty()) return;

        $lessonData = [
            'HTML Basics' => [
                ['title' => 'Введение в HTML', 'description' => 'Что такое HTML и как он работает в браузере.', 'video_url' => 'https://www.youtube.com/watch?v=html-intro', 'order_index' => 1],
                ['title' => 'Структура документа', 'description' => 'Теги html, head, body, meta, title.', 'video_url' => 'https://www.youtube.com/watch?v=html-structure', 'order_index' => 2],
                ['title' => 'Основные теги', 'description' => 'Заголовки, абзацы, списки, ссылки, изображения.', 'video_url' => 'https://www.youtube.com/watch?v=html-tags', 'order_index' => 3],
            ],
            'HTML Forms' => [
                ['title' => 'Формы в HTML', 'description' => 'Создание форм, input, select, textarea.', 'video_url' => 'https://www.youtube.com/watch?v=forms', 'order_index' => 1],
                ['title' => 'Валидация форм', 'description' => 'Атрибуты required, pattern, type.', 'video_url' => 'https://www.youtube.com/watch?v=validation', 'order_index' => 2],
            ],
            'HTML Semantics' => [
                ['title' => 'Семантические теги', 'description' => 'header, nav, main, article, section, footer.', 'video_url' => 'https://www.youtube.com/watch?v=semantic', 'order_index' => 1],
            ],
            'CSS Fundamentals' => [
                ['title' => 'Введение в CSS', 'description' => 'Селекторы, свойства, значения, каскадность.', 'video_url' => 'https://www.youtube.com/watch?v=css-intro', 'order_index' => 1],
                ['title' => 'Цвета и фоны', 'description' => 'Цветовые модели, градиенты, фоновые изображения.', 'video_url' => 'https://www.youtube.com/watch?v=colors', 'order_index' => 2],
                ['title' => 'Типографика', 'description' => 'Шрифты, размеры, межстрочный интервал.', 'video_url' => 'https://www.youtube.com/watch?v=typography', 'order_index' => 3],
            ],
            'CSS Box Model' => [
                ['title' => 'Box Model', 'description' => 'Content, padding, border, margin.', 'video_url' => 'https://www.youtube.com/watch?v=box-model', 'order_index' => 1],
                ['title' => 'Display и Positioning', 'description' => 'Block, inline, flex, grid, position.', 'video_url' => 'https://www.youtube.com/watch?v=positioning', 'order_index' => 2],
            ],
            'CSS Flexbox' => [
                ['title' => 'Flex Layout', 'description' => 'Container и item свойства, выравнивание.', 'video_url' => 'https://www.youtube.com/watch?v=flexbox', 'order_index' => 1],
                ['title' => 'Flex Practice', 'description' => 'Практические задачи с Flexbox.', 'video_url' => 'https://www.youtube.com/watch?v=flex-practice', 'order_index' => 2],
            ],
            'CSS Grid' => [
                ['title' => 'Grid Layout', 'description' => 'Grid container, areas,Template columns/rows.', 'video_url' => 'https://www.youtube.com/watch?v=grid', 'order_index' => 1],
            ],
            'JavaScript Basics' => [
                ['title' => 'Переменные и типы', 'description' => 'let, const, var, примитивы, объекты.', 'video_url' => 'https://www.youtube.com/watch?v=js-variables', 'order_index' => 1],
                ['title' => 'Операторы', 'description' => 'Арифметические, сравнения, логические.', 'video_url' => 'https://www.youtube.com/watch?v=operators', 'order_index' => 2],
                ['title' => 'Условия и циклы', 'description' => 'if/else, switch, for, while, do/while.', 'video_url' => 'https://www.youtube.com/watch?v=conditions', 'order_index' => 3],
            ],
            'JS Functions & Scope' => [
                ['title' => 'Функции', 'description' => 'Function declaration, expression, arrow functions.', 'video_url' => 'https://www.youtube.com/watch?v=functions', 'order_index' => 1],
                ['title' => 'Область видимости', 'description' => 'Global, function, block scope, closures.', 'video_url' => 'https://www.youtube.com/watch?v=scope', 'order_index' => 2],
            ],
            'JS Arrays & Objects' => [
                ['title' => 'Массивы', 'description' => 'Методы массивов: map, filter, reduce, find.', 'video_url' => 'https://www.youtube.com/watch?v=arrays', 'order_index' => 1],
                ['title' => 'Объекты', 'description' => 'Создание, деструктуризация, spread/rest.', 'video_url' => 'https://www.youtube.com/watch?v=objects', 'order_index' => 2],
            ],
            'JS DOM Manipulation' => [
                ['title' => 'DOM API', 'description' => 'querySelector, createElement, appendChild.', 'video_url' => 'https://www.youtube.com/watch?v=dom', 'order_index' => 1],
            ],
            'JS Events' => [
                ['title' => 'Обработчики событий', 'description' => 'addEventListener, event object, bubbling.', 'video_url' => 'https://www.youtube.com/watch?v=events', 'order_index' => 1],
            ],
            'JS Async / Promises' => [
                ['title' => 'Асинхронность', 'description' => 'Callbacks, Promises, async/await.', 'video_url' => 'https://www.youtube.com/watch?v=async', 'order_index' => 1],
                ['title' => 'Fetch API', 'description' => 'HTTP запросы, JSON, обработка ошибок.', 'video_url' => 'https://www.youtube.com/watch?v=fetch', 'order_index' => 2],
            ],
            'JS ES6+ Features' => [
                ['title' => 'ES6+ Новинки', 'description' => 'Template literals, modules, classes, Optional chaining.', 'video_url' => 'https://www.youtube.com/watch?v=es6', 'order_index' => 1],
            ],
            'React Basics' => [
                ['title' => 'Введение в React', 'description' => 'JSX, компоненты, props.', 'video_url' => 'https://www.youtube.com/watch?v=react-intro', 'order_index' => 1],
                ['title' => 'Компоненты', 'description' => 'Function components, props drilling.', 'video_url' => 'https://www.youtube.com/watch?v=components', 'order_index' => 2],
            ],
            'React Hooks' => [
                ['title' => 'useState и useEffect', 'description' => 'State management и side effects.', 'video_url' => 'https://www.youtube.com/watch?v=hooks', 'order_index' => 1],
                ['title' => 'Кастомные хуки', 'description' => 'Создание и использование кастомных хуков.', 'video_url' => 'https://www.youtube.com/watch?v=custom-hooks', 'order_index' => 2],
            ],
            'React Router & State' => [
                ['title' => 'React Router', 'description' => 'Маршрутизация, вложенные роуты, навигация.', 'video_url' => 'https://www.youtube.com/watch?v=router', 'order_index' => 1],
                ['title' => 'State Management', 'description' => 'Context API, useReducer, Zustand.', 'video_url' => 'https://www.youtube.com/watch?v=state-mgmt', 'order_index' => 2],
            ],
        ];

        foreach ($nodes as $node) {
            $lessons = $lessonData[$node->title] ?? [['title' => $node['title'], 'description' => 'Узел roadmap: ' . $node['title'], 'video_url' => '', 'order_index' => 1]];
            foreach ($lessons as $lesson) {
                RoadmapLesson::create(array_merge($lesson, ['node_id' => $node->id]));
            }
        }
    }

    private function seedQuizQuestions(): void
    {
        $nodes = RoadmapNode::where('is_exam', false)->get();
        if ($nodes->isEmpty()) return;

        $quizData = [
            'HTML Basics' => [
                ['question' => 'Какой тег используется для создания ссылки?', 'options' => ['<a>', '<link>', '<href>', '<url>'], 'correct_answer' => 0],
                ['question' => 'Какой тег является самозакрывающимся?', 'options' => ['<div>', '<p>', '<img>', '<span>'], 'correct_answer' => 2],
                ['question' => 'Какой тег определяет заголовок страницы в <head>?', 'options' => ['<h1>', '<head>', '<title>', '<meta>'], 'correct_answer' => 2],
            ],
            'HTML Forms' => [
                ['question' => 'Какой атрибут делает поле обязательным?', 'options' => ['required', 'mandatory', 'important', 'validate'], 'correct_answer' => 0],
                ['question' => 'Какой тип input для электронной почты?', 'options' => ['text', 'email', 'mail', 'address'], 'correct_answer' => 1],
            ],
            'HTML Semantics' => [
                ['question' => 'Какой тег используется для основного контента?', 'options' => ['<div>', '<main>', '<body>', '<content>'], 'correct_answer' => 1],
            ],
            'CSS Fundamentals' => [
                ['question' => 'Какой селектор имеет наибольший приоритет?', 'options' => ['class', 'tag', 'id', 'universal'], 'correct_answer' => 2],
                ['question' => 'Какое свойство задаёт внешний отступ?', 'options' => ['padding', 'margin', 'border', 'spacing'], 'correct_answer' => 1],
                ['question' => 'Какое значение display делает элемент flex-контейнером?', 'options' => ['block', 'inline', 'flex', 'grid'], 'correct_answer' => 2],
            ],
            'CSS Box Model' => [
                ['question' => 'Что такое box-sizing: border-box?', 'options' => ['Padding и border включены в ширину', 'Только border включён', 'Ничего не включено', 'Margin включён'], 'correct_answer' => 0],
            ],
            'CSS Flexbox' => [
                ['question' => 'Какое свойство выравнивает элементы по главной оси?', 'options' => ['align-items', 'justify-content', 'align-self', 'flex-wrap'], 'correct_answer' => 1],
                ['question' => 'Какое значение gap задаёт отступ 10px?', 'options' => ['gap: 10px', 'gap: 10', 'margin: 10px', 'spacing: 10px'], 'correct_answer' => 0],
            ],
            'CSS Grid' => [
                ['question' => 'Какое свойство определяет колонки сетки?', 'options' => ['grid-columns', 'grid-template-columns', 'columns', 'grid-rows'], 'correct_answer' => 1],
            ],
            'JavaScript Basics' => [
                ['question' => 'Какое ключевое слово объявляет неизменяемую переменную?', 'options' => ['var', 'let', 'const', 'static'], 'correct_answer' => 2],
                ['question' => 'Что вернёт typeof null?', 'options' => ['null', 'undefined', 'object', 'boolean'], 'correct_answer' => 2],
                ['question' => 'Какой оператор строгого сравнения?', 'options' => ['==', '===', '=', '!='], 'correct_answer' => 1],
            ],
            'JS Functions & Scope' => [
                ['question' => 'Что такое замыкание (closure)?', 'options' => ['Функция с доступом к внешней области', 'Закрытый цикл', 'Приватная переменная', 'Метод объекта'], 'correct_answer' => 0],
            ],
            'JS Arrays & Objects' => [
                ['question' => 'Какой метод массива возвращает новый массив с элементами прошедшими проверку?', 'options' => ['map', 'filter', 'reduce', 'forEach'], 'correct_answer' => 1],
                ['question' => 'Что делает spread operator (...)?', 'options' => ['Разворачивает массив/объект', 'Сворачивает данные', 'Удаляет элементы', 'Клонирует функцию'], 'correct_answer' => 0],
            ],
            'JS DOM Manipulation' => [
                ['question' => 'Какой метод находит элемент по CSS-селектору?', 'options' => ['getElementById', 'querySelector', 'getElementsByClassName', 'findElement'], 'correct_answer' => 1],
            ],
            'JS Events' => [
                ['question' => 'Что такое event bubbling?', 'options' => ['Событие всплывает от child к parent', 'Событие тонет от parent к child', 'Событие вызывается дважды', 'Событие блокируется'], 'correct_answer' => 0],
            ],
            'JS Async / Promises' => [
                ['question' => 'Что делает оператор await?', 'options' => ['Приостанавливает выполнение до результата промиса', 'Отменяет промис', 'Запускает промис параллельно', 'Возвращает undefined'], 'correct_answer' => 0],
                ['question' => 'Какой метод fetch возвращает?', 'options' => ['JSON', 'Promise<Response>', 'Response', 'string'], 'correct_answer' => 1],
            ],
            'JS ES6+ Features' => [
                ['question' => 'Что такое template literals?', 'options' => ['Строки с интерполяцией через ${}', 'Шаблоны HTML', 'Строки без кавычек', 'Многострочные комментарии'], 'correct_answer' => 0],
            ],
            'React Basics' => [
                ['question' => 'Что такое JSX?', 'options' => ['JavaScript XML — синтаксис расширения JS', 'Новый язык программирования', 'Библиотека для CSS', 'Тип данных'], 'correct_answer' => 0],
                ['question' => 'Что такое props?', 'options' => ['Состояние компонента', 'Данные передаваемые от родителя', 'Метод компонента', 'Глобальная переменная'], 'correct_answer' => 1],
            ],
            'React Hooks' => [
                ['question' => 'Для чего используется useState?', 'options' => ['Для управления состоянием', 'Для запросов к API', 'Для навигации', 'Для стилизации'], 'correct_answer' => 0],
                ['question' => 'Когда вызывается useEffect с пустым массивом зависимостей?', 'options' => ['При каждом рендере', 'Только при монтировании', 'При изменении props', 'Никогда'], 'correct_answer' => 1],
            ],
            'React Router & State' => [
                ['question' => 'Какой компонент определяет маршрут в React Router?', 'options' => ['<Route>', '<Link>', '<Navigate>', '<Switch>'], 'correct_answer' => 0],
            ],
        ];

        foreach ($nodes as $node) {
            $questions = $quizData[$node->title] ?? [];
            foreach ($questions as $i => $q) {
                RoadmapQuizQuestion::create([
                    'node_id' => $node->id,
                    'question' => $q['question'],
                    'options' => json_encode($q['options']),
                    'correct_answer' => $q['correct_answer'],
                ]);
            }
        }
    }
}
