<?php

namespace App\Http\Controllers;

use App\Models\UserNote;
use App\Models\ProblemList;
use App\Models\Problem;
use App\Models\ProblemTopic;
use App\Models\ProblemSubmission;
use App\Models\UserCourseProgress;
use App\Models\UserLessonProgress;
use App\Models\RoadmapUserProgress;
use App\Models\ContestSubmission;
use App\Models\PracticeSubmission;
use App\Models\UserActivity;
use App\Models\UserStudyPlan;
use App\Models\RatingHistory;
use App\Models\Certificate;
use App\Models\RoadmapCertificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProfilePagesController extends Controller
{
    public function myLists()
    {
        $userId = Auth::id();
        $lists = ProblemList::whereHas('users', fn($q) => $q->where('user_id', $userId))
            ->withCount('problems')
            ->get();

        foreach ($lists as $list) {
            $list->user_progress = $list->userProgress();
        }

        $defaultList = ProblemList::firstOrCreate(
            ['slug' => 'favorite'],
            ['title' => 'Favorite', 'icon' => 'fa-star', 'color' => '#f59e0b', 'problems_count' => 0]
        );

        if (!$defaultList->users()->where('user_id', $userId)->exists()) {
            $defaultList->users()->attach($userId);
        }

        $lists = ProblemList::whereHas('users', fn($q) => $q->where('user_id', $userId))
            ->withCount('problems')
            ->get();

        foreach ($lists as $list) {
            $list->user_progress = $list->userProgress();
        }

        return view('profile.my-lists', compact('lists', 'defaultList'));
    }

    public function showList($slug)
    {
        $userId = Auth::id();

        $list = ProblemList::where('slug', $slug)
            ->with(['problems' => function ($q) {
                $q->withPivot('order_num');
            }])
            ->withCount('problems')
            ->firstOrFail();

        $isMember = $list->users()->where('user_id', $userId)->exists();
        if (!$isMember) {
            $list->users()->attach($userId);
        }

        $list->user_progress = $list->userProgress();

        $listProblemIds = $list->problems->pluck('id')->toArray();
        $userProblems = [];
        if (!empty($listProblemIds)) {
            $rows = DB::table('problem_user')
                ->where('user_id', $userId)
                ->whereIn('problem_id', $listProblemIds)
                ->get();
            foreach ($rows as $row) {
                $userProblems[$row->problem_id] = $row->status;
            }
        }

        $solved = count(array_filter($userProblems, fn($s) => $s === 'solved'));
        $attempting = count(array_filter($userProblems, fn($s) => $s === 'attempted'));
        $topics = ProblemTopic::withCount('problems')->get();

        $allLists = ProblemList::whereHas('users', fn($q) => $q->where('user_id', $userId))
            ->withCount('problems')
            ->get();
        foreach ($allLists as $l) {
            $l->user_progress = $l->userProgress();
        }

        return view('profile.my-lists-show', compact('list', 'userProblems', 'solved', 'attempting', 'topics', 'allLists'));
    }

    public function createList(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $list = ProblemList::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'icon' => 'fa-list-ol',
            'color' => 'var(--accent)',
            'problems_count' => 0,
        ]);

        $list->users()->attach(Auth::id());

        return redirect()->route('profile.my-lists.show', $list->slug);
    }

    public function updateList(Request $request, $id)
    {
        $list = ProblemList::where('id', $id)
            ->whereHas('users', fn($q) => $q->where('user_id', Auth::id()))
            ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $list->update(['title' => $request->title]);

        return redirect()->route('profile.my-lists.show', $list->slug);
    }

    public function deleteList($id)
    {
        $list = ProblemList::where('id', $id)
            ->whereHas('users', fn($q) => $q->where('user_id', Auth::id()))
            ->firstOrFail();

        $list->users()->detach(Auth::id());

        return redirect()->route('profile.my-lists');
    }

    public function addProblems(Request $request, $id)
    {
        $list = ProblemList::where('id', $id)
            ->whereHas('users', fn($q) => $q->where('user_id', Auth::id()))
            ->firstOrFail();

        $request->validate([
            'problem_ids' => 'required|array',
            'problem_ids.*' => 'integer|exists:problems,id',
        ]);

        $existing = $list->problems()->pluck('problem_list_problem.problem_id')->toArray();
        $newIds = array_diff($request->problem_ids, $existing);

        foreach ($newIds as $index => $problemId) {
            $list->problems()->attach($problemId, ['order_num' => $list->problems()->count() + $index + 1]);
        }

        $list->update(['problems_count' => $list->problems()->count()]);

        return response()->json(['success' => true, 'count' => $list->problems()->count()]);
    }

    public function removeProblem($listId, $problemId)
    {
        $list = ProblemList::where('id', $listId)
            ->whereHas('users', fn($q) => $q->where('user_id', Auth::id()))
            ->firstOrFail();

        $list->problems()->detach($problemId);
        $list->update(['problems_count' => $list->problems()->count()]);

        return response()->json(['success' => true]);
    }

    public function availableProblems($id)
    {
        $list = ProblemList::where('id', $id)->firstOrFail();

        $listProblemIds = $list->problems()->pluck('problems.id')->toArray();

        $userId = Auth::id();
        $problems = Problem::orderBy('id')->get();

        foreach ($problems as $problem) {
            $problem->in_list = in_array($problem->id, $listProblemIds);
            $userStatus = DB::table('problem_user')
                ->where('user_id', $userId)
                ->where('problem_id', $problem->id)
                ->first();
            $problem->user_status = $userStatus ? $userStatus->status : null;
        }

        return response()->json([
            'problems' => $problems,
            'list_problem_ids' => $listProblemIds,
        ]);
    }

    public function notebook()
    {
        $notes = UserNote::where('user_id', Auth::id())
            ->with('problem')
            ->orderByDesc('updated_at')
            ->get();

        // История попыток по задачам, к которым привязаны заметки:
        // problem_id => ['attempts' => N, 'solved' => bool, 'last' => Carbon|null]
        $subStats = [];
        $problemIds = $notes->whereNotNull('problem_id')->pluck('problem_id')->unique()->values()->all();
        if (!empty($problemIds)) {
            $subs = ProblemSubmission::where('user_id', Auth::id())
                ->whereIn('problem_id', $problemIds)
                ->orderByDesc('created_at')
                ->get(['problem_id', 'status', 'created_at']);
            foreach ($subs as $s) {
                if (!isset($subStats[$s->problem_id])) {
                    $subStats[$s->problem_id] = ['attempts' => 0, 'solved' => false, 'last' => $s->created_at];
                }
                $subStats[$s->problem_id]['attempts']++;
                if ($s->status === 'solved') {
                    $subStats[$s->problem_id]['solved'] = true;
                }
            }
        }

        return view('profile.notebook', compact('notes', 'subStats'));
    }

    public function storeNote(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'problem_id' => 'nullable|integer',
            'tags' => 'nullable|string|max:255',
        ]);

        $note = UserNote::create([
            'user_id' => Auth::id(),
            'problem_id' => $request->problem_id,
            'title' => $request->title,
            'content' => $request->content,
            'tags' => $request->tags,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'note_id' => $note->id]);
        }

        return redirect()->back()->with('success', 'Note saved!');
    }

    public function deleteNote($id)
    {
        UserNote::where('id', $id)->where('user_id', Auth::id())->delete();
        return redirect()->back();
    }

    public function progress()
    {
        $userId = Auth::id();
        $user = Auth::user();

        $submissions = ProblemSubmission::where('user_id', $userId)->orderByDesc('created_at')->get();
        $solvedCount = ProblemSubmission::where('user_id', $userId)->where('status', 'solved')
            ->distinct('problem_id')->count('problem_id');
        $totalAttempts = $submissions->count();

        $easy = ProblemSubmission::where('user_id', $userId)->where('status', 'solved')
            ->whereHas('problem', fn($q) => $q->where('difficulty', 'easy'))
            ->distinct('problem_id')->count('problem_id');
        $medium = ProblemSubmission::where('user_id', $userId)->where('status', 'solved')
            ->whereHas('problem', fn($q) => $q->where('difficulty', 'medium'))
            ->distinct('problem_id')->count('problem_id');
        $hard = ProblemSubmission::where('user_id', $userId)->where('status', 'solved')
            ->whereHas('problem', fn($q) => $q->where('difficulty', 'hard'))
            ->distinct('problem_id')->count('problem_id');

        $totalProblems = Problem::count();
        $acceptanceRate = $totalAttempts > 0 ? round(($solvedCount / max(1, $totalAttempts)) * 100) : 0;

        $solvedByDay = ProblemSubmission::where('user_id', $userId)->where('status', 'solved')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(DISTINCT problem_id) as count'))
            ->groupBy('day')->pluck('count', 'day')->toArray();

        $langStats = ProblemSubmission::where('user_id', $userId)
            ->select('language', DB::raw('COUNT(*) as count'))
            ->groupBy('language')->pluck('count', 'language')->toArray();

        $dailyActivity = ProblemSubmission::where('user_id', $userId)
            ->where('created_at', '>=', Carbon::now()->subDays(14))
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as count'))
            ->groupBy('day')->pluck('count', 'day')->toArray();

        $coursesEnrolled = UserCourseProgress::where('user_id', $userId)->count();
        $coursesCompleted = UserCourseProgress::where('user_id', $userId)->where('completed', true)->count();
        $lessonsCompleted = UserLessonProgress::where('user_id', $userId)->where('completed', true)->count();
        $avgCourseProgress = UserCourseProgress::where('user_id', $userId)->avg('progress') ?? 0;

        $courseProgressList = UserCourseProgress::where('user_id', $userId)
            ->with('course')
            ->orderByDesc('progress')
            ->limit(10)
            ->get()
            ->map(fn($cp) => [
                'title' => $cp->course->title ?? 'Course',
                'progress' => $cp->progress,
                'completed' => $cp->completed,
            ]);

        $roadmapNodesCompleted = RoadmapUserProgress::where('user_id', $userId)->count();
        $roadmapCerts = RoadmapCertificate::where('user_id', $userId)->count();

        $contestsParticipated = ContestSubmission::where('user_id', $userId)
            ->distinct('contest_id')->count('contest_id');
        $contestSubmissionsCount = ContestSubmission::where('user_id', $userId)->count();

        $practiceTotal = PracticeSubmission::where('user_id', $userId)->count();
        $practicePassed = PracticeSubmission::where('user_id', $userId)->where('passed', true)->count();
        $practiceRate = $practiceTotal > 0 ? round(($practicePassed / $practiceTotal) * 100) : 0;

        $ratingHistory = RatingHistory::where('user_id', $userId)
            ->orderBy('created_at')
            ->get()
            ->map(fn($r) => ['date' => $r->created_at->format('M d'), 'rating' => $r->rating_after]);

        $xpByDay = UserActivity::where('user_id', $userId)->where('activity_type', 'xp_earned')
            ->where('activity_time', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(activity_time) as day'), DB::raw('SUM(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(activity_text, " ", 2), "+", -1) AS UNSIGNED)) as xp'))
            ->groupBy('day')->pluck('xp', 'day')->toArray();

        $activityByType = UserActivity::where('user_id', $userId)
            ->select('activity_type', DB::raw('COUNT(*) as count'))
            ->groupBy('activity_type')->pluck('count', 'activity_type')->toArray();

        $studyPlans = UserStudyPlan::where('user_id', $userId)->count();
        $studyPlansActive = UserStudyPlan::where('user_id', $userId)->active()->count();

        $certificates = Certificate::where('user_id', $userId)->count();

        $skills = $user->skills->map(fn($s) => ['name' => $s->skill_name, 'level' => $s->skill_level]);

        $streak = $user->streak_count ?? 0;
        $longestStreak = $user->longest_streak ?? 0;

        $subByStatus = ProblemSubmission::where('user_id', $userId)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->pluck('count', 'status')->toArray();

        $recentSubmissions = $submissions->take(20);

        $avgRuntime = ProblemSubmission::where('user_id', $userId)->where('status', 'solved')
            ->avg('runtime_ms') ?? 0;
        $avgMemory = ProblemSubmission::where('user_id', $userId)->where('status', 'solved')
            ->avg('memory_kb') ?? 0;

        $solvedPerDifficulty = [
            'easy' => $easy,
            'medium' => $medium,
            'hard' => $hard,
        ];

        return view('profile.progress', compact(
            'user', 'solvedCount', 'totalAttempts', 'totalProblems', 'acceptanceRate',
            'easy', 'medium', 'hard', 'solvedPerDifficulty',
            'solvedByDay', 'langStats', 'dailyActivity',
            'coursesEnrolled', 'coursesCompleted', 'lessonsCompleted', 'avgCourseProgress', 'courseProgressList',
            'roadmapNodesCompleted', 'roadmapCerts',
            'contestsParticipated', 'contestSubmissionsCount',
            'practiceTotal', 'practicePassed', 'practiceRate',
            'ratingHistory',
            'xpByDay', 'activityByType',
            'studyPlans', 'studyPlansActive', 'certificates',
            'skills', 'streak', 'longestStreak',
            'subByStatus', 'recentSubmissions',
            'avgRuntime', 'avgMemory'
        ));
    }

    public function points()
    {
        $user = Auth::user();

        $activities = UserActivity::where('user_id', $user->id)
            ->whereIn('activity_type', ['xp_earned', 'tokens_earned', 'tokens_spent', 'daily_bonus'])
            ->orderByDesc('activity_time')
            ->limit(50)
            ->get();

        $totalEarned = $activities->where('activity_type', '!=', 'tokens_spent')->count();
        $totalSpent = $activities->where('activity_type', 'tokens_spent')->count();

        return view('profile.points', compact('user', 'activities', 'totalEarned', 'totalSpent'));
    }
}
