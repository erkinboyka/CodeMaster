<?php if (!defined('APP_INIT')) die('Direct access not permitted'); ?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(function_exists('currentLang') ? currentLang() : 'ru') ?>">
<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('interview_ai_title', 'AI Interview Lab') ?> - CodeMaster</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --ai-bg:
                radial-gradient(circle at top left, rgba(79, 70, 229, 0.18), transparent 30%),
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.14), transparent 24%),
                linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%);
            --ai-card: rgba(255,255,255,0.94);
            --ai-line: rgba(148, 163, 184, 0.22);
            --ai-text: #0f172a;
            --ai-muted: #64748b;
            --ai-brand: #4f46e5;
            --ai-brand-strong: #4338ca;
            --ai-accent: #0ea5e9;
        }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--ai-text);
            background: var(--ai-bg);
        }

        h1, h2, h3 {
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: -0.03em;
        }

        .ai-shell {
            max-width: 1320px;
            margin: 0 auto;
            padding: 2rem 1rem 2.5rem;
        }

        .ai-hero,
        .ai-card {
            background: var(--ai-card);
            border: 1px solid var(--ai-line);
            border-radius: 28px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.10);
            backdrop-filter: blur(16px);
        }

        .ai-hero {
            padding: 1.5rem;
            overflow: hidden;
            position: relative;
        }

        .ai-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 0% 0%, rgba(79, 70, 229, 0.12), transparent 26%),
                radial-gradient(circle at 100% 0%, rgba(14, 165, 233, 0.12), transparent 24%);
            pointer-events: none;
        }

        .ai-chip {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .46rem .82rem;
            border-radius: 999px;
            background: rgba(79, 70, 229, 0.08);
            color: #3730a3;
            font-size: .8rem;
            font-weight: 800;
        }

        .ai-layout {
            display: grid;
            grid-template-columns: minmax(0, .95fr) minmax(0, 1.05fr);
            gap: 1rem;
            margin-top: 1rem;
        }

        .ai-card {
            padding: 1.15rem;
        }

        .mode-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .8rem;
        }

        .mode-card {
            border: 1px solid var(--ai-line);
            border-radius: 20px;
            padding: 1rem;
            background: linear-gradient(180deg, #fff, #f8fbff);
            cursor: pointer;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .mode-card:hover,
        .mode-card.is-active {
            transform: translateY(-2px);
            border-color: rgba(79, 70, 229, 0.26);
            box-shadow: 0 16px 32px rgba(79, 70, 229, 0.12);
        }

        .mode-card.is-active {
            background: linear-gradient(180deg, rgba(79,70,229,.09), rgba(255,255,255,.96));
        }

        .field,
        .textarea {
            width: 100%;
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.28);
            background: rgba(255,255,255,0.88);
            padding: .9rem 1rem;
            font: inherit;
            color: var(--ai-text);
            outline: none;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
        }

        .field:focus,
        .textarea:focus {
            border-color: rgba(79, 70, 229, 0.28);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.10);
        }

        .textarea {
            min-height: 110px;
            resize: vertical;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .85rem;
        }

        .quick-prompt {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.26);
            background: #fff;
            padding: .55rem .85rem;
            font-size: .8rem;
            font-weight: 700;
            color: #334155;
            cursor: pointer;
        }

        .action-row {
            display: flex;
            align-items: center;
            gap: .8rem;
            flex-wrap: wrap;
        }

        .btn-primary,
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .55rem;
            min-height: 46px;
            border-radius: 16px;
            padding: .8rem 1rem;
            font-weight: 800;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--ai-brand), var(--ai-brand-strong));
            color: #fff;
            box-shadow: 0 16px 34px rgba(79, 70, 229, 0.22);
        }

        .btn-secondary {
            background: #fff;
            color: #334155;
            border: 1px solid rgba(148, 163, 184, 0.28);
        }

        .output-shell {
            min-height: 620px;
            display: flex;
            flex-direction: column;
        }

        .output-pane {
            margin-top: 1rem;
            flex: 1;
            border-radius: 22px;
            border: 1px solid rgba(148, 163, 184, 0.22);
            background: linear-gradient(180deg, rgba(15,23,42,.98), rgba(30,41,59,.98));
            color: #e2e8f0;
            padding: 1rem;
            overflow: auto;
            white-space: pre-wrap;
            line-height: 1.6;
        }

        .output-placeholder {
            color: #94a3b8;
        }

        .stack {
            display: grid;
            gap: 1rem;
        }

        .subcard {
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 22px;
            padding: 1rem;
            background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,251,255,.96));
        }

        .history-list,
        .chat-thread {
            display: grid;
            gap: .75rem;
            margin-top: .85rem;
        }

        .history-item,
        .chat-msg {
            border: 1px solid rgba(148, 163, 184, 0.20);
            border-radius: 16px;
            padding: .85rem .95rem;
            background: #fff;
        }

        .history-item {
            cursor: pointer;
        }

        .history-item:hover,
        .history-item.is-active {
            border-color: rgba(79, 70, 229, 0.26);
            box-shadow: 0 12px 28px rgba(79, 70, 229, 0.10);
        }

        .chat-msg.ai {
            background: #eff6ff;
        }

        .chat-meta {
            font-size: .75rem;
            font-weight: 700;
            color: #64748b;
            margin-bottom: .35rem;
        }

        .chat-text {
            white-space: pre-wrap;
            line-height: 1.55;
            color: #0f172a;
        }

        .mini-output {
            margin-top: .85rem;
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.22);
            background: #fff;
            padding: 1rem;
            white-space: pre-wrap;
            color: #0f172a;
            min-height: 120px;
        }

        .ai-badges {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-top: 1rem;
        }

        .ai-badge {
            border-radius: 999px;
            padding: .42rem .75rem;
            background: rgba(14, 165, 233, 0.08);
            color: #075985;
            font-size: .78rem;
            font-weight: 700;
        }

        @media (max-width: 980px) {
            .ai-layout {
                grid-template-columns: 1fr;
            }
            .output-shell {
                min-height: 0;
            }
        }

        @media (max-width: 700px) {
            .mode-grid,
            .form-grid {
                grid-template-columns: 1fr;
            }
            .ai-shell {
                padding: 1.25rem .85rem 2rem;
            }
            .ai-hero,
            .ai-card {
                border-radius: 22px;
            }
            .action-row > * {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<main class="ai-shell">
    <section class="ai-hero">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
            <div>
                <span class="ai-chip"><i class="fas fa-robot"></i><?= t('interview_ai_label', 'AI Interview Lab') ?></span>
                <h1 class="text-4xl font-bold mt-3"><?= t('interview_ai_heading', 'Супер‑подготовка к интервью с AI') ?></h1>
                <p class="text-sm sm:text-base text-slate-600 mt-3 max-w-3xl">
                    <?= t('interview_ai_subtitle', 'Собери персональный mock interview, разбор ответов, behavioural pack и 14-дневный план подготовки в одном разделе.') ?>
                </p>
                <div class="ai-badges">
                    <span class="ai-badge"><i class="fas fa-bolt mr-1"></i><?= t('interview_ai_badge_mock', 'Mock interview') ?></span>
                    <span class="ai-badge"><i class="fas fa-clipboard-check mr-1"></i><?= t('interview_ai_badge_review', 'Answer review') ?></span>
                    <span class="ai-badge"><i class="fas fa-comments mr-1"></i><?= t('interview_ai_badge_hr', 'Behavioral prep') ?></span>
                    <span class="ai-badge"><i class="fas fa-calendar-check mr-1"></i><?= t('interview_ai_badge_plan', '14-day sprint') ?></span>
                </div>
            </div>
        </div>
    </section>

    <div class="ai-layout">
        <section class="ai-card">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-2xl font-bold"><?= t('interview_ai_modes', 'Режимы') ?></h2>
                    <p class="text-sm text-slate-500"><?= t('interview_ai_modes_hint', 'Выбери сценарий и заполни контекст, чтобы AI дал не общий, а реально полезный разбор.') ?></p>
                </div>
            </div>

            <div class="mode-grid" id="aiModeGrid">
                <button type="button" class="mode-card is-active" data-mode="mock">
                    <div class="text-lg font-bold text-slate-900"><i class="fas fa-microphone-lines text-indigo-600 mr-2"></i><?= t('interview_ai_mode_mock', 'Mock interview') ?></div>
                    <p class="text-sm text-slate-600 mt-2"><?= t('interview_ai_mode_mock_desc', 'Вопросы, критерии сильного ответа, red flags и финальный verdict.') ?></p>
                </button>
                <button type="button" class="mode-card" data-mode="review">
                    <div class="text-lg font-bold text-slate-900"><i class="fas fa-pen-ruler text-indigo-600 mr-2"></i><?= t('interview_ai_mode_review', 'Answer review') ?></div>
                    <p class="text-sm text-slate-600 mt-2"><?= t('interview_ai_mode_review_desc', 'Разбор твоего ответа, оценка, слабые места и follow-up вопросы.') ?></p>
                </button>
                <button type="button" class="mode-card" data-mode="behavioral">
                    <div class="text-lg font-bold text-slate-900"><i class="fas fa-user-group text-indigo-600 mr-2"></i><?= t('interview_ai_mode_behavioral', 'Behavioral pack') ?></div>
                    <p class="text-sm text-slate-600 mt-2"><?= t('interview_ai_mode_behavioral_desc', 'STAR-подготовка, истории, ошибки и HR/leadership блок.') ?></p>
                </button>
                <button type="button" class="mode-card" data-mode="plan">
                    <div class="text-lg font-bold text-slate-900"><i class="fas fa-calendar-day text-indigo-600 mr-2"></i><?= t('interview_ai_mode_plan', '14-day sprint') ?></div>
                    <p class="text-sm text-slate-600 mt-2"><?= t('interview_ai_mode_plan_desc', 'Интенсивный персональный план до собеседования.') ?></p>
                </button>
            </div>

            <div class="form-grid mt-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2"><?= t('interview_ai_role', 'Целевая роль') ?></label>
                    <input id="aiRole" class="field" placeholder="Frontend Developer / Backend / QA / PM">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2"><?= t('interview_ai_level', 'Уровень') ?></label>
                    <select id="aiLevel" class="field">
                        <option value="Junior">Junior</option>
                        <option value="Junior+">Junior+</option>
                        <option value="Middle" selected>Middle</option>
                        <option value="Middle+">Middle+</option>
                        <option value="Senior">Senior</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2"><?= t('interview_ai_company', 'Компания / тип компании') ?></label>
                    <input id="aiCompany" class="field" placeholder="EPAM / local fintech / product startup">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2"><?= t('interview_ai_stack', 'Стек') ?></label>
                    <input id="aiStack" class="field" placeholder="React, TypeScript, Node.js, SQL">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-semibold text-slate-700 mb-2"><?= t('interview_ai_focus', 'Слабые места / фокус') ?></label>
                <textarea id="aiFocus" class="textarea" placeholder="System design, async JS, telling impact stories, confidence in English"></textarea>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-semibold text-slate-700 mb-2"><?= t('interview_ai_experience', 'Опыт и текущая ситуация') ?></label>
                <textarea id="aiExperience" class="textarea" placeholder="2 года коммерческой разработки, делал CRM, плохо отвечаю на вопросы про архитектуру"></textarea>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-semibold text-slate-700 mb-2"><?= t('interview_ai_resume', 'Краткое резюме / достижения') ?></label>
                <textarea id="aiResume" class="textarea" placeholder="Ключевые проекты, достижения, цифры, лидерство, продуктовый эффект"></textarea>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-semibold text-slate-700 mb-2"><?= t('interview_ai_answer', 'Черновик ответа или вопрос интервьюера') ?></label>
                <textarea id="aiAnswer" class="textarea" placeholder="Вставь свой ответ сюда, если хочешь разбор. Или вопрос, который тебя пугает."></textarea>
            </div>

            <div class="mt-5">
                <div class="text-sm font-semibold text-slate-700 mb-2"><?= t('interview_ai_quick_prompts', 'Быстрые сценарии') ?></div>
                <div class="flex flex-wrap gap-2" id="quickPrompts">
                    <button type="button" class="quick-prompt" data-fill="React middle, frontend, product company, слабое место: architecture and performance">React middle</button>
                    <button type="button" class="quick-prompt" data-fill="Backend PHP Laravel, middle+, fintech, слабое место: system design and databases">Laravel fintech</button>
                    <button type="button" class="quick-prompt" data-fill="QA automation, junior+, outsourcing, слабое место: confidence and test strategy">QA automation</button>
                    <button type="button" class="quick-prompt" data-fill="Product manager, middle, startup, слабое место: metrics, prioritization, stakeholder stories">PM startup</button>
                </div>
            </div>

            <div class="action-row mt-6">
                <button type="button" class="btn-primary" id="generateAiInterview"><i class="fas fa-wand-magic-sparkles"></i><?= t('interview_ai_generate', 'Сгенерировать') ?></button>
                <button type="button" class="btn-secondary" id="clearAiInterview"><i class="fas fa-rotate-left"></i><?= t('interview_ai_clear', 'Очистить') ?></button>
            </div>
        </section>

        <section class="ai-card output-shell">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <h2 class="text-2xl font-bold"><?= t('interview_ai_output', 'AI output') ?></h2>
                    <p class="text-sm text-slate-500"><?= t('interview_ai_output_hint', 'Здесь будет персональный разбор, без воды и шаблонной болтовни.') ?></p>
                </div>
                <button type="button" class="btn-secondary" id="copyAiInterview"><i class="fas fa-copy"></i><?= t('common_copy', 'Copy') ?></button>
            </div>
            <div class="output-pane" id="aiOutputPane">
                <div class="output-placeholder">
<?= htmlspecialchars(t('interview_ai_placeholder', "Выбери режим и опиши цель.\n\nНапример:\n- роль: Frontend Middle\n- компания: product fintech\n- стек: React, TypeScript, Next.js\n- слабые места: system design, performance\n- что нужно: mock interview + red flags")) ?>
                </div>
            </div>
        </section>
    </div>
</main>

<script>
(() => {
    const modeGrid = document.getElementById('aiModeGrid');
    const outputPane = document.getElementById('aiOutputPane');
    const roleEl = document.getElementById('aiRole');
    const levelEl = document.getElementById('aiLevel');
    const companyEl = document.getElementById('aiCompany');
    const stackEl = document.getElementById('aiStack');
    const focusEl = document.getElementById('aiFocus');
    const experienceEl = document.getElementById('aiExperience');
    const resumeEl = document.getElementById('aiResume');
    const answerEl = document.getElementById('aiAnswer');
    const generateBtn = document.getElementById('generateAiInterview');
    const copyBtn = document.getElementById('copyAiInterview');
    const clearBtn = document.getElementById('clearAiInterview');
    let activeMode = 'mock';
    let latestOutput = '';

    function setMode(mode) {
        activeMode = mode;
        document.querySelectorAll('.mode-card').forEach((card) => {
            card.classList.toggle('is-active', card.dataset.mode === mode);
        });
    }

    modeGrid?.querySelectorAll('.mode-card').forEach((card) => {
        card.addEventListener('click', () => setMode(card.dataset.mode || 'mock'));
    });

    document.getElementById('quickPrompts')?.querySelectorAll('[data-fill]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const text = String(btn.dataset.fill || '');
            focusEl.value = text;
        });
    });

    function renderOutput(text) {
        latestOutput = String(text || '').trim();
        outputPane.textContent = latestOutput || '';
    }

    generateBtn?.addEventListener('click', async () => {
        const role = String(roleEl?.value || '').trim();
        if (!role) {
            if (window.tfNotify) window.tfNotify(<?= tfSafeJson(t('interview_ai_need_role', 'Укажите целевую роль'), JSON_UNESCAPED_UNICODE) ?>);
            roleEl?.focus();
            return;
        }
        generateBtn.disabled = true;
        renderOutput(<?= tfSafeJson(t('interview_ai_generating', 'AI готовит персональный разбор...'), JSON_UNESCAPED_UNICODE) ?>);
        try {
            const res = await fetch('?action=interview-ai-coach', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    mode: activeMode,
                    role,
                    level: levelEl?.value || '',
                    company: companyEl?.value || '',
                    stack: stackEl?.value || '',
                    focus: focusEl?.value || '',
                    experience: experienceEl?.value || '',
                    resume_summary: resumeEl?.value || '',
                    answer_draft: answerEl?.value || ''
                })
            });
            const data = await res.json();
            if (!data || !data.success) {
                throw new Error((data && data.message) ? data.message : <?= tfSafeJson(t('interview_ai_unavailable', 'AI временно недоступен'), JSON_UNESCAPED_UNICODE) ?>);
            }
            renderOutput(String(data.content || ''));
            if (window.tfNotify && data.message) window.tfNotify(String(data.message));
        } catch (e) {
            renderOutput('');
            if (window.tfNotify) {
                window.tfNotify(e && e.message ? e.message : <?= tfSafeJson(t('interview_ai_unavailable', 'AI временно недоступен'), JSON_UNESCAPED_UNICODE) ?>);
            }
        } finally {
            generateBtn.disabled = false;
        }
    });

    copyBtn?.addEventListener('click', async () => {
        if (!latestOutput) return;
        try {
            await navigator.clipboard.writeText(latestOutput);
            if (window.tfNotify) window.tfNotify(<?= tfSafeJson(t('common_copy_done', 'Скопировано'), JSON_UNESCAPED_UNICODE) ?>);
        } catch (e) {
            if (window.tfNotify) window.tfNotify(<?= tfSafeJson(t('common_copy_error', 'Не удалось скопировать'), JSON_UNESCAPED_UNICODE) ?>);
        }
    });

    clearBtn?.addEventListener('click', () => {
        [roleEl, companyEl, stackEl, focusEl, experienceEl, resumeEl, answerEl].forEach((el) => {
            if (el) el.value = '';
        });
        if (levelEl) levelEl.value = 'Middle';
        setMode('mock');
        latestOutput = '';
        outputPane.textContent = <?= tfSafeJson(t('interview_ai_placeholder', "Выбери режим и опиши цель.\n\nНапример:\n- роль: Frontend Middle\n- компания: product fintech\n- стек: React, TypeScript, Next.js\n- слабые места: system design, performance\n- что нужно: mock interview + red flags"), JSON_UNESCAPED_UNICODE) ?>;
    });
})();
</script>
</body>
</html>
