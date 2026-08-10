<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\LessonPracticeTask;
use App\Models\PracticeSubmission;
use App\Services\GamificationService;
use App\Services\Judge0Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PracticeController extends Controller
{
    protected $gamificationService;
    protected $judge0Service;

    public function __construct(GamificationService $gamificationService, Judge0Service $judge0Service)
    {
        $this->gamificationService = $gamificationService;
        $this->judge0Service = $judge0Service;
    }

    public function show($courseId, $taskId)
    {
        $course = Course::with('lessons')->findOrFail($courseId);
        $task = LessonPracticeTask::with('lesson')->findOrFail($taskId);
        $user = Auth::user();

        $submissions = PracticeSubmission::where('user_id', $user->id)
            ->where('task_id', $taskId)
            ->orderBy('created_at', 'desc')
            ->get();

        $bestSubmission = $submissions->where('passed', true)->first();

        return view('courses.practice', compact('course', 'task', 'submissions', 'bestSubmission'));
    }

    public function runTests(Request $request, $courseId, $taskId)
    {
        $request->validate([
            'code' => 'required|string|max:50000',
        ]);

        $task = LessonPracticeTask::findOrFail($taskId);
        $user = Auth::user();
        $language = $request->input('language', $task->language);

        $tests = $task->test_runner_json['tests'] ?? $task->tests_json ?? [];
        $result = $this->judge0Service->runPractice($language, $request->code, $tests);

        $success = $result['status'] === 'accepted';
        $passed = $result['passed_tests'] ?? 0;
        $total = $result['total_tests'] ?? 0;

        $submission = PracticeSubmission::create([
            'user_id' => $user->id,
            'task_id' => $taskId,
            'code' => $request->code,
            'passed' => $success,
            'stdout' => $result['results'][0]['output'] ?? '',
            'details_json' => $result,
        ]);

        $xpEarned = 0;
        if ($success) {
            $alreadyPassed = PracticeSubmission::where('user_id', $user->id)
                ->where('task_id', $taskId)
                ->where('passed', true)
                ->where('id', '!=', $submission->id)
                ->exists();

            if (!$alreadyPassed) {
                $xpEarned = $this->gamificationService->awardPracticeXp($user, $task->title);
                $user->refresh();
            }
        }

        return response()->json([
            'submission_id' => $submission->id,
            'passed' => $success,
            'passed_count' => $passed,
            'total_count' => $total,
            'results' => $result['results'],
            'xp_earned' => $xpEarned,
            'total_xp' => $user->total_xp,
            'level' => $user->level,
        ]);
    }

    public function submit(Request $request, $courseId, $taskId)
    {
        return $this->runTests($request, $courseId, $taskId);
    }
}
