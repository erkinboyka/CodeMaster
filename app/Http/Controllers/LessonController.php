<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonQuiz;
use App\Models\LessonPracticeTask;
use App\Models\UserLessonProgress;
use App\Models\UserCourseProgress;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function show($courseId, $lessonId)
    {
        $user = Auth::user();
        $course = Course::with('lessons')->findOrFail($courseId);
        $lesson = Lesson::with(['lessonQuizzes', 'practiceTasks.submissions', 'quizQuestions.options'])
            ->where('course_id', $courseId)
            ->where('id', $lessonId)
            ->firstOrFail();

        $completedLessonIds = UserLessonProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->pluck('lesson_id')
            ->toArray();

        $sortedLessons = $course->lessons->sortBy('order_num')->values();
        $currentIndex = $sortedLessons->pluck('id')->search($lessonId);
        $prevLesson = $currentIndex > 0 ? $sortedLessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $sortedLessons->count() - 1 ? $sortedLessons[$currentIndex + 1] : null;

        $totalLessons = $course->lessons->count();
        $completedCount = count($completedLessonIds);
        $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        $practiceResults = [];
        foreach ($lesson->practiceTasks as $task) {
            $practiceResults[$task->id] = $task->submissions->where('user_id', $user->id)->where('passed', true)->isNotEmpty();
        }

        return view('courses.lesson', compact(
            'course', 'lesson', 'sortedLessons', 'completedLessonIds',
            'prevLesson', 'nextLesson', 'percent', 'practiceResults'
        ));
    }

    public function submitQuiz(Request $request, $courseId, $lessonId)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        $lesson = Lesson::where('course_id', $courseId)->where('id', $lessonId)->firstOrFail();
        $quizzes = $lesson->lessonQuizzes()->orderBy('order_num')->get();
        $answers = $request->answers;

        $correct = 0;
        $total = $quizzes->count();
        $results = [];

        foreach ($quizzes as $index => $quiz) {
            $userAnswer = $answers[$index] ?? null;
            $isCorrect = $userAnswer !== null && (int)$userAnswer === $quiz->correct_option;

            if ($isCorrect) $correct++;

            $results[] = [
                'question' => $quiz->question_text,
                'options' => $quiz->options_json,
                'user_answer' => $userAnswer,
                'correct_answer' => $quiz->correct_option,
                'explanation' => $quiz->explanation,
                'is_correct' => $isCorrect,
            ];
        }

        $score = $total > 0 ? round(($correct / $total) * 100) : 0;
        $passed = $score >= 70;

        $user = Auth::user();
        $xpEarned = 0;

        if ($passed) {
            $xpEarned = $this->gamificationService->awardQuizXp($user, $score);
            $user->refresh();
        }

        return response()->json([
            'score' => $score,
            'passed' => $passed,
            'correct' => $correct,
            'total' => $total,
            'results' => $results,
            'xp_earned' => $xpEarned,
            'total_xp' => $user->total_xp,
            'level' => $user->level,
        ]);
    }

    public function completeLesson(Request $request, $courseId, $lessonId)
    {
        $user = Auth::user();

        $lesson = Lesson::where('course_id', $courseId)->where('id', $lessonId)->firstOrFail();

        UserLessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lessonId],
            ['completed' => true, 'completed_at' => now()]
        );

        $course = Course::with('lessons')->find($courseId);
        $totalLessons = $course->lessons->count();
        $completedCount = UserLessonProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->count();
        $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        UserCourseProgress::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $courseId],
            [
                'progress' => $percent,
                'completed' => $percent >= 100,
                'completed_at' => $percent >= 100 ? now() : null,
            ]
        );

        return response()->json([
            'success' => true,
            'percent' => $percent,
            'completed' => $percent >= 100,
        ]);
    }
}
