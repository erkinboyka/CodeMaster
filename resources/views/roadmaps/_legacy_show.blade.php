<div class="rm2-page">
    {{-- ================= HERO ================= --}}
    <section class="rm2-hero">
        <div class="rm2-hero-bg"><div class="rm2-hero-grid"></div><div class="rm2-orb rm2-orb-1"></div><div class="rm2-orb rm2-orb-2"></div></div>
        <div class="rm2-hero-inner">
            <nav class="rm2-crumb" aria-label="breadcrumb">
                <a href="{{ route('roadmaps.index') }}"><i class="fas fa-arrow-left"></i> {{ __('Roadmaps') }}</a>
                <i class="fas fa-chevron-right rm2-crumb-sep"></i>
                <span class="rm2-crumb-cur">{{ $roadmap->title }}</span>
            </nav>

            @if(isset($prevRoadmap) || isset($nextRoadmap))
            <div class="rm2-pn">
                @if($prevRoadmap)
                <a href="{{ route('roadmap.show', $prevRoadmap) }}" class="rm2-pn-btn" title="{{ $prevRoadmap }}"><i class="fas fa-chevron-left"></i><span>{{ $prevRoadmap }}</span></a>
                @endif
                @if($nextRoadmap)
                <a href="{{ route('roadmap.show', $nextRoadmap) }}" class="rm2-pn-btn rm2-pn-next" title="{{ $nextRoadmap }}"><span>{{ $nextRoadmap }}</span><i class="fas fa-chevron-right"></i></a>
                @endif
            </div>
            @endif

            <div class="rm2-badges">
                <span class="rm2-chip">{{ __('ROADMAP') }}</span>
                <span class="rm2-chip rm2-chip-accent"><i class="fas fa-sitemap"></i>{{ $totalNodes }} {{ __('topics') }}</span>
                @if($percent >= 100)<span class="rm2-chip rm2-chip-green"><i class="fas fa-check"></i>{{ __('Completed') }}</span>@endif
            </div>

            <h1 class="rm2-title">{{ $roadmap->title }}</h1>

            <div class="rm2-hero-row">
                <div class="rm2-progress">
                    <div class="rm2-progress-top"><span>{{ count($completedNodeIds) }}/{{ $totalNodes }} {{ __('done') }}</span><b>{{ $percent }}%</b></div>
                    <div class="rm2-progress-track"><span style="width:{{ $percent }}%"></span></div>
                </div>
                <button type="button" class="rm2-continue" id="rm2Continue"><i class="fas fa-play"></i>{{ __('Continue') }}</button>
            </div>
        </div>
    </section>

    {{-- ================= TOOLBAR + CANVAS (цель для fullscreen) ================= --}}
    <div id="rm2FsWrap">
    <div class="rm2-toolbar" id="rm2Toolbar">
        <div class="rm2-toolbar-inner">
            <label class="rm2-search">
                <i class="fas fa-search"></i>
                <input type="search" id="rm2Search" placeholder="{{ __('Search topics...') }}" autocomplete="off" aria-label="{{ __('Search topics...') }}">
            </label>
            <div class="rm2-filters" role="group" aria-label="filter">
                <button type="button" class="rm2-fbtn is-active" data-filter="all">{{ __('All') }}</button>
                <button type="button" class="rm2-fbtn" data-filter="available"><span class="rm2-dot rm2-dot-av"></span>{{ __('Available') }}</button>
                <button type="button" class="rm2-fbtn" data-filter="completed"><span class="rm2-dot rm2-dot-done"></span>{{ __('Completed') }}</button>
                <button type="button" class="rm2-fbtn" data-filter="locked"><span class="rm2-dot rm2-dot-lock"></span>{{ __('Locked') }}</button>
            </div>
            <div class="rm2-zoom">
                <button type="button" class="rm2-zbtn" id="rm2ZoomOut" title="{{ __('Zoom out') }}" aria-label="{{ __('Zoom out') }}"><i class="fas fa-minus"></i></button>
                <span class="rm2-zval" id="rm2ZoomVal">100%</span>
                <button type="button" class="rm2-zbtn" id="rm2ZoomIn" title="{{ __('Zoom in') }}" aria-label="{{ __('Zoom in') }}"><i class="fas fa-plus"></i></button>
                <button type="button" class="rm2-zbtn" id="rm2Fit" title="{{ __('Fit') }}" aria-label="{{ __('Fit') }}"><i class="fas fa-compress"></i></button>
                <button type="button" class="rm2-zbtn" id="rm2Fs" title="{{ __('Fullscreen') }}" aria-label="{{ __('Fullscreen') }}"><i class="fas fa-expand"></i></button>
            </div>
        </div>
    </div>

    {{-- ================= CANVAS ================= --}}
    <div class="rm2-body">
        @if($roadmap->nodes->isEmpty())
            <div class="rm2-empty">
                <div class="rm2-empty-icon"><i class="fas fa-sitemap"></i></div>
                <p>{{ __('No topics found') }}</p>
            </div>
        @else
        <div class="rm2-viewport" id="rm2Viewport">
            <div class="rm2-world" id="rm2World">
                <svg id="rm2Svg" class="rm2-svg" aria-hidden="true"></svg>
                @foreach($roadmap->nodes as $node)
                    @php
                        $deps = $node->deps;
                        if (is_string($deps)) $deps = json_decode($deps, true);
                        if (!is_array($deps)) $deps = [];
                        $deps = array_values(array_filter($deps));
                        $isDone = in_array($node->id, $completedNodeIds);
                        $depsMet = count($deps) === 0 || collect($deps)->every(fn($d) => in_array($d, $completedNodeIds));
                        $status = $isDone ? 'completed' : ($depsMet ? 'available' : 'locked');
                    @endphp
                    <div class="rm2-node rm2-node--{{ $status }}"
                         data-id="{{ $node->id }}"
                         data-status="{{ $status }}"
                         data-deps="{{ e(json_encode($deps)) }}"
                         data-title="{{ e($node->title) }}"
                         data-topic="{{ e($node->topic ?? '') }}"
                         data-course="{{ $node->course_id ?? '' }}"
                         data-exam="{{ $node->is_exam ? '1' : '0' }}"
                         data-materials="{{ e(json_encode($node->materials ?? [], JSON_UNESCAPED_UNICODE)) }}"
                         style="left:{{ (int)$node->x }}px;top:{{ (int)$node->y }}px;"
                         role="button" tabindex="0"
                         aria-label="{{ e($node->title) }}">
                        <span class="rm2-node-status">
                            @if($isDone)<i class="fas fa-check"></i>
                            @elseif($status === 'locked')<i class="fas fa-lock"></i>
                            @else<i class="fas fa-circle"></i>@endif
                        </span>
                        <span class="rm2-node-text">
                            <span class="rm2-node-topic">{{ $node->topic ?? __('Topic') }}</span>
                            <span class="rm2-node-name">{{ $node->title }}</span>
                            <span class="rm2-node-tags">
                                @if($node->is_exam)<span class="rm2-tag rm2-tag-exam">{{ __('EXAM') }}</span>
                                @elseif($node->course_id)<span class="rm2-tag rm2-tag-course">{{ __('COURSE') }}</span>@endif
                                @if($isDone)<span class="rm2-tag rm2-tag-done">{{ __('DONE') }}</span>@endif
                            </span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="rm2-empty rm2-empty-search" id="rm2NoResult" hidden>
            <div class="rm2-empty-icon"><i class="fas fa-search"></i></div>
            <p>{{ __('No topics found') }}</p>
        </div>

        <div class="rm2-legend">
            <span class="rm2-leg"><span class="rm2-dot rm2-dot-done"></span>{{ __('Completed') }}</span>
            <span class="rm2-leg"><span class="rm2-dot rm2-dot-av"></span>{{ __('Available') }}</span>
            <span class="rm2-leg"><span class="rm2-dot rm2-dot-lock"></span>{{ __('Locked') }}</span>
            <span class="rm2-leg rm2-leg-hint"><i class="fas fa-hand-pointer"></i>{{ __('Drag to pan') }} &middot; Ctrl + {{ __('scroll') }} = {{ __('Zoom in') }}/{{ __('Zoom out') }}</span>
        </div>
        @endif
    </div>
    </div>
</div>

{{-- ================= MODAL ================= --}}
<div id="rm2Modal" class="rm2-modal" role="dialog" aria-modal="true" aria-labelledby="rm2MTitle" hidden>
    <div class="rm2-dialog">
        <header class="rm2-mhead">
            <div style="min-width:0">
                <div class="rm2-mchips"><span id="rm2MTopic" class="rm2-mtopic"></span><span id="rm2MStatus" class="rm2-tag"></span></div>
                <h2 id="rm2MTitle" class="rm2-mtitle"></h2>
            </div>
            <button type="button" class="rm2-mclose" id="rm2MClose" aria-label="×"><i class="fas fa-times"></i></button>
        </header>
        <div class="rm2-mbody">
            <div id="rm2CourseWrap" hidden>
                <a id="rm2CourseBtn" href="#" class="rm2-coursebtn"><i class="fas fa-graduation-cap"></i>{{ __('Open course') }}<i class="fas fa-arrow-right" style="margin-left:auto;font-size:11px"></i></a>
            </div>
            <h3 class="rm2-msec">{{ __('Lessons & Materials') }}</h3>
            <ul id="rm2Materials" class="rm2-mats"></ul>
            <div id="rm2QuizSection" hidden>
                <div class="rm2-msep"></div>
                <h3 class="rm2-msec" id="rm2QuizTitle">{{ __('Mini Test') }}</h3>
                <div id="rm2MiniTest"></div>
                <p id="rm2MiniResult" class="rm2-quizres"></p>
            </div>
        </div>
        <footer class="rm2-mfoot">
            <button type="button" id="rm2ReadBtn" class="rm2-btn rm2-btn-primary">{{ __('I have read everything') }}</button>
            <button type="button" id="rm2CheckBtn" class="rm2-btn rm2-btn-green" hidden>{{ __('Check') }}</button>
        </footer>
    </div>
</div>

<style>
.rm2-page{background:var(--bg);color:var(--text);overflow-x:clip;min-height:100vh}
/* hero */
.rm2-hero{position:relative;overflow:hidden;isolation:isolate;border-bottom:1px solid var(--border)}
.rm2-hero-bg{position:absolute;inset:0;z-index:0;pointer-events:none}
.rm2-hero-grid{position:absolute;inset:0;opacity:.5;background-image:linear-gradient(var(--border) 1px,transparent 1px),linear-gradient(90deg,var(--border) 1px,transparent 1px);background-size:44px 44px;mask-image:radial-gradient(ellipse 75% 90% at 20% 10%,black 20%,transparent 70%);-webkit-mask-image:radial-gradient(ellipse 75% 90% at 20% 10%,black 20%,transparent 70%)}
.rm2-orb{position:absolute;border-radius:50%;filter:blur(90px)}
.rm2-orb-1{width:420px;height:420px;background:var(--accent);opacity:.13;top:-160px;left:-100px}
.rm2-orb-2{width:340px;height:340px;background:#8b5cf6;opacity:.10;bottom:-180px;right:-60px}
.rm2-hero-inner{position:relative;z-index:1;max-width:1280px;margin:0 auto;padding:32px clamp(16px,4vw,32px) 28px}
.rm2-crumb{display:flex;align-items:center;gap:8px;font-size:12.5px;color:var(--text-muted);margin-bottom:14px}
.rm2-crumb a{color:var(--text-muted);text-decoration:none;display:inline-flex;align-items:center;gap:6px}
.rm2-crumb a:hover{color:var(--accent)}
.rm2-crumb-sep{font-size:9px;opacity:.6}
.rm2-crumb-cur{color:var(--text);font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.rm2-pn{display:flex;gap:10px;margin-bottom:14px}
.rm2-pn-btn{display:inline-flex;align-items:center;gap:8px;padding:7px 14px;border-radius:10px;font-size:12.5px;font-weight:600;color:var(--text-secondary);text-decoration:none;border:1px solid var(--border);background:var(--card);max-width:45%;transition:.15s}
.rm2-pn-btn span{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.rm2-pn-btn:hover{color:var(--accent);border-color:var(--accent)}
.rm2-pn-next{margin-left:auto}
.rm2-badges{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px}
.rm2-chip{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:800;letter-spacing:.06em;padding:5px 12px;border-radius:999px;border:1px solid var(--border);background:var(--card);color:var(--text-secondary)}
.rm2-chip-accent{background:var(--accent-glow);border-color:var(--accent-glow-strong);color:var(--accent)}
.rm2-chip-green{background:rgba(34,197,94,.12);border-color:rgba(34,197,94,.3);color:#22c55e}
.rm2-title{font-size:clamp(28px,4vw,46px);font-weight:900;letter-spacing:-.02em;line-height:1.05;margin:0 0 18px;background:linear-gradient(120deg,var(--text) 30%,var(--accent));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.rm2-hero-row{display:flex;flex-wrap:wrap;gap:14px;align-items:center}
.rm2-progress{flex:1;min-width:220px;max-width:440px}
.rm2-progress-top{display:flex;justify-content:space-between;font-size:12.5px;color:var(--text-muted);margin-bottom:6px}
.rm2-progress-top b{color:var(--accent)}
.rm2-progress-track{height:8px;border-radius:99px;background:var(--bg-secondary);border:1px solid var(--border);overflow:hidden}
.rm2-progress-track span{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,var(--accent),var(--accent-hover,var(--accent-2)));transition:width .8s cubic-bezier(.4,0,.2,1)}
.rm2-continue{display:inline-flex;align-items:center;gap:8px;padding:12px 22px;border-radius:12px;font-size:14px;font-weight:700;color:#fff;border:none;cursor:pointer;background:linear-gradient(135deg,var(--accent),var(--accent-hover,var(--accent-2)));box-shadow:0 6px 22px var(--accent-glow-strong);transition:.15s}
.rm2-continue:hover{transform:translateY(-2px)}
.rm2-continue:active{transform:scale(.97)}
/* toolbar */
.rm2-toolbar{background:var(--card);border-bottom:1px solid var(--border)}
#rm2FsWrap:fullscreen,#rm2FsWrap:-webkit-full-screen{background:var(--bg);width:100%;height:100%;display:flex;flex-direction:column;padding:0;overflow:hidden}
#rm2FsWrap:fullscreen .rm2-toolbar-inner,#rm2FsWrap:-webkit-full-screen .rm2-toolbar-inner{max-width:none}
#rm2FsWrap:fullscreen .rm2-body,#rm2FsWrap:-webkit-full-screen .rm2-body{flex:1;min-height:0;max-width:none;margin:0;padding:12px 16px 8px;overflow:hidden;display:flex;flex-direction:column}
#rm2FsWrap:fullscreen .rm2-viewport,#rm2FsWrap:-webkit-full-screen .rm2-viewport{flex:1;min-height:0;height:auto}
#rm2FsWrap:fullscreen .rm2-legend,#rm2FsWrap:-webkit-full-screen .rm2-legend{padding:8px 4px 2px}
.rm2-toolbar-inner{max-width:1280px;margin:0 auto;padding:10px clamp(16px,4vw,32px);display:flex;flex-wrap:wrap;gap:10px;align-items:center}
.rm2-search{display:flex;align-items:center;gap:8px;flex:1;min-width:180px;max-width:320px;background:var(--bg-secondary);border:1px solid var(--border);border-radius:10px;padding:0 12px;color:var(--text-muted)}
.rm2-search:focus-within{border-color:var(--accent)}
.rm2-search input{flex:1;background:none;border:none;outline:none;color:var(--text);font-size:13px;padding:9px 0;font-family:inherit;min-width:0}
.rm2-filters{display:flex;gap:6px;flex-wrap:wrap}
.rm2-fbtn{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;padding:7px 13px;border-radius:999px;border:1px solid var(--border);background:var(--card);color:var(--text-secondary);cursor:pointer;transition:.15s;font-family:inherit}
.rm2-fbtn:hover{border-color:var(--accent)}
.rm2-fbtn.is-active{background:var(--accent-glow);border-color:var(--accent);color:var(--accent)}
.rm2-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.rm2-dot-done{background:#22c55e}.rm2-dot-av{background:var(--accent)}.rm2-dot-lock{background:var(--text-muted)}
.rm2-zoom{display:flex;align-items:center;gap:6px;margin-left:auto}
.rm2-zbtn{width:32px;height:32px;border-radius:9px;border:1px solid var(--border);background:var(--card);color:var(--text-secondary);cursor:pointer;font-size:12px;transition:.15s;display:inline-flex;align-items:center;justify-content:center}
.rm2-zbtn:hover{border-color:var(--accent);color:var(--accent)}
.rm2-zval{font-size:12px;font-weight:700;color:var(--text-muted);min-width:44px;text-align:center;font-variant-numeric:tabular-nums}
/* canvas */
.rm2-body{max-width:1280px;margin:0 auto;padding:20px clamp(16px,4vw,32px) 40px}
.rm2-viewport{position:relative;height:72vh;min-height:480px;overflow:auto;border:1px solid var(--border);border-radius:18px;background:var(--card);cursor:grab;touch-action:none;overscroll-behavior:contain}
.rm2-viewport:active{cursor:grabbing}
.rm2-world{position:relative;transform-origin:0 0;min-width:100%;min-height:100%}
.rm2-svg{position:absolute;top:0;left:0;pointer-events:none;overflow:visible}
.rm2-node{position:absolute;width:220px;padding:12px 14px 12px 12px;border-radius:14px;display:flex;gap:10px;align-items:flex-start;background:var(--bg-secondary);border:2px solid var(--border);cursor:pointer;transition:transform .15s,border-color .15s,box-shadow .15s,opacity .15s;z-index:2}
.rm2-node:hover{transform:scale(1.04);box-shadow:0 10px 28px rgba(0,0,0,.18)}
.rm2-node:focus-visible{outline:2px solid var(--accent);outline-offset:2px}
.rm2-node--completed{border-color:#22c55e;background:color-mix(in srgb,#22c55e 9%,var(--card))}
.rm2-node--available{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
.rm2-node--available:hover{box-shadow:0 0 0 4px var(--accent-glow-strong),0 10px 28px rgba(0,0,0,.18)}
.rm2-node--locked{opacity:.55;cursor:not-allowed}
.rm2-node--locked:hover{transform:none;box-shadow:none}
.rm2-node.is-hidden{display:none}
.rm2-node-status{width:26px;height:26px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:11px;background:var(--card);border:2px solid var(--border);color:var(--text-muted)}
.rm2-node--completed .rm2-node-status{background:rgba(34,197,94,.15);border-color:#22c55e;color:#22c55e}
.rm2-node--available .rm2-node-status{border-color:var(--accent);color:var(--accent)}
.rm2-node--available .rm2-node-status i{font-size:7px}
.rm2-node-text{flex:1;min-width:0;display:flex;flex-direction:column}
.rm2-node-topic{font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted)}
.rm2-node--available .rm2-node-topic{color:var(--accent)}
.rm2-node-name{font-size:13.5px;font-weight:700;color:var(--text);line-height:1.35;margin-top:1px}
.rm2-node-tags{display:flex;gap:5px;margin-top:7px;flex-wrap:wrap}
.rm2-tag{padding:2px 8px;border-radius:999px;font-size:9px;text-transform:uppercase;font-weight:800;letter-spacing:.05em}
.rm2-tag-exam{background:rgba(168,85,247,.15);color:#a855f7}
.rm2-tag-course{background:var(--accent-glow);color:var(--accent)}
.rm2-tag-done{background:rgba(34,197,94,.15);color:#22c55e}
.rm2-node-pulse{animation:rm2Pulse 1s ease 2}
@@keyframes rm2Pulse{0%,100%{box-shadow:0 0 0 0 var(--accent-glow-strong)}50%{box-shadow:0 0 0 10px transparent,0 0 0 4px var(--accent)}}
.rm2-empty{display:flex;flex-direction:column;align-items:center;gap:10px;padding:60px 20px;color:var(--text-muted);font-size:14px}
.rm2-empty-icon{width:72px;height:72px;border-radius:20px;background:var(--bg-secondary);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:28px}
.rm2-legend{display:flex;flex-wrap:wrap;gap:8px 18px;align-items:center;padding:14px 4px 0;font-size:12px;color:var(--text-muted)}
.rm2-leg{display:inline-flex;align-items:center;gap:6px}
.rm2-leg-hint{margin-left:auto}
.rm2-leg-hint i{color:var(--accent)}
/* modal */
.rm2-modal{position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.65);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;padding:16px}
.rm2-modal[hidden]{display:none}
.rm2-dialog{background:var(--card);border:1px solid var(--border);border-radius:18px;width:100%;max-width:640px;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.4);animation:rm2In .2s ease-out}
@@keyframes rm2In{from{opacity:0;transform:scale(.96) translateY(8px)}to{opacity:1;transform:none}}
.rm2-mhead{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:20px 22px 14px;border-bottom:1px solid var(--border);flex-shrink:0;background:var(--card)}
.rm2-mchips{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:6px}
.rm2-mtopic{font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:var(--accent);background:var(--accent-glow);padding:3px 10px;border-radius:999px}
.rm2-mtitle{font-size:20px;font-weight:800;margin:0;line-height:1.3}
.rm2-mclose{background:none;border:none;color:var(--text-muted);font-size:18px;cursor:pointer;padding:8px;border-radius:9px;transition:.15s;flex-shrink:0}
.rm2-mclose:hover{background:var(--bg-secondary);color:var(--text)}
.rm2-mbody{padding:18px 22px;flex:1 1 auto;min-height:0;overflow-y:auto;overscroll-behavior:contain;color:var(--text);font-size:14px;line-height:1.65;scrollbar-width:thin;scrollbar-color:var(--border) transparent}
.rm2-mbody::-webkit-scrollbar{width:10px}
.rm2-mbody::-webkit-scrollbar-track{background:transparent;margin:8px 0}
.rm2-mbody::-webkit-scrollbar-thumb{background:var(--border);border-radius:99px;border:3px solid transparent;background-clip:padding-box}
.rm2-mbody::-webkit-scrollbar-thumb:hover{background:var(--text-muted);border:3px solid transparent;background-clip:padding-box}
.rm2-coursebtn{display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:12px;font-weight:700;font-size:13.5px;color:#fff;text-decoration:none;background:linear-gradient(135deg,var(--accent),var(--accent-hover,var(--accent-2)));margin-bottom:16px;transition:.15s}
.rm2-coursebtn:hover{transform:translateY(-1px)}
.rm2-msec{font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin:0 0 10px}
.rm2-mats{list-style:none;margin:0 0 4px;padding:0;display:flex;flex-direction:column;gap:8px}
.rm2-mats a{color:var(--accent);font-weight:600;text-decoration:underline;text-underline-offset:3px}
.rm2-mat-lesson{padding:12px 14px;border:1px solid var(--border);border-radius:12px;background:var(--bg-secondary)}
.rm2-mat-lesson b{display:block;font-size:14px;margin-bottom:4px}
.rm2-mat-lesson p{margin:0;font-size:13px;color:var(--text-secondary)}
.rm2-mat-extra{font-size:12px;color:var(--accent);margin-top:6px}
.rm2-mat-empty{color:var(--text-muted);font-style:italic;font-size:13.5px}
.rm2-msep{border-top:1px solid var(--border);margin:16px 0}
.rm2-q{margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid var(--border)}
.rm2-q:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0}
.rm2-q p{margin:0 0 10px;font-weight:600;font-size:14.5px}
.rm2-opt{display:block;border:1px solid var(--border);border-radius:10px;padding:10px 14px;cursor:pointer;transition:.15s;font-size:13.5px;margin-bottom:6px;background:var(--bg-secondary)}
.rm2-opt:hover{border-color:var(--accent)}
.rm2-opt.sel{border-color:var(--accent);background:var(--accent-glow)}
.rm2-opt.ok{border-color:#22c55e!important;background:rgba(34,197,94,.1)!important}
.rm2-opt.bad{border-color:#f43f5e!important;background:rgba(244,63,94,.08)!important}
.rm2-qcount{display:block;text-align:center;padding:8px;background:var(--accent-glow);border-radius:9px;font-size:12.5px;font-weight:700;color:var(--accent);margin-bottom:12px}
.rm2-quizres{margin:10px 0 0;font-size:13.5px;font-weight:600}
.rm2-mfoot{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:10px;flex-shrink:0;background:var(--card)}
.rm2-btn{flex:1;padding:12px;border-radius:12px;font-size:14px;font-weight:700;border:none;cursor:pointer;transition:.15s;font-family:inherit}
.rm2-btn[hidden]{display:none}
.rm2-btn-primary{background:linear-gradient(135deg,var(--accent),var(--accent-hover,var(--accent-2)));color:#fff}
.rm2-btn-primary:hover{transform:translateY(-1px)}
.rm2-btn-primary.done{background:#22c55e!important}
.rm2-btn-green{background:#22c55e;color:#fff}
.rm2-btn-green:hover{background:#4ade80}
@@media(max-width:640px){.rm2-zoom{margin-left:0}.rm2-leg-hint{display:none}.rm2-viewport{min-height:60vh}}
@@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important}}
</style>

<script>
(function(){
    'use strict';
    var viewport = document.getElementById('rm2Viewport');
    var world = document.getElementById('rm2World');
    var svg = document.getElementById('rm2Svg');
    var nodes = Array.prototype.slice.call(document.querySelectorAll('.rm2-node'));
    if (!viewport || !world) return;

    var NS = 'http://www.w3.org/2000/svg';
    var zoom = 1, curFilter = 'all', curQuery = '';
    var courseBase = @json(url('/courses'));

    function esc(s){return String(s == null ? '' : s).replace(/[&<>"']/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]})}
    function cssVar(name){return getComputedStyle(document.documentElement).getPropertyValue(name).trim()}
    function accent(){return cssVar('--accent') || '#38bdf8'}
    function muted(){return cssVar('--text-muted') || '#94a3b8'}
    function getDeps(el){try{var d=JSON.parse(el.dataset.deps||'[]');return Array.isArray(d)?d:[]}catch(e){return[]}}
    function isDone(el){return el.dataset.status==='completed'}
    function isHidden(el){return el.classList.contains('is-hidden')}
    function nodeById(id){return document.querySelector('.rm2-node[data-id="'+id+'"]')}

    /* ---------- sizing ---------- */
    var contentW = 0, contentH = 0;
    function layout(){
        var maxX = 0, maxY = 0;
        nodes.forEach(function(n){
            if (isHidden(n)) return;
            maxX = Math.max(maxX, n.offsetLeft + n.offsetWidth);
            maxY = Math.max(maxY, n.offsetTop + n.offsetHeight);
        });
        contentW = maxX + 60;
        contentH = maxY + 60;
        var W = Math.max(contentW, viewport.clientWidth);
        var H = Math.max(contentH, viewport.clientHeight);
        world.style.width = W + 'px';
        world.style.height = H + 'px';
        svg.setAttribute('width', W);
        svg.setAttribute('height', H);
        drawLines();
    }

    /* ---------- edges ---------- */
    function drawLines(){
        while (svg.firstChild) svg.removeChild(svg.firstChild);
        var defs = document.createElementNS(NS,'defs');
        var A = accent(), M = muted();
        [['rm2arrA',A],['rm2arrL',M],['rm2arrD','#22c55e']].forEach(function(m){
            var mk=document.createElementNS(NS,'marker');
            mk.setAttribute('id',m[0]);mk.setAttribute('markerWidth','9');mk.setAttribute('markerHeight','8');
            mk.setAttribute('refX','8');mk.setAttribute('refY','4');mk.setAttribute('orient','auto');
            var p=document.createElementNS(NS,'path');
            p.setAttribute('d','M0,0 L9,4 L0,8');p.setAttribute('fill',m[1]);
            mk.appendChild(p);defs.appendChild(mk);
        });
        svg.appendChild(defs);
        nodes.forEach(function(c){
            if (isHidden(c)) return;
            getDeps(c).forEach(function(did){
                var par = nodeById(did);
                if (!par || isHidden(par)) return;
                var x1=par.offsetLeft+par.offsetWidth, y1=par.offsetTop+par.offsetHeight/2;
                var x2=c.offsetLeft, y2=c.offsetTop+c.offsetHeight/2;
                var d;
                if (x2 > x1 + 8){
                    var mx=(x1+x2)/2;
                    d='M'+x1+','+y1+' C'+mx+','+y1+' '+mx+','+y2+' '+x2+','+y2;
                } else {
                    var bx=Math.max(x1,x2)+70;
                    d='M'+x1+','+y1+' C'+bx+','+y1+' '+bx+','+y2+' '+x2+','+y2;
                }
                var path=document.createElementNS(NS,'path');
                path.setAttribute('d',d);
                path.setAttribute('fill','none');
                path.setAttribute('stroke-width','2.5');
                path.setAttribute('stroke-linecap','round');
                var pd=isDone(par), cd=isDone(c);
                if (pd&&cd){path.setAttribute('stroke','#22c55e');path.setAttribute('marker-end','url(#rm2arrD)')}
                else if (pd){path.setAttribute('stroke',A);path.setAttribute('marker-end','url(#rm2arrA)')}
                else{path.setAttribute('stroke',M);path.setAttribute('stroke-opacity','.55');path.setAttribute('stroke-dasharray','6 6');path.setAttribute('marker-end','url(#rm2arrL)')}
                svg.appendChild(path);
            });
        });
    }

    /* ---------- filters ---------- */
    var searchInput = document.getElementById('rm2Search');
    var noResult = document.getElementById('rm2NoResult');
    function applyFilters(){
        var q = curQuery.trim().toLowerCase(), shown = 0;
        nodes.forEach(function(n){
            var okF = curFilter==='all' || n.dataset.status===curFilter;
            var okQ = !q || (n.dataset.title+' '+(n.dataset.topic||'')).toLowerCase().indexOf(q)!==-1;
            var show = okF && okQ;
            n.classList.toggle('is-hidden', !show);
            if (show) shown++;
        });
        if (noResult) noResult.hidden = shown !== 0;
        if (viewport) viewport.style.display = shown === 0 ? 'none' : '';
        layout();
    }
    if (searchInput) searchInput.addEventListener('input', function(){curQuery=this.value;applyFilters()});
    document.querySelectorAll('.rm2-fbtn').forEach(function(b){
        b.addEventListener('click', function(){
            document.querySelectorAll('.rm2-fbtn').forEach(function(x){x.classList.remove('is-active')});
            b.classList.add('is-active');
            curFilter = b.dataset.filter;
            applyFilters();
        });
    });

    /* ---------- zoom ---------- */
    var zoomVal = document.getElementById('rm2ZoomVal');
    function setZoom(z){
        zoom = Math.min(2, Math.max(.3, z));
        world.style.transform = 'scale(' + zoom + ')';
        if (zoomVal) zoomVal.textContent = Math.round(zoom*100) + '%';
    }
    document.getElementById('rm2ZoomIn').addEventListener('click', function(){setZoom(zoom+.15)});
    document.getElementById('rm2ZoomOut').addEventListener('click', function(){setZoom(zoom-.15)});
    document.getElementById('rm2Fit').addEventListener('click', fitView);
    /* ---------- fullscreen секции ---------- */
    var fsWrap = document.getElementById('rm2FsWrap');
    var fsBtn = document.getElementById('rm2Fs');
    function fsOn(){
        return !!(document.fullscreenElement || document.webkitFullscreenElement);
    }
    function fsIcon(){
        if (fsBtn) fsBtn.innerHTML = fsOn() ? '<i class="fas fa-compress"></i>' : '<i class="fas fa-expand"></i>';
    }
    if (fsBtn) fsBtn.addEventListener('click', function(){
        if (fsOn()){ if(document.exitFullscreen)document.exitFullscreen(); else if(document.webkitExitFullscreen)document.webkitExitFullscreen(); }
        else if (fsWrap){
            if(fsWrap.requestFullscreen)fsWrap.requestFullscreen();
            else if(fsWrap.webkitRequestFullscreen)fsWrap.webkitRequestFullscreen();
        }
    });
    function fsChanged(){ fsIcon(); setTimeout(function(){ layout(); if (fsOn()) fitView(); }, 80); }
    document.addEventListener('fullscreenchange', fsChanged);
    document.addEventListener('webkitfullscreenchange', fsChanged);
    viewport.addEventListener('wheel', function(e){
        if (!e.ctrlKey && !e.metaKey) return;
        e.preventDefault();
        setZoom(zoom + (e.deltaY < 0 ? .1 : -.1));
    }, {passive:false});
    function fitView(){
        if (!contentW || !contentH) return;
        var z = Math.min(1.25, (viewport.clientWidth-48)/contentW, (viewport.clientHeight-48)/contentH);
        z = Math.min(2, Math.max(.3, z));
        setZoom(z);
        // скроллим после применения трансформации, в следующем кадре
        requestAnimationFrame(function(){
            viewport.scrollLeft = Math.max(0, (contentW*zoom - viewport.clientWidth)/2);
            viewport.scrollTop = Math.max(0, (contentH*zoom - viewport.clientHeight)/2);
        });
    }

    /* ---------- continue ---------- */
    document.getElementById('rm2Continue').addEventListener('click', function(){
        var target = document.querySelector('.rm2-node--available:not(.is-hidden)') || document.querySelector('.rm2-node:not(.is-hidden)');
        if (!target) return;
        if (curFilter!=='all' || curQuery.trim()){
            curFilter='all'; curQuery=''; if(searchInput) searchInput.value='';
            document.querySelectorAll('.rm2-fbtn').forEach(function(x){x.classList.toggle('is-active',x.dataset.filter==='all')});
            applyFilters();
        }
        setZoom(1);
        var cx = target.offsetLeft + target.offsetWidth/2, cy = target.offsetTop + target.offsetHeight/2;
        viewport.scrollLeft = Math.max(0, cx - viewport.clientWidth/2);
        viewport.scrollTop = Math.max(0, cy - viewport.clientHeight/2);
        target.classList.remove('rm2-node-pulse'); void target.offsetWidth; target.classList.add('rm2-node-pulse');
        target.focus({preventScroll:true});
    });

    /* ---------- pan (mouse + touch) ---------- */
    var drag=false,sx=0,sy=0,sl=0,st=0,vx=0,vy=0,raf=null,moved=false;
    viewport.addEventListener('mousedown', function(e){
        if (e.button!==0 || e.target.closest('.rm2-node')) return;
        drag=true;moved=false;sx=e.pageX;sy=e.pageY;sl=viewport.scrollLeft;st=viewport.scrollTop;vx=vy=0;
        if(raf)cancelAnimationFrame(raf);
    });
    window.addEventListener('mousemove', function(e){
        if(!drag)return;
        var dx=e.pageX-sx, dy=e.pageY-sy;
        if(Math.abs(dx)+Math.abs(dy)>4)moved=true;
        vx=dx*.25;vy=dy*.25;
        viewport.scrollLeft=sl-dx;viewport.scrollTop=st-dy;
    });
    window.addEventListener('mouseup', function(){
        if(!drag)return;drag=false;
        (function mom(){
            if(Math.abs(vx)<.4&&Math.abs(vy)<.4)return;
            viewport.scrollLeft-=vx;viewport.scrollTop-=vy;vx*=.93;vy*=.93;
            raf=requestAnimationFrame(mom);
        })();
    });
    var tsx=0,tsy=0,tsl=0,tst=0,panT=false;
    viewport.addEventListener('touchstart', function(e){
        if(e.touches.length!==1||e.target.closest('.rm2-node')){panT=false;return}
        panT=true;tsx=e.touches[0].pageX;tsy=e.touches[0].pageY;tsl=viewport.scrollLeft;tst=viewport.scrollTop;
    }, {passive:true});
    viewport.addEventListener('touchmove', function(e){
        if(!panT||e.touches.length!==1)return;
        e.preventDefault();
        viewport.scrollLeft=tsl-(e.touches[0].pageX-tsx);
        viewport.scrollTop=tst-(e.touches[0].pageY-tsy);
    }, {passive:false});
    viewport.addEventListener('touchend', function(){panT=false});

    /* ---------- nodes open modal ---------- */
    nodes.forEach(function(el){
        el.addEventListener('click', function(){ if(el.dataset.status!=='locked') openModal(el) });
        el.addEventListener('keydown', function(e){
            if((e.key==='Enter'||e.key===' ')&&el.dataset.status!=='locked'){e.preventDefault();openModal(el)}
        });
    });

    /* ---------- modal ---------- */
    var modal=document.getElementById('rm2Modal');
    var mTopic=document.getElementById('rm2MTopic'), mTitle=document.getElementById('rm2MTitle'),
        mStatus=document.getElementById('rm2MStatus'), mMats=document.getElementById('rm2Materials'),
        qSec=document.getElementById('rm2QuizSection'), qTitle=document.getElementById('rm2QuizTitle'),
        qBox=document.getElementById('rm2MiniTest'), qRes=document.getElementById('rm2MiniResult'),
        readBtn=document.getElementById('rm2ReadBtn'), checkBtn=document.getElementById('rm2CheckBtn'),
        courseWrap=document.getElementById('rm2CourseWrap'), courseBtn=document.getElementById('rm2CourseBtn');
    var curId=null, lastFocus=null;
    var quizData={!! json_encode($quizData ?? []) !!};
    var lessonsData={!! json_encode($lessonsData ?? []) !!};

    function statusChip(st){
        if(st==='completed')return['rm2-tag rm2-tag-done','{{ __("DONE") }}'];
        if(st==='locked')return['rm2-tag','{{ __("Locked") }}'];
        return['rm2-tag rm2-tag-course','{{ __("Available") }}'];
    }
    function openModal(el){
        curId=el.dataset.id; lastFocus=document.activeElement;
        mTopic.textContent=el.dataset.topic||'{{ __("Topic") }}';
        mTitle.textContent=el.dataset.title||'';
        var sc=statusChip(el.dataset.status); mStatus.className=sc[0]; mStatus.textContent=sc[1];
        if(el.dataset.course){courseBtn.href=courseBase+'/'+el.dataset.course;courseWrap.hidden=false}
        else{courseWrap.hidden=true}
        mMats.innerHTML=''; var hasAny=false;
        var nodeMats=[]; try{nodeMats=JSON.parse(el.dataset.materials||'[]')}catch(e){nodeMats=[]}
        if(!Array.isArray(nodeMats))nodeMats=[];
        nodeMats.forEach(function(m){
            hasAny=true;
            var url=(m&&m.url)||'#', label=(m&&m.label)||'Link';
            var li=document.createElement('li');
            var a=document.createElement('a');a.href=url;a.target='_blank';a.rel='noopener';a.textContent=label;
            li.appendChild(a);mMats.appendChild(li);
        });
        lessonsData.filter(function(l){return String(l.node_id)===String(curId)}).forEach(function(l){
            hasAny=true;
            var li=document.createElement('li');li.className='rm2-mat-lesson';
            var b=document.createElement('b');b.textContent=l.title||'Lesson';li.appendChild(b);
            if(l.description){var p=document.createElement('p');p.textContent=l.description;li.appendChild(p)}
            var extra='';
            if(typeof l.materials==='string'&&l.materials.trim())extra=l.materials;
            else if(Array.isArray(l.materials))extra=l.materials.map(function(m){return m.label||m.title||m}).join(', ');
            if(extra){var s=document.createElement('div');s.className='rm2-mat-extra';s.textContent=extra;li.appendChild(s)}
            mMats.appendChild(li);
        });
        if(!hasAny){var li=document.createElement('li');li.className='rm2-mat-empty';li.textContent='{{ __("Materials are being added.") }}';mMats.appendChild(li)}
        qSec.hidden=true;readBtn.hidden=false;checkBtn.hidden=true;
        readBtn.classList.remove('done');readBtn.textContent='{{ __("I have read everything") }}';
        qRes.textContent='';qBox.innerHTML='';
        var isExam=el.dataset.exam==='1';
        window._rm2Qs=quizData.filter(function(q){return String(q.node_id)===String(curId)});
        window._rm2Exam=isExam;
        qTitle.textContent=isExam?'{{ __("Exam") }}':'{{ __("Mini Test") }}';
        modal.hidden=false;document.body.style.overflow='hidden';
        document.getElementById('rm2MClose').focus();
    }
    function closeModal(){modal.hidden=true;document.body.style.overflow='';curId=null;if(lastFocus&&lastFocus.focus)lastFocus.focus({preventScroll:true})}
    document.getElementById('rm2MClose').addEventListener('click', closeModal);
    modal.addEventListener('click', function(e){if(e.target===modal)closeModal()});
    document.addEventListener('keydown', function(e){if(e.key==='Escape'&&!modal.hidden)closeModal()});

    readBtn.addEventListener('click', function(){
        var nodeQs=window._rm2Qs||[], isExam=window._rm2Exam;
        readBtn.classList.add('done');readBtn.textContent='{{ __("Theory completed!") }}';readBtn.hidden=true;
        qSec.hidden=false;checkBtn.hidden=false;
        if(!nodeQs.length){
            qBox.innerHTML='';
            var p=document.createElement('p');p.className='rm2-mat-empty';
            p.textContent=isExam?'{{ __("Exam is being prepared.") }}':'{{ __("Quiz is being prepared.") }}';
            qBox.appendChild(p);return;
        }
        qBox.innerHTML='';
        var cnt=document.createElement('div');cnt.className='rm2-qcount';
        cnt.innerHTML='<span id="rm2QCount">0</span>/'+nodeQs.length+' {{ __("answered") }}';
        qBox.appendChild(cnt);
        nodeQs.forEach(function(q,i){
            var opts=typeof q.options==='string'?(function(){try{return JSON.parse(q.options)}catch(e){return[]}})():(q.options||[]);
            var box=document.createElement('div');box.className='rm2-q';
            var t=document.createElement('p');t.textContent=(i+1)+'. '+(q.question||'');box.appendChild(t);
            opts.forEach(function(o,j){
                var d=document.createElement('div');d.className='rm2-opt';d.dataset.q=i;d.dataset.o=j;d.textContent=o;
                d.setAttribute('role','radio');d.setAttribute('tabindex','0');
                d.addEventListener('click',function(){rm2Sel(d)});
                d.addEventListener('keydown',function(e){if(e.key==='Enter'||e.key===' '){e.preventDefault();rm2Sel(d)}});
                box.appendChild(d);
            });
            qBox.appendChild(box);
        });
    });
    window.rm2Sel=function(div){
        var q=div.dataset.q;
        qBox.querySelectorAll('.rm2-opt[data-q="'+q+'"]').forEach(function(s){s.classList.remove('sel')});
        div.classList.add('sel');
        var n=0;
        qBox.querySelectorAll('.rm2-q').forEach(function(qq,qi){
            if(qq.querySelector('.rm2-opt[data-q="'+qi+'"].sel'))n++;
        });
        var c=document.getElementById('rm2QCount');if(c)c.textContent=n;
    };
    checkBtn.addEventListener('click', function(){
        var nodeQs=window._rm2Qs||[], isExam=window._rm2Exam;
        if(!nodeQs.length){qRes.textContent=isExam?'{{ __("No exam available.") }}':'{{ __("No quiz available.") }}';qRes.style.color=muted();return}
        var ok=0;
        nodeQs.forEach(function(q,i){
            var opts=typeof q.options==='string'?(function(){try{return JSON.parse(q.options)}catch(e){return[]}})():(q.options||[]);
            var ci=opts.indexOf(q.correct_answer);
            var sel=qBox.querySelector('.rm2-opt[data-q="'+i+'"].sel');
            var si=sel?parseInt(sel.dataset.o,10):-1;
            qBox.querySelectorAll('.rm2-opt[data-q="'+i+'"]').forEach(function(d){
                var oi=parseInt(d.dataset.o,10);
                d.classList.remove('ok','bad');
                if(oi===ci)d.classList.add('ok');
                else if(oi===si)d.classList.add('bad');
            });
            if(si===ci&&si!==-1)ok++;
        });
        var total=nodeQs.length, pct=Math.round(ok/total*100);
        if(isExam){
            if(pct>=70){
                qRes.textContent='{{ __("Exam passed:") }} '+pct+'% ('+ok+'/'+total+')';qRes.style.color='#22c55e';
                completeNode();
            }else{qRes.textContent='{{ __("Exam failed:") }} '+pct+'% ('+ok+'/'+total+')';qRes.style.color='#f43f5e'}
        }else{
            if(ok===total){
                qRes.textContent='{{ __("Perfect! All correct:") }} '+ok+'/'+total+' {{ __("Node completed!") }}';qRes.style.color='#22c55e';
                completeNode();
            }else{qRes.textContent='{{ __("Score:") }} '+ok+'/'+total+' ('+pct+'%). {{ __("Answer the wrong ones correctly.") }}';qRes.style.color=pct>=50?'#eab308':'#f43f5e'}
        }
    });
    function completeNode(){
        fetch('/roadmap/complete-node',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},body:JSON.stringify({node_id:curId})})
        .then(function(r){return r.json()}).then(function(d){if(d&&d.percent!==undefined)location.reload()});
    }

    /* ---------- init ---------- */
    layout();
    window.addEventListener('load', layout);
    window.addEventListener('resize', layout);
    if(document.fonts&&document.fonts.ready)document.fonts.ready.then(layout);
    new MutationObserver(function(){drawLines()}).observe(document.documentElement,{attributes:true,attributeFilter:['data-theme']});
})();
</script>

@include('components.ai-assistant', ['context' => 'roadmap', 'contextTitle' => $roadmap->title ?? ''])
