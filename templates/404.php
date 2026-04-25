<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$pageTitle = t('page_404_title', 'Страница не найдена');
$pageSubtitle = t('page_404_subtitle', 'Похоже, такого адреса нет. Проверьте ссылку или вернитесь на главную.');
$pageButton = t('page_404_button', 'На главную');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(function_exists('currentLang') ? currentLang() : 'ru') ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            --glow: rgba(99, 102, 241, 0.18);
        }

        body {
            font-family: 'IBM Plex Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(700px 380px at 10% -10%, rgba(99, 102, 241, 0.25), transparent 55%),
                radial-gradient(680px 360px at 90% -10%, rgba(56, 189, 248, 0.2), transparent 55%),
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
    </style>
</head>

<body class="tf-public-motion">
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <section class="glass-card rounded-3xl p-8 sm:p-12">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                <div class="flex items-start gap-4">
                    <div
                        class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                        <i class="fas fa-triangle-exclamation text-xl"></i>
                    </div>
                    <div>
                        <h1 class="title-font text-3xl sm:text-4xl font-bold"><?= htmlspecialchars($pageTitle) ?></h1>
                        <p class="mt-3 text-base text-slate-600 max-w-2xl"><?= htmlspecialchars($pageSubtitle) ?></p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="?action=home"
                                class="inline-flex items-center px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                                <?= htmlspecialchars($pageButton) ?>
                                <i class="fas fa-arrow-right ml-2 text-sm"></i>
                            </a>
                            <a href="javascript:history.back()"
                                class="inline-flex items-center px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-white transition">
                                <?= t('page_404_back', 'Назад') ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl bg-slate-900 text-white p-6 w-full lg:w-64">
                    <div class="text-sm uppercase tracking-widest text-slate-300">
                        <?= t('page_404_tip_title', 'Подсказка') ?>
                    </div>
                    <div class="mt-2 text-sm text-slate-200">
                        <?= t('page_404_tip_body', 'Проверьте адрес или перейдите в разделы платформы через меню.') ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>

</html>

