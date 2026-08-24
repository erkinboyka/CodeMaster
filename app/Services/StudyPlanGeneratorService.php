<?php

namespace App\Services;

use App\Models\Problem;
use App\Models\User;
use App\Models\UserStudyPlan;

class StudyPlanGeneratorService
{
    const GOALS = [
        'balanced' => 'Balanced practice across all topics',
        'interview_prep' => 'Interview preparation',
        'algorithms' => 'Algorithm mastery',
        'data_structures' => 'Data structures deep dive',
        'easy_start' => 'Easy start for beginners',
        'speed_run' => 'Quick problem solving',
    ];

    const DIFFICULTY_DISTRIBUTION = [
        'easy'   => ['easy' => 60, 'medium' => 30, 'hard' => 10],
        'medium' => ['easy' => 20, 'medium' => 50, 'hard' => 30],
        'hard'   => ['easy' => 5, 'medium' => 35, 'hard' => 60],
    ];

    const PROBLEM_COUNTS = [
        'easy_start' => 15,
        'balanced' => 30,
        'interview_prep' => 40,
        'algorithms' => 35,
        'data_structures' => 35,
        'speed_run' => 20,
    ];

    public function generate(User $user, string $goal, string $difficulty, int $dailyGoal = 3, ?string $deadline = null): UserStudyPlan
    {
        $level = $user->level;
        $effectiveDifficulty = $this->adjustDifficultyByLevel($difficulty, $level);
        $count = self::PROBLEM_COUNTS[$goal] ?? 30;
        $problems = $this->selectProblems($goal, $effectiveDifficulty, $count, $user);

        $plan = UserStudyPlan::create([
            'user_id' => $user->id,
            'title' => $this->generateTitle($goal, $effectiveDifficulty),
            'goal' => $goal,
            'difficulty' => $effectiveDifficulty,
            'daily_goal' => $dailyGoal,
            'deadline' => $deadline,
            'total_problems' => $problems->count(),
            'started_at' => now(),
        ]);

        foreach ($problems as $index => $problem) {
            $plan->problems()->attach($problem->id, [
                'order_num' => $index + 1,
                'is_solved' => false,
            ]);
        }

        $this->createDailyGoal($plan);

        return $plan->fresh(['problems']);
    }

    private function adjustDifficultyByLevel(string $difficulty, int $level): string
    {
        if ($level <= 4) return 'easy';
        if ($level <= 14) return $difficulty === 'hard' ? 'medium' : $difficulty;
        return $difficulty;
    }

    private function selectProblems(string $goal, string $difficulty, int $count, User $user): \Illuminate\Support\Collection
    {
        $query = Problem::query();

        $solvedIds = $user->problems()->pluck('problems.id')->toArray();
        $query->whereNotIn('id', $solvedIds);

        if ($goal === 'interview_prep') {
            $query->where('difficulty', '!=', 'easy');
        } elseif ($goal === 'easy_start') {
            $query->where('difficulty', 'easy');
        } elseif ($goal === 'algorithms') {
            $query->whereHas('topics', function ($q) {
                $q->whereIn('slug', ['algorithms', 'sorting', 'searching', 'dynamic-programming', 'recursion']);
            });
        } elseif ($goal === 'data_structures') {
            $query->whereHas('topics', function ($q) {
                $q->whereIn('slug', ['arrays', 'linked-lists', 'stacks', 'queues', 'trees', 'graphs', 'hash-tables', 'heaps']);
            });
        } elseif ($goal === 'speed_run') {
            $query->where('difficulty', '!=', 'hard');
        }

        $distribution = self::DIFFICULTY_DISTRIBUTION[$difficulty];
        $problems = collect();

        foreach ($distribution as $diff => $percentage) {
            $take = max(1, (int) round($count * $percentage / 100));
            $subset = $query->clone()
                ->where('difficulty', $diff)
                ->inRandomOrder()
                ->limit($take)
                ->get();
            $problems = $problems->concat($subset);
        }

        if ($problems->count() < $count) {
            $remaining = $count - $problems->count();
            $extra = Problem::whereNotIn('id', $solvedIds)
                ->whereNotIn('id', $problems->pluck('id'))
                ->inRandomOrder()
                ->limit($remaining)
                ->get();
            $problems = $problems->concat($extra);
        }

        return $problems->shuffle()->take($count)->values();
    }

    private function generateTitle(string $goal, string $difficulty): string
    {
        $goalTitle = match($goal) {
            'balanced' => 'Balanced Practice',
            'interview_prep' => 'Interview Preparation',
            'algorithms' => 'Algorithm Mastery',
            'data_structures' => 'Data Structures',
            'easy_start' => 'Easy Start',
            'speed_run' => 'Speed Run',
            default => 'Custom Plan',
        };

        $diffTitle = ucfirst($difficulty);
        return "{$goalTitle} ({$diffTitle})";
    }

    public function createDailyGoal(UserStudyPlan $plan): void
    {
        $today = now()->toDateString();
        $exists = $plan->dailyGoals()->whereDate('date', $today)->exists();

        if (!$exists) {
            $plan->dailyGoals()->create([
                'date' => $today,
                'target' => $plan->daily_goal,
                'completed' => 0,
                'is_met' => false,
            ]);
        }
    }

    public function markProblemSolved(UserStudyPlan $plan, int $problemId, int $timeSpentMs = 0): void
    {
        $plan->problems()->updateExistingPivot($problemId, [
            'is_solved' => true,
            'time_spent_ms' => $timeSpentMs,
            'solved_at' => now(),
        ]);

        $plan->increment('completed_problems');

        $todayGoal = $plan->todayGoal();
        if ($todayGoal) {
            $todayGoal->increment('completed');
            if ($todayGoal->completed >= $todayGoal->target) {
                $todayGoal->update(['is_met' => true]);
            }
        }

        if ($plan->completed_problems >= $plan->total_problems) {
            $plan->update(['completed_at' => now()]);
        }
    }
}
