<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\Certificate;
use App\Models\UserCourseProgress;
use App\Models\UserLessonProgress;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function index(Request $request)
    {
        $query = Course::with('lessons');

        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('title', 'like', '%' . $search . '%', 'and');
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        $courses = $query->latest()->paginate(12);

        return view('courses.index', compact('courses'));
    }

    public function show($id)
    {
        $course = Course::with('lessons')->findOrFail($id);
        $user = Auth::user();

        $progress = UserCourseProgress::where('user_id', $user->id)
            ->where('course_id', $id)
            ->first();

        $completedLessonIds = UserLessonProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->pluck('lesson_id')
            ->toArray();

        $totalLessons = $course->lessons->count();
        $completedCount = count($completedLessonIds);
        $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        $exam = CourseExam::where('course_id', $id)->first();
        $certificate = Certificate::where('user_id', $user->id)->where('course_id', $id)->first();

        $modules = $course->lessons->sortBy('order_num')->groupBy('module');

        $nextLesson = $course->lessons->sortBy('order_num')->first(function ($lesson) use ($completedLessonIds) {
            return !in_array($lesson->id, $completedLessonIds);
        });

        $instructorUser = \App\Models\User::where('name', $course->instructor)->first();

        return view('courses.show', compact(
            'course', 'progress', 'completedLessonIds', 'totalLessons',
            'percent', 'exam', 'certificate', 'modules', 'nextLesson', 'instructorUser'
        ));
    }

    public function completeLesson(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lesson_id' => 'required|exists:lessons,id',
        ]);

        $user = Auth::user();

        $alreadyCompleted = UserLessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $validated['lesson_id'])
            ->where('completed', true)
            ->exists();

        UserLessonProgress::firstOrCreate([
            'user_id' => $user->id,
            'lesson_id' => $validated['lesson_id'],
        ], [
            'completed' => true,
            'completed_at' => now(),
        ]);

        $lesson = \App\Models\Lesson::find($validated['lesson_id']);
        $xpEarned = 0;
        if (!$alreadyCompleted && $lesson) {
            $xpEarned = $this->gamificationService->awardLessonXp($user, $lesson->title);
        }

        $course = Course::with('lessons')->find($validated['course_id']);
        $totalLessons = $course->lessons->count();
        $completedCount = UserLessonProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->count();
        $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        $wasAlreadyComplete = UserCourseProgress::where('user_id', $user->id)
            ->where('course_id', $validated['course_id'])
            ->where('completed', true)
            ->exists();

        UserCourseProgress::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $validated['course_id']],
            [
                'progress' => $percent,
                'completed' => $percent >= 100,
                'completed_at' => $percent >= 100 ? now() : null,
            ]
        );

        $courseCompletedNow = $percent >= 100 && !$wasAlreadyComplete;
        $courseXp = 0;
        if ($courseCompletedNow) {
            $courseXp = $this->gamificationService->awardCourseCompleteXp($user, $course->title);
        }

        $user->refresh();

        return response()->json([
            'success' => true,
            'percent' => $percent,
            'completed' => $percent >= 100,
            'xp_earned' => $xpEarned,
            'course_xp' => $courseXp,
            'total_xp' => $user->total_xp,
            'level' => $user->level,
        ]);
    }

    public function exam($id)
    {
        $course = Course::findOrFail($id);
        $exam = CourseExam::where('course_id', $id)->firstOrFail();

        $questions = $exam->getRandomQuestions();

        return view('courses.exam', compact('course', 'exam', 'questions'));
    }

    public function submitExam(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $exam = CourseExam::where('course_id', $id)->firstOrFail();

        $validated = $request->validate([
            'answers' => 'required|array',
            'question_order' => 'required|array',
        ]);

        $questionOrder = $validated['question_order'];
        $answers = $validated['answers'];

        $bank = $exam->question_bank_json['questions'] ?? [];
        if (empty($bank)) {
            $bank = $exam->exam_json['questions'] ?? [];
        }

        $correct = 0;
        $total = count($questionOrder);
        $results = [];

        foreach ($questionOrder as $index => $bankIndex) {
            $question = $bank[$bankIndex] ?? null;
            if (!$question) continue;

            $userAnswer = $answers[$index] ?? null;
            $correctAnswer = $question['correct'] ?? null;
            $isCorrect = $userAnswer !== null && $userAnswer == $correctAnswer;

            if ($isCorrect) $correct++;

            $results[] = [
                'question' => $question['question'] ?? '',
                'options' => $question['options'] ?? [],
                'user_answer' => $userAnswer,
                'correct_answer' => $correctAnswer,
                'is_correct' => $isCorrect,
            ];
        }

        $score = $total > 0 ? round(($correct / $total) * 100) : 0;
        $passed = $score >= $exam->pass_percent;

        $user = Auth::user();
        $xpEarned = 0;

        $certificate = null;
        if ($passed) {
            $existingCert = Certificate::where('user_id', Auth::id())->where('course_id', $id)->first();
            if (!$existingCert) {
                $certificate = Certificate::create([
                    'user_id' => Auth::id(),
                    'course_id' => $id,
                    'cert_hash' => Str::random(40),
                    'certificate_name' => $course->title,
                    'issuer' => 'CodeMaster',
                    'issue_date' => now(),
                ]);

                $xpEarned = $this->gamificationService->awardCourseExamXp($user, $course->title, $score);
            } else {
                $certificate = $existingCert;
            }
        }

        $user->refresh();

        return view('courses.exam-result', compact(
            'course', 'exam', 'score', 'passed', 'correct', 'total',
            'results', 'certificate', 'xpEarned'
        ));
    }
}
