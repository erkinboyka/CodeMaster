<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CollaborationSession extends Model
{
    protected $fillable = [
        'user_id', 'problem_id', 'code', 'status', 'participants', 'expires_at',
    ];

    protected $casts = [
        'participants' => 'array',
        'expires_at' => 'datetime',
    ];

    public static function generateCode(): string
    {
        return strtoupper(Str::random(8));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast() || $this->status === 'expired';
    }

    public function isFull(): bool
    {
        return count($this->participants ?? []) >= 4;
    }
}
