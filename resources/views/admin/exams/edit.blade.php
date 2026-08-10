@extends('layouts.admin')

@section('title', __('Edit Exam') . ' - CodeMaster')
@section('header-title', __('Edit Exam'))
@section('header-subtitle', __('Update exam information'))

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.exams') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Exams') }}
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.exams.update', $exam->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Course') }}</label>
                    <select name="course_id" id="course_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('course_id') border-red-500 @enderror" required>
                        <option value="">{{ __('Select course') }}</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $exam->course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="time_limit_minutes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Time Limit (minutes)') }}</label>
                        <input type="number" name="time_limit_minutes" id="time_limit_minutes" value="{{ old('time_limit_minutes', $exam->time_limit_minutes) }}" min="1" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('time_limit_minutes') border-red-500 @enderror" required>
                        @error('time_limit_minutes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pass_percent" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Pass Percent (%)') }}</label>
                        <input type="number" name="pass_percent" id="pass_percent" value="{{ old('pass_percent', $exam->pass_percent) }}" min="0" max="100" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('pass_percent') border-red-500 @enderror" required>
                        @error('pass_percent')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="questions_per_exam" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Questions Per Exam') }}</label>
                    <input type="number" name="questions_per_exam" id="questions_per_exam" value="{{ old('questions_per_exam', $exam->questions_per_exam) }}" min="1" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('questions_per_exam') border-red-500 @enderror" required>
                    @error('questions_per_exam')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" name="shuffle_questions" id="shuffle_questions" value="1" {{ old('shuffle_questions', $exam->shuffle_questions) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="shuffle_questions" class="text-sm font-medium text-gray-700">{{ __('Shuffle Questions') }}</label>
                    </div>

                    <div class="flex items-center space-x-3">
                        <input type="checkbox" name="shuffle_options" id="shuffle_options" value="1" {{ old('shuffle_options', $exam->shuffle_options) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="shuffle_options" class="text-sm font-medium text-gray-700">{{ __('Shuffle Options') }}</label>
                    </div>
                </div>

                <div>
                    <label for="exam_json" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Exam JSON') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                    <textarea name="exam_json" id="exam_json" rows="6" placeholder='[{"question":"...","options":["a","b","c","d"],"correct":0}]' class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('exam_json') border-red-500 @enderror">{{ old('exam_json', $exam->exam_json) }}</textarea>
                    @error('exam_json')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="question_bank_json" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Question Bank JSON') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                    <textarea name="question_bank_json" id="question_bank_json" rows="6" placeholder='[{"question":"...","options":["a","b","c","d"],"correct":0}]' class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('question_bank_json') border-red-500 @enderror">{{ old('question_bank_json', $exam->question_bank_json) }}</textarea>
                    @error('question_bank_json')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.exams') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">{{ __('Update Exam') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
