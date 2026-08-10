<?php

return [
    1 => [ // HTML+CSS
        'lessons' => [
            ['title' => 'Структура HTML-документа', 'type' => 'video', 'module' => 'HTML Основы', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Изучаем основы HTML: DOCTYPE, head, body, meta-теги, иерархия элементов.',
             'content' => '<h2>Структура HTML-документа</h2>
<p>Каждый HTML-документ начинается с объявления типа документа:</p>
<pre><code>&lt;!DOCTYPE html&gt;
&lt;html lang="ru"&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
    &lt;title&gt;Заголовок страницы&lt;/title&gt;
    &lt;link rel="stylesheet" href="style.css"&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;Привет, мир!&lt;/h1&gt;
    &lt;p&gt;Это мой первый HTML-документ.&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
<h3>Ключевые элементы</h3>
<ul>
<li><code>&lt;!DOCTYPE html&gt;</code> — объявление типа документа (HTML5)</li>
<li><code>&lt;html&gt;</code> — корневой элемент, <code>lang</code> задаёт язык</li>
<li><code>&lt;head&gt;</code> — метаданные: кодировка, заголовок, подключение CSS/JS</li>
<li><code>&lt;body&gt;</code> — видимое содержимое страницы</li>
</ul>
<h3>Мета-теги</h3>
<pre><code>&lt;meta charset="UTF-8"&gt;          -- кодировка
&lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;  -- адаптивность
&lt;meta name="description" content="Описание страницы"&gt;  -- для SEO
&lt;meta name="keywords" content="HTML, CSS, веб"&gt;</code></pre>
<h3>Стандартная структура</h3>
<p>Правильный порядок тегов в head:</p>
<ol>
<li>charset (первым!)</li>
<li>viewport</li>
<li>title</li>
<li>description, keywords</li>
<li>подключение стилей (link rel="stylesheet")</li>
<li>подключение скриптов (script)</li>
</ol>'],
            ['title' => 'Семантические теги HTML5', 'type' => 'article', 'module' => 'HTML Основы', 'difficulty' => 'easy', 'duration_minutes' => 30,
             'description' => 'Семантические теги: header, nav, main, article, section, aside, footer.',
             'content' => '<h2>Семантические теги HTML5</h2>
<p>Семантические теги придают структуре документа смысл:</p>
<pre><code>&lt;header&gt;  -- шапка страницы или секции
&lt;nav&gt;     -- навигационное меню
&lt;main&gt;    -- основной контент (только один на страницу)
&lt;article&gt; -- самостоятельная статья
&lt;section&gt; -- тематическая секция
&lt;aside&gt;   -- побочный контент (сайдбар)
&lt;footer&gt;  -- подвал страницы или секции
&lt;figure&gt;  -- иллюстрация с подписью
&lt;figcaption&gt; -- подпись к figure
&lt;details&gt; -- раскрывающийся блок
&lt;summary&gt; -- заголовок details</code></pre>
<h3>Пример семантической страницы</h3>
<pre><code>&lt;body&gt;
  &lt;header&gt;
    &lt;h1&gt;Мой блог&lt;/h1&gt;
    &lt;nav&gt;
      &lt;a href="/"&gt;Главная&lt;/a&gt;
      &lt;a href="/posts"&gt;Статьи&lt;/a&gt;
      &lt;a href="/about"&gt;О себе&lt;/a&gt;
    &lt;/nav&gt;
  &lt;/header&gt;

  &lt;main&gt;
    &lt;article&gt;
      &lt;h2&gt;Заголовок статьи&lt;/h2&gt;
      &lt;section&gt;
        &lt;h3&gt;Введение&lt;/h3&gt;
        &lt;p&gt;Текст введения...&lt;/p&gt;
      &lt;/section&gt;
      &lt;section&gt;
        &lt;h3&gt;Основная часть&lt;/h3&gt;
        &lt;p&gt;Подробнее...&lt;/p&gt;
      &lt;/section&gt;
    &lt;/article&gt;

    &lt;aside&gt;
      &lt;h3&gt;Похожие статьи&lt;/h3&gt;
      &lt;ul&gt;&lt;li&gt;...&lt;/li&gt;&lt;/ul&gt;
    &lt;/aside&gt;
  &lt;/main&gt;

  &lt;footer&gt;
    &lt;p&gt;&amp;copy; 2025 Мой блог&lt;/p&gt;
  &lt;/footer&gt;
&lt;/body&gt;</code></pre>
<h3>Почему семантика важна?</h3>
<ul>
<li><strong>SEO:</strong> поисковики лучше индексируют семантический HTML</li>
<li><strong>Доступность:</strong> скринридеры понимают структуру</li>
<li><strong>Поддержка:</strong> код легче читать и поддерживать</li>
</ul>'],
            ['title' => 'Таблицы, списки и мультимедиа', 'type' => 'article', 'module' => 'HTML Основы', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Создание таблиц, нумерованных и ненумерованных списков, встраивание медиа.',
             'content' => '<h2>Таблицы</h2>
<pre><code>&lt;table&gt;
  &lt;caption&gt;Продукты&lt;/caption&gt;
  &lt;thead&gt;
    &lt;tr&gt;&lt;th&gt;Название&lt;/th&gt;&lt;th&gt;Цена&lt;/th&gt;&lt;th&gt;Кол-во&lt;/th&gt;&lt;/tr&gt;
  &lt;/thead&gt;
  &lt;tbody&gt;
    &lt;tr&gt;&lt;td&gt;Яблоки&lt;/td&gt;&lt;td&gt;120₽&lt;/td&gt;&lt;td&gt;5 кг&lt;/td&gt;&lt;/tr&gt;
    &lt;tr&gt;&lt;td&gt;Молоко&lt;/td&gt;&lt;td&gt;80₽&lt;/td&gt;&lt;td&gt;1 л&lt;/td&gt;&lt;/tr&gt;
  &lt;/tbody&gt;
  &lt;tfoot&gt;
    &lt;tr&gt;&lt;th colspan="2"&gt;Итого&lt;/th&gt;&lt;td&gt;...&lt;/td&gt;&lt;/tr&gt;
  &lt;/tfoot&gt;
&lt;/table&gt;</code></pre>
<p>Атрибуты: <code>colspan</code>, <code>rowspan</code>, <code>scope</code>.</p>
<h2>Списки</h2>
<pre><code>&lt;ul&gt;  -- ненумерованный
  &lt;li&gt;Элемент 1&lt;/li&gt;
  &lt;li&gt;Элемент 2&lt;/li&gt;
&lt;/ul&gt;

&lt;ol&gt;  -- нумерованный
  &lt;li&gt;Шаг 1&lt;/li&gt;
  &lt;li&gt;Шаг 2&lt;/li&gt;
&lt;/ol&gt;

&lt;dl&gt;  -- список определений
  &lt;dt&gt;HTML&lt;/dt&gt;
  &lt;dd&gt;Язык разметки&lt;/dd&gt;
&lt;/dl&gt;</code></pre>
<h2>Мультимедиа</h2>
<pre><code>&lt;img src="photo.jpg" alt="Описание" width="300" loading="lazy"&gt;

&lt;video controls width="640"&gt;
  &lt;source src="video.mp4" type="video/mp4"&gt;
  Ваш браузер не поддерживает видео.
&lt;/video&gt;

&lt;audio controls&gt;
  &lt;source src="audio.mp3" type="audio/mpeg"&gt;
&lt;/audio&gt;

&lt;iframe src="https://youtube.com/embed/..." width="560" height="315"
  allowfullscreen&gt;&lt;/iframe&gt;</code></pre>'],
            ['title' => 'Формы и валидация HTML5', 'type' => 'video', 'module' => 'HTML Формы', 'difficulty' => 'medium', 'duration_minutes' => 35,
             'description' => 'Создание форм: input типы, валидация, атрибуты, связывание с label.',
             'content' => '<h2>Формы HTML</h2>
<pre><code>&lt;form action="/register" method="POST" enctype="multipart/form-data"&gt;
  &lt;!-- Текст --&gt;
  &lt;label for="name"&gt;Имя:&lt;/label&gt;
  &lt;input type="text" id="name" name="name" required minlength="2"
         maxlength="50" placeholder="Ваше имя"&gt;

  &lt;!-- Email --&gt;
  &lt;label for="email"&gt;Email:&lt;/label&gt;
  &lt;input type="email" id="email" name="email" required&gt;

  &lt;!-- Пароль --&gt;
  &lt;label for="pass"&gt;Пароль:&lt;/label&gt;
  &lt;input type="password" id="pass" name="password"
         required minlength="8" pattern="(?=.*\\d).{8,}"&gt;

  &lt;!-- Число --&gt;
  &lt;input type="number" name="age" min="0" max="150" step="1"&gt;

  &lt;!-- Дата --&gt;
  &lt;input type="date" name="birthday"&gt;

  &lt;!-- Файл --&gt;
  &lt;input type="file" name="avatar" accept="image/*"&gt;

  &lt;!-- Цвет --&gt;
  &lt;input type="color" name="fav_color" value="#ff0000"&gt;

  &lt;!-- Выпадающий список --&gt;
  &lt;select name="country"&gt;
    &lt;option value=""&gt;Выберите&lt;/option&gt;
    &lt;option value="ru"&gt;Россия&lt;/option&gt;
    &lt;option value="kz"&gt;Казахстан&lt;/option&gt;
  &lt;/select&gt;

  &lt;!-- Текстовая область --&gt;
  &lt;textarea name="bio" rows="4" cols="50"&gt;&lt;/textarea&gt;

  &lt;!-- Радиокнопки --&gt;
  &lt;input type="radio" id="m" name="gender" value="male"&gt;
  &lt;label for="m"&gt;Мужской&lt;/label&gt;
  &lt;input type="radio" id="f" name="gender" value="female"&gt;
  &lt;label for="f"&gt;Женский&lt;/label&gt;

  &lt;!-- Чекбокс --&gt;
  &lt;input type="checkbox" id="agree" name="agree" required&gt;
  &lt;label for="agree"&gt;Согласен с условиями&lt;/label&gt;

  &lt;button type="submit"&gt;Отправить&lt;/button&gt;
  &lt;button type="reset"&gt;Сбросить&lt;/button&gt;
&lt;/form&gt;</code></pre>
<h3>Типы input</h3>
<table><tr><th>Тип</th><th>Назначение</th></tr>
<tr><td>text</td><td>Текст</td></tr>
<tr><td>email</td><td>Почта (авто-валидация @)</td></tr>
<tr><td>password</td><td>Пароль (скрытый ввод)</td></tr>
<tr><td>number</td><td>Число (spinbox)</td></tr>
<tr><td>tel</td><td>Телефон</td></tr>
<tr><td>url</td><td>URL (авто-валидация)</td></tr>
<tr><td>date</td><td>Дата (календарь)</td></tr>
<tr><td>time</td><td>Время</td></tr>
<tr><td>range</td><td>Ползунок</td></tr>
<tr><td>file</td><td>Загрузка файла</td></tr>
<tr><td>hidden</td><td>Скрытое поле</td></tr>
<tr><td>color</td><td>Выбор цвета</td></tr>
</table>'],
            ['title' => 'CSS-селекторы и каскадность', 'type' => 'article', 'module' => 'CSS Основы', 'difficulty' => 'easy', 'duration_minutes' => 30,
             'description' => 'Типы селекторов, специфичность, каскадность, наследование.',
             'content' => '<h2>CSS-селекторы</h2>
<h3>Базовые селекторы</h3>
<pre><code>/* Элемент */
p { color: blue; }

/* Класс */
.highlight { background: yellow; }

/* ID */
#header { height: 80px; }

/* Универсальный */
* { box-sizing: border-box; }

/* Группировка */
h1, h2, h3 { font-family: Arial; }</code></pre>
<h3>Комбинированные селекторы</h3>
<pre><code>/* Потомок (любой уровень) */
div p { color: red; }

/* Прямой потомок */
div > p { color: blue; }

/* Сосед */
h2 + p { margin-top: 0; }

/* Брат */
h2 ~ p { color: gray; }</code></pre>
<h3>Псевдоклассы</h3>
<pre><code>a:hover { color: red; }
a:active { color: blue; }
a:visited { color: purple; }
input:focus { border-color: blue; }
li:first-child { font-weight: bold; }
li:last-child { border-bottom: none; }
li:nth-child(2n) { background: #f5f5f5; }
input:required { border-left: 3px solid red; }
input:valid { border-color: green; }
input:invalid { border-color: red; }</code></pre>
<h3>Псевдоэлементы</h3>
<pre><code>.icon::before { content: "★ "; }
.quote::after { content: "»"; }
p::first-line { font-weight: bold; }
p::first-letter { font-size: 2em; }</code></pre>
<h3>Специфичность</h3>
<p>Порядок приоритетов (от низкого к высокому):</p>
<ol>
<li>Селектор элемента (p) — специфичность 0,0,1</li>
<li>Класс (.class) — 0,1,0</li>
<li>ID (#id) — 1,0,0</li>
<li>inline-стиль — 1,0,0,0</li>
<li>!important — выше всех</li>
</ol>'],
            ['title' => 'Box Model и позиционирование', 'type' => 'video', 'module' => 'CSS Основы', 'difficulty' => 'medium', 'duration_minutes' => 35,
             'description' => 'margin, padding, border, box-sizing, позиционирование: static, relative, absolute, fixed, sticky.',
             'content' => '<h2>CSS Box Model</h2>
<p>Каждый элемент — прямоугольная коробка:</p>
<pre><code>.box {
  width: 300px;
  padding: 20px;      /* внутренний отступ */
  border: 2px solid black;
  margin: 10px;       /* внешний отступ */
}
/* Итоговая ширина: 300 + 20*2 + 2*2 + 10*2 = 364px */

/* border-box: padding и border ВКЛЮЧЕНЫ в width */
.box {
  box-sizing: border-box;
  width: 300px;       /* итого 300px */
}</code></pre>
<h2>Позиционирование</h2>
<pre><code>/* static — по умолчанию */
.static { position: static; }

/* relative — смещение от своего места */
.relative { position: relative; top: 10px; left: 20px; }

/* absolute — от ближайшего позиционированного предка */
.parent { position: relative; }
.child { position: absolute; top: 0; right: 0; }

/* fixed — фиксируется в окне браузера */
.navbar { position: fixed; top: 0; width: 100%; z-index: 1000; }

/* sticky — прилипает при прокрутке */
.sidebar { position: sticky; top: 0; }</code></pre>
<h3>z-index</h3>
<pre><code>.overlay { position: fixed; z-index: 1000; }
.modal { z-index: 2000; } /* выше overlay */</code></pre>'],
            ['title' => 'Flexbox: компоновка элементов', 'type' => 'video', 'module' => 'CSS Компоновка', 'difficulty' => 'medium', 'duration_minutes' => 40,
             'description' => 'Flex-контейнер, оси, выравнивание, перенос, порядок элементов.',
             'content' => '<h2>Flexbox</h2>
<pre><code>.container {
  display: flex;

  /* Основная ось (по умолчанию — row) */
  flex-direction: row | row-reverse | column | column-reverse;

  /* Обёртка */
  flex-wrap: nowrap | wrap | wrap-reverse;

  /* Выравнивание по основной оси */
  justify-content: flex-start | center | space-between | space-around | space-evenly;

  /* Выравнивание по поперечной оси */
  align-items: stretch | flex-start | center | flex-end | baseline;

  /* Распределение строк */
  align-content: flex-start | center | space-between;

  /* Расстояние между элементами */
  gap: 16px;
}

.item {
  /* Порядок */
  order: 0;

  /* Растяжение */
  flex-grow: 0;    /* по умолчанию не растягивается */
  flex-shrink: 1;  /* по умолчанию сжимается */
  flex-basis: auto;

  /* Краткая запись */
  flex: 0 1 auto;  /* grow shrink basis */

  /* Выравнивание одного элемента */
  align-self: flex-start | center | flex-end | stretch;

  /* Фиксированная ширина */
  width: 200px;
  min-width: 100px;
  max-width: 400px;
}</code></pre>
<h3>Практические примеры</h3>
<pre><code>/* Центрирование */
.center { display: flex; justify-content: center; align-items: center; }

/* Навигация */
.nav { display: flex; justify-content: space-between; align-items: center; }

/* Карточки */
.cards { display: flex; flex-wrap: wrap; gap: 16px; }
.card { flex: 1 1 300px; }</code></pre>'],
            ['title' => 'CSS Grid: двумерная сетка', 'type' => 'video', 'module' => 'CSS Компоновка', 'difficulty' => 'medium', 'duration_minutes' => 40,
             'description' => 'grid-template-columns/rows, areas, gap, авто-размещение, повторение.',
             'content' => '<h2>CSS Grid</h2>
<pre><code>.grid {
  display: grid;

  /* Определение колонок */
  grid-template-columns: 200px 1fr 200px;  /* сайдбар-контент-сайдбар */
  grid-template-columns: repeat(3, 1fr);   /* 3 равных колонки */
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));

  /* Определение строк */
  grid-template-rows: 80px 1fr 60px;

  /* Именованные области */
  grid-template-areas:
    "header  header  header"
    "sidebar content aside"
    "footer  footer  footer";

  gap: 16px;
}

.header  { grid-area: header; }
.sidebar { grid-area: sidebar; }
.content { grid-area: content; }
.aside   { grid-area: aside; }
.footer  { grid-area: footer; }</code></pre>
<h3>Размещение элементов</h3>
<pre><code>.item {
  grid-column: 1 / 3;          /* от колонки 1 до 3 */
  grid-row: 1 / 2;
  grid-area: span 2;           /* занимает 2 колонки */

  justify-self: center;        /* по горизонтали */
  align-self: center;          /* по вертикали */
}</code></pre>
<h3>Адаптивная сетка</h3>
<pre><code>.responsive {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}
/* Элементы автоматически перестраиваются */</code></pre>'],
            ['title' => 'Адаптивный дизайн и медиа-запросы', 'type' => 'article', 'module' => 'CSS Продвинутый', 'difficulty' => 'hard', 'duration_minutes' => 35,
             'description' => 'Медиа-запросы, мобильная-first стратегия, относительные единицы, переменные CSS.',
             'content' => '<h2>Медиа-запросы</h2>
<pre><code>/* Мобильная-first: стили для мобильных ПЕРВЫМИ */
.container { padding: 16px; }

/* Планшеты */
@media (min-width: 768px) {
  .container { max-width: 720px; margin: 0 auto; }
}

/* Десктопы */
@media (min-width: 1024px) {
  .container { max-width: 960px; }
  .sidebar { display: block; }
}

/* Тёмная тема */
@media (prefers-color-scheme: dark) {
  body { background: #1a1a1a; color: #fff; }
}

/* Печать */
@media print {
  .nav, .footer { display: none; }
}</code></pre>
<h2>CSS-переменные</h2>
<pre><code>:root {
  --primary: #3b82f6;
  --text: #1f2937;
  --bg: #ffffff;
  --radius: 8px;
  --shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.btn {
  background: var(--primary);
  color: white;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
}

/* Переопределение для тёмной темы */
[data-theme="dark"] {
  --bg: #111827;
  --text: #f9fafb;
}</code></pre>
<h2>Относительные единицы</h2>
<pre><code>/* em — от шрифта родителя */
h1 { font-size: 2.5em; }  /* 2.5 × родительский */

/* rem — от корня */
html { font-size: 16px; }
h1 { font-size: 2rem; }   /* 32px */

/* vw/vh — от viewport */
.hero { height: 100vh; }
.full-width { width: 100vw; }

/* clamp() — адаптивный размер */
h1 { font-size: clamp(1.5rem, 4vw, 3rem); }</code></pre>'],
            ['title' => 'CSS-анимации и трансформации', 'type' => 'article', 'module' => 'CSS Продвинутый', 'difficulty' => 'hard', 'duration_minutes' => 30,
             'description' => 'transition, animation, keyframes, transform: rotate/scale/translate/skew.',
             'content' => '<h2>Transition — плавные переходы</h2>
<pre><code>.btn {
  background: #3b82f6;
  transition: background 0.3s ease, transform 0.2s ease;
}
.btn:hover {
  background: #2563eb;
  transform: translateY(-2px);
}
.btn:active {
  transform: translateY(0);
}</code></pre>
<h2>Transform — трансформации</h2>
<pre><code>.element {
  transform: rotate(45deg);           /* поворот */
  transform: scale(1.2);              /* масштаб */
  transform: translateX(100px);       /* смещение */
  transform: skewX(10deg);            /* наклон */
  transform: rotateX(15deg) rotateY(15deg);  /* 3D-поворот */
  transform-origin: center center;    /* точка трансформации */
}</code></pre>
<h2>Animation — ключевые кадры</h2>
<pre><code>@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animated {
  animation: fadeIn 0.5s ease-out;
  animation: pulse 2s infinite;
  animation: spin 1s linear infinite;
}

/* Задержка и итерации */
.delayed {
  animation-delay: 0.5s;
  animation-iteration-count: 3;
  animation-fill-mode: forwards; /* сохраняет конечное состояние */
}</code></pre>
<h3>Hover-эффекты</h3>
<pre><code>.card {
  transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}</code></pre>'],
            ['title' => 'Тест по HTML+CSS', 'type' => 'quiz', 'module' => 'Тестирование', 'difficulty' => 'hard', 'duration_minutes' => 30,
             'description' => 'Итоговый тест по всем темам HTML и CSS.',
             'content' => '<h2>Тест по HTML+CSS</h2><p>Проверьте свои знания по HTML и CSS.</p>'],
        ],
    ],

    2 => [ // JavaScript
        'lessons' => [
            ['title' => 'Переменные, типы данных и операторы', 'type' => 'video', 'module' => 'Основы JavaScript', 'difficulty' => 'easy', 'duration_minutes' => 30,
             'description' => 'var, let, const, примитивы, объекты, массивы, операторы.',
             'content' => '<h2>Переменные</h2>
<pre><code>let name = "Али";         // можно менять
const PI = 3.14159;       // константа
var old = "устаревший";   // не использовать

// Типы данных
let str = "Hello";        // string
let num = 42;             // number
let big = 9007199254740991n; // bigint
let flag = true;          // boolean
let nothing = null;       // null
let undef;                // undefined
let id = Symbol("id");    // symbol</code></pre>
<h2>Операторы</h2>
<pre><code>// Сравнение
5 == "5"     // true (с приведением типов)
5 === "5"    // false (строгое сравнение)

// Логические
true && false   // AND
true || false   // OR
!true           // NOT

// Nullish coalescing
let val = null ?? "default";  // "default"

// Optional chaining
let city = user?.address?.city;  // undefined если нет</code></pre>'],
            ['title' => 'Строки, массивы и объекты', 'type' => 'article', 'module' => 'Основы JavaScript', 'difficulty' => 'easy', 'duration_minutes' => 30,
             'description' => 'Методы строк, работа с массивами, деструктуризация объектов.',
             'content' => '<h2>Строки</h2>
<pre><code>let s = "Hello, World!";
s.length              // 13
s.toUpperCase()       // "HELLO, WORLD!"
s.includes("World")   // true
s.slice(0, 5)         // "Hello"
s.split(", ")         // ["Hello", "World!"]
"Hi".repeat(3)        // "HiHiHi"
`Привет, ${name}!`    // шаблонные строки</code></pre>
<h2>Массивы</h2>
<pre><code>let arr = [1, 2, 3, 4, 5];
arr.push(6);          // добавить в конец
arr.pop();            // удалить с конца
arr.unshift(0);       // добавить в начало
arr.shift();          // удалить из начала
arr.includes(3);      // true
arr.indexOf(3);       // 2
arr.slice(1, 3);      // [2, 3]
arr.splice(1, 1);     // удалить 1 элемент с позиции 1

// Методы перебора
arr.forEach((item, i) => console.log(i, item));
let doubled = arr.map(x => x * 2);
let evens = arr.filter(x => x % 2 === 0);
let sum = arr.reduce((acc, x) => acc + x, 0);
let found = arr.find(x => x > 3);
let exists = arr.some(x => x > 4);
let all = arr.every(x => x > 0);</code></pre>
<h2>Объекты</h2>
<pre><code>let user = { name: "Али", age: 25, city: "Душанбе" };
let { name, age } = user;  // деструктуризация

// Spread
let copy = { ...user, age: 26 };
let merged = { ...obj1, ...obj2 };

// Object.keys / values / entries
Object.keys(user);    // ["name", "age", "city"]
Object.values(user);  // ["Али", 25, "Душанбе"]
Object.entries(user); // [["name","Али"], ...]</code></pre>'],
            ['title' => 'Функции и области видимости', 'type' => 'video', 'module' => 'Функции JavaScript', 'difficulty' => 'medium', 'duration_minutes' => 35,
             'description' => 'Function declarations, expressions, arrow functions, замыкания, рекурсия.',
             'content' => '<h2>Типы функций</h2>
<pre><code>// Объявление
function greet(name) {
  return `Привет, ${name}!`;
}

// Выражение
const greet = function(name) {
  return `Привет, ${name}!`;
};

// Стрелочная
const greet = (name) => `Привет, ${name}!`;
const add = (a, b) => a + b;
const square = x => x * x;  // один аргумент — без скобок

// Параметры по умолчанию
function greet(name = "Гость") {
  return `Привет, ${name}!`;
}

// Rest-параметры
function sum(...nums) {
  return nums.reduce((a, b) => a + b, 0);
}</code></pre>
<h2>Замыкания</h2>
<pre><code>function counter() {
  let count = 0;
  return {
    increment: () => ++count,
    getCount: () => count,
  };
}
const c = counter();
c.increment(); // 1
c.increment(); // 2
c.getCount();  // 2</code></pre>'],
            ['title' => 'Работа с DOM', 'type' => 'video', 'module' => 'DOM и события', 'difficulty' => 'medium', 'duration_minutes' => 40,
             'description' => 'Поиск элементов, создание/удаление, событийная модель, делегирование.',
             'content' => '<h2>Поиск элементов</h2>
<pre><code>document.getElementById("app");
document.querySelector(".card");           // первый
document.querySelectorAll("ul > li");      // все
document.getElementsByClassName("btn");</code></pre>
<h2>Изменение DOM</h2>
<pre><code>el.textContent = "Новый текст";
el.innerHTML = "&lt;b&gt;Жирный&lt;/b&gt;";
el.setAttribute("class", "active");
el.classList.add("visible");
el.classList.toggle("hidden");
el.style.color = "red";
el.style.display = "none";

// Создание
const div = document.createElement("div");
div.className = "card";
div.textContent = "Новый элемент";
document.body.appendChild(div);
el.append(div, "текст", anotherEl);
el.prepend(div);
el.before(div);
el.after(div);

// Удаление
el.remove();
el.parentNode.removeChild(el);</code></pre>
<h2>События</h2>
<pre><code>btn.addEventListener("click", (e) => {
  e.preventDefault();
  console.log("Клик!", e.target);
});

// Делегирование
document.querySelector("ul").addEventListener("click", (e) => {
  if (e.target.tagName === "LI") {
    e.target.classList.toggle("active");
  }
});</code></pre>'],
            ['title' => 'Асинхронность: Promises и async/await', 'type' => 'video', 'module' => 'Асинхронность', 'difficulty' => 'hard', 'duration_minutes' => 45,
             'description' => 'Promise, then/catch, async/await, fetch API, обработка ошибок.',
             'content' => '<h2>Promise</h2>
<pre><code>const promise = new Promise((resolve, reject) => {
  setTimeout(() => resolve("Готово!"), 1000);
});

promise
  .then(result => console.log(result))
  .catch(error => console.error(error))
  .finally(() => console.log("Завершено"));</code></pre>
<h2>async/await</h2>
<pre><code>async function loadUser(id) {
  try {
    const response = await fetch(`/api/users/${id}`);
    if (!response.ok) throw new Error("Ошибка!");
    const user = await response.json();
    return user;
  } catch (err) {
    console.error("Ошибка:", err.message);
  }
}</code></pre>
<h2>Fetch API</h2>
<pre><code>// GET
const users = await fetch("/api/users").then(r => r.json());

// POST
const res = await fetch("/api/users", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify({ name: "Али", email: "ali@mail.com" }),
});
const data = await res.json();

// Параллельные запросы
const [users, posts] = await Promise.all([
  fetch("/api/users").then(r => r.json()),
  fetch("/api/posts").then(r => r.json()),
]);</code></pre>'],
            ['title' => 'Обработка ошибок и отладка', 'type' => 'article', 'module' => 'Асинхронность', 'difficulty' => 'hard', 'duration_minutes' => 30,
             'description' => 'try/catch, типы ошибок, console методы, breakpoints, отладка.',
             'content' => '<h2>Обработка ошибок</h2>
<pre><code>try {
  const data = JSON.parse(jsonString);
} catch (err) {
  if (err instanceof SyntaxError) {
    console.error("Невалидный JSON");
  } else {
    throw err;  // пробросить дальше
  }
} finally {
  console.log("Выполнится всегда");
}</code></pre>
<h2>Типы ошибок</h2>
<pre><code>TypeError       // "Cannot read property of undefined"
ReferenceError  // "x is not defined"
SyntaxError     // "Unexpected token"
RangeError      // "Maximum call stack size exceeded"
URIError        // "Malformed URI"</code></pre>
<h2>Console-методы</h2>
<pre><code>console.log("Обычный вывод");
console.warn("Предупреждение");
console.error("Ошибка");
console.table([{name: "Али", age: 25}]);
console.time("timer");
// ... код ...
console.timeEnd("timer");  // 123.456ms
console.group("Группа");
console.log("Вложенное");
console.groupEnd();
console.trace();  // стек вызовов</code></pre>'],
            ['title' => 'Модули ES6 и работа с JSON', 'type' => 'article', 'module' => 'Основы JavaScript', 'difficulty' => 'medium', 'duration_minutes' => 25,
             'description' => 'import/export, именованные/дефолтные экспорты, JSON.parse/stringify.',
             'content' => '<h2>ES6 Модули</h2>
<pre><code>// math.js
export const PI = 3.14159;
export function add(a, b) { return a + b; }
export default class Calculator { ... }

// app.js
import Calculator, { PI, add } from "./math.js";
import * as math from "./math.js";

console.log(math.PI);
console.log(math.add(2, 3));</code></pre>
<h2>JSON</h2>
<pre><code>// Объект → строка
const json = JSON.stringify({ name: "Али", age: 25 });
// \'{"name":"Али","age":25}\'

// С отступами
JSON.stringify(obj, null, 2);

// Строка → объект
const obj = JSON.parse(\'{"name":"Али","age":25}\');

// Безопасный парсинг
try {
  const obj = JSON.parse(input);
} catch {
  console.error("Невалидный JSON");
}</code></pre>'],
            ['title' => 'Работа с формами и валидация на JS', 'type' => 'article', 'module' => 'DOM и события', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'FormData, валидация полей, отправка через fetch, отображение ошибок.',
             'content' => '<h2>FormData</h2>
<pre><code>const form = document.querySelector("form");
form.addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(form);

  // Чтение
  const name = formData.get("name");
  const all = Object.fromEntries(formData);

  // Отправка
  const res = await fetch("/api/register", {
    method: "POST",
    body: formData,  // автоматически multipart
  });
});</code></pre>
<h2>Валидация</h2>
<pre><code>const emailInput = document.querySelector("#email");

function validateEmail(email) {
  return /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/.test(email);
}

emailInput.addEventListener("input", (e) => {
  const valid = validateEmail(e.target.value);
  e.target.classList.toggle("invalid", !valid);
  const error = e.target.nextElementSibling;
  if (error) error.textContent = valid ? "" : "Некорректный email";
});</code></pre>'],
            ['title' => 'Тест по JavaScript', 'type' => 'quiz', 'module' => 'Тестирование', 'difficulty' => 'hard', 'duration_minutes' => 30,
             'description' => 'Итоговый тест по JavaScript.',
             'content' => '<h2>Тест по JavaScript</h2><p>Проверьте свои знания по JavaScript.</p>'],
        ],
    ],

    3 => [ // PHP
        'lessons' => [
            ['title' => 'Синтаксис PHP и переменные', 'type' => 'video', 'module' => 'Основы PHP', 'difficulty' => 'easy', 'duration_minutes' => 30,
             'description' => 'Установка, синтаксис, переменные, типы данных, операторы.',
             'content' => '<h2>Основы PHP</h2>
<pre><code>&lt;?php
// Переменные начинаются с $
$name = "Али";
$age = 25;
$pi = 3.14;
$isActive = true;
$items = [1, 2, 3];
$user = ["name" => "Али", "age" => 25];

// Типы данных
is_int($age);        // true
is_string($name);    // true
is_array($items);    // true
is_null($var);       // true

// Вывод
echo "Привет, $name!";
print_r($items);
var_dump($user);  // с типами</code></pre>
<h2>Операторы</h2>
<pre><code>echo 5 . 10;      // "510" (конкатенация)
echo 5 + 10;      // 15
echo 5 ** 2;      // 25 (степень)
$x ?? "default";  // null coalescing
$x?->method();    // null safe</code></pre>'],
            ['title' => 'Массивы и строки', 'type' => 'article', 'module' => 'Основы PHP', 'difficulty' => 'easy', 'duration_minutes' => 30,
             'description' => 'Индексированные и ассоциативные массивы, функции массивов, строки.',
             'content' => '<h2>Массивы</h2>
<pre><code>// Индексированный
$fruits = ["яблоко", "банан", "вишня"];
$fruits[] = "груша";  // добавить

// Ассоциативный
$user = ["name" => "Али", "age" => 25];
echo $user["name"];

// Функции массивов
count($arr);           // размер
array_push($arr, $v);  // добавить в конец
array_pop($arr);       // удалить с конца
array_merge($a, $b);   // объединить
array_map(fn($x) => $x * 2, $arr);  // трансформация
array_filter($arr, fn($x) => $x > 5);  // фильтр
array_reduce($arr, fn($acc, $x) => $acc + $x, 0);  // свёртка
in_array("яблоко", $fruits);  // проверка
array_keys($user);     // ["name", "age"]
array_values($user);   // ["Али", 25]</code></pre>
<h2>Строки</h2>
<pre><code>$s = "Hello, World!";
strlen($s);              // 13
strpos($s, "World");     // 7
substr($s, 0, 5);       // "Hello"
str_replace("World", "PHP", $s);
strtoupper($s);          // "HELLO, WORLD!"
trim("  hi  ");          // "hi"
explode(", ", "a, b");   // ["a", "b"]
implode(", ", ["a","b"]); // "a, b"
"Привет, {$name}!";     // интерполяция</code></pre>'],
            ['title' => 'Функции и области видимости', 'type' => 'video', 'module' => 'Функции PHP', 'difficulty' => 'medium', 'duration_minutes' => 35,
             'description' => 'Объявление функций, параметры, типы возвратов, анонимные функции.',
             'content' => '<h2>Функции в PHP</h2>
<pre><code>// Базовая функция
function add(int $a, int $b): int {
    return $a + $b;
}

// Параметры по умолчанию
function greet(string $name = "Гость"): string {
    return "Привет, $name!";
}

// Typed
function process(array $data): bool {
    return count($data) > 0;
}

// Анонимные функции (замыкания)
$multiply = fn($a, $b) => $a * $b;
$multiply(3, 4);  // 12

// Замыкание с use
function counter() {
    $count = 0;
    return function () use (&$count) {
        return ++$count;
    };
}
$counter = counter();
$counter();  // 1
$counter();  // 2</code></pre>'],
            ['title' => 'ООП в PHP', 'type' => 'video', 'module' => 'ООП PHP', 'difficulty' => 'medium', 'duration_minutes' => 45,
             'description' => 'Классы, свойства, методы, наследование, интерфейсы, трейты.',
             'content' => '<h2>ООП в PHP</h2>
<pre><code>class User {
    public string $name;
    protected int $age;
    private string $password;

    public function __construct(string $name, int $age) {
        $this->name = $name;
        $this->age = $age;
    }

    public function getInfo(): string {
        return "{$this->name}, {$this->age}";
    }

    public static function create(string $name): self {
        return new self($name, 0);
    }
}

// Наследование
class Admin extends User {
    public function deleteUser(User $user): bool {
        // ...
        return true;
    }
}

// Интерфейс
interface Serializable {
    public function toArray(): array;
}

// Трейт
trait HasTimestamps {
    public function createdAt(): string {
        return $this->created_at;
    }
}</code></pre>'],
            ['title' => 'Работа с MySQL через PDO', 'type' => 'video', 'module' => 'PDO и БД', 'difficulty' => 'hard', 'duration_minutes' => 45,
             'description' => 'Подключение, CRUD операции, подготовленные выражения, транзакции.',
             'content' => '<h2>PDO в PHP</h2>
<pre><code>// Подключение
$pdo = new PDO(
    "mysql:host=localhost;dbname=mydb;charset=utf8mb4",
    "root", "password",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// SELECT
$stmt = $pdo->prepare("SELECT * FROM users WHERE age > :age");
$stmt->execute(["age" => 18]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// INSERT
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
$stmt->execute(["name" => "Али", "email" => "ali@mail.com"]);
$newId = $pdo->lastInsertId();

// UPDATE
$stmt = $pdo->prepare("UPDATE users SET name = :name WHERE id = :id");
$stmt->execute(["name" => "Новое имя", "id" => 1]);

// DELETE
$stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
$stmt->execute(["id" => 1]);

// Транзакция
$pdo->beginTransaction();
try {
    $pdo->exec("UPDATE accounts SET balance = balance - 100 WHERE id = 1");
    $pdo->exec("UPDATE accounts SET balance = balance + 100 WHERE id = 2");
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    throw $e;
}</code></pre>'],
            ['title' => 'Интерфейсы и абстрактные классы', 'type' => 'article', 'module' => 'ООП PHP', 'difficulty' => 'hard', 'duration_minutes' => 30,
             'description' => 'Интерфейсы, абстрактные классы, полиморфизм, атрибуты.',
             'content' => '<h2>Интерфейсы</h2>
<pre><code>interface Loggable {
    public function toLog(): string;
}

interface Cacheable {
    public function cacheKey(): string;
    public function cacheTtl(): int;
}

class User implements Loggable, Cacheable {
    public function toLog(): string {
        return "User: {$this->name}";
    }
    public function cacheKey(): string {
        return "user:{$this->id}";
    }
    public function cacheTtl(): int {
        return 3600;
    }
}</code></pre>
<h2>Абстрактные классы</h2>
<pre><code>abstract class Shape {
    abstract public function area(): float;

    public function describe(): string {
        return "Площадь: {$this->area()}";
    }
}

class Circle extends Shape {
    public function __construct(private float $radius) {}
    public function area(): float {
        return pi() * $this->radius ** 2;
    }
}

class Rectangle extends Shape {
    public function __construct(
        private float $width,
        private float $height
    ) {}
    public function area(): float {
        return $this->width * $this->height;
    }
}</code></pre>'],
            ['title' => 'Исключения и обработка ошибок', 'type' => 'article', 'module' => 'ООП PHP', 'difficulty' => 'medium', 'duration_minutes' => 25,
             'description' => 'try/catch/finally, пользовательские исключения, стек вызовов.',
             'content' => '<h2>Исключения</h2>
<pre><code>try {
    $user = User::find($id);
    if (!$user) {
        throw new NotFoundException("Пользователь не найден");
    }
} catch (NotFoundException $e) {
    Log::error($e->getMessage());
    return response()->json(["error" => $e->getMessage()], 404);
} catch (Exception $e) {
    return response()->json(["error" => "Ошибка сервера"], 500);
} finally {
    // выполняется всегда
}</code></pre>
<h2>Пользовательские исключения</h2>
<pre><code>class AppException extends Exception {}
class ValidationException extends AppException {
    private array $errors;
    public function __construct(array $errors) {
        parent::Ошибка валидации");
        $this->errors = $errors;
    }
    public function getErrors(): array { return $this->errors; }
}</code></pre>'],
            ['title' => 'Composer и автозагрузка', 'type' => 'article', 'module' => 'Основы PHP', 'difficulty' => 'medium', 'duration_minutes' => 25,
             'description' => 'Установка пакетов, PSR-4 автозагрузка, composer.json.',
             'content' => '<h2>Composer</h2>
<pre><code># Установка пакета
composer require monolog/monolog

# Автозагрузка
require "vendor/autoload.php";

use Monolog\Logger;</code></pre>
<h2>composer.json</h2>
<pre><code>{
    "name": "my/project",
    "autoload": {
        "psr-4": {
            "App\\\\": "app/"
        }
    },
    "require": {
        "php": "^8.1",
        "guzzlehttp/guzzle": "^7.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0"
    }
}</code></pre>'],
            ['title' => 'Тест по PHP', 'type' => 'quiz', 'module' => 'Тестирование', 'difficulty' => 'hard', 'duration_minutes' => 30,
             'description' => 'Итоговый тест по PHP.',
             'content' => '<h2>Тест по PHP</h2><p>Проверьте свои знания по PHP.</p>'],
        ],
    ],

    4 => [ // Laravel
        'lessons' => [
            ['title' => 'MVC архитектура и маршрутизация', 'type' => 'video', 'module' => 'Основы Laravel', 'difficulty' => 'easy', 'duration_minutes' => 35,
             'description' => 'Установка Laravel, маршруты, контроллеры, view, helper-функции.',
             'content' => '<h2>Laravel MVC</h2>
<pre><code>// routes/web.php
Route::get("/users", [UserController::class, "index"]);
Route::get("/users/{id}", [UserController::class, "show"]);
Route::post("/users", [UserController::class, "store"]);
Route::put("/users/{id}", [UserController::class, "update"]);
Route::delete("/users/{id}", [UserController::class, "destroy"]);

// Controller
class UserController extends Controller {
    public function index() {
        $users = User::all();
        return view("users.index", compact("users"));
    }
    public function show($id) {
        $user = User::findOrFail($id);
        return view("users.show", compact("user"));
    }
}

// Blade view
@extends("layouts.app")
@section("content")
    @foreach($users as $user)
        &lt;h2&gt;{{ $user->name }}&lt;/h2&gt;
    @endforeach
@endsection</code></pre>'],
            ['title' => 'Eloquent ORM и миграции', 'type' => 'video', 'module' => 'Eloquent ORM', 'difficulty' => 'medium', 'duration_minutes' => 40,
             'description' => 'Миграции, модели, связи (hasOne, hasMany, belongsTo), accessor/mutator.',
             'content' => '<h2>Миграции</h2>
<pre><code>// Создание
Schema::create("users", function (Blueprint $table) {
    $table->id();
    $table->string("name");
    $table->string("email")->unique();
    $table->enum("role", ["user", "admin"])->default("user");
    $table->timestamps();
});

// Изменение
Schema::table("posts", function (Blueprint $table) {
    $table->foreignId("user_id")->constrained()->cascadeOnDelete();
    $table->text("content")->nullable();
});</code></pre>
<h2>Eloquent</h2>
<pre><code>// CRUD
User::create(["name" => "Али", "email" => "ali@mail.com"]);
User::where("age", ">", 18)->get();
User::findOrFail(1);
$user->update(["name" => "Новое"]);
$user->delete();

// Связи
class User extends Model {
    public function posts() { return $this->hasMany(Post::class); }
    public function profile() { return $this->hasOne(Profile::class); }
}
$users = User::with("posts")->get();</code></pre>'],
            ['title' => 'Middleware и авторизация', 'type' => 'video', 'module' => 'Middleware и Auth', 'difficulty' => 'medium', 'duration_minutes' => 35,
             'description' => 'Middleware, Gate/Policy, Sanctum, registration/login.',
             'content' => '<h2>Middleware</h2>
<pre><code>// routes/web.php
Route::middleware(["auth"])->group(function () {
    Route::get("/dashboard", [DashController::class, "index"]);
    Route::resource("posts", PostController::class);
});

// Кастомный middleware
class CheckRole {
    public function handle($request, Closure $next, string $role) {
        if ($request->user()->role !== $role) {
            abort(403);
        }
        return $next($request);
    }
}
Route::middleware("role:admin")->group(...);</code></pre>
<h2>Gate</h2>
<pre><code>Gate::define("edit-post", function (User $user, Post $post) {
    return $user->id === $post->user_id;
});

// В Blade
@can("edit-post", $post)
    &lt;a href="/posts/{{ $post->id }}/edit"&gt;Редактировать&lt;/a&gt;
@endcan</code></pre>'],
            ['title' => 'Blade шаблоны и компоненты', 'type' => 'article', 'module' => 'Blade Templates', 'difficulty' => 'hard', 'duration_minutes' => 40,
             'description' => 'Blade-компоненты, секции, вкладки, формы, layouts.',
             'content' => '<h2>Blade Components</h2>
<pre><code>&lt;!-- resources/views/components/alert.blade.php --&gt;
@props(["type" => "info", "message"])

&lt;div class="alert alert-{{ $type }}"&gt;
    {{ $message }}
&lt;/div&gt;

&lt;!-- Использование --&gt;
&lt;x-alert type="success" message="Готово!" /&gt;

&lt;!-- Слоты --&gt;
&lt;x-card&gt;
    &lt;h2&gt;Заголовок&lt;/h2&gt;
    &lt;p&gt;Содержимое&lt;/p&gt;
&lt;/x-card&gt;</code></pre>
<h2>Формы</h2>
<pre><code>&lt;form method="POST" action="/posts"&gt;
    @csrf
    @method("PUT")
    &lt;input name="title" value="{{ old("title") }}"&gt;
    @error("title")
        &lt;span class="error"&gt;{{ $message }}&lt;/span&gt;
    @enderror
    &lt;button type="submit"&gt;Сохранить&lt;/button&gt;
&lt;/form&gt;</code></pre>'],
            ['title' => 'Валидация и запросы (Form Requests)', 'type' => 'video', 'module' => 'Blade Templates', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'Валидация в контроллере, Form Request классы, сообщения ошибок.',
             'content' => '<h2>Валидация</h2>
<pre><code>// В контроллере
public function store(Request $request) {
    $validated = $request->validate([
        "name" => "required|string|min:2|max:255",
        "email" => "required|email|unique:users,email",
        "password" => "required|min:8|confirmed",
    ]);
    User::create($validated);
    return redirect("/users");
}

// Form Request
class StoreUserRequest extends Request {
    public function rules(): array {
        return [
            "name" => "required|string|min:2",
            "email" => "required|email|unique:users,email",
        ];
    }
    public function messages(): array {
        return ["email.unique" => "Этот email уже зарегистрирован"];
    }
}

// Использование
public function store(StoreUserRequest $request) {
    $data = $request->validated();
    User::create($data);
}</code></pre>'],
            ['title' => 'Работа с API и JSON', 'type' => 'article', 'module' => 'Основы Laravel', 'difficulty' => 'hard', 'duration_minutes' => 35,
             'description' => 'API Resources, JSON-ответы, CORS, paginate, сортировка.',
             'content' => '<h2>API Resources</h2>
<pre><code>// app/Http/Resources/UserResource.php
class UserResource extends JsonResource {
    public function toArray($request): array {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "posts_count" => $this->posts()->count(),
        ];
    }
}

// Controller
class ApiUserController extends Controller {
    public function index() {
        return UserResource::collection(
            User::with("posts")->paginate(15)
        );
    }
    public function show(User $user) {
        return new UserResource($user);
    }
}

// routes/api.php
Route::apiResource("users", ApiUserController::class);</code></pre>'],
            ['title' => 'Тест по Laravel', 'type' => 'quiz', 'module' => 'Тестирование', 'difficulty' => 'hard', 'duration_minutes' => 30,
             'description' => 'Итоговый тест по Laravel.',
             'content' => '<h2>Тест по Laravel</h2><p>Проверьте свои знания по Laravel.</p>'],
        ],
    ],

    5 => [ // MySQL
        'lessons' => [
            ['title' => 'DDL: создание баз данных и таблиц', 'type' => 'video', 'module' => 'DDL MySQL', 'difficulty' => 'easy', 'duration_minutes' => 30,
             'description' => 'CREATE DATABASE/TABLE, типы данных, ограничения, ALTER/DROP.',
             'content' => '<h2>DDL — Data Definition Language</h2>
<pre><code>CREATE DATABASE mydb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mydb;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    age INT CHECK (age >= 0 AND age <= 150),
    role ENUM("user", "admin") DEFAULT "user",
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Изменение таблицы
ALTER TABLE users ADD COLUMN bio TEXT;
ALTER TABLE users MODIFY COLUMN name VARCHAR(200);
ALTER TABLE users DROP COLUMN bio;
ALTER TABLE users RENAME COLUMN name TO full_name;

-- Удаление
DROP TABLE IF EXISTS temp_data;
TRUNCATE TABLE logs;  -- удалить все строки</code></pre>'],
            ['title' => 'DML: SELECT, INSERT, UPDATE, DELETE', 'type' => 'video', 'module' => 'DML MySQL', 'difficulty' => 'easy', 'duration_minutes' => 35,
             'description' => 'CRUD-операции, WHERE, ORDER BY, LIMIT, DISTINCT.',
             'content' => '<h2>SELECT</h2>
<pre><code>SELECT * FROM users;
SELECT name, email FROM users WHERE age > 18;
SELECT DISTINCT city FROM users;
SELECT name, age FROM users ORDER BY age DESC;
SELECT * FROM users LIMIT 10 OFFSET 20;

-- Агрегаты
SELECT COUNT(*), AVG(age), MAX(age), MIN(age), SUM(salary)
FROM users GROUP BY role HAVING COUNT(*) > 5;</code></pre>
<h2>INSERT</h2>
<pre><code>INSERT INTO users (name, email, age) VALUES ("Али", "ali@mail.com", 25);
INSERT INTO users (name, email) VALUES
    ("Боб", "bob@mail.com"),
    ("Карл", "karl@mail.com");</code></pre>
<h2>UPDATE / DELETE</h2>
<pre><code>UPDATE users SET age = 26 WHERE id = 1;
UPDATE users SET role = "admin" WHERE age > 30;
DELETE FROM users WHERE id = 1;
DELETE FROM users WHERE created_at < "2024-01-01";</code></pre>'],
            ['title' => 'JOINы и подзапросы', 'type' => 'video', 'module' => 'Запросы MySQL', 'difficulty' => 'medium', 'duration_minutes' => 40,
             'description' => 'INNER JOIN, LEFT JOIN, RIGHT JOIN, CROSS JOIN, подзапросы, UNION.',
             'content' => '<h2>JOINы</h2>
<pre><code>-- INNER JOIN: только совпадающие
SELECT u.name, p.title
FROM users u
INNER JOIN posts p ON u.id = p.user_id;

-- LEFT JOIN: все из левой таблицы
SELECT u.name, COUNT(p.id) as post_count
FROM users u
LEFT JOIN posts p ON u.id = p.user_id
GROUP BY u.id;

-- CROSS JOIN: декартово произведение
SELECT u.name, r.title FROM users u CROSS JOIN roles r;

-- SELF JOIN
SELECT e.name, m.name as manager
FROM employees e
LEFT JOIN employees m ON e.manager_id = m.id;</code></pre>
<h2>Подзапросы</h2>
<pre><code>SELECT * FROM users WHERE id IN (
    SELECT user_id FROM posts WHERE created_at > "2024-01-01"
);

SELECT * FROM users WHERE age > (
    SELECT AVG(age) FROM users
);

-- EXISTS
SELECT * FROM users u WHERE EXISTS (
    SELECT 1 FROM posts p WHERE p.user_id = u.id
);</code></pre>'],
            ['title' => 'Индексы и оптимизация', 'type' => 'video', 'module' => 'Оптимизация', 'difficulty' => 'hard', 'duration_minutes' => 40,
             'description' => 'EXPLAIN, индексы, определение запросов, кэширование.',
             'content' => '<h2>EXPLAIN</h2>
<pre><code>EXPLAIN SELECT * FROM users WHERE email = "ali@mail.com";
-- Показывает: type (ALL=плохо, ref=хорошо), rows, key</code></pre>
<h2>Индексы</h2>
<pre><code>CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_name_age ON users(name, age);  -- составной
CREATE UNIQUE INDEX idx_unique_email ON users(email);

-- Когда нужен индекс:
-- WHERE column = value
-- JOIN ON column
-- ORDER BY column
-- Когда НЕ нужен:
-- маленькие таблицы
-- столбец с малым количеством уникальных значений</code></pre>
<h2>Оптимизация запросов</h2>
<pre><code>-- Не использовать SELECT *
SELECT id, name FROM users;

-- Использовать LIMIT
SELECT * FROM posts ORDER BY id DESC LIMIT 10;

-- Избегать LIKE с %
SELECT * FROM users WHERE name LIKE "али%";  -- хорошо
SELECT * FROM users WHERE name LIKE "%али%";  -- плохо

-- Кэширование
SELECT SQL_CACHE * FROM users;  -- MySQL 5.7</code></pre>'],
            ['title' => 'Транзакции и блокировки', 'type' => 'article', 'module' => 'Оптимизация', 'difficulty' => 'hard', 'duration_minutes' => 35,
             'description' => 'ACID, транзакции, уровни изоляции, блокировки, deadlock.',
             'content' => '<h2>Транзакции</h2>
<pre><code>START TRANSACTION;
UPDATE accounts SET balance = balance - 100 WHERE id = 1;
UPDATE accounts SET balance = balance + 100 WHERE id = 2;
COMMIT;  -- или ROLLBACK при ошибке</code></pre>
<h2>Уровни изоляции</h2>
<pre><code>SET TRANSACTION ISOLATION LEVEL READ COMMITTED;
-- READ UNCOMMITTED: грязные чтения
-- READ COMMITTED: без грязных чтений
-- REPEATABLE READ: постоянные чтения (по умолчанию)
-- SERIALIZABLE: полная изоляция</code></pre>
<h2>Полезные запросы</h2>
<pre><code>-- Последние 10 пользователей
SELECT * FROM users ORDER BY created_at DESC LIMIT 10;

-- Пользователи с количеством постов
SELECT u.name, COUNT(p.id) as posts
FROM users u LEFT JOIN posts p ON u.id = p.user_id
GROUP BY u.id ORDER BY posts DESC;

-- Топ-3 автора по лайкам
SELECT u.name, SUM(p.likes_count) as total_likes
FROM users u JOIN posts p ON u.id = p.user_id
GROUP BY u.id ORDER BY total_likes DESC LIMIT 3;</code></pre>'],
            ['title' => 'Процедуры, триггеры и представления', 'type' => 'article', 'module' => 'Оптимизация', 'difficulty' => 'hard', 'duration_minutes' => 40,
             'description' => 'STORED PROCEDURE, FUNCTION, TRIGGER, VIEW.',
             'content' => '<h2>Процедуры</h2>
<pre><code>DELIMITER //
CREATE PROCEDURE GetUsersByAge(IN min_age INT)
BEGIN
    SELECT * FROM users WHERE age >= min_age;
END //
DELIMITER ;

CALL GetUsersByAge(18);</code></pre>
<h2>Функции</h2>
<pre><code>CREATE FUNCTION CalculateAge(birthdate DATE)
RETURNS INT DETERMINISTIC
BEGIN
    RETURN TIMESTAMPDIFF(YEAR, birthdate, CURDATE());
END;

SELECT name, CalculateAge(birth_date) as age FROM users;</code></pre>
<h2>Триггеры</h2>
<pre><code>CREATE TRIGGER before_user_insert
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
    SET NEW.created_at = NOW();
END;</code></pre>
<h2>Представления</h2>
<pre><code>CREATE VIEW active_users AS
SELECT u.id, u.name, COUNT(p.id) as post_count
FROM users u LEFT JOIN posts p ON u.id = p.user_id
WHERE u.is_active = 1
GROUP BY u.id;

SELECT * FROM active_users WHERE post_count > 5;</code></pre>'],
            ['title' => 'Хранилища данных и нормализация', 'type' => 'article', 'module' => 'DDL MySQL', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'Нормальные формы (1NF-3NF), выбор хранилищ引擎, InnoDB vs MyISAM.',
             'content' => '<h2>Нормализация</h2>
<h3>1NF — Атомарность</h3>
<p>Каждая ячейка содержит одно значение. Нет повторяющихся групп.</p>
<h3>2NF — Полная зависимость</h3>
<p>Все неключевые поля зависят от всего первичного ключа.</p>
<h3>3NF — Транзитивная зависимость</h3>
<p>Неключевые поля зависят только от первичного ключа, не от других неключевых.</p>
<h2>Хранилища引擎</h2>
<pre><code>-- InnoDB (по умолчанию)
-- Транзакции, foreign keys, row-level locking

-- MyISAM
-- Быстрый SELECT, нет транзакций, table-level locking

-- MEMORY
-- В памяти, очень быстро, данные теряются при перезагрузке

-- Выбор
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    data TEXT
) ENGINE=MEMORY;</code></pre>'],
            ['title' => 'Тест по MySQL', 'type' => 'quiz', 'module' => 'Тестирование', 'difficulty' => 'hard', 'duration_minutes' => 30,
             'description' => 'Итоговый тест по MySQL.',
             'content' => '<h2>Тест по MySQL</h2><p>Проверьте свои знания по MySQL.</p>'],
        ],
    ],
];
