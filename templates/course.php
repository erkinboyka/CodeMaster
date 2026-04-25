<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$course = $course ?? [];
$lessons = $lessons ?? [];
$courseSkills = $courseSkills ?? [];
$courseExam = $courseExam ?? null;
$courseProgress = (int) ($progress['progress'] ?? 0);
$lessonsCount = count($lessons);
$courseLocale = currentLang();
$courseI18n = [
    'ru' => [
        'fallback_title' => 'Курс',
        'back_to_courses' => 'Назад к курсам',
        'lessons_label' => 'Уроки',
        'progress' => 'Прогресс',
        'skills' => 'Навыки',
        'program' => 'Программа',
        'no_lessons' => 'Пока нет уроков для этого курса.',
        'video_missing' => 'Видео не задано',
        'video_unavailable' => 'Видео недоступно',
        'video_open_link' => 'Открыть видео по ссылке',
        'video_embed_blocked' => 'Встроенный плеер недоступен. Откройте видео по ссылке.',
        'lesson_done' => 'Урок пройден',
        'prev' => 'Предыдущий',
        'next' => 'Следующий',
        'final_exam' => 'Финальный экзамен',
        'exam_after_lessons' => 'Экзамен доступен после завершения всех уроков.',
        'start_exam' => 'Начать экзамен',
        'exam_subtitle' => 'Ответьте на вопросы, чтобы получить сертификат.',
        'cert_profile' => 'Перейти в профиль',
        'cert_course' => 'Курс:',
        'cert_signature' => 'Подпись',
        'lesson_word' => 'Урок',
        'no_description' => 'Описание отсутствует',
        'materials' => 'Полезные материалы:',
        'link' => 'ссылка',
        'quiz_no_questions' => 'Нет вопросов для квиза.',
        'question' => 'Вопрос',
        'quiz_check' => 'Проверить ответы',
        'quiz_passed' => 'Квиз пройден. Урок отмечен как пройденный.',
        'quiz_failed' => 'Нужно правильно ответить на все вопросы. Верно: {correct}/{total}',
        'practice_title' => 'Практическое задание',
        'practice_run' => 'Проверить',
        'practice_checking' => 'Проверяем...',
        'practice_passed' => 'Задание пройдено. Урок можно завершить.',
        'practice_failed' => 'Введите решение и попробуйте снова.',
        'practice_placeholder' => 'Введите код решения...',
        'practice_fill_title' => 'Заполните пропуски',
        'practice_fill_blank' => 'Пропуск {num}',
        'practice_fill_code' => 'Фрагмент кода',
        'practice_fill_required' => 'Заполните все пропуски перед проверкой.',
        'practice_format_hint' => 'Формат: допишите код в поле. Сохраняйте синтаксис и не добавляйте лишний текст.',
        'practice_fill_hint' => 'Формат: выберите вариант для каждого пропуска.',
        'practice_fill_select' => 'Выберите вариант',
        'exam_no_created' => 'Экзамен пока не создан.',
        'exam_for_cert' => 'Пройдите экзамен для получения сертификата.',
        'exam_no_questions' => 'Нет вопросов для экзамена.',
        'exam_check' => 'Проверить экзамен',
        'exam_passed' => 'Экзамен пройден: {percent}%. Сертификат выдан.',
        'exam_failed' => 'Экзамен не пройден: {percent}%. Нужно минимум {pass}%',
        'exam_true' => 'Верно',
        'exam_false' => 'Неверно',
        'exam_answer_placeholder' => 'Введите ответ...',
        'exam_fill_all' => 'Ответьте на все вопросы перед проверкой.',
        'exam_short_hint' => 'Короткий ответ: без кавычек, с точным регистром и без лишних пробелов.',
        'error' => 'Ошибка',
        'connection_error' => 'Ошибка соединения',
        'lesson_marked' => 'Урок отмечен как пройденный',
        'watch_video_to_end' => 'Досмотрите видео до конца, чтобы перейти дальше',
        'exam_time_up' => 'Время экзамена истекло',
        'time' => 'Время',
        'infinity' => '?',
        'happy_title' => 'Поздравляем!',
        'happy_subtitle' => 'Сертификат готов и добавлен в профиль.',
    ],
    'en' => [
        'fallback_title' => 'Course',
        'back_to_courses' => 'Back to courses',
        'lessons_label' => 'Lessons',
        'progress' => 'Progress',
        'skills' => 'Skills',
        'program' => 'Program',
        'no_lessons' => 'No lessons for this course yet.',
        'video_missing' => 'No video set',
        'video_unavailable' => 'Video unavailable',
        'video_open_link' => 'Open video link',
        'video_embed_blocked' => 'Embedded player is unavailable. Open the video via link.',
        'lesson_done' => 'Lesson completed',
        'prev' => 'Previous',
        'next' => 'Next',
        'final_exam' => 'Final exam',
        'exam_after_lessons' => 'The exam is available after all lessons are completed.',
        'start_exam' => 'Start exam',
        'exam_subtitle' => 'Answer the questions to get a certificate.',
        'cert_profile' => 'Go to profile',
        'cert_course' => 'Course:',
        'cert_signature' => 'Signature',
        'lesson_word' => 'Lesson',
        'no_description' => 'No description available',
        'materials' => 'Useful materials:',
        'link' => 'link',
        'quiz_no_questions' => 'No quiz questions.',
        'question' => 'Question',
        'quiz_check' => 'Check answers',
        'quiz_passed' => 'Quiz passed. Lesson marked as completed.',
        'quiz_failed' => 'You need to answer all questions correctly. Correct: {correct}/{total}',
        'practice_title' => 'Practice task',
        'practice_run' => 'Run tests',
        'practice_checking' => 'Checking...',
        'practice_passed' => 'Passed. You can complete the lesson.',
        'practice_failed' => 'Enter a solution and try again.',
        'practice_placeholder' => 'Write your solution here...',
        'practice_fill_title' => 'Fill in the blanks',
        'practice_fill_blank' => 'Blank {num}',
        'practice_fill_code' => 'Code snippet',
        'practice_fill_required' => 'Fill all blanks before checking.',
        'practice_format_hint' => 'Format: complete the code. Keep syntax and avoid extra text.',
        'practice_fill_hint' => 'Format: select one option for each blank.',
        'practice_fill_select' => 'Select option',
        'exam_no_created' => 'Exam has not been created yet.',
        'exam_for_cert' => 'Pass the exam to receive a certificate.',
        'exam_no_questions' => 'No exam questions.',
        'exam_check' => 'Check exam',
        'exam_passed' => 'Exam passed: {percent}%. Certificate issued.',
        'exam_failed' => 'Exam failed: {percent}%. Minimum required is {pass}%',
        'exam_true' => 'True',
        'exam_false' => 'False',
        'exam_answer_placeholder' => 'Enter your answer...',
        'exam_fill_all' => 'Answer all questions before checking.',
        'exam_short_hint' => 'Short answer: no quotes, exact case, no extra spaces.',
        'error' => 'Error',
        'connection_error' => 'Connection error',
        'lesson_marked' => 'Lesson marked as completed',
        'watch_video_to_end' => 'Watch the video to the end to continue',
        'exam_time_up' => 'Exam time is up',
        'time' => 'Time',
        'infinity' => '?',
        'happy_title' => 'Congrats!',
        'happy_subtitle' => 'Your certificate is ready in your profile.',
    ],
    'tg' => [
        'fallback_title' => 'Курс',
        'back_to_courses' => 'Ба курсҳо бозгашт',
        'lessons_label' => 'Дарсҳо',
        'progress' => 'Пешрафт',
        'skills' => 'Малакаҳо',
        'program' => 'Барнома',
        'no_lessons' => 'Ҳоло барои ин курс дарс нест.',
        'video_missing' => 'Видео муайян нашудааст',
        'video_unavailable' => 'Видео дастнорас аст',
        'video_open_link' => 'Видеоро аз рӯи пайванд кушоед',
        'video_embed_blocked' => 'Плеери дарунсохт дастнорас аст. Видеоро аз рӯи пайванд кушоед.',
        'lesson_done' => 'Дарс анҷом ёфт',
        'prev' => 'Қаблӣ',
        'next' => 'Баъдӣ',
        'final_exam' => 'Имтиҳони ниҳоӣ',
        'exam_after_lessons' => 'Имтиҳон баъд аз анҷоми ҳамаи дарсҳо дастрас мешавад.',
        'start_exam' => 'Оғози имтиҳон',
        'exam_subtitle' => 'Ба саволҳо ҷавоб диҳед, то сертификат гиред.',
        'cert_profile' => 'Ба профил гузаред',
        'cert_course' => 'Курс:',
        'cert_signature' => 'Имзо',
        'lesson_word' => 'Дарс',
        'no_description' => 'Тавсиф мавҷуд нест',
        'materials' => 'Маводи муфид:',
        'link' => 'пайванд',
        'quiz_no_questions' => 'Барои квиз савол нест.',
        'question' => 'Савол',
        'quiz_check' => 'Санҷиши ҷавобҳо',
        'quiz_passed' => 'Квиз гузашт. Дарс ҳамчун анҷомшуда қайд шуд.',
        'quiz_failed' => 'Барои гузаштан ба ҳамаи саволҳо дуруст ҷавоб диҳед. Дуруст: {correct}/{total}',
        'practice_title' => 'Вазифаи амалӣ',
        'practice_run' => 'Санҷиш',
        'practice_checking' => 'Санҷида истодаем...',
        'practice_passed' => 'Вазифа гузашт. Метавонед дарсро анҷом диҳед.',
        'practice_failed' => 'Ҳалро ворид кунед ва дубора кӯшиш кунед.',
        'practice_placeholder' => 'Кодро ин ҷо нависед...',
        'practice_fill_title' => 'Ҷойҳои холиро пур кунед',
        'practice_fill_blank' => 'Ҷои холӣ {num}',
        'practice_fill_code' => 'Порчаи код',
        'practice_fill_required' => 'Пеш аз санҷиш ҳамаи ҷойҳои холиро пур кунед.',
        'practice_format_hint' => 'Формат: кодро пур кунед. Синтаксисро нигоҳ доред ва матни иловагӣ нанависед.',
        'practice_fill_hint' => 'Формат: барои ҳар ҷои холӣ як вариант интихоб кунед.',
        'practice_fill_select' => 'Вариантро интихоб кунед',
        'exam_no_created' => 'Имтиҳон ҳоло сохта нашудааст.',
        'exam_for_cert' => 'Барои гирифтани сертификат имтиҳонро гузаред.',
        'exam_no_questions' => 'Барои имтиҳон савол нест.',
        'exam_check' => 'Санҷиши имтиҳон',
        'exam_passed' => 'Имтиҳон гузашт: {percent}%. Сертификат дода шуд.',
        'exam_failed' => 'Имтиҳон нагузашт: {percent}%. Ҳадди ақал {pass}% лозим аст',
        'exam_true' => 'Дуруст',
        'exam_false' => 'Нодуруст',
        'exam_answer_placeholder' => 'Ҷавобро ворид кунед...',
        'exam_fill_all' => 'Пеш аз санҷиш ба ҳамаи саволҳо ҷавоб диҳед.',
        'exam_short_hint' => 'Ҷавоби кӯтоҳ: бе нохунак, бо регистри дуруст ва бе фосилаҳои зиёдатӣ.',
        'error' => 'Хато',
        'connection_error' => 'Хатои пайвастшавӣ',
        'lesson_marked' => 'Дарс ҳамчун анҷомшуда қайд шуд',
        'watch_video_to_end' => 'Видеоро то охир бинед, то ба дарси баъдӣ гузаред',
        'exam_time_up' => 'Вақти имтиҳон тамом шуд',
        'time' => 'Вақт',
        'infinity' => '?',
        'happy_title' => 'Табрик!',
        'happy_subtitle' => 'Сертификат омода шуд ва ба профил илова гардид.',
    ],
];
$ci = $courseI18n[$courseLocale] ?? $courseI18n['ru'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course['title'] ?? $ci['fallback_title']) ?> - CodeMaster</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.0/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Inter", sans-serif;
            background-color: #f9fafb;
            color: #111827;
            margin: 0;
        }

        .video-shell {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 16px;
            background: #0f172a;
        }

        .video-shell video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .video-shell iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .lesson-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .lesson-scroll::-webkit-scrollbar-thumb {
            background: #c7d2fe;
            border-radius: 9999px;
        }

        .course-cert {
            background: radial-gradient(circle at top left, #fff7ed, #fef3c7 40%, #fff1f2 100%);
            border: 8px double #111827;
            color: #111827;
        }

        .course-cert-title {
            font-family: "Georgia", serif;
            letter-spacing: 0.08em;
        }

        .exam-question {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            color: #111827;
        }

        .exam-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .exam-option:hover {
            border-color: #6366f1;
            background: #eef2ff;
        }

        .exam-option input {
            accent-color: #4f46e5;
        }

        .exam-option.selected {
            border-color: #4f46e5;
            background: #eef2ff;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.15);
            color: #111827;
        }

        .tf-happy-end {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top, rgba(255, 255, 255, 0.95), rgba(249, 250, 251, 0.85));
            z-index: 99999;
            animation: tfHappyFade 1.8s ease forwards;
        }

        .tf-happy-card {
            background: white;
            border-radius: 24px;
            padding: 28px 32px;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.2);
            text-align: center;
        }

        .tf-happy-title {
            font-size: 28px;
            font-weight: 800;
            color: #111827;
        }

        .tf-happy-subtitle {
            margin-top: 6px;
            font-size: 14px;
            color: #475569;
        }

        @keyframes tfHappyFade {
            0% { opacity: 0; transform: scale(0.96); }
            12% { opacity: 1; transform: scale(1); }
            75% { opacity: 1; }
            100% { opacity: 0; transform: scale(0.98); }
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">
    <?php include 'includes/header.php'; ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <a href="?action=courses" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-1"></i> <?= htmlspecialchars($ci['back_to_courses']) ?>
            </a>
            <div class="text-sm text-gray-500"><?= htmlspecialchars($ci['lessons_label']) ?>:
                <?= uiValue($lessonsCount) ?></div>
        </div>

        <section class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($course['title'] ?? '') ?></h1>
                    <p class="text-sm text-indigo-600 mt-1"><?= htmlspecialchars($course['instructor'] ?? '') ?></p>
                    <p class="text-gray-600 mt-3"><?= htmlspecialchars($course['description'] ?? '') ?></p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        <?php if (!empty($course['category'])): ?>
                            <span
                                class="px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700"><?= htmlspecialchars($course['category']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($course['level'])): ?>
                            <span
                                class="px-3 py-1 rounded-full text-xs bg-emerald-100 text-emerald-700"><?= htmlspecialchars($course['level']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="w-full lg:w-72">
                    <div class="text-sm text-gray-500 mb-2"><?= htmlspecialchars($ci['progress']) ?></div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-2 bg-indigo-600" style="width: <?= $courseProgress ?>%"></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-2"><?= $courseProgress ?>%</div>
                </div>
            </div>

            <?php if (!empty($courseSkills)): ?>
                <div class="mt-6">
                    <h2 class="text-sm font-semibold text-gray-700 mb-2"><?= htmlspecialchars($ci['skills']) ?></h2>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($courseSkills as $skill): ?>
                            <span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                <?= htmlspecialchars($skill['skill_name']) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <section class="mt-6 grid grid-cols-1 xl:grid-cols-[320px_1fr] gap-6">
            <aside class="bg-white rounded-2xl border border-gray-200 p-5 h-fit xl:sticky xl:top-24">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">
                        <?= htmlspecialchars($ci['program']) ?></h2>
                    <span class="text-xs text-gray-400"><?= uiValue($lessonsCount) ?></span>
                </div>
                <div id="lessons-list"
                    class="lesson-scroll mt-4 space-y-2 pr-0 max-h-none overflow-visible xl:max-h-[60vh] xl:overflow-y-auto xl:pr-1"></div>
            </aside>

            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <div id="lesson-empty" class="text-gray-500 hidden"><?= htmlspecialchars($ci['no_lessons']) ?></div>
                <div id="lesson-content">
                    <div class="video-shell border border-gray-200">
                        <video id="lesson-video" controls playsinline preload="metadata">
                            <source id="lesson-video-src" src="" type="video/mp4">
                        </video>
                        <iframe id="lesson-iframe" class="hidden"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen title="Video player"></iframe>
                        <div id="lesson-video-empty"
                            class="absolute inset-0 flex items-center justify-center text-sm text-gray-300 hidden">
                            <?= htmlspecialchars($ci['video_missing']) ?>
                        </div>
                    </div>
                    <div class="mt-6">
                        <h2 id="lesson-title" class="text-2xl font-bold text-gray-900"></h2>
                        <div id="lesson-meta" class="text-sm text-gray-500 mt-1"></div>
                        <div id="lesson-text" class="text-gray-700 mt-4 leading-relaxed whitespace-pre-line"></div>
                        <div id="lesson-quiz" class="mt-6 hidden"></div>
                        <div id="lesson-practice" class="mt-6 hidden"></div>
                    </div>
                    <div class="mt-6 flex flex-col gap-3">
                        <span id="lesson-status" class="text-sm text-emerald-600 hidden">
                            <i class="fas fa-check-circle mr-1"></i> <?= htmlspecialchars($ci['lesson_done']) ?>
                        </span>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <button id="prev-lesson-btn"
                                class="px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-arrow-left mr-2"></i> <?= htmlspecialchars($ci['prev']) ?>
                            </button>
                            <button id="next-lesson-btn"
                                class="px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-50">
                                <?= htmlspecialchars($ci['next']) ?> <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="final-exam-section" class="mt-8 bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($ci['final_exam']) ?></h2>
                    <p class="text-sm text-gray-600 mt-1" id="exam-hint">
                        <?= htmlspecialchars($ci['exam_after_lessons']) ?></p>
                </div>
                <button id="start-exam-btn"
                    class="px-5 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">
                    <?= htmlspecialchars($ci['start_exam']) ?>
                </button>
            </div>
        </section>
    </main>

    <div id="exam-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeExamModal()"></div>
        <div
            class="relative max-w-4xl mx-auto mt-10 rounded-2xl shadow-2xl p-8 border border-slate-200 bg-white max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900"><?= htmlspecialchars($ci['final_exam']) ?></h2>
                    <p class="text-sm text-slate-500 mt-1"><?= htmlspecialchars($ci['exam_subtitle']) ?></p>
                </div>
                <button class="text-slate-400 hover:text-slate-700" onclick="closeExamModal()">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm text-slate-500">
                    <?= htmlspecialchars($ci['progress']) ?>: <span id="exam-progress-text">0%</span>
                </div>
                <div class="text-sm text-slate-500">
                    <?= htmlspecialchars($ci['time']) ?>: <span id="exam-timer">00:00</span>
                </div>
            </div>
            <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden mb-6">
                <div id="exam-progress-bar" class="h-full bg-gradient-to-r from-cyan-400 to-emerald-400"
                    style="width: 0%"></div>
            </div>
            <div id="exam-container" class="space-y-4"></div>
        </div>
    </div>

    <div id="course-cert-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-6 bg-black/90">
        <div class="course-cert max-w-3xl w-full p-10 rounded-2xl shadow-2xl text-center">
            <div class="text-sm uppercase tracking-[0.35em] text-gray-700">CodeMaster</div>
            <h2 class="course-cert-title text-4xl font-bold mt-4"><?= t('cert_heading') ?></h2>
            <p class="mt-6 text-gray-700"><?= t('cert_confirm') ?></p>
            <p id="cert-user-name" class="text-2xl font-semibold mt-2"><?= htmlspecialchars($user['name'] ?? '') ?></p>
            <p class="mt-4 text-gray-700"><?= t('cert_completed') ?></p>
            <p class="mt-4 text-gray-700"><?= htmlspecialchars($ci['cert_course']) ?></p>
            <p id="cert-course-title" class="text-2xl font-bold mt-1 text-gray-900"></p>
            <div class="mt-8 flex items-center justify-between text-sm text-gray-700">
                <div>
                    <div class="font-semibold"><?= t('cert_date') ?></div>
                    <div id="cert-date"></div>
                </div>
                <div class="text-right">
                    <div class="font-semibold"><?= htmlspecialchars($ci['cert_signature']) ?></div>
                    <div>CodeMaster Academy</div>
                </div>
            </div>
            <button class="mt-8 px-4 py-2 rounded-lg border border-gray-700 text-gray-900"
                onclick="closeCourseCert()"><?= htmlspecialchars($ci['cert_profile']) ?></button>
        </div>
    </div>

    <script>
        const tfCourseI18n = <?= tfSafeJson($ci, JSON_UNESCAPED_UNICODE) ?>;
        const rawLessons = <?= tfSafeJson($lessons, JSON_UNESCAPED_UNICODE) ?>;
        const lessons = (() => {
            const seen = new Set();
            const unique = [];
            rawLessons.forEach((l) => {
                if (l && typeof l === 'object') {
                    const completedRaw = l.completed;
                    l.completed = completedRaw === true || completedRaw === 1 || completedRaw === '1' || completedRaw === 'true';
                    const practiceRequiredRaw = l.practice_required;
                    l.practice_required = practiceRequiredRaw === true || practiceRequiredRaw === 1 || practiceRequiredRaw === '1' || practiceRequiredRaw === 'true';
                    const practicePassedRaw = l.practice_passed;
                    l.practice_passed = practicePassedRaw === true || practicePassedRaw === 1 || practicePassedRaw === '1' || practicePassedRaw === 'true';
                    const testsRaw = l.practice_tests_json;
                    if (typeof testsRaw === 'string' && testsRaw !== '') {
                        try {
                            l.practice_tests = JSON.parse(testsRaw);
                        } catch (e) {
                            l.practice_tests = [];
                        }
                    } else {
                        l.practice_tests = [];
                    }
                }
                const id = l && l.id ? String(l.id) : null;
                if (id && !seen.has(id)) {
                    seen.add(id);
                    unique.push(l);
                } else if (!id) {
                    unique.push(l);
                }
            });
            return unique;
        })();
        let courseProgressValue = Number(<?= (int) $courseProgress ?>) || 0;
        const courseExam = <?= tfSafeJson($courseExam, JSON_UNESCAPED_UNICODE) ?>;
        let currentLessonIdx = 0;
        const videoWatched = {};
        let completeLessonRequestInFlight = false;

        const listEl = document.getElementById('lessons-list');
        const emptyEl = document.getElementById('lesson-empty');
        const contentEl = document.getElementById('lesson-content');
        const videoEl = document.getElementById('lesson-video');
        const videoSrcEl = document.getElementById('lesson-video-src');
        const iframeEl = document.getElementById('lesson-iframe');
        const videoEmptyEl = document.getElementById('lesson-video-empty');
        const titleEl = document.getElementById('lesson-title');
        const metaEl = document.getElementById('lesson-meta');
        const textEl = document.getElementById('lesson-text');
        const statusEl = document.getElementById('lesson-status');
        const prevLessonBtn = document.getElementById('prev-lesson-btn');
        const nextLessonBtn = document.getElementById('next-lesson-btn');
        const contentContainer = document.getElementById('lesson-content');
        const quizEl = document.getElementById('lesson-quiz');
        const practiceEl = document.getElementById('lesson-practice');
        const examSection = document.getElementById('final-exam-section');
        const examHint = document.getElementById('exam-hint');
        const startExamBtn = document.getElementById('start-exam-btn');
        const examContainer = document.getElementById('exam-container');
        const examModal = document.getElementById('exam-modal');
        const examProgressText = document.getElementById('exam-progress-text');
        const examProgressBar = document.getElementById('exam-progress-bar');
        const examTimerEl = document.getElementById('exam-timer');
        let examTimer = null;
        let examEndsAt = 0;
        let examQuestionCount = 0;
        let examRuntime = null;
        let ytPlayer = null;
        let ytApiReady = !!(window.YT && window.YT.Player);
        let ytApiRequested = false;
        let pendingYouTubeVideoId = '';
        let pendingYouTubeWatchUrl = '';
        let activeVideoSourceUrl = '';
        let activeExternalVideoUrl = '';

        function renderLessonList() {
            listEl.innerHTML = '';
            lessons.forEach((lesson, idx) => {
                const isActive = idx === currentLessonIdx;
                const isDone = !!lesson.completed;
                const isLocked = idx > 0 && !lessons[idx - 1].completed;
                const btn = document.createElement('button');
                btn.disabled = isLocked;
                btn.className = `w-full text-left p-4 rounded-xl border transition-all ${isActive ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-200 hover:bg-gray-50'} ${isLocked ? 'opacity-60 cursor-not-allowed' : ''}`;
                btn.innerHTML = `
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <div class="text-xs text-gray-400 uppercase">${tfCourseI18n.lesson_word} ${idx + 1}</div>
                            <div class="text-sm font-medium text-gray-900 truncate">${lesson.title || tfCourseI18n.lesson_word}</div>
                        </div>
                        ${isDone ? '<i class="fas fa-check-circle text-emerald-500"></i>' : (isLocked ? '<i class="fas fa-lock text-gray-300"></i>' : '')}
                    </div>
                `;
                btn.addEventListener('click', () => {
                    if (isLocked) return;
                    selectLesson(idx);
                });
                listEl.appendChild(btn);
            });
        }

        function isYouTubeHost(hostname) {
            const host = String(hostname || '').toLowerCase();
            return host === 'youtu.be'
                || host === 'youtube.com'
                || host.endsWith('.youtube.com')
                || host === 'youtube-nocookie.com'
                || host.endsWith('.youtube-nocookie.com');
        }

        function isYouTubePlaylistUrl(url) {
            if (!url) return false;
            try {
                const normalized = normalizeLessonVideoUrl(url);
                const parsed = new URL(normalized);
                if (!isYouTubeHost(parsed.hostname)) return false;
                const path = parsed.pathname.toLowerCase();
                const listType = (parsed.searchParams.get('listType') || '').toLowerCase();
                // Search-embed URLs are not playlists and should be handled by dedicated branch.
                if (listType === 'search') return false;
                if (path === '/results') return false;
                // Плейлисты: /playlist или URL с параметром list=
                if (path === '/playlist') return true;
                const listId = parsed.searchParams.get('list') || '';
                // Shared watch URLs may contain both v and list; treat as playlist only for valid-looking IDs.
                const hasVideoId = !!(parsed.searchParams.get('v') || getYouTubeVideoId(normalized));
                if (listId && !hasVideoId && /^[A-Za-z0-9_-]{10,}$/.test(listId)) return true;
                return false;
            } catch (e) {
                return false;
            }
        }

        function getYouTubePlaylistId(url) {
            if (!url) return '';
            try {
                const normalized = normalizeLessonVideoUrl(url);
                const parsed = new URL(normalized);
                const listType = (parsed.searchParams.get('listType') || '').toLowerCase();
                if (listType === 'search') return '';
                const list = parsed.searchParams.get('list');
                if (!list) return '';
                if (!/^[A-Za-z0-9_-]{10,}$/.test(list)) return '';
                return list;
            } catch (e) {
                return '';
            }
        }

        function isHttpUrl(url) {
            return /^https?:\/\//i.test(String(url || '').trim());
        }

        function resetVideoEmptyState() {
            if (!videoEmptyEl) return;
            videoEmptyEl.textContent = tfCourseI18n.video_missing || '';
        }

        function showVideoEmptyState(message, linkUrl = '') {
            if (!videoEmptyEl) return;
            videoEmptyEl.innerHTML = '';
            const messageEl = document.createElement('div');
            messageEl.className = 'text-center text-gray-300 px-4';
            messageEl.textContent = String(message || tfCourseI18n.video_missing || '');
            videoEmptyEl.appendChild(messageEl);

            if (linkUrl && isHttpUrl(linkUrl)) {
                const linkEl = document.createElement('a');
                linkEl.href = linkUrl;
                linkEl.target = '_blank';
                linkEl.rel = 'noopener';
                linkEl.className = 'mt-2 inline-block text-indigo-300 underline hover:text-indigo-200';
                linkEl.textContent = tfCourseI18n.video_open_link || 'Open video link';
                videoEmptyEl.appendChild(linkEl);
            }

            videoEmptyEl.classList.remove('hidden');
        }

        function getYouTubeVideoId(url) {
            if (!url) return '';
            const trimmed = normalizeLessonVideoUrl(url);
            try {
                const parsed = new URL(trimmed);
                const host = parsed.hostname.toLowerCase();
                if (host === 'youtu.be') {
                    const id = parsed.pathname.replace(/^\/+/, '').split('/')[0];
                    return id || '';
                }
                if (isYouTubeHost(host)) {
                    const v = parsed.searchParams.get('v');
                    if (v) return v;
                    const parts = parsed.pathname.split('/').filter(Boolean);
                    const embedIdx = parts.indexOf('embed');
                    if (embedIdx !== -1 && parts[embedIdx + 1]) return parts[embedIdx + 1];
                    const shortsIdx = parts.indexOf('shorts');
                    if (shortsIdx !== -1 && parts[shortsIdx + 1]) return parts[shortsIdx + 1];
                    const liveIdx = parts.indexOf('live');
                    if (liveIdx !== -1 && parts[liveIdx + 1]) return parts[liveIdx + 1];
                    const vPathIdx = parts.indexOf('v');
                    if (vPathIdx !== -1 && parts[vPathIdx + 1]) return parts[vPathIdx + 1];
                }
            } catch (e) {
                // fallback regex below
            }
            const shortMatch = trimmed.match(/^(?:https?:\/\/)?(?:www\.)?youtu\.be\/([A-Za-z0-9_-]{6,})/i);
            if (shortMatch) return shortMatch[1];
            const watchMatch = trimmed.match(/[?&]v=([A-Za-z0-9_-]{6,})/i);
            if (watchMatch) return watchMatch[1];
            const embedMatch = trimmed.match(/^(?:https?:\/\/)?(?:www\.)?(?:youtube(?:-nocookie)?\.com)\/(?:embed|shorts|live|v)\/([A-Za-z0-9_-]{6,})/i);
            if (embedMatch) return embedMatch[1];
            return '';
        }

        function buildYouTubeWatchUrl(videoId) {
            if (!videoId) return '';
            return `https://www.youtube.com/watch?v=${videoId}`;
        }

        function buildYouTubeEmbedUrl(videoId) {
            if (!videoId) return '';
            const params = new URLSearchParams({
                enablejsapi: '1',
                rel: '0',
                modestbranding: '1',
                playsinline: '1'
            });
            try {
                if (window.location && window.location.origin) {
                    params.set('origin', window.location.origin);
                }
            } catch (e) { }
            return `https://www.youtube-nocookie.com/embed/${videoId}?${params.toString()}`;
        }

        function isYouTubeEmbedSearchUrl(url) {
            if (!url) return false;
            try {
                const parsed = new URL(normalizeLessonVideoUrl(url));
                if (!isYouTubeHost(parsed.hostname)) return false;
                const path = parsed.pathname.toLowerCase();
                if (!path.startsWith('/embed')) return false;
                return parsed.searchParams.get('listType') === 'search' && parsed.searchParams.get('list');
            } catch (e) {
                return false;
            }
        }

        function isYouTubeResultsUrl(url) {
            if (!url) return false;
            try {
                const parsed = new URL(normalizeLessonVideoUrl(url));
                if (!isYouTubeHost(parsed.hostname)) return false;
                const path = parsed.pathname.toLowerCase();
                return path === '/results' && !!parsed.searchParams.get('search_query');
            } catch (e) {
                return false;
            }
        }

        function toYouTubeEmbedSearchUrl(url) {
            if (!url) return '';
            try {
                const parsed = new URL(normalizeLessonVideoUrl(url));
                const path = parsed.pathname.toLowerCase();
                if (!isYouTubeHost(parsed.hostname)) return '';

                if (path === '/results') {
                    const q = parsed.searchParams.get('search_query');
                    if (!q) return '';
                    return `https://www.youtube.com/embed?listType=search&list=${encodeURIComponent(q)}`;
                }

                if (path.startsWith('/embed') && parsed.searchParams.get('listType') === 'search') {
                    const list = parsed.searchParams.get('list');
                    if (!list) return '';
                    return `https://www.youtube.com/embed?listType=search&list=${encodeURIComponent(list)}`;
                }
            } catch (e) {
                return '';
            }
            return '';
        }

        function guessVideoMimeType(url) {
            const normalized = normalizeLessonVideoUrl(url);
            if (!normalized) return '';
            const lower = normalized.toLowerCase();
            if (lower.includes('.mp4')) return 'video/mp4';
            if (lower.includes('.webm')) return 'video/webm';
            if (lower.includes('.ogg') || lower.includes('.ogv')) return 'video/ogg';
            return '';
        }

        function isDirectMediaUrl(url) {
            const normalized = normalizeLessonVideoUrl(url);
            if (!normalized) return false;
            return /\.(mp4|webm|ogg)(\?|#|$)/i.test(normalized);
        }

        function isTrackableVideoUrl(url) {
            const normalized = normalizeLessonVideoUrl(url);
            if (!normalized) return false;
            if (getYouTubeVideoId(normalized)) return true;
            return isDirectMediaUrl(normalized);
        }

        function extractVideoUrl(raw) {
            const trimmed = String(raw || '').trim();
            if (!trimmed) return '';
            if (trimmed.startsWith('<')) {
                try {
                    const doc = new DOMParser().parseFromString(trimmed, 'text/html');
                    const iframe = doc.querySelector('iframe');
                    if (iframe && iframe.getAttribute('src')) return iframe.getAttribute('src');
                    const source = doc.querySelector('video source');
                    if (source && source.getAttribute('src')) return source.getAttribute('src');
                    const video = doc.querySelector('video');
                    if (video && video.getAttribute('src')) return video.getAttribute('src');
                } catch (e) {
                    return trimmed;
                }
            }
            return trimmed;
        }

        function normalizeLessonVideoUrl(url) {
            const trimmed = extractVideoUrl(url);
            if (!trimmed) return '';
            // Legacy demo links in DB point to example.com and are not playable.
            if (/^https?:\/\/example\.com\/videos\//i.test(trimmed)) {
                return 'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4';
            }
            return trimmed;
        }

        function lessonHasVideo(lesson) {
            if (!lesson) return false;
            return !!String(normalizeLessonVideoUrl(lesson.video_url || lesson.video || '')).trim();
        }

        function lessonRequiresVideoWatch(lesson) {
            if (!lesson || lesson.type === 'quiz') return false;
            const url = normalizeLessonVideoUrl(lesson.video_url || lesson.video || '');
            return isTrackableVideoUrl(url);
        }

        function compactLessonContent(content) {
            let text = String(content || '').trim();
            if (!text) return tfCourseI18n.no_description;
            text = text.replace(
                /-\s*Видео на русском \(YouTube\):\s*https?:\/\/\S+/gi,
                '- Видео на русском (YouTube): ссылка в блоке "Материалы"'
            );
            text = text.replace(
                /https?:\/\/www\.youtube\.com\/embed\?listType=search&list=\S+/gi,
                'YouTube (см. блок "Материалы")'
            );
            text = text.replace(/https?:\/\/\S{90,}/g, (url) => `${url.slice(0, 77)}...`);
            return text;
        }

        function onLessonVideoFinished() {
            const lesson = lessons[currentLessonIdx];
            if (!lesson || !lesson.id) return;
            videoWatched[lesson.id] = true;
            if (!lesson.completed) {
                completeLesson(lesson.id, false, { videoCompleted: true });
            } else {
                renderLesson();
            }
        }

        function ensureYouTubeApi() {
            if (ytApiReady || ytApiRequested) return;
            ytApiRequested = true;
            const script = document.createElement('script');
            script.src = 'https://www.youtube.com/iframe_api';
            document.head.appendChild(script);
        }

        function setupYouTubePlayer(videoId, sourceUrl = '') {
            if (!videoId || !iframeEl) return;
            pendingYouTubeVideoId = videoId;
            pendingYouTubeWatchUrl = buildYouTubeWatchUrl(videoId);
            activeExternalVideoUrl = sourceUrl || pendingYouTubeWatchUrl;
            const embedUrl = buildYouTubeEmbedUrl(videoId);
            iframeEl.src = embedUrl;
            iframeEl.classList.remove('hidden');
            videoEmptyEl.classList.add('hidden');

            if (!ytApiReady || !(window.YT && window.YT.Player)) {
                ensureYouTubeApi();
                return;
            }

            if (ytPlayer && typeof ytPlayer.loadVideoById === 'function') {
                ytPlayer.loadVideoById(videoId);
                return;
            }

            ytPlayer = new window.YT.Player('lesson-iframe', {
                videoId,
                playerVars: { rel: 0, modestbranding: 1, playsinline: 1 },
                events: {
                    onStateChange: (event) => {
                        if (event.data === window.YT.PlayerState.ENDED) {
                            onLessonVideoFinished();
                        }
                    },
                    onError: () => {
                        stopYouTubePlayback();
                        showVideoEmptyState(
                            tfCourseI18n.video_embed_blocked || tfCourseI18n.video_unavailable,
                            activeExternalVideoUrl || pendingYouTubeWatchUrl
                        );
                    }
                }
            });
        }

        function stopYouTubePlayback() {
            if (ytPlayer && typeof ytPlayer.stopVideo === 'function') {
                try { ytPlayer.stopVideo(); } catch (e) { }
            }
        }

        const prevYouTubeReady = window.onYouTubeIframeAPIReady;
        window.onYouTubeIframeAPIReady = function () {
            ytApiReady = true;
            if (typeof prevYouTubeReady === 'function') prevYouTubeReady();
            if (pendingYouTubeVideoId) setupYouTubePlayer(pendingYouTubeVideoId);
        };

        function setDirectVideoSource(url) {
            if (!videoEl || !videoSrcEl) return;
            try {
                videoEl.pause();
            } catch (e) { }
            videoEl.removeAttribute('src');
            videoSrcEl.removeAttribute('src');
            videoSrcEl.type = guessVideoMimeType(url);
            videoSrcEl.src = url;
            videoEl.load();
        }

        function renderLesson() {
            if (!lessons.length) {
                emptyEl.classList.remove('hidden');
                contentEl.classList.add('hidden');
                return;
            }

            emptyEl.classList.add('hidden');
            contentEl.classList.remove('hidden');

            const lesson = lessons[currentLessonIdx];
            titleEl.textContent = lesson.title || tfCourseI18n.lesson_word;

            const metaParts = [];
            if (lesson.type) metaParts.push(lesson.type);
            metaEl.textContent = metaParts.join(' • ');

            textEl.textContent = compactLessonContent(lesson.content);

            const existingMaterials = document.getElementById('lesson-materials');
            if (existingMaterials) existingMaterials.remove();
            if (lesson.materials_title || lesson.materials_url) {
                const materials = document.createElement('div');
                materials.id = 'lesson-materials';
                materials.className = 'mt-4 text-sm text-gray-600';
                const title = document.createElement('span');
                title.className = 'font-medium text-gray-700';
                title.textContent = tfCourseI18n.materials;
                materials.appendChild(title);
                if (lesson.materials_url) {
                    const link = document.createElement('a');
                    link.className = 'ml-2 text-indigo-600 hover:text-indigo-800 underline';
                    link.href = lesson.materials_url;
                    link.target = '_blank';
                    link.rel = 'noopener';
                    link.textContent = lesson.materials_title || tfCourseI18n.link;
                    materials.appendChild(link);
                } else if (lesson.materials_title) {
                    const text = document.createElement('span');
                    text.className = 'ml-2';
                    text.textContent = lesson.materials_title;
                    materials.appendChild(text);
                }
                textEl.insertAdjacentElement('afterend', materials);
            }

            const primaryVideoUrl = normalizeLessonVideoUrl(lesson.video_url || lesson.video || '');
            const materialsUrl = normalizeLessonVideoUrl(lesson.materials_url || '');
            const videoUrl = primaryVideoUrl || materialsUrl;
            activeVideoSourceUrl = videoUrl || '';
            if (videoUrl) {
                // Search/result URLs are handled first because they can also contain "list" param.
                if (isYouTubeEmbedSearchUrl(videoUrl) || isYouTubeResultsUrl(videoUrl)) {
                    stopYouTubePlayback();
                    videoEl.classList.add('hidden');
                    videoSrcEl.removeAttribute('src');
                    const embedSearchUrl = toYouTubeEmbedSearchUrl(videoUrl);
                    if (embedSearchUrl) {
                        iframeEl.src = embedSearchUrl;
                        iframeEl.classList.remove('hidden');
                        videoEmptyEl.classList.add('hidden');
                    } else {
                        iframeEl.classList.add('hidden');
                        iframeEl.src = '';
                        showVideoEmptyState(
                            tfCourseI18n.video_embed_blocked || tfCourseI18n.video_unavailable,
                            videoUrl
                        );
                    }
                // Проверяем, является ли URL плейлистом
                } else if (isYouTubePlaylistUrl(videoUrl)) {
                    const playlistId = getYouTubePlaylistId(videoUrl);
                    if (playlistId) {
                        // Показываем плейлист через embed с playlist parameter
                        stopYouTubePlayback();
                        videoEl.classList.add('hidden');
                        videoSrcEl.removeAttribute('src');
                        // Добавляем origin для работы YouTube API
                        let embedUrl = `https://www.youtube-nocookie.com/embed/videoseries?list=${playlistId}&enablejsapi=1&rel=0&modestbranding=1&playsinline=1`;
                        try {
                            if (window.location && window.location.origin) {
                                embedUrl += `&origin=${encodeURIComponent(window.location.origin)}`;
                            }
                        } catch (e) { }
                        iframeEl.src = embedUrl;
                        iframeEl.classList.remove('hidden');
                        videoEmptyEl.classList.add('hidden');
                    } else {
                        stopYouTubePlayback();
                        videoEl.classList.add('hidden');
                        iframeEl.classList.add('hidden');
                        iframeEl.src = '';
                        showVideoEmptyState(tfCourseI18n.video_unavailable, videoUrl);
                    }
                } else if (getYouTubeVideoId(videoUrl)) {
                    const ytVideoId = getYouTubeVideoId(videoUrl);
                    if (videoEl) {
                        try { videoEl.pause(); } catch (e) { }
                        videoEl.removeAttribute('src');
                        videoSrcEl.removeAttribute('src');
                    }
                    iframeEl.classList.remove('hidden');
                    videoEl.classList.add('hidden');
                    setupYouTubePlayer(ytVideoId, videoUrl);
                    videoEmptyEl.classList.add('hidden');
                } else {
                    if (isDirectMediaUrl(videoUrl)) {
                        stopYouTubePlayback();
                        iframeEl.src = '';
                        iframeEl.classList.add('hidden');
                        videoEl.classList.remove('hidden');
                        setDirectVideoSource(videoUrl);
                        videoEmptyEl.classList.add('hidden');
                    } else {
                        stopYouTubePlayback();
                        videoEl.classList.add('hidden');
                        iframeEl.classList.add('hidden');
                        iframeEl.src = '';
                        videoSrcEl.removeAttribute('src');
                        showVideoEmptyState(tfCourseI18n.video_unavailable, videoUrl);
                    }
                }
            } else {
                stopYouTubePlayback();
                videoEl.classList.add('hidden');
                iframeEl.classList.add('hidden');
                iframeEl.src = '';
                videoEmptyEl.classList.remove('hidden');
                videoSrcEl.removeAttribute('src');
            }

            renderQuiz(lesson);
            renderPractice(lesson);

            if (lesson.completed) {
                statusEl.classList.remove('hidden');
            } else {
                statusEl.classList.add('hidden');
            }

            updateNavButtons();
        }

        function renderQuiz(lesson) {
            if (!quizEl) return;
            quizEl.innerHTML = '';
            if (lesson.type !== 'quiz') {
                quizEl.classList.add('hidden');
                return;
            }
            quizEl.classList.remove('hidden');

            const questions = Array.isArray(lesson.questions) ? lesson.questions : [];
            if (!questions.length) {
                quizEl.innerHTML = `<div class="text-sm text-gray-500">${tfCourseI18n.quiz_no_questions}</div>`;
                return;
            }

            const form = document.createElement('form');
            form.className = 'space-y-4';

            questions.forEach((q, qIdx) => {
                const options = (q.options_text || '').split('|||').filter(Boolean);
                const block = document.createElement('div');
                block.className = 'p-4 border border-gray-200 rounded-xl';
                const title = document.createElement('div');
                title.className = 'font-medium text-gray-900';
                title.textContent = q.question_text || `${tfCourseI18n.question} ${qIdx + 1}`;
                block.appendChild(title);

                const hasMulti = String(q.correct_options || '').trim() !== '';
                options.forEach((opt, optIdx) => {
                    const label = document.createElement('label');
                    label.className = 'mt-2 flex items-start gap-2 text-sm text-gray-700';
                    const input = document.createElement('input');
                    input.type = hasMulti ? 'checkbox' : 'radio';
                    input.name = hasMulti ? `q_${q.id}[]` : `q_${q.id}`;
                    input.value = String(optIdx + 1);
                    label.appendChild(input);
                    const span = document.createElement('span');
                    span.textContent = opt;
                    label.appendChild(span);
                    block.appendChild(label);
                });

                form.appendChild(block);
            });

            const actions = document.createElement('div');
            actions.className = 'flex items-center gap-3';
            const submitBtn = document.createElement('button');
            submitBtn.type = 'submit';
            submitBtn.className = 'px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700';
            submitBtn.textContent = tfCourseI18n.quiz_check;
            const result = document.createElement('div');
            result.className = 'text-sm text-gray-600';
            actions.appendChild(submitBtn);
            actions.appendChild(result);
            form.appendChild(actions);

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                tfConfettiBurst(0.4);
                let correct = 0;
                questions.forEach((q) => {
                    const correctSet = String(q.correct_options || '')
                        .split(',')
                        .map(v => parseInt(v, 10))
                        .filter(v => v > 0);
                    if (correctSet.length > 0) {
                        const picked = Array.from(form.querySelectorAll(`input[name="q_${q.id}[]"]:checked`))
                            .map(el => parseInt(el.value, 10))
                            .filter(v => v > 0);
                        const uniquePicked = Array.from(new Set(picked)).sort();
                        const uniqueCorrect = Array.from(new Set(correctSet)).sort();
                        const isMatch = uniquePicked.length === uniqueCorrect.length
                            && uniquePicked.every((val, idx) => val === uniqueCorrect[idx]);
                        if (isMatch) {
                            correct += 1;
                        }
                        return;
                    }

                    const picked = form.querySelector(`input[name="q_${q.id}"]:checked`);
                    const pickedVal = picked ? parseInt(picked.value, 10) : 0;
                    if (pickedVal && pickedVal === parseInt(q.correct_option || 0, 10)) {
                        correct += 1;
                    }
                });
                if (correct === questions.length) {
                    result.textContent = tfCourseI18n.quiz_passed;
                    const lesson = lessons[currentLessonIdx];
                    if (lesson && lesson.id) completeLesson(lesson.id);
                } else {
                    result.textContent = tfCourseI18n.quiz_failed
                        .replace('{correct}', String(correct))
                        .replace('{total}', String(questions.length));
                }
            });

            quizEl.appendChild(form);
        }

        function renderPractice(lesson) {
            if (!practiceEl) return;
            practiceEl.innerHTML = '';

            const hasTask = !!(lesson && lesson.practice_task_id);
            const required = !!(lesson && lesson.practice_required);
            if (!hasTask || !required) {
                practiceEl.classList.add('hidden');
                return;
            }

            practiceEl.classList.remove('hidden');

            const practiceTitle = tfCourseI18n.practice_title || 'Practice task';
            const practiceRun = tfCourseI18n.practice_run || 'Run tests';
            const practiceChecking = tfCourseI18n.practice_checking || 'Checking...';
            const practicePassedText = tfCourseI18n.practice_passed || 'Passed.';
            const practiceFailedText = tfCourseI18n.practice_failed || 'Failed.';
            const practicePlaceholder = tfCourseI18n.practice_placeholder || 'Write your solution here...';
            const practiceFillTitle = tfCourseI18n.practice_fill_title || 'Fill in the blanks';
            const practiceFillBlank = tfCourseI18n.practice_fill_blank || 'Blank {num}';
            const practiceFillCode = tfCourseI18n.practice_fill_code || 'Code snippet';
            const practiceFillRequired = tfCourseI18n.practice_fill_required || 'Fill all blanks before checking.';
            const practiceFormatHint = tfCourseI18n.practice_format_hint || 'Format: complete the code.';
            const practiceFillHint = tfCourseI18n.practice_fill_hint || 'Format: enter each blank value.';
            const practiceFillSelect = tfCourseI18n.practice_fill_select || 'Select option';
            const practiceUpload = tfCourseI18n.practice_upload || 'Upload solution';
            const practiceUploaded = tfCourseI18n.practice_uploaded || 'Solution loaded.';
            const practiceUploadError = tfCourseI18n.practice_upload_error || 'Unable to load file.';
            const practiceUploadTooLarge = tfCourseI18n.practice_upload_too_large || 'File is too large. Max 256 MB.';
            const practiceUploadInvalidType = tfCourseI18n.practice_upload_invalid_type || 'Unsupported file type.';
            const practiceLang = String(lesson.practice_language || '').toLowerCase();
            const isFillTask = practiceLang === 'fill';
            const allowedSolutionExtensions = ['txt', 'cpp', 'cc', 'c', 'h', 'hpp', 'py', 'java', 'cs', 'js', 'ts', 'go', 'rs', 'php', 'rb', 'kt', 'swift', 'scala', 'dart', 'sql'];
            const maxSolutionFileSize = 268435456;

            const header = document.createElement('div');
            header.className = 'flex items-center justify-between gap-3';
            header.innerHTML = `
                <div class="text-sm font-semibold text-gray-900">${isFillTask ? practiceFillTitle : practiceTitle}</div>
                <div class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">${(lesson.practice_language || '').toUpperCase() || 'TASK'}</div>
            `;

            const prompt = document.createElement('div');
            prompt.className = 'mt-2 text-sm text-gray-700 whitespace-pre-line';
            prompt.textContent = lesson.practice_prompt || '';
            const storageKey = `tf_practice_code_${lesson.practice_task_id}`;
            const savedRaw = localStorage.getItem(storageKey);

            let textarea = null;
            let fillInputs = [];
            const loadPracticeSolutionFile = async (file) => {
                if (!file || !textarea) return;
                const name = String(file.name || '');
                const ext = name.includes('.') ? name.split('.').pop().toLowerCase() : '';
                if (!allowedSolutionExtensions.includes(ext)) {
                    throw new Error(practiceUploadInvalidType);
                }
                if (Number(file.size || 0) > maxSolutionFileSize) {
                    throw new Error(practiceUploadTooLarge);
                }
                const text = await file.text();
                if (text.includes('\u0000')) {
                    throw new Error(practiceUploadInvalidType);
                }
                textarea.value = text.replace(/\r\n?/g, '\n');
                localStorage.setItem(storageKey, textarea.value);
            };
            if (isFillTask) {
                const normalizedToken = (value) => String(value || '').trim().replace(/\s+/g, ' ');
                const uniquePush = (target, value) => {
                    const v = normalizedToken(value);
                    if (!v || target.includes(v)) return;
                    target.push(v);
                };
                const snippetTitle = document.createElement('div');
                snippetTitle.className = 'mt-3 text-xs font-semibold uppercase tracking-wide text-gray-500';
                snippetTitle.textContent = practiceFillCode;

                const snippet = document.createElement('pre');
                snippet.className = 'mt-2 rounded-xl border border-gray-200 bg-white p-3 text-sm text-gray-800 overflow-x-auto whitespace-pre';
                snippet.textContent = lesson.practice_starter_code || 'if (___) {\n    cout << ___;\n}';
                const fillTests = Array.isArray(lesson.practice_tests) ? lesson.practice_tests : [];
                const expectedAnswers = Array.isArray(fillTests[0] && fillTests[0].answers) ? fillTests[0].answers : [];
                const snippetBlankCount = (snippet.textContent.match(/___/g) || []).length;
                const blanksCount = Math.max(1, snippetBlankCount, expectedAnswers.length);
                const savedAnswers = (() => {
                    if (!savedRaw) return [];
                    try {
                        const parsed = JSON.parse(savedRaw);
                        return Array.isArray(parsed.answers) ? parsed.answers : [];
                    } catch (e) {
                        return [];
                    }
                })();

                const inputsWrap = document.createElement('div');
                inputsWrap.className = 'mt-3 grid grid-cols-1 md:grid-cols-2 gap-3';
                for (let idx = 0; idx < blanksCount; idx += 1) {
                    const expected = normalizedToken(expectedAnswers[idx] || '');
                    const options = [];
                    uniquePush(options, expected);
                    const tokenPool = (snippet.textContent.match(/[A-Za-z_#][A-Za-z0-9_:#.%+-]*/g) || [])
                        .filter(token => token !== '___' && token.length <= 32);
                    uniquePush(options, tokenPool[idx] || '');
                    uniquePush(options, tokenPool[idx + 1] || '');
                    ['true', 'false', 'null', '0', '1', 'auto', 'none', 'main', 'item', 'value'].forEach((fallback) => {
                        if (options.length < 4) uniquePush(options, fallback);
                    });
                    while (options.length < 4) {
                        uniquePush(options, `opt_${idx + options.length + 1}`);
                    }

                    const field = document.createElement('div');
                    field.className = 'space-y-1';
                    const label = document.createElement('label');
                    label.className = 'block text-xs font-semibold uppercase tracking-wide text-gray-500';
                    label.textContent = practiceFillBlank.replace('{num}', String(idx + 1));

                    const select = document.createElement('select');
                    select.className = 'w-full rounded-xl border border-gray-200 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white';

                    const placeholderOpt = document.createElement('option');
                    placeholderOpt.value = '';
                    placeholderOpt.textContent = practiceFillSelect;
                    select.appendChild(placeholderOpt);

                    options.forEach((optValue) => {
                        const opt = document.createElement('option');
                        opt.value = optValue;
                        opt.textContent = optValue;
                        select.appendChild(opt);
                    });

                    const savedValue = normalizedToken(savedAnswers[idx] || '');
                    if (savedValue !== '') {
                        const exists = options.some(optValue => optValue === savedValue);
                        if (!exists) {
                            const savedOpt = document.createElement('option');
                            savedOpt.value = savedValue;
                            savedOpt.textContent = savedValue;
                            select.appendChild(savedOpt);
                        }
                        select.value = savedValue;
                    }

                    select.addEventListener('change', () => {
                        const snapshot = { answers: fillInputs.map(el => el.value || '') };
                        localStorage.setItem(storageKey, JSON.stringify(snapshot));
                    });
                    fillInputs.push(select);
                    field.appendChild(label);
                    field.appendChild(select);
                    inputsWrap.appendChild(field);
                }

                const box = document.createElement('div');
                box.className = 'p-4 border border-gray-200 rounded-2xl bg-gray-50';
                box.appendChild(header);
                if (prompt.textContent) box.appendChild(prompt);
                const hint = document.createElement('div');
                hint.className = 'mt-2 text-xs text-gray-500';
                hint.textContent = practiceFillHint;
                box.appendChild(hint);
                box.appendChild(snippetTitle);
                box.appendChild(snippet);
                box.appendChild(inputsWrap);
                practiceEl.appendChild(box);
            } else {
                textarea = document.createElement('textarea');
                textarea.setAttribute('data-no-tinymce', 'true');
                textarea.className = 'mt-3 w-full min-h-[220px] rounded-xl border border-gray-200 p-3 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200';
                textarea.placeholder = practicePlaceholder;
                textarea.value = savedRaw !== null ? savedRaw : (lesson.practice_starter_code || '');
                textarea.addEventListener('input', () => {
                    localStorage.setItem(storageKey, textarea.value);
                });

                const box = document.createElement('div');
                box.className = 'p-4 border border-gray-200 rounded-2xl bg-gray-50';
                box.appendChild(header);
                if (prompt.textContent) box.appendChild(prompt);
                const hint = document.createElement('div');
                hint.className = 'mt-2 text-xs text-gray-500';
                hint.textContent = practiceFormatHint;
                box.appendChild(hint);
                const fileWrap = document.createElement('div');
                fileWrap.className = 'mt-3';
                const fileLabel = document.createElement('label');
                fileLabel.className = 'inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-100';
                fileLabel.title = practiceUpload;
                fileLabel.setAttribute('aria-label', practiceUpload);
                fileLabel.innerHTML = '<i class="fas fa-file-arrow-up"></i>';
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.className = 'hidden';
                fileInput.accept = '.txt,.cpp,.cc,.c,.h,.hpp,.py,.java,.cs,.js,.ts,.go,.rs,.php,.rb,.kt,.swift,.scala,.dart,.sql';
                fileInput.addEventListener('change', async (event) => {
                    const file = event.target && event.target.files ? event.target.files[0] : null;
                    if (!file) return;
                    try {
                        await loadPracticeSolutionFile(file);
                        if (typeof window.tfNotify === 'function') window.tfNotify(practiceUploaded);
                    } catch (e) {
                        if (typeof window.tfNotify === 'function') window.tfNotify(e && e.message ? e.message : practiceUploadError);
                    } finally {
                        event.target.value = '';
                    }
                });
                fileLabel.appendChild(fileInput);
                fileWrap.appendChild(fileLabel);
                box.appendChild(fileWrap);
                box.appendChild(textarea);
                practiceEl.appendChild(box);
            }

            const runBtn = document.createElement('button');
            runBtn.type = 'button';
            runBtn.className = 'px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed';
            runBtn.textContent = practiceRun;

            const result = document.createElement('div');
            result.className = 'text-sm text-gray-600 whitespace-pre-line';

            const actions = document.createElement('div');
            actions.className = 'mt-3 flex flex-col sm:flex-row sm:items-center gap-3';
            actions.appendChild(runBtn);
            actions.appendChild(result);

            const actionsWrap = document.createElement('div');
            actionsWrap.className = 'mt-3';
            actionsWrap.appendChild(actions);
            const targetBox = practiceEl.firstElementChild;
            if (targetBox) {
                targetBox.appendChild(actionsWrap);
            }

            if (lesson.practice_passed) {
                result.className = 'text-sm text-emerald-700 whitespace-pre-line';
                result.textContent = practicePassedText;
            }

            runBtn.addEventListener('click', () => {
                const payload = {
                    lessonId: lesson.id,
                    language: lesson.practice_language || ''
                };
                if (isFillTask) {
                    const answers = fillInputs.map(el => (el.value || '').trim());
                    if (!answers.length || answers.some(v => v === '')) {
                        result.className = 'text-sm text-rose-700 whitespace-pre-line';
                        result.textContent = practiceFillRequired;
                        return;
                    }
                    payload.answers = answers;
                    payload.code = lesson.practice_starter_code || '';
                } else {
                    const code = textarea ? (textarea.value || '') : '';
                    if (!code.trim()) {
                        result.className = 'text-sm text-rose-700 whitespace-pre-line';
                        result.textContent = practiceFailedText;
                        return;
                    }
                    payload.code = code;
                }

                runBtn.disabled = true;
                result.className = 'text-sm text-gray-600 whitespace-pre-line';
                result.textContent = practiceChecking;

                fetch('?action=submit-practice', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                })
                    .then(r => r.json())
                    .then((data) => {
                        if (data && data.message && typeof window.tfNotify === 'function') {
                            window.tfNotify(String(data.message));
                        }
                        if (data && data.success && data.passed) {
                            lesson.practice_passed = true;
                            result.className = 'text-sm text-emerald-700 whitespace-pre-line';
                            result.textContent = practicePassedText;
                            tfConfettiBurst();

                            if (!lesson.completed && lesson.id) {
                                const opts = {};
                                if (videoWatched[lesson.id] === true) opts.videoCompleted = true;
                                completeLesson(lesson.id, false, opts);
                            } else {
                                renderLessonList();
                                renderLesson();
                            }
                            return;
                        }

                        result.className = 'text-sm text-rose-700 whitespace-pre-line';
                        result.textContent = practiceFailedText;
                    })
                    .catch((error) => {
                        if (typeof window.tfNotify === 'function') {
                            window.tfNotify(error && error.message ? error.message : tfCourseI18n.connection_error);
                        }
                        result.className = 'text-sm text-rose-700 whitespace-pre-line';
                        result.textContent = practiceFailedText;
                    })
                    .finally(() => {
                        runBtn.disabled = false;
                    });
            });

        }

        function updateNavButtons() {
            if (!prevLessonBtn || !nextLessonBtn) return;
            const hasPrev = currentLessonIdx > 0;
            const hasNext = currentLessonIdx < lessons.length - 1;
            prevLessonBtn.disabled = !hasPrev;
            nextLessonBtn.disabled = !hasNext;
            prevLessonBtn.classList.toggle('opacity-50', !hasPrev);
            prevLessonBtn.classList.toggle('cursor-not-allowed', !hasPrev);
            nextLessonBtn.classList.toggle('opacity-50', !hasNext);
            nextLessonBtn.classList.toggle('cursor-not-allowed', !hasNext);
        }

        function shuffleArray(arr) {
            const copy = arr.slice();
            for (let i = copy.length - 1; i > 0; i -= 1) {
                const j = Math.floor(Math.random() * (i + 1));
                [copy[i], copy[j]] = [copy[j], copy[i]];
            }
            return copy;
        }

        function setupExam() {
            if (!examSection) return;
            if (!courseExam || !courseExam.exam_json) {
                examHint.textContent = tfCourseI18n.exam_no_created;
                startExamBtn.disabled = true;
                startExamBtn.classList.add('opacity-60', 'cursor-not-allowed');
                return;
            }
            const allCompletedByLessons = lessons.length > 0 && lessons.every(l => !!l.completed);
            const allCompletedByProgress = courseProgressValue >= 100;
            const allCompleted = allCompletedByLessons || allCompletedByProgress;
            if (!allCompleted) {
                examHint.textContent = tfCourseI18n.exam_after_lessons;
                startExamBtn.disabled = true;
                startExamBtn.classList.add('opacity-60', 'cursor-not-allowed');
                return;
            }
            examHint.textContent = tfCourseI18n.exam_for_cert;
            startExamBtn.disabled = false;
            startExamBtn.classList.remove('opacity-60', 'cursor-not-allowed');
        }

        function normalizeExamType(typeRaw) {
            const t = String(typeRaw || '').trim().toLowerCase();
            if (t === 'mcq' || t === 'mc_single' || t === 'single_choice' || t === 'multiple_choice') return 'mc_single';
            if (t === 'true_false' || t === 'boolean' || t === 'bool') return 'true_false';
            if (t === 'short_answer' || t === 'short' || t === 'text' || t === 'open') return 'short_answer';
            if (t === 'code') return 'code';
            return 'mc_single';
        }

        function normalizeExamValue(value) {
            if (typeof value === 'boolean') return value ? 'true' : 'false';
            if (typeof value === 'number') return String(value);
            return String(value ?? '').trim();
        }

        function normalizeExamValueLoose(value) {
            return normalizeExamValue(value).toLowerCase().replace(/\s+/g, ' ');
        }

        function parseCorrectAnswers(rawQuestion, options, type) {
            const answers = [];

            const pushAnswer = (value) => {
                if (Array.isArray(value)) {
                    value.forEach(pushAnswer);
                    return;
                }
                if (value === null || typeof value === 'undefined') return;
                const raw = normalizeExamValue(value);
                if (!raw) return;
                answers.push(raw);
            };

            if (Object.prototype.hasOwnProperty.call(rawQuestion || {}, 'correct_answers')) {
                pushAnswer(rawQuestion.correct_answers);
            }
            if (Object.prototype.hasOwnProperty.call(rawQuestion || {}, 'correct_answer')) {
                pushAnswer(rawQuestion.correct_answer);
            }

            const optionBased = rawQuestion && (rawQuestion.correct_option ?? rawQuestion.correct_index);
            if (answers.length === 0 && typeof optionBased !== 'undefined' && options.length > 0) {
                const idx = parseInt(optionBased, 10) - 1;
                if (idx >= 0 && idx < options.length) {
                    answers.push(options[idx]);
                }
            }

            const letters = ['A', 'B', 'C', 'D', 'E', 'F'];
            const mapped = answers.map((ans) => {
                if (type === 'true_false') {
                    const low = normalizeExamValueLoose(ans);
                    if (['true', '1', 'yes', 'y', 'да', 'верно', 'истина'].includes(low)) {
                        return options[0] || 'true';
                    }
                    if (['false', '0', 'no', 'n', 'нет', 'неверно', 'ложь'].includes(low)) {
                        return options[1] || 'false';
                    }
                }
                if (options.length > 0) {
                    const low = normalizeExamValueLoose(ans);
                    if (/^\d+$/.test(low)) {
                        const idx = parseInt(low, 10) - 1;
                        if (idx >= 0 && idx < options.length) return options[idx];
                    }
                    const letterIdx = letters.indexOf(ans.toUpperCase());
                    if (letterIdx !== -1 && letterIdx < options.length) return options[letterIdx];
                }
                return ans;
            });

            const unique = [];
            const seen = new Set();
            mapped.forEach((ans) => {
                const key = normalizeExamValueLoose(ans);
                if (!key || seen.has(key)) return;
                seen.add(key);
                unique.push(ans);
            });
            return unique;
        }

        function normalizeExamQuestion(rawQuestion, idx) {
            const base = (rawQuestion && typeof rawQuestion === 'object') ? rawQuestion : {};
            let merged = { ...base };

            if ((!merged.question || !String(merged.question).trim()) && Array.isArray(base.pool) && base.pool.length > 0) {
                const variant = base.pool[Math.floor(Math.random() * base.pool.length)];
                if (variant && typeof variant === 'object') {
                    merged = { ...base, ...variant };
                }
            }

            const question = normalizeExamValue(merged.question || merged.question_text || `${tfCourseI18n.question} ${idx + 1}`);
            let type = normalizeExamType(merged.type);

            let options = [];
            if (Array.isArray(merged.options)) {
                options = merged.options.map(v => normalizeExamValue(v)).filter(Boolean);
            } else if (typeof merged.options_text === 'string' && merged.options_text.trim() !== '') {
                options = merged.options_text.split('|||').map(v => normalizeExamValue(v)).filter(Boolean);
            }

            if (type === 'true_false') {
                if (!options.length) {
                    options = [tfCourseI18n.exam_true || 'True', tfCourseI18n.exam_false || 'False'];
                } else if (options.length > 2) {
                    options = options.slice(0, 2);
                }
            }

            if (type === 'mc_single' && options.length < 2) {
                if (normalizeExamValue(merged.correct_answer || merged.correct_answers || '') !== '') {
                    type = 'short_answer';
                } else {
                    return null;
                }
            }
            if (type === 'mc_single' && options.length < 2) {
                return null;
            }
            if ((type === 'short_answer' || type === 'code') && !question) {
                return null;
            }

            const correctAnswers = parseCorrectAnswers(merged, options, type);
            if (type === 'mc_single' && Array.isArray(correctAnswers) && correctAnswers.length > 1) {
                type = 'mc_multi';
            }

            return {
                question,
                type,
                options,
                correctAnswers
            };
        }

        function isExamAnswerCorrect(question, answerValue) {
            const expected = Array.isArray(question.correctAnswers) ? question.correctAnswers : [];
            if (!expected.length) return false;

            if (Array.isArray(answerValue)) {
                const actualSet = answerValue.map(v => normalizeExamValueLoose(v)).filter(Boolean).sort();
                const expectedSet = expected.map(v => normalizeExamValueLoose(v)).filter(Boolean).sort();
                if (!actualSet.length) return false;
                if (actualSet.length !== expectedSet.length) return false;
                return actualSet.every((val, idx) => val === expectedSet[idx]);
            }

            const actual = normalizeExamValueLoose(answerValue);
            if (!actual) return false;
            return expected.some(ans => normalizeExamValueLoose(ans) === actual);
        }

        function renderExam() {
            if (!courseExam || !courseExam.exam_json || !examContainer) return;
            let rawQuestions = [];
            try { rawQuestions = JSON.parse(courseExam.exam_json || '[]'); } catch (e) { }
            if (!Array.isArray(rawQuestions) || rawQuestions.length === 0) {
                examContainer.innerHTML = `<div class="text-sm text-gray-500">${tfCourseI18n.exam_no_questions}</div>`;
                return;
            }

            let questions = rawQuestions.map((q, idx) => normalizeExamQuestion(q, idx)).filter(Boolean);
            if (!questions.length) {
                examContainer.innerHTML = `<div class="text-sm text-gray-500">${tfCourseI18n.exam_no_questions}</div>`;
                return;
            }

            if (courseExam.shuffle_questions) questions = shuffleArray(questions);
            examQuestionCount = questions.length;
            examRuntime = {
                questions,
                answers: Array(questions.length).fill(''),
                currentIndex: 0
            };

            examContainer.innerHTML = `
                <div class="space-y-4">
                    <div id="exam-question-nav" class="grid grid-cols-8 gap-2"></div>
                    <div id="exam-question-stage" class="p-4 rounded-xl exam-question"></div>
                    <div class="flex items-center justify-between gap-2">
                        <button id="exam-prev-btn" type="button" class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-50">< ${tfCourseI18n.prev}</button>
                        <div id="exam-current-label" class="text-xs text-gray-500"></div>
                        <button id="exam-next-btn" type="button" class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-50">${tfCourseI18n.next} ></button>
                    </div>
                    <div class="flex items-center gap-3">
                        <button id="exam-submit-btn" type="button" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">${tfCourseI18n.exam_check}</button>
                        <div id="exam-submit-result" class="text-sm text-gray-600"></div>
                    </div>
                </div>
            `;

            const navEl = document.getElementById('exam-question-nav');
            const stageEl = document.getElementById('exam-question-stage');
            const currentLabelEl = document.getElementById('exam-current-label');
            const prevBtn = document.getElementById('exam-prev-btn');
            const nextBtn = document.getElementById('exam-next-btn');
            const submitBtn = document.getElementById('exam-submit-btn');
            const submitResultEl = document.getElementById('exam-submit-result');

            const renderExamNav = () => {
                if (!examRuntime) return;
                navEl.innerHTML = examRuntime.questions.map((_, idx) => {
                    const answered = String(examRuntime.answers[idx] || '').trim() !== '';
                    const active = idx === examRuntime.currentIndex;
                    const cls = active
                        ? 'border-indigo-500 bg-indigo-100 text-indigo-800'
                        : answered
                            ? 'border-emerald-300 bg-emerald-50 text-emerald-700'
                            : 'border-gray-200 bg-white text-gray-500';
                    return `<button type="button" data-exam-nav="${idx}" class="h-8 rounded-md border text-xs font-semibold ${cls}">${idx + 1}</button>`;
                }).join('');
                navEl.querySelectorAll('[data-exam-nav]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        if (!examRuntime) return;
                        examRuntime.currentIndex = parseInt(btn.getAttribute('data-exam-nav') || '0', 10);
                        renderExamQuestion();
                    });
                });
            };

            const renderExamQuestion = () => {
                if (!examRuntime) return;
                const idx = examRuntime.currentIndex;
                const question = examRuntime.questions[idx];
                currentLabelEl.textContent = `${idx + 1}/${examRuntime.questions.length}`;

                prevBtn.disabled = idx <= 0;
                nextBtn.disabled = idx >= examRuntime.questions.length - 1;
                prevBtn.classList.toggle('opacity-60', prevBtn.disabled);
                prevBtn.classList.toggle('cursor-not-allowed', prevBtn.disabled);
                nextBtn.classList.toggle('opacity-60', nextBtn.disabled);
                nextBtn.classList.toggle('cursor-not-allowed', nextBtn.disabled);

                stageEl.innerHTML = '';
                const title = document.createElement('div');
                title.className = 'font-medium text-slate-900';
                title.textContent = question.question || `${tfCourseI18n.question} ${idx + 1}`;
                stageEl.appendChild(title);

                if (question.type === 'short_answer' || question.type === 'code') {
                    const input = document.createElement(question.type === 'code' ? 'textarea' : 'input');
                    if (question.type === 'short_answer') input.type = 'text';
                    if (question.type === 'code') input.setAttribute('data-no-tinymce', 'true');
                    input.className = question.type === 'code'
                        ? 'mt-3 w-full min-h-[140px] rounded-xl border border-gray-200 p-3 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200'
                        : 'mt-3 w-full rounded-xl border border-gray-200 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200';
                    input.placeholder = tfCourseI18n.exam_answer_placeholder || 'Enter your answer...';
                    input.value = examRuntime.answers[idx] || '';
                    input.addEventListener('input', () => {
                        examRuntime.answers[idx] = input.value;
                        renderExamNav();
                        updateExamProgress();
                    });
                    stageEl.appendChild(input);
                    const hint = document.createElement('div');
                    hint.className = 'mt-2 text-xs text-slate-500';
                    hint.textContent = tfCourseI18n.exam_short_hint || '';
                    if (hint.textContent) stageEl.appendChild(hint);
                } else if (question.type === 'mc_multi') {
                    let options = Array.isArray(question.options) ? question.options.slice() : [];
                    if (courseExam.shuffle_options) options = shuffleArray(options);
                    options.forEach((opt) => {
                        const label = document.createElement('label');
                        label.className = 'exam-option mt-2';
                        const input = document.createElement('input');
                        input.type = 'checkbox';
                        input.name = `exam_q_${idx}[]`;
                        input.value = opt;
                        const current = Array.isArray(examRuntime.answers[idx]) ? examRuntime.answers[idx] : [];
                        input.checked = current.includes(opt);
                        label.appendChild(input);
                        const span = document.createElement('span');
                        span.textContent = opt;
                        label.appendChild(span);
                        input.addEventListener('change', () => {
                            const selected = Array.from(stageEl.querySelectorAll(`input[name="exam_q_${idx}[]"]:checked`))
                                .map(el => el.value);
                            examRuntime.answers[idx] = selected;
                            renderExamNav();
                            updateExamProgress();
                        });
                        stageEl.appendChild(label);
                    });
                } else {
                    let options = Array.isArray(question.options) ? question.options.slice() : [];
                    if (courseExam.shuffle_options) options = shuffleArray(options);
                    options.forEach((opt) => {
                        const label = document.createElement('label');
                        label.className = 'exam-option mt-2';
                        const input = document.createElement('input');
                        input.type = 'radio';
                        input.name = `exam_q_${idx}`;
                        input.value = opt;
                        input.checked = examRuntime.answers[idx] === opt;
                        label.appendChild(input);
                        const span = document.createElement('span');
                        span.textContent = opt;
                        label.appendChild(span);
                        if (input.checked) label.classList.add('selected');
                        input.addEventListener('change', () => {
                            examRuntime.answers[idx] = opt;
                            stageEl.querySelectorAll('label.exam-option').forEach(el => el.classList.remove('selected'));
                            label.classList.add('selected');
                            renderExamNav();
                            updateExamProgress();
                        });
                        stageEl.appendChild(label);
                    });
                }
                renderExamNav();
                updateExamProgress();
            };

            prevBtn.addEventListener('click', () => {
                if (!examRuntime) return;
                if (examRuntime.currentIndex > 0) {
                    examRuntime.currentIndex -= 1;
                    renderExamQuestion();
                }
            });
            nextBtn.addEventListener('click', () => {
                if (!examRuntime) return;
                if (examRuntime.currentIndex < examRuntime.questions.length - 1) {
                    examRuntime.currentIndex += 1;
                    renderExamQuestion();
                }
            });

            submitBtn.addEventListener('click', () => {
                if (!examRuntime) return;
                tfConfettiBurst(0.4);
                const total = examRuntime.questions.length;
                const unanswered = examRuntime.answers.some(v => String(v || '').trim() === '');
                if (unanswered) {
                    submitResultEl.textContent = tfCourseI18n.exam_fill_all || 'Answer all questions before checking.';
                    return;
                }
                let correct = 0;
                examRuntime.questions.forEach((q, idx) => {
                    if (isExamAnswerCorrect(q, examRuntime.answers[idx])) {
                        correct += 1;
                    }
                });
                const percent = Math.round((correct / total) * 100);
                if (percent >= (courseExam.pass_percent || 70)) {
                    submitResultEl.textContent = tfCourseI18n.exam_passed.replace('{percent}', String(percent));
                    tfConfettiBurst();
                    stopExamTimer();
                    issueCourseCertificate();
                } else {
                    submitResultEl.textContent = tfCourseI18n.exam_failed
                        .replace('{percent}', String(percent))
                        .replace('{pass}', String(courseExam.pass_percent || 70));
                }
            });

            renderExamQuestion();
            startExamTimer();
        }

        function tfConfettiBurst(power = 1) {
            const canvas = document.createElement('canvas');
            canvas.style.position = 'fixed';
            canvas.style.top = '0';
            canvas.style.left = '0';
            canvas.style.width = '100%';
            canvas.style.height = '100%';
            canvas.style.pointerEvents = 'none';
            canvas.style.zIndex = '9999';
            document.body.appendChild(canvas);
            const ctx = canvas.getContext('2d');
            const dpr = window.devicePixelRatio || 1;
            const resize = () => {
                canvas.width = window.innerWidth * dpr;
                canvas.height = window.innerHeight * dpr;
                ctx.scale(dpr, dpr);
            };
            resize();

            const colors = ['#22c55e', '#60a5fa', '#f59e0b', '#f472b6', '#a78bfa'];
            const count = Math.max(40, Math.floor(120 * power));
            const particles = Array.from({ length: count }, () => ({
                x: window.innerWidth / 2,
                y: window.innerHeight / 3,
                vx: (Math.random() - 0.5) * 8 * power,
                vy: (Math.random() * -8 - 4) * power,
                size: Math.random() * 4 + 3,
                color: colors[Math.floor(Math.random() * colors.length)],
                life: 100 + Math.random() * 40
            }));

            let frame = 0;
            const tick = () => {
                frame++;
                ctx.clearRect(0, 0, window.innerWidth, window.innerHeight);
                particles.forEach(p => {
                    p.vy += 0.15;
                    p.x += p.vx;
                    p.y += p.vy;
                    p.life -= 1;
                    ctx.fillStyle = p.color;
                    ctx.fillRect(p.x, p.y, p.size, p.size);
                });
                if (frame < 120) {
                    requestAnimationFrame(tick);
                } else {
                    canvas.remove();
                }
            };
            tick();
        }

        function tfHappyEnd() {
            const title = tfCourseI18n.happy_title || 'Congrats!';
            const subtitle = tfCourseI18n.happy_subtitle || '';
            const wrap = document.createElement('div');
            wrap.className = 'tf-happy-end';
            wrap.innerHTML = `
                <div class="tf-happy-card">
                    <div class="tf-happy-title">${title}</div>
                    <div class="tf-happy-subtitle">${subtitle}</div>
                </div>
            `;
            document.body.appendChild(wrap);
            setTimeout(() => wrap.remove(), 1900);
        }

        function issueCourseCertificate() {
            fetch('?action=course-issue-certificate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ course_id: <?= (int) ($course['id'] ?? 0) ?> })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showCourseCert(data.course_title || '');
                    } else {
                        if (window.tfNotify) tfNotify(data.message || tfCourseI18n.error);
                    }
                })
                .catch(() => {
                    if (window.tfNotify) tfNotify(tfCourseI18n.connection_error);
                });
        }

        function showCourseCert(courseTitle) {
            const modal = document.getElementById('course-cert-modal');
            const fallbackTitle = <?= tfSafeJson($course['title'] ?? '', JSON_UNESCAPED_UNICODE) ?>;
            document.getElementById('cert-course-title').textContent = courseTitle || fallbackTitle || '';
            document.getElementById('cert-date').textContent = new Date().toLocaleDateString();
            tfHappyEnd();
            if (modal) modal.classList.remove('hidden');
        }

        function closeCourseCert() {
            window.location.href = '?action=profile&tab=certificates';
        }

        function selectLesson(idx) {
            currentLessonIdx = idx;
            renderLessonList();
            renderLesson();
        }

        function completeLesson(lessonId, autoAdvance = true, options = {}) {
            if (completeLessonRequestInFlight) return;
            completeLessonRequestInFlight = true;
            const payload = { lessonId };
            if (options.videoCompleted === true) {
                payload.videoCompleted = true;
            }
            fetch('?action=complete-lesson', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (window.tfNotify) tfNotify(tfCourseI18n.lesson_marked);
                        tfConfettiBurst();
                        if (lessons[currentLessonIdx]) {
                            lessons[currentLessonIdx].completed = true;
                        }
                        if (typeof data.progress !== 'undefined') {
                            courseProgressValue = Number(data.progress) || courseProgressValue;
                        }
                        renderLessonList();
                        renderLesson();
                        setupExam();
                        if (autoAdvance && currentLessonIdx + 1 < lessons.length) {
                            selectLesson(currentLessonIdx + 1);
                        }
                    } else {
                        if (window.tfNotify) tfNotify(data.message || tfCourseI18n.error);
                    }
                })
                .catch(() => {
                    if (window.tfNotify) tfNotify(tfCourseI18n.connection_error);
                })
                .finally(() => {
                    completeLessonRequestInFlight = false;
                });
        }

        if (videoEl) {
            videoEl.addEventListener('ended', () => {
                onLessonVideoFinished();
            });
            videoEl.addEventListener('error', () => {
                videoEl.classList.add('hidden');
                if (videoEmptyEl) videoEmptyEl.classList.remove('hidden');
                if (window.tfNotify) tfNotify(tfCourseI18n.video_missing || 'Видео недоступно');
            });
        }

        if (prevLessonBtn) {
            prevLessonBtn.addEventListener('click', () => {
                if (currentLessonIdx > 0) selectLesson(currentLessonIdx - 1);
            });
        }

        if (nextLessonBtn) {
            nextLessonBtn.addEventListener('click', () => {
                const lesson = lessons[currentLessonIdx];
                if (!lesson) return;
                if (!lesson.completed) {
                    if (lesson.type === 'quiz') return;
                    if (lessonRequiresVideoWatch(lesson)) {
                        if (window.tfNotify) tfNotify(tfCourseI18n.watch_video_to_end);
                        return;
                    }
                    if (lesson.id) {
                        completeLesson(lesson.id, true);
                        return;
                    }
                }
                if (currentLessonIdx + 1 < lessons.length) selectLesson(currentLessonIdx + 1);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (lessons.length === 0) {
                renderLesson();
                return;
            }
            renderLessonList();
            renderLesson();
            setupExam();
        });

        function openExamModal() {
            if (examModal) examModal.classList.remove('hidden');
        }

        function closeExamModal() {
            if (examModal) examModal.classList.add('hidden');
            stopExamTimer();
        }

        if (startExamBtn) {
            startExamBtn.addEventListener('click', () => {
                renderExam();
                openExamModal();
            });
        }

        function updateExamProgress() {
            if (!examProgressText || !examProgressBar) return;
            const total = examRuntime && Array.isArray(examRuntime.questions) ? examRuntime.questions.length : (examQuestionCount || 0);
            let answered = 0;
            if (examRuntime && Array.isArray(examRuntime.answers)) {
                answered = examRuntime.answers.reduce((acc, v) => acc + (String(v || '').trim() !== '' ? 1 : 0), 0);
            }
            const percent = total ? Math.round((answered / total) * 100) : 0;
            examProgressText.textContent = total ? `${answered}/${total}` : '0/0';
            examProgressBar.style.width = `${percent}%`;
        }

        function startExamTimer() {
            stopExamTimer();
            const minutes = parseInt((courseExam && courseExam.time_limit_minutes) || 0, 10);
            if (!examTimerEl || !minutes) {
                if (examTimerEl) examTimerEl.textContent = tfCourseI18n.infinity;
                return;
            }
            examEndsAt = Date.now() + minutes * 60 * 1000;
            examTimer = setInterval(() => {
                const remaining = Math.max(0, examEndsAt - Date.now());
                const totalSeconds = Math.floor(remaining / 1000);
                const mm = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
                const ss = String(totalSeconds % 60).padStart(2, '0');
                examTimerEl.textContent = `${mm}:${ss}`;
                if (remaining <= 0) {
                    stopExamTimer();
                    if (window.tfNotify) tfNotify(tfCourseI18n.exam_time_up);
                    closeExamModal();
                }
            }, 500);
        }

        function stopExamTimer() {
            if (examTimer) {
                clearInterval(examTimer);
                examTimer = null;
            }
        }
    </script>
</body>

</html>

