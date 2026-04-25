<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$interviewProblems = $interviewProblems ?? [];
$trendingCompanies = $trendingCompanies ?? [];

$problemsPayload = [];
foreach ($interviewProblems as $problem) {
    $problemsPayload[] = [
        'id' => (int) ($problem['id'] ?? 0),
        'title' => (string) ($problem['title'] ?? ''),
        'difficulty' => (string) ($problem['difficulty'] ?? 'Easy'),
        'category' => (string) ($problem['category'] ?? ''),
        'statement' => (string) ($problem['statement'] ?? ''),
        'input' => (string) ($problem['input'] ?? ''),
        'output' => (string) ($problem['output'] ?? ''),
        'starter' => [
            'cpp' => (string) ($problem['starter_cpp'] ?? ''),
            'python' => (string) ($problem['starter_python'] ?? ''),
            'c' => (string) ($problem['starter_c'] ?? ''),
            'csharp' => (string) ($problem['starter_csharp'] ?? ''),
            'java' => (string) ($problem['starter_java'] ?? ''),
            'javascript' => '',
            'typescript' => '',
            'go' => '',
            'rust' => '',
            'kotlin' => '',
            'swift' => '',
            'php' => '',
            'ruby' => '',
            'scala' => '',
            'dart' => '',
            'sql' => '',
        ],
        'tests' => (array) ($problem['tests'] ?? []),
    ];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(function_exists('currentLang') ? currentLang() : 'ru') ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('courses_interview_title', 'Interview Prep - ITsphere360') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --indigo-50: #f0f5ff;
            --indigo-100: #e0e7ff;
            --indigo-200: #c7d2fe;
            --indigo-300: #a5b4fc;
            --indigo-400: #818cf8;
            --indigo-500: #6366f1;
            --indigo-600: #4f46e5;
            --indigo-700: #4338ca;
            --indigo-800: #3730a3;
            --indigo-900: #312e81;
            --bg: var(--indigo-50);
            --ink: var(--indigo-900);
            --muted: var(--indigo-700);
            --line: var(--indigo-200);
            --card: #ffffff;
            --accent: var(--indigo-600);
            --accent-strong: var(--indigo-700);
            --accent-soft: var(--indigo-100);
            --good-bg: var(--indigo-100);
            --good-text: var(--indigo-800);
            --mid-bg: var(--indigo-200);
            --mid-text: var(--indigo-800);
            --hard-bg: var(--indigo-300);
            --hard-text: var(--indigo-900);
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            background: var(--bg);
        }

        h1,
        h2,
        h3 {
            font-family: 'Inter', sans-serif;
            letter-spacing: -0.02em;
        }

        .app-shell {
            max-width: 1280px;
            margin: 0 auto;
            padding: 1.5rem 1rem 2rem;
        }

        .hero {
            background: #ffffff;
            border-radius: 1rem;
            border: 1px solid var(--line);
            color: var(--ink);
            padding: 1.25rem;
            box-shadow: 0 8px 24px rgba(49, 46, 129, 0.08);
        }

        .editor-surface {
            border-radius: 1rem;
            border: 1px solid var(--line);
            background: #ffffff;
            transition: background 0.2s ease, border-color 0.2s ease;
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .editor-surface .editor {
            background: #f9fafb;
            color: #0f172a;
            border: 1px solid var(--line);
            border-radius: 12px;
            min-height: 280px;
            white-space: pre;
        }
        .editor.editor-wrap {
            white-space: pre-wrap;
        }

        .editor-surface.editor-dark {
            background: #0b1020;
            border-color: #1f2937;
        }

        .editor-surface.editor-dark .editor {
            background: #0f172a;
            color: #e5e7eb;
            border-color: #1f2937;
        }

        .editor-surface.editor-dark .lang-btn,
        .editor-surface.editor-dark .action-btn {
            background: #111827;
            color: #e5e7eb;
            border-color: #1f2937;
        }

        .editor-surface.editor-dark .lang-btn.is-active,
        .editor-surface.editor-dark .action-btn.primary {
            background: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }
        .editor-surface.is-fullscreen {
            position: fixed;
            inset: 0;
            z-index: 9999;
            margin: 0;
            border-radius: 0;
            padding: 1.25rem;
            box-shadow: none;
        }
        body.editor-fullscreen-open {
            overflow: hidden;
        }
        .editor-surface.is-fullscreen .editor {
            min-height: 0;
            height: 100%;
            resize: none;
        }
        .editor-status {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .45rem .65rem;
            border: 1px solid var(--line);
            border-radius: .7rem;
            background: #f8fafc;
            font-size: .75rem;
        }
        .editor-status .meta {
            font-weight: 600;
            color: #64748b;
        }
        .editor-status .meta-strong {
            font-weight: 700;
            color: #334155;
        }
        .status-btn {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--indigo-800);
            border-radius: .6rem;
            padding: .25rem .5rem;
            font-size: .72rem;
            font-weight: 600;
        }
        .editor-surface.editor-dark .editor-status {
            background: #0f172a;
            border-color: #1f2937;
        }
        .editor-surface.editor-dark .editor-status .meta,
        .editor-surface.editor-dark .editor-status .meta-strong {
            color: #cbd5f5;
        }
        .editor-surface.editor-dark .status-btn {
            background: #111827;
            color: #e5e7eb;
            border-color: #1f2937;
        }

        .hero-title {
            font-size: clamp(1.55rem, 3.2vw, 2.3rem);
            font-weight: 700;
            line-height: 1.1;
            margin: .45rem 0;
        }

        .hero-sub {
            color: var(--indigo-700);
            max-width: 62ch;
        }

        .hero-badges {
            display: flex;
            gap: .55rem;
            flex-wrap: wrap;
            margin-top: .95rem;
        }

        .hero-badge {
            font-size: .78rem;
            padding: .3rem .62rem;
            border-radius: 999px;
            background: var(--indigo-50);
            border: 1px solid var(--indigo-200);
            color: var(--indigo-800);
            font-weight: 600;
        }

        .hero-cta {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            text-decoration: none;
            padding: .65rem .95rem;
            border-radius: .78rem;
            font-weight: 600;
            border: 1px solid var(--accent);
            background: var(--accent);
            color: #ffffff;
            transition: transform .2s ease, background .2s ease;
        }

        .hero-cta:hover {
            transform: translateY(-1px);
            background: var(--accent-strong);
        }

        .surface {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 1rem;
            box-shadow: 0 6px 20px rgba(49, 46, 129, 0.06);
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .8rem;
            margin-bottom: .95rem;
        }

        .subtle {
            color: var(--muted);
        }

        .field {
            border: 1px solid var(--line);
            border-radius: .72rem;
            padding: .45rem .72rem;
            background: #ffffff;
            color: var(--ink);
            font-size: .9rem;
        }

        .split-col {
            min-width: 0;
        }

        .problem-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 100%;
            table-layout: fixed;
        }

        .problem-table th,
        .problem-table td {
            overflow-wrap: anywhere;
        }

        .problem-table thead th {
            text-align: left;
            color: var(--indigo-700);
            font-size: .74rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            border-bottom: 1px solid var(--line);
            padding: .7rem .45rem;
        }

        .problem-scroll {
            overflow: auto;
            border: 1px solid var(--line);
            border-radius: .8rem;
            background: #ffffff;
            max-width: 100%;
        }

        .problem-scroll .problem-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #ffffff;
        }

        .problem-row td {
            border-bottom: 1px solid var(--line);
            padding: .8rem .45rem;
            vertical-align: top;
            transition: background .2s ease;
        }

        .problem-row:hover td {
            background: var(--indigo-50);
        }

        .row-active td {
            background: var(--indigo-100);
        }

        .row-active td:first-child {
            box-shadow: inset 3px 0 0 var(--accent);
        }

        .diff-easy {
            background: var(--good-bg);
            color: var(--good-text);
        }

        .diff-medium {
            background: var(--mid-bg);
            color: var(--mid-text);
        }

        .diff-hard {
            background: var(--hard-bg);
            color: var(--hard-text);
        }

        .pill {
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            font-size: .72rem;
            font-weight: 700;
            padding: .2rem .56rem;
        }

        .workspace-copy {
            border: 1px solid var(--line);
            border-radius: .88rem;
            background: var(--indigo-50);
            padding: .88rem;
        }

        .spec-box {
            border: 1px solid var(--line);
            border-radius: .75rem;
            padding: .74rem;
            background: var(--indigo-50);
        }

        .lang-btn {
            border: 1px solid var(--line);
            border-radius: .62rem;
            background: #fff;
            color: var(--indigo-800);
            padding: .38rem .58rem;
            font-size: .8rem;
            font-weight: 600;
            transition: all .2s ease;
        }

        .lang-btn.is-active {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .lang-row {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }
        .lang-label {
            font-size: .78rem;
            color: #6b7280;
            font-weight: 600;
        }
        .lang-select {
            border: 1px solid rgba(99, 102, 241, 0.25);
            background: #fff;
            color: #111827;
            border-radius: .7rem;
            padding: .5rem .75rem;
            font-weight: 600;
            min-width: 190px;
            box-shadow: 0 10px 18px rgba(15, 23, 42, 0.08);
        }
        .editor-surface.editor-dark .lang-select {
            background: #0f172a;
            color: #e5e7eb;
            border-color: rgba(148, 163, 184, 0.4);
            box-shadow: 0 10px 18px rgba(0,0,0,0.35);
        }

        .action-btn {
            border-radius: .72rem;
            padding: .5rem .84rem;
            font-size: .83rem;
            font-weight: 600;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--indigo-800);
            transition: all .2s ease;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .action-btn.primary {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .action-btn.primary:hover {
            background: var(--accent-strong);
        }

        .codebox {
            font-family: 'JetBrains Mono', monospace;
        }

        .editor {
            width: 100%;
            height: 18.5rem;
            resize: vertical;
            border: 1px solid var(--indigo-200);
            border-radius: .78rem;
            padding: .84rem;
            font-size: .83rem;
            line-height: 1.52;
            color: var(--indigo-900);
            background: var(--indigo-50);
        }

        .editor:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .verdict {
            margin-top: .78rem;
            font-weight: 700;
            font-size: .92rem;
        }

        .verdict.pass {
            color: var(--indigo-800);
        }

        .verdict.fail {
            color: var(--indigo-900);
        }

        .test-item {
            border: 1px solid var(--line);
            border-radius: .72rem;
            background: #ffffff;
            padding: .72rem;
            font-size: .82rem;
        }

        .test-item pre {
            margin: .25rem 0 0;
            white-space: pre-wrap;
            color: var(--indigo-800);
            font-family: 'JetBrains Mono', monospace;
            font-size: .76rem;
        }

        .trend-card {
            border: 1px solid var(--line);
            border-radius: .84rem;
            background: #ffffff;
            padding: .72rem;
        }

        .trend-meta {
            font-size: .72rem;
            border-radius: 999px;
            padding: .18rem .52rem;
            font-weight: 700;
            color: var(--indigo-800);
            background: var(--accent-soft);
        }

        .focus-chip {
            border-radius: 999px;
            font-size: .68rem;
            padding: .2rem .46rem;
            font-weight: 600;
            color: var(--indigo-800);
            background: var(--indigo-100);
        }

        @media (min-width: 1200px) {
            .split-layout {
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
                gap: 1rem;
                align-items: start;
            }

            .split-col {
                display: grid;
                gap: 1rem;
                align-content: start;
            }

            .tasks-pane {
                order: 1;
            }

            .code-pane {
                order: 2;
            }

            .tasks-pane>.surface:first-child {
                display: flex;
                flex-direction: column;
                min-height: 0;
            }

            .tasks-pane .problem-scroll {
                height: clamp(14rem, 30vh, 20rem);
            }
        }

        @media (max-width: 1199px) {
            .split-layout {
                display: grid;
                gap: 1rem;
            }

            .split-col {
                display: grid;
                gap: 1rem;
            }

            .tasks-pane {
                order: 1;
                margin-top: 0;
            }

            .code-pane {
                order: 2;
            }

            .problem-scroll {
                max-height: 16rem;
            }
        }

        @media (max-width: 860px) {
            .hero {
                padding: 1rem;
            }

            .hero-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main class="app-shell">
        <section class="hero mb-4">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="hero-title"><?= t('courses_interview_heading', 'Interview Prep') ?></h1>
                    <p class="hero-sub">
                        <?= t('courses_interview_subheading', 'Practice problems and trending companies for interview focus.') ?>
                    </p>
                    <div class="hero-badges">
                        <span class="hero-badge"><i class="fas fa-terminal mr-1"></i><?= count($interviewProblems) ?>
                            <?= t('courses_interview_items', 'задач') ?></span>
                        <span class="hero-badge"><i class="fas fa-building mr-1"></i><?= count($trendingCompanies) ?>
                            <?= t('courses_interview_companies', 'компаний') ?></span>
                        <span class="hero-badge"><i class="fas fa-bolt mr-1"></i>Judge0</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="split-layout">
            <section class="split-col code-pane">
                <article class="surface editor-surface p-4 sm:p-5" id="interviewPrepEditorSurface">
                    <div class="flex items-center justify-between gap-3 flex-wrap mb-3">
                        <div class="lang-row">
                            <button id="editorFullscreenBtn" class="action-btn" type="button" aria-label="<?= t('editor_fullscreen', 'Fullscreen') ?>">
                                <i class="fas fa-expand"></i>
                            </button>
                            <select id="languageSelect" class="lang-select">
                                <option value="cpp">C++</option>
                                <option value="python">Python</option>
                                <option value="c">C</option>
                                <option value="java">Java</option>
                                <option value="javascript">JavaScript</option>
                                <option value="typescript">TypeScript</option>
                                <option value="go">Go</option>
                                <option value="rust">Rust</option>
                                <option value="csharp">C#</option>
                                <option value="kotlin">Kotlin</option>
                                <option value="swift">Swift</option>
                                <option value="php">PHP</option>
                                <option value="ruby">Ruby</option>
                                <option value="scala">Scala</option>
                                <option value="dart">Dart</option>
                                <option value="sql">SQL</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <button id="editorThemeToggle" class="action-btn" aria-label="<?= t('theme_dark', 'Dark') ?>">
                                <i class="fas fa-moon"></i>
                            </button>
                            <label for="solutionFile" class="action-btn cursor-pointer" title="<?= t('contest_upload_solution', 'Upload solution') ?>" aria-label="<?= t('contest_upload_solution', 'Upload solution') ?>">
                                <i class="fas fa-file-arrow-up"></i>
                            </label>
                            <input id="solutionFile" type="file" class="hidden" accept=".txt,.cpp,.cc,.c,.h,.hpp,.py,.java,.cs,.js,.ts,.go,.rs,.php,.rb,.kt,.swift,.scala,.dart,.sql">
                            <button id="resetCode" class="action-btn"><?= t('contest_reset', 'Сбросить код') ?></button>
                            <button id="checkCode"
                                class="action-btn primary"><?= t('courses_interview_check', 'Проверить (Judge0)') ?></button>
                        </div>
                    </div>

                    <textarea id="editor" class="editor codebox" data-no-tinymce="true" spellcheck="false"></textarea>
                    <div class="editor-status">
                        <div class="meta" id="editorLineCol">Ln 1, Col 1</div>
                        <div class="flex items-center gap-2">
                            <span class="meta-strong" id="editorSaveState"><?= t('editor_saved', 'Saved') ?></span>
                            <button id="wrapToggle" type="button" class="status-btn"><?= t('editor_wrap_off', 'Wrap: Off') ?></button>
                        </div>
                    </div>
                </article>
                <article class="surface p-4 sm:p-5 mt-4">
                    <h3 class="text-base font-bold mb-2"><?= t('contest_verdict_title', 'Tests and verdict') ?></h3>
                    <div id="judgeVerdict" class="verdict"></div>
                    <div id="judgeTests" class="space-y-2 mt-2"></div>
                </article>
            </section>

            <section class="split-col tasks-pane">
                <article class="surface p-4 sm:p-5">
                    <div class="panel-head">
                        <div>
                            <h2 class="text-xl font-bold"><?= t('courses_interview_catalog_title', 'Каталог задач') ?>
                            </h2>
                            <p class="subtle text-sm">
                                <?= t('courses_interview_catalog_sub', 'Выберите задачу и сразу переходите к коду.') ?>
                            </p>
                        </div>
                        <label class="text-sm">
                            <span class="sr-only"><?= t('courses_interview_difficulty', 'Сложность') ?></span>
                            <select id="difficultyFilter" class="field">
                                <option value="all"><?= t('courses_interview_all', 'All difficulties') ?></option>
                                <option value="easy"><?= t('difficulty_easy', 'Легкая') ?></option>
                                <option value="medium"><?= t('difficulty_medium', 'Средняя') ?></option>
                                <option value="hard"><?= t('difficulty_hard', 'Сложная') ?></option>
                            </select>
                        </label>
                    </div>

                    <div class="problem-scroll overflow-x-auto">
                        <table class="problem-table">
                            <thead>
                                <tr>
                                    <th><?= t('courses_interview_problem', 'Problem') ?></th>
                                    <th><?= t('courses_interview_difficulty', 'Difficulty') ?></th>
                                    <th><?= t('courses_interview_category', 'Category') ?></th>
                                    <th><?= t('courses_interview_acceptance', 'Acceptance') ?></th>
                                </tr>
                            </thead>
                            <tbody id="problemRows">
                                <?php
                                $seenProblemTitles = [];
                                $normalizeTitle = static function ($value): string {
                                    $title = trim((string) $value);
                                    if ($title === '') {
                                        return '';
                                    }
                                    if (function_exists('mb_strtolower')) {
                                        return mb_strtolower($title, 'UTF-8');
                                    }
                                    return strtolower($title);
                                };
                                foreach ($interviewProblems as $problem):
                                    $titleKey = $normalizeTitle($problem['title'] ?? '');
                                    if ($titleKey !== '') {
                                        if (isset($seenProblemTitles[$titleKey])) {
                                            continue;
                                        }
                                        $seenProblemTitles[$titleKey] = true;
                                    }
                                    ?>
                                    <?php
                                    $diff = strtolower((string) ($problem['difficulty'] ?? 'easy'));
                                    $diffClass = $diff === 'hard' ? 'diff-hard' : ($diff === 'medium' ? 'diff-medium' : 'diff-easy');
                                    $diffLabel = $diff === 'hard'
                                        ? t('difficulty_hard', 'Сложная')
                                        : ($diff === 'medium' ? t('difficulty_medium', 'Средняя') : t('difficulty_easy', 'Легкая'));
                                    ?>
                                    <tr class="problem-row cursor-pointer"
                                        data-problem-id="<?= (int) ($problem['id'] ?? 0) ?>"
                                        data-difficulty="<?= htmlspecialchars($diff) ?>">
                                        <td>
                                            <div class="font-semibold text-indigo-900">
                                                <?= htmlspecialchars((string) ($problem['title'] ?? '')) ?>
                                            </div>
                                            <div class="text-xs subtle mt-1">
                                                <?= htmlspecialchars(implode(' • ', (array) ($problem['companies'] ?? []))) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="pill <?= $diffClass ?>"><?= htmlspecialchars($diffLabel) ?></span>
                                        </td>
                                        <td class="subtle"><?= htmlspecialchars((string) ($problem['category'] ?? '')) ?>
                                        </td>
                                        <td class="subtle"><?= htmlspecialchars((string) ($problem['acceptance'] ?? '')) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="surface p-4 sm:p-5">
                    <div class="flex items-start justify-between gap-3 flex-wrap mb-3">
                        <div>
                            <h3 id="problemTitle" class="text-xl font-bold"></h3>
                            <div id="problemMeta" class="subtle text-xs mt-1"></div>
                        </div>
                    </div>

                    <div class="workspace-copy">
                        <p id="problemStatement" class="text-sm leading-relaxed text-indigo-700"></p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                            <div class="spec-box">
                                <div class="text-xs font-semibold subtle mb-1"><?= t('contest_input', 'Ввод') ?></div>
                                <pre id="problemInput" class="text-sm text-indigo-700 whitespace-pre-wrap"></pre>
                            </div>
                            <div class="spec-box">
                                <div class="text-xs font-semibold subtle mb-1"><?= t('contest_output', 'Вывод') ?></div>
                                <pre id="problemOutput" class="text-sm text-indigo-700 whitespace-pre-wrap"></pre>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="surface p-4 sm:p-5">
                    <div class="panel-head">
                        <h2 class="text-xl font-bold"><?= t('courses_interview_trending', 'Trending Companies') ?></h2>
                        <span class="text-xs subtle"><?= count($trendingCompanies) ?></span>
                    </div>
                    <div class="space-y-3">
                        <?php foreach ($trendingCompanies as $company): ?>
                            <div class="trend-card">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="font-semibold text-indigo-900">
                                        <?= htmlspecialchars((string) ($company['name'] ?? '')) ?>
                                    </div>
                                    <span class="trend-meta"><?= (int) ($company['hiring'] ?? 0) ?>
                                        <?= t('courses_interview_roles', 'roles') ?></span>
                                </div>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <?php foreach ((array) ($company['focus'] ?? []) as $focus): ?>
                                        <span class="focus-chip"><?= htmlspecialchars((string) $focus) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>
            </section>
        </div>
    </main>

    <script>
        const problems = <?= tfSafeJson($problemsPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const rows = Array.from(document.querySelectorAll('.problem-row'));
        const difficultyFilter = document.getElementById('difficultyFilter');
        const problemTitleEl = document.getElementById('problemTitle');
        const problemMetaEl = document.getElementById('problemMeta');
        const problemStatementEl = document.getElementById('problemStatement');
        const problemInputEl = document.getElementById('problemInput');
        const problemOutputEl = document.getElementById('problemOutput');
        const editorEl = document.getElementById('editor');
        const verdictEl = document.getElementById('judgeVerdict');
        const testsEl = document.getElementById('judgeTests');
        const visibleTestsLimit = 2;
        const ui = {
            waiting: <?= tfSafeJson(t('contest_waiting', 'Ожидает'), JSON_UNESCAPED_UNICODE) ?>,
            ok: <?= tfSafeJson(t('contest_ok', 'OK'), JSON_UNESCAPED_UNICODE) ?>,
            wa: <?= tfSafeJson(t('contest_wa', 'WA'), JSON_UNESCAPED_UNICODE) ?>,
            copy: <?= tfSafeJson(t('common_copy', 'Copy'), JSON_UNESCAPED_UNICODE) ?>,
            copied: <?= tfSafeJson(t('common_copied', 'Copied'), JSON_UNESCAPED_UNICODE) ?>,
            uploadLoaded: <?= tfSafeJson(t('common_upload_loaded', 'Solution loaded'), JSON_UNESCAPED_UNICODE) ?>,
            uploadError: <?= tfSafeJson(t('common_upload_error', 'Unable to load file'), JSON_UNESCAPED_UNICODE) ?>,
            uploadTooLarge: <?= tfSafeJson(t('common_upload_too_large', 'File is too large'), JSON_UNESCAPED_UNICODE) ?>,
            uploadInvalidType: <?= tfSafeJson(t('common_upload_invalid_type', 'Unsupported file type'), JSON_UNESCAPED_UNICODE) ?>,
            test: <?= tfSafeJson(t('contest_test', 'Тест'), JSON_UNESCAPED_UNICODE) ?>,
            input: <?= tfSafeJson(t('contest_input', 'Ввод'), JSON_UNESCAPED_UNICODE) ?>,
            expected: <?= tfSafeJson(t('common_expected', 'Ожидаемо'), JSON_UNESCAPED_UNICODE) ?>,
            actual: <?= tfSafeJson(t('common_actual', 'Фактически'), JSON_UNESCAPED_UNICODE) ?>,
            accepted: <?= tfSafeJson(t('contest_accepted', 'Accepted'), JSON_UNESCAPED_UNICODE) ?>,
            needFix: <?= tfSafeJson(t('contest_need_fix', 'Нужно доработать решение'), JSON_UNESCAPED_UNICODE) ?>,
            serverError: <?= tfSafeJson(t('common_server_error', 'Ошибка сервера'), JSON_UNESCAPED_UNICODE) ?>,
            diffEasy: <?= tfSafeJson(t('difficulty_easy', 'Легкая'), JSON_UNESCAPED_UNICODE) ?>,
            diffMedium: <?= tfSafeJson(t('difficulty_medium', 'Средняя'), JSON_UNESCAPED_UNICODE) ?>,
            diffHard: <?= tfSafeJson(t('difficulty_hard', 'Сложная'), JSON_UNESCAPED_UNICODE) ?>
        };
        const editorUi = {
            saved: <?= tfSafeJson(t('editor_saved', 'Saved'), JSON_UNESCAPED_UNICODE) ?>,
            draft: <?= tfSafeJson(t('editor_draft', 'Draft'), JSON_UNESCAPED_UNICODE) ?>,
            wrapOn: <?= tfSafeJson(t('editor_wrap_on', 'Wrap: On'), JSON_UNESCAPED_UNICODE) ?>,
            wrapOff: <?= tfSafeJson(t('editor_wrap_off', 'Wrap: Off'), JSON_UNESCAPED_UNICODE) ?>,
            fullscreen: <?= tfSafeJson(t('editor_fullscreen', 'Fullscreen'), JSON_UNESCAPED_UNICODE) ?>,
            exitFullscreen: <?= tfSafeJson(t('editor_exit_fullscreen', 'Exit fullscreen'), JSON_UNESCAPED_UNICODE) ?>
        };

        function attachSmartIndent(textarea, getLang) {
            if (!textarea) return;
            const indentFor = (lang) => (lang === 'python' ? '    ' : '  ');
            textarea.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    e.preventDefault();
                    const indent = indentFor(getLang());
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const value = textarea.value;
                    textarea.value = value.slice(0, start) + indent + value.slice(end);
                    textarea.selectionStart = textarea.selectionEnd = start + indent.length;
                    return;
                }
                if (e.key === 'Enter') {
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const value = textarea.value;
                    const before = value.slice(0, start);
                    const after = value.slice(end);
                    const lineStart = before.lastIndexOf('\n') + 1;
                    const currentLine = before.slice(lineStart);
                    const baseIndent = (currentLine.match(/^\s*/)?.[0]) || '';
                    const trimmed = currentLine.trimEnd();
                    const lang = getLang();
                    const indentUnit = indentFor(lang);
                    let extra = '';
                    if (trimmed.endsWith('{')) extra = indentUnit;
                    if (lang === 'python' && trimmed.endsWith(':')) extra = indentUnit;
                    const insert = '\n' + baseIndent + extra;
                    e.preventDefault();
                    textarea.value = before + insert + after;
                    const caret = before.length + insert.length;
                    textarea.selectionStart = textarea.selectionEnd = caret;
                }
            });
        }

        let selectedProblemId = problems.length ? Number(problems[0].id) : 0;
        let testsInitialized = false;
        const languageSelect = document.getElementById('languageSelect');
        const availableLangs = Array.from(languageSelect?.options ?? []).map(opt => opt.value);
        const storedLang = localStorage.getItem('tfEditorLang');
        let selectedLang = storedLang && availableLangs.includes(storedLang) ? storedLang : 'cpp';
        if (languageSelect) languageSelect.value = selectedLang;
        const defaultStarter = {
            cpp: "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    // your code\n    return 0;\n}\n",
            python: "def solve():\n    # your code\n    pass\n\nif __name__ == '__main__':\n    solve()\n",
            c: "#include <stdio.h>\n\nint main(void) {\n    // your code\n    return 0;\n}\n",
            java: "import java.io.*;\nimport java.util.*;\n\npublic class Main {\n    public static void main(String[] args) throws Exception {\n        // your code\n    }\n}\n",
            javascript: "const fs = require('fs');\n\nfunction solve(input) {\n  // your code\n}\n\nsolve(fs.readFileSync(0, 'utf8').trim());\n",
            typescript: "import * as fs from 'fs';\n\nfunction solve(input: string): void {\n  // your code\n}\n\nsolve(fs.readFileSync(0, 'utf8').trim());\n",
            go: "package main\n\nimport (\n  \"bufio\"\n  \"fmt\"\n  \"os\"\n)\n\nfunc main() {\n  in := bufio.NewReader(os.Stdin)\n  _ = in\n  fmt.Println(\"\")\n}\n",
            rust: "use std::io::{self, Read};\n\nfn main() {\n  let mut input = String::new();\n  io::stdin().read_to_string(&mut input).unwrap();\n  // your code\n}\n",
            csharp: "using System;\n\npublic class Program {\n    public static void Main() {\n        // your code\n    }\n}\n",
            kotlin: "import java.io.BufferedReader\nimport java.io.InputStreamReader\n\nfun main() {\n  val br = BufferedReader(InputStreamReader(System.`in`))\n  val input = br.readLine()\n  // your code\n}\n",
            swift: "import Foundation\n\nlet data = String(data: FileHandle.standardInput.readDataToEndOfFile(), encoding: .utf8) ?? \"\"\n// your code\nprint(\"\")\n",
            php: "<" + "?php\n$input = trim(stream_get_contents(STDIN));\n// your code\n?" + ">\n",
            ruby: "input = STDIN.read\n# your code\n",
            scala: "import scala.io.StdIn\n\nobject Main {\n  def main(args: Array[String]): Unit = {\n    val input = StdIn.readLine()\n    // your code\n  }\n}\n",
            dart: "import 'dart:io';\n\nvoid main() {\n  final input = stdin.readLineSync() ?? '';\n  // your code\n}\n",
            sql: "-- write your query here\n"
        };

        const editorSurface = document.getElementById('interviewPrepEditorSurface');
        const themeToggle = document.getElementById('editorThemeToggle');
        const lineColEl = document.getElementById('editorLineCol');
        const saveStateEl = document.getElementById('editorSaveState');
        const wrapToggle = document.getElementById('wrapToggle');
        const fullscreenBtn = document.getElementById('editorFullscreenBtn');
        const solutionFileEl = document.getElementById('solutionFile');
        const wrapKey = 'tfEditorWrap';
        const allowedSolutionExtensions = ['txt', 'cpp', 'cc', 'c', 'h', 'hpp', 'py', 'java', 'cs', 'js', 'ts', 'go', 'rs', 'php', 'rb', 'kt', 'swift', 'scala', 'dart', 'sql'];
        const maxSolutionFileSize = 268435456;
        let draftTimer = null;

        const setSaveState = (state) => {
            if (!saveStateEl) return;
            saveStateEl.textContent = state === 'draft' ? editorUi.draft : editorUi.saved;
        };

        const updateLineCol = () => {
            if (!editorEl || !lineColEl) return;
            const pos = editorEl.selectionStart || 0;
            const before = editorEl.value.slice(0, pos);
            const lines = before.split('\n');
            const line = lines.length;
            const col = (lines[lines.length - 1] || '').length + 1;
            lineColEl.textContent = `Ln ${line}, Col ${col}`;
        };

        const applyWrap = (enabled) => {
            if (!editorEl || !wrapToggle) return;
            editorEl.classList.toggle('editor-wrap', enabled);
            wrapToggle.textContent = enabled ? editorUi.wrapOn : editorUi.wrapOff;
            localStorage.setItem(wrapKey, enabled ? '1' : '0');
        };

        const isFullscreen = () => editorSurface && editorSurface.classList.contains('is-fullscreen');
        const updateFullscreenButton = () => {
            if (!fullscreenBtn) return;
            fullscreenBtn.setAttribute('aria-label', isFullscreen() ? editorUi.exitFullscreen : editorUi.fullscreen);
            fullscreenBtn.innerHTML = isFullscreen() ? '<i class="fas fa-compress"></i>' : '<i class="fas fa-expand"></i>';
        };

        const toggleFullscreen = () => {
            if (!editorSurface) return;
            editorSurface.classList.toggle('is-fullscreen');
            document.body.classList.toggle('editor-fullscreen-open', isFullscreen());
            updateFullscreenButton();
            setTimeout(updateLineCol, 0);
        };

        const getDraftKey = (problemId, lang) => `tfInterviewDraft:${problemId}:${lang}`;
        const loadDraft = (problem, lang) => {
            if (!editorEl || !problem) return;
            const key = getDraftKey(problem.id, lang);
            const stored = localStorage.getItem(key);
            if (stored !== null) {
                editorEl.value = stored;
                setSaveState('draft');
            } else {
                editorEl.value = getStarter(problem, lang);
                setSaveState('saved');
            }
            updateLineCol();
        };

        const saveDraft = () => {
            const p = getProblem();
            if (!editorEl || !p) return;
            const key = getDraftKey(p.id, selectedLang);
            localStorage.setItem(key, editorEl.value || '');
            setSaveState('saved');
        };
        const applyEditorTheme = (theme) => {
            if (!editorSurface || !themeToggle) return;
            if (theme === 'dark') {
                editorSurface.classList.add('editor-dark');
                themeToggle.innerHTML = `<i class="fas fa-sun"></i>`;
            } else {
                editorSurface.classList.remove('editor-dark');
                themeToggle.innerHTML = `<i class="fas fa-moon"></i>`;
            }
            localStorage.setItem('tfEditorTheme', theme);
        };
        const storedTheme = localStorage.getItem('tfEditorTheme') || 'light';
        applyEditorTheme(storedTheme);
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const next = editorSurface && editorSurface.classList.contains('editor-dark') ? 'light' : 'dark';
                applyEditorTheme(next);
            });
        }

        function esc(v) {
            return String(v || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }

        function getClipboardText(test) {
            if (!test || typeof test !== 'object') return '';
            if (typeof test.in !== 'undefined') return String(test.in);
            if (typeof test.stdin !== 'undefined') return String(test.stdin);
            return '';
        }

        async function copyText(text) {
            const normalized = String(text || '');
            if (!normalized) return false;
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(normalized);
                return true;
            }

            const helper = document.createElement('textarea');
            helper.value = normalized;
            helper.setAttribute('readonly', 'readonly');
            helper.style.position = 'fixed';
            helper.style.opacity = '0';
            document.body.appendChild(helper);
            helper.select();
            const copied = document.execCommand('copy');
            document.body.removeChild(helper);
            return copied;
        }

        function attachCopyHandler(button, text) {
            if (!button) return;
            button.addEventListener('click', async () => {
                try {
                    const copied = await copyText(text);
                    if (copied && typeof window.tfNotify === 'function') {
                        window.tfNotify(ui.copied);
                        return;
                    }
                } catch (e) {}
                if (typeof window.tfNotify === 'function') {
                    window.tfNotify(ui.uploadError);
                }
            });
        }

        async function loadSolutionFile(file) {
            if (!file || !editorEl) return;
            const name = String(file.name || '');
            const ext = name.includes('.') ? name.split('.').pop().toLowerCase() : '';
            if (!allowedSolutionExtensions.includes(ext)) {
                throw new Error(ui.uploadInvalidType);
            }
            if (Number(file.size || 0) > maxSolutionFileSize) {
                throw new Error(ui.uploadTooLarge);
            }
            const text = await file.text();
            if (text.includes('\u0000')) {
                throw new Error(ui.uploadInvalidType);
            }
            editorEl.value = text.replace(/\r\n?/g, '\n');
            setSaveState('draft');
            updateLineCol();
            saveDraft();
        }

        function getProblem() {
            return problems.find((p) => Number(p.id) === Number(selectedProblemId)) || null;
        }

        function getStarter(problem, lang) {
            if (problem && problem.starter && typeof problem.starter[lang] === 'string' && problem.starter[lang].trim() !== '') {
                return problem.starter[lang];
            }
            return defaultStarter[lang] || '';
        }

        function localizeDifficulty(diffRaw) {
            const diff = String(diffRaw || '').toLowerCase();
            if (diff === 'hard') return ui.diffHard;
            if (diff === 'medium') return ui.diffMedium;
            return ui.diffEasy;
        }

        function syncLangSelect() {
            if (languageSelect) languageSelect.value = selectedLang;
        }

        function setActiveRow() {
            rows.forEach((row) => {
                const id = Number(row.getAttribute('data-problem-id') || 0);
                row.classList.toggle('row-active', id === Number(selectedProblemId));
            });
        }

        function renderTests(results) {
            testsEl.innerHTML = '';
            const problem = getProblem();
            if (!problem) return;
            const problemTests = Array.isArray(problem.tests) ? problem.tests.slice(0, visibleTestsLimit) : [];
            const showRows = Array.isArray(results) && results.length
                ? results.slice(0, visibleTestsLimit)
                : problemTests.map(() => null);
            showRows.forEach((res, i) => {
                const t = problemTests[i] || {};
                const passed = res ? !!res.passed : null;
                const state = passed === null ? '' : (passed ? ui.ok : ui.wa);
                const cls = passed === null ? '' : (passed ? 'text-indigo-700' : 'text-indigo-900');
                const item = document.createElement('div');
                item.className = 'test-item';
                item.innerHTML = `<div class="flex items-center justify-between gap-3 flex-wrap"><div class="font-semibold text-indigo-800">${esc(ui.test)} #${i + 1}</div><div class="flex items-center gap-2">${passed === null ? `<button type="button" class="action-btn test-copy-btn">${esc(ui.copy)}</button>` : `<div class="${cls} font-semibold">${esc(state)}</div>`}</div></div><div class="mt-2 text-indigo-600"><b>${esc(ui.input)}:</b><pre>${esc(t.in || '')}</pre></div><div class="mt-1 text-indigo-600"><b>${esc(ui.expected)}:</b><pre>${esc(t.out || '')}</pre></div>`;
                if (passed === null) {
                    attachCopyHandler(item.querySelector('.test-copy-btn'), getClipboardText(t));
                }
                testsEl.appendChild(item);
            });
        }

        function loadProblem() {
            const p = getProblem();
            if (!p) return;
            setActiveRow();
            syncLangSelect();
            problemTitleEl.textContent = p.title || '';
            problemMetaEl.textContent = `${localizeDifficulty(p.difficulty || '')} • ${p.category || ''}`;
            problemStatementEl.textContent = p.statement || '';
            problemInputEl.textContent = p.input || '';
            problemOutputEl.textContent = p.output || '';
            loadDraft(p, selectedLang);
            renderTests(null);
            testsInitialized = true;
        }

        rows.forEach((row) => {
            row.addEventListener('click', () => {
                saveDraft();
                selectedProblemId = Number(row.getAttribute('data-problem-id') || 0);
                loadProblem();
            });
        });

        if (difficultyFilter) {
            difficultyFilter.addEventListener('change', () => {
                const mode = (difficultyFilter.value || 'all').toLowerCase();
                rows.forEach((row) => {
                    const d = (row.getAttribute('data-difficulty') || '').toLowerCase();
                    row.style.display = (mode === 'all' || mode === d) ? '' : 'none';
                });
            });
        }

        if (languageSelect) {
            languageSelect.addEventListener('change', () => {
                saveDraft();
                selectedLang = languageSelect.value;
                localStorage.setItem('tfEditorLang', selectedLang);
                loadProblem();
            });
        }

        document.getElementById('resetCode')?.addEventListener('click', () => {
            const p = getProblem();
            if (!p) return;
            editorEl.value = getStarter(p, selectedLang);
            setSaveState('saved');
            saveDraft();
            verdictEl.textContent = '';
            verdictEl.className = 'verdict';
            renderTests(null);
        });

        document.getElementById('checkCode')?.addEventListener('click', () => {
            const p = getProblem();
            if (!p) return;

            fetch('?action=interview-submit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({
                    problem_id: p.id,
                    language: selectedLang,
                    code: editorEl.value || ''
                })
            })
                .then((r) => r.json())
                .then((data) => {
                    if (!data || !data.success) {
                        throw new Error((data && data.message) ? data.message : ui.serverError);
                    }

                    if (data.message && typeof window.tfNotify === 'function') {
                        window.tfNotify(String(data.message));
                    }
                    renderTests(Array.isArray(data.results) ? data.results : null);
                    if (data.passed) {
                        verdictEl.className = 'verdict pass';
                        verdictEl.textContent = ui.accepted;
                    } else {
                        verdictEl.className = 'verdict fail';
                        verdictEl.textContent = ui.needFix;
                    }
                })
                .catch((e) => {
                    if (typeof window.tfNotify === 'function') {
                        window.tfNotify(e && e.message ? e.message : ui.serverError);
                    }
                    verdictEl.className = 'verdict fail';
                    verdictEl.textContent = ui.needFix;
                });
        });

        if (solutionFileEl) {
            solutionFileEl.addEventListener('change', async (event) => {
                const file = event.target && event.target.files ? event.target.files[0] : null;
                if (!file) return;
                try {
                    await loadSolutionFile(file);
                    if (typeof window.tfNotify === 'function') {
                        window.tfNotify(ui.uploadLoaded);
                    }
                } catch (e) {
                    if (typeof window.tfNotify === 'function') {
                        window.tfNotify(e && e.message ? e.message : ui.uploadError);
                    }
                } finally {
                    event.target.value = '';
                }
            });
        }

        attachSmartIndent(editorEl, () => selectedLang);

        updateLineCol();
        applyWrap(localStorage.getItem(wrapKey) === '1');

        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', toggleFullscreen);
            updateFullscreenButton();
        }
        if (wrapToggle) {
            wrapToggle.addEventListener('click', () => {
                const enabled = !editorEl.classList.contains('editor-wrap');
                applyWrap(enabled);
            });
        }
        if (editorEl) {
            editorEl.addEventListener('input', () => {
                setSaveState('draft');
                updateLineCol();
                if (draftTimer) clearTimeout(draftTimer);
                draftTimer = setTimeout(saveDraft, 500);
            });
            editorEl.addEventListener('click', updateLineCol);
            editorEl.addEventListener('keyup', updateLineCol);
        }
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isFullscreen()) {
                e.preventDefault();
                toggleFullscreen();
                return;
            }
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
                e.preventDefault();
                saveDraft();
                return;
            }
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('checkCode')?.click();
            }
        });

        loadProblem();
    </script>
</body>

</html>
