@extends('layouts.app')

@section('title', __('Courses') . ' - CodeMaster')

@section('head')
<style>
    /* ============ COURSES 3D HERO ============ */
    .cs-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .cs-hero3d {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        overflow: hidden;
        padding: 110px clamp(20px,4vw,56px) 90px;
        isolation: isolate;
        perspective: 1600px;
    }
    .cs-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .cs-aurora {
        position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(139,92,246,.14) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(56,189,248,.10) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(236,72,153,.08) 0%, transparent 60%);
        animation: csAurora 22s ease-in-out infinite alternate;
    }
    @@keyframes csAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); }
    }
    .cs-grid3d {
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
    .cs-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .cs-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: csOrb1 16s ease-in-out infinite; }
    .cs-orb-2 { width: 460px; height: 460px; background: #8b5cf6; opacity: .10; bottom: -18%; right: -6%; animation: csOrb2 20s ease-in-out infinite; }
    .cs-orb-3 { width: 260px; height: 260px; background: #38bdf8; opacity: .10; top: 55%; left: 42%; animation: csOrb3 12s ease-in-out infinite; }
    @@keyframes csOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes csOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes csOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }

    .cs-hero3d-inner {
        position: relative; z-index: 2;
        width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr;
        gap: clamp(32px,5vw,72px); align-items: center;
    }
    .cs-eyebrow {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 8px 18px; border-radius: 100px;
        background: var(--accent-glow); border: 1px solid var(--accent-glow-strong);
        font-size: 11px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase; color: var(--accent);
        margin-bottom: 22px;
    }
    .cs-eyebrow i { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); animation: csBlink 1.8s infinite; display: inline-block; }
    @@keyframes csBlink { 0%,100%{opacity:1; transform:scale(1)} 50%{opacity:.4; transform:scale(1.6)} }
    .cs-title {
        font-size: clamp(42px,6.2vw,84px); font-weight: 900; line-height: .95;
        letter-spacing: -3px; margin: 0 0 18px; color: var(--text);
    }
    .cs-title .grad {
        background: linear-gradient(120deg, var(--accent), #8b5cf6 45%, #38bdf8 80%);
        background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: csGradShift 7s ease-in-out infinite;
        display: inline-block;
        filter: drop-shadow(0 8px 30px var(--accent-glow-strong));
    }
    @@keyframes csGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    .cs-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.75; max-width: 540px; margin-bottom: 26px; }
    .cs-sub b { color: var(--text); }

    .cs-search3d { position: relative; max-width: 520px; margin-bottom: 16px; transform-style: preserve-3d; }
    .cs-search3d input {
        width: 100%; padding: 17px 20px 17px 52px; border-radius: 18px;
        border: 1px solid var(--border); background: var(--card);
        color: var(--text); font-size: 14px; outline: none;
        box-shadow: 0 12px 40px rgba(0,0,0,.18), inset 0 1px 0 rgba(255,255,255,.06);
        transition: border-color .3s, box-shadow .3s, transform .3s;
    }
    .cs-search3d input:focus { border-color: var(--accent); box-shadow: 0 12px 40px rgba(0,0,0,.2), 0 0 0 4px var(--accent-glow); transform: translateZ(8px); }
    .cs-search3d > i { position: absolute; left: 19px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
    .cs-search3d kbd {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        font-family: var(--font-mono); font-size: 11px; color: var(--text-muted);
        border: 1px solid var(--border); border-radius: 8px; padding: 4px 8px; background: var(--bg-secondary);
    }
    .cs-filters3d { display: flex; flex-wrap: wrap; gap: 8px; max-width: 560px; margin-bottom: 30px; }
    .cs-chip {
        padding: 9px 18px; border-radius: 100px; font-size: 13px; font-weight: 700;
        border: 1px solid var(--border); background: var(--card); color: var(--text-secondary);
        text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
    }
    .cs-chip:hover { transform: translateY(-2px); border-color: var(--accent); color: var(--accent); box-shadow: 0 8px 24px var(--accent-glow); }
    .cs-chip.active { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff; border-color: transparent; box-shadow: 0 8px 28px var(--accent-glow-strong); }
    .cs-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
    .cs-btn {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 14px 26px; border-radius: 16px; font-weight: 800; font-size: 14px;
        text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
        border: 1px solid transparent; cursor: pointer;
    }
    .cs-btn-ai {
        background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff;
        box-shadow: 0 10px 32px var(--accent-glow-strong);
    }
    .cs-btn-ai:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 16px 44px var(--accent-glow-strong); }
    .cs-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .cs-btn-ghost:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-3px); }
    .cs-stats3d { display: flex; gap: clamp(20px,3.5vw,44px); flex-wrap: wrap; }
    .cs-stat { position: relative; }
    .cs-stat + .cs-stat::before { content:''; position: absolute; left: -22px; top: 50%; transform: translateY(-50%); width: 1px; height: 38px; background: var(--border); }
    .cs-stat-val { font-size: clamp(26px,3vw,38px); font-weight: 900; line-height: 1; background: linear-gradient(135deg,var(--text),var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-variant-numeric: tabular-nums; }
    .cs-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px; margin-top: 6px; font-weight: 600; }

    /* --- 3D stage --- */
    .cs-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .cs-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .cs-ring-1 { width: 480px; height: 480px; animation: csSpin 26s linear infinite; opacity: .7; }
    .cs-ring-2 { width: 590px; height: 590px; animation: csSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes csSpin { to { transform: rotate(360deg); } }
    @@keyframes csSpinRev { to { transform: rotate(-360deg); } }
    .cs-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: var(--accent); box-shadow: 0 0 14px var(--accent); top: -5px; left: 50%; }
    .cs-code3d {
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
    .cs-code3d::after {
        content:''; position: absolute; inset: 0; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: csSheen 6s ease-in-out infinite;
    }
    @@keyframes csSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .cs-code-bar { display: flex; align-items: center; gap: 8px; padding: 15px 18px; border-bottom: 1px solid var(--border); background: rgba(0,0,0,.22); }
    .cs-dot { width: 11px; height: 11px; border-radius: 50%; }
    .cs-tabs { display: flex; gap: 6px; margin-left: 12px; font-family: var(--font-mono); font-size: 11px; }
    .cs-tab { padding: 5px 12px; border-radius: 8px; color: var(--text-muted); }
    .cs-tab.on { background: var(--accent-glow); color: var(--accent); font-weight: 700; }
    .cs-live { margin-left: auto; display: flex; align-items: center; gap: 7px; font-size: 11px; font-weight: 700; color: #22c55e; font-family: var(--font-mono); }
    .cs-live i { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: csBlink 1.4s infinite; }
    .cs-code-body { padding: 22px 22px 26px; font-family: var(--font-mono); font-size: 12.5px; line-height: 2.05; min-height: 300px; transform: translateZ(40px); }
    .cs-line { display: flex; gap: 12px; white-space: nowrap; }
    .cs-ln { color: var(--text-muted); min-width: 22px; text-align: right; opacity: .5; font-size: 11px; user-select: none; }
    .k { color: #c678dd; } .f { color: #61afef; } .s { color: #98c379; } .c { color: #636d83; font-style: italic; } .v { color: #e06c75; } .o { color: #56b6c2; } .n { color: #d19a66; }
    .cs-cursor { display: inline-block; width: 8px; height: 15px; background: var(--accent); vertical-align: -2px; animation: csBlink 1s step-end infinite; border-radius: 2px; }
    .cs-float-chip {
        position: absolute; z-index: 4;
        display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent);
        backdrop-filter: blur(18px); border: 1px solid var(--border);
        border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3);
        font-size: 13px; transform-style: preserve-3d;
        animation: csFloatY 4.5s ease-in-out infinite;
        will-change: transform;
    }
    @@keyframes csFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .cs-fc-1 { top: 4%; right: -6px; animation-delay: 0s; }
    .cs-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .cs-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .cs-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
    .cs-fc-ico.g { background: rgba(34,197,94,.14); color: #22c55e; }
    .cs-fc-ico.p { background: rgba(139,92,246,.14); color: #8b5cf6; }
    .cs-fc-ico.a { background: rgba(245,158,11,.14); color: #f59e0b; }
    .cs-fc-txt b { display: block; font-size: 13px; color: var(--text); }
    .cs-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .cs-cube {
        position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px;
        display: flex; align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        transform-style: preserve-3d; animation: csCubeFloat 6s ease-in-out infinite;
    }
    .cs-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,#3776ab,#ffd43b); animation-delay: 0s; transform: rotate(-10deg); }
    .cs-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#61dafb,#20232a); animation-delay: 1.5s; transform: rotate(8deg); }
    @@keyframes csCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .cs-scroll-hint {
        position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px;
        color: var(--text-muted); font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase;
    }
    /* Пауза тяжёлых анимаций hero, когда он вне экрана — убирает дёрганье при скролле паков */
    .cs-paused .cs-aurora, .cs-paused .cs-orb, .cs-paused .cs-ring,
    .cs-paused .cs-cube, .cs-paused .cs-float-chip { animation-play-state: paused !important; }
    .cs-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .cs-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px; height: 8px; border-radius: 4px; background: var(--accent); animation: csWheel 1.8s ease-in-out infinite; }
    @@keyframes csWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ STEPS: по 3 карточки в одном окне ============ */
    .cs-steps-head { text-align: center; padding: 90px 24px 10px; position: relative; z-index: 5; }
    .cs-steps-tag { display: inline-flex; align-items: center; gap: 8px; padding: 7px 18px; border-radius: 100px; background: var(--accent-glow); border: 1px solid var(--accent-glow-strong); font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; color: var(--accent); margin-bottom: 18px; }
    .cs-steps-title { font-size: clamp(30px,4.4vw,58px); font-weight: 900; letter-spacing: -2px; margin: 0 0 10px; }
    .cs-steps-sub { color: var(--text-muted); font-size: 15px; }
    .cs-progress { position: sticky; top: 0; z-index: 60; height: 3px; background: var(--border); }
    .cs-progress > div { height: 100%; width: 0; background: linear-gradient(90deg,var(--accent),#8b5cf6,#38bdf8); box-shadow: 0 0 12px var(--accent-glow-strong); }
    .cs-dots { position: fixed; right: 22px; top: 50%; transform: translateY(-50%); z-index: 70; display: flex; flex-direction: column; gap: 12px; opacity: 0; pointer-events: none; transition: opacity .4s; }
    .cs-dots.show { opacity: 1; pointer-events: auto; }
    .cs-dot-btn { width: 9px; height: 9px; border-radius: 50%; background: var(--border-hover); border: none; cursor: pointer; transition: all .3s; padding: 0; position: relative; }
    .cs-dot-btn.on { background: var(--accent); transform: scale(1.6); box-shadow: 0 0 14px var(--accent-glow-strong); }
    .cs-steps-panels { position: relative; }
    .cs-step {
        position: sticky; top: 0; height: 100vh; height: 100svh;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; background: var(--bg);
        visibility: hidden;
    }
    .cs-step.on { visibility: visible; }
    .cs-step::before {
        content:''; position: absolute; width: 560px; height: 560px; border-radius: 50%;
        background: radial-gradient(circle, var(--step-c, var(--accent)) 0%, transparent 68%);
        opacity: 0; transition: opacity .8s; filter: blur(90px); pointer-events: none;
        top: 50%; left: 50%; transform: translate(-50%,-50%);
    }
    .cs-step.on::before { opacity: .10; }
    .cs-step-inner {
        position: relative; z-index: 2; width: 100%; max-width: 1200px; padding: 84px clamp(20px,4vw,48px) 40px;
        opacity: 0; transform: scale(.9) translateY(60px);
        transition: opacity .55s cubic-bezier(.16,1,.3,1), transform .55s cubic-bezier(.16,1,.3,1);
        will-change: opacity, transform;
    }
    .cs-step.on .cs-step-inner { opacity: 1; transform: none; }
    .cs-pack-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
    .cs-pack-num { font-family: var(--font-mono); font-size: 13px; letter-spacing: 3px; color: var(--accent); font-weight: 700; display: flex; align-items: center; gap: 12px; text-transform: uppercase; }
    .cs-pack-num::before { content:''; width: 38px; height: 2px; background: var(--accent); border-radius: 2px; display: inline-block; }
    .cs-pack-count { font-size: 12px; color: var(--text-muted); border: 1px solid var(--border); padding: 7px 14px; border-radius: 100px; background: var(--card); font-weight: 600; }
    .cs-trio { display: grid; grid-template-columns: repeat(3,1fr); gap: 22px; }
    .cs-card3d {
        background: var(--card); border: 1px solid var(--border); border-radius: 22px; overflow: hidden;
        text-decoration: none; color: inherit; display: flex; flex-direction: column;
        transform-style: preserve-3d; transition: transform .18s linear, border-color .3s, box-shadow .3s;
        box-shadow: 0 14px 44px rgba(0,0,0,.16); position: relative;
        opacity: 0; transform: translateY(46px) scale(.96);
    }
    .cs-step.on .cs-card3d { animation: csCardIn .55s cubic-bezier(.16,1,.3,1) forwards; }
    .cs-step.on .cs-card3d:nth-child(2) { animation-delay: .08s; }
    .cs-step.on .cs-card3d:nth-child(3) { animation-delay: .16s; }
    @@keyframes csCardIn { from { opacity: 0; transform: translateY(46px) scale(.96); } to { opacity: 1; transform: none; } }
    .cs-card3d:hover { border-color: var(--accent); box-shadow: 0 26px 70px rgba(0,0,0,.28), 0 0 50px var(--accent-glow); }
    .cs-cover { height: 168px; position: relative; overflow: hidden; display: flex; align-items: flex-end; }
    .cs-cover-bg { position: absolute; inset: 0; }
    .cs-cover-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px); background-size: 26px 26px; mask-image: radial-gradient(circle at 70% 30%, black, transparent 75%); }
    .cs-cover-ico { position: absolute; right: 20px; bottom: 6px; font-size: 74px; color: rgba(255,255,255,.22); filter: drop-shadow(0 8px 20px rgba(0,0,0,.3)); transform: translateZ(30px); }
    .cs-cover-txt { font-size: 58px; font-weight: 900; color: rgba(255,255,255,.25); font-family: var(--font-mono); position: absolute; right: 22px; bottom: 8px; letter-spacing: -3px; }
    .cs-badge-cat { position: absolute; top: 14px; left: 14px; z-index: 3; padding: 6px 12px; background: rgba(255,255,255,.18); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,.25); color: #fff; font-size: 11px; font-weight: 800; border-radius: 10px; text-transform: uppercase; letter-spacing: .8px; }
    .cs-badge-lvl { position: absolute; top: 14px; right: 14px; z-index: 3; padding: 6px 12px; background: #fff; font-size: 11px; font-weight: 800; border-radius: 10px; box-shadow: 0 6px 18px rgba(0,0,0,.2); }
    .cs-cbody { padding: 20px 20px 18px; display: flex; flex-direction: column; flex: 1; transform: translateZ(18px); }
    .cs-ctitle { font-size: 16px; font-weight: 800; line-height: 1.35; margin: 0 0 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .cs-cauthor { font-size: 12px; color: var(--text-muted); margin-bottom: 8px; }
    .cs-cdesc { font-size: 13px; color: var(--text-secondary); line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 14px; }
    .cs-cmeta { display: flex; align-items: center; gap: 14px; font-size: 12px; color: var(--text-muted); margin-bottom: 16px; margin-top: auto; }
    .cs-cbtn { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; border-radius: 14px; border: 1.5px solid var(--border); font-size: 13px; font-weight: 800; color: var(--accent); transition: all .3s; }
    .cs-card3d:hover .cs-cbtn { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff; border-color: transparent; }
    .cs-pagination { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 70px 20px 90px; flex-wrap: wrap; }
    .cs-pg { min-width: 44px; height: 44px; padding: 0 14px; display: inline-flex; align-items: center; justify-content: center; border-radius: 14px; border: 1px solid var(--border); background: var(--card); color: var(--text-muted); font-weight: 700; font-size: 14px; text-decoration: none; transition: all .3s; }
    .cs-pg:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-2px); }
    .cs-pg.on { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff; border-color: transparent; box-shadow: 0 8px 26px var(--accent-glow-strong); }
    .cs-pg.dis { opacity: .35; pointer-events: none; }
    .cs-empty { text-align: center; padding: 80px 20px 100px; color: var(--text-muted); }

    @@media (max-width: 1020px) {
        .cs-hero3d-inner { grid-template-columns: 1fr; }
        .cs-stage { height: 480px; }
        .cs-fc-3 { right: 0; } .cs-fc-1 { right: 0; } .cs-fc-2 { left: 0; }
        .cs-trio { grid-template-columns: 1fr; max-width: 460px; margin: 0 auto; max-height: 62vh; overflow: hidden; }
        .cs-step { align-items: flex-start; }
        .cs-step-inner { padding-top: 76px; }
        .cs-trio .cs-card3d:nth-child(n+2) { display: none; }
        .cs-dots { display: none; }
    }
</style>
@endsection

@section('content')
<div class="cs-page">
{{-- ================= HERO 3D ================= --}}
<section class="cs-hero3d" id="csHero">
    <div class="cs-hero3d-bg">
        <div class="cs-aurora"></div>
        <div class="cs-grid3d" data-depth="18"></div>
        <div class="cs-orb cs-orb-1" data-depth="40"></div>
        <div class="cs-orb cs-orb-2" data-depth="-30"></div>
        <div class="cs-orb cs-orb-3" data-depth="60"></div>
    </div>

    <div class="cs-hero3d-inner">
        <div>
            <h1 class="cs-title">{!! __('Explore<br><span class="grad">Courses</span> in 3D') !!}</h1>
            <p class="cs-sub">{!! __('Master new skills with <b>expert-led courses</b> in programming, design and more. Scroll down — packs of <b>3 courses</b> open in one window.') !!}</p>

            <form action="{{ route('courses.index') }}" method="GET" class="cs-search3d">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search courses... e.g. Python, React, Design') }}" autocomplete="off">
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                <kbd>⌘K</kbd>
            </form>

            <div class="cs-filters3d">
                @foreach(['All' => '', 'Frontend' => 'frontend', 'Backend' => 'backend', 'Design' => 'design', 'DevOps' => 'devops', 'Other' => 'other'] as $label => $value)
                <a href="{{ route('courses.index', array_merge(request()->except('category', 'page'), $value ? ['category' => $value] : [])) }}" class="cs-chip {{ (request('category') === $value || (!$value && !request('category'))) ? 'active' : '' }}">{{ __($label) }}</a>
                @endforeach
            </div>

            <div class="cs-hero-actions">
                <a href="#csSteps" class="cs-btn cs-btn-ghost" id="csToPacks"><i class="fas fa-layer-group"></i>{{ __('View packs') }}</a>
            </div>

            <div class="cs-stats3d">
                <div class="cs-stat"><div class="cs-stat-val" data-count="{{ $courses->total() }}">0</div><div class="cs-stat-label">{{ __('Courses') }}</div></div>
                <div class="cs-stat"><div class="cs-stat-val" data-count="{{ \App\Models\Lesson::count() }}">0</div><div class="cs-stat-label">{{ __('Lessons') }}</div></div>
                <div class="cs-stat"><div class="cs-stat-val" data-count="{{ \App\Models\User::count() }}">0</div><div class="cs-stat-label">{{ __('Students') }}</div></div>
                <div class="cs-stat"><div class="cs-stat-val" data-count="{{ \App\Models\Certificate::count() }}">0</div><div class="cs-stat-label">{{ __('Certificates') }}</div></div>
            </div>
        </div>

        <div class="cs-stage" id="csStage">
            <div class="cs-ring cs-ring-1"><span class="cs-ring-dot"></span></div>
            <div class="cs-ring cs-ring-2"><span class="cs-ring-dot" style="background:#8b5cf6;box-shadow:0 0 14px #8b5cf6"></span></div>
            <div class="cs-cube cs-cube-1" data-depth="70"><i class="fab fa-python"></i></div>
            <div class="cs-cube cs-cube-2" data-depth="-60"><i class="fab fa-react"></i></div>

            <div class="cs-code3d" id="csCode">
                <div class="cs-code-bar">
                    <span class="cs-dot" style="background:#ff5f57"></span>
                    <span class="cs-dot" style="background:#febc2e"></span>
                    <span class="cs-dot" style="background:#28c840"></span>
                    <div class="cs-tabs"><span class="cs-tab on">course.py</span><span class="cs-tab">skills.json</span><span class="cs-tab">roadmap.ts</span></div>
                    <span class="cs-live"><i></i>LIVE</span>
                </div>
                <div class="cs-code-body" id="csType">
                    <div class="cs-line"><span class="cs-ln">1</span><span><span class="c"># pick your pack — 3 courses per scroll</span></span></div>
                    <div class="cs-line"><span class="cs-ln">2</span><span><span class="k">class</span> <span class="f">CoursePack</span><span class="o">(</span><span class="v">Student</span><span class="o">):</span></span></div>
                    <div class="cs-line"><span class="cs-ln">3</span><span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="k">def</span> <span class="f">start</span><span class="o">(</span><span class="v">self</span><span class="o">):</span></span></div>
                    <div class="cs-line"><span class="cs-ln">4</span><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="v">pack</span> <span class="o">=</span> <span class="f">load_next</span><span class="o">(</span><span class="n">3</span><span class="o">)</span></span></div>
                    <div class="cs-line"><span class="cs-ln">5</span><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="k">return</span> <span class="v">pack</span><span class="o">.</span><span class="f">unlock</span><span class="o">(</span><span class="s">"certificate"</span><span class="o">)</span> <span class="cs-cursor"></span></span></div>
                    <div class="cs-line"><span class="cs-ln">6</span><span><span class="o">>>></span> <span class="s">scroll ↓ to open pack 01</span></span></div>
                </div>
            </div>

            <div class="cs-float-chip cs-fc-1" data-depth="50">
                <div class="cs-fc-ico g"><i class="fas fa-check"></i></div>
                <div class="cs-fc-txt"><b>+120 XP</b><span>{{ __('per lesson') }}</span></div>
            </div>
            <div class="cs-float-chip cs-fc-2" data-depth="-45">
                <div class="cs-fc-ico p"><i class="fas fa-award"></i></div>
                <div class="cs-fc-txt"><b>{{ __('Certificate') }}</b><span>{{ __('after exam') }}</span></div>
            </div>
            <div class="cs-float-chip cs-fc-3" data-depth="35">
                <div class="cs-fc-ico a"><i class="fas fa-fire"></i></div>
                <div class="cs-fc-txt"><b>{{ $courses->total() }} {{ __('courses') }}</b><span>{{ __('scroll for packs') }}</span></div>
            </div>
        </div>
    </div>

    <div class="cs-scroll-hint"><div class="cs-mouse"></div><span>{{ __('Scroll — 3 courses') }}</span></div>
</section>

{{-- ================= STEPS: по 3 карточки ================= --}}
@php
    $items = collect($courses->items());
    $chunks = $items->chunk(3);
    $totalPacks = max(1, $chunks->count());
    $gradients = [
        'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
        'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
        'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
        'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
        'linear-gradient(135deg, #fc5c7d 0%, #6a82fb 100%)',
        'linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%)',
    ];
    $stepColors = ['var(--accent)', '#8b5cf6', '#38bdf8', '#22c55e', '#f59e0b', '#ec4899'];
@endphp

<div class="cs-progress"><div id="csProg"></div></div>
<div class="cs-dots" id="csDots">
    @foreach($chunks as $di => $ch)
    <button class="cs-dot-btn" data-goto="{{ $di }}" aria-label="pack {{ $di+1 }}"></button>
    @endforeach
</div>

@if($items->count())
<div class="cs-steps-panels" id="csSteps" style="height:{{ $totalPacks * 100 }}vh">
    @foreach($chunks as $pi => $pack)
    <div class="cs-step" data-step-index="{{ $pi }}" style="--step-c:{{ $stepColors[$pi % count($stepColors)] }};z-index:{{ $pi + 1 }}">
        <div class="cs-step-inner">
            <div class="cs-pack-row">
                <span class="cs-pack-num">Pack {{ str_pad($pi+1, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($totalPacks, 2, '0', STR_PAD_LEFT) }}</span>
                <span class="cs-pack-count"><i class="fas fa-book-open" style="margin-right:6px"></i>{{ $pack->count() }} {{ __('courses in view') }}</span>
            </div>
            <div class="cs-trio">
                @foreach($pack as $course)
                @php
                    $t = strtolower($course->title);
                    $catIcon = match(true) {
                        str_contains($t, 'html') => 'fab fa-html5',
                        str_contains($t, 'css') => 'fab fa-css3-alt',
                        str_contains($t, 'javascript') || str_contains($t, ' js') => 'fab fa-js',
                        str_contains($t, 'react') => 'fab fa-react',
                        str_contains($t, 'php') => 'fab fa-php',
                        str_contains($t, 'laravel') => 'fab fa-laravel',
                        str_contains($t, 'python') => 'fab fa-python',
                        str_contains($t, 'java') && !str_contains($t, 'javascript') => 'fab fa-java',
                        str_contains($t, 'c++') => 'cplusplus',
                        str_contains($t, 'c#') || str_contains($t, 'csharp') => 'csharp',
                        str_contains($t, 'github') => 'fab fa-github',
                        str_contains($t, 'git') => 'fab fa-git-alt',
                        str_contains($t, 'docker') => 'fab fa-docker',
                        str_contains($t, 'sql') || str_contains($t, 'mysql') || str_contains($t, 'postgres') => 'fas fa-database',
                        str_contains($t, 'node') => 'fab fa-node-js',
                        str_contains($t, 'typescript') => 'fab fa-js',
                        str_contains($t, 'design') || str_contains($t, 'ui') || str_contains($t, 'ux') || str_contains($t, 'figma') => 'fas fa-palette',
                        default => 'fas fa-code',
                    };
                    $gradient = $gradients[$course->id % count($gradients)];
                    $lvlColor = match(mb_strtolower($course->level)) {
                        'beginner', 'начальный' => '#22c55e',
                        'intermediate', 'средний' => '#f59e0b',
                        'advanced', 'продвинутый' => '#ef4444',
                        default => '#6366f1',
                    };
                @endphp
                <a href="{{ route('courses.show', $course->id) }}" class="cs-card3d cs-tilt">
                    <div class="cs-cover">
                        <div class="cs-cover-bg" style="background:{{ $gradient }}"></div>
                        <div class="cs-cover-grid"></div>
                        @if($catIcon === 'cplusplus')
                            <span class="cs-cover-txt">C++</span>
                        @elseif($catIcon === 'csharp')
                            <span class="cs-cover-txt">C#</span>
                        @else
                            <i class="{{ $catIcon }} cs-cover-ico"></i>
                        @endif
                        <span class="cs-badge-cat">{{ $course->category }}</span>
                        <span class="cs-badge-lvl" style="color:{{ $lvlColor }}">{{ __('courses_level_' . mb_strtolower($course->level)) }}</span>
                    </div>
                    <div class="cs-cbody">
                        <h3 class="cs-ctitle">{{ $course->title }}</h3>
                        <div class="cs-cauthor"><i class="fas fa-user-tie" style="margin-right:5px"></i>{{ $course->instructor }}</div>
                        <p class="cs-cdesc">{{ $course->description }}</p>
                        <div class="cs-cmeta">
                            <span><i class="fas fa-book-open" style="margin-right:5px"></i>{{ $course->lessons->count() }} {{ __('lessons') }}</span>
                            <span><i class="fas fa-signal" style="margin-right:5px"></i>{{ __('courses_level_' . mb_strtolower($course->level)) }}</span>
                        </div>
                        <span class="cs-cbtn">{{ __('View Course') }} <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="cs-pagination">
    @if($courses->onFirstPage())
    <span class="cs-pg dis"><i class="fas fa-chevron-left"></i></span>
    @else
    <a href="{{ $courses->previousPageUrl() }}" class="cs-pg"><i class="fas fa-chevron-left"></i></a>
    @endif
    @foreach($courses->getUrlRange(max(1, $courses->currentPage() - 2), min($courses->lastPage(), $courses->currentPage() + 2)) as $page => $url)
        @if($page == $courses->currentPage())
        <span class="cs-pg on">{{ $page }}</span>
        @else
        <a href="{{ $url }}" class="cs-pg">{{ $page }}</a>
        @endif
    @endforeach
    @if($courses->hasMorePages())
    <a href="{{ $courses->nextPageUrl() }}" class="cs-pg"><i class="fas fa-chevron-right"></i></a>
    @else
    <span class="cs-pg dis"><i class="fas fa-chevron-right"></i></span>
    @endif
</div>
@else
<div class="cs-empty">
    <i class="fas fa-book-open" style="font-size:44px;margin-bottom:14px;display:block"></i>
    <p>{{ __('No courses found') }}</p>
    <a href="{{ route('courses.index') }}" class="cs-chip" style="margin-top:14px;display:inline-block">{{ __('Reset filters') }}</a>
</div>
@endif
</div>
@endsection

@section('scripts')
<script>
(function() {
    /* --- Hero mouse 3D parallax --- */
    var hero = document.getElementById('csHero');
    var code = document.getElementById('csCode');
    var layers = document.querySelectorAll('#csHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0;
        var heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('cs-paused', !heroVisible);
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
    document.querySelectorAll('.cs-stat-val[data-count]').forEach(function(el) {
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
    document.querySelectorAll('.cs-tilt').forEach(function(card) {
        card.addEventListener('mousemove', function(e) {
            var r = card.getBoundingClientRect();
            var px = (e.clientX - r.left) / r.width - 0.5;
            var py = (e.clientY - r.top) / r.height - 0.5;
            card.style.transform = 'perspective(900px) rotateY(' + (px * 12).toFixed(2) + 'deg) rotateX(' + (-py * 12).toFixed(2) + 'deg) translateY(-6px)';
        });
        card.addEventListener('mouseleave', function() { card.style.transform = ''; });
    });

    /* --- Steps: свободный нативный скролл, пак целиком через sticky (без перехвата) --- */
    var steps = document.querySelectorAll('.cs-step');
    var box = document.getElementById('csSteps');
    var dots = document.getElementById('csDots');
    var dotBtns = document.querySelectorAll('.cs-dot-btn');
    var prog = document.getElementById('csProg');
    var packNow = document.getElementById('csPackNow');
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
        // nearest pack — активен только когда виден >50%, целиком
        return Math.min(steps.length - 1, Math.max(0, Math.round(rel / vh)));
    }
    function setActive(idx) {
        if (idx === last) return;
        last = idx;
        steps.forEach(function(p, i) { p.classList.toggle('on', i === idx); });
        dotBtns.forEach(function(d, i){ d.classList.toggle('on', i === idx); });
        if (packNow) packNow.textContent = String(idx + 1).padStart(2, '0');
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
    var toPacks = document.getElementById('csToPacks');
    if (toPacks) toPacks.addEventListener('click', function(e) { e.preventDefault(); goTo(0); });
    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', function() { cachedTop = null; requestUpdate(); });
    updateSteps();
    setTimeout(updateSteps, 300);

    /* --- Cmd+K focuses search --- */
    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
            var inp = document.querySelector('.cs-search3d input');
            if (inp) { e.preventDefault(); inp.focus(); inp.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        }
    });
})();
</script>
@endsection
