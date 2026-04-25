<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$interviews = $interviews ?? [];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(function_exists('currentLang') ? currentLang() : 'ru') ?>">
<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('interview_page_title', 'Interview') ?> - CodeMaster</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: radial-gradient(circle at top left, #f4f7ff 0%, #ffffff 48%, #f8fafc 100%);
            color: #0f172a;
        }
        .hero-bg {
            background: linear-gradient(120deg, rgba(255,255,255,0.9) 0%, rgba(239,246,255,0.9) 70%);
            border: 1px solid #e5e7eb;
        }
        .interview-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e5e7eb;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        }
        .interview-card .status {
            color: #2563eb;
        }
        @media (max-width: 900px) {
            .hero-bg { padding: 1.2rem; }
            .hero-actions {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }
            #createInterviewBtn { width: 100%; justify-content: center; }
        }
        @media (max-width: 640px) {
            .hero-bg { padding: 1rem; }
            .hero-bg h1 { font-size: 1.6rem; }
            .hero-actions > * { width: 100%; }
        }
    </style>
</head>
<body class="bg-gray-50 m-0">
<?php include 'includes/header.php'; ?>
<main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="hero-bg rounded-3xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-user-tie text-indigo-600"></i> <?= t('interview_heading', 'Interview') ?>
                </h1>
                <p class="text-sm text-gray-500 mt-1"><?= t('interview_subtitle', 'Turn practice into progress.') ?></p>
            </div>
            <div class="flex items-center gap-4 hero-actions">
                <div class="text-xs text-gray-500">
                    <div class="flex items-center gap-1">
                        <?= t('interview_month_usage', 'Month\'s usage') ?>
                        <i class="fas fa-info-circle text-gray-300"></i>
                    </div>
                    <div class="mt-1 flex items-center gap-2">
                        <div class="w-40 h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500" style="width: <?= min(100, (count($interviews) / 10) * 100) ?>%"></div>
                        </div>
                        <span><?= count($interviews) ?>/10 <?= t('interview_used', 'used') ?></span>
                    </div>
                </div>
                <button id="createInterviewBtn" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed">
                    <i class="fas fa-plus mr-2"></i> <?= t('interview_new', 'New Interview') ?>
                </button>
            </div>
        </div>
    </div>

    <div class="mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h2 class="text-base font-semibold text-gray-700"><?= t('interview_past', 'Past Interviews') ?></h2>
        <div class="flex items-center gap-2">
            <select id="interviewSort" class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 text-gray-600">
                <option value="recent"><?= t('interview_sort_result', 'Result') ?></option>
                <option value="new"><?= t('interview_sort_new', 'Newest') ?></option>
                <option value="old"><?= t('interview_sort_old', 'Oldest') ?></option>
            </select>
            <button id="viewGrid" class="w-9 h-9 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50">
                <i class="fas fa-th-large"></i>
            </button>
            <button id="viewList" class="w-9 h-9 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>

    <div id="interviewGrid" class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($interviews as $interview): ?>
            <?php
            $sessionCode = trim((string) ($interview['code'] ?? ''));
            $isCompleted = (($interview['status'] ?? '') === 'completed');
            $canOpenRoom = !$isCompleted && $sessionCode !== '';
            ?>
            <div class="interview-card rounded-2xl p-5 <?= $canOpenRoom ? 'cursor-pointer interview-card-link' : '' ?>"
                data-code="<?= htmlspecialchars($sessionCode) ?>" data-openable="<?= $canOpenRoom ? '1' : '0' ?>">
                <div class="flex items-center justify-between">
                    <div class="font-semibold text-gray-900"><?= htmlspecialchars($interview['title'] ?? t('interview_default_title', 'Interview')) ?></div>
                    <span class="text-xs status font-semibold">
                        <?= htmlspecialchars($isCompleted ? t('interview_status_completed', 'Completed') : ($canOpenRoom ? t('interview_status_created', 'Created') : t('interview_status_closed', 'Closed'))) ?>
                    </span>
                </div>
                <div class="text-xs text-gray-500 mt-2">
                    <?= htmlspecialchars((string) ($interview['created_at'] ?? '')) ?>
                </div>
                <div class="mt-3 text-sm text-gray-600">
                    <i class="fas fa-clipboard-question mr-1"></i>
                    <?= (int) ($interview['question_count'] ?? 1) ?> <?= t('interview_question', 'question') ?>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <?php if ($canOpenRoom): ?>
                    <button class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-50 open-room"
                        data-code="<?= htmlspecialchars($sessionCode) ?>">
                        <i class="fas fa-arrow-right-to-bracket text-indigo-500 mr-2"></i> <?= t('interview_open_room', 'Open room') ?>
                    </button>
                    <?php endif; ?>
                    <button class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-50 close-interview"
                        data-interview-id="<?= (int) ($interview['id'] ?? 0) ?>">
                        <i class="fas fa-trash-can text-rose-500 mr-2"></i> <?= t('interview_delete', 'Delete') ?>
                    </button>
                    <button class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-50 copy-invite" data-code="<?= htmlspecialchars($sessionCode) ?>" <?= $canOpenRoom ? '' : 'disabled' ?>>
                        <i class="fas fa-share-nodes text-blue-500 mr-2"></i> <?= t('interview_invite', 'Invite') ?>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($interviews)): ?>
            <div class="bg-white rounded-2xl border border-gray-200 p-6 text-center text-gray-500">
                <i class="fas fa-calendar-plus text-2xl text-indigo-400 mb-2"></i>
                <div><?= t('interview_empty', 'Нет интервью. Создайте первую сессию.') ?></div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
const defaultInterviewTitle = <?= tfSafeJson(t('interview_default_title', 'Interview'), JSON_UNESCAPED_UNICODE) ?>;
const inviteLabel = <?= tfSafeJson(t('interview_invite', 'Invite'), JSON_UNESCAPED_UNICODE) ?>;
const serverErrorLabel = <?= tfSafeJson(t('common_server_error', 'Ошибка сервера'), JSON_UNESCAPED_UNICODE) ?>;
const networkErrorLabel = <?= tfSafeJson(t('common_network_error', 'Ошибка соединения'), JSON_UNESCAPED_UNICODE) ?>;
const csrfToken = <?= tfSafeJson((string) ($_SESSION['csrf_token'] ?? ''), JSON_UNESCAPED_UNICODE) ?>;
const challengeDetected = (text) => {
  const s = String(text || '').toLowerCase();
  return s.includes('slowaes.decrypt')
    || (s.includes('document.cookie') && s.includes('__test='))
    || s.includes('/aes.js')
    || s.includes('this site requires javascript to work');
};
const extractPlainServerMessage = (text) => {
  const plain = String(text || '')
    .replace(/<script[\s\S]*?<\/script>/gi, ' ')
    .replace(/<style[\s\S]*?<\/style>/gi, ' ')
    .replace(/<[^>]+>/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();
  const sqlMatch = plain.match(/SQLSTATE\[[^\]]+\]:\s*[^|<]+/i);
  if (sqlMatch) return sqlMatch[0];
  return plain ? plain.slice(0, 220) : '';
};
const withBypassParam = (url) => {
  const raw = String(url || '');
  if (!raw.includes('?')) return `${raw}?i=1`;
  if (/[?&]i=/.test(raw)) return raw;
  return `${raw}&i=1`;
};
const postJson = (url, payload) => {
  const headers = {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  };
  if (csrfToken) {
    headers['X-CSRF-Token'] = csrfToken;
  }
  return fetch(withBypassParam(url), {
    method: 'POST',
    headers,
    body: JSON.stringify(payload || {})
  });
};
const requestJson = async (url, payload = null) => {
  const response = payload === null
    ? await fetch(withBypassParam(url), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    : await postJson(url, payload);
  const raw = await response.text();
  const cleaned = String(raw || '').replace(/^\uFEFF/, '').trim();
  if (challengeDetected(cleaned)) {
    throw new Error(`${serverErrorLabel}. <?= t('common_try_again', 'Попробуйте еще раз.') ?>`);
  }
  let data = null;
  try {
    data = cleaned ? JSON.parse(cleaned) : null;
  } catch (e) {
    const start = cleaned.indexOf('{');
    const end = cleaned.lastIndexOf('}');
    if (start !== -1 && end !== -1 && end > start) {
      try {
        data = JSON.parse(cleaned.slice(start, end + 1));
      } catch (_) {
        throw new Error(extractPlainServerMessage(cleaned) || serverErrorLabel);
      }
    } else {
      throw new Error(extractPlainServerMessage(cleaned) || serverErrorLabel);
    }
  }
  if (!response.ok || !data || data.success === false) {
    throw new Error((data && data.message) ? data.message : serverErrorLabel);
  }
  return data;
};

const createBtn = document.getElementById('createInterviewBtn');
if (createBtn) {
  createBtn.addEventListener('click', async () => {
    const title = `${defaultInterviewTitle} ${new Date().toLocaleDateString()}`;
    createBtn.disabled = true;
    try {
      const data = await requestJson('?action=interview-create', { title, question_count: 1 });
      if (data && data.session && data.session.code) {
        const url = `?action=interview-room&code=${encodeURIComponent(data.session.code)}`;
        const win = window.open(url, '_blank', 'noopener');
        if (!win) window.location.href = url;
        return;
      }
      alert(data.message || <?= tfSafeJson(t('interview_create_error', 'Не удалось создать интервью'), JSON_UNESCAPED_UNICODE) ?>);
    } catch (e) {
      alert(e && e.message ? e.message : networkErrorLabel);
    } finally {
      createBtn.disabled = false;
    }
  });
}

document.querySelectorAll('.copy-invite').forEach(btn => {
  btn.addEventListener('click', async (event) => {
    if (event && typeof event.stopPropagation === 'function') event.stopPropagation();
    const code = btn.dataset.code || '';
    if (!code) return;
    const url = `${window.location.origin}${window.location.pathname}?action=interview-room&code=${encodeURIComponent(code)}`;
    try {
      await navigator.clipboard.writeText(url);
      btn.textContent = <?= tfSafeJson(t('interview_copied', 'Скопировано'), JSON_UNESCAPED_UNICODE) ?>;
      setTimeout(() => {
        btn.innerHTML = `<i class="fas fa-link mr-1"></i> ${inviteLabel}`;
      }, 1500);
    } catch (e) {
      alert(url);
    }
  });
});

document.querySelectorAll('.open-room').forEach(btn => {
  btn.addEventListener('click', (event) => {
    if (event && typeof event.stopPropagation === 'function') event.stopPropagation();
    const code = btn.dataset.code || '';
    if (!code) return;
    window.location.href = `?action=interview-room&code=${encodeURIComponent(code)}`;
  });
});

const grid = document.getElementById('interviewGrid');
const viewGrid = document.getElementById('viewGrid');
const viewList = document.getElementById('viewList');
const setView = (mode) => {
  if (!grid) return;
  if (mode === 'list') {
    grid.classList.remove('md:grid-cols-2', 'lg:grid-cols-3');
    grid.classList.add('grid-cols-1');
  } else {
    grid.classList.add('md:grid-cols-2', 'lg:grid-cols-3');
  }
};
if (viewGrid) viewGrid.addEventListener('click', () => setView('grid'));
if (viewList) viewList.addEventListener('click', () => setView('list'));

document.querySelectorAll('.close-interview').forEach(btn => {
  btn.addEventListener('click', async (event) => {
    if (event && typeof event.stopPropagation === 'function') event.stopPropagation();
    const interviewId = Number(btn.dataset.interviewId || 0);
    if (!interviewId) return;
    const ok = confirm(<?= tfSafeJson(t('interview_close_confirm', 'Удалить собеседование?'), JSON_UNESCAPED_UNICODE) ?>);
    if (!ok) return;
    btn.disabled = true;
    try {
      await requestJson('?action=interview-delete', { interview_id: interviewId });
      const card = btn.closest('.interview-card');
      if (card) card.remove();
    } catch (e) {
      alert(e && e.message ? e.message : networkErrorLabel);
    } finally {
      btn.disabled = false;
    }
  });
});

document.querySelectorAll('.interview-card-link').forEach(card => {
  card.addEventListener('click', () => {
    if (card.dataset.openable !== '1') return;
    const code = card.dataset.code || '';
    if (!code) return;
    window.location.href = `?action=interview-room&code=${encodeURIComponent(code)}`;
  });
});
</script>
</body>
</html>
