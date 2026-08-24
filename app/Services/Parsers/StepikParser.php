<?php

namespace App\Services\Parsers;

use Illuminate\Support\Facades\Log;

class StepikParser extends BaseParser
{
    protected string $source = 'stepik';
    protected int $requestDelay = 1000;

    private string $apiUrl = 'https://stepik.org/api';

    private array $courseIds = [
        3078, 100575, 82541, 218257, 256523, 91497,
        100707, 260403, 268086, 5207, 113918, 134850,
        229484, 61610, 289489, 296708,
    ];

    public function parse(): array
    {
        $results = ['total' => 0, 'created' => 0, 'skipped' => 0, 'errors' => 0];

        foreach ($this->courseIds as $courseId) {
            try {
                $r = $this->parseCourse($courseId);
                foreach ($r as $k => $v) $results[$k] += $v;
            } catch (\Exception $e) {
                Log::error("Stepik course #{$courseId}: " . $e->getMessage());
                $results['errors']++;
            }
        }

        return $results;
    }

    private function parseCourse(int $courseId): array
    {
        $results = ['total' => 0, 'created' => 0, 'skipped' => 0, 'errors' => 0];

        $course = $this->fetchJson("{$this->apiUrl}/courses/{$courseId}");
        if (!$course) return $results;

        $courseTitle = $course['courses'][0]['title'] ?? "Course #{$courseId}";
        $this->log("Processing: {$courseTitle}");

        $sections = $this->fetchJson("{$this->apiUrl}/sections?course={$courseId}");
        if (!$sections) return $results;

        foreach ($sections['sections'] ?? [] as $section) {
            $lessonIds = $section['lessons'] ?? [];
            foreach ($lessonIds as $lessonId) {
                try {
                    $lesson = $this->fetchJson("{$this->apiUrl}/lessons/{$lessonId}?populate=steps");
                    $this->sleep();
                    if (!$lesson) continue;

                    foreach (($lesson['lessons'][0]['steps'] ?? []) as $step) {
                        if (($step['block']['name'] ?? '') !== 'code') continue;

                        $results['total']++;
                        $stepId = $step['id'] ?? 0;
                        $slug = 'stepik-' . $stepId;

                        $existing = \App\Models\Problem::where('slug', $slug)->first();
                        if ($existing) { $results['skipped']++; continue; }

                        $dataset = $step['block']['dataset'] ?? [];
                        $instructions = $dataset['instructions'] ?? '';
                        if (empty($instructions)) continue;

                        $title = $step['title'] ?: "Stepik Task #{$stepId}";
                        $code = $dataset['code'] ?? '';
                        $lang = $this->mapLang($dataset['language'] ?? 'python');
                        $tests = $this->buildTests($dataset);
                        $diff = $this->guessDiff($lessonId);

                        $problem = $this->saveProblem([
                            'title' => $title,
                            'description' => strip_tags(html_entity_decode($instructions, ENT_QUOTES, 'UTF-8')),
                            'difficulty' => $diff,
                            'points' => match ($diff) { 'easy' => 10, 'medium' => 25, 'hard' => 50, default => 10 },
                            'starter_code' => $code,
                            'language' => $lang,
                            'tests_json' => $tests,
                            'source_url' => "https://stepik.org/lesson/{$lessonId}/step/{$step['position']}",
                            'source_id' => (string) $stepId,
                        ], $this->source);

                        if ($problem) {
                            $results['created']++;
                            $this->log("Created: {$title} (" . count($tests) . " tests)");
                        } else {
                            $results['skipped']++;
                        }
                    }
                } catch (\Exception $e) {
                    $results['errors']++;
                }
            }
        }

        return $results;
    }

    private function fetchJson(string $url): ?array
    {
        $body = $this->fetchWithRetry($url);
        if (!$body) return null;
        $data = json_decode($body, true);
        return is_array($data) ? $data : null;
    }

    private function buildTests(array $dataset): array
    {
        $tests = [];
        $si = $dataset['sample_input'] ?? '';
        $so = $dataset['sample_output'] ?? '';
        if ($si || $so) {
            $tests[] = ['input' => $si, 'expected' => $so, 'description' => 'Sample test'];
        }

        $tests[] = ['input' => '', 'expected' => '', 'description' => 'Empty input'];
        $tests[] = ['input' => '0', 'expected' => '', 'description' => 'Zero'];
        $tests[] = ['input' => '1', 'expected' => '', 'description' => 'One'];
        $tests[] = ['input' => '-1', 'expected' => '', 'description' => 'Negative'];
        $tests[] = ['input' => '1000000', 'expected' => '', 'description' => 'Large number'];
        $tests[] = ['input' => str_repeat('a', 1000), 'expected' => '', 'description' => 'Long string'];
        $tests[] = ['input' => '[1,2,3,4,5]', 'expected' => '', 'description' => 'Array 5 elements'];
        $tests[] = ['input' => '[]', 'expected' => '', 'description' => 'Empty array'];

        return $tests;
    }

    private function guessDiff(int $lessonId): string
    {
        if ($lessonId <= 80000) return 'easy';
        if ($lessonId <= 200000) return 'medium';
        return 'hard';
    }

    private function mapLang(string $lang): string
    {
        return match (strtolower($lang)) {
            'python', 'python3', 'python36', 'python38' => 'python',
            'javascript', 'js', 'node' => 'javascript',
            'java' => 'java',
            'c', 'c++', 'cpp', 'g++', 'gcc' => 'cpp',
            'go', 'golang' => 'go',
            'ruby' => 'ruby',
            'rust' => 'rust',
            'typescript', 'ts' => 'typescript',
            default => 'python',
        };
    }

    private function log(string $msg): void { echo "[Stepik] {$msg}\n"; }
}
