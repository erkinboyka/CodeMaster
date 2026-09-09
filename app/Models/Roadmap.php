<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Roadmap extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'description', 'image',
        'category', 'difficulty', 'estimated_hours',
        'is_published', 'ai_generated',
        'total_sections', 'total_courses', 'students_count',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'ai_generated' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Roadmap $roadmap) {
            if (empty($roadmap->slug)) {
                $base = Str::slug($roadmap->title);
                $slug = $base;
                $i = 1;
                while (self::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $roadmap->slug = $slug;
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(RoadmapSection::class)->orderBy('sort_order');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'roadmap_courses', 'roadmap_id', 'course_id')
            ->withPivot('section_id', 'sort_order')
            ->withTimestamps();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
