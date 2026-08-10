@extends('layouts.app')

@section('title', __('Certificate') . ' - CodeMaster')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-8 text-center text-white">
            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-certificate text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold mb-2">{{ __('Certificate of Completion') }}</h1>
            <p class="text-white/80">{{ __('This certifies that') }}</p>
        </div>

        <div class="p-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $certificate->user->name }}</h2>
            <p class="text-gray-500 mb-6">{{ __('has successfully completed the course') }}</p>

            <h3 class="text-xl font-bold text-indigo-600 mb-6">{{ $certificate->course->title }}</h3>

            <div class="flex items-center justify-center gap-8 text-sm text-gray-500 mb-8">
                <div>
                    <p class="text-xs text-gray-400">{{ __('Issue Date') }}</p>
                    <p class="font-semibold">{{ $certificate->issue_date }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">{{ __('Certificate ID') }}</p>
                    <p class="font-semibold font-mono">{{ $certificate->cert_hash }}</p>
                </div>
                @if($certificate->course->instructor)
                <div>
                    <p class="text-xs text-gray-400">{{ __('Instructor') }}</p>
                    <p class="font-semibold">{{ $certificate->course->instructor }}</p>
                </div>
                @endif
            </div>

            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('certificate.download', $certificate->cert_hash) }}" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
                    <i class="fas fa-download mr-2"></i>{{ __('Download PDF') }}
                </a>
                <a href="{{ route('courses.show', $certificate->course_id) }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">
                    {{ __('View Course') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
