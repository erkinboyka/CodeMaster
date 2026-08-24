@extends('layouts.guest')
@section('title', __('Two-Factor Authentication Setup'))

@section('content')
<div class="auth-card" style="max-width:480px">
    <div style="text-align:center;margin-bottom:24px">
        <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--accent),var(--accent-2));display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
            <i class="fas fa-shield-halved" style="color:#fff;font-size:20px"></i>
        </div>
        <h2 style="font-size:18px;font-weight:800;color:var(--text);margin:0">{{ __('Set Up Two-Factor Authentication') }}</h2>
        <p style="font-size:13px;color:var(--text-muted);margin:6px 0 0">{{ __('Scan the QR code with your authenticator app') }}</p>
    </div>

    <div style="text-align:center;margin-bottom:20px">
        <div style="display:inline-block;padding:12px;background:white;border-radius:12px;border:1px solid var(--border)">
            <div id="qr-placeholder" style="width:200px;height:200px;display:flex;align-items:center;justify-content:center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrUri) }}" alt="QR Code" style="width:200px;height:200px;border-radius:4px">
            </div>
        </div>
    </div>

    <div style="margin-bottom:20px">
        <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:6px">{{ __('Or enter this key manually') }}</div>
        <div style="padding:10px 14px;border-radius:8px;background:var(--bg-2);border:1px solid var(--border);font-family:'JetBrains Mono',monospace;font-size:14px;font-weight:700;color:var(--accent);letter-spacing:2px;text-align:center;word-break:break-all">
            {{ chunk_split($secret, 4, ' ') }}
        </div>
    </div>

    <form method="POST" action="{{ route('two-factor.confirm') }}">
        @csrf
        <div style="margin-bottom:16px">
            <label style="display:block;font-size:12px;font-weight:700;color:var(--text);margin-bottom:6px">{{ __('Enter the 6-digit code from your app') }}</label>
            <input type="text" name="code" required maxlength="6" pattern="[0-9]{6}" autocomplete="one-time-code"
                   placeholder="000000"
                   style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:'JetBrains Mono',monospace;font-size:18px;font-weight:700;text-align:center;letter-spacing:6px;box-sizing:border-box"
                   autofocus>
            @error('code')<p style="font-size:12px;color:#ef4444;margin-top:4px">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="auth-submit" style="width:100%">
            <i class="fas fa-check" style="margin-right:6px"></i> {{ __('Enable Two-Factor') }}
        </button>
    </form>

    <div style="text-align:center;margin-top:16px">
        <a href="{{ route('two-factor.show') }}" style="font-size:12px;color:var(--text-muted);text-decoration:none">{{ __('Back to settings') }}</a>
    </div>
</div>
@endsection
