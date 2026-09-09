@extends('layouts.app')

@section('title', __('Ratings') . ' - CodeMaster')

@section('head')
<style>
    /* ============ RATINGS: HALL OF FAME + 3D HERO ============ */
    .rt-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .rt-hero3d { position: relative; min-height: 100vh; display: flex; align-items: center;
        overflow: hidden; padding: 110px clamp(20px,4vw,56px) 90px; isolation: isolate; perspective: 1600px; }
    .rt-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .rt-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(255,215,0,.10) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(139,92,246,.12) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(234,179,8,.10) 0%, transparent 60%);
        animation: rtAurora 22s ease-in-out infinite alternate; }
    @@keyframes rtAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); } }
    .rt-grid3d { position: absolute; inset: -50%;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px; opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2); transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%); }
    .rt-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .rt-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: rtOrb1 16s ease-in-out infinite; }
    .rt-orb-2 { width: 460px; height: 460px; background: #eab308; opacity: .10; bottom: -18%; right: -6%; animation: rtOrb2 20s ease-in-out infinite; }
    .rt-orb-3 { width: 260px; height: 260px; background: #8b5cf6; opacity: .10; top: 55%; left: 42%; animation: rtOrb3 12s ease-in-out infinite; }
    @@keyframes rtOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes rtOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes rtOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }
    @@keyframes rtBlink { 0%,100%{opacity:1} 50%{opacity:.35} }

    .rt-hero3d-inner { position: relative; z-index: 2; width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px,5vw,72px); align-items: center; }
    .rt-eyebrow { display: inline-flex; align-items: center; gap: 10px; padding: 8px 18px; border-radius: 100px;
        background: rgba(234,179,8,.1); border: 1px solid rgba(234,179,8,.3);
        font-size: 11px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase; color: #facc15; margin-bottom: 22px; }
    .rt-eyebrow i { color: #facc15; }
    .rt-title { font-size: clamp(42px,6.2vw,84px); font-weight: 900; line-height: .95; letter-spacing: -3px;
        margin: 0 0 18px; color: var(--text); }
    .rt-title .grad { background: linear-gradient(120deg, #FFD700, #f59e0b 40%, var(--accent) 80%); background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: rtGradShift 7s ease-in-out infinite; display: inline-block;
        filter: drop-shadow(0 8px 30px rgba(234,179,8,.25)); }
    @@keyframes rtGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    .rt-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.75; max-width: 540px; margin-bottom: 24px; }
    .rt-sub b { color: var(--text); }

    .rt-search3d { position: relative; max-width: 520px; margin-bottom: 14px; }
    .rt-search3d input { width: 100%; box-sizing: border-box; padding: 16px 20px 16px 52px; border-radius: 18px;
        border: 1px solid var(--border); background: var(--card); color: var(--text); font-size: 14px; outline: none;
        box-shadow: 0 12px 40px rgba(0,0,0,.18), inset 0 1px 0 rgba(255,255,255,.06);
        transition: border-color .3s, box-shadow .3s; }
    .rt-search3d input:focus { border-color: #eab308; box-shadow: 0 12px 40px rgba(0,0,0,.2), 0 0 0 4px rgba(234,179,8,.15); }
    .rt-search3d > i { position: absolute; left: 19px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
    .rt-search3d button { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); border: none; cursor: pointer;
        padding: 9px 18px; border-radius: 12px; font-size: 13px; font-weight: 800; color: #fff;
        background: linear-gradient(135deg,#eab308,#f97316); box-shadow: 0 4px 16px rgba(234,179,8,.35); transition: all .2s; }
    .rt-search3d button:hover { transform: translateY(-50%) scale(1.04); }
    .rt-tabs3d { display: flex; flex-wrap: wrap; gap: 8px; max-width: 560px; margin-bottom: 26px; }
    .rt-chip { display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 100px;
        font-size: 13px; font-weight: 700; border: 1px solid var(--border); background: var(--card);
        color: var(--text-secondary); text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1); }
    .rt-chip:hover { transform: translateY(-2px); border-color: #eab308; color: #eab308; }
    .rt-chip.active { background: linear-gradient(135deg,#eab308,#f97316); color: #fff; border-color: transparent;
        box-shadow: 0 8px 28px rgba(234,179,8,.35); }

    .rt-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 26px; }
    .rt-btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 26px; border-radius: 16px;
        font-weight: 800; font-size: 14px; text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
        border: 1px solid transparent; cursor: pointer; }
    .rt-btn-me { background: linear-gradient(135deg,#eab308,#f97316); color: #fff; box-shadow: 0 10px 32px rgba(234,179,8,.35); }
    .rt-btn-me:hover { transform: translateY(-3px) scale(1.02); }
    .rt-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .rt-btn-ghost:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-3px); }
    .rt-stats3d { display: flex; gap: clamp(20px,3.5vw,44px); flex-wrap: wrap; }
    .rt-stat { position: relative; }
    .rt-stat + .rt-stat::before { content:''; position: absolute; left: -22px; top: 50%; transform: translateY(-50%);
        width: 1px; height: 38px; background: var(--border); }
    .rt-stat-val { font-size: clamp(26px,3vw,38px); font-weight: 900; line-height: 1; font-variant-numeric: tabular-nums;
        background: linear-gradient(135deg,var(--text),#eab308); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; }
    .rt-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px;
        margin-top: 6px; font-weight: 600; }
    .rt-me-card { display: flex; align-items: center; gap: 14px; margin-top: 24px; padding: 14px 18px; border-radius: 18px;
        background: color-mix(in srgb, var(--card) 85%, transparent); backdrop-filter: blur(16px);
        border: 1px solid rgba(234,179,8,.3); max-width: 520px; box-shadow: 0 12px 34px rgba(0,0,0,.16); }
    .rt-me-card img { width: 48px; height: 48px; border-radius: 14px; object-fit: cover; border: 2px solid rgba(234,179,8,.5); }
    .rt-me-lvl { font-size: 14px; font-weight: 800; color: var(--text); }
    .rt-me-xp { font-size: 12px; color: var(--text-muted); font-family: var(--font-mono); margin-top: 2px; }
    .rt-me-bar { width: 130px; height: 6px; border-radius: 3px; background: var(--border); margin-top: 6px; overflow: hidden; }
    .rt-me-bar div { height: 100%; border-radius: 3px; background: linear-gradient(90deg,#eab308,#f97316);
        box-shadow: 0 0 8px rgba(234,179,8,.5); }

    /* --- 3D podium stage --- */
    .rt-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .rt-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .rt-ring-1 { width: 480px; height: 480px; animation: rtSpin 26s linear infinite; opacity: .7; }
    .rt-ring-2 { width: 590px; height: 590px; animation: rtSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes rtSpin { to { transform: rotate(360deg); } }
    @@keyframes rtSpinRev { to { transform: rotate(-360deg); } }
    .rt-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: #eab308;
        box-shadow: 0 0 14px #eab308; top: -5px; left: 50%; }
    .rt-podium3d { position: relative; width: 100%; max-width: 520px; padding: 30px 26px 26px;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid var(--border); border-radius: 24px;
        box-shadow: 0 40px 100px rgba(0,0,0,.35), 0 0 80px rgba(234,179,8,.1);
        transform-style: preserve-3d; transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear; z-index: 3; transform-style: preserve-3d; }
    .rt-podium3d::after { content:''; position: absolute; inset: 0; border-radius: 24px; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: rtSheen 6s ease-in-out infinite; }
    @@keyframes rtSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .rt-podium-title { display: flex; align-items: center; gap: 10px; font-family: var(--font-mono); font-size: 11px;
        font-weight: 800; letter-spacing: 2px; text-transform: uppercase; color: #facc15; margin-bottom: 22px;
        transform: translateZ(40px); }
    .rt-podium-title i { animation: rtBlink 1.6s infinite; }
    .rt-podium-title .live { margin-left: auto; display: inline-flex; align-items: center; gap: 6px; color: #4ade80; }
    .rt-podium-title .live i { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: rtBlink 1.2s infinite; }
    .rt-podium { display: flex; align-items: flex-end; justify-content: center; gap: 14px; height: 300px;
        transform: translateZ(50px); }
    .rt-place { display: flex; flex-direction: column; align-items: center; width: 120px; }
    .rt-place-ava { width: 62px; height: 62px; border-radius: 20px; object-fit: cover; margin-bottom: -24px; z-index: 2;
        border: 3px solid var(--card); box-shadow: 0 10px 26px rgba(0,0,0,.35); }
    .rt-place-crown { font-size: 26px; margin-bottom: 4px; z-index: 2; filter: drop-shadow(0 4px 10px rgba(255,215,0,.5));
        animation: rtCrown 3s ease-in-out infinite; }
    @@keyframes rtCrown { 0%,100% { transform: translateY(0) rotate(-4deg); } 50% { transform: translateY(-6px) rotate(4deg); } }
    .rt-place-bar { width: 100%; border-radius: 16px 16px 10px 10px; display: flex; flex-direction: column;
        align-items: center; justify-content: flex-end; padding: 30px 8px 14px; position: relative; overflow: hidden;
        transform-origin: bottom; animation: rtRise .9s cubic-bezier(.16,1,.3,1) backwards; }
    .rt-place:nth-child(1) .rt-place-bar { animation-delay: .5s; }
    .rt-place:nth-child(2) .rt-place-bar { animation-delay: .2s; }
    .rt-place:nth-child(3) .rt-place-bar { animation-delay: .8s; }
    @@keyframes rtRise { from { transform: scaleY(0); } to { transform: scaleY(1); } }
    .rt-place-bar::after { content:''; position: absolute; inset: 0;
        background: linear-gradient(180deg, rgba(255,255,255,.22), transparent 55%); pointer-events: none; }
    .rt-bar-1 { height: 210px; background: linear-gradient(180deg,#FFD700,#b45309); box-shadow: 0 0 40px rgba(255,215,0,.35); }
    .rt-bar-2 { height: 158px; background: linear-gradient(180deg,#cbd5e1,#64748b); }
    .rt-bar-3 { height: 122px; background: linear-gradient(180deg,#d08a4e,#7c4a21); }
    .rt-place-num { font-family: var(--font-mono); font-size: 30px; font-weight: 900; color: rgba(255,255,255,.9); }
    .rt-place-name { font-size: 11px; font-weight: 800; color: rgba(255,255,255,.95); margin-top: 2px;
        max-width: 104px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .rt-place-xp { font-size: 10px; font-weight: 700; color: rgba(255,255,255,.75); font-family: var(--font-mono); }
    .rt-float-chip { position: absolute; z-index: 4; display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent); backdrop-filter: blur(18px);
        border: 1px solid var(--border); border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3); font-size: 13px; animation: rtFloatY 4.5s ease-in-out infinite; }
    @@keyframes rtFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .rt-fc-1 { top: 4%; right: -6px; } .rt-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .rt-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .rt-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0; }
    .rt-fc-ico.g { background: rgba(234,179,8,.14); color: #eab308; }
    .rt-fc-ico.p { background: rgba(139,92,246,.14); color: #8b5cf6; }
    .rt-fc-ico.a { background: rgba(34,197,94,.14); color: #22c55e; }
    .rt-fc-txt b { display: block; font-size: 13px; color: var(--text); font-family: var(--font-mono); }
    .rt-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .rt-cube { position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px; display: flex;
        align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        animation: rtCubeFloat 6s ease-in-out infinite; }
    .rt-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,#FFD700,#b45309); }
    .rt-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#8b5cf6,#6d28d9); animation-delay: 1.5s; }
    @@keyframes rtCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .rt-scroll-hint { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);
        font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-family: var(--font-mono); }
    .rt-paused .rt-aurora, .rt-paused .rt-orb, .rt-paused .rt-ring, .rt-paused .rt-cube,
    .rt-paused .rt-float-chip, .rt-paused .rt-confetti span, .rt-paused .rt-spot { animation-play-state: paused !important; }
    /* --- пафос: софиты, конфетти, чемпион --- */
    .rt-spot { position: absolute; top: -10%; width: 190px; height: 130%; pointer-events: none; opacity: .55;
        background: linear-gradient(to bottom, rgba(255,215,0,.16), transparent 70%);
        clip-path: polygon(42% 0, 58% 0, 100% 100%, 0 100%); filter: blur(6px);
        transform-origin: top center; animation: rtSpot 9s ease-in-out infinite; }
    .rt-spot.s1 { left: 16%; } .rt-spot.s2 { right: 16%; animation-delay: -4.5s; }
    @@keyframes rtSpot { 0%,100% { transform: rotate(-7deg); } 50% { transform: rotate(7deg); } }
    .rt-confetti { position: absolute; inset: 0; overflow: hidden; pointer-events: none; z-index: 1; }
    .rt-confetti span { position: absolute; top: -4%; opacity: 0; animation: rtFall linear infinite; }
    @@keyframes rtFall {
        0% { transform: translateY(-5vh) rotate(0deg); opacity: 0; }
        8% { opacity: .9; }
        90% { opacity: .7; }
        100% { transform: translateY(108vh) rotate(680deg); opacity: 0; } }
    .rt-stage-floor { position: absolute; bottom: 5%; left: 50%; transform: translateX(-50%);
        width: 430px; height: 64px; border-radius: 50%; pointer-events: none; z-index: 1;
        background: radial-gradient(ellipse, rgba(255,215,0,.22) 0%, transparent 70%); filter: blur(4px); }
    .rt-champ { font-family: var(--font-mono); font-size: 9px; font-weight: 900; letter-spacing: 2px;
        color: #1a1a1a; background: linear-gradient(135deg,#FFD700,#FFA500); border-radius: 6px;
        padding: 3px 10px; margin-bottom: 8px; z-index: 2;
        box-shadow: 0 0 18px rgba(255,215,0,.65); animation: rtChamp 2.4s ease-in-out infinite; }
    @@keyframes rtChamp { 0%,100% { transform: scale(1); } 50% { transform: scale(1.09); } }
    .rt-medal { overflow: hidden; position: relative; }
    .rt-medal::after { content: ''; position: absolute; top: 0; left: -80%; width: 50%; height: 100%;
        background: linear-gradient(100deg, transparent, rgba(255,255,255,.55), transparent);
        animation: rtMedalSheen 3.5s ease-in-out infinite; pointer-events: none; }
    @@keyframes rtMedalSheen { 0%,55% { left: -80%; } 100% { left: 180%; } }
    .rt-lb-row.top-1 { animation: rtTopGlow 3s ease-in-out infinite; }
    @@keyframes rtTopGlow { 0%,100% { box-shadow: inset 0 0 0 0 rgba(255,215,0,0); } 50% { box-shadow: inset 0 0 34px rgba(255,215,0,.14); } }
    /* --- рамки уровней: 1 Начинающий, 2 Студент, 3 Опытный, 4 Продвинутый, 5 Эксперт --- */
    .rt-frame { display: inline-flex; padding: 3px; flex-shrink: 0; transition: transform .25s; }
    .rt-frame img { display: block; object-fit: cover; }
    .rt-frame.podium { border-radius: 50%; margin-bottom: -24px; z-index: 2; }
    .rt-frame.podium img { width: 62px; height: 62px; border-radius: 50%; }
    .rt-frame.row { border-radius: 50%; }
    .rt-frame.row img { width: 44px; height: 44px; border-radius: 50%; }
    .rt-lb-row:hover .rt-frame.row { transform: scale(1.08); }
    /* благородные металлы: тонкое кольцо, фаска, гравировка — ноль неона */
    .rt-frame-1 { border-radius: 50%; background: linear-gradient(135deg,#6e4f26,#c9a35c 38%,#7c5a2e 68%,#d9b96c);
        box-shadow: 0 3px 10px rgba(0,0,0,.35), inset 0 1px 1px rgba(255,255,255,.5), inset 0 -1px 2px rgba(60,40,5,.45); }
    .rt-frame-2 { border-radius: 50%; background: linear-gradient(135deg,#5b6470,#e8ebef 38%,#9aa1ab 68%,#f4f6f8);
        box-shadow: 0 3px 10px rgba(0,0,0,.35), inset 0 1px 1px rgba(255,255,255,.7), inset 0 -1px 2px rgba(40,45,55,.4); }
    .rt-frame-3 { border-radius: 50%; background: linear-gradient(135deg,#2e1065,#7c3aed 45%,#4c1d95 80%);
        box-shadow: inset 0 0 0 1px rgba(246,226,122,.85), 0 3px 12px rgba(0,0,0,.4); }
    .rt-frame-4 { border-radius: 50%; background: linear-gradient(135deg,#8a6a1f,#f6e27a 35%,#b8860b 65%,#f9efb5);
        box-shadow: 0 3px 12px rgba(0,0,0,.4), inset 0 1px 1px rgba(255,255,255,.65), inset 0 -1px 2px rgba(90,60,5,.5); }
    .rt-frame-5 { border-radius: 50%; background: linear-gradient(135deg,#7a5c14,#ffe9a3 30%,#d4af37 52%,#fff3c4 74%,#7a5c14);
        box-shadow: 0 4px 16px rgba(0,0,0,.45), 0 0 18px rgba(212,175,55,.35), inset 0 1px 2px rgba(255,255,255,.7), inset 0 -2px 3px rgba(80,55,5,.5);
        position: relative; }
    .rt-frame-5::before { content: ''; position: absolute; top: -4px; left: 50%; width: 9px; height: 9px; z-index: 2;
        transform: translateX(-50%) rotate(45deg);
        background: linear-gradient(135deg,#ffffff,#f6e27a 55%,#b8860b);
        box-shadow: 0 0 10px rgba(255,215,0,.9); }
    .rt-frame-5::after { content: ''; position: absolute; inset: 0; border-radius: 50%; pointer-events: none;
        background: linear-gradient(115deg, transparent 42%, rgba(255,255,255,.30) 50%, transparent 58%);
        background-size: 280% 100%; animation: rtGemSheen 7s ease-in-out infinite; }
    @@keyframes rtGemSheen { 0%,55% { background-position: 125% 0; } 100% { background-position: -25% 0; } }
    /* --- супер-подиум: крышки, база, сияние чемпиона --- */
    .rt-place { position: relative; }
    .rt-place-bar::before { content: ''; position: absolute; top: 0; left: 7%; right: 7%; height: 7px;
        border-radius: 0 0 8px 8px; background: linear-gradient(180deg, rgba(255,255,255,.5), rgba(255,255,255,.05));
        pointer-events: none; }
    .rt-place.champion::after { content: ''; position: absolute; inset: -34px -30px auto -30px; height: 220px;
        background: radial-gradient(ellipse 50% 50% at 50% 30%, rgba(255,215,0,.28) 0%, transparent 70%);
        pointer-events: none; animation: rtChampGlow 2.6s ease-in-out infinite; }
    @@keyframes rtChampGlow { 0%,100% { opacity: .6; } 50% { opacity: 1; } }
    .rt-podium-base { margin: -4px auto 0; max-width: 460px; height: 30px; border-radius: 8px 8px 14px 14px;
        background: linear-gradient(180deg,#3d3d52,#14141b);
        border-top: 3px solid rgba(255,215,0,.85);
        box-shadow: 0 18px 44px rgba(0,0,0,.55), 0 0 34px rgba(255,215,0,.18);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-mono); font-size: 10px; font-weight: 800; letter-spacing: 4px; color: #d4af37; }
    /* --- легенда тиров --- */
    .rt-tiers { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; }
    .rt-tier { display: inline-flex; align-items: center; gap: 9px; padding: 7px 14px 7px 8px; border-radius: 100px;
        background: var(--card); border: 1px solid var(--border); font-size: 12px; font-weight: 700;
        color: var(--text-secondary); text-decoration: none; cursor: pointer;
        transition: all .25s cubic-bezier(.16,1,.3,1); }
    .rt-tier:hover { transform: translateY(-2px); border-color: var(--border-hover);
        box-shadow: 0 8px 22px rgba(0,0,0,.12); }
    .rt-tier.active { border-color: var(--accent); color: var(--text);
        box-shadow: 0 0 0 1px var(--accent), 0 8px 24px var(--accent-glow-strong); }
    .rt-tier.active.tier-1 { border-color: #22c55e; box-shadow: 0 0 0 1px #22c55e, 0 8px 24px rgba(34,197,94,.3); }
    .rt-tier.active.tier-2 { border-color: #3b82f6; box-shadow: 0 0 0 1px #3b82f6, 0 8px 24px rgba(59,130,246,.3); }
    .rt-tier.active.tier-3 { border-color: #8b5cf6; box-shadow: 0 0 0 1px #8b5cf6, 0 8px 24px rgba(139,92,246,.35); }
    .rt-tier.active.tier-4 { border-color: #f59e0b; box-shadow: 0 0 0 1px #f59e0b, 0 8px 24px rgba(245,158,11,.35); }
    .rt-tier.active.tier-5 { border-color: #FFD700; box-shadow: 0 0 0 1px #FFD700, 0 8px 28px rgba(255,215,0,.4); }
    .rt-tier .rt-frame { padding: 2px; border-radius: 50%; }
    .rt-tiers .rt-frame-1, .rt-tiers .rt-frame-2, .rt-tiers .rt-frame-3, .rt-tiers .rt-frame-4 {
        box-shadow: 0 1px 4px rgba(0,0,0,.3), inset 0 1px 1px rgba(255,255,255,.5); }
    .rt-tiers .rt-frame-5 { box-shadow: 0 1px 4px rgba(0,0,0,.3); }
    .rt-tiers .rt-frame-5::before { display: none; }
    .rt-tier { line-height: 1; white-space: nowrap; }
    .rt-tier .rt-frame span { width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center;
        justify-content: center; font-size: 11px; color: #fff; font-weight: 900; }
    .rt-tier small { font-family: var(--font-mono); font-size: 10px; color: var(--text-muted); }
    .rt-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .rt-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px;
        height: 8px; border-radius: 4px; background: #eab308; animation: rtWheel 1.8s ease-in-out infinite; }
    @@keyframes rtWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ BOARD ============ */
    .rt-wrap { max-width: 1280px; margin: 0 auto; padding: 70px clamp(20px,4vw,48px) 90px; }
    .rt-board { border-radius: 22px; background: var(--card); border: 1px solid var(--border);
        overflow: hidden; box-shadow: 0 18px 50px rgba(0,0,0,.14); }
    .rt-lb-head { display: grid; grid-template-columns: 64px minmax(0,1fr) 150px 110px 110px 110px 110px;
        gap: 8px; padding: 14px 24px; background: var(--bg-secondary);
        border-bottom: 2px solid var(--border); font-size: 10px; font-weight: 800; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 1.2px; font-family: var(--font-mono); align-items: center; }
    .rt-lb-row { display: grid; grid-template-columns: 64px minmax(0,1fr) 150px 110px 110px 110px 110px;
        gap: 8px; align-items: center; padding: 14px 24px; border-bottom: 1px solid color-mix(in srgb, var(--border) 55%, transparent);
        border-left: 3px solid transparent;
        opacity: 0; transform: translateY(16px);
        transition: opacity .45s ease, transform .45s cubic-bezier(.16,1,.3,1), background .15s, border-color .15s; }
    .rt-lb-row.in { opacity: 1; transform: none; }
    .rt-lb-row:last-child { border-bottom: none; }
    .rt-lb-row:hover { background: color-mix(in srgb, #eab308 5%, transparent); border-left-color: #eab308; }
    .rt-lb-row.top-1 { background: linear-gradient(90deg, rgba(255,215,0,.09), transparent 65%); border-left-color: #FFD700; }
    .rt-lb-row.top-2 { background: linear-gradient(90deg, rgba(192,192,192,.09), transparent 65%); border-left-color: #C0C0C0; }
    .rt-lb-row.top-3 { background: linear-gradient(90deg, rgba(205,127,50,.09), transparent 65%); border-left-color: #CD7F32; }
    .rt-lb-row.me { background: color-mix(in srgb, #eab308 9%, transparent); border-left-color: #eab308;
        box-shadow: inset 0 0 0 1px rgba(234,179,8,.35); }
    .rt-lb-row.me-flash { animation: rtFlash 1.6s ease; }
    @@keyframes rtFlash { 0%,100% { box-shadow: inset 0 0 0 1px rgba(234,179,8,.35); } 30%,60% { box-shadow: inset 0 0 0 2px #eab308, 0 0 30px rgba(234,179,8,.35); } }
    .rt-rank { font-family: var(--font-mono); font-size: 18px; font-weight: 800; color: var(--text-muted); }
    .rt-medal { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px;
        border-radius: 50%; font-size: 16px; transition: transform .25s; }
    .rt-medal:hover { transform: scale(1.15) rotate(6deg); }
    .rt-medal-1 { background: linear-gradient(135deg,#FFD700,#FFA500); color: #7c5800; box-shadow: 0 4px 18px rgba(255,215,0,.5); }
    .rt-medal-2 { background: linear-gradient(135deg,#E8E8E8,#B0B0B0); color: #555; box-shadow: 0 4px 18px rgba(192,192,192,.45); }
    .rt-medal-3 { background: linear-gradient(135deg,#CD7F32,#A0522D); color: #fff; box-shadow: 0 4px 18px rgba(205,127,50,.5); }
    .rt-player { display: flex; align-items: center; gap: 13px; min-width: 0; }
    .rt-player img { object-fit: cover; flex-shrink: 0; }
    .rt-lb-row:hover .rt-frame.row { transform: scale(1.08); }
    .rt-player-name { font-size: 14px; font-weight: 800; color: var(--text); text-decoration: none;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .rt-player-name:hover { color: #eab308; }
    .rt-player-sub { font-size: 11px; color: var(--text-muted); margin-top: 2px; overflow: hidden;
        text-overflow: ellipsis; white-space: nowrap; }
    .rt-lvl { display: inline-flex; align-items: center; gap: 5px; padding: 5px 11px; border-radius: 9px;
        font-size: 12px; font-weight: 800; }
    .rt-xpbar { width: 88px; height: 7px; border-radius: 4px; background: var(--border); margin-top: 6px; overflow: hidden; }
    .rt-xpbar div { height: 100%; border-radius: 4px; background: linear-gradient(90deg,#eab308,#f97316);
        width: 0; transition: width 1s cubic-bezier(.16,1,.3,1); box-shadow: 0 0 8px rgba(234,179,8,.5); }
    .rt-xptext { font-size: 10px; color: var(--text-muted); margin-top: 3px; font-family: var(--font-mono); }
    .rt-xp { font-size: 14px; font-weight: 800; font-family: var(--font-mono);
        background: linear-gradient(135deg,#eab308,#f97316); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; font-variant-numeric: tabular-nums; }
    .rt-num { display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 32px;
        border-radius: 10px; font-size: 13px; font-weight: 800; font-family: var(--font-mono); }
    .rt-tok { display: inline-flex; align-items: center; gap: 5px; padding: 5px 11px; border-radius: 10px;
        font-size: 12px; font-weight: 800; font-family: var(--font-mono);
        background: rgba(245,158,11,.1); color: #fbbf24; border: 1px solid rgba(245,158,11,.25); }
    .rt-center { text-align: center; }
    .rt-empty { text-align: center; padding: 80px 24px; color: var(--text-muted); font-family: var(--font-mono); }
    .rt-empty i { font-size: 52px; margin-bottom: 18px; display: block; opacity: .3; }
    .rt-pag { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 22px; flex-wrap: wrap;
        border-top: 1px solid var(--border); }
    .rt-pg { min-width: 42px; height: 42px; padding: 0 13px; display: inline-flex; align-items: center; justify-content: center;
        border-radius: 12px; border: 1px solid var(--border); background: var(--bg-secondary); color: var(--text-muted);
        font-weight: 700; font-size: 13px; text-decoration: none; transition: all .3s; font-family: var(--font-mono); }
    .rt-pg:hover { border-color: #eab308; color: #eab308; transform: translateY(-2px); }
    .rt-pg.on { background: linear-gradient(135deg,#eab308,#f97316); color: #fff; border-color: transparent;
        box-shadow: 0 4px 16px rgba(234,179,8,.4); }
    .rt-pg.dis { opacity: .35; pointer-events: none; }

    .rt-info { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-top: 26px; }
    .rt-info-card { border-radius: 20px; padding: 28px; color: #fff; position: relative; overflow: hidden;
        transition: transform .35s; }
    .rt-info-card:hover { transform: translateY(-6px); }
    .rt-info-card::before { content:''; position: absolute; inset: 0;
        background: radial-gradient(circle at 80% 20%, rgba(255,255,255,.15) 0%, transparent 50%); }
    .rt-info-ico { display: inline-flex; align-items: center; justify-content: center; width: 52px; height: 52px;
        border-radius: 15px; background: rgba(255,255,255,.15); backdrop-filter: blur(8px); font-size: 20px;
        margin-bottom: 14px; position: relative; z-index: 1; }
    .rt-info-t { font-size: 17px; font-weight: 800; margin-bottom: 10px; position: relative; z-index: 1; }
    .rt-info-list { list-style: none; padding: 0; margin: 0; position: relative; z-index: 1; }
    .rt-info-list li { font-size: 13px; color: rgba(255,255,255,.88); padding: 5px 0; display: flex; align-items: center; gap: 8px; }
    .rt-info-list li i { font-size: 10px; opacity: .7; }

    @@media(max-width: 1020px) {
        .rt-tiers { flex-wrap: nowrap; overflow-x: auto; padding-bottom: 8px; margin-left: -4px; padding-left: 4px; }
        .rt-tier { flex-shrink: 0; }
        .rt-hero3d-inner { grid-template-columns: 1fr; }
        .rt-stage { height: 480px; }
        .rt-fc-3 { right: 0; } .rt-fc-1 { right: 0; } .rt-fc-2 { left: 0; }
        .rt-info { grid-template-columns: 1fr; }
        .rt-lb-head, .rt-lb-row { grid-template-columns: 52px minmax(0,1fr) 100px 90px; }
        .rt-lb-head > :nth-child(n+5), .rt-lb-row > :nth-child(n+5) { display: none; }
    }
</style>
@endsection

@section('content')
<div class="rt-page">
{{-- ================= HERO 3D ================= --}}
<section class="rt-hero3d" id="rtHero">
    <div class="rt-hero3d-bg">
        <div class="rt-aurora"></div>
        <div class="rt-grid3d" data-depth="18"></div>
        <div class="rt-orb rt-orb-1" data-depth="40"></div>
        <div class="rt-orb rt-orb-2" data-depth="-30"></div>
        <div class="rt-orb rt-orb-3" data-depth="60"></div>
        <div class="rt-spot s1"></div>
        <div class="rt-spot s2"></div>
        <div class="rt-confetti">
            @for($i = 0; $i < 24; $i++)
            @php
                $cc = ['#FFD700','#FFA500','#a855f7','#38bdf8','#f472b6'][$i % 5];
                $cw = 5 + ($i % 4);
                $ch = 8 + ($i % 6);
            @endphp
            <span style="left:{{ 2 + (($i * 41) % 96) }}%;width:{{ $cw }}px;height:{{ $ch }}px;background:{{ $cc }};animation-delay:{{ ($i % 12) * 0.55 }}s;animation-duration:{{ 6 + (($i % 5) * 0.9) }}s;{{ $i % 3 === 0 ? 'border-radius:50%;' : 'border-radius:2px;' }}"></span>
            @endfor
        </div>
    </div>

    <div class="rt-hero3d-inner">
        <div>
            <span class="rt-eyebrow"><i class="fas fa-crown"></i>{{ __('Hall of fame') }} • {{ $users->total() }}</span>
            <h1 class="rt-title">{!! __('Top of the<br><span class="grad">League</span>') !!}</h1>
            <p class="rt-sub">{!! __('Grind <b>XP</b>, collect certificates, outrank everyone. The board never sleeps — <b>climb it</b>.') !!}</p>

            <form method="GET" action="{{ route('ratings.index') }}" class="rt-search3d">
                <i class="fas fa-search"></i>
                <input type="hidden" name="tab" value="{{ $tab }}">
                @if($tier)<input type="hidden" name="tier" value="{{ $tier }}">@endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Find a player...') }}" autocomplete="off">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>

            <div class="rt-tabs3d">
                <a href="{{ route('ratings.index', array_merge(request()->except('tab', 'page'), ['tab' => 'courses'])) }}" class="rt-chip {{ $tab === 'courses' ? 'active' : '' }}"><i class="fas fa-graduation-cap"></i>{{ __('Courses') }}</a>
                <a href="{{ route('ratings.index', array_merge(request()->except('tab', 'page'), ['tab' => 'tests'])) }}" class="rt-chip {{ $tab === 'tests' ? 'active' : '' }}"><i class="fas fa-flask"></i>{{ __('Tests') }}</a>
                <a href="{{ route('ratings.index', array_merge(request()->except('tab', 'page'), ['tab' => 'elo'])) }}" class="rt-chip {{ $tab === 'elo' ? 'active' : '' }}"><i class="fas fa-chess-king"></i>{{ __('ELO') }}</a>
            </div>

            <div class="rt-hero-actions">
                @if($currentUser)
                <button type="button" class="rt-btn rt-btn-me" id="rtFindMe"><i class="fas fa-location-crosshairs"></i>{{ __('Find me') }}</button>
                @endif
                <a href="#rtBoard" class="rt-btn rt-btn-ghost" id="rtToBoard"><i class="fas fa-list-ol"></i>{{ __('View board') }}</a>
            </div>

            <div class="rt-stats3d">
                <div class="rt-stat"><div class="rt-stat-val" data-count="{{ $users->total() }}">0</div><div class="rt-stat-label">{{ __('Players') }}</div></div>
                <div class="rt-stat"><div class="rt-stat-val" data-count="{{ $stats['total_xp'] ?? 0 }}">0</div><div class="rt-stat-label">{{ __('Total XP') }}</div></div>
                <div class="rt-stat"><div class="rt-stat-val" data-count="{{ $stats['certs'] ?? 0 }}">0</div><div class="rt-stat-label">{{ __('Certificates') }}</div></div>
            </div>

            @if($currentUser)
            <div class="rt-me-card">
                <img src="{{ $currentUser->avatar_url }}">
                <div>
                    <div class="rt-me-lvl">{!! $currentUser->level_badge !!} Lv.{{ $currentUser->level }} — {{ $currentUser->level_title }}</div>
                    <div class="rt-me-xp">{{ number_format($currentUser->total_xp) }} XP • {{ $currentUser->ai_tokens }} tokens</div>
                    <div class="rt-me-bar"><div style="width:{{ $currentUser->level_progress }}%"></div></div>
                </div>
            </div>
            @endif
        </div>

        <div class="rt-stage">
            <div class="rt-ring rt-ring-1"><span class="rt-ring-dot"></span></div>
            <div class="rt-ring rt-ring-2"><span class="rt-ring-dot" style="background:#8b5cf6;box-shadow:0 0 14px #8b5cf6"></span></div>
            <div class="rt-cube rt-cube-1" data-depth="70"><i class="fas fa-crown"></i></div>
            <div class="rt-cube rt-cube-2" data-depth="-60"><i class="fas fa-medal"></i></div>
            <div class="rt-stage-floor"></div>

            @php $podium = $users->currentPage() === 1 ? $users->take(3)->values() : collect(); @endphp
            <div class="rt-podium3d" id="rtPodium">
                <div class="rt-podium-title"><i class="fas fa-trophy"></i>TOP-3 • {{ strtoupper($tab) }}<span class="live"><i></i>LIVE</span></div>
                <div class="rt-podium">
                    @php
                        $order = [1 => 1, 0 => 0, 2 => 2];
                        $bars = ['rt-bar-2', 'rt-bar-1', 'rt-bar-3'];
                        $slots = [$podium->get(1), $podium->get(0), $podium->get(2)];
                    @endphp
                    @foreach($slots as $si => $pu)
                    <div class="rt-place{{ $si === 1 ? ' champion' : '' }}">
                        @if($si === 1)<div class="rt-place-crown"><i class="fas fa-crown" style="color:#FFD700"></i></div>@endif
                        @if($pu)
                        <span class="rt-frame podium rt-frame-{{ level_tier($pu->level ?? 1) }}"><img src="{{ $pu->avatar_url }}" title="{{ $pu->name }}"></span>
                        @else
                        <div class="rt-place-ava" style="width:68px;height:68px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--bg-secondary);color:var(--text-muted);border:1px dashed var(--border-hover)"><i class="fas fa-user"></i></div>
                        @endif
                        @if($si === 1)<div class="rt-champ">CHAMPION</div>@endif
                        <div class="rt-place-bar {{ $bars[$si] }}">
                            <div class="rt-place-num">{{ [2, 1, 3][$si] }}</div>
                            <div class="rt-place-name">{{ $pu ? $pu->name : '—' }}</div>
                            <div class="rt-place-xp">{{ $pu ? number_format($pu->total_xp) . ' xp' : '' }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="rt-podium-base">HALL OF FAME • TOP-3</div>
            </div>

            <div class="rt-float-chip rt-fc-1" data-depth="50">
                <div class="rt-fc-ico g"><i class="fas fa-bolt"></i></div>
                <div class="rt-fc-txt"><b>+100 XP</b><span>{{ __('per course') }}</span></div>
            </div>
            <div class="rt-float-chip rt-fc-2" data-depth="-45">
                <div class="rt-fc-ico p"><i class="fas fa-arrow-trend-up"></i></div>
                <div class="rt-fc-txt"><b>{{ __('Level up') }}</b><span>{{ __('every milestone') }}</span></div>
            </div>
            <div class="rt-float-chip rt-fc-3" data-depth="35">
                <div class="rt-fc-ico a"><i class="fas fa-coins"></i></div>
                <div class="rt-fc-txt"><b>{{ __('AI tokens') }}</b><span>{{ __('for activity') }}</span></div>
            </div>
        </div>
    </div>

    <div class="rt-scroll-hint"><div class="rt-mouse"></div><span>{{ __('Scroll — the board') }}</span></div>
</section>

{{-- ================= BOARD ================= --}}
<div class="rt-wrap" id="rtBoard">
    @php
        $tiersLegend = [
            ['t' => 1, 'n' => __('Beginner'), 'l' => 'Lv.1+', 'c' => '#22c55e', 'i' => 'fa-seedling'],
            ['t' => 2, 'n' => __('Student'), 'l' => 'Lv.5+', 'c' => '#3b82f6', 'i' => 'fa-graduation-cap'],
            ['t' => 3, 'n' => __('Experienced'), 'l' => 'Lv.10+', 'c' => '#8b5cf6', 'i' => 'fa-star'],
            ['t' => 4, 'n' => __('Advanced'), 'l' => 'Lv.15+', 'c' => '#f59e0b', 'i' => 'fa-fire'],
            ['t' => 5, 'n' => __('Expert'), 'l' => 'Lv.30+', 'c' => '#FFD700', 'i' => 'fa-crown'],
        ];
    @endphp
    <div class="rt-tiers">
        <a href="{{ route('ratings.index', array_merge(request()->except('tier', 'page'), ['tab' => $tab])) }}" class="rt-tier {{ !$tier ? 'active' : '' }}">
            <span class="rt-frame" style="background:var(--border-hover);padding:2px"><span style="background:var(--text-muted);width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;color:#fff"><i class="fas fa-users"></i></span></span>
            {{ __('All') }}
        </a>
        @foreach($tiersLegend as $tl)
        <a href="{{ route('ratings.index', array_merge(request()->except('tier', 'page'), ['tab' => $tab, 'tier' => $tl['t']])) }}" class="rt-tier {{ (int)$tier === $tl['t'] ? 'active tier-' . $tl['t'] : '' }}">
            <span class="rt-frame rt-frame-{{ $tl['t'] }}"><span style="background:{{ $tl['c'] }}"><i class="fas {{ $tl['i'] }}"></i></span></span>
            {{ $tl['n'] }} <small>{{ $tl['l'] }}</small>
        </a>
        @endforeach
    </div>
    <div class="rt-board">
        <div class="rt-lb-head">
            <div>#</div>
            <div>{{ __('Player') }}</div>
            <div>{{ __('Level') }}</div>
            <div class="rt-center">{{ __('XP') }}</div>
            @if($tab === 'courses')
            <div class="rt-center">{{ __('Certs') }}</div>
            <div class="rt-center">{{ __('Courses') }}</div>
            @elseif($tab === 'elo')
            <div class="rt-center">{{ __('Rating') }}</div>
            <div class="rt-center">{{ __('Peak') }}</div>
            @else
            <div class="rt-center">{{ __('Practice') }}</div>
            <div class="rt-center">{{ __('Contests') }}</div>
            @endif
            <div class="rt-center">{{ __('Tokens') }}</div>
        </div>

        @forelse($users as $index => $user)
        @php
            $rank = ($users->currentPage() - 1) * $users->perPage() + $index + 1;
            $rowClass = match($rank) { 1 => 'top-1', 2 => 'top-2', 3 => 'top-3', default => '' };
            $isMe = $currentUser && $user->id === $currentUser->id;
        @endphp
        <div class="rt-lb-row {{ $rowClass }}{{ $isMe ? ' me' : '' }}" data-i="{{ $index }}" @if($isMe)id="rtMeRow"@endif>
            <div>
                @if($rank === 1)
                <span class="rt-medal rt-medal-1"><i class="fas fa-crown"></i></span>
                @elseif($rank === 2)
                <span class="rt-medal rt-medal-2">{{ $rank }}</span>
                @elseif($rank === 3)
                <span class="rt-medal rt-medal-3">{{ $rank }}</span>
                @else
                <span class="rt-rank">{{ $rank }}</span>
                @endif
            </div>
            <div class="rt-player">
                <span class="rt-frame row rt-frame-{{ level_tier($user->level ?? 1) }}"><img src="{{ $user->avatar_url }}"></span>
                <div style="min-width:0">
                    <a href="{{ route('profile.show', $user->id) }}" class="rt-player-name">{{ $user->name }}</a>
                    @if($user->country)
                    <span style="font-size:15px;margin-left:6px" title="{{ country_name($user->country) }}">{!! country_flag($user->country) !!}</span>
                    @endif
                    <div class="rt-player-sub">{{ $user->title ?? $user->email }}</div>
                </div>
            </div>
            <div>
                <span class="rt-lvl" style="background:{{ $user->level_color }}18;color:{{ $user->level_color }}">{!! $user->level_badge !!} {{ $user->level }}</span>
                <div class="rt-xpbar"><div data-w="{{ $user->level_progress }}"></div></div>
                <div class="rt-xptext">{{ $user->xp_for_current_level }}/{{ $user->xp_for_next_level }}</div>
            </div>
            <div class="rt-center"><span class="rt-xp">{{ number_format($user->total_xp) }}</span></div>
            @if($tab === 'courses')
            <div class="rt-center"><span class="rt-num" style="background:rgba(234,179,8,.1);color:#eab308">{{ $user->certificates_count }}</span></div>
            <div class="rt-center"><span class="rt-num" style="background:var(--accent-glow);color:var(--accent)">{{ $user->completed_courses_count ?? 0 }}</span></div>
            @elseif($tab === 'elo')
            <div class="rt-center"><span class="rt-num" style="background:var(--accent-glow);color:var(--accent);font-size:15px">{{ $user->rating ?? 1200 }}</span></div>
            <div class="rt-center"><span class="rt-num" style="background:rgba(245,158,11,.1);color:#f59e0b">{{ $user->rating_peak ?? 1200 }}</span></div>
            @else
            <div class="rt-center"><span class="rt-num" style="background:rgba(34,197,94,.1);color:#22c55e">{{ $user->practice_passed_count ?? 0 }}</span></div>
            <div class="rt-center"><span class="rt-num" style="background:rgba(168,85,247,.1);color:#a855f7">{{ $user->contest_passed_count ?? 0 }}</span></div>
            @endif
            <div class="rt-center"><span class="rt-tok"><i class="fas fa-coins" style="font-size:10px"></i>{{ $user->ai_tokens }}</span></div>
        </div>
        @empty
        <div class="rt-empty">
            <i class="fas fa-trophy"></i>
            <p>{{ __('No players yet') }}</p>
            <small>{{ __('Be the first in the leaderboard!') }}</small>
        </div>
        @endforelse

        @if($users->hasPages())
        <div class="rt-pag">
            @if($users->onFirstPage())
            <span class="rt-pg dis"><i class="fas fa-chevron-left"></i></span>
            @else
            <a href="{{ $users->previousPageUrl() }}" class="rt-pg"><i class="fas fa-chevron-left"></i></a>
            @endif
            @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
            @if($page == $users->currentPage())
            <span class="rt-pg on">{{ $page }}</span>
            @else
            <a href="{{ $url }}" class="rt-pg">{{ $page }}</a>
            @endif
            @endforeach
            @if($users->currentPage() + 2 < $users->lastPage())
            <span style="color:var(--text-muted)">…</span>
            <a href="{{ $users->url($users->lastPage()) }}" class="rt-pg">{{ $users->lastPage() }}</a>
            @endif
            @if($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}" class="rt-pg"><i class="fas fa-chevron-right"></i></a>
            @else
            <span class="rt-pg dis"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
        @endif
    </div>

    <div class="rt-info">
        <div class="rt-info-card" style="background:linear-gradient(135deg, #6366f1, #8b5cf6)">
            <div class="rt-info-ico"><i class="fas fa-bullseye"></i></div>
            <div class="rt-info-t">{{ __('Earn XP') }}</div>
            <ul class="rt-info-list">
                <li><i class="fas fa-book-open"></i> +10 XP — {{ __('Per lesson') }}</li>
                <li><i class="fas fa-flask"></i> +25 XP — {{ __('Per test') }}</li>
                <li><i class="fas fa-code"></i> +30 XP — {{ __('Per practice') }}</li>
                <li><i class="fas fa-pen-fancy"></i> +50 XP — {{ __('Per exam') }}</li>
                <li><i class="fas fa-graduation-cap"></i> +100 XP — {{ __('Per course') }}</li>
            </ul>
        </div>
        <div class="rt-info-card" style="background:linear-gradient(135deg, #f59e0b, #f97316)">
            <div class="rt-info-ico"><i class="fas fa-gem"></i></div>
            <div class="rt-info-t">{{ __('AI Tokens') }}</div>
            <ul class="rt-info-list">
                <li><i class="fas fa-wallet"></i> {{ __('Starting balance: 25 tokens') }}</li>
                <li><i class="fas fa-paper-plane"></i> -1 token — {{ __('Per AI message') }}</li>
                <li><i class="fas fa-sun"></i> +5 tokens — {{ __('Daily bonus') }}</li>
                <li><i class="fas fa-chart-line"></i> {{ __('Earn tokens for activity') }}</li>
            </ul>
        </div>
        <div class="rt-info-card" style="background:linear-gradient(135deg, #10b981, #14b8a6)">
            <div class="rt-info-ico"><i class="fas fa-bolt"></i></div>
            <div class="rt-info-t">{{ __('Levels') }}</div>
            <ul class="rt-info-list">
                <li><i class="fas fa-seedling"></i> {{ __('Beginner') }} — 1</li>
                <li><i class="fas fa-graduation-cap"></i> {{ __('Student') }} — 5</li>
                <li><i class="fas fa-rocket"></i> {{ __('Experienced') }} — 10</li>
                <li><i class="fas fa-fire"></i> {{ __('Advanced') }} — 15</li>
                <li><i class="fas fa-crown"></i> {{ __('Expert') }} — 30+</li>
            </ul>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    /* --- Hero mouse 3D parallax --- */
    var hero = document.getElementById('rtHero');
    var podium = document.getElementById('rtPodium');
    var layers = document.querySelectorAll('#rtHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0, heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('rt-paused', !heroVisible);
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
                if (podium) podium.style.transform = 'rotateX(' + (6 + rx * -1).toFixed(2) + 'deg) rotateY(' + (-10 + ry).toFixed(2) + 'deg)';
            }
            requestAnimationFrame(tilt);
        })();
    }

    /* --- Animated counters --- */
    document.querySelectorAll('.rt-stat-val[data-count]').forEach(function(el) {
        var target = parseInt(el.dataset.count || 0, 10), t0 = null;
        function step(t) {
            if (!t0) t0 = t;
            var p = Math.min(1, (t - t0) / 1400);
            var e = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.round(target * e).toLocaleString('en-US');
            if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    });

    /* --- Rows stagger reveal + XP bars --- */
    var rows = document.querySelectorAll('.rt-lb-row');
    function fillBars(scope) {
        (scope || document).querySelectorAll('.rt-xpbar div[data-w]').forEach(function(b) {
            b.style.width = b.dataset.w + '%';
        });
    }
    if ('IntersectionObserver' in window && rows.length) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(en) {
                if (en.isIntersecting) {
                    var el = en.target;
                    el.style.transitionDelay = (parseInt(el.dataset.i || 0, 10) % 10 * 0.05) + 's';
                    el.classList.add('in');
                    setTimeout(function() { fillBars(el); }, 200);
                    io.unobserve(el);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
        rows.forEach(function(r) { io.observe(r); });
        setTimeout(function() { rows.forEach(function(r) { r.classList.add('in'); }); fillBars(document); }, 4000);
    } else {
        rows.forEach(function(r) { r.classList.add('in'); }); fillBars(document);
    }

    /* --- Find me --- */
    var findMe = document.getElementById('rtFindMe');
    if (findMe) findMe.addEventListener('click', function() {
        var me = document.getElementById('rtMeRow');
        if (me) {
            me.scrollIntoView({ behavior: 'smooth', block: 'center' });
            me.classList.remove('me-flash');
            setTimeout(function() { me.classList.add('me-flash'); }, 350);
        } else {
            var t = document.getElementById('rtBoard');
            if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    /* --- Scroll to board --- */
    var toBoard = document.getElementById('rtToBoard');
    if (toBoard) toBoard.addEventListener('click', function(e) {
        e.preventDefault();
        var t = document.getElementById('rtBoard');
        if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();
</script>
@endsection
