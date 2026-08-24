@extends('layouts.app')
@section('title', $plan->title)

@section('content')
<div style="max-width:800px;margin:0 auto;padding:24px 16px">
    <a href="{{ route('study-plans.index') }}" style="font-size:12px;color:var(--text-muted);text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:16px">
        <i class="fas fa-arrow-left"></i> {{ __('All Plans') }}
    </a>

    <div style="display:flex;align-items:center;gap:14px;margin-bottom:24px">
        <div style="width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;background:{{ $plan->color }}15;color:{{ $plan->color }}">
            <i class="fas {{ $plan->icon }}"></i>
        </div>
        <div style="flex:1">
            <h1 style="font-size:22px;font-weight:800;color:var(--text);margin-bottom:2px">{{ $plan->title }}</h1>
            <p style="font-size:13px;color:var(--text-muted)">{{ $plan->description }}</p>
        </div>
        <form action="{{ route('study-plans.toggle-favorite', $plan) }}" method="POST">
            @csrf
            <button type="submit" style="background:none;border:2px solid {{ $plan->isFavorited() ? '#f59e0b' : 'var(--border)' }};cursor:pointer;padding:10px 14px;border-radius:10px;color:{{ $plan->isFavorited() ? '#f59e0b' : 'var(--text-muted)' }};font-size:16px;transition:all .2s" onmouseover="this.style.borderColor='#f59e0b';this.style.color='#f59e0b'" onmouseout="this.style.borderColor='{{ $plan->isFavorited() ? '#f59e0b' : 'var(--border)' }}';this.style.color='{{ $plan->isFavorited() ? '#f59e0b' : 'var(--text-muted)' }}'">
                <i class="fas fa-star"></i>
            </button>
        </form>
    </div>

    @if($plan->problems_count > 0)
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding:14px 18px;border-radius:12px;background:var(--bg-2);border:1px solid var(--border)">
            <div>
                <span style="font-size:13px;font-weight:700;color:var(--text)">{{ $completedCount }}/{{ $plan->problems_count }}</span>
                <span style="font-size:12px;color:var(--text-muted)"> {{ __('completed') }}</span>
            </div>
            <div style="width:160px;height:6px;border-radius:3px;background:var(--border);overflow:hidden">
                <div style="height:100%;border-radius:3px;background:{{ $plan->color }};transition:width .4s" style="width:{{ $plan->problems_count ? round(($completedCount/$plan->problems_count)*100) : 0 }}%"></div>
            </div>
        </div>
    @endif

    <div style="display:grid;gap:8px">
        @forelse($plan->problems as $problem)
            <a href="{{ route('problems.show', $problem->slug) }}" style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-radius:12px;border:1px solid var(--border);background:var(--bg-2);text-decoration:none;transition:all .15s" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">
                <div style="width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0;
                    @if($problem->is_solved) background:var(--success);color:white @else background:var(--border);color:var(--text-muted) @endif">
                    @if($problem->is_solved)
                        <i class="fas fa-check"></i>
                    @else
                        {{ $loop->iteration }}
                    @endif
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:600;color:var(--text)">{{ $problem->title }}</div>
                    <div style="display:flex;gap:8px;margin-top:3px">
                        <span style="font-size:10px;padding:2px 6px;border-radius:4px;font-weight:700;
                            @if($problem->difficulty === 'easy') background:rgba(34,197,94,.1);color:#22c55e
                            @elseif($problem->difficulty === 'medium') background:rgba(245,158,11,.1);color:#f59e0b
                            @else background:rgba(239,68,68,.1);color:#ef4444 @endif">
                            {{ __('difficulty_' . $problem->difficulty) }}
                        </span>
                    </div>
                </div>
                <i class="fas fa-chevron-right" style="color:var(--text-muted);font-size:11px;flex-shrink:0"></i>
            </a>
        @empty
            <div style="text-align:center;color:var(--text-muted);font-size:13px;padding:40px 0">
                {{ __('No problems in this plan yet') }}
            </div>
        @endforelse
    </div>
</div>
@endsection
