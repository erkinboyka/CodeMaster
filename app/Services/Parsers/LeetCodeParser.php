<?php

namespace App\Services\Parsers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeetCodeParser extends BaseParser
{
    protected string $source = 'leetcode';
    protected int $requestDelay = 1500;

    private string $apiUrl = 'https://leetcode.com';
    private string $graphqlUrl = 'https://leetcode.com/graphql';

    public function parse(): array
    {
        $results = [
            'total' => 0,
            'created' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        $problemsList = $this->fetchProblemsList();
        if (empty($problemsList)) {
            Log::error('LeetCode: Failed to fetch problems list');
            return $results;
        }

        $results['total'] = count($problemsList);

        foreach ($problemsList as $index => $problemData) {
            try {
                $slug = $problemData['stat']['question__title_slug'] ?? '';
                $title = $problemData['stat']['question__title'] ?? '';
                $difficultyLevel = $problemData['difficulty']['level'] ?? 1;
                $isPremium = $problemData['paid_only'] ?? false;

                if ($isPremium || empty($slug)) {
                    $results['skipped']++;
                    continue;
                }

                $existing = \App\Models\Problem::where('slug', $slug)->first();
                if ($existing) {
                    $results['skipped']++;
                    continue;
                }

                $details = $this->fetchProblemDetails($slug);
                $this->sleep();

                $difficulty = match ($difficultyLevel) {
                    1 => 'easy',
                    2 => 'medium',
                    3 => 'hard',
                    default => 'easy',
                };

                $description = '';
                $inputExample = '';
                $outputExample = '';
                $constraints = '';
                $starterCode = '';
                $topics = [];

                if ($details) {
                    $description = $this->cleanHtml($details['content'] ?? '');
                    $rawTopics = $details['topicTags'] ?? [];
                    $topics = array_map(fn($t) => $t['name'] ?? $t, $rawTopics);
                    $starterCode = $this->extractStarterCode($details['codeSnippets'] ?? []);

                    $exampleTestCases = $details['exampleTestcaseList'] ?? [];
                    if (!empty($exampleTestCases)) {
                        $inputExample = implode("\n", $exampleTestCases);
                        $outputExample = $details['exampleTestcaseOutput'] ?? '';
                    }

                    $constraints = $this->cleanHtml($details['constraints'] ?? '');
                }

                $totalAcs = $problemData['stat']['total_acs'] ?? 0;
                $totalSubmitted = $problemData['stat']['total_submitted'] ?? 0;
                $frontendId = $problemData['stat']['frontend_question_id'] ?? 0;

                $tests = $this->generateTestsFromExamples($inputExample, $outputExample, $description);
                $tests = array_merge(
                    $this->getLeetCodeStandardTests($slug, $difficulty),
                    $tests
                );

                $problem = $this->saveProblem([
                    'title' => $title,
                    'description' => $description ?: 'Решите задачу: ' . $title,
                    'difficulty' => $difficulty,
                    'points' => match ($difficulty) {
                        'easy' => 10,
                        'medium' => 25,
                        'hard' => 50,
                        default => 10,
                    },
                    'input_example' => $inputExample ?: null,
                    'output_example' => $outputExample ?: null,
                    'constraints' => $constraints ?: null,
                    'starter_code' => $starterCode ?: null,
                    'language' => 'python',
                    'tests_json' => $tests,
                    'solved_count' => $totalAcs,
                    'attempt_count' => $totalSubmitted,
                    'topics' => $topics,
                    'source_url' => 'https://leetcode.com/problems/' . $slug . '/',
                    'source_id' => (string) $frontendId,
                ], $this->source);

                if ($problem) {
                    $results['created']++;
                    $this->log('Created: #' . $frontendId . ' ' . $title . ' (' . $difficulty . ', ' . count($tests) . ' tests)');
                } else {
                    $results['skipped']++;
                }
            } catch (\Exception $e) {
                $results['errors']++;
                Log::error('LeetCode parse error', [
                    'problem' => $title ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }

            if (($index + 1) % 100 === 0) {
                $this->log('Progress: ' . ($index + 1) . '/' . $results['total'] . ' created=' . $results['created']);
            }
        }

        return $results;
    }

    private function getLeetCodeStandardTests(string $slug, string $difficulty): array
    {
        $knownTests = [
            'two-sum' => [
                ['input' => '[2,7,11,15]\n9', 'expected' => '[0,1]', 'description' => 'Basic case'],
                ['input' => '[3,2,4]\n6', 'expected' => '[1,2]', 'description' => 'Middle elements'],
                ['input' => '[3,3]\n6', 'expected' => '[0,1]', 'description' => 'Duplicate values'],
                ['input' => '[1,5,8,3,9,2]\n11', 'expected' => '[1,3]', 'description' => 'Six elements'],
                ['input' => '[-1,-2,-3,-4,-5]\n-8', 'expected' => '[2,4]', 'description' => 'All negatives'],
                ['input' => '[0,4,3,0]\n0', 'expected' => '[0,3]', 'description' => 'Zero sum with zeros'],
                ['input' => '[1,2,3,4,5,6,7,8,9,10]\n19', 'expected' => '[8,9]', 'description' => 'Large array'],
                ['input' => '[-3,4,3,90]\n0', 'expected' => '[0,2]', 'description' => 'Mixed neg pos'],
                ['input' => str_repeat('[1,', 1000) . '2]\n3', 'expected' => '[0,1000]', 'description' => '1001 elements'],
                ['input' => '[5,75,25]\n100', 'expected' => '[1,2]', 'description' => 'Non-adjacent sum'],
            ],
            'reverse-string' => [
                ['input' => '["h","e","l","l","o"]', 'expected' => '["o","l","l","e","h"]', 'description' => 'Basic'],
                ['input' => '["H","a","n","n","a","h"]', 'expected' => '["h","a","n","n","a","H"]', 'description' => 'Mixed case'],
                ['input' => '["a"]', 'expected' => '["a"]', 'description' => 'Single char'],
                ['input' => str_repeat('["x",', 1000) . '"y"]', 'expected' => '', 'description' => '1001 chars'],
                ['input' => '[" "," ","a"]', 'expected' => '["a"," "," "]', 'description' => 'With spaces'],
                ['input' => '["A"," ","m","a","n",","," ","a"," ","p","l","a","n",","," ","a"," ","c","a","n","a","l",","," ","P","a","n","a","m","a"]', 'expected' => '', 'description' => 'Palindrome sentence'],
            ],
            'valid-parentheses' => [
                ['input' => '"()"', 'expected' => 'true', 'description' => 'Simple pair'],
                ['input' => '"()[]{}"', 'expected' => 'true', 'description' => 'Multiple types'],
                ['input' => '"(]"', 'expected' => 'false', 'description' => 'Mismatch close'],
                ['input' => '"(["', 'expected' => 'false', 'description' => 'Unclosed'],
                ['input' => '"{[]}"', 'expected' => 'true', 'description' => 'Nested'],
                ['input' => '"((()))"', 'expected' => 'true', 'description' => 'Deep nesting 3'],
                ['input' => '"((((()))))"', 'expected' => 'true', 'description' => 'Deep nesting 5'],
                ['input' => '"([{()}])"', 'expected' => 'true', 'description' => 'All types nested'],
                ['input' => '"(("', 'expected' => 'false', 'description' => 'Only opens'],
                ['input' => str_repeat('(', 1000) . str_repeat(')', 1000), 'expected' => 'true', 'description' => '2000 chars balanced'],
                ['input' => str_repeat('()', 500), 'expected' => 'true', 'description' => '500 pairs repeated'],
                ['input' => '"([{', 'expected' => 'false', 'description' => 'Three opens no close'],
            ],
            'palindrome-number' => [
                ['input' => '121', 'expected' => 'true', 'description' => 'Odd palindrome'],
                ['input' => '-121', 'expected' => 'false', 'description' => 'Negative'],
                ['input' => '10', 'expected' => 'false', 'description' => 'Ends with zero'],
                ['input' => '0', 'expected' => 'true', 'description' => 'Zero'],
                ['input' => '1234321', 'expected' => 'true', 'description' => '7-digit palindrome'],
                ['input' => '123454321', 'expected' => 'true', 'description' => '9-digit palindrome'],
                ['input' => '123456789987654321', 'expected' => 'true', 'description' => '18-digit palindrome'],
                ['input' => '123456789', 'expected' => 'false', 'description' => 'Non-palindrome large'],
                ['input' => '11', 'expected' => 'true', 'description' => 'Double digit'],
                ['input' => '1000021', 'expected' => 'false', 'description' => '7-digit non-palindrome'],
            ],
            'climbing-stairs' => [
                ['input' => '2', 'expected' => '2', 'description' => '2 steps'],
                ['input' => '3', 'expected' => '3', 'description' => '3 steps'],
                ['input' => '1', 'expected' => '1', 'description' => '1 step'],
                ['input' => '5', 'expected' => '8', 'description' => '5 steps'],
                ['input' => '10', 'expected' => '89', 'description' => '10 steps'],
                ['input' => '20', 'expected' => '10946', 'description' => '20 steps'],
                ['input' => '30', 'expected' => '1346269', 'description' => '30 steps'],
                ['input' => '35', 'expected' => '14930352', 'description' => '35 steps'],
                ['input' => '40', 'expected' => '165580141', 'description' => '40 steps'],
                ['input' => '45', 'expected' => '1836311903', 'description' => '45 steps - fib'],
            ],
            'best-time-to-buy-and-sell-stock' => [
                ['input' => '[7,1,5,3,6,4]', 'expected' => '5', 'description' => 'Basic case'],
                ['input' => '[7,6,4,3,1]', 'expected' => '0', 'description' => 'Always decreasing'],
                ['input' => '[1,2]', 'expected' => '1', 'description' => 'Two days profit'],
                ['input' => '[2,4,1]', 'expected' => '2', 'description' => 'Small profit'],
                ['input' => str_repeat('[', 1) . '1' . str_repeat(',', 49) . '50' . str_repeat(',', 1) . '1]', 'expected' => '49', 'description' => 'Last day high'],
                ['input' => '[' . implode(',', range(1, 50000)) . ']', 'expected' => '49999', 'description' => '50000 increasing'],
                ['input' => '[' . implode(',', array_reverse(range(1, 50000))) . ']', 'expected' => '0', 'description' => '50000 decreasing'],
                ['input' => '[3,3,3,3,3]', 'expected' => '0', 'description' => 'All same'],
                ['input' => '[1,2,3,4,5]', 'expected' => '4', 'description' => 'Linear increase'],
                ['input' => '[5,4,3,2,1]', 'expected' => '0', 'description' => 'Linear decrease'],
            ],
            'maximum-subarray' => [
                ['input' => '[-2,1,-3,4,-1,2,1,-5,4]', 'expected' => '6', 'description' => 'Mixed values'],
                ['input' => '[1]', 'expected' => '1', 'description' => 'Single element'],
                ['input' => '[5,4,-1,7,8]', 'expected' => '23', 'description' => 'Mostly positive'],
                ['input' => '[-1]', 'expected' => '-1', 'description' => 'Single negative'],
                ['input' => '[-2,-1]', 'expected' => '-1', 'description' => 'All negative'],
                ['input' => str_repeat('-1,', 1000) . '-1', 'expected' => '-1', 'description' => '1001 negatives'],
                ['input' => str_repeat('1,', 1000) . '1', 'expected' => '1001', 'description' => '1001 ones'],
                ['input' => '[0,0,0,0,0]', 'expected' => '0', 'description' => 'All zeros'],
                ['input' => '[-5,4,-1,7,8,-3]', 'expected' => '18', 'description' => 'Multiple subarrays'],
                ['input' => '[1,-1,1,-1,1,-1,1]', 'expected' => '1', 'description' => 'Alternating'],
            ],
            'fibonacci-number' => [
                ['input' => '2', 'expected' => '1', 'description' => 'F(2)'],
                ['input' => '3', 'expected' => '2', 'description' => 'F(3)'],
                ['input' => '0', 'expected' => '0', 'description' => 'F(0)'],
                ['input' => '1', 'expected' => '1', 'description' => 'F(1)'],
                ['input' => '10', 'expected' => '55', 'description' => 'F(10)'],
                ['input' => '20', 'expected' => '6765', 'description' => 'F(20)'],
                ['input' => '30', 'expected' => '832040', 'description' => 'F(30)'],
                ['input' => '37', 'expected' => '24157817', 'description' => 'F(37) max'],
            ],
        ];

        return $knownTests[$slug] ?? [];
    }

    private function fetchProblemsList(): ?array
    {
        $body = $this->fetchWithRetry($this->apiUrl . '/api/problems/algorithms/');
        if (!$body) return null;

        $data = json_decode($body, true);
        if (!is_array($data)) return null;

        return $data['stat_status_pairs'] ?? null;
    }

    private function fetchProblemDetails(string $slug): ?array
    {
        $query = 'query questionData($titleSlug: String!) {
            question(titleSlug: $titleSlug) {
                questionId
                questionFrontendId
                title
                titleSlug
                content
                difficulty
                topicTags { name slug }
                codeSnippets { lang langSlug code }
                exampleTestcaseList
                hints
            }
        }';

        $payload = json_encode([
            'query' => $query,
            'variables' => ['titleSlug' => $slug],
        ]);

        $ch = curl_init($this->graphqlUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Referer: https://leetcode.com/problems/' . $slug . '/',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $body = curl_exec($ch);
        curl_close($ch);

        if (!$body) return null;

        $data = json_decode($body, true);
        return $data['data']['question'] ?? null;
    }

    private function extractStarterCode(array $snippets): string
    {
        foreach ($snippets as $s) {
            if (($s['lang'] ?? '') === 'python3' || ($s['langSlug'] ?? '') === 'python3') {
                return $s['code'] ?? '';
            }
        }
        foreach ($snippets as $s) {
            if (($s['lang'] ?? '') === 'python' || ($s['langSlug'] ?? '') === 'python') {
                return $s['code'] ?? '';
            }
        }
        return '';
    }

    private function cleanHtml(string $html): string
    {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    private function log(string $message): void
    {
        echo '[LeetCode] ' . $message . "\n";
    }
}
