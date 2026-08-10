<footer class="footer">
    <div class="footer-scanlines"></div>

    <div class="footer-inner">
        <!-- ASCII Art Header -->
        <div class="footer-ascii" id="footerAscii">
            <pre class="footer-ascii-art" id="asciiArt">╔══════════════════════════════════════════════════════════════════════╗
║                                                                ║
║    _____          _      __  __           _                    ║
║   / ____|        | |    |  \/  |         | |                   ║
║  | |     ___   __| | ___| \  / | __ _ ___| |_ ___ _ __         ║
║  | |    / _ \ / _` |/ _ \ |\/| |/ _` / __| __/ _ \ ''__|       ║
║  | |___| (_) | (_| |  __/ |  | | (_| \__ \ ||  __/ |           ║
║   \_____\___/ \__,_|\___|_|  |_|\__,_|___/\__\___|_|           ║
║                                                                ║
║                  IT Education & Career Platform                ║
╚══════════════════════════════════════════════════════════════════════╝</pre>
            <div class="footer-ascii-glow"></div>
            <div class="footer-ascii-sweep"><div class="footer-ascii-sweep-line" id="sweepLine"></div></div>
            <canvas class="footer-ascii-particles" id="asciiParticles"></canvas>
        </div>

        <!-- Terminal-style Navigation -->
        <div class="footer-terminal">
            <div class="footer-terminal-header">
                <div class="footer-terminal-dots">
                    <span class="footer-terminal-dot footer-terminal-dot--red"></span>
                    <span class="footer-terminal-dot footer-terminal-dot--yellow"></span>
                    <span class="footer-terminal-dot footer-terminal-dot--green"></span>
                </div>
                <div class="footer-terminal-title">codemaster@career:~$</div>
            </div>
            <div class="footer-terminal-body">
                <div class="footer-grid">
                    <div class="footer-terminal-col">
                        <div class="footer-terminal-prompt">
                            <span class="footer-terminal-cmd">cat</span> <span
                                class="footer-terminal-file">education.sh</span>
                        </div>
                        <div class="footer-terminal-links">
                            <a href="{{ route('courses.index') }}" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">├──</span>
                                <i class="fas fa-book footer-terminal-fa-icon"></i> {{ __('Courses') }}
                            </a>
                            <a href="{{ route('roadmaps.index') }}" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">├──</span>
                                <i class="fas fa-project-diagram footer-terminal-fa-icon"></i> {{ __('Roadmaps') }}
                            </a>
                            <a href="{{ route('contests.index') }}" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">└──</span>
                                <i class="fas fa-trophy footer-terminal-fa-icon"></i> {{ __('Contests') }}
                            </a>
                        </div>
                    </div>
                    <div class="footer-terminal-col">
                        <div class="footer-terminal-prompt">
                            <span class="footer-terminal-cmd">cat</span> <span
                                class="footer-terminal-file">career.sh</span>
                        </div>
                        <div class="footer-terminal-links">
                            <a href="{{ route('vacancies.index') }}" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">├──</span>
                                <i class="fas fa-briefcase footer-terminal-fa-icon"></i> {{ __('Vacancies') }}
                            </a>
                            <a href="{{ route('interview.index') }}" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">├──</span>
                                <i class="fas fa-microphone footer-terminal-fa-icon"></i> {{ __('Interviews') }}
                            </a>
                            <a href="{{ route('ratings.index') }}" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">├──</span>
                                <i class="fas fa-star footer-terminal-fa-icon"></i> {{ __('Ratings') }}
                            </a>
                            <a href="{{ route('community.index') }}" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">└──</span>
                                <i class="fas fa-users footer-terminal-fa-icon"></i> {{ __('Community') }}
                            </a>
                        </div>
                    </div>
                    <div class="footer-terminal-col">
                        <div class="footer-terminal-prompt">
                            <span class="footer-terminal-cmd">cat</span> <span
                                class="footer-terminal-file">company.sh</span>
                        </div>
                        <div class="footer-terminal-links">
                            <a href="{{ route('static.about') }}" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">├──</span>
                                <i class="fas fa-info-circle footer-terminal-fa-icon"></i> {{ __('About Us') }}
                            </a>
                            <a href="{{ route('static.contacts') }}" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">├──</span>
                                <i class="fas fa-envelope footer-terminal-fa-icon"></i> {{ __('Contacts') }}
                            </a>
                            <a href="{{ route('static.terms') }}" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">├──</span>
                                <i class="fas fa-file-contract footer-terminal-fa-icon"></i> {{ __('Terms') }}
                            </a>
                            <a href="{{ route('static.privacy') }}" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">└──</span>
                                <i class="fas fa-shield-alt footer-terminal-fa-icon"></i> {{ __('Privacy') }}
                            </a>
                        </div>
                    </div>
                    <div class="footer-terminal-col">
                        <div class="footer-terminal-prompt">
                            <span class="footer-terminal-cmd">cat</span> <span
                                class="footer-terminal-file">social.sh</span>
                        </div>
                        <div class="footer-terminal-links">
                            <a href="#" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">├──</span>
                                <i class="fab fa-telegram-plane footer-terminal-fa-icon"></i> Telegram
                            </a>
                            <a href="#" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">├──</span>
                                <i class="fab fa-github footer-terminal-fa-icon"></i> GitHub
                            </a>
                            <a href="#" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">├──</span>
                                <i class="fab fa-youtube footer-terminal-fa-icon"></i> YouTube
                            </a>
                            <a href="#" class="footer-terminal-link">
                                <span class="footer-terminal-prefix">└──</span>
                                <i class="fab fa-instagram footer-terminal-fa-icon"></i> Instagram
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Git-style Footer -->
        <div class="footer-git">
            <div class="footer-git-left">
                <i class="fas fa-code-branch footer-git-icon"></i>
                <span class="footer-git-text">
                    <span class="footer-git-branch">main</span> @ <span
                        class="footer-git-hash">{{ substr(md5(date('Y-m-d')), 0, 7) }}</span>
                </span>
            </div>
            <div class="footer-git-center">
                <span class="footer-git-text">
                    &copy; {{ date('Y') }} <span class="footer-git-author">CodeMaster</span>. {{ __('Crafted with') }}
                    <i class="fas fa-heart footer-git-heart"></i> {{ __('for future IT leaders') }}
                </span>
            </div>
            <div class="footer-git-right">
                <a href="{{ route('static.terms') }}" class="footer-git-link">{{ __('Terms') }}</a>
                <span class="footer-git-sep">|</span>
                <a href="{{ route('static.privacy') }}" class="footer-git-link">{{ __('Privacy') }}</a>
                <span class="footer-git-sep">|</span>
                <span class="footer-git-version">v2.0.0</span>
            </div>
        </div>
    </div>
</footer>

<script>
(function() {
    const pre = document.getElementById('asciiArt');
    const box = document.getElementById('footerAscii');
    const sweepLine = document.getElementById('sweepLine');
    if (!pre || !box) return;

    const GLITCH = '!@#$%^&*<>{}[]|/\\~`';
    const ORIGINAL = pre.textContent;
    let mouseX = -1, mouseY = -1, hovering = false, locked = false;
    let rafPending = false;

    // ─── Wrap chars ───
    const ACC_RE = /[╔╗╚╝║═├└─┬│]/;
    const BIG_RE = /[A-Za-z0-9]/;
    pre.innerHTML = '';
    const frag = document.createDocumentFragment();
    for (let i = 0; i < ORIGINAL.length; i++) {
        const s = document.createElement('span');
        s.className = 'ac';
        s.textContent = ORIGINAL[i];
        if (ACC_RE.test(ORIGINAL[i])) s.classList.add('accent');
        else if (BIG_RE.test(ORIGINAL[i])) s.classList.add('big');
        frag.appendChild(s);
    }
    pre.appendChild(frag);
    const chars = pre.querySelectorAll('.ac');

    // ─── Cache char positions (once, on resize) ───
    let charRects = [];
    function cachePositions() {
        const pr = pre.getBoundingClientRect();
        charRects = Array.from(chars).map(c => {
            const r = c.getBoundingClientRect();
            return { x: r.left - pr.left + r.width / 2, y: r.top - pr.top + r.height / 2 };
        });
    }
    cachePositions();
    let resizeTimer;
    window.addEventListener('resize', () => { clearTimeout(resizeTimer); resizeTimer = setTimeout(cachePositions, 200); });

    // ─── Typing reveal on scroll ───
    let revealed = false;
    const obs = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting && !revealed) {
            revealed = true;
            obs.disconnect();
            let i = 0;
            (function step() {
                if (i < chars.length) {
                    chars[i].style.opacity = '0';
                    const ch = chars[i];
                    setTimeout(() => { ch.style.opacity = ''; ch.classList.add('flash'); setTimeout(() => ch.classList.remove('flash'), 500); }, 0);
                    i++;
                    setTimeout(step, 4);
                }
            })();
        }
    }, { threshold: 0.3 });
    obs.observe(pre);

    // ─── Throttled mouse glow ───
    box.addEventListener('mousemove', e => {
        const r = pre.getBoundingClientRect();
        mouseX = e.clientX - r.left;
        mouseY = e.clientY - r.top;
        hovering = true;
        if (!rafPending) { rafPending = true; requestAnimationFrame(updateGlow); }
    });
    box.addEventListener('mouseleave', () => {
        hovering = false;
        mouseX = mouseY = -1;
        chars.forEach(c => { c.classList.remove('glow', 'glow-strong'); });
    });

    function updateGlow() {
        rafPending = false;
        if (!hovering) return;
        for (let i = 0; i < chars.length; i++) {
            const cr = charRects[i];
            if (!cr) continue;
            const dx = mouseX - cr.x;
            const dy = mouseY - cr.y;
            const d = dx * dx + dy * dy;
            const c = chars[i];
            if (d < 900) { // 30px
                c.classList.add('glow-strong');
                c.classList.remove('glow');
            } else if (d < 4900) { // 70px
                c.classList.add('glow');
                c.classList.remove('glow-strong');
            } else {
                c.classList.remove('glow', 'glow-strong');
            }
        }
    }

    // ─── Click: scramble → decode ───
    box.addEventListener('click', e => {
        if (locked) return;
        locked = true;
        cachePositions();
        const pr = pre.getBoundingClientRect();
        const cx = e.clientX - pr.left;
        const cy = e.clientY - pr.top;

        // flash
        const f = document.createElement('div');
        f.className = 'footer-ascii-flash';
        f.style.cssText = 'width:60px;height:60px;left:' + cx + 'px;top:' + cy + 'px';
        box.appendChild(f);
        setTimeout(() => f.remove(), 600);

        // scramble
        const saved = [];
        for (let i = 0; i < chars.length; i++) {
            saved[i] = chars[i].textContent;
            if (saved[i] !== ' ' && saved[i] !== '\n') {
                chars[i].textContent = GLITCH[Math.random() * GLITCH.length | 0];
                chars[i].classList.add('glow-strong');
            }
        }

        // decode with stagger from click
        setTimeout(() => {
            for (let i = 0; i < chars.length; i++) {
                const cr = charRects[i];
                if (!cr) continue;
                const d = Math.sqrt((cx - cr.x) ** 2 + (cy - cr.y) ** 2);
                setTimeout(() => {
                    chars[i].textContent = saved[i];
                    chars[i].classList.remove('glow-strong');
                    chars[i].classList.add('flash');
                    setTimeout(() => chars[i].classList.remove('flash'), 500);
                }, d * 1.5);
            }
            setTimeout(() => { locked = false; }, 800);
        }, 250);
    });

    // ─── Periodic sweep ───
    function sweep() {
        sweepLine.classList.remove('active');
        void sweepLine.offsetWidth;
        sweepLine.classList.add('active');

        // glitch a few random chars
        for (let n = 0; n < 6; n++) {
            const i = Math.random() * chars.length | 0;
            const ch = chars[i];
            const orig = ch.textContent;
            if (orig === ' ' || orig === '\n') continue;
            ch.textContent = GLITCH[Math.random() * GLITCH.length | 0];
            ch.classList.add('glow');
            setTimeout(() => { ch.textContent = orig; ch.classList.remove('glow'); }, 100);
        }
    }
    setInterval(sweep, 6000);
    setTimeout(sweep, 2500);

    // ─── Particles (20, no lines) ───
    const cvs = document.getElementById('asciiParticles');
    const ctx = cvs.getContext('2d');
    const parts = [];
    function resizeCvs() { cvs.width = box.offsetWidth; cvs.height = box.offsetHeight; }
    resizeCvs();
    window.addEventListener('resize', resizeCvs);

    for (let i = 0; i < 20; i++) {
        parts.push({
            x: Math.random() * cvs.width,
            y: Math.random() * cvs.height,
            vx: (Math.random() - 0.5) * 0.3,
            vy: (Math.random() - 0.5) * 0.3,
            r: Math.random() * 1.5 + 0.5,
            a: Math.random() * 0.4 + 0.1,
            hue: Math.random() > 0.5 ? 170 : 270
        });
    }

    function drawParticles() {
        ctx.clearRect(0, 0, cvs.width, cvs.height);
        for (const p of parts) {
            if (hovering) {
                const dx = p.x - mouseX;
                const dy = p.y - mouseY;
                const d = Math.sqrt(dx * dx + dy * dy);
                if (d < 100) {
                    const f = (100 - d) / 100;
                    p.vx += dx * f * 0.002;
                    p.vy += dy * f * 0.002;
                }
            }
            p.vx *= 0.98; p.vy *= 0.98;
            p.x += p.vx; p.y += p.vy;
            if (p.x < 0 || p.x > cvs.width) p.vx *= -1;
            if (p.y < 0 || p.y > cvs.height) p.vy *= -1;
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, 6.283);
            ctx.fillStyle = 'hsla(' + p.hue + ',80%,60%,' + p.a + ')';
            ctx.fill();
        }
        requestAnimationFrame(drawParticles);
    }
    drawParticles();
})();
</script>