<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoadmapUserProgress extends Model
{
    public $timestamps = false;
    protected $table = 'roadmap_user_progress';

    protected $fillable = [
        'user_id',
        'node_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(RoadmapNode::class, 'node_id');
    }
}
