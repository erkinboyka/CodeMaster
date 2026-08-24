<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@codemaster.com'],
            [
                'name' => 'Admin CodeMaster',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'is_verified' => true,
                'ai_coins' => 999,
                'title' => 'Platform Administrator',
                'bio' => 'Администратор платформы CodeMaster. Управление контентом, пользователями и развитием платформы.',
                'location' => 'Душанбе, Таджикистан',
                'country_code' => 'TJ',
                'country_name' => 'Таджикистан',
                'github' => 'https://github.com/codemaster-admin',
                'linkedin' => 'https://linkedin.com/in/codemaster-admin',
                'website' => 'https://codemaster.tj',
                'xp' => 43500,
                'ai_tokens' => 10000,
                'level' => 30,
                'total_xp' => 43500,
                'streak_count' => 365,
                'longest_streak' => 365,
                'last_active_date' => now()->toDateString(),
            ]
        );
    }
}
