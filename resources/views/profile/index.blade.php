@extends('layouts.app')

@section('title', __('Профиль') . ' - CodeMaster')

@section('head')
<style>
    .pf-hero {
        position: relative; overflow: hidden;
        padding: 5rem 2rem 4rem; text-align: center;
        background: var(--gradient);
    }
    .pf-hero::before {
        content: ''; position: absolute; inset: -50%; width: 200%; height: 200%;
        background: radial-gradient(ellipse at 30% 50%, rgba(255,255,255,0.12) 0%, transparent 50%),
                    radial-gradient(ellipse at 70% 50%, rgba(255,255,255,0.08) 0%, transparent 50%);
        animation: pf-pulse 8s ease-in-out infinite alternate;
    }
    @keyframes pf-pulse { 0% { transform: scale(1); } 100% { transform: scale(1.1) rotate(2deg); } }
    .pf-hero__content { position: relative; z-index: 1; }
    .pf-avatar-wrap { position: relative; display: inline-block; margin-bottom: 1.25rem; }
    .pf-avatar {
        width: 7rem; height: 7rem; border-radius: 1rem; object-fit: cover;
        border: 4px solid rgba(255,255,255,0.3); box-shadow: 0 8px 32px rgba(0,0,0,0.25);
    }
    .pf-avatar-btn {
        position: absolute; bottom: -4px; right: -4px;
        width: 2rem; height: 2rem; border-radius: 50%;
        background: #fff; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: var(--accent); box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        transition: all 0.2s;
    }
    .pf-avatar-btn:hover { transform: scale(1.1); background: var(--accent); color: #fff; }
    .pf-hero__name { font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 800; color: #fff; margin-bottom: 0.25rem; }
    .pf-hero__sub { color: rgba(255,255,255,0.75); font-size: 0.95rem; margin-bottom: 0.75rem; }
    .pf-hero__stats {
        display: flex; flex-wrap: wrap; justify-content: center; gap: 1.25rem;
        color: rgba(255,255,255,0.65); font-size: 0.85rem;
    }
    .pf-hero__stats span { display: inline-flex; align-items: center; gap: 0.35rem; }
    .pf-hero__links { display: flex; justify-content: center; gap: 0.75rem; margin-top: 1rem; }
    .pf-hero__link {
        padding: 0.4rem 1rem; border-radius: 0.75rem;
        background: rgba(255,255,255,0.15); color: #fff; font-size: 0.85rem; font-weight: 500;
        text-decoration: none; transition: all 0.2s;
    }
    .pf-hero__link:hover { background: rgba(255,255,255,0.25); }
    .pf-body { padding: 2.5rem 1.5rem; }
    .pf-body__inner { max-width: 1000px; margin: 0 auto; }
    .pf-tabs {
        display: flex; gap: 0.25rem; overflow-x: auto;
        background: var(--bg-2); border: 1px solid var(--border);
        border-radius: 0.75rem; padding: 0.25rem; margin-bottom: 2rem;
    }
    .pf-tab {
        flex-shrink: 0; padding: 0.5rem 1rem; border-radius: 0.5rem;
        background: transparent; border: none; cursor: pointer;
        font-size: 0.85rem; font-weight: 500; color: var(--text-muted); transition: all 0.2s;
    }
    .pf-tab.active { background: var(--card); color: var(--accent); box-shadow: var(--card-shadow); }
    .pf-tab:hover:not(.active) { color: var(--text); }
    .pf-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius-lg); padding: 1.5rem;
    }
    .pf-card__header {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;
    }
    .pf-card__title { font-size: 1rem; font-weight: 700; color: var(--text); }
    .pf-card__add {
        font-size: 0.85rem; color: var(--accent); background: none;
        border: none; cursor: pointer; font-weight: 500; transition: color 0.2s;
    }
    .pf-card__add:hover { color: var(--accent-hover); }
    .pf-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .pf-grid-2 > * { display: flex; flex-direction: column; gap: 1.5rem; }
    .pf-item {
        display: flex; gap: 1rem; padding: 1rem;
        background: var(--bg-2); border-radius: var(--radius-md); transition: background 0.2s;
    }
    .pf-item:hover { background: color-mix(in srgb, var(--accent) 5%, var(--bg-2)); }
    .pf-item__icon {
        width: 2.5rem; height: 2.5rem; border-radius: 0.5rem;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.9rem;
    }
    .pf-item__icon--accent { background: color-mix(in srgb, var(--accent) 15%, var(--card)); color: var(--accent); }
    .pf-item__icon--purple { background: color-mix(in srgb, var(--accent-2) 15%, var(--card)); color: var(--accent-2); }
    .pf-item__icon--yellow { background: color-mix(in srgb, #f59e0b 15%, var(--card)); color: #f59e0b; }
    .pf-item__icon--green { background: color-mix(in srgb, #10b981 15%, var(--card)); color: #10b981; }
    .pf-item__body { flex: 1; min-width: 0; }
    .pf-item__row { display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; }
    .pf-item__name { font-size: 0.9rem; font-weight: 600; color: var(--text); }
    .pf-item__sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem; }
    .pf-item__desc { font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.35rem; line-height: 1.5; }
    .pf-del {
        background: none; border: none; color: var(--text-muted); cursor: pointer;
        padding: 0.25rem; border-radius: 0.375rem; transition: all 0.2s; font-size: 0.8rem;
    }
    .pf-del:hover { color: var(--danger); background: color-mix(in srgb, var(--danger) 10%, var(--card)); }
    .pf-stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .pf-stat {
        text-align: center; padding: 1rem;
        background: var(--bg-2); border-radius: var(--radius-md);
    }
    .pf-stat__val { font-size: 1.5rem; font-weight: 800; }
    .pf-stat__val--accent { color: var(--accent); }
    .pf-stat__val--purple { color: var(--accent-2); }
    .pf-stat__val--yellow { color: #f59e0b; }
    .pf-stat__val--green { color: #10b981; }
    .pf-stat__label { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; }
    .pf-skill {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.4rem 0.85rem; border-radius: 9999px;
        background: color-mix(in srgb, var(--accent) 12%, var(--card));
        color: var(--accent); font-size: 0.8rem; font-weight: 500;
    }
    .pf-skill i { font-size: 0.6rem; color: var(--success); }
    .pf-empty { text-align: center; padding: 2.5rem 1rem; color: var(--text-muted); font-size: 0.9rem; }
    .pf-empty i { font-size: 2rem; color: var(--border); margin-bottom: 0.75rem; display: block; }
    .pf-port-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .pf-port-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius-lg); overflow: hidden; transition: all 0.3s;
    }
    .pf-port-card:hover { transform: translateY(-2px); box-shadow: var(--card-shadow); }
    .pf-port-card__img {
        height: 10rem; overflow: hidden;
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
    }
    .pf-port-card__img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
    .pf-port-card:hover .pf-port-card__img img { transform: scale(1.05); }
    .pf-port-card__img i { font-size: 2.5rem; color: rgba(255,255,255,0.25); }
    .pf-port-card__body { padding: 1.1rem; }
    .pf-port-card__row { display: flex; align-items: center; justify-content: space-between; }
    .pf-port-card__title { font-size: 0.9rem; font-weight: 700; color: var(--text); }
    .pf-port-card__desc { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.3rem; }
    .pf-port-card__tags { display: flex; align-items: center; gap: 0.4rem; margin-top: 0.6rem; flex-wrap: wrap; }
    .pf-port-tag {
        padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500;
    }
    .pf-port-tag--cat { background: color-mix(in srgb, var(--accent) 12%, var(--card)); color: var(--accent); }
    .pf-port-tag--link { background: color-mix(in srgb, #3b82f6 12%, var(--card)); color: #3b82f6; }
    .pf-port-tag--gh { background: var(--bg-2); color: var(--text-muted); }
    .pf-cert-card {
        display: flex; align-items: center; gap: 1rem; padding: 1rem;
        background: var(--bg-2); border-radius: var(--radius-md);
        text-decoration: none; transition: all 0.2s;
    }
    .pf-cert-card:hover { background: color-mix(in srgb, var(--accent) 5%, var(--bg-2)); }
    .pf-cert-card__icon {
        width: 3rem; height: 3rem; border-radius: 0.75rem; flex-shrink: 0;
        background: linear-gradient(135deg, #f59e0b, #f97316);
        display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1rem;
    }
    .pf-cert-card__name { font-size: 0.85rem; font-weight: 600; color: var(--text); }
    .pf-cert-card__date { font-size: 0.75rem; color: var(--text-muted); }
    .pf-act-item {
        display: flex; align-items: start; gap: 0.75rem; padding: 0.75rem;
        border-radius: var(--radius-md); transition: background 0.2s;
    }
    .pf-act-item:hover { background: var(--bg-2); }
    .pf-act-dot {
        width: 2.5rem; height: 2.5rem; border-radius: 50%; flex-shrink: 0;
        background: color-mix(in srgb, var(--accent) 12%, var(--card));
        display: flex; align-items: center; justify-content: center;
        color: var(--accent); font-size: 0.8rem;
    }
    .pf-act-text { font-size: 0.85rem; color: var(--text); line-height: 1.4; }
    .pf-act-time { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; }
    .pf-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .pf-form-group { margin-bottom: 1rem; }
    .pf-form-group:last-child { margin-bottom: 0; }
    .pf-label {
        display: block; font-size: 0.8rem; font-weight: 600; color: var(--text);
        margin-bottom: 0.4rem;
    }
    .pf-label i { color: var(--text-muted); margin-right: 0.3rem; }
    .pf-input {
        width: 100%; padding: 0.6rem 1rem; border: 1px solid var(--border);
        border-radius: 0.75rem; background: var(--bg-2); color: var(--text);
        font-size: 0.85rem; transition: border-color 0.2s; box-sizing: border-box;
    }
    .pf-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 15%, transparent); }
    textarea.pf-input { resize: none; }
    .pf-btn {
        padding: 0.6rem 1.5rem; border-radius: 0.75rem; font-size: 0.85rem;
        font-weight: 600; border: none; cursor: pointer; transition: all 0.2s;
    }
    .pf-btn--primary { background: var(--accent); color: #fff; }
    .pf-btn--primary:hover { background: var(--accent-hover); }
    .pf-btn--ghost { background: var(--bg-2); color: var(--text); border: 1px solid var(--border); }
    .pf-btn--ghost:hover { background: var(--card); }
    .pf-form-actions { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem; }
    .pf-chk { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text); }
    .pf-chk input[type=checkbox] {
        width: 1rem; height: 1rem; accent-color: var(--accent);
    }
    /* MODALS */
    .pf-overlay {
        position: fixed; inset: 0; z-index: 50;
        display: flex; align-items: center; justify-content: center; padding: 1rem;
    }
    .pf-overlay__bg { position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
    .pf-modal {
        position: relative; z-index: 1;
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius-xl); padding: 1.5rem;
        width: 100%; max-width: 28rem; max-height: 90vh; overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .pf-modal__head {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;
    }
    .pf-modal__title { font-size: 1.1rem; font-weight: 700; color: var(--text); }
    .pf-modal__close {
        width: 2rem; height: 2rem; border-radius: 0.5rem;
        background: var(--bg-2); border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: var(--text-muted); transition: all 0.2s;
    }
    .pf-modal__close:hover { background: var(--border); color: var(--text); }
    @media (max-width: 640px) {
        .pf-grid-2 { grid-template-columns: 1fr; }
        .pf-stat-grid { grid-template-columns: 1fr 1fr; }
        .pf-port-grid { grid-template-columns: 1fr; }
        .pf-form-row { grid-template-columns: 1fr; }
    }
    .pf-file {
        position: relative; display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 2rem 1.5rem; border: 2px dashed var(--border);
        border-radius: var(--radius-lg); background: var(--bg-2);
        cursor: pointer; transition: all 0.25s; text-align: center;
    }
    .pf-file:hover { border-color: var(--accent); background: color-mix(in srgb, var(--accent) 5%, var(--bg-2)); }
    .pf-file.dragover { border-color: var(--accent); background: color-mix(in srgb, var(--accent) 10%, var(--bg-2)); transform: scale(1.01); }
    .pf-file input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .pf-file__icon {
        width: 3.5rem; height: 3.5rem; border-radius: 50%;
        background: color-mix(in srgb, var(--accent) 12%, var(--card));
        display: flex; align-items: center; justify-content: center;
        color: var(--accent); font-size: 1.2rem; margin-bottom: 0.75rem;
    }
    .pf-file__text { font-size: 0.85rem; color: var(--text); font-weight: 500; }
    .pf-file__hint { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; }
    .pf-file__preview {
        display: none; margin-top: 1rem; position: relative;
    }
    .pf-file__preview img {
        width: 6rem; height: 6rem; border-radius: 1rem; object-fit: cover;
        border: 3px solid var(--border); box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    .pf-file__preview-name {
        font-size: 0.75rem; color: var(--text-muted); margin-top: 0.4rem;
        max-width: 14rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
</style>
@endsection

@section('content')
<div class="pf-hero">
    <div class="pf-hero__content" x-data="{ showAvatarModal: false }">
        <div class="pf-avatar-wrap">
            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&background=fff&color=6366f1&size=128' }}" class="pf-avatar" alt="{{ $user->name }}">
            <button @click="showAvatarModal = true" class="pf-avatar-btn">
                <i class="fas fa-camera" style="font-size:0.7rem"></i>
            </button>
            <div x-show="showAvatarModal" x-transition style="display:none" class="pf-overlay">
                <div class="pf-overlay__bg" @click="showAvatarModal = false"></div>
                <div class="pf-modal" x-data="{ fileName: '', preview: '' }">
                    <div class="pf-modal__head">
                        <h3 class="pf-modal__title">{{ __('Загрузить аватар') }}</h3>
                        <button @click="showAvatarModal = false" class="pf-modal__close"><i class="fas fa-times"></i></button>
                    </div>
                    <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="pf-form-group">
                            <div class="pf-file"
                                 @dragover.prevent="$el.classList.add('dragover')"
                                 @dragleave.prevent="$el.classList.remove('dragover')"
                                 @drop.prevent="$el.classList.remove('dragover')">
                                <input type="file" name="avatar" accept="image/*"
                                       @change="const f = $event.target.files[0]; if(f){ fileName = f.name; preview = URL.createObjectURL(f); }">
                                <div class="pf-file__icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <p class="pf-file__text">{{ __('Нажмите или перетащите файл') }}</p>
                                <p class="pf-file__hint">PNG, JPG, GIF &bull; {{ __('максимум 2 МБ') }}</p>
                                <div class="pf-file__preview" :style="preview ? 'display:block' : ''">
                                    <img :src="preview" alt="preview">
                                    <p class="pf-file__preview-name" x-text="fileName"></p>
                                </div>
                            </div>
                        </div>
                        <div class="pf-form-actions">
                            <button type="button" @click="showAvatarModal = false" class="pf-btn pf-btn--ghost">{{ __('Отмена') }}</button>
                            <button type="submit" class="pf-btn pf-btn--primary">{{ __('Сохранить') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <h1 class="pf-hero__name">{{ $user->name }}</h1>
        <p class="pf-hero__sub">{{ $user->title ?? '' }}{{ $user->title && $user->location ? ' &bull; ' : '' }}{{ $user->location ?? '' }}</p>
        <div class="pf-hero__stats">
            <span><i class="fas fa-star"></i>Lv.{{ $user->level }} {{ $user->level_title }}</span>
            <span><i class="fas fa-trophy"></i>{{ number_format($user->total_xp) }} XP</span>
            <span><i class="fas fa-certificate"></i>{{ $user->certificates_count ?? 0 }} {{ __('сертификатов') }}</span>
        </div>
        <div class="pf-hero__links">
            <a href="{{ route('profile.show', $user->id) }}" class="pf-hero__link">
                <i class="fas fa-eye" style="margin-right:0.3rem"></i>{{ __('Публичный профиль') }}
            </a>
        </div>
    </div>
</div>

<div class="pf-body" x-data="profileApp()">
    <div class="pf-body__inner">
        @if(session('success'))
        <div style="margin-bottom:1.5rem;padding:0.75rem 1rem;background:color-mix(in srgb, var(--success) 10%, var(--card));border:1px solid color-mix(in srgb, var(--success) 30%, var(--card));border-radius:0.75rem;color:var(--success);font-size:0.85rem">{{ session('success') }}</div>
        @endif

        <div class="pf-tabs">
            @foreach(['overview' => __('Обзор'), 'experience' => __('Опыт'), 'education' => __('Образование'), 'portfolio' => __('Портфолио'), 'certificates' => __('Сертификаты'), 'activity' => __('Активность'), 'settings' => __('Настройки')] as $key => $label)
            <button @click="activeTab = '{{ $key }}'" :class="activeTab === '{{ $key }}' ? 'active' : ''" class="pf-tab">{{ $label }}</button>
            @endforeach
        </div>

        {{-- OVERVIEW --}}
        <div x-show="activeTab === 'overview'" x-cloak>
            <div class="pf-grid-2">
                <div style="display:flex;flex-direction:column;gap:1.5rem">
                    <div class="pf-card">
                        <div class="pf-card__header">
                            <h3 class="pf-card__title"><i class="fas fa-code" style="color:var(--accent);margin-right:0.4rem"></i>{{ __('Навыки') }}</h3>
                            <button @click="openModal('skill')" class="pf-card__add"><i class="fas fa-plus"></i></button>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:0.5rem">
                            @forelse($user->skills as $skill)
                            <span class="pf-skill">
                                {{ $skill->skill_name }}
                                @if($skill->is_verified)<i class="fas fa-check-circle"></i>@endif
                            </span>
                            @empty
                            <p style="font-size:0.85rem;color:var(--text-muted)">{{ __('Пока нет навыков') }}</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="pf-card">
                        <div class="pf-card__header">
                            <h3 class="pf-card__title"><i class="fas fa-chart-bar" style="color:var(--accent);margin-right:0.4rem"></i>{{ __('Статистика') }}</h3>
                        </div>
                        <div class="pf-stat-grid">
                            <div class="pf-stat">
                                <p class="pf-stat__val pf-stat__val--accent">{{ number_format($user->total_xp) }}</p>
                                <p class="pf-stat__label">{{ __('Всего XP') }}</p>
                            </div>
                            <div class="pf-stat">
                                <p class="pf-stat__val pf-stat__val--purple">{{ $stats->completed_courses ?? 0 }}</p>
                                <p class="pf-stat__label">{{ __('Курсов пройдено') }}</p>
                            </div>
                            <div class="pf-stat">
                                <p class="pf-stat__val pf-stat__val--yellow">{{ $user->certificates_count ?? 0 }}</p>
                                <p class="pf-stat__label">{{ __('Сертификатов') }}</p>
                            </div>
                            <div class="pf-stat">
                                <p class="pf-stat__val pf-stat__val--green">{{ $user->skills->count() }}</p>
                                <p class="pf-stat__label">{{ __('Навыков') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:1.5rem">
                    <div class="pf-card">
                        <div class="pf-card__header">
                            <h3 class="pf-card__title"><i class="fas fa-briefcase" style="color:var(--accent);margin-right:0.4rem"></i>{{ __('Опыт') }}</h3>
                            <button @click="openModal('experience')" class="pf-card__add"><i class="fas fa-plus"></i></button>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:0.75rem">
                            @forelse($user->experience as $exp)
                            <div class="pf-item">
                                <div class="pf-item__icon pf-item__icon--accent"><i class="fas fa-building"></i></div>
                                <div class="pf-item__body">
                                    <div class="pf-item__row">
                                        <span class="pf-item__name">{{ $exp->position }}</span>
                                        <form action="{{ route('profile.experience.delete', $exp->id) }}" method="POST" onsubmit="return confirm('{{ __('Удалить?') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="pf-del"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                    <p class="pf-item__sub">{{ $exp->company }} &bull; {{ $exp->start_date }} - {{ $exp->is_current ? __('Настоящее время') : ($exp->end_date ?? '') }}</p>
                                    @if($exp->description)<p class="pf-item__desc">{{ $exp->description }}</p>@endif
                                </div>
                            </div>
                            @empty
                            <div class="pf-empty"><i class="fas fa-briefcase"></i>{{ __('Пока нет опыта') }}</div>
                            @endforelse
                        </div>
                    </div>
                    <div class="pf-card">
                        <div class="pf-card__header">
                            <h3 class="pf-card__title"><i class="fas fa-graduation-cap" style="color:var(--accent-2);margin-right:0.4rem"></i>{{ __('Образование') }}</h3>
                            <button @click="openModal('education')" class="pf-card__add"><i class="fas fa-plus"></i></button>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:0.75rem">
                            @forelse($user->education as $edu)
                            <div class="pf-item">
                                <div class="pf-item__icon pf-item__icon--purple"><i class="fas fa-graduation-cap"></i></div>
                                <div class="pf-item__body">
                                    <div class="pf-item__row">
                                        <span class="pf-item__name">{{ $edu->degree }}{{ $edu->field ? ' &mdash; ' . $edu->field : '' }}</span>
                                        <form action="{{ route('profile.education.delete', $edu->id) }}" method="POST" onsubmit="return confirm('{{ __('Удалить?') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="pf-del"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                    <p class="pf-item__sub">{{ $edu->institution }} &bull; {{ $edu->start_date }} - {{ $edu->end_date ?? __('Настоящее время') }}</p>
                                </div>
                            </div>
                            @empty
                            <div class="pf-empty"><i class="fas fa-graduation-cap"></i>{{ __('Пока нет образования') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- EXPERIENCE --}}
        <div x-show="activeTab === 'experience'" x-cloak>
            <div style="display:flex;justify-content:flex-end;margin-bottom:1rem">
                <button @click="openModal('experience')" class="pf-btn pf-btn--primary"><i class="fas fa-plus" style="margin-right:0.3rem"></i>{{ __('Добавить опыт') }}</button>
            </div>
            <div style="display:flex;flex-direction:column;gap:1rem">
                @forelse($user->experience as $exp)
                <div class="pf-card">
                    <div class="pf-item" style="padding:0;background:transparent">
                        <div class="pf-item__icon pf-item__icon--accent"><i class="fas fa-building"></i></div>
                        <div class="pf-item__body">
                            <div class="pf-item__row">
                                <span class="pf-item__name" style="font-size:0.95rem">{{ $exp->position }}</span>
                                <form action="{{ route('profile.experience.delete', $exp->id) }}" method="POST" onsubmit="return confirm('{{ __('Удалить?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="pf-del"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                            <p class="pf-item__sub">{{ $exp->company }} &bull; {{ $exp->start_date }} - {{ $exp->is_current ? __('Настоящее время') : ($exp->end_date ?? '') }}</p>
                            @if($exp->description)<p class="pf-item__desc" style="margin-top:0.5rem">{{ $exp->description }}</p>@endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="pf-card pf-empty"><i class="fas fa-briefcase"></i>{{ __('Пока нет опыта') }}</div>
                @endforelse
            </div>
        </div>

        {{-- EDUCATION --}}
        <div x-show="activeTab === 'education'" x-cloak>
            <div style="display:flex;justify-content:flex-end;margin-bottom:1rem">
                <button @click="openModal('education')" class="pf-btn pf-btn--primary"><i class="fas fa-plus" style="margin-right:0.3rem"></i>{{ __('Добавить образование') }}</button>
            </div>
            <div style="display:flex;flex-direction:column;gap:1rem">
                @forelse($user->education as $edu)
                <div class="pf-card">
                    <div class="pf-item" style="padding:0;background:transparent">
                        <div class="pf-item__icon pf-item__icon--purple"><i class="fas fa-graduation-cap"></i></div>
                        <div class="pf-item__body">
                            <div class="pf-item__row">
                                <span class="pf-item__name" style="font-size:0.95rem">{{ $edu->degree }}{{ $edu->field ? ' &mdash; ' . $edu->field : '' }}</span>
                                <form action="{{ route('profile.education.delete', $edu->id) }}" method="POST" onsubmit="return confirm('{{ __('Удалить?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="pf-del"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                            <p class="pf-item__sub">{{ $edu->institution }} &bull; {{ $edu->start_date }} - {{ $edu->end_date ?? __('Настоящее время') }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="pf-card pf-empty"><i class="fas fa-graduation-cap"></i>{{ __('Пока нет образования') }}</div>
                @endforelse
            </div>
        </div>

        {{-- PORTFOLIO --}}
        <div x-show="activeTab === 'portfolio'" x-cloak>
            <div style="display:flex;justify-content:flex-end;margin-bottom:1rem">
                <button @click="openModal('portfolio')" class="pf-btn pf-btn--primary"><i class="fas fa-plus" style="margin-right:0.3rem"></i>{{ __('Добавить проект') }}</button>
            </div>
            <div class="pf-port-grid">
                @forelse($user->portfolio as $item)
                <div class="pf-port-card">
                    <div class="pf-port-card__img">
                        @if($item->image_url)
                        <img src="{{ asset('storage/' . $item->image_url) }}" alt="{{ $item->title }}">
                        @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center">
                            <i class="fas fa-globe"></i>
                        </div>
                        @endif
                    </div>
                    <div class="pf-port-card__body">
                        <div class="pf-port-card__row">
                            <span class="pf-port-card__title">{{ $item->title }}</span>
                            <form action="{{ route('profile.portfolio.delete', $item->id) }}" method="POST" onsubmit="return confirm('{{ __('Удалить?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="pf-del"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                        @if($item->description)<p class="pf-port-card__desc">{{ $item->description }}</p>@endif
                        <div class="pf-port-card__tags">
                            @if($item->category)<span class="pf-port-tag pf-port-tag--cat">{{ $item->category }}</span>@endif
                            @if($item->url)<a href="{{ $item->url }}" target="_blank" class="pf-port-tag pf-port-tag--link"><i class="fas fa-link" style="margin-right:0.2rem"></i>Link</a>@endif
                            @if($item->github_url)<a href="{{ $item->github_url }}" target="_blank" class="pf-port-tag pf-port-tag--gh"><i class="fab fa-github" style="margin-right:0.2rem"></i>GitHub</a>@endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="pf-card pf-empty" style="grid-column:1/-1"><i class="fas fa-folder-open"></i>{{ __('Пока нет проектов') }}</div>
                @endforelse
            </div>
        </div>

        {{-- CERTIFICATES --}}
        <div x-show="activeTab === 'certificates'" x-cloak>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                @forelse($certificates as $cert)
                <a href="{{ route('certificate.show', $cert->cert_hash) }}" class="pf-cert-card">
                    <div class="pf-cert-card__icon"><i class="fas fa-certificate"></i></div>
                    <div>
                        <p class="pf-cert-card__name">{{ $cert->certificate_name }}</p>
                        <p class="pf-cert-card__date">{{ $cert->course?->title ?? '' }} &bull; {{ $cert->issue_date?->format('M Y') ?? '' }}</p>
                    </div>
                </a>
                @empty
                <div class="pf-card pf-empty" style="grid-column:1/-1"><i class="fas fa-certificate"></i>{{ __('Пока нет сертификатов') }}</div>
                @endforelse
            </div>
        </div>

        {{-- ACTIVITY --}}
        <div x-show="activeTab === 'activity'" x-cloak>
            <div class="pf-card">
                <div style="display:flex;flex-direction:column;gap:0.25rem">
                    @forelse($recentActivity as $activity)
                    <div class="pf-act-item">
                        <div class="pf-act-dot"><i class="fas fa-check-circle"></i></div>
                        <div>
                            <p class="pf-act-text">{{ $activity->activity_text }}</p>
                            <p class="pf-act-time">{{ $activity->activity_time?->diffForHumans() ?? '' }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="pf-empty"><i class="fas fa-clock"></i>{{ __('Пока нет активности') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- SETTINGS --}}
        <div x-show="activeTab === 'settings'" x-cloak>
            <div style="display:flex;flex-direction:column;gap:1.5rem">
                <div class="pf-card">
                    <div class="pf-card__header">
                        <h3 class="pf-card__title"><i class="fas fa-user-edit" style="color:var(--accent);margin-right:0.4rem"></i>{{ __('Редактировать профиль') }}</h3>
                    </div>
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="pf-form-row">
                            <div class="pf-form-group">
                                <label class="pf-label">{{ __('Имя') }}</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="pf-input" required>
                            </div>
                            <div class="pf-form-group">
                                <label class="pf-label">{{ __('Должность') }}</label>
                                <input type="text" name="title" value="{{ old('title', $user->title) }}" class="pf-input" placeholder="Full Stack Developer">
                            </div>
                        </div>
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('Местоположение') }}</label>
                            <input type="text" name="location" value="{{ old('location', $user->location) }}" class="pf-input" placeholder="Душанбе">
                        </div>
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('О себе') }}</label>
                            <textarea name="bio" rows="3" class="pf-input">{{ old('bio', $user->bio) }}</textarea>
                        </div>
                        <div class="pf-form-row">
                            <div class="pf-form-group">
                                <label class="pf-label"><i class="fab fa-github"></i> GitHub</label>
                                <input type="url" name="github" value="{{ old('github', $user->github) }}" class="pf-input" placeholder="https://github.com/...">
                            </div>
                            <div class="pf-form-group">
                                <label class="pf-label"><i class="fab fa-linkedin"></i> LinkedIn</label>
                                <input type="url" name="linkedin" value="{{ old('linkedin', $user->linkedin) }}" class="pf-input" placeholder="https://linkedin.com/in/...">
                            </div>
                        </div>
                        <div class="pf-form-group">
                            <label class="pf-label"><i class="fas fa-globe"></i> {{ __('Сайт') }}</label>
                            <input type="url" name="website" value="{{ old('website', $user->website) }}" class="pf-input" placeholder="https://...">
                        </div>
                        <div class="pf-form-actions">
                            <button type="submit" class="pf-btn pf-btn--primary">{{ __('Сохранить') }}</button>
                        </div>
                    </form>
                </div>

                <div class="pf-card">
                    <div class="pf-card__header">
                        <h3 class="pf-card__title"><i class="fas fa-lock" style="color:var(--accent);margin-right:0.4rem"></i>{{ __('Изменить пароль') }}</h3>
                    </div>
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('Текущий пароль') }}</label>
                            <input type="password" name="current_password" class="pf-input" required>
                        </div>
                        <div class="pf-form-row">
                            <div class="pf-form-group">
                                <label class="pf-label">{{ __('Новый пароль') }}</label>
                                <input type="password" name="password" class="pf-input" required>
                            </div>
                            <div class="pf-form-group">
                                <label class="pf-label">{{ __('Подтверждение') }}</label>
                                <input type="password" name="password_confirmation" class="pf-input" required>
                            </div>
                        </div>
                        <div class="pf-form-actions">
                            <button type="submit" class="pf-btn pf-btn--primary">{{ __('Обновить пароль') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODALS --}}
        <div x-show="modalType === 'skill'" x-transition style="display:none" class="pf-overlay">
            <div class="pf-overlay__bg" @click="modalType = null"></div>
            <div class="pf-modal">
                <div class="pf-modal__head">
                    <h3 class="pf-modal__title">{{ __('Добавить навык') }}</h3>
                    <button @click="modalType = null" class="pf-modal__close"><i class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('profile.skill.add') }}" method="POST">
                    @csrf
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('Название') }}</label>
                        <input type="text" name="skill_name" class="pf-input" required placeholder="JavaScript">
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('Уровень') }}</label>
                        <select name="skill_level" class="pf-input">
                            <option value="beginner">{{ __('Начинающий') }}</option>
                            <option value="intermediate">{{ __('Средний') }}</option>
                            <option value="advanced">{{ __('Продвинутый') }}</option>
                            <option value="expert">{{ __('Эксперт') }}</option>
                        </select>
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('Категория') }}</label>
                        <select name="category" class="pf-input">
                            <option value="technical">{{ __('Технические') }}</option>
                            <option value="soft">{{ __('Гибкие навыки') }}</option>
                        </select>
                    </div>
                    <div class="pf-form-actions">
                        <button type="button" @click="modalType = null" class="pf-btn pf-btn--ghost">{{ __('Отмена') }}</button>
                        <button type="submit" class="pf-btn pf-btn--primary">{{ __('Добавить') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="modalType === 'experience'" x-transition style="display:none" class="pf-overlay">
            <div class="pf-overlay__bg" @click="modalType = null"></div>
            <div class="pf-modal" style="max-width:32rem">
                <div class="pf-modal__head">
                    <h3 class="pf-modal__title">{{ __('Добавить опыт') }}</h3>
                    <button @click="modalType = null" class="pf-modal__close"><i class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('profile.experience.add') }}" method="POST">
                    @csrf
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('Должность') }}</label>
                        <input type="text" name="position" class="pf-input" required placeholder="Senior Developer">
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('Компания') }}</label>
                        <input type="text" name="company" class="pf-input" required placeholder="TechCorp">
                    </div>
                    <div class="pf-form-row">
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('Дата начала') }}</label>
                            <input type="text" name="start_date" class="pf-input" required placeholder="2022-01">
                        </div>
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('Дата окончания') }}</label>
                            <input type="text" name="end_date" class="pf-input" placeholder="2024-01">
                        </div>
                    </div>
                    <label class="pf-chk">
                        <input type="checkbox" name="is_current" value="1">
                        <span>{{ __('Работаю здесь') }}</span>
                    </label>
                    <div class="pf-form-group" style="margin-top:0.75rem">
                        <label class="pf-label">{{ __('Описание') }}</label>
                        <textarea name="description" rows="3" class="pf-input"></textarea>
                    </div>
                    <div class="pf-form-actions">
                        <button type="button" @click="modalType = null" class="pf-btn pf-btn--ghost">{{ __('Отмена') }}</button>
                        <button type="submit" class="pf-btn pf-btn--primary">{{ __('Добавить') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="modalType === 'education'" x-transition style="display:none" class="pf-overlay">
            <div class="pf-overlay__bg" @click="modalType = null"></div>
            <div class="pf-modal" style="max-width:32rem">
                <div class="pf-modal__head">
                    <h3 class="pf-modal__title">{{ __('Добавить образование') }}</h3>
                    <button @click="modalType = null" class="pf-modal__close"><i class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('profile.education.add') }}" method="POST">
                    @csrf
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('Учебное заведение') }}</label>
                        <input type="text" name="institution" class="pf-input" required placeholder="Таджикский национальный университет">
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('Степень') }}</label>
                        <input type="text" name="degree" class="pf-input" required placeholder="Бакалавр компьютерных наук">
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('Специальность') }}</label>
                        <input type="text" name="field" class="pf-input" placeholder="Информатика">
                    </div>
                    <div class="pf-form-row">
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('Дата начала') }}</label>
                            <input type="text" name="start_date" class="pf-input" required placeholder="2018-09">
                        </div>
                        <div class="pf-form-group">
                            <label class="pf-label">{{ __('Дата окончания') }}</label>
                            <input type="text" name="end_date" class="pf-input" placeholder="2022-06">
                        </div>
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('Описание') }}</label>
                        <textarea name="description" rows="3" class="pf-input"></textarea>
                    </div>
                    <div class="pf-form-actions">
                        <button type="button" @click="modalType = null" class="pf-btn pf-btn--ghost">{{ __('Отмена') }}</button>
                        <button type="submit" class="pf-btn pf-btn--primary">{{ __('Добавить') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="modalType === 'portfolio'" x-transition style="display:none" class="pf-overlay">
            <div class="pf-overlay__bg" @click="modalType = null"></div>
            <div class="pf-modal" style="max-width:32rem">
                <div class="pf-modal__head">
                    <h3 class="pf-modal__title">{{ __('Добавить проект') }}</h3>
                    <button @click="modalType = null" class="pf-modal__close"><i class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('profile.portfolio.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('Название') }}</label>
                        <input type="text" name="title" class="pf-input" required placeholder="E-Commerce Platform">
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('Описание') }}</label>
                        <textarea name="description" rows="3" class="pf-input"></textarea>
                    </div>
                    <div class="pf-form-row">
                        <div class="pf-form-group">
                            <label class="pf-label">URL</label>
                            <input type="url" name="url" class="pf-input" placeholder="https://...">
                        </div>
                        <div class="pf-form-group">
                            <label class="pf-label">GitHub URL</label>
                            <input type="url" name="github_url" class="pf-input" placeholder="https://github.com/...">
                        </div>
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('Категория') }}</label>
                        <input type="text" name="category" class="pf-input" placeholder="Web App, Mobile...">
                    </div>
                    <div class="pf-form-group">
                        <label class="pf-label">{{ __('Изображение') }}</label>
                        <div class="pf-file"
                             @dragover.prevent="$el.classList.add('dragover')"
                             @dragleave.prevent="$el.classList.remove('dragover')"
                             @drop.prevent="$el.classList.remove('dragover')"
                             x-data="{ fileName: '', preview: '' }">
                            <input type="file" name="image" accept="image/*"
                                   @change="const f = $event.target.files[0]; if(f){ fileName = f.name; preview = URL.createObjectURL(f); }">
                            <div class="pf-file__icon"><i class="fas fa-image"></i></div>
                            <p class="pf-file__text">{{ __('Нажмите или перетащите файл') }}</p>
                            <p class="pf-file__hint">PNG, JPG, GIF &bull; {{ __('максимум 5 МБ') }}</p>
                            <div class="pf-file__preview" :style="preview ? 'display:block' : ''">
                                <img :src="preview" alt="preview">
                                <p class="pf-file__preview-name" x-text="fileName"></p>
                            </div>
                        </div>
                    </div>
                    <div class="pf-form-actions">
                        <button type="button" @click="modalType = null" class="pf-btn pf-btn--ghost">{{ __('Отмена') }}</button>
                        <button type="submit" class="pf-btn pf-btn--primary">{{ __('Добавить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function profileApp() {
    return {
        activeTab: 'overview',
        modalType: null,
        openModal(type) {
            this.modalType = type;
        }
    };
}
</script>
@endsection
