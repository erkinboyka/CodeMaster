<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class RoadmapSection extends Model
{
    protected $fillable = [
        'roadmap_id', 'parent_id', 'title', 'description',
        'icon', 'sort_order', 'total_courses',
    ];

    public function roadmap(): BelongsTo
    {
        return $this->belongsTo(Roadmap::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(RoadmapSection::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(RoadmapSection::class, 'parent_id')->orderBy('sort_order');
    }

    public function courseLinks(): HasMany
    {
        return $this->hasMany(RoadmapCourse::class, 'section_id');
    }

    public function courses(): HasManyThrough
    {
        return $this->hasManyThrough(Course::class, RoadmapCourse::class, 'section_id', 'id', 'id', 'course_id');
    }
}
