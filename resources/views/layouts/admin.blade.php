<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" data-theme="neon">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-secondary); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
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
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center">3</span>
                    </button>
                    <div class="flex items-center space-x-3">
                        <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=Admin&background=6366f1&color=fff' }}" class="w-8 h-8 rounded-full">
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
