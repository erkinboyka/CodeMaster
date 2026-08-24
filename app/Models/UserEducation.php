<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEducation extends Model
{
    protected $fillable = [
        'user_id',
        'degree',
        'institution',
        'field',
        'start_date',
        'end_date',
        'description',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
