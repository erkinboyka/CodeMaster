<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoadmapCourse extends Model
{
    protected $fillable = [
        'roadmap_id', 'section_id', 'course_id', 'sort_order',
    ];

    public function roadmap(): BelongsTo
    {
        return $this->belongsTo(Roadmap::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(RoadmapSection::class, 'section_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
