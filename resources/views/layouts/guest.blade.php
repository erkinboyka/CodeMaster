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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .auth-bg { background: linear-gradient(135deg, #312e81 0%, #4f46e5 30%, #7e22ce 60%, #581c87 100%); }
        .auth-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); }
        [data-theme="neon"] .auth-bg { background: linear-gradient(135deg, var(--bg) 0%, color-mix(in srgb, var(--accent) 40%, var(--bg)) 50%, var(--bg) 100%); }
        [data-theme="neon"] .auth-card,
        [data-theme="vscode"] .auth-card,
        [data-theme="nord"] .auth-card,
        [data-theme="github"] .auth-card { background: var(--card); color: var(--text); border: 1px solid var(--border); }
        .code-rain { position: absolute; overflow: hidden; opacity: 0.08; }
        .code-rain span { position: absolute; color: white; font-family: monospace; font-size: 14px; animation: fall linear infinite; }
        @@keyframes fall { 0% { transform: translateY(-100vh); opacity: 1; } 100% { transform: translateY(100vh); opacity: 0; } }
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
    @yield('head')
</head>
<body class="font-sans antialiased">
    @include('components.preloader')

    <div class="min-h-screen auth-bg relative overflow-hidden flex items-center justify-center p-4">
        <div class="code-rain inset-0" id="codeRain"></div>

        <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-20 left-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
            <div class="absolute top-40 right-10 w-72 h-72 bg-indigo-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse" style="animation-delay: 2s"></div>
            <div class="absolute bottom-20 left-1/3 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse" style="animation-delay: 4s"></div>
        </div>

        <div class="relative z-10 w-full max-w-md">
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center space-x-2">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-code text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold text-white">CodeMaster</span>
                </a>
            </div>

            <div class="auth-card rounded-2xl shadow-2xl p-8">
                <x-notification />
                @yield('content')
            </div>

            <div class="text-center mt-6">
                <a href="{{ route('home') }}" class="text-white/70 hover:text-white text-sm transition">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to home') }}
                </a>
            </div>
        </div>
    </div>

    <script>
        const codeSymbols = ['{', '}', '<', '>', '/', '=', ';', '(', ')', 'function', 'const', 'let', 'var', 'if', 'else', 'return', '$', '#', '@', 'class', 'div', 'span', 'return', '=>', '[]', '&&', '||'];
        const rain = document.getElementById('codeRain');
        for(let i = 0; i < 30; i++) {
            const span = document.createElement('span');
            span.textContent = codeSymbols[Math.floor(Math.random() * codeSymbols.length)];
            span.style.left = Math.random() * 100 + '%';
            span.style.animationDuration = (Math.random() * 5 + 5) + 's';
            span.style.animationDelay = (Math.random() * 5) + 's';
            rain.appendChild(span);
        }
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
