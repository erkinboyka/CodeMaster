@extends('layouts.guest')

@section('title', __('Login') . ' - CodeMaster')

@section('content')
<div x-data="loginForm()" class="space-y-6">
    <div>
        <h2 class="auth-title">{{ __('Welcome back') }}</h2>
        <p class="auth-subtitle">$ auth --login</p>
    </div>

    @if($errors->any())
    <div class="auth-alert">
        <i class="fas fa-exclamation-triangle"></i>
        @foreach($errors->all() as $error)
            <span>{{ $error }}</span>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf
        <div class="auth-field">
            <label class="auth-label">
                <span class="auth-label-prefix">></span> {{ __('Email') }}
            </label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon">@</span>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="auth-input" placeholder="user@codemaster.dev" autocomplete="email">
            </div>
        </div>

        <div class="auth-field">
            <label class="auth-label">
                <span class="auth-label-prefix">></span> {{ __('Password') }}
            </label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                <input :type="showPw ? 'text' : 'password'" name="password" required
                    class="auth-input" placeholder="••••••••" autocomplete="current-password">
                <button type="button" @click="showPw = !showPw" class="auth-input-action">
                    <i class="fas" :class="showPw ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <label class="auth-checkbox">
                <input type="checkbox" name="remember" class="auth-checkbox-input">
                <span>{{ __('Remember me') }}</span>
            </label>
            <a href="{{ route('password.request') }}" class="auth-link">
                {{ __('Forgot password?') }}
            </a>
        </div>

        <button type="submit" class="auth-submit">
            <span>{{ __('Sign In') }}</span>
            <i class="fas fa-terminal"></i>
        </button>
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
    </form>

    <div class="auth-divider">
        <span>{{ __('or continue with') }}</span>
    </div>

    <div>
        <a href="{{ route('auth.google') }}" class="auth-social-btn" style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:10px;border:1px solid var(--border);border-radius:8px;background:var(--bg-2);color:var(--text);font-size:13px;font-weight:600;text-decoration:none;transition:all .15s" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">
            <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
            Google
        </a>
    </div>

    <p class="auth-footer-text">
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="auth-link-bold">{{ __('Register') }}</a>
    </p>
</div>
@endsection

@push('scripts')
<script>
function loginForm() {
    return {
        showPw: false
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var script = document.createElement('script');
    script.src = 'https://www.google.com/recaptcha/api.js?render={{ config("services.recaptcha.site_key") }}';
    script.onload = function() {
        grecaptcha.ready(function() {
            grecaptcha.execute('{{ config("services.recaptcha.site_key") }}', {action: 'login'}).then(function(token) {
                var el = document.getElementById('g-recaptcha-response');
                if (el) el.value = token;
            });
        });
    };
    document.head.appendChild(script);

    document.querySelector('.auth-form').addEventListener('submit', function(e) {
        if (typeof grecaptcha !== 'undefined') {
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config("services.recaptcha.site_key") }}', {action: 'login'}).then(function(token) {
                    var el = document.getElementById('g-recaptcha-response');
                    if (el) el.value = token;
                    e.target.submit();
                });
            });
            e.preventDefault();
        }
    });
});
</script>
@endpush
