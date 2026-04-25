<?php
/**
 * CodeMaster Platform - Modern Dashboard Page
 * LeetCode/ElectiCode Style with Dark/Light Theme Support
 */

$pageTitle = 'Dashboard';
$content = ob_start();
?>

<!-- Page Header -->
<div class="cm-page-header">
    <h1 class="cm-page-title">Добро пожаловать в CodeMaster</h1>
    <p class="cm-page-subtitle">Ваша платформа для изучения программирования и подготовки к собеседованиям</p>
</div>

<!-- Stats Grid -->
<div class="cm-stats-grid cm-grid cm-grid-4" style="margin-bottom: var(--cm-spacing-12);">
    <div class="cm-stat-card">
        <div class="cm-stat-value">0</div>
        <div class="cm-stat-label">Решено задач</div>
    </div>
    <div class="cm-stat-card">
        <div class="cm-stat-value">0</div>
        <div class="cm-stat-label">Пройдено курсов</div>
    </div>
    <div class="cm-stat-card">
        <div class="cm-stat-value">0</div>
        <div class="cm-stat-label">Контестов</div>
    </div>
    <div class="cm-stat-card">
        <div class="cm-stat-value">0</div>
        <div class="cm-stat-label">Рейтинг</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="cm-section-header">
    <h2 class="cm-section-title">Быстрый старт</h2>
</div>

<div class="cm-grid cm-grid-auto" style="margin-bottom: var(--cm-spacing-12);">
    <!-- Start Learning Card -->
    <div class="cm-card">
        <div class="cm-feature-icon" style="width: 48px; height: 48px; font-size: var(--cm-font-size-xl); margin-bottom: var(--cm-spacing-3);">
            <i class="fas fa-book"></i>
        </div>
        <h3 class="cm-feature-title">Начать обучение</h3>
        <p class="cm-feature-text" style="margin-bottom: var(--cm-spacing-4);">
            Выберите курс и начните изучение новых технологий
        </p>
        <a href="?action=courses" class="cm-btn cm-btn-primary cm-btn-block">
            <i class="fas fa-arrow-right"></i>
            Смотреть курсы
        </a>
    </div>

    <!-- Practice Card -->
    <div class="cm-card">
        <div class="cm-feature-icon" style="width: 48px; height: 48px; font-size: var(--cm-font-size-xl); margin-bottom: var(--cm-spacing-3); background: linear-gradient(135deg, var(--cm-color-accent-secondary), var(--cm-color-accent-tertiary));">
            <i class="fas fa-code"></i>
        </div>
        <h3 class="cm-feature-title">Практика</h3>
        <p class="cm-feature-text" style="margin-bottom: var(--cm-spacing-4);">
            Решайте задачи разных уровней сложности
        </p>
        <a href="?action=contests" class="cm-btn cm-btn-outline cm-btn-block">
            <i class="fas fa-play"></i>
            Начать решать
        </a>
    </div>

    <!-- Interview Prep Card -->
    <div class="cm-card">
        <div class="cm-feature-icon" style="width: 48px; height: 48px; font-size: var(--cm-font-size-xl); margin-bottom: var(--cm-spacing-3); background: linear-gradient(135deg, var(--cm-color-accent-warning), var(--cm-color-accent-danger));">
            <i class="fas fa-user-tie"></i>
        </div>
        <h3 class="cm-feature-title">Подготовка к интервью</h3>
        <p class="cm-feature-text" style="margin-bottom: var(--cm-spacing-4);">
            Пройдите симуляцию технического собеседования
        </p>
        <a href="?action=interview-ai" class="cm-btn cm-btn-secondary cm-btn-block">
            <i class="fas fa-robot"></i>
            AI Интервью
        </a>
    </div>

    <!-- Vacancies Card -->
    <div class="cm-card">
        <div class="cm-feature-icon" style="width: 48px; height: 48px; font-size: var(--cm-font-size-xl); margin-bottom: var(--cm-spacing-3); background: linear-gradient(135deg, var(--cm-color-accent-success), var(--cm-color-accent-info));">
            <i class="fas fa-briefcase"></i>
        </div>
        <h3 class="cm-feature-title">Вакансии</h3>
        <p class="cm-feature-text" style="margin-bottom: var(--cm-spacing-4);">
            Найдите работу мечты среди наших партнеров
        </p>
        <a href="?action=vacancies" class="cm-btn cm-btn-ghost cm-btn-block">
            <i class="fas fa-search"></i>
            Смотреть вакансии
        </a>
    </div>
</div>

<!-- Recent Activity / Problems Table -->
<div class="cm-section-header">
    <h2 class="cm-section-title">Популярные задачи</h2>
    <a href="?action=contests" class="cm-btn cm-btn-ghost cm-btn-sm">
        Все задачи <i class="fas fa-arrow-right ml-2"></i>
    </a>
</div>

<div class="cm-table-container">
    <table class="cm-table cm-problem-table">
        <thead>
            <tr>
                <th class="cm-problem-status">Статус</th>
                <th class="cm-problem-id">#</th>
                <th class="cm-problem-title">Название</th>
                <th class="cm-problem-acceptance">Принято</th>
                <th class="cm-problem-difficulty">Сложность</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="cm-problem-status"></td>
                <td class="cm-problem-id">1</td>
                <td class="cm-problem-title">
                    <a href="#" class="cm-problem-title-link">Two Sum</a>
                </td>
                <td class="cm-problem-acceptance">48.2%</td>
                <td>
                    <span class="cm-badge cm-badge-easy">Easy</span>
                </td>
            </tr>
            <tr>
                <td class="cm-problem-status">
                    <span class="cm-solved-indicator cm-solved-medium"></span>
                </td>
                <td class="cm-problem-id">2</td>
                <td class="cm-problem-title">
                    <a href="#" class="cm-problem-title-link">Add Two Numbers</a>
                </td>
                <td class="cm-problem-acceptance">42.1%</td>
                <td>
                    <span class="cm-badge cm-badge-medium">Medium</span>
                </td>
            </tr>
            <tr>
                <td class="cm-problem-status"></td>
                <td class="cm-problem-id">3</td>
                <td class="cm-problem-title">
                    <a href="#" class="cm-problem-title-link">Longest Substring Without Repeating Characters</a>
                </td>
                <td class="cm-problem-acceptance">33.8%</td>
                <td>
                    <span class="cm-badge cm-badge-medium">Medium</span>
                </td>
            </tr>
            <tr>
                <td class="cm-problem-status">
                    <span class="cm-solved-indicator cm-solved-hard"></span>
                </td>
                <td class="cm-problem-id">4</td>
                <td class="cm-problem-title">
                    <a href="#" class="cm-problem-title-link">Median of Two Sorted Arrays</a>
                </td>
                <td class="cm-problem-acceptance">35.2%</td>
                <td>
                    <span class="cm-badge cm-badge-hard">Hard</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/page_template.php';
?>
