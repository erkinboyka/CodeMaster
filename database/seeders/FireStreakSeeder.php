<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class FireStreakSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@codemaster.com')->first();
        if ($admin) {
            $admin->update([
                'streak_count' => 365,
                'longest_streak' => 365,
                'last_active_date' => now()->toDateString(),
            ]);
        }

        $testUser = User::where('email', 'user@example.com')->first();
        if ($testUser) {
            $testUser->update([
                'streak_count' => 42,
                'longest_streak' => 67,
                'last_active_date' => now()->toDateString(),
            ]);
        }
    }
}
