<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserCourseProgress;
use App\Models\UserLessonProgress;
use Illuminate\Database\Seeder;

class ProgressSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'seeker')->get();
        $courses = Course::all();

        if ($users->isEmpty() || $courses->isEmpty()) return;

        foreach ($users as $user) {
            $enrolledCourses = $courses->random(min(rand(3, 8), $courses->count()));
            foreach ($enrolledCourses as $course) {
                $progress = rand(5, 100);
                $completed = $progress >= 100;
                $startedDaysAgo = rand(10, 90);

                UserCourseProgress::updateOrCreate(
                    ['user_id' => $user->id, 'course_id' => $course->id],
                    [
                        'progress' => $progress,
                        'completed' => $completed,
                        'started_at' => now()->subDays($startedDaysAgo),
                        'completed_at' => $completed ? now()->subDays(rand(1, max(1, $startedDaysAgo - 1))) : null,
                    ]
                );

                $lessons = Lesson::where('course_id', $course->id)->orderBy('order_num')->get();
                $lessonsToComplete = (int) ceil($lessons->count() * ($progress / 100));

                for ($i = 0; $i < $lessonsToComplete && $i < $lessons->count(); $i++) {
                    UserLessonProgress::updateOrCreate(
                        ['user_id' => $user->id, 'lesson_id' => $lessons[$i]->id],
                        [
                            'completed' => true,
                            'completed_at' => now()->subDays(rand(1, $startedDaysAgo)),
                        ]
                    );
                }
            }
        }
    }
}
