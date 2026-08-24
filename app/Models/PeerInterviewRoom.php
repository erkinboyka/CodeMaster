<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeerInterviewRoom extends Model
{
    protected $fillable = [
        'room_code',
        'host_id',
        'guest_id',
        'host_sdp',
        'guest_sdp',
        'host_ice',
        'guest_ice',
        'status',
        'host_name',
        'guest_name',
        'code_content',
        'code_language',
        'started_at',
        'ended_at',
        'total_score',
        'max_score',
        'summary',
    ];

    protected $casts = [
        'host_ice' => 'array',
        'guest_ice' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(PeerTask::class, 'room_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(PeerMessage::class, 'room_id');
    }

    public static function generateCode(): string
    {
        do {
            $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        } while (self::where('room_code', $code)->exists());

        return $code;
    }

    public function isHost($userId): bool
    {
        return $this->host_id === (int) $userId;
    }

    public function isGuest($userId): bool
    {
        return $this->guest_id === (int) $userId;
    }

    public function isParticipant($userId): bool
    {
        return $this->isHost($userId) || $this->isGuest($userId);
    }
}
