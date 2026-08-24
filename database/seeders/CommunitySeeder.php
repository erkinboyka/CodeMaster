<?php

namespace Database\Seeders;

use App\Models\CommunityPost;
use App\Models\CommunityComment;
use App\Models\CommunityPostLike;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) return;

        $posts = [
            ['title' => 'Как изучать алгоритмы в 2026 году?', 'content' => 'Привет всем! Хочу начать изучать алгоритмы и структуры данных. Какие ресурсы вы рекомендуете? LeetCode, HackerRank, или что-то другое? Какой порядок изучения тем?\n\nЯ уже знаю Python и немного JavaScript, но с алгоритмами дела обстоят плохо. Хочу подготовиться к техническим собеседованиям.', 'likes_count' => 24, 'views_count' => 156, 'comments_count' => 8],
            ['title' => 'Опыт работы с Laravel 12', 'content' => 'Недавно обновился на Laravel 12 и хочу поделиться впечатлениями. Новые фичи очень впечатляющие. Особенно понравилась новая система маршрутизации и улучшенная работа с очередями.\n\nВот что изменилось:\n1. Улучшенная производительность\n2. Новые хелперы для валидации\n3. Обновлённый Artisan\n4. Лучшая интеграция с PHP 8.3\n\nКто уже попробовал? Какие ваши впечатления?', 'likes_count' => 31, 'views_count' => 203, 'comments_count' => 12],
            ['title' => 'Советы по прохождению технического собеседования', 'content' => 'Поделитесь советами по подготовке к техническому собеседованию. Какие темы чаще всего спрашивают? Как лучше готовиться к алгоритмическим задачам?\n\nВот мой список тем для подготовки:\n- Arrays и Strings\n- Trees и Graphs\n- Dynamic Programming\n- System Design\n- Behavioral Questions\n\nЧто бы вы добавили?', 'likes_count' => 45, 'views_count' => 312, 'comments_count' => 15],
            ['title' => 'React vs Vue.js в 2026', 'content' => 'Стоит ли переходить с Vue на React? Или Vue всё ещё актуален? Работаю с Vue уже 3 года, но всё больше вакансий требуют React. Что думаете?\n\nПлюсы Vue:\n- Простой синтаксис\n- Отличная документация\n- Малый размер\n\nПлюсы React:\n- Большое сообщество\n- Много вакансий\n- Много библиотек', 'likes_count' => 42, 'views_count' => 280, 'comments_count' => 20],
            ['title' => 'Мой путь из новичка в разработку', 'content' => 'Хочу рассказать свою историю. Полгода назад я не знал что такое HTML. Прошёл курсы на CodeMaster, делал практики, участвовал в контестах.\n\nПуть:\n1. HTML + CSS (2 месяца)\n2. JavaScript (3 месяца)\n3. React (2 месяца)\n4. Портфолио (1 месяц)\n\nТеперь работаю junior frontend developer! Спасибо платформе за отличный старт. Зарплата 15000 TJS, удалёнка. Мечты сбываются!', 'likes_count' => 67, 'views_count' => 521, 'comments_count' => 25],
            ['title' => 'Лучшие практики работы с Docker', 'content' => 'Поделюсь своими советами по Docker, которые я собрал за 3 года работы:\n\n1. Всегда используй multi-stage builds\n2. Минимизируйте количество слоёв\n3. Используй .dockerignore\n4. Не запускайте от root\n5. Используй health checks\n6. Pin версии образов\n\nЧто бы вы добавили из своего опыта?', 'likes_count' => 38, 'views_count' => 198, 'comments_count' => 10],
            ['title' => 'Как подготовиться к собеседованию на Junior позицию', 'content' => 'Недавно прошёл 5 собеседований и получил 3 оффера. Делюсь опытом подготовки.\n\nПодготовка:\n- 2 месяца LeetCode (Easy + Medium)\n- Изучил основы system design\n- Подготовил рассказ о себе\n- Сделал 3 проекта для портфолио\n\nЧастые вопросы:\n- Расскажите о себе\n- Почему наша компания?\n- Какой самый сложный проект вы делали?\n- Как вы решаете конфликты в команде?', 'likes_count' => 52, 'views_count' => 389, 'comments_count' => 18],
            ['title' => 'TypeScript: почему стоит начать использовать', 'content' => 'После года работы с TypeScript не понимаю, как можно писать на чистом JavaScript в 2026 году.\n\nПреимущества:\n1. Статическая типизация\n2. Лучшая поддержка IDE\n3. Меньше багов в production\n4. Легче рефакторить\n5. Документация через типы\n\nНачните с простых типов и постепенно добавляйте strict mode.', 'likes_count' => 29, 'views_count' => 176, 'comments_count' => 7],
            ['title' => 'Roadmap для Backend разработчика 2026', 'content' => 'Составил актуальный roadmap для backend разработчика на 2026 год.\n\nНачальный уровень:\n- Один язык (Python/Go/Node.js/Java)\n- SQL основы\n- HTTP/REST\n- Git\n\nСредний уровень:\n- Фреймворк\n- Docker\n- CI/CD\n- Тестирование\n\nПродвинутый:\n- Микросервисы\n- Kubernetes\n- System Design\n- Мониторинг\n\nЧто бы вы изменили?', 'likes_count' => 41, 'views_count' => 267, 'comments_count' => 14],
            ['title' => 'Портфолио для Junior: что должно быть', 'content' => 'Создаю портфолио для устройства на позицию Junior Frontend Developer. Какие проекты стоит добавить?\n\nМои текущие проекты:\n1. TODO приложение (React)\n2. Погодное приложение (API)\n3. Лендинг для ресторана\n\nЧто ещё добавить? Может быть Open Source вклад?', 'likes_count' => 33, 'views_count' => 245, 'comments_count' => 11],
        ];

        foreach ($posts as $i => $postData) {
            $user = $users[$i % $users->count()];
            $post = CommunityPost::create([
                'user_id' => $user->id,
                'title' => $postData['title'],
                'content' => $postData['content'],
                'likes_count' => $postData['likes_count'],
                'views_count' => $postData['views_count'],
                'comments_count' => $postData['comments_count'],
            ]);

            $commentTexts = [
                'Отличный пост! Очень помогло.',
                'Согласен на 100%. Сам так делаю.',
                'А что насчёт альтернатив?',
                'Спасибо за подробное объяснение!',
                'Буду пробовать, отпишусь о результатах.',
                'А какой уровень знаний нужен для начала?',
                'Полезная информация, сохранил себе.',
                'Я недавно начинал, это確實 очень помогает.',
                'Можно подробнее про этот пункт?',
                'Отличные советы, спасибо автору!',
                'Как думаешь, это актуально в 2026?',
                'Лучший пост на эту тему, что я видел.',
            ];

            $commentCount = min($postData['comments_count'], 5);
            for ($c = 0; $c < $commentCount; $c++) {
                $commentUser = $users[array_rand($users->toArray())];
                CommunityComment::create([
                    'post_id' => $post->id,
                    'user_id' => $commentUser->id,
                    'content' => $commentTexts[array_rand($commentTexts)],
                ]);
            }

            $likeCount = min($postData['likes_count'], 8);
            $likerIds = $users->random(min($likeCount, $users->count()))->pluck('id');
            foreach ($likerIds as $likerId) {
                CommunityPostLike::create([
                    'post_id' => $post->id,
                    'user_id' => $likerId,
                ]);
            }
        }
    }
}
