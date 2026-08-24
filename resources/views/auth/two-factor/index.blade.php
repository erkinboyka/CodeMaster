@extends('layouts.app')
@section('title', __('Two-Factor Authentication'))

@section('content')
<main class="main-content">
    <div style="max-width:560px;margin:0 auto;padding:24px 16px">
        <h1 style="font-size:24px;font-weight:900;color:var(--text);margin:0 0 4px">{{ __('Two-Factor Authentication') }}</h1>
        <p style="font-size:14px;color:var(--text-muted);margin:0 0 32px">{{ __('Add an extra layer of security to your account') }}</p>

        @if (session('success'))
        <div style="padding:14px 18px;border-radius:12px;background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);margin-bottom:24px">
            <p style="font-size:13px;color:#22c55e;font-weight:600;margin:0">
                <i class="fas fa-check-circle" style="margin-right:6px"></i> {{ session('success') }}
            </p>
        </div>
        @endif

        {{-- Status Card --}}
        <div class="glass-card" style="padding:24px;margin-bottom:24px">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
                <div style="width:56px;height:56px;border-radius:16px;background:{{ $enabled ? 'linear-gradient(135deg,#22c55e,#16a34a)' : 'linear-gradient(135deg,#ef4444,#dc2626)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas {{ $enabled ? 'fa-shield-halved' : 'fa-shield-exclamation' }}" style="color:white;font-size:22px"></i>
                </div>
                <div>
                    <h2 style="font-size:16px;font-weight:800;color:var(--text);margin:0 0 4px">
                        {{ $enabled ? __('Enabled') : __('Disabled') }}
                    </h2>
                    <p style="font-size:13px;color:var(--text-muted);margin:0">
                        {{ $enabled ? __('Your account is protected with two-factor authentication.') : __('Your account is not protected. Enable 2FA for better security.') }}
                    </p>
                </div>
            </div>

            @if (!$enabled)
            <a href="{{ route('two-factor.setup') }}" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;border-radius:12px;background:var(--accent);color:white;font-size:14px;font-weight:700;text-decoration:none">
                <i class="fas fa-plus"></i> {{ __('Enable Two-Factor Authentication') }}
            </a>
            @else
            <form method="POST" action="{{ route('two-factor.destroy') }}" id="disable-2fa-form">
                @csrf
                @method('DELETE')
                <div style="margin-bottom:12px">
                    <label style="display:block;font-size:12px;font-weight:700;color:var(--text);margin-bottom:6px">{{ __('Enter your password to disable 2FA') }}</label>
                    <input type="password" name="password" required placeholder="{{ __('Password') }}" class="form-input" style="width:100%">
                    @error('password')<p style="font-size:12px;color:#ef4444;margin-top:4px">{{ $message }}</p>@enderror
                </div>
                <button type="submit" onclick="return confirm('{{ __('Are you sure you want to disable two-factor authentication?') }}')"
                        style="width:100%;padding:12px;border-radius:12px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#ef4444;font-size:14px;font-weight:700;cursor:pointer">
                    <i class="fas fa-shield-slash" style="margin-right:6px"></i> {{ __('Disable Two-Factor Authentication') }}
                </button>
            </form>
            @endif
        </div>

        <a href="{{ route('profile.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--text-muted);text-decoration:none">
            <i class="fas fa-arrow-left"></i> {{ __('Back to Profile') }}
        </a>
    </div>
</main>
@endsection
