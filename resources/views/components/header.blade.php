<nav class="nav" x-data="navController()" x-init="init()" :class="(scrolled ? 'scrolled' : '') + (hidden ? ' hidden' : '')">
    <div class="nav-inner">
        <!-- Terminal-style Logo -->
        <a href="{{ route('home') }}" class="nav-logo">
            <div class="nav-logo-icon"><i class="fas fa-terminal"></i></div>
            <span class="nav-logo-text"><span class="nav-logo-bracket">&gt;</span> Code<span class="nav-logo-accent">Master</span><span class="nav-logo-cursor">_</span></span>
        </a>

        <!-- Code-style Navigation -->
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
                </div>
            </div>
        </div>

        <div class="nav-actions">
            <!-- Language Switcher -->
            <div class="lang-switcher" @click.away="langOpen = false">
                <button class="lang-switcher-btn" @click="langOpen = !langOpen" title="Language">
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

            <!-- Theme Switcher -->
            <div class="theme-switcher" @click.away="themeOpen = false">
                <button class="nav-icon-btn" @click="themeOpen = !themeOpen" title="Switch theme">
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
                        <span x-text="isLight ? 'Dark Mode' : 'Light Mode'"></span>
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
            <!-- Notifications -->
            <div class="nav-notif" @click.away="notifOpen = false">
                <button class="nav-icon-btn" @click="notifOpen = !notifOpen" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="nav-notif-badge" x-show="hasUnread"></span>
                </button>
                <div class="notif-dropdown" x-show="notifOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95">
                    <div class="notif-dropdown-header">
                        <i class="fas fa-bell" style="font-size:12px;margin-right:6px"></i>{{ __('Notifications') }}
                    </div>
                    <div class="notif-empty">
                        <i class="fas fa-inbox" style="font-size:20px;display:block;margin-bottom:8px;opacity:0.3"></i>
                        {{ __('No notifications') }}
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="user-menu" @click.away="userOpen = false">
                <button class="user-avatar-btn" @click="userOpen = !userOpen">
                    <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'U').'&background=6366f1&color=fff&size=40' }}" alt="Avatar" class="user-avatar-img">
                </button>
                <div class="user-dropdown" x-show="userOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95">
                    <div class="user-dropdown-header">
                        <p class="user-dropdown-name">{{ Auth::user()->name ?? __('User') }}</p>
                        <p class="user-dropdown-email">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                    <div class="theme-dropdown-divider"></div>
                    <a href="{{ route('dashboard') }}" class="user-dropdown-link"><i class="fas fa-th-large"></i> {{ __('Dashboard') }}</a>
                    <a href="{{ route('profile.index') }}" class="user-dropdown-link"><i class="fas fa-user"></i> {{ __('Profile') }}</a>
                    <div class="theme-dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="user-dropdown-link user-dropdown-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
                </div>
            </div>
            @endauth

            <button class="nav-mobile-btn" @click="mobileOpen = !mobileOpen">
                <i class="fas" :class="mobileOpen ? 'fa-times' : 'fa-bars'"></i>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" :class="mobileOpen ? 'open' : ''" x-show="mobileOpen" x-transition>
    <div class="mobile-menu-header">
        <div class="mobile-menu-terminal-line">
            <span class="mobile-menu-prompt">$</span> <span class="mobile-menu-cmd">ls -la sections/</span>
        </div>
    </div>

    <div style="padding:8px 16px 4px">
        <div class="mobile-menu-section-title">education/</div>
        <a href="{{ route('courses.index') }}" class="mobile-link">
            <span class="mobile-link-prefix">├──</span>
            <i class="fas fa-book" style="width:20px"></i> {{ __('Courses') }}
        </a>
        <a href="{{ route('roadmaps.index') }}" class="mobile-link">
            <span class="mobile-link-prefix">└──</span>
            <i class="fas fa-project-diagram" style="width:20px"></i> {{ __('Roadmaps') }}
        </a>
    </div>
    <div style="padding:4px 16px">
        <div class="mobile-menu-section-title">competitive/</div>
        <a href="{{ route('contests.index') }}" class="mobile-link">
            <span class="mobile-link-prefix">└──</span>
            <i class="fas fa-code" style="width:20px"></i> {{ __('Contests') }}
        </a>
    </div>
    <div style="padding:4px 16px">
        <div class="mobile-menu-section-title">career/</div>
        <a href="{{ route('community.index') }}" class="mobile-link">
            <span class="mobile-link-prefix">├──</span>
            <i class="fas fa-users" style="width:20px"></i> {{ __('Community') }}
        </a>
        <a href="{{ route('ratings.index') }}" class="mobile-link">
            <span class="mobile-link-prefix">├──</span>
            <i class="fas fa-trophy" style="width:20px"></i> {{ __('Ratings') }}
        </a>
        <a href="{{ route('vacancies.index') }}" class="mobile-link">
            <span class="mobile-link-prefix">├──</span>
            <i class="fas fa-briefcase" style="width:20px"></i> {{ __('Vacancies') }}
        </a>
        <a href="{{ route('interview.index') }}" class="mobile-link mobile-link-nested">
            <span class="mobile-link-prefix">├──</span>
            <i class="fas fa-user-tie" style="width:20px"></i> {{ __('Interview Prep') }}
        </a>
        <a href="{{ route('interview.index') }}" class="mobile-link mobile-link-nested">
            <span class="mobile-link-prefix">└──</span>
            <i class="fas fa-video" style="width:20px"></i> {{ __('Interview') }}
        </a>
    </div>

    <div class="mobile-menu-divider"></div>

    <div style="padding:4px 16px">
        <div class="mobile-menu-section-title">config/</div>
        <div class="mobile-link mobile-theme-row">
            <span class="mobile-link-prefix">├──</span>
            <span style="font-size:13px;font-weight:500;color:var(--text-muted)">theme:</span>
            <div style="display:flex;gap:4px;margin-left:auto">
                <button @click="setTheme('neon')" :style="currentTheme === 'neon' ? 'border-color:var(--accent);box-shadow:0 0 6px var(--accent)' : ''" class="mobile-theme-dot" style="background:linear-gradient(135deg,#00F5D4,#7C3AED)"></button>
                <button @click="setTheme('vscode')" :style="currentTheme === 'vscode' ? 'border-color:var(--accent);box-shadow:0 0 6px var(--accent)' : ''" class="mobile-theme-dot" style="background:linear-gradient(135deg,#007ACC,#4EC9B0)"></button>
                <button @click="setTheme('nord')" :style="currentTheme === 'nord' ? 'border-color:var(--accent);box-shadow:0 0 6px var(--accent)' : ''" class="mobile-theme-dot" style="background:linear-gradient(135deg,#88C0D0,#B48EAD)"></button>
                <button @click="setTheme('github')" :style="currentTheme === 'github' ? 'border-color:var(--accent);box-shadow:0 0 6px var(--accent)' : ''" class="mobile-theme-dot" style="background:linear-gradient(135deg,#2F81F7,#A371F7)"></button>
            </div>
        </div>
        <div style="padding:10px 16px 10px 34px">
            <div style="font-size:12px;color:var(--text-muted)">lang:</div>
            <div style="display:flex;gap:6px;margin-top:6px">
                <a href="{{ route('lang.switch', 'ru') }}" class="mobile-lang-btn" :class="currentLang === 'ru' ? 'active' : ''">🇷🇺 RU</a>
                <a href="{{ route('lang.switch', 'en') }}" class="mobile-lang-btn" :class="currentLang === 'en' ? 'active' : ''">🇬🇧 EN</a>
                <a href="{{ route('lang.switch', 'tg') }}" class="mobile-lang-btn" :class="currentLang === 'tg' ? 'active' : ''">🇹🇯 TJ</a>
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
    <div class="mobile-menu-footer">
        <div class="mobile-menu-terminal-line">
            <span class="mobile-menu-prompt">$</span> <span class="mobile-menu-cmd">whoami</span>
        </div>
        <div class="mobile-menu-user">
            <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'U').'&background=6366f1&color=fff' }}" class="mobile-menu-user-avatar">
            <div>
                <div class="mobile-menu-user-name">{{ Auth::user()->name ?? __('User') }}</div>
                <div class="mobile-menu-user-email">{{ Auth::user()->email ?? '' }}</div>
            </div>
        </div>
        <a href="{{ route('dashboard') }}" class="mobile-link"><i class="fas fa-th-large" style="width:20px"></i> {{ __('Dashboard') }}</a>
        <a href="{{ route('profile.index') }}" class="mobile-link"><i class="fas fa-user" style="width:20px"></i> {{ __('Profile') }}</a>
        <a href="{{ route('logout') }}" class="mobile-link mobile-link-logout" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
            <i class="fas fa-sign-out-alt" style="width:20px"></i> {{ __('Logout') }}
        </a>
        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
    </div>
    @endauth
</div>

<script>
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
        currentTheme: 'neon',
        isLight: false,
        currentLang: 'ru',
        init() {
            this.scrolled = window.pageYOffset > 20;
            this.detectLang();
            window.addEventListener('scroll', () => {
                const y = window.pageYOffset;
                this.scrolled = y > 20;
                if (y < 80) { this.hidden = false; this.lastScroll = y; return; }
                if (y > this.lastScroll + 10 && y > 80) {
                    if (!this.hidden) this.hidden = true;
                } else if (y < this.lastScroll - 15) {
                    if (this.hidden) this.hidden = false;
                }
                this.lastScroll = y;
            }, { passive: true });
            const saved = localStorage.getItem('theme') || 'neon';
            const light = localStorage.getItem('theme-light') === '1';
            this.isLight = light;
            this.currentTheme = saved;
            this.applyTheme(saved, light);
        },
        detectLang() {
            const html = document.documentElement;
            const lang = html.getAttribute('lang') || 'ru';
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
            const t = light ? name + '-light' : name;
            document.documentElement.setAttribute('data-theme', t);
        }
    }
}
</script>
