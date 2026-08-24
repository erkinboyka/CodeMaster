@extends('layouts.guest')

@section('title', __('Confirm Password') . ' - CodeMaster')

@section('content')
<div>
    <h2 class="auth-title">{{ __('Confirm Password') }}</h2>
    <p class="auth-subtitle">$ sudo confirm --password</p>

    @if($errors->any())
    <div class="auth-alert">
        <i class="fas fa-exclamation-triangle"></i>
        @foreach($errors->all() as $error)
            <span>{{ $error }}</span>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}" class="auth-form">
        @csrf

        <div class="auth-field">
            <label class="auth-label">
                <span class="auth-label-prefix">></span> {{ __('Password') }}
            </label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" required
                    class="auth-input" placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="auth-submit">
            <span>{{ __('Confirm') }}</span>
            <i class="fas fa-check-circle"></i>
        </button>
    </form>
</div>
@endsection
