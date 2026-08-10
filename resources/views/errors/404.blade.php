<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - {{ __('Page Not Found') }} - CodeMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/innova.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        (function() {
            var t = localStorage.getItem('theme') || 'neon';
            var l = localStorage.getItem('theme-light') === '1';
            document.documentElement.setAttribute('data-theme', l ? t + '-light' : t);
        })();
    </script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); min-height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .error-page { text-align: center; padding: 40px 20px; position: relative; }
        .error-terminal {
            width: 420px; border-radius: 16px; overflow: hidden;
            border: 1px solid var(--border); background: var(--bg-secondary);
            box-shadow: 0 25px 80px rgba(0,0,0,0.4);
            margin: 0 auto 32px; text-align: left;
        }
        .error-terminal-bar {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; border-bottom: 1px solid var(--border);
            background: var(--bg);
        }
        .error-dots { display: flex; gap: 6px; }
        .error-dot { width: 12px; height: 12px; border-radius: 50%; }
        .error-dot--red { background: #ff5f57; }
        .error-dot--yellow { background: #febc2e; }
        .error-dot--green { background: #28c840; }
        .error-terminal-title {
            font-family: 'JetBrains Mono', monospace; font-size: 13px;
            color: var(--text-muted);
        }
        .error-terminal-body {
            padding: 20px; font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
        }
        .error-line { margin-bottom: 8px; display: flex; align-items: flex-start; gap: 8px; }
        .error-prompt { color: var(--accent); font-weight: 700; white-space: nowrap; }
        .error-cmd { color: var(--text); }
        .error-output { color: var(--text-muted); padding-left: 20px; }
        .error-output--red { color: #ef4444; font-weight: 600; }
        .error-output--yellow { color: #eab308; }
        .error-cursor {
            display: inline-block; width: 8px; height: 16px;
            background: var(--accent); animation: blink 1s step-end infinite;
            vertical-align: middle; margin-left: 2px;
        }
        @@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }
        .error-code {
            font-size: 120px; font-weight: 900;
            background: var(--gradient); -webkit-background-clip: text;
            -webkit-text-fill-color: transparent; background-clip: text;
            line-height: 1; margin-bottom: 16px;
            font-family: 'JetBrains Mono', monospace;
        }
        .error-msg { font-size: 18px; color: var(--text); font-weight: 600; margin-bottom: 8px; }
        .error-desc { font-size: 14px; color: var(--text-muted); margin-bottom: 32px; max-width: 400px; margin-left: auto; margin-right: auto; }
        .error-actions { display: flex; gap: 12px; justify-content: center; }
        .error-btn {
            padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 600;
            cursor: pointer; transition: all 0.2s; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
            font-family: 'JetBrains Mono', monospace;
        }
        .error-btn-primary { background: var(--accent); color: #fff; }
        .error-btn-primary:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 8px 24px var(--accent-glow-strong); }
        .error-btn-secondary { background: var(--bg-secondary); color: var(--text); border: 1px solid var(--border); }
        .error-btn-secondary:hover { border-color: var(--accent); color: var(--accent); }
        .error-glitch {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 600px; height: 600px; pointer-events: none; opacity: 0.03;
        }
        .error-glitch-line {
            position: absolute; left: 0; right: 0; height: 2px;
            background: var(--accent); animation: glitch 3s infinite;
        }
        @@keyframes glitch {
            0%, 90%, 100% { transform: translateX(0); opacity: 0; }
            92% { transform: translateX(-20px); opacity: 1; }
            94% { transform: translateX(20px); opacity: 0.5; }
            96% { transform: translateX(-10px); opacity: 0; }
        }
        .error-grid {
            position: fixed; inset: 0; pointer-events: none;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 60px 60px; opacity: 0.15;
        }
    </style>
</head>
<body>
    <div class="error-grid"></div>

    <div class="error-page">
        <div class="error-glitch">
            <div class="error-glitch-line" style="top:20%"></div>
            <div class="error-glitch-line" style="top:40%;animation-delay:0.5s"></div>
            <div class="error-glitch-line" style="top:60%;animation-delay:1s"></div>
            <div class="error-glitch-line" style="top:80%;animation-delay:1.5s"></div>
        </div>

        <div class="error-terminal">
            <div class="error-terminal-bar">
                <div class="error-dots">
                    <span class="error-dot error-dot--red"></span>
                    <span class="error-dot error-dot--yellow"></span>
                    <span class="error-dot error-dot--green"></span>
                </div>
                <div class="error-terminal-title">bash</div>
            </div>
            <div class="error-terminal-body">
                <div class="error-line">
                    <span class="error-prompt">$</span>
                    <span class="error-cmd">curl {{ url()->current() }}</span>
                </div>
                <div class="error-line">
                    <span class="error-output error-output--red">HTTP/1.1 404 Not Found</span>
                </div>
                <div class="error-line">
                    <span class="error-output error-output--yellow">Content-Type: text/html</span>
                </div>
                <div class="error-line">
                    <span class="error-output">Page not found on this server.</span>
                </div>
                <div class="error-line">
                    <span class="error-prompt">$</span>
                    <span class="error-cmd">echo $?</span>
                </div>
                <div class="error-line">
                    <span class="error-output error-output--red">1</span>
                </div>
                <div class="error-line">
                    <span class="error-prompt">$</span>
                    <span class="error-cmd">cd /home</span><span class="error-cursor"></span>
                </div>
            </div>
        </div>

        <div class="error-code">404</div>
        <div class="error-msg">{{ __('Page Not Found') }}</div>
        <div class="error-desc">{{ __('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.') }}</div>

        <div class="error-actions">
            <a href="{{ route('home') }}" class="error-btn error-btn-primary">
                <i class="fas fa-home"></i> {{ __('Back to Home') }}
            </a>
            <button onclick="window.history.back()" class="error-btn error-btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Go Back') }}
            </button>
        </div>
    </div>
</body>
</html>
