@extends('layouts.admin')

@section('title', __('Edit Contest') . ' - CodeMaster')
@section('header-title', __('Edit Contest'))

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.contests') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Contests') }}
    </a>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.contests.update', $contest->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }}</label>
                    <input type="text" name="title" value="{{ old('title', $contest->title) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror" required>
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $contest->description) }}</textarea>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Difficulty') }}</label>
                        <select name="difficulty" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            @foreach(['easy','medium','hard'] as $d)
                                <option value="{{ $d }}" {{ old('difficulty', $contest->difficulty) === $d ? 'selected' : '' }}>{{ __('difficulty_' . $d) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                        <select name="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            @foreach(['draft','active','finished'] as $s)
                                <option value="{{ $s }}" {{ old('status', $contest->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Time Limit (min)') }}</label>
                        <input type="number" name="time_limit" value="{{ old('time_limit', $contest->time_limit) }}" min="1" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Start Time') }}</label>
                        <input type="datetime-local" name="start_time" value="{{ old('start_time', $contest->start_time?->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('End Time') }}</label>
                        <input type="datetime-local" name="end_time" value="{{ old('end_time', $contest->end_time?->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Max Participants') }}</label>
                    <input type="number" name="max_participants" value="{{ old('max_participants', $contest->max_participants) }}" min="1" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.contests') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">{{ __('Update Contest') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
