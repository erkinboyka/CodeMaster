<?php
$current_action = $_GET['action'] ?? 'home';
$current_action = $current_action ?: 'dashboard';
$lang = currentLang();
?>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.10/dist/cdn.min.js" defer></script>
<?php include 'includes/csrf.php'; ?>

<style>
    .tf-notifications-container {
        position: fixed;
        top: 16px;
        right: 16px;
        left: 16px;
        z-index: 1000;
        max-width: 500px;
        margin: 0 auto;
        pointer-events: none;
    }
    
    .tf-notification {
        pointer-events: auto;
        animation: slideDown 0.3s ease-out, fadeOut 0.5s 2.5s forwards;
        margin-bottom: 12px;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        transform-origin: top center;
    }
    
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-50px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0; transform: scale(0.9) translateY(-20px); }
    }
    
    @media (min-width: 768px) {
    .tf-header-mobile-toggle {
        display: none !important;
    }
}
    
    @media print {
        .tf-header-chat-fab,
        .tf-notifications-container {
            display: none !important;
        }
    }
</style>

<!-- Уведомления -->
<div class="tf-notifications-container" x-data="{ notifications: <?= json_encode($user['notifications'] ?? []) ?> }" 
     x-init="setTimeout(() => notifications = [], 3000)">
    <template x-for="(notif, index) in notifications.slice(0, 3)" :key="index">
        <div class="tf-notification bg-white border-l-4 border-indigo-500 rounded-lg p-4 shadow-md">
            <div class="flex items-start">
                <div class="flex-shrink-0 mt-0.5">
                    <i class="fas fa-bell text-indigo-500"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900" x-text="notif.message"></p>
                    <p class="mt-1 text-xs text-gray-500" x-text="notif.notification_time"></p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button @click="notifications.splice(index, 1)" 
                            class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only"><?= t('close') ?></span>
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<div id="tf-preloader" class="tf-preloader" aria-hidden="true"></div>

<?php include __DIR__ . '/ai_tutor_modal.php'; ?>

<header x-data="{ mobileMenuOpen: false }" data-fixed-header class="tf-header-root bg-white shadow-sm z-50" style="margin-top:0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <a href="?action=dashboard" class="flex items-center tf-header-brand">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center mr-3 ring-1 ring-indigo-300/60 shadow-[0_6px_18px_rgba(79,70,229,0.35)]">
                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900 tf-header-brand-title">CodeMaster</span>
                </a>
                <nav class="tf-header-nav hidden md:ml-10 md:flex md:space-x-8">
                    <!-- Dropdown: Education -->
                    <div class="relative" x-data="{ open: false, closeTimer: null }"
                        @mouseenter="if (closeTimer) clearTimeout(closeTimer); open = true"
                        @mouseleave="closeTimer = setTimeout(() => { open = false }, 180)">
                        <a href="?action=courses"
                            class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium <?= (in_array($current_action, ['courses', 'course', 'roadmap', 'roadmaps', 'visualizations'], true)) ? 'border-indigo-500 text-indigo-600 font-medium' : '' ?>">
                            <i class="fas fa-layer-group mr-2"></i> <?= t('nav_education', 'Обучение') ?>
                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </a>
                        <div x-show="open" x-transition.origin.top.left.duration.120ms
                            class="tf-header-dropdown absolute left-0 top-full pt-1 w-64 bg-white border border-gray-200 rounded-lg shadow-lg z-50"
                            @mouseenter="if (closeTimer) clearTimeout(closeTimer); open = true"
                            @mouseleave="closeTimer = setTimeout(() => { open = false }, 180)">
                            <a href="?action=courses"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 <?= ($current_action === 'courses') ? 'text-indigo-600 font-medium' : '' ?>">
                                <i class="fas fa-book mr-2"></i><?= t('nav_courses') ?>
                            </a>
                            <a href="?action=roadmaps"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 <?= (in_array($current_action, ['roadmap', 'roadmaps'], true)) ? 'text-indigo-600 font-medium' : '' ?>">
                                <i class="fas fa-project-diagram mr-2"></i><?= t('nav_roadmap') ?>
                            </a>
                            <a href="?action=visualizations"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 <?= ($current_action === 'visualizations') ? 'text-indigo-600 font-medium' : '' ?>">
                                <i class="fas fa-wave-square mr-2"></i><?= t('nav_visualizations', 'Визуализация') ?>
                            </a>
                        </div>
                    </div>

                    <a href="?action=contests"
                        class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium <?= (in_array($current_action, ['contest', 'contests'], true)) ? 'border-indigo-500 text-indigo-600 font-medium' : '' ?>">
                        <i class="fas fa-code mr-2"></i><?= t('nav_contests', 'Конкурсы') ?>
                    </a> 
                    

                    <a href="?action=ratings"
                        class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium <?= ($current_action === 'ratings') ? 'border-indigo-500 text-indigo-600 font-medium' : '' ?>">
                        <i class="fas fa-trophy mr-2"></i> <?= t('nav_ratings') ?>
                    </a>
                    
                    <!-- Dropdown: Vacancies -->
                    <div class="relative" x-data="{ open: false, closeTimer: null }"
                        @mouseenter="if (closeTimer) clearTimeout(closeTimer); open = true"
                        @mouseleave="closeTimer = setTimeout(() => { open = false }, 180)">
                        <a href="?action=vacancies"
                            class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium <?= (in_array($current_action, ['vacancies', 'courses-interview', 'interview', 'interview-room', 'interview-ai'], true)) ? 'border-indigo-500 text-indigo-600 font-medium' : '' ?>">
                            <i class="fas fa-briefcase mr-2"></i> <?= t('nav_vacancies') ?>
                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </a>
                        <div x-show="open" x-transition.origin.top.left.duration.120ms
                            class="tf-header-dropdown absolute left-0 top-full pt-1 w-64 bg-white border border-gray-200 rounded-lg shadow-lg z-50"
                            @mouseenter="if (closeTimer) clearTimeout(closeTimer); open = true"
                            @mouseleave="closeTimer = setTimeout(() => { open = false }, 180)">
                            <a href="?action=courses-interview"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 <?= ($current_action === 'courses-interview') ? 'text-indigo-600 font-medium' : '' ?>">
                                <i class="fas fa-user-tie mr-2"></i><?= t('courses_interview_link') ?>
                            </a>
                            <a href="?action=interview"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 <?= ($current_action === 'interview' || $current_action === 'interview-room') ? 'text-indigo-600 font-medium' : '' ?>">
                                <i class="fas fa-video mr-2"></i> <?= t('nav_interview', 'Interview') ?>
                            </a>
                            <a href="?action=interview-ai"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 <?= ($current_action === 'interview-ai') ? 'text-indigo-600 font-medium' : '' ?>">
                                <i class="fas fa-robot mr-2"></i> <?= t('interview_ai_nav', 'AI Interview') ?>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="flex items-center">
                <div class="hidden sm:flex items-center mr-4 text-sm">
                    <label for="header-lang-select" class="sr-only"><?= t('label_language') ?></label>
                    <select id="header-lang-select"
                        class="tf-header-select border border-gray-200 rounded-full px-3 py-2 text-sm font-medium text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        onchange="window.location.href=this.value">
                        <option value="<?= htmlspecialchars(langUrl('ru')) ?>" <?= $lang === 'ru' ? 'selected' : '' ?>>
                            <?= t('lang_ru') ?>
                        </option>
                        <option value="<?= htmlspecialchars(langUrl('en')) ?>" <?= $lang === 'en' ? 'selected' : '' ?>>
                            <?= t('lang_en') ?>
                        </option>
                        <option value="<?= htmlspecialchars(langUrl('tg')) ?>" <?= $lang === 'tg' ? 'selected' : '' ?>>
                            <?= t('lang_tg') ?>
                        </option>
                    </select>
                </div>
                <div class="hidden sm:flex items-center space-x-4 mr-4">
                    <div class="relative" x-data="{
                             showNotifications: false,
                             hasUnread: <?= !empty($user['unread_notifications']) ? 'true' : 'false' ?>,
                             isMarkingRead: false,
                             markRead() {
                                 if (!this.hasUnread || this.isMarkingRead) return;
                                 this.isMarkingRead = true;
                                 fetch('?action=mark-notifications-read', {
                                     method: 'POST',
                                     headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                 })
                                 .then(response => response.text().then((raw) => {
                                     const text = String(raw || '').replace(/^\uFEFF/, '').trim();
                                     if (!text) return {};
                                     try {
                                         return JSON.parse(text);
                                     } catch (e) {
                                         const start = text.indexOf('{');
                                         const end = text.lastIndexOf('}');
                                         if (start !== -1 && end !== -1 && end > start) {
                                             return JSON.parse(text.slice(start, end + 1));
                                         }
                                         return {};
                                     }
                                 }))
                                 .then(data => {
                                     if (data && data.success) {
                                         this.hasUnread = false;
                                     }
                                 })
                                 .catch(() => {})
                                 .finally(() => {
                                     this.isMarkingRead = false;
                                 });
                             },
                             toggleNotifications() {
                                 this.showNotifications = !this.showNotifications;
                                 if (this.showNotifications) {
                                     this.markRead();
                                 }
                             }
                         }">
                        <button @click="toggleNotifications()"
                            class="tf-header-icon-btn p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 notification-badge">
                            <span class="sr-only"><?= t('notif_sr') ?></span>
                            <i class="fas fa-bell text-lg"></i>
                            <span x-show="hasUnread"
                                class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400"
                                aria-hidden="true"></span>
                        </button>
                        <div x-show="showNotifications" @click.away="showNotifications = false"
                            class="tf-header-user-menu origin-top-right absolute right-0 mt-2 w-80 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                            <div class="py-1 max-h-96 overflow-y-auto">
                                <?php if (!empty($user['notifications'])): ?>
                                    <?php foreach (array_slice($user['notifications'], 0, 5) as $notif): ?>
                                        <?php $notifText = translateNotificationMessage($notif['message'] ?? ''); ?>
                                        <a href="#" class="block px-4 py-2 hover:bg-gray-50">
                                            <div class="flex items-center">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        <?= htmlspecialchars($notifText) ?>
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        <?= htmlspecialchars($notif['notification_time']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="px-4 py-4 text-center text-gray-500"><?= t('notif_none') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="border-t border-gray-200 px-4 py-2 text-center">
                                <a href="#" @click.prevent="markRead()"
                                    class="text-sm text-indigo-600 hover:text-indigo-900 font-medium"><?= t('notif_mark_read') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="tf-header-avatar-btn max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only"><?= t('menu_open') ?></span>
                        <img class="h-8 w-8 rounded-full" src="<?= htmlspecialchars($user['avatar']) ?>"
                            alt="User avatar">
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="tf-header-user-menu origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <a href="?action=profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-circle mr-2"></i><?= t('menu_profile') ?>
                        </a>
                        <a href="?action=logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i><?= t('menu_logout') ?>
                        </a>
                    </div>
                </div>
                <button type="button"
                    class="tf-header-mobile-toggle md:hidden ml-2 p-2 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700"
                    @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen.toString()"
                    aria-controls="mobile-main-menu">
                    <span class="sr-only"><?= t('menu_open') ?></span>
                    <i class="fas" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                </button>
            </div>
        </div>
    </div>
    <div id="mobile-main-menu" x-show="mobileMenuOpen" x-transition.origin.top.duration.150ms
        class="tf-header-mobile-menu md:hidden border-t border-gray-200" @keydown.escape.window="mobileMenuOpen = false"
        style="display: none;">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <div class="px-3 pt-2 text-xs uppercase tracking-wide text-gray-400"><?= t('nav_education', 'Обучение') ?></div>
            <a href="?action=courses" @click="mobileMenuOpen = false"
                class="tf-mobile-link block px-3 py-2 rounded-md text-base font-medium <?= ($current_action === 'courses') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' ?>">
                <i class="fas fa-book mr-2"></i> <?= t('nav_courses') ?>
            </a>
            <a href="?action=roadmaps" @click="mobileMenuOpen = false"
                class="tf-mobile-link block px-3 py-2 rounded-md text-base font-medium <?= (in_array($current_action, ['roadmap', 'roadmaps'], true)) ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' ?>">
                <i class="fas fa-project-diagram mr-2"></i> <?= t('nav_roadmap') ?>
            </a>
            <a href="?action=visualizations" @click="mobileMenuOpen = false"
                class="tf-mobile-link block px-3 py-2 rounded-md text-base font-medium <?= ($current_action === 'visualizations') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' ?>">
                <i class="fas fa-wave-square mr-2"></i> <?= t('nav_visualizations', 'Визуализация') ?>
            </a>
            
            <div class="px-3 pt-3 text-xs uppercase tracking-wide text-gray-400"><?= t('nav_contests', 'КРѕнкСѓрсы') ?></div>
            <a href="?action=contests" @click="mobileMenuOpen = false"
                class="tf-mobile-link block px-3 py-2 rounded-md text-base font-medium <?= (in_array($current_action, ['contest', 'contests'], true)) ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' ?>">
                <i class="fas fa-trophy mr-2"></i> <?= t('nav_contests', 'Конкурсы') ?>
            </a>

            <div class="px-3 pt-3 text-xs uppercase tracking-wide text-gray-400"><?= t('nav_career', 'Карьера') ?></div>
            <a href="?action=ratings" @click="mobileMenuOpen = false"
                class="tf-mobile-link block px-3 py-2 rounded-md text-base font-medium <?= ($current_action === 'ratings') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' ?>">
                <i class="fas fa-trophy mr-2"></i> <?= t('nav_ratings') ?>
            </a>
            <a href="?action=vacancies" @click="mobileMenuOpen = false"
                class="tf-mobile-link block px-3 py-2 rounded-md text-base font-medium <?= (in_array($current_action, ['vacancies', 'interview', 'interview-room', 'courses-interview', 'interview-ai'], true)) ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' ?>">
                <i class="fas fa-briefcase mr-2"></i> <?= t('nav_vacancies') ?>
            </a>
            <a href="?action=courses-interview" @click="mobileMenuOpen = false"
                class="block px-8 py-2 rounded-md text-sm font-medium <?= ($current_action === 'courses-interview') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' ?>">
                <i class="fas fa-user-tie mr-2"></i> <?= t('courses_interview_link') ?>
            </a>
            <a href="?action=interview" @click="mobileMenuOpen = false"
                class="block px-8 py-2 rounded-md text-sm font-medium <?= (in_array($current_action, ['interview', 'interview-room'], true)) ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' ?>">
                <i class="fas fa-video mr-2"></i> <?= t('nav_interview', 'Interview') ?>
            </a>
            <a href="?action=interview-ai" @click="mobileMenuOpen = false"
                class="block px-8 py-2 rounded-md text-sm font-medium <?= ($current_action === 'interview-ai') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' ?>">
                <i class="fas fa-robot mr-2"></i> <?= t('interview_ai_nav', 'AI Interview') ?>
            </a>
            
            <!-- REMOVED: Community Link -->

            <div class="pt-3 mt-2 border-t border-gray-100 space-y-2">
                <label for="header-mobile-lang-select" class="sr-only"><?= t('label_language') ?></label>
                <select id="header-mobile-lang-select"
                    class="tf-header-select w-full border border-gray-200 rounded-full px-3 py-2 text-sm font-medium text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    onchange="window.location.href=this.value">
                    <option value="<?= htmlspecialchars(langUrl('ru')) ?>" <?= $lang === 'ru' ? 'selected' : '' ?>>
                        <?= t('lang_ru') ?>
                    </option>
                    <option value="<?= htmlspecialchars(langUrl('en')) ?>" <?= $lang === 'en' ? 'selected' : '' ?>>
                        <?= t('lang_en') ?>
                    </option>
                    <option value="<?= htmlspecialchars(langUrl('tg')) ?>" <?= $lang === 'tg' ? 'selected' : '' ?>>
                        <?= t('lang_tg') ?>
                    </option>
                </select>
                <a href="?action=profile" @click="mobileMenuOpen = false"
                    class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-user-circle mr-2"></i><?= t('menu_profile') ?>
                </a>
                <a href="?action=logout" @click="mobileMenuOpen = false"
                    class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-sign-out-alt mr-2"></i><?= t('menu_logout') ?>
                </a>
            </div>
        </div>
    </div>
</header>

<?php include __DIR__ . '/notifications.php'; ?>

<div 
    class="tf-header-chat-fab tf-quick-fab"
    x-data="{ open: false, closeTimer: null }"
    @mouseenter="if (closeTimer) clearTimeout(closeTimer); open = true"
    @mouseleave="closeTimer = setTimeout(() => { open = false }, 300)"
>
    <button 
        type="button"
        class="tf-quick-fab-btn"
        aria-label="<?= htmlspecialchars(t('nav_community')) ?>"
    >
        <i class="fas fa-message" style="font-size: 0.95rem !important;"></i>
    </button>

    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="tf-quick-fab-panel"
        @mouseenter="if (closeTimer) clearTimeout(closeTimer); open = true"
        @mouseleave="closeTimer = setTimeout(() => { open = false }, 300)"
    >
        <div class="flex items-center justify-between mb-3">
            <h3 class="m-0 text-sm font-semibold text-slate-900">
                <?= t('nav_community') ?>
            </h3>
            <button 
                type="button" 
                @click="open = false"
                class="rounded-lg p-1 text-sm text-slate-400 hover:bg-slate-100 hover:text-slate-600"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="flex flex-col gap-2">
            <!-- REMOVED Community Link from FAB as requested? No, just from main nav. Keeping here for utility. -->
            <!-- If you want it removed everywhere, delete this anchor -->
            <a 
                href="?action=community"
                style="
                    display: flex;
                    align-items: center;
                    padding: 0.5rem 0.75rem;
                    border-radius: 0.75rem;
                    background-color: #f9fafb;
                    color: #374151;
                    text-decoration: none;
                    font-weight: 500;
                    font-size: 0.875rem;
                    transition: background-color 0.2s;
                "
                onmouseover="this.style.backgroundColor='#f3f4f6'"
                onmouseout="this.style.backgroundColor='#f9fafb'"
            >
                <i class="fas fa-users" style="color: #4f46e5; margin-right: 0.5rem; width: 1.25rem; text-align: center;"></i>
                <span><?= t('nav_community') ?></span>
            </a>
            <a
                href="?action=dashboard#ai-tutor"
                style="
                    display: flex;
                    align-items: center;
                    padding: 0.5rem 0.75rem;
                    border-radius: 0.75rem;
                    background-color: #f9fafb;
                    color: #374151;
                    text-decoration: none;
                    font-weight: 500;
                    font-size: 0.875rem;
                    transition: background-color 0.2s;
                "
                onmouseover="this.style.backgroundColor='#f3f4f6'"
                onmouseout="this.style.backgroundColor='#f9fafb'"
            >
                <i class="fas fa-robot" style="color: #4f46e5; margin-right: 0.5rem; width: 1.25rem; text-align: center;"></i>
                <span><?= t('dashboard_ai_title', 'AI-тьютор') ?></span>
            </a>
            <a 
                href="?action=it-events"
                style="
                    display: flex;
                    align-items: center;
                    padding: 0.5rem 0.75rem;
                    border-radius: 0.75rem;
                    background-color: #f9fafb;
                    color: #374151;
                    text-decoration: none;
                    font-weight: 500;
                    font-size: 0.875rem;
                    transition: background-color 0.2s;
                "
                onmouseover="this.style.backgroundColor='#f3f4f6'"
                onmouseout="this.style.backgroundColor='#f9fafb'"
            >
                <i class="fas fa-calendar-days" style="color: #0ea5e9; margin-right: 0.5rem; width: 1.25rem; text-align: center;"></i>
                <span><?= t('nav_it_events_tj', 'IT events TJ') ?></span>
            </a>
        </div>
    </div>
</div>

<script>
(() => {
    const header = document.querySelector('[data-fixed-header]');
    if (header) {
        const setOffset = () => {
            const offset = `${header.offsetHeight}px`;
            document.body.style.paddingTop = offset;
            document.documentElement.style.setProperty('--tf-header-offset', offset);
        };
        setOffset();
        window.addEventListener('resize', setOffset);
    }

    const syncModalState = () => {
        const anyOpen = document.querySelector('.tf-ai-modal.is-open, .modal-backdrop.active, [role="dialog"]:not(.hidden)');
        document.body.classList.toggle('tf-modal-open', !!anyOpen);
    };
    const observer = new MutationObserver(syncModalState);
    observer.observe(document.body, {
        attributes: true,
        attributeFilter: ['class', 'style'],
        subtree: true
    });
    syncModalState();
})();
</script>
