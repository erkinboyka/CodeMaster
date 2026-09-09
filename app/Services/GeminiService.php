<?php

namespace App\Services;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected array $apiKeys;
    protected int $currentKeyIndex = 0;

    public function __construct()
    {
        $this->apiKeys = array_filter(explode(',', config('services.gemini.keys', '')));
    }

    public function callApi(array $contents, array $configOverrides = []): array
    {
        return $this->callWithKey($contents, $configOverrides);
    }

    public function keyPool(): string
    {
        if (empty($this->apiKeys)) {
            throw new \RuntimeException('No Gemini API keys configured.');
        }

        $key = $this->apiKeys[$this->currentKeyIndex % count($this->apiKeys)];
        $this->currentKeyIndex = ($this->currentKeyIndex + 1) % count($this->apiKeys);

        return $key;
    }

    public function callWithKey(array $contents, array $configOverrides = []): array
    {
        $maxRetries = min(3, count($this->apiKeys));
        $lastException = null;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            $key = $this->keyPool();
            $model = config('services.gemini.model', 'gemini-2.5-flash');

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}";

            $payload = [
                'contents' => $contents,
                'generationConfig' => array_merge([
                    'temperature' => 0.7,
                    'maxOutputTokens' => 2048,
                ], $configOverrides),
            ];

            try {
                $response = Http::timeout(60)->post($url, $payload);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::warning('Gemini API error', [
                    'status' => $response->status(),
                    'attempt' => $attempt + 1,
                ]);

                if ($response->status() === 429 || $response->status() === 503) {
                    usleep(500000);
                    continue;
                }

                break;
            } catch (\Exception $e) {
                $lastException = $e;
                Log::warning('Gemini API exception', [
                    'message' => $e->getMessage(),
                    'attempt' => $attempt + 1,
                ]);
                continue;
            }
        }

        return ['candidates' => []];
    }

    public function buildContents(int $userId, string $message, ?string $context = null): array
    {
        $history = ChatMessage::where('user_id', $userId)
            ->orderBy('sent_at', 'asc')
            ->limit(20)
            ->get()
            ->map(fn($msg) => [
                'role' => $msg->sender === 'ai' ? 'model' : 'user',
                'parts' => [['text' => $msg->message_text]],
            ])
            ->toArray();

        $systemInstruction = 'You are an AI tutor for CodeMaster, an IT education platform. ' .
            'Help students learn programming, explain concepts clearly, provide examples, and guide them through their learning journey. ' .
            'Be encouraging, patient, and thorough in your explanations.';

        if ($context) {
            $systemInstruction .= "\n\nContext: {$context}";
        }

        $contents = array_merge([
            ['role' => 'user', 'parts' => [['text' => $systemInstruction]]],
            ['role' => 'model', 'parts' => [['text' => 'I understand. I am your AI tutor for CodeMaster. I will help you learn programming and guide you through your learning journey. What would you like to learn about?']]],
        ], $history);

        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        return $contents;
    }

    public function buildContentsWithImage(int $userId, string $message, string $imageBase64, string $mimeType = 'image/jpeg'): array
    {
        $contents = $this->buildContents($userId, $message);

        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => 'Here is a screenshot of my current screen/code during the interview. Please analyze it and help me if needed.'],
                ['inline_data' => [
                    'mime_type' => $mimeType,
                    'data' => $imageBase64,
                ]],
            ],
        ];

        return $contents;
    }

    public function trimUserChatMessages(int $userId, int $keepLast = 50): void
    {
        $total = ChatMessage::where('user_id', $userId)->count();

        if ($total > $keepLast) {
            $toDelete = ChatMessage::where('user_id', $userId)
                ->orderBy('sent_at', 'asc')
                ->limit($total - $keepLast)
                ->pluck('id');

            ChatMessage::whereIn('id', $toDelete)->delete();
        }
    }
}
