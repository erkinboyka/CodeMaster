<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\ContestProblem;
use App\Models\ContestSubmission;
use App\Services\Judge0Service;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContestController extends Controller
{
    protected $judge0Service;
    protected $gamificationService;

    public function __construct(Judge0Service $judge0Service, GamificationService $gamificationService)
    {
        $this->judge0Service = $judge0Service;
        $this->gamificationService = $gamificationService;
    }

    public function index()
    {
        $contests = Contest::withCount('problems')
            ->withCount('submissions')
            ->latest()
            ->paginate(20);

        $activityData = [];
        if (Auth::check()) {
            $activityData = ContestSubmission::where('user_id', Auth::id())
                ->where('created_at', '>=', now()->subYear())
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date')
                ->toArray();
        }

        return view('contests.index', compact('contests', 'activityData'));
    }

    public function show($id)
    {
        $contest = Contest::with(['problems' => fn($q) => $q->orderBy('order_num')])
            ->withCount('submissions')
            ->findOrFail($id);

        $problems = $contest->problems;

        $userSubmissions = [];
        $activityData = [];
        if (Auth::check()) {
            $subs = ContestSubmission::where('contest_id', $id)
                ->where('user_id', Auth::id())
                ->orderByDesc('status')
                ->orderBy('id')
                ->get()
                ->keyBy('task_id');
            $userSubmissions = $subs;

            $activityData = ContestSubmission::where('user_id', Auth::id())
                ->where('created_at', '>=', now()->subYear())
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date')
                ->toArray();
        }

        return view('contests.show', compact('contest', 'problems', 'userSubmissions', 'activityData'));
    }

    public function showProblem($contestId, $problemId)
    {
        $contest = Contest::with(['problems' => fn($q) => $q->orderBy('order_num')])
            ->findOrFail($contestId);

        $problem = $contest->problems->firstWhere('id', $problemId);
        if (!$problem) abort(404);

        $problems = $contest->problems;

        $userSubmissions = [];
        if (Auth::check()) {
            $subs = ContestSubmission::where('contest_id', $contestId)
                ->where('user_id', Auth::id())
                ->orderByDesc('status')
                ->orderBy('id')
                ->get()
                ->keyBy('task_id');
            $userSubmissions = $subs;
        }

        $userPassed = isset($userSubmissions[$problemId]) && $userSubmissions[$problemId]->status === 'accepted';

        return view('contests.problem', compact('contest', 'problem', 'problems', 'userSubmissions', 'userPassed'));
    }

    public function destroyProblem($contestId, $problemId)
    {
        $contest = Contest::findOrFail($contestId);
        if (Auth::id() !== $contest->created_by) abort(403);

        ContestProblem::where('contest_id', $contestId)->where('id', $problemId)->delete();

        return redirect()->route('contests.show', $contestId)->with('success', 'Problem deleted');
    }

    public function create()
    {
        return view('contests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'status' => 'required|in:draft,active,finished',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'time_limit' => 'required|integer|min:1',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        $validated['created_by'] = Auth::id();

        $contest = Contest::create($validated);

        return redirect()->route('contests.show', $contest->id)
            ->with('success', 'Контест создан! Добавьте задачи.');
    }

    public function edit($id)
    {
        $contest = Contest::findOrFail($id);

        if ($contest->created_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('contests.edit', compact('contest'));
    }

    public function update(Request $request, $id)
    {
        $contest = Contest::findOrFail($id);

        if ($contest->created_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'status' => 'required|in:draft,active,finished',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'time_limit' => 'required|integer|min:1',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        $contest->update($validated);

        return redirect()->route('contests.show', $contest->id)
            ->with('success', 'Контест обновлён!');
    }

    public function destroy($id)
    {
        $contest = Contest::findOrFail($id);

        if ($contest->created_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $contest->delete();

        return redirect()->route('contests.index')
            ->with('success', 'Контест удалён!');
    }

    public function storeProblem(Request $request, $contestId)
    {
        $contest = Contest::findOrFail($contestId);

        if ($contest->created_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'points' => 'required|integer|min:1',
            'input_example' => 'nullable|string',
            'output_example' => 'nullable|string',
            'constraints' => 'nullable|string',
            'starter_code' => 'nullable|string',
            'language' => 'required|string|max:50',
            'tests_json' => 'nullable|string',
            'time_limit' => 'required|integer|min:1',
            'memory_limit' => 'required|integer|min:64',
        ]);

        $maxOrder = $contest->problems()->max('order_num') ?? 0;
        $validated['contest_id'] = $contestId;
        $validated['order_num'] = $maxOrder + 1;

        if (!empty($validated['tests_json'])) {
            $validated['tests_json'] = json_decode($validated['tests_json'], true);
        }

        ContestProblem::create($validated);

        return redirect()->route('contests.show', $contestId)
            ->with('success', 'Задача добавлена!');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'contest_id' => 'required|integer',
            'problem_id' => 'required|integer',
            'code' => 'required|string|max:50000',
            'language' => 'required|string|max:30',
        ]);

        $contest = Contest::findOrFail($validated['contest_id']);

        if (!$contest->isActive()) {
            return response()->json(['error' => 'Контест не активен'], 403);
        }

        $problem = ContestProblem::where('contest_id', $validated['contest_id'])
            ->findOrFail($validated['problem_id']);

        $result = $this->judge0Service->runPractice(
            $validated['language'],
            $validated['code'],
            $problem->tests_json ?? [],
            $problem->function_name
        );

        $rawStatus = $result['status'] ?? 'error';
        if (is_array($rawStatus)) {
            $statusMap = [
                'Accepted' => 'accepted',
                'Wrong Answer' => 'wrong_answer',
                'Time Limit Exceeded' => 'time_limit_exceeded',
                'Memory Limit Exceeded' => 'memory_limit_exceeded',
                'Runtime Error (NZEC)' => 'runtime_error',
                'Runtime Error (SIGSEGV)' => 'runtime_error',
                'Compilation Error' => 'compilation_error',
                'Internal Error' => 'error',
            ];
            $statusDesc = $rawStatus['description'] ?? 'error';
            $normalizedStatus = $statusMap[$statusDesc] ?? strtolower(str_replace(' ', '_', $statusDesc));
        } else {
            $normalizedStatus = is_string($rawStatus) ? strtolower(str_replace(' ', '_', $rawStatus)) : 'error';
        }

        $submission = ContestSubmission::create([
            'contest_id' => $validated['contest_id'],
            'task_id' => $validated['problem_id'],
            'user_id' => Auth::id(),
            'code' => $validated['code'],
            'status' => $normalizedStatus,
        ]);

        if ($normalizedStatus === 'accepted') {
            $userId = Auth::id();
            $xpEarned = DB::transaction(function () use ($userId, $contest, $validated) {
                $alreadySolved = ContestSubmission::where('user_id', $userId)
                    ->where('contest_id', $validated['contest_id'])
                    ->where('task_id', $validated['problem_id'])
                    ->where('status', 'accepted')
                    ->exists();

                if (!$alreadySolved) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        return $this->gamificationService->awardContestXp($user, $contest->title);
                    }
                }
                return 0;
            });
        }

        return response()->json([
            'submission' => $submission,
            'result' => $result,
        ]);
    }

    public function leaderboard($id)
    {
        $contest = Contest::withCount('problems')->findOrFail($id);

        $submissions = ContestSubmission::where('contest_id', $id)
            ->selectRaw('user_id, COUNT(DISTINCT task_id) as solved, MAX(created_at) as last_submit')
            ->where('status', 'accepted')
            ->groupBy('user_id')
            ->with('user:id,name')
            ->orderByDesc('solved')
            ->orderBy('last_submit')
            ->get();

        return view('contests.leaderboard', compact('contest', 'submissions'));
    }
}
