@extends('layouts.app')

@section('title', __('Roadmaps') . ' - CodeMaster')

@section('head')
<style>
    /* ============ ROADMAPS 3D HERO (same style as courses) ============ */
    .rm-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .rm-hero3d {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        overflow: hidden;
        padding: 110px clamp(20px,4vw,56px) 90px;
        isolation: isolate;
        perspective: 1600px;
    }
    .rm-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .rm-aurora {
        position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(139,92,246,.14) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(56,189,248,.10) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(236,72,153,.08) 0%, transparent 60%);
        animation: rmAurora 22s ease-in-out infinite alternate;
    }
    @@keyframes rmAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); }
    }
    .rm-grid3d {
        position: absolute; inset: -50%;
        background-image:
            linear-gradient(var(--border) 1px, transparent 1px),
            linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px;
        opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2);
        transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
    }
    .rm-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .rm-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: rmOrb1 16s ease-in-out infinite; }
    .rm-orb-2 { width: 460px; height: 460px; background: #8b5cf6; opacity: .10; bottom: -18%; right: -6%; animation: rmOrb2 20s ease-in-out infinite; }
    .rm-orb-3 { width: 260px; height: 260px; background: #38bdf8; opacity: .10; top: 55%; left: 42%; animation: rmOrb3 12s ease-in-out infinite; }
    @@keyframes rmOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes rmOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes rmOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }

    .rm-hero3d-inner {
        position: relative; z-index: 2;
        width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr;
        gap: clamp(32px,5vw,72px); align-items: center;
    }
    .rm-title {
        font-size: clamp(42px,6.2vw,84px); font-weight: 900; line-height: .95;
        letter-spacing: -3px; margin: 0 0 18px; color: var(--text);
    }
    .rm-title .grad {
        background: linear-gradient(120deg, var(--accent), #8b5cf6 45%, #38bdf8 80%);
        background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: rmGradShift 7s ease-in-out infinite;
        display: inline-block;
        filter: drop-shadow(0 8px 30px var(--accent-glow-strong));
    }
    @@keyframes rmGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    @@keyframes rmBlink { 0%,100%{opacity:1; transform:scale(1)} 50%{opacity:.4; transform:scale(1.6)} }
    .rm-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.75; max-width: 540px; margin-bottom: 26px; }
    .rm-sub b { color: var(--text); }

    .rm-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
    .rm-btn {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 14px 26px; border-radius: 16px; font-weight: 800; font-size: 14px;
        text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
        border: 1px solid transparent; cursor: pointer;
    }
    .rm-btn-ai {
        background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff;
        box-shadow: 0 10px 32px var(--accent-glow-strong);
    }
    .rm-btn-ai:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 16px 44px var(--accent-glow-strong); }
    .rm-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .rm-btn-ghost:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-3px); }
    .rm-stats3d { display: flex; gap: clamp(20px,3.5vw,44px); flex-wrap: wrap; }
    .rm-stat { position: relative; }
    .rm-stat + .rm-stat::before { content:''; position: absolute; left: -22px; top: 50%; transform: translateY(-50%); width: 1px; height: 38px; background: var(--border); }
    .rm-stat-val { font-size: clamp(26px,3vw,38px); font-weight: 900; line-height: 1; background: linear-gradient(135deg,var(--text),var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-variant-numeric: tabular-nums; }
    .rm-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px; margin-top: 6px; font-weight: 600; }

    /* --- 3D stage --- */
    .rm-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .rm-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .rm-ring-1 { width: 480px; height: 480px; animation: rmSpin 26s linear infinite; opacity: .7; }
    .rm-ring-2 { width: 590px; height: 590px; animation: rmSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes rmSpin { to { transform: rotate(360deg); } }
    @@keyframes rmSpinRev { to { transform: rotate(-360deg); } }
    .rm-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: var(--accent); box-shadow: 0 0 14px var(--accent); top: -5px; left: 50%; }
    .rm-code3d {
        position: relative; width: 100%; max-width: 520px;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid var(--border); border-radius: 24px; overflow: hidden;
        box-shadow: 0 40px 100px rgba(0,0,0,.35), 0 0 80px var(--accent-glow);
        transform-style: preserve-3d;
        transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear;
        z-index: 3;
    }
    .rm-code3d::after {
        content:''; position: absolute; inset: 0; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: rmSheen 6s ease-in-out infinite;
    }
    @@keyframes rmSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .rm-code-bar { display: flex; align-items: center; gap: 8px; padding: 15px 18px; border-bottom: 1px solid var(--border); background: rgba(0,0,0,.22); }
    .rm-dot { width: 11px; height: 11px; border-radius: 50%; }
    .rm-tabs { display: flex; gap: 6px; margin-left: 12px; font-family: var(--font-mono); font-size: 11px; }
    .rm-tab { padding: 5px 12px; border-radius: 8px; color: var(--text-muted); }
    .rm-tab.on { background: var(--accent-glow); color: var(--accent); font-weight: 700; }
    .rm-live { margin-left: auto; display: flex; align-items: center; gap: 7px; font-size: 11px; font-weight: 700; color: #22c55e; font-family: var(--font-mono); }
    .rm-live i { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: rmBlink 1.4s infinite; }
    .rm-code-body { padding: 22px 22px 26px; font-family: var(--font-mono); font-size: 12.5px; line-height: 2.05; min-height: 300px; transform: translateZ(40px); }
    .rm-line { display: flex; gap: 12px; white-space: nowrap; }
    .rm-ln { color: var(--text-muted); min-width: 22px; text-align: right; opacity: .5; font-size: 11px; user-select: none; }
    .k { color: #c678dd; } .f { color: #61afef; } .s { color: #98c379; } .c { color: #636d83; font-style: italic; } .v { color: #e06c75; } .o { color: #56b6c2; } .n { color: #d19a66; } .g { color: #22c55e; } .a { color: #f59e0b; }
    .rm-cursor { display: inline-block; width: 8px; height: 15px; background: var(--accent); vertical-align: -2px; animation: rmBlink 1s step-end infinite; border-radius: 2px; }
    .rm-float-chip {
        position: absolute; z-index: 4;
        display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent);
        backdrop-filter: blur(18px); border: 1px solid var(--border);
        border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3);
        font-size: 13px; transform-style: preserve-3d;
        animation: rmFloatY 4.5s ease-in-out infinite;
        will-change: transform;
    }
    @@keyframes rmFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .rm-fc-1 { top: 4%; right: -6px; animation-delay: 0s; }
    .rm-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .rm-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .rm-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
    .rm-fc-ico.g { background: rgba(34,197,94,.14); color: #22c55e; }
    .rm-fc-ico.p { background: rgba(139,92,246,.14); color: #8b5cf6; }
    .rm-fc-ico.a { background: rgba(245,158,11,.14); color: #f59e0b; }
    .rm-fc-txt b { display: block; font-size: 13px; color: var(--text); }
    .rm-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .rm-cube {
        position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px;
        display: flex; align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        transform-style: preserve-3d; animation: rmCubeFloat 6s ease-in-out infinite;
    }
    .rm-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,#61dafb,#007acc); animation-delay: 0s; transform: rotate(-10deg); }
    .rm-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#a29bfe,#6c5ce7); animation-delay: 1.5s; transform: rotate(8deg); }
    @@keyframes rmCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .rm-scroll-hint {
        position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px;
        color: var(--text-muted); font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase;
    }
    /* Пауза тяжёлых анимаций hero, когда он вне экрана */
    .rm-paused .rm-aurora, .rm-paused .rm-orb, .rm-paused .rm-ring,
    .rm-paused .rm-cube, .rm-paused .rm-float-chip { animation-play-state: paused !important; }
    .rm-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .rm-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px; height: 8px; border-radius: 4px; background: var(--accent); animation: rmWheel 1.8s ease-in-out infinite; }
    @@keyframes rmWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ STEPS: по 3 карточки в одном окне ============ */
    .rm-progress { position: sticky; top: 0; z-index: 60; height: 3px; background: var(--border); }
    .rm-progress > div { height: 100%; width: 0; background: linear-gradient(90deg,var(--accent),#8b5cf6,#38bdf8); box-shadow: 0 0 12px var(--accent-glow-strong); }
    .rm-dots { position: fixed; right: 22px; top: 50%; transform: translateY(-50%); z-index: 70; display: flex; flex-direction: column; gap: 12px; opacity: 0; pointer-events: none; transition: opacity .4s; }
    .rm-dots.show { opacity: 1; pointer-events: auto; }
    .rm-dot-btn { width: 9px; height: 9px; border-radius: 50%; background: var(--border-hover); border: none; cursor: pointer; transition: all .3s; padding: 0; position: relative; }
    .rm-dot-btn.on { background: var(--accent); transform: scale(1.6); box-shadow: 0 0 14px var(--accent-glow-strong); }
    .rm-steps-panels { position: relative; }
    .rm-step {
        position: sticky; top: 0; height: 100vh; height: 100svh;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; background: var(--bg);
        visibility: hidden;
    }
    .rm-step.on { visibility: visible; }
    .rm-step::before {
        content:''; position: absolute; width: 560px; height: 560px; border-radius: 50%;
        background: radial-gradient(circle, var(--step-c, var(--accent)) 0%, transparent 68%);
        opacity: 0; transition: opacity .8s; filter: blur(90px); pointer-events: none;
        top: 50%; left: 50%; transform: translate(-50%,-50%);
    }
    .rm-step.on::before { opacity: .10; }
    .rm-step-inner {
        position: relative; z-index: 2; width: 100%; max-width: 1200px; padding: 84px clamp(20px,4vw,48px) 40px;
        opacity: 0; transform: scale(.9) translateY(60px);
        transition: opacity .55s cubic-bezier(.16,1,.3,1), transform .55s cubic-bezier(.16,1,.3,1);
        will-change: opacity, transform;
    }
    .rm-step.on .rm-step-inner { opacity: 1; transform: none; }
    .rm-pack-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
    .rm-pack-num { font-family: var(--font-mono); font-size: 13px; letter-spacing: 3px; color: var(--accent); font-weight: 700; display: flex; align-items: center; gap: 12px; text-transform: uppercase; }
    .rm-pack-num::before { content:''; width: 38px; height: 2px; background: var(--accent); border-radius: 2px; display: inline-block; }
    .rm-pack-count { font-size: 12px; color: var(--text-muted); border: 1px solid var(--border); padding: 7px 14px; border-radius: 100px; background: var(--card); font-weight: 600; }
    .rm-trio { display: grid; grid-template-columns: repeat(3,1fr); gap: 22px; }
    .rm-card3d {
        background: var(--card); border: 1px solid var(--border); border-radius: 22px; overflow: hidden;
        text-decoration: none; color: inherit; display: flex; flex-direction: column;
        transform-style: preserve-3d; transition: transform .18s linear, border-color .3s, box-shadow .3s;
        box-shadow: 0 14px 44px rgba(0,0,0,.16); position: relative;
        opacity: 0; transform: translateY(46px) scale(.96);
    }
    .rm-step.on .rm-card3d { animation: rmCardIn .55s cubic-bezier(.16,1,.3,1) forwards; }
    .rm-step.on .rm-card3d:nth-child(2) { animation-delay: .08s; }
    .rm-step.on .rm-card3d:nth-child(3) { animation-delay: .16s; }
    @@keyframes rmCardIn { from { opacity: 0; transform: translateY(46px) scale(.96); } to { opacity: 1; transform: none; } }
    .rm-card3d:hover { border-color: var(--accent); box-shadow: 0 26px 70px rgba(0,0,0,.28), 0 0 50px var(--accent-glow); }
    .rm-cover { height: 168px; position: relative; overflow: hidden; display: flex; align-items: flex-end; }
    .rm-cover-bg { position: absolute; inset: 0; }
    .rm-cover-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px); background-size: 26px 26px; mask-image: radial-gradient(circle at 70% 30%, black, transparent 75%); }
    .rm-cover-ico { position: absolute; right: 20px; bottom: 6px; font-size: 74px; color: rgba(255,255,255,.22); filter: drop-shadow(0 8px 20px rgba(0,0,0,.3)); transform: translateZ(30px); }
    .rm-badge-diff { position: absolute; top: 14px; left: 14px; z-index: 3; padding: 6px 12px; font-size: 11px; font-weight: 800; border-radius: 10px; text-transform: uppercase; letter-spacing: .8px; backdrop-filter: blur(8px); }
    .rm-badge-hours { position: absolute; top: 14px; right: 14px; z-index: 3; padding: 6px 12px; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.25); backdrop-filter: blur(8px); color: #fff; font-size: 11px; font-weight: 800; border-radius: 10px; }
    .rm-cbody { padding: 20px 20px 18px; display: flex; flex-direction: column; flex: 1; transform: translateZ(18px); }
    .rm-ctitle { font-size: 16px; font-weight: 800; line-height: 1.35; margin: 0 0 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .rm-cdesc { font-size: 13px; color: var(--text-secondary); line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 14px; }
    .rm-cmeta { display: flex; align-items: center; gap: 14px; font-size: 12px; color: var(--text-muted); margin-bottom: 16px; margin-top: auto; flex-wrap: wrap; }
    .rm-cbtn { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; border-radius: 14px; border: 1.5px solid var(--border); font-size: 13px; font-weight: 800; color: var(--accent); transition: all .3s; }
    .rm-card3d:hover .rm-cbtn { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff; border-color: transparent; }

    /* --- legacy + features (обычные секции после паков) --- */
    .rm-tail { max-width: 1200px; margin: 0 auto; padding: 70px clamp(20px,4vw,48px) 90px; }
    .rm-legacy-title { font-size: 20px; font-weight: 800; color: var(--text); margin-bottom: 14px; }
    .rm-legacy-list { display: grid; grid-template-columns: repeat(auto-fill,minmax(240px,1fr)); gap: 10px; }
    .rm-legacy-link { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-radius: 12px; border: 1px solid var(--border); background: var(--card); text-decoration: none; font-size: 14px; font-weight: 600; color: var(--text); transition: all .2s; }
    .rm-legacy-link:hover { border-color: var(--accent); transform: translateX(3px); }
    .rm-legacy-link i { color: var(--accent); font-size: 16px; }
    .rm-features { display: grid; grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap: 16px; margin-top: 48px; }
    .rm-feature { background: var(--card); border: 1px solid var(--border); border-radius: 18px; padding: 26px; text-align: center; transition: all .3s; }
    .rm-feature:hover { border-color: var(--accent); transform: translateY(-4px); box-shadow: 0 18px 44px rgba(0,0,0,.16); }
    .rm-feature-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin: 0 auto 10px; background: var(--accent-glow); color: var(--accent); }
    .rm-feature-title { font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
    .rm-feature-desc { font-size: 13px; color: var(--text-muted); }
    .rm-empty { text-align: center; padding: 80px 20px 100px; color: var(--text-muted); }

    /* --- Journey map panel (вместо код-окна): чекпоинты маршрута --- */
    .rm-map-body { position: relative; padding: 20px 20px 22px; transform: translateZ(40px); }
    .rm-trail { position: absolute; left: 53px; top: 36px; bottom: 36px; width: 2px; border-radius: 2px;
        background: repeating-linear-gradient(to bottom, var(--border-hover) 0 6px, transparent 6px 12px); }
    .rm-trail-fill { position: absolute; top: 0; left: 0; width: 100%; border-radius: 2px;
        background: linear-gradient(to bottom, var(--accent), #8b5cf6);
        box-shadow: 0 0 10px var(--accent-glow-strong);
        animation: rmTrail 9s ease-in-out infinite; }
    @@keyframes rmTrail { 0%,100% { height: 12%; } 50% { height: 66%; } }
    .rm-traveler { position: absolute; left: 50%; width: 12px; height: 12px; margin-left: -6px; border-radius: 50%;
        background: #fff; border: 3px solid var(--accent); box-shadow: 0 0 16px var(--accent);
        animation: rmTravel 9s ease-in-out infinite; z-index: 2; }
    @@keyframes rmTravel { 0%,100% { top: 12%; } 50% { top: 66%; } }
    .rm-stop { position: relative; display: flex; align-items: center; gap: 14px; padding: 9px 12px; border-radius: 14px; border: 1px solid transparent; transition: all .3s; }
    .rm-stop.current { background: var(--accent-glow); border-color: var(--accent-glow-strong); }
    .rm-node { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 15px; flex-shrink: 0; background: var(--bg-secondary); border: 1px solid var(--border);
        color: var(--text-muted); position: relative; z-index: 1; }
    .rm-stop.done .rm-node { background: rgba(34,197,94,.14); border-color: rgba(34,197,94,.4); color: #4ade80; }
    .rm-stop.current .rm-node { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff; border-color: transparent;
        box-shadow: 0 0 22px var(--accent-glow-strong); animation: rmNodePulse 2s ease-in-out infinite; }
    @@keyframes rmNodePulse { 0%,100% { box-shadow: 0 0 12px var(--accent-glow-strong); } 50% { box-shadow: 0 0 30px var(--accent-glow-strong); } }
    .rm-stop.locked { opacity: .55; }
    .rm-stop-txt { flex: 1; min-width: 0; }
    .rm-stop-txt b { display: block; font-size: 13px; color: var(--text); }
    .rm-stop-txt span { font-size: 11px; color: var(--text-muted); font-family: var(--font-mono); }
    .rm-check { width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; flex-shrink: 0; }
    .rm-stop.done .rm-check { background: rgba(34,197,94,.15); color: #4ade80; }
    .rm-stop.current .rm-check { background: var(--accent-glow); color: var(--accent); font-family: var(--font-mono); font-size: 10px; font-weight: 800; }
    .rm-stop.locked .rm-check { color: var(--text-muted); }

    /* --- Шкала маршрута в каждом паке --- */
    .rm-rail { margin-bottom: 20px; }
    .rm-rail-line { position: relative; height: 2px; border-radius: 2px;
        background: repeating-linear-gradient(90deg, var(--border-hover) 0 7px, transparent 7px 14px); }
    .rm-rail-fill { position: absolute; top: 0; left: 0; height: 100%; border-radius: 2px;
        background: linear-gradient(90deg, var(--accent), #8b5cf6, #38bdf8);
        box-shadow: 0 0 10px var(--accent-glow-strong); transition: width .6s cubic-bezier(.16,1,.3,1); }
    .rm-rail-pin { position: absolute; top: 50%; width: 30px; height: 30px; border-radius: 50%;
        background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff; font-size: 12px;
        display: flex; align-items: center; justify-content: center;
        transform: translate(-50%,-50%); box-shadow: 0 0 18px var(--accent-glow-strong);
        border: 2px solid var(--bg); transition: left .6s cubic-bezier(.16,1,.3,1); }
    .rm-rail-ends { position: absolute; top: 50%; transform: translate(-50%,-50%); width: 20px; height: 20px; border-radius: 50%;
        background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text-muted); font-size: 9px;
        display: flex; align-items: center; justify-content: center; }
    .rm-rail-labels { display: flex; justify-content: space-between; margin-top: 12px;
        font-family: var(--font-mono); font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: var(--text-muted); }
    .rm-rail-labels b { color: var(--accent); }

    /* --- Контуры карты на фоне пака --- */
    .rm-step::after { content: ''; position: absolute; inset: 0; pointer-events: none;
        background: repeating-radial-gradient(circle at 88% 12%, transparent 0 34px, var(--border) 34px 35px);
        -webkit-mask-image: radial-gradient(circle at 88% 12%, black 0%, transparent 52%);
        mask-image: radial-gradient(circle at 88% 12%, black 0%, transparent 52%); }

    /* --- Дотсы-пинпоинты --- */
    .rm-dot-btn.on { animation: rmPinPulse 2s ease-in-out infinite; }
    @@keyframes rmPinPulse { 0%,100% { box-shadow: 0 0 10px var(--accent-glow-strong); } 50% { box-shadow: 0 0 24px var(--accent-glow-strong); } }

    /* --- Бейдж остановок на cover --- */
    .rm-badge-stops { position: absolute; left: 14px; bottom: 12px; z-index: 3; display: inline-flex; align-items: center;
        gap: 6px; padding: 6px 12px; background: rgba(0,0,0,.38); backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,.22); color: #fff; font-size: 11px; font-weight: 700; border-radius: 10px; }

    /* --- create modal --- */
    .rm-modal { position: fixed; inset: 0; background: rgba(0,0,0,.6); backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center; padding: 16px; z-index: 200; }
    .rm-modal.open { display: flex; }
    .rm-modal-box { background: var(--card); border: 1px solid var(--border); border-radius: 20px; padding: 28px; width: 100%; max-width: 440px; box-shadow: 0 25px 60px rgba(0,0,0,.3); animation: rmModalIn .25s cubic-bezier(.16,1,.3,1); }
    @@keyframes rmModalIn { from { opacity: 0; transform: scale(.94) translateY(12px); } to { opacity: 1; transform: none; } }
    .rm-modal-title { font-size: 20px; font-weight: 800; color: var(--text); margin-bottom: 6px; }
    .rm-modal-desc { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }
    .rm-modal-input { width: 100%; padding: 12px 16px; border-radius: 12px; font-size: 14px; background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text); outline: none; transition: border-color .2s, box-shadow .2s; box-sizing: border-box; }
    .rm-modal-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
    .rm-modal-actions { display: flex; gap: 10px; margin-top: 20px; }
    .rm-modal-btn { flex: 1; padding: 12px; border-radius: 12px; font-weight: 700; font-size: 14px; border: none; cursor: pointer; transition: all .2s; }
    .rm-modal-btn--primary { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff; }
    .rm-modal-btn--primary:hover { transform: translateY(-2px); }
    .rm-modal-btn--ghost { background: var(--bg-secondary); color: var(--text); border: 1px solid var(--border); }
    .rm-modal-btn--ghost:hover { border-color: var(--accent); }

    @@media (max-width: 1020px) {
        .rm-hero3d-inner { grid-template-columns: 1fr; }
        .rm-stage { height: 480px; }
        .rm-fc-3 { right: 0; } .rm-fc-1 { right: 0; } .rm-fc-2 { left: 0; }
        .rm-trio { grid-template-columns: 1fr; max-width: 460px; margin: 0 auto; max-height: 62vh; overflow: hidden; }
        .rm-step { align-items: flex-start; }
        .rm-step-inner { padding-top: 76px; }
        .rm-trio .rm-card3d:nth-child(n+2) { display: none; }
        .rm-dots { display: none; }
    }
</style>
@endsection

@section('content')
<div class="rm-page">
{{-- ================= HERO 3D ================= --}}
<section class="rm-hero3d" id="rmHero">
    <div class="rm-hero3d-bg">
        <div class="rm-aurora"></div>
        <div class="rm-grid3d" data-depth="18"></div>
        <div class="rm-orb rm-orb-1" data-depth="40"></div>
        <div class="rm-orb rm-orb-2" data-depth="-30"></div>
        <div class="rm-orb rm-orb-3" data-depth="60"></div>
    </div>

    <div class="rm-hero3d-inner">
        <div>
            <h1 class="rm-title">{!! __('Your<br><span class="grad">Route</span> to Mastery') !!}</h1>
            <p class="rm-sub">{!! __('Follow an <b>AI-built journey</b> from first lesson to job-ready. Scroll down — each screen is <b>one leg of the route</b> with 3 checkpoints.') !!}</p>

            <div class="rm-hero-actions">
                <button type="button" class="rm-btn rm-btn-ai" onclick="document.getElementById('rmCreateModal').classList.add('open')"><i class="fas fa-wand-magic-sparkles"></i>{{ __('Create AI Roadmap') }}</button>
                <a href="#rmSteps" class="rm-btn rm-btn-ghost" id="rmToPacks"><i class="fas fa-layer-group"></i>{{ __('View paths') }}</a>
            </div>

            <div class="rm-stats3d">
                <div class="rm-stat"><div class="rm-stat-val" data-count="{{ $roadmaps->count() + $legacyRoadmaps->count() }}">0</div><div class="rm-stat-label">{{ __('Paths') }}</div></div>
                <div class="rm-stat"><div class="rm-stat-val" data-count="{{ \App\Models\Course::where('ai_generated', true)->count() }}">0</div><div class="rm-stat-label">{{ __('Courses') }}</div></div>
                <div class="rm-stat"><div class="rm-stat-val" data-count="{{ \App\Models\CourseStep::count() }}">0</div><div class="rm-stat-label">{{ __('Steps') }}</div></div>
            </div>
        </div>

        <div class="rm-stage" id="rmStage">
            <div class="rm-ring rm-ring-1"><span class="rm-ring-dot"></span></div>
            <div class="rm-ring rm-ring-2"><span class="rm-ring-dot" style="background:#8b5cf6;box-shadow:0 0 14px #8b5cf6"></span></div>
            <div class="rm-cube rm-cube-1" data-depth="70"><i class="fas fa-map-signs"></i></div>
            <div class="rm-cube rm-cube-2" data-depth="-60"><i class="fas fa-compass"></i></div>

            <div class="rm-code3d" id="rmCode">
                <div class="rm-code-bar">
                    <span class="rm-dot" style="background:#ff5f57"></span>
                    <span class="rm-dot" style="background:#febc2e"></span>
                    <span class="rm-dot" style="background:#28c840"></span>
                    <div class="rm-tabs"><span class="rm-tab on">your_route</span><span class="rm-tab">checkpoints</span></div>
                    <span class="rm-live"><i></i>ON ROUTE</span>
                </div>
                <div class="rm-map-body">
                    <div class="rm-trail"><div class="rm-trail-fill"></div><span class="rm-traveler"></span></div>
                    <div class="rm-stop done">
                        <span class="rm-node"><i class="fas fa-flag"></i></span>
                        <span class="rm-stop-txt"><b>{{ __('Start • Basics') }}</b><span>12 lessons • done</span></span>
                        <span class="rm-check"><i class="fas fa-check"></i></span>
                    </div>
                    <div class="rm-stop done">
                        <span class="rm-node"><i class="fas fa-code"></i></span>
                        <span class="rm-stop-txt"><b>{{ __('Practice • Pet projects') }}</b><span>8 tasks • done</span></span>
                        <span class="rm-check"><i class="fas fa-check"></i></span>
                    </div>
                    <div class="rm-stop current">
                        <span class="rm-node"><i class="fas fa-location-dot"></i></span>
                        <span class="rm-stop-txt"><b>{{ __('You are here • Project') }}</b><span>review in progress</span></span>
                        <span class="rm-check">YOU</span>
                    </div>
                    <div class="rm-stop locked">
                        <span class="rm-node"><i class="fas fa-trophy"></i></span>
                        <span class="rm-stop-txt"><b>{{ __('Finish • Job ready') }}</b><span>exam + certificate</span></span>
                        <span class="rm-check"><i class="fas fa-lock"></i></span>
                    </div>
                </div>
            </div>

            <div class="rm-float-chip rm-fc-1" data-depth="50">
                <div class="rm-fc-ico g"><i class="fas fa-route"></i></div>
                <div class="rm-fc-txt"><b>{{ __('Checkpoint path') }}</b><span>{{ __('basics → job') }}</span></div>
            </div>
            <div class="rm-float-chip rm-fc-2" data-depth="-45">
                <div class="rm-fc-ico p"><i class="fas fa-robot"></i></div>
                <div class="rm-fc-txt"><b>{{ __('AI guide') }}</b><span>{{ __('lectures + tests + slides') }}</span></div>
            </div>
            <div class="rm-float-chip rm-fc-3" data-depth="35">
                <div class="rm-fc-ico a"><i class="fas fa-trophy"></i></div>
                <div class="rm-fc-txt"><b>{{ __('Finish') }}</b><span>{{ __('exam + certificate') }}</span></div>
            </div>
        </div>
    </div>

    <div class="rm-scroll-hint"><div class="rm-mouse"></div><span>{{ __('Scroll — next leg') }}</span></div>
</section>

{{-- ================= STEPS: по 3 карточки ================= --}}
@php
    $rmItems = $roadmaps->values();
    $rmChunks = $rmItems->chunk(3);
    $rmTotalPacks = max(1, $rmChunks->count());
    $rmGradients = [
        'frontend' => 'linear-gradient(135deg, #61dafb, #007acc)',
        'backend' => 'linear-gradient(135deg, #ff6b6b, #ee5a24)',
        'design' => 'linear-gradient(135deg, #fd79a8, #e84393)',
        'devops' => 'linear-gradient(135deg, #00cec9, #0984e3)',
        'other' => 'linear-gradient(135deg, #a29bfe, #6c5ce7)',
    ];
    $rmStepColors = ['var(--accent)', '#8b5cf6', '#38bdf8', '#22c55e', '#f59e0b', '#ec4899'];
    $rmDiffStyle = [
        'beginner' => 'background:rgba(34,197,94,.16);color:#4ade80;border:1px solid rgba(34,197,94,.3)',
        'intermediate' => 'background:rgba(234,179,8,.16);color:#facc15;border:1px solid rgba(234,179,8,.3)',
        'advanced' => 'background:rgba(239,68,68,.16);color:#f87171;border:1px solid rgba(239,68,68,.3)',
    ];
@endphp

<div class="rm-progress"><div id="rmProg"></div></div>
<div class="rm-dots" id="rmDots">
    @foreach($rmChunks as $di => $ch)
    <button class="rm-dot-btn" data-goto="{{ $di }}" aria-label="pack {{ $di+1 }}"></button>
    @endforeach
</div>

@if($rmItems->count())
<div class="rm-steps-panels" id="rmSteps" style="height:{{ $rmTotalPacks * 100 }}vh">
    @foreach($rmChunks as $pi => $pack)
    <div class="rm-step" data-step-index="{{ $pi }}" style="--step-c:{{ $rmStepColors[$pi % count($rmStepColors)] }};z-index:{{ $pi + 1 }}">
        <div class="rm-step-inner">
            <div class="rm-pack-row">
                <span class="rm-pack-num">Leg {{ str_pad($pi+1, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($rmTotalPacks, 2, '0', STR_PAD_LEFT) }}</span>
                <span class="rm-pack-count"><i class="fas fa-sitemap" style="margin-right:6px"></i>{{ $pack->count() }} {{ __('roadmaps in view') }}</span>
            </div>
            @php $rmPct = round(($pi + 1) / $rmTotalPacks * 100); @endphp
            <div class="rm-rail">
                <div class="rm-rail-line">
                    <div class="rm-rail-fill" style="width:{{ $rmPct }}%"></div>
                    <span class="rm-rail-ends" style="left:0"><i class="fas fa-flag"></i></span>
                    <span class="rm-rail-pin" style="left:{{ $rmPct }}%"><i class="fas fa-location-dot"></i></span>
                    <span class="rm-rail-ends" style="left:100%"><i class="fas fa-trophy"></i></span>
                </div>
                <div class="rm-rail-labels"><span>{{ __('Start') }}</span><span><b>{{ $rmPct }}%</b> • {{ __('of the journey') }}</span><span>{{ __('Job ready') }}</span></div>
            </div>
            <div class="rm-trio">
                @foreach($pack as $rm)
                @php
                    $rmGradient = $rmGradients[$rm->category] ?? $rmGradients['other'];
                    $rmDiff = $rmDiffStyle[$rm->difficulty] ?? $rmDiffStyle['beginner'];
                @endphp
                <a href="{{ route('roadmap.show', $rm->slug) }}" class="rm-card3d rm-tilt">
                    <div class="rm-cover">
                        <div class="rm-cover-bg" style="background:{{ $rmGradient }}"></div>
                        <div class="rm-cover-grid"></div>
                        <i class="fas fa-sitemap rm-cover-ico"></i>
                        <span class="rm-badge-diff" style="{{ $rmDiff }}">{{ __($rm->difficulty) }}</span>
                        @if($rm->estimated_hours)
                        <span class="rm-badge-hours">{{ $rm->estimated_hours }}h</span>
                        @endif
                        <span class="rm-badge-stops"><i class="fas fa-route"></i>{{ $rm->sections->count() }} {{ __('stops') }}</span>
                    </div>
                    <div class="rm-cbody">
                        <h3 class="rm-ctitle">{{ $rm->title }}</h3>
                        <p class="rm-cdesc">{{ Str::limit($rm->description, 120) }}</p>
                        <div class="rm-cmeta">
                            <span><i class="fas fa-book" style="margin-right:5px"></i>{{ $rm->courses_count ?? $rm->courses->count() }} {{ __('courses') }}</span>
                            <span><i class="fas fa-layer-group" style="margin-right:5px"></i>{{ $rm->sections->count() }} {{ __('sections') }}</span>
                            <span><i class="fas fa-user" style="margin-right:5px"></i>{{ $rm->students_count }}</span>
                        </div>
                        <span class="rm-cbtn">{{ __('Open Roadmap') }} <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="rm-empty">
    <i class="fas fa-sitemap" style="font-size:44px;margin-bottom:14px;display:block"></i>
    <p>{{ __('No roadmaps yet — create the first one') }}</p>
    <button type="button" class="rm-btn rm-btn-ai" style="margin-top:16px" onclick="document.getElementById('rmCreateModal').classList.add('open')"><i class="fas fa-plus"></i>{{ __('Create AI Roadmap') }}</button>
</div>
@endif

{{-- ================= LEGACY + FEATURES ================= --}}
<div class="rm-tail">
    @if($legacyRoadmaps->count())
    <h2 class="rm-legacy-title reveal-up">{{ __('Legacy Roadmaps') }}</h2>
    <div class="rm-legacy-list">
        @foreach($legacyRoadmaps as $title)
        <a href="{{ route('roadmap.show', $title) }}" class="rm-legacy-link reveal-up">
            <i class="fas fa-project-diagram"></i>
            {{ $title }}
        </a>
        @endforeach
    </div>
    @endif

    <div class="rm-features">
        <div class="rm-feature reveal-up">
            <div class="rm-feature-icon"><i class="fas fa-sitemap"></i></div>
            <div class="rm-feature-title">{{ __('Tree Visualization') }}</div>
            <div class="rm-feature-desc">{{ __('Hierarchical view of sections, courses, and steps') }}</div>
        </div>
        <div class="rm-feature reveal-up">
            <div class="rm-feature-icon"><i class="fas fa-book-open"></i></div>
            <div class="rm-feature-title">{{ __('Lecture + Practice') }}</div>
            <div class="rm-feature-desc">{{ __('AI-generated lectures with code examples and materials') }}</div>
        </div>
        <div class="rm-feature reveal-up">
            <div class="rm-feature-icon"><i class="fas fa-check-circle"></i></div>
            <div class="rm-feature-title">{{ __('Tests + Exams') }}</div>
            <div class="rm-feature-desc">{{ __('5 types of tests and final exams per step') }}</div>
        </div>
        <div class="rm-feature reveal-up">
            <div class="rm-feature-icon"><i class="fas fa-desktop"></i></div>
            <div class="rm-feature-title">{{ __('Slides') }}</div>
            <div class="rm-feature-desc">{{ __('AI-generated presentations for each topic') }}</div>
        </div>
    </div>
</div>

{{-- Create Modal --}}
<div id="rmCreateModal" class="rm-modal">
    <div class="rm-modal-box">
        <div class="rm-modal-title">{{ __('Create AI Roadmap') }}</div>
        <div class="rm-modal-desc">{{ __('Enter a topic and AI will generate a structured learning path with courses.') }}</div>
        <form method="POST" action="{{ route('roadmaps.store') }}">
            @csrf
            <input type="text" name="title" class="rm-modal-input" placeholder="{{ __('e.g. Fullstack Web Development, Mobile Apps...') }}" required autofocus>
            <div class="rm-modal-actions">
                <button type="button" class="rm-modal-btn rm-modal-btn--ghost" onclick="document.getElementById('rmCreateModal').classList.remove('open')">{{ __('Cancel') }}</button>
                <button type="submit" class="rm-modal-btn rm-modal-btn--primary"><i class="fas fa-magic mr-1"></i> {{ __('Generate') }}</button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    /* --- Hero mouse 3D parallax --- */
    var hero = document.getElementById('rmHero');
    var code = document.getElementById('rmCode');
    var layers = document.querySelectorAll('#rmHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0;
        var heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('rm-paused', !heroVisible);
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
    document.querySelectorAll('.rm-stat-val[data-count]').forEach(function(el) {
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

    /* --- Card 3D tilt --- */
    document.querySelectorAll('.rm-tilt').forEach(function(card) {
        card.addEventListener('mousemove', function(e) {
            var r = card.getBoundingClientRect();
            var px = (e.clientX - r.left) / r.width - 0.5;
            var py = (e.clientY - r.top) / r.height - 0.5;
            card.style.transform = 'perspective(900px) rotateY(' + (px * 12).toFixed(2) + 'deg) rotateX(' + (-py * 12).toFixed(2) + 'deg) translateY(-6px)';
        });
        card.addEventListener('mouseleave', function() { card.style.transform = ''; });
    });

    /* --- Steps: свободный нативный скролл, пак целиком через sticky (без перехвата) --- */
    var steps = document.querySelectorAll('.rm-step');
    var box = document.getElementById('rmSteps');
    var dots = document.getElementById('rmDots');
    var dotBtns = document.querySelectorAll('.rm-dot-btn');
    var prog = document.getElementById('rmProg');
    var last = -1;
    var cachedTop = null;
    var ticking = false;
    function boxTop() {
        if (cachedTop === null && box) cachedTop = box.getBoundingClientRect().top + window.scrollY;
        return cachedTop || 0;
    }
    function packTop(i) { return Math.round(boxTop() + i * window.innerHeight); }
    function currentIdx() {
        if (!box || !steps.length) return 0;
        var vh = window.innerHeight;
        var rel = window.scrollY - boxTop();
        return Math.min(steps.length - 1, Math.max(0, Math.round(rel / vh)));
    }
    function setActive(idx) {
        if (idx === last) return;
        last = idx;
        steps.forEach(function(p, i) { p.classList.toggle('on', i === idx); });
        dotBtns.forEach(function(d, i){ d.classList.toggle('on', i === idx); });
    }
    function goTo(idx) {
        if (!box) return;
        idx = Math.min(steps.length - 1, Math.max(0, idx));
        setActive(idx);
        window.scrollTo({ top: packTop(idx), behavior: 'smooth' });
    }
    function updateSteps() {
        ticking = false;
        if (!box || !steps.length) return;
        var vh = window.innerHeight;
        var top = boxTop();
        var y = window.scrollY;
        var total = Math.max(1, box.offsetHeight - vh);
        var sc = Math.max(0, Math.min(1, (y - top + vh * 0.5) / total));
        if (prog) prog.style.width = (sc * 100).toFixed(1) + '%';
        var inView = y + vh * 0.5 >= top && y <= top + box.offsetHeight;
        if (dots) dots.classList.toggle('show', inView);
        if (y + vh < top || y > top + box.offsetHeight) return;
        setActive(currentIdx());
    }
    function requestUpdate() {
        if (!ticking) { ticking = true; requestAnimationFrame(updateSteps); }
    }
    dotBtns.forEach(function(d) {
        d.addEventListener('click', function() { goTo(parseInt(d.dataset.goto, 10)); });
    });
    var toPacks = document.getElementById('rmToPacks');
    if (toPacks) toPacks.addEventListener('click', function(e) { e.preventDefault(); goTo(0); });
    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', function() { cachedTop = null; requestUpdate(); });
    updateSteps();
    setTimeout(updateSteps, 300);

    /* --- Create modal --- */
    var modal = document.getElementById('rmCreateModal');
    if (modal) {
        modal.addEventListener('click', function(e) { if (e.target === modal) modal.classList.remove('open'); });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') modal.classList.remove('open'); });
    }
})();
</script>
@endsection
