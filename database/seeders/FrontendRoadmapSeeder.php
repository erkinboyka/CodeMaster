<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoadmapNode;
use App\Models\RoadmapLesson;
use App\Models\RoadmapQuizQuestion;

class FrontendRoadmapSeeder extends Seeder
{
    public function run(): void
    {
        RoadmapNode::where('roadmap_title', 'Frontend Developer')->delete();

        $m = fn($l, $u) => ['label' => $l, 'url' => $u];

        $data = [
            // ════════════════════════════════════════════════════
            // LEVEL 0 — Фундамент
            // ════════════════════════════════════════════════════
            [
                'title' => 'Как работает интернет', 'topic' => 'Networking', 'course_id' => null, 'is_exam' => false,
                'x' => 60, 'y' => 400, 'deps' => [],
                'materials' => [
                    $m('MDN: How the Internet works', 'https://developer.mozilla.org/ru/docs/Learn/Common_questions/How_does_the_Internet_work'),
                    $m('HTTP — полный обзор', 'https://developer.mozilla.org/ru/docs/Web/HTTP/Overview'),
                    $m('DNS за 2 минуты', 'https://www.youtube.com/watch?v=mpQZurAfN_U'),
                    $m('Как браузеры рендерят страницы', 'https://developer.mozilla.org/ru/docs/Web/Performance/How_browsers_work'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 1 — Три столпа: HTML / CSS / Инструменты
            // ════════════════════════════════════════════════════
            [
                'title' => 'HTML Основы', 'topic' => 'Markup', 'course_id' => 1, 'is_exam' => false,
                'x' => 340, 'y' => 180, 'deps' => [1],
                'materials' => [
                    $m('MDN: Введение в HTML', 'https://developer.mozilla.org/ru/docs/Learn/HTML/Introduction_to_HTML'),
                    $m('HTML Academy: Путь🏠', 'https://htmlacademy.ru/courses/html'),
                    $m('Web.dev: Learn HTML', 'https://web.dev/learn/html/'),
                ],
            ],
            [
                'title' => 'CSS Основы', 'topic' => 'Styling', 'course_id' => 1, 'is_exam' => false,
                'x' => 340, 'y' => 400, 'deps' => [1],
                'materials' => [
                    $m('MDN: Введение в CSS', 'https://developer.mozilla.org/ru/docs/Learn/CSS/First_steps'),
                    $m('CSS Academy', 'https://htmlacademy.ru/courses/css'),
                    $m('web.dev: Learn CSS', 'https://web.dev/learn/css/'),
                ],
            ],
            [
                'title' => 'Терминал и CLI', 'topic' => 'Tooling', 'course_id' => null, 'is_exam' => false,
                'x' => 340, 'y' => 620, 'deps' => [1],
                'materials' => [
                    $m('Codecademy: Command Line', 'https://www.codecademy.com/learn/learn-the-command-line'),
                    $m('Linux Journey', 'https://linuxjourney.com/'),
                    $m('Bash Reference Manual', 'https://www.gnu.org/software/bash/manual/'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 2 — HTML: Структура / Семантика / Формы / Медиа
            // ════════════════════════════════════════════════════
            [
                'title' => 'Структура документа', 'topic' => 'HTML', 'course_id' => 1, 'is_exam' => false,
                'x' => 620, 'y' => 80, 'deps' => [2],
                'materials' => [
                    $m('DOCTYPE, html, head, body', 'https://developer.mozilla.org/ru/docs/Learn/HTML/Introduction_to_HTML/Getting_started'),
                    $m('HTML-дерево и DOM', 'https://developer.mozilla.org/ru/docs/Web/API/Document_Object_Model'),
                    $m('MDN: Каждый тег', 'https://developer.mozilla.org/ru/docs/Web/HTML/Element'),
                ],
            ],
            [
                'title' => 'Текст, ссылки и списки', 'topic' => 'HTML', 'course_id' => 1, 'is_exam' => false,
                'x' => 620, 'y' => 190, 'deps' => [2],
                'materials' => [
                    $m('Текстовый контент HTML', 'https://developer.mozilla.org/ru/docs/Learn/HTML/Introduction_to_HTML/HTML_text_fundamentals'),
                    $m('Ссылки и навигация', 'https://developer.mozilla.org/ru/docs/Learn/HTML/Introduction_to_HTML/Creating_hyperlinks'),
                    $m('Списки: ul, ol, dl', 'https://developer.mozilla.org/ru/docs/Learn/HTML/HTML_lists'),
                ],
            ],
            [
                'title' => 'Семантический HTML', 'topic' => 'HTML', 'course_id' => null, 'is_exam' => false,
                'x' => 620, 'y' => 300, 'deps' => [3],
                'materials' => [
                    $m('MDN: Семантика', 'https://developer.mozilla.org/ru/docs/Glossary/Semantics'),
                    $m('HTML5 Doctor', 'http://html5doctor.com/'),
                    $m('Why Semantic HTML matters', 'https://www.youtube.com/watch?v=bq8d1Q9x7dc'),
                    $m('MDN: section vs div', 'https://developer.mozilla.org/ru/docs/Web/HTML/Element/section'),
                ],
            ],
            [
                'title' => 'Формы и валидация', 'topic' => 'HTML', 'course_id' => 1, 'is_exam' => false,
                'x' => 620, 'y' => 410, 'deps' => [3, 4],
                'materials' => [
                    $m('MDN: Работа с формами', 'https://developer.mozilla.org/ru/docs/Learn/Forms'),
                    $m('Валидация форм: HTML + JS', 'https://developer.mozilla.org/ru/docs/Learn/Forms/Form_validation'),
                    $m('HTML Academy: Формы', 'https://htmlacademy.ru/courses/html/forms'),
                    $m('Custom form elements', 'https://www.sitepoint.com/accessible-custom-checkboxes-and-radio-buttons/'),
                ],
            ],
            [
                'title' => 'Таблицы и мета-теги', 'topic' => 'HTML', 'course_id' => null, 'is_exam' => false,
                'x' => 620, 'y' => 520, 'deps' => [4],
                'materials' => [
                    $m('HTML Tables: полный гайд', 'https://developer.mozilla.org/ru/docs/Learn/HTML/Tables'),
                    $m('Meta-теги для SEO', 'https://developer.mozilla.org/ru/docs/Web/HTML/Element/meta'),
                    $m('Open Graph протокол', 'https://ogp.me/'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 2 — CSS: Селекторы / Box Model / Цвета
            // ════════════════════════════════════════════════════
            [
                'title' => 'CSS Селекторы и каскад', 'topic' => 'CSS', 'course_id' => 1, 'is_exam' => false,
                'x' => 620, 'y' => 630, 'deps' => [3],
                'materials' => [
                    $m('MDN: Селекторы', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_Selectors'),
                    $m('Каскад и наследование', 'https://developer.mozilla.org/ru/docs/Learn/CSS/Building_blocks/Cascade_and_inheritance'),
                    $m('Специфичность: interactive tutorial', 'https://specificity.keegan.st/'),
                    $m('CSS Selectors Game', 'https://flukeout.github.io/'),
                ],
            ],
            [
                'title' => 'Box Model и sizing', 'topic' => 'CSS', 'course_id' => null, 'is_exam' => false,
                'x' => 620, 'y' => 740, 'deps' => [3],
                'materials' => [
                    $m('MDN: Box Model', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_Box_Model'),
                    $m('box-sizing: border-box', 'https://css-tricks.com/box-sizing/'),
                    $m('Всё о Box Model — видео', 'https://www.youtube.com/watch?v=rIO5326FgPE'),
                    $m('Margin collapse', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_Box_Model/Mastering_margin_collapsing'),
                ],
            ],
            [
                'title' => 'Цвета, фоны и тень', 'topic' => 'CSS', 'course_id' => null, 'is_exam' => false,
                'x' => 620, 'y' => 850, 'deps' => [3],
                'materials' => [
                    $m('Цветовые функции CSS', 'https://developer.mozilla.org/ru/docs/Web/CSS/color_value'),
                    $m('Градиенты: linear & radial', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_Background_and_Borders'),
                    $m('box-shadow генератор', 'https://box-shadow.CSSopia.net/'),
                    $m('MDN: text-shadow', 'https://developer.mozilla.org/ru/docs/Web/CSS/text-shadow'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 2 — Инструменты: Git
            // ════════════════════════════════════════════════════
            [
                'title' => 'Git Основы', 'topic' => 'VCS', 'course_id' => 11, 'is_exam' => false,
                'x' => 620, 'y' => 960, 'deps' => [4],
                'materials' => [
                    $m('Pro Git Book (рус)', 'https://git-scm.com/book/ru/v2'),
                    $m('Learn Git Branching', 'https://learngitbranching.js.org/?locale=ru'),
                    $m('Git Cheat Sheet', 'https://education.github.com/git-cheat-sheet-education.pdf'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 3 — CSS Layout: Flexbox / Grid / Позиционирование
            // ════════════════════════════════════════════════════
            [
                'title' => 'CSS Flexbox', 'topic' => 'Layout', 'course_id' => null, 'is_exam' => false,
                'x' => 900, 'y' => 600, 'deps' => [10, 11],
                'materials' => [
                    $m('Flexbox Froggy — игра', 'https://flexboxfroggy.com/#ru'),
                    $m('MDN: Flexbox', 'https://developer.mozilla.org/ru/docs/Learn/CSS/CSS_layout/Flexbox'),
                    $m('CSS-Tricks: A Guide to Flexbox', 'https://css-tricks.com/snippets/css/a-guide-to-flexbox/'),
                    $m('Flexbox patiently explained', 'https://www.sarasoueidan.com/blog/flexbox-vs-css-grid/'),
                ],
            ],
            [
                'title' => 'CSS Grid', 'topic' => 'Layout', 'course_id' => null, 'is_exam' => false,
                'x' => 900, 'y' => 710, 'deps' => [10, 11],
                'materials' => [
                    $m('Grid Garden — игра', 'https://cssgridgarden.com/#ru'),
                    $m('MDN: Grid Layout', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_Grid_Layout'),
                    $m('CSS-Tricks: Complete Guide to Grid', 'https://css-tricks.com/snippets/css/complete-guide-grid/'),
                    $m('CSS Grid vs Flexbox', 'https://www.youtube.com/watch?v=HV2_-J8pKJo'),
                ],
            ],
            [
                'title' => 'Позиционирование и display', 'topic' => 'Layout', 'course_id' => null, 'is_exam' => false,
                'x' => 900, 'y' => 820, 'deps' => [11],
                'materials' => [
                    $m('MDN: Position', 'https://developer.mozilla.org/ru/docs/Web/CSS/position'),
                    $m('display: все значения', 'https://developer.mozilla.org/ru/docs/Web/CSS/display'),
                    $m('z-index и контекст наложения', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_Positioning'),
                    $m('Stacking contexts', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_Positioning/Stacking_contexts'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 3 — CSS Visual: Анимации / Sass / Адаптив
            // ════════════════════════════════════════════════════
            [
                'title' => 'CSS Transitions и Animations', 'topic' => 'Visual', 'course_id' => null, 'is_exam' => false,
                'x' => 900, 'y' => 370, 'deps' => [12],
                'materials' => [
                    $m('MDN: CSS Transitions', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_Transitions'),
                    $m('MDN: CSS Animations', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_Animations'),
                    $m('Animate.css библиотека', 'https://animate.style/'),
                    $m('GPU-accelerated animations', 'https://web.dev/articles/stick-to-compositor-only-properties'),
                ],
            ],
            [
                'title' => 'Sass / SCSS', 'topic' => 'Preprocessor', 'course_id' => null, 'is_exam' => false,
                'x' => 900, 'y' => 480, 'deps' => [12],
                'materials' => [
                    $m('Sass Official Guide', 'https://sass-lang.com/guide/'),
                    $m('Sass Playground', 'https://sass-lang.com/playground/'),
                    $m('MDN: CSS Preprocessors', 'https://developer.mozilla.org/ru/docs/Web/CSS/Preprocessor'),
                    $m('Sass vs Less vs Stylus', 'https://www.sitepoint.com/less-vs-sass-vs-stylus/'),
                ],
            ],
            [
                'title' => 'Адаптивный дизайн', 'topic' => 'Responsive', 'course_id' => null, 'is_exam' => false,
                'x' => 900, 'y' => 260, 'deps' => [5, 15],
                'materials' => [
                    $m('MDN: Responsive Design', 'https://developer.mozilla.org/ru/docs/Learn/CSS/CSS_layout/Responsive_Design'),
                    $m('A List Apart: RWD', 'https://alistapart.com/article/responsive-web-design/'),
                    $m('Google: Responsive Basics', 'https://web.dev/responsive-web-design-basics/'),
                    $m('Mobile-first подход', 'https://developer.mozilla.org/ru/docs/Learn/CSS/CSS_layout/Responsive_Design'),
                ],
            ],
            [
                'title' => 'Медиа-запросы и Breakpoints', 'topic' => 'Responsive', 'course_id' => null, 'is_exam' => false,
                'x' => 900, 'y' => 150, 'deps' => [17],
                'materials' => [
                    $m('MDN: Media Queries', 'https://developer.mozilla.org/ru/docs/Web/CSS/Media_Queries'),
                    $m('Can I Use', 'https://caniuse.com/'),
                    $m('Breakpoints: когда менять', 'https://web.dev/responsive-web-design-basics/'),
                    $m('Container Queries (новое)', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_containment'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 3 — Доступность
            // ════════════════════════════════════════════════════
            [
                'title' => 'Доступность (a11y)', 'topic' => 'A11y', 'course_id' => null, 'is_exam' => false,
                'x' => 900, 'y' => 40, 'deps' => [5],
                'materials' => [
                    $m('MDN: Accessibility', 'https://developer.mozilla.org/ru/docs/Learn/Accessibility'),
                    $m('WAI-ARIA Practices', 'https://www.w3.org/WAI/ARIA/apg/'),
                    $m('A11y Project Checklist', 'https://www.a11yproject.com/checklist/'),
                    $m('WebAIM: Contrast Checker', 'https://webaim.org/resources/contrastchecker/'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 3 — Инструменты: npm / VS Code
            // ════════════════════════════════════════════════════
            [
                'title' => 'npm и пакеты', 'topic' => 'Tooling', 'course_id' => null, 'is_exam' => false,
                'x' => 900, 'y' => 960, 'deps' => [13],
                'materials' => [
                    $m('npm Docs', 'https://docs.npmjs.com/'),
                    $m('package.json explained', 'https://docs.npmjs.com/cli/v9/configuring-npm/package-json'),
                    $m('npx: запуск пакетов', 'https://docs.npmjs.com/cli/v9/commands/npx'),
                    $m('npms.io — поиск пакетов', 'https://npms.io/'),
                ],
            ],
            [
                'title' => 'VS Code для фронтенда', 'topic' => 'Tooling', 'course_id' => null, 'is_exam' => false,
                'x' => 900, 'y' => 1070, 'deps' => [13],
                'materials' => [
                    $m('VS Code для веб-разработки', 'https://code.visualstudio.com/docs/languages/web'),
                    $m('Лучшие расширения 2024', 'https://code.visualstudio.com/blogs/2024/01/24/extensions'),
                    $m('Emmet в VS Code', 'https://docs.emmet.io/cheat-sheet/'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 4 — JavaScript: Основы / DOM / ООП
            // ════════════════════════════════════════════════════
            [
                'title' => 'JavaScript Основы', 'topic' => 'Language', 'course_id' => 2, 'is_exam' => false,
                'x' => 1180, 'y' => 50, 'deps' => [5, 15],
                'materials' => [
                    $m('MDN: Первые шаги в JS', 'https://developer.mozilla.org/ru/docs/Learn/JavaScript/First_steps'),
                    $m('JavaScript.info: Основы', 'https://javascript.info/first-steps'),
                    $m('Eloquent JavaScript (онлайн)', 'https://eloquentjavascript.net/'),
                ],
            ],
            [
                'title' => 'Переменные, типы и операторы', 'topic' => 'Language', 'course_id' => 2, 'is_exam' => false,
                'x' => 1180, 'y' => 160, 'deps' => [22],
                'materials' => [
                    $m('let, const, var — разница', 'https://javascript.info/variables'),
                    $m('Примитивы и ссылочные типы', 'https://javascript.info/types'),
                    $m('Операторы: полный список', 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Operators'),
                    $m('Типы данных: typeof', 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Operators/typeof'),
                ],
            ],
            [
                'title' => 'Условия и циклы', 'topic' => 'Language', 'course_id' => 2, 'is_exam' => false,
                'x' => 1180, 'y' => 270, 'deps' => [22],
                'materials' => [
                    $m('if/else, switch, тернарный', 'https://javascript.info/ifelse'),
                    $m('Циклы: for, while, for...of', 'https://javascript.info/while-for'),
                    $m(' break/continue и метки', 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Statements/break'),
                ],
            ],
            [
                'title' => 'Функции и замыкания', 'topic' => 'Language', 'course_id' => 2, 'is_exam' => false,
                'x' => 1180, 'y' => 380, 'deps' => [23, 24],
                'materials' => [
                    $m('Function declaration vs expression', 'https://javascript.info/function-basics'),
                    $m('Стрелочные функции', 'https://javascript.info/arrow-functions-basics'),
                    $m('Замыкания (Closures)', 'https://javascript.info/closure'),
                    $m('Область видимости', 'https://javascript.info/closure#scope'),
                    $m('IIFE паттерн', 'https://developer.mozilla.org/ru/docs/Glossary/IIFE'),
                ],
            ],
            [
                'title' => 'Объекты и массивы', 'topic' => 'Language', 'course_id' => 2, 'is_exam' => false,
                'x' => 1180, 'y' => 490, 'deps' => [23, 24],
                'materials' => [
                    $m('Объекты: создание, доступ, методы', 'https://javascript.info/object'),
                    $m('Деструктуризация', 'https://javascript.info/destructuring-assignment'),
                    $m('Spread / Rest операторы', 'https://javascript.info/rest-parameters-spread'),
                    $m('Массивы: map, filter, reduce', 'https://javascript.info/array-methods'),
                    $m('entries, keys, values', 'https://javascript.info/iterable'),
                ],
            ],
            [
                'title' => 'Прототипы и классы', 'topic' => 'OOP', 'course_id' => null, 'is_exam' => false,
                'x' => 1180, 'y' => 600, 'deps' => [25, 26],
                'materials' => [
                    $m('Прототипное наследование', 'https://javascript.info/prototype'),
                    $m('Классы ES6', 'https://javascript.info/class'),
                    $m('Наследование классов', 'https://javascript.info/extend'),
                    $m('Когда использовать классы vs функции', 'https://www.youtube.com/watch?v=TFH7Q2nZKeE'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 4 — JavaScript: DOM / События
            // ════════════════════════════════════════════════════
            [
                'title' => 'DOM API', 'topic' => 'DOM', 'course_id' => null, 'is_exam' => false,
                'x' => 1180, 'y' => 710, 'deps' => [22, 6],
                'materials' => [
                    $m('MDN: Введение в DOM', 'https://developer.mozilla.org/ru/docs/Web/API/Document_Object_Model'),
                    $m('JavaScript.info: DOM-дерево', 'https://javascript.info/dom-nodes'),
                    $m('Поиск элементов', 'https://javascript.info/searching-elements-dom'),
                    $m('Создание и удаление элементов', 'https://javascript.info/modifying-document'),
                ],
            ],
            [
                'title' => 'События и делегирование', 'topic' => 'DOM', 'course_id' => null, 'is_exam' => false,
                'x' => 1180, 'y' => 820, 'deps' => [28],
                'materials' => [
                    $m('Введение в события', 'https://javascript.info/introduction-browser-events'),
                    $m('Bubbling и Capturing', 'https://javascript.info/bubbling-and-capturing'),
                    $m('Делегирование событий', 'https://javascript.info/event-delegation'),
                    $m('Справочник по событиям', 'https://developer.mozilla.org/ru/docs/Web/Events'),
                ],
            ],
            [
                'title' => 'Работа с формами через JS', 'topic' => 'DOM', 'course_id' => null, 'is_exam' => false,
                'x' => 1180, 'y' => 930, 'deps' => [28, 7],
                'materials' => [
                    $m('FormData API', 'https://developer.mozilla.org/ru/docs/Web/API/FormData'),
                    $m('Валидация через Constraint API', 'https://developer.mozilla.org/ru/docs/Web/API/Constraint_validation'),
                    $m('Создание динамических форм', 'https://javascript.info/forms'),
                    $m('События полей ввода', 'https://javascript.info/events-input-change'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 4 — ES6+ и Асинхронность
            // ════════════════════════════════════════════════════
            [
                'title' => 'ES6+ Modern Features', 'topic' => 'Language', 'course_id' => null, 'is_exam' => false,
                'x' => 1180, 'y' => 1040, 'deps' => [25, 26],
                'materials' => [
                    $m('ES6-features.org', 'https://es6-features.org/'),
                    $m('Optional chaining (?.)', 'https://javascript.info/optional-chaining'),
                    $m('Nullish coalescing (??)', 'https://javascript.info/nullish-coalescing-operator'),
                    $m('Symbol и iterators', 'https://javascript.info/symbol'),
                    $m('Map и Set', 'https://javascript.info/map-set'),
                ],
            ],
            [
                'title' => 'Асинхронность: Callbacks → Promises → async/await', 'topic' => 'Async', 'course_id' => null, 'is_exam' => false,
                'x' => 1180, 'y' => 1150, 'deps' => [25, 31],
                'materials' => [
                    $m('Callback-ад', 'https://javascript.info/callbacks'),
                    $m('Промисы (Promises)', 'https://javascript.info/promise-basics'),
                    $m('async/await', 'https://javascript.info/async-await'),
                    $m('Promise.all / Promise.allSettled', 'https://javascript.info/promise-api'),
                    $m('Микротаски и/macrotasки', 'https://javascript.info/event-loop'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 4 — Git Branching / npm Scripts
            // ════════════════════════════════════════════════════
            [
                'title' => 'Git: Ветки, Merge, Rebase', 'topic' => 'VCS', 'course_id' => 11, 'is_exam' => false,
                'x' => 1180, 'y' => 1260, 'deps' => [13],
                'materials' => [
                    $m('Git Branching Strategies', 'https://www.atlassian.com/git/tutorials/comparing-workflows'),
                    $m('Merge vs Rebase', 'https://www.atlassian.com/git/tutorials/rewriting-history/git-rebase'),
                    $m('Interactive Rebase', 'https://git-scm.com/book/ru/v2/Инструменты- Git-Перезапись-истории'),
                    $m('Git Flow', 'https://nvie.com/posts/a-successful-git-branching-model/'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 5 — Fetch / JSON / Web APIs
            // ════════════════════════════════════════════════════
            [
                'title' => 'Fetch API и HTTP-запросы', 'topic' => 'Web APIs', 'course_id' => null, 'is_exam' => false,
                'x' => 1460, 'y' => 50, 'deps' => [32],
                'materials' => [
                    $m('MDN: Fetch API', 'https://developer.mozilla.org/ru/docs/Web/API/Fetch_API'),
                    $m('JavaScript.info: Fetch', 'https://javascript.info/fetch'),
                    $m('HTTP-методы (GET, POST, PUT, DELETE)', 'https://developer.mozilla.org/ru/docs/Web/HTTP/Methods'),
                    $m('Обработка ошибок fetch', 'https://javascript.info/fetch-error'),
                    $m('AbortController', 'https://developer.mozilla.org/ru/docs/Web/API/AbortController'),
                ],
            ],
            [
                'title' => 'JSON и работа с данными', 'topic' => 'Web APIs', 'course_id' => null, 'is_exam' => false,
                'x' => 1460, 'y' => 160, 'deps' => [32],
                'materials' => [
                    $m('JSON: полный гайд', 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Global_Objects/JSON'),
                    $m('JSON.parse vs JSON.stringify', 'https://javascript.info/json'),
                    $m('structuredClone для глубокого копирования', 'https://developer.mozilla.org/ru/docs/Web/API/structuredClone'),
                ],
            ],
            [
                'title' => 'LocalStorage и Storage API', 'topic' => 'Web APIs', 'course_id' => null, 'is_exam' => false,
                'x' => 1460, 'y' => 270, 'deps' => [28],
                'materials' => [
                    $m('MDN: Web Storage API', 'https://developer.mozilla.org/ru/docs/Web/API/Web_Storage_API'),
                    $m('localStorage vs sessionStorage', 'https://javascript.info/localstorage'),
                    $m('IndexedDB для больших данных', 'https://developer.mozilla.org/ru/docs/Web/API/IndexedDB_API'),
                ],
            ],
            [
                'title' => 'Web Workers и Performance API', 'topic' => 'Web APIs', 'course_id' => null, 'is_exam' => false,
                'x' => 1460, 'y' => 380, 'deps' => [32],
                'materials' => [
                    $m('MDN: Web Workers', 'https://developer.mozilla.org/ru/docs/Web/API/Web_Workers_API'),
                    $m('requestAnimationFrame', 'https://developer.mozilla.org/ru/docs/Web/API/window/requestAnimationFrame'),
                    $m('Intersection Observer', 'https://developer.mozilla.org/ru/docs/Web/API/Intersection_Observer_API'),
                    $m('Performance.now()', 'https://developer.mozilla.org/ru/docs/Web/API/Performance/now'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 5 — Модули и сборка
            // ════════════════════════════════════════════════════
            [
                'title' => 'ES6 Модули и импорты', 'topic' => 'Modules', 'course_id' => null, 'is_exam' => false,
                'x' => 1460, 'y' => 490, 'deps' => [31, 22],
                'materials' => [
                    $m('MDN: ES Modules', 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Statements/import'),
                    $m('Динамические import()', 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Operators/import'),
                    $m('CommonJS vs ESM', 'https://www.snyk.io/blog/commonjs-vs-es-modules/'),
                    $m('Tree-shaking', 'https://webpack.js.org/glossary/#t'),
                ],
            ],
            [
                'title' => 'Webpack / Vite / Build Tools', 'topic' => 'Build', 'course_id' => null, 'is_exam' => false,
                'x' => 1460, 'y' => 600, 'deps' => [36, 14],
                'materials' => [
                    $m('Vite: Getting Started', 'https://vitejs.dev/guide/'),
                    $m('Webpack: Concepts', 'https://webpack.js.org/concepts/'),
                    $m('esbuild: why it\'s fast', 'https://esbuild.github.io/why/'),
                    $m('Parcel:零配置', 'https://parceljs.org/'),
                    $m('Портируем проект на Vite', 'https://vitejs.dev/guide/migration-from-v2'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 5 — TypeScript
            // ════════════════════════════════════════════════════
            [
                'title' => 'TypeScript Основы', 'topic' => 'Language', 'course_id' => 16, 'is_exam' => false,
                'x' => 1460, 'y' => 710, 'deps' => [22, 32],
                'materials' => [
                    $m('TypeScript Handbook', 'https://www.typescriptlang.org/docs/handbook/'),
                    $m('TypeScript Playground', 'https://www.typescriptlang.org/play'),
                    $m('TS: типы данных', 'https://www.typescriptlang.org/docs/handbook/basic-types.html'),
                    $m('Типизация функций', 'https://www.typescriptlang.org/docs/handbook/functions.html'),
                ],
            ],
            [
                'title' => 'TypeScript: Интерфейсы, Дженерики', 'topic' => 'Language', 'course_id' => 16, 'is_exam' => false,
                'x' => 1460, 'y' => 820, 'deps' => [38],
                'materials' => [
                    $m('Interfaces vs Type Aliases', 'https://www.typescriptlang.org/docs/handbook/2/objects.html'),
                    $m('Generics', 'https://www.typescriptlang.org/docs/handbook/2/generics.html'),
                    $m('Utility Types (Partial, Pick, Omit)', 'https://www.typescriptlang.org/docs/handbook/utility-types.html'),
                    $m('Декораторы', 'https://www.typescriptlang.org/docs/handbook/decorators.html'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 5 — REST API / GraphQL
            // ════════════════════════════════════════════════════
            [
                'title' => 'REST API и HTTP-запросы', 'topic' => 'Backend', 'course_id' => null, 'is_exam' => false,
                'x' => 1460, 'y' => 930, 'deps' => [32, 14],
                'materials' => [
                    $m('RESTful API Design', 'https://restfulapi.net/'),
                    $m('JSONPlaceholder — тестовый API', 'https://jsonplaceholder.typicode.com/'),
                    $m('Postman Learning Center', 'https://learning.postman.com/'),
                    $m('CORS: что это', 'https://developer.mozilla.org/ru/docs/Web/HTTP/CORS'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 6 — React
            // ════════════════════════════════════════════════════
            [
                'title' => 'React: Компоненты и JSX', 'topic' => 'Framework', 'course_id' => 14, 'is_exam' => false,
                'x' => 1740, 'y' => 50, 'deps' => [28, 25, 32],
                'materials' => [
                    $m('React Official Tutorial', 'https://react.dev/learn'),
                    $m('React: JSX в деталях', 'https://react.dev/learn/writing-markup-with-jsx'),
                    $m('Компоненты: функциональные', 'https://react.dev/learn/your-first-component'),
                    $m('Props и их передача', 'https://react.dev/learn/passing-props-to-a-component'),
                ],
            ],
            [
                'title' => 'React Hooks: useState, useEffect', 'topic' => 'Framework', 'course_id' => 14, 'is_exam' => false,
                'x' => 1740, 'y' => 160, 'deps' => [42],
                'materials' => [
                    $m('Полное руководство по Хукам', 'https://react.dev/reference/react/hooks'),
                    $m('useState', 'https://react.dev/reference/react/useState'),
                    $m('useEffect', 'https://react.dev/reference/react/useEffect'),
                    $m('Правила Хуков', 'https://react.dev/reference/react/rules-of-hooks'),
                ],
            ],
            [
                'title' => 'React: Обработка событий и условный рендер', 'topic' => 'Framework', 'course_id' => 14, 'is_exam' => false,
                'x' => 1740, 'y' => 270, 'deps' => [42],
                'materials' => [
                    $m('События в React', 'https://react.dev/learn/responding-to-events'),
                    $m('Условный рендеринг', 'https://react.dev/learn/conditional-rendering'),
                    $m('Списки и keys', 'https://react.dev/learn/rendering-lists'),
                    $m('Формы в React', 'https://react.dev/learn/reacting-to-input-with-state'),
                ],
            ],
            [
                'title' => 'React Router', 'topic' => 'Ecosystem', 'course_id' => null, 'is_exam' => false,
                'x' => 1740, 'y' => 380, 'deps' => [42],
                'materials' => [
                    $m('React Router v6 Docs', 'https://reactrouter.com/en/main'),
                    $m('Путеводитель по Router', 'https://reactrouter.com/en/main/start/tutorial'),
                    $m('Вложенные маршруты', 'https://reactrouter.com/en/main/start/tutorial#nested-routes'),
                ],
            ],
            [
                'title' => 'State Management (Context, Zustand, Redux)', 'topic' => 'Ecosystem', 'course_id' => null, 'is_exam' => false,
                'x' => 1740, 'y' => 490, 'deps' => [42, 35],
                'materials' => [
                    $m('React Context API', 'https://react.dev/learn/passing-data-deeply-with-context'),
                    $m('Zustand: простой state', 'https://github.com/pmndrs/zustand'),
                    $m('Redux Toolkit', 'https://redux-toolkit.js.org/'),
                    $m('Когда что использовать', 'https://www.youtube.com/watch?v=3yTjVaVDFhA'),
                ],
            ],
            [
                'title' => 'Кастомные хуки', 'topic' => 'Framework', 'course_id' => 14, 'is_exam' => false,
                'x' => 1740, 'y' => 600, 'deps' => [43],
                'materials' => [
                    $m('Создание кастомных хуков', 'https://react.dev/learn/reusing-logic-with-custom-hooks'),
                    $m('useFetch — пример', 'https://www.youtube.com/watch?v=UmtkqMO1viM'),
                    $m('Хуки и замыкания', 'https://dmitripavlutin.com/react-hooks-closures/'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 6 — Vue.js
            // ════════════════════════════════════════════════════
            [
                'title' => 'Vue.js: Основы', 'topic' => 'Framework', 'course_id' => null, 'is_exam' => false,
                'x' => 1740, 'y' => 710, 'deps' => [28, 25, 32],
                'materials' => [
                    $m('Vue.js Official Guide', 'https://vuejs.org/guide/introduction.html'),
                    $m('Vue Mastery', 'https://www.vuemastery.com/'),
                    $m('Vue: Template Syntax', 'https://vuejs.org/guide/essentials/template-syntax.html'),
                    $m('Vue: Reactivity Fundamentals', 'https://vuejs.org/guide/essentials/reactivity-fundamentals.html'),
                ],
            ],
            [
                'title' => 'Vue Router и Pinia', 'topic' => 'Ecosystem', 'course_id' => null, 'is_exam' => false,
                'x' => 1740, 'y' => 820, 'deps' => [48],
                'materials' => [
                    $m('Vue Router', 'https://router.vuejs.org/'),
                    $m('Pinia State Management', 'https://pinia.vuejs.org/'),
                    $m('Composition API', 'https://vuejs.org/guide/extras/composition-api-faq.html'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 6 — Тестирование
            // ════════════════════════════════════════════════════
            [
                'title' => 'Unit-тестирование (Jest / Vitest)', 'topic' => 'Testing', 'course_id' => null, 'is_exam' => false,
                'x' => 1740, 'y' => 930, 'deps' => [25, 36],
                'materials' => [
                    $m('Jest Docs', 'https://jestjs.io/'),
                    $m('Vitest', 'https://vitest.dev/'),
                    $m('Testing Library', 'https://testing-library.com/'),
                    $m('AAA паттерн тестов', 'https://automationpanda.com/2020/07/07/arrange-act-assert-a-pattern-for-writing-good-tests/'),
                ],
            ],
            [
                'title' => 'E2E тестирование (Cypress / Playwright)', 'topic' => 'Testing', 'course_id' => null, 'is_exam' => false,
                'x' => 1740, 'y' => 1040, 'deps' => [50],
                'materials' => [
                    $m('Cypress: Getting Started', 'https://docs.cypress.io/guides/getting-started/installing-cypress'),
                    $m('Playwright', 'https://playwright.dev/docs/intro'),
                    $m('Cypress Best Practices', 'https://docs.cypress.io/guides/references/best-practices'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 6 — SSR / SSG
            // ════════════════════════════════════════════════════
            [
                'title' => 'Next.js / Nuxt: SSR и SSG', 'topic' => 'Meta-Framework', 'course_id' => null, 'is_exam' => false,
                'x' => 1740, 'y' => 1150, 'deps' => [42, 48, 37],
                'materials' => [
                    $m('Next.js Learn', 'https://nextjs.org/learn'),
                    $m('Nuxt 3 Docs', 'https://nuxt.com/docs'),
                    $m('SSR vs SSG vs ISR', 'https://www.netlify.com/blog/2021/04/14/what-is-ssr-and-how-does-it-work/'),
                    $m('App Router (Next 13+)', 'https://nextjs.org/docs/app'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 7 — Production: Performance / Security / SEO / PWA
            // ════════════════════════════════════════════════════
            [
                'title' => 'Производительность (Core Web Vitals)', 'topic' => 'Performance', 'course_id' => null, 'is_exam' => false,
                'x' => 2020, 'y' => 50, 'deps' => [42, 48, 33],
                'materials' => [
                    $m('web.dev: Performance', 'https://web.dev/performance/'),
                    $m('Core Web Vitals', 'https://web.dev/vitals/'),
                    $m('Lighthouse', 'https://developer.chrome.com/docs/lighthouse/overview/'),
                    $m('PageSpeed Insights', 'https://pagespeed.web.dev/'),
                    $m('Lazy Loading', 'https://web.dev/articles/lazy-loading-video'),
                ],
            ],
            [
                'title' => 'Web Security: XSS, CSRF, CSP', 'topic' => 'Security', 'course_id' => null, 'is_exam' => false,
                'x' => 2020, 'y' => 160, 'deps' => [22, 32],
                'materials' => [
                    $m('OWASP Top 10', 'https://owasp.org/Top10/'),
                    $m('MDN: Web Security', 'https://developer.mozilla.org/ru/docs/Web/Security'),
                    $m('Content Security Policy', 'https://web.dev/content-security-policy/'),
                    $m('XSS: что это', 'https://owasp.org/www-community/attacks/xss/'),
                    $m('HTTPS: зачем нужен', 'https://web.dev/articles/why-https-matters'),
                ],
            ],
            [
                'title' => 'SEO для фронтенда', 'topic' => 'SEO', 'course_id' => null, 'is_exam' => false,
                'x' => 2020, 'y' => 270, 'deps' => [8, 34],
                'materials' => [
                    $m('Google SEO Starter', 'https://developers.google.com/search/docs/fundamentals/seo-starter-guide'),
                    $m('Structured Data (Schema.org)', 'https://schema.org/'),
                    $m('Meta теги для соцсетей', 'https://ogp.me/'),
                    $m('Robots.txt и Sitemap', 'https://developers.google.com/search/docs/crawling-indexing/robots/intro'),
                ],
            ],
            [
                'title' => 'PWA: Service Workers и оффлайн', 'topic' => 'PWA', 'course_id' => null, 'is_exam' => false,
                'x' => 2020, 'y' => 380, 'deps' => [33, 36],
                'materials' => [
                    $m('PWA Guide', 'https://web.dev/articles/what-are-pwas'),
                    $m('Workbox', 'https://developer.chrome.com/docs/workbox/'),
                    $m('Service Worker API', 'https://developer.mozilla.org/ru/docs/Web/API/Service_Worker_API'),
                    $m('PWA Builder', 'https://www.pwabuilder.com/'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 7 — Деплой и CI/CD
            // ════════════════════════════════════════════════════
            [
                'title' => 'CI/CD и автоматизация', 'topic' => 'DevOps', 'course_id' => null, 'is_exam' => false,
                'x' => 2020, 'y' => 490, 'deps' => [37, 50, 51],
                'materials' => [
                    $m('GitHub Actions', 'https://docs.github.com/en/actions'),
                    $m('GitHub Pages Deploy', 'https://docs.github.com/en/pages'),
                    $m('Netlify / Vercel Deploy', 'https://www.netlify.com/blog/2020/03/26/deploy-your-react-app-in-30-seconds/'),
                    $m('GitLab CI', 'https://docs.gitlab.com/ee/ci/'),
                ],
            ],
            [
                'title' => 'Деплой: Vercel / Netlify / Docker', 'topic' => 'DevOps', 'course_id' => 17, 'is_exam' => false,
                'x' => 2020, 'y' => 600, 'deps' => [54],
                'materials' => [
                    $m('Vercel: Deploy Next.js', 'https://vercel.com/docs'),
                    $m('Netlify: Deploy', 'https://docs.netlify.com/'),
                    $m('Docker для фронтенда', 'https://www.docker.com/blog/building-react-apps-with-server-side-rendering/'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 7 — Мониторинг и аналитика
            // ════════════════════════════════════════════════════
            [
                'title' => 'Мониторинг ошибок (Sentry)', 'topic' => 'Production', 'course_id' => null, 'is_exam' => false,
                'x' => 2020, 'y' => 710, 'deps' => [42, 35],
                'materials' => [
                    $m('Sentry for JavaScript', 'https://docs.sentry.io/platforms/javascript/'),
                    $m('Error Boundary в React', 'https://react.dev/reference/react/Component#catching-rendering-errors'),
                    $m('source maps для продакшена', 'https://web.dev/articles/source-maps'),
                ],
            ],

            // ════════════════════════════════════════════════════
            // LEVEL 8 — ЭКЗАМЕН
            // ════════════════════════════════════════════════════
            [
                'title' => 'Финальный экзамен', 'topic' => 'Exam', 'course_id' => null, 'is_exam' => true,
                'x' => 2020, 'y' => 820, 'deps' => [52, 53, 55, 56, 57],
                'materials' => [],
            ],
        ];

        // ─── Pass 1: Create nodes (deps=null) ───────────────
        $idMap = [];
        $order = 0;
        foreach ($data as $d) {
            $order++;
            $node = RoadmapNode::create([
                'title' => $d['title'],
                'topic' => $d['topic'],
                'course_id' => $d['course_id'],
                'is_exam' => $d['is_exam'],
                'roadmap_title' => 'Frontend Developer',
                'x' => $d['x'],
                'y' => $d['y'],
                'materials' => $d['materials'],
                'deps' => null,
            ]);
            $idMap[$order] = $node->id;
        }

        // ─── Pass 2: Resolve deps ──────────────────────────
        $order = 0;
        foreach ($data as $d) {
            $order++;
            if (!empty($d['deps'])) {
                $deps = array_map(fn($dep) => $idMap[$dep] ?? $dep, $d['deps']);
                RoadmapNode::where('id', $idMap[$order])->update(['deps' => $deps]);
            }
        }

        // ─── Pass 3: Lessons ───────────────────────────────
        $this->seedLessons();

        // ─── Pass 4: Quiz Questions ────────────────────────
        $this->seedQuizQuestions();
    }

    private function seedLessons(): void
    {
        $lessons = [
            'Как работает интернет' => [
                ['title' => 'Модель OSI и TCP/IP', 'description' => '7 уровней сетевой модели. Как пакеты проходят от браузера до сервера и обратно.', 'video_url' => 'https://www.youtube.com/watch?v=ke4s7T2cKBk', 'order_index' => 1],
                ['title' => 'DNS: как работает', 'description' => 'Резолвинг доменов, DNS-записи (A, AAAA, CNAME, MX), кэширование DNS.', 'video_url' => 'https://www.youtube.com/watch?v=mpQZurAfN_U', 'order_index' => 2],
                ['title' => 'HTTP/HTTPS протокол', 'description' => 'Запрос-ответ, заголовки, методы (GET, POST, PUT, DELETE), статус-коды. Что такое TLS и зачем HTTPS.', 'video_url' => 'https://www.youtube.com/watch?v=kC7LB5pOPPY', 'order_index' => 3],
            ],
            'HTML Основы' => [
                ['title' => 'Первый HTML-документ', 'description' => 'DOCTYPE, html, head, body. Минимальная структура страницы. Что делает каждый тег.', 'video_url' => 'https://www.youtube.com/watch?v=UB1O30fR-EE', 'order_index' => 1],
                ['title' => 'Заголовки и абзацы', 'description' => 'h1-h6, p, br, hr. Иерархия заголовков и семантическое значение.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Ссылки и изображения', 'description' => '<a> с атрибутами href, target, rel. <img> с src, alt, width, loading="lazy".', 'video_url' => '', 'order_index' => 3],
            ],
            'CSS Основы' => [
                ['title' => 'Синтаксис CSS', 'description' => 'Селекторы, свойства, значения. Внешние, внутренние и inline-стили.', 'video_url' => 'https://www.youtube.com/watch?v=1PnVfSUpKPY', 'order_index' => 1],
                ['title' => 'Наследование и каскадность', 'description' => 'Как CSS-свойства наследуются. Порядок разрешения конфликтов: специфичность и !important.', 'video_url' => '', 'order_index' => 2],
            ],
            'Терминал и CLI' => [
                ['title' => 'Навигация в файловой системе', 'description' => 'cd, ls, pwd, mkdir, rm, cp, mv. Абсолютные и относительные пути.', 'video_url' => 'https://www.youtube.com/watch?v=ZtqBQkZkvhI', 'order_index' => 1],
                ['title' => 'Пайпы и редиректы', 'description' => ' | (пайп), >, >> (редирект), grep, find, wc. Комбинирование команд.', 'video_url' => '', 'order_index' => 2],
            ],
            'Структура документа' => [
                ['title' => 'DOCTYPE и режимы рендеринга', 'description' => 'Зачем нужен DOCTYPE. Quirks mode vs Standards mode. Как браузер определяет режим.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Meta-теги: charset, viewport, description', 'description' => 'Обязательные meta-теги. Кодировка, адаптивность, SEO-описание.', 'video_url' => '', 'order_index' => 2],
            ],
            'Текст, ссылки и списки' => [
                ['title' => 'Форматирование текста', 'description' => '<strong>, <em>, <mark>, <code>, <pre>, <blockquote>. Когда что использовать.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Списки: ul, ol, dl', 'description' => 'Неупорядоченные, упорядоченные и описательные списки. Вложенность списков.', 'video_url' => '', 'order_index' => 2],
            ],
            'Семантический HTML' => [
                ['title' => 'Семантические теги', 'description' => 'header, nav, main, article, section, aside, footer. Как заменить div-ы.', 'video_url' => 'https://www.youtube.com/watch?v=bq8d1Q9x7dc', 'order_index' => 1],
                ['title' => 'Роль семантики в SEO и a11y', 'description' => 'Как скринридеры используют семантику. Роль в индексации поисковиками.', 'video_url' => '', 'order_index' => 2],
            ],
            'Формы и валидация' => [
                ['title' => 'Типы input и их атрибуты', 'description' => 'text, email, password, number, date, file, range, color, checkbox, radio. Атрибуты: placeholder, required, pattern, min, max.', 'video_url' => 'https://www.youtube.com/watch?v=YCbYvyQZLQQ', 'order_index' => 1],
                ['title' => 'Валидация на стороне клиента', 'description' => 'HTML5-валидация (required, pattern, min/max). JavaScript Constraint Validation API. Кастомные сообщения об ошибках.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'select, textarea, fieldset', 'description' => 'Выпадающие списки, области текста, группировка полей. Доступные формы.', 'video_url' => '', 'order_index' => 3],
            ],
            'Таблицы и мета-теги' => [
                ['title' => 'HTML Таблицы', 'description' => '<table>, <thead>, <tbody>, <tfoot>, <tr>, <th>, <td>. Атрибуты colspan, rowspan.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'SEO-мета: title, description, OG', 'description' => 'Тег title, meta description, Open Graph, Twitter Cards. Влияние на поисковую выдачу.', 'video_url' => '', 'order_index' => 2],
            ],
            'CSS Селекторы и каскад' => [
                ['title' => 'Типы селекторов', 'description' => 'Универсальный (*), тип (div), класс (.btn), ID (#header), атрибут ([type="email"]), комбинированные (>. + ~).', 'video_url' => 'https://www.youtube.com/watch?v=l1mER1mbV0g', 'order_index' => 1],
                ['title' => 'Специфичность и важность', 'description' => 'Приоритеты селекторов: inline > ID > class > tag. !important и когда его избегать.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Псевдо-классы и псевдо-элементы', 'description' => ':hover, :focus, :nth-child(), ::before, ::after. Разница между псевдо-классами и элементами.', 'video_url' => '', 'order_index' => 3],
            ],
            'Box Model и sizing' => [
                ['title' => 'Составляющие Box Model', 'description' => 'Content, padding, border, margin. Как они взаимодействуют.Margin collapse.', 'video_url' => 'https://www.youtube.com/watch?v=rIO5326FgPE', 'order_index' => 1],
                ['title' => 'box-sizing и размеры элементов', 'description' => 'content-box vs border-box. Почему border-box — лучший выбор для всего.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Размеры: width, height, min/max', 'description' => 'content-box vs border-box для размеров. min-width, max-height, aspect-ratio.', 'video_url' => '', 'order_index' => 3],
            ],
            'Цвета, фоны и тень' => [
                ['title' => 'Цветовые модели в CSS', 'description' => 'hex, rgb/rgba, hsl/hsla, color-mix(). Прозрачность и альфа-канал.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Градиенты и фоновые изображения', 'description' => 'linear-gradient, radial-gradient, conic-gradient. background-size, background-position.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'box-shadow и text-shadow', 'description' => 'Синтаксис теней. Inset-тени. Наложение нескольких теней.', 'video_url' => '', 'order_index' => 3],
            ],
            'Git Основы' => [
                ['title' => 'Git init, add, commit, status', 'description' => 'Создание репозитория. Индексирование. Первый коммит. Просмотр состояния.', 'video_url' => 'https://www.youtube.com/watch?v=HV_7vCr1B8Y', 'order_index' => 1],
                ['title' => 'Git log, diff, show', 'description' => 'Просмотр истории. Сравнение коммитов. Форматирование лога.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Git remote, push, pull, clone', 'description' => 'Работа с удалёнными репозиториями. GitHub/GitLab интеграция.', 'video_url' => '', 'order_index' => 3],
            ],
            'CSS Flexbox' => [
                ['title' => 'Flex-контейнер', 'description' => 'display: flex. Свойства контейнера: flex-direction, justify-content, align-items, flex-wrap, gap.', 'video_url' => 'https://www.youtube.com/watch?v=fYq5pzgRUdw', 'order_index' => 1],
                ['title' => 'Flex-элементы', 'description' => 'flex-grow, flex-shrink, flex-basis, order, align-self. Практика: навигация, карточки, центрирование.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Flexbox-паттерны', 'description' => 'Holy Grail Layout. Sticky Footer. Card Grid. Centering Pinwheel. Практические примеры.', 'video_url' => '', 'order_index' => 3],
            ],
            'CSS Grid' => [
                ['title' => 'Grid-контейнер и колонки', 'description' => 'display: grid. grid-template-columns, grid-template-rows, repeat(), fr, minmax().', 'video_url' => 'https://www.youtube.com/watch?v=rgJFfA2A86k', 'order_index' => 1],
                ['title' => 'Grid-области и размещение', 'description' => 'grid-template-areas, grid-column, grid-row, span. Размещение элементов по áreas.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Автоматическое размещение', 'description' => 'auto-fit, auto-fill, grid-auto-rows. Адаптивная сетка без медиа-запросов.', 'video_url' => '', 'order_index' => 3],
            ],
            'Позиционирование и display' => [
                ['title' => 'static, relative, absolute, fixed, sticky', 'description' => 'Разница между значениями position. Когда что использовать. Relative vs Absolute.', 'video_url' => 'https://www.youtube.com/watch?v=mPd2bLat4Yw', 'order_index' => 1],
                ['title' => 'z-index и stacking contexts', 'description' => 'Как формируется контекст наложения. Почему z-index иногда не работает.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'display: block, inline, inline-block, flex, grid, none', 'description' => 'Поведение разных значений display. Как они влияют на поток документа.', 'video_url' => '', 'order_index' => 3],
            ],
            'CSS Transitions и Animations' => [
                ['title' => 'CSS Transitions', 'description' => 'transition-property, transition-duration, transition-timing-function, transition-delay. Примеры hover-эффектов.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'CSS Keyframe Animations', 'description' => '@keyframes, animation-name, animation-duration, animation-iteration-count: infinite. Примеры: пульс, вращение, появление.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'GPU-ускоренные свойства', 'description' => 'transform и opacity для анимаций. Почему width/height — плохой выбор для анимаций. will-change.', 'video_url' => '', 'order_index' => 3],
            ],
            'Sass / SCSS' => [
                ['title' => 'Переменные и вложенность', 'description' => '$variables. Вложенность селекторов. parent selector (&). Работа с плейсхолдерами (%).', 'video_url' => 'https://www.youtube.com/watch?v=_a5RJ5fS1cM', 'order_index' => 1],
                ['title' => 'Миксины, функции и наследование', 'description' => '@mixin, @include, @extend. Переадача аргументов. Если/Иначе.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Модули и архитектура', 'description' => '@use, @forward. Архитектура файлов: variables, mixins, components. partials (_).', 'video_url' => '', 'order_index' => 3],
            ],
            'Адаптивный дизайн' => [
                ['title' => 'Mobile-first подход', 'description' => 'Почему начинать с мобильных. Базовые размеры. Fluid typography.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Breakpoints: когда и какие', 'description' => 'Стандартные breakpoints. Контентные vs устройственные breakpoints. min-width vs max-width.', 'video_url' => '', 'order_index' => 2],
            ],
            'Медиа-запросы и Breakpoints' => [
                ['title' => 'Синтаксис медиа-запросов', 'description' => '@media, media types, media features (width, height, orientation, prefers-color-scheme).', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Container Queries (новое!)', 'description' => '@container. Запросы размера контейнера вместо viewport. Поддержка браузерами.', 'video_url' => '', 'order_index' => 2],
            ],
            'Доступность (a11y)' => [
                ['title' => 'ARIA-атрибуты', 'description' => 'aria-label, aria-hidden, role, aria-expanded. Когда использовать и когда НЕ использовать.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Навигация клавиатурой', 'description' => 'tabindex, :focus-visible, skip-to-content. Проверка доступности без мыши.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Контрастность и шрифты', 'description' => 'WCAG 2.1 ratio. Минимальный контраст 4.5:1. Логическое увеличение шрифта.', 'video_url' => '', 'order_index' => 3],
            ],
            'npm и пакеты' => [
                ['title' => 'npm install, init, scripts', 'description' => 'Создание package.json. --save vs --save-dev. Скрипты: dev, build, test.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'node_modules и lock-файлы', 'description' => 'package-lock.json vs yarn.lock. Зачем не коммитить node_modules. npx.', 'video_url' => '', 'order_index' => 2],
            ],
            'VS Code для фронтенда' => [
                ['title' => 'Лучшие расширения', 'description' => 'ESLint, Prettier, Live Server, Emmet, GitLens, Path Intellisense, Tailwind CSS IntelliSense.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Emmet и 생산성', 'description' => 'Быстрое создание HTML/CSS. Живые шаблоны. Emmet-аббревиатуры.', 'video_url' => '', 'order_index' => 2],
            ],
            'JavaScript Основы' => [
                ['title' => 'Консоль и Chrome DevTools', 'description' => 'console.log/warn/error/table. Браузерная консоль как инструмент отладки.', 'video_url' => 'https://www.youtube.com/watch?v=UPwJSyag9g8', 'order_index' => 1],
                ['title' => 'Переменные: var, let, const', 'description' => 'Область видимости, hoisting, temporary dead zone. Почему let/const лучше var.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Примитивы и ссылочные типы', 'description' => 'string, number, boolean, null, undefined, symbol, bigint. Объекты, массивы, функции — ссылочные типы.', 'video_url' => '', 'order_index' => 3],
            ],
            'Переменные, типы и операторы' => [
                ['title' => 'Типы данных typeof', 'description' => 'Оператор typeof. typeof null === "object" (баг). Проверка на undefined.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Операторы сравнения', 'description' => '== vs ===. Loose vs strict equality. Что такое abstract equality comparison.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Логические операторы', 'description' => '&&, ||, !, ?. Маленький trick: || для дефолтов, && для условий.', 'video_url' => '', 'order_index' => 3],
            ],
            'Условия и циклы' => [
                ['title' => 'if / else / else if / switch', 'description' => 'Условные конструкции. Когда switch удобнее if. Тернарный оператор ?:', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Циклы: for, while, for...of, for...in', 'description' => 'Различия циклов. Когда что использовать. break/continue.', 'video_url' => '', 'order_index' => 2],
            ],
            'Функции и замыкания' => [
                ['title' => 'Function Declaration vs Expression', 'description' => 'Поднятие (hoisting) функций. Стрелочные функции =>. IIFE.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Параметры и аргументы', 'description' => 'Default params, rest params (...args), arguments объект. Деструктуризация параметров.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Замыкания (Closures)', 'description' => 'Как функции запоминают переменные из внешней области. Практические примеры: счётчики, приватные данные.', 'video_url' => '', 'order_index' => 3],
            ],
            'Объекты и массивы' => [
                ['title' => 'Создание и свойства объектов', 'description' => 'Литерал {}, конструктор new Object(), Object.create(). Свойства: вычисляемые ключи [expr], сокращённые свойства.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Деструктуризация', 'description' => 'const { a, b } = obj. Переименование: { a: myA }. Дефолты: { a = 10 }. Вложенная деструктуризация.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Методы массивов: map, filter, reduce, find', 'description' => 'Функциональные методы массивов. Когда что использовать. Цепочки вызовов.', 'video_url' => '', 'order_index' => 3],
            ],
            'Прототипы и классы' => [
                ['title' => 'Прототипное наследование', 'description' => '__proto__, prototype, Object.getPrototypeOf(). Цепочка прототипов.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'ES6 Классы', 'description' => 'class, constructor, methods, static, extends, super. Сахар над прототипами.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Приватные поля (#)', 'description' => '#privateFields. Геттеры и сеттеры. Инкапсуляция в JS.', 'video_url' => '', 'order_index' => 3],
            ],
            'DOM API' => [
                ['title' => 'Поиск элементов', 'description' => 'getElementById, querySelector, querySelectorAll, getElementsByClassName. Разница между Node и NodeList.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Создание и модификация', 'description' => 'createElement, appendChild, insertBefore, removeChild, replaceChild. innerHTML vs textContent.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Атрибуты и стили через JS', 'description' => 'getAttribute, setAttribute, dataset. element.style, getComputedStyle(). classList.add/toggle/remove.', 'video_url' => '', 'order_index' => 3],
            ],
            'События и делегирование' => [
                ['title' => 'addEventListener и типы событий', 'description' => 'click, input, submit, keydown/keyup, focus/blur, scroll, load. Анонимные и именованные обработчики.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Всплытие и перехват', 'description' => 'event.target vs event.currentTarget. event.stopPropagation(). Capture phase vs Bubble phase.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Делегирование событий', 'description' => 'Почему вешать обработчик на родителя. Паттерн для динамических списков. Проверка event.target.closest().', 'video_url' => '', 'order_index' => 3],
            ],
            'Работа с формами через JS' => [
                ['title' => 'FormData API', 'description' => 'new FormData(form). Методы append, get, has, entries. Отправка через fetch.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Динамические формы', 'description' => 'Создание полей на лету. Валидация при вводе. Кастомные сообщения об ошибках.', 'video_url' => '', 'order_index' => 2],
            ],
            'ES6+ Modern Features' => [
                ['title' => 'Template Literals и String Methods', 'description' => 'Обратные кавычки. Интерполяция ${}. Многострочные строки. startsWith, includes, repeat.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Optional Chaining и Nullish Coalescing', 'description' => '?. безопасный доступ к вложенным свойствам. ?? вместо || для дефолтов.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Map, Set, WeakMap, WeakSet', 'description' => 'Коллекции нового поколения. Разница от объектов и массивов. Когда использовать.', 'video_url' => '', 'order_index' => 3],
            ],
            'Асинхронность: Callbacks → Promises → async/await' => [
                ['title' => 'Callback-паттерн и его проблемы', 'description' => 'Callback hell. Асинхронные колбэки: setTimeout, addEventListener. Проблемы с управлением потоком.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Промисы (Promises)', 'description' => 'new Promise((resolve, reject) => {}). then/catch/finally. Promise.all, Promise.race, Promise.allSettled.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'async/await', 'description' => 'Синтаксис async функции. await вместо .then(). Обработка ошибок: try/catch. Параллельные await.', 'video_url' => '', 'order_index' => 3],
            ],
            'Git: Ветки, Merge, Rebase' => [
                ['title' => 'Ветвление в Git', 'description' => 'git branch, checkout, switch. HEAD. Локальные и удалённые ветки.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Merge vs Rebase', 'description' => 'Когда использовать merge, когда rebase. Конфликты и их разрешение. Interactive rebase.', 'video_url' => '', 'order_index' => 2],
            ],
            'Fetch API и HTTP-запросы' => [
                ['title' => 'fetch() основы', 'description' => 'GET-запрос. Обработка Response: .json(), .text(), .blob(). Headers.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'POST, PUT, DELETE запросы', 'description' => 'Методы, body, Content-Type. JSON.stringify(). Отправка FormData.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Обработка ошибок и AbortController', 'description' => 'Проверка response.ok. Таймауты через AbortController. Retry-логика.', 'video_url' => '', 'order_index' => 3],
            ],
            'JSON и работа с данными' => [
                ['title' => 'JSON.parse и JSON.stringify', 'description' => 'Сериализация и десериализация. Ревивайзеры в JSON.stringify.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Глубокое копирование: structuredClone', 'description' => 'structuredClone() vs JSON parse/stringify. Когда structuredClone ломается.', 'video_url' => '', 'order_index' => 2],
            ],
            'LocalStorage и Storage API' => [
                ['title' => 'localStorage и sessionStorage', 'description' => 'getItem, setItem, removeItem. Разница между localStorage и sessionStorage.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'IndexedDB для больших данных', 'description' => 'Когда localStorage не хватает. Открытие базы, хранилища, транзакции.', 'video_url' => '', 'order_index' => 2],
            ],
            'Web Workers и Performance API' => [
                ['title' => 'Web Workers', 'description' => 'Создание воркера. postMessage/onmessage. OffscreenCanvas. Когда использовать.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Intersection Observer', 'description' => 'Ленивая загрузка изображений. Бесконечная прокрутка. Видимость элементов.', 'video_url' => '', 'order_index' => 2],
            ],
            'ES6 Модули и импорты' => [
                ['title' => 'import/export синтаксис', 'description' => 'Named exports, default export. Переименование. Динамический import().', 'video_url' => '', 'order_index' => 1],
                ['title' => 'CommonJS vs ESM', 'description' => 'require() vs import. .mjs расширение. type: "module" в package.json.', 'video_url' => '', 'order_index' => 2],
            ],
            'Webpack / Vite / Build Tools' => [
                ['title' => 'Концепция сборщиков', 'description' => 'Entry, Output, Loaders, Plugins. Дерево зависимостей (Dependency Graph).', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Vite: мгновенная сборка', 'description' => 'ESM-based dev server. HMR. Rollup для прода. Конфигурация vite.config.js.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Оптимизация бандла', 'description' => 'Code splitting. Lazy loading. Tree shaking. Analyze bundle.', 'video_url' => '', 'order_index' => 3],
            ],
            'TypeScript Основы' => [
                ['title' => 'Типы примитивов и union', 'description' => 'string, number, boolean, any, unknown, never. Union (|) и intersection (&).', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Типизация функций и объектов', 'description' => 'Типы аргументов и возврата. Type assertions. Необязательные поля (?).', 'video_url' => '', 'order_index' => 2],
            ],
            'TypeScript: Интерфейсы, Дженерики' => [
                ['title' => 'Interface vs Type Alias', 'description' => 'interface User { ... }. type User = { ... }. Когда что. Расширение (extends vs交叉ение).', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Generics (Дженерики)', 'description' => 'function id<T>(x: T): T. Ограничения <T extends>. Utility Types: Partial, Pick, Omit, Record.', 'video_url' => '', 'order_index' => 2],
            ],
            'REST API и HTTP-запросы' => [
                ['title' => 'REST: принципы', 'description' => 'Stateless, resource-based URL, HTTP-методы. JSON как формат обмена.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'CORS и аутентификация', 'description' => 'Что такое CORS. Токены (JWT, Bearer). Cookie-based аутентификация.', 'video_url' => '', 'order_index' => 2],
            ],
            'React: Компоненты и JSX' => [
                ['title' => 'JSX: синтаксис', 'description' => 'JavaScript XML. Выражения в {}. Условия и списки в JSX. Ключевые атрибуты.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Компоненты и Props', 'description' => 'Функциональные компоненты. Props: передача данных. Деструктуризация props.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Жизненный цикл компонента', 'description' => 'Монтирование, обновление, размонтирование. useEffect как аналог lifecycle.', 'video_url' => '', 'order_index' => 3],
            ],
            'React Hooks: useState, useEffect' => [
                ['title' => 'useState', 'description' => 'Создание state. Функциональные обновления. Lazy initialization. Объекты в state.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'useEffect и side effects', 'description' => 'Побочные эффекты: запросы, таймеры, подписки. Массив зависимостей. Cleanup.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Другие хуки', 'description' => 'useRef, useMemo, useCallback. Когда использовать. Производительность.', 'video_url' => '', 'order_index' => 3],
            ],
            'React: Обработка событий и условный рендер' => [
                ['title' => 'События в React', 'description' => 'onClick, onChange, onSubmit. Синтетические события. Предотвращение дефолта.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Условный рендеринг', 'description' => 'Тернарный, &&, early return. Switch-case паттерн. Предпочтение раннего возврата.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Списки и ключи', 'description' => 'map для рендера списков. Почему key важен. Индекс массива как key — антипаттерн.', 'video_url' => '', 'order_index' => 3],
            ],
            'React Router' => [
                ['title' => 'Маршрутизация', 'description' => '<BrowserRouter>, <Routes>, <Route>. Параметры URL: useParams. Навигация: <Link>, useNavigate.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Вложенные маршруты и Layout', 'description' => '<Outlet>. Вложенные роуты. Layout routes. index routes.', 'video_url' => '', 'order_index' => 2],
            ],
            'State Management (Context, Zustand, Redux)' => [
                ['title' => 'React Context API', 'description' => 'createContext, useContext. Провайдеры. Когда Context достаточно.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Zustand: лёгкий state', 'description' => 'create store. Хуки. Мидлвэри. Когда Zustand лучше Redux.', 'video_url' => '', 'order_index' => 2],
            ],
            'Кастомные хуки' => [
                ['title' => 'Создание своих хуков', 'description' => 'Правило: начинается с use. Логика переиспользования. Пример: useFetch, useDebounce.', 'video_url' => '', 'order_index' => 1],
            ],
            'Vue.js: Основы' => [
                ['title' => 'Vue 3: Composition API', 'description' => 'setup(), ref(), reactive(), computed(), watch(). Сравнение с Options API.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Шаблоны и директивы', 'description' => 'v-if, v-for, v-model, v-on, v-bind. Двустороннее связывание.', 'video_url' => '', 'order_index' => 2],
            ],
            'Vue Router и Pinia' => [
                ['title' => 'Vue Router', 'description' => 'createRouter. Параметры. Guards. Динамические маршруты.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Pinia State Management', 'description' => 'defineStore. State, getters, actions. Хранение в localStorage.', 'video_url' => '', 'order_index' => 2],
            ],
            'Unit-тестирование (Jest / Vitest)' => [
                ['title' => 'Введение в тестирование', 'description' => 'AAA паттерн (Arrange-Act-Assert). describe/it/expect. Тесты чистых функций.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Mocking и spies', 'description' => 'jest.fn(), jest.mock(). Подмена модулей. Тесты async кода.', 'video_url' => '', 'order_index' => 2],
            ],
            'E2E тестирование (Cypress / Playwright)' => [
                ['title' => 'Cypress основы', 'description' => 'cy.visit, cy.get, cy.contains. Команды и ассерты. Авто-ожидание.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Паттерны E2E тестов', 'description' => 'Page Object Model. Data-testid. Stubbing API-ответов.', 'video_url' => '', 'order_index' => 2],
            ],
            'Next.js / Nuxt: SSR и SSG' => [
                ['title' => 'SSR vs SSG vs ISR', 'description' => 'Server-Side Rendering, Static Site Generation, Incremental Static Regeneration. Когда что.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Next.js App Router', 'description' => 'app/ directory. Server Components. Client Components use client directive. Loading и error UI.', 'video_url' => '', 'order_index' => 2],
            ],
            'Производительность (Core Web Vitals)' => [
                ['title' => 'LCP, FID, CLS', 'description' => 'Largest Contentful Paint. First Input Delay. Cumulative Layout Shift. Целевые значения.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Оптимизация загрузки', 'description' => 'Lazy loading. Code splitting. Image optimization. Preloading критических ресурсов.', 'video_url' => '', 'order_index' => 2],
                ['title' => 'Lighthouse аудит', 'description' => 'Performance, Accessibility, Best Practices, SEO. Как читать отчёт.', 'video_url' => '', 'order_index' => 3],
            ],
            'Web Security: XSS, CSRF, CSP' => [
                ['title' => 'XSS атаки', 'description' => 'Reflected, Stored, DOM-based XSS. Защита: санитайзинг, CSP, textContent vs innerHTML.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'CSRF и CORS', 'description' => 'Cross-Site Request Forgery. CSRF-токены. Same-origin policy. CORS-заголовки.', 'video_url' => '', 'order_index' => 2],
            ],
            'SEO для фронтенда' => [
                ['title' => 'Meta-теги и Open Graph', 'description' => 'title, description, canonical, robots. OG-теги для соцсетей. Structured Data.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Core Web Vitals и SEO', 'description' => 'Влияние производительности на ранжирование. Mobile-first индексация.', 'video_url' => '', 'order_index' => 2],
            ],
            'PWA: Service Workers и оффлайн' => [
                ['title' => 'Service Workers', 'description' => 'Регистрация, установка, активация. Перехват запросов. Стратегии кэширования.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Workbox и оффлайн', 'description' => 'Workbox API. Precache и runtime caching. Offline fallback page.', 'video_url' => '', 'order_index' => 2],
            ],
            'CI/CD и автоматизация' => [
                ['title' => 'GitHub Actions', 'description' => 'Workflow файлы. Steps, Jobs, Triggers. Деплой на GitHub Pages.', 'video_url' => '', 'order_index' => 1],
                ['title' => 'Автоматизация тестов и линтеров', 'description' => 'Запуск тестов при PR. ESLint + Prettier в CI. Автоматические проверки.', 'video_url' => '', 'order_index' => 2],
            ],
            'Деплой: Vercel / Netlify / Docker' => [
                ['title' => 'Vercel и Netlify', 'description' => 'Деплой одного коммита. Preview deployments. Environment variables.', 'video_url' => '', 'order_index' => 1],
            ],
            'Мониторинг ошибок (Sentry)' => [
                ['title' => 'Sentry интеграция', 'description' => 'Установка. Error Boundary в React. Source maps. Release tracking.', 'video_url' => '', 'order_index' => 1],
            ],
        ];

        foreach ($lessons as $title => $items) {
            $node = RoadmapNode::where('roadmap_title', 'Frontend Developer')
                ->where('title', $title)
                ->first();
            if (!$node) continue;
            foreach ($items as $item) {
                RoadmapLesson::create(array_merge($item, ['node_id' => $node->id]));
            }
        }
    }

    private function seedQuizQuestions(): void
    {
        $quizzes = [
            'Как работает интернет' => [
                ['question' => 'Какой протокол используется для передачи веб-страниц?', 'options' => ['FTP', 'HTTP/HTTPS', 'SMTP', 'SSH'], 'correct_answer' => 1],
                ['question' => 'Что делает DNS?', 'options' => ['Шифрует данные', 'Преобразует домены в IP-адреса', 'Кэширует страницы', 'Сжимает файлы'], 'correct_answer' => 1],
                ['question' => 'Какой HTTP-метод используется для получения данных?', 'options' => ['POST', 'PUT', 'GET', 'DELETE'], 'correct_answer' => 2],
            ],
            'HTML Основы' => [
                ['question' => 'Какой тег создаёт ссылку?', 'options' => ['<link>', '<a>', '<href>', '<url>'], 'correct_answer' => 1],
                ['question' => 'Какой тег является самозакрывающимся?', 'options' => ['<div>', '<p>', '<img>', '<span>'], 'correct_answer' => 2],
                ['question' => 'Что определяет <title>?', 'options' => ['Заголовок страницы', 'Заголовок раздела', 'Название браузера', 'Иконку'], 'correct_answer' => 0],
            ],
            'CSS Основы' => [
                ['question' => 'Какой селектор имеет приоритет ID над class?', 'options' => ['.class', '#id', 'tag', '*'], 'correct_answer' => 1],
                ['question' => 'Какое свойство задаёт внешний отступ?', 'options' => ['padding', 'margin', 'border', 'outline'], 'correct_answer' => 1],
            ],
            'Терминал и CLI' => [
                ['question' => 'Какая команда показывает текущую директорию?', 'options' => ['ls', 'cd', 'pwd', 'dir'], 'correct_answer' => 2],
                ['question' => 'Как скопировать файл через CLI?', 'options' => ['mv', 'cp', 'copy', 'rm'], 'correct_answer' => 1],
            ],
            'Структура документа' => [
                ['question' => 'Зачем нужен DOCTYPE?', 'options' => ['Для стилей', 'Для JS', 'Для переключения режима рендеринга', 'Для SEO'], 'correct_answer' => 2],
            ],
            'Текст, ссылки и списки' => [
                ['question' => 'Какой тег жирного текста семантически правильнее?', 'options' => ['<b>', '<strong>', '<bold>', '<em>'], 'correct_answer' => 1],
            ],
            'Семантический HTML' => [
                ['question' => 'Какой тег для основного контента?', 'options' => ['<div>', '<main>', '<body>', '<section>'], 'correct_answer' => 1],
                ['question' => 'Какой тег для навигации?', 'options' => ['<nav>', '<menu>', '<links>', '<navigate>'], 'correct_answer' => 0],
            ],
            'Формы и валидация' => [
                ['question' => 'Какой атрибут делает поле обязательным?', 'options' => ['mandatory', 'required', 'important', 'validate'], 'correct_answer' => 1],
                ['question' => 'Какой type input для пароля?', 'options' => ['text', 'secret', 'password', 'hidden'], 'correct_answer' => 2],
                ['question' => 'Как валидировать email через HTML?', 'options' => ['type="email"', 'pattern="[a-z]"', 'required', 'data-type="email"'], 'correct_answer' => 0],
            ],
            'Таблицы и мета-теги' => [
                ['question' => 'Какой тег задаёт заголовок таблицы?', 'options' => ['<thead>', '<caption>', '<th>', '<title>'], 'correct_answer' => 1],
            ],
            'CSS Селекторы и каскад' => [
                ['question' => 'Какой селектор самый специфичный?', 'options' => ['div', '.class', '#id', '*'], 'correct_answer' => 2],
                ['question' => 'Что делает > в селекторе?', 'options' => ['Все потомки', 'Прямых потомков', 'Соседей', 'Родителя'], 'correct_answer' => 1],
            ],
            'Box Model и sizing' => [
                ['question' => 'Что делает box-sizing: border-box?', 'options' => ['Padding и border включены в ширину', 'Только border включён', 'Ничего не включено', 'Margin включён'], 'correct_answer' => 0],
                ['question' => 'Что такое margin collapse?', 'options' => ['margin: collapse', 'Схлопывание вертикальных margin', 'Скрытие margin', 'Удаление margin'], 'correct_answer' => 1],
            ],
            'Цвета, фоны и тень' => [
                ['question' => 'Как записать полупрозрачный красный?', 'options' => ['red 50%', 'rgba(255,0,0,0.5)', 'color: red alpha 50', 'opacity: red 0.5'], 'correct_answer' => 1],
            ],
            'Git Основы' => [
                ['question' => 'Какая команда фиксирует изменения?', 'options' => ['git save', 'git commit', 'git push', 'git add'], 'correct_answer' => 1],
                ['question' => 'Что делает git add .?', 'options' => ['Коммитит', 'Индексирует все изменения', 'Удаляет файлы', 'Показывает статус'], 'correct_answer' => 1],
            ],
            'CSS Flexbox' => [
                ['question' => 'Какое свойство выравнивает по главной оси?', 'options' => ['align-items', 'justify-content', 'align-self', 'flex-direction'], 'correct_answer' => 1],
                ['question' => 'Как задать направление колонок?', 'options' => ['flex-direction: column', 'flex-flow: column', 'flex-wrap: column', 'flex: column'], 'correct_answer' => 0],
            ],
            'CSS Grid' => [
                ['question' => 'Какое свойство определяет колонки?', 'options' => ['grid-columns', 'grid-template-columns', 'columns', 'grid-areas'], 'correct_answer' => 1],
                ['question' => 'Что такое fr?', 'options' => ['Фракция пространства', 'Фиксированный размер', 'Относительный %', 'Пиксели'], 'correct_answer' => 0],
            ],
            'Позиционирование и display' => [
                ['question' => 'Какое position делает элемент неподвижным?', 'options' => ['static', 'relative', 'absolute', 'fixed'], 'correct_answer' => 3],
                ['question' => 'Какой display скрывает элемент из потока?', 'options' => ['visibility: hidden', 'display: none', 'opacity: 0', 'display: hidden'], 'correct_answer' => 1],
            ],
            'CSS Transitions и Animations' => [
                ['question' => 'Какое свойство задаёт плавность?', 'options' => ['animation', 'transition', 'transform', 'animate'], 'correct_answer' => 1],
                ['question' => 'Какое свойство создаёт ключевые кадры?', 'options' => ['@keyframes', '@animation', '@frames', '@transition'], 'correct_answer' => 0],
            ],
            'Sass / SCSS' => [
                ['question' => 'Какой оператор для переменных Sass?', 'options' => ['@', '$', '#', '--'], 'correct_answer' => 1],
                ['question' => 'Что делает & в Sass?', 'options' => ['Родительский селектор', 'Все селекторы', 'Новый селектор', 'Глобальный селектор'], 'correct_answer' => 0],
            ],
            'Адаптивный дизайн' => [
                ['question' => 'Что такое mobile-first?', 'options' => ['Десктоп в первую очередь', 'Мобильная версия как основа', 'Только мобильная', 'Отдельный сайт'], 'correct_answer' => 1],
            ],
            'Медиа-запросы и Breakpoints' => [
                ['question' => 'Как записать медиа-запрос для ширины > 768px?', 'options' => ['@media (min-width: 768px)', '@media (max-width: 768px)', '@media screen > 768', '@media width > 768'], 'correct_answer' => 0],
            ],
            'Доступность (a11y)' => [
                ['question' => 'Что такое ARIA?', 'options' => ['Язык стилей', 'Набор атрибутов доступности', 'Фреймворк', 'Библиотека JS'], 'correct_answer' => 1],
                ['question' => 'Какой атрибут скрывает элемент от скринридера?', 'options' => ['aria-hide', 'aria-hidden="true"', 'aria-hidden', 'role="hidden"'], 'correct_answer' => 1],
            ],
            'npm и пакеты' => [
                ['question' => 'Как установить пакет?', 'options' => ['npm create', 'npm install', 'npm add-package', 'npm get'], 'correct_answer' => 1],
                ['question' => 'Что делает npx?', 'options' => ['Устанавливает', 'Запускает пакет временно', 'Удаляет', 'Обновляет'], 'correct_answer' => 1],
            ],
            'JavaScript Основы' => [
                ['question' => 'Что вернёт typeof null?', 'options' => ['null', 'undefined', 'object', 'boolean'], 'correct_answer' => 2],
                ['question' => 'Какое ключевое слово объявляет неизменяемую переменную?', 'options' => ['var', 'let', 'const', 'static'], 'correct_answer' => 2],
                ['question' => 'Какой оператор строгого сравнения?', 'options' => ['==', '===', '=', '!='], 'correct_answer' => 1],
            ],
            'Переменные, типы и операторы' => [
                ['question' => 'Что такое "hoisting"?', 'options' => ['Поднятие объявлений', 'Перемещение файлов', 'Удаление переменных', 'Копирование данных'], 'correct_answer' => 0],
                ['question' => 'null == undefined?', 'options' => ['true', 'false', 'Ошибка', 'Зависит от версии'], 'correct_answer' => 0],
            ],
            'Условия и циклы' => [
                ['question' => 'Что такое "тернарный оператор"?', 'options' => ['Три операнда', 'Условие ? a : b', 'Три цикла', 'Три функции'], 'correct_answer' => 1],
            ],
            'Функции и замыкания' => [
                ['question' => 'Что такое замыкание (closure)?', 'options' => ['Функция с доступом к внешней области', 'Закрытый цикл', 'Приватная переменная', 'Метод объекта'], 'correct_answer' => 0],
                ['question' => 'Что делает ...args в функции?', 'options' => ['Разворачивает аргументы', 'Сворачивает в массив', 'Удаляет аргументы', 'Клонирует функцию'], 'correct_answer' => 1],
            ],
            'Объекты и массивы' => [
                ['question' => 'Какой метод массива возвращает новый массив по условию?', 'options' => ['map', 'filter', 'reduce', 'forEach'], 'correct_answer' => 1],
                ['question' => 'Что делает spread (...) в объектах?', 'options' => ['Копирует свойства', 'Удаляет свойства', 'Сворачивает', 'Разворачивает'], 'correct_answer' => 0],
            ],
            'Прототипы и классы' => [
                ['question' => 'Что такое прототип?', 'options' => ['Родитель объекта', 'Класс', 'Модуль', 'Интерфейс'], 'correct_answer' => 0],
                ['question' => 'Что делает super() в constructor?', 'options' => ['Вызывает родительский constructor', 'Создаёт объект', 'Удаляет объект', 'Возвращает undefined'], 'correct_answer' => 0],
            ],
            'DOM API' => [
                ['question' => 'Какой метод находит по CSS-селектору?', 'options' => ['getElementById', 'querySelector', 'find', 'select'], 'correct_answer' => 1],
                ['question' => 'Что возвращает querySelectorAll?', 'options' => ['Массив', 'NodeList', 'HTMLCollection', 'Объект'], 'correct_answer' => 1],
            ],
            'События и делегирование' => [
                ['question' => 'Что такое event bubbling?', 'options' => ['Событие всплывает от child к parent', 'Событие тонет', 'Событие блокируется', 'Событие дублируется'], 'correct_answer' => 0],
                ['question' => 'Зачем делегирование событий?', 'options' => ['Для скорости', 'Для динамических элементов', 'Для красивого кода', 'Для IE11'], 'correct_answer' => 1],
            ],
            'Работа с формами через JS' => [
                ['question' => 'Как отправить FormData через fetch?', 'options' => ['JSON.stringify()', 'new FormData()', 'FormData.send()', 'form.toRequest()'], 'correct_answer' => 1],
            ],
            'ES6+ Modern Features' => [
                ['question' => 'Что такое optional chaining (?.)?', 'options' => ['Безопасный доступ к вложенным свойствам', 'Новый оператор сравнения', 'Цепочка вызовов', 'Проверка на null'], 'correct_answer' => 0],
                ['question' => 'Что делает ?? (nullish coalescing)?', 'options' => ['Возвращает правый операнд если левый null/undefined', 'Проверяет равенство', 'Сравнивает строки', 'Логическое И'], 'correct_answer' => 0],
            ],
            'Асинхронность: Callbacks → Promises → async/await' => [
                ['question' => 'Что делает await?', 'options' => ['Приостанавливает выполнение до результата промиса', 'Отменяет промис', 'Запускает параллельно', 'Возвращает undefined'], 'correct_answer' => 0],
                ['question' => 'Что такое "callback hell"?', 'options' => ['Вложенные колбэки', 'Нет колбэков', 'Синхронный код', 'Ошибки в промисах'], 'correct_answer' => 0],
                ['question' => 'Что возвращает async-функция?', 'options' => ['Значение', 'Promise', 'undefined', 'Callback'], 'correct_answer' => 1],
            ],
            'Git: Ветки, Merge, Rebase' => [
                ['question' => 'Что делает git rebase?', 'options' => ['Перемещает коммиты на новую базу', 'Сливает ветки', 'Удаляет ветки', 'Клонирует'], 'correct_answer' => 0],
            ],
            'Fetch API и HTTP-запросы' => [
                ['question' => 'Что возвращает fetch()?', 'options' => ['JSON', 'Promise<Response>', 'Response', 'string'], 'correct_answer' => 1],
                ['question' => 'Как отправить POST-запрос?', 'options' => ['{ method: "POST" }', '{ type: "POST" }', '{ action: "POST" }', '{ send: "POST" }'], 'correct_answer' => 0],
            ],
            'JSON и работа с данными' => [
                ['question' => 'Что делает JSON.stringify?', 'options' => ['Парсит JSON', 'Превращает объект в строку', 'Копирует объект', 'Удаляет данные'], 'correct_answer' => 1],
            ],
            'LocalStorage и Storage API' => [
                ['question' => 'Чем localStorage отличается от sessionStorage?', 'options' => ['Ничем', 'Хранит данные до закрытия вкладки', 'Быстрее', 'Только для чтения'], 'correct_answer' => 1],
            ],
            'Web Workers и Performance API' => [
                ['question' => 'Зачем Web Workers?', 'options' => ['Для параллельных вычислений', 'Для стилей', 'Для форм', 'Для навигации'], 'correct_answer' => 0],
            ],
            'ES6 Модули и импорты' => [
                ['question' => 'Какой синтаксис для default export?', 'options' => ['export default', 'module.exports', 'export { default }', 'default export'], 'correct_answer' => 0],
            ],
            'Webpack / Vite / Build Tools' => [
                ['question' => 'Зачем нужен сборщик?', 'options' => ['Для объединения и оптимизации файлов', 'Для стилей', 'Для форм', 'Для сервера'], 'correct_answer' => 0],
            ],
            'TypeScript Основы' => [
                ['question' => 'Что такое "type annotation" в TS?', 'options' => ['Добавление типа к переменной', 'Комментарий', 'Импорт', 'Экспорт'], 'correct_answer' => 0],
                ['question' => 'Что делает "any" тип?', 'options' => ['Разрешает любые значения', 'Запрещает значения', 'Проверяет тип', 'Конвертирует тип'], 'correct_answer' => 0],
            ],
            'TypeScript: Интерфейсы, Дженерики' => [
                ['question' => 'Что такое Generics?', 'options' => ['Параметризованные типы', 'Глобальные переменные', 'Функции', 'Классы'], 'correct_answer' => 0],
            ],
            'REST API и HTTP-запросы' => [
                ['question' => 'Что такое CORS?', 'options' => ['Политика кросс-доменных запросов', 'Протокол шифрования', 'Формат данных', 'Метод запроса'], 'correct_answer' => 0],
            ],
            'React: Компоненты и JSX' => [
                ['question' => 'Что такое JSX?', 'options' => ['JavaScript XML — расширение синтаксиса', 'Новый язык', 'Библиотека CSS', 'Тип данных'], 'correct_answer' => 0],
                ['question' => 'Что такое props?', 'options' => ['State компонента', 'Данные от родителя', 'Метод компонента', 'Глобальная переменная'], 'correct_answer' => 1],
            ],
            'React Hooks: useState, useEffect' => [
                ['question' => 'Для чего useState?', 'options' => ['Управление состоянием', 'Запросы к API', 'Навигация', 'Стилизация'], 'correct_answer' => 0],
                ['question' => 'Когда вызывается useEffect с []?', 'options' => ['При каждом рендере', 'Только при монтировании', 'При изменении props', 'Никогда'], 'correct_answer' => 1],
            ],
            'React: Обработка событий и условный рендер' => [
                ['question' => 'Как предотвратить дефолт формы в React?', 'options' => ['e.preventDefault()', 'return false', 'event.stop()', 'e.cancel()'], 'correct_answer' => 0],
            ],
            'React Router' => [
                ['question' => 'Какой компонент определяет маршрут?', 'options' => ['<Route>', '<Link>', '<Navigate>', '<Path>'], 'correct_answer' => 0],
            ],
            'State Management (Context, Zustand, Redux)' => [
                ['question' => 'Когда Context Redux Toolkit?', 'options' => ['Для простого state', 'Сложное глобальное состояние', 'Для стилей', 'Для форм'], 'correct_answer' => 1],
            ],
            'Vue.js: Основы' => [
                ['question' => 'Что такое Vue 3 Composition API?', 'options' => ['Паттерн организации логики', 'Фреймворк', 'Библиотека', 'Типизация'], 'correct_answer' => 0],
                ['question' => 'Какой компонент связывает данные с формой?', 'options' => ['v-model', 'v-bind', 'v-on', 'v-if'], 'correct_answer' => 0],
            ],
            'Vue Router и Pinia' => [
                ['question' => 'Что такое Pinia?', 'options' => ['State Management для Vue', 'Роутер', 'UI-библиотека', 'Тестер'], 'correct_answer' => 0],
            ],
            'Unit-тестирование (Jest / Vitest)' => [
                ['question' => 'Что такое AAA в тестах?', 'options' => ['Arrange-Act-Assert', 'Always-Assert-Act', 'Assemble-Act-Apply', 'Accept-Act-Approve'], 'correct_answer' => 0],
            ],
            'E2E тестирование (Cypress / Playwright)' => [
                ['question' => 'Что такое E2E тестирование?', 'options' => ['Тестирование от пользователя до базы', 'Тестирование компонентов', 'Тестирование стилей', 'Тестирование API'], 'correct_answer' => 0],
            ],
            'Next.js / Nuxt: SSR и SSG' => [
                ['question' => 'Что такое SSR?', 'options' => ['Рендеринг на сервере', 'Рендеринг на клиенте', 'Статический файл', 'API'], 'correct_answer' => 0],
            ],
            'Производительность (Core Web Vitals)' => [
                ['question' => 'Что такое LCP?', 'options' => ['Largest Contentful Paint', 'Layout Component Protocol', 'Load CSS Properties', 'Local Cache Policy'], 'correct_answer' => 0],
                ['question' => 'Что такое CLS?', 'options' => ['Cumulative Layout Shift', 'CSS Layout System', 'Component Lifecycle State', 'Cache Loading Strategy'], 'correct_answer' => 0],
            ],
            'Web Security: XSS, CSRF, CSP' => [
                ['question' => 'Что такое XSS?', 'options' => ['Инъекция скриптов', 'Подмена стилей', 'Утечка данных', 'Блокировка запросов'], 'correct_answer' => 0],
            ],
            'SEO для фронтенда' => [
                ['question' => 'Какой meta-тег важен для SEO?', 'options' => ['description', 'color', 'font', 'background'], 'correct_answer' => 0],
            ],
            'PWA: Service Workers и оффлайн' => [
                ['question' => 'Что такое Service Worker?', 'options' => ['Скрипт для работы в фоне', 'Рабочий процессор', 'Менеджер задач', 'Сервер'], 'correct_answer' => 0],
            ],
            'CI/CD и автоматизация' => [
                ['question' => 'Что такое CI?', 'options' => ['Continuous Integration', 'Computer Intelligence', 'Code Inspector', 'Central Index'], 'correct_answer' => 0],
            ],
            'Деплой: Vercel / Netlify / Docker' => [
                ['question' => 'Какой сервис для деплоя Next.js?', 'options' => ['Vercel', 'Heroku', 'AWS', 'Firebase'], 'correct_answer' => 0],
            ],
            'Мониторинг ошибок (Sentry)' => [
                ['question' => 'Зачем Sentry?', 'options' => ['Отслеживание ошибок', 'Тестирование', 'Сборка', 'Деплой'], 'correct_answer' => 0],
            ],
        ];

        foreach ($quizzes as $title => $questions) {
            $node = RoadmapNode::where('roadmap_title', 'Frontend Developer')
                ->where('title', $title)
                ->first();
            if (!$node) continue;
            foreach ($questions as $q) {
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
