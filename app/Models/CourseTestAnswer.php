<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseTestAnswer extends Model
{
    protected $fillable = ['test_id', 'answer', 'is_correct'];

    protected function casts(): array
    {
        return ['is_correct' => 'boolean'];
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(CourseStepTest::class, 'test_id');
    }
}
