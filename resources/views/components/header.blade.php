<nav class="nav" x-data="navController()" x-init="init()" :class="(scrolled ? 'scrolled' : '') + (hidden ? ' hidden' : '')">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="nav-logo">
            <div class="nav-logo-icon"><i class="fas fa-terminal"></i></div>
            <span class="nav-logo-text"><span class="nav-logo-bracket">&gt;</span> Code<span class="nav-logo-accent">Master</span><span class="nav-logo-cursor">_</span></span>
        </a>

        <div class="nav-links">
            <div class="nav-dropdown" @mouseenter="dropdownEduc = true" @mouseleave="dropdownEduc = false">
                <a href="{{ route('courses.index') }}" class="nav-link nav-link-dropdown">
                    <span class="nav-link-icon"><i class="fas fa-layer-group"></i></span>
                    <span class="nav-link-label">{{ __('Education') }}</span>
                    <span class="nav-link-bracket">{</span>
                    <i class="fas fa-chevron-down nav-dropdown-arrow"></i>
                </a>
                <div class="nav-dropdown-menu" x-show="dropdownEduc" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95">
                    <a href="{{ route('courses.index') }}" class="nav-dropdown-link">
                        <span class="nav-dropdown-prefix">$</span>
                        <i class="fas fa-book"></i> {{ __('Courses') }}
                    </a>
                    <a href="{{ route('roadmaps.index') }}" class="nav-dropdown-link">
                        <span class="nav-dropdown-prefix">$</span>
                        <i class="fas fa-project-diagram"></i> {{ __('Roadmaps') }}
                    </a>
                </div>
            </div>

            <a href="{{ route('problems.index') }}" class="nav-link">
                <span class="nav-link-icon"><i class="fas fa-fire"></i></span>
                <span class="nav-link-label">{{ __('Problems') }}</span>
            </a>
            <a href="{{ route('contests.index') }}" class="nav-link">
                <span class="nav-link-icon"><i class="fas fa-code"></i></span>
                <span class="nav-link-label">{{ __('Contests') }}</span>
            </a>
            <a href="{{ route('community.index') }}" class="nav-link">
                <span class="nav-link-icon"><i class="fas fa-users"></i></span>
                <span class="nav-link-label">{{ __('Community') }}</span>
            </a>
            <a href="{{ route('ratings.index') }}" class="nav-link">
                <span class="nav-link-icon"><i class="fas fa-trophy"></i></span>
                <span class="nav-link-label">{{ __('Ratings') }}</span>
            </a>

            <div class="nav-dropdown" @mouseenter="dropdownVacs = true" @mouseleave="dropdownVacs = false">
                <a href="{{ route('vacancies.index') }}" class="nav-link nav-link-dropdown">
                    <span class="nav-link-icon"><i class="fas fa-briefcase"></i></span>
                    <span class="nav-link-label">{{ __('Career') }}</span>
                    <span class="nav-link-bracket">{</span>
                    <i class="fas fa-chevron-down nav-dropdown-arrow"></i>
                </a>
                <div class="nav-dropdown-menu" x-show="dropdownVacs" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95">
                    <a href="{{ route('vacancies.index') }}" class="nav-dropdown-link">
                        <span class="nav-dropdown-prefix">$</span>
                        <i class="fas fa-briefcase"></i> {{ __('Vacancies') }}
                    </a>
                    <a href="{{ route('interview.index') }}" class="nav-dropdown-link">
                        <span class="nav-dropdown-prefix">$</span>
                        <i class="fas fa-user-tie"></i> {{ __('Interview Prep') }}
                    </a>
                    <a href="{{ route('peer.index') }}" class="nav-dropdown-link">
                        <span class="nav-dropdown-prefix">$</span>
                        <i class="fas fa-users"></i> {{ __('Peer Interview') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="nav-actions">
            <div class="lang-switcher" @click.away="langOpen = false">
                <button class="lang-switcher-btn" @click="langOpen = !langOpen" title="{{ __('ml_language') }}">
                    <i class="fas fa-globe" style="font-size:11px;opacity:0.6"></i>
                    <span x-text="currentLang.toUpperCase()">Тоҷикӣ</span>
                </button>
                <div class="lang-dropdown" x-show="langOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95">
                    <a href="/lang/ru" class="lang-option" :class="currentLang === 'ru' ? 'active' : ''" @click="langOpen = false">
                        <span class="nav-dropdown-prefix">$  </span>  Русский
                    </a>
                    <a href="/lang/tg" class="lang-option" :class="currentLang === 'tg' ? 'active' : ''" @click="langOpen = false">
                        <span class="nav-dropdown-prefix">$  </span>  Тоҷикӣ
                    </a>
                    <a href="/lang/en" class="lang-option" :class="currentLang === 'en' ? 'active' : ''" @click="langOpen = false">
                        <span class="nav-dropdown-prefix">$  </span>  English
                    </a>
                </div>
            </div>

            <div class="theme-switcher" @click.away="themeOpen = false">
                <button class="nav-icon-btn" @click="themeOpen = !themeOpen" title="{{ __('ml_switch_theme') }}">
                    <i class="fas fa-palette"></i>
                </button>
                <div class="theme-dropdown" x-show="themeOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95">
                    <button class="theme-option" :class="currentTheme === 'neon' ? 'active' : ''" @click="setTheme('neon')">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#00F5D4,#7C3AED)"></span>
                        CodeMaster Neon
                    </button>
                    <button class="theme-option" :class="currentTheme === 'vscode' ? 'active' : ''" @click="setTheme('vscode')">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#007ACC,#4EC9B0)"></span>
                        VS Code
                    </button>
                    <button class="theme-option" :class="currentTheme === 'nord' ? 'active' : ''" @click="setTheme('nord')">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#88C0D0,#B48EAD)"></span>
                        Nord
                    </button>
                    <button class="theme-option" :class="currentTheme === 'github' ? 'active' : ''" @click="setTheme('github')">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#2F81F7,#A371F7)"></span>
                        GitHub Dark
                    </button>
                    <div class="theme-dropdown-divider"></div>
                    <button class="theme-option" @click="toggleLightMode()">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#f8fafc,#e2e8f0);border-color:#cbd5e1"></span>
                        <span x-text="isLight ? '{{ __("header_theme_dark") }}' : '{{ __("header_theme_light") }}'"></span>
                    </button>
                </div>
            </div>

            @guest
            <a href="{{ route('login') }}" class="nav-btn nav-btn-ghost">
                <span class="nav-btn-prefix"></span> {{ __('Login') }}
            </a>
            <a href="{{ route('register') }}" class="nav-btn nav-btn-primary">
                {{ __('Register') }}
            </a>
            @endguest
            @auth
            @php
                $user = Auth::user();
                $fireLevel = $user->getFireLevel();
                $fireColor = $user->getFireColor();
                $streak = $user->streak_count;
                $fireIcon = match($fireLevel) {
                    'ember' => 'fa-smog',
                    'spark' => 'fa-star',
                    'warm' => 'fa-fire',
                    'hot' => 'fa-fire',
                    'super' => 'fa-fire',
                    'mega' => 'fa-fire-flame-curved',
                    'supernova' => 'fa-burst',
                    'inferno' => 'fa-volcano',
                    'ascended' => 'fa-crown',
                    'immortal' => 'fa-hat-wizard',
                    'legendary' => 'fa-shield-halved',
                    'titan' => 'fa-bolt-lightning',
                    'eternal' => 'fa-circle-nodes',
                    default => 'fa-fire',
                };
            @endphp
            @if($streak >= 0)
            <div class="nav-fire" title="{{ $streak }} {{ __('ml_day_streak') }} — {{ ucfirst($fireLevel) }}">
                <div class="nav-fire-badge level-{{ $fireLevel }}" onclick="triggerFireBurst(this, {{ $streak }}, '{{ $fireLevel }}')">
                    <i class="fas {{ $fireIcon }}"></i>
                    <span class="nav-fire-count">{{ $streak }}</span>
                </div>
            </div>
            @if(session('fire_level_up'))
            <div id="fire-level-up" data-level="{{ session('fire_level_up') }}" data-streak="{{ $streak }}" style="display:none"></div>
            @php session()->forget('fire_level_up'); @endphp
            @endif
            @endif

            <div class="nav-notif" @click.away="notifOpen = false">
                <button class="nav-icon-btn" @click="notifOpen = !notifOpen; if(!notifLoaded) loadNotifs()" title="{{ __('ml_notifications') }}">
                    <i class="fas fa-bell"></i>
                    <span class="nav-notif-badge" x-show="unreadCount > 0" x-text="unreadCount"></span>
                </button>
                <div class="notif-dropdown" x-show="notifOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95">
                    <div class="notif-dropdown-header">
                        <i class="fas fa-bell" style="font-size:12px;margin-right:6px"></i>{{ __('Notifications') }}
                        <button x-show="unreadCount > 0" @click="markAllRead()" style="margin-left:auto;background:none;border:none;color:rgba(255,255,255,0.8);font-size:11px;cursor:pointer">{{ __('Mark all read') }}</button>
                    </div>
                    <template x-if="notifications.length === 0">
                        <div class="notif-empty">
                            <i class="fas fa-inbox" style="font-size:20px;display:block;margin-bottom:8px;opacity:0.3"></i>
                            {{ __('No notifications') }}
                        </div>
                    </template>
                    <template x-if="notifications.length > 0">
                        <div style="max-height:320px;overflow-y:auto">
                            <template x-for="n in notifications" :key="n.id">
                                <div :style="'padding:10px 14px;border-bottom:1px solid var(--border);font-size:13px;cursor:pointer;transition:background 0.2s;' + (n.is_read ? 'opacity:0.6' : 'background:color-mix(in srgb, var(--accent) 5%, var(--card))')" @click="markRead(n)">
                                    <div style="display:flex;align-items:start;gap:8px">
                                        <span x-show="!n.is_read" style="width:6px;height:6px;border-radius:50%;background:var(--accent);flex-shrink:0;margin-top:5px"></span>
                                        <span x-text="n.message" style="color:var(--text);line-height:1.4"></span>
                                    </div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:3px" x-text="timeAgo(n.notification_time)"></div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <div class="user-menu" @click.away="userOpen = false">
                <button class="user-avatar-btn" @click="userOpen = !userOpen">
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ __('ml_avatar') }}" class="user-avatar-img">
                </button>
                <div class="lc-user-dropdown" x-show="userOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95">
                    <div class="lc-ud-divider"></div>
                    <div class="lc-ud-user">
                        <img src="{{ Auth::user()->avatar_url }}" class="lc-ud-user-avatar">
                        <div>
                            <div class="lc-ud-user-name">{{ Auth::user()->name ?? __('User') }}</div>
                            <div class="lc-ud-user-sub">
                                <i class="fas fa-fire" style="color:var(--accent)"></i> {{ Auth::user()->xp ?? 0 }} XP
                                · <span style="color:var(--accent)">{{ Auth::user()->rating ?? 1200 }}</span> ELO
                            </div>
                        </div>
                    </div>
                    <div class="lc-ud-divider"></div>
                    <a href="{{ route('dashboard') }}" class="lc-ud-link"><i class="fas fa-th-large"></i> {{ __('Dashboard') }}</a>
                    <a href="{{ route('profile.index') }}" class="lc-ud-link"><i class="fas fa-user"></i> {{ __('My Profile') }}</a>
                    <a href="{{ route('daily-challenge') }}" class="lc-ud-link"><i class="fas fa-bolt"></i> {{ __('Daily Challenge') }}</a>
                    <a href="{{ route('study-plans.index') }}" class="lc-ud-link"><i class="fas fa-list-ol"></i> {{ __('Study Plans') }}</a>
                    <div class="lc-ud-divider"></div>
                    <a href="{{ route('profile.my-lists') }}" class="lc-ud-link"><i class="fas fa-bookmark"></i> {{ __('ml_my_lists') }}</a>
                    <a href="{{ route('profile.notebook') }}" class="lc-ud-link"><i class="fas fa-sticky-note"></i> {{ __('Notebook') }}</a>
                    <a href="{{ route('profile.progress') }}" class="lc-ud-link"><i class="fas fa-chart-line"></i> {{ __('Progress') }}</a>
                    <a href="{{ route('profile.points') }}" class="lc-ud-link"><i class="fas fa-star"></i> {{ __('Points') }}</a>
                    <div class="lc-ud-divider"></div>
                    <a href="{{ route('logout') }}" class="lc-ud-link lc-ud-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> {{ __('Log Out') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
                </div>
            </div>
            @endauth

            <button id="navHamburger" class="nav-mobile-btn" type="button" aria-label="{{ __('ml_menu') }}" aria-expanded="false" aria-controls="mobileMenuPanel">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Menu — vanilla JS controlled -->
<div id="mobileMenuPanel" class="mobile-menu" role="dialog" aria-label="{{ __('ml_navigation_menu') }}" style="margin-top: -5px;">
    <div class="mobile-menu-header">
        <div class="mobile-menu-terminal-line">
            <span class="mobile-menu-prompt">$</span> <span class="mobile-menu-cmd">ls -la sections/</span>
        </div>
    </div>

    <div style="padding:8px 16px 4px">
        <div class="mobile-menu-section-title">education/</div>
        <a href="{{ route('courses.index') }}" class="mobile-link">
            <i class="fas fa-book"></i> {{ __('Courses') }}
        </a>
        <a href="{{ route('roadmaps.index') }}" class="mobile-link">
            <i class="fas fa-project-diagram"></i> {{ __('Roadmaps') }}
        </a>
    </div>
    <div style="padding:4px 16px">
        <div class="mobile-menu-section-title">competitive/</div>
        <a href="{{ route('problems.index') }}" class="mobile-link">
            <i class="fas fa-fire"></i> {{ __('Problems') }}
        </a>
        <a href="{{ route('contests.index') }}" class="mobile-link">
            <i class="fas fa-code"></i> {{ __('Contests') }}
        </a>
    </div>
    <div style="padding:4px 16px">
        <div class="mobile-menu-section-title">career/</div>
        <a href="{{ route('community.index') }}" class="mobile-link">
            <i class="fas fa-users"></i> {{ __('Community') }}
        </a>
        <a href="{{ route('ratings.index') }}" class="mobile-link">
            <i class="fas fa-trophy"></i> {{ __('Ratings') }}
        </a>
        <a href="{{ route('vacancies.index') }}" class="mobile-link">
            <i class="fas fa-briefcase"></i> {{ __('Vacancies') }}
        </a>
        <a href="{{ route('interview.index') }}" class="mobile-link mobile-link-nested">
            <i class="fas fa-user-tie"></i> {{ __('Interview Prep') }}
        </a>
        <a href="{{ route('peer.index') }}" class="mobile-link mobile-link-nested">
            <i class="fas fa-users"></i> {{ __('Peer Interview Room') }}
        </a>
    </div>

    <div class="mobile-menu-divider"></div>

    <div style="padding:4px 16px">
        <div class="mobile-menu-section-title">config/</div>
        <div class="mobile-link mobile-theme-row">
            <span style="font-size:13px;font-weight:500;color:var(--text-muted)">{{ __('header_theme_label') }}</span>
            <div style="display:flex;gap:6px;margin-left:auto" id="mobileThemeDots">
                <button data-theme-val="neon" class="mobile-theme-dot" style="background:linear-gradient(135deg,#00F5D4,#7C3AED)"></button>
                <button data-theme-val="vscode" class="mobile-theme-dot" style="background:linear-gradient(135deg,#007ACC,#4EC9B0)"></button>
                <button data-theme-val="nord" class="mobile-theme-dot" style="background:linear-gradient(135deg,#88C0D0,#B48EAD)"></button>
                <button data-theme-val="github" class="mobile-theme-dot" style="background:linear-gradient(135deg,#2F81F7,#A371F7)"></button>
            </div>
        </div>
        <div style="padding:10px 16px 6px 34px">
            <div style="font-size:12px;color:var(--text-muted);margin-bottom:6px">{{ __('header_language_label') }}</div>
            <div style="display:flex;gap:6px">
                <a href="{{ route('lang.switch', 'ru') }}" class="mobile-lang-btn">RU</a>
                <a href="{{ route('lang.switch', 'en') }}" class="mobile-lang-btn">EN</a>
                <a href="{{ route('lang.switch', 'tg') }}" class="mobile-lang-btn">TJ</a>
            </div>
        </div>
    </div>

    @guest
    <div class="mobile-menu-footer">
        <div class="mobile-menu-terminal-line" style="margin-bottom:12px">
            <span class="mobile-menu-prompt">$</span> <span class="mobile-menu-cmd">auth --login</span>
        </div>
        <a href="{{ route('login') }}" class="nav-btn nav-btn-ghost" style="text-align:center">{{ __('Login') }}</a>
        <a href="{{ route('register') }}" class="nav-btn nav-btn-primary" style="text-align:center">{{ __('Register') }}</a>
    </div>
    @endguest
    @auth
    @php
        $user = Auth::user();
        $fireLevel = $user->getFireLevel();
        $fireColor = $user->getFireColor();
        $streak = $user->streak_count;
    @endphp
    <div class="mobile-menu-footer">
        @if($streak > 0)
        <div class="mobile-fire-badge">
            <i class="fas fa-fire" style="color:{{ $fireColor }};filter:drop-shadow(0 0 3px {{ $fireColor }}40)"></i>
            <span>{{ $streak }}d</span>
        </div>
        @endif
        <div class="mobile-menu-user">
            <img src="{{ Auth::user()->avatar_url }}" class="mobile-menu-user-avatar">
            <div>
                <div class="mobile-menu-user-name">{{ Auth::user()->name ?? __('User') }}</div>
                <div class="mobile-menu-user-email">{{ Auth::user()->email ?? '' }}</div>
            </div>
        </div>
        <a href="{{ route('dashboard') }}" class="mobile-link"><i class="fas fa-th-large"></i> {{ __('Dashboard') }}</a>
        <a href="{{ route('profile.index') }}" class="mobile-link"><i class="fas fa-user"></i> {{ __('Profile') }}</a>
        <a href="{{ route('logout') }}" class="mobile-link mobile-link-logout" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
            <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
        </a>
        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
    </div>
    @endauth
</div>

<script>
(function(){
    var hamburger = document.getElementById('navHamburger');
    var panel = document.getElementById('mobileMenuPanel');
    var nav = document.querySelector('.nav');
    var isOpen = false;

    function openMenu() {
        isOpen = true;
        panel.classList.add('open');
        hamburger.classList.add('is-active');
        hamburger.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }
    function closeMenu() {
        isOpen = false;
        panel.classList.remove('open');
        hamburger.classList.remove('is-active');
        hamburger.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }
    function toggleMenu() {
        isOpen ? closeMenu() : openMenu();
    }

    if (hamburger && panel) {
        hamburger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleMenu();
        });
        panel.querySelectorAll('a, button').forEach(function(el) {
            el.addEventListener('click', closeMenu);
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isOpen) closeMenu();
        });
        var lastY = 0;
        window.addEventListener('scroll', function() {
            var y = window.pageYOffset;
            if (isOpen && (y > lastY + 10 || y < 80)) closeMenu();
            lastY = y;
        }, { passive: true });
    }

    var themeDots = document.getElementById('mobileThemeDots');
    if (themeDots) {
        themeDots.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-theme-val]');
            if (!btn) return;
            var name = btn.getAttribute('data-theme-val');
            localStorage.setItem('theme', name);
            var light = localStorage.getItem('theme-light') === '1';
            document.documentElement.setAttribute('data-theme', light ? name + '-light' : name);
        });
    }

    function navController() {
        return {
            scrolled: false,
            hidden: false,
            lastScroll: 0,
            mobileOpen: false,
            themeOpen: false,
            langOpen: false,
            userOpen: false,
            notifOpen: false,
            dropdownEduc: false,
            dropdownVacs: false,
            hasUnread: false,
            notifications: [],
            unreadCount: 0,
            notifLoaded: false,
            currentTheme: 'neon',
            isLight: false,
            currentLang: 'ru',
            init() {
                this.scrolled = window.pageYOffset > 20;
                this.detectLang();
                var self = this;
                window.addEventListener('scroll', function() {
                    var y = window.pageYOffset;
                    self.scrolled = y > 20;
                    if (y < 80) { self.hidden = false; self.lastScroll = y; return; }
                    if (y > self.lastScroll + 10 && y > 80) {
                        if (!self.hidden) self.hidden = true;
                    } else if (y < self.lastScroll - 15) {
                        if (self.hidden) self.hidden = false;
                    }
                    self.lastScroll = y;
                }, { passive: true });
                var saved = localStorage.getItem('theme') || 'neon';
                var light = localStorage.getItem('theme-light') === '1';
                this.isLight = light;
                this.currentTheme = saved;
                this.applyTheme(saved, light);
            },
            detectLang() {
                var lang = (document.documentElement.getAttribute('lang') || 'ru');
                if (lang.startsWith('ru')) this.currentLang = 'ru';
                else if (lang.startsWith('en')) this.currentLang = 'en';
                else if (lang.startsWith('tg')) this.currentLang = 'tg';
            },
            setTheme(name) {
                this.currentTheme = name;
                localStorage.setItem('theme', name);
                this.applyTheme(name, this.isLight);
                this.themeOpen = false;
            },
            toggleLightMode() {
                this.isLight = !this.isLight;
                localStorage.setItem('theme-light', this.isLight ? '1' : '0');
                this.applyTheme(this.currentTheme, this.isLight);
                this.themeOpen = false;
            },
            applyTheme(name, light) {
                document.documentElement.setAttribute('data-theme', light ? name + '-light' : name);
            },
            async loadNotifs() {
                try {
                    const res = await fetch('/api/notifications');
                    const data = await res.json();
                    if (data.ok) {
                        this.notifications = data.notifications;
                        this.unreadCount = data.unread_count;
                        this.notifLoaded = true;
                    }
                } catch(e) {}
            },
            async markRead(n) {
                if (n.is_read) return;
                try {
                    await fetch('/notifications/mark-read', {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'}
                    });
                    n.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                } catch(e) {}
            },
            async markAllRead() {
                try {
                    await fetch('/notifications/mark-read', {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'}
                    });
                    this.notifications.forEach(n => n.is_read = true);
                    this.unreadCount = 0;
                } catch(e) {}
            },
            timeAgo(dt) {
                if (!dt) return '';
                var diff = (Date.now() - new Date(dt).getTime()) / 1000;
                if (diff < 60) return '{{ __("just now") }}';
                if (diff < 3600) return Math.floor(diff / 60) + ' {{ __("min ago") }}';
                if (diff < 86400) return Math.floor(diff / 3600) + ' {{ __("h ago") }}';
                return Math.floor(diff / 86400) + ' {{ __("d ago") }}';
            }
        }
    }
    window.navController = navController;

    var LEVEL_THEMES = {
        ember:     { label:'EMBER',      sub:'{{ $streak ?? 0 }} {{ __("days") }}',          cols:['#94a3b8','#cbd5e1','#64748b','#818cf8'], particles:30,  fireworks:1,  confetti:10, fanfare:'none', speed:4, glow:8 },
        spark:     { label:'SPARK',      sub:'{{ $streak ?? 0 }} {{ __("days") }}',          cols:['#7dd3fc','#93c5fd','#60a5fa','#818cf8'], particles:40,  fireworks:2,  confetti:15, fanfare:'none', speed:5, glow:10 },
        warm:      { label:'WARM',       sub:'{{ $streak ?? 0 }} {{ __("days_warm") }}',     cols:['#facc15','#fbbf24','#f59e0b','#fb923c'], particles:50,  fireworks:2,  confetti:25, fanfare:'stars', speed:6, glow:14 },
        hot:       { label:'ON FIRE',    sub:'{{ $streak ?? 0 }} {{ __("days_hot") }}',      cols:['#fb923c','#f97316','#ef4444','#fbbf24'], particles:60,  fireworks:3,  confetti:35, fanfare:'flames', speed:7, glow:18 },
        super:     { label:'SUPER',      sub:'{{ $streak ?? 0 }} {{ __("days_strong") }}',   cols:['#f97316','#ef4444','#fb923c','#facc15'], particles:70,  fireworks:3,  confetti:40, fanfare:'rings', speed:8, glow:22 },
        mega:      { label:'MEGA',       sub:'{{ $streak ?? 0 }} {{ __("days_powerful") }}', cols:['#ef4444','#dc2626','#f97316','#fbbf24'], particles:85,  fireworks:4,  confetti:50, fanfare:'burst', speed:9, glow:26 },
        supernova: { label:'SUPERNOVA',  sub:'{{ $streak ?? 0 }} {{ __("days_unstoppable") }}',cols:['#dc2626','#ef4444','#facc15','#f97316'],particles:100, fireworks:5,  confetti:60, fanfare:'wave', speed:10, glow:30 },
        inferno:   { label:'INFERNO',    sub:'{{ $streak ?? 0 }} {{ __("days_of_fire") }}',  cols:['#dc2626','#9333ea','#f97316','#facc15','#ef4444'], particles:120, fireworks:6,  confetti:70, fanfare:'volcano', speed:11, glow:36 },
        ascended:  { label:'ASCENDED',   sub:'{{ $streak ?? 0 }} {{ __("days_enlightened") }}',cols:['#facc15','#fef08a','#fff','#fbbf24'], particles:140, fireworks:6,  confetti:80, fanfare:'halo', speed:12, glow:40 },
        immortal:  { label:'IMMORTAL',   sub:'{{ $streak ?? 0 }} {{ __("days_eternal") }}',  cols:['#a855f7','#c084fc','#ec4899','#dc2626'], particles:160, fireworks:7,  confetti:90, fanfare:'vortex', speed:13, glow:44 },
        legendary: { label:'LEGENDARY',  sub:'{{ $streak ?? 0 }} {{ __("days_of_legend") }}',cols:['#ec4899','#f472b6','#f97316','#a855f7'], particles:180, fireworks:8,  confetti:100, fanfare:'diamond', speed:14, glow:48 },
        titan:     { label:'TITAN',      sub:'{{ $streak ?? 0 }} {{ __("days_supreme") }}',cols:['#22d3ee','#67e8f9','#a855f7','#f472b6','#c084fc'], particles:200, fireworks:9,  confetti:110, fanfare:'lightning', speed:15, glow:52 },
        eternal:   { label:'ETERNAL',    sub:'{{ $streak ?? 0 }} {{ __("days_forever") }}',  cols:['#ef4444','#a855f7','#22d3ee','#facc15','#ec4899','#f97316'], particles:240, fireworks:12, confetti:130, fanfare:'rainbow', speed:16, glow:60 }
    };

    function _createCanvas() {
        var cv = document.createElement('canvas');
        cv.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;pointer-events:none;z-index:99999';
        cv.width = window.innerWidth; cv.height = window.innerHeight;
        document.body.appendChild(cv);
        return cv;
    }

    function _drawStar(ctx, spikes, outerR, innerR) {
        var rot = Math.PI / 2 * 3, step = Math.PI / spikes;
        ctx.beginPath(); ctx.moveTo(0, -outerR);
        for (var i = 0; i < spikes; i++) {
            ctx.lineTo(Math.cos(rot) * outerR, Math.sin(rot) * outerR); rot += step;
            ctx.lineTo(Math.cos(rot) * innerR, Math.sin(rot) * innerR); rot += step;
        }
        ctx.closePath(); ctx.fill();
    }

    function _burstParticles(ctx, cx, cy, theme, t0) {
        var pts = [];
        var baseSpd = theme.speed || 4;
        for (var i = 0; i < theme.particles; i++) {
            var a = Math.PI * 2 * Math.random();
            var spd = baseSpd * (0.4 + Math.random() * 0.8);
            pts.push({
                x: cx, y: cy, vx: Math.cos(a) * spd, vy: Math.sin(a) * spd - 3,
                life: 1.5 + Math.random() * 2.5, t: 0, size: 2 + Math.random() * 5,
                color: theme.cols[i % theme.cols.length], rot: Math.random() * 6.28, rs: (Math.random() - 0.5) * 0.3,
                trail: []
            });
        }
        return pts;
    }

    function _launchFirework(ctx, theme) {
        var x = Math.random() * window.innerWidth * 0.6 + window.innerWidth * 0.2;
        var targetY = 50 + Math.random() * window.innerHeight * 0.3;
        var sparks = [];
        var col = theme.cols[Math.floor(Math.random() * theme.cols.length)];
        var sparkCount = 30 + Math.floor(Math.random() * 30);
        for (var i = 0; i < sparkCount; i++) {
            var a = Math.PI * 2 * Math.random();
            var spd = 1.5 + Math.random() * 4;
            sparks.push({
                x: x, y: targetY, vx: Math.cos(a) * spd, vy: Math.sin(a) * spd,
                life: 1 + Math.random() * 1.5, t: 0, size: 1.5 + Math.random() * 2.5,
                color: Math.random() > 0.5 ? col : theme.cols[(i + 1) % theme.cols.length],
                trail: []
            });
        }
        return { x: x, startY: window.innerHeight + 10, targetY: targetY, t: 0, dur: 0.5 + Math.random() * 0.3, sparks: sparks, launched: false, col: col };
    }

    function _spawnConfetti(theme, w, h) {
        var pieces = [];
        for (var i = 0; i < theme.confetti; i++) {
            pieces.push({
                x: Math.random() * w, y: -20 - Math.random() * h * 0.5,
                w: 4 + Math.random() * 6, h: 8 + Math.random() * 10,
                color: theme.cols[Math.floor(Math.random() * theme.cols.length)],
                rot: Math.random() * 6.28, rs: (Math.random() - 0.5) * 0.15,
                vx: (Math.random() - 0.5) * 2, vy: 1.5 + Math.random() * 3,
                wobble: Math.random() * 6.28, wobbleSpeed: 0.03 + Math.random() * 0.05,
                life: 3 + Math.random() * 2, t: 0
            });
        }
        return pieces;
    }

    function _drawFanfare(ctx, fanfare, elapsed, cx, cy, theme) {
        var t = elapsed / 1000;
        ctx.save();
        if (fanfare === 'stars') {
            for (var i = 0; i < 8; i++) {
                var a = (i / 8) * Math.PI * 2 + t * 0.5;
                var r = 60 + Math.sin(t * 3 + i) * 20;
                var sx = cx + Math.cos(a) * r, sy = cy + Math.sin(a) * r;
                ctx.globalAlpha = 0.6 + Math.sin(t * 4 + i) * 0.3;
                ctx.fillStyle = theme.cols[i % theme.cols.length];
                ctx.shadowColor = theme.cols[i % theme.cols.length]; ctx.shadowBlur = 8;
                ctx.translate(sx, sy); ctx.rotate(t * 2 + i);
                _drawStar(ctx, 5, 8, 3); ctx.setTransform(1, 0, 0, 1, 0, 0);
            }
        } else if (fanfare === 'flames') {
            for (var i = 0; i < 12; i++) {
                var a = (i / 12) * Math.PI * 2;
                var r = 50 + Math.sin(t * 5 + i * 0.5) * 15;
                var fx = cx + Math.cos(a) * r, fy = cy + Math.sin(a) * r;
                var fs = 6 + Math.sin(t * 6 + i) * 3;
                ctx.globalAlpha = 0.7 + Math.sin(t * 4 + i) * 0.3;
                ctx.fillStyle = theme.cols[i % theme.cols.length];
                ctx.shadowColor = theme.cols[i % theme.cols.length]; ctx.shadowBlur = 12;
                ctx.beginPath(); ctx.arc(fx, fy, fs, 0, Math.PI * 2); ctx.fill();
            }
        } else if (fanfare === 'rings') {
            for (var r = 0; r < 4; r++) {
                var radius = 40 + r * 30 + Math.sin(t * 3 + r) * 10;
                ctx.globalAlpha = 0.3 + Math.sin(t * 2 + r) * 0.2;
                ctx.strokeStyle = theme.cols[r % theme.cols.length];
                ctx.lineWidth = 2; ctx.shadowColor = ctx.strokeStyle; ctx.shadowBlur = 10;
                ctx.beginPath(); ctx.arc(cx, cy, radius, 0, Math.PI * 2); ctx.stroke();
            }
        } else if (fanfare === 'burst') {
            for (var i = 0; i < 6; i++) {
                var a = (i / 6) * Math.PI * 2 + t;
                var r = 30 + t * 40;
                var bx = cx + Math.cos(a) * r, by = cy + Math.sin(a) * r;
                ctx.globalAlpha = Math.max(0, 0.8 - t * 0.3);
                ctx.fillStyle = theme.cols[i % theme.cols.length];
                ctx.shadowColor = ctx.fillStyle; ctx.shadowBlur = 15;
                ctx.translate(bx, by); ctx.rotate(t * 3);
                _drawStar(ctx, 4, 10, 4); ctx.setTransform(1, 0, 0, 1, 0, 0);
            }
        } else if (fanfare === 'wave') {
            for (var i = 0; i < 20; i++) {
                var wx = cx + Math.cos(i * 0.6 + t * 2) * (50 + i * 8);
                var wy = cy + Math.sin(i * 0.6 + t * 2) * (30 + i * 5);
                ctx.globalAlpha = Math.max(0, 0.7 - i * 0.03);
                ctx.fillStyle = theme.cols[i % theme.cols.length];
                ctx.shadowColor = ctx.fillStyle; ctx.shadowBlur = 8;
                ctx.beginPath(); ctx.arc(wx, wy, 3 + Math.sin(t * 4 + i) * 2, 0, Math.PI * 2); ctx.fill();
            }
        } else if (fanfare === 'volcano') {
            for (var i = 0; i < 15; i++) {
                var vx = cx + (Math.random() - 0.5) * 20;
                var vy = cy - Math.random() * t * 120;
                ctx.globalAlpha = Math.max(0, 1 - (cy - vy) / 200);
                ctx.fillStyle = theme.cols[i % theme.cols.length];
                ctx.shadowColor = ctx.fillStyle; ctx.shadowBlur = 10;
                ctx.beginPath(); ctx.arc(vx, vy, 3 + Math.random() * 4, 0, Math.PI * 2); ctx.fill();
            }
        } else if (fanfare === 'halo') {
            ctx.globalAlpha = 0.4 + Math.sin(t * 2) * 0.3;
            ctx.strokeStyle = '#facc15'; ctx.lineWidth = 3; ctx.shadowColor = '#facc15'; ctx.shadowBlur = 20;
            ctx.beginPath(); ctx.ellipse(cx, cy - 10, 40 + Math.sin(t) * 5, 12, 0, 0, Math.PI * 2); ctx.stroke();
            ctx.globalAlpha = 0.2 + Math.sin(t * 3) * 0.15;
            ctx.strokeStyle = '#fef08a'; ctx.lineWidth = 2;
            ctx.beginPath(); ctx.ellipse(cx, cy - 10, 55 + Math.sin(t * 1.5) * 8, 16, 0, 0, Math.PI * 2); ctx.stroke();
        } else if (fanfare === 'vortex') {
            for (var i = 0; i < 30; i++) {
                var a = (i / 30) * Math.PI * 2 + t * 2;
                var r = 20 + i * 3 + Math.sin(t + i * 0.3) * 10;
                var vx = cx + Math.cos(a) * r, vy = cy + Math.sin(a) * r;
                ctx.globalAlpha = Math.max(0, 0.7 - i * 0.02);
                ctx.fillStyle = theme.cols[i % theme.cols.length];
                ctx.shadowColor = ctx.fillStyle; ctx.shadowBlur = 8;
                ctx.beginPath(); ctx.arc(vx, vy, 2.5, 0, Math.PI * 2); ctx.fill();
            }
        } else if (fanfare === 'diamond') {
            for (var i = 0; i < 8; i++) {
                var a = (i / 8) * Math.PI * 2 + t * 0.8;
                var r = 50 + Math.sin(t * 2 + i) * 15;
                var dx = cx + Math.cos(a) * r, dy = cy + Math.sin(a) * r;
                ctx.globalAlpha = 0.6 + Math.sin(t * 3 + i) * 0.3;
                ctx.fillStyle = theme.cols[i % theme.cols.length];
                ctx.shadowColor = ctx.fillStyle; ctx.shadowBlur = 15;
                ctx.save(); ctx.translate(dx, dy); ctx.rotate(t + i);
                ctx.beginPath(); ctx.moveTo(0, -8); ctx.lineTo(6, 0); ctx.lineTo(0, 8); ctx.lineTo(-6, 0); ctx.closePath(); ctx.fill();
                ctx.restore();
            }
        } else if (fanfare === 'lightning') {
            if (Math.sin(t * 8) > 0.7) {
                ctx.globalAlpha = 0.8;
                ctx.strokeStyle = '#22d3ee'; ctx.lineWidth = 2; ctx.shadowColor = '#22d3ee'; ctx.shadowBlur = 20;
                ctx.beginPath();
                var lx = cx, ly = cy;
                for (var s = 0; s < 5; s++) {
                    ctx.lineTo(lx, ly); lx += (Math.random() - 0.5) * 40; ly += 20 + Math.random() * 15;
                }
                ctx.stroke();
            }
            for (var i = 0; i < 5; i++) {
                var mx = cx + Math.cos(t + i * 1.3) * 70, my = cy + Math.sin(t * 0.7 + i) * 50;
                ctx.globalAlpha = 0.5; ctx.fillStyle = theme.cols[i % theme.cols.length];
                ctx.shadowColor = ctx.fillStyle; ctx.shadowBlur = 12;
                ctx.translate(mx, my); ctx.rotate(t * 2);
                _drawStar(ctx, 5, 6, 2); ctx.setTransform(1, 0, 0, 1, 0, 0);
            }
        } else if (fanfare === 'rainbow') {
            var rainbowCols = ['#ef4444','#f97316','#facc15','#22c55e','#3b82f6','#a855f7'];
            for (var i = 0; i < rainbowCols.length; i++) {
                var r = 30 + i * 15 + Math.sin(t * 2 + i) * 8;
                ctx.globalAlpha = 0.25 + Math.sin(t * 3 + i) * 0.15;
                ctx.strokeStyle = rainbowCols[i]; ctx.lineWidth = 3;
                ctx.shadowColor = rainbowCols[i]; ctx.shadowBlur = 10;
                ctx.beginPath(); ctx.arc(cx, cy, r, t + i * 0.3, t + i * 0.3 + Math.PI * 1.2); ctx.stroke();
            }
            for (var i = 0; i < 20; i++) {
                var da = (i / 20) * Math.PI * 2 + t;
                var dr = 80 + Math.sin(t * 2 + i * 0.5) * 20;
                ctx.globalAlpha = 0.4; ctx.fillStyle = rainbowCols[i % rainbowCols.length];
                ctx.shadowColor = ctx.fillStyle; ctx.shadowBlur = 6;
                ctx.beginPath(); ctx.arc(cx + Math.cos(da) * dr, cy + Math.sin(da) * dr, 2, 0, Math.PI * 2); ctx.fill();
            }
        }
        ctx.restore();
    }

    window.triggerFireBurst = function(el, streak, level) {
        var theme = LEVEL_THEMES[level] || LEVEL_THEMES.ember;
        var rect = el.getBoundingClientRect();
        var cx = rect.left + rect.width / 2, cy = rect.top + rect.height / 2;
        var W = window.innerWidth, H = window.innerHeight;

        var cv = _createCanvas();
        var ctx = cv.getContext('2d');
        var t0 = performance.now();
        var running = true;

        var particles = _burstParticles(ctx, cx, cy, theme, t0);
        var fireworks = [];
        var confetti = _spawnConfetti(theme, W, H);
        var fireworkTimings = [];
        for (var f = 0; f < theme.fireworks; f++) {
            fireworkTimings.push(800 + f * 600 + Math.random() * 400);
        }

        el.style.transition = 'transform 0.15s cubic-bezier(0.175,0.885,0.32,1.275)';
        el.style.transform = 'scale(1.3)';
        setTimeout(function() { el.style.transform = 'scale(0.9)'; setTimeout(function() { el.style.transform = ''; }, 120); }, 150);

        function frame(now) {
            if (!running) return;
            var elapsed = now - t0;
            ctx.clearRect(0, 0, W, H);

            var fadeAlpha = 1;
            if (elapsed > 5000) {
                fadeAlpha = Math.max(0, 1 - (elapsed - 5000) / 1500);
            }
            ctx.globalAlpha = fadeAlpha;

            var alive = 0;

            for (var i = 0; i < particles.length; i++) {
                var p = particles[i];
                p.t += 0.016;
                if (p.t > p.life) continue;
                alive++;
                p.x += p.vx; p.y += p.vy; p.vy += 0.06; p.vx *= 0.99; p.rot += p.rs;
                p.trail.push({ x: p.x, y: p.y });
                if (p.trail.length > 5) p.trail.shift();
                var alpha = 1 - p.t / p.life;
                var sz = p.size * (0.4 + 0.6 * (1 - p.t / p.life));

                for (var j = 0; j < p.trail.length; j++) {
                    ctx.globalAlpha = alpha * (j / p.trail.length) * 0.25;
                    ctx.fillStyle = p.color;
                    ctx.beginPath(); ctx.arc(p.trail[j].x, p.trail[j].y, sz * 0.3, 0, Math.PI * 2); ctx.fill();
                }

                ctx.save(); ctx.globalAlpha = alpha; ctx.translate(p.x, p.y); ctx.rotate(p.rot);
                ctx.shadowColor = p.color; ctx.shadowBlur = 10;
                ctx.fillStyle = p.color;
                ctx.beginPath(); ctx.arc(0, 0, sz, 0, Math.PI * 2); ctx.fill();
                ctx.restore();
            }

            for (var f = 0; f < fireworkTimings.length; f++) {
                if (elapsed >= fireworkTimings[f] && !fireworks[f]) {
                    fireworks[f] = _launchFirework(ctx, theme);
                }
                var fw = fireworks[f];
                if (!fw) continue;
                if (!fw.launched) {
                    fw.t += 0.016;
                    var progress = Math.min(fw.t / fw.dur, 1);
                    var curY = fw.startY + (fw.targetY - fw.startY) * progress;
                    ctx.globalAlpha = 1; ctx.fillStyle = fw.col;
                    ctx.shadowColor = fw.col; ctx.shadowBlur = 8;
                    ctx.beginPath(); ctx.arc(fw.x, curY, 3, 0, Math.PI * 2); ctx.fill();
                    ctx.globalAlpha = 0.3; ctx.strokeStyle = fw.col; ctx.lineWidth = 1;
                    ctx.beginPath(); ctx.moveTo(fw.x, curY); ctx.lineTo(fw.x, curY + 15); ctx.stroke();
                    if (progress >= 1) fw.launched = true;
                    alive++;
                } else {
                    for (var s = 0; s < fw.sparks.length; s++) {
                        var sp = fw.sparks[s];
                        sp.t += 0.016;
                        if (sp.t > sp.life) continue;
                        alive++;
                        sp.x += sp.vx; sp.y += sp.vy; sp.vy += 0.04; sp.vx *= 0.98;
                        sp.trail.push({ x: sp.x, y: sp.y });
                        if (sp.trail.length > 4) sp.trail.shift();
                        var sa = 1 - sp.t / sp.life;
                        var ss = sp.size * (1 - sp.t / sp.life * 0.5);
                        for (var j = 0; j < sp.trail.length; j++) {
                            ctx.globalAlpha = sa * (j / sp.trail.length) * 0.2;
                            ctx.fillStyle = sp.color;
                            ctx.beginPath(); ctx.arc(sp.trail[j].x, sp.trail[j].y, ss * 0.3, 0, Math.PI * 2); ctx.fill();
                        }
                        ctx.save(); ctx.globalAlpha = sa; ctx.fillStyle = sp.color;
                        ctx.shadowColor = sp.color; ctx.shadowBlur = 6;
                        ctx.beginPath(); ctx.arc(sp.x, sp.y, ss, 0, Math.PI * 2); ctx.fill();
                        ctx.restore();
                    }
                }
            }

            for (var i = 0; i < confetti.length; i++) {
                var c = confetti[i];
                c.t += 0.016;
                if (c.t > c.life) continue;
                alive++;
                c.wobble += c.wobbleSpeed;
                c.x += c.vx + Math.sin(c.wobble) * 1.5;
                c.y += c.vy;
                c.rot += c.rs;
                var ca = Math.max(0, 1 - c.t / c.life);
                ctx.save(); ctx.globalAlpha = ca; ctx.translate(c.x, c.y); ctx.rotate(c.rot);
                ctx.fillStyle = c.color;
                ctx.shadowColor = c.color; ctx.shadowBlur = 4;
                ctx.fillRect(-c.w / 2, -c.h / 2, c.w, c.h);
                ctx.restore();
            }

            ctx.globalAlpha = 1;

            _drawFanfare(ctx, theme.fanfare, elapsed, cx, cy, theme);

            if (alive > 0 && elapsed < 7000) {
                requestAnimationFrame(frame);
            } else {
                running = false; cv.remove();
            }
        }
        requestAnimationFrame(frame);
        setTimeout(function() { running = false; cv.remove(); }, 7500);
    };

    (function() {
        var fireBadge = document.querySelector('.nav-fire-badge');
        if (!fireBadge) return;
        var upEl = document.getElementById('fire-level-up');
        if (upEl) {
            var lvl = upEl.getAttribute('data-level');
            var str = parseInt(upEl.getAttribute('data-streak')) || 0;
            if (lvl && str > 0) {
                setTimeout(function() { window.triggerFireBurst(fireBadge, str, lvl); }, 600);
            }
        }
    })();
})();
</script>
