@extends('layouts.admin')

@section('title', __('Create Lesson') . ' - CodeMaster')
@section('header-title', __('Create Lesson'))
@section('header-subtitle', __('Add a new lesson to') . ' ' . $course->title)

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.courses.edit', $course->id) }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Course') }}
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.lessons.store', $course->id) }}" method="POST">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Type') }}</label>
                        <select name="type" id="type" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('type') border-red-500 @enderror" required>
                            <option value="">{{ __('Select type') }}</option>
                            <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>{{ __('Video') }}</option>
                            <option value="article" {{ old('type') === 'article' ? 'selected' : '' }}>{{ __('Article') }}</option>
                            <option value="quiz" {{ old('type') === 'quiz' ? 'selected' : '' }}>{{ __('Quiz') }}</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="order_num" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Order') }}</label>
                        <input type="number" name="order_num" id="order_num" value="{{ old('order_num', 0) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('order_num') border-red-500 @enderror" required>
                        @error('order_num')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Content') }}</label>
                    <textarea name="content" id="content" rows="8" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('content') border-red-500 @enderror" required>{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                    <textarea name="description" id="description" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="video_url" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Video URL') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                        <input type="text" name="video_url" id="video_url" value="{{ old('video_url') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('video_url') border-red-500 @enderror">
                        @error('video_url')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="audio_url" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Audio URL') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                        <input type="text" name="audio_url" id="audio_url" value="{{ old('audio_url') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('audio_url') border-red-500 @enderror">
                        @error('audio_url')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="presentation_url" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Presentation URL') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                        <input type="text" name="presentation_url" id="presentation_url" value="{{ old('presentation_url') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('presentation_url') border-red-500 @enderror">
                        @error('presentation_url')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Duration (minutes)') }}</label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 0) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('duration_minutes') border-red-500 @enderror">
                        @error('duration_minutes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Difficulty') }}</label>
                        <select name="difficulty" id="difficulty" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('difficulty') border-red-500 @enderror">
                            <option value="easy" {{ old('difficulty') === 'easy' ? 'selected' : '' }}>{{ __('Easy') }}</option>
                            <option value="medium" {{ old('difficulty') === 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                            <option value="hard" {{ old('difficulty') === 'hard' ? 'selected' : '' }}>{{ __('Hard') }}</option>
                        </select>
                        @error('difficulty')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="module" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Module') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                        <input type="text" name="module" id="module" value="{{ old('module') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('module') border-red-500 @enderror">
                        @error('module')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="materials_title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Materials Title') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                        <input type="text" name="materials_title" id="materials_title" value="{{ old('materials_title') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('materials_title') border-red-500 @enderror">
                        @error('materials_title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="materials_url" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Materials URL') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                        <input type="text" name="materials_url" id="materials_url" value="{{ old('materials_url') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('materials_url') border-red-500 @enderror">
                        @error('materials_url')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.courses.edit', $course->id) }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">{{ __('Create Lesson') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
