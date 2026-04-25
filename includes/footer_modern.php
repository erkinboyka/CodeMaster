<?php
/**
 * CodeMaster Platform - Modern Footer (LeetCode/ElectiCode Style)
 */
?>

<footer class="cm-footer">
  <div class="cm-footer-container">
    <!-- Main Content -->
    <div class="cm-footer-grid">
      <!-- Brand Column -->
      <div class="cm-footer-brand-col">
        <a href="?action=dashboard" class="cm-footer-brand">
          <div class="cm-header-logo" style="width: 36px; height: 36px; font-size: var(--cm-font-size-base);">CM</div>
          <span>CodeMaster</span>
        </a>
        <p class="cm-footer-description">
          Современная платформа для изучения программирования, решения задач и подготовки к собеседованиям.
        </p>
        <div class="cm-footer-socials">
          <a href="#" class="cm-footer-social" aria-label="Telegram">
            <i class="fab fa-telegram"></i>
          </a>
          <a href="#" class="cm-footer-social" aria-label="GitHub">
            <i class="fab fa-github"></i>
          </a>
          <a href="#" class="cm-footer-social" aria-label="YouTube">
            <i class="fab fa-youtube"></i>
          </a>
          <a href="#" class="cm-footer-social" aria-label="VK">
            <i class="fab fa-vk"></i>
          </a>
        </div>
      </div>
      
      <!-- Learning Column -->
      <div class="cm-footer-col">
        <h4 class="cm-footer-title"><?= t('nav_education', 'Обучение') ?></h4>
        <ul class="cm-footer-links">
          <li><a href="?action=courses"><?= t('nav_courses') ?></a></li>
          <li><a href="?action=roadmaps"><?= t('nav_roadmap') ?></a></li>
          <li><a href="?action=visualizations"><?= t('nav_visualizations', 'Визуализация') ?></a></li>
          <li><a href="?action=contests"><?= t('nav_contests', 'Конкурсы') ?></a></li>
        </ul>
      </div>
      
      <!-- Career Column -->
      <div class="cm-footer-col">
        <h4 class="cm-footer-title"><?= t('nav_vacancies') ?></h4>
        <ul class="cm-footer-links">
          <li><a href="?action=vacancies">Все вакансии</a></li>
          <li><a href="?action=courses-interview"><?= t('courses_interview_link') ?></a></li>
          <li><a href="?action=interview"><?= t('nav_interview', 'Interview') ?></a></li>
          <li><a href="?action=interview-ai"><?= t('interview_ai_nav', 'AI Interview') ?></a></li>
        </ul>
      </div>
      
      <!-- Community Column -->
      <div class="cm-footer-col">
        <h4 class="cm-footer-title">Сообщество</h4>
        <ul class="cm-footer-links">
          <li><a href="?action=ratings"><?= t('nav_ratings') ?></a></li>
          <li><a href="?action=community">Форум</a></li>
          <li><a href="?action=it_events">События</a></li>
          <li><a href="#">Блог</a></li>
        </ul>
      </div>
      
      <!-- Support Column -->
      <div class="cm-footer-col">
        <h4 class="cm-footer-title">Поддержка</h4>
        <ul class="cm-footer-links">
          <li><a href="#">Помощь</a></li>
          <li><a href="#">Контакты</a></li>
          <li><a href="#">Privacy Policy</a></li>
          <li><a href="#">Terms of Service</a></li>
        </ul>
      </div>
    </div>
    
    <!-- Bottom Bar -->
    <div class="cm-footer-bottom">
      <p class="cm-footer-copyright">
        © <?= date('Y') ?> CodeMaster. Все права защищены.
      </p>
      <div class="cm-footer-lang">
        <select class="cm-select cm-select-sm" onchange="window.location.href=this.value">
          <option value="<?= htmlspecialchars(langUrl('ru')) ?>" <?= currentLang() === 'ru' ? 'selected' : '' ?>>🇷🇺 Русский</option>
          <option value="<?= htmlspecialchars(langUrl('en')) ?>" <?= currentLang() === 'en' ? 'selected' : '' ?>>🇺🇸 English</option>
          <option value="<?= htmlspecialchars(langUrl('tg')) ?>" <?= currentLang() === 'tg' ? 'selected' : '' ?>>🇹🇯 Тоҷикӣ</option>
        </select>
      </div>
    </div>
  </div>
</footer>

<style>
.cm-footer {
  background: var(--cm-color-bg-secondary);
  border-top: 1px solid var(--cm-color-border-primary);
  padding: var(--cm-spacing-12) 0 var(--cm-spacing-8);
  margin-top: var(--cm-spacing-16);
}

.cm-footer-container {
  max-width: var(--cm-container-max);
  margin: 0 auto;
  padding: 0 var(--cm-spacing-6);
}

.cm-footer-grid {
  display: grid;
  grid-template-columns: 2fr repeat(4, 1fr);
  gap: var(--cm-spacing-8);
  margin-bottom: var(--cm-spacing-12);
}

.cm-footer-brand-col {
  display: flex;
  flex-direction: column;
  gap: var(--cm-spacing-4);
}

.cm-footer-brand {
  display: flex;
  align-items: center;
  gap: var(--cm-spacing-3);
  font-size: var(--cm-font-size-xl);
  font-weight: var(--cm-font-weight-extrabold);
  color: var(--cm-color-text-primary);
  text-decoration: none;
}

.cm-footer-description {
  font-size: var(--cm-font-size-sm);
  color: var(--cm-color-text-secondary);
  line-height: 1.7;
  max-width: 300px;
}

.cm-footer-socials {
  display: flex;
  gap: var(--cm-spacing-3);
  margin-top: var(--cm-spacing-2);
}

.cm-footer-social {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--cm-color-bg-tertiary);
  border-radius: var(--cm-radius-md);
  color: var(--cm-color-text-secondary);
  transition: all var(--cm-transition-fast);
  font-size: var(--cm-font-size-lg);
}

.cm-footer-social:hover {
  background: var(--cm-color-accent-primary);
  color: #fff;
  transform: translateY(-2px);
}

.cm-footer-col h4 {
  font-size: var(--cm-font-size-sm);
  font-weight: var(--cm-font-weight-bold);
  color: var(--cm-color-text-primary);
  margin-bottom: var(--cm-spacing-4);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.cm-footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: var(--cm-spacing-3);
}

.cm-footer-links a {
  font-size: var(--cm-font-size-sm);
  color: var(--cm-color-text-secondary);
  text-decoration: none;
  transition: color var(--cm-transition-fast);
}

.cm-footer-links a:hover {
  color: var(--cm-color-accent-primary);
}

.cm-footer-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: var(--cm-spacing-8);
  border-top: 1px solid var(--cm-color-border-primary);
}

.cm-footer-copyright {
  font-size: var(--cm-font-size-sm);
  color: var(--cm-color-text-muted);
}

/* Responsive */
@media (max-width: 1024px) {
  .cm-footer-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .cm-footer-brand-col {
    grid-column: span 2;
  }
}

@media (max-width: 640px) {
  .cm-footer-grid {
    grid-template-columns: 1fr;
  }
  
  .cm-footer-brand-col {
    grid-column: span 1;
  }
  
  .cm-footer-bottom {
    flex-direction: column;
    gap: var(--cm-spacing-4);
    text-align: center;
  }
  
  .cm-footer {
    padding: var(--cm-spacing-8) 0 var(--cm-spacing-6);
  }
}
</style>
