<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseStepExam extends Model
{
    protected $table = 'course_step_exams';

    protected $fillable = [
        'course_id', 'step_id', 'type', 'question',
        'options', 'correct_answer', 'explanation',
        'difficulty', 'score',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(CourseStep::class, 'step_id');
    }
}
