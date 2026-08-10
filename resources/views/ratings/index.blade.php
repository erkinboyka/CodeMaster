@extends('layouts.app')

@section('title', __('Ratings') . ' - CodeMaster')

@section('head')
<style>
    .rt-hero {
        position: relative;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 50%, var(--accent-3) 100%);
        padding: clamp(48px, 8vw, 100px) 24px clamp(40px, 6vw, 80px);
        overflow: hidden;
        text-align: center;
    }
    .rt-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%);
    }
    .rt-hero::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 60px;
        background: var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .rt-hero-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 40px 40px;
    }
    .rt-hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        animation: rtOrbFloat 8s ease-in-out infinite;
    }
    .rt-hero-orb:nth-child(1) { width: 300px; height: 300px; background: rgba(255,255,255,0.1); top: -100px; left: -50px; }
    .rt-hero-orb:nth-child(2) { width: 250px; height: 250px; background: rgba(255,255,255,0.08); bottom: -80px; right: -30px; animation-delay: -3s; }
    .rt-hero-orb:nth-child(3) { width: 200px; height: 200px; background: rgba(255,255,255,0.06); top: 50%; left: 60%; animation-delay: -5s; }
    @@keyframes rtOrbFloat {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-20px) scale(1.05); }
    }
    .rt-hero h1 {
        font-size: clamp(36px, 6vw, 72px);
        font-weight: 900;
        color: white;
        letter-spacing: -2px;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }
    .rt-hero p {
        font-size: clamp(16px, 2vw, 20px);
        color: rgba(255,255,255,0.75);
        max-width: 540px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
        line-height: 1.7;
    }
    .rt-hero-stats {
        display: flex;
        justify-content: center;
        gap: clamp(24px, 4vw, 56px);
        position: relative;
        z-index: 2;
        margin-top: 40px;
    }
    .rt-hero-stat-val {
        font-size: clamp(28px, 4vw, 42px);
        font-weight: 900;
        color: white;
        line-height: 1;
    }
    .rt-hero-stat-label {
        font-size: 12px;
        color: rgba(255,255,255,0.6);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-top: 8px;
        font-weight: 500;
    }

    .rt-user-card {
        display: inline-flex;
        align-items: center;
        gap: 20px;
        padding: 20px 28px;
        border-radius: 18px;
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255,255,255,0.12);
        margin-top: 32px;
        position: relative;
        z-index: 2;
        transition: all 0.3s;
    }
    .rt-user-card:hover { background: rgba(255,255,255,0.12); transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,0.15); }
    .rt-user-avatar {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        border: 2px solid rgba(255,255,255,0.25);
        object-fit: cover;
        transition: transform 0.3s;
    }
    .rt-user-card:hover .rt-user-avatar { transform: scale(1.05); }
    .rt-user-info { text-align: left; }
    .rt-user-level { font-size: 16px; font-weight: 800; color: white; letter-spacing: -0.3px; }
    .rt-user-xp { font-size: 13px; color: rgba(255,255,255,0.7); margin-top: 2px; }
    .rt-user-tokens {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 10px;
        background: rgba(245,158,11,0.15);
        border: 1px solid rgba(245,158,11,0.25);
        color: #fbbf24;
        font-size: 12px;
        font-weight: 700;
        margin-top: 4px;
    }
    .rt-divider {
        width: 1px;
        height: 40px;
        background: rgba(255,255,255,0.2);
    }

    .rt-tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 24px;
    }
    .rt-tab {
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
    .rt-tab:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-1px); }
    .rt-tab.active {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 14px var(--accent-glow);
    }

    .rt-search {
        display: flex;
        gap: 6px;
        margin-bottom: 24px;
    }
    .rt-search input {
        padding: 10px 16px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--card);
        color: var(--text);
        font-size: 14px;
        outline: none;
        width: 220px;
        transition: all 0.3s;
    }
    .rt-search input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
    .rt-search button {
        padding: 10px 18px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        border: none;
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: white;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 2px 8px var(--accent-glow);
    }
    .rt-search button:hover { transform: translateY(-2px); box-shadow: 0 6px 16px var(--accent-glow); }

    .rt-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 32px;
    }
    .rt-stat-card {
        border-radius: 16px;
        background: var(--card);
        border: 1px solid var(--border);
        padding: 24px 20px;
        text-align: center;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }
    .rt-stat-card::before {
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
    .rt-stat-card:hover { border-color: var(--accent); transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
    .rt-stat-card:hover::before { opacity: 1; }
    .rt-stat-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        margin-bottom: 12px;
        font-size: 18px;
    }
    .rt-stat-val {
        font-size: 28px;
        font-weight: 900;
        color: var(--accent);
        line-height: 1;
    }
    .rt-stat-label {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 8px;
        font-weight: 600;
    }

    .rt-leaderboard {
        border-radius: 20px;
        background: var(--card);
        border: 1px solid var(--border);
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,0.06);
    }
    .rt-lb-header {
        display: grid;
        grid-template-columns: 60px 1fr 100px 100px 100px 100px 80px;
        padding: 14px 24px;
        background: linear-gradient(180deg, var(--bg), var(--card));
        border-bottom: 2px solid var(--border);
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1.2px;
    }
    .rt-lb-row {
        display: grid;
        grid-template-columns: 60px 1fr 100px 100px 100px 100px 80px;
        align-items: center;
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
        transition: all 0.2s;
        border-left: 3px solid transparent;
        transition: all 0.25s ease;
    }
    .rt-lb-row:last-child { border-bottom: none; }
    .rt-lb-row:hover { background: var(--bg-secondary, rgba(0,0,0,0.02)); border-left-color: var(--accent); transform: translateX(2px); }
    .rt-lb-row.top-1 { background: linear-gradient(90deg, rgba(255,215,0,0.08), transparent 60%); border-left-color: #FFD700; }
    .rt-lb-row.top-2 { background: linear-gradient(90deg, rgba(192,192,192,0.08), transparent 60%); border-left-color: #C0C0C0; }
    .rt-lb-row.top-3 { background: linear-gradient(90deg, rgba(205,127,50,0.08), transparent 60%); border-left-color: #CD7F32; }

    .rt-rank { font-size: 20px; font-weight: 900; color: var(--text-muted); }
    .rt-rank.medal { font-size: 24px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3)); }
    .rt-medal-icon { display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:50%; font-size:15px; font-weight:700; transition: transform 0.3s, box-shadow 0.3s; }
    .rt-medal-icon:hover { transform: scale(1.15) rotate(5deg); }
    .rt-medal-1 { background:linear-gradient(135deg,#FFD700,#FFA500); color:#7c5800; box-shadow:0 4px 18px rgba(255,215,0,0.5); }
    .rt-medal-1:hover { box-shadow:0 6px 24px rgba(255,215,0,0.6); }
    .rt-medal-2 { background:linear-gradient(135deg,#E8E8E8,#B0B0B0); color:#555; box-shadow:0 4px 18px rgba(192,192,192,0.5); }
    .rt-medal-2:hover { box-shadow:0 6px 24px rgba(192,192,192,0.6); }
    .rt-medal-3 { background:linear-gradient(135deg,#CD7F32,#A0522D); color:#fff; box-shadow:0 4px 18px rgba(205,127,50,0.5); }
    .rt-medal-3:hover { box-shadow:0 6px 24px rgba(205,127,50,0.6); }

    .rt-player {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .rt-player-avatar {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        border: 2px solid var(--border);
        object-fit: cover;
        transition: transform 0.2s, border-color 0.2s;
    }
    .rt-lb-row:hover .rt-player-avatar { transform: scale(1.08); border-color: var(--accent); }
    .rt-player-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        text-decoration: none;
        transition: color 0.2s;
    }
    .rt-player-name:hover { color: var(--accent); }
    .rt-player-title { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

    .rt-level-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        transition: transform 0.2s;
    }
    .rt-level-badge:hover { transform: scale(1.05); }
    .rt-xp-bar {
        width: 84px;
        height: 7px;
        border-radius: 4px;
        background: var(--border);
        margin-top: 6px;
        overflow: hidden;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
    }
    .rt-xp-bar-fill {
        height: 100%;
        border-radius: 4px;
        background: linear-gradient(90deg, var(--accent), var(--accent-2));
        transition: width 0.6s ease;
        box-shadow: 0 0 8px var(--accent-glow);
    }
    .rt-xp-text { font-size: 10px; color: var(--text-muted); margin-top: 3px; font-weight: 500; }

    .rt-xp { font-size: 14px; font-weight: 800; background: linear-gradient(135deg, var(--accent), var(--accent-2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .rt-center { text-align: center; }

    .rt-badge-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 32px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        transition: transform 0.2s;
    }
    .rt-badge-num:hover { transform: scale(1.1); }

    .rt-token-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(249,115,22,0.1));
        color: #fbbf24;
        border: 1px solid rgba(245,158,11,0.2);
    }
    .rt-token-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #f97316);
        color: #fff;
        font-size: 9px;
    }

    .rt-empty {
        text-align: center;
        padding: 80px 24px;
    }
    .rt-empty i { font-size: 56px; color: var(--text-muted); margin-bottom: 20px; opacity: 0.3; }
    .rt-empty p { font-size: 18px; font-weight: 700; color: var(--text-muted); }
    .rt-empty small { font-size: 14px; color: var(--text-muted); opacity: 0.7; }

    .rt-pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 24px;
        border-top: 1px solid var(--border);
    }
    .rt-page-btn {
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
    .rt-page-btn:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-2px); box-shadow: 0 4px 12px var(--accent-glow); }
    .rt-page-btn.active {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 14px var(--accent-glow);
    }
    .rt-page-btn.disabled { opacity: 0.4; pointer-events: none; }
    .rt-page-dots { color: var(--text-muted); padding: 0 4px; }

    .rt-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-top: 32px;
    }
    .rt-info-card {
        border-radius: 20px;
        padding: 32px;
        color: white;
        position: relative;
        overflow: hidden;
        transition: all 0.4s ease;
    }
    .rt-info-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,0.25); }
    .rt-info-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 80% 20%, rgba(255,255,255,0.15) 0%, transparent 50%);
    }
    .rt-info-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(8px);
        font-size: 22px;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .rt-info-card:hover .rt-info-icon { transform: scale(1.1); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
    .rt-info-title { font-size: 18px; font-weight: 800; margin-bottom: 12px; position: relative; z-index: 1; }
    .rt-info-list { list-style: none; padding: 0; margin: 0; position: relative; z-index: 1; }
    .rt-info-list li {
        font-size: 13px;
        color: rgba(255,255,255,0.85);
        padding: 6px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .rt-info-list li i { font-size: 10px; opacity: 0.7; }

    @@media(max-width: 768px) {
        .rt-stats-grid { grid-template-columns: repeat(2, 1fr); }
        .rt-info-grid { grid-template-columns: 1fr; }
        .rt-lb-header, .rt-lb-row { grid-template-columns: 50px 1fr 80px 80px; }
        .rt-lb-header > :nth-child(n+5), .rt-lb-row > :nth-child(n+5) { display: none; }
        .rt-user-card { flex-wrap: wrap; justify-content: center; }
    }
</style>
@endsection

@section('content')
<section class="rt-hero">
    <div class="rt-hero-grid"></div>
    <div class="rt-hero-orb"></div>
    <div class="rt-hero-orb"></div>
    <div class="rt-hero-orb"></div>

    <h1>{{ __('Leaderboard') }}</h1>
    <p>{{ __('Compete with other developers. Earn XP, level up!') }}</p>

    <div class="rt-hero-stats">
        <div class="rt-hero-stat">
            <div class="rt-hero-stat-val">{{ $users->total() }}</div>
            <div class="rt-hero-stat-label">{{ __('Players') }}</div>
        </div>
        <div class="rt-hero-stat">
            <div class="rt-hero-stat-val">{{ number_format($users->sum('total_xp')) }}</div>
            <div class="rt-hero-stat-label">{{ __('Total XP') }}</div>
        </div>
        <div class="rt-hero-stat">
            <div class="rt-hero-stat-val">{{ $users->sum('certificates_count') }}</div>
            <div class="rt-hero-stat-label">{{ __('Certificates') }}</div>
        </div>
    </div>

    @if($currentUser)
    <div class="rt-user-card">
        <img src="{{ $currentUser->avatar ? asset('storage/' . $currentUser->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($currentUser->name ?? 'U') . '&background=6366f1&color=fff' }}" class="rt-user-avatar">
        <div class="rt-user-info">
            <div class="rt-user-level">{{ $currentUser->level_badge }} Lv.{{ $currentUser->level }} — {{ $currentUser->level_title }}</div>
            <div class="rt-user-xp">{{ number_format($currentUser->total_xp) }} XP</div>
            <div class="rt-user-tokens"><span class="rt-token-icon"><i class="fas fa-coins"></i></span> {{ $currentUser->ai_tokens }}</div>
        </div>
        <div class="rt-divider"></div>
        <div class="rt-user-info">
            <div style="font-size:12px;color:rgba(255,255,255,0.6)">{{ __('Level Progress') }}</div>
            <div style="width:120px;height:6px;border-radius:3px;background:rgba(255,255,255,0.2);margin-top:6px;overflow:hidden">
                <div style="height:100%;border-radius:3px;background:white;width:{{ $currentUser->level_progress }}%"></div>
            </div>
            <div style="font-size:10px;color:rgba(255,255,255,0.5);margin-top:4px">{{ $currentUser->xp_for_current_level }}/{{ $currentUser->xp_for_next_level }}</div>
        </div>
    </div>
    @endif
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px">
        <div class="rt-tabs">
            <a href="{{ route('ratings.index', ['tab' => 'courses']) }}" class="rt-tab {{ $tab === 'courses' ? 'active' : '' }}">
                <i class="fas fa-graduation-cap"></i> {{ __('Courses') }}
            </a>
            <a href="{{ route('ratings.index', ['tab' => 'tests']) }}" class="rt-tab {{ $tab === 'tests' ? 'active' : '' }}">
                <i class="fas fa-flask"></i> {{ __('Tests') }}
            </a>
        </div>
        <form method="GET" action="{{ route('ratings.index', ['tab' => $tab]) }}" class="rt-search">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search...') }}">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="rt-stats-grid">
        @if($tab === 'courses')
        <div class="rt-stat-card">
            <div class="rt-stat-icon" style="background:var(--accent-glow);color:var(--accent)"><i class="fas fa-bolt"></i></div>
            <div class="rt-stat-val">{{ number_format($users->sum('total_xp')) }}</div>
            <div class="rt-stat-label">{{ __('Total XP') }}</div>
        </div>
        <div class="rt-stat-card">
            <div class="rt-stat-icon" style="background:rgba(234,179,8,0.1);color:#eab308"><i class="fas fa-certificate"></i></div>
            <div class="rt-stat-val">{{ $users->sum('certificates_count') }}</div>
            <div class="rt-stat-label">{{ __('Certificates') }}</div>
        </div>
        <div class="rt-stat-card">
            <div class="rt-stat-icon" style="background:rgba(34,197,94,0.1);color:#22c55e"><i class="fas fa-check-double"></i></div>
            <div class="rt-stat-val">{{ $users->sum('completed_courses_count') }}</div>
            <div class="rt-stat-label">{{ __('Courses Done') }}</div>
        </div>
        <div class="rt-stat-card">
            <div class="rt-stat-icon" style="background:rgba(139,92,246,0.1);color:#8b5cf6"><i class="fas fa-users"></i></div>
            <div class="rt-stat-val">{{ $users->count() }}</div>
            <div class="rt-stat-label">{{ __('Players') }}</div>
        </div>
        @else
        <div class="rt-stat-card">
            <div class="rt-stat-icon" style="background:var(--accent-glow);color:var(--accent)"><i class="fas fa-bolt"></i></div>
            <div class="rt-stat-val">{{ number_format($users->sum('total_xp')) }}</div>
            <div class="rt-stat-label">{{ __('Total XP') }}</div>
        </div>
        <div class="rt-stat-card">
            <div class="rt-stat-icon" style="background:rgba(34,197,94,0.1);color:#22c55e"><i class="fas fa-code"></i></div>
            <div class="rt-stat-val">{{ $users->sum('practice_passed_count') }}</div>
            <div class="rt-stat-label">{{ __('Practice Done') }}</div>
        </div>
        <div class="rt-stat-card">
            <div class="rt-stat-icon" style="background:rgba(168,85,247,0.1);color:#a855f7"><i class="fas fa-trophy"></i></div>
            <div class="rt-stat-val">{{ $users->sum('contest_passed_count') }}</div>
            <div class="rt-stat-label">{{ __('Contests Done') }}</div>
        </div>
        <div class="rt-stat-card">
            <div class="rt-stat-icon" style="background:rgba(139,92,246,0.1);color:#8b5cf6"><i class="fas fa-users"></i></div>
            <div class="rt-stat-val">{{ $users->count() }}</div>
            <div class="rt-stat-label">{{ __('Players') }}</div>
        </div>
        @endif
    </div>

    <div class="rt-leaderboard">
        <div class="rt-lb-header">
            <div>#</div>
            <div>{{ __('Player') }}</div>
            <div>{{ __('Level') }}</div>
            <div style="text-align:center">{{ __('XP') }}</div>
            @if($tab === 'courses')
            <div style="text-align:center">{{ __('Certificates') }}</div>
            <div style="text-align:center">{{ __('Courses') }}</div>
            @else
            <div style="text-align:center">{{ __('Practice') }}</div>
            <div style="text-align:center">{{ __('Contests') }}</div>
            @endif
            <div style="text-align:center">{{ __('Tokens') }}</div>
        </div>

        @forelse($users as $index => $user)
        @php
            $rank = ($users->currentPage() - 1) * $users->perPage() + $index + 1;
            $rowClass = match($rank) { 1 => 'top-1', 2 => 'top-2', 3 => 'top-3', default => '' };
        @endphp
        <div class="rt-lb-row {{ $rowClass }}">
            <div>
                @if($rank === 1)
                <span class="rt-medal-icon rt-medal-1"><i class="fas fa-crown"></i></span>
                @elseif($rank === 2)
                <span class="rt-medal-icon rt-medal-2"><i class="fas fa-shield-halved"></i></span>
                @elseif($rank === 3)
                <span class="rt-medal-icon rt-medal-3"><i class="fas fa-shield"></i></span>
                @else
                <span class="rt-rank">{{ $rank }}</span>
                @endif
            </div>
            <div class="rt-player">
                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=6366f1&color=fff' }}" class="rt-player-avatar">
                <div>
                    <a href="{{ route('profile.show', $user->id) }}" class="rt-player-name">{{ $user->name }}</a>
                    <div class="rt-player-title">{{ $user->title ?? $user->email }}</div>
                </div>
            </div>
            <div>
                <span class="rt-level-badge" style="background:{{ $user->level_color }}15;color:{{ $user->level_color }}">
                    {{ $user->level_badge }} Lv.{{ $user->level }}
                </span>
                <div class="rt-xp-bar">
                    <div class="rt-xp-bar-fill" style="width:{{ $user->level_progress }}%"></div>
                </div>
                <div class="rt-xp-text">{{ $user->xp_for_current_level }}/{{ $user->xp_for_next_level }}</div>
            </div>
            <div class="rt-center">
                <span class="rt-xp">{{ number_format($user->total_xp) }}</span>
            </div>
            @if($tab === 'courses')
            <div class="rt-center">
                <span class="rt-badge-num" style="background:rgba(234,179,8,0.1);color:#eab308">{{ $user->certificates_count }}</span>
            </div>
            <div class="rt-center">
                <span class="rt-badge-num" style="background:var(--accent-glow);color:var(--accent)">{{ $user->completed_courses_count ?? 0 }}</span>
            </div>
            @else
            <div class="rt-center">
                <span class="rt-badge-num" style="background:rgba(34,197,94,0.1);color:#22c55e">{{ $user->practice_passed_count ?? 0 }}</span>
            </div>
            <div class="rt-center">
                <span class="rt-badge-num" style="background:rgba(168,85,247,0.1);color:#a855f7">{{ $user->contest_passed_count ?? 0 }}</span>
            </div>
            @endif
            <div class="rt-center">
                <span class="rt-token-badge"><span class="rt-token-icon"><i class="fas fa-coins"></i></span> {{ $user->ai_tokens }}</span>
            </div>
        </div>
        @empty
        <div class="rt-empty">
            <i class="fas fa-trophy" style="background: linear-gradient(135deg, var(--accent), var(--accent-2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"></i>
            <p>{{ __('No players yet') }}</p>
            <small>{{ __('Be the first in the leaderboard!') }}</small>
        </div>
        @endforelse

        @if($users->hasPages())
        <div class="rt-pagination">
            @if($users->onFirstPage())
            <span class="rt-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
            @else
            <a href="{{ $users->previousPageUrl() }}" class="rt-page-btn"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
            @if($page == $users->currentPage())
            <span class="rt-page-btn active">{{ $page }}</span>
            @else
            <a href="{{ $url }}" class="rt-page-btn">{{ $page }}</a>
            @endif
            @endforeach

            @if($users->currentPage() + 2 < $users->lastPage())
            <span class="rt-page-dots">...</span>
            <a href="{{ $users->url($users->lastPage()) }}" class="rt-page-btn">{{ $users->lastPage() }}</a>
            @endif

            @if($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}" class="rt-page-btn"><i class="fas fa-chevron-right"></i></a>
            @else
            <span class="rt-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
        @endif
    </div>

    <div class="rt-info-grid">
        <div class="rt-info-card" style="background:linear-gradient(135deg, #6366f1, #8b5cf6)">
            <div class="rt-info-icon"><i class="fas fa-bullseye"></i></div>
            <div class="rt-info-title">{{ __('Earn XP') }}</div>
            <ul class="rt-info-list">
                <li><i class="fas fa-book-open"></i> +10 XP — {{ __('Per lesson') }}</li>
                <li><i class="fas fa-flask"></i> +25 XP — {{ __('Per test') }}</li>
                <li><i class="fas fa-code"></i> +30 XP — {{ __('Per practice') }}</li>
                <li><i class="fas fa-pen-fancy"></i> +50 XP — {{ __('Per exam') }}</li>
                <li><i class="fas fa-graduation-cap"></i> +100 XP — {{ __('Per course') }}</li>
            </ul>
        </div>
        <div class="rt-info-card" style="background:linear-gradient(135deg, #f59e0b, #f97316)">
            <div class="rt-info-icon"><i class="fas fa-gem"></i></div>
            <div class="rt-info-title">{{ __('AI Tokens') }}</div>
            <ul class="rt-info-list">
                <li><i class="fas fa-wallet"></i> {{ __('Starting balance: 25 tokens') }}</li>
                <li><i class="fas fa-paper-plane"></i> -1 token — {{ __('Per AI message') }}</li>
                <li><i class="fas fa-sun"></i> +5 tokens — {{ __('Daily bonus') }}</li>
                <li><i class="fas fa-chart-line"></i> {{ __('Earn tokens for activity') }}</li>
            </ul>
        </div>
        <div class="rt-info-card" style="background:linear-gradient(135deg, #10b981, #14b8a6)">
            <div class="rt-info-icon"><i class="fas fa-bolt"></i></div>
            <div class="rt-info-title">{{ __('Levels') }}</div>
            <ul class="rt-info-list">
                <li><i class="fas fa-seedling" style="color:#22c55e"></i> {{ __('Beginner') }} — 1</li>
                <li><i class="fas fa-graduation-cap" style="color:#3b82f6"></i> {{ __('Student') }} — 5</li>
                <li><i class="fas fa-rocket" style="color:#8b5cf6"></i> {{ __('Experienced') }} — 10</li>
                <li><i class="fas fa-fire" style="color:#f97316"></i> {{ __('Advanced') }} — 15</li>
                <li><i class="fas fa-crown" style="color:#eab308"></i> {{ __('Expert') }} — 30+</li>
            </ul>
        </div>
    </div>
</div>
@endsection
