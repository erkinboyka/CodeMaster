<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoadmapLesson extends Model
{
    protected $table = 'roadmap_lessons';

    protected $fillable = [
        'node_id',
        'title',
        'video_url',
        'description',
        'materials',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'materials' => 'array',
            'order_index' => 'integer',
        ];
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(RoadmapNode::class, 'node_id');
    }
}
