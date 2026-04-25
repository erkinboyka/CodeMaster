<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$chatMessages = $chatMessages ?? [];
$topUsers = $topUsers ?? [];
$recommendedCourses = $recommendedCourses ?? [];
$user['activities'] = $user['activities'] ?? [];
$shouldShowOnboarding = !empty($_SESSION['just_registered']);
if ($shouldShowOnboarding) {
    unset($_SESSION['just_registered']);
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= t('dashboard_page_title') ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Tailwind CSS & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    <script>
        const tfLocale = '<?= currentLang() === 'en' ? 'en-US' : (currentLang() === 'tg' ? 'tg-TJ' : 'ru-RU') ?>';
        const tfI18n = {
            aiTyping: '<?= t('dashboard_ai_typing') ?>',
            aiReplyDefault: '<?= t('dashboard_ai_reply_default') ?>',
            aiReplyError: '<?= t('dashboard_ai_error') ?>',
            connectionError: '<?= t('dashboard_connection_error') ?>',
            clearChatConfirm: '<?= t('dashboard_clear_chat_confirm') ?>',
            toggleHide: '<?= t('dashboard_toggle_hide') ?>',
            toggleShow: '<?= t('dashboard_toggle_show') ?>'
        };
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            50: "#f0f5ff",
                            100: "#e0e7ff",
                            200: "#c7d2fe",
                            300: "#a5b4fc",
                            400: "#818cf8",
                            500: "#6366f1",
                            600: "#4f46e5",
                            700: "#4338ca",
                            800: "#3730a3",
                            900: "#312e81",
                        },
                    },
                },
            },
        };
    </script>

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");

        html,
        body {
            font-family: "Inter", sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
            line-height: 1.6;
            margin: 0;
            max-width: 100%;
            overflow-x: hidden;
        }

        .fade-in {
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
            overflow: hidden;
        }

        .progress-bar {
            height: 6px;
            border-radius: 3px;
            background-color: #e0e7ff;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
            background-color: #4f46e5;
            transition: width 0.3s ease;
        }

        .skill-tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            margin: 4px;
            border-radius: 20px;
            background-color: #e0e7ff;
            color: #4338ca;
            font-size: 14px;
            transition: all 0.2s;
        }

        .skill-tag:hover {
            background-color: #c7d2fe;
            cursor: pointer;
        }

        .skill-tag.remove:hover {
            background-color: #fecaca;
            color: #b91c1c;
        }

        .chat-message {
            animation: fadeIn 0.3s ease;
        }

        .chat-bubble {
            max-width: min(75%, 42rem);
        }

        .chat-text {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 640px) {
            .chat-bubble {
                max-width: 92%;
            }

            .dashboard-quick-btn {
                white-space: normal;
                text-align: center;
            }
        }

        .course-card {
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12);
            border-color: #c7d2fe;
        }

        .course-cover {
            height: 160px;
            background: radial-gradient(circle at 20% 20%, #a5b4fc 0%, #4f46e5 40%, #0f172a 100%);
        }

        .course-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.6;
            mix-blend-mode: soft-light;
        }

        .course-card .progress-bar {
            height: 8px;
            border-radius: 999px;
            background-color: #e2e8f0;
        }

        .course-card .progress-fill {
            border-radius: 999px;
            background: linear-gradient(90deg, #4f46e5, #10b981);
        }

        body.ob-open {
            overflow: hidden;
        }

        .ob-overlay {
            position: fixed;
            inset: 0;
            z-index: 70;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            pointer-events: none;
        }

        .ob-overlay.hidden {
            display: none !important;
        }

        .ob-slide.hidden {
            display: none !important;
        }

        .ob-backdrop {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 15% 20%, rgba(99, 102, 241, 0.35), transparent 50%),
                radial-gradient(circle at 85% 80%, rgba(14, 165, 233, 0.25), transparent 55%),
                linear-gradient(140deg, rgba(2, 6, 23, 0.85), rgba(15, 23, 42, 0.9));
            backdrop-filter: blur(8px);
            z-index: 0;
            pointer-events: auto;
        }

        .ob-panel {
            position: relative;
            z-index: 1;
            width: min(1080px, 94vw);
            border-radius: 28px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 320px 1fr;
            box-shadow: 0 40px 120px rgba(2, 6, 23, 0.65);
            border: 1px solid rgba(148, 163, 184, 0.35);
            animation: obIn 0.5s ease;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.06), rgba(255, 255, 255, 0.95));
            pointer-events: auto;
        }

        .ob-left {
            padding: 28px 26px;
            color: #e2e8f0;
            background:
                radial-gradient(circle at 20% 20%, rgba(99, 102, 241, 0.7), transparent 55%),
                radial-gradient(circle at 70% 60%, rgba(14, 165, 233, 0.55), transparent 60%),
                #0b1120;
            display: flex;
            flex-direction: column;
            gap: 16px;
            position: relative;
            overflow: hidden;
        }

        .ob-left::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(148, 163, 184, 0.12) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, 0.12) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: 0.25;
            animation: obGrid 12s linear infinite;
        }

        .ob-left::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 30%, rgba(99, 102, 241, 0.35), transparent 55%);
            opacity: 0.6;
            mix-blend-mode: screen;
        }

        .ob-brand {
            font-size: 12px;
            letter-spacing: 0.4em;
            text-transform: uppercase;
            color: rgba(226, 232, 240, 0.8);
            position: relative;
            z-index: 1;
        }

        .ob-side-title {
            font-size: 22px;
            font-weight: 700;
            color: #f8fafc;
            position: relative;
            z-index: 1;
        }

        .ob-side-body {
            font-size: 13px;
            color: rgba(226, 232, 240, 0.8);
            line-height: 1.5;
            position: relative;
            z-index: 1;
        }

        .ob-steps {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            position: relative;
            z-index: 1;
        }

        .ob-step-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: rgba(226, 232, 240, 0.3);
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .ob-step-dot.active {
            background: #e2e8f0;
            transform: scale(1.2);
        }

        .ob-right {
            display: flex;
            flex-direction: column;
            background: #ffffff;
            color: #0f172a;
        }

        .ob-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 22px 28px 12px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.35);
            background: linear-gradient(120deg, rgba(99, 102, 241, 0.1), rgba(14, 165, 233, 0.08));
        }

        .ob-step-label {
            font-size: 11px;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: #64748b;
        }

        .ob-title {
            font-size: 22px;
            font-weight: 700;
            margin-top: 6px;
        }

        .ob-subtitle {
            font-size: 13px;
            color: #475569;
            margin-top: 6px;
        }

        .ob-close {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            transition: all 0.2s ease;
        }

        .ob-close:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .ob-content {
            padding: 24px 28px 8px;
            min-height: 280px;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .ob-content.is-fade {
            opacity: 0;
            transform: translateY(6px);
        }

        .ob-footer {
            padding: 16px 28px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .ob-progress {
            font-size: 12px;
            color: #64748b;
        }

        .ob-actions {
            display: flex;
            gap: 10px;
        }

        .ob-btn {
            padding: 8px 16px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .ob-btn.primary {
            background: #4f46e5;
            color: #ffffff;
            border-color: transparent;
        }

        .ob-btn.primary:hover {
            background: #4338ca;
        }

        .ob-btn.ghost:hover {
            background: #f1f5f9;
        }

        .ob-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .ob-card {
            border-radius: 18px;
            padding: 16px;
            border: 1px solid rgba(148, 163, 184, 0.4);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        }

        .ob-card .ob-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(99, 102, 241, 0.1);
            color: #4338ca;
        }

        .ob-list {
            margin-top: 10px;
            display: grid;
            gap: 8px;
            font-size: 13px;
            color: #475569;
        }

        .ob-preview {
            border-radius: 16px;
            padding: 14px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            background: linear-gradient(150deg, rgba(99, 102, 241, 0.12), rgba(255, 255, 255, 0.95));
        }

        .ob-preview h4 {
            font-size: 14px;
            font-weight: 700;
        }

        .ob-preview p {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        .ob-bars {
            margin-top: 10px;
            display: grid;
            gap: 6px;
        }

        .ob-bar {
            height: 6px;
            border-radius: 999px;
            background: rgba(99, 102, 241, 0.35);
        }

        .ob-bar.short {
            width: 60%;
        }

        @keyframes obIn {
            0% {
                opacity: 0;
                transform: translateY(16px) scale(0.98);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes obGrid {
            0% {
                background-position: 0 0, 0 0;
            }

            100% {
                background-position: 140px 140px, 140px 140px;
            }
        }

        .ob-progress-bar {
            height: 6px;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.3);
            overflow: hidden;
            min-width: 180px;
        }

        .ob-progress-bar span {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #6366f1, #38bdf8);
            transition: width 0.25s ease;
            width: 0%;
        }

        .ob-hero-title {
            font-size: 22px;
            font-weight: 700;
            margin-top: 6px;
        }

        .ob-hero-text {
            font-size: 13px;
            color: #475569;
            margin-top: 6px;
            line-height: 1.5;
        }

        .ob-timeline {
            display: grid;
            gap: 14px;
        }

        .ob-node {
            display: grid;
            grid-template-columns: 16px 1fr;
            gap: 12px;
            align-items: start;
        }

        .ob-node-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: #6366f1;
            box-shadow: 0 0 12px rgba(99, 102, 241, 0.6);
            margin-top: 4px;
        }

        .ob-node-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .ob-node-text {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        @media (max-width: 900px) {
            .ob-panel {
                grid-template-columns: 1fr;
            }

            .ob-left {
                padding: 20px 22px;
            }
        }

        .vacancy-card {
            transition: all 0.2s ease;
        }

        .vacancy-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .notification-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(79, 70, 229, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
            }
        }

        .skill-level {
            position: relative;
            height: 4px;
            background-color: #e0e7ff;
            border-radius: 2px;
            overflow: hidden;
        }

        .skill-level-fill {
            height: 100%;
            border-radius: 2px;
            background: linear-gradient(90deg, #4f46e5, #818cf8);
            transition: width 0.5s ease;
        }

        .user-card {
            transition: all 0.2s ease;
        }

        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .application-status {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-applied {
            background-color: #dbeafe;
            color: #3b82f6;
        }

        .status-interview {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-offer {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .rating-star {
            color: #facc15;
        }

        .search-highlight {
            background-color: #fde047;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #f3f4f6;
            color: #1f2937;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background-color: #e5e7eb;
        }

        .input-field {
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            width: 100%;
            transition: all 0.2s;
        }

        .input-field:focus {
            outline: none;
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.2);
        }

        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .tab-active {
            border-bottom: 2px solid #4f46e5;
            color: #4f46e5;
            font-weight: 600;
        }

        .tab-inactive {
            color: #6b7280;
            transition: all 0.2s;
        }

        .tab-inactive:hover {
            color: #4b5563;
        }

        .notification-item {
            transition: all 0.2s;
        }

        .notification-item:hover {
            background-color: #f9fafb;
        }

        .notification-item.unread {
            background-color: #f0f5ff;
            font-weight: 500;
        }

        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .video-player {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
        }

        .video-player iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 8px;
        }

        .admin-only {
            display: none;
        }

        .admin-controls {
            position: absolute;
            top: 8px;
            right: 8px;
            display: flex;
            gap: 4px;
        }

        .admin-btn {
            background-color: #f3f4f6;
            color: #4b5563;
            border-radius: 4px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .admin-btn:hover {
            background-color: #e5e7eb;
            color: #1f2937;
        }

        .admin-sidebar {
            background-color: #f9fafb;
            border-right: 1px solid #e5e7eb;
        }

        .quiz-timer {
            height: 4px;
            background-color: #e0e7ff;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .timer-fill {
            height: 100%;
            background-color: #4f46e5;
            width: 100%;
            transition: width 0.5s linear;
        }

        .quiz-question {
            animation: fadeIn 0.3s ease;
        }

        .quiz-feedback {
            margin-top: 16px;
            padding: 12px;
            border-radius: 8px;
        }

        .feedback-correct {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .feedback-incorrect {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .hint-button {
            background-color: #f3f4f6;
            color: #4b5563;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .hint-button:hover {
            background-color: #e5e7eb;
        }

        .password-strength {
            height: 4px;
            background-color: #e0e7ff;
            border-radius: 2px;
            margin-top: 4px;
        }

        .password-strength-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 0.3s ease;
        }
    </style>
    <!-- Стили для чата — вставить в <head> или в общий CSS -->
    <style>
        .chat-messages {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 20px 16px;
            background: #f7f8fc;
        }

        .msg-row {
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }

        .msg-row.user {
            flex-direction: row-reverse;
        }

        .msg-icon {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #6366f1, #818cf8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 11px;
            flex-shrink: 0;
        }

        .msg-bubble {
            max-width: 72%;
            padding: 10px 14px;
            font-size: 13.5px;
            line-height: 1.55;
            word-break: break-word;
            border-radius: 18px;
        }

        .msg-row.bot .msg-bubble {
            background: #fff;
            color: #1e1b4b;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 8px rgba(80, 80, 180, .07);
        }

        .msg-row.user .msg-bubble {
            background: linear-gradient(135deg, #6366f1, #818cf8);
            color: #fff;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
        }

        .msg-time {
            font-size: 10.5px;
            color: #b0b3c8;
            white-space: nowrap;
            margin-bottom: 2px;
            flex-shrink: 0;
        }

        .msg-row.user .msg-time {
            color: #a5b4fc;
        }

        /* Chips */
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 11px;
            border: 1.5px solid #e5e7f0;
            border-radius: 20px;
            font-size: 12px;
            color: #4b4f6e;
            background: #fff;
            cursor: pointer;
            transition: border-color .2s, background .2s, box-shadow .2s;
            white-space: nowrap;
        }

        .chip i {
            color: #6366f1;
            font-size: 11px;
        }

        .chip:hover {
            border-color: #6366f1;
            background: #eef0fb;
            box-shadow: 0 2px 8px rgba(99, 102, 241, .12);
        }
    </style>
</head>

<body class="bg-gray-50 m-0">
    <!-- Header -->
    <?php include 'includes/header.php'; ?>
    <!-- Main Content -->
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Profile Summary -->
                <div class="lg:col-span-1">
                    <div class="card overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 h-24"></div>
                        <div class="px-6 py-8 -mt-12 text-center">
                            <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="<?= t('dashboard_avatar_alt') ?>"
                                class="w-24 h-24 rounded-full mx-auto object-cover border-4 border-white" />
                            <h2 class="mt-4 text-xl font-bold text-gray-900"><?= htmlspecialchars($user['name']) ?></h2>
                            <p class="text-indigo-600 font-medium"><?= htmlspecialchars($user['title']) ?></p>
                            <p class="text-gray-500 text-sm mt-1 flex items-center justify-center">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <span><?= htmlspecialchars($user['location']) ?></span>
                            </p>
                            <div class="mt-6 flex justify-center flex-wrap gap-4">
                                <?php if (!empty($user['social_linkedin'])): ?>
                                    <a href="<?= htmlspecialchars($user['social_linkedin']) ?>" target="_blank"
                                        rel="noopener noreferrer" class="text-gray-400 hover:text-gray-700"
                                        title="LinkedIn">
                                        <span class="sr-only">LinkedIn</span>
                                        <i class="fab fa-linkedin-in text-xl"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($user['social_github'])): ?>
                                    <a href="<?= htmlspecialchars($user['social_github']) ?>" target="_blank"
                                        rel="noopener noreferrer" class="text-gray-400 hover:text-gray-700" title="GitHub">
                                        <span class="sr-only">GitHub</span>
                                        <i class="fab fa-github text-xl"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($user['social_telegram'])): ?>
                                    <a href="<?= htmlspecialchars($user['social_telegram']) ?>" target="_blank"
                                        rel="noopener noreferrer" class="text-gray-400 hover:text-gray-700"
                                        title="Telegram">
                                        <span class="sr-only">Telegram</span>
                                        <i class="fab fa-telegram-plane text-xl"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($user['social_website'])): ?>
                                    <a href="<?= htmlspecialchars($user['social_website']) ?>" target="_blank"
                                        rel="noopener noreferrer" class="text-gray-400 hover:text-gray-700"
                                        title="<?= t('profile_website', 'Website') ?>">
                                        <span class="sr-only"><?= t('profile_website', 'Website') ?></span>
                                        <i class="fas fa-globe text-xl"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 px-6 py-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-1"></i> <?= t('dashboard_skills') ?>
                            </h3>
                            <div class="flex flex-wrap">
                                <?php if (!empty($user['skills'])): ?>
                                    <?php foreach ($user['skills'] as $skill): ?>
                                        <div class="w-full mb-3">
                                            <div class="flex justify-between text-xs mb-1">
                                                <span><?= htmlspecialchars($skill['skill_name']) ?></span>
                                                <span><?= $skill['skill_level'] ?>%</span>
                                            </div>
                                            <div class="skill-level">
                                                <div class="skill-level-fill" style="width: <?= $skill['skill_level'] ?>%">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-gray-500 text-sm"><?= t('dashboard_skills_empty') ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 px-6 py-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-chart-line mr-1"></i> <?= t('dashboard_stats') ?>
                            </h3>
                            <?php
                            $pointsData = calculateUserPoints($user);
                            ?>
                            <div class="grid grid-cols-3 gap-2 text-center">
                                <div>
                                    <p class="text-2xl font-bold text-gray-900">
                                        <?= uiValue(count($user['experience'])) ?>
                                    </p>
                                    <p class="text-xs text-gray-500"><?= t('dashboard_exp') ?></p>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900">
                                        <?= uiValue(count($user['certificates'])) ?>
                                    </p>
                                    <p class="text-xs text-gray-500"><?= t('dashboard_certs') ?></p>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900">
                                        <?= uiValue(count($user['applications'])) ?>
                                    </p>
                                    <p class="text-xs text-gray-500"><?= t('dashboard_apps') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 px-6 py-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-briefcase mr-1"></i> <?= t('dashboard_recent_apps') ?>
                            </h3>
                            <?php if (!empty($user['applications'])): ?>
                                <div class="space-y-3">
                                    <?php foreach (array_slice($user['applications'], 0, 3) as $application): ?>
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium text-gray-900">
                                                    <?= htmlspecialchars($application['vacancy_title']) ?>
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    <?= htmlspecialchars($application['company']) ?>
                                                </p>
                                            </div>
                                            <span
                                                class="application-status <?= getApplicationStatusClass($application['status']) ?>">
                                                <?= getApplicationStatusText($application['status']) ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500 text-center py-2"><?= t('dashboard_apps_empty') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Activity Feed -->
                    <div class="card mt-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-history mr-2"></i> <?= t('dashboard_recent_activity') ?>
                            </h2>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <?php $dashboardActivities = array_slice((array) ($user['activities'] ?? []), 0, 3); ?>
                            <?php if (!empty($dashboardActivities)): ?>
                                <?php foreach ($dashboardActivities as $activity): ?>
                                    <div class="px-6 py-4">
                                        <?php
                                        $activityRawText = normalizeMojibakeText((string) ($activity['activity_text'] ?? ''));
                                        $activityTranslations = [
                                            'ru' => translateActivityMessageForLang($activityRawText, 'ru'),
                                            'en' => translateActivityMessageForLang($activityRawText, 'en'),
                                            'tg' => translateActivityMessageForLang($activityRawText, 'tg'),
                                        ];
                                        ?>
                                        <?php
                                        $activityLines = array_values(array_unique(array_filter($activityTranslations, function ($value) {
                                            return is_string($value) && trim($value) !== '';
                                        })));
                                        ?>
                                        <div class="text-sm text-gray-700 leading-snug">
                                            <?php foreach ($activityLines as $line): ?>
                                                <div><?= htmlspecialchars($line) ?></div>
                                            <?php endforeach; ?>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1 flex items-center">
                                            <i class="far fa-clock mr-1"></i>
                                            <span><?= formatDate($activity['activity_time']) ?></span>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="px-6 py-4 text-sm text-gray-500"><?= t('dashboard_activity_empty') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="px-6 py-3 bg-gray-50 text-center border-t border-gray-200">
                            <a href="?action=profile&tab=activity"
                                class="text-indigo-600 text-sm font-medium hover:text-indigo-900">
                                <?= t('dashboard_activity_all') ?>
                            </a>
                        </div>
                    </div>

                    <!-- Top Users -->
                    <div class="mt-8">
                        <div class="card overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <i class="fas fa-trophy mr-2 text-yellow-500"></i> <?= t('dashboard_top_users') ?>
                                </h2>
                                <a href="?action=ratings"
                                    class="text-indigo-600 text-sm font-medium hover:text-indigo-900">
                                    <?= t('dashboard_more') ?> <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                </a>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-3 gap-4">
                                    <?php if (!empty($topUsers)): ?>
                                        <?php foreach ($topUsers as $topUser): ?>
                                            <div class="text-center relative">
                                                <div class="absolute -top-2 left-1/2 transform -translate-x-1/2"
                                                    style="<?= $topUser['position'] === 1 ? 'color: #facc15;' : ($topUser['position'] === 2 ? 'color: #9ca3af;' : 'color: #f59e0b;') ?>">
                                                    <?php if ($topUser['position'] === 1): ?>
                                                        <i class="fas fa-crown text-xl"></i>
                                                    <?php elseif ($topUser['position'] === 2 || $topUser['position'] === 3): ?>
                                                        <i class="fas fa-medal text-xl"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <img src="<?= htmlspecialchars($topUser['avatar']) ?>" alt="User avatar"
                                                    class="w-16 h-16 rounded-full mx-auto mb-2">
                                                <p class="font-medium text-sm text-gray-900 truncate">
                                                    <?= htmlspecialchars($topUser['name']) ?>
                                                </p>
                                                <p class="text-xs text-gray-500">#<?= $topUser['position'] ?> &bull;
                                                    <?= (int) ($topUser['points'] ?? 0) ?>         <?= t('dashboard_points') ?>
                                                </p>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-span-3 text-sm text-gray-500 text-center">
                                            <?= t('dashboard_no_data') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Middle Column - Main Content -->
                <div class="lg:col-span-2">
                    <!-- Welcome Message -->
                    <div class="card p-6 mb-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">
                                    <?= t('dashboard_welcome') ?>,
                                    <span><?= htmlspecialchars(explode(' ', $user['name'])[0]) ?>!</span>
                                </h1>
                                <p class="text-gray-600 mt-2">
                                    <?= t('dashboard_welcome_subtitle') ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0 flex items-center gap-2">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-user-graduate mr-1"></i>
                                    <?= $user['role'] === 'seeker' ? t('role_seeker') : ($user['role'] === 'admin' ? t('role_admin') : t('role_recruiter')) ?>
                                </span>
                            </div>
                        </div>
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button onclick="window.location.href='?action=courses'"
                                class="flex items-center justify-center px-4 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                <i class="fas fa-graduation-cap mr-2"></i> <?= t('dashboard_cta_courses') ?>
                            </button>
                            <button onclick="window.location.href='?action=vacancies'"
                                class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-briefcase mr-2"></i> <?= t('dashboard_cta_vacancies') ?>
                            </button>
                        </div>
                    </div>

                    <!-- AI Assistant -->
                    <div id="ai-tutor" class="card overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">

                            <div class="flex items-center">
                                <div class="bg-indigo-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-robot text-indigo-600 text-xl"></i>
                                </div>
                                <h2 class="text-lg font-semibold text-gray-900"><?= t('dashboard_ai_title') ?></h2>
                                <button onclick="clearChat()" class="text-blue-600 hover:text-red-600 transition-colors"
                                    title="<?= t('dashboard_chat_clear') ?>" style="margin-left: auto;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                <?= t('dashboard_ai_subtitle') ?>
                            </p>

                            <!-- Область сообщений -->
                            <div class="h-96 overflow-y-auto p-0 bg-gray-50 chat-messages" id="chatMessages">
                                <?php foreach ($chatMessages as $message): ?>
                                    <div class="msg-row <?= $message['sender'] === 'user' ? 'user' : 'bot' ?>">

                                        <?php if ($message['sender'] !== 'user'): ?>
                                            <div class="msg-icon"><i class="fas fa-robot"></i></div>
                                        <?php endif; ?>

                                        <div class="msg-bubble">
                                            <?= htmlspecialchars($message['message_text'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>

                                        <span class="msg-time">
                                            <?= date('H:i', strtotime($message['sent_at'])) ?>
                                        </span>

                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Футер с инпутом и чипами -->
                            <div class="border-t border-gray-200 px-4 py-3 bg-white">
                                <div class="flex">
                                    <input type="text" id="chatInput"
                                        placeholder="<?= t('dashboard_chat_placeholder') ?>"
                                        class="flex-1 input-field focus:outline-none" />
                                    <button id="chatSendBtn" onclick="sendMessage()"
                                        class="ml-2 bg-indigo-600 text-white p-2 rounded-lg hover:bg-indigo-700">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>

                                <!-- Quick actions -->
                                <div class="mt-3 flex flex-col items-center">
                                    <!-- Primary chips -->
                                    <div class="flex flex-wrap justify-center gap-2 mb-2">
                                        <button
                                            onclick="askQuestion('<?= htmlspecialchars(t('dashboard_q_explain'), ENT_QUOTES, 'UTF-8') ?>')"
                                            class="chip">
                                            <i class="fas fa-book-open"></i> <?= t('dashboard_q_explain') ?>
                                        </button>
                                        <button
                                            onclick="askQuestion('<?= htmlspecialchars(t('dashboard_q_plan'), ENT_QUOTES, 'UTF-8') ?>')"
                                            class="chip">
                                            <i class="fas fa-calendar-alt"></i> <?= t('dashboard_q_plan') ?>
                                        </button>
                                        <button
                                            onclick="askQuestion('<?= htmlspecialchars(t('dashboard_q_quiz'), ENT_QUOTES, 'UTF-8') ?>')"
                                            class="chip">
                                            <i class="fas fa-question-circle"></i> <?= t('dashboard_q_quiz') ?>
                                        </button>
                                    </div>

                                    <!-- More chips -->
                                    <div id="moreButtons" class="flex flex-wrap justify-center gap-2 mb-2"
                                        style="display:none!important;">
                                        <button
                                            onclick="askQuestion('<?= htmlspecialchars(t('dashboard_q_vacancies'), ENT_QUOTES, 'UTF-8') ?>')"
                                            class="chip">
                                            <i class="fas fa-briefcase"></i> <?= t('dashboard_q_vacancies') ?>
                                        </button>
                                        <button
                                            onclick="askQuestion('<?= htmlspecialchars(t('dashboard_q_skills'), ENT_QUOTES, 'UTF-8') ?>')"
                                            class="chip">
                                            <i class="fas fa-chart-line"></i> <?= t('dashboard_q_skills') ?>
                                        </button>
                                        <button
                                            onclick="askQuestion('<?= htmlspecialchars(t('dashboard_q_interview'), ENT_QUOTES, 'UTF-8') ?>')"
                                            class="chip">
                                            <i class="fas fa-user-tie"></i> <?= t('dashboard_q_interview') ?>
                                        </button>
                                        <button
                                            onclick="askQuestion('<?= htmlspecialchars(t('dashboard_q_progress'), ENT_QUOTES, 'UTF-8') ?>')"
                                            class="chip">
                                            <i class="fas fa-tachometer-alt"></i> <?= t('dashboard_q_progress') ?>
                                        </button>
                                        <button
                                            onclick="askQuestion('<?= htmlspecialchars(t('dashboard_q_certificate'), ENT_QUOTES, 'UTF-8') ?>')"
                                            class="chip">
                                            <i class="fas fa-certificate"></i> <?= t('dashboard_q_certificate') ?>
                                        </button>
                                    </div>

                                    <!-- Toggle -->
                                    <button onclick="toggleMoreButtons()"
                                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 focus:outline-none">
                                        <span id="toggleButtonText"><?= t('dashboard_toggle_show') ?></span>
                                        <i class="fas fa-chevron-down transition-transform" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recommended Courses -->
                    <div class="card overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <i class="fas fa-book-reader mr-2"></i> <?= t('dashboard_recommended') ?>
                                </h2>
                                <a href="?action=courses"
                                    class="text-indigo-600 text-sm font-medium hover:text-indigo-900">
                                    <?= t('dashboard_view_all') ?>
                                </a>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <?php if (!empty($recommendedCourses)): ?>
                                    <?php foreach ($recommendedCourses as $course): ?>
                                        <div class="course-card card">
                                            <div class="course-cover">
                                                <img src="<?= htmlspecialchars($course['image_url']) ?>" alt="Course image"
                                                    class="w-full h-full object-cover" />
                                            </div>
                                            <div class="p-4">
                                                <div class="flex items-center mb-2">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        <?= htmlspecialchars($course['level']) ?>
                                                    </span>
                                                    <span class="ml-2 text-xs text-gray-500 flex items-center">
                                                        <i class="far fa-clock mr-1"></i>
                                                        <span><?= t('dashboard_lessons_few') ?></span>
                                                    </span>
                                                </div>
                                                <h3 class="font-semibold text-gray-900">
                                                    <?= htmlspecialchars($course['title']) ?>
                                                </h3>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    <?= htmlspecialchars($course['instructor']) ?>
                                                </p>
                                                <div class="mt-3">
                                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                                        <span><?= t('dashboard_progress') ?></span>
                                                        <span><?= (int) ($course['progress'] ?? 0) ?>%</span>
                                                    </div>
                                                    <div class="progress-bar">
                                                        <div class="progress-fill"
                                                            style="width: <?= (int) ($course['progress'] ?? 0) ?>%">
                                                        </div>
                                                    </div>
                                                </div>
                                                <button onclick="openCourse(<?= $course['id'] ?>)"
                                                    class="mt-4 w-full py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                                    <?= t('dashboard_continue') ?>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-sm text-gray-500"><?= t('dashboard_recommended_empty') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        </div>
    </main>

    <div id="onboarding-tour" class="ob-overlay hidden">
        <div id="onboardBackdrop" class="ob-backdrop" onclick="closeOnboarding()"></div>
        <div class="ob-panel">
            <div class="ob-left">
                <div class="ob-brand">CodeMaster</div>
                <div class="ob-side-title" id="onboardAsideTitle"><?= t('dashboard_onboard_welcome_title') ?></div>
                <div class="ob-side-body" id="onboardAsideBody"><?= t('dashboard_onboard_welcome_body') ?></div>
                <div class="ob-steps" id="onboardDots"></div>
            </div>
            <div class="ob-right">
                <div class="ob-top">
                    <div>
                        <div class="ob-step-label" id="onboardStep"></div>
                        <div class="ob-title" id="onboardTitle"></div>
                        <div class="ob-subtitle" id="onboardBody"></div>
                    </div>
                    <button id="onboardClose" type="button" class="ob-close" aria-label="<?= t('close') ?>" onclick="closeOnboarding()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="onboardContent" class="ob-content">
                    <div class="ob-slide" data-title="<?= htmlspecialchars(t('dashboard_onboard_welcome_title'), ENT_QUOTES, 'UTF-8') ?>"
                        data-body="<?= htmlspecialchars(t('dashboard_onboard_welcome_body'), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="ob-grid">
                            <div class="ob-card">
                                <div class="ob-chip"><i class="fas fa-sparkles"></i><?= t('dashboard_onboard_welcome_title') ?></div>
                                <div class="ob-hero-title"><?= t('dashboard_welcome') ?>, <?= htmlspecialchars(explode(' ', $user['name'])[0] ?? '', ENT_QUOTES, 'UTF-8') ?>!</div>
                                <div class="ob-hero-text"><?= t('dashboard_onboard_welcome_body') ?></div>
                                <div class="ob-hero-text">
                                    <?= t('dashboard_onboard_hint', 'Мы собрали твой маршрут, чтобы за 3-5 минут стало ясно: куда идти дальше.') ?>
                                </div>
                            </div>
                            <div class="ob-card">
                                <div class="ob-chip"><i class="fas fa-satellite-dish"></i><?= t('dashboard_onboard_signal', 'Сигналы платформы') ?></div>
                                <div class="ob-list">
                                    <div><?= t('dashboard_onboard_signal_1', 'Учёба, вакансии и рейтинг связаны в одну траекторию.') ?></div>
                                    <div><?= t('dashboard_onboard_signal_2', 'Профиль + навыки = сильный сигнал для рекрутера.') ?></div>
                                    <div><?= t('dashboard_onboard_signal_3', 'Чем больше активности — тем выше доверие в рейтингах.') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (($user['role'] ?? 'seeker') === 'recruiter'): ?>
                        <div class="ob-slide hidden"
                            data-title="<?= htmlspecialchars(t('dashboard_onboard_recruiter_title'), ENT_QUOTES, 'UTF-8') ?>"
                            data-body="<?= htmlspecialchars(t('dashboard_onboard_recruiter_body'), ENT_QUOTES, 'UTF-8') ?>">
                            <div class="ob-grid">
                                <div class="ob-card">
                                    <div class="ob-chip"><i class="fas fa-briefcase"></i><?= t('nav_vacancies') ?></div>
                                    <div class="ob-timeline">
                                        <div class="ob-node">
                                            <div class="ob-node-dot"></div>
                                            <div>
                                                <div class="ob-node-title"><?= t('dashboard_onboard_recruiter_step1', 'Создай вакансию') ?></div>
                                                <div class="ob-node-text"><?= t('dashboard_onboard_recruiter_step1_sub', 'Опиши роль, стек и формат работы.') ?></div>
                                            </div>
                                        </div>
                                        <div class="ob-node">
                                            <div class="ob-node-dot"></div>
                                            <div>
                                                <div class="ob-node-title"><?= t('dashboard_onboard_recruiter_step2', 'Отфильтруй кандидатов') ?></div>
                                                <div class="ob-node-text"><?= t('dashboard_onboard_recruiter_step2_sub', 'Навыки и прогресс в учёбе дают ранжирование.') ?></div>
                                            </div>
                                        </div>
                                        <div class="ob-node">
                                            <div class="ob-node-dot"></div>
                                            <div>
                                                <div class="ob-node-title"><?= t('dashboard_onboard_recruiter_step3', 'Пригласи на интервью') ?></div>
                                                <div class="ob-node-text"><?= t('dashboard_onboard_recruiter_step3_sub', 'Быстрый отклик + готовые шаблоны писем.') ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ob-card">
                                    <div class="ob-chip"><i class="fas fa-chart-line"></i><?= t('dashboard_onboard_preview_ratings_sub') ?></div>
                                    <div class="ob-list">
                                        <div><?= t('dashboard_onboard_recruiter_tip1', 'Лучшие кандидаты подсвечиваются по активности и подтверждённым навыкам.') ?></div>
                                        <div><?= t('dashboard_onboard_recruiter_tip2', 'История задач и курсов — живое портфолио.') ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="ob-slide hidden"
                            data-title="<?= htmlspecialchars(t('dashboard_onboard_levels_title'), ENT_QUOTES, 'UTF-8') ?>"
                            data-body="<?= htmlspecialchars(t('dashboard_onboard_levels_intro'), ENT_QUOTES, 'UTF-8') ?>">
                            <div class="ob-grid">
                                <div class="ob-card">
                                    <div class="ob-chip"><i class="fas fa-user-graduate"></i><?= t('dashboard_onboard_seeker_title') ?></div>
                                    <div class="ob-list">
                                        <div><?= t('dashboard_onboard_seeker_beginner') ?></div>
                                        <div><?= t('dashboard_onboard_seeker_mid') ?></div>
                                        <div><?= t('dashboard_onboard_seeker_pro') ?></div>
                                    </div>
                                </div>
                                <div class="ob-card">
                                    <div class="ob-chip"><i class="fas fa-route"></i><?= t('dashboard_onboard_path_title', 'Твоя траектория') ?></div>
                                    <div class="ob-timeline">
                                        <div class="ob-node">
                                            <div class="ob-node-dot"></div>
                                            <div>
                                                <div class="ob-node-title"><?= t('dashboard_onboard_path1', 'Учёба') ?></div>
                                                <div class="ob-node-text"><?= t('dashboard_onboard_preview_edu_sub') ?></div>
                                            </div>
                                        </div>
                                        <div class="ob-node">
                                            <div class="ob-node-dot"></div>
                                            <div>
                                                <div class="ob-node-title"><?= t('dashboard_onboard_path2', 'Профиль и навыки') ?></div>
                                                <div class="ob-node-text"><?= t('dashboard_onboard_path2_sub', 'Подтвердите ключевые навыки и добавьте портфолио.') ?></div>
                                            </div>
                                        </div>
                                        <div class="ob-node">
                                            <div class="ob-node-dot"></div>
                                            <div>
                                                <div class="ob-node-title"><?= t('dashboard_onboard_path3', 'Вакансии') ?></div>
                                                <div class="ob-node-text"><?= t('dashboard_onboard_preview_vacancies_sub') ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="ob-slide hidden"
                        data-title="<?= htmlspecialchars(t('dashboard_onboard_preview_title'), ENT_QUOTES, 'UTF-8') ?>"
                        data-body="<?= htmlspecialchars(t('dashboard_onboard_preview_body'), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="ob-grid">
                            <div class="ob-preview">
                                <h4><?= t('nav_education', 'Учёба') ?></h4>
                                <p><?= t('dashboard_onboard_preview_edu_sub') ?></p>
                                <div class="ob-bars">
                                    <div class="ob-bar"></div>
                                    <div class="ob-bar short"></div>
                                    <div class="ob-bar"></div>
                                </div>
                            </div>
                            <div class="ob-preview">
                                <h4><?= t('nav_contests', 'Контесты') ?></h4>
                                <p><?= t('dashboard_onboard_preview_contests_sub') ?></p>
                                <div class="ob-bars">
                                    <div class="ob-bar"></div>
                                    <div class="ob-bar short"></div>
                                    <div class="ob-bar"></div>
                                </div>
                            </div>
                            <div class="ob-preview">
                                <h4><?= t('nav_vacancies') ?></h4>
                                <p><?= t('dashboard_onboard_preview_vacancies_sub') ?></p>
                                <div class="ob-bars">
                                    <div class="ob-bar"></div>
                                    <div class="ob-bar short"></div>
                                    <div class="ob-bar"></div>
                                </div>
                            </div>
                            <div class="ob-preview">
                                <h4><?= t('nav_ratings') ?></h4>
                                <p><?= t('dashboard_onboard_preview_ratings_sub') ?></p>
                                <div class="ob-bars">
                                    <div class="ob-bar"></div>
                                    <div class="ob-bar short"></div>
                                    <div class="ob-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ob-slide hidden"
                        data-title="<?= htmlspecialchars(t('dashboard_onboard_next_title', 'Что дальше'), ENT_QUOTES, 'UTF-8') ?>"
                        data-body="<?= htmlspecialchars(t('dashboard_onboard_next_body', 'Выберите первый шаг — мы уже подготовили быстрые кнопки.'), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="ob-grid">
                            <div class="ob-card">
                                <div class="ob-chip"><i class="fas fa-graduation-cap"></i><?= t('nav_education', 'Учёба') ?></div>
                                <div class="ob-list">
                                    <div><?= t('dashboard_cta_courses') ?></div>
                                    <div><?= t('dashboard_onboard_preview_edu_sub') ?></div>
                                </div>
                                <div class="ob-actions" style="margin-top:12px;">
                                    <button type="button" class="ob-btn primary" onclick="window.location.href='?action=courses'"><?= t('dashboard_cta_courses') ?></button>
                                </div>
                            </div>
                            <div class="ob-card">
                                <div class="ob-chip"><i class="fas fa-briefcase"></i><?= t('nav_vacancies') ?></div>
                                <div class="ob-list">
                                    <div><?= t('dashboard_cta_vacancies') ?></div>
                                    <div><?= t('dashboard_onboard_preview_vacancies_sub') ?></div>
                                </div>
                                <div class="ob-actions" style="margin-top:12px;">
                                    <button type="button" class="ob-btn primary" onclick="window.location.href='?action=vacancies'"><?= t('dashboard_cta_vacancies') ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ob-footer">
                    <div>
                        <div class="ob-progress" id="onboardProgress"></div>
                        <div class="ob-progress-bar"><span id="onboardProgressBar"></span></div>
                    </div>
                    <div class="ob-actions">
                        <button id="onboardBack" type="button" class="ob-btn ghost" onclick="backOnboarding()"><?= t('dashboard_onboard_btn_prev', 'Назад') ?></button>
                        <button id="onboardNext" type="button" class="ob-btn primary" onclick="advanceOnboarding()"><?= t('dashboard_onboard_btn_next', 'Далее') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php
    $footerContext = 'dashboard';
    include 'includes/footer.php';
    ?>
    <script>
        function escapeHtml(text) {
            return text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderMarkdown(text) {
            const safe = escapeHtml(text);
            const withBreaks = safe.replace(/\n/g, '<br>');
            const bold = withBreaks.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            const italic = bold.replace(/\*(.*?)\*/g, '<em>$1</em>');
            return italic;
        }

        function appendChatMessage(text, sender, timeText) {
            const container = document.getElementById('chatMessages');
            const wrapper = document.createElement('div');
            wrapper.className = sender === 'user' ? 'text-right mb-4' : 'mb-4';

            const bubble = document.createElement('div');
            bubble.className = sender === 'user'
                ? 'bg-indigo-600 text-white rounded-l-lg rounded-tr-lg ml-auto chat-bubble inline-block px-4 py-3 mb-1 shadow-sm'
                : 'bg-white rounded-r-lg rounded-tl-lg mr-auto chat-bubble inline-block px-4 py-3 mb-1 shadow-sm';

            const html = sender === 'ai' ? renderMarkdown(text) : escapeHtml(text);
            bubble.innerHTML = `<p class="chat-text" data-sender="${sender}">${html}</p>`;

            const time = document.createElement('span');
            time.className = sender === 'user' ? 'text-indigo-500 text-xs' : 'text-gray-500 text-xs';
            time.textContent = timeText || '';

            wrapper.appendChild(bubble);
            wrapper.appendChild(time);
            container.appendChild(wrapper);
            container.scrollTop = container.scrollHeight;
        }

        function showTypingIndicator() {
            const container = document.getElementById('chatMessages');
            const wrapper = document.createElement('div');
            wrapper.id = 'chatTyping';
            wrapper.className = 'mb-4';
            wrapper.innerHTML = `
                <div class="bg-white rounded-r-lg rounded-tl-lg mr-auto inline-block px-4 py-3 mb-1 shadow-sm text-gray-500 text-sm">
                    ${tfI18n.aiTyping}
                </div>
            `;
            container.appendChild(wrapper);
            container.scrollTop = container.scrollHeight;
        }

        function removeTypingIndicator() {
            const el = document.getElementById('chatTyping');
            if (el) el.remove();
        }

        function nowTime() {
            const d = new Date();
            return d.toLocaleTimeString(tfLocale, { hour: '2-digit', minute: '2-digit' });
        }

        function sendMessage() {
            const input = document.getElementById('chatInput');
            const button = document.getElementById('chatSendBtn');
            const message = input.value.trim();
            if (!message) return;

            input.value = '';
            if (button) button.disabled = true;
            appendChatMessage(message, 'user', nowTime());
            showTypingIndicator();

            fetch('?action=chat-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ message: message })
            })
                .then(response => response.json())
                .then(data => {
                    removeTypingIndicator();
                    if (data.success) {
                        const aiText = data.aiResponse || tfI18n.aiReplyDefault;
                        appendChatMessage(aiText, 'ai', nowTime());
                    } else {
                        tfNotify(data.message || tfI18n.aiReplyError);
                    }
                })
                .catch(() => {
                    removeTypingIndicator();
                    tfNotify(tfI18n.connectionError);
                })
                .finally(() => {
                    if (button) button.disabled = false;
                });
        }

        async function clearChat() {
            const ok = await tfConfirm(tfI18n.clearChatConfirm);
            if (!ok) return;
            fetch('?action=clear-chat', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(() => {
                    const container = document.getElementById('chatMessages');
                    if (container) container.innerHTML = '';
                });
        }

        function askQuestion(question) {
            document.getElementById('chatInput').value = question;
            setTimeout(sendMessage, 100);
        }

        function toggleMoreButtons() {
            const moreButtons = document.getElementById('moreButtons');
            const toggleText = document.getElementById('toggleButtonText');
            const toggleIcon = document.getElementById('toggleIcon');

            if (moreButtons.style.display === 'none') {
                moreButtons.style.display = 'flex';
                toggleText.textContent = tfI18n.toggleHide;
                toggleIcon.className = 'fas fa-chevron-up';
            } else {
                moreButtons.style.display = 'none';
                toggleText.textContent = tfI18n.toggleShow;
                toggleIcon.className = 'fas fa-chevron-down';
            }
        }

        function openCourse(courseId) {
            window.location.href = '?action=get-course&id=' + courseId;
        }

        const userId = <?= (int) ($user['id'] ?? 0) ?>;
        const onboardKey = `itsphere360_onboard_done_${userId}`;
        const isNewRegistration = <?= $shouldShowOnboarding ? 'true' : 'false' ?>;
        const onboardSlides = Array.from(document.querySelectorAll('#onboardContent .ob-slide'));

        function renderOnboardingStep(idx) {
            const total = onboardSlides.length;
            const step = onboardSlides[idx];
            if (!step) return;
            onboardSlides.forEach((slide, slideIndex) => {
                slide.classList.toggle('hidden', slideIndex !== idx);
            });
            document.getElementById('onboardStep').textContent = '<?= t('dashboard_onboard_step_label', 'Шаг {current} из {total}') ?>'
                .replace('{current}', String(idx + 1))
                .replace('{total}', String(total));
            document.getElementById('onboardTitle').textContent = step.dataset.title || '';
            document.getElementById('onboardBody').textContent = step.dataset.body || '';
            const asideTitle = document.getElementById('onboardAsideTitle');
            const asideBody = document.getElementById('onboardAsideBody');
            if (asideTitle) asideTitle.textContent = step.dataset.title || '';
            if (asideBody) asideBody.textContent = step.dataset.body || '';
            document.querySelectorAll('.ob-step-dot').forEach((dot) => {
                const stepIndex = parseInt(dot.dataset.step || '0', 10);
                dot.classList.toggle('active', stepIndex === idx);
            });
            const progress = document.getElementById('onboardProgress');
            progress.textContent = `${idx + 1}/${total}`;
            const progressBar = document.getElementById('onboardProgressBar');
            if (progressBar) {
                progressBar.style.width = `${((idx + 1) / total) * 100}%`;
            }
            const nextBtn = document.getElementById('onboardNext');
            const backBtn = document.getElementById('onboardBack');
            if (backBtn) {
                backBtn.disabled = idx === 0;
                backBtn.classList.toggle('opacity-50', idx === 0);
            }
            if (nextBtn) {
                nextBtn.textContent = idx === total - 1 ? '<?= t('dashboard_onboard_btn_finish', 'Готово') ?>' : '<?= t('dashboard_onboard_btn_next', 'Далее') ?>';
            }
        }

        function buildOnboardDots() {
            const dots = document.getElementById('onboardDots');
            if (!dots) return;
            dots.innerHTML = '';
            for (let i = 0; i < onboardSlides.length; i += 1) {
                const dot = document.createElement('span');
                dot.className = 'ob-step-dot';
                dot.dataset.step = String(i);
                dots.appendChild(dot);
            }
        }

        function openOnboarding(force = false) {
            if (!force && localStorage.getItem(onboardKey) === '1') return;
            const overlay = document.getElementById('onboarding-tour');
            if (!overlay) return;
            buildOnboardDots();
            overlay.classList.remove('hidden');
            document.body.classList.add('ob-open');
            overlay.dataset.step = '0';
            renderOnboardingStep(0);
        }

        function closeOnboarding() {
            const overlay = document.getElementById('onboarding-tour');
            if (!overlay) return;
            overlay.classList.add('hidden');
            document.body.classList.remove('ob-open');
            localStorage.setItem(onboardKey, '1');
        }

        function advanceOnboarding() {
            const overlay = document.getElementById('onboarding-tour');
            if (!overlay) return;
            const current = parseInt(overlay.dataset.step || '0', 10);
            const next = current + 1;
            if (next >= onboardSlides.length) {
                closeOnboarding();
                return;
            }
            overlay.dataset.step = String(next);
            renderOnboardingStep(next);
        }

        function backOnboarding() {
            const overlay = document.getElementById('onboarding-tour');
            if (!overlay) return;
            const current = parseInt(overlay.dataset.step || '0', 10);
            const prev = Math.max(0, current - 1);
            overlay.dataset.step = String(prev);
            renderOnboardingStep(prev);
        }

        document.getElementById('onboardClose')?.addEventListener('click', closeOnboarding);
        document.getElementById('onboardBackdrop')?.addEventListener('click', closeOnboarding);

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.chat-text').forEach((el) => {
                if (el.dataset.sender === 'ai') {
                    el.innerHTML = renderMarkdown(el.textContent || '');
                }
            });
            const completed = localStorage.getItem(onboardKey) === '1';
            if (isNewRegistration && !completed) {
                openOnboarding(true);
                localStorage.setItem(onboardKey, '1');
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeOnboarding();
            }
        });
    </script>
</body>

</html>
