<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ProblemTopic extends Model
{
    protected $fillable = ['name', 'slug', 'problems_count'];

    protected static function booted(): void
    {
        static::creating(function (ProblemTopic $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function problems(): BelongsToMany
    {
        return $this->belongsToMany(Problem::class, 'problem_problem_topic');
    }
}
