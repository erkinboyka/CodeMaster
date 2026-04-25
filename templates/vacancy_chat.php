<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$chatApp = $chatApp ?? [];
$isEmployer = !empty($chatApp['owner_id']) && (int) $chatApp['owner_id'] === (int) ($user['id'] ?? 0);
$isCandidate = (int) ($chatApp['user_id'] ?? 0) === (int) ($user['id'] ?? 0);
$employmentStatus = $chatApp['employment_status'] ?? 'pending';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('vac_chat_page_title') ?> - CodeMaster</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }

        .btn-primary {
            background: #4f46e5;
            color: #fff;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        .btn-ghost {
            background: #fff;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-ghost:hover {
            border-color: #c7d2fe;
            color: #4f46e5;
            background: #f8faff;
        }

        .file-input-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .file-pill {
            min-height: 42px;
            display: flex;
            align-items: center;
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            color: #64748b;
            background: #fff;
        }

        .chat-surface {
            background: #f1f5f9;
            border-radius: 18px;
            padding: 16px;
        }

        .chat-bubble {
            max-width: 75%;
            padding: 10px 14px;
            border-radius: 16px;
            position: relative;
        }

        .chat-bubble.mine {
            background: #4f46e5;
            color: #fff;
            border-bottom-right-radius: 6px;
        }

        .chat-bubble.their {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            border-bottom-left-radius: 6px;
        }

        .chat-msg-text {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .chat-meta {
            font-size: 10px;
            opacity: 0.7;
            margin-top: 6px;
        }

        .chat-avatar {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            flex-shrink: 0;
        }

        .break-anywhere {
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        @media (max-width: 640px) {
            .chat-bubble {
                max-width: 92%;
            }

            .chat-surface {
                padding: 12px;
            }

            #chat-messages {
                max-height: 58vh;
            }

            .chat-input-row {
                flex-direction: column;
            }

            .chat-input-row .btn-primary {
                width: 100%;
            }

            .doc-upload-row {
                align-items: stretch;
            }

            .doc-upload-row .btn-primary,
            .doc-upload-row .btn-ghost {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="max-w-6xl mx-auto px-4 md:px-6 py-6 md:py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <a href="?action=vacancies"
                    class="text-sm text-slate-500 hover:text-indigo-600 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-1"></i> <?= t('vac_chat_back') ?>
                </a>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold mt-2 break-anywhere">
                    <?= t('vac_chat_title') ?>: <?= htmlspecialchars($chatApp['vacancy_title'] ?? '') ?>
                </h1>
                <p class="text-slate-500 break-anywhere"><?= htmlspecialchars($chatApp['company'] ?? '') ?></p>
            </div>
            <div class="card p-4 flex items-center gap-3 w-full md:w-auto justify-between">
                <span class="text-xs uppercase text-slate-500"><?= t('vac_chat_status') ?></span>
                <span id="employment-status-badge"
                    class="px-3 py-1 rounded-full text-xs font-semibold
                <?= $employmentStatus === 'successful' ? 'bg-emerald-100 text-emerald-700' : ($employmentStatus === 'unsuccessful' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600') ?>">
                    <?= $employmentStatus === 'successful' ? t('vac_chat_status_success') : ($employmentStatus === 'unsuccessful' ? t('vac_chat_status_fail') : t('vac_chat_status_pending')) ?>
                </span>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold"><?= t('vac_chat_messages') ?></h2>
                </div>
                <div id="chat-messages" class="chat-surface max-h-[70vh] overflow-y-auto pr-2"></div>
                <div class="mt-4 flex gap-3 chat-input-row">
                    <input id="chat-input" class="flex-1 border border-slate-200 rounded-xl px-4 py-3"
                        placeholder="<?= t('vac_chat_placeholder') ?>" maxlength="1000">
                    <button class="btn-primary sm:w-auto" onclick="sendChatMessage()"><?= t('vac_chat_send') ?></button>
                </div>
                <p class="text-xs text-slate-400 mt-2"><?= t('vac_chat_max_chars') ?></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="card p-4 md:p-6 lg:col-span-2">
                    <h2 class="text-lg font-semibold mb-4"><?= t('vac_chat_docs') ?></h2>
                    <div class="flex flex-col sm:flex-row gap-3 items-center doc-upload-row">
                        <input id="doc-input" type="file" class="file-input-hidden">
                        <label for="doc-input"
                            class="btn-ghost w-full sm:w-auto text-center cursor-pointer"><?= t('vac_chat_choose_file') ?></label>
                        <div id="doc-selected-name" class="file-pill w-full"><?= t('vac_chat_no_file_selected') ?></div>
                        <button class="btn-primary w-full sm:w-auto"
                            onclick="uploadDocument()"><?= t('vac_chat_upload') ?></button>
                    </div>
                    <p class="text-xs text-slate-400 mt-2"><?= t('vac_chat_docs_hint') ?></p>
                    <div id="doc-list" class="mt-4 space-y-2 text-sm"></div>
                </div>

                <aside class="card p-4 md:p-6 h-fit space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold"><?= t('vac_chat_participants') ?></h3>
                        <p class="text-sm text-slate-600 mt-1"><?= t('vac_chat_candidate_label') ?>:
                            <?= htmlspecialchars($chatApp['applicant_name'] ?? '') ?></p>
                    </div>

                    <?php if ($isEmployer): ?>
                        <div class="space-y-2">
                            <h4 class="text-sm font-semibold text-slate-700"><?= t('vac_chat_decision') ?></h4>
                            <button class="btn-primary w-full"
                                onclick="setEmploymentStatus('successful')"><?= t('vac_chat_accept') ?></button>
                            <button class="btn-ghost w-full"
                                onclick="setEmploymentStatus('unsuccessful')"><?= t('vac_chat_reject') ?></button>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </div>

    <script>
        const tfI18n = <?= tfSafeJson([
            'no_messages' => t('vac_chat_no_messages'),
            'fallback_initial' => t('vac_chat_fallback_initial')
        ], JSON_UNESCAPED_UNICODE) ?>;
        const appId = <?= (int) ($chatApp['id'] ?? 0) ?>;
        const currentUserId = <?= (int) ($user['id'] ?? 0) ?>;
        const docInputEl = document.getElementById('doc-input');
        const docSelectedNameEl = document.getElementById('doc-selected-name');

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderMessages(messages) {
            const wrap = document.getElementById('chat-messages');
            wrap.innerHTML = '';
            if (!messages.length) {
                wrap.innerHTML = `<div class="text-sm text-slate-500">${tfI18n.no_messages}</div>`;
                return;
            }
            messages.forEach(m => {
                const isMine = parseInt(m.sender_id, 10) === currentUserId;
                const initials = (m.sender_name || '?').trim().split(/\s+/).slice(0, 2).map(p => p[0]).join('').toUpperCase();
                const senderName = escapeHtml(m.sender_name || '');
                const createdAt = escapeHtml(m.created_at || '');
                const messageHtml = escapeHtml(m.message_text || '').replace(/\n/g, '<br>');
                const item = document.createElement('div');
                item.className = `flex ${isMine ? 'justify-end' : 'justify-start'} gap-2`;
                item.innerHTML = `
                ${isMine ? '' : `<div class="chat-avatar">${escapeHtml(initials || tfI18n.fallback_initial)}</div>`}
                <div class="chat-bubble ${isMine ? 'mine' : 'their'}">
                    ${isMine ? '' : `<div class="text-xs opacity-70 mb-1">${senderName}</div>`}
                    <div class="text-sm chat-msg-text">${messageHtml}</div>
                    <div class="chat-meta">${createdAt}</div>
                </div>
            `;
                wrap.appendChild(item);
            });
            wrap.scrollTop = wrap.scrollHeight;
        }

        function loadMessages() {
            fetch(`?action=vacancy-chat-get&app_id=${appId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    renderMessages(data.messages || []);
                });
        }

        function sendChatMessage() {
            const input = document.getElementById('chat-input');
            const message = (input.value || '').trim();
            if (!message) return;
            fetch('?action=vacancy-chat-send', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ app_id: appId, message })
            })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    input.value = '';
                    loadMessages();
                });
        }

        function loadDocuments() {
            fetch(`?action=vacancy-doc-get&app_id=${appId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    const list = document.getElementById('doc-list');
                    list.innerHTML = '';
                    (data.documents || []).forEach(doc => {
                        const item = document.createElement('div');
                        item.innerHTML = `<a class="text-blue-600 hover:underline break-all" href="${doc.url}" target="_blank">${doc.original_name}</a>`;
                        list.appendChild(item);
                    });
                });
        }

        function uploadDocument() {
            const input = docInputEl;
            if (!input.files || !input.files[0]) return;
            const form = new FormData();
            form.append('app_id', appId);
            form.append('document', input.files[0]);
            fetch('?action=vacancy-doc-upload', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: form
            })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    input.value = '';
                    if (docSelectedNameEl) docSelectedNameEl.textContent = <?= tfSafeJson(t('vac_chat_no_file_selected'), JSON_UNESCAPED_UNICODE) ?>;
                    loadDocuments();
                });
        }

        function setEmploymentStatus(status) {
            const isSuccess = status === 'successful';
            const confirmText = isSuccess
                ? 'Подтвердить принятие кандидата? Вакансия будет закрыта.'
                : 'Подтвердить отклонение кандидата?';
            const confirmed = window.confirm(confirmText);
            if (!confirmed) {
                fetch('?action=vacancy-employment-status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ app_id: appId, status: 'pending' })
                }).finally(() => {
                    location.reload();
                });
                return;
            }
            fetch('?action=vacancy-employment-status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ app_id: appId, status })
            })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    location.reload();
                });
        }

        loadMessages();
        loadDocuments();
        setInterval(loadMessages, 5000);

        if (docInputEl) {
            docInputEl.addEventListener('change', () => {
                const file = docInputEl.files && docInputEl.files[0] ? docInputEl.files[0] : null;
                if (docSelectedNameEl) {
                    docSelectedNameEl.textContent = file ? file.name : <?= tfSafeJson(t('vac_chat_no_file_selected'), JSON_UNESCAPED_UNICODE) ?>;
                }
            });
        }

        document.getElementById('chat-input').addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendChatMessage();
            }
        });
    </script>
</body>

</html>

