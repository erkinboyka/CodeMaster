@extends('layouts.app')
@section('title', __('Problems') . ' - CodeMaster')

@section('head')
<style>
    /* ============ PROBLEMS: CODE THEME + 3D HERO ============ */
    .pb-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .pb-hero3d {
        position: relative; min-height: 100vh; display: flex; align-items: center;
        overflow: hidden; padding: 110px clamp(20px,4vw,56px) 90px;
        isolation: isolate; perspective: 1600px;
    }
    .pb-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .pb-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(139,92,246,.14) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(56,189,248,.10) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(34,197,94,.08) 0%, transparent 60%);
        animation: pbAurora 22s ease-in-out infinite alternate; }
    @@keyframes pbAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); } }
    .pb-grid3d { position: absolute; inset: -50%;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px; opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2); transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%); }
    .pb-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .pb-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: pbOrb1 16s ease-in-out infinite; }
    .pb-orb-2 { width: 460px; height: 460px; background: #8b5cf6; opacity: .10; bottom: -18%; right: -6%; animation: pbOrb2 20s ease-in-out infinite; }
    .pb-orb-3 { width: 260px; height: 260px; background: #22c55e; opacity: .08; top: 55%; left: 42%; animation: pbOrb3 12s ease-in-out infinite; }
    @@keyframes pbOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes pbOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes pbOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }
    /* floating code symbols */
    .pb-sym { position: absolute; font-family: var(--font-mono); font-weight: 700; color: var(--accent);
        opacity: .16; user-select: none; animation: pbSymFloat 7s ease-in-out infinite; white-space: nowrap; }
    @@keyframes pbSymFloat { 0%,100% { margin-top: 0; rotate: -3deg; } 50% { margin-top: -18px; rotate: 3deg; } }

    .pb-hero3d-inner { position: relative; z-index: 2; width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px,5vw,72px); align-items: center; }
    .pb-title { font-size: clamp(42px,6.2vw,84px); font-weight: 900; line-height: .95; letter-spacing: -3px; margin: 0 0 18px; color: var(--text); }
    .pb-title .grad { background: linear-gradient(120deg, var(--accent), #8b5cf6 45%, #22c55e 85%); background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: pbGradShift 7s ease-in-out infinite; display: inline-block;
        filter: drop-shadow(0 8px 30px var(--accent-glow-strong)); }
    @@keyframes pbGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    @@keyframes pbBlink { 0%,100%{opacity:1} 50%{opacity:.35} }
    .pb-caret { display: inline-block; width: .55em; height: 1em; background: var(--accent); vertical-align: -2px;
        margin-left: 6px; border-radius: 2px; animation: pbBlink 1.1s step-end infinite; }
    .pb-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.75; max-width: 540px; margin-bottom: 26px; }
    .pb-sub b { color: var(--text); }
    .pb-sub code { font-family: var(--font-mono); font-size: .85em; background: var(--accent-glow);
        border: 1px solid var(--accent-glow-strong); color: var(--accent); padding: 1px 7px; border-radius: 7px; }

    .pb-search3d { position: relative; max-width: 520px; margin-bottom: 16px; }
    .pb-search3d input { width: 100%; box-sizing: border-box; padding: 17px 20px 17px 52px; border-radius: 18px;
        border: 1px solid var(--border); background: var(--card); color: var(--text); font-size: 14px; outline: none;
        box-shadow: 0 12px 40px rgba(0,0,0,.18), inset 0 1px 0 rgba(255,255,255,.06);
        transition: border-color .3s, box-shadow .3s; font-family: var(--font-mono); }
    .pb-search3d input:focus { border-color: var(--accent); box-shadow: 0 12px 40px rgba(0,0,0,.2), 0 0 0 4px var(--accent-glow); }
    .pb-search3d > i { position: absolute; left: 19px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
    .pb-search3d kbd { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-family: var(--font-mono);
        font-size: 11px; color: var(--text-muted); border: 1px solid var(--border); border-radius: 8px; padding: 4px 8px; background: var(--bg-secondary); }
    .pb-filters3d { display: flex; flex-wrap: wrap; gap: 8px; max-width: 560px; margin-bottom: 26px; font-family: var(--font-mono); }
    .pb-chip { padding: 9px 18px; border-radius: 10px; font-size: 12px; font-weight: 700; border: 1px solid var(--border);
        background: var(--card); color: var(--text-secondary); text-decoration: none; transition: all .25s; }
    .pb-chip:hover { transform: translateY(-2px); border-color: var(--accent); color: var(--accent); }
    .pb-chip.active[data-d="easy"] { background: rgba(34,197,94,.14); border-color: #22c55e; color: #4ade80; }
    .pb-chip.active[data-d="medium"] { background: rgba(234,179,8,.12); border-color: #eab308; color: #facc15; }
    .pb-chip.active[data-d="hard"] { background: rgba(239,68,68,.12); border-color: #ef4444; color: #f87171; }
    .pb-chip.active[data-d="all"] { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff; border-color: transparent; }

    .pb-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
    .pb-btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 26px; border-radius: 16px;
        font-weight: 800; font-size: 14px; text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
        border: 1px solid transparent; cursor: pointer; }
    .pb-btn-run { background: linear-gradient(135deg,#22c55e,#16a34a); color: #fff; box-shadow: 0 10px 32px rgba(34,197,94,.35); font-family: var(--font-mono); }
    .pb-btn-run:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 16px 44px rgba(34,197,94,.45); }
    .pb-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .pb-btn-ghost:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-3px); }
    .pb-stats3d { display: flex; gap: clamp(20px,3.5vw,44px); flex-wrap: wrap; }
    .pb-stat { position: relative; }
    .pb-stat + .pb-stat::before { content:''; position: absolute; left: -22px; top: 50%; transform: translateY(-50%); width: 1px; height: 38px; background: var(--border); }
    .pb-stat-val { font-size: clamp(26px,3vw,38px); font-weight: 900; line-height: 1; font-family: var(--font-mono);
        background: linear-gradient(135deg,var(--text),var(--accent)); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; font-variant-numeric: tabular-nums; }
    .pb-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px; margin-top: 6px; font-weight: 600; }

    /* --- 3D editor stage --- */
    .pb-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .pb-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .pb-ring-1 { width: 480px; height: 480px; animation: pbSpin 26s linear infinite; opacity: .7; }
    .pb-ring-2 { width: 590px; height: 590px; animation: pbSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes pbSpin { to { transform: rotate(360deg); } }
    @@keyframes pbSpinRev { to { transform: rotate(-360deg); } }
    .pb-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: #22c55e; box-shadow: 0 0 14px #22c55e; top: -5px; left: 50%; }
    .pb-code3d { position: relative; width: 100%; max-width: 520px;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid var(--border); border-radius: 24px; overflow: hidden;
        box-shadow: 0 40px 100px rgba(0,0,0,.35), 0 0 80px rgba(34,197,94,.12);
        transform-style: preserve-3d; transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear; z-index: 3; }
    .pb-code3d::after { content:''; position: absolute; inset: 0; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: pbSheen 6s ease-in-out infinite; }
    @@keyframes pbSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .pb-code-bar { display: flex; align-items: center; gap: 8px; padding: 15px 18px; border-bottom: 1px solid var(--border); background: rgba(0,0,0,.22); }
    .pb-dot { width: 11px; height: 11px; border-radius: 50%; }
    .pb-tabs { display: flex; gap: 6px; margin-left: 12px; font-family: var(--font-mono); font-size: 11px; }
    .pb-tab { padding: 5px 12px; border-radius: 8px; color: var(--text-muted); }
    .pb-tab.on { background: rgba(34,197,94,.12); color: #4ade80; font-weight: 700; }
    .pb-run-btn { margin-left: auto; display: inline-flex; align-items: center; gap: 7px; font-family: var(--font-mono);
        font-size: 11px; font-weight: 800; color: #fff; background: linear-gradient(135deg,#22c55e,#16a34a);
        border: none; border-radius: 9px; padding: 7px 14px; cursor: pointer; box-shadow: 0 4px 16px rgba(34,197,94,.35); }
    .pb-code-body { padding: 20px 22px 14px; font-family: var(--font-mono); font-size: 12.5px; line-height: 2; transform: translateZ(40px); }
    .pb-line { display: flex; gap: 12px; white-space: nowrap; }
    .pb-ln { color: var(--text-muted); min-width: 22px; text-align: right; opacity: .5; font-size: 11px; user-select: none; }
    .k { color: #c678dd; } .f { color: #61afef; } .s { color: #98c379; } .c { color: #636d83; font-style: italic; }
    .v { color: #e06c75; } .o { color: #56b6c2; } .n { color: #d19a66; } .t { color: #e5c07b; }
    .pb-cursor { display: inline-block; width: 8px; height: 15px; background: #22c55e; vertical-align: -2px; animation: pbBlink 1s step-end infinite; border-radius: 2px; }
    /* verdict console */
    .pb-verdict { margin: 6px 14px 16px; border: 1px solid rgba(34,197,94,.25); border-radius: 14px; overflow: hidden;
        background: rgba(34,197,94,.05); transform: translateZ(30px); }
    .pb-verdict-head { display: flex; align-items: center; gap: 8px; padding: 10px 14px; font-family: var(--font-mono);
        font-size: 11px; font-weight: 800; color: #4ade80; border-bottom: 1px solid rgba(34,197,94,.18); }
    .pb-verdict-head .pulse { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: pbBlink 1.4s infinite; }
    .pb-verdict-head .meta { margin-left: auto; color: var(--text-muted); font-weight: 600; }
    .pb-test { display: flex; align-items: center; gap: 10px; padding: 7px 14px; font-family: var(--font-mono); font-size: 11px;
        color: var(--text-secondary); opacity: 0; transform: translateX(-8px); animation: pbTestIn .4s ease forwards; }
    .pb-test:nth-child(2) { animation-delay: .4s; } .pb-test:nth-child(3) { animation-delay: .9s; }
    .pb-test:nth-child(4) { animation-delay: 1.4s; } .pb-test:nth-child(5) { animation-delay: 1.9s; }
    @@keyframes pbTestIn { to { opacity: 1; transform: none; } }
    .pb-test i { font-size: 10px; }
    .pb-test.ok i { color: #22c55e; }
    .pb-test .ms { margin-left: auto; color: var(--text-muted); }
    .pb-float-chip { position: absolute; z-index: 4; display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent); backdrop-filter: blur(18px);
        border: 1px solid var(--border); border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3); font-size: 13px; animation: pbFloatY 4.5s ease-in-out infinite; }
    @@keyframes pbFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .pb-fc-1 { top: 4%; right: -6px; } .pb-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .pb-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .pb-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; font-family: var(--font-mono); font-weight: 800; }
    .pb-fc-ico.g { background: rgba(34,197,94,.14); color: #22c55e; }
    .pb-fc-ico.p { background: rgba(139,92,246,.14); color: #8b5cf6; }
    .pb-fc-ico.a { background: rgba(245,158,11,.14); color: #f59e0b; }
    .pb-fc-txt b { display: block; font-size: 13px; color: var(--text); font-family: var(--font-mono); }
    .pb-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .pb-cube { position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px; display: flex;
        align-items: center; justify-content: center; font-size: 26px; font-weight: 800; font-family: var(--font-mono); color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3); animation: pbCubeFloat 6s ease-in-out infinite; }
    .pb-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,#22c55e,#15803d); }
    .pb-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#ef4444,#b91c1c); animation-delay: 1.5s; }
    @@keyframes pbCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .pb-scroll-hint { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);
        font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-family: var(--font-mono); }
    .pb-paused .pb-aurora, .pb-paused .pb-orb, .pb-paused .pb-ring, .pb-paused .pb-cube,
    .pb-paused .pb-float-chip, .pb-paused .pb-sym { animation-play-state: paused !important; }
    .pb-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .pb-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px;
        height: 8px; border-radius: 4px; background: #22c55e; animation: pbWheel 1.8s ease-in-out infinite; }
    @@keyframes pbWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ PROBLEM SET: code listing ============ */
    .pb-wrap { max-width: 1280px; margin: 0 auto; padding: 70px clamp(20px,4vw,48px) 90px; }
    .pb-layout { display: flex; gap: 22px; align-items: flex-start; }
    .pb-side { width: 288px; flex-shrink: 0; position: sticky; top: 96px; display: flex; flex-direction: column; gap: 14px; }
    .pb-panel { background: var(--card); border: 1px solid var(--border); border-radius: 18px; padding: 18px;
        box-shadow: 0 12px 34px rgba(0,0,0,.12); }
    .pb-panel-label { font-family: var(--font-mono); font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 12px; }
    .pb-panel-label .cm { color: #636d83; }
    .pb-ringbox { display: flex; align-items: center; gap: 14px; }
    .pb-progress-ring { position: relative; width: 64px; height: 64px; flex-shrink: 0; }
    .pb-progress-ring svg { transform: rotate(-90deg); }
    .pb-progress-ring .bg { fill: none; stroke: var(--border); stroke-width: 5; }
    .pb-progress-ring .fg { fill: none; stroke-width: 5; stroke-linecap: round; }
    .pb-progress-text { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 900; color: var(--text); font-family: var(--font-mono); }
    .pb-bignum { font-family: var(--font-mono); font-size: 22px; font-weight: 800; color: var(--text); line-height: 1; }
    .pb-bignum small { font-size: 12px; color: var(--text-muted); }
    .pb-statbar { display: flex; gap: 2px; height: 6px; border-radius: 3px; overflow: hidden; margin-top: 12px; font-family: var(--font-mono); }
    .pb-statbar div { height: 100%; }
    .pb-kv { display: flex; justify-content: space-between; align-items: center; font-size: 12px; padding: 3px 0; font-family: var(--font-mono); }
    .pb-filter-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 13px; border-radius: 10px;
        font-size: 11px; font-weight: 700; font-family: var(--font-mono); border: 1px solid var(--border);
        background: var(--bg-secondary); color: var(--text-muted); cursor: pointer; transition: all .2s;
        white-space: nowrap; text-decoration: none; }
    .pb-filter-btn:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-1px); }
    .pb-filter-btn.active[data-f="easy"] { background: rgba(34,197,94,.14); border-color: #22c55e; color: #4ade80; }
    .pb-filter-btn.active[data-f="medium"] { background: rgba(234,179,8,.12); border-color: #eab308; color: #facc15; }
    .pb-filter-btn.active[data-f="hard"] { background: rgba(239,68,68,.12); border-color: #ef4444; color: #f87171; }
    .pb-filter-btn.active[data-f="all"] { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff; border-color: transparent; }
    .pb-topic-link { display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; border-radius: 9px;
        font-size: 12px; font-weight: 600; font-family: var(--font-mono); color: var(--text-muted);
        transition: all .15s; cursor: pointer; text-decoration: none; border-left: 2px solid transparent; }
    .pb-topic-link:hover { background: var(--bg-secondary); color: var(--text); }
    .pb-topic-link.active { background: var(--accent-glow); color: var(--accent); border-left-color: var(--accent); }
    .pb-topic-link .cnt { font-size: 10px; opacity: .6; }
    .pb-clear { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 10px; border-radius: 10px;
        font-size: 11px; font-weight: 700; font-family: var(--font-mono); color: var(--text-muted); transition: all .15s; text-decoration: none; }
    .pb-clear:hover { background: var(--bg-secondary); color: var(--text); }

    .pb-main { flex: 1; min-width: 0; background: var(--card); border: 1px solid var(--border); border-radius: 20px;
        overflow: hidden; box-shadow: 0 18px 50px rgba(0,0,0,.14); }
    .pb-termbar { display: flex; align-items: center; gap: 8px; padding: 13px 18px; border-bottom: 1px solid var(--border);
        background: rgba(0,0,0,.22); }
    .pb-termbar .t { font-family: var(--font-mono); font-size: 11px; color: var(--text-muted); margin-left: 8px; }
    .pb-termbar .t b { color: #4ade80; }
    .pb-term-actions { margin-left: auto; display: flex; gap: 8px; align-items: center; }
    .pb-lucky { font-family: var(--font-mono); font-size: 11px; font-weight: 700; color: var(--accent);
        background: var(--accent-glow); border: 1px solid var(--accent-glow-strong); border-radius: 9px;
        padding: 7px 13px; cursor: pointer; transition: all .2s; }
    .pb-lucky:hover { box-shadow: 0 0 16px var(--accent-glow-strong); transform: translateY(-1px); }
    .pb-tablewrap { overflow-x: auto; }
    .pb-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .pb-table thead th { padding: 11px 16px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .1em;
        color: var(--text-muted); text-align: left; border-bottom: 1.5px solid var(--border);
        background: var(--bg-secondary); font-family: var(--font-mono); }
    .pb-row { transition: background .15s; cursor: pointer; position: relative;
        opacity: 0; transform: translateY(14px); }
    .pb-row.in { opacity: 1; transform: none; transition: opacity .45s ease, transform .45s cubic-bezier(.16,1,.3,1), background .15s; }
    .pb-row:hover { background: color-mix(in srgb, var(--accent) 4%, transparent); }
    .pb-row td { padding: 13px 16px; border-bottom: 1px solid color-mix(in srgb, var(--border) 55%, transparent); vertical-align: middle; }
    .pb-row td:first-child { position: relative; }
    .pb-row td:first-child::before { content: '>'; position: absolute; left: 4px; top: 50%; transform: translateY(-50%);
        font-family: var(--font-mono); font-weight: 800; color: var(--accent); opacity: 0; transition: opacity .15s; }
    .pb-row:hover td:first-child::before { opacity: 1; }
    .pb-row.lucky { background: color-mix(in srgb, var(--accent) 10%, transparent); }
    .pb-row.lucky td:first-child::before { opacity: 1; content: '▶'; }
    .pb-id { font-family: var(--font-mono); font-size: 12px; font-weight: 700; color: var(--text-muted); }
    .pb-status-icon { width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; }
    .pb-title-cell { font-size: 13.5px; font-weight: 700; color: var(--text); transition: color .15s; }
    .pb-row:hover .pb-title-cell { color: var(--accent); }
    .pb-diff { display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 7px;
        font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; font-family: var(--font-mono); }
    .pb-diff.easy { background: rgba(34,197,94,.1); color: #16a34a; border: 1px solid rgba(34,197,94,.25); }
    .pb-diff.medium { background: rgba(234,179,8,.1); color: #ca8a04; border: 1px solid rgba(234,179,8,.25); }
    .pb-diff.hard { background: rgba(239,68,68,.1); color: #dc2626; border: 1px solid rgba(239,68,68,.25); }
    .pb-acc { font-family: var(--font-mono); font-size: 12px; font-weight: 700; color: var(--text-secondary); }
    .pb-accbar { height: 4px; border-radius: 2px; background: var(--border); margin-top: 5px; overflow: hidden; min-width: 70px; }
    .pb-accbar div { height: 100%; border-radius: 2px; background: linear-gradient(90deg,var(--accent),#8b5cf6); }
    .pb-tag { display: inline-flex; padding: 3px 8px; border-radius: 6px; font-size: 10px; font-weight: 700;
        background: color-mix(in srgb, var(--accent) 8%, transparent); color: var(--accent);
        border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent); font-family: var(--font-mono); white-space: nowrap; }
    .pb-tag-more { display: inline-flex; padding: 3px 7px; border-radius: 6px; font-size: 10px; font-weight: 700;
        background: var(--bg-secondary); color: var(--text-muted); font-family: var(--font-mono); }
    .pb-empty { text-align: center; padding: 70px 20px; color: var(--text-muted); font-family: var(--font-mono); }
    .pb-empty i { font-size: 44px; margin-bottom: 14px; display: block; opacity: .3; }
    .pb-pag { padding: 16px 20px; border-top: 1px solid var(--border); }

    @@media (max-width: 1020px) {
        .pb-hero3d-inner { grid-template-columns: 1fr; }
        .pb-stage { height: 480px; }
        .pb-fc-3 { right: 0; } .pb-fc-1 { right: 0; } .pb-fc-2 { left: 0; }
        .pb-layout { flex-direction: column; }
        .pb-side { width: 100%; position: static; }
    }
</style>
@endsection

@section('content')
<div class="pb-page">
{{-- ================= HERO 3D ================= --}}
<section class="pb-hero3d" id="pbHero">
    <div class="pb-hero3d-bg">
        <div class="pb-aurora"></div>
        <div class="pb-grid3d" data-depth="18"></div>
        <div class="pb-orb pb-orb-1" data-depth="40"></div>
        <div class="pb-orb pb-orb-2" data-depth="-30"></div>
        <div class="pb-orb pb-orb-3" data-depth="60"></div>
        <span class="pb-sym" style="top:14%;left:6%;font-size:44px" data-depth="70">{ }</span>
        <span class="pb-sym" style="top:68%;left:3%;font-size:26px;animation-delay:1s" data-depth="-50">&lt;/&gt;</span>
        <span class="pb-sym" style="top:22%;right:5%;font-size:30px;animation-delay:2s" data-depth="55">;</span>
        <span class="pb-sym" style="top:74%;right:8%;font-size:20px;animation-delay:.5s" data-depth="-65">0101</span>
        <span class="pb-sym" style="top:44%;left:44%;font-size:22px;animation-delay:1.6s" data-depth="90">=&gt;</span>
        <span class="pb-sym" style="top:10%;left:48%;font-size:18px;animation-delay:2.4s" data-depth="-40">fn()</span>
    </div>

    <div class="pb-hero3d-inner">
        <div>
            <h1 class="pb-title">{!! __('Solve.<br><span class="grad">Compile.</span><br>Conquer.<span class="pb-caret"></span>') !!}</h1>
            <p class="pb-sub">{!! __('Train algorithms on <b>real tasks</b> — from <code>two-sum</code> to <code>dynamic programming</code>. Filter by difficulty, pick a topic, get <b>Accepted</b>.') !!}</p>

            <form action="{{ route('problems.index') }}" method="GET" class="pb-search3d">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('grep problems... e.g. array, dp, graph') }}" autocomplete="off">
                @if(request('difficulty'))<input type="hidden" name="difficulty" value="{{ request('difficulty') }}">@endif
                @if(request('topic'))<input type="hidden" name="topic" value="{{ request('topic') }}">@endif
                @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                <kbd>⌘K</kbd>
            </form>

            <div class="pb-filters3d">
                <a href="{{ request()->fullUrlWithQuery(['difficulty' => null]) }}" data-d="all" class="pb-chip {{ !request('difficulty') ? 'active' : '' }}">[all]</a>
                <a href="{{ request()->fullUrlWithQuery(['difficulty' => 'easy']) }}" data-d="easy" class="pb-chip {{ request('difficulty') === 'easy' ? 'active' : '' }}">[easy]</a>
                <a href="{{ request()->fullUrlWithQuery(['difficulty' => 'medium']) }}" data-d="medium" class="pb-chip {{ request('difficulty') === 'medium' ? 'active' : '' }}">[med]</a>
                <a href="{{ request()->fullUrlWithQuery(['difficulty' => 'hard']) }}" data-d="hard" class="pb-chip {{ request('difficulty') === 'hard' ? 'active' : '' }}">[hard]</a>
            </div>

            <div class="pb-hero-actions">
                <a href="#pbTable" class="pb-btn pb-btn-run" id="pbToTable"><i class="fas fa-play"></i>{{ __('./run_problems') }}</a>
                <a href="{{ route('daily-challenge') }}" class="pb-btn pb-btn-ghost"><i class="fas fa-calendar-day"></i>{{ __('Daily Challenge') }}</a>
            </div>

            <div class="pb-stats3d">
                <div class="pb-stat"><div class="pb-stat-val" data-count="{{ $stats['total'] }}">0</div><div class="pb-stat-label">{{ __('Problems') }}</div></div>
                <div class="pb-stat"><div class="pb-stat-val" data-count="{{ $stats['easy'] }}" style="background:linear-gradient(135deg,#4ade80,#22c55e);-webkit-background-clip:text;background-clip:text">0</div><div class="pb-stat-label">{{ __('Easy') }}</div></div>
                <div class="pb-stat"><div class="pb-stat-val" data-count="{{ $stats['medium'] }}" style="background:linear-gradient(135deg,#facc15,#eab308);-webkit-background-clip:text;background-clip:text">0</div><div class="pb-stat-label">{{ __('Medium') }}</div></div>
                <div class="pb-stat"><div class="pb-stat-val" data-count="{{ $stats['hard'] }}" style="background:linear-gradient(135deg,#f87171,#ef4444);-webkit-background-clip:text;background-clip:text">0</div><div class="pb-stat-label">{{ __('Hard') }}</div></div>
            </div>
        </div>

        <div class="pb-stage">
            <div class="pb-ring pb-ring-1"><span class="pb-ring-dot"></span></div>
            <div class="pb-ring pb-ring-2"><span class="pb-ring-dot" style="background:#8b5cf6;box-shadow:0 0 14px #8b5cf6"></span></div>
            <div class="pb-cube pb-cube-1" data-depth="70">{ }</div>
            <div class="pb-cube pb-cube-2" data-depth="-60">;</div>

            <div class="pb-code3d" id="pbCode">
                <div class="pb-code-bar">
                    <span class="pb-dot" style="background:#ff5f57"></span>
                    <span class="pb-dot" style="background:#febc2e"></span>
                    <span class="pb-dot" style="background:#28c840"></span>
                    <div class="pb-tabs"><span class="pb-tab on">two_sum.cpp</span><span class="pb-tab">tests</span></div>
                    <button type="button" class="pb-run-btn" onclick="pbReplay()"><i class="fas fa-play"></i>Run</button>
                </div>
                <div class="pb-code-body" id="pbEditor">
                    <div class="pb-line"><span class="pb-ln">1</span><span><span class="c">// O(n) — hash map, one pass</span></span></div>
                    <div class="pb-line"><span class="pb-ln">2</span><span><span class="k">vector</span><span class="o">&lt;</span><span class="t">int</span><span class="o">&gt;</span> <span class="f">twoSum</span><span class="o">(</span><span class="v">nums</span><span class="o">,</span> <span class="v">target</span><span class="o">)</span> <span class="o">{</span></span></div>
                    <div class="pb-line"><span class="pb-ln">3</span><span>&nbsp;&nbsp;<span class="t">unordered_map</span><span class="o">&lt;</span><span class="t">int</span><span class="o">,</span> <span class="t">int</span><span class="o">&gt;</span> <span class="v">seen</span><span class="o">;</span></span></div>
                    <div class="pb-line"><span class="pb-ln">4</span><span>&nbsp;&nbsp;<span class="k">for</span> <span class="o">(</span><span class="t">auto</span> <span class="o">[</span><span class="v">i</span><span class="o">,</span> <span class="v">x</span><span class="o">]</span> <span class="o">:</span> <span class="f">enumerate</span><span class="o">(</span><span class="v">nums</span><span class="o">))</span> <span class="o">{</span></span></div>
                    <div class="pb-line"><span class="pb-ln">5</span><span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="k">if</span> <span class="o">(</span><span class="v">seen</span><span class="o">.</span><span class="f">has</span><span class="o">(</span><span class="v">target</span> <span class="o">-</span> <span class="v">x</span><span class="o">))</span> <span class="k">return</span> <span class="o">{</span><span class="v">seen</span><span class="o">[</span><span class="v">target</span><span class="o">-</span><span class="v">x</span><span class="o">],</span> <span class="v">i</span><span class="o">};</span></span></div>
                    <div class="pb-line"><span class="pb-ln">6</span><span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="v">seen</span><span class="o">[</span><span class="v">x</span><span class="o">]</span> <span class="o">=</span> <span class="v">i</span><span class="o">;</span> <span class="pb-cursor"></span></span></div>
                    <div class="pb-line"><span class="pb-ln">7</span><span><span class="o">}</span></span></div>
                </div>
                <div class="pb-verdict" id="pbVerdict">
                    <div class="pb-verdict-head"><span class="pulse"></span>Accepted<span class="meta">124ms • 12.4mb</span></div>
                    <div class="pb-test ok"><i class="fas fa-check"></i>test_01 — basic<span class="ms">8ms</span></div>
                    <div class="pb-test ok"><i class="fas fa-check"></i>test_02 — edge_cases<span class="ms">21ms</span></div>
                    <div class="pb-test ok"><i class="fas fa-check"></i>test_03 — large_input<span class="ms">95ms</span></div>
                    <div class="pb-test ok"><i class="fas fa-check"></i>4 / 4 passed<span class="ms">+50 xp</span></div>
                </div>
            </div>

            <div class="pb-float-chip pb-fc-1" data-depth="50">
                <div class="pb-fc-ico g"><i class="fas fa-check"></i></div>
                <div class="pb-fc-txt"><b>Accepted</b><span>{{ __('124ms • 12.4mb') }}</span></div>
            </div>
            <div class="pb-float-chip pb-fc-2" data-depth="-45">
                <div class="pb-fc-ico p"><i class="fas fa-bolt"></i></div>
                <div class="pb-fc-txt"><b>+50 XP</b><span>{{ __('per solve') }}</span></div>
            </div>
            <div class="pb-float-chip pb-fc-3" data-depth="35">
                <div class="pb-fc-ico a"><i class="fas fa-fire"></i></div>
                <div class="pb-fc-txt"><b>{{ $stats['total'] }} {{ __('tasks') }}</b><span>{{ __('scroll the listing') }}</span></div>
            </div>
        </div>
    </div>

    <div class="pb-scroll-hint"><div class="pb-mouse"></div><span>$ cat problem_set</span></div>
</section>

{{-- ================= PROBLEM SET ================= --}}
<div class="pb-wrap" id="pbTable">
    <div class="pb-layout">
        <aside class="pb-side">
            @if(Auth::check())
            @php
                $solved = $stats['solved'] ?? 0;
                $total = max($stats['total'], 1);
                $pct = round(($solved / $total) * 100);
                $circ = 2 * pi() * 27;
                $offset = $circ - ($pct / 100) * $circ;
            @endphp
            <div class="pb-panel">
                <div class="pb-panel-label"><span class="cm">//</span> {{ __('your progress') }}</div>
                <div class="pb-ringbox">
                    <div class="pb-progress-ring">
                        <svg width="64" height="64" viewBox="0 0 64 64">
                            <circle class="bg" cx="32" cy="32" r="27"/>
                            <circle class="fg" cx="32" cy="32" r="27" stroke="url(#pbRingGrad)"
                                    stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $offset }}"/>
                            <defs><linearGradient id="pbRingGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#22c55e"/><stop offset="100%" stop-color="#8b5cf6"/>
                            </linearGradient></defs>
                        </svg>
                        <div class="pb-progress-text">{{ $pct }}%</div>
                    </div>
                    <div>
                        <div class="pb-bignum">{{ $solved }}<small> / {{ $stats['total'] }}</small></div>
                        <div style="font-size:10px;color:var(--text-muted);margin-top:4px;font-family:var(--font-mono)">{{ __('solved.cnt') }}</div>
                    </div>
                </div>
            </div>
            @endif

            <div class="pb-panel">
                <div class="pb-panel-label"><span class="cm">//</span> {{ __('statistics') }}</div>
                <div class="pb-kv"><span style="color:var(--text-muted)">total()</span><b>{{ $stats['total'] }}</b></div>
                <div class="pb-kv"><span style="color:#4ade80">[easy]</span><b style="color:#4ade80">{{ $stats['easy'] }}</b></div>
                <div class="pb-kv"><span style="color:#facc15">[med]</span><b style="color:#facc15">{{ $stats['medium'] }}</b></div>
                <div class="pb-kv"><span style="color:#f87171">[hard]</span><b style="color:#f87171">{{ $stats['hard'] }}</b></div>
                @php
                    $wE = $stats['total'] > 0 ? ($stats['easy'] / $stats['total']) * 100 : 0;
                    $wM = $stats['total'] > 0 ? ($stats['medium'] / $stats['total']) * 100 : 0;
                    $wH = $stats['total'] > 0 ? ($stats['hard'] / $stats['total']) * 100 : 0;
                @endphp
                <div class="pb-statbar">
                    <div style="width:{{ $wE }}%;background:#22c55e"></div>
                    <div style="width:{{ $wM }}%;background:#eab308"></div>
                    <div style="width:{{ $wH }}%;background:#ef4444"></div>
                </div>
            </div>

            <div class="pb-panel">
                <div class="pb-panel-label"><span class="cm">//</span> {{ __('difficulty') }}</div>
                <div class="flex flex-wrap gap-1.5" style="display:flex;flex-wrap:wrap;gap:6px">
                    <a href="{{ request()->fullUrlWithQuery(['difficulty' => null]) }}" data-f="all" class="pb-filter-btn {{ !request('difficulty') ? 'active' : '' }}">all</a>
                    <a href="{{ request()->fullUrlWithQuery(['difficulty' => 'easy']) }}" data-f="easy" class="pb-filter-btn {{ request('difficulty') === 'easy' ? 'active' : '' }}">easy</a>
                    <a href="{{ request()->fullUrlWithQuery(['difficulty' => 'medium']) }}" data-f="medium" class="pb-filter-btn {{ request('difficulty') === 'medium' ? 'active' : '' }}">med</a>
                    <a href="{{ request()->fullUrlWithQuery(['difficulty' => 'hard']) }}" data-f="hard" class="pb-filter-btn {{ request('difficulty') === 'hard' ? 'active' : '' }}">hard</a>
                </div>
            </div>

            @if(Auth::check())
            <div class="pb-panel">
                <div class="pb-panel-label"><span class="cm">//</span> {{ __('status') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px">
                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" data-f="all" class="pb-filter-btn {{ !request('status') ? 'active' : '' }}">all</a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'solved']) }}" data-f="easy" class="pb-filter-btn {{ request('status') === 'solved' ? 'active' : '' }}"><i class="fas fa-check"></i>solved</a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'attempted']) }}" data-f="medium" class="pb-filter-btn {{ request('status') === 'attempted' ? 'active' : '' }}"><i class="fas fa-pen"></i>trying</a>
                </div>
            </div>
            @endif

            <div class="pb-panel">
                <div class="pb-panel-label"><span class="cm">//</span> {{ __('topics') }}</div>
                <div style="max-height:224px;overflow-y:auto" class="space-y-0.5">
                    <a href="{{ request()->fullUrlWithQuery(['topic' => null]) }}" class="pb-topic-link {{ !request('topic') ? 'active' : '' }}">
                        <span>*.all</span>
                    </a>
                    @foreach($topics as $topic)
                    <a href="{{ request()->fullUrlWithQuery(['topic' => $topic->slug]) }}" class="pb-topic-link {{ request('topic') === $topic->slug ? 'active' : '' }}">
                        <span>{{ __('topic_' . $topic->slug) }}</span>
                        <span class="cnt">{{ $topic->problems_count }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('problems.index') }}" class="pb-clear"><i class="fas fa-times-circle"></i>{{ __('git checkout -- filters') }}</a>
        </aside>

        <div class="pb-main">
            <div class="pb-termbar">
                <span class="pb-dot" style="background:#ff5f57"></span>
                <span class="pb-dot" style="background:#febc2e"></span>
                <span class="pb-dot" style="background:#28c840"></span>
                <span class="t">codemaster — <b>{{ $problems->total() }} problems</b> — page {{ $problems->currentPage() }}/{{ $problems->lastPage() }}</span>
                <span class="pb-term-actions">
                    <button type="button" class="pb-lucky" id="pbLucky"><i class="fas fa-dice"></i> random()</button>
                </span>
            </div>

            <div class="pb-tablewrap">
                <table class="pb-table">
                    <thead>
                        <tr>
                            <th style="width:64px">id</th>
                            <th style="width:40px">st</th>
                            <th>title</th>
                            <th style="width:110px">diff</th>
                            <th style="width:110px" class="hidden sm:table-cell">accept</th>
                            <th class="hidden md:table-cell">tags</th>
                        </tr>
                    </thead>
                    <tbody id="pbRows">
                        @forelse($problems as $i => $problem)
                        @php
                            $isSolved = Auth::check() && $problem->isSolvedBy(Auth::user());
                            $isAttempted = Auth::check() && !$isSolved && $problem->isAttemptedBy(Auth::user());
                        @endphp
                        <tr class="pb-row" data-i="{{ $i }}" onclick="window.location='{{ route('problems.show', $problem->slug) }}'">
                            <td><span class="pb-id">{{ sprintf('%03d', $problem->id) }}</span></td>
                            <td>
                                <div class="pb-status-icon">
                                    @if($isSolved)
                                        <i class="fas fa-check-circle text-green-500 text-base"></i>
                                    @elseif($isAttempted)
                                        <i class="fas fa-minus-circle text-yellow-500 text-base"></i>
                                    @else
                                        <span style="width:18px;height:18px;display:block;border:2px solid var(--border);border-radius:50%"></span>
                                    @endif
                                </div>
                            </td>
                            <td><span class="pb-title-cell">{{ $problem->title }}</span></td>
                            <td><span class="pb-diff {{ $problem->difficulty }}">[{{ $problem->difficulty }}]</span></td>
                            <td class="hidden sm:table-cell">
                                <span class="pb-acc">{{ $problem->acceptance_rate }}%</span>
                                <div class="pb-accbar"><div style="width:{{ min(100, max(0, (float)$problem->acceptance_rate)) }}%"></div></div>
                            </td>
                            <td class="hidden md:table-cell">
                                <div style="display:flex;flex-wrap:wrap;gap:4px">
                                    @foreach($problem->topics->take(3) as $topic)
                                        <span class="pb-tag">&lt;{{ __('topic_' . $topic->slug) }}&gt;</span>
                                    @endforeach
                                    @if($problem->topics->count() > 3)
                                        <span class="pb-tag-more">+{{ $problem->topics->count() - 3 }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="pb-empty">
                                    <i class="fas fa-terminal"></i>
                                    <p>$ {{ __('no matches found') }} — {{ __('try another query') }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($problems->hasPages())
            <div class="pb-pag">{{ $problems->links() }}</div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    /* --- Hero mouse 3D parallax --- */
    var hero = document.getElementById('pbHero');
    var code = document.getElementById('pbCode');
    var layers = document.querySelectorAll('#pbHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0, heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('pb-paused', !heroVisible);
            }, { threshold: 0 }).observe(hero);
        }
        hero.addEventListener('mousemove', function(e) {
            var r = hero.getBoundingClientRect();
            var px = (e.clientX - r.left) / r.width - 0.5;
            var py = (e.clientY - r.top) / r.height - 0.5;
            tx = px; ty = py;
            layers.forEach(function(el) {
                var d = parseFloat(el.dataset.depth || 20);
                el.style.translate = (-px * d) + 'px ' + (-py * d) + 'px';
            });
        });
        hero.addEventListener('mouseleave', function() { tx = 0; ty = 0; layers.forEach(function(el){ el.style.translate = '0px 0px'; }); });
        (function tilt() {
            if (heroVisible) {
                rx += ((-ty * 10) - rx) * 0.08;
                ry += ((tx * 14) - ry) * 0.08;
                if (code) code.style.transform = 'rotateX(' + (6 + rx * -1).toFixed(2) + 'deg) rotateY(' + (-10 + ry).toFixed(2) + 'deg)';
            }
            requestAnimationFrame(tilt);
        })();
    }

    /* --- Animated counters --- */
    document.querySelectorAll('.pb-stat-val[data-count]').forEach(function(el) {
        var target = parseInt(el.dataset.count || 0, 10), t0 = null;
        function step(t) {
            if (!t0) t0 = t;
            var p = Math.min(1, (t - t0) / 1400);
            var e = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.round(target * e);
            if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    });

    /* --- Rows stagger reveal --- */
    var rows = document.querySelectorAll('.pb-row');
    if ('IntersectionObserver' in window && rows.length) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(en) {
                if (en.isIntersecting) {
                    var el = en.target;
                    el.style.transitionDelay = Math.min(parseInt(el.dataset.i || 0, 10) % 12 * 0.04, 0.48) + 's';
                    el.classList.add('in');
                    io.unobserve(el);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
        rows.forEach(function(r) { io.observe(r); });
        setTimeout(function() { rows.forEach(function(r) { r.classList.add('in'); }); }, 4000);
    } else {
        rows.forEach(function(r) { r.classList.add('in'); });
    }

    /* --- random() dice --- */
    var lucky = document.getElementById('pbLucky');
    if (lucky) lucky.addEventListener('click', function() {
        var list = document.querySelectorAll('#pbRows .pb-row');
        if (!list.length) return;
        list.forEach(function(r) { r.classList.remove('lucky'); });
        var pick = list[Math.floor(Math.random() * list.length)];
        pick.classList.add('lucky');
        pick.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    /* --- Scroll to table --- */
    var toTable = document.getElementById('pbToTable');
    if (toTable) toTable.addEventListener('click', function(e) {
        e.preventDefault();
        var t = document.getElementById('pbTable');
        if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    /* --- Cmd+K focuses search --- */
    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
            var inp = document.querySelector('.pb-search3d input');
            if (inp) { e.preventDefault(); inp.focus(); inp.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        }
    });
})();

/* Replay verdict animation via Run button */
function pbReplay() {
    var v = document.getElementById('pbVerdict');
    if (!v) return;
    var tests = v.querySelectorAll('.pb-test');
    tests.forEach(function(t) { t.style.animation = 'none'; });
    void v.offsetWidth;
    tests.forEach(function(t) { t.style.animation = ''; });
}
</script>
@endsection
