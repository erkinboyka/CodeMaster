@extends('layouts.app')

@section('title', __('peer.title') . ' - CodeMaster')

@section('head')
<style>
.pi-hero {
    position: relative;
    background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 50%, var(--accent-3) 100%);
    padding: clamp(40px, 7vw, 80px) 24px clamp(60px, 8vw, 100px);
    overflow: hidden;
    text-align: center;
}
.pi-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%);
}
.pi-hero::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 60px;
    background: var(--bg);
    clip-path: ellipse(55% 100% at 50% 100%);
}
.pi-hero-grid {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
    background-size: 40px 40px;
}
.pi-hero h1 {
    font-size: clamp(32px, 5vw, 56px);
    font-weight: 900;
    color: white;
    letter-spacing: -1.5px;
    margin-bottom: 16px;
    position: relative;
    z-index: 2;
}
.pi-hero p {
    font-size: clamp(15px, 2vw, 18px);
    color: rgba(255,255,255,0.75);
    max-width: 500px;
    margin: 0 auto 32px;
    position: relative;
    z-index: 2;
    line-height: 1.7;
}
.pi-hero-actions {
    display: flex;
    justify-content: center;
    gap: 14px;
    position: relative;
    z-index: 2;
}
.pi-hero-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 32px;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.pi-hero-btn--primary {
    background: white;
    color: var(--accent);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}
.pi-hero-btn--primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.2);
}
.pi-hero-btn--ghost {
    background: rgba(255,255,255,0.15);
    color: white;
    border: 1px solid rgba(255,255,255,0.3);
    backdrop-filter: blur(8px);
}
.pi-hero-btn--ghost:hover {
    background: rgba(255,255,255,0.25);
    transform: translateY(-3px);
}
.pi-hero-btn i { font-size: 14px; }

.pi-section {
    max-width: 960px;
    margin: 0 auto;
    padding: 0 24px;
}
.pi-features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 48px;
}
.pi-feat {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 28px 20px;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
}
.pi-feat::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 50%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
    transition: transform 0.6s ease;
    pointer-events: none;
}
.pi-feat:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 50px -12px rgba(0,0,0,0.15);
}
.pi-feat:hover::before {
    transform: translateX(400%);
}
.pi-feat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.pi-feat:hover .pi-feat-icon {
    transform: scale(1.15) rotate(-5deg);
    filter: drop-shadow(0 6px 16px rgba(0,0,0,0.15));
}
.pi-feat-icon--accent {
    background: color-mix(in srgb, var(--accent) 10%, var(--card));
    color: var(--accent);
}
.pi-feat-icon--success {
    background: color-mix(in srgb, var(--success) 10%, var(--card));
    color: var(--success);
}
.pi-feat-icon--purple {
    background: color-mix(in srgb, var(--accent-2) 10%, var(--card));
    color: var(--accent-2);
}
.pi-feat-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 6px;
}
.pi-feat-desc {
    font-size: 13px;
    color: var(--text-muted);
    line-height: 1.6;
}

.pi-history {
    margin-top: 48px;
}
.pi-history-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}
.pi-history-title {
    font-size: 18px;
    font-weight: 800;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 8px;
}
.pi-history-title i { color: var(--accent); font-size: 16px; }
.pi-history-count {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 600;
}
.pi-list {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
}
.pi-list-empty {
    padding: 48px 24px;
    text-align: center;
    color: var(--text-muted);
}
.pi-list-empty i {
    font-size: 32px;
    color: var(--border);
    margin-bottom: 12px;
    display: block;
}
.pi-list-empty p {
    font-size: 14px;
}
.pi-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    transition: background 0.2s;
}
.pi-row:last-child { border-bottom: none; }
.pi-row:hover { background: var(--bg-secondary); }
.pi-row-info {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.pi-row-code {
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
    font-family: 'Courier New', monospace;
    letter-spacing: 0.5px;
}
.pi-row-meta {
    font-size: 11px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
}
.pi-row-meta i { font-size: 10px; }
.pi-row-right {
    display: flex;
    align-items: center;
    gap: 10px;
}
.pi-status {
    padding: 4px 10px;
    border-radius: 100px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.pi-status--waiting { background: rgba(234,179,8,0.1); color: var(--warning); }
.pi-status--connected { background: rgba(34,197,94,0.1); color: var(--success); }
.pi-status--ended { background: var(--bg-secondary); color: var(--text-muted); }
.pi-enter-btn {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid var(--border);
    background: var(--card);
    color: var(--text);
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.pi-enter-btn:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: color-mix(in srgb, var(--accent) 6%, var(--card));
}
.pi-enter-btn i { font-size: 11px; }

@media(max-width:640px) {
    .pi-hero-actions { flex-direction: column; align-items: center; }
    .pi-hero-btn { width: 100%; max-width: 280px; justify-content: center; }
    .pi-features { grid-template-columns: 1fr; }
    .pi-row { flex-direction: column; align-items: flex-start; gap: 10px; }
    .pi-row-right { width: 100%; justify-content: space-between; }
}
</style>
@endsection

@section('content')
<section class="pi-hero">
    <div class="pi-hero-grid"></div>
    <h1 class="reveal-up" data-delay="0">{{ __('peer.title') }}</h1>
    <p class="reveal-up" data-delay="0.1">{{ __('peer.hero_desc') }}</p>
    <div class="pi-hero-actions reveal-up" data-delay="0.2">
        <form action="{{ route('peer.create') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="pi-hero-btn pi-hero-btn--primary"><i class="fas fa-plus"></i> {{ __('peer.create_room') }}</button>
        </form>
        <a href="{{ route('peer.joinForm') }}" class="pi-hero-btn pi-hero-btn--ghost"><i class="fas fa-right-to-bracket"></i> {{ __('peer.join') }}</a>
    </div>
</section>

<div class="pi-section">
    @if(session('success'))
    <div style="margin-bottom:24px;padding:12px 16px;background:color-mix(in srgb,var(--success) 8%,var(--card));border:1px solid color-mix(in srgb,var(--success) 25%,var(--card));border-radius:12px;color:var(--success);font-size:13px;display:flex;align-items:center;gap:8px"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <div class="pi-features">
        <div class="pi-feat reveal-up" data-delay="0">
            <div class="pi-feat-icon pi-feat-icon--accent"><i class="fas fa-video"></i></div>
            <div class="pi-feat-title">{{ __('peer.video_connection') }}</div>
            <div class="pi-feat-desc">{{ __('peer.video_connection_desc') }}</div>
        </div>
        <div class="pi-feat reveal-up" data-delay="0.1">
            <div class="pi-feat-icon pi-feat-icon--success"><i class="fas fa-shield-halved"></i></div>
            <div class="pi-feat-title">{{ __('peer.secure') }}</div>
            <div class="pi-feat-desc">{{ __('peer.secure_desc') }}</div>
        </div>
        <div class="pi-feat reveal-up" data-delay="0.2">
            <div class="pi-feat-icon pi-feat-icon--purple"><i class="fas fa-code"></i></div>
            <div class="pi-feat-title">{{ __('peer.code_editor') }}</div>
            <div class="pi-feat-desc">{{ __('peer.code_editor_desc') }}</div>
        </div>
    </div>

    @if($rooms->count())
    <div class="pi-history reveal-up" data-delay="0">
        <div class="pi-history-head">
            <span class="pi-history-title"><i class="fas fa-clock-rotate-left"></i> {{ __('peer.my_history') }}</span>
            <span class="pi-history-count">{{ $rooms->total() }} {{ __('peer.total') }}</span>
        </div>
        <div class="pi-list">
            @foreach($rooms as $r)
            <div class="pi-row">
                <div class="pi-row-info">
                    <span class="pi-row-code">{{ $r->room_code }}</span>
                    <span class="pi-row-meta">
                        <i class="fas fa-user"></i> {{ $r->host_name }}
                        @if($r->guest_name) <i class="fas fa-arrow-right" style="font-size:8px;opacity:.4"></i> <i class="fas fa-user"></i> {{ $r->guest_name }} @endif
                        <span style="opacity:.3">&bull;</span>
                        <i class="fas fa-clock"></i> {{ $r->created_at->diffForHumans() }}
                    </span>
                </div>
                <div class="pi-row-right">
                    <span class="pi-status pi-status--{{ $r->status }}">
                        @if($r->status === 'waiting') {{ __('peer.waiting_status') }}
                        @elseif($r->status === 'connected') {{ __('peer.connected_status') }}
                        @else {{ __('interview_status_completed') }}
                        @endif
                    </span>
                    @if($r->status !== 'ended')
                    <a href="{{ route('peer.room', $r->room_code) }}" class="pi-enter-btn"><i class="fas fa-right-to-bracket"></i> {{ __('peer.join') }}</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div style="margin-top:12px">{{ $rooms->links() }}</div>
    </div>
    @endif
</div>
@endsection
