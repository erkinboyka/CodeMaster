<?php

return [
    1 => [
        'lessons' => [
            'Структура HTML-документа' => '<h2>Структура HTML-документа</h2><p>HTML (HyperText Markup Language) — стандартный язык разметки для создания веб-страниц.</p><h3>Базовый шаблон</h3><pre><code>&lt;!DOCTYPE html&gt;&lt;html lang="ru"&gt;&lt;head&gt;&lt;meta charset="UTF-8"&gt;&lt;title&gt;Заголовок&lt;/title&gt;&lt;/head&gt;&lt;body&gt;&lt;h1&gt;Привет!&lt;/h1&gt;&lt;p&gt;Текст&lt;/p&gt;&lt;/body&gt;&lt;/html&gt;</code></pre><h3>Основные элементы</h3><ul><li><code>&lt;!DOCTYPE html&gt;</code> — объявление типа документа</li><li><code>&lt;html&gt;</code> — корневой элемент</li><li><code>&lt;head&gt;</code> — метаданные</li><li><code>&lt;body&gt;</code> — видимое содержимое</li></ul><h3>Заголовки</h3><p>Шесть уровней: <code>&lt;h1&gt;</code> — <code>&lt;h6&gt;</code>. h1 — самый важный.</p><h3>Текст</h3><pre><code>&lt;p&gt;Абзац&lt;/p&gt;&lt;strong&gt;Жирный&lt;/strong&gt;&lt;em&gt;Курсив&lt;/em&gt;&lt;br&gt;Перенос</code></pre>',
            'Семантические теги HTML5' => '<h2>Семантические теги HTML5</h2><p>HTML5 ввёл семантические теги для понимания структуры браузерами и поисковиками.</p><h3>Основные теги</h3><pre><code>&lt;header&gt;Шапка&lt;/header&gt;&lt;nav&gt;Навигация&lt;/nav&gt;&lt;main&gt;&lt;article&gt;&lt;section&gt;Раздел&lt;/section&gt;&lt;aside&gt;Боковая панель&lt;/aside&gt;&lt;/article&gt;&lt;/main&gt;&lt;footer&gt;Подвал&lt;/footer&gt;</code></pre><h3>Преимущества</h3><ul><li>Доступность (screen readers)</li><li>SEO-оптимизация</li><li>Читаемость кода</li></ul>',
            'Формы и валидация HTML5' => '<h2>Формы и валидация HTML5</h2><p>HTML5 формы имеют расширенные возможности валидации без JavaScript.</p><h3>Пример формы</h3><pre><code>&lt;form action="/submit" method="POST"&gt;&lt;label for="name"&gt;Имя:&lt;/label&gt;&lt;input type="text" id="name" name="name" required minlength="2"&gt;&lt;label for="email"&gt;Email:&lt;/label&gt;&lt;input type="email" id="email" name="email" required&gt;&lt;label for="age"&gt;Возраст:&lt;/label&gt;&lt;input type="number" id="age" name="age" min="1" max="120"&gt;&lt;button type="submit"&gt;Отправить&lt;/button&gt;&lt;/form&gt;</code></pre><h3>Типы input</h3><ul><li><code>text</code> — текст</li><li><code>email</code> — валидация email</li><li><code>number</code> — числа</li><li><code>date</code> — дата</li><li><code>range</code> — ползунок</li></ul>',
            'CSS Box Model и позиционирование' => '<h2>CSS Box Model и позиционирование</h2><p>Каждый элемент — прямоугольная коробка с отступами и рамками.</p><h3>Box Model</h3><pre><code>.box { width: 200px; padding: 20px; border: 2px solid #000; margin: 10px; box-sizing: border-box; }</code></pre><h3>Позиционирование</h3><pre><code>.relative { position: relative; top: 10px; }.absolute { position: absolute; top: 0; right: 0; }.fixed { position: fixed; bottom: 20px; right: 20px; }</code></pre>',
            'Flexbox и Flex Layout' => '<h2>Flexbox и Flex Layout</h2><p>Flexbox — мощная модель компоновки для гибких макетов.</p><h3>Основы</h3><pre><code>.container { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }.item { flex: 1; order: 0; }</code></pre><h3>Направления</h3><ul><li><code>row</code> — горизонтально</li><li><code>column</code> — вертикально</li><li><code>row-reverse</code> — обратный порядок</li></ul>',
            'CSS Grid' => '<h2>CSS Grid</h2><p>CSS Grid — двухмерная система компоновки.</p><h3>Основы</h3><pre><code>.grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }</code></pre><h3>Именованная сетка</h3><pre><code>.layout { display: grid; grid-template-areas: "header header" "sidebar main" "footer footer"; }.header { grid-area: header; }</code></pre>',
            'Адаптивный дизайн' => '<h2>Адаптивный дизайн</h2><p>Обеспечивает корректное отображение на разных устройствах.</p><h3>Media Queries</h3><pre><code>@media (max-width: 768px) { .container { flex-direction: column; } }@media (min-width: 1025px) { .grid { grid-template-columns: repeat(3, 1fr); } }</code></pre><h3>Viewport</h3><pre><code>&lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;</code></pre>',
            'CSS Анимации и переходы' => '<h2>CSS Анимации и переходы</h2><p>Плавные анимации без JavaScript.</p><h3>Transitions</h3><pre><code>.button { transition: background-color 0.3s ease; }.button:hover { background-color: darkblue; transform: scale(1.05); }</code></pre><h3>Animations</h3><pre><code>@keyframes slideIn { from { opacity: 0; transform: translateX(-100px); } to { opacity: 1; transform: translateX(0); }}.animated { animation: slideIn 0.5s ease-out forwards; }</code></pre>',
            'CSS Переменные и препроцессоры' => '<h2>CSS Переменные и препроцессоры</h2><p>Переменные и препроцессоры упрощают разработку.</p><h3>CSS-переменные</h3><pre><code>:root { --primary: #3498db; --spacing: 8px; }.button { background: var(--primary); padding: calc(var(--spacing) * 2); }</code></pre><h3>SCSS</h3><pre><code>$primary: #3498db;@mixin respond-to($bp) { @media (min-width: $bp) { @content; } }</code></pre>',
            'Тест по HTML+CSS' => '<h2>Тест по HTML+CSS</h2><p>Проверьте свои знания HTML и CSS.</p>',
            'Финальный проект HTML+CSS' => '<h2>Финальный проект HTML+CSS</h2><p>Создайте лендинг с шапкой, hero-секцией, карточками товаров, формой и подвалом.</p><p>Используйте Flexbox/Grid, адаптивный дизайн и анимации.</p>',
        ],
        'quizzes' => [
            'Структура HTML-документа' => [
                ['q' => 'Какой тег является корневым элементом HTML-документа?', 'o' => ['<head>', '<body>', '<html>', '<document>'], 'c' => 2, 'e' => '<html> является корневым элементом.'],
                ['q' => 'Что делает <!DOCTYPE html>?', 'o' => ['Определяет язык', 'Объявляет тип документа как HTML5', 'Подключает стили', 'Задаёт кодировку'], 'c' => 1, 'e' => '<!DOCTYPE html> объявляет HTML5.'],
                ['q' => 'Где размещается видимое содержимое?', 'o' => ['<head>', '<body>', '<html>', '<meta>'], 'c' => 1, 'e' => 'Все видимое содержимое в <body>.'],
                ['q' => 'Какой тег создаёт абзац?', 'o' => ['<p>', '<para>', '<text>', '<div>'], 'c' => 0, 'e' => '<p> (paragraph) для абзацев.'],
                ['q' => 'Что такое UTF-8?', 'o' => ['Язык программирования', 'Стандарт кодировки символов', 'Протокол', 'Формат изображений'], 'c' => 1, 'e' => 'UTF-8 — стандарт кодировки символов Unicode.'],
            ],
            'Семантические теги HTML5' => [
                ['q' => 'Какой тег для навигации?', 'o' => ['<navigation>', '<nav>', '<menu>', '<links>'], 'c' => 1, 'e' => '<nav> для блоков навигации.'],
                ['q' => 'Какой тег определяет основное содержимое?', 'o' => ['<section>', '<article>', '<main>', '<content>'], 'c' => 2, 'e' => '<main> для основного содержимого.'],
                ['q' => 'Какой тег для боковой панели?', 'o' => ['<side>', '<aside>', '<panel>', '<sidebar>'], 'c' => 1, 'e' => '<aside> для косвенно связанного контента.'],
                ['q' => 'Какой тег — заголовок страницы?', 'o' => ['<head>', '<top>', '<header>', '<title>'], 'c' => 2, 'e' => '<header> для вводной информации.'],
                ['q' => 'Какой тег для группы статей?', 'o' => ['<article>', '<blog>', '<post>', '<feed>'], 'c' => 0, 'e' => '<article> — независимый блок содержимого.'],
            ],
            'Формы и валидация HTML5' => [
                ['q' => 'Какой атрибут делает поле обязательным?', 'o' => ['mandatory', 'required', 'necessary', 'important'], 'c' => 1, 'e' => 'required делает поле обязательным.'],
                ['q' => 'Какой тип input для email?', 'o' => ['text', 'email', 'mail', 'address'], 'c' => 1, 'e' => 'Тип email проверяет формат.'],
                ['q' => 'Что делает minlength?', 'o' => ['Максимальная длина', 'Минимальная длина', 'readonly', 'Скрывает поле'], 'c' => 1, 'e' => 'minlength задаёт минимальное количество символов.'],
                ['q' => 'Какой метод отправляет данные в URL?', 'o' => ['POST', 'GET', 'PUT', 'DELETE'], 'c' => 1, 'e' => 'GET добавляет данные в URL.'],
                ['q' => 'Какой атрибут задаёт регулярное выражение?', 'o' => ['validate', 'pattern', 'regex', 'mask'], 'c' => 1, 'e' => 'pattern принимает регулярное выражение.'],
            ],
            'CSS Box Model и позиционирование' => [
                ['q' => 'Что такое box-sizing: border-box?', 'o' => ['Блокирует элемент', 'Включает padding и border в width', 'Скрывает элемент', 'Создаёт рамку'], 'c' => 1, 'e' => 'border-box делает width включающим padding и border.'],
                ['q' => 'Какой позиционирование фиксирует элемент к окну?', 'o' => ['relative', 'absolute', 'fixed', 'static'], 'c' => 2, 'e' => 'fixed фиксирует к окну браузера.'],
                ['q' => 'Что такое margin collapse?', 'o' => ['Сворачивание отступов', 'Удаление отступов', 'Слияние элементов', 'Создание рамки'], 'c' => 0, 'e' => 'Margin collapse — объединение вертикальных отступов соседей.'],
                ['q' => 'Какой margin создаёт внешний отступ?', 'o' => ['Внутренний', 'Внешний', 'Границы', 'Контента'], 'c' => 1, 'e' => 'Margin — внешний отступ вокруг элемента.'],
                ['q' => 'Какой property задаёт отступ внутри?', 'o' => ['margin', 'padding', 'border', 'spacing'], 'c' => 1, 'e' => 'padding — внутренний отступ.'],
            ],
            'Flexbox и Flex Layout' => [
                ['q' => 'Какое display создаёт flex-контейнер?', 'o' => ['flex', 'block', 'inline', 'grid'], 'c' => 0, 'e' => 'display: flex создаёт flex-контейнер.'],
                ['q' => 'Что делает justify-content: space-between?', 'o' => ['Центрирует', 'Распределяет пространство между элементами', 'Выравнивает по краям', 'Сжимает'], 'c' => 1, 'e' => 'space-between распределяет пространство между элементами.'],
                ['q' => 'Какое свойство задаёт направление оси?', 'o' => ['flex-direction', 'flex-axis', 'flex-flow', 'flex-align'], 'c' => 0, 'e' => 'flex-direction определяет направление основной оси.'],
                ['q' => 'Что делает flex-wrap: wrap?', 'o' => ['Сжимает', 'Позволяет переноситься', 'Центрирует', 'Скрывает'], 'c' => 1, 'e' => 'flex-wrap: wrap позволяет перенос элементов.'],
                ['q' => 'Какое свойство выравнивает по поперечной оси?', 'o' => ['justify-content', 'align-items', 'flex-align', 'vertical-align'], 'c' => 1, 'e' => 'align-items выравнивает по поперечной оси.'],
            ],
            'CSS Grid' => [
                ['q' => 'Какое display создаёт grid-контейнер?', 'o' => ['grid', 'block', 'flex', 'inline-grid'], 'c' => 0, 'e' => 'display: grid создаёт grid-контейнер.'],
                ['q' => 'Что делает grid-template-columns: repeat(3, 1fr)?', 'o' => ['3 строки', '3 колонки одинаковой ширины', '3 элемента', 'Размер шрифта'], 'c' => 1, 'e' => 'repeat(3, 1fr) — 3 колонки равной ширины.'],
                ['q' => 'Как задать именованную область?', 'o' => ['grid-area', 'grid-template-areas', 'grid-region', 'grid-zone'], 'c' => 1, 'e' => 'grid-template-areas задаёт именованные области.'],
                ['q' => 'Что такое fr в Grid?', 'o' => ['Фиксированная ширина', 'Дробная единица пространства', 'Процент', 'Пиксель'], 'c' => 1, 'e' => 'fr — дробная единица, доля доступного пространства.'],
                ['q' => 'Какое свойство задаёт расстояние между ячейками?', 'o' => ['gap', 'spacing', 'margin', 'padding'], 'c' => 0, 'e' => 'gap задаёт расстояние между строками и колонками.'],
            ],
            'Адаптивный дизайн' => [
                ['q' => 'Что такое Media Query?', 'o' => ['Запрос к серверу', 'Правило для разных устройств', 'JavaScript функция', 'HTML тег'], 'c' => 1, 'e' => 'Media Query — CSS правило для разных устройств.'],
                ['q' => 'Какой тег meta необходим?', 'o' => ['charset', 'viewport', 'description', 'keywords'], 'c' => 1, 'e' => 'viewport сообщает об установке ширины устройства.'],
                ['q' => 'Что такое mobile-first?', 'o' => ['Сначала мобильная версия', 'Сначала десктоп', 'Только мобильные', 'Без верстки'], 'c' => 0, 'e' => 'Mobile-first — сначала стили для мобильных.'],
                ['q' => 'Какая единица лучше для адаптивности?', 'o' => ['px', 'em', 'rem', 'pt'], 'c' => 2, 'e' => 'rem — относительная единица от корневого шрифта.'],
                ['q' => 'Что делает min-width в media query?', 'o' => ['Стили при ширине от значения', 'Стили при ширине до значения', 'Минимальная ширина элемента', 'Скрывает элемент'], 'c' => 0, 'e' => 'min-width применяет стили при ширине >= значения.'],
            ],
            'CSS Анимации и переходы' => [
                ['q' => 'Какое свойство для плавного перехода?', 'o' => ['animation', 'transition', 'transform', 'motion'], 'c' => 1, 'e' => 'transition для плавного перехода.'],
                ['q' => 'Что делает @keyframes?', 'o' => ['Определяет ключевые кадры анимации', 'Создаёт функцию', 'Подключает шрифты', 'Задаёт цвета'], 'c' => 0, 'e' => '@keyframes определяет промежуточные состояния.'],
                ['q' => 'Какое значение animation-fill-mode сохраняет конец?', 'o' => ['forwards', 'backwards', 'both', 'none'], 'c' => 0, 'e' => 'forwards сохраняет стили последнего кадра.'],
                ['q' => 'Что такое transition-duration?', 'o' => ['Задержка', 'Длительность перехода', 'Скорость', 'Количество кадров'], 'c' => 1, 'e' => 'transition-duration — время перехода.'],
                ['q' => 'Какое значение создаёт ускорение?', 'o' => ['linear', 'ease-in', 'ease-out', 'ease-in-out'], 'c' => 1, 'e' => 'ease-in — ускорение в начале.'],
            ],
            'CSS Переменные и препроцессоры' => [
                ['q' => 'Как объявить CSS-переменную?', 'o' => ['var(--name)', '--name: value', '$name: value', 'const name = value'], 'c' => 1, 'e' => 'CSS-переменные через -- префикс.'],
                ['q' => 'Как использовать переменную?', 'o' => ['--name', 'var(--name)', '$name', '@name'], 'c' => 1, 'e' => 'Для использования — var().'],
                ['q' => 'Что такое SCSS?', 'o' => ['Язык программирования', 'Препроцессор CSS', 'База данных', 'Формат файлов'], 'c' => 1, 'e' => 'SCSS — препроцессор CSS.'],
                ['q' => 'Какой символ для переменных в SCSS?', 'o' => ['--', '@', '$', '#'], 'c' => 2, 'e' => 'В SCSS переменные через $.'],
                ['q' => 'Что делает миксин в SCSS?', 'o' => ['Создаёт переменные', 'Переиспользуемый блок стилей', 'Импортирует файлы', 'Создаёт анимации'], 'c' => 1, 'e' => 'Миксин — переиспользуемый блок стилей с параметрами.'],
            ],
        ],
        'practice' => [
            'Структура HTML-документа' => [
                ['lang' => 'html', 'title' => 'Первый HTML-документ', 'prompt' => 'Создайте HTML-документ с заголовком "Мой сайт" и абзацем приветствия.', 'out' => '<h1>Привет!</h1>', 'start' => '<h1></h1>', 'tests' => [['contains', '<!DOCTYPE html>'], ['contains', '<html'], ['contains', '<title>']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'html', 'title' => 'Список покупок', 'prompt' => 'Создайте нумерованный список из 4 товаров.', 'out' => '<ol><li>Молоко</li><li>Хлеб</li><li>Яйца</li><li>Сыр</li></ol>', 'start' => '<ol>\n</ol>', 'tests' => [['contains', '<ol>'], ['contains', '<li>'], ['count', '<li>', 4]], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'html', 'title' => 'Таблица данных', 'prompt' => 'Создайте таблицу 3x3 с заголовками "Имя", "Возраст", "Город".', 'out' => '<table><tr><th>Имя</th><th>Возраст</th><th>Город</th></tr><tr><td>Анна</td><td>25</td><td>Москва</td></tr></table>', 'start' => '<table>\n</table>', 'tests' => [['contains', '<table>'], ['contains', '<th>'], ['contains', '<td>']], 'diff' => 'medium', 'time' => 15],
            ],
            'Семантические теги HTML5' => [
                ['lang' => 'html', 'title' => 'Семантическая страница', 'prompt' => 'Создайте страницу с header, nav, main, article, aside и footer.', 'out' => '<header></header><nav></nav><main><article></article><aside></aside></main><footer></footer>', 'start' => '<header></header>\n<main>\n</main>', 'tests' => [['contains', '<header>'], ['contains', '<nav>'], ['contains', '<main>'], ['contains', '<article>'], ['contains', '<aside>'], ['contains', '<footer>']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'html', 'title' => 'Блог-разметка', 'prompt' => 'Создайте структуру блога с article и section внутри main.', 'out' => '<main><article><h2>Заголовок</h2><section><p>Содержимое</p></section></article></main>', 'start' => '<main>\n</main>', 'tests' => [['contains', '<article>'], ['contains', '<section>'], ['contains', '<h2>'], ['contains', '<p>']], 'diff' => 'medium', 'time' => 15],
            ],
            'Формы и валидация HTML5' => [
                ['lang' => 'html', 'title' => 'Форма регистрации', 'prompt' => 'Создайте форму с полями: имя (text, required), email (email, required), пароль (password, minlength 8).', 'out' => '<form><input type="text" required><input type="email" required><input type="password" minlength="8"></form>', 'start' => '<form>\n</form>', 'tests' => [['contains', 'type="text"'], ['contains', 'type="email"'], ['contains', 'type="password"'], ['contains', 'required'], ['contains', 'minlength']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'html', 'title' => 'Форма обратной связи', 'prompt' => 'Создайте форму с textarea и select для темы обращения.', 'out' => '<form><textarea></textarea><select><option>Техподдержка</option><option>Продажи</option></select><button type="submit">Отправить</button></form>', 'start' => '<form>\n</form>', 'tests' => [['contains', '<textarea>'], ['contains', '<select>'], ['contains', '<option>'], ['contains', 'submit']], 'diff' => 'medium', 'time' => 15],
            ],
            'CSS Box Model и позиционирование' => [
                ['lang' => 'css', 'title' => 'Стилизация карточки', 'prompt' => 'Создайте CSS для карточки с padding: 20px, border: 1px solid #ccc, box-sizing: border-box.', 'out' => '.card { padding: 20px; border: 1px solid #ccc; box-sizing: border-box; }', 'start' => '.card {\n\n}', 'tests' => [['contains', 'padding'], ['contains', 'border'], ['contains', 'box-sizing']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'css', 'title' => 'Фиксированная кнопка', 'prompt' => 'Создайте стили для кнопки, фиксированной в правом нижнем углу.', 'out' => '.fixed-btn { position: fixed; bottom: 20px; right: 20px; }', 'start' => '.fixed-btn {\n\n}', 'tests' => [['contains', 'position: fixed'], ['contains', 'bottom'], ['contains', 'right']], 'diff' => 'easy', 'time' => 10],
            ],
            'Flexbox и Flex Layout' => [
                ['lang' => 'css', 'title' => 'Flex-навигация', 'prompt' => 'Создайте flex-контейнер для навигации с justify-content: space-between и gap: 10px.', 'out' => '.nav { display: flex; justify-content: space-between; gap: 10px; }', 'start' => '.nav {\n\n}', 'tests' => [['contains', 'display: flex'], ['contains', 'space-between'], ['contains', 'gap']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'css', 'title' => 'Flex-карточки', 'prompt' => 'Создайте flex-контейнер с переносом и центрированием.', 'out' => '.cards { display: flex; flex-wrap: wrap; align-items: center; }', 'start' => '.cards {\n\n}', 'tests' => [['contains', 'display: flex'], ['contains', 'flex-wrap'], ['contains', 'align-items']], 'diff' => 'medium', 'time' => 15],
            ],
            'CSS Grid' => [
                ['lang' => 'css', 'title' => 'Grid-сетка', 'prompt' => 'Создайте сетку 3 колонки с gap: 20px.', 'out' => '.grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }', 'start' => '.grid {\n\n}', 'tests' => [['contains', 'display: grid'], ['contains', 'repeat(3, 1fr)'], ['contains', 'gap']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'css', 'title' => 'Named grid areas', 'prompt' => 'Создайте layout с header, main и footer через grid-template-areas.', 'out' => '.layout { display: grid; grid-template-areas: "header" "main" "footer"; }', 'start' => '.layout {\n\n}', 'tests' => [['contains', 'display: grid'], ['contains', 'grid-template-areas'], ['contains', 'header']], 'diff' => 'medium', 'time' => 15],
            ],
            'Адаптивный дизайн' => [
                ['lang' => 'css', 'title' => 'Media Query для мобильных', 'prompt' => 'Создайте media query для экранов до 768px, изменив flex-direction на column.', 'out' => '@media (max-width: 768px) { .container { flex-direction: column; } }', 'start' => '@media (max-width: 768px) {\n\n}', 'tests' => [['contains', '@media'], ['contains', 'max-width'], ['contains', 'flex-direction: column']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'css', 'title' => 'Адаптивная типографика', 'prompt' => 'Создайте media query с font-size: 14px для экранов до 480px.', 'out' => '@media (max-width: 480px) { body { font-size: 14px; } }', 'start' => '@media (max-width: 480px) {\n\n}', 'tests' => [['contains', '@media'], ['contains', 'max-width: 480px'], ['contains', 'font-size: 14px']], 'diff' => 'easy', 'time' => 10],
            ],
            'CSS Анимации и переходы' => [
                ['lang' => 'css', 'title' => 'Hover-эффект', 'prompt' => 'Создайте transition для кнопки с изменением background-color за 0.3s.', 'out' => '.btn { transition: background-color 0.3s ease; }', 'start' => '.btn {\n\n}', 'tests' => [['contains', 'transition'], ['contains', 'background-color'], ['contains', '0.3s']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'css', 'title' => 'Ключевая анимация', 'prompt' => 'Создайте анимацию fadeIn с opacity от 0 до 1.', 'out' => '@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }', 'start' => '@keyframes fadeIn {\n\n}', 'tests' => [['contains', '@keyframes fadeIn'], ['contains', 'opacity: 0'], ['contains', 'opacity: 1']], 'diff' => 'medium', 'time' => 15],
            ],
            'CSS Переменные и препроцессоры' => [
                ['lang' => 'css', 'title' => 'CSS-переменные', 'prompt' => 'Объявите --primary-color: #3498db и используйте для кнопки.', 'out' => ':root { --primary-color: #3498db; } .btn { background: var(--primary-color); }', 'start' => ':root {\n\n}\n.btn {\n\n}', 'tests' => [['contains', '--primary-color'], ['contains', 'var(--primary-color)']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'scss', 'title' => 'SCSS миксин', 'prompt' => 'Создайте миксин flex-center.', 'out' => '@mixin flex-center { display: flex; justify-content: center; align-items: center; }', 'start' => '@mixin flex-center {\n\n}', 'tests' => [['contains', '@mixin flex-center'], ['contains', 'display: flex'], ['contains', 'justify-content: center']], 'diff' => 'medium', 'time' => 15],
            ],
        ],
    ],

    2 => [
        'lessons' => [
            'Основы JavaScript' => '<h2>Основы JavaScript</h2><p>JavaScript — язык программирования для интерактивных веб-страниц.</p><h3>Подключение</h3><pre><code>&lt;script src="app.js"&gt;&lt;/script&gt;&lt;script&gt; console.log("Привет"); &lt;/script&gt;</code></pre><h3>Переменные</h3><pre><code>let name = "Анна";const age = 25;var oldWay = "устаревший";</code></pre><h3>Типы данных</h3><ul><li>String, Number, Boolean</li><li>undefined, null</li><li>Object, Array</li></ul>',
            'Переменные и типы данных' => '<h2>Переменные и типы данных</h2><p>Динамическая типизация с пониманием типов.</p><h3>typeof</h3><pre><code>typeof "hello"    // "string"typeof 42         // "number"typeof true       // "boolean"</code></pre><h3>Преобразование</h3><pre><code>String(42)       // "42"Number("42")     // 42Boolean(0)      // false</code></pre>',
            'Функции и область видимости' => '<h2>Функции и область видимости</h2><p>Основные строительные блоки JavaScript.</p><h3>Типы функций</h3><pre><code>function greet(name) { return `Привет, ${name}!`; }const add = function(a, b) { return a + b; };const multiply = (a, b) => a * b;</code></pre><h3>Область видимости</h3><pre><code>let global = "глобальная";function outer() { let outerVar = "внешняя"; function inner() { console.log(global); console.log(outerVar); } }</code></pre>',
            'Массивы и объекты' => '<h2>Массивы и объекты</h2><p>Основные структуры данных.</p><h3>Массивы</h3><pre><code>const fruits = ["яблоко", "банан"];fruits.push("груша");fruits.filter(f => f.length > 5);fruits.map(f => f.toUpperCase());</code></pre><h3>Объекты</h3><pre><code>const person = { name: "Анна", age: 25, greet() { return `Привет, я ${this.name}`; } };const { name, age } = person;</code></pre>',
            'DOM манипуляции' => '<h2>DOM манипуляции</h2><p>Изменение HTML и CSS через JavaScript.</p><h3>Поиск</h3><pre><code>document.getElementById("main");document.querySelector(".card");document.querySelectorAll("p");</code></pre><h3>Изменение</h3><pre><code>element.textContent = "Новый текст";element.innerHTML = "&lt;strong&gt;Жирный&lt;/strong&gt;";element.classList.add("visible");</code></pre>',
            'События в JavaScript' => '<h2>События в JavaScript</h2><p>Реакция на действия пользователя.</p><h3>Обработчики</h3><pre><code>button.addEventListener("click", function(event) { console.log("Нажато!"); });</code></pre><h3>Популярные события</h3><ul><li>click, submit, keydown</li><li>scroll, load</li></ul>',
            'Асинхронность: Callbacks и Promises' => '<h2>Асинхронность: Callbacks и Promises</h2><p>Асинхронный код без блокировки.</p><h3>Callback</h3><pre><code>function loadData(callback) { setTimeout(() => callback("Данные"), 1000); }</code></pre><h3>Promise</h3><pre><code>const promise = new Promise((resolve, reject) => { resolve("Данные"); });promise.then(data => console.log(data)).catch(err => console.error(err));</code></pre>',
            'async/await и Fetch API' => '<h2>async/await и Fetch API</h2><p>Синтаксический сахар для Promise.</p><h3>Fetch</h3><pre><code>fetch("https://api.example.com/data").then(r => r.json()).then(data => console.log(data));</code></pre><h3>Async/Await</h3><pre><code>async function getData() { const response = await fetch("https://api.example.com/data"); const data = await response.json(); }</code></pre>',
            'Тест по JavaScript' => '<h2>Тест по JavaScript</h2><p>Проверьте свои знания JavaScript.</p>',
        ],
        'quizzes' => [
            'Основы JavaScript' => [
                ['q' => 'Какой оператор объявляет константу?', 'o' => ['let', 'var', 'const', 'define'], 'c' => 2, 'e' => 'const для неизменяемых значений.'],
                ['q' => 'Как вывести в консоль?', 'o' => ['print("text")', 'console.log("text")', 'echo("text")', 'log("text")'], 'c' => 1, 'e' => 'console.log() — стандартный вывод.'],
                ['q' => 'Тип данных null?', 'o' => ['null', 'undefined', 'object', 'string'], 'c' => 2, 'e' => 'typeof null возвращает "object" — баг JS.'],
                ['q' => 'Как подключить внешний JS?', 'o' => ['<link src="app.js">', '<script src="app.js"></script>', '<javascript src="app.js">', '<import src="app.js">'], 'c' => 1, 'e' => 'Тег script с src.'],
                ['q' => 'Чем let отличается от var?', 'o' => ['Ничем', 'let имеет блочную область видимости', 'var быстрее', 'let только в strict mode'], 'c' => 1, 'e' => 'let — блочная область видимости.'],
            ],
            'Переменные и типы данных' => [
                ['q' => 'Что вернёт typeof 42?', 'o' => ['"int"', '"number"', '"integer"', '"float"'], 'c' => 1, 'e' => 'Все числа — тип "number".'],
                ['q' => 'Как преобразовать строку "123" в число?', 'o' => ['toNumber("123")', 'Number("123")', 'int("123")', 'parse("123")'], 'c' => 1, 'e' => 'Number() преобразует строку.'],
                ['q' => 'Какое falsy-значение?', 'o' => ['"0"', '0', '"false"', '[]'], 'c' => 1, 'e' => '0 — falsy.'],
                ['q' => 'Что такое template literal?', 'o' => ['Обычная строка', 'Строка с ${} для интерполяции', 'HTML шаблон', 'Комментарий'], 'c' => 1, 'e' => 'Template literals с обратными кавычками и ${}.'],
                ['q' => 'Что вернёт "5" + 3?', 'o' => ['8', '"53"', 'NaN', 'Ошибка'], 'c' => 1, 'e' => 'Конкатенация строки и числа.'],
            ],
            'Функции и область видимости' => [
                ['q' => 'Как объявить стрелочную функцию?', 'o' => ['() -> {}', '() => {}', 'function() => {}', '=> () {}'], 'c' => 1, 'e' => 'Стрелочные через =>.'],
                ['q' => 'Что такое замыкание?', 'o' => ['Функция без возврата', 'Функция с доступом к внешней области', 'Закрытый цикл', 'Приватная переменная'], 'c' => 1, 'e' => 'Замыкание сохраняет доступ к внешним переменным.'],
                ['q' => 'Что делает spread?', 'o' => ['Создаёт копию', 'Разворачивает коллекцию', 'Создаёт массив', 'Удаляет элемент'], 'c' => 1, 'e' => 'Spread разворачивает элементы.'],
                ['q' => 'Значение по умолчанию?', 'o' => ['function f(a = 10)', 'function f(a: 10)', 'function f(a => 10)', 'function f(default a = 10)'], 'c' => 0, 'e' => 'Через = после параметра.'],
                ['q' => 'Что такое рекурсия?', 'o' => ['Цикл', 'Функция, вызывающая сама себя', 'Массив функций', 'Клонирование'], 'c' => 1, 'e' => 'Рекурсия — вызов самой себя.'],
            ],
            'Массивы и объекты' => [
                ['q' => 'Как добавить элемент в конец?', 'o' => ['array.add()', 'array.push()', 'array.append()', 'array.insert()'], 'c' => 1, 'e' => 'push() добавляет в конец.'],
                ['q' => 'Что делает map()?', 'o' => ['Фильтрует', 'Создаёт новый массив с результатами', 'Находит элемент', 'Сортирует'], 'c' => 1, 'e' => 'map() создаёт новый массив.'],
                ['q' => 'Длина массива?', 'o' => ['array.length()', 'array.size', 'array.length', 'array.count()'], 'c' => 2, 'e' => 'Свойство length.'],
                ['q' => 'Деструктуризация?', 'o' => ['Удаление переменных', 'Разбор структуры на переменные', 'Копирование объекта', 'Сортировка'], 'c' => 1, 'e' => 'Извлечение значений в переменные.'],
                ['q' => 'Объединение массивов?', 'o' => ['array1 + array2', '[...array1, ...array2]', 'array1.concat(array2)', 'Все варианты B и C'], 'c' => 3, 'e' => 'Spread и concat работают.'],
            ],
            'DOM манипуляции' => [
                ['q' => 'Метод поиска первого элемента?', 'o' => ['querySelector()', 'getElementById()', 'querySelectorAll()', 'Все A и B'], 'c' => 3, 'e' => 'querySelector() и getElementById() возвращают первый.'],
                ['q' => 'innerHTML vs textContent?', 'o' => ['Ничем', 'innerHTML интерпретирует HTML', 'textContent быстрее', 'innerHTML безопаснее'], 'c' => 1, 'e' => 'innerHTML парсит теги, textContent — нет.'],
                ['q' => 'Добавить CSS-класс?', 'o' => ['element.addClass("cls")', 'element.classList.add("cls")', 'element.className.add("cls")', 'element.css("cls")'], 'c' => 1, 'e' => 'classList.add().'],
                ['q' => 'Создать DOM-элемент?', 'o' => ['new Element("div")', 'document.createElement("div")', 'document.buildElement("div")', '$("div")'], 'c' => 1, 'e' => 'document.createElement().'],
                ['q' => 'preventDefault()?', 'o' => ['Останавливает всплытие', 'Отменяет действие по умолчанию', 'Удаляет элемент', 'Создаёт событие'], 'c' => 1, 'e' => 'Отменяет стандартное поведение.'],
            ],
            'События в JavaScript' => [
                ['q' => 'Добавить обработчик?', 'o' => ['onclick()', 'addEventListener()', 'on()', 'bind()'], 'c' => 1, 'e' => 'addEventListener() — стандартный метод.'],
                ['q' => 'Что такое bubbling?', 'o' => ['Создание события', 'Всплытие от дочернего к родительскому', 'Поглощение', 'Удаление'], 'c' => 1, 'e' => 'Bubbling — всплытие события вверх.'],
                ['q' => 'stopPropagation()?', 'o' => ['Останавливает всплытие', 'Удаляет событие', 'Создаёт событие', 'Блокирует интерфейс'], 'c' => 0, 'e' => 'Предотвращает всплытие.'],
                ['q' => 'Объект события с клавишей?', 'o' => ['event.key', 'event.code', 'event.keyCode', 'Все варианты'], 'c' => 3, 'e' => 'Все три содержат информацию о клавише.'],
                ['q' => 'Делегирование событий?', 'o' => ['Создание событий', 'Обработка на родительском элементе', 'Удаление обработчиков', 'Копирование событий'], 'c' => 1, 'e' => 'Обработка на родителе с проверкой цели.'],
            ],
            'Асинхронность: Callbacks и Promises' => [
                ['q' => 'Что такое callback?', 'o' => ['Функция обратного вызова', 'Тип переменной', 'Цикл', 'Оператор'], 'c' => 0, 'e' => 'Функция, вызываемая после завершения операции.'],
                ['q' => 'Какой метод обрабатывает успех?', 'o' => ['catch()', 'then()', 'finally()', 'resolve()'], 'c' => 1, 'e' => 'then() для resolved.'],
                ['q' => 'Callback hell?', 'o' => ['Ошибка', 'Глубокая вложенность колбэков', 'Отсутствие Promise', 'Мёртвый цикл'], 'c' => 1, 'e' => 'Проблема вложенности колбэков.'],
                ['q' => 'catch() для?', 'o' => ['then()', 'catch()', 'error()', 'fail()'], 'c' => 1, 'e' => 'catch() для rejected.'],
                ['q' => 'Promise.all()?', 'o' => ['Последовательно', 'Параллельно и ждёт все', 'Первый результат', 'Удаляет промисы'], 'c' => 1, 'e' => 'Параллельно и ждёт все.'],
            ],
            'async/await и Fetch API' => [
                ['q' => 'async делает?', 'o' => ['Функцию асинхронной', 'Создаёт Promise', 'Ждёт результат', 'Обрабатывает ошибки'], 'c' => 0, 'e' => 'Делает функцию асинхронной, возвращает Promise.'],
                ['q' => 'await делает?', 'o' => ['Создаёт промис', 'Приостанавливает до результата', 'Удаляет промис', 'Пропускает ошибки'], 'c' => 1, 'e' => 'Приостанавливает до разрешения промиса.'],
                ['q' => 'fetch() по умолчанию?', 'o' => ['GET', 'POST', 'PUT', 'DELETE'], 'c' => 0, 'e' => 'По умолчанию GET.'],
                ['q' => 'Получить JSON из fetch?', 'o' => ['response.text()', 'response.json()', 'response.data()', 'response.parse()'], 'c' => 1, 'e' => 'response.json() парсит JSON.'],
                ['q' => 'POST через fetch?', 'o' => ['fetch(url, {method: "POST"})', 'fetch.post(url)', 'fetch(url).post()', 'http.post(url)'], 'c' => 0, 'e' => 'Через объект options.'],
            ],
        ],
        'practice' => [
            'Основы JavaScript' => [
                ['lang' => 'javascript', 'title' => 'Hello World', 'prompt' => 'Выведите "Hello, World!" в консоль.', 'out' => 'Hello, World!', 'start' => 'console.log("Hello, World!");', 'tests' => [['contains', 'console.log']], 'diff' => 'easy', 'time' => 5],
                ['lang' => 'javascript', 'title' => 'Калькулятор', 'prompt' => 'Создайте функцию add(a, b), возвращающую сумму.', 'out' => '7', 'start' => 'function add(a, b) {\n\n}', 'tests' => [['contains', 'function add'], ['contains', 'return']], 'diff' => 'easy', 'time' => 10],
            ],
            'Переменные и типы данных' => [
                ['lang' => 'javascript', 'title' => 'Проверка типа', 'prompt' => 'Создайте myVar = 42 и выведите typeof.', 'out' => 'number', 'start' => '', 'tests' => [['contains', 'typeof'], ['contains', 'number']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'javascript', 'title' => 'Преобразование типов', 'prompt' => 'Преобразуйте "123" в число.', 'out' => '123', 'start' => '', 'tests' => [['contains', 'Number('], ['contains', '123']], 'diff' => 'easy', 'time' => 10],
            ],
            'Функции и область видимости' => [
                ['lang' => 'javascript', 'title' => 'Стрелочная функция', 'prompt' => 'Создайте стрелочную функцию square для квадрата числа.', 'out' => '9', 'start' => 'const square = ', 'tests' => [['contains', '=>'], ['contains', '*']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'javascript', 'title' => 'Замыкание', 'prompt' => 'Создайте counter(), возвращающую функцию увеличения счётчика.', 'out' => '1', 'start' => 'function counter() {\n  let count = 0;\n  return ', 'tests' => [['contains', 'return'], ['contains', 'count']], 'diff' => 'medium', 'time' => 15],
            ],
            'Массивы и объекты' => [
                ['lang' => 'javascript', 'title' => 'Фильтрация', 'prompt' => 'Отфильтруйте числа, оставив чётные.', 'out' => '[2, 4, 6]', 'start' => 'const numbers = [1, 2, 3, 4, 5, 6];\nconst even = ', 'tests' => [['contains', 'filter'], ['contains', '% 2']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'javascript', 'title' => 'Деструктуризация', 'prompt' => 'Создайте объект user {name: "Анна", age: 25} и извлеките через деструктуризацию.', 'out' => 'Анна 25', 'start' => 'const user = { name: "Анна", age: 25 };\nconst ', 'tests' => [['contains', 'name:'], ['contains', 'age:'], ['const {']], 'diff' => 'medium', 'time' => 15],
            ],
            'DOM манипуляции' => [
                ['lang' => 'javascript', 'title' => 'Изменение текста', 'prompt' => 'Найдите #title и измените текст на "Привет!".', 'out' => '', 'start' => 'const title = ', 'tests' => [['contains', 'getElementById'], ['contains', 'textContent']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'javascript', 'title' => 'Создание элемента', 'prompt' => 'Создайте div с классом "card" и текстом "Карточка".', 'out' => '', 'start' => 'const div = ', 'tests' => [['contains', 'createElement'], ['contains', 'classList'], ['contains', 'appendChild']], 'diff' => 'medium', 'time' => 15],
            ],
            'События в JavaScript' => [
                ['lang' => 'javascript', 'title' => 'Обработчик клика', 'prompt' => 'Добавьте обработчик клика на кнопку, выводящий "Нажато!".', 'out' => 'Нажато!', 'start' => 'const button = document.querySelector("button");\nbutton.', 'tests' => [['contains', 'addEventListener'], ['contains', 'click']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'javascript', 'title' => 'Предотвращение отправки', 'prompt' => 'Предотвратите отправку формы при сабмите.', 'out' => '', 'start' => 'form.addEventListener("submit", function(event) {\n', 'tests' => [['contains', 'preventDefault'], ['contains', 'submit']], 'diff' => 'medium', 'time' => 15],
            ],
            'Асинхронность: Callbacks и Promises' => [
                ['lang' => 'javascript', 'title' => 'Promise', 'prompt' => 'Создайте Promise, разрешающийся через 1 секунду со значением "Готово".', 'out' => 'Готово', 'start' => 'const myPromise = new Promise((resolve, reject) => {\n  setTimeout(() => {\n', 'tests' => [['contains', 'resolve'], ['contains', 'setTimeout'], ['contains', '1000']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'javascript', 'title' => 'Цепочка промисов', 'prompt' => 'Цепочка: Promise.resolve(2).then(x => x * 3).', 'out' => '6', 'start' => 'Promise.resolve(2)\n  .then(', 'tests' => [['contains', 'then'], ['contains', '*']], 'diff' => 'medium', 'time' => 15],
            ],
            'async/await и Fetch API' => [
                ['lang' => 'javascript', 'title' => 'Async функция', 'prompt' => 'Создайте async-функцию, возвращающую "Привет".', 'out' => 'Привет', 'start' => 'async function greet() {\n', 'tests' => [['contains', 'async'], ['contains', 'return']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'javascript', 'title' => 'Fetch запрос', 'prompt' => 'Используйте fetch для GET запроса к URL.', 'out' => '', 'start' => 'async function getUsers() {\n  try {\n    const response = ', 'tests' => [['contains', 'fetch'], ['contains', 'json()'], ['contains', 'await']], 'diff' => 'hard', 'time' => 20],
            ],
        ],
    ],

    3 => [
        'lessons' => [
            'Основы PHP' => '<h2>Основы PHP</h2><p>PHP — серверный язык для веб-разработки.</p><h3>Базовый синтаксис</h3><pre><code>&lt;?php\necho "Привет, мир!";$name = "Анna";$age = 25;if ($age >= 18) { echo "Взрослый"; }?&gt;</code></pre><h3>Переменные</h3><ul><li>Начинаются с $</li><li>Чувствительны к регистру</li><li>Динамическая типизация</li></ul>',
            'Переменные и операторы' => '<h2>Переменные и операторы</h2><p>Основные типы данных и операторы.</p><h3>Типы</h3><pre><code>&lt;?php$string = "строка";$int = 42;$float = 3.14;$bool = true;$arr = [1, 2, 3];var_dump($string);?&gt;</code></pre><h3>Операторы</h3><pre><code>$a + $b;   // сложение$a * $b;   // умножение$a == $b;  // равенство$a === $b; // строгое равенство</code></pre>',
            'Массивы в PHP' => '<h2>Массивы в PHP</h2><p>Индексированные и ассоциативные массивы.</p><h3>Примеры</h3><pre><code>&lt;?php$fruits = ["яблоко", "банан"];$fruits[] = "груша";$person = ["name" => "Анна", "age" => 25];foreach ($person as $key => $val) { echo "$key: $val"; }?&gt;</code></pre>',
            'Функции в PHP' => '<h2>Функции в PHP</h2><h3>Примеры</h3><pre><code>&lt;?phpfunction greet($name) { return "Привет, $name!"; }function add($a, $b = 0) { return $a + $b; }function multiply(float $a, float $b): float { return $a * $b; }?&gt;</code></pre>',
            'Работа с формами' => '<h2>Работа с формами</h2><p>Обработка через $_GET и $_POST.</p><pre><code>&lt;?phpif ($_SERVER["REQUEST_METHOD"] === "POST") { $name = $_POST["name"] ?? "";$email = $_POST["email"] ?? "";}$name = htmlspecialchars($_POST["name"]);?&gt;</code></pre>',
            'Сессии и куки' => '<h2>Сессии и куки</h2><h3>Куки</h3><pre><code>&lt;?phpsetcookie("user", "Анна", time() + 3600 * 24 * 30);$user = $_COOKIE["user"] ?? "Гость";?&gt;</code></pre><h3>Сессии</h3><pre><code>&lt;?phpsession_start();$_SESSION["user_id"] = 123;session_destroy();?&gt;</code></pre>',
            'Работа с файлами' => '<h2>Работа с файлами</h2><h3>Чтение</h3><pre><code>&lt;?php$content = file_get_contents("data.txt");$lines = file("data.txt", FILE_IGNORE_NEW_LINES);?&gt;</code></pre><h3>Запись</h3><pre><code>&lt;?phpfile_put_contents("log.txt", "Запись\n", FILE_APPEND);?&gt;</code></pre>',
            'ООП в PHP' => '<h2>ООП в PHP</h2><pre><code>&lt;?php\nclass User {\n    public string $name;\n    private int $age;\n    public function __construct(string $name, int $age) {\n        $this->name = $name;\n        $this->age = $age;\n    }\n    public function greet(): string {\n        return "Привет, я {$this->name}";\n    }\n}\n$user = new User("Анна", 25);\necho $user->greet();\n?&gt;</code></pre><h3>Наследование</h3><pre><code>class Admin extends User { public string $role; }</code></pre>',
            'Тест по PHP' => '<h2>Тест по PHP</h2><p>Проверьте свои знания PHP.</p>',
        ],
        'quizzes' => [
            'Основы PHP' => [
                ['q' => 'Как начинается блок PHP?', 'o' => ['<?php', '<script>', '<php>', '<?'], 'c' => 0, 'e' => '<?php — начало блока PHP.'],
                ['q' => 'Как вывести текст?', 'o' => ['print()', 'echo', 'console.log()', 'System.out.println()'], 'c' => 1, 'e' => 'echo — основная конструкция вывода.'],
                ['q' => 'С чего начинается переменная?', 'o' => ['#', '@', '$', '&'], 'c' => 2, 'e' => 'Все переменные начинаются с $.'],
                ['q' => 'Строгое сравнение?', 'o' => ['==', '===', '=', '!='], 'c' => 1, 'e' => '=== без приведения типов.'],
                ['q' => 'Что такое суперглобальный массив?', 'o' => ['Локальная переменная', 'Глобальный доступ к данным', 'Константа', 'Функция'], 'c' => 1, 'e' => 'Доступны из任何 точки скрипта.'],
            ],
            'Переменные и операторы' => [
                ['q' => 'var_dump(42)?', 'o' => ['42', 'int(42)', '"42"', 'integer(42)'], 'c' => 1, 'e' => 'var_dump показывает тип и значение.'],
                ['q' => 'Оператор ??', 'o' => ['Сложение', 'Null coalescing', 'Сравнение', 'Присваивание'], 'c' => 1, 'e' => '?? возвращает значение по умолчанию.'],
                ['q' => 'Тип true?', 'o' => ['string', 'bool', 'int', 'float'], 'c' => 1, 'e' => 'boolean.'],
                ['q' => 'Конкатенация?', 'o' => ['Сложение', 'Точка (.)', 'Разделение', 'Перевод'], 'c' => 1, 'e' => 'Оператор . склеивает строки.'],
                ['q' => 'empty("")?', 'o' => ['false', 'true', 'null', '0'], 'c' => 1, 'e' => 'Пустая строка — пустая.'],
            ],
            'Массивы в PHP' => [
                ['q' => 'Добавить элемент в конец?', 'o' => ['array_push()', '$arr[] = value', 'array_add()', 'append()'], 'c' => 1, 'e' => '[] добавляет в конец.'],
                ['q' => 'Количество элементов?', 'o' => ['count()', 'length()', 'size()', 'len()'], 'c' => 0, 'e' => 'count().'],
                ['q' => 'Ассоциативный массив?', 'o' => ['С числами', 'С ключами-строками', 'Объектов', 'Функций'], 'c' => 1, 'e' => 'Строковые ключи.'],
                ['q' => 'Найти значение?', 'o' => ['find()', 'array_search()', 'search()', 'locate()'], 'c' => 1, 'e' => 'array_search() возвращает ключ.'],
                ['q' => 'Сортировать?', 'o' => ['sort()', 'array_sort()', 'order()', 'arrange()'], 'c' => 0, 'e' => 'sort() по возрастанию.'],
            ],
            'Функции в PHP' => [
                ['q' => 'Объявление функции?', 'o' => ['def func()', 'function func()', 'func func()', 'void func()'], 'c' => 1, 'e' => 'Через function.'],
                ['q' => 'Параметр по умолчанию?', 'o' => ['function f($a = 10)', 'function f($a: 10)', 'function f(default $a)', 'function f($a => 10)'], 'c' => 0, 'e' => 'Через = после параметра.'],
                ['q' => 'Оператор ... в аргументах?', 'o' => ['Комментарий', 'Rest/spread', 'Тип данных', 'Цикл'], 'c' => 1, 'e' => 'Собирает переменное количество аргументов.'],
                ['q' => 'Тип возвращаемого?', 'o' => ['function f(): int', 'function f() -> int', 'function f() :: int', 'int function f()'], 'c' => 0, 'e' => 'Через двоеточие после скобок.'],
                ['q' => 'Замыкание в PHP?', 'o' => ['Без return', 'Анонимная функция с внешними переменными', 'Приватная', 'Рекурсия'], 'c' => 1, 'e' => 'Анонимная функция с захватом контекста.'],
            ],
            'Работа с формами' => [
                ['q' => 'Данные POST?', 'o' => ['$_GET', '$_POST', '$_REQUEST', '$_FORM'], 'c' => 1, 'e' => '$_POST — данные POST.'],
                ['q' => 'Защита от XSS?', 'o' => ['htmlspecialchars()', 'escape()', 'sanitise()', 'clean()'], 'c' => 0, 'e' => 'htmlspecialchars() преобразует символы.'],
                ['q' => 'Метод запроса?', 'o' => ['$_SERVER["REQUEST_METHOD"]', 'request_method()', 'getMethod()', 'HTTP_METHOD'], 'c' => 0, 'e' => '$_SERVER["REQUEST_METHOD"].'],
                ['q' => 'filter_var()?', 'o' => ['Фильтрует переменные', 'Валидирует и очищает', 'Удаляет', 'Создаёт фильтры'], 'c' => 1, 'e' => 'Валидация и фильтрация.'],
                ['q' => 'Безопасный GET данных?', 'o' => ['$_POST["name"]', 'htmlspecialchars($_POST["name"] ?? "")', 'echo $_POST["name"]', 'eval($_POST["name"])'], 'c' => 1, 'e' => 'Комбинация null coalescing и htmlspecialchars.'],
            ],
            'Сессии и куки' => [
                ['q' => 'Начать сессию?', 'o' => ['session_start()', 'start_session()', 'begin_session()', 'session_init()'], 'c' => 0, 'e' => 'session_start().'],
                ['q' => 'Где хранятся данные сессии?', 'o' => ['В cookie', 'На сервере в файле', 'В localStorage', 'В URL'], 'c' => 1, 'e' => 'На сервере.'],
                ['q' => 'Установить куку?', 'o' => ['$_COOKIE["name"] = value', 'setcookie("name", "value")', 'cookie_set()', 'create_cookie()'], 'c' => 1, 'e' => 'setcookie().'],
                ['q' => 'Удалить сессию?', 'o' => ['session_close()', 'session_destroy()', 'session_clear()', 'session_delete()'], 'c' => 1, 'e' => 'session_destroy().'],
                ['q' => 'Суперглобальная переменная?', 'o' => ['Локальная', 'Всегда доступная', 'Глобальная константа', 'Функция'], 'c' => 1, 'e' => 'Доступна из任何 точки.'],
            ],
            'Работа с файлами' => [
                ['q' => 'Прочитать файл?', 'o' => ['file_get_contents()', 'read_file()', 'open()', 'fread()'], 'c' => 0, 'e' => 'file_get_contents().'],
                ['q' => 'Записать данные?', 'o' => ['write()', 'file_put_contents()', 'save()', 'fwrite()'], 'c' => 1, 'e' => 'file_put_contents().'],
                ['q' => 'FILE_APPEND?', 'o' => ['Создаёт файл', 'Добавляет в конец', 'Удаляет', 'Переименовывает'], 'c' => 1, 'e' => 'Добавляет в конец файла.'],
                ['q' => 'Безопасное открытие?', 'o' => ['fopen("file", "w")', 'open_file("file")', 'file_open("file")', 'read("file")'], 'c' => 0, 'e' => 'fopen() с режимом.'],
                ['q' => 'Проверка существования?', 'o' => ['file_exists()', 'is_file()', 'file_check()', 'exists()'], 'c' => 0, 'e' => 'file_exists().'],
            ],
            'ООП в PHP' => [
                ['q' => 'Объявление класса?', 'o' => ['class User {}', 'User class {}', 'object User {}', 'new User()'], 'c' => 0, 'e' => 'Через class.'],
                ['q' => '__construct?', 'o' => ['Удаляет', 'Инициализирует при создании', 'Клонирует', 'Вызывает метод'], 'c' => 1, 'e' => 'Конструктор при создании.'],
                ['q' => 'Инкапсуляция?', 'o' => ['Наследование', 'Сокрытие через модификаторы', 'Полиморфизм', 'Абстракция'], 'c' => 1, 'e' => 'Сокрытие данных.'],
                ['q' => 'Наследование?', 'o' => ['class B extends A', 'class B : A', 'class B inherits A', 'class B uses A'], 'c' => 0, 'e' => 'extends.'],
                ['q' => 'Интерфейс?', 'o' => ['Класс без методов', 'Контракт с методами', 'Абстрактный класс', 'Пространство имён'], 'c' => 1, 'e' => 'Набор методов для реализации.'],
            ],
        ],
        'practice' => [
            'Основы PHP' => [
                ['lang' => 'php', 'title' => 'Hello PHP', 'prompt' => 'Выведите "Hello, PHP!" через echo.', 'out' => 'Hello, PHP!', 'start' => "<?php\necho \"Hello, PHP!\";\n?>", 'tests' => [['contains', 'echo'], ['contains', 'Hello, PHP!']], 'diff' => 'easy', 'time' => 5],
                ['lang' => 'php', 'title' => 'Условие', 'prompt' => 'Создайте $age = 20 и выведите "Взрослый" если >= 18.', 'out' => 'Взрослый', 'start' => "<?php\n\$age = 20;\n", 'tests' => [['contains', 'if'], ['contains', '>= 18'], ['contains', 'echo']], 'diff' => 'easy', 'time' => 10],
            ],
            'Переменные и операторы' => [
                ['lang' => 'php', 'title' => 'Типы данных', 'prompt' => 'Создайте строку "hello" и выведите var_dump.', 'out' => 'string(5) "hello"', 'start' => "<?php\n\$string = \"hello\";\n", 'tests' => [['contains', 'var_dump']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'php', 'title' => 'Оператор остатка', 'prompt' => 'Вычислите 17 % 5 и выведите.', 'out' => '2', 'start' => "<?php\n", 'tests' => [['contains', '%'], ['contains', 'echo']], 'diff' => 'easy', 'time' => 10],
            ],
            'Массивы в PHP' => [
                ['lang' => 'php', 'title' => 'Ассоциативный массив', 'prompt' => 'Создайте $person с name, age, city и выведите через foreach.', 'out' => 'name: Анна age: 25 city: Москва', 'start' => "<?php\n\$person = [\n    \"name\" => \"Анна\",\n    \"age\" => 25,\n    \"city\" => \"Москва\"\n];\n", 'tests' => [['contains', 'foreach'], ['contains', '=>'], ['contains', 'echo']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'php', 'title' => 'Фильтрация', 'prompt' => 'Отфильтруйте положительные числа через array_filter.', 'out' => '1 2 3', 'start' => "<?php\n\$numbers = [-1, 2, -3, 1, 3];\n\$positive = ", 'tests' => [['contains', 'array_filter'], ['contains', '$']], 'diff' => 'medium', 'time' => 15],
            ],
            'Функции в PHP' => [
                ['lang' => 'php', 'title' => 'Функция приветствия', 'prompt' => 'Создайте greet($name) -> "Привет, {name}!".', 'out' => 'Привет, Анна!', 'start' => "<?php\nfunction greet(\$name) {\n", 'tests' => [['contains', 'function greet'], ['contains', 'return'], ['contains', 'Привет']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'php', 'title' => 'Факториал', 'prompt' => 'Создайте рекурсивную factorial($n).', 'out' => '120', 'start' => "<?php\nfunction factorial(\$n) {\n", 'tests' => [['contains', 'function factorial'], ['contains', 'return'], ['contains', '*']], 'diff' => 'medium', 'time' => 15],
            ],
            'Работа с формами' => [
                ['lang' => 'php', 'title' => 'Обработка POST', 'prompt' => 'Обработайте POST: если name установлен, выведите приветствие.', 'out' => 'Привет, Анна!', 'start' => "<?php\nif (\$_SERVER[\"REQUEST_METHOD\"] === \"POST\") {\n    \$name = \$_POST[\"name\"] ?? \"\";\n", 'tests' => [['contains', '$_POST'], ['contains', 'REQUEST_METHOD'], ['contains', 'echo']], 'diff' => 'medium', 'time' => 15],
            ],
            'Сессии и куки' => [
                ['lang' => 'php', 'title' => 'Сессия', 'prompt' => 'Запустите сессию и сохраните имя в $_SESSION.', 'out' => '', 'start' => "<?php\nsession_start();\n", 'tests' => [['contains', 'session_start()'], ['contains', '$_SESSION']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'php', 'title' => 'Куки', 'prompt' => 'Установите куку "theme" = "dark" на 30 дней.', 'out' => '', 'start' => "<?php\n", 'tests' => [['contains', 'setcookie("theme"'], ['contains', 'dark']], 'diff' => 'easy', 'time' => 10],
            ],
            'Работа с файлами' => [
                ['lang' => 'php', 'title' => 'Чтение файла', 'prompt' => 'Прочитайте data.txt и выведите.', 'out' => 'Привет из файла', 'start' => "<?php\n\$content = file_get_contents(\"data.txt\");\n", 'tests' => [['contains', 'file_get_contents'], ['contains', 'echo']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'php', 'title' => 'Запись в лог', 'prompt' => 'Добавьте "Запись" в log.txt в режиме APPEND.', 'out' => '', 'start' => "<?php\n", 'tests' => [['contains', 'file_put_contents'], ['contains', 'FILE_APPEND']], 'diff' => 'medium', 'time' => 15],
            ],
            'ООП в PHP' => [
                ['lang' => 'php', 'title' => 'Класс User', 'prompt' => 'Создайте класс User с name и методом greet().', 'out' => 'Привет, Анна!', 'start' => "<?php\nclass User {\n    public string \$name;\n\n    public function __construct(string \$name) {\n", 'tests' => [['contains', 'class User'], ['contains', 'public'], ['contains', 'function greet']], 'diff' => 'medium', 'time' => 15],
            ],
        ],
    ],

    4 => [
        'lessons' => [
            'Знакомство с Laravel' => '<h2>Знакомство с Laravel</h2><p>Laravel — PHP-фреймворк с элегантным синтаксисом.</p><h3>Установка</h3><pre><code>composer create-project laravel/laravel my-app\ncd my-app\nphp artisan serve</code></pre><h3>Структура</h3><ul><li>app/ — код приложения</li><li>routes/ — маршруты</li><li>resources/ — Blade представления</li><li>database/ — миграции</li></ul><h3>Маршрут</h3><pre><code>Route::get("/", function () { return view("welcome"); });</code></pre>',
            'Роутинг и контроллеры' => '<h2>Роутинг и контроллеры</h2><pre><code>Route::get("/users", [UserController::class, "index"]);Route::post("/users", [UserController::class, "store"]);Route::get("/users/{id}", [UserController::class, "show"]);Route::prefix("admin")->group(function () { Route::get("/dashboard", [AdminController::class, "dashboard"]); });</code></pre><h3>Контроллер</h3><pre><code>class UserController extends Controller { public function index() { $users = User::all(); return view("users.index", compact("users")); } }</code></pre>',
            'Eloquent ORM' => '<h2>Eloquent ORM</h2><pre><code>class User extends Model { protected $fillable = ["name", "email"]; public function posts() { return $this->hasMany(Post::class); } }</code></pre><h3>Запросы</h3><pre><code>$users = User::where("active", true)->get();$user = User::find(1);User::create(["name" => "Анна"]);$user->update(["name" => "Новое"]);$user->delete();</code></pre>',
            'Миграции и схема БД' => '<h2>Миграции и схема БД</h2><pre><code>php artisan make:migration create_users_tableSchema::create("users", function (Blueprint $table) { $table->id(); $table->string("name"); $table->string("email")->unique(); $table->timestamps(); });php artisan migratemigrate:rollbackmigrate:fresh</code></pre>',
            'Авторизация и middleware' => '<h2>Авторизация и middleware</h2><pre><code>Route::get("/dashboard", ...)->middleware("auth");Route::get("/admin", ...)->middleware("role:admin");class CheckRole { public function handle($request, Closure $next, $role) { if (!auth()->user()->hasRole($role)) { abort(403); } return $next($request); } }</code></pre><h3>Breeze</h3><pre><code>composer require laravel/breeze --dev\nphp artisan breeze:install</code></pre>',
            'REST API в Laravel' => '<h2>REST API в Laravel</h2><pre><code>Route::apiResource("posts", PostController::class);class PostResource extends JsonResource { public function toArray($request) { return ["id" => $this->id, "title" => $this->title]; } }</code></pre>',
            'Тест по Laravel' => '<h2>Тест по Laravel</h2><p>Проверьте свои знания Laravel.</p>',
        ],
        'quizzes' => [
            'Знакомство с Laravel' => [
                ['q' => 'Установка Laravel?', 'o' => ['npm install laravel', 'composer create-project laravel/laravel', 'pip install laravel', 'laravel new'], 'c' => 1, 'e' => 'composer create-project.'],
                ['q' => 'Где маршруты?', 'o' => ['app/routes.php', 'routes/web.php', 'config/routes.php', 'public/routes.php'], 'c' => 1, 'e' => 'routes/web.php.'],
                ['q' => 'Шаблонизатор?', 'o' => ['Twig', 'Blade', 'Smarty', 'Mustache'], 'c' => 1, 'e' => 'Blade.'],
                ['q' => 'Сервер разработки?', 'o' => ['php server', 'php artisan serve', 'npm start', 'laravel run'], 'c' => 1, 'e' => 'php artisan serve.'],
                ['q' => 'Artisan?', 'o' => ['Редактор', 'CLI Laravel', 'Фреймворк', 'База данных'], 'c' => 1, 'e' => 'CLI Laravel.'],
            ],
            'Роутинг и контроллеры' => [
                ['q' => 'GET-маршрут?', 'o' => ['Route::get("/url", [Controller::class, "method"])', 'Route::post("/url", ...)', 'GET("/url")', 'route("get", "/url")'], 'c' => 0, 'e' => 'Route::get().'],
                ['q' => 'Параметр URL?', 'o' => ['request("id")', 'Route::param("id")', 'URL::get("id")', 'Input::get("id")'], 'c' => 0, 'e' => 'Аргумент метода контроллера.'],
                ['q' => 'Защита маршрута?', 'o' => ['->middleware("auth")', '->protected()', '->requireAuth()', '->login()'], 'c' => 0, 'e' => 'middleware("auth").'],
                ['q' => 'Resource route?', 'o' => ['Один маршрут', 'Набор CRUD-маршрутов', 'API маршрут', 'Статический маршрут'], 'c' => 1, 'e' => 'Набор CRUD.'],
                ['q' => 'Группировка маршрутов?', 'o' => ['Route::group()', 'Route::prefix()->group()', 'Route::middleware()->group()', 'Все варианты'], 'c' => 3, 'e' => 'Все комбинации.'],
            ],
            'Eloquent ORM' => [
                ['q' => 'Все записи модели?', 'o' => ['Model.all()', 'Model::all()', 'Model.get()', 'Model.find()'], 'c' => 1, 'e' => 'Model::all().'],
                ['q' => 'Связь один ко многим?', 'o' => ['hasOne()', 'hasMany()', 'belongsTo()', 'belongsToMany()'], 'c' => 1, 'e' => 'hasMany().'],
                ['q' => 'Массовое присваивание?', 'o' => ['protected $fillable', 'protected $guarded', 'protected $hidden', '$safe'], 'c' => 0, 'e' => '$fillable.'],
                ['q' => 'findOrFail()?', 'o' => ['Находит или создаёт', 'Находит или исключение', 'Находит все', 'Находит или удаляет'], 'c' => 1, 'e' => 'Исключение если не найдено.'],
                ['q' => 'Обновить запись?', 'o' => ['Model::update()', '$model->update()', '$model->save()', 'Все B и C'], 'c' => 3, 'e' => 'update() и save().'],
            ],
            'Миграции и схема БД' => [
                ['q' => 'Создать миграцию?', 'o' => ['php artisan make:migration create_table', 'php artisan migration:new', 'php artisan create:migration', 'php artisan db:make'], 'c' => 0, 'e' => 'make:migration.'],
                ['q' => 'Добавить столбец?', 'o' => ['$table->string("name")', 'addColumn("string", "name")', 'column("name", "string")', 'schema->add("name")'], 'c' => 0, 'e' => 'Методы Blueprint.'],
                ['q' => 'php artisan migrate?', 'o' => ['Удаляет базу', 'Выполняет Pending миграции', 'Резервная копия', 'Проверяет базу'], 'c' => 1, 'e' => 'Применяет миграции.'],
                ['q' => 'Откат?', 'o' => ['migrate:reset', 'migrate:rollback', 'migrate:undo', 'migrate:back'], 'c' => 1, 'e' => 'rollback.'],
                ['q' => 'Foreign key?', 'o' => ['Первичный ключ', 'Внешний ключ для связей', 'Индекс', 'Уникальное ограничение'], 'c' => 1, 'e' => 'Внешний ключ.'],
            ],
            'Авторизация и middleware' => [
                ['q' => 'Middleware?', 'o' => ['Контроллер', 'Промежуточный слой', 'Модель', 'Представление'], 'c' => 1, 'e' => 'Промежуточный обработчик.'],
                ['q' => 'Проверка авторизации?', 'o' => ['auth()->check()', 'user()->isAuth()', 'request()->auth()', 'Auth::check()'], 'c' => 3, 'e' => 'Auth::check().'],
                ['q' => 'Middleware маршрута?', 'o' => ['->middleware("auth")', '->requiresAuth()', '->protected()', '->loginRequired()'], 'c' => 0, 'e' => 'middleware().'],
                ['q' => 'abort(403)?', 'o' => ['Страница 403', 'Удаляет пользователя', 'Перенаправляет', 'Останавливает'], 'c' => 0, 'e' => 'HTTP 403.'],
                ['q' => 'Пакеты авторизации?', 'o' => ['Breeze и Jetstream', 'Auth и Login', 'Passport и Guard', 'User и Session'], 'c' => 0, 'e' => 'Breeze и Jetstream.'],
            ],
            'REST API в Laravel' => [
                ['q' => 'API-ресурс?', 'o' => ['Route::apiResource()', 'Route::resource()', 'Api::route()', 'API::resource()'], 'c' => 0, 'e' => 'apiResource().'],
                ['q' => 'API Resource?', 'o' => ['Контроллер', 'Преобразование модели в JSON', 'Маршрут', 'Middleware'], 'c' => 1, 'e' => 'Преобразование в JSON.'],
                ['q' => 'Вернуть JSON?', 'o' => ['response()->json($data)', 'echo json_encode($data)', 'return $data->toJson()', 'Все варианты'], 'c' => 3, 'e' => 'Все работают.'],
                ['q' => 'CORS для API?', 'o' => ['config/cors.php', 'Middleware', 'fruitcake/laravel-cors', 'Все варианты'], 'c' => 3, 'e' => 'Все вместе.'],
                ['q' => 'Rate limiting?', 'o' => ['Ограничение скорости', 'Ограничение количества запросов', 'Кэширование', 'Сжатие'], 'c' => 1, 'e' => 'Ограничение запросов.'],
            ],
        ],
        'practice' => [
            'Знакомство с Laravel' => [
                ['lang' => 'php', 'title' => 'Hello Laravel', 'prompt' => 'Создайте маршрут GET /hello, возвращающий "Hello, Laravel!".', 'out' => 'Hello, Laravel!', 'start' => "<?php\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::get(\"/hello\", function () {\n", 'tests' => [['contains', 'Route::get'], ['contains', 'Hello, Laravel!']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'php', 'title' => 'Параметр маршрута', 'prompt' => 'Создайте GET /user/{name}, возвращающий приветствие.', 'out' => 'Привет, Анна!', 'start' => "<?php\nRoute::get(\"/user/{name}\", function (\$name) {\n", 'tests' => [['contains', '{name}'], ['contains', 'Привет,']], 'diff' => 'easy', 'time' => 10],
            ],
            'Роутинг и контроллеры' => [
                ['lang' => 'php', 'title' => 'Resource контроллер', 'prompt' => 'Создайте PostController с index, store, show.', 'out' => '', 'start' => "<?php\nnamespace App\\Http\\Controllers;\n\nclass PostController extends Controller\n{\n    public function index() {\n", 'tests' => [['contains', 'class PostController'], ['contains', 'function index'], ['contains', 'function store'], ['contains', 'function show']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'php', 'title' => 'Префикс маршрутов', 'prompt' => 'Сгруппируйте маршруты с префиксом "api/v1".', 'out' => '', 'start' => "<?php\nRoute::prefix(\"api/v1\")->group(function () {\n", 'tests' => [['contains', 'prefix'], ['contains', 'group']], 'diff' => 'medium', 'time' => 15],
            ],
            'Eloquent ORM' => [
                ['lang' => 'php', 'title' => 'Модель Post', 'prompt' => 'Создайте Post с $fillable и belongsTo(User::class).', 'out' => '', 'start' => "<?php\nnamespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Model;\n\nclass Post extends Model\n{\n    protected \$fillable = [\"title\", \"content\"];\n\n", 'tests' => [['contains', 'class Post'], ['contains', 'protected $fillable'], ['contains', 'belongsTo']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'php', 'title' => 'Запрос where', 'prompt' => 'Получите опубликованные посты через where()->get().', 'out' => '', 'start' => "<?php\n\$posts = Post::", 'tests' => [['contains', 'where("published"'], ['contains', 'get()']], 'diff' => 'medium', 'time' => 15],
            ],
            'Миграции и схема БД' => [
                ['lang' => 'php', 'title' => 'Создание таблицы', 'prompt' => 'Миграция для articles с id, title, body.', 'out' => '', 'start' => "<?php\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\n\nreturn new class extends Migration\n{\n    public function up()\n    {\n        Schema::create(\"articles\", function (Blueprint \$table) {\n            \$table->id();\n", 'tests' => [['contains', 'Schema::create'], ['contains', 'string("title")'], ['contains', 'text("body")']], 'diff' => 'hard', 'time' => 20],
            ],
            'Авторизация и middleware' => [
                ['lang' => 'php', 'title' => 'Middleware auth', 'prompt' => 'Защитите маршрут middleware "auth".', 'out' => '', 'start' => "<?php\nRoute::get(\"/dashboard\", function () {\n    return view(\"dashboard\");\n})->", 'tests' => [['contains', 'middleware("auth")']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'php', 'title' => 'Проверка роли', 'prompt' => 'Если пользователь не admin — abort(403).', 'out' => '', 'start' => "<?php\nif (!auth()->user()->", 'tests' => [['contains', 'isAdmin'], ['contains', 'abort(403)']], 'diff' => 'medium', 'time' => 15],
            ],
            'REST API в Laravel' => [
                ['lang' => 'php', 'title' => 'API Resource', 'prompt' => 'Создайте PostResource с id, title, author.', 'out' => '', 'start' => "<?php\nnamespace App\\Http\\Resources;\n\nuse Illuminate\\Http\\Resources\\Json\\JsonResource;\n\nclass PostResource extends JsonResource\n{\n    public function toArray(\$request)\n    {\n", 'tests' => [['contains', 'class PostResource'], ['contains', 'toArray'], ['contains', 'id'], ['contains', 'title']], 'diff' => 'medium', 'time' => 15],
            ],
        ],
    ],

    5 => [
        'lessons' => [
            'Основы SQL' => '<h2>Основы SQL</h2><p>SQL — язык запросов для реляционных баз данных.</p><h3>Команды</h3><pre><code>CREATE DATABASE mydb;USE mydb;CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, email VARCHAR(100) UNIQUE NOT NULL);</code></pre><h3>Типы данных</h3><ul><li>INT — целые числа</li><li>VARCHAR(n) — строки</li><li>TEXT — длинные строки</li><li>DECIMAL(p,s) — точные числа</li><li>BOOLEAN — логические</li></ul>',
            'SELECT и WHERE' => '<h2>SELECT и WHERE</h2><pre><code>SELECT * FROM users;SELECT name, email FROM users;SELECT * FROM users WHERE age >= 18;SELECT * FROM users ORDER BY name ASC LIMIT 10;SELECT * FROM users WHERE age BETWEEN 18 AND 30 AND status = "active" AND email LIKE "%@gmail.com" ORDER BY created_at DESC LIMIT 5;</code></pre>',
            'JOIN и связи таблиц' => '<h2>JOIN и связи таблиц</h2><pre><code>SELECT users.name, orders.total FROM users INNER JOIN orders ON users.id = orders.user_id;SELECT users.name, orders.total FROM users LEFT JOIN orders ON users.id = orders.user_id;CREATE TABLE posts (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255), user_id INT, FOREIGN KEY (user_id) REFERENCES users(id));</code></pre>',
            'INSERT UPDATE DELETE' => '<h2>INSERT UPDATE DELETE</h2><pre><code>INSERT INTO users (name, email) VALUES ("Анна", "anna@mail.ru");INSERT INTO users (name, email) VALUES ("Борис", "boris@mail.ru"), ("Вера", "vera@mail.ru");UPDATE users SET name = "Новое имя" WHERE id = 1;DELETE FROM users WHERE id = 1;</code></pre>',
            'Индексы и оптимизация' => '<h2>Индексы и оптимизация</h2><pre><code>CREATE INDEX idx_name ON users(name);CREATE UNIQUE INDEX idx_email ON users(email);CREATE INDEX idx_name_email ON users(name, email);EXPLAIN SELECT * FROM users WHERE name = "Анна";</code></pre>',
            'Хранимые процедуры и функции' => '<h2>Хранимые процедуры и функции</h2><pre><code>DELIMITER //CREATE PROCEDURE GetActiveUsers()BEGIN SELECT * FROM users WHERE status = "active";END //DELIMITER ;CALL GetActiveUsers();CREATE FUNCTION GetUserCount(status VARCHAR(20)) RETURNS INT BEGIN DECLARE count INT; SELECT COUNT(*) INTO count FROM users WHERE status = status; RETURN count; END;</code></pre>',
            'Триггеры и события' => '<h2>Триггеры и события</h2><pre><code>CREATE TRIGGER before_user_insert BEFORE INSERT ON users FOR EACH ROW BEGIN SET NEW.created_at = NOW(); END;CREATE TRIGGER after_order_update AFTER UPDATE ON orders FOR EACH ROW BEGIN INSERT INTO order_logs (order_id, old_status, new_status) VALUES (OLD.id, OLD.status, NEW.status); END;CREATE EVENT cleanup_old_logs ON SCHEDULE EVERY 1 DAY DO DELETE FROM logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);</code></pre>',
            'Тест по MySQL' => '<h2>Тест по MySQL</h2><p>Проверьте свои знания MySQL.</p>',
        ],
        'quizzes' => [
            'Основы SQL' => [
                ['q' => 'Создать базу данных?', 'o' => ['CREATE DATABASE db', 'NEW DATABASE db', 'MAKE DATABASE db', 'INIT DATABASE db'], 'c' => 0, 'e' => 'CREATE DATABASE.'],
                ['q' => 'Тип для строк?', 'o' => ['INT', 'VARCHAR', 'BOOLEAN', 'DATE'], 'c' => 1, 'e' => 'VARCHAR.'],
                ['q' => 'AUTO_INCREMENT?', 'o' => ['Увеличивает размер', 'Автоматически увеличивает значение', 'Создаёт индекс', 'Удаляет запись'], 'c' => 1, 'e' => 'Автоинкремент значения.'],
                ['q' => 'PRIMARY KEY?', 'o' => ['Первый столбец', 'Уникальный идентификатор', 'Индекс', 'Внешний ключ'], 'c' => 1, 'e' => 'Уникальный идентификатор записи.'],
                ['q' => 'Удалить таблицу?', 'o' => ['DELETE TABLE t', 'DROP TABLE t', 'REMOVE TABLE t', 'ERASE TABLE t'], 'c' => 1, 'e' => 'DROP TABLE.'],
            ],
            'SELECT и WHERE' => [
                ['q' => 'Все столбцы?', 'o' => ['SELECT all', 'SELECT *', 'SELECT columns', 'SELECT EVERYTHING'], 'c' => 1, 'e' => 'Звёздочка *.'],
                ['q' => 'ORDER BY?', 'o' => ['Группирует', 'Сортирует', 'Фильтрует', 'Ограничивает'], 'c' => 1, 'e' => 'Сортирует результаты.'],
                ['q' => 'Ограничение количества?', 'o' => ['TOP', 'LIMIT', 'MAX', 'COUNT'], 'c' => 1, 'e' => 'LIMIT.'],
                ['q' => 'LIKE в WHERE?', 'o' => ['Точное сравнение', 'Поиск по шаблону', 'Сортировка', 'Группировка'], 'c' => 1, 'e' => 'Поиск по шаблону.'],
                ['q' => 'BETWEEN?', 'o' => ['Между таблицами', 'Диапазон значений', 'Соединение', 'Разделение'], 'c' => 1, 'e' => 'Диапазон значений.'],
            ],
            'JOIN и связи таблиц' => [
                ['q' => 'Какой JOIN совпадающие?', 'o' => ['LEFT', 'RIGHT', 'INNER', 'FULL'], 'c' => 2, 'e' => 'INNER JOIN.'],
                ['q' => 'FOREIGN KEY?', 'o' => ['Уникальный ключ', 'Ссылка на ключ другой таблицы', 'Индекс', 'Столбец данных'], 'c' => 1, 'e' => 'Внешний ключ для связей.'],
                ['q' => 'LEFT JOIN?', 'o' => ['Совпадающие', 'Все из левой', 'Все из правой', 'Все из обеих'], 'c' => 1, 'e' => 'Все из левой таблицы.'],
                ['q' => 'Один ко многим?', 'o' => ['Одна на одну', 'Одна имеет много', 'Много на одну', 'Много на много'], 'c' => 1, 'e' => 'Одна запись — много связанных.'],
                ['q' => 'Три таблицы JOIN?', 'o' => ['JOIN t1 JOIN t2 JOIN t3', 'JOIN t1, t2, t3', 'TWO JOINS', 'MULTI JOIN'], 'c' => 0, 'e' => 'Три оператора JOIN.'],
            ],
            'INSERT UPDATE DELETE' => [
                ['q' => 'Добавить запись?', 'o' => ['INSERT INTO table VALUES (...)', 'ADD TO table VALUES (...)', 'NEW row IN table', 'CREATE row IN table'], 'c' => 0, 'e' => 'INSERT INTO ... VALUES.'],
                ['q' => 'WHERE в UPDATE?', 'o' => ['Указывает таблицу', 'Определяет какие записи обновить', 'Задаёт значения', 'Сортирует'], 'c' => 1, 'e' => 'Определяет записи для обновления.'],
                ['q' => 'Удалить всё, сохранив таблицу?', 'o' => ['DELETE FROM table', 'TRUNCATE TABLE table', 'DROP TABLE table', 'CLEAR TABLE table'], 'c' => 1, 'e' => 'TRUNCATE.'],
                ['q' => 'DELETE без WHERE?', 'o' => ['Нормально', 'Удалит все записи', 'Только для тестов', 'Только admin'], 'c' => 1, 'e' => 'Удалит ВСЕ записи.'],
                ['q' => 'Вставить несколько записей?', 'o' => ['Несколько INSERT', 'INSERT ... VALUES (...), (...)', 'INSERT ALL', 'BULK INSERT'], 'c' => 1, 'e' => 'VALUES с несколькими наборами.'],
            ],
            'Индексы и оптимизация' => [
                ['q' => 'Уникальный индекс?', 'o' => ['Обычный', 'Уникальный', 'Составной', 'Полный текстовый'], 'c' => 1, 'e' => 'Уникальный для уникальных значений.'],
                ['q' => 'EXPLAIN?', 'o' => ['Ошибки', 'План выполнения запроса', 'Структуру таблицы', 'Статистику БД'], 'c' => 1, 'e' => 'План выполнения.'],
                ['q' => 'LIKE "%word%" не помогает?', 'o' => ['При WHERE', 'При LIKE на начало', 'При JOIN', 'При ORDER BY'], 'c' => 1, 'e' => 'Нет индекса при полном переборе.'],
                ['q' => 'Составной индекс?', 'o' => ['Один столбец', 'Несколько столбцов', 'Всю таблицу', 'Виртуальную таблицу'], 'c' => 1, 'e' => 'На два и более столбца.'],
                ['q' => 'Оптимизация запроса?', 'o' => ['Увеличить память', 'Добавить индексы', 'SELECT *', 'Уменьшить страницу'], 'c' => 1, 'e' => 'Добавить правильные индексы.'],
            ],
            'Хранимые процедуры и функции' => [
                ['q' => 'Хранимая процедура?', 'o' => ['Файл запросов', 'Набор SQL-команд в БД', 'Таблица данных', 'Индекс'], 'c' => 1, 'e' => 'Набор SQL-команд в БД.'],
                ['q' => 'Вызов процедуры?', 'o' => ['EXEC proc()', 'CALL proc()', 'RUN proc()', 'proc()'], 'c' => 1, 'e' => 'CALL.'],
                ['q' => 'Процедура vs функция?', 'o' => ['Ничем', 'Функция возвращает значение', 'Процедура быстрее', 'Функция без параметров'], 'c' => 1, 'e' => 'Функция возвращает значение.'],
                ['q' => 'DELIMITER?', 'o' => ['Разделяет запросы', 'Изменяет символ завершения', 'Удаляет разделители', 'Создаёт разделитель'], 'c' => 1, 'e' => 'Меняет символ завершения.'],
                ['q' => 'Где хранятся процедуры?', 'o' => ['В файлах', 'В базе данных', 'В коде приложения', 'В памяти'], 'c' => 1, 'e' => 'В базе данных.'],
            ],
            'Триггеры и события' => [
                ['q' => 'BEFORE INSERT?', 'o' => ['После вставки', 'Перед вставкой', 'При обновлении', 'При удалении'], 'c' => 1, 'e' => 'Перед добавлением записи.'],
                ['q' => 'Event в MySQL?', 'o' => ['Действие', 'Запланированная задача', 'Триггер', 'Процедура'], 'c' => 1, 'e' => 'Запланированная задача.'],
                ['q' => 'OLD/NEW в триггере?', 'o' => ['Старое и новое значение', 'Все записи', 'Индекс', 'Связь'], 'c' => 0, 'e' => 'OLD — до изменения, NEW — после.'],
                ['q' => 'AFTER UPDATE триггер?', 'o' => ['Перед обновлением', 'После обновления', 'При удалении', 'При вставке'], 'c' => 1, 'e' => 'После обновления записи.'],
                ['q' => 'Событие ON SCHEDULE?', 'o' => ['Запуск при старте', 'Выполнение по расписанию', 'Выполнение при ошибке', 'Выполнение вручную'], 'c' => 1, 'e' => 'Выполнение по расписанию.'],
            ],
        ],
        'practice' => [
            'Основы SQL' => [
                ['lang' => 'sql', 'title' => 'Создание таблицы', 'prompt' => 'Создайте таблицу products с id, name, price.', 'out' => '', 'start' => "CREATE TABLE products (\n    id INT AUTO_INCREMENT PRIMARY KEY,\n    name VARCHAR(100) NOT NULL,\n    price DECIMAL(10, 2)\n);", 'tests' => [['contains', 'CREATE TABLE'], ['contains', 'id INT'], ['contains', 'name VARCHAR'], ['contains', 'price DECIMAL']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'sql', 'title' => 'Вставка данных', 'prompt' => 'Вставьте 3 товара: "Ноутбук" 50000, "Мышь" 1500, "Клавиатура" 3000.', 'out' => '', 'start' => "INSERT INTO products (name, price) VALUES", 'tests' => [['contains', 'INSERT INTO'], ['contains', 'Ноутбук'], ['contains', 'Мышь'], ['contains', 'Клавиатура']], 'diff' => 'easy', 'time' => 10],
            ],
            'SELECT и WHERE' => [
                ['lang' => 'sql', 'title' => 'Выборка с фильтром', 'prompt' => 'Выберите товары дешевле 5000, отсортированные по цене.', 'out' => '', 'start' => "SELECT * FROM products\n", 'tests' => [['contains', 'SELECT'], ['contains', 'WHERE'], ['contains', '< 5000'], ['contains', 'ORDER BY']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'sql', 'title' => 'Поиск по шаблону', 'prompt' => 'Найдите товары, начинающиеся на "Н".', 'out' => '', 'start' => "SELECT * FROM products\n", 'tests' => [['contains', 'LIKE'], ['contains', 'Н%']], 'diff' => 'medium', 'time' => 15],
            ],
            'JOIN и связи таблиц' => [
                ['lang' => 'sql', 'title' => 'INNER JOIN', 'prompt' => 'Соедините users и orders, показав имена и суммы.', 'out' => '', 'start' => "SELECT users.name, orders.total\nFROM users\n", 'tests' => [['contains', 'INNER JOIN'], ['contains', 'ON'], ['contains', 'users.id = orders.user_id']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'sql', 'title' => 'LEFT JOIN', 'prompt' => 'Покажите всех пользователей и их заказы (включая тех, у кого нет заказов).', 'out' => '', 'start' => "SELECT users.name, orders.total\nFROM users\n", 'tests' => [['contains', 'LEFT JOIN'], ['contains', 'ON']], 'diff' => 'medium', 'time' => 15],
            ],
            'INSERT UPDATE DELETE' => [
                ['lang' => 'sql', 'title' => 'Обновление данных', 'prompt' => 'Измените цену "Ноутбук" на 55000.', 'out' => '', 'start' => "UPDATE products\n", 'tests' => [['contains', 'UPDATE'], ['contains', 'SET'], ['contains', 'WHERE'], ['contains', '55000']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'sql', 'title' => 'Удаление данных', 'prompt' => 'Удалите товар "Мышь".', 'out' => '', 'start' => "DELETE FROM products\n", 'tests' => [['contains', 'DELETE FROM'], ['contains', 'WHERE'], ['contains', 'Мышь']], 'diff' => 'easy', 'time' => 10],
            ],
            'Индексы и оптимизация' => [
                ['lang' => 'sql', 'title' => 'Создание индекса', 'prompt' => 'Создайте индекс по столбцу name.', 'out' => '', 'start' => "CREATE INDEX idx_name ON products", 'tests' => [['contains', 'CREATE INDEX'], ['contains', 'idx_name'], ['contains', 'name']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'sql', 'title' => 'EXPLAIN запрос', 'prompt' => 'Покажите план выполнения для SELECT по name.', 'out' => '', 'start' => "EXPLAIN ", 'tests' => [['contains', 'EXPLAIN'], ['contains', 'SELECT'], ['contains', 'WHERE']], 'diff' => 'medium', 'time' => 15],
            ],
            'Хранимые процедуры и функции' => [
                ['lang' => 'sql', 'title' => 'Процедура', 'prompt' => 'Создайте процедуру GetCheapProducts, возвращающую товары дешевле 5000.', 'out' => '', 'start' => "DELIMITER //\nCREATE PROCEDURE GetCheapProducts()\nBEGIN\n    ", 'tests' => [['contains', 'CREATE PROCEDURE'], ['contains', 'BEGIN'], ['contains', 'SELECT'], ['contains', '< 5000'], ['contains', 'END']], 'diff' => 'hard', 'time' => 20],
                ['lang' => 'sql', 'title' => 'Функция', 'prompt' => 'Создайте функцию GetProductPrice(id) для получения цены товара.', 'out' => '', 'start' => "CREATE FUNCTION GetProductPrice(p_id INT)\nRETURNS DECIMAL(10,2)\nBEGIN\n    ", 'tests' => [['contains', 'CREATE FUNCTION'], ['contains', 'RETURNS'], ['contains', 'BEGIN'], ['contains', 'RETURN']], 'diff' => 'hard', 'time' => 20],
            ],
            'Триггеры и события' => [
                ['lang' => 'sql', 'title' => 'Триггер before insert', 'prompt' => 'Создайте триггер, автоматически устанавливающий created_at перед вставкой.', 'out' => '', 'start' => "CREATE TRIGGER before_product_insert\nBEFORE INSERT ON products\nFOR EACH ROW\nBEGIN\n    ", 'tests' => [['contains', 'CREATE TRIGGER'], ['contains', 'BEFORE INSERT'], ['contains', 'SET NEW']], 'diff' => 'hard', 'time' => 20],
                ['lang' => 'sql', 'title' => 'Событие по расписанию', 'prompt' => 'Создайте событие, очищающее старые логи каждые 7 дней.', 'out' => '', 'start' => "CREATE EVENT cleanup_logs\nON SCHEDULE EVERY 7 DAY\nDO\n    ", 'tests' => [['contains', 'CREATE EVENT'], ['contains', 'ON SCHEDULE'], ['contains', 'DELETE FROM']], 'diff' => 'hard', 'time' => 20],
            ],
        ],
    ],
];
