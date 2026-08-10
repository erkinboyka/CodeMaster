<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseExam extends Model
{
    protected $fillable = [
        'course_id',
        'exam_json',
        'question_bank_json',
        'time_limit_minutes',
        'pass_percent',
        'questions_per_exam',
        'shuffle_questions',
        'shuffle_options',
    ];

    protected function casts(): array
    {
        return [
            'exam_json' => 'array',
            'question_bank_json' => 'array',
            'time_limit_minutes' => 'integer',
            'pass_percent' => 'integer',
            'questions_per_exam' => 'integer',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function getRandomQuestions(int $count = null): array
    {
        $bank = $this->question_bank_json['questions'] ?? [];
        if (empty($bank)) {
            $bank = $this->exam_json['questions'] ?? [];
        }

        $count = $count ?? $this->questions_per_exam ?? 30;
        $count = min($count, count($bank));

        if ($this->shuffle_questions) {
            shuffle($bank);
        }

        return array_slice($bank, 0, $count);
    }
}
