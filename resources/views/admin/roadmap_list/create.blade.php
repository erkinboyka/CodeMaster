@extends('layouts.admin')

@section('title', __('Create Roadmap') . ' - CodeMaster')
@section('header-title', __('Create Roadmap Entry'))

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.roadmap-list') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
    </a>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.roadmap-list.store') }}" method="POST">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }}</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror" required>
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.roadmap-list') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">{{ __('Create') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
