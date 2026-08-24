@extends('layouts.guest')
@section('title', __('Two-Factor Authentication'))

@section('content')
<div class="auth-card" style="max-width:440px">
    <div style="text-align:center;margin-bottom:24px">
        <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--accent),var(--accent-2));display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
            <i class="fas fa-shield-halved" style="color:#fff;font-size:20px"></i>
        </div>
        <h2 style="font-size:18px;font-weight:800;color:var(--text);margin:0">{{ __('Two-Factor Authentication') }}</h2>
        <p style="font-size:13px;color:var(--text-muted);margin:6px 0 0">{{ __('Enter the 6-digit code from your authenticator app') }}</p>
    </div>

    @if ($errors->any())
    <div style="padding:12px 16px;border-radius:10px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);margin-bottom:16px">
        @foreach ($errors->all() as $error)
        <p style="font-size:12px;color:#ef4444;margin:0 0 4px">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('two-factor.challenge') }}">
        @csrf
        <div style="margin-bottom:16px">
            <input type="text" name="code" required maxlength="6" pattern="[0-9]{6}" autocomplete="one-time-code"
                   placeholder="000000"
                   style="width:100%;padding:14px;border-radius:12px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:'JetBrains Mono',monospace;font-size:22px;font-weight:700;text-align:center;letter-spacing:8px;box-sizing:border-box"
                   autofocus>
        </div>

        <button type="submit" class="auth-submit" style="width:100%;margin-bottom:12px">
            <i class="fas fa-arrow-right" style="margin-right:6px"></i> {{ __('Verify') }}
        </button>
    </form>

    <div style="text-align:center">
        <a href="{{ route('two-factor.recovery') }}" style="font-size:12px;color:var(--text-muted);text-decoration:none">{{ __('Use a recovery code instead') }}</a>
    </div>
</div>
@endsection
