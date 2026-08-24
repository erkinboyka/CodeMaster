<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vacancy extends Model
{
    protected $fillable = [
        'title',
        'company',
        'location',
        'type',
        'salary_min',
        'salary_max',
        'salary_currency',
        'description',
        'company_description',
        'verified',
        'owner_id',
    ];

    protected function casts(): array
    {
        return [
            'salary_min' => 'integer',
            'salary_max' => 'integer',
            'verified' => 'boolean',
        ];
    }

    public function vacancySkills(): HasMany
    {
        return $this->hasMany(VacancySkill::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(VacancyRequirement::class);
    }

    public function pluses(): HasMany
    {
        return $this->hasMany(VacancyPlus::class);
    }

    public function responsibilities(): HasMany
    {
        return $this->hasMany(VacancyResponsibility::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(UserApplication::class);
    }
}
