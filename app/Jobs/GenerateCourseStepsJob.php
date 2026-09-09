<?php

namespace App\Jobs;

use App\Models\Course;
use App\Services\CourseGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateCourseStepsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $courseId;
    public int $userId;
    public int $tries = 2;
    public int $timeout = 300;

    public function __construct(int $courseId, int $userId)
    {
        $this->courseId = $courseId;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        $course = Course::find($this->courseId);
        if (!$course) {
            Log::error("GenerateCourseStepsJob: course not found {$this->courseId}");
            return;
        }

        if ($course->steps()->count() > 0) {
            Log::info("GenerateCourseStepsJob: steps already exist for course {$this->courseId}");
            if ($course->generation_status !== 'ready') {
                $course->update(['generation_status' => 'ready', 'generation_progress' => 100]);
            }
            return;
        }

        $course->update(['generation_status' => 'generating', 'generation_progress' => 5]);

        $service = app(CourseGenerationService::class);

        $mapData = $service->generateRoadmap(
            $course->topic ?: $course->title,
            $course->course_level ?: 'beginner',
            (int) ($course->freetime ?: 5),
            'ru'
        );

        if (!$mapData || !isset($mapData['map'])) {
            $course->update(['generation_status' => 'error']);
            Log::error("GenerateCourseStepsJob: Gemini returned no map for course {$this->courseId}");
            return;
        }

        try {
            $service->populateCourseFromMapData($course, $mapData);
        } catch (\Throwable $e) {
            $course->update(['generation_status' => 'error']);
            Log::error("GenerateCourseStepsJob: populate failed for course {$this->courseId}: " . $e->getMessage());
            return;
        }

        $course->update(['generation_progress' => 30]);

        Log::info("GenerateCourseStepsJob: steps ready for course {$this->courseId}, dispatching content generation");

        GenerateAllCourseContent::dispatch($course->id);
    }

    public function failed(\Throwable $exception): void
    {
        $course = Course::find($this->courseId);
        if ($course) {
            $course->update(['generation_status' => 'error']);
        }
        Log::error("GenerateCourseStepsJob failed for course {$this->courseId}: " . $exception->getMessage());
    }
}
