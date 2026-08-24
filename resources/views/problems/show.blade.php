@extends('layouts.app')
@section('title', $problem->id . '. ' . $problem->title . ' - CodeMaster')

@section('head')
<style>
.prob-layout{display:flex;height:calc(100vh - 64px);overflow:hidden}
.prob-left{flex:1;display:flex;flex-direction:column;min-width:0;border-right:1px solid var(--border);overflow:hidden}
.prob-right{flex:1;display:flex;flex-direction:column;min-width:0;overflow:hidden}
.prob-desc{flex:1;overflow-y:auto;padding:1.5rem}
.prob-desc::-webkit-scrollbar{width:4px}
.prob-desc::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}
.prob-header{padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;flex-shrink:0}
.prob-title{font-size:16px;font-weight:800;color:var(--text)}
.prob-diff{font-size:10px;font-weight:800;padding:2px 8px;border-radius:5px;text-transform:uppercase}
.prob-meta{display:flex;gap:12px;font-size:11px;color:var(--text-muted);padding:8px 16px;border-bottom:1px solid var(--border);flex-wrap:wrap}
.prob-meta span{display:flex;align-items:center;gap:4px}
.prob-section{margin-bottom:1rem}
.prob-section h3{font-size:13px;font-weight:700;color:var(--text);margin-bottom:8px;display:flex;align-items:center;gap:6px}
.prob-section h3 i{color:var(--accent);font-size:11px}
.prob-text{font-size:13px;color:var(--text-secondary);line-height:1.7;white-space:pre-wrap}
.prob-example{background:var(--bg-2);border:1px solid var(--border);border-radius:8px;padding:12px;margin-bottom:12px}
.prob-example-label{font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin-bottom:6px}
.prob-example code{font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--success);display:block;white-space:pre}
.prob-tags{display:flex;flex-wrap:wrap;gap:4px;margin-top:8px}
.prob-tag{font-size:10px;font-weight:700;padding:2px 8px;border-radius:5px;background:var(--accent-glow);color:var(--accent-2)}
.editor-bar{display:flex;align-items:center;padding:6px 12px;background:var(--bg-elevated);border-bottom:1px solid var(--border);gap:8px;flex-shrink:0}
.editor-dots{display:flex;gap:4px}
.editor-dot{width:8px;height:8px;border-radius:50%}
.editor-dot:nth-child(1){background:#ef4444}
.editor-dot:nth-child(2){background:#eab308}
.editor-dot:nth-child(3){background:#22c55e}
.editor-lang{padding:4px 10px;border-radius:6px;border:1px solid var(--border);background:var(--bg);color:var(--text-secondary);font-size:11px;font-weight:600;outline:0}
#monaco-editor{flex:1;width:100%;min-height:200px}
.test-tabs{display:flex;border-bottom:1px solid var(--border);background:var(--card);flex-shrink:0}
.test-tab{padding:8px 16px;font-size:11px;font-weight:600;border:none;cursor:pointer;transition:.2s;color:var(--text-muted);background:0 0;border-bottom:2px solid transparent;display:flex;align-items:center;gap:5px}
.test-tab:hover{color:var(--text-secondary)}
.test-tab.on{color:var(--accent);border-bottom-color:var(--accent)}
.test-results{flex:1;overflow-y:auto;padding:12px}
.test-case{background:var(--bg-2);border:1px solid var(--border);border-radius:8px;padding:12px;margin-bottom:8px}
.test-case-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.test-case-label{font-size:11px;font-weight:700;color:var(--text-muted)}
.test-case-status{font-size:10px;font-weight:800;padding:2px 8px;border-radius:5px}
.test-case-status.pass{background:rgba(34,197,94,.1);color:var(--success)}
.test-case-status.fail{background:rgba(239,68,68,.1);color:var(--danger)}
.test-case-row{display:flex;gap:12px;margin-bottom:4px}
.test-case-col{flex:1}
.test-case-label2{font-size:9px;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin-bottom:2px}
.test-case-val{font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--text-secondary);white-space:pre-wrap}
.prob-actions{padding:10px 12px;border-top:1px solid var(--border);display:flex;gap:8px;flex-shrink:0;background:var(--card)}
.prob-btn{padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;border:0;cursor:pointer;transition:.2s;display:inline-flex;align-items:center;gap:6px}
.prob-btn--run{background:var(--bg-2);color:var(--text-secondary);border:1px solid var(--border)}
.prob-btn--run:hover{border-color:var(--accent);color:var(--accent)}
.prob-btn--submit{background:var(--gradient);color:#fff;box-shadow:0 2px 8px var(--accent-glow-strong)}
.prob-btn--submit:hover{transform:translateY(-1px)}
.prob-nav{display:flex;gap:4px;margin-left:auto}
.prob-nav-btn{width:32px;height:32px;border-radius:6px;border:1px solid var(--border);background:var(--bg-2);color:var(--text-muted);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:.2s;font-size:11px}
.prob-nav-btn:hover{border-color:var(--accent);color:var(--accent)}
.sub-table{width:100%;border-collapse:collapse;font-size:12px}
.sub-table th{padding:8px 12px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;color:var(--text-muted);border-bottom:2px solid var(--border)}
.sub-table td{padding:8px 12px;border-bottom:1px solid var(--border);color:var(--text-secondary)}
.sub-table tr:hover td{background:var(--accent-glow)}
.sub-status{font-size:10px;font-weight:800;padding:2px 8px;border-radius:5px;display:inline-block}
.sub-status.solved{background:rgba(34,197,94,.1);color:var(--success)}
.sub-status.attempted{background:rgba(239,68,68,.1);color:var(--danger)}
.sub-detail-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:200;display:flex;align-items:center;justify-content:center;padding:20px}
.sub-detail-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;width:100%;max-width:800px;max-height:80vh;overflow-y:auto;padding:24px}
.sub-detail-card pre{background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:12px;font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--text-secondary);overflow-x:auto;white-space:pre-wrap}

.sw-row{display:flex;align-items:center;gap:12px;padding:8px 16px;border-bottom:1px solid var(--border);background:var(--bg-2);flex-shrink:0}
.sw-timer{font-family:'JetBrains Mono',monospace;font-size:20px;font-weight:800;color:var(--text);letter-spacing:.05em;min-width:110px}
.sw-label{font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em}
.sw-sep{color:var(--text-muted);opacity:.3;font-weight:400}
.sw-btn{padding:5px 12px;border-radius:6px;font-size:11px;font-weight:700;border:1px solid var(--border);background:var(--bg);color:var(--text-secondary);cursor:pointer;transition:.15s;display:inline-flex;align-items:center;gap:5px}
.sw-btn:hover{border-color:var(--accent);color:var(--accent)}
.sw-btn.active{background:#22c55e;color:#fff;border-color:#22c55e}
.sw-btn.stop{background:#ef4444;color:#fff;border-color:#ef4444}

.collab-bar{display:flex;align-items:center;gap:8px;padding:8px 16px;border-bottom:1px solid var(--border);background:var(--bg-2);flex-shrink:0}
.collab-badge{font-size:10px;font-weight:700;padding:3px 8px;border-radius:5px;background:color-mix(in srgb, var(--accent) 15%, transparent);color:var(--accent)}
.collab-link-box{display:flex;align-items:center;gap:6px;background:var(--bg);border:1px solid var(--border);border-radius:6px;padding:4px 10px;flex:1;min-width:0}
.collab-link-box code{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--accent);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1}
.collab-copy-btn{padding:4px 8px;border-radius:4px;border:1px solid var(--border);background:var(--bg-2);color:var(--text-muted);font-size:10px;font-weight:700;cursor:pointer;transition:.15s;white-space:nowrap}
.collab-copy-btn:hover{border-color:var(--accent);color:var(--accent)}
.collab-avatars{display:flex;gap:-4px}
.collab-avatar{width:24px;height:24px;border-radius:50%;border:2px solid var(--bg-2);background:var(--accent);color:#fff;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;margin-left:-6px}
.collab-avatar:first-child{margin-left:0}
.collab-leave-btn{padding:4px 8px;border-radius:4px;border:1px solid #ef4444;color:#ef4444;background:transparent;font-size:10px;font-weight:700;cursor:pointer;transition:.15s}
.collab-leave-btn:hover{background:#ef4444;color:#fff}

.cm-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:300;display:flex;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(4px)}
.cm-card{background:var(--card);border:1px solid var(--border);border-radius:16px;width:100%;max-width:440px;padding:0;box-shadow:0 20px 60px rgba(0,0,0,.4)}
.cm-head{padding:20px 24px 0;display:flex;align-items:center;justify-content:space-between}
.cm-head h3{font-size:17px;font-weight:700;color:var(--text);margin:0}
.cm-close{width:32px;height:32px;border-radius:8px;border:none;background:var(--bg-3);color:var(--text-muted);font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.15s}
.cm-close:hover{background:var(--border);color:var(--text)}
.cm-body{padding:16px 24px 24px}
.cm-info{font-size:13px;color:var(--text-muted);line-height:1.6;margin-bottom:16px}
.cm-info strong{color:var(--text)}
.cm-link-box{display:flex;align-items:center;gap:8px;background:var(--bg-2);border:1px solid var(--border);border-radius:8px;padding:10px 14px;margin-bottom:16px}
.cm-link-box code{flex:1;font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--accent);word-break:break-all}
.cm-btn{padding:10px 20px;border-radius:8px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:.15s;display:inline-flex;align-items:center;gap:6px}
.cm-btn-primary{background:var(--accent);color:#fff}
.cm-btn-primary:hover{opacity:.9}
.cm-btn-ghost{background:transparent;color:var(--text-secondary);border:1px solid var(--border)}
.cm-btn-ghost:hover{border-color:var(--accent);color:var(--accent)}
@media(max-width:900px){.prob-layout{flex-direction:column}.prob-left,.prob-right{flex:none;height:50vh}.prob-left{border-right:0;border-bottom:1px solid var(--border)}}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/editor/editor.main.min.css">
@endsection

@section('content')
<div class="prob-layout" x-data="problemApp()">
    <div class="prob-left">
        <div class="prob-header">
            <a href="{{ route('problems.index') }}" class="prob-nav-btn" title="{{ __('ml_back') }}"><i class="fas fa-arrow-left"></i></a>
            <span class="prob-title">{{ $problem->id }}. {{ $problem->title }}</span>
            <span class="prob-diff" style="background:{{ $problem->difficulty_color }}15;color:{{ $problem->difficulty_color }}">{{ $problem->difficulty === 'easy' ? __('ml_easy') : ($problem->difficulty === 'medium' ? __('ml_med') : __('ml_hard')) }}</span>
            <div class="prob-nav">
                @if($problem->id > 1)
                <a href="{{ route('problems.show', \App\Models\Problem::where('id', $problem->id - 1)->first()->slug ?? $problem->slug) }}" class="prob-nav-btn"><i class="fas fa-chevron-left"></i></a>
                @endif
                @if(\App\Models\Problem::where('id', $problem->id + 1)->exists())
                <a href="{{ route('problems.show', \App\Models\Problem::where('id', $problem->id + 1)->first()->slug) }}" class="prob-nav-btn"><i class="fas fa-chevron-right"></i></a>
                @endif
            </div>
        </div>
        <div class="prob-meta">
            <span><i class="fas fa-check-circle" style="color:var(--success)"></i> {{ $problem->solved_count }} {{ __('Solved') }}</span>
            <span><i class="fas fa-users" style="color:var(--text-muted)"></i> {{ $problem->attempt_count }} {{ __('Submissions') }}</span>
            <span><i class="fas fa-percent" style="color:var(--text-muted)"></i> {{ $problem->acceptance_rate }}% {{ __('Acceptance') }}</span>
            <span><i class="fas fa-clock" style="color:var(--text-muted)"></i> {{ $problem->time_limit }}s</span>
        </div>
        <div class="sw-row" x-data="{ running: false, seconds: 0, interval: null }" x-init="
            saved = localStorage.getItem('sw_{{ $problem->id }}');
            if (saved) { seconds = parseInt(saved); }
        ">
            <div>
                <div class="sw-label">{{ __('ml_timing') }}</div>
                <div class="sw-timer">
                    <span x-text="String(Math.floor(seconds / 3600)).padStart(2, '0')"></span><span class="sw-sep">:</span><span x-text="String(Math.floor((seconds % 3600) / 60)).padStart(2, '0')"></span><span class="sw-sep">:</span><span x-text="String(seconds % 60).padStart(2, '0')"></span>
                </div>
            </div>
            <div style="display:flex;gap:6px">
                <template x-if="!running">
                    <button class="sw-btn" @click="
                        running = true;
                        interval = setInterval(() => {
                            seconds++;
                            localStorage.setItem('sw_{{ $problem->id }}', seconds);
                        }, 1000);
                    "><i class="fas fa-play"></i> {{ __('ml_start') }}</button>
                </template>
                <template x-if="running">
                    <button class="sw-btn stop" @click="
                        running = false;
                        clearInterval(interval);
                    "><i class="fas fa-pause"></i> {{ __('ml_stop') }}</button>
                </template>
                <button class="sw-btn" @click="
                    running = false;
                    clearInterval(interval);
                    seconds = 0;
                    localStorage.removeItem('sw_{{ $problem->id }}');
                "><i class="fas fa-redo"></i></button>
            </div>
            <div style="margin-left:auto">
                <template x-if="running">
                    <span style="font-size:10px;color:#22c55e;font-weight:700;display:flex;align-items:center;gap:4px">
                        <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;animation:pulse 1.5s infinite"></span> {{ __('ml_timing') }}
                    </span>
                </template>
            </div>
        </div>
        <div class="prob-desc">
            <div class="prob-section">
                <div class="prob-text">{!! nl2br(e($problem->description)) !!}</div>
            </div>
            @if($problem->constraints)
            <div class="prob-section">
                <h3><i class="fas fa-ruler-combined"></i> {{ __('Constraints') }}</h3>
                <div class="prob-text">{!! nl2br(e($problem->constraints)) !!}</div>
            </div>
            @endif
            @if($problem->input_example)
            <div class="prob-section">
                <h3><i class="fas fa-arrow-right"></i> {{ __('Example') }}</h3>
                <div class="prob-example">
                    <div class="prob-example-label">{{ __('Input') }}</div>
                    <code>{{ $problem->input_example }}</code>
                </div>
                <div class="prob-example">
                    <div class="prob-example-label">{{ __('Output') }}</div>
                    <code>{{ $problem->output_example }}</code>
                </div>
            </div>
            @endif
            @if($problem->topics->count())
            <div class="prob-section">
                <h3><i class="fas fa-tags"></i> {{ __('Topics') }}</h3>
                <div class="prob-tags">
                    @foreach($problem->topics as $topic)
                    <span class="prob-tag">{{ __('topic_' . $topic->slug) }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="prob-right">
        <div class="editor-bar">
            <div class="editor-dots"><div class="editor-dot"></div><div class="editor-dot"></div><div class="editor-dot"></div></div>
            <span style="font-size:11px;color:var(--text-muted);font-family:'JetBrains Mono',monospace">solution.<span id="ext-label">{{ ['python'=>'py','javascript'=>'js','typescript'=>'ts','java'=>'java','cpp'=>'cpp','c'=>'c','go'=>'go','rust'=>'rs'][$problem->language] ?? 'py' }}</span></span>
            <select class="editor-lang" id="lang-select" style="margin-left:auto">
                <option value="python" {{ $problem->language === 'python' ? 'selected' : '' }}>Python 3</option>
                <option value="javascript" {{ $problem->language === 'javascript' ? 'selected' : '' }}>JavaScript</option>
                <option value="typescript" {{ $problem->language === 'typescript' ? 'selected' : '' }}>TypeScript</option>
                <option value="java" {{ $problem->language === 'java' ? 'selected' : '' }}>Java</option>
                <option value="cpp" {{ $problem->language === 'cpp' ? 'selected' : '' }}>C++</option>
                <option value="c" {{ $problem->language === 'c' ? 'selected' : '' }}>C</option>
                <option value="go" {{ $problem->language === 'go' ? 'selected' : '' }}>Go</option>
                <option value="rust" {{ $problem->language === 'rust' ? 'selected' : '' }}>Rust</option>
            </select>
        </div>
        @auth
        <div class="collab-bar" x-data="collabApp()">
            <template x-if="!collabSession">
                <div style="display:flex;align-items:center;gap:8px;width:100%">
                    <span style="font-size:11px;color:var(--text-muted)"><i class="fas fa-users" style="margin-right:4px"></i> {{ __('ml_collaboration') }}</span>
                    <span class="collab-badge">{{ __('ml_beta') }}</span>
                    <button class="sw-btn" style="margin-left:auto" @click="createSession()"><i class="fas fa-link"></i> {{ __('ml_create_link') }}</button>
                </div>
            </template>
            <template x-if="collabSession">
                <div style="display:flex;align-items:center;gap:8px;width:100%">
                    <span style="font-size:11px;color:var(--text-muted)"><i class="fas fa-users" style="margin-right:4px"></i> {{ __('ml_live') }}</span>
                    <div class="collab-link-box">
                        <code x-text="collabSession.url"></code>
                        <button class="collab-copy-btn" @click="copyLink()"><i class="fas fa-copy"></i> {{ __('ml_copy') }}</button>
                    </div>
                    <div class="collab-avatars">
                        <template x-for="(p, i) in (collabParticipants || [])" :key="i">
                            <div class="collab-avatar" :title="p.name" x-text="p.name.charAt(0).toUpperCase()"></div>
                        </template>
                    </div>
                    <button class="collab-leave-btn" @click="leaveSession()"><i class="fas fa-times"></i></button>
                </div>
            </template>
        </div>
        @endauth
        <div id="monaco-editor"></div>
        <div class="prob-actions">
            <button class="prob-btn prob-btn--run" @click="runCode()" :disabled="loading">
                <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-play'"></i> {{ __('Run') }}
            </button>
            <button class="prob-btn prob-btn--submit" @click="submitCode()" :disabled="loading">
                <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i> {{ __('Submit') }}
            </button>
            @auth
            <button class="prob-btn" @click="showNoteModal = true" style="background:var(--bg-2);color:var(--text-secondary);border:1px solid var(--border)">
                <i class="fas fa-sticky-note"></i> {{ __('Save to Notebook') }}
            </button>
            @endauth
            @if($userProgress)
            <div class="ml-2 flex items-center gap-2">
                @if($userProgress->pivot->status === 'solved')
                <span class="text-xs font-bold" style="color:var(--success)"><i class="fas fa-check-circle mr-1"></i>{{ __('Solved') }}</span>
                @else
                <span class="text-xs font-bold" style="color:var(--warning)"><i class="fas fa-pen mr-1"></i>{{ __('Attempted') }}</span>
                @endif
                <span class="text-[10px]" style="color:var(--text-muted)">{{ $userProgress->pivot->attempts }} {{ __('attempts') }}</span>
            </div>
            @endif
        </div>
        <div class="test-tabs">
            <button class="test-tab" :class="activeTab === 'tests' ? 'on' : ''" @click="activeTab = 'tests'">
                <i class="fas fa-flask"></i> {{ __('Test Cases') }}
            </button>
            <button class="test-tab" :class="activeTab === 'results' ? 'on' : ''" @click="activeTab = 'results'">
                <i class="fas" :class="lastResult ? (lastResult.all_passed ? 'fa-check-circle' : 'fa-times-circle') : 'fa-terminal'"></i>
                {{ __('Results') }}
            </button>
        </div>
        <div class="test-results">
            {{-- TEST CASES TAB --}}
            <div x-show="activeTab === 'tests'">
                <template x-if="testCases.length > 0">
                    <div>
                        <template x-for="(tc, i) in testCases" :key="i">
                            <div class="test-case">
                                <div class="test-case-head">
                                    <span class="test-case-label" x-text="'{{ __("ml_case") }} ' + (i + 1)"></span>
                                </div>
                                <div class="test-case-row">
                                    <div class="test-case-col">
                                        <div class="test-case-label2">{{ __('Input') }}</div>
                                        <div class="test-case-val" x-text="tc.input"></div>
                                    </div>
                                    <div class="test-case-col">
                                        <div class="test-case-label2">{{ __('Expected') }}</div>
                                        <div class="test-case-val" x-text="tc.expected"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="testCases.length === 0">
                    <div style="text-align:center;color:var(--text-muted);font-size:13px;padding:32px 0">
                        <i class="fas fa-flask" style="font-size:24px;margin-bottom:8px;display:block"></i>
                        {{ __('No test cases available') }}
                    </div>
                </template>
            </div>

            {{-- RESULTS TAB --}}
            <div x-show="activeTab === 'results'">
                <template x-if="lastResult">
                    <div>
                        <div class="mb-3 p-3 rounded-lg" :class="lastResult.all_passed ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                            <div class="flex items-center gap-2">
                                <i class="fas" :class="lastResult.all_passed ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600'"></i>
                                <span class="font-bold text-sm" :class="lastResult.all_passed ? 'text-green-700' : 'text-red-700'"
                                      x-text="lastResult.all_passed ? '{{ __('All test cases passed!') }}' : '{{ __('Some test cases failed') }}'"></span>
                            </div>
                            <div class="text-xs mt-1" style="color:var(--text-muted)">
                                {{ __('Time') }}: <span x-text="lastResult.total_time_ms + 'ms'"></span> |
                                {{ __('Memory') }}: <span x-text="(lastResult.total_memory_kb / 1024).toFixed(1) + 'MB'"></span>
                            </div>
                        </div>
                        <template x-for="(r, i) in lastResult.results" :key="i">
                            <div class="test-case">
                                <div class="test-case-head">
                                    <span class="test-case-label" x-text="'{{ __("ml_test") }} ' + r.test"></span>
                                    <span class="test-case-status" :class="r.passed ? 'pass' : 'fail'" x-text="r.passed ? '✓ {{ __("ml_accepted") }}' : '✗ {{ __("ml_wrong_answer") }}'"></span>
                                </div>
                                <div class="test-case-row">
                                    <div class="test-case-col">
                                        <div class="test-case-label2">{{ __('Input') }}</div>
                                        <div class="test-case-val" x-text="r.input"></div>
                                    </div>
                                    <div class="test-case-col">
                                        <div class="test-case-label2">{{ __('Expected') }}</div>
                                        <div class="test-case-val" x-text="r.expected"></div>
                                    </div>
                                    <div class="test-case-col">
                                        <div class="test-case-label2">{{ __('Output') }}</div>
                                        <div class="test-case-val" :style="r.passed ? 'color:var(--success)' : 'color:var(--danger)'" x-text="r.output"></div>
                                    </div>
                                </div>
                                <div class="text-[10px] mt-1" style="color:var(--text-muted)">
                                    {{ __('Time') }}: <span x-text="r.time_ms + 'ms'"></span> |
                                    {{ __('Memory') }}: <span x-text="(r.memory_kb / 1024).toFixed(1) + 'MB'"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="!lastResult">
                    <div style="text-align:center;color:var(--text-muted);font-size:13px;padding:32px 0">
                        <i class="fas fa-terminal" style="font-size:24px;margin-bottom:8px;display:block"></i>
                        {{ __('Run your code to see results') }}
                    </div>
                </template>
            </div>
        </div>
    </div>

    <template x-if="detailSub">
        <div class="sub-detail-overlay" @click.self="detailSub=null">
            <div class="sub-detail-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 style="font-size:16px;font-weight:700;color:var(--text)" x-text="'{{ __("ml_submission") }} #' + detailSub.id"></h3>
                    <button @click="detailSub=null" class="prob-nav-btn"><i class="fas fa-times"></i></button>
                </div>
                <div class="flex items-center gap-4 mb-4 text-xs" style="color:var(--text-muted)">
                    <span class="sub-status" :class="detailSub.status" x-text="detailSub.status === 'solved' ? '{{ __("ml_accepted") }}' : '{{ __("ml_wrong_answer") }}'"></span>
                    <span x-text="detailSub.language"></span>
                    <span x-text="detailSub.runtime_ms + 'ms'"></span>
                    <span x-text="(detailSub.memory_kb / 1024).toFixed(1) + 'MB'"></span>
                </div>
                <pre x-text="detailSub.code"></pre>
            </div>
        </div>
    </template>

<div x-show="showNoteModal" x-transition style="position:fixed;inset:0;z-index:300;display:flex;align-items:center;justify-content:center;padding:20px" @click.self="showNoteModal = false">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,.6);backdrop-filter:blur(4px)" @click="showNoteModal = false"></div>
    <div style="position:relative;z-index:1;background:var(--card);border:1px solid var(--border);border-radius:16px;width:100%;max-width:560px;max-height:85vh;overflow-y:auto;padding:24px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <h3 style="font-size:16px;font-weight:700;color:var(--text)"><i class="fas fa-sticky-note" style="margin-right:6px;color:var(--accent)"></i>{{ __('Save to Notebook') }}</h3>
            <button @click="showNoteModal = false; destroyNoteEditor()" style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:16px"><i class="fas fa-times"></i></button>
        </div>
        <input type="text" x-model="noteTitle" placeholder="{{ __('Title (optional)') }}" style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:13px;margin-bottom:8px;box-sizing:border-box">
        <div id="note-problem-tinymce-wrap">
            <textarea id="note-problem-tinymce" style="width:100%;min-height:200px"></textarea>
        </div>
        <input type="hidden" id="note-problem-content-hidden" value="">
        <input type="text" x-model="noteTags" placeholder="{{ __('Tags (comma separated)') }}" style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:13px;margin:8px 0;box-sizing:border-box">
        <div style="display:flex;gap:8px;justify-content:flex-end">
            <button @click="showNoteModal = false; destroyNoteEditor()" style="padding:8px 16px;border-radius:8px;border:1px solid var(--border);background:transparent;color:var(--text-secondary);font-size:13px;cursor:pointer">{{ __('Cancel') }}</button>
            <button @click="saveNote()" :disabled="savingNote" style="padding:8px 16px;border-radius:8px;background:var(--accent);color:white;border:none;font-size:13px;font-weight:600;cursor:pointer">
                <i class="fas" :class="savingNote ? 'fa-spinner fa-spin' : 'fa-save'" style="margin-right:4px"></i>{{ __('Save') }}
            </button>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<style>
    @keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/loader.min.js"></script>
<script>
const LANG_MAP = {python:'python',javascript:'javascript',typescript:'typescript',java:'java',cpp:'cpp',c:'c',go:'go',rust:'rust'};
const EXT_MAP = {python:'py',javascript:'js',typescript:'ts',java:'java',cpp:'cpp',c:'c',go:'go',rust:'rs'};
const STARTER = @json($problem->starter_code);
let monacoEditor = null;

function getLang() {
    return document.getElementById('lang-select').value;
}

function problemApp() {
    return {
        loading: false,
        activeTab: 'tests',
        lastResult: null,
        testCases: @json($problem->tests_json ?? []),
        detailSub: null,
        showNoteModal: false,
        noteTitle: '',
        noteTags: '',
        savingNote: false,
        noteEditor: null,

        init() {
            const self = this;
            require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs' }});
            require(['vs/editor/editor.main'], () => {
                const isDark = !(document.documentElement.getAttribute('data-theme') || '').includes('light');
                monaco.editor.setTheme(isDark ? 'vs-dark' : 'vs');

                const container = document.getElementById('monaco-editor');
                monacoEditor = monaco.editor.create(container, {
                    value: STARTER,
                    language: LANG_MAP[getLang()] || 'python',
                    fontSize: 13,
                    fontFamily: "'JetBrains Mono', 'Fira Code', monospace",
                    minimap: { enabled: false },
                    scrollBeyondLastLine: false,
                    automaticLayout: false,
                    tabSize: 4,
                    wordWrap: 'on',
                    lineNumbers: 'on',
                    renderWhitespace: 'selection',
                    bracketPairColorization: { enabled: true },
                    cursorBlinking: 'smooth',
                    smoothScrolling: true,
                });

                window.addEventListener('resize', () => monacoEditor && monacoEditor.layout());

                monacoEditor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.Enter, () => self.runCode());

                document.getElementById('lang-select').addEventListener('change', function() {
                    document.getElementById('ext-label').textContent = EXT_MAP[this.value] || 'txt';
                    monaco.editor.setModelLanguage(monacoEditor.getModel(), LANG_MAP[this.value] || 'plaintext');
                });
            });

            this.$watch('showNoteModal', (val) => {
                if (val) {
                    this.$nextTick(() => this.initNoteEditor());
                } else {
                    this.destroyNoteEditor();
                }
            });
        },

        initNoteEditor() {
            if (this.noteEditor) return;
            const self = this;
            const isDark = !(document.documentElement.getAttribute('data-theme') || '').includes('light');
            tinymce.init({
                selector: '#note-problem-tinymce',
                height: 250,
                skin: isDark ? 'oxide-dark' : 'oxide',
                content_css: isDark ? 'dark' : 'default',
                menubar: false,
                plugins: 'lists link code codesample fullscreen quickbars',
                toolbar: 'undo redo | blocks | bold italic strikethrough | link codesample | bullist numlist | code fullscreen',
                codesample_languages: [
                    {text: 'HTML/XML', value: 'markup'}, {text: 'JavaScript', value: 'javascript'},
                    {text: 'TypeScript', value: 'typescript'}, {text: 'CSS', value: 'css'},
                    {text: 'PHP', value: 'php'}, {text: 'Python', value: 'python'},
                    {text: 'Java', value: 'java'}, {text: 'C', value: 'c'}, {text: 'C++', value: 'cpp'},
                ],
                content_style: 'body { font-family: Inter, sans-serif; font-size: 14px; color: ' + (isDark ? '#e2e8f0' : '#1e293b') + '; }',
                setup: (editor) => { self.noteEditor = editor; }
            });
        },

        destroyNoteEditor() {
            if (this.noteEditor) {
                tinymce.remove('#note-problem-tinymce');
                this.noteEditor = null;
            }
        },

        async saveNote() {
            let content = '';
            if (this.noteEditor) {
                content = this.noteEditor.getContent();
            }
            if (!content.trim()) return;
            this.savingNote = true;
            try {
                const res = await fetch('{{ route("profile.notebook.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        title: this.noteTitle || '{{ addslashes($problem->title) }}',
                        content: content,
                        problem_id: {{ $problem->id }},
                        tags: this.noteTags
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    this.showNoteModal = false;
                    this.noteTitle = '';
                    this.noteTags = '';
                    this.destroyNoteEditor();
                }
            } catch (e) {
                console.error(e);
            }
            this.savingNote = false;
        },

        async runCode() {
            this.loading = true;
            this.activeTab = 'results';
            const codeToSend = monacoEditor ? monacoEditor.getValue() : '';
            try {
                const res = await fetch('{{ route("problems.submit", $problem->slug) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ code: codeToSend, language: getLang() }),
                });
                this.lastResult = await res.json();
            } catch (e) {
                console.error(e);
            }
            this.loading = false;
        },

        showSubmission(sub) {
            this.detailSub = sub;
        }
    }
}

function collabApp() {
    return {
        collabSession: @json(session('collab_code') ? ['code' => session('collab_code'), 'url' => url('/collab/' . session('collab_code'))] : null),
        collabParticipants: @json(session('collab_participants', [])),
        loading: false,

        async createSession() {
            this.loading = true;
            try {
                const res = await fetch('{{ route("problems.collab.create", $problem->slug) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.url) {
                    this.collabSession = { code: data.code, url: data.url };
                    this.collabParticipants = this.collabParticipants.length ? this.collabParticipants : [
                        { name: '{{ Auth::user()->name }}', role: 'host' }
                    ];
                }
            } catch (e) {
                console.error(e);
            }
            this.loading = false;
        },

        copyLink() {
            if (this.collabSession) {
                navigator.clipboard.writeText(this.collabSession.url);
            }
        },

        async leaveSession() {
            if (!this.collabSession) return;
            try {
                await fetch(`/collab/${this.collabSession.code}/leave`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
            } catch (e) {}
            this.collabSession = null;
            this.collabParticipants = [];
        }
    }
}
</script>
@endpush
