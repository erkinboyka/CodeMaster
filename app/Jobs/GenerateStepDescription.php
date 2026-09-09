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

class GenerateStepDescription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $stepId;
    public int $tries = 3;
    public int $timeout = 90;

    public function __construct(int $stepId)
    {
        $this->stepId = $stepId;
    }

    public function handle(): void
    {
        $step = CourseStep::with('course')->find($this->stepId);
        if (!$step || !$step->course) {
            Log::error("Step not found: {$this->stepId}");
            return;
        }

        if ($step->description) {
            Log::info("Description already exists for step {$this->stepId}");
            return;
        }

        $service = app(CourseGenerationService::class);
        $data = $service->generateStepDescription($step->course, $step);

        if ($data) {
            $service->storeDescription($step, $data);
            $step->update(['generation_status' => 'ready', 'generation_progress' => 100]);
            Log::info("Description generated for step {$this->stepId}");
        } else {
            $step->update(['generation_status' => 'error']);
            Log::error("Failed to generate description for step {$this->stepId}");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateStepDescription failed for step {$this->stepId}: " . $exception->getMessage());
    }
}
