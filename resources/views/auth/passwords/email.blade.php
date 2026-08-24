@extends('layouts.guest')

@section('title', __('Reset Password') . ' - CodeMaster')

@section('content')
<div>
    <h2 class="auth-title">{{ __('Reset Password') }}</h2>
    <p class="auth-subtitle">$ password --reset --email</p>

    @if (session('status'))
        <div class="auth-alert" style="border-color: color-mix(in srgb, #22c55e 25%, var(--border)); background: color-mix(in srgb, #22c55e 8%, var(--card)); color: #22c55e;">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if($errors->any())
    <div class="auth-alert">
        <i class="fas fa-exclamation-triangle"></i>
        @foreach($errors->all() as $error)
            <span>{{ $error }}</span>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf
        <div class="auth-field">
            <label class="auth-label">
                <span class="auth-label-prefix">></span> {{ __('Email') }}
            </label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon">@</span>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="auth-input" placeholder="you@codemaster.dev">
            </div>
        </div>

        <button type="submit" class="auth-submit">
            <span>{{ __('Send Reset Link') }}</span>
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>

    <p class="auth-footer-text" style="margin-top:20px">
        <a href="{{ route('login') }}" class="auth-link-bold">{{ __('Back to Login') }}</a>
    </p>
</div>
@endsection
