@extends('layouts.app')

@section('title', __('Vacancies') . ' - CodeMaster')

@section('head')
<style>
    /* ============ VACANCIES: OFFER THEME + 3D HERO ============ */
    .vc-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .vc-hero3d { position: relative; min-height: 100vh; display: flex; align-items: center;
        overflow: hidden; padding: 110px clamp(20px,4vw,56px) 90px; isolation: isolate; perspective: 1600px; }
    .vc-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .vc-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(16,185,129,.12) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(59,130,246,.12) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(139,92,246,.10) 0%, transparent 60%);
        animation: vcAurora 22s ease-in-out infinite alternate; }
    @@keyframes vcAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); } }
    .vc-grid3d { position: absolute; inset: -50%;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px; opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2); transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%); }
    .vc-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .vc-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: vcOrb1 16s ease-in-out infinite; }
    .vc-orb-2 { width: 460px; height: 460px; background: #10b981; opacity: .10; bottom: -18%; right: -6%; animation: vcOrb2 20s ease-in-out infinite; }
    .vc-orb-3 { width: 260px; height: 260px; background: #3b82f6; opacity: .10; top: 55%; left: 42%; animation: vcOrb3 12s ease-in-out infinite; }
    @@keyframes vcOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes vcOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes vcOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }
    @@keyframes vcBlink { 0%,100%{opacity:1} 50%{opacity:.35} }

    .vc-hero3d-inner { position: relative; z-index: 2; width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px,5vw,72px); align-items: center; }
    .vc-eyebrow { display: inline-flex; align-items: center; gap: 10px; padding: 8px 18px; border-radius: 100px;
        background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3);
        font-size: 11px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase; color: #34d399; margin-bottom: 22px; }
    .vc-eyebrow i { width: 7px; height: 7px; border-radius: 50%; background: #10b981; animation: vcBlink 1.6s infinite;
        box-shadow: 0 0 10px #10b981; }
    .vc-title { font-size: clamp(42px,6.2vw,84px); font-weight: 900; line-height: .95; letter-spacing: -3px;
        margin: 0 0 18px; color: var(--text); }
    .vc-title .grad { background: linear-gradient(120deg, #10b981, var(--accent) 55%, #3b82f6 90%); background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: vcGradShift 7s ease-in-out infinite; display: inline-block;
        filter: drop-shadow(0 8px 30px rgba(16,185,129,.22)); }
    @@keyframes vcGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    .vc-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.75; max-width: 540px; margin-bottom: 24px; }
    .vc-sub b { color: var(--text); }

    .vc-search3d { position: relative; max-width: 520px; margin-bottom: 14px; }
    .vc-search3d input { width: 100%; box-sizing: border-box; padding: 16px 20px 16px 52px; border-radius: 18px;
        border: 1px solid var(--border); background: var(--card); color: var(--text); font-size: 14px; outline: none;
        box-shadow: 0 12px 40px rgba(0,0,0,.18), inset 0 1px 0 rgba(255,255,255,.06);
        transition: border-color .3s, box-shadow .3s; }
    .vc-search3d input:focus { border-color: #10b981; box-shadow: 0 12px 40px rgba(0,0,0,.2), 0 0 0 4px rgba(16,185,129,.15); }
    .vc-search3d > i { position: absolute; left: 19px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
    .vc-search3d kbd { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-family: var(--font-mono);
        font-size: 11px; color: var(--text-muted); border: 1px solid var(--border); border-radius: 8px;
        padding: 4px 8px; background: var(--bg-secondary); }
    .vc-filters3d { display: flex; flex-wrap: wrap; gap: 8px; max-width: 560px; margin-bottom: 26px; }
    .vc-chip { display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 100px;
        font-size: 13px; font-weight: 700; border: 1px solid var(--border); background: var(--card);
        color: var(--text-secondary); text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1); }
    .vc-chip:hover { transform: translateY(-2px); border-color: #10b981; color: #10b981; }
    .vc-chip.active { background: linear-gradient(135deg,#10b981,#3b82f6); color: #fff; border-color: transparent;
        box-shadow: 0 8px 28px rgba(16,185,129,.35); }

    .vc-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
    .vc-btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 26px; border-radius: 16px;
        font-weight: 800; font-size: 14px; text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
        border: 1px solid transparent; cursor: pointer; }
    .vc-btn-go { background: linear-gradient(135deg,#10b981,#3b82f6); color: #fff; box-shadow: 0 10px 32px rgba(16,185,129,.35); }
    .vc-btn-go:hover { transform: translateY(-3px) scale(1.02); }
    .vc-stats3d { display: flex; gap: clamp(20px,3.5vw,44px); flex-wrap: wrap; }
    .vc-stat { position: relative; }
    .vc-stat + .vc-stat::before { content:''; position: absolute; left: -22px; top: 50%; transform: translateY(-50%);
        width: 1px; height: 38px; background: var(--border); }
    .vc-stat-val { font-size: clamp(26px,3vw,38px); font-weight: 900; line-height: 1; font-variant-numeric: tabular-nums;
        background: linear-gradient(135deg,var(--text),#10b981); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; }
    .vc-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px;
        margin-top: 6px; font-weight: 600; }

    /* --- 3D offer-letter stage --- */
    .vc-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .vc-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .vc-ring-1 { width: 480px; height: 480px; animation: vcSpin 26s linear infinite; opacity: .7; }
    .vc-ring-2 { width: 590px; height: 590px; animation: vcSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes vcSpin { to { transform: rotate(360deg); } }
    @@keyframes vcSpinRev { to { transform: rotate(-360deg); } }
    .vc-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: #10b981;
        box-shadow: 0 0 14px #10b981; top: -5px; left: 50%; }
    .vc-offer3d { position: relative; width: 100%; max-width: 460px; padding: 26px;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid var(--border); border-radius: 24px;
        box-shadow: 0 40px 100px rgba(0,0,0,.35), 0 0 80px rgba(16,185,129,.12);
        transform-style: preserve-3d; transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear; z-index: 3; }
    .vc-offer3d::after { content:''; position: absolute; inset: 0; border-radius: 24px; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: vcSheen 6s ease-in-out infinite; }
    @@keyframes vcSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .vc-offer-top { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; transform: translateZ(40px); }
    .vc-offer-logo { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center;
        font-size: 20px; font-weight: 900; color: #fff; background: linear-gradient(135deg,#10b981,#3b82f6);
        box-shadow: 0 10px 26px rgba(16,185,129,.4); flex-shrink: 0; }
    .vc-offer-role { font-size: 17px; font-weight: 800; color: var(--text); }
    .vc-offer-co { font-size: 12px; color: var(--text-muted); margin-top: 3px; }
    .vc-offer-salary { font-family: var(--font-mono); font-size: 26px; font-weight: 800; margin-bottom: 4px;
        background: linear-gradient(135deg,#10b981,#3b82f6); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; transform: translateZ(30px); }
    .vc-offer-per { font-size: 11px; color: var(--text-muted); font-family: var(--font-mono); margin-bottom: 16px; }
    .vc-match { margin-bottom: 18px; transform: translateZ(25px); }
    .vc-match-row { display: flex; justify-content: space-between; font-size: 11px; font-weight: 800;
        font-family: var(--font-mono); color: var(--text-muted); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 1px; }
    .vc-match-row b { color: #34d399; }
    .vc-match-bar { height: 9px; border-radius: 5px; background: var(--border); overflow: hidden; }
    .vc-match-fill { height: 100%; width: 0; border-radius: 5px; background: linear-gradient(90deg,#10b981,#3b82f6);
        box-shadow: 0 0 12px rgba(16,185,129,.6); animation: vcMatch 3s cubic-bezier(.16,1,.3,1) .5s forwards; }
    @@keyframes vcMatch { to { width: 94%; } }
    .vc-offer-tags { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 18px; transform: translateZ(20px); }
    .vc-offer-tag { padding: 6px 13px; border-radius: 9px; font-size: 11px; font-weight: 700;
        background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text-secondary); font-family: var(--font-mono); }
    .vc-accept { display: flex; align-items: center; justify-content: center; gap: 9px; width: 100%; padding: 14px;
        border: none; border-radius: 15px; font-size: 14px; font-weight: 800; color: #fff; cursor: pointer;
        background: linear-gradient(135deg,#10b981,#059669); box-shadow: 0 10px 28px rgba(16,185,129,.4);
        transition: all .25s; transform: translateZ(35px); }
    .vc-accept:hover { transform: translateZ(35px) translateY(-2px) scale(1.01); }
    .vc-stamp { position: absolute; top: 18px; right: 20px; z-index: 4; padding: 7px 14px; border-radius: 10px;
        border: 2px solid #10b981; color: #34d399; font-size: 11px; font-weight: 900; letter-spacing: 2px;
        transform: translateZ(60px) rotate(8deg); background: rgba(16,185,129,.08); font-family: var(--font-mono); }
    .vc-float-chip { position: absolute; z-index: 4; display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent); backdrop-filter: blur(18px);
        border: 1px solid var(--border); border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3); font-size: 13px; animation: vcFloatY 4.5s ease-in-out infinite; }
    @@keyframes vcFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .vc-fc-1 { top: 4%; right: -6px; } .vc-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .vc-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .vc-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0; }
    .vc-fc-ico.g { background: rgba(16,185,129,.14); color: #10b981; }
    .vc-fc-ico.p { background: rgba(59,130,246,.14); color: #3b82f6; }
    .vc-fc-ico.a { background: rgba(245,158,11,.14); color: #f59e0b; }
    .vc-fc-txt b { display: block; font-size: 13px; color: var(--text); font-family: var(--font-mono); }
    .vc-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .vc-cube { position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px; display: flex;
        align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        animation: vcCubeFloat 6s ease-in-out infinite; }
    .vc-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,#10b981,#059669); }
    .vc-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#3b82f6,#1d4ed8); animation-delay: 1.5s; }
    @@keyframes vcCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .vc-scroll-hint { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);
        font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-family: var(--font-mono); }
    .vc-paused .vc-aurora, .vc-paused .vc-orb, .vc-paused .vc-ring, .vc-paused .vc-cube,
    .vc-paused .vc-float-chip { animation-play-state: paused !important; }
    .vc-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .vc-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px;
        height: 8px; border-radius: 4px; background: #10b981; animation: vcWheel 1.8s ease-in-out infinite; }
    @@keyframes vcWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ OFFERS LIST ============ */
    .vc-wrap { max-width: 1280px; margin: 0 auto; padding: 70px clamp(20px,4vw,48px) 90px; }
    .vc-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 20px; }
    .vc-card { border-radius: 22px; background: var(--card); border: 1px solid var(--border); padding: 26px;
        position: relative; overflow: hidden;
        opacity: 0; transform: translateY(30px);
        transition: opacity .5s ease, transform .5s cubic-bezier(.16,1,.3,1), border-color .3s, box-shadow .3s; }
    .vc-card.in { opacity: 1; transform: none; }
    .vc-card::before { content:''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg,#10b981,#3b82f6); opacity: 0; transition: opacity .3s; }
    .vc-card:hover { border-color: #10b981; transform: translateY(-5px); box-shadow: 0 18px 46px rgba(0,0,0,.14); }
    .vc-card:hover::before { opacity: 1; }
    .vc-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
    .vc-co { display: flex; align-items: center; gap: 13px; min-width: 0; }
    .vc-logo { width: 52px; height: 52px; border-radius: 15px; display: flex; align-items: center; justify-content: center;
        font-weight: 900; font-size: 16px; flex-shrink: 0; transition: transform .3s; font-family: var(--font-mono); }
    .vc-card:hover .vc-logo { transform: scale(1.08) rotate(-3deg); }
    .vc-job { font-size: 17px; font-weight: 800; color: var(--text); margin-bottom: 4px; line-height: 1.3; }
    .vc-card:hover .vc-job { color: #10b981; }
    .vc-meta { font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 7px; flex-wrap: wrap; }
    .vc-type { padding: 6px 13px; border-radius: 10px; font-size: 10px; font-weight: 800; text-transform: uppercase;
        letter-spacing: .6px; white-space: nowrap; flex-shrink: 0; font-family: var(--font-mono); }
    .vc-type-remote { background: rgba(16,185,129,.1); color: #10b981; border: 1px solid rgba(16,185,129,.25); }
    .vc-type-hybrid { background: rgba(234,179,8,.1); color: #eab308; border: 1px solid rgba(234,179,8,.25); }
    .vc-type-office { background: rgba(59,130,246,.1); color: #3b82f6; border: 1px solid rgba(59,130,246,.25); }
    .vc-new { position: absolute; top: 16px; right: 16px; padding: 5px 11px; border-radius: 9px; font-size: 10px;
        font-weight: 900; letter-spacing: 1px; background: linear-gradient(135deg,#10b981,#059669); color: #fff;
        box-shadow: 0 4px 14px rgba(16,185,129,.4); font-family: var(--font-mono); }
    .vc-has-new .vc-type { margin-top: 26px; }
    .vc-salary { font-size: 16px; font-weight: 800; margin-bottom: 14px; display: flex; align-items: center; gap: 9px;
        font-family: var(--font-mono); }
    .vc-skills { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 18px; }
    .vc-skill { padding: 5px 13px; border-radius: 9px; font-size: 12px; font-weight: 600; background: var(--bg-secondary);
        color: var(--text-secondary); border: 1px solid var(--border); transition: all .2s; font-family: var(--font-mono); }
    .vc-card:hover .vc-skill { border-color: #10b981; color: #10b981; }
    .vc-bottom { display: flex; align-items: center; justify-content: space-between; padding-top: 16px;
        border-top: 1px solid var(--border); }
    .vc-time { font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; font-family: var(--font-mono); }
    .vc-view { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 12px;
        font-size: 13px; font-weight: 800; text-decoration: none; color: #10b981; border: 1.5px solid #10b981;
        background: transparent; transition: all .3s; }
    .vc-view:hover { background: linear-gradient(135deg,#10b981,#059669); color: #fff; border-color: transparent;
        box-shadow: 0 6px 18px rgba(16,185,129,.4); transform: translateY(-1px); }
    .vc-empty { text-align: center; padding: 80px 24px; grid-column: 1/-1; color: var(--text-muted); font-family: var(--font-mono); }
    .vc-empty i { font-size: 52px; margin-bottom: 18px; display: block; opacity: .3; }
    .vc-pag { display: flex; align-items: center; justify-content: center; gap: 8px; padding-top: 30px; flex-wrap: wrap; }
    .vc-pg { min-width: 42px; height: 42px; padding: 0 13px; display: inline-flex; align-items: center; justify-content: center;
        border-radius: 12px; border: 1px solid var(--border); background: var(--card); color: var(--text-muted);
        font-weight: 700; font-size: 13px; text-decoration: none; transition: all .3s; font-family: var(--font-mono); }
    .vc-pg:hover { border-color: #10b981; color: #10b981; transform: translateY(-2px); }
    .vc-pg.on { background: linear-gradient(135deg,#10b981,#3b82f6); color: #fff; border-color: transparent;
        box-shadow: 0 4px 16px rgba(16,185,129,.4); }
    .vc-pg.dis { opacity: .35; pointer-events: none; }

    @@media(max-width: 1020px) {
        .vc-hero3d-inner { grid-template-columns: 1fr; }
        .vc-stage { height: 500px; }
        .vc-fc-3 { right: 0; } .vc-fc-1 { right: 0; } .vc-fc-2 { left: 0; }
    }
    @@media(max-width: 768px) { .vc-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="vc-page">
{{-- ================= HERO 3D ================= --}}
<section class="vc-hero3d" id="vcHero">
    <div class="vc-hero3d-bg">
        <div class="vc-aurora"></div>
        <div class="vc-grid3d" data-depth="18"></div>
        <div class="vc-orb vc-orb-1" data-depth="40"></div>
        <div class="vc-orb vc-orb-2" data-depth="-30"></div>
        <div class="vc-orb vc-orb-3" data-depth="60"></div>
    </div>

    <div class="vc-hero3d-inner">
        <div>
            <span class="vc-eyebrow"><i></i>{{ __('Hiring now') }} • {{ $vacancies->total() }}</span>
            <h1 class="vc-title">{!! __('Land Your<br><span class="grad">Dream Offer</span>') !!}</h1>
            <p class="vc-sub">{!! __('Top companies hunt for <b>developers</b> like you. Filter by format, compare <b>salaries</b>, hit apply.') !!}</p>

            <form action="{{ route('vacancies.index') }}" method="GET" class="vc-search3d">
                @foreach(['type', 'location', 'skill', 'salary_min', 'salary_max'] as $fk)
                    @if(request($fk) !== null && request($fk) !== '')
                    <input type="hidden" name="{{ $fk }}" value="{{ request($fk) }}">
                    @endif
                @endforeach
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search role, skill or company...') }}" autocomplete="off">
                <i class="fas fa-search"></i>
                <kbd>⌘K</kbd>
            </form>

            @php $currentType = request('type'); @endphp
            <div class="vc-filters3d">
                <a href="{{ route('vacancies.index', array_merge(request()->except('type', 'page'))) }}" class="vc-chip {{ !$currentType ? 'active' : '' }}"><i class="fas fa-layer-group"></i>{{ __('All') }}</a>
                <a href="{{ route('vacancies.index', array_merge(request()->except('type', 'page'), ['type' => 'remote'])) }}" class="vc-chip {{ $currentType === 'remote' ? 'active' : '' }}"><i class="fas fa-wifi"></i>{{ __('Remote') }}</a>
                <a href="{{ route('vacancies.index', array_merge(request()->except('type', 'page'), ['type' => 'office'])) }}" class="vc-chip {{ $currentType === 'office' ? 'active' : '' }}"><i class="fas fa-building"></i>{{ __('Office') }}</a>
                <a href="{{ route('vacancies.index', array_merge(request()->except('type', 'page'), ['type' => 'hybrid'])) }}" class="vc-chip {{ $currentType === 'hybrid' ? 'active' : '' }}"><i class="fas fa-shuffle"></i>{{ __('Hybrid') }}</a>
            </div>

            <div class="vc-hero-actions">
                <a href="#vcList" class="vc-btn vc-btn-go" id="vcToList"><i class="fas fa-briefcase"></i>{{ __('Browse offers') }}</a>
            </div>

            <div class="vc-stats3d">
                <div class="vc-stat"><div class="vc-stat-val" data-count="{{ $vacancies->total() }}">0</div><div class="vc-stat-label">{{ __('Positions') }}</div></div>
                <div class="vc-stat"><div class="vc-stat-val" data-count="{{ $stats['companies'] ?? 0 }}">0</div><div class="vc-stat-label">{{ __('Companies') }}</div></div>
                <div class="vc-stat"><div class="vc-stat-val" data-count="{{ $stats['remote'] ?? 0 }}">0</div><div class="vc-stat-label">{{ __('Remote') }}</div></div>
            </div>
        </div>

        <div class="vc-stage">
            <div class="vc-ring vc-ring-1"><span class="vc-ring-dot"></span></div>
            <div class="vc-ring vc-ring-2"><span class="vc-ring-dot" style="background:#3b82f6;box-shadow:0 0 14px #3b82f6"></span></div>
            <div class="vc-cube vc-cube-1" data-depth="70"><i class="fas fa-briefcase"></i></div>
            <div class="vc-cube vc-cube-2" data-depth="-60"><i class="fas fa-building"></i></div>

            <div class="vc-offer3d" id="vcOffer">
                <span class="vc-stamp">OFFER</span>
                <div class="vc-offer-top">
                    <div class="vc-offer-logo">TC</div>
                    <div>
                        <div class="vc-offer-role">Senior Laravel Dev</div>
                        <div class="vc-offer-co"><i class="fas fa-building" style="margin-right:5px"></i>TechCorp • <i class="fas fa-wifi" style="margin:0 4px"></i>Remote</div>
                    </div>
                </div>
                <div class="vc-offer-salary">$4 500 – $6 000</div>
                <div class="vc-offer-per">per month • full-time • equity</div>
                <div class="vc-match">
                    <div class="vc-match-row"><span>skill_match</span><b>94%</b></div>
                    <div class="vc-match-bar"><div class="vc-match-fill"></div></div>
                </div>
                <div class="vc-offer-tags">
                    <span class="vc-offer-tag">Laravel</span>
                    <span class="vc-offer-tag">Vue</span>
                    <span class="vc-offer-tag">MySQL</span>
                    <span class="vc-offer-tag">Docker</span>
                </div>
                <button type="button" class="vc-accept" onclick="vcAccept(this)"><i class="fas fa-paper-plane"></i>{{ __('Accept offer') }}</button>
            </div>

            <div class="vc-float-chip vc-fc-1" data-depth="50">
                <div class="vc-fc-ico g"><i class="fas fa-sack-dollar"></i></div>
                <div class="vc-fc-txt"><b>$6k+</b><span>{{ __('top salaries') }}</span></div>
            </div>
            <div class="vc-float-chip vc-fc-2" data-depth="-45">
                <div class="vc-fc-ico p"><i class="fas fa-wifi"></i></div>
                <div class="vc-fc-txt"><b>{{ __('Remote') }}</b><span>{{ __('work from anywhere') }}</span></div>
            </div>
            <div class="vc-float-chip vc-fc-3" data-depth="35">
                <div class="vc-fc-ico a"><i class="fas fa-handshake"></i></div>
                <div class="vc-fc-txt"><b>{{ __('1-click') }}</b><span>{{ __('fast apply') }}</span></div>
            </div>
        </div>
    </div>

    <div class="vc-scroll-hint"><div class="vc-mouse"></div><span>{{ __('Scroll — offers') }}</span></div>
</section>

{{-- ================= OFFERS LIST ================= --}}
<div class="vc-wrap" id="vcList">
    <div class="vc-grid" id="vcGrid">
        @forelse($vacancies as $vacancy)
        @php
            $typeClass = match($vacancy->type) { 'remote' => 'vc-type-remote', 'hybrid' => 'vc-type-hybrid', default => 'vc-type-office' };
            $typeIcon = match($vacancy->type) { 'remote' => 'fa-wifi', 'hybrid' => 'fa-shuffle', default => 'fa-building' };
            $colors = ['#10b981','#3b82f6','#8b5cf6','#ec4899','#f97316','#eab308','#6366f1'];
            $color = $colors[$vacancy->id % count($colors)];
            $isNew = $vacancy->created_at && $vacancy->created_at->gt(now()->subDays(3));
        @endphp
        <div class="vc-card{{ $isNew ? ' vc-has-new' : '' }}" data-i="{{ $loop->index }}">
            @if($isNew)<span class="vc-new">NEW</span>@endif
            <div class="vc-top">
                <div class="vc-co">
                    <div class="vc-logo" style="background:{{ $color }}18;color:{{ $color }};border:1px solid {{ $color }}33">
                        {{ strtoupper(substr($vacancy->company, 0, 2)) }}
                    </div>
                    <div style="min-width:0">
                        <div class="vc-job">{{ $vacancy->title }}</div>
                        <div class="vc-meta">
                            <span><i class="fas fa-building"></i>{{ $vacancy->company }}</span>
                            <span style="opacity:.4">·</span>
                            <span><i class="fas fa-location-dot"></i>{{ $vacancy->location }}</span>
                        </div>
                    </div>
                </div>
                <span class="vc-type {{ $typeClass }}"><i class="fas {{ $typeIcon }}" style="margin-right:4px"></i>{{ __($vacancy->type) }}</span>
            </div>

            @if($vacancy->salary_min || $vacancy->salary_max)
            <div class="vc-salary" style="color:{{ $color }}">
                <i class="fas fa-coins" style="font-size:13px"></i>
                @if($vacancy->salary_min && $vacancy->salary_max)
                    {{ number_format($vacancy->salary_min) }} – {{ number_format($vacancy->salary_max) }} {{ $vacancy->salary_currency }}
                @elseif($vacancy->salary_min)
                    {{ __('from') }} {{ number_format($vacancy->salary_min) }} {{ $vacancy->salary_currency }}
                @else
                    {{ __('up to') }} {{ number_format($vacancy->salary_max) }} {{ $vacancy->salary_currency }}
                @endif
            </div>
            @endif

            @if($vacancy->vacancySkills->count())
            <div class="vc-skills">
                @foreach($vacancy->vacancySkills as $skill)
                <span class="vc-skill">{{ $skill->skill_name }}</span>
                @endforeach
            </div>
            @endif

            <div class="vc-bottom">
                <div class="vc-time"><i class="fas fa-clock"></i>{{ $vacancy->created_at->diffForHumans() }}</div>
                <a href="{{ route('vacancies.show', $vacancy->id) }}" class="vc-view">{{ __('View') }}<i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        @empty
        <div class="vc-empty">
            <i class="fas fa-briefcase"></i>
                <p>{{ __('no offers found') }} — {{ __('try another query') }}</p>
        </div>
        @endforelse
    </div>

    @if($vacancies->hasPages())
    <div class="vc-pag">
        @if($vacancies->onFirstPage())
        <span class="vc-pg dis"><i class="fas fa-chevron-left"></i></span>
        @else
        <a href="{{ $vacancies->previousPageUrl() }}" class="vc-pg"><i class="fas fa-chevron-left"></i></a>
        @endif
        @foreach($vacancies->getUrlRange(max(1, $vacancies->currentPage() - 2), min($vacancies->lastPage(), $vacancies->currentPage() + 2)) as $page => $url)
        @if($page == $vacancies->currentPage())
        <span class="vc-pg on">{{ $page }}</span>
        @else
        <a href="{{ $url }}" class="vc-pg">{{ $page }}</a>
        @endif
        @endforeach
        @if($vacancies->currentPage() + 2 < $vacancies->lastPage())
        <span style="color:var(--text-muted)">…</span>
        <a href="{{ $vacancies->url($vacancies->lastPage()) }}" class="vc-pg">{{ $vacancies->lastPage() }}</a>
        @endif
        @if($vacancies->hasMorePages())
        <a href="{{ $vacancies->nextPageUrl() }}" class="vc-pg"><i class="fas fa-chevron-right"></i></a>
        @else
        <span class="vc-pg dis"><i class="fas fa-chevron-right"></i></span>
        @endif
    </div>
    @endif
</div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    /* --- Hero mouse 3D parallax --- */
    var hero = document.getElementById('vcHero');
    var offer = document.getElementById('vcOffer');
    var layers = document.querySelectorAll('#vcHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0, heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('vc-paused', !heroVisible);
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
                if (offer) offer.style.transform = 'rotateX(' + (6 + rx * -1).toFixed(2) + 'deg) rotateY(' + (-10 + ry).toFixed(2) + 'deg)';
            }
            requestAnimationFrame(tilt);
        })();
    }

    /* --- Animated counters --- */
    document.querySelectorAll('.vc-stat-val[data-count]').forEach(function(el) {
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

    /* --- Cards stagger reveal --- */
    var cards = document.querySelectorAll('#vcGrid .vc-card');
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
    var toList = document.getElementById('vcToList');
    if (toList) toList.addEventListener('click', function(e) {
        e.preventDefault();
        var t = document.getElementById('vcList');
        if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    /* --- Cmd+K focuses search --- */
    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
            var inp = document.querySelector('.vc-search3d input');
            if (inp) { e.preventDefault(); inp.focus(); inp.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        }
    });
})();

/* Demo accept button on the hero offer */
function vcAccept(btn) {
    if (btn.dataset.orig === undefined) btn.dataset.orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i>{{ __("Offer accepted") }}';
    btn.style.background = 'linear-gradient(135deg,#059669,#047857)';
    setTimeout(function() {
        btn.innerHTML = btn.dataset.orig;
        btn.style.background = '';
    }, 2600);
}
</script>
@endsection
