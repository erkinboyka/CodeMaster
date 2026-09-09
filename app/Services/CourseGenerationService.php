<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseSkill;
use App\Models\CourseStep;
use App\Models\CourseStepTest;
use App\Models\CourseTestVariant;
use App\Models\CourseTestAnswer;
use App\Models\CourseTestMatching;
use App\Models\CourseStepVocabulary;
use App\Models\CourseStepLink;
use App\Models\CourseStepExam;
use App\Models\CourseSlide;
use App\Models\RoadmapCourse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CourseGenerationService
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function generateRoadmap(string $topic, string $level, int $freetime, string $language = 'ru'): ?array
    {
        $prompt = "Создай учебный план.
Тема курса: '{$topic}'.
Уровень: '{$level}'.

Правила:
1. topic_course = {$topic}
2. Ровно 8 шагов.
3. Каждый шаг: topic (название), experience (5-30).

Ответ строго в JSON без комментариев:
{\"topic_course\":\"\",\"skills\":[\"навык1\",\"навык2\",\"навык3\",\"навык4\",\"навык5\"],\"map\":[{\"topic\":\"Тема 1\",\"type\":\"parent\",\"heirs\":[1,2],\"experience\":10},{\"topic\":\"Тема 2\",\"type\":\"heir\",\"experience\":10},{\"topic\":\"Тема 3\",\"type\":\"heir\",\"experience\":10},{\"topic\":\"Тема 4\",\"type\":\"parent\",\"heirs\":[4,5],\"experience\":15},{\"topic\":\"Тема 5\",\"type\":\"heir\",\"experience\":15},{\"topic\":\"Тема 6\",\"type\":\"heir\",\"experience\":15},{\"topic\":\"Тема 7\",\"type\":\"parent\",\"heirs\":[7],\"experience\":20},{\"topic\":\"Тема 8\",\"type\":\"heir\",\"experience\":20}]}";

        $contents = [
            ['role' => 'user', 'parts' => [['text' => $prompt]]],
        ];

        $response = $this->gemini->callApi($contents, [
            'temperature' => 0.7,
            'maxOutputTokens' => 4096,
        ]);

        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$text) {
            Log::error('Gemini roadmap generation failed: no text in response');
            return null;
        }

        $clean = str_replace(['```json', '```'], '', $text);
        $clean = preg_replace('/[\x00-\x1F\x7F]/s', ' ', $clean);
        $clean = preg_replace('/\s+/', ' ', $clean);
        $decoded = json_decode(trim($clean), true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($decoded['map'])) {
            $clean = preg_replace('/,\s*}/', '}', $clean);
            $clean = preg_replace('/,\s*]/', ']', $clean);
            $decoded = json_decode(trim($clean), true);
        }

        if (json_last_error() !== JSON_ERROR_NONE || !isset($decoded['map'])) {
            Log::error('Gemini roadmap JSON parse failed', [
                'error' => json_last_error_msg(),
                'last_chars' => mb_substr($clean, -200),
                'has_map' => isset($decoded['map']),
            ]);
            return null;
        }

        return $decoded;
    }

    public function generateStepDescription(Course $course, CourseStep $step): ?array
    {
        $prompt = "Создай подробное HTML-описание на тему курса \"{$course->topic}\" в шаге \"{$step->title}\". Это должно быть похоже на мини-лекцию.

### Требования:
1. Используй HTML-разметку: заголовки (h2, h3), параграфы (p), списки (ul, ol), примеры кода (pre > code с классом языка, например class=\"language-python\"), цитаты (blockquote), выделения (strong, em), таблицы (table) там где уместно сравнение.
2. Структура: начни с вводного абзаца (что изучим и зачем), затем разделы h2 с подзаголовками h3, заверши разделом \"Ключевые выводы\" (ul из 3-5 пунктов).
3. Важные советы оформляй как <blockquote class=\"tip\">, предупреждения — <blockquote class=\"warning\">.
4. НЕ используй инлайн-стили (style=...), только семантические теги.
5. Контент только внутри тега body, но сам тег body не пиши.
6. Тема должна быть раскрыта глубоко, минимум 400 слов.
7. Добавь не менее 5 внешних ссылок.
8. Ответ строго в JSON.

### Формат ответа:
{
    \"description\": \"<h2>Заголовок</h2><p>Описание...</p>\",
    \"links\": [\"https://example.com/1\", \"https://example.com/2\", \"https://example.com/3\", \"https://example.com/4\", \"https://example.com/5\"]
}";

        $contents = [
            ['role' => 'user', 'parts' => [['text' => $prompt]]],
        ];

        $response = $this->gemini->callApi($contents, [
            'temperature' => 0.7,
            'maxOutputTokens' => 4096,
        ]);

        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$text) return null;

        $clean = str_replace(['```json', '```'], '', $text);
        $decoded = json_decode(trim($clean), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $clean = preg_replace('/,\s*}/', '}', $clean);
            $clean = preg_replace('/,\s*]/', ']', $clean);
            $decoded = json_decode(trim($clean), true);
        }

        return $decoded;
    }

    public function generateTests(Course $course, CourseStep $step, $skills): ?array
    {
        // Унификация: в БД есть skill_name (старое) и skill (новое nullable).
        // Пишем всегда в оба поля, читаем с fallback.
        $skillNames = collect(is_string($skills) ? json_decode($skills, true) ?? [] : $skills)
            ->map(fn($s) => is_array($s) ? ($s['skill'] ?? $s['skill_name'] ?? null) : ($s->skill ?? $s->skill_name ?? null))
            ->filter()
            ->values()
            ->all();
        if (empty($skillNames)) {
            $skillNames = $course->courseSkills()
                ->get()
                ->map(fn($s) => $s->skill ?? $s->skill_name)
                ->filter()
                ->values()
                ->all();
        }
        $skillsList = json_encode($skillNames, JSON_UNESCAPED_UNICODE);
        $experience = $course->total_experience;

        $prompt = "Создай ровно 10 тестов по теме '{$course->topic}' для шага '{$step->title}'.

### Требования к тестам:
1. **2 теста с одним правильным ответом** (one_correct)
   - Вопрос, 4 варианта ответа, 1 правильный

2. **2 теста с несколькими правильными ответами** (list_correct)
   - Вопрос, 4 варианта ответа, 2+ правильных

3. **2 вопроса с открытым ответом** (question_answer)
   - Вопрос, текстовый ответ до 10 символов

4. **2 теста с верно/неверно** (true_false)
   - Вопрос, 1=верно, 0=неверно

5. **2 теста на соответствие** (matching)
   - Вопрос, две колонки (list1 и list2) в правильном порядке

### Условия:
- Укажи навык для каждого теста из списка: {$skillsList}
- Присвой баллы от 5 до 30

### Формат ответа (JSON):
[
  {
    \"one_correct\": {
      \"text\": \"Текст вопроса\",
      \"variants\": [\"Вариант 1\", \"Вариант 2\", \"Вариант 3\", \"Вариант 4\"],
      \"correct\": 1,
      \"score\": 10,
      \"skill_name\": \"навык\"
    }
  },
  {
    \"list_correct\": {
      \"text\": \"Текст вопроса\",
      \"variants\": [\"Вариант 1\", \"Вариант 2\", \"Вариант 3\", \"Вариант 4\"],
      \"correct\": [0, 2],
      \"score\": 15,
      \"skill_name\": \"навык\"
    }
  },
  {
    \"question_answer\": {
      \"text\": \"Текст вопроса\",
      \"correct\": \"Ответ\",
      \"score\": 20,
      \"skill_name\": \"навык\"
    }
  },
  {
    \"true_false\": {
      \"text\": \"Текст вопроса\",
      \"correct\": \"1\",
      \"score\": 10,
      \"skill_name\": \"навык\"
    }
  },
  {
    \"matching\": {
      \"text\": \"Текст вопроса\",
      \"list1\": [\"Элемент 1\", \"Элемент 2\", \"Элемент 3\"],
      \"list2\": [\"Соответствие 1\", \"Соответствие 2\", \"Соответствие 3\"],
      \"score\": 25,
      \"skill_name\": \"навык\"
    }
  }
]";

        $contents = [
            ['role' => 'user', 'parts' => [['text' => $prompt]]],
        ];

        $response = $this->gemini->callApi($contents, [
            'temperature' => 0.8,
            'maxOutputTokens' => 8192,
        ]);

        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$text) return null;

        $clean = str_replace(['```json', '```'], '', $text);
        $decoded = json_decode(trim($clean), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $clean = preg_replace('/,\s*}/', '}', $clean);
            $clean = preg_replace('/,\s*]/', ']', $clean);
            $decoded = json_decode(trim($clean), true);
        }

        return is_array($decoded) ? $decoded : null;
    }

    public function generateVocabulary(Course $course, CourseStep $step): ?array
    {
        $prompt = "Я изучаю '{$course->topic}' в шаге '{$step->title}'. Раздели шаг на подшаги с подробной информацией.

### Требования:
1. Заголовок для каждого подшага.
2. Подробная информация с примерами в формате HTML (только содержимое body): начинай каждый блок с 1-2 предложений сути, затем детали (p, ul/ol), пример кода в <pre><code class=\"language-python\">, заверши мини-выводом.
3. Важные нюансы — <blockquote class=\"tip\">. БЕЗ инлайн-стилей.
4. Количество подшагов от 3 до 8, от простого к сложному.
5. Ссылки для изучения.
6. Оценка сложности (exp) от 1 до 10.

### Формат ответа (JSON):
[
    {
        \"title\": \"Название подшага\",
        \"exp\": 6,
        \"info\": \"<p>HTML-контент с объяснениями и примерами, минимум 300 слов</p>\",
        \"links\": [\"https://example.com/1\", \"https://example.com/2\"]
    }
]";

        $contents = [
            ['role' => 'user', 'parts' => [['text' => $prompt]]],
        ];

        $response = $this->gemini->callApi($contents, [
            'temperature' => 0.7,
            'maxOutputTokens' => 16384,
        ]);

        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$text) return null;

        $clean = str_replace(['```json', '```'], '', $text);
        $decoded = json_decode(trim($clean), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $clean = preg_replace('/,\s*}/', '}', $clean);
            $clean = preg_replace('/,\s*]/', ']', $clean);
            $decoded = json_decode(trim($clean), true);
        }

        return is_array($decoded) ? $decoded : null;
    }

    public function createCourseFromRoadmap(int $userId, array $mapData, string $level, int $freetime): Course
    {
        $userName = \App\Models\User::find($userId)?->name ?? 'AI Generator';
        $course = Course::create([
            'user_id' => $userId,
            'title' => $mapData['topic_course'],
            'topic' => $mapData['topic_course'],
            'instructor' => $userName,
            'type' => 'private',
            'ai_generated' => true,
            'course_level' => $level,
            'freetime' => $freetime,
            'total_steps' => 0,
            'total_experience' => 0,
            'category' => 'other',
            'level' => $level === 'beginner' ? 'Начальный' : ($level === 'intermediate' ? 'Средний' : 'Продвинутый'),
            'generation_status' => 'generating',
        ]);

        $this->populateCourseFromMapData($course, $mapData);

        return $course->fresh();
    }

    /**
     * Заполнение существующего курса навыками и шагами из mapData.
     * Используется GenerateCourseRoadmap, GenerateCourseStepsJob и createCourseFromRoadmap.
     * - пишет skill_name + skill для совместимости
     * - heirs хранит как array (cast в модели), без двойного json_encode
     * - parent_id строится по индексам heirs из ИИ, с fallback на "последний parent"
     */
    public function populateCourseFromMapData(Course $course, array $mapData): void
    {
        if ($course->courseSkills()->count() === 0) {
            foreach (($mapData['skills'] ?? []) as $skillName) {
                $skillName = trim((string) $skillName);
                if ($skillName === '') continue;
                CourseSkill::create([
                    'course_id' => $course->id,
                    'skill_name' => $skillName,
                    'skill' => $skillName,
                ]);
            }
        }

        if ($course->steps()->count() > 0) {
            return;
        }

        $map = array_values($mapData['map'] ?? []);
        $created = [];
        $totalExp = 0;

        // Проход 1: создать все шаги без parent, запомнить id по индексу.
        foreach ($map as $index => $stepData) {
            $exp = (int) ($stepData['experience'] ?? 10);
            $exp = max(5, min(30, $exp));
            $totalExp += $exp;

            $heirs = $stepData['heirs'] ?? null;
            if (is_string($heirs)) {
                $decodedHeirs = json_decode($heirs, true);
                $heirs = is_array($decodedHeirs) ? $decodedHeirs : null;
            }
            if (is_array($heirs)) {
                $heirs = array_values(array_filter($heirs, fn($v) => is_int($v) || ctype_digit((string) $v)));
                $heirs = array_map('intval', $heirs);
            } else {
                $heirs = null;
            }

            $created[$index] = CourseStep::create([
                'course_id' => $course->id,
                'parent_id' => null,
                'type' => ($stepData['type'] ?? 'parent') === 'heir' ? 'heir' : 'parent',
                'title' => $stepData['topic'] ?? ('Шаг ' . ($index + 1)),
                'experience' => $exp,
                'sort_order' => $index,
                'heirs' => $heirs,
            ]);
        }

        // Проход 2: расставить parent_id.
        // Если у parent есть heirs-индексы — цепляем их. Иначе heir цепляется к последнему parent (fallback).
        $lastParentId = null;
        foreach ($map as $index => $stepData) {
            $step = $created[$index];
            $type = $step->type;

            if ($type === 'parent') {
                $lastParentId = $step->id;
                $heirs = $step->heirs ?? null;
                if (is_array($heirs)) {
                    foreach ($heirs as $childIdx) {
                        if (isset($created[$childIdx]) && $created[$childIdx]->id !== $step->id) {
                            $created[$childIdx]->update(['parent_id' => $step->id]);
                        }
                    }
                }
            } elseif ($type === 'heir' && $step->parent_id === null && $lastParentId !== null) {
                $step->update(['parent_id' => $lastParentId]);
            }
        }

        $course->update([
            'total_steps' => count($created),
            'total_experience' => $totalExp,
        ]);
    }

    public function storeTests(CourseStep $step, array $testsData): void
    {
        $course = $step->course;
        $skills = $course->courseSkills;

        foreach ($testsData as $item) {
            $type = key($item);
            $test = $item[$type];

            $skillId = null;
            if (isset($test['skill_name'])) {
                $skill = $skills->first(fn($s) => ($s->skill ?? null) === $test['skill_name'] || ($s->skill_name ?? null) === $test['skill_name']);
                $skillId = $skill?->id;
            }

            $testModel = CourseStepTest::create([
                'course_id' => $step->course_id,
                'step_id' => $step->id,
                'skill_id' => $skillId,
                'type_test' => $type,
                'text' => $test['text'],
                'score' => $test['score'] ?? 10,
            ]);

            if (in_array($type, ['one_correct', 'list_correct'])) {
                foreach ($test['variants'] as $variant) {
                    CourseTestVariant::create([
                        'test_id' => $testModel->id,
                        'variant' => $variant,
                    ]);
                }
                if (is_array($test['correct'])) {
                    foreach ($test['correct'] as $idx) {
                        CourseTestAnswer::create([
                            'test_id' => $testModel->id,
                            'answer' => $test['variants'][$idx] ?? $idx,
                            'is_correct' => true,
                        ]);
                    }
                } else {
                    CourseTestAnswer::create([
                        'test_id' => $testModel->id,
                        'answer' => $test['variants'][$test['correct']] ?? $test['correct'],
                        'is_correct' => true,
                    ]);
                }
            }

            if ($type === 'question_answer') {
                CourseTestAnswer::create([
                    'test_id' => $testModel->id,
                    'answer' => $test['correct'],
                    'is_correct' => true,
                ]);
            }

            if ($type === 'true_false') {
                CourseTestAnswer::create([
                    'test_id' => $testModel->id,
                    'answer' => $test['correct'],
                    'is_correct' => true,
                ]);
            }

            if ($type === 'matching' && isset($test['list1'], $test['list2'])) {
                $count = min(count($test['list1']), count($test['list2']));
                for ($i = 0; $i < $count; $i++) {
                    CourseTestMatching::create([
                        'test_id' => $testModel->id,
                        'list1_item' => $test['list1'][$i],
                        'list2_item' => $test['list2'][$i],
                    ]);
                }
            }
        }
    }

    public function storeVocabulary(CourseStep $step, array $vocabularyData): void
    {
        foreach ($vocabularyData as $item) {
            $vocab = CourseStepVocabulary::create([
                'step_id' => $step->id,
                'course_id' => $step->course_id,
                'title' => $item['title'] ?? 'Без названия',
                'content' => $item['info'] ?? '',
                'experience' => $item['exp'] ?? 5,
            ]);

            $step->course->increment('total_experience', $item['exp'] ?? 5);

            if (!empty($item['links']) && is_array($item['links'])) {
                foreach ($item['links'] as $link) {
                    CourseStepLink::create([
                        'vocabulary_id' => $vocab->id,
                        'link' => $link,
                    ]);
                }
            }
        }
    }

    public function storeDescription(CourseStep $step, array $descriptionData): void
    {
        $step->update(['description' => $descriptionData['description'] ?? '']);

        if (!empty($descriptionData['links'])) {
            foreach ($descriptionData['links'] as $link) {
                CourseStepLink::create([
                    'step_id' => $step->id,
                    'link' => $link,
                ]);
            }
        }
    }

    public function generateExams(Course $course, CourseStep $step): ?array
    {
        $prompt = "Создай экзамен по шагу '{$step->title}' курса '{$course->topic}'.

### Требования:
1. 3 вопроса типа quiz (выбор одного ответа из 4)
2. 1 вопрос типа test (написать код или краткий ответ)
3. 1 вопрос типа practice (написать развернутый ответ/код)
4. Укажи правильный ответ и объяснение
5. Сложность: {$course->course_level}

### Формат ответа (JSON):
[
    {
        \"type\": \"quiz\",
        \"question\": \"Вопрос\",
        \"options\": [\"A\", \"B\", \"C\", \"D\"],
        \"correct_answer\": \"A\",
        \"explanation\": \"Пояснение\",
        \"difficulty\": \"easy\",
        \"score\": 10
    },
    {
        \"type\": \"test\",
        \"question\": \"Вопрос\",
        \"options\": null,
        \"correct_answer\": \"ответ\",
        \"explanation\": \"Пояснение\",
        \"difficulty\": \"medium\",
        \"score\": 15
    },
    {
        \"type\": \"practice\",
        \"question\": \"Напишите код для...\",
        \"options\": null,
        \"correct_answer\": \"код\",
        \"explanation\": \"Пояснение\",
        \"difficulty\": \"hard\",
        \"score\": 25
    }
]";

        $contents = [
            ['role' => 'user', 'parts' => [['text' => $prompt]]],
        ];

        $response = $this->gemini->callApi($contents, [
            'temperature' => 0.8,
            'maxOutputTokens' => 4096,
        ]);

        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$text) return null;

        $clean = str_replace(['```json', '```'], '', $text);
        $decoded = json_decode(trim($clean), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $clean = preg_replace('/,\s*}/', '}', $clean);
            $clean = preg_replace('/,\s*]/', ']', $clean);
            $decoded = json_decode(trim($clean), true);
        }

        return is_array($decoded) ? $decoded : null;
    }

    public function storeExams(CourseStep $step, array $examsData): void
    {
        foreach ($examsData as $exam) {
            CourseStepExam::create([
                'course_id' => $step->course_id,
                'step_id' => $step->id,
                'type' => $exam['type'] ?? 'quiz',
                'question' => $exam['question'],
                'options' => $exam['options'] ?? null,
                'correct_answer' => $exam['correct_answer'],
                'explanation' => $exam['explanation'] ?? null,
                'difficulty' => $exam['difficulty'] ?? 'medium',
                'score' => $exam['score'] ?? 10,
            ]);
        }
    }

    public function generateSlides(Course $course, CourseStep $step): ?array
    {
        $prompt = "Создай слайды-презентацию по шагу '{$step->title}' курса '{$course->topic}'.

### Требования:
1. От 5 до 10 слайдов
2. Каждый слайд: заголовок + HTML-контент (ключевые моменты, примеры кода в pre > code с классом языка)
3. Контент для чтения на слайде (лаконичный, наглядный): 3-5 буллетов или короткий пример, БЕЗ инлайн-стилей
4. Используй HTML: h3, p, ul, li, pre, code, strong

### Формат ответа (JSON):
[
    {
        \"title\": \"Название слайда\",
        \"content\": \"<h3>Заголовок</h3><p>Текст</p><pre><code>пример</code></pre>\"
    }
]";

        $contents = [
            ['role' => 'user', 'parts' => [['text' => $prompt]]],
        ];

        $response = $this->gemini->callApi($contents, [
            'temperature' => 0.7,
            'maxOutputTokens' => 4096,
        ]);

        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$text) return null;

        $clean = str_replace(['```json', '```'], '', $text);
        $decoded = json_decode(trim($clean), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $clean = preg_replace('/,\s*}/', '}', $clean);
            $clean = preg_replace('/,\s*]/', ']', $clean);
            $decoded = json_decode(trim($clean), true);
        }

        return is_array($decoded) ? $decoded : null;
    }

    public function storeSlides(CourseStep $step, array $slidesData): void
    {
        foreach ($slidesData as $index => $slide) {
            CourseSlide::create([
                'course_id' => $step->course_id,
                'step_id' => $step->id,
                'title' => $slide['title'] ?? 'Слайд ' . ($index + 1),
                'content' => $slide['content'] ?? '',
                'sort_order' => $index,
            ]);
        }
    }

    public function generateRoadmapForRoadmap(string $title, string $category, string $difficulty): ?array
    {
        $prompt = "Создай дорожную карту для изучения '{$title}'.

### Требования:
1. От 4 до 8 секций (тематических блоков)
2. Каждая секция содержит от 2 до 4 курсов
3. Каждый курс: тема, уровень, количество шагов (10-25), навыки
4. Уровни курсов: beginner, intermediate, advanced

### Формат ответа (JSON):
{
    \"title\": \"{$title}\",
    \"description\": \"Краткое описание дорожной карты\",
    \"sections\": [
        {
            \"title\": \"Название секции\",
            \"description\": \"Описание секции\",
            \"courses\": [
                {
                    \"topic\": \"Тема курса\",
                    \"level\": \"beginner\",
                    \"steps_count\": 15,
                    \"skills\": [\"Навык1\", \"Навык2\"]
                }
            ]
        }
    ]
}";

        $contents = [
            ['role' => 'user', 'parts' => [['text' => $prompt]]],
        ];

        $response = $this->gemini->callApi($contents, [
            'temperature' => 0.7,
            'maxOutputTokens' => 4096,
        ]);

        \Log::info('Gemini roadmap response', ['response' => is_array($response) ? array_keys($response) : $response]);

        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$text) {
            \Log::error('Gemini roadmap: no text in response', ['response' => $response]);
            return null;
        }

        $clean = str_replace(['```json', '```'], '', $text);
        $decoded = json_decode(trim($clean), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $clean = preg_replace('/,\s*}/', '}', $clean);
            $clean = preg_replace('/,\s*]/', ']', $clean);
            $decoded = json_decode(trim($clean), true);
        }

        if (!is_array($decoded) || !isset($decoded['sections'])) {
            \Log::error('Gemini roadmap: invalid JSON', ['text' => mb_substr($text, 0, 500), 'error' => json_last_error_msg()]);
            return null;
        }

        return $decoded;
    }

    public function createCourseForRoadmap(int $userId, array $courseData, int $roadmapId, int $sectionId, int $sortOrder): Course
    {
        $userName = \App\Models\User::find($userId)?->name ?? 'AI Generator';
        $course = Course::create([
            'user_id' => $userId,
            'title' => $courseData['topic'],
            'topic' => $courseData['topic'],
            'instructor' => $userName,
            'type' => 'private',
            'ai_generated' => true,
            'course_level' => $courseData['level'] ?? 'beginner',
            'total_steps' => $courseData['steps_count'] ?? 15,
            'category' => 'other',
            'level' => ($courseData['level'] ?? 'beginner') === 'beginner' ? 'Начальный' : (($courseData['level'] ?? 'beginner') === 'intermediate' ? 'Средний' : 'Продвинутый'),
            'generation_status' => 'pending',
        ]);

        foreach (($courseData['skills'] ?? []) as $skillName) {
            $skillName = trim((string) $skillName);
            if ($skillName === '') continue;
            CourseSkill::create([
                'course_id' => $course->id,
                'skill_name' => $skillName,
                'skill' => $skillName,
            ]);
        }

        RoadmapCourse::create([
            'roadmap_id' => $roadmapId,
            'section_id' => $sectionId,
            'course_id' => $course->id,
            'sort_order' => $sortOrder,
        ]);

        return $course;
    }
}
