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

class GenerateCourseRoadmap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $courseId;
    public string $topic;
    public string $level;
    public int $freetime;
    public string $language;
    public int $tries = 2;
    public int $timeout = 300;

    public function __construct(int $courseId, string $topic, string $level, int $freetime, string $language = 'ru')
    {
        $this->courseId = $courseId;
        $this->topic = $topic;
        $this->level = $level;
        $this->freetime = $freetime;
        $this->language = $language;
    }

    public function handle(): void
    {
        $course = Course::find($this->courseId);
        if (!$course) {
            Log::error("GenerateCourseRoadmap: course not found {$this->courseId}");
            return;
        }

        if ($course->steps()->count() > 0) {
            Log::info("GenerateCourseRoadmap: steps already exist for course {$this->courseId}");
            return;
        }

        $course->update(['generation_status' => 'generating', 'generation_progress' => 5]);

        $service = app(CourseGenerationService::class);

        $mapData = $service->generateRoadmap(
            $this->topic,
            $this->level,
            $this->freetime,
            $this->language
        );

        if (!$mapData || !isset($mapData['map'])) {
            $course->update(['generation_status' => 'error']);
            Log::error("GenerateCourseRoadmap: Gemini returned no map for course {$this->courseId}");
            return;
        }

        try {
            $service->populateCourseFromMapData($course, $mapData);
        } catch (\Throwable $e) {
            $course->update(['generation_status' => 'error']);
            Log::error("GenerateCourseRoadmap: populate failed for course {$this->courseId}: " . $e->getMessage());
            return;
        }

        $course->update(['generation_progress' => 30]);

        Log::info("GenerateCourseRoadmap: roadmap ready for course {$this->courseId}, dispatching content generation");

        GenerateAllCourseContent::dispatch($course->id);
    }

    public function failed(\Throwable $exception): void
    {
        $course = Course::find($this->courseId);
        if ($course) {
            $course->update(['generation_status' => 'error']);
        }
        Log::error("GenerateCourseRoadmap failed for course {$this->courseId}: " . $exception->getMessage());
    }
}
