@extends('layouts.app')
@section('title', 'Доступные курсы - CodeMaster')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8" style="color: var(--text)">Доступные курсы</h1>

    @if($courses->isEmpty())
        <div class="text-center py-16">
            <div class="text-5xl mb-4">📚</div>
            <p class="text-lg" style="color: var(--text-muted)">Нет доступных курсов для подписки</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="rounded-2xl p-6 transition-all hover:shadow-lg" style="background: var(--card-bg); border: 1px solid var(--border)">
                    <a href="{{ route('courses.show', $course->id) }}">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: var(--accent-glow); color: var(--accent)">🤖</div>
                            <div class="flex-1"><h3 class="font-bold text-sm" style="color: var(--text)">{{ $course->topic ?? $course->title }}</h3><div class="text-xs" style="color: var(--text-muted)">{{ $course->owner?->name }}</div></div>
                        </div>
                        <div class="flex flex-wrap gap-1 mb-3">
                            @foreach($course->courseSkills->take(3) as $s)<span class="px-2 py-0.5 rounded-full text-xs" style="background: var(--accent-glow); color: var(--accent)">{{ $s->skill ?? $s->skill_name }}</span>@endforeach
                        </div>
                        <div class="flex items-center justify-between text-xs" style="color: var(--text-muted)">
                            <span>{{ $course->total_steps }} шагов</span><span>{{ $course->students_count }} студентов</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $courses->links() }}</div>
    @endif
</div>
@endsection
