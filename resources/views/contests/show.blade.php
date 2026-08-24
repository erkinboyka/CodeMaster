@extends('layouts.app')

@section('title', $contest->title . ' - CodeMaster')

@section('head')
<style>
    .ct-show-hero {
        position: relative;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 50%, var(--accent-3) 100%);
        padding: clamp(60px, 10vw, 120px) 24px clamp(50px, 8vw, 100px);
        overflow: hidden;
    }
    .ct-show-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%);
    }
    .ct-show-hero::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 50px;
        background: var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .ct-show-hero-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 40px 40px;
    }
    .ct-show-hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        animation: ctShowOrb 8s ease-in-out infinite;
    }
    .ct-show-hero-orb:nth-child(1) { width: 250px; height: 250px; background: rgba(255,255,255,0.1); top: -80px; left: -40px; }
    .ct-show-hero-orb:nth-child(2) { width: 200px; height: 200px; background: rgba(255,255,255,0.08); bottom: -60px; right: -20px; animation-delay: -3s; }
    @@keyframes ctShowOrb {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-15px) scale(1.05); }
    }

    .ct-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        margin-bottom: 16px;
        position: relative;
        z-index: 2;
    }
    .ct-bc-link { color: rgba(255,255,255,0.6); text-decoration: none; transition: .2s; }
    .ct-bc-link:hover { color: white; }
    .ct-bc-sep { font-size: 10px; color: rgba(255,255,255,0.4); }
    .ct-bc-current { color: white; font-weight: 600; }

    .ct-show-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
        position: relative;
        z-index: 2;
    }
    .ct-show-title {
        font-size: clamp(28px, 5vw, 48px);
        font-weight: 900;
        color: white;
        letter-spacing: -1.5px;
        margin-bottom: 12px;
    }
    .ct-show-desc {
        font-size: 16px;
        color: rgba(255,255,255,0.7);
        margin-bottom: 20px;
        line-height: 1.7;
    }
    .ct-show-badges { display: flex; gap: 8px; flex-wrap: wrap; }
    .ct-badge {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .ct-badge-active { background: #22c55e; color: white; animation: ctPulse 2s ease-in-out infinite; }
    .ct-badge-draft { background: #eab308; color: #1a1a1a; }
    .ct-badge-finished { background: rgba(255,255,255,0.2); color: white; }
    .ct-badge-diff { background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); }
    @@keyframes ctPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.4); }
        50% { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
    }

    .ct-timer {
        text-align: right;
        flex-shrink: 0;
    }
    .ct-timer-val {
        font-size: clamp(28px, 4vw, 44px);
        font-weight: 900;
        color: white;
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: 2px;
    }
    .ct-timer-label {
        font-size: 12px;
        color: rgba(255,255,255,0.5);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 4px;
    }

    .ct-show-actions {
        display: flex;
        gap: 8px;
        margin-top: 16px;
        position: relative;
        z-index: 2;
    }
    .ct-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        border: 1px solid rgba(255,255,255,0.2);
        background: rgba(255,255,255,0.1);
        color: white;
        backdrop-filter: blur(8px);
    }
    .ct-action-btn:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-2px);
    }
    .ct-action-btn--danger {
        background: rgba(239,68,68,0.2);
        border-color: rgba(239,68,68,0.3);
        color: #fca5a5;
    }
    .ct-action-btn--danger:hover {
        background: rgba(239,68,68,0.3);
    }

    .ct-problem-card {
        border-radius: 16px;
        background: var(--card);
        border: 1px solid var(--border);
        overflow: visible;
        transition: all 0.3s;
    }
    .ct-problem-card:hover {
        border-color: var(--accent);
    }
    .ct-problem-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .ct-problem-header:hover { background: var(--bg-secondary, rgba(0,0,0,0.02)); }
    .ct-problem-num {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--accent-glow);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        color: var(--accent);
        flex-shrink: 0;
    }
    .ct-problem-info { flex: 1; margin-left: 12px; }
    .ct-problem-title { font-size: 14px; font-weight: 700; color: var(--text); }
    .ct-problem-meta { display: flex; gap: 8px; margin-top: 4px; }
    .ct-problem-tag {
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 600;
    }
    .ct-problem-check {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    .ct-problem-expand {
        padding: 0 20px 20px;
        border-top: 1px solid var(--border);
    }
    .ct-problem-desc {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.7;
        margin: 16px 0;
    }
    .ct-code-block {
        background: #0f172a;
        border-radius: 12px;
        padding: 16px;
        margin: 12px 0;
        overflow-x: auto;
    }
    .ct-code-label {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }
    .ct-code-content {
        font-family: 'JetBrains Mono', monospace;
        font-size: 13px;
        color: #4ade80;
        white-space: pre;
    }
    .ct-textarea {
        width: 100%;
        min-height: 160px;
        padding: 16px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--card);
        color: var(--text);
        font-family: 'JetBrains Mono', monospace;
        font-size: 13px;
        resize: vertical;
        transition: border-color 0.3s;
        outline: none;
    }
    .ct-textarea:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }
    .ct-submit-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        background: var(--accent);
        color: white;
    }
    .ct-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px var(--accent-glow);
    }
    .ct-submit-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

    .ct-result {
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        margin-top: 12px;
    }
    .ct-result-pass { background: rgba(34,197,94,0.1); color: #22c55e; border: 1px solid rgba(34,197,94,0.2); }
    .ct-result-fail { background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }

    .ct-info-card {
        border-radius: 16px;
        background: var(--card);
        border: 1px solid var(--border);
        padding: 24px;
    }
    .ct-info-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 20px;
    }
    .ct-info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid var(--border);
    }
    .ct-info-row:last-child { border-bottom: none; }
    .ct-info-label { font-size: 13px; color: var(--text-muted); }
    .ct-info-value { font-size: 13px; font-weight: 600; color: var(--text); }

    .ct-score-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: conic-gradient(var(--accent) calc(var(--pct) * 1%), var(--border) 0);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        position: relative;
    }
    .ct-score-inner {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        background: var(--card);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .ct-score-val { font-size: 28px; font-weight: 900; color: var(--accent); }
    .ct-score-label { font-size: 11px; color: var(--text-muted); }

    .ct-add-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        padding: 10px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        border: 2px dashed var(--border);
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.3s;
    }
    .ct-add-btn:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: var(--accent-glow);
    }

    .ct-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
        padding: 16px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s;
    }
    .ct-modal-overlay.open { opacity: 1; pointer-events: all; }
    .ct-modal {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 20px;
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        transform: scale(0.95) translateY(10px);
        transition: transform 0.3s;
    }
    .ct-modal-overlay.open .ct-modal { transform: scale(1) translateY(0); }
    .ct-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 24px;
        border-bottom: 1px solid var(--border);
    }
    .ct-modal-title { font-size: 18px; font-weight: 700; color: var(--text); }
    .ct-modal-close {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: none;
        background: var(--bg);
        color: var(--text-muted);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .ct-modal-close:hover { background: var(--accent-glow); color: var(--accent); }
    .ct-modal-body { padding: 24px; }
    .ct-modal-foot {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding: 16px 24px;
        border-top: 1px solid var(--border);
    }
    .ct-form-group { margin-bottom: 16px; }
    .ct-form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
    .ct-form-input {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: var(--bg);
        color: var(--text);
        font-size: 14px;
        transition: border-color 0.3s;
        outline: none;
    }
    .ct-form-input:focus { border-color: var(--accent); }
    .ct-form-select {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: var(--bg);
        color: var(--text);
        font-size: 14px;
        outline: none;
    }
    .ct-form-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .ct-form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .ct-btn-cancel {
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid var(--border);
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s;
    }
    .ct-btn-cancel:hover { background: var(--bg); }
    .ct-btn-submit {
        padding: 10px 24px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        background: var(--accent);
        color: white;
        cursor: pointer;
        transition: all 0.3s;
    }
    .ct-btn-submit:hover { transform: translateY(-1px); box-shadow: 0 4px 12px var(--accent-glow); }

    .ct-sticky-sidebar {
        position: sticky;
        top: 24px;
        align-self: start;
        overflow: visible;
        z-index: 1;
    }

    .ct-heatmap-card {
        border-radius: 16px;
        background: var(--card);
        border: 1px solid var(--border);
        padding: 20px;
        overflow: visible;
        position: relative;
    }
    .ct-heatmap-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ct-heatmap-grid {
        display: flex;
        gap: 3px;
        overflow-x: auto;
        padding-bottom: 4px;
        position: relative;
    }
    .ct-heatmap-week {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .ct-heatmap-cell {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        cursor: pointer;
        transition: all 0.15s;
        position: relative;
    }
    .ct-heatmap-cell:hover {
        outline: 2px solid var(--accent);
        outline-offset: 1px;
    }
    .ct-heatmap-lvl-0 { background: var(--bg); border: 1px solid var(--border); }
    .ct-heatmap-lvl-1 { background: #0e4429; }
    .ct-heatmap-lvl-2 { background: #006d32; }
    .ct-heatmap-lvl-3 { background: #26a641; }
    .ct-heatmap-lvl-4 { background: #39d353; }
    [data-theme*="-light"] .ct-heatmap-lvl-0 { background: var(--bg); border-color: var(--border); }
    [data-theme*="-light"] .ct-heatmap-lvl-1 { background: #9be9a8; }
    [data-theme*="-light"] .ct-heatmap-lvl-2 { background: #40c463; }
    [data-theme*="-light"] .ct-heatmap-lvl-3 { background: #30a14e; }
    [data-theme*="-light"] .ct-heatmap-lvl-4 { background: #216e39; }
    .ct-heatmap-months {
        display: flex;
        gap: 3px;
        margin-bottom: 4px;
        overflow-x: auto;
    }
    .ct-heatmap-month-label {
        font-size: 10px;
        color: var(--text-muted);
        min-width: 42px;
    }
    .ct-heatmap-legend {
        display: flex;
        align-items: center;
        gap: 4px;
        justify-content: flex-end;
        margin-top: 8px;
        font-size: 10px;
        color: var(--text-muted);
    }
    .ct-heatmap-legend-cell {
        width: 12px;
        height: 12px;
        border-radius: 3px;
    }
    .ct-heatmap-tooltip {
        display: none;
        position: fixed;
        padding: 6px 12px;
        border-radius: 8px;
        background: var(--text);
        color: var(--bg);
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        z-index: 9999;
        pointer-events: none;
        box-shadow: 0 4px 12px rgba(0,0,0,.3);
    }
    .ct-heatmap-cell:hover .ct-heatmap-tooltip { display: block; }
</style>
@endsection

@section('content')
<section class="ct-show-hero">
    <div class="ct-show-hero-grid"></div>
    <div class="ct-show-hero-orb"></div>
    <div class="ct-show-hero-orb"></div>

    <div class="max-w-5xl mx-auto">
        <nav class="ct-breadcrumb">
            <a href="{{ route('contests.index') }}" class="ct-bc-link">{{ __('Contests') }}</a>
            <i class="fas fa-chevron-right ct-bc-sep"></i>
            <span class="ct-bc-current">{{ $contest->title }}</span>
        </nav>

        <div class="ct-show-header">
            <div>
                <h1 class="ct-show-title reveal-up" data-delay="0">{{ $contest->title }}</h1>
                @if($contest->description)
                <p class="ct-show-desc reveal-up" data-delay="0.1">{{ $contest->description }}</p>
                @endif
                <div class="ct-show-badges reveal-up" data-delay="0.2">
                    @if($contest->status === 'active')
                    <span class="ct-badge ct-badge-active">{{ __('Active') }}</span>
                    @elseif($contest->status === 'draft')
                    <span class="ct-badge ct-badge-draft">{{ __('Draft') }}</span>
                    @else
                    <span class="ct-badge ct-badge-finished">{{ __('Finished') }}</span>
                    @endif
                    <span class="ct-badge ct-badge-diff">{{ __('difficulty_' . $contest->difficulty) }}</span>
                </div>
            </div>
            <div class="ct-timer" x-data="{ t: {{ $contest->getTimeRemainingAttribute() ?? 0 }} }" x-init="if(t > 0) setInterval(() => { if(t > 0) t-- }, 1000)">
                <div class="ct-timer-val" x-text="`${Math.floor(t/3600)}:${String(Math.floor((t%3600)/60)).padStart(2,'0')}:${String(t%60).padStart(2,'0')}`">{{ $contest->time_limit }}:00</div>
                <div class="ct-timer-label">{{ __('Time Remaining') }}</div>
            </div>
        </div>

        <div class="ct-show-actions">
            @auth
            @if(Auth::id() === $contest->created_by)
            <a href="{{ route('contests.edit', $contest->id) }}" class="ct-action-btn">
                <i class="fas fa-edit"></i> {{ __('Edit') }}
            </a>
            <form action="{{ route('contests.destroy', $contest->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete this contest?') }}')" style="margin:0">
                @csrf
                @method('DELETE')
                <button type="submit" class="ct-action-btn ct-action-btn--danger">
                    <i class="fas fa-trash"></i> {{ __('Delete') }}
                </button>
            </form>
            @endif
            @endauth
            <a href="{{ route('contests.leaderboard', $contest->id) }}" class="ct-action-btn">
                <i class="fas fa-trophy"></i> {{ __('Leaderboard') }}
            </a>
        </div>
    </div>
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl text-sm font-medium" style="background:rgba(34,197,94,0.1);color:#22c55e;border:1px solid rgba(34,197,94,0.2)">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-4">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                <h3 style="font-size:18px;font-weight:700;color:var(--text)">{{ __('Problems') }} ({{ $problems->count() }})</h3>
                @auth
                @if(Auth::id() === $contest->created_by)
                <button onclick="document.getElementById('addProblemModal').classList.add('open')" class="ct-add-btn" style="width:auto;border:none;background:var(--accent);color:white;padding:8px 16px">
                    <i class="fas fa-plus"></i> {{ __('Add Problem') }}
                </button>
                @endif
                @endauth
            </div>

            @forelse($problems as $problem)
            @php
            $diffColor = match($problem->difficulty) { 'easy' => '#22c55e', 'medium' => '#eab308', 'hard' => '#ef4444', default => '#6366f1' };
            $isPassed = isset($userSubmissions[$problem->id]) && $userSubmissions[$problem->id]->status === 'accepted';
            @endphp
            <a href="{{ route('contests.problems.show', [$contest->id, $problem->id]) }}" class="ct-problem-card reveal-up" data-stagger="{{ $loop->index }}" style="text-decoration:none;display:block">
                <div class="ct-problem-header">
                    <div style="display:flex;align-items:center;gap:12px;flex:1">
                        <div class="ct-problem-num">{{ $problem->order_num ?? $loop->iteration }}</div>
                        <div class="ct-problem-info">
                            <div class="ct-problem-title">{{ $problem->title }}</div>
                            <div class="ct-problem-meta">
                                <span class="ct-problem-tag" style="background:{{ $diffColor }}15;color:{{ $diffColor }}">{{ __('difficulty_' . $problem->difficulty) }}</span>
                                <span class="ct-problem-tag" style="background:var(--accent-glow);color:var(--accent)">{{ $problem->points }} {{ __('pts') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="ct-problem-check" style="background:{{ $isPassed ? 'rgba(34,197,94,0.1)' : 'var(--bg)' }}">
                        @if($isPassed)
                        <i class="fas fa-check" style="color:#22c55e"></i>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <div class="ct-problem-card" style="text-align:center;padding:40px">
                <i class="fas fa-list-check" style="font-size:36px;color:var(--text-muted);margin-bottom:12px"></i>
                <p style="color:var(--text-muted)">{{ __('No problems yet') }}</p>
                @auth
                @if(Auth::id() === $contest->created_by)
                <p style="font-size:13px;color:var(--text-muted);margin-top:8px">{{ __('Click "Add Problem" to get started') }}</p>
                @endif
                @endauth
            </div>
            @endforelse
        </div>

        <div class="space-y-6 ct-sticky-sidebar">
            @auth
            <div class="ct-heatmap-card">
                <div class="ct-heatmap-title">
                    <i class="fas fa-fire" style="color:var(--accent)"></i>
                    {{ __('Your Activity') }}
                    <span style="margin-left:auto;font-size:12px;font-weight:500;color:var(--text-muted)" id="heatmap-total"></span>
                </div>
                <div x-data="heatmap()" x-init="init()" id="heatmap-container">
                    <div class="ct-heatmap-grid" id="heatmap-grid"></div>
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

            <div class="ct-info-card">
                <div class="ct-info-title">{{ __('Info') }}</div>
                <div class="ct-info-row">
                    <span class="ct-info-label">{{ __('Status') }}</span>
                    <span class="ct-info-value">{{ __('Active') }}</span>
                </div>
                <div class="ct-info-row">
                    <span class="ct-info-label">{{ __('Difficulty') }}</span>
                    <span class="ct-info-value">{{ __('difficulty_' . $contest->difficulty) }}</span>
                </div>
                <div class="ct-info-row">
                    <span class="ct-info-label">{{ __('Time Limit') }}</span>
                    <span class="ct-info-value">{{ $contest->time_limit }} {{ __('min') }}</span>
                </div>
                <div class="ct-info-row">
                    <span class="ct-info-label">{{ __('Problems') }}</span>
                    <span class="ct-info-value">{{ $problems->count() }}</span>
                </div>
                <div class="ct-info-row">
                    <span class="ct-info-label">{{ __('Submissions') }}</span>
                    <span class="ct-info-value">{{ $contest->submissions_count }}</span>
                </div>
                @if($contest->start_time)
                <div class="ct-info-row">
                    <span class="ct-info-label">{{ __('Start') }}</span>
                    <span class="ct-info-value">{{ $contest->start_time->format('d.m.Y H:i') }}</span>
                </div>
                @endif
                @if($contest->end_time)
                <div class="ct-info-row">
                    <span class="ct-info-label">{{ __('End') }}</span>
                    <span class="ct-info-value">{{ $contest->end_time->format('d.m.Y H:i') }}</span>
                </div>
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
            <div class="ct-info-card" style="text-align:center">
                <div class="ct-info-title">{{ __('Score') }}</div>
                <div class="ct-score-circle" style="--pct:{{ $pct }}">
                    <div class="ct-score-inner">
                        <div class="ct-score-val">{{ $earnedPoints }}</div>
                        <div class="ct-score-label">/ {{ $totalPoints }}</div>
                    </div>
                </div>
                <p style="font-size:13px;color:var(--text-muted)">{{ __('points') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

@if(Auth::id() === $contest->created_by ?? false)
<div id="addProblemModal" class="ct-modal-overlay">
    <div class="ct-modal">
        <div class="ct-modal-head">
            <div class="ct-modal-title">{{ __('Add Problem') }}</div>
            <button class="ct-modal-close" onclick="document.getElementById('addProblemModal').classList.remove('open')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('contests.problems.store', $contest->id) }}" method="POST">
            <div class="ct-modal-body">
                <div class="ct-form-group">
                    <label class="ct-form-label">{{ __('Title') }} *</label>
                    <input type="text" name="title" required class="ct-form-input">
                </div>
                <div class="ct-form-group">
                    <label class="ct-form-label">{{ __('Description') }}</label>
                    <textarea name="description" rows="4" class="ct-form-input" style="resize:vertical"></textarea>
                </div>
                <div class="ct-form-row" style="margin-bottom:16px">
                    <div>
                        <label class="ct-form-label">{{ __('Difficulty') }} *</label>
                        <select name="difficulty" class="ct-form-select">
                            <option value="easy">{{ __('Easy') }}</option>
                            <option value="medium" selected>{{ __('Medium') }}</option>
                            <option value="hard">{{ __('Hard') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="ct-form-label">{{ __('Points') }} *</label>
                        <input type="number" name="points" value="100" min="1" class="ct-form-input">
                    </div>
                    <div>
                        <label class="ct-form-label">{{ __('Language') }} *</label>
                        <select name="language" class="ct-form-select">
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
                <div class="ct-form-row-2" style="margin-bottom:16px">
                    <div>
                        <label class="ct-form-label">{{ __('Input Example') }}</label>
                        <textarea name="input_example" rows="3" class="ct-form-input" style="font-family:'JetBrains Mono',monospace;font-size:12px;resize:vertical"></textarea>
                    </div>
                    <div>
                        <label class="ct-form-label">{{ __('Output Example') }}</label>
                        <textarea name="output_example" rows="3" class="ct-form-input" style="font-family:'JetBrains Mono',monospace;font-size:12px;resize:vertical"></textarea>
                    </div>
                </div>
                <div class="ct-form-group">
                    <label class="ct-form-label">{{ __('Constraints') }}</label>
                    <input type="text" name="constraints" class="ct-form-input">
                </div>
                <div class="ct-form-group">
                    <label class="ct-form-label">{{ __('Starter Code') }}</label>
                    <textarea name="starter_code" rows="4" class="ct-form-input" style="font-family:'JetBrains Mono',monospace;font-size:12px;resize:vertical"></textarea>
                </div>
                <div class="ct-form-group">
                    <label class="ct-form-label">{{ __('Tests (JSON)') }}</label>
                    <textarea name="tests_json" rows="3" placeholder='[{"input": "1 2", "expected": "3"}]' class="ct-form-input" style="font-family:'JetBrains Mono',monospace;font-size:11px;resize:vertical"></textarea>
                </div>
                <div class="ct-form-row-2">
                    <div>
                        <label class="ct-form-label">{{ __('Time Limit (sec)') }}</label>
                        <input type="number" name="time_limit" value="2" min="1" class="ct-form-input">
                    </div>
                    <div>
                        <label class="ct-form-label">{{ __('Memory Limit (MB)') }}</label>
                        <input type="number" name="memory_limit" value="256" min="64" class="ct-form-input">
                    </div>
                </div>
            </div>
            <div class="ct-modal-foot">
                <button type="button" onclick="document.getElementById('addProblemModal').classList.remove('open')" class="ct-btn-cancel">{{ __('Cancel') }}</button>
                <button type="submit" class="ct-btn-submit">{{ __('Add Problem') }}</button>
            </div>
        </form>
    </div>
</div>
@endif

<div id="hm-tip" class="ct-heatmap-tooltip"></div>

<script>
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
                html += '<div class="ct-heatmap-week">';
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

                    html += `<div class="ct-heatmap-cell ct-heatmap-lvl-${isFuture ? 0 : lvl}" style="${opacity}" data-tip="${label.replace(/"/g,'&quot;')}"></div>`;
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                html += '</div>';
            }

            grid.innerHTML = html;

            grid.addEventListener('mousemove', function(e) {
                const cell = e.target.closest('.ct-heatmap-cell');
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
