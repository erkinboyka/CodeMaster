<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacancyTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'vacancy_id',
        'locale',
        'title',
        'company',
        'description',
        'company_description',
    ];

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }
}
