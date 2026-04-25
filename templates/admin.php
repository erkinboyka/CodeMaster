<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$totalUsers = $totalUsers ?? 0;
$totalCourses = $totalCourses ?? 0;
$totalLessons = $totalLessons ?? 0;
$totalVacancies = $totalVacancies ?? 0;
$recentUsers = $recentUsers ?? [];
$recentCourses = $recentCourses ?? [];
$users = $users ?? [];
$courses = $courses ?? [];
$lessons = $lessons ?? [];
$vacancies = $vacancies ?? [];
$notifications = $notifications ?? [];
$courseExams = $courseExams ?? [];
$roadmapNodes = $roadmapNodes ?? [];
$roadmapLessons = $roadmapLessons ?? [];
$roadmapQuizzes = $roadmapQuizzes ?? [];
$roadmapList = $roadmapList ?? [];
$roadmapListForSelect = $roadmapListForSelect ?? $roadmapList;
$roadmapNodesForSelect = $roadmapNodesForSelect ?? $roadmapNodes;
$adminContests = $adminContests ?? [];
$adminContestsForSelect = $adminContestsForSelect ?? $adminContests;
$adminContestTasks = $adminContestTasks ?? [];
$adminContestSubmissionTasksForSelect = $adminContestSubmissionTasksForSelect ?? [];
$adminInterviewPrepTasks = $adminInterviewPrepTasks ?? [];
$practiceTasks = $practiceTasks ?? [];
$recentPracticeSubmissions = $recentPracticeSubmissions ?? [];
$recentContestSubmissions = $recentContestSubmissions ?? [];
$adminPagination = $adminPagination ?? [];
$adminCourseId = max(0, (int) ($_GET['admin_course_id'] ?? 0));
$adminContestId = max(0, (int) ($_GET['admin_contest_id'] ?? 0));
$adminRoadmapTitle = trim((string) ($_GET['admin_roadmap_title'] ?? ''));
$adminRoleCounts = $adminRoleCounts ?? null;
$adminTrendLabels = $adminTrendLabels ?? [];
$adminTrendValues = $adminTrendValues ?? [];
$adminSection = trim((string) ($_GET['section'] ?? ''));
$isCourseLessonsTab = in_array($tab, ['lessons', 'course-lessons'], true);
$isCoursePracticeTab = ($tab === 'course-practice');
$isCourseExamsTab = in_array($tab, ['exams', 'course-exams'], true);
$isContestOverviewTab = ($tab === 'contests');
$isContestTasksTab = ($tab === 'contest-tasks');
$isContestSolutionsTab = ($tab === 'contest-solutions');
$isVacancyPrepTasksTab = ($tab === 'vacancy-prep-tasks');
$isRoadmapOverviewTab = in_array($tab, ['roadmap', 'roadmaps'], true);
$isRoadmapNodesTab = ($tab === 'roadmap-nodes');
$isRoadmapTasksTab = ($tab === 'roadmap-tasks');
$isRoadmapExamsTab = ($tab === 'roadmap-exams');
$currencyLabels = [
    'TJS' => t('currency_tjs'),
    'RUB' => t('currency_rub'),
    'USD' => t('currency_usd')
];
$seenLessonIds = [];
$uniqueLessons = [];
foreach ($lessons as $lesson) {
    $id = $lesson['id'] ?? null;
    if ($id !== null) {
        if (isset($seenLessonIds[$id])) {
            continue;
        }
        $seenLessonIds[$id] = true;
    }
    $uniqueLessons[] = $lesson;
}
$lessons = $uniqueLessons;
$roleCounts = ['admin' => 0, 'recruiter' => 0, 'seeker' => 0];
if (is_array($adminRoleCounts)) {
    foreach (array_keys($roleCounts) as $roleKey) {
        $roleCounts[$roleKey] = (int) ($adminRoleCounts[$roleKey] ?? 0);
    }
} else {
    foreach ($users as $user) {
        $role = $user['role'] ?? 'seeker';
        if (!isset($roleCounts[$role])) {
            $roleCounts['seeker']++;
        } else {
            $roleCounts[$role]++;
        }
    }
}
$roleSum = array_sum($roleCounts);
$roleTotal = $roleSum > 0 ? $roleSum : 1;
$rolePctAdmin = (int) round(($roleCounts['admin'] / $roleTotal) * 100);
$rolePctRecruiter = (int) round(($roleCounts['recruiter'] / $roleTotal) * 100);
$rolePctSeeker = max(0, 100 - $rolePctAdmin - $rolePctRecruiter);

$contentSum = $totalUsers + $totalCourses + $totalLessons + $totalVacancies;
$contentTotal = $contentSum > 0 ? $contentSum : 1;
$mixUsersPct = (int) round(($totalUsers / $contentTotal) * 100);
$mixCoursesPct = (int) round(($totalCourses / $contentTotal) * 100);
$mixLessonsPct = (int) round(($totalLessons / $contentTotal) * 100);
$mixVacanciesPct = max(0, 100 - $mixUsersPct - $mixCoursesPct - $mixLessonsPct);

$trendLabels = [];
$trendValues = [];
if (!empty($adminTrendLabels) && !empty($adminTrendValues)) {
    $trendLabels = array_values($adminTrendLabels);
    $trendValues = array_values($adminTrendValues);
} else {
    $trendWeeks = 8;
    $trendStart = strtotime('monday this week');
    if ($trendStart === false) {
        $trendStart = time();
    }
    $trendStart = strtotime('-' . ($trendWeeks - 1) . ' weeks', $trendStart);
    $trendValues = array_fill(0, $trendWeeks, 0);
    for ($i = 0; $i < $trendWeeks; $i++) {
        $trendLabels[] = date('d.m', strtotime('+' . $i . ' weeks', $trendStart));
    }
    foreach ($users as $user) {
        $created = strtotime($user['created_at'] ?? '');
        if (!$created) {
            continue;
        }
        $diff = $created - $trendStart;
        if ($diff < 0) {
            continue;
        }
        $index = (int) floor($diff / (7 * 86400));
        if ($index >= 0 && $index < $trendWeeks) {
            $trendValues[$index]++;
        }
    }
}
$trendWeeks = max(1, count($trendValues));
$trendMax = max($trendValues);
if ($trendMax < 1) {
    $trendMax = 1;
}
$buildAdminTabUrl = static function (string $targetTab, array $extra = []) use ($adminCourseId, $adminContestId, $adminRoadmapTitle) {
    $params = array_merge([
        'action' => 'admin',
        'tab' => $targetTab,
        'admin_course_id' => $adminCourseId > 0 ? $adminCourseId : null,
        'admin_contest_id' => $adminContestId > 0 ? $adminContestId : null,
        'admin_roadmap_title' => $adminRoadmapTitle !== '' ? $adminRoadmapTitle : null,
    ], $extra);
    $params = array_filter($params, static function ($value) {
        return $value !== null && $value !== '';
    });
    return '?' . http_build_query($params);
};
$trendBase = 54;
$trendPoints = [];
for ($i = 0; $i < $trendWeeks; $i++) {
    $x = $trendWeeks === 1 ? 0 : ($i / ($trendWeeks - 1)) * 100;
    $y = $trendBase - (($trendValues[$i] / $trendMax) * 38);
    $trendPoints[] = number_format($x, 2, '.', '') . ',' . number_format($y, 2, '.', '');
}
$trendPolyline = implode(' ', $trendPoints);
$trendArea = 'M0,' . $trendBase . ' L' . implode(' L', $trendPoints) . ' L100,' . $trendBase . ' Z';
$trendPeak = max($trendValues);
$trendLastPoint = $trendPoints[$trendWeeks - 1] ?? ('0,' . $trendBase);
$trendLastParts = explode(',', $trendLastPoint);
$trendLastX = $trendLastParts[0] ?? 0;
$trendLastY = $trendLastParts[1] ?? $trendBase;

$renderAdminPager = static function (string $key) use ($adminPagination) {
    $pager = $adminPagination[$key] ?? null;
    if (!is_array($pager)) {
        return;
    }
    $total = (int) ($pager['total'] ?? 0);
    $page = (int) ($pager['page'] ?? 1);
    $totalPages = (int) ($pager['total_pages'] ?? 1);
    $param = (string) ($pager['param'] ?? '');
    if ($total <= 0 || $totalPages <= 1 || $param === '') {
        return;
    }
    $queryBase = $_GET;
    unset($queryBase[$param]);
    $makeHref = static function (int $target) use ($queryBase, $param): string {
        $query = $queryBase;
        $query[$param] = max(1, $target);
        return '?' . http_build_query($query);
    };
    ?>
    <div class="px-5 py-4 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3 admin-table-pagination" data-server-pager="<?= htmlspecialchars($key, ENT_QUOTES) ?>">
        <div class="text-sm text-gray-500">Страница <?= $page ?> из <?= $totalPages ?> (всего: <?= $total ?>)</div>
        <div class="flex items-center gap-2">
            <?php if ($page > 1): ?>
<a href="<?= htmlspecialchars($makeHref($page - 1)) ?>" class="btn-secondary px-3 py-2 text-sm">Назад</a>            <?php else: ?>
<span class="btn-secondary px-3 py-2 text-sm opacity-50 cursor-not-allowed">Назад</span>            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
<a href="<?= htmlspecialchars($makeHref($page + 1)) ?>" class="btn-secondary px-3 py-2 text-sm">Вперёд</a>            <?php else: ?>
<span class="btn-secondary px-3 py-2 text-sm opacity-50 cursor-not-allowed">Вперёд</span>            <?php endif; ?>
        </div>
    </div>
    <?php
};
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('admin_page_title') ?></title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.0/dist/cdn.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f5ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }

        #lessons-table {
            table-layout: fixed;
            width: 100%;
        }

        #lessons-table th:nth-child(1),
        #lessons-table td:nth-child(1) {
            width: 5%;
        }

        #lessons-table th:nth-child(2),
        #lessons-table td:nth-child(2) {
            width: 25%;
        }

        #lessons-table th:nth-child(3),
        #lessons-table td:nth-child(3) {
            width: 20%;
        }

        #lessons-table th:nth-child(4),
        #lessons-table td:nth-child(4) {
            width: 10%;
        }

        #lessons-table th:nth-child(5),
        #lessons-table td:nth-child(5) {
            width: 10%;
        }

        #lessons-table th:nth-child(6),
        #lessons-table td:nth-child(6) {
            width: 10%;
        }

        #lessons-table th:nth-child(7),
        #lessons-table td:nth-child(7) {
            width: 20%;
        }

        .card {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e2e8f0;
            min-width: 0;
        }

        .card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.15);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(79, 70, 229, 0.25);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #334155;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
            border-color: #cbd5e1;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .input-field {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: #1e293b;
            width: 100%;
            transition: all 0.2s ease;
            background-color: #f8fafc;
        }

        .input-field:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            background-color: white;
        }

        .input-field:hover:not(:focus) {
            border-color: #94a3b8;
        }

        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 12px;
            margin: -1px;
        }

        .table-container::-webkit-scrollbar {
            height: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 800px;
        }

        th,
        td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
        }

        th {
            background-color: #f8fafc;
            font-weight: 700;
            color: #475569;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #f8fafc;
        }

        tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        tr:nth-child(even):hover td {
            background-color: #f1f5f9;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-primary {
            background-color: #eef2ff;
            color: #4338ca;
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            overflow-y: auto;
            padding: 1rem;
        }

        .modal.active {
            display: block;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 0;
        }

        .modal-content {
            position: relative;
            z-index: 1;
            background: white;
            border-radius: 20px;
            max-width: 95%;
            margin: 2rem auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalSlideIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            max-height: calc(100vh - 4rem);
            overflow-y: auto;
        }

        .modal-sheet {
            max-height: calc(100vh - 5rem);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon-primary {
            background-color: #eef2ff;
            color: #4338ca;
        }

        .stat-icon-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .stat-icon-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .stat-icon-purple {
            background-color: #f5f3ff;
            color: #6d28d9;
        }

        .admin-chart-shell {
            position: relative;
            border-radius: 18px;
            padding: 1.1rem;
            background: linear-gradient(135deg, #eef2ff 0%, #ffffff 55%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .admin-chart-shell::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 15% 20%, rgba(99, 102, 241, 0.12), transparent 55%),
                radial-gradient(circle at 85% 10%, rgba(34, 197, 94, 0.12), transparent 50%);
            opacity: 0.8;
            pointer-events: none;
        }

        .admin-line-chart {
            width: 100%;
            height: 180px;
            position: relative;
            z-index: 1;
        }

        .admin-chart-area {
            fill: url(#adminTrendGradient);
        }

        .admin-chart-line {
            stroke: #6366f1;
            stroke-width: 2.6;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 320;
            stroke-dashoffset: 320;
            animation: admin-line-draw 1.3s ease forwards;
        }

        .admin-chart-dot {
            fill: #4f46e5;
            filter: drop-shadow(0 6px 10px rgba(79, 70, 229, 0.35));
        }

        .admin-chart-grid {
            display: grid;
            grid-template-columns: repeat(8, minmax(0, 1fr));
            gap: 4px;
            margin-top: 0.75rem;
            font-size: 0.7rem;
            color: #94a3b8;
            position: relative;
            z-index: 1;
        }

        .admin-mix-bar {
            display: flex;
            height: 12px;
            border-radius: 999px;
            overflow: hidden;
            background: #e2e8f0;
        }

        .admin-mix-seg {
            height: 100%;
            transition: width 0.6s ease;
        }

        .admin-mix-users {
            background: linear-gradient(90deg, #60a5fa, #2563eb);
        }

        .admin-mix-courses {
            background: linear-gradient(90deg, #34d399, #10b981);
        }

        .admin-mix-lessons {
            background: linear-gradient(90deg, #fbbf24, #f97316);
        }

        .admin-mix-vacancies {
            background: linear-gradient(90deg, #c4b5fd, #8b5cf6);
        }

        .admin-legend-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            font-size: 0.85rem;
            color: #475569;
        }

        .admin-legend-chip {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .admin-legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
        }

        .admin-donut {
            width: 170px;
            height: 170px;
            border-radius: 999px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: conic-gradient(#f97316 0 calc(var(--a) * 1%), #38bdf8 calc(var(--a) * 1%) calc((var(--a) + var(--b)) * 1%), #22c55e calc((var(--a) + var(--b)) * 1%) calc((var(--a) + var(--b) + var(--c)) * 1%));
            position: relative;
        }

        .admin-donut::before {
            content: "";
            position: absolute;
            inset: 14px;
            border-radius: 999px;
            background: #ffffff;
            box-shadow: inset 0 0 0 1px #e2e8f0;
        }

        .admin-donut-center {
            position: relative;
            text-align: center;
            z-index: 1;
        }

        @keyframes admin-line-draw {
            to {
                stroke-dashoffset: 0;
            }
        }

        @media (max-width: 768px) {
            .mobile-hidden {
                display: none !important;
            }

            .mobile-block {
                display: block !important;
            }

            .mobile-flex {
                display: flex !important;
            }

            .mobile-grid {
                display: grid !important;
            }

            th,
            td {
                padding: 0.75rem;
                font-size: 0.8125rem;
            }

            .stat-card {
                padding: 1.25rem;
            }

            .admin-line-chart {
                height: 150px;
            }

            .admin-donut {
                width: 140px;
                height: 140px;
            }

            .admin-chart-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                font-size: 0.65rem;
            }

            .btn-primary,
            .btn-secondary {
                padding: 0.625rem 1.25rem;
                font-size: 0.8125rem;
            }

            .modal-content {
                margin: 1rem auto;
                border-radius: 16px;
            }

            table {
                min-width: 100%;
            }

            .grid-cols-mobile {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 480px) {
            .mobile-xs-hidden {
                display: none !important;
            }

            table {
                min-width: 100%;
            }

            .grid-cols-mobile {
                grid-template-columns: 1fr !important;
            }

            .px-mobile {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }

            .py-mobile {
                padding-top: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                position: sticky;
                top: 0.75rem;
                z-index: 30;
            }

            .sidebar-link {
                justify-content: flex-start;
                gap: 0.6rem;
                padding: 0.6rem 0.85rem;
                font-size: 0.8rem;
            }

            .sidebar-link i {
                margin: 0;
                width: 20px;
                text-align: center;
            }

            .admin-sidebar nav {
                display: flex;
                flex-direction: column;
                gap: 0.35rem;
            }

            .admin-sidebar .sidebar-header {
                padding-bottom: 0.75rem;
                margin-bottom: 0.75rem;
            }

            .admin-sidebar .sidebar-header h3 {
                font-size: 0.95rem;
            }
        }

        .admin-menu-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 0.85rem;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #1f2937;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.08);
        }

        .admin-sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.35);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease;
            z-index: 50;
        }

        .admin-mobile-table {
            display: block;
        }

        .admin-mobile-cards {
            display: none;
        }

        .admin-card {
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 14px;
            padding: 1rem;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        }

        .admin-card-title {
            font-weight: 600;
            color: #0f172a;
            font-size: 0.95rem;
        }

        .admin-card-meta {
            font-size: 0.8125rem;
            color: #64748b;
        }

        .admin-sidebar {
            position: sticky;
            top: calc(var(--tf-header-offset, 64px) + 1rem);
            align-self: flex-start;
            max-height: calc(100vh - var(--tf-header-offset, 64px) - 2rem);
            overflow: auto;
        }

        @media (max-width: 1024px) {
            .admin-menu-toggle {
                display: inline-flex;
            }

            .admin-sidebar {
                position: fixed;
                top: calc(var(--tf-header-offset, 64px) + 0.5rem);
                bottom: 0;
                left: 0;
                width: 260px;
                transform: translateX(-110%);
                transition: transform 0.25s ease;
                z-index: 60;
                padding: 0.75rem;
                max-height: calc(100vh - var(--tf-header-offset, 64px) - 1rem);
            }

            .admin-sidebar .card {
                max-height: calc(100vh - var(--tf-header-offset, 64px) - 1.5rem);
                overflow-y: auto;
            }

            body.admin-sidebar-open .admin-sidebar {
                transform: translateX(0);
            }

            body.admin-sidebar-open .admin-sidebar-overlay {
                opacity: 1;
                visibility: visible;
            }

            body.admin-sidebar-open {
                overflow: hidden;
            }
        }

        @media (max-width: 768px) {
            .admin-mobile-table {
                display: none;
            }

            .admin-mobile-cards {
                display: grid;
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            text-decoration: none;
            color: #475569;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 0.5rem;
        }

        .sidebar-link:hover {
            background-color: #f1f5f9;
            color: #4338ca;
            transform: translateX(4px);
        }

        .sidebar-link.active {
            background-color: #eef2ff;
            color: #4338ca;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.1);
        }

        .sidebar-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-submenu {
            margin: -0.2rem 0 0.6rem 2.15rem;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .sidebar-sub-link {
            display: inline-flex;
            align-items: center;
            font-size: 0.76rem;
            color: #64748b;
            text-decoration: none;
            padding: 0.2rem 0;
        }

        .sidebar-sub-link:hover {
            color: #4338ca;
        }

        .sidebar-sub-link.active {
            color: #4338ca;
            font-weight: 600;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            background: none;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .action-btn-edit {
            color: #4338ca;
            background-color: #eef2ff;
        }

        .action-btn-edit:hover {
            background-color: #dbeafe;
        }

        .action-btn-delete {
            color: #dc2626;
            background-color: #fee2e2;
        }

        .action-btn-delete:hover {
            background-color: #fecaca;
        }

        .action-btn-toggle {
            color: #059669;
            background-color: #dcfce7;
        }

        .action-btn-toggle:hover {
            background-color: #bbf7d0;
        }

        .action-btn-exam {
            color: #0ea5e9;
            background-color: #dbeafe;
        }

        .action-btn-exam:hover {
            background-color: #bfdbfe;
        }

        .select-field {
            border: 1px solid #cbd5e1;
            border-radius: 18px;
            padding: 0.82rem 1.1rem;
            font-size: 0.875rem;
            color: #1e293b;
            width: 100%;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            transition: all 0.2s ease;
            font-weight: 500;
            appearance: none;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        }

        .select-field:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            background-color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #64748b;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 0.875rem;
        }

        .responsive-table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 12px;
            margin: 1px 0;
            max-width: 100%;
            overscroll-behavior-x: contain;
        }

        .dashboard-table {
            min-width: 100%;
            table-layout: auto;
        }

        .dashboard-table td,
        .dashboard-table th {
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.875rem;
        }

        .breadcrumb i {
            font-size: 0.75rem;
        }

        .breadcrumb a {
            color: #4338ca;
            text-decoration: none;
            font-weight: 600;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .tab-nav {
            display: flex;
            gap: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 1.5rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }

        .tab-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            background: none;
            color: #64748b;
            font-weight: 500;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tab-btn:hover {
            color: #4338ca;
            background-color: #f8fafc;
        }

        .tab-btn.active {
            color: #4338ca;
            background-color: #eef2ff;
            font-weight: 600;
            box-shadow: 0 -2px 0 #4f46e5 inset;
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .search-box input {
            padding-left: 2.5rem;
            height: 40px;
            line-height: 1.2;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
            margin: 1.5rem 0;
        }

        .text-truncate {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 1024px) {
            .text-truncate {
                max-width: 150px;
            }
        }

        @media (max-width: 768px) {
            .text-truncate {
                max-width: 120px;
            }

            .dashboard-table {
                min-width: 100%;
            }
        }

        .fade-in {
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-delay {
            animation: fadeIn 0.4s ease 0.1s;
            animation-fill-mode: backwards;
        }

        .fade-in-delay-2 {
            animation: fadeIn 0.4s ease 0.2s;
            animation-fill-mode: backwards;
        }

        .fade-in-delay-3 {
            animation: fadeIn 0.4s ease 0.3s;
            animation-fill-mode: backwards;
        }

        .loading-spinner {
            width: 24px;
            height: 24px;
            border: 3px solid #e2e8f0;
            border-top-color: #4f46e5;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 0%, #e2e8f0 50%, #f1f5f9 100%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s ease-in-out infinite;
            border-radius: 8px;
        }

        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .admin-table-tools {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .admin-table-search {
            position: relative;
            flex: 1 1 260px;
            max-width: 420px;
            display: flex;
            align-items: center;
        }

        .admin-table-search i {
            position: absolute;
            left: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            font-size: 0.9rem;
        }

        .admin-table-search input {
            width: 100%;
            border: 1px solid #cbd5e1;
            background-color: #fff;
            border-radius: 10px;
            padding: 0.55rem 0.75rem 0.55rem 2.25rem;
            font-size: 0.875rem;
            color: #1e293b;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            height: 40px;
            line-height: 1.2;
        }

        .admin-table-search input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .admin-table-meta {
            color: #64748b;
            font-size: 0.8125rem;
            white-space: nowrap;
        }

        .admin-table-pagination {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 0.75rem;
        }

        .admin-table-page-btn {
            border: 1px solid #cbd5e1;
            background-color: #fff;
            color: #334155;
            border-radius: 8px;
            padding: 0.35rem 0.7rem;
            font-size: 0.8125rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .admin-table-page-btn:hover:not(:disabled) {
            border-color: #94a3b8;
            background-color: #f8fafc;
        }

        .admin-table-page-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .admin-table-page-info {
            color: #475569;
            font-size: 0.8125rem;
            min-width: 90px;
            text-align: center;
        }

        @media (max-width: 640px) {
            .admin-table-tools {
                flex-direction: column;
                align-items: stretch;
            }

            .admin-table-search {
                max-width: none;
            }

            .admin-table-pagination {
                justify-content: space-between;
            }
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
    </style>
</head>

<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>
    <div id="admin-sidebar-overlay" class="admin-sidebar-overlay"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style="margin-top: 20px;">
        <div class="flex items-center justify-between mb-4 lg:hidden">
            <button id="admin-menu-toggle" class="admin-menu-toggle" type="button">
                <i class="fas fa-bars"></i>
<?= t('admin_menu', 'Меню') ?>            </button>
            <span class="text-xs text-gray-500"><?= t('admin_title') ?></span>
        </div>
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Sidebar -->
            <div class="lg:w-64 flex-shrink-0 admin-sidebar">
                <div class="card sticky top-6">
                    <div class="p-5">
                        <div class="flex items-center mb-6 pb-4 border-b border-gray-200 sidebar-header">
                            <h3 class="font-bold text-gray-900 text-lg"><?= t('admin_title') ?></h3>
                        </div>

                        <nav class="space-y-1">
                            <a href="?action=admin&tab=dashboard"
                                class="sidebar-link <?= $tab === 'dashboard' ? 'active' : '' ?>">
                                <i class="fas fa-tachometer-alt"></i>
                                <span><?= t('admin_dashboard') ?></span>
                            </a>
                            <a href="?action=admin&tab=users"
                                class="sidebar-link <?= $tab === 'users' ? 'active' : '' ?>">
                                <i class="fas fa-users"></i>
                                <span><?= t('admin_users') ?></span>
                            </a>
                            <a href="?action=admin&tab=courses"
                                class="sidebar-link <?= $tab === 'courses' ? 'active' : '' ?>">
                                <i class="fas fa-graduation-cap"></i>
                                <span><?= t('admin_courses') ?></span>
                            </a>
                            <a href="?action=admin&tab=course-lessons"
                                class="sidebar-link <?= $isCourseLessonsTab ? 'active' : '' ?>">
                                <i class="fas fa-book-open"></i>
                                <span><?= t('admin_lessons', 'Уроки курсов') ?></span>
                            </a>
                            <a href="?action=admin&tab=course-practice"
                                class="sidebar-link <?= $isCoursePracticeTab ? 'active' : '' ?>">
                                <i class="fas fa-laptop-code"></i>
                                <span><?= t('admin_course_practice_tasks', 'Практические задания курсов') ?></span>
                            </a>
                            <a href="?action=admin&tab=course-exams"
                                class="sidebar-link <?= $isCourseExamsTab ? 'active' : '' ?>">
                                <i class="fas fa-clipboard-check"></i>
                                <span><?= t('admin_course_exams', 'Экзамены курсов') ?></span>
                            </a>
                            <a href="?action=admin&tab=roadmaps"
                                class="sidebar-link <?= $isRoadmapOverviewTab ? 'active' : '' ?>">
                                <i class="fas fa-project-diagram"></i>
                                <span><?= t('admin_roadmaps', 'Роадмапы') ?></span>
                            </a>
                            <a href="?action=admin&tab=roadmap-nodes"
                                class="sidebar-link <?= $isRoadmapNodesTab ? 'active' : '' ?>">
                                <i class="fas fa-sitemap"></i>
                                <span><?= t('admin_nodes', 'Ноды роадмапов') ?></span>
                            </a>
                            <a href="?action=admin&tab=roadmap-tasks"
                                class="sidebar-link <?= $isRoadmapTasksTab ? 'active' : '' ?>">
                                <i class="fas fa-tasks"></i>
                                <span><?= t('admin_roadmap_tasks', 'Задания роадмапов') ?></span>
                            </a>
                            <a href="?action=admin&tab=roadmap-exams"
                                class="sidebar-link <?= $isRoadmapExamsTab ? 'active' : '' ?>">
                                <i class="fas fa-list-check"></i>
                                <span><?= t('admin_roadmap_exams', 'Экзамены роадмапов') ?></span>
                            </a>
                            <a href="?action=admin&tab=contests"
                                class="sidebar-link <?= $isContestOverviewTab ? 'active' : '' ?>">
                                <i class="fas fa-code"></i>
                                <span><?= t('admin_contests', 'Контесты') ?></span>
                            </a>
                            <a href="?action=admin&tab=contest-tasks"
                                class="sidebar-link <?= $isContestTasksTab ? 'active' : '' ?>">
                                <i class="fas fa-file-code"></i>
                                <span><?= t('admin_contest_tasks', 'Задачи контестов') ?></span>
                            </a>
                            <a href="?action=admin&tab=contest-solutions"
                                class="sidebar-link <?= $isContestSolutionsTab ? 'active' : '' ?>">
                                <i class="fas fa-square-check"></i>
                                <span><?= t('admin_contest_solutions', 'Решённые задачи') ?></span>
                            </a>
                            <a href="?action=admin&tab=vacancy-prep-tasks"
                                class="sidebar-link <?= $isVacancyPrepTasksTab ? 'active' : '' ?>">
                                <i class="fas fa-user-graduate"></i>
                                <span><?= t('admin_interview_prep', 'Задачи подготовки к вакансиям') ?></span>
                            </a>
                            <a href="?action=admin&tab=vacancies"
                                class="sidebar-link <?= $tab === 'vacancies' ? 'active' : '' ?>">
                                <i class="fas fa-briefcase"></i>
                                <span><?= t('admin_vacancies') ?></span>
                            </a>
                            <a href="?action=admin&tab=notifications"
                                class="sidebar-link <?= $tab === 'notifications' ? 'active' : '' ?>">
                                <i class="fas fa-bell"></i>
                                <span><?= t('admin_notifications') ?></span>
                            </a>
                            <a href="?action=admin&tab=ejudge-import"
                                class="sidebar-link <?= $tab === 'ejudge-import' ? 'active' : '' ?>">
                                <i class="fas fa-file-import"></i>
                                <span><?= t('admin_ejudge_import', 'Импорт Ejudge') ?></span>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 min-w-0">
                <?php if ($tab === 'dashboard'): ?>
                    <!-- Dashboard Tab -->
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <div>
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                    <i class="fas fa-tachometer-alt mr-3 text-primary-600"></i>
                                    <?= t('admin_dashboard') ?>
                                </h1>
                                <div class="text-sm text-gray-500 mt-1 flex items-center">
                                    <i class="far fa-clock mr-2"></i>
                                    <?= t('admin_last_update') ?>: <?= date('d.m.Y H:i') ?>
                                </div>
                            </div>
                            <button onclick="seedLearningPack()" class="btn-primary">
                                <i class="fas fa-box-open mr-2"></i>
                                <?= t('admin_upload_materials_pack') ?>
                            </button>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 fade-in">
                            <div class="stat-card">
                                <div class="flex items-center">
                                    <div class="stat-icon stat-icon-primary mr-4">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-sm font-medium"><?= t('admin_total_users') ?></p>
                                        <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-1"><?= $totalUsers ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="flex items-center">
                                    <div class="stat-icon stat-icon-success mr-4">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-sm font-medium"><?= t('admin_total_courses') ?></p>
                                        <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-1"><?= $totalCourses ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="flex items-center">
                                    <div class="stat-icon stat-icon-info mr-4">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-sm font-medium"><?= t('admin_total_lessons') ?></p>
                                        <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-1"><?= $totalLessons ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="flex items-center">
                                    <div class="stat-icon stat-icon-purple mr-4">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-sm font-medium"><?= t('admin_total_vacancies') ?></p>
                                        <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-1"><?= $totalVacancies ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics -->
                        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                            <div class="card p-5 xl:col-span-2">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div>
                                        <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                            <i class="fas fa-chart-area mr-2 text-indigo-600"></i>
<?= t('admin_platform_pulse', 'Пульс платформы') ?>                                        </h2>
                                        <p class="text-xs text-gray-500 mt-1">
<?= t('admin_last_weeks', 'Последние 8 недель') ?>                                            <?= $trendPeak ?>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <span class="inline-flex items-center gap-1">
                                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
<?= t('admin_new_users', 'Новые пользователи') ?>                                        </span>
                                    </div>
                                </div>
                                <div class="mt-4 admin-chart-shell">
                                    <svg class="admin-line-chart" viewBox="0 0 100 60" preserveAspectRatio="none"
                                        role="img"
                                        aria-label="<?= t('admin_new_users_chart', 'График новых пользователей') ?>">
                                        <defs>
                                            <linearGradient id="adminTrendGradient" x1="0" x2="0" y1="0" y2="1">
                                                <stop offset="0%" stop-color="#6366f1" stop-opacity="0.35" />
                                                <stop offset="100%" stop-color="#6366f1" stop-opacity="0" />
                                            </linearGradient>
                                        </defs>
                                        <line x1="0" y1="<?= $trendBase ?>" x2="100" y2="<?= $trendBase ?>" stroke="#e2e8f0"
                                            stroke-width="0.8" />
                                        <line x1="0" y1="<?= $trendBase - 12 ?>" x2="100" y2="<?= $trendBase - 12 ?>"
                                            stroke="#e2e8f0" stroke-width="0.6" />
                                        <line x1="0" y1="<?= $trendBase - 24 ?>" x2="100" y2="<?= $trendBase - 24 ?>"
                                            stroke="#e2e8f0" stroke-width="0.6" />
                                        <line x1="0" y1="<?= $trendBase - 36 ?>" x2="100" y2="<?= $trendBase - 36 ?>"
                                            stroke="#e2e8f0" stroke-width="0.6" />
                                        <path class="admin-chart-area" d="<?= $trendArea ?>"></path>
                                        <polyline class="admin-chart-line" points="<?= $trendPolyline ?>"></polyline>
                                        <circle class="admin-chart-dot" cx="<?= $trendLastX ?>" cy="<?= $trendLastY ?>" r="2.8">
                                        </circle>
                                    </svg>
                                    <div class="admin-chart-grid">
                                        <?php foreach ($trendLabels as $label): ?>
                                            <div class="text-center"><?= htmlspecialchars($label) ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card p-5">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                        <i class="fas fa-user-friends mr-2 text-emerald-600"></i>
<?= t('admin_role_mix', 'Роли пользователей') ?>                                    </h2>
                                    <span class="text-xs text-gray-500"><?= t('admin_total', 'Итого') ?>: <?= $roleSum ?></span>
                                </div>
                                <div class="mt-4">
                                    <div class="admin-donut"
                                        style="--a: <?= $rolePctAdmin ?>; --b: <?= $rolePctRecruiter ?>; --c: <?= $rolePctSeeker ?>;">
                                        <div class="admin-donut-center">
                                            <div class="text-xs text-gray-500"><?= t('admin_users', 'Пользователи') ?></div>
                                            <div class="text-2xl font-bold text-gray-900"><?= $roleSum ?></div>
                                        </div>
                                    </div>
                                    <div class="mt-4 space-y-2">
                                        <div class="admin-legend-row">
                                            <div class="admin-legend-chip">
                                                <span class="admin-legend-dot" style="background:#f97316"></span>
                                                <span><?= t('role_admin') ?></span>
                                            </div>
                                            <span class="font-semibold text-gray-900"><?= $roleCounts['admin'] ?></span>
                                        </div>
                                        <div class="admin-legend-row">
                                            <div class="admin-legend-chip">
                                                <span class="admin-legend-dot" style="background:#38bdf8"></span>
                                                <span><?= t('role_recruiter') ?></span>
                                            </div>
                                            <span class="font-semibold text-gray-900"><?= $roleCounts['recruiter'] ?></span>
                                        </div>
                                        <div class="admin-legend-row">
                                            <div class="admin-legend-chip">
                                                <span class="admin-legend-dot" style="background:#22c55e"></span>
                                                <span><?= t('role_seeker') ?></span>
                                            </div>
                                            <span class="font-semibold text-gray-900"><?= $roleCounts['seeker'] ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card p-5 xl:col-span-3">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                        <i class="fas fa-layer-group mr-2 text-purple-600"></i>
<?= t('admin_content_mix', 'Состав платформы') ?>                                    </h2>
                                    <span class="text-xs text-gray-500"><?= t('admin_total', 'Итого') ?>: <?= $contentSum ?></span>
                                </div>
                                <div class="mt-4">
                                    <div class="admin-mix-bar">
                                        <span class="admin-mix-seg admin-mix-users" style="width: <?= $mixUsersPct ?>%"></span>
                                        <span class="admin-mix-seg admin-mix-courses" style="width: <?= $mixCoursesPct ?>%"></span>
                                        <span class="admin-mix-seg admin-mix-lessons" style="width: <?= $mixLessonsPct ?>%"></span>
                                        <span class="admin-mix-seg admin-mix-vacancies"
                                            style="width: <?= $mixVacanciesPct ?>%"></span>
                                    </div>
                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div class="admin-legend-row">
                                            <div class="admin-legend-chip">
                                                <span class="admin-legend-dot admin-mix-users"></span>
                                                <span><?= t('admin_total_users') ?></span>
                                            </div>
                                            <span class="font-semibold text-gray-900"><?= $totalUsers ?></span>
                                        </div>
                                        <div class="admin-legend-row">
                                            <div class="admin-legend-chip">
                                                <span class="admin-legend-dot admin-mix-courses"></span>
                                                <span><?= t('admin_total_courses') ?></span>
                                            </div>
                                            <span class="font-semibold text-gray-900"><?= $totalCourses ?></span>
                                        </div>
                                        <div class="admin-legend-row">
                                            <div class="admin-legend-chip">
                                                <span class="admin-legend-dot admin-mix-lessons"></span>
                                                <span><?= t('admin_total_lessons') ?></span>
                                            </div>
                                            <span class="font-semibold text-gray-900"><?= $totalLessons ?></span>
                                        </div>
                                        <div class="admin-legend-row">
                                            <div class="admin-legend-chip">
                                                <span class="admin-legend-dot admin-mix-vacancies"></span>
                                                <span><?= t('admin_total_vacancies') ?></span>
                                            </div>
                                            <span class="font-semibold text-gray-900"><?= $totalVacancies ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Users -->
                        <div class="card fade-in-delay">
                            <div class="px-5 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                    <i class="fas fa-users mr-2 text-primary-600"></i>
                                    <?= t('admin_recent_users') ?>
                                </h2>
                            </div>
                            <div class="responsive-table-wrapper">
                                <table class="dashboard-table">
                                    <thead>
                                        <tr>
                                            <th><?= t('admin_name') ?></th>
                                            <th class="mobile-hidden">Email</th>
                                            <th><?= t('admin_role') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_date') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentUsers as $user): ?>
                                            <tr>
                                                <td>
                                                    <div class="flex items-center">
                                                        <img class="h-10 w-10 rounded-full object-cover"
                                                            src="<?= htmlspecialchars($user['avatar']) ?>" alt="" />
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                <?= htmlspecialchars($user['name']) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= htmlspecialchars($user['email']) ?>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge <?= $user['role'] === 'admin' ? 'badge-danger' : ($user['role'] === 'recruiter' ? 'badge-info' : 'badge-success') ?>">
                                                        <?= $user['role'] === 'admin' ? t('role_admin') : ($user['role'] === 'recruiter' ? t('role_recruiter') : t('role_seeker')) ?>
                                                    </span>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= formatDate($user['created_at']) ?>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <button onclick="editUser(<?= $user['id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button
                                                            onclick="toggleUserBlock(<?= $user['id'] ?>, <?= $user['is_blocked'] ? 'false' : 'true' ?>)"
                                                            class="action-btn <?= $user['is_blocked'] ? 'action-btn-toggle' : 'action-btn-delete' ?>"
                                                            title="<?= $user['is_blocked'] ? t('admin_unblock') : t('admin_block') ?>">
                                                            <i
                                                                class="fas fa-<?= $user['is_blocked'] ? 'unlock' : 'lock' ?>"></i>
                                                        </button>
                                                        <button onclick="deleteUser(<?= $user['id'] ?>)"
                                                            class="action-btn action-btn-delete mobile-hidden"
                                                            title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Recent Courses -->
                        <div class="card fade-in-delay-2">
                            <div class="px-5 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                    <i class="fas fa-graduation-cap mr-2 text-green-600"></i>
                                    <?= t('admin_recent_courses') ?>
                                </h2>
                            </div>
                            <div class="responsive-table-wrapper">
                                <table class="dashboard-table">
                                    <thead>
                                        <tr>
                                            <th><?= t('admin_title_label') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_roadmap') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_instructor') ?></th>
                                            <th><?= t('admin_category') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_level') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentCourses as $course): ?>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                                    <?= htmlspecialchars($course['title']) ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= htmlspecialchars($course['roadmap_title'] ?? $course['roadmap'] ?? '-') ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= htmlspecialchars($course['instructor']) ?>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-primary"><?= htmlspecialchars($course['category']) ?></span>
                                                </td>
                                                <td class="mobile-hidden">
                                                    <span
                                                        class="badge badge-success"><?= htmlspecialchars($course['level']) ?></span>
                                                </td>
                                                <td class="mobile-hidden">
                                                    <div class="flex items-center gap-1">
                                                        <button onclick="editCourse(<?= $course['id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="openCourseExamModal(<?= $course['id'] ?>)"
                                                            class="action-btn action-btn-exam" title="<?= t('admin_exam') ?>">
                                                            <i class="fas fa-clipboard-check"></i>
                                                        </button>
                                                        <button onclick="deleteCourse(<?= $course['id'] ?>)"
                                                            class="action-btn action-btn-delete"
                                                            title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                <?php elseif ($tab === 'users'): ?>
                    <!-- Users Tab -->
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-users mr-3 text-primary-600"></i>
                                <?= t('admin_manage_users') ?>
                            </h1>
                            <button onclick="openAddUserModal()" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>
                                <?= t('admin_add_user') ?>
                            </button>
                        </div>

                        <div class="card">
                            <div class="responsive-table-wrapper admin-mobile-table">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_name') ?></th>
                                            <th class="mobile-hidden">Email</th>
                                            <th><?= t('admin_role') ?></th>
                                            <th><?= t('admin_status') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_date') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $index => $user): ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td>
                                                    <div class="flex items-center">
                                                        <img class="h-10 w-10 rounded-full object-cover"
                                                            src="<?= htmlspecialchars($user['avatar']) ?>" alt="" />
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                <?= htmlspecialchars($user['name']) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= htmlspecialchars($user['email']) ?>
                                                </td>
                                                <td>
                                                    <select onchange="updateUserRole(<?= $user['id'] ?>, this.value)"
                                                        class="select-field text-xs <?= $user['role'] === 'admin' ? 'text-red-600' : ($user['role'] === 'recruiter' ? 'text-blue-600' : 'text-green-600') ?>">
                                                        <option value="seeker" <?= $user['role'] === 'seeker' ? 'selected' : '' ?>>
                                                            <?= t('role_seeker') ?>
                                                        </option>
                                                        <option value="recruiter" <?= $user['role'] === 'recruiter' ? 'selected' : '' ?>><?= t('role_recruiter') ?></option>
                                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
                                                            <?= t('role_admin') ?>
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge <?= $user['is_blocked'] ? 'badge-danger' : 'badge-success' ?>">
                                                        <?= $user['is_blocked'] ? t('admin_blocked') : t('admin_active') ?>
                                                    </span>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= formatDate($user['created_at']) ?>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <button onclick="editUser(<?= $user['id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="resetUserContests(<?= $user['id'] ?>)"
                                                            class="action-btn action-btn-exam" title="<?= t('admin_reset_solved_tasks', 'Сбросить посылки и прогресс') ?>">
                                                            <i class="fas fa-rotate-left"></i>
                                                        </button>
                                                        <button
                                                            onclick="toggleUserBlock(<?= $user['id'] ?>, <?= $user['is_blocked'] ? 'false' : 'true' ?>)"
                                                            class="action-btn <?= $user['is_blocked'] ? 'action-btn-toggle' : 'action-btn-delete' ?>"
                                                            title="<?= $user['is_blocked'] ? t('admin_unblock') : t('admin_block') ?>">
                                                            <i
                                                                class="fas fa-<?= $user['is_blocked'] ? 'unlock' : 'lock' ?>"></i>
                                                        </button>
                                                        <button onclick="deleteUser(<?= $user['id'] ?>)"
                                                            class="action-btn action-btn-delete" title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('users'); ?>
                            <div class="admin-mobile-cards">
                                <?php foreach ($users as $index => $user): ?>
                                    <div class="admin-card">
                                        <div class="flex items-center gap-3">
                                            <img class="h-11 w-11 rounded-full object-cover"
                                                src="<?= htmlspecialchars($user['avatar']) ?>" alt="" />
                                            <div>
                                                <div class="admin-card-title"><?= htmlspecialchars($user['name']) ?></div>
                                                <div class="admin-card-meta"><?= htmlspecialchars($user['email']) ?></div>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex flex-wrap items-center gap-2">
                                            <select onchange="updateUserRole(<?= $user['id'] ?>, this.value)"
                                                class="select-field text-xs <?= $user['role'] === 'admin' ? 'text-red-600' : ($user['role'] === 'recruiter' ? 'text-blue-600' : 'text-green-600') ?>">
                                                <option value="seeker" <?= $user['role'] === 'seeker' ? 'selected' : '' ?>>
                                                    <?= t('role_seeker') ?>
                                                </option>
                                                <option value="recruiter" <?= $user['role'] === 'recruiter' ? 'selected' : '' ?>>
                                                    <?= t('role_recruiter') ?>
                                                </option>
                                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
                                                    <?= t('role_admin') ?>
                                                </option>
                                            </select>
                                            <span class="badge <?= $user['is_blocked'] ? 'badge-danger' : 'badge-success' ?>">
                                                <?= $user['is_blocked'] ? t('admin_blocked') : t('admin_active') ?>
                                            </span>
                                            <span class="admin-card-meta"><?= formatDate($user['created_at']) ?></span>
                                        </div>
                                        <div class="mt-3 flex items-center gap-1">
                                            <button onclick="editUser(<?= $user['id'] ?>)" class="action-btn action-btn-edit"
                                                title="<?= t('admin_edit') ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="resetUserContests(<?= $user['id'] ?>)" class="action-btn action-btn-exam"
                                                title="<?= t('admin_reset_solved_tasks', 'Сбросить посылки и прогресс') ?>">
                                                <i class="fas fa-rotate-left"></i>
                                            </button>
                                            <button
                                                onclick="toggleUserBlock(<?= $user['id'] ?>, <?= $user['is_blocked'] ? 'false' : 'true' ?>)"
                                                class="action-btn <?= $user['is_blocked'] ? 'action-btn-toggle' : 'action-btn-delete' ?>"
                                                title="<?= $user['is_blocked'] ? t('admin_unblock') : t('admin_block') ?>">
                                                <i class="fas fa-<?= $user['is_blocked'] ? 'unlock' : 'lock' ?>"></i>
                                            </button>
                                            <button onclick="deleteUser(<?= $user['id'] ?>)" class="action-btn action-btn-delete"
                                                title="<?= t('admin_delete') ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                <?php elseif ($tab === 'courses'): ?>
                    <!-- Courses Tab -->
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-graduation-cap mr-3 text-green-600"></i>
                                <?= t('admin_manage_courses') ?>
                            </h1>
                            <div class="flex items-center gap-2">
                                <button onclick="openAddCourseModal()" class="btn-primary">
                                    <i class="fas fa-plus mr-2"></i>
                                    <?= t('admin_add_course') ?>
                                </button>
                            </div>
                        </div>

                        <div class="card">
                            <div class="responsive-table-wrapper admin-mobile-table">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_title_label') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_instructor') ?></th>
                                            <th><?= t('admin_category') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_level') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_date') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($courses as $index => $course): ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                                    <?= htmlspecialchars($course['title']) ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= htmlspecialchars($course['instructor']) ?>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-primary"><?= htmlspecialchars($course['category']) ?></span>
                                                </td>
                                                <td class="mobile-hidden">
                                                    <span
                                                        class="badge badge-success"><?= htmlspecialchars($course['level']) ?></span>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= formatDate($course['created_at']) ?>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <a href="<?= htmlspecialchars($buildAdminTabUrl('course-lessons', ['admin_course_id' => (int) $course['id']])) ?>"
                                                            class="action-btn action-btn-exam" title="<?= t('admin_lessons', 'Уроки') ?>">
                                                            <i class="fas fa-book-open"></i>
                                                        </a>
                                                        <a href="<?= htmlspecialchars($buildAdminTabUrl('course-practice', ['admin_course_id' => (int) $course['id']])) ?>"
                                                            class="action-btn action-btn-exam" title="<?= t('admin_course_practice_tasks', 'Практика') ?>">
                                                            <i class="fas fa-laptop-code"></i>
                                                        </a>
                                                        <a href="<?= htmlspecialchars($buildAdminTabUrl('course-exams', ['admin_course_id' => (int) $course['id']])) ?>"
                                                            class="action-btn action-btn-exam" title="<?= t('admin_final_exams', 'Экзамен') ?>">
                                                            <i class="fas fa-clipboard-check"></i>
                                                        </a>
                                                        <button onclick="editCourse(<?= $course['id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteCourse(<?= $course['id'] ?>)"
                                                            class="action-btn action-btn-delete"
                                                            title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('courses'); ?>
                            <div class="admin-mobile-cards">
                                <?php foreach ($courses as $index => $course): ?>
                                    <div class="admin-card">
                                        <div class="admin-card-title"><?= htmlspecialchars($course['title']) ?></div>
                                        <div class="admin-card-meta mt-1">
                                            <?= htmlspecialchars($course['instructor']) ?>
                                        </div>
                                        <div class="mt-3 flex flex-wrap items-center gap-2">
                                            <span
                                                class="badge badge-primary"><?= htmlspecialchars($course['category']) ?></span>
                                            <span class="badge badge-success"><?= htmlspecialchars($course['level']) ?></span>
                                            <span class="admin-card-meta"><?= formatDate($course['created_at']) ?></span>
                                        </div>
                                        <div class="mt-3 flex items-center gap-1">
                                            <a href="<?= htmlspecialchars($buildAdminTabUrl('course-lessons', ['admin_course_id' => (int) $course['id']])) ?>"
                                                class="action-btn action-btn-exam" title="<?= t('admin_lessons', 'Уроки') ?>">
                                                <i class="fas fa-book-open"></i>
                                            </a>
                                            <a href="<?= htmlspecialchars($buildAdminTabUrl('course-practice', ['admin_course_id' => (int) $course['id']])) ?>"
                                                class="action-btn action-btn-exam" title="<?= t('admin_course_practice_tasks', 'Практика') ?>">
                                                <i class="fas fa-laptop-code"></i>
                                            </a>
                                            <a href="<?= htmlspecialchars($buildAdminTabUrl('course-exams', ['admin_course_id' => (int) $course['id']])) ?>"
                                                class="action-btn action-btn-exam" title="<?= t('admin_final_exams', 'Экзамен') ?>">
                                                <i class="fas fa-clipboard-check"></i>
                                            </a>
                                            <button onclick="editCourse(<?= $course['id'] ?>)"
                                                class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteCourse(<?= $course['id'] ?>)"
                                                class="action-btn action-btn-delete" title="<?= t('admin_delete') ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                <?php elseif ($isContestOverviewTab): ?>
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-flag-checkered mr-3 text-indigo-600"></i>
                                <?= t('admin_contests', 'Контесты') ?>
                            </h1>
                            <button onclick="openContestModal()" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i><?= t('admin_add', 'Добавить') ?>
                            </button>
                        </div>

                        <div class="card" id="contests-list">
                            <div class="responsive-table-wrapper">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_contest_task_title', 'Название') ?></th>
                                            <th class="mobile-hidden">Slug</th>
                                            <th><?= t('admin_status') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($adminContests as $index => $contest): ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($contest['title'] ?? '') ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= htmlspecialchars((string) ($contest['slug'] ?? '')) ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?= !empty($contest['is_active']) ? 'badge-success' : 'badge-danger' ?>">
                                                        <?= !empty($contest['is_active']) ? t('admin_active') : t('admin_blocked') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <a href="<?= htmlspecialchars($buildAdminTabUrl('contest-tasks', ['admin_contest_id' => (int) $contest['id']])) ?>"
                                                            class="action-btn action-btn-exam" title="<?= t('admin_contest_tasks', 'Задачи') ?>">
                                                            <i class="fas fa-file-code"></i>
                                                        </a>
                                                        <a href="<?= htmlspecialchars($buildAdminTabUrl('contest-solutions', ['admin_contest_id' => (int) $contest['id'], 'contest_solution_contest_id' => (int) $contest['id']])) ?>"
                                                            class="action-btn action-btn-exam" title="<?= t('admin_contest_solutions', 'Решения') ?>">
                                                            <i class="fas fa-square-check"></i>
                                                        </a>
                                                        <button onclick="openContestModal(<?= (int) $contest['id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteContest(<?= (int) $contest['id'] ?>)"
                                                            class="action-btn action-btn-delete" title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('contests'); ?>
                        </div>
                    </div>

                <?php elseif ($isContestTasksTab): ?>
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-file-code mr-3 text-emerald-600"></i>
                                <?= t('admin_contest_tasks', 'Задачи контестов') ?>
                            </h1>
                            <div class="flex items-center gap-2 flex-wrap">
                                <input id="contest-task-package-path" class="input-field" type="text"
                                    placeholder="tasks/123/000091/problems/A" style="min-width:280px;max-width:420px;">
                                <button onclick="importContestTaskPackageToModal()" class="btn-secondary">
                                    <i class="fas fa-file-import mr-2"></i><?= t('admin_import_xml_to_form', 'Импорт XML в форму') ?>
                                </button>
                                <button onclick="openContestTaskModal()" class="btn-primary">
                                    <i class="fas fa-plus mr-2"></i><?= t('admin_add', 'Добавить') ?>
                                </button>
                            </div>
                        </div>

                        <div class="card p-4">
                            <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
                                <input type="hidden" name="action" value="admin">
                                <input type="hidden" name="tab" value="contest-tasks">
                                <div class="min-w-[260px]">
                                    <label class="block text-sm text-gray-600 mb-2"><?= t('admin_contests', 'Контесты') ?></label>
                                    <select name="admin_contest_id" class="input-field w-full">
                                        <option value="0"><?= t('admin_all', 'Все') ?></option>
                                        <?php foreach ($adminContestsForSelect as $contestOption): ?>
                                            <option value="<?= (int) ($contestOption['id'] ?? 0) ?>" <?= $adminContestId === (int) ($contestOption['id'] ?? 0) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((string) ($contestOption['title'] ?? '')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="btn-secondary"><?= t('ratings_apply_filters', 'Применить') ?></button>
                                    <a href="?action=admin&tab=contest-tasks" class="btn-secondary"><?= t('admin_reset', 'Сбросить') ?></a>
                                </div>
                            </form>
                        </div>

                        <div class="card" id="contest-tasks">
                            <div class="responsive-table-wrapper">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_title_label') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_contests', 'Контесты') ?></th>
                                            <th><?= t('admin_level') ?></th>
                                            <th><?= t('admin_order') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($adminContestTasks as $index => $task): ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($task['title'] ?? '') ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= htmlspecialchars((string) ($task['contest_title'] ?? '-')) ?>
                                                </td>
                                                <td><span class="badge badge-primary"><?= htmlspecialchars((string) ($task['difficulty'] ?? 'easy')) ?></span></td>
                                                <td class="text-sm text-gray-500"><?= (int) ($task['order_num'] ?? 0) ?></td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <a href="<?= htmlspecialchars($buildAdminTabUrl('contest-solutions', ['admin_contest_id' => (int) ($task['contest_id'] ?? 0), 'contest_solution_contest_id' => (int) ($task['contest_id'] ?? 0), 'contest_solution_task_id' => (int) ($task['id'] ?? 0)])) ?>"
                                                            class="action-btn action-btn-exam" title="<?= t('admin_contest_solutions', 'Решения') ?>">
                                                            <i class="fas fa-square-check"></i>
                                                        </a>
                                                        <button onclick="openContestTaskModal(<?= (int) $task['id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteContestTask(<?= (int) $task['id'] ?>)"
                                                            class="action-btn action-btn-delete" title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('contest_tasks'); ?>
                        </div>

                    </div>

                <?php elseif ($isContestSolutionsTab): ?>
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-square-check mr-3 text-indigo-600"></i>
                                <?= t('admin_contest_solutions', 'Решённые задачи') ?>
                            </h1>
                            <a href="?action=admin&tab=contest-solutions" class="btn-secondary">
                                <i class="fas fa-rotate-left mr-2"></i><?= t('admin_filter_reset', 'Сбросить фильтры') ?>
                            </a>
                        </div>

                        <div class="card">
                            <form method="get" class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                <input type="hidden" name="action" value="admin">
                                <input type="hidden" name="tab" value="contest-solutions">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1" for="contest_solution_contest_id">
                                        <?= t('admin_contest', 'Контест') ?>
                                    </label>
                                    <select id="contest_solution_contest_id" name="contest_solution_contest_id" class="input-field">
                                        <option value="0"><?= t('admin_all', 'Все') ?></option>
                                        <?php foreach ($adminContestsForSelect as $contest): ?>
                                            <?php $contestId = (int) ($contest['id'] ?? 0); ?>
                                            <option value="<?= $contestId ?>" <?= $contestId === (int) ($_GET['contest_solution_contest_id'] ?? 0) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((string) ($contest['title'] ?? '')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1" for="contest_solution_task_id">
                                        <?= t('admin_title_label') ?>
                                    </label>
                                    <select id="contest_solution_task_id" name="contest_solution_task_id" class="input-field">
                                        <option value="0"><?= t('admin_all', 'Все') ?></option>
                                        <?php foreach ($adminContestSubmissionTasksForSelect as $task): ?>
                                            <?php $taskId = (int) ($task['id'] ?? 0); ?>
                                            <option value="<?= $taskId ?>" <?= $taskId === (int) ($_GET['contest_solution_task_id'] ?? 0) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((string) (($task['contest_title'] ?? '') !== '' ? ($task['contest_title'] . ' / ' . ($task['title'] ?? '')) : ($task['title'] ?? ''))) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="submit" class="btn-primary">
                                        <i class="fas fa-filter mr-2"></i><?= t('admin_filter_apply', 'Применить') ?>
                                    </button>
                                    <a href="?action=admin&tab=contest-solutions" class="btn-secondary">
                                        <?= t('admin_filter_reset', 'Сбросить фильтры') ?>
                                    </a>
                                </div>
                            </form>
                        </div>

                        <div class="card" id="contest-submissions">
                            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
                                <h2 class="text-lg font-bold text-gray-900"><?= t('admin_contest_submissions', 'Посылки по контестам') ?></h2>
                                <span class="text-xs text-gray-500"><?= count($recentContestSubmissions) ?></span>
                            </div>
                            <div class="responsive-table-wrapper">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_user', 'Пользователь') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_contests', 'Контесты') ?></th>
                                            <th><?= t('admin_title_label') ?></th>
                                            <th><?= t('admin_status') ?></th>
                                            <th><?= t('admin_attempts', 'Попытки') ?></th>
                                            <th><?= t('admin_checks', 'Проверки') ?></th>
                                            <th><?= t('admin_points', 'Баллы') ?></th>
                                            <th><?= t('admin_date', 'Дата') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($recentContestSubmissions)): ?>
                                            <tr>
                                                <td colspan="10" class="text-center text-sm text-gray-500 py-6">
                                                    <?= t('admin_table_no_results') ?>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($recentContestSubmissions as $index => $submission): ?>
                                                <?php
                                                $submissionStatus = (string) ($submission['status'] ?? 'unknown');
                                                $submissionStatusClass = $submissionStatus === 'accepted' ? 'badge-success' : 'badge-danger';
                                                ?>
                                                <tr>
                                                    <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                    <td class="text-sm text-gray-900"><?= htmlspecialchars((string) ($submission['user_name'] ?? '-')) ?></td>
                                                    <td class="mobile-hidden text-sm text-gray-500"><?= htmlspecialchars((string) ($submission['contest_title'] ?? '-')) ?></td>
                                                    <td class="text-sm text-gray-900 max-w-xs truncate"><?= htmlspecialchars((string) ($submission['task_title'] ?? '-')) ?></td>
                                                    <td>
                                                        <span class="badge <?= $submissionStatusClass ?>">
                                                            <?= htmlspecialchars($submissionStatus) ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-sm text-gray-500">
                                                        <?= (int) ($submission['attempts'] ?? 0) ?> / <?= (int) ($submission['wrong_attempts'] ?? 0) ?>
                                                    </td>
                                                    <td class="text-sm text-gray-500">
                                                        <?= (int) ($submission['checks_passed'] ?? 0) ?>/<?= (int) ($submission['checks_total'] ?? 0) ?>
                                                    </td>
                                                    <td class="text-sm text-gray-500"><?= (int) ($submission['points_awarded'] ?? 0) ?></td>
                                                    <td class="text-sm text-gray-500"><?= htmlspecialchars((string) ($submission['updated_at'] ?? $submission['created_at'] ?? '')) ?></td>
                                                    <td>
                                                        <div class="flex items-center gap-1">
                                                            <button onclick="openSubmissionDetail('contest', <?= (int) ($submission['id'] ?? 0) ?>)" class="action-btn action-btn-edit" title="<?= t('admin_view', 'Просмотр') ?>">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button onclick="resetContestSubmission(<?= (int) ($submission['id'] ?? 0) ?>)" class="action-btn action-btn-delete" title="<?= t('admin_reset', 'Сбросить') ?>">
                                                                <i class="fas fa-rotate-left"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('contest_submissions'); ?>
                        </div>
                    </div>

                <?php elseif ($isVacancyPrepTasksTab): ?>
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-user-tie mr-3 text-sky-600"></i>
                                <?= t('admin_interview_prep', 'Задачи подготовки к вакансиям') ?>
                            </h1>
                            <div class="flex flex-wrap items-center gap-2">
                                <button onclick="importInterviewPrepFolders()" class="btn-secondary">
                                    <i class="fas fa-folder-open mr-2"></i><?= t('admin_import_123_a', 'Импорт') ?>
                                </button>
                                <button onclick="openInterviewPrepTaskModal()" class="btn-primary">
                                    <i class="fas fa-plus mr-2"></i><?= t('admin_add', 'Добавить') ?>
                                </button>
                            </div>
                        </div>

                        <div class="card" id="interview-prep-tasks">
                            <div class="responsive-table-wrapper">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_title_label') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_category', 'Категория') ?></th>
                                            <th><?= t('admin_level') ?></th>
                                            <th><?= t('admin_order') ?></th>
                                            <th><?= t('admin_status') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($adminInterviewPrepTasks as $index => $task): ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($task['title'] ?? '') ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= htmlspecialchars((string) ($task['category'] ?? 'General')) ?>
                                                </td>
                                                <td><span class="badge badge-primary"><?= htmlspecialchars((string) ($task['difficulty'] ?? 'easy')) ?></span></td>
                                                <td class="text-sm text-gray-500"><?= (int) ($task['sort_order'] ?? 0) ?></td>
                                                <td>
                                                    <span class="badge <?= !empty($task['is_active']) ? 'badge-success' : 'badge-danger' ?>">
                                                        <?= !empty($task['is_active']) ? t('admin_active') : t('admin_blocked') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <button onclick="openInterviewPrepTaskModal(<?= (int) $task['id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteInterviewPrepTask(<?= (int) $task['id'] ?>)"
                                                            class="action-btn action-btn-delete" title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('interview_prep_tasks'); ?>
                        </div>
                    </div>

                <?php elseif ($tab === 'vacancies'): ?>
                    <!-- Vacancies Tab -->
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-briefcase mr-3 text-purple-600"></i>
                                <?= t('admin_manage_vacancies') ?>
                            </h1>
                            <button onclick="openAddVacancyModal()" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>
                                <?= t('admin_add_vacancy') ?>
                            </button>
                        </div>

                        <div class="card" id="roadmap-list">
                            <div class="responsive-table-wrapper admin-mobile-table">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_title_label') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_company') ?></th>
                                            <th><?= t('admin_type') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_salary') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_date') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($vacancies as $index => $vacancy): ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                                    <?= htmlspecialchars($vacancy['title']) ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= htmlspecialchars($vacancy['company']) ?>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-info"><?= getVacancyTypeText($vacancy['type']) ?></span>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= t('admin_salary_from') ?>         <?= $vacancy['salary_min'] ?>
                                                    <?= $currencyLabels[$vacancy['salary_currency'] ?? 'TJS'] ?? ($vacancy['salary_currency'] ?? 'TJS') ?>
                                                    <?php if ($vacancy['salary_max']): ?>
                                                        / <?= t('admin_salary_to') ?>             <?= $vacancy['salary_max'] ?>
                                                        <?= $currencyLabels[$vacancy['salary_currency'] ?? 'TJS'] ?? ($vacancy['salary_currency'] ?? 'TJS') ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= formatDate($vacancy['created_at']) ?>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <button onclick="editVacancy(<?= $vacancy['id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteVacancy(<?= $vacancy['id'] ?>)"
                                                            class="action-btn action-btn-delete"
                                                            title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('vacancies'); ?>
                            <div class="admin-mobile-cards">
                                <?php foreach ($vacancies as $index => $vacancy): ?>
                                    <div class="admin-card">
                                        <div class="admin-card-title"><?= htmlspecialchars($vacancy['title']) ?></div>
                                        <div class="admin-card-meta mt-1">
                                            <?= htmlspecialchars($vacancy['company']) ?>
                                        </div>
                                        <div class="mt-3 flex flex-wrap items-center gap-2">
                                            <span class="badge badge-info"><?= getVacancyTypeText($vacancy['type']) ?></span>
                                            <span class="admin-card-meta">
                                                <?= t('admin_salary_from') ?>         <?= $vacancy['salary_min'] ?>
                                                <?= $currencyLabels[$vacancy['salary_currency'] ?? 'TJS'] ?? ($vacancy['salary_currency'] ?? 'TJS') ?>
                                                <?php if ($vacancy['salary_max']): ?>
                                                    / <?= t('admin_salary_to') ?>             <?= $vacancy['salary_max'] ?>
                                                    <?= $currencyLabels[$vacancy['salary_currency'] ?? 'TJS'] ?? ($vacancy['salary_currency'] ?? 'TJS') ?>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="mt-2 admin-card-meta"><?= formatDate($vacancy['created_at']) ?></div>
                                        <div class="mt-3 flex items-center gap-1">
                                            <button onclick="editVacancy(<?= $vacancy['id'] ?>)"
                                                class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteVacancy(<?= $vacancy['id'] ?>)"
                                                class="action-btn action-btn-delete" title="<?= t('admin_delete') ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                <?php elseif ($isRoadmapOverviewTab || $isRoadmapNodesTab || $isRoadmapTasksTab || $isRoadmapExamsTab): ?>
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-project-diagram mr-3 text-blue-600"></i>
                                <?php if ($isRoadmapNodesTab): ?>
                                    <?= t('admin_nodes', 'Ноды роадмапов') ?>
                                <?php elseif ($isRoadmapTasksTab): ?>
                                    <?= t('admin_roadmap_tasks', 'Задания роадмапов') ?>
                                <?php elseif ($isRoadmapExamsTab): ?>
                                    <?= t('admin_roadmap_exams', 'Экзамены роадмапов') ?>
                                <?php else: ?>
                                    <?= t('admin_roadmaps', 'Роадмапы') ?>
                                <?php endif; ?>
                            </h1>
                            <div class="flex flex-wrap gap-2">
                                <?php if ($isRoadmapOverviewTab): ?>
                                    <button onclick="openRoadmapModal()" class="btn-primary">
                                        <i class="fas fa-plus mr-2"></i>
                                        <?= t('admin_roadmap', 'Роадмап') ?>
                                    </button>
                                <?php elseif ($isRoadmapNodesTab): ?>
                                    <button onclick="openRoadmapNodeModal()" class="btn-primary">
                                        <i class="fas fa-plus mr-2"></i>
                                        <?= t('admin_node', 'Нода') ?>
                                    </button>
                                <?php elseif ($isRoadmapTasksTab): ?>
                                    <button onclick="openRoadmapLessonModal()" class="btn-primary">
                                        <i class="fas fa-plus mr-2"></i>
                                        <?= t('admin_lesson', 'Задание') ?>
                                    </button>
                                <?php elseif ($isRoadmapExamsTab): ?>
                                    <button onclick="openRoadmapQuizModal()" class="btn-primary">
                                        <i class="fas fa-plus mr-2"></i>
                                        <?= t('admin_question', 'Экзамен') ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php
                        $roadmapCounts = (array) ($roadmapCountsMap ?? []);
                        if (empty($roadmapCounts)) {
                            foreach ($roadmapNodes as $n) {
                                $key = $n['roadmap_title'] ?? t('admin_default');
                                $roadmapCounts[$key] = ($roadmapCounts[$key] ?? 0) + 1;
                            }
                        }
                        ?>

                        <?php if ($isRoadmapOverviewTab): ?>
                        <div class="card" id="roadmap-list">
                            <div class="px-5 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-900"><?= t('admin_roadmaps', 'Роадмапы') ?></h2>
                            </div>
                            <div class="responsive-table-wrapper">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_title_label') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_description') ?></th>
                                            <th><?= t('admin_nodes') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($roadmapList)): ?>
                                            <tr>
                                                <td colspan="5" class="empty-state">
                                                    <i class="fas fa-inbox text-gray-400"></i>
                                                    <p><?= t('admin_roadmaps_empty') ?></p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($roadmapList as $index => $roadmap): ?>
                                            <?php $title = $roadmap['title'] ?? t('admin_default'); ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="text-sm font-medium text-gray-900"><?= htmlspecialchars($title) ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500 max-w-xs truncate">
                                                    <?= htmlspecialchars($roadmap['description'] ?? '') ?>
                                                </td>
                                                <td class="text-sm text-gray-500"><?= (int) ($roadmapCounts[$title] ?? 0) ?>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <a href="<?= htmlspecialchars($buildAdminTabUrl('roadmap-nodes', ['admin_roadmap_title' => $title])) ?>"
                                                            class="action-btn action-btn-exam" title="<?= t('admin_nodes', 'Ноды') ?>">
                                                            <i class="fas fa-project-diagram"></i>
                                                        </a>
                                                        <a href="<?= htmlspecialchars($buildAdminTabUrl('roadmap-tasks', ['admin_roadmap_title' => $title])) ?>"
                                                            class="action-btn action-btn-exam" title="<?= t('admin_roadmap_tasks', 'Задания') ?>">
                                                            <i class="fas fa-book-open"></i>
                                                        </a>
                                                        <a href="<?= htmlspecialchars($buildAdminTabUrl('roadmap-exams', ['admin_roadmap_title' => $title])) ?>"
                                                            class="action-btn action-btn-exam" title="<?= t('admin_roadmap_exams', 'Экзамены') ?>">
                                                            <i class="fas fa-clipboard-check"></i>
                                                        </a>
                                                        <button onclick="openRoadmapModal(<?= (int) $roadmap['id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteRoadmap(<?= (int) $roadmap['id'] ?>)"
                                                            class="action-btn action-btn-delete"
                                                            title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('roadmap_list'); ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($isRoadmapNodesTab): ?>
                        <div class="card p-4">
                            <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
                                <input type="hidden" name="action" value="admin">
                                <input type="hidden" name="tab" value="roadmap-nodes">
                                <div class="min-w-[280px]">
                                    <label class="block text-sm text-gray-600 mb-2"><?= t('admin_roadmap_title') ?></label>
                                    <select name="admin_roadmap_title" class="input-field w-full">
                                        <option value=""><?= t('admin_all', 'Все') ?></option>
                                        <?php foreach ($roadmapListForSelect as $roadmapOption): ?>
                                            <?php $roadmapOptionTitle = (string) ($roadmapOption['title'] ?? ''); ?>
                                            <option value="<?= htmlspecialchars($roadmapOptionTitle) ?>" <?= $adminRoadmapTitle === $roadmapOptionTitle ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($roadmapOptionTitle) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="btn-secondary"><?= t('ratings_apply_filters', 'Применить') ?></button>
                                    <a href="?action=admin&tab=roadmap-nodes" class="btn-secondary"><?= t('admin_reset', 'Сбросить') ?></a>
                                </div>
                            </form>
                        </div>
                        <div class="card" id="roadmap-nodes">
                            <div class="px-5 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-900"><?= t('admin_nodes', 'Ноды') ?></h2>
                            </div>
                            <div class="responsive-table-wrapper">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_title_label') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_roadmap') ?></th>
                                            <th class="mobile-xs-hidden">X</th>
                                            <th class="mobile-xs-hidden">Y</th>
                                            <th class="mobile-hidden">Deps</th>
                                            <th class="mobile-hidden"><?= t('admin_exam') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($roadmapNodes as $index => $node): ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                                    <?= htmlspecialchars($node['title']) ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= htmlspecialchars($node['roadmap_title'] ?? t('admin_default')) ?>
                                                </td>
                                                <td class="mobile-xs-hidden text-sm text-gray-500"><?= (int) $node['x'] ?></td>
                                                <td class="mobile-xs-hidden text-sm text-gray-500"><?= (int) $node['y'] ?></td>
                                                <td class="mobile-hidden text-sm text-gray-500 max-w-xs truncate">
                                                    <?= htmlspecialchars($node['deps']) ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <span
                                                        class="badge <?= !empty($node['is_exam']) ? 'badge-success' : 'badge-danger' ?>">
                                                        <?= !empty($node['is_exam']) ? t('admin_yes') : t('admin_no') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <button onclick="openRoadmapNodeModal(<?= (int) $node['id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteRoadmapNode(<?= (int) $node['id'] ?>)"
                                                            class="action-btn action-btn-delete"
                                                            title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('roadmap_nodes'); ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($isRoadmapTasksTab): ?>
                        <div class="card p-4">
                            <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
                                <input type="hidden" name="action" value="admin">
                                <input type="hidden" name="tab" value="roadmap-tasks">
                                <div class="min-w-[280px]">
                                    <label class="block text-sm text-gray-600 mb-2"><?= t('admin_roadmap_title') ?></label>
                                    <select name="admin_roadmap_title" class="input-field w-full">
                                        <option value=""><?= t('admin_all', 'Все') ?></option>
                                        <?php foreach ($roadmapListForSelect as $roadmapOption): ?>
                                            <?php $roadmapOptionTitle = (string) ($roadmapOption['title'] ?? ''); ?>
                                            <option value="<?= htmlspecialchars($roadmapOptionTitle) ?>" <?= $adminRoadmapTitle === $roadmapOptionTitle ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($roadmapOptionTitle) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="btn-secondary"><?= t('ratings_apply_filters', 'Применить') ?></button>
                                    <a href="?action=admin&tab=roadmap-tasks" class="btn-secondary"><?= t('admin_reset', 'Сбросить') ?></a>
                                </div>
                            </form>
                        </div>
                        <div class="card" id="roadmap-tasks">
                            <div class="px-5 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-900"><?= t('admin_roadmap_tasks', 'Задания роадмапов') ?></h2>
                            </div>
                            <div class="responsive-table-wrapper">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_node') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_title_label') ?></th>
                                            <th><?= t('admin_order') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($roadmapLessons as $index => $lesson): ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                                    <?= htmlspecialchars($lesson['node_title'] ?? '') ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500 max-w-xs truncate">
                                                    <?= htmlspecialchars($lesson['title']) ?>
                                                </td>
                                                <td class="text-sm text-gray-500"><?= (int) $lesson['order_index'] ?></td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <button onclick="openRoadmapLessonModal(<?= (int) $lesson['id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteRoadmapLesson(<?= (int) $lesson['id'] ?>)"
                                                            class="action-btn action-btn-delete"
                                                            title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('roadmap_lessons'); ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($isRoadmapExamsTab): ?>
                        <div class="card p-4">
                            <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
                                <input type="hidden" name="action" value="admin">
                                <input type="hidden" name="tab" value="roadmap-exams">
                                <div class="min-w-[280px]">
                                    <label class="block text-sm text-gray-600 mb-2"><?= t('admin_roadmap_title') ?></label>
                                    <select name="admin_roadmap_title" class="input-field w-full">
                                        <option value=""><?= t('admin_all', 'Все') ?></option>
                                        <?php foreach ($roadmapListForSelect as $roadmapOption): ?>
                                            <?php $roadmapOptionTitle = (string) ($roadmapOption['title'] ?? ''); ?>
                                            <option value="<?= htmlspecialchars($roadmapOptionTitle) ?>" <?= $adminRoadmapTitle === $roadmapOptionTitle ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($roadmapOptionTitle) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="btn-secondary"><?= t('ratings_apply_filters', 'Применить') ?></button>
                                    <a href="?action=admin&tab=roadmap-exams" class="btn-secondary"><?= t('admin_reset', 'Сбросить') ?></a>
                                </div>
                            </form>
                        </div>
                        <div class="card" id="roadmap-exams">
                            <div class="px-5 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-900"><?= t('admin_roadmap_exams', 'Экзамены роадмапов') ?></h2>
                            </div>
                            <div class="responsive-table-wrapper">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_node') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_question') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_answer') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($roadmapQuizzes as $index => $quiz): ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                                    <?= htmlspecialchars($quiz['node_title'] ?? '') ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500 max-w-xs truncate">
                                                    <?= htmlspecialchars($quiz['question']) ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500 max-w-xs truncate">
                                                    <?= htmlspecialchars($quiz['correct_answer']) ?>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <button onclick="openRoadmapQuizModal(<?= (int) $quiz['id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteRoadmapQuiz(<?= (int) $quiz['id'] ?>)"
                                                            class="action-btn action-btn-delete"
                                                            title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('roadmap_quizzes'); ?>
                        </div>
                        <?php endif; ?>
                    </div>

                <?php elseif ($isCoursePracticeTab): ?>
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-laptop-code mr-3 text-violet-600"></i>
                                <?= t('admin_course_practice_tasks', 'Практические задания курсов') ?>
                            </h1>
                            <button onclick="openLessonModal()" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>
                                <?= t('admin_add_lesson', 'Добавить урок') ?>
                            </button>
                        </div>

                        <div class="card p-4">
                            <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
                                <input type="hidden" name="action" value="admin">
                                <input type="hidden" name="tab" value="course-practice">
                                <div class="min-w-[280px]">
                                    <label class="block text-sm text-gray-600 mb-2"><?= t('admin_course') ?></label>
                                    <select name="admin_course_id" class="input-field w-full">
                                        <option value="0"><?= t('admin_all', 'Все') ?></option>
                                        <?php foreach ($courses as $courseOption): ?>
                                            <option value="<?= (int) ($courseOption['id'] ?? 0) ?>" <?= $adminCourseId === (int) ($courseOption['id'] ?? 0) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((string) ($courseOption['title'] ?? '')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="btn-secondary"><?= t('ratings_apply_filters', 'Применить') ?></button>
                                    <a href="?action=admin&tab=course-practice" class="btn-secondary"><?= t('admin_reset', 'Сбросить') ?></a>
                                </div>
                            </form>
                        </div>

                        <div class="card">
                            <div class="responsive-table-wrapper">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_title_label') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_course') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_lesson', 'Урок') ?></th>
                                            <th><?= t('admin_type') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($practiceTasks as $index => $task): ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                                    <?= htmlspecialchars($task['title'] ?: ('#' . (int) ($task['id'] ?? 0))) ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500 max-w-xs truncate">
                                                    <?= htmlspecialchars((string) ($task['course_title'] ?? '-')) ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500 max-w-xs truncate">
                                                    <?= htmlspecialchars((string) ($task['lesson_title'] ?? '-')) ?>
                                                </td>
                                                <td class="text-sm text-gray-500"><?= htmlspecialchars((string) ($task['language'] ?? 'code')) ?></td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <button onclick="openLessonModal(<?= (int) ($task['lesson_id'] ?? 0) ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <a href="<?= htmlspecialchars($buildAdminTabUrl('course-lessons', ['admin_course_id' => (int) ($task['course_id'] ?? 0)])) ?>"
                                                            class="action-btn action-btn-exam" title="<?= t('admin_open', 'Открыть урок') ?>">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('practice_tasks'); ?>
                        </div>

                        <div class="card">
                            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
                                <h2 class="text-lg font-bold text-gray-900"><?= t('admin_practice_submissions', 'Решения по практическим заданиям') ?></h2>
                                <span class="text-xs text-gray-500"><?= count($recentPracticeSubmissions) ?></span>
                            </div>
                            <div class="responsive-table-wrapper">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_user', 'Пользователь') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_course') ?></th>
                                            <th><?= t('admin_title_label') ?></th>
                                            <th><?= t('admin_status') ?></th>
                                            <th><?= t('admin_date') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentPracticeSubmissions as $index => $submission): ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="text-sm text-gray-900"><?= htmlspecialchars((string) ($submission['user_name'] ?? '-')) ?></td>
                                                <td class="mobile-hidden text-sm text-gray-500"><?= htmlspecialchars((string) ($submission['course_title'] ?? '-')) ?></td>
                                                <td class="text-sm text-gray-900 max-w-xs truncate"><?= htmlspecialchars((string) ($submission['task_title'] ?? '-')) ?></td>
                                                <td>
                                                    <span class="badge <?= !empty($submission['passed']) ? 'badge-success' : 'badge-danger' ?>">
                                                        <?= !empty($submission['passed']) ? t('admin_success', 'Успешно') : t('admin_failed', 'Ошибка') ?>
                                                    </span>
                                                </td>
                                                <td class="text-sm text-gray-500"><?= htmlspecialchars((string) ($submission['created_at'] ?? '')) ?></td>
                                                <td>
                                                    <button onclick="openSubmissionDetail('practice', <?= (int) ($submission['id'] ?? 0) ?>)" class="action-btn action-btn-edit" title="<?= t('admin_view', 'Просмотр') ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php elseif ($isCourseLessonsTab): ?>
                    <!-- Lessons Tab -->
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-book-open mr-3 text-blue-600"></i>
                                <?= t('admin_manage_lessons') ?>
                            </h1>
                            <button onclick="openLessonModal()" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>
                                <?= t('admin_add_lesson') ?>
                            </button>
                        </div>

                        <div class="card p-4">
                            <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
                                <input type="hidden" name="action" value="admin">
                                <input type="hidden" name="tab" value="course-lessons">
                                <div class="min-w-[280px]">
                                    <label class="block text-sm text-gray-600 mb-2"><?= t('admin_course') ?></label>
                                    <select name="admin_course_id" class="input-field w-full">
                                        <option value="0"><?= t('admin_all', 'Все') ?></option>
                                        <?php foreach ($courses as $courseOption): ?>
                                            <option value="<?= (int) ($courseOption['id'] ?? 0) ?>" <?= $adminCourseId === (int) ($courseOption['id'] ?? 0) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((string) ($courseOption['title'] ?? '')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="btn-secondary"><?= t('ratings_apply_filters', 'Применить') ?></button>
                                    <a href="?action=admin&tab=course-lessons" class="btn-secondary"><?= t('admin_reset', 'Сбросить') ?></a>
                                </div>
                            </form>
                        </div>

                        <div class="card">
                            <div class="responsive-table-wrapper">
                                <table id="lessons-table" data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_title_label') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_course') ?></th>
                                            <th><?= t('admin_type') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_order') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lessons as $index => $lesson): ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                                    <?= htmlspecialchars($lesson['title']) ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500 max-w-xs truncate">
                                                    <?= htmlspecialchars($lesson['course_title'] ?? (t('admin_course') . ' #' . (int) ($lesson['course_id'] ?? 0))) ?>
                                                </td>
                                                <td class="text-sm text-gray-500"><?= htmlspecialchars($lesson['type']) ?></td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= (int) $lesson['order_num'] ?>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <a href="<?= htmlspecialchars($buildAdminTabUrl('course-practice', ['admin_course_id' => (int) ($lesson['course_id'] ?? 0)])) ?>"
                                                            class="action-btn action-btn-exam" title="<?= t('admin_course_practice_tasks', 'Практика') ?>">
                                                            <i class="fas fa-laptop-code"></i>
                                                        </a>
                                                        <button onclick="openLessonModal(<?= (int) $lesson['id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteLesson(<?= (int) $lesson['id'] ?>)"
                                                            class="action-btn action-btn-delete"
                                                            title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('lessons'); ?>
                        </div>
                    </div>

                <?php elseif ($isCourseExamsTab): ?>
                    <!-- Exams Tab -->
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-clipboard-check mr-3 text-emerald-600"></i>
                                <?= t('admin_final_exams') ?>
                            </h1>
                        </div>

                        <div class="card p-4">
                            <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
                                <input type="hidden" name="action" value="admin">
                                <input type="hidden" name="tab" value="course-exams">
                                <div class="min-w-[280px]">
                                    <label class="block text-sm text-gray-600 mb-2"><?= t('admin_course') ?></label>
                                    <select name="admin_course_id" class="input-field w-full">
                                        <option value="0"><?= t('admin_all', 'Все') ?></option>
                                        <?php foreach ($courses as $courseOption): ?>
                                            <option value="<?= (int) ($courseOption['id'] ?? 0) ?>" <?= $adminCourseId === (int) ($courseOption['id'] ?? 0) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((string) ($courseOption['title'] ?? '')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="btn-secondary"><?= t('ratings_apply_filters', 'Применить') ?></button>
                                    <a href="?action=admin&tab=course-exams" class="btn-secondary"><?= t('admin_reset', 'Сбросить') ?></a>
                                </div>
                            </form>
                        </div>

                        <div class="card">
                            <div class="responsive-table-wrapper">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= t('admin_course') ?></th>
                                            <th><?= t('admin_questions_count') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_minutes') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_pass_percent') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($courseExams as $index => $exam): ?>
                                            <?php
                                            $qCount = 0;
                                            try {
                                                $qCount = count(json_decode($exam['exam_json'] ?? '[]', true) ?: []);
                                            } catch (Throwable $e) {
                                            }
                                            ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                                    <?= htmlspecialchars($exam['course_title'] ?? (t('admin_course') . ' #' . (int) $exam['course_id'])) ?>
                                                </td>
                                                <td class="text-sm text-gray-500"><?= (int) $qCount ?></td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= (int) ($exam['time_limit_minutes'] ?? 0) ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= (int) ($exam['pass_percent'] ?? 0) ?>%
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1">
                                                        <button onclick="openCourseExamModal(<?= (int) $exam['course_id'] ?>)"
                                                            class="action-btn action-btn-edit" title="<?= t('admin_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteCourseExam(<?= (int) $exam['course_id'] ?>)"
                                                            class="action-btn action-btn-delete"
                                                            title="<?= t('admin_delete') ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('exams'); ?>
                        </div>
                    </div>

                <?php elseif ($tab === 'notifications'): ?>
                    <!-- Notifications Tab -->
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-bell mr-3 text-yellow-600"></i>
                                <?= t('admin_notifications') ?>
                            </h1>
                            <button onclick="openNotificationModal()" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>
                                <?= t('admin_add_notification') ?>
                            </button>
                        </div>

                        <div class="card">
                            <div class="responsive-table-wrapper">
                                <table data-server-paginated="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th class="mobile-hidden"><?= t('admin_user') ?></th>
                                            <th><?= t('admin_message') ?></th>
                                            <th class="mobile-hidden"><?= t('admin_date') ?></th>
                                            <th><?= t('admin_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($notifications as $index => $notification): ?>
                                            <tr>
                                                <td class="text-sm text-gray-500"><?= $index + 1 ?></td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= htmlspecialchars($notification['user_name'] ?? '') ?>
                                                </td>
                                                <?php $adminNotifText = translateNotificationMessage($notification['message'] ?? ''); ?>
                                                <td class="text-sm text-gray-900 max-w-xs truncate"
                                                    title="<?= htmlspecialchars($adminNotifText) ?>">
                                                    <?= htmlspecialchars($adminNotifText) ?>
                                                </td>
                                                <td class="mobile-hidden text-sm text-gray-500">
                                                    <?= formatDate($notification['notification_time']) ?>
                                                </td>
                                                <td>
                                                    <button onclick="deleteNotification(<?= (int) $notification['id'] ?>)"
                                                        class="action-btn action-btn-delete" title="<?= t('admin_delete') ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $renderAdminPager('notifications'); ?>
                        </div>
                    </div>

                <?php elseif ($tab === 'ejudge-import'): ?>
                    <!-- Ejudge Import Tab -->
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-file-import mr-3 text-indigo-600"></i>
                                <?= t('admin_ejudge_import', 'Импорт Ejudge') ?>
                            </h1>
                        </div>

                        <div class="card p-6 space-y-5">
                            <div>
                                <h2 class="text-lg font-bold text-gray-900"><?= t('admin_ejudge_sources_title', 'Источники задач') ?></h2>
                                <p class="text-sm text-gray-500 mt-1">
<?= t('admin_ejudge_sources_hint', 'Укажите папки, где лежат statement.xml и tests. Можно несколько строк.') ?>                                </p>
                            </div>
                            <textarea id="ejudgePaths" class="input-field w-full" rows="4"
                                placeholder="tasks">tasks</textarea>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="flex items-center gap-2 text-sm text-gray-600">
                                    <input type="checkbox" id="ejudgeImportInterview" class="accent-indigo-600" checked>
<?= t('admin_ejudge_to_interview', 'Импорт в подготовку') ?>                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-600">
                                    <input type="checkbox" id="ejudgeImportContest" class="accent-indigo-600">
<?= t('admin_ejudge_to_contest', 'Импорт в контест') ?>                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="text-sm text-gray-600"><?= t('admin_ejudge_contest', 'Контест') ?></label>
                                    <select id="ejudgeContestId" class="input-field w-full">
                                        <option value="0"><?= t('admin_select', 'Выберите') ?></option>
                                        <?php foreach ($adminContests as $contest): ?>
                                            <option value="<?= (int) ($contest['id'] ?? 0) ?>">
                                                <?= htmlspecialchars((string) ($contest['title'] ?? '')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600"><?= t('admin_ejudge_category', 'Категория') ?></label>
                                    <input id="ejudgeCategory" class="input-field w-full" type="text" value="Ejudge">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600"><?= t('admin_ejudge_difficulty', 'Сложность') ?></label>
                                    <select id="ejudgeDifficulty" class="input-field w-full">
                                        <option value="easy">Easy</option>
                                        <option value="medium">Medium</option>
                                        <option value="hard">Hard</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <button id="ejudgeScanBtn" class="btn-secondary">
                                    <i class="fas fa-magnifying-glass mr-2"></i><?= t('admin_ejudge_scan', 'Сканировать') ?>
                                </button>
                                <button id="ejudgeImportBtn" class="btn-primary">
                                    <i class="fas fa-file-import mr-2"></i><?= t('admin_ejudge_import_btn', 'Импортировать') ?>
                                </button>
                            </div>

                            <div class="border-t border-gray-200 pt-4 space-y-3">
                                <div class="text-sm font-semibold text-gray-700"><?= t('admin_ejudge_result', 'Результат') ?></div>
                                <div id="ejudgeResult" class="text-sm text-gray-600"></div>
                                <div id="ejudgePreview" class="text-sm text-gray-500"></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Course Exam Modal -->
    <div id="courseExamModal" class="modal">
        <div class="modal-overlay" onclick="closeModal('courseExamModal')"></div>
        <div class="modal-content card p-6 max-w-4xl">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900"><?= t('admin_final_exam') ?></h2>
                    <p class="text-sm text-gray-500 mt-1"><?= t('admin_exam_settings') ?></p>
                </div>
                <button class="text-gray-400 hover:text-gray-700" onclick="closeModal('courseExamModal')">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <input type="hidden" id="exam-course-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-gray-50 border border-gray-200 mb-6">
                <div>
                    <label class="text-sm text-gray-600 mb-1 block"><?= t('admin_limit_min') ?></label>
                    <input id="exam-time-limit" class="input-field w-full" type="number" min="5" value="45">
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block"><?= t('admin_pass_percent') ?></label>
                    <input id="exam-pass-percent" class="input-field w-full" type="number" min="50" max="100"
                        value="70">
                </div>
                <div class="flex items-center gap-2">
                    <input id="exam-shuffle-q" type="checkbox" class="h-4 w-4 rounded" checked>
                    <label class="text-sm text-gray-600"><?= t('admin_shuffle_questions') ?></label>
                </div>
                <div class="flex items-center gap-2">
                    <input id="exam-shuffle-o" type="checkbox" class="h-4 w-4 rounded" checked>
                    <label class="text-sm text-gray-600"><?= t('admin_shuffle_answers') ?></label>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm text-gray-600 font-medium"><?= t('admin_exam_questions') ?></label>
                    <button type="button" class="btn-secondary text-sm" onclick="addExamQuestion()">
                        <i class="fas fa-plus mr-2"></i><?= t('admin_add_question') ?>
                    </button>
                </div>
                <div id="exam-questions-list" class="space-y-4"></div>
            </div>
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <label
                        class="text-sm text-gray-600 font-medium"><?= t('admin_exam_json', 'JSON экзамена (расширенный)') ?></label>
                </div>
                <textarea id="exam-json-raw" class="input-field w-full font-mono text-xs" rows="8" data-no-tinymce="true"
                    placeholder='[{"type":"mc_single","question":"...","options":["A","B"],"correct_answer":"A"}]'></textarea>
                <div class="text-xs text-gray-500 mt-2">
<?= t('admin_exam_json_hint', 'Если заполнить JSON, он будет сохранён как есть (для расширенных типов).') ?>                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button class="btn-secondary px-6 py-2.5"
                    onclick="closeModal('courseExamModal')"><?= t('admin_cancel') ?></button>
                <button class="btn-primary px-6 py-2.5" onclick="saveCourseExam()"><?= t('admin_save') ?></button>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-overlay" onclick="closeModal('userModal')"></div>
        <div class="modal-content card p-6 max-w-2xl">
            <h2 class="text-xl font-bold text-gray-900 mb-5"><?= t('admin_user') ?></h2>
            <input type="hidden" id="user-id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Email</label>
                    <input id="user-email" class="input-field w-full" type="email">
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block"><?= t('admin_name') ?></label>
                    <input id="user-name" class="input-field w-full" type="text">
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block"><?= t('admin_role') ?></label>
                    <select id="user-role" class="select-field w-full">
                        <option value="seeker"><?= t('role_seeker') ?></option>
                        <option value="recruiter"><?= t('role_recruiter') ?></option>
                        <option value="admin"><?= t('role_admin') ?></option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block"><?= t('admin_position') ?></label>
                    <input id="user-title" class="input-field w-full" type="text">
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block"><?= t('admin_location') ?></label>
                    <input id="user-location" class="input-field w-full" type="text">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600 mb-1 block"><?= t('admin_avatar_field') ?></label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
                        <input id="user-avatar" class="input-field w-full md:col-span-2" type="text"
                            placeholder="<?= t('admin_avatar_placeholder') ?>">
                        <div class="flex items-center gap-2">
                            <input id="course-image-file" type="file" accept="image/*" class="w-full input-field">
                            <button type="button" class="btn-secondary text-xs"
                                onclick="uploadImageToInput('user-avatar-file','user-avatar')"><?= t('admin_upload') ?></button>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600 mb-1 block"><?= t('admin_about') ?></label>
                    <textarea id="user-bio" class="input-field w-full" rows="3"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600 mb-1 block"><?= t('admin_password_optional') ?></label>
                    <input id="user-password" class="input-field w-full" type="text">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button class="btn-secondary px-6 py-2.5"
                    onclick="closeModal('userModal')"><?= t('admin_cancel') ?></button>
                <button class="btn-primary px-6 py-2.5" onclick="saveUser()"><?= t('admin_save') ?></button>
            </div>
        </div>
    </div>

    <!-- Course Modal -->
    <div id="courseModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('courseModal')"></div>
        <div class="relative max-w-2xl mx-auto mt-10 bg-white rounded-xl shadow-xl p-6 modal-sheet">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><?= t('admin_course') ?></h2>
            <input type="hidden" id="course-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_title_label') ?></label>
                    <input id="course-title" class="input-field w-full" type="text">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_instructor') ?></label>
                    <input id="course-instructor" class="input-field w-full" type="text">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_category') ?></label>
                    <select id="course-category" class="input-field w-full">
                        <option value="frontend">Frontend</option>
                        <option value="backend">Backend</option>
                        <option value="design">Design</option>
                        <option value="devops">DevOps</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_level') ?></label>
                    <select id="course-level" class="input-field w-full">
                        <option value="Начинающий"><?= t('courses_level_beginner') ?></option>
                        <option value="Средний"><?= t('courses_level_medium') ?></option>
                        <option value="Высокий"><?= t('courses_level_advanced') ?></option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_progress') ?></label>
                    <input id="course-progress" class="input-field w-full" type="number" min="0" max="100">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_image_field') ?></label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
                        <input id="course-image" class="input-field w-full md:col-span-2" type="text"
                            placeholder="<?= t('admin_image_placeholder') ?>">
                        <div class="flex items-center gap-2">
                            <input id="portfolio-image-file" type="file" accept="image/*" class="w-full input-field">
                            <button type="button" class="btn-secondary"
                                onclick="uploadImageToInput('course-image-file','course-image')"><?= t('admin_upload') ?></button>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_description') ?></label>
                    <textarea id="course-description" class="input-field w-full" rows="4"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button class="btn-primary" onclick="saveCourse()"><?= t('admin_save') ?></button>
                <button class="btn-secondary" onclick="closeModal('courseModal')"><?= t('admin_cancel') ?></button>
            </div>
        </div>
    </div>

    <!-- Lesson Modal -->
    <div id="lessonModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('lessonModal')"></div>
        <div class="relative max-w-2xl mx-auto mt-10 bg-white rounded-xl shadow-xl p-6 modal-sheet">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><?= t('admin_lesson') ?></h2>
            <input type="hidden" id="lesson-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_course') ?></label>
                    <select id="lesson-course-id" class="input-field w-full">
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= (int) $course['id'] ?>"><?= htmlspecialchars($course['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_title_label') ?></label>
                    <input id="lesson-title" class="input-field w-full" type="text">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_type') ?></label>
                    <select id="lesson-type" class="input-field w-full">
                        <option value="article"><?= t('admin_type_article') ?></option>
                        <option value="video"><?= t('admin_type_video') ?></option>
                        <option value="quiz"><?= t('admin_type_quiz') ?></option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_order') ?></label>
                    <input id="lesson-order" class="input-field w-full" type="number" min="0">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_video_url') ?></label>
                    <input id="lesson-video" class="input-field w-full" type="text">
                </div>
                <div class="md:col-span-2" id="lesson-quiz-wrap" style="display:none;">
                    <label class="text-sm text-gray-600"><?= t('admin_quiz') ?></label>
                    <div id="lesson-quiz-list" class="space-y-4 mt-2"></div>
                    <button type="button" class="btn-secondary mt-3"
                        onclick="addQuizQuestion()"><?= t('admin_add_question') ?></button>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_materials_title') ?></label>
                    <input id="lesson-materials-title" class="input-field w-full" type="text">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_materials_url') ?></label>
                    <input id="lesson-materials-url" class="input-field w-full" type="text">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_content') ?></label>
                    <textarea id="lesson-content" class="input-field w-full" rows="5"></textarea>
                </div>

                <div class="md:col-span-2 border-t border-gray-200 pt-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-gray-800">Практическое задание (Judge0)</div>
                            <div class="text-xs text-gray-500 mt-1">Проверка решений через Judge0
                                (tests_json не показывается студенту).</div>
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input id="lesson-practice-enabled" type="checkbox" class="rounded border-gray-300">
                            Включить
                        </label>
                    </div>

                    <div id="lesson-practice-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="text-sm text-gray-600"><?= t('admin_task_kind', 'Вид задачи') ?></label>
                            <select id="lesson-practice-kind" class="input-field w-full">
                                <option value="code"><?= t('admin_task_kind_code', 'Кодовая') ?></option>
                                <option value="sql"><?= t('admin_task_kind_sql', 'SQL') ?></option>
                                <option value="fill"><?= t('admin_task_kind_fill', 'Заполни пропуски') ?></option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600"><?= t('admin_language', 'Язык') ?></label>
                            <select id="lesson-practice-language" class="input-field w-full"></select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600"><?= t('admin_title_label') ?></label>
                            <input id="lesson-practice-title" class="input-field w-full" type="text"
                                placeholder="<?= t('admin_task_title_placeholder', 'Задача : вот') ?>">
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="text-sm text-gray-600"><?= t('admin_task_prompt', 'Задача (prompt)') ?></label>
                            <textarea id="lesson-practice-prompt" class="input-field w-full" rows="3"
                                placeholder="<?= t('admin_task_prompt_placeholder', 'Промпт задачи') ?>"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="text-sm text-gray-600"><?= t('admin_starter_code', ' Код (starter_code)') ?></label>
                            <textarea id="lesson-practice-starter" class="input-field w-full" rows="6" data-no-tinymce="true"
                                placeholder="<?= t('admin_starter_placeholder', 'Стартер') ?>"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <div class="flex items-center justify-between gap-3">
                                <label
                                    class="text-sm text-gray-600"><?= t('admin_tests_json', 'Тест (tests_json)') ?></label>
                                <button type="button" class="btn-secondary text-xs" onclick="applyPracticeTemplate()">
                                    <?= t('admin_apply_template', 'Шаблон') ?>
                                </button>
                            </div>
                            <textarea id="lesson-practice-tests" class="input-field w-full" rows="6" data-no-tinymce="true"></textarea>
                            <div id="lesson-practice-tests-help" class="text-xs text-gray-500 mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button class="btn-primary" onclick="saveLesson()"><?= t('admin_save') ?></button>
                <button class="btn-secondary" onclick="closeModal('lessonModal')"><?= t('admin_cancel') ?></button>
            </div>
        </div>
    </div>

    <!-- Vacancy Modal -->
    <div id="vacancyModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('vacancyModal')"></div>
        <div class="relative max-w-3xl mx-auto mt-10 bg-white rounded-xl shadow-xl p-6 modal-sheet">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><?= t('admin_vacancy') ?></h2>
            <input type="hidden" id="vacancy-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_title_label') ?></label>
                    <input id="vacancy-title" class="input-field w-full" type="text">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_company') ?></label>
                    <input id="vacancy-company" class="input-field w-full" type="text">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_location') ?></label>
                    <input id="vacancy-location" class="input-field w-full" type="text">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_type') ?></label>
                    <select id="vacancy-type" class="input-field w-full">
                        <option value="remote"><?= t('vacancies_remote') ?></option>
                        <option value="office"><?= t('vacancies_office') ?></option>
                        <option value="hybrid"><?= t('vacancies_hybrid') ?></option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700"><?= t('admin_salary') ?></label>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_salary_from') ?></label>
                    <input id="vacancy-salary-min" class="input-field w-full" type="number" min="0">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_salary_to') ?></label>
                    <input id="vacancy-salary-max" class="input-field w-full" type="number" min="0">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_salary_currency') ?></label>
                    <select id="vacancy-salary-currency" class="input-field w-full">
                        <option value="TJS"><?= t('currency_tjs') ?></option>
                        <option value="RUB"><?= t('currency_rub') ?></option>
                        <option value="USD"><?= t('currency_usd') ?></option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_description') ?></label>
                    <textarea id="vacancy-description" class="input-field w-full" rows="4"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_about') ?></label>
                    <textarea id="vacancy-company-description" class="input-field w-full" rows="3"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_skills_csv') ?></label>
                    <input id="vacancy-skills" class="input-field w-full" type="text">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_requirements_lines') ?></label>
                    <textarea id="vacancy-requirements" class="input-field w-full" rows="3"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_pluses_lines') ?></label>
                    <textarea id="vacancy-pluses" class="input-field w-full" rows="3"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_responsibilities_lines') ?></label>
                    <textarea id="vacancy-responsibilities" class="input-field w-full" rows="3"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button class="btn-primary" onclick="saveVacancy()"><?= t('admin_save') ?></button>
                <button class="btn-secondary" onclick="closeModal('vacancyModal')"><?= t('admin_cancel') ?></button>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div id="notificationModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('notificationModal')"></div>
        <div class="relative max-w-xl mx-auto mt-10 bg-white rounded-xl shadow-xl p-6 modal-sheet">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><?= t('admin_notification') ?></h2>
            <div>
                <label class="text-sm text-gray-600"><?= t('admin_notification_user_id') ?></label>
                <input id="notification-user-id" class="input-field w-full" type="number" min="1">
            </div>
            <div class="mt-4">
                <label class="text-sm text-gray-600"><?= t('admin_message') ?></label>
                <textarea id="notification-message" class="input-field w-full" rows="4"></textarea>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button class="btn-primary" onclick="saveNotification()"><?= t('admin_save') ?></button>
                <button class="btn-secondary"
                    onclick="closeModal('notificationModal')"><?= t('admin_cancel') ?></button>
            </div>
        </div>
    </div>

    <!-- Roadmap Modal -->
    <div id="contestModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('contestModal')"></div>
        <div class="relative bg-white max-w-3xl mx-auto mt-10 rounded-xl shadow-xl p-6">
            <h3 class="text-lg font-semibold mb-4"><?= t('admin_contest', 'Контест') ?></h3>
            <input type="hidden" id="contest-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_title_label') ?></label>
                    <input id="contest-title" class="input-field w-full" type="text" required>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Slug</label>
                    <input id="contest-slug" class="input-field w-full" type="text" placeholder="algorithms-1">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_contest_start', 'Старт контеста') ?></label>
                    <input id="contest-starts-at" class="input-field w-full" type="datetime-local">
                </div>
                <div>
                    <label
                        class="text-sm text-gray-600"><?= t('admin_contest_duration', ' (Контекст)') ?></label>
                    <input id="contest-duration-minutes" class="input-field w-full" type="number" min="1">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_description') ?></label>
                    <textarea id="contest-description" class="input-field w-full" rows="4"></textarea>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_status') ?></label>
                    <select id="contest-active" class="input-field w-full">
                        <option value="1"><?= t('admin_active') ?></option>
                        <option value="0"><?= t('admin_blocked') ?></option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button class="btn-primary" onclick="saveContest()"><?= t('admin_save') ?></button>
                <button class="btn-secondary" onclick="closeModal('contestModal')"><?= t('admin_cancel') ?></button>
            </div>
        </div>
    </div>

    <div id="contestTaskModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('contestTaskModal')"></div>
        <div class="relative bg-white max-w-4xl mx-auto mt-10 rounded-xl shadow-xl p-6 modal-sheet">
            <h3 class="text-lg font-semibold mb-4"><?= t('admin_contest_task', 'Задача контеста') ?></h3>
            <input type="hidden" id="contest-task-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_contest', 'Контест') ?></label>
                    <select id="contest-task-contest-id" class="input-field w-full" required>
                        <?php foreach ($adminContestsForSelect as $contest): ?>
                            <option value="<?= (int) ($contest['id'] ?? 0) ?>">
                                <?= htmlspecialchars((string) ($contest['title'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_title_label') ?></label>
                    <input id="contest-task-title" class="input-field w-full" type="text" required>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_level') ?></label>
                    <select id="contest-task-difficulty" class="input-field w-full">
                        <option value="easy">easy</option>
                        <option value="medium">medium</option>
                        <option value="hard">hard</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_order') ?></label>
                    <input id="contest-task-order" class="input-field w-full" type="number" value="0">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('contest_time_limit', 'Time limit') ?></label>
                    <input id="contest-task-time-limit" class="input-field w-full" type="number" min="1" max="15" value="3">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('contest_memory_limit', 'Memory limit') ?></label>
                    <input id="contest-task-memory-limit" class="input-field w-full" type="number" min="32768" step="1024" value="262144">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_description') ?></label>
                    <textarea id="contest-task-statement" class="input-field w-full" rows="3" required></textarea>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('contest_input', 'Ввод') ?></label>
                    <textarea id="contest-task-input" class="input-field w-full" rows="2" data-no-tinymce="true"></textarea>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('contest_output', 'Вывод') ?></label>
                    <textarea id="contest-task-output" class="input-field w-full" rows="2" data-no-tinymce="true"></textarea>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Starter C++</label>
                    <textarea id="contest-task-starter-cpp" class="input-field w-full" rows="6" data-no-tinymce="true"></textarea>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Starter Python</label>
                    <textarea id="contest-task-starter-python" class="input-field w-full" rows="6" data-no-tinymce="true"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600">Tests JSON</label>
                    <textarea id="contest-task-tests-json" class="input-field w-full" rows="5" data-no-tinymce="true"
                        required>[{"stdin":"","expected_stdout":""}]</textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button class="btn-primary" onclick="saveContestTask()"><?= t('admin_save') ?></button>
                <button class="btn-secondary" onclick="closeModal('contestTaskModal')"><?= t('admin_cancel') ?></button>
            </div>
        </div>
    </div>

    <div id="interviewPrepModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('interviewPrepModal')"></div>
        <div class="relative bg-white max-w-4xl mx-auto mt-10 rounded-xl shadow-xl p-6 modal-sheet">
            <h3 class="text-lg font-semibold mb-4"><?= t('admin_interview_prep_task', 'Задача подготовки') ?></h3>
            <input type="hidden" id="interview-prep-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_title_label') ?></label>
                    <input id="interview-prep-title" class="input-field w-full" type="text" required>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Slug</label>
                    <input id="interview-prep-slug" class="input-field w-full" type="text" placeholder="two-sum">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_category', 'Категория') ?></label>
                    <input id="interview-prep-category" class="input-field w-full" type="text" value="General">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_level') ?></label>
                    <select id="interview-prep-difficulty" class="input-field w-full">
                        <option value="easy">easy</option>
                        <option value="medium">medium</option>
                        <option value="hard">hard</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_order') ?></label>
                    <input id="interview-prep-order" class="input-field w-full" type="number" value="0">
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_status') ?></label>
                    <select id="interview-prep-active" class="input-field w-full">
                        <option value="1"><?= t('admin_active') ?></option>
                        <option value="0"><?= t('admin_blocked') ?></option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_description') ?></label>
                    <textarea id="interview-prep-statement" class="input-field w-full" rows="3" required></textarea>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('contest_input', 'Ввод') ?></label>
                    <textarea id="interview-prep-input" class="input-field w-full" rows="2" data-no-tinymce="true"></textarea>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('contest_output', 'Вывод') ?></label>
                    <textarea id="interview-prep-output" class="input-field w-full" rows="2" data-no-tinymce="true"></textarea>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Starter C++</label>
                    <textarea id="interview-prep-starter-cpp" class="input-field w-full" rows="6" data-no-tinymce="true"></textarea>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Starter Python</label>
                    <textarea id="interview-prep-starter-python" class="input-field w-full" rows="6" data-no-tinymce="true"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600">Tests JSON</label>
                    <textarea id="interview-prep-tests-json" class="input-field w-full" rows="5" data-no-tinymce="true"
                        required>[{"stdin":"","expected_stdout":""}]</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_source_task', 'ID задачи-источника (опционально)') ?></label>
                    <input id="interview-prep-source-task-id" class="input-field w-full" type="number" min="0" value="">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button class="btn-primary" onclick="saveInterviewPrepTask()"><?= t('admin_save') ?></button>
                <button class="btn-secondary" onclick="closeModal('interviewPrepModal')"><?= t('admin_cancel') ?></button>
            </div>
        </div>
    </div>

    <div id="roadmapModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('roadmapModal')"></div>
        <div class="relative bg-white rounded-lg max-w-xl w-full mx-auto mt-20 p-6">
            <h3 class="text-lg font-semibold mb-4"><?= t('admin_roadmap') ?></h3>
            <input type="hidden" id="roadmap-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1"><?= t('admin_title_label') ?></label>
                    <input id="roadmap-title" class="input-field w-full" type="text" required
                        placeholder="<?= t('admin_roadmap_placeholder') ?>">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1"><?= t('admin_description') ?></label>
                    <textarea id="roadmap-description" class="input-field w-full" rows="4"
                        placeholder="<?= t('admin_roadmap_desc_placeholder') ?>"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button class="btn-primary" onclick="saveRoadmap()"><?= t('admin_save') ?></button>
                <button class="btn-secondary" onclick="closeModal('roadmapModal')"><?= t('admin_cancel') ?></button>
            </div>
        </div>
    </div>

    <!-- Roadmap Node Modal -->
    <div id="roadmapNodeModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('roadmapNodeModal')"></div>
        <div class="relative max-w-2xl mx-auto mt-10 bg-white rounded-xl shadow-xl p-6 modal-sheet">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><?= t('admin_roadmap_node') ?></h2>
            <input type="hidden" id="roadmap-node-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_title_label') ?></label>
                    <input id="roadmap-node-title" class="input-field w-full" type="text" required>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_roadmap_title') ?></label>
                    <input id="roadmap-node-roadmap-title" class="input-field w-full" type="text" required
                        placeholder="<?= t('admin_roadmap_placeholder') ?>" list="roadmap-title-list">
                    <datalist id="roadmap-title-list">
                        <?php foreach ($roadmapListForSelect as $rm): ?>
                            <option value="<?= htmlspecialchars($rm['title'] ?? '') ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_topic') ?></label>
                    <input id="roadmap-node-topic" class="input-field w-full" type="text"
                        placeholder="<?= t('admin_topic_placeholder') ?>">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_materials') ?></label>
                    <textarea id="roadmap-node-materials" class="input-field w-full" rows="4"
                        placeholder="<?= t('admin_materials_placeholder') ?>"></textarea>
                </div>
                <div>
                    <label class="text-sm text-gray-600">X</label>
                    <input id="roadmap-node-x" class="input-field w-full" type="number">
                </div>
                <div>
                    <label class="text-sm text-gray-600">Y</label>
                    <input id="roadmap-node-y" class="input-field w-full" type="number">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_deps') ?></label>
                    <input id="roadmap-node-deps" class="input-field w-full" type="text">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600">
                        <input id="roadmap-node-exam" type="checkbox" class="mr-2"> <?= t('admin_exam') ?>
                    </label>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button class="btn-primary" onclick="saveRoadmapNode()"><?= t('admin_save') ?></button>
                <button class="btn-secondary" onclick="closeModal('roadmapNodeModal')"><?= t('admin_cancel') ?></button>
            </div>
        </div>
    </div>

    <!-- Roadmap Lesson Modal -->
    <div id="roadmapLessonModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('roadmapLessonModal')"></div>
        <div class="relative max-w-2xl mx-auto mt-10 bg-white rounded-xl shadow-xl p-6 modal-sheet">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><?= t('admin_roadmap_lesson') ?></h2>
            <input type="hidden" id="roadmap-lesson-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_node') ?></label>
                    <select id="roadmap-lesson-node" class="input-field w-full" required>
                        <?php foreach ($roadmapNodesForSelect as $node): ?>
                            <option value="<?= (int) $node['id'] ?>"><?= htmlspecialchars($node['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600"><?= t('admin_order') ?></label>
                    <input id="roadmap-lesson-order" class="input-field w-full" type="number">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_title_label') ?></label>
                    <input id="roadmap-lesson-title" class="input-field w-full" type="text" required>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_video_url') ?></label>
                    <input id="roadmap-lesson-video" class="input-field w-full" type="text">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_description') ?></label>
                    <textarea id="roadmap-lesson-description" class="input-field w-full" rows="4"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_materials_lines') ?></label>
                    <textarea id="roadmap-lesson-materials" class="input-field w-full" rows="4"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button class="btn-primary" onclick="saveRoadmapLesson()"><?= t('admin_save') ?></button>
                <button class="btn-secondary"
                    onclick="closeModal('roadmapLessonModal')"><?= t('admin_cancel') ?></button>
            </div>
        </div>
    </div>

    <!-- Roadmap Quiz Modal -->
    <div id="roadmapQuizModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('roadmapQuizModal')"></div>
        <div class="relative max-w-2xl mx-auto mt-10 bg-white rounded-xl shadow-xl p-6 modal-sheet">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><?= t('admin_roadmap_question') ?></h2>
            <input type="hidden" id="roadmap-quiz-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_node') ?></label>
                    <select id="roadmap-quiz-node" class="input-field w-full">
                        <?php foreach ($roadmapNodesForSelect as $node): ?>
                            <option value="<?= (int) $node['id'] ?>"><?= htmlspecialchars($node['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_question') ?></label>
                    <textarea id="roadmap-quiz-question" class="input-field w-full" rows="3" required></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_options_lines') ?></label>
                    <textarea id="roadmap-quiz-options" class="input-field w-full" rows="4" required></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600"><?= t('admin_correct_answer') ?></label>
                    <select id="roadmap-quiz-correct" class="input-field w-full" required>
                        <option value=""><?= t('admin_select', 'Выберите') ?></option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button class="btn-primary" onclick="saveRoadmapQuiz()"><?= t('admin_save') ?></button>
                <button class="btn-secondary" onclick="closeModal('roadmapQuizModal')"><?= t('admin_cancel') ?></button>
            </div>
        </div>
    </div>

    <div id="submissionDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('submissionDetailModal')"></div>
        <div class="relative max-w-4xl mx-auto mt-10 bg-white rounded-xl shadow-xl p-6 modal-sheet">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900"><?= t('admin_submission_detail', 'Детали посылки') ?></h2>
                    <p id="submission-detail-meta" class="text-sm text-gray-500 mt-1"></p>
                </div>
                <button class="text-gray-400 hover:text-gray-700" onclick="closeModal('submissionDetailModal')">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="submission-detail-stats" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-5"></div>
            <div class="mb-5">
                <div class="text-sm font-semibold text-gray-900 mb-2"><?= t('admin_solution_code', 'Код решения') ?></div>
                <pre id="submission-detail-code" class="bg-slate-950 text-slate-100 rounded-xl p-4 overflow-auto text-xs leading-6 max-h-[320px]"></pre>
            </div>
            <div>
                <div class="text-sm font-semibold text-gray-900 mb-2"><?= t('admin_submission_logs', 'Результат и логи') ?></div>
                <pre id="submission-detail-output" class="bg-slate-50 text-slate-800 rounded-xl p-4 overflow-auto text-xs leading-6 max-h-[240px] border border-slate-200"></pre>
            </div>
        </div>
    </div>

    <script>
            (function () {
                const toggle = document.getElementById('admin-menu-toggle');
                const overlay = document.getElementById('admin-sidebar-overlay');
                if (!toggle || !overlay) return;
                const close = () => document.body.classList.remove('admin-sidebar-open');
                toggle.addEventListener('click', () => {
                    document.body.classList.toggle('admin-sidebar-open');
                });
                overlay.addEventListener('click', close);
            })();

        const tfI18n = {
            fileRequired: '<?= t('admin_file_required') ?>',
            uploadError: '<?= t('admin_upload_error') ?>',
            uploadSuccess: '<?= t('admin_upload_success') ?>',
            error: '<?= t('admin_error') ?>',
            done: '<?= t('admin_done') ?>',
            passwordPrefix: '<?= t('admin_password_prefix') ?>',
            confirmRoleChange: '<?= t('admin_confirm_role_change') ?>',
            confirmBlock: '<?= t('admin_confirm_block_user') ?>',
            confirmUnblock: '<?= t('admin_confirm_unblock_user') ?>',
            confirmDeleteUser: '<?= t('admin_confirm_delete_user') ?>',
            confirmResetSolvedTasks: '<?= t('admin_confirm_reset_solved_tasks', 'Сбросить все посылки пользователя и прогресс контестов?') ?>',
            confirmResetContestSubmission: '<?= t('admin_confirm_reset_contest_submission', 'Сбросить это решение?') ?>',
            confirmDeleteCourse: '<?= t('admin_confirm_delete_course') ?>',
            confirmDeleteLesson: '<?= t('admin_confirm_delete_lesson') ?>',
            confirmDeleteVacancy: '<?= t('admin_confirm_delete_vacancy') ?>',
            confirmDeleteNotification: '<?= t('admin_confirm_delete_notification') ?>',
            confirmDeleteContest: '<?= t('admin_confirm_delete_contest') ?>',
            confirmDeleteContestTask: '<?= t('admin_confirm_delete_contest_task') ?>',
            confirmDeleteInterviewPrep: '<?= t('admin_confirm_delete_interview_prep', 'Удалить задачу подготовки?') ?>',
            confirmImportInterviewPrep: '<?= t('admin_confirm_import_interview_prep', 'Импортировать задачи из папок 123 и A?') ?>',
            confirmDeleteRoadmap: '<?= t('admin_confirm_delete_roadmap') ?>',
            confirmDeleteNode: '<?= t('admin_confirm_delete_node') ?>',
            confirmDeleteRoadmapLesson: '<?= t('admin_confirm_delete_roadmap_lesson') ?>',
            confirmDeleteQuestion: '<?= t('admin_confirm_delete_question') ?>',
            confirmSeedLearningPack: '<?= t('admin_confirm_seed_learning_pack') ?>',
            tableSearchPlaceholder: '<?= t('admin_table_search_placeholder') ?>',
            tableNoResults: '<?= t('admin_table_no_results') ?>',
            tablePrev: '<?= t('admin_table_prev') ?>',
            tableNext: '<?= t('admin_table_next') ?>',
            tablePage: '<?= t('admin_table_page') ?>',
            tableShowing: '<?= t('admin_table_showing') ?>',
            msgInvalidData: '<?= t('admin_msg_invalid_data') ?>',
            msgInvalidId: '<?= t('admin_msg_invalid_id') ?>',
            msgNotFound: '<?= t('admin_msg_not_found') ?>',
            msgNoDataToUpdate: '<?= t('admin_msg_no_data_to_update') ?>',
            msgTitleRequired: '<?= t('admin_msg_title_required') ?>',
            msgAccessDenied: '<?= t('admin_msg_access_denied') ?>',
            msgUnknownAction: '<?= t('admin_msg_unknown_action') ?>',
            msgUnauthorized: '<?= t('admin_msg_unauthorized') ?>',
            msgUpdated: '<?= t('admin_msg_updated') ?>',
            msgDeleted: '<?= t('admin_msg_deleted') ?>',
            msgCreated: '<?= t('admin_msg_created') ?>',
            courseLevelDefault: '<?= t('courses_level_beginner') ?>',
            examQuestion: '<?= t('admin_exam_question') ?>',
            examOption1: '<?= t('admin_exam_option1') ?>',
            examOption2: '<?= t('admin_exam_option2') ?>',
            examOption3: '<?= t('admin_exam_option3') ?>',
            examOption4: '<?= t('admin_exam_option4') ?>',
            examCorrect: '<?= t('admin_exam_correct') ?>',
            remove: '<?= t('admin_remove') ?>',
            material: '<?= t('admin_material') ?>',
            defaultRoadmap: '<?= t('admin_default') ?>',
            requiredFields: '<?= t('admin_required_fields', 'Заполните обязательные поля') ?>',
            invalidTestsJson: '<?= t('admin_invalid_tests_json', 'Некорректный tests_json') ?>',
            invalidQuizOptions: '<?= t('admin_invalid_quiz_options', 'Добавьте минимум 2 варианта ответа') ?>',
            invalidCorrectAnswer: '<?= t('admin_invalid_correct_answer', 'Выберите корректный правильный ответ') ?>'
        };
        const adminContext = {
            courseId: <?= (int) $adminCourseId ?>,
            contestId: <?= (int) $adminContestId ?>,
            roadmapTitle: <?= tfSafeJson($adminRoadmapTitle, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
        };

        const PRACTICE_KIND_LANGUAGES = {
            code: [
                { value: 'python', label: 'Python' },
                { value: 'cpp', label: 'C++' },
                { value: 'c', label: 'C' },
                { value: 'csharp', label: 'C#' },
                { value: 'java', label: 'Java' },
                { value: 'js', label: 'JavaScript' }
            ],
            sql: [
                { value: 'mysql', label: 'MySQL (SQL)' },
                { value: 'pgsql', label: 'PostgreSQL (SQL)' }
            ],
            fill: [
                { value: 'fill', label: 'Fill-in-the-blanks' }
            ]
        };

        const PRACTICE_KIND_HELP = {
            code: 'Формат tests_json: [{"stdin":"...","expected_stdout":"...","timeout_sec":2}]',
            sql: 'Формат tests_json: [{"setup_sql":["..."],"expected_sql":"SELECT ...","allow":["select","with"]}]',
            fill: 'Формат tests_json: [{"answers":["token1","token2"]}]'
        };

        const PRACTICE_KIND_TEMPLATES = {
            code: {
                starter: "def solve():\n    # TODO\n    pass\n\nif __name__ == '__main__':\n    solve()\n",
                tests: '[{"stdin":"2 3\\n","expected_stdout":"5"}]'
            },
            sql: {
                starter: "SELECT * FROM table_name;",
                tests: '[{"setup_sql":["DROP TABLE IF EXISTS t","CREATE TEMP TABLE t (id INT)","INSERT INTO t (id) VALUES (1),(2)"],"expected_sql":"SELECT COUNT(*) FROM t","allow":["select","with"]}]'
            },
            fill: {
                starter: "if (___) {\n    return ___;\n}",
                tests: '[{"answers":["x > 0","x"]}]'
            }
        };

        function inferPracticeKind(language) {
            if (language === 'mysql' || language === 'pgsql') return 'sql';
            if (language === 'fill') return 'fill';
            return 'code';
        }

        function getPracticeLanguageForKind(kind, preferredLanguage) {
            const list = PRACTICE_KIND_LANGUAGES[kind] || PRACTICE_KIND_LANGUAGES.code;
            if (preferredLanguage && list.some(item => item.value === preferredLanguage)) {
                return preferredLanguage;
            }
            return list[0].value;
        }

        function renderPracticeLanguageOptions(kind, preferredLanguage = '') {
            const select = document.getElementById('lesson-practice-language');
            if (!select) return;
            const list = PRACTICE_KIND_LANGUAGES[kind] || PRACTICE_KIND_LANGUAGES.code;
            const selectedValue = getPracticeLanguageForKind(kind, preferredLanguage || select.value);
            select.innerHTML = list
                .map(item => `<option value="${item.value}">${item.label}</option>`)
                .join('');
            select.value = selectedValue;
        }

        function updatePracticeTestsHelp(kind) {
            const help = document.getElementById('lesson-practice-tests-help');
            const tests = document.getElementById('lesson-practice-tests');
            if (help) help.textContent = PRACTICE_KIND_HELP[kind] || PRACTICE_KIND_HELP.code;
            if (tests) {
                const tpl = PRACTICE_KIND_TEMPLATES[kind] || PRACTICE_KIND_TEMPLATES.code;
                tests.placeholder = tpl.tests;
            }
        }

        function syncPracticeTaskKind(preferredLanguage = '') {
            const kindEl = document.getElementById('lesson-practice-kind');
            if (!kindEl) return;
            const kind = (kindEl.value in PRACTICE_KIND_LANGUAGES) ? kindEl.value : 'code';
            kindEl.value = kind;
            renderPracticeLanguageOptions(kind, preferredLanguage);
            updatePracticeTestsHelp(kind);
        }

        function applyPracticeTemplate() {
            const kindEl = document.getElementById('lesson-practice-kind');
            const starter = document.getElementById('lesson-practice-starter');
            const tests = document.getElementById('lesson-practice-tests');
            if (!kindEl || !starter || !tests) return;
            const kind = (kindEl.value in PRACTICE_KIND_TEMPLATES) ? kindEl.value : 'code';
            const tpl = PRACTICE_KIND_TEMPLATES[kind];
            starter.value = tpl.starter;
            tests.value = tpl.tests;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const lessonType = document.getElementById('lesson-type');
            if (lessonType) lessonType.addEventListener('change', toggleLessonType);
            const practiceToggle = document.getElementById('lesson-practice-enabled');
            if (practiceToggle) practiceToggle.addEventListener('change', toggleLessonPractice);
            const practiceKind = document.getElementById('lesson-practice-kind');
            if (practiceKind) {
                practiceKind.addEventListener('change', () => syncPracticeTaskKind());
            }
            const practiceLanguage = document.getElementById('lesson-practice-language');
            if (practiceLanguage) {
                practiceLanguage.addEventListener('change', () => {
                    const kindEl = document.getElementById('lesson-practice-kind');
                    if (!kindEl) return;
                    const inferred = inferPracticeKind(practiceLanguage.value);
                    if ((inferred === 'sql' || inferred === 'fill') && kindEl.value !== inferred) {
                        kindEl.value = inferred;
                    }
                    updatePracticeTestsHelp(kindEl.value);
                });
            }
            const roadmapQuizOptions = document.getElementById('roadmap-quiz-options');
            if (roadmapQuizOptions) {
                roadmapQuizOptions.addEventListener('input', () => {
                    syncRoadmapQuizCorrectOptions();
                });
            }
            syncPracticeTaskKind('python');
            toggleLessonPractice();
            initAdminDataTables();
        });

        window.addEventListener('unhandledrejection', (event) => {
            const msg = (event && event.reason && event.reason.message) ? event.reason.message : tfI18n.error;
            tfNotify(msg || tfI18n.error);
        });

        function closeModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.add('hidden');
            el.classList.remove('active');
        }

        function openModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.remove('hidden');
            el.classList.add('active');
        }

        async function openSubmissionDetail(kind, id) {
            if (!kind || !id) return;
            const metaEl = document.getElementById('submission-detail-meta');
            const statsEl = document.getElementById('submission-detail-stats');
            const codeEl = document.getElementById('submission-detail-code');
            const outputEl = document.getElementById('submission-detail-output');
            if (!metaEl || !statsEl || !codeEl || !outputEl) return;

            metaEl.textContent = 'Загружаем...';
            statsEl.innerHTML = '';
            codeEl.textContent = '';
            outputEl.textContent = '';
            openModal('submissionDetailModal');

            try {
                const res = await fetch(`?action=admin-submission-detail&kind=${encodeURIComponent(kind)}&id=${encodeURIComponent(String(id))}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (!data || !data.success || !data.submission) {
                    throw new Error((data && data.message) ? data.message : 'Не удалось загрузить посылку');
                }
                const s = data.submission;
                const titleParts = [s.user_name, s.contest_title || s.course_title, s.task_title].filter(Boolean);
                metaEl.textContent = titleParts.join(' • ');
                const statItems = [
                    { label: 'Статус', value: s.status || (s.passed ? 'passed' : 'failed') },
                    { label: 'Язык', value: s.language || '-' },
                    { label: 'Проверки', value: `${parseInt(s.checks_passed || 0, 10)}/${parseInt(s.checks_total || 0, 10)}` },
                    { label: 'Баллы', value: String(parseInt(s.points_awarded || 0, 10)) }
                ];
                statsEl.innerHTML = '';
                statItems.forEach((item) => {
                    const card = document.createElement('div');
                    card.className = 'rounded-xl border border-gray-200 bg-gray-50 px-4 py-3';
                    const labelEl = document.createElement('div');
                    labelEl.className = 'text-xs uppercase tracking-wide text-gray-400';
                    labelEl.textContent = String(item.label);
                    const valueEl = document.createElement('div');
                    valueEl.className = 'mt-1 text-sm font-semibold text-gray-900';
                    valueEl.textContent = String(item.value);
                    card.appendChild(labelEl);
                    card.appendChild(valueEl);
                    statsEl.appendChild(card);
                });
                codeEl.textContent = String(s.code || '');

                const outputParts = [];
                if (s.created_at) outputParts.push(`Created: ${s.created_at}`);
                if (s.stdout) outputParts.push(`STDOUT:\n${s.stdout}`);
                if (s.stderr) outputParts.push(`STDERR:\n${s.stderr}`);
                if (s.details && Object.keys(s.details).length) {
                    outputParts.push(`DETAILS:\n${JSON.stringify(s.details, null, 2)}`);
                }
                outputEl.textContent = outputParts.join('\n\n') || 'Нет дополнительных данных';
            } catch (e) {
                metaEl.textContent = e && e.message ? e.message : 'Не удалось загрузить посылку';
            }
        }

        async function resetContestSubmission(id) {
            if (!id) return;
            const ok = await tfConfirm(tfI18n.confirmResetContestSubmission);
            if (!ok) return;
            try {
                const res = await fetch(`?action=admin-reset-contest-submission&id=${encodeURIComponent(String(id))}`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (!data.success) {
                    tfNotify(data.message || tfI18n.error);
                    return;
                }
                tfNotify(data.message || tfI18n.done);
                window.location.reload();
            } catch (e) {
                tfNotify((e && e.message) ? e.message : tfI18n.error);
            }
        }

        function parseList(value) {
            return value
                .split(/\n|,/)
                .map(s => s.trim())
                .filter(Boolean);
        }

        function ensureRequiredFields(fields) {
            for (const field of fields) {
                const el = document.getElementById(field.id);
                if (!el) continue;
                const value = typeof el.value === 'string' ? el.value.trim() : String(el.value || '').trim();
                if (value !== '') {
                    el.classList.remove('ring-2', 'ring-red-300');
                    continue;
                }
                el.classList.add('ring-2', 'ring-red-300');
                tfNotify(`${tfI18n.requiredFields}: ${field.label}`);
                if (typeof el.focus === 'function') el.focus();
                return false;
            }
            return true;
        }

        function parseContestDateTimeForInput(value) {
            const source = String(value || '').trim();
            if (!source) return '';
            const normalized = source.replace(' ', 'T');
            return normalized.length >= 16 ? normalized.slice(0, 16) : normalized;
        }

        function syncRoadmapQuizCorrectOptions(preferred = '') {
            const optionsEl = document.getElementById('roadmap-quiz-options');
            const correctEl = document.getElementById('roadmap-quiz-correct');
            if (!optionsEl || !correctEl) return [];
            const options = optionsEl.value
                .split('\n')
                .map(s => s.trim())
                .filter(Boolean);
            const prevValue = String(preferred || correctEl.value || '').trim();
            const effectiveValue = options.includes(prevValue) ? prevValue : (options[0] || '');
            correctEl.innerHTML = '';
            const emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.textContent = tfI18n.invalidCorrectAnswer;
            correctEl.appendChild(emptyOption);
            options.forEach((opt, idx) => {
                const option = document.createElement('option');
                option.value = opt;
                option.textContent = `${idx + 1}. ${opt}`;
                correctEl.appendChild(option);
            });
            correctEl.value = effectiveValue;
            return options;
        }

        function localizeAdminApiMessage(message) {
            const source = String(message || '').trim();
            if (!source) return source;
            const text = source.replace(/\s+/g, ' ');
            const patterns = [
                [/Неверные данные|Некорректные данные/i, tfI18n.msgInvalidData],
                [/Неверный ID|\?{4,} ID/i, tfI18n.msgInvalidId],
                [/не найден|не найдена|не найдено/i, tfI18n.msgNotFound],
                [/Нет данных для обновления/i, tfI18n.msgNoDataToUpdate],
                [/Название обязательно/i, tfI18n.msgTitleRequired],
                [/Доступ запрещен|Доступ запрещён/i, tfI18n.msgAccessDenied],
                [/Unauthorized|не авторизован|пользователь не авторизован/i, tfI18n.msgUnauthorized],
                [/Неизвестное действие/i, tfI18n.msgUnknownAction]
            ];
            for (const [regex, localized] of patterns) {
                if (regex.test(text)) return localized;
            }
            if (/создан|создана|создано|добавлен|добавлена|добавлено/i.test(text)) {
                return tfI18n.msgCreated;
            }
            if (/обновл|изменен|изменён/i.test(text)) {
                return tfI18n.msgUpdated;
            }
            if (/удален|удалена|удалено|удалён/i.test(text)) {
                return tfI18n.msgDeleted;
            }
            const errMatch = text.match(/^(?:Ошибка|Error)\s*:\s*(.+)$/i);
            if (errMatch && errMatch[1]) {
                return `${tfI18n.error}: ${errMatch[1]}`;
            }
            return text;
        }

        function initAdminDataTables() {
            const wrappers = Array.from(document.querySelectorAll('.responsive-table-wrapper'));
            wrappers.forEach((wrapper) => {
                const table = wrapper.querySelector('table');
                if (!table || table.dataset.tfEnhanced === '1' || table.dataset.serverPaginated === '1') return;
                const tbody = table.tBodies && table.tBodies[0];
                if (!tbody) return;
                const rows = Array.from(tbody.querySelectorAll('tr'));
                if (!rows.length) return;

                table.dataset.tfEnhanced = '1';
                const pageSizeRaw = parseInt(table.dataset.pageSize || '10', 10);
                const pageSize = Number.isFinite(pageSizeRaw) && pageSizeRaw > 0 ? pageSizeRaw : 10;
                const textCache = new Map(rows.map((row) => [row, row.textContent.toLowerCase()]));
                const columnsCount = (table.tHead && table.tHead.rows[0] && table.tHead.rows[0].cells.length) || 1;
                const container = wrapper.parentElement;
                if (!container) return;

                const tools = document.createElement('div');
                tools.className = 'admin-table-tools';
                tools.innerHTML = `
                    <div class="admin-table-search">
                        <i class="fas fa-search"></i>
                        <input type="search" placeholder="${tfI18n.tableSearchPlaceholder}" aria-label="${tfI18n.tableSearchPlaceholder}">
                    </div>
                    <div class="admin-table-meta" data-table-meta></div>
                `;
                container.insertBefore(tools, wrapper);

                const pager = document.createElement('div');
                pager.className = 'admin-table-pagination';
                pager.innerHTML = `
                    <button type="button" class="admin-table-page-btn" data-page-prev>${tfI18n.tablePrev}</button>
                    <span class="admin-table-page-info" data-page-info></span>
                    <button type="button" class="admin-table-page-btn" data-page-next>${tfI18n.tableNext}</button>
                `;
                if (wrapper.nextSibling) {
                    container.insertBefore(pager, wrapper.nextSibling);
                } else {
                    container.appendChild(pager);
                }

                const searchInput = tools.querySelector('input');
                const metaEl = tools.querySelector('[data-table-meta]');
                const prevBtn = pager.querySelector('[data-page-prev]');
                const nextBtn = pager.querySelector('[data-page-next]');
                const pageInfoEl = pager.querySelector('[data-page-info]');

                let query = '';
                let currentPage = 1;
                let filteredRows = rows.slice();
                let emptyRow = null;

                function setEmptyState(show) {
                    if (!show) {
                        if (emptyRow) emptyRow.style.display = 'none';
                        return;
                    }
                    if (!emptyRow) {
                        emptyRow = document.createElement('tr');
                        emptyRow.innerHTML = `<td colspan="${columnsCount}" class="text-center text-sm text-gray-500 py-6">${tfI18n.tableNoResults}</td>`;
                        tbody.appendChild(emptyRow);
                    }
                    emptyRow.style.display = '';
                }

                function render() {
                    rows.forEach((row) => {
                        row.style.display = 'none';
                    });

                    filteredRows = query
                        ? rows.filter((row) => (textCache.get(row) || '').includes(query))
                        : rows.slice();

                    const totalItems = filteredRows.length;
                    const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
                    if (currentPage > totalPages) currentPage = totalPages;
                    if (currentPage < 1) currentPage = 1;

                    if (totalItems === 0) {
                        setEmptyState(true);
                        metaEl.textContent = tfI18n.tableNoResults;
                        pageInfoEl.textContent = `${tfI18n.tablePage} 0 / 0`;
                        prevBtn.disabled = true;
                        nextBtn.disabled = true;
                        return;
                    }

                    setEmptyState(false);
                    const from = (currentPage - 1) * pageSize;
                    const to = Math.min(from + pageSize, totalItems);
                    filteredRows.slice(from, to).forEach((row) => {
                        row.style.display = '';
                    });

                    metaEl.textContent = `${tfI18n.tableShowing}: ${from + 1}-${to} / ${totalItems}`;
                    pageInfoEl.textContent = `${tfI18n.tablePage} ${currentPage} / ${totalPages}`;
                    prevBtn.disabled = currentPage <= 1;
                    nextBtn.disabled = currentPage >= totalPages;
                }

                searchInput.addEventListener('input', () => {
                    query = searchInput.value.trim().toLowerCase();
                    currentPage = 1;
                    render();
                });
                prevBtn.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage -= 1;
                        render();
                    }
                });
                nextBtn.addEventListener('click', () => {
                    currentPage += 1;
                    render();
                });

                render();
            });
        }

        function parseAdminJson(response) {
            return response.text().then((raw) => {
                const text = String(raw || '').replace(/^\uFEFF/, '').trim();
                let data = null;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    const start = text.indexOf('{');
                    const end = text.lastIndexOf('}');
                    if (start !== -1 && end !== -1 && end > start) {
                        data = JSON.parse(text.slice(start, end + 1));
                    } else {
                        const plain = text
                            .replace(/<script[\s\S]*?<\/script>/gi, ' ')
                            .replace(/<style[\s\S]*?<\/style>/gi, ' ')
                            .replace(/<[^>]+>/g, ' ')
                            .replace(/\s+/g, ' ')
                            .trim();
                        const sqlMatch = plain.match(/SQLSTATE\[[^\]]+\]:\s*[^|<]+/i);
                        const fallback = sqlMatch
                            ? sqlMatch[0]
                            : (plain ? plain.slice(0, 220) : tfI18n.error);
                        throw new Error(fallback || tfI18n.error);
                    }
                }
                if (data && typeof data.message === 'string') {
                    const localized = localizeAdminApiMessage(data.message);
                    data.message = localized;
                    if (/[\u0401\u0409\u040E\u045E\u040F]/.test(localized) || /\?{3,}/.test(localized)) {
                        data.message = data.success ? tfI18n.done : tfI18n.error;
                    }
                }
                if (!response.ok) {
                    throw new Error((data && data.message) ? data.message : tfI18n.error);
                }
                return data;
            });
        }

        function uploadImageToInput(fileInputId, targetInputId) {
            const fileInput = document.getElementById(fileInputId);
            if (!fileInput || !fileInput.files || !fileInput.files[0]) {
                return tfNotify(tfI18n.fileRequired);
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
                        const serverMsg = (data && data.message) ? localizeAdminApiMessage(data.message) : tfI18n.uploadError;
                        return tfNotify(serverMsg || tfI18n.uploadError);
                    }
                    document.getElementById(targetInputId).value = data.url || '';
                    tfNotify(tfI18n.uploadSuccess);
                })
                .catch(() => tfNotify(tfI18n.uploadError));
        }

        function openAddUserModal() {
            document.getElementById('user-id').value = '';
            document.getElementById('user-email').value = '';
            document.getElementById('user-name').value = '';
            document.getElementById('user-role').value = 'seeker';
            document.getElementById('user-title').value = '';
            document.getElementById('user-location').value = '';
            document.getElementById('user-bio').value = '';
            document.getElementById('user-avatar').value = '';
            const userAvatarFile = document.getElementById('user-avatar-file');
            if (userAvatarFile) userAvatarFile.value = '';
            document.getElementById('user-password').value = '';
            openModal('userModal');
        }

        function editUser(userId) {
            fetch(`?action=admin-get-user&id=${userId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const u = data.user;
                    document.getElementById('user-id').value = u.id;
                    document.getElementById('user-email').value = u.email || '';
                    document.getElementById('user-name').value = u.name || '';
                    document.getElementById('user-role').value = u.role || 'seeker';
                    document.getElementById('user-title').value = u.title || '';
                    document.getElementById('user-location').value = u.location || '';
                    document.getElementById('user-bio').value = u.bio || '';
                    document.getElementById('user-avatar').value = u.avatar || '';
                    const userAvatarFile = document.getElementById('user-avatar-file');
                    if (userAvatarFile) userAvatarFile.value = '';
                    document.getElementById('user-password').value = '';
                    openModal('userModal');
                });
        }

        function saveUser() {
            const payload = {
                id: document.getElementById('user-id').value || null,
                email: document.getElementById('user-email').value.trim(),
                name: document.getElementById('user-name').value.trim(),
                role: document.getElementById('user-role').value,
                title: document.getElementById('user-title').value.trim(),
                location: document.getElementById('user-location').value.trim(),
                bio: document.getElementById('user-bio').value.trim(),
                avatar: document.getElementById('user-avatar').value.trim(),
                password: document.getElementById('user-password').value.trim()
            };
            const action = payload.id ? 'admin-update-user' : 'admin-create-user';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
                .then(parseAdminJson)
                .then(data => {
                    if (data.success) {
                        tfNotify(data.message || tfI18n.done);
                        if (data.password) tfNotify(`${tfI18n.passwordPrefix} ${data.password}`);
                        window.location.reload();
                    } else {
                        tfNotify(data.message || tfI18n.error);
                    }
                });
        }

        async function updateUserRole(userId, role) {
            const ok = await tfConfirm(tfI18n.confirmRoleChange);
            if (!ok) return;
            fetch(`?action=update-user-role&id=${userId}&role=${role}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        async function toggleUserBlock(userId, block) {
            const ok = await tfConfirm(block ? tfI18n.confirmBlock : tfI18n.confirmUnblock);
            if (!ok) return;
            fetch(`?action=toggle-user-block&id=${userId}&block=${block}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        async function deleteUser(userId) {
            const ok = await tfConfirm(tfI18n.confirmDeleteUser);
            if (!ok) return;
            fetch(`?action=admin-delete-user&id=${userId}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        async function resetUserContests(userId) {
            const ok = await tfConfirm(tfI18n.confirmResetSolvedTasks);
            if (!ok) return;
            fetch(`?action=admin-reset-user-contests&id=${userId}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        function openAddCourseModal() {
            document.getElementById('course-id').value = '';
            document.getElementById('course-title').value = '';
            document.getElementById('course-instructor').value = '';
            document.getElementById('course-description').value = '';
            document.getElementById('course-category').value = 'frontend';
            document.getElementById('course-level').value = tfI18n.courseLevelDefault;
            document.getElementById('course-progress').value = 0;
            document.getElementById('course-image').value = '';
            const courseImageFile = document.getElementById('course-image-file');
            if (courseImageFile) courseImageFile.value = '';
            openModal('courseModal');
        }

        async function seedLearningPack() {
            const ok = await tfConfirm(tfI18n.confirmSeedLearningPack);
            if (!ok) return;
            fetch(`?action=admin-seed-learning-pack`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({})
            })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    tfNotify(data.message || tfI18n.done);
                    window.location.reload();
                })
                .catch(() => tfNotify(tfI18n.error));
        }

        function editCourse(courseId) {
            fetch(`?action=admin-get-course&id=${courseId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const c = data.course;
                    document.getElementById('course-id').value = c.id;
                    document.getElementById('course-title').value = c.title || '';
                    document.getElementById('course-instructor').value = c.instructor || '';
                    document.getElementById('course-description').value = c.description || '';
                    document.getElementById('course-category').value = c.category || 'frontend';
                    document.getElementById('course-level').value = c.level || tfI18n.courseLevelDefault;
                    document.getElementById('course-progress').value = c.progress || 0;
                    document.getElementById('course-image').value = c.image_url || '';
                    const courseImageFile = document.getElementById('course-image-file');
                    if (courseImageFile) courseImageFile.value = '';
                    openModal('courseModal');
                });
        }

        function saveCourse() {
            const payload = {
                id: document.getElementById('course-id').value || null,
                title: document.getElementById('course-title').value.trim(),
                instructor: document.getElementById('course-instructor').value.trim(),
                description: document.getElementById('course-description').value.trim(),
                category: document.getElementById('course-category').value,
                level: document.getElementById('course-level').value,
                progress: parseInt(document.getElementById('course-progress').value, 10) || 0,
                image_url: document.getElementById('course-image').value.trim()
            };
            const action = payload.id ? 'admin-update-course' : 'admin-create-course';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
                .then(parseAdminJson)
                .then(data => {
                    if (data.success) {
                        tfNotify(data.message || tfI18n.done);
                        window.location.reload();
                    } else {
                        tfNotify(data.message || tfI18n.error);
                    }
                });
        }

        function openContestModal(contestId) {
            document.getElementById('contest-id').value = contestId || '';
            document.getElementById('contest-title').value = '';
            document.getElementById('contest-slug').value = '';
            document.getElementById('contest-starts-at').value = '';
            document.getElementById('contest-duration-minutes').value = '';
            document.getElementById('contest-description').value = '';
            document.getElementById('contest-active').value = '1';
            if (!contestId) {
                openModal('contestModal');
                return;
            }
            fetch(`?action=admin-get-contest&id=${contestId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const c = data.contest || {};
                    document.getElementById('contest-id').value = c.id || '';
                    document.getElementById('contest-title').value = c.title || '';
                    document.getElementById('contest-slug').value = c.slug || '';
                    document.getElementById('contest-starts-at').value = parseContestDateTimeForInput(c.starts_at || '');
                    document.getElementById('contest-duration-minutes').value = c.duration_minutes || '';
                    document.getElementById('contest-description').value = c.description || '';
                    document.getElementById('contest-active').value = String(parseInt(c.is_active, 10) ? 1 : 0);
                    openModal('contestModal');
                });
        }

        function saveContest() {
            if (!ensureRequiredFields([{ id: 'contest-title', label: 'Название контеста' }])) {
                return;
            }
            const payload = {
                id: document.getElementById('contest-id').value || null,
                title: document.getElementById('contest-title').value.trim(),
                slug: document.getElementById('contest-slug').value.trim(),
                starts_at: document.getElementById('contest-starts-at').value.trim(),
                duration_minutes: parseInt(document.getElementById('contest-duration-minutes').value, 10) || 0,
                description: document.getElementById('contest-description').value.trim(),
                is_active: document.getElementById('contest-active').value === '1'
            };
            const action = payload.id ? 'admin-update-contest' : 'admin-create-contest';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    tfNotify(data.message || tfI18n.done);
                    window.location.reload();
                });
        }

        async function deleteContest(contestId) {
            const ok = await tfConfirm(tfI18n.confirmDeleteContest);
            if (!ok) return;
            fetch(`?action=admin-delete-contest&id=${contestId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        function importContestTaskPackageToModal() {
            const pathEl = document.getElementById('contest-task-package-path');
            const packagePath = (pathEl?.value || '').trim();
            const contestId = parseInt(document.getElementById('contest-task-contest-id')?.value || '0', 10) || 0;
            if (!packagePath) {
                tfNotify('Укажите путь к папке задачи (где лежит statement.xml)');
                return;
            }
            fetch(`?action=admin-import-contest-task-package`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ package_path: packagePath, contest_id: contestId })
            })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) {
                        tfNotify(data.message || tfI18n.error);
                        return;
                    }
                    const task = data.task || {};
                    openContestTaskModal();
                    document.getElementById('contest-task-title').value = task.title || '';
                    document.getElementById('contest-task-difficulty').value = task.difficulty || 'easy';
                    document.getElementById('contest-task-order').value = Number(task.suggested_order_num || 0);
                    document.getElementById('contest-task-time-limit').value = Number(task.time_limit_sec || 3);
                    document.getElementById('contest-task-memory-limit').value = Number(task.memory_limit_kb || 262144);
                    document.getElementById('contest-task-statement').value = task.statement || '';
                    document.getElementById('contest-task-input').value = task.input_spec || '';
                    document.getElementById('contest-task-output').value = task.output_spec || '';
                    document.getElementById('contest-task-starter-cpp').value = task.starter_cpp || '';
                    document.getElementById('contest-task-starter-python').value = task.starter_python || '';
                    document.getElementById('contest-task-tests-json').value = task.tests_json || '[]';
                    tfNotify('XML загружен. Проверьте и нажмите "Сохранить".');
                })
                .catch((e) => tfNotify((e && e.message) ? e.message : tfI18n.error));
        }

        function openContestTaskModal(taskId) {
            document.getElementById('contest-task-id').value = taskId || '';
            document.getElementById('contest-task-contest-id').value = adminContext.contestId || '';
            document.getElementById('contest-task-title').value = '';
            document.getElementById('contest-task-difficulty').value = 'easy';
            document.getElementById('contest-task-order').value = 0;
            document.getElementById('contest-task-time-limit').value = 3;
            document.getElementById('contest-task-memory-limit').value = 262144;
            document.getElementById('contest-task-statement').value = '';
            document.getElementById('contest-task-input').value = '';
            document.getElementById('contest-task-output').value = '';
            document.getElementById('contest-task-starter-cpp').value = '';
            document.getElementById('contest-task-starter-python').value = '';
            document.getElementById('contest-task-tests-json').value = '[]';
            if (!taskId) {
                openModal('contestTaskModal');
                return;
            }
            fetch(`?action=admin-get-contest-task&id=${taskId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const t = data.task || {};
                    document.getElementById('contest-task-id').value = t.id || '';
                    document.getElementById('contest-task-contest-id').value = t.contest_id || '';
                    document.getElementById('contest-task-title').value = t.title || '';
                    document.getElementById('contest-task-difficulty').value = t.difficulty || 'easy';
                    document.getElementById('contest-task-order').value = t.order_num || 0;
                    document.getElementById('contest-task-time-limit').value = t.time_limit_sec || 3;
                    document.getElementById('contest-task-memory-limit').value = t.memory_limit_kb || 262144;
                    document.getElementById('contest-task-statement').value = t.statement || '';
                    document.getElementById('contest-task-input').value = t.input_spec || '';
                    document.getElementById('contest-task-output').value = t.output_spec || '';
                    document.getElementById('contest-task-starter-cpp').value = t.starter_cpp || '';
                    document.getElementById('contest-task-starter-python').value = t.starter_python || '';
                    document.getElementById('contest-task-tests-json').value = t.tests_json || '[]';
                    openModal('contestTaskModal');
                });
        }

        function saveContestTask() {
            if (!ensureRequiredFields([
                { id: 'contest-task-contest-id', label: 'Контест' },
{ id: 'contest-task-title', label: 'Название задачи' },
{ id: 'contest-task-statement', label: 'Условие' },
{ id: 'contest-task-tests-json', label: 'Tests JSON' }
            ])) {
                return;
            }
            const testsRaw = document.getElementById('contest-task-tests-json').value.trim() || '[]';
            let testsParsed = null;
            try {
                testsParsed = JSON.parse(testsRaw);
            } catch (e) {
                testsParsed = null;
            }
            if (!Array.isArray(testsParsed) || testsParsed.length === 0) {
                tfNotify(tfI18n.invalidTestsJson);
                return;
            }
            const payload = {
                id: document.getElementById('contest-task-id').value || null,
                contest_id: parseInt(document.getElementById('contest-task-contest-id').value, 10) || 0,
                title: document.getElementById('contest-task-title').value.trim(),
                difficulty: document.getElementById('contest-task-difficulty').value,
                order_num: parseInt(document.getElementById('contest-task-order').value, 10) || 0,
                time_limit_sec: parseInt(document.getElementById('contest-task-time-limit').value, 10) || 3,
                memory_limit_kb: parseInt(document.getElementById('contest-task-memory-limit').value, 10) || 262144,
                statement: document.getElementById('contest-task-statement').value.trim(),
                input_spec: document.getElementById('contest-task-input').value.trim(),
                output_spec: document.getElementById('contest-task-output').value.trim(),
                starter_cpp: document.getElementById('contest-task-starter-cpp').value,
                starter_python: document.getElementById('contest-task-starter-python').value,
                tests_json: JSON.stringify(testsParsed)
            };
            const action = payload.id ? 'admin-update-contest-task' : 'admin-create-contest-task';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    tfNotify(data.message || tfI18n.done);
                    window.location.reload();
                });
        }

        async function deleteContestTask(taskId) {
            const ok = await tfConfirm(tfI18n.confirmDeleteContestTask);
            if (!ok) return;
            fetch(`?action=admin-delete-contest-task&id=${taskId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        function openInterviewPrepTaskModal(taskId) {
            document.getElementById('interview-prep-id').value = taskId || '';
            document.getElementById('interview-prep-title').value = '';
            document.getElementById('interview-prep-slug').value = '';
            document.getElementById('interview-prep-category').value = 'General';
            document.getElementById('interview-prep-difficulty').value = 'easy';
            document.getElementById('interview-prep-order').value = 0;
            document.getElementById('interview-prep-active').value = '1';
            document.getElementById('interview-prep-statement').value = '';
            document.getElementById('interview-prep-input').value = '';
            document.getElementById('interview-prep-output').value = '';
            document.getElementById('interview-prep-starter-cpp').value = '';
            document.getElementById('interview-prep-starter-python').value = '';
            document.getElementById('interview-prep-tests-json').value = '[{"stdin":"","expected_stdout":""}]';
            document.getElementById('interview-prep-source-task-id').value = '';
            if (!taskId) {
                openModal('interviewPrepModal');
                return;
            }
            fetch(`?action=admin-get-interview-prep-task&id=${taskId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const t = data.task || {};
                    document.getElementById('interview-prep-id').value = t.id || '';
                    document.getElementById('interview-prep-title').value = t.title || '';
                    document.getElementById('interview-prep-slug').value = t.slug || '';
                    document.getElementById('interview-prep-category').value = t.category || 'General';
                    document.getElementById('interview-prep-difficulty').value = t.difficulty || 'easy';
                    document.getElementById('interview-prep-order').value = t.sort_order || 0;
                    document.getElementById('interview-prep-active').value = String(parseInt(t.is_active, 10) ? 1 : 0);
                    document.getElementById('interview-prep-statement').value = t.statement || '';
                    document.getElementById('interview-prep-input').value = t.input_spec || '';
                    document.getElementById('interview-prep-output').value = t.output_spec || '';
                    document.getElementById('interview-prep-starter-cpp').value = t.starter_cpp || '';
                    document.getElementById('interview-prep-starter-python').value = t.starter_python || '';
                    document.getElementById('interview-prep-tests-json').value = t.tests_json || '[]';
                    document.getElementById('interview-prep-source-task-id').value = t.source_task_id || '';
                    openModal('interviewPrepModal');
                });
        }

        function saveInterviewPrepTask() {
            if (!ensureRequiredFields([
                { id: 'interview-prep-title', label: 'Название задания' },
                { id: 'interview-prep-statement', label: 'Условие' },
                { id: 'interview-prep-tests-json', label: 'Tests JSON' }
            ])) {
                return;
            }
            const testsRaw = document.getElementById('interview-prep-tests-json').value.trim() || '[]';
            let testsParsed = null;
            try {
                testsParsed = JSON.parse(testsRaw);
            } catch (e) {
                testsParsed = null;
            }
            if (!Array.isArray(testsParsed) || testsParsed.length === 0) {
                tfNotify(tfI18n.invalidTestsJson);
                return;
            }
            const payload = {
                id: document.getElementById('interview-prep-id').value || null,
                title: document.getElementById('interview-prep-title').value.trim(),
                slug: document.getElementById('interview-prep-slug').value.trim(),
                category: document.getElementById('interview-prep-category').value.trim(),
                difficulty: document.getElementById('interview-prep-difficulty').value,
                sort_order: parseInt(document.getElementById('interview-prep-order').value, 10) || 0,
                is_active: document.getElementById('interview-prep-active').value === '1',
                statement: document.getElementById('interview-prep-statement').value.trim(),
                input_spec: document.getElementById('interview-prep-input').value.trim(),
                output_spec: document.getElementById('interview-prep-output').value.trim(),
                starter_cpp: document.getElementById('interview-prep-starter-cpp').value,
                starter_python: document.getElementById('interview-prep-starter-python').value,
                tests_json: JSON.stringify(testsParsed),
                source_task_id: parseInt(document.getElementById('interview-prep-source-task-id').value, 10) || 0
            };
            const action = payload.id ? 'admin-update-interview-prep-task' : 'admin-create-interview-prep-task';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    tfNotify(data.message || tfI18n.done);
                    window.location.reload();
                });
        }

        async function deleteInterviewPrepTask(taskId) {
            const ok = await tfConfirm(tfI18n.confirmDeleteInterviewPrep);
            if (!ok) return;
            fetch(`?action=admin-delete-interview-prep-task&id=${taskId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        async function importInterviewPrepFolders() {
            const ok = await tfConfirm(tfI18n.confirmImportInterviewPrep);
            if (!ok) return;
            fetch(`?action=admin-import-interview-prep-folders`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({})
            })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const added = Number(data.added || 0);
                    const skipped = Number(data.skipped || 0);
                    const errors = Array.isArray(data.errors) ? data.errors : [];
                    tfNotify(`Импорт: добавлено ${added}, пропущено ${skipped}`);
                    if (errors.length) {
                        tfNotify(errors.slice(0, 3).join(' | '));
                    }
                    window.location.reload();
                })
                .catch((e) => tfNotify((e && e.message) ? e.message : tfI18n.error));
        }

        const ejudgeI18n = {
            scanFound: '<?= t('admin_ejudge_found', 'Найдено задач') ?>',
            importDone: '<?= t('admin_ejudge_import_done', 'Импорт завершён') ?>',
            needTarget: '<?= t('admin_ejudge_need_target', 'Выберите хотя бы один тип импорта') ?>',
            needContest: '<?= t('admin_ejudge_need_contest', 'Выберите контест') ?>'
        };

        function getEjudgePaths() {
            const raw = (document.getElementById('ejudgePaths')?.value || '').trim();
            if (!raw) return [];
            return raw.split(/\r?\n/).map(line => line.trim()).filter(Boolean);
        }

        async function ejudgeScan() {
            const resultEl = document.getElementById('ejudgeResult');
            const previewEl = document.getElementById('ejudgePreview');
            if (resultEl) resultEl.textContent = tfI18n.loading;
            if (previewEl) previewEl.textContent = '';
            fetch(`?action=admin-ejudge-scan`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ paths: getEjudgePaths() })
            })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    if (resultEl) {
                        resultEl.textContent = `${ejudgeI18n.scanFound}: ${Number(data.found || 0)}`;
                    }
                    if (previewEl) {
                        const preview = Array.isArray(data.preview) ? data.preview : [];
                        previewEl.innerHTML = preview.length
                            ? preview.map(item => `<div>Найдено ${item.title || ''} <span class="text-xs text-gray-400">(${item.path || ''})</span></div>`).join('')
                            : '';
                    }
                    const errors = Array.isArray(data.errors) ? data.errors : [];
                    if (errors.length) {
                        tfNotify(errors.slice(0, 3).join(' | '));
                    }
                })
                .catch((e) => tfNotify((e && e.message) ? e.message : tfI18n.error));
        }

        async function ejudgeImport() {
            const importInterview = document.getElementById('ejudgeImportInterview')?.checked;
            const importContest = document.getElementById('ejudgeImportContest')?.checked;
            if (!importInterview && !importContest) {
                return tfNotify(ejudgeI18n.needTarget);
            }
            const contestId = Number(document.getElementById('ejudgeContestId')?.value || 0);
            if (importContest && !contestId) {
                return tfNotify(ejudgeI18n.needContest);
            }
            const category = (document.getElementById('ejudgeCategory')?.value || 'Ejudge').trim();
            const difficulty = document.getElementById('ejudgeDifficulty')?.value || 'easy';
            fetch(`?action=admin-ejudge-import`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({
                    paths: getEjudgePaths(),
                    import_interview: importInterview ? 1 : 0,
                    import_contest: importContest ? 1 : 0,
                    contest_id: contestId,
                    interview_category: category,
                    difficulty
                })
            })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const addedContest = Number(data.added_contest || 0);
                    const addedInterview = Number(data.added_interview || 0);
                    const skipped = Number(data.skipped || 0);
                    tfNotify(`${ejudgeI18n.importDone}: +${addedContest} контест, +${addedInterview} подготовка, получение ${skipped}`);
                    const errors = Array.isArray(data.errors) ? data.errors : [];
                    if (errors.length) {
                        tfNotify(errors.slice(0, 3).join(' | '));
                    }
                })
                .catch((e) => tfNotify((e && e.message) ? e.message : tfI18n.error));
        }

        function createExamQuestionBlock(data = {}) {
            const wrapper = document.createElement('div');
            wrapper.className = 'p-4 border border-gray-200 rounded-xl space-y-3';
            wrapper.innerHTML = `
        <div>
            <label class="text-sm text-gray-600">${tfI18n.examQuestion}</label>
            <input class="input-field w-full" data-exam-question type="text">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="text-sm text-gray-600">${tfI18n.examOption1}</label>
                <input class="input-field w-full" data-exam-option type="text">
            </div>
            <div>
                <label class="text-sm text-gray-600">${tfI18n.examOption2}</label>
                <input class="input-field w-full" data-exam-option type="text">
            </div>
            <div>
                <label class="text-sm text-gray-600">${tfI18n.examOption3}</label>
                <input class="input-field w-full" data-exam-option type="text">
            </div>
            <div>
                <label class="text-sm text-gray-600">${tfI18n.examOption4}</label>
                <input class="input-field w-full" data-exam-option type="text">
            </div>
        </div>
        <div class="flex items-center gap-3">
            <label class="text-sm text-gray-600">${tfI18n.examCorrect}</label>
            <select class="input-field w-24" data-exam-correct>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
            <button type="button" class="btn-secondary ml-auto" data-exam-remove>${tfI18n.remove}</button>
        </div>
    `;
            const q = wrapper.querySelector('[data-exam-question]');
            const opts = wrapper.querySelectorAll('[data-exam-option]');
            const correct = wrapper.querySelector('[data-exam-correct]');
            q.value = data.question || data.question_text || '';
            const dataOpts = data.options || (data.options_text || '').split('|||').filter(Boolean);
            opts.forEach((el, idx) => { el.value = dataOpts[idx] || ''; });
            correct.value = String(data.correct_index || data.correct_option || 1);
            wrapper.querySelector('[data-exam-remove]').addEventListener('click', () => wrapper.remove());
            return wrapper;
        }

        function setExamQuestions(questions) {
            const list = document.getElementById('exam-questions-list');
            if (!list) return;
            list.innerHTML = '';
            (questions || []).forEach(q => list.appendChild(createExamQuestionBlock(q)));
            if (!questions || questions.length === 0) {
                list.appendChild(createExamQuestionBlock());
            }
        }

        function addExamQuestion() {
            const list = document.getElementById('exam-questions-list');
            if (!list) return;
            list.appendChild(createExamQuestionBlock());
        }

        function openCourseExamModal(courseId) {
            document.getElementById('exam-course-id').value = courseId;
            document.getElementById('exam-time-limit').value = 45;
            document.getElementById('exam-pass-percent').value = 70;
            document.getElementById('exam-shuffle-q').checked = true;
            document.getElementById('exam-shuffle-o').checked = true;
            setExamQuestions([]);
            const rawField = document.getElementById('exam-json-raw');
            if (rawField) rawField.value = '';
            fetch(`?action=admin-get-course-exam&course_id=${courseId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const exam = data.exam || null;
                    if (exam) {
                        document.getElementById('exam-time-limit').value = exam.time_limit_minutes || 45;
                        document.getElementById('exam-pass-percent').value = exam.pass_percent || 70;
                        document.getElementById('exam-shuffle-q').checked = !!exam.shuffle_questions;
                        document.getElementById('exam-shuffle-o').checked = !!exam.shuffle_options;
                        let questions = [];
                        try { questions = JSON.parse(exam.exam_json || '[]'); } catch (e) { }
                        setExamQuestions(questions);
                        if (rawField) {
                            try { rawField.value = JSON.stringify(questions, null, 2); } catch (e) { rawField.value = ''; }
                        }
                    }
                    openModal('courseExamModal');
                });
        }

        function saveCourseExam() {
            const courseId = parseInt(document.getElementById('exam-course-id').value, 10) || 0;
            const list = document.getElementById('exam-questions-list');
            const blocks = Array.from(list.children);
            const questions = blocks.map(block => {
                const question = block.querySelector('[data-exam-question]').value.trim();
                const options = Array.from(block.querySelectorAll('[data-exam-option]'))
                    .map(el => el.value.trim())
                    .filter(Boolean);
                const correctIndex = parseInt(block.querySelector('[data-exam-correct]').value, 10) || 1;
                return { question, type: 'mc_single', options, correct_answer: options[correctIndex - 1] || '' };
            }).filter(q => q.question && q.options.length >= 2);
            const rawField = document.getElementById('exam-json-raw');
            let rawExam = null;
            if (rawField && rawField.value.trim()) {
                try {
                    const parsed = JSON.parse(rawField.value);
                    if (Array.isArray(parsed)) rawExam = parsed;
                } catch (e) {
tfNotify('Некорректный JSON экзамена');                    return;
                }
            }

            const payload = {
                course_id: courseId,
                time_limit_minutes: parseInt(document.getElementById('exam-time-limit').value, 10) || 45,
                pass_percent: parseInt(document.getElementById('exam-pass-percent').value, 10) || 70,
                shuffle_questions: document.getElementById('exam-shuffle-q').checked,
                shuffle_options: document.getElementById('exam-shuffle-o').checked,
                exam_json: JSON.stringify(rawExam || questions)
            };
            fetch(`?action=admin-save-course-exam`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
                .then(parseAdminJson)
                .then(data => {
                    if (data.success) {
                        tfNotify(data.message || tfI18n.done);
                        closeModal('courseExamModal');
                        setTimeout(() => window.location.reload(), 250);
                    } else {
                        tfNotify(data.message || tfI18n.error);
                    }
                })
                .catch((e) => tfNotify((e && e.message) ? e.message : tfI18n.error));
        }

        async function deleteCourseExam(courseId) {
            const ok = await tfConfirm(tfI18n.confirmDeleteCourse);
            if (!ok) return;
            fetch(`?action=admin-delete-course-exam`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ course_id: courseId })
            })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) {
                        tfNotify(data.message || tfI18n.error);
                        return;
                    }
                    tfNotify(data.message || tfI18n.done);
                    setTimeout(() => window.location.reload(), 250);
                })
                .catch((e) => tfNotify((e && e.message) ? e.message : tfI18n.error));
        }

        async function deleteCourse(courseId) {
            const ok = await tfConfirm(tfI18n.confirmDeleteCourse);
            if (!ok) return;
            fetch(`?action=delete-course&id=${courseId}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        function openLessonModal(lessonId) {
            document.getElementById('lesson-id').value = lessonId || '';
            if (!lessonId) {
                if (adminContext.courseId) {
                    document.getElementById('lesson-course-id').value = adminContext.courseId;
                }
                document.getElementById('lesson-title').value = '';
                document.getElementById('lesson-type').value = 'article';
                document.getElementById('lesson-order').value = 0;
                document.getElementById('lesson-content').value = '';
                document.getElementById('lesson-video').value = '';
                setLessonQuiz([]);
                document.getElementById('lesson-materials-title').value = '';
                document.getElementById('lesson-materials-url').value = '';
                document.getElementById('lesson-practice-enabled').checked = false;
                document.getElementById('lesson-practice-kind').value = 'code';
                syncPracticeTaskKind('python');
                document.getElementById('lesson-practice-title').value = '';
                document.getElementById('lesson-practice-prompt').value = '';
                document.getElementById('lesson-practice-starter').value = '';
                document.getElementById('lesson-practice-tests').value = '';
                toggleLessonType();
                toggleLessonPractice();
                openModal('lessonModal');
                return;
            }
            fetch(`?action=admin-get-lesson&id=${lessonId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const l = data.lesson;
                    document.getElementById('lesson-course-id').value = l.course_id;
                    document.getElementById('lesson-title').value = l.title || '';
                    document.getElementById('lesson-type').value = l.type || 'article';
                    document.getElementById('lesson-order').value = l.order_num || 0;
                    document.getElementById('lesson-content').value = l.content || '';
                    document.getElementById('lesson-video').value = l.video_url || '';
                    setLessonQuiz(l.questions || []);
                    document.getElementById('lesson-materials-title').value = l.materials_title || '';
                    document.getElementById('lesson-materials-url').value = l.materials_url || '';
                    const p = l.practice || null;
                    const pEnabled = !!(p && (p.is_required === 1 || p.is_required === true || p.is_required === '1'));
                    document.getElementById('lesson-practice-enabled').checked = pEnabled;
                    const practiceLanguage = (p && p.language) ? p.language : 'python';
                    document.getElementById('lesson-practice-kind').value = inferPracticeKind(practiceLanguage);
                    syncPracticeTaskKind(practiceLanguage);
                    document.getElementById('lesson-practice-title').value = (p && p.title) ? p.title : '';
                    document.getElementById('lesson-practice-prompt').value = (p && p.prompt) ? p.prompt : '';
                    document.getElementById('lesson-practice-starter').value = (p && p.starter_code) ? p.starter_code : '';
                    document.getElementById('lesson-practice-tests').value = (p && p.tests_json) ? p.tests_json : '';
                    toggleLessonType();
                    toggleLessonPractice();
                    openModal('lessonModal');
                });
        }

        function toggleLessonType() {
            const type = document.getElementById('lesson-type').value;
            const quizWrap = document.getElementById('lesson-quiz-wrap');
            if (quizWrap) quizWrap.style.display = type === 'quiz' ? 'block' : 'none';
        }

        function toggleLessonPractice() {
            const toggle = document.getElementById('lesson-practice-enabled');
            const fields = document.getElementById('lesson-practice-fields');
            if (!toggle || !fields) return;
            const enabled = !!toggle.checked;
            fields.style.display = enabled ? 'grid' : 'none';
        }

        function createQuizQuestionBlock(data = {}) {
            const wrapper = document.createElement('div');
            wrapper.className = 'p-4 border border-gray-200 rounded-xl space-y-3';
            wrapper.innerHTML = `
        <div>
            <label class="text-sm text-gray-600">${tfI18n.examQuestion}</label>
            <input class="input-field w-full" data-quiz-question type="text">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="text-sm text-gray-600">${tfI18n.examOption1}</label>
                <input class="input-field w-full" data-quiz-option type="text">
            </div>
            <div>
                <label class="text-sm text-gray-600">${tfI18n.examOption2}</label>
                <input class="input-field w-full" data-quiz-option type="text">
            </div>
            <div>
                <label class="text-sm text-gray-600">${tfI18n.examOption3}</label>
                <input class="input-field w-full" data-quiz-option type="text">
            </div>
            <div>
                <label class="text-sm text-gray-600">${tfI18n.examOption4}</label>
                <input class="input-field w-full" data-quiz-option type="text">
            </div>
        </div>
        <div class="flex items-center gap-3">
            <label class="text-sm text-gray-600">${tfI18n.examCorrect}</label>
            <select class="input-field w-24" data-quiz-correct>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
            <button type="button" class="btn-secondary ml-auto" data-quiz-remove>${tfI18n.remove}</button>
        </div>
    `;
            const questionInput = wrapper.querySelector('[data-quiz-question]');
            const optionsInputs = wrapper.querySelectorAll('[data-quiz-option]');
            const correctSelect = wrapper.querySelector('[data-quiz-correct]');
            questionInput.value = data.question_text || data.question || '';
            const opts = (data.options_text || '').split('|||').filter(Boolean);
            optionsInputs.forEach((el, idx) => {
                el.value = (data.options && data.options[idx]) || opts[idx] || '';
            });
            correctSelect.value = String(data.correct_option || data.correct_index || 1);
            wrapper.querySelector('[data-quiz-remove]').addEventListener('click', () => wrapper.remove());
            return wrapper;
        }

        function setLessonQuiz(questions) {
            const list = document.getElementById('lesson-quiz-list');
            if (!list) return;
            list.innerHTML = '';
            (questions || []).forEach(q => list.appendChild(createQuizQuestionBlock(q)));
            if (!questions || questions.length === 0) {
                list.appendChild(createQuizQuestionBlock());
            }
        }

        function addQuizQuestion() {
            const list = document.getElementById('lesson-quiz-list');
            if (!list) return;
            list.appendChild(createQuizQuestionBlock());
        }

        function parseLessonQuiz() {
            const type = document.getElementById('lesson-type').value;
            if (type !== 'quiz') return null;
            const list = document.getElementById('lesson-quiz-list');
            if (!list) return [];
            const blocks = Array.from(list.children);
            return blocks.map(block => {
                const question = block.querySelector('[data-quiz-question]').value.trim();
                const options = Array.from(block.querySelectorAll('[data-quiz-option]'))
                    .map(el => el.value.trim())
                    .filter(Boolean);
                const correctIndex = parseInt(block.querySelector('[data-quiz-correct]').value, 10) || 1;
                return { question, options, correct_index: correctIndex };
            }).filter(q => q.question && q.options.length >= 2);
        }

        function saveLesson() {
            const rawLessonId = document.getElementById('lesson-id').value.trim();
            const parsedLessonId = parseInt(rawLessonId, 10);
            const lessonId = Number.isFinite(parsedLessonId) && parsedLessonId > 0 ? parsedLessonId : null;
            const practiceKind = document.getElementById('lesson-practice-kind').value;
            const practiceLanguage = getPracticeLanguageForKind(practiceKind, document.getElementById('lesson-practice-language').value);
            const payload = {
                id: lessonId,
                course_id: parseInt(document.getElementById('lesson-course-id').value, 10) || 0,
                title: document.getElementById('lesson-title').value.trim(),
                type: document.getElementById('lesson-type').value,
                order_num: parseInt(document.getElementById('lesson-order').value, 10) || 0,
                content: document.getElementById('lesson-content').value.trim(),
                video_url: document.getElementById('lesson-video').value.trim(),
                quiz_json: parseLessonQuiz(),
                materials_title: document.getElementById('lesson-materials-title').value.trim(),
                materials_url: document.getElementById('lesson-materials-url').value.trim(),
                practice: {
                    enabled: !!document.getElementById('lesson-practice-enabled').checked,
                    kind: practiceKind,
                    language: practiceLanguage,
                    title: document.getElementById('lesson-practice-title').value.trim(),
                    prompt: document.getElementById('lesson-practice-prompt').value,
                    starter_code: document.getElementById('lesson-practice-starter').value,
                    tests_json: document.getElementById('lesson-practice-tests').value
                }
            };
            const action = payload.id ? 'admin-update-lesson' : 'admin-create-lesson';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
                .then(parseAdminJson)
                .then(data => {
                    if (data.success) {
                        tfNotify(data.message || tfI18n.done);
                        window.location.reload();
                    } else {
                        tfNotify(data.message || tfI18n.error);
                    }
                });
        }

        async function deleteLesson(lessonId) {
            const ok = await tfConfirm(tfI18n.confirmDeleteLesson);
            if (!ok) return;
            fetch(`?action=admin-delete-lesson&id=${lessonId}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        function openAddVacancyModal() {
            document.getElementById('vacancy-id').value = '';
            document.getElementById('vacancy-title').value = '';
            document.getElementById('vacancy-company').value = '';
            document.getElementById('vacancy-location').value = '';
            document.getElementById('vacancy-type').value = 'remote';
            document.getElementById('vacancy-salary-min').value = '';
            document.getElementById('vacancy-salary-max').value = '';
            document.getElementById('vacancy-salary-currency').value = 'TJS';
            document.getElementById('vacancy-description').value = '';
            document.getElementById('vacancy-company-description').value = '';
            document.getElementById('vacancy-skills').value = '';
            document.getElementById('vacancy-requirements').value = '';
            document.getElementById('vacancy-pluses').value = '';
            document.getElementById('vacancy-responsibilities').value = '';
            openModal('vacancyModal');
        }

        function editVacancy(vacancyId) {
            fetch(`?action=get-vacancy&id=${vacancyId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const v = data.vacancy;
                    document.getElementById('vacancy-id').value = v.id;
                    document.getElementById('vacancy-title').value = v.title || '';
                    document.getElementById('vacancy-company').value = v.company || '';
                    document.getElementById('vacancy-location').value = v.location || '';
                    document.getElementById('vacancy-type').value = v.type || 'remote';
                    document.getElementById('vacancy-salary-min').value = v.salary_min || 0;
                    document.getElementById('vacancy-salary-max').value = v.salary_max || '';
                    document.getElementById('vacancy-salary-currency').value = v.salary_currency || 'TJS';
                    document.getElementById('vacancy-description').value = v.description || '';
                    document.getElementById('vacancy-company-description').value = v.company_description || '';
                    document.getElementById('vacancy-skills').value = (v.skills || []).map(s => s.skill_name).join(', ');
                    document.getElementById('vacancy-requirements').value = (v.requirements || []).map(r => r.requirement_text).join('\n');
                    document.getElementById('vacancy-pluses').value = (v.pluses || []).map(p => p.plus_text).join('\n');
                    document.getElementById('vacancy-responsibilities').value = (v.responsibilities || []).map(r => r.responsibility_text).join('\n');
                    openModal('vacancyModal');
                });
        }

        function saveVacancy() {
            const payload = {
                id: document.getElementById('vacancy-id').value || null,
                title: document.getElementById('vacancy-title').value.trim(),
                company: document.getElementById('vacancy-company').value.trim(),
                location: document.getElementById('vacancy-location').value.trim(),
                type: document.getElementById('vacancy-type').value,
                salary_min: parseInt(document.getElementById('vacancy-salary-min').value, 10) || 0,
                salary_max: parseInt(document.getElementById('vacancy-salary-max').value, 10) || null,
                salary_currency: document.getElementById('vacancy-salary-currency').value || 'TJS',
                description: document.getElementById('vacancy-description').value.trim(),
                company_description: document.getElementById('vacancy-company-description').value.trim(),
                skills: parseList(document.getElementById('vacancy-skills').value),
                requirements: parseList(document.getElementById('vacancy-requirements').value),
                pluses: parseList(document.getElementById('vacancy-pluses').value),
                responsibilities: parseList(document.getElementById('vacancy-responsibilities').value)
            };
            const action = payload.id ? 'admin-update-vacancy' : 'admin-create-vacancy';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
                .then(parseAdminJson)
                .then(data => {
                    if (data.success) {
                        tfNotify(data.message || tfI18n.done);
                        window.location.reload();
                    } else {
                        tfNotify(data.message || tfI18n.error);
                    }
                });
        }

        async function deleteVacancy(vacancyId) {
            const ok = await tfConfirm(tfI18n.confirmDeleteVacancy);
            if (!ok) return;
            fetch(`?action=delete-vacancy&id=${vacancyId}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        function openNotificationModal() {
            document.getElementById('notification-user-id').value = '';
            document.getElementById('notification-message').value = '';
            openModal('notificationModal');
        }

        function saveNotification() {
            const payload = {
                user_id: document.getElementById('notification-user-id').value.trim(),
                message: document.getElementById('notification-message').value.trim()
            };
            fetch('?action=admin-create-notification', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
                .then(parseAdminJson)
                .then(data => {
                    if (data.success) {
                        tfNotify(data.message || tfI18n.done);
                        window.location.reload();
                    } else {
                        tfNotify(data.message || tfI18n.error);
                    }
                });
        }

        async function deleteNotification(id) {
            const ok = await tfConfirm(tfI18n.confirmDeleteNotification);
            if (!ok) return;
            fetch(`?action=admin-delete-notification&id=${id}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        function openRoadmapModal(id) {
            document.getElementById('roadmap-id').value = id || '';
            document.getElementById('roadmap-title').value = '';
            document.getElementById('roadmap-description').value = '';
            if (!id) {
                openModal('roadmapModal');
                return;
            }
            fetch(`?action=admin-roadmap-get-roadmap&id=${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const rm = data.roadmap;
                    document.getElementById('roadmap-id').value = rm.id;
                    document.getElementById('roadmap-title').value = rm.title || '';
                    document.getElementById('roadmap-description').value = rm.description || '';
                    openModal('roadmapModal');
                });
        }

        function saveRoadmap() {
            if (!ensureRequiredFields([{ id: 'roadmap-title', label: 'Название родмапа' }])) {
                return;
            }
            const payload = {
                id: document.getElementById('roadmap-id').value || null,
                title: document.getElementById('roadmap-title').value.trim(),
                description: document.getElementById('roadmap-description').value.trim()
            };
            const action = payload.id ? 'admin-roadmap-update-roadmap' : 'admin-roadmap-create-roadmap';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
                .then(parseAdminJson)
                .then(data => {
                    if (data.success) {
                        tfNotify(data.message || tfI18n.done);
                        window.location.reload();
                    } else {
                        tfNotify(data.message || tfI18n.error);
                    }
                });
        }

        async function deleteRoadmap(id) {
            const ok = await tfConfirm(tfI18n.confirmDeleteRoadmap);
            if (!ok) return;
            fetch(`?action=admin-roadmap-delete-roadmap&id=${id}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (data.success) {
                        tfNotify(data.message || tfI18n.done);
                        window.location.reload();
                    } else {
                        tfNotify(data.message || tfI18n.error);
                    }
                });
        }

        function openRoadmapNodeModal(nodeId) {
            document.getElementById('roadmap-node-id').value = nodeId || '';
            document.getElementById('roadmap-node-title').value = '';
            document.getElementById('roadmap-node-roadmap-title').value = adminContext.roadmapTitle || '';
            document.getElementById('roadmap-node-topic').value = '';
            document.getElementById('roadmap-node-materials').value = '';
            document.getElementById('roadmap-node-x').value = 0;
            document.getElementById('roadmap-node-y').value = 0;
            document.getElementById('roadmap-node-deps').value = '';
            document.getElementById('roadmap-node-exam').checked = false;
            if (!nodeId) {
                openModal('roadmapNodeModal');
                return;
            }
            fetch(`?action=admin-roadmap-get-node&id=${nodeId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const n = data.node;
                    document.getElementById('roadmap-node-id').value = n.id;
                    document.getElementById('roadmap-node-title').value = n.title || '';
                    document.getElementById('roadmap-node-roadmap-title').value = n.roadmap_title || '';
                    document.getElementById('roadmap-node-topic').value = n.topic || '';
                    document.getElementById('roadmap-node-materials').value = (Array.isArray(n.materials) ? n.materials : []).map(m => {
                        if (typeof m === 'string') return m;
                        const title = m.title || tfI18n.material;
                        const url = m.url || '';
                        return url ? `${title}|${url}` : title;
                    }).join('\n');
                    document.getElementById('roadmap-node-x').value = n.x || 0;
                    document.getElementById('roadmap-node-y').value = n.y || 0;
                    document.getElementById('roadmap-node-deps').value = n.deps || '';
                    document.getElementById('roadmap-node-exam').checked = !!n.is_exam;
                    openModal('roadmapNodeModal');
                });
        }

        function saveRoadmapNode() {
            if (!ensureRequiredFields([
                { id: 'roadmap-node-title', label: 'Название блока родмапа' },
                { id: 'roadmap-node-roadmap-title', label: 'задача программы' }
            ])) {
                return;
            }
            const depsRaw = document.getElementById('roadmap-node-deps').value.trim();
            const depsArr = depsRaw ? depsRaw.split(',').map(s => parseInt(s.trim(), 10)).filter(n => !isNaN(n)) : [];
            const matsRaw = document.getElementById('roadmap-node-materials').value
                .split('\n')
                .map(s => s.trim())
                .filter(Boolean)
                .slice(0, 5)
                .map(line => {
                    const parts = line.split('|');
                    if (parts.length >= 2) {
                        return { title: parts[0].trim() || tfI18n.material, url: parts.slice(1).join('|').trim() };
                    }
                    return { title: line, url: '' };
                });
            const payload = {
                id: document.getElementById('roadmap-node-id').value || null,
                title: document.getElementById('roadmap-node-title').value.trim(),
                roadmap_title: document.getElementById('roadmap-node-roadmap-title').value.trim() || tfI18n.defaultRoadmap,
                topic: document.getElementById('roadmap-node-topic').value.trim(),
                materials: JSON.stringify(matsRaw),
                x: parseInt(document.getElementById('roadmap-node-x').value, 10) || 0,
                y: parseInt(document.getElementById('roadmap-node-y').value, 10) || 0,
                deps: JSON.stringify(depsArr),
                is_exam: document.getElementById('roadmap-node-exam').checked ? 1 : 0
            };
            const action = payload.id ? 'admin-roadmap-update-node' : 'admin-roadmap-create-node';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            }).then(parseAdminJson)
                .then(data => {
                    if (data.success) {
                        tfNotify(data.message || tfI18n.done);
                        window.location.reload();
                    } else {
                        tfNotify(data.message || tfI18n.error);
                    }
                });
        }

        async function deleteRoadmapNode(id) {
            const ok = await tfConfirm(tfI18n.confirmDeleteNode);
            if (!ok) return;
            fetch(`?action=admin-roadmap-delete-node&id=${id}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        function openRoadmapLessonModal(lessonId) {
            document.getElementById('roadmap-lesson-id').value = lessonId || '';
            document.getElementById('roadmap-lesson-title').value = '';
            document.getElementById('roadmap-lesson-video').value = '';
            document.getElementById('roadmap-lesson-description').value = '';
            document.getElementById('roadmap-lesson-materials').value = '';
            document.getElementById('roadmap-lesson-order').value = 0;
            if (!lessonId) {
                const nodeSelect = document.getElementById('roadmap-lesson-node');
                if (nodeSelect && nodeSelect.options.length > 0) {
                    nodeSelect.value = nodeSelect.options[0].value;
                }
                openModal('roadmapLessonModal');
                return;
            }
            fetch(`?action=admin-roadmap-get-lesson&id=${lessonId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const l = data.lesson;
                    document.getElementById('roadmap-lesson-id').value = l.id;
                    document.getElementById('roadmap-lesson-node').value = l.node_id;
                    document.getElementById('roadmap-lesson-title').value = l.title || '';
                    document.getElementById('roadmap-lesson-video').value = l.video_url || '';
                    document.getElementById('roadmap-lesson-description').value = l.description || '';
                    const mats = JSON.parse(l.materials || '[]') || [];
                    document.getElementById('roadmap-lesson-materials').value = mats.map(m => {
                        if (typeof m === 'string') return m;
                        const title = m.title || '';
                        const url = m.url || '';
                        return url ? `${title} | ${url}` : title;
                    }).join('\n');
                    document.getElementById('roadmap-lesson-order').value = l.order_index || 0;
                    openModal('roadmapLessonModal');
                });
        }

        function saveRoadmapLesson() {
            if (!ensureRequiredFields([
                { id: 'roadmap-lesson-node', label: 'Урок блока' },
                { id: 'roadmap-lesson-title', label: 'Названия урока ноды' }
            ])) {
                return;
            }
            const materials = document.getElementById('roadmap-lesson-materials').value
                .split('\n')
                .map(s => s.trim())
                .filter(Boolean)
                .map(line => {
                    const parts = line.split('|').map(p => p.trim()).filter(Boolean);
                    if (parts.length >= 2) {
                        return { title: parts[0], url: parts.slice(1).join('|') };
                    }
                    return { title: line, url: '' };
                });
            const payload = {
                id: document.getElementById('roadmap-lesson-id').value || null,
                node_id: parseInt(document.getElementById('roadmap-lesson-node').value, 10) || 0,
                title: document.getElementById('roadmap-lesson-title').value.trim(),
                video_url: document.getElementById('roadmap-lesson-video').value.trim(),
                description: document.getElementById('roadmap-lesson-description').value.trim(),
                materials: JSON.stringify(materials),
                order_index: parseInt(document.getElementById('roadmap-lesson-order').value, 10) || 0
            };
            const action = payload.id ? 'admin-roadmap-update-lesson' : 'admin-roadmap-create-lesson';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            }).then(parseAdminJson)
                .then(data => {
                    if (data.success) {
                        tfNotify(data.message || tfI18n.done);
                        window.location.reload();
                    } else {
                        tfNotify(data.message || tfI18n.error);
                    }
                });
        }

        async function deleteRoadmapLesson(id) {
            const ok = await tfConfirm(tfI18n.confirmDeleteRoadmapLesson);
            if (!ok) return;
            fetch(`?action=admin-roadmap-delete-lesson&id=${id}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        function openRoadmapQuizModal(quizId) {
            document.getElementById('roadmap-quiz-id').value = quizId || '';
            document.getElementById('roadmap-quiz-question').value = '';
            document.getElementById('roadmap-quiz-options').value = '';
            syncRoadmapQuizCorrectOptions('');
            if (!quizId) {
                const nodeSelect = document.getElementById('roadmap-quiz-node');
                if (nodeSelect && nodeSelect.options.length > 0) {
                    nodeSelect.value = nodeSelect.options[0].value;
                }
                openModal('roadmapQuizModal');
                return;
            }
            fetch(`?action=admin-roadmap-get-quiz&id=${quizId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    const q = data.quiz;
                    document.getElementById('roadmap-quiz-id').value = q.id;
                    document.getElementById('roadmap-quiz-node').value = q.node_id;
                    document.getElementById('roadmap-quiz-question').value = q.question || '';
                    document.getElementById('roadmap-quiz-options').value = (JSON.parse(q.options || '[]') || []).join('\n');
                    syncRoadmapQuizCorrectOptions(q.correct_answer || '');
                    openModal('roadmapQuizModal');
                });
        }

        function saveRoadmapQuiz() {
            if (!ensureRequiredFields([
                { id: 'roadmap-quiz-node', label: 'Раздел' },
{ id: 'roadmap-quiz-question', label: 'Вопрос' },
{ id: 'roadmap-quiz-options', label: 'Варианты ответа' }
            ])) {
                return;
            }
            const options = syncRoadmapQuizCorrectOptions();
            if (options.length < 2) {
                tfNotify(tfI18n.invalidQuizOptions);
                return;
            }
            const selectedCorrect = document.getElementById('roadmap-quiz-correct').value.trim();
            if (!selectedCorrect || !options.includes(selectedCorrect)) {
                tfNotify(tfI18n.invalidCorrectAnswer);
                return;
            }
            const payload = {
                id: document.getElementById('roadmap-quiz-id').value || null,
                node_id: parseInt(document.getElementById('roadmap-quiz-node').value, 10) || 0,
                question: document.getElementById('roadmap-quiz-question').value.trim(),
                options: JSON.stringify(options),
                correct_answer: selectedCorrect
            };
            const action = payload.id ? 'admin-roadmap-update-quiz' : 'admin-roadmap-create-quiz';
            fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            }).then(parseAdminJson)
                .then(data => {
                    if (data.success) {
                        tfNotify(data.message || tfI18n.done);
                        window.location.reload();
                    } else {
                        tfNotify(data.message || tfI18n.error);
                    }
                });
        }

        async function deleteRoadmapQuiz(id) {
            const ok = await tfConfirm(tfI18n.confirmDeleteQuestion);
            if (!ok) return;
            fetch(`?action=admin-roadmap-delete-quiz&id=${id}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(parseAdminJson)
                .then(data => {
                    if (!data.success) return tfNotify(data.message || tfI18n.error);
                    window.location.reload();
                });
        }

        document.getElementById('ejudgeScanBtn')?.addEventListener('click', ejudgeScan);
        document.getElementById('ejudgeImportBtn')?.addEventListener('click', ejudgeImport);
    </script>
</body>

</html>
