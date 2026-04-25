# CodeMaster Modern UI System

## LeetCode/ElectiCode Style Redesign

### Overview
This project has been updated with a modern UI/UX system inspired by LeetCode and ElectiCode platforms. The new design features:

- **Dark/Light Theme Support** - Automatic theme detection with manual toggle
- **Modern CSS Variables** - Consistent design tokens across the platform
- **Responsive Design** - Mobile-first approach with tablet and desktop optimizations
- **Smooth Animations** - Subtle transitions and hover effects
- **Accessible Components** - WCAG compliant color contrast and focus states

### Files Added/Modified

#### New Files
1. `/includes/modern-ui.css` - Complete modern UI component library
2. `/includes/ui-controller.js` - JavaScript controller for theme, sidebar, notifications
3. `/includes/header_modern.php` - Modern header with responsive navigation
4. `/includes/footer_modern.php` - Modern footer with social links
5. `/includes/page_template.php` - Reusable page template

#### Modified Files
1. `/includes/head_meta.php` - Updated to include modern UI assets and Inter/JetBrains Mono fonts
2. `/ui/tokens.css` - Enhanced design tokens

### Usage

#### Using the Modern Template
```php
<?php
$pageTitle = 'My Page';
$content = '<h1>Hello World</h1>';
require_once __DIR__ . '/includes/page_template.php';
?>
```

#### Manual Integration
```php
<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <?php require_once __DIR__ . '/includes/head_meta.php'; ?>
</head>
<body>
    <?php require_once __DIR__ . '/includes/header_modern.php'; ?>
    
    <main style="padding-top:var(--header-height);min-height:100vh;">
        <!-- Your content here -->
    </main>
    
    <?php require_once __DIR__ . '/includes/footer_modern.php'; ?>
</body>
</html>
```

### Components

#### Buttons
```html
<button class="cm-btn cm-btn-primary">Primary</button>
<button class="cm-btn cm-btn-secondary">Secondary</button>
<button class="cm-btn cm-btn-outline">Outline</button>
<button class="cm-btn cm-btn-sm">Small</button>
<button class="cm-btn cm-btn-lg">Large</button>
```

#### Cards
```html
<div class="cm-card">
    <div class="cm-card-header">
        <h3 class="cm-card-title">Card Title</h3>
    </div>
    <div class="cm-card-body">
        Card content goes here...
    </div>
</div>
```

#### Badges
```html
<span class="cm-badge cm-badge-easy">Easy</span>
<span class="cm-badge cm-badge-medium">Medium</span>
<span class="cm-badge cm-badge-hard">Hard</span>
<span class="cm-badge cm-badge-new">New</span>
```

#### Theme Toggle
```html
<button class="cm-theme-toggle" onclick="cmToggleTheme()">
    <i class="fas fa-sun" data-theme-icon></i>
</button>
```

#### Notifications (JavaScript)
```javascript
cmShowNotification('Success!', 'success');
cmShowNotification('Error occurred', 'error');
cmShowNotification('Warning message', 'warning');
cmShowNotification('Info message', 'info');
```

#### Copy to Clipboard
```javascript
cmCopyToClipboard('Text to copy', 'Copied!');
```

### CSS Variables

#### Colors (Dark Theme Default)
- `--bg-primary`: #1a1a1a
- `--bg-secondary`: #252525
- `--accent-primary`: #00bfa5 (teal)
- `--accent-secondary`: #2962ff (blue)

#### Light Theme
Automatically applied when `[data-theme="light"]` is set on the HTML element.

### Responsive Breakpoints
- Mobile: < 768px
- Tablet: 768px - 1023px
- Desktop: ≥ 1024px

### Keyboard Shortcuts
- `Alt+T`: Toggle theme
- `Escape`: Close sidebar/modal

### Browser Support
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

### Migration Guide

For existing pages using Tailwind CDN:

1. Remove Tailwind CDN script from your pages
2. Add the modern UI stylesheet via `head_meta.php`
3. Replace Tailwind classes with `cm-*` classes
4. Use the new header/footer components

Example conversion:
```html
<!-- Before (Tailwind) -->
<div class="bg-gray-800 text-white p-4 rounded-lg">

<!-- After (Modern UI) -->
<div class="cm-card">
```

### Credits
Design inspired by:
- LeetCode (leetcode.com)
- ElectiCode (electicode.com)
- Modern web design best practices
