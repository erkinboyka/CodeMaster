<?php

namespace App\Jobs;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAllCourseContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $courseId;
    public int $tries = 2;
    public int $timeout = 600;

    public function __construct(int $courseId)
    {
        $this->courseId = $courseId;
    }

    public function handle(): void
    {
        $course = Course::with('steps')->find($this->courseId);
        if (!$course) {
            Log::error("Course not found for content generation: {$this->courseId}");
            return;
        }

        $course->update(['generation_status' => 'generating']);
        $totalSteps = $course->steps->count();

        if ($totalSteps === 0) {
            $course->update([
                'generation_status' => 'error',
                'generation_progress' => 0,
            ]);
            Log::error("GenerateAllCourseContent: no steps for course {$this->courseId}");
            return;
        }

        $service = app(\App\Services\CourseGenerationService::class);
        $completed = 0;
        $hasErrors = false;

        foreach ($course->steps as $step) {
            // Пропуск уже готовых шагов (идемпотентность при ретраях)
            if ($step->generation_status === 'ready' && $step->tests()->count() > 0 && $step->vocabularies()->count() > 0) {
                $completed++;
                $course->update(['generation_progress' => (int) round(($completed / $totalSteps) * 100)]);
                continue;
            }

            $step->update(['generation_status' => 'generating']);
            $stepOk = true;

            try {
                // Синхронная генерация — прогресс честный, курс станет ready только когда контент реально готов.
                // Каждый блок независим: ошибка одного не валит весь шаг.
                if ($step->tests()->count() === 0) {
                    $skills = $course->courseSkills()->get();
                    $testsData = $service->generateTests($course, $step, $skills);
                    if ($testsData) {
                        $service->storeTests($step, $testsData);
                    } else {
                        $stepOk = false;
                    }
                }

                if (!$step->description) {
                    $descData = $service->generateStepDescription($course, $step);
                    if ($descData) {
                        $service->storeDescription($step, $descData);
                    } else {
                        $stepOk = false;
                    }
                }

                if ($step->vocabularies()->count() === 0) {
                    $vocabData = $service->generateVocabulary($course, $step);
                    if ($vocabData) {
                        $service->storeVocabulary($step, $vocabData);
                    } else {
                        $stepOk = false;
                    }
                }

                if ($step->exams()->count() === 0) {
                    $examsData = $service->generateExams($course, $step);
                    if ($examsData) {
                        $service->storeExams($step, $examsData);
                    } else {
                        $stepOk = false;
                    }
                }

                if ($step->slides()->count() === 0) {
                    $slidesData = $service->generateSlides($course, $step);
                    if ($slidesData) {
                        $service->storeSlides($step, $slidesData);
                    } else {
                        $stepOk = false;
                    }
                }

                $step->update([
                    'generation_status' => $stepOk ? 'ready' : 'partial_ready',
                    'generation_progress' => 100,
                ]);
                if (!$stepOk) $hasErrors = true;
                $completed++;
            } catch (\Throwable $e) {
                $step->update(['generation_status' => 'error']);
                $hasErrors = true;
                Log::error("Failed to generate content for step {$step->id}: " . $e->getMessage());
            }

            $progress = (int) round(($completed / $totalSteps) * 100);
            $course->update(['generation_progress' => $progress]);
        }

        // Курс ready только если все шаги ready, иначе partial_ready/error
        $readyCount = $course->steps()->where('generation_status', 'ready')->count();
        if ($readyCount === $totalSteps) {
            $course->update(['generation_status' => 'ready', 'generation_progress' => 100]);
        } elseif ($readyCount > 0) {
            $course->update(['generation_status' => 'partial_ready', 'generation_progress' => 100]);
        } else {
            $course->update(['generation_status' => 'error', 'generation_progress' => 100]);
        }

        Log::info("All content generated for course {$this->courseId}: {$readyCount}/{$totalSteps} steps ready");
    }

    public function failed(\Throwable $exception): void
    {
        $course = Course::find($this->courseId);
        if ($course) {
            $course->update(['generation_status' => 'error']);
        }
        Log::error("GenerateAllCourseContent failed for course {$this->courseId}: " . $exception->getMessage());
    }
}
