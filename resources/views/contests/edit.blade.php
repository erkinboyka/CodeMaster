@extends('layouts.app')

@section('title', __('Edit Contest') . ' - CodeMaster')

@section('content')
<div class="bg-gradient-to-r from-indigo-500 to-purple-600 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center space-x-2 text-sm text-white/60 mb-4">
            <a href="{{ route('contests.index') }}" class="hover:text-white transition">{{ __('Contests') }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('contests.show', $contest->id) }}" class="hover:text-white transition">{{ $contest->title }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-white">{{ __('Edit') }}</span>
        </nav>
        <h1 class="text-2xl font-bold text-white">{{ __('Edit Contest') }}</h1>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <form action="{{ route('contests.update', $contest->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }} *</label>
                    <input type="text" name="title" value="{{ old('title', $contest->title) }}" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('title') border-red-500 @enderror">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('description', $contest->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Difficulty') }} *</label>
                        <select name="difficulty" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="easy" {{ old('difficulty', $contest->difficulty) === 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ old('difficulty', $contest->difficulty) === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ old('difficulty', $contest->difficulty) === 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }} *</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="draft" {{ old('status', $contest->status) === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                            <option value="active" {{ old('status', $contest->status) === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="finished" {{ old('status', $contest->status) === 'finished' ? 'selected' : '' }}>{{ __('Finished') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Start Time') }}</label>
                        <input type="datetime-local" name="start_time"
                            value="{{ old('start_time', $contest->start_time ? $contest->start_time->format('Y-m-d\TH:i') : '') }}"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('End Time') }}</label>
                        <input type="datetime-local" name="end_time"
                            value="{{ old('end_time', $contest->end_time ? $contest->end_time->format('Y-m-d\TH:i') : '') }}"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Time Limit (min)') }} *</label>
                        <input type="number" name="time_limit" value="{{ old('time_limit', $contest->time_limit) }}" min="1"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Max Participants') }}</label>
                    <input type="number" name="max_participants" value="{{ old('max_participants', $contest->max_participants) }}" min="1"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('contests.show', $contest->id) }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-save mr-1"></i> {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
