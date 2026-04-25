<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$contests = $contests ?? [];
$contestLeaderboard = $contestLeaderboard ?? [];
$contestActivityUser = function_exists('getCurrentUser') ? (getCurrentUser() ?: []) : [];
$resolveContestRank = static function (int $points): array {
    if (function_exists('tfContestResolveUserRank')) {
        return tfContestResolveUserRank($points);
    }
    if ($points >= 2000) {
        return ['key' => 'gold', 'label' => 'Gold'];
    }
    if ($points >= 1000) {
        return ['key' => 'silver', 'label' => 'Silver'];
    }
    if ($points >= 500) {
        return ['key' => 'bronze', 'label' => 'Bronze'];
    }
    return ['key' => 'starter', 'label' => 'Starter'];
};
$contestHeatmapDays = 140;
$contestHeatmapStartTs = strtotime('-' . ($contestHeatmapDays - 1) . ' days');
$contestHeatmapStartTs = $contestHeatmapStartTs ?: time();
$contestHeatmapMap = !empty($contestActivityUser['id'])
    ? getContestActivityHeatmap((int) $contestActivityUser['id'], $contestHeatmapDays)
    : [];
if (empty($contestHeatmapMap)) {
    $contestHeatmapMap = [];
    for ($i = 0; $i < $contestHeatmapDays; $i++) {
        $date = date('Y-m-d', $contestHeatmapStartTs + ($i * 86400));
        $contestHeatmapMap[$date] = ['count' => 0, 'items' => []];
    }
}
$contestGridStartTs = $contestHeatmapStartTs;
$contestStartWeekday = (int) date('w', $contestGridStartTs);
if ($contestStartWeekday > 0) {
    $contestGridStartTs -= $contestStartWeekday * 86400;
}
$contestGridEndTs = $contestHeatmapStartTs + (($contestHeatmapDays - 1) * 86400);
$contestGridEndWeekday = (int) date('w', $contestGridEndTs);
$contestGridDays = $contestHeatmapDays + $contestStartWeekday + (6 - $contestGridEndWeekday);
$contestGridWeeks = (int) ceil($contestGridDays / 7);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(function_exists('currentLang') ? currentLang() : 'ru') ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('contest_page_title', 'Contests - CodeMaster') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=IBM+Plex+Sans:wght@400;500;600&display=swap"
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
        }

        body {
            margin: 0;
            font-family: 'IBM Plex Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(1000px 520px at 12% -6%, rgba(79, 70, 229, 0.18), transparent 60%),
                radial-gradient(900px 420px at 92% -8%, rgba(99, 102, 241, 0.14), transparent 55%),
                var(--bg);
        }

        h1,
        h2,
        h3 {
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: -0.02em;
        }

        .shell {
            max-width: 1240px;
            margin: 0 auto;
            padding: 2rem 1rem 2.4rem;
        }

        .hero {
            border-radius: 1.2rem;
            background: linear-gradient(140deg, var(--indigo-700) 0%, var(--indigo-600) 50%, var(--indigo-500) 100%);
            color: var(--indigo-50);
            padding: 1.2rem;
            box-shadow: 0 16px 40px rgba(67, 56, 202, 0.3);
        }

        .hero-title {
            font-size: clamp(1.55rem, 3.2vw, 2.35rem);
            margin: .4rem 0 .25rem;
            line-height: 1.08;
        }

        .hero-sub {
            color: rgba(240, 245, 255, 0.9);
            max-width: 65ch;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            border-radius: 999px;
            padding: .28rem .62rem;
            font-size: .76rem;
            font-weight: 700;
            border: 1px solid rgba(255, 255, 255, .28);
            background: rgba(255, 255, 255, .13);
        }

        .surface {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 1.05rem;
            box-shadow: 0 12px 28px rgba(16, 33, 38, 0.08);
        }

        .contest-card {
            display: block;
            text-decoration: none;
            color: inherit;
            border: 1px solid var(--line);
            border-radius: 1.1rem;
            padding: 1rem;
            background:
                radial-gradient(260px 120px at 12% -10%, rgba(99, 102, 241, 0.18), transparent 70%),
                linear-gradient(180deg, #fff, var(--indigo-50));
            position: relative;
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .contest-card:hover {
            transform: translateY(-2px);
            border-color: var(--indigo-300);
            box-shadow: 0 18px 36px rgba(67, 56, 202, .18);
        }

        .contest-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(255, 255, 255, 0.6), transparent 45%);
            opacity: 0;
            transition: opacity .2s ease;
            pointer-events: none;
        }

        .contest-card:hover::after {
            opacity: 1;
        }

        .contest-card .card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .75rem;
        }

        .contest-card .card-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--indigo-900);
        }

        .contest-card .card-desc {
            font-size: .92rem;
            color: var(--indigo-700);
            margin-top: .45rem;
            line-height: 1.45;
        }

        .card-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 999px;
            padding: .25rem .6rem;
            font-size: .72rem;
            font-weight: 700;
            color: var(--indigo-800);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(99, 102, 241, 0.2);
            white-space: nowrap;
        }

        .card-meta {
            margin-top: .85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .6rem;
            flex-wrap: wrap;
        }

        .card-status {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .75rem;
            font-weight: 700;
            color: var(--indigo-700);
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.2);
            padding: .2rem .55rem;
            border-radius: 999px;
        }

        .card-cta {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            font-size: .8rem;
            font-weight: 800;
            color: var(--accent);
        }

        .count-badge {
            font-size: .72rem;
            font-weight: 700;
            border-radius: 999px;
            color: var(--indigo-800);
            background: var(--accent-soft);
            padding: .2rem .5rem;
        }

        .open-link {
            color: var(--accent);
            font-weight: 700;
            font-size: .84rem;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .leader-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            border: 1px solid var(--line);
            border-radius: .78rem;
            padding: .56rem .66rem;
            background: #ffffff;
        }

        .rank {
            min-width: 1.55rem;
            height: 1.55rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .72rem;
            font-weight: 700;
            background: var(--indigo-100);
            color: var(--indigo-800);
        }

        .rank.top {
            color: var(--indigo-900);
            background: var(--indigo-200);
        }

        .score {
            font-weight: 800;
            color: var(--indigo-800);
            font-size: .92rem;
        }

        .rank-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: .22rem .55rem;
            font-size: .68rem;
            font-weight: 800;
            border: 1px solid transparent;
            margin-top: .22rem;
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

        .contest-activity-card {
            margin-top: 1rem;
        }

        .contest-activity-title {
            font-size: .95rem;
            font-weight: 800;
            color: var(--indigo-900);
        }

        .contest-activity-subtitle {
            font-size: .78rem;
            color: var(--indigo-600);
        }

        .contest-activity-grid {
            display: grid;
            grid-template-columns: repeat(<?= $contestGridWeeks ?>, minmax(0, 1fr));
            gap: 4px;
            margin-top: .9rem;
        }

        .contest-activity-col {
            display: grid;
            grid-template-rows: repeat(7, 1fr);
            gap: 4px;
        }

        .contest-activity-cell {
            width: 100%;
            aspect-ratio: 1 / 1;
            border: 1px solid transparent;
            border-radius: 4px;
            background: #e8edf8;
            padding: 0;
            cursor: pointer;
        }

        .contest-activity-cell.level-1 { background: #c7d2fe; }
        .contest-activity-cell.level-2 { background: #93c5fd; }
        .contest-activity-cell.level-3 { background: #60a5fa; }
        .contest-activity-cell.level-4 { background: #2563eb; }
        .contest-activity-cell.is-outside { background: transparent; cursor: default; }
        .contest-activity-cell.is-active { outline: 2px solid #1d4ed8; outline-offset: 1px; }

        .contest-activity-detail {
            margin-top: .9rem;
            border: 1px solid var(--line);
            border-radius: .9rem;
            padding: .8rem;
            background: #f8fbff;
        }

        .contest-activity-detail-title {
            font-size: .78rem;
            color: var(--indigo-700);
            margin-bottom: .45rem;
        }

        .contest-activity-detail-list {
            display: grid;
            gap: .45rem;
        }

        .contest-activity-detail-item {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            gap: .5rem;
            font-size: .8rem;
        }

        .contest-activity-tooltip {
            position: fixed;
            z-index: 90;
            min-width: 220px;
            max-width: min(360px, calc(100vw - 24px));
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(15, 23, 42, 0.96);
            color: #f8fafc;
            box-shadow: 0 18px 44px rgba(15, 23, 42, 0.28);
            pointer-events: none;
            opacity: 0;
            transform: translateY(4px);
            transition: opacity .14s ease, transform .14s ease;
        }

        .contest-activity-tooltip.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .contest-activity-tooltip-title {
            font-size: 12px;
            font-weight: 700;
            color: #c7d2fe;
            margin-bottom: 8px;
        }

        .contest-activity-tooltip-list {
            display: grid;
            gap: 6px;
        }

        .contest-activity-tooltip-item {
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr);
            gap: 8px;
            font-size: 12px;
        }

        @media (max-width: 640px) {
            .contest-activity-grid {
                gap: 3px;
            }
        }

        @media (min-width: 1200px) {
            .layout {
                display: grid;
                grid-template-columns: 1.2fr .8fr;
                gap: 1rem;
            }

            .sticky-side {
                position: sticky;
                top: 1rem;
                align-self: start;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main class="shell">
        <section class="hero mb-4">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="hero-title"><?= t('contest_list', 'Contests') ?></h1>
                    <p class="hero-sub">
                        <?= t('contest_subtitle', 'Choose a contest and solve algorithmic tasks in a competitive format.') ?>
                    </p>
                    <div class="flex items-center gap-2 mt-3 flex-wrap">
                        <span class="hero-chip"><i class="fas fa-flag"></i><?= count($contests) ?>
                            <?= t('contest_list', 'contests') ?></span>
                        <span class="hero-chip"><i class="fas fa-ranking-star"></i><?= count($contestLeaderboard) ?>
                            <?= t('contest_leaderboard', 'leaderboard') ?></span>
                    </div>
                </div>
            </div>
        </section>

        <div class="layout">
            <section class="surface p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-xl font-bold"><?= t('contest_list', 'Contests') ?></h2>
                    <span class="text-sm text-indigo-500"><?= count($contests) ?></span>
                </div>

                <?php if (empty($contests)): ?>
                    <div class="rounded-xl border border-dashed border-indigo-300 p-8 text-center text-indigo-500">
                        <?= t('contest_empty', 'No available contests yet.') ?>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($contests as $contest): ?>
                            <a href="?action=contest&id=<?= (int) ($contest['id'] ?? 0) ?>" class="contest-card">
                                <div class="card-head">
                                    <div>
                                        <h3 class="card-title">
                                            <?= htmlspecialchars((string) ($contest['title'] ?? '')) ?>
                                        </h3>
                                        <p class="card-desc">
                                            <?= htmlspecialchars((string) ($contest['description'] ?? '')) ?>
                                        </p>
                                    </div>
                                    <span class="card-pill">
                                        <i class="fas fa-list-check"></i>
                                        <?= (int) ($contest['tasks_count'] ?? 0) ?> <?= t('contest_tasks', 'tasks') ?>
                                    </span>
                                </div>
                                <div class="card-meta">
                                    <span class="card-status">
                                        <i class="fas fa-sparkles"></i>
                                        <?= ((int) ($contest['tasks_count'] ?? 0) > 0) ? t('contest_ready', 'Ready to start') : t('contest_no_task', 'No tasks yet') ?>
                                    </span>
                                    <span class="card-cta"><?= t('contest_open', 'Open contest') ?><i
                                            class="fas fa-arrow-right"></i></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <aside class="surface p-4 sm:p-5 sticky-side mt-4 xl:mt-0">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-xl font-bold"><?= t('contest_leaderboard', 'Leaderboard') ?></h2>
                    <i class="fas fa-trophy text-indigo-300"></i>
                </div>

                <?php if (empty($contestLeaderboard)): ?>
                    <div class="rounded-xl border border-dashed border-indigo-300 p-6 text-center text-sm text-indigo-500">
                        <?= t('contest_leaderboard_empty', 'No participants yet.') ?>
                    </div>
                <?php else: ?>
                    <div class="space-y-2">
                        <div class="leader-row text-xs font-semibold uppercase tracking-[0.12em] text-indigo-400">
                            <div class="min-w-0 flex items-center gap-2">
                                <span class="rank">#</span>
                                <div class="truncate"><?= t('common_user', 'User') ?></div>
                            </div>
                            <div class="text-right"><?= t('contest_points', 'Points') ?></div>
                        </div>
                        <?php foreach (array_slice($contestLeaderboard, 0, 20) as $idx => $row): ?>
                            <?php $rowRank = $resolveContestRank((int) ($row['contest_points'] ?? 0)); ?>
                            <div class="leader-row">
                                <div class="min-w-0 flex items-center gap-2">
                                    <span class="rank <?= $idx < 3 ? 'top' : '' ?>"><?= $idx + 1 ?></span>
                                    <div class="min-w-0">
                                        <div class="truncate text-sm text-indigo-800">
                                            <?= htmlspecialchars((string) ($row['name'] ?? t('common_user', 'Пользователь'))) ?>
                                        </div>
                                        <span class="rank-chip <?= htmlspecialchars((string) ($rowRank['key'] ?? 'starter')) ?>"><?= htmlspecialchars((string) ($rowRank['label'] ?? 'Starter')) ?></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="score"><?= (int) ($row['contest_points'] ?? 0) ?></div>
                                    <div class="text-[11px] text-indigo-400">
                                        <?= (int) ($row['attempts_count'] ?? 0) ?> <?= t('contest_attempts_label', 'Attempts') ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="contest-activity-card">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <div class="contest-activity-title"><?= t('profile_activity_contest', 'Contests') ?></div>
                            <div class="contest-activity-subtitle"><?= t('profile_activity_year', 'Last 12 months') ?></div>
                        </div>
                        <i class="fas fa-fire text-indigo-300"></i>
                    </div>
                    <div class="contest-activity-grid" id="contestActivityGrid">
                        <?php for ($w = 0; $w < $contestGridWeeks; $w++): ?>
                            <div class="contest-activity-col">
                                <?php for ($d = 0; $d < 7; $d++): ?>
                                    <?php
                                    $dayIndex = ($w * 7) + $d;
                                    $dayTs = $contestGridStartTs + ($dayIndex * 86400);
                                    $dateKey = date('Y-m-d', $dayTs);
                                    $inRange = $dayTs >= $contestHeatmapStartTs && $dayTs <= $contestGridEndTs;
                                    $count = $inRange ? (int) ($contestHeatmapMap[$dateKey]['count'] ?? 0) : 0;
                                    $level = 0;
                                    if ($count >= 1 && $count < 2) {
                                        $level = 1;
                                    } elseif ($count >= 2 && $count < 4) {
                                        $level = 2;
                                    } elseif ($count >= 4 && $count < 7) {
                                        $level = 3;
                                    } elseif ($count >= 7) {
                                        $level = 4;
                                    }
                                    ?>
                                    <button type="button"
                                        class="contest-activity-cell level-<?= $level ?><?= $inRange ? '' : ' is-outside' ?>"
                                        data-date="<?= htmlspecialchars($dateKey) ?>"
                                        <?= $inRange ? '' : 'tabindex="-1" aria-hidden="true"' ?>></button>
                                <?php endfor; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <div class="contest-activity-detail">
                        <div class="contest-activity-detail-title" data-contest-activity-title><?= t('profile_activity_pick_day', 'Select a day to see details') ?></div>
                        <div class="contest-activity-detail-list" data-contest-activity-list></div>
                    </div>
                </div>
            </aside>
        </div>
    </main>
    <script>
        (function () {
            const activityMap = <?= json_encode($contestHeatmapMap, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            const localeMap = { ru: 'ru-RU', en: 'en-US', tg: 'tg-TJ' };
            const activityLocale = localeMap['<?= htmlspecialchars(function_exists('currentLang') ? currentLang() : 'ru') ?>'] || 'ru-RU';
            const labels = {
                pick: '<?= t('profile_activity_pick_day', 'Select a day to see details') ?>',
                count: '<?= t('profile_activity_count', 'activities') ?>',
                empty: '<?= t('profile_activity_no_entries', 'No activity') ?>',
            };
            const grid = document.getElementById('contestActivityGrid');
            if (!grid) return;
            const detailTitle = document.querySelector('[data-contest-activity-title]');
            const detailList = document.querySelector('[data-contest-activity-list]');
            const tooltip = document.createElement('div');
            tooltip.className = 'contest-activity-tooltip';
            document.body.appendChild(tooltip);

            function formatSummary(dateKey, count) {
                const formattedDate = new Date(`${dateKey}T00:00:00`).toLocaleDateString(activityLocale, {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                return `${formattedDate} / ${count} ${labels.count}`;
            }

            function renderDetail(dateKey, items) {
                if (!detailTitle || !detailList) return;
                detailList.innerHTML = '';
                if (!dateKey) {
                    detailTitle.textContent = labels.pick;
                    return;
                }
                detailTitle.textContent = formatSummary(dateKey, items.length);
                if (!items.length) {
                    const empty = document.createElement('div');
                    empty.textContent = labels.empty;
                    detailList.appendChild(empty);
                    return;
                }
                items.forEach((item) => {
                    const row = document.createElement('div');
                    row.className = 'contest-activity-detail-item';
                    const time = document.createElement('div');
                    time.textContent = item.time || '';
                    const text = document.createElement('div');
                    text.textContent = item.text || '';
                    row.appendChild(time);
                    row.appendChild(text);
                    detailList.appendChild(row);
                });
            }

            function hideTooltip() {
                tooltip.classList.remove('is-visible');
            }

            function showTooltip(anchor, dateKey, items) {
                tooltip.innerHTML = '';
                const title = document.createElement('div');
                title.className = 'contest-activity-tooltip-title';
                title.textContent = formatSummary(dateKey, items.length);
                tooltip.appendChild(title);
                const list = document.createElement('div');
                list.className = 'contest-activity-tooltip-list';
                if (!items.length) {
                    const empty = document.createElement('div');
                    empty.textContent = labels.empty;
                    list.appendChild(empty);
                } else {
                    items.forEach((item) => {
                        const row = document.createElement('div');
                        row.className = 'contest-activity-tooltip-item';
                        const time = document.createElement('div');
                        time.textContent = item.time || '';
                        const text = document.createElement('div');
                        text.textContent = item.text || '';
                        row.appendChild(time);
                        row.appendChild(text);
                        list.appendChild(row);
                    });
                }
                tooltip.appendChild(list);
                tooltip.classList.add('is-visible');
                const rect = anchor.getBoundingClientRect();
                const tooltipRect = tooltip.getBoundingClientRect();
                let left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                let top = rect.top - tooltipRect.height - 10;
                if (left < 8) left = 8;
                if (left + tooltipRect.width > window.innerWidth - 8) left = window.innerWidth - tooltipRect.width - 8;
                if (top < 8) top = rect.bottom + 10;
                tooltip.style.left = `${left}px`;
                tooltip.style.top = `${top}px`;
            }

            renderDetail('', []);
            grid.querySelectorAll('.contest-activity-cell[data-date]').forEach((cell) => {
                if (cell.classList.contains('is-outside')) return;
                cell.addEventListener('click', () => {
                    grid.querySelectorAll('.contest-activity-cell.is-active').forEach((active) => active.classList.remove('is-active'));
                    cell.classList.add('is-active');
                    const dateKey = cell.dataset.date;
                    const entry = activityMap[dateKey] || { items: [] };
                    renderDetail(dateKey, entry.items || []);
                });
                const show = () => {
                    const dateKey = cell.dataset.date;
                    const entry = activityMap[dateKey] || { items: [] };
                    showTooltip(cell, dateKey, entry.items || []);
                };
                cell.addEventListener('mouseenter', show);
                cell.addEventListener('focus', show);
                cell.addEventListener('mouseleave', hideTooltip);
                cell.addEventListener('blur', hideTooltip);
            });
            window.addEventListener('scroll', hideTooltip, true);
            window.addEventListener('resize', hideTooltip);
        })();
    </script>
</body>

</html>

