<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$profileUser = $profileUser ?? null;
if (!$profileUser) {
    header('Location: ?action=ratings');
    exit;
}
$pointsData = calculateUserPoints($profileUser);
$ratingLevels = [
    ['min' => 0, 'max' => 199, 'key' => 'rating_title_novice', 'icon' => 'fa-seedling', 'class' => 'rating-lv-1'],
    ['min' => 200, 'max' => 399, 'key' => 'rating_title_beginner', 'icon' => 'fa-person-walking', 'class' => 'rating-lv-2'],
    ['min' => 400, 'max' => 699, 'key' => 'rating_title_outsider', 'icon' => 'fa-user-clock', 'class' => 'rating-lv-3'],
    ['min' => 700, 'max' => 999, 'key' => 'rating_title_average', 'icon' => 'fa-user', 'class' => 'rating-lv-4'],
    ['min' => 1000, 'max' => 1499, 'key' => 'rating_title_involved', 'icon' => 'fa-brain', 'class' => 'rating-lv-5'],
    ['min' => 1500, 'max' => 2199, 'key' => 'rating_title_master', 'icon' => 'fa-screwdriver-wrench', 'class' => 'rating-lv-6'],
    ['min' => 2200, 'max' => 2999, 'key' => 'rating_title_maestro', 'icon' => 'fa-music', 'class' => 'rating-lv-7'],
    ['min' => 3000, 'max' => 3999, 'key' => 'rating_title_grandmaster', 'icon' => 'fa-chess-king', 'class' => 'rating-lv-8'],
    ['min' => 4000, 'max' => PHP_INT_MAX, 'key' => 'rating_title_gigachat', 'icon' => 'fa-bolt', 'class' => 'rating-lv-9'],
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
$socialLinks = [
    ['key' => 'social_linkedin', 'icon' => 'fab fa-linkedin', 'label' => 'LinkedIn'],
    ['key' => 'social_github', 'icon' => 'fab fa-github', 'label' => 'GitHub'],
    ['key' => 'social_telegram', 'icon' => 'fab fa-telegram-plane', 'label' => 'Telegram'],
    ['key' => 'social_website', 'icon' => 'fas fa-globe', 'label' => t('profile_website', 'Website')],
];
$viewerUserId = (int) ($_SESSION['user_id'] ?? ($user['id'] ?? 0));
$profileOwnerId = (int) ($profileUser['id'] ?? 0);
$isProfileOwner = $viewerUserId > 0 && $viewerUserId === $profileOwnerId;

$cvCustomization = (array) ($profileUser['cv_customization'] ?? []);
$defaultCvState = [
    'accent' => '#6366f1',
    'asideWidth' => 32,
    'gap' => 16,
    'theme' => 1,
    'layout' => 1,
    'fontScale' => 100,
    'cardRadius' => 14,
    'sectionStyle' => 'soft',
    'order' => [
        'aside' => ['social', 'skills', 'stats'],
        'main' => ['about', 'experience', 'education', 'portfolio', 'certificates'],
    ],
];
$cvState = array_replace_recursive($defaultCvState, $cvCustomization);
$cvVariant = max(1, min(5, (int) ($cvState['theme'] ?? 1)));
$cvLayout = max(1, min(5, (int) ($cvState['layout'] ?? 1)));
$cvSectionStyle = (string) ($cvState['sectionStyle'] ?? 'soft');
if (!in_array($cvSectionStyle, ['soft', 'flat', 'outline'], true)) {
    $cvSectionStyle = 'soft';
}
$renderSocialSection = static function () use ($socialLinks, $profileUser): void {
    ?>
    <section class="cv-section">
        <h2 class="font-bold text-slate-900 mb-3"><?= t('profile_social_links', 'Social Links') ?></h2>
        <div class="space-y-2 text-sm">
            <?php
            $hasSocial = false;
            foreach ($socialLinks as $social):
                $url = trim((string) ($profileUser[$social['key']] ?? ''));
                if ($url === '') {
                    continue;
                }
                $hasSocial = true;
                ?>
                <a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener noreferrer"
                    class="flex items-center gap-2 text-indigo-700 hover:text-indigo-900 break-all">
                    <i class="<?= htmlspecialchars($social['icon']) ?>"></i>
                    <span><?= htmlspecialchars($social['label']) ?></span>
                </a>
            <?php endforeach; ?>
            <?php if (!$hasSocial): ?>
                <div class="text-slate-500"><?= t('profile_no_social_links', 'No social links') ?></div>
            <?php endif; ?>
        </div>
    </section>
    <?php
};
$renderSkillsSection = static function () use ($profileUser): void {
    ?>
    <section class="cv-section">
        <h2 class="font-bold text-slate-900 mb-3"><?= t('profile_skills', 'Skills') ?></h2>
        <?php if (!empty($profileUser['skills'])): ?>
            <div>
                <?php foreach ($profileUser['skills'] as $skill): ?>
                    <span class="cv-chip">
                        <?= htmlspecialchars((string) ($skill['skill_name'] ?? '')) ?>
                        <?php if (isset($skill['skill_level'])): ?>
                            · <?= (int) $skill['skill_level'] ?>%
                        <?php endif; ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-slate-500 text-sm"><?= t('profile_skills_empty', 'No skills yet') ?></p>
        <?php endif; ?>
    </section>
    <?php
};
$renderStatsSection = static function () use ($profileUser): void {
    ?>
    <section class="cv-section">
        <h2 class="font-bold text-slate-900 mb-3"><?= t('profile_stats', 'Stats') ?></h2>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="bg-slate-50 rounded-lg p-3">
                <div class="text-xs text-slate-500"><?= t('profile_exp', 'Experience') ?></div>
                <div class="text-lg font-bold"><?= count($profileUser['experience'] ?? []) ?></div>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
                <div class="text-xs text-slate-500"><?= t('profile_education', 'Education') ?></div>
                <div class="text-lg font-bold"><?= count($profileUser['education'] ?? []) ?></div>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
                <div class="text-xs text-slate-500"><?= t('profile_tab_portfolio', 'Portfolio') ?></div>
                <div class="text-lg font-bold"><?= count($profileUser['portfolio'] ?? []) ?></div>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
                <div class="text-xs text-slate-500"><?= t('profile_certs', 'Certificates') ?></div>
                <div class="text-lg font-bold"><?= count($profileUser['certificates'] ?? []) ?></div>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
                <div class="text-xs text-slate-500"><?= t('profile_solved_tasks', 'Solved tasks') ?></div>
                <div class="text-lg font-bold"><?= (int) ($profileUser['solved_tasks'] ?? 0) ?></div>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
                <div class="text-xs text-slate-500"><?= t('profile_passed_contests', 'Passed contests') ?></div>
                <div class="text-lg font-bold"><?= (int) ($profileUser['passed_contests'] ?? 0) ?></div>
            </div>
        </div>
    </section>
    <?php
};
$renderAboutSection = static function () use ($profileUser): void {
    ?>
    <section class="cv-section">
        <h2 class="font-bold text-slate-900 mb-2"><?= t('profile_about', 'About') ?></h2>
        <p class="text-slate-700 whitespace-pre-line">
            <?= htmlspecialchars((string) ($profileUser['bio'] ?? t('common_none', 'none'))) ?>
        </p>
    </section>
    <?php
};
$renderExperienceSection = static function (string $mode = 'default') use ($profileUser): void {
    ?>
    <section class="cv-section">
        <h2 class="font-bold text-slate-900 mb-3"><?= t('profile_tab_experience', 'Experience') ?></h2>
        <?php if (!empty($profileUser['experience'])): ?>
            <div class="<?= $mode === 'timeline' ? 'cv-timeline space-y-4' : 'space-y-3' ?>">
                <?php foreach ($profileUser['experience'] as $exp): ?>
                    <article class="<?= $mode === 'timeline' ? 'cv-timeline-item' : 'cv-list-item' ?>">
                        <div class="font-semibold text-slate-900">
                            <?= htmlspecialchars((string) ($exp['position'] ?? '')) ?>
                        </div>
                        <div class="text-indigo-700 text-sm">
                            <?= htmlspecialchars((string) ($exp['company'] ?? '')) ?>
                        </div>
                        <div class="text-xs text-slate-500 mt-1">
                            <?= htmlspecialchars((string) ($exp['start_date'] ?? '')) ?> -
                            <?= htmlspecialchars((string) (($exp['end_date'] ?? '') !== '' ? $exp['end_date'] : t('profile_now', 'Now'))) ?>
                        </div>
                        <?php if (!empty($exp['description'])): ?>
                            <p class="text-sm text-slate-700 mt-2 whitespace-pre-line">
                                <?= htmlspecialchars((string) $exp['description']) ?>
                            </p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-slate-500 text-sm"><?= t('profile_experience_empty', 'No experience') ?></p>
        <?php endif; ?>
    </section>
    <?php
};
$renderEducationSection = static function (string $mode = 'default') use ($profileUser): void {
    ?>
    <section class="cv-section">
        <h2 class="font-bold text-slate-900 mb-3"><?= t('profile_tab_education', 'Education') ?></h2>
        <?php if (!empty($profileUser['education'])): ?>
            <div class="<?= $mode === 'timeline' ? 'cv-timeline space-y-4' : 'space-y-3' ?>">
                <?php foreach ($profileUser['education'] as $edu): ?>
                    <article class="<?= $mode === 'timeline' ? 'cv-timeline-item' : 'cv-list-item' ?>">
                        <div class="font-semibold text-slate-900">
                            <?= htmlspecialchars((string) ($edu['degree'] ?? '')) ?>
                        </div>
                        <div class="text-indigo-700 text-sm">
                            <?= htmlspecialchars((string) ($edu['institution'] ?? '')) ?>
                        </div>
                        <div class="text-xs text-slate-500 mt-1">
                            <?= htmlspecialchars((string) ($edu['start_date'] ?? '')) ?> -
                            <?= htmlspecialchars((string) (($edu['end_date'] ?? '') !== '' ? $edu['end_date'] : t('profile_now', 'Now'))) ?>
                        </div>
                        <?php if (!empty($edu['description'])): ?>
                            <p class="text-sm text-slate-700 mt-2 whitespace-pre-line">
                                <?= htmlspecialchars((string) $edu['description']) ?>
                            </p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-slate-500 text-sm"><?= t('profile_education_empty', 'No education') ?></p>
        <?php endif; ?>
    </section>
    <?php
};
$renderPortfolioSection = static function () use ($profileUser): void {
    ?>
    <section class="cv-section">
        <h2 class="font-bold text-slate-900 mb-3"><?= t('profile_tab_portfolio', 'Portfolio') ?></h2>
        <?php if (!empty($profileUser['portfolio'])): ?>
            <div class="space-y-3">
                <?php foreach ($profileUser['portfolio'] as $item): ?>
                    <article class="cv-list-item">
                        <div class="font-semibold text-slate-900">
                            <?= htmlspecialchars((string) ($item['title'] ?? '')) ?>
                        </div>
                        <?php if (!empty($item['category'])): ?>
                            <div class="text-xs text-slate-500 mt-1">
                                <?= htmlspecialchars((string) $item['category']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($item['image_url'])): ?>
                            <a href="<?= htmlspecialchars((string) $item['image_url']) ?>" target="_blank"
                                rel="noopener noreferrer"
                                class="text-indigo-700 hover:text-indigo-900 text-sm break-all"><?= htmlspecialchars((string) $item['image_url']) ?></a>
                            <img src="<?= htmlspecialchars((string) $item['image_url']) ?>"
                                alt="<?= t('profile_portfolio_project', 'Project') ?>"
                                class="mt-2 rounded-lg border border-slate-200 max-h-44 object-cover w-full"
                                loading="lazy">
                        <?php endif; ?>
                        <?php if (!empty($item['github_url'])): ?>
                            <a href="<?= htmlspecialchars((string) $item['github_url']) ?>" target="_blank"
                                rel="noopener noreferrer"
                                class="mt-2 inline-flex items-center gap-2 text-indigo-700 hover:text-indigo-900 text-sm break-all">
                                <i class="fab fa-github"></i>
                                <span><?= htmlspecialchars((string) $item['github_url']) ?></span>
                            </a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-slate-500 text-sm"><?= t('profile_projects_empty', 'No projects') ?></p>
        <?php endif; ?>
    </section>
    <?php
};
$renderCertificatesSection = static function () use ($profileUser): void {
    ?>
    <section class="cv-section">
        <h2 class="font-bold text-slate-900 mb-3"><?= t('profile_tab_certificates', 'Certificates') ?></h2>
        <?php if (!empty($profileUser['certificates'])): ?>
            <div class="space-y-2">
                <?php foreach ($profileUser['certificates'] as $cert): ?>
                    <div class="cv-list-item">
                        <div class="font-semibold text-slate-900">
                            <?= htmlspecialchars((string) ($cert['course_title'] ?? $cert['certificate_name'] ?? 'Certificate')) ?>
                        </div>
                        <div class="text-xs text-slate-500 mt-1">
                            <?= htmlspecialchars((string) ($cert['created_at'] ?? '')) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-slate-500 text-sm"><?= t('profile_certs_empty', 'No certificates') ?></p>
        <?php endif; ?>
    </section>
    <?php
};
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <title><?= t('profile_page_title', 'Profile') ?> - CodeMaster</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --cv-accent: #6366f1;
            --cv-accent-strong: #312e81;
            --cv-accent-soft: #eef2ff;
            --cv-header-from: #1e293b;
            --cv-header-to: #312e81;
            --cv-chip-bg: #eff6ff;
            --cv-chip-text: #1e40af;
            --cv-list-bg: #f1f5f9;
            --cv-body-bg: linear-gradient(145deg, #eef2ff 0%, #f8fafc 55%, #e2e8f0 100%);
            --cv-section-gap: 1rem;
            --cv-column-gap: 1.5rem;
            --cv-aside-width: 32%;
            --cv-font-scale: 100;
            --cv-card-radius: 14px;
        }

        .cv-theme-1 {
            --cv-accent: #6366f1;
            --cv-accent-strong: #312e81;
            --cv-accent-soft: #eef2ff;
            --cv-header-from: #1e293b;
            --cv-header-to: #312e81;
            --cv-chip-bg: #eff6ff;
            --cv-chip-text: #1e40af;
            --cv-list-bg: #f1f5f9;
            --cv-body-bg: linear-gradient(145deg, #eef2ff 0%, #f8fafc 55%, #e2e8f0 100%);
        }

        .cv-theme-2 {
            --cv-accent: #10b981;
            --cv-accent-strong: #064e3b;
            --cv-accent-soft: #ecfdf5;
            --cv-header-from: #064e3b;
            --cv-header-to: #0f766e;
            --cv-chip-bg: #ecfdf3;
            --cv-chip-text: #065f46;
            --cv-list-bg: #f0fdf4;
            --cv-body-bg: linear-gradient(145deg, #ecfdf5 0%, #f8fafc 55%, #e2e8f0 100%);
        }

        .cv-theme-3 {
            --cv-accent: #f59e0b;
            --cv-accent-strong: #92400e;
            --cv-accent-soft: #fffbeb;
            --cv-header-from: #78350f;
            --cv-header-to: #b45309;
            --cv-chip-bg: #fef3c7;
            --cv-chip-text: #92400e;
            --cv-list-bg: #fffbeb;
            --cv-body-bg: linear-gradient(145deg, #fff7ed 0%, #f8fafc 55%, #e2e8f0 100%);
        }

        .cv-theme-4 {
            --cv-accent: #475569;
            --cv-accent-strong: #0f172a;
            --cv-accent-soft: #f1f5f9;
            --cv-header-from: #0f172a;
            --cv-header-to: #1f2937;
            --cv-chip-bg: #e2e8f0;
            --cv-chip-text: #0f172a;
            --cv-list-bg: #f8fafc;
            --cv-body-bg: linear-gradient(145deg, #f1f5f9 0%, #f8fafc 55%, #e2e8f0 100%);
        }

        .cv-theme-5 {
            --cv-accent: #f43f5e;
            --cv-accent-strong: #9f1239;
            --cv-accent-soft: #fff1f2;
            --cv-header-from: #9f1239;
            --cv-header-to: #be123c;
            --cv-chip-bg: #ffe4e6;
            --cv-chip-text: #9f1239;
            --cv-list-bg: #fff1f2;
            --cv-body-bg: linear-gradient(145deg, #fff1f2 0%, #f8fafc 55%, #e2e8f0 100%);
        }

        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
            font-family: "Inter", sans-serif;
            background: var(--cv-body-bg);
            color: #0f172a;
        }

        main[data-profile-id] {
            font-size: calc(16px * (var(--cv-font-scale) / 100));
        }

        .cv-shell {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 24px 50px rgba(15, 23, 42, 0.1);
            overflow: hidden;
            border: 1px solid #dbeafe;
        }

        .cv-header {
            background: linear-gradient(135deg, var(--cv-header-from) 0%, var(--cv-header-to) 100%);
            color: #fff;
        }

        .cv-section {
            border: 1px solid #e5e7eb;
            border-radius: var(--cv-card-radius);
            padding: 1rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .cv-section+.cv-section {
            margin-top: var(--cv-section-gap);
        }

        .cv-chip {
            display: inline-flex;
            align-items: center;
            border: 1px solid #dbeafe;
            background: var(--cv-chip-bg);
            color: var(--cv-chip-text);
            border-radius: 999px;
            padding: 0.25rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin: 0.2rem;
        }

        .cv-list-item {
            border-left: 3px solid var(--cv-accent);
            background: var(--cv-list-bg);
            padding: 0.75rem 0.9rem;
            border-radius: calc(var(--cv-card-radius) - 4px);
        }

        .print-hide {
            display: block;
        }

        .cv-print-btn {
            position: static;
            z-index: auto;
        }

        .cv-theme-switch {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .cv-theme-link {
            border: 1px solid rgba(79, 70, 229, 0.25);
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #3730a3;
            background: #eef2ff;
            transition: all 0.2s ease;
        }

        .cv-theme-link:hover {
            background: #e0e7ff;
        }

        .cv-theme-link.active {
            color: #fff;
            background: var(--cv-accent);
            border-color: var(--cv-accent);
        }

        .cv-timeline {
            position: relative;
            padding-left: 1.5rem;
        }

        .cv-timeline::before {
            content: "";
            position: absolute;
            left: 6px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--cv-accent-soft);
        }

        .cv-timeline-item {
            position: relative;
            padding: 0.6rem 0.9rem 0.6rem 0.6rem;
            border-radius: calc(var(--cv-card-radius) - 2px);
            background: var(--cv-list-bg);
            border-left: 3px solid var(--cv-accent);
        }

        .cv-style-flat .cv-section {
            background: #fff;
            border-style: solid;
            box-shadow: none;
        }

        .cv-style-outline .cv-section {
            background: transparent;
            border-width: 2px;
            border-color: #cbd5e1;
        }

        .cv-timeline-item::before {
            content: "";
            position: absolute;
            left: -18px;
            top: 1rem;
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--cv-accent);
        }

        .cv-card-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .cv-edit-grid {
            display: grid;
            grid-template-columns: minmax(220px, var(--cv-aside-width)) minmax(0, 1fr);
            gap: var(--cv-column-gap);
        }

        .cv-edit-column {
            display: flex;
            flex-direction: column;
            gap: var(--cv-section-gap);
        }

        .cv-edit-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .cv-edit-controls {
            display: none;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
        }

        .cv-edit-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-left: auto;
        }

        .cv-editing .cv-edit-controls {
            display: flex;
        }

        .cv-editing .cv-edit-block {
            position: relative;
            border: 1px dashed rgba(99, 102, 241, 0.6);
            background: rgba(99, 102, 241, 0.04);
            border-radius: 16px;
            padding: 0.5rem;
        }

        .cv-editing .cv-edit-block .cv-section {
            border-color: transparent;
        }

        .cv-edit-handle {
            position: absolute;
            top: 8px;
            right: 10px;
            font-size: 0.7rem;
            font-weight: 700;
            color: #4f46e5;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 999px;
            padding: 0.1rem 0.5rem;
            cursor: grab;
            user-select: none;
            display: none;
        }

        .cv-editing .cv-edit-handle {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .cv-edit-block.dragging {
            opacity: 0.6;
        }

        .cv-edit-placeholder {
            border: 2px dashed #c7d2fe;
            border-radius: 14px;
            min-height: 60px;
            background: rgba(99, 102, 241, 0.06);
        }

        @media (max-width: 768px) {
            .cv-card-grid {
                grid-template-columns: 1fr;
            }
            .tf-header-chat-fab {
                display: none !important;
            }
        }

        @media (max-width: 640px) {
            .cv-edit-toolbar {
                padding: 0.75rem;
            }
            .cv-edit-actions {
                width: 100%;
                flex-wrap: wrap;
                gap: 0.5rem;
                margin-left: 0;
            }
            .cv-edit-actions button {
                width: 100%;
                justify-content: center;
            }
            .cv-edit-actions span,
            .cv-edit-actions i {
                display: inline-flex;
                align-items: center;
            }
        }

        @media (max-width: 1024px) {
            .cv-edit-grid {
                grid-template-columns: 1fr;
            }
        }

        .cv-layout-5 .cv-section {
            border: none;
            background: transparent;
            padding: 0;
        }

        .cv-layout-5 .cv-section + .cv-section {
            margin-top: 1.5rem;
        }

        .cv-layout-5 .cv-list-item,
        .cv-layout-5 .cv-timeline-item {
            background: #fff;
            border: 1px solid #e5e7eb;
        }

        .cv-layout-5 h2 {
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.75rem;
            color: #64748b;
        }

        /* Единый и правильный @media print */
        @media print {
            body {
                background: #fff !important;
            }

            /* Скрываем header, footer и элементы управления */
            header,
            footer,
            .print-hide,
            .tf-header-chat-fab,
            .tf-toast-root,
            .tf-page-transition-layer,
            .cv-edit-toolbar {
                display: none !important;
            }

            /* Скрываем AI чат и все его элементы */
            #ai-tutor,
            .chat-messages,
            .msg-row,
            .msg-icon,
            .msg-bubble,
            .msg-time,
            .chip,
            #chatInput,
            #chatSendBtn {
                display: none !important;
            }

            main {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .cv-shell {
                border-radius: 0;
                border: none;
                box-shadow: none;
            }

            .cv-section {
                break-inside: avoid;
            }

            .cv-print-btn {
                display: none !important;
            }

            .card {
                break-inside: avoid;
                box-shadow: none !important;
                border: 1px solid #e5e7eb !important;
            }

            a {
                color: #0f172a !important;
                text-decoration: none !important;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 cv-theme-<?= $cvVariant ?> cv-layout-<?= $cvLayout ?> cv-style-<?= htmlspecialchars($cvSectionStyle) ?>"
        data-profile-id="<?= (int) ($profileUser['id'] ?? 0) ?>" data-is-owner="<?= $isProfileOwner ? '1' : '0' ?>">
        <div class="print-hide flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-3">
            <a href="?action=ratings" class="text-indigo-700 hover:text-indigo-900 font-medium text-sm">
                <i class="fas fa-arrow-left mr-1"></i><?= t('common_back', 'Back') ?>
            </a>
        </div>
        <?php if ($isProfileOwner): ?>
        <div class="print-hide mb-4">
            <div class="cv-edit-toolbar" id="cvEditToolbar">
                <div class="cv-edit-controls">
                    <label class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        Цвет
                        <input id="cvAccentColor" type="color" class="h-7 w-10 rounded border border-slate-200"
                            value="#6366f1">
                    </label>
                    <label class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        Ширина левой колонки
                        <input id="cvAsideWidth" type="range" min="24" max="42" value="32" class="accent-indigo-600">
                    </label>
                    <label class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        Отступы
                        <input id="cvSectionGap" type="range" min="10" max="24" value="16" class="accent-indigo-600">
                    </label>
                    <label class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        Тема
                        <select id="cvThemeSelect" class="rounded border border-slate-200 px-2 py-1 text-xs">
                            <option value="1">Indigo</option>
                            <option value="2">Emerald</option>
                            <option value="3">Amber</option>
                            <option value="4">Slate</option>
                            <option value="5">Rose</option>
                        </select>
                    </label>
                    <label class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        Layout
                        <select id="cvLayoutSelect" class="rounded border border-slate-200 px-2 py-1 text-xs">
                            <option value="1">Classic</option>
                            <option value="2">Wide</option>
                            <option value="3">Modern</option>
                            <option value="4">Minimal</option>
                            <option value="5">Clean</option>
                        </select>
                    </label>
                    <label class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        Стиль
                        <select id="cvSectionStyle" class="rounded border border-slate-200 px-2 py-1 text-xs">
                            <option value="soft">Soft</option>
                            <option value="flat">Flat</option>
                            <option value="outline">Outline</option>
                        </select>
                    </label>
                    <label class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        Шрифт
                        <input id="cvFontScale" type="range" min="90" max="120" value="100" class="accent-indigo-600">
                    </label>
                    <label class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        Радиус
                        <input id="cvCardRadius" type="range" min="8" max="24" value="14" class="accent-indigo-600">
                    </label>
                    <span class="text-xs text-slate-500">Перетаскивайте блоки мышью</span>
                </div>
                <div class="cv-edit-actions">
                    <span id="cvSaveStatus" class="text-xs text-slate-500 hidden sm:inline"></span>
                    <button type="button" id="cvEditToggle"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-pen"></i>
                        <span><?= t('profile_cv_edit', 'Edit CV') ?></span>
                    </button>
                    <button type="button" id="cvEditSave"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition-colors">
                        <i class="fas fa-save"></i>
                        <span><?= t('profile_cv_save', 'Save') ?></span>
                    </button>
                    <button type="button" id="cvEditReset"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-sm font-semibold hover:border-slate-300 hover:text-slate-900 transition-colors">
                        <i class="fas fa-rotate-left"></i>
                        <span><?= t('profile_cv_reset', 'Reset') ?></span>
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <section class="cv-shell">
            <div class="cv-header p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                    <img src="<?= htmlspecialchars((string) ($profileUser['avatar'] ?? getAvatarUrl((string) ($profileUser['name'] ?? 'U')))) ?>"
                        alt="<?= t('profile_avatar_alt', 'Avatar') ?>"
                        class="w-24 h-24 rounded-full object-cover border-4 border-white/40">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-2xl sm:text-3xl font-extrabold break-words text-amber-200">
                            <span class="inline-flex items-center gap-2 flex-wrap">
                                <span><?= htmlspecialchars((string) ($profileUser['name'] ?? '-')) ?></span>
                                <?php if (!empty($profileUser['country_flag'])): ?>
                                    <span class="inline-flex items-center justify-center rounded-full bg-white/15 px-2 py-0.5 leading-none overflow-hidden"
                                        title="<?= htmlspecialchars(t('label_country', 'Страна проживания')) ?>">
                                        <img src="<?= htmlspecialchars((string) ($profileUser['country_flag_url'] ?? tfCountryFlagUrl((string) ($profileUser['country_code'] ?? '')))) ?>"
                                            alt="" loading="lazy" class="block w-4 h-3 object-cover">
                                    </span>
                                <?php endif; ?>
                            </span>
                        </h1>
                        <p class="text-indigo-100 text-base sm:text-lg mt-1 break-words">
                            <?= htmlspecialchars((string) ($profileUser['title'] ?? '-')) ?>
                        </p>
                        <div class="mt-3 text-sm text-indigo-100 flex flex-wrap gap-x-4 gap-y-1">
                            <?php if (!empty($profileUser['location'])): ?>
                                <span><i
                                        class="fas fa-location-dot mr-1"></i><?= htmlspecialchars((string) $profileUser['location']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($profileUser['email'])): ?>
                                <span><i
                                        class="fas fa-envelope mr-1"></i><?= htmlspecialchars((string) $profileUser['email']) ?></span>
                            <?php endif; ?>
                            <span><i class="fas fa-star mr-1"></i><?= (int) ($pointsData['points'] ?? 0) ?>
                                <?= t('profile_points', 'points') ?></span>
                        </div>
                    </div>
                    <div class="text-sm text-indigo-100 flex flex-col items-start sm:items-end gap-1.5">
                        <!-- Кнопка Print CV над текстом -->
                        <button type="button" onclick="window.print()"
                            class="print-hide inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-xs font-medium text-white border border-white/20 transition-colors">
                            <i class="fas fa-print"></i>
                            <span><?= t('profile_print_cv', 'Print CV') ?></span>
                        </button>

                        <!-- Текст "CV generated" под кнопкой -->
                        <div class="text-xs opacity-80">
                            <?= t('profile_cv_generated', 'CV generated') ?>: <?= date('Y-m-d H:i') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-6 lg:p-8">
                <div class="cv-edit-grid">
                    <aside class="cv-edit-column" data-column="aside">
                        <div class="cv-edit-block" data-block="social">
                            <span class="cv-edit-handle">Перетащить</span>
                            <?php $renderSocialSection(); ?>
                        </div>
                        <div class="cv-edit-block" data-block="skills">
                            <span class="cv-edit-handle">Перетащить</span>
                            <?php $renderSkillsSection(); ?>
                        </div>
                        <div class="cv-edit-block" data-block="stats">
                            <span class="cv-edit-handle">Перетащить</span>
                            <?php $renderStatsSection(); ?>
                        </div>
                    </aside>
                    <section class="cv-edit-column" data-column="main">
                        <div class="cv-edit-block" data-block="about">
                            <span class="cv-edit-handle">Перетащить</span>
                            <?php $renderAboutSection(); ?>
                        </div>
                        <div class="cv-edit-block" data-block="experience">
                            <span class="cv-edit-handle">Перетащить</span>
                            <?php $renderExperienceSection(); ?>
                        </div>
                        <div class="cv-edit-block" data-block="education">
                            <span class="cv-edit-handle">Перетащить</span>
                            <?php $renderEducationSection(); ?>
                        </div>
                        <div class="cv-edit-block" data-block="portfolio">
                            <span class="cv-edit-handle">Перетащить</span>
                            <?php $renderPortfolioSection(); ?>
                        </div>
                        <div class="cv-edit-block" data-block="certificates">
                            <span class="cv-edit-handle">Перетащить</span>
                            <?php $renderCertificatesSection(); ?>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </main>
    <script>
        (function () {
            const main = document.querySelector('main[data-profile-id]');
            if (!main) return;

            const profileId = parseInt(main.getAttribute('data-profile-id') || '0', 10);
            const isOwner = main.getAttribute('data-is-owner') === '1';
            const columns = Array.from(document.querySelectorAll('.cv-edit-column'));

            const defaultState = {
                accent: '#6366f1',
                asideWidth: 32,
                gap: 16,
                theme: 1,
                layout: 1,
                fontScale: 100,
                cardRadius: 14,
                sectionStyle: 'soft',
                order: {
                    aside: ['social', 'skills', 'stats'],
                    main: ['about', 'experience', 'education', 'portfolio', 'certificates']
                }
            };

            const serverState = <?= json_encode($cvState, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

            const normalizeState = (raw) => {
                const merged = {
                    ...defaultState,
                    ...(raw || {}),
                    order: {
                        ...defaultState.order,
                        ...((raw && raw.order) ? raw.order : {})
                    }
                };
                const clamp = (v, min, max, fallback) => {
                    const n = parseInt(v, 10);
                    if (!Number.isFinite(n)) return fallback;
                    return Math.max(min, Math.min(max, n));
                };
                merged.accent = /^#[0-9a-f]{6}$/i.test(String(merged.accent || '')) ? String(merged.accent).toLowerCase() : defaultState.accent;
                merged.asideWidth = clamp(merged.asideWidth, 24, 42, defaultState.asideWidth);
                merged.gap = clamp(merged.gap, 10, 24, defaultState.gap);
                merged.theme = clamp(merged.theme, 1, 5, defaultState.theme);
                merged.layout = clamp(merged.layout, 1, 5, defaultState.layout);
                merged.fontScale = clamp(merged.fontScale, 90, 120, defaultState.fontScale);
                merged.cardRadius = clamp(merged.cardRadius, 8, 24, defaultState.cardRadius);
                merged.sectionStyle = ['soft', 'flat', 'outline'].includes(merged.sectionStyle) ? merged.sectionStyle : defaultState.sectionStyle;
                return merged;
            };

            let state = normalizeState(serverState);

            const mix = (hex, target, weight) => {
                const toRgb = (h) => {
                    const clean = String(h).replace('#', '');
                    const bigint = parseInt(clean, 16);
                    return [bigint >> 16 & 255, bigint >> 8 & 255, bigint & 255];
                };
                const toHex = (rgb) => '#' + rgb.map((v) => {
                    const clamped = Math.max(0, Math.min(255, Math.round(v)));
                    return clamped.toString(16).padStart(2, '0');
                }).join('');
                const [r1, g1, b1] = toRgb(hex);
                const [r2, g2, b2] = toRgb(target);
                return toHex([
                    r1 + (r2 - r1) * weight,
                    g1 + (g2 - g1) * weight,
                    b1 + (b2 - b1) * weight
                ]);
            };

            const setVariantClass = (prefix, value, max) => {
                for (let i = 1; i <= max; i++) {
                    main.classList.remove(`${prefix}${i}`);
                }
                main.classList.add(`${prefix}${value}`);
            };

            const setStyleClass = (style) => {
                ['soft', 'flat', 'outline'].forEach((name) => main.classList.remove(`cv-style-${name}`));
                main.classList.add(`cv-style-${style}`);
            };

            const applyColors = (accent) => {
                const strong = mix(accent, '#000000', 0.35);
                const soft = mix(accent, '#ffffff', 0.86);
                const chipBg = mix(accent, '#ffffff', 0.9);
                const chipText = mix(accent, '#000000', 0.3);
                const listBg = mix(accent, '#ffffff', 0.92);
                const headerFrom = mix(accent, '#000000', 0.6);
                const headerTo = mix(accent, '#000000', 0.35);
                const bodyA = mix(accent, '#ffffff', 0.88);
                const bodyB = mix(accent, '#ffffff', 0.82);

                main.style.setProperty('--cv-accent', accent);
                main.style.setProperty('--cv-accent-strong', strong);
                main.style.setProperty('--cv-accent-soft', soft);
                main.style.setProperty('--cv-chip-bg', chipBg);
                main.style.setProperty('--cv-chip-text', chipText);
                main.style.setProperty('--cv-list-bg', listBg);
                main.style.setProperty('--cv-header-from', headerFrom);
                main.style.setProperty('--cv-header-to', headerTo);
                main.style.setProperty('--cv-body-bg', `linear-gradient(145deg, ${bodyA} 0%, #f8fafc 55%, ${bodyB} 100%)`);
            };

            const applyLayout = (asideWidth, gap, fontScale, cardRadius) => {
                main.style.setProperty('--cv-aside-width', `${asideWidth}%`);
                main.style.setProperty('--cv-section-gap', `${gap}px`);
                main.style.setProperty('--cv-column-gap', `${Math.max(12, gap + 4)}px`);
                main.style.setProperty('--cv-font-scale', `${fontScale}`);
                main.style.setProperty('--cv-card-radius', `${cardRadius}px`);
            };

            const applyOrder = (order) => {
                columns.forEach((col) => {
                    const key = col.getAttribute('data-column');
                    const desired = order[key];
                    if (!Array.isArray(desired)) return;
                    desired.forEach((blockKey) => {
                        const block = col.querySelector(`.cv-edit-block[data-block="${blockKey}"]`);
                        if (block) col.appendChild(block);
                    });
                });
            };

            const serializeOrder = () => {
                const order = {};
                columns.forEach((col) => {
                    const key = col.getAttribute('data-column');
                    order[key] = Array.from(col.querySelectorAll('.cv-edit-block')).map((block) => block.getAttribute('data-block'));
                });
                return order;
            };

            const applyState = (nextState) => {
                state = normalizeState(nextState);
                applyColors(state.accent);
                applyLayout(state.asideWidth, state.gap, state.fontScale, state.cardRadius);
                setVariantClass('cv-theme-', state.theme, 5);
                setVariantClass('cv-layout-', state.layout, 5);
                setStyleClass(state.sectionStyle);
                applyOrder(state.order);
            };

            applyState(state);

            if (!isOwner) {
                return;
            }

            const editToggle = document.getElementById('cvEditToggle');
            const editSave = document.getElementById('cvEditSave');
            const editReset = document.getElementById('cvEditReset');
            const saveStatus = document.getElementById('cvSaveStatus');
            const cvEditLabel = <?= json_encode(t('profile_cv_edit', 'Edit CV'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
            const cvDoneLabel = <?= json_encode(t('profile_cv_done', 'Done'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

            const accentInput = document.getElementById('cvAccentColor');
            const asideRange = document.getElementById('cvAsideWidth');
            const gapRange = document.getElementById('cvSectionGap');
            const themeSelect = document.getElementById('cvThemeSelect');
            const layoutSelect = document.getElementById('cvLayoutSelect');
            const styleSelect = document.getElementById('cvSectionStyle');
            const fontScale = document.getElementById('cvFontScale');
            const cardRadius = document.getElementById('cvCardRadius');

            const renderControls = () => {
                accentInput.value = state.accent;
                asideRange.value = String(state.asideWidth);
                gapRange.value = String(state.gap);
                themeSelect.value = String(state.theme);
                layoutSelect.value = String(state.layout);
                styleSelect.value = state.sectionStyle;
                fontScale.value = String(state.fontScale);
                cardRadius.value = String(state.cardRadius);
            };

            renderControls();

            const setStatus = (text, isError = false) => {
                if (!saveStatus) return;
                saveStatus.textContent = text;
                saveStatus.classList.remove('hidden', 'text-emerald-600', 'text-rose-600', 'text-slate-500');
                saveStatus.classList.add(isError ? 'text-rose-600' : 'text-emerald-600');
                if (!text) saveStatus.classList.add('hidden');
            };

            const persistState = async () => {
                const payload = {
                    profile_id: profileId,
                    settings: {
                        ...state,
                        order: serializeOrder()
                    }
                };
                setStatus('Сохранение...');
                try {
                    const response = await fetch('?action=save-cv-customization', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    });
                    const result = await response.json();
                    if (!result || !result.success) {
                        setStatus((result && result.message) ? result.message : 'Ошибка сохранения', true);
                        return;
                    }
                    applyState(result.settings || payload.settings);
                    renderControls();
                    setStatus('Сохранено');
                } catch (e) {
                    setStatus('Ошибка сети', true);
                }
            };

            let editing = false;
            const setEditing = (value) => {
                editing = !!value;
                document.body.classList.toggle('cv-editing', editing);
                document.querySelectorAll('.cv-edit-block').forEach((block) => {
                    block.setAttribute('draggable', editing ? 'true' : 'false');
                });
                const label = editToggle ? editToggle.querySelector('span') : null;
                if (label) label.textContent = editing ? cvDoneLabel : cvEditLabel;
            };

            if (editToggle) {
                editToggle.addEventListener('click', () => setEditing(!editing));
            }
            if (editSave) {
                editSave.addEventListener('click', () => {
                    state.order = serializeOrder();
                    persistState();
                });
            }
            if (editReset) {
                editReset.addEventListener('click', async () => {
                    try {
                        const response = await fetch('?action=reset-cv-customization', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ profile_id: profileId })
                        });
                        const result = await response.json();
                        if (!result || !result.success) {
                            setStatus((result && result.message) ? result.message : 'Ошибка сброса', true);
                            return;
                        }
                        applyState(result.settings || defaultState);
                        renderControls();
                        setStatus('Сброшено');
                    } catch (e) {
                        setStatus('Ошибка сети', true);
                    }
                });
            }

            const applyFromControls = () => {
                const next = {
                    ...state,
                    accent: accentInput.value,
                    asideWidth: parseInt(asideRange.value, 10),
                    gap: parseInt(gapRange.value, 10),
                    theme: parseInt(themeSelect.value, 10),
                    layout: parseInt(layoutSelect.value, 10),
                    sectionStyle: styleSelect.value,
                    fontScale: parseInt(fontScale.value, 10),
                    cardRadius: parseInt(cardRadius.value, 10),
                    order: serializeOrder()
                };
                applyState(next);
                setStatus('');
            };

            [accentInput, asideRange, gapRange, themeSelect, layoutSelect, styleSelect, fontScale, cardRadius].forEach((el) => {
                if (!el) return;
                el.addEventListener('input', applyFromControls);
                el.addEventListener('change', applyFromControls);
            });

            let dragged = null;
            const getDragAfterElement = (container, y) => {
                const elements = [...container.querySelectorAll('.cv-edit-block:not(.dragging)')];
                return elements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return { offset, element: child };
                    }
                    return closest;
                }, { offset: Number.NEGATIVE_INFINITY, element: null }).element;
            };

            document.querySelectorAll('.cv-edit-block').forEach((block) => {
                block.setAttribute('draggable', 'false');
                block.addEventListener('dragstart', (e) => {
                    if (!editing) {
                        e.preventDefault();
                        return;
                    }
                    dragged = block;
                    block.classList.add('dragging');
                });
                block.addEventListener('dragend', () => {
                    block.classList.remove('dragging');
                    dragged = null;
                    state.order = serializeOrder();
                });
            });

            columns.forEach((col) => {
                col.addEventListener('dragover', (e) => {
                    if (!editing || !dragged) return;
                    e.preventDefault();
                    const afterElement = getDragAfterElement(col, e.clientY);
                    if (!afterElement) col.appendChild(dragged);
                    else col.insertBefore(dragged, afterElement);
                });
                col.addEventListener('drop', (e) => {
                    if (!editing) return;
                    e.preventDefault();
                    state.order = serializeOrder();
                });
            });
        })();
    </script>
</body>

</html>



