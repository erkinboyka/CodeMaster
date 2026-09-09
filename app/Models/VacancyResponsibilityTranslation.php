<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacancyResponsibilityTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'vacancy_responsibility_id',
        'locale',
        'responsibility_text',
    ];

    public function vacancyResponsibility(): BelongsTo
    {
        return $this->belongsTo(VacancyResponsibility::class);
    }
}
