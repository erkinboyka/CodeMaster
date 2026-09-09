@extends('layouts.app')
@section('title', $list->title . ' - ' . __('ml_my_lists'))

@section('content')
<style>
    .ml-layout{display:flex;min-height:calc(100vh - 60px);background:var(--bg);position:relative}
    .ml-layout::before{content:'';position:absolute;inset:0;pointer-events:none;
        background:radial-gradient(ellipse 40% 30% at 10% 0%, var(--accent-glow) 0%, transparent 60%),
        radial-gradient(ellipse 30% 30% at 95% 90%, rgba(139,92,246,.07) 0%, transparent 60%)}
    .ml-sidebar{width:280px;background:color-mix(in srgb, var(--card) 75%, transparent);
        backdrop-filter:blur(16px);border-right:1px solid var(--border);
        padding:22px 12px;flex-shrink:0;overflow-y:auto;position:sticky;top:60px;height:calc(100vh - 60px);z-index:2}
    .ml-sidebar-head{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.12em;
        color:var(--text-muted);padding:0 12px;margin-bottom:10px;display:flex;align-items:center;justify-content:space-between}
    .ml-sidebar-item{display:flex;align-items:center;gap:12px;padding:10px 12px;cursor:pointer;text-decoration:none;
        color:var(--text);font-size:13.5px;font-weight:600;transition:all .18s;
        border-left:3px solid transparent;border-radius:0 12px 12px 0;margin-bottom:2px}
    .ml-sidebar-item:hover{background:var(--bg-secondary);transform:translateX(2px)}
    .ml-sidebar-item.active{background:var(--accent-glow);border-left-color:var(--accent);color:var(--accent)}
    .ml-sidebar-item .ico{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;
        font-size:14px;flex-shrink:0;background:var(--bg-secondary);border:1px solid var(--border)}
    .ml-sidebar-item .cnt{margin-left:auto;font-size:11px;font-weight:800;font-family:var(--font-mono);
        color:var(--text-muted);background:var(--bg-secondary);border:1px solid var(--border);
        border-radius:8px;padding:2px 8px;min-width:30px;text-align:center}
    .ml-sidebar-item.active .cnt{color:var(--accent);border-color:var(--accent-glow-strong)}
    .ml-star{color:#f59e0b}
    .ml-new-btn{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:8px;
        background:transparent;border:1px dashed var(--border-hover);color:var(--text-muted);
        font-size:11px;font-weight:700;cursor:pointer;transition:all .2s}
    .ml-new-btn:hover{border-color:var(--accent);color:var(--accent);border-style:solid}
    .ml-main{flex:1;overflow-y:auto;padding-bottom:48px;z-index:1;min-width:0}

    .ml-hero{margin:26px 32px 0;border-radius:22px;border:1px solid var(--border);
        background:var(--card);padding:30px 32px;display:flex;gap:36px;align-items:center;
        position:relative;overflow:hidden;box-shadow:0 18px 50px rgba(0,0,0,.1);
        opacity:0;transform:translateY(20px);animation:mlIn .6s cubic-bezier(.16,1,.3,1) forwards}
    @@keyframes mlIn{to{opacity:1;transform:none}}
    .ml-hero::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;
        background:linear-gradient(90deg,var(--accent),#8b5cf6,#38bdf8)}
    .ml-hero::after{content:'';position:absolute;width:340px;height:340px;border-radius:50%;right:-110px;top:-110px;
        background:radial-gradient(circle, var(--accent-glow) 0%, transparent 70%);pointer-events:none}
    .ml-hero-left{flex-shrink:0;text-align:center;width:210px;position:relative;z-index:1}
    .ml-icon-box{width:76px;height:76px;border-radius:20px;display:flex;align-items:center;justify-content:center;
        font-size:30px;margin:0 auto 14px;box-shadow:0 12px 30px rgba(0,0,0,.2);
        animation:mlFloat 5s ease-in-out infinite}
    @@keyframes mlFloat{0%,100%{transform:translateY(0) rotate(-3deg)}50%{transform:translateY(-7px) rotate(3deg)}}
    .ml-title{font-size:21px;font-weight:900;color:var(--text);line-height:1.25;letter-spacing:-.3px;overflow-wrap:anywhere}
    .ml-author{font-size:12.5px;color:var(--text-muted);margin-top:5px}
    .ml-btns{display:flex;gap:7px;justify-content:center;margin-top:16px;flex-wrap:wrap}
    .ml-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:11px;font-size:12px;
        font-weight:700;border:1px solid var(--border);background:var(--bg-secondary);color:var(--text);
        cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap}
    .ml-btn:hover{border-color:var(--accent);color:var(--accent);transform:translateY(-1px)}
    .ml-btn.primary{background:linear-gradient(135deg,var(--accent),#8b5cf6);color:#fff;border-color:transparent;
        box-shadow:0 6px 18px var(--accent-glow-strong)}
    .ml-btn.primary:hover{color:#fff;transform:translateY(-2px)}
    .ml-btn.danger:hover{border-color:#ef4444;color:#ef4444}
    .ml-progress-section{flex:1;min-width:0;position:relative;z-index:1}
    .ml-progress-card{border-radius:18px;border:1px solid var(--border);background:var(--bg-secondary);padding:22px}
    .ml-progress-card .p-title{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;
        margin-bottom:16px;color:var(--text-muted)}
    .ml-ring-wrap{position:relative;width:140px;height:140px;margin:0 auto}
    .ml-ring-wrap svg{transform:rotate(-90deg)}
    .ml-ring-wrap .bg{stroke:var(--border)}
    .ml-ring-wrap .fg{transition:stroke-dashoffset 1.4s cubic-bezier(.16,1,.3,1)}
    .ml-ring-center{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center}
    .ml-ring-center .big{font-size:23px;font-weight:900;color:var(--text);font-family:var(--font-mono)}
    .ml-ring-center .sub{font-size:10px;color:var(--text-muted);margin-top:3px;text-transform:uppercase;
        letter-spacing:1.2px;font-weight:700}
    .ml-stat-row{display:flex;justify-content:space-around;margin-top:16px;padding-top:14px;border-top:1px solid var(--border)}
    .ml-stat{text-align:center}
    .ml-stat .val{font-size:17px;font-weight:800;font-family:var(--font-mono)}
    .ml-stat .lbl{font-size:9.5px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-top:3px;font-weight:700}

    .ml-content{padding:22px 32px}
    .ml-toolbar{display:flex;align-items:center;gap:8px;margin-bottom:16px;flex-wrap:wrap;
        opacity:0;transform:translateY(16px);animation:mlIn .6s .15s cubic-bezier(.16,1,.3,1) forwards}
    .ml-search-wrap{position:relative}
    .ml-search-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:12px;color:var(--text-muted)}
    .ml-search{width:250px;padding:9px 14px 9px 36px;border-radius:11px;border:1px solid var(--border);
        background:var(--card);color:var(--text);font-size:13px;outline:none;transition:all .2s}
    .ml-search:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
    .ml-filter-btn{padding:8px 16px;border-radius:100px;border:1px solid var(--border);background:var(--card);
        color:var(--text-muted);font-size:12px;font-weight:700;cursor:pointer;transition:all .2s}
    .ml-filter-btn:hover{border-color:var(--accent);color:var(--accent);transform:translateY(-1px)}
    .ml-filter-btn.active{background:linear-gradient(135deg,var(--accent),#8b5cf6);border-color:transparent;color:#fff;
        box-shadow:0 4px 14px var(--accent-glow-strong)}
    .ml-table-card{border-radius:18px;overflow:hidden;border:1px solid var(--border);background:var(--card);
        box-shadow:0 14px 40px rgba(0,0,0,.08);
        opacity:0;transform:translateY(18px);animation:mlIn .6s .25s cubic-bezier(.16,1,.3,1) forwards}
    .ml-table{width:100%;border-collapse:collapse}
    .ml-table th{text-align:left;padding:12px 16px;font-size:10.5px;font-weight:800;color:var(--text-muted);
        text-transform:uppercase;letter-spacing:.08em;background:var(--bg-secondary);border-bottom:1px solid var(--border);
        font-family:var(--font-mono)}
    .ml-table td{font-size:14px;border-bottom:1px solid var(--border);transition:background .12s}
    .ml-table tbody tr:last-child td{border-bottom:none}
    .ml-table tbody tr{cursor:pointer;position:relative}
    .ml-table tbody tr:hover td{background:color-mix(in srgb, var(--accent) 4%, transparent)}
    .ml-table tbody tr:hover .title-cell{color:var(--accent)}
    .ml-table .num{width:52px;color:var(--text-muted);font-size:12.5px;font-family:var(--font-mono);padding:13px 16px}
    .ml-table .status-col{width:44px;text-align:center;padding:13px 10px}
    .ml-table .title-cell{padding:13px 16px;font-weight:600;color:var(--text);transition:color .15s}
    .ml-table .diff-cell{width:100px;padding:13px 16px}
    .ml-table .actions-cell{width:48px;padding:13px 10px;text-align:center}
    .ml-status-dot{width:20px;height:20px;border-radius:50%;border:2px solid var(--border-hover);
        display:inline-flex;align-items:center;justify-content:center;transition:all .2s;vertical-align:middle}
    .ml-status-dot.solved{border-color:#22c55e;background:linear-gradient(135deg,#22c55e,#16a34a);
        box-shadow:0 0 10px rgba(34,197,94,.5)}
    .ml-status-dot.attempted{border-color:#f59e0b;background:rgba(245,158,11,.25)}
    .diff-pill{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;
        padding:4px 10px;border-radius:7px;font-family:var(--font-mono)}
    .diff-easy{background:rgba(34,197,94,.12);color:#22c55e}
    .diff-medium{background:rgba(234,179,8,.12);color:#eab308}
    .diff-hard{background:rgba(239,68,68,.12);color:#ef4444}
    .ml-empty-table{text-align:center;padding:70px 20px;color:var(--text-muted)}
    .ml-empty-table i{font-size:44px;opacity:.15;margin-bottom:14px;display:block}
    .ml-remove-btn{background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:12px;
        padding:6px 8px;border-radius:8px;transition:all .15s}
    .ml-remove-btn:hover{color:#ef4444;background:rgba(239,68,68,.1);transform:scale(1.1)}

    .ml-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;display:flex;
        align-items:center;justify-content:center;backdrop-filter:blur(6px);animation:mlFadeIn .15s ease}
    @@keyframes mlFadeIn{from{opacity:0}to{opacity:1}}
    .ml-modal{background:var(--card);border:1px solid var(--border);border-radius:20px;width:440px;max-width:90vw;
        padding:0;box-shadow:0 24px 80px rgba(0,0,0,.4);animation:mlSlideUp .22s cubic-bezier(.16,1,.3,1);overflow:hidden}
    @@keyframes mlSlideUp{from{transform:translateY(14px) scale(.97);opacity:0}to{transform:none;opacity:1}}
    .ml-modal-head{padding:20px 24px 0;display:flex;align-items:center;justify-content:space-between}
    .ml-modal-head h3{font-size:17px;font-weight:800;color:var(--text);margin:0;letter-spacing:-.3px}
    .ml-modal-close{width:32px;height:32px;border-radius:9px;border:none;background:var(--bg-secondary);
        color:var(--text-muted);font-size:13px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s}
    .ml-modal-close:hover{background:var(--accent-glow);color:var(--accent)}
    .ml-modal-body{padding:16px 24px 8px}
    .ml-modal-body p{font-size:12.5px;color:var(--text-muted);margin:0 0 12px}
    .ml-modal input[type=text]{width:100%;box-sizing:border-box;padding:11px 15px;border-radius:12px;
        border:1px solid var(--border);background:var(--bg-secondary);color:var(--text);font-size:14px;
        outline:none;transition:all .2s}
    .ml-modal input[type=text]:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
    .ml-modal-foot{padding:14px 24px 20px;display:flex;justify-content:flex-end;gap:8px}
    .ml-modal-foot button{padding:9px 20px;border-radius:11px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:all .2s}
    .ml-modal-cancel{background:var(--bg-secondary);color:var(--text);border:1px solid var(--border)}
    .ml-modal-cancel:hover{border-color:var(--accent)}
    .ml-modal-submit{background:linear-gradient(135deg,var(--accent),#8b5cf6);color:#fff;
        box-shadow:0 4px 14px var(--accent-glow-strong)}
    .ml-modal-submit:hover{transform:translateY(-1px)}
    .ml-modal-submit:disabled{opacity:.4;cursor:not-allowed;transform:none}
    .ml-add-btn{display:inline-flex;align-items:center;gap:7px;padding:11px 24px;border-radius:12px;
        background:linear-gradient(135deg,var(--accent),#8b5cf6);color:#fff;font-size:13px;font-weight:800;
        border:none;cursor:pointer;transition:all .2s;box-shadow:0 6px 20px var(--accent-glow-strong)}
    .ml-add-btn:hover{transform:translateY(-2px)}
    .ml-add-item{display:flex;align-items:center;padding:10px 14px;border-bottom:1px solid var(--border);
        cursor:pointer;transition:background .12s}
    .ml-add-item:hover{background:var(--bg-secondary)}
    .ml-add-item input{margin-right:12px;width:16px;height:16px;accent-color:var(--accent);flex-shrink:0;cursor:pointer}
    @@media(max-width: 900px){
        .ml-hero{flex-direction:column;gap:24px;margin:16px 16px 0;padding:24px 20px}
        .ml-content{padding:16px}
        .ml-hero-left{width:100%}
    }
    @@media(max-width: 760px){
        .ml-layout{flex-direction:column}
        .ml-sidebar{width:100%;height:auto;position:static;border-right:none;border-bottom:1px solid var(--border)}
    }
</style>

<div class="ml-layout">
    <div class="ml-sidebar">
        <div class="ml-sidebar-head">
            <span><i class="fas fa-layer-group" style="margin-right:6px;color:var(--accent)"></i>{{ __('ml_my_lists') }}</span>
            <button class="ml-new-btn" onclick="document.getElementById('createListModal').style.display='flex'"><i class="fas fa-plus"></i>new</button>
        </div>
        @foreach($allLists as $l)
            <a href="{{ route('profile.my-lists.show', $l->slug) }}" class="ml-sidebar-item {{ $l->id === $list->id ? 'active' : '' }}">
                <span class="ico">
                    @if($l->slug === 'favorite')
                        <i class="fas fa-star ml-star"></i>
                    @else
                        <i class="fas {{ $l->icon }}" style="color:{{ $l->color }}"></i>
                    @endif
                </span>
                <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $l->title }}</span>
                <span class="cnt">{{ $l->problems_count }}</span>
            </a>
        @endforeach
    </div>

    <div class="ml-main">
        <div class="ml-hero">
            <div class="ml-hero-left">
                <div class="ml-icon-box" style="background:linear-gradient(135deg, {{ $list->color }}, {{ $list->color }}99);color:#fff">
                    <i class="fas {{ $list->slug === 'favorite' ? 'fa-star' : $list->icon }}"></i>
                </div>
                <div class="ml-title">{{ $list->title }}</div>
                <div class="ml-author">{{ Auth::user()->name }} · {{ $list->problems_count }} {{ __('ml_search_questions') }}</div>
                <div class="ml-btns">
                    <a href="{{ route('problems.index') }}" class="ml-btn primary"><i class="fas fa-play"></i> {{ __('ml_practice') }}</a>
                    <button class="ml-btn" onclick="openAddModal()"><i class="fas fa-plus"></i> {{ __('ml_add') }}</button>
                    <button class="ml-btn" onclick="document.getElementById('editListModal').style.display='flex'" title="{{ __('ml_rename_list') }}"><i class="fas fa-pen"></i></button>
                    <form action="{{ route('profile.my-lists.delete', $list->id) }}" method="POST" style="display:inline" onsubmit="return confirm('{{ __('ml_delete_list_confirm') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="ml-btn danger" title="{{ __('ml_remove') }}"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>

            <div class="ml-progress-section">
                <div class="ml-progress-card">
                    <div class="p-title">{{ __('ml_progress') }}</div>
                    @php
                        $total = $list->problems_count;
                        $pct = $total > 0 ? round(($solved / $total) * 100) : 0;
                        $r = 52;
                        $circumference = 2 * pi() * $r;
                        $offset = $circumference - ($pct / 100) * $circumference;
                    @endphp
                    <div class="ml-ring-wrap">
                        <svg width="140" height="140">
                            <circle class="bg" cx="70" cy="70" r="56" fill="none" stroke-width="9"/>
                            <circle class="fg" id="mlRingFg" cx="70" cy="70" r="56" fill="none" stroke-width="9"
                                stroke="url(#mlShowGrad)" stroke-dasharray="{{ number_format($circumference, 2, '.', '') }}"
                                stroke-dashoffset="{{ number_format($circumference, 2, '.', '') }}"
                                data-offset="{{ number_format($offset, 2, '.', '') }}" stroke-linecap="round"/>
                            <defs><linearGradient id="mlShowGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="{{ $list->color }}"/>
                                <stop offset="100%" stop-color="#8b5cf6"/>
                            </linearGradient></defs>
                        </svg>
                        <div class="ml-ring-center">
                            <div class="big">{{ $solved }}/{{ $total }}</div>
                            <div class="sub">{{ __('ml_solved') }}</div>
                        </div>
                    </div>
                    <div class="ml-stat-row">
                        <div class="ml-stat">
                            <div class="val" style="color:#22c55e">{{ $solved }}</div>
                            <div class="lbl">{{ __('ml_solved') }}</div>
                        </div>
                        <div class="ml-stat">
                            <div class="val" style="color:#f59e0b">{{ $attempting }}</div>
                            <div class="lbl">{{ __('ml_attempting') }}</div>
                        </div>
                        <div class="ml-stat">
                            <div class="val" style="color:var(--text-muted)">{{ $total - $solved - $attempting }}</div>
                            <div class="lbl">{{ __('ml_remaining') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ml-content">
            <div class="ml-toolbar">
                <span class="ml-search-wrap" style="position:relative">
                    <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:12px;color:var(--text-muted)"></i>
                    <input type="text" class="ml-search" placeholder="{{ __('ml_search_questions') }}" id="searchInput" oninput="filterTable()">
                </span>
                <button class="ml-filter-btn active" data-filter="all" onclick="setFilter(this,'all')">{{ __('ml_all') }}</button>
                <button class="ml-filter-btn" data-filter="solved" onclick="setFilter(this,'solved')">{{ __('ml_solved') }}</button>
                <button class="ml-filter-btn" data-filter="attempted" onclick="setFilter(this,'attempted')">{{ __('ml_attempting') }}</button>
                <button class="ml-filter-btn" data-filter="unsolved" onclick="setFilter(this,'unsolved')">{{ __('ml_todo') }}</button>
            </div>

            @if($list->problems->count())
                <div class="ml-table-card">
                <table class="ml-table">
                    <thead>
                        <tr>
                            <th class="status-col"></th>
                            <th class="num">#</th>
                            <th>{{ __('ml_title') }}</th>
                            <th class="diff-cell">{{ __('ml_difficulty') }}</th>
                            <th class="actions-cell"></th>
                        </tr>
                    </thead>
                    <tbody id="problemTable">
                        @foreach($list->problems as $idx => $problem)
                            @php $status = $userProblems[$problem->id] ?? null; @endphp
                            <tr class="problem-row"
                                data-title="{{ strtolower($problem->title) }}"
                                data-status="{{ $status ?? 'unsolved' }}"
                                data-difficulty="{{ $problem->difficulty }}"
                                onclick="window.location='{{ route('problems.show', $problem->slug) }}'">
                                <td class="status-col" onclick="event.stopPropagation()">
                                    @if($status === 'solved')
                                        <span class="ml-status-dot solved"><i class="fas fa-check" style="font-size:9px;color:#fff"></i></span>
                                    @elseif($status === 'attempted')
                                        <span class="ml-status-dot attempted"></span>
                                    @else
                                        <span class="ml-status-dot"></span>
                                    @endif
                                </td>
                                <td class="num">{{ $idx + 1 }}</td>
                                <td class="title-cell">{{ $problem->title }}</td>
                                <td class="diff-cell">
                                    <span class="diff-pill diff-{{ $problem->difficulty }}">{{ $problem->difficulty === 'easy' ? __('ml_easy') : ($problem->difficulty === 'medium' ? __('ml_med') : __('ml_hard')) }}</span>
                                </td>
                                <td class="actions-cell" onclick="event.stopPropagation()">
                                    <button class="ml-remove-btn" onclick="removeProblem({{ $list->id }}, {{ $problem->id }}, this)" title="{{ __('ml_remove') }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @else
                <div class="ml-empty-table">
                    <i class="fas fa-list-ol"></i>
                    <p style="margin-bottom:16px">{{ __('ml_no_questions') }}</p>
                    <button class="ml-add-btn" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> {{ __('ml_add_questions') }}
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<div id="createListModal" class="ml-modal-overlay" style="display:none" onclick="if(event.target===this)this.style.display='none'">
    <div class="ml-modal">
        <div class="ml-modal-head">
            <h3>{{ __('ml_create_new_list') }}</h3>
            <button class="ml-modal-close" onclick="this.closest('.ml-modal-overlay').style.display='none'"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('profile.my-lists.create') }}" method="POST">
            @csrf
            <div class="ml-modal-body">
                <input type="text" name="title" placeholder="{{ __('ml_list_name') }}" required autofocus maxlength="60">
            </div>
            <div class="ml-modal-foot">
                <button type="button" class="ml-modal-cancel" onclick="this.closest('.ml-modal-overlay').style.display='none'">{{ __('ml_cancel') }}</button>
                <button type="submit" class="ml-modal-submit">{{ __('ml_create') }}</button>
            </div>
        </form>
    </div>
</div>

<div id="editListModal" class="ml-modal-overlay" style="display:none" onclick="if(event.target===this)this.style.display='none'">
    <div class="ml-modal">
        <div class="ml-modal-head">
            <h3>{{ __('ml_rename_list') }}</h3>
            <button class="ml-modal-close" onclick="this.closest('.ml-modal-overlay').style.display='none'"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('profile.my-lists.update', $list->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="ml-modal-body">
                <input type="text" name="title" value="{{ $list->title }}" required maxlength="60">
            </div>
            <div class="ml-modal-foot">
                <button type="button" class="ml-modal-cancel" onclick="this.closest('.ml-modal-overlay').style.display='none'">{{ __('ml_cancel') }}</button>
                <button type="submit" class="ml-modal-submit">{{ __('ml_save') }}</button>
            </div>
        </form>
    </div>
</div>

<div id="addQuestionsModal" class="ml-modal-overlay" style="display:none" onclick="if(event.target===this)this.style.display='none'">
    <div class="ml-modal" style="width:580px">
        <div class="ml-modal-head">
            <h3>{{ __('ml_add_questions') }}</h3>
            <button class="ml-modal-close" onclick="document.getElementById('addQuestionsModal').style.display='none'"><i class="fas fa-times"></i></button>
        </div>
        <div class="ml-modal-body" style="padding-bottom:0">
            <input type="text" id="addSearchInput" placeholder="{{ __('ml_search_questions_dots') }}" oninput="filterAddList()" style="margin-bottom:12px">
            <div id="addProblemsList" style="max-height:45vh;overflow-y:auto;border:1px solid var(--border);border-radius:12px"></div>
        </div>
        <div class="ml-modal-foot">
            <button class="ml-modal-cancel" onclick="document.getElementById('addQuestionsModal').style.display='none'">{{ __('ml_cancel') }}</button>
            <button class="ml-modal-submit" id="addSelectedBtn" onclick="addSelectedProblems()" disabled>{{ __('ml_add_selected') }} (0)</button>
        </div>
    </div>
</div>

<script>
let currentFilter = 'all';
let listId = {{ $list->id }};

function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.problem-row').forEach(row => {
        const matchSearch = row.dataset.title.includes(q);
        const matchFilter = currentFilter === 'all' || row.dataset.status === currentFilter;
        row.style.display = matchSearch && matchFilter ? '' : 'none';
    });
}

function setFilter(btn, filter) {
    currentFilter = filter;
    document.querySelectorAll('.ml-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filterTable();
}

function removeProblem(lid, pid, btn) {
    fetch(`/profile/my-lists/${lid}/problems/${pid}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(r => r.json()).then(d => {
        if (d.success) {
            const row = btn.closest('tr');
            row.style.transition = 'all .25s';
            row.style.opacity = '0';
            row.style.transform = 'translateX(20px)';
            setTimeout(() => row.remove(), 250);
        }
    });
}

let allProblems = [];
let selectedIds = new Set();

function openAddModal() {
    document.getElementById('addQuestionsModal').style.display = 'flex';
    document.getElementById('addSearchInput').value = '';
    selectedIds.clear();
    updateAddBtn();
    document.getElementById('addProblemsList').innerHTML = '<div style="text-align:center;padding:30px;color:var(--text-muted)">{{ __('ml_loading') }}</div>';

    fetch(`/profile/my-lists/${listId}/available-problems`)
        .then(r => r.json())
        .then(d => {
            allProblems = d.problems;
            renderAddList();
        });
}

function renderAddList() {
    const q = document.getElementById('addSearchInput').value.toLowerCase();
    const container = document.getElementById('addProblemsList');
    const filtered = allProblems.filter(p => p.title.toLowerCase().includes(q));

    container.innerHTML = filtered.map(p => `
        <div class="ml-add-item" style="${p.in_list ? 'opacity:.35;pointer-events:none' : ''}"
             onclick="toggleAddItem(this, ${p.id})">
            <input type="checkbox" ${p.in_list || selectedIds.has(p.id) ? 'checked disabled' : ''}>
            <span style="flex:1;font-size:14px;color:var(--text)">${p.title}</span>
            <span class="diff-pill diff-${p.difficulty}" style="font-size:11px;margin-right:8px">${p.difficulty === 'easy' ? '{{ __('ml_easy') }}' : (p.difficulty === 'medium' ? '{{ __('ml_med') }}' : '{{ __('ml_hard') }}')}</span>
            ${p.user_status === 'solved' ? '<i class="fas fa-check-circle" style="font-size:12px;color:#22c55e"></i>' : ''}
        </div>
    `).join('');
}

function toggleAddItem(el, id) {
    if (selectedIds.has(id)) { selectedIds.delete(id); el.querySelector('input').checked = false; }
    else { selectedIds.add(id); el.querySelector('input').checked = true; }
    updateAddBtn();
}

function updateAddBtn() {
    const btn = document.getElementById('addSelectedBtn');
    btn.textContent = `{{ __('ml_add_selected') }} (${selectedIds.size})`;
    btn.disabled = selectedIds.size === 0;
}

function filterAddList() { renderAddList(); }

function addSelectedProblems() {
    if (selectedIds.size === 0) return;
    const btn = document.getElementById('addSelectedBtn');
    btn.disabled = true;
    btn.textContent = '{{ __('ml_adding') }}';

    fetch(`/profile/my-lists/${listId}/problems`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ problem_ids: Array.from(selectedIds) })
    }).then(r => r.json()).then(d => {
        if (d.success) location.reload();
    });
}

/* animate progress ring on load */
(function() {
    var fg = document.getElementById('mlRingFg');
    if (fg) {
        requestAnimationFrame(function() {
            requestAnimationFrame(function() {
                fg.style.strokeDashoffset = fg.dataset.offset;
            });
        });
    }
})();
</script>
@endsection
