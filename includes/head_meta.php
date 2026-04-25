<?php
$tfBasePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$tfFaviconFile = __DIR__ . '/../favicon.svg';
$tfFaviconVersion = (is_file($tfFaviconFile) && is_readable($tfFaviconFile)) ? (string) filemtime($tfFaviconFile) : '1';
$tfFaviconHref = (($tfBasePath === '' ? '' : $tfBasePath) . '/favicon.svg') . '?v=' . $tfFaviconVersion;
$tfIcoFile = __DIR__ . '/../favicon.ico';
$tfIcoVersion = (is_file($tfIcoFile) && is_readable($tfIcoFile)) ? (string) filemtime($tfIcoFile) : $tfFaviconVersion;
$tfFaviconIcoHref = (($tfBasePath === '' ? '' : $tfBasePath) . '/favicon.ico') . '?v=' . $tfIcoVersion;
$tfAnimationsCssFile = __DIR__ . '/animations.css';
$tfAnimationsJsFile = __DIR__ . '/animations.js';
$tfThemeCssFile = __DIR__ . '/theme.css';
$tfUiTokensFile = __DIR__ . '/../ui/tokens.css';
$tfUiLayoutFile = __DIR__ . '/../layout/layout.css';
$tfUiComponentsFile = __DIR__ . '/../components/components.css';
$tfUiFormsFile = __DIR__ . '/../forms/forms.css';
$tfUiTablesFile = __DIR__ . '/../tables/tables.css';
$tfUiA11yFile = __DIR__ . '/../ui/accessibility.css';
$tfAnimationsCssVersion = (is_file($tfAnimationsCssFile) && is_readable($tfAnimationsCssFile)) ? (string) filemtime($tfAnimationsCssFile) : '1';
$tfAnimationsJsVersion = (is_file($tfAnimationsJsFile) && is_readable($tfAnimationsJsFile)) ? (string) filemtime($tfAnimationsJsFile) : '1';
$tfAnimationsCssHref = (($tfBasePath === '' ? '' : $tfBasePath) . '/includes/animations.css') . '?v=' . $tfAnimationsCssVersion;
$tfAnimationsJsHref = (($tfBasePath === '' ? '' : $tfBasePath) . '/includes/animations.js') . '?v=' . $tfAnimationsJsVersion;
$tfThemeCssVersion = (is_file($tfThemeCssFile) && is_readable($tfThemeCssFile)) ? (string) filemtime($tfThemeCssFile) : '1';
$tfUiControllerJsFile = __DIR__ . '/ui-controller.js';
$tfUiControllerJsVersion = (is_file($tfUiControllerJsFile) && is_readable($tfUiControllerJsFile)) ? (string) filemtime($tfUiControllerJsFile) : '1';
$tfUiControllerJsHref = (($tfBasePath === '' ? '' : $tfBasePath) . '/includes/ui-controller.js') . '?v=' . $tfUiControllerJsVersion;
$tfUiTokensVersion = (is_file($tfUiTokensFile) && is_readable($tfUiTokensFile)) ? (string) filemtime($tfUiTokensFile) : '1';
$tfUiLayoutVersion = (is_file($tfUiLayoutFile) && is_readable($tfUiLayoutFile)) ? (string) filemtime($tfUiLayoutFile) : '1';
$tfUiComponentsVersion = (is_file($tfUiComponentsFile) && is_readable($tfUiComponentsFile)) ? (string) filemtime($tfUiComponentsFile) : '1';
$tfUiFormsVersion = (is_file($tfUiFormsFile) && is_readable($tfUiFormsFile)) ? (string) filemtime($tfUiFormsFile) : '1';
$tfUiTablesVersion = (is_file($tfUiTablesFile) && is_readable($tfUiTablesFile)) ? (string) filemtime($tfUiTablesFile) : '1';
$tfUiA11yVersion = (is_file($tfUiA11yFile) && is_readable($tfUiA11yFile)) ? (string) filemtime($tfUiA11yFile) : '1';
$tfThemeCssHref = (($tfBasePath === '' ? '' : $tfBasePath) . '/includes/theme.css') . '?v=' . $tfThemeCssVersion;
$tfUiTokensHref = (($tfBasePath === '' ? '' : $tfBasePath) . '/ui/tokens.css') . '?v=' . $tfUiTokensVersion;
$tfUiLayoutHref = (($tfBasePath === '' ? '' : $tfBasePath) . '/layout/layout.css') . '?v=' . $tfUiLayoutVersion;
$tfUiComponentsHref = (($tfBasePath === '' ? '' : $tfBasePath) . '/components/components.css') . '?v=' . $tfUiComponentsVersion;
$tfUiFormsHref = (($tfBasePath === '' ? '' : $tfBasePath) . '/forms/forms.css') . '?v=' . $tfUiFormsVersion;
$tfUiTablesHref = (($tfBasePath === '' ? '' : $tfBasePath) . '/tables/tables.css') . '?v=' . $tfUiTablesVersion;
$tfUiA11yHref = (($tfBasePath === '' ? '' : $tfBasePath) . '/ui/accessibility.css') . '?v=' . $tfUiA11yVersion;
$tfAction = trim((string) ($_GET['action'] ?? 'home'));
$tfNeedsTinyMce = in_array($tfAction, ['admin'], true);

// Theme detection - support both dark and light themes
$preferredTheme = isset($_COOKIE['cm-theme']) ? $_COOKIE['cm-theme'] : 'dark';
?>
<link rel="icon" href="<?= htmlspecialchars($tfFaviconHref) ?>" type="image/svg+xml" sizes="any">
<link rel="icon" href="<?= htmlspecialchars($tfFaviconIcoHref) ?>" type="image/x-icon">
<link rel="shortcut icon" href="<?= htmlspecialchars($tfFaviconIcoHref) ?>">
<link rel="apple-touch-icon" href="<?= htmlspecialchars($tfFaviconHref) ?>">
<meta name="theme-color" content="<?= $preferredTheme === 'light' ? '#f8fafc' : '#0f172a' ?>">
<meta name="msapplication-TileColor" content="<?= $preferredTheme === 'light' ? '#f8fafc' : '#0f172a' ?>">
<meta name="color-scheme" content="<?= $preferredTheme ?>">

<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="//cdn.jsdelivr.net">
<link rel="dns-prefetch" href="//cdn.tiny.cloud">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="preload" href="<?= htmlspecialchars($tfUiTokensHref) ?>" as="style">
<link rel="preload" href="<?= htmlspecialchars($tfUiLayoutHref) ?>" as="style">
<link rel="preload" href="<?= htmlspecialchars($tfUiComponentsHref) ?>" as="style">
<link rel="preload" href="<?= htmlspecialchars($tfThemeCssHref) ?>" as="style">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
<noscript>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
</noscript>
<link rel="stylesheet" href="<?= htmlspecialchars($tfUiTokensHref) ?>">
<link rel="stylesheet" href="<?= htmlspecialchars($tfUiLayoutHref) ?>">
<link rel="stylesheet" href="<?= htmlspecialchars($tfUiComponentsHref) ?>">
<link rel="stylesheet" href="<?= htmlspecialchars($tfUiFormsHref) ?>">
<link rel="stylesheet" href="<?= htmlspecialchars($tfUiTablesHref) ?>">
<link rel="stylesheet" href="<?= htmlspecialchars($tfUiA11yHref) ?>">
<link rel="stylesheet" href="<?= htmlspecialchars($tfThemeCssHref) ?>">
<link rel='stylesheet' href='<?= htmlspecialchars($tfAnimationsCssHref) ?>'>
<script src='<?= htmlspecialchars($tfAnimationsJsHref) ?>' defer></script>
<script src='<?= htmlspecialchars($tfUiControllerJsHref) ?>' defer></script>
<?php if ($tfNeedsTinyMce): ?>
    <link rel="preconnect" href="https://cdn.tiny.cloud" crossorigin>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin" defer></script>
<?php endif; ?>
