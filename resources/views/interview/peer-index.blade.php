@extends('layouts.app')

@section('title', __('peer.title') . ' - CodeMaster')

@section('head')
<style>
    /* ============ PEER: SPARRING THEME + 3D HERO ============ */
    .pi-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .pi-hero3d { position: relative; min-height: 100vh; display: flex; align-items: center;
        overflow: hidden; padding: 110px clamp(20px,4vw,56px) 90px; isolation: isolate; perspective: 1600px; }
    .pi-hero3d-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .pi-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 55% 40% at 18% 25%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 45% 45% at 85% 20%, rgba(34,197,94,.12) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 70% 90%, rgba(56,189,248,.12) 0%, transparent 60%),
            radial-gradient(ellipse 35% 35% at 30% 85%, rgba(139,92,246,.10) 0%, transparent 60%);
        animation: piAurora 22s ease-in-out infinite alternate; }
    @@keyframes piAurora {
        0% { transform: translate(0,0) rotate(0) scale(1); }
        50% { transform: translate(2%,-2%) rotate(1.5deg) scale(1.05); }
        100% { transform: translate(-2%,2%) rotate(-1.5deg) scale(1.02); } }
    .pi-grid3d { position: absolute; inset: -50%;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 64px 64px; opacity: .35;
        transform: rotateX(62deg) translateZ(-220px) scale(2.2); transform-origin: center;
        mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 55%, black 25%, transparent 75%); }
    .pi-orb { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
    .pi-orb-1 { width: 520px; height: 520px; background: var(--accent); opacity: .12; top: -12%; left: -8%; animation: piOrb1 16s ease-in-out infinite; }
    .pi-orb-2 { width: 460px; height: 460px; background: #22c55e; opacity: .09; bottom: -18%; right: -6%; animation: piOrb2 20s ease-in-out infinite; }
    .pi-orb-3 { width: 260px; height: 260px; background: #38bdf8; opacity: .10; top: 55%; left: 42%; animation: piOrb3 12s ease-in-out infinite; }
    @@keyframes piOrb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(70px,50px) scale(1.12)} }
    @@keyframes piOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-60px,-60px) scale(1.08)} }
    @@keyframes piOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-80px,-40px) scale(1.2)} }
    @@keyframes piBlink { 0%,100%{opacity:1} 50%{opacity:.35} }

    .pi-hero3d-inner { position: relative; z-index: 2; width: 100%; max-width: 1360px; margin: 0 auto;
        display: grid; grid-template-columns: 1.05fr .95fr; gap: clamp(32px,5vw,72px); align-items: center; }
    .pi-eyebrow { display: inline-flex; align-items: center; gap: 10px; padding: 8px 18px; border-radius: 100px;
        background: rgba(34,197,94,.08); border: 1px solid rgba(34,197,94,.28);
        font-size: 11px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase; color: #4ade80; margin-bottom: 22px; }
    .pi-eyebrow i { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: piBlink 1.6s infinite;
        box-shadow: 0 0 10px #22c55e; }
    .pi-title { font-size: clamp(42px,6.2vw,84px); font-weight: 900; line-height: .95; letter-spacing: -3px;
        margin: 0 0 18px; color: var(--text); }
    .pi-title .grad { background: linear-gradient(120deg, #22c55e, var(--accent) 55%, #38bdf8 90%); background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: piGradShift 7s ease-in-out infinite; display: inline-block;
        filter: drop-shadow(0 8px 30px rgba(34,197,94,.22)); }
    @@keyframes piGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    .pi-sub { font-size: clamp(15px,1.6vw,18px); color: var(--text-secondary); line-height: 1.75; max-width: 540px; margin-bottom: 26px; }
    .pi-sub b { color: var(--text); }
    .pi-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
    .pi-btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 28px; border-radius: 16px;
        font-weight: 800; font-size: 14px; text-decoration: none; transition: all .3s cubic-bezier(.16,1,.3,1);
        border: 1px solid transparent; cursor: pointer; }
    .pi-btn-create { background: linear-gradient(135deg,#22c55e,var(--accent)); color: #fff;
        box-shadow: 0 10px 32px rgba(34,197,94,.35); }
    .pi-btn-create:hover { transform: translateY(-3px) scale(1.02); }
    .pi-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .pi-btn-ghost:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-3px); }
    .pi-stats3d { display: flex; gap: clamp(20px,3.5vw,44px); flex-wrap: wrap; }
    .pi-stat { position: relative; }
    .pi-stat + .pi-stat::before { content:''; position: absolute; left: -22px; top: 50%; transform: translateY(-50%);
        width: 1px; height: 38px; background: var(--border); }
    .pi-stat-val { font-size: clamp(26px,3vw,38px); font-weight: 900; line-height: 1; font-variant-numeric: tabular-nums;
        background: linear-gradient(135deg,var(--text),#22c55e); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; }
    .pi-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px;
        margin-top: 6px; font-weight: 600; }

    /* --- 3D VS stage --- */
    .pi-stage { position: relative; height: 600px; display: flex; align-items: center; justify-content: center; perspective: 1400px; }
    .pi-ring { position: absolute; border-radius: 50%; border: 1px dashed var(--border-hover); pointer-events: none; }
    .pi-ring-1 { width: 480px; height: 480px; animation: piSpin 26s linear infinite; opacity: .7; }
    .pi-ring-2 { width: 590px; height: 590px; animation: piSpinRev 36s linear infinite; opacity: .45; }
    @@keyframes piSpin { to { transform: rotate(360deg); } }
    @@keyframes piSpinRev { to { transform: rotate(-360deg); } }
    .pi-ring-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: #22c55e;
        box-shadow: 0 0 14px #22c55e; top: -5px; left: 50%; }
    .pi-vs3d { position: relative; width: 100%; max-width: 500px; padding: 22px;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid var(--border); border-radius: 24px;
        box-shadow: 0 40px 100px rgba(0,0,0,.35), 0 0 80px rgba(34,197,94,.1);
        transform-style: preserve-3d; transform: rotateY(-10deg) rotateX(6deg);
        transition: transform .15s linear; z-index: 3; }
    .pi-vs3d::after { content:''; position: absolute; inset: 0; border-radius: 24px; pointer-events: none;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.07) 45%, transparent 60%);
        transform: translateX(-100%); animation: piSheen 6s ease-in-out infinite; }
    @@keyframes piSheen { 0%,60%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .pi-vs-top { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; transform: translateZ(40px); }
    .pi-rec { display: inline-flex; align-items: center; gap: 7px; font-family: var(--font-mono); font-size: 11px;
        font-weight: 800; color: #f87171; letter-spacing: 1px; }
    .pi-rec i { width: 8px; height: 8px; border-radius: 50%; background: #ef4444; animation: piBlink 1.2s infinite;
        box-shadow: 0 0 10px #ef4444; }
    .pi-clock { margin-left: auto; font-family: var(--font-mono); font-size: 13px; font-weight: 800;
        color: var(--text); background: var(--bg-secondary); border: 1px solid var(--border);
        border-radius: 9px; padding: 6px 12px; font-variant-numeric: tabular-nums; }
    .pi-duel { display: grid; grid-template-columns: 1fr auto 1fr; gap: 10px; align-items: stretch; margin-bottom: 16px; }
    .pi-cam { border-radius: 18px; padding: 18px 12px; text-align: center; position: relative; overflow: hidden;
        border: 1px solid var(--border); }
    .pi-cam.host { background: linear-gradient(160deg, rgba(34,197,94,.16), transparent 70%); }
    .pi-cam.guest { background: linear-gradient(160deg, rgba(56,189,248,.16), transparent 70%); }
    .pi-cam-ava { width: 56px; height: 56px; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center;
        justify-content: center; font-size: 22px; color: #fff; border: 2px solid rgba(255,255,255,.25);
        box-shadow: 0 8px 22px rgba(0,0,0,.3); }
    .pi-cam-name { font-size: 13px; font-weight: 800; color: var(--text); }
    .pi-cam-role { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
        color: var(--text-muted); margin-top: 3px; font-family: var(--font-mono); }
    .pi-cam-mics { display: flex; justify-content: center; gap: 6px; margin-top: 10px; }
    .pi-cam-mics span { width: 26px; height: 26px; border-radius: 8px; background: rgba(0,0,0,.3);
        display: flex; align-items: center; justify-content: center; font-size: 11px; color: #4ade80; }
    .pi-cam-mics span.off { color: #f87171; }
    .pi-vs-badge { align-self: center; width: 52px; height: 52px; border-radius: 50%;
        background: linear-gradient(135deg,#22c55e,#38bdf8); color: #fff; font-weight: 900; font-style: italic;
        display: flex; align-items: center; justify-content: center; font-size: 16px;
        box-shadow: 0 0 30px rgba(34,197,94,.5); animation: piVs 2.6s ease-in-out infinite; flex-shrink: 0; }
    @@keyframes piVs { 0%,100% { transform: scale(1) rotate(-4deg); } 50% { transform: scale(1.1) rotate(4deg); } }
    .pi-codebar { border-radius: 14px; background: rgba(0,0,0,.3); border: 1px solid var(--border);
        padding: 12px 16px; font-family: var(--font-mono); font-size: 11.5px; line-height: 1.9;
        transform: translateZ(25px); overflow: hidden; white-space: nowrap; }
    .pi-codebar .k { color: #c678dd; } .pi-codebar .f { color: #61afef; }
    .pi-codebar .v { color: #e06c75; } .pi-codebar .c { color: #636d83; }
    .pi-caret { display: inline-block; width: 7px; height: 13px; background: #22c55e; vertical-align: -2px;
        animation: piBlink 1s step-end infinite; border-radius: 2px; }
    .pi-float-chip { position: absolute; z-index: 4; display: flex; align-items: center; gap: 12px;
        background: color-mix(in srgb, var(--card) 90%, transparent); backdrop-filter: blur(18px);
        border: 1px solid var(--border); border-radius: 18px; padding: 13px 17px;
        box-shadow: 0 18px 50px rgba(0,0,0,.3); font-size: 13px; animation: piFloatY 4.5s ease-in-out infinite; }
    @@keyframes piFloatY { 0%,100%{ margin-top: 0; } 50%{ margin-top: -14px; } }
    .pi-fc-1 { top: 4%; right: -6px; } .pi-fc-2 { bottom: 12%; left: -24px; animation-delay: 1.2s; }
    .pi-fc-3 { top: 46%; right: -34px; animation-delay: 2s; }
    .pi-fc-ico { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0; }
    .pi-fc-ico.g { background: rgba(34,197,94,.14); color: #22c55e; }
    .pi-fc-ico.p { background: rgba(56,189,248,.14); color: #38bdf8; }
    .pi-fc-ico.a { background: rgba(245,158,11,.14); color: #f59e0b; }
    .pi-fc-txt b { display: block; font-size: 13px; color: var(--text); }
    .pi-fc-txt span { font-size: 11px; color: var(--text-muted); }
    .pi-cube { position: absolute; z-index: 2; width: 74px; height: 74px; border-radius: 20px; display: flex;
        align-items: center; justify-content: center; font-size: 30px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.3);
        animation: piCubeFloat 6s ease-in-out infinite; }
    .pi-cube-1 { top: 6%; left: 6%; background: linear-gradient(135deg,#22c55e,#15803d); }
    .pi-cube-2 { bottom: 6%; right: 12%; background: linear-gradient(135deg,#38bdf8,#0369a1); animation-delay: 1.5s; }
    @@keyframes piCubeFloat { 0%,100%{ translate: 0 0; rotate: -4deg; } 50%{ translate: 0 -16px; rotate: 5deg; } }
    .pi-scroll-hint { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 5;
        display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);
        font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-family: var(--font-mono); }
    .pi-paused .pi-aurora, .pi-paused .pi-orb, .pi-paused .pi-ring, .pi-paused .pi-cube,
    .pi-paused .pi-float-chip { animation-play-state: paused !important; }
    .pi-mouse { width: 26px; height: 42px; border: 2px solid var(--border-hover); border-radius: 14px; position: relative; }
    .pi-mouse::after { content:''; position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 4px;
        height: 8px; border-radius: 4px; background: #22c55e; animation: piWheel 1.8s ease-in-out infinite; }
    @@keyframes piWheel { 0%{opacity:1; top:7px} 100%{opacity:0; top:20px} }

    /* ============ LOBBY ============ */
    .pi-wrap { max-width: 1000px; margin: 0 auto; padding: 70px clamp(20px,4vw,48px) 90px; }
    .pi-flash { margin-bottom: 22px; padding: 14px 18px; border-radius: 14px; font-size: 13px; font-weight: 600;
        background: rgba(34,197,94,.08); color: #22c55e; border: 1px solid rgba(34,197,94,.2); }
    .pi-feats { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; margin-bottom: 40px; }
    .pi-feat { background: var(--card); border: 1px solid var(--border); border-radius: 20px; padding: 28px 22px;
        text-align: center; position: relative; overflow: hidden;
        opacity: 0; transform: translateY(26px);
        transition: opacity .5s ease, transform .5s cubic-bezier(.16,1,.3,1), border-color .3s, box-shadow .3s; }
    .pi-feat.in { opacity: 1; transform: none; }
    .pi-feat:hover { transform: translateY(-6px); border-color: #22c55e; box-shadow: 0 20px 46px rgba(0,0,0,.14); }
    .pi-feat-ico { width: 54px; height: 54px; border-radius: 16px; margin: 0 auto 14px; display: flex; align-items: center;
        justify-content: center; font-size: 20px; transition: transform .35s; }
    .pi-feat:hover .pi-feat-ico { transform: scale(1.14) rotate(-5deg); }
    .pi-feat-t { font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 6px; }
    .pi-feat-d { font-size: 13px; color: var(--text-muted); line-height: 1.6; }
    .pi-hist-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
    .pi-hist-title { font-size: 18px; font-weight: 900; color: var(--text); display: flex; align-items: center; gap: 9px; }
    .pi-hist-title i { color: #22c55e; }
    .pi-hist-count { font-size: 12px; color: var(--text-muted); font-weight: 700; font-family: var(--font-mono); }
    .pi-list { background: var(--card); border: 1px solid var(--border); border-radius: 20px; overflow: hidden;
        box-shadow: 0 14px 40px rgba(0,0,0,.1); }
    .pi-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 15px 20px;
        border-bottom: 1px solid var(--border); border-left: 3px solid transparent;
        opacity: 0; transform: translateX(-14px);
        transition: opacity .4s ease, transform .4s cubic-bezier(.16,1,.3,1), background .15s, border-color .15s; }
    .pi-row.in { opacity: 1; transform: none; }
    .pi-row:last-child { border-bottom: none; }
    .pi-row:hover { background: color-mix(in srgb, #22c55e 4%, transparent); border-left-color: #22c55e; }
    .pi-row-code { font-size: 15px; font-weight: 800; color: var(--text); font-family: var(--font-mono); letter-spacing: 1px; }
    .pi-row-meta { font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 7px;
        margin-top: 4px; font-family: var(--font-mono); flex-wrap: wrap; }
    .pi-row-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
    .pi-status { padding: 5px 12px; border-radius: 100px; font-size: 10px; font-weight: 800; text-transform: uppercase;
        letter-spacing: .5px; font-family: var(--font-mono); }
    .pi-status--waiting { background: rgba(234,179,8,.12); color: #eab308; animation: piBlink 2s infinite; }
    .pi-status--connected { background: rgba(34,197,94,.12); color: #22c55e; }
    .pi-status--ended { background: var(--bg-secondary); color: var(--text-muted); }
    .pi-enter { padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 800;
        border: 1.5px solid #22c55e; background: transparent; color: #22c55e; cursor: pointer; transition: all .25s;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .pi-enter:hover { background: linear-gradient(135deg,#22c55e,#16a34a); color: #fff; border-color: transparent;
        box-shadow: 0 6px 18px rgba(34,197,94,.4); }
    .pi-pag { margin-top: 16px; display: flex; justify-content: center; }

    @@media(max-width: 1020px) {
        .pi-hero3d-inner { grid-template-columns: 1fr; }
        .pi-stage { height: 520px; }
        .pi-fc-3 { right: 0; } .pi-fc-1 { right: 0; } .pi-fc-2 { left: 0; }
    }
    @@media(max-width: 640px) {
        .pi-feats { grid-template-columns: 1fr; }
        .pi-row { flex-direction: column; align-items: flex-start; }
        .pi-row-right { width: 100%; justify-content: space-between; }
    }
</style>
@endsection

@section('content')
<div class="pi-page">
{{-- ================= HERO 3D ================= --}}
<section class="pi-hero3d" id="piHero">
    <div class="pi-hero3d-bg">
        <div class="pi-aurora"></div>
        <div class="pi-grid3d" data-depth="18"></div>
        <div class="pi-orb pi-orb-1" data-depth="40"></div>
        <div class="pi-orb pi-orb-2" data-depth="-30"></div>
        <div class="pi-orb pi-orb-3" data-depth="60"></div>
    </div>

    <div class="pi-hero3d-inner">
        <div>
            <span class="pi-eyebrow"><i></i>{{ __('live sparring') }}</span>
            <h1 class="pi-title">{!! __('Mock With<br><span class="grad">a Human</span>') !!}</h1>
            <p class="pi-sub">{!! __('Create a room, share the <b>code</b>, and interview each other <b>live</b> — video, shared editor, real pressure.') !!}</p>

            <div class="pi-hero-actions">
                <form action="{{ route('peer.create') }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="pi-btn pi-btn-create"><i class="fas fa-plus"></i>{{ __('peer.create_room') }}</button>
                </form>
                <a href="{{ route('peer.joinForm') }}" class="pi-btn pi-btn-ghost"><i class="fas fa-right-to-bracket"></i>{{ __('peer.join') }}</a>
            </div>

            <div class="pi-stats3d">
                <div class="pi-stat"><div class="pi-stat-val" data-count="{{ $rooms->total() }}">0</div><div class="pi-stat-label">{{ __('peer.total') }}</div></div>
                <div class="pi-stat"><div class="pi-stat-val" data-count="{{ $rooms->getCollection()->where('status', 'waiting')->count() }}">0</div><div class="pi-stat-label">{{ __('peer.waiting_status') }}</div></div>
                <div class="pi-stat"><div class="pi-stat-val" data-count="{{ $rooms->getCollection()->where('status', 'ended')->count() }}">0</div><div class="pi-stat-label">{{ __('interview_status_completed') }}</div></div>
            </div>
        </div>

        <div class="pi-stage">
            <div class="pi-ring pi-ring-1"><span class="pi-ring-dot"></span></div>
            <div class="pi-ring pi-ring-2"><span class="pi-ring-dot" style="background:#38bdf8;box-shadow:0 0 14px #38bdf8"></span></div>
            <div class="pi-cube pi-cube-1" data-depth="70"><i class="fas fa-video"></i></div>
            <div class="pi-cube pi-cube-2" data-depth="-60"><i class="fas fa-code"></i></div>

            <div class="pi-vs3d" id="piVs">
                <div class="pi-vs-top">
                    <span class="pi-rec"><i></i>REC</span>
                    <span class="pi-clock"><i class="fas fa-stopwatch" style="margin-right:6px"></i><span id="piClock">00:00</span></span>
                </div>
                <div class="pi-duel">
                    <div class="pi-cam host">
                        <div class="pi-cam-ava" style="background:linear-gradient(135deg,#22c55e,#15803d)"><i class="fas fa-user"></i></div>
                        <div class="pi-cam-name">{{ __('You') }}</div>
                        <div class="pi-cam-role">host</div>
                        <div class="pi-cam-mics"><span><i class="fas fa-microphone"></i></span><span><i class="fas fa-video"></i></span></div>
                    </div>
                    <div class="pi-vs-badge">VS</div>
                    <div class="pi-cam guest">
                        <div class="pi-cam-ava" style="background:linear-gradient(135deg,#38bdf8,#0369a1)"><i class="fas fa-user-astronaut"></i></div>
                        <div class="pi-cam-name">{{ __('Peer') }}</div>
                        <div class="pi-cam-role">guest</div>
                        <div class="pi-cam-mics"><span><i class="fas fa-microphone"></i></span><span class="off"><i class="fas fa-video-slash"></i></span></div>
                    </div>
                </div>
                <div class="pi-codebar">
                    <span class="c">// shared editor — both type here</span><br>
                    <span class="k">def</span> <span class="f">two_sum</span>(<span class="v">nums</span>, <span class="v">target</span>): <span class="pi-caret"></span>
                </div>
            </div>

            <div class="pi-float-chip pi-fc-1" data-depth="50">
                <div class="pi-fc-ico g"><i class="fas fa-video"></i></div>
                <div class="pi-fc-txt"><b>HD video</b><span>{{ __('face to face') }}</span></div>
            </div>
            <div class="pi-float-chip pi-fc-2" data-depth="-45">
                <div class="pi-fc-ico p"><i class="fas fa-code"></i></div>
                <div class="pi-fc-txt"><b>{{ __('Shared editor') }}</b><span>{{ __('type together') }}</span></div>
            </div>
            <div class="pi-float-chip pi-fc-3" data-depth="35">
                <div class="pi-fc-ico a"><i class="fas fa-shield-halved"></i></div>
                <div class="pi-fc-txt"><b>{{ __('Private') }}</b><span>{{ __('8-symbol code') }}</span></div>
            </div>
        </div>
    </div>

    <div class="pi-scroll-hint"><div class="pi-mouse"></div><span>{{ __('Scroll — lobby') }}</span></div>
</section>

{{-- ================= LOBBY ================= --}}
<div class="pi-wrap" id="piList">
    @if(session('success'))
    <div class="pi-flash"><i class="fas fa-check-circle" style="margin-right:8px"></i>{{ session('success') }}</div>
    @endif

    <div class="pi-feats" id="piFeats">
        <div class="pi-feat" data-i="0">
            <div class="pi-feat-ico" style="background:rgba(34,197,94,.1);color:#22c55e"><i class="fas fa-video"></i></div>
            <div class="pi-feat-t">{{ __('peer.video_connection') }}</div>
            <div class="pi-feat-d">{{ __('peer.video_connection_desc') }}</div>
        </div>
        <div class="pi-feat" data-i="1">
            <div class="pi-feat-ico" style="background:rgba(56,189,248,.1);color:#38bdf8"><i class="fas fa-shield-halved"></i></div>
            <div class="pi-feat-t">{{ __('peer.secure') }}</div>
            <div class="pi-feat-d">{{ __('peer.secure_desc') }}</div>
        </div>
        <div class="pi-feat" data-i="2">
            <div class="pi-feat-ico" style="background:rgba(139,92,246,.1);color:#8b5cf6"><i class="fas fa-code"></i></div>
            <div class="pi-feat-t">{{ __('peer.code_editor') }}</div>
            <div class="pi-feat-d">{{ __('peer.code_editor_desc') }}</div>
        </div>
    </div>

    @if($rooms->count())
    <div class="pi-hist-head">
        <span class="pi-hist-title"><i class="fas fa-clock-rotate-left"></i>{{ __('peer.my_history') }}</span>
        <span class="pi-hist-count">{{ $rooms->total() }} {{ __('peer.total') }}</span>
    </div>
    <div class="pi-list" id="piRows">
        @foreach($rooms as $r)
        <div class="pi-row" data-i="{{ $loop->index }}">
            <div>
                <div class="pi-row-code">{{ $r->room_code }}</div>
                <div class="pi-row-meta">
                    <span><i class="fas fa-user"></i>{{ $r->host_name }}</span>
                    @if($r->guest_name)<span>→</span><span><i class="fas fa-user"></i>{{ $r->guest_name }}</span>@endif
                    <span style="opacity:.35">•</span>
                    <span><i class="fas fa-clock"></i>{{ $r->created_at->diffForHumans() }}</span>
                </div>
            </div>
            <div class="pi-row-right">
                <span class="pi-status pi-status--{{ $r->status }}">
                    @if($r->status === 'waiting'){{ __('peer.waiting_status') }}
                    @elseif($r->status === 'connected'){{ __('peer.connected_status') }}
                    @else{{ __('interview_status_completed') }}
                    @endif
                </span>
                @if($r->status !== 'ended')
                <a href="{{ route('peer.room', $r->room_code) }}" class="pi-enter"><i class="fas fa-right-to-bracket"></i>{{ __('peer.join') }}</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="pi-pag">{{ $rooms->links() }}</div>
    @endif
</div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    /* --- Hero mouse 3D parallax --- */
    var hero = document.getElementById('piHero');
    var vs = document.getElementById('piVs');
    var layers = document.querySelectorAll('#piHero [data-depth]');
    if (hero) {
        var rx = 0, ry = 0, tx = 0, ty = 0, heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('pi-paused', !heroVisible);
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
                if (vs) vs.style.transform = 'rotateX(' + (6 + rx * -1).toFixed(2) + 'deg) rotateY(' + (-10 + ry).toFixed(2) + 'deg)';
            }
            requestAnimationFrame(tilt);
        })();
    }

    /* --- Animated counters --- */
    document.querySelectorAll('.pi-stat-val[data-count]').forEach(function(el) {
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

    /* --- Demo session clock --- */
    var clock = document.getElementById('piClock'), secs = 0;
    if (clock) setInterval(function() {
        secs++;
        var m = String(Math.floor(secs / 60)).padStart(2, '0');
        var s = String(secs % 60).padStart(2, '0');
        clock.textContent = m + ':' + s;
    }, 1000);

    /* --- Reveal feats + rows --- */
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
    revealAll('#piFeats .pi-feat');
    revealAll('#piRows .pi-row');
})();
</script>
@endsection
