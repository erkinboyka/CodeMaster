@extends('layouts.app')

@section('title', ($interview->title ?? __('interview_room_title')) . ' - CodeMaster')

@section('head')
<style>
html,body{background:var(--bg)!important;color-scheme:dark}
.ir{display:flex;height:calc(100vh - 64px);overflow:hidden}

.ir-main{flex:1;display:flex;flex-direction:column;background:var(--bg);min-width:0}

.ir-hdr{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;background:var(--card);border-bottom:1px solid var(--border)}
.ir-hdr-l{display:flex;align-items:center;gap:12px;min-width:0}
.ir-back{width:34px;height:34px;border-radius:10px;border:1px solid var(--border-hover);background:0 0;color:var(--text-muted);display:flex;align-items:center;justify-content:center;text-decoration:none;transition:.2s;flex-shrink:0}
.ir-back:hover{border-color:var(--accent);color:var(--accent-2)}
.ir-hdr-title{font-size:14px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ir-hdr-sub{font-size:11px;color:var(--text-muted);display:flex;align-items:center;gap:6px;margin-top:1px}
.ir-live{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:6px;background:rgba(34,197,94,.1);color:var(--success);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
.ir-live-dot{width:5px;height:5px;border-radius:50%;background:var(--success);animation:irP 1.4s ease-in-out infinite}
@keyframes irP{0%,100%{opacity:1}50%{opacity:.3}}
.ir-hdr-r{display:flex;align-items:center;gap:8px;flex-shrink:0}

.ir-tmr{display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:10px;background:var(--bg-elevated);border:1px solid var(--border)}
.ir-tmr i{font-size:12px;color:var(--text-muted)}
.ir-tmr-v{font-family:'Courier New',monospace;font-size:15px;font-weight:700;color:var(--text);letter-spacing:1px}

.ir-end{padding:6px 12px;border-radius:8px;font-size:11px;font-weight:700;border:1px solid rgba(239,68,68,.25);background:rgba(239,68,68,.06);color:var(--danger);cursor:pointer;transition:.2s}
.ir-end:hover{background:rgba(239,68,68,.12);border-color:rgba(239,68,68,.4)}

.ir-tabs{display:flex;gap:2px;padding:8px 16px 0;background:var(--bg)}
.ir-tab{padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;border:none;cursor:pointer;transition:.2s;color:var(--text-muted);background:0 0;display:inline-flex;align-items:center;gap:6px}
.ir-tab:hover{color:var(--text-secondary);background:rgba(255,255,255,.02)}
.ir-tab.on{background:var(--bg-elevated);color:var(--text)}

.ir-prog{display:flex;align-items:center;gap:0;padding:10px 16px 0}
.ir-prog-dot{width:10px;height:10px;border-radius:50%;background:var(--border);transition:.3s;cursor:pointer;flex-shrink:0}
.ir-prog-dot:hover{transform:scale(1.3)}
.ir-prog-dot.done{background:var(--success)}
.ir-prog-dot.cur{background:var(--accent);width:26px;border-radius:5px}
.ir-prog-line{width:16px;height:2px;background:var(--border);flex-shrink:0;transition:.3s}
.ir-prog-line.done{background:var(--success)}

.ir-body{flex:1;overflow-y:auto;padding:14px 16px}

.ir-q{padding:24px;border-radius:14px;background:var(--card);border:1px solid var(--border)}
.ir-q-tags{display:flex;gap:6px;margin-bottom:14px;flex-wrap:wrap}
.ir-tag{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:6px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.3px}
.ir-tag-e{background:rgba(34,197,94,.1);color:var(--success)}
.ir-tag-m{background:rgba(234,179,8,.1);color:var(--warning)}
.ir-tag-h{background:rgba(239,68,68,.1);color:var(--danger)}
.ir-tag-t{background:var(--accent-glow);color:var(--accent-2)}
.ir-tag-n{background:var(--bg-elevated);color:var(--text-muted);border:1px solid var(--border)}
.ir-q h3{font-size:18px;font-weight:800;color:var(--text);margin:0 0 12px;line-height:1.35}
.ir-q-desc{font-size:13px;color:var(--text-secondary);line-height:1.8;white-space:pre-wrap}
.ir-q-code{margin-top:14px;padding:14px 16px;border-radius:10px;background:var(--bg);border:1px solid var(--border);font-family:'Courier New',monospace;font-size:12px;line-height:1.7;color:var(--success);white-space:pre;overflow-x:auto}
.ir-q-ex{margin-top:12px;padding:12px 14px;border-radius:8px;background:var(--bg);border:1px solid var(--border)}
.ir-q-ex-l{font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
.ir-q-ex code{font-family:'Courier New',monospace;font-size:12px;color:var(--accent-2);white-space:pre-wrap}

.ir-opts{margin-top:16px;display:flex;flex-direction:column;gap:8px}
.ir-opt{display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:10px;border:1px solid var(--border);background:var(--bg-elevated);cursor:pointer;transition:.2s}
.ir-opt:hover{border-color:var(--accent);background:var(--accent-glow)}
.ir-opt.sel{border-color:var(--accent);background:var(--accent-glow)}
.ir-opt-ltr{width:26px;height:26px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--text-muted);background:var(--bg);border:1px solid var(--border);flex-shrink:0;transition:.2s}
.ir-opt.sel .ir-opt-ltr{background:var(--accent);color:#fff;border-color:var(--accent)}
.ir-opt-txt{font-size:13px;color:var(--text-secondary)}
.ir-opt.sel .ir-opt-txt{color:var(--text)}

.ir-ed{border-radius:12px;background:var(--card);border:1px solid var(--border);overflow:hidden;display:flex;flex-direction:column;height:100%}
.ir-ed-bar{display:flex;align-items:center;padding:8px 14px;background:var(--bg-elevated);border-bottom:1px solid var(--border);gap:8px;flex-shrink:0}
.ir-ed-dots{display:flex;gap:5px}
.ir-ed-d{width:9px;height:9px;border-radius:50%}
.ir-ed-d:nth-child(1){background:var(--danger)}.ir-ed-d:nth-child(2){background:var(--warning)}.ir-ed-d:nth-child(3){background:var(--success)}
.ir-ed-name{font-size:11px;color:var(--text-muted);font-family:'Courier New',monospace;margin-left:6px}
.ir-ed-lang{margin-left:auto;padding:4px 10px;border-radius:6px;border:1px solid var(--border);background:var(--bg);color:var(--text-secondary);font-size:11px;outline:0}
.ir-ed textarea{flex:1;width:100%;padding:14px;background:0 0;border:0;color:var(--success);font-family:'Courier New',monospace;font-size:12px;line-height:1.7;resize:none;outline:0}

.ir-q-editor{border-radius:12px;background:var(--card);border:1px solid var(--border);overflow:hidden;margin-top:14px;min-height:220px;display:flex;flex-direction:column}
.ir-q-editor-ta{flex:1;width:100%;padding:14px;background:0 0;border:0;color:var(--success);font-family:'Courier New',monospace;font-size:12px;line-height:1.7;resize:vertical;outline:0;min-height:180px}

.ir-ans{width:100%;padding:14px;border-radius:12px;border:1px solid var(--border);background:var(--card);color:var(--text);font-size:13px;outline:0;transition:border-color .3s;resize:none;min-height:200px;font-family:inherit;line-height:1.7}
.ir-ans:focus{border-color:var(--accent)}
.ir-ans::placeholder{color:var(--text-muted)}

.ir-acts{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;background:var(--bg);border-top:1px solid var(--border)}
.ir-acts-l{display:flex;gap:6px}
.ir-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;border:1px solid var(--border);background:var(--card);color:var(--text-secondary);cursor:pointer;transition:.2s}
.ir-btn:hover{border-color:var(--accent);color:var(--accent-2)}
.ir-btn.pri{background:var(--gradient);color:#fff;border-color:transparent;box-shadow:0 3px 12px var(--accent-glow-strong)}
.ir-btn.pri:hover{transform:translateY(-1px);box-shadow:0 5px 18px var(--accent-glow-strong)}
.ir-btn:disabled{opacity:.35;cursor:not-allowed;transform:none!important;box-shadow:none!important}

.ir-sb{width:340px;border-left:1px solid var(--border);background:var(--bg-2);display:flex;flex-direction:column;flex-shrink:0;transition:width .3s ease,opacity .3s}
.ir-sb.hide{width:0;overflow:hidden;border:0;opacity:0}
.ir-sb-hdr{padding:14px 16px;border-bottom:1px solid var(--border)}
.ir-sb-t{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:var(--text)}
.ir-sb-t i{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:var(--accent-glow);color:var(--accent-2);font-size:12px}
.ir-sb-btns{display:flex;gap:6px;margin-top:10px}
.ir-sb-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:5px;padding:8px;border-radius:8px;border:1px solid var(--border);background:var(--bg-elevated);color:var(--text-muted);font-size:11px;font-weight:600;cursor:pointer;transition:.2s;position:relative}
.ir-sb-btn:hover{border-color:var(--accent);color:var(--accent-2)}
.ir-sb-btn.on{border-color:var(--success);color:var(--success);background:rgba(34,197,94,.06)}
.ir-sb-btn.on::after{content:'';position:absolute;top:-2px;right:-2px;width:7px;height:7px;border-radius:50%;background:var(--success);border:2px solid var(--bg-2)}
.ir-sb-btn.off{border-color:rgba(239,68,68,.3);color:var(--danger);background:rgba(239,68,68,.05)}

.ir-ch{flex:1;overflow-y:auto;padding:12px 16px;display:flex;flex-direction:column;gap:12px}
.ir-ch::-webkit-scrollbar{width:3px}
.ir-ch::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
.ir-ch-s{font-size:10px;font-weight:600;color:var(--text-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px}
.ir-ch-b{padding:10px 14px;border-radius:10px;font-size:12px;line-height:1.6;color:var(--text-secondary);border:1px solid var(--border);background:var(--bg-elevated);white-space:pre-wrap}
.ir-ch-m.me .ir-ch-b{background:var(--accent-glow);border-color:rgba(99,102,241,.15);color:var(--accent-2)}
.ir-ch-in{padding:12px 16px;border-top:1px solid var(--border)}
.ir-ch-f{display:flex;gap:6px}
.ir-ch-fi{flex:1;padding:8px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-elevated);color:var(--text);font-size:12px;outline:0;transition:border-color .2s}
.ir-ch-fi:focus{border-color:var(--accent)}
.ir-ch-fi::placeholder{color:var(--text-muted)}
.ir-ch-sd{width:36px;height:36px;border-radius:8px;border:0;background:var(--gradient);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:.2s;box-shadow:0 2px 8px var(--accent-glow-strong);flex-shrink:0}
.ir-ch-sd:hover{transform:scale(1.06)}

.ir-toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%) translateY(80px);padding:10px 20px;border-radius:10px;background:var(--bg-elevated);border:1px solid var(--border-hover);color:var(--text);font-size:12px;font-weight:600;z-index:999;box-shadow:0 8px 28px rgba(0,0,0,.5);transition:transform .3s ease;display:flex;align-items:center;gap:8px;pointer-events:none}
.ir-toast.on{transform:translateX(-50%) translateY(0)}

.ir-q-pts{margin-top:14px;display:flex;gap:8px;flex-wrap:wrap}
.ir-q-pt{display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;background:rgba(34,197,94,.06);border:1px solid rgba(34,197,94,.15);font-size:11px;color:var(--success);font-weight:600}
.ir-q-pt i{font-size:10px}

.ir-cam{width:100%;aspect-ratio:16/9;border-radius:10px;background:var(--bg);border:1px solid var(--border);overflow:hidden;position:relative;display:none}
.ir-cam.show{display:block}
.ir-cam video{width:100%;height:100%;object-fit:cover;transform:scaleX(-1)}
.ir-cam-off{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:6px;color:var(--text-muted);font-size:11px}
.ir-cam-off i{font-size:24px}
.ir-audio-bar{height:3px;border-radius:2px;background:var(--border);overflow:hidden;margin-top:4px;width:100%}
.ir-audio-bar-fill{height:100%;background:var(--success);border-radius:2px;transition:width .1s;width:0%}
.ir-listening{display:flex;align-items:center;gap:6px;padding:6px 10px;border-radius:8px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);font-size:10px;font-weight:700;color:var(--danger);text-transform:uppercase;letter-spacing:.5px;margin-top:8px}
.ir-listening-dot{width:6px;height:6px;border-radius:50%;background:var(--danger);animation:blink .8s infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.ir-cam-label{position:absolute;bottom:6px;right:6px;padding:2px 8px;border-radius:4px;background:rgba(0,0,0,.6);color:#fff;font-size:9px;font-weight:600;backdrop-filter:blur(4px)}

@media(max-width:900px){
    .ir-sb{position:fixed;bottom:0;left:0;right:0;width:100%!important;height:55vh;z-index:50;border-left:0;border-top:1px solid var(--border);border-radius:16px 16px 0 0;transform:translateY(100%);transition:transform .3s ease}
    .ir-sb.mob-open{transform:translateY(0)}
}
</style>
@endsection

@section('content')
@php
    $qType = $question['type'] ?? 'open_ended';
    $difficulty = $interview->difficulty ?? 'medium';
    $diffClass = 'ir-tag-m';
    if ($difficulty === 'easy') $diffClass = 'ir-tag-e';
    elseif ($difficulty === 'hard') $diffClass = 'ir-tag-h';

    $diffIcon = 'fa-fire';
    if ($difficulty === 'easy') $diffIcon = 'fa-seedling';
    elseif ($difficulty === 'hard') $diffIcon = 'fa-skull';

    $typeIcon = 'fa-circle-question';
    $itype = $interview->type ?? 'technical';
    if ($itype === 'technical') $typeIcon = 'fa-microchip';
    elseif ($itype === 'behavioral') $typeIcon = 'fa-comments';
    elseif ($itype === 'coding') $typeIcon = 'fa-code';
    elseif ($itype === 'system_design') $typeIcon = 'fa-network-wired';

    $qText = $question['question'] ?? __('interview_question_default');
    if (is_string($qText)) {
        $decoded = json_decode($qText, true);
        if (!is_array($decoded) && preg_match('/\{[\s\S]*\}/', $qText, $m)) {
            $decoded = json_decode($m[0], true);
        }
        if (is_array($decoded) && isset($decoded['question'])) {
            $question = array_merge($question, $decoded);
            $qText = $decoded['question'];
            $qType = $decoded['type'] ?? $qType;
        } elseif (str_starts_with(trim($qText), '{')) {
            if (preg_match('/"question"\s*:\s*"(.*?)(?:"\s*[,}])/', $qText, $mq)) {
                $qText = stripcslashes(trim($mq[1]));
            } elseif (preg_match('/"question"\s*:\s*"(.+)/', $qText, $mq)) {
                $qText = stripcslashes(trim($mq[1]));
            }
            $raw = $question['question'] ?? $qText;
            if (preg_match('/"type"\s*:\s*"([^"]+)"/', $raw, $mt)) {
                $qType = $mt[1];
            }
            if (preg_match('/"options"\s*:\s*(\[[\s\S]*?\])/', $raw, $mo)) {
                $o = json_decode($mo[1], true);
                if (is_array($o)) {
                    $qOptions = $o;
                }
            }
            if (preg_match('/"correct_answer"\s*:\s*"([^"]*)"/', $raw, $mc)) {
                $question['correct_answer'] = $mc[1];
            }
        }
    }

    $qDesc = $question['description'] ?? '';
    $qExample = $question['example'] ?? '';
    $qStarter = $question['starter_code'] ?? '';
    $qLang = $question['language'] ?? 'python';
    $qOptions = $question['options'] ?? [];
    if (is_string($qOptions)) { $d = json_decode($qOptions, true); $qOptions = is_array($d) ? $d : []; }
    $qExpected = $question['expected_key_points'] ?? [];
    if (is_string($qExpected)) { $d = json_decode($qExpected, true); $qExpected = is_array($d) ? $d : []; }
@endphp

<div class="ir" x-data="irRoom()" x-init="init()">

    {{-- ========== MAIN ========== --}}
    <div class="ir-main">

        <div class="ir-hdr">
            <div class="ir-hdr-l">
                <a href="{{ route('interview.index') }}" class="ir-back"><i class="fas fa-arrow-left"></i></a>
                <div>
                    <div class="ir-hdr-title">{{ $interview->title }}</div>
                    <div class="ir-hdr-sub">
                        <span class="ir-live"><span class="ir-live-dot"></span> {{ __('interview_live') }}</span>
                        <span x-text="(qi+1) + '/5'"></span>
                    </div>
                </div>
            </div>
            <div class="ir-hdr-r">
                <div class="ir-tmr"><i class="fas fa-clock"></i><span class="ir-tmr-v" x-text="fmt(timer)"></span></div>
                <button class="ir-end" @click="sbHide = !sbHide; mobChat = !sbHide ? false : mobChat" :title="sbHide ? '{{ __('interview_show_chat') }}' : '{{ __('interview_hide_chat') }}'">
                    <i class="fas" :class="sbHide ? 'fa-angles-left' : 'fa-angles-right'"></i>
                </button>
                <button class="ir-end" @click="end()"><i class="fas fa-stop"></i> {{ __('interview_finish') }}</button>
            </div>
        </div>

        <div class="ir-tabs">
            <button class="ir-tab" :class="tab==='q' && 'on'" @click="tab='q'"><i class="fas fa-circle-question"></i> {{ __('interview_tab_question') }}</button>
            <button class="ir-tab" :class="tab==='c' && 'on'" @click="tab='c'"><i class="fas fa-code"></i> {{ __('interview_tab_code') }}</button>
            <button class="ir-tab" :class="tab==='a' && 'on'" @click="tab='a'"><i class="fas fa-pen"></i> {{ __('interview_tab_answer') }}</button>
        </div>

        <div class="ir-prog">
            @for($i = 0; $i < 5; $i++)
                <div class="ir-prog-dot" :class="{{ $i }} < qi ? 'done' : ({{ $i }} === qi ? 'cur' : '')" @click="goTo({{ $i }})"></div>
                @if($i < 4)<div class="ir-prog-line" :class="{{ $i }} < qi ? 'done' : ''"></div>@endif
            @endfor
        </div>

        <div class="ir-body">

            {{-- QUESTION --}}
            <div x-show="tab==='q'" x-cloak class="ir-q">
                <div class="ir-q-tags">
                    <span class="ir-tag {{ $diffClass }}"><i class="fas {{ $diffIcon }}"></i> {{ match($interview->difficulty) { 'easy' => __('interview_diff_easy'), 'medium' => __('interview_diff_medium'), 'hard' => __('interview_diff_hard'), default => ucfirst($interview->difficulty) } }}</span>
                    <span class="ir-tag ir-tag-t"><i class="fas {{ $typeIcon }}"></i> {{ match($interview->type) { 'technical' => __('interview_type_technical'), 'behavioral' => __('interview_type_behavioral'), 'coding' => __('interview_type_coding'), 'system_design' => __('interview_type_system_design'), default => ucfirst(str_replace('_', ' ', $interview->type)) } }}</span>
                    <span class="ir-tag ir-tag-n"><i class="fas fa-hashtag"></i> #{{ $questionIndex + 1 }}</span>
                    <span class="ir-tag ir-tag-n"><i class="fas fa-list"></i> {{ match(str_replace('_', ' ', $qType)) { 'multiple choice' => __('interview_qtype_multiple_choice'), 'code writing' => __('interview_qtype_code_writing'), 'open ended' => __('interview_qtype_open_ended'), default => ucfirst(str_replace('_', ' ', $qType)) } }}</span>
                </div>
                <h3>{{ $qText }}</h3>
                @if($qDesc)<div class="ir-q-desc">{!! nl2br(e($qDesc)) !!}</div>@endif
                @if($qType === 'code_writing')
                <div class="ir-q-editor">
                    <div class="ir-ed-bar">
                        <div class="ir-ed-dots"><div class="ir-ed-d"></div><div class="ir-ed-d"></div><div class="ir-ed-d"></div></div>
                        <span class="ir-ed-name" x-text="'solution' + (lang==='javascript'?'.js':lang==='java'?'.java':lang==='cpp'?'.cpp':'.py')"></span>
                        <select class="ir-ed-lang" x-model="lang">
                            <option value="python">Python</option>
                            <option value="javascript">JavaScript</option>
                            <option value="java">Java</option>
                            <option value="cpp">C++</option>
                        </select>
                    </div>
                    <textarea x-model="code" spellcheck="false" placeholder="{{ __('interview.write_solution_placeholder') }}" class="ir-q-editor-ta"></textarea>
                </div>
                @elseif($qStarter)
                <div class="ir-q-code">{{ $qStarter }}</div>
                @endif
                @if($qExample)
                <div class="ir-q-ex">
                    <div class="ir-q-ex-l"><i class="fas fa-flask"></i> Example</div>
                    <code>{{ $qExample }}</code>
                </div>
                @endif
                @if(!empty($qExpected))
                <div class="ir-q-pts">
                    @foreach($qExpected as $kp)
                    <div class="ir-q-pt"><i class="fas fa-check-circle"></i> {{ $kp }}</div>
                    @endforeach
                </div>
                @endif
                @if($qType === 'multiple_choice' && !empty($qOptions))
                <div class="ir-opts">
                    @foreach($qOptions as $oi => $opt)
                    <div class="ir-opt" :class="ans === @js($opt) && 'sel'" @click="ans = @js($opt)">
                        <div class="ir-opt-ltr">{{ chr(65 + $oi) }}</div>
                        <div class="ir-opt-txt">{{ $opt }}</div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- CODE --}}
            <div x-show="tab==='c'" x-cloak class="ir-ed">
                <div class="ir-ed-bar">
                    <div class="ir-ed-dots"><div class="ir-ed-d"></div><div class="ir-ed-d"></div><div class="ir-ed-d"></div></div>
                    <span class="ir-ed-name" x-text="'solution' + (lang==='javascript'?'.js':lang==='java'?'.java':lang==='cpp'?'.cpp':'.py')"></span>
                    <select class="ir-ed-lang" x-model="lang">
                        <option value="python">Python</option>
                        <option value="javascript">JavaScript</option>
                        <option value="java">Java</option>
                        <option value="cpp">C++</option>
                    </select>
                </div>
                <textarea x-model="code" spellcheck="false" placeholder="{{ __('interview_write_solution_here') }}"></textarea>
            </div>

            {{-- ANSWER --}}
            <div x-show="tab==='a'" x-cloak>
                @if($qType === 'multiple_choice')
                <div class="ir-q" style="text-align:center">
                    <i class="fas fa-hand-pointer" style="font-size:36px;color:var(--accent);margin-bottom:12px;display:block"></i>
                    <div style="color:var(--text-secondary);font-size:13px;line-height:1.7">
                        {{ __('interview_select_answer_hint') }}.
                    </div>
                    @if(!empty($qOptions))
                    <div style="margin-top:14px;padding:10px 16px;border-radius:8px;background:var(--accent-glow);border:1px solid rgba(99,102,241,.2);color:var(--accent-2);font-size:13px">
                        {{ __('interview_selected') }} <strong x-text="ans"></strong>
                    </div>
                    @endif
                </div>
                @elseif($qType === 'code_writing')
                <div class="ir-q">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
                        <i class="fas fa-code" style="color:var(--accent)"></i>
                        <strong style="color:var(--text)">{{ __('interview_solution') }}</strong>
                    </div>
                    <div class="ir-q-editor" style="margin-top:0">
                        <div class="ir-ed-bar">
                            <div class="ir-ed-dots"><div class="ir-ed-d"></div><div class="ir-ed-d"></div><div class="ir-ed-d"></div></div>
                            <span class="ir-ed-name" x-text="'solution' + (lang==='javascript'?'.js':lang==='java'?'.java':lang==='cpp'?'.cpp':'.py')"></span>
                            <select class="ir-ed-lang" x-model="lang">
                                <option value="python">Python</option>
                                <option value="javascript">JavaScript</option>
                                <option value="java">Java</option>
                                <option value="cpp">C++</option>
                            </select>
                        </div>
<textarea x-model="code" spellcheck="false" placeholder="{{ __('interview_write_solution_here') }}" class="ir-q-editor-ta"></textarea>
                    </div>
                    <div style="margin-top:10px;font-size:11px;color:var(--text-muted)">
                        <i class="fas fa-info-circle"></i> {{ __('interview_submit_hint') }}
                    </div>
                </div>
                @else
                <div class="ir-q">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
                        <i class="fas fa-pen" style="color:var(--accent)"></i>
                        <strong style="color:var(--text)">{{ __('interview_qtype_open_ended') }}</strong>
                    </div>
                    @if(!empty($qExpected))
                    <div style="margin-bottom:12px;padding:12px;border-radius:8px;background:var(--bg);border:1px solid var(--border)">
                        <div style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">{{ __('interview_key_points') }}</div>
                        @foreach($qExpected as $kp)
                        <div style="display:flex;align-items:center;gap:6px;padding:4px 0;font-size:12px;color:var(--text-secondary)">
                            <i class="fas fa-check-circle" style="color:var(--success);font-size:10px"></i> {{ $kp }}
                        </div>
                        @endforeach
                    </div>
                    @endif
                    <textarea class="ir-ans" x-model="ans" placeholder="{{ __('interview_placeholder_detailed') }}" rows="8"></textarea>
                    <div style="margin-top:8px;font-size:11px;color:var(--text-muted)">
                        <i class="fas fa-info-circle"></i> {{ __('interview_describe_approach') }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="ir-acts">
            <div class="ir-acts-l">
                <button class="ir-btn" @click="prev()" :disabled="qi <= 0">
                    <i class="fas fa-chevron-left"></i> {{ __('interview_back') }}
                </button>
                <button class="ir-btn" @click="next()" :disabled="qi >= 4">
                    {{ __('interview_next') }} <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <form id="irSubmitForm" action="{{ route('interview.answer', $interview->id) }}" method="POST" style="display:none">
                @csrf
                <input type="hidden" name="answer" :value="ans">
            </form>
            <button class="ir-btn pri" @click="qi >= 4 ? end() : submit()" :disabled="qi < 4 && (!ans || !ans.trim())">
                <i class="fas" :class="qi >= 4 ? 'fa-flag-checkered' : 'fa-paper-plane'"></i>
                <span x-text="qi >= 4 ? '{{ __('interview_finish') }}' : '{{ __('interview_submit') }}'"></span>
            </button>
        </div>
    </div>

    {{-- ========== SIDEBAR ========== --}}
    <div class="ir-sb" :class="[sbHide && 'hide', mobChat && 'mob-open']">
        <div class="ir-sb-hdr">
            <div class="ir-sb-t"><i class="fas fa-robot"></i> {{ __('interview_ai_interviewer') }}</div>
            <div class="ir-cam" :class="camStream && 'show'">
                <video x-ref="camVideo" autoplay muted playsinline x-show="camStream"></video>
                <div class="ir-cam-off" x-show="!camStream">
                    <i class="fas fa-video-slash"></i>
                    <span>{{ __('interview_camera_off') }}</span>
                </div>
                <div class="ir-cam-label" x-show="camStream">LIVE</div>
            </div>
            <div class="ir-sb-btns">
                <button class="ir-sb-btn" :class="mic ? 'on' : 'off'" @click="toggleMic()">
                    <i :class="mic ? 'fas fa-microphone' : 'fas fa-microphone-slash'"></i>
                </button>
                <button class="ir-sb-btn" :class="cam ? 'on' : 'off'" @click="toggleCam()">
                    <i :class="cam ? 'fas fa-video' : 'fas fa-video-slash'"></i>
                </button>
            </div>
            <div class="ir-audio-bar" x-show="mic && micStream">
                <div class="ir-audio-bar-fill" :style="'width:' + audioLevel + '%'"></div>
            </div>
            <div class="ir-listening" x-show="listening">
                <div class="ir-listening-dot"></div>
                <span>Listening... speak now</span>
            </div>
        </div>
        <canvas x-ref="frameCanvas" style="display:none" width="320" height="180"></canvas>
        <div class="ir-ch" id="irChat">
            <template x-for="(m, i) in msgs" :key="i">
                <div :class="m.s === '{{ __('interview_you') }}' && 'me'">
                    <div class="ir-ch-s" x-text="m.s"></div>
                    <div class="ir-ch-b" x-text="m.t"></div>
                </div>
            </template>
        </div>
        <div class="ir-ch-in">
            <form @submit.prevent="send()" class="ir-ch-f">
                <input x-model="nMsg" type="text" class="ir-ch-fi" placeholder="{{ __('interview_placeholder_message') }}">
                <button type="submit" class="ir-ch-sd"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>

    {{-- Toast --}}
    <div class="ir-toast" :class="t.on && 'on'" :style="'color:' + t.c">
        <i :class="t.i"></i><span x-text="t.m"></span>
    </div>
</div>
@endsection

@push('scripts')
<script>
function irRoom(){
    return {
        timer: 0,
        tab: 'q',
        ans: '',
        code: @json($qStarter),
        lang: @json($qLang),
        mic: false,
        cam: false,
        micStream: null,
        camStream: null,
        audioLevel: 0,
        audioCtx: null,
        audioAnalyser: null,
        audioAnim: null,
        listening: false,
        recognition: null,
        frameInterval: null,
        sbHide: false,
        mobChat: false,
        nMsg: '',
        msgs: [{s:'Interviewer',t:'{{ __("interview_welcome") }}'}],
        t: {on:false,m:'',i:'',c:''},
        qi: {{ $questionIndex }},
        sending: false,
        ended: false,

        fmt(s){ return String(Math.floor(s/60)).padStart(2,'0')+':'+String(s%60).padStart(2,'0'); },

        toast(m,i,c){
            this.t = {on:true,m:m||'',i:i||'fas fa-info-circle',c:c||'#818cf8'};
            setTimeout(()=>{this.t.on=false},2200);
        },

        sendMsg(msg, withImage){
            if(this.sending) return;
            this.msgs.push({s:'{{ __("interview_you") }}',t:msg});
            this.sending = true;
            this.$nextTick(()=>{const c=document.getElementById('irChat');if(c)c.scrollTop=c.scrollHeight;});

            const body = {message: msg, interview_id: {{ $interview->id }}};
            if(withImage){
                try{
                    const canvas = this.$refs.frameCanvas;
                    const video = this.$refs.camVideo;
                    if(canvas && video && video.videoWidth){
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0);
                        body.image = canvas.toDataURL('image/jpeg', 0.6).split(',')[1];
                        body.image_type = 'image/jpeg';
                    }
                }catch(e){}
            }

            fetch('{{ route("interview.aiChat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body)
            })
            .then(r => r.json())
            .then(d => {
                this.msgs.push({s:'Interviewer', t: d.reply || '{{ __("interview_ai_error_response") }}'});
                this.$nextTick(()=>{const c=document.getElementById('irChat');if(c)c.scrollTop=c.scrollHeight;});
            })
            .catch(() => {
                this.msgs.push({s:'Interviewer', t:'{{ __("interview_connection_error") }}'});
            })
            .finally(()=>{ this.sending = false; });
        },

        send(){
            if(!this.nMsg.trim()) return;
            const msg = this.nMsg;
            this.nMsg = '';
            this.sendMsg(msg, false);
        },

        async toggleMic(){
            if(this.mic){
                this.stopMic();
                return;
            }
            try{
                this.micStream = await navigator.mediaDevices.getUserMedia({audio:true});
                this.mic = true;
                this.startAudioLevel();
                this.startListening();
                this.toast('{{ __("interview_mic_on") }}','fas fa-microphone','#22c55e');
            }catch(e){
                this.toast('{{ __("interview_mic_denied") }}','fas fa-microphone-slash','#ef4444');
                this.mic = false;
            }
        },

        stopMic(){
            if(this.recognition){ this.recognition.stop(); this.recognition=null; }
            this.listening = false;
            if(this.micStream){ this.micStream.getTracks().forEach(t=>t.stop()); this.micStream=null; }
            this.mic = false;
            this.audioLevel = 0;
            if(this.audioAnim) cancelAnimationFrame(this.audioAnim);
            if(this.audioCtx){ this.audioCtx.close(); this.audioCtx=null; }
                this.toast('{{ __("interview_mic_off") }}','fas fa-microphone-slash','#ef4444');
        },

        startAudioLevel(){
            if(!this.micStream) return;
            this.audioCtx = new (window.AudioContext||window.webkitAudioContext)();
            const src = this.audioCtx.createMediaStreamSource(this.micStream);
            this.audioAnalyser = this.audioCtx.createAnalyser();
            this.audioAnalyser.fftSize = 256;
            src.connect(this.audioAnalyser);
            const data = new Uint8Array(this.audioAnalyser.frequencyBinCount);
            const update = ()=>{
                if(!this.mic){ this.audioLevel=0; return; }
                this.audioAnalyser.getByteFrequencyData(data);
                let sum=0;
                for(let i=0;i<data.length;i++) sum+=data[i];
                this.audioLevel = Math.min(100, (sum/data.length)*1.5);
                this.audioAnim = requestAnimationFrame(update);
            };
            update();
        },

        startListening(){
            const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
            if(!SR){
                this.toast('{{ __('interview.speech_requires_https') }}','fas fa-exclamation-triangle','#eab308');
                return;
            }

            this.recognition = new SR();
            this.recognition.continuous = true;
            this.recognition.interimResults = true;
            this.recognition.lang = 'ru-RU';

            let finalTranscript = '';
            let silenceTimer = null;

            this.recognition.onstart = ()=>{
                this.listening = true;
            };

            this.recognition.onresult = (e)=>{
                let interim = '';
                for(let i=e.resultIndex; i<e.results.length; i++){
                    const transcript = e.results[i][0].transcript;
                    if(e.results[i].isFinal){
                        finalTranscript += transcript + ' ';
                    } else {
                        interim += transcript;
                    }
                }

                this.nMsg = finalTranscript + interim;

                clearTimeout(silenceTimer);
                silenceTimer = setTimeout(()=>{
                    if(finalTranscript.trim()){
                        this.sendMsg(finalTranscript.trim(), this.cam);
                        finalTranscript = '';
                        this.nMsg = '';
                    }
                }, 2000);
            };

            this.recognition.onerror = (e)=>{
                if(e.error === 'not-allowed'){
                    this.toast('{{ __('interview.mic_permission_denied') }}','fas fa-microphone-slash','#ef4444');
                    this.mic = false;
                    this.listening = false;
                } else if(e.error !== 'no-speech' && e.error !== 'aborted'){
                    this.toast('{{ __('interview.recognition_error') }}: '+e.error,'fas fa-exclamation-triangle','#eab308');
                }
            };

            this.recognition.onend = ()=>{
                if(this.mic && !this.ended){
                    try{ this.recognition.start(); }catch(e){}
                } else {
                    this.listening = false;
                }
            };

            try{ this.recognition.start(); }catch(e){
                this.toast('{{ __('interview.speech_recognition_failed') }}','fas fa-exclamation-triangle','#eab308');
            }
        },

        async toggleCam(){
            if(this.cam){
                if(this.camStream){ this.camStream.getTracks().forEach(t=>t.stop()); this.camStream=null; }
                this.cam = false;
                this.stopFrameCapture();
                this.toast('{{ __('interview.camera_off') }}','fas fa-video-slash','#ef4444');
                return;
            }
            try{
                this.camStream = await navigator.mediaDevices.getUserMedia({video:{width:320,height:180,facingMode:'user'},audio:false});
                this.cam = true;
                this.$nextTick(()=>{
                    const v = this.$refs.camVideo;
                    if(v) v.srcObject = this.camStream;
                });
                this.startFrameCapture();
                this.toast('{{ __('interview.camera_on') }}','fas fa-video','#22c55e');
            }catch(e){
                this.toast('{{ __('interview.camera_denied') }}','fas fa-video-slash','#ef4444');
                this.cam = false;
            }
        },

        startFrameCapture(){
            this.stopFrameCapture();
            this.frameInterval = setInterval(()=>{
                if(!this.cam || !this.camStream || this.sending) return;
                this.sendMsg('{{ __('interview.camera_ai_prompt') }}', true);
            }, 15000);
        },

        stopFrameCapture(){
            if(this.frameInterval){ clearInterval(this.frameInterval); this.frameInterval=null; }
        },

        stopAll(){
            if(this.recognition){ this.recognition.stop(); this.recognition=null; }
            this.listening = false;
            if(this.micStream){ this.micStream.getTracks().forEach(t=>t.stop()); this.micStream=null; }
            if(this.camStream){ this.camStream.getTracks().forEach(t=>t.stop()); this.camStream=null; }
            if(this.audioCtx){ this.audioCtx.close(); this.audioCtx=null; }
            if(this.audioAnim) cancelAnimationFrame(this.audioAnim);
            this.stopFrameCapture();
        },

        goTo(i){
            if(i < 0 || i > 4 || i === this.qi) return;
            window.location = '{{ route("interview.room", $interview->id) }}?q=' + i;
        },

        prev(){ this.goTo(this.qi - 1); },

        next(){ this.goTo(this.qi + 1); },

        submit(){
            if(!this.ans || !this.ans.trim()) return;
            document.getElementById('irSubmitForm').submit();
        },

        end(){
            if(this.ended) return;
            this.ended = true;
            this.stopAll();
            fetch('{{ route("interview.finish", $interview->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({answer: this.ans || ''})
            })
            .then(()=>{ window.location='{{ route("interview.result", $interview->id) }}'; })
            .catch(()=>{ window.location='{{ route("interview.result", $interview->id) }}'; });
        },

        init(){
            const started = {{ $startedAt }} * 1000;
            const elapsed = Math.floor((Date.now() - started) / 1000);
            this.timer = Math.max(0, 2700 - elapsed);
            if(this.timer <= 0) { this.end(); return; }
            setInterval(()=>{
                if(this.ended) return;
                if(this.timer > 0) this.timer--;
                if(this.timer <= 0) this.end();
            }, 1000);
        }
    }
}
</script>
@endpush
