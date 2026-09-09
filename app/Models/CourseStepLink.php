<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseStepLink extends Model
{
    protected $fillable = ['step_id', 'vocabulary_id', 'link'];

    public function step(): BelongsTo
    {
        return $this->belongsTo(CourseStep::class, 'step_id');
    }

    public function vocabulary(): BelongsTo
    {
        return $this->belongsTo(CourseStepVocabulary::class, 'vocabulary_id');
    }
}
