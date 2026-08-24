@extends('layouts.app')

@section('title', ('peer.join_title') . ' - CodeMaster')

@section('head')
<style>
.pj-page {
    min-height: calc(100vh - 64px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 24px;
    background: var(--bg);
    position: relative;
    overflow: hidden;
}
.pj-page::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 30% 30%, color-mix(in srgb, var(--accent) 6%, transparent) 0%, transparent 50%),
        radial-gradient(circle at 70% 70%, color-mix(in srgb, var(--accent-2) 5%, transparent) 0%, transparent 50%);
    pointer-events: none;
}

.pj-card {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 420px;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 40px 32px 32px;
    box-shadow: 0 20px 60px -15px rgba(0,0,0,0.1);
}
.pj-card-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    background: color-mix(in srgb, var(--accent) 10%, var(--card));
    color: var(--accent);
    border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);
}
.pj-card-title {
    font-size: 22px;
    font-weight: 800;
    color: var(--text);
    text-align: center;
    margin-bottom: 6px;
}
.pj-card-desc {
    font-size: 14px;
    color: var(--text-muted);
    text-align: center;
    margin-bottom: 28px;
    line-height: 1.6;
}

.pj-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}
.pj-label i { font-size: 11px; color: var(--accent); }
.pj-input {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid var(--border);
    border-radius: 12px;
    background: var(--bg-secondary);
    color: var(--text);
    font-size: 1.5rem;
    font-weight: 800;
    font-family: 'Courier New', monospace;
    text-align: center;
    letter-spacing: 5px;
    text-transform: uppercase;
    transition: all 0.3s;
    box-sizing: border-box;
}
.pj-input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 10%, transparent);
}
.pj-input::placeholder {
    color: var(--text-muted);
    font-size: 1rem;
    letter-spacing: 3px;
    font-weight: 400;
    opacity: 0.4;
}
.pj-hint {
    text-align: center;
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}
.pj-hint i { font-size: 11px; color: var(--accent); opacity: 0.6; }
.pj-error {
    margin-bottom: 16px;
    padding: 10px 14px;
    border-radius: 10px;
    background: color-mix(in srgb, var(--danger) 8%, var(--card));
    border: 1px solid color-mix(in srgb, var(--danger) 20%, var(--card));
    color: var(--danger);
    font-size: 13px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.pj-btn {
    width: 100%;
    padding: 14px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    border: 0;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    color: #fff;
    background: var(--gradient);
    box-shadow: 0 4px 16px var(--accent-glow-strong);
    margin-top: 24px;
}
.pj-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px var(--accent-glow-strong);
}
.pj-btn:active { transform: translateY(0); }
.pj-btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none !important; box-shadow: none !important; }

.pj-divider {
    text-align: center;
    color: var(--text-muted);
    font-size: 13px;
    margin: 24px 0;
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
}
.pj-divider::before,
.pj-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}
.pj-back {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    color: var(--accent);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    padding: 8px;
    border-radius: 8px;
}
.pj-back:hover { background: color-mix(in srgb, var(--accent) 6%, var(--card)); }
.pj-back i { font-size: 12px; transition: transform 0.2s; }
.pj-back:hover i { transform: translateX(-3px); }

.pj-features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 24px;
}
.pj-feat {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 16px 12px;
    text-align: center;
    transition: all 0.3s;
}
.pj-feat:hover { transform: translateY(-3px); border-color: color-mix(in srgb, var(--accent) 30%, var(--border)); }
.pj-feat-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    margin: 0 auto 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}
.pj-feat-icon--1 { background: color-mix(in srgb, var(--accent) 10%, var(--card)); color: var(--accent); }
.pj-feat-icon--2 { background: color-mix(in srgb, var(--success) 10%, var(--card)); color: var(--success); }
.pj-feat-icon--3 { background: color-mix(in srgb, var(--accent-2) 10%, var(--card)); color: var(--accent-2); }
.pj-feat-title { font-size: 12px; font-weight: 700; color: var(--text); margin-bottom: 2px; }
.pj-feat-desc { font-size: 11px; color: var(--text-muted); line-height: 1.4; }

@media(max-width:480px) {
    .pj-card { padding: 32px 20px 24px; }
    .pj-features { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')
<div class="pj-page">
    <div>
        <div class="pj-card reveal-up" data-delay="0">
            <div class="pj-card-icon"><i class="fas fa-right-to-bracket"></i></div>
            <h1 class="pj-card-title">{{ __('peer.join') }}</h1>
            <p class="pj-card-desc">{{ __('peer.join_desc') }}</p>

            @if(session('error'))
            <div class="pj-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif

            <form action="{{ route('peer.join') }}" method="POST">
                @csrf
                <div style="margin-bottom:20px">
                    <label class="pj-label"><i class="fas fa-key"></i> {{ __('peer.room_code') }}</label>
                    <input type="text" name="room_code" class="pj-input" placeholder="A1B2C3D4" maxlength="8"
                           value="{{ old('room_code') }}" required autofocus
                           pattern="[A-Za-z0-9]{8}" title="{{ __('peer.room_code_hint') }}">
                    <div class="pj-hint"><i class="fas fa-circle-info"></i> {{ __('peer.room_code_sub') }}</div>
                    @error('room_code')
                    <p style="color:var(--danger);font-size:12px;margin-top:6px;text-align:center">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="pj-btn"><i class="fas fa-right-to-bracket" style="margin-right:6px"></i> {{ __('peer.join') }}</button>
            </form>

            <div class="pj-divider">{{ __('peer.or') }}</div>
            <a href="{{ route('peer.index') }}" class="pj-back"><i class="fas fa-arrow-left"></i> {{ __('peer.go_back') }}</a>
        </div>

        <div class="pj-features reveal-up" data-delay="0.15">
            <div class="pj-feat">
                <div class="pj-feat-icon pj-feat-icon--1"><i class="fas fa-video"></i></div>
                <div class="pj-feat-title">{{ __('peer.video') }}</div>
                <div class="pj-feat-desc">{{ __('peer.p2p_webrtc') }}</div>
            </div>
            <div class="pj-feat">
                <div class="pj-feat-icon pj-feat-icon--2"><i class="fas fa-shield-halved"></i></div>
                <div class="pj-feat-title">{{ __('peer.secure') }}</div>
                <div class="pj-feat-desc">{{ __('peer.encryption') }}</div>
            </div>
            <div class="pj-feat">
                <div class="pj-feat-icon pj-feat-icon--3"><i class="fas fa-code"></i></div>
                <div class="pj-feat-title">{{ __('peer.code') }}</div>
                <div class="pj-feat-desc">{{ __('peer.editor') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
