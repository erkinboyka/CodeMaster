<?php

namespace App\Console\Commands;

use App\Models\Problem;
use Illuminate\Console\Command;

class FixProblemTests extends Command
{
    protected $signature = 'problems:fix-tests
        {--dry-run : Show what would be changed without saving}
        {--slug= : Fix only specific problem by slug}';

    protected $description = 'Fix incomplete test cases, missing constraints, and starter code for existing problems';

    private int $fixed = 0;
    private int $skipped = 0;

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $slug = $this->option('slug');

        $query = Problem::query();
        if ($slug) {
            $query->where('slug', $slug);
        }
        $problems = $query->get();

        $this->info("Found {$problems->count()} problems to check.");
        $this->newLine();

        $standardTests = $this->getStandardTests();

        foreach ($problems as $problem) {
            $this->processProblem($problem, $standardTests, $dryRun);
        }

        $this->newLine();
        $this->info("Done. Fixed: {$this->fixed}, Skipped: {$this->skipped}");

        return Command::SUCCESS;
    }

    private function processProblem(Problem $problem, array $standardTests, bool $dryRun): void
    {
        $needsUpdate = false;
        $updates = [];

        $currentTests = $problem->tests_json ?? [];
        $hasEmptyExpected = false;
        $hasTooFewTests = count($currentTests) < 3;

        foreach ($currentTests as $test) {
            if (empty($test['expected'] ?? $test['output'] ?? '')) {
                $hasEmptyExpected = true;
                break;
            }
        }

        $slug = $problem->slug;

        if (isset($standardTests[$slug])) {
            $newTests = $standardTests[$slug];
            if ($newTests !== $currentTests) {
                $updates['tests_json'] = $newTests;
                $needsUpdate = true;
            }
        } elseif ($hasEmptyExpected || $hasTooFewTests) {
            $betterTests = $this->generateBetterTests($problem, $currentTests);
            if (!empty($betterTests) && count($betterTests) > count($currentTests)) {
                $updates['tests_json'] = $betterTests;
                $needsUpdate = true;
            }
        }

        if (empty($problem->constraints) && !empty($problem->description)) {
            $constraints = $this->inferConstraints($problem);
            if (!empty($constraints)) {
                $updates['constraints'] = $constraints;
                $needsUpdate = true;
            }
        }

        if (empty($problem->starter_code)) {
            $starter = $this->generateStarterCode($problem);
            if (!empty($starter)) {
                $updates['starter_code'] = $starter;
                $needsUpdate = true;
            }
        }

        if ($needsUpdate) {
            if ($dryRun) {
                $this->line("  [DRY-RUN] Would update: {$problem->title} ({$problem->slug})");
                foreach ($updates as $key => $val) {
                    if ($key === 'tests_json') {
                        $this->line("    - tests_json: " . count($val) . " test cases");
                    } else {
                        $this->line("    - {$key}: " . substr((string)$val, 0, 80) . "...");
                    }
                }
            } else {
                $problem->update($updates);
                $this->line("  [FIXED] {$problem->title} ({$problem->slug})");
                foreach ($updates as $key => $val) {
                    if ($key === 'tests_json') {
                        $this->line("    - tests_json: " . count($val) . " test cases");
                    } else {
                        $this->line("    - {$key}: " . substr((string)$val, 0, 80) . "...");
                    }
                }
            }
            $this->fixed++;
        } else {
            $this->skipped++;
        }
    }

    private function generateBetterTests(Problem $problem, array $existingTests): array
    {
        $title = strtolower($problem->title);
        $input = $problem->input_example ?? '';
        $output = $problem->output_example ?? '';

        $tests = [];

        if (!empty($input) && !empty($output)) {
            $tests[] = [
                'input' => $input,
                'expected' => $output,
                'description' => 'Example from description',
            ];
        }

        foreach ($existingTests as $test) {
            $expected = $test['expected'] ?? $test['output'] ?? '';
            if (!empty($expected)) {
                $tests[] = [
                    'input' => $test['input'] ?? '',
                    'expected' => $expected,
                    'description' => $test['description'] ?? 'Existing test',
                ];
            }
        }

        $unique = [];
        $seen = [];
        foreach ($tests as $t) {
            $key = $t['input'];
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $t;
            }
        }

        return array_slice($unique, 0, 10);
    }

    private function generateSmartEdgeCases(Problem $problem, string $inputExample, string $outputExample): array
    {
        $tests = [];
        $desc = strtolower($problem->description . ' ' . $inputExample);
        $difficulty = $problem->difficulty;

        $extraCount = $difficulty === 'hard' ? 4 : ($difficulty === 'medium' ? 3 : 2);

        if (preg_match('/\[\s*-?\d/', $inputExample)) {
            $arrays = [
                '[]' => '[]',
                '[1]' => '',
                '[0,0,0]' => '',
                '[-1,-2,-3]' => '',
                '[1,2,3,4,5]' => '',
                '[5,4,3,2,1]' => '',
                '[1,1,2,2,3,3]' => '',
            ];
            $i = 0;
            foreach ($arrays as $inp => $exp) {
                if ($i >= $extraCount) break;
                if (!empty($exp)) {
                    $tests[] = ['input' => $inp, 'expected' => $exp, 'description' => 'Edge: ' . $inp];
                    $i++;
                }
            }
        }

        if (str_contains($desc, 'string') || preg_match('/["\']/', $inputExample)) {
            $strings = [
                '""' => '',
                '"a"' => '',
                '"aa"' => '',
                '"abc"' => '',
            ];
            $i = 0;
            foreach ($strings as $inp => $exp) {
                if ($i >= $extraCount) break;
                if (!empty($exp)) {
                    $tests[] = ['input' => $inp, 'expected' => $exp, 'description' => 'Edge: ' . $inp];
                    $i++;
                }
            }
        }

        if (str_contains($desc, 'number') || str_contains($desc, 'int')) {
            $nums = ['0', '1', '-1', '100', '-100'];
            $i = 0;
            foreach ($nums as $n) {
                if ($i >= min($extraCount, 2)) break;
                $tests[] = ['input' => $n, 'expected' => '', 'description' => "Edge: n={$n}"];
                $i++;
            }
        }

        return $tests;
    }

    private function inferConstraints(Problem $problem): string
    {
        $desc = $problem->description . ' ' . ($problem->input_example ?? '');
        $constraints = [];

        if (preg_match('/(\d+)\s*<=\s*(?:nums|arr|s|n)\.length/i', $desc, $m)) {
            $constraints[] = "Array length: {$m[0]}";
        }

        if (preg_match('/-?\d+\s*<=\s*(?:nums|arr)\[i\]\s*<=\s*-?\d+/i', $desc, $m)) {
            $constraints[] = "Element values: {$m[0]}";
        }

        if (str_contains($desc, '10^5') || str_contains($desc, '10^4')) {
            $constraints[] = "1 <= n <= 10^5";
        }

        if (preg_match('/(\d+)\s*<=\s*(?:s|strs|word)/i', $desc)) {
            $constraints[] = "String length: " . $desc;
        }

        return implode("\n", $constraints) ?: "1 <= n <= 10^4";
    }

    private function generateStarterCode(Problem $problem): string
    {
        $title = strtolower($problem->title);
        $lang = $problem->language ?? 'python';

        if ($lang !== 'python') return '';

        $funcName = $this->titleToFunction($problem->title);

        if (str_contains($title, 'class') || str_contains($title, 'design') || str_contains($title, 'cache')) {
            return $this->generateClassStarter($problem->title);
        }

        $params = $this->inferParams($problem);

        return "def {$funcName}({$params}):\n    pass\n";
    }

    private function titleToFunction(string $title): string
    {
        $slug = \Illuminate\Support\Str::slug($title, '_');
        $parts = explode('_', $slug);
        $func = '';
        foreach ($parts as $i => $part) {
            $func .= ($i === 0 ? $part : ucfirst($part));
        }
        return $func;
    }

    private function inferParams(Problem $problem): string
    {
        $input = strtolower($problem->input_example ?? '');
        $desc = strtolower($problem->description ?? '');

        $params = [];

        if (preg_match_all('/\b(nums|arr|array|numbers?)\b/i', $input . ' ' . $desc, $m)) {
            $params[] = 'nums';
        }
        if (preg_match_all('/\b(str|s|string|text)\b/i', $input . ' ' . $desc, $m)) {
            if (!in_array('s', $params) && !in_array('str', $params)) {
                $params[] = 's';
            }
        }
        if (preg_match('/\btarget\b/i', $input . ' ' . $desc)) {
            $params[] = 'target';
        }
        if (preg_match('/\bn\b/', $input)) {
            $params[] = 'n';
        }
        if (preg_match('/\bk\b/', $input)) {
            $params[] = 'k';
        }

        return implode(', ', $params) ?: 'nums';
    }

    private function generateClassStarter(string $title): string
    {
        $className = str_replace(' ', '', $title);

        if (str_contains(strtolower($title), 'lru cache')) {
            return "class LRUCache:\n    def __init__(self, capacity):\n        pass\n\n    def get(self, key):\n        pass\n\n    def put(self, key, value):\n        pass\n";
        }
        if (str_contains(strtolower($title), 'trie')) {
            return "class Trie:\n    def __init__(self):\n        pass\n\n    def insert(self, word):\n        pass\n\n    def search(self, word):\n        pass\n\n    def startsWith(self, prefix):\n        pass\n";
        }
        if (str_contains(strtolower($title), 'median finder')) {
            return "class MedianFinder:\n    def __init__(self):\n        pass\n\n    def addNum(self, num):\n        pass\n\n    def findMedian(self):\n        pass\n";
        }
        if (str_contains(strtolower($title), 'freq stack')) {
            return "class FreqStack:\n    def __init__(self):\n        pass\n\n    def push(self, val):\n        pass\n\n    def pop(self):\n        pass\n";
        }
        if (str_contains(strtolower($title), 'codec') || str_contains(strtolower($title), 'serialize')) {
            return "class Codec:\n    def serialize(self, root):\n        pass\n\n    def deserialize(self, data):\n        pass\n";
        }

        return "class {$className}:\n    def __init__(self):\n        pass\n";
    }

    private function getStandardTests(): array
    {
        $testsDir = __DIR__ . '/tests_data';
        $allTests = [];

        if (is_dir($testsDir)) {
            $files = glob($testsDir . '/*.php');
            foreach ($files as $file) {
                $fileTests = require $file;
                if (is_array($fileTests)) {
                    $allTests = array_merge($allTests, $fileTests);
                }
            }
        }

        return $allTests;
    }
}
