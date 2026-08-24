<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\GeminiService;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiTutorController extends Controller
{
    protected $geminiService;
    protected $gamificationService;

    public function __construct(GeminiService $geminiService, GamificationService $gamificationService)
    {
        $this->geminiService = $geminiService;
        $this->gamificationService = $gamificationService;
    }

    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'context' => 'nullable|string',
        ]);

        $userId = Auth::id();
        $user = Auth::user();

        if ($user->ai_tokens < GamificationService::AI_TOKEN_CHAT_COST) {
            return response()->json([
                'error' => 'Недостаточно токенов для обращения к ИИ',
                'tokens' => $user->ai_tokens,
            ], 403);
        }

        $this->geminiService->trimUserChatMessages($userId, 50);

        $sanitizedMessage = strip_tags($validated['message']);
        $sanitizedMessage = str_replace(['<', '>'], ['', ''], $sanitizedMessage);

        ChatMessage::create([
            'user_id' => $userId,
            'sender' => 'user',
            'message_text' => $sanitizedMessage,
            'sent_at' => now(),
        ]);

        $contents = $this->geminiService->buildContents($userId, $sanitizedMessage, $validated['context'] ?? null);

        $response = $this->geminiService->callApi($contents);

        $reply = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($reply === null) {
            return response()->json([
                'error' => 'Не удалось получить ответ от ИИ. Попробуйте снова.',
                'tokens' => $user->ai_tokens,
            ], 503);
        }

        ChatMessage::create([
            'user_id' => $userId,
            'sender' => 'ai',
            'message_text' => $reply,
            'sent_at' => now(),
        ]);

        $this->gamificationService->deductAiTokens($user, GamificationService::AI_TOKEN_CHAT_COST);

        $user->refresh();

        return response()->json([
            'reply' => $reply,
            'tokens_remaining' => $user->ai_tokens,
        ]);
    }

    public function getChat()
    {
        $messages = ChatMessage::where('user_id', Auth::id())
            ->orderBy('sent_at', 'asc')
            ->limit(50)
            ->get();

        return response()->json(['messages' => $messages]);
    }

    public function clearChat()
    {
        ChatMessage::where('user_id', Auth::id())->delete();

        return response()->json(['success' => true]);
    }
}
