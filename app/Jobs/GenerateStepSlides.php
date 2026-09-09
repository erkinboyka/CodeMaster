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

class GenerateStepSlides implements ShouldQueue
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
        $step = CourseStep::with('course')->find($this->stepId);
        if (!$step || !$step->course) {
            Log::error("Step not found for slides: {$this->stepId}");
            return;
        }

        if ($step->slides()->count() > 0) {
            Log::info("Slides already exist for step {$this->stepId}");
            return;
        }

        $service = app(CourseGenerationService::class);
        $slidesData = $service->generateSlides($step->course, $step);

        if ($slidesData) {
            $service->storeSlides($step, $slidesData);
            $step->update(['generation_status' => 'ready', 'generation_progress' => 100]);
            Log::info("Slides generated for step {$this->stepId}: " . count($slidesData) . " slides");
        } else {
            $step->update(['generation_status' => 'error']);
            Log::error("Failed to generate slides for step {$this->stepId}");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateStepSlides failed for step {$this->stepId}: " . $exception->getMessage());
    }
}
