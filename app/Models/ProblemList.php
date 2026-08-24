<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class ProblemList extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'icon', 'color', 'problems_count',
    ];

    public function problems(): BelongsToMany
    {
        return $this->belongsToMany(Problem::class, 'problem_list_problem')
            ->withPivot('order_num')
            ->orderByPivot('order_num');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'problem_list_user')
            ->withPivot('solved_count', 'completed_at');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'problem_list_user_favorite')
            ->withTimestamps();
    }

    public function isFavorited(): bool
    {
        if (!Auth::check()) return false;
        return $this->favoritedBy()->where('user_id', Auth::id())->exists();
    }

    public function isCompleted(): bool
    {
        if (!Auth::check()) return false;
        return $this->users()->where('user_id', Auth::id())->whereNotNull('problem_list_user.completed_at')->exists();
    }

    public function userProgress(): ?int
    {
        if (!Auth::check()) return 0;
        $pivot = $this->users()->where('user_id', Auth::id())->first()?->pivot;
        return $pivot ? $pivot->solved_count : 0;
    }

    public function progressPercent(): int
    {
        $total = $this->problems_count;
        if ($total === 0) return 0;
        return round(($this->userProgress() / $total) * 100);
    }
}
