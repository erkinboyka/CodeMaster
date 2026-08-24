@extends('layouts.app')
@section('title', __('Create AI Study Plan'))

@section('content')
<div style="max-width:640px;margin:0 auto;padding:24px 16px">
    <a href="{{ route('study-plans.index') }}" style="font-size:12px;color:var(--text-muted);text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:16px">
        <i class="fas fa-arrow-left"></i> {{ __('All Plans') }}
    </a>

    <div style="margin-bottom:28px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px">
            <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,var(--accent),var(--accent-2));display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px">
                <i class="fas fa-wand-magic-sparkles"></i>
            </div>
            <div>
                <h1 style="font-size:22px;font-weight:800;color:var(--text)">{{ __('Create AI Plan') }}</h1>
                <p style="font-size:13px;color:var(--text-muted)">{{ __('Personalized study plan based on your level') }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('study-plans.store') }}" method="POST">
        @csrf
        <div style="margin-bottom:20px">
            <label style="font-size:13px;font-weight:700;color:var(--text);display:block;margin-bottom:8px">{{ __('Your Goal') }}</label>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                @foreach($goals as $key => $label)
                <label style="display:flex;align-items:center;gap:10px;padding:14px;border-radius:12px;border:2px solid var(--border);background:var(--bg-2);cursor:pointer;transition:all .2s" class="goal-card" data-value="{{ $key }}">
                    <input type="radio" name="goal" value="{{ $key }}" {{ $loop->first ? 'checked' : '' }} style="display:none">
                    <div class="goal-check" style="width:20px;height:20px;border-radius:50%;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;transition:all .2s;flex-shrink:0">
                        <i class="fas fa-check" style="font-size:10px;color:#fff;opacity:0;transition:opacity .2s"></i>
                    </div>
                    <span style="font-size:13px;font-weight:600;color:var(--text)">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div style="margin-bottom:20px">
            <label style="font-size:13px;font-weight:700;color:var(--text);display:block;margin-bottom:8px">{{ __('Difficulty') }}</label>
            <div style="display:flex;gap:8px">
                <label style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:12px;border-radius:10px;border:2px solid var(--border);background:var(--bg-2);cursor:pointer;transition:all .2s" class="diff-card" data-value="easy">
                    <input type="radio" name="difficulty" value="easy" style="display:none">
                    <span style="font-size:12px;font-weight:700;color:#22c55e">{{ __('Easy') }}</span>
                </label>
                <label style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:12px;border-radius:10px;border:2px solid var(--accent);background:color-mix(in srgb, var(--accent) 10%, var(--bg-2));cursor:pointer;transition:all .2s" class="diff-card" data-value="medium">
                    <input type="radio" name="difficulty" value="medium" checked style="display:none">
                    <span style="font-size:12px;font-weight:700;color:var(--accent)">{{ __('Medium') }}</span>
                </label>
                <label style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:12px;border-radius:10px;border:2px solid var(--border);background:var(--bg-2);cursor:pointer;transition:all .2s" class="diff-card" data-value="hard">
                    <input type="radio" name="difficulty" value="hard" style="display:none">
                    <span style="font-size:12px;font-weight:700;color:#ef4444">{{ __('Hard') }}</span>
                </label>
            </div>
            <p style="font-size:11px;color:var(--text-muted);margin-top:6px">{{ __('Auto-adjusted based on your level:') }} <strong style="color:var(--accent)">{{ $user->level }} {{ $user->level_title }}</strong></p>
        </div>

        <div style="display:flex;gap:12px;margin-bottom:20px">
            <div style="flex:1">
                <label style="font-size:13px;font-weight:700;color:var(--text);display:block;margin-bottom:6px">{{ __('Daily Goal') }}</label>
                <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-2)">
                    <button type="button" onclick="adjustDaily(-1)" style="width:28px;height:28px;border-radius:6px;border:1px solid var(--border);background:var(--bg);color:var(--text);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px">-</button>
                    <input type="number" name="daily_goal" id="dailyGoal" value="3" min="1" max="10" style="width:40px;text-align:center;border:none;background:transparent;color:var(--text);font-size:16px;font-weight:700;outline:none;font-family:inherit">
                    <span style="font-size:11px;color:var(--text-muted)">{{ __('problems/day') }}</span>
                    <button type="button" onclick="adjustDaily(1)" style="width:28px;height:28px;border-radius:6px;border:1px solid var(--border);background:var(--bg);color:var(--text);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px">+</button>
                </div>
            </div>
            <div style="flex:1">
                <label style="font-size:13px;font-weight:700;color:var(--text);display:block;margin-bottom:6px">{{ __('Deadline') }}</label>
                <input type="date" name="deadline" min="{{ now()->addDay()->toDateString() }}" style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-2);color:var(--text);font-size:13px;outline:none;font-family:inherit;box-sizing:border-box">
            </div>
        </div>

        <button type="submit" style="width:100%;padding:14px;border:none;border-radius:12px;background:linear-gradient(135deg,var(--accent),var(--accent-2));color:#fff;font-size:15px;font-weight:700;cursor:pointer;transition:all .2s;box-shadow:0 4px 16px var(--accent-glow)" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <i class="fas fa-wand-magic-sparkles" style="margin-right:8px"></i>{{ __('Generate Plan') }}
        </button>
    </form>
</div>

@push('scripts')
<script>
document.querySelectorAll('.goal-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.goal-card').forEach(c => {
            c.style.borderColor = 'var(--border)';
            c.style.background = 'var(--bg-2)';
            c.querySelector('.goal-check').style.borderColor = 'var(--border)';
            c.querySelector('.goal-check i').style.opacity = '0';
        });
        this.style.borderColor = 'var(--accent)';
        this.style.background = 'color-mix(in srgb, var(--accent) 10%, var(--bg-2))';
        this.querySelector('.goal-check').style.borderColor = 'var(--accent)';
        this.querySelector('.goal-check').style.background = 'var(--accent)';
        this.querySelector('.goal-check i').style.opacity = '1';
        this.querySelector('input').checked = true;
    });
});

document.querySelectorAll('.diff-card').forEach(card => {
    card.addEventListener('click', function() {
        const colors = { easy: '#22c55e', medium: 'var(--accent)', hard: '#ef4444' };
        const val = this.dataset.value;
        document.querySelectorAll('.diff-card').forEach(c => {
            c.style.borderColor = 'var(--border)';
            c.style.background = 'var(--bg-2)';
        });
        this.style.borderColor = colors[val];
        this.style.background = `color-mix(in srgb, ${colors[val]} 10%, var(--bg-2))`;
        this.querySelector('input').checked = true;
    });
});

function adjustDaily(delta) {
    const input = document.getElementById('dailyGoal');
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > 10) val = 10;
    input.value = val;
}
</script>
@endpush
@endsection
