# CodeMaster — Software Requirements Specification (SRS)

**Версия:** 1.0  
**Дата:** 2026-08-01  
**Статус:** Финальный

---

## 1. Введение

### 1.1 Назначение

CodeMaster — это комплексная IT-платформа образования и трудоустройства, объединяющая обучение (курсы, дорожные карты), практику (выполнение кода через Judge0), карьеру (вакансии с чатом) и AI-инструменты (репетитор 24/7, симулятор собеседований) в единой экосистеме.

### 1.2 Проблема

- Развитие IT-образования в регионе (Таджикистан) с ограниченными локализованными платформами
- Разрыв между обучением и трудоустройством
- Отсутствие единой экосистемы: обучение → практика → сертификат → трудоустройство

### 1.3 Целевая аудитория

| Роль | Описание |
|------|----------|
| Seeker | Начинающий/опытный разработчик, ищущий обучение и работу |
| Recruiter | Работодатель, публикующий вакансии и общающийся с кандидатами |
| Admin | Администратор платформы, управляющий контентом и пользователями |

### 1.4 Языки интерфейса

- Русский (ru) — по умолчанию
- Английский (en)
- Таджикский (tg)

---

## 2. Системные сущности

### 2.1 User (Пользователь)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| name | string | Имя |
| email | string unique | Email |
| password | string nullable | Хеш пароля (bcrypt) |
| role | enum(seeker, recruiter, admin) | Роль |
| avatar | string nullable | URL аватара |
| title | string nullable | Должность/специализация |
| location | string nullable | Локация |
| bio | text nullable | Биография |
| country_code | string nullable | Код страны |
| country_name | string nullable | Название страны |
| google_locale | string nullable | Локаль Google OAuth |
| ai_coins | integer default 0 | Баланс AI-монет |
| is_verified | boolean default false | Верифицирован |
| is_blocked | boolean default false | Заблокирован |
| failed_login_attempts | integer default 0 | Неудачные попытки входа |
| locked_until | timestamp nullable | Блокировка до |
| last_login | timestamp nullable | Последний вход |

**Связи:** skills, experience, education, portfolio, courses, certificates, activities, notifications, applications, chatMessages, reviews, aiWallet, courseProgress, lessonProgress, roadmapProgress, roadmapCertificates, submissions, interviewSessions

### 2.2 Course (Курс)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| title | string | Название |
| instructor | string | Инструктор |
| description | text | Описание |
| category | enum(frontend, backend, design, devops, other) | Категория |
| level | enum(Начальный, Средний, Продвинутый) | Уровень |
| image_url | string nullable | URL изображения |
| materials_title | string nullable | Название материалов |
| materials_url | string nullable | URL материалов |

**Связи:** lessons, courseSkills, userProgress, exams, certificates, roadmapNodes

### 2.3 Lesson (Урок)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| course_id | bigint FK | Курс |
| title | string | Название |
| type | enum(video, article, quiz) | Тип контента |
| content | text nullable | Содержание |
| video_url | string nullable | URL видео |
| materials_title | string nullable | Название материалов |
| materials_url | string nullable | URL материалов |
| completed | boolean default false | Завершён (глобально) |
| order_num | integer default 0 | Порядок |

**Связи:** course, quizQuestions, practiceTasks, userProgress, lessonTests

### 2.4 QuizQuestion (Вопрос теста)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| lesson_id | bigint FK | Урок |
| question_text | text | Текст вопроса |
| correct_option | string nullable | Правильный вариант |
| correct_options | json nullable | Правильные варианты (множественный выбор) |
| hint | string nullable | Подсказка |

**Связи:** lesson, options

### 2.5 QuizOption (Вариант ответа)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| question_id | bigint FK | Вопрос |
| option_text | string | Текст варианта |
| option_order | integer | Порядок |

### 2.6 CourseExam (Экзамен курса)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| course_id | bigint FK | Курс |
| exam_json | json | Вопросы экзамена |
| time_limit_minutes | integer | Время (минуты) |
| pass_percent | integer default 70 | Проходной балл |
| shuffle_questions | boolean default true | Перемешивать вопросы |
| shuffle_options | boolean default true | Перемешивать варианты |

### 2.7 Certificate (Сертификат)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| course_id | bigint FK | Курс |
| cert_hash | string unique | Уникальный хеш (40 символов) |
| certificate_name | string | Название сертификата |
| issuer | string | Организация |
| issue_date | date | Дата выдачи |
| certificate_url | string nullable | URL |

### 2.8 Vacancy (Вакансия)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| title | string | Должность |
| company | string | Компания |
| location | string | Локация |
| type | enum(remote, office, hybrid) | Тип работы |
| salary_min | decimal nullable | Мин. зарплата |
| salary_max | decimal nullable | Макс. зарплата |
| salary_currency | string default 'TJS' | Валюта |
| description | text | Описание |
| company_description | text nullable | О компании |
| verified | boolean default false | Верифицирована |
| owner_id | bigint nullable FK | Владелец (recruiter) |

**Связи:** vacancySkills, requirements, pluses, responsibilities, applications

### 2.9 UserApplication (Заявка на вакансию)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Кандидат |
| vacancy_id | bigint FK | Вакансия |
| status | string default 'applied' | Статус |
| employment_status | string nullable | Статус трудоустройства |
| applied_at | timestamp | Дата отклика |

**Связи:** user, vacancy, chats, documents

### 2.10 VacancyChat (Чат по вакансии)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| application_id | bigint FK | Заявка |
| sender_id | bigint FK | Отправитель |
| message_text | text | Сообщение |

### 2.11 VacancyDocument (Документ вакансии)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| application_id | bigint FK | Заявка |
| uploader_id | bigint FK | Загрузчик |
| file_path | string | Путь к файлу |
| original_name | string | Оригинальное имя |
| mime_type | string | MIME-тип |
| size_bytes | integer | Размер |

### 2.12 RoadmapNode (Узел дорожной карты)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| roadmap_title | string | Название дорожной карты |
| title | string | Название узла |
| description | text nullable | Описание |
| course_id | bigint nullable FK | Связанный курс |
| x | integer | Координата X |
| y | integer | Координата Y |
| deps | json | Зависимости (массив ID) |
| materials | json | Материалы |
| is_exam | boolean default false | Является экзаменом |

**Связи:** roadmapLessons, quizQuestions, userProgress, certificates, course

### 2.13 RoadmapUserProgress (Прогресс дорожной карты)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| node_id | bigint FK | Узел |

### 2.14 RoadmapCertificate (Сертификат дорожной карты)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| node_id | bigint FK | Узел |
| cert_hash | string | Хеш |
| issued_at | timestamp | Дата выдачи |

### 2.15 CommunityPost (Пост сообщества)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Автор |
| title | string | Заголовок |
| content | text | Содержание |
| likes_count | integer default 0 | Лайки |
| views_count | integer default 0 | Просмотры |

**Связи:** user, comments, likes

### 2.16 CommunityComment (Комментарий)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| post_id | bigint FK | Пост |
| user_id | bigint FK | Автор |
| content | text | Содержание |

### 2.17 CommunityPostLike (Лайк)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| post_id | bigint FK | Пост |
| user_id | bigint FK | Пользователь |

### 2.18 ChatMessage (Сообщение AI-чата)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| sender | string | Роль (user/assistant) |
| message_text | text | Сообщение |
| sent_at | timestamp | Время отправки |

### 2.19 Interview (Собеседование)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| title | string | Название |
| type | enum(technical, behavioral, coding, system_design) | Тип |
| difficulty | enum(easy, medium, hard) | Сложность |
| status | string default 'in_progress' | Статус |
| score | integer nullable | Итоговый балл |
| feedback | text nullable | Фидбек |
| started_at | timestamp nullable | Начало |
| completed_at | timestamp nullable | Завершение |

### 2.20 PracticeSubmission (Отправка практики)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| task_id | bigint FK | Задание |
| code | text | Код |
| passed | boolean | Пройдено |
| stdout | text nullable | Вывод |
| stderr | text nullable | Ошибки |
| details_json | json nullable | Детали |

### 2.21 ContestSubmission (Отправка конкурса)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| contest_id | bigint nullable FK | Конкурс |
| task_id | bigint nullable FK | Задание |
| code | text | Код |
| status | string | Статус |

### 2.22 Notification (Уведомление)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| message | text | Сообщение |
| notification_time | timestamp | Время |
| is_read | boolean default false | Прочитано |

### 2.23 UserActivity (Активность)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| activity_type | string | Тип активности |
| activity_text | text | Описание |
| activity_time | timestamp | Время |

### 2.24 UserSkill (Навык пользователя)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| skill_name | string | Название навыка |
| skill_level | string | Уровень |
| category | string nullable | Категория |
| endorsements | integer default 0 | Рекомендации |
| is_verified | boolean default false | Верифицирован |

### 2.25 UserExperience (Опыт работы)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| position | string | Должность |
| company | string | Компания |
| start_date | date | Начало |
| end_date | date nullable | Окончание |
| description | text nullable | Описание |

### 2.26 UserEducation (Образование)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| degree | string | Степень/квалификация |
| institution | string | Учебное заведение |
| start_date | date | Начало |
| end_date | date nullable | Окончание |
| description | text nullable | Описание |

### 2.27 UserPortfolio (Портфолио)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| title | string | Название проекта |
| category | string | Категория |
| image_url | string nullable | URL изображения |
| github_url | string nullable | GitHub URL |

### 2.28 UserAiWallet (AI кошелёк)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| balance | integer default 0 | Баланс монет |

### 2.29 PlatformReview (Отзыв)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| user_id | bigint FK | Пользователь |
| rating | integer | Оценка (1-5) |
| comment | text | Комментарий |

### 2.30 LessonPracticeTask (Практическое задание)

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | Идентификатор |
| lesson_id | bigint FK | Урок |
| language | string | Язык программирования |
| title | string | Название |
| prompt | text | Условие |
| starter_code | text nullable | Начальный код |
| tests_json | json | Тесты |
| is_required | boolean default false | Обязательное |

### 2.31 Утилитарные сущности

- **LessonTest** — тесты уроков (lesson_id, test_json)
- **CourseSkill** — навыки курса (course_id, skill_name, skill_level)
- **VacancySkill** — навыки вакансии (vacancy_id, skill_name)
- **VacancyRequirement** — требования вакансии (vacancy_id, requirement_text)
- **VacancyPluse** — плюсы вакансии (vacancy_id, plus_text)
- **VacancyResponsibility** — обязанности вакансии (vacancy_id, responsibility_text)
- **RoadmapLesson** — уроки узла (node_id, title, video_url, description, materials, order_index)
- **RoadmapQuizQuestion** — вопросы узла (node_id, question, options, correct_answer)
- **UserCourseProgress** — прогресс курса (user_id, course_id, progress, completed, started_at, completed_at)
- **UserLessonProgress** — прогресс урока (user_id, lesson_id, completed, completed_at)
- **UserCvCustomization** — настройки CV (user_id, settings_json)
- **UserSkillsAssessment** — оценка навыков (user_id, skill_name, state_json)
- **Session** — сессии (id string, user_id, session_data, expires_at)

---

## 3. Роли и права доступа

### 3.1 Seeker (по умолчанию)

| Право | Доступ |
|-------|--------|
| Просмотр курсов | ✅ |
| Прохождение уроков | ✅ |
| Сдача экзаменов | ✅ |
| Получение сертификатов | ✅ |
| Прохождение дорожных карт | ✅ |
| Выполнение кода (Judge0) | ✅ |
| Просмотр вакансий | ✅ |
| Отклик на вакансии | ✅ |
| Чат по вакансии | ✅ |
| AI-репетитор | ✅ |
| AI-собеседование | ✅ |
| Сообщество (посты, комментарии, лайки) | ✅ |
| Профиль (CV/Resume) | ✅ |
| Рейтинги | ✅ |
| Управление контентом | ❌ |
| Админ-панель | ❌ |

### 3.2 Recruiter

| Право | Доступ |
|-------|--------|
| Все права Seeker | ✅ |
| Публикация вакансий | ✅ |
| Общение с кандидатами | ✅ |
| Управление курсами | ❌ |
| Админ-панель | ❌ |

### 3.3 Admin

| Право | Доступ |
|-------|--------|
| Все права Seeker + Recruiter | ✅ |
| Управление пользователями (CRUD, блокировка) | ✅ |
| Управление курсами (CRUD) | ✅ |
| Управление уроками (CRUD) | ✅ |
| Управление вакансиями (CRUD) | ✅ |
| Управление уведомлениями | ✅ |
| Просмотр статистики | ✅ |
| Админ-панель (/admin) | ✅ |

---

## 4. Пользовательские сценарии

### 4.1 Регистрация

Гость → Главная → "Регистрация" → Форма (имя, email, пароль, роль, навыки) → reCAPTCHA → Отправка → Проверка уникальности email → Хеширование пароля (bcrypt 12) → Создание User → Перенаправление на /login → Вход

### 4.2 Авторизация

Гость → "Вход" → Форма (email, пароль) → Отправка → Проверка: существует? → Проверка: не заблокирован? → Проверка: пароль верный? → Проверка: не исчерпаны попытки? → Создание сессии (DB, 24ч) → Dashboard

### 4.3 Google OAuth

Главная → "Вход" → "Google" → Google OAuth flow → Получение ID token → Верификация через oauth2.googleapis.com/tokeninfo → Поиск/создание User → Создание сессии → Dashboard

### 4.4 Сброс пароля

"Забыли пароль?" → Форма (email) → Отправка → Email с ссылкой → Переход по ссылке → Форма нового пароля → Хеширование → Обновление БД → /login

### 4.5 Прохождение курса

Dashboard → "Курсы" → Каталог (поиск, фильтр, пагинация) → Выбор курса → Страница курса (описание, уроки, прогресс) → Выбор урока → Тип урока: video (просмотр), article (чтение), quiz (тесты) → Завершение урока → AJAX → Обновление прогресса

### 4.6 Сдача экзамена

Страница курса (100% прогресс) → "Сдать экзамен" → Shuffle вопросов/вариантов → Таймер → Ответы → Отправка → Проверка → Расчёт балла → Если >= 70% → Certificate (уникальный хеш) → Результат → Скачивание

### 4.7 Прохождение дорожной карты

"Дорожные карты" → Список → Выбор карты → Интерактивная визуализация (узлы x/y, зависимости) → Выбор доступного узла → Урок + Тест → Завершение → Обновление прогресса → Если 100% → Сертификат

### 4.8 Выполнение кода

Практика → Редактор кода → Выбор языка → Написание кода → "Выполнить" → Отправка на Judge0 → Компиляция + Запуск → Результат (stdout/stderr, время, память) → Автоматическая проверка test cases

### 4.9 Отклик на вакансию

Dashboard → "Вакансии" → Каталог (поиск, фильтр) → Выбор вакансии → "Откликнуться" → Проверка дубликата → Создание UserApplication (status: applied) → Создание VacancyChat → Общение → Загрузка документов

### 4.10 AI-репетитор

Плавающий виджет → Ввод вопроса → Сохранение в БД (role: user) → Контекст (20 последних сообщений) → Gemini API → Ответ → Сохранение (role: assistant) → Обрезка до 50 сообщений → Отображение

### 4.11 AI-собеседование

"Собеседования" → Выбор типа (technical/behavioral/coding/system_design) + уровня (easy/medium/hard) → "Начать" → Создание Interview → AI генерирует 5 вопросов → Вопрос → Ответ → AI оценивает (0-100) → Фидбек → ... (5 раз) → Итоговый балл (среднее) → Комплексный фидбек

### 4.12 Сообщество

"Сообщество" → Список постов → "Создать пост" → Форма (заголовок, контент) → Публикация → Просмотр → Комментарии → Лайки (toggle) → Просмотры

### 4.13 Профиль (CV)

"Профиль" → Редактирование: био, локация, аватар, навыки (уровни), портфолио, опыт, образование → Публичный профиль для других → Верификация навыков

### 4.14 Админ-панель

"/admin" → Дашборд (статистика) → Управление: пользователи (CRUD, блокировка, роль), курсы (CRUD), уроки (CRUD), вакансии (CRUD), уведомления

---

## 5. Внешние интеграции

### 5.1 Google Gemini AI

- Модель: gemini-2.5-flash
- Пул API-ключей (до 6) с ротацией
- Retry при 429/503 (до 3 попыток)
- AI Tutor: system instruction + история 20 сообщений → ответ
- AI Interview: генерация вопросов, оценка 0-100, фидбек

### 5.2 Judge0 CE

- Выполнение кода на 12 языках
- Языки: JavaScript (63), Python (71), Java (62), C++ (54), C (50), PHP (68), Ruby (73), Go (60), Rust (73), TypeScript (74), SQL (82), HTML/CSS (61)
- Режимы: practice (с тестами), SQL practice, fill-in-the-blank

### 5.3 Google OAuth2

- Верификация ID token через oauth2.googleapis.com/tokeninfo
- Создание/поиск пользователя по email

### 5.4 Google reCAPTCHA

- Верификация токена на сервере
- Graceful fallback (true если ключ не настроен)

### 5.5 UI Avatars

- Генерация аватара по имени пользователя
- URL-формат: https://ui-avatars.com/api/?name=...

---

## 6. Бизнес-правила

### 6.1 Аутентификация

- Блокировка: 5 неудачных попыток → 15 минут
- Хеширование: bcrypt, 12 раундов
- Сессии: DB-driven, 24 часа

### 6.2 Прогресс курса

- Формула: (завершённые_уроки / всего_уроков) × 100
- При 100%: курс помечается как завершённый
- Экзамен доступен только при 100% прогрессе

### 6.3 Сертификат

- Автоматическое создание при прохождении экзамена с проходным баллом
- Уникальный 40-символьный хеш
- Публичная ссылка: /certificate/{hash}

### 6.4 Отклик на вакансию

- Запрет дубликатов (одна вакансия — один отклик)
- Автоматическое создание чата

### 6.5 AI-интерфейс

- Максимум 5 вопросов за сессию
- Оценка: 0-100 за каждый ответ
- Итоговый балл: среднее арифметическое
- История: 20 сообщений контекст, обрезка до 50

### 6.6 Мультиязычность

- Приоритет: query param > session > cookie > config default
- Локали: ru, en, tg

### 6.7 Дорожные карты

- Узлы связаны зависимостями (deps JSON)
- Доступны только узлы с выполненными зависимостями
- Прогресс: % завершённых узлов

### 6.8 Сообщество

- Только автор может редактировать/удалять свой пост
- Toggle лайков (повторное нажатие убирает)
- Подсчёт просмотров

---

## 7. Ограничения системы

### 7.1 Технические

- PHP 8.2+
- Laravel 12
- MySQL/SQLite
- Node.js (для Vite сборки)

### 7.2 Внешние зависимости

- Google Gemini API (AI функции)
- Judge0 CE API (выполнение кода)
- Google OAuth2 (вход через Google)
- Google reCAPTCHA (защита от ботов)

### 7.3 Безопасность

- CSRF-защита (Laravel tokens)
- XSS-защита (экранирование Blade)
- SQL-инъекции (Eloquent ORM)
- Brute-force защита (5 попыток → 15 мин)
- Валидация данных на сервере

---

## 8. Состояния системы

### 8.1 Пользователь

- Гость → Авторизован → Заблокирован → Разблокирован

### 8.2 Заявка

- Applied → In Review → Accepted / Rejected

### 8.3 Собеседование

- In Progress → Completed

### 8.4 Код

- Pending → Running → Accepted / Wrong Answer / TLE / Runtime Error / Compilation Error
