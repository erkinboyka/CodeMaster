<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'type',
        'content',
        'video_url',
        'audio_url',
        'presentation_url',
        'description',
        'materials_title',
        'materials_url',
        'completed',
        'order_num',
        'duration_minutes',
        'difficulty',
        'module',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'order_num' => 'integer',
            'duration_minutes' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function quizQuestions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function lessonQuizzes(): HasMany
    {
        return $this->hasMany(LessonQuiz::class);
    }

    public function practiceTasks(): HasMany
    {
        return $this->hasMany(LessonPracticeTask::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_num');
    }

    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }
}
