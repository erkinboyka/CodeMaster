<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPortfolio extends Model
{
    protected $table = 'user_portfolio';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'url',
        'category',
        'image_url',
        'github_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
