<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'title',
        'instructor',
        'description',
        'category',
        'level',
        'image_url',
        'materials_title',
        'materials_url',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function courseSkills(): HasMany
    {
        return $this->hasMany(CourseSkill::class);
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserCourseProgress::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(CourseExam::class);
    }

    public function getModules(): \Illuminate\Support\Collection
    {
        return $this->lessons()
            ->orderBy('order_num')
            ->get()
            ->groupBy('module');
    }
}
