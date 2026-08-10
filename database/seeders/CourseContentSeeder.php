<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\LessonQuiz;
use App\Models\LessonPracticeTask;
use App\Models\CourseExam;

class CourseContentSeeder extends Seeder
{
    public function run(): void
    {
        $dataFiles = [
            __DIR__.'/CourseData1To5.php',
            __DIR__.'/CourseData6To10.php',
            __DIR__.'/CourseData11To15.php',
            __DIR__.'/CourseData16To20.php',
        ];

        foreach ($dataFiles as $file) {
            $courses = require $file;
            foreach ($courses as $courseId => $data) {
                $this->seedCourse($courseId, $data);
            }
        }
    }

    private function seedCourse(int $courseId, array $data): void
    {
        $this->command->info("Seeding course {$courseId}...");

        // Seed lessons
        if (!empty($data['lessons'])) {
            $lessons = Lesson::where('course_id', $courseId)->orderBy('order_num')->get();
            foreach ($data['lessons'] as $i => $lessonData) {
                $lesson = $lessons->get($i);
                if ($lesson) {
                    $lesson->update($lessonData);
                }
            }
        }

        // Seed quizzes
        if (!empty($data['quizzes'])) {
            LessonQuiz::whereHas('lesson', fn($q) => $q->where('course_id', $courseId))->delete();
            $lessons = Lesson::where('course_id', $courseId)->orderBy('order_num')->pluck('id');
            $questions = $data['quizzes'];
            $perLesson = (int) ceil(count($questions) / max($lessons->count(), 1));

            foreach ($lessons as $li => $lessonId) {
                $slice = array_slice($questions, $li * $perLesson, $perLesson);
                foreach ($slice as $qi => $q) {
                    LessonQuiz::create([
                        'lesson_id' => $lessonId,
                        'question_text' => $q['question'],
                        'options_json' => $q['options'],
                        'correct_option' => $q['correct'],
                        'explanation' => $q['explanation'] ?? '',
                        'order_num' => $qi + 1,
                    ]);
                }
            }
        }

        // Seed practice tasks
        if (!empty($data['practices'])) {
            LessonPracticeTask::whereHas('lesson', fn($q) => $q->where('course_id', $courseId))->delete();
            $lessons = Lesson::where('course_id', $courseId)->orderBy('order_num')->pluck('id');
            $lessonId = $lessons->last();
            if ($lessonId) {
                foreach ($data['practices'] as $ti => $t) {
                    LessonPracticeTask::create([
                        'lesson_id' => $lessonId,
                        'language' => $t['language'] ?? 'html',
                        'title' => $t['title'],
                        'prompt' => $t['instructions'] ?? $t['prompt'] ?? '',
                        'expected_output' => $t['solution_code'] ?? $t['expected_output'] ?? '',
                        'starter_code' => $t['starter_code'] ?? '',
                        'tests_json' => $t['tests'] ?? [],
                        'is_required' => true,
                        'difficulty' => match($t['difficulty'] ?? 'beginner') {
                            'beginner', 'easy' => 'easy',
                            'intermediate', 'medium' => 'medium',
                            'advanced', 'hard' => 'hard',
                            default => 'medium',
                        },
                    ]);
                }
            }
        }

        // Seed exam
        if (!empty($data['exam'])) {
            $exam = $data['exam'];
            CourseExam::where('course_id', $courseId)->update([
                'question_bank_json' => $exam['bank'],
                'time_limit_minutes' => $exam['time'] ?? 70,
                'pass_percent' => $exam['pass'] ?? 70,
                'questions_per_exam' => min(30, count($exam['bank'])),
            ]);
        }
    }
}
