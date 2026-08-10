<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonPracticeTask extends Model
{
    protected $table = 'lesson_practice_tasks';

    protected $fillable = [
        'lesson_id',
        'language',
        'title',
        'prompt',
        'starter_code',
        'tests_json',
        'expected_output',
        'time_limit',
        'hints',
        'difficulty',
        'test_runner_json',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'tests_json' => 'array',
            'test_runner_json' => 'array',
            'is_required' => 'boolean',
            'time_limit' => 'integer',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(PracticeSubmission::class, 'task_id');
    }

    public function userPassed(int $userId): bool
    {
        return $this->submissions()
            ->where('user_id', $userId)
            ->where('passed', true)
            ->exists();
    }
}
