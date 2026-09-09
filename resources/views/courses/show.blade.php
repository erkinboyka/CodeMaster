@extends('layouts.app')

@section('title', ($course->title ?? 'Course') . ' - CodeMaster')

@section('head')
<style>
    /* ============ COURSE DETAIL (cd-) — scoped, theme-aware ============ */
    .cd-page { background: var(--bg); color: var(--text); overflow-x: clip; }
    .cd-hero { position: relative; overflow: hidden; isolation: isolate; border-bottom: 1px solid var(--border); }
    .cd-hero-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .cd-hero-grid {
        position: absolute; inset: 0; opacity: .5;
        background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 44px 44px;
        mask-image: radial-gradient(ellipse 75% 90% at 20% 10%, black 20%, transparent 70%);
        -webkit-mask-image: radial-gradient(ellipse 75% 90% at 20% 10%, black 20%, transparent 70%);
    }
    .cd-orb { position: absolute; border-radius: 50%; filter: blur(90px); }
    .cd-orb-1 { width: 420px; height: 420px; background: var(--accent); opacity: .13; top: -160px; left: -100px; }
    .cd-orb-2 { width: 340px; height: 340px; background: #8b5cf6; opacity: .10; bottom: -180px; right: -60px; }
    .cd-hero-inner { position: relative; z-index: 1; max-width: 1280px; margin: 0 auto; padding: 36px clamp(16px,4vw,32px) 32px; }
    .cd-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--text-muted); margin-bottom: 18px; flex-wrap: wrap; }
    .cd-crumb a { color: var(--text-muted); text-decoration: none; transition: color .15s; }
    .cd-crumb a:hover { color: var(--accent); }
    .cd-crumb .sep { font-size: 9px; opacity: .6; }
    .cd-crumb .cur { color: var(--text); font-weight: 600; max-width: 420px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cd-badges { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
    .cd-chip {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 11.5px; font-weight: 700; letter-spacing: .02em;
        padding: 5px 12px; border-radius: 999px;
        border: 1px solid var(--border); background: var(--card); color: var(--text-secondary);
    }
    .cd-chip.accent { background: var(--accent-glow); border-color: var(--accent-glow-strong); color: var(--accent); }
    .cd-chip.violet { background: rgba(168,85,247,.12); border-color: rgba(168,85,247,.3); color: #c084fc; }
    .cd-chip.green { background: rgba(34,197,94,.12); border-color: rgba(34,197,94,.3); color: #22c55e; }
    .cd-title { font-size: clamp(26px,3.6vw,42px); font-weight: 900; letter-spacing: -.02em; line-height: 1.08; margin: 0 0 12px; max-width: 800px; }
    .cd-title .grad {
        background: linear-gradient(120deg, var(--accent), #8b5cf6 55%, #38bdf8);
        -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
    }
    .cd-desc { font-size: 15px; line-height: 1.7; color: var(--text-secondary); max-width: 760px; margin: 0 0 20px; }
    .cd-meta { display: flex; flex-wrap: wrap; gap: 10px 22px; margin-bottom: 22px; }
    .cd-meta-item { display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; color: var(--text-secondary); }
    .cd-meta-item i { color: var(--accent); font-size: 13px; width: 16px; text-align: center; }
    .cd-meta-item b { color: var(--text); }
    .cd-instructor-link { color: var(--text); font-weight: 600; text-decoration: underline; text-decoration-color: var(--accent-glow-strong); text-underline-offset: 3px; }
    .cd-instructor-link:hover { color: var(--accent); }
    .cd-hero-cta { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
    .cd-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 12px 22px; border-radius: 12px; font-size: 14px; font-weight: 700;
        text-decoration: none; border: 1px solid transparent; cursor: pointer;
        transition: transform .15s, box-shadow .15s, background .15s, border-color .15s;
    }
    .cd-btn:active { transform: scale(.97); }
    .cd-btn-primary { background: linear-gradient(135deg, var(--accent), var(--accent-hover, var(--accent-2))); color: #fff; box-shadow: 0 6px 22px var(--accent-glow-strong); }
    .cd-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px var(--accent-glow-strong); }
    .cd-btn-ghost { background: var(--card); border-color: var(--border); color: var(--text); }
    .cd-btn-ghost:hover { border-color: var(--accent); color: var(--accent); }
    .cd-btn-success { background: rgba(34,197,94,.14); border-color: rgba(34,197,94,.35); color: #22c55e; cursor: default; }
    .cd-btn-block { width: 100%; }

    .cd-body { max-width: 1280px; margin: 0 auto; padding: 28px clamp(16px,4vw,32px) 90px; display: grid; grid-template-columns: minmax(0,1fr) 360px; gap: 24px; align-items: start; }
    .cd-card { background: var(--card); border: 1px solid var(--border); border-radius: 18px; overflow: hidden; }
    .cd-card-head { padding: 20px 22px 0; }
    .cd-card-head-sticky {
        position: sticky; top: 0; z-index: 20;
        background: var(--card);
        padding-bottom: 14px; border-bottom: 1px solid var(--border);
    }
    /* Вся секция «Содержание курса» закреплена при скролле страницы,
       длинный список крутится внутри неё */
    #curriculum.cd-pin {
        position: sticky; top: 76px; z-index: 15;
        max-height: calc(100vh - 96px);
        overflow-x: hidden; overflow-y: auto;
        scrollbar-width: thin;
    }
    @@media (max-width: 1024px) {
        #curriculum.cd-pin { position: static; max-height: none; overflow: visible; }
    }
    .cd-card-title { font-size: 17px; font-weight: 800; margin: 0; }
    .cd-card-sub { font-size: 12.5px; color: var(--text-muted); margin: 5px 0 0; }
    .cd-card-tools { display: flex; gap: 8px; }
    .cd-tool { font-size: 12px; font-weight: 700; color: var(--accent); background: none; border: none; cursor: pointer; padding: 4px 6px; }
    .cd-tool:hover { text-decoration: underline; }

    .cd-mod { border-top: 1px solid var(--border); }
    .cd-mod:first-of-type { border-top: none; }
    .cd-mod-head {
        width: 100%; display: flex; align-items: center; gap: 14px;
        padding: 16px 22px; background: none; border: none; cursor: pointer; text-align: left;
        transition: background .15s;
    }
    .cd-mod-head:hover { background: var(--bg-secondary); }
    .cd-mod-num {
        width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 800;
        background: var(--accent-glow); color: var(--accent); border: 1px solid var(--accent-glow-strong);
    }
    .cd-mod-num.done { background: rgba(34,197,94,.14); color: #22c55e; border-color: rgba(34,197,94,.35); }
    .cd-mod-body { flex: 1; min-width: 0; }
    .cd-mod-name { display: block; font-size: 14.5px; font-weight: 700; color: var(--text); }
    .cd-mod-sub { display: block; font-size: 11.5px; color: var(--text-muted); margin-top: 3px; }
    .cd-mod-bar { width: 90px; height: 5px; border-radius: 99px; background: var(--bg-secondary); border: 1px solid var(--border); overflow: hidden; flex-shrink: 0; }
    .cd-mod-bar > span { display: block; height: 100%; background: linear-gradient(90deg, var(--accent), var(--accent-hover, var(--accent-2))); border-radius: 99px; }
    .cd-mod-chev { color: var(--text-muted); font-size: 12px; transition: transform .2s; flex-shrink: 0; }
    .cd-mod-chev.open { transform: rotate(90deg); color: var(--accent); }
    .cd-lessons { background: var(--bg-secondary); border-top: 1px dashed var(--border); }
    .cd-lesson {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 22px 11px 70px; text-decoration: none;
        border-top: 1px solid var(--border); transition: background .15s;
    }
    .cd-lesson:first-child { border-top: none; }
    .cd-lesson:hover { background: color-mix(in srgb, var(--accent) 6%, transparent); }
    .cd-lesson.current { background: var(--accent-glow); box-shadow: inset 3px 0 0 var(--accent); }
    .cd-check {
        width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
        border: 2px solid var(--border); display: flex; align-items: center; justify-content: center;
        font-size: 10px; color: transparent; background: var(--card);
    }
    .cd-check.done { background: rgba(34,197,94,.16); border-color: #22c55e; color: #22c55e; }
    .cd-check.num { font-size: 10.5px; font-weight: 700; color: var(--text-muted); border-style: dashed; }
    .cd-lesson-main { flex: 1; min-width: 0; }
    .cd-lesson-title { font-size: 13.5px; margin: 0; color: var(--text); font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cd-lesson.done .cd-lesson-title { color: var(--text-muted); font-weight: 500; }
    .cd-lesson-meta { display: flex; flex-wrap: wrap; gap: 4px 12px; margin-top: 3px; font-size: 11px; color: var(--text-muted); }
    .cd-lesson-meta i { margin-right: 3px; }
    .cd-diff { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; padding: 3px 8px; border-radius: 99px; flex-shrink: 0; }
    .cd-diff-easy { background: rgba(34,197,94,.12); color: #22c55e; }
    .cd-diff-medium { background: rgba(234,179,8,.14); color: #d9a406; }
    .cd-diff-hard { background: rgba(239,68,68,.12); color: #ef4444; }
    .cd-go { color: var(--text-muted); font-size: 12px; opacity: 0; transition: opacity .15s, transform .15s; }
    .cd-lesson:hover .cd-go { opacity: 1; color: var(--accent); transform: translateX(2px); }

    /* AI steps timeline */
    .cd-step { display: block; padding: 14px 22px; text-decoration: none; border-top: 1px solid var(--border); transition: background .15s; }
    .cd-step:first-of-type { border-top: none; }
    .cd-step:hover { background: var(--bg-secondary); }
    .cd-step-row { display: flex; align-items: center; gap: 14px; }
    .cd-step-children { margin: 4px 22px 10px 82px; border-left: 2px solid var(--border); border-radius: 2px; }
    .cd-child { display: flex; align-items: center; gap: 10px; padding: 9px 0 9px 18px; text-decoration: none; border-top: 1px dashed var(--border); transition: background .15s; }
    .cd-child:first-child { border-top: none; }
    .cd-child:hover .cd-child-title { color: var(--accent); }
    .cd-child-title { font-size: 13px; font-weight: 600; color: var(--text); margin: 0; }
    .cd-child.done .cd-child-title { color: var(--text-muted); font-weight: 500; }
    .cd-xp { font-size: 10.5px; font-weight: 800; color: var(--accent); background: var(--accent-glow); border: 1px solid var(--accent-glow-strong); padding: 2px 8px; border-radius: 99px; white-space: nowrap; }

    /* Sidebar */
    .cd-side { position: sticky; top: 88px; display: flex; flex-direction: column; gap: 16px; }
    .cd-cover { position: relative; height: 190px; background: linear-gradient(135deg, var(--accent), #8b5cf6 60%, #38bdf8); overflow: hidden; display: flex; align-items: center; justify-content: center; }
    .cd-cover img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1; }
    .cd-cover::after { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 30% 20%, rgba(255,255,255,.25), transparent 55%); }
    .cd-cover-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,.14) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.14) 1px, transparent 1px); background-size: 28px 28px; mask-image: radial-gradient(circle at 50% 50%, black, transparent 80%); }
    .cd-cover-icon { position: relative; z-index: 2; width: 60px; height: 60px; border-radius: 50%; background: rgba(255,255,255,.94); display: flex; align-items: center; justify-content: center; font-size: 22px; color: var(--accent); box-shadow: 0 10px 30px rgba(0,0,0,.3); text-decoration: none; transition: transform .2s; }
    .cd-cover-icon:hover { transform: scale(1.08); }
    .cd-side-body { padding: 20px 22px 22px; display: flex; flex-direction: column; gap: 16px; }
    .cd-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .cd-stat { background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 12px; padding: 10px 12px; }
    .cd-stat-v { font-size: 16px; font-weight: 800; }
    .cd-stat-l { font-size: 11px; color: var(--text-muted); margin-top: 1px; }
    .cd-progress-top { display: flex; align-items: center; justify-content: space-between; font-size: 12.5px; margin-bottom: 7px; }
    .cd-progress-top b { color: var(--accent); }
    .cd-progress { height: 8px; border-radius: 99px; background: var(--bg-secondary); border: 1px solid var(--border); overflow: hidden; }
    .cd-progress > span { display: block; height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--accent), #8b5cf6); transition: width .8s cubic-bezier(.4,0,.2,1); }
    .cd-includes { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 9px; font-size: 13px; color: var(--text-secondary); }
    .cd-includes i { color: var(--accent); width: 18px; text-align: center; margin-right: 8px; }
    .cd-teacher { display: flex; align-items: center; gap: 12px; text-decoration: none; }
    .cd-teacher img { width: 46px; height: 46px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); }
    .cd-teacher:hover img { border-color: var(--accent); }
    .cd-teacher-name { font-size: 14px; font-weight: 700; color: var(--text); margin: 0; }
    .cd-teacher:hover .cd-teacher-name { color: var(--accent); }
    .cd-teacher-sub { font-size: 11.5px; color: var(--text-muted); margin: 2px 0 0; }
    .cd-side-title { font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); margin: 0 0 10px; }
    .cd-mat { display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 700; color: var(--accent); text-decoration: none; }
    .cd-mat:hover { text-decoration: underline; }

    .cd-skills { display: flex; flex-wrap: wrap; gap: 8px; padding: 4px 22px 22px; }
    .cd-skill { font-size: 12.5px; font-weight: 600; padding: 7px 13px; border-radius: 10px; background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text-secondary); }
    .cd-skill i { color: #22c55e; margin-right: 6px; font-size: 11px; }

    .cd-mobilebar {
        display: none; position: fixed; left: 0; right: 0; bottom: 0; z-index: 60;
        padding: 10px 14px calc(10px + env(safe-area-inset-bottom));
        background: color-mix(in srgb, var(--card) 88%, transparent);
        backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
        border-top: 1px solid var(--border);
    }
    @@media (max-width: 1024px) {
        .cd-body { grid-template-columns: 1fr; }
        .cd-side { position: static; }
    }
    @@media (max-width: 768px) {
        .cd-mobilebar { display: block; }
        .cd-body { padding-bottom: 110px; }
        .cd-lesson { padding-left: 18px; }
        .cd-step-children { margin-left: 30px; }
        .cd-mod-bar { display: none; }
    }
    @@media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
    }
</style>
@endsection

@section('content')
@php
    // --- Нормализация: приоритет — данным контроллера, без лишних запросов из вида ---
    $isAi = $isAi ?? (bool) ($course->ai_generated ?? false);
    if (!$isAi && $course->relationLoaded('steps') && $course->steps->count()) $isAi = true;

    $uid = auth()->id();
    $isOwner = $isOwner ?? ($course->user_id === $uid);

    if ($isAi) {
        $doneSteps = $completedStepIds ?? [];
        $allSteps = $course->relationLoaded('steps') ? $course->steps->sortBy('sort_order') : collect();
        $parentSteps = $allSteps->whereNull('parent_id')->values();
        $childSteps = $allSteps->where('type', 'heir')->groupBy('parent_id');
        $totalSteps = $allSteps->count();
        $completedSteps = count(array_intersect($doneSteps, $allSteps->pluck('id')->toArray()));
        $percent = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;
        $nextStep = null;
        foreach ($allSteps as $s) { if (!in_array($s->id, $doneSteps)) { $nextStep = $s; break; } }
        $totalXp = $course->total_experience ?? $allSteps->sum('experience');
        $totalMinutes = null;
    } else {
        $completedLessonIds = $completedLessonIds ?? [];
        $lessons = $course->relationLoaded('lessons') ? $course->lessons->sortBy('order_num')->values() : collect();
        $modules = $modules ?? $lessons->groupBy('module');
        $totalLessons = $totalLessons ?? $lessons->count();
        $completedCount = count(array_intersect($completedLessonIds, $lessons->pluck('id')->toArray()));
        $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
        $totalMinutes = $lessons->sum('duration_minutes');
        $totalXp = $lessons->count() * 20;
    }

    $skills = $course->relationLoaded('courseSkills') ? $course->courseSkills : collect();
    $levelKey = 'courses_level_' . mb_strtolower($course->level ?? 'beginner');
    $coverImg = $course->image_url ?? $course->logo ?? null;
    $studentsCount = $course->students_count ?? null;
@endphp

<div class="cd-page">
    {{-- ================= HERO ================= --}}
    <section class="cd-hero">
        <div class="cd-hero-bg"><div class="cd-hero-grid"></div><div class="cd-orb cd-orb-1"></div><div class="cd-orb cd-orb-2"></div></div>
        <div class="cd-hero-inner">
            <nav class="cd-crumb" aria-label="breadcrumb">
                <a href="{{ route('courses.index') }}">{{ __('Courses') }}</a>
                <i class="fas fa-chevron-right sep"></i>
                @if($course->category)<span>{{ $course->category }}</span><i class="fas fa-chevron-right sep"></i>@endif
                <span class="cur">{{ $course->title }}</span>
            </nav>

            <div class="cd-badges">
                <span class="cd-chip accent"><i class="fas fa-layer-group"></i>{{ $course->category ?? __('Course') }}</span>
                <span class="cd-chip">{{ __($levelKey) }}</span>
                @if($isAi)<span class="cd-chip violet"><i class="fas fa-wand-magic-sparkles"></i>AI</span>@endif
                @if($percent >= 100)<span class="cd-chip green"><i class="fas fa-check"></i>{{ __('Completed') }}</span>@endif
            </div>

            <h1 class="cd-title">{{ $course->title }}</h1>
            @if($course->description)
                <p class="cd-desc">{{ $course->description }}</p>
            @endif

            <div class="cd-meta">
                @if($isAi)
                    <span class="cd-meta-item"><i class="fas fa-list-ol"></i><b>{{ $totalSteps }}</b>&nbsp;{{ __('steps') }}</span>
                @else
                    <span class="cd-meta-item"><i class="fas fa-play-circle"></i><b>{{ $totalLessons }}</b>&nbsp;{{ __('lessons') }}</span>
                    <span class="cd-meta-item"><i class="fas fa-cubes"></i><b>{{ $modules->count() }}</b>&nbsp;{{ __('modules') }}</span>
                    @if($totalMinutes)<span class="cd-meta-item"><i class="fas fa-clock"></i><b>{{ $totalMinutes }}</b>&nbsp;{{ __('min') }}</span>@endif
                @endif
                @if(!empty($totalXp))<span class="cd-meta-item"><i class="fas fa-bolt"></i><b>{{ $totalXp }}</b>&nbsp;XP</span>@endif
                @if($studentsCount)<span class="cd-meta-item"><i class="fas fa-users"></i><b>{{ $studentsCount }}</b></span>@endif
                <span class="cd-meta-item">
                    <i class="fas fa-user-tie"></i>
                    @if($instructorUser ?? null)
                        <a class="cd-instructor-link" href="{{ route('profile.show', $instructorUser->id) }}">{{ $course->instructor }}</a>
                    @else
                        {{ $course->instructor }}
                    @endif
                </span>
            </div>

            <div class="cd-hero-cta">
                @if($isAi)
                    @if(!$isOwner && empty($enrollment))
                        <form method="POST" action="{{ route('courses.subscribe', $course->id) }}">@csrf<button type="submit" class="cd-btn cd-btn-primary"><i class="fas fa-plus"></i>{{ __('Subscribe') }}</button></form>
                    @elseif($nextStep)
                        <a href="{{ route('courses.step', [$course->id, $nextStep->id]) }}" class="cd-btn cd-btn-primary"><i class="fas fa-play"></i>{{ $completedSteps > 0 ? __('Continue Learning') : __('Start Learning') }}</a>
                    @else
                        <span class="cd-btn cd-btn-success"><i class="fas fa-check"></i>{{ __('Completed') }}</span>
                    @endif
                @else
                    @if(!empty($certificate))
                        <a href="{{ route('certificate.show', $certificate->cert_hash) }}" class="cd-btn cd-btn-primary"><i class="fas fa-certificate"></i>{{ __('View Certificate') }}</a>
                    @elseif(!empty($exam) && $percent >= 80)
                        <a href="{{ route('courses.exam', $course->id) }}" class="cd-btn cd-btn-primary"><i class="fas fa-pen"></i>{{ __('Take Final Exam') }}</a>
                    @elseif($nextLesson)
                        <a href="{{ route('courses.lesson', [$course->id, $nextLesson->id]) }}" class="cd-btn cd-btn-primary"><i class="fas fa-play"></i>{{ $completedCount > 0 ? __('Continue Learning') : __('Start Learning') }}</a>
                    @endif
                @endif
                <a href="#curriculum" class="cd-btn cd-btn-ghost"><i class="fas fa-list"></i>{{ __('Course Content') }}</a>
            </div>
        </div>
    </section>

    {{-- ================= BODY ================= --}}
    <div class="cd-body">
        {{-- ---------- Main ---------- --}}
        <div id="curriculum" class="cd-card cd-pin" x-data="{ openModule: 'm0' }" style="scroll-margin-top:90px">
            <div class="cd-card-head cd-card-head-sticky" style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px">
                <div>
                    <h2 class="cd-card-title">{{ __('Course Content') }}</h2>
                    <p class="cd-card-sub">
                        @if($isAi)
                            {{ $totalSteps }} {{ __('steps') }} &bull; {{ $completedSteps }} {{ __('completed') }} &bull; {{ $percent }}%
                        @else
                            {{ $totalLessons }} {{ __('lessons') }} &bull; {{ $completedCount }} {{ __('completed') }} &bull; {{ $modules->count() }} {{ __('modules') }}
                        @endif
                    </p>
                </div>
                @if(!$isAi && $modules->count() > 1)
                <div class="cd-card-tools">
                    <button class="cd-tool" @click="openModule = 'all'">{{ __('Expand all') }}</button>
                </div>
                @endif
            </div>

            <div style="padding:14px 0 10px">
                @if($isAi)
                    @forelse($parentSteps as $step)
                        @php $stepDone = in_array($step->id, $doneSteps); $children = $childSteps[$step->id] ?? collect(); @endphp
                        <a href="{{ route('courses.step', [$course->id, $step->id]) }}" class="cd-step">
                            <div class="cd-step-row">
                                <span class="cd-check {{ $stepDone ? 'done' : 'num' }}">@if($stepDone)<i class="fas fa-check"></i>@else{{ $loop->iteration }}@endif</span>
                                <div class="cd-lesson-main">
                                    <p class="cd-lesson-title" style="{{ $stepDone ? 'color:var(--text-muted);font-weight:500' : '' }}">{{ $step->title }}</p>
                                    <div class="cd-lesson-meta">
                                        @if($step->experience)<span><i class="fas fa-bolt"></i>{{ $step->experience }} XP</span>@endif
                                        @if($step->relationLoaded('tests') && $step->tests->count())<span><i class="fas fa-question-circle"></i>{{ $step->tests->count() }} {{ __('tests') }}</span>@endif
                                        @if($step->relationLoaded('exams') && $step->exams->count())<span><i class="fas fa-pen"></i>{{ $step->exams->count() }} {{ __('exams') }}</span>@endif
                                        @if($step->relationLoaded('slides') && $step->slides->count())<span><i class="fas fa-images"></i>{{ $step->slides->count() }} {{ __('slides') }}</span>@endif
                                    </div>
                                </div>
                                @if($step->experience)<span class="cd-xp">{{ $step->experience }} XP</span>@endif
                                <i class="fas fa-arrow-right cd-go" style="opacity:1"></i>
                            </div>
                        </a>
                        @if($children->count())
                        <div class="cd-step-children">
                            @foreach($children->sortBy('sort_order') as $child)
                                @php $childDone = in_array($child->id, $doneSteps); @endphp
                                <a href="{{ route('courses.step', [$course->id, $child->id]) }}" class="cd-child {{ $childDone ? 'done' : '' }}">
                                    <span class="cd-check {{ $childDone ? 'done' : '' }}" style="width:18px;height:18px">@if($childDone)<i class="fas fa-check"></i>@endif</span>
                                    <p class="cd-child-title" style="flex:1">{{ $child->title }}</p>
                                    @if($child->experience)<span class="cd-xp">{{ $child->experience }} XP</span>@endif
                                </a>
                            @endforeach
                        </div>
                        @endif
                    @empty
                        <p style="padding:10px 22px;color:var(--text-muted);font-size:13.5px">{{ __('No content yet') }}</p>
                    @endforelse
                @else
                    @forelse($modules as $moduleName => $moduleLessons)
                    @php
                        $modLessons = $moduleLessons->sortBy('order_num')->values();
                        $moduleKey = 'm' . $loop->index;
                        $moduleTitle = $moduleName ?: __('General');
                        $modDone = $modLessons->filter(fn($l) => in_array($l->id, $completedLessonIds))->count();
                        $modTotal = $modLessons->count();
                        $modPct = $modTotal > 0 ? round(($modDone / $modTotal) * 100) : 0;
                        $modMinutes = $modLessons->sum('duration_minutes');
                        $isOpen = "openModule === '$moduleKey' || openModule === 'all'";
                    @endphp
                    <div class="cd-mod">
                        <button class="cd-mod-head" @click="openModule = (openModule === '{{ $moduleKey }}') ? '' : '{{ $moduleKey }}'" :aria-expanded="{{ $isOpen }} ? 'true' : 'false'">
                            <span class="cd-mod-num {{ $modPct === 100 ? 'done' : '' }}">@if($modPct === 100)<i class="fas fa-check"></i>@else{{ $loop->iteration }}@endif</span>
                            <span class="cd-mod-body">
                                <span class="cd-mod-name">{{ $moduleTitle }}</span>
                                <span class="cd-mod-sub">{{ $modDone }}/{{ $modTotal }} {{ __('lessons') }}@if($modMinutes) &bull; {{ $modMinutes }} {{ __('min') }}@endif &bull; {{ $modPct }}%</span>
                            </span>
                            <span class="cd-mod-bar"><span style="width:{{ $modPct }}%"></span></span>
                            <i class="fas fa-chevron-right cd-mod-chev" :class="{{ $isOpen }} ? 'open' : ''"></i>
                        </button>
                        <div class="cd-lessons" x-show="{{ $isOpen }}" x-cloak x-collapse>
                            @foreach($modLessons as $lesson)
                            @php
                                $done = in_array($lesson->id, $completedLessonIds);
                                $isNext = !$isAi && isset($nextLesson) && $nextLesson && $nextLesson->id === $lesson->id;
                                $diff = $lesson->difficulty ?? 'easy';
                            @endphp
                            <a href="{{ route('courses.lesson', [$course->id, $lesson->id]) }}" class="cd-lesson {{ $done ? 'done' : '' }} {{ $isNext ? 'current' : '' }}">
                                <span class="cd-check {{ $done ? 'done' : 'num' }}">@if($done)<i class="fas fa-check"></i>@else{{ $loop->iteration }}@endif</span>
                                <span class="cd-lesson-main">
                                    <p class="cd-lesson-title">{{ $lesson->title }}</p>
                                    <span class="cd-lesson-meta">
                                        @if($lesson->duration_minutes)<span><i class="fas fa-clock"></i>{{ $lesson->duration_minutes }} {{ __('min') }}</span>@endif
                                        @if($lesson->relationLoaded('practiceTasks') && $lesson->practiceTasks->count())<span><i class="fas fa-code"></i>{{ $lesson->practiceTasks->count() }}</span>@endif
                                        @if($lesson->relationLoaded('lessonQuizzes') && $lesson->lessonQuizzes->count())<span><i class="fas fa-question-circle"></i>{{ __('Quiz') }}</span>@endif
                                        @if($isNext)<span style="color:var(--accent);font-weight:700"><i class="fas fa-play"></i>{{ __('Continue') }}</span>@endif
                                    </span>
                                </span>
                                <span class="cd-diff cd-diff-{{ $diff }}">{{ __('difficulty_' . $diff) }}</span>
                                <i class="fas fa-play cd-go"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @empty
                        <p style="padding:10px 22px;color:var(--text-muted);font-size:13.5px">{{ __('No content yet') }}</p>
                    @endforelse

                    @if(!empty($exam))
                    <a href="{{ $percent >= 80 ? route('courses.exam', $course->id) : '#' }}" class="cd-step" @if($percent < 80) onclick="return false" style="opacity:.55;cursor:not-allowed" @endif>
                        <div class="cd-step-row">
                            <span class="cd-mod-num" style="border-radius:50%"><i class="fas fa-graduation-cap"></i></span>
                            <div class="cd-lesson-main">
                                <p class="cd-lesson-title">{{ __('Final Exam') }}</p>
                                <div class="cd-lesson-meta"><span>{{ $percent >= 80 ? __('Available') : __('Available at 80% progress') }} &bull; {{ $percent }}%</span></div>
                            </div>
                            <i class="fas fa-arrow-right cd-go" style="opacity:1"></i>
                        </div>
                    </a>
                    @endif
                @endif
            </div>

            @if($skills->count())
            <div class="cd-card-head" style="border-top:1px solid var(--border);padding-top:18px">
                <h3 class="cd-card-title" style="font-size:15px">{{ __('What you will learn') }}</h3>
            </div>
            <div class="cd-skills" style="padding-top:12px">
                @foreach($skills as $s)<span class="cd-skill"><i class="fas fa-check"></i>{{ $s->skill ?? $s->skill_name }}</span>@endforeach
            </div>
            @endif
        </div>

        {{-- ---------- Sidebar ---------- --}}
        <aside class="cd-side">
            <div class="cd-card">
                <div class="cd-cover">
                    <div class="cd-cover-grid"></div>
                    @if($coverImg)<img src="{{ $coverImg }}" alt="" loading="lazy" onerror="this.remove()">@endif
                    @php
                        $coverLink = $isAi
                            ? ($nextStep ? route('courses.step', [$course->id, $nextStep->id]) : null)
                            : ((isset($nextLesson) && $nextLesson) ? route('courses.lesson', [$course->id, $nextLesson->id]) : null);
                    @endphp
                    @if($coverLink)<a class="cd-cover-icon" href="{{ $coverLink }}" aria-label="{{ __('Start Learning') }}"><i class="fas fa-play" style="margin-left:3px"></i></a>@endif
                </div>
                <div class="cd-side-body">
                    <div>
                        <div class="cd-progress-top"><span style="color:var(--text-muted)">{{ __('Progress') }}</span><b>{{ $percent }}%</b></div>
                        <div class="cd-progress"><span style="width:{{ $percent }}%"></span></div>
                        <p style="font-size:11.5px;color:var(--text-muted);margin:7px 0 0">
                            @if($isAi){{ $completedSteps }}/{{ $totalSteps }} {{ __('steps') }}@else{{ $completedCount }}/{{ $totalLessons }} {{ __('lessons') }}@endif
                        </p>
                    </div>

                    @if($isAi)
                        @if(!$isOwner && empty($enrollment))
                            <form method="POST" action="{{ route('courses.subscribe', $course->id) }}">@csrf<button type="submit" class="cd-btn cd-btn-primary cd-btn-block"><i class="fas fa-plus"></i>{{ __('Subscribe') }}</button></form>
                        @elseif($nextStep)
                            <a href="{{ route('courses.step', [$course->id, $nextStep->id]) }}" class="cd-btn cd-btn-primary cd-btn-block"><i class="fas fa-play"></i>{{ __('Continue Learning') }}</a>
                        @else
                            <span class="cd-btn cd-btn-success cd-btn-block"><i class="fas fa-check"></i>{{ __('Completed') }}</span>
                        @endif
                    @else
                        @if(!empty($certificate))
                            <a href="{{ route('certificate.show', $certificate->cert_hash) }}" class="cd-btn cd-btn-primary cd-btn-block"><i class="fas fa-certificate"></i>{{ __('View Certificate') }}</a>
                        @elseif(!empty($exam) && $percent >= 80)
                            <a href="{{ route('courses.exam', $course->id) }}" class="cd-btn cd-btn-primary cd-btn-block"><i class="fas fa-pen"></i>{{ __('Take Final Exam') }}</a>
                        @elseif(isset($nextLesson) && $nextLesson)
                            <a href="{{ route('courses.lesson', [$course->id, $nextLesson->id]) }}" class="cd-btn cd-btn-primary cd-btn-block"><i class="fas fa-book-open"></i>{{ __('Continue Learning') }}</a>
                        @endif
                    @endif

                    <div class="cd-stats">
                        <div class="cd-stat"><div class="cd-stat-v">{{ $isAi ? $totalSteps : $totalLessons }}</div><div class="cd-stat-l">{{ $isAi ? __('Steps') : __('Lessons') }}</div></div>
                        <div class="cd-stat"><div class="cd-stat-v">{{ $isAi ? ($totalXp ?? '—') : ($totalMinutes ? $totalMinutes . '′' : $modules->count()) }}</div><div class="cd-stat-l">{{ $isAi ? 'XP' : ($totalMinutes ? __('min') : __('Modules')) }}</div></div>
                        <div class="cd-stat"><div class="cd-stat-v">{{ __($levelKey) }}</div><div class="cd-stat-l">{{ __('Level') }}</div></div>
                        <div class="cd-stat"><div class="cd-stat-v">{{ $studentsCount ?? '∞' }}</div><div class="cd-stat-l">{{ __('Students') }}</div></div>
                    </div>

                    <ul class="cd-includes">
                        <li><i class="fas fa-certificate"></i>{{ __('Certificate') }} — {{ __('Included') }}</li>
                        <li><i class="fas fa-infinity"></i>{{ __('Lifetime access') }}</li>
                        <li><i class="fas fa-mobile-alt"></i>{{ __('Mobile & desktop') }}</li>
                        @if($course->freetime)<li><i class="fas fa-clock"></i>{{ $course->freetime }} {{ __('hours per week') }}</li>@endif
                    </ul>
                </div>
            </div>

            <div class="cd-card" style="padding:20px 22px">
                <p class="cd-side-title">{{ __('Instructor') }}</p>
                @if($instructorUser ?? null)
                <a class="cd-teacher" href="{{ route('profile.show', $instructorUser->id) }}">
                    <img src="{{ $instructorUser->avatar_url }}" alt="{{ $course->instructor }}" loading="lazy">
                    <span><p class="cd-teacher-name">{{ $course->instructor }}</p><p class="cd-teacher-sub">{{ $course->category }}</p></span>
                </a>
                @else
                <div class="cd-teacher" style="cursor:default">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($course->instructor ?? 'C') }}&background=6366f1&color=fff" alt="{{ $course->instructor }}" loading="lazy">
                    <span><p class="cd-teacher-name">{{ $course->instructor }}</p><p class="cd-teacher-sub">{{ $course->category }}</p></span>
                </div>
                @endif
            </div>

            @if($course->materials_url)
            <div class="cd-card" style="padding:20px 22px">
                <p class="cd-side-title">{{ __('Materials') }}</p>
                <a class="cd-mat" href="{{ $course->materials_url }}" target="_blank" rel="noopener"><i class="fas fa-external-link-alt"></i>{{ $course->materials_title ?: __('View Materials') }}</a>
            </div>
            @endif
        </aside>
    </div>
</div>

{{-- Mobile sticky CTA --}}
<div class="cd-mobilebar">
    @if($isAi)
        @if(!$isOwner && empty($enrollment))
            <form method="POST" action="{{ route('courses.subscribe', $course->id) }}">@csrf<button type="submit" class="cd-btn cd-btn-primary cd-btn-block"><i class="fas fa-plus"></i>{{ __('Subscribe') }} &bull; {{ $percent }}%</button></form>
        @elseif($nextStep)
            <a href="{{ route('courses.step', [$course->id, $nextStep->id]) }}" class="cd-btn cd-btn-primary cd-btn-block"><i class="fas fa-play"></i>{{ __('Continue Learning') }} &bull; {{ $percent }}%</a>
        @endif
    @else
        @if(!empty($certificate))
            <a href="{{ route('certificate.show', $certificate->cert_hash) }}" class="cd-btn cd-btn-primary cd-btn-block"><i class="fas fa-certificate"></i>{{ __('View Certificate') }}</a>
        @elseif(!empty($exam) && $percent >= 80)
            <a href="{{ route('courses.exam', $course->id) }}" class="cd-btn cd-btn-primary cd-btn-block"><i class="fas fa-pen"></i>{{ __('Take Final Exam') }}</a>
        @elseif(isset($nextLesson) && $nextLesson)
            <a href="{{ route('courses.lesson', [$course->id, $nextLesson->id]) }}" class="cd-btn cd-btn-primary cd-btn-block"><i class="fas fa-play"></i>{{ __('Continue Learning') }} &bull; {{ $percent }}%</a>
        @endif
    @endif
</div>
@endsection
