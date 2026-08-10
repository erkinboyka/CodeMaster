@extends('layouts.app')

@section('title', __('Exam Result') . ' - ' . $course->title)

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <a href="{{ route('courses.show', $course->id) }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Course') }}
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center mb-8">
        @if($passed)
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-green-600 text-3xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Congratulations! You Passed!') }}</h1>
        @else
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-times text-red-600 text-3xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Exam Not Passed') }}</h1>
        @endif

        <div class="text-5xl font-bold mt-4 mb-2 {{ $passed ? 'text-green-600' : 'text-red-600' }}">{{ $score }}%</div>
        <p class="text-gray-500">{{ $correct }}/{{ $total }} {{ __('correct answers') }}</p>
        <p class="text-sm text-gray-400 mt-2">{{ __('Required') }}: {{ $exam->pass_percent }}%</p>

        @if($passed && $certificate)
        <div class="mt-6 p-4 bg-indigo-50 rounded-xl inline-block">
            <p class="text-sm text-indigo-700"><i class="fas fa-certificate mr-2"></i>{{ __('Certificate earned!') }}</p>
            <a href="{{ route('certificate.show', $certificate->cert_hash) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">{{ __('View Certificate') }}</a>
        </div>
        @endif

        @if(!$passed)
        <a href="{{ route('courses.exam', $course->id) }}" class="inline-block mt-6 px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition">
            {{ __('Try Again') }}
        </a>
        @endif
    </div>

    <h2 class="text-lg font-bold text-gray-900 mb-4">{{ __('Detailed Results') }}</h2>
    <div class="space-y-4">
        @foreach($results as $index => $result)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-start space-x-4">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 {{ $result['is_correct'] ? 'bg-green-100' : 'bg-red-100' }}">
                    <i class="fas {{ $result['is_correct'] ? 'fa-check text-green-600' : 'fa-times text-red-600' }} text-sm"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-medium text-gray-900 mb-3">{{ $index + 1 }}. {{ $result['question'] }}</h3>
                    <div class="space-y-2">
                        @foreach($result['options'] as $optIndex => $option)
                        <div class="flex items-center space-x-3 text-sm p-2 rounded-lg
                            @if($optIndex == $result['correct_answer']) bg-green-50 text-green-800
                            @elseif($optIndex == $result['user_answer'] && !$result['is_correct']) bg-red-50 text-red-800
                            @else text-gray-600 @endif">
                            @if($optIndex == $result['correct_answer'])
                                <i class="fas fa-check-circle text-green-500"></i>
                            @elseif($optIndex == $result['user_answer'] && !$result['is_correct'])
                                <i class="fas fa-times-circle text-red-500"></i>
                            @else
                                <i class="far fa-circle text-gray-400"></i>
                            @endif
                            <span>{{ $option }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
