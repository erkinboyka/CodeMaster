@extends('layouts.app')

@section('title', 'О нас - CodeMaster')

@section('head')
<style>
    .sp-hero {
        position: relative;
        overflow: hidden;
        padding: 6rem 2rem 5rem;
        background: linear-gradient(135deg, var(--bg) 0%, var(--bg-2) 50%, var(--bg) 100%);
        text-align: center;
    }

    .sp-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(ellipse at 30% 50%, var(--accent-glow) 0%, transparent 50%),
                    radial-gradient(ellipse at 70% 50%, var(--accent-glow) 0%, transparent 50%);
        opacity: 0.15;
        animation: sp-hero-pulse 8s ease-in-out infinite alternate;
    }

    @keyframes sp-hero-pulse {
        0% { transform: scale(1) rotate(0deg); opacity: 0.12; }
        100% { transform: scale(1.1) rotate(3deg); opacity: 0.2; }
    }

    .sp-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.25;
        animation: sp-orb-float 12s ease-in-out infinite;
        pointer-events: none;
    }

    .sp-orb--1 {
        width: 300px;
        height: 300px;
        background: var(--accent);
        top: -80px;
        right: -60px;
        animation-duration: 14s;
    }

    .sp-orb--2 {
        width: 220px;
        height: 220px;
        background: var(--accent-2);
        bottom: -60px;
        left: -40px;
        animation-duration: 10s;
        animation-delay: -3s;
    }

    .sp-orb--3 {
        width: 160px;
        height: 160px;
        background: var(--accent-3);
        top: 40%;
        left: 50%;
        animation-duration: 16s;
        animation-delay: -6s;
    }

    @keyframes sp-orb-float {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(30px, -20px) scale(1.05); }
        50% { transform: translate(-20px, 30px) scale(0.95); }
        75% { transform: translate(20px, 15px) scale(1.03); }
    }

    .sp-hero__content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        margin: 0 auto;
    }

    .sp-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        border-radius: var(--radius-xl);
        background: var(--card);
        border: 1px solid var(--border);
        color: var(--accent);
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        margin-bottom: 1.5rem;
        backdrop-filter: blur(12px);
    }

    .sp-hero__title {
        font-size: clamp(2.5rem, 6vw, 4rem);
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 1.25rem;
        background: linear-gradient(135deg, var(--text) 0%, var(--accent) 50%, var(--accent-2) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .sp-hero__subtitle {
        font-size: 1.2rem;
        color: var(--text-secondary);
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .sp-section {
        padding: 5rem 2rem;
    }

    .sp-section__inner {
        max-width: 1200px;
        margin: 0 auto;
    }

    .sp-section__label {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 1rem;
        border-radius: var(--radius-xl);
        background: var(--card);
        border: 1px solid var(--border);
        color: var(--accent);
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 1rem;
    }

    .sp-section__title {
        font-size: clamp(1.8rem, 4vw, 2.5rem);
        font-weight: 800;
        color: var(--text);
        margin-bottom: 0.75rem;
        line-height: 1.2;
    }

    .sp-section__desc {
        font-size: 1.05rem;
        color: var(--text-secondary);
        max-width: 600px;
        line-height: 1.7;
    }

    .sp-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        align-items: start;
    }

    @media (max-width: 768px) {
        .sp-grid-2 { grid-template-columns: 1fr; }
    }

    .sp-mission-text {
        padding-top: 1rem;
    }

    .sp-mission-text p {
        color: var(--text-secondary);
        line-height: 1.8;
        margin-bottom: 1.25rem;
        font-size: 1rem;
    }

    .sp-stats-card {
        background: var(--gradient);
        border-radius: var(--radius-lg);
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .sp-stats-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%);
        pointer-events: none;
    }

    .sp-stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        position: relative;
        z-index: 1;
    }

    .sp-stat {
        text-align: center;
        padding: 1.5rem 1rem;
        border-radius: var(--radius-md);
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), background 0.35s ease;
    }

    .sp-stat:hover {
        transform: translateY(-4px) scale(1.02);
        background: rgba(255, 255, 255, 0.18);
    }

    .sp-stat__number {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.4rem;
        background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.8) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .sp-stat__label {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.75);
        font-weight: 500;
    }

    .sp-values-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-top: 3rem;
    }

    @media (max-width: 900px) {
        .sp-values-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 560px) {
        .sp-values-grid { grid-template-columns: 1fr; }
    }

    .sp-value-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem 1.5rem;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }

    .sp-value-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, var(--accent-glow) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }

    .sp-value-card:hover {
        transform: translateY(-6px);
        border-color: var(--border-hover);
        box-shadow: 0 12px 40px -8px rgba(0, 0, 0, 0.15),
                    0 0 0 1px var(--border-hover);
    }

    .sp-value-card:hover::before {
        opacity: 1;
    }

    .sp-value-card__icon {
        width: 64px;
        height: 64px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 1.5rem;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
        color: #fff;
        position: relative;
        z-index: 1;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .sp-value-card:hover .sp-value-card__icon {
        transform: scale(1.1) rotate(-3deg);
    }

    .sp-value-card__title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.6rem;
        position: relative;
        z-index: 1;
    }

    .sp-value-card__desc {
        font-size: 0.9rem;
        color: var(--text-secondary);
        line-height: 1.6;
        position: relative;
        z-index: 1;
    }

    .sp-timeline {
        position: relative;
        margin-top: 3rem;
        padding-left: 40px;
    }

    .sp-timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, var(--accent), var(--accent-2), var(--accent-3));
        border-radius: 1px;
    }

    .sp-timeline__item {
        position: relative;
        margin-bottom: 2.5rem;
        padding-left: 2rem;
    }

    .sp-timeline__item:last-child {
        margin-bottom: 0;
    }

    .sp-timeline__dot {
        position: absolute;
        left: -32px;
        top: 6px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: var(--accent);
        border: 3px solid var(--bg-2);
        box-shadow: 0 0 0 2px var(--accent), 0 0 12px var(--accent-glow);
        z-index: 1;
    }

    .sp-timeline__year {
        display: inline-block;
        padding: 0.25rem 0.85rem;
        border-radius: var(--radius-xl);
        background: var(--card);
        border: 1px solid var(--border);
        color: var(--accent);
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 0.6rem;
    }

    .sp-timeline__title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.35rem;
    }

    .sp-timeline__desc {
        font-size: 0.92rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .sp-team-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.75rem;
        margin-top: 3rem;
    }

    @media (max-width: 768px) {
        .sp-team-grid {
            grid-template-columns: 1fr;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
    }

    .sp-team-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem 1.5rem;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .sp-team-card:hover {
        transform: translateY(-8px);
        border-color: var(--border-hover);
        box-shadow: 0 16px 48px -12px rgba(0, 0, 0, 0.15);
    }

    .sp-team-card__avatar {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 1.6rem;
        font-weight: 800;
        color: #fff;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
        box-shadow: 0 8px 24px -4px var(--accent-glow-strong);
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease;
    }

    .sp-team-card:hover .sp-team-card__avatar {
        transform: scale(1.08);
        box-shadow: 0 12px 32px -4px var(--accent-glow-strong);
    }

    .sp-team-card__name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.3rem;
    }

    .sp-team-card__role {
        font-size: 0.88rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .sp-cta {
        margin-top: 3rem;
        background: var(--gradient);
        border-radius: var(--radius-xl);
        padding: 4rem 2.5rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .sp-cta::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at 30% 50%, rgba(255,255,255,0.12) 0%, transparent 50%);
        pointer-events: none;
    }

    .sp-cta__title {
        font-size: clamp(1.6rem, 3.5vw, 2.2rem);
        font-weight: 800;
        color: #fff;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
    }

    .sp-cta__desc {
        font-size: 1.05rem;
        color: rgba(255, 255, 255, 0.8);
        max-width: 550px;
        margin: 0 auto 2rem;
        line-height: 1.7;
        position: relative;
        z-index: 1;
    }

    .sp-cta__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.9rem 2.2rem;
        border-radius: var(--radius-xl);
        background: #fff;
        color: var(--accent);
        font-size: 1rem;
        font-weight: 700;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        z-index: 1;
    }

    .sp-cta__btn:hover {
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.25);
    }

    .sp-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, var(--border), transparent);
        margin: 0;
        border: none;
    }
</style>
@endsection

@section('content')

<section class="sp-hero">
    <div class="sp-orb sp-orb--1"></div>
    <div class="sp-orb sp-orb--2"></div>
    <div class="sp-orb sp-orb--3"></div>
    <div class="sp-hero__content">
        <div class="sp-badge">
            <i class="fas fa-graduation-cap"></i>
            {{ __('Образовательная платформа') }}
        </div>
        <h1 class="sp-hero__title">{{ __('О CodeMaster') }}</h1>
        <p class="sp-hero__subtitle">{{ __('Мы создаём будущее IT-образования, делая качественные знания доступными для каждого разработчика по всему миру.') }}</p>
    </div>
</section>

<hr class="sp-divider">

<section class="sp-section">
    <div class="sp-section__inner">
        <div class="sp-grid-2">
            <div class="sp-mission-text reveal-up">
                <div class="sp-section__label">
                    <i class="fas fa-bullseye"></i>
                    {{ __('Наша миссия') }}
                </div>
                <h2 class="sp-section__title">{{ __('Строим будущее IT-образования') }}</h2>
                <p>{{ __('CodeMaster был основан с простой миссией: сделать качественное IT-образование доступным для каждого. Мы верим, что талант есть везде, и людям нужно лишь подходящее возможность для обучения и роста.') }}</p>
                <p>{{ __('Наша платформа сочетает курсы, дорожные карты, соревнования и возможности трудоустройства, создавая полноценную экосистему для разработчиков на любом этапе карьеры.') }}</p>
            </div>
            <div class="reveal-scale">
                <div class="sp-stats-card">
                    <div class="sp-stats-grid">
                        <div class="sp-stat">
                            <p class="sp-stat__number">15K+</p>
                            <p class="sp-stat__label">{{ __('Студентов') }}</p>
                        </div>
                        <div class="sp-stat">
                            <p class="sp-stat__number">200+</p>
                            <p class="sp-stat__label">{{ __('Курсов') }}</p>
                        </div>
                        <div class="sp-stat">
                            <p class="sp-stat__number">500+</p>
                            <p class="sp-stat__label">{{ __('Трудоустройств') }}</p>
                        </div>
                        <div class="sp-stat">
                            <p class="sp-stat__number">50+</p>
                            <p class="sp-stat__label">{{ __('Стран') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<hr class="sp-divider">

<section class="sp-section">
    <div class="sp-section__inner">
        <div style="text-align:center; margin-bottom:1rem;" class="reveal-up">
            <div class="sp-section__label" style="justify-content:center;">
                <i class="fas fa-heart"></i>
                {{ __('Наши ценности') }}
            </div>
            <h2 class="sp-section__title" style="text-align:center;">{{ __('Принципы, которые нас направляют') }}</h2>
            <p class="sp-section__desc" style="margin:0 auto;">{{ __('Каждое решение мы принимаем, опираясь на эти фундаментальные ценности.') }}</p>
        </div>
        <div class="sp-values-grid">
            @foreach([
                ['icon' => 'fa-globe', 'title' => 'Доступность', 'desc' => 'Образование должно быть доступно каждому, независимо от местоположения, возраста или финансового положения.'],
                ['icon' => 'fa-award', 'title' => 'Качество', 'desc' => 'Мы не идём на компромиссы в качестве контента и методик преподавания.'],
                ['icon' => 'fa-users', 'title' => 'Сообщество', 'desc' => 'Сила платформы — в активном сообществе разработчиков, делящихся опытом.'],
                ['icon' => 'fa-hands-on', 'title' => 'Практика', 'desc' => 'Теория без практики мертва. Мы делаем акцент на реальных проектах.'],
                ['icon' => 'fa-rocket', 'title' => 'Карьера', 'desc' => 'Наша цель — помочь каждому студенту найти работу мечты в IT.'],
                ['icon' => 'fa-lightbulb', 'title' => 'Инновации', 'desc' => 'Мы постоянно ищем новые методы обучения и внедряем лучшие практики.']
            ] as $idx => $value)
            <div class="sp-value-card reveal-up" data-stagger="{{ $idx }}">
                <div class="sp-value-card__icon">
                    <i class="fas {{ $value['icon'] }}"></i>
                </div>
                <h3 class="sp-value-card__title">{{ __($value['title']) }}</h3>
                <p class="sp-value-card__desc">{{ __($value['desc']) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<hr class="sp-divider">

<section class="sp-section">
    <div class="sp-section__inner">
        <div class="sp-grid-2">
            <div class="reveal-up">
                <div class="sp-section__label">
                    <i class="fas fa-history"></i>
                    {{ __('Наша история') }}
                </div>
                <h2 class="sp-section__title">{{ __('Путь развития') }}</h2>
                <p class="sp-section__desc">{{ __('От небольшого стартапа до международной образовательной платформы.') }}</p>
            </div>
            <div class="reveal-up">
                <div class="sp-timeline">
                    @foreach([
                        ['year' => '2022', 'title' => 'Основание компании', 'desc' => 'Запуск платформы CodeMaster с первыми 10 курсами и группой из 50 энтузиастов.'],
                        ['year' => '2023', 'title' => 'Быстрый рост', 'desc' => 'Достижение отметки в 5000 студентов и запуск программы трудоустройства.'],
                        ['year' => '2024', 'title' => 'Международный запуск', 'desc' => 'Выход на рынки СНГ и Юго-Восточной Азии, более 100 курсов в каталоге.'],
                        ['year' => '2025', 'title' => 'Партнёрская сеть', 'desc' => 'Заключение партнёрств с 200+ IT-компаниями для трудоустройства выпускников.'],
                        ['year' => '2026', 'title' => 'Новая эра', 'desc' => 'Запуск ИИ-платформы для персонализированного обучения и менторства.']
                    ] as $idx => $milestone)
                    <div class="sp-timeline__item reveal-left" data-stagger="{{ $idx }}">
                        <div class="sp-timeline__dot"></div>
                        <div class="sp-timeline__year">{{ $milestone['year'] }}</div>
                        <h3 class="sp-timeline__title">{{ __($milestone['title']) }}</h3>
                        <p class="sp-timeline__desc">{{ __($milestone['desc']) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<hr class="sp-divider">

<section class="sp-section">
    <div class="sp-section__inner">
        <div style="text-align:center;" class="reveal-up">
            <div class="sp-section__label" style="justify-content:center;">
                <i class="fas fa-user-friends"></i>
                {{ __('Наша команда') }}
            </div>
            <h2 class="sp-section__title" style="text-align:center;">{{ __('Люди, создающие будущее') }}</h2>
            <p class="sp-section__desc" style="margin:0 auto;">{{ __('Познакомьтесь с людьми, которые стоят за CodeMaster.') }}</p>
        </div>
        <div class="sp-team-grid">
            @foreach([
                ['name' => 'Алекс Ким', 'role' => 'Генеральный директор', 'initials' => 'АК'],
                ['name' => 'Сара Джонсон', 'role' => 'Технический директор', 'initials' => 'СД'],
                ['name' => 'Майк Чен', 'role' => 'Руководитель образования', 'initials' => 'МЧ']
            ] as $idx => $member)
            <div class="sp-team-card reveal-up" data-stagger="{{ $idx }}">
                <div class="sp-team-card__avatar">
                    {{ $member['initials'] }}
                </div>
                <h3 class="sp-team-card__name">{{ __($member['name']) }}</h3>
                <p class="sp-team-card__role">{{ __($member['role']) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="sp-section">
    <div class="sp-section__inner">
        <div class="sp-cta reveal-scale">
            <h2 class="sp-cta__title">{{ __('Присоединяйтесь к CodeMaster') }}</h2>
            <p class="sp-cta__desc">{{ __('Начните свой путь в IT уже сегодня. Более 15 000 студентов уже изменили свою жизнь вместе с нами.') }}</p>
            <a href="{{ route('courses.index') }}" class="sp-cta__btn">
                <i class="fas fa-arrow-right"></i>
                {{ __('Начать обучение') }}
            </a>
        </div>
    </div>
</section>

@endsection