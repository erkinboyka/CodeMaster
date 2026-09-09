<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" data-theme="neon">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#0b1220">
    <title>@yield('title', __('Admin Panel') . ' - ' . __('CodeMaster'))</title>
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
    <script defer src="{{ asset('vendor/alpine/collapse.min.js') }}"></script>
    <script defer src="{{ asset('vendor/alpine/alpine.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
    <script>
        /* CDN fallback: если локальные либы недоступны — догрузить с CDN */
        window.addEventListener('load', function() {
            function loadSrc(src, onload) {
                var s = document.createElement('script');
                s.src = src;
                if (onload) s.onload = onload;
                document.head.appendChild(s);
            }
            if (typeof window.Alpine === 'undefined') {
                loadSrc('https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js', function() {
                    loadSrc('https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js');
                });
            }
            if (typeof window.tinymce === 'undefined') {
                loadSrc('https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js');
            }
        });
    </script>
    <style>
        [x-cloak] { display: none !important; }
        /* Фулскрин TinyMCE — поверх всего + прячем фикс-панели */
        .tox.tox-tinymce.tox-fullscreen { z-index: 100000 !important; }
        body:has(.tox-fullscreen) aside,
        body:has(.tox-fullscreen) header { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-secondary); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); border-bottom: 2px solid var(--border); }
        .admin-table td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border); color: var(--text); font-size: 0.875rem; }
        .admin-table tr:hover td { background: color-mix(in srgb, var(--accent) 3%, var(--card)); }
        .admin-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius-lg, 12px); padding: 1.5rem; }
        .admin-badge { display: inline-flex; align-items: center; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
        .admin-badge-success { background: color-mix(in srgb, #22c55e 15%, transparent); color: #22c55e; }
        .admin-badge-danger { background: color-mix(in srgb, #ef4444 15%, transparent); color: #ef4444; }
        .admin-badge-warning { background: color-mix(in srgb, #f59e0b 15%, transparent); color: #f59e0b; }
        .admin-badge-info { background: color-mix(in srgb, var(--accent) 15%, transparent); color: var(--accent); }
        .admin-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; transition: all 0.15s; cursor: pointer; border: none; }
        .admin-btn-primary { background: var(--accent); color: white; }
        .admin-btn-primary:hover { background: var(--accent-hover); }
        .admin-btn-danger { background: #ef4444; color: white; }
        .admin-btn-danger:hover { background: #dc2626; }
        .admin-btn-ghost { background: transparent; color: var(--text-muted); border: 1px solid var(--border); }
        .admin-btn-ghost:hover { background: var(--bg-secondary); color: var(--text); }
        .admin-input { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; background: var(--bg); color: var(--text); font-size: 0.875rem; transition: border-color 0.15s; }
        .admin-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 15%, transparent); }
        .admin-select { appearance: none; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem; }
        .admin-label { display: block; font-size: 0.875rem; font-weight: 500; color: var(--text); margin-bottom: 0.375rem; }
        .admin-form-group { margin-bottom: 1rem; }
        .admin-sidebar-active { background: var(--card); color: var(--accent) !important; border-right: 3px solid var(--accent); }
        .admin-sidebar-active i { color: var(--accent); }
    </style>
    <style id="theme-tailwind-overrides">
        .bg-indigo-600{background:var(--accent)!important}
        .bg-indigo-500{background:var(--accent)!important}
        .bg-gradient-to-br.from-indigo-500.to-purple-600{background:linear-gradient(135deg,var(--accent),var(--accent-2))!important}
        .from-indigo-500{--tw-gradient-from:var(--accent)!important}
        .to-purple-600{--tw-gradient-to:var(--accent-2)!important}
        .text-indigo-600,.text-indigo-500,.text-indigo-400{color:var(--accent)!important}
        .text-gray-900,.text-gray-800,.text-gray-700{color:var(--text)!important}
        .text-gray-500,.text-gray-400,.text-gray-600{color:var(--text-muted)!important}
        .text-gray-300{color:var(--text-muted)!important}
        .bg-white{background:var(--card)!important}
        .bg-gray-100{background:var(--bg-secondary)!important}
        .bg-gray-900{background:var(--bg)!important}
        .bg-gray-800{background:var(--bg-secondary)!important}
        .border-gray-200,.border-gray-800{border-color:var(--border)!important}
        .hover\:bg-gray-800:hover{background:color-mix(in srgb, var(--text) 10%, var(--bg))!important}
        .hover\:bg-gray-50:hover{background:var(--bg-secondary)!important}
        .hover\:bg-indigo-50:hover{background:color-mix(in srgb, var(--accent) 10%, var(--card))!important}
        .hover\:text-gray-700:hover,.hover\:text-white:hover{color:var(--text)!important}
        .hover\:text-indigo-600:hover{color:var(--accent)!important}
        .border-gray-100{border-color:var(--border)!important}
        .bg-gray-50{background:var(--bg-secondary)!important}
        .divide-gray-100>*>*{border-color:var(--border)!important}
        .text-purple-600{color:var(--accent-2)!important}
        .bg-purple-50{background:color-mix(in srgb, var(--accent-2) 15%, transparent)!important}
        .text-blue-600{color:var(--accent-3)!important}
        .bg-blue-50{background:color-mix(in srgb, var(--accent-3) 15%, transparent)!important}
        .text-green-400{color:var(--success)!important}
        .bg-green-400{background:var(--success)!important}
        .text-red-400{color:var(--danger)!important}
        .bg-red-400{background:var(--danger)!important}
        .bg-red-50{background:color-mix(in srgb, var(--danger) 15%, transparent)!important}
        .bg-red-100{background:color-mix(in srgb, var(--danger) 20%, transparent)!important}
        .text-yellow-600{color:var(--warning)!important}
        .bg-yellow-50{background:color-mix(in srgb, var(--warning) 15%, transparent)!important}
        .bg-yellow-100{background:color-mix(in srgb, var(--warning) 20%, transparent)!important}
        .text-green-600{color:var(--success)!important}
        .bg-green-50{background:color-mix(in srgb, var(--success) 15%, transparent)!important}
        .bg-green-100{background:color-mix(in srgb, var(--success) 20%, transparent)!important}
        .text-orange-600{color:#f97316!important}
        .bg-orange-50{background:rgba(249,115,22,0.15)!important}
        .bg-orange-100{background:rgba(249,115,22,0.2)!important}
        .bg-purple-100{background:color-mix(in srgb, var(--accent-2) 20%, transparent)!important}
        .bg-blue-100{background:color-mix(in srgb, var(--accent-3) 20%, transparent)!important}
        .text-blue-700{color:var(--accent-3)!important}
        .focus\:ring-indigo-500:focus{box-shadow:0 0 0 3px color-mix(in srgb, var(--accent) 25%, transparent)!important;border-color:var(--accent)!important}
        .focus\:border-indigo-500:focus{border-color:var(--accent)!important}
    </style>
    @yield('head')
</head>
<body class="font-sans antialiased" style="background:var(--bg);color:var(--text)" x-data="{ sidebarOpen: true, sidebarMobile: false }">
    <div class="flex min-h-screen">
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="hidden lg:flex flex-col transition-all duration-300 fixed h-full z-30" style="background:var(--bg-secondary);border-right:1px solid var(--border)">
            <div class="p-4 flex items-center space-x-3" style="border-bottom:1px solid var(--border)">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,var(--accent),var(--accent-2))">
                    <i class="fas fa-code text-white"></i>
                </div>
                <span x-show="sidebarOpen" class="text-lg font-bold whitespace-nowrap" style="color:var(--text)">CodeMaster</span>
            </div>
            <nav class="flex-1 py-4 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.dashboard') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-th-large w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('Dashboard') }}</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.users') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-users w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('Users') }}</span>
                </a>
                <a href="{{ route('admin.courses') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.courses') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-graduation-cap w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('Courses') }}</span>
                </a>
                <a href="{{ route('admin.exams') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.exams*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-clipboard-check w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('Exams') }}</span>
                </a>
                <a href="{{ route('admin.quizzes') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.quizzes*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-question-circle w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('Quizzes') }}</span>
                </a>
                <a href="{{ route('admin.practices') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.practices*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-code w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('Practice Tasks') }}</span>
                </a>
                <a href="{{ route('admin.roadmaps') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.roadmaps*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-project-diagram w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('Roadmaps') }}</span>
                </a>
                <a href="{{ route('admin.vacancies') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.vacancies') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-briefcase w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('Vacancies') }}</span>
                </a>
                <a href="{{ route('admin.contests') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.contests*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-trophy w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('Contests') }}</span>
                </a>
                <a href="{{ route('admin.interview-prep') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.interview-prep*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-comments w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('Interview Prep') }}</span>
                </a>
                <a href="{{ route('admin.news.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.news*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-newspaper w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('News') }}</span>
                </a>
                <a href="{{ route('admin.notifications') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.notifications*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-envelope w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('Notifications') }}</span>
                </a>
                <a href="{{ route('admin.roadmap-list') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.roadmap-list*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-sitemap w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('Roadmap Lists') }}</span>
                </a>
                <hr style="border-color:var(--border)" class="my-2">
                <a href="{{ route('home') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="color:var(--text-muted)">
                    <i class="fas fa-arrow-left w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">{{ __('Back to Site') }}</span>
                </a>
            </nav>
            <div class="p-4" style="border-top:1px solid var(--border)">
                <button @click="sidebarOpen = !sidebarOpen" class="w-full flex items-center justify-center py-2 transition" style="color:var(--text-muted)">
                    <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                </button>
            </div>
        </aside>

        <div class="lg:hidden fixed top-0 left-0 right-0 z-40 px-4 py-3 flex items-center justify-between" style="background:var(--bg-secondary);color:var(--text);border-bottom:1px solid var(--border)">
            <div class="flex items-center space-x-3">
                <button @click="sidebarMobile = !sidebarMobile" class="p-2 rounded-lg" style="color:var(--text-muted)">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="font-bold">Admin Panel</span>
            </div>
        </div>

        <div x-show="sidebarMobile" @click="sidebarMobile = false" class="lg:hidden fixed inset-0 z-30" style="background:rgba(0,0,0,0.5)" x-transition.opacity></div>
        <aside x-show="sidebarMobile" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="lg:hidden fixed top-0 left-0 w-64 h-full z-40 overflow-y-auto" style="background:var(--bg-secondary);color:var(--text)">
            <div class="p-4 flex items-center space-x-3" style="border-bottom:1px solid var(--border)">
                <div class="w-10 h-10 rounded-lg flex items-center" style="background:linear-gradient(135deg,var(--accent),var(--accent-2))">
                    <i class="fas fa-code text-white"></i>
                </div>
                <span class="text-lg font-bold">CodeMaster</span>
            </div>
            <nav class="py-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.dashboard') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-th-large w-6 text-center"></i> {{ __('Dashboard') }}
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.users') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-users w-6 text-center"></i> {{ __('Users') }}
                </a>
                <a href="{{ route('admin.courses') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.courses') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-graduation-cap w-6 text-center"></i> {{ __('Courses') }}
                </a>
                <a href="{{ route('admin.exams') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.exams*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-clipboard-check w-6 text-center"></i> {{ __('Exams') }}
                </a>
                <a href="{{ route('admin.quizzes') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.quizzes*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-question-circle w-6 text-center"></i> {{ __('Quizzes') }}
                </a>
                <a href="{{ route('admin.practices') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.practices*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-code w-6 text-center"></i> {{ __('Practice Tasks') }}
                </a>
                <a href="{{ route('admin.roadmaps') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.roadmaps*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-project-diagram w-6 text-center"></i> {{ __('Roadmaps') }}
                </a>
                <a href="{{ route('admin.vacancies') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.vacancies') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-briefcase w-6 text-center"></i> {{ __('Vacancies') }}
                </a>
                <a href="{{ route('admin.contests') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.contests*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-trophy w-6 text-center"></i> {{ __('Contests') }}
                </a>
                <a href="{{ route('admin.interview-prep') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.interview-prep*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-comments w-6 text-center"></i> {{ __('Interview Prep') }}
                </a>
                <a href="{{ route('admin.news.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.news*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-newspaper w-6 text-center"></i> {{ __('News') }}
                </a>
                <a href="{{ route('admin.notifications') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.notifications*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-envelope w-6 text-center"></i> {{ __('Notifications') }}
                </a>
                <a href="{{ route('admin.roadmap-list') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="{{ request()->routeIs('admin.roadmap-list*') ? 'background:var(--card);color:var(--accent)' : 'color:var(--text-muted)' }}">
                    <i class="fas fa-sitemap w-6 text-center"></i> {{ __('Roadmap Lists') }}
                </a>
                <hr style="border-color:var(--border)" class="my-2">
                <a href="{{ route('home') }}" class="flex items-center px-4 py-3 text-sm font-medium transition" style="color:var(--text-muted)">
                    <i class="fas fa-arrow-left w-6 text-center"></i> {{ __('Back to Site') }}
                </a>
            </nav>
        </aside>

        <div :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'" class="flex-1 transition-all duration-300">
            <header class="hidden lg:flex items-center justify-between px-6 py-4 sticky top-0 z-20" style="background:var(--card);border-bottom:1px solid var(--border)">
                <div>
                    <h1 class="text-xl font-semibold" style="color:var(--text)">@yield('header-title', __('Admin Dashboard'))</h1>
                    <p class="text-sm" style="color:var(--text-muted)">@yield('header-subtitle', __('Welcome back'))</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="relative p-2 rounded-lg transition" style="color:var(--text-muted)">
                        <i class="fas fa-bell"></i>
                        @php $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count(); @endphp
                        @if($unreadCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </button>
                    <div class="flex items-center space-x-3">
                        <img src="{{ Auth::user()->avatar_url }}" class="w-8 h-8 rounded-full">
                        <div>
                            <p class="text-sm font-medium" style="color:var(--text)">{{ Auth::user()->name ?? 'Admin' }}</p>
                            <p class="text-xs" style="color:var(--text-muted)">{{ __('Administrator') }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 lg:p-6">
                <x-notification />
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
