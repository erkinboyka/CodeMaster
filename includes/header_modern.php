<?php
$currentAction = $_GET['action'] ?? 'home';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<header class="cm-header">
    <div class="cm-header-content">
        <!-- Logo -->
        <a href="?action=home" class="cm-logo">
            <div class="cm-logo-icon">CM</div>
            <span>CodeMaster</span>
        </a>
        
        <!-- Navigation -->
        <nav class="cm-nav cm-hidden cm-block-tablet">
            <a href="?action=courses" class="cm-nav-link <?= $currentAction === 'courses' ? 'active' : '' ?>">Курсы</a>
            <a href="?action=vacancies" class="cm-nav-link <?= $currentAction === 'vacancies' ? 'active' : '' ?>">Вакансии</a>
            <a href="?action=contests" class="cm-nav-link <?= $currentAction === 'contests' ? 'active' : '' ?>">Соревнования</a>
            <a href="?action=roadmaps" class="cm-nav-link <?= $currentAction === 'roadmaps' ? 'active' : '' ?>">Roadmaps</a>
            <a href="?action=ratings" class="cm-nav-link <?= $currentAction === 'ratings' ? 'active' : '' ?>">Рейтинг</a>
        </nav>
        
        <!-- Actions -->
        <div class="cm-header-actions">
            <!-- Theme Toggle -->
            <button class="cm-theme-toggle" onclick="cmToggleTheme()" aria-label="Переключить тему" data-tooltip="Переключить тему (Alt+T)">
                <i class="fas fa-sun" data-theme-icon></i>
            </button>
            
            <!-- Mobile Menu Toggle -->
            <button class="cm-theme-toggle cm-block-tablet-none" onclick="cmToggleSidebar()" aria-label="Меню">
                <i class="fas fa-bars"></i>
            </button>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="?action=dashboard" class="cm-btn cm-btn-secondary cm-btn-sm cm-hidden cm-block-desktop">
                    <i class="fas fa-user"></i>
                    <span>Профиль</span>
                </a>
            <?php else: ?>
                <a href="?action=login" class="cm-btn cm-btn-outline cm-btn-sm cm-hidden cm-block-desktop">
                    Войти
                </a>
                <a href="?action=register" class="cm-btn cm-btn-primary cm-btn-sm cm-hidden cm-block-desktop">
                    Регистрация
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Mobile Sidebar -->
<aside class="cm-sidebar" id="cm-sidebar">
    <div class="cm-flex cm-items-center cm-justify-between cm-mb-8">
        <a href="?action=home" class="cm-logo">
            <div class="cm-logo-icon">CM</div>
            <span>CodeMaster</span>
        </a>
        <button class="cm-theme-toggle" onclick="cmCloseSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <ul class="cm-sidebar-menu">
        <li class="cm-sidebar-item">
            <a href="?action=home" class="cm-sidebar-link <?= $currentAction === 'home' ? 'active' : '' ?>">
                <i class="fas fa-home cm-sidebar-icon"></i>
                <span>Главная</span>
            </a>
        </li>
        <li class="cm-sidebar-item">
            <a href="?action=courses" class="cm-sidebar-link <?= $currentAction === 'courses' ? 'active' : '' ?>">
                <i class="fas fa-book cm-sidebar-icon"></i>
                <span>Курсы</span>
            </a>
        </li>
        <li class="cm-sidebar-item">
            <a href="?action=vacancies" class="cm-sidebar-link <?= $currentAction === 'vacancies' ? 'active' : '' ?>">
                <i class="fas fa-briefcase cm-sidebar-icon"></i>
                <span>Вакансии</span>
            </a>
        </li>
        <li class="cm-sidebar-item">
            <a href="?action=contests" class="cm-sidebar-link <?= $currentAction === 'contests' ? 'active' : '' ?>">
                <i class="fas fa-trophy cm-sidebar-icon"></i>
                <span>Соревнования</span>
            </a>
        </li>
        <li class="cm-sidebar-item">
            <a href="?action=roadmaps" class="cm-sidebar-link <?= $currentAction === 'roadmaps' ? 'active' : '' ?>">
                <i class="fas fa-map cm-sidebar-icon"></i>
                <span>Roadmaps</span>
            </a>
        </li>
        <li class="cm-sidebar-item">
            <a href="?action=ratings" class="cm-sidebar-link <?= $currentAction === 'ratings' ? 'active' : '' ?>">
                <i class="fas fa-chart-leaderboard cm-sidebar-icon"></i>
                <span>Рейтинг</span>
            </a>
        </li>
        <li class="cm-sidebar-item">
            <a href="?action=community" class="cm-sidebar-link <?= $currentAction === 'community' ? 'active' : '' ?>">
                <i class="fas fa-users cm-sidebar-icon"></i>
                <span>Сообщество</span>
            </a>
        </li>
        <li class="cm-sidebar-item">
            <a href="?action=it_events" class="cm-sidebar-link <?= $currentAction === 'it_events' ? 'active' : '' ?>">
                <i class="fas fa-calendar-alt cm-sidebar-icon"></i>
                <span>IT Events</span>
            </a>
        </li>
    </ul>
    
    <div class="cm-mt-8 cm-pt-6" style="border-top: 1px solid var(--border-primary);">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="?action=dashboard" class="cm-sidebar-link">
                <i class="fas fa-user-circle cm-sidebar-icon"></i>
                <span>Личный кабинет</span>
            </a>
            <a href="?action=profile" class="cm-sidebar-link">
                <i class="fas fa-cog cm-sidebar-icon"></i>
                <span>Настройки</span>
            </a>
            <a href="?action=logout" class="cm-sidebar-link" style="color: var(--accent-danger);">
                <i class="fas fa-sign-out-alt cm-sidebar-icon"></i>
                <span>Выйти</span>
            </a>
        <?php else: ?>
            <a href="?action=login" class="cm-sidebar-link">
                <i class="fas fa-sign-in-alt cm-sidebar-icon"></i>
                <span>Войти</span>
            </a>
            <a href="?action=register" class="cm-sidebar-link">
                <i class="fas fa-user-plus cm-sidebar-icon"></i>
                <span>Регистрация</span>
            </a>
        <?php endif; ?>
    </div>
</aside>

<!-- Sidebar Overlay for Mobile -->
<div class="cm-sidebar-overlay hidden" onclick="cmCloseSidebar()" style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:150;"></div>
