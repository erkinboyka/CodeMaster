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

class GenerateStepExams implements ShouldQueue
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
            Log::error("Step not found for exams: {$this->stepId}");
            return;
        }

        if ($step->exams()->count() > 0) {
            Log::info("Exams already exist for step {$this->stepId}");
            return;
        }

        $service = app(CourseGenerationService::class);
        $examsData = $service->generateExams($step->course, $step);

        if ($examsData) {
            $service->storeExams($step, $examsData);
            $step->update(['generation_status' => 'ready', 'generation_progress' => 100]);
            Log::info("Exams generated for step {$this->stepId}: " . count($examsData) . " exams");
        } else {
            $step->update(['generation_status' => 'error']);
            Log::error("Failed to generate exams for step {$this->stepId}");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateStepExams failed for step {$this->stepId}: " . $exception->getMessage());
    }
}
