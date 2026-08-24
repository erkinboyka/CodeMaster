@extends('layouts.app')

@section('title', __('Vacancies') . ' - CodeMaster')

@section('head')
<style>
    .vc-hero {
        position: relative;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 50%, var(--accent-3) 100%);
        padding: clamp(48px, 8vw, 100px) 24px clamp(40px, 6vw, 80px);
        overflow: hidden;
        text-align: center;
    }
    .vc-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%);
    }
    .vc-hero::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 60px;
        background: var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .vc-hero-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 40px 40px;
    }
    .vc-hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        animation: vcOrbFloat 8s ease-in-out infinite;
    }
    .vc-hero-orb:nth-child(1) { width: 300px; height: 300px; background: rgba(255,255,255,0.1); top: -100px; left: -50px; }
    .vc-hero-orb:nth-child(2) { width: 250px; height: 250px; background: rgba(255,255,255,0.08); bottom: -80px; right: -30px; animation-delay: -3s; }
    .vc-hero-orb:nth-child(3) { width: 200px; height: 200px; background: rgba(255,255,255,0.06); top: 50%; left: 60%; animation-delay: -5s; }
    @@keyframes vcOrbFloat {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-20px) scale(1.05); }
    }
    .vc-hero h1 {
        font-size: clamp(36px, 6vw, 72px);
        font-weight: 900;
        color: white;
        letter-spacing: -2px;
        margin-bottom: 16px;
        position: relative;
        z-index: 2;
    }
    .vc-hero p {
        font-size: clamp(16px, 2vw, 20px);
        color: rgba(255,255,255,0.75);
        max-width: 540px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
        line-height: 1.7;
    }
    .vc-hero-search {
        position: relative;
        z-index: 2;
        max-width: 560px;
        margin: 32px auto 0;
    }
    .vc-hero-search input {
        width: 100%;
        padding: 16px 20px 16px 50px;
        border-radius: 16px;
        border: 2px solid rgba(255,255,255,0.2);
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(12px);
        color: #1a1a2e;
        font-size: 15px;
        outline: none;
        transition: all 0.3s;
        box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    }
    .vc-hero-search input:focus { border-color: white; box-shadow: 0 8px 32px rgba(0,0,0,0.2), 0 0 0 4px rgba(255,255,255,0.2); }
    .vc-hero-search input::placeholder { color: #9ca3af; }
    .vc-hero-search i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 16px;
    }
    .vc-hero-stats {
        display: flex;
        justify-content: center;
        gap: clamp(24px, 4vw, 56px);
        position: relative;
        z-index: 2;
        margin-top: 36px;
    }
    .vc-hero-stat-val {
        font-size: clamp(28px, 4vw, 42px);
        font-weight: 900;
        color: white;
        line-height: 1;
    }
    .vc-hero-stat-label {
        font-size: 12px;
        color: rgba(255,255,255,0.6);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-top: 8px;
        font-weight: 500;
    }

    .vc-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 28px;
    }
    .vc-filter {
        padding: 10px 22px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid var(--border);
        color: var(--text-muted);
        background: var(--card);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .vc-filter:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-1px); }
    .vc-filter.active {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 14px var(--accent-glow);
    }

    .vc-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    @@media(max-width: 768px) { .vc-grid { grid-template-columns: 1fr; } }

    .vc-card {
        border-radius: 20px;
        background: var(--card);
        border: 1px solid var(--border);
        padding: 28px;
        transition: all 0.35s ease;
        position: relative;
        overflow: hidden;
    }
    .vc-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--accent), var(--accent-2));
        opacity: 0;
        transition: opacity 0.3s;
    }
    .vc-card:hover { border-color: var(--accent); transform: translateY(-4px); box-shadow: 0 12px 36px rgba(0,0,0,0.08); }
    .vc-card:hover::before { opacity: 1; }

    .vc-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .vc-card-company {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .vc-card-logo {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        flex-shrink: 0;
        transition: transform 0.3s;
    }
    .vc-card:hover .vc-card-logo { transform: scale(1.08) rotate(-3deg); }
    .vc-card-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
        transition: color 0.2s;
    }
    .vc-card:hover .vc-card-title { color: var(--accent); }
    .vc-card-meta {
        font-size: 13px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .vc-card-meta i { font-size: 11px; }

    .vc-type-badge {
        padding: 5px 14px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .vc-type-remote { background: rgba(34,197,94,0.1); color: #22c55e; border: 1px solid rgba(34,197,94,0.2); }
    .vc-type-hybrid { background: rgba(234,179,8,0.1); color: #eab308; border: 1px solid rgba(234,179,8,0.2); }
    .vc-type-office { background: rgba(59,130,246,0.1); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2); }

    .vc-salary {
        font-size: 15px;
        font-weight: 800;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .vc-salary-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 6px;
        font-size: 10px;
    }

    .vc-skills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 18px;
    }
    .vc-skill {
        padding: 5px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        background: var(--bg);
        color: var(--text-secondary);
        border: 1px solid var(--border);
        transition: all 0.2s;
    }
    .vc-card:hover .vc-skill { border-color: var(--accent); color: var(--accent); }

    .vc-card-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 16px;
        border-top: 1px solid var(--border);
    }
    .vc-card-time {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .vc-card-time i { font-size: 11px; }
    .vc-card-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 20px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        color: var(--accent);
        border: 1px solid var(--accent);
        background: transparent;
        transition: all 0.3s;
    }
    .vc-card-btn:hover {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 14px var(--accent-glow);
        transform: translateY(-1px);
    }

    .vc-empty {
        text-align: center;
        padding: 80px 24px;
        grid-column: 1 / -1;
    }
    .vc-empty i {
        font-size: 56px;
        margin-bottom: 20px;
        opacity: 0.3;
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .vc-empty p { font-size: 18px; font-weight: 700; color: var(--text-muted); }
    .vc-empty small { font-size: 14px; color: var(--text-muted); opacity: 0.7; }

    .vc-pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 28px 0 0;
    }
    .vc-page-btn {
        min-width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid var(--border);
        color: var(--text-muted);
        background: var(--bg);
    }
    .vc-page-btn:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-2px); box-shadow: 0 4px 12px var(--accent-glow); }
    .vc-page-btn.active {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 14px var(--accent-glow);
    }
    .vc-page-btn.disabled { opacity: 0.4; pointer-events: none; }
    .vc-page-dots { color: var(--text-muted); padding: 0 4px; }
</style>
@endsection

@section('content')
<section class="vc-hero">
    <div class="vc-hero-grid"></div>
    <div class="vc-hero-orb"></div>
    <div class="vc-hero-orb"></div>
    <div class="vc-hero-orb"></div>

    <h1 class="reveal-up" data-delay="0">{{ __('Find Your Dream Job') }}</h1>
    <p class="reveal-up" data-delay="0.1">{{ __('Discover opportunities from top companies looking for talented developers.') }}</p>

    <form action="{{ route('vacancies.index') }}" method="GET" class="vc-hero-search reveal-up" data-delay="0.2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by title, skill, or company...') }}">
        <i class="fas fa-search"></i>
    </form>

    <div class="vc-hero-stats reveal-up" data-delay="0.3">
        <div class="vc-hero-stat">
            <div class="vc-hero-stat-val">{{ $vacancies->total() }}</div>
            <div class="vc-hero-stat-label">{{ __('Open Positions') }}</div>
        </div>
        <div class="vc-hero-stat">
            <div class="vc-hero-stat-val">{{ $vacancies->unique('company')->count() }}</div>
            <div class="vc-hero-stat-label">{{ __('Companies') }}</div>
        </div>
        <div class="vc-hero-stat">
            <div class="vc-hero-stat-val">{{ $vacancies->where('type', 'remote')->count() }}</div>
            <div class="vc-hero-stat-label">{{ __('Remote Jobs') }}</div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="vc-filters reveal-up" data-delay="0">
        @php $currentType = request('type'); @endphp
        <a href="{{ route('vacancies.index', array_merge(request()->except('type', 'page'))) }}" class="vc-filter {{ !$currentType ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i> {{ __('All') }}
        </a>
        <a href="{{ route('vacancies.index', array_merge(request()->except('type', 'page'), ['type' => 'remote'])) }}" class="vc-filter {{ $currentType === 'remote' ? 'active' : '' }}">
            <i class="fas fa-wifi"></i> {{ __('Remote') }}
        </a>
        <a href="{{ route('vacancies.index', array_merge(request()->except('type', 'page'), ['type' => 'office'])) }}" class="vc-filter {{ $currentType === 'office' ? 'active' : '' }}">
            <i class="fas fa-building"></i> {{ __('Office') }}
        </a>
        <a href="{{ route('vacancies.index', array_merge(request()->except('type', 'page'), ['type' => 'hybrid'])) }}" class="vc-filter {{ $currentType === 'hybrid' ? 'active' : '' }}">
            <i class="fas fa-arrows-split-up-and-left"></i> {{ __('Hybrid') }}
        </a>
    </div>

    <div class="vc-grid">
        @forelse($vacancies as $vacancy)
        @php
            $typeClass = match($vacancy->type) { 'remote' => 'vc-type-remote', 'hybrid' => 'vc-type-hybrid', default => 'vc-type-office' };
            $typeIcon = match($vacancy->type) { 'remote' => 'fa-wifi', 'hybrid' => 'fa-arrows-split-up-and-left', default => 'fa-building' };
            $colors = ['#6366f1','#8b5cf6','#ec4899','#f97316','#10b981','#3b82f6','#eab308'];
            $color = $colors[$vacancy->id % count($colors)];
        @endphp
        <div class="vc-card reveal-up" data-stagger="{{ $loop->index }}">
            <div class="vc-card-top">
                <div class="vc-card-company">
                    <div class="vc-card-logo" style="background:{{ $color }}15;color:{{ $color }}">
                        {{ strtoupper(substr($vacancy->company, 0, 2)) }}
                    </div>
                    <div>
                        <div class="vc-card-title">{{ $vacancy->title }}</div>
                        <div class="vc-card-meta">
                            <i class="fas fa-building"></i> {{ $vacancy->company }}
                            <span style="opacity:0.4">·</span>
                            <i class="fas fa-location-dot"></i> {{ $vacancy->location }}
                        </div>
                    </div>
                </div>
                <span class="vc-type-badge {{ $typeClass }}"><i class="fas {{ $typeIcon }}" style="margin-right:4px"></i>{{ __($vacancy->type) }}</span>
            </div>

            @if($vacancy->salary_min || $vacancy->salary_max)
            <div class="vc-salary" style="color:{{ $color }}">
                <span class="vc-salary-icon" style="background:{{ $color }}15;color:{{ $color }}"><i class="fas fa-coins"></i></span>
                @if($vacancy->salary_min && $vacancy->salary_max)
                    {{ number_format($vacancy->salary_min) }} – {{ number_format($vacancy->salary_max) }} {{ $vacancy->salary_currency }}
                @elseif($vacancy->salary_min)
                    {{ __('from') }} {{ number_format($vacancy->salary_min) }} {{ $vacancy->salary_currency }}
                @else
                    {{ __('up to') }} {{ number_format($vacancy->salary_max) }} {{ $vacancy->salary_currency }}
                @endif
            </div>
            @endif

            @if($vacancy->vacancySkills->count())
            <div class="vc-skills">
                @foreach($vacancy->vacancySkills as $skill)
                <span class="vc-skill">{{ $skill->skill_name }}</span>
                @endforeach
            </div>
            @endif

            <div class="vc-card-bottom">
                <div class="vc-card-time">
                    <i class="fas fa-clock"></i> {{ $vacancy->created_at->diffForHumans() }}
                </div>
                <a href="{{ route('vacancies.show', $vacancy->id) }}" class="vc-card-btn">
                    {{ __('View') }} <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="vc-empty">
            <i class="fas fa-briefcase"></i>
            <p>{{ __('No vacancies found') }}</p>
            <small>{{ __('Try adjusting your search or filters') }}</small>
        </div>
        @endforelse
    </div>

    @if($vacancies->hasPages())
    <div class="vc-pagination reveal-up">
        @if($vacancies->onFirstPage())
        <span class="vc-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
        @else
        <a href="{{ $vacancies->previousPageUrl() }}" class="vc-page-btn"><i class="fas fa-chevron-left"></i></a>
        @endif

        @foreach($vacancies->getUrlRange(max(1, $vacancies->currentPage() - 2), min($vacancies->lastPage(), $vacancies->currentPage() + 2)) as $page => $url)
        @if($page == $vacancies->currentPage())
        <span class="vc-page-btn active">{{ $page }}</span>
        @else
        <a href="{{ $url }}" class="vc-page-btn">{{ $page }}</a>
        @endif
        @endforeach

        @if($vacancies->currentPage() + 2 < $vacancies->lastPage())
        <span class="vc-page-dots">...</span>
        <a href="{{ $vacancies->url($vacancies->lastPage()) }}" class="vc-page-btn">{{ $vacancies->lastPage() }}</a>
        @endif

        @if($vacancies->hasMorePages())
        <a href="{{ $vacancies->nextPageUrl() }}" class="vc-page-btn"><i class="fas fa-chevron-right"></i></a>
        @else
        <span class="vc-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
        @endif
    </div>
    @endif
</div>
@endsection
