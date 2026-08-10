<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonTest extends Model
{
    protected $fillable = [
        'lesson_id',
        'test_json',
    ];

    protected function casts(): array
    {
        return [
            'test_json' => 'array',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
