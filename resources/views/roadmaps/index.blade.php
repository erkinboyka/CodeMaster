@extends('layouts.app')

@section('title', __('Roadmaps') . ' - CodeMaster')

@section('head')
<style>
    .rm-hero {
        position: relative;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 50%, var(--accent-3) 100%);
        padding: clamp(80px, 12vw, 140px) 24px clamp(60px, 8vw, 100px);
        overflow: hidden;
        text-align: center;
    }
    .rm-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%);
    }
    .rm-hero::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 60px;
        background: var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .rm-hero-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 40px 40px;
    }
    .rm-hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        animation: rmOrbFloat 8s ease-in-out infinite;
    }
    .rm-hero-orb:nth-child(1) { width: 300px; height: 300px; background: rgba(255,255,255,0.1); top: -100px; left: -50px; }
    .rm-hero-orb:nth-child(2) { width: 250px; height: 250px; background: rgba(255,255,255,0.08); bottom: -80px; right: -30px; animation-delay: -3s; }
    .rm-hero-orb:nth-child(3) { width: 200px; height: 200px; background: rgba(255,255,255,0.06); top: 50%; left: 60%; animation-delay: -5s; }
    @@keyframes rmOrbFloat {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-20px) scale(1.05); }
    }
    .rm-hero h1 {
        font-size: clamp(36px, 6vw, 72px);
        font-weight: 900;
        color: white;
        letter-spacing: -2px;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }
    .rm-hero p {
        font-size: clamp(16px, 2vw, 20px);
        color: rgba(255,255,255,0.75);
        max-width: 540px;
        margin: 0 auto 40px;
        position: relative;
        z-index: 2;
        line-height: 1.7;
    }
    .rm-hero-stats {
        display: flex;
        justify-content: center;
        gap: clamp(24px, 4vw, 56px);
        position: relative;
        z-index: 2;
    }
    .rm-hero-stat-val {
        font-size: clamp(28px, 4vw, 42px);
        font-weight: 900;
        color: white;
        line-height: 1;
    }
    .rm-hero-stat-label {
        font-size: 12px;
        color: rgba(255,255,255,0.6);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-top: 8px;
        font-weight: 500;
    }

    .rm-carousel-wrap {
        position: relative;
        margin-top: 32px;
    }
    .rm-carousel {
        display: flex;
        gap: 24px;
        overflow-x: auto;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        padding: 8px 4px 20px;
        cursor: grab;
        user-select: none;
    }
    .rm-carousel:active { cursor: grabbing; }
    .rm-carousel::-webkit-scrollbar { height: 6px; }
    .rm-carousel::-webkit-scrollbar-track { background: var(--bg-secondary, #f1f1f1); border-radius: 10px; }
    .rm-carousel::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }

    .rm-carousel > .rm-card {
        min-width: 340px;
        max-width: 380px;
        flex-shrink: 0;
        scroll-snap-align: start;
    }

    .rm-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--card);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent);
        font-size: 18px;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    .rm-arrow:hover {
        background: var(--accent);
        color: white;
        transform: translateY(-50%) scale(1.1);
    }
    .rm-arrow-left { left: -12px; }
    .rm-arrow-right { right: -12px; }

    @@media(max-width:768px) {
        .rm-carousel > .rm-card { min-width: 290px; }
        .rm-arrow { display: none; }
    }

    .rm-card {
        opacity: 0;
        transform: translateY(30px) scale(0.97);
        transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s, border-color 0.3s;
    }
    .rm-card.visible {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
    .rm-card:hover {
        transform: translateY(-8px) scale(1.02) !important;
        box-shadow: 0 25px 60px -15px rgba(0,0,0,0.2);
        border-color: var(--accent);
    }
    .rm-card:hover .rm-card-icon {
        transform: scale(1.2) rotate(-8deg);
        filter: drop-shadow(0 6px 20px rgba(0,0,0,0.25));
    }
    .rm-card:hover .rm-card-glow {
        opacity: 1;
        transform: scale(1.3);
    }
    .rm-card:hover .rm-card-shine {
        transform: translateX(200%);
    }
    .rm-card:hover .rm-card-cover-overlay {
        opacity: 0.3;
    }
    .rm-card-icon {
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .rm-card-glow {
        position: absolute;
        bottom: -30px;
        right: -30px;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        filter: blur(50px);
        opacity: 0;
        transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
        z-index: 1;
    }
    .rm-card-shine {
        position: absolute;
        top: 0;
        left: -100%;
        width: 50%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: transform 0.7s ease;
        pointer-events: none;
        z-index: 3;
    }
    .rm-card-cover-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 100%);
        opacity: 0;
        transition: opacity 0.4s;
        z-index: 2;
    }
    @@keyframes rmFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    .rm-card-float {
        animation: rmFloat 4s ease-in-out infinite;
    }
</style>
@endsection

@section('content')
<section class="rm-hero">
    <div class="rm-hero-grid"></div>
    <div class="rm-hero-orb"></div>
    <div class="rm-hero-orb"></div>
    <div class="rm-hero-orb"></div>

    <h1 class="reveal-up" data-delay="0">{{ __('Learning Roadmaps') }}</h1>
    <p class="reveal-up" data-delay="0.1">{{ __('Structured learning paths from zero to professional developer.') }}</p>

    <div class="rm-hero-stats">
        <div class="rm-hero-stat">
            <div class="rm-hero-stat-val">8</div>
            <div class="rm-hero-stat-label">{{ __('Paths') }}</div>
        </div>
        <div class="rm-hero-stat">
            <div class="rm-hero-stat-val">{{ \App\Models\RoadmapNode::count() }}</div>
            <div class="rm-hero-stat-label">{{ __('Nodes') }}</div>
        </div>
        <div class="rm-hero-stat">
            <div class="rm-hero-stat-val">{{ \App\Models\RoadmapLesson::count() }}</div>
            <div class="rm-hero-stat-label">{{ __('Lessons') }}</div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h2 class="text-2xl font-bold" style="color:var(--text)">{{ __('Choose Your Path') }}</h2>
        <p class="mt-2" style="color:var(--text-muted)">{{ __('Pick a roadmap and follow a structured path to become a professional developer.') }}</p>
    </div>

    <div class="rm-carousel-wrap">
        @php
        $roadmaps = [
            ['title' => 'Frontend Developer', 'desc' => 'HTML, CSS, JavaScript, React — everything for building modern user interfaces and interactive web applications.', 'icon' => 'fab fa-react', 'gradient' => 'linear-gradient(135deg, #61dafb, #007acc)', 'tags' => ['HTML', 'CSS', 'JavaScript', 'React'], 'difficulty' => 3, 'duration' => '6-8 months', 'nodes' => 12],
            ['title' => 'Backend Developer', 'desc' => 'PHP, Laravel, MySQL, APIs — server-side development, databases and building robust applications.', 'icon' => 'fab fa-php', 'gradient' => 'linear-gradient(135deg, #ff6b6b, #ee5a24)', 'tags' => ['PHP', 'Laravel', 'MySQL', 'API'], 'difficulty' => 4, 'duration' => '7-9 months', 'nodes' => 14],
            ['title' => 'Fullstack Developer', 'desc' => 'Complete web development — frontend + backend + deployment. The most in-demand role.', 'icon' => 'fas fa-layer-group', 'gradient' => 'linear-gradient(135deg, #a29bfe, #6c5ce7)', 'tags' => ['HTML/CSS', 'JS', 'PHP', 'DB', 'DevOps'], 'difficulty' => 5, 'duration' => '10-12 months', 'nodes' => 20],
            ['title' => 'DevOps Engineer', 'desc' => 'Git, Docker, Kubernetes, CI/CD — infrastructure, automation and deployment.', 'icon' => 'fas fa-server', 'gradient' => 'linear-gradient(135deg, #00cec9, #0984e3)', 'tags' => ['Git', 'Docker', 'K8s', 'CI/CD'], 'difficulty' => 4, 'duration' => '5-7 months', 'nodes' => 10],
            ['title' => 'Python Developer', 'desc' => 'Python, Django, Flask, Data Science — versatile programming for any field.', 'icon' => 'fab fa-python', 'gradient' => 'linear-gradient(135deg, #fdcb6e, #e17055)', 'tags' => ['Python', 'Django', 'Flask', 'Data'], 'difficulty' => 3, 'duration' => '6-8 months', 'nodes' => 11],
            ['title' => 'UI/UX Designer', 'desc' => 'Figma, User Research, Prototyping, Design Systems — create beautiful experiences.', 'icon' => 'fas fa-palette', 'gradient' => 'linear-gradient(135deg, #fd79a8, #e84393)', 'tags' => ['Figma', 'Design', 'UX', 'Prototyping'], 'difficulty' => 2, 'duration' => '4-6 months', 'nodes' => 8],
            ['title' => 'Mobile Developer', 'desc' => 'React Native, Flutter — build cross-platform mobile apps for iOS and Android.', 'icon' => 'fas fa-mobile-alt', 'gradient' => 'linear-gradient(135deg, #55efc4, #00b894)', 'tags' => ['React Native', 'Flutter', 'iOS', 'Android'], 'difficulty' => 4, 'duration' => '6-8 months', 'nodes' => 10],
            ['title' => 'C++ Developer', 'desc' => 'System programming, algorithms, game engines, embedded systems.', 'icon' => 'fas fa-microchip', 'gradient' => 'linear-gradient(135deg, #636e72, #2d3436)', 'tags' => ['C++', 'Algorithms', 'OOP', 'STL'], 'difficulty' => 5, 'duration' => '8-10 months', 'nodes' => 13],
        ];
        @endphp

        <button class="rm-arrow rm-arrow-left" onclick="document.getElementById('rmCarousel').scrollBy({left:-400,behavior:'smooth'})"><i class="fas fa-chevron-left"></i></button>
        <div class="rm-carousel" id="rmCarousel">
        @foreach($roadmaps as $rm)
        @php
        $id = $loop->index + 100;
        $iconRotate = ($id * 15) % 360;
        $iconScale = 0.8 + ($id % 3) * 0.15;
        @endphp
        <div class="rm-card reveal-up" data-stagger="{{ $loop->index }}" style="background:var(--card);border:1px solid var(--border);border-radius:20px;overflow:hidden;scroll-snap-align:start;position:relative">
            <div class="h-44 relative overflow-hidden" style="background:{{ $rm['gradient'] }}">
                <div style="position:absolute;inset:0;background:radial-gradient(circle at 30% 70%, rgba(255,255,255,0.2) 0%, transparent 50%),radial-gradient(circle at 70% 30%, rgba(255,255,255,0.15) 0%, transparent 50%)"></div>
                <div class="rm-card-cover-overlay"></div>
                <div class="rm-card-shine"></div>
                <div class="rm-card-glow" style="background:{{ $rm['gradient'] }}"></div>
                <i class="{{ $rm['icon'] }} rm-card-icon" style="position:absolute;right:20px;bottom:20px;font-size:64px;color:rgba(255,255,255,0.18);z-index:2;text-shadow:0 4px 20px rgba(0,0,0,0.1)"></i>
                <div class="absolute top-3 left-3 z-10">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-xs font-medium rounded-full">
                        @for($i = 1; $i <= 5; $i++)
                        <span style="display:inline-block;width:6px;height:6px;border-radius:50%;margin-right:2px;background:{{ $i <= $rm['difficulty'] ? 'white' : 'rgba(255,255,255,0.3)' }}"></span>
                        @endfor
                    </span>
                </div>
                <div class="absolute top-3 right-3 z-10">
                    <span class="px-3 py-1 bg-white text-xs font-bold rounded-full shadow" style="color:var(--accent)">{{ __($rm['duration']) }}</span>
                </div>
            </div>
            <div class="p-5">
                <h3 class="text-lg font-bold mb-2 transition" style="color:var(--text)">{{ $rm['title'] }}</h3>
                <p class="text-sm mb-4 line-clamp-2" style="color:var(--text-muted)">{{ $rm['desc'] }}</p>
                <div class="flex flex-wrap gap-1.5 mb-4">
                    @foreach($rm['tags'] as $tag)
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background:var(--accent-glow);color:var(--accent)">{{ $tag }}</span>
                    @endforeach
                </div>
                <div class="flex items-center justify-between text-xs mb-4" style="color:var(--text-muted)">
                    <span><i class="fas fa-project-diagram mr-1"></i>{{ $rm['nodes'] }} {{ __('nodes') }}</span>
                </div>
                <a href="{{ route('roadmap.show', $rm['title']) }}" class="block w-full py-2.5 text-center text-sm font-semibold rounded-xl transition-all duration-300" style="color:var(--accent);border:2px solid var(--border)" onmouseover="this.style.background='var(--accent)';this.style.color='white';this.style.borderColor='var(--accent)'" onmouseout="this.style.background='';this.style.color='var(--accent)';this.style.borderColor='var(--border)'">
                    {{ __('Start Learning') }}
                </a>
            </div>
        </div>
        @endforeach
        </div>
        <button class="rm-arrow rm-arrow-right" onclick="document.getElementById('rmCarousel').scrollBy({left:400,behavior:'smooth'})"><i class="fas fa-chevron-right"></i></button>
    </div>

    <div class="mt-16">
        <h2 class="text-2xl font-bold text-center" style="color:var(--text)">{{ __('Why Our Roadmaps?') }}</h2>
        <p class="text-center mt-2 mb-8" style="color:var(--text-muted)">{{ __('Designed by industry experts to take you from beginner to professional.') }}</p>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center p-8 rounded-2xl" style="background:var(--card);border:1px solid var(--border)">
                <div class="w-14 h-14 rounded-xl mx-auto mb-4 flex items-center justify-center" style="background:var(--accent-glow)">
                    <i class="fas fa-route text-xl" style="color:var(--accent)"></i>
                </div>
                <h3 class="font-bold mb-2" style="color:var(--text)">{{ __('Structured Path') }}</h3>
                <p class="text-sm" style="color:var(--text-muted)">{{ __('Step-by-step progression from basics to advanced topics.') }}</p>
            </div>
            <div class="text-center p-8 rounded-2xl" style="background:var(--card);border:1px solid var(--border)">
                <div class="w-14 h-14 rounded-xl mx-auto mb-4 flex items-center justify-center" style="background:var(--accent-glow)">
                    <i class="fas fa-code text-xl" style="color:var(--accent)"></i>
                </div>
                <h3 class="font-bold mb-2" style="color:var(--text)">{{ __('Hands-on Practice') }}</h3>
                <p class="text-sm" style="color:var(--text-muted)">{{ __('Real projects and coding exercises at every step.') }}</p>
            </div>
            <div class="text-center p-8 rounded-2xl" style="background:var(--card);border:1px solid var(--border)">
                <div class="w-14 h-14 rounded-xl mx-auto mb-4 flex items-center justify-center" style="background:var(--accent-glow)">
                    <i class="fas fa-certificate text-xl" style="color:var(--accent)"></i>
                </div>
                <h3 class="font-bold mb-2" style="color:var(--text)">{{ __('Certificates') }}</h3>
                <p class="text-sm" style="color:var(--text-muted)">{{ __('Earn certificates as you complete each milestone.') }}</p>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const c = document.getElementById('rmCarousel');
    if(!c) return;
    let isDown = false, startX, scrollLeft, vel = 0, raf = null;

    c.addEventListener('mousedown', e => {
        isDown = true;
        c.style.cursor = 'grabbing';
        startX = e.pageX - c.offsetLeft;
        scrollLeft = c.scrollLeft;
        vel = 0;
        if(raf) cancelAnimationFrame(raf);
    });
    c.addEventListener('mouseleave', () => { isDown = false; c.style.cursor = 'grab'; });
    c.addEventListener('mouseup', () => {
        isDown = false;
        c.style.cursor = 'grab';
        function momentum() {
            if(Math.abs(vel) < 0.5) return;
            c.scrollLeft -= vel;
            vel *= 0.95;
            raf = requestAnimationFrame(momentum);
        }
        momentum();
    });
    c.addEventListener('mousemove', e => {
        if(!isDown) return;
        e.preventDefault();
        const x = e.pageX - c.offsetLeft;
        const walk = (x - startX) * 1.5;
        vel = (x - startX) * 0.3;
        c.scrollLeft = scrollLeft - walk;
    });

    let touchStartX, touchScrollLeft, touchVel = 0, touchRaf = null;
    c.addEventListener('touchstart', e => {
        touchStartX = e.touches[0].pageX - c.offsetLeft;
        touchScrollLeft = c.scrollLeft;
        touchVel = 0;
        if(touchRaf) cancelAnimationFrame(touchRaf);
    }, {passive: true});
    c.addEventListener('touchend', () => {
        function momentum() {
            if(Math.abs(touchVel) < 0.5) return;
            c.scrollLeft -= touchVel;
            touchVel *= 0.95;
            touchRaf = requestAnimationFrame(momentum);
        }
        momentum();
    });
    c.addEventListener('touchmove', e => {
        const x = e.touches[0].pageX - c.offsetLeft;
        touchVel = (x - touchStartX) * 0.3;
        c.scrollLeft = touchScrollLeft - (x - touchStartX) * 1.2;
    }, {passive: true});
})();
</script>
@endsection
