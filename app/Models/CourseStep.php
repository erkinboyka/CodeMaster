<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseStep extends Model
{
    protected $fillable = [
        'course_id', 'parent_id', 'type', 'title', 'description',
        'experience', 'sort_order', 'is_completed', 'heirs',
        'generation_status', 'generation_progress',
    ];

    protected function casts(): array
    {
        return [
            'heirs' => 'array',
            'is_completed' => 'boolean',
            'experience' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CourseStep::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CourseStep::class, 'parent_id')->orderBy('sort_order');
    }

    public function tests(): HasMany
    {
        return $this->hasMany(CourseStepTest::class, 'step_id');
    }

    public function vocabularies(): HasMany
    {
        return $this->hasMany(CourseStepVocabulary::class, 'step_id');
    }

    public function links(): HasMany
    {
        return $this->hasMany(CourseStepLink::class, 'step_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(CourseStepExam::class, 'step_id');
    }

    public function slides(): HasMany
    {
        return $this->hasMany(CourseSlide::class, 'step_id');
    }

    public function studentProgress(): HasMany
    {
        return $this->hasMany(StepStudent::class, 'step_id');
    }
}
