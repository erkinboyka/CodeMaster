@extends('layouts.app')

@section('title', __('Leaderboard') . ' - ' . $contest->title . ' - CodeMaster')

@section('content')
<div class="bg-gradient-to-r from-indigo-500 to-purple-600 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center space-x-2 text-sm text-white/60 mb-4">
            <a href="{{ route('contests.index') }}" class="hover:text-white transition">{{ __('Contests') }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('contests.show', $contest->id) }}" class="hover:text-white transition">{{ $contest->title }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-white">{{ __('Leaderboard') }}</span>
        </nav>
        <h1 class="text-2xl font-bold text-white">{{ __('Leaderboard') }} - {{ $contest->title }}</h1>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">{{ __('User') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">{{ __('Solved') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">{{ __('Last Submit') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($submissions as $index => $entry)
                    <tr class="{{ $entry->user_id === Auth::id() ? 'bg-indigo-50' : 'hover:bg-gray-50' }} transition">
                        <td class="px-6 py-4">
                            <span class="w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold
                                {{ $index < 3 ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-500' }}">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    {{ strtoupper(substr($entry->user->name ?? '?', 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-800 {{ $entry->user_id === Auth::id() ? 'text-indigo-600' : '' }}">
                                    {{ $entry->user->name ?? 'Unknown' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-bold text-gray-800">{{ $entry->solved }}</span>
                            <span class="text-xs text-gray-400">/ {{ $contest->problems_count }}</span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-500">
                            {{ $entry->last_submit ? \Carbon\Carbon::parse($entry->last_submit)->diffForHumans() : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-trophy text-3xl mb-3 block"></i>
                            {{ __('No submissions yet') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
