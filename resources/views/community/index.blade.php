@extends('layouts.app')

@section('title', __('Community') . ' - CodeMaster')

@section('head')
<style>
    .cm-hero {
        position: relative;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 50%, var(--accent-3) 100%);
        padding: clamp(48px, 8vw, 100px) 24px clamp(40px, 6vw, 80px);
        overflow: hidden;
        text-align: center;
    }
    .cm-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%);
    }
    .cm-hero::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 60px;
        background: var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .cm-hero-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 40px 40px;
    }
    .cm-hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        animation: cmOrbFloat 8s ease-in-out infinite;
    }
    .cm-hero-orb:nth-child(1) { width: 300px; height: 300px; background: rgba(255,255,255,0.1); top: -100px; left: -50px; }
    .cm-hero-orb:nth-child(2) { width: 250px; height: 250px; background: rgba(255,255,255,0.08); bottom: -80px; right: -30px; animation-delay: -3s; }
    .cm-hero-orb:nth-child(3) { width: 200px; height: 200px; background: rgba(255,255,255,0.06); top: 50%; left: 60%; animation-delay: -5s; }
    @@keyframes cmOrbFloat {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-20px) scale(1.05); }
    }
    .cm-hero h1 {
        font-size: clamp(36px, 6vw, 72px);
        font-weight: 900;
        color: white;
        letter-spacing: -2px;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }
    .cm-hero p {
        font-size: clamp(16px, 2vw, 20px);
        color: rgba(255,255,255,0.75);
        max-width: 540px;
        margin: 0 auto 32px;
        position: relative;
        z-index: 2;
        line-height: 1.7;
    }
    .cm-hero-stats {
        display: flex;
        justify-content: center;
        gap: clamp(24px, 4vw, 56px);
        position: relative;
        z-index: 2;
    }
    .cm-hero-stat-val {
        font-size: clamp(28px, 4vw, 42px);
        font-weight: 900;
        color: white;
        line-height: 1;
    }
    .cm-hero-stat-label {
        font-size: 12px;
        color: rgba(255,255,255,0.6);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-top: 8px;
        font-weight: 500;
    }

    .cm-post-card {
        border-radius: 20px;
        background: var(--card);
        border: 1px solid var(--border);
        padding: 24px;
        transition: all 0.3s;
    }
    .cm-post-card:hover {
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }
    .cm-post-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    .cm-post-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid var(--border);
    }
    .cm-post-author {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        text-decoration: none;
        transition: color 0.2s;
    }
    .cm-post-author:hover { color: var(--accent); }
    .cm-post-time { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
    .cm-post-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 8px;
        line-height: 1.3;
    }
    .cm-post-excerpt {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 16px;
    }
    .cm-post-actions {
        display: flex;
        align-items: center;
        gap: 20px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
    }
    .cm-action-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--text-muted);
        background: none;
        border: none;
        cursor: pointer;
        transition: color 0.2s;
        padding: 0;
    }
    .cm-action-btn:hover { color: var(--accent); }
    .cm-action-btn.liked { color: #ef4444; }
    .cm-action-btn.liked i { font-weight: 900; }

    .cm-filter-tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 8px;
    }
    .cm-filter-tab {
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        border: 1px solid var(--border);
        color: var(--text-muted);
        background: var(--card);
    }
    .cm-filter-tab:hover {
        border-color: var(--accent);
        color: var(--accent);
    }
    .cm-filter-tab.active {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }

    .cm-sidebar-card {
        border-radius: 20px;
        background: var(--card);
        border: 1px solid var(--border);
        padding: 24px;
    }
    .cm-sidebar-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 16px;
    }
    .cm-create-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 14px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        background: var(--accent);
        color: white;
    }
    .cm-create-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px var(--accent-glow);
    }
    .cm-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
        background: var(--bg);
        color: var(--text-muted);
        border: 1px solid var(--border);
        transition: all 0.2s;
        cursor: pointer;
        letter-spacing: 0.02em;
    }
    .cm-tag i {
        font-size: 18px;
    }
    .cm-tag:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: var(--accent-glow);
    }

    .cm-empty {
        text-align: center;
        padding: 60px 24px;
        border-radius: 20px;
        background: var(--card);
        border: 1px solid var(--border);
    }
    .cm-empty i { font-size: 48px; color: var(--text-muted); margin-bottom: 16px; }
    .cm-empty p { color: var(--text-muted); }

    .cm-pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 32px;
    }
    .cm-page-btn {
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
    .cm-page-btn:hover {
        border-color: var(--accent);
        color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px var(--accent-glow);
    }
    .cm-page-btn.active {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
        box-shadow: 0 4px 20px var(--accent-glow);
        transform: translateY(-2px);
    }
    .cm-page-btn.disabled { opacity: 0.4; pointer-events: none; }
    .cm-page-dots { color: var(--text-muted); font-size: 14px; padding: 0 4px; }

    .cm-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
        padding: 16px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s;
    }
    .cm-modal-overlay.open { opacity: 1; pointer-events: all; }
    .cm-modal {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 20px;
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        overflow-y: auto;
        transform: scale(0.95) translateY(10px);
        transition: transform 0.3s;
    }
    .cm-modal-overlay.open .cm-modal { transform: scale(1) translateY(0); }
    .cm-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 24px;
        border-bottom: 1px solid var(--border);
    }
    .cm-modal-title { font-size: 18px; font-weight: 700; color: var(--text); }
    .cm-modal-close {
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
    .cm-modal-close:hover { background: var(--accent-glow); color: var(--accent); }
    .cm-modal-body { padding: 24px; }
    .cm-modal-foot {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding: 16px 24px;
        border-top: 1px solid var(--border);
    }
    .cm-form-input {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--bg);
        color: var(--text);
        font-size: 14px;
        transition: border-color 0.3s;
        outline: none;
        margin-bottom: 12px;
    }
    .cm-form-input:focus { border-color: var(--accent); }
    .cm-form-textarea {
        width: 100%;
        min-height: 140px;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--bg);
        color: var(--text);
        font-size: 14px;
        resize: vertical;
        transition: border-color 0.3s;
        outline: none;
        margin-bottom: 16px;
    }
    .cm-form-textarea:focus { border-color: var(--accent); }
    .cm-btn-cancel {
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid var(--border);
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s;
    }
    .cm-btn-cancel:hover { background: var(--bg); }
    .cm-btn-submit {
        padding: 10px 24px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        background: var(--accent);
        color: white;
        cursor: pointer;
        transition: all 0.3s;
    }
    .cm-btn-submit:hover { transform: translateY(-1px); box-shadow: 0 4px 12px var(--accent-glow); }

    .cm-comment {
        display: flex;
        gap: 10px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }
    .cm-comment:last-child { border-bottom: none; }
    .cm-comment-avatar {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        flex-shrink: 0;
    }
    .cm-comment-body {
        flex: 1;
        background: var(--bg);
        border-radius: 12px;
        padding: 10px 14px;
    }
    .cm-comment-name { font-size: 13px; font-weight: 600; color: var(--text); }
    .cm-comment-time { font-size: 11px; color: var(--text-muted); margin-left: 8px; }
    .cm-comment-text { font-size: 13px; color: var(--text-muted); margin-top: 4px; line-height: 1.5; }

    .cm-detail-title { font-size: 20px; font-weight: 800; color: var(--text); margin-bottom: 12px; }
    .cm-detail-content { font-size: 14px; color: var(--text-muted); line-height: 1.7; white-space: pre-wrap; margin-bottom: 20px; }
</style>
@endsection

@section('content')
<section class="cm-hero">
    <div class="cm-hero-grid"></div>
    <div class="cm-hero-orb"></div>
    <div class="cm-hero-orb"></div>
    <div class="cm-hero-orb"></div>

    <h1>{{ __('Community') }}</h1>
    <p>{{ __('Connect with fellow developers, share knowledge, and grow together.') }}</p>

    <div class="cm-hero-stats">
        <div class="cm-hero-stat">
            <div class="cm-hero-stat-val">{{ $posts->total() }}</div>
            <div class="cm-hero-stat-label">{{ __('Posts') }}</div>
        </div>
        <div class="cm-hero-stat">
            <div class="cm-hero-stat-val">{{ \App\Models\User::count() }}</div>
            <div class="cm-hero-stat-label">{{ __('Members') }}</div>
        </div>
            <div class="cm-hero-stat">
                <div class="cm-hero-stat-val">{{ \App\Models\CommunityComment::count() }}</div>
                <div class="cm-hero-stat-label">{{ __('Comments') }}</div>
            </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="communityApp()" x-init="init()">
    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">

            @forelse($posts as $post)
            <div class="cm-post-card" style="margin-bottom:16px" x-data="{ liked: {{ $post->isLikedBy(Auth::id()) ? 'true' : 'false' }}, likes: {{ $post->likes_count }}, commentsCount: {{ $post->comments_count }} }" id="post-{{ $post->id }}">
                <div class="cm-post-header">
                    <img src="{{ $post->user->avatar_url }}" class="cm-post-avatar">
                    <div>
                        <a href="{{ route('profile.show', $post->user_id) }}" class="cm-post-author">{{ $post->user->name ?? __('Unknown') }}</a>
                        <div class="cm-post-time">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                <h3 class="cm-post-title">{{ $post->title }}</h3>
                <p class="cm-post-excerpt">{{ Str::limit(strip_tags($post->content), 220) }}</p>
                @if($post->tags->count())
                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px">
                    @foreach($post->tags as $tag)
                    @php
                        $tagIcons = [
                            'javascript' => 'fa-brands fa-js',
                            'react' => 'fa-brands fa-react',
                            'laravel' => 'fa-brands fa-laravel',
                            'python' => 'fa-brands fa-python',
                            'php' => 'fa-brands fa-php',
                            'nodejs' => 'fa-brands fa-node-js',
                            'typescript' => 'fa-brands fa-js',
                            'docker' => 'fa-brands fa-docker',
                            'kubernetes' => 'fa-solid fa-dharmachakra',
                            'devops' => 'fa-solid fa-gears',
                            'frontend' => 'fa-solid fa-code',
                            'backend' => 'fa-solid fa-server',
                            'css' => 'fa-brands fa-css3-alt',
                            'html' => 'fa-brands fa-html5',
                            'git' => 'fa-brands fa-git-alt',
                            'mysql' => 'fa-solid fa-database',
                            'postgresql' => 'fa-solid fa-database',
                            'java' => 'fa-brands fa-java',
                            'cpp' => 'fa-solid fa-c',
                            'csharp' => 'fa-solid fa-c',
                            'ui-ux' => 'fa-solid fa-pen-nib',
                            'algorithms' => 'fa-solid fa-brain',
                            'interview' => 'fa-solid fa-microphone',
                            'career' => 'fa-solid fa-briefcase',
                            'beginners' => 'fa-solid fa-graduation-cap',
                            'projects' => 'fa-solid fa-folder-open',
                            'code-review' => 'fa-solid fa-code-branch',
                            'testing' => 'fa-solid fa-vial',
                            'security' => 'fa-solid fa-shield-halved',
                            'ai-ml' => 'fa-solid fa-robot',
                        ];
                        $icon = $tagIcons[strtolower($tag->slug)] ?? 'fa-solid fa-code';
                    @endphp
                    <a href="{{ route('community.index', ['tag' => $tag->slug, 'sort' => $sort]) }}" class="cm-tag">
                        <i class="{{ $icon }}"></i>
                        {{ $tag->name }}
                    </a>
                    @endforeach
                </div>
                @endif
                <div class="cm-post-actions">
                    <button @click="toggleLike({{ $post->id }}, $el)" class="cm-action-btn" :class="liked ? 'liked' : ''">
                        <i :class="liked ? 'fas fa-heart' : 'far fa-heart'"></i>
                        <span x-text="likes">{{ $post->likes_count }}</span>
                    </button>
                    <button @click="openPost({{ $post->id }})" class="cm-action-btn">
                        <i class="far fa-comment"></i>
                        <span x-text="commentsCount">{{ $post->comments_count }}</span>
                    </button>
                    <span class="cm-action-btn" style="cursor:default">
                        <i class="far fa-eye"></i>
                        <span>{{ $post->views_count }}</span>
                    </span>
                </div>
            </div>
            @empty
            <div class="cm-empty">
                <i class="fas fa-comments"></i>
                <p>{{ __('No posts yet. Be the first to share!') }}</p>
            </div>
            @endforelse

            @if($posts->hasPages())
            <div class="cm-pagination">
                @if($posts->onFirstPage())
                <span class="cm-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                @else
                <a href="{{ $posts->previousPageUrl() }}" class="cm-page-btn"><i class="fas fa-chevron-left"></i></a>
                @endif

                @foreach($posts->getUrlRange(max(1, $posts->currentPage() - 2), min($posts->lastPage(), $posts->currentPage() + 2)) as $page => $url)
                @if($page == $posts->currentPage())
                <span class="cm-page-btn active">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="cm-page-btn">{{ $page }}</a>
                @endif
                @endforeach

                @if($posts->currentPage() + 2 < $posts->lastPage())
                <span class="cm-page-dots">...</span>
                <a href="{{ $posts->url($posts->lastPage()) }}" class="cm-page-btn">{{ $posts->lastPage() }}</a>
                @endif

                @if($posts->hasMorePages())
                <a href="{{ $posts->nextPageUrl() }}" class="cm-page-btn"><i class="fas fa-chevron-right"></i></a>
                @else
                <span class="cm-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
            @endif
        </div>

        <div class="space-y-6" style="position:sticky;top:80px;align-self:start">
            <div class="cm-sidebar-card" style="background:linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);border:none;color:white">
                <div class="cm-sidebar-title" style="color:white">{{ __('Start a Discussion') }}</div>
                <p style="font-size:13px;color:rgba(255,255,255,0.8);margin-bottom:16px">{{ __('Share your thoughts, ask questions, or help others.') }}</p>
                <button @click="showEditor = true" class="cm-create-btn" style="background:rgba(255,255,255,0.2);backdrop-filter:blur(8px)">
                    <i class="fas fa-plus"></i> {{ __('Create Post') }}
                </button>
            </div>

            <div class="cm-sidebar-card">
                <div class="cm-sidebar-title">{{ __('Tags') }}</div>
                @if($activeTag)
                <div style="margin-bottom:12px">
                    <a href="{{ route('community.index', ['sort' => $sort]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;background:var(--accent);color:white;text-decoration:none">
                        {{ $activeTag->name }}
                        <i class="fas fa-times" style="font-size:10px"></i>
                    </a>
                </div>
                @endif
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    @forelse($popularTags as $tag)
                    @php
                        $tagIcons = [
                            'javascript' => 'fa-brands fa-js text-yellow-400',
                            'react' => 'fa-brands fa-react text-cyan-400',
                            'laravel' => 'fa-brands fa-laravel text-red-500',
                            'python' => 'fa-brands fa-python text-blue-400',
                            'php' => 'fa-brands fa-php text-indigo-400',
                            'nodejs' => 'fa-brands fa-node-js text-green-500',
                            'typescript' => 'fa-brands fa-js text-blue-500',
                            'docker' => 'fa-brands fa-docker text-blue-500',
                            'kubernetes' => 'fa-solid fa-dharmachakra text-blue-400',
                            'devops' => 'fa-solid fa-gears text-sky-400',
                            'frontend' => 'fa-solid fa-code text-pink-400',
                            'backend' => 'fa-solid fa-server text-green-400',
                            'css' => 'fa-brands fa-css3-alt text-blue-400',
                            'html' => 'fa-brands fa-html5 text-orange-500',
                            'git' => 'fa-brands fa-git-alt text-orange-600',
                            'mysql' => 'fa-solid fa-database text-blue-500',
                            'postgresql' => 'fa-solid fa-database text-blue-600',
                            'java' => 'fa-brands fa-java text-red-400',
                            'cpp' => 'fa-solid fa-c text-blue-400',
                            'csharp' => 'fa-solid fa-c text-purple-500',
                            'ui-ux' => 'fa-solid fa-pen-nib text-fuchsia-400',
                            'algorithms' => 'fa-solid fa-brain text-amber-400',
                            'interview' => 'fa-solid fa-microphone text-teal-400',
                            'career' => 'fa-solid fa-briefcase text-emerald-400',
                            'beginners' => 'fa-solid fa-graduation-cap text-green-400',
                            'projects' => 'fa-solid fa-folder-open text-violet-400',
                            'code-review' => 'fa-solid fa-code-branch text-sky-400',
                            'testing' => 'fa-solid fa-vial text-lime-400',
                            'security' => 'fa-solid fa-shield-halved text-red-400',
                            'ai-ml' => 'fa-solid fa-robot text-purple-400',
                        ];
                        $icon = $tagIcons[strtolower($tag->slug)] ?? 'fa-solid fa-code text-gray-400';
                    @endphp
                    <a href="{{ route('community.index', ['tag' => $tag->slug, 'sort' => $sort]) }}" class="cm-tag {{ $activeTag && $activeTag->id === $tag->id ? 'active' : '' }}">
                        <i class="{{ $icon }}"></i>
                        {{ $tag->name }}
                    </a>
                    @empty
                    <span style="font-size:13px;color:var(--text-muted)">{{ __('No tags yet') }}</span>
                    @endforelse
                </div>
            </div>

            @if(isset($latestNews) && $latestNews->count())
            <div class="cm-sidebar-card">
                <div class="cm-sidebar-title"><i class="fas fa-newspaper" style="margin-right:6px;color:var(--accent)"></i>{{ __('News') }}</div>
                <div style="display:flex;flex-direction:column;gap:12px">
                    @foreach($latestNews as $news)
                    <a href="#" style="text-decoration:none;display:flex;gap:10px;align-items:flex-start;padding:8px;border-radius:10px;transition:background .15s" onmouseover="this.style.background='var(--bg-2)'" onmouseout="this.style.background='transparent'">
                        @if($news->image)
                        <img src="{{ $news->image }}" style="width:48px;height:48px;border-radius:8px;object-fit:cover;flex-shrink:0">
                        @else
                        <div style="width:48px;height:48px;border-radius:8px;background:linear-gradient(135deg,var(--accent),var(--accent-2));display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-newspaper" style="color:white;font-size:14px"></i>
                        </div>
                        @endif
                        <div style="min-width:0;flex:1">
                            <div style="font-size:12px;font-weight:700;color:var(--text);line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $news->title }}</div>
                            <div style="font-size:10px;color:var(--text-muted);margin-top:2px">{{ $news->created_at->diffForHumans() }}</div>
                            @if($news->tags->count())
                            <div style="display:flex;flex-wrap:wrap;gap:3px;margin-top:4px">
                                @foreach($news->tags->take(2) as $ntag)
                                <span style="font-size:9px;padding:1px 5px;border-radius:4px;background:color-mix(in srgb,var(--accent) 8%,var(--card));color:var(--accent);font-weight:600">{{ $ntag->name }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <div x-show="showEditor" x-transition class="cm-modal-overlay" :class="showEditor ? 'open' : ''" style="display:none" @click.self="showEditor = false">
        <div class="cm-modal" @click.stop>
            <div class="cm-modal-head">
                <div class="cm-modal-title" x-text="editingPost ? '{{ __("Edit Post") }}' : '{{ __("New Post") }}'"></div>
                <button class="cm-modal-close" @click="showEditor = false; editingPost = null; editorTitle = ''; editorContent = ''; editorTags = []; tagInputValue = ''">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="cm-modal-body">
                <input type="text" x-model="editorTitle" placeholder="{{ __('Title') }}" class="cm-form-input">
                <div id="cm-editor-create-wrap">
                    <textarea id="cm-editor-create" style="width:100%;min-height:200px"></textarea>
                </div>
                <input type="hidden" id="cm-editor-create-content" value="">
                <div style="margin-bottom:12px">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;padding:10px 12px;border-radius:12px;border:1px solid var(--border);background:var(--bg);min-height:42px;align-items:center;cursor:text" @click="$refs.tagInput.focus()">
                        <template x-for="(tag, i) in editorTags" :key="i">
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;background:var(--accent);color:white">
                                <span x-text="tag"></span>
                                <button @click="editorTags.splice(i, 1)" style="background:none;border:none;color:rgba(255,255,255,0.7);cursor:pointer;padding:0;font-size:10px;line-height:1">&times;</button>
                            </span>
                        </template>
                        <input x-ref="tagInput" type="text" x-model="tagInputValue" @keydown.enter.prevent="addTag()" @keydown.backspace="if(tagInputValue === '' && editorTags.length) editorTags.pop()" :placeholder="editorTags.length === 0 ? '{{ __('Tags (Enter to add, max 5)') }}' : ''" style="flex:1;min-width:120px;border:none;background:transparent;outline:none;font-size:13px;color:var(--text);padding:0">
                    </div>
                </div>
            </div>
            <div class="cm-modal-foot">
                <button @click="showEditor = false; editingPost = null; editorTitle = ''; editorContent = ''; editorTags = []; tagInputValue = ''" class="cm-btn-cancel">{{ __('Cancel') }}</button>
                <button @click="savePost()" class="cm-btn-submit">
                    <i class="fas fa-paper-plane mr-1"></i>{{ __('Publish') }}
                </button>
            </div>
        </div>
    </div>

    <div x-show="viewingPost" x-transition class="cm-modal-overlay" :class="viewingPost ? 'open' : ''" style="display:none" @click.self="viewingPost = null">
        <div class="cm-modal" style="max-width:640px" @click.stop>
            <template x-if="viewingPost">
                <div>
                    <div class="cm-modal-head">
                        <div style="display:flex;align-items:center;gap:10px">
                            <img :src="viewingPost.user?.avatar || 'https://ui-avatars.com/api/?name=U&background=6366f1&color=fff'" class="cm-comment-avatar">
                            <div>
                                <div class="cm-comment-name" x-text="viewingPost.user?.name"></div>
                                <div class="cm-post-time" x-text="timeAgo(viewingPost.created_at)"></div>
                            </div>
                        </div>
                        <div style="display:flex;gap:8px">
                            <template x-if="viewingPost.is_owner">
                                <div style="display:flex;gap:8px">
                                    <button @click="startEditPost()" class="cm-modal-close" title="{{ __('Edit') }}"><i class="fas fa-edit"></i></button>
                                    <button @click="deletePost()" class="cm-modal-close" title="{{ __('Delete') }}" style="color:#ef4444"><i class="fas fa-trash"></i></button>
                                </div>
                            </template>
                            <button @click="viewingPost = null" class="cm-modal-close"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div class="cm-modal-body">
                        <template x-if="!editingInModal">
                            <div>
                                <div class="cm-detail-title" x-text="viewingPost.title"></div>
                                <template x-if="viewingPost.tags && viewingPost.tags.length">
                                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px">
                                        <template x-for="tag in viewingPost.tags" :key="tag.slug">
                                            <a :href="`/community?tag=${tag.slug}`" class="cm-tag" style="display:inline-flex;align-items:center;gap:5px" x-text="tag.name"></a>
                                        </template>
                                    </div>
                                </template>
                                <div class="cm-detail-content" x-text="viewingPost.content"></div>
                            </div>
                        </template>
                        <template x-if="editingInModal">
                            <div style="margin-bottom:16px">
                                <input type="text" x-model="editTitle" class="cm-form-input">
                                <div id="cm-editor-edit-wrap">
                                    <textarea id="cm-editor-edit" style="width:100%;min-height:200px"></textarea>
                                </div>
                                <input type="hidden" id="cm-editor-edit-content" value="">
                                <div style="margin-bottom:12px">
                                    <div style="display:flex;flex-wrap:wrap;gap:8px;padding:10px 12px;border-radius:12px;border:1px solid var(--border);background:var(--bg);min-height:42px;align-items:center;cursor:text" @click="$refs.editTagInput.focus()">
                                        <template x-for="(tag, i) in editTags" :key="i">
                                            <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;background:var(--accent);color:white">
                                                <span x-text="tag"></span>
                                                <button @click="editTags.splice(i, 1)" style="background:none;border:none;color:rgba(255,255,255,0.7);cursor:pointer;padding:0;font-size:10px;line-height:1">&times;</button>
                                            </span>
                                        </template>
                                        <input x-ref="editTagInput" type="text" x-model="editTagInputValue" @keydown.enter.prevent="addEditTag()" @keydown.backspace="if(editTagInputValue === '' && editTags.length) editTags.pop()" :placeholder="editTags.length === 0 ? '{{ __('Tags (Enter to add, max 5)') }}' : ''" style="flex:1;min-width:120px;border:none;background:transparent;outline:none;font-size:13px;color:var(--text);padding:0">
                                    </div>
                                </div>
                                <div style="display:flex;gap:8px">
                                    <button @click="editingInModal = false" class="cm-btn-cancel">{{ __('Cancel') }}</button>
                                    <button @click="saveEditPost()" class="cm-btn-submit">{{ __('Save') }}</button>
                                </div>
                            </div>
                        </template>

                        <div style="display:flex;gap:16px;padding:16px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin-bottom:16px">
                            <button @click="toggleLike(viewingPost.id, $el)" class="cm-action-btn" :class="viewingPost.liked ? 'liked' : ''">
                                <i :class="viewingPost.liked ? 'fas fa-heart' : 'far fa-heart'"></i>
                                <span x-text="viewingPost.likes_count"></span>
                            </button>
                            <span class="cm-action-btn" style="cursor:default">
                                <i class="far fa-eye"></i>
                                <span x-text="viewingPost.views_count"></span>
                            </span>
                        </div>

                        <div style="margin-bottom:16px">
                            <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:12px">{{ __('Comments') }} (<span x-text="viewingPost.comments?.length || 0"></span>)</div>
                            <div style="max-height:300px;overflow-y:auto">
                                <template x-for="comment in viewingPost.comments" :key="comment.id">
                                    <div class="cm-comment">
                                        <img :src="comment.user?.avatar || 'https://ui-avatars.com/api/?name=U&background=6366f1&color=fff'" class="cm-comment-avatar">
                                        <div class="cm-comment-body">
                                            <div>
                                                <span class="cm-comment-name" x-text="comment.user?.name"></span>
                                                <span class="cm-comment-time" x-text="timeAgo(comment.created_at)"></span>
                                            </div>
                                            <div class="cm-comment-text" x-text="comment.content"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div style="display:flex;gap:10px">
                            <input type="text" x-model="newComment" @keydown.enter="submitComment(viewingPost.id)" placeholder="{{ __('Write a comment...') }}" class="cm-form-input" style="margin-bottom:0">
                            <button @click="submitComment(viewingPost.id)" class="cm-btn-submit" style="white-space:nowrap">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function communityApp() {
    return {
        showEditor: false,
        editingPost: null,
        editorTitle: '',
        editorContent: '',
        editorTags: [],
        tagInputValue: '',
        problemId: new URLSearchParams(window.location.search).get('problem') || null,
        viewingPost: null,
        newComment: '',
        editingInModal: false,
        editTitle: '',
        editContent: '',
        editTags: [],
        cmEditorCreate: null,
        cmEditorEdit: null,

        init() {
            if (this.problemId) {
                this.showEditor = true;
                this.$nextTick(() => this.initCreateEditor());
            }
            this.$watch('showEditor', (val) => {
                if (val) {
                    this.$nextTick(() => this.initCreateEditor());
                } else {
                    this.destroyCreateEditor();
                }
            });
            this.$watch('editingInModal', (val) => {
                if (val) {
                    this.$nextTick(() => this.initEditEditor());
                } else {
                    this.destroyEditEditor();
                }
            });
        },
        editTagInputValue: '',

        getCmSkin() {
            return !(document.documentElement.getAttribute('data-theme') || '').includes('light') ? 'oxide-dark' : 'oxide';
        },
        getCmContentCss() {
            return !(document.documentElement.getAttribute('data-theme') || '').includes('light') ? 'dark' : 'default';
        },
        getCmContentStyle() {
            const isDark = !(document.documentElement.getAttribute('data-theme') || '').includes('light');
            return 'body { font-family: Inter, sans-serif; font-size: 14px; color: ' + (isDark ? '#e2e8f0' : '#1e293b') + '; }';
        },

        initCreateEditor() {
            if (this.cmEditorCreate) return;
            const self = this;
            tinymce.init({
                selector: '#cm-editor-create',
                height: 300,
                skin: this.getCmSkin(),
                content_css: this.getCmContentCss(),
                menubar: false,
                plugins: 'lists link image code codesample fullscreen quickbars',
                toolbar: 'undo redo | blocks | bold italic strikethrough | link image codesample | bullist numlist | code fullscreen',
                codesample_languages: [
                    {text: 'HTML/XML', value: 'markup'}, {text: 'JavaScript', value: 'javascript'},
                    {text: 'TypeScript', value: 'typescript'}, {text: 'CSS', value: 'css'},
                    {text: 'PHP', value: 'php'}, {text: 'Python', value: 'python'},
                    {text: 'Java', value: 'java'}, {text: 'C', value: 'c'}, {text: 'C++', value: 'cpp'},
                ],
                content_style: this.getCmContentStyle(),
                setup: (editor) => { self.cmEditorCreate = editor; }
            });
        },

        destroyCreateEditor() {
            if (this.cmEditorCreate) {
                tinymce.remove('#cm-editor-create');
                this.cmEditorCreate = null;
            }
        },

        initEditEditor() {
            if (this.cmEditorEdit) return;
            const self = this;
            this.$nextTick(() => {
                const ta = document.getElementById('cm-editor-edit');
                if (!ta) return;
                tinymce.init({
                    selector: '#cm-editor-edit',
                    height: 300,
                    skin: self.getCmSkin(),
                    content_css: self.getCmContentCss(),
                    menubar: false,
                    plugins: 'lists link image code codesample fullscreen quickbars',
                    toolbar: 'undo redo | blocks | bold italic strikethrough | link image codesample | bullist numlist | code fullscreen',
                    codesample_languages: [
                        {text: 'HTML/XML', value: 'markup'}, {text: 'JavaScript', value: 'javascript'},
                        {text: 'TypeScript', value: 'typescript'}, {text: 'CSS', value: 'css'},
                        {text: 'PHP', value: 'php'}, {text: 'Python', value: 'python'},
                        {text: 'Java', value: 'java'}, {text: 'C', value: 'c'}, {text: 'C++', value: 'cpp'},
                    ],
                    content_style: self.getCmContentStyle(),
                    setup: (editor) => {
                        self.cmEditorEdit = editor;
                        editor.on('init', () => {
                            editor.setContent(self.editContent || '');
                        });
                    }
                });
            });
        },

        destroyEditEditor() {
            if (this.cmEditorEdit) {
                tinymce.remove('#cm-editor-edit');
                this.cmEditorEdit = null;
            }
        },

        addTag() {
            const val = this.tagInputValue.trim();
            if (val && this.editorTags.length < 5 && !this.editorTags.includes(val)) {
                this.editorTags.push(val);
            }
            this.tagInputValue = '';
        },

        addEditTag() {
            const val = this.editTagInputValue.trim();
            if (val && this.editTags.length < 5 && !this.editTags.includes(val)) {
                this.editTags.push(val);
            }
            this.editTagInputValue = '';
        },

        async openPost(postId) {
            try {
                const res = await fetch(`/community/${postId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.viewingPost = data.post;
                    this.editingInModal = false;
                }
            } catch (e) {
                console.error(e);
            }
        },

        async savePost() {
            if (!this.editorTitle.trim()) return;
            let content = '';
            if (this.cmEditorCreate) {
                content = this.cmEditorCreate.getContent();
            }
            if (!content.trim()) return;
            try {
                const url = this.editingPost ? `/community/${this.editingPost.id}` : '/community';
                const method = this.editingPost ? 'PUT' : 'POST';
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title: this.editorTitle, content: content, tags: this.editorTags, problem_id: this.problemId }),
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        },

        startEditPost() {
            this.editTitle = this.viewingPost.title;
            this.editContent = this.viewingPost.content;
            this.editTags = this.viewingPost.tags ? this.viewingPost.tags.map(t => t.name) : [];
            this.editTagInputValue = '';
            this.editingInModal = true;
        },

        async saveEditPost() {
            if (!this.editTitle.trim()) return;
            let content = '';
            if (this.cmEditorEdit) {
                content = this.cmEditorEdit.getContent();
            }
            if (!content.trim()) return;
            try {
                const res = await fetch(`/community/${this.viewingPost.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title: this.editTitle, content: content, tags: this.editTags }),
                });
                const data = await res.json();
                if (data.success) {
                    this.viewingPost.title = this.editTitle;
                    this.viewingPost.content = content;
                    this.viewingPost.tags = this.editTags.map(t => ({name: t, slug: t.toLowerCase().replace(/\s+/g, '-')}));
                    this.editingInModal = false;
                }
            } catch (e) {
                console.error(e);
            }
        },

        async deletePost() {
            if (!confirm('{{ __("Delete this post?") }}')) return;
            try {
                const res = await fetch(`/community/${this.viewingPost.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        },

        async toggleLike(postId, el) {
            try {
                const res = await fetch(`/community/${postId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    if (this.viewingPost && this.viewingPost.id === postId) {
                        this.viewingPost.liked = data.liked;
                        this.viewingPost.likes_count = data.likes;
                    }
                    const row = document.getElementById('post-' + postId);
                    if (row && row._x_dataStack) {
                        const scope = row._x_dataStack[0];
                        scope.liked = data.liked;
                        scope.likes = data.likes;
                    }
                }
            } catch (e) {
                console.error(e);
            }
        },

        async submitComment(postId) {
            if (!this.newComment.trim()) return;
            try {
                const res = await fetch('/community/comment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ post_id: postId, content: this.newComment }),
                });
                const data = await res.json();
                if (data.success) {
                    if (!this.viewingPost.comments) this.viewingPost.comments = [];
                    this.viewingPost.comments.push(data.comment);
                    this.newComment = '';
                    const count = data.comments_count || ((this.viewingPost.comments_count || 0) + 1);
                    this.viewingPost.comments_count = count;
                    const row = document.getElementById('post-' + postId);
                    if (row && row._x_dataStack) {
                        row._x_dataStack[0].commentsCount = count;
                    }
                }
            } catch (e) {
                console.error(e);
            }
        },

        timeAgo(date) {
            if (!date) return '';
            const seconds = Math.floor((new Date() - new Date(date)) / 1000);
            if (seconds < 60) return '{{ __("just now") }}';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return minutes + ' {{ __("min ago") }}';
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return hours + ' {{ __("h ago") }}';
            const days = Math.floor(hours / 24);
            return days + ' {{ __("d ago") }}';
        }
    };
}
</script>
@endsection
