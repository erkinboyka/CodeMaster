<?php
$countryOptions = require __DIR__ . '/../includes/countries.php';
$countryFlag = static function (string $code): string {
    $code = strtoupper(trim($code));
    if (strlen($code) !== 2) {
        return '';
    }
    $offset = 127397;
    return mb_chr($offset + ord($code[0]), 'UTF-8') . mb_chr($offset + ord($code[1]), 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('login_title') ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            min-height: 100svh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .auth-container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-image {
            flex: 1;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .auth-image::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: backgroundMove 20s linear infinite;
        }

        @keyframes backgroundMove {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(50px, 50px);
            }
        }

        .auth-image-content {
            text-align: center;
            max-width: 350px;
            position: relative;
            z-index: 1;
        }

        .auth-image-content i {
            font-size: 5.5rem;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .auth-image-content h2 {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .auth-image-content p {
            font-size: 1.05rem;
            opacity: 0.95;
            line-height: 1.7;
        }

        .auth-form {
            flex: 1.2;
            padding: 3rem 2.5rem;
            overflow-y: auto;
            overflow-x: hidden;
            max-height: 95vh;
        }

        .auth-form::-webkit-scrollbar {
            width: 6px;
        }

        .auth-form::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .auth-form::-webkit-scrollbar-thumb {
            background: #4f46e5;
            border-radius: 3px;
        }

        .lang-switcher {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 1.5rem;
        }

        .lang-switcher select {
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            background: white;
            transition: all 0.2s ease;
        }

        .lang-switcher select:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
        }

        .auth-form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-form-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .auth-form-header p {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .auth-form-group {
            margin-bottom: 1.25rem;
        }

        .auth-form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .auth-form-group input:not([type="checkbox"]),
        .auth-form-group select {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background: white;
        }

        .auth-form-group input:not([type="checkbox"]):focus,
        .auth-form-group select:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .auth-form-group input:not([type="checkbox"]):hover,
        .auth-form-group select:hover {
            border-color: #c7d2fe;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle input:not([type="checkbox"]) {
            padding-right: 3rem;
        }

        .password-toggle button {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0;
            width: 1.75rem;
            height: 1.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: color 0.2s ease;
            z-index: 2;
        }

        .password-toggle button,
        .password-toggle button.tf-interactive,
        .password-toggle button:hover,
        .password-toggle button.tf-interactive:hover,
        .password-toggle button:active,
        .password-toggle button.tf-interactive:active {
            transform: translateY(-50%) !important;
            box-shadow: none !important;
        }

        .password-toggle button.tf-sheen::after {
            display: none !important;
        }

        .password-toggle button:hover {
            color: #4f46e5;
        }

        .password-toggle button:focus-visible {
            outline: none;
            color: #4f46e5;
        }

        .country-picker {
            position: relative;
        }

        .country-picker-toggle {
            width: 100%;
            min-height: 44px;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            cursor: pointer;
            background: #fff;
        }

        .country-picker-toggle .country-picker-value {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            min-width: 0;
        }

        .country-flag-icon {
            width: 22px;
            height: 16px;
            flex: 0 0 auto;
            border-radius: 3px;
            object-fit: cover;
            box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.08);
            background: #f3f4f6;
        }

        .country-picker-toggle .country-picker-label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .country-picker-menu {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 0.5rem);
            z-index: 40;
            max-height: 18rem;
            overflow: auto;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            background: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
        }

        .hidden {
            display: none !important;
        }

        .country-picker-option {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.65rem 0.75rem;
            border: 0;
            border-radius: 0.6rem;
            background: transparent;
            color: #0f172a;
            text-align: left;
            cursor: pointer;
        }

        .country-picker-option:hover {
            background: #f8fafc;
        }

        .country-picker-option-flag {
            width: 20px;
            height: 14px;
            flex: 0 0 auto;
            border-radius: 2px;
            object-fit: cover;
            background: #f3f4f6;
        }

        .auth-form-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .recaptcha-wrap {
            margin-top: 1rem;
            margin-bottom: 0.25rem;
        }

        .social-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #9ca3af;
            font-size: 0.8rem;
            margin: 1rem 0 0.75rem;
        }

        .social-divider::before,
        .social-divider::after {
            content: '';
            height: 1px;
            background: #e5e7eb;
            flex: 1;
        }

        .google-auth {
            display: flex;
            justify-content: center;
            min-height: 44px;
        }

        .remember-me {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
            cursor: pointer;
            user-select: none;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #4f46e5;
        }

        .section-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: #1f2937;
            margin: 1.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .input-field {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .input-field:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            border: none;
            padding: 0.875rem 2rem;
            font-size: 0.875rem;
            letter-spacing: 0.3px;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-primary:hover{
            color: #4f46e5;
            box-shadow: #7c3aed;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        .btn-primary:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-secondary {
            background-color: #f3f4f6;
            color: #374151;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary:hover:not(:disabled) {
            background-color: #e5e7eb;
            border-color: #d1d5db;
        }

        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .auth-footer a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .auth-footer a:hover {
            color: #4338ca;
            text-decoration: underline;
        }

        .password-strength {
            margin-top: 0.75rem;
        }

        .password-tools {
            margin-top: 0.65rem;
            display: flex;
            justify-content: flex-end;
        }

        .password-generate-btn {
            border: 1px solid #c7d2fe;
            background: #eef2ff;
            color: #3730a3;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .password-generate-btn:hover:not(:disabled) {
            background: #e0e7ff;
            border-color: #a5b4fc;
        }

        .password-generate-btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .password-strength-label {
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .password-strength-bar {
            height: 6px;
            background-color: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
        }

        .password-strength-fill {
            height: 100%;
            transition: width 0.3s ease, background-color 0.3s ease;
            border-radius: 3px;
        }

        .error-message,
        .success-message {
            padding: 1rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-message {
            background-color: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .error-message i {
            color: #ef4444;
        }

        .success-message {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .success-message i {
            color: #10b981;
        }

        .tabs {
            display: flex;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e5e7eb;
            gap: 0.5rem;
        }

        .tab {
            padding: 1rem 2rem;
            cursor: pointer;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease;
            border-radius: 8px 8px 0 0;
            position: relative;
            bottom: -2px;
        }

        .tab:hover {
            color: #4f46e5;
            background: #f9fafb;
        }

        .tab.active {
            color: #4f46e5;
            border-bottom-color: #4f46e5;
            background: transparent;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 9999;
            padding: 24px;
            overflow-y: auto;
            backdrop-filter: blur(4px);
        }

        .modal-overlay.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            max-width: 760px;
            width: 100%;
            background: white;
            border-radius: 16px;
            padding: 2rem;
            position: relative;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            animation: modalSlideUp 0.3s ease;
        }

        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #f3f4f6;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            color: #6b7280;
        }

        .modal-close:hover {
            background: #e5e7eb;
            color: #374151;
            transform: rotate(90deg);
        }

        .modal-header {
            margin-bottom: 1.5rem;
        }

        .modal-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .modal-header p {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .modal-body {
            max-height: 60vh;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #4f46e5;
            border-radius: 3px;
        }

        .quiz-question {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f9fafb;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            transition: all 0.2s ease;
        }

        .quiz-question:hover {
            border-color: #c7d2fe;
        }

        .quiz-question-title {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 1rem;
            color: #1f2937;
            line-height: 1.5;
        }

        .quiz-option {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            margin: 0.75rem 0;
            padding: 0.875rem 1rem;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .quiz-option:hover {
            border-color: #4f46e5;
            background: #eef2ff;
        }

        .quiz-option input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #4f46e5;
        }

        .quiz-option label {
            cursor: pointer;
            margin: 0;
            flex: 1;
            color: #374151;
            font-size: 0.875rem;
        }

        .modal-footer {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 0;
            }

            .auth-container {
                flex-direction: column;
                border-radius: 0;
                min-height: 100vh;
                min-height: 100svh;
                min-height: 100dvh;
                height: 100%;
            }

            .auth-image {
                padding: 2rem 1.5rem;
                min-height: 200px;
            }

            .auth-image-content i {
                font-size: 4rem;
            }

            .auth-image-content h2 {
                font-size: 1.75rem;
            }

            .auth-form {
                padding: 2rem 1.5rem;
                max-height: none;
            }

            .form-grid-2 {
                grid-template-columns: 1fr;
            }

            .tabs {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0;
            }

            .tab {
                padding: 1rem;
                font-size: 0.875rem;
                text-align: center;
            }

            .modal-content {
                margin: 1rem;
                padding: 1.5rem;
            }

            .auth-form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .auth-form-header h1 {
                font-size: 1.5rem;
            }

            .lang-switcher {
                justify-content: center;
            }
        }
    </style>
    <?php if (defined('RECAPTCHA_SITE_KEY') && RECAPTCHA_SITE_KEY !== ''): ?>
        <script src="https://www.google.com/recaptcha/api.js?onload=tfOnRecaptchaLoaded&render=explicit" defer></script>
    <?php endif; ?>
    <?php if (defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID !== ''): ?>
        <script src="https://accounts.google.com/gsi/client" async defer></script>
    <?php endif; ?>
</head>

<body>
<?php include 'includes/csrf.php'; ?>
    <div class="auth-container">
        <div class="auth-image">
            <div class="auth-image-content">
                <i class="fas fa-graduation-cap"></i>
                <h2>CodeMaster</h2>
                <p><?= t('login_hero_text') ?></p>
            </div>
        </div>

        <div class="auth-form">
            <div class="lang-switcher">
                <?php $lang = currentLang(); ?>
                <label for="login-lang-select" class="sr-only"><?= t('label_language') ?></label>
                <select id="login-lang-select" onchange="window.location.href=this.value">
                    <option value="<?= htmlspecialchars(langUrl('ru')) ?>" <?= $lang === 'ru' ? 'selected' : '' ?>>
                        <?= t('lang_ru') ?>
                    </option>
                    <option value="<?= htmlspecialchars(langUrl('en')) ?>" <?= $lang === 'en' ? 'selected' : '' ?>>
                        <?= t('lang_en') ?>
                    </option>
                    <option value="<?= htmlspecialchars(langUrl('tg')) ?>" <?= $lang === 'tg' ? 'selected' : '' ?>>
                        <?= t('lang_tg') ?>
                    </option>
                </select>
            </div>

            <div class="auth-form-header">
                <h1><?= t('login_welcome') ?></h1>
                <p><?= t('login_subtitle') ?></p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($_GET['error']) ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars($_GET['success']) ?></span>
                </div>
            <?php endif; ?>

            <div class="tabs">
                <div class="tab active" onclick="switchTab('login', event)"><?= t('tab_login') ?></div>
                <div class="tab" onclick="switchTab('register', event)"><?= t('tab_register') ?></div>
            </div>

            <div id="login-tab" class="tab-content active">
                <form id="loginForm" onsubmit="event.preventDefault(); handleLogin()">
                    <div class="auth-form-group">
                        <label for="login-email"><?= t('label_email') ?></label>
                        <input type="email" id="login-email" required placeholder="<?= t('placeholder_email') ?>">
                    </div>

                    <div class="auth-form-group">
                        <label for="login-password"><?= t('label_password') ?></label>
                        <div class="password-toggle">
                            <input type="password" id="login-password" required
                                placeholder="<?= t('placeholder_password') ?>">
                            <button type="button" data-tf-ripple="off"
                                onclick="togglePassword('login-password', event)"
                                aria-label="Toggle password visibility">
                                <i class="fas fa-eye" id="login-password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="recaptcha-wrap" id="login-recaptcha-wrap">
                        <div id="login-recaptcha"></div>
                    </div>

                    <div class="auth-form-actions">
                        <label class="remember-me">
                            <input type="checkbox" id="remember-me">
                            <span><?= t('label_remember') ?></span>
                        </label>
                        <button type="submit" class="btn btn-primary" id="loginBtn">
                            <?= t('btn_sign_in') ?>
                        </button>
                    </div>

                    <div class="js-google-auth-wrap">
                        <div class="social-divider"><?= t('login_or_continue', 'OR') ?></div>
                        <div class="google-auth js-google-signin-button"></div>
                    </div>
                </form>
            </div>

            <div id="register-tab" class="tab-content">
                <form id="registerForm" onsubmit="event.preventDefault(); handleRegister()">
                    <div class="form-grid-2">
                        <div class="auth-form-group">
                            <label for="register-name"><?= t('label_name') ?></label>
                            <input class="input-field" type="text" id="register-name" required
                                placeholder="<?= t('placeholder_name') ?>">
                        </div>
                        <div class="auth-form-group">
                            <label for="register-email"><?= t('label_email') ?></label>
                            <input class="input-field" type="email" id="register-email" required
                                placeholder="<?= t('placeholder_email') ?>">
                        </div>
                    </div>

                    <div class="auth-form-group">
                        <label for="register-password"><?= t('label_password') ?></label>
                        <div class="password-toggle">
                            <input class="input-field" type="password" id="register-password" required
                                placeholder="<?= t('placeholder_password') ?>" oninput="checkPasswordStrength()">
                            <button type="button" data-tf-ripple="off"
                                onclick="togglePassword('register-password', event)"
                                aria-label="Toggle password visibility">
                                <i class="fas fa-eye" id="register-password-icon"></i>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-label">
                                <?= t('strength_label') ?>: <span id="strength-text"><?= t('strength_weak') ?></span>
                            </div>
                            <div class="password-strength-bar">
                                <div class="password-strength-fill" id="password-strength-fill" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="password-tools">
                            <button type="button" class="password-generate-btn" id="generatePasswordBtn"
                                onclick="generateStrongPassword()">
                                <?= t('btn_generate_password', 'Сгенерировать надежный пароль') ?>
                            </button>
                        </div>
                    </div>

                    <div class="auth-form-group">
                        <label for="register-confirm-password"><?= t('label_confirm_password') ?></label>
                        <div class="password-toggle">
                            <input class="input-field" type="password" id="register-confirm-password" required
                                placeholder="<?= t('label_confirm_password') ?>">
                            <button type="button" data-tf-ripple="off"
                                onclick="togglePassword('register-confirm-password', event)"
                                aria-label="Toggle password visibility">
                                <i class="fas fa-eye" id="register-confirm-password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="section-title"><?= t('section_profile') ?></div>
                    <div class="form-grid-2">
                        <div class="auth-form-group">
                            <label for="register-role"><?= t('label_role') ?></label>
                            <select id="register-role" class="input-field">
                                <option value="seeker"><?= t('role_seeker') ?></option>
                                <option value="recruiter"><?= t('role_recruiter') ?></option>
                            </select>
                        </div>
                        <div class="auth-form-group">
                            <label for="register-country"><?= t('label_country', 'Страна проживания') ?></label>
                            <input type="hidden" id="register-country" value="">
                            <input type="hidden" id="register-country-name" value="">
                            <div class="country-picker">
                                <button type="button" id="register-country-button"
                                    class="input-field country-picker-toggle" aria-haspopup="listbox"
                                    aria-expanded="false">
                                    <span class="country-picker-value">
                                        <img id="register-country-button-flag" class="country-flag-icon"
                                            src="https://flagcdn.com/w20/un.png" alt="" loading="lazy">
                                        <span id="register-country-button-label" class="country-picker-label">
                                            <?= t('placeholder_country', 'Выберите страну') ?>
                                        </span>
                                    </span>
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </button>
                                <div id="register-country-menu" class="country-picker-menu hidden" role="listbox">
                                    <?php foreach ($countryOptions as $code => $countryName): ?>
                                        <?php $code = strtoupper(trim((string) $code)); ?>
                                        <?php $flagSrc = 'https://flagcdn.com/w20/' . strtolower($code) . '.png'; ?>
                                        <button type="button" class="country-picker-option"
                                            data-country-code="<?= htmlspecialchars($code) ?>"
                                            data-country-name="<?= htmlspecialchars($countryName) ?>"
                                            data-country-flag-src="<?= htmlspecialchars($flagSrc) ?>">
                                            <img class="country-picker-option-flag"
                                                src="<?= htmlspecialchars($flagSrc) ?>" alt="" loading="lazy">
                                            <span><?= htmlspecialchars($countryName) ?></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="recaptcha-wrap" id="register-recaptcha-wrap">
                        <div id="register-recaptcha"></div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="registerBtn"
                        style="width: 100%; margin-top: 1rem;">
                        <?= t('btn_register') ?>
                    </button>

                    <div class="js-google-auth-wrap">
                        <div class="social-divider"><?= t('login_or_continue', 'OR') ?></div>
                        <div class="google-auth js-google-signin-button"></div>
                    </div>
                </form>
            </div>

            <div class="auth-footer">
                <p><?= t('footer_prefix') ?> <a href="?action=terms"><?= t('footer_terms') ?></a>
                    <?= t('footer_and', '') ?> <a href="?action=privacy"><?= t('footer_privacy') ?></a></p>
            </div>
        </div>
    </div>

        <script>
        const tfRecaptchaSiteKey = <?= tfSafeJson((defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const tfGoogleClientId = <?= tfSafeJson((defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

        let loginRecaptchaWidgetId = null;
        let registerRecaptchaWidgetId = null;
        let googleInitialized = false;

        function tfOnRecaptchaLoaded() {
            if (!tfRecaptchaSiteKey || !window.grecaptcha) return;
            if (document.getElementById('login-recaptcha')) {
                loginRecaptchaWidgetId = grecaptcha.render('login-recaptcha', { sitekey: tfRecaptchaSiteKey });
            }
            if (document.getElementById('register-recaptcha')) {
                registerRecaptchaWidgetId = grecaptcha.render('register-recaptcha', { sitekey: tfRecaptchaSiteKey });
            }
        }
        window.tfOnRecaptchaLoaded = tfOnRecaptchaLoaded;

        function getRecaptchaToken(kind) {
            if (!tfRecaptchaSiteKey) return '';
            if (!window.grecaptcha) return '';

            const widgetId = kind === 'register' ? registerRecaptchaWidgetId : loginRecaptchaWidgetId;
            if (widgetId === null) return '';
            return grecaptcha.getResponse(widgetId);
        }

        function resetRecaptcha(kind) {
            if (!tfRecaptchaSiteKey) return;
            if (!window.grecaptcha) return;
            const widgetId = kind === 'register' ? registerRecaptchaWidgetId : loginRecaptchaWidgetId;
            if (widgetId !== null) {
                grecaptcha.reset(widgetId);
            }
        }

        function initGoogleSignIn() {
            if (googleInitialized) return;
            if (!tfGoogleClientId) return;
            if (!window.google || !google.accounts || !google.accounts.id) return;
            const buttonContainers = Array.from(document.querySelectorAll('.js-google-signin-button'));
            if (buttonContainers.length === 0) return;

            google.accounts.id.initialize({
                client_id: tfGoogleClientId,
                callback: handleGoogleSignIn
            });
            buttonContainers.forEach(buttonContainer => {
                buttonContainer.innerHTML = '';
                google.accounts.id.renderButton(buttonContainer, {
                    type: 'standard',
                    shape: 'pill',
                    theme: 'outline',
                    text: 'signin_with',
                    size: 'large',
                    width: 280
                });
            });
            googleInitialized = true;
        }

        async function checkSessionAndRedirect() {
            try {
                const sessionResponse = await fetch('?action=session-status', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!sessionResponse.ok) return false;
                const sessionData = await sessionResponse.json();
                if (sessionData && sessionData.authenticated) {
                    window.location.href = '?action=dashboard';
                    return true;
                }
            } catch (error) {
                console.error('Session check failed:', error);
            }
            return false;
        }

        async function handleGoogleSignIn(response) {
            if (!response || !response.credential) {
                tfNotify('Google credential is missing');
                return;
            }

            try {
                const loginResponse = await fetch('?action=google-login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        credential: response.credential,
                        browserLocale: navigator.language || (navigator.languages && navigator.languages[0]) || ''
                    })
                });

                const raw = await loginResponse.text();
                let data = null;
                try {
                    data = raw ? JSON.parse(raw) : null;
                } catch (parseError) {
                    console.error('Google login response is not valid JSON:', parseError, raw);
                }

                if (loginResponse.ok && data && data.success) {
                    window.location.href = '?action=dashboard';
                    return;
                }

                const redirected = await checkSessionAndRedirect();
                if (redirected) {
                    return;
                }

                if (data && data.message) {
                    tfNotify(data.message);
                    return;
                }

                tfNotify(loginResponse.ok ? 'Google sign-in error' : '<?= t('js_server_error') ?>');
            } catch (error) {
                console.error('Google login error:', error);
                const redirected = await checkSessionAndRedirect();
                if (!redirected) {
                    tfNotify('<?= t('js_connection_error') ?>');
                }
            }
        }

        function initRegisterCountryPicker() {
            const button = document.getElementById('register-country-button');
            const menu = document.getElementById('register-country-menu');
            const codeInput = document.getElementById('register-country');
            const nameInput = document.getElementById('register-country-name');
            const flagLabel = document.getElementById('register-country-button-flag');
            const textLabel = document.getElementById('register-country-button-label');
            if (!button || !menu || !codeInput || !nameInput || !flagLabel || !textLabel) {
                return;
            }

            const getOption = (code) => {
                const safeCode = String(code || '').trim().toUpperCase();
                return menu.querySelector(`.country-picker-option[data-country-code="${safeCode}"]`);
            };

            const closeMenu = () => {
                menu.classList.add('hidden');
                button.setAttribute('aria-expanded', 'false');
            };

            const openMenu = () => {
                menu.classList.remove('hidden');
                button.setAttribute('aria-expanded', 'true');
            };

            const setSelection = (code, name, flag) => {
                const safeCode = String(code || '').trim().toUpperCase();
                const safeName = String(name || '').trim();
                const safeFlag = String(flag || '').trim();
                codeInput.value = safeCode;
                nameInput.value = safeName;
                flagLabel.src = safeFlag || (safeCode ? `https://flagcdn.com/w20/${safeCode.toLowerCase()}.png` : 'https://flagcdn.com/w20/un.png');
                flagLabel.alt = safeName || safeCode || '';
                textLabel.textContent = safeName || '<?= t('placeholder_country', 'Выберите страну') ?>';
                button.dataset.selectedCountry = safeCode;
                button.dataset.selectedCountryName = safeName;
                if (safeCode) {
                    button.classList.add('has-value');
                } else {
                    button.classList.remove('has-value');
                }
            };

            const syncFromInputs = () => {
                const currentCode = codeInput.value.trim().toUpperCase();
                const option = currentCode ? getOption(currentCode) : null;
                if (option) {
                    setSelection(
                        option.dataset.countryCode || '',
                        option.dataset.countryName || '',
                        option.dataset.countryFlagSrc || ''
                    );
                } else {
                    setSelection('', '', '');
                }
            };

            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                if (menu.classList.contains('hidden')) {
                    openMenu();
                } else {
                    closeMenu();
                }
            });

            menu.querySelectorAll('.country-picker-option').forEach(option => {
                option.addEventListener('click', () => {
                    setSelection(
                        option.dataset.countryCode || '',
                        option.dataset.countryName || '',
                        option.dataset.countryFlagSrc || ''
                    );
                    closeMenu();
                });
            });

            document.addEventListener('click', (event) => {
                if (!button.contains(event.target) && !menu.contains(event.target)) {
                    closeMenu();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeMenu();
                }
            });

            syncFromInputs();
        }

        document.addEventListener('DOMContentLoaded', () => {
            initRegisterCountryPicker();
            if (!tfRecaptchaSiteKey) {
                const wraps = [
                    document.getElementById('login-recaptcha-wrap'),
                    document.getElementById('register-recaptcha-wrap')
                ];
                wraps.forEach(el => {
                    if (el) el.style.display = 'none';
                });
            }
            if (!tfGoogleClientId) {
                document.querySelectorAll('.js-google-auth-wrap').forEach(wrap => {
                    wrap.style.display = 'none';
                });
            } else {
                let attempts = 0;
                const maxAttempts = 30;
                const start = () => {
                    initGoogleSignIn();
                    attempts += 1;
                    if (!googleInitialized && attempts < maxAttempts) {
                        setTimeout(start, 200);
                    } else if (!googleInitialized) {
                        console.warn('Google Sign-In SDK failed to initialize.');
                    }
                };
                start();
            }
        });

        function switchTab(tabName, evt) {
            // Remove active class from all tabs and content
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            // Add active class to clicked tab
            const e = evt || window.event || null;
            const clickedTab = e && e.target ? e.target.closest('.tab') : null;
            if (clickedTab) {
                clickedTab.classList.add('active');
            }

            // Show corresponding content
            const targetContent = document.getElementById(tabName + '-tab');
            if (targetContent) {
                targetContent.classList.add('active');
            }
        }

        function togglePassword(inputId, evt) {
            if (evt && typeof evt.preventDefault === 'function') evt.preventDefault();
            if (evt && typeof evt.stopPropagation === 'function') evt.stopPropagation();
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');

            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById('register-password').value;
            const strengthFill = document.getElementById('password-strength-fill');
            const strengthText = document.getElementById('strength-text');
            const strengthLabels = {
                weak: '<?= t('strength_weak') ?>',
                medium: '<?= t('strength_medium') ?>',
                good: '<?= t('strength_good') ?>',
                strong: '<?= t('strength_strong') ?>'
            };

            if (!password) {
                strengthFill.style.width = '0%';
                strengthText.textContent = strengthLabels.weak;
                return;
            }

            let strength = 0;

            // Length criteria
            if (password.length >= 8) strength += 25;
            if (password.length >= 12) strength += 25;

            // Character variety
            if (/[a-z]/.test(password)) strength += 12.5;
            if (/[A-Z]/.test(password)) strength += 12.5;
            if (/[0-9]/.test(password)) strength += 12.5;
            if (/[^a-zA-Z0-9]/.test(password)) strength += 12.5;

            strengthFill.style.width = strength + '%';

            if (strength < 25) {
                strengthFill.style.backgroundColor = '#ef4444';
                strengthText.textContent = strengthLabels.weak;
            } else if (strength < 50) {
                strengthFill.style.backgroundColor = '#f59e0b';
                strengthText.textContent = strengthLabels.medium;
            } else if (strength < 75) {
                strengthFill.style.backgroundColor = '#eab308';
                strengthText.textContent = strengthLabels.good;
            } else {
                strengthFill.style.backgroundColor = '#22c55e';
                strengthText.textContent = strengthLabels.strong;
            }
        }

        function generateStrongPassword() {
            const btn = document.getElementById('generatePasswordBtn');
            const passwordInput = document.getElementById('register-password');
            const confirmInput = document.getElementById('register-confirm-password');
            if (!btn || !passwordInput || !confirmInput) return;

            const initialLabel = btn.textContent;
            btn.disabled = true;
            btn.textContent = '<?= t('btn_generate_password_loading', 'Генерация...') ?>';

            fetch('?action=generate-password', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('<?= t('js_server_error') ?>');
                    return response.text();
                })
                .then(text => {
                    const cleaned = (text || '').replace(/^\uFEFF/, '').trim();
                    if (!cleaned) throw new Error('<?= t('js_connection_error') ?>');
                    const data = JSON.parse(cleaned);
                    if (!data.success || !data.password) {
                        throw new Error(data.message || '<?= t('js_registration_error') ?>');
                    }

                    passwordInput.value = data.password;
                    confirmInput.value = data.password;
                    checkPasswordStrength();
                    tfNotify('<?= t('password_generated_success', 'Надежный пароль сгенерирован') ?>');
                })
                .catch(error => {
                    console.error('Generate password error:', error);
                    tfNotify('<?= t('password_generated_error', 'Не удалось сгенерировать пароль') ?>');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.textContent = initialLabel;
                });
        }

        function handleLogin() {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            const loginBtn = document.getElementById('loginBtn');
            const recaptchaToken = getRecaptchaToken('login');

            if (!email || !password) {
                tfNotify('<?= t('js_fill_all_fields') ?>');
                return;
            }

            if (tfRecaptchaSiteKey && !recaptchaToken) {
                tfNotify('Complete reCAPTCHA verification.');
                return;
            }

            loginBtn.disabled = true;
            loginBtn.innerHTML = `<span class="loading-spinner"></span> <?= t('login_loading_signin') ?>`;

            fetch('?action=login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ email: email, password: password, recaptchaToken: recaptchaToken })
            })
                .then(response => {
                    if (!response.ok) throw new Error('<?= t('js_server_error') ?>');
                    return response.text();
                })
                .then(text => {
                    const cleaned = (text || '').replace(/^\uFEFF/, '').trim();
                    if (!cleaned) throw new Error('<?= t('js_connection_error') ?>');
                    let data;
                    try {
                        data = JSON.parse(cleaned);
                    } catch (e) {
                        console.error('Login JSON parse failed:', cleaned);
                        throw new Error('<?= t('js_connection_error') ?>');
                    }
                    if (data.success) {
                        window.location.href = '?action=dashboard';
                    } else {
                        tfNotify(data.message || '<?= t('js_login_error') ?>');
                        loginBtn.disabled = false;
                        loginBtn.textContent = '<?= t('btn_sign_in') ?>';
                        resetRecaptcha('login');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    tfNotify('<?= t('js_connection_error') ?>');
                    loginBtn.disabled = false;
                    loginBtn.textContent = '<?= t('btn_sign_in') ?>';
                    resetRecaptcha('login');
                });
        }

        function handleRegister() {
            const name = document.getElementById('register-name').value.trim();
            const email = document.getElementById('register-email').value.trim();
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm-password').value;
            const role = document.getElementById('register-role').value;
            const country = document.getElementById('register-country').value.trim().toUpperCase();
            const countryName = document.getElementById('register-country-name').value.trim();
            const recaptchaToken = getRecaptchaToken('register');

            // Validation
            if (!name || !email || !password || !confirmPassword || !country) {
                tfNotify('<?= t('js_fill_required_fields') ?>');
                return;
            }

            if (password !== confirmPassword) {
                tfNotify('<?= t('js_password_mismatch') ?>');
                return;
            }

            if (password.length < 8) {
                tfNotify('<?= t('js_password_short') ?>');
                return;
            }

            if (tfRecaptchaSiteKey && !recaptchaToken) {
                tfNotify('Complete reCAPTCHA verification.');
                return;
            }

            const payload = {
                fullName: name,
                email: email,
                password: password,
                role: role,
                countryCode: country,
                countryName: countryName,
                location: countryName || country,
                recaptchaToken: recaptchaToken
            };

            submitRegister(payload);
        }

        function submitRegister(payload) {
            const registerBtn = document.getElementById('registerBtn');
            registerBtn.disabled = true;
            registerBtn.innerHTML = `<span class="loading-spinner"></span> <?= t('login_loading_register') ?>`;

            fetch('?action=register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'index.php?action=dashboard';
                    } else {
                        tfNotify(data.message || '<?= t('js_registration_error') ?>');
                        registerBtn.disabled = false;
                        registerBtn.textContent = '<?= t('btn_register') ?>';
                        resetRecaptcha('register');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    tfNotify('<?= t('js_connection_error') ?>');
                    registerBtn.disabled = false;
                    registerBtn.textContent = '<?= t('btn_register') ?>';
                    resetRecaptcha('register');
                });
        }
    </script>
    <?php include 'includes/notifications.php'; ?>
</body>

</html>


