@extends('layouts.app')

@section('title', __('Dashboard') . ' - CodeMaster')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 md:p-8 mb-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative">
            <h1 class="text-2xl md:text-3xl font-bold mb-2">{{ __('Welcome back') }}, {{ $user->name ?? 'User' }}!</h1>
            <p class="text-white/80 mb-4">{{ __('Continue your learning journey. You are doing great!') }}</p>
            <a href="{{ route('courses.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white text-indigo-600 font-semibold rounded-xl hover:bg-indigo-50 transition">
                <i class="fas fa-play mr-2"></i>{{ __('Continue Learning') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-indigo-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $completedCourses }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ __('Courses Completed') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-spinner text-purple-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $inProgressCourses }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ __('In Progress') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-certificate text-yellow-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $certificates->count() }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ __('Certificates') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-briefcase text-green-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $applications }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ __('Applications') }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 space-y-6">
            {{-- In Progress Courses --}}
            @if($inProgressCourseList->count())
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ __('Continue Learning') }}</h3>
                    <a href="{{ route('courses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">{{ __('View all') }}</a>
                </div>
                <div class="space-y-4">
                    @foreach($inProgressCourseList as $progress)
                    <a href="{{ route('courses.show', $progress->course_id) }}" class="flex items-center space-x-4 p-3 rounded-xl hover:bg-gray-50 transition">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-book text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 text-sm">{{ $progress->course->title ?? __('Course') }}</h4>
                            <div class="flex items-center mt-1">
                                <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $progress->progress }}%"></div>
                                </div>
                                <span class="text-xs text-gray-400 ml-2">{{ $progress->progress }}%</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Recent Activity --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Recent Activity') }}</h3>
                <div class="space-y-4">
                    @forelse($recentActivity as $activity)
                    <div class="flex items-start space-x-4 p-3 rounded-xl hover:bg-gray-50 transition">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-{{ $activity->activity_type === 'course' ? 'book' : ($activity->activity_type === 'certificate' ? 'certificate' : ($activity->activity_type === 'application' ? 'briefcase' : 'code')) }} text-indigo-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800 font-medium">{{ $activity->activity_text }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $activity->activity_time?->diffForHumans() ?? '' }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">{{ __('No activity yet') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- Recommended Courses --}}
            @if($recommendedCourses->count())
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ __('Recommended Courses') }}</h3>
                    <a href="{{ route('courses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">{{ __('View all') }}</a>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    @foreach($recommendedCourses as $course)
                    <a href="{{ route('courses.show', $course->id) }}" class="p-4 border border-gray-100 rounded-xl hover:shadow-md transition group" style="text-decoration:none">
                        <div class="flex items-start space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-book text-white text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 text-sm group-hover:text-indigo-600 transition">{{ $course->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $course->lessons->count() }} {{ __('lessons') }} • {{ __('courses_level_' . mb_strtolower($course->level)) }}</p>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            {{-- User Skills --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Your Skills') }}</h3>
                <div class="space-y-3">
                    @forelse($user->skills->take(8) as $skill)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $skill->skill_name }}</span>
                            <span class="text-xs text-gray-500">{{ ucfirst($skill->skill_level) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            @php
                                $levelPct = match($skill->skill_level) {
                                    'beginner' => 25,
                                    'intermediate' => 50,
                                    'advanced' => 75,
                                    'expert' => 100,
                                    default => 50,
                                };
                            @endphp
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all duration-1000" style="width: {{ $levelPct }}%"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">{{ __('No skills added yet') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- Notifications --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Notifications') }}</h3>
                <div class="space-y-3">
                    @forelse($notifications as $notif)
                    <div class="p-3 {{ $notif->is_read ? 'bg-gray-50' : 'bg-indigo-50 rounded-xl border-l-4 border-indigo-500' }} rounded-xl">
                        <p class="text-sm text-gray-800">{{ $notif->message }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notif->notification_time?->diffForHumans() ?? '' }}</p>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">{{ __('No notifications') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- Certificates --}}
            @if($certificates->count())
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Certificates') }}</h3>
                <div class="space-y-3">
                    @foreach($certificates as $cert)
                    <a href="{{ route('certificate.show', $cert->cert_hash) }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-50 transition" style="text-decoration:none">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-certificate text-yellow-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $cert->certificate_name }}</p>
                            <p class="text-xs text-gray-400">{{ $cert->course->title ?? '' }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M12 2L4 7v10l8 5 8-5V7l-8-5z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" fill="currentColor" opacity=".7"/><path d="M12 2v7M12 15v7M4 7l6 3M14 12l6 3M4 17l6-3M14 12l6-3" stroke="currentColor" stroke-width="1" opacity=".5"/></svg>
                    </div>
                    <h3 class="font-bold">{{ __('AI Tutor') }}</h3>
                </div>
                <p class="text-sm text-white/80 mb-4">{{ __('Need help? Ask our AI tutor anything about programming.') }}</p>
                <a href="{{ route('courses.index') }}" class="block w-full py-2.5 bg-white/20 hover:bg-white/30 rounded-xl text-sm font-medium transition text-center">
                    {{ __('Start Learning') }} <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
