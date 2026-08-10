<?php

return [
    11 => [ // Git
        'lessons' => [
            ['title' => 'Основы Git: установка и базовые команды', 'type' => 'video', 'module' => 'Git', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Установка, конфигурация, git init, add, commit, status, log.',
             'content' => '<h2>Основы Git</h2>
<pre><code># Установка
git --version
git config --global user.name "Ваше имя"
git config --global user.email "email@example.com"

# Инициализация
git init               # создать репозиторий
git clone URL          # скопировать существующий

# Основной цикл
git status             # состояние файлов
git add file.txt       # добавить в индекс
git add .              # добавить все изменения
git commit -m "Описание"# зафиксировать

# Просмотр
git log --oneline      # история коммитов
git log --oneline -10  # последние 10
git diff               # изменения
git diff --staged      # изменения в индексе</code></pre>'],
            ['title' => 'Ветвление и слияние', 'type' => 'video', 'module' => 'Git', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'git branch, checkout, switch, merge, rebase.',
             'content' => '<h2>Ветвление</h2>
<pre><code># Ветки
git branch                  # список веток
git branch feature-login    # создать ветку
git switch feature-login    # переключиться
git switch -c feature-new   # создать и переключить
git branch -d feature-login # удалить ветку

# Слияние
git switch main
git merge feature-login     # слить feature в main

# Rebase (альтернатива merge)
git switch feature-login
git rebase main             # переместить коммиты feature поверх main

# Разрешение конфликтов
# Редактируем файлы, затем:
git add .
git rebase --continue       # для rebase
git commit                  # для merge</code></pre>'],
            ['title' => 'Удалённые репозитории и работа в команде', 'type' => 'video', 'module' => 'Git', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'git push, pull, fetch, origin, forks, Pull Requests.',
             'content' => '<h2>Удалённые репозитории</h2>
<pre><code># Подключение
git remote add origin URL
git remote -v

# Отправка
git push -u origin main     # в первый раз
git push                    # далее

# Получение
git fetch origin            # скачать изменения
git pull origin main        # fetch + merge

# Fork workflow
# 1. Fork на GitHub
# 2. git clone ваш-fork
# 3. git remote add upstream ORIGINAL_URL
# 4. git fetch upstream
# 5. git merge upstream/main
# 6. git push</code></pre>'],
            ['title' => 'Продвинутые техники Git', 'type' => 'article', 'module' => 'Git', 'difficulty' => 'hard', 'duration_minutes' => 35,
             'description' => 'git stash, cherry-pick, reset, revert, bisect, hooks.',
             'content' => '<h2>Продвинутые команды</h2>
<pre><code># Stash (временное сохранение)
git stash                  # сохранить изменения
git stash pop              # восстановить
git stash list             # список стэшей

# Cherry-pick (взять конкретный коммит)
git cherry-pick abc123

# Reset (откат)
git reset --soft HEAD~1    # откатить коммит, изменения в индексе
git reset --mixed HEAD~1   # откатить, изменения в рабочей директории
git reset --hard HEAD~1    # откатить, УДАЛИТЬ изменения

# Revert (отменить коммит безопасно)
git revert abc123

# Bisect (поиск бага)
git bisect start
git bisect bad             # текущий коммит сломан
git bisect good abc123     # этот коммит был рабочим
# Git переключается между коммитами, вы тестируете</code></pre>'],
            ['title' => 'Тест по Git', 'type' => 'quiz', 'module' => 'Git', 'difficulty' => 'hard', 'duration_minutes' => 60,
             'description' => 'Итоговый тест по Git.',
             'content' => '<h2>Тест по Git</h2>'],
        ],
    ],

    12 => [ // DevOps
        'lessons' => [
            ['title' => 'Основы DevOps', 'type' => 'video', 'module' => 'DevOps', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Принципы DevOps, культура, CI/CD, автоматизация.',
             'content' => '<h2>Что такое DevOps?</h2>
<p>DevOps — это набор практик, объединяющих разработку (Dev) и эксплуатацию (Ops) для ускорения доставки ПО.</p>
<h3>Ключевые принципы</h3>
<ul>
<li><strong>CI/CD:</strong> непрерывная интеграция и доставка</li>
<li><strong>Инфраструктура как код:</strong> Terraform, Ansible</li>
<li><strong>Мониторинг:</strong> Prometheus, Grafana</li>
<li><strong>Контейнеризация:</strong> Docker, Kubernetes</li>
<li><strong>Автоматизация:</strong> скрипты, пайплайны</li>
</ul>'],
            ['title' => 'Автоматизация сборки: CI/CD', 'type' => 'video', 'module' => 'DevOps', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'GitHub Actions, GitLab CI, Jenkins, пайплайны.',
             'content' => '<h2>GitHub Actions</h2>
<pre><code># .github/workflows/ci.yml
name: CI

on:
  push:
    branches: [main]
  pull_request:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: 20
      - run: npm ci
      - run: npm test
      - run: npm run build

  deploy:
    needs: test
    if: github.ref == "refs/heads/main"
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - run: docker build -t app .
      - run: docker push myregistry/app</code></pre>'],
            ['title' => 'Мониторинг и логирование', 'type' => 'video', 'module' => 'DevOps', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'Prometheus, Grafana, ELK Stack, алерты.',
             'content' => '<h2>Мониторинг</h2>
<p>Prometheus собирает метрики, Grafana визуализирует.</p>
<h2>ELK Stack</h2>
<ul>
<li><strong>Elasticsearch:</strong> поиск и хранение логов</li>
<li><strong>Logstash:</strong> обработка логов</li>
<li><strong>Kibana:</strong> визуализация</li>
</ul>'],
            ['title' => 'Инфраструктура как код', 'type' => 'video', 'module' => 'DevOps', 'difficulty' => 'hard', 'duration_minutes' => 35,
             'description' => 'Terraform, Ansible, CloudFormation.',
             'content' => '<h2>Terraform</h2>
<pre><code># main.tf
resource "aws_instance" "web" {
  ami           = "ami-0c55b159cbfafe1f0"
  instance_type = "t2.micro"

  tags = {
    Name = "WebServer"
  }
}

# Команды
terraform init
terraform plan
terraform apply
terraform destroy</code></pre>'],
            ['title' => 'Тест по DevOps', 'type' => 'quiz', 'module' => 'DevOps', 'difficulty' => 'hard', 'duration_minutes' => 60,
             'description' => 'Итоговый тест по DevOps.',
             'content' => '<h2>Тест по DevOps</h2>'],
        ],
    ],

    13 => [ // UI/UX Design
        'lessons' => [
            ['title' => 'Основы UX: принципы пользовательского опыта', 'type' => 'video', 'module' => 'UI/UX Design', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Что такое UX, personas, user journey, исследования.',
             'content' => '<h2>Что такое UX?</h2>
<p>UX (User Experience) — это впечатление пользователя от взаимодействия с продуктом.</p>
<h3>Принципы хорошего UX</h3>
<ul>
<li><strong>Удобство:</strong> продукт должен быть интуитивным</li>
<li><strong>Эффективность:</strong> минимальные шаги для достижения цели</li>
<li><strong>Доступность:</strong> доступен всем пользователям</li>
<li><strong>Надёжность:</strong> работает стабильно</li>
<li><strong>Эстетика:</strong> приятный внешний вид</li>
</ul>'],
            ['title' => 'Теория цвета и типографика', 'type' => 'video', 'module' => 'UI/UX Design', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'Цветовые модели, гармония, шрифты, иерархия текста.',
             'content' => '<h2>Цвет</h2>
<ul>
<li><strong>Primary:</strong> основной цвет бренда</li>
<li><strong>Secondary:</strong> дополнительный</li>
<li><strong>Accent:</strong> акцентные элементы</li>
<li><strong>Neutral:</strong> серые тона для фона и текста</li>
</ul>
<h2>Типографика</h2>
<ul>
<li>Заголовки: крупный, полужирный</li>
<li>Основной текст: 16px, межстрочный 1.5</li>
<li>Не больше 2-3 шрифтов на проект</li>
</ul>'],
            ['title' => 'Прототипирование в Figma', 'type' => 'video', 'module' => 'UI/UX Design', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'Wireframe, mockup, интерактивный прототип, компоненты.',
             'content' => '<h2>Figma</h2>
<p>Figma — инструмент для дизайна интерфейсов.</p>
<h3>Workflow</h3>
<ol>
<li>Wireframe (эскиз) → low-fidelity</li>
<li>Mockup (макет) → high-fidelity</li>
<li>Прототип → интерактивные связи</li>
<li>Handoff → передача разработчикам</li>
</ol>'],
            ['title' => 'Дизайн-системы и UI-компоненты', 'type' => 'video', 'module' => 'UI/UX Design', 'difficulty' => 'hard', 'duration_minutes' => 35,
             'description' => 'Создание компонентов, стилей, documentation.',
             'content' => '<h2>Дизайн-система</h2>
<p>Набор повторно используемых компонентов и правил.</p>
<h3>Компоненты</h3>
<ul>
<li>Кнопки (primary, secondary, ghost)</li>
<li>Формы (input, select, checkbox)</li>
<li>Карточки</li>
<li>Модальные окна</li>
<li>Навигация</li>
</ul>'],
            ['title' => 'Тест по UI/UX', 'type' => 'quiz', 'module' => 'UI/UX Design', 'difficulty' => 'hard', 'duration_minutes' => 60,
             'description' => 'Итоговый тест по UI/UX Design.',
             'content' => '<h2>Тест по UI/UX</h2>'],
        ],
    ],

    14 => [ // React
        'lessons' => [
            ['title' => 'Основы React: компоненты, JSX, пропсы', 'type' => 'video', 'module' => 'React', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'JSX, функциональные компоненты, пропсы, условный рендеринг.',
             'content' => '<h2>React</h2>
<pre><code>// JSX — расширенный HTML в JS
function App() {
  return (
    &lt;div className="app"&gt;
      &lt;h1&gt;Привет, мир!&lt;/h1&gt;
      &lt;Greeting name="Али" /&gt;
    &lt;/div&gt;
  );
}

// Компонент с пропсами
function Greeting({ name, age = 25 }) {
  return (
    &lt;div&gt;
      &lt;p&gt;Привет, {name}!&lt;/p&gt;
      {age &gt;= 18 && &lt;p&gt;Совершеннолетний&lt;/p&gt;}
    &lt;/div&gt;
  );
}

// Условный рендеринг
function Status({ isLoggedIn }) {
  return isLoggedIn ? &lt;Dashboard /&gt; : &lt;LoginForm /&gt;;
}</code></pre>'],
            ['title' => 'Хуки: useState, useEffect, useRef', 'type' => 'video', 'module' => 'React', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'Состояние, эффекты, ссылки, кастомные хуки.',
             'content' => '<h2>React Hooks</h2>
<pre><code>import { useState, useEffect, useRef } from "react";

function Counter() {
  const [count, setCount] = useState(0);

  useEffect(() =&gt; {
    document.title = `Счётчик: ${count}`;
    return () =&gt; console.log("cleanup");
  }, [count]);

  return (
    &lt;button onClick={() =&gt; setCount(c =&gt; c + 1)}&gt;
      Клики: {count}
    &lt;/button&gt;
  );
}

function TextInput() {
  const inputRef = useRef(null);
  return (
    &lt;div&gt;
      &lt;input ref={inputRef} /&gt;
      &lt;button onClick={() =&gt; inputRef.current.focus()}&gt;
        Фокус
      &lt;/button&gt;
    &lt;/div&gt;
  );
}

// Кастомный хук
function useLocalStorage(key, initial) {
  const [value, setValue] = useState(() =&gt; {
    return localStorage.getItem(key) || initial;
  });
  useEffect(() =&gt; localStorage.setItem(key, value), [key, value]);
  return [value, setValue];
}</code></pre>'],
            ['title' => 'Управление состоянием: Context, Redux', 'type' => 'video', 'module' => 'React', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'Context API, useReducer, Redux Toolkit, Zustand.',
             'content' => '<h2>Context API</h2>
<pre><code>const ThemeContext = createContext("light");

function App() {
  const [theme, setTheme] = useState("light");
  return (
    &lt;ThemeContext.Provider value={{ theme, setTheme }}&gt;
      &lt;Child /&gt;
    &lt;/ThemeContext.Provider&gt;
  );
}

function Child() {
  const { theme, setTheme } = useContext(ThemeContext);
  return &lt;button onClick={() =&gt; setTheme(t =&gt; t === "light" ? "dark" : "light")}&gt;Тема: {theme}&lt;/button&gt;;
}</code></pre>'],
            ['title' => 'React Router и работа с API', 'type' => 'video', 'module' => 'React', 'difficulty' => 'hard', 'duration_minutes' => 35,
             'description' => 'Маршрутизация, useParams, useNavigate, fetch, axios.',
             'content' => '<h2>React Router</h2>
<pre><code>import { BrowserRouter, Routes, Route, Link, useParams, useNavigate } from "react-router-dom";

function App() {
  return (
    &lt;BrowserRouter&gt;
      &lt;nav&gt;
        &lt;Link to="/"&gt;Главная&lt;/Link&gt;
        &lt;Link to="/users"&gt;Пользователи&lt;/Link&gt;
      &lt;/nav&gt;
      &lt;Routes&gt;
        &lt;Route path="/" element={&lt;Home /&gt;} /&gt;
        &lt;Route path="/users" element={&lt;UserList /&gt;} /&gt;
        &lt;Route path="/users/:id" element={&lt;UserDetail /&gt;} /&gt;
      &lt;/Routes&gt;
    &lt;/BrowserRouter&gt;
  );
}

function UserDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [user, setUser] = useState(null);

  useEffect(() =&gt; {
    fetch(`/api/users/${id}`).then(r =&gt; r.json()).then(setUser);
  }, [id]);

  return user ? &lt;div&gt;{user.name}&lt;/div&gt; : &lt;Spinner /&gt;;
}</code></pre>'],
            ['title' => 'Тест по React', 'type' => 'quiz', 'module' => 'React', 'difficulty' => 'hard', 'duration_minutes' => 60,
             'description' => 'Итоговый тест по React.',
             'content' => '<h2>Тест по React</h2>'],
        ],
    ],

    15 => [ // Node.js
        'lessons' => [
            ['title' => 'Основы Node.js', 'type' => 'video', 'module' => 'Node.js', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Модули, fs, path, events, npm.',
             'content' => '<h2>Node.js</h2>
<pre><code>// Модули
const fs = require("fs");
const path = require("path");
const EventEmitter = require("events");

// fs
fs.writeFileSync("test.txt", "Hello");
const data = fs.readFileSync("test.txt", "utf-8");
fs.readdirSync(".").forEach(f =&gt; console.log(f));

// path
path.join(__dirname, "uploads", "file.jpg");
path.extname("photo.jpg");  // ".jpg"

// Events
class Logger extends EventEmitter {}
const log = new Logger();
log.on("data", (msg) =&gt; console.log(msg));
log.emit("data", "Новое событие");

// npm
// npm init -y
// npm install express
// npm install -D nodemon</code></pre>'],
            ['title' => 'Express.js: маршруты и middleware', 'type' => 'video', 'module' => 'Node.js', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'GET/POST/PUT/DELETE, middleware, валидация, обработка ошибок.',
             'content' => '<h2>Express.js</h2>
<pre><code>const express = require("express");
const app = express();

app.use(express.json());

// Маршруты
app.get("/api/users", async (req, res) =&gt; {
  const users = await User.find();
  res.json(users);
});

app.post("/api/users", async (req, res) =&gt; {
  const user = await User.create(req.body);
  res.status(201).json(user);
});

app.put("/api/users/:id", async (req, res) =&gt; {
  const user = await User.findByIdAndUpdate(req.params.id, req.body, { new: true });
  res.json(user);
});

app.delete("/api/users/:id", async (req, res) =&gt; {
  await User.findByIdAndDelete(req.params.id);
  res.status(204).send();
});

// Middleware
function authMiddleware(req, res, next) {
  const token = req.headers.authorization?.split(" ")[1];
  if (!token) return res.status(401).json({ error: "Unauthorized" });
  req.user = jwt.verify(token, SECRET);
  next();
}

app.use("/api", authMiddleware);

// Обработка ошибок
app.use((err, req, res, next) =&gt; {
  console.error(err.stack);
  res.status(500).json({ error: "Internal Server Error" });
});

app.listen(3000);</code></pre>'],
            ['title' => 'Работа с базами данных', 'type' => 'video', 'module' => 'Node.js', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'MongoDB, Mongoose, Prisma, подключение и CRUD.',
             'content' => '<h2>MongoDB + Mongoose</h2>
<pre><code>const mongoose = require("mongoose");

mongoose.connect("mongodb://localhost:27017/mydb");

const userSchema = new mongoose.Schema({
  name: { type: String, required: true },
  email: { type: String, unique: true },
  age: { type: Number, min: 0 },
}, { timestamps: true });

const User = mongoose.model("User", userSchema);

// CRUD
await User.create({ name: "Али", email: "ali@mail.com" });
await User.find({ age: { $gte: 18 } });
await User.findById(id);
await User.findByIdAndUpdate(id, { name: "Новое" });
await User.findByIdAndDelete(id);</code></pre>'],
            ['title' => 'REST API и аутентификация', 'type' => 'video', 'module' => 'Node.js', 'difficulty' => 'hard', 'duration_minutes' => 35,
             'description' => 'JWT, OAuth, rate limiting, CORS.',
             'content' => '<h2>JWT Аутентификация</h2>
<pre><code>const jwt = require("jsonwebtoken");
const bcrypt = require("bcrypt");

const SECRET = process.env.JWT_SECRET;

// Регистрация
app.post("/register", async (req, res) =&gt; {
  const hash = await bcrypt.hash(req.body.password, 10);
  const user = await User.create({ ...req.body, password: hash });
  const token = jwt.sign({ id: user.id }, SECRET, { expiresIn: "7d" });
  res.json({ token, user });
});

// Вход
app.post("/login", async (req, res) =&gt; {
  const user = await User.findOne({ email: req.body.email });
  if (!user || !(await bcrypt.compare(req.body.password, user.password))) {
    return res.status(401).json({ error: "Invalid credentials" });
  }
  const token = jwt.sign({ id: user.id }, SECRET, { expiresIn: "7d" });
  res.json({ token, user });
});</code></pre>'],
            ['title' => 'Тест по Node.js', 'type' => 'quiz', 'module' => 'Node.js', 'difficulty' => 'hard', 'duration_minutes' => 60,
             'description' => 'Итоговый тест по Node.js.',
             'content' => '<h2>Тест по Node.js</h2>'],
        ],
    ],
];
