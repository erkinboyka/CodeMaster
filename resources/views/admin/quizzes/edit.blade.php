@extends('layouts.admin')

@section('title', __('Edit Quiz') . ' - CodeMaster')
@section('header-title', __('Edit Quiz'))
@section('header-subtitle', __('Update quiz information'))

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.courses.edit', $quiz->lesson->course_id) }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Course') }}
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <div>
                    <label for="question_text" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Question Text') }}</label>
                    <textarea name="question_text" id="question_text" rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('question_text') border-red-500 @enderror" required>{{ old('question_text', $quiz->question_text) }}</textarea>
                    @error('question_text')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="options_json" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Options (JSON Array)') }}</label>
                    <textarea name="options_json" id="options_json" rows="3" placeholder='["Option 1", "Option 2", "Option 3", "Option 4"]' class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('options_json') border-red-500 @enderror" required>{{ old('options_json', $quiz->options_json) }}</textarea>
                    @error('options_json')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="correct_option" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Correct Option (index, starting from 0)') }}</label>
                    <input type="number" name="correct_option" id="correct_option" value="{{ old('correct_option', $quiz->correct_option) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('correct_option') border-red-500 @enderror" required>
                    @error('correct_option')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="explanation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Explanation') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                    <textarea name="explanation" id="explanation" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('explanation') border-red-500 @enderror">{{ old('explanation', $quiz->explanation) }}</textarea>
                    @error('explanation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="order_num" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Order') }}</label>
                    <input type="number" name="order_num" id="order_num" value="{{ old('order_num', $quiz->order_num) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('order_num') border-red-500 @enderror" required>
                    @error('order_num')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.courses.edit', $quiz->lesson->course_id) }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">{{ __('Update Quiz') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
