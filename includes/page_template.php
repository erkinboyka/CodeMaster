<?php
/**
 * CodeMaster Platform - Modern Page Template
 * LeetCode/ElectiCode Style with Dark/Light Theme Support
 * 
 * Usage: Include this file in any template to get modern UI
 */

if (!defined('APP_INIT')) {
    die('Direct access not allowed');
}

$pageTitle = $pageTitle ?? 'CodeMaster';
$pageDescription = $pageDescription ?? 'Платформа для изучения программирования';
$showHeader = $showHeader ?? true;
$showFooter = $showFooter ?? true;
$containerClass = $containerClass ?? '';
?>
<!DOCTYPE html>
<html lang="<?= currentLang() ?>" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <title><?= htmlspecialchars($pageTitle) ?> - CodeMaster</title>
    
    <?php include __DIR__ . '/head_meta.php'; ?>
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.10/dist/cdn.min.js" defer></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="cm-body">
    <!-- Skip Link for Accessibility -->
    <a href="#main-content" class="cm-skip-link">Перейти к основному контенту</a>

    <?php if ($showHeader): ?>
        <!-- Modern Header -->
        <?php include __DIR__ . '/header_modern.php'; ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main id="main-content" class="cm-main <?= htmlspecialchars($containerClass) ?>">
        <div class="cm-container">
            <?= $content ?? '' ?>
        </div>
    </main>

    <?php if ($showFooter): ?>
        <!-- Modern Footer -->
        <?php include __DIR__ . '/footer_modern.php'; ?>
    <?php endif; ?>

    <!-- Notifications Container -->
    <div id="cm-notifications"></div>

    <!-- CSRF Token -->
    <?php include __DIR__ . '/csrf.php'; ?>

    <!-- AI Tutor Modal -->
    <?php include __DIR__ . '/ai_tutor_modal.php'; ?>
</body>
</html>
