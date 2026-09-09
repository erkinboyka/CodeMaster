@extends('layouts.app')

@section('title', __('Community') . ' - CodeMaster')

@section('head')
<style>
    /* ============ COMMUNITY: people constellation, отличается от остальных ============ */
    .cm-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .cm-hero { position: relative; overflow: hidden; text-align: center;
        padding: 120px clamp(20px,4vw,56px) 70px; isolation: isolate; }
    .cm-hero-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .cm-aurora { position: absolute; inset: -20%;
        background:
            radial-gradient(ellipse 50% 38% at 50% 18%, var(--accent-glow-strong) 0%, transparent 60%),
            radial-gradient(ellipse 40% 40% at 12% 70%, rgba(139,92,246,.12) 0%, transparent 60%),
            radial-gradient(ellipse 40% 40% at 88% 70%, rgba(56,189,248,.10) 0%, transparent 60%);
        animation: cmAurora 20s ease-in-out infinite alternate; }
    @@keyframes cmAurora {
        0% { transform: translate(0,0) scale(1); }
        50% { transform: translate(0,-2%) scale(1.05); }
        100% { transform: translate(0,2%) scale(1.02); } }
    .cm-orb { position: absolute; border-radius: 50%; filter: blur(90px); }
    .cm-orb-1 { width: 460px; height: 460px; background: var(--accent); opacity: .10; top: -14%; left: 50%; transform: translateX(-50%); animation: cmOrb1 15s ease-in-out infinite; }
    .cm-orb-2 { width: 340px; height: 340px; background: #a855f7; opacity: .08; bottom: -10%; left: 4%; animation: cmOrb2 18s ease-in-out infinite; }
    .cm-orb-3 { width: 340px; height: 340px; background: #38bdf8; opacity: .08; bottom: -10%; right: 4%; animation: cmOrb3 17s ease-in-out infinite; }
    @@keyframes cmOrb1 { 0%,100%{transform:translateX(-50%) scale(1)} 50%{transform:translateX(-46%) scale(1.12)} }
    @@keyframes cmOrb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(50px,-40px) scale(1.1)} }
    @@keyframes cmOrb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-50px,-40px) scale(1.1)} }
    @@keyframes cmBlink { 0%,100%{opacity:1} 50%{opacity:.3} }

    .cm-eyebrow { display: inline-flex; align-items: center; gap: 9px; padding: 8px 18px; border-radius: 100px;
        background: rgba(34,197,94,.08); border: 1px solid rgba(34,197,94,.25);
        font-size: 11px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase; color: #4ade80;
        margin-bottom: 20px; position: relative; z-index: 2; }
    .cm-eyebrow i { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: cmBlink 1.6s infinite;
        box-shadow: 0 0 10px #22c55e; }
    .cm-title { font-size: clamp(44px,7vw,92px); font-weight: 900; line-height: .95; letter-spacing: -3px;
        margin: 0 0 18px; color: var(--text); position: relative; z-index: 2; }
    .cm-title .grad { background: linear-gradient(120deg, #a855f7, var(--accent) 50%, #38bdf8 85%); background-size: 220% 220%;
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: cmGradShift 7s ease-in-out infinite; display: inline-block; }
    @@keyframes cmGradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    .cm-sub { font-size: clamp(15px,1.7vw,18px); color: var(--text-secondary); line-height: 1.7; max-width: 560px;
        margin: 0 auto 26px; position: relative; z-index: 2; }
    .cm-sub b { color: var(--text); }
    .cm-hero-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-bottom: 30px;
        position: relative; z-index: 2; }
    .cm-btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 28px; border-radius: 16px;
        font-weight: 800; font-size: 14px; text-decoration: none; cursor: pointer;
        transition: all .3s cubic-bezier(.16,1,.3,1); border: 1px solid transparent; }
    .cm-btn-talk { background: linear-gradient(135deg,#a855f7,var(--accent)); color: #fff;
        box-shadow: 0 10px 32px var(--accent-glow-strong); }
    .cm-btn-talk:hover { transform: translateY(-3px) scale(1.02); }
    .cm-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .cm-btn-ghost:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-3px); }
    .cm-stats { display: flex; justify-content: center; gap: clamp(24px,5vw,64px); flex-wrap: wrap;
        position: relative; z-index: 2; margin-bottom: 10px; }
    .cm-stat-val { font-size: clamp(28px,3.4vw,42px); font-weight: 900; line-height: 1; font-variant-numeric: tabular-nums;
        background: linear-gradient(135deg,var(--text),var(--accent)); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; }
    .cm-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.6px;
        margin-top: 6px; font-weight: 600; }

    /* --- constellation: орбиты людей + чат-пузыри --- */
    .cm-stage { position: relative; z-index: 1; height: 460px; max-width: 900px; margin: 10px auto 0;
        perspective: 1200px; }
    .cm-hub { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); z-index: 3;
        width: 110px; height: 110px; border-radius: 50%;
        background: linear-gradient(135deg,#a855f7,var(--accent),#38bdf8);
        display: flex; align-items: center; justify-content: center; font-size: 40px; color: #fff;
        box-shadow: 0 0 60px var(--accent-glow-strong), 0 20px 50px rgba(0,0,0,.3); }
    .cm-hub-ring { position: absolute; top: 50%; left: 50%; border-radius: 50%; border: 1px solid var(--border-hover);
        pointer-events: none; }
    .cm-hub-ring.r1 { width: 160px; height: 160px; transform: translate(-50%,-50%); animation: cmRingPulse 3s ease-out infinite; }
    .cm-hub-ring.r2 { width: 160px; height: 160px; transform: translate(-50%,-50%); animation: cmRingPulse 3s 1.5s ease-out infinite; }
    @@keyframes cmRingPulse { 0% { transform: translate(-50%,-50%) scale(.7); opacity: .8; } 100% { transform: translate(-50%,-50%) scale(1.5); opacity: 0; } }
    .cm-orbit { position: absolute; top: 50%; left: 50%; border-radius: 50%; border: 1px dashed var(--border-hover);
        pointer-events: none; }
    .cm-orbit.o1 { width: 340px; height: 200px; transform: translate(-50%,-50%) rotate(-6deg); animation: cmOrbitSpin 26s linear infinite; }
    .cm-orbit.o2 { width: 560px; height: 300px; transform: translate(-50%,-50%) rotate(5deg); animation: cmOrbitSpinRev 38s linear infinite; }
    .cm-orbit.o3 { width: 760px; height: 380px; transform: translate(-50%,-50%) rotate(-3deg); animation: cmOrbitSpin 52s linear infinite; opacity: .6; }
    @@keyframes cmOrbitSpin { from { transform: translate(-50%,-50%) rotate(var(--orb-rot,0deg)) rotate(0deg); } to { transform: translate(-50%,-50%) rotate(var(--orb-rot,0deg)) rotate(360deg); } }
    @@keyframes cmOrbitSpinRev { from { transform: translate(-50%,-50%) rotate(var(--orb-rot,0deg)) rotate(0deg); } to { transform: translate(-50%,-50%) rotate(var(--orb-rot,0deg)) rotate(-360deg); } }
    .cm-orbit.o1 { --orb-rot: -6deg; } .cm-orbit.o2 { --orb-rot: 5deg; } .cm-orbit.o3 { --orb-rot: -3deg; }
    .cm-person { position: absolute; top: -19px; left: 50%; margin-left: -19px; width: 38px; height: 38px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-size: 15px; color: #fff;
        border: 2px solid var(--bg); box-shadow: 0 6px 18px rgba(0,0,0,.3); }
    .cm-orbit.o1 .cm-person { animation: cmOrbitSpinRev 26s linear infinite; }
    .cm-orbit.o2 .cm-person { animation: cmOrbitSpin 38s linear infinite; }
    .cm-orbit.o3 .cm-person { animation: cmOrbitSpinRev 52s linear infinite; }
    .cm-bubble { position: absolute; z-index: 2; max-width: 210px; padding: 10px 15px; border-radius: 16px;
        background: color-mix(in srgb, var(--card) 92%, transparent); backdrop-filter: blur(14px);
        border: 1px solid var(--border); box-shadow: 0 14px 36px rgba(0,0,0,.2);
        font-size: 12px; font-weight: 600; color: var(--text); text-align: left;
        animation: cmBubFloat 5s ease-in-out infinite; }
    .cm-bubble small { display: block; font-size: 10px; font-weight: 600; color: var(--text-muted); margin-top: 3px; }
    @@keyframes cmBubFloat { 0%,100% { margin-top: 0; rotate: -1deg; } 50% { margin-top: -12px; rotate: 1deg; } }
    .cm-bubble.b1 { top: 6%; left: 2%; animation-delay: 0s; }
    .cm-bubble.b2 { top: 12%; right: 0; animation-delay: 1.4s; }
    .cm-bubble.b3 { bottom: 8%; left: 6%; animation-delay: 2.2s; }
    .cm-bubble.b4 { bottom: 12%; right: 4%; animation-delay: .8s; }
    .cm-typing { display: inline-flex; gap: 4px; padding: 12px 16px; }
    .cm-typing span { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); animation: cmTp 1.2s ease-in-out infinite; }
    .cm-typing span:nth-child(2) { animation-delay: .15s; } .cm-typing span:nth-child(3) { animation-delay: .3s; }
    @@keyframes cmTp { 0%,60%,100% { transform: translateY(0); opacity: .4; } 30% { transform: translateY(-5px); opacity: 1; } }
    .cm-paused .cm-aurora, .cm-paused .cm-orb, .cm-paused .cm-orbit, .cm-paused .cm-person,
    .cm-paused .cm-bubble, .cm-paused .cm-hub-ring { animation-play-state: paused !important; }

    /* --- бегущая строка тегов --- */
    .cm-marquee { border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
        background: var(--bg-secondary); overflow: hidden; position: relative; padding: 16px 0; }
    .cm-marquee-track { display: flex; gap: 12px; width: max-content; animation: cmMarquee 36s linear infinite; }
    .cm-marquee:hover .cm-marquee-track { animation-play-state: paused; }
    @@keyframes cmMarquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
    .cm-mq-tag { display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 100px;
        background: var(--card); border: 1px solid var(--border); color: var(--text-secondary);
        font-size: 13px; font-weight: 700; text-decoration: none; white-space: nowrap; transition: all .2s; }
    .cm-mq-tag:hover { border-color: var(--accent); color: var(--accent); }
    .cm-mq-tag i { font-size: 15px; }

    /* --- лента как тред --- */
    .cm-wrap { max-width: 1280px; margin: 0 auto; padding: 60px clamp(20px,4vw,48px) 90px; }
    .cm-cols { display: grid; grid-template-columns: minmax(0,1fr) 320px; gap: 32px; align-items: start; }
    .cm-feed-head { display: flex; align-items: center; justify-content: space-between; gap: 14px;
        margin-bottom: 22px; flex-wrap: wrap; }
    .cm-feed-title { font-size: 22px; font-weight: 900; letter-spacing: -.5px; color: var(--text); }
    .cm-feed-title small { font-size: 13px; font-weight: 600; color: var(--text-muted); margin-left: 8px; }
    .cm-sort { display: flex; gap: 4px; padding: 4px; border-radius: 12px; background: var(--card); border: 1px solid var(--border); }
    .cm-sort a { padding: 8px 18px; border-radius: 9px; font-size: 13px; font-weight: 700; color: var(--text-muted);
        text-decoration: none; transition: all .25s; }
    .cm-sort a:hover { color: var(--text); }
    .cm-sort a.active { background: linear-gradient(135deg,#a855f7,var(--accent)); color: #fff;
        box-shadow: 0 4px 14px var(--accent-glow-strong); }
    .cm-feed { position: relative; }
    .cm-feed::before { content: ''; position: absolute; left: 25px; top: 26px; bottom: 26px; width: 2px; border-radius: 2px;
        background: repeating-linear-gradient(to bottom, var(--border-hover) 0 7px, transparent 7px 14px); }
    .cm-post { position: relative; display: flex; gap: 16px; border-radius: 20px; background: var(--card);
        border: 1px solid var(--border); padding: 22px;
        opacity: 0; transform: translateY(26px);
        transition: opacity .5s ease, transform .5s cubic-bezier(.16,1,.3,1), border-color .25s, box-shadow .25s; }
    .cm-post.in { opacity: 1; transform: none; }
    .cm-post:hover { border-color: var(--accent); box-shadow: 0 14px 40px rgba(0,0,0,.12); }
    .cm-post-ava { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0; position: relative; z-index: 1;
        border: 2px solid var(--bg); outline: 2px solid var(--border); }
    .cm-post-main { flex: 1; min-width: 0; }
    .cm-post-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
    .cm-post-author { font-size: 14px; font-weight: 800; color: var(--text); text-decoration: none; }
    .cm-post-author:hover { color: var(--accent); }
    .cm-post-time { font-size: 12px; color: var(--text-muted); }
    .cm-post-title { font-size: 18px; font-weight: 800; color: var(--text); margin: 0 0 8px; line-height: 1.35; }
    .cm-post-excerpt { font-size: 14px; color: var(--text-muted); line-height: 1.65; margin: 0 0 14px; }
    .cm-post-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 6px; }
    .cm-post-actions { display: flex; align-items: center; gap: 22px; padding-top: 14px; border-top: 1px solid var(--border); margin-top: 12px; }
    .cm-action-btn { display: flex; align-items: center; gap: 7px; font-size: 13px; font-weight: 700;
        color: var(--text-muted); background: none; border: none; cursor: pointer; transition: all .2s; padding: 4px 6px; }
    .cm-action-btn:hover { color: var(--accent); transform: translateY(-1px); }
    .cm-action-btn.liked { color: #ef4444; }
    .cm-action-btn.liked i { animation: cmPop .35s cubic-bezier(.16,1,.3,1); }
    @@keyframes cmPop { 0% { transform: scale(.4); } 60% { transform: scale(1.35); } 100% { transform: scale(1); } }
    .cm-tag { display: inline-flex; align-items: center; gap: 6px; padding: 7px 13px; border-radius: 10px;
        font-size: 11px; font-weight: 700; background: var(--bg-secondary); color: var(--text-muted);
        border: 1px solid var(--border); transition: all .2s; cursor: pointer; text-decoration: none; }
    .cm-tag i { font-size: 14px; }
    .cm-tag:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-glow); }
    .cm-empty { text-align: center; padding: 70px 24px; border-radius: 20px; background: var(--card);
        border: 1px dashed var(--border); }
    .cm-empty i { font-size: 48px; color: var(--text-muted); margin-bottom: 16px; opacity: .5; }
    .cm-empty p { color: var(--text-muted); }
    .cm-pagination { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 30px; flex-wrap: wrap; }
    .cm-page-btn { min-width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center;
        border-radius: 12px; font-size: 14px; font-weight: 700; text-decoration: none;
        transition: all .3s cubic-bezier(.16,1,.3,1); border: 1px solid var(--border);
        color: var(--text-muted); background: var(--card); font-family: var(--font-mono); }
    .cm-page-btn:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-2px); }
    .cm-page-btn.active { background: linear-gradient(135deg,#a855f7,var(--accent)); color: #fff; border-color: transparent;
        box-shadow: 0 4px 20px var(--accent-glow-strong); }
    .cm-page-btn.disabled { opacity: .4; pointer-events: none; }

    .cm-side { position: sticky; top: 96px; align-self: start; display: flex; flex-direction: column; gap: 18px; }
    .cm-sidebar-card { border-radius: 20px; background: var(--card); border: 1px solid var(--border); padding: 24px;
        box-shadow: 0 12px 34px rgba(0,0,0,.08); }
    .cm-sidebar-title { font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 14px; }
    .cm-create-btn { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;
        padding: 14px; border-radius: 14px; font-size: 14px; font-weight: 800; border: none; cursor: pointer;
        transition: all .3s; background: rgba(255,255,255,.2); backdrop-filter: blur(8px); color: #fff; }
    .cm-create-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,.2); }

    .cm-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.6); backdrop-filter: blur(6px);
        display: flex; align-items: center; justify-content: center; z-index: 100; padding: 16px;
        opacity: 0; pointer-events: none; transition: opacity .3s; }
    .cm-modal-overlay.open { opacity: 1; pointer-events: all; }
    .cm-modal { background: var(--card); border: 1px solid var(--border); border-radius: 22px; width: 100%;
        max-width: 560px; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 30px 80px rgba(0,0,0,.35);
        transform: scale(.95) translateY(10px); transition: transform .3s cubic-bezier(.16,1,.3,1); }
    .cm-modal-overlay.open .cm-modal { transform: scale(1) translateY(0); }
    .cm-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 22px 24px;
        border-bottom: 1px solid var(--border); flex-shrink: 0; background: var(--card); z-index: 2; }
    .cm-modal-title { font-size: 18px; font-weight: 800; color: var(--text); }
    .cm-modal-close { width: 36px; height: 36px; border-radius: 10px; border: none; background: var(--bg-secondary);
        color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .2s; }
    .cm-modal-close:hover { background: var(--accent-glow); color: var(--accent); }
    .cm-modal-body { padding: 24px; flex: 1 1 auto; min-height: 0; overflow-y: auto; overscroll-behavior: contain;
        scrollbar-width: thin; scrollbar-color: var(--border) transparent; }
    .cm-modal-body::-webkit-scrollbar { width: 10px; }
    .cm-modal-body::-webkit-scrollbar-track { background: transparent; margin: 8px 0; }
    .cm-modal-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px;
        border: 3px solid transparent; background-clip: padding-box; }
    .cm-modal-body::-webkit-scrollbar-thumb:hover { background: var(--text-muted); border: 3px solid transparent; background-clip: padding-box; }
    .cm-modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 16px 24px; border-top: 1px solid var(--border); flex-shrink: 0; background: var(--card); }
    .cm-form-input { width: 100%; box-sizing: border-box; padding: 12px 16px; border-radius: 12px;
        border: 1px solid var(--border); background: var(--bg-secondary); color: var(--text);
        font-size: 14px; transition: border-color .2s, box-shadow .2s; outline: none; margin-bottom: 12px; }
    .cm-form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
    .cm-btn-cancel { padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600;
        border: 1px solid var(--border); background: transparent; color: var(--text-muted); cursor: pointer; transition: all .2s; }
    .cm-btn-cancel:hover { border-color: var(--accent); color: var(--accent); }
    .cm-btn-submit { padding: 10px 24px; border-radius: 10px; font-size: 13px; font-weight: 800; border: none;
        background: linear-gradient(135deg,#a855f7,var(--accent)); color: #fff; cursor: pointer; transition: all .25s; }
    .cm-btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px var(--accent-glow-strong); }
    .cm-comment { display: flex; gap: 10px; padding: 12px 0; border-bottom: 1px solid var(--border); }
    .cm-comment:last-child { border-bottom: none; }
    .cm-comment-avatar { width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; object-fit: cover; }
    .cm-comment-body { flex: 1; background: var(--bg-secondary); border-radius: 12px; padding: 10px 14px; }
    .cm-comment-name { font-size: 13px; font-weight: 700; color: var(--text); }
    .cm-comment-time { font-size: 11px; color: var(--text-muted); margin-left: 8px; }
    .cm-comment-text { font-size: 13px; color: var(--text-secondary); margin-top: 4px; line-height: 1.55; }
    .cm-detail-title { font-size: 20px; font-weight: 800; color: var(--text); margin-bottom: 12px; }
    .cm-detail-content { font-size: 14px; color: var(--text-secondary); line-height: 1.7; white-space: pre-wrap; margin-bottom: 20px; }

    /* --- светлая тема: контраст заголовка и мелочей --- */
    [data-theme*="-light"] .cm-title .grad {
        background: linear-gradient(120deg, #7e22ce, var(--accent) 55%, #0369a1);
        -webkit-background-clip: text; background-clip: text;
        filter: none;
    }
    [data-theme*="-light"] .cm-bubble { background: rgba(255,255,255,.94); border-color: #e2e8f0;
        box-shadow: 0 14px 30px rgba(0,0,0,.1); }
    [data-theme*="-light"] .cm-bubble small,
    [data-theme*="-light"] .cm-post-time,
    [data-theme*="-light"] .cm-stat-label,
    [data-theme*="-light"] .cm-feed-title small { color: #64748b; }
    [data-theme*="-light"] .cm-orbit { border-color: #c3d0e0; }
    [data-theme*="-light"] .cm-person { border-color: #fff; }
    [data-theme*="-light"] .cm-hub { box-shadow: 0 0 44px rgba(168,85,247,.4), 0 20px 50px rgba(0,0,0,.18); }
    [data-theme*="-light"] .cm-orb-2, [data-theme*="-light"] .cm-orb-3 { opacity: .16; }
    [data-theme*="-light"] .cm-marquee { background: #f1f5f9; }

    @@media (max-width: 1020px) {
        .cm-cols { grid-template-columns: 1fr; }
        .cm-stage { height: 400px; }
        .cm-orbit.o3 { display: none; }
        .cm-bubble { max-width: 160px; font-size: 11px; }
        .cm-side { position: static; }
        .cm-feed::before { left: 21px; }
    }
</style>
@endsection

@section('content')
<div class="cm-page" x-data="communityApp()" x-init="init()">
{{-- ================= HERO: constellation ================= --}}
<section class="cm-hero" id="cmHero">
    <div class="cm-hero-bg">
        <div class="cm-aurora"></div>
        <div class="cm-orb cm-orb-1" data-depth="30"></div>
        <div class="cm-orb cm-orb-2" data-depth="-30"></div>
        <div class="cm-orb cm-orb-3" data-depth="45"></div>
    </div>

    <span class="cm-eyebrow"><i></i>{{ __('online now') }} • {{ \App\Models\User::count() }} {{ __('members') }}</span>
    <h1 class="cm-title">{!! __('Talk <span class="grad">Code</span> Together') !!}</h1>
    <p class="cm-sub">{!! __('Ask questions, share <b>pet-projects</b> and review each other\'s code. The friendliest corner of <b>CodeMaster</b>.') !!}</p>

    <div class="cm-hero-actions">
        <button @click="showEditor = true" class="cm-btn cm-btn-talk"><i class="fas fa-pen-nib"></i>{{ __('Start a Discussion') }}</button>
        <a href="#cmFeed" class="cm-btn cm-btn-ghost" id="cmToFeed"><i class="fas fa-comments"></i>{{ __('Browse threads') }}</a>
    </div>

    <div class="cm-stats">
        <div class="cm-stat"><div class="cm-stat-val" data-count="{{ $posts->total() }}">0</div><div class="cm-stat-label">{{ __('Threads') }}</div></div>
        <div class="cm-stat"><div class="cm-stat-val" data-count="{{ \App\Models\User::count() }}">0</div><div class="cm-stat-label">{{ __('Members') }}</div></div>
        <div class="cm-stat"><div class="cm-stat-val" data-count="{{ \App\Models\CommunityComment::count() }}">0</div><div class="cm-stat-label">{{ __('Replies') }}</div></div>
    </div>

    <div class="cm-stage" id="cmStage">
        <div class="cm-hub-ring r1"></div>
        <div class="cm-hub-ring r2"></div>
        <div class="cm-orbit o1">
            <span class="cm-person" style="background:linear-gradient(135deg,#a855f7,#f43f5e)"><i class="fas fa-user-astronaut"></i></span>
        </div>
        <div class="cm-orbit o2">
            <span class="cm-person" style="background:linear-gradient(135deg,#38bdf8,#0369a1)"><i class="fas fa-user-ninja"></i></span>
            <span class="cm-person" style="background:linear-gradient(135deg,#22c55e,#15803d);top:auto;bottom:-19px"><i class="fas fa-robot"></i></span>
        </div>
        <div class="cm-orbit o3">
            <span class="cm-person" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9)"><i class="fas fa-ghost"></i></span>
            <span class="cm-person" style="background:linear-gradient(135deg,#f59e0b,#b45309);left:auto;right:8%;top:auto;bottom:12%"><i class="fas fa-user-secret"></i></span>
            <span class="cm-person" style="background:linear-gradient(135deg,#f43f5e,#881337);left:8%;top:auto;bottom:12%"><i class="fas fa-user"></i></span>
        </div>
        <div class="cm-hub"><i class="fas fa-users"></i></div>

        <div class="cm-bubble b1" data-depth="55">"{{ __('Anyone solved dp/lis yet?') }}"<small>@algo_king • 2m</small></div>
        <div class="cm-bubble b2" data-depth="-45">"{{ __('Ship it! +1') }}"<small>@segfault • 5m</small></div>
        <div class="cm-bubble b3" data-depth="40">"{{ __('Welcome to the club') }}"<small>@mentor_anna • 9m</small></div>
        <div class="cm-bubble b4 cm-typing" data-depth="-60"><span></span><span></span><span></span></div>
    </div>
</section>

{{-- ================= бегущая строка тегов ================= --}}
<div class="cm-marquee">
    <div class="cm-marquee-track">
        @forelse($popularTags as $tag)
        <a href="{{ route('community.index', ['tag' => $tag->slug, 'sort' => $sort]) }}" class="cm-mq-tag">
            <i class="fas fa-hashtag" style="color:var(--accent)"></i>{{ $tag->name }}
        </a>
        @empty
        <span class="cm-mq-tag"><i class="fas fa-hashtag"></i>javascript</span>
        <span class="cm-mq-tag"><i class="fas fa-hashtag"></i>python</span>
        <span class="cm-mq-tag"><i class="fas fa-hashtag"></i>laravel</span>
        <span class="cm-mq-tag"><i class="fas fa-hashtag"></i>react</span>
        <span class="cm-mq-tag"><i class="fas fa-hashtag"></i>career</span>
        @endforelse
        @foreach($popularTags as $tag)
        <a href="{{ route('community.index', ['tag' => $tag->slug, 'sort' => $sort]) }}" class="cm-mq-tag" aria-hidden="true" tabindex="-1">
            <i class="fas fa-hashtag" style="color:var(--accent)"></i>{{ $tag->name }}
        </a>
        @endforeach
    </div>
</div>

{{-- ================= FEED ================= --}}
<div class="cm-wrap" id="cmFeed">
    <div class="cm-cols">
            <div class="min-w-0">
                <div class="cm-feed-head">
                    <div class="cm-feed-title">{{ __('Fresh threads') }}<small>{{ $posts->total() }}</small></div>
                    <div class="cm-sort">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" class="{{ $sort !== 'likes' ? 'active' : '' }}">{{ __('Newest') }}</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'likes']) }}" class="{{ $sort === 'likes' ? 'active' : '' }}">{{ __('Top liked') }}</a>
                    </div>
                </div>

                <div class="cm-feed">
                    @forelse($posts as $post)
                    <div class="cm-post" style="margin-bottom:16px" data-i="{{ $loop->index }}" x-data="{ liked: {{ in_array($post->id, $likedIds ?? []) ? 'true' : 'false' }}, likes: {{ $post->likes_count }}, commentsCount: {{ $post->comments_count }} }" id="post-{{ $post->id }}">
                        <img src="{{ $post->user?->avatar_url ?? 'https://ui-avatars.com/api/?name=U&background=6366f1&color=fff' }}" class="cm-post-ava" alt="" loading="lazy">
                        <div class="cm-post-main">
                            <div class="cm-post-header">
                                <a href="{{ route('profile.show', $post->user_id) }}" class="cm-post-author">{{ $post->user->name ?? __('Unknown') }}</a>
                                <span class="cm-post-time">• {{ $post->created_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="cm-post-title">{{ $post->title }}</h3>
                            <p class="cm-post-excerpt">{{ Str::limit(strip_tags($post->content), 220) }}</p>
                            @if($post->tags->count())
                            <div class="cm-post-tags">
                                @foreach($post->tags as $tag)
                                @php
                                    $tagIcons = [
                                        'javascript' => 'fa-brands fa-js', 'react' => 'fa-brands fa-react',
                                        'laravel' => 'fa-brands fa-laravel', 'python' => 'fa-brands fa-python',
                                        'php' => 'fa-brands fa-php', 'nodejs' => 'fa-brands fa-node-js',
                                        'typescript' => 'fa-brands fa-js', 'docker' => 'fa-brands fa-docker',
                                        'kubernetes' => 'fa-solid fa-dharmachakra', 'devops' => 'fa-solid fa-gears',
                                        'frontend' => 'fa-solid fa-code', 'backend' => 'fa-solid fa-server',
                                        'css' => 'fa-brands fa-css3-alt', 'html' => 'fa-brands fa-html5',
                                        'git' => 'fa-brands fa-git-alt', 'mysql' => 'fa-solid fa-database',
                                        'postgresql' => 'fa-solid fa-database', 'java' => 'fa-brands fa-java',
                                        'cpp' => 'fa-solid fa-c', 'csharp' => 'fa-solid fa-c',
                                        'ui-ux' => 'fa-solid fa-pen-nib', 'algorithms' => 'fa-solid fa-brain',
                                        'interview' => 'fa-solid fa-microphone', 'career' => 'fa-solid fa-briefcase',
                                        'beginners' => 'fa-solid fa-graduation-cap', 'projects' => 'fa-solid fa-folder-open',
                                        'code-review' => 'fa-solid fa-code-branch', 'testing' => 'fa-solid fa-vial',
                                        'security' => 'fa-solid fa-shield-halved', 'ai-ml' => 'fa-solid fa-robot',
                                    ];
                                    $icon = $tagIcons[strtolower($tag->slug)] ?? 'fa-solid fa-code';
                                @endphp
                                <a href="{{ route('community.index', ['tag' => $tag->slug, 'sort' => $sort]) }}" class="cm-tag">
                                    <i class="{{ $icon }}"></i>{{ $tag->name }}
                                </a>
                                @endforeach
                            </div>
                            @endif
                            <div class="cm-post-actions">
                                <button @click="toggleLike({{ $post->id }}, $el)" class="cm-action-btn" :class="liked ? 'liked' : ''">
                                    <i :class="liked ? 'fas fa-heart' : 'far fa-heart'"></i>
                                    <span x-text="likes">{{ $post->likes_count }}</span>
                                </button>
                                <button @click="openPost({{ $post->id }})" class="cm-action-btn">
                                    <i class="far fa-comment"></i>
                                    <span x-text="commentsCount">{{ $post->comments_count }}</span>
                                </button>
                                <span class="cm-action-btn" style="cursor:default">
                                    <i class="far fa-eye"></i>
                                    <span>{{ $post->views_count }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="cm-empty">
                        <i class="fas fa-comments"></i>
                        <p>{{ __('No posts yet. Be the first to share!') }}</p>
                    </div>
                    @endforelse
                </div>

                @if($posts->hasPages())
                <div class="cm-pagination">
                    @if($posts->onFirstPage())
                    <span class="cm-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                    @else
                    <a href="{{ $posts->previousPageUrl() }}" class="cm-page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif
                    @foreach($posts->getUrlRange(max(1, $posts->currentPage() - 2), min($posts->lastPage(), $posts->currentPage() + 2)) as $page => $url)
                    @if($page == $posts->currentPage())
                    <span class="cm-page-btn active">{{ $page }}</span>
                    @else
                    <a href="{{ $url }}" class="cm-page-btn">{{ $page }}</a>
                    @endif
                    @endforeach
                    @if($posts->currentPage() + 2 < $posts->lastPage())
                    <span style="color:var(--text-muted)">…</span>
                    <a href="{{ $posts->url($posts->lastPage()) }}" class="cm-page-btn">{{ $posts->lastPage() }}</a>
                    @endif
                    @if($posts->hasMorePages())
                    <a href="{{ $posts->nextPageUrl() }}" class="cm-page-btn"><i class="fas fa-chevron-right"></i></a>
                    @else
                    <span class="cm-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
                @endif
            </div>

            <div class="cm-side">
                <div class="cm-sidebar-card" style="background:linear-gradient(135deg,#a855f7 0%,var(--accent) 100%);border:none;color:white">
                    <div class="cm-sidebar-title" style="color:white">{{ __('Start a Discussion') }}</div>
                    <p style="font-size:13px;color:rgba(255,255,255,0.85);margin:0 0 16px;line-height:1.6">{{ __('Share your thoughts, ask questions, or help others.') }}</p>
                    <button @click="showEditor = true" class="cm-create-btn">
                        <i class="fas fa-plus"></i> {{ __('Create Post') }}
                    </button>
                </div>

                <div class="cm-sidebar-card">
                    <div class="cm-sidebar-title">{{ __('Tags') }}</div>
                    @if($activeTag)
                    <div style="margin-bottom:12px">
                        <a href="{{ route('community.index', ['sort' => $sort]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;background:linear-gradient(135deg,#a855f7,var(--accent));color:white;text-decoration:none">
                            {{ $activeTag->name }}
                            <i class="fas fa-times" style="font-size:10px"></i>
                        </a>
                    </div>
                    @endif
                    <div style="display:flex;flex-wrap:wrap;gap:8px">
                        @forelse($popularTags as $tag)
                        @php
                            $tagIcons2 = [
                                'javascript' => 'fa-brands fa-js', 'react' => 'fa-brands fa-react',
                                'laravel' => 'fa-brands fa-laravel', 'python' => 'fa-brands fa-python',
                                'php' => 'fa-brands fa-php', 'nodejs' => 'fa-brands fa-node-js',
                                'typescript' => 'fa-brands fa-js', 'docker' => 'fa-brands fa-docker',
                                'kubernetes' => 'fa-solid fa-dharmachakra', 'devops' => 'fa-solid fa-gears',
                                'frontend' => 'fa-solid fa-code', 'backend' => 'fa-solid fa-server',
                                'css' => 'fa-brands fa-css3-alt', 'html' => 'fa-brands fa-html5',
                                'git' => 'fa-brands fa-git-alt', 'mysql' => 'fa-solid fa-database',
                                'postgresql' => 'fa-solid fa-database', 'java' => 'fa-brands fa-java',
                                'cpp' => 'fa-solid fa-c', 'csharp' => 'fa-solid fa-c',
                                'ui-ux' => 'fa-solid fa-pen-nib', 'algorithms' => 'fa-solid fa-brain',
                                'interview' => 'fa-solid fa-microphone', 'career' => 'fa-solid fa-briefcase',
                                'beginners' => 'fa-solid fa-graduation-cap', 'projects' => 'fa-solid fa-folder-open',
                                'code-review' => 'fa-solid fa-code-branch', 'testing' => 'fa-solid fa-vial',
                                'security' => 'fa-solid fa-shield-halved', 'ai-ml' => 'fa-solid fa-robot',
                            ];
                            $icon2 = $tagIcons2[strtolower($tag->slug)] ?? 'fa-solid fa-code';
                        @endphp
                        <a href="{{ route('community.index', ['tag' => $tag->slug, 'sort' => $sort]) }}" class="cm-tag">
                            <i class="{{ $icon2 }}"></i>{{ $tag->name }}
                        </a>
                        @empty
                        <span style="font-size:13px;color:var(--text-muted)">{{ __('No tags yet') }}</span>
                        @endforelse
                    </div>
                </div>

                @if(isset($latestNews) && $latestNews->count())
                <div class="cm-sidebar-card">
                    <div class="cm-sidebar-title"><i class="fas fa-newspaper" style="margin-right:6px;color:var(--accent)"></i>{{ __('News') }}</div>
                    <div style="display:flex;flex-direction:column;gap:12px">
                        @foreach($latestNews as $news)
                        <div style="display:flex;gap:10px;align-items:flex-start;padding:8px;border-radius:10px">
                            @if($news->image)
                            <img src="{{ $news->image }}" style="width:48px;height:48px;border-radius:12px;object-fit:cover;flex-shrink:0">
                            @else
                            <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#a855f7,var(--accent));display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fas fa-newspaper" style="color:white;font-size:14px"></i>
                            </div>
                            @endif
                            <div style="min-width:0;flex:1">
                                <div style="font-size:12px;font-weight:700;color:var(--text);line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $news->title }}</div>
                                <div style="font-size:10px;color:var(--text-muted);margin-top:2px">{{ $news->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

    <div x-show="showEditor" x-transition class="cm-modal-overlay" :class="showEditor ? 'open' : ''" style="display:none" @click.self="showEditor = false">
        <div class="cm-modal" @click.stop>
            <div class="cm-modal-head">
                <div class="cm-modal-title" x-text="editingPost ? '{{ __("Edit Post") }}' : '{{ __("New Post") }}'"></div>
                <button class="cm-modal-close" @click="showEditor = false; editingPost = null; editorTitle = ''; editorContent = ''; editorTags = []; tagInputValue = ''">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="cm-modal-body">
                <input type="text" x-model="editorTitle" placeholder="{{ __('Title') }}" class="cm-form-input">
                <div id="cm-editor-create-wrap">
                    <textarea id="cm-editor-create" style="width:100%;min-height:200px"></textarea>
                </div>
                <input type="hidden" id="cm-editor-create-content" value="">
                <div style="margin-bottom:12px">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;padding:10px 12px;border-radius:12px;border:1px solid var(--border);background:var(--bg-secondary);min-height:42px;align-items:center;cursor:text" @click="$refs.tagInput.focus()">
                        <template x-for="(tag, i) in editorTags" :key="i">
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;background:var(--accent);color:white">
                                <span x-text="tag"></span>
                                <button @click="editorTags.splice(i, 1)" style="background:none;border:none;color:rgba(255,255,255,0.7);cursor:pointer;padding:0;font-size:10px;line-height:1">&times;</button>
                            </span>
                        </template>
                        <input x-ref="tagInput" type="text" x-model="tagInputValue" @keydown.enter.prevent="addTag()" @keydown.backspace="if(tagInputValue === '' && editorTags.length) editorTags.pop()" :placeholder="editorTags.length === 0 ? '{{ __('Tags (Enter to add, max 5)') }}' : ''" style="flex:1;min-width:120px;border:none;background:transparent;outline:none;font-size:13px;color:var(--text);padding:0">
                    </div>
                </div>
            </div>
            <div class="cm-modal-foot">
                <button @click="showEditor = false; editingPost = null; editorTitle = ''; editorContent = ''; editorTags = []; tagInputValue = ''" class="cm-btn-cancel">{{ __('Cancel') }}</button>
                <button @click="savePost()" class="cm-btn-submit">
                    <i class="fas fa-paper-plane mr-1"></i>{{ __('Publish') }}
                </button>
            </div>
        </div>
    </div>

    <div x-show="viewingPost" x-transition class="cm-modal-overlay" :class="viewingPost ? 'open' : ''" style="display:none" @click.self="viewingPost = null">
        <div class="cm-modal" style="max-width:640px" @click.stop>
            <template x-if="viewingPost">
                <div>
                    <div class="cm-modal-head">
                        <div style="display:flex;align-items:center;gap:10px">
                            <img :src="viewingPost.user?.avatar || 'https://ui-avatars.com/api/?name=U&background=6366f1&color=fff'" class="cm-comment-avatar">
                            <div>
                                <div class="cm-comment-name" x-text="viewingPost.user?.name"></div>
                                <div class="cm-post-time" x-text="timeAgo(viewingPost.created_at)"></div>
                            </div>
                        </div>
                        <div style="display:flex;gap:8px">
                            <template x-if="viewingPost.is_owner">
                                <div style="display:flex;gap:8px">
                                    <button @click="startEditPost()" class="cm-modal-close" title="{{ __('Edit') }}"><i class="fas fa-edit"></i></button>
                                    <button @click="deletePost()" class="cm-modal-close" title="{{ __('Delete') }}" style="color:#ef4444"><i class="fas fa-trash"></i></button>
                                </div>
                            </template>
                            <button @click="viewingPost = null" class="cm-modal-close"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div class="cm-modal-body">
                        <template x-if="!editingInModal">
                            <div>
                                <div class="cm-detail-title" x-text="viewingPost.title"></div>
                                <template x-if="viewingPost.tags && viewingPost.tags.length">
                                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px">
                                        <template x-for="tag in viewingPost.tags" :key="tag.slug">
                                            <a :href="`/community?tag=${tag.slug}`" class="cm-tag" style="display:inline-flex;align-items:center;gap:5px" x-text="tag.name"></a>
                                        </template>
                                    </div>
                                </template>
                                <div class="cm-detail-content" x-text="viewingPost.content"></div>
                            </div>
                        </template>
                        <template x-if="editingInModal">
                            <div style="margin-bottom:16px">
                                <input type="text" x-model="editTitle" class="cm-form-input">
                                <div id="cm-editor-edit-wrap">
                                    <textarea id="cm-editor-edit" style="width:100%;min-height:200px"></textarea>
                                </div>
                                <input type="hidden" id="cm-editor-edit-content" value="">
                                <div style="margin-bottom:12px">
                                    <div style="display:flex;flex-wrap:wrap;gap:8px;padding:10px 12px;border-radius:12px;border:1px solid var(--border);background:var(--bg-secondary);min-height:42px;align-items:center;cursor:text" @click="$refs.editTagInput.focus()">
                                        <template x-for="(tag, i) in editTags" :key="i">
                                            <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;background:var(--accent);color:white">
                                                <span x-text="tag"></span>
                                                <button @click="editTags.splice(i, 1)" style="background:none;border:none;color:rgba(255,255,255,0.7);cursor:pointer;padding:0;font-size:10px;line-height:1">&times;</button>
                                            </span>
                                        </template>
                                        <input x-ref="editTagInput" type="text" x-model="editTagInputValue" @keydown.enter.prevent="addEditTag()" @keydown.backspace="if(editTagInputValue === '' && editTags.length) editTags.pop()" :placeholder="editTags.length === 0 ? '{{ __('Tags (Enter to add, max 5)') }}' : ''" style="flex:1;min-width:120px;border:none;background:transparent;outline:none;font-size:13px;color:var(--text);padding:0">
                                    </div>
                                </div>
                                <div style="display:flex;gap:8px">
                                    <button @click="editingInModal = false" class="cm-btn-cancel">{{ __('Cancel') }}</button>
                                    <button @click="saveEditPost()" class="cm-btn-submit">{{ __('Save') }}</button>
                                </div>
                            </div>
                        </template>

                        <div style="display:flex;gap:16px;padding:16px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin-bottom:16px">
                            <button @click="toggleLike(viewingPost.id, $el)" class="cm-action-btn" :class="viewingPost.liked ? 'liked' : ''">
                                <i :class="viewingPost.liked ? 'fas fa-heart' : 'far fa-heart'"></i>
                                <span x-text="viewingPost.likes_count"></span>
                            </button>
                            <span class="cm-action-btn" style="cursor:default">
                                <i class="far fa-eye"></i>
                                <span x-text="viewingPost.views_count"></span>
                            </span>
                        </div>

                        <div style="margin-bottom:16px">
                            <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:12px">{{ __('Comments') }} (<span x-text="viewingPost.comments?.length || 0"></span>)</div>
                            <div style="max-height:300px;overflow-y:auto">
                                <template x-for="comment in viewingPost.comments" :key="comment.id">
                                    <div class="cm-comment">
                                        <img :src="comment.user?.avatar || 'https://ui-avatars.com/api/?name=U&background=6366f1&color=fff'" class="cm-comment-avatar">
                                        <div class="cm-comment-body">
                                            <div>
                                                <span class="cm-comment-name" x-text="comment.user?.name"></span>
                                                <span class="cm-comment-time" x-text="timeAgo(comment.created_at)"></span>
                                            </div>
                                            <div class="cm-comment-text" x-text="comment.content"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div style="display:flex;gap:10px">
                            <input type="text" x-model="newComment" @keydown.enter="submitComment(viewingPost.id)" placeholder="{{ __('Write a comment...') }}" class="cm-form-input" style="margin-bottom:0">
                            <button @click="submitComment(viewingPost.id)" class="cm-btn-submit" style="white-space:nowrap">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    /* --- Hero parallax + pause offscreen --- */
    var hero = document.getElementById('cmHero');
    var layers = document.querySelectorAll('#cmHero [data-depth]');
    if (hero) {
        var heroVisible = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                heroVisible = entries[0].isIntersecting;
                hero.classList.toggle('cm-paused', !heroVisible);
            }, { threshold: 0 }).observe(hero);
        }
        hero.addEventListener('mousemove', function(e) {
            if (!heroVisible) return;
            var r = hero.getBoundingClientRect();
            var px = (e.clientX - r.left) / r.width - 0.5;
            var py = (e.clientY - r.top) / r.height - 0.5;
            layers.forEach(function(el) {
                var d = parseFloat(el.dataset.depth || 20);
                el.style.translate = (-px * d) + 'px ' + (-py * d) + 'px';
            });
        });
        hero.addEventListener('mouseleave', function() { layers.forEach(function(el){ el.style.translate = '0px 0px'; }); });
    }

    /* --- Animated counters --- */
    document.querySelectorAll('.cm-stat-val[data-count]').forEach(function(el) {
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

    /* --- Posts stagger reveal --- */
    var posts = document.querySelectorAll('.cm-post');
    if ('IntersectionObserver' in window && posts.length) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(en) {
                if (en.isIntersecting) {
                    var el = en.target;
                    el.style.transitionDelay = (parseInt(el.dataset.i || 0, 10) % 6 * 0.06) + 's';
                    el.classList.add('in');
                    io.unobserve(el);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
        posts.forEach(function(p) { io.observe(p); });
        setTimeout(function() { posts.forEach(function(p) { p.classList.add('in'); }); }, 4000);
    } else {
        posts.forEach(function(p) { p.classList.add('in'); });
    }

    /* --- Scroll to feed --- */
    var toFeed = document.getElementById('cmToFeed');
    if (toFeed) toFeed.addEventListener('click', function(e) {
        e.preventDefault();
        var t = document.getElementById('cmFeed');
        if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();

function communityApp() {
    return {
        showEditor: false,
        editingPost: null,
        editorTitle: '',
        editorContent: '',
        editorTags: [],
        tagInputValue: '',
        problemId: new URLSearchParams(window.location.search).get('problem') || null,
        viewingPost: null,
        newComment: '',
        editingInModal: false,
        editTitle: '',
        editContent: '',
        editTags: [],
        cmEditorCreate: null,
        cmEditorEdit: null,

        init() {
            if (this.problemId) {
                this.showEditor = true;
                this.$nextTick(() => this.initCreateEditor());
            }
            this.$watch('showEditor', (val) => {
                if (val) {
                    this.$nextTick(() => this.initCreateEditor());
                } else {
                    this.destroyCreateEditor();
                }
            });
            this.$watch('editingInModal', (val) => {
                if (val) {
                    this.$nextTick(() => this.initEditEditor());
                } else {
                    this.destroyEditEditor();
                }
            });
        },
        editTagInputValue: '',

        getCmSkin() {
            return !(document.documentElement.getAttribute('data-theme') || '').includes('light') ? 'oxide-dark' : 'oxide';
        },
        getCmContentCss() {
            return !(document.documentElement.getAttribute('data-theme') || '').includes('light') ? 'dark' : 'default';
        },
        getCmContentStyle() {
            const isDark = !(document.documentElement.getAttribute('data-theme') || '').includes('light');
            return 'body { font-family: Inter, sans-serif; font-size: 14px; color: ' + (isDark ? '#e2e8f0' : '#1e293b') + '; }';
        },

        initCreateEditor() {
            if (this.cmEditorCreate) return;
            const self = this;
            tinymce.init({
                selector: '#cm-editor-create',
                height: 300,
                skin: this.getCmSkin(),
                content_css: this.getCmContentCss(),
                menubar: false,
                plugins: 'lists link image code codesample fullscreen quickbars',
                toolbar: 'undo redo | blocks | bold italic strikethrough | link image codesample | bullist numlist | code fullscreen',
                codesample_languages: [
                    {text: 'HTML/XML', value: 'markup'}, {text: 'JavaScript', value: 'javascript'},
                    {text: 'TypeScript', value: 'typescript'}, {text: 'CSS', value: 'css'},
                    {text: 'PHP', value: 'php'}, {text: 'Python', value: 'python'},
                    {text: 'Java', value: 'java'}, {text: 'C', value: 'c'}, {text: 'C++', value: 'cpp'},
                ],
                content_style: this.getCmContentStyle(),
                setup: (editor) => { self.cmEditorCreate = editor; }
            });
        },

        destroyCreateEditor() {
            if (this.cmEditorCreate) {
                tinymce.remove('#cm-editor-create');
                this.cmEditorCreate = null;
            }
        },

        initEditEditor() {
            if (this.cmEditorEdit) return;
            const self = this;
            this.$nextTick(() => {
                const ta = document.getElementById('cm-editor-edit');
                if (!ta) return;
                tinymce.init({
                    selector: '#cm-editor-edit',
                    height: 300,
                    skin: self.getCmSkin(),
                    content_css: self.getCmContentCss(),
                    menubar: false,
                    plugins: 'lists link image code codesample fullscreen quickbars',
                    toolbar: 'undo redo | blocks | bold italic strikethrough | link image codesample | bullist numlist | code fullscreen',
                    codesample_languages: [
                        {text: 'HTML/XML', value: 'markup'}, {text: 'JavaScript', value: 'javascript'},
                        {text: 'TypeScript', value: 'typescript'}, {text: 'CSS', value: 'css'},
                        {text: 'PHP', value: 'php'}, {text: 'Python', value: 'python'},
                        {text: 'Java', value: 'java'}, {text: 'C', value: 'c'}, {text: 'C++', value: 'cpp'},
                    ],
                    content_style: self.getCmContentStyle(),
                    setup: (editor) => {
                        self.cmEditorEdit = editor;
                        editor.on('init', () => {
                            editor.setContent(self.editContent || '');
                        });
                    }
                });
            });
        },

        destroyEditEditor() {
            if (this.cmEditorEdit) {
                tinymce.remove('#cm-editor-edit');
                this.cmEditorEdit = null;
            }
        },

        addTag() {
            const val = this.tagInputValue.trim();
            if (val && this.editorTags.length < 5 && !this.editorTags.includes(val)) {
                this.editorTags.push(val);
            }
            this.tagInputValue = '';
        },

        addEditTag() {
            const val = this.editTagInputValue.trim();
            if (val && this.editTags.length < 5 && !this.editTags.includes(val)) {
                this.editTags.push(val);
            }
            this.editTagInputValue = '';
        },

        async openPost(postId) {
            try {
                const res = await fetch(`/community/${postId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.viewingPost = data.post;
                    this.editingInModal = false;
                }
            } catch (e) {
                console.error(e);
            }
        },

        async savePost() {
            if (!this.editorTitle.trim()) return;
            let content = '';
            if (this.cmEditorCreate) {
                content = this.cmEditorCreate.getContent();
            }
            if (!content.trim()) return;
            try {
                const url = this.editingPost ? `/community/${this.editingPost.id}` : '/community';
                const method = this.editingPost ? 'PUT' : 'POST';
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title: this.editorTitle, content: content, tags: this.editorTags, problem_id: this.problemId }),
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        },

        startEditPost() {
            this.editTitle = this.viewingPost.title;
            this.editContent = this.viewingPost.content;
            this.editTags = this.viewingPost.tags ? this.viewingPost.tags.map(t => t.name) : [];
            this.editTagInputValue = '';
            this.editingInModal = true;
        },

        async saveEditPost() {
            if (!this.editTitle.trim()) return;
            let content = '';
            if (this.cmEditorEdit) {
                content = this.cmEditorEdit.getContent();
            }
            if (!content.trim()) return;
            try {
                const res = await fetch(`/community/${this.viewingPost.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title: this.editTitle, content: content, tags: this.editTags }),
                });
                const data = await res.json();
                if (data.success) {
                    this.viewingPost.title = this.editTitle;
                    this.viewingPost.content = content;
                    this.viewingPost.tags = this.editTags.map(t => ({name: t, slug: t.toLowerCase().replace(/\s+/g, '-')}));
                    this.editingInModal = false;
                }
            } catch (e) {
                console.error(e);
            }
        },

        async deletePost() {
            if (!confirm('{{ __("Delete this post?") }}')) return;
            try {
                const res = await fetch(`/community/${this.viewingPost.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        },

        async toggleLike(postId, el) {
            try {
                const res = await fetch(`/community/${postId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    if (this.viewingPost && this.viewingPost.id === postId) {
                        this.viewingPost.liked = data.liked;
                        this.viewingPost.likes_count = data.likes;
                    }
                    const row = document.getElementById('post-' + postId);
                    if (row && row._x_dataStack) {
                        const scope = row._x_dataStack[0];
                        scope.liked = data.liked;
                        scope.likes = data.likes;
                    }
                }
            } catch (e) {
                console.error(e);
            }
        },

        async submitComment(postId) {
            if (!this.newComment.trim()) return;
            try {
                const res = await fetch('/community/comment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ post_id: postId, content: this.newComment }),
                });
                const data = await res.json();
                if (data.success) {
                    if (!this.viewingPost.comments) this.viewingPost.comments = [];
                    this.viewingPost.comments.push(data.comment);
                    this.newComment = '';
                    const count = data.comments_count || ((this.viewingPost.comments_count || 0) + 1);
                    this.viewingPost.comments_count = count;
                    const row = document.getElementById('post-' + postId);
                    if (row && row._x_dataStack) {
                        row._x_dataStack[0].commentsCount = count;
                    }
                }
            } catch (e) {
                console.error(e);
            }
        },

        timeAgo(date) {
            if (!date) return '';
            const seconds = Math.floor((new Date() - new Date(date)) / 1000);
            if (seconds < 60) return '{{ __("just now") }}';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return minutes + ' {{ __("min ago") }}';
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return hours + ' {{ __("h ago") }}';
            const days = Math.floor(hours / 24);
            return days + ' {{ __("d ago") }}';
        }
    };
}
</script>
@endsection
