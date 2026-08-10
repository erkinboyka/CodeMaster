@extends('layouts.guest')

@section('title', __('Reset Password') . ' - CodeMaster')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ __('Reset Password') }}</h2>
    <p class="text-sm text-gray-500 mb-6">{{ __('Enter your new password below') }}</p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token ?? '' }}">

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Email') }}</label>
            <div class="relative">
                <input type="email" name="email" value="{{ old('email', $email ?? '') }}" required class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="you@example.com">
                <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            @error('email')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Password') }}</label>
            <div class="relative">
                <input type="password" name="password" required class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="••••••••">
                <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            @error('password')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Confirm Password') }}</label>
            <div class="relative">
                <input type="password" name="password_confirmation" required class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="••••••••">
                <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors duration-200">
            {{ __('Reset Password') }}
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 font-medium">{{ __('Back to Login') }}</a>
    </p>
</div>
@endsection
