<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacancyPlus extends Model
{
    public $timestamps = false;

    protected $table = 'vacancy_pluses';

    protected $fillable = [
        'vacancy_id',
        'plus_text',
    ];

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }
}
