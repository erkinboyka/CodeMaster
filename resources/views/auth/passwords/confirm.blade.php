@extends('layouts.guest')

@section('title', __('Confirm Password') . ' - CodeMaster')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ __('Confirm Password') }}</h2>
    <p class="text-sm text-gray-500 mb-6">{{ __('Please confirm your password before continuing') }}</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Password') }}</label>
            <div class="relative">
                <input type="password" name="password" required class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="••••••••">
                <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            @error('password')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors duration-200">
            {{ __('Confirm') }}
        </button>
    </form>
</div>
@endsection
