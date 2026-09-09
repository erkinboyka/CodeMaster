<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'problem_id',
        'locale',
        'title',
        'description',
        'input_example',
        'output_example',
        'constraints',
    ];

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }
}
