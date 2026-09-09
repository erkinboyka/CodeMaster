@extends('layouts.app')
@section('title', 'Создать дорожную карту - CodeMaster')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6 reveal-up">
        <a href="{{ route('roadmaps.index') }}" class="text-sm font-medium transition" style="color: var(--accent)" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'"><i class="fas fa-arrow-left mr-1"></i>Назад к дорожным картам</a>
    </div>

    <div class="rounded-2xl p-8 reveal-up" style="background: var(--card); border: 1px solid var(--border); box-shadow: var(--shadow-lg)">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: var(--accent-glow)">
                <i class="fas fa-route text-lg" style="color: var(--accent)"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold" style="color: var(--text)">Создать дорожную карту</h1>
                <p class="text-sm" style="color: var(--text-muted)">AI сгенерирует структуру из секций и курсов</p>
            </div>
        </div>

        <form action="{{ route('roadmaps.store') }}" method="POST" class="mt-8">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color: var(--text)">Название</label>
                    <input type="text" name="title" required placeholder="Frontend Разработчик"
                        class="w-full px-4 py-3 rounded-xl text-sm focus:outline-none focus:ring-2 transition"
                        style="background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text); --tw-ring-color: var(--accent)"
                        onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color: var(--text)">Категория</label>
                        <select name="category" class="w-full px-4 py-3 rounded-xl text-sm focus:outline-none focus:ring-2 transition"
                            style="background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text); --tw-ring-color: var(--accent)">
                            <option value="frontend">Frontend</option>
                            <option value="backend">Backend</option>
                            <option value="design">Design</option>
                            <option value="devops">DevOps</option>
                            <option value="other">Другое</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color: var(--text)">Уровень</label>
                        <select name="difficulty" class="w-full px-4 py-3 rounded-xl text-sm focus:outline-none focus:ring-2 transition"
                            style="background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text); --tw-ring-color: var(--accent)">
                            <option value="beginner">Начинающий</option>
                            <option value="intermediate">Средний</option>
                            <option value="advanced">Продвинутый</option>
                        </select>
                    </div>
                </div>

                @error('title')
                <p class="text-xs font-medium" style="color: var(--danger)">{{ $message }}</p>
                @enderror

                <button type="submit" class="w-full py-3.5 rounded-xl font-semibold text-white transition-all" style="background: var(--gradient); box-shadow: 0 8px 32px var(--accent-glow-strong)" onmouseover="this.style.transform='translateY(-4px) scale(1.02)'" onmouseout="this.style.transform=''">
                    <i class="fas fa-magic mr-2"></i>Сгенерировать дорожную карту
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
