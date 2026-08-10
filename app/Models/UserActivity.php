<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    protected $table = 'user_activities';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_text',
        'activity_time',
    ];

    protected function casts(): array
    {
        return [
            'activity_time' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
