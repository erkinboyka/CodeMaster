@extends('layouts.admin')

@section('title', ($task ? __('Edit Task') : __('Create Task')) . ' - CodeMaster')
@section('header-title', $task ? __('Edit Task') : __('Create Task'))

@section('content')
<div class="max-w-4xl">
    <a href="{{ route('admin.contests.tasks', $contest->id) }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Tasks') }}
    </a>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ $task ? route('admin.contests.tasks.update', [$contest->id, $task->id]) : route('admin.contests.tasks.store', $contest->id) }}" method="POST">
            @csrf
            @if($task) @method('PUT') @endif

            <div class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }}</label>
                        <input type="text" name="title" value="{{ old('title', $task->title ?? '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror" required>
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Language') }}</label>
                        <select name="language" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            @foreach(['python','cpp','c','java','csharp','javascript','go','rust','php'] as $lang)
                                <option value="{{ $lang }}" {{ old('language', $task->language ?? 'python') === $lang ? 'selected' : '' }}>{{ strtoupper($lang) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Difficulty') }}</label>
                        <select name="difficulty" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            @foreach(['easy','medium','hard'] as $d)
                                <option value="{{ $d }}" {{ old('difficulty', $task->difficulty ?? 'medium') === $d ? 'selected' : '' }}>{{ __('difficulty_' . $d) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Points') }}</label>
                        <input type="number" name="points" value="{{ old('points', $task->points ?? 100) }}" min="1" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Order') }}</label>
                        <input type="number" name="order_num" value="{{ old('order_num', $task->order_num ?? 0) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Time Limit (sec)') }}</label>
                        <input type="number" name="time_limit" value="{{ old('time_limit', $task->time_limit ?? 2) }}" min="1" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Memory Limit (MB)') }}</label>
                        <input type="number" name="memory_limit" value="{{ old('memory_limit', $task->memory_limit ?? 256) }}" min="64" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $task->description ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Input Example') }}</label>
                        <textarea name="input_example" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">{{ old('input_example', $task->input_example ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Output Example') }}</label>
                        <textarea name="output_example" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">{{ old('output_example', $task->output_example ?? '') }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Constraints') }}</label>
                    <textarea name="constraints" rows="2" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('constraints', $task->constraints ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Starter Code') }}</label>
                    <textarea name="starter_code" rows="5" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">{{ old('starter_code', $task->starter_code ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Tests JSON') }}</label>
                    <textarea name="tests_json" rows="6" placeholder='[{"input":"1 2\n","output":"3\n"}]' class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">{{ is_array(old('tests_json', $task->tests_json ?? '')) ? json_encode(old('tests_json', $task->tests_json ?? ''), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : old('tests_json', '') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.contests.tasks', $contest->id) }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">{{ $task ? __('Update Task') : __('Create Task') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
