<?php 
if (!defined('APP_INIT')) define('APP_INIT', true); 

if (!defined('APP_INIT')) {
    die('Direct access not permitted');
}

if (!function_exists('t')) {
    function t($key, $default) { return $default; }
}
if (!function_exists('tfSafeJson')) {
    function tfSafeJson($value, $options = 0) {
        return json_encode($value, $options | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }
}
if (!function_exists('currentLang')) {
    function currentLang() { return 'ru'; }
}
if (session_status() === PHP_SESSION_ACTIVE && empty($_SESSION['csrf_token'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } catch (Throwable $e) {
        $_SESSION['csrf_token'] = sha1(uniqid('csrf', true));
    }
}

$session = is_array($session ?? null) ? $session : [];
$sessionCode = (string) ($session['code'] ?? ($_GET['code'] ?? 'test-session-123'));
$sessionTitle = (string) ($session['title'] ?? t('interview_default_title', 'Interview'));
$currentUser = $user ?? (function_exists('getCurrentUser') ? getCurrentUser() : []);
$currentUserId = (int) ($currentUser['id'] ?? 0);
$currentUserRole = 'member';
foreach ((array) ($session['participants'] ?? []) as $participant) {
    if ((int) ($participant['id'] ?? 0) === $currentUserId) {
        $currentUserRole = (string) ($participant['role'] ?? 'member');
        break;
    }
}
$initialInterviewSession = [
    'code' => $sessionCode,
    'title' => $sessionTitle,
    'participants' => array_values((array) ($session['participants'] ?? [])),
    'messages' => array_values((array) ($session['messages'] ?? [])),
    'remaining_seconds' => (int) ($session['remaining_seconds'] ?? 0),
    'is_running' => (int) ($session['is_running'] ?? 0),
    'code_snapshot' => (string) ($session['code_snapshot'] ?? ''),
    'boards_snapshot' => is_array($session['boards_snapshot'] ?? null) ? $session['boards_snapshot'] : null,
    'current_user_role' => $currentUserRole,
];
$interviewProblems = [];
if (function_exists('tfGetInterviewProblemsDataRich')) {
    $interviewProblems = (array) tfGetInterviewProblemsDataRich();
} elseif (function_exists('tfGetInterviewProblemsData')) {
    $interviewProblems = (array) tfGetInterviewProblemsData();
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(function_exists('currentLang') ? currentLang() : 'ru') ?>">
<head>
    <?php 
    if (file_exists(__DIR__ . '/../includes/head_meta.php')) {
        include __DIR__ . '/../includes/head_meta.php';
    } else {
        echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
    }
    ?>
    <title><?= t('interview_room_title', 'Interview Room') ?> - CodeMaster</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg: var(--tf-bg, #f8fafc);
            --bg-accent: radial-gradient(circle at top left, rgba(79, 70, 229, 0.08), transparent 32%), radial-gradient(circle at top right, rgba(14, 165, 233, 0.08), transparent 28%), linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
            --panel: rgba(255, 255, 255, 0.92);
            --panel-soft: #f8fafc;
            --panel-strong: #ffffff;
            --line: #e2e8f0;
            --line-strong: #cbd5f5;
            --ink: #0f172a;
            --ink-soft: #1f2937;
            --muted: #64748b;
            --brand: var(--tf-brand, #4f46e5);
            --brand-strong: var(--tf-brand-strong, #4338ca);
            --brand-soft: rgba(79, 70, 229, 0.12);
            --accent: var(--tf-accent, #0ea5e9);
            --success: var(--tf-success, #10b981);
            --danger: #ef4444;
            --shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
            --shadow-soft: 0 14px 34px rgba(15, 23, 42, 0.08);
        }

        body[data-theme="dark"] {
            --bg: #0b1220;
            --bg-accent: radial-gradient(circle at top left, rgba(79, 70, 229, 0.18), transparent 28%), radial-gradient(circle at top right, rgba(14, 165, 233, 0.16), transparent 24%), linear-gradient(180deg, #0f172a 0%, #0b1220 100%);
            --panel: rgba(15, 23, 42, 0.9);
            --panel-soft: #0f1a2b;
            --panel-strong: #17213a;
            --line: rgba(148, 163, 184, 0.16);
            --line-strong: rgba(148, 163, 184, 0.28);
            --ink: #e2e8f0;
            --ink-soft: #cbd5f5;
            --muted: #94a3b8;
            --brand: var(--tf-brand, #818cf8);
            --brand-strong: var(--tf-brand-strong, #6366f1);
            --brand-soft: rgba(129, 140, 248, 0.2);
            --accent: var(--tf-accent, #38bdf8);
            --success: var(--tf-success, #22c55e);
            --danger: #f87171;
            --shadow: 0 28px 70px rgba(0, 0, 0, 0.34);
            --shadow-soft: 0 14px 36px rgba(0, 0, 0, 0.24);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background: var(--bg);
            background-image: var(--bg-accent);
            min-height: 100vh;
        }

        .topbar {
            min-height: 74px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 24px;
            background: rgba(255, 252, 247, 0.68);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 20;
        }

        body[data-theme="dark"] .topbar {
            background: rgba(10, 16, 24, 0.74);
        }

        .top-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .top-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            flex-wrap: wrap;
        }

        .top-title .dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--brand);
            box-shadow: 0 0 0 6px var(--brand-soft);
        }

        .title-stack {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .room-kicker {
            font-size: 11px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 700;
        }

        .room-code {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: var(--panel-soft);
            color: var(--ink-soft);
            font-size: 12px;
            font-weight: 700;
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 13px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .timer-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--panel-strong), var(--panel-soft));
            border: 1px solid var(--line);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.24);
            color: var(--ink);
            font-weight: 700;
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, var(--panel-strong), var(--panel));
            color: var(--ink-soft);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform .18s ease, border-color .18s ease, background .18s ease, color .18s ease;
            box-shadow: var(--shadow-soft);
        }

        .icon-btn:hover,
        .icon-btn:focus-visible {
            transform: translateY(-1px);
            border-color: var(--line-strong);
            color: var(--brand);
        }

        .invite-btn {
            border: none;
            background: linear-gradient(135deg, var(--brand), var(--brand-strong));
            color: #fff;
            padding: 11px 16px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 16px 30px rgba(191, 90, 42, 0.24);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .invite-btn:hover,
        .invite-btn:focus-visible {
            transform: translateY(-1px);
            box-shadow: 0 20px 38px rgba(191, 90, 42, 0.28);
        }

        .avatar-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-soft), rgba(18, 77, 125, 0.12));
            color: var(--brand);
            font-weight: 700;
            display: grid;
            place-items: center;
            border: 1px solid var(--line);
        }

        .room-shell {
            max-width: 1460px;
            margin: 22px auto 28px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(360px, 0.86fr) minmax(320px, 0.62fr);
            gap: 18px;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 24px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
            overflow: hidden;
        }

        .editor-card {
            display: flex;
            flex-direction: column;
            min-height: 700px;
        }

        .editor-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px;
            border-bottom: 1px solid var(--line);
            background: linear-gradient(180deg, rgba(255,255,255,.18), transparent);
            gap: 12px;
            flex-wrap: wrap;
        }

        .lang-select {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px 12px;
            background: var(--panel-strong);
            color: var(--ink);
            font-size: 13px;
            min-height: 42px;
        }

        .hidden-control {
            display: none !important;
        }

        .editor-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .board-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }

        .board-menu {
            position: relative;
        }

        .board-menu-btn {
            border: 1px solid var(--line);
            background: linear-gradient(180deg, var(--panel-strong), var(--panel-soft));
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            color: var(--ink);
        }

        .board-menu-list {
            position: absolute;
            right: 0;
            top: 52px;
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: var(--shadow);
            min-width: 190px;
            display: none;
            z-index: 20;
            overflow: hidden;
        }

        .board-menu-list.open {
            display: block;
        }

        .board-menu-list button {
            width: 100%;
            text-align: left;
            padding: 12px 14px;
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--ink);
            font-weight: 600;
        }

        .board-menu-list button:hover {
            background: var(--panel-soft);
        }

        .board-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 18px;
            border-bottom: 1px solid var(--line);
            background: rgba(255,255,255,.08);
            flex-wrap: wrap;
        }

        .board-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .board-tab {
            border: 1px solid var(--line);
            background: var(--panel-soft);
            color: var(--ink-soft);
            padding: 9px 12px;
            border-radius: 999px;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            transition: border-color .18s ease, transform .18s ease, background .18s ease;
        }

        .board-tab .close {
            border: none;
            background: transparent;
            color: inherit;
            font-size: 12px;
            cursor: pointer;
        }

        .board-tab.active {
            background: var(--brand-soft);
            border-color: var(--brand);
            color: var(--brand);
            transform: translateY(-1px);
        }

        .board-add {
            border: 1px dashed var(--line);
            background: transparent;
            color: var(--ink-soft);
            padding: 10px 12px;
            border-radius: 999px;
            font-size: 12px;
            cursor: pointer;
            font-weight: 700;
        }

        .board-add:hover,
        .board-menu-btn:hover,
        .ghost-btn:hover,
        .btn:hover {
            border-color: var(--line-strong);
            background: var(--panel-soft);
        }

        .room-editor {
            flex: 1;
            display: flex;
            padding: 18px;
            background: linear-gradient(180deg, rgba(255,255,255,.08), transparent);
        }

        .editor {
            width: 100%;
            border: none;
            outline: none;
            padding: 22px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            line-height: 1.65;
            background: #10151c;
            color: #f6f7fb;
            border-radius: 18px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
            resize: none;
            min-height: 100%;
        }

        body[data-theme="dark"] .editor {
            background: #0b1118;
            color: var(--ink);
        }

        .output-card {
            display: flex;
            flex-direction: column;
            min-height: 700px;
            position: relative;
        }

        .output-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px;
            border-bottom: 1px solid var(--line);
            font-weight: 700;
            color: var(--ink-soft);
            background: linear-gradient(180deg, rgba(255,255,255,.18), transparent);
        }

        .panel-title {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .panel-title strong {
            color: var(--ink);
            font-size: 16px;
        }

        .panel-title span {
            font-size: 12px;
            color: var(--muted);
        }

        .ghost-btn {
            border: 1px solid var(--line);
            background: var(--panel-soft);
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 12px;
            cursor: pointer;
            font-weight: 700;
            color: var(--ink);
        }

        .output-box {
            flex: 1;
            margin: 18px;
            padding: 18px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: #dfe7f2;
            white-space: pre-wrap;
            border-radius: 18px;
            background: linear-gradient(180deg, #131d2b, #0c141f);
            border: 1px solid rgba(255,255,255,.05);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.03);
        }

        .output-actions {
            padding: 0 18px 18px;
            display: flex;
            justify-content: flex-end;
        }

        .primary-btn {
            border: none;
            background: linear-gradient(135deg, var(--brand), var(--brand-strong));
            color: #fff;
            padding: 12px 16px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 14px 28px rgba(191, 90, 42, 0.22);
        }

        .side-card {
            display: flex;
            flex-direction: column;
            min-height: 700px;
        }

        .call-card {
            padding: 16px 18px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: linear-gradient(180deg, rgba(255,255,255,.18), transparent);
        }

        .presence-copy {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .presence-copy strong {
            font-size: 15px;
        }

        .presence-copy span {
            font-size: 12px;
            color: var(--muted);
        }

        .call-btn {
            border: 1px solid var(--line);
            background: rgba(31, 138, 91, 0.12);
            color: var(--success);
            padding: 10px 14px;
            border-radius: 12px;
            font-weight: 600;
            cursor: not-allowed;
        }

        .participants {
            padding: 16px 18px;
            border-bottom: 1px solid var(--line);
        }

        .participants h4 {
            margin: 0 0 8px;
            font-size: 12px;
            color: var(--muted);
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .participant {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(127,127,127,.08);
        }

        .participant:last-child {
            border-bottom: none;
        }

        .participant-meta {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .participant-meta strong {
            color: var(--ink);
        }

        .participant-meta span {
            color: var(--muted);
            font-size: 12px;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--brand-soft);
            display: grid;
            place-items: center;
            font-weight: 700;
            color: var(--brand);
        }

        .right-tabs {
            display: flex;
            gap: 6px;
            padding: 14px 18px 10px;
            border-bottom: 1px solid var(--line);
        }

        .tab {
            padding: 9px 12px;
            border-radius: 999px;
            border: 1px solid var(--line);
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--ink-soft);
            font-weight: 700;
            background: var(--panel-soft);
        }

        .tab.active {
            background: var(--brand-soft);
            border-color: var(--brand);
            color: var(--brand);
        }

        .tab-panel {
            flex: 1;
            padding: 14px 18px;
            overflow: auto;
        }

        .chat-messages {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .chat-msg {
            background: linear-gradient(180deg, var(--panel-strong), var(--panel-soft));
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 13px;
            box-shadow: var(--shadow-soft);
        }

        .chat-msg strong {
            display: block;
            margin-bottom: 4px;
        }

        .chat-msg-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 6px;
            font-size: 11px;
            color: var(--muted);
        }

        .feedback-history {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 14px;
        }

        .feedback-card {
            background: linear-gradient(180deg, var(--panel-strong), var(--panel-soft));
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 12px 14px;
            box-shadow: var(--shadow-soft);
        }

        .feedback-card strong {
            color: var(--ink);
            display: block;
            margin-bottom: 4px;
        }

        .feedback-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .feedback-note {
            color: var(--ink-soft);
            font-size: 13px;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .chat-input {
            display: flex;
            gap: 8px;
            padding: 14px 18px 18px;
            border-top: 1px solid var(--line);
            background: rgba(255,255,255,.06);
        }

        .chat-input input {
            flex: 1;
            padding: 11px 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--panel-strong);
            color: var(--ink);
        }

        .inline-status {
            margin: 0 18px 16px;
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 12px;
            line-height: 1.45;
            border: 1px solid var(--line);
            background: var(--panel-soft);
            color: var(--muted);
            display: none;
        }

        .inline-status.is-visible {
            display: block;
        }

        .inline-status.is-error {
            border-color: rgba(239, 68, 68, 0.24);
            background: rgba(239, 68, 68, 0.08);
            color: #b91c1c;
        }

        .inline-status.is-success {
            border-color: rgba(16, 185, 129, 0.24);
            background: rgba(16, 185, 129, 0.08);
            color: #047857;
        }

        .inline-status.is-info {
            border-color: rgba(79, 70, 229, 0.2);
            background: rgba(79, 70, 229, 0.08);
            color: var(--brand-strong);
        }

        .btn {
            border: 1px solid var(--line);
            background: var(--panel-strong);
            padding: 10px 12px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            color: var(--ink);
        }

        .emoji-rating {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .emoji-btn {
            appearance: none;
            border: 1px solid var(--line);
            background: var(--panel-strong);
            border-radius: 14px;
            padding: 8px 10px;
            font-size: 20px;
            color: var(--muted);
            cursor: pointer;
            transition: transform .18s ease, background .18s ease, border-color .18s ease, color .18s ease, box-shadow .18s ease;
        }

        .emoji-btn i {
            font-size: 20px;
        }

        .emoji-btn:hover {
            transform: translateY(-1px);
            border-color: var(--line-strong);
            color: var(--ink);
        }

        .emoji-btn.active {
            background: var(--brand-soft);
            border-color: var(--brand);
            color: var(--brand);
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(191, 90, 42, 0.18);
        }

        .emoji-caption {
            text-align: center;
            margin-top: 10px;
            font-size: 13px;
            color: var(--muted);
        }

        .output-box.is-preview {
            padding: 0;
            overflow: hidden;
            border-radius: 16px;
        }

        .preview-frame {
            width: 100%;
            height: 100%;
            border: 0;
            background: #fff;
            display: block;
        }

        .evaluation {
            color: var(--muted);
            font-size: 13px;
        }

        .menu {
            position: absolute;
            top: 52px;
            right: 0;
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: var(--shadow);
            min-width: 230px;
            padding: 8px;
            display: none;
            z-index: 30;
        }

        .menu.open {
            display: block;
        }

        .menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 13px;
            color: var(--ink);
        }

        .menu-item:hover {
            background: var(--panel-soft);
        }

        .toggle {
            width: 38px;
            height: 20px;
            background: #e5e7eb;
            border-radius: 999px;
            position: relative;
            cursor: pointer;
        }

        .toggle::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            transition: transform .2s ease;
        }

        .toggle.active {
            background: var(--brand);
        }

        .toggle.active::after {
            transform: translateX(18px);
        }

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.42);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 40;
            padding: 16px;
        }

        .modal.open {
            display: flex;
        }

        .modal-card {
            background: var(--panel);
            border-radius: 24px;
            padding: 20px;
            width: min(520px, 100%);
            box-shadow: var(--shadow);
            border: 1px solid var(--line);
        }

        .modal-card h3 {
            margin: 0 0 12px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 12px;
        }

        .close-btn {
            border: none;
            background: transparent;
            cursor: pointer;
            color: var(--muted);
            float: right;
        }

        @media (max-width: 1200px) {
            .topbar {
                align-items: flex-start;
            }

            .top-right {
                width: 100%;
                justify-content: flex-start;
            }

            .room-shell {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .topbar {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .top-right {
                justify-content: flex-start;
            }

            .invite-btn {
                padding: 10px 12px;
            }

            .invite-btn .btn-label {
                display: none;
            }

            .board-tabs {
                overflow-x: auto;
                padding-bottom: 6px;
            }
        }

        @media (max-width: 700px) {
            .topbar {
                padding: 14px 16px;
            }

            .top-left,
            .top-title,
            .top-right,
            .editor-top,
            .board-bar,
            .output-head,
            .output-actions,
            .chat-input {
                width: 100%;
            }

            .top-left,
            .top-title,
            .top-right,
            .editor-top,
            .output-head,
            .chat-input {
                flex-direction: column;
                align-items: stretch;
            }

            .title-stack {
                min-width: 0;
            }

            #boardTitle {
                display: block;
                overflow-wrap: anywhere;
            }

            .room-code,
            .timer-pill,
            .icon-btn,
            .invite-btn,
            .board-menu-btn,
            .board-add,
            .ghost-btn,
            .primary-btn,
            .btn {
                width: 100%;
                justify-content: center;
            }

            .editor-card {
                min-height: 0;
            }

            .room-shell {
                padding: 0 14px;
                gap: 14px;
            }

            .panel,
            .modal-card {
                border-radius: 18px;
            }

            .room-editor,
            .output-box {
                margin: 0;
            }

            .room-editor {
                padding: 14px;
            }

            .output-box {
                margin: 14px;
                min-height: 220px;
            }

            .board-tabs {
                width: 100%;
                display: flex;
                gap: 8px;
            }

            .board-add {
                flex: 0 0 auto;
            }

            .participants,
            .right-tabs,
            .tab-panel {
                width: 100%;
            }

            .right-tabs {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .tab {
                justify-content: center;
            }

            .chat-input input,
            #evaluationNote,
            .lang-select {
                width: 100%;
            }

            .modal {
                padding: 16px;
            }

            .modal-card {
                width: min(100%, 100%);
            }
        }
    </style>
</head>
<body>
<header class="topbar">
    <div class="top-left">
        <button class="icon-btn" id="backToInterviews" aria-label="<?= t('interview_back', 'Back') ?>"><i class="fas fa-arrow-left"></i></button>
        <div class="top-title">
            <span class="dot"></span>
            <div class="title-stack">
                <span class="room-kicker"><?= t('interview_room_kicker', 'Interview Room') ?></span>
                <span id="boardTitle"><?= htmlspecialchars($sessionTitle !== '' ? $sessionTitle : t('interview_whiteboard', 'WhiteBoard 1')) ?></span>
            </div>
            <button class="icon-btn" id="renameBtn" aria-label="<?= t('interview_rename', 'Rename') ?>"><i class="fas fa-pen-to-square"></i></button>
            <span class="room-code"><i class="fas fa-hashtag"></i> <?= htmlspecialchars($sessionCode) ?></span>
        </div>
    </div>
    <div class="top-right">
        <span class="timer-pill"><i class="fas fa-clock"></i> <span id="globalTimer">00:00:00</span></span>
        <button class="icon-btn" id="timerStart" aria-label="<?= t('interview_timer_start_label', 'Start timer') ?>"><i class="fas fa-play"></i></button>
        <button class="icon-btn" id="timerPause" aria-label="<?= t('interview_timer_pause_label', 'Pause timer') ?>"><i class="fas fa-pause"></i></button>
        <button class="icon-btn" id="shortcutsBtn" aria-label="<?= t('interview_shortcuts_label', 'Shortcuts') ?>"><i class="fas fa-keyboard"></i></button>
        <div style="position: relative;">
            <button class="icon-btn" id="settingsBtn" aria-label="<?= t('interview_settings', 'Settings') ?>"><i class="fas fa-sliders"></i></button>
            <div class="menu" id="settingsMenu">
                <div class="menu-item">
                    <span><?= t('interview_dark_side', 'Dark Side') ?></span>
                    <div class="toggle" id="darkSideToggle"></div>
                </div>
                <div class="menu-item" id="equipmentTestBtn">
                    <span><?= t('interview_equipment_test', 'Equipment Test') ?></span>
                    <i class="fas fa-chevron-right"></i>
                </div>
                <div class="menu-item" id="openEvaluationBtn">
                    <span><?= t('interview_feedback', 'Feedback') ?></span>
                    <i class="fas fa-chevron-right"></i>
                </div>
                <div class="menu-item" id="endInterviewBtnTop">
                    <span><?= t('interview_end', 'End Interview') ?></span>
                    <i class="fas fa-door-open"></i>
                </div>
            </div>
        </div>
        <div class="avatar-circle"><?= htmlspecialchars(mb_substr((string) ($currentUser['name'] ?? 'A'), 0, 1)) ?></div>
        <button class="invite-btn" id="inviteBtnTop"><i class="fas fa-share-nodes"></i> <span class="btn-label"><?= t('interview_invite', 'Invite') ?></span></button>
    </div>
</header>

<div class="room-shell" data-session-code="<?= htmlspecialchars($sessionCode) ?>">
    <section class="panel editor-card">
        <div class="editor-top">
            <div class="editor-controls">
                <select id="languageSelect" class="lang-select">
                    <option value="cpp">C++</option>
                    <option value="python">Python</option>
                    <option value="java">Java</option>
                    <option value="javascript">JavaScript</option>
                    <option value="typescript">TypeScript</option>
                    <option value="go">Go</option>
                    <option value="rust">Rust</option>
                    <option value="csharp">C#</option>
                    <option value="kotlin">Kotlin</option>
                    <option value="swift">Swift</option>
                    <option value="php">PHP</option>
                    <option value="ruby">Ruby</option>
                    <option value="scala">Scala</option>
                    <option value="dart">Dart</option>
                    <option value="sql">SQL</option>
                </select>
                <select id="problemSelect" class="lang-select hidden-control">
                    <?php if (!empty($interviewProblems)): ?>
                        <?php foreach ($interviewProblems as $problem): ?>
                            <option value="<?= (int) ($problem['id'] ?? 0) ?>">
                                <?= htmlspecialchars((string) ($problem['title'] ?? ('Problem #' . (int) ($problem['id'] ?? 0)))) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="0"><?= t('interview_problem_default', 'Problem') ?></option>
                    <?php endif; ?>
                </select>
            </div>
            <span id="timerValue" style="display:none;">00:00:00</span>
            <div class="board-actions">
                <div class="board-menu">
                    <button class="board-menu-btn" id="boardMenuBtn"><i class="fas fa-layer-group"></i> <?= t('interview_board_create', 'Create board') ?></button>
                    <div class="board-menu-list" id="boardMenuList">
                        <button type="button" data-board-template="lc"><?= t('interview_board_lc', 'LC Question') ?></button>
                        <button type="button" data-board-template="html"><?= t('interview_board_html', 'HTML') ?></button>
                        <button type="button" data-board-template="css"><?= t('interview_board_css', 'CSS') ?></button>
                        <button type="button" data-board-template="js"><?= t('interview_board_js', 'JS') ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="board-bar">
            <div class="board-tabs" id="boardTabs"></div>
            <button class="board-add" id="addBoard"><i class="fas fa-plus"></i> <?= t('interview_board_add', 'Board') ?></button>
        </div>
        <div class="room-editor">
            <textarea class="editor" id="codeEditor" spellcheck="false">#include <stdio.h>

int main(void) {
    puts("Hello LeetCoder");
    return 0;
}
</textarea>
        </div>
    </section>

    <section class="panel output-card">
        <div class="output-head">
            <div class="panel-title">
                <strong><?= t('interview_output', 'Вывод') ?></strong>
                <span><?= t('interview_output_hint', 'Результаты компилятора, заметки и быстрый запуск') ?></span>
            </div>
            <button class="ghost-btn" id="clearOutput"><?= t('interview_clear', 'Очистить') ?></button>
        </div>
        <div class="output-box" id="outputBox"></div>
        <div class="output-actions">
            <button class="primary-btn" id="runCode"><?= t('interview_run', 'Проверить') ?></button>
        </div>
    </section>

    <aside class="panel side-card">
        <div class="call-card">
            <div class="presence-copy">
                <strong><?= t('interview_sidebar_title', 'Сотрудничество') ?></strong>
                <span><?= t('interview_sidebar_hint', 'Участники, заметки и быстрая обратная связь в одном месте') ?></span>
            </div>
            <button class="call-btn"><i class="fas fa-video"></i> <?= t('interview_call', 'Звонок') ?></button>
        </div>
        <div class="participants">
            <h4><?= t('interview_participants', 'Participants') ?> <span id="participantsCount">0</span></h4>
            <div id="participantsList"></div>
        </div>
        <div class="right-tabs">
            <div class="tab active" data-tab="chat"><i class="fas fa-comment-dots"></i> <?= t('interview_chat', 'Чат') ?></div>
            <div class="tab" data-tab="evaluation"><i class="fas fa-clipboard-check"></i> <?= t('interview_evaluation', 'Оценка') ?></div>
        </div>
        <div class="tab-panel" id="chatPanel">
            <div class="chat-messages" id="chatMessages"></div>
        </div>
        <div class="tab-panel" id="evalPanel" style="display:none;">
            <div class="evaluation">
                <div class="emoji-rating" id="emojiRating">
                    <button class="emoji-btn" data-score="1" aria-label="<?= t('interview_rating_bad', 'Bad') ?>"><i class="fa-regular fa-face-angry"></i></button>
                    <button class="emoji-btn" data-score="2" aria-label="<?= t('interview_rating_ok', 'Okay') ?>"><i class="fa-regular fa-face-frown"></i></button>
                    <button class="emoji-btn" data-score="3" aria-label="<?= t('interview_rating_good', 'Good') ?>"><i class="fa-regular fa-face-meh"></i></button>
                    <button class="emoji-btn" data-score="4" aria-label="<?= t('interview_rating_great', 'Great') ?>"><i class="fa-regular fa-face-smile"></i></button>
                    <button class="emoji-btn" data-score="5" aria-label="<?= t('interview_rating_excellent', 'Excellent') ?>"><i class="fa-regular fa-face-grin-stars"></i></button>
                </div>
                <div class="emoji-caption" id="emojiCaption"><?= t('interview_evaluation_pick', 'Выберите оценку') ?></div>
                <textarea id="evaluationNote" class="lang-select" style="width:100%; min-height:100px; margin-top:12px;" placeholder="<?= htmlspecialchars(t('interview_feedback', 'Обратная связь')) ?>"></textarea>
                <div style="display:flex; justify-content:flex-end; margin-top:10px;">
                    <button class="primary-btn" id="saveEvaluationBtn"><?= t('common_save', 'Сохранить') ?></button>
                </div>
                <div class="evaluation" id="evaluationStatus"><?= t('interview_evaluation_hint', 'Оценка будет сохранена локально для этой комнаты.') ?></div>
                <div id="feedbackHistory" class="feedback-history"></div>
            </div>
        </div>
        <div class="chat-input">
            <input id="chatInput" placeholder="<?= t('interview_chat_placeholder', 'Нажмите Enter, чтобы отправить') ?>" />
            <button class="btn" id="sendMsg"><?= t('interview_send', 'Отправить') ?></button>
        </div>
        <div id="chatStatus" class="inline-status"></div>
    </aside>
</div>

<div class="modal" id="equipmentModal">
    <div class="modal-card">
        <button class="close-btn" data-close>&times;</button>
        <h3><?= t('interview_equipment_test', 'Equipment Test') ?></h3>
        <div id="equipmentStatus" style="font-size:13px; color:var(--muted); line-height:1.8;">
            <div><?= t('interview_equipment_checking', 'Checking browser capabilities...') ?></div>
        </div>
    </div>
</div>

<div class="modal" id="shortcutsModal">
    <div class="modal-card">
        <button class="close-btn" data-close>&times;</button>
        <h3><?= t('interview_shortcuts', 'Editor Shortcuts') ?></h3>
        <div style="font-size:13px; color:var(--muted); line-height:1.8;">
            <div><?= t('interview_shortcut_run', 'Run Code') ?> <strong>CTRL + Enter</strong></div>
            <div><?= t('interview_shortcut_indent', 'Indent Line') ?> <strong>TAB</strong></div>
            <div><?= t('interview_shortcut_outdent', 'Outdent Line') ?> <strong>SHIFT + TAB</strong></div>
            <div><?= t('interview_shortcut_comment', 'Comment') ?> <strong>CTRL + /</strong></div>
            <div><?= t('interview_shortcut_undo', 'Undo') ?> <strong>CTRL + Z</strong></div>
            <div><?= t('interview_shortcut_redo', 'Redo') ?> <strong>CTRL + SHIFT + Z</strong></div>
        </div>
    </div>
</div>

<div class="modal" id="renameModal">
    <div class="modal-card">
        <button class="close-btn" data-close>&times;</button>
        <h3><?= t('interview_rename_title', 'Rename board') ?></h3>
        <input class="lang-select" id="renameInput" type="text" value="<?= htmlspecialchars($sessionTitle) ?>" style="width:100%; margin-top:8px;">
        <div class="modal-actions">
            <button class="btn" data-close><?= t('common_cancel', 'Cancel') ?></button>
            <button class="primary-btn" id="confirmRename"><?= t('common_confirm', 'Confirm') ?></button>
        </div>
    </div>
</div><script>
// Вспомогательная функция для безопасного вывода текста (защита от XSS)
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text || '';
  return div.innerHTML;
}

const initialSession = <?= tfSafeJson($initialInterviewSession, JSON_UNESCAPED_UNICODE) ?>;
const sessionCode = initialSession.code || document.querySelector('[data-session-code]')?.dataset.sessionCode || '';
const editor = document.getElementById('codeEditor');
const chatMessages = document.getElementById('chatMessages');
const participantsList = document.getElementById('participantsList');
const participantsCountEl = document.getElementById('participantsCount');
const timerValue = document.getElementById('timerValue');
const darkSideToggle = document.getElementById('darkSideToggle');
const languageSelect = document.getElementById('languageSelect');
const problemSelect = document.getElementById('problemSelect');
const globalTimer = document.getElementById('globalTimer');
const inviteBtnTop = document.getElementById('inviteBtnTop');
const settingsBtn = document.getElementById('settingsBtn');
const settingsMenu = document.getElementById('settingsMenu');
const shortcutsBtn = document.getElementById('shortcutsBtn');
const shortcutsModal = document.getElementById('shortcutsModal');
const equipmentModal = document.getElementById('equipmentModal');
const equipmentStatus = document.getElementById('equipmentStatus');
const renameBtn = document.getElementById('renameBtn');
const renameModal = document.getElementById('renameModal');
const renameInput = document.getElementById('renameInput');
const confirmRename = document.getElementById('confirmRename');
const equipmentTestBtn = document.getElementById('equipmentTestBtn');
const openEvaluationBtn = document.getElementById('openEvaluationBtn');
const backToInterviews = document.getElementById('backToInterviews');
const boardMenuBtn = document.getElementById('boardMenuBtn');
const boardMenuList = document.getElementById('boardMenuList');
const boardTitleEl = document.getElementById('boardTitle');
const boardTabsEl = document.getElementById('boardTabs');
const addBoardBtn = document.getElementById('addBoard');
const output = document.getElementById('outputBox');
const saveEvaluationBtn = document.getElementById('saveEvaluationBtn');
const evaluationNote = document.getElementById('evaluationNote');
const evaluationStatus = document.getElementById('evaluationStatus');
const feedbackHistory = document.getElementById('feedbackHistory');
const chatStatus = document.getElementById('chatStatus');
let selectedProblemId = Number(problemSelect?.value || 0);
let currentUserRole = initialSession.current_user_role || 'member';
let timer = Number(initialSession.remaining_seconds || 0);
let timerRunning = false;
let timerInterval = null;
let remoteTimerMode = false;
let boardsDirty = false;
let pollingInFlight = false;
let saveInFlight = false;
let pendingSave = false;
let runInFlight = false;
const csrfToken = <?= tfSafeJson((string) ($_SESSION['csrf_token'] ?? ''), JSON_UNESCAPED_UNICODE) ?>;
const withBypassParam = (url) => {
  const raw = String(url || '');
  if (!raw.includes('?')) {
    return `${raw}?i=1`;
  }
  if (/[?&]i=/.test(raw)) {
    return raw;
  }
  return `${raw}&i=1`;
};
const postJson = (url, payload) => {
  const headers = {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  };
  if (csrfToken) {
    headers['X-CSRF-Token'] = csrfToken;
  }
  return fetch(withBypassParam(url), {
    method: 'POST',
    headers,
    body: JSON.stringify(payload || {})
  });
};
const setInlineStatus = (element, message = '', type = 'info') => {
  if (!element) return;
  const text = String(message || '').trim();
  element.classList.remove('is-error', 'is-success', 'is-info', 'is-visible');
  element.textContent = '';
  if (!text) return;
  element.textContent = text;
  element.classList.add('is-visible');
  element.classList.add(type === 'error' ? 'is-error' : (type === 'success' ? 'is-success' : 'is-info'));
};
const requestJson = async (url, payload = null, options = {}) => {
  const isPost = payload !== null || options.method === 'POST';
  const response = isPost
    ? await postJson(url, payload || {})
    : await fetch(withBypassParam(url), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  const raw = await response.text();
  const cleaned = String(raw || '').replace(/^\uFEFF/, '');
  if (challengeDetected(cleaned)) {
    throw new Error(antiBotErrorLabel);
  }
  let data = null;
  try {
    data = cleaned ? JSON.parse(cleaned) : null;
  } catch (e) {
    const start = cleaned.indexOf('{');
    const end = cleaned.lastIndexOf('}');
    if (start !== -1 && end !== -1 && end > start) {
      try {
        data = JSON.parse(cleaned.slice(start, end + 1));
      } catch (_) {
        throw new Error(extractPlainServerMessage(cleaned) || serverErrorLabel);
      }
    } else {
      throw new Error(extractPlainServerMessage(cleaned) || serverErrorLabel);
    }
  }
  if (!response.ok || !data || data.success === false) {
    throw new Error((data && data.message) ? data.message : serverErrorLabel);
  }
  return data;
};
const formatMessageTime = (value) => {
  const text = String(value || '').trim();
  if (!text) return '';
  const normalized = text.replace(' ', 'T');
  const parsed = new Date(normalized);
  if (Number.isNaN(parsed.getTime())) return text;
  return parsed.toLocaleString();
};
const normalizeJudgeLanguage = (lang) => {
  if (lang === 'javascript') return 'js';
  if (lang === 'typescript') return 'ts';
  return lang;
};
const challengeDetected = (text) => {
  const s = String(text || '').toLowerCase();
  return s.includes('slowaes.decrypt')
    || (s.includes('document.cookie') && s.includes('__test='))
    || s.includes('/aes.js')
    || s.includes('this site requires javascript to work');
};
const extractPlainServerMessage = (text) => {
  const plain = String(text || '')
    .replace(/<script[\s\S]*?<\/script>/gi, ' ')
    .replace(/<style[\s\S]*?<\/style>/gi, ' ')
    .replace(/<[^>]+>/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();
  const sqlMatch = plain.match(/SQLSTATE\[[^\]]+\]:\s*[^|<]+/i);
  if (sqlMatch) return sqlMatch[0];
  return plain ? plain.slice(0, 220) : '';
};
const antiBotErrorLabel = <?= tfSafeJson(t('common_server_error', 'Ошибка сервера'), JSON_UNESCAPED_UNICODE) ?> + '. ' + <?= tfSafeJson(t('common_try_again', 'Попробуйте еще раз.'), JSON_UNESCAPED_UNICODE) ?>;

const defaultSnippets = {
  cpp: "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n  // your code\n  return 0;\n}\n",
  python: "def solve():\n  # your code\n  pass\n\nif __name__ == '__main__':\n  solve()\n",
  java: "import java.io.*;\nimport java.util.*;\n\npublic class Main {\n  public static void main(String[] args) throws Exception {\n    // your code\n  }\n}\n",
  javascript: "const fs = require('fs');\n\nfunction solve(input) {\n  // your code\n}\n\nsolve(fs.readFileSync(0, 'utf8').trim());\n",
  typescript: "import * as fs from 'fs';\n\nfunction solve(input: string): void {\n  // your code\n}\n\nsolve(fs.readFileSync(0, 'utf8').trim());\n",
  go: "package main\n\nimport (\n  \"bufio\"\n  \"fmt\"\n  \"os\"\n)\n\nfunc main() {\n  in := bufio.NewReader(os.Stdin)\n  _ = in\n  fmt.Println(\"\")\n}\n",
  rust: "use std::io::{self, Read};\n\nfn main() {\n  let mut input = String::new();\n  io::stdin().read_to_string(&mut input).unwrap();\n  // your code\n}\n",
  csharp: "using System;\n\npublic class Program {\n  public static void Main() {\n    // your code\n  }\n}\n",
  kotlin: "import java.io.BufferedReader\nimport java.io.InputStreamReader\n\nfun main() {\n  val br = BufferedReader(InputStreamReader(System.`in`))\n  val input = br.readLine()\n  // your code\n}\n",
  swift: "import Foundation\n\nlet data = String(data: FileHandle.standardInput.readDataToEndOfFile(), encoding: .utf8) ?? \"\"\n// your code\nprint(\"\")\n",
  php: "<" + "?php\n$input = trim(stream_get_contents(STDIN));\n// your code\n?" + ">\n",
  ruby: "input = STDIN.read\n# your code\n",
  scala: "import scala.io.StdIn\n\nobject Main {\n  def main(args: Array[String]): Unit = {\n    val input = StdIn.readLine()\n    // your code\n  }\n}\n",
  dart: "import 'dart:io';\n\nvoid main() {\n  final input = stdin.readLineSync() ?? '';\n  // your code\n}\n",
  sql: "-- write your query here\n"
};

const attachSmartIndent = (textarea, getLang) => {
  if (!textarea) return;
  const indentFor = (lang) => (lang === 'python' ? '    ' : '  ');
  textarea.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') {
      e.preventDefault();
      const indent = indentFor(getLang());
      const start = textarea.selectionStart;
      const end = textarea.selectionEnd;
      const value = textarea.value;
      textarea.value = value.slice(0, start) + indent + value.slice(end);
      textarea.selectionStart = textarea.selectionEnd = start + indent.length;
      return;
    }
    if (e.key === 'Enter') {
      const start = textarea.selectionStart;
      const end = textarea.selectionEnd;
      const value = textarea.value;
      const before = value.slice(0, start);
      const after = value.slice(end);
      const lineStart = before.lastIndexOf('\n') + 1;
      const currentLine = before.slice(lineStart);
      const baseIndent = (currentLine.match(/^\s*/)?.[0]) || '';
      const trimmed = currentLine.trimEnd();
      const lang = getLang();
      const indentUnit = indentFor(lang);
      let extra = '';
      if (trimmed.endsWith('{')) extra = indentUnit;
      if (lang === 'python' && trimmed.endsWith(':')) extra = indentUnit;
      const insert = '\n' + baseIndent + extra;
      e.preventDefault();
      textarea.value = before + insert + after;
      const caret = before.length + insert.length;
      textarea.selectionStart = textarea.selectionEnd = caret;
    }
  });
};

const boardLabel = <?= tfSafeJson(t('interview_board', 'Board'), JSON_UNESCAPED_UNICODE) ?>;
const lcBoardLabel = <?= tfSafeJson(t('interview_board_lc', 'LC Question'), JSON_UNESCAPED_UNICODE) ?>;
const htmlBoardLabel = <?= tfSafeJson(t('interview_board_html', 'HTML'), JSON_UNESCAPED_UNICODE) ?>;
const cssBoardLabel = <?= tfSafeJson(t('interview_board_css', 'CSS'), JSON_UNESCAPED_UNICODE) ?>;
const jsBoardLabel = <?= tfSafeJson(t('interview_board_js', 'JS'), JSON_UNESCAPED_UNICODE) ?>;
const runLabelLc = <?= tfSafeJson(t('interview_run', 'Run Code'), JSON_UNESCAPED_UNICODE) ?>;
const runLabelPreview = <?= tfSafeJson(t('interview_preview', 'Preview'), JSON_UNESCAPED_UNICODE) ?>;
const previewTitle = <?= tfSafeJson(t('interview_preview', 'Preview'), JSON_UNESCAPED_UNICODE) ?>;
const testLabel = <?= tfSafeJson(t('interview_test', 'Test'), JSON_UNESCAPED_UNICODE) ?>;
const okLabel = <?= tfSafeJson(t('interview_ok', 'OK'), JSON_UNESCAPED_UNICODE) ?>;
const waLabel = <?= tfSafeJson(t('interview_wa', 'WA'), JSON_UNESCAPED_UNICODE) ?>;
const serverErrorLabel = <?= tfSafeJson(t('common_server_error', 'Ошибка сервера'), JSON_UNESCAPED_UNICODE) ?>;
const feedbackTitleLabel = <?= tfSafeJson(t('interview_feedback', 'Обратная связь'), JSON_UNESCAPED_UNICODE) ?>;
const feedbackEmptyLabel = <?= tfSafeJson(t('interview_feedback_empty', 'Пока нет сохраненных отзывов.'), JSON_UNESCAPED_UNICODE) ?>;
const feedbackScoreLabel = <?= tfSafeJson(t('interview_feedback_score', 'Оценка'), JSON_UNESCAPED_UNICODE) ?>;
const feedbackNoteLabel = <?= tfSafeJson(t('interview_feedback_note', 'Комментарий'), JSON_UNESCAPED_UNICODE) ?>;
const feedbackNoNoteLabel = <?= tfSafeJson(t('interview_feedback_no_note', 'Без комментария'), JSON_UNESCAPED_UNICODE) ?>;
const htmlSnippet = "<!doctype html>\n<html lang=\"en\">\n<head>\n  <meta charset=\"UTF-8\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <title>Interview</title>\n</head>\n<body>\n  <h1>Hello</h1>\n</body>\n</html>\n";
const cssSnippet = "/* interview styles */\nbody {\n  font-family: system-ui, sans-serif;\n  padding: 16px;\n}\n";
const jsSnippet = "// interview scripts\nconsole.log('Interview');\n";
const boardStorageKey = `tfBoards:${sessionCode}`;
const evaluationStorageKey = `tfEvalNote:${sessionCode}`;
let boardsState = { activeId: '', boards: [] };
const boardTemplates = {
  lc: () => ({
    kind: 'lc',
    title: lcBoardLabel,
    code: defaultSnippets[languageSelect?.value || 'cpp'] || defaultSnippets.cpp,
    language: languageSelect?.value || 'cpp',
    problemId: Number(problemSelect?.value || 0)
  }),
  html: () => ({
    kind: 'html',
    title: htmlBoardLabel,
    code: htmlSnippet,
    language: 'markup'
  }),
  css: () => ({
    kind: 'css',
    title: cssBoardLabel,
    code: cssSnippet,
    language: 'css'
  }),
  js: () => ({
    kind: 'js',
    title: jsBoardLabel,
    code: jsSnippet,
    language: 'javascript'
  })
};

const saveBoardsState = () => {
  localStorage.setItem(boardStorageKey, JSON.stringify(boardsState));
};

const loadBoardsState = () => {
  const initialBoards = initialSession && initialSession.boards_snapshot && Array.isArray(initialSession.boards_snapshot.boards)
    ? initialSession.boards_snapshot
    : null;
  if (initialBoards && initialBoards.boards.length) {
    boardsState = initialBoards;
    saveBoardsState();
    return;
  }
  try {
    const raw = localStorage.getItem(boardStorageKey);
    const parsed = raw ? JSON.parse(raw) : null;
    if (parsed && Array.isArray(parsed.boards) && parsed.boards.length) {
      boardsState = parsed;
      return;
    }
  } catch (e) { /* ignore */ }
  const initialCode = (typeof initialSession.code_snapshot === 'string' && initialSession.code_snapshot !== '')
    ? initialSession.code_snapshot
    : (editor ? editor.value : '');
  boardsState = {
    activeId: 'board-lc',
    boards: [
      { id: 'board-lc', kind: 'lc', title: lcBoardLabel, code: initialCode || (defaultSnippets[languageSelect?.value || 'cpp'] || defaultSnippets.cpp), language: languageSelect?.value || 'cpp', problemId: Number(problemSelect?.value || 0) },
      { id: 'board-html', kind: 'html', title: htmlBoardLabel, code: htmlSnippet, language: 'markup' },
      { id: 'board-css', kind: 'css', title: cssBoardLabel, code: cssSnippet, language: 'css' },
      { id: 'board-js', kind: 'js', title: jsBoardLabel, code: jsSnippet, language: 'javascript' }
    ]
  };
  saveBoardsState();
};

const renderBoardTabs = () => {
  if (!boardTabsEl) return;
  boardTabsEl.innerHTML = '';
  boardsState.boards.forEach((b) => {
    if (!b.kind) b.kind = 'lc';
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'board-tab' + (b.id === boardsState.activeId ? ' active' : '');
    btn.innerHTML = `<span>${escapeHtml(b.title)}</span>`;
    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'close';
    close.innerHTML = '&times;';
    close.addEventListener('click', (e) => {
      e.stopPropagation();
      removeBoard(b.id);
    });
    btn.appendChild(close);
    btn.addEventListener('click', () => switchBoard(b.id));
    boardTabsEl.appendChild(btn);
  });
};

const updateBoardUi = () => {
  const active = getActiveBoard();
  if (!active) return;
  const isLcBoard = active.kind === 'lc';
  if (boardTitleEl) boardTitleEl.textContent = active.title || boardLabel;
  if (languageSelect) {
    languageSelect.classList.toggle('hidden-control', !isLcBoard);
    if (isLcBoard && active.language) {
      languageSelect.value = active.language;
    }
  }
  if (problemSelect) {
    problemSelect.classList.toggle('hidden-control', !isLcBoard);
    if (isLcBoard) {
      selectedProblemId = Number(active.problemId || problemSelect.value || 0);
      if (selectedProblemId <= 0) {
        const firstAvailable = Array.from(problemSelect.options || []).find((option) => Number(option.value || 0) > 0);
        if (firstAvailable) {
          selectedProblemId = Number(firstAvailable.value || 0);
          active.problemId = selectedProblemId;
        }
      }
      if (selectedProblemId > 0) {
        problemSelect.value = String(selectedProblemId);
      }
    }
  }
  const runButton = document.getElementById('runCode');
  if (runButton) {
    runButton.disabled = false;
    runButton.style.opacity = '1';
    runButton.textContent = isLcBoard ? runLabelLc : runLabelPreview;
    runButton.title = isLcBoard ? '' : '';
    runButton.dataset.mode = isLcBoard ? 'lc' : 'preview';
  }
  if (output && isLcBoard && output.classList.contains('is-preview')) {
    output.classList.remove('is-preview');
    output.textContent = `${<?= tfSafeJson(t('interview_output', 'Output'), JSON_UNESCAPED_UNICODE) ?>}:`;
  }
};

const switchBoard = (id) => {
  const current = boardsState.boards.find(b => b.id === boardsState.activeId);
  if (current && editor) {
    current.code = editor.value;
    if (current.kind === 'lc') {
      current.language = languageSelect?.value || current.language || 'cpp';
      current.problemId = Number(problemSelect?.value || current.problemId || 0);
    }
  }
  const next = boardsState.boards.find(b => b.id === id);
  if (!next) return;
  boardsState.activeId = id;
  if (editor) {
    editor.value = next.code || '';
    editor.dataset.dirty = '1';
  }
  updateBoardUi();
  renderBoardTabs();
  saveBoardsState();
  saveCode();
};

const getActiveBoard = () => boardsState.boards.find(b => b.id === boardsState.activeId);
const removeBoard = (id) => {
  if (boardsState.boards.length <= 1) return;
  const idx = boardsState.boards.findIndex(b => b.id === id);
  if (idx === -1) return;
  boardsState.boards.splice(idx, 1);
  if (boardsState.activeId === id) {
    const next = boardsState.boards[Math.max(0, idx - 1)] || boardsState.boards[0];
    boardsState.activeId = next.id;
  }
  saveBoardsState();
  renderBoardTabs();
  const active = getActiveBoard();
  if (active && editor) {
    editor.value = active.code || '';
    editor.dataset.dirty = '1';
  }
  updateBoardUi();
  boardsDirty = true;
  saveCode();
};

const createBoardFromTemplate = (templateKey) => {
  const factory = boardTemplates[templateKey];
  if (!factory) return;
  const payload = factory();
  const id = `board-${templateKey}-${Date.now()}`;
  boardsState.boards.push({ id, ...payload });
  saveBoardsState();
  switchBoard(id);
  boardsDirty = true;
  boardMenuList?.classList.remove('open');
};


const applyTheme = (theme) => {
  document.body.dataset.theme = theme;
  if (darkSideToggle) darkSideToggle.classList.toggle('active', theme === 'dark');
  localStorage.setItem('tfEditorTheme', theme);
};

applyTheme(localStorage.getItem('tfEditorTheme') || 'light');
const bindThemeToggle = (btn) => {
  if (!btn) return;
  btn.addEventListener('click', () => {
    const next = document.body.dataset.theme === 'dark' ? 'light' : 'dark';
    applyTheme(next);
  });
};
bindThemeToggle(darkSideToggle);

loadBoardsState();
const initialBoard = getActiveBoard();
if (initialBoard && editor) {
  editor.value = initialBoard.code || editor.value;
}
renderBoardTabs();
updateBoardUi();
if (addBoardBtn) {
  addBoardBtn.addEventListener('click', () => {
    createBoardFromTemplate('lc');
  });
}

const updateSnippet = () => {
  const active = getActiveBoard();
  if (active?.kind !== 'lc') return;
  if (editor && !editor.dataset.dirty) {
    const snippet = defaultSnippets[languageSelect.value];
    if (snippet) editor.value = snippet;
  }
};

if (languageSelect) {
  const storedLang = localStorage.getItem('tfEditorLang');
  if (storedLang) {
    languageSelect.value = storedLang;
    updateSnippet();
  }
  languageSelect.addEventListener('change', () => {
    const active = getActiveBoard();
    if (active && active.kind === 'lc') {
      active.language = languageSelect.value;
    }
    localStorage.setItem('tfEditorLang', languageSelect.value);
    // Принудительно сбрасываем флаг dirty, чтобы принудительно подставить новый шаблон
    editor.dataset.dirty = ''; 
    updateSnippet();
    saveCode();
  });
}

if (problemSelect) {
  problemSelect.addEventListener('change', () => {
    selectedProblemId = Number(problemSelect.value || 0);
    const active = getActiveBoard();
    if (active && active.kind === 'lc') {
      active.problemId = selectedProblemId;
      saveCode();
    }
  });
}

const syncGlobalTimer = () => {
  if (globalTimer) globalTimer.textContent = timerValue.textContent;
};

const renderParticipants = (list=[]) => {
  participantsList.innerHTML = '';
  if (participantsCountEl) participantsCountEl.textContent = String(list.length);
  list.forEach(p => {
    const userName = p.name || <?= tfSafeJson(t('common_user', 'User'), JSON_UNESCAPED_UNICODE) ?>;
    const initials = userName.trim().split(' ').map(s => s[0]).slice(0,2).join('').toUpperCase();
    
    const el = document.createElement('div');
    el.className = 'participant';
    const roleLabel = p.role === 'owner'
      ? <?= tfSafeJson(t('interview_role_host', 'Host'), JSON_UNESCAPED_UNICODE) ?>
      : <?= tfSafeJson(t('interview_role_member', 'Member'), JSON_UNESCAPED_UNICODE) ?>;
      
    el.innerHTML = `<div class="avatar">${escapeHtml(initials)}</div><div class="participant-meta"><strong>${escapeHtml(userName)}</strong><span>${escapeHtml(roleLabel)}</span></div>`;
    participantsList.appendChild(el);
  });
};

const renderMessages = (list=[]) => {
  chatMessages.innerHTML = '';
  list.filter((m) => !parseFeedbackMessage(m?.message)).forEach(m => {
    const el = document.createElement('div');
    el.className = 'chat-msg';
    const fallbackUser = <?= tfSafeJson(t('common_user', 'User'), JSON_UNESCAPED_UNICODE) ?>;
    const author = escapeHtml(m.name || fallbackUser);
    const createdAt = escapeHtml(formatMessageTime(m.created_at || ''));
    el.innerHTML = `
      <div class="chat-msg-meta">
        <strong>${author}</strong>
        <span>${createdAt}</span>
      </div>
      <div>${escapeHtml(m.message || '')}</div>
    `;
    chatMessages.appendChild(el);
  });
  chatMessages.scrollTop = chatMessages.scrollHeight;
};

const parseFeedbackMessage = (message) => {
  const text = String(message || '').trim();
  const match = text.match(/^\[Feedback\]\s*score=(\d+)\s*;\s*note=(.*)$/i);
  if (!match) return null;
  return {
    score: Number(match[1] || 0),
    note: String(match[2] || '').trim()
  };
};

const renderFeedbackHistory = (list = []) => {
  if (!feedbackHistory) return;
  feedbackHistory.innerHTML = '';
  const feedbackItems = list
    .map((item) => {
      const parsed = parseFeedbackMessage(item?.message);
      if (!parsed) return null;
      return {
        name: item?.name || <?= tfSafeJson(t('common_user', 'User'), JSON_UNESCAPED_UNICODE) ?>,
        score: parsed.score,
        note: parsed.note,
        createdAt: item?.created_at || ''
      };
    })
    .filter(Boolean)
    .reverse();

  if (!feedbackItems.length) {
    const empty = document.createElement('div');
    empty.className = 'evaluation';
    empty.textContent = feedbackEmptyLabel;
    feedbackHistory.appendChild(empty);
    return;
  }

  feedbackItems.forEach((item) => {
    const card = document.createElement('div');
    card.className = 'feedback-card';
    const safeNote = item.note && item.note !== '-' ? escapeHtml(item.note) : feedbackNoNoteLabel;
    card.innerHTML = `
      <strong>${escapeHtml(item.name)}</strong>
      <div class="feedback-meta">
        <span>${feedbackScoreLabel}: ${Math.max(0, Math.min(5, Number(item.score) || 0))}/5</span>
        <span>${escapeHtml(formatMessageTime(item.createdAt || '')) || feedbackTitleLabel}</span>
      </div>
      <div class="feedback-note"><span class="sr-only">${feedbackNoteLabel}: </span>${safeNote}</div>
    `;
    feedbackHistory.appendChild(card);
  });
};

const loadSession = async () => {
  if (!sessionCode || pollingInFlight) return;
  pollingInFlight = true;
  try {
    const data = await requestJson(`?action=interview-get&code=${encodeURIComponent(sessionCode)}`);
    if (data && data.success && data.session) {
      const s = data.session;
      currentUserRole = s.current_user_role || currentUserRole;
      updateTimerControls();
      if (s.code_snapshot && !editor.dataset.dirty) {
        editor.value = s.code_snapshot;
        const firstBoard = boardsState.boards[0];
        if (firstBoard) {
          firstBoard.code = s.code_snapshot;
          saveBoardsState();
        }
      }
      if (s.boards_snapshot && !boardsDirty && !editor.dataset.dirty) {
        const incoming = s.boards_snapshot;
        if (incoming && Array.isArray(incoming.boards) && incoming.boards.length) {
          boardsState = incoming;
          saveBoardsState();
          renderBoardTabs();
          const active = getActiveBoard();
          if (active && editor) {
            editor.value = active.code || '';
            editor.dataset.dirty = '1';
          }
          updateBoardUi();
        }
      }
      const serverTimer = Math.max(0, Number(s.remaining_seconds || 0));
      const serverRunning = !!Number(s.is_running || 0);
      if (!timerRunning || remoteTimerMode) {
        timer = serverTimer;
        timerValue.textContent = formatTime(timer);
        syncGlobalTimer();
      }
      if (serverRunning && !timerRunning) {
        startTimer(currentUserRole !== 'owner');
      } else if (!serverRunning && timerRunning && remoteTimerMode) {
        pauseTimer(true);
        timer = serverTimer;
        timerValue.textContent = formatTime(timer);
        syncGlobalTimer();
      }
      renderParticipants(s.participants || []);
      renderMessages(s.messages || []);
      renderFeedbackHistory(s.messages || []);
    }
  } catch (e) {
    console.warn('Interview polling skipped:', e && e.message ? e.message : 'unknown');
  } finally {
    pollingInFlight = false;
  }
};

const performSaveCode = async () => {
  if (!sessionCode) return;
  const active = getActiveBoard();
  if (active && editor) {
    active.code = editor.value;
    saveBoardsState();
  }
  await requestJson('?action=interview-code-save', { code: sessionCode, snapshot: editor.value, boards: boardsState });
};

const saveCode = async () => {
  if (saveInFlight) {
    pendingSave = true;
    return;
  }
  saveInFlight = true;
  try {
    await performSaveCode();
  } catch (e) {
    console.warn('Interview code save failed:', e && e.message ? e.message : 'unknown');
  } finally {
    saveInFlight = false;
    if (pendingSave) {
      pendingSave = false;
      saveCode();
    }
  }
};

let saveTimer = null;
editor.addEventListener('input', () => {
  editor.dataset.dirty = '1';
  boardsDirty = true;
  if (saveTimer) clearTimeout(saveTimer);
  saveTimer = setTimeout(saveCode, 600);
});

const sendMessage = async () => {
  const input = document.getElementById('chatInput');
  const msg = input.value.trim();
  if (!msg) return;
  setInlineStatus(chatStatus, '', 'info');
  await requestJson('?action=interview-message', { code: sessionCode, message: msg });
  input.value = '';
  setInlineStatus(chatStatus, <?= tfSafeJson(t('common_saved', 'Сохранено'), JSON_UNESCAPED_UNICODE) ?>, 'success');
  loadSession();
};

if (inviteBtnTop) {
  inviteBtnTop.addEventListener('click', async () => {
    const url = `${window.location.origin}${window.location.pathname}?action=interview-room&code=${encodeURIComponent(sessionCode)}`;
    try {
      await navigator.clipboard.writeText(url);
      const original = inviteBtnTop.innerHTML;
      inviteBtnTop.innerHTML = `<i class="fas fa-check"></i> ${<?= tfSafeJson(t('interview_copied', 'Copied'), JSON_UNESCAPED_UNICODE) ?>}`;
      setTimeout(() => { inviteBtnTop.innerHTML = original; }, 1200);
    } catch (e) {
      alert(url);
    }
  });
}

const formatTime = (t) => {
  const h = String(Math.floor(t / 3600)).padStart(2, '0');
  const m = String(Math.floor((t % 3600) / 60)).padStart(2, '0');
  const s = String(t % 60).padStart(2, '0');
  return `${h}:${m}:${s}`;
};

const updateTimerControls = () => {
  const ownerOnly = currentUserRole !== 'owner';
  document.getElementById('timerStart')?.toggleAttribute('disabled', ownerOnly);
  document.getElementById('timerPause')?.toggleAttribute('disabled', ownerOnly);
};

timerValue.textContent = formatTime(timer);
syncGlobalTimer();
updateTimerControls();

const startTimer = (syncOnly = false) => {
  if (timerRunning) return;
  remoteTimerMode = !!syncOnly;
  timerRunning = true;
  timerInterval = setInterval(() => {
    timer += 1;
    timerValue.textContent = formatTime(timer);
    syncGlobalTimer();
    if (!syncOnly && currentUserRole === 'owner') {
      requestJson('?action=interview-timer-update', { code: sessionCode, remaining: timer, is_running: 1 }).catch(() => {});
    }
  }, 1000);
};

const pauseTimer = (silent = false) => {
  if (!timerRunning) return;
  timerRunning = false;
  remoteTimerMode = false;
  clearInterval(timerInterval);
  timerInterval = null;
  syncGlobalTimer();
  if (!silent && currentUserRole === 'owner') {
    requestJson('?action=interview-timer-update', { code: sessionCode, remaining: timer, is_running: 0 }).catch(() => {});
  }
};

const endInterview = async () => {
  const ok = confirm(<?= tfSafeJson(t('interview_end_confirm', 'Завершить собеседование?'), JSON_UNESCAPED_UNICODE) ?>);
  if (!ok) return;
  pauseTimer();
  try {
    await requestJson('?action=interview-end', { code: sessionCode });
  } catch (e) {
    setInlineStatus(chatStatus, e && e.message ? e.message : serverErrorLabel, 'error');
    return;
  }
  try { localStorage.removeItem(boardStorageKey); } catch (e) { /* ignore */ }
  window.location.href = '?action=interview';
};
document.getElementById('endInterviewBtnTop')?.addEventListener('click', endInterview);

const runBtn = document.getElementById('runCode');
const getBoardByKind = (kind) => boardsState.boards.find(b => b.kind === kind);
const syncActiveBoardFromEditor = () => {
  const active = getActiveBoard();
  if (active && editor) {
    active.code = editor.value;
  }
};
const buildPreviewDoc = (html, css, js) => {
  const safeCss = css || '';
  const safeJs = (js || '').replace(/<\/script>/gi, '<\\/script>');
  const hasHtmlTag = /<html[\s>]/i.test(html || '');
  const hasHeadTag = /<head[\s>]/i.test(html || '');
  const hasBodyTag = /<body[\s>]/i.test(html || '');
  if (!hasHtmlTag) {
    return `<!doctype html><html><head><meta charset="utf-8"><style>${safeCss}</style></head><body>${html || ''}<script>${safeJs}<\/script></body></html>`;
  }
  let doc = html || '';
  if (hasHeadTag) {
    doc = doc.replace(/<\/head>/i, `<style>${safeCss}</style></head>`);
  } else {
    doc = doc.replace(/<html[^>]*>/i, (m) => `${m}<head><style>${safeCss}</style></head>`);
  }
  if (hasBodyTag) {
    doc = doc.replace(/<\/body>/i, `<script>${safeJs}<\/script></body>`);
  } else {
    doc += `<script>${safeJs}<\/script>`;
  }
  return doc;
};
const renderPreview = () => {
  if (!output) return;
  syncActiveBoardFromEditor();
  const active = getActiveBoard();
  if (!active) return;
  let html = '';
  let css = '';
  let js = '';
  if (active.kind === 'html') {
    html = active.code || '';
  } else if (active.kind === 'css') {
    css = active.code || '';
  } else if (active.kind === 'js') {
    js = active.code || '';
  } else {
    html = active.code || '';
  }
  const doc = buildPreviewDoc(html, css, js);
  output.classList.add('is-preview');
  output.innerHTML = `<iframe class="preview-frame" sandbox="allow-scripts allow-forms allow-modals" title="${previewTitle}"></iframe>`;
  const frame = output.querySelector('iframe');
  if (frame) {
    frame.srcdoc = doc;
  }
};
const clearPreview = () => {
  if (!output) return;
  output.classList.remove('is-preview');
  output.textContent = `${<?= tfSafeJson(t('interview_output', 'Output'), JSON_UNESCAPED_UNICODE) ?>}:`;
};
const allowedJudgeLangs = ['cpp', 'python', 'c', 'csharp', 'java', 'js', 'ts', 'go', 'rust', 'php', 'ruby', 'swift', 'kotlin', 'scala', 'dart', 'sql'];
runBtn?.addEventListener('click', async () => {
  if (!output || runInFlight) return;
  const active = getActiveBoard();
  if (active && active.kind !== 'lc') {
    renderPreview();
    return;
  }
  const langRaw = languageSelect?.value || 'cpp';
  const lang = normalizeJudgeLanguage(langRaw);
  const problemId = Number(problemSelect?.value || 0);
  if (!problemId) {
    output.textContent = <?= tfSafeJson(t('interview_problem_required', 'Выберите задачу для проверки.'), JSON_UNESCAPED_UNICODE) ?>;
    return;
  }
  if (!allowedJudgeLangs.includes(lang)) {
    output.textContent = <?= tfSafeJson(t('interview_language_limited', 'Доступные языки: C++, Python, C, C#, Java, JavaScript, TypeScript, Go, Rust, PHP, Ruby, Swift, Kotlin, Scala, Dart.'), JSON_UNESCAPED_UNICODE) ?>;
    return;
  }
  runInFlight = true;
  if (runBtn) runBtn.disabled = true;
  output.textContent = <?= tfSafeJson(t('interview_running', 'Запуск...'), JSON_UNESCAPED_UNICODE) ?>;
  try {
    const data = await requestJson('?action=interview-submit', {
      problem_id: problemId,
      language: lang,
      code: editor?.value || ''
    });
    const lines = [];
    const verdict = data.passed ? <?= tfSafeJson(t('contest_accepted', 'Accepted'), JSON_UNESCAPED_UNICODE) ?> : <?= tfSafeJson(t('contest_need_fix', 'Need to fix solution'), JSON_UNESCAPED_UNICODE) ?>;
    lines.push(`${verdict} (${data.checks_passed}/${data.checks_total})`);
    if (Array.isArray(data.results)) {
      data.results.forEach((r, idx) => {
        const st = r && r.passed ? okLabel : waLabel;
        lines.push(`${testLabel} ${idx + 1}: ${st}`);
      });
    }
    output.textContent = lines.join('\n');
  } catch (e) {
    output.textContent = e && e.message ? e.message : <?= tfSafeJson(t('common_server_error', 'Ошибка сервера'), JSON_UNESCAPED_UNICODE) ?>;
  } finally {
    runInFlight = false;
    if (runBtn) runBtn.disabled = false;
  }
});

document.getElementById('clearOutput')?.addEventListener('click', () => {
  clearPreview();
});

document.getElementById('sendMsg')?.addEventListener('click', () => {
  sendMessage().catch((e) => {
    setInlineStatus(chatStatus, e && e.message ? e.message : serverErrorLabel, 'error');
  });
});
document.getElementById('chatInput')?.addEventListener('keydown', (event) => {
  if (event.key === 'Enter') {
    event.preventDefault();
    sendMessage().catch((e) => {
      setInlineStatus(chatStatus, e && e.message ? e.message : serverErrorLabel, 'error');
    });
  }
});

const emojiButtons = document.querySelectorAll('#emojiRating .emoji-btn');
const emojiCaption = document.getElementById('emojiCaption');
const emojiStorageKey = `tfEval:${sessionCode}`;
const emojiLabels = {
  1: <?= tfSafeJson(t('interview_rating_1', 'Сложно'), JSON_UNESCAPED_UNICODE) ?>,
  2: <?= tfSafeJson(t('interview_rating_2', 'Нормально'), JSON_UNESCAPED_UNICODE) ?>,
  3: <?= tfSafeJson(t('interview_rating_3', 'Хорошо'), JSON_UNESCAPED_UNICODE) ?>,
  4: <?= tfSafeJson(t('interview_rating_4', 'Очень хорошо'), JSON_UNESCAPED_UNICODE) ?>,
  5: <?= tfSafeJson(t('interview_rating_5', 'Отлично'), JSON_UNESCAPED_UNICODE) ?>
};
const setEmojiRating = (score) => {
  emojiButtons.forEach(btn => btn.classList.toggle('active', Number(btn.dataset.score) === score));
  if (emojiCaption) emojiCaption.textContent = emojiLabels[score] || <?= tfSafeJson(t('interview_evaluation_pick', 'Выберите оценку'), JSON_UNESCAPED_UNICODE) ?>;
  localStorage.setItem(emojiStorageKey, String(score));
};
emojiButtons.forEach(btn => {
  btn.addEventListener('click', () => setEmojiRating(Number(btn.dataset.score)));
});
const storedRating = Number(localStorage.getItem(emojiStorageKey) || 0);
if (storedRating) setEmojiRating(storedRating);

const tabs = document.querySelectorAll('.tab');
const chatPanel = document.getElementById('chatPanel');
const evalPanel = document.getElementById('evalPanel');
const openEvaluationTab = () => {
  tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === 'evaluation'));
  chatPanel.style.display = 'none';
  evalPanel.style.display = 'block';
};
const openChatTab = () => {
  tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === 'chat'));
  chatPanel.style.display = 'block';
  evalPanel.style.display = 'none';
};

tabs.forEach(tab => {
  tab.addEventListener('click', () => {
    if (tab.dataset.tab === 'chat') {
      openChatTab();
    } else {
      openEvaluationTab();
    }
  });
});

document.getElementById('timerStart')?.addEventListener('click', startTimer);
document.getElementById('timerPause')?.addEventListener('click', pauseTimer);

document.querySelectorAll('[data-close]').forEach((btn) => {
  btn.addEventListener('click', () => {
    btn.closest('.modal')?.classList.remove('open');
  });
});

shortcutsBtn?.addEventListener('click', () => shortcutsModal?.classList.add('open'));
renameBtn?.addEventListener('click', () => {
  if (renameInput && boardTitleEl) renameInput.value = boardTitleEl.textContent || '';
  renameModal?.classList.add('open');
});
settingsBtn?.addEventListener('click', () => settingsMenu?.classList.toggle('open'));
boardMenuBtn?.addEventListener('click', () => boardMenuList?.classList.toggle('open'));
document.querySelectorAll('[data-board-template]').forEach((btn) => {
  btn.addEventListener('click', () => createBoardFromTemplate(btn.dataset.boardTemplate || 'lc'));
});
confirmRename?.addEventListener('click', () => {
  const nextTitle = (renameInput?.value || '').trim();
  if (!nextTitle) return;
  if (boardTitleEl) boardTitleEl.textContent = nextTitle;
  const active = getActiveBoard();
  if (active) {
    active.title = nextTitle;
    renderBoardTabs();
    saveBoardsState();
    saveCode();
  }
  renameModal?.classList.remove('open');
});
equipmentTestBtn?.addEventListener('click', () => {
  const checks = [
    `Clipboard API: ${navigator.clipboard ? 'available' : 'not available'}`,
    `Local storage: ${typeof window.localStorage !== 'undefined' ? 'available' : 'not available'}`,
    `Media devices: ${(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) ? 'available' : 'not available'}`
  ];
  if (equipmentStatus) {
    equipmentStatus.innerHTML = checks.map(item => `<div>${escapeHtml(item)}</div>`).join('');
  }
  equipmentModal?.classList.add('open');
  settingsMenu?.classList.remove('open');
});
openEvaluationBtn?.addEventListener('click', () => {
  openEvaluationTab();
  settingsMenu?.classList.remove('open');
});
saveEvaluationBtn?.addEventListener('click', () => {
  const note = (evaluationNote?.value || '').trim();
  const score = Number(localStorage.getItem(emojiStorageKey) || 0);
  localStorage.setItem(evaluationStorageKey, note);
  if (!score && !note) {
    if (evaluationStatus) {
      evaluationStatus.textContent = <?= tfSafeJson(t('interview_evaluation_pick', 'Выберите оценку'), JSON_UNESCAPED_UNICODE) ?>;
    }
    return;
  }
  const feedbackMessage = `[Feedback] score=${score || 0}; note=${note || '-'}`;
  requestJson('?action=interview-message', { code: sessionCode, message: feedbackMessage })
    .then((data) => {
      if (evaluationStatus) {
        evaluationStatus.textContent = <?= tfSafeJson(t('common_saved', 'Сохранено'), JSON_UNESCAPED_UNICODE) ?>;
      }
      loadSession();
    })
    .catch((e) => {
      if (evaluationStatus) {
        evaluationStatus.textContent = e && e.message ? e.message : serverErrorLabel;
      }
    });
});
if (evaluationNote) {
  evaluationNote.value = localStorage.getItem(evaluationStorageKey) || '';
}
backToInterviews?.addEventListener('click', () => {
  window.location.href = '?action=interview';
});
document.addEventListener('click', (event) => {
  if (!settingsBtn?.contains(event.target) && !settingsMenu?.contains(event.target)) {
    settingsMenu?.classList.remove('open');
  }
  if (!boardMenuBtn?.contains(event.target) && !boardMenuList?.contains(event.target)) {
    boardMenuList?.classList.remove('open');
  }
});
document.querySelectorAll('.modal').forEach((modal) => {
  modal.addEventListener('click', (event) => {
    if (event.target === modal) modal.classList.remove('open');
  });
});

attachSmartIndent(editor, () => languageSelect?.value || 'cpp');

renderParticipants(initialSession.participants || []);
renderMessages(initialSession.messages || []);
renderFeedbackHistory(initialSession.messages || []);
loadSession();
setInterval(loadSession, 5000);
</script>
</body>
</html>
