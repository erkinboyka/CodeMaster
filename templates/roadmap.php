<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$roadmapTitle = $_GET['id'] ?? '';
$ROADMAP_I18N = [
'ru' => [
    'page_title' => 'Роадмап - CodeMaster',
    'all_roadmaps' => 'Все роадмапы',
    'roadmap' => 'Роадмап',
    'progress' => 'Прогресс',
    'blocks' => 'Блоки',
    'details' => 'Детали',
    'choose_block' => 'Выберите блок, чтобы увидеть детали.',
    'program' => 'Программа',
    'close' => 'Закрыть',
    'loading' => 'Загрузка...',
    'watch_video_for_quiz' => 'Досмотрите видео для доступа к тесту',
    'take_quiz' => 'Пройти тест',
    'quiz_title' => 'Тестирование',
    'quiz_info' => 'Ответьте на все вопросы',
    'submit_answers' => 'Отправить ответы',
    'certificate' => 'Сертификат',
    'certificate_subtitle' => 'Об успешном окончании модуля',
    'happy_title' => 'Поздравляем!',
    'happy_subtitle' => 'Сертификат готов и добавлен в профиль.',
    'course_placeholder' => 'КУРС',
    'print_pdf' => 'Печать / PDF',
    'to_home' => 'На главную',
    'topic_not_set' => 'Тема не указана',
    'material' => 'Материал',
    'materials_not_set' => 'Полезные материалы не указаны',
    'nodes_count' => '{count} блок(ов)',
    'nodes_not_found' => 'Блоки не найдены.',
    'exam' => 'Экзамен',
    'module' => 'Модуль',
    'done' => 'Пройдено',
    'available' => 'Доступно',
    'locked' => 'Закрыто',
    'in_progress' => 'В процессе',
    'topic' => 'Тема',
    'materials' => 'Материалы',
    'no_materials' => 'Нет материалов',
    'mark_materials' => 'Отметить материалы как пройденные',
    'materials_done' => 'Материалы отмечены',
    'quiz_locked_materials' => 'Сначала отметьте материалы как пройденные',
    'lessons_count' => 'Уроков: {count}',
    'open_module' => 'Открыть модуль',
    'module_locked_message' => 'Сначала завершите предыдущие модули',
    'module_empty' => 'В этом модуле ещё нет уроков',
    'load_error' => 'Ошибка загрузки данных',
    'video_missing' => 'Видео не задано для этого урока',
    'lesson_completed_next' => 'Урок пройден. Переходим к следующему',
    'all_lessons_done' => 'Все уроки пройдены. Тест доступен!',
    'quiz_not_found' => 'Вопросы для теста не найдены',
    'quiz_success' => 'Успешно! {score}/{total}',
    'quiz_fail' => 'Неудача. {score} из {total}',
    'quiz_pass_min' => 'Нужно набрать минимум {score} из {total}',
    'server_connection_error' => 'Ошибка связи с сервером',
    'lecture' => 'Лекция',
    'mini_test' => 'Мини-тест',
    'final_exam' => 'Финальный экзамен',
    'check_mini_test' => 'Проверить мини-тест',
    'check_exam' => 'Проверить экзамен',
    'complete_lecture_first' => 'Сначала отметьте лекцию и материалы как пройденные',
    'exam_only_node' => 'Финальный экзамен доступен только для экзамен-узла',
    'exam_unlock_message' => 'Экзамен доступен после прохождения всех блоков.',
    'fill_all_answers' => 'Ответьте на все вопросы перед отправкой',
    'lecture_done' => 'Лекция и материалы отмечены',
    'open_lecture_video' => 'Открыть видео лекции',
    'lesson' => 'Урок',
    'description_missing' => 'Описание отсутствует',
    'useful_materials' => 'Полезные материалы',
    'meta_id' => 'ID: {id}',
    'default_roadmap' => 'Основной',
    'search_placeholder' => 'Поиск по блокам...',
    'filter_label' => 'Фильтр',
    'filter_all' => 'Все',
    'filter_available' => 'Доступно',
    'filter_done' => 'Пройдено',
    'filter_locked' => 'Закрыто',
    'filter_in_progress' => 'В процессе',
    'filter_exam' => 'Экзамен',
    'pagination_prev' => 'Назад',
    'pagination_next' => 'Вперёд'
],
    'en' => [
        'page_title' => 'Roadmap - CodeMaster',
        'all_roadmaps' => 'All roadmaps',
        'roadmap' => 'Roadmap',
        'progress' => 'Progress',
        'blocks' => 'Blocks',
        'details' => 'Details',
        'choose_block' => 'Select a block to view details.',
        'program' => 'Program',
        'close' => 'Close',
        'loading' => 'Loading...',
        'watch_video_for_quiz' => 'Watch the video to unlock the quiz',
        'take_quiz' => 'Take quiz',
        'quiz_title' => 'Quiz',
        'quiz_info' => 'Answer all questions',
        'submit_answers' => 'Submit answers',
        'certificate' => 'Certificate',
        'certificate_subtitle' => 'For successful module completion',
        'happy_title' => 'Congrats!',
        'happy_subtitle' => 'Your certificate is ready in your profile.',
        'course_placeholder' => 'COURSE',
        'print_pdf' => 'Print / PDF',
        'to_home' => 'Home',
        'topic_not_set' => 'Topic is not set',
        'material' => 'Material',
        'materials_not_set' => 'No useful materials specified',
        'nodes_count' => '{count} block(s)',
        'nodes_not_found' => 'No blocks found.',
        'exam' => 'Exam',
        'module' => 'Module',
        'done' => 'Completed',
        'available' => 'Available',
        'locked' => 'Locked',
        'in_progress' => 'In progress',
        'topic' => 'Topic',
        'materials' => 'Materials',
        'no_materials' => 'No materials',
        'mark_materials' => 'Mark materials as done',
        'materials_done' => 'Materials marked',
        'quiz_locked_materials' => 'Mark materials as done first',
        'lessons_count' => 'Lessons: {count}',
        'open_module' => 'Open module',
        'module_locked_message' => 'Complete previous modules first',
        'module_empty' => 'This module has no lessons yet',
        'load_error' => 'Failed to load data',
        'video_missing' => 'No video specified for this lesson',
        'lesson_completed_next' => 'Lesson completed. Moving to the next one',
        'all_lessons_done' => 'All lessons completed. Quiz unlocked!',
        'quiz_not_found' => 'Quiz questions not found',
        'quiz_success' => 'Success! {score}/{total}',
        'quiz_fail' => 'Failed. {score} of {total}',
        'quiz_pass_min' => 'You need at least {score} of {total}',
        'server_connection_error' => 'Server connection error',
        'lecture' => 'Lecture',
        'mini_test' => 'Mini test',
        'final_exam' => 'Final exam',
        'check_mini_test' => 'Check mini test',
        'check_exam' => 'Check exam',
        'complete_lecture_first' => 'Mark lecture and materials as done first',
        'exam_only_node' => 'Final exam is available only in the exam node',
        'exam_unlock_message' => 'Exam is available after all modules are completed.',
        'fill_all_answers' => 'Answer all questions before submitting',
        'lecture_done' => 'Lecture and materials marked',
        'open_lecture_video' => 'Open lecture video',
        'lesson' => 'Lesson',
        'description_missing' => 'No description',
        'useful_materials' => 'Useful materials',
        'meta_id' => 'ID: {id}',
        'default_roadmap' => 'Main',
        'search_placeholder' => 'Search blocks...',
        'filter_label' => 'Filter',
        'filter_all' => 'All',
        'filter_available' => 'Available',
        'filter_done' => 'Completed',
        'filter_locked' => 'Locked',
        'filter_in_progress' => 'In progress',
        'filter_exam' => 'Exam',
        'pagination_prev' => 'Prev',
        'pagination_next' => 'Next'
    ],
    'tg' => [
        'page_title' => 'Роҳнамо - CodeMaster',
        'all_roadmaps' => 'Ҳама роҳнамоҳо',
        'roadmap' => 'Роҳнамо',
        'progress' => 'Пешрафт',
        'blocks' => 'Блокҳо',
        'details' => 'Тафсилот',
        'choose_block' => 'Блокро интихоб кунед, то тафсилотро бинед.',
        'program' => 'Барнома',
        'close' => 'Бастан',
        'loading' => 'Боркунӣ...',
        'watch_video_for_quiz' => 'Барои дастрасӣ ба тест видео тамошо кунед',
        'take_quiz' => 'Гузаштан аз тест',
        'quiz_title' => 'Тест',
        'quiz_info' => 'Ба ҳамаи саволҳо ҷавоб диҳед',
        'submit_answers' => 'Фиристодани ҷавобҳо',
        'certificate' => 'Сертификат',
        'certificate_subtitle' => 'Оид ба анҷоми муваффақи модул',
        'happy_title' => 'Табрик!',
        'happy_subtitle' => 'Сертификат омода шуд ва ба профил илова гардид.',
        'course_placeholder' => 'КУРС',
        'print_pdf' => 'Чоп / PDF',
        'to_home' => 'Ба саҳифаи асосӣ',
        'topic_not_set' => 'Мавзӯъ нишон дода нашудааст',
        'material' => 'Мавод',
        'materials_not_set' => 'Маводи муфид нишон дода нашудаанд',
        'nodes_count' => '{count} блок',
        'nodes_not_found' => 'Блокҳо ёфт нашуданд.',
        'exam' => 'Имтиҳон',
        'module' => 'Модул',
        'done' => 'Анҷом шуд',
        'available' => 'Дастрас',
        'locked' => 'Басташуда',
        'in_progress' => 'Дар раванд',
        'topic' => 'Мавзӯъ',
        'materials' => 'Маводҳо',
        'no_materials' => 'Мавод нест',
        'mark_materials' => 'Маводҳоро гузашта ҳисоб кунед',
        'materials_done' => 'Маводҳо гузаштанд',
        'quiz_locked_materials' => 'Аввал маводҳоро гузашта ҳисоб кунед',
        'lessons_count' => 'Дарсҳо: {count}',
        'open_module' => 'Кушодани модул',
        'module_locked_message' => 'Аввал модулҳои қаблиро анҷом диҳед',
        'module_empty' => 'Дар ин модул ҳоло дарс нест',
        'load_error' => 'Хатои боркунии маълумот',
        'video_missing' => 'Барои ин дарс видео таъин нашудааст',
        'lesson_completed_next' => 'Дарс анҷом шуд. Ба дарси навбатӣ мегузарем',
        'all_lessons_done' => 'Ҳамаи дарсҳо анҷом шуданд. Тест дастрас шуд!',
        'quiz_not_found' => 'Саволҳои тест ёфт нашуданд',
        'quiz_success' => 'Муваффақ! {score}/{total}',
        'quiz_fail' => 'Номуваффақ. {score} аз {total}',
        'quiz_pass_min' => 'Камаш {score} аз {total} лозим аст',
        'server_connection_error' => 'Хатои пайвастшавӣ ба сервер',
        'lecture' => 'Лексия',
        'mini_test' => 'Мини-тест',
        'final_exam' => 'Имтиҳони ниҳоӣ',
        'check_mini_test' => 'Санҷиши мини-тест',
        'check_exam' => 'Санҷиши имтиҳон',
        'complete_lecture_first' => 'Аввал лексия ва маводҳоро гузашта ҳисоб кунед',
        'exam_only_node' => 'Имтиҳони ниҳоӣ танҳо дар нодаи имтиҳон дастрас аст',
        'exam_unlock_message' => 'Имтиҳон пас аз анҷоми ҳамаи блокҳо дастрас мешавад.',
        'fill_all_answers' => 'Пеш аз ирсол ба ҳамаи саволҳо ҷавоб диҳед',
        'lecture_done' => 'Лексия ва маводҳо гузашта ҳисоб шуданд',
        'open_lecture_video' => 'Кушодани видеоии лексия',
        'lesson' => 'Дарс',
        'description_missing' => 'Тавсиф мавҷуд нест',
        'useful_materials' => 'Маводи муфид',
        'meta_id' => 'ID: {id}',
        'default_roadmap' => 'Асосӣ',
        'search_placeholder' => 'Ҷустуҷӯи блокҳо...',
        'filter_label' => 'Филтр',
        'filter_all' => 'Ҳама',
        'filter_available' => 'Дастрас',
        'filter_done' => 'Анҷом шуд',
        'filter_locked' => 'Басташуда',
        'filter_in_progress' => 'Дар раванд',
        'filter_exam' => 'Имтиҳон',
        'pagination_prev' => 'Қаблӣ',
        'pagination_next' => 'Баъдӣ'
    ]
];
$roadmapLang = currentLang();
$roadmapI18n = $ROADMAP_I18N[$roadmapLang] ?? $ROADMAP_I18N['ru'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($roadmapI18n['page_title']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
            background: #f7f8fb;
            color: #0f172a;
        }

        .roadmap-line {
            stroke-width: 2.5px;
            transition: stroke 0.4s ease-in-out, stroke-dasharray 0.4s ease-in-out;
            stroke: rgba(148, 163, 184, 0.7);
            stroke-dasharray: 6 6;
        }

        .roadmap-line.unlocked {
            stroke: #0ea5e9;
            stroke-dasharray: 0;
        }

        .roadmap-line.completed {
            stroke: #22c55e;
            stroke-dasharray: 0;
        }

        .quiz-option input:checked+label {
            border-color: #0ea5e9;
            background-color: rgba(14, 165, 233, 0.08);
        }

        #roadmap-container {
            cursor: grab;
        }

        #roadmap-container.dragging {
            cursor: grabbing;
        }
        .exam-question {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            color: #111827;
        }

        .exam-question-card {
            position: relative;
        }

        .tf-fade {
            animation: tfFadeIn 220ms ease;
        }

        @keyframes tfFadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
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

        .quiz-option-label {
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

        .quiz-option-label.variant-0 {
            border-color: #c7d2fe;
            background: #eef2ff;
        }

        .quiz-option-label.variant-1 {
            border-color: #a5f3fc;
            background: #ecfeff;
        }

        .quiz-option-label.variant-2 {
            border-color: #fde68a;
            background: #fef3c7;
        }

        .quiz-option-label.variant-3 {
            border-color: #86efac;
            background: #dcfce7;
        }

        .quiz-option-key {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid transparent;
            flex-shrink: 0;
        }

        .quiz-option-key-0 {
            background: #eef2ff;
            color: #3730a3;
            border-color: #c7d2fe;
        }

        .quiz-option-key-1 {
            background: #ecfeff;
            color: #155e75;
            border-color: #a5f3fc;
        }

        .quiz-option-key-2 {
            background: #fef3c7;
            color: #92400e;
            border-color: #fde68a;
        }

        .quiz-option-key-3 {
            background: #dcfce7;
            color: #166534;
            border-color: #86efac;
        }

        .quiz-option-text {
            flex: 1;
            min-width: 0;
        }
        .btn-primary {
            background: #0ea5e9;
            color: #fff;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: #0284c7;
        }

        .btn-ghost {
            background: #fff;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-ghost:hover {
            border-color: #bae6fd;
            color: #0ea5e9;
            background: #f0f9ff;
        }

        .node-title-clamp {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .node-topic-clamp {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        #active-roadmap-meta,
        #lesson-description {
            overflow-wrap: anywhere;
            word-break: break-word;
            hyphens: auto;
        }

        .roadmap-toolbar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .roadmap-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-width: 42px;
            min-height: 42px;
            padding: 0 14px;
        }

        .roadmap-surface.is-fullscreen {
            display: flex;
            flex-direction: column;
            height: 100%;
            border-radius: 0;
            padding: 1.25rem;
        }

        .roadmap-surface.is-fullscreen #roadmap-container {
            flex: 1;
            min-height: 0;
            height: auto;
        }

        body.roadmap-fullscreen-open {
            overflow: hidden;
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="max-w-6xl mx-auto px-4 md:px-6 py-6 md:py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <a href="?action=roadmaps" class="text-sm text-slate-500 hover:text-sky-600">&larr;
                    <?= htmlspecialchars($roadmapI18n['all_roadmaps']) ?></a>
                <h1 id="active-roadmap-title" class="text-2xl md:text-4xl font-extrabold mt-2">
                    <?= htmlspecialchars($roadmapI18n['roadmap']) ?></h1>
                <p id="active-roadmap-meta" class="text-slate-500 mt-1 break-words"></p>
            </div>
            <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-4 flex items-center gap-4">
                <div>
                    <div class="text-xs text-slate-500 uppercase font-semibold">
                        <?= htmlspecialchars($roadmapI18n['progress']) ?></div>
                    <div class="text-2xl font-bold text-sky-600" id="progress-text">0%</div>
                </div>
                <div class="w-40">
                    <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
                        <div id="progress-bar" class="h-full bg-gradient-to-r from-sky-500 to-emerald-500"
                            style="width:0%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="roadmap-surface" class="roadmap-surface bg-white rounded-2xl shadow-lg border border-slate-200 p-5 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg md:text-xl font-semibold"><?= htmlspecialchars($roadmapI18n['blocks']) ?></h2>
                <div class="roadmap-toolbar">
                    <span id="nodes-count" class="text-sm text-slate-500"></span>
                    <button
                        id="roadmapFullscreenBtn"
                        type="button"
                        class="btn-ghost roadmap-icon-btn"
                        aria-label="<?= htmlspecialchars(t('editor_fullscreen', 'Fullscreen')) ?>"
                        title="<?= htmlspecialchars(t('editor_fullscreen', 'Fullscreen')) ?>">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
            </div>
            <div id="roadmap-container"
                class="relative w-full h-[820px] overflow-auto bg-slate-50 rounded-2xl border border-slate-200">
                <svg id="roadmap-svg-layer" class="absolute top-0 left-0 w-full h-full"
                    style="width: 1400px; height: 820px;"></svg>
                <div id="roadmap-nodes-layer" class="absolute top-0 left-0" style="width: 1400px; height: 820px;"></div>
            </div>
            <div id="lesson-modal" class="fixed inset-0 hidden items-center justify-center p-4 z-50">
                <div class="absolute inset-0 bg-black/60" onclick="closeModal()"></div>
                <div class="relative bg-white w-full max-w-4xl max-h-[85vh] rounded-3xl overflow-hidden flex flex-col shadow-2xl">
                    <header class="flex items-center justify-between p-6 border-b border-slate-200">
                            <div>
                                <div id="lesson-topic" class="text-xs uppercase tracking-wide text-slate-400">
                                    <?= htmlspecialchars($roadmapI18n['topic']) ?></div>
                                <h2 id="lesson-title" class="text-2xl font-bold text-slate-900"></h2>
                            </div>
                            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-800 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </header>
                        <div class="p-6 overflow-y-auto">
                            <div id="lesson-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="lg:col-span-2 space-y-6">
                                    <div id="lesson-lecture-section">
                                        <h3 class="text-lg font-semibold text-slate-900 mb-2">
                                            <?= htmlspecialchars($roadmapI18n['lecture']) ?></h3>
                                        <p id="lesson-description" class="text-slate-700 mb-3 break-words"></p>
                                        <a id="lesson-video-link"
                                            class="inline-flex items-center gap-2 text-sky-600 hover:underline mb-3" href="#"
                                            target="_blank" rel="noopener"></a>
                                        <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-500 mb-2">
                                            <?= htmlspecialchars($roadmapI18n['materials']) ?></h4>
                                        <ul id="lesson-materials" class="space-y-2 text-slate-700"></ul>
                                        <div class="mt-4 flex flex-col sm:flex-row sm:items-center gap-3">
                                            <button id="lesson-complete-btn"
                                                class="btn-ghost"><?= htmlspecialchars($roadmapI18n['mark_materials']) ?></button>
                                            <span id="lesson-complete-status" class="text-xs text-slate-500"></span>
                                        </div>
                                    </div>
                                    <div id="mini-test-section" class="border-t border-slate-200 pt-6">
                                        <h3 class="text-lg font-semibold text-slate-900 mb-2">
                                            <?= htmlspecialchars($roadmapI18n['mini_test']) ?></h3>
                                        <div id="mini-test" class="space-y-3"></div>
                                        <p id="mini-test-result" class="mt-3 text-sm"></p>
                                    </div>
                                    <div id="exam-section" class="border-t border-slate-200 pt-6 hidden">
                                        <h3 class="text-lg font-semibold text-slate-900 mb-2">
                                            <?= htmlspecialchars($roadmapI18n['final_exam']) ?></h3>
                                        <div id="exam-form" class="space-y-3"></div>
                                        <p id="exam-result" class="mt-3 text-sm"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <footer class="p-6 border-t border-slate-200 mt-auto flex flex-col md:flex-row gap-3">
                            <button id="mini-test-submit-btn"
                                class="flex-1 bg-sky-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-sky-400 transition-all"><?= htmlspecialchars($roadmapI18n['check_mini_test']) ?></button>
                            <button id="exam-submit-btn"
                                class="flex-1 bg-emerald-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-emerald-400 transition-all hidden"><?= htmlspecialchars($roadmapI18n['check_exam']) ?></button>
                        </footer>
                    </div>
                </div>

                <div id="cert-modal" class="fixed inset-0 hidden flex items-center justify-center p-6 z-[70]">
                    <div class="absolute inset-0 bg-black/60"></div>
                    <div class="relative bg-white rounded-3xl p-10 max-w-3xl w-full text-center shadow-2xl">
                        <h1 class="text-4xl font-extrabold text-indigo-600 mb-2">
                            <?= htmlspecialchars($roadmapI18n['certificate']) ?></h1>
                        <p class="text-slate-500 uppercase text-xs tracking-widest mb-6">
                            <?= htmlspecialchars($roadmapI18n['certificate_subtitle']) ?></p>
                        <h2 class="text-3xl font-bold mb-4">STUDENT PRO</h2>
                        <h3 id="cert-course" class="text-xl font-semibold text-slate-700 mb-6">
                            <?= htmlspecialchars($roadmapI18n['course_placeholder']) ?></h3>
                        <p class="font-semibold" id="cert-date"></p>
                        <div class="mt-8 flex gap-3 justify-center">
                            <button onclick="window.print()"
                                class="btn-ghost"><?= htmlspecialchars($roadmapI18n['print_pdf']) ?></button>
                            <button onclick="location.reload()"
                                class="btn-primary"><?= htmlspecialchars($roadmapI18n['to_home']) ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script>
        const tfI18n = <?= tfSafeJson($roadmapI18n, JSON_UNESCAPED_UNICODE) ?>;
        const roadmapTitle = <?php echo tfSafeJson($roadmapTitle, JSON_UNESCAPED_UNICODE); ?>;
        let appData = { nodes: [], progress: [], states: {}, filteredNodes: [], roadmaps: [], roadmapNodes: [] };
        let currentNode = null;

        const NODE_WIDTH = 260;
        const NODE_HEIGHT = 120;
        const NODE_GAP_X = 140;
        const NODE_GAP_Y = 140;
        const MAP_PADDING = 40;
        const QUIZ_PASS_PERCENT = 70;

        const roadmapContainer = document.getElementById('roadmap-container');
        const roadmapSurface = document.getElementById('roadmap-surface');
        const roadmapFullscreenBtn = document.getElementById('roadmapFullscreenBtn');
        if (roadmapContainer) {
            let isDragging = false;
            let dragStarted = false;
            let startX = 0;
            let startY = 0;
            let scrollLeft = 0;
            let scrollTop = 0;
            const dragThreshold = 6;

            const onPointerDown = (e) => {
                if (e.button !== 0) return;
                if (e.target.closest('button, a, input, label, textarea, select, [data-no-drag]')) {
                    isDragging = false;
                    dragStarted = false;
                    return;
                }
                isDragging = true;
                dragStarted = false;
                startX = e.clientX;
                startY = e.clientY;
                scrollLeft = roadmapContainer.scrollLeft;
                scrollTop = roadmapContainer.scrollTop;
            };

            const onPointerMove = (e) => {
                if (!isDragging) return;
                const dx = e.clientX - startX;
                const dy = e.clientY - startY;
                if (!dragStarted) {
                    if (Math.abs(dx) < dragThreshold && Math.abs(dy) < dragThreshold) {
                        return;
                    }
                    dragStarted = true;
                    roadmapContainer.classList.add('dragging');
                    roadmapContainer.setPointerCapture(e.pointerId);
                }
                roadmapContainer.scrollLeft = scrollLeft - dx;
                roadmapContainer.scrollTop = scrollTop - dy;
            };

            const endDrag = (e) => {
                if (!isDragging) return;
                isDragging = false;
                if (dragStarted) {
                    roadmapContainer.classList.remove('dragging');
                    try { roadmapContainer.releasePointerCapture(e.pointerId); } catch (_) {}
                }
                dragStarted = false;
            };

            roadmapContainer.addEventListener('pointerdown', onPointerDown);
            roadmapContainer.addEventListener('pointermove', onPointerMove);
            roadmapContainer.addEventListener('pointerup', endDrag);
            roadmapContainer.addEventListener('pointercancel', endDrag);
            roadmapContainer.addEventListener('pointerleave', endDrag);
        }

        function isRoadmapFullscreen() {
            return document.fullscreenElement === roadmapSurface || (roadmapSurface && roadmapSurface.classList.contains('is-fullscreen'));
        }

        function updateRoadmapFullscreenButton() {
            if (!roadmapFullscreenBtn) return;
            const active = isRoadmapFullscreen();
            const label = active
                ? <?= tfSafeJson(t('editor_exit_fullscreen', 'Exit fullscreen'), JSON_UNESCAPED_UNICODE) ?>
                : <?= tfSafeJson(t('editor_fullscreen', 'Fullscreen'), JSON_UNESCAPED_UNICODE) ?>;
            roadmapFullscreenBtn.setAttribute('aria-label', label);
            roadmapFullscreenBtn.setAttribute('title', label);
            roadmapFullscreenBtn.innerHTML = active
                ? '<i class="fas fa-compress"></i>'
                : '<i class="fas fa-expand"></i>';
        }

        async function toggleRoadmapFullscreen() {
            if (!roadmapSurface) return;
            try {
                if (document.fullscreenElement === roadmapSurface) {
                    await document.exitFullscreen();
                } else if (document.fullscreenElement) {
                    await document.exitFullscreen();
                    await roadmapSurface.requestFullscreen();
                } else if (roadmapSurface.requestFullscreen) {
                    await roadmapSurface.requestFullscreen();
                } else {
                    roadmapSurface.classList.toggle('is-fullscreen');
                    document.body.classList.toggle('roadmap-fullscreen-open', roadmapSurface.classList.contains('is-fullscreen'));
                    updateRoadmapFullscreenButton();
                }
            } catch (err) {
                roadmapSurface.classList.toggle('is-fullscreen');
                document.body.classList.toggle('roadmap-fullscreen-open', roadmapSurface.classList.contains('is-fullscreen'));
                updateRoadmapFullscreenButton();
            }
        }

        document.addEventListener('fullscreenchange', () => {
            if (!roadmapSurface) return;
            const active = document.fullscreenElement === roadmapSurface;
            roadmapSurface.classList.toggle('is-fullscreen', active);
            document.body.classList.toggle('roadmap-fullscreen-open', active);
            updateRoadmapFullscreenButton();
        });

        if (roadmapFullscreenBtn) {
            roadmapFullscreenBtn.addEventListener('click', toggleRoadmapFullscreen);
            updateRoadmapFullscreenButton();
        }

        function tfFormat(template, vars = {}) {
            return String(template || '').replace(/\{(\w+)\}/g, (_, key) => (vars[key] ?? `{${key}}`));
        }

        function notify(text) {
            if (window.tfNotify) return tfNotify(text);
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
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
            canvas.width = window.innerWidth * dpr;
            canvas.height = window.innerHeight * dpr;
            ctx.scale(dpr, dpr);

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

        function getNodeDeps(node) {
            if (!node.deps) return [];
            if (Array.isArray(node.deps)) return node.deps.map(n => parseInt(n, 10)).filter(Boolean);
            try {
                return JSON.parse(node.deps).map(n => parseInt(n, 10)).filter(Boolean);
            } catch (e) {
                return [];
            }
        }

        function isExamNode(node) {
            return parseInt(node.is_exam, 10) === 1;
        }

        function getNodeState(nodeId) {
            const key = String(nodeId);
            return appData.states && appData.states[key]
                ? appData.states[key]
                : { lesson_done: 0, quiz_score: 0, quiz_total: 0, completed: false, legacy: false };
        }

        function isNodeCompleted(nodeId) {
            const state = getNodeState(nodeId);
            return !!state.completed;
        }

        function isExamUnlocked(node) {
            if (!isExamNode(node)) return true;
            const list = appData.roadmapNodes && appData.roadmapNodes.length
                ? appData.roadmapNodes
                : (appData.filteredNodes && appData.filteredNodes.length ? appData.filteredNodes : appData.nodes);
            const requiredIds = list
                .filter(n => !isExamNode(n))
                .map(n => parseInt(n.id, 10))
                .filter(Boolean);
            if (!requiredIds.length) return true;
            return requiredIds.every(id => isNodeCompleted(id));
        }

        function getNodeStatus(node) {
            const id = parseInt(node.id, 10);
            const state = getNodeState(id);
            const isDone = !!state.completed;
            const deps = getNodeDeps(node);
            const depsOk = deps.length === 0 || deps.every(depId => isNodeCompleted(parseInt(depId, 10)));
            const canOpen = depsOk && (!isExamNode(node) || isExamUnlocked(node));
            const inProgress = !isDone && (state.lesson_done || state.quiz_total > 0);
            return { isDone, canOpen, inProgress };
        }

        function getNodeTopic(node) {
            if (node.topic && String(node.topic).trim()) return node.topic;
            const lesson = (node.lessons || [])[0];
            if (lesson?.title) return lesson.title;
            return tfI18n.topic_not_set;
        }

        function normalizeMaterials(raw) {
            if (!Array.isArray(raw)) return [];
            return raw.filter(Boolean).map(item => {
                if (typeof item === 'string') return { title: item, url: '' };
                return { title: item.title || tfI18n.material, url: item.url || '' };
            });
        }

        function getNodeMaterials(node) {
            const nodeMats = normalizeMaterials(node.materials || []);
            const lessonMats = (node.lessons || []).flatMap(lesson => normalizeMaterials(lesson.materials || []));
            const all = [...nodeMats, ...lessonMats];
            return all.length ? all : [];
        }

        function getQuizQuestions(node) {
            const questions = Array.isArray(node.quiz_questions) ? node.quiz_questions : [];
            return questions.map(q => ({
                question: q.question || '',
                options: Array.isArray(q.options) ? q.options : []
            })).filter(q => q.question && q.options.length);
        }

        function getMiniTestQuestions(node) {
            return getQuizQuestions(node).slice(0, 5);
        }

        function getExamQuestions(node) {
            return getQuizQuestions(node).slice(0, 30);
        }

        function layoutNodes(nodes) {
            const withCoords = nodes.filter(n => {
                const nx = Number(n.x || 0);
                const ny = Number(n.y || 0);
                return nx !== 0 || ny !== 0;
            });
            let useCoords = withCoords.length === nodes.length;
            if (useCoords) {
                const coordSet = new Set();
                for (const node of nodes) {
                    const key = `${node.x || 0},${node.y || 0}`;
                    if (coordSet.has(key)) {
                        useCoords = false;
                        break;
                    }
                    coordSet.add(key);
                }
            }
            if (useCoords) {
                const coords = nodes.map(n => ({ x: Number(n.x || 0), y: Number(n.y || 0) }));
                for (let i = 0; i < coords.length && useCoords; i++) {
                    for (let j = i + 1; j < coords.length; j++) {
                        const dx = Math.abs(coords[i].x - coords[j].x);
                        const dy = Math.abs(coords[i].y - coords[j].y);
                        if (dx < (NODE_WIDTH + 40) && dy < (NODE_HEIGHT + 40)) {
                            useCoords = false;
                            break;
                        }
                    }
                }
            }
            if (useCoords) {
                const positioned = nodes.map(n => ({
                    ...n,
                    _x: Number(n.x || 0),
                    _y: Number(n.y || 0)
                }));
                const minX = positioned.reduce((acc, n) => Math.min(acc, n._x), 0);
                const minY = positioned.reduce((acc, n) => Math.min(acc, n._y), 0);
                positioned.forEach(n => {
                    n._x = n._x - minX;
                    n._y = n._y - minY;
                });
                const maxX = positioned.reduce((acc, n) => Math.max(acc, n._x), 0);
                const maxY = positioned.reduce((acc, n) => Math.max(acc, n._y), 0);
                const mapWidth = Math.max(900, maxX + NODE_WIDTH + MAP_PADDING * 2);
                const mapHeight = Math.max(600, maxY + NODE_HEIGHT + MAP_PADDING * 2);

                const svgLayer = document.getElementById('roadmap-svg-layer');
                const nodesLayer = document.getElementById('roadmap-nodes-layer');
                svgLayer.style.width = `${mapWidth}px`;
                svgLayer.style.height = `${mapHeight}px`;
                nodesLayer.style.width = `${mapWidth}px`;
                nodesLayer.style.height = `${mapHeight}px`;

                return positioned;
            }
            const byId = new Map(nodes.map(n => [parseInt(n.id, 10), n]));
            const levelCache = new Map();

            function getLevel(node, stack = new Set()) {
                const id = parseInt(node.id, 10);
                if (levelCache.has(id)) return levelCache.get(id);
                if (stack.has(id)) return 0;
                stack.add(id);
                const deps = getNodeDeps(node);
                let level = 0;
                if (deps.length) {
                    const depLevels = deps.map(depId => {
                        const dep = byId.get(parseInt(depId, 10));
                        return dep ? getLevel(dep, new Set(stack)) : 0;
                    });
                    level = Math.max(...depLevels) + 1;
                }
                levelCache.set(id, level);
                return level;
            }

            const withLevel = nodes.map(n => ({ ...n, _level: getLevel(n) }));
            const levels = new Map();
            withLevel.forEach(n => {
                const level = n._level || 0;
                if (!levels.has(level)) levels.set(level, []);
                levels.get(level).push(n);
            });

            levels.forEach(list => list.sort((a, b) => parseInt(a.id, 10) - parseInt(b.id, 10)));

            const positioned = [];
            levels.forEach((list, level) => {
                list.forEach((node, idx) => {
                    const x = level * (NODE_WIDTH + NODE_GAP_X);
                    const y = idx * (NODE_HEIGHT + NODE_GAP_Y);
                    positioned.push({ ...node, _x: x, _y: y });
                });
            });

            const maxX = positioned.reduce((acc, n) => Math.max(acc, n._x), 0);
            const maxY = positioned.reduce((acc, n) => Math.max(acc, n._y), 0);
            const mapWidth = Math.max(900, maxX + NODE_WIDTH + MAP_PADDING * 2);
            const mapHeight = Math.max(600, maxY + NODE_HEIGHT + MAP_PADDING * 2);

            const svgLayer = document.getElementById('roadmap-svg-layer');
            const nodesLayer = document.getElementById('roadmap-nodes-layer');
            svgLayer.style.width = `${mapWidth}px`;
            svgLayer.style.height = `${mapHeight}px`;
            nodesLayer.style.width = `${mapWidth}px`;
            nodesLayer.style.height = `${mapHeight}px`;

            return positioned;
        }

        function drawConnections(nodes) {
            const svg = document.getElementById('roadmap-svg-layer');
            svg.innerHTML = '';
            const byId = new Map(nodes.map(n => [parseInt(n.id, 10), n]));
            nodes.forEach(node => {
                const deps = getNodeDeps(node);
                deps.forEach(depId => {
                    const dep = byId.get(parseInt(depId, 10));
                    if (!dep) return;
                    const x1 = dep._x + NODE_WIDTH / 2 + MAP_PADDING;
                    const y1 = dep._y + NODE_HEIGHT / 2 + MAP_PADDING;
                    const x2 = node._x + NODE_WIDTH / 2 + MAP_PADDING;
                    const y2 = node._y + NODE_HEIGHT / 2 + MAP_PADDING;
                    const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                    line.setAttribute('x1', x1);
                    line.setAttribute('y1', y1);
                    line.setAttribute('x2', x2);
                    line.setAttribute('y2', y2);
                    const status = getNodeStatus(node);
                    if (status.isDone) line.classList.add('roadmap-line', 'completed');
                    else if (status.canOpen) line.classList.add('roadmap-line', 'unlocked');
                    else line.classList.add('roadmap-line');
                    svg.appendChild(line);
                });
            });
        }

        function renderNodes(nodes) {
            const layer = document.getElementById('roadmap-nodes-layer');
            layer.innerHTML = '';
            const totalCount = appData.filteredNodes && appData.filteredNodes.length ? appData.filteredNodes.length : nodes.length;
            document.getElementById('nodes-count').innerText = tfFormat(tfI18n.nodes_count, { count: totalCount });

            if (!nodes.length) {
                layer.innerHTML = `<div class="text-slate-500 p-6">${tfI18n.nodes_not_found}</div>`;
                return;
            }

            const positioned = layoutNodes(nodes);
            drawConnections(positioned);

            positioned.forEach((node, index) => {
                const status = getNodeStatus(node);
                const topicRaw = String(getNodeTopic(node) || '').trim();
                const titleRaw = String(node.title || '').trim();
                const topic = topicRaw !== '' && topicRaw !== titleRaw ? topicRaw : '';
                const card = document.createElement('button');
                card.type = 'button';
                card.dataset.nodeId = node.id;
                card.style.left = `${node._x + MAP_PADDING}px`;
                card.style.top = `${node._y + MAP_PADDING}px`;
                card.style.width = `${NODE_WIDTH}px`;
                card.style.height = `${NODE_HEIGHT}px`;
                card.className = `absolute text-left rounded-2xl border p-4 shadow-sm transition-all overflow-hidden ${status.isDone
                        ? 'border-emerald-300 bg-emerald-50'
                        : status.inProgress
                            ? 'border-amber-200 bg-amber-50'
                            : status.canOpen
                                ? 'border-sky-200 bg-white hover:border-sky-300'
                                : 'border-slate-200 bg-slate-100 opacity-70'
                    }`;
                card.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] uppercase tracking-wide text-slate-400">${tfI18n.module}</div>
                        <div class="text-base font-semibold text-slate-900 node-title-clamp">${node.title}</div>
                        ${topic ? `<div class="text-sm text-slate-500 mt-1 node-topic-clamp">${topic}</div>` : ''}
                    </div>
                    <span class="text-[10px] px-2 py-1 rounded-full font-semibold ${status.isDone
                        ? 'bg-emerald-100 text-emerald-700'
                        : status.inProgress
                            ? 'bg-amber-100 text-amber-700'
                            : status.canOpen
                                ? 'bg-sky-100 text-sky-700'
                                : 'bg-slate-200 text-slate-600'
                    }">${status.isDone ? tfI18n.done : status.inProgress ? tfI18n.in_progress : status.canOpen ? tfI18n.available : tfI18n.locked}</span>
                </div>
            `;

                card.classList.remove('tf-fade');
                void card.offsetWidth;
                card.classList.add('tf-fade');
                card.addEventListener('click', () => openNode(node));
                layer.appendChild(card);
            });
        }

        function renderRoadmap() {
            const base = appData.roadmapNodes && appData.roadmapNodes.length ? appData.roadmapNodes : appData.nodes;
            appData.filteredNodes = base;
            renderNodes(base);
            updateProgressHeader();
        }

        function openNode(node) {
            const status = getNodeStatus(node);
            if (!status.canOpen && !status.isDone) {
                notify(tfI18n.module_locked_message);
                return;
            }
            currentNode = node;
            document.getElementById('lesson-modal').classList.remove('hidden');
            document.getElementById('lesson-modal').classList.add('flex');
            renderModal(node);
        }

        function renderModal(node) {
            document.getElementById('lesson-title').innerText = node.title || '';
            document.getElementById('lesson-topic').innerText = getNodeTopic(node) || tfI18n.topic_not_set;
            const lesson = (node.lessons || [])[0] || {};
            document.getElementById('lesson-description').innerText = lesson.description || tfI18n.description_missing;
            const videoLink = document.getElementById('lesson-video-link');
            if (lesson.video_url) {
                videoLink.href = lesson.video_url;
                videoLink.textContent = tfI18n.open_lecture_video;
                videoLink.classList.remove('hidden');
            } else {
                videoLink.href = '#';
                videoLink.textContent = tfI18n.video_missing;
                videoLink.classList.add('hidden');
            }

            const materials = getNodeMaterials(node);
            const materialsWrap = document.getElementById('lesson-materials');
            materialsWrap.innerHTML = materials.length
                ? materials.map(m => m.url
                    ? `<li><a class="text-sky-600 hover:underline" href="${escapeHtml(m.url)}" target="_blank" rel="noopener">${escapeHtml(m.title)}</a></li>`
                    : `<li>${escapeHtml(m.title)}</li>`).join('')
                : `<li class="text-slate-400">${tfI18n.no_materials}</li>`;

            const state = getNodeState(node.id);
            const completeBtn = document.getElementById('lesson-complete-btn');
            const completeStatus = document.getElementById('lesson-complete-status');
            const miniSection = document.getElementById('mini-test-section');
            const miniSubmitBtn = document.getElementById('mini-test-submit-btn');
            const examSection = document.getElementById('exam-section');
            const examSubmitBtn = document.getElementById('exam-submit-btn');
            const lectureSection = document.getElementById('lesson-lecture-section');
            const isExam = isExamNode(node);
            const lessonDone = !!state.lesson_done || !!state.completed;
            completeStatus.textContent = lessonDone ? tfI18n.materials_done : tfI18n.quiz_locked_materials;
            completeStatus.className = lessonDone ? 'text-xs text-emerald-600' : 'text-xs text-slate-500';
            completeBtn.disabled = lessonDone;
            completeBtn.classList.toggle('opacity-60', lessonDone);
            completeBtn.classList.toggle('cursor-not-allowed', lessonDone);
            completeBtn.onclick = async () => {
                const data = await saveProgress(node.id, { stage: 'lesson' });
                if (data && data.success) {
                    completeStatus.textContent = tfI18n.materials_done;
                    completeStatus.className = 'text-xs text-emerald-600';
                    completeBtn.disabled = true;
                    completeBtn.classList.add('opacity-60', 'cursor-not-allowed');
                    if (miniSection) miniSection.classList.remove('hidden');
                    if (miniSubmitBtn) {
                        miniSubmitBtn.disabled = false;
                        miniSubmitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                    }
                    renderMiniTest(node);
                }
            };

            if (isExam) {
                if (lectureSection) lectureSection.classList.add('hidden');
                if (miniSection) miniSection.classList.add('hidden');
                if (miniSubmitBtn) {
                    miniSubmitBtn.disabled = true;
                    miniSubmitBtn.classList.add('opacity-60', 'cursor-not-allowed', 'hidden');
                }
                if (examSection) examSection.classList.remove('hidden');
                if (examSubmitBtn) {
                    examSubmitBtn.classList.remove('hidden');
                    examSubmitBtn.disabled = false;
                }
                renderExam(node);
                return;
            }

            if (lectureSection) lectureSection.classList.remove('hidden');
            if (examSection) examSection.classList.add('hidden');
            if (examSubmitBtn) {
                examSubmitBtn.disabled = true;
                examSubmitBtn.classList.add('opacity-60', 'cursor-not-allowed', 'hidden');
            }
            if (miniSection) miniSection.classList.toggle('hidden', !lessonDone);
            if (miniSubmitBtn) {
                miniSubmitBtn.disabled = !lessonDone;
                miniSubmitBtn.classList.toggle('opacity-60', !lessonDone);
                miniSubmitBtn.classList.toggle('cursor-not-allowed', !lessonDone);
                miniSubmitBtn.classList.remove('hidden');
            }
            if (lessonDone) renderMiniTest(node);
        }

        function renderMiniTest(node) {
            const questions = getMiniTestQuestions(node);
            const miniTest = document.getElementById('mini-test');
            const result = document.getElementById('mini-test-result');
            result.textContent = '';
                        const miniState = {
                questions,
                answers: Array(questions.length).fill(''),
                current: 0
            };

            miniTest.innerHTML = `
            <div class="mb-3 text-xs text-slate-500">${tfI18n.quiz_title}: ${questions.length}</div>
            <div id="mini-nav" class="grid grid-cols-8 gap-2 mb-4"></div>
            <div id="mini-question-card" class="exam-question exam-question-card space-y-2 border border-slate-200 rounded-xl p-3 bg-white"></div>
            <div class="mt-3 flex items-center justify-between gap-2">
                <button type="button" id="mini-prev-btn" class="btn-ghost px-3 py-2 text-sm">< ${tfI18n.pagination_prev}</button>
                <div id="mini-current-label" class="text-xs text-slate-500"></div>
                <button type="button" id="mini-next-btn" class="btn-ghost px-3 py-2 text-sm">${tfI18n.pagination_next} ></button>
            </div>
        `;

            const navWrap = document.getElementById('mini-nav');
            const card = document.getElementById('mini-question-card');
            const currentLabel = document.getElementById('mini-current-label');
            const prevBtn = document.getElementById('mini-prev-btn');
            const nextBtn = document.getElementById('mini-next-btn');

            const renderMiniNav = () => {
                navWrap.innerHTML = miniState.questions.map((_, idx) => {
                    const answered = String(miniState.answers[idx] || '').trim() !== '';
                    const active = idx === miniState.current;
                    const cls = active
                        ? 'border-sky-500 bg-sky-100 text-sky-800'
                        : answered
                            ? 'border-emerald-300 bg-emerald-50 text-emerald-700'
                            : 'border-slate-200 bg-white text-slate-600';
                    return `<button type="button" data-mini-nav="${idx}" class="h-8 rounded-md border text-xs font-semibold ${cls}">${idx + 1}</button>`;
                }).join('');
                navWrap.querySelectorAll('[data-mini-nav]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        miniState.current = parseInt(btn.getAttribute('data-mini-nav') || '0', 10);
                        renderMiniQuestion();
                    });
                });
            };

            const renderMiniQuestion = () => {
                const idx = miniState.current;
                const q = miniState.questions[idx];
                currentLabel.textContent = `${idx + 1}/${miniState.questions.length}`;
                prevBtn.disabled = idx <= 0;
                nextBtn.disabled = idx >= miniState.questions.length - 1;
                prevBtn.classList.toggle('opacity-50', prevBtn.disabled);
                nextBtn.classList.toggle('opacity-50', nextBtn.disabled);

                card.innerHTML = `
                <div class="text-sm font-semibold">${idx + 1}. ${escapeHtml(q.question)}</div>
                <div class="grid gap-2 mt-2">
                    ${q.options.map((opt, optIdx) => `
                        <div class="quiz-option">
                            <input id="mini-${idx}-${optIdx}" type="radio" name="mini-test-${idx}" value="${escapeHtml(opt)}" class="hidden" ${miniState.answers[idx] === String(opt) ? 'checked' : ''}>
                            <label for="mini-${idx}-${optIdx}" class="quiz-option-label variant-${optIdx % 4} block border border-slate-200 rounded-lg px-3 py-2 cursor-pointer">
                                <span class="quiz-option-key quiz-option-key-${optIdx % 4}">${String.fromCharCode(65 + optIdx)}</span>
                                <span class="quiz-option-text">${escapeHtml(opt)}</span>
                            </label>
                        </div>
                    `).join('')}
                </div>
            `;

                card.classList.remove('tf-fade');
                void card.offsetWidth;
                card.classList.add('tf-fade');

                card.classList.remove('tf-fade');
                void card.offsetWidth;
                card.classList.add('tf-fade');
                card.querySelectorAll(`input[name="mini-test-${idx}"]`).forEach(input => {
                    input.addEventListener('change', () => {
                        miniState.answers[idx] = input.value;
                        renderMiniNav();
                    });
                });
                renderMiniNav();
            };

            prevBtn.addEventListener('click', () => {
                if (miniState.current > 0) {
                    miniState.current -= 1;
                    renderMiniQuestion();
                }
            });
            nextBtn.addEventListener('click', () => {
                if (miniState.current < miniState.questions.length - 1) {
                    miniState.current += 1;
                    renderMiniQuestion();
                }
            });
            renderMiniQuestion();

            const miniSubmitBtn = document.getElementById('mini-test-submit-btn');
            if (!miniSubmitBtn) {
                return;
            }

            miniSubmitBtn.onclick = async () => {
                tfConfettiBurst(0.4);
                const answers = miniState.answers.slice();
                if (answers.some(v => String(v || '').trim() === '')) {
                    result.textContent = tfI18n.fill_all_answers;
                    result.className = 'mt-3 text-sm text-rose-500';
                    return;
                }
                const data = await saveProgress(node.id, {
                    stage: 'quiz',
                    answers
                });
                if (data && data.success) {
                    const score = typeof data.score === 'number' ? data.score : questions.length;
                    const total = typeof data.total === 'number' ? data.total : questions.length;
                    result.textContent = tfFormat(tfI18n.quiz_success, { score, total });
                    result.className = 'mt-3 text-sm text-emerald-600';
                    tfConfettiBurst();
                    renderRoadmap();
                    return;
                }
                if (data && typeof data.score === 'number' && typeof data.total === 'number') {
                    result.textContent = tfFormat(tfI18n.quiz_fail, { score: data.score, total: data.total });
                    result.className = 'mt-3 text-sm text-rose-500';
                    return;
                }
            };
        }

        function renderExam(node) {
            const questions = getExamQuestions(node);
            const examWrap = document.getElementById('exam-form');
            const result = document.getElementById('exam-result');
            result.textContent = '';
            examWrap.innerHTML = '';

            const examBtn = document.getElementById('exam-submit-btn');
            if (node.is_exam != 1) {
                examWrap.innerHTML = `<div class="text-slate-400 text-sm">${tfI18n.exam_only_node}</div>`;
                examBtn.disabled = true;
                examBtn.classList.add('opacity-50', 'cursor-not-allowed');
                return;
            }

            if (!isExamUnlocked(node)) {
                examWrap.innerHTML = `<div class="text-slate-400 text-sm">${tfI18n.exam_unlock_message}</div>`;
                examBtn.disabled = true;
                examBtn.classList.add('opacity-50', 'cursor-not-allowed');
                return;
            }

            examBtn.disabled = false;
            examBtn.classList.remove('opacity-50', 'cursor-not-allowed');

            if (!questions.length) {
                examWrap.innerHTML = `<div class="text-slate-400 text-sm">${tfI18n.quiz_not_found}</div>`;
                examBtn.disabled = true;
                examBtn.classList.add('opacity-50', 'cursor-not-allowed');
                return;
            }

            const examState = {
                questions,
                answers: Array(questions.length).fill(''),
                current: 0
            };
            examWrap.innerHTML = `
            <div class="mb-3 text-xs text-slate-500">ЭкР·амРµн: ${questions.length} вРѕпрРѕсРѕв</div>
            <div id="exam-nav" class="grid grid-cols-8 gap-2 mb-4"></div>
            <div id="exam-question-card" class="exam-question exam-question-card space-y-2 border border-slate-200 rounded-xl p-3 bg-white"></div>
            <div class="mt-3 flex items-center justify-between gap-2">
                <button type="button" id="exam-prev-btn" class="btn-ghost px-3 py-2 text-sm">< ${tfI18n.prev || 'Prev'}</button>
                <div id="exam-current-label" class="text-xs text-slate-500"></div>
                <button type="button" id="exam-next-btn" class="btn-ghost px-3 py-2 text-sm">${tfI18n.next || 'Next'} ></button>
            </div>
        `;

            const navWrap = document.getElementById('exam-nav');
            const card = document.getElementById('exam-question-card');
            const currentLabel = document.getElementById('exam-current-label');
            const prevBtn = document.getElementById('exam-prev-btn');
            const nextBtn = document.getElementById('exam-next-btn');

            const renderExamNav = () => {
                navWrap.innerHTML = examState.questions.map((_, idx) => {
                    const answered = String(examState.answers[idx] || '').trim() !== '';
                    const active = idx === examState.current;
                    const cls = active
                        ? 'border-sky-500 bg-sky-100 text-sky-800'
                        : answered
                            ? 'border-emerald-300 bg-emerald-50 text-emerald-700'
                            : 'border-slate-200 bg-white text-slate-600';
                    return `<button type="button" data-exam-nav="${idx}" class="h-8 rounded-md border text-xs font-semibold ${cls}">${idx + 1}</button>`;
                }).join('');
                navWrap.querySelectorAll('[data-exam-nav]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        examState.current = parseInt(btn.getAttribute('data-exam-nav') || '0', 10);
                        renderExamQuestion();
                    });
                });
            };

            const renderExamQuestion = () => {
                const idx = examState.current;
                const q = examState.questions[idx];
                currentLabel.textContent = `${idx + 1}/${examState.questions.length}`;
                prevBtn.disabled = idx <= 0;
                nextBtn.disabled = idx >= examState.questions.length - 1;
                prevBtn.classList.toggle('opacity-50', prevBtn.disabled);
                nextBtn.classList.toggle('opacity-50', nextBtn.disabled);

                card.innerHTML = `
                <div class="text-sm font-semibold">${idx + 1}. ${escapeHtml(q.question)}</div>
                <div class="grid gap-2 mt-2">
                    ${q.options.map((opt, optIdx) => `
                        <div class="quiz-option">
                            <input id="exam-${idx}-${optIdx}" type="radio" name="exam-${idx}" value="${escapeHtml(opt)}" class="hidden" ${examState.answers[idx] === String(opt) ? 'checked' : ''}>
                            <label for="exam-${idx}-${optIdx}" class="quiz-option-label variant-${optIdx % 4} block border border-slate-200 rounded-lg px-3 py-2 cursor-pointer"><span class="quiz-option-key quiz-option-key-${optIdx % 4}">${String.fromCharCode(65 + optIdx)}</span><span class="quiz-option-text">${escapeHtml(opt)}</span></label>
                        </div>
                    `).join('')}
                </div>
            `;

                card.classList.remove('tf-fade');
                void card.offsetWidth;
                card.classList.add('tf-fade');
                card.querySelectorAll(`input[name="exam-${idx}"]`).forEach(input => {
                    input.addEventListener('change', () => {
                        examState.answers[idx] = input.value;
                        renderExamNav();
                    });
                });
                renderExamNav();
            };
            prevBtn.addEventListener('click', () => {
                if (examState.current > 0) {
                    examState.current -= 1;
                    renderExamQuestion();
                }
            });
            nextBtn.addEventListener('click', () => {
                if (examState.current < examState.questions.length - 1) {
                    examState.current += 1;
                    renderExamQuestion();
                }
            });
            renderExamQuestion();

            examBtn.onclick = async () => {
                tfConfettiBurst(0.4);
                const answers = examState.answers.slice();
                if (answers.some(v => String(v || '').trim() === '')) {
                    result.textContent = tfI18n.fill_all_answers;
                    result.className = 'mt-3 text-sm text-rose-500';
                    return;
                }

                const data = await saveProgress(node.id, {
                    stage: 'exam',
                    answers
                });
                if (data && data.success) {
                    const score = typeof data.score === 'number' ? data.score : questions.length;
                    const total = typeof data.total === 'number' ? data.total : questions.length;
                    result.textContent = tfFormat(tfI18n.quiz_success, { score, total });
                    result.className = 'mt-3 text-sm text-emerald-600';
                    tfConfettiBurst();
                    await issueCertificate(node.id);
                    return;
                }
                if (data && typeof data.score === 'number' && typeof data.total === 'number') {
                    result.textContent = tfFormat(tfI18n.quiz_fail, { score: data.score, total: data.total });
                    result.className = 'mt-3 text-sm text-rose-500';
                }
            };
        }

        function upsertClientNodeState(nodeId, statePatch = {}) {
            const key = String(parseInt(nodeId, 10));
            const base = appData.states[key] || { lesson_done: 0, quiz_score: 0, quiz_total: 0, completed: false, legacy: false };
            appData.states[key] = { ...base, ...statePatch };
        }

        async function saveProgress(nodeId, payload = {}) {
            try {
                const res = await fetch('?action=roadmap-save-progress', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ node_id: nodeId, ...payload })
                });
                const data = await res.json();
                if (data && data.state && typeof data.state === 'object') {
                    upsertClientNodeState(nodeId, data.state);
                }
                if (data.success) {
                    if (data.completed !== false && !appData.progress.includes(parseInt(nodeId, 10))) {
                        appData.progress.push(parseInt(nodeId, 10));
                    }
                    updateProgressHeader();
                    renderRoadmap();
                    return data;
                }
                if (data && data.message) {
                    notify(data.message);
                }
                renderRoadmap();
                return data;
            } catch (e) {
                notify(tfI18n.server_connection_error);
            }
            return null;
        }

        async function issueCertificate(nodeId) {
            try {
                const res = await fetch('?action=roadmap-issue-certificate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ node_id: nodeId })
                });
                const data = await res.json();
                if (data.success) showCert(data.cert_hash);
            } catch (e) {
                notify(tfI18n.server_connection_error);
            }
        }

        function tfHappyEnd() {
            const title = tfI18n.happy_title || 'Congrats!';
            const subtitle = tfI18n.happy_subtitle || '';
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

        function showCert(hash) {
            document.getElementById('lesson-modal').classList.add('hidden');
            document.getElementById('lesson-modal').classList.remove('flex');
            document.getElementById('cert-date').innerText = new Date().toLocaleDateString();
            document.getElementById('cert-course').innerText = currentNode?.title || tfI18n.roadmap;
            tfHappyEnd();
            document.getElementById('cert-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('lesson-modal').classList.add('hidden');
            document.getElementById('lesson-modal').classList.remove('flex');
        }

        function updateProgressHeader() {
            const baseList = appData.roadmapNodes && appData.roadmapNodes.length ? appData.roadmapNodes : appData.nodes;
            const total = baseList.length;
            const done = baseList.filter(n => appData.progress.includes(parseInt(n.id, 10))).length;
            const perc = total ? Math.round((done / total) * 100) : 0;
            document.getElementById('progress-text').innerText = perc + '%';
            document.getElementById('progress-bar').style.width = perc + '%';
        }

        async function init() {
            try {
                const params = new URLSearchParams({ action: 'roadmap-data', view: 'roadmap' });
                if (roadmapTitle) params.set('roadmap_title', roadmapTitle);
                const res = await fetch(`?${params.toString()}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                if (!data.success) throw new Error(data.message || tfI18n.load_error);
                appData.nodes = data.nodes || [];
                appData.progress = (data.progress || []).map(id => parseInt(id, 10));
                appData.states = (data.states && typeof data.states === 'object') ? data.states : {};
                appData.roadmaps = data.roadmaps || [];

                const title = roadmapTitle
                    || data.active_roadmap
                    || (appData.roadmaps[0]?.title || '')
                    || (appData.nodes[0]?.roadmap_title || tfI18n.default_roadmap);
                const filtered = appData.nodes.filter(n => (n.roadmap_title || tfI18n.default_roadmap) === title);
                appData.roadmapNodes = filtered.length ? filtered : appData.nodes;
                appData.filteredNodes = appData.roadmapNodes;

                document.getElementById('active-roadmap-title').innerText = title || tfI18n.roadmap;
                const activeRoadmap = appData.roadmaps.find(r => String(r.title || '').trim() === String(title || '').trim());
                document.getElementById('active-roadmap-meta').innerText = (activeRoadmap && activeRoadmap.description)
                    ? String(activeRoadmap.description)
                    : tfFormat(tfI18n.meta_id, { id: title });
                renderRoadmap();
            } catch (e) {
                notify(tfI18n.load_error);
                console.error(e);
            }
        }

        init();
    </script>
</body>

</html>
