<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoadmapCertificate extends Model
{
    protected $table = 'roadmap_certificates';

    protected $fillable = [
        'user_id',
        'node_id',
        'cert_hash',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(RoadmapNode::class, 'node_id');
    }
}
