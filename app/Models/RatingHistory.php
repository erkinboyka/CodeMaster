<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RatingHistory extends Model
{
    protected $table = 'rating_history';

    protected $fillable = [
        'user_id', 'contest_id', 'rating_before', 'rating_after',
        'rating_change', 'rank_position', 'participants_count',
    ];

    protected $casts = [
        'rating_change' => 'integer',
        'rank_position' => 'integer',
        'participants_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contest(): BelongsTo
    {
        return $this->belongsTo(Contest::class);
    }
}
