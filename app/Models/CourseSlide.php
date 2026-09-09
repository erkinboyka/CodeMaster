<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSlide extends Model
{
    protected $fillable = [
        'course_id', 'step_id', 'title', 'content', 'sort_order',
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
