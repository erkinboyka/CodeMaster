@extends('layouts.app')

@section('title', 'Подготовка к собеседованию' . ' - CodeMaster')

@section('head')
<style>
    .iv-hero {
        position: relative;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 50%, var(--accent-3) 100%);
        padding: clamp(48px, 8vw, 100px) 24px clamp(40px, 6vw, 80px);
        overflow: hidden;
        text-align: center;
    }
    .iv-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%);
    }
    .iv-hero::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 60px;
        background: var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .iv-hero-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 40px 40px;
    }
    .iv-hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        animation: ivOrbFloat 8s ease-in-out infinite;
    }
    .iv-hero-orb:nth-child(1) { width: 300px; height: 300px; background: rgba(255,255,255,0.1); top: -100px; left: -50px; }
    .iv-hero-orb:nth-child(2) { width: 250px; height: 250px; background: rgba(255,255,255,0.08); bottom: -80px; right: -30px; animation-delay: -3s; }
    .iv-hero-orb:nth-child(3) { width: 200px; height: 200px; background: rgba(255,255,255,0.06); top: 50%; left: 60%; animation-delay: -5s; }
    @@keyframes ivOrbFloat {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-20px) scale(1.05); }
    }
    .iv-hero h1 {
        font-size: clamp(36px, 6vw, 72px);
        font-weight: 900;
        color: white;
        letter-spacing: -2px;
        margin-bottom: 16px;
        position: relative;
        z-index: 2;
    }
    .iv-hero p {
        font-size: clamp(16px, 2vw, 20px);
        color: rgba(255,255,255,0.75);
        max-width: 540px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
        line-height: 1.7;
    }
    .iv-hero-stats {
        display: flex;
        justify-content: center;
        gap: clamp(24px, 4vw, 56px);
        position: relative;
        z-index: 2;
        margin-top: 36px;
    }
    .iv-hero-stat-val {
        font-size: clamp(28px, 4vw, 42px);
        font-weight: 900;
        color: white;
        line-height: 1;
    }
    .iv-hero-stat-label {
        font-size: 12px;
        color: rgba(255,255,255,0.6);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-top: 8px;
        font-weight: 500;
    }

    .iv-content {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 28px;
    }
    @@media(max-width: 900px) { .iv-content { grid-template-columns: 1fr; } }

    .iv-panel {
        border-radius: 20px;
        background: var(--card);
        border: 1px solid var(--border);
        overflow: hidden;
    }
    .iv-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
    }
    .iv-panel-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .iv-panel-title i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: var(--accent-glow);
        color: var(--accent);
        font-size: 14px;
    }
    .iv-new-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: white;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 14px var(--accent-glow);
    }
    .iv-new-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px var(--accent-glow); }

    .iv-list { padding: 8px; }
    .iv-card {
        display: block;
        padding: 20px;
        border-radius: 14px;
        border: 1px solid var(--border);
        text-decoration: none;
        transition: all 0.3s ease;
        margin-bottom: 8px;
        position: relative;
        overflow: hidden;
    }
    .iv-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        border-radius: 0 3px 3px 0;
        background: var(--accent);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .iv-card:hover { border-color: var(--accent); transform: translateX(4px); box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
    .iv-card:hover::before { opacity: 1; }
    .iv-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }
    .iv-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        transition: color 0.2s;
    }
    .iv-card:hover .iv-card-title { color: var(--accent); }
    .iv-card-tags {
        display: flex;
        gap: 8px;
        margin-top: 10px;
        flex-wrap: wrap;
    }
    .iv-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
    }
    .iv-tag-type { background: var(--accent-glow); color: var(--accent); }
    .iv-tag-diff-easy { background: rgba(34,197,94,0.1); color: #22c55e; }
    .iv-tag-diff-medium { background: rgba(234,179,8,0.1); color: #eab308; }
    .iv-tag-diff-hard { background: rgba(239,68,68,0.1); color: #ef4444; }
    .iv-tag-time { background: var(--bg); color: var(--text-muted); }

    .iv-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .iv-status-completed { background: rgba(34,197,94,0.1); color: #22c55e; }
    .iv-status-in_progress { background: rgba(234,179,8,0.1); color: #eab308; }
    .iv-status-pending { background: var(--bg); color: var(--text-muted); }

    .iv-score {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 12px;
        padding: 6px 14px;
        border-radius: 10px;
        background: var(--bg);
        font-size: 13px;
        font-weight: 700;
        color: var(--accent);
    }
    .iv-score i { font-size: 12px; }

    .iv-empty {
        text-align: center;
        padding: 60px 24px;
    }
    .iv-empty i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.3;
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .iv-empty p { font-size: 16px; font-weight: 600; color: var(--text-muted); }
    .iv-empty small { font-size: 13px; color: var(--text-muted); opacity: 0.7; }

    .iv-tips {
        padding: 24px;
    }
    .iv-tip {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
    }
    .iv-tip:last-child { border-bottom: none; }
    .iv-tip-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        font-size: 14px;
        flex-shrink: 0;
    }
    .iv-tip-text {
        font-size: 14px;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .iv-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 200;
        padding: 24px;
    }
    .iv-modal-overlay.open { display: flex; }
    .iv-modal {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 20px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        animation: ivModalIn 0.25s ease-out;
    }
    @@keyframes ivModalIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    .iv-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 24px 28px;
        border-bottom: 1px solid var(--border);
    }
    .iv-modal-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .iv-modal-close {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: none;
        background: var(--bg);
        color: var(--text-muted);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .iv-modal-close:hover { background: var(--border); color: var(--text); }
    .iv-modal-body { padding: 24px 28px; }
    .iv-form-group { margin-bottom: 18px; }
    .iv-form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 8px;
    }
    .iv-form-select {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--bg);
        color: var(--text);
        font-size: 14px;
        outline: none;
        transition: all 0.3s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
    }
    .iv-form-select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
    .iv-modal-foot {
        display: flex;
        gap: 10px;
        padding: 0 28px 24px;
    }
    .iv-btn-start {
        flex: 1;
        padding: 12px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        border: none;
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: white;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 14px var(--accent-glow);
    }
    .iv-btn-start:hover { transform: translateY(-1px); box-shadow: 0 8px 24px var(--accent-glow); }
    .iv-btn-cancel {
        padding: 12px 20px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        border: 1px solid var(--border);
        background: var(--bg);
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s;
    }
    .iv-btn-cancel:hover { border-color: var(--accent); color: var(--accent); }
</style>
@endsection

@section('content')
<section class="iv-hero">
    <div class="iv-hero-grid"></div>
    <div class="iv-hero-orb"></div>
    <div class="iv-hero-orb"></div>
    <div class="iv-hero-orb"></div>

    <h1 class="reveal-up" data-delay="0">Подготовка к собеседованию</h1>
    <p class="reveal-up" data-delay="0.1">Практикуйтесь с AI-собеседованиями и улучшайте свои навыки.</p>

    <div class="iv-hero-stats reveal-up" data-delay="0.2">
        <div class="iv-hero-stat">
            <div class="iv-hero-stat-val">{{ $interviews->count() }}</div>
            <div class="iv-hero-stat-label">Всего</div>
        </div>
        <div class="iv-hero-stat">
            <div class="iv-hero-stat-val">{{ $interviews->where('status', 'completed')->count() }}</div>
            <div class="iv-hero-stat-label">Завершено</div>
        </div>
        <div class="iv-hero-stat">
            <div class="iv-hero-stat-val">{{ $interviews->where('status', 'in_progress')->count() }}</div>
            <div class="iv-hero-stat-label">В процессе</div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="iv-content">
        <div class="iv-panel reveal-left">
            <div class="iv-panel-head">
                <div class="iv-panel-title">
                    <i class="fas fa-user-tie"></i>
                    Твои собеседования
                </div>
                <button onclick="document.getElementById('iv-modal').classList.add('open')" class="iv-new-btn">
                    <i class="fas fa-plus"></i> Новое собеседование
                </button>
            </div>
            <div class="iv-list">
                @forelse($interviews as $interview)
                @php
                    $statusClass = match($interview->status) { 'completed' => 'iv-status-completed', 'in_progress' => 'iv-status-in_progress', default => 'iv-status-pending' };
                    $diffClass = match($interview->difficulty) { 'easy' => 'iv-tag-diff-easy', 'medium' => 'iv-tag-diff-medium', 'hard' => 'iv-tag-diff-hard', default => 'iv-tag-diff-easy' };
                    $diffIcon = match($interview->difficulty) { 'easy' => 'fa-seedling', 'medium' => 'fa-fire', 'hard' => 'fa-skull', default => 'fa-circle' };
                    $typeIcon = match($interview->type) { 'technical' => 'fa-microchip', 'behavioral' => 'fa-comments', 'coding' => 'fa-code', 'system_design' => 'fa-network-wired', default => 'fa-circle-question' };
                @endphp
                <a href="{{ route('interview.room', $interview->id) }}" class="iv-card" data-stagger="{{ $loop->index }}">
                    <div class="iv-card-top">
                        <div class="iv-card-title">{{ $interview->title }}</div>
                        <span class="iv-status {{ $statusClass }}">
                            @if($interview->status === 'completed')<i class="fas fa-check-circle"></i>
                            @elseif($interview->status === 'in_progress')<i class="fas fa-spinner"></i>
                            @else<i class="fas fa-clock"></i>
                            @endif
                            {{ match($interview->status) { 'completed' => 'Завершено', 'in_progress' => 'В процессе', default => 'Ожидание' } }}
                        </span>
                    </div>
                    <div class="iv-card-tags">
                        <span class="iv-tag iv-tag-type"><i class="fas {{ $typeIcon }}"></i> {{ match($interview->type) { 'technical' => 'Техническое', 'behavioral' => 'Поведенческое', 'coding' => 'Кодинг', 'system_design' => 'Проектирование систем', default => ucfirst(str_replace('_', ' ', $interview->type)) } }}</span>
                        <span class="iv-tag {{ $diffClass }}"><i class="fas {{ $diffIcon }}"></i> {{ match($interview->difficulty) { 'easy' => 'Лёгкий', 'medium' => 'Средний', 'hard' => 'Сложный', default => ucfirst($interview->difficulty) } }}</span>
                        <span class="iv-tag iv-tag-time"><i class="far fa-clock"></i> {{ $interview->created_at->diffForHumans() }}</span>
                    </div>
                    @if($interview->score !== null)
                    <div class="iv-score">
                        <i class="fas fa-star"></i> Оценка: {{ $interview->score }}%
                    </div>
                    @endif
                </a>
                @empty
                <div class="iv-empty">
                    <i class="fas fa-user-tie"></i>
                    <p>Пока нет собеседований</p>
                    <small>Начни своё первое!</small>
                </div>
                @endforelse
            </div>
        </div>

        <div class="reveal-right">
            <div class="iv-panel">
                <div class="iv-panel-head">
                <div class="iv-panel-title">
                    <i class="fas fa-lightbulb"></i>
                    Советы по собеседованию
                </div>
            </div>
            <div class="iv-tips">
                <div class="iv-tip">
                    <div class="iv-tip-icon" style="background:rgba(234,179,8,0.1);color:var(--warning)"><i class="fas fa-comment-dots"></i></div>
                    <div class="iv-tip-text">Практикуйтесь объяснять свой ход мыслей вслух</div>
                </div>
                <div class="iv-tip">
                    <div class="iv-tip-icon" style="background:rgba(139,92,246,0.1);color:var(--accent-3)"><i class="fas fa-database"></i></div>
                    <div class="iv-tip-text">Регулярно повторяйте структуры данных и алгоритмы</div>
                </div>
                <div class="iv-tip">
                    <div class="iv-tip-icon" style="background:rgba(34,197,94,0.1);color:var(--success)"><i class="fas fa-pen-to-square"></i></div>
                    <div class="iv-tip-text">Практикуйте написание кода на доске или бумаге</div>
                </div>
                    <div class="iv-tip">
                        <div class="iv-tip-icon" style="background:rgba(59,130,246,0.1);color:var(--info)"><i class="fas fa-building"></i></div>
                        <div class="iv-tip-text">Исследуйте компанию перед собеседованием</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="iv-modal" class="iv-modal-overlay" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="iv-modal">
        <div class="iv-modal-head">
            <div class="iv-modal-title"><i class="fas fa-plus-circle" style="color:var(--accent)"></i> Новое собеседование</div>
            <button class="iv-modal-close" onclick="document.getElementById('iv-modal').classList.remove('open')"><i class="fas fa-xmark"></i></button>
        </div>
        <form action="{{ route('interview.store') }}" method="POST">
            @csrf
            <div class="iv-modal-body">
                <div class="iv-form-group">
                    <label class="iv-form-label">Тип собеседования</label>
                    <select name="type" required class="iv-form-select">
                        <option value="technical">Техническое</option>
                        <option value="behavioral">Поведенческое</option>
                        <option value="coding">Кодинг</option>
                        <option value="system_design">Проектирование систем</option>
                    </select>
                </div>
                <div class="iv-form-group">
                    <label class="iv-form-label">Сложность</label>
                    <select name="difficulty" required class="iv-form-select">
                        <option value="easy">Лёгкий</option>
                        <option value="medium">Средний</option>
                        <option value="hard">Сложный</option>
                    </select>
                </div>
            </div>
            <div class="iv-modal-foot">
                <button type="submit" class="iv-btn-start"><i class="fas fa-play"></i> Начать собеседование</button>
                <button type="button" onclick="document.getElementById('iv-modal').classList.remove('open')" class="iv-btn-cancel">Отмена</button>
            </div>
        </form>
    </div>
</div>
@endsection
