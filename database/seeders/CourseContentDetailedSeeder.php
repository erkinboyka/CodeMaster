<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseContentDetailedSeeder extends Seeder
{
    public function run(): void
    {
        $parts = [
            require __DIR__ . '/part1.php',
            require __DIR__ . '/part2.php',
            require __DIR__ . '/part3.php',
            require __DIR__ . '/part4.php',
        ];

        foreach ($parts as $partData) {
            foreach ($partData as $courseId => $data) {
                $this->seedCourseContent($courseId, $data);
            }
        }
    }

    private function seedCourseContent(int $courseId, array $data): void
    {
        if (isset($data['lessons'])) {
            foreach ($data['lessons'] as $title => $content) {
                DB::table('lessons')
                    ->where('course_id', $courseId)
                    ->where('title', $title)
                    ->update(['content' => $content]);
            }
        }

        if (isset($data['quizzes'])) {
            foreach ($data['quizzes'] as $lessonTitle => $quizzes) {
                $this->seedQuizzes($courseId, $lessonTitle, $quizzes);
            }
        }

        if (isset($data['practice'])) {
            foreach ($data['practice'] as $lessonTitle => $tasks) {
                $this->seedPracticeTasks($courseId, $lessonTitle, $tasks);
            }
        }
    }

    private function seedQuizzes(int $courseId, string $lessonTitle, array $quizzes): void
    {
        $lessonId = DB::table('lessons')
            ->where('course_id', $courseId)
            ->where('title', $lessonTitle)
            ->value('id');

        if (!$lessonId) return;

        DB::table('lesson_quizzes')->where('lesson_id', $lessonId)->delete();

        foreach ($quizzes as $i => $q) {
            DB::table('lesson_quizzes')->insert([
                'lesson_id' => $lessonId,
                'question_text' => $q['q'],
                'options_json' => json_encode($q['o']),
                'correct_option' => $q['c'],
                'explanation' => $q['e'],
                'order_num' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedPracticeTasks(int $courseId, string $lessonTitle, array $tasks): void
    {
        $lessonId = DB::table('lessons')
            ->where('course_id', $courseId)
            ->where('title', $lessonTitle)
            ->value('id');

        if (!$lessonId) return;

        DB::table('lesson_practice_tasks')->where('lesson_id', $lessonId)->delete();

        foreach ($tasks as $t) {
            DB::table('lesson_practice_tasks')->insert([
                'lesson_id' => $lessonId,
                'language' => $t['lang'],
                'title' => $t['title'],
                'prompt' => $t['prompt'],
                'expected_output' => $t['out'] ?? '',
                'starter_code' => $t['start'],
                'tests_json' => json_encode($t['tests']),
                'is_required' => true,
                'difficulty' => $t['diff'] ?? 'medium',
                'time_limit' => $t['time'] ?? 30,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
