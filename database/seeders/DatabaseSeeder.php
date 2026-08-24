<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            UserSeeder::class,
            FireStreakSeeder::class,
            CourseSeeder::class,
            CourseContentFullSeeder::class,
            CourseUpgradeSeeder::class,
            CourseSkillsSeeder::class,
            ProgressSeeder::class,
            ContestProblemsSeeder::class,
            AllRoadmapsSeeder::class,
            TagSeeder::class,
            CommunitySeeder::class,
            PlatformReviewSeeder::class,
            ReviewSeeder::class,
            InterviewPrepSeeder::class,
            ProblemSeeder::class,
        ]);
    }
}
