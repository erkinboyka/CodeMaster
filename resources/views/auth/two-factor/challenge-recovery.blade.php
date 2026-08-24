@extends('layouts.guest')
@section('title', __('Recovery Code'))

@section('content')
<div class="auth-card" style="max-width:440px">
    <div style="text-align:center;margin-bottom:24px">
        <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#eab308,#ca8a04);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
            <i class="fas fa-key" style="color:#fff;font-size:20px"></i>
        </div>
        <h2 style="font-size:18px;font-weight:800;color:var(--text);margin:0">{{ __('Use Recovery Code') }}</h2>
        <p style="font-size:13px;color:var(--text-muted);margin:6px 0 0">{{ __('Enter one of your recovery codes to access your account') }}</p>
    </div>

    @if ($errors->any())
    <div style="padding:12px 16px;border-radius:10px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);margin-bottom:16px">
        @foreach ($errors->all() as $error)
        <p style="font-size:12px;color:#ef4444;margin:0 0 4px">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('two-factor.recovery.verify') }}">
        @csrf
        <div style="margin-bottom:16px">
            <label style="display:block;font-size:12px;font-weight:700;color:var(--text);margin-bottom:6px">{{ __('Recovery Code') }}</label>
            <input type="text" name="recovery_code" required
                   placeholder="XXXX-XXXX"
                   style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:'JetBrains Mono',monospace;font-size:16px;font-weight:700;text-align:center;letter-spacing:2px;box-sizing:border-box"
                   autofocus>
        </div>

        <button type="submit" class="auth-submit" style="width:100%;margin-bottom:12px">
            <i class="fas fa-unlock" style="margin-right:6px"></i> {{ __('Recover Account') }}
        </button>
    </form>

    <div style="text-align:center">
        <a href="{{ route('login') }}" style="font-size:12px;color:var(--text-muted);text-decoration:none">{{ __('Back to login') }}</a>
    </div>
</div>
@endsection
