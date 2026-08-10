<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSkillsAssessment extends Model
{
    protected $table = 'user_skills_assessments';

    protected $fillable = [
        'user_id',
        'skill_name',
        'state_json',
    ];

    protected function casts(): array
    {
        return [
            'state_json' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
