@extends('layouts.app')
@section('title', 'Генерация дорожной карты - CodeMaster')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16 text-center">
    <div class="rounded-2xl p-8 reveal-up" style="background: var(--card); border: 1px solid var(--border); box-shadow: var(--shadow-lg)">
        <div class="mb-6">
            <div class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center" style="background: var(--accent-glow)">
                <i class="fas fa-cog fa-spin text-3xl" style="color: var(--accent)"></i>
            </div>
        </div>
        <h1 class="text-2xl font-bold mb-2" style="color: var(--text)">Генерируем дорожную карту</h1>
        <p class="text-sm mb-6" style="color: var(--text-muted)">{{ $roadmap->title }}</p>

        <div class="w-full h-3 rounded-full mb-4" style="background: var(--bg-secondary)">
            <div id="progress-bar" class="h-full rounded-full transition-all duration-500" style="width: 30%; background: var(--gradient)"></div>
        </div>
        <p id="status-text" class="text-sm" style="color: var(--text-muted)">Создаём секции и курсы...</p>
    </div>
</div>

<script>
const roadmapSlug = '{{ $roadmap->slug }}';
const checkInterval = setInterval(() => {
    fetch(`/roadmaps/${roadmapSlug}/status`)
        .then(r => r.json())
        .then(data => {
            const bar = document.getElementById('progress-bar');
            const text = document.getElementById('status-text');

            if (data.is_published) {
                bar.style.width = '100%';
                text.textContent = 'Roadmap готов! Переход...';
                text.style.color = 'var(--accent)';
                clearInterval(checkInterval);
                setTimeout(() => {
                    window.location.href = `/roadmap/${roadmapSlug}`;
                }, 1000);
            } else if (data.has_error) {
                bar.style.width = '100%';
                bar.style.background = 'var(--danger)';
                text.textContent = 'Ошибка генерации. Попробуйте создать roadmap заново.';
                text.style.color = 'var(--danger)';
                clearInterval(checkInterval);
            } else if (data.total_sections > 0) {
                bar.style.width = Math.min(90, 30 + data.total_sections * 10) + '%';
                text.textContent = `Создано ${data.total_sections} секций, ${data.total_courses} курсов...`;
            }
        })
        .catch(() => {});
}, 2000);
</script>
@endsection
