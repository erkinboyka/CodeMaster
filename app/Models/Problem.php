<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Problem extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'difficulty', 'points',
        'solved_count', 'attempt_count',
        'input_example', 'output_example', 'constraints',
        'starter_code', 'language', 'tests_json',
        'time_limit', 'memory_limit', 'is_premium',
        'source', 'source_url',
    ];

    protected $casts = [
        'tests_json' => 'array',
        'is_premium' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Problem $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(ProblemTopic::class, 'problem_problem_topic');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'problem_user')
            ->withPivot('status', 'best_time_ms', 'best_memory_kb', 'attempts', 'solved_at');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ProblemSubmission::class);
    }

    public function hints(): HasMany
    {
        return $this->hasMany(ProblemHint::class)->orderBy('order_num');
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(\App\Models\CommunityPost::class);
    }

    public function getAcceptanceRateAttribute(): float
    {
        if ($this->attempt_count === 0) return 0;
        return round(($this->solved_count / $this->attempt_count) * 100, 1);
    }

    public function getDifficultyColorAttribute(): string
    {
        return match($this->difficulty) {
            'easy' => '#22c55e',
            'medium' => '#eab308',
            'hard' => '#ef4444',
            default => '#6b7280',
        };
    }

    public function sources(): HasMany
    {
        return $this->hasMany(\App\Models\ProblemSource::class);
    }

    public function isSolvedBy(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->where('status', 'solved')->exists();
    }

    public function isAttemptedBy(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }
}
