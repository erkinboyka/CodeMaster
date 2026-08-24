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
    <link rel="stylesheet" href="{{ asset('css/admin-fire-streak.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('build/assets/app-DUr89oQr.js') }}" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        (function() {
            var t = localStorage.getItem('theme') || 'neon';
            var l = localStorage.getItem('theme-light') === '1';
            document.documentElement.setAttribute('data-theme', l ? t + '-light' : t);
        })();
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
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

    @auth
    @php
        $lastShown = session('review_last_shown');
        $showReview = !$lastShown || now()->diffInHours(\Carbon\Carbon::parse($lastShown)) >= 48;
    @endphp
    @if($showReview)
    <div x-data="{ open: true }" x-show="open" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px)" @click="open=false;fetch('{{ route('reviews.dismiss') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}})"></div>
        <div style="position:relative;z-index:1;background:var(--card);border:1px solid var(--border);border-radius:20px;padding:32px;max-width:440px;width:100%;box-shadow:0 25px 80px rgba(0,0,0,0.4)">
            <button @click="open=false;fetch('{{ route('reviews.dismiss') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}})" style="position:absolute;top:16px;right:16px;background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:18px">&times;</button>
            <div style="text-align:center;margin-bottom:20px">
                <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--accent),var(--accent-2));display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
                    <i class="fas fa-star" style="color:#fff;font-size:20px"></i>
                </div>
                <h3 style="font-size:18px;font-weight:800;color:var(--text);margin:0">{{ __('review_title') }}</h3>
                <p style="font-size:13px;color:var(--text-muted);margin:6px 0 0">{{ __('review_desc') }}</p>
            </div>
            <form action="{{ route('reviews.store') }}" method="POST">
                @csrf
                <div style="display:flex;gap:6px;justify-content:center;margin-bottom:16px" x-data="{ rating: 0 }">
                    @for($i = 1; $i <= 5; $i++)
                    <label style="cursor:pointer;font-size:24px;color:var(--border);transition:color 0.2s" onmouseover="this.style.color='#f59e0b'" onmouseout="this.style.color='var(--border)'">
                        <input type="radio" name="rating" value="{{ $i }}" style="display:none" x-model="rating">
                        <i class="fas fa-star" :style="rating >= {{ $i }} ? 'color:#f59e0b' : 'color:var(--border)'"></i>
                    </label>
                    @endfor
                </div>
                <textarea name="text" rows="3" required placeholder="{{ __('review_placeholder') }}" style="width:100%;padding:12px;border-radius:12px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;resize:none;box-sizing:border-box;outline:none;font-family:inherit"></textarea>
                <button type="submit" style="width:100%;padding:12px;border:none;border-radius:12px;background:linear-gradient(135deg,var(--accent),var(--accent-2));color:#fff;font-size:14px;font-weight:700;cursor:pointer;margin-top:12px;transition:transform 0.2s,box-shadow 0.2s" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px var(--accent-glow)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <i class="fas fa-paper-plane" style="margin-right:6px"></i>{{ __('review_submit') }}
                </button>
            </form>
        </div>
    </div>
    @endif
    @endauth

    <style>
        .nav-fire { display:flex; align-items:center; margin-right:2px; position:relative; }
        .nav-fire-badge {
            display:flex; align-items:center; gap:5px; padding:5px 10px 5px 8px;
            border-radius:6px; background:var(--bg-secondary,#1a1a2e);
            border:1px solid rgba(255,120,0,0.2);
            font-family:'JetBrains Mono',monospace; font-size:11px; font-weight:600;
            cursor:pointer; letter-spacing:0.5px; transition:all 0.3s; position:relative; overflow:visible;
        }
        .nav-fire-badge:active { transform:scale(0.93); }
        .nav-fire-badge i { font-size:13px; display:inline-block; transition:all 0.3s; }
        .nav-fire-count { display:inline-block; font-weight:800; }

        /* ember: 1-2 days — dim gray spark */
        .nav-fire-badge.level-ember {
            border-color:rgba(100,116,139,0.3); background:rgba(100,116,139,0.08);
        }
        .nav-fire-badge.level-ember i, .nav-fire-badge.level-ember .nav-fire-count { color:#94a3b8; }
        .nav-fire-badge.level-ember i { animation:emberGlow 4s ease-in-out infinite; }
        @keyframes emberGlow { 0%,100%{filter:drop-shadow(0 0 1px #94a3b8);opacity:0.6} 50%{filter:drop-shadow(0 0 4px #94a3b8);opacity:1} }

        /* spark: 3-6 days — faint blue sparkle */
        .nav-fire-badge.level-spark {
            border-color:rgba(56,189,248,0.3); background:rgba(56,189,248,0.06);
        }
        .nav-fire-badge.level-spark i, .nav-fire-badge.level-spark .nav-fire-count { color:#7dd3fc; }
        .nav-fire-badge.level-spark i { animation:sparkPulse 3s ease-in-out infinite; }
        @keyframes sparkPulse { 0%,100%{transform:scale(1);filter:drop-shadow(0 0 2px #7dd3fc)} 50%{transform:scale(1.15);filter:drop-shadow(0 0 6px #7dd3fc)} }

        /* warm: 7-13 days — steady yellow flame */
        .nav-fire-badge.level-warm {
            border-color:rgba(234,179,8,0.35); background:rgba(234,179,8,0.08);
        }
        .nav-fire-badge.level-warm i, .nav-fire-badge.level-warm .nav-fire-count { color:#facc15; }
        .nav-fire-badge.level-warm i { animation:warmFlicker 2.5s ease-in-out infinite; }
        @keyframes warmFlicker { 0%,100%{transform:scale(1) rotate(0);filter:drop-shadow(0 0 3px #facc15)} 33%{transform:scale(1.1) rotate(-3deg);filter:drop-shadow(0 0 6px #facc15)} 66%{transform:scale(1.05) rotate(3deg);filter:drop-shadow(0 0 5px #facc15)} }

        /* hot: 14-29 days — bright orange + glow ring */
        .nav-fire-badge.level-hot {
            border-color:rgba(249,115,22,0.4); background:rgba(249,115,22,0.1);
            box-shadow:0 0 6px rgba(249,115,22,0.15);
        }
        .nav-fire-badge.level-hot i, .nav-fire-badge.level-hot .nav-fire-count { color:#fb923c; }
        .nav-fire-badge.level-hot i { animation:hotBurn 2s ease-in-out infinite; }
        @keyframes hotBurn { 0%,100%{transform:scale(1);filter:drop-shadow(0 0 4px #fb923c)} 50%{transform:scale(1.2);filter:drop-shadow(0 0 10px #fb923c) drop-shadow(0 0 20px rgba(249,115,22,0.3))} }

        /* super: 30-89 days — intense orange + animated border */
        .nav-fire-badge.level-super {
            border-color:rgba(249,115,22,0.5); background:rgba(239,68,68,0.08);
            box-shadow:0 0 8px rgba(249,115,22,0.2);
            animation:superBorder 3s ease-in-out infinite;
        }
        .nav-fire-badge.level-super i, .nav-fire-badge.level-super .nav-fire-count { color:#f97316; }
        .nav-fire-badge.level-super i { animation:superPulse 1.8s ease-in-out infinite; }
        @keyframes superBorder { 0%,100%{box-shadow:0 0 6px rgba(249,115,22,0.15)} 50%{box-shadow:0 0 14px rgba(249,115,22,0.35)} }
        @keyframes superPulse { 0%,100%{transform:scale(1);filter:drop-shadow(0 0 5px #f97316)} 50%{transform:scale(1.25);filter:drop-shadow(0 0 14px #f97316) drop-shadow(0 0 24px rgba(249,115,22,0.4))} }

        /* mega: 90-179 days — red-orange double icon */
        .nav-fire-badge.level-mega {
            border-color:rgba(239,68,68,0.45); background:rgba(239,68,68,0.1);
            box-shadow:0 0 10px rgba(239,68,68,0.2);
            animation:megaGlow 2s ease-in-out infinite;
        }
        .nav-fire-badge.level-mega i, .nav-fire-badge.level-mega .nav-fire-count { color:#ef4444; }
        .nav-fire-badge.level-mega i { animation:megaShake 1.5s ease-in-out infinite; font-size:14px; }
        @keyframes megaGlow { 0%,100%{box-shadow:0 0 8px rgba(239,68,68,0.15)} 50%{box-shadow:0 0 20px rgba(239,68,68,0.35),0 0 40px rgba(239,68,68,0.1)} }
        @keyframes megaShake { 0%,100%{transform:scale(1) rotate(0)} 25%{transform:scale(1.15) rotate(-5deg)} 75%{transform:scale(1.15) rotate(5deg)} }

        /* supernova: 180-364 days — crimson with spinning glow */
        .nav-fire-badge.level-supernova {
            border-color:rgba(220,38,38,0.5); background:rgba(220,38,38,0.12);
            box-shadow:0 0 12px rgba(220,38,38,0.25);
            animation:supernovaGlow 1.8s ease-in-out infinite;
        }
        .nav-fire-badge.level-supernova i, .nav-fire-badge.level-supernova .nav-fire-count { color:#dc2626; }
        .nav-fire-badge.level-supernova i { animation:supernovaSpin 2s linear infinite; font-size:14px; filter:drop-shadow(0 0 8px #dc2626); }
        @keyframes supernovaGlow { 0%,100%{box-shadow:0 0 10px rgba(220,38,38,0.2),0 0 20px rgba(220,38,38,0.05)} 50%{box-shadow:0 0 24px rgba(220,38,38,0.4),0 0 48px rgba(220,38,38,0.15)} }
        @keyframes supernovaSpin { 0%{transform:rotate(0) scale(1)} 50%{transform:rotate(180deg) scale(1.3)} 100%{transform:rotate(360deg) scale(1)} }

        /* inferno: 365+ days — elite crimson-purple chaos */
        .nav-fire-badge.level-inferno {
            border:none;
            background:linear-gradient(135deg,rgba(220,38,38,0.2),rgba(147,51,234,0.2));
            box-shadow:0 0 16px rgba(220,38,38,0.3),0 0 32px rgba(147,51,234,0.15);
            animation:infernoChaos 1.2s ease-in-out infinite;
        }
        .nav-fire-badge.level-inferno i {
            color:#dc2626; font-size:15px;
            animation:infernoFlame 1s ease-in-out infinite;
            filter:drop-shadow(0 0 6px #dc2626) drop-shadow(0 0 12px rgba(147,51,234,0.5));
        }
        .nav-fire-badge.level-inferno .nav-fire-count {
            background:linear-gradient(90deg,#dc2626,#9333ea);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            animation:infernoCount 1s ease-in-out infinite;
        }
        @keyframes infernoChaos { 0%,100%{box-shadow:0 0 12px rgba(220,38,38,0.25),0 0 24px rgba(147,51,234,0.1)} 50%{box-shadow:0 0 28px rgba(220,38,38,0.5),0 0 56px rgba(147,51,234,0.25),0 0 80px rgba(220,38,38,0.1)} }
        @keyframes infernoFlame { 0%,100%{transform:scale(1) rotate(0);filter:drop-shadow(0 0 6px #dc2626) drop-shadow(0 0 12px rgba(147,51,234,0.5))} 25%{transform:scale(1.2) rotate(-8deg);filter:drop-shadow(0 0 14px #dc2626) drop-shadow(0 0 24px rgba(147,51,234,0.7))} 75%{transform:scale(1.15) rotate(8deg);filter:drop-shadow(0 0 12px #9333ea) drop-shadow(0 0 20px rgba(220,38,38,0.6))} }
        @keyframes infernoCount { 0%,100%{opacity:0.8;transform:scale(1)} 50%{opacity:1;transform:scale(1.15)} }

        /* ascended: 2 years — glowing white-gold crown */
        .nav-fire-badge.level-ascended {
            border:none;
            background:linear-gradient(135deg,rgba(250,204,21,0.15),rgba(255,255,255,0.08));
            box-shadow:0 0 18px rgba(250,204,21,0.3),0 0 36px rgba(255,255,255,0.08);
            animation:ascendedGlow 1.5s ease-in-out infinite;
        }
        .nav-fire-badge.level-ascended i {
            color:#facc15; font-size:15px;
            animation:ascendedSpin 3s linear infinite;
            filter:drop-shadow(0 0 8px #facc15) drop-shadow(0 0 16px rgba(255,255,255,0.3));
        }
        .nav-fire-badge.level-ascended .nav-fire-count {
            background:linear-gradient(90deg,#facc15,#fef08a);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            font-weight:900;
        }
        @keyframes ascendedGlow { 0%,100%{box-shadow:0 0 14px rgba(250,204,21,0.25),0 0 28px rgba(255,255,255,0.05)} 50%{box-shadow:0 0 32px rgba(250,204,21,0.5),0 0 64px rgba(255,255,255,0.12)} }
        @keyframes ascendedSpin { 0%{transform:rotate(0) scale(1)} 50%{transform:rotate(180deg) scale(1.25)} 100%{transform:rotate(360deg) scale(1)} }

        /* immortal: 3 years — deep violet pulse */
        .nav-fire-badge.level-immortal {
            border:none;
            background:linear-gradient(135deg,rgba(139,92,246,0.2),rgba(220,38,38,0.1));
            box-shadow:0 0 20px rgba(139,92,246,0.35),0 0 40px rgba(220,38,38,0.1);
            animation:immortalPulse 1.3s ease-in-out infinite;
        }
        .nav-fire-badge.level-immortal i {
            color:#a855f7; font-size:16px;
            animation:immortalBreathe 1.8s ease-in-out infinite;
            filter:drop-shadow(0 0 8px #a855f7) drop-shadow(0 0 20px rgba(139,92,246,0.6));
        }
        .nav-fire-badge.level-immortal .nav-fire-count {
            background:linear-gradient(90deg,#c084fc,#f472b6);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            font-weight:900;
        }
        @keyframes immortalPulse { 0%,100%{box-shadow:0 0 16px rgba(139,92,246,0.3),0 0 32px rgba(220,38,38,0.08)} 50%{box-shadow:0 0 36px rgba(139,92,246,0.55),0 0 72px rgba(220,38,38,0.2),0 0 100px rgba(139,92,246,0.08)} }
        @keyframes immortalBreathe { 0%,100%{transform:scale(1);filter:drop-shadow(0 0 8px #a855f7) drop-shadow(0 0 20px rgba(139,92,246,0.6))} 50%{transform:scale(1.3);filter:drop-shadow(0 0 18px #a855f7) drop-shadow(0 0 36px rgba(244,114,182,0.5))} }

        /* legendary: 4 years — blazing magenta fire */
        .nav-fire-badge.level-legendary {
            border:none;
            background:linear-gradient(135deg,rgba(236,72,153,0.2),rgba(249,115,22,0.15));
            box-shadow:0 0 22px rgba(236,72,153,0.35),0 0 44px rgba(249,115,22,0.12);
            animation:legendaryBlaze 1.1s ease-in-out infinite;
        }
        .nav-fire-badge.level-legendary i {
            color:#ec4899; font-size:16px;
            animation:legendaryFlame 0.9s ease-in-out infinite;
            filter:drop-shadow(0 0 10px #ec4899) drop-shadow(0 0 22px rgba(249,115,22,0.5));
        }
        .nav-fire-badge.level-legendary .nav-fire-count {
            background:linear-gradient(90deg,#ec4899,#f97316);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            font-weight:900;
        }
        @keyframes legendaryBlaze { 0%,100%{box-shadow:0 0 18px rgba(236,72,153,0.3),0 0 36px rgba(249,115,22,0.1)} 50%{box-shadow:0 0 40px rgba(236,72,153,0.55),0 0 80px rgba(249,115,22,0.2),0 0 120px rgba(236,72,153,0.08)} }
        @keyframes legendaryFlame { 0%,100%{transform:scale(1) rotate(0)} 33%{transform:scale(1.2) rotate(-10deg)} 66%{transform:scale(1.15) rotate(10deg)} }

        /* titan: 5 years — electric cyan-violet explosion */
        .nav-fire-badge.level-titan {
            border:none;
            background:linear-gradient(135deg,rgba(34,211,238,0.15),rgba(168,85,247,0.2));
            box-shadow:0 0 24px rgba(34,211,238,0.3),0 0 48px rgba(168,85,247,0.15);
            animation:titanStorm 1s ease-in-out infinite;
        }
        .nav-fire-badge.level-titan i {
            color:#22d3ee; font-size:17px;
            animation:titanCrackle 0.8s ease-in-out infinite;
            filter:drop-shadow(0 0 10px #22d3ee) drop-shadow(0 0 24px rgba(168,85,247,0.6));
        }
        .nav-fire-badge.level-titan .nav-fire-count {
            background:linear-gradient(90deg,#22d3ee,#a855f7,#f472b6);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            font-weight:900;
        }
        @keyframes titanStorm { 0%,100%{box-shadow:0 0 20px rgba(34,211,238,0.25),0 0 40px rgba(168,85,247,0.12)} 50%{box-shadow:0 0 48px rgba(34,211,238,0.5),0 0 96px rgba(168,85,247,0.25),0 0 140px rgba(34,211,238,0.1)} }
        @keyframes titanCrackle { 0%,100%{transform:scale(1);filter:drop-shadow(0 0 10px #22d3ee) drop-shadow(0 0 24px rgba(168,85,247,0.6))} 25%{transform:scale(1.25) rotate(-6deg);filter:drop-shadow(0 0 20px #a855f7) drop-shadow(0 0 40px rgba(34,211,238,0.5))} 75%{transform:scale(1.2) rotate(6deg);filter:drop-shadow(0 0 18px #22d3ee) drop-shadow(0 0 36px rgba(168,85,247,0.4))} }

        /* eternal: 7+ years — cosmic rainbow maelstrom */
        .nav-fire-badge.level-eternal {
            border:none;
            background:linear-gradient(135deg,rgba(220,38,38,0.15),rgba(168,85,247,0.15),rgba(34,211,238,0.15),rgba(250,204,21,0.15));
            box-shadow:0 0 28px rgba(168,85,247,0.4),0 0 56px rgba(220,38,38,0.2),0 0 84px rgba(34,211,238,0.1);
            animation:eternalMaelstrom 0.9s ease-in-out infinite;
        }
        .nav-fire-badge.level-eternal i {
            color:#f472b6; font-size:18px;
            animation:eternalChaos 1.2s linear infinite;
            filter:drop-shadow(0 0 12px #f472b6) drop-shadow(0 0 24px rgba(168,85,247,0.7)) drop-shadow(0 0 36px rgba(34,211,238,0.4));
        }
        .nav-fire-badge.level-eternal .nav-fire-count {
            background:linear-gradient(90deg,#ef4444,#a855f7,#22d3ee,#facc15,#ef4444);
            background-size:200% auto;
            -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            animation:eternalGradient 2s linear infinite;
            font-weight:900; font-size:12px;
        }
        @keyframes eternalMaelstrom { 0%,100%{box-shadow:0 0 24px rgba(168,85,247,0.35),0 0 48px rgba(220,38,38,0.18),0 0 72px rgba(34,211,238,0.08)} 50%{box-shadow:0 0 56px rgba(220,38,38,0.5),0 0 112px rgba(168,85,247,0.3),0 0 168px rgba(34,211,238,0.15),0 0 200px rgba(250,204,21,0.08)} }
        @keyframes eternalChaos { 0%{transform:rotate(0) scale(1)} 25%{transform:rotate(90deg) scale(1.3)} 50%{transform:rotate(180deg) scale(1)} 75%{transform:rotate(270deg) scale(1.3)} 100%{transform:rotate(360deg) scale(1)} }
        @keyframes eternalGradient { 0%{background-position:0% center} 100%{background-position:200% center} }

        .nav-fire-badge:hover { filter:brightness(1.15); }

        .mobile-fire-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:6px; background:var(--bg-secondary,#1a1a2e); border:1px solid rgba(255,120,0,0.25); font-family:'JetBrains Mono',monospace; font-size:12px; font-weight:600; color:#ff8c00; margin:8px 16px; }
        .mobile-fire-badge i { animation:iconPulse 3s ease-in-out infinite; font-size:14px; }
    </style>

    <style>
        .ai-assistant-fab { position:fixed; bottom:24px; right:24px; z-index:9999; font-family:'Inter',sans-serif; }
        .ai-fab-btn { width:56px; height:56px; border-radius:50%; background:linear-gradient(135deg,var(--accent),var(--accent-2)); border:none; color:white; font-size:22px; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 20px rgba(99,102,241,0.4); transition:all 0.3s; position:relative; }
        .ai-fab-icon { display:flex; align-items:center; justify-content:center; line-height:0; }
        .ai-fab-icon svg { width:26px; height:26px; filter:drop-shadow(0 0 6px rgba(255,255,255,0.5)); animation:aiIconGlow 3s ease-in-out infinite; }
        @keyframes aiIconGlow { 0%,100%{filter:drop-shadow(0 0 4px rgba(255,255,255,0.4))} 50%{filter:drop-shadow(0 0 10px rgba(255,255,255,0.8))} }
        .ai-fab-btn:hover { transform:scale(1.1); box-shadow:0 6px 30px rgba(99,102,241,0.5); }
        .ai-fab-active { background:linear-gradient(135deg,#6366f1,#a855f7); border-radius:16px; }
        .ai-fab-pulse { position:absolute; top:-2px; right:-2px; width:14px; height:14px; background:#22c55e; border-radius:50%; border:2px solid white; animation:aiPulse 2s ease-in-out infinite; }
        @keyframes aiPulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.3);opacity:0.7} }
        .ai-panel { position:absolute; bottom:70px; right:0; width:380px; max-height:520px; background:var(--card,#fff); border:1px solid var(--border,#e2e8f0); border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.2); display:flex; flex-direction:column; overflow:hidden; }
        .ai-panel-header { display:flex; align-items:center; justify-content:space-between; padding:14px 16px; background:linear-gradient(135deg,var(--accent),var(--accent-2)); color:white; }
        .ai-panel-title { display:flex; align-items:center; gap:8px; font-weight:700; font-size:14px; }
        .ai-panel-context { font-size:11px; font-weight:400; opacity:0.8; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .ai-action-btn { background:rgba(255,255,255,0.2); border:none; color:white; width:30px; height:30px; border-radius:8px; cursor:pointer; font-size:12px; transition:background 0.2s; }
        .ai-action-btn:hover { background:rgba(255,255,255,0.35); }
        .ai-panel-messages { flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:12px; max-height:360px; min-height:200px; }
        .ai-welcome { text-align:center; padding:20px 10px; }
        .ai-welcome-icon { width:64px; height:64px; margin:0 auto 10px; background:linear-gradient(135deg,var(--accent),var(--accent-2)); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; animation:aiIconGlow 3s ease-in-out infinite; }
        .ai-welcome-icon svg { width:36px; height:36px; }
        .ai-welcome-title { font-weight:700; font-size:15px; color:var(--text,#1e293b); margin-bottom:6px; }
        .ai-welcome-desc { font-size:12px; color:var(--text-muted,#64748b); line-height:1.5; margin-bottom:14px; }
        .ai-suggestions { display:flex; flex-direction:column; gap:6px; }
        .ai-suggestion-btn { padding:8px 12px; border:1px solid var(--border,#e2e8f0); border-radius:10px; background:var(--bg-secondary,#f8fafc); color:var(--accent,#6366f1); font-size:12px; font-weight:500; cursor:pointer; transition:all 0.2s; text-align:left; }
        .ai-suggestion-btn:hover { border-color:var(--accent,#6366f1); background:color-mix(in srgb, var(--accent) 10%, var(--card)); }
        .ai-msg { display:flex; gap:8px; align-items:flex-start; }
        .ai-msg-user { justify-content:flex-end; }
        .ai-msg-avatar { width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg,var(--accent),var(--accent-2)); display:flex; align-items:center; justify-content:center; color:white; font-size:12px; flex-shrink:0; overflow:hidden; }
        .ai-msg-avatar svg { width:16px; height:16px; }
        .ai-msg-content { padding:10px 14px; border-radius:12px; font-size:13px; line-height:1.6; max-width:85%; }
        .ai-msg-ai .ai-msg-content { background:var(--bg-secondary,#f1f5f9); color:var(--text,#1e293b); border-bottom-left-radius:4px; }
        .ai-msg-user .ai-msg-content { background:linear-gradient(135deg,var(--accent),var(--accent-2)); color:white; border-bottom-right-radius:4px; }
        .ai-msg-content code { background:rgba(0,0,0,0.08); padding:1px 5px; border-radius:4px; font-family:'JetBrains Mono',monospace; font-size:12px; }
        .ai-msg-content pre { background:#1e293b; color:#e2e8f0; padding:10px; border-radius:8px; margin:6px 0; overflow-x:auto; font-size:12px; }
        .ai-msg-content pre code { background:none; padding:0; color:inherit; }
        .ai-typing { display:flex; gap:4px; padding:12px 16px; }
        .ai-typing span { width:6px; height:6px; background:var(--accent,#6366f1); border-radius:50%; animation:aiTyping 1.4s ease-in-out infinite; }
        .ai-typing span:nth-child(2) { animation-delay:0.2s; }
        .ai-typing span:nth-child(3) { animation-delay:0.4s; }
        @keyframes aiTyping { 0%,60%,100%{transform:translateY(0);opacity:0.4} 30%{transform:translateY(-6px);opacity:1} }
        .ai-panel-input { display:flex; gap:8px; padding:12px 14px; border-top:1px solid var(--border,#e2e8f0); background:var(--card,#fff); }
        .ai-input { flex:1; padding:10px 14px; border:1px solid var(--border,#e2e8f0); border-radius:10px; font-size:13px; background:var(--bg-secondary,#f8fafc); color:var(--text,#1e293b); outline:none; transition:border-color 0.2s; }
        .ai-input:focus { border-color:var(--accent,#6366f1); }
        .ai-send-btn { width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg,var(--accent),var(--accent-2)); border:none; color:white; font-size:14px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.2s; }
        .ai-send-btn:hover:not(:disabled) { transform:scale(1.05); }
        .ai-send-btn:disabled { opacity:0.5; cursor:not-allowed; }
        @media (max-width:480px) { .ai-panel { width:calc(100vw - 32px); right:-8px; bottom:65px; max-height:450px; } }
    </style>

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