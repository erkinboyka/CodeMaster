# CodeMaster — System Design

**Версия:** 1.0  
**Дата:** 2026-08-01

---

## 1. Архитектура системы

### 1.1 Обзор

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                              │
│                                                                  │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐            │
│  │   Browser    │  │   Mobile    │  │   Admin     │            │
│  │  (Blade)     │  │  (PWA)      │  │  (Panel)    │            │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘            │
│         │                 │                 │                    │
└─────────┼─────────────────┼─────────────────┼──────────────────┘
          │                 │                 │
          ▼                 ▼                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                     PRESENTATION LAYER                           │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                    Laravel 12 Router                       │   │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐              │   │
│  │  │ Public   │  │ Auth     │  │ Admin    │              │   │
│  │  │ Routes   │  │ Routes   │  │ Routes   │              │   │
│  │  └──────────┘  └──────────┘  └──────────┘              │   │
│  └──────────────────────────┬───────────────────────────────┘   │
│                              │                                   │
│  ┌──────────────────────────▼───────────────────────────────┐   │
│  │                    Middleware Stack                         │   │
│  │  SetLocale → auth → admin → TrimStrings → Validate       │   │
│  └──────────────────────────┬───────────────────────────────┘   │
│                              │                                   │
│  ┌──────────────────────────▼───────────────────────────────┐   │
│  │                   Controllers                              │   │
│  │  Auth │ Education │ Career │ AI │ Community │ Admin       │   │
│  └──────────────────────────┬───────────────────────────────┘   │
│                              │                                   │
│  ┌──────────────────────────▼───────────────────────────────┐   │
│  │                    Blade Views                             │   │
│  │  Layouts │ Components │ Pages │ Admin │ Auth              │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
          │
          ▼
┌─────────────────────────────────────────────────────────────────┐
│                      DOMAIN LAYER                                │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                      Services                              │   │
│  │  GeminiService │ Judge0Service │ I18nService              │   │
│  │  GoogleAuthService │ RecaptchaService                     │   │
│  └──────────────────────────┬───────────────────────────────┘   │
│                              │                                   │
│  ┌──────────────────────────▼───────────────────────────────┐   │
│  │                    Models (Eloquent)                        │   │
│  │  User │ Course │ Lesson │ Vacancy │ Certificate │ ...    │   │
│  │  43 Eloquent Models                                       │   │
│  └──────────────────────────┬───────────────────────────────┘   │
│                              │                                   │
│  ┌──────────────────────────▼───────────────────────────────┐   │
│  │                  Helpers                                   │   │
│  │  t() │ currentLang() │ langUrl() │ getAvatarUrl()         │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
          │
          ▼
┌─────────────────────────────────────────────────────────────────┐
│                      DATA LAYER                                  │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                   Database                                 │   │
│  │  MySQL (production) │ SQLite (testing)                    │   │
│  │  44 migrations │ 30+ tables                              │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                   Cache                                     │   │
│  │  Database (default) │ Redis (optional)                    │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                   Session                                   │   │
│  │  Database driver │ 24-hour TTL                            │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
          │
          ▼
┌─────────────────────────────────────────────────────────────────┐
│                  EXTERNAL SERVICES LAYER                         │
│                                                                  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │ Gemini AI    │  │ Judge0 CE    │  │ Google OAuth │         │
│  │ (gemini-2.5  │  │ (code exec)  │  │ (auth)       │         │
│  │  flash)      │  │              │  │              │         │
│  └──────────────┘  └──────────────┘  └──────────────┘         │
│                                                                  │
│  ┌──────────────┐  ┌──────────────┐                             │
│  │ reCAPTCHA    │  │ UI Avatars   │                             │
│  │ (anti-bot)   │  │ (avatars)    │                             │
│  └──────────────┘  └──────────────┘                             │
└─────────────────────────────────────────────────────────────────┘
```

---

## 2. Структура базы данных

### 2.1 ER-диаграмма (текстовый)

```
┌──────────────┐     ┌──────────────────┐     ┌──────────────┐
│    users      │────<│  user_skills      │     │   courses    │
│              │────<│  user_experience   │     │              │
│              │────<│  user_education    │     │              │
│              │────<│  user_portfolio    │     │              │
│              │────<│  user_course_progress│    │              │
│              │────<│  user_lesson_progress│    │              │
│              │────<│  certificates      │     │              │
│              │────<│  notifications     │     │              │
│              │────<│  user_activities   │     │              │
│              │────<│  chat_messages     │     │              │
│              │────<│  user_applications │     │              │
│              │────<│  platform_reviews  │     │              │
│              │────<│  user_ai_wallets   │     │              │
│              │────<│  interviews        │     │              │
│              │────<│  community_posts   │     │              │
│              │────<│  community_comments│     │              │
│              │────<│  community_post_likes│    │              │
│              │────<│  practice_submissions│    │              │
│              │────<│  contest_submissions│    │              │
│              │────<│  roadmap_user_progress│   │              │
│              │────<│  roadmap_certificates│   │              │
│              │────<│  user_cv_customizations│  │              │
│              │────<│  user_skills_assessments│ │              │
└──────────────┘     └──────────────────┘     └──────┬───────┘
                                                      │
                                                      │ 1:N
                                               ┌──────▼───────┐
                                               │   lessons     │
                                               │              │
                                               └──────┬───────┘
                                                      │
                                          ┌───────────┼───────────┐
                                          │           │           │
                                   ┌──────▼──┐  ┌────▼────┐  ┌───▼────────┐
                                   │quiz_    │  │practice_│  │lesson_     │
                                   │questions│  │tasks    │  │tests       │
                                   └────┬────┘  └─────────┘  └────────────┘
                                        │
                                   ┌────▼────┐
                                   │quiz_    │
                                   │options  │
                                   └─────────┘

┌──────────────┐     ┌──────────────────┐     ┌──────────────┐
│  vacancies   │────<│  vacancy_skills   │     │user_         │
│              │────<│  vacancy_         │     │applications  │
│              │────<│   requirements    │     │              │
│              │────<│  vacancy_pluses   │     └──────┬───────┘
│              │────<│  vacancy_         │            │
│              │     │   responsibilities│     ┌──────▼───────┐
└──────────────┘     └──────────────────┘     │vacancy_chats  │
                                               │              │
                                               └──────┬───────┘
                                                      │
                                               ┌──────▼───────┐
                                               │vacancy_      │
                                               │documents     │
                                               └──────────────┘

┌──────────────┐     ┌──────────────────┐
│roadmap_nodes │────<│roadmap_lessons    │
│              │────<│roadmap_quiz_      │
│              │────<│  questions        │
│              │────<│roadmap_user_      │
│              │     │  progress         │
│              │────<│roadmap_certificates│
└──────────────┘     └──────────────────┘

┌──────────────┐     ┌──────────────────┐
│course_exams  │     │course_skills      │
└──────────────┘     └──────────────────┘

┌──────────────┐
│   sessions   │
└──────────────┘
```

### 2.2 Таблицы и индексы

| Таблица | Ключевые индексы | Объём (ожид.) |
|---------|------------------|---------------|
| users | email (unique), role, is_blocked | 10K+ |
| courses | category, level | 100+ |
| lessons | course_id, order_num | 500+ |
| quiz_questions | lesson_id | 2K+ |
| quiz_options | question_id | 10K+ |
| course_exams | course_id | 100+ |
| certificates | user_id, course_id, cert_hash (unique) | 5K+ |
| vacancies | owner_id, type, location | 500+ |
| user_applications | user_id, vacancy_id (unique composite) | 5K+ |
| vacancy_chats | application_id | 20K+ |
| community_posts | user_id, views_count | 5K+ |
| community_comments | post_id, user_id | 20K+ |
| community_post_likes | post_id, user_id (unique composite) | 50K+ |
| chat_messages | user_id, created_at | 100K+ |
| interviews | user_id, type, status | 10K+ |
| notifications | user_id, is_read | 50K+ |
| roadmap_nodes | roadmap_title | 200+ |
| roadmap_user_progress | user_id, node_id (unique composite) | 10K+ |
| practice_submissions | user_id, task_id | 50K+ |
| sessions | user_id, expires_at | 1K+ |

---

## 3. API интеграции

### 3.1 Gemini AI API

**Базовый URL:** `https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent`

**Конфигурация:**
- Модель: `gemini-2.5-flash`
- Пул ключей: до 6 (ротация при ошибках)
- Таймаут: 60 секунд
- Retry: до 3 попыток при 429/503
- Delay между retry: 500ms

**Запрос (AI Tutor):**
```json
{
  "contents": [
    {"role": "user", "parts": [{"text": "System instruction + context"}]},
    {"role": "model", "parts": [{"text": "Acknowledgment"}]},
    {"role": "user", "parts": [{"text": "...history..."}]},
    {"role": "user", "parts": [{"text": "User message"}]}
  ],
  "generationConfig": {
    "temperature": 0.7,
    "maxOutputTokens": 2048
  }
}
```

**Запрос (AI Interview — генерация вопросов):**
```
System: Generate 5 {type} interview questions at {difficulty} level.
Format: JSON array of questions.
```

**Запрос (AI Interview — оценка ответа):**
```
System: Evaluate this answer on a scale of 0-100.
Provide: score, strengths, improvements, feedback.
```

### 3.2 Judge0 CE API

**Базовый URL:** `{JUDGE0_URL}/submissions?base64_encoded=false&wait=true`

**Конфигурация:**
- Таймаут: 30 секунд
- Режим: синхронный (wait=true)

**Запрос:**
```json
{
  "source_code": "print('Hello')",
  "language_id": 63,
  "stdin": "",
  "expected_output": "Hello"
}
```

**Ответ:**
```json
{
  "stdout": "Hello",
  "stderr": null,
  "time": "0.023",
  "memory": 8432,
  "status": {"id": 3, "description": "Accepted"}
}
```

### 3.3 Google OAuth2

**Эндпоинт верификации:** `https://oauth2.googleapis.com/tokeninfo?id_token={token}`

**Ответ:**
```json
{
  "email": "user@gmail.com",
  "name": "John Doe",
  "picture": "https://..."
}
```

### 3.4 Google reCAPTCHA

**Эндпоинт:** `https://www.google.com/recaptcha/api/siteverify`

**Запрос:**
```
POST /recaptcha/api/siteverify
  secret={SECRET_KEY}
  response={TOKEN}
```

---

## 4. Структура файлов

### 4.1 Корневая структура

```
Codemaster/
├── app/
│   ├── Helpers/helpers.php          # Глобальные хелперы
│   ├── Http/
│   │   ├── Controllers/             # 24 контроллера
│   │   │   ├── Admin/AdminController.php
│   │   │   └── Auth/                # 6 auth контроллеров
│   │   └── Middleware/              # 2 кастомных middleware
│   ├── Models/                      # 43 Eloquent модели
│   ├── Providers/                   # AppServiceProvider
│   └── Services/                    # 5 сервисов
├── config/
│   └── services.php                 # Gemini, Judge0, OAuth, reCAPTCHA
├── database/
│   ├── migrations/                  # 44 миграции
│   └── seeders/                     # 2 сидера (AdminUserSeeder, DatabaseSeeder)
├── lang/
│   ├── ru.php, en.php, tg.php      # Переводы
│   └── ru.json, tg.json
├── resources/
│   ├── css/app.css                  # Tailwind CSS v4
│   ├── js/app.js, bootstrap.js      # Axios + CSRF
│   └── views/                       # 50+ Blade шаблонов
│       ├── layouts/                 # app, admin, guest
│       ├── components/              # ai-tutor, header, footer, notification, preloader
│       ├── admin/                   # Админ-панель
│       ├── auth/                    # login, register
│       ├── courses/                 # index, show, exam, exam-result
│       ├── vacancies/               # index, show, chat
│       ├── profile/                 # index, show
│       ├── certificates/            # show, print
│       ├── community/               # index, show
│       ├── contests/                # index, show
│       ├── interview/               # index, room, result
│       ├── ratings/                 # index
│       ├── roadmaps/                # index, show
│       └── static/                  # about, contacts, privacy, terms
├── routes/
│   └── web.php                      # Все маршруты
├── composer.json                    # Laravel 12, PHP 8.2+
├── package.json                     # Tailwind 4, Vite 7, Alpine.js
└── vite.config.js                   # Vite конфигурация
```

### 4.2 Маршрутизация

**Публичные маршруты:**
- GET / — Главная
- GET /login, POST /login — Вход
- POST /logout — Выход
- POST /google-login — Google OAuth
- GET /register, POST /register — Регистрация
- GET /forgot-password, POST /forgot-password — Сброс пароля
- GET /reset-password/{token}, POST /reset-password — Новый пароль
- GET /about, /contacts, /terms, /privacy — Статические страницы

**Защищённые маршруты (auth):**
- GET /dashboard — Дашборд
- GET /courses, GET /courses/{id} — Курсы
- POST /courses/complete-lesson — Завершение урока
- GET /courses/{id}/exam, POST /courses/{id}/exam/submit — Экзамен
- GET /vacancies, GET /vacancies/{id}, POST /vacancies/{id}/apply — Вакансии
- GET /vacancy-chat/{applicationId}, POST /vacancy-chat — Чат
- GET /profile, PUT /profile, /profile/{userId} — Профиль
- GET /certificate/{hash}, GET /certificate/{hash}/download — Сертификаты
- GET /ratings — Рейтинги
- GET /roadmaps, GET /roadmap/{title}, POST /roadmap/complete-node — Дорожные карты
- GET /community, POST /community, /community/{id} — Сообщество
- POST /community/comment, POST /community/{id}/like — Комментарии/лайки
- GET /interview, POST /interview, /interview/{id} — Собеседования
- GET /contests, GET /contest/{id}, POST /contest/submit — Конкурсы
- POST /notifications/mark-read — Уведомления
- POST /practice/submit — Практика
- POST /ai/chat, GET /ai/history, POST /ai/clear — AI Tutor

**Админ маршруты (auth + admin, prefix /admin):**
- GET /admin — Дашборд
- CRUD: users, courses, lessons, vacancies
- POST /admin/users/{id}/toggle-block — Блокировка
- POST /admin/users/{id}/role — Смена роли
- DELETE /admin/notifications/{id} — Удаление уведомления

---

## 5. Конфигурация

### 5.1 Переменные окружения (.env)

```env
# App
APP_NAME=CodeMaster
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=codemaster
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=1440

# Gemini AI
GEMINI_API_KEYS=key1,key2,key3
GEMINI_MODEL=gemini-2.5-flash

# Judge0
JUDGE0_URL=https://judge0-ce.p.rapidapi.com
JUDGE0_API_TOKEN=

# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_SECRET=

# reCAPTCHA
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=
```

### 5.2 Конфигурация сервисов (config/services.php)

```php
'gemini' => [
    'keys' => explode(',', env('GEMINI_API_KEYS', '')),
    'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
],
'judge0' => [
    'url' => env('JUDGE0_URL', 'https://judge0-ce.p.rapidapi.com'),
    'token' => env('JUDGE0_API_TOKEN', ''),
],
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'secret' => env('GOOGLE_SECRET'),
],
'recaptcha' => [
    'site_key' => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
],
```

---

## 6. Фронтенд

### 6.1 Технологии

- **CSS:** Tailwind CSS 4 (CDN + Vite)
- **JS:** Alpine.js 3.x (CDN)
- **Иконки:** Font Awesome 6.5.1
- **Шрифты:** Inter, JetBrains Mono (Google Fonts)
- **Сборка:** Vite 7 + laravel-vite-plugin

### 6.2 Темы

- Тёмная тема (по умолчанию, neon-стиль)
- Светлая тема
- Управление через localStorage + data-атрибуты

### 6.3 Компоненты

- **Header:** Навигация, переключатель языков, уведомления, профиль
- **Footer:** Ссылки, копирайт
- **AI Tutor:** Плавающий чат-виджет
- **Notification:** Flash-уведомления
- **Preloader:** Анимация загрузки

### 6.4 Страницы

| Страница | Layout | Описание |
|----------|--------|----------|
| home | guest | Лендинг с анимированной hero-секцией |
| dashboard | app | Дашборд с статистикой |
| courses/index | app | Каталог курсов с фильтрами |
| courses/show | app | Страница курса |
| courses/exam | app | Экзамен |
| courses/exam-result | app | Результат экзамена |
| vacancies/index | app | Каталог вакансий |
| vacancies/show | app | Страница вакансии |
| vacancies/chat | app | Чат по вакансии |
| profile/index | app | Свой профиль |
| profile/show | app | Публичный профиль |
| certificates/show | app | Сертификат |
| certificates/print | app | Печать сертификата |
| community/index | app | Список постов |
| community/show | app | Пост с комментариями |
| contests/index | app | Конкурсы |
| contests/show | app | Задание конкурса |
| interview/index | app | Собеседования |
| interview/room | app | Комната собеседования |
| interview/result | app | Результат |
| ratings/index | app | Лидерборд |
| roadmaps/index | app | Дорожные карты |
| roadmaps/show | app | Интерактивная карта |
| admin/dashboard | admin | Дашборд админки |
| admin/users | admin | Управление пользователями |
| admin/courses | admin | Управление курсами |
| admin/lessons | admin | Управление уроками |
| admin/vacancies | admin | Управление вакансиями |
| auth/login | guest | Вход |
| auth/register | guest | Регистрация |

---

## 7. Тестирование

### 7.1 Типы тестов

| Тип | Фреймворк | Покрытие |
|-----|-----------|----------|
| Unit | PHPUnit 11 | Модели, сервисы |
| Feature | PHPUnit 11 | HTTP-запросы, бизнес-логика |
| Browser | Laravel Dusk | E2E (опционально) |

### 7.2 Конфигурация

- БД для тестов: SQLite in-memory
- Фабрики: UserFactory
- Seeder: DatabaseSeeder (полная сидеризация)

### 7.3 Ключевые сценарии тестов

**Auth:**
- Регистрация нового пользователя
- Вход с верными данными
- Вход с неверным паролем
- Блокировка после 5 попыток
- Google OAuth вход

**Education:**
- Завершение урока и пересчёт прогресса
- Сдача экзамена и получение сертификата
- Прохождение дорожной карты

**Career:**
- Отклик на вакансию
- Запрет дубликатов
- Отправка сообщения в чат

**AI:**
- Отправка сообщения AI Tutor
- Создание собеседования
- Оценка ответа

**Community:**
- Создание поста
- Добавление комментария
- Toggle лайка

---

## 8. Деплой

### 8.1 Требования

- PHP 8.2+ (extensions: mbstring, xml, curl, zip, bcmath, gd)
- MySQL 8.0+ или SQLite 3.35+
- Node.js 18+ (для сборки фронтенда)
- Composer 2+
- Web-сервер: Nginx или Apache

### 8.2 Шаги деплоя

```bash
# 1. Клонирование
git clone repo-url
cd codemaster

# 2. Зависимости
composer install
npm install

# 3. Конфигурация
cp .env.example .env
php artisan key:generate
# Настроить .env (БД, API ключи)

# 4. База данных
php artisan migrate --seed

# 5. Сборка фронтенда
npm run build

# 6. Кэширование
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Запуск
php artisan serve
# или настройка Nginx/Apache
```

### 8.3 Composer скрипты

```json
{
  "setup": ["cp .env.example .env", "composer install", "php artisan key:generate", "npm install"],
  "dev": ["concurrently \"php artisan serve\" \"npm run dev\" \"php artisan queue:work\" \"php artisan pail\""]
}
```

---

## 9. Производительность

### 9.1 Оптимизации

| Область | Оптимизация |
|---------|-------------|
| Запросы | Eager loading (with()), lazy loading |
| Кэш | Cache::remember() для частых запросов |
| БД | Индексы на внешние ключи |
| Фронтенд | Vite code splitting, CDN для зависимостей |
| Изображения | Ленивая загрузка (loading="lazy") |

### 9.2 Мониторинг

- Логирование: daily + stderr
- Метрики: Laravel Telescope (опционально)
- Ошибки: Sentry (опционально)

---

## 10. Масштабируемость

### 10.1 Горизонтальное масштабирование

- Stateless приложение (сессии в БД/Redis)
- Балансировщик нагрузки (Nginx)
- Выделенный DB сервер

### 10.2 Вертикальное масштабирование

- Opcode кэш (OPcache)
- Redis для кэша и сессий
- Connection pooling

### 10.3 Ограничения

- AI-запросы: 60 секунд таймаут
- Judge0: 30 секунд таймаут
- Файлы: ограничение по размеру (2MB для аватаров)
- Brute-force: 5 попыток → 15 минут
