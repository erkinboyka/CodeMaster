@extends('layouts.app')
@section('title', $problem->title . ' — ' . $contest->title . ' - CodeMaster')

@section('head')
<style>
    :root {
        --ed-bg: var(--bg);
        --ed-card: var(--card);
        --ed-border: var(--border);
        --ed-text: var(--text);
        --ed-muted: var(--text-muted);
        --ed-accent: var(--accent);
        --ed-surface: var(--bg-elevated, var(--bg-secondary));
    }
    * { box-sizing: border-box; }
    body { margin: 0; overflow: hidden; height: 100vh; }
    [x-cloak] { display: none !important; }

    .cp-layout { display: flex; height: 100vh; font-family: 'Inter', system-ui, sans-serif; background: var(--ed-bg); }

    /* Sidebar */
    .cp-sidebar { width: 260px; min-width: 260px; background: var(--ed-card); border-right: 1px solid var(--ed-border); display: flex; flex-direction: column; }
    .cp-sidebar-head { padding: 14px 16px; border-bottom: 1px solid var(--ed-border); }
    .cp-sidebar-back { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; color: var(--ed-muted); text-decoration: none; margin-bottom: 8px; transition: color .2s; text-transform: uppercase; letter-spacing: .5px; font-weight: 600; }
    .cp-sidebar-back:hover { color: var(--ed-accent); }
    .cp-sidebar-title { font-size: 13px; font-weight: 700; color: var(--ed-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cp-sidebar-meta { display: flex; gap: 6px; margin-top: 6px; }
    .cp-sidebar-tag { padding: 2px 7px; border-radius: 4px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
    .cp-problem-list { flex: 1; overflow-y: auto; padding: 6px; }
    .cp-problem-list::-webkit-scrollbar { width: 4px; }
    .cp-problem-list::-webkit-scrollbar-thumb { background: var(--ed-border); border-radius: 4px; }
    .cp-problem-item { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 6px; text-decoration: none; transition: all .15s; margin-bottom: 1px; }
    .cp-problem-item:hover { background: var(--ed-bg); }
    .cp-problem-item.active { background: var(--ed-surface); }
    .cp-pi-num { width: 26px; height: 26px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; font-family: 'JetBrains Mono', monospace; }
    .cp-pi-num.done { background: rgba(34,197,94,.12); color: #22c55e; }
    .cp-pi-num.wrong { background: rgba(239,68,68,.12); color: #ef4444; }
    .cp-pi-num.current { background: var(--ed-accent); color: var(--ed-bg); }
    .cp-pi-num.locked { background: var(--ed-surface); color: var(--ed-muted); }
    .cp-pi-info { flex: 1; min-width: 0; }
    .cp-pi-title { font-size: 12px; font-weight: 600; color: var(--ed-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cp-pi-diff { font-size: 10px; font-weight: 600; margin-top: 1px; }

    /* Main */
    .cp-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
    .cp-topbar { display: flex; align-items: center; justify-content: space-between; padding: 8px 16px; border-bottom: 1px solid var(--ed-border); background: var(--ed-card); flex-shrink: 0; gap: 12px; }
    .cp-topbar-left { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .cp-topbar-title { font-size: 13px; font-weight: 700; color: var(--ed-text); white-space: nowrap; }
    .cp-topbar-diff { padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
    .cp-topbar-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
    .cp-lang-select { padding: 5px 10px; border-radius: 6px; border: 1px solid var(--ed-border); background: var(--ed-bg); color: var(--ed-text); font-size: 12px; font-weight: 600; outline: none; cursor: pointer; font-family: 'JetBrains Mono', monospace; }
    .cp-lang-select:focus { border-color: var(--ed-accent); }
    .cp-submit-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 700; border: none; cursor: pointer; transition: all .15s; font-family: 'Inter', sans-serif; background: var(--ed-accent); color: var(--ed-bg); }
    .cp-submit-btn:hover:not(:disabled) { opacity: .9; }
    .cp-submit-btn:disabled { opacity: .5; cursor: not-allowed; }
    .cp-submit-btn:active:not(:disabled) { transform: scale(.97); }
    .cp-delete-btn { width: 30px; height: 30px; border-radius: 6px; border: 1px solid rgba(239,68,68,.2); background: rgba(239,68,68,.08); color: #ef4444; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; transition: all .2s; }
    .cp-delete-btn:hover { background: rgba(239,68,68,.2); }

    .cp-mobile-tabs { display: none; border-bottom: 1px solid var(--ed-border); background: var(--ed-card); }
    .cp-mobile-tabs button { flex: 1; padding: 10px; border: none; background: transparent; color: var(--ed-muted); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: all .2s; border-bottom: 2px solid transparent; font-family: inherit; }
    .cp-mobile-tabs button.active { color: var(--ed-accent); border-bottom-color: var(--ed-accent); }

    .cp-content { flex: 1; display: flex; overflow: hidden; }

    /* Description */
    .cp-desc-panel { flex: 1; overflow-y: auto; padding: 20px; border-right: 1px solid var(--ed-border); background: var(--ed-bg); }
    .cp-desc-panel::-webkit-scrollbar { width: 6px; }
    .cp-desc-panel::-webkit-scrollbar-thumb { background: var(--ed-border); border-radius: 4px; }
    .cp-desc-section { margin-bottom: 20px; }
    .cp-desc-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--ed-muted); margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
    .cp-desc-text { font-size: 13px; color: var(--ed-text); line-height: 1.8; }
    .cp-code-block { background: var(--ed-surface); border-radius: 8px; padding: 12px 14px; overflow-x: auto; margin-top: 6px; border: 1px solid var(--ed-border); }
    .cp-code-content { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #22c55e; white-space: pre; }
    .cp-constraints { font-size: 12px; color: var(--ed-muted); line-height: 1.6; }
    .cp-limits { display: flex; gap: 10px; margin-top: 6px; }
    .cp-limit-card { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 8px; background: var(--ed-surface); border: 1px solid var(--ed-border); flex: 1; }
    .cp-limit-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
    .cp-limit-icon.time { background: rgba(234,179,8,.12); color: #eab308; }
    .cp-limit-icon.mem { background: rgba(99,102,241,.12); color: #6366f1; }
    .cp-limit-info { display: flex; flex-direction: column; gap: 1px; }
    .cp-limit-val { font-size: 14px; font-weight: 700; color: var(--ed-text); font-family: 'JetBrains Mono', monospace; }
    .cp-limit-label { font-size: 10px; color: var(--ed-muted); text-transform: uppercase; letter-spacing: .5px; font-weight: 600; }

    /* Editor */
    .cp-editor-panel { width: 50%; min-width: 380px; display: flex; flex-direction: column; background: var(--ed-bg); }
    .cp-editor-titlebar { display: flex; align-items: center; background: var(--ed-card); border-bottom: 1px solid var(--ed-border); flex-shrink: 0; height: 36px; padding: 0 14px; gap: 8px; }
    .cp-editor-tab { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; color: var(--ed-muted); font-family: 'JetBrains Mono', monospace; }
    .cp-editor-tab-icon { width: 14px; height: 14px; border-radius: 3px; display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 900; }

    .cp-editor-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
    .cp-editor-area textarea {
        flex: 1;
        width: 100%;
        padding: 16px;
        border: none;
        background: var(--ed-bg);
        color: var(--ed-text);
        font-family: 'JetBrains Mono', monospace;
        font-size: 13px;
        line-height: 1.6;
        resize: none;
        outline: none;
        tab-size: 4;
    }
    .cp-editor-area textarea::placeholder { color: var(--ed-muted); }

    /* Status bar */
    .cp-statusbar { display: flex; align-items: center; justify-content: space-between; padding: 0 12px; height: 24px; background: var(--accent-2, var(--ed-accent)); color: rgba(255,255,255,.85); flex-shrink: 0; font-size: 11px; font-family: 'JetBrains Mono', monospace; }
    .cp-statusbar-left, .cp-statusbar-right { display: flex; align-items: center; gap: 12px; }
    .cp-statusbar-item { display: flex; align-items: center; gap: 4px; }
    .cp-statusbar-item i { font-size: 10px; }

    /* Results */
    .cp-results-panel { border-top: 1px solid var(--ed-border); background: var(--ed-card); flex-shrink: 0; max-height: 0; overflow: hidden; transition: max-height .4s cubic-bezier(.4,0,.2,1); }
    .cp-results-panel.open { max-height: 420px; overflow-y: auto; }
    .cp-results-panel::-webkit-scrollbar { width: 6px; }
    .cp-results-panel::-webkit-scrollbar-thumb { background: var(--ed-border); border-radius: 4px; }
    .cp-results-header { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; border-bottom: 1px solid var(--ed-border); position: sticky; top: 0; background: var(--ed-card); z-index: 2; }
    .cp-results-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; display: flex; align-items: center; gap: 8px; }
    .cp-results-title.pass { color: #22c55e; }
    .cp-results-title.fail { color: #ef4444; }
    .cp-results-title.running { color: #eab308; }
    .cp-results-close { width: 22px; height: 22px; border-radius: 4px; border: none; background: transparent; color: var(--ed-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 10px; }
    .cp-results-close:hover { background: var(--ed-surface); color: var(--ed-text); }
    .cp-results-body { padding: 10px 16px 16px; }
    .cp-result-summary { display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-radius: 8px; margin-bottom: 12px; font-size: 13px; font-weight: 600; }
    .cp-result-summary.pass { background: rgba(34,197,94,.08); border: 1px solid rgba(34,197,94,.2); color: #22c55e; }
    .cp-result-summary.fail { background: rgba(239,68,68,.08); border: 1px solid rgba(239,68,68,.2); color: #ef4444; }
    .cp-result-stats { display: flex; gap: 14px; margin-bottom: 12px; }
    .cp-result-stat { display: flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 6px; background: var(--ed-surface); border: 1px solid var(--ed-border); font-size: 11px; }
    .cp-result-stat i { font-size: 10px; }
    .cp-result-stat .val { font-weight: 700; color: var(--ed-text); font-family: 'JetBrains Mono', monospace; }
    .cp-result-stat .label { color: var(--ed-muted); }
    .cp-result-stat .label.ok { color: #22c55e; }
    .cp-result-stat .label.fail { color: #ef4444; }
    .cp-tests-list { display: flex; flex-direction: column; gap: 6px; }
    .cp-test-row { display: flex; align-items: stretch; border-radius: 8px; overflow: hidden; border: 1px solid var(--ed-border); background: var(--ed-surface); cursor: pointer; transition: all .15s; }
    .cp-test-row:hover { border-color: var(--ed-muted); }
    .cp-test-row.expanded { border-color: var(--ed-accent); }
    .cp-test-indicator { width: 4px; flex-shrink: 0; }
    .cp-test-indicator.pass { background: #22c55e; }
    .cp-test-indicator.fail { background: #ef4444; }
    .cp-test-head { flex: 1; display: flex; align-items: center; gap: 12px; padding: 8px 12px; min-width: 0; }
    .cp-test-num { font-size: 11px; font-weight: 700; font-family: 'JetBrains Mono', monospace; color: var(--ed-muted); width: 20px; text-align: center; flex-shrink: 0; }
    .cp-test-icon { width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; flex-shrink: 0; }
    .cp-test-icon.pass { background: rgba(34,197,94,.15); color: #22c55e; }
    .cp-test-icon.fail { background: rgba(239,68,68,.15); color: #ef4444; }
    .cp-test-name { font-size: 12px; color: var(--ed-text); font-weight: 600; flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cp-test-metrics { display: flex; gap: 10px; flex-shrink: 0; }
    .cp-test-metric { font-size: 10px; font-family: 'JetBrains Mono', monospace; color: var(--ed-muted); display: flex; align-items: center; gap: 3px; }
    .cp-test-metric i { font-size: 9px; }
    .cp-test-metric .val { font-weight: 600; color: var(--ed-text); }
    .cp-test-metric.warn { color: #eab308; }
    .cp-test-metric.danger { color: #ef4444; }
    .cp-test-arrow { color: var(--ed-muted); font-size: 10px; padding: 0 10px; display: flex; align-items: center; transition: transform .2s; flex-shrink: 0; }
    .cp-test-row.expanded .cp-test-arrow { transform: rotate(90deg); }
    .cp-test-detail { padding: 0 12px 10px 48px; display: none; }
    .cp-test-row.expanded .cp-test-detail { display: block; }
    .cp-test-detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .cp-test-detail-box { border-radius: 6px; overflow: hidden; }
    .cp-test-detail-box.full { grid-column: 1 / -1; }
    .cp-test-detail-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; padding: 4px 8px; display: flex; align-items: center; gap: 4px; }
    .cp-test-detail-label.input { background: rgba(99,102,241,.1); color: #6366f1; }
    .cp-test-detail-label.output { background: rgba(34,197,94,.1); color: #22c55e; }
    .cp-test-detail-label.expected { background: rgba(234,179,8,.1); color: #eab308; }
    .cp-test-detail-label.error { background: rgba(239,68,68,.1); color: #ef4444; }
    .cp-test-detail-content { font-family: 'JetBrains Mono', monospace; font-size: 11px; line-height: 1.5; padding: 6px 8px; background: var(--ed-bg); border: 1px solid var(--ed-border); border-top: none; color: var(--ed-text); white-space: pre-wrap; word-break: break-all; max-height: 80px; overflow-y: auto; }
    .cp-test-detail-content::-webkit-scrollbar { width: 3px; }
    .cp-test-detail-content::-webkit-scrollbar-thumb { background: var(--ed-border); border-radius: 3px; }
    .cp-test-detail-content.error-text { color: #ef4444; }

    /* Toast notification */
    .cp-toast {
        position: fixed;
        top: 16px;
        right: 16px;
        z-index: 999;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        transform: translateX(120%);
        transition: transform .3s cubic-bezier(.4,0,.2,1), opacity .3s;
        opacity: 0;
        pointer-events: none;
        box-shadow: 0 8px 24px rgba(0,0,0,.3);
        max-width: 360px;
    }
    .cp-toast.show { transform: translateX(0); opacity: 1; pointer-events: all; }
    .cp-toast.success { background: #22c55e; color: #fff; }
    .cp-toast.error { background: #ef4444; color: #fff; }
    .cp-toast.info { background: var(--ed-accent); color: var(--ed-bg); }
    .cp-toast-icon { font-size: 16px; flex-shrink: 0; }
    .cp-toast-close { margin-left: auto; background: none; border: none; color: inherit; opacity: .7; cursor: pointer; font-size: 14px; }
    .cp-toast-close:hover { opacity: 1; }

    /* Status banner */
    .cp-status-banner {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 700;
        transition: all .3s;
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        padding: 0 16px;
    }
    .cp-status-banner.visible { max-height: 50px; padding: 10px 16px; opacity: 1; }
    .cp-status-banner.pass { background: rgba(34,197,94,.1); border-bottom: 1px solid rgba(34,197,94,.2); color: #22c55e; }
    .cp-status-banner.fail { background: rgba(239,68,68,.1); border-bottom: 1px solid rgba(239,68,68,.2); color: #ef4444; }
    .cp-status-banner.running { background: rgba(234,179,8,.1); border-bottom: 1px solid rgba(234,179,8,.2); color: #eab308; }
    .cp-status-banner i { font-size: 15px; }

    @media (max-width: 1024px) {
        .cp-sidebar { display: none; }
        .cp-mobile-tabs { display: flex; }
        .cp-desc-panel { border-right: none; }
        .cp-editor-panel { width: 100%; min-width: 0; }
        .cp-content { flex-direction: column; }
        .cp-content[data-tab="desc"] .cp-editor-panel { display: none; }
        .cp-content[data-tab="code"] .cp-desc-panel { display: none; }
    }
</style>
@endsection

@section('content')
<div class="cp-layout" x-data="problemApp()" @keydown.window="if($event.ctrlKey && $event.key==='Enter'){$event.preventDefault();submitCode()}">
    <!-- Toast -->
    <div class="cp-toast" :class="toastType + (toastShow ? ' show' : '')">
        <i class="cp-toast-icon" :class="toastType==='success' ? 'fas fa-check-circle' : (toastType==='error' ? 'fas fa-times-circle' : 'fas fa-info-circle')"></i>
        <span x-text="toastMsg"></span>
        <button class="cp-toast-close" @click="toastShow=false"><i class="fas fa-times"></i></button>
    </div>
    <!-- Sidebar -->
    <aside class="cp-sidebar">
        <div class="cp-sidebar-head">
            <a href="{{ route('contests.show', $contest->id) }}" class="cp-sidebar-back"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>
            <div class="cp-sidebar-title">{{ $contest->title }}</div>
            <div class="cp-sidebar-meta">
                @php
                $diffMap = [
                    'easy'   => ['c' => '#22c55e', 'bg' => 'rgba(34,197,94,.12)'],
                    'medium' => ['c' => '#eab308', 'bg' => 'rgba(234,179,8,.12)'],
                    'hard'   => ['c' => '#ef4444', 'bg' => 'rgba(239,68,68,.12)'],
                ];
                $dc = $diffMap[$contest->difficulty] ?? $diffMap['easy'];
                @endphp
                <span class="cp-sidebar-tag" style="background:{{ $dc['bg'] }};color:{{ $dc['c'] }}">{{ __('difficulty_' . $contest->difficulty) }}</span>
                <span class="cp-sidebar-tag" style="background:var(--accent-glow);color:var(--accent)">{{ $problems->count() }}</span>
            </div>
        </div>
        <div class="cp-problem-list">
            @foreach($problems as $p)
            @php
            $isActive = $p->id === $problem->id;
            $isPassed = isset($userSubmissions[$p->id]) && $userSubmissions[$p->id]->status === 'accepted';
            $isWrong = isset($userSubmissions[$p->id]) && $userSubmissions[$p->id]->status !== 'accepted';
            $pd = $diffMap[$p->difficulty] ?? ['c' => '#6366f1', 'bg' => 'rgba(99,102,241,.12)'];
            @endphp
            <a href="{{ route('contests.problems.show', [$contest->id, $p->id]) }}" class="cp-problem-item {{ $isActive ? 'active' : '' }}">
                <div class="cp-pi-num {{ $isActive ? 'current' : ($isPassed ? 'done' : ($isWrong ? 'wrong' : 'locked')) }}">
                    @if($isPassed)<i class="fas fa-check"></i>@else{{ $p->order_num ?? $loop->iteration }}@endif
                </div>
                <div class="cp-pi-info">
                    <div class="cp-pi-title">{{ $p->title }}</div>
                    <div class="cp-pi-diff" style="color:{{ $pd['c'] }}">{{ $p->points }} {{ __('pts') }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </aside>

    <!-- Main -->
    <div class="cp-main">
        <div class="cp-topbar">
            <div class="cp-topbar-left">
                <span class="cp-topbar-title">{{ $problem->title }}</span>
                <span class="cp-topbar-diff" style="background:{{ $dc['bg'] }};color:{{ $dc['c'] }}">{{ __('difficulty_' . $problem->difficulty) }}</span>
                @if($userPassed)
                <span class="cp-topbar-diff" style="background:rgba(34,197,94,.12);color:#22c55e"><i class="fas fa-check mr-1"></i> {{ __('Solved') }}</span>
                @endif
            </div>
            <div class="cp-topbar-right">
                @auth
                @if(Auth::id() === $contest->created_by)
                <form action="{{ route('contests.problems.destroy', [$contest->id, $problem->id]) }}" method="POST" onsubmit="return confirm('Delete?')" style="margin:0">
                    @csrf @method('DELETE')
                    <button type="submit" class="cp-delete-btn"><i class="fas fa-trash"></i></button>
                </form>
                @endif
                @endauth
                <select class="cp-lang-select" x-model="language">
                    <option value="python">Python</option>
                    <option value="javascript">JavaScript</option>
                    <option value="php">PHP</option>
                    <option value="c">C</option>
                    <option value="cpp">C++</option>
                    <option value="java">Java</option>
                    <option value="ruby">Ruby</option>
                    <option value="go">Go</option>
                </select>
                <button class="cp-submit-btn" :class="running && 'loading'" @click="submitCode()" :disabled="running">
                    <template x-if="!running"><i class="fas fa-paper-plane"></i></template>
                    <template x-if="running"><i class="fas fa-spinner fa-spin"></i></template>
                    <span x-text="running ? '{{ __("Running...") }}' : '{{ __("Submit") }}'"></span>
                </button>
            </div>
        </div>

        <!-- Status banner -->
        <div class="cp-status-banner" :class="statusType + (statusText ? ' visible' : '')">
            <template x-if="statusType==='running'"><i class="fas fa-spinner fa-spin"></i></template>
            <template x-if="statusType==='pass'"><i class="fas fa-check-circle"></i></template>
            <template x-if="statusType==='fail'"><i class="fas fa-times-circle"></i></template>
            <span x-text="statusText"></span>
        </div>

        <div class="cp-mobile-tabs">
            <button :class="mobileTab === 'desc' && 'active'" @click="mobileTab = 'desc'">{{ __('Description') }}</button>
            <button :class="mobileTab === 'code' && 'active'" @click="mobileTab = 'code'">{{ __('Code') }}</button>
        </div>

        <div class="cp-content" :data-tab="mobileTab">
            <!-- Description -->
            <div class="cp-desc-panel">
                <div class="cp-desc-section">
                    <div class="cp-desc-label"><i class="fas fa-align-left"></i> {{ __('Description') }}</div>
                    <div class="cp-desc-text">{!! nl2br(e($problem->description)) !!}</div>
                </div>
                @if($problem->input_example)
                <div class="cp-desc-section">
                    <div class="cp-desc-label"><i class="fas fa-arrow-right-to-bracket"></i> {{ __('Input') }}</div>
                    <div class="cp-code-block"><div class="cp-code-content">{{ $problem->input_example }}</div></div>
                </div>
                @endif
                @if($problem->output_example)
                <div class="cp-desc-section">
                    <div class="cp-desc-label"><i class="fas fa-arrow-right-from-bracket"></i> {{ __('Output') }}</div>
                    <div class="cp-code-block"><div class="cp-code-content">{{ $problem->output_example }}</div></div>
                </div>
                @endif
                @if($problem->constraints)
                <div class="cp-desc-section">
                    <div class="cp-desc-label"><i class="fas fa-sliders"></i> {{ __('Constraints') }}</div>
                    <div class="cp-constraints">{{ $problem->constraints }}</div>
                </div>
                @endif
                <div class="cp-desc-section">
                    <div class="cp-desc-label"><i class="fas fa-gauge-high"></i> {{ __('Limits') }}</div>
                    <div class="cp-limits">
                        <div class="cp-limit-card">
                            <div class="cp-limit-icon time"><i class="fas fa-clock"></i></div>
                            <div class="cp-limit-info">
                                <div class="cp-limit-val">{{ $problem->time_limit ?? 2 }}s</div>
                                <div class="cp-limit-label">{{ __('Time Limit') }}</div>
                            </div>
                        </div>
                        <div class="cp-limit-card">
                            <div class="cp-limit-icon mem"><i class="fas fa-memory"></i></div>
                            <div class="cp-limit-info">
                                <div class="cp-limit-val">{{ $problem->memory_limit ?? 256 }} MB</div>
                                <div class="cp-limit-label">{{ __('Memory Limit') }}</div>
                            </div>
                        </div>
                        <div class="cp-limit-card">
                            <div class="cp-limit-icon" style="background:rgba(99,102,241,.12);color:#6366f1"><i class="fas fa-list-ol"></i></div>
                            <div class="cp-limit-info">
                                <div class="cp-limit-val">{{ $problem->points }} {{ __('pts') }}</div>
                                <div class="cp-limit-label">{{ __('Points') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Editor -->
            <div class="cp-editor-panel">
                <div class="cp-editor-titlebar">
                    <div class="cp-editor-tab">
                        @php $langColors = ['python'=>'#22c55e','javascript'=>'#eab308','php'=>'#8b5cf6','c'=>'#3b82f6','cpp'=>'#3b82f6','java'=>'#ef4444','ruby'=>'#ef4444','go'=>'#06b6d4']; @endphp
                        <span class="cp-editor-tab-icon" style="background:{{ $langColors[$problem->language] ?? '#666' }}20;color:{{ $langColors[$problem->language] ?? '#666' }}">
                            @switch($problem->language)
                                @case('python') PY @break
                                @case('javascript') JS @break
                                @case('php') PH @break
                                @case('c') C @break
                                @case('cpp') C+ @break
                                @case('java') JV @break
                                @case('ruby') RB @break
                                @case('go') GO @break
                                @default ?? @endswitch
                        </span>
                        <span x-text="'solution' + {python:'.py',javascript:'.js',php:'.php',c:'.c',cpp:'.cpp',java:'.java',ruby:'.rb',go:'.go'}[language]"></span>
                    </div>
                </div>

                <div class="cp-editor-area">
                    <textarea id="codeArea" placeholder="{{ __('Write your code here...') }}" spellcheck="false">{{ $problem->starter_code ?? '' }}</textarea>
                </div>

                <div class="cp-statusbar">
                    <div class="cp-statusbar-left">
                        <div class="cp-statusbar-item"><i class="fas fa-code-branch"></i> <span x-text="{python:'Python',javascript:'JavaScript',php:'PHP',c:'C',cpp:'C++',java:'Java',ruby:'Ruby',go:'Go'}[language]"></span></div>
                        <div class="cp-statusbar-item">UTF-8</div>
                    </div>
                    <div class="cp-statusbar-right">
                        <template x-if="!running && !result">
                            <div class="cp-statusbar-item"><i class="fas fa-keyboard"></i> Ctrl+Enter {{ __('to submit') }}</div>
                        </template>
                        <template x-if="running">
                            <div class="cp-statusbar-item" style="color:#fff"><i class="fas fa-spinner fa-spin"></i> {{ __('Processing...') }}</div>
                        </template>
                        <template x-if="!running && result">
                            <div class="cp-statusbar-item" :style="result?.result?.status==='accepted' ? 'color:#fff' : 'color:rgba(255,255,255,.9)'">
                                <i :class="result?.result?.status==='accepted' ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                                <span x-text="result?.result?.status==='accepted' ? '{{ __("Accepted") }}' : '{{ __("Failed") }}'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Results -->
                <div class="cp-results-panel" :class="showResults && 'open'">
                    <div class="cp-results-header">
                        <div class="cp-results-title" :class="running ? 'running' : (result?.result?.status === 'accepted' ? 'pass' : 'fail')">
                            <template x-if="running"><i class="fas fa-spinner fa-spin"></i></template>
                            <template x-if="!running && result?.result?.status === 'accepted'"><i class="fas fa-check-circle"></i></template>
                            <template x-if="!running && result?.result?.status !== 'accepted'"><i class="fas fa-times-circle"></i></template>
                            <span x-text="running ? '{{ __("Running...") }}' : (result?.result?.status === 'accepted' ? '{{ __("Accepted") }}' : (result?.result?.error || '{{ __("Wrong Answer") }}'))"></span>
                        </div>
                        <button class="cp-results-close" @click="showResults = false"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="cp-results-body">
                        <template x-if="result && result.result">
                            <div>
                                <div class="cp-result-summary" :class="result.result?.status === 'accepted' ? 'pass' : 'fail'">
                                    <i :class="result.result?.status === 'accepted' ? 'fas fa-check-circle' : 'fas fa-times-circle'" style="font-size:18px"></i>
                                    <div style="flex:1">
                                        <div x-text="result.result?.status === 'accepted' ? '{{ __("All tests passed!") }}' : (result.result?.error || '{{ __("Wrong answer") }}')"></div>
                                    </div>
                                    <template x-if="result.result?.passed_tests !== undefined">
                                        <span style="font-family:'JetBrains Mono',monospace;font-weight:700" x-text="result.result.passed_tests + '/' + result.result.total_tests"></span>
                                    </template>
                                </div>
                                <div class="cp-result-stats">
                                    <div class="cp-result-stat"><i class="fas fa-vial" style="color:var(--ed-accent)"></i><span class="val" x-text="(result.result?.total_tests || 0)"></span><span class="label">{{ __('tests') }}</span></div>
                                    <div class="cp-result-stat"><i class="fas fa-check" style="color:#22c55e"></i><span class="val" x-text="(result.result?.passed_tests || 0)"></span><span class="label ok">{{ __('passed') }}</span></div>
                                    <div class="cp-result-stat"><i class="fas fa-xmark" style="color:#ef4444"></i><span class="val" x-text="((result.result?.total_tests||0) - (result.result?.passed_tests||0))"></span><span class="label fail">{{ __('failed') }}</span></div>
                                    <template x-if="maxTime">
                                        <div class="cp-result-stat"><i class="fas fa-clock" style="color:#eab308"></i><span class="val" x-text="maxTime"></span><span class="label">{{ __('max time') }}</span></div>
                                    </template>
                                    <template x-if="maxMemory">
                                        <div class="cp-result-stat"><i class="fas fa-memory" style="color:#6366f1"></i><span class="val" x-text="maxMemory"></span><span class="label">{{ __('max mem') }}</span></div>
                                    </template>
                                </div>
                                <template x-if="result.result?.results && result.result.results.length > 0">
                                    <div class="cp-tests-list">
                                        <template x-for="(test, i) in result.result.results" :key="i">
                                            <div class="cp-test-row" :class="expandedTest === i && 'expanded'" @click="expandedTest = expandedTest === i ? null : i">
                                                <div class="cp-test-indicator" :class="test.passed ? 'pass' : 'fail'"></div>
                                                <div class="cp-test-head">
                                                    <div class="cp-test-num" x-text="i + 1"></div>
                                                    <div class="cp-test-icon" :class="test.passed ? 'pass' : 'fail'">
                                                        <i :class="test.passed ? 'fas fa-check' : 'fas fa-xmark'"></i>
                                                    </div>
                                                    <div class="cp-test-name" x-text="test.description || ('Test ' + (i+1))"></div>
                                                    <div class="cp-test-metrics">
                                                        <template x-if="test.time !== null && test.time !== undefined">
                                                            <div class="cp-test-metric" :class="parseFloat(test.time) > 1.5 ? 'warn' : ''"><i class="fas fa-clock"></i><span class="val" x-text="test.time + 's'"></span></div>
                                                        </template>
                                                        <template x-if="test.memory !== null && test.memory !== undefined">
                                                            <div class="cp-test-metric" :class="parseFloat(test.memory) > 128 ? 'danger' : ''"><i class="fas fa-memory"></i><span class="val" x-text="test.memory + 'MB'"></span></div>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="cp-test-arrow"><i class="fas fa-chevron-right"></i></div>
                                                <div class="cp-test-detail">
                                                    <div class="cp-test-detail-grid">
                                                        <template x-if="test.input">
                                                            <div class="cp-test-detail-box">
                                                                <div class="cp-test-detail-label input"><i class="fas fa-arrow-right-to-bracket"></i> {{ __('Input') }}</div>
                                                                <div class="cp-test-detail-content" x-text="test.input"></div>
                                                            </div>
                                                        </template>
                                                        <template x-if="test.expected">
                                                            <div class="cp-test-detail-box">
                                                                <div class="cp-test-detail-label expected"><i class="fas fa-bullseye"></i> {{ __('Expected') }}</div>
                                                                <div class="cp-test-detail-content" x-text="test.expected"></div>
                                                            </div>
                                                        </template>
                                                        <template x-if="test.output !== null && test.output !== undefined && test.output !== ''">
                                                            <div class="cp-test-detail-box">
                                                                <div class="cp-test-detail-label output"><i class="fas fa-arrow-right-from-bracket"></i> {{ __('Output') }}</div>
                                                                <div class="cp-test-detail-content" x-text="test.output"></div>
                                                            </div>
                                                        </template>
                                                        <template x-if="test.error">
                                                            <div class="cp-test-detail-box full">
                                                                <div class="cp-test-detail-label error"><i class="fas fa-triangle-exclamation"></i> {{ __('Error') }}</div>
                                                                <div class="cp-test-detail-content error-text" x-text="test.error"></div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="result.result?.error && !result.result?.results">
                                    <div class="cp-error-box" style="margin-top:10px;padding:12px;border-radius:8px;background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.15);font-family:'JetBrains Mono',monospace;font-size:12px;color:#ef4444;white-space:pre-wrap;word-break:break-all" x-text="result.result.error"></div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function problemApp() {
    return {
        language: '{{ $problem->language }}',
        mobileTab: 'desc',
        running: false,
        result: null,
        showResults: false,
        toastMsg: '',
        toastType: 'info',
        toastShow: false,
        statusText: '',
        statusType: '',
        expandedTest: null,

        get maxTime() {
            if (!this.result?.result?.results) return null;
            const times = this.result.result.results.map(r => parseFloat(r.time)).filter(t => !isNaN(t));
            return times.length ? Math.max(...times).toFixed(3) + 's' : null;
        },

        get maxMemory() {
            if (!this.result?.result?.results) return null;
            const mems = this.result.result.results.map(r => parseFloat(r.memory)).filter(m => !isNaN(m));
            return mems.length ? Math.max(...mems).toFixed(1) + 'MB' : null;
        },

        showToast(msg, type) {
            this.toastMsg = msg;
            this.toastType = type === 'pass' ? 'success' : (type === 'running' ? 'info' : 'error');
            this.toastShow = true;
            setTimeout(() => { this.toastShow = false; }, 4000);
        },

        submitCode() {
            if (this.running) return;
            this.running = true;
            this.statusText = '{{ __("Running...") }}';
            this.statusType = 'running';
            this.result = null;
            this.showResults = true;
            this.toastShow = false;
            this.expandedTest = null;

            const self = this;
            fetch('{{ route("contest.submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    contest_id: {{ $contest->id }},
                    problem_id: {{ $problem->id }},
                    code: document.getElementById('codeArea').value,
                    language: self.language
                })
            })
            .then(r => r.json())
            .then(d => {
                self.result = d;
                self.running = false;
                if (d && d.result) {
                    const isOk = d.result.status === 'accepted';
                    self.statusText = isOk ? '{{ __("Accepted — All tests passed!") }}' : (d.result.error || '{{ __("Wrong Answer") }}');
                    self.statusType = isOk ? 'pass' : 'fail';
                    self.showToast(self.statusText, isOk ? 'pass' : 'error');
                }
            })
            .catch(e => {
                self.result = { result: { status: 'error', error: e.message } };
                self.running = false;
                self.statusText = e.message;
                self.statusType = 'fail';
                self.showToast(e.message, 'error');
            });
        }
    };
}
</script>

@endsection
