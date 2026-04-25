<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>

<?php
$ROADMAPS_I18N = [
    'ru' => [
        'page_title' => 'Роадмапы - CodeMaster',
        'heading' => 'Роадмапы',
        'progress' => 'Прогресс',
        'available' => 'Доступные роадмапы',
        'filters' => 'Фильтры',
        'search_placeholder' => 'Поиск роадмапов...',
        'search_aria' => 'Поиск',
        'topics' => 'Темы',
        'filter_all' => 'Все',
        'filter_reset' => 'Сбросить',
        'no_results' => 'Ничего не найдено',
        'page_prev' => 'Назад',
        'page_next' => 'Вперед',
        'roadmap_word' => 'Roadmap',
        'badge' => 'Роадмап',
        'blocks' => 'блоков',
        'done_of_total' => '{done} из {total} пройдено',
        'go_to_roadmap' => 'Перейти к роадмапу',
        'roadmaps_count' => '{count} роадмап(ов)',
        'default_roadmap' => 'Основной',
        'load_error' => 'Ошибка загрузки данных',
        'description_fallback' => 'Структурированный путь обучения с модулями, уроками и итоговой проверкой.',
        'topics_prefix' => 'Темы: {topics}'
    ],
    'en' => [
        'page_title' => 'Roadmaps - CodeMaster',
        'heading' => 'Roadmaps',
        'progress' => 'Progress',
        'available' => 'Available roadmaps',
        'filters' => 'Filters',
        'search_placeholder' => 'Search roadmaps...',
        'search_aria' => 'Search',
        'topics' => 'Topics',
        'filter_all' => 'All',
        'filter_reset' => 'Reset',
        'no_results' => 'No results found',
        'page_prev' => 'Prev',
        'page_next' => 'Next',
        'roadmap_word' => 'Roadmap',
        'badge' => 'Roadmap',
        'blocks' => 'blocks',
        'done_of_total' => '{done} of {total} completed',
        'go_to_roadmap' => 'Open roadmap',
        'roadmaps_count' => '{count} roadmap(s)',
        'default_roadmap' => 'Main',
        'load_error' => 'Failed to load data',
        'description_fallback' => 'A structured learning path with modules, lessons, and final assessment.',
        'topics_prefix' => 'Topics: {topics}'
    ],
    'tg' => [
        'page_title' => 'Роадмапҳо - CodeMaster',
        'heading' => 'Роадмапҳо',
        'progress' => 'Пешрафт',
        'available' => 'Роадмапҳои дастрас',
        'filters' => 'Филтрҳо',
        'search_placeholder' => 'Ҷустуҷӯи роадмапҳо...',
        'search_aria' => 'Ҷустуҷӯ',
        'topics' => 'Мавзӯъҳо',
        'filter_all' => 'Ҳама',
        'filter_reset' => 'Пок кардан',
        'no_results' => 'Ҳеҷ чиз ёфт нашуд',
        'page_prev' => 'Қаблӣ',
        'page_next' => 'Баъдӣ',
        'roadmap_word' => 'Roadmap',
        'badge' => 'Роадмап',
        'blocks' => 'блок',
        'done_of_total' => '{done} аз {total} анҷом шуд',
        'go_to_roadmap' => 'Гузаштан ба роадмап',
        'roadmaps_count' => '{count} роадмап',
        'default_roadmap' => 'Асосӣ',
        'load_error' => 'Хатои боркунии маълумот',
        'description_fallback' => 'Роҳи сохторбандии омӯзиш бо модулҳо, дарсҳо ва санҷиши ниҳоӣ.',
        'topics_prefix' => 'Мавзӯъҳо: {topics}'
    ]
];
$roadmapsLang = currentLang();
$roadmapsI18n = $ROADMAPS_I18N[$roadmapsLang] ?? $ROADMAPS_I18N['ru'];
if ($roadmapsLang === 'tg') {
    $roadmapsI18n = $ROADMAPS_I18N['ru'];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($roadmapsI18n['page_title']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
	    <style>
	        html,
	        body {
	            max-width: 100%;
	            overflow-x: hidden;
	            font-family: 'Inter', sans-serif;
	            background: #f8fafc;
	            color: #0f172a;
	        }

        .page-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
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

        .search-input {
            padding-left: 40px;
        }

        .filter-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .filter-scroll::-webkit-scrollbar-thumb {
            background: #c7d2fe;
            border-radius: 9999px;
        }

        .course-card {
            border-radius: 24px;
            overflow: hidden;
        }

        .progress-bar {
            height: 8px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4f46e5, #10b981);
        }

        .roadmap-cover {
            height: 180px;
            background: radial-gradient(circle at 20% 20%, #a5b4fc 0%, #4f46e5 40%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            border-radius: 24px 24px 0 0;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .roadmap-text-safe {
            overflow-wrap: anywhere;
            word-break: break-word;
            hyphens: auto;
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="page-wrap">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2 sm:mb-0 flex items-center">
                <i class="fas fa-project-diagram mr-2"></i> <?= htmlspecialchars($roadmapsI18n['heading']) ?>
            </h1>
            <div class="card p-4 flex items-center gap-4">
                <div>
                    <div class="text-xs text-slate-500 uppercase font-semibold">
                        <?= htmlspecialchars($roadmapsI18n['progress']) ?>
                    </div>
                    <div class="text-2xl font-bold text-indigo-600" id="progress-text">0%</div>
                </div>
                <div class="w-40">
                    <div class="progress-bar">
                        <div id="progress-bar" class="progress-fill" style="width:0%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <div class="lg:w-64 flex-shrink-0" x-data="{ filtersOpen: window.innerWidth >= 1024 }"
                @resize.window="if (window.innerWidth >= 1024) filtersOpen = true">
                <button type="button"
                    class="lg:hidden w-full mb-3 px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-700 font-medium text-sm flex items-center justify-between"
                    @click="filtersOpen = !filtersOpen">
                    <span><i class="fas fa-filter mr-2"></i><?= htmlspecialchars($roadmapsI18n['filters']) ?></span>
                    <i class="fas" :class="filtersOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <div class="card sticky top-8" :class="filtersOpen ? 'block' : 'hidden lg:block'">
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-filter mr-2"></i> <?= htmlspecialchars($roadmapsI18n['filters']) ?>
                        </h3>
                        <div class="border-t border-slate-200 pt-4">
                            <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-tags mr-2"></i> <?= htmlspecialchars($roadmapsI18n['topics']) ?>
                            </h4>
                            <div id="roadmap-topics" class="space-y-2 max-h-56 overflow-y-auto pr-2 filter-scroll"></div>
                        </div>
                        <div class="mt-4">
                            <button type="button" id="roadmap-reset"
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-50">
                                <?= htmlspecialchars($roadmapsI18n['filter_reset']) ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1">
                <div class="card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
                        <div>
                            <h2 class="text-xl font-semibold"><?= htmlspecialchars($roadmapsI18n['available']) ?></h2>
                            <span class="text-sm text-slate-500" id="roadmap-count">0</span>
                        </div>
                        <div class="relative w-full sm:w-64">
                            <input id="roadmap-search" type="text" class="input-field search-input w-full"
                                placeholder="<?= htmlspecialchars($roadmapsI18n['search_placeholder']) ?>"
                                aria-label="<?= htmlspecialchars($roadmapsI18n['search_aria']) ?>">
                            <span
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                    <div id="roadmap-cards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
                    <div id="roadmap-pagination" class="mt-8 flex items-center justify-center gap-2"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const tfI18n = <?= tfSafeJson($roadmapsI18n, JSON_UNESCAPED_UNICODE) ?>;
        const PAGE_SIZE = 3;
        let appData = { nodes: [], progress: [], roadmaps: [] };
        let roadmapIndex = [];
        let activeTopics = new Set();
        let searchTerm = '';
        let currentPage = 1;

        function notify(text) {
            if (window.tfNotify) return tfNotify(text);
        }

        function tfFormat(template, vars = {}) {
            return String(template || '').replace(/\{(\w+)\}/g, (_, key) => (vars[key] ?? `{${key}}`));
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function groupRoadmaps(nodes, roadmapsMeta = []) {
            const metaMap = new Map((roadmapsMeta || []).map(r => [String(r.title || '').trim(), r]));
            const groups = new Map();
            nodes.forEach(node => {
                const key = node.roadmap_title || tfI18n.default_roadmap;
                if (!groups.has(key)) groups.set(key, []);
                groups.get(key).push(node);
            });
            return Array.from(groups.entries()).map(([key, list]) => {
                const meta = metaMap.get(key) || {};
                const topics = Array.from(new Set(list.map(n => String(n.topic || '').trim()).filter(Boolean)));
                return {
                    key,
                    title: key,
                    description: String(meta.description || '').trim(),
                    nodes: list,
                    topics
                };
            });
        }

        function buildRoadmapDescription(roadmap) {
            if (roadmap.description) {
                return roadmap.description;
            }
            const topics = Array.from(new Set((roadmap.nodes || [])
                .map(n => String(n.topic || '').trim())
                .filter(Boolean)))
                .slice(0, 3);
            if (topics.length > 0) {
                return tfFormat(tfI18n.topics_prefix, { topics: topics.join(', ') });
            }
            return tfI18n.description_fallback;
        }

        function renderRoadmapCards(roadmaps) {
            const wrap = document.getElementById('roadmap-cards');
            wrap.innerHTML = '';
            if (!roadmaps.length) {
                wrap.innerHTML = `
                    <div class="col-span-full text-center py-10 card">
                        <div class="text-gray-400 text-4xl mb-3"><i class="fas fa-search"></i></div>
                        <div class="text-sm text-gray-600">${escapeHtml(tfI18n.no_results)}</div>
                    </div>
                `;
                return;
            }

            roadmaps.forEach(r => {
                const total = r.nodes.length;
                const done = r.nodes.filter(n => appData.progress.includes(parseInt(n.id, 10))).length;
                const percent = total > 0 ? Math.round((done / total) * 100) : 0;
                const description = buildRoadmapDescription(r);
                const safeTitle = escapeHtml(r.title);
                const safeDescription = escapeHtml(description);

                const card = document.createElement('div');
                card.className = 'course-card card';
                card.innerHTML = `
                <div class="roadmap-cover">
                    <div class="text-center">
                        <div class="text-xs uppercase tracking-widest text-indigo-200 roadmap-text-safe">${tfI18n.roadmap_word}</div>
                        <div class="text-2xl font-extrabold roadmap-text-safe">${safeTitle}</div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">${tfI18n.badge}</span>
                        <span class="ml-2 text-xs text-gray-500 flex items-center">
                            <i class="far fa-clock mr-1"></i>
                            <span>${total} ${tfI18n.blocks}</span>
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 roadmap-text-safe">${safeTitle}</h3>
                    <p class="text-gray-500 text-sm mt-1 line-clamp-2 roadmap-text-safe">${safeDescription}</p>
                    <p class="text-gray-600 text-sm mt-2">${tfFormat(tfI18n.done_of_total, { done, total })}</p>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm text-gray-500 mb-1">
                            <span>${tfI18n.progress}</span>
                            <span>${percent}%</span>
                        </div>
                        <div class="progress-bar"><div class="progress-fill" style="width:${percent}%"></div></div>
                    </div>
                    <button class="mt-4 w-full py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">${tfI18n.go_to_roadmap}</button>
                </div>
            `;
                card.querySelector('button').onclick = () => {
                    const title = encodeURIComponent(r.title);
                    window.location.href = `?action=roadmap&id=${title}`;
                };
                wrap.appendChild(card);
            });
        }

        function renderTopicsFilter(topics) {
            const container = document.getElementById('roadmap-topics');
            if (!container) return;
            container.innerHTML = '';
            const allLabel = document.createElement('label');
            allLabel.className = 'flex items-center text-sm text-gray-700';
            allLabel.innerHTML = `
                <input type="radio" name="roadmap-topic-all" value="" class="text-indigo-600 border-gray-300 mr-2" checked>
                <span>${escapeHtml(tfI18n.filter_all)}</span>
            `;
            allLabel.querySelector('input').addEventListener('change', () => {
                activeTopics = new Set();
                currentPage = 1;
                applyFilters();
            });
            container.appendChild(allLabel);

            topics.forEach(topic => {
                const label = document.createElement('label');
                label.className = 'flex items-center text-sm text-gray-700';
                label.innerHTML = `
                    <input type="checkbox" value="${escapeHtml(topic)}" class="text-indigo-600 border-gray-300 mr-2">
                    <span>${escapeHtml(topic)}</span>
                `;
                label.querySelector('input').addEventListener('change', (e) => {
                    const value = String(e.target.value || '');
                    if (e.target.checked) {
                        activeTopics.add(value);
                    } else {
                        activeTopics.delete(value);
                    }
                    const allRadio = container.querySelector('input[name="roadmap-topic-all"]');
                    if (allRadio) allRadio.checked = activeTopics.size === 0;
                    currentPage = 1;
                    applyFilters();
                });
                container.appendChild(label);
            });
        }

        function filterRoadmaps(roadmaps) {
            const term = String(searchTerm || '').trim().toLowerCase();
            return roadmaps.filter(r => {
                if (activeTopics.size) {
                    const hasTopic = (r.topics || []).some(t => activeTopics.has(t));
                    if (!hasTopic) return false;
                }
                if (term) {
                    const hay = `${r.title} ${r.description} ${(r.topics || []).join(' ')}`.toLowerCase();
                    if (!hay.includes(term)) return false;
                }
                return true;
            });
        }

        function renderPagination(totalPages) {
            const container = document.getElementById('roadmap-pagination');
            if (!container) return;
            container.innerHTML = '';
            if (totalPages <= 1) return;

            const maxPagesToShow = 3;
            let startPage = Math.max(1, currentPage - 1);
            let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
            if ((endPage - startPage + 1) < maxPagesToShow) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            const prevBtn = document.createElement('button');
            prevBtn.className = 'px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50';
            prevBtn.textContent = '←';
            prevBtn.disabled = currentPage <= 1;
            prevBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage -= 1;
                    applyFilters();
                }
            });
            container.appendChild(prevBtn);

            for (let p = startPage; p <= endPage; p += 1) {
                const btn = document.createElement('button');
                btn.className = p === currentPage
                    ? 'px-3 py-2 rounded-lg text-sm bg-indigo-600 text-white'
                    : 'px-3 py-2 rounded-lg text-sm border border-gray-200 text-gray-600 hover:bg-gray-50';
                btn.textContent = String(p);
                btn.addEventListener('click', () => {
                    currentPage = p;
                    applyFilters();
                });
                container.appendChild(btn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = 'px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50';
            nextBtn.textContent = '→';
            nextBtn.disabled = currentPage >= totalPages;
            nextBtn.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage += 1;
                    applyFilters();
                }
            });
            container.appendChild(nextBtn);
        }

        function applyFilters() {
            const filtered = filterRoadmaps(roadmapIndex);
            const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * PAGE_SIZE;
            const pageItems = filtered.slice(start, start + PAGE_SIZE);
            const countEl = document.getElementById('roadmap-count');
            if (countEl) {
                countEl.innerText = tfFormat(tfI18n.roadmaps_count, { count: filtered.length });
            }
            renderRoadmapCards(pageItems);
            renderPagination(totalPages);
        }

        function updateHeaderProgress(nodes) {
            const total = nodes.length;
            const done = nodes.filter(n => appData.progress.includes(parseInt(n.id, 10))).length;
            const perc = total ? Math.round((done / total) * 100) : 0;
            document.getElementById('progress-text').innerText = perc + '%';
            document.getElementById('progress-bar').style.width = perc + '%';
        }

        async function init() {
            try {
                const res = await fetch('?action=roadmap-data&view=list', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                if (!data.success) throw new Error(data.message || tfI18n.load_error);
                appData.nodes = data.nodes || [];
                appData.progress = (data.progress || []).map(id => parseInt(id, 10));
                appData.roadmaps = data.roadmaps || [];

                roadmapIndex = groupRoadmaps(appData.nodes, appData.roadmaps);
                const topicList = Array.from(new Set(roadmapIndex.flatMap(r => r.topics || []))).sort();
                renderTopicsFilter(topicList);
                applyFilters();
                updateHeaderProgress(appData.nodes);
            } catch (e) {
                notify(tfI18n.load_error);
                console.error(e);
            }
        }

        const searchInput = document.getElementById('roadmap-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                searchTerm = e.target.value || '';
                currentPage = 1;
                applyFilters();
            });
        }

        const resetBtn = document.getElementById('roadmap-reset');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                activeTopics = new Set();
                searchTerm = '';
                currentPage = 1;
                if (searchInput) searchInput.value = '';
                const container = document.getElementById('roadmap-topics');
                if (container) {
                    container.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                    const allRadio = container.querySelector('input[name="roadmap-topic-all"]');
                    if (allRadio) allRadio.checked = true;
                }
                applyFilters();
            });
        }

        init();
    </script>
    <?php
    $footerContext = 'ratings';
    include 'includes/footer.php';
    ?>
</body>

</html>