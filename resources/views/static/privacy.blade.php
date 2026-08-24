@extends('layouts.app')

@section('title', __('privacy.page_title') . ' - CodeMaster')

@section('head')
<style>
    .sp-hero {
        position: relative;
        overflow: hidden;
        padding: 8rem 2rem 6rem;
        background: var(--gradient);
        text-align: center;
    }
    .sp-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 50%, var(--accent-glow) 0%, transparent 50%),
            radial-gradient(circle at 80% 50%, var(--accent-glow-strong) 0%, transparent 50%);
        opacity: 0.3;
    }
    .sp-hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        animation: sp-orb-float 8s ease-in-out infinite;
    }
    .sp-hero-orb--1 {
        width: 400px;
        height: 400px;
        background: var(--accent);
        top: -100px;
        left: -100px;
        opacity: 0.15;
    }
    .sp-hero-orb--2 {
        width: 300px;
        height: 300px;
        background: var(--accent-2);
        bottom: -80px;
        right: -80px;
        opacity: 0.12;
        animation-delay: 3s;
    }
    .sp-hero-orb--3 {
        width: 200px;
        height: 200px;
        background: var(--accent-3);
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.1;
        animation-delay: 5s;
    }
    @keyframes sp-orb-float {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-30px) scale(1.05); }
    }
    .sp-hero-shield {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 600px;
        height: 600px;
        opacity: 0.04;
        z-index: 1;
        pointer-events: none;
    }
    .sp-hero-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        margin: 0 auto;
    }
    .sp-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        background: var(--bg-elevated);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--accent);
        margin-bottom: 2rem;
        backdrop-filter: blur(12px);
    }
    .sp-hero-title {
        font-size: clamp(2.5rem, 6vw, 4rem);
        font-weight: 800;
        color: var(--text);
        line-height: 1.1;
        margin: 0 0 1.25rem;
        letter-spacing: -0.02em;
    }
    .sp-hero-subtitle {
        font-size: 1.1rem;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.6;
    }
    .sp-hero-date {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
        font-size: 0.9rem;
        color: var(--text-muted);
    }
    .sp-layout {
        max-width: 1200px;
        margin: 0 auto;
        padding: 4rem 2rem;
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 4rem;
        align-items: start;
    }
    .sp-toc {
        position: sticky;
        top: 2rem;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
    }
    .sp-toc-title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        margin: 0 0 1rem;
    }
    .sp-toc-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .sp-toc-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 0.75rem;
        border-radius: var(--radius);
        text-decoration: none;
        color: var(--text-secondary);
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    .sp-toc-link:hover {
        background: var(--bg-3);
        color: var(--text);
        border-color: var(--border);
    }
    .sp-toc-num {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: var(--radius-sm);
        background: var(--bg-3);
        color: var(--text-muted);
        font-size: 0.7rem;
        font-weight: 700;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    .sp-toc-link:hover .sp-toc-num {
        background: var(--accent);
        color: var(--bg);
    }
    .sp-content {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    .sp-section {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2.5rem;
        transition: all 0.3s ease;
    }
    .sp-section:hover {
        border-color: var(--border-hover);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }
    .sp-section-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .sp-section-num {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        border-radius: var(--radius-md);
        background: var(--gradient);
        color: var(--bg);
        font-size: 1.25rem;
        font-weight: 800;
        flex-shrink: 0;
        box-shadow: 0 4px 16px var(--accent-glow);
    }
    .sp-section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }
    .sp-section-body {
        color: var(--text-secondary);
        font-size: 1rem;
        line-height: 1.8;
    }
    .sp-section-body p {
        margin: 0 0 1rem;
    }
    .sp-section-body p:last-child {
        margin-bottom: 0;
    }
    .sp-section-body ul {
        margin: 0.5rem 0 1rem;
        padding-left: 1.5rem;
    }
    .sp-section-body li {
        margin-bottom: 0.5rem;
    }
    .sp-section-body strong {
        color: var(--text);
        font-weight: 600;
    }
    @media (max-width: 900px) {
        .sp-layout {
            grid-template-columns: 1fr;
            gap: 2rem;
            padding: 2rem 1rem;
        }
        .sp-toc {
            position: static;
        }
        .sp-section {
            padding: 1.75rem;
        }
        .sp-hero {
            padding: 5rem 1.5rem 4rem;
        }
    }
</style>
@endsection

@section('content')
<section class="sp-hero">
    <div class="sp-hero-orb sp-hero-orb--1"></div>
    <div class="sp-hero-orb sp-hero-orb--2"></div>
    <div class="sp-hero-orb sp-hero-orb--3"></div>

    <svg class="sp-hero-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
    </svg>

    <div class="sp-hero-content">
        <div class="sp-hero-badge">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            {{ __('privacy.hero_badge') }}
        </div>
        <h1 class="sp-hero-title">{{ __('privacy.hero_title') }}</h1>
        <p class="sp-hero-subtitle">{{ __('privacy.hero_subtitle') }}</p>
        <div class="sp-hero-date">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            {{ __('privacy.hero_date') }}
        </div>
    </div>
</section>

<div class="sp-layout">
    <nav class="sp-toc">
        <h2 class="sp-toc-title">{{ __('privacy.toc_title') }}</h2>
        <ul class="sp-toc-list">
            <li><a href="#section-1" class="sp-toc-link"><span class="sp-toc-num">1</span> {{ __('privacy.toc_item_1') }}</a></li>
            <li><a href="#section-2" class="sp-toc-link"><span class="sp-toc-num">2</span> {{ __('privacy.toc_item_2') }}</a></li>
            <li><a href="#section-3" class="sp-toc-link"><span class="sp-toc-num">3</span> {{ __('privacy.toc_item_3') }}</a></li>
            <li><a href="#section-4" class="sp-toc-link"><span class="sp-toc-num">4</span> {{ __('privacy.toc_item_4') }}</a></li>
            <li><a href="#section-5" class="sp-toc-link"><span class="sp-toc-num">5</span> {{ __('privacy.toc_item_5') }}</a></li>
            <li><a href="#section-6" class="sp-toc-link"><span class="sp-toc-num">6</span> {{ __('privacy.toc_item_6') }}</a></li>
            <li><a href="#section-7" class="sp-toc-link"><span class="sp-toc-num">7</span> {{ __('privacy.toc_item_7') }}</a></li>
            <li><a href="#section-8" class="sp-toc-link"><span class="sp-toc-num">8</span> {{ __('privacy.toc_item_8') }}</a></li>
        </ul>
    </nav>

    <div class="sp-content">
        <section id="section-1" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">1</div>
                <h2 class="sp-section-title">{{ __('privacy.section_1_title') }}</h2>
            </div>
            <div class="sp-section-body">
                <p>{{ __('privacy.section_1_para_1') }}</p>
                <ul>
                    <li>{!! __('privacy.section_1_item_1') !!}</li>
                    <li>{!! __('privacy.section_1_item_2') !!}</li>
                    <li>{!! __('privacy.section_1_item_3') !!}</li>
                    <li>{!! __('privacy.section_1_item_4') !!}</li>
                </ul>
                <p>{{ __('privacy.section_1_para_2') }}</p>
            </div>
        </section>

        <section id="section-2" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">2</div>
                <h2 class="sp-section-title">{{ __('privacy.section_2_title') }}</h2>
            </div>
            <div class="sp-section-body">
                <p>{{ __('privacy.section_2_para_1') }}</p>
                <ul>
                    <li>{!! __('privacy.section_2_item_1') !!}</li>
                    <li>{!! __('privacy.section_2_item_2') !!}</li>
                    <li>{!! __('privacy.section_2_item_3') !!}</li>
                    <li>{!! __('privacy.section_2_item_4') !!}</li>
                    <li>{!! __('privacy.section_2_item_5') !!}</li>
                </ul>
                <p>{{ __('privacy.section_2_para_2') }}</p>
            </div>
        </section>

        <section id="section-3" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">3</div>
                <h2 class="sp-section-title">{{ __('privacy.section_3_title') }}</h2>
            </div>
            <div class="sp-section-body">
                <p>{{ __('privacy.section_3_para_1') }}</p>
                <p>{{ __('privacy.section_3_para_2') }}</p>
                <ul>
                    <li>{!! __('privacy.section_3_item_1') !!}</li>
                    <li>{!! __('privacy.section_3_item_2') !!}</li>
                    <li>{{ __('privacy.section_3_item_3') }}</li>
                </ul>
                <p>{{ __('privacy.section_3_para_3') }}</p>
            </div>
        </section>

        <section id="section-4" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">4</div>
                <h2 class="sp-section-title">{{ __('privacy.section_4_title') }}</h2>
            </div>
            <div class="sp-section-body">
                <p>{{ __('privacy.section_4_para_1') }}</p>
                <ul>
                    <li>{!! __('privacy.section_4_item_1') !!}</li>
                    <li>{!! __('privacy.section_4_item_2') !!}</li>
                    <li>{!! __('privacy.section_4_item_3') !!}</li>
                </ul>
                <p>{{ __('privacy.section_4_para_2') }}</p>
            </div>
        </section>

        <section id="section-5" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">5</div>
                <h2 class="sp-section-title">Cookies</h2>
            </div>
            <div class="sp-section-body">
                <p>{{ __('privacy.section_5_para_1') }}</p>
                <ul>
                    <li>{!! __('privacy.section_5_item_1') !!}</li>
                    <li>{!! __('privacy.section_5_item_2') !!}</li>
                    <li>{!! __('privacy.section_5_item_3') !!}</li>
                </ul>
                <p>{{ __('privacy.section_5_para_2') }}</p>
            </div>
        </section>

        <section id="section-6" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">6</div>
                <h2 class="sp-section-title">{{ __('privacy.section_6_title') }}</h2>
            </div>
            <div class="sp-section-body">
                <p>{{ __('privacy.section_6_para_1') }}</p>
                <ul>
                    <li>{!! __('privacy.section_6_item_1') !!}</li>
                    <li>{!! __('privacy.section_6_item_2') !!}</li>
                    <li>{!! __('privacy.section_6_item_3') !!}</li>
                    <li>{!! __('privacy.section_6_item_4') !!}</li>
                    <li>{!! __('privacy.section_6_item_5') !!}</li>
                    <li>{!! __('privacy.section_6_item_6') !!}</li>
                </ul>
                <p>{!! __('privacy.section_6_para_2') !!}</p>
            </div>
        </section>

        <section id="section-7" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">7</div>
                <h2 class="sp-section-title">{{ __('privacy.section_7_title') }}</h2>
            </div>
            <div class="sp-section-body">
                <p>{{ __('privacy.section_7_para_1') }}</p>
                <ul>
                    <li>{!! __('privacy.section_7_item_1') !!}</li>
                    <li>{!! __('privacy.section_7_item_2') !!}</li>
                    <li>{!! __('privacy.section_7_item_3') !!}</li>
                    <li>{!! __('privacy.section_7_item_4') !!}</li>
                </ul>
                <p>{{ __('privacy.section_7_para_2') }}</p>
            </div>
        </section>

        <section id="section-8" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">8</div>
                <h2 class="sp-section-title">{{ __('privacy.section_8_title') }}</h2>
            </div>
            <div class="sp-section-body">
                <p>{{ __('privacy.section_8_para_1') }}</p>
                <ul>
                    <li>{!! __('privacy.section_8_item_1') !!}</li>
                    <li>{!! __('privacy.section_8_item_2') !!}</li>
                </ul>
                <p>{!! __('privacy.section_8_para_2') !!}</p>
                <p>{{ __('privacy.section_8_para_3') }}</p>
            </div>
        </section>
    </div>
</div>
@endsection
