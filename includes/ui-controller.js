/**
 * CodeMaster Platform - UI/UX Controller
 * LeetCode / ElectiCode Style Theme Manager
 */

(function() {
  'use strict';

  // ============================================
  // THEME MANAGER
  // ============================================
  const ThemeManager = {
    STORAGE_KEY: 'cm-theme-preference',
    COOKIE_NAME: 'cm-theme',
    DEFAULT_THEME: 'dark',

    init() {
      this.loadTheme();
      this.bindEvents();
    },

    loadTheme() {
      const saved = localStorage.getItem(this.STORAGE_KEY);
      const theme = saved || this.DEFAULT_THEME;
      this.setTheme(theme, false);
    },

    setTheme(theme, save = true) {
      if (save) {
        localStorage.setItem(this.STORAGE_KEY, theme);
        // Set cookie for server-side detection
        document.cookie = `${this.COOKIE_NAME}=${theme};path=/;max-age=31536000;samesite=lax`;
      }
      document.documentElement.setAttribute('data-theme', theme);
      document.documentElement.setAttribute('data-color-scheme', theme);
      this.updateToggleState(theme);
      this.updateMetaThemeColor(theme);
    },

    updateMetaThemeColor(theme) {
      const metaThemeColor = document.querySelector('meta[name="theme-color"]');
      if (metaThemeColor) {
        metaThemeColor.setAttribute('content', theme === 'light' ? '#f8fafc' : '#0f172a');
      }
    },

    toggle() {
      const current = document.documentElement.getAttribute('data-theme');
      const next = current === 'dark' ? 'light' : 'dark';
      this.setTheme(next);
    },

    updateToggleState(theme) {
      const toggles = document.querySelectorAll('[data-theme-toggle]');
      toggles.forEach(toggle => {
        const icon = toggle.querySelector('.theme-icon');
        if (icon) {
          icon.textContent = theme === 'dark' ? '☀️' : '🌙';
        }
      });
    },

    bindEvents() {
      document.addEventListener('click', (e) => {
        const toggle = e.target.closest('[data-theme-toggle]');
        if (toggle) {
          e.preventDefault();
          this.toggle();
        }
      });

      // Keyboard shortcut (Alt + T)
      document.addEventListener('keydown', (e) => {
        if (e.altKey && e.key === 't') {
          e.preventDefault();
          this.toggle();
        }
      });
    }
  };

  // ============================================
  // SIDEBAR CONTROLLER
  // ============================================
  const SidebarController = {
    CLASS_OPEN: 'open',
    STORAGE_KEY: 'cm-sidebar-state',

    init() {
      this.sidebar = document.querySelector('.cm-sidebar');
      this.toggleBtn = document.querySelector('[data-sidebar-toggle]');
      
      if (this.toggleBtn) {
        this.toggleBtn.addEventListener('click', () => this.toggle());
      }

      // Close on outside click
      document.addEventListener('click', (e) => {
        if (this.isOpen() && !e.target.closest('.cm-sidebar') && !e.target.closest('[data-sidebar-toggle]')) {
          this.close();
        }
      });

      // Keyboard (Escape)
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && this.isOpen()) {
          this.close();
        }
      });
    },

    isOpen() {
      return this.sidebar?.classList.contains(this.CLASS_OPEN);
    },

    toggle() {
      if (this.isOpen()) {
        this.close();
      } else {
        this.open();
      }
    },

    open() {
      this.sidebar?.classList.add(this.CLASS_OPEN);
      document.body.style.overflow = 'hidden';
    },

    close() {
      this.sidebar?.classList.remove(this.CLASS_OPEN);
      document.body.style.overflow = '';
    }
  };

  // ============================================
  // TOOLTIP INITIALIZER
  // ============================================
  const TooltipManager = {
    init() {
      document.querySelectorAll('[data-tooltip]').forEach(el => {
        el.classList.add('cm-tooltip');
      });
    }
  };

  // ============================================
  // SKELETON LOADER
  // ============================================
  const SkeletonLoader = {
    show(containerId) {
      const container = document.getElementById(containerId);
      if (!container) return;

      container.innerHTML = `
        <div class="cm-skeleton cm-skeleton-title"></div>
        <div class="cm-skeleton cm-skeleton-text"></div>
        <div class="cm-skeleton cm-skeleton-text"></div>
        <div class="cm-skeleton cm-skeleton-text" style="width: 60%"></div>
      `;
    },

    hide(containerId, content) {
      const container = document.getElementById(containerId);
      if (!container) return;
      container.innerHTML = content;
    }
  };

  // ============================================
  // NOTIFICATION SYSTEM
  // ============================================
  const NotificationManager = {
    CONTAINER_ID: 'cm-notifications',

    init() {
      let container = document.getElementById(this.CONTAINER_ID);
      if (!container) {
        container = document.createElement('div');
        container.id = this.CONTAINER_ID;
        container.className = 'cm-notifications';
        container.style.cssText = `
          position: fixed;
          top: 80px;
          right: 24px;
          z-index: 9999;
          display: flex;
          flex-direction: column;
          gap: 12px;
          pointer-events: none;
        `;
        document.body.appendChild(container);
      }
    },

    show(message, type = 'info', duration = 3000) {
      const container = document.getElementById(this.CONTAINER_ID);
      if (!container) return;

      const notification = document.createElement('div');
      notification.className = `cm-notification cm-notification-${type}`;
      notification.style.cssText = `
        padding: 16px 20px;
        background: var(--cm-color-bg-card);
        border: 1px solid var(--cm-color-border-primary);
        border-radius: var(--cm-radius-lg);
        box-shadow: var(--cm-shadow-lg);
        color: var(--cm-color-text-primary);
        font-size: var(--cm-font-size-sm);
        animation: cm-slide-down 0.3s ease-out;
        pointer-events: auto;
        min-width: 300px;
        max-width: 500px;
      `;

      const icons = {
        success: '✓',
        error: '✕',
        warning: '⚠',
        info: 'ℹ'
      };

      const colors = {
        success: 'var(--cm-color-accent-success)',
        error: 'var(--cm-color-accent-danger)',
        warning: 'var(--cm-color-accent-warning)',
        info: 'var(--cm-color-accent-info)'
      };

      notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
          <span style="color: ${colors[type]}; font-size: 20px;">${icons[type]}</span>
          <span>${message}</span>
        </div>
      `;

      container.appendChild(notification);

      setTimeout(() => {
        notification.style.animation = 'cm-fade-in 0.3s ease-out reverse';
        setTimeout(() => notification.remove(), 300);
      }, duration);
    },

    success(message) { this.show(message, 'success'); },
    error(message) { this.show(message, 'error'); },
    warning(message) { this.show(message, 'warning'); },
    info(message) { this.show(message, 'info'); }
  };

  // ============================================
  // COPY TO CLIPBOARD
  // ============================================
  const ClipboardManager = {
    init() {
      document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-copy]');
        if (!btn) return;

        const text = btn.getAttribute('data-copy');
        try {
          await navigator.clipboard.writeText(text);
          NotificationManager.success('Скопировано в буфер обмена!');
          
          // Visual feedback
          const originalText = btn.innerHTML;
          btn.innerHTML = '✓ Скопировано!';
          setTimeout(() => {
            btn.innerHTML = originalText;
          }, 2000);
        } catch (err) {
          NotificationManager.error('Ошибка копирования');
        }
      });
    }
  };

  // ============================================
  // CONFIRM DIALOGS
  // ============================================
  const ConfirmDialog = {
    async show(message, options = {}) {
      return new Promise((resolve) => {
        const modal = document.createElement('div');
        modal.className = 'cm-modal-backdrop';
        modal.style.cssText = `
          position: fixed;
          inset: 0;
          background: rgba(0, 0, 0, 0.7);
          display: flex;
          align-items: center;
          justify-content: center;
          z-index: var(--cm-z-modal);
          animation: cm-fade-in 0.2s ease-out;
        `;

        const dialog = document.createElement('div');
        dialog.style.cssText = `
          background: var(--cm-color-bg-card);
          border: 1px solid var(--cm-color-border-primary);
          border-radius: var(--cm-radius-lg);
          padding: var(--cm-spacing-6);
          max-width: 400px;
          width: 90%;
          animation: cm-scale-in 0.3s ease-out;
        `;

        dialog.innerHTML = `
          <p style="margin-bottom: var(--cm-spacing-6); color: var(--cm-color-text-primary);">${message}</p>
          <div style="display: flex; gap: var(--cm-spacing-3); justify-content: flex-end;">
            <button class="cm-btn cm-btn-secondary cm-btn-cancel">Отмена</button>
            <button class="cm-btn cm-btn-primary cm-btn-confirm">Подтвердить</button>
          </div>
        `;

        modal.appendChild(dialog);
        document.body.appendChild(modal);

        const cleanup = () => modal.remove();

        modal.querySelector('.cm-btn-cancel').addEventListener('click', () => {
          cleanup();
          resolve(false);
        });

        modal.querySelector('.cm-btn-confirm').addEventListener('click', () => {
          cleanup();
          resolve(true);
        });

        modal.addEventListener('click', (e) => {
          if (e.target === modal) {
            cleanup();
            resolve(false);
          }
        });
      });
    }
  };

  // ============================================
  // INITIALIZE ALL MODULES
  // ============================================
  function init() {
    ThemeManager.init();
    SidebarController.init();
    TooltipManager.init();
    NotificationManager.init();
    ClipboardManager.init();

    // Expose to window for external use
    window.CodeMasterUI = {
      theme: ThemeManager,
      sidebar: SidebarController,
      notifications: NotificationManager,
      confirm: ConfirmDialog,
      skeleton: SkeletonLoader
    };

    console.log('✅ CodeMaster UI initialized');
  }

  // Run on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
