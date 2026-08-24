@extends('layouts.app')
@section('title', __('Favorite Study Plans'))

@section('content')
<div style="max-width:800px;margin:0 auto;padding:24px 16px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                <a href="{{ route('study-plans.index') }}" style="font-size:12px;color:var(--text-muted);text-decoration:none;display:inline-flex;align-items:center;gap:4px">
                    <i class="fas fa-arrow-left"></i> {{ __('Study Plans') }}
                </a>
            </div>
            <h1 style="font-size:22px;font-weight:800;color:var(--text);display:flex;align-items:center;gap:8px">
                <i class="fas fa-star" style="color:#f59e0b;font-size:18px"></i> {{ __('Favorite Study Plans') }}
            </h1>
            <p style="font-size:13px;color:var(--text-muted)">{{ __('Your saved curated study plans') }}</p>
        </div>
    </div>

    <div style="display:grid;gap:10px">
        @forelse($plans as $plan)
            <div style="display:flex;align-items:center;gap:16px;padding:18px;border-radius:14px;border:1px solid var(--border);background:var(--bg-2);transition:all .2s;position:relative" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">
                <a href="{{ route('study-plans.show', $plan->slug) }}" style="display:flex;align-items:center;gap:16px;flex:1;min-width:0;text-decoration:none;color:inherit">
                    <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;background:{{ $plan->color }}15;color:{{ $plan->color }}">
                        <i class="fas {{ $plan->icon }}"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:3px">{{ $plan->title }}</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-bottom:6px">{{ Str::limit($plan->description, 80) }}</div>
                        <div style="display:flex;align-items:center;gap:10px">
                            <span style="font-size:11px;color:var(--text-muted)">{{ $plan->problems_count }} {{ __('problems') }}</span>
                            @if($plan->user_progress > 0)
                                <span style="font-size:11px;color:var(--success);font-weight:600">{{ $plan->progressPercent() }}%</span>
                            @endif
                        </div>
                        @if($plan->progressPercent() > 0)
                            <div style="margin-top:6px;height:4px;border-radius:2px;background:var(--border);overflow:hidden">
                                <div style="height:100%;border-radius:2px;background:{{ $plan->color }};width:{{ $plan->progressPercent() }}%"></div>
                            </div>
                        @endif
                    </div>
                </a>
                <form action="{{ route('study-plans.toggle-favorite', $plan) }}" method="POST" style="margin-right:4px">
                    @csrf
                    @method('POST')
                    <button type="submit" style="background:none;border:none;cursor:pointer;padding:8px;border-radius:8px;color:#f59e0b;font-size:16px;transition:all .2s" onmouseover="this.style.background='rgba(245,158,11,.1)'" onmouseout="this.style.background='none'" title="{{ __('Remove from favorites') }}">
                        <i class="fas fa-star"></i>
                    </button>
                </form>
            </div>
        @empty
            <div style="text-align:center;color:var(--text-muted);font-size:14px;padding:48px 0">
                <i class="fas fa-star" style="font-size:32px;margin-bottom:10px;display:block;opacity:.3"></i>
                {{ __('No favorite study plans yet') }}
                <br>
                <a href="{{ route('study-plans.index') }}" style="color:var(--accent);font-size:13px;text-decoration:none;margin-top:8px;display:inline-block">
                    {{ __('Browse study plans') }} <i class="fas fa-arrow-right" style="font-size:11px"></i>
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
