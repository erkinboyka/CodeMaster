@extends('layouts.app')
@section('title', $plan->title . ' - Study Plan')

@section('content')
<div style="max-width:800px;margin:0 auto;padding:24px 16px">
    <a href="{{ route('study-plans.index') }}" style="font-size:12px;color:var(--text-muted);text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:16px">
        <i class="fas fa-arrow-left"></i> {{ __('All Plans') }}
    </a>

    <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:24px">
        <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,var(--accent),var(--accent-2));display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;flex-shrink:0">
            <i class="fas fa-wand-magic-sparkles"></i>
        </div>
        <div style="flex:1;min-width:0">
            <h1 style="font-size:22px;font-weight:800;color:var(--text);margin-bottom:2px">{{ $plan->title }}</h1>
            <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-top:4px">
                <span style="font-size:11px;padding:3px 8px;border-radius:5px;font-weight:700;background:color-mix(in srgb, var(--accent) 15%, var(--card));color:var(--accent)">{{ ucfirst($plan->goal) }}</span>
                <span style="font-size:11px;padding:3px 8px;border-radius:5px;font-weight:700;
                    @if($plan->difficulty === 'easy') background:rgba(34,197,94,.1);color:#22c55e
                    @elseif($plan->difficulty === 'medium') background:rgba(245,158,11,.1);color:#f59e0b
                    @else background:rgba(239,68,68,.1);color:#ef4444 @endif">
                    {{ __('difficulty_' . $plan->difficulty) }}
                </span>
                @if($plan->deadline)
                    <span style="font-size:11px;color:{{ $plan->daysLeft() < 3 ? '#ef4444' : 'var(--text-muted)' }}">
                        <i class="fas fa-clock"></i> {{ $plan->daysLeft() }} {{ __('days left') }}
                    </span>
                @endif
            </div>
        </div>
        <form action="{{ route('study-plans.user.destroy', $plan) }}" method="POST" onsubmit="return confirm('{{ __('Delete this plan?') }}')">
            @csrf
            @method('DELETE')
            <button type="submit" style="padding:6px 10px;border-radius:8px;border:1px solid var(--border);background:var(--bg-2);color:var(--text-muted);cursor:pointer;font-size:11px;transition:all .2s" onmouseover="this.style.borderColor='#ef4444';this.style.color='#ef4444'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)'">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px">
        <div style="padding:16px;border-radius:12px;background:var(--bg-2);border:1px solid var(--border);text-align:center">
            <div style="font-size:24px;font-weight:800;color:var(--accent)">{{ $plan->completed_problems }}/{{ $plan->total_problems }}</div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:2px">{{ __('Solved') }}</div>
        </div>
        <div style="padding:16px;border-radius:12px;background:var(--bg-2);border:1px solid var(--border);text-align:center">
            <div style="font-size:24px;font-weight:800;color:var(--accent-2)">{{ $plan->daily_goal }}</div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:2px">{{ __('Daily goal') }}</div>
        </div>
        <div style="padding:16px;border-radius:12px;background:var(--bg-2);border:1px solid var(--border);text-align:center">
            <div style="font-size:24px;font-weight:800;color:{{ $plan->isGoalMetToday() ? '#22c55e' : 'var(--text-muted)' }}">{{ $plan->todayCompleted() }}/{{ $plan->daily_goal }}</div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:2px">{{ __('Today') }}</div>
        </div>
    </div>

    @if($plan->progressPercent() > 0)
    <div style="margin-bottom:20px;padding:14px 18px;border-radius:12px;background:var(--bg-2);border:1px solid var(--border)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
            <span style="font-size:13px;font-weight:700;color:var(--text)">{{ $plan->progressPercent() }}% {{ __('completed') }}</span>
            @if($plan->isCompleted())
                <span style="font-size:11px;padding:3px 8px;border-radius:5px;font-weight:700;background:rgba(34,197,94,.1);color:#22c55e"><i class="fas fa-check"></i> {{ __('Done') }}</span>
            @endif
        </div>
        <div style="height:6px;border-radius:3px;background:var(--border);overflow:hidden">
            <div style="height:100%;border-radius:3px;background:linear-gradient(90deg,var(--accent),var(--accent-2));transition:width .4s;width:{{ $plan->progressPercent() }}%"></div>
        </div>
    </div>
    @endif

    @if($todayGoal && !$todayGoal->is_met)
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;background:color-mix(in srgb, var(--accent-3) 10%, var(--card));border:1px solid color-mix(in srgb, var(--accent-3) 20%, var(--border));display:flex;align-items:center;gap:10px">
        <i class="fas fa-bullseye" style="color:var(--accent-3);font-size:16px"></i>
        <div style="flex:1">
            <span style="font-size:12px;font-weight:600;color:var(--text)">{{ __('Daily goal:') }} {{ $todayGoal->completed }}/{{ $todayGoal->target }}</span>
            <span style="font-size:11px;color:var(--text-muted)"> — {{ $todayGoal->target - $todayGoal->completed }} {{ __('more to go') }}</span>
        </div>
    </div>
    @endif

    <div style="display:grid;gap:6px">
        @forelse($plan->problems as $problem)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-2);transition:all .15s" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">
                <div style="width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0;
                    @if($problem->pivot->is_solved) background:var(--success);color:white @else background:var(--border);color:var(--text-muted) @endif">
                    @if($problem->pivot->is_solved)
                        <i class="fas fa-check"></i>
                    @else
                        {{ $problem->pivot->order_num }}
                    @endif
                </div>
                <div style="flex:1;min-width:0">
                    <a href="{{ route('problems.show', $problem->slug) }}" style="font-size:13px;font-weight:600;color:var(--text);text-decoration:none;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $problem->title }}</a>
                    <div style="display:flex;gap:6px;margin-top:3px;align-items:center">
                        <span style="font-size:10px;padding:2px 6px;border-radius:4px;font-weight:700;
                            @if($problem->difficulty === 'easy') background:rgba(34,197,94,.1);color:#22c55e
                            @elseif($problem->difficulty === 'medium') background:rgba(245,158,11,.1);color:#f59e0b
                            @else background:rgba(239,68,68,.1);color:#ef4444 @endif">
                            {{ __('difficulty_' . $problem->difficulty) }}
                        </span>
                        @if($problem->pivot->is_solved && $problem->pivot->solved_at)
                            <span style="font-size:10px;color:var(--text-muted)">{{ \Carbon\Carbon::parse($problem->pivot->solved_at)->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
                @if(!$problem->pivot->is_solved)
                <form action="{{ route('study-plans.mark-solved', $plan) }}" method="POST" style="margin:0">
                    @csrf
                    <input type="hidden" name="problem_id" value="{{ $problem->id }}">
                    <button type="submit" style="padding:6px 12px;border-radius:6px;border:1px solid var(--success);background:rgba(34,197,94,.08);color:var(--success);font-size:11px;font-weight:700;cursor:pointer;transition:all .2s" onmouseover="this.style.background='rgba(34,197,94,.15)'" onmouseout="this.style.background='rgba(34,197,94,.08)'">
                        <i class="fas fa-check"></i> {{ __('Solved') }}
                    </button>
                </form>
                @else
                    <i class="fas fa-check-circle" style="color:var(--success);font-size:16px"></i>
                @endif
            </div>
        @empty
            <div style="text-align:center;color:var(--text-muted);font-size:13px;padding:40px 0">
                {{ __('No problems in this plan') }}
            </div>
        @endforelse
    </div>
</div>
@endsection
