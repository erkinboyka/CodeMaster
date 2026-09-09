<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseTestVariant extends Model
{
    protected $fillable = ['test_id', 'variant'];

    public function test(): BelongsTo
    {
        return $this->belongsTo(CourseStepTest::class, 'test_id');
    }
}
