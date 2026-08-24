@extends('layouts.app')
@section('title', __('Problems') . ' - CodeMaster')

@section('head')
<style>
.prob-page{font-family:'Space Grotesk',system-ui,sans-serif}
.prob-stat-bar{display:flex;gap:2px;height:6px;border-radius:3px;overflow:hidden;margin-top:8px}
.prob-stat-seg{height:100%;border-radius:2px;transition:width .4s}
.prob-filter-btn{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:8px;font-size:11px;font-weight:700;border:1.5px solid var(--border);background:var(--card);color:var(--text-muted);cursor:pointer;transition:all .2s;white-space:nowrap}
.prob-filter-btn:hover{border-color:var(--accent);color:var(--accent)}
.prob-filter-btn.active{color:#fff;border-color:transparent}
.prob-filter-btn.active.f-all{background:#111827}
.prob-filter-btn.active.f-easy{background:#22c55e}
.prob-filter-btn.active.f-med{background:#eab308}
.prob-filter-btn.active.f-hard{background:#ef4444}
.prob-filter-btn.active.f-solved{background:#22c55e}
.prob-filter-btn.active.f-attempted{background:#eab308}
.prob-filter-btn.active.f-unsolved{background:#6b7280}
.prob-topic-link{display:flex;align-items:center;justify-content:space-between;padding:6px 10px;border-radius:8px;font-size:12px;font-weight:600;color:var(--text-muted);transition:all .15s;cursor:pointer;text-decoration:none}
.prob-topic-link:hover{background:var(--bg-2);color:var(--text)}
.prob-topic-link.active{background:color-mix(in srgb,var(--accent) 8%,var(--card));color:var(--accent)}
.prob-topic-link .cnt{font-size:10px;color:var(--text-muted);opacity:.6}
.prob-topic-link.active .cnt{opacity:1}
.prob-table{width:100%;border-collapse:separate;border-spacing:0}
.prob-table thead th{padding:10px 16px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);text-align:left;border-bottom:1.5px solid var(--border);background:var(--bg-2)}
.prob-table tbody tr{transition:all .15s;cursor:pointer}
.prob-table tbody tr:hover{background:color-mix(in srgb,var(--accent) 3%,var(--card))}
.prob-table tbody tr:active{transform:scale(.998)}
.prob-table tbody td{padding:12px 16px;border-bottom:1px solid color-mix(in srgb,var(--border) 50%,transparent);vertical-align:middle}
.prob-id{font-family:'Courier New',monospace;font-size:12px;font-weight:700;color:var(--text-muted);min-width:28px}
.prob-status-icon{width:22px;height:22px;display:flex;align-items:center;justify-content:center}
.prob-title-cell{font-size:13px;font-weight:700;color:var(--text);transition:color .15s}
.prob-table tbody tr:hover .prob-title-cell{color:var(--accent)}
.prob-diff-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:6px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.03em}
.prob-diff-badge.easy{background:rgba(34,197,94,.08);color:#16a34a}
.prob-diff-badge.medium{background:rgba(234,179,8,.08);color:#ca8a04}
.prob-diff-badge.hard{background:rgba(239,68,68,.08);color:#dc2626}
.prob-accept{font-size:12px;color:var(--text-muted);font-weight:600}
.prob-accept span{color:var(--text-secondary)}
.prob-topic-tag{display:inline-flex;padding:2px 7px;border-radius:5px;font-size:9px;font-weight:700;background:color-mix(in srgb,var(--accent) 6%,var(--card));color:var(--accent);letter-spacing:.02em;white-space:nowrap}
.prob-topic-more{display:inline-flex;padding:2px 6px;border-radius:5px;font-size:9px;font-weight:700;background:var(--bg-2);color:var(--text-muted)}
.prob-header-bar{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-shrink:0}
.prob-search{position:relative;flex-shrink:0}
.prob-search input{width:240px;padding:8px 12px 8px 36px;border-radius:10px;border:1.5px solid var(--border);background:var(--card);color:var(--text);font-size:12px;font-weight:500;outline:0;transition:border-color .2s}
.prob-search input:focus{border-color:var(--accent)}
.prob-search input::placeholder{color:var(--text-muted)}
.prob-search i{position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:12px;color:var(--text-muted);pointer-events:none}
.prob-sidebar-section{margin-bottom:16px}
.prob-sidebar-label{font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);margin-bottom:8px;padding-left:2px}
.prob-empty{text-align:center;padding:60px 20px;color:var(--text-muted)}
.prob-empty i{font-size:48px;margin-bottom:12px;display:block;opacity:.3}
.prob-empty p{font-size:13px}
.prob-clear{display:flex;align-items:center;justify-content:center;gap:4px;padding:8px;border-radius:8px;font-size:11px;font-weight:600;color:var(--text-muted);transition:all .15s;text-decoration:none}
.prob-clear:hover{background:var(--bg-2);color:var(--text)}
.prob-stats-num{display:flex;align-items:baseline;gap:6px}
.prob-stats-num .val{font-size:22px;font-weight:900;color:var(--text);line-height:1}
.prob-stats-num .lbl{font-size:10px;font-weight:600;color:var(--text-muted);text-transform:uppercase}
.prob-progress-ring{position:relative;width:64px;height:64px;flex-shrink:0}
.prob-progress-ring svg{transform:rotate(-90deg)}
.prob-progress-ring .bg{fill:none;stroke:var(--border);stroke-width:5}
.prob-progress-ring .fg{fill:none;stroke-width:5;stroke-linecap:round;transition:stroke-dashoffset .6s ease}
.prob-progress-text{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:900;color:var(--text)}
</style>
@endsection

@section('content')
<div class="prob-page max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-6">

        <div class="w-full lg:w-72 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-24">

                @if(Auth::check())
                <div class="flex items-center gap-4 mb-5 pb-5 border-b border-gray-100">
                    @php
                        $solved = $stats['solved'] ?? 0;
                        $total = max($stats['total'], 1);
                        $pct = round(($solved / $total) * 100);
                        $circ = 2 * pi() * 27;
                        $offset = $circ - ($pct / 100) * $circ;
                    @endphp
                    <div class="prob-progress-ring">
                        <svg width="64" height="64" viewBox="0 0 64 64">
                            <circle class="bg" cx="32" cy="32" r="27"/>
                            <circle class="fg" cx="32" cy="32" r="27"
                                    stroke="url(#ringGrad)"
                                    stroke-dasharray="{{ $circ }}"
                                    stroke-dashoffset="{{ $offset }}"/>
                            <defs><linearGradient id="ringGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#6366f1"/>
                                <stop offset="100%" stop-color="#8b5cf6"/>
                            </linearGradient></defs>
                        </svg>
                        <div class="prob-progress-text">{{ $pct }}%</div>
                    </div>
                    <div>
                        <div class="prob-stats-num">
                            <span class="val">{{ $solved }}</span>
                            <span class="lbl">/ {{ $stats['total'] }}</span>
                        </div>
                        <div style="font-size:10px;color:var(--text-muted);margin-top:2px">{{ __('problems solved') }}</div>
                    </div>
                </div>
                @endif

                <div class="prob-sidebar-section">
                    <div class="prob-sidebar-label"><i class="fas fa-signal mr-1"></i>{{ __('Statistics') }}</div>
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center text-xs">
                            <span style="color:var(--text-muted)">{{ __('All') }}</span>
                            <span class="font-bold" style="color:var(--text)">{{ $stats['total'] }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-green-600"><i class="fas fa-circle text-[5px] mr-1.5 align-middle"></i>{{ __('Easy') }}</span>
                            <span class="font-bold text-green-600">{{ $stats['easy'] }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-yellow-500"><i class="fas fa-circle text-[5px] mr-1.5 align-middle"></i>{{ __('Medium') }}</span>
                            <span class="font-bold text-yellow-500">{{ $stats['medium'] }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-red-500"><i class="fas fa-circle text-[5px] mr-1.5 align-middle"></i>{{ __('Hard') }}</span>
                            <span class="font-bold text-red-500">{{ $stats['hard'] }}</span>
                        </div>
                    </div>
                    @php
                        $wE = $stats['total'] > 0 ? ($stats['easy'] / $stats['total']) * 100 : 0;
                        $wM = $stats['total'] > 0 ? ($stats['medium'] / $stats['total']) * 100 : 0;
                        $wH = $stats['total'] > 0 ? ($stats['hard'] / $stats['total']) * 100 : 0;
                    @endphp
                    <div class="prob-stat-bar">
                        <div class="prob-stat-seg" style="width:{{ $wE }}%;background:#22c55e"></div>
                        <div class="prob-stat-seg" style="width:{{ $wM }}%;background:#eab308"></div>
                        <div class="prob-stat-seg" style="width:{{ $wH }}%;background:#ef4444"></div>
                    </div>
                </div>

                <div class="prob-sidebar-section">
                    <div class="prob-sidebar-label"><i class="fas fa-sliders-h mr-1"></i>{{ __('Difficulty') }}</div>
                    <div class="flex flex-wrap gap-1.5">
                        <a href="{{ request()->fullUrlWithQuery(['difficulty' => null]) }}"
                           class="prob-filter-btn {{ !request('difficulty') ? 'active f-all' : '' }}">
                            {{ __('All') }}
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['difficulty' => 'easy']) }}"
                           class="prob-filter-btn {{ request('difficulty') === 'easy' ? 'active f-easy' : '' }}">
                            <i class="fas fa-circle text-[5px]"></i>{{ __('Easy') }}
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['difficulty' => 'medium']) }}"
                           class="prob-filter-btn {{ request('difficulty') === 'medium' ? 'active f-med' : '' }}">
                            <i class="fas fa-circle text-[5px]"></i>{{ __('Medium') }}
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['difficulty' => 'hard']) }}"
                           class="prob-filter-btn {{ request('difficulty') === 'hard' ? 'active f-hard' : '' }}">
                            <i class="fas fa-circle text-[5px]"></i>{{ __('Hard') }}
                        </a>
                    </div>
                </div>

                @if(Auth::check())
                <div class="prob-sidebar-section">
                    <div class="prob-sidebar-label"><i class="fas fa-check-double mr-1"></i>{{ __('Status') }}</div>
                    <div class="flex flex-wrap gap-1.5">
                        <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}"
                           class="prob-filter-btn {{ !request('status') ? 'active f-all' : '' }}">
                            {{ __('All') }}
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'solved']) }}"
                           class="prob-filter-btn {{ request('status') === 'solved' ? 'active f-solved' : '' }}">
                            <i class="fas fa-check"></i>{{ __('Solved') }}
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'attempted']) }}"
                           class="prob-filter-btn {{ request('status') === 'attempted' ? 'active f-attempted' : '' }}">
                            <i class="fas fa-pen"></i>{{ __('Attempted') }}
                        </a>
                    </div>
                </div>
                @endif

                <div class="prob-sidebar-section">
                    <div class="prob-sidebar-label"><i class="fas fa-tags mr-1"></i>{{ __('Topics') }}</div>
                    <div class="max-h-56 overflow-y-auto space-y-0.5 pr-1" style="scrollbar-width:thin">
                        <a href="{{ request()->fullUrlWithQuery(['topic' => null]) }}"
                           class="prob-topic-link {{ !request('topic') ? 'active' : '' }}">
                            <span><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>{{ __('All Topics') }}</span>
                        </a>
                        @foreach($topics as $topic)
                        <a href="{{ request()->fullUrlWithQuery(['topic' => $topic->slug]) }}"
                           class="prob-topic-link {{ request('topic') === $topic->slug ? 'active' : '' }}">
                             <span>{{ __('topic_' . $topic->slug) }}</span>
                            <span class="cnt">{{ $topic->problems_count }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('problems.index') }}" class="prob-clear">
                    <i class="fas fa-times-circle"></i>{{ __('Clear all filters') }}
                </a>
            </div>
        </div>

        <div class="flex-1 min-w-0">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="prob-header-bar">
                    <div class="flex items-center gap-3">
                        <h1 class="text-lg font-extrabold" style="color:var(--text)">
                            <i class="fas fa-fire mr-2 text-orange-500"></i>{{ __('Problem Set') }}
                        </h1>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold" style="background:color-mix(in srgb,var(--accent) 8%,var(--card));color:var(--accent)">{{ $stats['total'] }}</span>
                    </div>
                    <div class="prob-search">
                        <i class="fas fa-search"></i>
                        <form method="GET">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search...') }}">
                            @if(request('difficulty')) <input type="hidden" name="difficulty" value="{{ request('difficulty') }}"> @endif
                            @if(request('topic')) <input type="hidden" name="topic" value="{{ request('topic') }}"> @endif
                            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="prob-table">
                        <thead>
                            <tr>
                                <th style="width:50px">#</th>
                                <th style="width:40px"></th>
                                <th>{{ __('Title') }}</th>
                                <th style="width:100px">{{ __('Difficulty') }}</th>
                                <th style="width:90px" class="hidden sm:table-cell">{{ __('Acceptance') }}</th>
                                <th class="hidden md:table-cell">{{ __('Topics') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($problems as $problem)
                            @php
                                $isSolved = Auth::check() && $problem->isSolvedBy(Auth::user());
                                $isAttempted = Auth::check() && !$isSolved && $problem->isAttemptedBy(Auth::user());
                            @endphp
                            <tr onclick="window.location='{{ route('problems.show', $problem->slug) }}'">
                                <td><span class="prob-id">{{ $problem->id }}</span></td>
                                <td>
                                    <div class="prob-status-icon">
                                        @if($isSolved)
                                            <i class="fas fa-check-circle text-green-500 text-base"></i>
                                        @elseif($isAttempted)
                                            <i class="fas fa-minus-circle text-yellow-500 text-base"></i>
                                        @else
                                            <span class="w-[18px] h-[18px] block border-[2px] rounded-full" style="border-color:var(--border)"></span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="prob-title-cell">{{ $problem->id }}. {{ $problem->title }}</span>
                                </td>
                                <td>
                                    <span class="prob-diff-badge {{ $problem->difficulty }}">
                                        {{ __('difficulty_' . $problem->difficulty) }}
                                    </span>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="prob-accept"><span>{{ $problem->acceptance_rate }}%</span></span>
                                </td>
                                <td class="hidden md:table-cell">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($problem->topics->take(3) as $topic)
                                            <span class="prob-topic-tag">{{ __('topic_' . $topic->slug) }}</span>
                                        @endforeach
                                        @if($problem->topics->count() > 3)
                                            <span class="prob-topic-more">+{{ $problem->topics->count() - 3 }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    <div class="prob-empty">
                                        <i class="fas fa-code"></i>
                                        <p>{{ __('No problems found') }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($problems->hasPages())
                <div class="px-4 py-3 border-t" style="border-color:var(--border)">
                    {{ $problems->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
