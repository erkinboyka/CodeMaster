<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseTestMatching extends Model
{
    protected $fillable = ['test_id', 'list1_item', 'list2_item'];

    public function test(): BelongsTo
    {
        return $this->belongsTo(CourseStepTest::class, 'test_id');
    }
}
