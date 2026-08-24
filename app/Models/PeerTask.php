<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeerTask extends Model
{
    protected $fillable = [
        'room_id',
        'title',
        'description',
        'type',
        'difficulty',
        'starter_code',
        'language',
        'status',
        'solution',
        'score',
        'feedback',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'score' => 'integer',
        'sort_order' => 'integer',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(PeerInterviewRoom::class, 'room_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'done';
    }

    public function isReview(): bool
    {
        return $this->status === 'review';
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
