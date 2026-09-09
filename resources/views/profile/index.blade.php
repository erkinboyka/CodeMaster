@extends('layouts.app')

@section('title', __('profile_heading') . ' - CodeMaster')

@section('head')
<style>
    /* ============ PROFILE: ID-CARD THEME + 3D HERO ============ */
    .pr-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .pr-hero3d { position: relative; min-height: 100vh; display: flex; align-items: center;
        overflow: hidden; padding: 110px clamp(20px,4vw,56px) 90px; isolation: isolate; perspective: 1600px; }
    .pr-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .pr-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(139,92,246,.14) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(236,72,153,.10) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(56,189,248,.10) 0%, transparent 60%);
        animation: prAurora 22s ease-in-out infinite alternate; }
    @@keyframes prAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); } }
    .pr-grid3d { position: absolute; inset: -50%;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px; opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2); transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%); }
    .pr-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .pr-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: prOrb1 16s ease-in-out infinite; }
    .pr-orb-2 { width: 460px; height: 460px; background: #8b5cf6; opacity: .10; bottom: -18%; right: -6%; animation: prOrb2 20s ease-in-out infinite; }
    .pr-orb-3 { width: 260px; height: 260px; background: #38bdf8; opacity: .10; top: 55%; left: 42%; animation: prOrb3 12s ease-in-out infinite; }
    @@keyframes prOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes prOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes prOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }
    @@keyframes prBlink { 0%,100%{opacity:1} 50%{opacity:.35} }

    .pr-hero3d-inner { position: relative; z-index: 2; width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px,5vw,72px); align-items: center; }
    .pr-eyebrow { display: inline-flex; align-items: center; gap: 10px; padding: 8px 18px; border-radius: 100px;
        background: var(--accent-glow); border: 1px solid var(--accent-glow-strong);
        font-size: 11px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase; color: var(--accent); margin-bottom: 22px; }
    .pr-eyebrow i { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: prBlink 1.6s infinite;
        box-shadow: 0 0 10px #22c55e; }
    .pr-title { font-size: clamp(40px,5.6vw,78px); font-weight: 900; line-height: .98; letter-spacing: -2.5px;
        margin: 0 0 14px; color: var(--text); overflow-wrap: anywhere; }
    .pr-title .grad { background: linear-gradient(120deg, var(--accent), #8b5cf6 45%, #38bdf8 85%); background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: prGradShift 7s ease-in-out infinite; display: inline-block;
        filter: drop-shadow(0 8px 30px var(--accent-glow-strong)); }
    @@keyframes prGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    .pr-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.7; max-width: 540px;
        margin-bottom: 22px; }
    .pr-links { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 24px; }
    .pr-link { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 12px;
        background: var(--card); border: 1px solid var(--border); color: var(--text-secondary);
        font-size: 13px; font-weight: 700; text-decoration: none; transition: all .25s; }
    .pr-link:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-2px); }
    .pr-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
    .pr-btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 26px; border-radius: 16px;
        font-weight: 800; font-size: 14px; text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
        border: 1px solid transparent; cursor: pointer; }
    .pr-btn-go { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff;
        box-shadow: 0 10px 32px var(--accent-glow-strong); }
    .pr-btn-go:hover { transform: translateY(-3px) scale(1.02); }
    .pr-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .pr-btn-ghost:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-3px); }
    .pr-stats3d { display: flex; gap: clamp(20px,3.5vw,44px); flex-wrap: wrap; }
    .pr-stat { position: relative; }
    .pr-stat + .pr-stat::before { content:''; position: absolute; left: -22px; top: 50%; transform: translateY(-50%);
        width: 1px; height: 38px; background: var(--border); }
    .pr-stat-val { font-size: clamp(26px,3vw,38px); font-weight: 900; line-height: 1; font-variant-numeric: tabular-nums;
        background: linear-gradient(135deg,var(--text),var(--accent)); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; }
    .pr-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px;
        margin-top: 6px; font-weight: 600; }

    /* --- 3D ID-card stage --- */
    .pr-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .pr-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .pr-ring-1 { width: 480px; height: 480px; animation: prSpin 26s linear infinite; opacity: .7; }
    .pr-ring-2 { width: 590px; height: 590px; animation: prSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes prSpin { to { transform: rotate(360deg); } }
    @@keyframes prSpinRev { to { transform: rotate(-360deg); } }
    .pr-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: var(--accent);
        box-shadow: 0 0 14px var(--accent); top: -5px; left: 50%; }
    .pr-id3d { position: relative; width: 100%; max-width: 420px; padding: 26px; overflow: hidden;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid var(--border); border-radius: 24px;
        box-shadow: 0 40px 100px rgba(0,0,0,.35), 0 0 80px var(--accent-glow);
        transform-style: preserve-3d; transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear; z-index: 3; }
    .pr-id3d::after { content:''; position: absolute; inset: 0; border-radius: 24px; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: prSheen 6s ease-in-out infinite; }
    @@keyframes prSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .pr-id-stripe { position: absolute; top: 0; left: 0; right: 0; height: 6px;
        background: linear-gradient(90deg,var(--accent),#8b5cf6,#38bdf8); }
    .pr-id-top { display: flex; align-items: center; gap: 16px; margin: 8px 0 18px; transform: translateZ(40px); }
    .pr-id-ava { position: relative; flex-shrink: 0; }
    /* живописная рама: золото, резьба-волна, угловые гвозди, фаска */
    .pf-paint { position: relative; display: inline-block; padding: 13px; border-radius: 24px;
        background:
            radial-gradient(circle 4px at 15px 15px, #fffbe8 0 1.5px, #7c5c14 2.5px, transparent 4px),
            radial-gradient(circle 4px at calc(100% - 15px) 15px, #fffbe8 0 1.5px, #7c5c14 2.5px, transparent 4px),
            radial-gradient(circle 4px at 15px calc(100% - 15px), #fffbe8 0 1.5px, #7c5c14 2.5px, transparent 4px),
            radial-gradient(circle 4px at calc(100% - 15px) calc(100% - 15px), #fffbe8 0 1.5px, #7c5c14 2.5px, transparent 4px),
            repeating-radial-gradient(circle at 50% 135%, rgba(255,255,255,.10) 0 2px, rgba(0,0,0,.08) 2px 5px),
            linear-gradient(135deg,#6e5110,#f6e27a 28%,#a87b1f 52%,#f9efb5 76%,#6e5110);
        background-repeat: no-repeat,no-repeat,no-repeat,no-repeat,repeat,no-repeat;
        box-shadow: 0 18px 44px rgba(0,0,0,.4), inset 0 2px 3px rgba(255,255,255,.65), inset 0 -4px 8px rgba(60,40,5,.5); }
    .pf-paint::before { content: ''; position: absolute; inset: 6px; border-radius: 18px; pointer-events: none;
        border: 2px solid rgba(74,52,8,.6); box-shadow: inset 0 0 0 1px rgba(255,248,220,.5); }
    .pf-paint::after { content: ''; position: absolute; inset: 0; border-radius: 24px; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.22) 45%, transparent 60%);
        transform: translateX(-100%); animation: pfPaintSheen 7s ease-in-out infinite; }
    @@keyframes pfPaintSheen { 0%,55% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
    .pf-paint img { display: block; width: 84px; height: 84px; border-radius: 12px; object-fit: cover;
        box-shadow: 0 0 0 1px rgba(0,0,0,.45); position: relative; }
    /* тир-металл внутри рамы: тонкое кольцо по уровню */
    .pf-paint .pf-tier { display: inline-flex; padding: 2.5px; border-radius: 50%; position: relative; }
    .pf-paint .pf-tier img { width: 78px; height: 78px; border-radius: 50%; box-shadow: none; }
    .pf-tier-1 { background: linear-gradient(135deg,#6e4f26,#c9a35c 38%,#7c5a2e 68%,#d9b96c);
        box-shadow: 0 2px 8px rgba(0,0,0,.4), inset 0 1px 1px rgba(255,255,255,.5); }
    .pf-tier-2 { background: linear-gradient(135deg,#5b6470,#e8ebef 38%,#9aa1ab 68%,#f4f6f8);
        box-shadow: 0 2px 8px rgba(0,0,0,.4), inset 0 1px 1px rgba(255,255,255,.7); }
    .pf-tier-3 { background: linear-gradient(135deg,#2e1065,#7c3aed 45%,#4c1d95 80%);
        box-shadow: inset 0 0 0 1px rgba(246,226,122,.85), 0 2px 10px rgba(0,0,0,.4); }
    .pf-tier-4 { background: linear-gradient(135deg,#8a6a1f,#f6e27a 35%,#b8860b 65%,#f9efb5);
        box-shadow: 0 2px 10px rgba(0,0,0,.4), inset 0 1px 1px rgba(255,255,255,.65); }
    .pf-tier-5 { background: linear-gradient(135deg,#7a5c14,#ffe9a3 30%,#d4af37 52%,#fff3c4 74%,#7a5c14);
        box-shadow: 0 2px 12px rgba(0,0,0,.45), 0 0 14px rgba(212,175,55,.4), inset 0 1px 2px rgba(255,255,255,.7); }
    .pr-id-cam { position: absolute; bottom: -6px; right: -6px; width: 28px; height: 28px; border-radius: 50%;
        background: linear-gradient(135deg,var(--accent),#8b5cf6); border: 2px solid var(--card); color: #fff;
        font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform .2s; }
    .pr-id-cam:hover { transform: scale(1.12); }
    .pr-id-name { font-size: 19px; font-weight: 900; color: var(--text); line-height: 1.2; overflow-wrap: anywhere; }
    .pr-id-lvl { display: inline-flex; align-items: center; gap: 6px; margin-top: 7px; padding: 4px 11px;
        border-radius: 9px; font-size: 11px; font-weight: 800; }
    .pr-id-xp { font-family: var(--font-mono); font-size: 28px; font-weight: 800;
        background: linear-gradient(135deg,var(--accent),#8b5cf6); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; font-variant-numeric: tabular-nums;
        transform: translateZ(30px); }
    .pr-id-sub { font-size: 10px; color: var(--text-muted); font-family: var(--font-mono); letter-spacing: 1.5px;
        text-transform: uppercase; margin-bottom: 10px; }
    .pr-id-bar { height: 10px; border-radius: 5px; background: var(--border); overflow: hidden; margin-bottom: 8px;
        transform: translateZ(25px); }
    .pr-id-bar div { height: 100%; width: 0; border-radius: 5px; background: linear-gradient(90deg,var(--accent),#8b5cf6,#22c55e);
        box-shadow: 0 0 12px var(--accent-glow-strong); animation: prXp 2.2s cubic-bezier(.16,1,.3,1) .4s forwards; }
    @@keyframes prXp { to { width: var(--w, 50%); } }
    .pr-id-meta { display: flex; justify-content: space-between; font-size: 10px; color: var(--text-muted);
        font-family: var(--font-mono); margin-bottom: 18px; }
    .pr-id-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; transform: translateZ(20px); }
    .pr-mini { border-radius: 14px; background: var(--bg-secondary); border: 1px solid var(--border);
        padding: 12px 14px; display: flex; align-items: center; gap: 10px; }
    .pr-mini i { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-size: 14px; flex-shrink: 0; }
    .pr-mini b { display: block; font-size: 15px; font-weight: 900; color: var(--text); font-variant-numeric: tabular-nums; }
    .pr-mini span { font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: .8px; font-weight: 700; }
    .pr-float-chip { position: absolute; z-index: 4; display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent); backdrop-filter: blur(18px);
        border: 1px solid var(--border); border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3); font-size: 13px; animation: prFloatY 4.5s ease-in-out infinite; }
    @@keyframes prFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .pr-fc-1 { top: 4%; right: -6px; } .pr-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .pr-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .pr-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0; }
    .pr-fc-ico.g { background: rgba(234,179,8,.14); color: #eab308; }
    .pr-fc-ico.p { background: rgba(139,92,246,.14); color: #8b5cf6; }
    .pr-fc-ico.a { background: rgba(34,197,94,.14); color: #22c55e; }
    .pr-fc-txt b { display: block; font-size: 13px; color: var(--text); font-family: var(--font-mono); }
    .pr-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .pr-cube { position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px; display: flex;
        align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        animation: prCubeFloat 6s ease-in-out infinite; }
    .pr-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,var(--accent),#8b5cf6); }
    .pr-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#eab308,#b45309); animation-delay: 1.5s; }
    @@keyframes prCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .pr-scroll-hint { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);
        font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-family: var(--font-mono); }
    .pr-paused .pr-aurora, .pr-paused .pr-orb, .pr-paused .pr-ring, .pr-paused .pr-cube,
    .pr-paused .pr-float-chip { animation-play-state: paused !important; }
    .pr-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .pr-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px;
        height: 8px; border-radius: 4px; background: var(--accent); animation: prWheel 1.8s ease-in-out infinite; }
    @@keyframes prWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ BODY (tabs, cards, modals) — как было ============ */
    .pf-body { padding: 4rem 1.5rem; max-width: 1120px; margin: 0 auto; }
    .pf-tabs { display: flex; gap: 0.25rem; overflow-x: auto; background: var(--card); border: 1px solid var(--border);
        border-radius: 1rem; padding: 0.3rem; margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,.12); }
    .pf-tab { flex: 1 0 auto; padding: 0.65rem 1rem; border-radius: 0.7rem; background: transparent; border: none;
        cursor: pointer; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); transition: all 0.2s;
        white-space: nowrap; }
    .pf-tab.active { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff;
        box-shadow: 0 4px 14px var(--accent-glow-strong); }
    .pf-tab:hover:not(.active) { color: var(--text); }
    .pf-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem;
        opacity: 0; transform: translateY(22px); transition: opacity .5s ease, transform .5s cubic-bezier(.16,1,.3,1), border-color .25s; }
    .pf-card.in { opacity: 1; transform: none; }
    .pf-card__header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
    .pf-card__title { font-size: 1rem; font-weight: 700; color: var(--text); }
    .pf-card__add { font-size: 0.85rem; color: var(--accent); background: none; border: none; cursor: pointer;
        font-weight: 500; transition: color 0.2s; }
    .pf-card__add:hover { color: var(--accent-hover); }
    .pf-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .pf-grid-2 > * { display: flex; flex-direction: column; gap: 1.5rem; }
    .pf-item { display: flex; gap: 1rem; padding: 1rem; background: var(--bg-2); border-radius: var(--radius-md);
        transition: background 0.2s; }
    .pf-item:hover { background: color-mix(in srgb, var(--accent) 5%, var(--bg-2)); }
    .pf-item__icon { width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; display: flex; align-items: center;
        justify-content: center; flex-shrink: 0; font-size: 0.9rem; }
    .pf-item__icon--accent { background: color-mix(in srgb, var(--accent) 15%, var(--card)); color: var(--accent); }
    .pf-item__icon--purple { background: color-mix(in srgb, var(--accent-2) 15%, var(--card)); color: var(--accent-2); }
    .pf-item__icon--yellow { background: color-mix(in srgb, #f59e0b 15%, var(--card)); color: #f59e0b; }
    .pf-item__icon--green { background: color-mix(in srgb, #10b981 15%, var(--card)); color: #10b981; }
    .pf-item__body { flex: 1; min-width: 0; }
    .pf-item__row { display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; }
    .pf-item__name { font-size: 0.9rem; font-weight: 600; color: var(--text); }
    .pf-item__sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem; }
    .pf-item__desc { font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.35rem; line-height: 1.5; }
    .pf-del { background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 0.25rem;
        border-radius: 0.375rem; transition: all 0.2s; font-size: 0.8rem; }
    .pf-del:hover { color: var(--danger); background: color-mix(in srgb, var(--danger) 10%, var(--card)); }
    .pf-stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .pf-stat { text-align: center; padding: 1rem; background: var(--bg-2); border-radius: var(--radius-md); }
    .pf-stat__val { font-size: 1.5rem; font-weight: 800; }
    .pf-stat__val--accent { color: var(--accent); }
    .pf-stat__val--purple { color: var(--accent-2); }
    .pf-stat__val--yellow { color: #f59e0b; }
    .pf-stat__val--green { color: #10b981; }
    .pf-stat__label { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; }
    .pf-skill { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; border-radius: 9999px;
        background: color-mix(in srgb, var(--accent) 12%, var(--card)); color: var(--accent);
        font-size: 0.8rem; font-weight: 500; }
    .pf-skill i { font-size: 0.6rem; color: var(--success); }
    .pf-empty { text-align: center; padding: 2.5rem 1rem; color: var(--text-muted); font-size: 0.9rem; }
    .pf-empty i { font-size: 2rem; color: var(--border); margin-bottom: 0.75rem; display: block; }
    .pf-port-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .pf-port-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius-lg);
        overflow: hidden; transition: all 0.3s; }
    .pf-port-card:hover { transform: translateY(-2px); box-shadow: var(--card-shadow); }
    .pf-port-card__img { height: 10rem; overflow: hidden; background: linear-gradient(135deg, var(--accent), var(--accent-2)); }
    .pf-port-card__img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
    .pf-port-card:hover .pf-port-card__img img { transform: scale(1.05); }
    .pf-port-card__img i { font-size: 2.5rem; color: rgba(255,255,255,0.25); }
    .pf-port-card__body { padding: 1.1rem; }
    .pf-port-card__row { display: flex; align-items: center; justify-content: space-between; }
    .pf-port-card__title { font-size: 0.9rem; font-weight: 700; color: var(--text); }
    .pf-port-card__desc { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.3rem; }
    .pf-port-card__tags { display: flex; align-items: center; gap: 0.4rem; margin-top: 0.6rem; flex-wrap: wrap; }
    .pf-port-tag { padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; }
    .pf-port-tag--cat { background: color-mix(in srgb, var(--accent) 12%, var(--card)); color: var(--accent); }
    .pf-port-tag--link { background: color-mix(in srgb, #3b82f6 12%, var(--card)); color: #3b82f6; }
    .pf-port-tag--gh { background: var(--bg-2); color: var(--text-muted); }
    .pf-cert-card { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-2);
        border-radius: var(--radius-md); text-decoration: none; transition: all 0.2s; }
    .pf-cert-card:hover { background: color-mix(in srgb, var(--accent) 5%, var(--bg-2)); }
    .pf-cert-card__icon { width: 3rem; height: 3rem; border-radius: 0.75rem; flex-shrink: 0;
        background: linear-gradient(135deg, #f59e0b, #f97316);
        display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1rem; }
    .pf-cert-card__name { font-size: 0.85rem; font-weight: 600; color: var(--text); }
    .pf-cert-card__date { font-size: 0.75rem; color: var(--text-muted); }
    .pf-act-item { display: flex; align-items: start; gap: 0.75rem; padding: 0.75rem; border-radius: var(--radius-md);
        transition: background 0.2s; }
    .pf-act-item:hover { background: var(--bg-2); }
    .pf-act-dot { width: 2.5rem; height: 2.5rem; border-radius: 50%; flex-shrink: 0;
        background: color-mix(in srgb, var(--accent) 12%, var(--card));
        display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 0.8rem; }
    .pf-act-text { font-size: 0.85rem; color: var(--text); line-height: 1.4; }
    .pf-act-time { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; }
    .pf-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .pf-form-group { margin-bottom: 1rem; }
    .pf-form-group:last-child { margin-bottom: 0; }
    .pf-label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text); margin-bottom: 0.4rem; }
    .pf-label i { color: var(--text-muted); margin-right: 0.3rem; }
    .pf-input { width: 100%; padding: 0.6rem 1rem; border: 1px solid var(--border); border-radius: 0.75rem;
        background: var(--bg-2); color: var(--text); font-size: 0.85rem; transition: border-color 0.2s;
        box-sizing: border-box; }
    .pf-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 15%, transparent); }
    textarea.pf-input { resize: none; }
    .pf-btn { padding: 0.6rem 1.5rem; border-radius: 0.75rem; font-size: 0.85rem; font-weight: 600; border: none;
        cursor: pointer; transition: all 0.2s; }
    .pf-btn--primary { background: var(--accent); color: #fff; }
    .pf-btn--primary:hover { background: var(--accent-hover); }
    .pf-btn--ghost { background: var(--bg-2); color: var(--text); border: 1px solid var(--border); }
    .pf-btn--ghost:hover { background: var(--card); }
    .pf-form-actions { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem; }
    .pf-chk { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text); }
    .pf-chk input[type=checkbox] { width: 1rem; height: 1rem; accent-color: var(--accent); }
    .pf-overlay { position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .pf-overlay__bg { position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
    .pf-modal { position: relative; z-index: 1; background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius-xl); padding: 1.5rem; width: 100%; max-width: 28rem; max-height: 90vh;
        overflow-y: auto; overscroll-behavior: contain; box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        scrollbar-width: thin; scrollbar-color: var(--border) transparent; background-clip: padding-box; }
    .pf-modal::-webkit-scrollbar { width: 10px; }
    .pf-modal::-webkit-scrollbar-track { background: transparent; margin: 12px 0; }
    .pf-modal::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px;
        border: 3px solid transparent; background-clip: padding-box; }
    .pf-modal::-webkit-scrollbar-thumb:hover { background: var(--text-muted); border: 3px solid transparent; background-clip: padding-box; }
    .pf-modal__head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
    .pf-modal__title { font-size: 1.1rem; font-weight: 700; color: var(--text); }
    .pf-modal__close { width: 2rem; height: 2rem; border-radius: 0.5rem; background: var(--bg-2); border: none;
        cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--text-muted);
        transition: all 0.2s; }
    .pf-modal__close:hover { background: var(--border); color: var(--text); }
    .pf-file { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 2rem 1.5rem; border: 2px dashed var(--border); border-radius: var(--radius-lg);
        background: var(--bg-2); cursor: pointer; transition: all 0.25s; text-align: center; }
    .pf-file:hover { border-color: var(--accent); background: color-mix(in srgb, var(--accent) 5%, var(--bg-2)); }
    .pf-file.dragover { border-color: var(--accent); background: color-mix(in srgb, var(--accent) 10%, var(--bg-2)); transform: scale(1.01); }
    .pf-file input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .pf-file__icon { width: 3.5rem; height: 3.5rem; border-radius: 50%;
        background: color-mix(in srgb, var(--accent) 12%, var(--card));
        display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1.2rem;
        margin-bottom: 0.75rem; }
    .pf-file__text { font-size: 0.85rem; color: var(--text); font-weight: 500; }
    .pf-file__hint { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; }
    .pf-file__preview { display: none; margin-top: 1rem; position: relative; }
    .pf-file__preview img { width: 6rem; height: 6rem; border-radius: 1rem; object-fit: cover;
        border: 3px solid var(--border); box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
    .pf-file__preview-name { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.4rem; max-width: 14rem;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    @@media (max-width: 1020px) {
        .pr-hero3d-inner { grid-template-columns: 1fr; }
        .pr-stage { height: 540px; }
        .pr-fc-3 { right: 0; } .pr-fc-1 { right: 0; } .pr-fc-2 { left: 0; }
    }
    @@media (max-width: 640px) {
        .pf-grid-2 { grid-template-columns: 1fr; }
        .pf-stat-grid { grid-template-columns: 1fr 1fr; }
        .pf-port-grid { grid-template-columns: 1fr; }
        .pf-form-row { grid-template-columns: 1fr; }
    }
    /* утончённо: живописная рама отключена, только тонкое тир-кольцо */
    .pf-paint { padding: 0; background: none; border-radius: 50%; box-shadow: none; }
    .pf-paint::before, .pf-paint::after { display: none; }
    .pf-paint .pf-tier { padding: 2px; box-shadow: 0 4px 14px rgba(0,0,0,.28), 0 0 0 1px rgba(255,255,255,.14); }
    .pf-paint .pf-tier img { width: 88px; height: 88px; }
    .pf-tier-1, .pf-tier-2, .pf-tier-3, .pf-tier-4, .pf-tier-5 {
        box-shadow: 0 4px 14px rgba(0,0,0,.28), 0 0 0 1px rgba(255,255,255,.14); }
</style>
@endsection

@section('content')
<div class="pr-page" x-data="{ showAvatarModal: false }">
{{-- ================= HERO 3D: ID CARD ================= --}}
<section class="pr-hero3d" id="prHero">
    <div class="pr-hero3d-bg">
        <div class="pr-aurora"></div>
        <div class="pr-grid3d" data-depth="18"></div>
        <div class="pr-orb pr-orb-1" data-depth="40"></div>
        <div class="pr-orb pr-orb-2" data-depth="-30"></div>
        <div class="pr-orb pr-orb-3" data-depth="60"></div>
    </div>

    <div class="pr-hero3d-inner">
        <div>
            <span class="pr-eyebrow"><i></i>{{ __('player profile') }} • Lv.{{ $user->level }}</span>
            <h1 class="pr-title">{!! e($user->name) !!} @if($user->country)<span style="font-size:.55em" title="{{ country_name($user->country) }}">{!! country_flag($user->country) !!}</span>@endif</h1>
            <p class="pr-sub">{{ $user->title ?? '' }}{{ $user->title && $user->location ? ' • ' : '' }}{{ $user->location ?? '' }}{{ (!$user->title && !$user->location) ? __('New player on CodeMaster') : '' }}</p>

            <div class="pr-links">
                <a href="{{ route('profile.show', $user->id) }}" class="pr-link"><i class="fas fa-eye"></i>{{ __('profile_public_profile') }}</a>
                @if($user->github)<a href="{{ $user->github }}" target="_blank" class="pr-link"><i class="fab fa-github"></i>GitHub</a>@endif
                @if($user->linkedin)<a href="{{ $user->linkedin }}" target="_blank" class="pr-link"><i class="fab fa-linkedin"></i>LinkedIn</a>@endif
                @if($user->website)<a href="{{ $user->website }}" target="_blank" class="pr-link"><i class="fas fa-globe"></i>{{ __('profile_site') }}</a>@endif
            </div>

            <div class="pr-hero-actions">
                <a href="#prBody" class="pr-btn pr-btn-go" id="prToBody"><i class="fas fa-id-card"></i>{{ __('profile_tab_overview') }}</a>
            </div>

            <div class="pr-stats3d">
                <div class="pr-stat"><div class="pr-stat-val" data-count="{{ (int)($stats->completed_courses ?? 0) }}">0</div><div class="pr-stat-label">{{ __('profile_courses_completed') }}</div></div>
                <div class="pr-stat"><div class="pr-stat-val" data-count="{{ (int)($user->certificates_count ?? 0) }}">0</div><div class="pr-stat-label">{{ __('profile_certs_count') }}</div></div>
                <div class="pr-stat"><div class="pr-stat-val" data-count="{{ $user->skills->count() }}">0</div><div class="pr-stat-label">{{ __('profile_skills_count') }}</div></div>
                <div class="pr-stat"><div class="pr-stat-val" data-count="{{ (int)($user->total_xp ?? 0) }}">0</div><div class="pr-stat-label">{{ __('profile_total_xp') }}</div></div>
            </div>
        </div>

        <div class="pr-stage">
            <div class="pr-ring pr-ring-1"><span class="pr-ring-dot"></span></div>
            <div class="pr-ring pr-ring-2"><span class="pr-ring-dot" style="background:#8b5cf6;box-shadow:0 0 14px #8b5cf6"></span></div>
            <div class="pr-cube pr-cube-1" data-depth="70"><i class="fas fa-id-card"></i></div>
            <div class="pr-cube pr-cube-2" data-depth="-60"><i class="fas fa-star"></i></div>

            <div class="pr-id3d" id="prId">
                <div class="pr-id-stripe"></div>
                <div class="pr-id-top">
                    <div class="pr-id-ava">
                        <span class="pf-paint"><span class="pf-tier pf-tier-{{ level_tier($user->level ?? 1) }}"><img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"></span></span>
                        <button @click="showAvatarModal = true" class="pr-id-cam"><i class="fas fa-camera"></i></button>
                    </div>
                    <div>
                        <div class="pr-id-name">{{ $user->name }}</div>
                        <span class="pr-id-lvl" style="background:{{ $user->level_color ?? 'var(--accent)' }}18;color:{{ $user->level_color ?? 'var(--accent)' }}">{!! $user->level_badge ?? '' !!} Lv.{{ $user->level ?? 1 }} {{ $user->level_title ?? '' }}</span>
                    </div>
                </div>
                <div class="pr-id-xp" data-count="{{ (int)($user->total_xp ?? 0) }}">0</div>
                <div class="pr-id-sub">total xp • {{ $user->ai_tokens ?? 0 }} ai tokens</div>
                <div class="pr-id-bar"><div style="--w:{{ $user->level_progress ?? 0 }}%"></div></div>
                <div class="pr-id-meta"><span>LV.{{ $user->level ?? 1 }}</span><span>{{ $user->level_progress ?? 0 }}%</span><span>LV.{{ ($user->level ?? 1) + 1 }}</span></div>
                <div class="pr-id-grid">
                    <div class="pr-mini"><i class="fas fa-book-open" style="background:rgba(34,197,94,.12);color:#22c55e"></i><div><b>{{ $stats->completed_courses ?? 0 }}</b><span>{{ __('done') }}</span></div></div>
                    <div class="pr-mini"><i class="fas fa-certificate" style="background:rgba(234,179,8,.12);color:#eab308"></i><div><b>{{ $user->certificates_count ?? 0 }}</b><span>{{ __('certs') }}</span></div></div>
                    <div class="pr-mini"><i class="fas fa-code" style="background:rgba(139,92,246,.12);color:#8b5cf6"></i><div><b>{{ $user->skills->count() }}</b><span>{{ __('skills') }}</span></div></div>
                    <div class="pr-mini"><i class="fas fa-coins" style="background:rgba(59,130,246,.12);color:#3b82f6"></i><div><b>{{ $user->ai_tokens ?? 0 }}</b><span>{{ __('tokens') }}</span></div></div>
                </div>
            </div>

            <div class="pr-float-chip pr-fc-1" data-depth="50">
                <div class="pr-fc-ico g"><i class="fas fa-trophy"></i></div>
                <div class="pr-fc-txt"><b>{{ number_format($user->total_xp ?? 0) }} XP</b><span>{{ __('and counting') }}</span></div>
            </div>
            <div class="pr-float-chip pr-fc-2" data-depth="-45">
                <div class="pr-fc-ico p"><i class="fas fa-arrow-trend-up"></i></div>
                <div class="pr-fc-txt"><b>Lv.{{ $user->level ?? 1 }}</b><span>{{ $user->level_title ?? '' }}</span></div>
            </div>
            <div class="pr-float-chip pr-fc-3" data-depth="35">
                <div class="pr-fc-ico a"><i class="fas fa-shield-halved"></i></div>
                <div class="pr-fc-txt"><b>{{ __('Verified') }}</b><span>{{ __('CodeMaster player') }}</span></div>
            </div>
        </div>
    </div>

    <div x-show="showAvatarModal" x-transition style="display:none" class="pf-overlay">
        <div class="pf-overlay__bg" @click="showAvatarModal = false"></div>
        <div class="pf-modal" x-data="{ fileName: '', preview: '' }">
            <div class="pf-modal__head">
                <h3 class="pf-modal__title">{{ __('profile_upload_avatar') }}</h3>
                <button @click="showAvatarModal = false" class="pf-modal__close"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="pf-form-group">
                    <div class="pf-file"
                         @dragover.prevent="$el.classList.add('dragover')"
                         @dragleave.prevent="$el.classList.remove('dragover')"
                         @drop.prevent="$el.classList.remove('dragover')">
                        <input type="file" name="avatar" accept="image/*"
                               @change="const f = $event.target.files[0]; if(f){ fileName = f.name; preview = URL.createObjectURL(f); }">
                        <div class="pf-file__icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <p class="pf-file__text">{{ __('profile_click_or_drag') }}</p>
                        <p class="pf-file__hint">PNG, JPG, GIF &bull; {{ __('profile_max_2mb') }}</p>
                        <div class="pf-file__preview" :style="preview ? 'display:block' : ''">
                            <img :src="preview" alt="preview">
                            <p class="pf-file__preview-name" x-text="fileName"></p>
                        </div>
                    </div>
                </div>
                <div class="pf-form-actions">
                    <button type="button" @click="showAvatarModal = false" class="pf-btn pf-btn--ghost">{{ __('profile_cancel') }}</button>
                    <button type="submit" class="pf-btn pf-btn--primary">{{ __('profile_save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="pr-scroll-hint"><div class="pr-mouse"></div><span>{{ __('Scroll — details') }}</span></div>
</section>

{{-- ================= BODY (как было) ================= --}}
<div class="pf-body" id="prBody" x-data="profileApp()">
    <div>
        @if(session('success'))
        <div style="margin-bottom:1.5rem;padding:0.75rem 1rem;background:color-mix(in srgb, var(--success) 10%, var(--card));border:1px solid color-mix(in srgb, var(--success) 30%, var(--card));border-radius:0.75rem;color:var(--success);font-size:0.85rem">{{ session('success') }}</div>
        @endif

        <div class="pf-tabs">
            @foreach(['overview' => __('profile_tab_overview'), 'experience' => __('profile_tab_experience'), 'education' => __('profile_tab_education'), 'portfolio' => __('profile_tab_portfolio'), 'certificates' => __('profile_tab_certificates'), 'activity' => __('profile_tab_activity'), 'settings' => __('profile_tab_settings')] as $key => $label)
            <button @click="activeTab = '{{ $key }}'" :class="activeTab === '{{ $key }}' ? 'active' : ''" class="pf-tab">{{ $label }}</button>
            @endforeach
        </div>

        {{-- OVERVIEW --}}
        <div x-show="activeTab === 'overview'" x-cloak>
            <div class="pf-grid-2">
                <div style="display:flex;flex-direction:column;gap:1.5rem">
                    <div class="pf-card" data-rv>
                        <div class="pf-card__header">
                            <h3 class="pf-card__title"><i class="fas fa-code" style="color:var(--accent);margin-right:0.4rem"></i>{{ __('profile_skills') }}</h3>
                            <button @click="openModal('skill')" class="pf-card__add"><i class="fas fa-plus"></i></button>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:0.5rem">
                            @forelse($user->skills as $skill)
                            <span class="pf-skill">
                                {{ $skill->skill_name }}
                                @if($skill->is_verified)<i class="fas fa-check-circle"></i>@endif
                            </span>
                            @empty
                            <p style="font-size:0.85rem;color:var(--text-muted)">{{ __('profile_no_skills_yet') }}</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="pf-card" data-rv>
                        <div class="pf-card__header">
                            <h3 class="pf-card__title"><i class="fas fa-chart-bar" style="color:var(--accent);margin-right:0.4rem"></i>{{ __('profile_stats') }}</h3>
                        </div>
                        <div class="pf-stat-grid">
                            <div class="pf-stat">
                                <p class="pf-stat__val pf-stat__val--accent">{{ number_format($user->total_xp) }}</p>
                                <p class="pf-stat__label">{{ __('profile_total_xp') }}</p>
                            </div>
                            <div class="pf-stat">
                                <p class="pf-stat__val pf-stat__val--purple">{{ $stats->completed_courses ?? 0 }}</p>
                                <p class="pf-stat__label">{{ __('profile_courses_completed') }}</p>
                            </div>
                            <div class="pf-stat">
                                <p class="pf-stat__val pf-stat__val--yellow">{{ $user->certificates_count ?? 0 }}</p>
                                <p class="pf-stat__label">{{ __('profile_certs_count') }}</p>
                            </div>
                            <div class="pf-stat">
                                <p class="pf-stat__val pf-stat__val--green">{{ $user->skills->count() }}</p>
                                <p class="pf-stat__label">{{ __('profile_skills_count') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:1.5rem">
                    <div class="pf-card" data-rv>
                        <div class="pf-card__header">
                            <h3 class="pf-card__title"><i class="fas fa-briefcase" style="color:var(--accent);margin-right:0.4rem"></i>{{ __('profile_tab_experience') }}</h3>
                            <button @click="openModal('experience')" class="pf-card__add"><i class="fas fa-plus"></i></button>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:0.75rem">
                            @forelse($user->experience as $exp)
                            <div class="pf-item">
                                <div class="pf-item__icon pf-item__icon--accent"><i class="fas fa-building"></i></div>
                                <div class="pf-item__body">
                                    <div class="pf-item__row">
                                        <span class="pf-item__name">{{ $exp->position }}</span>
                                        <form action="{{ route('profile.experience.delete', $exp->id) }}" method="POST" onsubmit="return confirm('{{ __('profile_confirm_delete') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="pf-del"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                    <p class="pf-item__sub">{{ $exp->company }} &bull; {{ $exp->start_date }} - {{ $exp->is_current ? __('profile_now') : ($exp->end_date ?? '') }}</p>
                                    @if($exp->description)<p class="pf-item__desc">{{ $exp->description }}</p>@endif
                                </div>
                            </div>
                            @empty
                            <div class="pf-empty"><i class="fas fa-briefcase"></i>{{ __('profile_experience_empty') }}</div>
                            @endforelse
                        </div>
                    </div>
                    <div class="pf-card" data-rv>
                        <div class="pf-card__header">
                            <h3 class="pf-card__title"><i class="fas fa-graduation-cap" style="color:var(--accent-2);margin-right:0.4rem"></i>{{ __('profile_tab_education') }}</h3>
                            <button @click="openModal('education')" class="pf-card__add"><i class="fas fa-plus"></i></button>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:0.75rem">
                            @forelse($user->education as $edu)
                            <div class="pf-item">
                                <div class="pf-item__icon pf-item__icon--purple"><i class="fas fa-graduation-cap"></i></div>
                                <div class="pf-item__body">
                                    <div class="pf-item__row">
                                        <span class="pf-item__name">{{ $edu->degree }}{{ $edu->field ? ' &mdash; ' . $edu->field : '' }}</span>
                                        <form action="{{ route('profile.education.delete', $edu->id) }}" method="POST" onsubmit="return confirm('{{ __('profile_confirm_delete') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="pf-del"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                    <p class="pf-item__sub">{{ $edu->institution }} &bull; {{ $edu->start_date }} - {{ $edu->end_date ?? __('profile_now') }}</p>
                                </div>
                            </div>
                            @empty
                            <div class="pf-empty"><i class="fas fa-graduation-cap"></i>{{ __('profile_education_empty') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- EXPERIENCE --}}
        <div x-show="activeTab === 'experience'" x-cloak>
            <div style="display:flex;justify-content:flex-end;margin-bottom:1rem">
                <button @click="openModal('experience')" class="pf-btn pf-btn--primary"><i class="fas fa-plus" style="margin-right:0.3rem"></i>{{ __('profile_add_experience') }}</button>
            </div>
            <div style="display:flex;flex-direction:column;gap:1rem">
                @forelse($user->experience as $exp)
                <div class="pf-card in">
                    <div class="pf-item" style="padding:0;background:transparent">
                        <div class="pf-item__icon pf-item__icon--accent"><i class="fas fa-building"></i></div>
                        <div class="pf-item__body">
                            <div class="pf-item__row">
                                <span class="pf-item__name" style="font-size:0.95rem">{{ $exp->position }}</span>
                                <form action="{{ route('profile.experience.delete', $exp->id) }}" method="POST" onsubmit="return confirm('{{ __('profile_confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="pf-del"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                            <p class="pf-item__sub">{{ $exp->company }} &bull; {{ $exp->start_date }} - {{ $exp->is_current ? __('profile_now') : ($exp->end_date ?? '') }}</p>
                            @if($exp->description)<p class="pf-item__desc" style="margin-top:0.5rem">{{ $exp->description }}</p>@endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="pf-card pf-empty in"><i class="fas fa-briefcase"></i>{{ __('profile_experience_empty') }}</div>
                @endforelse
            </div>
        </div>

        {{-- EDUCATION --}}
        <div x-show="activeTab === 'education'" x-cloak>
            <div style="display:flex;justify-content:flex-end;margin-bottom:1rem">
                <button @click="openModal('education')" class="pf-btn pf-btn--primary"><i class="fas fa-plus" style="margin-right:0.3rem"></i>{{ __('profile_add_education') }}</button>
            </div>
            <div style="display:flex;flex-direction:column;gap:1rem">
                @forelse($user->education as $edu)
                <div class="pf-card in">
                    <div class="pf-item" style="padding:0;background:transparent">
                        <div class="pf-item__icon pf-item__icon--purple"><i class="fas fa-graduation-cap"></i></div>
                        <div class="pf-item__body">
                            <div class="pf-item__row">
                                <span class="pf-item__name" style="font-size:0.95rem">{{ $edu->degree }}{{ $edu->field ? ' &mdash; ' . $edu->field : '' }}</span>
                                <form action="{{ route('profile.education.delete', $edu->id) }}" method="POST" onsubmit="return confirm('{{ __('profile_confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="pf-del"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                            <p class="pf-item__sub">{{ $edu->institution }} &bull; {{ $edu->start_date }} - {{ $edu->end_date ?? __('profile_now') }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="pf-card pf-empty in"><i class="fas fa-graduation-cap"></i>{{ __('profile_education_empty') }}</div>
                @endforelse
            </div>
        </div>

        {{-- PORTFOLIO --}}
        <div x-show="activeTab === 'portfolio'" x-cloak>
            <div style="display:flex;justify-content:flex-end;margin-bottom:1rem">
                <button @click="openModal('portfolio')" class="pf-btn pf-btn--primary"><i class="fas fa-plus" style="margin-right:0.3rem"></i>{{ __('profile_add_project') }}</button>
            </div>
            <div class="pf-port-grid">
                @forelse($user->portfolio as $item)
                <div class="pf-port-card">
                    <div class="pf-port-card__img">
                        @if($item->image_url)
                        <img src="{{ asset('storage/' . $item->image_url) }}" alt="{{ $item->title }}">
                        @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center">
                            <i class="fas fa-globe"></i>
                        </div>
                        @endif
                    </div>
                    <div class="pf-port-card__body">
                        <div class="pf-port-card__row">
                            <span class="pf-port-card__title">{{ $item->title }}</span>
                            <form action="{{ route('profile.portfolio.delete', $item->id) }}" method="POST" onsubmit="return confirm('{{ __('profile_confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="pf-del"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                        @if($item->description)<p class="pf-port-card__desc">{{ $item->description }}</p>@endif
                        <div class="pf-port-card__tags">
                            @if($item->category)<span class="pf-port-tag pf-port-tag--cat">{{ $item->category }}</span>@endif
                            @if($item->url)<a href="{{ $item->url }}" target="_blank" class="pf-port-tag pf-port-tag--link"><i class="fas fa-link" style="margin-right:0.2rem"></i>Link</a>@endif
                            @if($item->github_url)<a href="{{ $item->github_url }}" target="_blank" class="pf-port-tag pf-port-tag--gh"><i class="fab fa-github" style="margin-right:0.2rem"></i>GitHub</a>@endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="pf-card pf-empty" style="grid-column:1/-1"><i class="fas fa-folder-open"></i>{{ __('profile_projects_empty') }}</div>
                @endforelse
            </div>
        </div>

        {{-- CERTIFICATES --}}
        <div x-show="activeTab === 'certificates'" x-cloak>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                @forelse($certificates as $cert)
                <a href="{{ route('certificate.show', $cert->cert_hash) }}" class="pf-cert-card">
                    <div class="pf-cert-card__icon"><i class="fas fa-certificate"></i></div>
                    <div>
                        <p class="pf-cert-card__name">{{ $cert->certificate_name }}</p>
                        <p class="pf-cert-card__date">{{ $cert->course?->title ?? '' }} &bull; {{ $cert->issue_date?->format('M Y') ?? '' }}</p>
                    </div>
                </a>
                @empty
                <div class="pf-card pf-empty" style="grid-column:1/-1"><i class="fas fa-certificate"></i>{{ __('profile_certs_empty') }}</div>
                @endforelse
            </div>
        </div>

        {{-- ACTIVITY --}}
        <div x-show="activeTab === 'activity'" x-cloak>
            <div class="pf-card in">
                <div style="display:flex;flex-direction:column;gap:0.25rem">
                    @forelse($recentActivity as $activity)
                    <div class="pf-act-item">
                        <div class="pf-act-dot"><i class="fas fa-check-circle"></i></div>
                        <div>
                            <p class="pf-act-text">{{ $activity->activity_text }}</p>
                            <p class="pf-act-time">{{ $activity->activity_time?->diffForHumans() ?? '' }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="pf-empty"><i class="fas fa-clock"></i>{{ __('profile_no_activity_yet') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- SETTINGS --}}
        <div x-show="activeTab === 'settings'" x-cloak>
            <div style="display:flex;flex-direction:column;gap:1.5rem">
                <div class="pf-card in">
                    <div class="pf-card__header">
                        <h3 class="pf-card__title"><i class="fas fa-user-edit" style="color:var(--accent);margin-right:0.4rem"></i>{{ __('profile_edit_profile') }}</h3>
                    </div>
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="pf-form-row">
                            <div class="pf-form-group">
                                <label class="pf-label">{{ __('profile_name') }}</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="pf-input" required>
                            </div>
                            <div class="pf-form-group">
                                <label class="pf-label">{{ __('profile_position_label') }}</label>
                                <input type="text" name="title" value="{{ old('title', $user->title) }}" class="pf-input" placeholder="Full Stack Developer">
                            </div>
                        </div>
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('profile_location_label') }}</label>
                            <input type="text" name="location" value="{{ old('location', $user->location) }}" class="pf-input" placeholder="{{ __('profile_location_placeholder') }}">
                        </div>
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('profile_about_me') }}</label>
                            <textarea name="bio" rows="3" class="pf-input">{{ old('bio', $user->bio) }}</textarea>
                        </div>
                        <div class="pf-form-row">
                            <div class="pf-form-group">
                                <label class="pf-label"><i class="fab fa-github"></i> GitHub</label>
                                <input type="url" name="github" value="{{ old('github', $user->github) }}" class="pf-input" placeholder="https://github.com/...">
                            </div>
                            <div class="pf-form-group">
                                <label class="pf-label"><i class="fab fa-linkedin"></i> LinkedIn</label>
                                <input type="url" name="linkedin" value="{{ old('linkedin', $user->linkedin) }}" class="pf-input" placeholder="https://linkedin.com/in/...">
                            </div>
                        </div>
                        <div class="pf-form-group">
                            <label class="pf-label"><i class="fas fa-globe"></i> {{ __('profile_site') }}</label>
                            <input type="url" name="website" value="{{ old('website', $user->website) }}" class="pf-input" placeholder="https://...">
                        </div>
                        <div class="pf-form-actions">
                            <button type="submit" class="pf-btn pf-btn--primary">{{ __('profile_save') }}</button>
                        </div>
                    </form>
                </div>

                <div class="pf-card in">
                    <div class="pf-card__header">
                        <h3 class="pf-card__title"><i class="fas fa-lock" style="color:var(--accent);margin-right:0.4rem"></i>{{ __('profile_change_password') }}</h3>
                    </div>
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('profile_current_password') }}</label>
                            <input type="password" name="current_password" class="pf-input" required>
                        </div>
                        <div class="pf-form-row">
                            <div class="pf-form-group">
                                <label class="pf-label">{{ __('profile_new_password') }}</label>
                                <input type="password" name="password" class="pf-input" required>
                            </div>
                            <div class="pf-form-group">
                                <label class="pf-label">{{ __('profile_confirm_password') }}</label>
                                <input type="password" name="password_confirmation" class="pf-input" required>
                            </div>
                        </div>
                        <div class="pf-form-actions">
                            <button type="submit" class="pf-btn pf-btn--primary">{{ __('profile_update_password') }}</button>
                        </div>
                    </form>
                </div>

                <div class="pf-card in">
                    <div class="pf-card__header">
                        <h3 class="pf-card__title"><i class="fas fa-shield-halved" style="color:var(--accent);margin-right:0.4rem"></i>{{ __('profile_two_factor') }}</h3>
                    </div>
                    <div style="padding:0 1.25rem 1.25rem">
                        <p style="font-size:13px;color:var(--text-muted);margin:0 0 1rem">{{ __('profile_two_factor_desc') }}</p>
                        <a href="{{ route('two-factor.show') }}" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;background:var(--accent);color:white;font-size:13px;font-weight:700;text-decoration:none">
                            <i class="fas fa-arrow-right"></i> {{ __('profile_setup_2fa') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="modalType === 'skill'" x-transition style="display:none" class="pf-overlay">
            <div class="pf-overlay__bg" @click="modalType = null"></div>
            <div class="pf-modal">
                <div class="pf-modal__head">
                    <h3 class="pf-modal__title">{{ __('profile_add_skill') }}</h3>
                    <button @click="modalType = null" class="pf-modal__close"><i class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('profile.skill.add') }}" method="POST">
                    @csrf
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('profile_title') }}</label>
                        <input type="text" name="skill_name" class="pf-input" required placeholder="JavaScript">
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('profile_skill_level') }}</label>
                        <select name="skill_level" class="pf-input">
                            <option value="beginner">{{ __('profile_skill_difficulty_beginner') }}</option>
                            <option value="intermediate">{{ __('profile_skill_difficulty_intermediate') }}</option>
                            <option value="advanced">{{ __('profile_skill_difficulty_advanced') }}</option>
                            <option value="expert">{{ __('profile_skill_difficulty_expert') }}</option>
                        </select>
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('profile_skill_category') }}</label>
                        <select name="category" class="pf-input">
                            <option value="technical">{{ __('profile_skill_cat_technical') }}</option>
                            <option value="soft">{{ __('profile_skill_cat_soft') }}</option>
                        </select>
                    </div>
                    <div class="pf-form-actions">
                        <button type="button" @click="modalType = null" class="pf-btn pf-btn--ghost">{{ __('profile_cancel') }}</button>
                        <button type="submit" class="pf-btn pf-btn--primary">{{ __('profile_add') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="modalType === 'experience'" x-transition style="display:none" class="pf-overlay">
            <div class="pf-overlay__bg" @click="modalType = null"></div>
            <div class="pf-modal" style="max-width:32rem">
                <div class="pf-modal__head">
                    <h3 class="pf-modal__title">{{ __('profile_add_experience') }}</h3>
                    <button @click="modalType = null" class="pf-modal__close"><i class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('profile.experience.add') }}" method="POST">
                    @csrf
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('profile_position_label') }}</label>
                        <input type="text" name="position" class="pf-input" required placeholder="Senior Developer">
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('profile_company_label') }}</label>
                        <input type="text" name="company" class="pf-input" required placeholder="TechCorp">
                    </div>
                    <div class="pf-form-row">
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('profile_start_date') }}</label>
                            <input type="text" name="start_date" class="pf-input" required placeholder="2022-01">
                        </div>
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('profile_end_date') }}</label>
                            <input type="text" name="end_date" class="pf-input" placeholder="2024-01">
                        </div>
                    </div>
                    <label class="pf-chk">
                        <input type="checkbox" name="is_current" value="1">
                        <span>{{ __('profile_working_here') }}</span>
                    </label>
                    <div class="pf-form-group" style="margin-top:0.75rem">
                        <label class="pf-label">{{ __('profile_description_label') }}</label>
                        <textarea name="description" rows="3" class="pf-input"></textarea>
                    </div>
                    <div class="pf-form-actions">
                        <button type="button" @click="modalType = null" class="pf-btn pf-btn--ghost">{{ __('profile_cancel') }}</button>
                        <button type="submit" class="pf-btn pf-btn--primary">{{ __('profile_add') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="modalType === 'education'" x-transition style="display:none" class="pf-overlay">
            <div class="pf-overlay__bg" @click="modalType = null"></div>
            <div class="pf-modal" style="max-width:32rem">
                <div class="pf-modal__head">
                    <h3 class="pf-modal__title">{{ __('profile_add_education') }}</h3>
                    <button @click="modalType = null" class="pf-modal__close"><i class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('profile.education.add') }}" method="POST">
                    @csrf
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('profile_institution_label') }}</label>
                        <input type="text" name="institution" class="pf-input" required placeholder="{{ __('profile_institution_placeholder') }}">
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('profile_degree_label') }}</label>
                        <input type="text" name="degree" class="pf-input" required placeholder="{{ __('profile_degree_placeholder') }}">
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('profile_specialization') }}</label>
                        <input type="text" name="field" class="pf-input" placeholder="{{ __('profile_specialization_placeholder') }}">
                    </div>
                    <div class="pf-form-row">
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('profile_start_date') }}</label>
                            <input type="text" name="start_date" class="pf-input" required placeholder="2018-09">
                        </div>
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('profile_end_date') }}</label>
                            <input type="text" name="end_date" class="pf-input" placeholder="2022-06">
                        </div>
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('profile_description_label') }}</label>
                        <textarea name="description" rows="3" class="pf-input"></textarea>
                    </div>
                    <div class="pf-form-actions">
                        <button type="button" @click="modalType = null" class="pf-btn pf-btn--ghost">{{ __('profile_cancel') }}</button>
                        <button type="submit" class="pf-btn pf-btn--primary">{{ __('profile_add') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="modalType === 'portfolio'" x-transition style="display:none" class="pf-overlay">
            <div class="pf-overlay__bg" @click="modalType = null"></div>
            <div class="pf-modal" style="max-width:32rem">
                <div class="pf-modal__head">
                    <h3 class="pf-modal__title">{{ __('profile_add_project') }}</h3>
                    <button @click="modalType = null" class="pf-modal__close"><i class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('profile.portfolio.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('profile_title') }}</label>
                        <input type="text" name="title" class="pf-input" required placeholder="E-Commerce Platform">
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('profile_description_label') }}</label>
                        <textarea name="description" rows="3" class="pf-input"></textarea>
                    </div>
                    <div class="pf-form-row">
                        <div class="pf-form-group">
                            <label class="pf-label">URL</label>
                            <input type="url" name="url" class="pf-input" placeholder="https://...">
                        </div>
                        <div class="pf-form-group">
                            <label class="pf-label">GitHub URL</label>
                            <input type="url" name="github_url" class="pf-input" placeholder="https://github.com/...">
                        </div>
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('profile_skill_category') }}</label>
                        <input type="text" name="category" class="pf-input" placeholder="Web App, Mobile...">
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('profile_image') }}</label>
                        <div class="pf-file"
                             @dragover.prevent="$el.classList.add('dragover')"
                             @dragleave.prevent="$el.classList.remove('dragover')"
                             @drop.prevent="$el.classList.remove('dragover')"
                             x-data="{ fileName: '', preview: '' }">
                            <input type="file" name="image" accept="image/*"
                                   @change="const f = $event.target.files[0]; if(f){ fileName = f.name; preview = URL.createObjectURL(f); }">
                            <div class="pf-file__icon"><i class="fas fa-image"></i></div>
                            <p class="pf-file__text">{{ __('profile_click_or_drag') }}</p>
                            <p class="pf-file__hint">PNG, JPG, GIF &bull; {{ __('profile_max_5mb') }}</p>
                            <div class="pf-file__preview" :style="preview ? 'display:block' : ''">
                                <img :src="preview" alt="preview">
                                <p class="pf-file__preview-name" x-text="fileName"></p>
                            </div>
                        </div>
                    </div>
                    <div class="pf-form-actions">
                        <button type="button" @click="modalType = null" class="pf-btn pf-btn--ghost">{{ __('profile_cancel') }}</button>
                        <button type="submit" class="pf-btn pf-btn--primary">{{ __('profile_add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    /* --- Hero mouse 3D parallax --- */
    var hero = document.getElementById('prHero');
    var idcard = document.getElementById('prId');
    var layers = document.querySelectorAll('#prHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0, heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('pr-paused', !heroVisible);
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
                if (idcard) idcard.style.transform = 'rotateX(' + (6 + rx * -1).toFixed(2) + 'deg) rotateY(' + (-10 + ry).toFixed(2) + 'deg)';
            }
            requestAnimationFrame(tilt);
        })();
    }

    /* --- Animated counters --- */
    document.querySelectorAll('[data-count]').forEach(function(el) {
        var target = parseInt(el.dataset.count || 0, 10), t0 = null;
        function step(t) {
            if (!t0) t0 = t;
            var p = Math.min(1, (t - t0) / 1500);
            var e = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.round(target * e).toLocaleString('en-US');
            if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    });

    /* --- Cards reveal --- */
    var cards = document.querySelectorAll('.pf-card[data-rv], .pf-card:not(.in)');
    var targets = document.querySelectorAll('[data-rv], .pf-grid-2 .pf-card, .pf-port-grid, #prBody .pf-card');
    if ('IntersectionObserver' in window && targets.length) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(en) {
                if (en.isIntersecting) {
                    en.target.classList.add('in');
                    io.unobserve(en.target);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
        targets.forEach(function(x) { io.observe(x); });
        setTimeout(function() { targets.forEach(function(x) { x.classList.add('in'); }); }, 4000);
    } else {
        targets.forEach(function(x) { x.classList.add('in'); });
    }

    /* --- Scroll to body --- */
    var toBody = document.getElementById('prToBody');
    if (toBody) toBody.addEventListener('click', function(e) {
        e.preventDefault();
        var t = document.getElementById('prBody');
        if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();

function profileApp() {
    return {
        activeTab: 'overview',
        modalType: null,
        openModal(type) {
            this.modalType = type;
        }
    };
}
</script>
@endsection
