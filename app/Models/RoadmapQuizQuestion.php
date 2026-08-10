<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoadmapQuizQuestion extends Model
{
    protected $table = 'roadmap_quiz_questions';

    protected $fillable = [
        'node_id',
        'question',
        'options',
        'correct_answer',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(RoadmapNode::class, 'node_id');
    }
}
