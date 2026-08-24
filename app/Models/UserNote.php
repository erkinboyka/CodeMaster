<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNote extends Model
{
    protected $fillable = ['user_id', 'problem_id', 'title', 'content', 'tags'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }
}
