<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContestProblem extends Model
{
    protected $fillable = [
        'contest_id',
        'title',
        'description',
        'difficulty',
        'points',
        'input_example',
        'output_example',
        'constraints',
        'starter_code',
        'language',
        'tests_json',
        'time_limit',
        'memory_limit',
        'order_num',
    ];

    protected $casts = [
        'tests_json' => 'array',
        'points' => 'integer',
        'order_num' => 'integer',
    ];

    public function contest(): BelongsTo
    {
        return $this->belongsTo(Contest::class);
    }
}
