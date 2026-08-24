@extends('layouts.app')
@section('title', $list->title . ' - ' . __('ml_my_lists'))

@section('content')
<style>
    .ml-layout{display:flex;min-height:calc(100vh - 60px)}
    .ml-sidebar{width:260px;background:var(--bg-2);border-right:1px solid var(--border);padding:20px 0;flex-shrink:0;overflow-y:auto;position:sticky;top:60px;height:calc(100vh - 60px)}
    .ml-sidebar-head{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);padding:0 20px;margin-bottom:8px;display:flex;align-items:center;justify-content:space-between}
    .ml-sidebar-item{display:flex;align-items:center;gap:10px;padding:8px 20px;cursor:pointer;text-decoration:none;color:var(--text);font-size:14px;transition:background .15s;border-left:3px solid transparent}
    .ml-sidebar-item:hover{background:var(--bg-3)}
    .ml-sidebar-item.active{background:color-mix(in srgb, var(--accent) 8%, transparent);border-left-color:var(--accent);font-weight:600;color:var(--accent)}
    .ml-sidebar-item .cnt{margin-left:auto;font-size:12px;color:var(--text-muted)}
    .ml-star{color:#f59e0b}
    .ml-new-btn{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:6px;background:transparent;border:1px solid var(--border);color:var(--text-muted);font-size:11px;cursor:pointer;transition:all .15s}
    .ml-new-btn:hover{border-color:var(--accent);color:var(--accent)}
    .ml-main{flex:1;overflow-y:auto;padding-bottom:40px}
    .ml-header-card{margin:24px 32px 0;border-radius:16px;border:1px solid var(--border);background:var(--bg-1);padding:28px 32px;display:flex;gap:32px;align-items:flex-start}
    .ml-header-left{flex-shrink:0;text-align:center;width:200px}
    .ml-icon-box{width:72px;height:72px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 14px}
    .ml-title{font-size:20px;font-weight:800;color:var(--text);line-height:1.2}
    .ml-author{font-size:13px;color:var(--text-muted);margin-top:4px}
    .ml-btns{display:flex;gap:6px;justify-content:center;margin-top:16px;flex-wrap:wrap}
    .ml-btn{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;border:1px solid var(--border);background:var(--bg-2);color:var(--text);cursor:pointer;transition:all .15s;text-decoration:none;white-space:nowrap}
    .ml-btn:hover{border-color:var(--accent);color:var(--accent)}
    .ml-btn.primary{background:var(--accent);color:#fff;border-color:var(--accent)}
    .ml-btn.primary:hover{opacity:.9;color:#fff}
    .ml-progress-section{flex:1;min-width:0}
    .ml-progress-card{border-radius:14px;border:1px solid var(--border);background:var(--bg-2);padding:20px}
    .ml-progress-card .p-title{font-size:13px;font-weight:700;margin-bottom:14px;color:var(--text)}
    .ml-ring-wrap{position:relative;width:130px;height:130px;margin:0 auto}
    .ml-ring-wrap svg{transform:rotate(-90deg)}
    .ml-ring-wrap .bg{stroke:var(--border)}
    .ml-ring-wrap .fg{transition:stroke-dashoffset .6s ease}
    .ml-ring-center{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center}
    .ml-ring-center .big{font-size:22px;font-weight:800;color:var(--text)}
    .ml-ring-center .sub{font-size:11px;color:var(--text-muted);margin-top:2px}
    .ml-stat-row{display:flex;justify-content:space-around;margin-top:14px;padding-top:12px;border-top:1px solid var(--border)}
    .ml-stat{text-align:center}
    .ml-stat .val{font-size:15px;font-weight:700}
    .ml-stat .lbl{font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-top:2px}
    .ml-content{padding:20px 32px}
    .ml-toolbar{display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap}
    .ml-search{width:260px;padding:8px 12px 8px 34px;border-radius:8px;border:1px solid var(--border);background:var(--bg-2);color:var(--text);font-size:13px;outline:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%2394a3b8' viewBox='0 0 24 24' width='16' height='16'%3E%3Cpath d='M15.5 14h-.79l-.28-.27A6.47 6.47 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:10px center}
    .ml-search:focus{border-color:var(--accent)}
    .ml-filter-btn{padding:6px 14px;border-radius:20px;border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:12px;font-weight:500;cursor:pointer;transition:all .15s}
    .ml-filter-btn:hover{border-color:var(--text-muted);color:var(--text)}
    .ml-filter-btn.active{background:var(--accent);border-color:var(--accent);color:#fff}
    .ml-table{width:100%;border-collapse:collapse;border-radius:12px;overflow:hidden;border:1px solid var(--border)}
    .ml-table th{text-align:left;padding:10px 14px;font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em;background:var(--bg-2);border-bottom:1px solid var(--border)}
    .ml-table td{padding:0;font-size:14px;border-bottom:1px solid var(--border);transition:background .1s}
    .ml-table tbody tr:last-child td{border-bottom:none}
    .ml-table tbody tr{cursor:pointer}
    .ml-table tbody tr:hover td{background:var(--bg-2)}
    .ml-table .num{width:48px;color:var(--text-muted);font-size:13px;font-weight:500;padding:11px 14px}
    .ml-table .status-col{width:40px;text-align:center;padding:11px 10px}
    .ml-table .title-cell{padding:11px 14px;font-weight:500;color:var(--text);text-decoration:none}
    .ml-table tbody tr:hover .title-cell{color:var(--accent)}
    .ml-table .diff-cell{width:90px;padding:11px 14px}
    .ml-table .actions-cell{width:40px;padding:11px 10px;text-align:center}
    .ml-status-dot{width:18px;height:18px;border-radius:50%;border:2px solid var(--border);display:inline-block;transition:all .15s}
    .ml-status-dot.solved{border-color:#22c55e;background:#22c55e}
    .ml-status-dot.attempted{border-color:#f59e0b;background:rgba(245,158,11,.2)}
    .diff-easy{color:#22c55e;font-weight:600;font-size:13px}
    .diff-medium{color:#eab308;font-weight:600;font-size:13px}
    .diff-hard{color:#ef4444;font-weight:600;font-size:13px}
    .ml-empty-table{text-align:center;padding:60px 20px;color:var(--text-muted)}
    .ml-empty-table i{font-size:40px;opacity:.15;margin-bottom:12px;display:block}
    .ml-remove-btn{background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:12px;padding:4px 6px;border-radius:4px;transition:all .15s}
    .ml-remove-btn:hover{color:#ef4444;background:rgba(239,68,68,.1)}

    .ml-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1000;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);animation:mlFadeIn .15s ease}
    @keyframes mlFadeIn{from{opacity:0}to{opacity:1}}
    .ml-modal{background:var(--bg-1);border:1px solid var(--border);border-radius:16px;width:440px;max-width:90vw;padding:0;box-shadow:0 24px 80px rgba(0,0,0,.4);animation:mlSlideUp .2s ease}
    @keyframes mlSlideUp{from{transform:translateY(12px);opacity:0}to{transform:translateY(0);opacity:1}}
    .ml-modal-head{padding:20px 24px 0;display:flex;align-items:center;justify-content:space-between}
    .ml-modal-head h3{font-size:17px;font-weight:700;color:var(--text);margin:0}
    .ml-modal-close{width:32px;height:32px;border-radius:8px;border:none;background:var(--bg-3);color:var(--text-muted);font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s}
    .ml-modal-close:hover{background:var(--border);color:var(--text)}
    .ml-modal-body{padding:16px 24px 24px}
    .ml-modal input[type=text]{width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg-2);color:var(--text);font-size:14px;outline:none;transition:border .15s}
    .ml-modal input[type=text]:focus{border-color:var(--accent)}
    .ml-modal-foot{padding:12px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px}
    .ml-modal-foot button{padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .15s}
    .ml-modal-cancel{background:var(--bg-3);color:var(--text)}
    .ml-modal-cancel:hover{opacity:.8}
    .ml-modal-submit{background:var(--accent);color:#fff}
    .ml-modal-submit:hover{opacity:.9}
    .ml-modal-submit:disabled{opacity:.4;cursor:not-allowed}
    .ml-add-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border-radius:10px;background:var(--accent);color:#fff;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:opacity .15s}
    .ml-add-btn:hover{opacity:.9}
</style>

<div class="ml-layout">
    <div class="ml-sidebar">
        <div class="ml-sidebar-head">
            {{ __('ml_my_lists') }}
            <button class="ml-new-btn" onclick="document.getElementById('createListModal').style.display='flex'"><i class="fas fa-plus"></i></button>
        </div>
        @foreach($allLists as $l)
            <a href="{{ route('profile.my-lists.show', $l->slug) }}" class="ml-sidebar-item {{ $l->id === $list->id ? 'active' : '' }}">
                @if($l->slug === 'favorite')
                    <i class="fas fa-star ml-star"></i>
                @else
                    <i class="fas {{ $l->icon }}" style="color:{{ $l->color }}"></i>
                @endif
                <span>{{ $l->title }}</span>
                <span class="cnt">{{ $l->problems_count }}</span>
            </a>
        @endforeach
    </div>

    <div class="ml-main">
        <div class="ml-header-card">
            <div class="ml-header-left">
                <div class="ml-icon-box" style="background:{{ $list->color }}15;color:{{ $list->color }}">
                    <i class="fas {{ $list->slug === 'favorite' ? 'fa-star' : $list->icon }}"></i>
                </div>
                <div class="ml-title">{{ $list->title }}</div>
                <div class="ml-author">{{ Auth::user()->name }} · {{ $list->problems_count }} {{ __('ml_search_questions') }}</div>
                <div class="ml-btns">
                    <a href="{{ route('problems.index') }}" class="ml-btn primary"><i class="fas fa-play"></i> {{ __('ml_practice') }}</a>
                    <button class="ml-btn" onclick="openAddModal()"><i class="fas fa-plus"></i> {{ __('ml_add') }}</button>
                    <button class="ml-btn" onclick="document.getElementById('editListModal').style.display='flex'"><i class="fas fa-pen"></i></button>
                    <form action="{{ route('profile.my-lists.delete', $list->id) }}" method="POST" style="display:inline" onsubmit="return confirm('{{ __('ml_delete_list_confirm') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="ml-btn"><i class="fas fa-trash"></i></button>
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
                        <svg width="130" height="130">
                            <circle class="bg" cx="65" cy="65" r="{{ $r }}" fill="none" stroke-width="8"/>
                            <circle class="fg" cx="65" cy="65" r="{{ $r }}" fill="none" stroke-width="8"
                                stroke="{{ $list->color }}" stroke-dasharray="{{ number_format($circumference, 2, '.', '') }}"
                                stroke-dashoffset="{{ number_format($offset, 2, '.', '') }}" stroke-linecap="round"/>
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
                <input type="text" class="ml-search" placeholder="{{ __('ml_search_questions') }}" id="searchInput" oninput="filterTable()">
                <button class="ml-filter-btn active" data-filter="all" onclick="setFilter(this,'all')">{{ __('ml_all') }}</button>
                <button class="ml-filter-btn" data-filter="solved" onclick="setFilter(this,'solved')">{{ __('ml_solved') }}</button>
                <button class="ml-filter-btn" data-filter="attempted" onclick="setFilter(this,'attempted')">{{ __('ml_attempting') }}</button>
                <button class="ml-filter-btn" data-filter="unsolved" onclick="setFilter(this,'unsolved')">{{ __('ml_todo') }}</button>
            </div>

            @if($list->problems->count())
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
                                        <span class="ml-status-dot solved"><i class="fas fa-check" style="font-size:9px;color:#fff;display:flex;align-items:center;justify-content:center;height:100%"></i></span>
                                    @elseif($status === 'attempted')
                                        <span class="ml-status-dot attempted"></span>
                                    @else
                                        <span class="ml-status-dot"></span>
                                    @endif
                                </td>
                                <td class="num">{{ $idx + 1 }}</td>
                                <td class="title-cell">{{ $problem->title }}</td>
                                <td class="diff-cell">
                                    <span class="diff-{{ $problem->difficulty }}">{{ $problem->difficulty === 'easy' ? __('ml_easy') : ($problem->difficulty === 'medium' ? __('ml_med') : __('ml_hard')) }}</span>
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
                <input type="text" name="title" placeholder="{{ __('ml_list_name') }}" required autofocus>
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
                <input type="text" name="title" value="{{ $list->title }}" required>
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
            <div id="addProblemsList" style="max-height:45vh;overflow-y:auto;border:1px solid var(--border);border-radius:8px"></div>
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
            row.style.transition = 'opacity .2s';
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 200);
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
        <div style="display:flex;align-items:center;padding:9px 12px;border-bottom:1px solid var(--border);cursor:pointer;${p.in_list ? 'opacity:.35;pointer-events:none' : ''}"
             onclick="toggleAddItem(this, ${p.id})">
            <input type="checkbox" ${p.in_list || selectedIds.has(p.id) ? 'checked disabled' : ''}
                   style="margin-right:12px;width:16px;height:16px;accent-color:var(--accent);flex-shrink:0">
            <span style="flex:1;font-size:14px;color:var(--text)">${p.title}</span>
            <span class="diff-${p.difficulty}" style="font-size:12px;margin-right:8px">${p.difficulty === 'easy' ? '{{ __('ml_easy') }}' : (p.difficulty === 'medium' ? '{{ __('ml_med') }}' : '{{ __('ml_hard') }}')}</span>
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
</script>
@endsection
