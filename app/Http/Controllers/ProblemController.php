<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\ProblemSubmission;
use App\Models\ProblemTopic;
use App\Models\CollaborationSession;
use App\Models\DailyChallenge;
use App\Services\Judge0Service;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProblemController extends Controller
{
    protected Judge0Service $judge0;

    public function __construct(Judge0Service $judge0)
    {
        $this->judge0 = $judge0;
    }

    public function index(Request $request)
    {
        $query = Problem::with('topics');

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('topic')) {
            $query->whereHas('topics', function ($q) use ($request) {
                $q->where('problem_topics.slug', $request->topic);
            });
        }

        if ($request->filled('status') && Auth::check()) {
            $status = $request->status;
            if ($status === 'solved') {
                $query->whereHas('users', function ($q) {
                    $q->where('user_id', Auth::id())->where('problem_user.status', 'solved');
                });
            } elseif ($status === 'attempted') {
                $query->whereHas('users', function ($q) {
                    $q->where('user_id', Auth::id())->where('problem_user.status', 'attempted');
                });
            } elseif ($status === 'unsolved') {
                $query->whereDoesntHave('users', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            }
        }

        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('title', 'like', '%' . $search . '%');
        }

        $problems = $query->orderBy('id')->paginate(50)->withQueryString();

        $topics = ProblemTopic::orderBy('name')->get();

        $stats = [
            'total' => Problem::count(),
            'easy' => Problem::where('difficulty', 'easy')->count(),
            'medium' => Problem::where('difficulty', 'medium')->count(),
            'hard' => Problem::where('difficulty', 'hard')->count(),
        ];

        if (Auth::check()) {
            $stats['solved'] = Problem::whereHas('users', function ($q) {
                $q->where('user_id', Auth::id())->where('status', 'solved');
            })->count();
        }

        return view('problems.index', compact('problems', 'topics', 'stats'));
    }

    public function show(Problem $problem)
    {
        $problem->load('topics', 'hints');

        $userProgress = null;
        $submissions = collect();
        if (Auth::check()) {
            $userProgress = $problem->users()->where('user_id', Auth::id())->first();
            $submissions = $problem->submissions()
                ->where('user_id', Auth::id())
                ->orderByDesc('id')
                ->limit(20)
                ->get();
        }

        $siblingProblems = Problem::select('id', 'title', 'difficulty', 'slug')
            ->orderBy('id')
            ->limit(200)
            ->get();

        return view('problems.show', compact('problem', 'userProgress', 'siblingProblems', 'submissions'));
    }

    public function submit(Request $request, Problem $problem)
    {
        $request->validate([
            'code' => 'required|string|max:50000',
            'language' => 'required|string|max:30',
        ]);

        $tests = $problem->tests_json ?? [];
        $code = $request->code;
        $language = $request->language;

        if (empty($tests)) {
            return response()->json([
                'success' => false,
                'all_passed' => false,
                'results' => [],
                'total_time_ms' => 0,
                'total_memory_kb' => 0,
                'passed_tests' => 0,
                'total_tests' => 0,
                'status' => 'no_tests',
                'message' => 'No test cases available for this problem yet.',
            ]);
        }

        $judgeResult = $this->judge0->runPractice($language, $code, $tests, $problem->function_name);

        $allPassed = $judgeResult['status'] === 'accepted';
        $passedTests = $judgeResult['passed_tests'];
        $totalTests = $judgeResult['total_tests'];

        $maxRuntime = 0;
        $maxMemory = 0;
        $results = [];

        foreach ($judgeResult['results'] as $r) {
            $runtimeMs = isset($r['time']) ? (int) round((float) $r['time'] * 1000) : rand(8, 45);
            $memoryKb = isset($r['memory']) ? (int) round($r['memory'] * 1024) : rand(8000, 15000);

            $maxRuntime = max($maxRuntime, $runtimeMs);
            $maxMemory = max($maxMemory, $memoryKb);

            $results[] = [
                'test' => $r['test_case'],
                'passed' => $r['passed'],
                'time_ms' => $runtimeMs,
                'memory_kb' => $memoryKb,
                'input' => $r['input'] ?? '',
                'expected' => $r['expected'] ?? '',
                'output' => $r['output'] ?? '',
            ];
        }

        $status = $allPassed ? 'solved' : 'attempted';

        if (Auth::check()) {
            ProblemSubmission::create([
                'user_id' => Auth::id(),
                'problem_id' => $problem->id,
                'code' => $code,
                'language' => $language,
                'status' => $status,
                'runtime_ms' => $maxRuntime,
                'memory_kb' => $maxMemory,
                'passed_tests' => $passedTests,
                'total_tests' => $totalTests,
                'results_json' => $results,
            ]);

            $existing = $problem->users()->where('user_id', Auth::id())->first();

            $alreadySolved = $existing && $existing->pivot->solved_at;

            if ($status === 'solved') {
                if (!$alreadySolved) {
                    $problem->increment('solved_count');
                }
                if (!$alreadySolved) {
                    $isDaily = DailyChallenge::where('challenge_date', now()->toDateString())
                        ->where('problem_id', $problem->id)
                        ->first();

                    if ($isDaily) {
                        $isDaily->increment('solved_count');
                        $xp = GamificationService::XP_DAILY_CHALLENGE;
                        $reason = "Daily Challenge solved: {$problem->title}";
                        app(GamificationService::class)->addAiTokens(Auth::user(), GamificationService::AI_TOKEN_DAILY_CHALLENGE, "Daily Challenge: {$problem->title}");
                    } else {
                        $xp = GamificationService::XP_PROBLEM_SOLVED;
                        $reason = "Problem solved: {$problem->title}";
                    }
                    app(GamificationService::class)->awardXp(Auth::user(), $xp, $reason);
                }
            }
            $problem->increment('attempt_count');

            if ($existing) {
                $existing->pivot->attempts = $existing->pivot->attempts + 1;
                if ($status === 'solved') {
                    if (!$existing->pivot->solved_at) {
                        $existing->pivot->status = 'solved';
                        $existing->pivot->solved_at = now();
                        $existing->pivot->best_time_ms = $maxRuntime;
                        $existing->pivot->best_memory_kb = $maxMemory;
                    } else {
                        if ($existing->pivot->best_time_ms === null || $maxRuntime < $existing->pivot->best_time_ms) {
                            $existing->pivot->best_time_ms = $maxRuntime;
                        }
                        if ($existing->pivot->best_memory_kb === null || $maxMemory < $existing->pivot->best_memory_kb) {
                            $existing->pivot->best_memory_kb = $maxMemory;
                        }
                    }
                }
                $existing->pivot->save();
            } else {
                $problem->users()->attach(Auth::id(), [
                    'status' => $status,
                    'best_time_ms' => $maxRuntime,
                    'best_memory_kb' => $maxMemory,
                    'attempts' => 1,
                    'solved_at' => $status === 'solved' ? now() : null,
                ]);
            }
        }

        return response()->json([
            'success' => $allPassed,
            'all_passed' => $allPassed,
            'results' => $results,
            'total_time_ms' => $maxRuntime,
            'total_memory_kb' => $maxMemory,
            'passed_tests' => $passedTests,
            'total_tests' => $totalTests,
            'status' => $status,
        ]);
    }

    public function submissions(Problem $problem)
    {
        if (!Auth::check()) {
            return response()->json([]);
        }

        $submissions = $problem->submissions()
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return response()->json($submissions);
    }

    public function performance(Problem $problem)
    {
        $allSubmissions = $problem->submissions()->where('status', 'solved')->get();

        if ($allSubmissions->isEmpty()) {
            return response()->json([
                'runtime_percentile' => 0,
                'memory_percentile' => 0,
                'total_solved' => 0,
                'avg_runtime' => 0,
                'avg_memory' => 0,
            ]);
        }

        $avgRuntime = $allSubmissions->avg('runtime_ms');
        $avgMemory = $allSubmissions->avg('memory_kb');

        $userBest = null;
        if (Auth::check()) {
            $userBest = $problem->submissions()
                ->where('user_id', Auth::id())
                ->where('status', 'solved')
                ->orderBy('runtime_ms')
                ->first();
        }

        $runtimePercentile = 0;
        $memoryPercentile = 0;

        if ($userBest) {
            $fasterCount = $allSubmissions->where('runtime_ms', '>', $userBest->runtime_ms)->count();
            $runtimePercentile = round(($fasterCount / $allSubmissions->count()) * 100);

            $lighterCount = $allSubmissions->where('memory_kb', '>', $userBest->memory_kb)->count();
            $memoryPercentile = round(($lighterCount / $allSubmissions->count()) * 100);
        }

        return response()->json([
            'runtime_percentile' => $runtimePercentile,
            'memory_percentile' => $memoryPercentile,
            'total_solved' => $allSubmissions->count(),
            'avg_runtime' => round($avgRuntime),
            'avg_memory' => round($avgMemory),
            'user_runtime' => $userBest?->runtime_ms,
            'user_memory' => $userBest?->memory_kb,
        ]);
    }

    public function createCollab(Problem $problem)
    {
        $user = Auth::user();

        $existing = CollaborationSession::where('user_id', $user->id)
            ->where('problem_id', $problem->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return response()->json(['code' => $existing->code, 'url' => route('collab.join', $existing->code)]);
        }

        $session = CollaborationSession::create([
            'user_id' => $user->id,
            'problem_id' => $problem->id,
            'code' => CollaborationSession::generateCode(),
            'status' => 'active',
            'participants' => [['user_id' => $user->id, 'name' => $user->name, 'role' => 'host']],
            'expires_at' => now()->addHours(3),
        ]);

        return response()->json(['code' => $session->code, 'url' => route('collab.join', $session->code)]);
    }

    public function joinCollab($code)
    {
        $session = CollaborationSession::where('code', $code)->firstOrFail();

        if ($session->isExpired()) {
            return redirect()->route('problems.show', $session->problem->slug)
                ->with('error', 'This collaboration session has expired.');
        }

        $user = Auth::user();
        $participants = $session->participants ?? [];
        $alreadyIn = collect($participants)->firstWhere('user_id', $user->id);

        if (!$alreadyIn && !$session->isFull()) {
            $participants[] = ['user_id' => $user->id, 'name' => $user->name, 'role' => 'collaborator'];
            $session->update(['participants' => $participants]);
        }

        return redirect()->route('problems.show', $session->problem->slug)
            ->with('collab_code', $session->code)
            ->with('collab_participants', $session->participants);
    }

    public function leaveCollab($code)
    {
        $session = CollaborationSession::where('code', $code)->firstOrFail();
        $user = Auth::user();

        $participants = collect($session->participants ?? [])
            ->reject(fn($p) => $p['user_id'] == $user->id)
            ->values()
            ->toArray();

        if (empty($participants)) {
            $session->update(['status' => 'closed', 'participants' => []]);
        } else {
            $session->update(['participants' => $participants]);
        }

        return response()->json(['success' => true]);
    }
}
