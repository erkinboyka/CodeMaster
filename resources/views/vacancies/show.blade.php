@extends('layouts.app')

@section('title', ($vacancy->title ?? 'Vacancy') . ' - CodeMaster')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <nav class="flex items-center space-x-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('vacancies.index') }}" class="hover:text-indigo-600 transition">{{ __('Vacancies') }}</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-700">{{ $vacancy->title }}</span>
    </nav>

    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <span class="text-xl font-bold text-indigo-600">{{ mb_strtoupper(mb_substr($vacancy->company ?? 'C', 0, 2)) }}</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $vacancy->title }}</h1>
                            <p class="text-gray-500">{{ $vacancy->company }} • {{ $vacancy->location }}</p>
                        </div>
                    </div>
                    <span class="px-4 py-1.5 text-sm font-medium rounded-full {{ $vacancy->type === 'remote' ? 'bg-green-50 text-green-600' : ($vacancy->type === 'hybrid' ? 'bg-yellow-50 text-yellow-600' : 'bg-blue-50 text-blue-600') }}">{{ __($vacancy->type) }}</span>
                </div>

                <div class="flex flex-wrap gap-6 mb-6 pb-6 border-b border-gray-100">
                    @if($vacancy->salary_min || $vacancy->salary_max)
                    <div>
                        <p class="text-xs text-gray-400 mb-1">{{ __('Salary') }}</p>
                        <p class="text-lg font-bold text-indigo-600">
                            @if($vacancy->salary_min && $vacancy->salary_max)
                                {{ number_format($vacancy->salary_min) }} - {{ number_format($vacancy->salary_max) }} {{ $vacancy->salary_currency }}
                            @elseif($vacancy->salary_min)
                                {{ __('from') }} {{ number_format($vacancy->salary_min) }} {{ $vacancy->salary_currency }}
                            @else
                                {{ __('up to') }} {{ number_format($vacancy->salary_max) }} {{ $vacancy->salary_currency }}
                            @endif
                        </p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-400 mb-1">{{ __('Posted') }}</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $vacancy->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="prose prose-sm max-w-none">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">{{ __('Description') }}</h3>
                    <p class="text-gray-600 mb-4">{{ $vacancy->description }}</p>

                    @if($vacancy->requirements->count())
                    <h3 class="text-lg font-bold text-gray-900 mb-3 mt-6">{{ __('Requirements') }}</h3>
                    <ul class="space-y-2 text-gray-600">
                        @foreach($vacancy->requirements as $req)
                        <li class="flex items-start"><i class="fas fa-check text-green-500 mt-1 mr-2"></i>{{ $req->requirement_text }}</li>
                        @endforeach
                    </ul>
                    @endif

                    @if($vacancy->responsibilities->count())
                    <h3 class="text-lg font-bold text-gray-900 mb-3 mt-6">{{ __('Responsibilities') }}</h3>
                    <ul class="space-y-2 text-gray-600">
                        @foreach($vacancy->responsibilities as $resp)
                        <li class="flex items-start"><i class="fas fa-arrow-right text-indigo-500 mt-1 mr-2"></i>{{ $resp->responsibility_text }}</li>
                        @endforeach
                    </ul>
                    @endif

                    @if($vacancy->pluses->count())
                    <h3 class="text-lg font-bold text-gray-900 mb-3 mt-6">{{ __('Nice to Have') }}</h3>
                    <ul class="space-y-2 text-gray-600">
                        @foreach($vacancy->pluses as $plus)
                        <li class="flex items-start"><i class="fas fa-star text-yellow-500 mt-1 mr-2"></i>{{ $plus->plus_text }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-24">
                @if($hasApplied)
                <div class="w-full py-3 bg-green-100 text-green-700 font-semibold rounded-xl text-center mb-3">
                    <i class="fas fa-check mr-2"></i>{{ __('Applied') }}
                </div>
                <a href="{{ route('vacancyChat.show', $applicationId) }}" class="block w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl text-center hover:from-indigo-600 hover:to-purple-700 shadow-lg transition-all duration-300">
                    <i class="fas fa-comments mr-2"></i>{{ __('Go to Chat') }}
                </a>
                @else
                <form action="{{ route('vacancies.apply', $vacancy->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 shadow-lg transition-all duration-300 mb-3">
                        <i class="fas fa-paper-plane mr-2"></i>{{ __('Apply Now') }}
                    </button>
                </form>
                @endif

                <div class="space-y-4">
                    <h3 class="font-bold text-gray-900">{{ __('Required Skills') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($vacancy->vacancySkills as $skill)
                        <span class="px-3 py-1.5 bg-indigo-50 text-indigo-600 text-xs font-medium rounded-full">{{ $skill->skill_name }}</span>
                        @endforeach
                    </div>
                </div>

                <hr class="my-6 border-gray-100">

                <div class="space-y-4">
                    <h3 class="font-bold text-gray-900">{{ __('Company Info') }}</h3>
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl flex items-center justify-center">
                            <span class="text-lg font-bold text-indigo-600">{{ mb_strtoupper(mb_substr($vacancy->company ?? 'C', 0, 2)) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $vacancy->company }}</p>
                        </div>
                    </div>
                    @if($vacancy->company_description)
                    <p class="text-sm text-gray-500">{{ $vacancy->company_description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
