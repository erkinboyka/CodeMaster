<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="neon">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('CodeMaster') . ' - ' . __('IT Education & Career Platform'))</title>
    <meta name="description" content="@yield('description', __('CodeMaster - your gateway to IT education, career development, courses, roadmaps, contests and job opportunities.'))">
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        (function() {
            var t = localStorage.getItem('theme') || 'neon';
            var l = localStorage.getItem('theme-light') === '1';
            document.documentElement.setAttribute('data-theme', l ? t + '-light' : t);
        })();
    </script>
    <style>[x-cloak]{display:none!important}</style>
    <style>
        .reveal-up, .reveal-down, .reveal-left, .reveal-right, .reveal-scale {
            opacity: 0;
            transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .reveal-up { transform: translateY(50px); }
        .reveal-down { transform: translateY(-50px); }
        .reveal-left { transform: translateX(-50px); }
        .reveal-right { transform: translateX(50px); }
        .reveal-scale { transform: scale(0.92); }

        .reveal-up.visible, .reveal-down.visible, .reveal-left.visible, .reveal-right.visible, .reveal-scale.visible {
            opacity: 1;
            transform: translateY(0) translateX(0) scale(1);
        }

        .reveal-up.no-init, .reveal-down.no-init, .reveal-left.no-init, .reveal-right.no-init, .reveal-scale.no-init {
            opacity: 0;
        }
    </style>
    <style id="theme-tailwind-overrides">
        .bg-indigo-600{background:var(--accent)!important}
        .bg-indigo-500{background:var(--accent)!important}
        .bg-gradient-to-r.from-indigo-500.to-purple-600{background:linear-gradient(to right,var(--accent),var(--accent-2))!important}
        .bg-gradient-to-br.from-indigo-500.to-purple-600{background:linear-gradient(to bottom right,var(--accent),var(--accent-2))!important}
        .from-indigo-500{--tw-gradient-from:var(--accent)!important}
        .to-purple-600{--tw-gradient-to:var(--accent-2)!important}
        .from-purple-500{--tw-gradient-from:var(--accent-2)!important}
        .to-indigo-500{--tw-gradient-to:var(--accent)!important}
        .from-cyan-500{--tw-gradient-from:var(--accent-3)!important}
        .to-blue-400{--tw-gradient-to:var(--accent)!important}
        .text-indigo-600,.text-indigo-500,.text-indigo-400{color:var(--accent)!important}
        .text-purple-600,.text-purple-500{color:var(--accent-2)!important}
        .text-gray-900,.text-gray-800,.text-gray-700{color:var(--text)!important}
        .text-gray-500,.text-gray-400,.text-gray-600{color:var(--text-muted)!important}
        .text-gray-300{color:var(--text-muted)!important}
        .bg-white{background:var(--card)!important}
        .bg-gray-900{background:var(--bg)!important}
        .bg-gray-800{background:var(--bg-secondary)!important}
        .bg-gray-100{background:var(--bg-secondary)!important}
        .bg-gray-50{background:var(--bg-secondary)!important}
        .bg-indigo-100{background:color-mix(in srgb, var(--accent) 15%, var(--card))!important}
        .bg-purple-100{background:color-mix(in srgb, var(--accent-2) 15%, var(--card))!important}
        .bg-indigo-50{background:color-mix(in srgb, var(--accent) 10%, var(--card))!important}
        .border-gray-200,.border-gray-100{border-color:var(--border)!important}
        .border-gray-800{border-color:var(--border)!important}
        .border-indigo-100{border-color:color-mix(in srgb, var(--accent) 15%, var(--card))!important}
        .border-indigo-600{border-color:var(--accent)!important}
        .hover\:bg-gray-800:hover{background:var(--bg-secondary)!important}
        .hover\:bg-gray-50:hover{background:var(--bg-secondary)!important}
        .hover\:bg-indigo-50:hover{background:color-mix(in srgb, var(--accent) 10%, var(--card))!important}
        .hover\:bg-indigo-600:hover{background:var(--accent-hover)!important}
        .hover\:text-gray-700:hover,.hover\:text-gray-600:hover{color:var(--text)!important}
        .hover\:text-indigo-600:hover,.hover\:text-indigo-800:hover{color:var(--accent-hover)!important}
        .ring-indigo-500{--tw-ring-color:var(--accent)!important}
        .focus\:ring-indigo-500:focus{--tw-ring-color:var(--accent)!important}
        .focus\:border-indigo-500:focus{border-color:var(--accent)!important}
        .focus\:border-transparent:focus{border-color:transparent!important}
        .shadow-indigo-200{--tw-shadow-color:color-mix(in srgb, var(--accent) 20%, transparent)!important}
        .divide-gray-200>:not([hidden])~:not([hidden]){border-color:var(--border)!important}
        [data-theme="neon"] .bg-indigo-600{box-shadow:var(--glow-shadow-accent)!important}
        [data-theme="neon"] .hover\:bg-indigo-600:hover{box-shadow:var(--glow-shadow-accent)!important}
    </style>
    @yield('head')
</head>
<body>
    @include('components.preloader')
    @include('components.header')
    <x-notification />
    <main style="min-height:100vh" class="@yield('main-class', 'page-with-header')">
        @yield('content')
    </main>
    @include('components.footer')
    @yield('scripts')
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var revealElements = document.querySelectorAll('.reveal-up, .reveal-down, .reveal-left, .reveal-right, .reveal-scale, .stagger');

            if (!('IntersectionObserver' in window)) {
                revealElements.forEach(function(el) { el.classList.add('visible'); });
                return;
            }

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var el = entry.target;
                        var delay = el.getAttribute('data-delay') || 0;
                        var idx = el.getAttribute('data-stagger');
                        if (idx !== null) delay = parseInt(idx) * 0.08;
                        el.style.transitionDelay = delay + 's';
                        el.classList.add('visible');
                        observer.unobserve(el);
                    }
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

            revealElements.forEach(function(el) { observer.observe(el); });

            setTimeout(function() {
                document.querySelectorAll('.reveal-up:not(.visible), .reveal-down:not(.visible), .reveal-left:not(.visible), .reveal-right:not(.visible), .reveal-scale:not(.visible), .stagger:not(.visible)')
                    .forEach(function(el) { el.classList.add('visible'); });
            }, 3000);
        });

        setTimeout(function() {
            if (typeof Alpine === 'undefined') {
                document.querySelectorAll('[x-cloak]').forEach(function(el) {
                    el.removeAttribute('x-cloak');
                });
            }
        }, 5000);
    </script>
</body>
</html>
