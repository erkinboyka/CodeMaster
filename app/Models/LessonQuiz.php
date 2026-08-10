<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonQuiz extends Model
{
    protected $fillable = [
        'lesson_id',
        'question_text',
        'options_json',
        'correct_option',
        'explanation',
        'order_num',
    ];

    protected function casts(): array
    {
        return [
            'options_json' => 'array',
            'correct_option' => 'integer',
            'order_num' => 'integer',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
