<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$courses = $courses ?? [];
$filter = $filter ?? '';
$search = $search ?? '';
$levelFilter = $levelFilter ?? '';
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$totalCourses = $totalCourses ?? count($courses);
$isAdmin = !empty($user) && (($user['role'] ?? '') === 'admin');
$buildUrl = function ($overrides = []) use ($filter, $levelFilter, $search) {
    $params = array_merge(
        ['action' => 'courses', 'filter' => $filter, 'level' => $levelFilter, 'search' => $search],
        $overrides
    );
    $params = array_filter($params, function ($v) {
        return $v !== '' && $v !== null; });
    return '?' . http_build_query($params);
};
$courseExcerpt = static function ($text, $limit = 100) {
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
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(function_exists('currentLang') ? currentLang() : 'ru') ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('courses_page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

	        html,
	        body {
	            max-width: 100%;
	            overflow-x: hidden;
	            font-family: "Inter", sans-serif;
	            background-color: #f8fafc;
	            color: #0f172a;
	            line-height: 1.6;
	        }

        .card {
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.1);
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

        .input-field {
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            width: 100%;
            transition: all 0.2s;
        }

        .search-input {
            padding-left: 40px;
        }

        .input-field:focus {
            outline: none;
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.2);
        }

        .progress-bar {
            height: 8px;
            border-radius: 999px;
            background-color: #e2e8f0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #4f46e5, #10b981);
            transition: width 0.3s ease;
        }

        .course-card {
            transition: all 0.3s ease;
            border-radius: 24px;
            overflow: hidden;
            border-color: #e2e8f0;
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12);
            border-color: #c7d2fe;
        }

        .course-cover {
            position: relative;
            height: 180px;
            background: radial-gradient(circle at 20% 20%, #a5b4fc 0%, #4f46e5 40%, #0f172a 100%);
        }

        .course-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.6;
            mix-blend-mode: soft-light;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .skill-tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            margin: 4px;
            border-radius: 20px;
            background-color: #e0e7ff;
            color: #4338ca;
            font-size: 14px;
            transition: all 0.2s;
        }

        .skill-tag:hover {
            background-color: #c7d2fe;
            cursor: pointer;
        }

        .skill-tag.remove:hover {
            background-color: #fecaca;
            color: #b91c1c;
        }

        .notification-badge {
            animation: pulse 2s infinite;
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

<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="lg:w-64 flex-shrink-0" x-data="{ filtersOpen: window.innerWidth >= 1024 }"
                @resize.window="if (window.innerWidth >= 1024) filtersOpen = true">
                <button type="button"
                    class="lg:hidden w-full mb-3 px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-700 font-medium text-sm flex items-center justify-between"
                    @click="filtersOpen = !filtersOpen">
                    <span><i class="fas fa-filter mr-2"></i><?= t('courses_filters') ?></span>
                    <i class="fas" :class="filtersOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <div class="card sticky top-8" :class="filtersOpen ? 'block' : 'hidden lg:block'">
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-filter mr-2"></i> <?= t('courses_filters') ?>
                        </h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="?action=courses" class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50
                               <?= $filter === '' ? 'bg-indigo-50 text-indigo-600 font-medium' : '' ?>">
                                    <i class="fas fa-list mr-2"></i> <?= t('courses_all') ?>
                                </a>
                            </li>
                            <li>
                                <a href="?action=courses&filter=frontend" class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50
                               <?= $filter === 'frontend' ? 'bg-indigo-50 text-indigo-600 font-medium' : '' ?>">
                                    <i class="fas fa-code text-indigo-600 mr-2"></i> Frontend
                                </a>
                            </li>
                            <li>
                                <a href="?action=courses&filter=backend" class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50
                               <?= $filter === 'backend' ? 'bg-indigo-50 text-indigo-600 font-medium' : '' ?>">
                                    <i class="fas fa-server text-indigo-600 mr-2"></i> Backend
                                </a>
                            </li>
                            <li>
                                <a href="?action=courses&filter=design" class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50
                               <?= $filter === 'design' ? 'bg-indigo-50 text-indigo-600 font-medium' : '' ?>">
                                    <i class="fas fa-paint-brush text-indigo-600 mr-2"></i> <?= t('courses_design') ?>
                                </a>
                            </li>
                            <li>
                                <a href="?action=courses&filter=devops" class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50
                               <?= $filter === 'devops' ? 'bg-indigo-50 text-indigo-600 font-medium' : '' ?>">
                                    <i class="fas fa-tools text-indigo-600 mr-2"></i> DevOps
                                </a>
                            </li>
                        </ul>
                        <div class="mt-6 border-t pt-4">
                            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-chart-bar mr-2"></i> <?= t('courses_level') ?>
                            </h3>
                            <ul class="space-y-2">
                                <li>
                                    <a href="?action=courses&filter=<?= htmlspecialchars($filter) ?>" class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50
                                   <?= $levelFilter === '' ? 'bg-indigo-50 text-indigo-600 font-medium' : '' ?>">
                                        <?= t('courses_all_levels') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="?action=courses&filter=<?= htmlspecialchars($filter) ?>&level=Начальный"
                                        class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50
                                   <?= $levelFilter === 'Начальный' ? 'bg-indigo-50 text-indigo-600 font-medium' : '' ?>">
                                        <?= t('courses_level_beginner') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="?action=courses&filter=<?= htmlspecialchars($filter) ?>&level=Средний"
                                        class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50
                                   <?= $levelFilter === 'Средний' ? 'bg-indigo-50 text-indigo-600 font-medium' : '' ?>">
                                        <?= t('courses_level_medium') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="?action=courses&filter=<?= htmlspecialchars($filter) ?>&level=Продвинутый"
                                        class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50
                                   <?= $levelFilter === 'Продвинутый' ? 'bg-indigo-50 text-indigo-600 font-medium' : '' ?>">
                                        <?= t('courses_level_advanced') ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-4 sm:mb-0 flex items-center">
                        <i class="fas fa-graduation-cap mr-2"></i> <?= t('courses_heading') ?>
                    </h1>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="relative w-full sm:w-64">
                            <form method="GET" action="">
                                <input type="hidden" name="action" value="courses">
                                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                                <input type="hidden" name="level" value="<?= htmlspecialchars($levelFilter) ?>">
                                <input type="hidden" name="page" value="1">
                                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                    placeholder="<?= t('courses_search_placeholder') ?>"
                                    class="input-field search-input w-full">
                                <span
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <i class="fas fa-search" style="margin-top:-15px;"></i>

                            </form>
                        </div>
                        <?php if ($isAdmin): ?>
                            <a href="?action=admin&tab=courses" style="margin-top:-15px;"
                                class="w-full sm:w-auto btn-primary flex items-center justify-center">
                                <i class="fas fa-plus mr-2"></i> <?= t('courses_create') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (count($courses) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($courses as $course): ?>
                            <div class="course-card card">
                                <div class="course-cover">
                                    <img src="<?= htmlspecialchars($course['image_url']) ?>" alt="Course image"
                                        class="w-full h-full object-cover" />
                                </div>
                                <div class="p-5">
                                    <div class="flex items-center mb-2">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            <?= htmlspecialchars($course['level']) ?>
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500 flex items-center">
                                            <i class="far fa-clock mr-1"></i>
                                            <span><?= t('courses_lessons_few') ?></span>
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <?= htmlspecialchars($course['title']) ?>
                                    </h3>
                                    <p class="text-indigo-600 mt-1">
                                        <?= htmlspecialchars($course['instructor']) ?>
                                    </p>
                                    <p class="text-gray-600 text-sm mt-2 line-clamp-2">
                                        <?= htmlspecialchars($courseExcerpt($course['description'] ?? '', 100)) ?>
                                    </p>
                                    <?php $progress = (int) ($course['progress'] ?? 0);
                                    if ($progress < 0)
                                        $progress = 0;
                                    if ($progress > 100)
                                        $progress = 100; ?>
                                    <div class="mt-4">
                                        <div class="flex justify-between text-sm text-gray-500 mb-1">
                                            <span><?= t('courses_progress') ?></span>
                                            <span><?= $progress ?>%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?= $progress ?>%"></div>
                                        </div>
                                    </div>
                                    <button onclick="openCourse(<?= $course['id'] ?>)"
                                        class="mt-4 w-full py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                        <?= t('courses_open') ?>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 card">
                        <i class="fas fa-graduation-cap text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900">
                            <?= t('courses_not_found') ?>
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            <?= t('courses_not_found_hint') ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if ($totalPages > 1): ?>
                    <?php
                    $maxPagesToShow = 3;
                    $startPage = max(1, (int) $page - 1);
                    $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);
                    if (($endPage - $startPage + 1) < $maxPagesToShow) {
                        $startPage = max(1, $endPage - $maxPagesToShow + 1);
                    }
                    ?>
                    <div class="mt-8 flex items-center justify-center gap-2">
                        <a href="<?= $buildUrl(['page' => max(1, $page - 1)]) ?>"
                            class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">
                            &#8592;
                        </a>
                        <?php for ($p = $startPage; $p <= $endPage; $p++): ?>
                            <a href="<?= $buildUrl(['page' => $p]) ?>"
                                class="px-3 py-2 rounded-lg text-sm <?= $p === (int) $page ? 'bg-indigo-600 text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50' ?>">
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

    <?php
    $footerContext = 'courses';
    include 'includes/footer.php';
    ?>

    <script>
        function openCourse(courseId) {
            window.location.href = '?action=get-course&id=' + courseId;
        }
    </script>
</body>

</html>
