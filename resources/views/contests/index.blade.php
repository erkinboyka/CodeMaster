@extends('layouts.app')

@section('title', __('Contests') . ' - CodeMaster')

@section('head')
<style>
    .ct-hero {
        position: relative;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 50%, var(--accent-3) 100%);
        padding: clamp(48px, 8vw, 100px) 24px clamp(40px, 6vw, 80px);
        overflow: hidden;
        text-align: center;
    }
    .ct-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%);
    }
    .ct-hero::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 60px;
        background: var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .ct-hero-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 40px 40px;
    }
    .ct-hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        animation: ctOrbFloat 8s ease-in-out infinite;
    }
    .ct-hero-orb:nth-child(1) { width: 300px; height: 300px; background: rgba(255,255,255,0.1); top: -100px; left: -50px; }
    .ct-hero-orb:nth-child(2) { width: 250px; height: 250px; background: rgba(255,255,255,0.08); bottom: -80px; right: -30px; animation-delay: -3s; }
    .ct-hero-orb:nth-child(3) { width: 200px; height: 200px; background: rgba(255,255,255,0.06); top: 50%; left: 60%; animation-delay: -5s; }
    @@keyframes ctOrbFloat {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-20px) scale(1.05); }
    }
    .ct-hero h1 {
        font-size: clamp(36px, 6vw, 72px);
        font-weight: 900;
        color: white;
        letter-spacing: -2px;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }
    .ct-hero p {
        font-size: clamp(16px, 2vw, 20px);
        color: rgba(255,255,255,0.75);
        max-width: 540px;
        margin: 0 auto 32px;
        position: relative;
        z-index: 2;
        line-height: 1.7;
    }
    .ct-hero-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        background: white;
        color: var(--accent);
        font-weight: 700;
        font-size: 14px;
        border-radius: 14px;
        text-decoration: none;
        transition: all 0.3s;
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        position: relative;
        z-index: 2;
    }
    .ct-hero-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.2);
    }
    .ct-hero-stats {
        display: flex;
        justify-content: center;
        gap: clamp(24px, 4vw, 56px);
        position: relative;
        z-index: 2;
        margin-top: 40px;
    }
    .ct-hero-stat-val {
        font-size: clamp(28px, 4vw, 42px);
        font-weight: 900;
        color: white;
        line-height: 1;
    }
    .ct-hero-stat-label {
        font-size: 12px;
        color: rgba(255,255,255,0.6);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-top: 8px;
        font-weight: 500;
    }

    .ct-card {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        text-decoration: none;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid var(--border);
        background: var(--card);
    }
    .ct-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 50px -15px rgba(0,0,0,0.2);
        border-color: var(--accent);
    }
    .ct-card-cover {
        position: relative;
        height: 120px;
        overflow: hidden;
    }
    .ct-card-cover::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 40px;
        background: linear-gradient(transparent, var(--card));
    }
    .ct-card-icon {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
        z-index: 2;
    }
    .ct-card-status {
        position: absolute;
        top: 16px;
        left: 16px;
        z-index: 2;
    }
    .ct-card-status span {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .ct-status-active { background: #22c55e; color: white; animation: ctPulse 2s ease-in-out infinite; }
    .ct-status-draft { background: #eab308; color: #1a1a1a; }
    .ct-status-finished { background: rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px); }
    @@keyframes ctPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.4); }
        50% { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
    }
    .ct-card-body { padding: 20px; }
    .ct-card-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 12px;
        transition: color 0.3s;
    }
    .ct-card:hover .ct-card-title { color: var(--accent); }
    .ct-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 16px;
    }
    .ct-card-meta span { display: flex; align-items: center; gap: 4px; }
    .ct-card-tags { display: flex; gap: 6px; margin-bottom: 16px; }
    .ct-card-tag {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
    }
    .ct-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 16px;
        border-top: 1px solid var(--border);
    }
    .ct-card-footer-left {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--text-muted);
    }
    .ct-card-arrow {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--accent-glow);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent);
        font-size: 14px;
        transition: all 0.3s;
    }
    .ct-card:hover .ct-card-arrow {
        background: var(--accent);
        color: white;
        transform: translateX(4px);
    }

    .ct-sidebar {
        padding: 24px;
        border-radius: 20px;
        background: var(--card);
        border: 1px solid var(--border);
    }
    .ct-sidebar-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 20px;
    }
    .ct-sidebar-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }
    .ct-sidebar-item:last-child { border-bottom: none; }
    .ct-sidebar-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--accent-glow);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent);
        font-size: 14px;
        flex-shrink: 0;
    }
    .ct-sidebar-text { font-size: 13px; color: var(--text-muted); line-height: 1.5; }

    .ct-pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 32px;
    }
    .ct-page-btn {
        min-width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid var(--border);
        color: var(--text-muted);
        background: var(--card);
    }
    .ct-page-btn:hover {
        border-color: var(--accent);
        color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px var(--accent-glow);
    }
    .ct-page-btn.active {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
        box-shadow: 0 4px 20px var(--accent-glow);
        transform: translateY(-2px);
    }
    .ct-page-btn.disabled { opacity: 0.4; pointer-events: none; }
    .ct-page-dots { color: var(--text-muted); font-size: 14px; padding: 0 4px; }

    .ct-activity{position:sticky;top:80px}
    .ct-heatmap-card{border-radius:16px;background:var(--card);border:1px solid var(--border);padding:20px;margin-bottom:16px}
    .ct-heatmap-title{font-size:13px;font-weight:700;color:var(--text);margin-bottom:12px;display:flex;align-items:center;gap:6px}
    .ct-heatmap-grid{display:flex;gap:3px;overflow-x:auto;padding-bottom:4px}
    .ct-heatmap-week{display:flex;flex-direction:column;gap:3px}
    .ct-heatmap-cell{width:12px;height:12px;border-radius:3px;cursor:pointer;transition:all .15s;position:relative}
    .ct-heatmap-cell:hover{outline:2px solid var(--accent);outline-offset:1px}
    .ct-heatmap-lvl-0{background:var(--bg);border:1px solid var(--border)}
    .ct-heatmap-lvl-1{background:#0e4429}
    .ct-heatmap-lvl-2{background:#006d32}
    .ct-heatmap-lvl-3{background:#26a641}
    .ct-heatmap-lvl-4{background:#39d353}
    [data-theme*="-light"] .ct-heatmap-lvl-0{background:var(--bg);border-color:var(--border)}
    [data-theme*="-light"] .ct-heatmap-lvl-1{background:#9be9a8}
    [data-theme*="-light"] .ct-heatmap-lvl-2{background:#40c463}
    [data-theme*="-light"] .ct-heatmap-lvl-3{background:#30a14e}
    [data-theme*="-light"] .ct-heatmap-lvl-4{background:#216e39}
    .ct-heatmap-legend{display:flex;align-items:center;gap:4px;margin-top:10px;justify-content:flex-end}
    .ct-heatmap-legend span{font-size:10px;color:var(--text-muted)}
    .ct-heatmap-legend-cell{width:12px;height:12px;border-radius:3px}
    .ct-heatmap-tooltip{display:none;position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);padding:4px 8px;border-radius:6px;background:var(--bg-elevated);border:1px solid var(--border);color:var(--text);font-size:10px;white-space:nowrap;z-index:10;pointer-events:none;box-shadow:0 4px 12px rgba(0,0,0,.3)}
    .ct-heatmap-cell:hover .ct-heatmap-tooltip{display:block}
</style>
@endsection

@section('content')
<section class="ct-hero">
    <div class="ct-hero-grid"></div>
    <div class="ct-hero-orb"></div>
    <div class="ct-hero-orb"></div>
    <div class="ct-hero-orb"></div>

    <h1 class="reveal-up" data-delay="0">{{ __('Coding Contests') }}</h1>
    <p class="reveal-up" data-delay="0.1">{{ __('Compete with developers worldwide, solve challenges, and climb the leaderboard.') }}</p>

    @auth
    <a href="{{ route('contests.create') }}" class="ct-hero-btn">
        <i class="fas fa-plus"></i> {{ __('Create Contest') }}
    </a>
    @endauth

    <div class="ct-hero-stats">
        <div class="ct-hero-stat">
            <div class="ct-hero-stat-val">{{ $contests->total() }}</div>
            <div class="ct-hero-stat-label">{{ __('Contests') }}</div>
        </div>
        <div class="ct-hero-stat">
            <div class="ct-hero-stat-val">{{ \App\Models\Contest::where('status','active')->count() }}</div>
            <div class="ct-hero-stat-label">{{ __('Active') }}</div>
        </div>
        <div class="ct-hero-stat">
            <div class="ct-hero-stat-val">{{ \App\Models\ContestProblem::count() }}</div>
            <div class="ct-hero-stat-label">{{ __('Problems') }}</div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl text-sm font-medium" style="background:rgba(34,197,94,0.1);color:#22c55e;border:1px solid rgba(34,197,94,0.2)">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="grid md:grid-cols-2 gap-5">
                @forelse($contests as $contest)
                @php
                $gradients = [
                    'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                    'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                    'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                    'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                    'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
                    'linear-gradient(135deg, #fccb90 0%, #d57eeb 100%)',
                    'linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%)',
                ];
                $gradient = $gradients[$contest->id % count($gradients)];
                $diffColor = match($contest->difficulty) { 'easy' => '#22c55e', 'medium' => '#eab308', 'hard' => '#ef4444', default => '#6366f1' };
                @endphp
                <a href="{{ route('contests.show', $contest->id) }}" class="ct-card reveal-up" data-stagger="{{ $loop->index }}">
                    <div class="ct-card-cover" style="background:{{ $gradient }}">
                        <div class="ct-card-status">
                            @if($contest->status === 'active')
                            <span class="ct-status-active">{{ __('Active') }}</span>
                            @elseif($contest->status === 'draft')
                            <span class="ct-status-draft">{{ __('Draft') }}</span>
                            @else
                            <span class="ct-status-finished">{{ __('Finished') }}</span>
                            @endif
                        </div>
                        <div class="ct-card-icon"><i class="fas fa-trophy"></i></div>
                    </div>
                    <div class="ct-card-body">
                        <h3 class="ct-card-title">{{ $contest->title }}</h3>
                        <div class="ct-card-meta">
                            <span><i class="fas fa-list-check"></i> {{ $contest->problems_count }} {{ __('problems') }}</span>
                            <span><i class="fas fa-users"></i> {{ $contest->submissions_count }} {{ __('submissions') }}</span>
                            <span><i class="fas fa-clock"></i> {{ $contest->time_limit }} {{ __('min') }}</span>
                        </div>
                        <div class="ct-card-tags">
                            <span class="ct-card-tag" style="background:{{ $diffColor }}15;color:{{ $diffColor }}">{{ ucfirst($contest->difficulty) }}</span>
                            @if($contest->start_time)
                            <span class="ct-card-tag" style="background:var(--accent-glow);color:var(--accent)">{{ $contest->start_time->diffForHumans() }}</span>
                            @endif
                        </div>
                        <div class="ct-card-footer">
                            <div class="ct-card-footer-left">
                                @auth
                                @if(Auth::id() === $contest->created_by)
                                <i class="fas fa-edit" style="color:var(--accent)"></i> {{ __('Owner') }}
                                @endif
                                @endauth
                            </div>
                            <div class="ct-card-arrow"><i class="fas fa-arrow-right"></i></div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="col-span-full text-center py-16">
                    <i class="fas fa-trophy text-4xl mb-4" style="color:var(--text-muted)"></i>
                    <p style="color:var(--text-muted)">{{ __('No contests yet') }}</p>
                </div>
                @endforelse
            </div>

            @if($contests->hasPages())
            <div class="ct-pagination">
                @if($contests->onFirstPage())
                <span class="ct-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                @else
                <a href="{{ $contests->previousPageUrl() }}" class="ct-page-btn"><i class="fas fa-chevron-left"></i></a>
                @endif

                @foreach($contests->getUrlRange(max(1, $contests->currentPage() - 2), min($contests->lastPage(), $contests->currentPage() + 2)) as $page => $url)
                @if($page == $contests->currentPage())
                <span class="ct-page-btn active">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="ct-page-btn">{{ $page }}</a>
                @endif
                @endforeach

                @if($contests->currentPage() + 2 < $contests->lastPage())
                <span class="ct-page-dots">...</span>
                <a href="{{ $contests->url($contests->lastPage()) }}" class="ct-page-btn">{{ $contests->lastPage() }}</a>
                @endif

                @if($contests->hasMorePages())
                <a href="{{ $contests->nextPageUrl() }}" class="ct-page-btn"><i class="fas fa-chevron-right"></i></a>
                @else
                <span class="ct-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
            @endif
        </div>

        <div>
            <div class="ct-activity">
                @auth
                <div class="ct-heatmap-card">
                    <div class="ct-heatmap-title">
                        <i class="fas fa-fire" style="color:var(--accent)"></i>
                        {{ __('Your Activity') }}
                        <span style="margin-left:auto;font-size:12px;font-weight:500;color:var(--text-muted)" id="heatmap-total"></span>
                    </div>
                    <div x-data="heatmap()" x-init="init()" id="heatmap-container">
                        <div class="ct-heatmap-grid" id="heatmap-grid"></div>
                    </div>
                    <div class="ct-heatmap-legend">
                        <span>{{ __('Less') }}</span>
                        <div class="ct-heatmap-legend-cell ct-heatmap-lvl-0"></div>
                        <div class="ct-heatmap-legend-cell ct-heatmap-lvl-1"></div>
                        <div class="ct-heatmap-legend-cell ct-heatmap-lvl-2"></div>
                        <div class="ct-heatmap-legend-cell ct-heatmap-lvl-3"></div>
                        <div class="ct-heatmap-legend-cell ct-heatmap-lvl-4"></div>
                        <span>{{ __('More') }}</span>
                    </div>
                </div>
                @endauth
                <div class="ct-sidebar">
                    <div class="ct-sidebar-title">{{ __('How It Works') }}</div>
                <div class="ct-sidebar-item">
                    <div class="ct-sidebar-icon"><i class="fas fa-code"></i></div>
                    <div class="ct-sidebar-text">{{ __('Solve algorithm problems in real-time') }}</div>
                </div>
                <div class="ct-sidebar-item">
                    <div class="ct-sidebar-icon"><i class="fas fa-clock"></i></div>
                    <div class="ct-sidebar-text">{{ __('Compete against other developers') }}</div>
                </div>
                <div class="ct-sidebar-item">
                    <div class="ct-sidebar-icon"><i class="fas fa-trophy"></i></div>
                    <div class="ct-sidebar-text">{{ __('Earn points and climb the leaderboard') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function heatmap(){return{init(){const data=@json($activityData);const grid=document.getElementById('heatmap-grid');if(!grid)return;const today=new Date();const total=Object.values(data).reduce((a,b)=>a+b,0);document.getElementById('heatmap-total').textContent=total+' {{ __("submissions this year") }}';const weeks=53;const startDate=new Date(today);startDate.setDate(startDate.getDate()-(weeks*7-1)+(6-startDate.getDay()));let html='';let currentDate=new Date(startDate);for(let w=0;w<weeks;w++){html+='<div class="ct-heatmap-week">';for(let d=0;d<7;d++){const dateStr=currentDate.toISOString().split('T')[0];const count=data[dateStr]||0;let lvl=0;if(count>=10)lvl=4;else if(count>=6)lvl=3;else if(count>=3)lvl=2;else if(count>=1)lvl=1;const label=count>0?count+' {{ __("submissions on") }} '+dateStr:'{{ __("No submissions on") }} '+dateStr;const isFuture=currentDate>today;const opacity=isFuture?'opacity:0.3;pointer-events:none;':'';html+='<div class="ct-heatmap-cell ct-heatmap-lvl-'+(isFuture?0:lvl)+'" style="'+opacity+'"><div class="ct-heatmap-tooltip">'+label+'</div></div>';currentDate.setDate(currentDate.getDate()+1)}html+='</div>'}grid.innerHTML=html}}}
</script>
@endsection
