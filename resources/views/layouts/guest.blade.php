<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" data-theme="neon">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('CodeMaster') . ' - ' . __('Authentication'))</title>
    <script>
        (function() {
            var t = localStorage.getItem('theme') || 'neon';
            var l = localStorage.getItem('theme-light') === '1';
            document.documentElement.setAttribute('data-theme', l ? t + '-light' : t);
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: ['selector', '[data-theme]'],
            theme: {
                extend: {
                    colors: {
                        gray: {
                            50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0',
                            300: '#cbd5e1', 400: '#94a3b8', 500: '#64748b',
                            600: '#475569', 700: '#334155', 800: '#1e293b', 900: '#0f172a',
                        },
                        indigo: {
                            50: 'color-mix(in srgb, var(--accent) 10%, white)',
                            100: 'color-mix(in srgb, var(--accent) 15%, white)',
                            200: 'color-mix(in srgb, var(--accent) 25%, white)',
                            300: 'color-mix(in srgb, var(--accent) 40%, white)',
                            400: 'color-mix(in srgb, var(--accent) 70%, white)',
                            500: 'var(--accent)', 600: 'var(--accent)', 700: 'var(--accent-hover)',
                            800: 'var(--accent-hover)', 900: 'var(--accent)',
                        },
                        purple: {
                            50: 'color-mix(in srgb, var(--accent-2) 10%, white)',
                            500: 'var(--accent-2)', 600: 'var(--accent-2)',
                        },
                        cyan: {
                            400: 'color-mix(in srgb, var(--accent-3) 70%, white)',
                            500: 'var(--accent-3)',
                        },
                        white: '#ffffff',
                    },
                    borderRadius: { '2xl': 'var(--radius-lg)', '3xl': 'var(--radius-xl)', '4xl': '2rem' },
                    boxShadow: { 'glow': 'var(--glow-shadow)', 'card': '0 2px 8px var(--shadow)', 'card-hover': 'var(--card-shadow)' },
                    fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="{{ asset('css/innova.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('build/assets/app-DUr89oQr.js') }}" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <style id="theme-tailwind-overrides">
        .bg-indigo-600{background:var(--accent)!important}
        .bg-gradient-to-r.from-indigo-500.to-purple-600{background:linear-gradient(to right,var(--accent),var(--accent-2))!important}
        .from-indigo-500{--tw-gradient-from:var(--accent)!important}
        .to-purple-600{--tw-gradient-to:var(--accent-2)!important}
        .text-indigo-600,.text-indigo-500{color:var(--accent)!important}
        .text-gray-900,.text-gray-800,.text-gray-700{color:var(--text)!important}
        .text-gray-500,.text-gray-400,.text-gray-600{color:var(--text-muted)!important}
        .bg-white{background:var(--card)!important}
        .bg-gray-50{background:var(--bg-secondary)!important}
        .bg-gray-100{background:var(--bg-secondary)!important}
        .bg-indigo-50{background:color-mix(in srgb, var(--accent) 10%, var(--card))!important}
        .border-gray-200{border-color:var(--border)!important}
        .hover\:bg-gray-50:hover{background:var(--bg-secondary)!important}
        .hover\:bg-indigo-50:hover{background:color-mix(in srgb, var(--accent) 10%, var(--card))!important}
        .hover\:text-gray-700:hover{color:var(--text)!important}
        .hover\:text-indigo-800:hover{color:var(--accent-hover)!important}
        .ring-indigo-500{--tw-ring-color:var(--accent)!important}
        .focus\:ring-indigo-500:focus{--tw-ring-color:var(--accent)!important}
        .focus\:border-indigo-500:focus{border-color:var(--accent)!important}
        .shadow-indigo-200{--tw-shadow-color:color-mix(in srgb, var(--accent) 20%, transparent)!important}
        [data-theme="neon"] .bg-indigo-600{box-shadow:var(--glow-shadow-accent)!important}
    </style>
    <style>
        /* ══════════════════════════════════════════
           AUTH PAGE — TERMINAL STYLE
           ══════════════════════════════════════════ */
        .auth-bg {
            background: var(--bg);
            position: relative;
        }
        .auth-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 50%, color-mix(in srgb, var(--accent) 8%, transparent) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, color-mix(in srgb, var(--accent-2) 6%, transparent) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, color-mix(in srgb, var(--accent-3) 5%, transparent) 0%, transparent 50%);
            pointer-events: none;
        }
        .auth-bg .code-rain {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }
        .auth-bg .code-rain span {
            position: absolute;
            color: var(--accent);
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            opacity: 0;
            animation: fall linear infinite;
            will-change: transform;
            text-shadow: 0 0 8px color-mix(in srgb, var(--accent) 40%, transparent);
        }
        @keyframes fall {
            0% { transform: translateY(-5vh); opacity: 0; }
            10% { opacity: 0.6; }
            90% { opacity: 0.6; }
            100% { transform: translateY(105vh); opacity: 0; }
        }

        .auth-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 60px rgba(0, 0, 0, 0.15);
        }
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), var(--accent-2), var(--accent-3));
        }

        .auth-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.03em;
            margin-bottom: 4px;
        }
        .auth-subtitle {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: var(--text-muted);
            opacity: 0.7;
        }

        .auth-form { display: flex; flex-direction: column; gap: 16px; }

        .auth-field { display: flex; flex-direction: column; gap: 6px; }

        .auth-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .auth-label-prefix { color: var(--accent); font-weight: 700; }

        .auth-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .auth-input {
            width: 100%;
            height: 48px;
            padding: 0 16px 0 42px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 14px;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text);
            transition: all 0.2s;
            outline: none;
        }
        .auth-input::placeholder {
            color: var(--text-muted);
            opacity: 0.5;
            font-family: 'JetBrains Mono', monospace;
        }
        .auth-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 15%, transparent);
        }
        .auth-input-icon {
            position: absolute;
            left: 14px;
            font-size: 13px;
            color: var(--text-muted);
            opacity: 0.6;
            pointer-events: none;
            font-family: 'JetBrains Mono', monospace;
            display: flex;
            align-items: center;
            width: 16px;
            justify-content: center;
        }
        .auth-input:focus ~ .auth-input-icon,
        .auth-input:focus + .auth-input-icon { color: var(--accent); opacity: 1; }
        .auth-input-action {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            font-size: 14px;
            transition: color 0.2s;
        }
        .auth-input-action:hover { color: var(--accent); }

        .auth-select {
            appearance: none;
            cursor: pointer;
        }

        .auth-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            height: 48px;
            background: var(--gradient);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .auth-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 30px color-mix(in srgb, var(--accent) 30%, transparent);
        }
        .auth-submit:active { transform: translateY(0) scale(0.98); }

        .auth-btn-secondary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 48px;
            padding: 0 20px;
            background: var(--bg);
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
            border: 1px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .auth-btn-secondary:hover {
            border-color: var(--accent);
            color: var(--text);
        }

        .auth-alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: color-mix(in srgb, #ef4444 10%, var(--card));
            border: 1px solid color-mix(in srgb, #ef4444 25%, var(--border));
            border-radius: 12px;
            color: #ef4444;
            font-size: 13px;
        }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 4px 0;
        }
        .auth-divider::before, .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .auth-divider span {
            font-size: 12px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .auth-social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 44px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
        }
        .auth-social-btn:hover {
            border-color: var(--accent);
            color: var(--text);
            background: var(--accent-glow);
        }

        .auth-footer-text {
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }
        .auth-link-bold {
            color: var(--accent);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        .auth-link-bold:hover { color: var(--accent-hover); }
        .auth-link {
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        .auth-link:hover { color: var(--accent); }

        .auth-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
            cursor: pointer;
        }
        .auth-checkbox-input {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid var(--border);
            background: var(--bg);
            cursor: pointer;
            accent-color: var(--accent);
        }

        .auth-step {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            border: 2px solid var(--border);
            color: var(--text-muted);
            transition: all 0.3s;
        }
        .auth-step.active {
            border-color: var(--accent);
            color: var(--accent);
            background: color-mix(in srgb, var(--accent) 10%, transparent);
            box-shadow: 0 0 16px color-mix(in srgb, var(--accent) 20%, transparent);
        }
        .auth-step.done {
            border-color: #22c55e;
            color: #22c55e;
            background: color-mix(in srgb, #22c55e 10%, transparent);
        }

        .auth-role-card { cursor: pointer; }
        .auth-role-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 16px;
            border: 2px solid var(--border);
            border-radius: 12px;
            transition: all 0.2s;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
        }
        .auth-role-inner i { font-size: 20px; }
        .auth-role-inner:hover { border-color: color-mix(in srgb, var(--accent) 40%, var(--border)); }
        .auth-role-inner.active {
            border-color: var(--accent);
            color: var(--accent);
            background: color-mix(in srgb, var(--accent) 8%, transparent);
        }

        .auth-skill-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            background: color-mix(in srgb, var(--accent) 12%, transparent);
            color: var(--accent);
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        .auth-skill-tag button {
            background: none;
            border: none;
            color: var(--accent);
            font-size: 14px;
            line-height: 1;
            cursor: pointer;
            opacity: 0.6;
        }
        .auth-skill-tag button:hover { opacity: 1; }

        .auth-dropdown {
            margin-top: 4px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        .auth-dropdown-item {
            display: block;
            width: 100%;
            text-align: left;
            padding: 10px 14px;
            font-size: 13px;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-secondary);
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
        }
        .auth-dropdown-item:hover {
            background: var(--accent-glow);
            color: var(--accent);
        }

        .auth-logo-wrap {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .auth-logo-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            box-shadow: 0 4px 20px color-mix(in srgb, var(--accent) 30%, transparent);
        }
        .auth-logo-text {
            font-family: 'JetBrains Mono', monospace;
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.02em;
        }
        .auth-logo-accent { color: var(--accent); }

        @media (max-width: 480px) {
            .auth-card { padding: 24px 20px; }
        }
    </style>
    @yield('head')
</head>
<body class="font-sans antialiased">
    @include('components.preloader')

    <div class="min-h-screen auth-bg relative overflow-hidden flex items-center justify-center p-4 lg:p-8">
        <div class="code-rain inset-0" id="codeRain"></div>

        <div class="relative z-10 w-full max-w-5xl flex flex-col lg:flex-row items-center gap-8 lg:gap-16">
            <div class="hidden lg:flex flex-col flex-1 text-left">
                <a href="{{ route('home') }}" class="auth-logo-wrap mb-8">
                    <div class="auth-logo-icon"><i class="fas fa-terminal"></i></div>
                    <span class="auth-logo-text"><span class="auth-logo-accent">&gt;</span> Code<span class="auth-logo-accent">Master</span><span style="animation:cursorBlink 1s step-end infinite; margin-left:1px; color:var(--accent)">_</span></span>
                </a>
                <h1 class="text-4xl font-extrabold tracking-tight mb-4" style="color:var(--text)">
                    {{ __('Build your') }}<br>
                    <span style="background:var(--gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">{{ __('tech career') }}</span>
                </h1>
                <p class="text-lg mb-8" style="color:var(--text-muted);max-width:380px">
                    {{ __('Courses, contests, roadmap & job opportunities — all in one place.') }}
                </p>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:var(--accent-glow)">
                            <i class="fas fa-graduation-cap" style="color:var(--accent)"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold" style="color:var(--text)">{{ __('120+ Courses') }}</div>
                            <div class="text-xs" style="color:var(--text-muted)">{{ __('IT, design, devops') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:var(--accent-glow)">
                            <i class="fas fa-trophy" style="color:var(--accent)"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold" style="color:var(--text)">{{ __('Live Contests') }}</div>
                            <div class="text-xs" style="color:var(--text-muted)">{{ __('Compete & win prizes') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:var(--accent-glow)">
                            <i class="fas fa-briefcase" style="color:var(--accent)"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold" style="color:var(--text)">{{ __('Job Board') }}</div>
                            <div class="text-xs" style="color:var(--text-muted)">{{ __('Top companies hiring') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-md">
                <div class="text-center mb-6 lg:hidden">
                    <a href="{{ route('home') }}" class="auth-logo-wrap justify-center">
                        <div class="auth-logo-icon"><i class="fas fa-terminal"></i></div>
                        <span class="auth-logo-text"><span class="auth-logo-accent">&gt;</span> Code<span class="auth-logo-accent">Master</span><span style="animation:cursorBlink 1s step-end infinite; margin-left:1px; color:var(--accent)">_</span></span>
                    </a>
                </div>

                <div class="auth-card">
                    <x-notification />
                    @yield('content')
                </div>

                <div class="text-center mt-6">
                    <a href="{{ route('home') }}" class="auth-link" style="font-size:13px">
                        <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const codeSymbols = ['{', '}', '< />', '</>', '=', '()', '=>', '[]', '&&', '||', 'function', 'const', 'let', 'return', '$', '#', '@', 'class', 'import', 'async', 'await', 'npm', 'git', 'sudo', 'grep', 'echo'];
        const rain = document.getElementById('codeRain');
        if (rain) {
            const count = window.innerWidth < 768 ? 15 : 35;
            for (let i = 0; i < count; i++) {
                const span = document.createElement('span');
                span.textContent = codeSymbols[Math.floor(Math.random() * codeSymbols.length)];
                span.style.left = Math.random() * 100 + '%';
                const dur = Math.random() * 8 + 6;
                span.style.animationDuration = dur + 's';
                span.style.animationDelay = (Math.random() * dur) + 's';
                span.style.fontSize = (Math.random() * 6 + 11) + 'px';
                span.style.opacity = '0';
                rain.appendChild(span);
            }
        }
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
