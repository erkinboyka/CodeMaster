<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DailyChallenge extends Model
{
    protected $fillable = [
        'challenge_date', 'problem_id', 'submissions_count', 'solved_count',
    ];

    protected $casts = [
        'challenge_date' => 'date',
    ];

    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }

    public static function today(): ?self
    {
        return Cache::remember('daily_challenge_today', 3600, function () {
            return static::where('challenge_date', Carbon::today())->first();
        });
    }

    public static function todaysProblem(): ?Problem
    {
        return static::today()?->problem;
    }

    public static function streak(int $userId): int
    {
        $key = "daily_streak_{$userId}";
        return Cache::remember($key, 3600, function () use ($userId) {
            $streak = 0;
            $date = Carbon::today();

            while (true) {
                $challenge = static::where('challenge_date', $date)->first();
                if (!$challenge) break;

                $solved = \DB::table('problem_user')
                    ->where('user_id', $userId)
                    ->where('problem_id', $challenge->problem_id)
                    ->exists();

                if (!$solved) break;

                $streak++;
                $date = $date->subDay();
            }

            return $streak;
        });
    }
}
