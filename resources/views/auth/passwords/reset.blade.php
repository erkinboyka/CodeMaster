@extends('layouts.guest')

@section('title', __('Reset Password') . ' - CodeMaster')

@section('content')
<div x-data="{ showPw: false }">
    <h2 class="auth-title">{{ __('Reset Password') }}</h2>
    <p class="auth-subtitle">$ password --set <span class="text-[var(--accent)]">new</span></p>

    @if($errors->any())
    <div class="auth-alert">
        <i class="fas fa-exclamation-triangle"></i>
        @foreach($errors->all() as $error)
            <span>{{ $error }}</span>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="auth-form">
        @csrf
        <input type="hidden" name="token" value="{{ $token ?? '' }}">

        <div class="auth-field">
            <label class="auth-label">
                <span class="auth-label-prefix">></span> {{ __('Email') }}
            </label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon">@</span>
                <input type="email" name="email" value="{{ old('email', $email ?? '') }}" required
                    class="auth-input" placeholder="you@codemaster.dev">
            </div>
        </div>

        <div class="auth-field">
            <label class="auth-label">
                <span class="auth-label-prefix">></span> {{ __('New Password') }}
            </label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                <input :type="showPw ? 'text' : 'password'" name="password" required
                    class="auth-input" placeholder="••••••••">
                <button type="button" @click="showPw = !showPw" class="auth-input-action">
                    <i class="fas" :class="showPw ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
        </div>

        <div class="auth-field">
            <label class="auth-label">
                <span class="auth-label-prefix">></span> {{ __('Confirm Password') }}
            </label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon"><i class="fas fa-shield-alt"></i></span>
                <input type="password" name="password_confirmation" required
                    class="auth-input" placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="auth-submit">
            <span>{{ __('Reset Password') }}</span>
            <i class="fas fa-key"></i>
        </button>
    </form>

    <p class="auth-footer-text" style="margin-top:20px">
        <a href="{{ route('login') }}" class="auth-link-bold">{{ __('Back to Login') }}</a>
    </p>
</div>
@endsection
