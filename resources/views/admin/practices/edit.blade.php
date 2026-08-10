@extends('layouts.admin')

@section('title', __('Edit Practice') . ' - CodeMaster')
@section('header-title', __('Edit Practice'))
@section('header-subtitle', __('Update practice task information'))

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.courses.edit', $practice->lesson->course_id) }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Course') }}
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.practices.update', $practice->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $practice->title) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Language') }}</label>
                        <input type="text" name="language" id="language" value="{{ old('language', $practice->language) }}" placeholder="e.g. javascript, python, php" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('language') border-red-500 @enderror" required>
                        @error('language')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Difficulty') }}</label>
                        <select name="difficulty" id="difficulty" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('difficulty') border-red-500 @enderror" required>
                            <option value="">{{ __('Select difficulty') }}</option>
                            <option value="easy" {{ old('difficulty', $practice->difficulty) === 'easy' ? 'selected' : '' }}>{{ __('Easy') }}</option>
                            <option value="medium" {{ old('difficulty', $practice->difficulty) === 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                            <option value="hard" {{ old('difficulty', $practice->difficulty) === 'hard' ? 'selected' : '' }}>{{ __('Hard') }}</option>
                        </select>
                        @error('difficulty')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="prompt" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Prompt') }}</label>
                    <textarea name="prompt" id="prompt" rows="5" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('prompt') border-red-500 @enderror" required>{{ old('prompt', $practice->prompt) }}</textarea>
                    @error('prompt')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="starter_code" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Starter Code') }}</label>
                    <textarea name="starter_code" id="starter_code" rows="8" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('starter_code') border-red-500 @enderror">{{ old('starter_code', $practice->starter_code) }}</textarea>
                    @error('starter_code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tests_json" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Tests (JSON)') }}</label>
                    <textarea name="tests_json" id="tests_json" rows="6" placeholder='[{"input":"...","expected":"..."}]' class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('tests_json') border-red-500 @enderror">{{ old('tests_json', $practice->tests_json) }}</textarea>
                    @error('tests_json')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="expected_output" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Expected Output') }}</label>
                        <textarea name="expected_output" id="expected_output" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('expected_output') border-red-500 @enderror">{{ old('expected_output', $practice->expected_output) }}</textarea>
                        @error('expected_output')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="time_limit" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Time Limit (seconds)') }}</label>
                        <input type="number" name="time_limit" id="time_limit" value="{{ old('time_limit', $practice->time_limit) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('time_limit') border-red-500 @enderror">
                        @error('time_limit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="hints" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Hints') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                    <textarea name="hints" id="hints" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('hints') border-red-500 @enderror">{{ old('hints', $practice->hints) }}</textarea>
                    @error('hints')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center space-x-3">
                    <input type="checkbox" name="is_required" id="is_required" value="1" {{ old('is_required', $practice->is_required) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="is_required" class="text-sm font-medium text-gray-700">{{ __('Required Practice') }}</label>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.courses.edit', $practice->lesson->course_id) }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">{{ __('Update Practice') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
