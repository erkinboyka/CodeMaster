<div id="preloader" style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:var(--bg);flex-direction:column">
    <!-- Terminal Window -->
    <div class="preloader-terminal">
        <div class="preloader-terminal-bar">
            <div class="preloader-dots">
                <span class="preloader-dot preloader-dot--red"></span>
                <span class="preloader-dot preloader-dot--yellow"></span>
                <span class="preloader-dot preloader-dot--green"></span>
            </div>
            <div class="preloader-terminal-title">codemaster@dev:~</div>
        </div>
        <div class="preloader-terminal-body">
            <div class="preloader-line" id="pl-1">
                <span class="preloader-prompt">$</span>
                <span class="preloader-cmd">npm install knowledge</span>
            </div>
            <div class="preloader-line" id="pl-2" style="opacity:0">
                <span class="preloader-prompt">$</span>
                <span class="preloader-cmd">compiling skills...</span>
            </div>
            <div class="preloader-line" id="pl-3" style="opacity:0">
                <span class="preloader-prompt">$</span>
                <span class="preloader-cmd">loading future developers</span>
            </div>
            <div class="preloader-line preloader-line--success" id="pl-4" style="opacity:0">
                <span class="preloader-prompt">$</span>
                <span class="preloader-cmd">ready_</span>
                <span class="preloader-check">&#10003;</span>
            </div>
        </div>
    </div>
    <!-- Progress Bar -->
    <div class="preloader-progress">
        <div class="preloader-progress-bar" id="preloader-bar"></div>
    </div>
    <!-- Logo -->
    <div class="preloader-logo">
        <i class="fas fa-terminal"></i>
        <span>CodeMaster</span>
    </div>
</div>
<style>
.preloader-terminal {
    width: 340px; border-radius: 12px; overflow: hidden;
    border: 1px solid var(--border); background: var(--bg-secondary);
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    margin-bottom: 24px;
}
.preloader-terminal-bar {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 14px; border-bottom: 1px solid var(--border);
    background: var(--bg);
}
.preloader-dots { display: flex; gap: 6px; }
.preloader-dot { width: 10px; height: 10px; border-radius: 50%; }
.preloader-dot--red { background: #ff5f57; }
.preloader-dot--yellow { background: #febc2e; }
.preloader-dot--green { background: #28c840; }
.preloader-terminal-title {
    font-family: 'JetBrains Mono', monospace; font-size: 12px;
    color: var(--text-muted);
}
.preloader-terminal-body {
    padding: 16px; font-family: 'JetBrains Mono', monospace;
    font-size: 13px; min-height: 100px;
}
.preloader-line {
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 8px; color: var(--text);
    transition: opacity 0.3s ease;
}
.preloader-prompt { color: var(--accent); font-weight: 700; }
.preloader-cmd { color: var(--text); }
.preloader-line--success { color: #22c55e; }
.preloader-check { font-weight: 700; margin-left: 4px; }
.preloader-progress {
    width: 200px; height: 3px; border-radius: 2px;
    background: var(--border); overflow: hidden;
    margin-bottom: 16px;
}
.preloader-progress-bar {
    height: 100%; background: var(--gradient); border-radius: 2px;
    width: 0%; transition: width 0.3s ease;
}
.preloader-logo {
    display: flex; align-items: center; gap: 8px;
    font-family: 'JetBrains Mono', monospace; font-size: 14px;
    color: var(--text-muted); opacity: 0.6;
}
.preloader-logo i { color: var(--accent); font-size: 12px; }
@@keyframes fadeOutPreloader {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(1.02); }
}
</style>
<script>
(function() {
    var bar = document.getElementById('preloader-bar');
    var el = document.getElementById('preloader');

    setTimeout(function() {
        var l2 = document.getElementById('pl-2');
        if (l2) l2.style.opacity = '1';
        if (bar) bar.style.width = '33%';
    }, 400);

    setTimeout(function() {
        var l3 = document.getElementById('pl-3');
        if (l3) l3.style.opacity = '1';
        if (bar) bar.style.width = '66%';
    }, 900);

    setTimeout(function() {
        var l4 = document.getElementById('pl-4');
        if (l4) l4.style.opacity = '1';
        if (bar) bar.style.width = '100%';
    }, 1300);

    setTimeout(function() {
        if (el) {
            el.style.animation = 'fadeOutPreloader 0.5s ease forwards';
            setTimeout(function() {
                if (el) el.style.display = 'none';
            }, 500);
        }
    }, 1800);
})();
</script>
