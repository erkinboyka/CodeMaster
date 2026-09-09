<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSkill extends Model
{
    protected $fillable = [
        'course_id',
        'skill_name',
        'skill',
        'skill_level',
        'score',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
