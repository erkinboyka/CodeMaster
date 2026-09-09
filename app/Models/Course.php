<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'user_id', 'type', 'ai_generated', 'generation_status', 'generation_progress',
        'topic', 'title', 'instructor', 'description', 'category', 'level',
        'course_level', 'freetime', 'total_steps', 'total_experience',
        'image_url', 'logo', 'students_count', 'materials_title', 'materials_url',
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

    public function steps(): HasMany
    {
        return $this->hasMany(CourseStep::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_courses', 'course_id', 'user_id')
            ->withPivot('status', 'experience', 'steps_completed')
            ->withTimestamps();
    }

    public function getModules(): \Illuminate\Support\Collection
    {
        return $this->lessons()
            ->orderBy('order_num')
            ->get()
            ->groupBy('module');
    }
}
