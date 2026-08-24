<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeerMessage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'room_id',
        'user_id',
        'text',
        'message_type',
        'file_url',
        'file_name',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(PeerInterviewRoom::class, 'room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
