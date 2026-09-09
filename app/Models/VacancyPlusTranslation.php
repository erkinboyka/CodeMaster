<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacancyPlusTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'vacancy_plus_id',
        'locale',
        'plus_text',
    ];

    public function vacancyPlus(): BelongsTo
    {
        return $this->belongsTo(VacancyPlus::class);
    }
}
