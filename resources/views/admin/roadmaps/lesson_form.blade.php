@extends('layouts.admin')

@section('title', ($lesson ? __('Edit Lesson') : __('Create Lesson')) . ' - CodeMaster')
@section('header-title', $lesson ? __('Edit Lesson') : __('Create Lesson'))
@section('header-subtitle', $lesson ? __('Update lesson information') : __('Add a new lesson to') . ' ' . $node->title)

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.roadmap.lessons', $node->id) }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Roadmap Lessons') }}
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ $lesson ? route('admin.roadmap.lessons.update', $lesson->id) : route('admin.roadmap.lessons.store', $node->id) }}" method="POST">
            @csrf
            @if($lesson)
                @method('PUT')
            @endif

            <div class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $lesson->title ?? '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="video_url" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Video URL') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                    <input type="text" name="video_url" id="video_url" value="{{ old('video_url', $lesson->video_url ?? '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('video_url') border-red-500 @enderror">
                    @error('video_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                    <textarea name="description" id="description" rows="5" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $lesson->description ?? '') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="materials" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Materials') }} <span class="text-gray-400">(JSON array)</span></label>
                    <textarea name="materials" id="materials" rows="3" placeholder='["link1", "link2"]' class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('materials') border-red-500 @enderror">{{ old('materials', isset($lesson->materials) && is_array($lesson->materials) ? json_encode($lesson->materials, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : ($lesson->materials ?? '')) }}</textarea>
                    @error('materials')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="order_index" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Order') }}</label>
                    <input type="number" name="order_index" id="order_index" value="{{ old('order_index', $lesson->order_index ?? 0) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('order_index') border-red-500 @enderror" required>
                    @error('order_index')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.roadmap.lessons', $node->id) }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">{{ $lesson ? __('Update Lesson') : __('Create Lesson') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
