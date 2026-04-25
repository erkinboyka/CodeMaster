<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$pageKey = $pageKey ?? '';
$pageTitle = $pageTitle ?? '';
$pageSubtitle = $pageSubtitle ?? '';
$pageSections = $pageSections ?? [];
$pageHighlights = $pageHighlights ?? [];
$pageCta = $pageCta ?? [];
$supportForm = is_array($supportForm ?? null) ? $supportForm : [];
$supportFlash = is_array($supportFlash ?? null) ? $supportFlash : null;
$icons = [
    'blog' => 'fa-pen-nib',
    'about' => 'fa-orbit',
    'contacts' => 'fa-headset',
    'partners' => 'fa-people-group',
    'support' => 'fa-life-ring',
    'docs' => 'fa-layer-group',
    'charity' => 'fa-heart',
    'privacy' => 'fa-shield-halved',
    'terms' => 'fa-scale-balanced',
];
$icon = $icons[$pageKey] ?? 'fa-book-open';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(function_exists('currentLang') ? currentLang() : 'ru') ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <title><?= htmlspecialchars($pageTitle) ?> - CodeMaster</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=IBM+Plex+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --ink: #0f172a;
            --muted: #475569;
            --glow: rgba(99, 102, 241, 0.15);
        }

        body {
            font-family: 'IBM Plex Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(800px 400px at 10% -10%, rgba(99, 102, 241, 0.25), transparent 55%),
                radial-gradient(700px 380px at 90% -10%, rgba(56, 189, 248, 0.2), transparent 55%),
                #f8fafc;
        }

        .title-font {
            font-family: 'Space Grotesk', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(148, 163, 184, 0.35);
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.12);
        }

        .policy-links {
            position: fixed;
            right: 20px;
            bottom: 22px;
            display: flex;
            gap: 14px;
            font-size: 12px;
            color: #64748b;
            z-index: 40;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 60;
        }

        .modal-backdrop.active {
            display: flex;
        }

        .modal-card {
            width: min(520px, 92vw);
            border-radius: 20px;
            background: #fff;
            border: 1px solid rgba(148, 163, 184, 0.3);
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.2);
            padding: 20px;
        }
    </style>
</head>

<body class="tf-public-motion">
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-4">
            <a href="?action=dashboard" onclick="history.back(); return false;"
                class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-500">
                <i class="fas fa-arrow-left mr-2"></i><?= htmlspecialchars(t('back', 'Назад')) ?>
            </a>
        </div>
        <section class="glass-card rounded-3xl p-6 sm:p-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div
                        class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                        <i class="fas <?= htmlspecialchars($icon) ?> text-xl"></i>
                    </div>
                    <div>
                        <h1 class="title-font text-3xl sm:text-4xl font-bold"><?= htmlspecialchars($pageTitle) ?></h1>
                        <?php if ($pageSubtitle): ?>
                            <p class="mt-2 text-base text-slate-600 max-w-2xl"><?= htmlspecialchars($pageSubtitle) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($pageCta)): ?>
                    <div class="rounded-2xl bg-slate-900 text-white p-5 w-full lg:w-80">
                        <div class="text-sm uppercase tracking-widest text-slate-300">
                            <?= htmlspecialchars($pageCta['title'] ?? '') ?></div>
                        <div class="mt-2 text-sm text-slate-200"><?= htmlspecialchars($pageCta['body'] ?? '') ?></div>
                        <?php if (!empty($pageCta['button'])): ?>
                            <a href="?action=support"
                                class="mt-4 inline-flex items-center text-sm font-semibold text-indigo-300 hover:text-indigo-200">
                                <?= htmlspecialchars($pageCta['button']) ?>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($pageHighlights) && is_array($pageHighlights)): ?>
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php foreach ($pageHighlights as $highlight): ?>
                        <div class="rounded-2xl border border-slate-200 bg-white/80 p-4">
                            <h3 class="title-font text-lg font-semibold"><?= htmlspecialchars($highlight['title'] ?? '') ?></h3>
                            <p class="mt-2 text-sm text-slate-600"><?= htmlspecialchars($highlight['body'] ?? '') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($pageSections) && is_array($pageSections)): ?>
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <?php foreach ($pageSections as $section): ?>
                        <div class="rounded-2xl border border-slate-200 bg-white/90 p-5">
                            <?php if (!empty($section['title'])): ?>
                                <h2 class="title-font text-xl font-semibold"><?= htmlspecialchars($section['title']) ?></h2>
                            <?php endif; ?>
                            <?php if (!empty($section['body'])): ?>
                                <p class="mt-2 text-sm text-slate-600 leading-relaxed"><?= htmlspecialchars($section['body']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($pageKey === 'support'): ?>
                <div class="mt-10 rounded-2xl border border-slate-200 bg-white/95 p-5 sm:p-6">
                    <h2 class="title-font text-2xl font-semibold">
                        <?= htmlspecialchars(t('support_form_title', 'Напишите в поддержку')) ?></h2>
                    <p class="mt-2 text-sm text-slate-600">
                        <?= htmlspecialchars(t('support_form_subtitle', 'Опишите вопрос, и мы ответим как можно быстрее.')) ?>
                    </p>

                    <?php if (!empty($supportFlash)): ?>
                        <div
                            class="mt-4 rounded-xl px-4 py-3 text-sm <?= ($supportFlash['type'] ?? '') === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                            <?= htmlspecialchars((string) ($supportFlash['message'] ?? '')) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="?action=support" class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="block">
                            <span
                                class="text-sm font-medium text-slate-700"><?= htmlspecialchars(t('support_form_name', 'Имя')) ?></span>
                            <input type="text" name="name" required maxlength="120"
                                value="<?= htmlspecialchars((string) ($supportForm['name'] ?? '')) ?>"
                                class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </label>
                        <label class="block">
                            <span
                                class="text-sm font-medium text-slate-700"><?= htmlspecialchars(t('support_form_email', 'Email')) ?></span>
                            <input type="email" name="email" required maxlength="190"
                                value="<?= htmlspecialchars((string) ($supportForm['email'] ?? '')) ?>"
                                class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </label>
                        <label class="block md:col-span-2">
                            <span
                                class="text-sm font-medium text-slate-700"><?= htmlspecialchars(t('support_form_subject', 'Тема')) ?></span>
                            <input type="text" name="subject" required maxlength="190"
                                value="<?= htmlspecialchars((string) ($supportForm['subject'] ?? '')) ?>"
                                class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </label>
                        <label class="block md:col-span-2">
                            <span
                                class="text-sm font-medium text-slate-700"><?= htmlspecialchars(t('support_form_message', 'Сообщение')) ?></span>
                            <textarea name="message" required maxlength="5000" rows="6"
                                class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"><?= htmlspecialchars((string) ($supportForm['message'] ?? '')) ?></textarea>
                        </label>
                        <label class="block">
                            <span
                                class="text-sm font-medium text-slate-700"><?= htmlspecialchars(t('support_form_priority', 'Приоритет')) ?></span>
                            <?php $priority = (string) ($supportForm['priority'] ?? 'normal'); ?>
                            <select name="priority"
                                class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="low" <?= $priority === 'low' ? 'selected' : '' ?>><?= htmlspecialchars(t('support_form_priority_low', 'Низкий')) ?></option>
                                <option value="normal" <?= $priority === 'normal' ? 'selected' : '' ?>><?= htmlspecialchars(t('support_form_priority_normal', 'Обычный')) ?></option>
                                <option value="high" <?= $priority === 'high' ? 'selected' : '' ?>><?= htmlspecialchars(t('support_form_priority_high', 'Высокий')) ?></option>
                            </select>
                        </label>
                        <div class="md:col-span-2">
                            <button type="submit"
                                class="inline-flex items-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition">
                                <i
                                    class="fas fa-paper-plane mr-2"></i><?= htmlspecialchars(t('support_form_submit', 'Отправить')) ?>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Кнопки для открытия модальных окон -->
    <div class="fixed left-6 bottom-6 flex gap-3 z-50">
        <button id="openChat" class="w-12 h-12 rounded-full bg-indigo-600 text-white shadow-lg flex items-center justify-center hover:bg-indigo-500 transition">
            <i class="fas fa-comments"></i>
        </button>
        <button id="openAi" class="w-12 h-12 rounded-full bg-emerald-600 text-white shadow-lg flex items-center justify-center hover:bg-emerald-500 transition">
            <i class="fas fa-robot"></i>
        </button>
    </div>

    <div class="policy-links">
        <a href="?action=privacy" class="hover:text-slate-700"><?= t('privacy_policy', 'Политика конфиденциальности') ?></a>
        <a href="?action=terms" class="hover:text-slate-700"><?= t('terms_of_use', 'Условия использования') ?></a>
    </div>

    <div class="modal-backdrop" id="chatModal">
        <div class="modal-card">
            <div class="flex items-center justify-between">
                <strong><?= t('chat_title', 'Чат') ?></strong>
                <button class="text-slate-500 hover:text-slate-700" data-close-modal>&times;</button>
            </div>
            <p class="mt-2 text-sm text-slate-600"><?= t('chat_hint', 'Задайте вопрос, мы поможем.') ?></p>
            <textarea class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" rows="4" placeholder="<?= t('chat_placeholder', 'Напишите сообщение...') ?>"></textarea>
            <div class="mt-3 text-right">
                <button class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    <?= t('chat_send', 'Отправить') ?>
                </button>
            </div>
        </div>
    </div>

    <div class="modal-backdrop" id="aiModal">
        <div class="modal-card">
            <div class="flex items-center justify-between">
                <strong><?= t('ai_title', 'AI Помощник') ?></strong>
                <button class="text-slate-500 hover:text-slate-700" data-close-modal>&times;</button>
            </div>
            <p class="mt-2 text-sm text-slate-600"><?= t('ai_hint', 'Опишите задачу — предложим решение или план.') ?></p>
            <textarea class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" rows="4" placeholder="<?= t('ai_placeholder', 'Что нужно сделать?') ?>"></textarea>
            <div class="mt-3 text-right">
                <button class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    <?= t('ai_start', 'Получить ответ') ?>
                </button>
            </div>
        </div>
    </div>

    <script>
        const chatModal = document.getElementById('chatModal');
        const aiModal = document.getElementById('aiModal');
        const openChat = document.getElementById('openChat');
        const openAi = document.getElementById('openAi');

        const syncModalState = () => {
            const anyOpen = document.querySelector('.modal-backdrop.active');
            document.body.classList.toggle('tf-modal-open', !!anyOpen);
        };

        const closeModal = (modal) => {
            if (!modal) return;
            modal.classList.remove('active');
            syncModalState();
        };

        const openModal = (modal) => {
            if (!modal) return;
            modal.classList.add('active');
            syncModalState();
        };

        openChat?.addEventListener('click', () => openModal(chatModal));
        openAi?.addEventListener('click', () => openModal(aiModal));

        [chatModal, aiModal].forEach(modal => {
            modal?.addEventListener('click', (e) => {
                if (e.target === modal) closeModal(modal);
            });
            modal?.querySelectorAll('[data-close-modal]').forEach(btn => {
                btn.addEventListener('click', () => closeModal(modal));
            });
        });
    </script>
</body>

</html>