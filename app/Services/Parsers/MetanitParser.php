<?php

namespace App\Services\Parsers;

use Illuminate\Support\Facades\Log;

class MetanitParser extends BaseParser
{
    protected string $source = 'metanit';
    protected int $requestDelay = 1200;

    private string $baseUrl = 'https://metanit.com';

    private array $sections = [
        ['url' => '/net/', 'topic' => 'C# / .NET'],
        ['url' => '/java/', 'topic' => 'Java'],
        ['url' => '/python/', 'topic' => 'Python'],
        ['url' => '/javascript/', 'topic' => 'JavaScript'],
        ['url' => '/cpp/', 'topic' => 'C++'],
        ['url' => '/sql/', 'topic' => 'SQL'],
        ['url' => '/web/', 'topic' => 'HTML/CSS'],
        ['url' => '/kotlin/', 'topic' => 'Kotlin'],
        ['url' => '/dart/', 'topic' => 'Dart/Flutter'],
    ];

    public function parse(): array
    {
        $results = ['total' => 0, 'created' => 0, 'skipped' => 0, 'errors' => 0];

        foreach ($this->sections as $section) {
            try {
                $r = $this->parseSection($section);
                foreach ($r as $k => $v) $results[$k] += $v;
            } catch (\Exception $e) {
                Log::error("Metanit {$section['topic']}: " . $e->getMessage());
                $results['errors']++;
            }
        }

        return $results;
    }

    private function parseSection(array $section): array
    {
        $results = ['total' => 0, 'created' => 0, 'skipped' => 0, 'errors' => 0];

        $body = $this->fetchWithRetry($this->baseUrl . $section['url']);
        if (!$body) return $results;

        $urls = $this->extractLinks($body, $section['url']);
        $this->log("Found " . count($urls) . " articles in {$section['topic']}");

        foreach ($urls as $url) {
            try {
                $results['total']++;
                $slug = 'metanit-' . md5($url);
                $existing = \App\Models\Problem::where('slug', $slug)->first();
                if ($existing) { $results['skipped']++; continue; }

                $article = $this->fetchArticle($this->baseUrl . $url);
                $this->sleep();

                if (!$article) { $results['errors']++; continue; }

                $tests = [
                    ['input' => '', 'expected' => '', 'description' => 'Study the article'],
                    ['input' => $article['title'], 'expected' => '', 'description' => 'Apply concept'],
                ];

                $problem = $this->saveProblem([
                    'title' => $article['title'],
                    'description' => $article['content'],
                    'difficulty' => 'easy',
                    'points' => 5,
                    'language' => 'python',
                    'tests_json' => $tests,
                    'source_url' => $this->baseUrl . $url,
                    'source_id' => md5($url),
                    'topics' => [$section['topic']],
                ], $this->source);

                if ($problem) {
                    $results['created']++;
                    $this->log("Created: {$article['title']}");
                } else {
                    $results['skipped']++;
                }
            } catch (\Exception $e) {
                $results['errors']++;
            }
        }

        return $results;
    }

    private function extractLinks(string $html, string $path): array
    {
        $urls = [];
        $pattern = '/href="(' . preg_quote($path, '/') . '[^"]+)"/i';
        if (preg_match_all($pattern, $html, $m)) {
            foreach ($m[1] as $url) {
                $url = rtrim($url, '/');
                if (!in_array($url, $urls) && $url !== $path) {
                    $urls[] = $url;
                }
            }
        }
        return array_slice($urls, 100);
    }

    private function fetchArticle(string $url): ?array
    {
        $body = $this->fetchWithRetry($url);
        if (!$body) return null;

        $title = '';
        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $body, $m)) {
            $title = strip_tags(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8'));
        }
        if (empty($title) && preg_match('/<title>(.*?)<\/title>/is', $body, $m)) {
            $title = strip_tags(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8'));
            $title = preg_replace('/\s*[-–|]\s*METANIT.*$/i', '', $title);
        }
        if (empty(trim($title))) return null;

        $content = '';
        if (preg_match('/<article[^>]*>(.*?)<\/article>/is', $body, $m)) {
            $content = $m[1];
        } elseif (preg_match('/<h1[^>]*>.*?<\/h1>(.*?)(?:<footer|<div class="footer")/is', $body, $m)) {
            $content = $m[1];
        }

        $content = strip_tags($content, '<br><p><b><i><ul><ol><li><code><pre><h2><h3><h4>');
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);

        if (strlen($content) > 10000) $content = substr($content, 0, 10000) . '...';

        return ['title' => trim($title), 'content' => $content ?: 'Содержимое статьи отсутствует.'];
    }

    private function log(string $msg): void { echo "[Metanit] {$msg}\n"; }
}
