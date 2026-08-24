<?php

namespace App\Http\Controllers;

use App\Models\ProblemList;
use App\Models\UserStudyPlan;
use App\Services\GamificationService;
use App\Services\StudyPlanGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyPlanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $plans = ProblemList::withCount('problems')->orderByDesc('problems_count')->get();
        $userPlans = UserStudyPlan::where('user_id', $user->id)
            ->withCount('problems')
            ->orderByDesc('created_at')
            ->get();

        foreach ($plans as $plan) {
            $plan->user_progress = $plan->userProgress();
        }

        return view('study-plans.index', compact('plans', 'userPlans'));
    }

    public function favorite()
    {
        $user = Auth::user();
        $plans = $user->favoriteStudyPlans()->withCount('problems')->get();

        foreach ($plans as $plan) {
            $plan->user_progress = $plan->userProgress();
        }

        return view('study-plans.favorite', compact('plans'));
    }

    public function toggleFavorite(ProblemList $plan)
    {
        $user = Auth::user();

        if ($plan->isFavorited()) {
            $plan->favoritedBy()->detach($user->id);
            return back()->with('success', __('Removed from favorites'));
        }

        $plan->favoritedBy()->attach($user->id);
        return back()->with('success', __('Added to favorites'));
    }

    public function show(ProblemList $plan)
    {
        $plan->load(['problems' => function ($q) {
            $q->withPivot('order_num');
        }]);

        $solvedIds = [];
        if (Auth::check()) {
            $solvedIds = Auth::user()->problems()
                ->whereIn('problem_id', $plan->problems->pluck('id'))
                ->pluck('problem_id')
                ->toArray();
        }

        foreach ($plan->problems as $problem) {
            $problem->is_solved = in_array($problem->id, $solvedIds);
        }

        $completedCount = count(array_filter($plan->problems->toArray(), fn($p) => $p['is_solved']));

        return view('study-plans.show', compact('plan', 'solvedIds', 'completedCount'));
    }

    public function create()
    {
        $user = Auth::user();
        $goals = StudyPlanGeneratorService::GOALS;

        return view('study-plans.create', compact('goals', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'goal' => 'required|in:' . implode(',', array_keys(StudyPlanGeneratorService::GOALS)),
            'difficulty' => 'required|in:easy,medium,hard',
            'daily_goal' => 'required|integer|min:1|max:10',
            'deadline' => 'nullable|date|after:today',
        ]);

        $user = Auth::user();
        $generator = app(StudyPlanGeneratorService::class);

        $plan = $generator->generate(
            $user,
            $request->goal,
            $request->difficulty,
            $request->daily_goal,
            $request->deadline
        );

        return redirect()->route('study-plans.user.show', $plan);
    }

    public function userShow(UserStudyPlan $plan)
    {
        if ($plan->user_id !== Auth::id()) {
            abort(403);
        }

        $plan->load(['problems' => function ($q) {
            $q->withPivot('order_num', 'is_solved', 'time_spent_ms', 'solved_at');
        }]);

        $todayGoal = $plan->todayGoal();

        return view('study-plans.user-show', compact('plan', 'todayGoal'));
    }

    public function userDestroy(UserStudyPlan $plan)
    {
        if ($plan->user_id !== Auth::id()) {
            abort(403);
        }

        $plan->delete();

        return redirect()->route('study-plans.index');
    }

    public function markSolved(Request $request, UserStudyPlan $plan)
    {
        if ($plan->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'problem_id' => 'required|exists:problems,id',
            'time_spent_ms' => 'nullable|integer|min:0',
        ]);

        $generator = app(StudyPlanGeneratorService::class);
        $generator->markProblemSolved($plan, $request->problem_id, $request->time_spent_ms ?? 0);

        $user = Auth::user();
        app(GamificationService::class)->awardXp($user, 5, 'Study plan problem solved');

        return back()->with('success', 'Problem marked as solved!');
    }
}
