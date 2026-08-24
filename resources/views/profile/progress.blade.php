@extends('layouts.app')
@section('title', __('Progress Dashboard'))

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<style>
    .pd-hero{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
    .pd-card{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden}
    .pd-card__head{padding:14px 18px 10px;font-size:13px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;border-bottom:1px solid var(--border)}
    .pd-card__head i{font-size:14px}
    .pd-card__body{padding:16px 18px}
    .pd-card__body canvas{max-height:220px}
    .pd-stat{padding:18px;border-radius:14px;border:1px solid var(--border);background:var(--bg-2);text-align:center;transition:all .2s}
    .pd-stat:hover{border-color:color-mix(in srgb,var(--accent) 30%,var(--border));transform:translateY(-2px)}
    .pd-stat__val{font-size:28px;font-weight:800;line-height:1}
    .pd-stat__label{font-size:11px;color:var(--text-muted);margin-top:4px}
    .pd-stat__sub{font-size:10px;color:var(--text-muted);margin-top:2px}
    .pd-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
    .pd-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px}
    .pd-full{grid-column:1/-1}
    .pd-mini{font-size:11px;color:var(--text-muted);margin-top:6px}
    .pd-bar{height:8px;border-radius:4px;background:var(--border);overflow:hidden;margin-top:6px}
    .pd-bar__fill{height:100%;border-radius:4px;transition:width .6s}
    .pd-skill{display:flex;align-items:center;gap:8px;padding:6px 0}
    .pd-skill__name{font-size:12px;color:var(--text);width:90px;flex-shrink:0}
    .pd-skill__bar{flex:1;height:6px;border-radius:3px;background:var(--border);overflow:hidden}
    .pd-skill__fill{height:100%;border-radius:3px}
    .pd-skill__lvl{font-size:10px;color:var(--text-muted);width:60px;text-align:right;flex-shrink:0}
    .pd-sub{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border)}
    .pd-sub:last-child{border-bottom:none}
    .pd-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
    .pd-sub__title{font-size:12px;font-weight:600;color:var(--text);flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .pd-sub__meta{font-size:10px;color:var(--text-muted);flex-shrink:0}
    @media(max-width:768px){.pd-hero{grid-template-columns:repeat(2,1fr)}.pd-grid,.pd-grid-3{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 16px">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
        <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,var(--accent),var(--accent-2));display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px">
            <i class="fas fa-chart-line"></i>
        </div>
        <div>
            <h1 style="font-size:22px;font-weight:800;color:var(--text)">{{ __('Progress Dashboard') }}</h1>
            <p style="font-size:13px;color:var(--text-muted)">{{ __('Your complete learning analytics') }}</p>
        </div>
    </div>

    {{-- ═══════ HERO STATS ═══════ --}}
    <div class="pd-hero">
        <div class="pd-stat" style="border-color:var(--accent);background:linear-gradient(135deg,var(--accent-glow),var(--bg-2))">
            <div class="pd-stat__val" style="color:var(--accent)">{{ number_format($user->total_xp ?? 0) }}</div>
            <div class="pd-stat__label">{{ __('Total XP') }}</div>
            <div class="pd-stat__sub">Lv.{{ $user->level ?? 1 }} {{ $user->level_title ?? '' }}</div>
        </div>
        <div class="pd-stat">
            <div class="pd-stat__val" style="color:var(--accent)">{{ $solvedCount }}</div>
            <div class="pd-stat__label">{{ __('Problems Solved') }}</div>
            <div class="pd-stat__sub">/ {{ $totalProblems }} {{ __('total') }}</div>
        </div>
        <div class="pd-stat">
            <div class="pd-stat__val" style="color:#22c55e">{{ $coursesCompleted }}</div>
            <div class="pd-stat__label">{{ __('Courses Done') }}</div>
            <div class="pd-stat__sub">/ {{ $coursesEnrolled }} {{ __('enrolled') }}</div>
        </div>
        <div class="pd-stat">
            <div class="pd-stat__val" style="color:{{ $user->getFireColor() }}">{{ $streak }}</div>
            <div class="pd-stat__label">{{ __('Day Streak') }}</div>
            <div class="pd-stat__sub">{{ __('Best') }}: {{ $longestStreak }} {{ __('days') }}</div>
        </div>
    </div>

    {{-- ═══════ ROW 1: Difficulty Donut + Solved Over Time ═══════ --}}
    <div class="pd-grid">
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-chart-pie" style="color:#22c55e"></i>{{ __('Problems by Difficulty') }}</div>
            <div class="pd-card__body" style="display:flex;align-items:center;gap:20px">
                <div style="width:140px;height:140px;flex-shrink:0"><canvas id="chartDifficulty"></canvas></div>
                <div>
                    <div style="font-size:13px;color:var(--text);margin-bottom:8px"><span style="color:#22c55e;font-weight:700">{{ $easy }}</span> Easy</div>
                    <div style="font-size:13px;color:var(--text);margin-bottom:8px"><span style="color:#f59e0b;font-weight:700">{{ $medium }}</span> Medium</div>
                    <div style="font-size:13px;color:var(--text)"><span style="color:#ef4444;font-weight:700">{{ $hard }}</span> Hard</div>
                    <div class="pd-mini" style="margin-top:10px">{{ $acceptanceRate }}% {{ __('acceptance rate') }}</div>
                </div>
            </div>
        </div>
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-chart-area" style="color:var(--accent)"></i>{{ __('Solved Over Time') }} <span class="pd-mini" style="margin-left:auto">30 {{ __('days') }}</span></div>
            <div class="pd-card__body"><canvas id="chartSolvedTime"></canvas></div>
        </div>
    </div>

    {{-- ═══════ ROW 2: Languages Pie + Submissions by Status + Acceptance Rate ═══════ --}}
    <div class="pd-grid-3">
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-code" style="color:#8b5cf6"></i>{{ __('Languages Used') }}</div>
            <div class="pd-card__body"><canvas id="chartLanguages"></canvas></div>
        </div>
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-check-circle" style="color:#3b82f6"></i>{{ __('Submissions by Status') }}</div>
            <div class="pd-card__body"><canvas id="chartSubStatus"></canvas></div>
        </div>
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-bullseye" style="color:#f59e0b"></i>{{ __('Performance') }}</div>
            <div class="pd-card__body">
                <div style="text-align:center;margin-bottom:12px">
                    <div style="font-size:28px;font-weight:800;color:var(--accent)">{{ $acceptanceRate }}%</div>
                    <div style="font-size:11px;color:var(--text-muted)">{{ __('Acceptance Rate') }}</div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;text-align:center">
                    <div>
                        <div style="font-size:16px;font-weight:700;color:var(--text)">{{ round($avgRuntime) }}ms</div>
                        <div style="font-size:10px;color:var(--text-muted)">{{ __('Avg Runtime') }}</div>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:700;color:var(--text)">{{ round($avgMemory) }}KB</div>
                        <div style="font-size:10px;color:var(--text-muted)">{{ __('Avg Memory') }}</div>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:700;color:var(--text)">{{ $totalAttempts }}</div>
                        <div style="font-size:10px;color:var(--text-muted)">{{ __('Total Submissions') }}</div>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:700;color:var(--text)">{{ $solvedCount }}</div>
                        <div style="font-size:10px;color:var(--text-muted)">{{ __('Unique Solved') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════ ROW 3: Daily Activity + XP Over Time ═══════ --}}
    <div class="pd-grid">
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-fire" style="color:#ef4444"></i>{{ __('Daily Activity') }} <span class="pd-mini" style="margin-left:auto">14 {{ __('days') }}</span></div>
            <div class="pd-card__body"><canvas id="chartDaily"></canvas></div>
        </div>
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-bolt" style="color:#f59e0b"></i>{{ __('XP Earned') }} <span class="pd-mini" style="margin-left:auto">30 {{ __('days') }}</span></div>
            <div class="pd-card__body"><canvas id="chartXp"></canvas></div>
        </div>
    </div>

    {{-- ═══════ ROW 4: Courses + Roadmap ═══════ --}}
    <div class="pd-grid">
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-graduation-cap" style="color:var(--accent)"></i>{{ __('Course Progress') }}</div>
            <div class="pd-card__body">
                @forelse($courseProgressList as $cp)
                <div style="margin-bottom:10px">
                    <div style="display:flex;justify-content:space-between;margin-bottom:3px">
                        <span style="font-size:12px;font-weight:600;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px">{{ $cp['title'] }}</span>
                        <span style="font-size:11px;font-weight:700;color:{{ $cp['completed'] ? '#22c55e' : 'var(--accent)' }}">{{ $cp['progress'] }}%</span>
                    </div>
                    <div class="pd-bar">
                        <div class="pd-bar__fill" style="width:{{ $cp['progress'] }}%;background:{{ $cp['completed'] ? '#22c55e' : 'linear-gradient(90deg,var(--accent),var(--accent-2))' }}"></div>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:12px">{{ __('No courses enrolled yet') }}</div>
                @endforelse
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:12px;padding-top:12px;border-top:1px solid var(--border)">
                    <div style="text-align:center"><div style="font-size:16px;font-weight:800;color:var(--accent)">{{ $coursesEnrolled }}</div><div style="font-size:10px;color:var(--text-muted)">{{ __('Enrolled') }}</div></div>
                    <div style="text-align:center"><div style="font-size:16px;font-weight:800;color:#22c55e">{{ $coursesCompleted }}</div><div style="font-size:10px;color:var(--text-muted)">{{ __('Done') }}</div></div>
                    <div style="text-align:center"><div style="font-size:16px;font-weight:800;color:#8b5cf6">{{ $lessonsCompleted }}</div><div style="font-size:10px;color:var(--text-muted)">{{ __('Lessons') }}</div></div>
                </div>
            </div>
        </div>
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-project-diagram" style="color:#8b5cf6"></i>{{ __('Roadmap & Contests') }}</div>
            <div class="pd-card__body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                    <div style="text-align:center;padding:14px;border-radius:10px;background:var(--bg-2)">
                        <div style="font-size:22px;font-weight:800;color:#8b5cf6">{{ $roadmapNodesCompleted }}</div>
                        <div style="font-size:10px;color:var(--text-muted)">{{ __('Roadmap Nodes') }}</div>
                    </div>
                    <div style="text-align:center;padding:14px;border-radius:10px;background:var(--bg-2)">
                        <div style="font-size:22px;font-weight:800;color:#f59e0b">{{ $roadmapCerts }}</div>
                        <div style="font-size:10px;color:var(--text-muted)">{{ __('Roadmap Certs') }}</div>
                    </div>
                    <div style="text-align:center;padding:14px;border-radius:10px;background:var(--bg-2)">
                        <div style="font-size:22px;font-weight:800;color:#ef4444">{{ $contestsParticipated }}</div>
                        <div style="font-size:10px;color:var(--text-muted)">{{ __('Contests') }}</div>
                    </div>
                    <div style="text-align:center;padding:14px;border-radius:10px;background:var(--bg-2)">
                        <div style="font-size:22px;font-weight:800;color:#3b82f6">{{ $certificates }}</div>
                        <div style="font-size:10px;color:var(--text-muted)">{{ __('Certificates') }}</div>
                    </div>
                </div>
                @if($ratingHistory->count())
                <div style="font-size:12px;font-weight:600;color:var(--text);margin-bottom:8px">{{ __('Rating History') }}</div>
                <div style="height:120px"><canvas id="chartRating"></canvas></div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════ ROW 5: Practice + Activity Types + Study Plans ═══════ --}}
    <div class="pd-grid-3">
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-laptop-code" style="color:#22c55e"></i>{{ __('Practice') }}</div>
            <div class="pd-card__body" style="text-align:center">
                <div style="width:100px;height:100px;margin:0 auto 12px;position:relative">
                    <canvas id="chartPractice"></canvas>
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;flex-direction:column">
                        <div style="font-size:20px;font-weight:800;color:var(--accent)">{{ $practiceRate }}%</div>
                    </div>
                </div>
                <div style="font-size:12px;color:var(--text-muted)">{{ $practicePassed }} / {{ $practiceTotal }} {{ __('passed') }}</div>
            </div>
        </div>
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-chart-bar" style="color:#f59e0b"></i>{{ __('Activity Types') }}</div>
            <div class="pd-card__body"><canvas id="chartActivityTypes"></canvas></div>
        </div>
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-calendar-check" style="color:#3b82f6"></i>{{ __('Study Plans') }}</div>
            <div class="pd-card__body" style="text-align:center">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px">
                    <div style="padding:14px;border-radius:10px;background:var(--bg-2)">
                        <div style="font-size:22px;font-weight:800;color:var(--accent)">{{ $studyPlans }}</div>
                        <div style="font-size:10px;color:var(--text-muted)">{{ __('Total') }}</div>
                    </div>
                    <div style="padding:14px;border-radius:10px;background:var(--bg-2)">
                        <div style="font-size:22px;font-weight:800;color:#22c55e">{{ $studyPlansActive }}</div>
                        <div style="font-size:10px;color:var(--text-muted)">{{ __('Active') }}</div>
                    </div>
                </div>
                <div style="font-size:12px;color:var(--text-muted)">{{ __('Avg course progress:') }} <strong style="color:var(--accent)">{{ round($avgCourseProgress) }}%</strong></div>
            </div>
        </div>
    </div>

    {{-- ═══════ ROW 6: Skills + Recent Submissions ═══════ --}}
    <div class="pd-grid">
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-star" style="color:#eab308"></i>{{ __('Skills') }}</div>
            <div class="pd-card__body">
                @forelse($skills as $skill)
                <div class="pd-skill">
                    <span class="pd-skill__name">{{ $skill['name'] }}</span>
                    <div class="pd-skill__bar">
                        <div class="pd-skill__fill" style="width:{{ match($skill['level']){'beginner'=>25,'intermediate'=>50,'advanced'=>75,'expert'=>100,default=>50} }}%;background:linear-gradient(90deg,var(--accent),var(--accent-2))"></div>
                    </div>
                    <span class="pd-skill__lvl">{{ ucfirst($skill['level']) }}</span>
                </div>
                @empty
                <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:12px">{{ __('No skills added') }}</div>
                @endforelse
            </div>
        </div>
        <div class="pd-card">
            <div class="pd-card__head"><i class="fas fa-clock" style="color:var(--text-muted)"></i>{{ __('Recent Submissions') }}</div>
            <div class="pd-card__body" style="max-height:280px;overflow-y:auto">
                @forelse($recentSubmissions as $sub)
                <div class="pd-sub">
                    <div class="pd-dot" style="background:{{ $sub->status === 'solved' ? '#22c55e' : '#ef4444' }}"></div>
                    <a href="{{ route('problems.show', $sub->problem->slug ?? '#') }}" class="pd-sub__title">{{ $sub->problem->title ?? 'Deleted' }}</a>
                    <span class="pd-sub__meta">{{ $sub->language }} {{ $sub->runtime_ms ? $sub->runtime_ms.'ms' : '' }}</span>
                    <span class="pd-sub__meta">{{ $sub->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:12px">{{ __('No submissions yet') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const ACCENT = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#6366f1';
const ACCENT2 = getComputedStyle(document.documentElement).getPropertyValue('--accent-2').trim() || '#a855f7';
const chartDefaults = { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}} };

function daysAgo(n){ const d=[]; for(let i=n-1;i>=0;i--){ const dt=new Date(); dt.setDate(dt.getDate()-i); d.push(dt.toLocaleDateString('en',{month:'short',day:'numeric'})); } return d; }

// 1. Difficulty Donut
new Chart(document.getElementById('chartDifficulty'),{type:'doughnut',data:{labels:['Easy','Medium','Hard'],datasets:[{data:[{{ $easy }},{{ $medium }},{{ $hard }}],backgroundColor:['#22c55e','#f59e0b','#ef4444'],borderWidth:0}]},options:{...chartDefaults,cutout:'65%',plugins:{legend:{display:false}}}});

// 2. Solved Over Time
const solvedRaw = {!! json_encode($solvedByDay) !!};
const solvedDays = daysAgo(30);
const solvedMapped = solvedDays.map(d=>{ const k=Object.keys(solvedRaw).find(x=>new Date(x).toLocaleDateString('en',{month:'short',day:'numeric'})===d); return k?solvedRaw[k]:0; });
new Chart(document.getElementById('chartSolvedTime'),{type:'line',data:{labels:solvedDays,datasets:[{data:solvedMapped,borderColor:ACCENT,backgroundColor:ACCENT+'22',fill:true,tension:.4,pointRadius:0,borderWidth:2}]},options:{...chartDefaults,scales:{x:{display:true,grid:{display:false},ticks:{maxTicksLimit:7,font:{size:10},color:'var(--text-muted)'}},y:{beginAtZero:true,grid:{color:'var(--border)'},ticks:{font:{size:10},color:'var(--text-muted)'}}}}});

// 3. Languages Pie
const langLabels = {!! json_encode(array_keys($langStats)) !!};
const langData = {!! json_encode(array_values($langStats)) !!};
const langColors = ['#6366f1','#8b5cf6','#3b82f6','#22c55e','#f59e0b','#ef4444','#ec4899','#14b8a6'];
new Chart(document.getElementById('chartLanguages'),{type:'doughnut',data:{labels:langLabels,datasets:[{data:langData,backgroundColor:langColors.slice(0,langLabels.length),borderWidth:0}]},options:{...chartDefaults,cutout:'55%',plugins:{legend:{position:'right',labels:{boxWidth:10,font:{size:11},color:'var(--text)'}}}}});

// 4. Submissions by Status
new Chart(document.getElementById('chartSubStatus'),{type:'bar',data:{labels:{!! json_encode(array_map(fn($s)=>ucfirst($s), array_keys($subByStatus))) !!},datasets:[{data:{!! json_encode(array_values($subByStatus)) !!},backgroundColor:['#22c55e','#ef4444','#f59e0b','#3b82f6'],borderRadius:6,barThickness:32}]},options:{...chartDefaults,indexAxis:'y',scales:{x:{beginAtZero:true,grid:{color:'var(--border)'},ticks:{font:{size:10}}},y:{grid:{display:false},ticks:{font:{size:11,weight:'bold'}}}}}});

// 5. Daily Activity
const dailyRaw = {!! json_encode($dailyActivity) !!};
const dailyDays = daysAgo(14);
const dailyMapped = dailyDays.map(d=>{ const k=Object.keys(dailyRaw).find(x=>new Date(x).toLocaleDateString('en',{month:'short',day:'numeric'})===d); return k?dailyRaw[k]:0; });
new Chart(document.getElementById('chartDaily'),{type:'bar',data:{labels:dailyDays,datasets:[{data:dailyMapped,backgroundColor:ACCENT+'88',borderColor:ACCENT,borderWidth:1,borderRadius:4}]},options:{...chartDefaults,scales:{x:{grid:{display:false},ticks:{font:{size:9}}},y:{beginAtZero:true,grid:{color:'var(--border)'},ticks:{font:{size:10},stepSize:1}}}}});

// 6. XP Over Time
const xpRaw = {!! json_encode($xpByDay) !!};
const xpDays = daysAgo(30);
const xpMapped = xpDays.map(d=>{ const k=Object.keys(xpRaw).find(x=>new Date(x).toLocaleDateString('en',{month:'short',day:'numeric'})===d); return k?xpRaw[k]:0; });
new Chart(document.getElementById('chartXp'),{type:'line',data:{labels:xpDays,datasets:[{data:xpMapped,borderColor:'#f59e0b',backgroundColor:'#f59e0b22',fill:true,tension:.4,pointRadius:0,borderWidth:2}]},options:{...chartDefaults,scales:{x:{grid:{display:false},ticks:{maxTicksLimit:7,font:{size:10}}},y:{beginAtZero:true,grid:{color:'var(--border)'},ticks:{font:{size:10}}}}}});

// 7. Rating History
@if($ratingHistory->count())
new Chart(document.getElementById('chartRating'),{type:'line',data:{labels:{!! json_encode($ratingHistory->pluck('date')) !!},datasets:[{data:{!! json_encode($ratingHistory->pluck('rating')) !!},borderColor:'#ef4444',backgroundColor:'#ef444422',fill:true,tension:.3,pointRadius:2,borderWidth:2}]},options:{...chartDefaults,scales:{x:{grid:{display:false},ticks:{font:{size:9}}},y:{grid:{color:'var(--border)'},ticks:{font:{size:10}}}}}});
@endif

// 8. Practice Donut
new Chart(document.getElementById('chartPractice'),{type:'doughnut',data:{labels:['Passed','Failed'],datasets:[{data:[{{ $practicePassed }},{{ $practiceTotal - $practicePassed }}],backgroundColor:['#22c55e','#33333333'],borderWidth:0}]},options:{...chartDefaults,cutout:'75%'}});

// 9. Activity Types
const actLabels = {!! json_encode(array_keys($activityByType)) !!};
const actData = {!! json_encode(array_values($activityByType)) !!};
const actColors = ['#6366f1','#f59e0b','#22c55e','#3b82f6','#ef4444','#ec4899','#8b5cf6','#14b8a6'];
new Chart(document.getElementById('chartActivityTypes'),{type:'doughnut',data:{labels:actLabels,datasets:[{data:actData,backgroundColor:actColors.slice(0,actLabels.length),borderWidth:0}]},options:{...chartDefaults,cutout:'50%',plugins:{legend:{position:'right',labels:{boxWidth:8,font:{size:10},color:'var(--text)'}}}}});
</script>
@endpush
@endsection