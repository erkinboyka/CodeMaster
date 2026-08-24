<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemSubmission extends Model
{
    protected $fillable = [
        'user_id', 'problem_id', 'code', 'language', 'status',
        'runtime_ms', 'memory_kb', 'passed_tests', 'total_tests', 'results_json',
    ];

    protected $casts = [
        'results_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }
}
