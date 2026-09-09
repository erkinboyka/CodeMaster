<?php

namespace App\Jobs;

use App\Models\CourseStep;
use App\Services\CourseGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateStepTests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $stepId;
    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(int $stepId)
    {
        $this->stepId = $stepId;
    }

    public function handle(): void
    {
        $step = CourseStep::with('course.courseSkills')->find($this->stepId);
        if (!$step || !$step->course) {
            Log::error("Step not found: {$this->stepId}");
            return;
        }

        if ($step->tests()->count() > 0) {
            Log::info("Tests already exist for step {$this->stepId}");
            return;
        }

        $service = app(CourseGenerationService::class);
        $skills = $step->course->courseSkills;

        $testsData = $service->generateTests($step->course, $step, $skills);
        if ($testsData) {
            $service->storeTests($step, $testsData);
            $step->update(['generation_status' => 'ready', 'generation_progress' => 100]);
            Log::info("Tests generated for step {$this->stepId}: " . count($testsData) . " tests");
        } else {
            $step->update(['generation_status' => 'error']);
            Log::error("Failed to generate tests for step {$this->stepId}");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateStepTests failed for step {$this->stepId}: " . $exception->getMessage());
    }
}
