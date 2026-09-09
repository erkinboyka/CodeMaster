@extends('layouts.app')
@section('title', __('Points & XP'))

@section('head')
<style>
    /* ============ POINTS: TREASURY THEME + 3D HERO ============ */
    .px-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .px-hero3d { position: relative; min-height: 100vh; display: flex; align-items: center;
        overflow: hidden; padding: 110px clamp(20px,4vw,56px) 90px; isolation: isolate; perspective: 1600px; }
    .px-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .px-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(245,158,11,.14) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(34,197,94,.10) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(139,92,246,.10) 0%, transparent 60%);
        animation: pxAurora 22s ease-in-out infinite alternate; }
    @@keyframes pxAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); } }
    .px-grid3d { position: absolute; inset: -50%;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px; opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2); transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%); }
    .px-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .px-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: pxOrb1 16s ease-in-out infinite; }
    .px-orb-2 { width: 460px; height: 460px; background: #f59e0b; opacity: .10; bottom: -18%; right: -6%; animation: pxOrb2 20s ease-in-out infinite; }
    .px-orb-3 { width: 260px; height: 260px; background: #22c55e; opacity: .09; top: 55%; left: 42%; animation: pxOrb3 12s ease-in-out infinite; }
    @@keyframes pxOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes pxOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes pxOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }
    @@keyframes pxBlink { 0%,100%{opacity:1} 50%{opacity:.35} }

    .px-hero3d-inner { position: relative; z-index: 2; width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px,5vw,72px); align-items: center; }
    .px-eyebrow { display: inline-flex; align-items: center; gap: 10px; padding: 8px 18px; border-radius: 100px;
        background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.3);
        font-size: 11px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase; color: #fbbf24; margin-bottom: 22px; }
    .px-eyebrow i { animation: pxBlink 1.6s infinite; }
    .px-title { font-size: clamp(42px,6.2vw,84px); font-weight: 900; line-height: .95; letter-spacing: -3px;
        margin: 0 0 18px; color: var(--text); }
    .px-title .grad { background: linear-gradient(120deg, #f59e0b, var(--accent) 55%, #22c55e 90%); background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: pxGradShift 7s ease-in-out infinite; display: inline-block;
        filter: drop-shadow(0 8px 30px rgba(245,158,11,.22)); }
    @@keyframes pxGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    .px-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.75; max-width: 540px; margin-bottom: 26px; }
    .px-sub b { color: var(--text); }
    .px-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
    .px-btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 26px; border-radius: 16px;
        font-weight: 800; font-size: 14px; text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
        border: 1px solid transparent; cursor: pointer; }
    .px-btn-go { background: linear-gradient(135deg,#f59e0b,var(--accent)); color: #fff;
        box-shadow: 0 10px 32px rgba(245,158,11,.35); }
    .px-btn-go:hover { transform: translateY(-3px) scale(1.02); }
    .px-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .px-btn-ghost:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-3px); }
    .px-stats3d { display: flex; gap: clamp(20px,3.5vw,44px); flex-wrap: wrap; }
    .px-stat { position: relative; }
    .px-stat + .px-stat::before { content:''; position: absolute; left: -22px; top: 50%; transform: translateY(-50%);
        width: 1px; height: 38px; background: var(--border); }
    .px-stat-val { font-size: clamp(26px,3vw,38px); font-weight: 900; line-height: 1; font-variant-numeric: tabular-nums;
        background: linear-gradient(135deg,var(--text),#f59e0b); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; }
    .px-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px;
        margin-top: 6px; font-weight: 600; }

    /* --- 3D vault card stage --- */
    .px-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .px-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .px-ring-1 { width: 480px; height: 480px; animation: pxSpin 26s linear infinite; opacity: .7; }
    .px-ring-2 { width: 590px; height: 590px; animation: pxSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes pxSpin { to { transform: rotate(360deg); } }
    @@keyframes pxSpinRev { to { transform: rotate(-360deg); } }
    .px-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;
        box-shadow: 0 0 14px #f59e0b; top: -5px; left: 50%; }
    .px-vault3d { position: relative; width: 100%; max-width: 440px; padding: 28px; overflow: hidden;
        background: linear-gradient(150deg, #1a1a2e 0%, #2d2d44 60%, #1a1a2e 100%);
        border: 1px solid rgba(245,158,11,.35); border-radius: 24px;
        box-shadow: 0 40px 100px rgba(0,0,0,.45), 0 0 80px rgba(245,158,11,.15);
        transform-style: preserve-3d; transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear; z-index: 3; color: #fff; }
    [data-theme*="-light"] .px-vault3d { background: linear-gradient(150deg,#312e81 0%,#6d28d9 60%,#312e81 100%); }
    .px-vault3d::before { content:''; position: absolute; width: 300px; height: 300px; border-radius: 50%;
        right: -100px; top: -100px; background: radial-gradient(circle, rgba(245,158,11,.25) 0%, transparent 70%);
        pointer-events: none; }
    .px-vault3d::after { content:''; position: absolute; inset: 0; border-radius: 24px; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.09) 45%, transparent 60%);
        transform: translateX(-100%); animation: pxSheen 6s ease-in-out infinite; }
    @@keyframes pxSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .px-vault-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px;
        transform: translateZ(40px); position: relative; z-index: 1; }
    .px-vault-brand { font-family: var(--font-mono); font-size: 11px; font-weight: 800; letter-spacing: 2.5px;
        color: rgba(255,255,255,.75); }
    .px-vault-chip-ico { width: 40px; height: 30px; border-radius: 7px;
        background: linear-gradient(135deg,#fbbf24,#b45309); box-shadow: inset 0 0 0 1px rgba(255,255,255,.3); }
    .px-vault-xp { font-family: var(--font-mono); font-size: 44px; font-weight: 800; line-height: 1;
        color: #fff; font-variant-numeric: tabular-nums; transform: translateZ(50px); position: relative; z-index: 1; }
    .px-vault-sub { font-size: 10.5px; color: rgba(255,255,255,.55); text-transform: uppercase; letter-spacing: 2px;
        font-weight: 700; margin: 6px 0 16px; position: relative; z-index: 1; }
    .px-vault-bar { height: 10px; border-radius: 5px; background: rgba(255,255,255,.15); overflow: hidden;
        margin-bottom: 8px; transform: translateZ(25px); position: relative; z-index: 1; }
    .px-vault-fill { height: 100%; width: 0; border-radius: 5px; background: linear-gradient(90deg,#fbbf24,#f97316);
        box-shadow: 0 0 14px rgba(251,191,36,.7); animation: pxVault 2.2s cubic-bezier(.16,1,.3,1) .4s forwards; }
    @@keyframes pxVault { to { width: var(--w, 50%); } }
    .px-vault-meta { display: flex; justify-content: space-between; font-size: 10px; color: rgba(255,255,255,.55);
        font-family: var(--font-mono); margin-bottom: 16px; position: relative; z-index: 1; }
    .px-vault-num { font-family: var(--font-mono); font-size: 17px; letter-spacing: 3px; color: rgba(255,255,255,.92);
        margin-bottom: 12px; position: relative; z-index: 1; font-variant-numeric: tabular-nums; }
    .px-vault-holder { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 18px;
        position: relative; z-index: 1; }
    .px-vault-holder small { display: block; font-size: 8.5px; text-transform: uppercase; letter-spacing: 1.8px;
        color: rgba(255,255,255,.45); font-weight: 700; margin-bottom: 3px; }
    .px-vault-holder b { font-size: 12.5px; color: #fff; letter-spacing: .4px; overflow: hidden;
        text-overflow: ellipsis; white-space: nowrap; max-width: 200px; display: block; }
    .px-vault-holder .r { text-align: right; }
    .px-vault-row { display: flex; gap: 10px; transform: translateZ(20px); position: relative; z-index: 1; }
    .px-vault-cell { flex: 1; border-radius: 14px; background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12); padding: 11px 12px; }
    .px-vault-cell b { display: block; font-size: 16px; font-weight: 900; font-variant-numeric: tabular-nums; }
    .px-vault-cell span { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; opacity: .6; font-weight: 700; }
    .px-float-chip { position: absolute; z-index: 4; display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent); backdrop-filter: blur(18px);
        border: 1px solid var(--border); border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3); font-size: 13px; animation: pxFloatY 4.5s ease-in-out infinite; }
    @@keyframes pxFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .px-fc-1 { top: 4%; right: -6px; } .px-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .px-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .px-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0; }
    .px-fc-ico.g { background: rgba(245,158,11,.14); color: #f59e0b; }
    .px-fc-ico.p { background: rgba(34,197,94,.14); color: #22c55e; }
    .px-fc-ico.a { background: rgba(139,92,246,.14); color: #8b5cf6; }
    .px-fc-txt b { display: block; font-size: 13px; color: var(--text); font-family: var(--font-mono); }
    .px-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .px-cube { position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px; display: flex;
        align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        animation: pxCubeFloat 6s ease-in-out infinite; }
    .px-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,#f59e0b,#b45309); }
    .px-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#22c55e,#15803d); animation-delay: 1.5s; }
    @@keyframes pxCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .px-scroll-hint { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);
        font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-family: var(--font-mono); }
    .px-paused .px-aurora, .px-paused .px-orb, .px-paused .px-ring, .px-paused .px-cube,
    .px-paused .px-float-chip { animation-play-state: paused !important; }
    .px-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .px-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px;
        height: 8px; border-radius: 4px; background: #f59e0b; animation: pxWheel 1.8s ease-in-out infinite; }
    @@keyframes pxWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ LEDGER ============ */
    .px-wrap { max-width: 1000px; margin: 0 auto; padding: 70px clamp(20px,4vw,48px) 90px; }
    .px-level-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px;
        padding: 24px; margin-bottom: 20px; box-shadow: 0 14px 40px rgba(0,0,0,.1);
        opacity: 0; transform: translateY(22px); transition: opacity .5s ease, transform .5s cubic-bezier(.16,1,.3,1); }
    .px-level-card.in { opacity: 1; transform: none; }
    .px-level-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
    .px-level-title { font-size: 14px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 9px; }
    .px-level-sub { font-size: 12px; color: var(--text-muted); font-family: var(--font-mono); }
    .px-bar { height: 10px; border-radius: 5px; background: var(--border); overflow: hidden; position: relative; }
    .px-bar__fill { height: 100%; width: 0; border-radius: 5px; background: linear-gradient(90deg,#f59e0b,var(--accent));
        transition: width 1.2s cubic-bezier(.16,1,.3,1); position: relative; }
    .px-bar__fill::after { content:''; position: absolute; inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.35), transparent);
        animation: pxShimmer 2.2s infinite; }
    @@keyframes pxShimmer { 0%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .px-bar-labels { display: flex; justify-content: space-between; margin-top: 8px; font-size: 11px;
        color: var(--text-muted); font-family: var(--font-mono); }
    .px-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    .px-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; overflow: hidden;
        box-shadow: 0 12px 34px rgba(0,0,0,.08);
        opacity: 0; transform: translateY(22px);
        transition: opacity .5s ease, transform .5s cubic-bezier(.16,1,.3,1), border-color .25s; }
    .px-card.in { opacity: 1; transform: none; }
    .px-card:hover { border-color: color-mix(in srgb, #f59e0b 35%, var(--border)); }
    .px-card__head { padding: 16px 20px 13px; display: flex; align-items: center; gap: 9px;
        border-bottom: 1px solid var(--border); font-size: 13px; font-weight: 800; color: var(--text);
        text-transform: uppercase; letter-spacing: .8px; font-family: var(--font-mono); }
    .px-card__body { padding: 10px 14px 14px; }
    .px-row { display: flex; align-items: center; gap: 11px; padding: 9px 10px; border-radius: 11px; transition: background .15s; }
    .px-row:hover { background: var(--bg-secondary); }
    .px-row__icon { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center;
        justify-content: center; font-size: 13px; flex-shrink: 0; }
    .px-row__text { flex: 1; font-size: 13px; color: var(--text-secondary); font-weight: 600; }
    .px-row__val { font-size: 12.5px; font-weight: 800; white-space: nowrap; font-family: var(--font-mono); }
    .px-full { grid-column: 1 / -1; }
    .px-lvls { display: grid; grid-template-columns: repeat(auto-fill,minmax(190px,1fr)); gap: 8px; }
    .px-lvl { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 12px;
        transition: all .2s; border: 1px solid transparent; }
    .px-lvl:hover { background: var(--bg-secondary); }
    .px-lvl.current { background: rgba(245,158,11,.07); border-color: rgba(245,158,11,.3);
        box-shadow: 0 0 20px rgba(245,158,11,.12); }
    .px-lvl__icon { width: 36px; height: 36px; border-radius: 11px; display: flex; align-items: center;
        justify-content: center; font-size: 15px; flex-shrink: 0; }
    .px-lvl__name { font-size: 13px; font-weight: 700; color: var(--text); }
    .px-lvl__range { font-size: 10.5px; color: var(--text-muted); margin-top: 1px; font-family: var(--font-mono); }
    .px-lvl__badge { margin-left: auto; font-size: 10.5px; font-weight: 800; padding: 4px 11px; border-radius: 100px;
        font-family: var(--font-mono); }
    .px-tabs { display: flex; gap: 4px; margin-bottom: 12px; background: var(--bg-secondary);
        border: 1px solid var(--border); border-radius: 12px; padding: 4px; }
    .px-tab { flex: 1; padding: 9px; text-align: center; font-size: 12.5px; font-weight: 800; color: var(--text-muted);
        cursor: pointer; transition: all .2s; border: none; background: none; border-radius: 9px; font-family: var(--font-mono); }
    .px-tab:hover { color: var(--text); }
    .px-tab.active { color: #fff; background: linear-gradient(135deg,#f59e0b,var(--accent));
        box-shadow: 0 4px 14px rgba(245,158,11,.35); }
    .px-hist { display: flex; flex-direction: column; gap: 6px; }
    .px-hist__item { display: flex; align-items: center; gap: 12px; padding: 11px 13px; border-radius: 12px;
        background: var(--bg-secondary); border: 1px solid transparent; transition: all .15s; }
    .px-hist__item:hover { border-color: var(--border); transform: translateX(2px); }
    .px-hist__icon { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center;
        justify-content: center; font-size: 13px; flex-shrink: 0; }
    .px-hist__info { flex: 1; min-width: 0; }
    .px-hist__text { font-size: 12.5px; font-weight: 600; color: var(--text);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .px-hist__time { font-size: 10.5px; color: var(--text-muted); margin-top: 2px; font-family: var(--font-mono); }
    .px-hist__amount { font-size: 13px; font-weight: 900; white-space: nowrap; font-family: var(--font-mono); }
    .px-hist__empty { text-align: center; padding: 36px 16px; color: var(--text-muted); font-size: 13px; }
    .px-hist__empty i { font-size: 28px; margin-bottom: 10px; display: block; opacity: .3; }

    @@media(max-width: 1020px) {
        .px-hero3d-inner { grid-template-columns: 1fr; }
        .px-stage { height: 560px; }
        .px-fc-3 { right: 0; } .px-fc-1 { right: 0; } .px-fc-2 { left: 0; }
    }
    @@media(max-width:640px){ .px-grid{grid-template-columns:1fr} }
</style>
@endsection

@section('content')
<div class="px-page">
{{-- ================= HERO 3D ================= --}}
@php
    $xpForNext = $user->xp_for_next_level ?? 100;
    $xpForCurrent = $user->xp_for_current_level ?? 0;
    $lvlPct = $xpForNext > 0 ? round(($xpForCurrent / $xpForNext) * 100) : 0;
@endphp
<section class="px-hero3d" id="pxHero">
    <div class="px-hero3d-bg">
        <div class="px-aurora"></div>
        <div class="px-grid3d" data-depth="18"></div>
        <div class="px-orb px-orb-1" data-depth="40"></div>
        <div class="px-orb px-orb-2" data-depth="-30"></div>
        <div class="px-orb px-orb-3" data-depth="60"></div>
    </div>

    <div class="px-hero3d-inner">
        <div>
            <span class="px-eyebrow"><i class="fas fa-vault"></i>{{ __('treasury') }} • Lv.{{ $user->level ?? 1 }}</span>
            <h1 class="px-title">{!! __('Stack <span class="grad">XP</span>') !!}</h1>
            <p class="px-sub">{!! __('Every lesson mints <b>coins</b>, every course builds the <b>fortune</b>. Watch the vault fill.') !!}</p>

            <div class="px-hero-actions">
                <a href="{{ route('courses.index') }}" class="px-btn px-btn-go"><i class="fas fa-hammer"></i>{{ __('Earn more') }}</a>
                <a href="#pxLedger" class="px-btn px-btn-ghost" id="pxToLedger"><i class="fas fa-receipt"></i>{{ __('Ledger') }}</a>
            </div>

            <div class="px-stats3d">
                <div class="px-stat"><div class="px-stat-val" data-count="{{ (int)($user->total_xp ?? 0) }}">0</div><div class="px-stat-label">{{ __('Total XP') }}</div></div>
                <div class="px-stat"><div class="px-stat-val" data-count="{{ $user->level ?? 1 }}">0</div><div class="px-stat-label">{{ __('Level') }}</div></div>
                <div class="px-stat"><div class="px-stat-val" data-count="{{ (int)($user->ai_tokens ?? 0) }}">0</div><div class="px-stat-label">{{ __('AI Tokens') }}</div></div>
                <div class="px-stat"><div class="px-stat-val" data-count="{{ $user->streak_count ?? 0 }}">0</div><div class="px-stat-label">{{ __('Day Streak') }}</div></div>
                <div class="px-stat"><div class="px-stat-val" data-count="{{ $user->longest_streak ?? 0 }}">0</div><div class="px-stat-label">{{ __('Best Streak') }}</div></div>
            </div>
        </div>

        <div class="px-stage">
            <div class="px-ring px-ring-1"><span class="px-ring-dot"></span></div>
            <div class="px-ring px-ring-2"><span class="px-ring-dot" style="background:#8b5cf6;box-shadow:0 0 14px #8b5cf6"></span></div>
            <div class="px-cube px-cube-1" data-depth="70"><i class="fas fa-coins"></i></div>
            <div class="px-cube px-cube-2" data-depth="-60"><i class="fas fa-bolt"></i></div>

            <div class="px-vault3d" id="pxVault">
                <div class="px-vault-top">
                    <span class="px-vault-brand"><i class="fas fa-gem" style="margin-right:8px;color:#fbbf24"></i>CODEMASTER • XP</span>
                    <span class="px-vault-chip-ico"></span>
                </div>
                <div class="px-vault-xp" data-count="{{ (int)($user->total_xp ?? 0) }}">0</div>
                <div class="px-vault-sub">total xp • {{ $user->level_title ?? '' }}</div>
                <div class="px-vault-bar"><div class="px-vault-fill" style="--w:{{ $lvlPct }}%"></div></div>
                <div class="px-vault-meta"><span>LV.{{ $user->level ?? 1 }}</span><span>{{ $lvlPct }}%</span><span>LV.{{ ($user->level ?? 1) + 1 }}</span></div>
                <div class="px-vault-num">5312 •••• •••• {{ str_pad((($user->id ?? 1) * 7) % 10000, 4, '0', STR_PAD_LEFT) }}</div>
                <div class="px-vault-holder">
                    <div><small>{{ __('card holder') }}</small><b>{{ strtoupper($user->name ?? 'PLAYER') }}</b></div>
                    <div class="r"><small>{{ __('member since') }}</small><b>{{ $user->created_at ? $user->created_at->format('Y') : now()->format('Y') }}</b></div>
                </div>
                <div class="px-vault-row">
                    <div class="px-vault-cell"><b>{{ number_format($user->ai_tokens ?? 0) }}</b><span>{{ __('tokens') }}</span></div>
                    <div class="px-vault-cell"><b>{{ $user->streak_count ?? 0 }} <i class="fas fa-fire" style="font-size:13px;color:#fbbf24"></i></b><span>{{ __('streak') }}</span></div>
                </div>
            </div>

            <div class="px-float-chip px-fc-1" data-depth="50">
                <div class="px-fc-ico g"><i class="fas fa-arrow-trend-up"></i></div>
                <div class="px-fc-txt"><b>+100 XP</b><span>{{ __('per course') }}</span></div>
            </div>
            <div class="px-float-chip px-fc-2" data-depth="-45">
                <div class="px-fc-ico p"><i class="fas fa-coins"></i></div>
                <div class="px-fc-txt"><b>+5 {{ __('tokens') }}</b><span>{{ __('daily bonus') }}</span></div>
            </div>
            <div class="px-float-chip px-fc-3" data-depth="35">
                <div class="px-fc-ico a"><i class="fas fa-fire"></i></div>
                <div class="px-fc-txt"><b>{{ __('Streak') }}</b><span>{{ __('don\'t break it') }}</span></div>
            </div>
        </div>
    </div>

    <div class="px-scroll-hint"><div class="px-mouse"></div><span>{{ __('Scroll — ledger') }}</span></div>
</section>

{{-- ================= LEDGER ================= --}}
<div class="px-wrap" id="pxLedger">
    <div class="px-level-card" data-rv>
        <div class="px-level-head" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <div style="font-size:14px;font-weight:800;color:var(--text);display:flex;align-items:center;gap:9px">
                <i class="fas fa-layer-group" style="color:#f59e0b"></i>{{ __('Level') }} {{ $user->level ?? 1 }}
            </div>
            <div style="font-size:12px;color:var(--text-muted);font-family:var(--font-mono)">{{ $xpForCurrent }} / {{ $xpForNext }} XP • <b style="color:#f59e0b">{{ max(0, $xpForNext - $xpForCurrent) }}</b> {{ __('to go') }}</div>
        </div>
        <div class="px-bar"><div class="px-bar__fill" data-w="{{ $lvlPct }}"></div></div>
        <div class="px-bar-labels">
            <span>{{ $user->level_title ?? __('Beginner_title') }}</span>
            <span>Lv.{{ ($user->level ?? 1) + 1 }}</span>
        </div>
    </div>

    <div class="px-grid">
        <div class="px-card" data-rv>
            <div class="px-card__head"><i class="fas fa-bolt" style="color:var(--accent)"></i>{{ __('Earn XP') }}</div>
            <div class="px-card__body">
                <div class="px-row">
                    <div class="px-row__icon" style="background:color-mix(in srgb,var(--accent) 12%,transparent);color:var(--accent)"><i class="fas fa-book-open"></i></div>
                    <span class="px-row__text">{{ __('Per lesson') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">+10 XP</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:rgba(245,158,11,.12);color:#f59e0b"><i class="fas fa-clipboard-check"></i></div>
                    <span class="px-row__text">{{ __('Per test') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">+25 XP</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:rgba(59,130,246,.12);color:#3b82f6"><i class="fas fa-code"></i></div>
                    <span class="px-row__text">{{ __('Per practice') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">+30 XP</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:rgba(139,92,246,.12);color:#8b5cf6"><i class="fas fa-file-alt"></i></div>
                    <span class="px-row__text">{{ __('Per exam') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">+50 XP</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:rgba(34,197,94,.12);color:#22c55e"><i class="fas fa-graduation-cap"></i></div>
                    <span class="px-row__text">{{ __('Per course') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">+100 XP</span>
                </div>
            </div>
        </div>

        <div class="px-card" data-rv>
            <div class="px-card__head"><i class="fas fa-robot" style="color:#8b5cf6"></i>{{ __('AI Tokens') }}</div>
            <div class="px-card__body">
                <div class="px-row">
                    <div class="px-row__icon" style="background:rgba(245,158,11,.12);color:#f59e0b"><i class="fas fa-coins"></i></div>
                    <span class="px-row__text">{{ __('Starting balance') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">25 {{ __('tokens') }}</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:rgba(239,68,68,.12);color:#ef4444"><i class="fas fa-comment-dots"></i></div>
                    <span class="px-row__text">{{ __('Per AI message') }}</span>
                    <span class="px-row__val" style="color:#ef4444">-1 {{ __('token') }}</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:rgba(34,197,94,.12);color:#22c55e"><i class="fas fa-calendar-check"></i></div>
                    <span class="px-row__text">{{ __('Daily bonus') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">+5 {{ __('tokens') }}</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:rgba(59,130,246,.12);color:#3b82f6"><i class="fas fa-tasks"></i></div>
                    <span class="px-row__text">{{ __('Earn tokens for activity') }}</span>
                    <span class="px-row__val" style="color:var(--accent)"><i class="fas fa-arrow-right" style="font-size:10px"></i></span>
                </div>
            </div>
        </div>

        <div class="px-card px-full" data-rv>
            <div class="px-card__head"><i class="fas fa-layer-group" style="color:var(--accent)"></i>{{ __('Levels') }}</div>
            <div class="px-card__body">
                <div class="px-lvls">
                    @php
                        $levels = [
                            ['name' => __('Beginner_title'), 'icon' => 'fa-seedling', 'color' => '#22c55e', 'min' => 1, 'max' => 4],
                            ['name' => __('Student'), 'icon' => 'fa-graduation-cap', 'color' => '#3b82f6', 'min' => 5, 'max' => 9],
                            ['name' => __('Experienced'), 'icon' => 'fa-star', 'color' => '#8b5cf6', 'min' => 10, 'max' => 14],
                            ['name' => __('Advanced_title'), 'icon' => 'fa-fire', 'color' => '#f97316', 'min' => 15, 'max' => 29],
                            ['name' => __('Expert'), 'icon' => 'fa-crown', 'color' => '#eab308', 'min' => 30, 'max' => 999],
                        ];
                        $currentLevel = $user->level ?? 1;
                    @endphp
                    @foreach($levels as $lvl)
                    <div class="px-lvl {{ $currentLevel >= $lvl['min'] && $currentLevel <= $lvl['max'] ? 'current' : '' }}">
                        <div class="px-lvl__icon" style="background:{{ $lvl['color'] }}1f;color:{{ $lvl['color'] }}">
                            <i class="fas {{ $lvl['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="px-lvl__name">{{ $lvl['name'] }}</div>
                            <div class="px-lvl__range">Lv.{{ $lvl['min'] }}{{ $lvl['max'] < 999 ? '–' . $lvl['max'] : '+' }}</div>
                        </div>
                        @if($currentLevel >= $lvl['min'])
                            <span class="px-lvl__badge" style="background:{{ $currentLevel >= $lvl['min'] && $currentLevel <= $lvl['max'] ? 'linear-gradient(135deg,#f59e0b,var(--accent));color:#fff' : 'var(--bg-secondary);color:var(--text-muted)' }}">@if($currentLevel >= $lvl['min'] && $currentLevel <= $lvl['max']){{ __('You') }}@else<i class="fas fa-check"></i>@endif</span>
                        @else
                            <span class="px-lvl__badge" style="background:var(--bg-secondary);color:var(--text-muted)"><i class="fas fa-lock" style="font-size:9px"></i></span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="px-card px-full" data-rv x-data="pxHistory()">
            <div class="px-card__head"><i class="fas fa-clock-rotate-left" style="color:var(--accent)"></i>{{ __('History') }}
                <span style="margin-left:auto;display:flex;gap:8px;font-family:var(--font-mono);font-size:11px;font-weight:800">
                    <span style="color:#22c55e">+{{ $totalEarned }}</span>
                    <span style="color:var(--text-muted)">/</span>
                    <span style="color:#ef4444">−{{ $totalSpent }}</span>
                </span>
            </div>
            <div class="px-card__body">
                <div class="px-tabs">
                    <button class="px-tab" :class="filter === 'all' && 'active'" @click="filter = 'all'">{{ __('All') }}</button>
                    <button class="px-tab" :class="filter === 'xp' && 'active'" @click="filter = 'xp'">XP</button>
                    <button class="px-tab" :class="filter === 'tokens' && 'active'" @click="filter = 'tokens'">{{ __('Tokens') }}</button>
                </div>

                @php
                    $iconMap = [
                        'xp_earned' => ['icon' => 'fa-bolt', 'type' => 'plus'],
                        'tokens_earned' => ['icon' => 'fa-coins', 'type' => 'plus'],
                        'daily_bonus' => ['icon' => 'fa-calendar-check', 'type' => 'plus'],
                        'tokens_spent' => ['icon' => 'fa-comment-dots', 'type' => 'minus'],
                    ];
                @endphp

                @if($activities->isEmpty())
                    <div class="px-hist__empty">
                        <i class="fas fa-inbox"></i>
                        {{ __('No activity yet — earn your first XP') }}
                    </div>
                @else
                    <div class="px-hist">
                        @foreach($activities as $act)
                            @php
                                $map = $iconMap[$act->activity_type] ?? ['icon' => 'fa-circle', 'type' => 'plus'];
                                $filterType = in_array($act->activity_type, ['xp_earned']) ? 'xp' : 'tokens';
                            @endphp
                            <div class="px-hist__item" x-show="filter === 'all' || filter === '{{ $filterType }}'">
                                <div class="px-hist__icon" style="{{ $map['type'] === 'plus' ? 'background:rgba(34,197,94,.12);color:#22c55e' : 'background:rgba(239,68,68,.12);color:#ef4444' }}">
                                    <i class="fas {{ $map['icon'] }}"></i>
                                </div>
                                <div class="px-hist__info">
                                    <div class="px-hist__text">{{ $act->activity_text }}</div>
                                    <div class="px-hist__time">{{ $act->activity_time->diffForHumans() }}</div>
                                </div>
                                @php
                                    preg_match('/^[+-]\d+/', $act->activity_text, $matches);
                                    $amount = $matches[0] ?? '';
                                @endphp
                                @if($amount)
                                    <div class="px-hist__amount" style="color:{{ $map['type'] === 'plus' ? '#22c55e' : '#ef4444' }}">
                                        {{ $amount }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
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
    var hero = document.getElementById('pxHero');
    var vault = document.getElementById('pxVault');
    var layers = document.querySelectorAll('#pxHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0, heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('px-paused', !heroVisible);
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
                if (vault) vault.style.transform = 'rotateX(' + (6 + rx * -1).toFixed(2) + 'deg) rotateY(' + (-10 + ry).toFixed(2) + 'deg)';
            }
            requestAnimationFrame(tilt);
        })();
    }

    /* --- Animated counters --- */
    document.querySelectorAll('[data-count]').forEach(function(el) {
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

    /* --- Reveal + progress bars --- */
    function fillBars(scope) {
        (scope || document).querySelectorAll('.px-bar__fill[data-w]').forEach(function(b) {
            b.style.width = b.dataset.w + '%';
        });
    }
    var els = document.querySelectorAll('[data-rv]');
    if ('IntersectionObserver' in window && els.length) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(en) {
                if (en.isIntersecting) {
                    en.target.classList.add('in');
                    setTimeout(function() { fillBars(en.target); }, 250);
                    io.unobserve(en.target);
                }
            });
        }, { threshold: 0.06, rootMargin: '0px 0px -20px 0px' });
        els.forEach(function(x) { io.observe(x); });
        setTimeout(function() { els.forEach(function(x) { x.classList.add('in'); }); fillBars(document); }, 4000);
    } else {
        els.forEach(function(x) { x.classList.add('in'); }); fillBars(document);
    }

    /* --- Scroll to ledger --- */
    var toLedger = document.getElementById('pxToLedger');
    if (toLedger) toLedger.addEventListener('click', function(e) {
        e.preventDefault();
        var t = document.getElementById('pxLedger');
        if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();

function pxHistory() {
    return {
        filter: 'all'
    }
}
</script>
@endsection
