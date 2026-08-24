<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InterviewController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function index()
    {
        $interviews = Interview::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('interview.index', compact('interviews'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:technical,behavioral,coding,system_design',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $typeLabels = [
            'technical' => 'Техническое собеседование',
            'behavioral' => 'Поведенческое собеседование',
            'coding' => 'Кодинг собеседование',
            'system_design' => 'Проектирование систем',
        ];

        $difficultyLabels = [
            'easy' => 'Лёгкий',
            'medium' => 'Средний',
            'hard' => 'Сложный',
        ];

        $interview = Interview::create([
            'user_id' => Auth::id(),
            'title' => $typeLabels[$request->type] . ' - ' . $difficultyLabels[$request->difficulty],
            'type' => $request->type,
            'difficulty' => $request->difficulty,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        session([
            'interview_id' => $interview->id,
            'interview_question_index' => 0,
            'interview_questions' => [],
            'interview_current_question' => null,
        ]);

        return redirect()->route('interview.room', $interview->id);
    }

    public function room(Request $request, $id)
    {
        $interview = Interview::where('user_id', Auth::id())->findOrFail($id);

        if ($interview->status !== 'in_progress') {
            return redirect()->route('interview.result', $interview->id);
        }

        $questionIndex = (int) $request->query('q', session('interview_question_index', 0));
        $questions = session('interview_questions', []);

        if ($questionIndex >= count($questions)) {
            $question = $this->generateQuestion($interview, $questions);
            $questions[] = $question;
            session([
                'interview_question_index' => $questionIndex,
                'interview_current_question' => $question,
                'interview_questions' => $questions,
            ]);
        } else {
            $question = $questions[$questionIndex];
            session(['interview_question_index' => $questionIndex]);
        }

        $startedAt = $interview->started_at ? $interview->started_at->timestamp : now()->timestamp;

        return view('interview.room', compact('interview', 'question', 'questionIndex', 'startedAt'));
    }

    public function answer(Request $request, $id)
    {
        $interview = Interview::where('user_id', Auth::id())->findOrFail($id);

        if ($interview->status !== 'in_progress') {
            return redirect()->route('interview.result', $interview->id);
        }

        $validator = Validator::make($request->all(), [
            'answer' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $questionIndex = session('interview_question_index', 0);
        $questions = session('interview_questions', []);

        $currentQuestion = $questions[$questionIndex] ?? session('interview_current_question');

        $evaluation = $this->evaluateAnswer($interview, $currentQuestion, $request->answer);

        $questions[$questionIndex] = array_merge($currentQuestion, [
            'user_answer' => $request->answer,
            'evaluation' => $evaluation,
        ]);

        $nextIndex = $questionIndex + 1;
        $maxQuestions = 5;

        if ($nextIndex >= $maxQuestions) {
            return $this->completeInterview($interview, $questions);
        }

        session([
            'interview_question_index' => $nextIndex,
            'interview_questions' => $questions,
        ]);

        return redirect('/interview/' . $interview->id . '?q=' . $nextIndex);
    }

    public function result($id)
    {
        $interview = Interview::where('user_id', Auth::id())->findOrFail($id);

        if ($interview->status === 'in_progress') {
            return redirect()->route('interview.room', $interview->id);
        }

        return view('interview.result', compact('interview'));
    }

    public function destroy($id)
    {
        $interview = Interview::where('user_id', Auth::id())->findOrFail($id);
        $interview->delete();

        return redirect()->route('interview.index')->with('success', 'Interview deleted.');
    }

    public function finish(Request $request, $id)
    {
        $interview = Interview::where('user_id', Auth::id())->findOrFail($id);

        if ($interview->status !== 'in_progress') {
            return response()->json(['ok' => true]);
        }

        $answer = $request->input('answer', '');
        $questions = session('interview_questions', []);
        $questionIndex = session('interview_question_index', 0);

        if ($answer && trim($answer) !== '') {
            $currentQuestion = $questions[$questionIndex] ?? session('interview_current_question');
            if ($currentQuestion) {
                $evaluation = $this->evaluateAnswer($interview, $currentQuestion, $answer);
                $questions[$questionIndex] = array_merge($currentQuestion, [
                    'user_answer' => $answer,
                    'evaluation' => $evaluation,
                ]);
            }
        }

        return $this->completeInterview($interview, $questions);
    }

    public function aiChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required_without:image|string',
            'interview_id' => 'nullable|integer',
            'context' => 'nullable|array',
            'image' => 'nullable|string',
            'image_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $interviewId = $request->interview_id ?? session('interview_id');
        $interview = Interview::where('user_id', Auth::id())->find($interviewId);

        if (!$interview) {
            return response()->json(['error' => 'Interview not found'], 404);
        }

        $message = $request->message ?: 'Проанализируй этот скриншот и помоги мне с вопросом собеседования.';
        $context = $this->buildChatContext($interview, $request->context);

        $langInstruction = "ВАЖНО: Отвечай ТОЛЬКО на русском языке. Все ответы, объяснения и рекомендации — исключительно на русском.";

        if ($request->has('image') && $request->image) {
            $contents = $this->geminiService->buildContentsWithImage(
                Auth::id(),
                $langInstruction . "\n\n" . $message . "\n\nContext: " . $context,
                $request->image,
                $request->image_type ?? 'image/jpeg'
            );
        } else {
            $fullMessage = $langInstruction . "\n\n" . $message;
            if ($context) {
                $fullMessage .= "\n\nContext: " . $context;
            }
            $contents = $this->geminiService->buildContents(Auth::id(), $fullMessage);
        }

        $response = $this->geminiService->callWithKey($contents, [
            'temperature' => 0.8,
            'maxOutputTokens' => 1024,
        ]);

        $reply = $response['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I could not generate a response.';

        return response()->json(['reply' => $reply]);
    }

    protected function generateQuestion(Interview $interview, array $previousQuestions = []): array
    {
        $previousQs = collect($previousQuestions)
            ->pluck('question')
            ->filter()
            ->implode('\n');

        $prompt = $this->buildQuestionPrompt($interview, $previousQs);

        $contents = $this->geminiService->buildContents(Auth::id(), $prompt, 'Отвечай ТОЛЬКО на русском языке. Верни JSON без объяснений.');
        $response = $this->geminiService->callWithKey($contents, [
            'temperature' => 0.7,
            'maxOutputTokens' => 2048,
        ]);

        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $question = $this->parseQuestionJson($text);

        return $question + [
            'type' => $this->getQuestionType($interview->type),
            'difficulty' => $interview->difficulty,
            'index' => count($previousQuestions) + 1,
        ];
    }

    protected function buildQuestionPrompt(Interview $interview, string $previousQuestions): string
    {
        $typePrompts = [
            'technical' => 'Задай технический вопрос о концепциях программирования, алгоритмах, структурах данных или архитектуре систем.',
            'behavioral' => 'Задай вопрос поведенческого собеседования, используя метод STAR (Ситуация, Задача, Действие, Результат).',
            'coding' => 'Предложи задачу на программирование с чётким условием, форматом ввода/вывода и ограничениями. Попроси кандидата написать решение на предпочитаемом языке.',
            'system_design' => 'Предложи задачу на проектирование системы (например, сокращатель URL, чат-приложение, rate limiter). Попроси кандидата описать архитектуру.',
        ];

        $difficultyGuidance = [
            'easy' => 'Сделай вопрос простым и понятным, проверяющим базовые знания.',
            'medium' => 'Сделай вопрос средней сложности, требующий анализа и знания компромиссов.',
            'hard' => 'Сделай сложный вопрос, требующий глубокого понимания, оптимизации и учёта крайних случаев.',
        ];

        $type = $typePrompts[$interview->type] ?? $typePrompts['technical'];
        $difficulty = $difficultyGuidance[$interview->difficulty] ?? $difficultyGuidance['medium'];

        $questionType = $this->getQuestionType($interview->type);

        $jsonFormat = match ($questionType) {
            'multiple_choice' => '{"type": "multiple_choice", "question": "...", "options": ["A", "B", "C", "D"], "correct_answer": "A"}',
            'code_writing' => '{"type": "code_writing", "question": "...", "starter_code": "def solve():\\n    pass", "language": "python"}',
            default => '{"type": "open_ended", "question": "...", "expected_key_points": ["point1", "point2"]}',
        };

        return <<<PROMPT
Ты — эксперт-интервьюер, проводящий {$interview->type} собеседование уровня {$interview->difficulty}.

{$type}
{$difficulty}

Ранее заданные вопросы:
{$previousQuestions}

Сгенерируй ОДИН новый вопрос. Краткий, но содержательный.

Отвечай ТОЛЬКО валидным JSON. Весь текст на РУССКОМ языке. Без markdown и объяснений.

Формат:
{$jsonFormat}
PROMPT;
    }

    protected function getQuestionType(string $type): string
    {
        return match ($type) {
            'technical' => 'multiple_choice',
            'behavioral' => 'open_ended',
            'coding' => 'code_writing',
            'system_design' => 'open_ended',
            default => 'open_ended',
        };
    }

    protected function parseQuestionJson(string $text): array
    {
        $text = trim($text);
        $text = preg_replace('/^```json\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);
        $text = trim($text);

        $data = json_decode($text, true);

        if (!is_array($data)) {
            if (preg_match('/\{[\s\S]*\}/', $text, $m)) {
                $data = json_decode($m[0], true);
            }
        }

        if (!is_array($data) || !isset($data['question'])) {
            $extracted = $this->extractQuestionFromRaw($text);
            if ($extracted) {
                $data = $extracted;
            }
        }

        if (!is_array($data) || !isset($data['question'])) {
            return [
                'type' => 'open_ended',
                'question' => $text ?: 'Опишите сложный проект, над которым вы работали.',
                'expected_key_points' => ['Проблема', 'Решение', 'Результат'],
            ];
        }

        if (is_string($data['question'])) {
            $inner = json_decode($data['question'], true);
            if (is_array($inner) && isset($inner['question'])) {
                $data = array_merge($inner, array_diff_key($data, ['question' => 0]));
            }
        }

        foreach (['options', 'expected_key_points'] as $key) {
            if (isset($data[$key]) && is_string($data[$key])) {
                $decoded = json_decode($data[$key], true);
                if (is_array($decoded)) {
                    $data[$key] = $decoded;
                }
            }
        }

        return $data;
    }

    protected function extractQuestionFromRaw(string $text): ?array
    {
        $q = null;
        $opts = null;
        $ans = null;
        $type = null;

        if (preg_match('/"question"\s*:\s*"(.*?)(?:"\s*[,}])/', $text, $m)) {
            $q = stripcslashes(trim($m[1]));
        } elseif (preg_match('/"question"\s*:\s*"(.+)/', $text, $m)) {
            $q = stripcslashes(trim($m[1]));
        }
        if (preg_match('/"type"\s*:\s*"([^"]+)"/', $text, $m)) {
            $type = $m[1];
        }
        if (preg_match('/"options"\s*:\s*(\[[\s\S]*?\])/', $text, $m)) {
            $o = json_decode($m[1], true);
            if (is_array($o)) {
                $opts = $o;
            }
        }
        if (preg_match('/"correct_answer"\s*:\s*"([^"]*)"/', $text, $m)) {
            $ans = $m[1];
        }

        if ($q) {
            $result = ['question' => $q];
            if ($type) {
                $result['type'] = $type;
            }
            if ($opts) {
                $result['options'] = $opts;
            }
            if ($ans) {
                $result['correct_answer'] = $ans;
            }
            return $result;
        }

        return null;
    }

    protected function evaluateAnswer(Interview $interview, array $question, string $answer): array
    {
        $prompt = $this->buildEvaluationPrompt($interview, $question, $answer);

        $contents = $this->geminiService->buildContents(Auth::id(), $prompt, 'Оцени ответ кандидата на собеседовании. Отвечай ТОЛЬКО на русском языке. Верни JSON без объяснений.');
        $response = $this->geminiService->callWithKey($contents, [
            'temperature' => 0.3,
            'maxOutputTokens' => 1024,
        ]);

        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $evaluation = $this->parseEvaluationJson($text);

        return $evaluation + [
            'score' => $evaluation['score'] ?? 0,
            'feedback' => $evaluation['feedback'] ?? 'Ответ получен.',
            'correct' => $evaluation['correct'] ?? false,
        ];
    }

    protected function buildEvaluationPrompt(Interview $interview, array $question, string $answer): string
    {
        $questionText = $question['question'] ?? '';
        $questionType = $question['type'] ?? 'open_ended';

        $typeInstructions = match ($questionType) {
            'multiple_choice' => 'Проверь, совпадает ли выбранный вариант с правильным ответом.',
            'code_writing' => 'Оцени правильность кода, эффективность и обработку крайних случаев.',
            'open_ended' => 'Оцени релевантность, глубину, структуру (STAR для поведенческих вопросов) и раскрытие ключевых моментов.',
            default => 'Оцени качество и релевантность ответа.',
        };

        $sanitizedAnswer = strip_tags($answer);
        $sanitizedAnswer = str_replace(["\n", "\r"], [' ', ''], $sanitizedAnswer);
        $sanitizedAnswer = mb_substr($sanitizedAnswer, 0, 5000);

        return <<<PROMPT
Ты — эксперт-интервьюер, оценивающий ответ кандидата.

Тип собеседования: {$interview->type}
Уровень сложности: {$interview->difficulty}
Тип вопроса: {$questionType}
Вопрос: {$questionText}
Ответ кандидата: {$sanitizedAnswer}

{$typeInstructions}

ВАЖНО: Весь текст обратной связи, сильных сторон и рекомендаций по улучшению должен быть на РУССКОМ языке.

Отвечай ТОЛЬКО валидным JSON:
{
  "score": 0-100,
  "correct": true/false,
  "feedback": "Конструктивная обратная связь с объяснением оценки",
  "strengths": ["сильная сторона 1", "сильная сторона 2"],
  "improvements": ["рекомендация 1", "рекомендация 2"]
}
PROMPT;
    }

    protected function parseEvaluationJson(string $text): array
    {
        $text = trim($text);
        $text = preg_replace('/^```json\s*/', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);

        $data = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'score' => 50,
                'correct' => false,
                'feedback' => 'Ответ получен. Рекомендуется ручная проверка.',
                'strengths' => [],
                'improvements' => ['Добавьте больше конкретных деталей'],
            ];
        }

        return $data;
    }

    protected function completeInterview(Interview $interview, array $questions): \Illuminate\Http\RedirectResponse
    {
        $evaluations = collect($questions)->pluck('evaluation')->filter()->values();
        $totalScore = $evaluations->isEmpty() ? 0 : (int) $evaluations->avg('score');
        $feedback = $this->generateFeedback($interview, $questions, $totalScore);

        $interview->update([
            'status' => 'completed',
            'score' => $totalScore,
            'feedback' => $feedback,
            'completed_at' => now(),
        ]);

        session()->forget(['interview_id', 'interview_question_index', 'interview_current_question', 'interview_questions']);

        return redirect()->route('interview.result', $interview->id);
    }

    protected function generateFeedback(Interview $interview, array $questions, int $score): string
    {
        $prompt = <<<PROMPT
Ты — эксперт-интервьюер, дающий итоговую обратную связь по собеседованию типа "{$interview->type}" уровня "{$interview->difficulty}".

Набрано баллов: {$score}/100

Вопросы и оценки:
PROMPT;

        foreach ($questions as $q) {
            if (isset($q['evaluation'])) {
                $prompt .= "\nВ: " . ($q['question'] ?? 'N/A') . "\n";
                $prompt .= "Оценка: " . ($q['evaluation']['score'] ?? 0) . "/100\n";
                $prompt .= "Обратная связь: " . ($q['evaluation']['feedback'] ?? 'N/A') . "\n";
            }
        }

        $prompt .= <<<PROMPT

Дай развёрнутую итоговую обратную связь из 3-4 абзацев, включающую:
1. Общую оценку работы
2. Ключевые сильные стороны
3. Области для улучшения
4. Конкретные рекомендации на будущее

Отвечай обычным текстом, без JSON или markdown. Весь текст на РУССКОМ языке.
PROMPT;

        $contents = $this->geminiService->buildContents(Auth::id(), $prompt, 'Дай итоговую обратную связь по собеседованию на русском языке.');
        $response = $this->geminiService->callWithKey($contents, [
            'temperature' => 0.5,
            'maxOutputTokens' => 1536,
        ]);

        return $response['candidates'][0]['content']['parts'][0]['text'] ?? "Собеседование завершено. Набрано баллов: {$score}/100.";
    }

    protected function buildChatContext(Interview $interview, ?array $context): string
    {
        $contextParts = [
            "Тип собеседования: {$interview->type}",
            "Уровень сложности: {$interview->difficulty}",
            "Статус: {$interview->status}",
        ];

        if ($context && isset($context['current_question'])) {
            $contextParts[] = "Текущий вопрос: " . ($context['current_question']['question'] ?? '');
        }

        if ($context && isset($context['question_history'])) {
            $history = collect($context['question_history'])->take(-3)->map(fn($q) => "В: " . ($q['question'] ?? '') . " | О: " . ($q['user_answer'] ?? ''))->implode("\n");
            $contextParts[] = "Последние ответы:\n" . $history;
        }

        return implode("\n", $contextParts);
    }
}
