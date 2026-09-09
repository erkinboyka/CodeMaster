@extends('layouts.app')

@section('title', __('Dashboard') . ' - CodeMaster')

@section('head')
<style>
    /* ============ DASHBOARD: MISSION CONTROL + 3D HERO ============ */
    .db-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .db-hero3d { position: relative; min-height: 100vh; display: flex; align-items: center;
        overflow: hidden; padding: 110px clamp(20px,4vw,56px) 90px; isolation: isolate; perspective: 1600px; }
    .db-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .db-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(139,92,246,.14) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(34,197,94,.10) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(56,189,248,.10) 0%, transparent 60%);
        animation: dbAurora 22s ease-in-out infinite alternate; }
    @@keyframes dbAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); } }
    .db-grid3d { position: absolute; inset: -50%;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px; opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2); transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%); }
    .db-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .db-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: dbOrb1 16s ease-in-out infinite; }
    .db-orb-2 { width: 460px; height: 460px; background: #8b5cf6; opacity: .10; bottom: -18%; right: -6%; animation: dbOrb2 20s ease-in-out infinite; }
    .db-orb-3 { width: 260px; height: 260px; background: #22c55e; opacity: .09; top: 55%; left: 42%; animation: dbOrb3 12s ease-in-out infinite; }
    @@keyframes dbOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes dbOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes dbOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }
    @@keyframes dbBlink { 0%,100%{opacity:1} 50%{opacity:.35} }

    .db-hero3d-inner { position: relative; z-index: 2; width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px,5vw,72px); align-items: center; }
    .db-eyebrow { display: inline-flex; align-items: center; gap: 10px; padding: 8px 18px; border-radius: 100px;
        background: var(--accent-glow); border: 1px solid var(--accent-glow-strong);
        font-size: 11px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase; color: var(--accent); margin-bottom: 22px; }
    .db-eyebrow i { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: dbBlink 1.6s infinite;
        box-shadow: 0 0 10px #22c55e; }
    .db-title { font-size: clamp(40px,5.6vw,76px); font-weight: 900; line-height: .98; letter-spacing: -2.5px;
        margin: 0 0 18px; color: var(--text); }
    .db-title .grad { background: linear-gradient(120deg, var(--accent), #8b5cf6 45%, #22c55e 85%); background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: dbGradShift 7s ease-in-out infinite; display: inline-block;
        filter: drop-shadow(0 8px 30px var(--accent-glow-strong)); }
    @@keyframes dbGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    .db-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.75; max-width: 540px; margin-bottom: 26px; }
    .db-sub b { color: var(--text); }
    .db-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
    .db-btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 26px; border-radius: 16px;
        font-weight: 800; font-size: 14px; text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
        border: 1px solid transparent; cursor: pointer; }
    .db-btn-go { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff;
        box-shadow: 0 10px 32px var(--accent-glow-strong); }
    .db-btn-go:hover { transform: translateY(-3px) scale(1.02); }
    .db-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .db-btn-ghost:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-3px); }
    .db-stats3d { display: grid; grid-template-columns: repeat(4,auto); gap: clamp(18px,3vw,40px); justify-content: start; }
    .db-stat { position: relative; }
    .db-stat-val { font-size: clamp(26px,2.8vw,36px); font-weight: 900; line-height: 1; font-variant-numeric: tabular-nums;
        background: linear-gradient(135deg,var(--text),var(--accent)); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; }
    .db-stat-label { font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.4px;
        margin-top: 6px; font-weight: 700; }

    /* --- 3D pilot card stage --- */
    .db-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .db-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .db-ring-1 { width: 480px; height: 480px; animation: dbSpin 26s linear infinite; opacity: .7; }
    .db-ring-2 { width: 590px; height: 590px; animation: dbSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes dbSpin { to { transform: rotate(360deg); } }
    @@keyframes dbSpinRev { to { transform: rotate(-360deg); } }
    .db-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: var(--accent);
        box-shadow: 0 0 14px var(--accent); top: -5px; left: 50%; }
    .db-pilot3d { position: relative; width: 100%; max-width: 440px; padding: 26px; overflow: hidden;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid var(--border); border-radius: 24px;
        box-shadow: 0 40px 100px rgba(0,0,0,.35), 0 0 80px var(--accent-glow);
        transform-style: preserve-3d; transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear; z-index: 3; }
    .db-pilot3d::after { content:''; position: absolute; inset: 0; border-radius: 24px; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: dbSheen 6s ease-in-out infinite; }
    @@keyframes dbSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .db-pilot-top { display: flex; align-items: center; gap: 15px; margin-bottom: 18px; transform: translateZ(40px); }
    .db-pilot-ava { width: 64px; height: 64px; border-radius: 20px; object-fit: cover; flex-shrink: 0;
        border: 2px solid var(--accent-glow-strong); box-shadow: 0 10px 26px rgba(0,0,0,.3); }
    .db-pilot-name { font-size: 18px; font-weight: 900; color: var(--text); }
    .db-pilot-lvl { display: inline-flex; align-items: center; gap: 6px; margin-top: 5px; padding: 4px 11px;
        border-radius: 9px; font-size: 11px; font-weight: 800; }
    .db-xp-big { font-family: var(--font-mono); font-size: 30px; font-weight: 800; margin-bottom: 2px;
        background: linear-gradient(135deg,var(--accent),#8b5cf6); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; font-variant-numeric: tabular-nums;
        transform: translateZ(30px); }
    .db-xp-sub { font-size: 11px; color: var(--text-muted); font-family: var(--font-mono); margin-bottom: 12px; }
    .db-xpbar { height: 11px; border-radius: 6px; background: var(--border); overflow: hidden; margin-bottom: 8px;
        transform: translateZ(25px); }
    .db-xpbar div { height: 100%; width: 0; border-radius: 6px; background: linear-gradient(90deg,var(--accent),#8b5cf6,#22c55e);
        box-shadow: 0 0 14px var(--accent-glow-strong); animation: dbXp 2.2s cubic-bezier(.16,1,.3,1) .4s forwards; }
    @@keyframes dbXp { to { width: var(--w, 50%); } }
    .db-xp-meta { display: flex; justify-content: space-between; font-size: 10px; color: var(--text-muted);
        font-family: var(--font-mono); margin-bottom: 18px; }
    .db-pilot-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; transform: translateZ(20px); }
    .db-mini { border-radius: 14px; background: var(--bg-secondary); border: 1px solid var(--border);
        padding: 12px 14px; display: flex; align-items: center; gap: 10px; }
    .db-mini i { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-size: 14px; flex-shrink: 0; }
    .db-mini b { display: block; font-size: 15px; font-weight: 900; color: var(--text); font-variant-numeric: tabular-nums; }
    .db-mini span { font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: .8px; font-weight: 700; }
    .db-float-chip { position: absolute; z-index: 4; display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent); backdrop-filter: blur(18px);
        border: 1px solid var(--border); border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3); font-size: 13px; animation: dbFloatY 4.5s ease-in-out infinite; }
    @@keyframes dbFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .db-fc-1 { top: 4%; right: -6px; } .db-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .db-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .db-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0; }
    .db-fc-ico.g { background: rgba(34,197,94,.14); color: #22c55e; }
    .db-fc-ico.p { background: rgba(139,92,246,.14); color: #8b5cf6; }
    .db-fc-ico.a { background: rgba(245,158,11,.14); color: #f59e0b; }
    .db-fc-txt b { display: block; font-size: 13px; color: var(--text); font-family: var(--font-mono); }
    .db-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .db-cube { position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px; display: flex;
        align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        animation: dbCubeFloat 6s ease-in-out infinite; }
    .db-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,var(--accent),#8b5cf6); }
    .db-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#22c55e,#15803d); animation-delay: 1.5s; }
    @@keyframes dbCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .db-scroll-hint { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);
        font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-family: var(--font-mono); }
    .db-paused .db-aurora, .db-paused .db-orb, .db-paused .db-ring, .db-paused .db-cube,
    .db-paused .db-float-chip { animation-play-state: paused !important; }
    .db-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .db-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px;
        height: 8px; border-radius: 4px; background: var(--accent); animation: dbWheel 1.8s ease-in-out infinite; }
    @@keyframes dbWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ CONSOLE ============ */
    .db-wrap { max-width: 1280px; margin: 0 auto; padding: 70px clamp(20px,4vw,48px) 90px; }
    .db-cols { display: grid; grid-template-columns: minmax(0,1fr) 340px; gap: 22px; align-items: start; }
    .db-panel { border-radius: 20px; background: var(--card); border: 1px solid var(--border);
        box-shadow: 0 14px 40px rgba(0,0,0,.1); overflow: hidden;
        opacity: 0; transform: translateY(26px);
        transition: opacity .5s ease, transform .5s cubic-bezier(.16,1,.3,1); }
    .db-panel.in { opacity: 1; transform: none; }
    .db-panel-head { display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 18px 22px; border-bottom: 1px solid var(--border); }
    .db-panel-title { font-size: 15px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 10px; }
    .db-panel-title i { display: inline-flex; align-items: center; justify-content: center; width: 33px; height: 33px;
        border-radius: 11px; background: var(--accent-glow); color: var(--accent); font-size: 14px; }
    .db-panel-link { font-size: 12px; font-weight: 800; color: var(--accent); text-decoration: none; }
    .db-panel-link:hover { text-decoration: underline; }
    .db-panel-body { padding: 12px; }
    .db-row { display: flex; align-items: center; gap: 13px; padding: 13px 14px; border-radius: 14px;
        text-decoration: none; transition: background .15s; border: 1px solid transparent; }
    a.db-row:hover { background: var(--bg-secondary); border-color: var(--border); }
    .db-row-ico { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 15px; flex-shrink: 0; }
    .db-row-title { font-size: 13.5px; font-weight: 700; color: var(--text); line-height: 1.35; }
    .db-row-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 3px; font-family: var(--font-mono); }
    .db-bar { height: 7px; border-radius: 4px; background: var(--border); overflow: hidden; margin-top: 7px; }
    .db-bar div { height: 100%; width: 0; border-radius: 4px; background: linear-gradient(90deg,var(--accent),#8b5cf6);
        transition: width 1.1s cubic-bezier(.16,1,.3,1); }
    .db-pct { font-size: 11px; font-weight: 800; color: var(--accent); font-family: var(--font-mono); }
    .db-act { display: flex; gap: 12px; padding: 11px 14px; border-radius: 14px; align-items: flex-start; }
    .db-act-dot { width: 34px; height: 34px; border-radius: 11px; display: flex; align-items: center; justify-content: center;
        font-size: 13px; flex-shrink: 0; }
    .db-act-txt { font-size: 13px; color: var(--text); line-height: 1.5; }
    .db-act-time { font-size: 11px; color: var(--text-muted); margin-top: 2px; font-family: var(--font-mono); }
    .db-empty { text-align: center; padding: 26px 16px; color: var(--text-muted); font-size: 13px; }
    .db-side { display: flex; flex-direction: column; gap: 18px; position: sticky; top: 96px; }
    .db-skill { margin-bottom: 15px; }
    .db-skill:last-child { margin-bottom: 0; }
    .db-skill-top { display: flex; justify-content: space-between; font-size: 12.5px; margin-bottom: 7px; }
    .db-skill-name { font-weight: 700; color: var(--text); }
    .db-skill-lvl { color: var(--text-muted); font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; }
    .db-notif { padding: 11px 13px; border-radius: 12px; background: var(--bg-secondary); margin-bottom: 8px;
        border-left: 3px solid var(--accent); }
    .db-notif:last-child { margin-bottom: 0; }
    .db-notif-txt { font-size: 12.5px; color: var(--text); line-height: 1.5; }
    .db-notif-time { font-size: 10.5px; color: var(--text-muted); margin-top: 3px; font-family: var(--font-mono); }
    .db-cta { border-radius: 20px; padding: 26px; color: #fff; position: relative; overflow: hidden;
        background: linear-gradient(135deg,var(--accent),#8b5cf6); box-shadow: 0 18px 46px var(--accent-glow-strong); }
    .db-cta::before { content:''; position: absolute; inset: 0;
        background: radial-gradient(circle at 85% 15%, rgba(255,255,255,.2) 0%, transparent 50%); }
    .db-cta h3 { font-size: 17px; font-weight: 900; margin: 0 0 8px; position: relative; z-index: 1; }
    .db-cta p { font-size: 13px; color: rgba(255,255,255,.85); line-height: 1.6; margin: 0 0 16px; position: relative; z-index: 1; }
    .db-cta a { display: inline-flex; align-items: center; gap: 8px; padding: 11px 22px; border-radius: 12px;
        background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3); color: #fff;
        font-size: 13px; font-weight: 800; text-decoration: none; position: relative; z-index: 1; transition: all .25s; }
    .db-cta a:hover { background: rgba(255,255,255,.3); transform: translateY(-2px); }

    @@media(max-width: 1020px) {
        .db-hero3d-inner { grid-template-columns: 1fr; }
        .db-stage { height: 540px; }
        .db-fc-3 { right: 0; } .db-fc-1 { right: 0; } .db-fc-2 { left: 0; }
        .db-cols { grid-template-columns: 1fr; }
        .db-side { position: static; }
        .db-stats3d { grid-template-columns: repeat(2,1fr); }
    }
</style>
@endsection

@section('content')
<div class="db-page">
{{-- ================= HERO 3D ================= --}}
@php
    $hour = (int) now()->format('H');
    $daypart = $hour < 5 ? __('deep night') : ($hour < 12 ? __('morning') : ($hour < 18 ? __('afternoon') : __('evening')));
    $firstName = strtok($user->name ?? 'Pilot', ' ') ?: 'Pilot';
@endphp
<section class="db-hero3d" id="dbHero">
    <div class="db-hero3d-bg">
        <div class="db-aurora"></div>
        <div class="db-grid3d" data-depth="18"></div>
        <div class="db-orb db-orb-1" data-depth="40"></div>
        <div class="db-orb db-orb-2" data-depth="-30"></div>
        <div class="db-orb db-orb-3" data-depth="60"></div>
    </div>

    <div class="db-hero3d-inner">
        <div>
            <span class="db-eyebrow"><i></i>{{ __('mission control') }} • {{ $daypart }}</span>
            <h1 class="db-title">{!! __('Welcome back,') . '<br><span class="grad">' . e($firstName) . '</span>' !!}</h1>
            <p class="db-sub">{!! __('Your streak is alive and the board remembers. <b>Continue the mission</b> — one more lesson today.') !!}</p>

            <div class="db-hero-actions">
                <a href="{{ route('courses.index') }}" class="db-btn db-btn-go"><i class="fas fa-play"></i>{{ __('Continue Learning') }}</a>
                <a href="#dbConsole" class="db-btn db-btn-ghost" id="dbToConsole"><i class="fas fa-gauge-high"></i>{{ __('Open console') }}</a>
            </div>

            <div class="db-stats3d">
                <div class="db-stat"><div class="db-stat-val" data-count="{{ $completedCourses }}">0</div><div class="db-stat-label">{{ __('Courses Done') }}</div></div>
                <div class="db-stat"><div class="db-stat-val" data-count="{{ $inProgressCourses }}">0</div><div class="db-stat-label">{{ __('In Progress') }}</div></div>
                <div class="db-stat"><div class="db-stat-val" data-count="{{ $certificates->count() }}">0</div><div class="db-stat-label">{{ __('Certificates') }}</div></div>
                <div class="db-stat"><div class="db-stat-val" data-count="{{ $applications }}">0</div><div class="db-stat-label">{{ __('Applications') }}</div></div>
            </div>
        </div>

        <div class="db-stage">
            <div class="db-ring db-ring-1"><span class="db-ring-dot"></span></div>
            <div class="db-ring db-ring-2"><span class="db-ring-dot" style="background:#8b5cf6;box-shadow:0 0 14px #8b5cf6"></span></div>
            <div class="db-cube db-cube-1" data-depth="70"><i class="fas fa-rocket"></i></div>
            <div class="db-cube db-cube-2" data-depth="-60"><i class="fas fa-chart-line"></i></div>

            <div class="db-pilot3d" id="dbPilot">
                <div class="db-pilot-top">
                    <img src="{{ $user->avatar_url }}" class="db-pilot-ava">
                    <div>
                        <div class="db-pilot-name">{{ $user->name }}</div>
                        <span class="db-pilot-lvl" style="background:{{ $user->level_color ?? 'var(--accent)' }}18;color:{{ $user->level_color ?? 'var(--accent)' }}">{!! $user->level_badge ?? '' !!} Lv.{{ $user->level ?? 1 }}</span>
                    </div>
                </div>
                <div class="db-xp-big" data-count="{{ $user->total_xp ?? 0 }}">0</div>
                <div class="db-xp-sub">TOTAL XP • {{ $user->ai_tokens ?? 0 }} AI TOKENS</div>
                <div class="db-xpbar"><div style="--w:{{ $user->level_progress ?? 0 }}%"></div></div>
                <div class="db-xp-meta"><span>LV.{{ $user->level ?? 1 }}</span><span>{{ $user->level_progress ?? 0 }}%</span><span>LV.{{ ($user->level ?? 1) + 1 }}</span></div>
                <div class="db-pilot-grid">
                    <div class="db-mini"><i class="fas fa-certificate" style="background:rgba(234,179,8,.12);color:#eab308"></i><div><b>{{ $certificates->count() }}</b><span>{{ __('certs') }}</span></div></div>
                    <div class="db-mini"><i class="fas fa-briefcase" style="background:rgba(59,130,246,.12);color:#3b82f6"></i><div><b>{{ $applications }}</b><span>{{ __('applied') }}</span></div></div>
                    <div class="db-mini"><i class="fas fa-book-open" style="background:rgba(34,197,94,.12);color:#22c55e"></i><div><b>{{ $completedCourses }}</b><span>{{ __('done') }}</span></div></div>
                    <div class="db-mini"><i class="fas fa-spinner" style="background:rgba(139,92,246,.12);color:#8b5cf6"></i><div><b>{{ $inProgressCourses }}</b><span>{{ __('active') }}</span></div></div>
                </div>
            </div>

            <div class="db-float-chip db-fc-1" data-depth="50">
                <div class="db-fc-ico g"><i class="fas fa-fire"></i></div>
                <div class="db-fc-txt"><b>{{ __('Streak on') }}</b><span>{{ __('keep shipping') }}</span></div>
            </div>
            <div class="db-float-chip db-fc-2" data-depth="-45">
                <div class="db-fc-ico p"><i class="fas fa-bolt"></i></div>
                <div class="db-fc-txt"><b>{{ number_format($user->total_xp ?? 0) }} XP</b><span>{{ __('and counting') }}</span></div>
            </div>
            <div class="db-float-chip db-fc-3" data-depth="35">
                <div class="db-fc-ico a"><i class="fas fa-coins"></i></div>
                <div class="db-fc-txt"><b>{{ $user->ai_tokens ?? 0 }}</b><span>{{ __('AI tokens left') }}</span></div>
            </div>
        </div>
    </div>

    <div class="db-scroll-hint"><div class="db-mouse"></div><span>{{ __('Scroll — console') }}</span></div>
</section>

{{-- ================= CONSOLE ================= --}}
<div class="db-wrap" id="dbConsole">
    <div class="db-cols">
        <div style="display:flex;flex-direction:column;gap:18px;min-width:0">
            @if($inProgressCourseList->count())
            <div class="db-panel" data-rv>
                <div class="db-panel-head">
                    <span class="db-panel-title"><i class="fas fa-play"></i>{{ __('Continue Learning') }}</span>
                    <a href="{{ route('courses.index') }}" class="db-panel-link">{{ __('View all') }} →</a>
                </div>
                <div class="db-panel-body">
                    @foreach($inProgressCourseList as $progress)
                    <a href="{{ route('courses.show', $progress->course_id) }}" class="db-row">
                        <div class="db-row-ico" style="background:var(--accent-glow);color:var(--accent)"><i class="fas fa-book"></i></div>
                        <div style="flex:1;min-width:0">
                            <div class="db-row-title">{{ $progress->course->title ?? __('Course') }}</div>
                            <div class="db-bar"><div data-w="{{ $progress->progress }}"></div></div>
                        </div>
                        <span class="db-pct">{{ $progress->progress }}%</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="db-panel" data-rv>
                <div class="db-panel-head">
                    <span class="db-panel-title"><i class="fas fa-wave-square"></i>{{ __('Recent Activity') }}</span>
                </div>
                <div class="db-panel-body">
                    @forelse($recentActivity as $activity)
                    <div class="db-act">
                        <div class="db-act-dot" style="background:var(--accent-glow);color:var(--accent)">
                            <i class="fas fa-{{ $activity->activity_type === 'course' ? 'book' : ($activity->activity_type === 'certificate' ? 'certificate' : ($activity->activity_type === 'application' ? 'briefcase' : 'code')) }}"></i>
                        </div>
                        <div>
                            <div class="db-act-txt">{{ $activity->activity_text }}</div>
                            <div class="db-act-time">{{ $activity->activity_time?->diffForHumans() ?? '' }}</div>
                        </div>
                    </div>
                    @empty
                    <p class="db-empty">{{ __('No activity yet — solve something today') }}</p>
                    @endforelse
                </div>
            </div>

            @if($recommendedCourses->count())
            <div class="db-panel" data-rv>
                <div class="db-panel-head">
                    <span class="db-panel-title"><i class="fas fa-compass"></i>{{ __('Recommended Courses') }}</span>
                    <a href="{{ route('courses.index') }}" class="db-panel-link">{{ __('View all') }} →</a>
                </div>
                <div class="db-panel-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:8px">
                    @foreach($recommendedCourses as $course)
                    <a href="{{ route('courses.show', $course->id) }}" class="db-row" style="border:1px solid var(--border)">
                        <div class="db-row-ico" style="background:rgba(139,92,246,.1);color:#8b5cf6"><i class="fas fa-book"></i></div>
                        <div style="min-width:0">
                            <div class="db-row-title">{{ $course->title }}</div>
                            <div class="db-row-sub">{{ $course->lessons->count() }} {{ __('lessons') }} • {{ __('courses_level_' . mb_strtolower($course->level)) }}</div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="db-side">
            <div class="db-panel in" data-rv>
                <div class="db-panel-head"><span class="db-panel-title"><i class="fas fa-layer-group"></i>{{ __('Your Skills') }}</span></div>
                <div class="db-panel-body">
                    @forelse($user->skills->take(8) as $skill)
                    @php
                        $levelPct = match($skill->skill_level) {
                            'beginner' => 25, 'intermediate' => 50,
                            'advanced' => 75, 'expert' => 100, default => 50,
                        };
                    @endphp
                    <div class="db-skill">
                        <div class="db-skill-top"><span class="db-skill-name">{{ $skill->skill_name }}</span><span class="db-skill-lvl">{{ $skill->skill_level }}</span></div>
                        <div class="db-bar"><div data-w="{{ $levelPct }}"></div></div>
                    </div>
                    @empty
                    <p class="db-empty">{{ __('No skills yet — courses add them automatically') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="db-panel" data-rv>
                <div class="db-panel-head"><span class="db-panel-title"><i class="fas fa-bell"></i>{{ __('Notifications') }} ({{ $notifications->count() }})</span></div>
                <div class="db-panel-body">
                    @forelse($notifications as $notif)
                    <div class="db-notif">
                        <div class="db-notif-txt">{{ $notif->message }}</div>
                        <div class="db-notif-time">{{ $notif->notification_time?->diffForHumans() ?? '' }}</div>
                    </div>
                    @empty
                    <p class="db-empty">{{ __('All caught up') }}</p>
                    @endforelse
                </div>
            </div>

            @if($certificates->count())
            <div class="db-panel" data-rv>
                <div class="db-panel-head"><span class="db-panel-title"><i class="fas fa-certificate"></i>{{ __('Certificates') }}</span></div>
                <div class="db-panel-body">
                    @foreach($certificates as $cert)
                    <a href="{{ route('certificate.show', $cert->cert_hash) }}" class="db-row">
                        <div class="db-row-ico" style="background:rgba(234,179,8,.12);color:#eab308"><i class="fas fa-certificate"></i></div>
                        <div style="min-width:0">
                            <div class="db-row-title" style="font-size:13px">{{ $cert->certificate_name }}</div>
                            <div class="db-row-sub">{{ $cert->course->title ?? '' }}</div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="db-cta" data-rv>
                <h3>{{ __('AI Tutor') }}</h3>
                <p>{{ __('Stuck on a task? Ask the tutor — it knows your progress.') }}</p>
                <a href="{{ route('courses.index') }}">{{ __('Ask anything') }}<i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    /* --- Hero mouse 3D parallax --- */
    var hero = document.getElementById('dbHero');
    var pilot = document.getElementById('dbPilot');
    var layers = document.querySelectorAll('#dbHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0, heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('db-paused', !heroVisible);
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
                if (pilot) pilot.style.transform = 'rotateX(' + (6 + rx * -1).toFixed(2) + 'deg) rotateY(' + (-10 + ry).toFixed(2) + 'deg)';
            }
            requestAnimationFrame(tilt);
        })();
    }

    /* --- Animated counters (hero stats + pilot XP) --- */
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

    /* --- Panels reveal + progress bars --- */
    function fillBars(scope) {
        (scope || document).querySelectorAll('.db-bar div[data-w]').forEach(function(b) {
            b.style.width = b.dataset.w + '%';
        });
    }
    var panels = document.querySelectorAll('[data-rv]');
    if ('IntersectionObserver' in window && panels.length) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(en) {
                if (en.isIntersecting) {
                    var el = en.target;
                    el.classList.add('in');
                    setTimeout(function() { fillBars(el); }, 250);
                    io.unobserve(el);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
        panels.forEach(function(p) { io.observe(p); });
        setTimeout(function() { panels.forEach(function(p) { p.classList.add('in'); }); fillBars(document); }, 4000);
    } else {
        panels.forEach(function(p) { p.classList.add('in'); }); fillBars(document);
    }

    /* --- Scroll to console --- */
    var toConsole = document.getElementById('dbToConsole');
    if (toConsole) toConsole.addEventListener('click', function(e) {
        e.preventDefault();
        var t = document.getElementById('dbConsole');
        if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();
</script>
@endsection
