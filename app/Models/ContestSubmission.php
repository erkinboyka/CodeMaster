<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContestSubmission extends Model
{
    protected $table = 'contest_submissions';

    protected $fillable = [
        'user_id',
        'contest_id',
        'task_id',
        'code',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contest(): BelongsTo
    {
        return $this->belongsTo(Contest::class);
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(ContestProblem::class, 'task_id');
    }
}
