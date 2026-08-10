<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonQuiz;
use App\Models\LessonPracticeTask;
use App\Models\CourseExam;

class CourseContentFullSeeder extends Seeder
{
    public function run(): void
    {
        $lessonFiles = [
            __DIR__ . '/LessonData1To5.php',
            __DIR__ . '/LessonData6To10.php',
            __DIR__ . '/LessonData11To15.php',
            __DIR__ . '/LessonData16To20.php',
            __DIR__ . '/LessonData18To20Expanded.php',
        ];

        $quizFiles = [
            __DIR__ . '/CourseData1To5.php',
            __DIR__ . '/CourseData6To10.php',
            __DIR__ . '/CourseData11To15.php',
            __DIR__ . '/CourseData16To20.php',
        ];

        foreach ($lessonFiles as $lessonFile) {
            $lessonData = require $lessonFile;
            foreach ($lessonData as $courseId => $data) {
                $this->seedCourseContent($courseId, $data);
            }
        }

        foreach ($quizFiles as $quizFile) {
            $quizData = require $quizFile;
            foreach ($quizData as $courseId => $data) {
                $this->seedQuizzesAndExams($courseId, $data);
            }
        }
    }

    private function seedCourseContent(int $courseId, array $data): void
    {
        $this->command->info("Seeding lessons for course {$courseId}...");

        $existingLessons = Lesson::where('course_id', $courseId)->orderBy('order_num')->get();
        $lessonCount = count($data['lessons']);

        Lesson::where('course_id', $courseId)->delete();

        foreach ($data['lessons'] as $i => $lessonData) {
            Lesson::create(array_merge($lessonData, [
                'course_id' => $courseId,
                'order_num' => $i + 1,
                'completed' => false,
            ]));
        }
    }

    private function seedQuizzesAndExams(int $courseId, array $data): void
    {
        $this->command->info("Seeding quizzes/exams for course {$courseId}...");

        $lessons = Lesson::where('course_id', $courseId)->orderBy('order_num')->get();
        $quizLessons = $lessons->where('type', 'quiz')->values();
        $nonQuizLessons = $lessons->where('type', '!=', 'quiz')->values();

        LessonQuiz::whereHas('lesson', fn($q) => $q->where('course_id', $courseId))->delete();

        if (!empty($data['quizzes']) && $quizLessons->count() > 0) {
            $questions = $data['quizzes'];
            $perLesson = (int) ceil(count($questions) / $quizLessons->count());

            foreach ($quizLessons as $li => $lesson) {
                $slice = array_slice($questions, $li * $perLesson, $perLesson);
                foreach ($slice as $qi => $q) {
                    LessonQuiz::create([
                        'lesson_id' => $lesson->id,
                        'question_text' => $q['question'],
                        'options_json' => $q['options'],
                        'correct_option' => $q['correct'],
                        'explanation' => $q['explanation'] ?? '',
                        'order_num' => $qi + 1,
                    ]);
                }
            }
        }

        LessonPracticeTask::whereHas('lesson', fn($q) => $q->where('course_id', $courseId))->delete();

        if (!empty($data['practices']) && $nonQuizLessons->count() > 0) {
            $tasks = $data['practices'];
            $perLesson = max(1, (int) ceil(count($tasks) / $nonQuizLessons->count()));

            foreach ($nonQuizLessons as $li => $lesson) {
                $slice = array_slice($tasks, $li * $perLesson, $perLesson);
                foreach ($slice as $t) {
                    LessonPracticeTask::create([
                        'lesson_id' => $lesson->id,
                        'language' => $t['language'] ?? 'html',
                        'title' => $t['title'],
                        'prompt' => $t['instructions'] ?? $t['prompt'] ?? '',
                        'expected_output' => $t['solution_code'] ?? $t['expected_output'] ?? '',
                        'starter_code' => $t['starter_code'] ?? '',
                        'tests_json' => $t['tests'] ?? [],
                        'is_required' => true,
                        'difficulty' => match ($t['difficulty'] ?? 'beginner') {
                            'beginner', 'easy' => 'easy',
                            'intermediate', 'medium' => 'medium',
                            'advanced', 'hard' => 'hard',
                            default => 'medium',
                        },
                        'time_limit' => 60,
                    ]);
                }
            }
        }

        if (!empty($data['exam'])) {
            CourseExam::where('course_id', $courseId)->update([
                'question_bank_json' => $data['exam']['bank'],
                'time_limit_minutes' => $data['exam']['time'] ?? 70,
                'pass_percent' => $data['exam']['pass'] ?? 70,
                'questions_per_exam' => min(30, count($data['exam']['bank'])),
            ]);
        }
    }
}
