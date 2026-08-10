<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacancyChat extends Model
{
    protected $table = 'vacancy_chats';

    protected $fillable = [
        'application_id',
        'sender_id',
        'message_text',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(UserApplication::class, 'application_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
