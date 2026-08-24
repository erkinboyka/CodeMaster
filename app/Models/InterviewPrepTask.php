<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewPrepTask extends Model
{
    protected $fillable = [
        'title',
        'category',
        'difficulty',
        'statement',
        'input_spec',
        'output_spec',
        'starter_cpp',
        'starter_python',
        'starter_c',
        'starter_csharp',
        'starter_java',
        'tests_json',
        'time_limit_sec',
        'memory_limit_kb',
        'hints',
        'sort_order',
        'source_task_id',
    ];

    protected $casts = [
        'tests_json' => 'array',
        'time_limit_sec' => 'integer',
        'memory_limit_kb' => 'integer',
        'sort_order' => 'integer',
        'source_task_id' => 'integer',
    ];
}
