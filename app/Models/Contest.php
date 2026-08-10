<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contest extends Model
{
    protected $fillable = [
        'title',
        'description',
        'difficulty',
        'status',
        'start_time',
        'end_time',
        'time_limit',
        'max_participants',
        'created_by',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function problems(): HasMany
    {
        return $this->hasMany(ContestProblem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ContestSubmission::class, 'contest_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && (!$this->start_time || $this->start_time->isPast())
            && (!$this->end_time || $this->end_time->isFuture());
    }

    public function getTimeRemainingAttribute(): ?int
    {
        if (!$this->end_time) return null;
        $remaining = now()->diffInSeconds($this->end_time, false);
        return max(0, $remaining);
    }
}
