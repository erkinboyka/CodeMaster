@extends('layouts.app')

@section('title', ($user->name ?? 'User') . ' - ' . __('Профиль') . ' - CodeMaster')

@section('head')
<style>
    .ps-hero {
        position: relative; overflow: hidden;
        padding: 5rem 2rem 4rem; text-align: center;
        background: var(--gradient);
    }
    .ps-hero::before {
        content: ''; position: absolute; inset: -50%; width: 200%; height: 200%;
        background: radial-gradient(ellipse at 30% 50%, rgba(255,255,255,0.12) 0%, transparent 50%),
                    radial-gradient(ellipse at 70% 50%, rgba(255,255,255,0.08) 0%, transparent 50%);
        animation: ps-pulse 8s ease-in-out infinite alternate;
    }
    @keyframes ps-pulse { 0% { transform: scale(1); } 100% { transform: scale(1.1) rotate(2deg); } }
    .ps-hero__content { position: relative; z-index: 1; }
    .ps-avatar {
        width: 7rem; height: 7rem; border-radius: 1rem; object-fit: cover;
        border: 4px solid rgba(255,255,255,0.3); box-shadow: 0 8px 32px rgba(0,0,0,0.25);
        margin-bottom: 1.25rem;
    }
    .ps-hero__name { font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 800; color: #fff; margin-bottom: 0.25rem; }
    .ps-hero__sub { color: rgba(255,255,255,0.75); font-size: 0.95rem; margin-bottom: 0.75rem; }
    .ps-hero__stats {
        display: flex; flex-wrap: wrap; justify-content: center; gap: 1.25rem;
        color: rgba(255,255,255,0.65); font-size: 0.85rem;
    }
    .ps-hero__stats span { display: inline-flex; align-items: center; gap: 0.35rem; }
    .ps-hero__links { display: flex; justify-content: center; gap: 0.75rem; margin-top: 1rem; }
    .ps-hero__link {
        padding: 0.4rem 1rem; border-radius: 0.75rem;
        background: rgba(255,255,255,0.15); color: #fff; font-size: 0.85rem; font-weight: 500;
        text-decoration: none; transition: all 0.2s;
    }
    .ps-hero__link:hover { background: rgba(255,255,255,0.25); }
    .ps-body { padding: 3rem 1.5rem; }
    .ps-body__inner { max-width: 1000px; margin: 0 auto; }
    .ps-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; }
    .ps-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius-lg); padding: 1.5rem;
    }
    .ps-card__title {
        font-size: 1rem; font-weight: 700; color: var(--text); margin-bottom: 1rem;
    }
    .ps-card__title i { margin-right: 0.4rem; }
    .ps-item {
        display: flex; gap: 1rem; padding: 1rem;
        background: var(--bg-2); border-radius: var(--radius-md); transition: background 0.2s;
    }
    .ps-item:hover { background: color-mix(in srgb, var(--accent) 5%, var(--bg-2)); }
    .ps-item__icon {
        width: 2.5rem; height: 2.5rem; border-radius: 0.5rem;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.9rem;
    }
    .ps-item__icon--accent { background: color-mix(in srgb, var(--accent) 15%, var(--card)); color: var(--accent); }
    .ps-item__icon--purple { background: color-mix(in srgb, var(--accent-2) 15%, var(--card)); color: var(--accent-2); }
    .ps-item__icon--yellow { background: color-mix(in srgb, #f59e0b 15%, var(--card)); color: #f59e0b; }
    .ps-item__icon--green { background: color-mix(in srgb, #10b981 15%, var(--card)); color: #10b981; }
    .ps-item__name { font-size: 0.9rem; font-weight: 600; color: var(--text); }
    .ps-item__sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem; }
    .ps-item__desc { font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.35rem; line-height: 1.5; }
    .ps-item-list { display: flex; flex-direction: column; gap: 0.75rem; }
    .ps-skill {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.4rem 0.85rem; border-radius: 9999px;
        background: color-mix(in srgb, var(--accent) 12%, var(--card));
        color: var(--accent); font-size: 0.8rem; font-weight: 500;
    }
    .ps-skill i { font-size: 0.6rem; color: var(--success); }
    .ps-stat-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.6rem 0;
    }
    .ps-stat-row + .ps-stat-row { border-top: 1px solid var(--border); }
    .ps-stat-row__label { font-size: 0.85rem; color: var(--text-muted); }
    .ps-stat-row__val { font-size: 0.85rem; font-weight: 700; }
    .ps-stat-row__val--accent { color: var(--accent); }
    .ps-stat-row__val--purple { color: var(--accent-2); }
    .ps-stat-row__val--green { color: #10b981; }
    .ps-stat-row__val--yellow { color: #f59e0b; }
    .ps-progress { margin-top: 1rem; }
    .ps-progress__labels { display: flex; justify-content: space-between; font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.3rem; }
    .ps-progress__bar {
        width: 100%; height: 0.5rem; border-radius: 9999px;
        background: var(--bg-2); overflow: hidden;
    }
    .ps-progress__fill {
        height: 100%; border-radius: 9999px;
        background: linear-gradient(to right, var(--accent), var(--accent-2));
        transition: width 0.5s;
    }
    .ps-progress__detail { font-size: 0.65rem; color: var(--text-muted); text-align: center; margin-top: 0.3rem; }
    .ps-port-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .ps-port-card {
        border: 1px solid var(--border); border-radius: var(--radius-md);
        overflow: hidden; transition: all 0.3s;
    }
    .ps-port-card:hover { transform: translateY(-2px); box-shadow: var(--card-shadow); }
    .ps-port-card__img {
        height: 8rem; overflow: hidden;
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
    }
    .ps-port-card__img img { width: 100%; height: 100%; object-fit: cover; }
    .ps-port-card__body { padding: 1rem; }
    .ps-port-card__title { font-size: 0.85rem; font-weight: 600; color: var(--text); }
    .ps-port-card__desc { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; }
    .ps-port-card__tags { display: flex; gap: 0.35rem; margin-top: 0.5rem; flex-wrap: wrap; }
    .ps-port-tag { padding: 0.15rem 0.45rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 500; }
    .ps-port-tag--cat { background: color-mix(in srgb, var(--accent) 12%, var(--card)); color: var(--accent); }
    .ps-cert-card {
        display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem;
        background: var(--bg-2); border-radius: var(--radius-md);
        text-decoration: none; transition: all 0.2s;
    }
    .ps-cert-card:hover { background: color-mix(in srgb, var(--accent) 5%, var(--bg-2)); }
    .ps-cert-card__icon {
        width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; flex-shrink: 0;
        background: linear-gradient(135deg, #f59e0b, #f97316);
        display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem;
    }
    .ps-cert-card__name { font-size: 0.8rem; font-weight: 600; color: var(--text); }
    .ps-cert-card__date { font-size: 0.7rem; color: var(--text-muted); }
    .ps-act-item {
        display: flex; align-items: start; gap: 0.75rem; padding: 0.5rem 0;
    }
    .ps-act-item + .ps-act-item { border-top: 1px solid var(--border); }
    .ps-act-dot {
        width: 0.5rem; height: 0.5rem; border-radius: 50%; flex-shrink: 0;
        background: var(--accent); margin-top: 0.4rem;
    }
    .ps-act-text { font-size: 0.85rem; color: var(--text); }
    .ps-act-time { font-size: 0.7rem; color: var(--text-muted); margin-top: 0.15rem; }
    @media (max-width: 768px) {
        .ps-grid { grid-template-columns: 1fr; }
        .ps-port-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="ps-hero">
    <div class="ps-hero__content">
        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'U') . '&background=fff&color=6366f1&size=128' }}" class="ps-avatar" alt="{{ $user->name }}">
        <h1 class="ps-hero__name">{{ $user->name }}</h1>
        <p class="ps-hero__sub">{{ $user->title ?? '' }}{{ $user->title && $user->location ? ' &bull; ' : '' }}{{ $user->location ?? '' }}</p>
        <div class="ps-hero__stats">
            <span><i class="fas fa-star"></i>Lv.{{ $user->level }} {{ $user->level_title }}</span>
            <span><i class="fas fa-trophy"></i>{{ number_format($user->total_xp) }} XP</span>
            <span><i class="fas fa-certificate"></i>{{ $user->certificates_count ?? 0 }} {{ __('сертификатов') }}</span>
        </div>
        <div class="ps-hero__links">
            @if($user->github)
            <a href="{{ $user->github }}" target="_blank" class="ps-hero__link"><i class="fab fa-github" style="margin-right:0.3rem"></i>GitHub</a>
            @endif
            @if($user->linkedin)
            <a href="{{ $user->linkedin }}" target="_blank" class="ps-hero__link"><i class="fab fa-linkedin" style="margin-right:0.3rem"></i>LinkedIn</a>
            @endif
            @if($user->website)
            <a href="{{ $user->website }}" target="_blank" class="ps-hero__link"><i class="fas fa-globe" style="margin-right:0.3rem"></i>{{ __('Сайт') }}</a>
            @endif
        </div>
    </div>
</div>

<div class="ps-body">
    <div class="ps-body__inner">
        <div class="ps-grid">
            <div style="display:flex;flex-direction:column;gap:1.5rem">
                @if($user->bio)
                <div class="ps-card">
                    <h3 class="ps-card__title"><i class="fas fa-user" style="color:var(--accent)"></i>{{ __('О себе') }}</h3>
                    <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.7">{{ $user->bio }}</p>
                </div>
                @endif

                @if($user->experience->count())
                <div class="ps-card">
                    <h3 class="ps-card__title"><i class="fas fa-briefcase" style="color:var(--accent)"></i>{{ __('Опыт работы') }}</h3>
                    <div class="ps-item-list">
                        @foreach($user->experience as $exp)
                        <div class="ps-item">
                            <div class="ps-item__icon ps-item__icon--accent"><i class="fas fa-building"></i></div>
                            <div>
                                <div class="ps-item__name">{{ $exp->position }}</div>
                                <div class="ps-item__sub">{{ $exp->company }} &bull; {{ $exp->start_date }} - {{ $exp->is_current ? __('Настоящее время') : ($exp->end_date ?? '') }}</div>
                                @if($exp->description)<p class="ps-item__desc">{{ $exp->description }}</p>@endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($user->education->count())
                <div class="ps-card">
                    <h3 class="ps-card__title"><i class="fas fa-graduation-cap" style="color:var(--accent-2)"></i>{{ __('Образование') }}</h3>
                    <div class="ps-item-list">
                        @foreach($user->education as $edu)
                        <div class="ps-item">
                            <div class="ps-item__icon ps-item__icon--purple"><i class="fas fa-graduation-cap"></i></div>
                            <div>
                                <div class="ps-item__name">{{ $edu->degree }}{{ $edu->field ? ' &mdash; ' . $edu->field : '' }}</div>
                                <div class="ps-item__sub">{{ $edu->institution }} &bull; {{ $edu->start_date }} - {{ $edu->end_date ?? __('Настоящее время') }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($user->portfolio->count())
                <div class="ps-card">
                    <h3 class="ps-card__title"><i class="fas fa-folder-open" style="color:var(--accent)"></i>{{ __('Портфолио') }}</h3>
                    <div class="ps-port-grid">
                        @foreach($user->portfolio as $item)
                        <div class="ps-port-card">
                            <div class="ps-port-card__img">
                                @if($item->image_url)
                                <img src="{{ asset('storage/' . $item->image_url) }}" alt="{{ $item->title }}">
                                @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center">
                                    <i class="fas fa-globe" style="font-size:2rem;color:rgba(255,255,255,0.25)"></i>
                                </div>
                                @endif
                            </div>
                            <div class="ps-port-card__body">
                                <div class="ps-port-card__title">{{ $item->title }}</div>
                                @if($item->description)<p class="ps-port-card__desc">{{ $item->description }}</p>@endif
                                <div class="ps-port-card__tags">
                                    @if($item->category)<span class="ps-port-tag ps-port-tag--cat">{{ $item->category }}</span>@endif
                                    @if($item->url)<a href="{{ $item->url }}" target="_blank" class="ps-port-tag" style="background:color-mix(in srgb, #3b82f6 12%, var(--card));color:#3b82f6"><i class="fas fa-link" style="margin-right:0.15rem"></i>Link</a>@endif
                                    @if($item->github_url)<a href="{{ $item->github_url }}" target="_blank" class="ps-port-tag" style="background:var(--bg-2);color:var(--text-muted)"><i class="fab fa-github" style="margin-right:0.15rem"></i>GitHub</a>@endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($certificates->count())
                <div class="ps-card">
                    <h3 class="ps-card__title"><i class="fas fa-certificate" style="color:#f59e0b"></i>{{ __('Сертификаты') }}</h3>
                    <div style="display:flex;flex-direction:column;gap:0.5rem">
                        @foreach($certificates as $cert)
                        <a href="{{ route('certificate.show', $cert->cert_hash) }}" class="ps-cert-card">
                            <div class="ps-cert-card__icon"><i class="fas fa-certificate"></i></div>
                            <div>
                                <p class="ps-cert-card__name">{{ $cert->certificate_name }}</p>
                                <p class="ps-cert-card__date">{{ $cert->issue_date?->format('M Y') ?? '' }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div style="display:flex;flex-direction:column;gap:1.5rem">
                @if($user->skills->count())
                <div class="ps-card">
                    <h3 class="ps-card__title"><i class="fas fa-code" style="color:var(--accent)"></i>{{ __('Навыки') }}</h3>
                    <div style="display:flex;flex-wrap:wrap;gap:0.5rem">
                        @foreach($user->skills as $skill)
                        <span class="ps-skill">
                            {{ $skill->skill_name }}
                            @if($skill->is_verified)<i class="fas fa-check-circle"></i>@endif
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="ps-card">
                    <h3 class="ps-card__title"><i class="fas fa-chart-bar" style="color:var(--accent)"></i>{{ __('Статистика') }}</h3>
                    <div class="ps-stat-row">
                        <span class="ps-stat-row__label">{{ __('Всего XP') }}</span>
                        <span class="ps-stat-row__val ps-stat-row__val--accent">{{ number_format($user->total_xp) }}</span>
                    </div>
                    <div class="ps-stat-row">
                        <span class="ps-stat-row__label">{{ __('Уровень') }}</span>
                        <span class="ps-stat-row__val ps-stat-row__val--purple">{{ $user->level_badge }} {{ $user->level_title }} ({{ $user->level }})</span>
                    </div>
                    <div class="ps-stat-row">
                        <span class="ps-stat-row__label">{{ __('Курсов пройдено') }}</span>
                        <span class="ps-stat-row__val ps-stat-row__val--green">{{ $stats->completed_courses ?? 0 }}</span>
                    </div>
                    <div class="ps-stat-row">
                        <span class="ps-stat-row__label">{{ __('Сертификатов') }}</span>
                        <span class="ps-stat-row__val ps-stat-row__val--yellow">{{ $user->certificates_count ?? 0 }}</span>
                    </div>
                    <div class="ps-progress">
                        <div class="ps-progress__labels">
                            <span>Lv.{{ $user->level }}</span>
                            <span>Lv.{{ $user->level + 1 }}</span>
                        </div>
                        <div class="ps-progress__bar">
                            <div class="ps-progress__fill" style="width:{{ $user->level_progress }}%"></div>
                        </div>
                        <p class="ps-progress__detail">{{ $user->xp_for_current_level }}/{{ $user->xp_for_next_level }} XP</p>
                    </div>
                </div>

                @if($recentActivity->count())
                <div class="ps-card">
                    <h3 class="ps-card__title"><i class="fas fa-clock" style="color:var(--accent)"></i>{{ __('Активность') }}</h3>
                    @foreach($recentActivity->take(5) as $activity)
                    <div class="ps-act-item">
                        <div class="ps-act-dot"></div>
                        <div>
                            <p class="ps-act-text">{{ $activity->activity_text }}</p>
                            <p class="ps-act-time">{{ $activity->activity_time?->diffForHumans() ?? '' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
