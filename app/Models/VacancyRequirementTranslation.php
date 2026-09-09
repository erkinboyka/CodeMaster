<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacancyRequirementTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'vacancy_requirement_id',
        'locale',
        'requirement_text',
    ];

    public function vacancyRequirement(): BelongsTo
    {
        return $this->belongsTo(VacancyRequirement::class);
    }
}
