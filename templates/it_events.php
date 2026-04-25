<?php if (!defined('APP_INIT')) {
    die('Direct access not permitted');
} ?>
<?php
require_once __DIR__ . '/../includes/telegram_feed.php';

$lang = function_exists('currentLang') ? currentLang() : 'ru';
$labels = [
    'ru' => [
        'title' => 'IT Events TJ - CodeMaster',
        'heading' => 'IT Events TJ',
        'subheading' => 'Актуальные публикации из Telegram-канала t.me/iteventstj.',
        'open_channel' => 'Открыть канал',
        'last_update' => 'Лента обновляется автоматически.',
        'empty' => 'Пока не удалось загрузить публикации. Попробуйте обновить страницу.',
        'stale' => 'Показаны последние кэшированные публикации.',
        'open_post' => 'Открыть публикацию',
        'home' => 'Главная',
        'courses' => 'Курсы',
        'vacancies' => 'Вакансии',
        'events' => 'События',
        'email' => 'Email',
        'social' => 'Мы в соцсетях'
    ],
    'en' => [
        'title' => 'IT Events TJ - CodeMaster',
        'heading' => 'IT Events TJ',
        'subheading' => 'Latest posts from Telegram channel t.me/iteventstj.',
        'open_channel' => 'Open channel',
        'last_update' => 'Feed updates automatically.',
        'empty' => 'Could not load posts yet. Please refresh the page.',
        'stale' => 'Showing latest cached posts.',
        'open_post' => 'Open post',
        'home' => 'Home',
        'courses' => 'Courses',
        'vacancies' => 'Vacancies',
        'events' => 'Events',
        'email' => 'Email',
        'social' => 'Follow us'
    ],
    'tg' => [
        'title' => 'IT Events TJ - CodeMaster',
        'heading' => 'IT Events TJ',
        'subheading' => 'Нашрияҳои нав аз канали Telegram: t.me/iteventstj.',
        'open_channel' => 'Каналро кушодан',
        'last_update' => 'Лента худкор нав мешавад.',
        'empty' => 'Ҳоло боркунии нашрияҳо нашуд. Саҳифаро аз нав кушоед.',
        'stale' => 'Нашрияҳои кэшшуда нишон дода мешаванд.',
        'open_post' => 'Кушодани нашрия',
        'home' => 'Асосӣ',
        'courses' => 'Курсҳо',
        'vacancies' => 'Вакансияҳо',
        'events' => 'Рӯйдодҳо',
        'email' => 'Почта',
        'social' => 'Мо дар шабакаҳо'
    ]
];
$ui = $labels[$lang] ?? $labels['ru'];

$feed = tfTelegramFeedGetPosts('iteventstj', 12, 600);
$posts = is_array($feed['posts'] ?? null) ? $feed['posts'] : [];
$isStale = !empty($feed['stale']);

$formatDate = static function ($isoDate, $fallback) use ($lang) {
    $fallback = trim((string) $fallback);
    $isoDate = trim((string) $isoDate);
    if ($isoDate === '') {
        return $fallback;
    }
    try {
        $dt = new DateTime($isoDate);
        $locale = 'ru_RU';
        if ($lang === 'en') {
            $locale = 'en_US';
        } elseif ($lang === 'tg') {
            $locale = 'tg_TJ';
        }
        if (class_exists('IntlDateFormatter')) {
            $formatter = new IntlDateFormatter($locale, IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
            $formatter->setTimezone(new DateTimeZone(date_default_timezone_get() ?: 'UTC'));
            $formatted = $formatter->format($dt);
            if (is_string($formatted) && $formatted !== '') {
                return $formatted;
            }
        }
        return $dt->format('Y-m-d H:i');
    } catch (Throwable $e) {
        return $fallback;
    }
};
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" class="scroll-smooth">
<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($ui['title']) ?></title>
    
    <!-- Fonts & Icons - как в референсе -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        :root {
            --ink: #0f172a;
            --muted: #475569;
            --glow: rgba(99, 102, 241, 0.15);
            --indigo: var(--tf-brand, #6366f1);
            --indigo-strong: var(--tf-brand-strong, #4f46e5);
            --indigo-light: rgba(99, 102, 241, 0.1);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(99, 102, 241, 0.10), transparent 28%),
                radial-gradient(circle at top right, rgba(129, 140, 248, 0.08), transparent 24%),
                linear-gradient(180deg, #f8fafc 0%, #f9fafb 100%);
        }
        
        .title-font {
            font-family: 'Inter', sans-serif;
        }
        
        /* Glass cards - как в референсе */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(148, 163, 184, 0.24);
            box-shadow: var(--tf-shadow-card);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 28px;
        }
        .glass-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 35px 70px rgba(15, 23, 42, 0.16);
        }
        
        /* Hero section */
        .hero-glow {
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, var(--indigo-light) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(50px);
            z-index: 0;
            pointer-events: none;
            animation: pulse 8s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 0.9; transform: scale(1.05); }
        }
        
        /* Post cards */
        .post-card {
            transition: all 0.3s ease;
        }
        .post-card:hover {
            border-color: rgba(99, 102, 241, 0.32);
            box-shadow: var(--tf-shadow-card);
        }
        
        /* Images */
        .post-image-wrap {
            position: relative;
            overflow: hidden;
            aspect-ratio: 16/9;
            background: linear-gradient(135deg, #e2e8f0 0%, #f1f5f9 100%);
        }
        .post-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .post-card:hover .post-image {
            transform: scale(1.04);
        }
        .post-image-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 2rem;
            background: #f8fafc;
        }
        .image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.5), transparent 60%);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: flex-end;
            padding: 1rem;
        }
        .post-image-wrap:hover .image-overlay {
            opacity: 1;
        }
        
        /* Line clamp */
        .line-clamp-fade {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            position: relative;
        }
        .line-clamp-fade::after {
            content: '';
            position: absolute;
            bottom: 0; right: 0;
            width: 100%; height: 1.5rem;
            background: linear-gradient(transparent, rgba(255, 255, 255, 0.95));
            pointer-events: none;
        }
        
        /* Buttons */
        .btn-indigo {
            background: linear-gradient(135deg, var(--indigo), var(--indigo-strong));
            transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 16px 34px rgba(79, 70, 229, 0.22);
        }
        .btn-indigo:hover {
            transform: translateY(-1px);
        }
        .btn-outline {
            border: 1px solid rgba(148, 163, 184, 0.3);
            transition: all 0.2s ease;
            background: rgba(255,255,255,0.82);
        }
        .btn-outline:hover {
            border-color: rgba(99, 102, 241, 0.28);
            background: rgba(99, 102, 241, 0.05);
            color: #4f46e5;
        }
        @media (max-width: 768px) {
            .glass-card {
                border-radius: 20px;
            }
            .hero-glow {
                width: 280px;
                height: 280px;
                filter: blur(36px);
            }
            .post-card:hover,
            .glass-card:hover {
                transform: none;
            }
        }
        
        /* Scroll animations */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(12px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        .reveal-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Stagger delays */
        .stagger-1 { transition-delay: 0.05s; }
        .stagger-2 { transition-delay: 0.1s; }
        .stagger-3 { transition-delay: 0.15s; }
        .stagger-4 { transition-delay: 0.2s; }
        .stagger-5 { transition-delay: 0.25s; }
        .stagger-6 { transition-delay: 0.3s; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { 
            background: linear-gradient(180deg, #6366f1, #818cf8); 
            border-radius: 3px;
        }
    </style>
</head>

<body class="tf-public-motion min-h-screen flex flex-col">
<?php include 'includes/header.php'; ?>

<main class="relative z-10 flex-grow max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- Hero Section - в стиле референса -->
    <section class="glass-card rounded-3xl p-6 sm:p-10 mb-8 overflow-hidden">
        <div class="hero-glow -top-32 -right-32"></div>
        <div class="hero-glow -bottom-32 -left-32" style="background: radial-gradient(circle, rgba(56, 189, 248, 0.12) 0%, transparent 70%);"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm flex-shrink-0">
                    <i class="fab fa-telegram-plane text-xl"></i>
                </div>
                <div>
                    <h1 class="title-font text-3xl sm:text-4xl font-bold text-slate-800">
                        <?= htmlspecialchars($ui['heading']) ?>
                    </h1>
                    <?php if ($ui['subheading']): ?>
                        <p class="mt-2 text-base text-slate-600 max-w-2xl">
                            <?= htmlspecialchars($ui['subheading']) ?>
                        </p>
                    <?php endif; ?>
                    <p class="mt-3 text-sm text-slate-500 flex items-center gap-2">
                        <i class="fas fa-sync-alt animate-spin-slow text-indigo-400"></i>
                        <?= htmlspecialchars($ui['last_update']) ?>
                    </p>
                </div>
            </div>
            
            <a href="https://t.me/iteventstj" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl btn-indigo text-white font-semibold shadow-lg shadow-indigo-500/20">
                <i class="fab fa-telegram-plane"></i>
                <span><?= htmlspecialchars($ui['open_channel']) ?></span>
                <i class="fas fa-arrow-right-long text-sm opacity-80"></i>
            </a>
        </div>
    </section>

    <!-- Stale Notice -->
    <?php if ($isStale): ?>
        <div class="mb-6 p-4 rounded-2xl border border-amber-200 bg-amber-50 
                    flex items-start gap-3 text-amber-800 reveal-on-scroll">
            <i class="fas fa-info-circle mt-0.5 flex-shrink-0 text-amber-500"></i>
            <span class="text-sm font-medium"><?= htmlspecialchars($ui['stale']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Posts Grid -->
    <section>
        <?php if (empty($posts)): ?>
            <div class="glass-card rounded-2xl p-8 text-center reveal-on-scroll">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-rss text-slate-400 text-xl"></i>
                </div>
                <p class="text-slate-600 font-medium"><?= htmlspecialchars($ui['empty']) ?></p>
                <button onclick="window.location.reload()" 
                        class="mt-4 px-4 py-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 
                               transition-colors inline-flex items-center gap-2 hover:bg-indigo-50 rounded-xl">
                    <i class="fas fa-rotate-right"></i>
                    <?= $lang === 'tg' ? 'Аз нав кардан' : ($lang === 'en' ? 'Refresh' : 'Обновить') ?>
                </button>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                <?php foreach ($posts as $index => $post): ?>
                    <?php
                    $postUrl = (string) ($post['url'] ?? 'https://t.me/iteventstj');
                    $excerpt = (string) ($post['excerpt'] ?? '');
                    $image = trim((string) ($post['image'] ?? ''));
                    $dateLabel = $formatDate((string) ($post['datetime'] ?? ''), (string) ($post['time_label'] ?? ''));
                    $staggerClass = "stagger-" . min(($index % 6) + 1, 6);
                    ?>
                    <article class="post-card glass-card rounded-2xl overflow-hidden flex flex-col h-full reveal-on-scroll <?= $staggerClass ?>">
                        
                        <!-- Image -->
                        <?php if ($image !== ''): ?>
                            <a href="<?= htmlspecialchars($postUrl) ?>" target="_blank" rel="noopener noreferrer" 
                               class="block post-image-wrap group">
                                <img src="<?= htmlspecialchars($image) ?>" alt="Telegram post" 
                                     class="post-image" loading="lazy"
                                     onerror="this.parentElement.innerHTML='<div class=\\'post-image-placeholder\\'><i class=\\'fas fa-image\\'></i></div>'">
                                <div class="image-overlay">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg 
                                                 bg-white/95 backdrop-blur-sm text-slate-800 text-xs font-semibold
                                                 shadow-sm">
                                        <i class="fas fa-external-link-alt"></i>
                                        <?= htmlspecialchars($ui['open_post']) ?>
                                    </span>
                                </div>
                            </a>
                        <?php else: ?>
                            <div class="post-image-wrap">
                                <div class="post-image-placeholder">
                                    <i class="fas fa-newspaper text-slate-300"></i>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Content -->
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex items-center justify-between text-xs text-slate-500 mb-3 pb-3 border-b border-slate-200">
                                <a href="https://t.me/iteventstj" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1.5 font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                                    <i class="fab fa-telegram-plane text-[10px]"></i>
                                    iteventstj
                                </a>
                                <?php if ($dateLabel !== ''): ?>
                                    <time class="flex items-center gap-1 text-slate-400" datetime="<?= htmlspecialchars($post['datetime'] ?? '') ?>">
                                        <i class="far fa-clock"></i>
                                        <?= htmlspecialchars($dateLabel) ?>
                                    </time>
                                <?php endif; ?>
                            </div>
                            
                            <p class="line-clamp-fade text-sm text-slate-700 leading-relaxed flex-grow mb-4">
                                <?= htmlspecialchars($excerpt !== '' ? $excerpt : '...') ?>
                            </p>
                            
                            <a href="<?= htmlspecialchars($postUrl) ?>" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl 
                                      btn-outline text-sm font-semibold text-slate-700 mt-auto">
                                <?= htmlspecialchars($ui['open_post']) ?>
                                <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    
    <!-- Hint -->
    <div class="mt-10 text-center text-slate-500 text-sm reveal-on-scroll stagger-6">
        <p class="flex items-center justify-center gap-2">
            <i class="fas fa-sync-alt text-indigo-400"></i>
            <?= $lang === 'tg' ? 'Маълумот аз' : ($lang === 'en' ? 'Data from' : 'Данные из') ?> 
            <a href="https://t.me/iteventstj" class="font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">t.me/iteventstj</a>
        </p>
    </div>
</main>

<!-- Footer - в стиле референса -->
<?php include 'includes/footer.php'; ?>
    
<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll reveal
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });
    
    document.querySelectorAll('.reveal-on-scroll').forEach((el, i) => {
        observer.observe(el);
    });
    
    // Image fallback
    document.querySelectorAll('.post-image').forEach(img => {
        img.addEventListener('error', function() {
            const placeholder = document.createElement('div');
            placeholder.className = 'post-image-placeholder';
            placeholder.innerHTML = '<i class="fas fa-image text-slate-300"></i>';
            this.parentElement.replaceWith(placeholder);
        });
    });
});
</script>
</body>
</html>
