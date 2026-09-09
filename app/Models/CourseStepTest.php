<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseStepTest extends Model
{
    protected $fillable = ['course_id', 'step_id', 'skill_id', 'type_test', 'text', 'score'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(CourseStep::class, 'step_id');
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(CourseSkill::class, 'skill_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(CourseTestVariant::class, 'test_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CourseTestAnswer::class, 'test_id');
    }

    public function matchingItems(): HasMany
    {
        return $this->hasMany(CourseTestMatching::class, 'test_id');
    }

    public function studentResults(): HasMany
    {
        return $this->hasMany(TestStudent::class, 'test_id');
    }
}
