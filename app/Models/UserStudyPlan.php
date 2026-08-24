<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class UserStudyPlan extends Model
{
    protected $fillable = [
        'user_id', 'title', 'goal', 'difficulty', 'daily_goal',
        'deadline', 'total_problems', 'completed_problems',
        'started_at', 'completed_at',
    ];

    protected $casts = [
        'deadline' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function problems(): BelongsToMany
    {
        return $this->belongsToMany(Problem::class, 'user_study_plan_problems')
            ->withPivot('order_num', 'is_solved', 'time_spent_ms', 'solved_at')
            ->orderByPivot('order_num');
    }

    public function dailyGoals(): HasMany
    {
        return $this->hasMany(UserStudyPlanDailyGoal::class, 'user_study_plan_id');
    }

    public function progressPercent(): int
    {
        if ($this->total_problems === 0) return 0;
        return round(($this->completed_problems / $this->total_problems) * 100);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function daysLeft(): ?int
    {
        if (!$this->deadline) return null;
        return (int) now()->diffInDays($this->deadline, false);
    }

    public function todayGoal(): ?UserStudyPlanDailyGoal
    {
        return $this->dailyGoals()->whereDate('date', now()->toDateString())->first();
    }

    public function todayCompleted(): int
    {
        return $this->todayGoal()?->completed ?? 0;
    }

    public function isGoalMetToday(): bool
    {
        return $this->todayGoal()?->is_met ?? false;
    }

    public function streak(): int
    {
        $streak = 0;
        $date = now()->toDateString();

        while (true) {
            $goal = $this->dailyGoals()->whereDate('date', $date)->where('is_met', true)->first();
            if (!$goal) break;
            $streak++;
            $date = now()->subDay()->toDateString();
            $date = \Carbon\Carbon::parse($date)->subDays($streak)->toDateString();
        }

        return $streak;
    }

    public function scopeActive($query)
    {
        return $query->whereNull('completed_at');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }
}
