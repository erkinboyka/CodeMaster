<?php

namespace App\Jobs;

use App\Models\Roadmap;
use App\Models\RoadmapCourse;
use App\Services\CourseGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateRoadmapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $roadmapId;
    public int $userId;
    public int $tries = 2;
    public int $timeout = 180;

    public function __construct(int $roadmapId, int $userId)
    {
        $this->roadmapId = $roadmapId;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        $roadmap = Roadmap::find($this->roadmapId);
        if (!$roadmap) {
            Log::error("Roadmap not found: {$this->roadmapId}");
            return;
        }

        $service = app(CourseGenerationService::class);

        $level = $roadmap->difficulty ?: 'beginner';
        $freetime = 5;

        $mapData = $service->generateRoadmap(
            $roadmap->title,
            $level,
            $freetime,
            'ru'
        );

        if (!$mapData || !isset($mapData['map'])) {
            // Ошибка генерации: оставляем is_published=false, но пишем причину в description,
            // чтобы generating-экран мог показать ошибку, а не крутиться вечно.
            $roadmap->update([
                'is_published' => false,
                'description' => 'generation_error',
            ]);
            Log::error("Failed to generate roadmap for {$this->roadmapId}");
            return;
        }

        $course = $service->createCourseFromRoadmap(
            $this->userId,
            $mapData,
            $level,
            $freetime
        );

        RoadmapCourse::create([
            'roadmap_id' => $roadmap->id,
            'section_id' => null,
            'course_id' => $course->id,
            'sort_order' => 0,
        ]);

        $roadmap->update([
            'description' => $mapData['topic_course'] ?? $roadmap->title,
            'total_sections' => 1,
            'total_courses' => 1,
            'is_published' => true,
        ]);

        // Догенерируем контент шагов, чтобы роадмап-курс не оставался пустым скелетом.
        \App\Jobs\GenerateAllCourseContent::dispatch($course->id);

        Log::info("Roadmap generated: {$roadmap->id} -> course {$course->id} with " . $course->steps()->count() . " steps");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateRoadmapJob failed for {$this->roadmapId}: " . $exception->getMessage());
    }
}
