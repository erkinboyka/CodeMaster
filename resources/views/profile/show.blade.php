@extends('layouts.app')

@section('title', ($user->name ?? 'User') . ' - ' . __('section_profile') . ' - CodeMaster')

@section('head')
<style>
    /* ============ PUBLIC PROFILE: ID-CARD THEME + 3D HERO ============ */
    .ps-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .ps-hero3d { position: relative; min-height: 100vh; display: flex; align-items: center;
        overflow: hidden; padding: 110px clamp(20px,4vw,56px) 90px; isolation: isolate; perspective: 1600px; }
    .ps-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .ps-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(139,92,246,.14) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(236,72,153,.10) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(56,189,248,.10) 0%, transparent 60%);
        animation: psAurora 22s ease-in-out infinite alternate; }
    @@keyframes psAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); } }
    .ps-grid3d { position: absolute; inset: -50%;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px; opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2); transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%); }
    .ps-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .ps-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: psOrb1 16s ease-in-out infinite; }
    .ps-orb-2 { width: 460px; height: 460px; background: #8b5cf6; opacity: .10; bottom: -18%; right: -6%; animation: psOrb2 20s ease-in-out infinite; }
    .ps-orb-3 { width: 260px; height: 260px; background: #38bdf8; opacity: .10; top: 55%; left: 42%; animation: psOrb3 12s ease-in-out infinite; }
    @@keyframes psOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes psOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes psOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }
    @@keyframes psBlink { 0%,100%{opacity:1} 50%{opacity:.35} }

    .ps-hero3d-inner { position: relative; z-index: 2; width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px,5vw,72px); align-items: center; }
    .ps-eyebrow { display: inline-flex; align-items: center; gap: 10px; padding: 8px 18px; border-radius: 100px;
        background: var(--accent-glow); border: 1px solid var(--accent-glow-strong);
        font-size: 11px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase; color: var(--accent); margin-bottom: 22px; }
    .ps-eyebrow i { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: psBlink 1.6s infinite;
        box-shadow: 0 0 10px #22c55e; }
    .ps-title { font-size: clamp(40px,5.6vw,78px); font-weight: 900; line-height: .98; letter-spacing: -2.5px;
        margin: 0 0 14px; color: var(--text); overflow-wrap: anywhere; }
    .ps-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.7; max-width: 540px;
        margin-bottom: 22px; }
    .ps-bio { font-size: 14px; color: var(--text-secondary); line-height: 1.7; max-width: 540px; margin-bottom: 22px;
        padding: 14px 18px; border-radius: 14px; background: color-mix(in srgb, var(--card) 80%, transparent);
        border: 1px solid var(--border); border-left: 3px solid var(--accent); }
    .ps-links { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 24px; }
    .ps-link { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 12px;
        background: var(--card); border: 1px solid var(--border); color: var(--text-secondary);
        font-size: 13px; font-weight: 700; text-decoration: none; transition: all .25s; }
    .ps-link:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-2px); }
    .ps-stats3d { display: flex; gap: clamp(20px,3.5vw,44px); flex-wrap: wrap; }
    .ps-stat { position: relative; }
    .ps-stat + .ps-stat::before { content:''; position: absolute; left: -22px; top: 50%; transform: translateY(-50%);
        width: 1px; height: 38px; background: var(--border); }
    .ps-stat-val { font-size: clamp(26px,3vw,38px); font-weight: 900; line-height: 1; font-variant-numeric: tabular-nums;
        background: linear-gradient(135deg,var(--text),var(--accent)); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; }
    .ps-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px;
        margin-top: 6px; font-weight: 600; }

    /* --- 3D ID-card stage --- */
    .ps-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .ps-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .ps-ring-1 { width: 480px; height: 480px; animation: psSpin 26s linear infinite; opacity: .7; }
    .ps-ring-2 { width: 590px; height: 590px; animation: psSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes psSpin { to { transform: rotate(360deg); } }
    @@keyframes psSpinRev { to { transform: rotate(-360deg); } }
    .ps-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: var(--accent);
        box-shadow: 0 0 14px var(--accent); top: -5px; left: 50%; }
    .ps-id3d { position: relative; width: 100%; max-width: 420px; padding: 26px; overflow: hidden;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid var(--border); border-radius: 24px;
        box-shadow: 0 40px 100px rgba(0,0,0,.35), 0 0 80px var(--accent-glow);
        transform-style: preserve-3d; transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear; z-index: 3; }
    .ps-id3d::after { content:''; position: absolute; inset: 0; border-radius: 24px; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: psSheen 6s ease-in-out infinite; }
    @@keyframes psSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .ps-id-stripe { position: absolute; top: 0; left: 0; right: 0; height: 6px;
        background: linear-gradient(90deg,var(--accent),#8b5cf6,#38bdf8); }
    .ps-id-top { display: flex; align-items: center; gap: 16px; margin: 8px 0 18px; transform: translateZ(40px); }
    /* живописная рама: золото, резьба-волна, угловые гвозди, фаска */
    .pf-paint { position: relative; display: inline-block; padding: 13px; border-radius: 24px; flex-shrink: 0;
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
    .ps-id-name { font-size: 19px; font-weight: 900; color: var(--text); line-height: 1.2; overflow-wrap: anywhere; }
    .ps-id-lvl { display: inline-flex; align-items: center; gap: 6px; margin-top: 7px; padding: 4px 11px;
        border-radius: 9px; font-size: 11px; font-weight: 800; }
    .ps-id-xp { font-family: var(--font-mono); font-size: 28px; font-weight: 800;
        background: linear-gradient(135deg,var(--accent),#8b5cf6); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; font-variant-numeric: tabular-nums;
        transform: translateZ(30px); }
    .ps-id-sub { font-size: 10px; color: var(--text-muted); font-family: var(--font-mono); letter-spacing: 1.5px;
        text-transform: uppercase; margin-bottom: 10px; }
    .ps-id-bar { height: 10px; border-radius: 5px; background: var(--border); overflow: hidden; margin-bottom: 8px;
        transform: translateZ(25px); }
    .ps-id-bar div { height: 100%; width: 0; border-radius: 5px; background: linear-gradient(90deg,var(--accent),#8b5cf6,#22c55e);
        box-shadow: 0 0 12px var(--accent-glow-strong); animation: psXp 2.2s cubic-bezier(.16,1,.3,1) .4s forwards; }
    @@keyframes psXp { to { width: var(--w, 50%); } }
    .ps-id-meta { display: flex; justify-content: space-between; font-size: 10px; color: var(--text-muted);
        font-family: var(--font-mono); margin-bottom: 18px; }
    .ps-id-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; transform: translateZ(20px); }
    .ps-mini { border-radius: 14px; background: var(--bg-secondary); border: 1px solid var(--border);
        padding: 12px 14px; display: flex; align-items: center; gap: 10px; }
    .ps-mini i { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-size: 14px; flex-shrink: 0; }
    .ps-mini b { display: block; font-size: 15px; font-weight: 900; color: var(--text); font-variant-numeric: tabular-nums; }
    .ps-mini span { font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: .8px; font-weight: 700; }
    .ps-float-chip { position: absolute; z-index: 4; display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent); backdrop-filter: blur(18px);
        border: 1px solid var(--border); border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3); font-size: 13px; animation: psFloatY 4.5s ease-in-out infinite; }
    @@keyframes psFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .ps-fc-1 { top: 4%; right: -6px; } .ps-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .ps-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .ps-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0; }
    .ps-fc-ico.g { background: rgba(234,179,8,.14); color: #eab308; }
    .ps-fc-ico.p { background: rgba(139,92,246,.14); color: #8b5cf6; }
    .ps-fc-ico.a { background: rgba(34,197,94,.14); color: #22c55e; }
    .ps-fc-txt b { display: block; font-size: 13px; color: var(--text); font-family: var(--font-mono); }
    .ps-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .ps-cube { position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px; display: flex;
        align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        animation: psCubeFloat 6s ease-in-out infinite; }
    .ps-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,var(--accent),#8b5cf6); }
    .ps-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#eab308,#b45309); animation-delay: 1.5s; }
    @@keyframes psCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .ps-scroll-hint { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);
        font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-family: var(--font-mono); }
    .ps-paused .ps-aurora, .ps-paused .ps-orb, .ps-paused .ps-ring, .ps-paused .ps-cube,
    .ps-paused .ps-float-chip { animation-play-state: paused !important; }
    .ps-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .ps-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px;
        height: 8px; border-radius: 4px; background: var(--accent); animation: psWheel 1.8s ease-in-out infinite; }
    @@keyframes psWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ BODY ============ */
    .ps-body { max-width: 1120px; margin: 0 auto; padding: 4rem clamp(20px,4vw,48px); }
    .ps-grid { display: grid; grid-template-columns: minmax(0,1.6fr) minmax(0,1fr); gap: 22px; align-items: start; }
    .ps-col { display: flex; flex-direction: column; gap: 18px; min-width: 0; }
    .ps-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; padding: 22px;
        opacity: 0; transform: translateY(24px);
        transition: opacity .5s ease, transform .5s cubic-bezier(.16,1,.3,1), border-color .25s; }
    .ps-card.in { opacity: 1; transform: none; }
    .ps-card__title { font-size: 15px; font-weight: 800; color: var(--text); margin: 0 0 16px;
        display: flex; align-items: center; gap: 9px; }
    .ps-card__title i { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;
        border-radius: 10px; background: var(--accent-glow); color: var(--accent); font-size: 13px; }
    .ps-bio { font-size: 14px; color: var(--text-secondary); line-height: 1.7; margin: 0; }
    .ps-item { display: flex; gap: 14px; padding: 14px; background: var(--bg-secondary); border-radius: 14px;
        transition: background 0.2s; margin-bottom: 10px; }
    .ps-item:last-child { margin-bottom: 0; }
    .ps-item:hover { background: color-mix(in srgb, var(--accent) 5%, var(--bg-secondary)); }
    .ps-item__icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center;
        justify-content: center; flex-shrink: 0; font-size: 15px; }
    .ps-item__name { font-size: 14px; font-weight: 700; color: var(--text); }
    .ps-item__sub { font-size: 12px; color: var(--text-muted); margin-top: 3px; }
    .ps-item__desc { font-size: 12.5px; color: var(--text-secondary); margin-top: 6px; line-height: 1.55; }
    .ps-skill { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 100px;
        background: color-mix(in srgb, var(--accent) 12%, transparent); border: 1px solid color-mix(in srgb, var(--accent) 25%, transparent);
        color: var(--accent); font-size: 12.5px; font-weight: 700; }
    .ps-skill i { font-size: 10px; color: #22c55e; }
    .ps-stat-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 2px;
        font-size: 13.5px; }
    .ps-stat-row + .ps-stat-row { border-top: 1px solid var(--border); }
    .ps-stat-row span:first-child { color: var(--text-muted); }
    .ps-stat-row b { font-family: var(--font-mono); }
    .ps-bar { height: 9px; border-radius: 5px; background: var(--border); overflow: hidden; margin-top: 14px; }
    .ps-bar div { height: 100%; width: 0; border-radius: 5px; background: linear-gradient(90deg,var(--accent),#8b5cf6);
        transition: width 1.2s cubic-bezier(.16,1,.3,1); }
    .ps-bar-meta { display: flex; justify-content: space-between; font-size: 10.5px; color: var(--text-muted);
        font-family: var(--font-mono); margin-top: 6px; }
    .ps-port-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .ps-port-card { border: 1px solid var(--border); border-radius: 16px; overflow: hidden; transition: all .3s; }
    .ps-port-card:hover { transform: translateY(-3px); box-shadow: 0 14px 34px rgba(0,0,0,.14); }
    .ps-port-card__img { height: 128px; overflow: hidden; background: linear-gradient(135deg, var(--accent), var(--accent-2)); }
    .ps-port-card__img img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
    .ps-port-card:hover .ps-port-card__img img { transform: scale(1.06); }
    .ps-port-card__body { padding: 14px; }
    .ps-port-card__title { font-size: 13.5px; font-weight: 800; color: var(--text); }
    .ps-port-card__desc { font-size: 12px; color: var(--text-muted); margin-top: 4px; line-height: 1.55; }
    .ps-port-card__tags { display: flex; gap: 6px; margin-top: 9px; flex-wrap: wrap; }
    .ps-port-tag { padding: 4px 10px; border-radius: 100px; font-size: 10.5px; font-weight: 700; text-decoration: none; }
    .ps-cert { display: flex; align-items: center; gap: 13px; padding: 12px; background: var(--bg-secondary);
        border-radius: 14px; text-decoration: none; transition: all .2s; margin-bottom: 8px; }
    .ps-cert:last-child { margin-bottom: 0; }
    .ps-cert:hover { background: color-mix(in srgb, var(--accent) 6%, var(--bg-secondary)); transform: translateX(3px); }
    .ps-cert__icon { width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
        background: linear-gradient(135deg, #f59e0b, #f97316);
        display: flex; align-items: center; justify-content: center; color: #fff; font-size: 15px; }
    .ps-cert__name { font-size: 13px; font-weight: 700; color: var(--text); }
    .ps-cert__date { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
    .ps-act { display: flex; gap: 11px; padding: 9px 2px; align-items: flex-start; }
    .ps-act + .ps-act { border-top: 1px solid var(--border); }
    .ps-act-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; background: var(--accent);
        margin-top: 5px; box-shadow: 0 0 8px var(--accent-glow-strong); }
    .ps-act-text { font-size: 13px; color: var(--text); line-height: 1.5; }
    .ps-act-time { font-size: 11px; color: var(--text-muted); margin-top: 2px; font-family: var(--font-mono); }
    @@media (max-width: 1020px) {
        .ps-hero3d-inner { grid-template-columns: 1fr; }
        .ps-stage { height: 560px; }
        .ps-fc-3 { right: 0; } .ps-fc-1 { right: 0; } .ps-fc-2 { left: 0; }
        .ps-grid { grid-template-columns: 1fr; }
    }
    @@media (max-width: 640px) { .ps-port-grid { grid-template-columns: 1fr; } }
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
<div class="ps-page">
{{-- ================= HERO 3D: ID CARD ================= --}}
<section class="ps-hero3d" id="psHero">
    <div class="ps-hero3d-bg">
        <div class="ps-aurora"></div>
        <div class="ps-grid3d" data-depth="18"></div>
        <div class="ps-orb ps-orb-1" data-depth="40"></div>
        <div class="ps-orb ps-orb-2" data-depth="-30"></div>
        <div class="ps-orb ps-orb-3" data-depth="60"></div>
    </div>

    <div class="ps-hero3d-inner">
        <div>
            <span class="ps-eyebrow"><i></i>{{ __('player card') }} • Lv.{{ $user->level }}</span>
            <h1 class="ps-title">{!! e($user->name) !!} @if($user->country)<span style="font-size:.55em" title="{{ country_name($user->country) }}">{!! country_flag($user->country) !!}</span>@endif</h1>
            <p class="ps-sub">{{ $user->title ?? '' }}{{ $user->title && $user->location ? ' • ' : '' }}{{ $user->location ?? '' }}{{ (!$user->title && !$user->location) ? __('CodeMaster player') : '' }}</p>

            <div class="ps-links">
                @if($user->github)<a href="{{ $user->github }}" target="_blank" class="ps-link"><i class="fab fa-github"></i>GitHub</a>@endif
                @if($user->linkedin)<a href="{{ $user->linkedin }}" target="_blank" class="ps-link"><i class="fab fa-linkedin"></i>LinkedIn</a>@endif
                @if($user->website)<a href="{{ $user->website }}" target="_blank" class="ps-link"><i class="fas fa-globe"></i>{{ __('profile_website') }}</a>@endif
            </div>

            <div class="ps-stats3d">
                <div class="ps-stat"><div class="ps-stat-val" data-count="{{ (int)($stats->completed_courses ?? 0) }}">0</div><div class="ps-stat-label">{{ __('profile_completed_courses') }}</div></div>
                <div class="ps-stat"><div class="ps-stat-val" data-count="{{ (int)($user->certificates_count ?? 0) }}">0</div><div class="ps-stat-label">{{ __('profile_certs') }}</div></div>
                <div class="ps-stat"><div class="ps-stat-val" data-count="{{ $user->skills->count() }}">0</div><div class="ps-stat-label">{{ __('profile_skills') }}</div></div>
                <div class="ps-stat"><div class="ps-stat-val" data-count="{{ (int)($user->total_xp ?? 0) }}">0</div><div class="ps-stat-label">{{ __('profile_total_xp') }}</div></div>
            </div>
        </div>

        <div class="ps-stage">
            <div class="ps-ring ps-ring-1"><span class="ps-ring-dot"></span></div>
            <div class="ps-ring ps-ring-2"><span class="ps-ring-dot" style="background:#8b5cf6;box-shadow:0 0 14px #8b5cf6"></span></div>
            <div class="ps-cube ps-cube-1" data-depth="70"><i class="fas fa-id-card"></i></div>
            <div class="ps-cube ps-cube-2" data-depth="-60"><i class="fas fa-star"></i></div>

            <div class="ps-id3d" id="psId">
                <div class="ps-id-stripe"></div>
                <div class="ps-id-top">
                    <span class="pf-paint"><span class="pf-tier pf-tier-{{ level_tier($user->level ?? 1) }}"><img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"></span></span>
                    <div>
                        <div class="ps-id-name">{{ $user->name }}</div>
                        <span class="ps-id-lvl" style="background:{{ $user->level_color ?? 'var(--accent)' }}18;color:{{ $user->level_color ?? 'var(--accent)' }}">{!! $user->level_badge ?? '' !!} Lv.{{ $user->level ?? 1 }} {{ $user->level_title ?? '' }}</span>
                    </div>
                </div>
                <div class="ps-id-xp" data-count="{{ (int)($user->total_xp ?? 0) }}">0</div>
                <div class="ps-id-sub">total xp</div>
                <div class="ps-id-bar"><div style="--w:{{ $user->level_progress ?? 0 }}%"></div></div>
                <div class="ps-id-meta"><span>LV.{{ $user->level ?? 1 }}</span><span>{{ $user->level_progress ?? 0 }}%</span><span>LV.{{ ($user->level ?? 1) + 1 }}</span></div>
                <div class="ps-id-grid">
                    <div class="ps-mini"><i class="fas fa-book-open" style="background:rgba(34,197,94,.12);color:#22c55e"></i><div><b>{{ $stats->completed_courses ?? 0 }}</b><span>{{ __('done') }}</span></div></div>
                    <div class="ps-mini"><i class="fas fa-certificate" style="background:rgba(234,179,8,.12);color:#eab308"></i><div><b>{{ $user->certificates_count ?? 0 }}</b><span>{{ __('certs') }}</span></div></div>
                    <div class="ps-mini"><i class="fas fa-code" style="background:rgba(139,92,246,.12);color:#8b5cf6"></i><div><b>{{ $user->skills->count() }}</b><span>{{ __('skills') }}</span></div></div>
                    <div class="ps-mini"><i class="fas fa-bolt" style="background:rgba(59,130,246,.12);color:#3b82f6"></i><div><b>{{ $user->level ?? 1 }}</b><span>{{ __('level') }}</span></div></div>
                </div>
            </div>

            <div class="ps-float-chip ps-fc-1" data-depth="50">
                <div class="ps-fc-ico g"><i class="fas fa-trophy"></i></div>
                <div class="ps-fc-txt"><b>{{ number_format($user->total_xp ?? 0) }} XP</b><span>{{ __('and counting') }}</span></div>
            </div>
            <div class="ps-float-chip ps-fc-2" data-depth="-45">
                <div class="ps-fc-ico p"><i class="fas fa-arrow-trend-up"></i></div>
                <div class="ps-fc-txt"><b>Lv.{{ $user->level ?? 1 }}</b><span>{{ $user->level_title ?? '' }}</span></div>
            </div>
            <div class="ps-float-chip ps-fc-3" data-depth="35">
                <div class="ps-fc-ico a"><i class="fas fa-certificate"></i></div>
                <div class="ps-fc-txt"><b>{{ $user->certificates_count ?? 0 }}</b><span>{{ __('certificates earned') }}</span></div>
            </div>
        </div>
    </div>

    <div class="ps-scroll-hint"><div class="ps-mouse"></div><span>{{ __('Scroll — details') }}</span></div>
</section>

{{-- ================= BODY ================= --}}
<div class="ps-body" id="psBody">
    <div class="ps-grid">
        <div class="ps-col">
            @if($user->bio)
            <div class="ps-card" data-rv>
                <h3 class="ps-card__title"><i class="fas fa-user"></i>{{ __('profile_about') }}</h3>
                <p class="ps-bio">{{ $user->bio }}</p>
            </div>
            @endif

            @if($user->experience->count())
            <div class="ps-card" data-rv>
                <h3 class="ps-card__title"><i class="fas fa-briefcase"></i>{{ __('profile_experience_work') }}</h3>
                @foreach($user->experience as $exp)
                <div class="ps-item">
                    <div class="ps-item__icon" style="background:color-mix(in srgb, var(--accent) 12%, transparent);color:var(--accent)"><i class="fas fa-building"></i></div>
                    <div>
                        <div class="ps-item__name">{{ $exp->position }}</div>
                        <div class="ps-item__sub">{{ $exp->company }} • {{ $exp->start_date }} - {{ $exp->is_current ? __('profile_now') : ($exp->end_date ?? '') }}</div>
                        @if($exp->description)<p class="ps-item__desc">{{ $exp->description }}</p>@endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($user->education->count())
            <div class="ps-card" data-rv>
                <h3 class="ps-card__title"><i class="fas fa-graduation-cap"></i>{{ __('profile_tab_education') }}</h3>
                @foreach($user->education as $edu)
                <div class="ps-item">
                    <div class="ps-item__icon" style="background:rgba(139,92,246,.12);color:#8b5cf6"><i class="fas fa-graduation-cap"></i></div>
                    <div>
                        <div class="ps-item__name">{{ $edu->degree }}{{ $edu->field ? ' — ' . $edu->field : '' }}</div>
                        <div class="ps-item__sub">{{ $edu->institution }} • {{ $edu->start_date }} - {{ $edu->end_date ?? __('profile_now') }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($user->portfolio->count())
            <div class="ps-card" data-rv>
                <h3 class="ps-card__title"><i class="fas fa-folder-open"></i>{{ __('profile_tab_portfolio') }}</h3>
                <div class="ps-port-grid">
                    @foreach($user->portfolio as $item)
                    <div class="ps-port-card">
                        <div class="ps-port-card__img">
                            @if($item->image_url)
                            <img src="{{ asset('storage/' . $item->image_url) }}" alt="{{ $item->title }}">
                            @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center">
                                <i class="fas fa-globe" style="font-size:2rem;color:rgba(255,255,255,0.25)"></i>
                            </div>
                            @endif
                        </div>
                        <div class="ps-port-card__body">
                            <div class="ps-port-card__title">{{ $item->title }}</div>
                            @if($item->description)<p class="ps-port-card__desc">{{ $item->description }}</p>@endif
                            <div class="ps-port-card__tags">
                                @if($item->category)<span class="ps-port-tag" style="background:color-mix(in srgb, var(--accent) 12%, transparent);color:var(--accent)">{{ $item->category }}</span>@endif
                                @if($item->url)<a href="{{ $item->url }}" target="_blank" class="ps-port-tag" style="background:rgba(59,130,246,.12);color:#3b82f6"><i class="fas fa-link" style="margin-right:4px"></i>Link</a>@endif
                                @if($item->github_url)<a href="{{ $item->github_url }}" target="_blank" class="ps-port-tag" style="background:var(--bg-secondary);color:var(--text-muted)"><i class="fab fa-github" style="margin-right:4px"></i>GitHub</a>@endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($certificates->count())
            <div class="ps-card" data-rv>
                <h3 class="ps-card__title"><i class="fas fa-certificate" style="background:rgba(234,179,8,.12);color:#eab308"></i>{{ __('profile_tab_certificates') }}</h3>
                @foreach($certificates as $cert)
                <a href="{{ route('certificate.show', $cert->cert_hash) }}" class="ps-cert">
                    <div class="ps-cert__icon"><i class="fas fa-certificate"></i></div>
                    <div>
                        <p class="ps-cert__name">{{ $cert->certificate_name }}</p>
                        <p class="ps-cert__date">{{ $cert->issue_date?->format('M Y') ?? '' }}</p>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        <div class="ps-col">
            @if($user->skills->count())
            <div class="ps-card" data-rv>
                <h3 class="ps-card__title"><i class="fas fa-code"></i>{{ __('profile_skills') }}</h3>
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    @foreach($user->skills as $skill)
                    <span class="ps-skill">
                        {{ $skill->skill_name }}
                        @if($skill->is_verified)<i class="fas fa-check-circle"></i>@endif
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="ps-card" data-rv>
                <h3 class="ps-card__title"><i class="fas fa-chart-bar"></i>{{ __('profile_stats') }}</h3>
                <div class="ps-stat-row"><span>{{ __('profile_total_xp') }}</span><b style="color:var(--accent)">{{ number_format($user->total_xp) }}</b></div>
                <div class="ps-stat-row"><span>{{ __('profile_level') }}</span><b style="color:#8b5cf6">{!! $user->level_badge !!} {{ $user->level_title }} ({{ $user->level }})</b></div>
                <div class="ps-stat-row"><span>{{ __('profile_completed_courses') }}</span><b style="color:#22c55e">{{ $stats->completed_courses ?? 0 }}</b></div>
                <div class="ps-stat-row"><span>{{ __('profile_certs') }}</span><b style="color:#eab308">{{ $user->certificates_count ?? 0 }}</b></div>
                <div class="ps-bar"><div data-w="{{ $user->level_progress ?? 0 }}"></div></div>
                <div class="ps-bar-meta"><span>Lv.{{ $user->level }}</span><span>{{ $user->xp_for_current_level }}/{{ $user->xp_for_next_level }} XP</span><span>Lv.{{ $user->level + 1 }}</span></div>
            </div>

            @if($recentActivity->count())
            <div class="ps-card" data-rv>
                <h3 class="ps-card__title"><i class="fas fa-clock"></i>{{ __('profile_tab_activity') }}</h3>
                @foreach($recentActivity->take(5) as $activity)
                <div class="ps-act">
                    <div class="ps-act-dot"></div>
                    <div>
                        <p class="ps-act-text">{{ $activity->activity_text }}</p>
                        <p class="ps-act-time">{{ $activity->activity_time?->diffForHumans() ?? '' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
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
    var hero = document.getElementById('psHero');
    var idcard = document.getElementById('psId');
    var layers = document.querySelectorAll('#psHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0, heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('ps-paused', !heroVisible);
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

    /* --- Cards reveal + bars --- */
    function fillBars(scope) {
        (scope || document).querySelectorAll('.ps-bar div[data-w]').forEach(function(b) {
            b.style.width = b.dataset.w + '%';
        });
    }
    var cards = document.querySelectorAll('[data-rv]');
    if ('IntersectionObserver' in window && cards.length) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(en) {
                if (en.isIntersecting) {
                    en.target.classList.add('in');
                    setTimeout(function() { fillBars(en.target); }, 250);
                    io.unobserve(en.target);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
        cards.forEach(function(x) { io.observe(x); });
        setTimeout(function() { cards.forEach(function(x) { x.classList.add('in'); }); fillBars(document); }, 4000);
    } else {
        cards.forEach(function(x) { x.classList.add('in'); }); fillBars(document);
    }
})();
</script>
@endsection
