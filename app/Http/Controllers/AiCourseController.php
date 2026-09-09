<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateStepDescription;
use App\Jobs\GenerateStepTests;
use App\Jobs\GenerateStepVocabulary;
use App\Jobs\GenerateStepExams;
use App\Jobs\GenerateStepSlides;
use App\Jobs\GenerateAllCourseContent;
use App\Jobs\GenerateCourseRoadmap;
use App\Models\Course;
use App\Models\CourseStep;
use App\Models\CourseStepTest;
use App\Models\StudentCourse;
use App\Models\StepStudent;
use App\Models\TestStudent;
use App\Services\CourseGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::where('type', 'teacher')
            ->with(['owner', 'courseSkills'])
            ->withCount('students');

        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('topic', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('level') && $request->level) {
            $query->where('course_level', $request->level);
        }

        $courses = $query->latest()->paginate(12);
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'level' => 'required|in:beginner,intermediate,advanced',
            'freetime' => 'required|integer|min:1|max:40',
            'type' => 'required|in:private,teacher',
        ]);

        $user = Auth::user();
        $service = app(CourseGenerationService::class);

        $course = Course::create([
            'user_id' => $user->id,
            'title' => $validated['topic'],
            'topic' => $validated['topic'],
            'instructor' => $user->name,
            'type' => $validated['type'],
            'ai_generated' => true,
            'course_level' => $validated['level'],
            'freetime' => $validated['freetime'],
            'total_steps' => 0,
            'category' => 'other',
            'level' => $validated['level'] === 'beginner' ? 'Начальный' : ($validated['level'] === 'intermediate' ? 'Средний' : 'Продвинутый'),
            'generation_status' => 'pending',
        ]);

        GenerateCourseRoadmap::dispatch(
            $course->id,
            $validated['topic'],
            $validated['level'],
            $validated['freetime'],
            $user->language ?? 'ru'
        );

        return redirect()->route('courses.generating', $course);
    }

    public function generating(Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            abort(403);
        }

        return view('courses.generating', compact('course'));
    }

    public function status(Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        return response()->json([
            'status' => $course->generation_status,
            'progress' => $course->generation_progress,
            'total_steps' => $course->total_steps,
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $course = Course::with(['owner', 'courseSkills', 'steps' => function ($q) {
            $q->with(['tests', 'vocabularies']);
        }])->findOrFail($id);

        if ($course->type === 'private' && $course->user_id !== $user->id) {
            abort(403);
        }

        $enrollment = StudentCourse::where('user_id', $user->id)
            ->where('course_id', $id)
            ->first();

        $completedStepIds = [];
        if ($enrollment) {
            $completedStepIds = StepStudent::where('user_id', $user->id)
                ->where('course_id', $id)
                ->where('is_completed', true)
                ->pluck('step_id')
                ->toArray();
        }

        $isOwner = $course->user_id === $user->id;

        return view('courses.show', compact('course', 'enrollment', 'completedStepIds', 'isOwner'));
    }

    public function showStep($courseId, $stepId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        $enrollment = StudentCourse::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->first();

        $isOwner = $course->user_id === $user->id;

        if (!$enrollment && !$isOwner) {
            return redirect()->route('courses.show', $courseId)
                ->with('info', 'Подпишитесь на курс для доступа к шагам.');
        }

        $step = CourseStep::where('id', $stepId)
            ->where('course_id', $courseId)
            ->with(['tests.variants', 'tests.answers', 'tests.matchingItems', 'vocabularies.links', 'links', 'children'])
            ->firstOrFail();

        $stepProgress = StepStudent::where('user_id', $user->id)
            ->where('step_id', $stepId)
            ->first();

        $testResults = TestStudent::where('user_id', $user->id)
            ->where('step_id', $stepId)
            ->pluck('test_id')
            ->toArray();

        // --- Навигация и прогресс по курсу (для learning-flow страницы шага) ---
        $courseSteps = CourseStep::where('course_id', $courseId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'course_id', 'parent_id', 'type', 'title', 'experience', 'sort_order']);

        $completedStepIds = StepStudent::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('is_completed', true)
            ->pluck('step_id')
            ->toArray();

        $orderedIds = $courseSteps->pluck('id')->all();
        $pos = array_search((int) $stepId, array_map('intval', $orderedIds), true);
        $prevStep = ($pos !== false && $pos > 0) ? $courseSteps[$pos - 1] : null;
        $nextStep = ($pos !== false && $pos < count($courseSteps) - 1) ? $courseSteps[$pos + 1] : null;

        $totalSteps = $courseSteps->count();
        $doneCount = count(array_intersect($completedStepIds, $orderedIds));
        $progressPercent = $totalSteps > 0 ? (int) round(($doneCount / $totalSteps) * 100) : 0;

        // Приблизительное время чтения лекции (200 слов/мин, минимум 1 мин)
        $plain = trim(strip_tags((string) $step->description));
        $wordCount = $plain === '' ? 0 : count(preg_split('/\s+/u', $plain));
        $readingMinutes = max(1, (int) ceil($wordCount / 200));

        // Сколько XP уже можно забрать на шаге (тесты + словарные блоки + сам шаг)
        $testsXp = $step->tests->sum('score');
        $vocabXp = $step->vocabularies->sum('experience');

        return view('courses.step', compact(
            'course', 'step', 'enrollment', 'stepProgress', 'testResults', 'isOwner',
            'courseSteps', 'completedStepIds', 'prevStep', 'nextStep',
            'progressPercent', 'doneCount', 'totalSteps',
            'readingMinutes', 'wordCount', 'testsXp', 'vocabXp'
        ));
    }

    public function completeStep($courseId, $stepId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        $enrollment = StudentCourse::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment && $course->user_id !== $user->id) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

        StepStudent::updateOrCreate(
            ['user_id' => $user->id, 'step_id' => $stepId],
            ['is_completed' => true, 'course_id' => $courseId]
        );

        if ($enrollment) {
            $totalSteps = CourseStep::where('course_id', $courseId)->count();
            $completedSteps = StepStudent::where('user_id', $user->id)
                ->where('course_id', $courseId)
                ->where('is_completed', true)
                ->count();

            $step = CourseStep::find($stepId);
            $enrollment->increment('experience', $step?->experience ?? 0);
            $enrollment->update([
                'steps_completed' => $completedSteps,
                'status' => $completedSteps >= $totalSteps,
            ]);

            $percent = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;

            return response()->json([
                'success' => true,
                'percent' => $percent,
                'completed' => $completedSteps >= $totalSteps,
                'completed_steps' => $completedSteps,
                'total_steps' => $totalSteps,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function submitTest(Request $request, $courseId, $stepId, $testId)
    {
        $user = Auth::user();
        $test = CourseStepTest::where('id', $testId)->where('step_id', $stepId)->firstOrFail();

        $validated = $request->validate(['answer' => 'required']);

        $userAnswer = $validated['answer'];
        $isCorrect = false;
        $score = 0;

        match ($test->type_test) {
            'one_correct' => function () use ($test, $userAnswer, &$isCorrect) {
                $correctAnswer = $test->answers()->where('is_correct', true)->first();
                $isCorrect = $correctAnswer && mb_strtolower(trim((string) $userAnswer)) === mb_strtolower(trim((string) $correctAnswer->answer));
            },
            'list_correct' => function () use ($test, $userAnswer, &$isCorrect) {
                $norm = fn($a) => mb_strtolower(trim((string) $a));
                $correctAnswers = $test->answers()->where('is_correct', true)->pluck('answer')->map($norm)->sort()->values()->toArray();
                $userAnswers = is_array($userAnswer) ? collect($userAnswer)->map($norm)->sort()->values()->toArray() : [];
                $isCorrect = $correctAnswers === $userAnswers && count($userAnswers) > 0;
            },
            'question_answer' => function () use ($test, $userAnswer, &$isCorrect) {
                $correctAnswer = $test->answers()->where('is_correct', true)->first();
                $isCorrect = $correctAnswer && mb_strtolower(trim((string) $userAnswer)) === mb_strtolower(trim((string) $correctAnswer->answer));
            },
            'true_false' => function () use ($test, $userAnswer, &$isCorrect) {
                $correctAnswer = $test->answers()->where('is_correct', true)->first();
                $isCorrect = $correctAnswer && trim((string) $userAnswer) == trim((string) $correctAnswer->answer);
            },
            'matching' => function () use ($test, $userAnswer, &$isCorrect) {
                $matchings = $test->matchingItems()->get();
                if (!is_array($userAnswer) || count($userAnswer) !== $matchings->count() || $matchings->count() === 0) {
                    $isCorrect = false;
                    return;
                }
                // Строим карту list1 -> list2, проверяем полноту и отсутствие дублей.
                $correctMap = $matchings->pluck('list2_item', 'list1_item')->map(fn($v) => trim((string) $v))->toArray();
                $seenL1 = [];
                $isCorrect = true;
                foreach ($userAnswer as $pair) {
                    $l1 = trim((string) ($pair['list1'] ?? ''));
                    $l2 = trim((string) ($pair['list2'] ?? ''));
                    if ($l1 === '' || !array_key_exists($l1, $correctMap) || isset($seenL1[$l1]) || $correctMap[$l1] !== $l2) {
                        $isCorrect = false;
                        break;
                    }
                    $seenL1[$l1] = true;
                }
                // Все ключи должны быть покрыты.
                if ($isCorrect && count($seenL1) !== count($correctMap)) {
                    $isCorrect = false;
                }
            },
            default => null,
        };

        if ($isCorrect) $score = $test->score;

        TestStudent::updateOrCreate(
            ['user_id' => $user->id, 'test_id' => $testId],
            ['is_correct' => $isCorrect, 'score' => $score, 'step_id' => $stepId, 'course_id' => $courseId]
        );

        $enrollment = StudentCourse::where('user_id', $user->id)->where('course_id', $courseId)->first();
        if ($isCorrect && $enrollment) {
            $enrollment->increment('experience', $score);
        }

        return response()->json([
            'is_correct' => $isCorrect,
            'score' => $score,
            'correct_answer' => $test->answers()->where('is_correct', true)->pluck('answer')->toArray(),
        ]);
    }

    public function generateContent(Request $request, $courseId, $stepId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        if ($course->user_id !== $user->id) {
            abort(403);
        }

        $step = CourseStep::where('id', $stepId)->where('course_id', $courseId)->firstOrFail();
        $type = $request->input('type', 'all');

        if (in_array($type, ['tests', 'all']) && $step->tests()->count() === 0) {
            GenerateStepTests::dispatch($step->id);
        }
        if (in_array($type, ['vocabulary', 'all']) && $step->vocabularies()->count() === 0) {
            GenerateStepVocabulary::dispatch($step->id);
        }
        if (in_array($type, ['description', 'all']) && !$step->description) {
            GenerateStepDescription::dispatch($step->id);
        }
        if (in_array($type, ['exams', 'all']) && $step->exams()->count() === 0) {
            GenerateStepExams::dispatch($step->id);
        }
        if (in_array($type, ['slides', 'all']) && $step->slides()->count() === 0) {
            GenerateStepSlides::dispatch($step->id);
        }

        return response()->json(['status' => 'ok', 'message' => 'AI генерирует контент. Обновите страницу через 30-60 секунд.']);
    }

    public function generateAllContent($courseId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        if ($course->user_id !== $user->id) {
            abort(403);
        }

        GenerateAllCourseContent::dispatch($courseId);

        return response()->json(['status' => 'ok', 'message' => 'Генерация всего контента запущена.']);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        Course::where('id', $id)->where('user_id', $user->id)->delete();
        return redirect()->route('courses.my')->with('success', 'Курс удалён.');
    }

    public function myCourses()
    {
        $user = Auth::user();

        $owned = Course::where('user_id', $user->id)
            ->withCount('students')
            ->latest()
            ->get();

        $enrolled = StudentCourse::where('user_id', $user->id)
            ->with(['course' => function ($q) {
                $q->with('owner');
            }])
            ->get();

        return view('courses.my', compact('owned', 'enrolled'));
    }

    public function available()
    {
        $user = Auth::user();

        $enrolledIds = StudentCourse::where('user_id', $user->id)->pluck('course_id')->toArray();

        $courses = Course::where('type', 'teacher')
            ->whereNotIn('id', $enrolledIds)
            ->with(['owner', 'courseSkills'])
            ->withCount('students')
            ->latest()
            ->paginate(12);

        return view('courses.available', compact('courses'));
    }

    public function subscribe($id)
    {
        $course = Course::where('id', $id)->where('type', 'teacher')->firstOrFail();
        $user = Auth::user();

        if (StudentCourse::where('user_id', $user->id)->where('course_id', $id)->exists()) {
            return back()->with('info', 'Вы уже подписаны.');
        }

        StudentCourse::create(['user_id' => $user->id, 'course_id' => $id]);
        $course->increment('students_count');

        return back()->with('success', 'Подписались на курс!');
    }

    public function unsubscribe($id)
    {
        $user = Auth::user();
        $enrollment = StudentCourse::where('user_id', $user->id)->where('course_id', $id)->first();
        if ($enrollment) {
            $enrollment->delete();
            Course::where('id', $id)->decrement('students_count');
        }
        return back()->with('success', 'Отписаны от курса.');
    }
}
