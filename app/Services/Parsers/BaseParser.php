<?php

namespace App\Services\Parsers;

use App\Models\Problem;
use App\Models\ProblemTopic;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class BaseParser
{
    protected string $source;
    protected int $requestDelay = 800;
    protected int $maxRetries = 3;

    abstract public function parse(): array;

    protected function getOrCreateTopic(string $name): ProblemTopic
    {
        return ProblemTopic::firstOrCreate(
            ['name' => $name],
            ['slug' => Str::slug($name)]
        );
    }

    protected function saveProblem(array $data, string $sourceTag): ?Problem
    {
        $slug = Str::slug($data['title']);

        $existing = Problem::where('slug', $slug)->first();
        if ($existing) {
            return null;
        }

        $problem = Problem::create([
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'] ?? '',
            'difficulty' => $data['difficulty'] ?? 'easy',
            'points' => $data['points'] ?? 10,
            'input_example' => $data['input_example'] ?? null,
            'output_example' => $data['output_example'] ?? null,
            'constraints' => $data['constraints'] ?? null,
            'starter_code' => $data['starter_code'] ?? null,
            'language' => $data['language'] ?? 'python',
            'tests_json' => $data['tests_json'] ?? null,
            'time_limit' => $data['time_limit'] ?? 2,
            'memory_limit' => $data['memory_limit'] ?? 256,
            'solved_count' => $data['solved_count'] ?? 0,
            'attempt_count' => $data['attempt_count'] ?? 0,
            'is_premium' => $data['is_premium'] ?? false,
            'source' => $sourceTag,
            'source_url' => $data['source_url'] ?? null,
        ]);

        if (!empty($data['topics'])) {
            foreach ($data['topics'] as $topicName) {
                $topic = $this->getOrCreateTopic($topicName);
                $problem->topics()->attach($topic->id);
                $topic->increment('problems_count');
            }
        }

        if (!empty($data['source_url'])) {
            \DB::table('problem_sources')->insertOrIgnore([
                'problem_id' => $problem->id,
                'source' => $sourceTag,
                'source_url' => $data['source_url'],
                'source_id' => $data['source_id'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $problem;
    }

    protected function fetchWithRetry(string $url, array $headers = []): ?string
    {
        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $defaultHeaders = [
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                ];

                $headerStrings = [];
                foreach ($headers as $key => $value) {
                    $headerStrings[] = "{$key}: {$value}";
                }

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($defaultHeaders, $headerStrings));
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_ENCODING, '');

                $body = curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);

                if ($error) {
                    if ($attempt < $this->maxRetries) sleep($attempt * 2);
                    continue;
                }

                if ($statusCode === 200) return $body;

                if ($statusCode === 429) {
                    sleep($attempt * 5);
                    continue;
                }

                if ($attempt < $this->maxRetries) sleep($attempt);
            } catch (\Exception $e) {
                if ($attempt < $this->maxRetries) sleep($attempt);
            }
        }

        return null;
    }

    protected function sleep(): void
    {
        usleep($this->requestDelay * 1000);
    }

    protected function generateTestsFromExamples(string $inputExample, string $outputExample, string $description = ''): array
    {
        $tests = [];
        $inputs = $this->parseExamples($inputExample);
        $outputs = $this->parseExamples($outputExample);

        $count = max(count($inputs), count($outputs), 1);
        for ($i = 0; $i < $count; $i++) {
            $tests[] = [
                'input' => $inputs[$i] ?? ($inputs[0] ?? ''),
                'expected' => $outputs[$i] ?? ($outputs[0] ?? ''),
                'description' => 'Example ' . ($i + 1),
            ];
        }

        if (empty($tests) && !empty($inputExample) && !empty($outputExample)) {
            $tests[] = [
                'input' => $inputExample,
                'expected' => $outputExample,
                'description' => 'Example',
            ];
        }

        $tests = array_merge($tests, $this->generateEdgeCases($description, $inputExample, $outputExample));

        return $tests;
    }

    protected function generateEdgeCases(string $description, string $inputExample, string $outputExample): array
    {
        $tests = [];
        $desc = strtolower($description . ' ' . $inputExample);

        if (str_contains($desc, 'array') || str_contains($desc, 'массив') || preg_match('/\[\d/', $inputExample)) {
            $tests[] = ['input' => '[]', 'expected' => '[]', 'description' => 'Empty array'];
            $tests[] = ['input' => '[1]', 'expected' => '[1]', 'description' => 'Single element'];
            $tests[] = ['input' => '[1,2,3,4,5]', 'expected' => '', 'description' => 'Small sorted array'];
            $tests[] = ['input' => '[5,4,3,2,1]', 'expected' => '', 'description' => 'Reverse sorted array'];
            $tests[] = ['input' => '[-1,-2,-3]', 'expected' => '', 'description' => 'Negative numbers'];
            $tests[] = ['input' => str_repeat('[1,2,3,', 300) . '1,2,3]', 'expected' => '', 'description' => 'Large array 1000 elements'];
            $tests[] = ['input' => str_repeat('[0,', 500) . '0]', 'expected' => '', 'description' => 'All zeros'];
            $tests[] = ['input' => '[1,1,1,1,1]', 'expected' => '', 'description' => 'All same elements'];
            $tests[] = ['input' => '[2147483647]', 'expected' => '', 'description' => 'Max int32'];
            $tests[] = ['input' => '[-2147483648]', 'expected' => '', 'description' => 'Min int32'];
        }

        if (str_contains($desc, 'string') || str_contains($desc, 'строк') || preg_match('/["\']/', $inputExample)) {
            $tests[] = ['input' => '""', 'expected' => '""', 'description' => 'Empty string'];
            $tests[] = ['input' => '"a"', 'expected' => '"a"', 'description' => 'Single char'];
            $tests[] = ['input' => '"abcabc"', 'expected' => '', 'description' => 'Repeated substring'];
            $tests[] = ['input' => '"aabbcc"', 'expected' => '', 'description' => 'Pairs'];
            $tests[] = ['input' => str_repeat('ab', 500), 'expected' => '', 'description' => 'Long string 1000 chars'];
            $tests[] = ['input' => '"       "', 'expected' => '', 'description' => 'Spaces only'];
            $tests[] = ['input' => '"!@#$%^&*()"', 'expected' => '', 'description' => 'Special characters'];
            $tests[] = ['input' => '"1234567890"', 'expected' => '', 'description' => 'Digits as string'];
        }

        if (str_contains($desc, 'number') || str_contains($desc, 'числ') || str_contains($desc, 'int') || str_contains($desc, 'integer')) {
            $tests[] = ['input' => '0', 'expected' => '0', 'description' => 'Zero'];
            $tests[] = ['input' => '1', 'expected' => '1', 'description' => 'One'];
            $tests[] = ['input' => '-1', 'expected' => '-1', 'description' => 'Negative one'];
            $tests[] = ['input' => '2147483647', 'expected' => '', 'description' => 'Max int32'];
            $tests[] = ['input' => '-2147483648', 'expected' => '', 'description' => 'Min int32'];
            $tests[] = ['input' => '1000000', 'expected' => '', 'description' => 'Large number'];
        }

        if (str_contains($desc, 'linked list') || str_contains($desc, 'связ') || str_contains($desc, 'list')) {
            $tests[] = ['input' => '[]', 'expected' => '[]', 'description' => 'Empty list'];
            $tests[] = ['input' => '[1]', 'expected' => '[1]', 'description' => 'Single node'];
            $tests[] = ['input' => '[1,2,3,4,5]', 'expected' => '', 'description' => '5 nodes'];
            $tests[] = ['input' => str_repeat('[1,2,', 200) . '1]', 'expected' => '', 'description' => '401 nodes'];
        }

        if (str_contains($desc, 'tree') || str_contains($desc, 'дерев') || str_contains($desc, 'binary')) {
            $tests[] = ['input' => '[]', 'expected' => '0', 'description' => 'Empty tree'];
            $tests[] = ['input' => '[1]', 'expected' => '1', 'description' => 'Single node tree'];
            $tests[] = ['input' => '[1,2,3]', 'expected' => '', 'description' => 'Full binary tree depth 2'];
            $tests[] = ['input' => '[1,null,2,null,null,null,3]', 'expected' => '', 'description' => 'Right skewed tree'];
            $tests[] = ['input' => '[1,2,null,3,null,null,null,null,4]', 'expected' => '', 'description' => 'Left skewed tree'];
            $tests[] = ['input' => '[5,3,7,2,4,6,8,1,null,null,null,3.5,null,null,null,9]', 'expected' => '', 'description' => 'Balanced tree depth 4'];
        }

        if (str_contains($desc, 'matrix') || str_contains($desc, 'матриц') || str_contains($desc, 'grid') || str_contains($desc, 'двумерн')) {
            $tests[] = ['input' => '[]', 'expected' => '0', 'description' => 'Empty matrix'];
            $tests[] = ['input' => '[[1]]', 'expected' => '1', 'description' => '1x1 matrix'];
            $tests[] = ['input' => '[[1,2],[3,4]]', 'expected' => '', 'description' => '2x2 matrix'];
            $tests[] = ['input' => '[[1,2,3],[4,5,6],[7,8,9]]', 'expected' => '', 'description' => '3x3 matrix'];
            $big = '[';
            for ($r = 0; $r < 100; $r++) {
                $big .= ($r > 0 ? ',' : '') . '[';
                for ($c = 0; $c < 100; $c++) {
                    $big .= ($c > 0 ? ',' : '') . (($r * 100 + $c) % 10);
                }
                $big .= ']';
            }
            $big .= ']';
            $tests[] = ['input' => $big, 'expected' => '', 'description' => '100x100 matrix'];
        }

        $tests[] = ['input' => '', 'expected' => '', 'description' => 'Empty input'];

        return $tests;
    }

    protected function parseExamples(string $text): array
    {
        if (empty($text)) return [];

        $lines = array_filter(explode("\n", $text), fn($l) => trim($l) !== '');
        if (count($lines) <= 1) return [$text];

        $tests = [];
        $current = [];
        $inExample = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^(Пример|Example)\s*\d*/i', $line)) {
                if (!empty($current)) {
                    $tests[] = implode("\n", $current);
                    $current = [];
                }
                $inExample = true;
                continue;
            }
            if (preg_match('/^>>>/', $line)) {
                $current[] = preg_replace('/^>>>/', '', $line);
            } else {
                $current[] = $line;
            }
        }
        if (!empty($current)) {
            $tests[] = implode("\n", $current);
        }

        return $tests ?: [$text];
    }
}
