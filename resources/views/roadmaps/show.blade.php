@extends('layouts.app')

@section('title', ($roadmap->title ?? 'Roadmap') . ' - CodeMaster')

@section('content')
@if(!empty($roadmap->is_legacy))
    @include('roadmaps._legacy_show')
@else
@php
    // Логика roadmap = курс: дерево строится из шагов привязанного курса.
    $orderedSteps = $course ? $course->steps->sortBy('sort_order')->values() : collect();
    $nextStep = $orderedSteps->first(fn($s) => !in_array($s->id, $completedSteps ?? []));
    $nextId = $nextStep?->id;
    $totalXp = $orderedSteps->sum('experience');
    $excerpt = fn($s) => mb_strimwidth(trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($s->description ?? '')))), 0, 220, '…');
@endphp
<div class="rp-page">
    {{-- ================= HERO (как на Python Developer) ================= --}}
    <section class="rp-hero">
        <div class="rp-hero-bg"><div class="rp-hero-grid"></div><div class="rp-orb rp-orb-1"></div><div class="rp-orb rp-orb-2"></div></div>
        <div class="rp-hero-inner">
            <nav class="rp-crumb" aria-label="breadcrumb">
                <a href="{{ route('roadmaps.index') }}"><i class="fas fa-arrow-left"></i> {{ __('Roadmaps') }}</a>
                <i class="fas fa-chevron-right rp-crumb-sep"></i>
                <span class="rp-crumb-cur">{{ $roadmap->title }}</span>
            </nav>

            <div class="rp-badges">
                <span class="rp-chip">{{ $roadmap->ai_generated ? 'AI GENERATED' : __('ROADMAP') }}</span>
                <span class="rp-chip rp-chip-accent"><i class="fas fa-list-ol"></i>{{ $totalSteps }} {{ __('steps') }}</span>
                @if($totalXp)<span class="rp-chip rp-chip-accent"><i class="fas fa-bolt"></i>{{ $totalXp }} XP</span>@endif
                @if($percent >= 100)<span class="rp-chip rp-chip-green"><i class="fas fa-check"></i>{{ __('Completed') }}</span>@endif
            </div>

            <h1 class="rp-title">{{ $roadmap->title }}</h1>
            @if($course && $course->description)
                <p class="rp-desc">{{ $course->description }}</p>
            @endif

            <div class="rp-hero-row">
                <div class="rp-progress">
                    <div class="rp-progress-top"><span>{{ $completedCount }}/{{ $totalSteps }} {{ __('done') }}</span><b>{{ $percent }}%</b></div>
                    <div class="rp-progress-track"><span style="width:{{ $percent }}%"></span></div>
                </div>
                @if(!$course || $parentSteps->isEmpty())
                @elseif(!$enrolled)
                    <form method="POST" action="{{ route('roadmap.enroll') }}">@csrf<input type="hidden" name="roadmap_id" value="{{ $roadmap->id }}"><button type="submit" class="rp-continue"><i class="fas fa-rocket"></i>{{ __('Start Learning') }}</button></form>
                @elseif($nextStep)
                    <a href="{{ route('courses.step', [$course->id, $nextStep->id]) }}" class="rp-continue"><i class="fas fa-play"></i>{{ __('Continue') }}: {{ mb_strimwidth($nextStep->title, 0, 28, '…') }}</a>
                @else
                    <span class="rp-continue rp-continue-done"><i class="fas fa-check"></i>{{ __('Completed') }}</span>
                @endif
            </div>
        </div>
    </section>

    {{-- ================= BODY ================= --}}
    <div class="rp-body">
        @if(!$course || $parentSteps->isEmpty())
            <div class="rp-empty">
                <div class="rp-empty-icon"><i class="fas fa-sitemap"></i></div>
                <h2 class="rp-empty-title">{{ $roadmap->title }}</h2>
                <p class="rp-empty-desc">{{ __('Course is being generated...') }}</p>
                @if(!$enrolled)
                    <form method="POST" action="{{ route('roadmap.enroll') }}">@csrf<input type="hidden" name="roadmap_id" value="{{ $roadmap->id }}"><button type="submit" class="rp-continue"><i class="fas fa-rocket"></i>{{ __('Start Learning') }}</button></form>
                @endif
            </div>
        @else
        <main class="rp-main" x-data="rpTree()">
            <div id="rpFsWrap">
            <div class="rp-treebar">
                <div class="rp-treebar-btns" role="group" aria-label="tree direction">
                    <button type="button" class="rp-tbtn" :class="dir === 'v' && 'is-active'" @click="dir = 'v'; localStorage.setItem('rp-tree-dir', 'v')" title="{{ __('Vertical') }}"><i class="fas fa-arrows-up-down"></i><span>{{ __('Vertical') }}</span></button>
                    <button type="button" class="rp-tbtn" :class="dir === 'h' && 'is-active'" @click="dir = 'h'; localStorage.setItem('rp-tree-dir', 'h')" title="{{ __('Horizontal') }}"><i class="fas fa-arrows-left-right"></i><span>{{ __('Horizontal') }}</span></button>
                </div>
                <div class="rp-treebar-btns" role="group" aria-label="tree view">
                    <button type="button" class="rp-tbtn rp-tbtn--icon" @click="zoomOut()" title="{{ __('Zoom out') }}"><i class="fas fa-minus"></i></button>
                    <span class="rp-zoom-val" x-text="Math.round(zoom * 100) + '%'">100%</span>
                    <button type="button" class="rp-tbtn rp-tbtn--icon" @click="zoomIn()" title="{{ __('Zoom in') }}"><i class="fas fa-plus"></i></button>
                    <button type="button" class="rp-tbtn rp-tbtn--icon" @click="fit()" title="{{ __('Fit') }}"><i class="fas fa-compress"></i></button>
                    <button type="button" class="rp-tbtn rp-tbtn--icon" @click="locate()" title="{{ __('Continue') }}"><i class="fas fa-crosshairs"></i></button>
                    <button type="button" class="rp-tbtn rp-tbtn--icon" @click="toggleFs()" :title="fs ? '{{ __('Fit') }}' : '{{ __('Fullscreen') }}'"><i class="fas" :class="fs ? 'fa-compress' : 'fa-expand'"></i></button>
                </div>
            </div>
            {{-- Настоящее дерево пути: корень → этапы → шаги. Данные те же (шаги курса), только вид — ветки. --}}
            <div class="rp-tree-wrap" id="rpTreeWrap">
                <ul class="rp-tree" x-cloak :class="dir === 'h' && 'rp-tree--hoz'" :style="'transform: scale(' + zoom + '); transform-origin: ' + (dir === 'h' ? 'left center' : 'top center') + ';'">
                    <li>
                        <div class="rp-tnode rp-troot">
                            <span class="rp-troot-kicker">{{ __('Path') }} &middot; {{ $percent }}%</span>
                            <b>{{ $roadmap->title }}</b>
                            <small>{{ $completedCount }}/{{ $totalSteps }} {{ __('steps') }} &middot; {{ $totalXp }} XP</small>
                        </div>
                        <ul>
                            @foreach($parentSteps as $parent)
                                @php
                                    $children = $heirSteps->get($parent->id, collect())->sortBy('sort_order')->values();
                                    $parentDone = in_array($parent->id, $completedSteps);
                                    $childrenDone = $children->filter(fn($c) => in_array($c->id, $completedSteps))->count();
                                    $childrenTotal = $children->count();
                                    $stageDone = $parentDone && $childrenDone >= $childrenTotal;
                                    $hasNext = ((int) $parent->id === (int) $nextId) || $children->contains(fn($c) => (int) $c->id === (int) $nextId);
                                    $stageState = $stageDone ? 'done' : ($hasNext ? 'current' : 'todo');
                                @endphp
                                <li>
                                    <a href="{{ route('courses.step', [$course->id, $parent->id]) }}" class="rp-tnode rp-tstage rp-tnode--{{ $stageState }}"
                                       @click.prevent="openNode($event)"
                                       data-title="{{ $parent->title }}"
                                       data-meta="{{ $parent->experience }} XP @if($childrenTotal > 0)· {{ $childrenDone }}/{{ $childrenTotal }} {{ __('sub-steps') }}@endif"
                                       data-status="{{ $stageDone ? __('done') : ($hasNext ? __('Continue') : __('Upcoming')) }}"
                                       data-state="{{ $stageState }}"
                                       data-desc="{{ $excerpt($parent) }}">
                                        <span class="rp-tdot">
                                            @if($stageDone)<i class="fas fa-check"></i>
                                            @elseif($hasNext)<i class="fas fa-play"></i>
                                            @else<span>{{ $loop->iteration }}</span>@endif
                                        </span>
                                        <b>{{ $parent->title }}</b>
                                        <small>{{ $parent->experience }} XP @if($childrenTotal > 0)&middot; {{ $childrenDone }}/{{ $childrenTotal }}@endif</small>
                                        @if($hasNext)<em class="rp-next-tag">{{ __('Continue') }}</em>@endif
                                    </a>
                                    @if($childrenTotal > 0)
                                        <ul>
                                            @foreach($children as $child)
                                                @php
                                                    $childDone = in_array($child->id, $completedSteps);
                                                    $isNext = (int) $child->id === (int) $nextId;
                                                    $childState = $childDone ? 'done' : ($isNext ? 'current' : 'todo');
                                                @endphp
                                                <li>
                                                    <a href="{{ route('courses.step', [$course->id, $child->id]) }}" class="rp-tnode rp-tkid rp-tnode--{{ $childState }}"
                                                       @click.prevent="openNode($event)"
                                                       data-title="{{ $child->title }}"
                                                       data-meta="{{ $child->experience }} XP"
                                                       data-status="{{ $childDone ? __('done') : ($isNext ? __('Continue') : __('Upcoming')) }}"
                                                       data-state="{{ $childState }}"
                                                       data-sub="{{ __('Stage') }}: {{ $parent->title }}"
                                                       data-desc="{{ $excerpt($child) }}">
                                                        <span class="rp-tdot rp-tdot--sm">
                                                            @if($childDone)<i class="fas fa-check"></i>
                                                            @elseif($isNext)<i class="fas fa-play"></i>
                                                            @else<i class="fas fa-circle"></i>@endif
                                                        </span>
                                                        <b>{{ $child->title }}</b>
                                                        <small>{{ $child->experience }} XP</small>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="rp-legend">
                <span><i class="rp-leg rp-leg--done"></i>{{ __('done') }}</span>
                <span><i class="rp-leg rp-leg--current"></i>{{ __('Continue') }}</span>
                <span><i class="rp-leg rp-leg--todo"></i>{{ __('steps') }}</span>
                <span class="rp-legend-hint"><i class="fas fa-keyboard"></i>+ / − / 0 / F</span>
            </div>
            <div id="rpTip" class="rp-tip-float" hidden></div>
            <div class="rp-modal" x-show="modal.open" x-cloak @click.self="modal.open = false" @keydown.escape.window="modal.open = false">
                <div class="rp-dialog" x-transition.scale.95>
                    <button type="button" class="rp-mclose" @click="modal.open = false" aria-label="×"><i class="fas fa-times"></i></button>
                    <div class="rp-scroll">
                    <div class="rp-mkicker"><span class="rp-chip rp-chip-accent" x-text="modal.status"></span><span class="rp-mmeta" x-text="modal.meta"></span></div>
                    <h3 class="rp-mtitle" x-text="modal.title"></h3>
                    <p class="rp-msub" x-show="modal.sub" x-text="modal.sub"></p>
                    <p class="rp-mdesc" x-show="modal.desc" x-text="modal.desc"></p>
                    <div x-show="modal.kids.length">
                        <h4 class="rp-msec">{{ __('sub-steps') }}</h4>
                        <ul class="rp-mkids">
                            <template x-for="k in modal.kids" :key="k.t">
                                <li><i class="rp-leg" :class="'rp-leg--' + k.s"></i><span x-text="k.t"></span></li>
                            </template>
                        </ul>
                    </div>
                    <div class="rp-mfoot">
                        <a :href="modal.href" class="rp-btn rp-btn-primary rp-btn-block"><i class="fas fa-arrow-right"></i>{{ __('Open step') }}</a>
                    </div>
                    </div>
                </div>
            </div>
            </div>
        </main>

        <aside class="rp-side">
            <div class="rp-card">
                <h3>{{ __('Progress') }}</h3>
                <div class="rp-bigpct">{{ $percent }}<small>%</small></div>
                <div class="rp-progress-track"><span style="width:{{ $percent }}%"></span></div>
                <p class="rp-side-sub">{{ $completedCount }}/{{ $totalSteps }} {{ __('steps') }} &middot; {{ $totalXp }} XP</p>
                @if(!$enrolled)
                    <form method="POST" action="{{ route('roadmap.enroll') }}">@csrf<input type="hidden" name="roadmap_id" value="{{ $roadmap->id }}"><button type="submit" class="rp-btn rp-btn-primary rp-btn-block"><i class="fas fa-rocket"></i>{{ __('Start Learning') }}</button></form>
                @elseif($nextStep)
                    <a href="{{ route('courses.step', [$course->id, $nextStep->id]) }}" class="rp-btn rp-btn-primary rp-btn-block"><i class="fas fa-play"></i>{{ __('Continue Learning') }}</a>
                @else
                    <span class="rp-btn rp-btn-done rp-btn-block"><i class="fas fa-check"></i>{{ __('Completed') }}</span>
                @endif
            </div>

            <div class="rp-card">
                <h3>{{ __('Path') }}</h3>
                <ul class="rp-facts">
                    <li><i class="fas fa-flag"></i>{{ __('Stage') }}: {{ $parentSteps->count() }}</li>
                    <li><i class="fas fa-list-ol"></i>{{ __('steps') }}: {{ $totalSteps }}</li>
                    <li><i class="fas fa-bolt"></i>XP: {{ $totalXp }}</li>
                    <li><i class="fas fa-check-circle"></i>{{ __('done') }}: {{ $completedCount }}</li>
                </ul>
            </div>

            <div class="rp-card rp-tip">
                <h3><i class="fas fa-lightbulb"></i>{{ __('How to go') }}</h3>
                <ul>
                    <li>{{ __('Go in order, step by step') }}</li>
                    <li>{{ __('Tests and practice give XP') }}</li>
                    <li>{{ __('Finish the stage to open the next one') }}</li>
                </ul>
            </div>
        </aside>
        @endif
    </div>
</div>

<style>
.rp-page{background:var(--bg);color:var(--text);overflow-x:clip;min-height:100vh}
/* hero — как на Python Developer */
.rp-hero{position:relative;overflow:hidden;isolation:isolate;border-bottom:1px solid var(--border)}
.rp-hero-bg{position:absolute;inset:0;z-index:0;pointer-events:none}
.rp-hero-grid{position:absolute;inset:0;opacity:.5;background-image:linear-gradient(var(--border) 1px,transparent 1px),linear-gradient(90deg,var(--border) 1px,transparent 1px);background-size:44px 44px;mask-image:radial-gradient(ellipse 75% 90% at 20% 10%,black 20%,transparent 70%);-webkit-mask-image:radial-gradient(ellipse 75% 90% at 20% 10%,black 20%,transparent 70%)}
.rp-orb{position:absolute;border-radius:50%;filter:blur(90px)}
.rp-orb-1{width:420px;height:420px;background:var(--accent);opacity:.13;top:-160px;left:-100px}
.rp-orb-2{width:340px;height:340px;background:#8b5cf6;opacity:.10;bottom:-180px;right:-60px}
.rp-hero-inner{position:relative;z-index:1;max-width:1280px;margin:0 auto;padding:32px clamp(16px,4vw,32px) 28px}
.rp-crumb{display:flex;align-items:center;gap:8px;font-size:12.5px;color:var(--text-muted);margin-bottom:14px}
.rp-crumb a{color:var(--text-muted);text-decoration:none;display:inline-flex;align-items:center;gap:6px}
.rp-crumb a:hover{color:var(--accent)}
.rp-crumb-sep{font-size:9px;opacity:.6}
.rp-crumb-cur{color:var(--text);font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.rp-badges{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px}
.rp-chip{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:800;letter-spacing:.06em;padding:5px 12px;border-radius:999px;border:1px solid var(--border);background:var(--card);color:var(--text-secondary)}
.rp-chip-accent{background:var(--accent-glow);border-color:var(--accent-glow-strong);color:var(--accent)}
.rp-chip-green{background:rgba(34,197,94,.12);border-color:rgba(34,197,94,.3);color:#22c55e}
.rp-title{font-size:clamp(28px,4vw,46px);font-weight:900;letter-spacing:-.02em;line-height:1.05;margin:0 0 12px;background:linear-gradient(120deg,var(--text) 30%,var(--accent));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.rp-desc{font-size:14.5px;line-height:1.7;color:var(--text-secondary);max-width:760px;margin:0 0 18px}
.rp-hero-row{display:flex;flex-wrap:wrap;gap:14px;align-items:center}
.rp-progress{flex:1;min-width:220px;max-width:440px}
.rp-progress-top{display:flex;justify-content:space-between;font-size:12.5px;color:var(--text-muted);margin-bottom:6px}
.rp-progress-top b{color:var(--accent)}
.rp-progress-track{height:8px;border-radius:99px;background:var(--bg-secondary);border:1px solid var(--border);overflow:hidden}
.rp-progress-track span{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,var(--accent),var(--accent-hover,var(--accent-2)));transition:width .8s cubic-bezier(.4,0,.2,1)}
.rp-continue{display:inline-flex;align-items:center;gap:8px;padding:12px 22px;border-radius:12px;font-size:14px;font-weight:700;color:#fff;border:none;cursor:pointer;text-decoration:none;background:linear-gradient(135deg,var(--accent),var(--accent-hover,var(--accent-2)));box-shadow:0 6px 22px var(--accent-glow-strong);transition:transform .15s}
.rp-continue:hover{transform:translateY(-2px)}
.rp-continue-done{background:rgba(34,197,94,.16);color:#22c55e;box-shadow:none;cursor:default}
/* body */
.rp-body{max-width:1280px;margin:0 auto;padding:26px clamp(16px,4vw,32px) 90px;display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:26px;align-items:start}
.rp-main{min-width:0}
/* === Дерево: корень → этапы → шаги, линии ветвления через псевдоэлементы === */
.rp-tree-wrap{overflow-x:auto;padding:10px 4px 6px;cursor:grab}
.rp-tree-wrap.rp-panning,.rp-tree-wrap.rp-panning a{cursor:grabbing}
.rp-tree-wrap.rp-panning a.rp-tnode:hover{transform:none}
.rp-tree,.rp-tree ul{display:flex;justify-content:center;margin:0;padding:26px 0 0;list-style:none;position:relative;min-width:max-content}
.rp-tree{margin:0 auto;min-width:100%;width:max-content;padding-bottom:6px}
.rp-tree ul::before{content:'';position:absolute;top:0;left:50%;width:0;height:26px;border-left:2px solid var(--border)}
.rp-tree>li:only-child>ul::before{display:none}
.rp-tree li{position:relative;display:flex;flex-direction:column;align-items:center;padding:26px 14px 0;text-align:center}
.rp-tree li::before,.rp-tree li::after{content:'';position:absolute;top:0;right:50%;width:50%;height:26px;border-top:2px solid var(--border)}
.rp-tree li::after{right:auto;left:50%;border-left:2px solid var(--border)}
.rp-tree li:only-child::before,.rp-tree li:only-child::after{display:none}
.rp-tree li:only-child{padding-top:0}
.rp-tree li:first-child::before,.rp-tree li:last-child::after{border:0 none}
.rp-tree li:first-child::after{border-radius:8px 0 0 0}
.rp-tree li:last-child::before{border-right:2px solid var(--border);border-radius:0 8px 0 0}
/* узлы */
.rp-tnode{display:flex;flex-direction:column;align-items:center;gap:5px;width:212px;padding:15px 14px;border-radius:15px;background:var(--card);border:1px solid var(--border);text-decoration:none;transition:transform .15s,border-color .15s,box-shadow .15s}
a.rp-tnode:hover{transform:translateY(-3px);border-color:var(--accent)}
.rp-tnode b{font-size:13.5px;font-weight:750;color:var(--text);line-height:1.35}
.rp-tnode small{font-size:11px;color:var(--text-muted);font-weight:600}
.rp-tnode--done{border-color:rgba(34,197,94,.4)}
.rp-tnode--done b{color:var(--text-muted);font-weight:600}
.rp-tnode--current{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
.rp-troot{width:280px;padding:18px;background:linear-gradient(160deg,var(--accent-glow),transparent 75%),var(--card);border-color:var(--accent-glow-strong)}
.rp-troot b{font-size:16px;font-weight:800}
.rp-troot-kicker{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:var(--accent)}
.rp-tdot{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;background:var(--bg-secondary);border:2px solid var(--border);color:var(--text-muted)}
.rp-tdot--sm{width:24px;height:24px;font-size:9px}
.rp-tdot--sm i.fa-circle{font-size:6px}
.rp-tnode--done .rp-tdot{background:rgba(34,197,94,.15);border-color:#22c55e;color:#22c55e}
.rp-tnode--current .rp-tdot{border-color:var(--accent);color:var(--accent);box-shadow:0 0 0 4px var(--accent-glow);animation:rpPulse 2s ease-in-out infinite}
.rp-tnode--current .rp-tdot i{font-size:9px;margin-left:2px}
.rp-next-tag{font-style:normal;font-size:9.5px;font-weight:800;background:var(--accent-glow);color:var(--accent);border:1px solid var(--accent-glow-strong);padding:2px 9px;border-radius:99px}
/* легенда */
.rp-legend{display:flex;gap:18px;justify-content:center;flex-wrap:wrap;padding:14px 4px 0;font-size:12px;color:var(--text-muted)}
.rp-legend span{display:inline-flex;align-items:center;gap:7px}
.rp-leg{width:11px;height:11px;border-radius:50%;flex-shrink:0}
.rp-leg--done{background:#22c55e}
.rp-leg--current{background:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
.rp-leg--todo{background:var(--bg-secondary);border:2px solid var(--border)}
/* === Переключатель ориентации === */
.rp-treebar{display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:8px}
.rp-treebar-btns{display:inline-flex;gap:4px;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:4px;align-items:center}
.rp-tbtn{display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:700;padding:8px 14px;border-radius:9px;border:none;background:none;color:var(--text-muted);cursor:pointer;font-family:inherit}
.rp-tbtn:hover{color:var(--text)}
.rp-tbtn.is-active{background:var(--accent-glow);color:var(--accent)}
.rp-tbtn--icon{padding:8px 11px}
.rp-zoom-val{font-size:12px;font-weight:700;color:var(--text-muted);min-width:46px;text-align:center;font-variant-numeric:tabular-nums}
/* === Фуллскрин секции дерева === */
#rpFsWrap:fullscreen{background:var(--bg);width:100%;height:100%;padding:18px clamp(12px,3vw,28px);overflow:hidden;display:flex;flex-direction:column}
#rpFsWrap:fullscreen .rp-tree-wrap{flex:1;min-height:0;border:1px solid var(--border);border-radius:16px;margin-top:4px}
.rp-legend-hint{margin-left:auto}
.rp-legend-hint i{color:var(--accent);margin-right:5px}
/* === Hover-превью ноды === */
.rp-tip-float{position:fixed;z-index:300;max-width:280px;background:var(--card);border:1px solid var(--accent-glow-strong);border-radius:13px;padding:12px 14px;box-shadow:0 14px 40px rgba(0,0,0,.35);pointer-events:none}
.rp-tip-float[hidden]{display:none}
.rp-tip-float b{display:block;font-size:13px;margin-bottom:3px}
.rp-tip-float span{font-size:11px;font-weight:700;color:var(--accent)}
.rp-tip-float p{margin:7px 0 0;font-size:12px;line-height:1.55;color:var(--text-secondary)}
/* === Модалка-превью ноды === */
.rp-modal{position:fixed;inset:0;z-index:250;background:rgba(0,0,0,.65);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;padding:16px}
.rp-modal[x-cloak]{display:none}
.rp-dialog{position:relative;background:var(--card);border:1px solid var(--border);border-radius:18px;width:100%;max-width:520px;max-height:88vh;overflow:hidden;display:flex;flex-direction:column}
.rp-scroll{flex:1 1 auto;min-height:0;overflow-y:auto;overscroll-behavior:contain;padding:22px;scrollbar-width:thin;scrollbar-color:var(--border) transparent}
.rp-scroll::-webkit-scrollbar{width:10px}
.rp-scroll::-webkit-scrollbar-track{background:transparent;margin:8px 0}
.rp-scroll::-webkit-scrollbar-thumb{background:var(--border);border-radius:99px;border:3px solid transparent;background-clip:padding-box}
.rp-scroll::-webkit-scrollbar-thumb:hover{background:var(--text-muted);border:3px solid transparent;background-clip:padding-box}
.rp-mclose{position:absolute;top:12px;right:12px;background:none;border:none;color:var(--text-muted);font-size:16px;cursor:pointer;padding:8px;border-radius:9px}
.rp-mclose:hover{background:var(--bg-secondary);color:var(--text)}
.rp-mkicker{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px}
.rp-mmeta{font-size:12px;font-weight:700;color:var(--text-muted)}
.rp-mtitle{font-size:20px;font-weight:800;margin:0 0 6px;line-height:1.3;padding-right:28px}
.rp-msub{font-size:12.5px;color:var(--accent);font-weight:600;margin:0 0 8px}
.rp-mdesc{font-size:13.5px;line-height:1.65;color:var(--text-secondary);margin:0 0 6px}
.rp-msec{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin:14px 0 8px}
.rp-mkids{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:7px}
.rp-mkids li{display:flex;align-items:center;gap:9px;font-size:13px;font-weight:600;background:var(--bg-secondary);border:1px solid var(--border);border-radius:10px;padding:9px 12px}
.rp-mfoot{margin-top:16px}
/* === Горизонтальное дерево (корень слева, ветки вправо) — тот же markup === */
.rp-tree--hoz,.rp-tree--hoz ul{flex-direction:column;justify-content:center;align-items:stretch;padding:0 0 0 30px}
.rp-tree--hoz{padding-left:0;align-items:flex-start}
.rp-tree--hoz ul{gap:26px}
.rp-tree--hoz ul::before{content:'';position:absolute;left:0;top:50%;width:30px;height:0;border-top:2px solid var(--border);border-left:0}
.rp-tree--hoz>li{padding-left:0}
.rp-tree--hoz>li::before,.rp-tree--hoz>li::after{display:none}
.rp-tree--hoz li{flex-direction:row;align-items:center;padding:0 0 0 30px}
.rp-tree--hoz li::before{left:0;right:auto;top:50%;width:30px;height:0;border-top:2px solid var(--border);border-radius:0}
.rp-tree--hoz li::after{left:0;right:auto;top:0;bottom:0;width:0;height:auto;border-top:0;border-left:2px solid var(--border);border-radius:0}
.rp-tree--hoz li:first-child::before,.rp-tree--hoz li:last-child::before{border-radius:0}
.rp-tree--hoz li:first-child::after,.rp-tree--hoz li:last-child::after{border-radius:0}
.rp-tree--hoz li:first-child::after{top:50%}
.rp-tree--hoz li:last-child::after{bottom:50%}
.rp-tree--hoz li:only-child::after{display:none}
.rp-tree--hoz ul li:only-child::before{display:block}
/* сайдбар */
.rp-side{position:sticky;top:88px;display:flex;flex-direction:column;gap:16px}
.rp-card{background:var(--card);border:1px solid var(--border);border-radius:18px;padding:20px 22px}
.rp-card h3{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin:0 0 12px}
.rp-card h3 i{color:var(--accent);margin-right:6px}
.rp-bigpct{font-size:40px;font-weight:900;letter-spacing:-.02em}
.rp-bigpct small{font-size:16px;color:var(--text-muted);font-weight:700}
.rp-side-sub{font-size:12px;color:var(--text-muted);margin:8px 0 14px}
.rp-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 20px;border-radius:12px;font-size:13.5px;font-weight:700;text-decoration:none;border:1px solid transparent;cursor:pointer;transition:transform .15s}
.rp-btn:active{transform:scale(.97)}
.rp-btn-primary{background:linear-gradient(135deg,var(--accent),var(--accent-hover,var(--accent-2)));color:#fff;box-shadow:0 6px 22px var(--accent-glow-strong)}
.rp-btn-primary:hover{transform:translateY(-2px)}
.rp-btn-done{background:rgba(34,197,94,.14);border-color:rgba(34,197,94,.35);color:#22c55e;cursor:default}
.rp-btn-block{width:100%}
.rp-facts{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:9px;font-size:13px;color:var(--text-secondary)}
.rp-facts i{color:var(--accent);width:18px;text-align:center;margin-right:8px}
.rp-tip{background:linear-gradient(160deg,var(--accent-glow),transparent 70%),var(--card)}
.rp-tip ul{margin:0;padding-left:1.1em;font-size:12.5px;color:var(--text-secondary);line-height:1.65}
.rp-tip li{margin:4px 0}
.rp-tip li::marker{color:var(--accent)}
/* пусто */
.rp-empty{grid-column:1/-1;display:flex;flex-direction:column;align-items:center;gap:10px;padding:70px 20px;text-align:center}
.rp-empty-icon{width:80px;height:80px;border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:32px;background:var(--bg-secondary);border:1px solid var(--border);color:var(--text-muted);margin-bottom:6px}
.rp-empty-title{font-size:24px;font-weight:800;margin:0}
.rp-empty-desc{font-size:14px;color:var(--text-muted);margin:0 0 10px}
@@media(max-width:1024px){.rp-body{grid-template-columns:1fr}.rp-side{position:static}}
@@media(max-width:640px){.rp-continue{width:100%;justify-content:center}.rp-tnode{width:180px}.rp-troot{width:230px}}
@@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important}}
</style>

<script>
function rpTree() {
    return {
        dir: localStorage.getItem('rp-tree-dir') || 'v',
        zoom: 1,
        fs: false,
        modal: { open: false, title: '', meta: '', sub: '', status: '', desc: '', href: '#', kids: [] },
        init() {
            document.addEventListener('fullscreenchange', () => { this.fs = !!document.fullscreenElement; });
            this.initPan();
            this.initTip();
            window.addEventListener('keydown', (e) => {
                const t = e.target;
                if (t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.tagName === 'SELECT' || t.isContentEditable)) return;
                if (e.key === '+' || e.key === '=') this.zoomIn();
                else if (e.key === '-' || e.key === '_') this.zoomOut();
                else if (e.key === '0') this.zoom = 1;
                else if (e.key === 'f' || e.key === 'F' || e.key === 'а' || e.key === 'А') this.toggleFs();
            });
        },
        zoomIn() { this.zoom = Math.min(2, +(this.zoom + 0.15).toFixed(2)); },
        zoomOut() { this.zoom = Math.max(0.3, +(this.zoom - 0.15).toFixed(2)); },
        fit() {
            const wrap = document.getElementById('rpTreeWrap');
            const ul = wrap ? wrap.querySelector('.rp-tree') : null;
            if (!wrap || !ul) return;
            const w = ul.scrollWidth || 1;
            this.zoom = Math.min(1, Math.max(0.3, wrap.clientWidth / w));
            wrap.scrollLeft = 0;
            wrap.scrollTop = 0;
        },
        locate() {
            const el = document.querySelector('#rpTreeWrap .rp-tnode--current');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
        },
        toggleFs() {
            const w = document.getElementById('rpFsWrap');
            if (document.fullscreenElement) { document.exitFullscreen(); }
            else if (w && w.requestFullscreen) { w.requestFullscreen(); }
        },
        openNode(e) {
            const el = e.currentTarget;
            const kids = [];
            const li = el.closest('li');
            if (li) {
                li.querySelectorAll(':scope > ul .rp-tnode').forEach(k => {
                    kids.push({ t: k.dataset.title || '', s: k.dataset.state || 'todo' });
                });
            }
            this.modal = {
                open: true,
                title: el.dataset.title || '',
                meta: el.dataset.meta || '',
                sub: el.dataset.sub || '',
                status: el.dataset.status || '',
                desc: el.dataset.desc || '',
                href: el.getAttribute('href') || '#',
                kids: kids
            };
        },
        /* --- drag-pan дерева --- */
        initPan() {
            const wrap = document.getElementById('rpTreeWrap');
            if (!wrap) return;
            let pan = null;
            const esc = (s) => String(s == null ? '' : s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
            wrap.addEventListener('mousedown', (e) => {
                if (e.button !== 0) return;
                pan = { x: e.clientX, y: e.clientY, sl: wrap.scrollLeft, st: wrap.scrollTop, moved: false };
                wrap.dataset.dragged = '0';
            });
            window.addEventListener('mousemove', (e) => {
                if (!pan) return;
                const dx = e.clientX - pan.x, dy = e.clientY - pan.y;
                if (Math.abs(dx) + Math.abs(dy) > 5) {
                    pan.moved = true;
                    wrap.dataset.dragged = '1';
                    wrap.classList.add('rp-panning');
                    this.hideTip();
                }
                if (pan.moved) { wrap.scrollLeft = pan.sl - dx; wrap.scrollTop = pan.st - dy; }
            });
            window.addEventListener('mouseup', () => {
                pan = null;
                wrap.classList.remove('rp-panning');
            });
            // Клик после перетаскивания — не открывать модалку и не переходить
            wrap.addEventListener('click', (e) => {
                if (wrap.dataset.dragged === '1') {
                    e.preventDefault();
                    e.stopPropagation();
                    wrap.dataset.dragged = '0';
                }
            }, true);
            this._esc = esc;
        },
        /* --- hover-превью нод --- */
        initTip() {
            const wrap = document.getElementById('rpTreeWrap');
            const tip = document.getElementById('rpTip');
            if (!wrap || !tip) return;
            let tipFor = null;
            const move = (e) => {
                const pad = 14;
                let x = e.clientX + pad, y = e.clientY + pad;
                const r = tip.getBoundingClientRect();
                if (x + r.width > window.innerWidth - 8) x = e.clientX - r.width - pad;
                if (y + r.height > window.innerHeight - 8) y = e.clientY - r.height - pad;
                tip.style.left = Math.max(8, x) + 'px';
                tip.style.top = Math.max(8, y) + 'px';
            };
            wrap.addEventListener('mouseover', (e) => {
                const n = e.target.closest('.rp-tnode');
                if (!n || !wrap.contains(n)) { this.hideTip(); tipFor = null; return; }
                tipFor = n;
                const esc = this._esc || ((s) => s);
                tip.innerHTML = '<b>' + esc(n.dataset.title) + '</b>'
                    + '<span>' + esc(n.dataset.meta) + ' · ' + esc(n.dataset.status) + '</span>'
                    + (n.dataset.desc ? '<p>' + esc(n.dataset.desc.slice(0, 140)) + '</p>' : '');
                tip.hidden = false;
                move(e);
            });
            wrap.addEventListener('mousemove', (e) => { if (tipFor) move(e); });
            wrap.addEventListener('mouseout', (e) => {
                if (tipFor && !tipFor.contains(e.relatedTarget)) { this.hideTip(); tipFor = null; }
            });
            wrap.addEventListener('scroll', () => { this.hideTip(); tipFor = null; }, { passive: true });
        },
        hideTip() {
            const tip = document.getElementById('rpTip');
            if (tip) tip.hidden = true;
        }
    };
}
</script>
@endif
@endsection
