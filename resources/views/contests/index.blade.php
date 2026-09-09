@extends('layouts.app')

@section('title', __('Contests') . ' - CodeMaster')

@section('head')
<style>
    /* ============ CONTESTS: ARENA THEME + 3D HERO ============ */
    .ct-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .ct-hero3d { position: relative; min-height: 100vh; display: flex; align-items: center;
        overflow: hidden; padding: 110px clamp(20px,4vw,56px) 90px; isolation: isolate; perspective: 1600px; }
    .ct-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .ct-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(245,158,11,.12) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(139,92,246,.12) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(239,68,68,.08) 0%, transparent 60%);
        animation: ctAurora 22s ease-in-out infinite alternate; }
    @@keyframes ctAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); } }
    .ct-grid3d { position: absolute; inset: -50%;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px; opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2); transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%); }
    .ct-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .ct-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: ctOrb1 16s ease-in-out infinite; }
    .ct-orb-2 { width: 460px; height: 460px; background: #f59e0b; opacity: .09; bottom: -18%; right: -6%; animation: ctOrb2 20s ease-in-out infinite; }
    .ct-orb-3 { width: 260px; height: 260px; background: #8b5cf6; opacity: .10; top: 55%; left: 42%; animation: ctOrb3 12s ease-in-out infinite; }
    @@keyframes ctOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes ctOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes ctOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }
    @@keyframes ctBlink { 0%,100%{opacity:1} 50%{opacity:.35} }

    .ct-hero3d-inner { position: relative; z-index: 2; width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px,5vw,72px); align-items: center; }
    .ct-live-badge { display: inline-flex; align-items: center; gap: 9px; padding: 8px 18px; border-radius: 100px;
        background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3);
        font-size: 11px; font-weight: 800; letter-spacing: 1.8px; text-transform: uppercase; color: #f87171; margin-bottom: 22px; }
    .ct-live-badge i { width: 8px; height: 8px; border-radius: 50%; background: #ef4444; animation: ctBlink 1.2s infinite;
        box-shadow: 0 0 12px #ef4444; }
    .ct-title { font-size: clamp(42px,6.2vw,84px); font-weight: 900; line-height: .95; letter-spacing: -3px;
        margin: 0 0 18px; color: var(--text); }
    .ct-title .grad { background: linear-gradient(120deg, #f59e0b, #ef4444 50%, #8b5cf6 85%); background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: ctGradShift 7s ease-in-out infinite; display: inline-block;
        filter: drop-shadow(0 8px 30px rgba(245,158,11,.25)); }
    @@keyframes ctGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    .ct-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.75; max-width: 540px; margin-bottom: 26px; }
    .ct-sub b { color: var(--text); }
    .ct-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
    .ct-btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 26px; border-radius: 16px;
        font-weight: 800; font-size: 14px; text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
        border: 1px solid transparent; cursor: pointer; }
    .ct-btn-fight { background: linear-gradient(135deg,#f59e0b,#ef4444); color: #fff; box-shadow: 0 10px 32px rgba(239,68,68,.35); }
    .ct-btn-fight:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 16px 44px rgba(239,68,68,.45); }
    .ct-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .ct-btn-ghost:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-3px); }
    .ct-stats3d { display: flex; gap: clamp(20px,3.5vw,44px); flex-wrap: wrap; }
    .ct-stat { position: relative; }
    .ct-stat + .ct-stat::before { content:''; position: absolute; left: -22px; top: 50%; transform: translateY(-50%);
        width: 1px; height: 38px; background: var(--border); }
    .ct-stat-val { font-size: clamp(26px,3vw,38px); font-weight: 900; line-height: 1; font-variant-numeric: tabular-nums;
        background: linear-gradient(135deg,var(--text),#f59e0b); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; }
    .ct-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px; margin-top: 6px; font-weight: 600; }

    /* --- 3D arena stage: live scoreboard --- */
    .ct-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .ct-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .ct-ring-1 { width: 480px; height: 480px; animation: ctSpin 26s linear infinite; opacity: .7; }
    .ct-ring-2 { width: 590px; height: 590px; animation: ctSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes ctSpin { to { transform: rotate(360deg); } }
    @@keyframes ctSpinRev { to { transform: rotate(-360deg); } }
    .ct-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;
        box-shadow: 0 0 14px #f59e0b; top: -5px; left: 50%; }
    .ct-board3d { position: relative; width: 100%; max-width: 520px;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid var(--border); border-radius: 24px; overflow: hidden;
        box-shadow: 0 40px 100px rgba(0,0,0,.35), 0 0 80px rgba(245,158,11,.12);
        transform-style: preserve-3d; transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear; z-index: 3; }
    .ct-board3d::after { content:''; position: absolute; inset: 0; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: ctSheen 6s ease-in-out infinite; }
    @@keyframes ctSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .ct-board-bar { display: flex; align-items: center; gap: 8px; padding: 15px 18px;
        border-bottom: 1px solid var(--border); background: rgba(0,0,0,.22); }
    .ct-dot { width: 11px; height: 11px; border-radius: 50%; }
    .ct-board-live { display: inline-flex; align-items: center; gap: 7px; margin-left: 12px; padding: 5px 12px;
        border-radius: 8px; background: rgba(239,68,68,.12); color: #f87171;
        font-size: 11px; font-weight: 800; font-family: var(--font-mono); letter-spacing: 1px; }
    .ct-board-live i { width: 7px; height: 7px; border-radius: 50%; background: #ef4444; animation: ctBlink 1.2s infinite; }
    .ct-timer { margin-left: auto; font-family: var(--font-mono); font-size: 13px; font-weight: 800; color: #fbbf24;
        background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.25); border-radius: 9px; padding: 6px 12px;
        font-variant-numeric: tabular-nums; }
    .ct-board-body { padding: 16px 18px 20px; transform: translateZ(40px); }
    .ct-row { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 14px;
        border: 1px solid transparent; opacity: 0; transform: translateX(-10px); animation: ctRowIn .5s ease forwards; }
    .ct-row:nth-child(1){animation-delay:.3s} .ct-row:nth-child(2){animation-delay:.55s}
    .ct-row:nth-child(3){animation-delay:.8s} .ct-row:nth-child(4){animation-delay:1.05s}
    @@keyframes ctRowIn { to { opacity: 1; transform: none; } }
    .ct-row.lead { background: linear-gradient(135deg, rgba(245,158,11,.14), rgba(239,68,68,.08));
        border-color: rgba(245,158,11,.3); }
    .ct-rank { width: 30px; height: 30px; border-radius: 9px; display: flex; align-items: center; justify-content: center;
        font-family: var(--font-mono); font-size: 12px; font-weight: 800; background: var(--bg-secondary);
        color: var(--text-muted); flex-shrink: 0; }
    .ct-row.lead .ct-rank { background: linear-gradient(135deg,#f59e0b,#ef4444); color: #fff; }
    .ct-ava { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 800; color: #fff; flex-shrink: 0; }
    .ct-name { font-size: 13px; font-weight: 700; color: var(--text); }
    .ct-name small { display: block; font-size: 10px; font-weight: 600; color: var(--text-muted); font-family: var(--font-mono); }
    .ct-score { margin-left: auto; font-family: var(--font-mono); font-size: 14px; font-weight: 800; color: #fbbf24;
        font-variant-numeric: tabular-nums; }
    .ct-vs { display: flex; align-items: center; gap: 10px; margin: 12px 2px 2px; }
    .ct-vs-line { flex: 1; height: 1px; background: var(--border); }
    .ct-vs-badge { font-family: var(--font-mono); font-size: 10px; font-weight: 800; letter-spacing: 2px;
        color: var(--text-muted); border: 1px solid var(--border); border-radius: 8px; padding: 4px 12px; }
    .ct-float-chip { position: absolute; z-index: 4; display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent); backdrop-filter: blur(18px);
        border: 1px solid var(--border); border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3); font-size: 13px; animation: ctFloatY 4.5s ease-in-out infinite; }
    @@keyframes ctFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .ct-fc-1 { top: 4%; right: -6px; } .ct-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .ct-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .ct-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0; }
    .ct-fc-ico.g { background: rgba(239,68,68,.14); color: #f87171; }
    .ct-fc-ico.p { background: rgba(245,158,11,.14); color: #f59e0b; }
    .ct-fc-ico.a { background: rgba(139,92,246,.14); color: #8b5cf6; }
    .ct-fc-txt b { display: block; font-size: 13px; color: var(--text); font-family: var(--font-mono); }
    .ct-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .ct-cube { position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px; display: flex;
        align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        animation: ctCubeFloat 6s ease-in-out infinite; }
    .ct-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,#f59e0b,#ef4444); }
    .ct-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#8b5cf6,#6d28d9); animation-delay: 1.5s; }
    @@keyframes ctCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .ct-scroll-hint { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);
        font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-family: var(--font-mono); }
    .ct-paused .ct-aurora, .ct-paused .ct-orb, .ct-paused .ct-ring, .ct-paused .ct-cube,
    .ct-paused .ct-float-chip { animation-play-state: paused !important; }
    .ct-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .ct-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px;
        height: 8px; border-radius: 4px; background: #f59e0b; animation: ctWheel 1.8s ease-in-out infinite; }
    @@keyframes ctWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ ARENA LIST ============ */
    .ct-wrap { max-width: 1280px; margin: 0 auto; padding: 70px clamp(20px,4vw,48px) 90px; }
    .ct-flash { margin-bottom: 22px; padding: 14px 18px; border-radius: 14px; font-size: 13px; font-weight: 600;
        background: rgba(34,197,94,.08); color: #22c55e; border: 1px solid rgba(34,197,94,.2); }
    .ct-layout { display: grid; grid-template-columns: 1fr 320px; gap: 26px; align-items: start; }
    .ct-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 20px; }
    .ct-card { position: relative; border-radius: 22px; overflow: hidden; text-decoration: none; display: flex;
        flex-direction: column; border: 1px solid var(--border); background: var(--card);
        box-shadow: 0 14px 40px rgba(0,0,0,.12);
        opacity: 0; transform: translateY(34px) scale(.98);
        transition: opacity .55s ease, transform .55s cubic-bezier(.16,1,.3,1), border-color .3s, box-shadow .3s; }
    .ct-card.in { opacity: 1; transform: none; }
    .ct-card:hover { transform: translateY(-6px); border-color: #f59e0b; box-shadow: 0 24px 60px rgba(0,0,0,.22), 0 0 44px rgba(245,158,11,.14); }
    .ct-cover { position: relative; height: 132px; overflow: hidden; }
    .ct-cover-grid { position: absolute; inset: 0;
        background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px);
        background-size: 26px 26px; mask-image: radial-gradient(circle at 70% 30%, black, transparent 78%); }
    .ct-cover-ico { position: absolute; right: 20px; bottom: 8px; font-size: 64px; color: rgba(255,255,255,.22);
        filter: drop-shadow(0 8px 20px rgba(0,0,0,.3)); }
    .ct-status { position: absolute; top: 14px; left: 14px; z-index: 2; padding: 6px 13px; border-radius: 10px;
        font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; font-family: var(--font-mono); }
    .ct-status-live { background: #22c55e; color: #fff; animation: ctPulse 2s ease-in-out infinite; }
    .ct-status-draft { background: #eab308; color: #1a1a1a; }
    .ct-status-done { background: rgba(0,0,0,.4); color: #fff; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,.25); }
    @@keyframes ctPulse { 0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,.45); } 50% { box-shadow: 0 0 0 9px rgba(34,197,94,0); } }
    .ct-timer-chip { position: absolute; top: 14px; right: 14px; z-index: 2; padding: 6px 12px; border-radius: 10px;
        background: rgba(0,0,0,.4); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,.22);
        color: #fde68a; font-size: 11px; font-weight: 800; font-family: var(--font-mono); font-variant-numeric: tabular-nums; }
    .ct-body { padding: 20px 20px 18px; display: flex; flex-direction: column; flex: 1; }
    .ct-title-sm { font-size: 17px; font-weight: 800; color: var(--text); margin: 0 0 10px; line-height: 1.35; }
    .ct-card:hover .ct-title-sm { color: #f59e0b; }
    .ct-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; color: var(--text-muted);
        margin-bottom: 14px; font-family: var(--font-mono); }
    .ct-tags { display: flex; gap: 6px; margin-bottom: 16px; flex-wrap: wrap; }
    .ct-tag { padding: 5px 11px; border-radius: 8px; font-size: 11px; font-weight: 700; font-family: var(--font-mono); }
    .ct-foot { display: flex; align-items: center; justify-content: space-between; padding-top: 14px;
        border-top: 1px solid var(--border); margin-top: auto; }
    .ct-owner { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted); font-family: var(--font-mono); }
    .ct-go { width: 38px; height: 38px; border-radius: 12px; background: rgba(245,158,11,.12); color: #f59e0b;
        display: flex; align-items: center; justify-content: center; font-size: 14px; transition: all .3s; }
    .ct-card:hover .ct-go { background: linear-gradient(135deg,#f59e0b,#ef4444); color: #fff; transform: translateX(4px); }
    .ct-empty { grid-column: 1/-1; text-align: center; padding: 70px 20px; color: var(--text-muted); font-family: var(--font-mono); }
    .ct-empty i { font-size: 44px; margin-bottom: 14px; display: block; opacity: .3; }
    .ct-pagination { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 34px; flex-wrap: wrap; }
    .ct-pg { min-width: 44px; height: 44px; padding: 0 14px; display: inline-flex; align-items: center; justify-content: center;
        border-radius: 14px; border: 1px solid var(--border); background: var(--card); color: var(--text-muted);
        font-weight: 700; font-size: 14px; text-decoration: none; transition: all .3s; font-family: var(--font-mono); }
    .ct-pg:hover { border-color: #f59e0b; color: #f59e0b; transform: translateY(-2px); }
    .ct-pg.on { background: linear-gradient(135deg,#f59e0b,#ef4444); color: #fff; border-color: transparent;
        box-shadow: 0 8px 26px rgba(239,68,68,.35); }
    .ct-pg.dis { opacity: .35; pointer-events: none; }

    .ct-side { position: sticky; top: 96px; display: flex; flex-direction: column; gap: 16px; }
    .ct-panel { border-radius: 18px; background: var(--card); border: 1px solid var(--border); padding: 20px;
        box-shadow: 0 12px 34px rgba(0,0,0,.1); }
    .ct-panel-title { font-size: 13px; font-weight: 800; color: var(--text); margin-bottom: 14px;
        display: flex; align-items: center; gap: 8px; font-family: var(--font-mono); }
    .ct-panel-title i { color: #f59e0b; }
    .ct-how { display: flex; align-items: flex-start; gap: 12px; padding: 11px 0; border-bottom: 1px solid var(--border); }
    .ct-how:last-child { border-bottom: none; }
    .ct-how-ico { width: 36px; height: 36px; border-radius: 11px; background: rgba(245,158,11,.1); color: #f59e0b;
        display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; font-family: var(--font-mono); font-weight: 800; }
    .ct-how-txt { font-size: 13px; color: var(--text-muted); line-height: 1.55; }
    .ct-how-txt b { color: var(--text); font-family: var(--font-mono); }
    .ct-heatmap-grid { display: flex; gap: 3px; overflow-x: auto; padding-bottom: 4px; }
    .ct-heatmap-week { display: flex; flex-direction: column; gap: 3px; }
    .ct-heatmap-cell { width: 12px; height: 12px; border-radius: 3px; cursor: pointer; transition: all .15s; }
    .ct-heatmap-cell:hover { outline: 2px solid #f59e0b; outline-offset: 1px; }
    .ct-heatmap-lvl-0 { background: var(--bg-secondary); border: 1px solid var(--border); }
    .ct-heatmap-lvl-1 { background: #78350f; } .ct-heatmap-lvl-2 { background: #b45309; }
    .ct-heatmap-lvl-3 { background: #f59e0b; } .ct-heatmap-lvl-4 { background: #fbbf24; }
    .ct-heatmap-legend { display: flex; align-items: center; gap: 4px; margin-top: 10px; justify-content: flex-end; }
    .ct-heatmap-legend span { font-size: 10px; color: var(--text-muted); font-family: var(--font-mono); }
    .ct-heatmap-legend-cell { width: 12px; height: 12px; border-radius: 3px; }
    .ct-heatmap-tooltip { display: none; position: fixed; padding: 6px 10px; border-radius: 8px; background: var(--bg-secondary);
        border: 1px solid var(--border); color: var(--text); font-size: 10px; font-family: var(--font-mono);
        white-space: nowrap; z-index: 9999; pointer-events: none; box-shadow: 0 4px 12px rgba(0,0,0,.3); }

    @@media (max-width: 1020px) {
        .ct-hero3d-inner { grid-template-columns: 1fr; }
        .ct-stage { height: 480px; }
        .ct-fc-3 { right: 0; } .ct-fc-1 { right: 0; } .ct-fc-2 { left: 0; }
        .ct-layout { grid-template-columns: 1fr; }
        .ct-side { position: static; }
        .ct-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="ct-page">
{{-- ================= HERO 3D ================= --}}
<section class="ct-hero3d" id="ctHero">
    <div class="ct-hero3d-bg">
        <div class="ct-aurora"></div>
        <div class="ct-grid3d" data-depth="18"></div>
        <div class="ct-orb ct-orb-1" data-depth="40"></div>
        <div class="ct-orb ct-orb-2" data-depth="-30"></div>
        <div class="ct-orb ct-orb-3" data-depth="60"></div>
    </div>

    <div class="ct-hero3d-inner">
        <div>
            <span class="ct-live-badge"><i></i>{{ __('Season live') }} • {{ \App\Models\Contest::where('status','active')->count() }}</span>
            <h1 class="ct-title">{!! __('Enter the<br><span class="grad">Arena</span>') !!}</h1>
            <p class="ct-sub">{!! __('Compete with developers <b>worldwide</b> — solve tasks against the clock, earn rating and lift the <b>trophy</b>.') !!}</p>

            <div class="ct-hero-actions">
                @auth
                <a href="{{ route('contests.create') }}" class="ct-btn ct-btn-fight"><i class="fas fa-plus"></i>{{ __('Create Contest') }}</a>
                @endauth
                <a href="#ctList" class="ct-btn ct-btn-ghost" id="ctToList"><i class="fas fa-swords"></i>{{ __('View arenas') }}</a>
            </div>

            <div class="ct-stats3d">
                <div class="ct-stat"><div class="ct-stat-val" data-count="{{ $contests->total() }}">0</div><div class="ct-stat-label">{{ __('Contests') }}</div></div>
                <div class="ct-stat"><div class="ct-stat-val" data-count="{{ \App\Models\Contest::where('status','active')->count() }}">0</div><div class="ct-stat-label">{{ __('Live now') }}</div></div>
                <div class="ct-stat"><div class="ct-stat-val" data-count="{{ \App\Models\ContestProblem::count() }}">0</div><div class="ct-stat-label">{{ __('Tasks') }}</div></div>
                <div class="ct-stat"><div class="ct-stat-val" data-count="{{ \App\Models\ContestSubmission::count() }}">0</div><div class="ct-stat-label">{{ __('Submits') }}</div></div>
            </div>
        </div>

        <div class="ct-stage">
            <div class="ct-ring ct-ring-1"><span class="ct-ring-dot"></span></div>
            <div class="ct-ring ct-ring-2"><span class="ct-ring-dot" style="background:#8b5cf6;box-shadow:0 0 14px #8b5cf6"></span></div>
            <div class="ct-cube ct-cube-1" data-depth="70"><i class="fas fa-trophy"></i></div>
            <div class="ct-cube ct-cube-2" data-depth="-60"><i class="fas fa-bolt"></i></div>

            <div class="ct-board3d" id="ctBoard">
                <div class="ct-board-bar">
                    <span class="ct-dot" style="background:#ff5f57"></span>
                    <span class="ct-dot" style="background:#febc2e"></span>
                    <span class="ct-dot" style="background:#28c840"></span>
                    <span class="ct-board-live"><i></i>LIVE</span>
                    <span class="ct-timer"><i class="fas fa-stopwatch" style="margin-right:6px"></i><span id="ctClock">--:--:--</span></span>
                </div>
                <div class="ct-board-body">
                    <div class="ct-row lead">
                        <span class="ct-rank">1</span>
                        <span class="ct-ava" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">A</span>
                        <span class="ct-name">algo_king<small>4 solves • 38:12</small></span>
                        <span class="ct-score">2400</span>
                    </div>
                    <div class="ct-row">
                        <span class="ct-rank">2</span>
                        <span class="ct-ava" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9)">S</span>
                        <span class="ct-name">segfault<small>4 solves • 41:05</small></span>
                        <span class="ct-score">2280</span>
                    </div>
                    <div class="ct-row">
                        <span class="ct-rank">3</span>
                        <span class="ct-ava" style="background:linear-gradient(135deg,#38bdf8,#0369a1)">N</span>
                        <span class="ct-name">null_ptr<small>3 solves • 35:44</small></span>
                        <span class="ct-score">2110</span>
                    </div>
                    <div class="ct-row">
                        <span class="ct-rank">4</span>
                        <span class="ct-ava" style="background:linear-gradient(135deg,#22c55e,#15803d)">Y</span>
                        <span class="ct-name">you<small>climbing…</small></span>
                        <span class="ct-score" id="ctYouScore">1975</span>
                    </div>
                    <div class="ct-vs"><span class="ct-vs-line"></span><span class="ct-vs-badge">ROUND 03 / 05</span><span class="ct-vs-line"></span></div>
                </div>
            </div>

            <div class="ct-float-chip ct-fc-1" data-depth="50">
                <div class="ct-fc-ico g"><i class="fas fa-trophy"></i></div>
                <div class="ct-fc-txt"><b>+100 XP</b><span>{{ __('winner takes') }}</span></div>
            </div>
            <div class="ct-float-chip ct-fc-2" data-depth="-45">
                <div class="ct-fc-ico p"><i class="fas fa-stopwatch"></i></div>
                <div class="ct-fc-txt"><b>{{ __('vs clock') }}</b><span>{{ __('every second counts') }}</span></div>
            </div>
            <div class="ct-float-chip ct-fc-3" data-depth="35">
                <div class="ct-fc-ico a"><i class="fas fa-ranking-star"></i></div>
                <div class="ct-fc-txt"><b>{{ __('Rating') }}</b><span>{{ __('climb the board') }}</span></div>
            </div>
        </div>
    </div>

    <div class="ct-scroll-hint"><div class="ct-mouse"></div><span>{{ __('Scroll — pick a fight') }}</span></div>
</section>

{{-- ================= ARENA LIST ================= --}}
<div class="ct-wrap" id="ctList">
    @if(session('success'))
    <div class="ct-flash"><i class="fas fa-check-circle" style="margin-right:8px"></i>{{ session('success') }}</div>
    @endif

    <div class="ct-layout">
        <div>
            <div class="ct-grid" id="ctGrid">
                @forelse($contests as $contest)
                @php
                $gradients = [
                    'linear-gradient(135deg, #f59e0b 0%, #ef4444 100%)',
                    'linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)',
                    'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                    'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                    'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                    'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
                    'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                ];
                $gradient = $gradients[$contest->id % count($gradients)];
                $diffColor = match($contest->difficulty) { 'easy' => '#22c55e', 'medium' => '#eab308', 'hard' => '#ef4444', default => '#6366f1' };
                @endphp
                <a href="{{ route('contests.show', $contest->id) }}" class="ct-card" data-i="{{ $loop->index }}">
                    <div class="ct-cover" style="background:{{ $gradient }}">
                        <div class="ct-cover-grid"></div>
                        <i class="fas fa-trophy ct-cover-ico"></i>
                        @if($contest->status === 'active')
                        <span class="ct-status ct-status-live">● {{ __('Live') }}</span>
                        @elseif($contest->status === 'draft')
                        <span class="ct-status ct-status-draft">{{ __('Draft') }}</span>
                        @else
                        <span class="ct-status ct-status-done">{{ __('Finished') }}</span>
                        @endif
                        <span class="ct-timer-chip"><i class="fas fa-clock" style="margin-right:5px"></i>{{ $contest->time_limit }} {{ __('min') }}</span>
                    </div>
                    <div class="ct-body">
                        <h3 class="ct-title-sm">{{ $contest->title }}</h3>
                        <div class="ct-meta">
                            <span><i class="fas fa-list-check"></i>{{ $contest->problems_count }} {{ __('tasks') }}</span>
                            <span><i class="fas fa-users"></i>{{ $contest->submissions_count }} {{ __('submits') }}</span>
                        </div>
                        <div class="ct-tags">
                            <span class="ct-tag" style="background:{{ $diffColor }}18;color:{{ $diffColor }};border:1px solid {{ $diffColor }}44">[{{ $contest->difficulty }}]</span>
                            @if($contest->start_time)
                            <span class="ct-tag" style="background:var(--accent-glow);color:var(--accent)">{{ $contest->start_time->diffForHumans() }}</span>
                            @endif
                        </div>
                        <div class="ct-foot">
                            <span class="ct-owner">
                                @auth
                                    @if(Auth::id() === $contest->created_by)
                                    <i class="fas fa-crown" style="color:#f59e0b"></i>{{ __('Your arena') }}
                                    @else
                                    <i class="fas fa-swords"></i>{{ __('Enter') }}
                                    @endif
                                @else
                                <i class="fas fa-swords"></i>{{ __('Enter') }}
                                @endauth
                            </span>
                            <span class="ct-go"><i class="fas fa-arrow-right"></i></span>
                        </div>
                    </div>
                </a>
                @empty
                <div class="ct-empty">
                    <i class="fas fa-trophy"></i>
                    <p>{{ __('No arenas yet — be the first to create one') }}</p>
                </div>
                @endforelse
            </div>

            @if($contests->hasPages())
            <div class="ct-pagination">
                @if($contests->onFirstPage())
                <span class="ct-pg dis"><i class="fas fa-chevron-left"></i></span>
                @else
                <a href="{{ $contests->previousPageUrl() }}" class="ct-pg"><i class="fas fa-chevron-left"></i></a>
                @endif
                @foreach($contests->getUrlRange(max(1, $contests->currentPage() - 2), min($contests->lastPage(), $contests->currentPage() + 2)) as $page => $url)
                @if($page == $contests->currentPage())
                <span class="ct-pg on">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="ct-pg">{{ $page }}</a>
                @endif
                @endforeach
                @if($contests->currentPage() + 2 < $contests->lastPage())
                <span style="color:var(--text-muted)">…</span>
                <a href="{{ $contests->url($contests->lastPage()) }}" class="ct-pg">{{ $contests->lastPage() }}</a>
                @endif
                @if($contests->hasMorePages())
                <a href="{{ $contests->nextPageUrl() }}" class="ct-pg"><i class="fas fa-chevron-right"></i></a>
                @else
                <span class="ct-pg dis"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
            @endif
        </div>

        <aside class="ct-side">
            @auth
            <div class="ct-panel">
                <div class="ct-panel-title"><i class="fas fa-fire"></i>{{ __('Your Activity') }}
                    <span style="margin-left:auto;font-size:11px;font-weight:600;color:var(--text-muted)" id="heatmap-total"></span>
                </div>
                <div x-data="heatmap()" x-init="init()">
                    <div class="ct-heatmap-grid" id="heatmap-grid"></div>
                    <div class="ct-heatmap-tooltip" id="ct-heatmap-tooltip"></div>
                </div>
                <div class="ct-heatmap-legend">
                    <span>{{ __('Less') }}</span>
                    <div class="ct-heatmap-legend-cell ct-heatmap-lvl-0"></div>
                    <div class="ct-heatmap-legend-cell ct-heatmap-lvl-1"></div>
                    <div class="ct-heatmap-legend-cell ct-heatmap-lvl-2"></div>
                    <div class="ct-heatmap-legend-cell ct-heatmap-lvl-3"></div>
                    <div class="ct-heatmap-legend-cell ct-heatmap-lvl-4"></div>
                    <span>{{ __('More') }}</span>
                </div>
            </div>
            @endauth
            <div class="ct-panel">
                <div class="ct-panel-title"><i class="fas fa-scroll"></i>{{ __('Arena Rules') }}</div>
                <div class="ct-how">
                    <div class="ct-how-ico">01</div>
                    <div class="ct-how-txt">{!! __('<b>Pick an arena</b> — live ones tick, finished ones train') !!}</div>
                </div>
                <div class="ct-how">
                    <div class="ct-how-ico">02</div>
                    <div class="ct-how-txt">{!! __('<b>Beat the clock</b> — faster solves, higher board') !!}</div>
                </div>
                <div class="ct-how">
                    <div class="ct-how-ico">03</div>
                    <div class="ct-how-txt">{!! __('<b>Take XP</b> — winners grab rating and glory') !!}</div>
                </div>
            </div>
        </aside>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    /* --- Hero mouse 3D parallax --- */
    var hero = document.getElementById('ctHero');
    var board = document.getElementById('ctBoard');
    var layers = document.querySelectorAll('#ctHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0, heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('ct-paused', !heroVisible);
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
                if (board) board.style.transform = 'rotateX(' + (6 + rx * -1).toFixed(2) + 'deg) rotateY(' + (-10 + ry).toFixed(2) + 'deg)';
            }
            requestAnimationFrame(tilt);
        })();
    }

    /* --- Animated counters --- */
    document.querySelectorAll('.ct-stat-val[data-count]').forEach(function(el) {
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

    /* --- Arena countdown to midnight --- */
    var clock = document.getElementById('ctClock');
    function tickClock() {
        if (!clock) return;
        var now = new Date();
        var end = new Date(now); end.setHours(24, 0, 0, 0);
        var s = Math.max(0, Math.floor((end - now) / 1000));
        var h = String(Math.floor(s / 3600)).padStart(2, '0');
        var m = String(Math.floor(s % 3600 / 60)).padStart(2, '0');
        var sec = String(s % 60).padStart(2, '0');
        clock.textContent = h + ':' + m + ':' + sec;
    }
    tickClock(); setInterval(tickClock, 1000);

    /* --- Your score slowly climbing (demo pulse) --- */
    var you = document.getElementById('ctYouScore');
    var base = 1975;
    if (you) setInterval(function() { base += Math.floor(Math.random() * 6); you.textContent = base; }, 4000);

    /* --- Cards stagger reveal --- */
    var cards = document.querySelectorAll('#ctGrid .ct-card');
    if ('IntersectionObserver' in window && cards.length) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(en) {
                if (en.isIntersecting) {
                    var el = en.target;
                    el.style.transitionDelay = (parseInt(el.dataset.i || 0, 10) % 6 * 0.07) + 's';
                    el.classList.add('in');
                    io.unobserve(el);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
        cards.forEach(function(c) { io.observe(c); });
        setTimeout(function() { cards.forEach(function(c) { c.classList.add('in'); }); }, 4000);
    } else {
        cards.forEach(function(c) { c.classList.add('in'); });
    }

    /* --- Scroll to list --- */
    var toList = document.getElementById('ctToList');
    if (toList) toList.addEventListener('click', function(e) {
        e.preventDefault();
        var t = document.getElementById('ctList');
        if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();

/* heatmap (kept from previous version) */
function heatmap(){return{init(){var data=@json($activityData);var grid=document.getElementById('heatmap-grid');if(!grid)return;var today=new Date();var total=Object.values(data).reduce(function(a,b){return a+b},0);var tot=document.getElementById('heatmap-total');if(tot)tot.textContent=total+' {{ __("submissions this year") }}';var weeks=53;var startDate=new Date(today);startDate.setDate(startDate.getDate()-(weeks*7-1)+(6-startDate.getDay()));var html='';var currentDate=new Date(startDate);for(var w=0;w<weeks;w++){html+='<div class="ct-heatmap-week">';for(var d=0;d<7;d++){var dateStr=currentDate.toISOString().split('T')[0];var count=data[dateStr]||0;var lvl=0;if(count>=10)lvl=4;else if(count>=6)lvl=3;else if(count>=3)lvl=2;else if(count>=1)lvl=1;var label=count>0?count+' {{ __("submissions on") }} '+dateStr:'{{ __("No submissions on") }} '+dateStr;var isFuture=currentDate>today;var op=isFuture?'opacity:0.3;pointer-events:none;':'';html+='<div class="ct-heatmap-cell ct-heatmap-lvl-'+(isFuture?0:lvl)+'" style="'+op+'" data-tip="'+label.replace(/"/g,'&quot;')+'"></div>';currentDate.setDate(currentDate.getDate()+1)}html+='</div>'}grid.innerHTML=html;var tip=document.getElementById('ct-heatmap-tooltip');grid.addEventListener('mouseover',function(e){var cell=e.target.closest('.ct-heatmap-cell');if(!cell||!cell.dataset.tip)return;tip.textContent=cell.dataset.tip;tip.style.display='block';var r=cell.getBoundingClientRect();tip.style.left=r.left+r.width/2-tip.offsetWidth/2+'px';tip.style.top=r.top-tip.offsetHeight-6+'px'});grid.addEventListener('mouseout',function(e){var cell=e.target.closest('.ct-heatmap-cell');if(cell)tip.style.display='none'})}}}
</script>
@endsection
