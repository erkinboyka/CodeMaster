@extends('layouts.admin')

@section('title', __('Create Course') . ' - CodeMaster')
@section('header-title', __('Create Course'))
@section('header-subtitle', __('Add a new course to the platform'))

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.courses') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Courses') }}
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.courses.store') }}" method="POST">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="instructor" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Instructor') }}</label>
                    <input type="text" name="instructor" id="instructor" value="{{ old('instructor') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('instructor') border-red-500 @enderror" required>
                    @error('instructor')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                    <textarea name="description" id="description" rows="5" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror" required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="image_url" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Image URL') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                        <input type="text" name="image_url" id="image_url" value="{{ old('image_url') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('image_url') border-red-500 @enderror">
                        @error('image_url')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Level') }}</label>
                        <select name="level" id="level" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('level') border-red-500 @enderror" required>
                            <option value="">{{ __('Select level') }}</option>
                            <option value="Beginner" {{ old('level') === 'Beginner' ? 'selected' : '' }}>{{ __('Beginner') }}</option>
                            <option value="Intermediate" {{ old('level') === 'Intermediate' ? 'selected' : '' }}>{{ __('Intermediate') }}</option>
                            <option value="Advanced" {{ old('level') === 'Advanced' ? 'selected' : '' }}>{{ __('Advanced') }}</option>
                        </select>
                        @error('level')
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
                <a href="{{ route('admin.courses') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">{{ __('Create Course') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
