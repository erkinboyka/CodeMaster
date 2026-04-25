<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Git тренажер - CodeMaster</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --tf-ink: #0f172a;
            --tf-ink-2: #111827;
            --tf-surface: #f8fafc;
            --tf-card: #ffffff;
            --tf-border: #e2e8f0;
            --tf-accent: #4f46e5;
            --tf-accent-2: #0ea5e9;
            --tf-success: #16a34a;
            --tf-warn: #f59e0b;
            --tf-danger: #ef4444;
        }

        body {
            background: var(--tf-surface);
            color: var(--tf-ink);
        }

        .trainer-shell {
            min-height: calc(100vh - 64px);
        }

        .panel {
            background: var(--tf-card);
            border: 1px solid var(--tf-border);
            border-radius: 16px;
        }

        .panel-header {
            border-bottom: 1px solid var(--tf-border);
        }

        .command-input {
            background: #0b1220;
            color: #e5e7eb;
            border: 1px solid #1f2937;
        }

        .command-input:focus {
            outline: 2px solid rgba(79, 70, 229, 0.35);
            outline-offset: 0;
        }

        .console {
            background: #0b1220;
            color: #e5e7eb;
            border-radius: 12px;
        }

        .console-line {
            white-space: pre-wrap;
        }

        .pill {
            border: 1px solid var(--tf-border);
            border-radius: 999px;
        }

        .badge {
            background: rgba(79, 70, 229, 0.12);
            color: #312e81;
        }

        .level-active {
            border-color: var(--tf-accent);
            box-shadow: 0 0 0 1px rgba(79, 70, 229, 0.25);
        }

        .graph-wrap {
            background:
                radial-gradient(circle at 20% 20%, rgba(56, 189, 248, 0.16), transparent 45%),
                radial-gradient(circle at 80% 10%, rgba(99, 102, 241, 0.18), transparent 40%),
                radial-gradient(circle at 60% 90%, rgba(14, 165, 233, 0.18), transparent 40%),
                #0b1020;
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.35);
        }

        .graph-title {
            color: #e2e8f0;
        }

        .graph-sub {
            color: #94a3b8;
        }

        .graph-grid line {
            stroke: rgba(148, 163, 184, 0.12);
        }

        .graph-edge {
            stroke: rgba(148, 163, 184, 0.55);
            stroke-width: 2.4;
            fill: none;
        }

        .graph-edge.merge {
            stroke: rgba(56, 189, 248, 0.65);
        }

        .graph-node {
            transition: transform 0.2s ease, filter 0.2s ease;
        }

        .graph-node:hover {
            transform: scale(1.05);
            filter: drop-shadow(0 0 10px rgba(56, 189, 248, 0.5));
        }

        .branch-tag {
            filter: drop-shadow(0 6px 12px rgba(15, 23, 42, 0.35));
        }

        .head-ring {
            stroke: #22c55e;
            stroke-width: 2.6;
            fill: none;
        }

        .head-arrow {
            fill: #22c55e;
        }

        .btn {
            background: var(--tf-accent);
            color: #fff;
            border-radius: 10px;
            padding: 8px 12px;
            font-weight: 600;
        }

        .btn:hover {
            background: #4338ca;
        }

        .btn-ghost {
            background: transparent;
            color: #1f2937;
            border: 1px solid var(--tf-border);
        }

        .btn-ghost:hover {
            border-color: #cbd5f5;
            background: #f8fafc;
        }

        .kbd {
            border: 1px solid #334155;
            border-bottom-width: 2px;
            background: #111827;
            color: #e2e8f0;
            border-radius: 6px;
            padding: 2px 6px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <section class="trainer-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">
        <div class="flex flex-col gap-4 pt-6 pb-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Тренажер Git</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Ветки, merge и rebase в симуляторе</h1>
                </div>
                <div class="flex items-center gap-2">
                    <button id="reset-level" class="btn-ghost text-sm px-3 py-2 rounded-lg">Сбросить уровень</button>
                    <button id="reset-all" class="btn text-sm">Начать заново</button>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-sm text-slate-600">
                <span class="pill px-3 py-1">Команды: <span class="font-medium">init, commit, branch, switch, merge,
                        rebase</span></span>
                <span class="pill px-3 py-1">Подсказка: <span class="kbd">Enter</span> чтобы выполнить</span>
                <span class="pill px-3 py-1">Симуляция: без реального Git</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-6">
            <aside class="panel p-4">
                <div class="panel-header pb-3 mb-4">
                    <h2 class="text-base font-semibold text-slate-900">Уровни</h2>
                    <p class="text-sm text-slate-500">Проходи шаг за шагом</p>
                </div>
                <div id="levels" class="flex flex-col gap-3"></div>
            </aside>

            <main class="flex flex-col gap-6">
                <section class="panel p-4">
                    <div class="panel-header pb-3 mb-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Задача</h2>
                            <p id="level-title" class="text-sm text-slate-500"></p>
                        </div>
                        <span id="level-status" class="badge text-xs font-semibold px-2 py-1 rounded-full">Р’
                            процессе</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-[1.1fr_0.9fr] gap-4">
                        <div>
                            <p id="level-description" class="text-sm text-slate-700 leading-relaxed"></p>
                            <div class="mt-3 flex flex-wrap gap-2" id="level-goals"></div>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Подсказки</p>
                            <ul id="level-hints" class="mt-2 text-sm text-slate-600 list-disc pl-5"></ul>
                        </div>
                    </div>
                </section>

                <section class="grid grid-cols-1 xl:grid-cols-[1.4fr_1fr] gap-6">
                    <div class="panel p-4">
                        <div class="panel-header pb-3 mb-4 flex items-center justify-between">
                            <div>
                                <h2 class="text-base font-semibold text-slate-900">Граф коммитов</h2>
                                <p class="text-sm text-slate-500">Визуальная история репозитория</p>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <span class="flex items-center gap-1"><span
                                        class="w-2 h-2 rounded-full bg-emerald-400"></span>HEAD</span>
                                <span class="flex items-center gap-1"><span
                                        class="w-2 h-2 rounded-full bg-indigo-400"></span>ветка</span>
                            </div>
                        </div>
                        <div class="graph-wrap p-3">
                            <div class="flex items-center justify-between mb-2">
                                <p class="graph-title text-sm font-semibold">Repo state</p>
                                <p id="graph-subtitle" class="graph-sub text-xs"></p>
                            </div>
                            <svg id="graph" class="w-full" height="360" viewBox="0 0 700 360"
                                preserveAspectRatio="xMidYMid meet"></svg>
                        </div>
                    </div>

                    <div class="panel p-4 flex flex-col gap-4">
                        <div class="panel-header pb-3">
                            <h2 class="text-base font-semibold text-slate-900">Консоль</h2>
                            <p class="text-sm text-slate-500">Вводи команды Git</p>
                        </div>
                        <div id="console" class="console p-3 h-48 overflow-auto text-sm"></div>
                        <div>
                            <label class="text-xs uppercase tracking-wide text-slate-400">Команда</label>
                            <input id="command" class="command-input w-full mt-1 rounded-lg px-3 py-2 font-mono text-sm"
                                placeholder="git commit -m &quot;init&quot;" />
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs text-slate-500">
                            <span class="pill px-2 py-1">git init</span>
                            <span class="pill px-2 py-1">git commit -m "msg"</span>
                            <span class="pill px-2 py-1">git branch feature</span>
                            <span class="pill px-2 py-1">git switch feature</span>
                            <span class="pill px-2 py-1">git merge feature</span>
                            <span class="pill px-2 py-1">git rebase main</span>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </section>

    <script>
        const levels = [
            {
                id: 'init',
                title: 'Инициализация',
                description: 'Создай новый репозиторий и первый коммит.',
                goals: ['репозиторий инициализирован', 'есть хотя бы 1 коммит'],
                hints: ['Выполни: git init', 'Затем: git commit -m "init"'],
                setup: () => createState(),
                check: (s) => s.initialized && s.commits.length >= 1,
            },
            {
                id: 'branch',
                title: 'Новая ветка',
                description: 'Создай ветку feature и переключись на нее.',
                goals: ['ветка feature создана', 'HEAD на feature'],
                hints: ['git branch feature', 'git switch feature'],
                setup: () => {
                    const s = createState(true);
                    commit(s, 'init');
                    return s;
                },
                check: (s) => !!s.branches.feature && s.currentBranch === 'feature',
            },
            {
                id: 'feature-commit',
                title: 'Коммит в feature',
                description: 'Сделай новый коммит в feature, чтобы ветка отличалась от main.',
                goals: ['feature впереди main'],
                hints: ['git commit -m "feat: change"'],
                setup: () => {
                    const s = createState(true);
                    commit(s, 'init');
                    createBranch(s, 'feature');
                    switchBranch(s, 'feature');
                    return s;
                },
                check: (s) => s.branches.feature && s.branches.main && s.branches.feature !== s.branches.main,
            },
            {
                id: 'merge',
                title: 'Слияние',
                description: 'Слей feature в main. Получится merge-коммит.',
                goals: ['main содержит merge-коммит'],
                hints: ['Переключись на main', 'git merge feature'],
                setup: () => {
                    const s = createState(true);
                    commit(s, 'init');
                    createBranch(s, 'feature');
                    switchBranch(s, 'feature');
                    commit(s, 'feat');
                    switchBranch(s, 'main');
                    return s;
                },
                check: (s) => {
                    const head = s.commitsById[s.branches.main];
                    return head && head.parents.length === 2;
                },
            },
            {
                id: 'rebase',
                title: 'Rebase',
                description: 'Перенеси feature поверх main с помощью rebase.',
                goals: ['feature основана на main', 'без merge-коммита'],
                hints: ['Переключись на feature', 'git rebase main'],
                setup: () => {
                    const s = createState(true);
                    commit(s, 'init');
                    createBranch(s, 'feature');
                    switchBranch(s, 'feature');
                    commit(s, 'feat A');
                    switchBranch(s, 'main');
                    commit(s, 'hotfix');
                    return s;
                },
                check: (s) => {
                    const featureHead = s.branches.feature;
                    const mainHead = s.branches.main;
                    if (!featureHead || !mainHead) return false;
                    const head = s.commitsById[featureHead];
                    if (!head || head.parents.length !== 1) return false;
                    return isAncestor(s, mainHead, featureHead);
                },
            },
            {
                id: 'remote-flow',
                title: 'Базовый workflow',
                description: 'Создай ветку bugfix, сделай коммит и вернись в main.',
                goals: ['ветка bugfix создана', 'в main не меньше 2 коммитов'],
                hints: ['git branch bugfix', 'git switch bugfix', 'git commit -m "fix"', 'git switch main'],
                setup: () => {
                    const s = createState(true);
                    commit(s, 'init');
                    commit(s, 'prep');
                    return s;
                },
                check: (s) => {
                    const mainCommits = countChain(s, s.branches.main);
                    return !!s.branches.bugfix && mainCommits >= 2 && s.currentBranch === 'main';
                },
            },
        ];

        const consoleEl = document.getElementById('console');
        const cmdInput = document.getElementById('command');
        const levelsEl = document.getElementById('levels');
        const graphEl = document.getElementById('graph');
        const levelTitleEl = document.getElementById('level-title');
        const levelDescEl = document.getElementById('level-description');
        const levelGoalsEl = document.getElementById('level-goals');
        const levelHintsEl = document.getElementById('level-hints');
        const levelStatusEl = document.getElementById('level-status');
        const graphSubtitle = document.getElementById('graph-subtitle');

        let state = null;
        let currentLevel = 0;

        function createState(initialized = false) {
            return {
                initialized,
                commits: [],
                commitsById: {},
                branches: {},
                branchOrder: [],
                head: { ref: initialized ? 'main' : null, commit: null },
                currentBranch: initialized ? 'main' : null,
                counter: 0,
            };
        }

        function addConsole(line, type = 'info') {
            const row = document.createElement('div');
            row.className = 'console-line';
            row.textContent = line;
            if (type === 'error') row.style.color = '#f87171';
            if (type === 'success') row.style.color = '#34d399';
            consoleEl.appendChild(row);
            consoleEl.scrollTop = consoleEl.scrollHeight;
        }

        function shortId(n) {
            const base = 'abcdef0123456789';
            let out = '';
            for (let i = 0; i < 6; i++) {
                out += base[(n + i * 7) % base.length];
            }
            return out;
        }

        function commit(s, message = 'commit') {
            if (!s.initialized) return { ok: false, msg: 'Репозиторий не инициализирован.' };
            const id = shortId(++s.counter);
            const parents = [];
            if (s.head.commit) parents.push(s.head.commit);
            const node = { id, message, parents, branch: s.currentBranch };
            s.commits.push(node);
            s.commitsById[id] = node;
            if (s.currentBranch) {
                s.branches[s.currentBranch] = id;
            }
            s.head.commit = id;
            return { ok: true, msg: `Создан коммит ${id}` };
        }

        function createBranch(s, name) {
            if (!s.initialized) return { ok: false, msg: 'Сначала git init.' };
            if (s.branches[name]) return { ok: false, msg: 'Такая ветка уже есть.' };
            s.branches[name] = s.head.commit;
            s.branchOrder.push(name);
            return { ok: true, msg: `Ветка ${name} создана.` };
        }

        function switchBranch(s, name) {
            if (!s.branches[name]) return { ok: false, msg: 'Ветка не найдена.' };
            s.currentBranch = name;
            s.head.ref = name;
            s.head.commit = s.branches[name];
            return { ok: true, msg: `Переключено на ${name}.` };
        }

        function mergeBranch(s, name) {
            if (!s.branches[name]) return { ok: false, msg: 'Ветка не найдена.' };
            if (name === s.currentBranch) return { ok: false, msg: 'Нельзя слить ветку в саму себя.' };
            const current = s.head.commit;
            const other = s.branches[name];
            if (!current || !other) return { ok: false, msg: 'Нет коммитов для merge.' };
            const id = shortId(++s.counter);
            const node = { id, message: `merge ${name}`, parents: [current, other], branch: s.currentBranch };
            s.commits.push(node);
            s.commitsById[id] = node;
            s.branches[s.currentBranch] = id;
            s.head.commit = id;
            return { ok: true, msg: `Слияние ${name} -> ${s.currentBranch}.` };
        }

        function isAncestor(s, ancestorId, childId) {
            const visited = new Set();
            const stack = [childId];
            while (stack.length) {
                const id = stack.pop();
                if (!id || visited.has(id)) continue;
                visited.add(id);
                if (id === ancestorId) return true;
                const node = s.commitsById[id];
                if (node && node.parents) stack.push(...node.parents);
            }
            return false;
        }

        function countChain(s, headId) {
            let count = 0;
            let cursor = headId;
            const visited = new Set();
            while (cursor && !visited.has(cursor)) {
                visited.add(cursor);
                count += 1;
                const node = s.commitsById[cursor];
                cursor = node && node.parents ? node.parents[0] : null;
            }
            return count;
        }

        function rebaseBranch(s, ontoName) {
            const onto = s.branches[ontoName];
            if (!onto) return { ok: false, msg: 'Ветка не найдена.' };
            if (!s.currentBranch) return { ok: false, msg: 'HEAD не на ветке.' };
            if (s.currentBranch === ontoName) return { ok: false, msg: 'Нечего перебазировать.' };
            const head = s.branches[s.currentBranch];
            if (!head) return { ok: false, msg: 'Нет коммитов.' };
            const chain = [];
            let cursor = head;
            while (cursor && cursor !== onto && !isAncestor(s, cursor, onto)) {
                const node = s.commitsById[cursor];
                if (!node) break;
                chain.push(node);
                cursor = node.parents[0];
                if (cursor && cursor === onto) break;
            }
            if (chain.length === 0) return { ok: false, msg: 'Нечего переносить.' };
            let base = onto;
            for (let i = chain.length - 1; i >= 0; i--) {
                const orig = chain[i];
                const id = shortId(++s.counter);
                const node = { id, message: orig.message + ' (rebased)', parents: base ? [base] : [], branch: s.currentBranch };
                s.commits.push(node);
                s.commitsById[id] = node;
                base = id;
            }
            s.branches[s.currentBranch] = base;
            s.head.commit = base;
            return { ok: true, msg: `Rebase на ${ontoName} выполнен.` };
        }

        function resetHard(s, ref) {
            if (!ref) return { ok: false, msg: 'Укажи ref.' };
            const id = s.branches[ref] || s.commitsById[ref] ? ref : null;
            const commitId = s.branches[ref] || ref;
            if (!s.commitsById[commitId]) return { ok: false, msg: 'Ref не найден.' };
            if (!s.currentBranch) return { ok: false, msg: 'HEAD не на ветке.' };
            s.branches[s.currentBranch] = commitId;
            s.head.commit = commitId;
            return { ok: true, msg: `HEAD перемещен на ${commitId}.` };
        }

        function statusLine(s) {
            if (!s.initialized) return 'Репозиторий не инициализирован.';
            if (!s.currentBranch) return `HEAD detached at ${s.head.commit || 'none'}`;
            return `На ветке ${s.currentBranch}, HEAD=${s.head.commit || 'empty'}`;
        }

        function renderLevels() {
            levelsEl.innerHTML = '';
            levels.forEach((lvl, idx) => {
                const item = document.createElement('button');
                item.className = 'text-left p-3 rounded-xl border border-slate-200 hover:border-indigo-200 transition ' + (idx === currentLevel ? 'level-active' : '');
                item.innerHTML = `
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-slate-900">${idx + 1}. ${lvl.title}</p>
                        <span class="text-xs text-slate-400">${idx === currentLevel ? 'Текущий' : ''}</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">${lvl.description}</p>
                `;
                item.addEventListener('click', () => loadLevel(idx));
                levelsEl.appendChild(item);
            });
        }

        function renderLevelInfo() {
            const lvl = levels[currentLevel];
            levelTitleEl.textContent = lvl.title;
            levelDescEl.textContent = lvl.description;
            levelGoalsEl.innerHTML = '';
            lvl.goals.forEach(g => {
                const pill = document.createElement('span');
                pill.className = 'pill px-2 py-1 text-xs text-slate-600';
                pill.textContent = g;
                levelGoalsEl.appendChild(pill);
            });
            levelHintsEl.innerHTML = '';
            lvl.hints.forEach(h => {
                const li = document.createElement('li');
                li.textContent = h;
                levelHintsEl.appendChild(li);
            });
        }

        function renderGraph(s) {
            const width = 700;
            const height = 360;
            graphEl.setAttribute('viewBox', `0 0 ${width} ${height}`);
            graphEl.innerHTML = '';
            const margin = { x: 90, y: 40 };
            const stepY = 58;
            const branchNames = Object.keys(s.branches);
            if (branchNames.length === 0) graphSubtitle.textContent = 'Нет веток';
            else graphSubtitle.textContent = `Ветки: ${branchNames.join(', ')}`;
            if (s.branchOrder.length === 0 && s.branches.main) s.branchOrder = ['main'];

            const branchIndex = {};
            const order = s.branchOrder.length ? s.branchOrder : branchNames;
            order.forEach((b, i) => { branchIndex[b] = i; });

            const colors = ['#6366f1', '#38bdf8', '#f472b6', '#f59e0b', '#22c55e', '#a855f7'];
            const branchColor = {};
            order.forEach((b, i) => { branchColor[b] = colors[i % colors.length]; });

            const grid = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            grid.setAttribute('class', 'graph-grid');
            for (let y = margin.y; y < height - 20; y += stepY) {
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line.setAttribute('x1', 20);
                line.setAttribute('y1', y);
                line.setAttribute('x2', width - 20);
                line.setAttribute('y2', y);
                grid.appendChild(line);
            }
            graphEl.appendChild(grid);

            const positions = {};
            s.commits.forEach((c, idx) => {
                const col = branchIndex[c.branch] ?? 0;
                positions[c.id] = {
                    x: margin.x + col * 120,
                    y: margin.y + idx * stepY,
                };
            });

            s.commits.forEach((c) => {
                const p1 = positions[c.id];
                c.parents.forEach(parentId => {
                    const p2 = positions[parentId];
                    if (!p1 || !p2) return;
                    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    const midX = (p1.x + p2.x) / 2;
                    const d = `M ${p1.x} ${p1.y} C ${midX} ${p1.y}, ${midX} ${p2.y}, ${p2.x} ${p2.y}`;
                    path.setAttribute('d', d);
                    path.setAttribute('class', 'graph-edge' + (c.parents.length > 1 ? ' merge' : ''));
                    graphEl.appendChild(path);
                });
            });

            s.commits.forEach((c) => {
                const p = positions[c.id];
                if (!p) return;
                const nodeGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                nodeGroup.setAttribute('class', 'graph-node');
                const glow = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                glow.setAttribute('cx', p.x);
                glow.setAttribute('cy', p.y);
                glow.setAttribute('r', '16');
                glow.setAttribute('fill', 'rgba(56, 189, 248, 0.18)');
                nodeGroup.appendChild(glow);
                const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('cx', p.x);
                circle.setAttribute('cy', p.y);
                circle.setAttribute('r', '9');
                circle.setAttribute('fill', branchColor[c.branch] || '#38bdf8');
                circle.setAttribute('stroke', '#0b1020');
                circle.setAttribute('stroke-width', '2');
                nodeGroup.appendChild(circle);
                const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                label.setAttribute('x', p.x + 18);
                label.setAttribute('y', p.y + 5);
                label.setAttribute('fill', '#e2e8f0');
                label.setAttribute('font-size', '12');
                label.textContent = c.id;
                nodeGroup.appendChild(label);
                graphEl.appendChild(nodeGroup);
            });

            Object.entries(s.branches).forEach(([name, commitId]) => {
                const p = positions[commitId];
                if (!p) return;
                const tag = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                tag.setAttribute('x', p.x - 14);
                tag.setAttribute('y', p.y - 32);
                tag.setAttribute('width', 76);
                tag.setAttribute('height', 18);
                tag.setAttribute('rx', 6);
                tag.setAttribute('fill', branchColor[name] || '#6366f1');
                tag.setAttribute('class', 'branch-tag');
                graphEl.appendChild(tag);
                const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                text.setAttribute('x', p.x - 8);
                text.setAttribute('y', p.y - 19);
                text.setAttribute('fill', '#eef2ff');
                text.setAttribute('font-size', '11');
                text.textContent = name;
                graphEl.appendChild(text);
            });

            if (s.head.commit) {
                const p = positions[s.head.commit];
                if (p) {
                    const head = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    head.setAttribute('cx', p.x);
                    head.setAttribute('cy', p.y);
                    head.setAttribute('r', '18');
                    head.setAttribute('class', 'head-ring');
                    graphEl.appendChild(head);
                    const arrow = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    const ax = p.x - 26;
                    const ay = p.y - 6;
                    arrow.setAttribute('d', `M ${ax} ${ay} l -10 6 l 10 6 z`);
                    arrow.setAttribute('class', 'head-arrow');
                    graphEl.appendChild(arrow);
                }
            }
        }

        function render() {
            renderLevels();
            renderLevelInfo();
            renderGraph(state);
            updateStatus();
        }

        function updateStatus() {
            const passed = levels[currentLevel].check(state);
            if (passed) {
                levelStatusEl.textContent = 'Пройдено';
                levelStatusEl.className = 'badge text-xs font-semibold px-2 py-1 rounded-full';
            } else {
                levelStatusEl.textContent = 'Р’ процессе';
                levelStatusEl.className = 'badge text-xs font-semibold px-2 py-1 rounded-full';
            }
        }

        function loadLevel(idx) {
            currentLevel = idx;
            state = levels[idx].setup();
            consoleEl.innerHTML = '';
            addConsole(`Уровень ${idx + 1}: ${levels[idx].title}`, 'success');
            addConsole(statusLine(state));
            render();
        }

        function parseCommand(input) {
            const trimmed = input.trim();
            if (!trimmed) return null;
            if (trimmed === 'clear') return { cmd: 'clear' };
            if (!trimmed.startsWith('git ')) return { cmd: 'error', msg: 'Использовать команды git.' };
            const raw = trimmed.slice(4);
            const matchMsg = raw.match(/-m\s+\"([^\"]+)\"/);
            const msg = matchMsg ? matchMsg[1] : null;
            const parts = raw.replace(/-m\s+\"([^\"]+)\"/, '').trim().split(/\s+/);
            return { cmd: parts[0], args: parts.slice(1), msg };
        }

        function runCommand(input) {
            const parsed = parseCommand(input);
            if (!parsed) return;
            if (parsed.cmd === 'clear') {
                consoleEl.innerHTML = '';
                return;
            }
            if (parsed.cmd === 'error') {
                addConsole(parsed.msg, 'error');
                return;
            }
            let res = { ok: false, msg: 'Команда не распознана.' };
            switch (parsed.cmd) {
                case 'init':
                    if (state.initialized) res = { ok: false, msg: 'Репозиторий уже инициализирован.' };
                    else {
                        state.initialized = true;
                        state.branches.main = null;
                        state.branchOrder = ['main'];
                        state.currentBranch = 'main';
                        state.head.ref = 'main';
                        res = { ok: true, msg: 'Репозиторий инициализирован.' };
                    }
                    break;
                case 'status':
                    res = { ok: true, msg: statusLine(state) };
                    break;
                case 'commit':
                    res = commit(state, parsed.msg || 'commit');
                    break;
                case 'branch':
                    res = parsed.args[0] ? createBranch(state, parsed.args[0]) : { ok: false, msg: 'Укажи имя ветки.' };
                    break;
                case 'switch':
                case 'checkout':
                    if (parsed.args[0] === '-c' || parsed.args[0] === '-b') {
                        const name = parsed.args[1];
                        if (!name) res = { ok: false, msg: 'Укажи имя ветки.' };
                        else {
                            const created = createBranch(state, name);
                            if (!created.ok) res = created;
                            else res = switchBranch(state, name);
                        }
                    } else {
                        res = parsed.args[0] ? switchBranch(state, parsed.args[0]) : { ok: false, msg: 'Укажи ветку.' };
                    }
                    break;
                case 'merge':
                    res = parsed.args[0] ? mergeBranch(state, parsed.args[0]) : { ok: false, msg: 'Укажи ветку.' };
                    break;
                case 'rebase':
                    res = parsed.args[0] ? rebaseBranch(state, parsed.args[0]) : { ok: false, msg: 'Укажи ветку.' };
                    break;
                case 'log':
                    res = { ok: true, msg: state.commits.slice().reverse().map(c => `${c.id} ${c.message}`).join('\n') || 'Нет коммитов.' };
                    break;
                case 'reset':
                    if (parsed.args[0] === '--hard') res = resetHard(state, parsed.args[1]);
                    else res = { ok: false, msg: 'Поддерживается только reset --hard.' };
                    break;
                case 'help':
                    res = { ok: true, msg: 'Команды: init, status, commit -m, branch, switch, merge, rebase, log, reset --hard.' };
                    break;
                default:
                    res = { ok: false, msg: 'Команда не распознана.' };
            }
            addConsole(`$ ${input}`);
            addConsole(res.msg, res.ok ? 'success' : 'error');
            render();
        }

        cmdInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                runCommand(cmdInput.value);
                cmdInput.value = '';
            }
        });
        document.getElementById('reset-level').addEventListener('click', () => loadLevel(currentLevel));
        document.getElementById('reset-all').addEventListener('click', () => loadLevel(0));

        loadLevel(0);
    </script>
</body>

</html>

