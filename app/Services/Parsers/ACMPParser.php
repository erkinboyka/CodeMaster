<?php

namespace App\Services\Parsers;

use Illuminate\Support\Facades\Log;

class ACMPParser extends BaseParser
{
    protected string $source = 'acmp';
    protected int $requestDelay = 600;

    private string $baseUrl = 'https://acmp.ru';

    public function parse(): array
    {
        $results = [
            'total' => 0,
            'created' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        $taskIds = range(1, 1500);
        $results['total'] = count($taskIds);

        foreach ($taskIds as $index => $taskId) {
            try {
                $slug = 'acmp-' . $taskId;
                $existing = \App\Models\Problem::where('slug', $slug)->first();
                if ($existing) {
                    $results['skipped']++;
                    continue;
                }

                $taskData = $this->fetchTask($taskId);
                $this->sleep();

                if (!$taskData) {
                    $results['errors']++;
                    continue;
                }

                $tests = $this->generateTestsFromExamples(
                    $taskData['input_example'] ?? '',
                    $taskData['output_example'] ?? '',
                    $taskData['description'] ?? ''
                );

                $problem = $this->saveProblem([
                    'title' => $taskData['title'],
                    'description' => $taskData['description'],
                    'difficulty' => $taskData['difficulty'],
                    'points' => match ($taskData['difficulty']) {
                        'easy' => 10,
                        'medium' => 25,
                        'hard' => 50,
                        default => 10,
                    },
                    'input_example' => $taskData['input_example'] ?? null,
                    'output_example' => $taskData['output_example'] ?? null,
                    'constraints' => $taskData['constraints'] ?? null,
                    'tests_json' => $tests,
                    'source_url' => $this->baseUrl . '/index.asp?gr=nacmp&id_task=' . $taskId,
                    'source_id' => (string) $taskId,
                ], $this->source);

                if ($problem) {
                    $results['created']++;
                    $this->log("Created: #" . $taskId . ' ' . $taskData['title'] . ' (' . count($tests) . ' tests)');
                } else {
                    $results['skipped']++;
                }
            } catch (\Exception $e) {
                $results['errors']++;
                Log::error('ACMP parse error for task #' . $taskId . ': ' . $e->getMessage());
            }

            if (($index + 1) % 50 === 0) {
                $this->log('Progress: ' . ($index + 1) . '/' . $results['total'] . ' created=' . $results['created']);
            }
        }

        return $results;
    }

    private function fetchTask(int $id): ?array
    {
        $url = $this->baseUrl . '/index.asp?gr=nacmp&id_task=' . $id;
        $body = $this->fetchWithRetry($url);
        if (!$body) return null;

        if (mb_detect_encoding($body, 'UTF-8', true) === false) {
            $body = mb_convert_encoding($body, 'UTF-8', 'Windows-1251');
        }

        if (str_contains($body, 'Задача не найдена') || str_contains($body, 'Task not found')) {
            return null;
        }

        $title = $this->extractTitle($body);
        if (!$title) return null;

        $description = $this->extractDescription($body);
        $inputExample = $this->extractSection($body, 'Входные данные', 'Выходные данные');
        $outputExample = $this->extractSection($body, 'Выходные данные', 'Пример');
        $constraints = $this->extractSection($body, 'Ограничения', '<h');
        $examples = $this->extractExamplesBlock($body);

        if ($examples) {
            $inputExample = $examples['input'] ?? $inputExample;
            $outputExample = $examples['output'] ?? $outputExample;
        }

        return [
            'title' => $title,
            'description' => $description,
            'difficulty' => $this->guessDifficulty($id),
            'input_example' => $inputExample,
            'output_example' => $outputExample,
            'constraints' => $constraints,
        ];
    }

    private function extractTitle(string $html): ?string
    {
        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $m)) {
            $t = strip_tags(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8'));
            if (!empty(trim($t))) return trim($t);
        }
        if (preg_match('/<title>(.*?)<\/title>/is', $html, $m)) {
            $t = strip_tags(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8'));
            $t = preg_replace('/\s*[-–|]\s*(АСМП|ACMP).*$/i', '', $t);
            return !empty(trim($t)) ? trim($t) : null;
        }
        return null;
    }

    private function extractDescription(string $html): string
    {
        $d = '';
        if (preg_match('/<h1[^>]*>.*?<\/h1>\s*(.*?)(?:<h[23]|<table\b|<div\s+class="menu"|<hr)/is', $html, $m)) {
            $d = $m[1];
        }
        $d = strip_tags($d, '<br><p><b><i><ul><ol><li><code><pre>');
        $d = html_entity_decode($d, ENT_QUOTES, 'UTF-8');
        $d = preg_replace('/\s+/', ' ', $d);
        return trim($d) ?: 'Описание задачи отсутствует.';
    }

    private function extractSection(string $html, string $start, string $end): ?string
    {
        $pattern = '/' . preg_quote($start, '/') . '[:\s]*(.*?)(?:' . preg_quote($end, '/') . '|$)/is';
        if (preg_match($pattern, $html, $m)) {
            $text = strip_tags($m[1]);
            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
            $text = preg_replace('/\s+/', ' ', $text);
            return trim($text) ?: null;
        }
        return null;
    }

    private function extractExamplesBlock(string $html): ?array
    {
        if (!preg_match('/<b>\s*(Пример|Example)\s*<\/b>(.*?)(?:<b>|<h[23]|<table|<div\s+class="menu"|\z)/is', $html, $m)) {
            return null;
        }

        $block = $m[2];
        $input = '';
        $output = '';

        if (preg_match('/Входные данные[:\s]*(.*?)(?:Выходные данные|\z)/is', $block, $im)) {
            $input = trim(strip_tags($im[1]));
        }
        if (preg_match('/Выходные данные[:\s]*(.*?)(?:\z)/is', $block, $om)) {
            $output = trim(strip_tags($om[1]));
        }

        if (empty($input) && empty($output)) {
            if (preg_match_all('/<div[^>]*>\s*(.*?)\s*<\/div>/is', $block, $divs)) {
                foreach ($divs[1] as $div) {
                    $clean = trim(strip_tags($div));
                    if (str_contains(mb_strtolower($clean), 'вход') || str_contains(mb_strtolower($clean), 'input')) {
                        continue;
                    }
                    if (empty($input)) {
                        $input = $clean;
                    } else {
                        $output = $clean;
                    }
                }
            }
        }

        if (empty($input) && empty($output)) return null;

        return ['input' => $input, 'output' => $output];
    }

    private function guessDifficulty(int $id): string
    {
        if ($id <= 100) return 'easy';
        if ($id <= 500) return 'medium';
        return 'hard';
    }

    private function log(string $message): void
    {
        echo '[ACMP] ' . $message . "\n";
    }
}
