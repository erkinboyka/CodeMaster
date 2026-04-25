<?php
$current_action = $_GET['action'] ?? 'home';
$lang = currentLang();
?>

<header class="cm-header">
    <div class="cm-container" style="display:flex;align-items:center;width:100%;max-width:var(--cm-container-max);margin:0 auto;">
        <a href="?action=dashboard" class="cm-header-brand">
            <div class="cm-header-logo">
                <i class="fas fa-code"></i>
            </div>
            <span>CodeMaster</span>
        </a>
        
        <nav class="cm-header-nav">
            <a href="?action=courses" class="cm-header-link <?= in_array($current_action, ['courses', 'course']) ? 'active' : '' ?>">
                <i class="fas fa-book"></i> <?= t('nav_courses') ?>
            </a>
            <a href="?action=contests" class="cm-header-link <?= in_array($current_action, ['contest', 'contests']) ? 'active' : '' ?>">
                <i class="fas fa-code"></i> <?= t('nav_contests', 'Конкурсы') ?>
            </a>
            <a href="?action=ratings" class="cm-header-link <?= $current_action === 'ratings' ? 'active' : '' ?>">
                <i class="fas fa-trophy"></i> <?= t('nav_ratings') ?>
            </a>
            <a href="?action=vacancies" class="cm-header-link <?= in_array($current_action, ['vacancies', 'vacancy']) ? 'active' : '' ?>">
                <i class="fas fa-briefcase"></i> <?= t('nav_vacancies') ?>
            </a>
        </nav>
        
        <div class="cm-header-actions">
            <button class="cm-btn cm-btn-sm cm-btn-outline" onclick="toggleTheme()">
                <i class="fas fa-moon"></i>
            </button>
            <div class="cm-avatar">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="<?= htmlspecialchars($user['username']) ?>">
                <?php else: ?>
                    <?= strtoupper(substr($user['username'] ?? 'U', 0, 2)) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<script>
function toggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme');
    html.setAttribute('data-theme', current === 'light' ? 'dark' : 'light');
    localStorage.setItem('theme', html.getAttribute('data-theme'));
}

// Load saved theme
document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', saved);
});
</script>
