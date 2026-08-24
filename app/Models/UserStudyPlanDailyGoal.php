<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStudyPlanDailyGoal extends Model
{
    protected $fillable = [
        'user_study_plan_id', 'date', 'target', 'completed', 'is_met',
    ];

    protected $casts = [
        'date' => 'date',
        'is_met' => 'boolean',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(UserStudyPlan::class, 'user_study_plan_id');
    }
}
