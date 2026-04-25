<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$contest = $contest ?? [];
$contestTasks = $contestTasks ?? [];
$contestLeaderboard = $contestLeaderboard ?? [];
$contestSolvedTaskIds = $contestSolvedTaskIds ?? [];
$contestUserPoints = (int) ($contestUserPoints ?? 0);
$contestUserRank = function_exists('tfContestResolveUserRank')
    ? tfContestResolveUserRank($contestUserPoints)
    : ['key' => 'starter', 'label' => 'Starter'];

$tasksPayload = [];
foreach ($contestTasks as $task) {
    $tests = [];
    try {
        $tests = json_decode((string) ($task['tests_json'] ?? '[]'), true) ?: [];
    } catch (Throwable $e) {
        $tests = [];
    }
    $tasksPayload[] = [
        'id' => (int) ($task['id'] ?? 0),
        'contest_id' => (int) ($task['contest_id'] ?? 0),
        'title' => (string) ($task['title'] ?? ''),
        'difficulty' => (string) ($task['difficulty'] ?? 'easy'),
        'description' => (string) ($task['statement'] ?? ''),
        'input' => (string) ($task['input_spec'] ?? ''),
        'output' => (string) ($task['output_spec'] ?? ''),
        'time_limit_sec' => max(1, (int) ($task['time_limit_sec'] ?? 3)),
        'memory_limit_kb' => 262144,
        'tests' => is_array($tests) ? $tests : [],
        'starter' => [
            'cpp' => (string) ($task['starter_cpp'] ?? ''),
            'python' => (string) ($task['starter_python'] ?? ''),
            'c' => (string) ($task['starter_c'] ?? ''),
            'csharp' => (string) ($task['starter_csharp'] ?? ''),
            'java' => (string) ($task['starter_java'] ?? ''),
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
    ];
}

$totalTaskCount = count($tasksPayload);
$solvedCount = count(array_unique(array_map('intval', (array) $contestSolvedTaskIds)));
if ($solvedCount > $totalTaskCount) {
    $solvedCount = $totalTaskCount;
}
$progressPercent = $totalTaskCount > 0 ? (int) round(($solvedCount / $totalTaskCount) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(function_exists('currentLang') ? currentLang() : 'ru') ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars((string) ($contest['title'] ?? t('contest_heading', 'Contest'))) ?> - CodeMaster</title>
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

        .codebox {
            font-family: 'JetBrains Mono', monospace;
        }

        .shell {
            max-width: 1320px;
            margin: 0 auto;
            padding: 1.5rem 1rem 2rem;
        }

        .hero {
            border-radius: 1rem;
            border: 1px solid var(--line);
            background: #ffffff;
            color: var(--ink);
            box-shadow: 0 8px 24px rgba(49, 46, 129, 0.08);
            padding: 1.15rem;
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
            font-size: clamp(1.4rem, 2.8vw, 2.05rem);
            line-height: 1.1;
            margin-top: .4rem;
        }

        .surface {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 1rem;
            box-shadow: 0 6px 20px rgba(49, 46, 129, 0.06);
        }

        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .9rem;
        }

        .leaderboard-table th,
        .leaderboard-table td {
            padding: .65rem .5rem;
            border-bottom: 1px solid var(--indigo-100);
            text-align: left;
            vertical-align: middle;
        }

        .leaderboard-table th {
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--muted);
        }

        .leaderboard-table td.num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .rank-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .3rem;
            border-radius: 999px;
            padding: .3rem .65rem;
            font-size: .75rem;
            font-weight: 800;
            border: 1px solid transparent;
        }

        .rank-chip.starter {
            background: #eef2ff;
            color: #4338ca;
            border-color: #c7d2fe;
        }

        .rank-chip.bronze {
            background: #f59e0b1a;
            color: #92400e;
            border-color: #fcd34d;
        }

        .rank-chip.silver {
            background: #e5e7eb;
            color: #374151;
            border-color: #cbd5e1;
        }

        .rank-chip.gold {
            background: #fef3c7;
            color: #92400e;
            border-color: #fbbf24;
        }

        .leaderboard-table tr:last-child td {
            border-bottom: none;
        }

        .progress-wrap {
            margin-top: .6rem;
            background: var(--indigo-100);
            border: 1px solid var(--indigo-200);
            border-radius: 999px;
            overflow: hidden;
            height: .58rem;
        }

        .progress-fill {
            height: 100%;
            background: var(--indigo-600);
            transition: width .25s ease;
        }

        .task-nav {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
        }

        .task-row {
            min-width: 2.2rem;
            height: 2.2rem;
            border: 1px solid var(--line);
            border-radius: .68rem;
            background: #ffffff;
            padding: 0 .5rem;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .86rem;
            font-weight: 700;
            color: var(--indigo-800);
            transition: all .2s ease;
        }

        .task-row:hover {
            background: var(--indigo-50);
            border-color: var(--indigo-300);
        }

        .task-row.is-done {
            background: var(--good-bg);
            color: var(--good-text);
            border-color: var(--indigo-300);
        }

        .task-row.item-active {
            border-color: var(--indigo-500);
            background: var(--indigo-100);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, .16);
        }

        .diff-badge {
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            padding: .22rem .56rem;
        }

        .diff-badge.easy {
            background: var(--good-bg);
            color: var(--good-text);
        }

        .diff-badge.medium {
            background: var(--mid-bg);
            color: var(--mid-text);
        }

        .diff-badge.hard {
            background: var(--hard-bg);
            color: var(--hard-text);
        }

        .spec-box {
            border: 1px solid var(--line);
            border-radius: .75rem;
            background: var(--indigo-50);
            padding: .7rem;
        }

        .lang-btn {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--indigo-800);
            border-radius: .62rem;
            padding: .38rem .58rem;
            font-size: .8rem;
            font-weight: 600;
            transition: all .2s ease;
        }

        .lang-btn.is-active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
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
            padding: .48rem .8rem;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--indigo-800);
            font-size: .82rem;
            font-weight: 600;
        }

        .action-btn.primary {
            border-color: var(--accent);
            background: var(--accent);
            color: #fff;
        }

        .editor {
            width: 100%;
            min-height: 18rem;
            resize: vertical;
            border: 1px solid var(--indigo-200);
            border-radius: .8rem;
            padding: .82rem;
            background: var(--indigo-50);
            color: var(--indigo-900);
            line-height: 1.5;
            font-size: .82rem;
            white-space: pre;
        }
        .editor.editor-wrap {
            white-space: pre-wrap;
        }

        .editor:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .12);
        }

        .test-card {
            border: 1px solid var(--line);
            border-radius: .75rem;
            padding: .68rem;
            background: #ffffff;
            font-size: .8rem;
        }

        .test-card pre {
            margin-top: .25rem;
            white-space: pre-wrap;
            font-family: 'JetBrains Mono', monospace;
            color: var(--indigo-800);
            font-size: .76rem;
        }

        .verdict {
            margin-top: .72rem;
            font-size: .9rem;
            font-weight: 700;
        }

        .verdict.pass {
            color: var(--indigo-800);
        }

        .verdict.fail {
            color: var(--indigo-900);
        }

        .leader-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            border: 1px solid var(--line);
            border-radius: .72rem;
            padding: .52rem .62rem;
            background: #ffffff;
            font-size: .83rem;
        }

        .leader-rank {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 999px;
            background: var(--indigo-100);
            color: var(--indigo-800);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            font-weight: 700;
            margin-right: .4rem;
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

            .right-pane {
                order: 1;
            }

            .left-pane {
                order: 2;
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

            .right-pane {
                order: 1;
                margin-top: 0;
            }

            .left-pane {
                order: 2;
            }

        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main class="shell">
        <?php
        $startsAtTs = !empty($contest['starts_at']) ? strtotime((string) $contest['starts_at']) : null;
        $endsAtTs = !empty($contest['ends_at']) ? strtotime((string) $contest['ends_at']) : null;
        $nowTs = time();
        $isLocked = !empty($contest['is_locked']) || ($endsAtTs !== null && $nowTs >= $endsAtTs);
        $isNotStarted = !$isLocked && $startsAtTs !== null && $nowTs < $startsAtTs;
        ?>
        <section class="hero mb-4">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <a href="?action=contests" class="text-xs font-semibold text-indigo-700 hover:text-indigo-900">
                        <i class="fas fa-arrow-left mr-1"></i><?= t('contest_list', 'Contests') ?>
                    </a>
                    <h1 class="hero-title">
                        <?= htmlspecialchars((string) ($contest['title'] ?? t('contest_heading', 'Contest'))) ?></h1>
                    <p class="text-sm text-indigo-700 mt-1">
                        <?= htmlspecialchars((string) ($contest['description'] ?? '')) ?></p>
                    <?php if ($totalTaskCount > 0): ?>
                        <div class="text-xs text-indigo-600 mt-2">
                            <span id="contest-solved-count"><?= $solvedCount ?></span>/<?= $totalTaskCount ?>
                            <?= t('contest_solved', 'Solved') ?> (<span id="contest-progress-percent"><?= $progressPercent ?></span>%)
                        </div>
                        <div class="progress-wrap">
                            <div id="contest-progress-fill" class="progress-fill" style="width: <?= $progressPercent ?>%"></div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex flex-col gap-3">
                    <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3">
                        <div class="text-xs text-indigo-600"><?= t('contest_points', 'Points') ?></div>
                        <div id="user-points" class="text-2xl font-bold text-indigo-900"><?= $contestUserPoints ?></div>
                        <div class="mt-2">
                            <span id="user-rank-chip" class="rank-chip <?= htmlspecialchars((string) ($contestUserRank['key'] ?? 'starter')) ?>">
                                <?= htmlspecialchars((string) ($contestUserRank['label'] ?? 'Starter')) ?>
                            </span>
                        </div>
                    </div>
                    <?php if ($startsAtTs !== null || $endsAtTs !== null): ?>
                        <div class="rounded-xl border border-indigo-200 bg-white px-4 py-3">
                            <div class="text-xs text-indigo-500"><?= t('contest_timer_label', 'Таймер контеста') ?></div>
                            <div id="contest-timer" class="text-sm font-semibold text-indigo-900">
                                <?php if ($isLocked): ?>
                                    <?= t('contest_locked', 'Контест завершен') ?>
                                <?php elseif ($isNotStarted): ?>
                                    <?= t('contest_starts_in', 'Начнется через') ?> -
                                <?php else: ?>
                                    <?= t('contest_ends_in', 'Закончится через') ?> -
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($contest['ends_at'])): ?>
                                <div class="text-xs text-indigo-500 mt-1"><?= htmlspecialchars((string) $contest['ends_at']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php if ($isLocked): ?>
            <section class="surface p-4 sm:p-5 mb-4 text-sm text-indigo-700">
                <?= t('contest_locked', 'Контест завершен') ?>
            </section>
        <?php elseif ($isNotStarted): ?>
            <section class="surface p-4 sm:p-5 mb-4 text-sm text-indigo-700">
                <?= t('contest_not_started', 'Контест еще не начался') ?>
            </section>
        <?php endif; ?>

        <?php if (empty($tasksPayload)): ?>
            <section class="surface p-8 text-center">
                <h2 class="text-xl font-bold"><?= t('contest_no_task', 'No tasks in this contest yet.') ?></h2>
                <p class="text-indigo-600 mt-2"><?= t('contest_no_task_hint', 'Pick another contest from the list.') ?></p>
                <a href="?action=contests"
                    class="inline-flex items-center mt-4 px-4 py-2 rounded-lg bg-indigo-700 text-white text-sm font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i><?= t('contest_list', 'Contests') ?>
                </a>
            </section>
        <?php else: ?>
            <div class="split-layout">
                <section class="split-col left-pane">
                    <article class="surface editor-surface p-4 sm:p-5" id="contestEditorSurface">
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
                            <div class="flex items-center gap-2">
                                <button id="editorThemeToggle" class="action-btn" aria-label="<?= t('theme_dark', 'Dark') ?>">
                                    <i class="fas fa-moon"></i>
                                </button>
                                <label for="solution-file" class="action-btn cursor-pointer" title="<?= t('contest_upload_solution', 'Upload solution') ?>" aria-label="<?= t('contest_upload_solution', 'Upload solution') ?>">
                                    <i class="fas fa-file-arrow-up"></i>
                                </label>
                                <input id="solution-file" type="file" class="hidden" accept=".txt,.cpp,.cc,.c,.h,.hpp,.py,.java,.cs,.js,.ts,.go,.rs,.php,.rb,.kt,.swift,.scala,.dart,.sql">
                                <button id="reset-code" class="action-btn"><?= t('contest_reset', 'Reset code') ?></button>
                                <button id="run-check"
                                    class="action-btn primary"><?= t('contest_check', 'Check solution') ?></button>
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

                    <article class="surface p-4 sm:p-5">
                        <h3 class="text-base font-bold mb-2"><?= t('contest_verdict_title', 'Tests and verdict') ?></h3>
                        <div id="tests-box" class="space-y-2"></div>
                        <div id="verdict" class="verdict"></div>
                    </article>
                </section>

                <section class="split-col right-pane">
                    <article class="surface p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-base font-bold"><?= t('contest_tasks', 'Tasks') ?></h2>
                            <span id="task-count" class="text-xs text-indigo-500"><?= $totalTaskCount ?></span>
                        </div>
                        <div id="task-list" class="task-nav"></div>
                    </article>

                    <article class="surface p-4 sm:p-5">
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <h2 id="task-title" class="text-xl font-bold"></h2>
                            <span id="task-diff" class="diff-badge easy"><?= t('difficulty_easy', 'Easy') ?></span>
                        </div>
                        <p id="task-description" class="text-sm text-indigo-700 leading-relaxed mt-2"></p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                            <div class="spec-box">
                                <div class="text-xs font-semibold text-indigo-500 mb-1"><?= t('contest_time_limit', 'Time limit') ?></div>
                                <div id="task-time-limit" class="text-sm font-semibold text-indigo-800">3 sec</div>
                            </div>
                            <div class="spec-box">
                                <div class="text-xs font-semibold text-indigo-500 mb-1"><?= t('contest_memory_limit', 'Memory limit') ?></div>
                                <div id="task-memory-limit" class="text-sm font-semibold text-indigo-800">256 MB</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                            <div class="spec-box">
                                <div class="text-xs font-semibold text-indigo-500 mb-1"><?= t('contest_input', 'Input') ?>
                                </div>
                                <pre id="task-input" class="text-sm text-indigo-700 whitespace-pre-wrap"></pre>
                            </div>
                            <div class="spec-box">
                                <div class="text-xs font-semibold text-indigo-500 mb-1"><?= t('contest_output', 'Output') ?>
                                </div>
                                <pre id="task-output" class="text-sm text-indigo-700 whitespace-pre-wrap"></pre>
                            </div>
                        </div>
                    </article>

                    <article class="surface p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-base font-bold"><?= t('contest_leaderboard', 'Leaderboard') ?></h2>
                            <i class="fas fa-trophy text-indigo-300"></i>
                        </div>
                        <?php if (empty($contestLeaderboard)): ?>
                            <div class="text-sm text-indigo-500"><?= t('contest_leaderboard_empty', 'No participants yet.') ?>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="leaderboard-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('common_user', 'User') ?></th>
                                            <th><?= t('contest_rank', 'Rank') ?></th>
                                            <th class="text-right"><?= t('contest_attempts_label', 'Attempts') ?></th>
                                            <th class="text-right"><?= t('contest_points', 'Points') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($contestLeaderboard as $idx => $row): ?>
                                            <?php $rowRank = function_exists('tfContestResolveUserRank') ? tfContestResolveUserRank((int) ($row['contest_points'] ?? 0)) : ['key' => 'starter', 'label' => 'Starter']; ?>
                                            <tr>
                                                <td><?= $idx + 1 ?></td>
                                                <td class="truncate"><?= htmlspecialchars((string) ($row['name'] ?? t('common_user', 'User'))) ?></td>
                                                <td><span class="rank-chip <?= htmlspecialchars((string) ($rowRank['key'] ?? 'starter')) ?>"><?= htmlspecialchars((string) ($rowRank['label'] ?? 'Starter')) ?></span></td>
                                                <td class="num"><?= (int) ($row['attempts_count'] ?? 0) ?></td>
                                                <td class="num font-bold text-indigo-700"><?= (int) ($row['contest_points'] ?? 0) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </article>
                </section>
            </div>

            <script>
                const tasks = <?= tfSafeJson($tasksPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
                const visibleTasks = Array.isArray(tasks) ? tasks.slice() : [];
                const solvedFromServer = <?= tfSafeJson(array_values(array_map('intval', $contestSolvedTaskIds)), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
                const contestId = <?= (int) ($contest['id'] ?? 0) ?>;
                const editorUi = {
                    saved: <?= tfSafeJson(t('editor_saved', 'Saved'), JSON_UNESCAPED_UNICODE) ?>,
                    draft: <?= tfSafeJson(t('editor_draft', 'Draft'), JSON_UNESCAPED_UNICODE) ?>,
                    wrapOn: <?= tfSafeJson(t('editor_wrap_on', 'Wrap: On'), JSON_UNESCAPED_UNICODE) ?>,
                    wrapOff: <?= tfSafeJson(t('editor_wrap_off', 'Wrap: Off'), JSON_UNESCAPED_UNICODE) ?>,
                    fullscreen: <?= tfSafeJson(t('editor_fullscreen', 'Fullscreen'), JSON_UNESCAPED_UNICODE) ?>,
                    exitFullscreen: <?= tfSafeJson(t('editor_exit_fullscreen', 'Exit fullscreen'), JSON_UNESCAPED_UNICODE) ?>
                };
                const contestTiming = {
                    startsAt: <?= tfSafeJson($contest['starts_at'] ?? null, JSON_UNESCAPED_UNICODE) ?>,
                    endsAt: <?= tfSafeJson($contest['ends_at'] ?? null, JSON_UNESCAPED_UNICODE) ?>,
                    isLocked: <?= $isLocked ? 'true' : 'false' ?>
                };
                const i18n = {
                    solved: <?= tfSafeJson(t('contest_solved', 'Solved'), JSON_UNESCAPED_UNICODE) ?>,
                    unsolved: <?= tfSafeJson(t('contest_unsolved', 'Unsolved'), JSON_UNESCAPED_UNICODE) ?>,
                    waiting: <?= tfSafeJson(t('contest_waiting', 'Pending'), JSON_UNESCAPED_UNICODE) ?>,
                    ok: <?= tfSafeJson(t('contest_ok', 'OK'), JSON_UNESCAPED_UNICODE) ?>,
                    wa: <?= tfSafeJson(t('contest_wa', 'WA'), JSON_UNESCAPED_UNICODE) ?>,
                    copy: <?= tfSafeJson(t('common_copy', 'Copy'), JSON_UNESCAPED_UNICODE) ?>,
                    copied: <?= tfSafeJson(t('common_copied', 'Copied'), JSON_UNESCAPED_UNICODE) ?>,
                    uploadLoaded: <?= tfSafeJson(t('common_upload_loaded', 'Solution loaded'), JSON_UNESCAPED_UNICODE) ?>,
                    uploadError: <?= tfSafeJson(t('common_upload_error', 'Unable to load file'), JSON_UNESCAPED_UNICODE) ?>,
                    uploadTooLarge: <?= tfSafeJson(t('common_upload_too_large', 'File is too large'), JSON_UNESCAPED_UNICODE) ?>,
                    uploadInvalidType: <?= tfSafeJson(t('common_upload_invalid_type', 'Unsupported file type'), JSON_UNESCAPED_UNICODE) ?>,
                    test: <?= tfSafeJson(t('contest_test', 'Test'), JSON_UNESCAPED_UNICODE) ?>,
                    input: <?= tfSafeJson(t('contest_input', 'Input'), JSON_UNESCAPED_UNICODE) ?>,
                    expected: <?= tfSafeJson(t('common_expected', 'Expected'), JSON_UNESCAPED_UNICODE) ?>,
                    diffEasy: <?= tfSafeJson(t('difficulty_easy', 'Easy'), JSON_UNESCAPED_UNICODE) ?>,
                    diffMedium: <?= tfSafeJson(t('difficulty_medium', 'Medium'), JSON_UNESCAPED_UNICODE) ?>,
                    diffHard: <?= tfSafeJson(t('difficulty_hard', 'Hard'), JSON_UNESCAPED_UNICODE) ?>,
                    accepted: <?= tfSafeJson(t('contest_accepted', 'Accepted'), JSON_UNESCAPED_UNICODE) ?>,
                    needFix: <?= tfSafeJson(t('contest_need_fix', 'Need to fix solution'), JSON_UNESCAPED_UNICODE) ?>,
                    taskDone: <?= tfSafeJson(t('contest_task_done', 'Task marked as solved'), JSON_UNESCAPED_UNICODE) ?>,
                    pointsEarned: <?= tfSafeJson(t('contest_points_earned', 'Points'), JSON_UNESCAPED_UNICODE) ?>,
                    rank: <?= tfSafeJson(t('contest_rank', 'Rank'), JSON_UNESCAPED_UNICODE) ?>,
                    successNotice: <?= tfSafeJson(t('contest_submit_success', 'Решение принято'), JSON_UNESCAPED_UNICODE) ?>,
                    failNotice: <?= tfSafeJson(t('contest_submit_fail', 'Решение не прошло проверку'), JSON_UNESCAPED_UNICODE) ?>,
                    timeLimit: <?= tfSafeJson(t('contest_time_limit', 'Time limit'), JSON_UNESCAPED_UNICODE) ?>,
                    memoryLimit: <?= tfSafeJson(t('contest_memory_limit', 'Memory limit'), JSON_UNESCAPED_UNICODE) ?>,
                    sendingNotice: <?= tfSafeJson(t('contest_submit_sending', 'Решение отправлено на проверку'), JSON_UNESCAPED_UNICODE) ?>,
                    serverError: <?= tfSafeJson(t('common_server_error', 'Server error'), JSON_UNESCAPED_UNICODE) ?>
                };

                const timerEl = document.getElementById('contest-timer');
                const runBtn = document.getElementById('run-check');
                const solvedCountEl = document.getElementById('contest-solved-count');
                const progressPercentEl = document.getElementById('contest-progress-percent');
                const progressFillEl = document.getElementById('contest-progress-fill');
                const totalTaskCount = <?= (int) $totalTaskCount ?>;
                let solvedCount = <?= (int) $solvedCount ?>;

                function formatCountdown(ms) {
                    const total = Math.max(0, Math.floor(ms / 1000));
                    const h = Math.floor(total / 3600);
                    const m = Math.floor((total % 3600) / 60);
                    const s = total % 60;
                    const pad = (v) => String(v).padStart(2, '0');
                    return `${pad(h)}:${pad(m)}:${pad(s)}`;
                }

                function updateContestTimer() {
                    if (!timerEl) return;
                    if (contestTiming.isLocked) {
                        timerEl.textContent = <?= tfSafeJson(t('contest_locked', 'Контест завершен'), JSON_UNESCAPED_UNICODE) ?>;
                        if (runBtn) runBtn.disabled = true;
                        return;
                    }
                    const now = Date.now();
                    const startsAt = contestTiming.startsAt ? Date.parse(contestTiming.startsAt.replace(' ', 'T')) : null;
                    const endsAt = contestTiming.endsAt ? Date.parse(contestTiming.endsAt.replace(' ', 'T')) : null;

                    if (startsAt && now < startsAt) {
                        timerEl.textContent = <?= tfSafeJson(t('contest_starts_in', 'Начнется через'), JSON_UNESCAPED_UNICODE) ?> + ' ' + formatCountdown(startsAt - now);
                        if (runBtn) runBtn.disabled = true;
                        return;
                    }
                    if (endsAt && now >= endsAt) {
                        timerEl.textContent = <?= tfSafeJson(t('contest_locked', 'Контест завершен'), JSON_UNESCAPED_UNICODE) ?>;
                        contestTiming.isLocked = true;
                        if (runBtn) runBtn.disabled = true;
                        return;
                    }
                    if (endsAt) {
                        timerEl.textContent = <?= tfSafeJson(t('contest_ends_in', 'Закончится через'), JSON_UNESCAPED_UNICODE) ?> + ' ' + formatCountdown(endsAt - now);
                    }
                    if (runBtn) runBtn.disabled = false;
                }

                let selectedTaskIndex = 0;
                const taskCountEl = document.getElementById('task-count');
                if (taskCountEl) {
                    taskCountEl.textContent = String(visibleTasks.length);
                }
                const languageSelect = document.getElementById('languageSelect');
                const availableLangs = Array.from(languageSelect?.options ?? []).map(opt => opt.value);
                const storedLang = localStorage.getItem('tfEditorLang');
                let selectedLang = storedLang && availableLangs.includes(storedLang) ? storedLang : 'cpp';
                if (languageSelect) languageSelect.value = selectedLang;
                let solved = {};
                solvedFromServer.forEach((id) => { solved[String(id)] = true; });

                const defaultStarter = {
                    cpp: "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n  // your code\n  return 0;\n}\n",
                    python: "def solve():\n  # your code\n  pass\n\nif __name__ == '__main__':\n  solve()\n",
                    c: "#include <stdio.h>\n\nint main(void) {\n  // your code\n  return 0;\n}\n",
                    java: "import java.io.*;\nimport java.util.*;\n\npublic class Main {\n  public static void main(String[] args) throws Exception {\n    // your code\n  }\n}\n",
                    javascript: "const fs = require('fs');\n\nfunction solve(input) {\n  // your code\n}\n\nsolve(fs.readFileSync(0, 'utf8').trim());\n",
                    typescript: "import * as fs from 'fs';\n\nfunction solve(input: string): void {\n  // your code\n}\n\nsolve(fs.readFileSync(0, 'utf8').trim());\n",
                    go: "package main\n\nimport (\n  \"bufio\"\n  \"fmt\"\n  \"os\"\n)\n\nfunc main() {\n  in := bufio.NewReader(os.Stdin)\n  _ = in\n  fmt.Println(\"\")\n}\n",
                    rust: "use std::io::{self, Read};\n\nfn main() {\n  let mut input = String::new();\n  io::stdin().read_to_string(&mut input).unwrap();\n  // your code\n}\n",
                    csharp: "using System;\n\npublic class Program {\n  public static void Main() {\n    // your code\n  }\n}\n",
                    kotlin: "import java.io.BufferedReader\nimport java.io.InputStreamReader\n\nfun main() {\n  val br = BufferedReader(InputStreamReader(System.`in`))\n  val input = br.readLine()\n  // your code\n}\n",
                    swift: "import Foundation\n\nlet data = String(data: FileHandle.standardInput.readDataToEndOfFile(), encoding: .utf8) ?? \"\"\n// your code\nprint(\"\")\n",
                    php: "<" + "?php\n$input = trim(stream_get_contents(STDIN));\n// your code\n?" + ">\n",
                    ruby: "input = STDIN.read\n# your code\n",
                    scala: "import scala.io.StdIn\n\nobject Main {\n  def main(args: Array[String]): Unit = {\n    val input = StdIn.readLine()\n    // your code\n  }\n}\n",
                    dart: "import 'dart:io';\n\nvoid main() {\n  final input = stdin.readLineSync() ?? '';\n  // your code\n}\n",
                    sql: "-- write your query here\n"
                };

                const editorSurface = document.getElementById('contestEditorSurface');
                const themeToggle = document.getElementById('editorThemeToggle');
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

                const taskListEl = document.getElementById('task-list');
                const taskTitleEl = document.getElementById('task-title');
                const taskDescEl = document.getElementById('task-description');
                const taskDiffEl = document.getElementById('task-diff');
                const taskInputEl = document.getElementById('task-input');
                const taskOutputEl = document.getElementById('task-output');
                const taskTimeLimitEl = document.getElementById('task-time-limit');
                const taskMemoryLimitEl = document.getElementById('task-memory-limit');
                const editorEl = document.getElementById('editor');
                const lineColEl = document.getElementById('editorLineCol');
                const saveStateEl = document.getElementById('editorSaveState');
                const wrapToggle = document.getElementById('wrapToggle');
                const fullscreenBtn = document.getElementById('editorFullscreenBtn');
                const wrapKey = 'tfEditorWrap';
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

                const getDraftKey = (taskId, lang) => `tfContestDraft:${contestId}:${taskId}:${lang}`;
                const loadDraft = (task, lang) => {
                    if (!editorEl || !task) return;
                    const key = getDraftKey(task.id, lang);
                    const stored = localStorage.getItem(key);
                    if (stored !== null) {
                        editorEl.value = stored;
                        setSaveState('draft');
                    } else {
                        editorEl.value = getStarter(task, lang);
                        setSaveState('saved');
                    }
                    updateLineCol();
                };

                const saveDraft = () => {
                    const task = getTask();
                    if (!editorEl || !task) return;
                    const key = getDraftKey(task.id, selectedLang);
                    localStorage.setItem(key, editorEl.value || '');
                    setSaveState('saved');
                };
                const testsEl = document.getElementById('tests-box');
                const verdictEl = document.getElementById('verdict');
                const userPointsEl = document.getElementById('user-points');
                const solutionFileEl = document.getElementById('solution-file');
                const allowedSolutionExtensions = ['txt', 'cpp', 'cc', 'c', 'h', 'hpp', 'py', 'java', 'cs', 'js', 'ts', 'go', 'rs', 'php', 'rb', 'kt', 'swift', 'scala', 'dart', 'sql'];
                const maxSolutionFileSize = 268435456;

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
                            if (copied) {
                                if (typeof window.tfNotify === 'function') window.tfNotify(i18n.copied);
                                return;
                            }
                        } catch (e) {}
                        if (typeof window.tfNotify === 'function') window.tfNotify(i18n.uploadError);
                    });
                }

                async function loadSolutionFile(file) {
                    if (!file || !editorEl) return;
                    const name = String(file.name || '');
                    const ext = name.includes('.') ? name.split('.').pop().toLowerCase() : '';
                    if (!allowedSolutionExtensions.includes(ext)) {
                        throw new Error(i18n.uploadInvalidType);
                    }
                    if (Number(file.size || 0) > maxSolutionFileSize) {
                        throw new Error(i18n.uploadTooLarge);
                    }
                    const text = await file.text();
                    if (text.includes('\u0000')) {
                        throw new Error(i18n.uploadInvalidType);
                    }
                    editorEl.value = text.replace(/\r\n?/g, '\n');
                    setSaveState('draft');
                    updateLineCol();
                    saveDraft();
                }

                function getTask() {
                    return visibleTasks[selectedTaskIndex] || null;
                }

                function getStarter(task, lang) {
                    if (task && task.starter && typeof task.starter[lang] === 'string' && task.starter[lang].trim() !== '') {
                        return task.starter[lang];
                    }
                    return defaultStarter[lang] || '';
                }

                function localizeDifficulty(diffRaw) {
                    const diff = String(diffRaw || '').toLowerCase();
                    if (diff === 'hard') return i18n.diffHard;
                    if (diff === 'medium') return i18n.diffMedium;
                    return i18n.diffEasy;
                }

                function syncLangSelect() {
                    if (languageSelect) languageSelect.value = selectedLang;
                }

                function applyDifficultyBadge(diffRaw) {
                    const diff = String(diffRaw || 'easy').toLowerCase();
                    taskDiffEl.textContent = localizeDifficulty(diff);
                    taskDiffEl.className = `diff-badge ${diff === 'hard' ? 'hard' : (diff === 'medium' ? 'medium' : 'easy')}`;
                }

                function resolveRank(points) {
                    const value = parseInt(points, 10) || 0;
                    if (value >= 2000) return { key: 'gold', label: 'Gold' };
                    if (value >= 1000) return { key: 'silver', label: 'Silver' };
                    if (value >= 500) return { key: 'bronze', label: 'Bronze' };
                    return { key: 'starter', label: 'Starter' };
                }

                function formatMemoryLimit(memoryKb) {
                    const value = parseInt(memoryKb, 10) || 0;
                    if (value >= 1024) {
                        return `${Math.round((value / 1024) * 10) / 10} MB`;
                    }
                    return `${value} KB`;
                }

                function getTaskLabel(idx) {
                    const letter = String.fromCharCode(65 + (idx % 26));
                    const suffix = idx >= 26 ? String(Math.floor(idx / 26) + 1) : '';
                    return `${letter}${suffix}`;
                }

                function renderTaskList() {
                    taskListEl.innerHTML = '';
                    visibleTasks.forEach((task, idx) => {
                        const active = idx === selectedTaskIndex;
                        const done = !!solved[String(task.id)];
                        const row = document.createElement('button');
                        row.type = 'button';
                        row.className = `task-row ${active ? 'item-active' : ''} ${done ? 'is-done' : 'is-pending'}`;
                        row.textContent = getTaskLabel(idx);
                        row.title = `${getTaskLabel(idx)}. ${String(task.title || '')}`.trim();
                        row.setAttribute('aria-label', `${getTaskLabel(idx)} ${String(task.title || '')}`.trim());
                        row.addEventListener('click', () => {
                            saveDraft();
                            selectedTaskIndex = idx;
                            loadTask();
                        });
                        taskListEl.appendChild(row);
                    });
                }

                function renderTests(results) {
                    testsEl.innerHTML = '';
                    const task = getTask();
                    if (!task) return;

                    const rows = Array.isArray(results) && results.length ? results : (task.tests || []).map(() => null);
                    rows.slice(0, 2).forEach((res, idx) => {
                        const t = (task.tests || [])[idx] || {};
                        const passed = res ? !!res.passed : null;
                        const st = passed === null ? '' : (passed ? i18n.ok : i18n.wa);
                        const cls = passed === null ? '' : (passed ? 'text-indigo-700' : 'text-indigo-900');
                        const input = (typeof t.in !== 'undefined') ? t.in : (typeof t.stdin !== 'undefined' ? t.stdin : '');
                        const expected = (typeof t.out !== 'undefined') ? t.out : (typeof t.expected_stdout !== 'undefined' ? t.expected_stdout : '');

                        const row = document.createElement('div');
                        row.className = 'test-card';
                        row.innerHTML = `<div class="flex items-center justify-between gap-3 flex-wrap"><div class="font-semibold text-indigo-800">${esc(i18n.test)} #${idx + 1}</div><div class="flex items-center gap-2">${passed === null ? `<button type="button" class="action-btn test-copy-btn">${esc(i18n.copy)}</button>` : `<div class="${cls} font-semibold">${esc(st)}</div>`}</div></div><div class="mt-2 text-indigo-600"><b>${esc(i18n.input)}:</b><pre>${esc(input)}</pre></div><div class="mt-1 text-indigo-600"><b>${esc(i18n.expected)}:</b><pre>${esc(expected)}</pre></div>`;
                        if (passed === null) {
                            attachCopyHandler(row.querySelector('.test-copy-btn'), getClipboardText(t));
                        }
                        testsEl.appendChild(row);
                    });
                }

                function loadTask() {
                    renderTaskList();
                    syncLangSelect();
                    const task = getTask();
                    if (!task) return;

                    const taskLabel = getTaskLabel(selectedTaskIndex);
                    taskTitleEl.textContent = task.title ? `${taskLabel}. ${task.title}` : taskLabel;
                    taskDescEl.textContent = task.description || '';
                    applyDifficultyBadge(task.difficulty || 'easy');
                    if (taskTimeLimitEl) taskTimeLimitEl.textContent = `${parseInt(task.time_limit_sec || 3, 10) || 3} sec`;
                    if (taskMemoryLimitEl) taskMemoryLimitEl.textContent = formatMemoryLimit(task.memory_limit_kb || 262144);
                    taskInputEl.textContent = task.input || '';
                    taskOutputEl.textContent = task.output || '';
                    loadDraft(task, selectedLang);
                    verdictEl.textContent = '';
                    verdictEl.className = 'verdict';
                    renderTests(null);
                }

                if (languageSelect) {
                    languageSelect.addEventListener('change', () => {
                        saveDraft();
                        selectedLang = languageSelect.value;
                        localStorage.setItem('tfEditorLang', selectedLang);
                        loadTask();
                    });
                }

                document.getElementById('reset-code')?.addEventListener('click', () => {
                    const task = getTask();
                    if (!task) return;
                    editorEl.value = getStarter(task, selectedLang);
                    setSaveState('saved');
                    saveDraft();
                    verdictEl.textContent = '';
                    verdictEl.className = 'verdict';
                    renderTests(null);
                });

                document.getElementById('run-check')?.addEventListener('click', () => {
                    const task = getTask();
                    if (!task) return;
                    if (runBtn) runBtn.disabled = true;
                    verdictEl.className = 'verdict';
                    verdictEl.textContent = i18n.waiting;
                    if (typeof window.tfNotify === 'function') {
                        window.tfNotify(i18n.sendingNotice, 'info');
                    }

                    fetch('?action=contest-submit', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({
                            contest_id: contestId,
                            task_id: task.id,
                            language: selectedLang,
                            code: editorEl.value || ''
                        })
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (!data || !data.success) {
                                throw new Error((data && data.message) ? data.message : i18n.serverError);
                            }

                            renderTests(Array.isArray(data.results) ? data.results : null);
                            if (data.passed) {
                                const taskKey = String(task.id);
                                if (!solved[taskKey]) {
                                    solved[taskKey] = true;
                                    solvedCount = Math.min(totalTaskCount, solvedCount + 1);
                                    if (solvedCountEl) solvedCountEl.textContent = String(solvedCount);
                                    if (progressPercentEl) {
                                        const percent = totalTaskCount > 0 ? Math.round((solvedCount / totalTaskCount) * 100) : 0;
                                        progressPercentEl.textContent = String(percent);
                                        if (progressFillEl) progressFillEl.style.width = `${percent}%`;
                                    }
                                }
                                verdictEl.className = 'verdict pass';
                                verdictEl.textContent = i18n.accepted;
                                if (userPointsEl && typeof data.total_points !== 'undefined') {
                                    userPointsEl.textContent = String(parseInt(data.total_points, 10) || 0);
                                }
                                const rankChipEl = document.getElementById('user-rank-chip');
                                if (rankChipEl) {
                                    const rankMeta = data.user_rank && data.user_rank.label ? data.user_rank : resolveRank(data.total_points);
                                    rankChipEl.textContent = String(rankMeta.label || 'Starter');
                                    rankChipEl.className = `rank-chip ${String(rankMeta.key || 'starter')}`;
                                }
                                if (typeof window.tfNotify === 'function') {
                                    const earned = parseInt(data.points_awarded || 0, 10) || 0;
                                    const limitText = `${i18n.timeLimit}: ${parseInt(data.time_limit_sec || task.time_limit_sec || 3, 10) || 3} sec, ${i18n.memoryLimit}: ${formatMemoryLimit(data.memory_limit_kb || task.memory_limit_kb || 262144)}`;
                                    window.tfNotify(`${i18n.successNotice}. +${earned} ${i18n.pointsEarned}. ${limitText}`, 'success');
                                }
                            } else {
                                verdictEl.className = 'verdict fail';
                                verdictEl.textContent = i18n.needFix;
                                if (typeof window.tfNotify === 'function') {
                                    const limitText = `${i18n.timeLimit}: ${parseInt(data.time_limit_sec || task.time_limit_sec || 3, 10) || 3} sec, ${i18n.memoryLimit}: ${formatMemoryLimit(data.memory_limit_kb || task.memory_limit_kb || 262144)}`;
                                    window.tfNotify(`${i18n.failNotice}. ${limitText}`, 'error');
                                }
                            }
                            renderTaskList();
                        })
                        .catch((e) => {
                            if (typeof window.tfNotify === 'function') {
                                window.tfNotify(e && e.message ? e.message : i18n.serverError, 'error');
                            }
                            verdictEl.className = 'verdict fail';
                            verdictEl.textContent = i18n.needFix;
                        })
                        .finally(() => {
                            if (runBtn) {
                                updateContestTimer();
                            }
                        });
                });

                if (solutionFileEl) {
                    solutionFileEl.addEventListener('change', async (event) => {
                        const file = event.target && event.target.files ? event.target.files[0] : null;
                        if (!file) return;
                        try {
                            await loadSolutionFile(file);
                            if (typeof window.tfNotify === 'function') {
                                window.tfNotify(i18n.uploadLoaded);
                            }
                        } catch (e) {
                            if (typeof window.tfNotify === 'function') {
                                window.tfNotify(e && e.message ? e.message : i18n.uploadError);
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
                        document.getElementById('run-check')?.click();
                    }
                });

                loadTask();
                updateContestTimer();
                setInterval(updateContestTimer, 1000);
            </script>
        <?php endif; ?>
    </main>
</body>

</html>

