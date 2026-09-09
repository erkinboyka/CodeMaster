@extends('layouts.app')

@section('title', __('interview_prep_title') . ' - CodeMaster')

@section('head')
<style>
    /* ============ INTERVIEW: DIALOGUE THEME + 3D HERO ============ */
    .iv-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .iv-hero3d { position: relative; min-height: 100vh; display: flex; align-items: center;
        overflow: hidden; padding: 110px clamp(20px,4vw,56px) 90px; isolation: isolate; perspective: 1600px; }
    .iv-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .iv-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(236,72,153,.12) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(56,189,248,.12) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(139,92,246,.10) 0%, transparent 60%);
        animation: ivAurora 22s ease-in-out infinite alternate; }
    @@keyframes ivAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); } }
    .iv-grid3d { position: absolute; inset: -50%;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px; opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2); transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%); }
    .iv-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .iv-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: ivOrb1 16s ease-in-out infinite; }
    .iv-orb-2 { width: 460px; height: 460px; background: #ec4899; opacity: .09; bottom: -18%; right: -6%; animation: ivOrb2 20s ease-in-out infinite; }
    .iv-orb-3 { width: 260px; height: 260px; background: #38bdf8; opacity: .10; top: 55%; left: 42%; animation: ivOrb3 12s ease-in-out infinite; }
    @@keyframes ivOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes ivOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes ivOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }
    @@keyframes ivBlink { 0%,100%{opacity:1} 50%{opacity:.35} }

    .iv-hero3d-inner { position: relative; z-index: 2; width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px,5vw,72px); align-items: center; }
    .iv-eyebrow { display: inline-flex; align-items: center; gap: 10px; padding: 8px 18px; border-radius: 100px;
        background: var(--accent-glow); border: 1px solid var(--accent-glow-strong);
        font-size: 11px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase; color: var(--accent); margin-bottom: 22px; }
    .iv-eyebrow i { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); animation: ivBlink 1.6s infinite; }
    .iv-title { font-size: clamp(42px,6.2vw,84px); font-weight: 900; line-height: .95; letter-spacing: -3px;
        margin: 0 0 18px; color: var(--text); }
    .iv-title .grad { background: linear-gradient(120deg, var(--accent), #ec4899 50%, #38bdf8 85%); background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: ivGradShift 7s ease-in-out infinite; display: inline-block;
        filter: drop-shadow(0 8px 30px var(--accent-glow-strong)); }
    @@keyframes ivGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    .iv-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.75; max-width: 540px; margin-bottom: 26px; }
    .iv-sub b { color: var(--text); }
    .iv-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
    .iv-btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 26px; border-radius: 16px;
        font-weight: 800; font-size: 14px; text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
        border: 1px solid transparent; cursor: pointer; }
    .iv-btn-start { background: linear-gradient(135deg,var(--accent),#ec4899); color: #fff;
        box-shadow: 0 10px 32px var(--accent-glow-strong); }
    .iv-btn-start:hover { transform: translateY(-3px) scale(1.02); }
    .iv-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .iv-btn-ghost:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-3px); }
    .iv-stats3d { display: flex; gap: clamp(20px,3.5vw,44px); flex-wrap: wrap; }
    .iv-stat { position: relative; }
    .iv-stat + .iv-stat::before { content:''; position: absolute; left: -22px; top: 50%; transform: translateY(-50%);
        width: 1px; height: 38px; background: var(--border); }
    .iv-stat-val { font-size: clamp(26px,3vw,38px); font-weight: 900; line-height: 1; font-variant-numeric: tabular-nums;
        background: linear-gradient(135deg,var(--text),var(--accent)); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; }
    .iv-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px;
        margin-top: 6px; font-weight: 600; }

    /* --- 3D dialogue stage --- */
    .iv-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .iv-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .iv-ring-1 { width: 480px; height: 480px; animation: ivSpin 26s linear infinite; opacity: .7; }
    .iv-ring-2 { width: 590px; height: 590px; animation: ivSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes ivSpin { to { transform: rotate(360deg); } }
    @@keyframes ivSpinRev { to { transform: rotate(-360deg); } }
    .iv-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: var(--accent);
        box-shadow: 0 0 14px var(--accent); top: -5px; left: 50%; }
    .iv-chat3d { position: relative; width: 100%; max-width: 480px; padding: 22px;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid var(--border); border-radius: 24px;
        box-shadow: 0 40px 100px rgba(0,0,0,.35), 0 0 80px var(--accent-glow);
        transform-style: preserve-3d; transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear; z-index: 3; }
    .iv-chat3d::after { content:''; position: absolute; inset: 0; border-radius: 24px; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: ivSheen 6s ease-in-out infinite; }
    @@keyframes ivSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .iv-chat-head { display: flex; align-items: center; gap: 12px; padding-bottom: 16px; margin-bottom: 16px;
        border-bottom: 1px solid var(--border); transform: translateZ(40px); }
    .iv-chat-ava { width: 42px; height: 42px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
        font-size: 18px; color: #fff; background: linear-gradient(135deg,var(--accent),#ec4899); flex-shrink: 0;
        box-shadow: 0 8px 22px var(--accent-glow-strong); }
    .iv-chat-name { font-size: 14px; font-weight: 800; color: var(--text); }
    .iv-chat-online { font-size: 11px; color: #4ade80; display: flex; align-items: center; gap: 5px; margin-top: 2px;
        font-family: var(--font-mono); }
    .iv-chat-online i { width: 6px; height: 6px; border-radius: 50%; background: #22c55e; animation: ivBlink 1.4s infinite; }
    .iv-chat-score { margin-left: auto; text-align: center; background: var(--accent-glow);
        border: 1px solid var(--accent-glow-strong); border-radius: 12px; padding: 6px 12px; }
    .iv-chat-score b { display: block; font-size: 17px; font-weight: 900; color: var(--accent); font-family: var(--font-mono); }
    .iv-chat-score span { font-size: 9px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
    .iv-msg { max-width: 88%; padding: 12px 16px; border-radius: 18px; font-size: 13px; line-height: 1.55;
        margin-bottom: 10px; opacity: 0; transform: translateY(12px); animation: ivMsgIn .5s ease forwards; }
    .iv-msg:nth-of-type(1) { animation-delay: .4s; } .iv-msg:nth-of-type(2) { animation-delay: 1s; }
    .iv-msg:nth-of-type(3) { animation-delay: 1.6s; } .iv-msg:nth-of-type(4) { animation-delay: 2.2s; }
    @@keyframes ivMsgIn { to { opacity: 1; transform: none; } }
    .iv-msg.ai { background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text);
        border-bottom-left-radius: 6px; align-self: flex-start; transform: translateZ(25px); }
    .iv-msg.me { background: linear-gradient(135deg,var(--accent),#ec4899); color: #fff;
        border-bottom-right-radius: 6px; margin-left: auto; transform: translateZ(45px); }
    .iv-msg .who { display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
        opacity: .65; margin-bottom: 4px; }
    .iv-typing { display: inline-flex; gap: 4px; padding: 13px 17px; border-radius: 18px; border-bottom-left-radius: 6px;
        background: var(--bg-secondary); border: 1px solid var(--border); margin-bottom: 4px;
        opacity: 0; animation: ivMsgIn .5s ease 2.8s forwards; }
    .iv-typing span { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); animation: ivTp 1.2s ease-in-out infinite; }
    .iv-typing span:nth-child(2) { animation-delay: .15s; } .iv-typing span:nth-child(3) { animation-delay: .3s; }
    @@keyframes ivTp { 0%,60%,100% { transform: translateY(0); opacity: .4; } 30% { transform: translateY(-5px); opacity: 1; } }
    .iv-float-chip { position: absolute; z-index: 4; display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent); backdrop-filter: blur(18px);
        border: 1px solid var(--border); border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3); font-size: 13px; animation: ivFloatY 4.5s ease-in-out infinite; }
    @@keyframes ivFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .iv-fc-1 { top: 4%; right: -6px; } .iv-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .iv-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .iv-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0; }
    .iv-fc-ico.g { background: rgba(34,197,94,.14); color: #22c55e; }
    .iv-fc-ico.p { background: rgba(236,72,153,.14); color: #ec4899; }
    .iv-fc-ico.a { background: rgba(245,158,11,.14); color: #f59e0b; }
    .iv-fc-txt b { display: block; font-size: 13px; color: var(--text); }
    .iv-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .iv-cube { position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px; display: flex;
        align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        animation: ivCubeFloat 6s ease-in-out infinite; }
    .iv-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,var(--accent),#ec4899); }
    .iv-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#38bdf8,#0369a1); animation-delay: 1.5s; }
    @@keyframes ivCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .iv-scroll-hint { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);
        font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-family: var(--font-mono); }
    .iv-paused .iv-aurora, .iv-paused .iv-orb, .iv-paused .iv-ring, .iv-paused .iv-cube,
    .iv-paused .iv-float-chip { animation-play-state: paused !important; }
    .iv-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .iv-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px;
        height: 8px; border-radius: 4px; background: var(--accent); animation: ivWheel 1.8s ease-in-out infinite; }
    @@keyframes ivWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ SESSIONS ============ */
    .iv-wrap { max-width: 1280px; margin: 0 auto; padding: 70px clamp(20px,4vw,48px) 60px; }
    .iv-cols { display: grid; grid-template-columns: minmax(0,1fr) 340px; gap: 26px; align-items: start; }
    .iv-panel { border-radius: 20px; background: var(--card); border: 1px solid var(--border);
        overflow: hidden; box-shadow: 0 14px 40px rgba(0,0,0,.1); }
    .iv-panel-head { display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 18px 22px; border-bottom: 1px solid var(--border); flex-wrap: wrap; }
    .iv-panel-title { font-size: 15px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 10px; }
    .iv-panel-title i { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px;
        border-radius: 11px; background: var(--accent-glow); color: var(--accent); font-size: 15px; }
    .iv-new-btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px; border-radius: 12px;
        font-size: 13px; font-weight: 800; border: none; cursor: pointer;
        background: linear-gradient(135deg,var(--accent),#ec4899); color: #fff;
        box-shadow: 0 6px 18px var(--accent-glow-strong); transition: all .25s; }
    .iv-new-btn:hover { transform: translateY(-2px); }
    .iv-list { padding: 10px; }
    .iv-card { display: block; padding: 20px; border-radius: 15px; border: 1px solid var(--border);
        text-decoration: none; margin-bottom: 8px; position: relative; overflow: hidden;
        opacity: 0; transform: translateX(-18px);
        transition: opacity .45s ease, transform .45s cubic-bezier(.16,1,.3,1), border-color .25s, box-shadow .25s; }
    .iv-card.in { opacity: 1; transform: none; }
    .iv-card::before { content:''; position: absolute; top: 0; left: 0; width: 3px; height: 100%;
        background: linear-gradient(180deg,var(--accent),#ec4899); opacity: 0; transition: opacity .3s; }
    .iv-card:hover { border-color: var(--accent); transform: translateX(5px); box-shadow: 0 8px 26px rgba(0,0,0,.1); }
    .iv-card:hover::before { opacity: 1; }
    .iv-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .iv-card-title { font-size: 15px; font-weight: 800; color: var(--text); }
    .iv-card:hover .iv-card-title { color: var(--accent); }
    .iv-card-tags { display: flex; gap: 7px; margin-top: 11px; flex-wrap: wrap; }
    .iv-tag { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 8px;
        font-size: 11px; font-weight: 700; font-family: var(--font-mono); }
    .iv-tag-type { background: var(--accent-glow); color: var(--accent); }
    .iv-tag-diff-easy { background: rgba(34,197,94,.1); color: #22c55e; }
    .iv-tag-diff-medium { background: rgba(234,179,8,.1); color: #eab308; }
    .iv-tag-diff-hard { background: rgba(239,68,68,.1); color: #ef4444; }
    .iv-tag-time { background: var(--bg-secondary); color: var(--text-muted); }
    .iv-status { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border-radius: 10px;
        font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap;
        flex-shrink: 0; font-family: var(--font-mono); }
    .iv-status-completed { background: rgba(34,197,94,.12); color: #22c55e; }
    .iv-status-in_progress { background: rgba(234,179,8,.12); color: #eab308; }
    .iv-status-pending { background: var(--bg-secondary); color: var(--text-muted); }
    .iv-score { display: inline-flex; align-items: center; gap: 7px; margin-top: 12px; padding: 7px 14px;
        border-radius: 10px; background: var(--accent-glow); font-size: 13px; font-weight: 800; color: var(--accent);
        font-family: var(--font-mono); }
    .iv-empty { text-align: center; padding: 64px 24px; color: var(--text-muted); font-family: var(--font-mono); }
    .iv-empty i { font-size: 46px; margin-bottom: 16px; display: block; opacity: .3; }
    .iv-side { display: flex; flex-direction: column; gap: 18px; position: sticky; top: 96px; }
    .iv-tips { padding: 8px 24px 18px; }
    .iv-tip { display: flex; align-items: flex-start; gap: 13px; padding: 13px 0; border-bottom: 1px solid var(--border); }
    .iv-tip:last-child { border-bottom: none; }
    .iv-tip-ico { display: inline-flex; align-items: center; justify-content: center; width: 37px; height: 37px;
        border-radius: 11px; font-size: 14px; flex-shrink: 0; font-weight: 800; }
    .iv-tip-txt { font-size: 13.5px; color: var(--text-secondary); line-height: 1.6; }
    .iv-peer { display: flex; align-items: center; justify-content: center; gap: 10px; padding: 16px; border-radius: 16px;
        background: var(--card); border: 2px dashed var(--border); color: var(--text); font-size: 14px; font-weight: 800;
        text-decoration: none; transition: all .3s; }
    .iv-peer:hover { border-color: #22c55e; color: #22c55e; transform: translateY(-2px); }

    .iv-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.6); backdrop-filter: blur(6px);
        display: none; align-items: center; justify-content: center; z-index: 200; padding: 24px; }
    .iv-modal-overlay.open { display: flex; }
    .iv-modal { background: var(--card); border: 1px solid var(--border); border-radius: 22px; width: 100%;
        max-width: 440px; box-shadow: 0 25px 60px rgba(0,0,0,.3); animation: ivModalIn .25s cubic-bezier(.16,1,.3,1); overflow: hidden; }
    @@keyframes ivModalIn { from { opacity: 0; transform: scale(.94) translateY(12px); } to { opacity: 1; transform: none; } }
    .iv-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 22px 24px;
        border-bottom: 1px solid var(--border); }
    .iv-modal-title { font-size: 17px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 10px; }
    .iv-modal-close { width: 36px; height: 36px; border-radius: 10px; border: none; background: var(--bg-secondary);
        color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .2s; }
    .iv-modal-close:hover { background: var(--accent-glow); color: var(--accent); }
    .iv-modal-body { padding: 22px 24px; }
    .iv-form-group { margin-bottom: 16px; }
    .iv-form-label { display: block; font-size: 13px; font-weight: 700; color: var(--text-secondary); margin-bottom: 8px; }
    .iv-form-select { width: 100%; box-sizing: border-box; padding: 12px 16px; border-radius: 12px;
        border: 1px solid var(--border); background: var(--bg-secondary); color: var(--text); font-size: 14px;
        outline: none; transition: all .2s; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 14px center; }
    .iv-form-select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
    .iv-modal-foot { display: flex; gap: 10px; padding: 0 24px 22px; }
    .iv-btn-go { flex: 1; padding: 13px; border-radius: 13px; font-size: 14px; font-weight: 800; border: none;
        background: linear-gradient(135deg,var(--accent),#ec4899); color: #fff; cursor: pointer; transition: all .25s;
        box-shadow: 0 6px 18px var(--accent-glow-strong); }
    .iv-btn-go:hover { transform: translateY(-2px); }
    .iv-btn-cancel { padding: 13px 20px; border-radius: 13px; font-size: 14px; font-weight: 700;
        border: 1px solid var(--border); background: var(--bg-secondary); color: var(--text-muted); cursor: pointer; transition: all .2s; }
    .iv-btn-cancel:hover { border-color: var(--accent); color: var(--accent); }

    @@media(max-width: 1020px) {
        .iv-hero3d-inner { grid-template-columns: 1fr; }
        .iv-stage { height: 520px; }
        .iv-fc-3 { right: 0; } .iv-fc-1 { right: 0; } .iv-fc-2 { left: 0; }
        .iv-cols { grid-template-columns: 1fr; }
        .iv-side { position: static; }
    }
</style>
@endsection

@section('content')
<div class="iv-page">
{{-- ================= HERO 3D ================= --}}
<section class="iv-hero3d" id="ivHero">
    <div class="iv-hero3d-bg">
        <div class="iv-aurora"></div>
        <div class="iv-grid3d" data-depth="18"></div>
        <div class="iv-orb iv-orb-1" data-depth="40"></div>
        <div class="iv-orb iv-orb-2" data-depth="-30"></div>
        <div class="iv-orb iv-orb-3" data-depth="60"></div>
    </div>

    <div class="iv-hero3d-inner">
        <div>
            <span class="iv-eyebrow"><i></i>{{ __('AI interviewer online') }}</span>
            <h1 class="iv-title">{!! __('Ace Your<br><span class="grad">Interview</span>') !!}</h1>
            <p class="iv-sub">{!! __('Rehearse with an <b>AI interviewer</b> — technical, behavioral, live coding. Get a <b>score</b> and know exactly what to fix.') !!}</p>

            <div class="iv-hero-actions">
                <button onclick="document.getElementById('iv-modal').classList.add('open')" class="iv-btn iv-btn-start"><i class="fas fa-microphone"></i>{{ __('interview_prep_new') }}</button>
                <a href="{{ route('peer.index') }}" class="iv-btn iv-btn-ghost"><i class="fas fa-users"></i>{{ __('interview_prep_peer') }}</a>
            </div>

            <div class="iv-stats3d">
                <div class="iv-stat"><div class="iv-stat-val" data-count="{{ $interviews->count() }}">0</div><div class="iv-stat-label">{{ __('interview_prep_total') }}</div></div>
                <div class="iv-stat"><div class="iv-stat-val" data-count="{{ $interviews->where('status', 'completed')->count() }}">0</div><div class="iv-stat-label">{{ __('interview_prep_completed') }}</div></div>
                <div class="iv-stat"><div class="iv-stat-val" data-count="{{ $interviews->where('status', 'in_progress')->count() }}">0</div><div class="iv-stat-label">{{ __('interview_prep_in_progress') }}</div></div>
            </div>
        </div>

        <div class="iv-stage">
            <div class="iv-ring iv-ring-1"><span class="iv-ring-dot"></span></div>
            <div class="iv-ring iv-ring-2"><span class="iv-ring-dot" style="background:#ec4899;box-shadow:0 0 14px #ec4899"></span></div>
            <div class="iv-cube iv-cube-1" data-depth="70"><i class="fas fa-microphone"></i></div>
            <div class="iv-cube iv-cube-2" data-depth="-60"><i class="fas fa-comments"></i></div>

            <div class="iv-chat3d" id="ivChat">
                <div class="iv-chat-head">
                    <div class="iv-chat-ava"><i class="fas fa-robot"></i></div>
                    <div>
                        <div class="iv-chat-name">AI Interviewer</div>
                        <div class="iv-chat-online"><i></i>asking…</div>
                    </div>
                    <div class="iv-chat-score"><b>8.5</b><span>score</span></div>
                </div>
                <div style="display:flex;flex-direction:column">
                    <div class="iv-msg ai"><span class="who">interviewer</span>{{ __('Explain Big-O of your solution.') }}</div>
                    <div class="iv-msg me"><span class="who">you</span>{{ __('O(n) — single pass with a hash map.') }}</div>
                    <div class="iv-msg ai"><span class="who">interviewer</span>{{ __('Nice. And the memory tradeoff?') }}</div>
                    <div class="iv-typing"><span></span><span></span><span></span></div>
                </div>
            </div>

            <div class="iv-float-chip iv-fc-1" data-depth="50">
                <div class="iv-fc-ico g"><i class="fas fa-check"></i></div>
                <div class="iv-fc-txt"><b>8.5 / 10</b><span>{{ __('avg score') }}</span></div>
            </div>
            <div class="iv-float-chip iv-fc-2" data-depth="-45">
                <div class="iv-fc-ico p"><i class="fas fa-stopwatch"></i></div>
                <div class="iv-fc-txt"><b>{{ __('Live') }}</b><span>{{ __('real-time pressure') }}</span></div>
            </div>
            <div class="iv-float-chip iv-fc-3" data-depth="35">
                <div class="iv-fc-ico a"><i class="fas fa-clipboard-check"></i></div>
                <div class="iv-fc-txt"><b>{{ __('Feedback') }}</b><span>{{ __('what to fix') }}</span></div>
            </div>
        </div>
    </div>

    <div class="iv-scroll-hint"><div class="iv-mouse"></div><span>{{ __('Scroll — sessions') }}</span></div>
</section>

{{-- ================= SESSIONS ================= --}}
<div class="iv-wrap" id="ivList">
    <div class="iv-cols">
        <div class="iv-panel">
            <div class="iv-panel-head">
                <div class="iv-panel-title"><i class="fas fa-user-tie"></i>{{ __('interview_prep_your') }}</div>
                <button onclick="document.getElementById('iv-modal').classList.add('open')" class="iv-new-btn">
                    <i class="fas fa-plus"></i>{{ __('interview_prep_new') }}
                </button>
            </div>
            <div class="iv-list" id="ivGrid">
                @forelse($interviews as $interview)
                @php
                    $statusClass = match($interview->status) { 'completed' => 'iv-status-completed', 'in_progress' => 'iv-status-in_progress', default => 'iv-status-pending' };
                    $diffClass = match($interview->difficulty) { 'easy' => 'iv-tag-diff-easy', 'medium' => 'iv-tag-diff-medium', 'hard' => 'iv-tag-diff-hard', default => 'iv-tag-diff-easy' };
                    $diffIcon = match($interview->difficulty) { 'easy' => 'fa-seedling', 'medium' => 'fa-fire', 'hard' => 'fa-skull', default => 'fa-circle' };
                    $typeIcon = match($interview->type) { 'technical' => 'fa-microchip', 'behavioral' => 'fa-comments', 'coding' => 'fa-code', 'system_design' => 'fa-network-wired', default => 'fa-circle-question' };
                @endphp
                <a href="{{ route('interview.room', $interview->id) }}" class="iv-card" data-i="{{ $loop->index }}">
                    <div class="iv-card-top">
                        <div class="iv-card-title">{{ $interview->title }}</div>
                        <span class="iv-status {{ $statusClass }}">
                            @if($interview->status === 'completed')<i class="fas fa-check-circle"></i>
                            @elseif($interview->status === 'in_progress')<i class="fas fa-circle-notch fa-spin"></i>
                            @else<i class="fas fa-clock"></i>
                            @endif
                            {{ match($interview->status) { 'completed' => __('interview_prep_status_completed'), 'in_progress' => __('interview_prep_status_in_progress'), default => __('interview_prep_status_pending') } }}
                        </span>
                    </div>
                    <div class="iv-card-tags">
                        <span class="iv-tag iv-tag-type"><i class="fas {{ $typeIcon }}"></i>{{ match($interview->type) { 'technical' => __('interview_prep_type_technical'), 'behavioral' => __('interview_prep_type_behavioral'), 'coding' => __('interview_prep_type_coding'), 'system_design' => __('interview_prep_type_system_design'), default => ucfirst(str_replace('_', ' ', $interview->type)) } }}</span>
                        <span class="iv-tag {{ $diffClass }}"><i class="fas {{ $diffIcon }}"></i>{{ match($interview->difficulty) { 'easy' => __('interview_prep_diff_easy'), 'medium' => __('interview_prep_diff_medium'), 'hard' => __('interview_prep_diff_hard'), default => ucfirst($interview->difficulty) } }}</span>
                        <span class="iv-tag iv-tag-time"><i class="far fa-clock"></i>{{ $interview->created_at->diffForHumans() }}</span>
                    </div>
                    @if($interview->score !== null)
                    <div class="iv-score"><i class="fas fa-star"></i>{{ __('interview_prep_score') }} {{ $interview->score }}%</div>
                    @endif
                </a>
                @empty
                <div class="iv-empty">
                    <i class="fas fa-user-tie"></i>
                    <p>{{ __('interview_prep_empty') }}</p>
                    <small>{{ __('interview_prep_empty_hint') }}</small>
                </div>
                @endforelse
            </div>
        </div>

        <div class="iv-side">
            <div class="iv-panel">
                <div class="iv-panel-head">
                    <div class="iv-panel-title"><i class="fas fa-lightbulb"></i>{{ __('interview_prep_tips') }}</div>
                </div>
                <div class="iv-tips">
                    <div class="iv-tip">
                        <div class="iv-tip-ico" style="background:rgba(234,179,8,.12);color:#eab308"><i class="fas fa-comment-dots"></i></div>
                        <div class="iv-tip-txt">{{ __('interview_prep_tip_1') }}</div>
                    </div>
                    <div class="iv-tip">
                        <div class="iv-tip-ico" style="background:rgba(139,92,246,.12);color:#8b5cf6"><i class="fas fa-database"></i></div>
                        <div class="iv-tip-txt">{{ __('interview_prep_tip_2') }}</div>
                    </div>
                    <div class="iv-tip">
                        <div class="iv-tip-ico" style="background:rgba(34,197,94,.12);color:#22c55e"><i class="fas fa-pen-to-square"></i></div>
                        <div class="iv-tip-txt">{{ __('interview_prep_tip_3') }}</div>
                    </div>
                    <div class="iv-tip">
                        <div class="iv-tip-ico" style="background:rgba(59,130,246,.12);color:#3b82f6"><i class="fas fa-building"></i></div>
                        <div class="iv-tip-txt">{{ __('interview_prep_tip_4') }}</div>
                    </div>
                </div>
            </div>
            <a href="{{ route('peer.index') }}" class="iv-peer"><i class="fas fa-users"></i>{{ __('interview_prep_peer') }}</a>
        </div>
    </div>
</div>

<div id="iv-modal" class="iv-modal-overlay" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="iv-modal">
        <div class="iv-modal-head">
            <div class="iv-modal-title"><i class="fas fa-microphone" style="color:var(--accent)"></i>{{ __('interview_prep_new_title') }}</div>
            <button class="iv-modal-close" onclick="document.getElementById('iv-modal').classList.remove('open')"><i class="fas fa-xmark"></i></button>
        </div>
        <form action="{{ route('interview.store') }}" method="POST">
            @csrf
            <div class="iv-modal-body">
                <div class="iv-form-group">
                    <label class="iv-form-label">{{ __('interview_prep_type_label') }}</label>
                    <select name="type" required class="iv-form-select">
                        <option value="technical">{{ __('interview_prep_type_technical') }}</option>
                        <option value="behavioral">{{ __('interview_prep_type_behavioral') }}</option>
                        <option value="coding">{{ __('interview_prep_type_coding') }}</option>
                        <option value="system_design">{{ __('interview_prep_type_system_design') }}</option>
                    </select>
                </div>
                <div class="iv-form-group">
                    <label class="iv-form-label">{{ __('interview_prep_diff_label') }}</label>
                    <select name="difficulty" required class="iv-form-select">
                        <option value="easy">{{ __('interview_prep_diff_easy') }}</option>
                        <option value="medium">{{ __('interview_prep_diff_medium') }}</option>
                        <option value="hard">{{ __('interview_prep_diff_hard') }}</option>
                    </select>
                </div>
            </div>
            <div class="iv-modal-foot">
                <button type="submit" class="iv-btn-go"><i class="fas fa-play"></i>{{ __('interview_prep_start') }}</button>
                <button type="button" onclick="document.getElementById('iv-modal').classList.remove('open')" class="iv-btn-cancel">{{ __('interview_prep_cancel') }}</button>
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
    var hero = document.getElementById('ivHero');
    var chat = document.getElementById('ivChat');
    var layers = document.querySelectorAll('#ivHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0, heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('iv-paused', !heroVisible);
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
                if (chat) chat.style.transform = 'rotateX(' + (6 + rx * -1).toFixed(2) + 'deg) rotateY(' + (-10 + ry).toFixed(2) + 'deg)';
            }
            requestAnimationFrame(tilt);
        })();
    }

    /* --- Animated counters --- */
    document.querySelectorAll('.iv-stat-val[data-count]').forEach(function(el) {
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

    /* --- Cards slide-in reveal --- */
    var cards = document.querySelectorAll('#ivGrid .iv-card');
    if ('IntersectionObserver' in window && cards.length) {
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
        cards.forEach(function(c) { io.observe(c); });
        setTimeout(function() { cards.forEach(function(c) { c.classList.add('in'); }); }, 4000);
    } else {
        cards.forEach(function(c) { c.classList.add('in'); });
    }

    /* --- Escape closes modal --- */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var m = document.getElementById('iv-modal');
            if (m) m.classList.remove('open');
        }
    });
})();
</script>
@endsection
