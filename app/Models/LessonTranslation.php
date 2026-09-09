<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'lesson_id',
        'locale',
        'title',
        'content',
        'description',
        'materials_title',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
