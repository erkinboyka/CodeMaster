<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::inRandomOrder()->take(6)->get();

        $reviews = [
            ['rating' => 5, 'text' => 'Отличная платформа! Прошёл курс по React и сразу нашёл работу. AI-помощник очень помог с практикой.'],
            ['rating' => 5, 'text' => 'Курсы на высшем уровне. Особенно понравилась интерактивная практика и Peer Interview. Рекомендую всем!'],
            ['rating' => 4, 'text' => 'Хорошая платформа для начинающих. Контент понятный, задания интересные. Хотелось бы больше курсов по DevOps.'],
            ['rating' => 5, 'text' => 'Благодаря CodeMaster получил оффер как Junior Developer. Система геймификации мотивирует не бросать обучение.'],
            ['rating' => 4, 'text' => 'Нравится сообщество и рейтинговая система. Задания по практике реалистичные — как на настоящей работе.'],
            ['rating' => 5, 'text' => 'Лучшая IT-платформа! Сертификаты действительно ценятся работодателями. Уже третий курс прохожу.'],
        ];

        foreach ($users as $i => $user) {
            Review::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'rating' => $reviews[$i]['rating'],
                    'text' => $reviews[$i]['text'],
                    'is_public' => true,
                ]
            );
        }
    }
}
