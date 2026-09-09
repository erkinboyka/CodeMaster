@extends('layouts.app')
@section('title', 'Генерация курса - CodeMaster')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16 text-center">
    <div class="rounded-2xl p-8 reveal-up" style="background: var(--card); border: 1px solid var(--border); box-shadow: var(--shadow-lg)">
        <div class="mb-6">
            <div class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center" style="background: var(--accent-glow)">
                <i class="fas fa-cog fa-spin text-3xl" style="color: var(--accent)"></i>
            </div>
        </div>
        <h1 class="text-2xl font-bold mb-2" style="color: var(--text)">Генерируем ваш курс</h1>
        <p class="text-sm mb-6" style="color: var(--text-muted)">{{ $course->topic }}</p>

        <div class="w-full h-3 rounded-full mb-4" style="background: var(--bg-secondary)">
            <div id="progress-bar" class="h-full rounded-full transition-all duration-500" style="width: 0%; background: var(--gradient)"></div>
        </div>
        <p id="status-text" class="text-sm" style="color: var(--text-muted)">Подготовка...</p>
    </div>
</div>

<script>
const courseId = {{ $course->id }};
const checkInterval = setInterval(() => {
    fetch(`/courses/${courseId}/status`)
        .then(r => r.json())
        .then(data => {
            const bar = document.getElementById('progress-bar');
            const text = document.getElementById('status-text');

            if (data.status === 'ready' || data.status === 'partial_ready') {
                bar.style.width = '100%';
                text.textContent = data.status === 'partial_ready'
                    ? 'Курс частично готов (часть блоков не сгенерилась). Переход...'
                    : 'Курс готов! Переход...';
                text.style.color = 'var(--accent)';
                clearInterval(checkInterval);
                setTimeout(() => {
                    window.location.href = `/courses/${courseId}`;
                }, 1200);
            } else if (data.status === 'error') {
                text.textContent = 'Ошибка генерации. Попробуйте создать курс заново.';
                text.style.color = 'var(--danger)';
                clearInterval(checkInterval);
            } else {
                bar.style.width = data.progress + '%';
                text.textContent = `Создаём шаги... ${data.total_steps} шагов`;
            }
        })
        .catch(() => {});
}, 2000);
</script>
@endsection
