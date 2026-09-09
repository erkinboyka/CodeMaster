@extends('layouts.app')
@section('title', __('ml_my_lists'))

@section('head')
<style>
    .ml-layout { display: flex; min-height: calc(100vh - 60px); background: var(--bg); position: relative; }
    .ml-layout::before { content: ''; position: absolute; inset: 0; pointer-events: none;
        background:
            radial-gradient(ellipse 40% 30% at 10% 0%, var(--accent-glow) 0%, transparent 60%),
            radial-gradient(ellipse 30% 30% at 95% 90%, rgba(139,92,246,.07) 0%, transparent 60%); }
    .ml-sidebar { width: 280px; background: color-mix(in srgb, var(--card) 75%, transparent);
        backdrop-filter: blur(16px); border-right: 1px solid var(--border);
        padding: 22px 12px; flex-shrink: 0; overflow-y: auto; position: sticky; top: 60px;
        height: calc(100vh - 60px); z-index: 2; }
    .ml-sidebar-head { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .12em;
        color: var(--text-muted); padding: 0 12px; margin-bottom: 10px; display: flex;
        align-items: center; justify-content: space-between; }
    .ml-sidebar-item { display: flex; align-items: center; gap: 12px; padding: 10px 12px; cursor: pointer;
        text-decoration: none; color: var(--text); font-size: 13.5px; font-weight: 600;
        transition: all .18s; border-left: 3px solid transparent; border-radius: 0 12px 12px 0; margin-bottom: 2px; }
    .ml-sidebar-item:hover { background: var(--bg-secondary); transform: translateX(2px); }
    .ml-sidebar-item.active { background: var(--accent-glow); border-left-color: var(--accent); color: var(--accent); }
    .ml-sidebar-item .ico { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center;
        justify-content: center; font-size: 14px; flex-shrink: 0; background: var(--bg-secondary);
        border: 1px solid var(--border); }
    .ml-sidebar-item .cnt { margin-left: auto; font-size: 11px; font-weight: 800; font-family: var(--font-mono);
        color: var(--text-muted); background: var(--bg-secondary); border: 1px solid var(--border);
        border-radius: 8px; padding: 2px 8px; min-width: 30px; text-align: center; }
    .ml-sidebar-item.active .cnt { color: var(--accent); border-color: var(--accent-glow-strong); }
    .ml-star { color: #f59e0b; }
    .ml-new-btn { display: inline-flex; align-items: center; gap: 5px; padding: 5px 11px; border-radius: 8px;
        background: transparent; border: 1px dashed var(--border-hover); color: var(--text-muted);
        font-size: 11px; font-weight: 700; cursor: pointer; transition: all .2s; }
    .ml-new-btn:hover { border-color: var(--accent); color: var(--accent); border-style: solid; }
    .ml-main { flex: 1; overflow-y: auto; padding: 40px 32px; display: flex; align-items: flex-start;
        justify-content: center; z-index: 1; }
    .ml-empty { text-align: center; padding: 56px 48px; max-width: 460px; width: 100%;
        border-radius: 24px; background: var(--card); border: 1.5px dashed var(--border-hover);
        box-shadow: 0 18px 50px rgba(0,0,0,.1); position: relative; overflow: hidden;
        opacity: 0; transform: translateY(24px); animation: mlIn .7s cubic-bezier(.16,1,.3,1) forwards; }
    @@keyframes mlIn { to { opacity: 1; transform: none; } }
    .ml-empty::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg,var(--accent),#8b5cf6,#38bdf8); }
    .ml-ring-wrap { position: relative; width: 150px; height: 150px; margin: 0 auto 22px; }
    .ml-ring-wrap svg { transform: rotate(-90deg); }
    .ml-ring-wrap .bg { stroke: var(--border); }
    .ml-ring-glow { position: absolute; inset: 18px; border-radius: 50%;
        background: radial-gradient(circle, var(--accent-glow) 0%, transparent 70%);
        animation: mlGlow 3s ease-in-out infinite; }
    @@keyframes mlGlow { 0%,100% { opacity: .4; transform: scale(.95); } 50% { opacity: .9; transform: scale(1.05); } }
    .ml-ring-center { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); text-align: center; }
    .ml-ring-num { font-size: 24px; font-weight: 900; color: var(--text); font-family: var(--font-mono); }
    .ml-ring-lbl { font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px;
        font-weight: 700; margin-top: 2px; }
    .ml-empty-icon { width: 60px; height: 60px; border-radius: 18px; margin: 0 auto 16px;
        background: linear-gradient(135deg,var(--accent),#8b5cf6); display: flex; align-items: center;
        justify-content: center; font-size: 24px; color: #fff;
        box-shadow: 0 12px 30px var(--accent-glow-strong); animation: mlFloat 4s ease-in-out infinite; }
    @@keyframes mlFloat { 0%,100% { transform: translateY(0) rotate(-3deg); } 50% { transform: translateY(-8px) rotate(3deg); } }
    .ml-empty-text { font-size: 17px; font-weight: 800; color: var(--text); margin-bottom: 6px; letter-spacing: -.3px; }
    .ml-empty-sub { font-size: 13px; color: var(--text-muted); line-height: 1.6; margin-bottom: 24px; }
    .ml-add-btn { display: inline-flex; align-items: center; gap: 8px; padding: 13px 28px; border-radius: 14px;
        background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff; font-size: 14px; font-weight: 800;
        border: none; cursor: pointer; transition: all .25s; text-decoration: none;
        box-shadow: 0 8px 26px var(--accent-glow-strong); }
    .ml-add-btn:hover { transform: translateY(-2px) scale(1.02); }
    .ml-hint { margin-top: 18px; font-size: 11.5px; color: var(--text-muted); font-family: var(--font-mono); }
    .ml-hint kbd { border: 1px solid var(--border); border-radius: 6px; padding: 1px 7px;
        background: var(--bg-secondary); font-family: inherit; }
    .ml-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.55); z-index: 1000; display: flex;
        align-items: center; justify-content: center; backdrop-filter: blur(6px); }
    .ml-modal { background: var(--card); border: 1px solid var(--border); border-radius: 20px; width: 440px;
        max-width: 90vw; padding: 26px; box-shadow: 0 25px 70px rgba(0,0,0,.35);
        animation: mlModalIn .25s cubic-bezier(.16,1,.3,1); }
    @@keyframes mlModalIn { from { opacity: 0; transform: scale(.94) translateY(10px); } to { opacity: 1; transform: none; } }
    .ml-modal h3 { font-size: 17px; font-weight: 800; margin: 0 0 6px; color: var(--text); letter-spacing: -.3px; }
    .ml-modal p { font-size: 12.5px; color: var(--text-muted); margin: 0 0 16px; }
    .ml-modal input[type=text] { width: 100%; box-sizing: border-box; padding: 12px 16px; border-radius: 12px;
        border: 1px solid var(--border); background: var(--bg-secondary); color: var(--text); font-size: 14px;
        outline: none; transition: all .2s; }
    .ml-modal input[type=text]:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
    .ml-modal-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 18px; }
    .ml-modal-actions button { padding: 10px 20px; border-radius: 11px; font-size: 13px; font-weight: 700;
        border: none; cursor: pointer; transition: all .2s; }
    .ml-modal-cancel { background: var(--bg-secondary); color: var(--text); border: 1px solid var(--border); }
    .ml-modal-cancel:hover { border-color: var(--accent); }
    .ml-modal-submit { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff;
        box-shadow: 0 4px 14px var(--accent-glow-strong); }
    .ml-modal-submit:hover { transform: translateY(-1px); }
    @@media(max-width: 760px) {
        .ml-layout { flex-direction: column; }
        .ml-sidebar { width: 100%; height: auto; position: static; border-right: none;
            border-bottom: 1px solid var(--border); }
        .ml-main { padding: 24px 16px; }
        .ml-empty { padding: 40px 24px; }
    }
</style>
@endsection

@section('content')
<div class="ml-layout">
    <div class="ml-sidebar">
        <div class="ml-sidebar-head">
            <span><i class="fas fa-layer-group" style="margin-right:6px;color:var(--accent)"></i>{{ __('ml_my_lists') }}</span>
            <button class="ml-new-btn" onclick="document.getElementById('createListModal').style.display='flex'"><i class="fas fa-plus"></i>new</button>
        </div>
        @forelse($lists as $item)
            <a href="{{ route('profile.my-lists.show', $item->slug) }}" class="ml-sidebar-item">
                <span class="ico">
                    @if($item->slug === 'favorite')
                        <i class="fas fa-star ml-star"></i>
                    @else
                        <i class="fas {{ $item->icon }}" style="color:{{ $item->color }}"></i>
                    @endif
                </span>
                <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $item->title }}</span>
                <span class="cnt">{{ $item->problems_count }}</span>
            </a>
        @empty
        @endforelse
    </div>

    <div class="ml-main">
        <div class="ml-empty">
            <div class="ml-ring-wrap">
                <div class="ml-ring-glow"></div>
                <svg width="150" height="150">
                    <circle class="bg" cx="75" cy="75" r="56" fill="none" stroke-width="9"/>
                    <circle class="fg" cx="75" cy="75" r="56" fill="none" stroke-width="9" stroke="url(#mlGrad)"
                        stroke-dasharray="{{ 2 * pi() * 56 }}" stroke-dashoffset="{{ 2 * pi() * 56 }}" stroke-linecap="round"/>
                    <defs><linearGradient id="mlGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="var(--accent)"/>
                        <stop offset="100%" stop-color="#8b5cf6"/>
                    </linearGradient></defs>
                </svg>
                <div class="ml-ring-center">
                    <div class="ml-ring-num">–/–</div>
                    <div class="ml-ring-lbl">{{ __('ml_solved') }}</div>
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
        <h3><i class="fas fa-plus" style="color:var(--accent);margin-right:8px"></i>{{ __('ml_create_new_list') }}</h3>
        <p>{{ __('ml_select_list_sub') }}</p>
        <form action="{{ route('profile.my-lists.create') }}" method="POST">
            @csrf
            <input type="text" name="title" placeholder="{{ __('ml_list_name') }}" required autofocus maxlength="60">
            <div class="ml-modal-actions">
                <button type="button" class="ml-modal-cancel" onclick="this.closest('.ml-modal-overlay').style.display='none'">{{ __('ml_cancel') }}</button>
                <button type="submit" class="ml-modal-submit"><i class="fas fa-check" style="margin-right:6px"></i>{{ __('ml_create') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
