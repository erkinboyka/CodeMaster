@extends('layouts.admin')

@section('title', __('Edit Roadmap Node') . ' - CodeMaster')
@section('header-title', __('Edit Roadmap Node'))
@section('header-subtitle', __('Update roadmap node information'))

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.roadmaps') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Roadmaps') }}
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.roadmaps.update', $roadmap->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $roadmap->title) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Course') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                        <select name="course_id" id="course_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('course_id') border-red-500 @enderror">
                            <option value="">{{ __('No course') }}</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $roadmap->course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="roadmap_title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Roadmap Title') }}</label>
                        <input type="text" name="roadmap_title" id="roadmap_title" value="{{ old('roadmap_title', $roadmap->roadmap_title ?? 'Основной') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('roadmap_title') border-red-500 @enderror">
                        @error('roadmap_title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="topic" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Topic') }}</label>
                    <input type="text" name="topic" id="topic" value="{{ old('topic', $roadmap->topic) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('topic') border-red-500 @enderror">
                    @error('topic')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="materials" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Materials') }} <span class="text-gray-400">(JSON array)</span></label>
                    <textarea name="materials" id="materials" rows="3" placeholder='["link1", "link2"]' class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('materials') border-red-500 @enderror">{{ old('materials', is_array($roadmap->materials) ? json_encode($roadmap->materials, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : $roadmap->materials) }}</textarea>
                    @error('materials')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="x" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Position X') }}</label>
                        <input type="number" name="x" id="x" value="{{ old('x', $roadmap->x) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('x') border-red-500 @enderror" required>
                        @error('x')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="y" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Position Y') }}</label>
                        <input type="number" name="y" id="y" value="{{ old('y', $roadmap->y) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('y') border-red-500 @enderror" required>
                        @error('y')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="deps" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Dependencies') }} <span class="text-gray-400">(JSON array of node IDs)</span></label>
                    <textarea name="deps" id="deps" rows="2" placeholder='[1, 2, 3]' class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('deps') border-red-500 @enderror">{{ old('deps', is_array($roadmap->deps) ? json_encode($roadmap->deps, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : $roadmap->deps) }}</textarea>
                    @error('deps')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_exam" id="is_exam" value="1" {{ old('is_exam', $roadmap->is_exam) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 bg-gray-50 border-gray-200 rounded focus:ring-2 focus:ring-indigo-500">
                    <label for="is_exam" class="ml-2 text-sm font-medium text-gray-700">{{ __('Is Exam Node') }}</label>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.roadmaps') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">{{ __('Update Roadmap Node') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
