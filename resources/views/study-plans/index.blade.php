@extends('layouts.app')
@section('title', __('Study Plans'))

@section('content')
<div style="max-width:800px;margin:0 auto;padding:24px 16px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
        <div>
            <h1 style="font-size:22px;font-weight:800;color:var(--text);margin-bottom:4px">{{ __('Study Plans') }}</h1>
            <p style="font-size:13px;color:var(--text-muted)">{{ __('AI-powered personalized plans & curated lists') }}</p>
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('study-plans.favorite') }}" style="padding:10px 16px;border-radius:10px;border:1px solid var(--border);background:var(--bg-2);color:var(--text);font-size:13px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:8px;transition:all .2s" onmouseover="this.style.borderColor='#f59e0b'" onmouseout="this.style.borderColor='var(--border)'">
                <i class="fas fa-star" style="color:#f59e0b"></i> {{ __('Favorites') }}
            </a>
            <a href="{{ route('study-plans.create') }}" style="padding:10px 20px;border-radius:10px;background:linear-gradient(135deg,var(--accent),var(--accent-2));color:#fff;font-size:13px;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:8px;transition:all .2s;box-shadow:0 2px 12px var(--accent-glow)" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                <i class="fas fa-plus"></i> {{ __('Create AI Plan') }}
            </a>
        </div>
    </div>

    @if($userPlans->count() > 0)
    <div style="margin-bottom:32px">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
            <i class="fas fa-wand-magic-sparkles" style="color:var(--accent);font-size:14px"></i>
            <h2 style="font-size:16px;font-weight:700;color:var(--text)">{{ __('My AI Plans') }}</h2>
            <span style="font-size:11px;padding:2px 8px;border-radius:5px;font-weight:700;background:color-mix(in srgb, var(--accent) 15%, var(--card));color:var(--accent)">{{ $userPlans->count() }}</span>
        </div>
        <div style="display:grid;gap:10px">
            @foreach($userPlans as $plan)
            <a href="{{ route('study-plans.user.show', $plan) }}" style="display:flex;align-items:center;gap:16px;padding:18px;border-radius:14px;border:1px solid var(--border);background:var(--bg-2);text-decoration:none;transition:all .2s;position:relative;overflow:hidden" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">
                <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,var(--accent),var(--accent-2));display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;flex-shrink:0">
                    <i class="fas fa-wand-magic-sparkles"></i>
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:3px">{{ $plan->title }}</div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;margin-bottom:6px">
                        <span style="font-size:10px;padding:2px 6px;border-radius:4px;font-weight:700;background:color-mix(in srgb, var(--accent) 10%, var(--card));color:var(--accent)">{{ $plan->total_problems }} {{ __('problems') }}</span>
                        <span style="font-size:10px;padding:2px 6px;border-radius:4px;font-weight:700;
                            @if($plan->difficulty === 'easy') background:rgba(34,197,94,.1);color:#22c55e
                            @elseif($plan->difficulty === 'medium') background:rgba(245,158,11,.1);color:#f59e0b
                            @else background:rgba(239,68,68,.1);color:#ef4444 @endif">
                            {{ __('difficulty_' . $plan->difficulty) }}
                        </span>
                        @if($plan->deadline)
                            <span style="font-size:10px;color:{{ $plan->daysLeft() < 3 ? '#ef4444' : 'var(--text-muted)' }}">
                                <i class="fas fa-clock"></i> {{ $plan->daysLeft() }}d
                            </span>
                        @endif
                    </div>
                    @if($plan->progressPercent() > 0)
                    <div style="height:4px;border-radius:2px;background:var(--border);overflow:hidden">
                        <div style="height:100%;border-radius:2px;background:linear-gradient(90deg,var(--accent),var(--accent-2));width:{{ $plan->progressPercent() }}%"></div>
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:4px">{{ $plan->completed_problems }}/{{ $plan->total_problems }} ({{ $plan->progressPercent() }}%)</div>
                    @endif
                </div>
                @if($plan->isCompleted())
                    <span style="font-size:10px;padding:4px 8px;border-radius:5px;font-weight:700;background:rgba(34,197,94,.1);color:#22c55e"><i class="fas fa-check"></i></span>
                @else
                    <i class="fas fa-chevron-right" style="color:var(--text-muted);font-size:12px;flex-shrink:0"></i>
                @endif
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
            <i class="fas fa-list-ol" style="color:var(--accent-2);font-size:14px"></i>
            <h2 style="font-size:16px;font-weight:700;color:var(--text)">{{ __('Curated Plans') }}</h2>
        </div>
        <div style="display:grid;gap:10px">
            @forelse($plans as $plan)
                <a href="{{ route('study-plans.show', $plan->slug) }}" style="display:flex;align-items:center;gap:16px;padding:18px;border-radius:14px;border:1px solid var(--border);background:var(--bg-2);text-decoration:none;transition:all .2s" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">
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
                    <i class="fas fa-chevron-right" style="color:var(--text-muted);font-size:12px;flex-shrink:0"></i>
                </a>
            @empty
                <div style="text-align:center;color:var(--text-muted);font-size:14px;padding:48px 0">
                    <i class="fas fa-list-ol" style="font-size:32px;margin-bottom:10px;display:block;opacity:.3"></i>
                    {{ __('No curated plans yet') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
