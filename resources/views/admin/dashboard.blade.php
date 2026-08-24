@extends('layouts.admin')

@section('title', __('Admin Dashboard') . ' - CodeMaster')

@section('header-title', __('Admin Dashboard'))
@section('header-subtitle', __('Welcome back'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold mb-8" style="color:var(--text)">{{ __('Admin Dashboard') }}</h1>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="admin-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:color-mix(in srgb, var(--accent) 15%, transparent)">
                    <i class="fas fa-users" style="color:var(--accent)"></i>
                </div>
            </div>
            <p class="text-2xl font-bold" style="color:var(--text)">{{ $totalUsers }}</p>
            <p class="text-sm mt-1" style="color:var(--text-muted)">{{ __('Total Users') }}</p>
        </div>
        <div class="admin-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:color-mix(in srgb, var(--accent-2) 15%, transparent)">
                    <i class="fas fa-book" style="color:var(--accent-2)"></i>
                </div>
            </div>
            <p class="text-2xl font-bold" style="color:var(--text)">{{ $totalCourses }}</p>
            <p class="text-sm mt-1" style="color:var(--text-muted)">{{ __('Total Courses') }}</p>
        </div>
        <div class="admin-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:color-mix(in srgb, #22c55e 15%, transparent)">
                    <i class="fas fa-briefcase" style="color:#22c55e"></i>
                </div>
            </div>
            <p class="text-2xl font-bold" style="color:var(--text)">{{ $totalVacancies }}</p>
            <p class="text-sm mt-1" style="color:var(--text-muted)">{{ __('Total Vacancies') }}</p>
        </div>
        <div class="admin-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:color-mix(in srgb, #f59e0b 15%, transparent)">
                    <i class="fas fa-user-plus" style="color:#f59e0b"></i>
                </div>
            </div>
            <p class="text-2xl font-bold" style="color:var(--text)">{{ $newUsersToday }}</p>
            <p class="text-sm mt-1" style="color:var(--text-muted)">{{ __('New Today') }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="admin-card">
            <h3 class="font-bold mb-4" style="color:var(--text)">{{ __('Quick Actions') }}</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.users') }}" class="admin-btn admin-btn-ghost justify-center py-4">
                    <i class="fas fa-users" style="color:var(--accent)"></i>
                    <span style="color:var(--text)">{{ __('Manage Users') }}</span>
                </a>
                <a href="{{ route('admin.courses') }}" class="admin-btn admin-btn-ghost justify-center py-4">
                    <i class="fas fa-book" style="color:var(--accent-2)"></i>
                    <span style="color:var(--text)">{{ __('Manage Courses') }}</span>
                </a>
                <a href="{{ route('admin.lessons') }}" class="admin-btn admin-btn-ghost justify-center py-4">
                    <i class="fas fa-list" style="color:#22c55e"></i>
                    <span style="color:var(--text)">{{ __('Manage Lessons') }}</span>
                </a>
                <a href="{{ route('admin.vacancies') }}" class="admin-btn admin-btn-ghost justify-center py-4">
                    <i class="fas fa-briefcase" style="color:#f59e0b"></i>
                    <span style="color:var(--text)">{{ __('Manage Vacancies') }}</span>
                </a>
                <a href="{{ route('admin.contests') }}" class="admin-btn admin-btn-ghost justify-center py-4">
                    <i class="fas fa-trophy" style="color:#f97316"></i>
                    <span style="color:var(--text)">{{ __('Contests') }}</span>
                </a>
                <a href="{{ route('admin.interview-prep') }}" class="admin-btn admin-btn-ghost justify-center py-4">
                    <i class="fas fa-comments" style="color:#14b8a6"></i>
                    <span style="color:var(--text)">{{ __('Interview Prep') }}</span>
                </a>
                <a href="{{ route('admin.roadmap-list') }}" class="admin-btn admin-btn-ghost justify-center py-4">
                    <i class="fas fa-project-diagram" style="color:#3b82f6"></i>
                    <span style="color:var(--text)">{{ __('Roadmaps') }}</span>
                </a>
                <button onclick="if(confirm('{{ __('Install learning content?') }}')){fetch('{{ route('admin.seed-learning-pack') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}}).then(r=>r.json()).then(d=>alert(d.message)).catch(()=>alert('{{ __("ml_error_loading") }}'))}" class="admin-btn admin-btn-ghost justify-center py-4">
                    <i class="fas fa-magic" style="color:#ec4899"></i>
                    <span style="color:var(--text)">{{ __('Seed Content') }}</span>
                </button>
            </div>
        </div>

        <div class="admin-card">
            <h3 class="font-bold mb-4" style="color:var(--text)">{{ __('Platform Stats') }}</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm" style="color:var(--text-muted)">{{ __('Total Users') }}</span>
                    <span class="font-semibold" style="color:var(--text)">{{ $totalUsers }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm" style="color:var(--text-muted)">{{ __('Blocked Users') }}</span>
                    <span class="font-semibold" style="color:#ef4444">{{ $blockedUsers }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm" style="color:var(--text-muted)">{{ __('New Users Today') }}</span>
                    <span class="font-semibold" style="color:#22c55e">{{ $newUsersToday }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 rounded-2xl border p-6" style="background:var(--bg-secondary);border-color:var(--border)" x-data="firePreview()">
        <h3 class="font-bold mb-2" style="color:var(--text)"><i class="fas fa-fire mr-2" style="color:#f97316"></i>{{ __('ml_fire_streak_preview') }}</h3>
        <p class="text-sm mb-6" style="color:var(--text-muted)">{{ __('ml_set_streak_days') }}</p>

        <div class="flex items-center gap-4 mb-6">
            <label class="text-sm font-medium whitespace-nowrap" style="color:var(--text)">{{ __('ml_streak') }}</label>
            <input type="range" min="0" max="2600" x-model="days" class="flex-1 h-2 rounded-full appearance-none cursor-pointer" style="accent-color:#f97316">
            <input type="number" min="0" max="9999" x-model="days" class="w-20 px-3 py-2 rounded-lg border text-center font-mono font-bold text-lg focus:outline-none" style="background:var(--bg);color:var(--text);border-color:var(--border)">
        </div>

        <div class="mb-4 flex items-center gap-3">
            <span class="text-sm" style="color:var(--text-muted)">{{ __('ml_level') }}</span>
            <span class="px-3 py-1 rounded-full text-sm font-bold" :style="'background:'+levelCfg.bg+';color:'+levelCfg.color+';border:1px solid '+levelCfg.border" x-text="levelName"></span>
            <span class="text-sm" style="color:var(--text-muted)" x-text="days + ' {{ __('ml_days') }}'"></span>
        </div>

        <div class="rounded-xl p-6 mb-6 flex items-center justify-center" style="background:var(--bg);min-height:80px">
            <div class="nav-fire-badge" :class="'level-'+levelName" style="font-size:13px;padding:8px 16px 8px 12px">
                <i class="fas" :class="iconClass"></i>
                <span class="nav-fire-count" x-text="days"></span>
            </div>
        </div>

        <h4 class="text-sm font-bold mb-3 uppercase tracking-wider" style="color:var(--text-muted)">{{ __('ml_all_levels') }}</h4>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">
            <template x-for="l in allLevels" :key="l.name">
                <button @click="days=l.days" class="p-3 rounded-xl border transition-all text-center"
                    :class="levelName===l.name ? 'border-orange-500' : 'hover:border-gray-500'"
                    :style="levelName===l.name ? 'background:rgba(249,115,22,0.1);border-color:rgba(249,115,22,0.5)' : 'background:var(--bg);border-color:var(--border)'">
                    <div class="nav-fire-badge mb-2 mx-auto" :class="'level-'+l.name" style="font-size:11px;padding:4px 8px 4px 6px;display:inline-flex">
                        <i class="fas" :class="l.icon" style="font-size:12px"></i>
                        <span class="nav-fire-count" x-text="l.days"></span>
                    </div>
                    <div class="text-xs font-bold mt-1" :class="levelName===l.name ? 'text-orange-400' : ''" :style="levelName!==l.name ? 'color:var(--text-muted)' : ''" x-text="l.name"></div>
                    <div class="text-[10px]" style="color:var(--text-muted)" x-text="l.range"></div>
                </button>
            </template>
        </div>

        <div class="mt-6 flex items-center gap-4">
            <span class="text-sm" style="color:var(--text-muted)">{{ __('ml_click_badge') }}</span>
            <div class="nav-fire-badge cursor-pointer" :class="'level-'+levelName" style="font-size:13px;padding:8px 16px 8px 12px"
                @click="triggerFireBurst($el, parseInt(days), levelName)">
                <i class="fas" :class="iconClass"></i>
                <span class="nav-fire-count" x-text="days"></span>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/admin-fire-streak.css') }}">
<script src="{{ asset('js/admin-fire-streak.js') }}"></script>
@endsection
