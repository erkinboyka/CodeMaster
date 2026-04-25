(function () {
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const revealVariants = ['fade-up', 'fade-left', 'fade-right', 'zoom-in', 'tilt-up'];
    const autoTargetsSelector = [
        'main .card',
        'main .section-card',
        'main .timeline-item',
        'main .portfolio-card',
        'main .viz-card',
        'main .gh-actions',
        'main .gh-heatmap-card',
        'main .vacancy-card',
        'main .tg-vacancy-card',
        'main .glass-card',
        'main .post-card',
        'main .side-card',
        'main .event-card',
        'main .community-card',
        'main .roadmap-card',
        'main .profile-mobile-tab',
        'main .action-icon',
        'main .skill-tag',
        'main [data-animate]',
        'main .tf-reveal',
        '[role="dialog"]',
        '[id$="Modal"]',
        '.modal',
        '.tf-ai-modal'
    ].join(', ');

    let preloader = null;
    let revealObserver = null;
    let modalObserver = null;

    function randomBetween(min, max) {
        return min + Math.random() * (max - min);
    }

    function createPreloader() {
        if (preloader) return preloader;

        const shell = document.getElementById('tf-preloader') || document.createElement('div');
        shell.id = 'tf-preloader';
        shell.className = 'tf-preloader';
        shell.setAttribute('aria-hidden', 'true');
        shell.innerHTML = '';

        const rain = document.createElement('div');
        rain.className = 'tf-preloader-shell';

        const rainGrid = document.createElement('div');
        rainGrid.className = 'tf-preloader-rain';
        const symbols = ['0', '1', '2', '3', '<', '>', '{', '}', '[', ']', '(', ')', '/', '\\', ';', '.', '=', '+', '-', '*', 'A', 'C', 'D', 'E', 'F', 'K', 'M', 'R', 'S', 'T', 'X', '7', '9'];

        for (let column = 0; column < 28; column += 1) {
            const col = document.createElement('div');
            col.className = 'tf-preloader-column';

            const stream = document.createElement('div');
            stream.className = 'tf-preloader-stream';
            stream.style.animationDuration = `${randomBetween(1.05, 2.2).toFixed(2)}s`;
            stream.style.animationDelay = `${randomBetween(0, 1.2).toFixed(2)}s`;
            stream.style.left = `${randomBetween(-6, 6).toFixed(1)}%`;

            const charCount = Math.floor(randomBetween(14, 22));
            for (let i = 0; i < charCount; i += 1) {
                const char = document.createElement('span');
                char.className = 'tf-preloader-char';
                char.textContent = symbols[Math.floor(Math.random() * symbols.length)];
                char.style.opacity = String(Math.max(0.18, 1 - (i / charCount)));
                char.style.transform = `translateX(${randomBetween(-1.4, 1.4).toFixed(2)}px)`;
                stream.appendChild(char);
            }

            col.appendChild(stream);
            rainGrid.appendChild(col);
        }

        rain.appendChild(rainGrid);
        shell.appendChild(rain);
        if (!shell.parentNode) {
            document.body.appendChild(shell);
        }
        document.body.classList.add('tf-loading');
        preloader = shell;
        return shell;
    }

    function hidePreloader() {
        if (!preloader) return;
        preloader.classList.add('is-hidden');
        document.body.classList.remove('tf-loading');
        window.setTimeout(() => {
            if (preloader && preloader.parentNode) {
                preloader.parentNode.removeChild(preloader);
            }
            preloader = null;
        }, 340);
    }

    function setRevealVariant(el, index) {
        if (!el || el.dataset.animate) return;
        el.dataset.animate = 'fade-up';
    }

    function syncRevealTargets() {
        const targets = document.querySelectorAll(autoTargetsSelector);
        if (!targets.length) return [];
        targets.forEach((el, index) => {
            if (el.classList.contains('review-dialog')) {
                el.classList.add('tf-modal-panel');
            }
            if (!el.classList.contains('tf-motion-target')) {
                el.classList.add('tf-motion-target');
            }
            if (!el.classList.contains('tf-reveal') && !el.matches('[role="dialog"], [id$="Modal"], .modal, .tf-ai-modal, .review-dialog')) {
                el.classList.add('tf-reveal');
            }
            setRevealVariant(el, index);
            if (el.classList.contains('tf-reveal')) {
                el.style.setProperty('--reveal-delay', `${Math.min(600, index * 36)}ms`);
            }
        });
        return Array.from(targets);
    }

    function initRevealObserver(force = false) {
        const targets = syncRevealTargets();
        if (!targets.length) return;
        if (revealObserver && !force) return;
        if (revealObserver) {
            revealObserver.disconnect();
            revealObserver = null;
        }
        if (prefersReducedMotion || !('IntersectionObserver' in window)) {
            targets.forEach((el) => el.classList.add('is-visible'));
            return;
        }
        revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '0px 0px -8% 0px',
            threshold: 0.08
        });
        targets.forEach((el) => {
            if (!el.classList.contains('is-visible')) {
                revealObserver.observe(el);
            }
        });
    }

    function getModalPanel(modal) {
        if (!modal) return null;
        const explicit = modal.querySelector('[data-tf-modal-panel]');
        if (explicit) return explicit;
        const children = Array.from(modal.children).filter((node) => node && node.nodeType === 1);
        if (!children.length) return null;
        const candidate = children[children.length - 1];
        if (candidate) {
            candidate.classList.add('tf-modal-panel');
            candidate.setAttribute('data-tf-modal-panel', '1');
        }
        return candidate;
    }

    function isVisibleModal(modal) {
        if (!modal) return false;
        const style = window.getComputedStyle(modal);
        return style.display !== 'none' && style.visibility !== 'hidden' && Number(style.opacity || '1') > 0;
    }

    function syncModals() {
        const modals = Array.from(document.querySelectorAll('[role="dialog"], [id$="Modal"], .modal, .tf-ai-modal'));
        modals.forEach((modal) => {
            const visible = isVisibleModal(modal) || modal.classList.contains('active') || modal.classList.contains('open') || modal.classList.contains('is-open');
            const panel = getModalPanel(modal);
            if (panel) {
                panel.classList.add('tf-modal-panel');
                panel.classList.toggle('is-visible', visible);
                panel.classList.toggle('tf-modal-ready', visible);
            }
            modal.classList.toggle('tf-modal-open', visible);
        });
        document.body.classList.toggle('tf-modal-open', modals.some(isVisibleModal));
    }

    function initModalObserver() {
        if (modalObserver) return;
        modalObserver = new MutationObserver(() => {
            window.requestAnimationFrame(syncModals);
        });
        modalObserver.observe(document.body, {
            attributes: true,
            subtree: true,
            childList: true,
            attributeFilter: ['class', 'style', 'hidden', 'aria-hidden']
        });
        syncModals();
    }

    function swapAdminPagination(currentPager, nextPager, currentTable, nextTable) {
        if (currentTable && nextTable) {
            currentTable.replaceWith(nextTable);
        }
        if (currentPager) {
            if (nextPager) {
                currentPager.replaceWith(nextPager);
            } else {
                currentPager.remove();
            }
        }
        syncRevealTargets();
        initRevealObserver(true);
        syncModals();
    }

    function initAjaxPagination() {
        document.addEventListener('click', async (event) => {
            const link = event.target.closest('.admin-table-pagination a[href]');
            if (!link) return;
            const pager = link.closest('.admin-table-pagination');
            if (!pager || !pager.hasAttribute('data-server-pager')) return;
            const href = link.getAttribute('href');
            if (!href || href === '#') return;
            event.preventDefault();

            if (pager.dataset.loading === '1') return;
            pager.dataset.loading = '1';
            pager.classList.add('is-loading');

            try {
                const targetUrl = new URL(href, window.location.href);
                const response = await fetch(targetUrl.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const html = await response.text();
                const parsed = new DOMParser().parseFromString(html, 'text/html');
                const currentPagers = Array.from(document.querySelectorAll('.admin-table-pagination'));
                const currentTables = Array.from(document.querySelectorAll('table[data-server-paginated="1"]'));
                const nextPagers = Array.from(parsed.querySelectorAll('.admin-table-pagination'));
                const nextTables = Array.from(parsed.querySelectorAll('table[data-server-paginated="1"]'));
                const pagerIndex = currentPagers.indexOf(pager);
                const nextPager = nextPagers[pagerIndex] || null;
                const nextTable = nextTables[pagerIndex] || null;
                const currentTable = currentTables[pagerIndex] || null;

                if (!nextTable || !currentTable) {
                    window.location.href = targetUrl.toString();
                    return;
                }

                swapAdminPagination(pager, nextPager, currentTable, nextTable);
                window.history.pushState({ tfPager: true }, '', targetUrl.toString());
                window.scrollTo({
                    top: Math.max(0, nextTable.getBoundingClientRect().top + window.scrollY - 96),
                    behavior: prefersReducedMotion ? 'auto' : 'smooth'
                });
            } catch (error) {
                window.location.href = href;
            } finally {
                pager.dataset.loading = '0';
                pager.classList.remove('is-loading');
            }
        });

        window.addEventListener('popstate', () => {
            window.location.reload();
        });
    }

    function initPreloader() {
        if (prefersReducedMotion) return;
        createPreloader();
        if (document.readyState === 'complete') {
            hidePreloader();
        } else {
            window.addEventListener('load', () => {
                window.setTimeout(hidePreloader, 140);
            }, { once: true });
        }
    }

    function boot() {
        initPreloader();
        initRevealObserver(true);
        initModalObserver();
        initAjaxPagination();
        const refresh = () => {
            syncRevealTargets();
            syncModals();
            initRevealObserver(true);
        };
        window.setTimeout(refresh, 120);
        window.setTimeout(refresh, 560);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
