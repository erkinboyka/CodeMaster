@extends('layouts.app')

@section('title', $contest->title . ' - CodeMaster')

@section('head')
<style>
    /* ============ CONTEST ARENA (detail) ============ */
    .ca-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .ca-hero3d { position: relative; min-height: 100vh; display: flex; align-items: center;
        overflow: hidden; padding: 110px clamp(20px,4vw,56px) 80px; isolation: isolate; perspective: 1600px; }
    .ca-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .ca-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(245,158,11,.13) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(239,68,68,.10) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(139,92,246,.10) 0%, transparent 60%);
        animation: caAurora 22s ease-in-out infinite alternate; }
    @@keyframes caAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); } }
    .ca-grid3d { position: absolute; inset: -50%;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px; opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2); transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%); }
    .ca-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .ca-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: caOrb1 16s ease-in-out infinite; }
    .ca-orb-2 { width: 460px; height: 460px; background: #f59e0b; opacity: .10; bottom: -18%; right: -6%; animation: caOrb2 20s ease-in-out infinite; }
    .ca-orb-3 { width: 260px; height: 260px; background: #ef4444; opacity: .09; top: 55%; left: 42%; animation: caOrb3 12s ease-in-out infinite; }
    @@keyframes caOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes caOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes caOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }
    @@keyframes caBlink { 0%,100%{opacity:1} 50%{opacity:.35} }

    .ca-hero3d-inner { position: relative; z-index: 2; width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px,5vw,72px); align-items: center; }
    .ca-crumb { display: flex; align-items: center; gap: 9px; font-size: 12.5px; margin-bottom: 18px;
        font-family: var(--font-mono); }
    .ca-crumb a { color: var(--text-muted); text-decoration: none; transition: color .2s; }
    .ca-crumb a:hover { color: var(--accent); }
    .ca-crumb i { font-size: 9px; color: var(--text-muted); opacity: .6; }
    .ca-crumb span { color: var(--text); font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        max-width: 320px; }
    .ca-badges { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
    .ca-badge { padding: 6px 14px; border-radius: 10px; font-size: 10.5px; font-weight: 800; text-transform: uppercase;
        letter-spacing: .8px; font-family: var(--font-mono); }
    .ca-badge-live { background: #22c55e; color: #fff; animation: caPulse 2s ease-in-out infinite; }
    .ca-badge-draft { background: #eab308; color: #1a1a1a; }
    .ca-badge-done { background: rgba(255,255,255,.12); color: #fff; border: 1px solid rgba(255,255,255,.2); }
    .ca-badge-diff { background: var(--accent-glow); color: var(--accent); border: 1px solid var(--accent-glow-strong); }
    @@keyframes caPulse { 0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,.45); } 50% { box-shadow: 0 0 0 9px rgba(34,197,94,0); } }
    .ca-title { font-size: clamp(36px,5vw,68px); font-weight: 900; line-height: .98; letter-spacing: -2.5px;
        margin: 0 0 14px; color: var(--text); overflow-wrap: anywhere; }
    .ca-desc { font-size: clamp(14px,1.5vw,17px); color: var(--text-secondary); line-height: 1.7; max-width: 560px;
        margin-bottom: 20px; }
    .ca-meta { display: flex; flex-wrap: wrap; gap: 10px 22px; margin-bottom: 24px; font-family: var(--font-mono);
        font-size: 12.5px; color: var(--text-muted); }
    .ca-meta b { color: var(--text); }
    .ca-meta i { color: #f59e0b; margin-right: 6px; }
    .ca-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .ca-btn { display: inline-flex; align-items: center; gap: 8px; padding: 13px 24px; border-radius: 14px;
        font-weight: 800; font-size: 13.5px; text-decoration: none; transition: all .3s;
        border: 1px solid var(--border); background: var(--card); color: var(--text); cursor: pointer; }
    .ca-btn:hover { border-color: #f59e0b; color: #f59e0b; transform: translateY(-2px); }
    .ca-btn-gold { background: linear-gradient(135deg,#f59e0b,#ef4444); color: #fff; border-color: transparent;
        box-shadow: 0 10px 30px rgba(239,68,68,.35); }
    .ca-btn-gold:hover { color: #fff; transform: translateY(-3px); }
    .ca-btn-danger { border-color: rgba(239,68,68,.35); color: #f87171; background: transparent; }
    .ca-btn-danger:hover { border-color: #ef4444; color: #ef4444; background: rgba(239,68,68,.08); }

    /* --- countdown panel stage --- */
    .ca-stage { position: relative; height: 560px; display: flex; align-items: center; justify-content: center;
        perspective: 1400px; }
    .ca-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .ca-ring-1 { width: 460px; height: 460px; animation: caSpin 26s linear infinite; opacity: .7; }
    .ca-ring-2 { width: 560px; height: 560px; animation: caSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes caSpin { to { transform: rotate(360deg); } }
    @@keyframes caSpinRev { to { transform: rotate(-360deg); } }
    .ca-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;
        box-shadow: 0 0 14px #f59e0b; top: -5px; left: 50%; }
    .ca-clock3d { position: relative; width: 100%; max-width: 420px; padding: 30px 28px; text-align: center;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid var(--border); border-radius: 26px;
        box-shadow: 0 40px 100px rgba(0,0,0,.35), 0 0 80px rgba(245,158,11,.12);
        transform-style: preserve-3d; transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear; z-index: 3; overflow: hidden; }
    .ca-clock3d::after { content:''; position: absolute; inset: 0; border-radius: 26px; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: caSheen 6s ease-in-out infinite; }
    @@keyframes caSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .ca-clock-live { display: inline-flex; align-items: center; gap: 7px; padding: 6px 14px; border-radius: 100px;
        background: rgba(239,68,68,.12); color: #f87171; font-size: 10.5px; font-weight: 800;
        font-family: var(--font-mono); letter-spacing: 1.5px; margin-bottom: 14px; transform: translateZ(40px); }
    .ca-clock-live i { width: 7px; height: 7px; border-radius: 50%; background: #ef4444; animation: caBlink 1.2s infinite; }
    .ca-clock-val { font-family: var(--font-mono); font-size: clamp(48px,5vw,68px); font-weight: 900; line-height: 1;
        background: linear-gradient(135deg,#fbbf24,#ef4444); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; font-variant-numeric: tabular-nums;
        transform: translateZ(50px); }
    .ca-clock-lbl { font-size: 10.5px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2.5px;
        font-weight: 700; margin: 10px 0 20px; }
    .ca-clock-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; transform: translateZ(25px); }
    .ca-clock-cell { border-radius: 14px; background: var(--bg-secondary); border: 1px solid var(--border); padding: 12px 6px; }
    .ca-clock-cell b { display: block; font-size: 18px; font-weight: 900; color: var(--text);
        font-family: var(--font-mono); font-variant-numeric: tabular-nums; }
    .ca-clock-cell span { font-size: 9.5px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;
        font-weight: 700; }
    .ca-float-chip { position: absolute; z-index: 4; display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent); backdrop-filter: blur(18px);
        border: 1px solid var(--border); border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3); font-size: 13px; animation: caFloatY 4.5s ease-in-out infinite; }
    @@keyframes caFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .ca-fc-1 { top: 4%; right: -6px; } .ca-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .ca-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .ca-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0; }
    .ca-fc-ico.g { background: rgba(245,158,11,.14); color: #f59e0b; }
    .ca-fc-ico.p { background: rgba(34,197,94,.14); color: #22c55e; }
    .ca-fc-ico.a { background: rgba(139,92,246,.14); color: #8b5cf6; }
    .ca-fc-txt b { display: block; font-size: 13px; color: var(--text); font-family: var(--font-mono); }
    .ca-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .ca-cube { position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px; display: flex;
        align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        animation: caCubeFloat 6s ease-in-out infinite; }
    .ca-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,#f59e0b,#ef4444); }
    .ca-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#8b5cf6,#6d28d9); animation-delay: 1.5s; }
    @@keyframes caCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .ca-scroll-hint { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);
        font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-family: var(--font-mono); }
    .ca-paused .ca-aurora, .ca-paused .ca-orb, .ca-paused .ca-ring, .ca-paused .ca-cube,
    .ca-paused .ca-float-chip { animation-play-state: paused !important; }
    .ca-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .ca-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px;
        height: 8px; border-radius: 4px; background: #f59e0b; animation: caWheel 1.8s ease-in-out infinite; }
    @@keyframes caWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ BATTLEFIELD ============ */
    .ca-wrap { max-width: 1120px; margin: 0 auto; padding: 70px clamp(20px,4vw,48px) 90px; }
    .ca-flash { margin-bottom: 22px; padding: 14px 18px; border-radius: 14px; font-size: 13px; font-weight: 600;
        background: rgba(34,197,94,.08); color: #22c55e; border: 1px solid rgba(34,197,94,.2); }
    .ca-cols { display: grid; grid-template-columns: minmax(0,1fr) 320px; gap: 24px; align-items: start; }
    .ca-sec-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; gap: 12px; }
    .ca-sec-t { font-size: 18px; font-weight: 900; color: var(--text); letter-spacing: -.4px;
        display: flex; align-items: center; gap: 9px; }
    .ca-sec-t i { color: #f59e0b; }
    .ca-sec-t small { font-family: var(--font-mono); font-size: 12px; color: var(--text-muted); font-weight: 700; }
    .ca-add { display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px; border-radius: 11px;
        font-size: 12.5px; font-weight: 800; border: none; cursor: pointer;
        background: linear-gradient(135deg,#f59e0b,#ef4444); color: #fff;
        box-shadow: 0 6px 18px rgba(239,68,68,.35); transition: all .25s; }
    .ca-add:hover { transform: translateY(-2px); }
    .ca-task { display: flex; align-items: center; gap: 14px; padding: 17px 20px; border-radius: 17px;
        border: 1px solid var(--border); background: var(--card); text-decoration: none; margin-bottom: 10px;
        box-shadow: 0 10px 28px rgba(0,0,0,.08); position: relative; overflow: hidden;
        opacity: 0; transform: translateY(20px);
        transition: opacity .45s ease, transform .45s cubic-bezier(.16,1,.3,1), border-color .25s, box-shadow .25s; }
    .ca-task.in { opacity: 1; transform: none; }
    .ca-task::before { content:''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
        background: linear-gradient(180deg,#f59e0b,#ef4444); opacity: 0; transition: opacity .25s; }
    .ca-task:hover { border-color: #f59e0b; transform: translateX(5px); box-shadow: 0 14px 36px rgba(0,0,0,.13); }
    .ca-task:hover::before { opacity: 1; }
    .ca-num { width: 38px; height: 38px; border-radius: 12px; background: rgba(245,158,11,.1); color: #f59e0b;
        display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 900;
        font-family: var(--font-mono); flex-shrink: 0; }
    .ca-task-t { font-size: 14.5px; font-weight: 800; color: var(--text); }
    .ca-task:hover .ca-task-t { color: #f59e0b; }
    .ca-task-tags { display: flex; gap: 6px; margin-top: 6px; flex-wrap: wrap; }
    .ca-tag { padding: 3px 10px; border-radius: 7px; font-size: 10.5px; font-weight: 800; font-family: var(--font-mono);
        text-transform: uppercase; letter-spacing: .4px; }
    .ca-check { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 13px; flex-shrink: 0; margin-left: auto; background: var(--bg-secondary);
        border: 1px solid var(--border); color: transparent; }
    .ca-check.done { background: linear-gradient(135deg,#22c55e,#16a34a); border-color: transparent; color: #fff;
        box-shadow: 0 0 16px rgba(34,197,94,.5); }
    .ca-empty { text-align: center; padding: 60px 24px; border-radius: 18px; border: 1.5px dashed var(--border-hover);
        background: var(--card); color: var(--text-muted); font-family: var(--font-mono); }
    .ca-empty i { font-size: 40px; margin-bottom: 12px; display: block; opacity: .25; }
    .ca-side { display: flex; flex-direction: column; gap: 16px; position: sticky; top: 96px; }
    .ca-panel { border-radius: 18px; background: var(--card); border: 1px solid var(--border); padding: 20px;
        box-shadow: 0 12px 34px rgba(0,0,0,.1); }
    .ca-panel-t { font-size: 13px; font-weight: 800; color: var(--text); margin-bottom: 14px;
        display: flex; align-items: center; gap: 8px; font-family: var(--font-mono); text-transform: uppercase;
        letter-spacing: 1px; }
    .ca-panel-t i { color: #f59e0b; }
    .ca-kv { display: flex; align-items: center; justify-content: space-between; padding: 9px 0;
        border-bottom: 1px solid var(--border); font-size: 13px; }
    .ca-kv:last-child { border-bottom: none; }
    .ca-kv span:first-child { color: var(--text-muted); }
    .ca-kv b { font-family: var(--font-mono); }
    .ca-score-ring { width: 132px; height: 132px; border-radius: 50%; margin: 4px auto 12px; position: relative;
        background: conic-gradient(#f59e0b calc(var(--pct) * 1%), var(--border) 0); }
    .ca-score-ring::before { content:''; position: absolute; inset: 12px; border-radius: 50%; background: var(--card); }
    .ca-score-in { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center;
        justify-content: center; }
    .ca-score-in b { font-size: 30px; font-weight: 900; color: #f59e0b; font-family: var(--font-mono); }
    .ca-score-in span { font-size: 10px; color: var(--text-muted); font-family: var(--font-mono); }
    .ca-heatmap-grid { display: flex; gap: 3px; overflow-x: auto; padding-bottom: 4px; }
    .ca-heatmap-week { display: flex; flex-direction: column; gap: 3px; }
    .ca-heatmap-cell { width: 12px; height: 12px; border-radius: 3px; cursor: pointer; transition: all .15s; }
    .ca-heatmap-cell:hover { outline: 2px solid #f59e0b; outline-offset: 1px; }
    .ca-heatmap-lvl-0 { background: var(--bg-secondary); border: 1px solid var(--border); }
    .ca-heatmap-lvl-1 { background: #78350f; } .ca-heatmap-lvl-2 { background: #b45309; }
    .ca-heatmap-lvl-3 { background: #f59e0b; } .ca-heatmap-lvl-4 { background: #fbbf24; }
    .ca-heatmap-legend { display: flex; align-items: center; gap: 4px; margin-top: 10px; justify-content: flex-end;
        font-size: 10px; color: var(--text-muted); font-family: var(--font-mono); }
    .ca-heatmap-legend-cell { width: 12px; height: 12px; border-radius: 3px; }
    .ca-heatmap-tooltip { display: none; position: fixed; padding: 6px 12px; border-radius: 8px;
        background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text);
        font-size: 11px; font-family: var(--font-mono); white-space: nowrap; z-index: 9999; pointer-events: none;
        box-shadow: 0 4px 12px rgba(0,0,0,.3); }

    .ca-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.6); backdrop-filter: blur(6px);
        display: none; align-items: center; justify-content: center; z-index: 200; padding: 16px; }
    .ca-modal-overlay.open { display: flex; }
    .ca-modal { background: var(--card); border: 1px solid var(--border); border-radius: 22px; width: 100%;
        max-width: 600px; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 70px rgba(0,0,0,.35);
        animation: caModalIn .25s cubic-bezier(.16,1,.3,1); }
    @@keyframes caModalIn { from { opacity: 0; transform: scale(.95) translateY(10px); } to { opacity: 1; transform: none; } }
    .ca-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 22px 24px;
        border-bottom: 1px solid var(--border); flex-shrink: 0; background: var(--card); z-index: 2; }
    .ca-modal-title { font-size: 17px; font-weight: 800; color: var(--text); }
    .ca-modal-close { width: 36px; height: 36px; border-radius: 10px; border: none; background: var(--bg-secondary);
        color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .2s; }
    .ca-modal-close:hover { background: rgba(245,158,11,.12); color: #f59e0b; }
    .ca-modal-body { padding: 22px 24px; flex: 1 1 auto; min-height: 0; overflow-y: auto; overscroll-behavior: contain;
        scrollbar-width: thin; scrollbar-color: var(--border) transparent; }
    .ca-modal-body::-webkit-scrollbar { width: 10px; }
    .ca-modal-body::-webkit-scrollbar-track { background: transparent; margin: 8px 0; }
    .ca-modal-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px;
        border: 3px solid transparent; background-clip: padding-box; }
    .ca-modal-body::-webkit-scrollbar-thumb:hover { background: var(--text-muted); border: 3px solid transparent; background-clip: padding-box; }
    .ca-form-group { margin-bottom: 14px; }
    .ca-form-label { display: block; font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
    .ca-form-input, .ca-form-select { width: 100%; box-sizing: border-box; padding: 11px 14px; border-radius: 11px;
        border: 1px solid var(--border); background: var(--bg-secondary); color: var(--text); font-size: 13.5px;
        outline: none; transition: all .2s; }
    .ca-form-input:focus, .ca-form-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); }
    .ca-form-input.mono { font-family: var(--font-mono); font-size: 12px; }
    .ca-form-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-bottom: 14px; }
    .ca-form-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px; }
    .ca-modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 16px 24px; border-top: 1px solid var(--border); flex-shrink: 0; background: var(--card); }
    .ca-mbtn { padding: 11px 22px; border-radius: 11px; font-size: 13px; font-weight: 800; cursor: pointer; transition: all .2s; }
    .ca-mbtn-cancel { border: 1px solid var(--border); background: transparent; color: var(--text-muted); }
    .ca-mbtn-cancel:hover { border-color: #f59e0b; color: #f59e0b; }
    .ca-mbtn-go { border: none; background: linear-gradient(135deg,#f59e0b,#ef4444); color: #fff;
        box-shadow: 0 6px 18px rgba(239,68,68,.35); }
    .ca-mbtn-go:hover { transform: translateY(-1px); }

    @@media(max-width: 1020px) {
        .ca-hero3d-inner { grid-template-columns: 1fr; }
        .ca-stage { height: 520px; }
        .ca-fc-3 { right: 0; } .ca-fc-1 { right: 0; } .ca-fc-2 { left: 0; }
        .ca-cols { grid-template-columns: 1fr; }
        .ca-side { position: static; }
    }
</style>
@endsection

@section('content')
<div class="ca-page">
{{-- ================= HERO 3D ================= --}}
<section class="ca-hero3d" id="caHero">
    <div class="ca-hero3d-bg">
        <div class="ca-aurora"></div>
        <div class="ca-grid3d" data-depth="18"></div>
        <div class="ca-orb ca-orb-1" data-depth="40"></div>
        <div class="ca-orb ca-orb-2" data-depth="-30"></div>
        <div class="ca-orb ca-orb-3" data-depth="60"></div>
    </div>

    <div class="ca-hero3d-inner">
        <div>
            <nav class="ca-crumb">
                <a href="{{ route('contests.index') }}">{{ __('Contests') }}</a>
                <i class="fas fa-chevron-right"></i>
                <span>{{ $contest->title }}</span>
            </nav>
            <div class="ca-badges">
                @if($contest->status === 'active')
                <span class="ca-badge ca-badge-live">● {{ __('Live') }}</span>
                @elseif($contest->status === 'draft')
                <span class="ca-badge ca-badge-draft">{{ __('Draft') }}</span>
                @else
                <span class="ca-badge ca-badge-done">{{ __('Finished') }}</span>
                @endif
                <span class="ca-badge ca-badge-diff">[{{ $contest->difficulty }}]</span>
            </div>
            <h1 class="ca-title">{{ $contest->title }}</h1>
            @if($contest->description)
            <p class="ca-desc">{{ $contest->description }}</p>
            @endif
            <div class="ca-meta">
                <span><i class="fas fa-list-check"></i><b>{{ $problems->count() }}</b>&nbsp;{{ __('tasks') }}</span>
                <span><i class="fas fa-users"></i><b>{{ $contest->submissions_count }}</b>&nbsp;{{ __('submits') }}</span>
                <span><i class="fas fa-clock"></i><b>{{ $contest->time_limit }}</b>&nbsp;{{ __('min') }}</span>
            </div>
            <div class="ca-actions">
                @auth
                @if(Auth::id() === $contest->created_by)
                <a href="{{ route('contests.edit', $contest->id) }}" class="ca-btn"><i class="fas fa-edit"></i>{{ __('Edit') }}</a>
                <form action="{{ route('contests.destroy', $contest->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete this contest?') }}')" style="margin:0">
                    @csrf @method('DELETE')
                    <button type="submit" class="ca-btn ca-btn-danger"><i class="fas fa-trash"></i>{{ __('Delete') }}</button>
                </form>
                @endif
                @endauth
                <a href="{{ route('contests.leaderboard', $contest->id) }}" class="ca-btn ca-btn-gold"><i class="fas fa-trophy"></i>{{ __('Leaderboard') }}</a>
            </div>
        </div>

        <div class="ca-stage">
            <div class="ca-ring ca-ring-1"><span class="ca-ring-dot"></span></div>
            <div class="ca-ring ca-ring-2"><span class="ca-ring-dot" style="background:#8b5cf6;box-shadow:0 0 14px #8b5cf6"></span></div>
            <div class="ca-cube ca-cube-1" data-depth="70"><i class="fas fa-trophy"></i></div>
            <div class="ca-cube ca-cube-2" data-depth="-60"><i class="fas fa-stopwatch"></i></div>

            <div class="ca-clock3d" id="caClock" x-data="{ t: {{ $contest->getTimeRemainingAttribute() ?? 0 }} }" x-init="if(t > 0) setInterval(() => { if(t > 0) t-- }, 1000)">
                <span class="ca-clock-live"><i></i>{{ __('countdown') }}</span>
                <div x-show="t > 0" x-cloak>
                    <div class="ca-clock-val" x-text="`${Math.floor(t/3600)}:${String(Math.floor((t%3600)/60)).padStart(2,'0')}:${String(t%60).padStart(2,'0')}`">{{ $contest->time_limit }}:00</div>
                    <div class="ca-clock-lbl">{{ __('time remaining') }}</div>
                </div>
                <div x-show="t <= 0" x-cloak>
                    <div class="ca-clock-val">--:--:--</div>
                    <div class="ca-clock-lbl">{{ __('no active timer') }}</div>
                </div>
                <div class="ca-clock-grid">
                    <div class="ca-clock-cell"><b>{{ $problems->count() }}</b><span>{{ __('tasks') }}</span></div>
                    <div class="ca-clock-cell"><b>{{ $contest->submissions_count }}</b><span>{{ __('submits') }}</span></div>
                    <div class="ca-clock-cell"><b>{{ $contest->time_limit }}′</b><span>{{ __('limit') }}</span></div>
                </div>
            </div>

            <div class="ca-float-chip ca-fc-1" data-depth="50">
                <div class="ca-fc-ico g"><i class="fas fa-trophy"></i></div>
                <div class="ca-fc-txt"><b>{{ __('Leaderboard') }}</b><span>{{ __('live standings') }}</span></div>
            </div>
            <div class="ca-float-chip ca-fc-2" data-depth="-45">
                <div class="ca-fc-ico p"><i class="fas fa-stopwatch"></i></div>
                <div class="ca-fc-txt"><b>{{ $contest->time_limit }} {{ __('min') }}</b><span>{{ __('beat the clock') }}</span></div>
            </div>
            <div class="ca-float-chip ca-fc-3" data-depth="35">
                <div class="ca-fc-ico a"><i class="fas fa-bolt"></i></div>
                <div class="ca-fc-txt"><b>{{ $problems->count() }}</b><span>{{ __('tasks to crush') }}</span></div>
            </div>
        </div>
    </div>

    <div class="ca-scroll-hint"><div class="ca-mouse"></div><span>{{ __('Scroll — tasks') }}</span></div>
</section>

{{-- ================= BATTLEFIELD ================= --}}
<div class="ca-wrap" id="caList">
    @if(session('success'))
    <div class="ca-flash"><i class="fas fa-check-circle" style="margin-right:8px"></i>{{ session('success') }}</div>
    @endif

    <div class="ca-cols">
        <div style="min-width:0">
            <div class="ca-sec-head">
                <div class="ca-sec-t"><i class="fas fa-list-check"></i>{{ __('Problems') }} <small>({{ $problems->count() }})</small></div>
                @auth
                @if(Auth::id() === $contest->created_by)
                <button onclick="document.getElementById('addProblemModal').classList.add('open')" class="ca-add">
                    <i class="fas fa-plus"></i>{{ __('Add Problem') }}
                </button>
                @endif
                @endauth
            </div>

            <div id="caTasks">
                @forelse($problems as $problem)
                @php
                $diffColor = match($problem->difficulty) { 'easy' => '#22c55e', 'medium' => '#eab308', 'hard' => '#ef4444', default => '#6366f1' };
                $isPassed = isset($userSubmissions[$problem->id]) && $userSubmissions[$problem->id]->status === 'accepted';
                @endphp
                <a href="{{ route('contests.problems.show', [$contest->id, $problem->id]) }}" class="ca-task" data-i="{{ $loop->index }}">
                    <div class="ca-num">{{ $problem->order_num ?? $loop->iteration }}</div>
                    <div style="min-width:0">
                        <div class="ca-task-t">{{ $problem->title }}</div>
                        <div class="ca-task-tags">
                            <span class="ca-tag" style="background:{{ $diffColor }}18;color:{{ $diffColor }}">[{{ $problem->difficulty }}]</span>
                            <span class="ca-tag" style="background:var(--accent-glow);color:var(--accent)">{{ $problem->points }} {{ __('pts') }}</span>
                        </div>
                    </div>
                    <div class="ca-check {{ $isPassed ? 'done' : '' }}">@if($isPassed)<i class="fas fa-check"></i>@endif</div>
                </a>
                @empty
                <div class="ca-empty">
                    <i class="fas fa-list-check"></i>
                    <p>{{ __('No problems yet') }}</p>
                    @auth
                    @if(Auth::id() === $contest->created_by)
                    <p style="font-size:12px;margin-top:8px">{{ __('Click "Add Problem" to get started') }}</p>
                    @endif
                    @endauth
                </div>
                @endforelse
            </div>
        </div>

        <aside class="ca-side">
            @auth
            <div class="ca-panel">
                <div class="ca-panel-t"><i class="fas fa-fire"></i>{{ __('Your Activity') }}
                    <span style="margin-left:auto;font-size:11px;font-weight:600;color:var(--text-muted);font-family:var(--font-mono)" id="heatmap-total"></span>
                </div>
                <div x-data="heatmap()" x-init="init()">
                    <div class="ca-heatmap-grid" id="heatmap-grid"></div>
                </div>
                <div class="ca-heatmap-legend">
                    <span>{{ __('Less') }}</span>
                    <div class="ca-heatmap-legend-cell ca-heatmap-lvl-0"></div>
                    <div class="ca-heatmap-legend-cell ca-heatmap-lvl-1"></div>
                    <div class="ca-heatmap-legend-cell ca-heatmap-lvl-2"></div>
                    <div class="ca-heatmap-legend-cell ca-heatmap-lvl-3"></div>
                    <div class="ca-heatmap-legend-cell ca-heatmap-lvl-4"></div>
                    <span>{{ __('More') }}</span>
                </div>
            </div>
            @endauth

            <div class="ca-panel">
                <div class="ca-panel-t"><i class="fas fa-circle-info"></i>{{ __('Info') }}</div>
                <div class="ca-kv"><span>{{ __('Status') }}</span><b>{{ $contest->isActive() ? __('Active') : ($contest->status === 'draft' ? __('Draft') : __('Finished')) }}</b></div>
                <div class="ca-kv"><span>{{ __('Difficulty') }}</span><b>[{{ $contest->difficulty }}]</b></div>
                <div class="ca-kv"><span>{{ __('Time Limit') }}</span><b>{{ $contest->time_limit }} {{ __('min') }}</b></div>
                <div class="ca-kv"><span>{{ __('Problems') }}</span><b>{{ $problems->count() }}</b></div>
                <div class="ca-kv"><span>{{ __('Submissions') }}</span><b>{{ $contest->submissions_count }}</b></div>
                @if($contest->start_time)
                <div class="ca-kv"><span>{{ __('Start') }}</span><b>{{ $contest->start_time->format('d.m.Y H:i') }}</b></div>
                @endif
                @if($contest->end_time)
                <div class="ca-kv"><span>{{ __('End') }}</span><b>{{ $contest->end_time->format('d.m.Y H:i') }}</b></div>
                @endif
            </div>

            @if($problems->count() > 0)
            @php
            $totalPoints = $problems->sum('points');
            $earnedPoints = 0;
            foreach($problems as $p) {
                if(isset($userSubmissions[$p->id]) && $userSubmissions[$p->id]->status === 'accepted') {
                    $earnedPoints += $p->points;
                }
            }
            $pct = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
            @endphp
            <div class="ca-panel" style="text-align:center">
                <div class="ca-panel-t" style="justify-content:center"><i class="fas fa-star"></i>{{ __('Score') }}</div>
                <div class="ca-score-ring" style="--pct:{{ $pct }}">
                    <div class="ca-score-in"><b>{{ $earnedPoints }}</b><span>/ {{ $totalPoints }} {{ __('pts') }}</span></div>
                </div>
            </div>
            @endif
        </aside>
    </div>
</div>

@if(Auth::id() === $contest->created_by ?? false)
<div id="addProblemModal" class="ca-modal-overlay">
    <div class="ca-modal">
        <div class="ca-modal-head">
            <div class="ca-modal-title">{{ __('Add Problem') }}</div>
            <button class="ca-modal-close" onclick="document.getElementById('addProblemModal').classList.remove('open')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('contests.problems.store', $contest->id) }}" method="POST">
            <div class="ca-modal-body">
                <div class="ca-form-group">
                    <label class="ca-form-label">{{ __('Title') }} *</label>
                    <input type="text" name="title" required class="ca-form-input">
                </div>
                <div class="ca-form-group">
                    <label class="ca-form-label">{{ __('Description') }}</label>
                    <textarea name="description" rows="4" class="ca-form-input" style="resize:vertical"></textarea>
                </div>
                <div class="ca-form-3">
                    <div>
                        <label class="ca-form-label">{{ __('Difficulty') }} *</label>
                        <select name="difficulty" class="ca-form-select">
                            <option value="easy">{{ __('Easy') }}</option>
                            <option value="medium" selected>{{ __('Medium') }}</option>
                            <option value="hard">{{ __('Hard') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="ca-form-label">{{ __('Points') }} *</label>
                        <input type="number" name="points" value="100" min="1" class="ca-form-input">
                    </div>
                    <div>
                        <label class="ca-form-label">{{ __('Language') }} *</label>
                        <select name="language" class="ca-form-select">
                            <option value="python">Python</option>
                            <option value="javascript">JavaScript</option>
                            <option value="php">PHP</option>
                            <option value="c">C</option>
                            <option value="cpp">C++</option>
                            <option value="java">Java</option>
                            <option value="ruby">Ruby</option>
                            <option value="go">Go</option>
                        </select>
                    </div>
                </div>
                <div class="ca-form-2">
                    <div>
                        <label class="ca-form-label">{{ __('Input Example') }}</label>
                        <textarea name="input_example" rows="3" class="ca-form-input mono" style="resize:vertical"></textarea>
                    </div>
                    <div>
                        <label class="ca-form-label">{{ __('Output Example') }}</label>
                        <textarea name="output_example" rows="3" class="ca-form-input mono" style="resize:vertical"></textarea>
                    </div>
                </div>
                <div class="ca-form-group">
                    <label class="ca-form-label">{{ __('Constraints') }}</label>
                    <input type="text" name="constraints" class="ca-form-input">
                </div>
                <div class="ca-form-group">
                    <label class="ca-form-label">{{ __('Starter Code') }}</label>
                    <textarea name="starter_code" rows="4" class="ca-form-input mono" style="resize:vertical"></textarea>
                </div>
                <div class="ca-form-group">
                    <label class="ca-form-label">{{ __('Tests (JSON)') }}</label>
                    <textarea name="tests_json" rows="3" placeholder='[{"input": "1 2", "expected": "3"}]' class="ca-form-input mono" style="resize:vertical"></textarea>
                </div>
                <div class="ca-form-2">
                    <div>
                        <label class="ca-form-label">{{ __('Time Limit (sec)') }}</label>
                        <input type="number" name="time_limit" value="2" min="1" class="ca-form-input">
                    </div>
                    <div>
                        <label class="ca-form-label">{{ __('Memory Limit (MB)') }}</label>
                        <input type="number" name="memory_limit" value="256" min="64" class="ca-form-input">
                    </div>
                </div>
            </div>
            <div class="ca-modal-foot">
                <button type="button" onclick="document.getElementById('addProblemModal').classList.remove('open')" class="ca-mbtn ca-mbtn-cancel">{{ __('Cancel') }}</button>
                <button type="submit" class="ca-mbtn ca-mbtn-go">{{ __('Add Problem') }}</button>
            </div>
        </form>
    </div>
</div>
@endif

<div id="hm-tip" class="ca-heatmap-tooltip"></div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    /* --- Hero mouse 3D parallax --- */
    var hero = document.getElementById('caHero');
    var clock = document.getElementById('caClock');
    var layers = document.querySelectorAll('#caHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0, heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('ca-paused', !heroVisible);
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
                if (clock) clock.style.transform = 'rotateX(' + (6 + rx * -1).toFixed(2) + 'deg) rotateY(' + (-10 + ry).toFixed(2) + 'deg)';
            }
            requestAnimationFrame(tilt);
        })();
    }

    /* --- Tasks stagger reveal --- */
    var tasks = document.querySelectorAll('#caTasks .ca-task');
    if ('IntersectionObserver' in window && tasks.length) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(en) {
                if (en.isIntersecting) {
                    var el = en.target;
                    el.style.transitionDelay = (parseInt(el.dataset.i || 0, 10) % 8 * 0.06) + 's';
                    el.classList.add('in');
                    io.unobserve(el);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
        tasks.forEach(function(t) { io.observe(t); });
        setTimeout(function() { tasks.forEach(function(t) { t.classList.add('in'); }); }, 4000);
    } else {
        tasks.forEach(function(t) { t.classList.add('in'); });
    }
})();

function heatmap() {
    return {
        init() {
            const data = @json($activityData);
            const grid = document.getElementById('heatmap-grid');
            const today = new Date();
            const total = Object.values(data).reduce((a, b) => a + b, 0);

            document.getElementById('heatmap-total').textContent = total + ' {{ __("submissions this year") }}';

            const weeks = 53;
            const startDate = new Date(today);
            startDate.setDate(startDate.getDate() - (weeks * 7 - 1) + (6 - startDate.getDay()));

            const tip = document.getElementById('hm-tip');

            let html = '';
            let currentDate = new Date(startDate);

            for (let w = 0; w < weeks; w++) {
                html += '<div class="ca-heatmap-week">';
                for (let d = 0; d < 7; d++) {
                    const dateStr = currentDate.toISOString().split('T')[0];
                    const count = data[dateStr] || 0;
                    let lvl = 0;
                    if (count >= 10) lvl = 4;
                    else if (count >= 6) lvl = 3;
                    else if (count >= 3) lvl = 2;
                    else if (count >= 1) lvl = 1;

                    const label = count > 0
                        ? count + ' {{ __("submissions on") }} ' + dateStr
                        : '{{ __("No submissions on") }} ' + dateStr;

                    const isFuture = currentDate > today;
                    const opacity = isFuture ? 'opacity:0.3;pointer-events:none;' : '';

                    html += `<div class="ca-heatmap-cell ca-heatmap-lvl-${isFuture ? 0 : lvl}" style="${opacity}" data-tip="${label.replace(/"/g,'&quot;')}"></div>`;
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                html += '</div>';
            }

            grid.innerHTML = html;

            grid.addEventListener('mousemove', function(e) {
                const cell = e.target.closest('.ca-heatmap-cell');
                if (!cell || !cell.dataset.tip) { tip.style.display = 'none'; return; }
                tip.textContent = cell.dataset.tip;
                tip.style.display = 'block';
                const r = cell.getBoundingClientRect();
                tip.style.left = (r.left + r.width / 2 - tip.offsetWidth / 2) + 'px';
                tip.style.top = (r.top - tip.offsetHeight - 6) + 'px';
            });
            grid.addEventListener('mouseleave', function() { tip.style.display = 'none'; });
        }
    };
}
</script>
@endsection
