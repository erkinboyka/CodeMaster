<?php
if (!defined('APP_INIT')) {
    die('Direct access not permitted');
}

if (!function_exists('tfTelegramFeedCachePath')) {
    function tfTelegramFeedCachePath($channel)
    {
        $safe = strtolower((string) preg_replace('/[^a-zA-Z0-9_]/', '', (string) $channel));
        if ($safe === '') {
            $safe = 'channel';
        }
        return rtrim((string) sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'codemaster_tg_feed_' . $safe . '.json';
    }
}

if (!function_exists('tfTelegramFeedHttpGet')) {
    function tfTelegramFeedHttpGet($url, $timeout = 15)
    {
        $headers = [
            'Accept: text/html,application/xhtml+xml',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36'
        ];

        if (function_exists('tfHttpRequest')) {
            return tfHttpRequest('GET', (string) $url, $headers, '', (int) $timeout);
        }

        $raw = false;
        $status = 0;
        $error = '';

        if (function_exists('curl_init')) {
            $ch = curl_init((string) $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, (int) $timeout);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $raw = curl_exec($ch);
            $error = (string) curl_error($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => (int) $timeout,
                    'ignore_errors' => true,
                    'header' => implode("\r\n", $headers) . "\r\n"
                ]
            ]);
            $raw = @file_get_contents((string) $url, false, $context);
            if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', (string) $http_response_header[0], $m)) {
                $status = (int) $m[1];
            }
        }

        $body = is_string($raw) ? $raw : '';
        return [
            'ok' => $raw !== false && $status >= 200 && $status < 300,
            'status' => $status,
            'body' => $body,
            'error' => $error
        ];
    }
}

if (!function_exists('tfTelegramFeedText')) {
    function tfTelegramFeedText($value)
    {
        $text = (string) $value;
        if (function_exists('normalizeMojibakeText')) {
            $text = normalizeMojibakeText($text);
        }
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim((string) $text);
    }
}

if (!function_exists('tfTelegramFeedExcerpt')) {
    function tfTelegramFeedExcerpt($text, $limit = 220)
    {
        $text = tfTelegramFeedText($text);
        if ($text === '') {
            return '';
        }

        $limit = max(32, (int) $limit);
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            if (mb_strlen($text, 'UTF-8') <= $limit) {
                return $text;
            }
            return mb_substr($text, 0, $limit - 1, 'UTF-8') . '…';
        }

        if (strlen($text) <= $limit) {
            return $text;
        }
        return substr($text, 0, $limit - 1) . '...';
    }
}

if (!function_exists('tfTelegramFeedExtractImageFromStyle')) {
    function tfTelegramFeedExtractImageFromStyle($style)
    {
        $style = (string) $style;
        if ($style === '') {
            return '';
        }
        if (preg_match("/background-image:url\\('([^']+)'\\)/", $style, $m)) {
            return html_entity_decode((string) $m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        if (preg_match('/background-image:url\\(([^)]+)\\)/', $style, $m)) {
            return trim((string) $m[1], " \t\n\r\0\x0B\"'");
        }
        return '';
    }
}

if (!function_exists('tfTelegramFeedParsePosts')) {
    function tfTelegramFeedParsePosts($html, $limit = 12)
    {
        $html = (string) $html;
        if ($html === '') {
            return [];
        }
        if (!class_exists('DOMDocument') || !class_exists('DOMXPath')) {
            return [];
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        libxml_clear_errors();
        if (!$loaded) {
            return [];
        }

        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' tgme_widget_message ') and @data-post]");
        if (!$nodes || $nodes->length === 0) {
            return [];
        }

        $items = [];
        foreach ($nodes as $node) {
            if (!($node instanceof DOMElement)) {
                continue;
            }
            $dataPost = trim((string) $node->getAttribute('data-post'));
            if ($dataPost === '' || strpos($dataPost, '/') === false) {
                continue;
            }

            [$channel, $postIdRaw] = explode('/', $dataPost, 2);
            $postId = (int) preg_replace('/\D+/', '', (string) $postIdRaw);
            if ($channel === '' || $postId <= 0) {
                continue;
            }

            $textNode = $xpath->query(".//div[contains(concat(' ', normalize-space(@class), ' '), ' tgme_widget_message_text ')]", $node)->item(0);
            $captionNode = $xpath->query(".//div[contains(concat(' ', normalize-space(@class), ' '), ' tgme_widget_message_caption ')]", $node)->item(0);
            $text = '';
            if ($textNode instanceof DOMNode) {
                $text = tfTelegramFeedText($textNode->textContent);
            }
            if ($text === '' && $captionNode instanceof DOMNode) {
                $text = tfTelegramFeedText($captionNode->textContent);
            }

            $photoNode = $xpath->query(".//a[contains(concat(' ', normalize-space(@class), ' '), ' tgme_widget_message_photo_wrap ')]", $node)->item(0);
            $image = '';
            if ($photoNode instanceof DOMElement) {
                $image = tfTelegramFeedExtractImageFromStyle($photoNode->getAttribute('style'));
            }
            if ($image === '') {
                $imageNode = $xpath->query('.//img', $node)->item(0);
                if ($imageNode instanceof DOMElement) {
                    $image = trim((string) $imageNode->getAttribute('src'));
                }
            }

            $timeNode = $xpath->query(".//a[contains(concat(' ', normalize-space(@class), ' '), ' tgme_widget_message_date ')]/time", $node)->item(0);
            $datetime = '';
            $timeLabel = '';
            if ($timeNode instanceof DOMElement) {
                $datetime = trim((string) $timeNode->getAttribute('datetime'));
                $timeLabel = tfTelegramFeedText($timeNode->textContent);
            }

            $items[] = [
                'id' => $postId,
                'channel' => (string) $channel,
                'url' => 'https://t.me/' . rawurlencode((string) $channel) . '/' . $postId,
                'text' => $text,
                'excerpt' => tfTelegramFeedExcerpt($text, 240),
                'image' => $image,
                'datetime' => $datetime,
                'time_label' => $timeLabel
            ];
        }

        usort($items, static function ($a, $b) {
            return ((int) ($b['id'] ?? 0)) <=> ((int) ($a['id'] ?? 0));
        });

        $limit = max(1, (int) $limit);
        return array_slice($items, 0, $limit);
    }
}

if (!function_exists('tfTelegramFeedGetPosts')) {
    function tfTelegramFeedGetPosts($channel, $limit = 12, $ttl = 900)
    {
        $channel = trim((string) $channel);
        if (!preg_match('/^[A-Za-z0-9_]{4,64}$/', $channel)) {
            return ['posts' => [], 'stale' => false, 'error' => 'invalid_channel'];
        }

        $cachePath = tfTelegramFeedCachePath($channel);
        $ttl = max(60, (int) $ttl);
        $cached = null;

        if (is_file($cachePath)) {
            $raw = @file_get_contents($cachePath);
            $decoded = is_string($raw) ? json_decode($raw, true) : null;
            if (is_array($decoded) && is_array($decoded['posts'] ?? null)) {
                $cached = $decoded;
                $age = time() - (int) ($decoded['fetched_at'] ?? 0);
                if ($age >= 0 && $age <= $ttl) {
                    return ['posts' => $decoded['posts'], 'stale' => false, 'error' => ''];
                }
            }
        }

        $response = tfTelegramFeedHttpGet('https://t.me/s/' . rawurlencode($channel), 20);
        if (!empty($response['ok'])) {
            $posts = tfTelegramFeedParsePosts((string) ($response['body'] ?? ''), (int) $limit);
            if (!empty($posts)) {
                $payload = [
                    'fetched_at' => time(),
                    'posts' => $posts
                ];
                @file_put_contents($cachePath, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                return ['posts' => $posts, 'stale' => false, 'error' => ''];
            }
        }

        if (is_array($cached) && !empty($cached['posts'])) {
            return ['posts' => array_slice($cached['posts'], 0, max(1, (int) $limit)), 'stale' => true, 'error' => 'stale_cache'];
        }

        return ['posts' => [], 'stale' => false, 'error' => 'fetch_failed'];
    }
}
