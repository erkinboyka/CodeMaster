<?php
/**
 * CodeMaster Modern Page Template
 * LeetCode/ElectiCode Style
 */

$pageTitle = $pageTitle ?? 'CodeMaster';
$pageDescription = $pageDescription ?? 'Современная платформа для обучения программированию';
$hideHeader = $hideHeader ?? false;
$hideFooter = $hideFooter ?? false;
?>
<!DOCTYPE html>
<html lang="<?= currentLang() ?? 'ru' ?>" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    
    <?php require_once __DIR__ . '/head_meta.php'; ?>
    
    <!-- Additional page styles -->
    <style>
        .cm-block-tablet { display: none; }
        .cm-block-desktop { display: none; }
        
        @media (min-width: 768px) {
            .cm-block-tablet { display: flex; }
            .cm-block-tablet-none { display: none !important; }
        }
        
        @media (min-width: 1024px) {
            .cm-block-desktop { display: inline-flex; }
        }
        
        .hidden { display: none !important; }
        
        .cm-nav-link.active {
            color: var(--accent-primary);
            background: rgba(0, 191, 165, 0.1);
        }
        
        .cm-sidebar-link.active {
            color: var(--accent-primary);
            background: rgba(0, 191, 165, 0.1);
        }
    </style>
</head>
<body>
    <?php if (!$hideHeader): ?>
        <?php require_once __DIR__ . '/header_modern.php'; ?>
    <?php endif; ?>
    
    <main class="cm-main-content" style="padding-top:var(--header-height);min-height:100vh;">
        <?= $content ?? '' ?>
    </main>
    
    <?php if (!$hideFooter): ?>
        <?php require_once __DIR__ . '/footer_modern.php'; ?>
    <?php endif; ?>
</body>
</html>
