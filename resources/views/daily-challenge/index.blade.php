@extends('layouts.app')
@section('title', __('Daily Challenge'))

@section('head')
<style>
    /* ============ DAILY: QUEST THEME + 3D HERO ============ */
    .dc-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .dc-hero3d { position: relative; min-height: 100vh; display: flex; align-items: center;
        overflow: hidden; padding: 110px clamp(20px,4vw,56px) 90px; isolation: isolate; perspective: 1600px; }
    .dc-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .dc-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(245,158,11,.14) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(34,197,94,.10) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(139,92,246,.10) 0%, transparent 60%);
        animation: dcAurora 22s ease-in-out infinite alternate; }
    @@keyframes dcAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); } }
    .dc-grid3d { position: absolute; inset: -50%;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px; opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2); transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%); }
    .dc-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .dc-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: dcOrb1 16s ease-in-out infinite; }
    .dc-orb-2 { width: 460px; height: 460px; background: #f59e0b; opacity: .10; bottom: -18%; right: -6%; animation: dcOrb2 20s ease-in-out infinite; }
    .dc-orb-3 { width: 260px; height: 260px; background: #22c55e; opacity: .09; top: 55%; left: 42%; animation: dcOrb3 12s ease-in-out infinite; }
    @@keyframes dcOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes dcOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes dcOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }
    @@keyframes dcBlink { 0%,100%{opacity:1} 50%{opacity:.35} }

    .dc-hero3d-inner { position: relative; z-index: 2; width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px,5vw,72px); align-items: center; }
    .dc-eyebrow { display: inline-flex; align-items: center; gap: 10px; padding: 8px 18px; border-radius: 100px;
        background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.3);
        font-size: 11px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase; color: #fbbf24; margin-bottom: 22px; }
    .dc-eyebrow i { animation: dcBlink 1.4s infinite; }
    .dc-title { font-size: clamp(42px,6.2vw,84px); font-weight: 900; line-height: .95; letter-spacing: -3px;
        margin: 0 0 18px; color: var(--text); }
    .dc-title .grad { background: linear-gradient(120deg, #f59e0b, var(--accent) 55%, #22c55e 90%); background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: dcGradShift 7s ease-in-out infinite; display: inline-block;
        filter: drop-shadow(0 8px 30px rgba(245,158,11,.22)); }
    @@keyframes dcGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    .dc-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.75; max-width: 540px; margin-bottom: 24px; }
    .dc-sub b { color: var(--text); }
    .dc-count { display: inline-flex; align-items: center; gap: 12px; padding: 14px 22px; border-radius: 18px;
        background: var(--card); border: 1px solid var(--border); margin-bottom: 24px;
        box-shadow: 0 12px 34px rgba(0,0,0,.14); }
    .dc-count i { color: #f59e0b; font-size: 18px; animation: dcBlink 1.6s infinite; }
    .dc-count small { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.2px; font-weight: 700; }
    .dc-count b { font-family: var(--font-mono); font-size: 24px; font-weight: 800; color: var(--text);
        font-variant-numeric: tabular-nums; }
    .dc-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
    .dc-btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 26px; border-radius: 16px;
        font-weight: 800; font-size: 14px; text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
        border: 1px solid transparent; cursor: pointer; }
    .dc-btn-go { background: linear-gradient(135deg,#f59e0b,var(--accent)); color: #fff;
        box-shadow: 0 10px 32px rgba(245,158,11,.35); }
    .dc-btn-go:hover { transform: translateY(-3px) scale(1.02); }
    .dc-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .dc-btn-ghost:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-3px); }
    .dc-stats3d { display: flex; gap: clamp(20px,3.5vw,44px); flex-wrap: wrap; }
    .dc-stat { position: relative; }
    .dc-stat + .dc-stat::before { content:''; position: absolute; left: -22px; top: 50%; transform: translateY(-50%);
        width: 1px; height: 38px; background: var(--border); }
    .dc-stat-val { font-size: clamp(26px,3vw,38px); font-weight: 900; line-height: 1; font-variant-numeric: tabular-nums;
        background: linear-gradient(135deg,var(--text),#f59e0b); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; }
    .dc-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px;
        margin-top: 6px; font-weight: 600; }

    /* --- 3D quest card stage --- */
    .dc-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .dc-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .dc-ring-1 { width: 480px; height: 480px; animation: dcSpin 26s linear infinite; opacity: .7; }
    .dc-ring-2 { width: 590px; height: 590px; animation: dcSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes dcSpin { to { transform: rotate(360deg); } }
    @@keyframes dcSpinRev { to { transform: rotate(-360deg); } }
    .dc-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;
        box-shadow: 0 0 14px #f59e0b; top: -5px; left: 50%; }
    .dc-quest3d { position: relative; width: 100%; max-width: 440px; padding: 26px; overflow: hidden;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid var(--border); border-radius: 24px;
        box-shadow: 0 40px 100px rgba(0,0,0,.35), 0 0 80px rgba(245,158,11,.12);
        transform-style: preserve-3d; transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear; z-index: 3; text-decoration: none; display: block; }
    a.dc-quest3d:hover { border-color: #f59e0b; }
    .dc-quest3d::after { content:''; position: absolute; inset: 0; border-radius: 24px; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: dcSheen 6s ease-in-out infinite; }
    @@keyframes dcSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .dc-quest-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;
        transform: translateZ(40px); }
    .dc-quest-day { font-family: var(--font-mono); font-size: 11px; font-weight: 800; letter-spacing: 2px;
        text-transform: uppercase; color: #fbbf24; }
    .dc-quest-diff { padding: 6px 13px; border-radius: 10px; font-size: 11px; font-weight: 800;
        text-transform: uppercase; letter-spacing: .6px; }
    .dc-quest-date { font-family: var(--font-mono); font-size: 64px; font-weight: 900; line-height: 1;
        background: linear-gradient(135deg,var(--text),#f59e0b); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; transform: translateZ(30px); }
    .dc-quest-month { font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 3px;
        font-weight: 700; margin: 4px 0 14px; }
    .dc-quest-title { font-size: 19px; font-weight: 800; color: var(--text); margin-bottom: 12px; transform: translateZ(25px); }
    .dc-rate { margin-bottom: 18px; transform: translateZ(20px); }
    .dc-rate-row { display: flex; justify-content: space-between; font-size: 11px; font-weight: 800;
        font-family: var(--font-mono); color: var(--text-muted); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 1px; }
    .dc-rate-row b { color: #4ade80; }
    .dc-rate-bar { height: 9px; border-radius: 5px; background: var(--border); overflow: hidden; }
    .dc-rate-fill { height: 100%; width: 0; border-radius: 5px; background: linear-gradient(90deg,#22c55e,#a3e635);
        box-shadow: 0 0 12px rgba(34,197,94,.6); animation: dcRate 2.4s cubic-bezier(.16,1,.3,1) .5s forwards; }
    @@keyframes dcRate { to { width: var(--w, 60%); } }
    .dc-solve { display: flex; align-items: center; justify-content: center; gap: 9px; width: 100%; padding: 14px;
        border-radius: 15px; font-size: 14px; font-weight: 800; color: #fff;
        background: linear-gradient(135deg,#f59e0b,#ef4444); box-shadow: 0 10px 28px rgba(245,158,11,.4);
        transform: translateZ(35px); }
    .dc-float-chip { position: absolute; z-index: 4; display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent); backdrop-filter: blur(18px);
        border: 1px solid var(--border); border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3); font-size: 13px; animation: dcFloatY 4.5s ease-in-out infinite; }
    @@keyframes dcFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .dc-fc-1 { top: 4%; right: -6px; } .dc-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .dc-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .dc-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0; }
    .dc-fc-ico.g { background: rgba(245,158,11,.14); color: #f59e0b; }
    .dc-fc-ico.p { background: rgba(34,197,94,.14); color: #22c55e; }
    .dc-fc-ico.a { background: rgba(139,92,246,.14); color: #8b5cf6; }
    .dc-fc-txt b { display: block; font-size: 13px; color: var(--text); font-family: var(--font-mono); }
    .dc-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .dc-cube { position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px; display: flex;
        align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        animation: dcCubeFloat 6s ease-in-out infinite; }
    .dc-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,#f59e0b,#ef4444); }
    .dc-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#22c55e,#15803d); animation-delay: 1.5s; }
    @@keyframes dcCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .dc-scroll-hint { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);
        font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-family: var(--font-mono); }
    .dc-paused .dc-aurora, .dc-paused .dc-orb, .dc-paused .dc-ring, .dc-paused .dc-cube,
    .dc-paused .dc-float-chip { animation-play-state: paused !important; }
    .dc-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .dc-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px;
        height: 8px; border-radius: 4px; background: #f59e0b; animation: dcWheel 1.8s ease-in-out infinite; }
    @@keyframes dcWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ WEEK STRIP ============ */
    .dc-wrap { max-width: 1000px; margin: 0 auto; padding: 70px clamp(20px,4vw,48px) 90px; }
    .dc-week { display: grid; grid-template-columns: repeat(7,1fr); gap: 10px; margin-bottom: 34px; }
    .dc-day { border-radius: 16px; background: var(--card); border: 1px solid var(--border); padding: 14px 8px;
        text-align: center; text-decoration: none; transition: all .25s;
        opacity: 0; transform: translateY(18px); }
    .dc-day.in { opacity: 1; transform: none; transition: opacity .45s ease, transform .45s cubic-bezier(.16,1,.3,1), border-color .2s, box-shadow .2s; }
    a.dc-day:hover { border-color: #f59e0b; transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,.14); }
    .dc-day.today { border-color: #f59e0b; box-shadow: 0 0 0 1px #f59e0b, 0 10px 30px rgba(245,158,11,.2); }
    .dc-day-dow { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); }
    .dc-day-num { font-family: var(--font-mono); font-size: 22px; font-weight: 800; color: var(--text); margin: 4px 0; }
    .dc-day.done .dc-day-num { color: #4ade80; }
    .dc-day-count { font-size: 10px; font-weight: 700; font-family: var(--font-mono); color: var(--text-muted); }
    .dc-day.done .dc-day-count { color: #4ade80; }
    .dc-sec-t { font-size: 16px; font-weight: 900; color: var(--text); margin-bottom: 14px;
        display: flex; align-items: center; gap: 9px; }
    .dc-sec-t i { color: #f59e0b; }
    .dc-recent { display: grid; gap: 10px; }
    .dc-row { display: flex; align-items: center; gap: 14px; padding: 14px 16px; border-radius: 15px;
        border: 1px solid var(--border); background: var(--card); text-decoration: none; transition: all .2s;
        opacity: 0; transform: translateX(-14px); }
    .dc-row.in { opacity: 1; transform: none; transition: opacity .4s ease, transform .4s cubic-bezier(.16,1,.3,1), border-color .15s; }
    .dc-row:hover { border-color: #f59e0b; transform: translateX(4px); }
    .dc-cal { width: 46px; text-align: center; flex-shrink: 0; border-radius: 12px; background: var(--bg-secondary);
        border: 1px solid var(--border); padding: 7px 0; }
    .dc-cal small { display: block; font-size: 9px; font-weight: 800; text-transform: uppercase; color: var(--text-muted); }
    .dc-cal b { display: block; font-family: var(--font-mono); font-size: 17px; font-weight: 800; color: var(--text); }
    .dc-row-t { font-size: 13.5px; font-weight: 700; color: var(--text); }
    .dc-row-s { font-size: 11.5px; color: var(--text-muted); margin-top: 3px; font-family: var(--font-mono); }
    .dc-row-go { margin-left: auto; color: var(--text-muted); font-size: 12px; flex-shrink: 0; }
    .dc-row:hover .dc-row-go { color: #f59e0b; }
    .dc-empty { text-align: center; padding: 60px 20px; border-radius: 20px; border: 1px dashed var(--border);
        color: var(--text-muted); font-family: var(--font-mono); }
    .dc-empty i { font-size: 40px; margin-bottom: 12px; display: block; opacity: .3; }

    @@media(max-width: 1020px) {
        .dc-hero3d-inner { grid-template-columns: 1fr; }
        .dc-stage { height: 560px; }
        .dc-fc-3 { right: 0; } .dc-fc-1 { right: 0; } .dc-fc-2 { left: 0; }
        .dc-week { grid-template-columns: repeat(4,1fr); }
    }
    @@media(max-width: 560px) { .dc-week { grid-template-columns: repeat(3,1fr); } }
</style>
@endsection

@section('content')
<div class="dc-page">
{{-- ================= HERO 3D ================= --}}
<section class="dc-hero3d" id="dcHero">
    <div class="dc-hero3d-bg">
        <div class="dc-aurora"></div>
        <div class="dc-grid3d" data-depth="18"></div>
        <div class="dc-orb dc-orb-1" data-depth="40"></div>
        <div class="dc-orb dc-orb-2" data-depth="-30"></div>
        <div class="dc-orb dc-orb-3" data-depth="60"></div>
    </div>

    <div class="dc-hero3d-inner">
        <div>
            <span class="dc-eyebrow"><i class="fas fa-bolt"></i>{{ __('daily quest') }}</span>
            <h1 class="dc-title">{!! __('One Problem.<br><span class="grad">Every Day.</span>') !!}</h1>
            <p class="dc-sub">{!! __('A fresh task drops at <b>midnight</b>. Solve it before the timer dies and keep your <b>streak</b> alive.') !!}</p>

            <div class="dc-count">
                <i class="fas fa-hourglass-half"></i>
                <div><small>{{ __('next drop in') }}</small><br><b id="dcClock">--:--:--</b></div>
            </div>

            <div class="dc-hero-actions">
                @if($problem)
                <a href="{{ route('problems.show', $problem->slug) }}" class="dc-btn dc-btn-go"><i class="fas fa-play"></i>{{ __('Solve today\'s') }}</a>
                @endif
                <a href="#dcWeek" class="dc-btn dc-btn-ghost" id="dcToWeek"><i class="fas fa-calendar-week"></i>{{ __('This week') }}</a>
            </div>

            @php
                $todaySolved = $today->solved_count ?? 0;
                $todaySubs = $today->submissions_count ?? 0;
                $todayRate = $todaySubs > 0 ? round(($todaySolved / $todaySubs) * 100) : 0;
            @endphp
            <div class="dc-stats3d">
                <div class="dc-stat"><div class="dc-stat-val" data-count="{{ $todaySolved }}">0</div><div class="dc-stat-label">{{ __('Solved today') }}</div></div>
                <div class="dc-stat"><div class="dc-stat-val" data-count="{{ $todayRate }}">0</div><div class="dc-stat-label">{{ __('Success %') }}</div></div>
                <div class="dc-stat"><div class="dc-stat-val" data-count="{{ $recent->count() }}">0</div><div class="dc-stat-label">{{ __('This week') }}</div></div>
            </div>
        </div>

        <div class="dc-stage">
            <div class="dc-ring dc-ring-1"><span class="dc-ring-dot"></span></div>
            <div class="dc-ring dc-ring-2"><span class="dc-ring-dot" style="background:#22c55e;box-shadow:0 0 14px #22c55e"></span></div>
            <div class="dc-cube dc-cube-1" data-depth="70"><i class="fas fa-bolt"></i></div>
            <div class="dc-cube dc-cube-2" data-depth="-60"><i class="fas fa-calendar-day"></i></div>

            @if($problem)
            @php
                $diffStyle = match($problem->difficulty) {
                    'easy' => 'background:rgba(34,197,94,.14);color:#4ade80;border:1px solid rgba(34,197,94,.3)',
                    'medium' => 'background:rgba(245,158,11,.14);color:#fbbf24;border:1px solid rgba(245,158,11,.3)',
                    default => 'background:rgba(239,68,68,.14);color:#f87171;border:1px solid rgba(239,68,68,.3)',
                };
            @endphp
            <a href="{{ route('problems.show', $problem->slug) }}" class="dc-quest3d" id="dcQuest">
                <div class="dc-quest-top">
                    <span class="dc-quest-day">◉ {{ __('today\'s quest') }}</span>
                    <span class="dc-quest-diff" style="{{ $diffStyle }}">{{ __('difficulty_' . $problem->difficulty) }}</span>
                </div>
                <div class="dc-quest-date">{{ now()->format('d') }}</div>
                <div class="dc-quest-month">{{ now()->format('F Y') }}</div>
                <div class="dc-quest-title">{{ $problem->title }}</div>
                <div class="dc-rate">
                    <div class="dc-rate-row"><span>solve_rate</span><b>{{ $todayRate }}%</b></div>
                    <div class="dc-rate-bar"><div class="dc-rate-fill" style="--w:{{ $todayRate }}%"></div></div>
                </div>
                <span class="dc-solve">{{ __('Solve Now') }}<i class="fas fa-arrow-right"></i></span>
            </a>
            @else
            <div class="dc-quest3d" id="dcQuest">
                <div class="dc-quest-top"><span class="dc-quest-day">◉ {{ __('daily quest') }}</span></div>
                <div class="dc-quest-date">{{ now()->format('d') }}</div>
                <div class="dc-quest-month">{{ now()->format('F Y') }}</div>
                <div class="dc-quest-title">{{ __('No challenge today. Check back tomorrow!') }}</div>
            </div>
            @endif

            <div class="dc-float-chip dc-fc-1" data-depth="50">
                <div class="dc-fc-ico g"><i class="fas fa-fire"></i></div>
                <div class="dc-fc-txt"><b>{{ __('Streak') }}</b><span>{{ __('solve daily') }}</span></div>
            </div>
            <div class="dc-float-chip dc-fc-2" data-depth="-45">
                <div class="dc-fc-ico p"><i class="fas fa-bolt"></i></div>
                <div class="dc-fc-txt"><b>+XP</b><span>{{ __('bonus reward') }}</span></div>
            </div>
            <div class="dc-float-chip dc-fc-3" data-depth="35">
                <div class="dc-fc-ico a"><i class="fas fa-trophy"></i></div>
                <div class="dc-fc-txt"><b>{{ $todaySolved }}</b><span>{{ __('solved today') }}</span></div>
            </div>
        </div>
    </div>

    <div class="dc-scroll-hint"><div class="dc-mouse"></div><span>{{ __('Scroll — week') }}</span></div>
</section>

{{-- ================= WEEK ================= --}}
<div class="dc-wrap" id="dcWeek">
    @php
        $byDate = $recent->keyBy(fn($d) => $d->challenge_date->format('Y-m-d'));
        $weekDays = [];
        for ($i = 6; $i >= 0; $i--) {
            $dt = \Carbon\Carbon::today()->subDays($i);
            $weekDays[] = ['dt' => $dt, 'rec' => $byDate->get($dt->format('Y-m-d'))];
        }
    @endphp
    <div class="dc-sec-t"><i class="fas fa-calendar-week"></i>{{ __('This week') }}</div>
    <div class="dc-week" id="dcDays">
        @foreach($weekDays as $wd)
        @php
            $isToday = $wd['dt']->isToday();
            $has = $wd['rec'] && $wd['rec']->problem;
            $done = $has && ($wd['rec']->solved_count ?? 0) > 0;
        @endphp
        @if($has)
        <a href="{{ route('problems.show', $wd['rec']->problem->slug) }}" class="dc-day{{ $isToday ? ' today' : '' }}{{ $done ? ' done' : '' }}" data-i="{{ $loop->index }}">
        @else
        <div class="dc-day{{ $isToday ? ' today' : '' }}" data-i="{{ $loop->index }}">
        @endif
            <div class="dc-day-dow">{{ $wd['dt']->format('D') }}</div>
            <div class="dc-day-num">{{ $wd['dt']->format('d') }}</div>
            <div class="dc-day-count">{{ $has ? ($wd['rec']->solved_count ?? 0) . ' ✓' : '—' }}</div>
        @if($has)
        </a>
        @else
        </div>
        @endif
        @endforeach
    </div>

    @if($recent->count() > 1)
    <div class="dc-sec-t"><i class="fas fa-clock-rotate-left"></i>{{ __('Recent Challenges') }}</div>
    <div class="dc-recent" id="dcRows">
        @foreach($recent as $day)
            @if($day->problem)
            <a href="{{ route('problems.show', $day->problem->slug) }}" class="dc-row" data-i="{{ $loop->index }}">
                <div class="dc-cal"><small>{{ $day->challenge_date->format('M') }}</small><b>{{ $day->challenge_date->format('d') }}</b></div>
                <div style="min-width:0">
                    <div class="dc-row-t">{{ $day->problem->title }}</div>
                    <div class="dc-row-s">[{{ $day->problem->difficulty }}] • {{ $day->solved_count }} {{ __('solved') }}</div>
                </div>
                <i class="fas fa-chevron-right dc-row-go"></i>
            </a>
            @endif
        @endforeach
    </div>
    @else
    <div class="dc-empty">
        <i class="fas fa-calendar-day"></i>
        <p>{{ __('History builds up day by day — come back tomorrow') }}</p>
    </div>
    @endif
</div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    /* --- Hero mouse 3D parallax --- */
    var hero = document.getElementById('dcHero');
    var quest = document.getElementById('dcQuest');
    var layers = document.querySelectorAll('#dcHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0, heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('dc-paused', !heroVisible);
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
                if (quest) quest.style.transform = 'rotateX(' + (6 + rx * -1).toFixed(2) + 'deg) rotateY(' + (-10 + ry).toFixed(2) + 'deg)';
            }
            requestAnimationFrame(tilt);
        })();
    }

    /* --- Animated counters --- */
    document.querySelectorAll('.dc-stat-val[data-count]').forEach(function(el) {
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

    /* --- Countdown to midnight --- */
    var clock = document.getElementById('dcClock');
    function tick() {
        if (!clock) return;
        var now = new Date();
        var end = new Date(now); end.setHours(24, 0, 0, 0);
        var s = Math.max(0, Math.floor((end - now) / 1000));
        var h = String(Math.floor(s / 3600)).padStart(2, '0');
        var m = String(Math.floor(s % 3600 / 60)).padStart(2, '0');
        var ss = String(s % 60).padStart(2, '0');
        clock.textContent = h + ':' + m + ':' + ss;
    }
    tick(); setInterval(tick, 1000);

    /* --- Reveal days + rows --- */
    function revealAll(sel) {
        var els = document.querySelectorAll(sel);
        if ('IntersectionObserver' in window && els.length) {
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
            els.forEach(function(x) { io.observe(x); });
            setTimeout(function() { els.forEach(function(x) { x.classList.add('in'); }); }, 4000);
        } else {
            els.forEach(function(x) { x.classList.add('in'); });
        }
    }
    revealAll('#dcDays .dc-day');
    revealAll('#dcRows .dc-row');

    /* --- Scroll to week --- */
    var toWeek = document.getElementById('dcToWeek');
    if (toWeek) toWeek.addEventListener('click', function(e) {
        e.preventDefault();
        var t = document.getElementById('dcWeek');
        if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();
</script>
@endsection
