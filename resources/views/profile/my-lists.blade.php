@extends('layouts.app')
@section('title', __('ml_my_lists'))

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
    .ml-main{flex:1;overflow-y:auto;padding:32px;display:flex;align-items:flex-start;justify-content:center}
    .ml-empty{text-align:center;padding:60px 20px;max-width:400px}
    .ml-ring-wrap{position:relative;width:140px;height:140px;margin:0 auto 20px}
    .ml-ring-wrap svg{transform:rotate(-90deg)}
    .ml-ring-wrap .bg{stroke:var(--border)}
    .ml-empty-icon{font-size:32px;color:var(--text-muted);opacity:.2;margin-bottom:16px}
    .ml-empty-text{font-size:15px;color:var(--text-muted);margin-bottom:6px}
    .ml-empty-sub{font-size:13px;color:var(--text-muted);opacity:.7;margin-bottom:20px}
    .ml-add-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border-radius:10px;background:var(--accent);color:#fff;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:opacity .15s;text-decoration:none}
    .ml-add-btn:hover{opacity:.9}
    .ml-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(2px)}
    .ml-modal{background:var(--bg-1);border-radius:16px;width:440px;max-width:90vw;padding:24px;box-shadow:0 20px 60px rgba(0,0,0,.3)}
    .ml-modal h3{font-size:17px;font-weight:700;margin-bottom:16px;color:var(--text)}
    .ml-modal input[type=text]{width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg-2);color:var(--text);font-size:14px;outline:none;transition:border .15s}
    .ml-modal input[type=text]:focus{border-color:var(--accent)}
    .ml-modal-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:16px}
    .ml-modal-actions button{padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .15s}
    .ml-modal-cancel{background:var(--bg-3);color:var(--text)}
    .ml-modal-cancel:hover{opacity:.8}
    .ml-modal-submit{background:var(--accent);color:#fff}
    .ml-modal-submit:hover{opacity:.9}
</style>

<div class="ml-layout">
    <div class="ml-sidebar">
        <div class="ml-sidebar-head">
            {{ __('ml_my_lists') }}
            <button class="ml-new-btn" onclick="document.getElementById('createListModal').style.display='flex'"><i class="fas fa-plus"></i></button>
        </div>
        @forelse($lists as $item)
            <a href="{{ route('profile.my-lists.show', $item->slug) }}" class="ml-sidebar-item">
                @if($item->slug === 'favorite')
                    <i class="fas fa-star ml-star"></i>
                @else
                    <i class="fas {{ $item->icon }}" style="color:{{ $item->color }}"></i>
                @endif
                <span>{{ $item->title }}</span>
                <span class="cnt">{{ $item->problems_count }}</span>
            </a>
        @empty
        @endforelse
    </div>

    <div class="ml-main">
        <div class="ml-empty">
            <div class="ml-ring-wrap">
                <svg width="140" height="140">
                    <circle class="bg" cx="70" cy="70" r="52" fill="none" stroke-width="8"/>
                    <circle class="fg" cx="70" cy="70" r="52" fill="none" stroke-width="8" stroke="var(--accent)"
                        stroke-dasharray="{{ 2 * pi() * 52 }}" stroke-dashoffset="{{ 2 * pi() * 52 }}" stroke-linecap="round"/>
                </svg>
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center">
                    <div style="font-size:22px;font-weight:800;color:var(--text)">–/–</div>
                    <div style="font-size:11px;color:var(--text-muted)">{{ __('ml_solved') }}</div>
                </div>
            </div>
            <div class="ml-empty-icon"><i class="fas fa-star"></i></div>
            <div class="ml-empty-text">{{ __('ml_select_list') }}</div>
            <div class="ml-empty-sub">{{ __('ml_select_list_sub') }}</div>
            <a href="{{ route('problems.index') }}" class="ml-add-btn">
                <i class="fas fa-plus"></i> {{ __('ml_browse_problems') }}
            </a>
        </div>
    </div>
</div>

<div id="createListModal" class="ml-modal-overlay" style="display:none" onclick="if(event.target===this)this.style.display='none'">
    <div class="ml-modal">
        <h3>{{ __('ml_create_new_list') }}</h3>
        <form action="{{ route('profile.my-lists.create') }}" method="POST">
            @csrf
            <input type="text" name="title" placeholder="{{ __('ml_list_name') }}" required autofocus>
            <div class="ml-modal-actions">
                <button type="button" class="ml-modal-cancel" onclick="this.closest('.ml-modal-overlay').style.display='none'">{{ __('ml_cancel') }}</button>
                <button type="submit" class="ml-modal-submit">{{ __('ml_create') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
