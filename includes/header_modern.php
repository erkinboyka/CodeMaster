<?php
/**
 * CodeMaster Platform - Modern Header (LeetCode/ElectiCode Style)
 * Supports dark/light themes with toggle
 */
$current_action = $_GET['action'] ?? 'home';
$lang = currentLang();
?>

<style>
/* Header-specific styles that extend the design system */
.cm-header-root {
  position: sticky;
  top: 0;
  z-index: var(--cm-z-fixed);
  background: var(--cm-color-bg-card);
  border-bottom: 1px solid var(--cm-color-border-primary);
  backdrop-filter: blur(8px);
}

.cm-header-container {
  max-width: var(--cm-container-max);
  margin: 0 auto;
  padding: 0 var(--cm-spacing-6);
  height: var(--cm-header-height);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.cm-header-brand {
  display: flex;
  align-items: center;
  gap: var(--cm-spacing-3);
  font-size: var(--cm-font-size-xl);
  font-weight: var(--cm-font-weight-extrabold);
  color: var(--cm-color-text-primary);
  text-decoration: none;
  transition: opacity var(--cm-transition-fast);
}

.cm-header-brand:hover {
  opacity: 0.8;
}

.cm-header-logo {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, var(--cm-color-accent-primary), var(--cm-color-accent-secondary));
  border-radius: var(--cm-radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-weight: var(--cm-font-weight-bold);
  font-size: var(--cm-font-size-lg);
}

.cm-header-nav {
  display: flex;
  align-items: center;
  gap: var(--cm-spacing-2);
}

.cm-header-link {
  display: inline-flex;
  align-items: center;
  gap: var(--cm-spacing-2);
  padding: var(--cm-spacing-2) var(--cm-spacing-4);
  font-size: var(--cm-font-size-sm);
  font-weight: var(--cm-font-weight-medium);
  color: var(--cm-color-text-secondary);
  text-decoration: none;
  border-radius: var(--cm-radius-md);
  transition: all var(--cm-transition-fast);
}

.cm-header-link:hover,
.cm-header-link.active {
  color: var(--cm-color-text-primary);
  background: var(--cm-color-bg-hover);
}

.cm-header-link.active {
  color: var(--cm-color-accent-primary);
}

.cm-header-actions {
  display: flex;
  align-items: center;
  gap: var(--cm-spacing-3);
}

.cm-header-theme-toggle {
  background: transparent;
  border: 1px solid var(--cm-color-border-primary);
  border-radius: var(--cm-radius-md);
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all var(--cm-transition-fast);
  font-size: var(--cm-font-size-lg);
}

.cm-header-theme-toggle:hover {
  background: var(--cm-color-bg-hover);
  border-color: var(--cm-color-border-light);
}

.cm-header-mobile-toggle {
  display: none;
  background: transparent;
  border: none;
  color: var(--cm-color-text-primary);
  font-size: var(--cm-font-size-xl);
  cursor: pointer;
  padding: var(--cm-spacing-2);
}

.cm-header-mobile-menu {
  position: fixed;
  top: var(--cm-header-height);
  left: 0;
  right: 0;
  background: var(--cm-color-bg-card);
  border-bottom: 1px solid var(--cm-color-border-primary);
  padding: var(--cm-spacing-4);
  transform: translateY(-100%);
  opacity: 0;
  visibility: hidden;
  transition: all var(--cm-transition-base);
  z-index: var(--cm-z-sticky);
}

.cm-header-mobile-menu.open {
  transform: translateY(0);
  opacity: 1;
  visibility: visible;
}

.cm-header-mobile-nav {
  display: flex;
  flex-direction: column;
  gap: var(--cm-spacing-2);
}

.cm-header-dropdown {
  position: relative;
}

.cm-header-dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  min-width: 200px;
  background: var(--cm-color-bg-card);
  border: 1px solid var(--cm-color-border-primary);
  border-radius: var(--cm-radius-lg);
  box-shadow: var(--cm-shadow-xl);
  padding: var(--cm-spacing-2);
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all var(--cm-transition-fast);
}

.cm-header-dropdown:hover .cm-header-dropdown-menu,
.cm-header-dropdown-menu.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.cm-header-dropdown-item {
  display: flex;
  align-items: center;
  gap: var(--cm-spacing-3);
  padding: var(--cm-spacing-3) var(--cm-spacing-4);
  font-size: var(--cm-font-size-sm);
  color: var(--cm-color-text-secondary);
  text-decoration: none;
  border-radius: var(--cm-radius-md);
  transition: all var(--cm-transition-fast);
}

.cm-header-dropdown-item:hover {
  background: var(--cm-color-bg-hover);
  color: var(--cm-color-text-primary);
}

/* Responsive */
@media (max-width: 768px) {
  .cm-header-nav {
    display: none;
  }
  
  .cm-header-mobile-toggle {
    display: flex;
  }
  
  .cm-header-actions .cm-btn {
    display: none;
  }
}
</style>

<header class="cm-header-root" x-data="{ mobileMenuOpen: false, themeDropdown: false }">
  <div class="cm-header-container">
    <!-- Brand -->
    <a href="?action=dashboard" class="cm-header-brand">
      <div class="cm-header-logo">CM</div>
      <span>CodeMaster</span>
    </a>
    
    <!-- Desktop Navigation -->
    <nav class="cm-header-nav">
      <div class="cm-header-dropdown" @mouseenter="themeDropdown = true" @mouseleave="themeDropdown = false">
        <a href="?action=courses" class="cm-header-link <?= in_array($current_action, ['courses', 'course', 'roadmap', 'roadmaps', 'visualizations']) ? 'active' : '' ?>">
          <i class="fas fa-layer-group"></i>
          <span><?= t('nav_education', 'Обучение') ?></span>
          <i class="fas fa-chevron-down text-xs"></i>
        </a>
        <div class="cm-header-dropdown-menu" :class="{ 'show': themeDropdown }">
          <a href="?action=courses" class="cm-header-dropdown-item">
            <i class="fas fa-book"></i>
            <span><?= t('nav_courses') ?></span>
          </a>
          <a href="?action=roadmaps" class="cm-header-dropdown-item">
            <i class="fas fa-project-diagram"></i>
            <span><?= t('nav_roadmap') ?></span>
          </a>
          <a href="?action=visualizations" class="cm-header-dropdown-item">
            <i class="fas fa-wave-square"></i>
            <span><?= t('nav_visualizations', 'Визуализация') ?></span>
          </a>
        </div>
      </div>
      
      <a href="?action=contests" class="cm-header-link <?= in_array($current_action, ['contest', 'contests']) ? 'active' : '' ?>">
        <i class="fas fa-code"></i>
        <span><?= t('nav_contests', 'Конкурсы') ?></span>
      </a>
      
      <a href="?action=ratings" class="cm-header-link <?= $current_action === 'ratings' ? 'active' : '' ?>">
        <i class="fas fa-trophy"></i>
        <span><?= t('nav_ratings') ?></span>
      </a>
      
      <div class="cm-header-dropdown" @mouseenter="themeDropdown = true" @mouseleave="themeDropdown = false">
        <a href="?action=vacancies" class="cm-header-link <?= in_array($current_action, ['vacancies', 'courses-interview', 'interview', 'interview-room', 'interview-ai']) ? 'active' : '' ?>">
          <i class="fas fa-briefcase"></i>
          <span><?= t('nav_vacancies') ?></span>
          <i class="fas fa-chevron-down text-xs"></i>
        </a>
        <div class="cm-header-dropdown-menu" :class="{ 'show': themeDropdown }">
          <a href="?action=courses-interview" class="cm-header-dropdown-item">
            <i class="fas fa-user-tie"></i>
            <span><?= t('courses_interview_link') ?></span>
          </a>
          <a href="?action=interview" class="cm-header-dropdown-item">
            <i class="fas fa-video"></i>
            <span><?= t('nav_interview', 'Interview') ?></span>
          </a>
          <a href="?action=interview-ai" class="cm-header-dropdown-item">
            <i class="fas fa-robot"></i>
            <span><?= t('interview_ai_nav', 'AI Interview') ?></span>
          </a>
        </div>
      </div>
    </nav>
    
    <!-- Actions -->
    <div class="cm-header-actions">
      <!-- Language Select -->
      <select class="cm-select cm-select-sm" onchange="window.location.href=this.value" style="min-width: 80px;">
        <option value="<?= htmlspecialchars(langUrl('ru')) ?>" <?= $lang === 'ru' ? 'selected' : 'false' ?>>🇷🇺 RU</option>
        <option value="<?= htmlspecialchars(langUrl('en')) ?>" <?= $lang === 'en' ? 'selected' : 'false' ?>>🇺🇸 EN</option>
        <option value="<?= htmlspecialchars(langUrl('tg')) ?>" <?= $lang === 'tg' ? 'selected' : 'false' ?>>🇹🇯 TG</option>
      </select>
      
      <!-- Theme Toggle -->
      <button class="cm-header-theme-toggle" data-theme-toggle aria-label="Toggle theme">
        <span class="theme-icon">☀️</span>
      </button>
      
      <!-- Auth Buttons -->
      <?php if (isLoggedIn()): ?>
        <a href="?action=profile" class="cm-btn cm-btn-outline cm-btn-sm">
          <i class="fas fa-user"></i>
          <span class="hidden sm:inline"><?= htmlspecialchars($user['username'] ?? 'Profile') ?></span>
        </a>
      <?php else: ?>
        <a href="?action=login" class="cm-btn cm-btn-ghost cm-btn-sm">Вход</a>
        <a href="?action=register" class="cm-btn cm-btn-primary cm-btn-sm">Регистрация</a>
      <?php endif; ?>
      
      <!-- Mobile Toggle -->
      <button class="cm-header-mobile-toggle" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Menu">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </div>
  
  <!-- Mobile Menu -->
  <div class="cm-header-mobile-menu" :class="{ 'open': mobileMenuOpen }">
    <nav class="cm-header-mobile-nav">
      <a href="?action=courses" class="cm-header-link">
        <i class="fas fa-layer-group"></i>
        <span><?= t('nav_education', 'Обучение') ?></span>
      </a>
      <a href="?action=contests" class="cm-header-link">
        <i class="fas fa-code"></i>
        <span><?= t('nav_contests', 'Конкурсы') ?></span>
      </a>
      <a href="?action=ratings" class="cm-header-link">
        <i class="fas fa-trophy"></i>
        <span><?= t('nav_ratings') ?></span>
      </a>
      <a href="?action=vacancies" class="cm-header-link">
        <i class="fas fa-briefcase"></i>
        <span><?= t('nav_vacancies') ?></span>
      </a>
      <?php if (isLoggedIn()): ?>
        <a href="?action=profile" class="cm-header-link">
          <i class="fas fa-user"></i>
          <span>Профиль</span>
        </a>
      <?php else: ?>
        <a href="?action=login" class="cm-header-link">
          <i class="fas fa-sign-in-alt"></i>
          <span>Вход</span>
        </a>
        <a href="?action=register" class="cm-btn cm-btn-primary cm-btn-block">
          Регистрация
        </a>
      <?php endif; ?>
    </nav>
  </div>
</header>
