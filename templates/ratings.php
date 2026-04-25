<?php if (!defined('APP_INIT')) die('Direct access not permitted'); ?>
<?php
$allUsers = $allUsers ?? [];
$filteredUsers = $filteredUsers ?? [];
$contestLeaderboardPage = $contestLeaderboardPage ?? [];
$search = $search ?? '';
$skillsFilter = $skillsFilter ?? [];
$experienceFilter = $experienceFilter ?? [];
$educationFilter = $educationFilter ?? [];
$page = $page ?? 1;
$perPage = $perPage ?? 10;
$totalUsers = $totalUsers ?? count($allUsers);
$totalFiltered = $totalFiltered ?? count($filteredUsers);
$totalPages = max(1, (int) ($totalPages ?? 1));
$activeTab = $activeTab ?? 'general';
$contestSearch = $contestSearch ?? '';
$contestPointsMin = $contestPointsMin ?? 0;
$contestPointsMax = $contestPointsMax ?? null;
$contestSolvedMin = $contestSolvedMin ?? 0;
$contestAttemptsMax = $contestAttemptsMax ?? null;
$contestPage = $contestPage ?? 1;
$contestPerPage = $contestPerPage ?? 10;
$contestTotalFiltered = $contestTotalFiltered ?? count($contestLeaderboardPage);
$contestTotalPages = max(1, (int) ($contestTotalPages ?? 1));

$ratingLevels = [
    ['min' => 0, 'max' => 199, 'key' => 'rating_title_novice', 'icon' => 'fa-seedling', 'class' => 'text-emerald-700 border-emerald-200 bg-emerald-50'],
    ['min' => 200, 'max' => 399, 'key' => 'rating_title_beginner', 'icon' => 'fa-person-walking', 'class' => 'text-amber-700 border-amber-200 bg-amber-50'],
    ['min' => 400, 'max' => 699, 'key' => 'rating_title_outsider', 'icon' => 'fa-user-clock', 'class' => 'text-rose-700 border-rose-200 bg-rose-50'],
    ['min' => 700, 'max' => 999, 'key' => 'rating_title_average', 'icon' => 'fa-user', 'class' => 'text-indigo-700 border-indigo-200 bg-indigo-50'],
    ['min' => 1000, 'max' => 1499, 'key' => 'rating_title_involved', 'icon' => 'fa-brain', 'class' => 'text-sky-700 border-sky-200 bg-sky-50'],
    ['min' => 1500, 'max' => 2199, 'key' => 'rating_title_master', 'icon' => 'fa-screwdriver-wrench', 'class' => 'text-green-700 border-green-200 bg-green-50'],
    ['min' => 2200, 'max' => 2999, 'key' => 'rating_title_maestro', 'icon' => 'fa-music', 'class' => 'text-pink-700 border-pink-200 bg-pink-50'],
    ['min' => 3000, 'max' => 3999, 'key' => 'rating_title_grandmaster', 'icon' => 'fa-chess-king', 'class' => 'text-violet-700 border-violet-200 bg-violet-50'],
    ['min' => 4000, 'max' => PHP_INT_MAX, 'key' => 'rating_title_gigachat', 'icon' => 'fa-bolt', 'class' => 'text-orange-700 border-orange-200 bg-orange-50'],
];

$ratingBadge = static function (int $points) use ($ratingLevels): array {
    foreach ($ratingLevels as $level) {
        if ($points >= $level['min'] && $points <= $level['max']) {
            return [
                'title' => t($level['key'], $level['key']),
                'icon' => $level['icon'],
                'class' => $level['class'],
            ];
        }
    }
    $last = $ratingLevels[count($ratingLevels) - 1];
    return [
        'title' => t($last['key'], $last['key']),
        'icon' => $last['icon'],
        'class' => $last['class'],
    ];
};

$buildGeneralUrl = function ($overrides = []) use ($search, $skillsFilter, $experienceFilter, $educationFilter, $contestSearch, $contestPointsMin, $contestPointsMax, $contestSolvedMin, $contestAttemptsMax, $contestPage) {
    $params = array_merge([
        'action' => 'ratings',
        'tab' => 'general',
        'search' => $search,
        'skills' => $skillsFilter,
        'experience' => $experienceFilter,
        'education' => $educationFilter,
        'contest_search' => $contestSearch,
        'contest_points_min' => $contestPointsMin,
        'contest_points_max' => $contestPointsMax,
        'contest_solved_min' => $contestSolvedMin,
        'contest_attempts_max' => $contestAttemptsMax,
        'contest_page' => $contestPage,
    ], $overrides);
    $params = array_filter($params, static function ($v) {
        if (is_array($v)) {
            return !empty($v);
        }
        return $v !== '' && $v !== null;
    });
    return '?' . http_build_query($params);
};

$buildContestUrl = function ($overrides = []) use ($search, $skillsFilter, $experienceFilter, $educationFilter, $contestSearch, $contestPointsMin, $contestPointsMax, $contestSolvedMin, $contestAttemptsMax, $page) {
    $params = array_merge([
        'action' => 'ratings',
        'tab' => 'contests',
        'search' => $search,
        'skills' => $skillsFilter,
        'experience' => $experienceFilter,
        'education' => $educationFilter,
        'page' => $page,
        'contest_search' => $contestSearch,
        'contest_points_min' => $contestPointsMin,
        'contest_points_max' => $contestPointsMax,
        'contest_solved_min' => $contestSolvedMin,
        'contest_attempts_max' => $contestAttemptsMax,
    ], $overrides);
    $params = array_filter($params, static function ($v) {
        if (is_array($v)) {
            return !empty($v);
        }
        return $v !== '' && $v !== null;
    });
    return '?' . http_build_query($params);
};
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">
<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('ratings_page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background:
                radial-gradient(circle at top right, rgba(99, 102, 241, 0.10), transparent 26%),
                linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }
        .ratings-shell {
            max-width: 1320px;
        }
        .panel-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.06);
        }
        .tab-link {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.85rem 1.15rem;
            border-radius: 999px;
            font-weight: 700;
            border: 1px solid #dbe3f0;
            color: #475569;
            background: #fff;
        }
        .tab-link.active {
            border-color: #4f46e5;
            background: linear-gradient(135deg, #eef2ff, #ffffff);
            color: #312e81;
        }
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border-width: 1px;
            border-style: solid;
            border-radius: 999px;
            padding: 0.35rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .country-flag-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.75rem;
            height: 1.75rem;
            padding: 0 0.35rem;
            border-radius: 999px;
            background: #eef2ff;
            line-height: 1;
            flex: 0 0 auto;
            overflow: hidden;
        }
        .country-flag-pill img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .skill-tag {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.7rem;
            border-radius: 999px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .input-field {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            padding: 0.8rem 0.95rem;
            font-size: 0.95rem;
            background: #fff;
        }
        .input-field:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }
        .btn-primary {
            width: 100%;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            font-weight: 700;
            padding: 0.85rem 1rem;
            cursor: pointer;
        }
        .ratings-table-wrap {
            overflow-x: auto;
        }
        .ratings-table {
            width: 100%;
            min-width: 920px;
            border-collapse: collapse;
        }
        .ratings-table thead th {
            padding: 1rem;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            text-align: left;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #475569;
        }
        .ratings-table tbody td {
            padding: 1rem;
            border-top: 1px solid #eef2f7;
            vertical-align: top;
        }
        .ratings-table tbody tr:hover {
            background: #f8fbff;
        }
        .line-clamp-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }
        .is-hidden {
            display: none;
        }
    </style>
</head>
<body class="text-slate-900">
    <?php include 'includes/header.php'; ?>

    <main class="ratings-shell mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <section class="panel-card p-5 sm:p-6 mb-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900"><?= t('ratings_heading') ?></h1>
                    <p class="text-sm text-slate-500 mt-2 <?= $activeTab === 'general' ? '' : 'is-hidden' ?>" data-summary="general">
                        <?= (int) $totalFiltered ?> <?= t('ratings_of') ?> <?= (int) $totalUsers ?> <?= t('ratings_users') ?>
                    </p>
                    <p class="text-sm text-slate-500 mt-2 <?= $activeTab === 'contests' ? '' : 'is-hidden' ?>" data-summary="contests">
                        <?= (int) $contestTotalFiltered ?> <?= t('ratings_users') ?> • <?= t('nav_contests', 'Contests') ?>
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="<?= htmlspecialchars($buildGeneralUrl(['tab' => 'general', 'page' => 1])) ?>" class="tab-link <?= $activeTab === 'general' ? 'active' : '' ?>" data-tab-trigger="general">
                        <i class="fas fa-users"></i><?= t('ratings_overall_tab', 'Общая') ?>
                    </a>
                    <a href="<?= htmlspecialchars($buildContestUrl(['contest_page' => 1])) ?>" class="tab-link <?= $activeTab === 'contests' ? 'active' : '' ?>" data-tab-trigger="contests">
                        <i class="fas fa-trophy"></i><?= t('ratings_contests_tab', 'Контесты') ?>
                    </a>
                </div>
            </div>
        </section>

        <div class="flex flex-col xl:flex-row gap-6">
            <aside class="xl:w-80 flex-shrink-0">
                <div class="panel-card p-5 xl:sticky xl:top-8">
                    <form method="GET" action="" data-filter-panel="general" class="<?= $activeTab === 'general' ? '' : 'is-hidden' ?>">
                        <input type="hidden" name="action" value="ratings">
                        <input type="hidden" name="tab" value="general">
                        <input type="hidden" name="page" value="1">
                        <h2 class="text-lg font-bold text-slate-900 flex items-center mb-4">
                            <i class="fas fa-filter mr-2 text-indigo-600"></i><?= t('ratings_filters') ?>
                        </h2>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2"><?= t('ratings_search_label') ?></label>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="<?= t('ratings_search_placeholder') ?>" class="input-field">
                        </div>

                        <div class="mb-5">
                            <h3 class="text-sm font-semibold text-slate-900 mb-2"><?= t('ratings_skills') ?></h3>
                            <div class="space-y-2 max-h-36 overflow-y-auto pr-1 text-sm">
                                <?php foreach (['JavaScript', 'React', 'Node.js', 'Python', 'HTML/CSS', 'TypeScript', 'SQL', 'AWS', 'Judge0', 'Git', 'Java', 'C#', 'PHP'] as $skill): ?>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="skills[]" value="<?= htmlspecialchars($skill) ?>" <?= in_array($skill, $skillsFilter, true) ? 'checked' : '' ?>>
                                        <span><?= htmlspecialchars($skill) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h3 class="text-sm font-semibold text-slate-900 mb-2"><?= t('ratings_experience') ?></h3>
                            <div class="space-y-2 text-sm">
                                <?php foreach ([['val' => '1-3', 'lbl' => t('ratings_exp_1_3')], ['val' => '3-5', 'lbl' => t('ratings_exp_3_5')], ['val' => '5+', 'lbl' => t('ratings_exp_5_plus')]] as $exp): ?>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="experience[]" value="<?= $exp['val'] ?>" <?= in_array($exp['val'], $experienceFilter, true) ? 'checked' : '' ?>>
                                        <span><?= $exp['lbl'] ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h3 class="text-sm font-semibold text-slate-900 mb-2"><?= t('ratings_education') ?></h3>
                            <div class="space-y-2 text-sm">
                                <?php foreach ([['val' => 'bachelor', 'lbl' => t('ratings_edu_bachelor')], ['val' => 'master', 'lbl' => t('ratings_edu_master')], ['val' => 'phd', 'lbl' => t('ratings_edu_phd')]] as $edu): ?>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="education[]" value="<?= $edu['val'] ?>" <?= in_array($edu['val'], $educationFilter, true) ? 'checked' : '' ?>>
                                        <span><?= $edu['lbl'] ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button type="submit" class="btn-primary"><?= t('ratings_apply_filters') ?></button>
                    </form>

                    <form method="GET" action="" data-filter-panel="contests" class="<?= $activeTab === 'contests' ? '' : 'is-hidden' ?>">
                        <input type="hidden" name="action" value="ratings">
                        <input type="hidden" name="tab" value="contests">
                        <input type="hidden" name="contest_page" value="1">
                        <h2 class="text-lg font-bold text-slate-900 flex items-center mb-4">
                            <i class="fas fa-filter mr-2 text-indigo-600"></i><?= t('ratings_filters') ?>
                        </h2>

                        <div class="mb-5">
                            <label class="block text-sm font-medium text-slate-700 mb-2"><?= t('ratings_search_label') ?></label>
                            <input type="text" name="contest_search" value="<?= htmlspecialchars($contestSearch) ?>" placeholder="<?= t('ratings_search_placeholder') ?>" class="input-field">
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-medium text-slate-700 mb-2"><?= t('contest_points', 'Points') ?> min</label>
                            <input type="number" min="0" name="contest_points_min" value="<?= (int) $contestPointsMin ?>" class="input-field">
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-medium text-slate-700 mb-2"><?= t('contest_points', 'Points') ?> max</label>
                            <input type="number" min="0" name="contest_points_max" value="<?= $contestPointsMax !== null ? (int) $contestPointsMax : '' ?>" class="input-field">
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-medium text-slate-700 mb-2"><?= t('contest_solved', 'Solved') ?> min</label>
                            <input type="number" min="0" name="contest_solved_min" value="<?= (int) $contestSolvedMin ?>" class="input-field">
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-medium text-slate-700 mb-2"><?= t('contest_attempts_label', 'Attempts') ?> max</label>
                            <input type="number" min="0" name="contest_attempts_max" value="<?= $contestAttemptsMax !== null ? (int) $contestAttemptsMax : '' ?>" class="input-field">
                        </div>

                        <button type="submit" class="btn-primary"><?= t('ratings_apply_filters') ?></button>
                    </form>
                </div>
            </aside>

            <section class="flex-1">
                <div class="panel-card overflow-hidden <?= $activeTab === 'general' ? '' : 'is-hidden' ?>" data-tab-panel="general">
                    <div class="ratings-table-wrap">
                        <table class="ratings-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?= t('ratings_dev') ?></th>
                                    <th><?= t('ratings_skills') ?></th>
                                    <th><?= t('ratings_experience') ?></th>
                                    <th><?= t('ratings_certs') ?></th>
                                    <th><?= t('ratings_points_col') ?></th>
                                    <th><?= t('ratings_details') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $startIndex = ($page - 1) * $perPage; ?>
                                <?php foreach ($filteredUsers as $index => $user): ?>
                                    <?php
                                    $isMe = !empty($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $user['id'];
                                    $badge = $ratingBadge((int) ($user['points'] ?? 0));
                                    $position = $startIndex + $index + 1;
                                    ?>
                                    <tr class="<?= $isMe ? 'bg-indigo-50' : '' ?>">
                                        <td class="font-semibold text-slate-500 whitespace-nowrap">#<?= $position ?></td>
                                        <td>
                                            <div class="flex items-center gap-3 min-w-0">
                                                <img class="h-11 w-11 rounded-2xl object-cover" src="<?= htmlspecialchars((string) ($user['avatar'] ?? '')) ?>" alt="<?= htmlspecialchars((string) ($user['name'] ?? '')) ?>">
                                                <div class="min-w-0">
                                                    <div class="font-semibold text-slate-900 line-clamp-1 flex items-center gap-2">
                                                        <span class="min-w-0 truncate"><?= htmlspecialchars((string) ($user['name'] ?? '')) ?></span>
                                                        <?php if (!empty($user['country_flag'])): ?>
                                                            <span class="country-flag-pill" title="<?= htmlspecialchars(t('label_country', 'Страна проживания')) ?>">
                                                                <img src="<?= htmlspecialchars((string) ($user['country_flag_url'] ?? tfCountryFlagUrl((string) ($user['country_code'] ?? '')))) ?>" alt="" loading="lazy">
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-sm text-slate-500 line-clamp-1"><?= htmlspecialchars((string) ($user['title'] ?? '')) ?></div>
                                                    <div class="mt-1">
                                                        <span class="badge-pill <?= $badge['class'] ?>">
                                                            <i class="fas <?= $badge['icon'] ?>"></i><?= htmlspecialchars($badge['title']) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex flex-wrap gap-1">
                                                <?php
                                                $skills = is_array($user['skills'] ?? null) ? $user['skills'] : [];
                                                $shownSkills = array_slice($skills, 0, 3);
                                                $extra = count($skills) - 3;
                                                foreach ($shownSkills as $skill) {
                                                    echo '<span class="skill-tag">' . htmlspecialchars((string) ($skill['skill_name'] ?? '')) . '</span>';
                                                }
                                                if ($extra > 0) {
                                                    echo '<span class="skill-tag">+' . (int) $extra . '</span>';
                                                }
                                                if (!$skills) {
                                                    echo '<span class="text-sm text-slate-400">' . t('ratings_none') . '</span>';
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td class="text-sm text-slate-600"><?= htmlspecialchars((string) ($user['total_experience'] ?? t('ratings_none'))) ?></td>
                                        <td class="text-sm text-slate-600"><?= uiValue(is_array($user['certificates'] ?? null) ? count($user['certificates']) : (int) ($user['certificates'] ?? 0)) ?></td>
                                        <td class="font-bold text-indigo-700"><?= (int) ($user['points'] ?? 0) ?></td>
                                        <td>
                                            <a href="?action=profile-view&id=<?= (int) $user['id'] ?>" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                                <i class="fas fa-user mr-2"></i><?= $isMe ? t('ratings_this_is_you') : t('ratings_details') ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($filteredUsers)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-12 text-slate-500"><?= t('ratings_none', 'Nothing found') ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel-card overflow-hidden <?= $activeTab === 'contests' ? '' : 'is-hidden' ?>" data-tab-panel="contests">
                    <div class="ratings-table-wrap">
                        <table class="ratings-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?= t('ratings_dev') ?></th>
                                    <th><?= t('contest_solved', 'Solved') ?></th>
                                    <th><?= t('contest_attempts_label', 'Attempts') ?></th>
                                    <th><?= t('contest_points', 'Points') ?></th>
                                    <th><?= t('ratings_details') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $contestStartIndex = ($contestPage - 1) * $contestPerPage; ?>
                                <?php foreach ($contestLeaderboardPage as $index => $row): ?>
                                    <?php $position = $contestStartIndex + $index + 1; ?>
                                    <tr>
                                        <td class="font-semibold text-slate-500 whitespace-nowrap">#<?= $position ?></td>
                                        <td>
                                            <div class="flex items-center gap-3 min-w-0">
                                                <img class="h-11 w-11 rounded-2xl object-cover" src="<?= htmlspecialchars((string) ($row['avatar'] ?? '')) ?>" alt="<?= htmlspecialchars((string) ($row['name'] ?? '')) ?>">
                                                <div class="min-w-0">
                                                    <div class="font-semibold text-slate-900 line-clamp-1"><?= htmlspecialchars((string) ($row['name'] ?? '')) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="font-semibold text-slate-700"><?= (int) ($row['solved_count'] ?? 0) ?></td>
                                        <td class="text-slate-600"><?= (int) ($row['attempts_count'] ?? 0) ?></td>
                                        <td class="font-bold text-indigo-700"><?= (int) ($row['contest_points'] ?? 0) ?></td>
                                        <td>
                                            <a href="?action=profile-view&id=<?= (int) ($row['id'] ?? 0) ?>" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                                <i class="fas fa-user mr-2"></i><?= t('ratings_details') ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($contestLeaderboardPage)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-12 text-slate-500"><?= t('contest_leaderboard_empty', 'No participants yet.') ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-2 <?= $activeTab === 'general' ? '' : 'is-hidden' ?>" data-pagination="general">
                        <a href="<?= htmlspecialchars($buildGeneralUrl(['page' => max(1, $page - 1)])) ?>" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm text-slate-600 hover:bg-slate-50">&larr;</a>
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <a href="<?= htmlspecialchars($buildGeneralUrl(['page' => $p])) ?>" class="px-4 py-2 rounded-xl text-sm <?= $p === (int) $page ? 'bg-indigo-600 text-white' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' ?>"><?= $p ?></a>
                        <?php endfor; ?>
                        <a href="<?= htmlspecialchars($buildGeneralUrl(['page' => min($totalPages, $page + 1)])) ?>" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm text-slate-600 hover:bg-slate-50">&rarr;</a>
                    </div>
                <?php endif; ?>

                <?php if ($contestTotalPages > 1): ?>
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-2 <?= $activeTab === 'contests' ? '' : 'is-hidden' ?>" data-pagination="contests">
                        <a href="<?= htmlspecialchars($buildContestUrl(['contest_page' => max(1, $contestPage - 1)])) ?>" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm text-slate-600 hover:bg-slate-50">&larr;</a>
                        <?php for ($p = 1; $p <= $contestTotalPages; $p++): ?>
                            <a href="<?= htmlspecialchars($buildContestUrl(['contest_page' => $p])) ?>" class="px-4 py-2 rounded-xl text-sm <?= $p === (int) $contestPage ? 'bg-indigo-600 text-white' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' ?>"><?= $p ?></a>
                        <?php endfor; ?>
                        <a href="<?= htmlspecialchars($buildContestUrl(['contest_page' => min($contestTotalPages, $contestPage + 1)])) ?>" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm text-slate-600 hover:bg-slate-50">&rarr;</a>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <?php
    $footerContext = 'ratings';
    include 'includes/footer.php';
    ?>
    <script>
        (function () {
            const triggers = document.querySelectorAll('[data-tab-trigger]');
            const panels = document.querySelectorAll('[data-tab-panel]');
            const filterPanels = document.querySelectorAll('[data-filter-panel]');
            const summaries = document.querySelectorAll('[data-summary]');
            const paginations = document.querySelectorAll('[data-pagination]');

            function switchTab(tab, pushState) {
                triggers.forEach((item) => {
                    item.classList.toggle('active', item.dataset.tabTrigger === tab);
                });
                panels.forEach((item) => {
                    item.classList.toggle('is-hidden', item.dataset.tabPanel !== tab);
                });
                filterPanels.forEach((item) => {
                    item.classList.toggle('is-hidden', item.dataset.filterPanel !== tab);
                });
                summaries.forEach((item) => {
                    item.classList.toggle('is-hidden', item.dataset.summary !== tab);
                });
                paginations.forEach((item) => {
                    item.classList.toggle('is-hidden', item.dataset.pagination !== tab);
                });

                if (pushState) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('tab', tab);
                    window.history.pushState({ tab }, '', url);
                }
            }

            triggers.forEach((item) => {
                item.addEventListener('click', function (event) {
                    event.preventDefault();
                    switchTab(this.dataset.tabTrigger || 'general', true);
                });
            });

            window.addEventListener('popstate', function () {
                const url = new URL(window.location.href);
                switchTab(url.searchParams.get('tab') === 'contests' ? 'contests' : 'general', false);
            });

            switchTab('<?= $activeTab === 'contests' ? 'contests' : 'general' ?>', false);
        })();
    </script>
</body>
</html>
