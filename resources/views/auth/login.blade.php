@extends('layouts.guest')

@section('title', __('Login') . ' - CodeMaster')

@section('content')
<div x-data="{ tab: 'login' }">
    <div class="flex bg-gray-100 rounded-lg p-1 mb-6">
        <button @click="tab = 'login'" :class="tab === 'login' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-2.5 text-sm font-medium rounded-md transition-all duration-200">{{ __('Login') }}</button>
        <button @click="tab = 'register'; window.location='{{ route('register') }}'" :class="tab === 'register' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-2.5 text-sm font-medium rounded-md transition-all duration-200">{{ __('Register') }}</button>
    </div>

    <div x-show="tab === 'login'">
        <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ __('Welcome back') }}</h2>
        <p class="text-sm text-gray-500 mb-6">{{ __('Sign in to your account') }}</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Email') }}</label>
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="you@example.com">
                    <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                @error('email')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Password') }}</label>
                <div class="relative" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'" name="password" required class="w-full pl-10 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="••••••••">
                    <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <button type="button" @click="show = !show" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @error('password')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">{{ __('Forgot password?') }}</a>
            </div>

            <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 shadow-lg shadow-indigo-200 transition-all duration-300">
                {{ __('Sign In') }}
            </button>
        </form>

        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
            <div class="relative flex justify-center text-sm"><span class="px-3 bg-white text-gray-400">{{ __('or continue with') }}</span></div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <button class="flex items-center justify-center space-x-2 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                <i class="fab fa-google text-red-500"></i>
                <span>Google</span>
            </button>
            <button class="flex items-center justify-center space-x-2 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                <i class="fab fa-github text-gray-800"></i>
                <span>GitHub</span>
            </button>
        </div>
    </div>
</div>
@endsection
