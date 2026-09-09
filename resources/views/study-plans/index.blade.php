@extends('layouts.app')
@section('title', __('Study Plans'))

@section('head')
<style>
    /* ============ STUDY PLANS: BATTLE PLAN THEME + 3D HERO ============ */
    .sp-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .sp-hero3d { position: relative; min-height: 100vh; display: flex; align-items: center;
        overflow: hidden; padding: 110px clamp(20px,4vw,56px) 90px; isolation: isolate; perspective: 1600px; }
    .sp-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .sp-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(139,92,246,.14) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(34,197,94,.10) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(56,189,248,.10) 0%, transparent 60%);
        animation: spAurora 22s ease-in-out infinite alternate; }
    @@keyframes spAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); } }
    .sp-grid3d { position: absolute; inset: -50%;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px; opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2); transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%); }
    .sp-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .sp-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: spOrb1 16s ease-in-out infinite; }
    .sp-orb-2 { width: 460px; height: 460px; background: #8b5cf6; opacity: .10; bottom: -18%; right: -6%; animation: spOrb2 20s ease-in-out infinite; }
    .sp-orb-3 { width: 260px; height: 260px; background: #22c55e; opacity: .09; top: 55%; left: 42%; animation: spOrb3 12s ease-in-out infinite; }
    @@keyframes spOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes spOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes spOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }
    @@keyframes spBlink { 0%,100%{opacity:1} 50%{opacity:.35} }

    .sp-hero3d-inner { position: relative; z-index: 2; width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px,5vw,72px); align-items: center; }
    .sp-eyebrow { display: inline-flex; align-items: center; gap: 10px; padding: 8px 18px; border-radius: 100px;
        background: var(--accent-glow); border: 1px solid var(--accent-glow-strong);
        font-size: 11px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase; color: var(--accent); margin-bottom: 22px; }
    .sp-eyebrow i { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); animation: spBlink 1.6s infinite; }
    .sp-title { font-size: clamp(42px,6.2vw,84px); font-weight: 900; line-height: .95; letter-spacing: -3px;
        margin: 0 0 18px; color: var(--text); }
    .sp-title .grad { background: linear-gradient(120deg, var(--accent), #8b5cf6 45%, #22c55e 85%); background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: spGradShift 7s ease-in-out infinite; display: inline-block;
        filter: drop-shadow(0 8px 30px var(--accent-glow-strong)); }
    @@keyframes spGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    .sp-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.75; max-width: 540px; margin-bottom: 26px; }
    .sp-sub b { color: var(--text); }
    .sp-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
    .sp-btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 26px; border-radius: 16px;
        font-weight: 800; font-size: 14px; text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
        border: 1px solid transparent; cursor: pointer; }
    .sp-btn-ai { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff;
        box-shadow: 0 10px 32px var(--accent-glow-strong); }
    .sp-btn-ai:hover { transform: translateY(-3px) scale(1.02); }
    .sp-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .sp-btn-ghost:hover { border-color: #eab308; color: #eab308; transform: translateY(-3px); }
    .sp-stats3d { display: flex; gap: clamp(20px,3.5vw,44px); flex-wrap: wrap; }
    .sp-stat { position: relative; }
    .sp-stat + .sp-stat::before { content:''; position: absolute; left: -22px; top: 50%; transform: translateY(-50%);
        width: 1px; height: 38px; background: var(--border); }
    .sp-stat-val { font-size: clamp(26px,3vw,38px); font-weight: 900; line-height: 1; font-variant-numeric: tabular-nums;
        background: linear-gradient(135deg,var(--text),var(--accent)); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; }
    .sp-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px;
        margin-top: 6px; font-weight: 600; }

    /* --- 3D week-plan stage --- */
    .sp-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .sp-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .sp-ring-1 { width: 480px; height: 480px; animation: spSpin 26s linear infinite; opacity: .7; }
    .sp-ring-2 { width: 590px; height: 590px; animation: spSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes spSpin { to { transform: rotate(360deg); } }
    @@keyframes spSpinRev { to { transform: rotate(-360deg); } }
    .sp-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: var(--accent);
        box-shadow: 0 0 14px var(--accent); top: -5px; left: 50%; }
    .sp-week3d { position: relative; width: 100%; max-width: 440px; padding: 24px;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid var(--border); border-radius: 24px;
        box-shadow: 0 40px 100px rgba(0,0,0,.35), 0 0 80px var(--accent-glow);
        transform-style: preserve-3d; transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear; z-index: 3; }
    .sp-week3d::after { content:''; position: absolute; inset: 0; border-radius: 24px; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: spSheen 6s ease-in-out infinite; }
    @@keyframes spSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .sp-week-head { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; transform: translateZ(40px); }
    .sp-week-head .ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center;
        justify-content: center; font-size: 16px; color: #fff; background: linear-gradient(135deg,var(--accent),#8b5cf6);
        box-shadow: 0 8px 20px var(--accent-glow-strong); }
    .sp-week-head b { font-size: 14px; color: var(--text); display: block; }
    .sp-week-head span { font-size: 11px; color: var(--text-muted); font-family: var(--font-mono); }
    .sp-week-head .pct { margin-left: auto; font-family: var(--font-mono); font-size: 20px; font-weight: 800; color: var(--accent); }
    .sp-day { display: flex; align-items: center; gap: 12px; padding: 9px 12px; border-radius: 13px;
        border: 1px solid transparent; margin-bottom: 6px; transform: translateZ(25px); }
    .sp-day:last-child { margin-bottom: 0; }
    .sp-check { width: 26px; height: 26px; border-radius: 9px; border: 1.5px solid var(--border-hover);
        display: flex; align-items: center; justify-content: center; font-size: 12px; color: transparent; flex-shrink: 0;
        animation: spCheck 10.5s ease-in-out infinite; }
    .sp-day:nth-child(2) .sp-check { animation-delay: 0s; } .sp-day:nth-child(3) .sp-check { animation-delay: 1.5s; }
    .sp-day:nth-child(4) .sp-check { animation-delay: 3s; } .sp-day:nth-child(5) .sp-check { animation-delay: 4.5s; }
    .sp-day:nth-child(6) .sp-check { animation-delay: 6s; } .sp-day:nth-child(7) .sp-check { animation-delay: 7.5s; }
    .sp-day:nth-child(8) .sp-check { animation-delay: 9s; }
    @@keyframes spCheck {
        0%, 100% { background: transparent; border-color: var(--border-hover); color: transparent; box-shadow: none; }
        7%, 86% { background: rgba(34,197,94,.15); border-color: #22c55e; color: #4ade80;
            box-shadow: 0 0 12px rgba(34,197,94,.35); }
        93% { background: transparent; border-color: var(--border-hover); color: transparent; box-shadow: none; } }
    .sp-day-name { font-size: 12px; font-weight: 800; font-family: var(--font-mono); color: var(--text-secondary);
        width: 34px; text-transform: uppercase; }
    .sp-day-task { flex: 1; font-size: 12.5px; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sp-day-xp { font-size: 10px; font-weight: 800; font-family: var(--font-mono); color: var(--text-muted); }
    .sp-week-bar { height: 8px; border-radius: 4px; background: var(--border); overflow: hidden; margin-top: 14px;
        transform: translateZ(20px); }
    .sp-week-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg,var(--accent),#8b5cf6,#22c55e);
        box-shadow: 0 0 12px var(--accent-glow-strong); animation: spWeek 10.5s ease-in-out infinite; }
    @@keyframes spWeek { 0%,100% { width: 8%; } 80% { width: 92%; } 93% { width: 8%; } }
    .sp-float-chip { position: absolute; z-index: 4; display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent); backdrop-filter: blur(18px);
        border: 1px solid var(--border); border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3); font-size: 13px; animation: spFloatY 4.5s ease-in-out infinite; }
    @@keyframes spFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .sp-fc-1 { top: 4%; right: -6px; } .sp-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .sp-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .sp-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0; }
    .sp-fc-ico.g { background: rgba(139,92,246,.14); color: #8b5cf6; }
    .sp-fc-ico.p { background: rgba(34,197,94,.14); color: #22c55e; }
    .sp-fc-ico.a { background: rgba(245,158,11,.14); color: #f59e0b; }
    .sp-fc-txt b { display: block; font-size: 13px; color: var(--text); }
    .sp-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .sp-cube { position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px; display: flex;
        align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        animation: spCubeFloat 6s ease-in-out infinite; }
    .sp-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,var(--accent),#8b5cf6); }
    .sp-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#22c55e,#15803d); animation-delay: 1.5s; }
    @@keyframes spCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .sp-scroll-hint { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);
        font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-family: var(--font-mono); }
    .sp-paused .sp-aurora, .sp-paused .sp-orb, .sp-paused .sp-ring, .sp-paused .sp-cube,
    .sp-paused .sp-float-chip { animation-play-state: paused !important; }
    .sp-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .sp-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px;
        height: 8px; border-radius: 4px; background: var(--accent); animation: spWheel 1.8s ease-in-out infinite; }
    @@keyframes spWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ PLANS LIST ============ */
    .sp-wrap { max-width: 900px; margin: 0 auto; padding: 70px clamp(20px,4vw,48px) 90px; }
    .sp-sec { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
    .sp-sec h2 { font-size: 17px; font-weight: 900; color: var(--text); margin: 0; letter-spacing: -.3px; }
    .sp-sec .cnt { font-size: 11px; padding: 3px 10px; border-radius: 7px; font-weight: 800;
        background: var(--accent-glow); color: var(--accent); font-family: var(--font-mono); }
    .sp-sec i { font-size: 15px; }
    .sp-plan { display: flex; align-items: center; gap: 16px; padding: 19px 20px; border-radius: 18px;
        border: 1px solid var(--border); background: var(--card); text-decoration: none; position: relative; overflow: hidden;
        margin-bottom: 12px; box-shadow: 0 10px 30px rgba(0,0,0,.08);
        opacity: 0; transform: translateY(24px);
        transition: opacity .5s ease, transform .5s cubic-bezier(.16,1,.3,1), border-color .25s, box-shadow .25s; }
    .sp-plan.in { opacity: 1; transform: none; }
    .sp-plan:hover { border-color: var(--accent); transform: translateY(-3px); box-shadow: 0 18px 44px rgba(0,0,0,.14); }
    .sp-plan-ico { width: 52px; height: 52px; border-radius: 15px; display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0; transition: transform .3s; }
    .sp-plan:hover .sp-plan-ico { transform: scale(1.08) rotate(-4deg); }
    .sp-plan-t { font-size: 14.5px; font-weight: 800; color: var(--text); margin-bottom: 5px; line-height: 1.3; }
    .sp-plan-d { font-size: 12px; color: var(--text-muted); margin-bottom: 7px; line-height: 1.5; }
    .sp-badges { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; margin-bottom: 8px; }
    .sp-badge { font-size: 10px; padding: 3px 9px; border-radius: 6px; font-weight: 800; font-family: var(--font-mono);
        text-transform: uppercase; letter-spacing: .4px; }
    .sp-bar { height: 5px; border-radius: 3px; background: var(--border); overflow: hidden; }
    .sp-bar div { height: 100%; border-radius: 3px; background: linear-gradient(90deg,var(--accent),#8b5cf6);
        width: 0; transition: width 1.1s cubic-bezier(.16,1,.3,1); }
    .sp-prog { font-size: 11px; color: var(--text-muted); margin-top: 5px; font-family: var(--font-mono); }
    .sp-deadline { font-size: 10.5px; font-weight: 700; font-family: var(--font-mono); }
    .sp-done { font-size: 11px; padding: 6px 11px; border-radius: 8px; font-weight: 800;
        background: rgba(34,197,94,.12); color: #22c55e; flex-shrink: 0; }
    .sp-go { color: var(--text-muted); font-size: 13px; flex-shrink: 0; transition: all .25s; }
    .sp-plan:hover .sp-go { color: var(--accent); transform: translateX(4px); }
    .sp-empty { text-align: center; color: var(--text-muted); font-size: 14px; padding: 56px 0; font-family: var(--font-mono); }
    .sp-empty i { font-size: 36px; margin-bottom: 12px; display: block; opacity: .3; }
    .sp-gap { height: 40px; }

    @@media(max-width: 1020px) {
        .sp-hero3d-inner { grid-template-columns: 1fr; }
        .sp-stage { height: 560px; }
        .sp-fc-3 { right: 0; } .sp-fc-1 { right: 0; } .sp-fc-2 { left: 0; }
    }
</style>
@endsection

@section('content')
<div class="sp-page">
{{-- ================= HERO 3D ================= --}}
<section class="sp-hero3d" id="spHero">
    <div class="sp-hero3d-bg">
        <div class="sp-aurora"></div>
        <div class="sp-grid3d" data-depth="18"></div>
        <div class="sp-orb sp-orb-1" data-depth="40"></div>
        <div class="sp-orb sp-orb-2" data-depth="-30"></div>
        <div class="sp-orb sp-orb-3" data-depth="60"></div>
    </div>

    <div class="sp-hero3d-inner">
        <div>
            <span class="sp-eyebrow"><i></i>{{ __('personal syllabus') }}</span>
            <h1 class="sp-title">{!! __('Your <span class="grad">Battle Plan</span>') !!}</h1>
            <p class="sp-sub">{!! __('<b>AI builds</b> a week of tasks around your level and deadline. Tick days, watch the bar fill, <b>finish strong</b>.') !!}</p>

            <div class="sp-hero-actions">
                <a href="{{ route('study-plans.create') }}" class="sp-btn sp-btn-ai"><i class="fas fa-plus"></i>{{ __('Create AI Plan') }}</a>
                <a href="{{ route('study-plans.favorite') }}" class="sp-btn sp-btn-ghost"><i class="fas fa-star"></i>{{ __('Favorites') }}</a>
            </div>

            <div class="sp-stats3d">
                <div class="sp-stat"><div class="sp-stat-val" data-count="{{ $userPlans->count() }}">0</div><div class="sp-stat-label">{{ __('My plans') }}</div></div>
                <div class="sp-stat"><div class="sp-stat-val" data-count="{{ (int)$userPlans->sum('total_problems') }}">0</div><div class="sp-stat-label">{{ __('Tasks queued') }}</div></div>
                <div class="sp-stat"><div class="sp-stat-val" data-count="{{ $plans->count() }}">0</div><div class="sp-stat-label">{{ __('Curated') }}</div></div>
            </div>
        </div>

        <div class="sp-stage">
            <div class="sp-ring sp-ring-1"><span class="sp-ring-dot"></span></div>
            <div class="sp-ring sp-ring-2"><span class="sp-ring-dot" style="background:#8b5cf6;box-shadow:0 0 14px #8b5cf6"></span></div>
            <div class="sp-cube sp-cube-1" data-depth="70"><i class="fas fa-calendar-check"></i></div>
            <div class="sp-cube sp-cube-2" data-depth="-60"><i class="fas fa-list-check"></i></div>

            <div class="sp-week3d" id="spWeek">
                <div class="sp-week-head">
                    <span class="ico"><i class="fas fa-bolt"></i></span>
                    <div><b>{{ __('This week') }}</b><span>7 days • 12 tasks</span></div>
                    <span class="pct">86%</span>
                </div>
                @php
                    $demoDays = [
                        ['mon', 'Arrays + warmup', '+20 xp'], ['tue', 'Two pointers', '+20 xp'],
                        ['wed', 'Hash maps', '+30 xp'], ['thu', 'Mock contest', '+50 xp'],
                        ['fri', 'DP basics', '+30 xp'], ['sat', 'Review + flashcards', '+20 xp'],
                        ['sun', 'Rest • read solutions', '+10 xp'],
                    ];
                @endphp
                @foreach($demoDays as $d)
                <div class="sp-day">
                    <span class="sp-check"><i class="fas fa-check"></i></span>
                    <span class="sp-day-name">{{ $d[0] }}</span>
                    <span class="sp-day-task">{{ $d[1] }}</span>
                    <span class="sp-day-xp">{{ $d[2] }}</span>
                </div>
                @endforeach
                <div class="sp-week-bar"><div class="sp-week-fill"></div></div>
            </div>

            <div class="sp-float-chip sp-fc-1" data-depth="50">
                <div class="sp-fc-ico g"><i class="fas fa-wand-magic-sparkles"></i></div>
                <div class="sp-fc-txt"><b>{{ __('AI built') }}</b><span>{{ __('around your level') }}</span></div>
            </div>
            <div class="sp-float-chip sp-fc-2" data-depth="-45">
                <div class="sp-fc-ico p"><i class="fas fa-check-double"></i></div>
                <div class="sp-fc-txt"><b>{{ __('Tick days') }}</b><span>{{ __('streak grows') }}</span></div>
            </div>
            <div class="sp-float-chip sp-fc-3" data-depth="35">
                <div class="sp-fc-ico a"><i class="fas fa-hourglass-half"></i></div>
                <div class="sp-fc-txt"><b>{{ __('Deadline') }}</b><span>{{ __('no mercy') }}</span></div>
            </div>
        </div>
    </div>

    <div class="sp-scroll-hint"><div class="sp-mouse"></div><span>{{ __('Scroll — plans') }}</span></div>
</section>

{{-- ================= PLANS ================= --}}
<div class="sp-wrap" id="spList">
    @if($userPlans->count() > 0)
    <div class="sp-sec">
        <i class="fas fa-wand-magic-sparkles" style="color:var(--accent)"></i>
        <h2>{{ __('My AI Plans') }}</h2>
        <span class="cnt">{{ $userPlans->count() }}</span>
    </div>
    <div id="spMine">
        @foreach($userPlans as $plan)
        <a href="{{ route('study-plans.user.show', $plan) }}" class="sp-plan" data-i="{{ $loop->index }}">
            <div class="sp-plan-ico" style="background:linear-gradient(135deg,var(--accent),#8b5cf6);color:#fff">
                <i class="fas fa-wand-magic-sparkles"></i>
            </div>
            <div style="flex:1;min-width:0">
                <div class="sp-plan-t">{{ $plan->title }}</div>
                <div class="sp-badges">
                    <span class="sp-badge" style="background:var(--accent-glow);color:var(--accent)">{{ $plan->total_problems }} {{ __('problems') }}</span>
                    <span class="sp-badge" style="@if($plan->difficulty === 'easy') background:rgba(34,197,94,.12);color:#22c55e @elseif($plan->difficulty === 'medium') background:rgba(245,158,11,.12);color:#f59e0b @else background:rgba(239,68,68,.12);color:#ef4444 @endif">{{ __('difficulty_' . $plan->difficulty) }}</span>
                    @if($plan->deadline)
                    <span class="sp-deadline" style="color:{{ $plan->daysLeft() < 3 ? '#ef4444' : 'var(--text-muted)' }}"><i class="fas fa-clock"></i> {{ $plan->daysLeft() }}d {{ __('left') }}</span>
                    @endif
                </div>
                @if($plan->progressPercent() > 0)
                <div class="sp-bar"><div data-w="{{ $plan->progressPercent() }}"></div></div>
                <div class="sp-prog">{{ $plan->completed_problems }}/{{ $plan->total_problems }} ({{ $plan->progressPercent() }}%)</div>
                @endif
            </div>
            @if($plan->isCompleted())
            <span class="sp-done"><i class="fas fa-check"></i> done</span>
            @else
            <i class="fas fa-chevron-right sp-go"></i>
            @endif
        </a>
        @endforeach
    </div>
    <div class="sp-gap"></div>
    @endif

    <div class="sp-sec">
        <i class="fas fa-list-ol" style="color:#8b5cf6"></i>
        <h2>{{ __('Curated Plans') }}</h2>
        <span class="cnt">{{ $plans->count() }}</span>
    </div>
    <div id="spCurated">
        @forelse($plans as $plan)
        <a href="{{ route('study-plans.show', $plan->slug) }}" class="sp-plan" data-i="{{ $loop->index }}">
            <div class="sp-plan-ico" style="background:{{ $plan->color }}18;color:{{ $plan->color }}">
                <i class="fas {{ $plan->icon }}"></i>
            </div>
            <div style="flex:1;min-width:0">
                <div class="sp-plan-t">{{ $plan->title }}</div>
                <div class="sp-plan-d">{{ Str::limit($plan->description, 90) }}</div>
                <div class="sp-badges">
                    <span class="sp-badge" style="background:var(--bg-secondary);color:var(--text-muted);border:1px solid var(--border)">{{ $plan->problems_count }} {{ __('problems') }}</span>
                    @if($plan->user_progress > 0)
                    <span class="sp-badge" style="background:rgba(34,197,94,.12);color:#22c55e">{{ $plan->progressPercent() }}%</span>
                    @endif
                </div>
                @if($plan->progressPercent() > 0)
                <div class="sp-bar"><div data-w="{{ $plan->progressPercent() }}" style="background:{{ $plan->color }}"></div></div>
                @endif
            </div>
            <i class="fas fa-chevron-right sp-go"></i>
        </a>
        @empty
        <div class="sp-empty">
            <i class="fas fa-list-ol"></i>
            <p>{{ __('No curated plans yet') }}</p>
        </div>
        @endforelse
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    /* --- Hero mouse 3D parallax --- */
    var hero = document.getElementById('spHero');
    var week = document.getElementById('spWeek');
    var layers = document.querySelectorAll('#spHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0, heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('sp-paused', !heroVisible);
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
                if (week) week.style.transform = 'rotateX(' + (6 + rx * -1).toFixed(2) + 'deg) rotateY(' + (-10 + ry).toFixed(2) + 'deg)';
            }
            requestAnimationFrame(tilt);
        })();
    }

    /* --- Animated counters --- */
    document.querySelectorAll('.sp-stat-val[data-count]').forEach(function(el) {
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

    /* --- Plans reveal + progress bars --- */
    function fillBars(scope) {
        (scope || document).querySelectorAll('.sp-bar div[data-w]').forEach(function(b) {
            b.style.width = b.dataset.w + '%';
        });
    }
    var cards = document.querySelectorAll('.sp-plan');
    if ('IntersectionObserver' in window && cards.length) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(en) {
                if (en.isIntersecting) {
                    var el = en.target;
                    el.style.transitionDelay = (parseInt(el.dataset.i || 0, 10) % 8 * 0.06) + 's';
                    el.classList.add('in');
                    setTimeout(function() { fillBars(el); }, 250);
                    io.unobserve(el);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
        cards.forEach(function(c) { io.observe(c); });
        setTimeout(function() { cards.forEach(function(c) { c.classList.add('in'); }); fillBars(document); }, 4000);
    } else {
        cards.forEach(function(c) { c.classList.add('in'); }); fillBars(document);
    }

    /* --- Cmd+K focuses nothing here, scroll helper --- */
    var toList = document.getElementById('spToList');
    if (toList) toList.addEventListener('click', function(e) {
        e.preventDefault();
        var t = document.getElementById('spList');
        if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();
</script>
@endsection
