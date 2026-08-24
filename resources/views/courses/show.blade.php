@extends('layouts.app')

@section('title', ($course->title ?? 'Course') . ' - CodeMaster')

@section('content')
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8 items-start">
            <div class="lg:col-span-2">
                <nav class="flex items-center space-x-2 text-sm text-white/60 mb-4">
                    <a href="{{ route('courses.index') }}" class="hover:text-white transition">{{ __('Courses') }}</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="text-white">{{ $course->title }}</span>
                </nav>
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-3 reveal-up" data-delay="0">{{ $course->title }}</h1>
                <p class="text-white/80 mb-4 reveal-up" data-delay="0.1">{{ $course->description }}</p>
                <div class="flex flex-wrap items-center gap-4 text-sm text-white/70 reveal-up" data-delay="0.2">
                    @if($instructorUser)
                    <a href="{{ route('profile.show', $instructorUser->id) }}" class="hover:text-white transition underline underline-offset-2 decoration-white/30 hover:decoration-white"><i class="fas fa-user-tie mr-1"></i>{{ $course->instructor }}</a>
                    @else
                    <span><i class="fas fa-user-tie mr-1"></i>{{ $course->instructor }}</span>
                    @endif
                    <span><i class="fas fa-layer-group mr-1"></i>{{ $course->category }}</span>
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-medium">{{ __('courses_level_' . mb_strtolower($course->level)) }}</span>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-2xl reveal-right" data-delay="0.3">
                <div class="aspect-video bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl mb-4 flex items-center justify-center">
                    <button class="w-16 h-16 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition shadow-xl">
                        <i class="fas fa-book-open text-indigo-600 text-xl ml-1"></i>
                    </button>
                </div>
                <div class="space-y-3 mb-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">{{ __('Lessons') }}</span>
                        <span class="font-semibold text-gray-800">{{ $totalLessons }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">{{ __('Modules') }}</span>
                        <span class="font-semibold text-gray-800">{{ $modules->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">{{ __('Level') }}</span>
                        <span class="font-semibold text-gray-800">{{ __('courses_level_' . mb_strtolower($course->level)) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">{{ __('Certificate') }}</span>
                        <span class="font-semibold text-green-600"><i class="fas fa-check-circle mr-1"></i>{{ __('Included') }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ __('Progress') }}</span>
                        <span class="font-semibold text-indigo-600">{{ $percent }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all" style="width: {{ $percent }}%"></div>
                    </div>
                </div>

                @if($certificate)
                <a href="{{ route('certificate.show', $certificate->cert_hash) }}" class="block w-full py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition text-center">
                    <i class="fas fa-certificate mr-2"></i>{{ __('View Certificate') }}
                </a>
                @elseif($exam && $percent >= 80)
                <a href="{{ route('courses.exam', $course->id) }}" class="block w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 shadow-lg transition-all duration-300 text-center">
                    <i class="fas fa-pen mr-2"></i>{{ __('Take Final Exam') }} ({{ $exam->questions_per_exam }} {{ __('questions') }})
                </a>
                @else
                <a href="{{ $nextLesson ? route('courses.lesson', [$course->id, $nextLesson->id]) : '#' }}" class="block w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 shadow-lg transition-all duration-300 text-center">
                    <i class="fas fa-book-open mr-2"></i>{{ __('Continue Learning') }}
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2" x-data="{ openModule: null }">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <h2 class="text-xl font-bold text-gray-900 p-6 pb-0">{{ __('Course Content') }}</h2>
                <p class="text-sm text-gray-500 px-6 mb-4">{{ $totalLessons }} {{ __('lessons') }} • {{ $completedCount = count($completedLessonIds) }} {{ __('completed') }} • {{ $modules->count() }} {{ __('modules') }}</p>

                @foreach($modules as $moduleName => $moduleLessons)
                @php
                $moduleKey = $moduleName ?: __('General');
                $moduleCompleted = $moduleLessons->filter(fn($l) => in_array($l->id, $completedLessonIds))->count();
                $moduleTotal = $moduleLessons->count();
                $modulePercent = $moduleTotal > 0 ? round(($moduleCompleted / $moduleTotal) * 100) : 0;
                @endphp
                <div class="border-t border-gray-100 reveal-up" data-stagger="{{ $loop->index }}">
                    <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition cursor-pointer" @click="openModule = openModule === '{{ $moduleKey }}' ? null : '{{ $moduleKey }}'">
                        <div class="flex items-center space-x-3">
                            <i class="fas" :class="openModule === '{{ $moduleKey }}' ? 'fa-chevron-down' : 'fa-chevron-right'" style="color: var(--text-muted)"></i>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $moduleKey }}</p>
                                <p class="text-xs text-gray-500">{{ $moduleCompleted }}/{{ $moduleTotal }} {{ __('lessons') }} • {{ $modulePercent }}%</p>
                            </div>
                        </div>
                        <div class="w-24 bg-gray-100 rounded-full h-1.5">
                            <div class="bg-indigo-500 h-1.5 rounded-full transition-all" style="width: {{ $modulePercent }}%"></div>
                        </div>
                    </div>
                    <div x-show="openModule === '{{ $moduleKey }}'" x-collapse>
                        @foreach($moduleLessons->sortBy('order_num') as $lesson)
                        <div class="border-t border-gray-50">
                            <a href="{{ route('courses.lesson', [$course->id, $lesson->id]) }}" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition group">
                                <div class="flex items-center space-x-3 pl-6">
                                    @if(in_array($lesson->id, $completedLessonIds))
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-green-600 text-xs"></i>
                                    </div>
                                    @else
                                    <div class="w-6 h-6 border-2 border-gray-200 rounded-full group-hover:border-indigo-400 transition"></div>
                                    @endif
                                    <div>
                                        <p class="text-sm {{ in_array($lesson->id, $completedLessonIds) ? 'text-gray-500' : 'text-gray-800 font-medium' }}">{{ $lesson->title }}</p>
                                        <div class="flex items-center space-x-3 mt-0.5">
                                            <span class="text-xs text-gray-400"><i class="fas fa-clock mr-1"></i>{{ $lesson->duration_minutes }} {{ __('min') }}</span>
                                            @if($lesson->presentation_url)<span class="text-xs text-gray-400"><i class="fas fa-desktop mr-1"></i></span>@endif
                                            @if($lesson->materials_url)<span class="text-xs text-gray-400"><i class="fas fa-link mr-1"></i></span>@endif
                                            @if($lesson->practiceTasks->count())<span class="text-xs text-gray-400"><i class="fas fa-code mr-1"></i>{{ $lesson->practiceTasks->count() }}</span>@endif
                                            @if($lesson->lessonQuizzes->count())<span class="text-xs text-gray-400"><i class="fas fa-question-circle mr-1"></i></span>@endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $lesson->difficulty === 'easy' ? 'bg-green-100 text-green-700' : ($lesson->difficulty === 'hard' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">{{ __('difficulty_' . $lesson->difficulty) }}</span>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                @if($exam)
                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ __('Final Exam') }}</p>
                                <p class="text-xs text-gray-500">{{ $exam->questions_per_exam }} {{ __('questions') }} • {{ $exam->time_limit_minutes }} {{ __('min') }} • {{ __('Pass') }}: {{ $exam->pass_percent }}%</p>
                            </div>
                        </div>
                        @if($percent >= 80)
                        <a href="{{ route('courses.exam', $course->id) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                            {{ __('Start Exam') }}
                        </a>
                        @else
                        <span class="text-sm text-gray-400">{{ __('Complete 80% to unlock') }}</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4">{{ __('Instructor') }}</h3>
                @if($instructorUser)
                <a href="{{ route('profile.show', $instructorUser->id) }}" class="flex items-center space-x-3 group">
                    <img src="{{ $instructorUser->avatar_url }}" class="w-12 h-12 rounded-full ring-2 ring-transparent group-hover:ring-indigo-500 transition">
                    <div>
                        <p class="font-semibold text-gray-800 group-hover:text-indigo-600 transition">{{ $course->instructor }}</p>
                        <p class="text-xs text-gray-500">{{ $course->category }}</p>
                    </div>
                </a>
                @else
                <div class="flex items-center space-x-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($course->instructor) }}&background=6366f1&color=fff" class="w-12 h-12 rounded-full">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $course->instructor }}</p>
                        <p class="text-xs text-gray-500">{{ $course->category }}</p>
                    </div>
                </div>
                @endif
            </div>

            @if($course->materials_url)
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4">{{ __('Materials') }}</h3>
                <a href="{{ $course->materials_url }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-external-link-alt mr-1"></i>{{ $course->materials_title ?: __('View Materials') }}
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
