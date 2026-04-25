<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
if (!isset($countryOptions) || !is_array($countryOptions)) {
    $countryOptions = require __DIR__ . '/../includes/countries.php';
}
$countryFlag = static function (string $code): string {
    $code = strtoupper(trim($code));
    if (strlen($code) !== 2) {
        return '';
    }
    $offset = 127397;
    return mb_chr($offset + ord($code[0]), 'UTF-8') . mb_chr($offset + ord($code[1]), 'UTF-8');
};
$currentCountryCode = strtoupper(trim((string) ($user['country_code'] ?? '')));
$currentCountryName = trim((string) ($user['country_name'] ?? ''));
if ($currentCountryCode === '' && $currentCountryName !== '') {
    foreach ($countryOptions as $code => $countryName) {
        if (mb_strtolower((string) $countryName, 'UTF-8') === mb_strtolower($currentCountryName, 'UTF-8')) {
            $currentCountryCode = strtoupper((string) $code);
            break;
        }
    }
}
if ($currentCountryCode !== '' && $currentCountryName === '' && isset($countryOptions[$currentCountryCode])) {
    $currentCountryName = (string) $countryOptions[$currentCountryCode];
}
$heatmap = getActivityHeatmap($user['id'] ?? 0, 365);
$heatmapStart = strtotime($heatmap['start'] ?? date('Y-m-d'));
$heatmapDays = (int) ($heatmap['days'] ?? 84);
$heatmapMap = $heatmap['map'] ?? [];
$yearDays = 365;
$yearStart = date('Y-m-d', strtotime('-' . ($yearDays - 1) . ' days'));
$yearStartTs = strtotime($yearStart);
$profileActivities = is_array($user['activities'] ?? null) ? $user['activities'] : [];

$buildHeatmap = static function (array $activities, int $days, callable $filter) use ($yearStartTs): array {
    $startDate = date('Y-m-d', $yearStartTs);
    $map = [];
    for ($i = 0; $i < $days; $i++) {
        $date = date('Y-m-d', $yearStartTs + ($i * 86400));
        $map[$date] = ['count' => 0, 'items' => []];
    }

    foreach ($activities as $activity) {
        $time = (string) ($activity['activity_time'] ?? '');
        if ($time === '') {
            continue;
        }

        $date = date('Y-m-d', strtotime($time));
        if (!isset($map[$date])) {
            continue;
        }

        if (!$filter($activity)) {
            continue;
        }

        $map[$date]['count']++;
        $text = normalizeMojibakeText((string) ($activity['activity_text'] ?? ''));

        $map[$date]['items'][] = [
            'time' => date('H:i', strtotime($time)),
            'text' => translateActivityMessage($text),
        ];
    }

    return [
        'start' => $startDate,
        'days' => $days,
        'map' => $map,
    ];
};

$heatmapAll = $buildHeatmap($profileActivities, $yearDays, static function () {
    return true;
});

$heatmapStudy = $buildHeatmap($profileActivities, $yearDays, static function ($activity) {
    $type = (string) ($activity['activity_type'] ?? '');
    return in_array($type, ['lesson', 'course'], true);
});

$heatmapContest = $buildHeatmap($profileActivities, $yearDays, static function ($activity) {
    $type = (string) ($activity['activity_type'] ?? '');
    if (in_array($type, ['contest', 'contest_task', 'competition'], true)) {
        return true;
    }

    $text = normalizeMojibakeText((string) ($activity['activity_text'] ?? ''));
    return stripos($text, 'контест') !== false || stripos($text, 'contest') !== false;
});

$gridStartTs = $yearStartTs;
$gridStartWeekday = (int) date('w', $gridStartTs);
if ($gridStartWeekday > 0) {
    $gridStartTs -= $gridStartWeekday * 86400;
}

$gridEndTs = $yearStartTs + (($yearDays - 1) * 86400);
$gridEndWeekday = (int) date('w', $gridEndTs);
$gridDays = $yearDays + $gridStartWeekday + (6 - $gridEndWeekday);
$gridWeeks = (int) ceil($gridDays / 7);

$appMap = [];
if (!empty($user['applications'])) {
    foreach ($user['applications'] as $app) {
        $title = $app['vacancy_title'] ?? '';
        if ($title !== '') {
            $appMap[$title] = $app['id'];
        }
    }
}

$buildProfileTabUrl = static function (string $targetTab): string {
    $params = $_GET;
    $params['action'] = 'profile';
    $params['tab'] = $targetTab;
    if (function_exists('currentLang')) {
        $params['lang'] = currentLang();
    }
    return '?' . http_build_query($params);
};

$profileLang = function_exists('currentLang') ? currentLang() : 'ru';
if (!in_array($profileLang, ['ru', 'en', 'tg'], true)) {
    $profileLang = 'ru';
}

$profileTabLabelsMap = [
    'ru' => [
        'overview' => 'Обзор',
        'experience' => 'Опыт работы',
        'education' => 'Образование',
        'portfolio' => 'Портфолио',
        'certificates' => 'Сертификаты',
        'activity' => 'Активность',
        'mobile_label' => 'Раздел профиля',
        'activity_empty' => 'Активность пока отсутствует',
    ],
    'en' => [
        'overview' => 'Overview',
        'experience' => 'Work experience',
        'education' => 'Education',
        'portfolio' => 'Portfolio',
        'certificates' => 'Certificates',
        'activity' => 'Activity',
        'mobile_label' => 'Profile section',
        'activity_empty' => 'No activity yet',
    ],
    'tg' => [
        'overview' => 'Шарҳ',
        'experience' => 'Таҷрибаи корӣ',
        'education' => 'Маориф',
        'portfolio' => 'Портфолио',
        'certificates' => 'Сертификатҳо',
        'activity' => 'Фаъолият',
        'mobile_label' => 'Бахши профил',
        'activity_empty' => 'Ҳоло фаъолият нест',
    ],
];

$profileMonthLabelsMap = [
    'ru' => ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
    'en' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    'tg' => ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
];

$profileDayLabelsMap = [
    'ru' => ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
    'en' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    'tg' => ['Якш', 'Дш', 'Сш', 'Чш', 'Пш', 'Ҷм', 'Шн'],
];

$profileMonthLabels = $profileMonthLabelsMap[$profileLang] ?? $profileMonthLabelsMap['ru'];
$profileDayLabels = $profileDayLabelsMap[$profileLang] ?? $profileDayLabelsMap['ru'];
$profileTabLabels = $profileTabLabelsMap[$profileLang] ?? $profileTabLabelsMap['ru'];

$profileMonthColumns = [];
for ($week = 0; $week < $gridWeeks; $week++) {
    $weekTs = $gridStartTs + ($week * 7 * 86400);
    $monthIndex = (int) date('n', $weekTs) - 1;
    $profileMonthColumns[$week] = $profileMonthLabels[$monthIndex] ?? '';
}

$skillsAll = is_array($user['skills'] ?? null) ? $user['skills'] : [];

$skillsTechnical = array_values(array_filter($skillsAll, static function ($skill) {
    return ($skill['category'] ?? 'technical') === 'technical';
}));

$skillsSoft = array_values(array_filter($skillsAll, static function ($skill) {
    return ($skill['category'] ?? 'technical') === 'soft';
}));

$technicalSkillOptions = [
    'JavaScript',
    'TypeScript',
    'PHP',
    'Python',
    'Java',
    'C++',
    'C#',
    'Go',
    'Rust',
    'SQL',
    'HTML/CSS',
    'React',
    'Vue',
    'Node.js',
    'Laravel',
    'Django',
    'PostgreSQL',
    'MySQL',
    'Docker',
    'Kubernetes',
    'Git',
    'Linux',
    'AWS',
    'DevOps'
];

$softSkillOptions = [
    'Коммуникация',
    'Командная работа',
    'Решение проблем',
    'Лидерство',
    'Тайм-менеджмент',
    'Адаптивность',
    'Критическое мышление',
    'Креативность',
    'Разрешение конфликтов',
    'Менторство',
    'Переговоры',
    'Ответственность',
    'Презентация',
    'Эмпатия',
    'Принятие решений'
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <title><?= t('profile_page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.10/dist/cdn.min.js" defer></script>
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

        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
            font-family: "Inter", sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .fade-in {
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
            overflow: hidden;
        }

        .progress-bar {
            height: 6px;
            border-radius: 3px;
            background-color: #e0e7ff;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
            background-color: #4f46e5;
            transition: width 0.3s ease;
        }

        .tf-quiz-loader {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .tf-quiz-spinner {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            border: 3px solid #cbd5f5;
            border-top-color: #4f46e5;
            animation: tf-spin 0.9s linear infinite;
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

        .exam-option.variant-0 {
            border-color: #c7d2fe;
            background: #eef2ff;
        }

        .exam-option.variant-1 {
            border-color: #a5f3fc;
            background: #ecfeff;
        }

        .exam-option.variant-2 {
            border-color: #fde68a;
            background: #fef3c7;
        }

        .exam-option.variant-3 {
            border-color: #86efac;
            background: #dcfce7;
        }

        .exam-option-key {
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

        .exam-option-key-0 {
            background: #eef2ff;
            color: #3730a3;
            border-color: #c7d2fe;
        }

        .exam-option-key-1 {
            background: #ecfeff;
            color: #155e75;
            border-color: #a5f3fc;
        }

        .exam-option-key-2 {
            background: #fef3c7;
            color: #92400e;
            border-color: #fde68a;
        }

        .exam-option-key-3 {
            background: #dcfce7;
            color: #166534;
            border-color: #86efac;
        }

        .exam-option-text {
            flex: 1;
            min-width: 0;
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

        .exam-question-card {
            position: relative;
        }

        .tf-fade {
            animation: tfFadeIn 220ms ease;
        }

        @keyframes tfFadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes tf-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .skill-level {
            position: relative;
            height: 4px;
            background-color: #e0e7ff;
            border-radius: 2px;
            overflow: hidden;
        }

        .skill-level-fill {
            height: 100%;
            border-radius: 2px;
            background: linear-gradient(90deg, #4f46e5, #818cf8);
            transition: width 0.5s ease;
        }

        .skill-level-fill.skill-verified {
            background: linear-gradient(90deg, #22c55e, #86efac);
        }

        .skill-level-fill.skill-unverified {
            background: linear-gradient(90deg, #f87171, #fecaca);
        }

        .skill-name-verified {
            color: #15803d;
        }

        .skill-name-unverified {
            color: #b91c1c;
        }

        .skill-tag {
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            padding: 4px 12px;
            margin: 4px;
            border-radius: 20px;
            background-color: #e0e7ff;
            color: #4338ca;
            font-size: 14px;
            transition: all 0.2s;
            max-width: 100%;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .skill-tag.skill-verified {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .skill-tag.skill-unverified {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .skill-tag:hover {
            background-color: #c7d2fe;
            cursor: pointer;
        }

        .skill-tag.skill-verified:hover {
            background-color: #bbf7d0;
        }

        .skill-tag.skill-unverified:hover {
            background-color: #fecaca;
        }

        .skill-tag.remove:hover {
            background-color: #fecaca;
            color: #b91c1c;
        }

        .cv-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            min-width: 110px;
            text-align: center;
        }

        .gh-actions {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
        }

        .gh-actions-header {
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f9fafb;
        }

        .gh-actions-item {
            display: grid;
            grid-template-columns: 18px 1fr auto;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .gh-actions-item:last-child {
            border-bottom: none;
        }

        .gh-actions-status {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            margin-top: 6px;
        }

        .gh-actions-status.success {
            background: #22c55e;
        }

        .gh-actions-status.warning {
            background: #f59e0b;
        }

        .gh-actions-status.neutral {
            background: #94a3b8;
        }

        .gh-actions-meta {
            font-size: 0.75rem;
            color: #6b7280;
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .application-status {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-applied {
            background-color: #dbeafe;
            color: #3b82f6;
        }

        .status-interview {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-offer {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #f3f4f6;
            color: #1f2937;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background-color: #e5e7eb;
        }

        .review-dialog {
            margin: 80px auto;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
        }

        .review-stars-row {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .review-star {
            appearance: none;
            border: none;
            background: transparent;
            padding: 6px;
            border-radius: 12px;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
            transition: transform 0.16s ease, color 0.16s ease, filter 0.16s ease, box-shadow 0.16s ease;
        }

        .review-star:hover {
            transform: translateY(-2px) scale(1.05);
            filter: drop-shadow(0 10px 16px rgba(245, 158, 11, 0.25));
        }

        .review-star:focus-visible {
            outline: none;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.18);
        }

        .review-stars-wrap {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            background: rgba(248, 250, 252, 0.9);
        }

        .btn-ghost {
            background: transparent;
            color: #4f46e5;
            border: 1px solid #e0e7ff;
            padding: 8px 14px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-ghost:hover {
            background: #eef2ff;
        }

        .btn-outline {
            background: #fff;
            color: #374151;
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-outline:hover {
            border-color: #c7d2fe;
            color: #4338ca;
            background: #f8faff;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid #eef2f7;
        }

        .section-body {
            padding: 16px 20px;
        }

        .timeline-item {
            position: relative;
            padding: 16px 16px 16px 24px;
            border: 1px solid #eef2f7;
            border-radius: 14px;
            background: #f9fbff;
        }

        .timeline-dot {
            position: absolute;
            left: -6px;
            top: 24px;
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: linear-gradient(135deg, #6366f1, #22c55e);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 12px;
            font-weight: 600;
        }

        .action-icon {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #4b5563;
            transition: all 0.2s;
        }

        .action-icon:hover {
            border-color: #c7d2fe;
            color: #4338ca;
            background: #eef2ff;
        }

        .portfolio-card {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.05);
        }

        .portfolio-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px dashed #c7d2fe;
            border-radius: 10px;
            background: #f8faff;
            color: #374151;
        }

        input[type="file"]::file-selector-button {
            margin-right: 10px;
            border: 1px solid #c7d2fe;
            background: #eef2ff;
            color: #4338ca;
            padding: 6px 10px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        input[type="file"]::file-selector-button:hover {
            background: #e0e7ff;
        }

        .input-field {
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            width: 100%;
            transition: all 0.2s;
        }

        .input-field:focus {
            outline: none;
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.2);
        }

        .country-picker {
            position: relative;
        }

        .country-picker-toggle {
            width: 100%;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            text-align: left;
            background: #fff;
        }

        .country-picker-value {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            min-width: 0;
        }

        .country-flag-icon {
            width: 22px;
            height: 16px;
            flex: 0 0 auto;
            border-radius: 3px;
            object-fit: cover;
            background: #f3f4f6;
            box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.08);
        }

        .country-picker-label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .country-picker-menu {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 0.5rem);
            z-index: 40;
            max-height: 18rem;
            overflow: auto;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            background: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
        }

        .country-picker-option {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.65rem 0.75rem;
            border: 0;
            border-radius: 0.6rem;
            background: transparent;
            color: #0f172a;
            text-align: left;
            cursor: pointer;
        }

        .country-picker-option:hover {
            background: #f8fafc;
        }

        .country-picker-option-flag {
            width: 20px;
            height: 14px;
            flex: 0 0 auto;
            border-radius: 2px;
            object-fit: cover;
            background: #f3f4f6;
        }

        .password-toggle {
            position: relative;
        }

        .password-input {
            padding-right: 44px;
        }

        .password-toggle button {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }

        .password-toggle button:hover {
            color: #374151;
        }

        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .tab-active {
            border-bottom: 2px solid #4f46e5;
            color: #4f46e5;
            font-weight: 600;
        }

        .tab-inactive {
            color: #6b7280;
            transition: all 0.2s;
        }

        .tab-inactive:hover {
            color: #4b5563;
        }

        .notification-badge {
            animation: pulse 2s infinite;
        }

        .heatmap {
            display: grid;
            grid-auto-flow: column;
            grid-template-rows: repeat(7, 1fr);
            gap: 4px;
        }

        .heat-cell {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            background: #e5e7eb;
        }

        .heat-1 {
            background: #c7d2fe;
        }

        .heat-2 {
            background: #a5b4fc;
        }

        .heat-3 {
            background: #818cf8;
        }

        .heat-4 {
            background: #4f46e5;
        }

        .gh-heatmap-card {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
            padding: 18px;
        }

        .gh-heatmap-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .gh-heatmap-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }

        .gh-heatmap-subtitle {
            font-size: 12px;
            color: #6b7280;
        }

        .gh-heatmap-wrapper {
            overflow-x: auto;
            padding-bottom: 4px;
        }

        .gh-heatmap-grid {
            display: grid;
            grid-template-columns: 36px 1fr;
            column-gap: 10px;
        }

        .gh-heatmap-months {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: 14px;
            gap: 4px;
            padding-left: 2px;
            margin-bottom: 6px;
        }

        .gh-heatmap-month {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .gh-heatmap-days {
            display: grid;
            grid-template-rows: repeat(7, 1fr);
            gap: 4px;
            font-size: 10px;
            color: #6b7280;
        }

        .gh-heatmap-weeks {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: 14px;
            gap: 4px;
        }

        .gh-heatmap-week {
            display: grid;
            grid-template-rows: repeat(7, 1fr);
            gap: 4px;
        }

        .gh-heat-cell {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            border: 1px solid transparent;
            background: #ebedf0;
            cursor: pointer;
            padding: 0;
        }

        .gh-heat-cell.is-outside {
            background: transparent;
            cursor: default;
            border-color: transparent;
        }

        .gh-heat-cell.level-1 {
            background: #dbeafe;
        }

        .gh-heat-cell.level-2 {
            background: #93c5fd;
        }

        .gh-heat-cell.level-3 {
            background: #3b82f6;
        }

        .gh-heat-cell.level-4 {
            background: #1d4ed8;
        }

        .gh-heat-cell.is-active {
            outline: 2px solid #1d4ed8;
            outline-offset: 1px;
        }

        .gh-heatmap-legend {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #6b7280;
        }

        .gh-heatmap-legend .legend-cells {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .gh-heatmap-detail {
            margin-top: 14px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .gh-heatmap-detail-title {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .gh-heatmap-detail-list {
            display: grid;
            gap: 6px;
            font-size: 13px;
            color: #1f2937;
        }

        .gh-heatmap-detail-item {
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        .gh-heatmap-detail-time {
            font-size: 11px;
            color: #64748b;
            min-width: 44px;
        }

        .gh-heatmap-detail-text {
            font-size: 13px;
            color: #111827;
            line-height: 1.4;
        }

        .gh-heatmap-empty {
            font-size: 12px;
            color: #94a3b8;
        }

        .gh-heatmap-tooltip {
            position: fixed;
            z-index: 80;
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

        .gh-heatmap-tooltip.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .gh-heatmap-tooltip-title {
            font-size: 12px;
            font-weight: 700;
            color: #c7d2fe;
            margin-bottom: 8px;
        }

        .gh-heatmap-tooltip-list {
            display: grid;
            gap: 6px;
        }

        .gh-heatmap-tooltip-item {
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr);
            gap: 8px;
            align-items: start;
            font-size: 12px;
        }

        .gh-heatmap-tooltip-time {
            color: #93c5fd;
            font-weight: 700;
        }

        .gh-heatmap-tooltip-text {
            color: #e2e8f0;
            line-height: 1.4;
            word-break: break-word;
        }

        .viz-card {
            position: relative;
            background: linear-gradient(135deg, #ffffff, #f8faff);
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 16px;
            box-shadow: 0 10px 24px rgba(79, 70, 229, 0.08);
            overflow: hidden;
        }

        .viz-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 10% 10%, rgba(99, 102, 241, 0.12), transparent 55%),
                radial-gradient(circle at 90% 0%, rgba(34, 197, 94, 0.12), transparent 45%);
            opacity: 0.6;
            pointer-events: none;
        }

        .viz-card>* {
            position: relative;
            z-index: 1;
        }

        .viz-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 999px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 11px;
            font-weight: 600;
        }

        .viz-donut {
            width: 150px;
            height: 150px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: conic-gradient(#6366f1 0 calc(var(--value) * 1%), #e2e8f0 calc(var(--value) * 1%) 100%);
            position: relative;
        }

        .viz-donut::before {
            content: "";
            position: absolute;
            inset: 12px;
            background: #ffffff;
            border-radius: 999px;
            box-shadow: inset 0 0 0 1px #eef2ff;
        }

        .viz-donut-center {
            position: relative;
            text-align: center;
            z-index: 1;
        }

        .viz-split-bar {
            display: flex;
            height: 12px;
            border-radius: 999px;
            overflow: hidden;
            background: #e2e8f0;
        }

        .viz-split-bar span {
            height: 100%;
            transition: width 0.6s ease;
        }

        .viz-verified {
            background: linear-gradient(90deg, #22c55e, #16a34a);
        }

        .viz-unverified {
            background: linear-gradient(90deg, #0ea5e9, #ef4444);
        }

        .viz-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            display: inline-block;
        }

        .viz-dot-verified {
            background: #22c55e;
        }

        .viz-dot-unverified {
            background: #ef4444;
        }

        .viz-bars {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: minmax(0, 1fr);
            gap: 6px;
            align-items: end;
            height: 90px;
        }

        .viz-bar-track {
            height: 100%;
            background: #eef2ff;
            border-radius: 999px;
            display: flex;
            align-items: flex-end;
            padding: 2px;
        }

        .viz-bar-fill {
            width: 100%;
            border-radius: 999px;
            background: linear-gradient(180deg, #6366f1, #22c55e);
            transform-origin: bottom;
            transform: scaleY(0);
            animation: viz-rise 1.1s ease forwards;
        }

        @keyframes viz-rise {
            to {
                transform: scaleY(1);
            }
        }

        .badge-verified {
            background: #dcfce7;
            color: #16a34a;
            padding: 2px 6px;
            border-radius: 999px;
            font-size: 11px;
        }

        .badge-unverified {
            background: #fee2e2;
            color: #b91c1c;
            padding: 2px 6px;
            border-radius: 999px;
            font-size: 11px;
        }

        .skill-row-verified {
            border-left: 3px solid #22c55e;
        }

        .skill-row-unverified {
            border-left: 3px solid #ef4444;
        }

        @media (max-width: 768px) {
            .viz-donut {
                width: 120px;
                height: 120px;
            }

            .viz-bars {
                height: 70px;
                gap: 4px;
            }
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .profile-tabs-nav a {
            padding: 0.45rem 0.15rem;
        }

        .profile-mobile-switch {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: linear-gradient(135deg, #ffffff, #f8faff);
            padding: 0.75rem;
            box-shadow: 0 8px 22px rgba(79, 70, 229, 0.08);
        }

        .profile-mobile-switch-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 0.45rem;
        }

        .profile-mobile-tabs {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.55rem;
        }

        .profile-mobile-tab {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #111827;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 0.7rem 0.6rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.06);
        }

        .profile-mobile-tab i {
            font-size: 0.85rem;
            color: #6366f1;
        }

        .profile-mobile-tab.is-active {
            border-color: #6366f1;
            background: linear-gradient(135deg, #eef2ff, #ffffff);
            color: #312e81;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.18);
        }

        .profile-mobile-tab:active {
            transform: translateY(1px);
        }

        @keyframes tf-page-in {
            from {
                opacity: 0;
                transform: translateY(14px) scale(0.995);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes tf-float-soft {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .card,
        .section-card,
        .timeline-item,
        .portfolio-card,
        .skill-tag,
        .profile-mobile-tab,
        .action-icon {
            transition: transform 0.45s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.45s cubic-bezier(0.22, 1, 0.36, 1), border-color 0.35s ease, background-color 0.35s ease;
            will-change: transform, box-shadow;
            transform: translateZ(0);
        }

        .card:hover,
        .section-card:hover,
        .timeline-item:hover,
        .portfolio-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 36px rgba(15, 23, 42, 0.14);
        }

        .skill-tag:hover {
            transform: translateY(-2px) scale(1.02);
        }

        .btn-primary,
        .btn-secondary,
        .btn-ghost,
        .btn-outline {
            position: relative;
            overflow: hidden;
            transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.28s ease, background-color 0.28s ease, color 0.28s ease;
        }

        .btn-primary::after,
        .btn-secondary::after,
        .btn-ghost::after,
        .btn-outline::after {
            content: "";
            position: absolute;
            inset: -120% auto auto -45%;
            width: 40%;
            height: 320%;
            transform: rotate(18deg);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
            pointer-events: none;
            transition: transform 0.65s ease;
        }

        .btn-primary:hover::after,
        .btn-secondary:hover::after,
        .btn-ghost:hover::after,
        .btn-outline:hover::after {
            transform: translateX(285%) rotate(18deg);
        }

        .btn-primary:hover,
        .btn-secondary:hover,
        .btn-ghost:hover,
        .btn-outline:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
        }

        .tf-reveal {
            opacity: 0;
            transform: translateY(20px) scale(0.99);
        }

        .tf-reveal.is-visible {
            opacity: 1;
            transform: translateY(0) scale(1);
            transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1), transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
            transition-delay: var(--reveal-delay, 0ms);
        }

        .profile-topbar h1 i {
            animation: tf-float-soft 3.6s ease-in-out infinite;
        }

        @media (max-width: 420px) {
            .profile-mobile-tabs {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .profile-topbar {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
            }

            .profile-topbar-actions {
                display: grid;
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .profile-topbar-actions .btn-primary,
            .profile-topbar-actions .btn-secondary {
                width: 100%;
            }

            .section-head {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
            }

            .section-head .btn-ghost {
                width: 100%;
                justify-content: center;
                display: inline-flex;
            }
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(79, 70, 229, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
            }
        }
    </style>
</head>

<body class="bg-gray-50 m-0">
    <?php include 'includes/header.php'; ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Profile Sidebar -->
            <div class="lg:col-span-1">
                <div class="card overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 h-24"></div>
                    <div class="px-6 py-8 -mt-12 text-center">
                        <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="<?= t('profile_avatar_alt') ?>"
                            class="w-24 h-24 rounded-full mx-auto object-cover border-4 border-white" />
                        <h2 class="mt-4 text-xl font-bold text-gray-900"><?= htmlspecialchars($user['name']) ?></h2>
                        <p class="text-indigo-600 font-medium"><?= htmlspecialchars($user['title']) ?></p>
                        <p class="text-gray-500 text-sm mt-1 flex items-center justify-center">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <span><?= htmlspecialchars($user['location']) ?></span>
                        </p>
                        <?php if ($currentCountryName !== ''): ?>
                            <p class="text-gray-400 text-sm mt-1 flex items-center justify-center gap-1">
                                <span><?= htmlspecialchars(t('label_country', 'Страна проживания')) ?>:</span>
                                <span><?= htmlspecialchars(trim((string) ($countryFlag($currentCountryCode) . ' ' . $currentCountryName))) ?></span>
                            </p>
                        <?php endif; ?>
                        <div class="mt-6 flex justify-center flex-wrap gap-4">
                            <?php if (!empty($user['social_linkedin'])): ?>
                                <a href="<?= htmlspecialchars($user['social_linkedin']) ?>" target="_blank"
                                    rel="noopener noreferrer" class="text-gray-400 hover:text-gray-700" title="LinkedIn">
                                    <span class="sr-only">LinkedIn</span>
                                    <i class="fab fa-linkedin-in text-xl"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($user['social_github'])): ?>
                                <a href="<?= htmlspecialchars($user['social_github']) ?>" target="_blank"
                                    rel="noopener noreferrer" class="text-gray-400 hover:text-gray-700" title="GitHub">
                                    <span class="sr-only">GitHub</span>
                                    <i class="fab fa-github text-xl"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($user['social_telegram'])): ?>
                                <a href="<?= htmlspecialchars($user['social_telegram']) ?>" target="_blank"
                                    rel="noopener noreferrer" class="text-gray-400 hover:text-gray-700" title="Telegram">
                                    <span class="sr-only">Telegram</span>
                                    <i class="fab fa-telegram-plane text-xl"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($user['social_website'])): ?>
                                <a href="<?= htmlspecialchars($user['social_website']) ?>" target="_blank"
                                    rel="noopener noreferrer" class="text-gray-400 hover:text-gray-700"
                                    title="<?= t('profile_website', 'Website') ?>">
                                    <span class="sr-only"><?= t('profile_website', 'Website') ?></span>
                                    <i class="fas fa-globe text-xl"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-star text-yellow-400 mr-1"></i> <?= t('profile_skills') ?>
                        </h3>
                        <div class="flex flex-wrap">
                            <?php if (!empty($skillsTechnical)): ?>
                                <div class="w-full mb-4">
                                    <p class="text-xs uppercase tracking-widest text-gray-400 mb-2">Technical</p>
                                    <?php foreach ($skillsTechnical as $skill): ?>
                                        <?php $skillVerified = !empty($skill['is_verified']); ?>
                                        <div class="w-full mb-3">
                                            <div class="flex justify-between text-xs mb-1">
                                                <span
                                                    class="<?= $skillVerified ? 'skill-name-verified' : 'skill-name-unverified' ?>">
                                                    <?= htmlspecialchars($skill['skill_name']) ?>
                                                    <?php if ($skillVerified): ?>
                                                        <i class="fas fa-check-circle text-emerald-500 ml-1"></i>
                                                    <?php endif; ?>
                                                </span>
                                                <span><?= $skill['skill_level'] ?>%</span>
                                            </div>
                                            <div class="skill-level">
                                                <div class="skill-level-fill <?= $skillVerified ? 'skill-verified' : 'skill-unverified' ?>"
                                                    style="width: <?= $skill['skill_level'] ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($skillsSoft)): ?>
                                <div class="w-full mb-2">
                                    <p class="text-xs uppercase tracking-widest text-gray-400 mb-2">Soft Skills</p>
                                    <?php foreach ($skillsSoft as $skill): ?>
                                        <?php $skillVerified = !empty($skill['is_verified']); ?>
                                        <div class="w-full mb-3">
                                            <div class="flex justify-between text-xs mb-1">
                                                <span
                                                    class="<?= $skillVerified ? 'skill-name-verified' : 'skill-name-unverified' ?>">
                                                    <?= htmlspecialchars($skill['skill_name']) ?>
                                                    <?php if ($skillVerified): ?>
                                                        <i class="fas fa-check-circle text-emerald-500 ml-1"></i>
                                                    <?php endif; ?>
                                                </span>
                                                <span><?= $skill['skill_level'] ?>%</span>
                                            </div>
                                            <div class="skill-level">
                                                <div class="skill-level-fill <?= $skillVerified ? 'skill-verified' : 'skill-unverified' ?>"
                                                    style="width: <?= $skill['skill_level'] ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (empty($skillsTechnical) && empty($skillsSoft)): ?>
                                <p class="text-gray-500 text-sm"><?= t('profile_skills_empty') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-chart-line mr-1"></i> <?= t('profile_stats') ?>
                        </h3>
                        <?php
                        $pointsData = calculateUserPoints($user);
                        ?>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 text-center">
                            <div>
                                <p class="text-2xl font-bold text-gray-900"><?= uiValue(count($user['experience'])) ?>
                                </p>
                                <p class="text-xs text-gray-500"><?= t('profile_exp') ?></p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900"><?= uiValue(count($user['certificates'])) ?>
                                </p>
                                <p class="text-xs text-gray-500"><?= t('profile_certs') ?></p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900"><?= uiValue(count($user['applications'])) ?>
                                </p>
                                <p class="text-xs text-gray-500"><?= t('profile_apps') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-briefcase mr-1"></i> <?= t('profile_current_job') ?>
                        </h3>
                        <?php if (!empty($user['current_job'])): ?>
                            <div class="text-sm text-gray-900 font-semibold">
                                <?= htmlspecialchars($user['current_job']['vacancy_title'] ?? '') ?>
                            </div>
                            <div class="text-xs text-gray-500">
                                <?= htmlspecialchars($user['current_job']['company'] ?? '') ?>
                            </div>
                            <div class="text-xs text-emerald-600 mt-1"><?= t('profile_employed') ?></div>
                        <?php else: ?>
                            <p class="text-gray-500 text-sm"><?= t('profile_no_job') ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="border-t border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-briefcase mr-1"></i> <?= t('profile_recent_apps') ?>
                        </h3>
                        <?php if (!empty($user['applications'])): ?>
                            <div class="space-y-3">
                                <?php foreach (array_slice($user['applications'], 0, 3) as $application): ?>
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                <?= htmlspecialchars($application['vacancy_title']) ?>
                                            </p>
                                            <p class="text-xs text-gray-500"><?= htmlspecialchars($application['company']) ?>
                                            </p>
                                        </div>
                                        <span
                                            class="application-status <?= getApplicationStatusClass($application['status']) ?>">
                                            <?= getApplicationStatusText($application['status']) ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-center py-2"><?= t('profile_apps_empty') ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Middle Column - Main Content -->
            <div class="lg:col-span-2">
                <div class="flex justify-between items-center mb-6 profile-topbar">
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-user-circle mr-2"></i> <?= t('profile_my_profile') ?>
                    </h1>
                    <div class="flex gap-2 profile-topbar-actions">
                        <a href="?action=profile-view&id=<?= (int) ($user['id'] ?? 0) ?>"
                            class="btn-secondary cv-button">
                            <i class="fas fa-file-lines"></i>
                            <span>CV</span>
                        </a>
                        <button onclick="openReviewModal()" class="btn-secondary">
                            <i class="fas fa-star mr-2"></i> <?= t('profile_platform_review') ?>
                        </button>
                        <button onclick="openEditProfileModal()" class="btn-primary">
                            <i class="fas fa-edit mr-2"></i> <?= t('profile_edit') ?>
                        </button>
                    </div>
                </div>

                <!-- Profile Tabs -->
                <div class="card overflow-hidden">
                    <div class="border-b border-gray-200">
                        <div class="sm:hidden px-4 py-3">
                            <div class="profile-mobile-switch">
                                <div class="profile-mobile-switch-label">
                                    <?= htmlspecialchars($profileTabLabels['mobile_label']) ?>
                                </div>
                                <div class="profile-mobile-tabs">
                                    <a href="<?= htmlspecialchars($buildProfileTabUrl('overview')) ?>"
                                        class="profile-mobile-tab <?= $tab === 'overview' ? 'is-active' : '' ?>">
                                        <i class="fas fa-compass"></i>
                                        <?= htmlspecialchars($profileTabLabels['overview']) ?>
                                    </a>
                                    <a href="<?= htmlspecialchars($buildProfileTabUrl('experience')) ?>"
                                        class="profile-mobile-tab <?= $tab === 'experience' ? 'is-active' : '' ?>">
                                        <i class="fas fa-briefcase"></i>
                                        <?= htmlspecialchars($profileTabLabels['experience']) ?>
                                    </a>
                                    <a href="<?= htmlspecialchars($buildProfileTabUrl('education')) ?>"
                                        class="profile-mobile-tab <?= $tab === 'education' ? 'is-active' : '' ?>">
                                        <i class="fas fa-graduation-cap"></i>
                                        <?= htmlspecialchars($profileTabLabels['education']) ?>
                                    </a>
                                    <a href="<?= htmlspecialchars($buildProfileTabUrl('portfolio')) ?>"
                                        class="profile-mobile-tab <?= $tab === 'portfolio' ? 'is-active' : '' ?>">
                                        <i class="fas fa-layer-group"></i>
                                        <?= htmlspecialchars($profileTabLabels['portfolio']) ?>
                                    </a>
                                    <a href="<?= htmlspecialchars($buildProfileTabUrl('certificates')) ?>"
                                        class="profile-mobile-tab <?= $tab === 'certificates' ? 'is-active' : '' ?>">
                                        <i class="fas fa-certificate"></i>
                                        <?= htmlspecialchars($profileTabLabels['certificates']) ?>
                                    </a>
                                    <a href="<?= htmlspecialchars($buildProfileTabUrl('activity')) ?>"
                                        class="profile-mobile-tab <?= $tab === 'activity' ? 'is-active' : '' ?>">
                                        <i class="fas fa-history"></i>
                                        <?= htmlspecialchars($profileTabLabels['activity']) ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <nav
                            class="profile-tabs-nav hidden sm:flex gap-4 overflow-x-auto whitespace-nowrap no-scrollbar px-4 sm:px-6 py-4">
                            <a href="<?= htmlspecialchars($buildProfileTabUrl('overview')) ?>"
                                class="shrink-0 <?= $tab === 'overview' ? 'tab-active' : 'tab-inactive' ?>">
                                <?= htmlspecialchars($profileTabLabels['overview']) ?>
                            </a>
                            <a href="<?= htmlspecialchars($buildProfileTabUrl('experience')) ?>"
                                class="shrink-0 <?= $tab === 'experience' ? 'tab-active' : 'tab-inactive' ?>">
                                <?= htmlspecialchars($profileTabLabels['experience']) ?>
                            </a>
                            <a href="<?= htmlspecialchars($buildProfileTabUrl('education')) ?>"
                                class="shrink-0 <?= $tab === 'education' ? 'tab-active' : 'tab-inactive' ?>">
                                <?= htmlspecialchars($profileTabLabels['education']) ?>
                            </a>
                            <a href="<?= htmlspecialchars($buildProfileTabUrl('portfolio')) ?>"
                                class="shrink-0 <?= $tab === 'portfolio' ? 'tab-active' : 'tab-inactive' ?>">
                                <?= htmlspecialchars($profileTabLabels['portfolio']) ?>
                            </a>
                            <a href="<?= htmlspecialchars($buildProfileTabUrl('certificates')) ?>"
                                class="shrink-0 <?= $tab === 'certificates' ? 'tab-active' : 'tab-inactive' ?>">
                                <?= htmlspecialchars($profileTabLabels['certificates']) ?>
                            </a>
                            <a href="<?= htmlspecialchars($buildProfileTabUrl('activity')) ?>"
                                class="shrink-0 <?= $tab === 'activity' ? 'tab-active' : 'tab-inactive' ?>">
                                <?= htmlspecialchars($profileTabLabels['activity']) ?>
                            </a>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="px-4 sm:px-6 py-6">
                        <?php if ($tab === 'overview'): ?>
                            <!-- Overview Tab Content -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i> <?= t('profile_about') ?>
                                    </h3>
                                    <p class="text-gray-600"><?= htmlspecialchars($user['bio']) ?></p>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                            <i class="fas fa-star text-yellow-400 mr-2"></i> <?= t('profile_skills') ?>
                                        </h3>
                                        <button onclick="openAddSkillModal()"
                                            class="text-indigo-600 hover:text-indigo-900 text-sm">
                                            <i class="fas fa-plus mr-1"></i> <?= t('profile_add') ?>
                                        </button>
                                    </div>
                                    <?php if (!empty($skillsTechnical) || !empty($skillsSoft)): ?>
                                        <?php if (!empty($skillsTechnical)): ?>
                                            <div class="mb-4">
                                                <div class="text-xs uppercase tracking-widest text-gray-400 mb-2">Technical</div>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php foreach ($skillsTechnical as $skill): ?>
                                                        <?php
                                                        $skillNameAttr = htmlspecialchars($skill['skill_name'], ENT_QUOTES);
                                                        $skillCategoryAttr = htmlspecialchars($skill['category'] ?? 'technical', ENT_QUOTES);
                                                        $skillVerified = !empty($skill['is_verified']);
                                                        ?>
                                                        <div
                                                            class="skill-tag gap-2 <?= $skillVerified ? 'skill-verified' : 'skill-unverified' ?>">
                                                            <span><?= htmlspecialchars($skill['skill_name']) ?></span>
                                                            <span class="text-xs text-gray-500">(<?= $skill['skill_level'] ?>%)</span>
                                                            <?php if ($skillVerified): ?>
                                                                <span class="badge-verified"><?= t('profile_verified') ?></span>
                                                            <?php else: ?>
                                                                <span class="badge-unverified"><?= t('profile_unverified') ?></span>
                                                            <?php endif; ?>
                                                            <button type="button"
                                                                class="ml-1 text-xs text-indigo-700 hover:text-indigo-900"
                                                                data-skill-action="edit" data-skill-id="<?= (int) $skill['id'] ?>"
                                                                data-skill-name="<?= $skillNameAttr ?>"
                                                                data-skill-category="<?= $skillCategoryAttr ?>">
                                                                <?= t('profile_edit') ?>
                                                            </button>
                                                            <button type="button"
                                                                class="text-xs <?= $skillVerified ? 'text-emerald-600 hover:text-emerald-800' : 'text-rose-600 hover:text-rose-800' ?>"
                                                                data-skill-action="verify"
                                                                data-skill-verified="<?= $skillVerified ? '1' : '0' ?>"
                                                                data-skill-id="<?= (int) $skill['id'] ?>"
                                                                data-skill-name="<?= $skillNameAttr ?>">
                                                                <?= $skillVerified ? t('profile_retry') : t('profile_check') ?>
                                                            </button>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <div class="mt-4 space-y-2">
                                                    <?php foreach ($skillsTechnical as $skill): ?>
                                                        <?php
                                                        $skillNameAttr = htmlspecialchars($skill['skill_name'], ENT_QUOTES);
                                                        $skillCategoryAttr = htmlspecialchars($skill['category'] ?? 'technical', ENT_QUOTES);
                                                        $skillVerified = !empty($skill['is_verified']);
                                                        ?>
                                                        <div
                                                            class="flex flex-wrap items-center justify-between gap-3 bg-gray-50 rounded-lg px-3 py-2 <?= $skillVerified ? 'skill-row-verified' : 'skill-row-unverified' ?>">
                                                            <div class="min-w-0">
                                                                <div
                                                                    class="text-sm font-medium truncate <?= $skillVerified ? 'skill-name-verified' : 'skill-name-unverified' ?>">
                                                                    <?= htmlspecialchars($skill['skill_name']) ?>
                                                                </div>
                                                                <div class="text-xs text-gray-500"><?= $skill['skill_level'] ?>%</div>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <button class="text-xs text-indigo-600 hover:text-indigo-900"
                                                                    data-skill-action="edit" data-skill-id="<?= (int) $skill['id'] ?>"
                                                                    data-skill-name="<?= $skillNameAttr ?>"
                                                                    data-skill-category="<?= $skillCategoryAttr ?>">
                                                                    <?= t('profile_edit') ?>
                                                                </button>
                                                                <button
                                                                    class="text-xs <?= $skillVerified ? 'text-emerald-600 hover:text-emerald-800' : 'text-rose-600 hover:text-rose-800' ?>"
                                                                    data-skill-action="verify"
                                                                    data-skill-verified="<?= $skillVerified ? '1' : '0' ?>"
                                                                    data-skill-id="<?= (int) $skill['id'] ?>"
                                                                    data-skill-name="<?= $skillNameAttr ?>">
                                                                    <?= $skillVerified ? t('profile_retry') : t('profile_check') ?>
                                                                </button>
                                                                <button class="text-xs text-red-500 hover:text-red-700"
                                                                    onclick="deleteSkill(<?= (int) $skill['id'] ?>)">
                                                                    <?= t('profile_delete') ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($skillsSoft)): ?>
                                            <div class="mb-4">
                                                <div class="text-xs uppercase tracking-widest text-gray-400 mb-2">Soft Skills</div>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php foreach ($skillsSoft as $skill): ?>
                                                        <?php
                                                        $skillNameAttr = htmlspecialchars($skill['skill_name'], ENT_QUOTES);
                                                        $skillCategoryAttr = htmlspecialchars($skill['category'] ?? 'soft', ENT_QUOTES);
                                                        $skillVerified = !empty($skill['is_verified']);
                                                        ?>
                                                        <div
                                                            class="skill-tag gap-2 <?= $skillVerified ? 'skill-verified' : 'skill-unverified' ?>">
                                                            <span><?= htmlspecialchars($skill['skill_name']) ?></span>
                                                            <span class="text-xs text-gray-500">(<?= $skill['skill_level'] ?>%)</span>
                                                            <?php if ($skillVerified): ?>
                                                                <span class="badge-verified"><?= t('profile_verified') ?></span>
                                                            <?php else: ?>
                                                                <span class="badge-unverified"><?= t('profile_unverified') ?></span>
                                                            <?php endif; ?>
                                                            <button type="button"
                                                                class="ml-1 text-xs text-indigo-700 hover:text-indigo-900"
                                                                data-skill-action="edit" data-skill-id="<?= (int) $skill['id'] ?>"
                                                                data-skill-name="<?= $skillNameAttr ?>"
                                                                data-skill-category="<?= $skillCategoryAttr ?>">
                                                                <?= t('profile_edit') ?>
                                                            </button>
                                                            <button type="button"
                                                                class="text-xs <?= $skillVerified ? 'text-emerald-600 hover:text-emerald-800' : 'text-rose-600 hover:text-rose-800' ?>"
                                                                data-skill-action="verify"
                                                                data-skill-verified="<?= $skillVerified ? '1' : '0' ?>"
                                                                data-skill-id="<?= (int) $skill['id'] ?>"
                                                                data-skill-name="<?= $skillNameAttr ?>">
                                                                <?= $skillVerified ? t('profile_retry') : t('profile_check') ?>
                                                            </button>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <div class="mt-4 space-y-2">
                                                    <?php foreach ($skillsSoft as $skill): ?>
                                                        <?php
                                                        $skillNameAttr = htmlspecialchars($skill['skill_name'], ENT_QUOTES);
                                                        $skillCategoryAttr = htmlspecialchars($skill['category'] ?? 'soft', ENT_QUOTES);
                                                        $skillVerified = !empty($skill['is_verified']);
                                                        ?>
                                                        <div
                                                            class="flex flex-wrap items-center justify-between gap-3 bg-gray-50 rounded-lg px-3 py-2 <?= $skillVerified ? 'skill-row-verified' : 'skill-row-unverified' ?>">
                                                            <div class="min-w-0">
                                                                <div
                                                                    class="text-sm font-medium truncate <?= $skillVerified ? 'skill-name-verified' : 'skill-name-unverified' ?>">
                                                                    <?= htmlspecialchars($skill['skill_name']) ?>
                                                                </div>
                                                                <div class="text-xs text-gray-500"><?= $skill['skill_level'] ?>%</div>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <button class="text-xs text-indigo-600 hover:text-indigo-900"
                                                                    data-skill-action="edit" data-skill-id="<?= (int) $skill['id'] ?>"
                                                                    data-skill-name="<?= $skillNameAttr ?>"
                                                                    data-skill-category="<?= $skillCategoryAttr ?>">
                                                                    <?= t('profile_edit') ?>
                                                                </button>
                                                                <button
                                                                    class="text-xs <?= $skillVerified ? 'text-emerald-600 hover:text-emerald-800' : 'text-rose-600 hover:text-rose-800' ?>"
                                                                    data-skill-action="verify"
                                                                    data-skill-verified="<?= $skillVerified ? '1' : '0' ?>"
                                                                    data-skill-id="<?= (int) $skill['id'] ?>"
                                                                    data-skill-name="<?= $skillNameAttr ?>">
                                                                    <?= $skillVerified ? t('profile_retry') : t('profile_check') ?>
                                                                </button>
                                                                <button class="text-xs text-red-500 hover:text-red-700"
                                                                    onclick="deleteSkill(<?= (int) $skill['id'] ?>)">
                                                                    <?= t('profile_delete') ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="text-sm text-gray-500"><?= t('profile_skills_empty') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-chart-line mr-2"></i> <?= t('profile_stats') ?>
                                    </h3>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                                            <p class="text-3xl font-bold text-indigo-600">
                                                <?= uiValue($pointsData['completed_courses']) ?>
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1"><?= t('profile_completed_courses') ?></p>
                                        </div>
                                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                                            <p class="text-3xl font-bold text-indigo-600">
                                                <?= uiValue($pointsData['certificates']) ?>
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1"><?= t('profile_certs') ?></p>
                                        </div>
                                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                                            <p class="text-3xl font-bold text-indigo-600">
                                                <?= uiValue($pointsData['skills_count']) ?>
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1"><?= t('profile_skills') ?></p>
                                        </div>
                                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                                            <p class="text-3xl font-bold text-indigo-600">
                                                <?= uiValue($pointsData['points']) ?>
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1"><?= t('profile_points') ?></p>
                                        </div>
                                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                                            <p class="text-3xl font-bold text-indigo-600">
                                                <?= $pointsData['total_experience'] ?>
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1"><?= t('profile_experience_work') ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $skillCount = count($user['skills'] ?? []);
                                $expCount = count($user['experience'] ?? []);
                                $eduCount = count($user['education'] ?? []);
                                $portCount = count($user['portfolio'] ?? []);
                                $certCount = count($user['certificates'] ?? []);
                                $appCount = count($user['applications'] ?? []);
                                $skillPct = min(100, $skillCount * 20);
                                $portPct = min(100, $portCount * 25);
                                $expPct = min(100, $expCount * 20);
                                $topSkills = array_slice($user['skills'] ?? [], 0, 3);
                                $verifiedSkillCount = 0;
                                $unverifiedSkillCount = 0;
                                foreach ($user['skills'] ?? [] as $skill) {
                                    if (!empty($skill['is_verified'])) {
                                        $verifiedSkillCount++;
                                    } else {
                                        $unverifiedSkillCount++;
                                    }
                                }
                                $skillTotal = max(1, $skillCount);
                                $verifiedPct = (int) round(($verifiedSkillCount / $skillTotal) * 100);
                                $unverifiedPct = max(0, 100 - $verifiedPct);
                                $profileParts = [
                                    min(100, $skillCount * 20),
                                    min(100, $expCount * 25),
                                    min(100, $eduCount * 20),
                                    min(100, $portCount * 20),
                                    min(100, $certCount * 15),
                                ];
                                $profileScore = (int) round(array_sum($profileParts) / count($profileParts));
                                $profileScore = min(100, $profileScore);
                                $activityDays = 14;
                                $activitySeries = [];
                                for ($i = $activityDays - 1; $i >= 0; $i--) {
                                    $date = date('Y-m-d', strtotime("-{$i} days"));
                                    $activitySeries[] = (int) ($heatmapMap[$date] ?? 0);
                                }
                                $activityMax = max($activitySeries);
                                if ($activityMax < 1) {
                                    $activityMax = 1;
                                }
                                ?>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-wave-square mr-2"></i> <?= t('profile_trackers') ?>
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= t('profile_growth_tracker') ?>
                                                </div>
                                                <div class="text-xs text-gray-500"><?= t('profile_updating') ?></div>
                                            </div>
                                            <div class="mt-4 space-y-3">
                                                <div>
                                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                                        <span><?= t('profile_skills') ?></span><span><?= $skillPct ?>%</span>
                                                    </div>
                                                    <div class="progress-bar">
                                                        <div class="progress-fill" style="width: <?= $skillPct ?>%"></div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                                        <span><?= t('profile_tab_portfolio') ?></span><span><?= $portPct ?>%</span>
                                                    </div>
                                                    <div class="progress-bar">
                                                        <div class="progress-fill" style="width: <?= $portPct ?>%"></div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                                        <span><?= t('profile_exp') ?></span><span><?= $expPct ?>%</span>
                                                    </div>
                                                    <div class="progress-bar">
                                                        <div class="progress-fill" style="width: <?= $expPct ?>%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= t('profile_skills_chart') ?>
                                                </div>
                                                <div class="text-xs text-gray-500"><?= count($topSkills) ?>
                                                    <?= t('profile_of') ?>     <?= $skillCount ?>
                                                </div>
                                            </div>
                                            <div class="mt-4 space-y-3">
                                                <?php if (!empty($topSkills)): ?>
                                                    <?php foreach ($topSkills as $skill): ?>
                                                        <div>
                                                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                                                <span><?= htmlspecialchars($skill['skill_name']) ?></span>
                                                                <span><?= (int) $skill['skill_level'] ?>%</span>
                                                            </div>
                                                            <div class="progress-bar">
                                                                <div class="progress-fill"
                                                                    style="width: <?= (int) $skill['skill_level'] ?>%"></div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="text-sm text-gray-500"><?= t('profile_skills_chart_empty') ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="text-sm font-medium text-gray-900"><?= t('profile_bigdata') ?>
                                                </div>
                                                <div class="text-xs text-gray-500"><?= t('profile_snapshot') ?></div>
                                            </div>
                                            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                                <div class="bg-white rounded-lg p-3">
                                                    <div class="text-gray-500 text-xs"><?= t('profile_courses') ?></div>
                                                    <div class="text-lg font-semibold text-gray-900">
                                                        <?= uiValue($pointsData['completed_courses']) ?>
                                                    </div>
                                                </div>
                                                <div class="bg-white rounded-lg p-3">
                                                    <div class="text-gray-500 text-xs"><?= t('profile_certs') ?></div>
                                                    <div class="text-lg font-semibold text-gray-900"><?= $certCount ?></div>
                                                </div>
                                                <div class="bg-white rounded-lg p-3">
                                                    <div class="text-gray-500 text-xs"><?= t('profile_apps') ?></div>
                                                    <div class="text-lg font-semibold text-gray-900"><?= $appCount ?></div>
                                                </div>
                                                <div class="bg-white rounded-lg p-3">
                                                    <div class="text-gray-500 text-xs"><?= t('profile_education') ?></div>
                                                    <div class="text-lg font-semibold text-gray-900"><?= $eduCount ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-chart-pie mr-2 text-indigo-600"></i>
                                        <?= t('profile_visuals', 'Визуальные метрики') ?>
                                    </h3>
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                        <div class="viz-card">
                                            <div class="flex items-center justify-between">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    <?= t('profile_completion', 'Заполнение профиля') ?>
                                                </div>
                                                <span class="viz-chip"><?= $profileScore ?>%</span>
                                            </div>
                                            <div class="mt-4 flex flex-col sm:flex-row items-center gap-4">
                                                <div class="viz-donut" style="--value: <?= $profileScore ?>;">
                                                    <div class="viz-donut-center">
                                                        <div class="text-2xl font-bold text-gray-900"><?= $profileScore ?>%
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            <?= t('profile_done', 'Р“РѕС‚РѕРІРѕ') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="w-full space-y-2 text-xs text-gray-500">
                                                    <div class="flex items-center justify-between">
                                                        <span><?= t('profile_skills') ?></span>
                                                        <span class="font-semibold text-gray-900"><?= $skillCount ?></span>
                                                    </div>
                                                    <div class="flex items-center justify-between">
                                                        <span><?= t('profile_exp') ?></span>
                                                        <span class="font-semibold text-gray-900"><?= $expCount ?></span>
                                                    </div>
                                                    <div class="flex items-center justify-between">
                                                        <span><?= t('profile_education') ?></span>
                                                        <span class="font-semibold text-gray-900"><?= $eduCount ?></span>
                                                    </div>
                                                    <div class="flex items-center justify-between">
                                                        <span><?= t('profile_tab_portfolio') ?></span>
                                                        <span class="font-semibold text-gray-900"><?= $portCount ?></span>
                                                    </div>
                                                    <div class="flex items-center justify-between">
                                                        <span><?= t('profile_certs') ?></span>
                                                        <span class="font-semibold text-gray-900"><?= $certCount ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="viz-card">
                                            <div class="flex items-center justify-between">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    <?= t('profile_skill_verification', 'Верификация навыков') ?>
                                                </div>
                                                <span
                                                    class="text-xs text-gray-500"><?= $verifiedSkillCount ?>/<?= $skillCount ?></span>
                                            </div>
                                            <div class="mt-4">
                                                <div class="viz-split-bar">
                                                    <span class="viz-verified" style="width: <?= $verifiedPct ?>%"></span>
                                                    <span class="viz-unverified"
                                                        style="width: <?= $unverifiedPct ?>%"></span>
                                                </div>
                                                <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-gray-600">
                                                    <div class="flex items-center gap-2">
                                                        <span class="viz-dot viz-dot-verified"></span>
                                                        <span><?= t('profile_verified') ?></span>
                                                        <span
                                                            class="font-semibold text-gray-900"><?= $verifiedSkillCount ?></span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="viz-dot viz-dot-unverified"></span>
                                                        <span><?= t('profile_unverified') ?></span>
                                                        <span
                                                            class="font-semibold text-gray-900"><?= $unverifiedSkillCount ?></span>
                                                    </div>
                                                </div>
                                                <div class="mt-3 text-xs text-gray-500">
                                                    <?= t('profile_skill_verification_hint', 'Проверка навыков идёт через тесты и проверки') ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="viz-card">
                                            <div class="flex items-center justify-between">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    <?= t('profile_activity_pulse', 'Пульс активности') ?>
                                                </div>
                                                <span class="text-xs text-gray-500">
                                                    <?= t('profile_last_14_days', 'Последние 14 дней') ?>
                                                </span>
                                            </div>
                                            <div class="mt-4 viz-bars">
                                                <?php foreach ($activitySeries as $value): ?>
                                                    <?php
                                                    $height = (int) round(($value / $activityMax) * 100);
                                                    $height = max(10, $height);
                                                    ?>
                                                    <div class="viz-bar-track">
                                                        <div class="viz-bar-fill" style="height: <?= $height ?>%"></div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="mt-3 text-xs text-gray-500">
                                                <?= t('profile_activity_hint', 'Чем выше столбик, тем больше действий') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-history mr-2"></i> <?= t('profile_tab_activity') ?>
                                    </h3>
                                    <div class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
                                        <div class="heatmap">
                                            <?php for ($i = 0; $i < $heatmapDays; $i++): ?>
                                                <?php
                                                $date = date('Y-m-d', $heatmapStart + ($i * 86400));
                                                $count = (int) ($heatmapMap[$date] ?? 0);
                                                $level = 0;
                                                if ($count >= 1 && $count < 2)
                                                    $level = 1;
                                                elseif ($count >= 2 && $count < 4)
                                                    $level = 2;
                                                elseif ($count >= 4 && $count < 7)
                                                    $level = 3;
                                                elseif ($count >= 7)
                                                    $level = 4;
                                                ?>
                                                <?php $dateTitle = date('d:m:y', $heatmapStart + ($i * 86400)); ?>
                                                <div class="heat-cell heat-<?= $level ?>" title="<?= $dateTitle ?>"></div>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="mt-2 text-xs text-gray-500"><?= t('profile_last_12_weeks') ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($tab === 'experience'): ?>
                            <!-- Experience Tab Content -->
                            <div class="section-card">
                                <div class="section-head">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <i class="fas fa-briefcase mr-2 text-indigo-600"></i>
                                        <?= t('profile_tab_experience') ?>
                                    </h3>
                                    <button onclick="openAddExperienceModal()" class="btn-ghost">
                                        <i class="fas fa-plus mr-1"></i> <?= t('profile_add') ?>
                                    </button>
                                </div>
                                <div class="section-body">
                                    <?php if (!empty($user['experience'])): ?>
                                        <div class="space-y-4">
                                            <?php foreach ($user['experience'] as $exp): ?>
                                                <div class="timeline-item">
                                                    <div class="timeline-dot"></div>
                                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                                        <div>
                                                            <div class="flex items-center gap-2 flex-wrap">
                                                                <h4 class="font-semibold text-gray-900">
                                                                    <?= htmlspecialchars($exp['position']) ?>
                                                                </h4>
                                                                <span class="pill">
                                                                    <i class="far fa-calendar"></i>
                                                                    <?= htmlspecialchars($exp['start_date']) ?>
                                                                    <?php if ($exp['end_date']): ?> -
                                                                        <?= htmlspecialchars($exp['end_date']) ?>             <?php else: ?> -
                                                                        <?= t('profile_now') ?>             <?php endif; ?>
                                                                </span>
                                                            </div>
                                                            <p class="text-indigo-700 mt-1 font-medium">
                                                                <?= htmlspecialchars($exp['company']) ?>
                                                            </p>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <button class="action-icon"
                                                                onclick="openEditExperienceModal(<?= (int) $exp['id'] ?>, '<?= htmlspecialchars($exp['position']) ?>', '<?= htmlspecialchars($exp['company']) ?>', '<?= htmlspecialchars($exp['start_date']) ?>', '<?= htmlspecialchars($exp['end_date']) ?>', '<?= htmlspecialchars($exp['description']) ?>')"
                                                                aria-label="<?= t('profile_edit') ?>">
                                                                <i class="fas fa-pen"></i>
                                                            </button>
                                                            <button class="action-icon"
                                                                onclick="deleteExperience(<?= (int) $exp['id'] ?>)"
                                                                aria-label="<?= t('profile_delete') ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <?php if ($exp['description']): ?>
                                                        <p class="text-gray-600 mt-2"><?= htmlspecialchars($exp['description']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-10">
                                            <i class="fas fa-briefcase text-4xl text-indigo-100 mb-3"></i>
                                            <p class="text-gray-500"><?= t('profile_experience_empty') ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php elseif ($tab === 'education'): ?>
                            <!-- Education Tab Content -->
                            <div class="section-card">
                                <div class="section-head">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <i class="fas fa-graduation-cap mr-2 text-indigo-600"></i>
                                        <?= t('profile_tab_education') ?>
                                    </h3>
                                    <button onclick="openAddEducationModal()" class="btn-ghost">
                                        <i class="fas fa-plus mr-1"></i> <?= t('profile_add') ?>
                                    </button>
                                </div>
                                <div class="section-body">
                                    <?php if (!empty($user['education'])): ?>
                                        <div class="space-y-4">
                                            <?php foreach ($user['education'] as $edu): ?>
                                                <div class="timeline-item">
                                                    <div class="timeline-dot"></div>
                                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                                        <div>
                                                            <div class="flex items-center gap-2 flex-wrap">
                                                                <h4 class="font-semibold text-gray-900">
                                                                    <?= htmlspecialchars($edu['degree']) ?>
                                                                </h4>
                                                                <span class="pill">
                                                                    <i class="far fa-calendar"></i>
                                                                    <?= htmlspecialchars($edu['start_date']) ?>
                                                                    <?php if ($edu['end_date']): ?> -
                                                                        <?= htmlspecialchars($edu['end_date']) ?>             <?php else: ?> -
                                                                        <?= t('profile_now') ?>             <?php endif; ?>
                                                                </span>
                                                            </div>
                                                            <p class="text-indigo-700 mt-1 font-medium">
                                                                <?= htmlspecialchars($edu['institution']) ?>
                                                            </p>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <button class="action-icon"
                                                                onclick="openEditEducationModal(<?= (int) $edu['id'] ?>, '<?= htmlspecialchars($edu['degree']) ?>', '<?= htmlspecialchars($edu['institution']) ?>', '<?= htmlspecialchars($edu['start_date']) ?>', '<?= htmlspecialchars($edu['end_date']) ?>', '<?= htmlspecialchars($edu['description']) ?>')"
                                                                aria-label="<?= t('profile_edit') ?>">
                                                                <i class="fas fa-pen"></i>
                                                            </button>
                                                            <button class="action-icon"
                                                                onclick="deleteEducation(<?= (int) $edu['id'] ?>)"
                                                                aria-label="<?= t('profile_delete') ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <?php if ($edu['description']): ?>
                                                        <p class="text-gray-600 mt-2"><?= htmlspecialchars($edu['description']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-10">
                                            <i class="fas fa-graduation-cap text-4xl text-indigo-100 mb-3"></i>
                                            <p class="text-gray-500"><?= t('profile_education_empty') ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php elseif ($tab === 'portfolio'): ?>
                            <!-- Portfolio Tab Content -->
                            <div class="section-card">
                                <div class="section-head">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <i class="fas fa-folder-open mr-2 text-indigo-600"></i>
                                        <?= t('profile_tab_portfolio') ?>
                                    </h3>
                                    <button class="btn-ghost" onclick="openAddPortfolioModal()">
                                        <i class="fas fa-plus mr-1"></i> <?= t('profile_add_project') ?>
                                    </button>
                                </div>
                                <div class="section-body">
                                    <?php if (!empty($user['portfolio'])): ?>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <?php foreach ($user['portfolio'] as $project): ?>
                                                <div class="portfolio-card">
                                                    <img src="<?= htmlspecialchars((string) (!empty($project['image_url']) ? $project['image_url'] : 'https://placehold.co/800x420/e2e8f0/64748b?text=Project')) ?>"
                                                        alt="<?= t('profile_portfolio_project') ?>" />
                                                    <div class="p-4">
                                                        <div
                                                            class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                                            <div class="min-w-0">
                                                                <h4 class="font-semibold text-gray-900">
                                                                    <?= htmlspecialchars($project['title']) ?>
                                                                </h4>
                                                                <p class="text-sm text-gray-500 mt-1">
                                                                    <?= htmlspecialchars($project['category']) ?>
                                                                </p>
                                                            </div>
                                                            <?php if (!empty($project['category'])): ?>
                                                                <span class="pill"><?= htmlspecialchars($project['category']) ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if (!empty($project['github_url'])): ?>
                                                            <a href="<?= htmlspecialchars((string) $project['github_url']) ?>"
                                                                target="_blank" rel="noopener noreferrer"
                                                                class="mt-2 inline-flex items-center gap-2 text-sm text-indigo-700 hover:text-indigo-900 break-all">
                                                                <i
                                                                    class="fab fa-github"></i><span><?= htmlspecialchars((string) $project['github_url']) ?></span>
                                                            </a>
                                                        <?php endif; ?>
                                                        <div class="mt-4 flex flex-wrap items-center gap-2">
                                                            <button class="btn-outline text-sm flex-1 sm:flex-none"
                                                                onclick="openEditPortfolioModal(<?= (int) $project['id'] ?>, '<?= htmlspecialchars($project['title']) ?>', '<?= htmlspecialchars($project['category']) ?>', '<?= htmlspecialchars($project['image_url']) ?>', '<?= htmlspecialchars($project['github_url']) ?>')">
                                                                <i class="fas fa-pen mr-1"></i> <?= t('profile_edit') ?>
                                                            </button>
                                                            <button class="action-icon"
                                                                onclick="deletePortfolio(<?= (int) $project['id'] ?>)"
                                                                aria-label="<?= t('profile_delete') ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-10">
                                            <i class="fas fa-folder-open text-4xl text-indigo-100 mb-3"></i>
                                            <p class="text-gray-500"><?= t('profile_projects_empty') ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php elseif ($tab === 'certificates'): ?>
                            <!-- Certificates Tab Content -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-certificate mr-2"></i> <?= t('profile_tab_certificates') ?>
                                </h3>
                                <?php if (!empty($user['certificates'])): ?>
                                    <div class="space-y-4">
                                        <?php foreach ($user['certificates'] as $cert): ?>
                                            <div class="card border border-gray-200">
                                                <div
                                                    class="px-4 py-3 bg-gradient-to-r from-indigo-50 to-emerald-50 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                                    <div
                                                        class="min-w-0 flex items-center gap-2 text-sm text-indigo-700 font-semibold break-words">
                                                        <i class="fas fa-certificate text-yellow-500"></i>
                                                        <?= htmlspecialchars(normalizeMojibakeText((string) ($cert['certificate_name'] ?? ''))) ?>
                                                    </div>
                                                    <span
                                                        class="text-xs px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-500 self-start sm:self-auto">
                                                        <?php $issueDate = (string) ($cert['issue_date'] ?? ''); ?>
                                                        <?= htmlspecialchars($issueDate !== '' ? formatDate($issueDate) : '') ?>
                                                    </span>
                                                </div>
                                                <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                                    <div class="min-w-0 break-words">
                                                        <p class="text-sm text-gray-600 mb-1">
                                                            <i class="fas fa-user mr-1"></i> <?= t('cert_recipient') ?>:
                                                            <?= htmlspecialchars(normalizeMojibakeText((string) ($user['name'] ?? ''))) ?>
                                                        </p>
                                                        <p class="text-gray-900 font-semibold">
                                                            <?= htmlspecialchars(normalizeMojibakeText((string) ($cert['course_title'] ?? ''))) ?>
                                                        </p>
                                                        <p class="text-sm text-gray-500 mt-1">
                                                            <i class="fas fa-award mr-1"></i>
                                                            <?= htmlspecialchars(normalizeMojibakeText((string) ($cert['issuer'] ?? ''))) ?>
                                                        </p>
                                                    </div>
                                                    <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                                                        <a class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-50 text-center flex-1 sm:flex-none"
                                                            href="?action=certificate&id=<?= (int) $cert['id'] ?>">
                                                            <?= t('profile_view') ?>
                                                        </a>
                                                        <a class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700 text-center flex-1 sm:flex-none"
                                                            href="?action=certificate&id=<?= (int) $cert['id'] ?>&download=1">
                                                            <?= t('cert_download_pdf') ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-8">
                                        <i class="fas fa-certificate text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-500"><?= t('profile_certs_empty') ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($tab === 'activity'): ?>
                            <!-- Activity Tab Content -->
                            <div>
                                <?php
                                $heatmapSets = [
                                    'all' => [
                                        'title' => t('profile_activity_all', 'All activity'),
                                        'subtitle' => t('profile_activity_year', 'Last 12 months'),
                                        'data' => $heatmapAll,
                                    ],
                                    'study' => [
                                        'title' => t('profile_activity_study', 'Study'),
                                        'subtitle' => t('profile_activity_year', 'Last 12 months'),
                                        'data' => $heatmapStudy,
                                    ],
                                    'contest' => [
                                        'title' => t('profile_activity_contest', 'Contests'),
                                        'subtitle' => t('profile_activity_year', 'Last 12 months'),
                                        'data' => $heatmapContest,
                                    ],
                                ];
                                ?>
                                <div class="space-y-6">
                                    <?php foreach ($heatmapSets as $mapKey => $set): ?>
                                        <?php $mapData = $set['data']['map'] ?? []; ?>
                                        <div class="gh-heatmap-card gh-heatmap-block"
                                            data-heatmap="<?= htmlspecialchars($mapKey) ?>">
                                            <div class="gh-heatmap-header">
                                                <div>
                                                    <div class="gh-heatmap-title"><?= htmlspecialchars($set['title']) ?></div>
                                                    <div class="gh-heatmap-subtitle"><?= htmlspecialchars($set['subtitle']) ?>
                                                    </div>
                                                </div>
                                                <div class="gh-heatmap-legend">
                                                    <span><?= t('profile_activity_legend_less', 'Less') ?></span>
                                                    <div class="legend-cells">
                                                        <span class="gh-heat-cell level-0" aria-hidden="true"></span>
                                                        <span class="gh-heat-cell level-1" aria-hidden="true"></span>
                                                        <span class="gh-heat-cell level-2" aria-hidden="true"></span>
                                                        <span class="gh-heat-cell level-3" aria-hidden="true"></span>
                                                        <span class="gh-heat-cell level-4" aria-hidden="true"></span>
                                                    </div>
                                                    <span><?= t('profile_activity_legend_more', 'More') ?></span>
                                                </div>
                                            </div>
                                            <div class="gh-heatmap-wrapper">
                                                <div class="gh-heatmap-months">
                                                    <?php for ($w = 0; $w < $gridWeeks; $w++): ?>
                                                        <div class="gh-heatmap-month">
                                                            <?= htmlspecialchars($profileMonthColumns[$w] ?? '') ?></div>
                                                    <?php endfor; ?>
                                                </div>
                                                <div class="gh-heatmap-grid">
                                                    <div class="gh-heatmap-days">
                                                        <?php for ($d = 0; $d < 7; $d++): ?>
                                                            <?php
                                                            $label = '';
                                                            if (in_array($d, [1, 3, 5], true)) {
                                                                $label = $profileDayLabels[$d] ?? '';
                                                            }
                                                            ?>
                                                            <div><?= htmlspecialchars($label) ?></div>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <div class="gh-heatmap-weeks">
                                                        <?php for ($w = 0; $w < $gridWeeks; $w++): ?>
                                                            <div class="gh-heatmap-week">
                                                                <?php for ($d = 0; $d < 7; $d++): ?>
                                                                    <?php
                                                                    $dayIndex = ($w * 7) + $d;
                                                                    $dayTs = $gridStartTs + ($dayIndex * 86400);
                                                                    $dateKey = date('Y-m-d', $dayTs);
                                                                    $inRange = $dayTs >= $yearStartTs && $dayTs <= $gridEndTs;
                                                                    $count = $inRange ? (int) ($mapData[$dateKey]['count'] ?? 0) : 0;
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
                                                                    $title = $inRange ? $dateKey . ' · ' . $count : '';
                                                                    ?>
                                                                    <button type="button"
                                                                        class="gh-heat-cell level-<?= $level ?><?= $inRange ? '' : ' is-outside' ?>"
                                                                        data-date="<?= htmlspecialchars($dateKey) ?>"
                                                                        data-count="<?= $count ?>" <?= $inRange ? '' : 'tabindex="-1" aria-hidden="true"' ?>
                                                                        title="<?= htmlspecialchars($title) ?>"></button>
                                                                <?php endfor; ?>
                                                            </div>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gh-heatmap-detail">
                                                <div class="gh-heatmap-detail-title" data-detail-title>
                                                    <?= t('profile_activity_pick_day', 'Select a day to see details') ?>
                                                </div>
                                                <div class="gh-heatmap-detail-list" data-detail-list></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Skill Modal -->
    <div id="addSkillModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-star text-indigo-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                <?= t('profile_add_skill') ?>
                            </h3>
                            <div class="mt-4">
                                <form id="addSkillForm" onsubmit="event.preventDefault(); addSkill()">
                                    <div class="mb-4">
                                        <label for="skill-name"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_skill') ?></label>
                                        <select id="skill-name" class="w-full input-field" required>
                                            <optgroup label="Technical">
                                                <?php foreach ($technicalSkillOptions as $option): ?>
                                                    <option value="<?= htmlspecialchars($option) ?>"
                                                        data-skill-category="technical">
                                                        <?= htmlspecialchars($option) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                            <optgroup label="Soft Skills">
                                                <?php foreach ($softSkillOptions as $option): ?>
                                                    <option value="<?= htmlspecialchars($option) ?>"
                                                        data-skill-category="soft">
                                                        <?= htmlspecialchars($option) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Выбор только из списка.
                                        </p>
                                    </div>
                                    <div class="mb-4">
                                        <label for="skill-category"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_skill_category') ?></label>
                                        <select id="skill-category" class="w-full input-field">
                                            <option value="technical"><?= t('profile_skill_cat_technical') ?></option>
                                            <option value="soft"><?= t('profile_skill_cat_soft') ?></option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="addSkill()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <?= t('profile_add') ?>
                    </button>
                    <button type="button" onclick="closeAddSkillModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        <?= t('profile_cancel') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Skill Modal -->
    <div id="editSkillModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-pen text-indigo-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                <?= t('profile_edit_skill') ?>
                            </h3>
                            <div class="mt-4">
                                <form id="editSkillForm" onsubmit="event.preventDefault(); updateSkill()">
                                    <input type="hidden" id="edit-skill-id" />
                                    <div class="mb-4">
                                        <label for="edit-skill-name"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_skill') ?></label>
                                        <select id="edit-skill-name" class="w-full input-field" required>
                                            <optgroup label="Technical">
                                                <?php foreach ($technicalSkillOptions as $option): ?>
                                                    <option value="<?= htmlspecialchars($option) ?>"
                                                        data-skill-category="technical">
                                                        <?= htmlspecialchars($option) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                            <optgroup label="Soft Skills">
                                                <?php foreach ($softSkillOptions as $option): ?>
                                                    <option value="<?= htmlspecialchars($option) ?>"
                                                        data-skill-category="soft">
                                                        <?= htmlspecialchars($option) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Выбор только из списка.
                                        </p>
                                    </div>
                                    <div class="mb-4">
                                        <label for="edit-skill-category"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_skill_category') ?></label>
                                        <select id="edit-skill-category" class="w-full input-field">
                                            <option value="technical"><?= t('profile_skill_cat_technical') ?></option>
                                            <option value="soft"><?= t('profile_skill_cat_soft') ?></option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="updateSkill()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <?= t('profile_save') ?>
                    </button>
                    <button type="button" onclick="closeEditSkillModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        <?= t('profile_cancel') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Skill Quiz Modal -->
    <div id="skillQuizModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeSkillQuizModal()"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900"><?= t('profile_skill_check') ?>: <span
                                id="skill-quiz-title"></span></h3>
                    </div>
                    <button type="button" onclick="closeSkillQuizModal()" class="text-slate-400 hover:text-slate-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <div id="skill-quiz-body" class="space-y-4"></div>
                </div>
                <div
                    class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button type="button" onclick="closeSkillQuizModal()"
                        class="w-full sm:w-auto px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-100">
                        <?= t('profile_cancel') ?>
                    </button>
                    <button type="button" id="skill-quiz-submit" onclick="submitSkillQuiz()"
                        class="w-full sm:w-auto px-4 py-2 rounded-lg bg-emerald-600 text-sm font-medium text-white hover:bg-emerald-700">
                        <?= t('profile_check') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-user-edit text-indigo-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                <?= t('profile_edit_profile') ?>
                            </h3>
                            <div class="mt-4">
                                <form id="editProfileForm" onsubmit="event.preventDefault(); updateProfile()">
                                    <div class="mb-4">
                                        <label for="edit-name"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_full_name') ?></label>
                                        <input id="edit-name" type="text" value="<?= htmlspecialchars($user['name']) ?>"
                                            class="w-full input-field" required />
                                    </div>
                                    <div class="mb-4">
                                        <label for="edit-avatar"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_avatar_field') ?></label>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
                                            <input id="edit-avatar" type="text"
                                                value="<?= htmlspecialchars($user['avatar']) ?>"
                                                class="w-full input-field sm:col-span-2"
                                                placeholder="<?= t('profile_avatar_placeholder') ?>">
                                            <div class="flex items-center gap-2">
                                                <input id="edit-avatar-file" type="file" accept="image/*"
                                                    class="w-full input-field">
                                                <button type="button" class="btn-ghost"
                                                    onclick="uploadImageToInput('edit-avatar-file','edit-avatar')">
                                                    <i class="fas fa-upload mr-1"></i> <?= t('profile_upload') ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="edit-title"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_position') ?></label>
                                        <input id="edit-title" type="text"
                                            value="<?= htmlspecialchars($user['title']) ?>" class="w-full input-field"
                                            required />
                                    </div>
                                    <div class="mb-4">
                                        <label for="edit-location"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_location') ?></label>
                                        <input id="edit-location" type="text"
                                            value="<?= htmlspecialchars($user['location']) ?>"
                                            class="w-full input-field" required />
                                    </div>
                                    <div class="mb-4">
                                        <label for="edit-country"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('label_country', 'Страна проживания') ?></label>
                                        <input type="hidden" id="edit-country"
                                            value="<?= htmlspecialchars($currentCountryCode) ?>">
                                        <input type="hidden" id="edit-country-name"
                                            value="<?= htmlspecialchars($currentCountryName) ?>">
                                        <div class="country-picker">
                                            <button type="button" id="edit-country-button"
                                                class="input-field country-picker-toggle" aria-haspopup="listbox"
                                                aria-expanded="false">
                                                <span class="country-picker-value">
                                                    <img id="edit-country-button-flag" class="country-flag-icon"
                                                        src="<?= htmlspecialchars($currentCountryCode !== '' ? 'https://flagcdn.com/w20/' . strtolower($currentCountryCode) . '.png' : 'https://flagcdn.com/w20/un.png') ?>"
                                                        alt="" loading="lazy">
                                                    <span id="edit-country-button-label" class="country-picker-label">
                                                        <?= htmlspecialchars($currentCountryName !== '' ? $currentCountryName : t('placeholder_country', 'Выберите страну')) ?>
                                                    </span>
                                                </span>
                                                <i class="fas fa-chevron-down text-gray-400"></i>
                                            </button>
                                            <div id="edit-country-menu" class="country-picker-menu hidden"
                                                role="listbox">
                                                <?php foreach ($countryOptions as $code => $countryName): ?>
                                                    <?php $code = strtoupper(trim((string) $code)); ?>
                                                    <?php $flagSrc = 'https://flagcdn.com/w20/' . strtolower($code) . '.png'; ?>
                                                    <button type="button" class="country-picker-option"
                                                        data-country-code="<?= htmlspecialchars($code) ?>"
                                                        data-country-name="<?= htmlspecialchars($countryName) ?>"
                                                        data-country-flag-src="<?= htmlspecialchars($flagSrc) ?>">
                                                        <img class="country-picker-option-flag"
                                                            src="<?= htmlspecialchars($flagSrc) ?>" alt="" loading="lazy">
                                                        <span><?= htmlspecialchars($countryName) ?></span>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="edit-bio"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_about') ?></label>
                                        <textarea id="edit-bio" rows="3"
                                            class="w-full input-field"><?= htmlspecialchars($user['bio']) ?></textarea>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="mb-4 sm:mb-0">
                                            <label for="edit-linkedin"
                                                class="block text-sm font-medium text-gray-700 mb-1">LinkedIn</label>
                                            <input id="edit-linkedin" type="url"
                                                value="<?= htmlspecialchars($user['social_linkedin'] ?? '') ?>"
                                                class="w-full input-field"
                                                placeholder="https://linkedin.com/in/username">
                                        </div>
                                        <div class="mb-4 sm:mb-0">
                                            <label for="edit-github"
                                                class="block text-sm font-medium text-gray-700 mb-1">GitHub</label>
                                            <input id="edit-github" type="url"
                                                value="<?= htmlspecialchars($user['social_github'] ?? '') ?>"
                                                class="w-full input-field" placeholder="https://github.com/username">
                                        </div>
                                        <div class="mb-4 sm:mb-0">
                                            <label for="edit-telegram"
                                                class="block text-sm font-medium text-gray-700 mb-1">Telegram</label>
                                            <input id="edit-telegram" type="text"
                                                value="<?= htmlspecialchars($user['social_telegram'] ?? '') ?>"
                                                class="w-full input-field"
                                                placeholder="@username https://t.me/username">
                                        </div>
                                        <div class="mb-4 sm:mb-0">
                                            <label for="edit-website"
                                                class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_website', 'Website') ?></label>
                                            <input id="edit-website" type="url"
                                                value="<?= htmlspecialchars($user['social_website'] ?? '') ?>"
                                                class="w-full input-field" placeholder="https://example.com">
                                        </div>
                                    </div>
                                    <div class="mt-6 border-t border-slate-200 pt-4">
                                        <div class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                                            <i class="fas fa-key text-indigo-500"></i> <?= t('label_password') ?>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-1"><?= t('profile_password_hint') ?></p>
                                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <div>
                                                <label for="current-password"
                                                    class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_current_password') ?></label>
                                                <div class="relative">
                                                    <input id="current-password" type="password"
                                                        autocomplete="current-password" class="w-full input-field pr-10"
                                                        required>
                                                    <button type="button"
                                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                                                        onclick="togglePasswordVisibility('current-password', 'current-password-icon')"
                                                        aria-label="<?= t('profile_toggle_password', 'Toggle password visibility') ?>">
                                                        <i class="fas fa-eye" id="current-password-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="new-password"
                                                    class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_new_password') ?></label>
                                                <div class="relative">
                                                    <input id="new-password" type="password" autocomplete="new-password"
                                                        minlength="8" class="w-full input-field pr-10" required>
                                                    <button type="button"
                                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                                                        onclick="togglePasswordVisibility('new-password', 'new-password-icon')"
                                                        aria-label="<?= t('profile_toggle_password', 'Toggle password visibility') ?>">
                                                        <i class="fas fa-eye" id="new-password-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="confirm-password"
                                                    class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_confirm_password') ?></label>
                                                <div class="relative">
                                                    <input id="confirm-password" type="password"
                                                        autocomplete="new-password" minlength="8"
                                                        class="w-full input-field pr-10" required>
                                                    <button type="button"
                                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                                                        onclick="togglePasswordVisibility('confirm-password', 'confirm-password-icon')"
                                                        aria-label="<?= t('profile_toggle_password', 'Toggle password visibility') ?>">
                                                        <i class="fas fa-eye" id="confirm-password-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex justify-end">
                                            <button type="button" onclick="updatePassword()" class="btn-secondary">
                                                <?= t('profile_password_update') ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="updateProfile()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <?= t('profile_save_changes') ?>
                    </button>
                    <button type="button" onclick="closeEditProfileModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        <?= t('profile_cancel') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function openEditProfileModal() {
            document.getElementById('editProfileModal').classList.remove('hidden');
            document.getElementById('current-password').value = '';
            document.getElementById('new-password').value = '';
            document.getElementById('confirm-password').value = '';
        }

        function closeEditProfileModal() {
            document.getElementById('editProfileModal').classList.add('hidden');
            document.getElementById('current-password').value = '';
            document.getElementById('new-password').value = '';
            document.getElementById('confirm-password').value = '';
        }
    </script>

    <style>
        .input-field {
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: border-color 0.15s ease-in-out;
        }

        .input-field:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle button {
            padding: 0;
            background: none;
            border: none;
            cursor: pointer;
        }
    </style>

    <!-- Add Experience Modal -->
    <div id="addExperienceModal" class="fixed inset-0 z-50 overflow-y-auto hidden"
        aria-labelledby="experience-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-briefcase text-indigo-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="experience-modal-title">
                                <?= t('profile_add_experience') ?>
                            </h3>
                            <div class="mt-4">
                                <form id="addExperienceForm" onsubmit="event.preventDefault(); addExperience()">
                                    <input type="hidden" id="experience-id">
                                    <div class="mb-4">
                                        <label for="exp-position"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_position') ?></label>
                                        <input id="exp-position" type="text" class="w-full input-field" required />
                                    </div>
                                    <div class="mb-4">
                                        <label for="exp-company"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_company') ?></label>
                                        <input id="exp-company" type="text" class="w-full input-field" required />
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label for="exp-start"
                                                class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_start') ?></label>
                                            <input id="exp-start" type="date" class="w-full input-field" />
                                        </div>
                                        <div>
                                            <label for="exp-end"
                                                class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_end') ?></label>
                                            <input id="exp-end" type="date" class="w-full input-field" />
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="exp-description"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_description') ?></label>
                                        <textarea id="exp-description" rows="3" class="w-full input-field"></textarea>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button id="experience-save-btn" type="button" onclick="addExperience()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <?= t('profile_add') ?>
                    </button>
                    <button type="button" onclick="closeAddExperienceModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        <?= t('profile_cancel') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Education Modal -->
    <div id="addEducationModal" class="fixed inset-0 z-50 overflow-y-auto hidden"
        aria-labelledby="education-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-graduation-cap text-indigo-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="education-modal-title">
                                <?= t('profile_add_education') ?>
                            </h3>
                            <div class="mt-4">
                                <form id="addEducationForm" onsubmit="event.preventDefault(); addEducation()">
                                    <input type="hidden" id="education-id">
                                    <div class="mb-4">
                                        <label for="edu-degree"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_degree') ?></label>
                                        <select id="edu-degree" class="w-full input-field" required>
                                            <option value=""><?= t('profile_degree_select') ?></option>
                                            <option value="<?= t('profile_degree_secondary') ?>">
                                                <?= t('profile_degree_secondary') ?></option>
                                            <option value="<?= t('profile_degree_bachelor') ?>">
                                                <?= t('profile_degree_bachelor') ?>
                                            </option>
                                            <option value="<?= t('profile_degree_master') ?>">
                                                <?= t('profile_degree_master') ?></option>
                                            <option value="<?= t('profile_degree_phd') ?>">
                                                <?= t('profile_degree_phd') ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="edu-institution"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_institution') ?></label>
                                        <input id="edu-institution" type="text" class="w-full input-field" required />
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label for="edu-start"
                                                class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_start') ?></label>
                                            <input id="edu-start" type="date" class="w-full input-field" />
                                        </div>
                                        <div>
                                            <label for="edu-end"
                                                class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_end') ?></label>
                                            <input id="edu-end" type="date" class="w-full input-field" />
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="edu-description"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_description') ?></label>
                                        <textarea id="edu-description" rows="3" class="w-full input-field"></textarea>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button id="education-save-btn" type="button" onclick="addEducation()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <?= t('profile_add') ?>
                    </button>
                    <button type="button" onclick="closeAddEducationModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        <?= t('profile_cancel') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Portfolio Modal -->
    <div id="portfolioModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-hidden="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-folder-open text-indigo-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="portfolio-modal-title">
                                <?= t('profile_portfolio_project') ?>
                            </h3>
                            <div class="mt-4">
                                <form id="portfolioForm" onsubmit="event.preventDefault(); savePortfolio()">
                                    <input type="hidden" id="portfolio-id">
                                    <div class="mb-4">
                                        <label for="portfolio-title"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_title') ?></label>
                                        <input id="portfolio-title" type="text" class="w-full input-field" required />
                                    </div>
                                    <div class="mb-4">
                                        <label for="portfolio-category"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_category') ?></label>
                                        <input id="portfolio-category" type="text" class="w-full input-field" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="portfolio-image"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_image_url') ?></label>
                                        <input id="portfolio-image" type="text" class="w-full input-field" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="portfolio-github"
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_github_url', 'GitHub URL') ?></label>
                                        <input id="portfolio-github" type="url" class="w-full input-field"
                                            placeholder="https://github.com/username/repo" />
                                    </div>
                                    <div class="mb-4">
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1"><?= t('profile_or_upload') ?></label>
                                        <div class="flex items-center gap-2">
                                            <input id="portfolio-image-file" type="file" accept="image/*"
                                                class="w-full input-field">
                                            <button type="button" class="btn-ghost"
                                                onclick="uploadImageToInput('portfolio-image-file','portfolio-image')">
                                                <i class="fas fa-upload mr-1"></i> <?= t('profile_upload') ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button id="portfolio-save-btn" type="button" onclick="savePortfolio()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <?= t('profile_save') ?>
                    </button>
                    <button type="button" onclick="closePortfolioModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        <?= t('profile_cancel') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Platform Review Modal -->
    <div id="reviewModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeReviewModal()"></div>
        <div class="relative max-w-lg w-full review-dialog mx-auto">
            <div class="bg-white px-6 py-5 border-b border-slate-200">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-10 w-10 rounded-2xl bg-indigo-50 ring-1 ring-indigo-100 flex items-center justify-center">
                            <i class="fas fa-star text-indigo-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold leading-tight text-slate-900">
                                <?= t('profile_platform_review') ?>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5"><?= t('profile_rating_required') ?></p>
                        </div>
                    </div>
                    <button type="button" onclick="closeReviewModal()"
                        class="h-10 w-10 rounded-2xl bg-slate-50 hover:bg-slate-100 ring-1 ring-slate-200 flex items-center justify-center transition text-slate-500 hover:text-slate-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="bg-white px-6 py-6">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2"><?= t('profile_rating') ?></label>
                        <div class="review-stars-wrap">
                            <div id="review-stars" class="review-stars-row" aria-label="<?= t('profile_rating') ?>">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button type="button" class="review-star text-gray-300" data-value="<?= $i ?>"
                                        aria-label="star-<?= $i ?>" title="<?= $i ?>/5">★</button>
                                <?php endfor; ?>
                            </div>
                            <span class="text-xs font-semibold text-slate-500 whitespace-nowrap"
                                id="review-rating-label"></span>
                            <span
                                class="inline-flex items-center justify-center h-10 w-10 rounded-2xl bg-white ring-1 ring-slate-200"
                                aria-hidden="true" title="mood">
                                <i id="review-mood-icon" class="fa-regular fa-face-meh text-slate-500 text-lg"></i>
                            </span>
                        </div>
                        <input type="hidden" id="review-rating" value="<?= (int) ($user['review']['rating'] ?? 0) ?>">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2"><?= t('profile_comment') ?></label>
                        <textarea id="review-comment" class="input-field w-full resize-y" rows="4" maxlength="1000"
                            placeholder="<?= t('profile_review_placeholder') ?>"><?= htmlspecialchars($user['review']['comment'] ?? '') ?></textarea>
                        <p class="text-xs text-slate-500 mt-2"><?= t('profile_comment_max', 'Максимально: 1000') ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
                <button class="btn-secondary" onclick="closeReviewModal()"><?= t('profile_cancel') ?></button>
                <button class="btn-primary" onclick="submitReview()"><?= t('profile_save') ?></button>
            </div>
        </div>
    </div>

    <?php
    $footerContext = 'profile';
    include 'includes/footer.php';
    ?>

    <script>
        const tfI18n = {
            skillVerified: '<?= t('profile_skill_verified') ?>',
            ratingRequired: '<?= t('profile_rating_required') ?>',
            commentTooLong: '<?= t('profile_comment_too_long') ?>',
            reviewSaved: '<?= t('profile_review_saved') ?>',
            errorGeneric: '<?= t('profile_error') ?>',
            profileUpdated: '<?= t('profile_updated') ?>',
            profileSaveError: '<?= t('profile_save_error') ?>',
            expEditTitle: '<?= t('profile_edit_experience') ?>',
            expAddTitle: '<?= t('profile_add_experience') ?>',
            eduEditTitle: '<?= t('profile_edit_education') ?>',
            eduAddTitle: '<?= t('profile_add_education') ?>',
            add: '<?= t('profile_add') ?>',
            save: '<?= t('profile_save') ?>',
            expSaved: '<?= t('profile_exp_saved') ?>',
            expAdded: '<?= t('profile_exp_added') ?>',
            expSaveError: '<?= t('profile_exp_save_error') ?>',
            eduSaved: '<?= t('profile_edu_saved') ?>',
            eduAdded: '<?= t('profile_edu_added') ?>',
            eduSaveError: '<?= t('profile_edu_save_error') ?>',
            portfolioAddTitle: '<?= t('profile_add_project') ?>',
            portfolioEditTitle: '<?= t('profile_edit_project') ?>',
            portfolioSaved: '<?= t('profile_portfolio_saved') ?>',
            portfolioSaveError: '<?= t('profile_portfolio_save_error') ?>',
            confirmDeleteExp: '<?= t('profile_confirm_delete_exp') ?>',
            confirmDeleteEdu: '<?= t('profile_confirm_delete_edu') ?>',
            confirmDeleteProject: '<?= t('profile_confirm_delete_project') ?>',
            expDeleted: '<?= t('profile_exp_deleted') ?>',
            expDeleteError: '<?= t('profile_exp_delete_error') ?>',
            eduDeleted: '<?= t('profile_edu_deleted') ?>',
            eduDeleteError: '<?= t('profile_edu_delete_error') ?>',
            projectDeleted: '<?= t('profile_project_deleted') ?>',
            projectDeleteError: '<?= t('profile_project_delete_error') ?>',
            fileRequired: '<?= t('profile_file_required') ?>',
            uploadError: '<?= t('profile_upload_error') ?>',
            uploadSuccess: '<?= t('profile_upload_success') ?>',
            skillNameRequired: '<?= t('profile_skill_name_required') ?>',
            done: '<?= t('profile_done') ?>',
            confirmDeleteSkill: '<?= t('profile_confirm_delete_skill') ?>',
            quizLoading: '<?= t('profile_quiz_loading') ?>',
            quizQuestion: '<?= t('profile_quiz_question') ?>',
            quizYes: '<?= t('profile_quiz_yes') ?>',
            quizNo: '<?= t('profile_quiz_no') ?>',
            quizServerEmpty: '<?= t('profile_quiz_server_empty') ?>',
            quizLoadFail: '<?= t('profile_quiz_load_fail') ?>',
            quizFail: '<?= t('profile_quiz_fail') ?>',
            requiredProfileFields: '<?= t('profile_required_fields', 'Заполните обязательные поля: имя, позиция и локация') ?>',
            invalidUrl: '<?= t('profile_invalid_url', 'Некорректная ссылка') ?>',
            invalidLinkedin: '<?= t('profile_invalid_linkedin', 'Укажите корректную ссылку LinkedIn') ?>',
            invalidGithub: '<?= t('profile_invalid_github', 'Укажите корректную ссылку GitHub') ?>',
            invalidTelegram: '<?= t('profile_invalid_telegram', 'Укажите корректную ссылку Telegram') ?>',
            invalidWebsite: '<?= t('profile_invalid_website', 'Укажите корректную ссылку сайта') ?>',
            commentMax: '<?= t('profile_comment_max', 'Максимум: 1000') ?>',
            skillStateLoadingTitle: '<?= t('profile_skill_state_loading_title', 'Загрузка состояния проверки...') ?>',
            skillStateLoadingSub: '<?= t('profile_skill_state_loading_sub', 'Проверяем доступный тур и лимиты') ?>',
            skillStateLoadFail: '<?= t('profile_skill_state_load_fail', 'Не удалось загрузить состояние проверки.') ?>',
            skillStateNotStarted: '<?= t('profile_skill_state_not_started', 'Проверка не начата.') ?>',
            skillStateInProgress: '<?= t('profile_skill_state_in_progress', 'Проверка в процессе.') ?>',
            skillStateCompleted: '<?= t('profile_skill_state_completed', 'Максимальный уровень подтвержден.') ?>',
            skillStateSurrendered: '<?= t('profile_skill_state_surrendered', 'Проверка остановлена (сдались).') ?>',
            skillRoundUsed: '<?= t('profile_skill_round_used', 'Тур {round} уже использован: {score}/{total} ({percent}%).') ?>',
            skillRoundUnused: '<?= t('profile_skill_round_unused', 'Тур {round}: не использован') ?>',
            skillRoundPassed: '<?= t('profile_skill_round_passed', 'Тур {round}: пройден ({score}/{total}, {percent}%)') ?>',
            skillRoundFailed: '<?= t('profile_skill_round_failed', 'Тур {round}: использован ({score}/{total}, {percent}%)') ?>',
            skillRoundStart: '<?= t('profile_skill_round_start', 'Начать тур {round} ({difficulty}, проходной {pass}%)') ?>',
            skillRoundUnavailable: '<?= t('profile_skill_round_unavailable', 'Тур {round} ({difficulty}) недоступен: попытка уже использована.') ?>',
            skillSurrender: '<?= t('profile_skill_surrender', 'Сдаться и зафиксировать {percent}%') ?>',
            skillStatusLabel: '<?= t('profile_skill_status_label', 'Статус:') ?>',
            skillMaxLabel: '<?= t('profile_skill_max_label', 'Максимум:') ?>',
            skillMaxSummary: '<?= t('profile_skill_max_summary', 'тур {round}/3, уровень {percent}%') ?>',
            skillRules: <?= json_encode(t('profile_skill_rules', 'Правила: Тур 1 - {d1} (10 вопросов, 40%), Тур 2 - {d2} (10 вопросов, 70%), Тур 3 - {d3} (10 вопросов, 100%).')) ?>,
            skillRulesLimit: <?= json_encode(t('profile_skill_rules_limit', 'Ограничение: каждый тур можно пройти только 1 раз.')) ?>,
            skillRoundGenerating: <?= json_encode(t('profile_skill_round_generating', 'Генерация тура {round}...')) ?>,
            skillRoundGeneratingSub: <?= json_encode(t('profile_skill_round_generating_sub', 'Подбираем 10 вопросов нужной сложности')) ?>,
            skillRoundStartFail: <?= json_encode(t('profile_skill_round_start_fail', 'Не удалось начать тур.')) ?>,
            skillAnswersRequired: <?= json_encode(t('profile_skill_answers_required', 'Ответьте на все вопросы перед отправкой.')) ?>,
            skillRoundSending: <?= json_encode(t('profile_skill_round_sending', 'Отправка тура {round}...')) ?>,
            skillRoundSendFail: <?= json_encode(t('profile_skill_round_send_fail', 'Не удалось отправить ответы.')) ?>,
            skillRoundSendError: <?= json_encode(t('profile_skill_round_send_error', 'Ошибка отправки ответов.')) ?>,
            skillResultReceived: <?= json_encode(t('profile_skill_result_received', 'Результат получен')) ?>,
            skillStateSaveFail: <?= json_encode(t('profile_skill_state_save_fail', 'Не удалось зафиксировать результат.')) ?>,
            skillRoundSubmit: <?= json_encode(t('profile_skill_round_submit', 'Отправить тур {round}')) ?>,
            skillRoundHeader: <?= json_encode(t('profile_skill_round_header', 'Тур {round}: {difficulty}, проходной порог {pass}%')) ?>,
            skillDifficultyEasy: '<?= t('profile_skill_difficulty_easy', 'easy') ?>',
            skillDifficultyMedium: '<?= t('profile_skill_difficulty_medium', 'medium') ?>',
            skillDifficultyHard: '<?= t('profile_skill_difficulty_hard', 'hard') ?>'
        };

        document.addEventListener('DOMContentLoaded', () => {
            initReviewStars();
            initEditCountryPicker();
            initProfileMotion();
            const flash = sessionStorage.getItem('tf_flash');
            if (flash) {
                sessionStorage.removeItem('tf_flash');
                if (window.tfNotify) tfNotify(flash, 'success');
            }
            document.querySelectorAll('[data-skill-action="edit"]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.skillId;
                    const name = btn.dataset.skillName || '';
                    const category = btn.dataset.skillCategory || 'technical';
                    openEditSkillModal(id, name, category);
                });
            });
            document.querySelectorAll('[data-skill-action="verify"]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.skillId;
                    const name = btn.dataset.skillName || '';
                    if (btn.dataset.skillVerified === '1') {
                        notify(tfI18n.skillVerified);
                    }
                    startSkillQuiz(id, name);
                });
            });
        });

        function initProfileMotion() {
            const revealTargets = document.querySelectorAll(
                '.card, .section-card, .timeline-item, .portfolio-card, .skill-tag, .profile-mobile-tab, .profile-tabs-nav a, .heat-cell'
            );
            if (!revealTargets.length) return;
            revealTargets.forEach((el, index) => {
                el.classList.add('tf-reveal');
                const delay = Math.min(520, index * 28);
                el.style.setProperty('--reveal-delay', `${delay}ms`);
            });
            const showAll = () => {
                revealTargets.forEach(el => el.classList.add('is-visible'));
            };
            if (!('IntersectionObserver' in window)) {
                showAll();
                return;
            }
            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            }, {
                rootMargin: '0px 0px -8% 0px',
                threshold: 0.06
            });
            revealTargets.forEach(el => io.observe(el));
        }

        function openEditProfileModal() {
            document.getElementById('editProfileModal').classList.remove('hidden');
        }

        function closeEditProfileModal() {
            document.getElementById('editProfileModal').classList.add('hidden');
        }

        function initEditCountryPicker() {
            const button = document.getElementById('edit-country-button');
            const menu = document.getElementById('edit-country-menu');
            const codeInput = document.getElementById('edit-country');
            const nameInput = document.getElementById('edit-country-name');
            const flagLabel = document.getElementById('edit-country-button-flag');
            const textLabel = document.getElementById('edit-country-button-label');
            if (!button || !menu || !codeInput || !nameInput || !flagLabel || !textLabel) {
                return;
            }

            const getOption = (code) => {
                const safeCode = String(code || '').trim().toUpperCase();
                return menu.querySelector(`.country-picker-option[data-country-code="${safeCode}"]`);
            };

            const closeMenu = () => {
                menu.classList.add('hidden');
                button.setAttribute('aria-expanded', 'false');
            };

            const openMenu = () => {
                menu.classList.remove('hidden');
                button.setAttribute('aria-expanded', 'true');
            };

            const setSelection = (code, name, flag) => {
                const safeCode = String(code || '').trim().toUpperCase();
                const safeName = String(name || '').trim();
                const safeFlag = String(flag || '').trim();
                codeInput.value = safeCode;
                nameInput.value = safeName;
                flagLabel.src = safeFlag || (safeCode ? `https://flagcdn.com/w20/${safeCode.toLowerCase()}.png` : 'https://flagcdn.com/w20/un.png');
                flagLabel.alt = safeName || safeCode || '';
                textLabel.textContent = safeName || '<?= t('placeholder_country', 'Выберите страну') ?>';
                if (safeCode) {
                    button.classList.add('has-value');
                } else {
                    button.classList.remove('has-value');
                }
            };

            const syncFromInputs = () => {
                const currentCode = codeInput.value.trim().toUpperCase();
                const option = currentCode ? getOption(currentCode) : null;
                if (option) {
                    setSelection(
                        option.dataset.countryCode || '',
                        option.dataset.countryName || '',
                        option.dataset.countryFlagSrc || ''
                    );
                } else {
                    setSelection('', '', '');
                }
            };

            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                if (menu.classList.contains('hidden')) {
                    openMenu();
                } else {
                    closeMenu();
                }
            });

            menu.querySelectorAll('.country-picker-option').forEach(option => {
                option.addEventListener('click', () => {
                    setSelection(
                        option.dataset.countryCode || '',
                        option.dataset.countryName || '',
                        option.dataset.countryFlagSrc || ''
                    );
                    closeMenu();
                });
            });

            document.addEventListener('click', (event) => {
                if (!button.contains(event.target) && !menu.contains(event.target)) {
                    closeMenu();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeMenu();
                }
            });

            syncFromInputs();
        }

        function openReviewModal() {
            document.getElementById('reviewModal').classList.remove('hidden');
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
        }

        function initReviewStars() {
            const stars = document.querySelectorAll('.review-star');
            const ratingInput = document.getElementById('review-rating');
            const row = document.getElementById('review-stars');
            const label = document.getElementById('review-rating-label');
            const moodIcon = document.getElementById('review-mood-icon');
            let current = parseInt(ratingInput.value || '0', 10);
            const setMood = (val) => {
                if (!moodIcon) return;
                const iconClasses = [
                    'fa-face-angry',
                    'fa-face-frown',
                    'fa-face-meh',
                    'fa-face-smile',
                    'fa-face-grin-stars'
                ];
                const colorClasses = [
                    'text-rose-500',
                    'text-orange-500',
                    'text-slate-500',
                    'text-emerald-600',
                    'text-indigo-600'
                ];
                iconClasses.forEach(c => moodIcon.classList.remove(c));
                colorClasses.forEach(c => moodIcon.classList.remove(c));
                if (!val) {
                    moodIcon.classList.add('fa-face-meh', 'text-slate-500');
                    return;
                }
                const idx = Math.max(1, Math.min(5, val)) - 1;
                moodIcon.classList.add(iconClasses[idx], colorClasses[idx]);
            };
            const paint = (val) => {
                stars.forEach(star => {
                    const v = parseInt(star.dataset.value, 10);
                    star.classList.toggle('text-yellow-400', v <= val && val > 0);
                    star.classList.toggle('text-gray-300', v > val || val === 0);
                });
                if (label) label.textContent = val ? `${val}/5` : '…/5';
                setMood(val);
            };
            const set = (val) => {
                current = val;
                ratingInput.value = val;
                paint(val);
            };
            stars.forEach(star => {
                const v = parseInt(star.dataset.value, 10);
                star.addEventListener('click', () => set(v));
                star.addEventListener('mouseenter', () => paint(v));
            });
            if (row) {
                row.addEventListener('mouseleave', () => paint(current));
            }
            paint(current);
        }

        function submitReview() {
            const rating = parseInt(document.getElementById('review-rating').value || '0', 10);
            const comment = document.getElementById('review-comment').value.trim();
            if (rating < 1 || rating > 5) {
                return notify(tfI18n.ratingRequired);
            }
            if (comment.length > 1000) {
                return notify(tfI18n.commentTooLong);
            }
            fetch('?action=platform-review', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ rating, comment })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        sessionStorage.setItem('tf_flash', tfI18n.reviewSaved);
                        closeReviewModal();
                        window.location.reload();
                    } else {
                        notify(data.message || tfI18n.errorGeneric);
                    }
                })
                .catch(() => notify(tfI18n.errorGeneric));
        }

        function normalizeProfileUrl(value) {
            const raw = (value || '').trim();
            if (!raw) return '';
            if (/^[a-z][a-z0-9+.-]*:\/\//i.test(raw)) return raw;
            if (/^www\./i.test(raw) || /^[A-Za-z0-9.-]+\.[A-Za-z]{2,}([/?#].*)?$/.test(raw)) {
                return `https://${raw}`;
            }
            return raw;
        }

        function normalizeTelegram(value) {
            const raw = (value || '').trim();
            if (!raw) return '';
            if (/^@?[A-Za-z0-9_]{3,}$/.test(raw)) {
                return `https://t.me/${raw.replace(/^@/, '')}`;
            }
            if (/^t\.me\//i.test(raw)) {
                return `https://${raw}`;
            }
            return normalizeProfileUrl(raw);
        }

        function isValidUrl(value) {
            if (!value) return true;
            try {
                const url = new URL(value);
                return ['http:', 'https:'].includes(url.protocol);
            } catch (e) {
                return false;
            }
        }

        function isHostMatchingDomain(value, domain) {
            if (!value || !domain) return false;
            try {
                const host = String((new URL(value)).hostname || '').toLowerCase();
                const d = String(domain || '').toLowerCase();
                return host === d || host.endsWith(`.${d}`);
            } catch (e) {
                return false;
            }
        }

        function normalizeUploadsPath(value) {
            const raw = (value || '').trim();
            if (!raw) return '';
            const marker = '/uploads/';
            const markerIndex = raw.toLowerCase().indexOf(marker);
            if (markerIndex >= 0) {
                return raw.slice(markerIndex + 1);
            }
            if (/^uploads\//i.test(raw)) {
                return raw;
            }
            if (/^\/uploads\//i.test(raw)) {
                return raw.slice(1);
            }
            return raw;
        }

        function toDateInputValue(value) {
            const raw = (value || '').trim();
            if (!raw) return '';
            if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) return raw;
            if (/^\d{4}-\d{2}$/.test(raw)) return `${raw}-01`;
            if (/^\d{4}$/.test(raw)) return `${raw}-01-01`;
            const parsed = new Date(raw);
            if (!Number.isNaN(parsed.getTime())) {
                return parsed.toISOString().slice(0, 10);
            }
            return '';
        }

        function updateProfile() {
            const name = document.getElementById('edit-name').value.trim();
            const title = document.getElementById('edit-title').value.trim();
            const location = document.getElementById('edit-location').value.trim();
            const countryCode = document.getElementById('edit-country')?.value.trim() || '';
            const countryName = document.getElementById('edit-country-name')?.value.trim() || '';
            const bio = document.getElementById('edit-bio').value.trim();
            const avatar = normalizeUploadsPath(document.getElementById('edit-avatar').value);
            const linkedin = normalizeProfileUrl(document.getElementById('edit-linkedin').value);
            const github = normalizeProfileUrl(document.getElementById('edit-github').value);
            const telegram = document.getElementById('edit-telegram').value.trim();
            const website = normalizeProfileUrl(document.getElementById('edit-website').value);
            if (!name || !title || !location || !countryCode) {
                notify(tfI18n.requiredProfileFields);
                return;
            }
            const normalizedTelegram = normalizeTelegram(telegram);
            const isUploadPath = /(^|\/)uploads\//i.test(avatar);
            if (avatar && !isValidUrl(avatar) && !isUploadPath) {
                notify(tfI18n.invalidUrl);
                return;
            }
            if (linkedin && (!isValidUrl(linkedin) || !isHostMatchingDomain(linkedin, 'linkedin.com'))) {
                notify(tfI18n.invalidLinkedin);
                return;
            }
            if (github && (!isValidUrl(github) || !isHostMatchingDomain(github, 'github.com'))) {
                notify(tfI18n.invalidGithub);
                return;
            }
            if (normalizedTelegram && (!isValidUrl(normalizedTelegram) || !isHostMatchingDomain(normalizedTelegram, 't.me'))) {
                notify(tfI18n.invalidTelegram);
                return;
            }
            if (website && !isValidUrl(website)) {
                notify(tfI18n.invalidWebsite);
                return;
            }
            const formData = {
                name,
                title,
                location,
                bio,
                countryCode,
                countryName,
                avatar,
                social_linkedin: linkedin,
                social_github: github,
                social_telegram: normalizedTelegram,
                social_website: website
            };
            fetch('?action=update-profile', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data && data.success) {
                        sessionStorage.setItem('tf_flash', tfI18n.profileUpdated);
                        closeEditProfileModal();
                        window.location.reload();
                        return;
                    }
                    notify((data && data.message) ? data.message : tfI18n.profileSaveError);
                })
                .catch(() => notify(tfI18n.profileSaveError));
        }

        function setExperienceModalState(mode) {
            const title = document.getElementById('experience-modal-title');
            const btn = document.getElementById('experience-save-btn');
            if (mode === 'edit') {
                title.textContent = tfI18n.expEditTitle;
                btn.textContent = tfI18n.save;
            } else {
                title.textContent = tfI18n.expAddTitle;
                btn.textContent = tfI18n.add;
            }
        }

        function openAddExperienceModal() {
            document.getElementById('experience-id').value = '';
            document.getElementById('exp-position').value = '';
            document.getElementById('exp-company').value = '';
            document.getElementById('exp-start').value = '';
            document.getElementById('exp-end').value = '';
            document.getElementById('exp-description').value = '';
            setExperienceModalState('add');
            document.getElementById('addExperienceModal').classList.remove('hidden');
        }

        function openEditExperienceModal(id, position, company, start, end, description) {
            document.getElementById('experience-id').value = id;
            document.getElementById('exp-position').value = position || '';
            document.getElementById('exp-company').value = company || '';
            document.getElementById('exp-start').value = toDateInputValue(start);
            document.getElementById('exp-end').value = toDateInputValue(end);
            document.getElementById('exp-description').value = description || '';
            setExperienceModalState('edit');
            document.getElementById('addExperienceModal').classList.remove('hidden');
        }

        function closeAddExperienceModal() {
            document.getElementById('addExperienceModal').classList.add('hidden');
        }

        function addExperience() {
            const id = document.getElementById('experience-id').value;
            const formData = {
                id: id || undefined,
                position: document.getElementById('exp-position').value,
                company: document.getElementById('exp-company').value,
                start: document.getElementById('exp-start').value,
                end: document.getElementById('exp-end').value,
                description: document.getElementById('exp-description').value
            };
            const action = id ? 'update-experience' : 'add-experience';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data && data.success) {
                        sessionStorage.setItem('tf_flash', id ? tfI18n.expSaved : tfI18n.expAdded);
                        closeAddExperienceModal();
                        window.location.reload();
                        return;
                    }
                    notify(tfI18n.expSaveError);
                })
                .catch(() => notify(tfI18n.expSaveError));
        }

        function setEducationModalState(mode) {
            const title = document.getElementById('education-modal-title');
            const btn = document.getElementById('education-save-btn');
            if (mode === 'edit') {
                title.textContent = tfI18n.eduEditTitle;
                btn.textContent = tfI18n.save;
            } else {
                title.textContent = tfI18n.eduAddTitle;
                btn.textContent = tfI18n.add;
            }
        }

        function openAddEducationModal() {
            document.getElementById('education-id').value = '';
            document.getElementById('edu-degree').value = '';
            document.getElementById('edu-institution').value = '';
            document.getElementById('edu-start').value = '';
            document.getElementById('edu-end').value = '';
            document.getElementById('edu-description').value = '';
            setEducationModalState('add');
            document.getElementById('addEducationModal').classList.remove('hidden');
        }

        function openEditEducationModal(id, degree, institution, start, end, description) {
            document.getElementById('education-id').value = id;
            document.getElementById('edu-degree').value = degree || '';
            document.getElementById('edu-institution').value = institution || '';
            document.getElementById('edu-start').value = toDateInputValue(start);
            document.getElementById('edu-end').value = toDateInputValue(end);
            document.getElementById('edu-description').value = description || '';
            setEducationModalState('edit');
            document.getElementById('addEducationModal').classList.remove('hidden');
        }

        function closeAddEducationModal() {
            document.getElementById('addEducationModal').classList.add('hidden');
        }

        function addEducation() {
            const id = document.getElementById('education-id').value;
            const formData = {
                id: id || undefined,
                degree: document.getElementById('edu-degree').value,
                institution: document.getElementById('edu-institution').value,
                start: document.getElementById('edu-start').value,
                end: document.getElementById('edu-end').value,
                description: document.getElementById('edu-description').value
            };
            const action = id ? 'update-education' : 'add-education';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data && data.success) {
                        sessionStorage.setItem('tf_flash', id ? tfI18n.eduSaved : tfI18n.eduAdded);
                        closeAddEducationModal();
                        window.location.reload();
                        return;
                    }
                    notify(tfI18n.eduSaveError);
                })
                .catch(() => notify(tfI18n.eduSaveError));
        }

        function openAddPortfolioModal() {
            document.getElementById('portfolio-id').value = '';
            document.getElementById('portfolio-title').value = '';
            document.getElementById('portfolio-category').value = '';
            document.getElementById('portfolio-image').value = '';
            document.getElementById('portfolio-github').value = '';
            document.getElementById('portfolio-modal-title').textContent = tfI18n.portfolioAddTitle;
            document.getElementById('portfolio-save-btn').textContent = tfI18n.add;
            document.getElementById('portfolioModal').classList.remove('hidden');
        }

        function openEditPortfolioModal(id, title, category, imageUrl, githubUrl) {
            document.getElementById('portfolio-id').value = id;
            document.getElementById('portfolio-title').value = title || '';
            document.getElementById('portfolio-category').value = category || '';
            document.getElementById('portfolio-image').value = imageUrl || '';
            document.getElementById('portfolio-github').value = githubUrl || '';
            document.getElementById('portfolio-modal-title').textContent = tfI18n.portfolioEditTitle;
            document.getElementById('portfolio-save-btn').textContent = tfI18n.save;
            document.getElementById('portfolioModal').classList.remove('hidden');
        }

        function closePortfolioModal() {
            document.getElementById('portfolioModal').classList.add('hidden');
        }

        function savePortfolio() {
            const id = document.getElementById('portfolio-id').value;
            const githubUrl = normalizeProfileUrl(document.getElementById('portfolio-github').value);
            if (githubUrl && (!isValidUrl(githubUrl) || !isHostMatchingDomain(githubUrl, 'github.com'))) {
                notify(tfI18n.invalidGithub);
                return;
            }
            const formData = {
                id: id || undefined,
                title: document.getElementById('portfolio-title').value.trim(),
                category: document.getElementById('portfolio-category').value,
                image_url: normalizeUploadsPath(document.getElementById('portfolio-image').value),
                github_url: githubUrl
            };
            if (!formData.title) {
                notify(tfI18n.portfolioSaveError);
                return;
            }
            const action = id ? 'update-portfolio' : 'add-portfolio';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data && data.success) {
                        sessionStorage.setItem('tf_flash', tfI18n.portfolioSaved);
                        closePortfolioModal();
                        window.location.reload();
                        return;
                    }
                    notify((data && data.message) ? data.message : tfI18n.portfolioSaveError);
                })
                .catch(() => notify(tfI18n.portfolioSaveError));
        }

        function deleteExperience(id) {
            if (!confirm(tfI18n.confirmDeleteExp)) return;
            fetch('?action=delete-experience', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ id })
            })
                .then(r => r.json())
                .then(data => {
                    if (data && data.success) {
                        sessionStorage.setItem('tf_flash', tfI18n.expDeleted);
                        window.location.reload();
                        return;
                    }
                    notify(tfI18n.expDeleteError);
                })
                .catch(() => notify(tfI18n.expDeleteError));
        }

        function deleteEducation(id) {
            if (!confirm(tfI18n.confirmDeleteEdu)) return;
            fetch('?action=delete-education', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ id })
            })
                .then(r => r.json())
                .then(data => {
                    if (data && data.success) {
                        sessionStorage.setItem('tf_flash', tfI18n.eduDeleted);
                        window.location.reload();
                        return;
                    }
                    notify(tfI18n.eduDeleteError);
                })
                .catch(() => notify(tfI18n.eduDeleteError));
        }

        function deletePortfolio(id) {
            if (!confirm(tfI18n.confirmDeleteProject)) return;
            fetch('?action=delete-portfolio', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ id })
            })
                .then(r => r.json())
                .then(data => {
                    if (data && data.success) {
                        sessionStorage.setItem('tf_flash', tfI18n.projectDeleted);
                        window.location.reload();
                        return;
                    }
                    notify(tfI18n.projectDeleteError);
                })
                .catch(() => notify(tfI18n.projectDeleteError));
        }

        function notify(msg) {
            if (window.tfNotify) {
                tfNotify(msg);
            }
        }

        function uploadImageToInput(fileInputId, targetInputId) {
            const fileInput = document.getElementById(fileInputId);
            if (!fileInput || !fileInput.files || !fileInput.files[0]) {
                return notify(tfI18n.fileRequired);
            }
            const formData = new FormData();
            formData.append('image', fileInput.files[0]);
            fetch('?action=upload-image', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
                .then(r => r.text())
                .then(text => {
                    let data = null;
                    if (text) {
                        try { data = JSON.parse(text); } catch (e) { data = null; }
                    }
                    if (!data || !data.success) {
                        console.warn('upload-image raw response:', text);
                        return notify((data && data.message) ? data.message : tfI18n.uploadError);
                    }
                    document.getElementById(targetInputId).value = normalizeUploadsPath(data.path || data.url || '');
                    fileInput.value = '';
                    notify(tfI18n.uploadSuccess);
                })
                .catch(() => notify(tfI18n.uploadError));
        }

        function openAddSkillModal() {
            syncSkillSelect('skill-name', document.getElementById('skill-category').value);
            document.getElementById('addSkillModal').classList.remove('hidden');
        }

        function closeAddSkillModal() {
            document.getElementById('addSkillModal').classList.add('hidden');
        }

        function syncSkillSelect(selectId, category) {
            const select = document.getElementById(selectId);
            if (!select) return;
            const options = Array.from(select.options);
            let firstVisible = null;
            options.forEach(option => {
                const optCategory = option.getAttribute('data-skill-category');
                if (!optCategory) return;
                const isVisible = optCategory === category;
                option.hidden = !isVisible;
                option.disabled = !isVisible;
                if (isVisible && !firstVisible) {
                    firstVisible = option;
                }
            });
            if (select.selectedOptions.length === 0 || select.selectedOptions[0].disabled) {
                if (firstVisible) {
                    select.value = firstVisible.value;
                }
            }
        }

        document.getElementById('skill-category')?.addEventListener('change', (e) => {
            syncSkillSelect('skill-name', e.target.value);
        });
        document.getElementById('edit-skill-category')?.addEventListener('change', (e) => {
            syncSkillSelect('edit-skill-name', e.target.value);
        });

        function addSkill() {
            const rawSkillName = document.getElementById('skill-name').value.trim();
            const formData = {
                skillName: rawSkillName,
                category: document.getElementById('skill-category').value
            };
            if (!formData.skillName) {
                notify(tfI18n.skillNameRequired);
                return;
            }
            fetch('?action=add-skill', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    notify(data.message || tfI18n.done);
                    if (data.success) {
                        closeAddSkillModal();
                        window.location.reload();
                    }
                })
                .catch(() => notify(tfI18n.errorGeneric));
        }

        function openEditSkillModal(id, name, category) {
            document.getElementById('edit-skill-id').value = id;
            document.getElementById('edit-skill-category').value = category || 'technical';
            syncSkillSelect('edit-skill-name', document.getElementById('edit-skill-category').value);
            document.getElementById('edit-skill-name').value = name;
            document.getElementById('editSkillModal').classList.remove('hidden');
        }

        function closeEditSkillModal() {
            document.getElementById('editSkillModal').classList.add('hidden');
        }

        function updateSkill() {
            const rawSkillName = document.getElementById('edit-skill-name').value.trim();
            const formData = {
                skillId: document.getElementById('edit-skill-id').value,
                skillName: rawSkillName,
                category: document.getElementById('edit-skill-category').value
            };
            fetch('?action=update-skill', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    notify(data.message || tfI18n.done);
                    if (data.success) {
                        closeEditSkillModal();
                        window.location.reload();
                    }
                })
                .catch(() => notify(tfI18n.errorGeneric));
        }

        function deleteSkill(skillId) {
            if (!confirm(tfI18n.confirmDeleteSkill)) return;
            fetch('?action=delete-skill', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ skillId })
            })
                .then(response => response.json())
                .then(data => {
                    notify(data.message || tfI18n.done);
                    if (data.success) window.location.reload();
                })
                .catch(() => notify(tfI18n.errorGeneric));
        }

        let currentSkillQuiz = null;
        let currentSkillQuizState = null;
        let currentSkillId = null;
        let currentSkillName = '';
        let currentSkillRound = 0;
        let currentSkillRoundAction = '';

        function skillRoundMeta(round) {
            const r = parseInt(round || 0, 10);
            if (r === 1) return { difficulty: 'easy', pass: 40, level: 40 };
            if (r === 2) return { difficulty: 'medium', pass: 70, level: 70 };
            return { difficulty: 'hard', pass: 100, level: 100 };
        }

        function skillRoundAction(round) {
            const r = parseInt(round || 0, 10);
            return `?action=skill-assessment-round-${r}`;
        }

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function tfFormat(template, vars = {}) {
            return String(template || '').replace(/\{(\w+)\}/g, (_, key) => {
                return Object.prototype.hasOwnProperty.call(vars, key) ? String(vars[key]) : '';
            });
        }

        function skillDifficultyLabel(key) {
            if (key === 'easy') return tfI18n.skillDifficultyEasy;
            if (key === 'medium') return tfI18n.skillDifficultyMedium;
            return tfI18n.skillDifficultyHard;
        }

        function updateSkillQuizSubmitState(enabled, label = null) {
            const submitBtn = document.getElementById('skill-quiz-submit');
            if (!submitBtn) return;
            if (label) {
                submitBtn.textContent = label;
            }
            submitBtn.disabled = !enabled;
            submitBtn.classList.toggle('opacity-50', !enabled);
            submitBtn.classList.toggle('cursor-not-allowed', !enabled);
        }

        function startSkillQuiz(skillId, skillName) {
            currentSkillId = parseInt(skillId || '0', 10) || 0;
            currentSkillName = String(skillName || '');
            currentSkillQuiz = null;
            currentSkillQuizState = null;
            currentSkillRound = 0;
            currentSkillRoundAction = '';
            document.getElementById('skill-quiz-title').textContent = currentSkillName;
            updateSkillQuizSubmitState(false, '<?= t('profile_check') ?>');
            document.getElementById('skill-quiz-body').innerHTML = `
        <div class="tf-quiz-loader">
            <div class="tf-quiz-spinner"></div>
            <div>
                <div class="text-gray-800 font-medium">${escapeHtml(tfI18n.skillStateLoadingTitle)}</div>
                <div class="text-sm text-gray-500">${escapeHtml(tfI18n.skillStateLoadingSub)}</div>
            </div>
        </div>
    `;
            document.getElementById('skillQuizModal').classList.remove('hidden');
            loadSkillAssessmentState();
        }

        function loadSkillAssessmentState() {
            if (!currentSkillId) return;
            updateSkillQuizSubmitState(false, '<?= t('profile_check') ?>');
            fetch('?action=skill-assessment-state', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ skillId: currentSkillId })
            })
                .then(r => r.text())
                .then(text => {
                    const data = parseJsonLoose(text);
                    if (!data || !data.success || !data.state) {
                        const msg = (data && data.message) ? data.message : tfI18n.skillStateLoadFail;
                        notify(msg);
                        document.getElementById('skill-quiz-body').innerHTML = `<div class="text-rose-500">${escapeHtml(msg)}</div>`;
                        return;
                    }
                    renderSkillAssessmentState(data.state);
                })
                .catch(() => {
                    notify(tfI18n.skillStateLoadFail);
                    document.getElementById('skill-quiz-body').innerHTML = `<div class="text-rose-500">${escapeHtml(tfI18n.skillStateLoadFail)}</div>`;
                });
        }

        function renderSkillAssessmentState(state) {
            const body = document.getElementById('skill-quiz-body');
            if (!body) return;
            const status = String((state && state.status) || 'not_started');
            const maxRound = parseInt((state && state.max_round) || '0', 10) || 0;
            const maxPercent = parseInt((state && state.max_percent) || '0', 10) || 0;
            const nextRound = parseInt((state && state.next_round) || '0', 10) || 0;
            const roundAttempts = Array.isArray(state && state.round_attempts) ? state.round_attempts : [];
            const attemptByRound = new Map();
            roundAttempts.forEach((item) => {
                const rn = parseInt(item && item.round || '0', 10) || 0;
                if (rn > 0) attemptByRound.set(rn, item);
            });
            const nextRoundInfo = attemptByRound.get(nextRound) || null;
            const nextRoundUsed = !!(nextRoundInfo && nextRoundInfo.used);
            const canStart = status !== 'completed' && status !== 'surrendered' && nextRound >= 1 && nextRound <= 3 && !nextRoundUsed;
            let statusText = tfI18n.skillStateNotStarted;
            if (status === 'in_progress') statusText = tfI18n.skillStateInProgress;
            if (status === 'completed') statusText = tfI18n.skillStateCompleted;
            if (status === 'surrendered') statusText = tfI18n.skillStateSurrendered;
            const failedRound = roundAttempts.find((item) => item && item.used && !item.passed);
            if ((status === 'not_started' || status === 'in_progress') && failedRound) {
                const ts = parseInt(failedRound.score || '0', 10) || 0;
                const tt = parseInt(failedRound.total_questions || '0', 10) || 0;
                const tp = parseInt(failedRound.percent || '0', 10) || 0;
                statusText = tfFormat(tfI18n.skillRoundUsed, {
                    round: failedRound.round,
                    score: ts,
                    total: tt,
                    percent: tp
                });
            }
            const roundsBlock = roundAttempts.map((item) => {
                const r = parseInt(item && item.round || '0', 10) || 0;
                if (!r) return '';
                const used = !!item.used;
                const passed = !!item.passed;
                const score = parseInt(item.score || '0', 10) || 0;
                const total = parseInt(item.total_questions || '0', 10) || 0;
                const percent = parseInt(item.percent || '0', 10) || 0;
                let cls = 'bg-slate-100 text-slate-700 border-slate-200';
                let text = tfFormat(tfI18n.skillRoundUnused, { round: r });
                if (used && passed) {
                    cls = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                    text = tfFormat(tfI18n.skillRoundPassed, { round: r, score, total, percent });
                } else if (used && !passed) {
                    cls = 'bg-rose-100 text-rose-700 border-rose-200';
                    text = tfFormat(tfI18n.skillRoundFailed, { round: r, score, total, percent });
                }
                return `<div class="text-xs px-2.5 py-1.5 rounded-lg border ${cls}">${escapeHtml(text)}</div>`;
            }).filter(Boolean).join('');
            let nextBlock = '';
            if (canStart) {
                const meta = skillRoundMeta(nextRound);
                const diffLabel = skillDifficultyLabel(meta.difficulty);
                nextBlock = `
            <button type="button" id="skill-round-start" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">
                ${escapeHtml(tfFormat(tfI18n.skillRoundStart, { round: nextRound, difficulty: diffLabel, pass: meta.pass }))}
            </button>
        `;
            } else if (status !== 'completed' && status !== 'surrendered' && nextRound > 0) {
                const meta = skillRoundMeta(nextRound);
                const diffLabel = skillDifficultyLabel(meta.difficulty);
                nextBlock = `
            <div class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                ${escapeHtml(tfFormat(tfI18n.skillRoundUnavailable, { round: nextRound, difficulty: diffLabel }))}
            </div>
        `;
            }
            const canSurrender = status !== 'completed' && status !== 'surrendered';
            let surrenderBlock = '';
            if (canSurrender) {
                surrenderBlock = `
            <button type="button" id="skill-round-surrender" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-100">
                ${escapeHtml(tfFormat(tfI18n.skillSurrender, { percent: maxPercent }))}
            </button>
        `;
            }
            body.innerHTML = `
        <div class="space-y-4">
            <div class="exam-question rounded-xl p-4">
                <div class="text-sm text-slate-700"><strong>${escapeHtml(tfI18n.skillStatusLabel)}</strong> ${escapeHtml(statusText)}</div>
                <div class="text-sm text-slate-700 mt-1"><strong>${escapeHtml(tfI18n.skillMaxLabel)}</strong> ${escapeHtml(tfFormat(tfI18n.skillMaxSummary, { round: maxRound, percent: maxPercent }))}</div>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2">${roundsBlock}</div>
            </div>
            <div class="flex flex-wrap gap-2">
                ${nextBlock}
                ${surrenderBlock}
            </div>
            <div class="text-xs text-slate-500">
                ${escapeHtml(tfFormat(tfI18n.skillRules, {
                d1: skillDifficultyLabel('easy'),
                d2: skillDifficultyLabel('medium'),
                d3: skillDifficultyLabel('hard')
            }))} ${escapeHtml(tfI18n.skillRulesLimit)}
            </div>
        </div>
    `;
            const startBtn = document.getElementById('skill-round-start');
            if (startBtn) {
                startBtn.addEventListener('click', () => startSkillRound(nextRound));
            }
            const surrenderBtn = document.getElementById('skill-round-surrender');
            if (surrenderBtn) {
                surrenderBtn.addEventListener('click', () => surrenderSkillAssessment());
            }
        }

        function startSkillRound(round) {
            const r = parseInt(round || '0', 10) || 0;
            if (r < 1 || r > 3) return;
            currentSkillRound = r;
            currentSkillRoundAction = skillRoundAction(r);
            currentSkillQuiz = null;
            currentSkillQuizState = null;
            updateSkillQuizSubmitState(false, tfFormat(tfI18n.skillRoundSubmit, { round: r }));
            document.getElementById('skill-quiz-body').innerHTML = `
        <div class="tf-quiz-loader">
            <div class="tf-quiz-spinner"></div>
            <div>
                <div class="text-gray-800 font-medium">${escapeHtml(tfFormat(tfI18n.skillRoundGenerating, { round: r }))}</div>
                <div class="text-sm text-gray-500">${escapeHtml(tfI18n.skillRoundGeneratingSub)}</div>
            </div>
        </div>
    `;
            fetch(currentSkillRoundAction, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ skillId: currentSkillId })
            })
                .then(rsp => rsp.text())
                .then(text => {
                    const data = parseJsonLoose(text);
                    if (!data || !data.success || !data.quiz || !Array.isArray(data.quiz.questions) || data.quiz.questions.length === 0) {
                        const msg = (data && data.message) ? data.message : tfI18n.skillRoundStartFail;
                        notify(msg);
                        document.getElementById('skill-quiz-body').innerHTML = `<div class="text-rose-500">${escapeHtml(msg)}</div>`;
                        updateSkillQuizSubmitState(false, '<?= t('profile_check') ?>');
                        if (data && data.state) {
                            renderSkillAssessmentState(data.state);
                        }
                        return;
                    }
                    currentSkillQuiz = data.quiz;
                    currentSkillRound = parseInt(data.round || r, 10) || r;
                    updateSkillQuizSubmitState(true, tfFormat(tfI18n.skillRoundSubmit, { round: currentSkillRound }));
                    renderSkillQuiz();
                })
                .catch(() => {
                    notify(tfI18n.skillRoundStartFail);
                    document.getElementById('skill-quiz-body').innerHTML = `<div class="text-rose-500">${escapeHtml(tfI18n.skillRoundStartFail)}</div>`;
                    updateSkillQuizSubmitState(false, '<?= t('profile_check') ?>');
                });
        }

        function parseJsonLoose(text) {
            if (!text) return null;
            try {
                return JSON.parse(text);
            } catch (e) {
                // fall through to extraction
            }
            const firstObj = text.indexOf('{');
            const firstArr = text.indexOf('[');
            let start = -1;
            if (firstObj === -1 && firstArr === -1) return null;
            if (firstObj === -1) start = firstArr;
            else if (firstArr === -1) start = firstObj;
            else start = Math.min(firstObj, firstArr);
            const end = text.lastIndexOf(start === firstArr ? ']' : '}');
            if (end === -1 || end <= start) return null;
            const slice = text.slice(start, end + 1);
            try {
                return JSON.parse(slice);
            } catch (e) {
                return null;
            }
        }

        function renderSkillQuiz() {
            if (!currentSkillQuiz || !currentSkillQuiz.questions) return;
            const body = document.getElementById('skill-quiz-body');
            body.innerHTML = '';
            const meta = skillRoundMeta(currentSkillRound);
            const header = document.createElement('div');
            header.className = 'mb-4 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800';
            header.textContent = tfFormat(tfI18n.skillRoundHeader, {
                round: currentSkillRound,
                difficulty: skillDifficultyLabel(meta.difficulty),
                pass: meta.pass
            });
            body.appendChild(header);
            currentSkillQuizState = {
                questions: currentSkillQuiz.questions,
                answers: Array(currentSkillQuiz.questions.length).fill(''),
                current: 0
            };
            body.insertAdjacentHTML('beforeend', `
        <div id="skill-quiz-nav" class="grid grid-cols-8 gap-2 mb-4"></div>
        <div id="skill-quiz-card" class="exam-question exam-question-card rounded-xl p-4"></div>
        <div class="mt-3 flex items-center justify-between gap-2">
            <button type="button" id="skill-quiz-prev" class="btn-ghost px-3 py-2 text-sm">&larr;</button>
            <div id="skill-quiz-current" class="text-xs text-slate-500"></div>
            <button type="button" id="skill-quiz-next" class="btn-ghost px-3 py-2 text-sm">&rarr;</button>
        </div>
    `);
            const navWrap = document.getElementById('skill-quiz-nav');
            const card = document.getElementById('skill-quiz-card');
            const currentLabel = document.getElementById('skill-quiz-current');
            const prevBtn = document.getElementById('skill-quiz-prev');
            const nextBtn = document.getElementById('skill-quiz-next');
            const renderSkillNav = () => {
                navWrap.innerHTML = currentSkillQuizState.questions.map((_, idx) => {
                    const answered = String(currentSkillQuizState.answers[idx] || '').trim() !== '';
                    const active = idx === currentSkillQuizState.current;
                    const cls = active
                        ? 'border-indigo-500 bg-indigo-100 text-indigo-800'
                        : answered
                            ? 'border-emerald-300 bg-emerald-50 text-emerald-700'
                            : 'border-slate-200 bg-white text-slate-600';
                    return `<button type="button" data-skill-nav="${idx}" class="h-8 rounded-md border text-xs font-semibold ${cls}">${idx + 1}</button>`;
                }).join('');
                navWrap.querySelectorAll('[data-skill-nav]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        currentSkillQuizState.current = parseInt(btn.getAttribute('data-skill-nav') || '0', 10);
                        renderSkillQuestion();
                    });
                });
            };
            const renderSkillQuestion = () => {
                const idx = currentSkillQuizState.current;
                const q = currentSkillQuizState.questions[idx];
                currentLabel.textContent = `${idx + 1}/${currentSkillQuizState.questions.length}`;
                prevBtn.disabled = idx <= 0;
                nextBtn.disabled = idx >= currentSkillQuizState.questions.length - 1;
                prevBtn.classList.toggle('opacity-50', prevBtn.disabled);
                nextBtn.classList.toggle('opacity-50', nextBtn.disabled);
                card.innerHTML = `
            <div class="font-medium text-slate-900 mb-3">${idx + 1}. ${escapeHtml(q.question)}</div>
            <div class="space-y-2">
                ${q.options.map((opt, optIdx) => `
                    <label class="exam-option variant-${optIdx % 4}">
                        <input type="radio" name="skill-q${idx}" value="${escapeHtml(opt)}" ${currentSkillQuizState.answers[idx] === String(opt) ? 'checked' : ''}>
                        <span class="exam-option-key exam-option-key-${optIdx % 4}">${String.fromCharCode(65 + optIdx)}</span>
                        <span class="exam-option-text">${escapeHtml(opt)}</span>
                    </label>
                `).join('')}
            </div>
        `;
                card.classList.remove('tf-fade');
                void card.offsetWidth;
                card.classList.add('tf-fade');
                card.querySelectorAll(`input[name="skill-q${idx}"]`).forEach((input) => {
                    input.addEventListener('change', () => {
                        currentSkillQuizState.answers[idx] = input.value;
                        card.querySelectorAll('label.exam-option').forEach(el => el.classList.remove('selected'));
                        const label = input.closest('label.exam-option');
                        if (label) label.classList.add('selected');
                        renderSkillNav();
                    });
                });
                renderSkillNav();
            };
            prevBtn.addEventListener('click', () => {
                if (currentSkillQuizState.current > 0) {
                    currentSkillQuizState.current -= 1;
                    renderSkillQuestion();
                }
            });
            nextBtn.addEventListener('click', () => {
                if (currentSkillQuizState.current < currentSkillQuizState.questions.length - 1) {
                    currentSkillQuizState.current += 1;
                    renderSkillQuestion();
                }
            });
            renderSkillQuestion();
        }

        function submitSkillQuiz() {
            if (!currentSkillQuiz || !currentSkillQuiz.questions || !currentSkillRoundAction) return;
            if (!currentSkillQuizState || !Array.isArray(currentSkillQuizState.answers)) return;
            const answers = currentSkillQuizState.answers.slice();
            const allAnswered = answers.every(v => String(v || '').trim() !== '');
            if (!allAnswered) {
                notify(tfI18n.skillAnswersRequired);
                return;
            }
            updateSkillQuizSubmitState(false, tfFormat(tfI18n.skillRoundSending, { round: currentSkillRound }));
            fetch(currentSkillRoundAction, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ skillId: currentSkillId, answers })
            })
                .then(r => r.text())
                .then(data => {
                    const payload = parseJsonLoose(data);
                    if (!payload || !payload.success) {
                        const msg = (payload && payload.message) ? payload.message : tfI18n.skillRoundSendFail;
                        notify(msg);
                        updateSkillQuizSubmitState(true, tfFormat(tfI18n.skillRoundSubmit, { round: currentSkillRound }));
                        return;
                    }
                    const msg = `${payload.message || tfI18n.skillResultReceived}: ${payload.score}/${payload.total} (${payload.percent}%)`;
                    notify(msg);
                    currentSkillQuiz = null;
                    currentSkillQuizState = null;
                    currentSkillRound = 0;
                    currentSkillRoundAction = '';
                    updateSkillQuizSubmitState(false, '<?= t('profile_check') ?>');
                    if (payload.state) {
                        renderSkillAssessmentState(payload.state);
                    } else {
                        loadSkillAssessmentState();
                    }
                })
                .catch(() => {
                    notify(tfI18n.skillRoundSendError);
                    updateSkillQuizSubmitState(true, tfFormat(tfI18n.skillRoundSubmit, { round: currentSkillRound }));
                });
        }

        function surrenderSkillAssessment() {
            if (!currentSkillId) return;
            fetch('?action=skill-assessment-surrender', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ skillId: currentSkillId })
            })
                .then(r => r.text())
                .then(text => {
                    const data = parseJsonLoose(text);
                    if (!data || !data.success) {
                        notify((data && data.message) ? data.message : tfI18n.skillStateSaveFail);
                        return;
                    }
                    notify(data.message || tfI18n.done);
                    currentSkillQuiz = null;
                    currentSkillQuizState = null;
                    currentSkillRound = 0;
                    currentSkillRoundAction = '';
                    updateSkillQuizSubmitState(false, '<?= t('profile_check') ?>');
                    if (data.state) {
                        renderSkillAssessmentState(data.state);
                    } else {
                        loadSkillAssessmentState();
                    }
                })
                .catch(() => notify(tfI18n.skillStateSaveFail));
        }

        function closeSkillQuizModal() {
            document.getElementById('skillQuizModal').classList.add('hidden');
            currentSkillQuiz = null;
            currentSkillQuizState = null;
            currentSkillId = null;
            currentSkillName = '';
            currentSkillRound = 0;
            currentSkillRoundAction = '';
        }

        (function () {
            const heatmapData = {
                all: <?= json_encode($heatmapAll['map'] ?? [] | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                study: <?= json_encode($heatmapStudy['map'] ?? [] | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                contest: <?= json_encode($heatmapContest['map'] ?? [] | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
            };
            const localeMap = { ru: 'ru-RU', en: 'en-US', tg: 'tg-TJ' };
            const activityLocale = localeMap['<?= htmlspecialchars($profileLang) ?>'] || 'ru-RU';
            const labels = {
                pick: '<?= t('profile_activity_pick_day', 'Select a day to see details') ?>',
                count: '<?= t('profile_activity_count', 'activities') ?>',
                empty: '<?= t('profile_activity_no_entries', 'No activity') ?>',
            };
            const tooltip = document.createElement('div');
            tooltip.className = 'gh-heatmap-tooltip';
            document.body.appendChild(tooltip);

            function formatDaySummary(dateKey, count) {
                const formattedDate = new Date(`${dateKey}T00:00:00`).toLocaleDateString(activityLocale, {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                return `${formattedDate} В· ${count} ${labels.count}`;
            }

            function hideTooltip() {
                tooltip.classList.remove('is-visible');
            }

            function positionTooltip(anchor) {
                const rect = anchor.getBoundingClientRect();
                const tipRect = tooltip.getBoundingClientRect();
                let left = rect.left + (rect.width / 2) - (tipRect.width / 2);
                let top = rect.top - tipRect.height - 10;
                if (left < 8) left = 8;
                if (left + tipRect.width > window.innerWidth - 8) left = window.innerWidth - tipRect.width - 8;
                if (top < 8) top = rect.bottom + 10;
                tooltip.style.left = `${left}px`;
                tooltip.style.top = `${top}px`;
            }

            function showTooltip(anchor, dateKey, entries) {
                tooltip.innerHTML = '';
                const title = document.createElement('div');
                title.className = 'gh-heatmap-tooltip-title';
                title.textContent = formatDaySummary(dateKey, entries.length);
                tooltip.appendChild(title);
                const list = document.createElement('div');
                list.className = 'gh-heatmap-tooltip-list';
                if (!entries.length) {
                    const empty = document.createElement('div');
                    empty.className = 'gh-heatmap-tooltip-text';
                    empty.textContent = labels.empty;
                    list.appendChild(empty);
                } else {
                    entries.forEach((item) => {
                        const row = document.createElement('div');
                        row.className = 'gh-heatmap-tooltip-item';
                        const time = document.createElement('div');
                        time.className = 'gh-heatmap-tooltip-time';
                        time.textContent = item.time || '';
                        const text = document.createElement('div');
                        text.className = 'gh-heatmap-tooltip-text';
                        text.textContent = item.text || '';
                        row.appendChild(time);
                        row.appendChild(text);
                        list.appendChild(row);
                    });
                }
                tooltip.appendChild(list);
                tooltip.classList.add('is-visible');
                positionTooltip(anchor);
            }

            function renderDetail(block, dateKey, entries) {
                const title = block.querySelector('[data-detail-title]');
                const list = block.querySelector('[data-detail-list]');
                if (!title || !list) return;
                list.innerHTML = '';
                if (!dateKey) {
                    title.textContent = labels.pick;
                    return;
                }
                const count = entries.length;
                const formattedDate = new Date(`${dateKey}T00:00:00`).toLocaleDateString(activityLocale, {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                title.textContent = formatDaySummary(dateKey, count);

                if (!count) {
                    const empty = document.createElement('div');
                    empty.className = 'gh-heatmap-empty';
                    empty.textContent = labels.empty;
                    list.appendChild(empty);
                    return;
                }
                entries.forEach((item) => {
                    const row = document.createElement('div');
                    row.className = 'gh-heatmap-detail-item';
                    const time = document.createElement('div');
                    time.className = 'gh-heatmap-detail-time';
                    time.textContent = item.time || '';
                    const text = document.createElement('div');
                    text.className = 'gh-heatmap-detail-text';
                    text.textContent = item.text || '';
                    row.appendChild(time);
                    row.appendChild(text);
                    list.appendChild(row);
                });
            }

            document.querySelectorAll('.gh-heatmap-block').forEach((block) => {
                const mapKey = block.dataset.heatmap;
                const dataMap = heatmapData[mapKey] || {};
                renderDetail(block, '', []);
                block.querySelectorAll('.gh-heat-cell[data-date]').forEach((cell) => {
                    if (cell.classList.contains('is-outside')) return;
                    cell.addEventListener('click', () => {
                        block.querySelectorAll('.gh-heat-cell.is-active').forEach((active) => {
                            active.classList.remove('is-active');
                        });
                        cell.classList.add('is-active');
                        const dateKey = cell.dataset.date;
                        const entry = dataMap[dateKey] || { items: [] };
                        renderDetail(block, dateKey, entry.items || []);
                    });
                    const hoverHandler = () => {
                        const dateKey = cell.dataset.date;
                        const entry = dataMap[dateKey] || { items: [] };
                        showTooltip(cell, dateKey, entry.items || []);
                    };
                    cell.addEventListener('mouseenter', hoverHandler);
                    cell.addEventListener('focus', hoverHandler);
                    cell.addEventListener('mouseleave', hideTooltip);
                    cell.addEventListener('blur', hideTooltip);
                });
            });
            window.addEventListener('scroll', hideTooltip, true);
            window.addEventListener('resize', hideTooltip);
        })();
    </script>
</body>

</html>