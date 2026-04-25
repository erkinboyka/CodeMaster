/**
 * CodeMaster UI Controller
 * LeetCode/ElectiCode Style Theme Management
 */

(function() {
  'use strict';

  // ==================== THEME MANAGEMENT ====================
  const THEME_KEY = 'cm_theme_preference';
  
  function getPreferredTheme() {
    const saved = localStorage.getItem(THEME_KEY);
    if (saved) return saved;
    
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
      return 'light';
    }
    return 'dark';
  }

  function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem(THEME_KEY, theme);
    updateThemeIcon(theme);
  }

  function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme') || 'dark';
    const next = current === 'dark' ? 'light' : 'dark';
    setTheme(next);
  }

  function updateThemeIcon(theme) {
    const icons = document.querySelectorAll('[data-theme-icon]');
    icons.forEach(icon => {
      icon.className = theme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
    });
  }

  function initTheme() {
    setTheme(getPreferredTheme());
    
    if (window.matchMedia) {
      window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', (e) => {
        if (!localStorage.getItem(THEME_KEY)) {
          setTheme(e.matches ? 'light' : 'dark');
        }
      });
    }
  }

  // ==================== SIDEBAR ====================
  function toggleSidebar() {
    const sidebar = document.querySelector('.cm-sidebar');
    if (sidebar) sidebar.classList.toggle('open');
  }

  function closeSidebar() {
    const sidebar = document.querySelector('.cm-sidebar');
    if (sidebar) sidebar.classList.remove('open');
  }

  // ==================== NOTIFICATIONS ====================
  function showNotification(message, type = 'info', duration = 5000) {
    let container = document.getElementById('cm-notification-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'cm-notification-container';
      container.style.cssText = 'position:fixed;top:84px;right:20px;z-index:800;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
      document.body.appendChild(container);
    }
    
    const notification = document.createElement('div');
    notification.className = 'cm-notification cm-notification-' + type;
    notification.innerHTML = '<div class="cm-notification-content"><i class="fas ' + getNotificationIcon(type) + '"></i><span>' + escapeHtml(message) + '</span></div><button class="cm-notification-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>';
    
    container.appendChild(notification);
    requestAnimationFrame(() => notification.classList.add('cm-notification-show'));
    
    if (duration > 0) {
      setTimeout(() => {
        notification.classList.remove('cm-notification-show');
        setTimeout(() => notification.remove(), 300);
      }, duration);
    }
  }

  function getNotificationIcon(type) {
    return { success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' }[type] || 'fa-info-circle';
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // ==================== MODALS ====================
  function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('cm-modal-active');
      document.body.style.overflow = 'hidden';
    }
  }

  function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove('cm-modal-active');
      document.body.style.overflow = '';
    }
  }

  function initModals() {
    document.querySelectorAll('.cm-modal').forEach(modal => {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          modal.classList.remove('cm-modal-active');
          document.body.style.overflow = '';
        }
      });
    });
    
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        document.querySelectorAll('.cm-modal-active').forEach(modal => {
          modal.classList.remove('cm-modal-active');
          document.body.style.overflow = '';
        });
      }
    });
  }

  // ==================== COPY TO CLIPBOARD ====================
  async function copyToClipboard(text, successMessage = 'Copied!') {
    try {
      await navigator.clipboard.writeText(text);
      showNotification(successMessage, 'success', 2000);
      return true;
    } catch (err) {
      const textarea = document.createElement('textarea');
      textarea.value = text;
      textarea.style.position = 'fixed';
      textarea.style.opacity = '0';
      document.body.appendChild(textarea);
      textarea.select();
      try {
        document.execCommand('copy');
        showNotification(successMessage, 'success', 2000);
        return true;
      } catch (err2) {
        showNotification('Failed to copy', 'error');
        return false;
      } finally {
        document.body.removeChild(textarea);
      }
    }
  }

  // ==================== INIT ====================
  function init() {
    initTheme();
    initModals();
    
    window.cmToggleTheme = toggleTheme;
    window.cmSetTheme = setTheme;
    window.cmToggleSidebar = toggleSidebar;
    window.cmCloseSidebar = closeSidebar;
    window.cmShowNotification = showNotification;
    window.cmShowModal = showModal;
    window.cmHideModal = hideModal;
    window.cmCopyToClipboard = copyToClipboard;
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
