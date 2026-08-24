<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonQuiz;
use App\Models\LessonPracticeTask;
use App\Models\RoadmapNode;
use App\Models\RoadmapLesson;
use App\Models\RoadmapQuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSeedController extends Controller
{
    public function seedLearningPack(Request $request)
    {
        $created = [];
        $existing = [];

        $coursesData = [
            ['title' => 'HTML & CSS', 'instructor' => 'CodeMaster', 'description' => 'Основы веб-разработки', 'category' => 'frontend', 'level' => 'Начальный'],
            ['title' => 'JavaScript', 'instructor' => 'CodeMaster', 'description' => 'Программирование на JavaScript', 'category' => 'frontend', 'level' => 'Средний'],
            ['title' => 'PHP', 'instructor' => 'CodeMaster', 'description' => 'Серверная разработка на PHP', 'category' => 'backend', 'level' => 'Средний'],
            ['title' => 'Laravel', 'instructor' => 'CodeMaster', 'description' => 'Фреймворк Laravel', 'category' => 'backend', 'level' => 'Продвинутый'],
            ['title' => 'MySQL', 'instructor' => 'CodeMaster', 'description' => 'Реляционные базы данных', 'category' => 'backend', 'level' => 'Средний'],
            ['title' => 'Python', 'instructor' => 'CodeMaster', 'description' => 'Программирование на Python', 'category' => 'backend', 'level' => 'Начальный'],
            ['title' => 'C++', 'instructor' => 'CodeMaster', 'description' => 'Системное программирование', 'category' => 'backend', 'level' => 'Продвинутый'],
            ['title' => 'Java', 'instructor' => 'CodeMaster', 'description' => 'Кроссплатформенная разработка', 'category' => 'backend', 'level' => 'Средний'],
            ['title' => 'C#', 'instructor' => 'CodeMaster', 'description' => 'Разработка на .NET', 'category' => 'backend', 'level' => 'Средний'],
            ['title' => 'Git', 'instructor' => 'CodeMaster', 'description' => 'Система контроля версий', 'category' => 'devops', 'level' => 'Начальный'],
            ['title' => 'DevOps', 'instructor' => 'CodeMaster', 'description' => 'Автоматизация деплоя', 'category' => 'devops', 'level' => 'Продвинутый'],
            ['title' => 'UI/UX Design', 'instructor' => 'CodeMaster', 'description' => 'Дизайн интерфейсов', 'category' => 'design', 'level' => 'Начальный'],
        ];

        DB::transaction(function () use ($coursesData, &$created, &$existing) {
            foreach ($coursesData as $cData) {
                $course = Course::firstOrCreate(
                    ['title' => $cData['title']],
                    $cData
                );

                if ($wasCreated = $course->wasRecentlyCreated) {
                    $created[] = $course->id;

                    for ($i = 1; $i <= 3; $i++) {
                        $lesson = Lesson::create([
                            'course_id' => $course->id,
                            'title' => "{$course->title} - Урок {$i}",
                            'type' => 'article',
                            'content' => "Содержание урока {$i} по курсу {$course->title}",
                            'order_num' => $i,
                        ]);

                        LessonQuiz::create([
                            'lesson_id' => $lesson->id,
                            'question_text' => "Тестовый вопрос {$i} для {$course->title}?",
                            'options_json' => ['Вариант A', 'Вариант B', 'Вариант C', 'Вариант D'],
                            'correct_option' => 0,
                        ]);

                        LessonPracticeTask::create([
                            'lesson_id' => $lesson->id,
                            'title' => "Практика {$i}: {$course->title}",
                            'language' => 'python',
                            'prompt' => "Решите задачу по теме {$course->title}",
                            'starter_code' => '# Your code here',
                            'difficulty' => 'medium',
                        ]);
                    }
                } else {
                    $existing[] = $course->id;
                }
            }
        });

        return response()->json([
            'created' => $created,
            'existing' => $existing,
            'message' => count($created) . ' courses created, ' . count($existing) . ' already existed.',
        ]);
    }
}
