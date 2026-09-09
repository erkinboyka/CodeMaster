@extends('layouts.app')
@section('title', 'Мои курсы - CodeMaster')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold" style="color: var(--text)">Мои курсы</h1>
        <a href="{{ route('courses.create') }}" class="px-6 py-3 rounded-xl font-semibold text-white" style="background: var(--accent)">+ Создать AI-курс</a>
    </div>

    @if($owned->count() > 0)
        <h2 class="text-xl font-bold mb-4" style="color: var(--text)">Созданные мной</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($owned as $course)
                <a href="{{ route('courses.show', $course->id) }}" class="rounded-2xl p-6 transition-all hover:shadow-lg" style="background: var(--card-bg); border: 1px solid var(--border)">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: var(--accent-glow); color: var(--accent)">🤖</div>
                        <div class="flex-1"><h3 class="font-bold text-sm" style="color: var(--text)">{{ $course->topic ?? $course->title }}</h3></div>
                    </div>
                    <div class="flex items-center justify-between text-xs" style="color: var(--text-muted)">
                        <span>{{ $course->total_steps }} шагов</span>
                        <span>{{ $course->students_count }} студентов</span>
                        <span class="px-2 py-0.5 rounded-full" style="background: {{ $course->type === 'teacher' ? '#dcfce7' : '#e0e7ff' }}; color: {{ $course->type === 'teacher' ? '#166534' : '#3730a3' }}">{{ $course->type === 'teacher' ? 'Публичный' : 'Приватный' }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    @if($enrolled->count() > 0)
        <h2 class="text-xl font-bold mb-4" style="color: var(--text)">Подписки</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($enrolled as $e)
                @php $course = $e->course; $p = $course->total_steps > 0 ? round(($e->steps_completed / $course->total_steps) * 100) : 0; @endphp
                <a href="{{ route('courses.show', $course->id) }}" class="rounded-2xl p-6 transition-all hover:shadow-lg" style="background: var(--card-bg); border: 1px solid var(--border)">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: var(--accent-glow); color: var(--accent)">📖</div>
                        <div class="flex-1"><h3 class="font-bold text-sm" style="color: var(--text)">{{ $course->topic ?? $course->title }}</h3><div class="text-xs" style="color: var(--text-muted)">{{ $course->owner?->name }}</div></div>
                    </div>
                    <div class="w-full h-2 rounded-full mb-2" style="background: var(--bg-secondary)"><div class="h-full rounded-full" style="width:{{ $p }}%; background: var(--accent)"></div></div>
                    <div class="flex justify-between text-xs" style="color: var(--text-muted)"><span>{{ $e->experience }} XP</span><span>{{ $p }}%</span></div>
                </a>
            @endforeach
        </div>
    @endif

    @if($owned->isEmpty() && $enrolled->isEmpty())
        <div class="text-center py-16">
            <div class="text-5xl mb-4">📚</div>
            <p class="text-lg mb-4" style="color: var(--text-muted)">У вас пока нет курсов</p>
            <a href="{{ route('courses.create') }}" class="px-6 py-3 rounded-xl font-semibold text-white" style="background: var(--accent)">Создать AI-курс</a>
        </div>
    @endif
</div>
@endsection
