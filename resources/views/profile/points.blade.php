@extends('layouts.app')
@section('title', __('Points & XP'))

@section('head')
<style>
    .px-hero {
        position: relative; overflow: hidden;
        padding: 3rem 2rem 2.5rem; text-align: center;
        background: var(--gradient);
    }
    .px-hero::before {
        content: ''; position: absolute; inset: -50%; width: 200%; height: 200%;
        background: radial-gradient(ellipse at 30% 50%, rgba(255,255,255,0.12) 0%, transparent 50%),
                    radial-gradient(ellipse at 70% 50%, rgba(255,255,255,0.08) 0%, transparent 50%);
        animation: px-pulse 8s ease-in-out infinite alternate;
    }
    @keyframes px-pulse { 0%{transform:scale(1)} 100%{transform:scale(1.1) rotate(2deg)} }
    .px-hero__inner { position: relative; z-index: 1; }
    .px-hero__badge {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.45rem 1.1rem; border-radius: 9999px;
        background: rgba(255,255,255,0.15); color: #fff;
        font-size: 0.82rem; font-weight: 600; margin-bottom: 0.8rem;
        backdrop-filter: blur(4px);
    }
    .px-hero__xp { font-size: clamp(2.5rem, 5vw, 3.5rem); font-weight: 900; color: #fff; line-height: 1; }
    .px-hero__label { color: rgba(255,255,255,0.7); font-size: 0.88rem; margin-top: 0.3rem; }
    .px-hero__stats { display: flex; justify-content: center; gap: 2rem; margin-top: 1.25rem; }
    .px-hero__stat { text-align: center; }
    .px-hero__stat-val { font-size: 1.25rem; font-weight: 800; color: #fff; }
    .px-hero__stat-lbl { font-size: 0.72rem; color: rgba(255,255,255,0.6); margin-top: 2px; }

    .px-body { padding: 1.5rem 1.5rem 2rem; max-width: 1000px; margin: 0 auto; }

    .px-progress-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 1rem; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem;
    }
    .px-progress-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem; }
    .px-progress-title { font-size: 0.88rem; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 0.5rem; }
    .px-progress-sub { font-size: 0.78rem; color: var(--text-muted); }
    .px-bar { height: 8px; border-radius: 4px; background: var(--border); overflow: hidden; }
    .px-bar__fill {
        height: 100%; border-radius: 4px;
        background: linear-gradient(90deg, var(--accent), var(--accent-2, #a855f7));
        transition: width 0.8s cubic-bezier(0.4,0,0.2,1);
        position: relative;
    }
    .px-bar__fill::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.3) 50%, transparent 100%);
        animation: px-shimmer 2s infinite;
    }
    @keyframes px-shimmer { 0%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
    .px-bar-labels { display: flex; justify-content: space-between; margin-top: 0.4rem; }
    .px-bar-label { font-size: 0.72rem; color: var(--text-muted); }

    .px-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.15rem; }
    @media(max-width:640px){ .px-grid{grid-template-columns:1fr} }

    .px-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 1rem; overflow: hidden; transition: all 0.25s;
    }
    .px-card:hover { border-color: color-mix(in srgb, var(--accent) 30%, var(--border)); box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
    .px-card__head {
        padding: 0.9rem 1.2rem; display: flex; align-items: center; gap: 0.55rem;
        border-bottom: 1px solid var(--border); font-size: 0.88rem; font-weight: 700; color: var(--text);
    }
    .px-card__head i { font-size: 0.95rem; }
    .px-card__body { padding: 0.6rem 1.1rem 0.9rem; }

    .px-row {
        display: flex; align-items: center; gap: 0.65rem;
        padding: 0.5rem 0.65rem; border-radius: 0.55rem;
        transition: background 0.2s;
    }
    .px-row:hover { background: var(--bg-2); }
    .px-row__icon {
        width: 1.8rem; height: 1.8rem; border-radius: 0.45rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.72rem; flex-shrink: 0;
    }
    .px-row__text { flex: 1; font-size: 0.8rem; color: var(--text-secondary); }
    .px-row__val { font-size: 0.75rem; font-weight: 700; white-space: nowrap; }

    .px-lvl {
        display: flex; align-items: center; gap: 0.65rem;
        padding: 0.6rem 0.65rem; border-radius: 0.55rem;
        transition: all 0.2s; border: 1px solid transparent;
    }
    .px-lvl:hover { background: var(--bg-2); }
    .px-lvl.current {
        background: color-mix(in srgb, var(--accent) 8%, var(--card));
        border-color: color-mix(in srgb, var(--accent) 25%, var(--border));
    }
    .px-lvl__icon {
        width: 2rem; height: 2rem; border-radius: 0.45rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.82rem; flex-shrink: 0;
    }
    .px-lvl__name { font-size: 0.82rem; font-weight: 600; color: var(--text); }
    .px-lvl__range { font-size: 0.7rem; color: var(--text-muted); margin-top: 1px; }
    .px-lvl__badge { margin-left: auto; font-size: 0.68rem; font-weight: 700; padding: 0.18rem 0.55rem; border-radius: 9999px; }
    .px-lvl__badge--active { background: color-mix(in srgb, var(--accent) 15%, var(--card)); color: var(--accent); }
    .px-lvl__badge--locked { background: var(--bg-2); color: var(--text-muted); }

    .px-full { grid-column: 1 / -1; }

    .px-history { margin-top: 1.25rem; }
    .px-history__header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 0.75rem;
    }
    .px-history__title {
        font-size: 0.95rem; font-weight: 700; color: var(--text);
        display: flex; align-items: center; gap: 0.5rem;
    }
    .px-history__filters { display: flex; gap: 0.4rem; }
    .px-history__filter {
        padding: 0.3rem 0.7rem; border-radius: 9999px; font-size: 0.72rem;
        font-weight: 600; border: 1px solid var(--border); background: var(--card);
        color: var(--text-muted); cursor: pointer; transition: all 0.2s;
    }
    .px-history__filter:hover, .px-history__filter.active {
        background: color-mix(in srgb, var(--accent) 10%, var(--card));
        border-color: color-mix(in srgb, var(--accent) 30%, var(--border));
        color: var(--accent);
    }
    .px-history__list { display: flex; flex-direction: column; gap: 0.3rem; }
    .px-history__item {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.65rem 0.85rem; border-radius: 0.6rem;
        background: var(--card); border: 1px solid var(--border);
        transition: all 0.2s;
    }
    .px-history__item:hover { border-color: color-mix(in srgb, var(--accent) 20%, var(--border)); }
    .px-history__icon {
        width: 2rem; height: 2rem; border-radius: 0.5rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.78rem; flex-shrink: 0;
    }
    .px-history__icon--plus {
        background: color-mix(in srgb, #22c55e 12%, var(--card));
        color: #22c55e;
    }
    .px-history__icon--minus {
        background: color-mix(in srgb, #ef4444 12%, var(--card));
        color: #ef4444;
    }
    .px-history__info { flex: 1; min-width: 0; }
    .px-history__text {
        font-size: 0.82rem; font-weight: 600; color: var(--text);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .px-history__time { font-size: 0.7rem; color: var(--text-muted); margin-top: 1px; }
    .px-history__amount {
        font-size: 0.82rem; font-weight: 800; white-space: nowrap;
    }
    .px-history__amount--plus { color: #22c55e; }
    .px-history__amount--minus { color: #ef4444; }
    .px-history__empty {
        text-align: center; padding: 2rem 1rem; color: var(--text-muted);
        font-size: 0.85rem;
    }
    .px-history__empty i { font-size: 1.5rem; margin-bottom: 0.5rem; display: block; opacity: 0.4; }

    .px-tabs { display: flex; gap: 0; margin-bottom: 1rem; background: var(--card); border: 1px solid var(--border); border-radius: 0.75rem; overflow: hidden; }
    .px-tab {
        flex: 1; padding: 0.65rem 1rem; text-align: center; font-size: 0.82rem;
        font-weight: 600; color: var(--text-muted); cursor: pointer; transition: all 0.2s;
        border: none; background: none;
    }
    .px-tab:hover { color: var(--text); background: var(--bg-2); }
    .px-tab.active {
        color: var(--accent); background: color-mix(in srgb, var(--accent) 8%, var(--card));
        box-shadow: inset 0 -2px 0 var(--accent);
    }
</style>
@endsection

@section('content')
<div class="px-hero">
    <div class="px-hero__inner">
        <div class="px-hero__badge">
            <span style="font-size:1.05rem">{!! $user->level_badge !!}</span>
{{ $user->level_title ?? __('Beginner_title') }}
        </div>
        <div class="px-hero__xp">{{ number_format($user->total_xp ?? 0) }}</div>
        <div class="px-hero__label">{{ __('Total XP Earned') }}</div>
        <div class="px-hero__stats">
            <div class="px-hero__stat">
                <div class="px-hero__stat-val">{{ $user->level ?? 1 }}</div>
                <div class="px-hero__stat-lbl">{{ __('Level') }}</div>
            </div>
            <div class="px-hero__stat">
                <div class="px-hero__stat-val">{{ number_format($user->ai_tokens ?? 0) }}</div>
                <div class="px-hero__stat-lbl">{{ __('AI Tokens') }}</div>
            </div>
            <div class="px-hero__stat">
                <div class="px-hero__stat-val">{{ $user->streak_count ?? 0 }}</div>
                <div class="px-hero__stat-lbl">{{ __('Day Streak') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="px-body">
    @php
        $xpForNext = $user->xp_for_next_level ?? 100;
        $xpForCurrent = $user->xp_for_current_level ?? 0;
        $progress = $xpForNext > 0 ? round(($xpForCurrent / $xpForNext) * 100) : 0;
    @endphp
    <div class="px-progress-card">
        <div class="px-progress-header">
            <div class="px-progress-title">
                <i class="fas fa-layer-group" style="color:var(--accent)"></i>
                {{ __('Level') }} {{ $user->level ?? 1 }}
            </div>
            <div class="px-progress-sub">{{ $xpForCurrent }} / {{ $xpForNext }} XP</div>
        </div>
        <div class="px-bar">
            <div class="px-bar__fill" style="width:{{ $progress }}%"></div>
        </div>
        <div class="px-bar-labels">
            <span class="px-bar-label">{{ $user->level_title ?? __('Beginner_title') }}</span>
            <span class="px-bar-label">Lv.{{ ($user->level ?? 1) + 1 }}</span>
        </div>
    </div>

    <div class="px-grid">
        <div class="px-card">
            <div class="px-card__head">
                <i class="fas fa-bolt" style="color:var(--accent)"></i>
                {{ __('Earn XP') }}
            </div>
            <div class="px-card__body">
                <div class="px-row">
                    <div class="px-row__icon" style="background:color-mix(in srgb,var(--accent) 12%,var(--card));color:var(--accent)">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <span class="px-row__text">{{ __('Per lesson') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">+10 XP</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:color-mix(in srgb,#f59e0b 12%,var(--card));color:#f59e0b">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <span class="px-row__text">{{ __('Per test') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">+25 XP</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:color-mix(in srgb,#3b82f6 12%,var(--card));color:#3b82f6">
                        <i class="fas fa-code"></i>
                    </div>
                    <span class="px-row__text">{{ __('Per practice') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">+30 XP</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:color-mix(in srgb,#8b5cf6 12%,var(--card));color:#8b5cf6">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <span class="px-row__text">{{ __('Per exam') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">+50 XP</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:color-mix(in srgb,#22c55e 12%,var(--card));color:#22c55e">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="px-row__text">{{ __('Per course') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">+100 XP</span>
                </div>
            </div>
        </div>

        <div class="px-card">
            <div class="px-card__head">
                <i class="fas fa-robot" style="color:#8b5cf6"></i>
                {{ __('AI Tokens') }}
            </div>
            <div class="px-card__body">
                <div class="px-row">
                    <div class="px-row__icon" style="background:color-mix(in srgb,#f59e0b 12%,var(--card));color:#f59e0b">
                        <i class="fas fa-coins"></i>
                    </div>
                    <span class="px-row__text">{{ __('Starting balance') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">25 {{ __('tokens') }}</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:color-mix(in srgb,#ef4444 12%,var(--card));color:#ef4444">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <span class="px-row__text">{{ __('Per AI message') }}</span>
                    <span class="px-row__val" style="color:#ef4444">-1 {{ __('token') }}</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:color-mix(in srgb,#22c55e 12%,var(--card));color:#22c55e">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <span class="px-row__text">{{ __('Daily bonus') }}</span>
                    <span class="px-row__val" style="color:var(--accent)">+5 {{ __('tokens') }}</span>
                </div>
                <div class="px-row">
                    <div class="px-row__icon" style="background:color-mix(in srgb,#3b82f6 12%,var(--card));color:#3b82f6">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <span class="px-row__text">{{ __('Earn tokens for activity') }}</span>
                    <span class="px-row__val" style="color:var(--accent)"><i class="fas fa-arrow-right" style="font-size:0.6rem"></i></span>
                </div>
            </div>
        </div>

        <div class="px-card px-full">
            <div class="px-card__head">
                <i class="fas fa-layer-group" style="color:var(--accent)"></i>
                {{ __('Levels') }}
            </div>
            <div class="px-card__body" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0.45rem">
                @php
                    $levels = [
                        ['name' => __('Beginner_title'), 'icon' => 'fa-seedling', 'color' => '#22c55e', 'min' => 1, 'max' => 4],
                        ['name' => __('Student'), 'icon' => 'fa-graduation-cap', 'color' => '#3b82f6', 'min' => 5, 'max' => 9],
                        ['name' => __('Experienced'), 'icon' => 'fa-star', 'color' => '#8b5cf6', 'min' => 10, 'max' => 14],
                        ['name' => __('Advanced_title'), 'icon' => 'fa-fire', 'color' => '#f97316', 'min' => 15, 'max' => 29],
                        ['name' => __('Expert'), 'icon' => 'fa-crown', 'color' => '#eab308', 'min' => 30, 'max' => 999],
                    ];
                    $currentLevel = $user->level ?? 1;
                @endphp
                @foreach($levels as $lvl)
                <div class="px-lvl {{ $currentLevel >= $lvl['min'] && $currentLevel <= $lvl['max'] ? 'current' : '' }}">
                    <div class="px-lvl__icon" style="background:color-mix(in srgb,{$lvl['color']} 12%,var(--card));color:{$lvl['color']}">
                        <i class="fas {$lvl['icon']}"></i>
                    </div>
                    <div>
                        <div class="px-lvl__name">{{ $lvl['name'] }}</div>
                        <div class="px-lvl__range">Lv.{{ $lvl['min'] }}{{ $lvl['max'] < 999 ? '–' . $lvl['max'] : '+' }}</div>
                    </div>
                    @if($currentLevel >= $lvl['min'])
                        <span class="px-lvl__badge px-lvl__badge--active">{{ $currentLevel >= $lvl['min'] && $currentLevel <= $lvl['max'] ? __('You') : '✓' }}</span>
                    @else
                        <span class="px-lvl__badge px-lvl__badge--locked"><i class="fas fa-lock" style="font-size:0.55rem"></i></span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div class="px-card px-full px-history" x-data="pxHistory()">
            <div class="px-card__head">
                <i class="fas fa-clock-rotate-left" style="color:var(--accent)"></i>
                {{ __('History') }}
            </div>
            <div class="px-card__body" style="padding:0.75rem 1.1rem 1rem">
                <div class="px-history__filters">
                    <button class="px-history__filter" :class="filter === 'all' && 'active'" @click="filter = 'all'">
                        {{ __('All') }}
                    </button>
                    <button class="px-history__filter" :class="filter === 'xp' && 'active'" @click="filter = 'xp'">
                        XP
                    </button>
                    <button class="px-history__filter" :class="filter === 'tokens' && 'active'" @click="filter = 'tokens'">
                        {{ __('Tokens') }}
                    </button>
                </div>

                @php
                    $iconMap = [
                        'xp_earned' => ['icon' => 'fa-bolt', 'type' => 'plus'],
                        'tokens_earned' => ['icon' => 'fa-coins', 'type' => 'plus'],
                        'daily_bonus' => ['icon' => 'fa-calendar-check', 'type' => 'plus'],
                        'tokens_spent' => ['icon' => 'fa-comment-dots', 'type' => 'minus'],
                    ];
                @endphp

                @if($activities->isEmpty())
                    <div class="px-history__empty">
                        <i class="fas fa-inbox"></i>
                        {{ __('No activity yet') }}
                    </div>
                @else
                    <div class="px-history__list">
                        @foreach($activities as $act)
                            @php
                                $map = $iconMap[$act->activity_type] ?? ['icon' => 'fa-circle', 'type' => 'plus'];
                                $isPlus = $map['type'] === 'plus';
                                $sign = $isPlus ? '+' : '-';
                                $filterType = in_array($act->activity_type, ['xp_earned']) ? 'xp' : 'tokens';
                            @endphp
                            <div class="px-history__item" data-filter="{{ $filterType }}" x-show="filter === 'all' || filter === '{{ $filterType }}'" style="margin-top: 10px;">
                                <div class="px-history__icon px-history__icon--{{ $map['type'] }}">
                                    <i class="fas {{ $map['icon'] }}"></i>
                                </div>
                                <div class="px-history__info">
                                    <div class="px-history__text">{{ $act->activity_text }}</div>
                                    <div class="px-history__time">{{ $act->activity_time->diffForHumans() }}</div>
                                </div>
                                @php
                                    preg_match('/^[+-]\d+/', $act->activity_text, $matches);
                                    $amount = $matches[0] ?? '';
                                @endphp
                                @if($amount)
                                    <div class="px-history__amount px-history__amount--{{ $map['type'] }}">
                                        {{ $amount }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function pxHistory() {
    return {
        filter: 'all'
    }
}
</script>
@endsection
