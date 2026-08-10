<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticeSubmission extends Model
{
    protected $table = 'practice_submissions';

    protected $fillable = [
        'user_id',
        'task_id',
        'code',
        'passed',
        'stdout',
        'stderr',
        'details_json',
    ];

    protected function casts(): array
    {
        return [
            'passed' => 'boolean',
            'details_json' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(LessonPracticeTask::class, 'task_id');
    }
}
