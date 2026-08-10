@extends('layouts.guest')

@section('title', __('Verify Email') . ' - CodeMaster')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ __('Verify Email') }}</h2>
    <p class="text-sm text-gray-500 mb-6">{{ __('A verification link has been sent to your email address') }}</p>

    @if (session('resent'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
            {{ __('A new verification link has been sent to your email address') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors duration-200 mb-4">
            {{ __('Resend Verification Link') }}
        </button>
    </form>

    <p class="text-center text-sm text-gray-500">
        <a href="{{ route('logout') }}" class="text-indigo-600 hover:text-indigo-500 font-medium"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </p>
</div>
@endsection
