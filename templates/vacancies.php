<?php if (!defined('APP_INIT')) die('Direct access not permitted'); ?>
<?php
$typeFilter = $typeFilter ?? '';
$vacancies = $vacancies ?? [];
$search = $search ?? '';
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$totalVacancies = $totalVacancies ?? count($vacancies);
$isEmployer = !empty($user) && in_array(($user['role'] ?? ''), ['admin', 'recruiter'], true);
$isSeeker = !empty($user) && (($user['role'] ?? '') === 'seeker');
$currentUserId = (int) ($user['id'] ?? 0);
$appliedMap = [];
if (!empty($user['applications'])) {
    foreach ($user['applications'] as $app) {
        $appliedMap[(int)$app['vacancy_id']] = (int)$app['id'];
    }
}
$salaryMins = $_GET['salary_min'] ?? [];
if (!is_array($salaryMins)) { $salaryMins = [$salaryMins]; }
$skillsFilter = $_GET['skills'] ?? [];
if (!is_array($skillsFilter)) { $skillsFilter = [$skillsFilter]; }
$formatSalary = static function ($value) {
    $num = (int)$value;
    if ($num <= 0) {
        return null;
    }
    return number_format($num, 0, '.', ' ');
};
$buildUrl = function ($overrides = []) use ($typeFilter, $search, $salaryMins, $skillsFilter) {
    $params = array_merge(
        ['action' => 'vacancies', 'type' => $typeFilter, 'search' => $search, 'salary_min' => $salaryMins, 'skills' => $skillsFilter],
        $overrides
    );
    $params = array_filter($params, function ($v) {
        if (is_array($v)) {
            return !empty($v);
        }
        return $v !== '' && $v !== null;
    });
    return '?' . http_build_query($params);
};
$vacancyExcerpt = static function ($text, $limit = 150) {
    $normalized = normalizeMojibakeText((string) $text);
    $normalized = str_replace("\u{FFFD}", '', $normalized);
    $normalized = trim((string) preg_replace('/\s+/u', ' ', $normalized));
    if ($normalized === '') {
        return '';
    }

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($normalized, 'UTF-8') <= $limit) {
            return $normalized;
        }
        return mb_substr($normalized, 0, $limit, 'UTF-8') . '...';
    }

    if (strlen($normalized) <= $limit) {
        return $normalized;
    }
    return substr($normalized, 0, $limit) . '...';
};

require_once __DIR__ . '/../includes/telegram_feed.php';
$vacancyChannelFeed = tfTelegramFeedGetPosts('Career1ink', 6, 900);
$vacancyChannelPosts = is_array($vacancyChannelFeed['posts'] ?? null) ? $vacancyChannelFeed['posts'] : [];
$vacancyChannelStale = !empty($vacancyChannelFeed['stale']);
$vacancyChannelLabels = [
    'ru' => [
        'title' => 'Вакансии из Telegram',
        'subtitle' => 'Публикации из канала t.me/Career1ink',
        'open_channel' => 'Открыть канал',
        'open_post' => 'Перейти к публикации',
        'stale' => 'Показаны кэшированные публикации.',
        'empty' => 'Пока нет доступных публикаций из канала.'
    ],
    'en' => [
        'title' => 'Telegram Vacancies',
        'subtitle' => 'Posts from channel t.me/Career1ink',
        'open_channel' => 'Open channel',
        'open_post' => 'Open post',
        'stale' => 'Showing cached posts.',
        'empty' => 'No channel posts are available yet.'
    ],
    'tg' => [
        'title' => 'Вакансияҳо аз Telegram',
        'subtitle' => 'Нашрияҳо аз канали t.me/Career1ink',
        'open_channel' => 'Каналро кушодан',
        'open_post' => 'Кушодани нашрия',
        'stale' => 'Нашрияҳои кэшшуда нишон дода мешаванд.',
        'empty' => 'Ҳоло нашрияҳои канал дастрас нестанд.'
    ]
];
$vacancyChannelUi = $vacancyChannelLabels[currentLang()] ?? $vacancyChannelLabels['ru'];
$vacancyFeedDate = static function ($isoDate, $fallback = '') {
    $isoDate = trim((string) $isoDate);
    if ($isoDate === '') {
        return trim((string) $fallback);
    }
    try {
        $dt = new DateTime($isoDate);
        return $dt->format('Y-m-d H:i');
    } catch (Throwable $e) {
        return trim((string) $fallback);
    }
};
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">
<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('vacancies_page_title') ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.0/dist/cdn.min.js" defer></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            50: "#f0f5ff",
                            100: "#e0e7ff",
                            200: "#c7d2fe",
                            300: "#a5b4fc",
                            400: "#818cf8",
                            500: "#6366f1",
                            600: "#4f46e5",
                            700: "#4338ca",
                            800: "#3730a3",
                            900: "#312e81",
                        },
                    },
                },
            },
        };
    </script>

	    <style>
	        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");
            :root {
                --tf-brand: #6366f1;
                --tf-brand-strong: #4f46e5;
                --tf-border-strong: rgba(99, 102, 241, 0.22);
                --tf-page-glow:
                    radial-gradient(circle at top left, rgba(99, 102, 241, 0.10), transparent 28%),
                    radial-gradient(circle at top right, rgba(129, 140, 248, 0.08), transparent 24%),
                    linear-gradient(180deg, #f8fafc 0%, #f9fafb 100%);
                --tf-surface-strong: rgba(255, 255, 255, 0.94);
                --tf-shadow-soft: 0 18px 40px rgba(15, 23, 42, 0.08);
                --tf-shadow-card: 0 22px 48px rgba(15, 23, 42, 0.12);
            }
	        html,
	        body {
	            max-width: 100%;
	            overflow-x: hidden;
	            font-family: "Inter", sans-serif;
	            background: var(--tf-page-glow);
	            color: #0f172a;
	            line-height: 1.6;
                margin: 0;
            }
        .card {
            background: var(--tf-surface-strong);
            border-radius: 24px;
            border: 1px solid rgba(148, 163, 184, 0.24);
            box-shadow: var(--tf-shadow-soft);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
            backdrop-filter: blur(16px);
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--tf-shadow-card);
            border-color: var(--tf-border-strong);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--tf-brand), var(--tf-brand-strong));
            color: white;
            padding: 12px 18px;
            border-radius: 14px;
            font-weight: 700;
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 16px 34px rgba(79, 70, 229, 0.22);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            filter: saturate(1.05);
        }
        .input-field {
            padding: 12px 14px;
            border: 1px solid rgba(148, 163, 184, 0.28);
            border-radius: 14px;
            width: 100%;
            background: rgba(255, 255, 255, 0.92);
            transition: all 0.2s;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }
        .input-field:focus {
            outline: none;
            border-color: rgba(99, 102, 241, 0.34);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }
        .vacancy-card {
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
            border-left: 4px solid var(--tf-brand);
        }
        .vacancy-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--tf-shadow-card);
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .skills-tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            margin: 4px;
            border-radius: 20px;
            background: rgba(99, 102, 241, 0.10);
            color: #3730a3;
            font-size: 13px;
            font-weight: 700;
            transition: all 0.2s;
        }
        .notification-badge {
            animation: pulse 2s infinite;
        }
        .search-input {
            padding-left: 40px;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.45); }
            70% { box-shadow: 0 0 0 10px rgba(99, 102, 241, 0); }
            100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
        }
        .tg-vacancy-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            border: 1px solid rgba(148, 163, 184, 0.24);
            border-radius: 20px;
            background: rgba(255,255,255,0.96);
            box-shadow: var(--tf-shadow-soft);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
            overflow: hidden;
        }
        .tg-vacancy-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--tf-shadow-card);
            border-color: var(--tf-border-strong);
        }
        .tg-vacancy-image {
            width: 100%;
            aspect-ratio: 16 / 9;
            height: auto;
            object-fit: contain;
            background: #f8fafc;
            border-bottom: 1px solid rgba(148, 163, 184, 0.18);
        }
        .tg-vacancy-text {
            flex: 1 1 auto;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 3.8rem;
        }
        .vacancies-hero {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background:
                radial-gradient(circle at top left, rgba(99, 102, 241, 0.12), transparent 30%),
                radial-gradient(circle at top right, rgba(129, 140, 248, 0.12), transparent 26%),
                rgba(255,255,255,0.94);
        }
        .vacancies-title {
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            line-height: 1.05;
            letter-spacing: -0.04em;
        }
        .vacancies-section-accent {
            border: 1px solid rgba(99, 102, 241, 0.12);
            background: linear-gradient(135deg, rgba(238,242,255,0.96), rgba(255,255,255,0.96));
        }
        @media (max-width: 768px) {
            .card,
            .tg-vacancy-card {
                border-radius: 18px;
            }
            .vacancies-hero {
                padding: 1rem;
            }
            .vacancy-card:hover,
            .tg-vacancy-card:hover,
            .card:hover {
                transform: none;
            }
        }
    </style>
</head>
<body class="m-0 tf-platform-page">
    <?php include 'includes/header.php'; ?>

<!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <section class="card vacancies-hero">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                <div>
                    <span class="tf-platform-kicker"><i class="fas fa-briefcase"></i><?= t('vacancies_heading') ?></span>
                    <h1 class="vacancies-title font-extrabold text-slate-900 mt-3"><?= t('vacancies_heading') ?></h1>
                    <p class="mt-3 text-sm sm:text-base text-slate-600 max-w-2xl"><?= t('vacancies_hero_subtitle', 'Актуальные вакансии, Telegram-публикации и быстрый отклик в одном интерфейсе.') ?></p>
                </div>
                <div class="flex flex-wrap gap-2 text-sm text-slate-600">
                    <span class="tf-platform-chip"><i class="fas fa-bolt"></i><?= (int) $totalVacancies ?> <?= t('vacancies_heading', 'Vacancies') ?></span>
                    <span class="tf-platform-chip"><i class="fab fa-telegram-plane"></i><?= count($vacancyChannelPosts) ?> Telegram</span>
                </div>
            </div>
        </section>
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar -->
            <div class="lg:w-64 flex-shrink-0" x-data="{ filtersOpen: window.innerWidth >= 1024 }"
                @resize.window="if (window.innerWidth >= 1024) filtersOpen = true">
                <button type="button"
                    class="lg:hidden w-full mb-3 px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-700 font-medium text-sm flex items-center justify-between"
                    @click="filtersOpen = !filtersOpen">
                    <span><i class="fas fa-filter mr-2"></i><?= t('vacancies_type') ?></span>
                    <i class="fas" :class="filtersOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <div class="card sticky top-8" :class="filtersOpen ? 'block' : 'hidden lg:block'">
                    <div class="p-4">
                        <form method="GET" action="">
                            <input type="hidden" name="action" value="vacancies">
                            <input type="hidden" name="type" value="<?= htmlspecialchars($typeFilter) ?>">
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                            <input type="hidden" name="page" value="1">
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-filter mr-2"></i> <?= t('vacancies_type') ?>
                        </h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="<?= $buildUrl(['type' => '']) ?>" 
                                   class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50
                                   <?= $typeFilter === '' ? 'bg-indigo-50 text-indigo-600 font-medium' : '' ?>">
                                    <i class="fas fa-list mr-2"></i> <?= t('vacancies_all_types') ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $buildUrl(['type' => 'remote']) ?>" 
                                   class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50
                                   <?= $typeFilter === 'remote' ? 'bg-indigo-50 text-indigo-600 font-medium' : '' ?>">
                                    <i class="fas fa-laptop-house text-indigo-600 mr-2"></i> <?= t('vacancies_remote') ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $buildUrl(['type' => 'office']) ?>" 
                                   class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50
                                   <?= $typeFilter === 'office' ? 'bg-indigo-50 text-indigo-600 font-medium' : '' ?>">
                                    <i class="fas fa-building text-indigo-600 mr-2"></i> <?= t('vacancies_office') ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $buildUrl(['type' => 'hybrid']) ?>" 
                                   class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50
                                   <?= $typeFilter === 'hybrid' ? 'bg-indigo-50 text-indigo-600 font-medium' : '' ?>">
                                    <i class="fas fa-exchange-alt text-indigo-600 mr-2"></i> <?= t('vacancies_hybrid') ?>
                                </a>
                            </li>
                        </ul>
                        <div class="mt-6 border-t pt-4">
                            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-money-bill-wave mr-2"></i> <?= t('vacancies_salary') ?>
                            </h3>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="salary_min[]" value="100000"
                                           <?= in_array('100000', $salaryMins, true) ? 'checked' : '' ?>
                                           class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                    <span class="ml-2 text-sm text-gray-700"><?= t('vacancies_salary_from_100') ?></span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="salary_min[]" value="150000"
                                           <?= in_array('150000', $salaryMins, true) ? 'checked' : '' ?>
                                           class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                    <span class="ml-2 text-sm text-gray-700"><?= t('vacancies_salary_from_150') ?></span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="salary_min[]" value="200000"
                                           <?= in_array('200000', $salaryMins, true) ? 'checked' : '' ?>
                                           class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                    <span class="ml-2 text-sm text-gray-700"><?= t('vacancies_salary_from_200') ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="mt-6 border-t pt-4">
                            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-star mr-2"></i> <?= t('vacancies_skills') ?>
                            </h3>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                                <?php
                                $allSkills = ['JavaScript', 'React', 'Node.js', 'Python', 'HTML/CSS', 'TypeScript', 'SQL', 'AWS', 'Judge0', 'Git', 'Java', 'C#', 'PHP'];
                                foreach ($allSkills as $skill): ?>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="skills[]" value="<?= htmlspecialchars($skill) ?>"
                                               <?= in_array($skill, $skillsFilter, true) ? 'checked' : '' ?>
                                               class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                        <span class="ml-2 text-sm text-gray-700"><?= htmlspecialchars($skill) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="w-full btn-primary"><?= t('vacancies_apply_filters') ?></button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Main Content -->
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-4 sm:mb-0 flex items-center">
                        <i class="fas fa-briefcase mr-2"></i> <?= t('vacancies_heading') ?>
                    </h1>
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="relative w-full sm:w-64">
                            <form method="GET" action="" class="relative w-full">
                                <input type="hidden" name="action" value="vacancies">
                                <input type="hidden" name="type" value="<?= htmlspecialchars($typeFilter) ?>">
                                <input type="hidden" name="page" value="1">
                                <?php foreach ($salaryMins as $salaryMin): ?>
                                    <input type="hidden" name="salary_min[]" value="<?= htmlspecialchars($salaryMin) ?>">
                                <?php endforeach; ?>
                                <?php foreach ($skillsFilter as $skillFilter): ?>
                                    <input type="hidden" name="skills[]" value="<?= htmlspecialchars($skillFilter) ?>">
                                <?php endforeach; ?>
                                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                       placeholder="<?= t('vacancies_search_placeholder') ?>"
                                       class="search-input input-field w-full"
                                       aria-label="<?= t('vacancies_search_aria') ?>">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <i class="fas fa-search"></i>
                                </span>
                            </form>
                        </div>
                        <?php if ($isEmployer): ?>
                            <button onclick="showCreateVacancyForm()"
                                    class="w-full sm:w-auto btn-primary flex items-center justify-center">
                                <i class="fas fa-plus mr-2"></i> <?= t('vacancies_post') ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <section class="card vacancies-section-accent p-4 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fab fa-telegram-plane text-sky-600 mr-2"></i>
                                <?= htmlspecialchars($vacancyChannelUi['title']) ?>
                            </h2>
                            <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($vacancyChannelUi['subtitle']) ?></p>
                        </div>
                        <a href="https://t.me/Career1ink" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center px-3 py-2 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700">
                            <i class="fas fa-arrow-up-right-from-square mr-2 text-xs"></i><?= htmlspecialchars($vacancyChannelUi['open_channel']) ?>
                        </a>
                    </div>

                    <?php if ($vacancyChannelStale): ?>
                        <div class="mt-3 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                            <?= htmlspecialchars($vacancyChannelUi['stale']) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($vacancyChannelPosts)): ?>
                        <div class="mt-3 text-sm text-gray-600"><?= htmlspecialchars($vacancyChannelUi['empty']) ?></div>
                    <?php else: ?>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 items-stretch">
                            <?php foreach ($vacancyChannelPosts as $tgPost): ?>
                                <?php
                                $tgPostUrl = (string) ($tgPost['url'] ?? 'https://t.me/Career1ink');
                                $tgPostText = (string) ($tgPost['excerpt'] ?? '');
                                $tgPostImage = trim((string) ($tgPost['image'] ?? ''));
                                $tgDateLabel = $vacancyFeedDate((string) ($tgPost['datetime'] ?? ''), (string) ($tgPost['time_label'] ?? ''));
                                ?>
                                <article class="tg-vacancy-card overflow-hidden">
                                    <?php if ($tgPostImage !== ''): ?>
                                        <a href="<?= htmlspecialchars($tgPostUrl) ?>" target="_blank" rel="noopener noreferrer">
                                            <img src="<?= htmlspecialchars($tgPostImage) ?>" alt="Telegram vacancy" class="tg-vacancy-image" loading="eager" decoding="async">
                                        </a>
                                    <?php endif; ?>
                                    <div class="p-3">
                                        <div class="text-xs text-gray-500 flex items-center justify-between mb-2">
                                            <span class="text-sky-700 font-semibold">t.me/Career1ink</span>
                                            <?php if ($tgDateLabel !== ''): ?>
                                                <span><?= htmlspecialchars($tgDateLabel) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="tg-vacancy-text text-sm text-gray-700">
                                            <?= htmlspecialchars($tgPostText !== '' ? $tgPostText : '...') ?>
                                        </p>
                                        <a href="<?= htmlspecialchars($tgPostUrl) ?>" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center mt-3 text-sm font-semibold text-sky-700 hover:text-sky-900">
                                            <?= htmlspecialchars($vacancyChannelUi['open_post']) ?>
                                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Create Vacancy Modal -->
                <?php if ($isEmployer): ?>
                <div id="createVacancyModal" class="fixed inset-0 z-50 hidden">
                    <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="hideCreateVacancyForm()"></div>
                    <div class="relative max-w-4xl mx-auto mt-10 mb-10 bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-edit mr-2"></i> <?= t('vacancies_create_title') ?>
                            </h2>
                            <button type="button" onclick="hideCreateVacancyForm()" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="p-6 max-h-[75vh] overflow-y-auto">
                            <form id="vacancyForm" onsubmit="event.preventDefault(); createVacancy()">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="vacancy-title" class="block text-sm font-medium text-gray-700 mb-1"><?= t('vacancies_field_title') ?></label>
                                        <input id="vacancy-title" type="text" required class="w-full input-field" />
                                    </div>
                                    <div>
                                        <label for="vacancy-company" class="block text-sm font-medium text-gray-700 mb-1"><?= t('vacancies_field_company') ?></label>
                                        <input id="vacancy-company" type="text" required class="w-full input-field" />
                                    </div>
                                    <div>
                                        <label for="vacancy-location" class="block text-sm font-medium text-gray-700 mb-1"><?= t('vacancies_field_location') ?></label>
                                        <input id="vacancy-location" type="text" required class="w-full input-field" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1"><?= t('vacancies_type') ?></label>
                                        <select id="vacancy-type" class="mt-1 block w-full input-field">
                                            <option value="remote"><?= t('vacancies_remote') ?></option>
                                            <option value="office"><?= t('vacancies_office') ?></option>
                                            <option value="hybrid"><?= t('vacancies_hybrid') ?></option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1"><?= t('vacancies_salary') ?></label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-500 mb-1"><?= t('vacancies_salary_min') ?></label>
                                                <input type="number" id="vacancy-salary-min" placeholder="100000" class="w-full input-field" />
                                            </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1"><?= t('vacancies_salary_max') ?></label>
                                        <input type="number" id="vacancy-salary-max" placeholder="200000" class="w-full input-field" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1"><?= t('admin_salary_currency') ?></label>
                                        <select id="vacancy-salary-currency" class="w-full input-field">
                                            <option value="TJS"><?= t('currency_tjs') ?></option>
                                            <option value="RUB"><?= t('currency_rub') ?></option>
                                            <option value="USD"><?= t('currency_usd') ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1"><?= t('vacancies_skills') ?></label>
                                        <div class="flex">
                                            <input type="text" id="vacancy-skill-input" placeholder="<?= t('vacancies_skill_placeholder') ?>"
                                                   class="flex-1 input-field rounded-r-none" />
                                            <button type="button" onclick="addVacancySkill()"
                                                    class="px-4 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <div id="vacancy-skills-container" class="mt-3 flex flex-wrap"></div>
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <label for="vacancy-description" class="block text-sm font-medium text-gray-700 mb-1"><?= t('vacancies_description') ?></label>
                                    <textarea id="vacancy-description" rows="4" required class="w-full input-field"></textarea>
                                    <p class="mt-2 text-xs text-gray-500">
                                        <?= t('vacancies_description_words_hint', 'Для красивой карточки: от 12 до 24 слов.') ?>
                                    </p>
                                    <p id="vacancy-description-word-count" class="mt-1 text-xs text-gray-400">
                                        <?= t('vacancies_description_words_count', 'Слов: 0') ?>
                                    </p>
                                </div>
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= t('vacancies_requirements') ?></label>
                                    <div id="requirements-list" class="space-y-2"></div>
                                    <div class="mt-2 flex">
                                        <input type="text" id="requirement-input" placeholder="<?= t('vacancies_requirement_placeholder') ?>"
                                               class="flex-1 input-field rounded-r-none" />
                                        <button type="button" onclick="addRequirement()"
                                                class="px-4 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= t('vacancies_pluses') ?></label>
                                    <div id="pluses-list" class="space-y-2"></div>
                                    <div class="mt-2 flex">
                                        <input type="text" id="plus-input" placeholder="<?= t('vacancies_plus_placeholder') ?>"
                                               class="flex-1 input-field rounded-r-none" />
                                        <button type="button" onclick="addPlus()"
                                                class="px-4 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= t('vacancies_responsibilities') ?></label>
                                    <div id="responsibilities-list" class="space-y-2"></div>
                                    <div class="mt-2 flex">
                                        <input type="text" id="responsibility-input" placeholder="<?= t('vacancies_responsibility_placeholder') ?>"
                                               class="flex-1 input-field rounded-r-none" />
                                        <button type="button" onclick="addResponsibility()"
                                                class="px-4 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <label for="company-description" class="block text-sm font-medium text-gray-700 mb-1"><?= t('vacancies_about_company') ?></label>
                                    <textarea id="company-description" rows="3" class="w-full input-field"></textarea>
                                </div>
                                <div class="mt-6 flex justify-end space-x-3">
                                    <button type="button" onclick="hideCreateVacancyForm()"
                                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        <?= t('vacancies_cancel') ?>
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                                        <?= t('vacancies_create') ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Vacancy Grid -->
                <?php if (count($vacancies) > 0): ?>
                    <div class="grid grid-cols-1 gap-6">
                    <?php foreach ($vacancies as $vacancy): ?>
                        <div class="vacancy-card card">
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">
                                            <?= htmlspecialchars($vacancy['title']) ?>
                                        </h3>
                                        <p class="text-indigo-600 font-medium mt-1 flex items-center">
                                            <i class="fas fa-building mr-1"></i>
                                            <span><?= htmlspecialchars($vacancy['company']) ?></span>
                                        </p>
                                        <div class="flex items-center mt-2 text-gray-500 flex-wrap gap-2">
                                            <div class="flex items-center">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                <span><?= htmlspecialchars($vacancy['location']) ?></span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-briefcase mr-1"></i>
                                                <span><?= getVacancyTypeText($vacancy['type']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> <?= t('vacancies_verified') ?>
                                        </span>
                                        <?php
                                        $salaryMinText = $formatSalary($vacancy['salary_min'] ?? 0);
                                        $salaryMaxText = $formatSalary($vacancy['salary_max'] ?? 0);
                                        $currencyCode = strtoupper((string) ($vacancy['salary_currency'] ?? 'TJS'));
                                        $currencyLabel = t('currency_' . strtolower($currencyCode), $currencyCode);
                                        ?>
                                        <?php if ($salaryMinText !== null): ?>
                                            <p class="mt-2 text-lg font-bold text-gray-900">
                                                <?= t('vacancies_salary_from') ?> <?= $salaryMinText ?> <?= $currencyLabel ?>
                                            </p>
                                            <?php if ($salaryMaxText !== null): ?>
                                                <p class="text-sm text-gray-500">
                                                    <?= t('vacancies_salary_to') ?> <?= $salaryMaxText ?> <?= $currencyLabel ?>
                                                </p>
                                            <?php endif; ?>
                                        <?php elseif ($salaryMaxText !== null): ?>
                                            <p class="mt-2 text-lg font-bold text-gray-900">
                                                <?= t('vacancies_salary_to') ?> <?= $salaryMaxText ?> <?= $currencyLabel ?>
                                            </p>
                                        <?php else: ?>
                                            <p class="mt-2 text-lg font-bold text-gray-900">
                                                <?= t('vacancies_salary') ?>: <?= t('common_none') ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($vacancy['skills'] as $skill): ?>
                                            <span class="skills-tag"><?= htmlspecialchars($skill['skill_name'] ?? $skill['skills_name'] ?? '') ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <p class="text-gray-600 mt-4 line-clamp-2">
                                    <?= htmlspecialchars($vacancyExcerpt($vacancy['description'] ?? '', 150)) ?>
                                </p>
                                <div class="mt-6 flex justify-between items-center">
                                    <button type="button" onclick="openVacancyDetails(<?= $vacancy['id'] ?>)" 
                                            class="text-indigo-600 hover:text-indigo-900 font-medium flex items-center">
                                        <i class="fas fa-info-circle mr-1"></i> <?= t('vacancies_details') ?>
                                    </button>
                                    <?php
                                        $vacancyOwnerId = (int) ($vacancy['owner_id'] ?? 0);
                                        $canApplyVacancy = $isSeeker && $currentUserId > 0 && $vacancyOwnerId !== $currentUserId;
                                    ?>
                                    <?php if (!empty($appliedMap[$vacancy['id']])): ?>
                                        <a href="?action=vacancy-chat&app_id=<?= (int)$appliedMap[$vacancy['id']] ?>"
                                           class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700">
                                            <?= t('vacancies_chat') ?>
                                        </a>
                                    <?php elseif (!$canApplyVacancy): ?>
                                        <span class="px-4 py-2 bg-gray-200 text-gray-600 rounded-lg text-sm font-medium cursor-not-allowed">
                                            <?= t('vacancies_apply') ?>
                                        </span>
                                    <?php else: ?>
                                        <button onclick="applyToVacancy(<?= $vacancy['id'] ?>)" 
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                                            <?= t('vacancies_apply') ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 card">
                        <i class="fas fa-briefcase text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900">
                            <?= t('vacancies_not_found') ?>
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            <?= t('vacancies_not_found_hint') ?>
                        </p>
                        <?php if ($isEmployer): ?>
                        <div class="mt-6">
                            <button onclick="showCreateVacancyForm()" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                <i class="fas fa-plus mr-2"></i> <?= t('vacancies_post') ?>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($totalPages > 1): ?>
                    <div class="mt-8 flex items-center justify-center gap-2">
                        <a href="<?= $buildUrl(['page' => max(1, $page - 1)]) ?>"
                           class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">
                            &#8592;
                        </a>
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <a href="<?= $buildUrl(['page' => $p]) ?>"
                               class="px-3 py-2 rounded-lg text-sm <?= $p === (int)$page ? 'bg-indigo-600 text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50' ?>">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>
                        <a href="<?= $buildUrl(['page' => min($totalPages, $page + 1)]) ?>"
                           class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">
                            &#8594;
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Vacancy Details Modal -->
    <div id="vacancyDetailsModal" class="fixed inset-0 z-50 overflow-y-auto hidden" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-white">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900" id="vacancy-title-display"></h2>
                                <p class="text-indigo-600 font-medium mt-1 flex items-center">
                                    <i class="fas fa-building mr-1"></i>
                                    <span id="vacancy-company-display"></span>
                                </p>
                                <div class="flex items-center mt-2 text-gray-500 flex-wrap gap-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <span id="vacancy-location-display"></span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-briefcase mr-1"></i>
                                        <span id="vacancy-type-display"></span>
                                    </div>
                                </div>
                            </div>
                            <button onclick="closeVacancyDetails()" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times text-xl"></i>
                                <span class="sr-only"><?= t('vacancies_close') ?></span>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i> <?= t('vacancies_description_title') ?>
                                    </h3>
                                    <p class="text-gray-700" id="vacancy-description-display"></p>
                                </div>
                                <div class="mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-clipboard-list mr-2"></i> <?= t('vacancies_requirements') ?>
                                    </h3>
                                    <ul class="list-disc pl-5 space-y-1" id="vacancy-requirements-display"></ul>
                                </div>
                                <div class="mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-tasks mr-2"></i> <?= t('vacancies_responsibilities') ?>
                                    </h3>
                                    <ul class="list-disc pl-5 space-y-1" id="vacancy-responsibilities-display"></ul>
                                </div>
                                <div class="mt-6" id="vacancy-pluses-container">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-plus-circle mr-2"></i> <?= t('vacancies_pluses_title') ?>
                                    </h3>
                                    <ul class="list-disc pl-5 space-y-1" id="vacancy-pluses-display"></ul>
                                </div>
                            </div>
                            <div>
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-600 flex items-center">
                                            <i class="fas fa-money-bill-wave mr-1"></i> <?= t('vacancies_salary') ?>
                                        </span>
                                        <span class="font-medium" id="vacancy-salary-min-display"></span>
                                    </div>
                                    <div class="mt-1 flex justify-between items-center" id="vacancy-salary-max-container">
                                        <span class="text-gray-600"><?= t('vacancies_salary_to') ?></span>
                                        <span class="font-medium" id="vacancy-salary-max-display"></span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-star mr-1"></i> <?= t('vacancies_skills') ?>
                                    </h4>
                                    <div class="flex flex-wrap" id="vacancy-skills-display"></div>
                                </div>
                                <div class="mt-6" id="company-description-container">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-building mr-1"></i> <?= t('vacancies_about_company') ?>
                                    </h4>
                                    <p class="text-gray-700 text-sm" id="vacancy-company-description-display"></p>
                                </div>
                                <div class="mt-6 border-t pt-4">
                                    <button id="apply-vacancy-btn" onclick="applyToVacancyFromModal()" 
                                            class="w-full py-3 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                        <?= t('vacancies_apply_full') ?>
                                    </button>
                                    <a id="chat-vacancy-btn" href="#" class="hidden w-full text-center py-3 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">
                                        <?= t('vacancies_go_chat') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php
    $footerContext = 'vacancies';
    include 'includes/footer.php';
    ?>

    <script>
        const appliedVacancies = <?= tfSafeJson($appliedMap, JSON_UNESCAPED_UNICODE) ?>;
        const canApplyByRole = <?= $isSeeker ? 'true' : 'false' ?>;
        const currentUserId = <?= $currentUserId ?>;
        let selectedVacancySkills = [];
        let vacancyRequirements = [];
        let vacancyPluses = [];
        let vacancyResponsibilities = [];
        let isSubmittingVacancy = false;
        let currentVacancyId = null;
        const vacancyDescriptionEl = document.getElementById('vacancy-description');
        const vacancyDescriptionWordCountEl = document.getElementById('vacancy-description-word-count');
        const currencyLabels = <?= tfSafeJson([
            'TJS' => t('currency_tjs'),
            'RUB' => t('currency_rub'),
            'USD' => t('currency_usd')
        ], JSON_UNESCAPED_UNICODE) ?>;
        const getCurrencyLabel = (code) => currencyLabels[code] || code || '';
        const salaryMinText = '<?= t('vacancies_salary_from') ?>';
        const salaryToText = '<?= t('vacancies_salary_to') ?>';
        const salaryNoneText = '<?= t('common_none') ?>';
        const vacancyDescriptionWordsLabel = '<?= t('vacancies_description_words_label', 'Слов') ?>';
        const vacancyDescriptionWordsGood = '<?= t('vacancies_description_words_good', 'Нормально для карточки') ?>';
        const vacancyDescriptionWordsRange = '<?= t('vacancies_description_words_range', 'Рекомендуется 12-24 слова') ?>';

        function countWords(value) {
            return String(value || '').trim().split(/\s+/).filter(Boolean).length;
        }

        function updateVacancyDescriptionWordCount() {
            if (!vacancyDescriptionEl || !vacancyDescriptionWordCountEl) return;
            const words = countWords(vacancyDescriptionEl.value);
            const inRange = words >= 12 && words <= 24;
            vacancyDescriptionWordCountEl.textContent = `${vacancyDescriptionWordsLabel}: ${words}. ${inRange ? vacancyDescriptionWordsGood : vacancyDescriptionWordsRange}`;
            vacancyDescriptionWordCountEl.className = `mt-1 text-xs ${inRange ? 'text-emerald-600' : 'text-amber-600'}`;
        }

        function formatSalaryValue(value, currencyCode) {
            const num = Number.parseInt(value, 10);
            if (!Number.isFinite(num) || num <= 0) return null;
            const label = getCurrencyLabel(currencyCode);
            return label ? `${num.toLocaleString()} ${label}` : `${num.toLocaleString()}`;
        }

        async function parseJsonResponse(response, fallbackMessage) {
            const textRaw = await response.text();
            const text = String(textRaw || '').replace(/^\uFEFF/, '').trim();
            let data = null;
            try {
                data = JSON.parse(text);
            } catch (e) {
                const start = text.indexOf('{');
                const end = text.lastIndexOf('}');
                if (start !== -1 && end !== -1 && end > start) {
                    data = JSON.parse(text.slice(start, end + 1));
                } else {
                    throw new Error(fallbackMessage || 'Invalid JSON response');
                }
            }
            if (!response.ok) {
                throw new Error((data && data.message) ? data.message : (fallbackMessage || 'HTTP error'));
            }
            return data;
        }

        function showCreateVacancyForm() {
            const modal = document.getElementById('createVacancyModal');
            if (!modal) {
                if (window.tfNotify) {
                    tfNotify('<?= t('vacancies_create_unavailable') ?>');
                }
                return;
            }
            modal.classList.remove('hidden');
        }

        function hideCreateVacancyForm() {
            const modal = document.getElementById('createVacancyModal');
            if (modal) modal.classList.add('hidden');
        }

        function addVacancySkill() {
            const input = document.getElementById('vacancy-skill-input');
            const skill = input.value.trim();
            if (skill && !selectedVacancySkills.includes(skill)) {
                selectedVacancySkills.push(skill);
                updateVacancySkillsDisplay();
                input.value = '';
            }
        }

        function updateVacancySkillsDisplay() {
            const container = document.getElementById('vacancy-skills-container');
            container.innerHTML = selectedVacancySkills.map(skill => 
                `<span class="skills-tag remove mb-2" onclick="removeVacancySkill('${skill}')">${skill} ×</span>`
            ).join('');
        }

        function removeVacancySkill(skill) {
            selectedVacancySkills = selectedVacancySkills.filter(s => s !== skill);
            updateVacancySkillsDisplay();
        }

        function addRequirement() {
            const input = document.getElementById('requirement-input');
            const requirement = input.value.trim();
            if (requirement) {
                vacancyRequirements.push(requirement);
                updateRequirementsList();
                input.value = '';
            }
        }

        function updateRequirementsList() {
            const container = document.getElementById('requirements-list');
            container.innerHTML = vacancyRequirements.map((req, index) => 
                `<div class="flex items-center">
                    <span class="mr-2 text-gray-500">•</span>
                    <span class="flex-1">${req}</span>
                    <button type="button" onclick="removeRequirement(${index})" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`
            ).join('');
        }

        function removeRequirement(index) {
            vacancyRequirements.splice(index, 1);
            updateRequirementsList();
        }

        function addPlus() {
            const input = document.getElementById('plus-input');
            const plus = input.value.trim();
            if (plus) {
                vacancyPluses.push(plus);
                updatePlusesList();
                input.value = '';
            }
        }

        function updatePlusesList() {
            const container = document.getElementById('pluses-list');
            container.innerHTML = vacancyPluses.map((plus, index) => 
                `<div class="flex items-center">
                    <span class="mr-2 text-gray-500">•</span>
                    <span class="flex-1">${plus}</span>
                    <button type="button" onclick="removePlus(${index})" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`
            ).join('');
        }

        function removePlus(index) {
            vacancyPluses.splice(index, 1);
            updatePlusesList();
        }

        function addResponsibility() {
            const input = document.getElementById('responsibility-input');
            const responsibility = input.value.trim();
            if (responsibility) {
                vacancyResponsibilities.push(responsibility);
                updateResponsibilitiesList();
                input.value = '';
            }
        }

        function updateResponsibilitiesList() {
            const container = document.getElementById('responsibilities-list');
            container.innerHTML = vacancyResponsibilities.map((resp, index) => 
                `<div class="flex items-center">
                    <span class="mr-2 text-gray-500">•</span>
                    <span class="flex-1">${resp}</span>
                    <button type="button" onclick="removeResponsibility(${index})" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`
            ).join('');
        }

        function removeResponsibility(index) {
            vacancyResponsibilities.splice(index, 1);
            updateResponsibilitiesList();
        }

        function createVacancy() {
            if (isSubmittingVacancy) return;
            isSubmittingVacancy = true;
            const formData = {
                title: document.getElementById('vacancy-title').value,
                company: document.getElementById('vacancy-company').value,
                location: document.getElementById('vacancy-location').value,
                type: document.getElementById('vacancy-type').value,
                salary: {
                    min: parseInt(document.getElementById('vacancy-salary-min').value) || 0,
                    max: parseInt(document.getElementById('vacancy-salary-max').value) || null,
                    currency: document.getElementById('vacancy-salary-currency').value || 'TJS'
                },
                skills: selectedVacancySkills,
                description: document.getElementById('vacancy-description').value,
                requirements: vacancyRequirements,
                pluses: vacancyPluses,
                responsibilities: vacancyResponsibilities,
                companyDescription: document.getElementById('company-description').value
            };

            const submitBtn = document.querySelector('#vacancyForm button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;
            
            fetch('?action=create-vacancy', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data && data.success) {
                    tfNotify(data.message);
                    resetVacancyForm();
                    hideCreateVacancyForm();
                    setTimeout(() => window.location.reload(), 300);
                } else {
                    tfNotify((data && data.message) ? data.message : '<?= t('vacancies_create_error') ?>');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tfNotify('<?= t('vacancies_create_error') ?>');
            })
            .finally(() => {
                isSubmittingVacancy = false;
                if (submitBtn) submitBtn.disabled = false;
            });
        }

        function resetVacancyForm() {
            const form = document.getElementById('vacancyForm');
            if (form) form.reset();
            selectedVacancySkills = [];
            vacancyRequirements = [];
            vacancyPluses = [];
            vacancyResponsibilities = [];
            updateVacancySkillsDisplay();
            updateRequirementsList();
            updatePlusesList();
            updateResponsibilitiesList();
            updateVacancyDescriptionWordCount();
        }

        function openVacancyDetails(vacancyId) {
            currentVacancyId = vacancyId;
            document.getElementById('vacancyDetailsModal').classList.remove('hidden');
            document.getElementById('vacancy-title-display').innerHTML = '<span class=\"ui-skeleton ui-skeleton-line-title\"></span>';
            document.getElementById('vacancy-company-display').innerHTML = '<span class=\"ui-skeleton ui-skeleton-line-subtitle\"></span>';
            document.getElementById('vacancy-description-display').innerHTML = '<span class=\"ui-skeleton ui-skeleton-block\"></span>';
            
            fetch(`?action=get-vacancy&id=${vacancyId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => parseJsonResponse(response, '<?= t('vacancies_generic_error') ?>'))
            .then(data => {
                if (data.success) {
                    const vacancy = data.vacancy || {};
                    const skills = Array.isArray(vacancy.skills) ? vacancy.skills : [];
                    const requirements = Array.isArray(vacancy.requirements) ? vacancy.requirements : [];
                    const responsibilities = Array.isArray(vacancy.responsibilities) ? vacancy.responsibilities : [];
                    const pluses = Array.isArray(vacancy.pluses) ? vacancy.pluses : [];
                    
                    document.getElementById('vacancy-title-display').textContent = vacancy.title || '';
                    document.getElementById('vacancy-company-display').textContent = vacancy.company || '';
                    document.getElementById('vacancy-location-display').textContent = vacancy.location || '';
                    document.getElementById('vacancy-type-display').textContent = getVacancyTypeText(vacancy.type || '');
                    document.getElementById('vacancy-description-display').textContent = vacancy.description || '';

                    const minSalary = formatSalaryValue(vacancy.salary_min, vacancy.salary_currency);
                    const maxSalary = formatSalaryValue(vacancy.salary_max, vacancy.salary_currency);
                    if (minSalary) {
                        document.getElementById('vacancy-salary-min-display').textContent = `${salaryMinText} ${minSalary}`;
                    } else if (maxSalary) {
                        document.getElementById('vacancy-salary-min-display').textContent = `${salaryToText} ${maxSalary}`;
                    } else {
                        document.getElementById('vacancy-salary-min-display').textContent = salaryNoneText;
                    }

                    if (maxSalary) {
                        document.getElementById('vacancy-salary-max-container').style.display = 'flex';
                        document.getElementById('vacancy-salary-max-display').textContent = maxSalary;
                    } else {
                        document.getElementById('vacancy-salary-max-container').style.display = 'none';
                    }
                    
                    // Render skills
                    const skillsContainer = document.getElementById('vacancy-skills-display');
                    skillsContainer.innerHTML = skills.map(skill => 
                        `<span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full">${skill.skill_name || skill.skills_name || ''}</span>`
                    ).join('');
                    if (!skills.length) {
                        skillsContainer.innerHTML = `<span class="text-sm text-gray-500">${salaryNoneText}</span>`;
                    }
                    
                    // Render requirements
                    const requirementsContainer = document.getElementById('vacancy-requirements-display');
                    requirementsContainer.innerHTML = requirements.map(req => 
                        `<li>${req.requirement_text}</li>`
                    ).join('');
                    
                    // Render responsibilities
                    const responsibilitiesContainer = document.getElementById('vacancy-responsibilities-display');
                    responsibilitiesContainer.innerHTML = responsibilities.map(resp => 
                        `<li>${resp.responsibility_text}</li>`
                    ).join('');
                    
                    // Render pluses
                    if (pluses.length > 0) {
                        document.getElementById('vacancy-pluses-container').style.display = 'block';
                        const plusesContainer = document.getElementById('vacancy-pluses-display');
                        plusesContainer.innerHTML = pluses.map(plus => 
                            `<li>${plus.plus_text}</li>`
                        ).join('');
                    } else {
                        document.getElementById('vacancy-pluses-container').style.display = 'none';
                    }
                    
                    // Render company info
                    if (vacancy.company_description) {
                        document.getElementById('company-description-container').style.display = 'block';
                        document.getElementById('vacancy-company-description-display').textContent = vacancy.company_description;
                    } else {
                        document.getElementById('company-description-container').style.display = 'none';
                    }

                    const applyBtn = document.getElementById('apply-vacancy-btn');
                    const chatBtn = document.getElementById('chat-vacancy-btn');
                    const hasApplied = !!appliedVacancies[vacancyId];
                    const ownerId = parseInt(vacancy.owner_id || 0, 10) || 0;
                    const canApplyThisVacancy = canApplyByRole && currentUserId > 0 && ownerId !== currentUserId;
                    if (hasApplied) {
                        if (applyBtn) applyBtn.classList.add('hidden');
                        if (chatBtn) {
                            chatBtn.href = `?action=vacancy-chat&app_id=${appliedVacancies[vacancyId]}`;
                            chatBtn.classList.remove('hidden');
                        }
                    } else if (!canApplyThisVacancy) {
                        if (chatBtn) chatBtn.classList.add('hidden');
                        if (applyBtn) {
                            applyBtn.classList.remove('hidden');
                            applyBtn.disabled = true;
                            applyBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                            applyBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
                        }
                    } else {
                        if (chatBtn) chatBtn.classList.add('hidden');
                        if (applyBtn) {
                            applyBtn.disabled = false;
                            applyBtn.classList.remove('hidden', 'bg-gray-300', 'cursor-not-allowed');
                            applyBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                        }
                    }
                    
                } else {
                    tfNotify(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (window.tfNotify) tfNotify((error && error.message) ? error.message : '<?= t('vacancies_generic_error') ?>', 'error');
            });
        }

        function closeVacancyDetails() {
            document.getElementById('vacancyDetailsModal').classList.add('hidden');
        }

        async function applyToVacancy(vacancyId) {
            const ok = await tfConfirm('<?= t('vacancies_apply_confirm') ?>');
            if (!ok) return;
            fetch('?action=apply-vacancy', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ vacancyId: vacancyId })
            })
            .then(response => parseJsonResponse(response, '<?= t('vacancies_generic_error') ?>'))
            .then(data => {
                if (data.success) {
                    tfNotify(data.message, 'success');
                    window.location.reload();
                } else {
                    tfNotify(data.message, 'error');
                }
            })
            .catch(() => tfNotify('<?= t('vacancies_generic_error') ?>', 'error'));
        }

        function applyToVacancyFromModal() {
            applyToVacancy(currentVacancyId);
            closeVacancyDetails();
        }

        function getVacancyTypeText(type) {
            const types = {
                'remote': '<?= t('vacancies_remote') ?>',
                'office': '<?= t('vacancies_office') ?>',
                'hybrid': '<?= t('vacancies_hybrid') ?>'
            };
            return types[type] || type;
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeVacancyDetails();
            }
        });

        if (vacancyDescriptionEl) {
            vacancyDescriptionEl.addEventListener('input', updateVacancyDescriptionWordCount);
            updateVacancyDescriptionWordCount();
        }
    </script>
</body>
</html>
