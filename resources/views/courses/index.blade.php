@extends('layouts.app')

@section('title', __('Courses') . ' - CodeMaster')

@section('head')
<style>
    .cs-hero {
        position: relative;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 50%, var(--accent-3) 100%);
        padding: clamp(36px, 6vw, 80px) 24px clamp(60px, 8vw, 100px);
        overflow: hidden;
        text-align: center;
    }
    .cs-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%);
    }
    .cs-hero::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 60px;
        background: var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .cs-hero-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 40px 40px;
    }
    .cs-hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        animation: csOrbFloat 8s ease-in-out infinite;
    }
    .cs-hero-orb:nth-child(1) { width: 300px; height: 300px; background: rgba(255,255,255,0.1); top: -100px; left: -50px; }
    .cs-hero-orb:nth-child(2) { width: 250px; height: 250px; background: rgba(255,255,255,0.08); bottom: -80px; right: -30px; animation-delay: -3s; }
    .cs-hero-orb:nth-child(3) { width: 200px; height: 200px; background: rgba(255,255,255,0.06); top: 50%; left: 60%; animation-delay: -5s; }
    @@keyframes csOrbFloat {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-20px) scale(1.05); }
    }
    .cs-hero h1 {
        font-size: clamp(36px, 6vw, 72px);
        font-weight: 900;
        color: white;
        letter-spacing: -2px;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }
    .cs-hero p {
        font-size: clamp(16px, 2vw, 20px);
        color: rgba(255,255,255,0.75);
        max-width: 540px;
        margin: 0 auto 40px;
        position: relative;
        z-index: 2;
        line-height: 1.7;
    }
    .cs-hero-stats {
        display: flex;
        justify-content: center;
        gap: clamp(24px, 4vw, 56px);
        position: relative;
        z-index: 2;
    }
    .cs-hero-stat-val {
        font-size: clamp(28px, 4vw, 42px);
        font-weight: 900;
        color: white;
        line-height: 1;
    }
    .cs-hero-stat-label {
        font-size: 12px;
        color: rgba(255,255,255,0.6);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-top: 8px;
        font-weight: 500;
    }
    .cs-search {
        position: relative;
        max-width: 520px;
        margin: 0 auto 48px;
        z-index: 2;
    }
    .cs-search input {
        width: 100%;
        padding: 16px 20px 16px 50px;
        border-radius: 16px;
        border: 1px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
        font-size: 14px;
        color: var(--text);
        outline: none;
        transition: all 0.3s;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .cs-search input:focus {
        border-color: var(--accent);
        box-shadow: 0 10px 40px rgba(0,0,0,0.1), 0 0 0 3px var(--accent-glow);
    }
    .cs-search i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }
    .cs-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }
    .cs-filter {
        padding: 8px 20px;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid rgba(255,255,255,0.25);
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(8px);
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        transition: all 0.3s;
    }
    .cs-filter:hover {
        background: rgba(255,255,255,0.2);
        color: white;
    }
    .cs-filter.active {
        background: white;
        color: var(--accent);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }

    .cs-pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 48px;
    }
    .cs-page-btn {
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
    .cs-page-btn:hover {
        border-color: var(--accent);
        color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px var(--accent-glow);
    }
    .cs-page-btn.active {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
        box-shadow: 0 4px 20px var(--accent-glow);
        transform: translateY(-2px);
    }
    .cs-page-btn.disabled {
        opacity: 0.4;
        pointer-events: none;
    }
    .cs-page-dots {
        color: var(--text-muted);
        font-size: 14px;
        padding: 0 4px;
    }

    .cs-card:hover {
        transform: translateY(-8px) scale(1.02) !important;
        box-shadow: 0 25px 60px -15px rgba(0,0,0,0.2);
    }
    .cs-card.visible {
        opacity: 1 !important;
        transform: translateY(0) scale(1) !important;
    }
    .cs-card:hover .cs-card-icon {
        transform: scale(1.2) rotate(-8deg);
        filter: drop-shadow(0 6px 20px rgba(0,0,0,0.25));
    }
    .cs-card:hover .cs-card-glow {
        opacity: 1;
        transform: scale(1.3);
    }
    .cs-card:hover .cs-card-shine {
        transform: translateX(200%);
    }
    .cs-card:hover .cs-card-cover-overlay {
        opacity: 0.3;
    }
    .cs-card-icon {
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .cs-card-glow {
        position: absolute;
        bottom: -30px;
        right: -30px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        filter: blur(50px);
        opacity: 0;
        transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
        z-index: 1;
    }
    .cs-card-shine {
        position: absolute;
        top: 0;
        left: -100%;
        width: 50%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: transform 0.7s ease;
        pointer-events: none;
        z-index: 3;
    }
    .cs-card-cover-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 100%);
        opacity: 0;
        transition: opacity 0.4s;
        z-index: 2;
    }
    @@keyframes csFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    .cs-card-float {
        animation: csFloat 4s ease-in-out infinite;
    }
    @@keyframes csPulse {
        0%, 100% { opacity: 0.6; }
        50% { opacity: 1; }
    }
    .cs-badge-pulse {
        animation: csPulse 2s ease-in-out infinite;
    }
</style>
@endsection

@section('content')
<section class="cs-hero">
    <div class="cs-hero-grid"></div>
    <div class="cs-hero-orb"></div>
    <div class="cs-hero-orb"></div>
    <div class="cs-hero-orb"></div>

    <h1 class="reveal-up" data-delay="0">{{ __('Explore Courses') }}</h1>
    <p class="reveal-up" data-delay="0.1">{{ __('Master new skills with expert-led courses in programming, design, and more.') }}</p>

    <div class="cs-search reveal-up" data-delay="0.2">
        <form action="{{ route('courses.index') }}" method="GET">
            <i class="fas fa-search"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search courses...') }}">
        </form>
    </div>

    <div class="cs-filters reveal-up" data-delay="0.3">
        @foreach(['All' => '', 'Frontend' => 'frontend', 'Backend' => 'backend', 'Design' => 'design', 'DevOps' => 'devops', 'Other' => 'other'] as $label => $value)
        <a href="{{ route('courses.index', array_merge(request()->except('category', 'page'), $value ? ['category' => $value] : [])) }}" class="cs-filter {{ (request('category') === $value || (!$value && !request('category'))) ? 'active' : '' }}">{{ __($label) }}</a>
        @endforeach
    </div>

    <div class="cs-hero-stats" style="margin-top:40px">
        <div class="cs-hero-stat">
            <div class="cs-hero-stat-val">{{ $courses->total() }}</div>
            <div class="cs-hero-stat-label">{{ __('Courses') }}</div>
        </div>
        <div class="cs-hero-stat">
            <div class="cs-hero-stat-val">{{ \App\Models\Lesson::count() }}</div>
            <div class="cs-hero-stat-label">{{ __('Lessons') }}</div>
        </div>
        <div class="cs-hero-stat">
            <div class="cs-hero-stat-val">{{ \App\Models\User::count() }}</div>
            <div class="cs-hero-stat-label">{{ __('Students') }}</div>
        </div>
        <div class="cs-hero-stat">
            <div class="cs-hero-stat-val">{{ \App\Models\Certificate::count() }}</div>
            <div class="cs-hero-stat-label">{{ __('Certificates') }}</div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($courses as $course)
        @php
        $catIcon = match(true) {
            str_contains(strtolower($course->title), 'html') => 'fab fa-html5',
            str_contains(strtolower($course->title), 'css') => 'fab fa-css3-alt',
            str_contains(strtolower($course->title), 'javascript') || str_contains(strtolower($course->title), 'js') => 'fab fa-js',
            str_contains(strtolower($course->title), 'react') => 'fab fa-react',
            str_contains(strtolower($course->title), 'php') => 'fab fa-php',
            str_contains(strtolower($course->title), 'laravel') => 'fab fa-laravel',
            str_contains(strtolower($course->title), 'python') => 'fab fa-python',
            str_contains(strtolower($course->title), 'java') && !str_contains(strtolower($course->title), 'javascript') => 'fab fa-java',
            str_contains(strtolower($course->title), 'c#') || str_contains(strtolower($course->title), 'csharp') => 'csharp',
            str_contains(strtolower($course->title), 'c++') || str_contains(strtolower($course->title), 'cplusplus') => 'cplusplus',
            str_contains(strtolower($course->title), 'git') && !str_contains(strtolower($course->title), 'github') => 'fab fa-git-alt',
            str_contains(strtolower($course->title), 'github') => 'fab fa-github',
            str_contains(strtolower($course->title), 'docker') => 'fab fa-docker',
            str_contains(strtolower($course->title), 'kubernetes') || str_contains(strtolower($course->title), 'k8s') => 'fas fa-dharmachakra',
            str_contains(strtolower($course->title), 'mysql') || str_contains(strtolower($course->title), 'sql') => 'fas fa-database',
            str_contains(strtolower($course->title), 'postgres') => 'fas fa-database',
            str_contains(strtolower($course->title), 'node') => 'fab fa-node-js',
            str_contains(strtolower($course->title), 'typescript') || str_contains(strtolower($course->title), 'ts') => 'fab fa-js',
            str_contains(strtolower($course->title), 'design') || str_contains(strtolower($course->title), 'ui') || str_contains(strtolower($course->title), 'ux') => 'fas fa-palette',
            str_contains(strtolower($course->title), 'mobile') || str_contains(strtolower($course->title), 'android') || str_contains(strtolower($course->title), 'ios') => 'fas fa-mobile-alt',
            str_contains(strtolower($course->title), 'english') => 'fas fa-language',
            $course->category === 'frontend' => 'fab fa-html5',
            $course->category === 'backend' => 'fab fa-php',
            $course->category === 'design' => 'fas fa-palette',
            $course->category === 'devops' => 'fas fa-server',
            default => 'fas fa-code',
        };
        $isCpp = $catIcon === 'cplusplus';
        $isCSharp = $catIcon === 'csharp';
        $id = $course->id;
        $gradients = [
            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
            'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
            'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
            'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
            'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
            'linear-gradient(135deg, #fccb90 0%, #d57eeb 100%)',
            'linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%)',
            'linear-gradient(135deg, #f5576c 0%, #ff6a88 100%)',
            'linear-gradient(135deg, #667eea 0%, #f093fb 100%)',
            'linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%)',
            'linear-gradient(135deg, #fddb92 0%, #d1fdff 100%)',
            'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
            'linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%)',
            'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
            'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)',
            'linear-gradient(135deg, #cfd9df 0%, #e2ebf0 100%)',
            'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
            'linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%)',
            'linear-gradient(135deg, #0c3483 0%, #a2b6df 50%, #6b8cce 100%)',
            'linear-gradient(135deg, #fc5c7d 0%, #6a82fb 100%)',
            'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
            'linear-gradient(135deg, #ee9ca7 0%, #ffdde1 100%)',
            'linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%)',
        ];
        $gradient = $gradients[$id % count($gradients)];
        $slidesCount = \App\Models\Lesson::where('course_id', $id)->whereNotNull('presentation_url')->where('presentation_url', '!=', '')->count();

        $catColors = match(true) {
            str_contains(strtolower($course->title), 'html') => ['#e34f26', '#f96d3b'],
            str_contains(strtolower($course->title), 'css') => ['#1572b6', '#33a9dc'],
            str_contains(strtolower($course->title), 'javascript') || str_contains(strtolower($course->title), 'js') => ['#f7df1e', '#f0db4f'],
            str_contains(strtolower($course->title), 'react') => ['#61dafb', '#0d8ecf'],
            str_contains(strtolower($course->title), 'php') => ['#777bb4', '#52599f'],
            str_contains(strtolower($course->title), 'laravel') => ['#ff2d20', '#e83b3a'],
            str_contains(strtolower($course->title), 'python') => ['#3776ab', '#ffd43b'],
            str_contains(strtolower($course->title), 'java') => ['#f89820', '#5382a1'],
            str_contains(strtolower($course->title), 'c#') || str_contains(strtolower($course->title), 'csharp') => ['#68217a', '#9b4dca'],
            str_contains(strtolower($course->title), 'c++') || str_contains(strtolower($course->title), 'cplusplus') => ['#00599c', '#004c8c'],
            str_contains(strtolower($course->title), 'git') => ['#f05032', '#de4c36'],
            str_contains(strtolower($course->title), 'docker') => ['#2496ed', '#1a7bc4'],
            str_contains(strtolower($course->title), 'mysql') => ['#4479a1', '#00758f'],
            str_contains(strtolower($course->title), 'node') => ['#339933', '#2d892d'],
            str_contains(strtolower($course->title), 'typescript') => ['#3178c6', '#235a97'],
            default => ['#6366f1', '#8b5cf6'],
        };
        @endphp
        <div class="cs-card reveal-up" data-stagger="{{ $loop->index }}" style="background:var(--card);border:1px solid var(--border);border-radius:20px;overflow:hidden;position:relative">
            <div class="h-52 relative overflow-hidden" style="background:{{ $gradient }}">
                <div style="position:absolute;inset:0;background:radial-gradient(circle at 20% 80%, rgba(255,255,255,0.25) 0%, transparent 50%),radial-gradient(circle at 80% 20%, rgba(255,255,255,0.2) 0%, transparent 50%),radial-gradient(circle at 50% 50%, rgba(255,255,255,0.05) 0%, transparent 70%)"></div>
                <div class="cs-card-cover-overlay"></div>
                <div class="cs-card-shine"></div>
                <div class="cs-card-glow" style="background:{{ $catColors[0] }}"></div>
                @if($isCpp)
                <span class="cs-card-icon" style="position:absolute;right:28px;bottom:16px;font-size:64px;font-weight:900;color:rgba(255,255,255,0.22);z-index:2;font-family:'JetBrains Mono',monospace;letter-spacing:-3px;text-shadow:0 4px 20px rgba(0,0,0,0.15)">C++</span>
                @elseif($isCSharp)
                <span class="cs-card-icon" style="position:absolute;right:28px;bottom:16px;font-size:64px;font-weight:900;color:rgba(255,255,255,0.22);z-index:2;font-family:'JetBrains Mono',monospace;letter-spacing:-3px;text-shadow:0 4px 20px rgba(0,0,0,0.15)">C#</span>
                @else
                <i class="{{ $catIcon }} cs-card-icon" style="position:absolute;right:28px;bottom:16px;font-size:72px;color:rgba(255,255,255,0.2);z-index:2;text-shadow:0 4px 20px rgba(0,0,0,0.1)"></i>
                @endif
                <div class="absolute top-4 left-4 z-10">
                    <span class="cs-badge-pulse px-3 py-1.5 bg-white/20 backdrop-blur-sm text-white text-xs font-semibold rounded-lg">{{ $course->category }}</span>
                </div>
                <div class="absolute top-4 right-4 z-10">
                    <span class="px-3 py-1.5 bg-white text-xs font-bold rounded-lg shadow-lg" style="color:{{ $catColors[0] }}">{{ __('courses_level_' . mb_strtolower($course->level)) }}</span>
                </div>
            </div>
            <div class="p-5">
                <h3 class="text-lg font-bold mb-2 group-hover:text-indigo-600 transition" style="color:var(--text)">{{ $course->title }}</h3>
                <p class="text-sm mb-2" style="color:var(--text-muted)">{{ $course->instructor }}</p>
                <p class="text-sm mb-4 line-clamp-2" style="color:var(--text-muted)">{{ $course->description }}</p>
                <div class="flex items-center justify-between text-xs mb-4" style="color:var(--text-muted)">
                    <span class="flex items-center gap-2">
                        <span><i class="fas fa-book-open mr-1"></i>{{ $course->lessons->count() }} {{ __('lessons') }}</span>
                        @if($slidesCount > 0)<span><i class="fas fa-desktop mr-1"></i>{{ $slidesCount }} {{ __('slides') }}</span>@endif
                    </span>
                    <span class="px-2 py-0.5 rounded-full" style="background:var(--bg-secondary)">{{ __('courses_level_' . mb_strtolower($course->level)) }}</span>
                </div>
                <a href="{{ route('courses.show', $course->id) }}" class="block w-full py-2.5 text-center text-sm font-semibold rounded-xl transition-all duration-300" style="color:var(--accent);border:2px solid var(--border)" onmouseover="this.style.background='var(--accent)';this.style.color='white';this.style.borderColor='var(--accent)'" onmouseout="this.style.background='';this.style.color='var(--accent)';this.style.borderColor='var(--border)'">
                    {{ __('View Course') }}
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16">
            <i class="fas fa-book-open text-4xl mb-4" style="color:var(--text-muted)"></i>
            <p style="color:var(--text-muted)">{{ __('No courses found') }}</p>
        </div>
        @endforelse
    </div>

    <div class="cs-pagination">
        @if($courses->onFirstPage())
        <span class="cs-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
        @else
        <a href="{{ $courses->previousPageUrl() }}" class="cs-page-btn"><i class="fas fa-chevron-left"></i></a>
        @endif

        @foreach($courses->getUrlRange(max(1, $courses->currentPage() - 2), min($courses->lastPage(), $courses->currentPage() + 2)) as $page => $url)
        @if($page == $courses->currentPage())
        <span class="cs-page-btn active">{{ $page }}</span>
        @else
        <a href="{{ $url }}" class="cs-page-btn">{{ $page }}</a>
        @endif
        @endforeach

        @if($courses->currentPage() + 2 < $courses->lastPage())
        <span class="cs-page-dots">...</span>
        <a href="{{ $courses->url($courses->lastPage()) }}" class="cs-page-btn">{{ $courses->lastPage() }}</a>
        @endif

        @if($courses->hasMorePages())
        <a href="{{ $courses->nextPageUrl() }}" class="cs-page-btn"><i class="fas fa-chevron-right"></i></a>
        @else
        <span class="cs-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
        @endif
    </div>
</div>
@endsection
