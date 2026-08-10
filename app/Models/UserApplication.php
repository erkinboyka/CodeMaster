<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserApplication extends Model
{
    public $timestamps = false;

    protected $table = 'user_applications';

    protected $fillable = [
        'user_id',
        'vacancy_id',
        'status',
        'employment_status',
        'employment_updated_at',
        'applied_at',
    ];

    protected function casts(): array
    {
        return [
            'employment_updated_at' => 'datetime',
            'applied_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(VacancyChat::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VacancyDocument::class);
    }
}
