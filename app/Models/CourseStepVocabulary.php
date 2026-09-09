<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseStepVocabulary extends Model
{
    protected $table = 'course_step_vocabulary';
    protected $fillable = ['step_id', 'course_id', 'title', 'content', 'experience'];

    public function step(): BelongsTo
    {
        return $this->belongsTo(CourseStep::class, 'step_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(CourseStepLink::class, 'vocabulary_id');
    }
}
