@extends('layouts.app')
@section('title', __('Daily Challenge'))

@section('content')
<div style="max-width:720px;margin:0 auto;padding:24px 16px">
    <div style="text-align:center;margin-bottom:32px">
        <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,var(--accent),var(--accent-2));display:flex;align-items:center;justify-content:center;font-size:28px;color:white;margin:0 auto 12px">
            <i class="fas fa-bolt"></i>
        </div>
        <h1 style="font-size:22px;font-weight:800;color:var(--text);margin-bottom:4px">{{ __('Daily Challenge') }}</h1>
        <p style="font-size:13px;color:var(--text-muted)" id="countdown-text">
            {{ __('New challenge in') }} <span id="countdown" style="font-weight:700;color:var(--accent)"></span>
        </p>
    </div>

    @if($problem)
        <a href="{{ route('problems.show', $problem->slug) }}" style="display:block;padding:24px;border-radius:16px;border:1px solid var(--accent);background:var(--bg-2);text-decoration:none;transition:all .2s;margin-bottom:24px" onmouseover="this.style.boxShadow='0 0 20px var(--accent-glow)'" onmouseout="this.style.boxShadow='none'">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                <span style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase">{{ __('Today\'s Problem') }}</span>
                <span style="font-size:10px;padding:3px 8px;border-radius:6px;font-weight:700;
                    @if($problem->difficulty === 'easy') background:rgba(34,197,94,.12);color:#22c55e
                    @elseif($problem->difficulty === 'medium') background:rgba(245,158,11,.12);color:#f59e0b
                    @else background:rgba(239,68,68,.12);color:#ef4444 @endif">
                    {{ __('difficulty_' . $problem->difficulty) }}
                </span>
            </div>
            <h2 style="font-size:18px;font-weight:700;color:var(--text);margin-bottom:8px">{{ $problem->title }}</h2>
            <div style="font-size:12px;color:var(--text-muted)">
                @if($today->submissions_count > 0)
                    {{ $today->solved_count }}/{{ $today->submissions_count }} {{ __('solved') }} ({{ $today->submissions_count > 0 ? round(($today->solved_count/$today->submissions_count)*100) : 0 }}%)
                @else
                    {{ __('Be the first to solve!') }}
                @endif
            </div>
            <div style="margin-top:14px;text-align:center;padding:8px;border-radius:8px;background:var(--accent);color:white;font-size:13px;font-weight:700">
                {{ __('Solve Now') }} <i class="fas fa-arrow-right" style="margin-left:4px"></i>
            </div>
        </a>
    @else
        <div style="text-align:center;padding:40px;border-radius:16px;border:1px dashed var(--border);background:var(--bg-2)">
            <i class="fas fa-calendar-day" style="font-size:32px;color:var(--text-muted);opacity:.3;margin-bottom:10px;display:block"></i>
            <p style="font-size:14px;color:var(--text-muted)">{{ __('No challenge today. Check back tomorrow!') }}</p>
        </div>
    @endif

    @if($recent->count() > 1)
        <h3 style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:12px">{{ __('Recent Challenges') }}</h3>
        <div style="display:grid;gap:8px">
            @foreach($recent as $day)
                @if($day->problem)
                    <a href="{{ route('problems.show', $day->problem->slug) }}" style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-2);text-decoration:none;transition:all .15s" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">
                        <div style="width:36px;text-align:center;flex-shrink:0">
                            <div style="font-size:11px;color:var(--text-muted)">{{ $day->challenge_date->format('M') }}</div>
                            <div style="font-size:16px;font-weight:800;color:var(--text)">{{ $day->challenge_date->format('d') }}</div>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:13px;font-weight:600;color:var(--text)">{{ $day->problem->title }}</div>
                            <div style="font-size:11px;color:var(--text-muted)">
                                <span style="font-weight:700;
                                    @if($day->problem->difficulty === 'easy') color:#22c55e
                                    @elseif($day->problem->difficulty === 'medium') color:#f59e0b
                                    @else color:#ef4444 @endif">{{ __('difficulty_' . $day->problem->difficulty) }}</span>
                                · {{ $day->solved_count }} {{ __('solved') }}
                            </div>
                        </div>
                        <i class="fas fa-chevron-right" style="color:var(--text-muted);font-size:11px"></i>
                    </a>
                @endif
            @endforeach
        </div>
    @endif
</div>

<script>
function updateCountdown() {
    const now = new Date();
    const tomorrow = new Date(now);
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(0, 0, 0, 0);
    const diff = tomorrow - now;
    const h = Math.floor(diff / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    const el = document.getElementById('countdown');
    if (el) el.textContent = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
}
updateCountdown();
setInterval(updateCountdown, 1000);
</script>
@endsection
