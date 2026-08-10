<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoadmapNode extends Model
{
    protected $table = 'roadmap_nodes';

    protected $fillable = [
        'title',
        'course_id',
        'roadmap_title',
        'topic',
        'materials',
        'x',
        'y',
        'deps',
        'is_exam',
    ];

    protected function casts(): array
    {
        return [
            'materials' => 'array',
            'deps' => 'array',
            'is_exam' => 'boolean',
            'x' => 'float',
            'y' => 'float',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function roadmapLessons(): HasMany
    {
        return $this->hasMany(RoadmapLesson::class, 'node_id');
    }

    public function quizQuestions(): HasMany
    {
        return $this->hasMany(RoadmapQuizQuestion::class, 'node_id');
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(RoadmapUserProgress::class, 'node_id');
    }
}
