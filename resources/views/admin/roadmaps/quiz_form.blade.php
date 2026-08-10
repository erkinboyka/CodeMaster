@extends('layouts.admin')

@section('title', ($quiz ? __('Edit Quiz Question') : __('Create Quiz Question')) . ' - CodeMaster')
@section('header-title', $quiz ? __('Edit Quiz Question') : __('Create Quiz Question'))
@section('header-subtitle', $quiz ? __('Update quiz question information') : __('Add a new quiz question to') . ' ' . $node->title)

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.roadmap.quizzes', $node->id) }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Roadmap Quizzes') }}
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ $quiz ? route('admin.roadmap.quizzes.update', $quiz->id) : route('admin.roadmap.quizzes.store', $node->id) }}" method="POST">
            @csrf
            @if($quiz)
                @method('PUT')
            @endif

            <div class="space-y-5">
                <div>
                    <label for="question" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Question') }}</label>
                    <textarea name="question" id="question" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('question') border-red-500 @enderror" required>{{ old('question', $quiz->question ?? '') }}</textarea>
                    @error('question')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="options" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Options') }} <span class="text-gray-400">(JSON array)</span></label>
                    <textarea name="options" id="options" rows="3" placeholder='["A", "B", "C", "D"]' class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('options') border-red-500 @enderror">{{ old('options', isset($quiz->options) && is_array($quiz->options) ? json_encode($quiz->options, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : ($quiz->options ?? '')) }}</textarea>
                    @error('options')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="correct_answer" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Correct Answer') }}</label>
                    <input type="text" name="correct_answer" id="correct_answer" value="{{ old('correct_answer', $quiz->correct_answer ?? '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('correct_answer') border-red-500 @enderror" required>
                    @error('correct_answer')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.roadmap.quizzes', $node->id) }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">{{ $quiz ? __('Update Quiz Question') : __('Create Quiz Question') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
