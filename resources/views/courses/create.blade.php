@extends('layouts.app')
@section('title', 'Создать AI-курс - CodeMaster')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('courses.index') }}" class="text-sm" style="color: var(--accent)">← Назад к курсам</a>
        <h1 class="text-3xl font-bold mt-2" style="color: var(--text)">Создать AI-курс</h1>
        <p class="mt-1" style="color: var(--text-muted)">AI автоматически сгенерирует учебный план, тесты и материалы</p>
    </div>

    @if($errors->any())
        <div class="rounded-xl p-4 mb-6" style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form action="{{ route('courses.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="rounded-2xl p-6 space-y-6" style="background: var(--card-bg); border: 1px solid var(--border)">
            <div>
                <label class="block text-sm font-semibold mb-2" style="color: var(--text)">Тема курса</label>
                <input type="text" name="topic" value="{{ old('topic') }}" required
                    class="w-full px-4 py-3 rounded-xl text-sm focus:outline-none focus:ring-2"
                    style="background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text)"
                    placeholder="Например: Python для данных, React, Machine Learning, DevOps...">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color: var(--text)">Уровень</label>
                    <select name="level" required class="w-full px-4 py-3 rounded-xl text-sm" style="background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text)">
                        <option value="beginner">Начинающий</option>
                        <option value="intermediate">Средний</option>
                        <option value="advanced">Продвинутый</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color: var(--text)">Часов в неделю</label>
                    <input type="number" name="freetime" value="{{ old('freetime', 5) }}" min="1" max="40" required
                        class="w-full px-4 py-3 rounded-xl text-sm" style="background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text)">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2" style="color: var(--text)">Тип курса</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="teacher" checked class="peer sr-only">
                        <div class="p-4 rounded-xl text-center transition-all peer-checked:ring-2" style="background: var(--bg-secondary); border: 1px solid var(--border); --tw-ring-color: var(--accent)">
                            <div class="text-2xl mb-1">🌍</div>
                            <div class="font-semibold text-sm" style="color: var(--text)">Публичный</div>
                            <div class="text-xs" style="color: var(--text-muted)">Доступен всем</div>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="private" class="peer sr-only">
                        <div class="p-4 rounded-xl text-center transition-all peer-checked:ring-2" style="background: var(--bg-secondary); border: 1px solid var(--border); --tw-ring-color: var(--accent)">
                            <div class="text-2xl mb-1">🔒</div>
                            <div class="font-semibold text-sm" style="color: var(--text)">Приватный</div>
                            <div class="text-xs" style="color: var(--text-muted)">Только для вас</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="rounded-2xl p-6" style="background: var(--accent-glow); border: 1px solid var(--accent)">
            <div class="flex items-start gap-3">
                <div class="text-2xl">🤖</div>
                <div>
                    <h3 class="font-bold" style="color: var(--accent)">AI автоматически создаст:</h3>
                    <ul class="mt-2 text-sm space-y-1" style="color: var(--text-muted)">
                        <li>• Учебный план из 8 шагов (блоки + подшаги)</li>
                        <li>• Навыки и компетенции</li>
                        <li>• Тесты (выбор, множественный, открытый, верно/неверно, соответствие)</li>
                        <li>• Учебные материалы и лекции</li>
                        <li>• Описания с примерами кода</li>
                    </ul>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full py-4 rounded-xl font-bold text-lg text-white hover:opacity-90 transition" style="background: var(--accent)">
            Сгенерировать курс с AI
        </button>
    </form>
</div>
@endsection
