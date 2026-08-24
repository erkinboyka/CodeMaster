@extends('layouts.app')

@section('title', __('page_contacts_title') . ' - CodeMaster')

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
        inset: -50%;
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
        width: 320px; height: 320px;
        background: var(--accent);
        top: -90px; right: -70px;
        animation-duration: 14s;
    }
    .sp-orb--2 {
        width: 240px; height: 240px;
        background: var(--accent-2);
        bottom: -70px; left: -50px;
        animation-duration: 10s;
        animation-delay: -3s;
    }
    .sp-orb--3 {
        width: 180px; height: 180px;
        background: var(--accent-3);
        top: 40%; left: 50%;
        animation-duration: 16s;
        animation-delay: -6s;
    }
    .sp-orb--4 {
        width: 140px; height: 140px;
        background: var(--accent-4);
        top: 20%; left: 15%;
        animation-duration: 18s;
        animation-delay: -9s;
        opacity: 0.15;
    }
    @keyframes sp-orb-float {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(30px, -20px) scale(1.05); }
        50% { transform: translate(-20px, 30px) scale(0.95); }
        75% { transform: translate(20px, 15px) scale(1.03); }
    }
    .sp-hero__content {
        position: relative; z-index: 2;
        max-width: 800px; margin: 0 auto;
    }
    .sp-badge {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.5rem 1.25rem; border-radius: var(--radius-xl);
        background: var(--card); border: 1px solid var(--border);
        color: var(--accent); font-size: 0.85rem; font-weight: 600;
        letter-spacing: 0.02em; margin-bottom: 1.5rem;
        backdrop-filter: blur(12px);
        animation: spFadeUp 0.7s 0.2s both;
    }
    .sp-hero__title {
        font-size: clamp(2.5rem, 6vw, 4rem); font-weight: 800;
        line-height: 1.1; margin-bottom: 1.25rem;
        background: linear-gradient(135deg, var(--text) 0%, var(--accent) 50%, var(--accent-2) 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        animation: spFadeUp 0.8s 0.4s both;
    }
    .sp-hero__subtitle {
        font-size: 1.15rem; color: var(--text-secondary);
        max-width: 580px; margin: 0 auto; line-height: 1.7;
        animation: spFadeUp 0.8s 0.6s both;
    }
    @keyframes spFadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .sp-section { padding: 5rem 2rem; }
    .sp-section__inner { max-width: 1200px; margin: 0 auto; }
    .sp-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, var(--border), transparent);
        margin: 0; border: none;
    }
    .sp-grid-2 {
        display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem;
        align-items: start;
    }
    .sp-sticky-col {
        position: sticky; top: 100px; align-self: start;
    }
    @media (max-width: 900px) {
        .sp-grid-2 { grid-template-columns: 1fr; }
    }
    .sp-section-header {
        margin-bottom: 2.5rem;
    }
    .sp-section-label {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.4rem 1rem; border-radius: var(--radius-xl);
        background: var(--card); border: 1px solid var(--border);
        color: var(--accent); font-size: 0.8rem; font-weight: 600;
        letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 1rem;
    }
    .sp-section-title {
        font-size: clamp(1.8rem, 4vw, 2.5rem); font-weight: 800;
        color: var(--text); margin-bottom: 0.75rem; line-height: 1.2;
    }
    .sp-section-desc {
        font-size: 1.05rem; color: var(--text-secondary);
        max-width: 600px; line-height: 1.7;
    }

    /* ═══ CONTACT CARDS ═══ */
    .sp-contact-list { display: flex; flex-direction: column; gap: 1.25rem; }
    .sp-contact-card {
        display: flex; gap: 1.25rem; align-items: flex-start;
        padding: 1.75rem; border-radius: var(--radius-lg);
        background: var(--card); border: 1px solid var(--border);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative; overflow: hidden;
    }
    .sp-contact-card::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(135deg, var(--accent-glow) 0%, transparent 60%);
        opacity: 0; transition: opacity 0.4s ease; pointer-events: none;
    }
    .sp-contact-card:hover {
        transform: translateY(-4px); border-color: var(--border-hover);
        box-shadow: 0 12px 40px -8px rgba(0,0,0,0.15), 0 0 0 1px var(--border-hover);
    }
    .sp-contact-card:hover::before { opacity: 1; }
    .sp-contact-card__icon {
        width: 56px; height: 56px; min-width: 56px;
        border-radius: var(--radius-md);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; color: #fff; position: relative; z-index: 1;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .sp-contact-card:hover .sp-contact-card__icon {
        transform: scale(1.1) rotate(-3deg);
    }
    .sp-contact-card__icon--email {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        box-shadow: 0 4px 20px var(--accent-glow-strong);
    }
    .sp-contact-card__icon--address {
        background: linear-gradient(135deg, var(--accent-2), var(--accent-3));
        box-shadow: 0 4px 20px rgba(124, 58, 237, 0.25);
    }
    .sp-contact-card__icon--phone {
        background: linear-gradient(135deg, var(--success), #059669);
        box-shadow: 0 4px 20px rgba(34, 197, 94, 0.25);
    }
    .sp-contact-card__icon--telegram {
        background: linear-gradient(135deg, var(--info), var(--accent));
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.25);
    }
    .sp-contact-card__body { position: relative; z-index: 1; flex: 1; }
    .sp-contact-card__title {
        font-size: 1rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;
    }
    .sp-contact-card__detail {
        font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6;
    }
    .sp-contact-card__detail a {
        color: var(--text-secondary); text-decoration: none; transition: color 0.2s;
    }
    .sp-contact-card__detail a:hover { color: var(--accent); }
    .sp-contact-card__detail + .sp-contact-card__detail { margin-top: 0.2rem; }
    .sp-contact-card__label {
        display: inline-block; font-size: 0.75rem; font-weight: 600;
        color: var(--accent); background: var(--accent-glow);
        padding: 0.2rem 0.6rem; border-radius: var(--radius-xl);
        margin-top: 0.6rem; letter-spacing: 0.02em;
    }

    /* ═══ FORM ═══ */
    .sp-form-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius-lg); padding: 2.25rem;
    }
    .sp-form-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: var(--gradient); opacity: 0.8;
    }
    .sp-form-card__title {
        font-size: 1.25rem; font-weight: 700; color: var(--text);
        margin-bottom: 1.75rem; display: flex; align-items: center; gap: 0.6rem;
    }
    .sp-form-card__title i { color: var(--accent); font-size: 1.1rem; }
    .sp-form-group { margin-bottom: 1.25rem; }
    .sp-form-row {
        display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;
    }
    @media (max-width: 560px) {
        .sp-form-row { grid-template-columns: 1fr; }
    }
    .sp-form-label {
        display: block; font-size: 0.85rem; font-weight: 600;
        color: var(--text-secondary); margin-bottom: 0.5rem;
        letter-spacing: 0.01em;
    }
    .sp-form-input, .sp-form-textarea {
        width: 100%; padding: 0.8rem 1rem; border-radius: var(--radius);
        border: 1px solid var(--border); background: var(--bg-2);
        color: var(--text); font-size: 0.9rem; font-family: inherit;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        outline: none;
    }
    .sp-form-input::placeholder, .sp-form-textarea::placeholder {
        color: var(--text-muted);
    }
    .sp-form-input:focus, .sp-form-textarea:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-glow);
        background: var(--bg-3);
    }
    .sp-form-textarea { resize: vertical; min-height: 140px; line-height: 1.7; }
    .sp-form-btn {
        width: 100%; padding: 0.95rem 2rem; border-radius: var(--radius);
        background: var(--gradient); color: #fff;
        font-size: 1rem; font-weight: 700; border: none;
        cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.6rem;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative; overflow: hidden;
    }
    .sp-form-btn::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 50%);
        opacity: 0; transition: opacity 0.3s;
    }
    .sp-form-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px var(--accent-glow-strong);
    }
    .sp-form-btn:hover::before { opacity: 1; }
    .sp-form-btn:active { transform: scale(0.98); }
    .sp-form-btn i { font-size: 0.9rem; }

    /* ═══ OFFICE HOURS ═══ */
    .sp-hours-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius-lg); padding: 2.25rem;
        position: sticky; top: 80px;
    }
    .sp-hours-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--accent), var(--accent-2), var(--accent-3));
    }
    .sp-hours-card__title {
        font-size: 1.25rem; font-weight: 700; color: var(--text);
        margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;
    }
    .sp-hours-card__title i { color: var(--accent); }
    .sp-hours-list { display: flex; flex-direction: column; gap: 0; }
    .sp-hours-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.9rem 0;
        border-bottom: 1px solid var(--border);
    }
    .sp-hours-row:last-child { border-bottom: none; }
    .sp-hours-day {
        font-size: 0.9rem; font-weight: 600; color: var(--text);
    }
    .sp-hours-time {
        font-size: 0.9rem; color: var(--text-secondary);
        font-family: 'JetBrains Mono', monospace;
    }
    .sp-hours-time--active {
        color: var(--success); font-weight: 600;
    }
    .sp-hours-time--closed {
        color: var(--danger); font-weight: 600;
    }

    /* ═══ MAP PLACEHOLDER ═══ */
    .sp-map {
        border-radius: var(--radius-lg); overflow: hidden;
        border: 1px solid var(--border);
        background: var(--bg-2);
        height: 340px; position: sticky; top: 80px;
    }
    .sp-map iframe { width: 100%; height: calc(100% + 40px); margin-bottom: -40px; border: 0; }
    .sp-map__overlay {
        position: absolute; inset: 0;
        background: linear-gradient(135deg, var(--accent-glow) 0%, transparent 40%),
                    linear-gradient(315deg, rgba(124, 58, 237, 0.06) 0%, transparent 40%);
        pointer-events: none;
    }
    .sp-map__content {
        text-align: center; position: relative; z-index: 1;
    }
    .sp-map__icon {
        width: 80px; height: 80px; border-radius: 50%;
        background: var(--card); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.25rem; font-size: 2rem;
        color: var(--accent);
        box-shadow: 0 8px 32px var(--accent-glow-strong);
    }
    .sp-map__title {
        font-size: 1.15rem; font-weight: 700; color: var(--text); margin-bottom: 0.4rem;
    }
    .sp-map__address {
        font-size: 0.9rem; color: var(--text-secondary);
    }

    /* ═══ CTA ═══ */
    .sp-cta {
        margin-top: 3rem; background: var(--gradient);
        border-radius: var(--radius-xl); padding: 4rem 2.5rem;
        text-align: center; position: relative; overflow: hidden;
    }
    .sp-cta::before {
        content: ''; position: absolute; top: -50%; left: -50%;
        width: 200%; height: 200%;
        background: radial-gradient(circle at 30% 50%, rgba(255,255,255,0.12) 0%, transparent 50%);
        pointer-events: none;
    }
    .sp-cta__title {
        font-size: clamp(1.6rem, 3.5vw, 2.2rem); font-weight: 800;
        color: #fff; margin-bottom: 1rem; position: relative; z-index: 1;
    }
    .sp-cta__desc {
        font-size: 1.05rem; color: rgba(255,255,255,0.8);
        max-width: 550px; margin: 0 auto 2rem; line-height: 1.7;
        position: relative; z-index: 1;
    }
    .sp-cta__btn {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.9rem 2.2rem; border-radius: var(--radius-xl);
        background: #fff; color: var(--accent); font-size: 1rem; font-weight: 700;
        text-decoration: none; border: none; cursor: pointer;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative; z-index: 1;
    }
    .sp-cta__btn:hover {
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 8px 28px rgba(0,0,0,0.25);
    }

    /* ═══ GRID PATTERN ═══ */
    .sp-grid-pattern {
        position: absolute; inset: 0;
        background-image:
            linear-gradient(var(--border) 1px, transparent 1px),
            linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 60px 60px; opacity: 0.15;
        mask-image: radial-gradient(ellipse at center, black 20%, transparent 65%);
        -webkit-mask-image: radial-gradient(ellipse at center, black 20%, transparent 65%);
        pointer-events: none;
    }

    /* ═══ SOCIAL ICONS ═══ */
    .sp-social-row {
        display: flex; gap: 0.75rem; margin-top: 1rem;
    }
    .sp-social-link {
        width: 42px; height: 42px; border-radius: var(--radius);
        display: flex; align-items: center; justify-content: center;
        background: var(--bg-3); border: 1px solid var(--border);
        color: var(--text-secondary); font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .sp-social-link:hover {
        color: #fff; transform: translateY(-3px);
        box-shadow: 0 6px 20px var(--accent-glow-strong);
    }
    .sp-social-link--github:hover { background: #333; border-color: #333; }
    .sp-social-link--telegram:hover { background: #0088cc; border-color: #0088cc; }
    .sp-social-link--instagram:hover { background: linear-gradient(135deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); border-color: transparent; }
    .sp-social-link--youtube:hover { background: #ff0000; border-color: #ff0000; }
</style>
@endsection

@section('content')

<section class="sp-hero">
    <div class="sp-grid-pattern"></div>
    <div class="sp-orb sp-orb--1"></div>
    <div class="sp-orb sp-orb--2"></div>
    <div class="sp-orb sp-orb--3"></div>
    <div class="sp-orb sp-orb--4"></div>
    <div class="sp-hero__content">
        <div class="sp-badge">
            <i class="fas fa-paper-plane"></i>
            {{ __('page_contacts_badge') }}
        </div>
        <h1 class="sp-hero__title">{{ __('page_contacts_heading') }}</h1>
        <p class="sp-hero__subtitle">{{ __('page_contacts_subtitle') }}</p>
    </div>
</section>

<hr class="sp-divider">

<section class="sp-section">
    <div class="sp-section__inner">
        <div class="sp-grid-2">

            <div>
                <div class="sp-section-header reveal-up">
                    <div class="sp-section-label">
                        <i class="fas fa-address-card"></i>
                        {{ __('page_contacts_info_label') }}
                    </div>
                    <h2 class="sp-section-title">{{ __('page_contacts_info_title') }}</h2>
                    <p class="sp-section-desc">{{ __('page_contacts_info_desc') }}</p>
                </div>

                <div class="sp-contact-list stagger">
                    <div class="sp-contact-card">
                        <div class="sp-contact-card__icon sp-contact-card__icon--email">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="sp-contact-card__body">
                            <h3 class="sp-contact-card__title">{{ __('page_contacts_email_title') }}</h3>
                            <p class="sp-contact-card__detail">
                                <a href="mailto:support@codemaster.tj">support@codemaster.tj</a>
                            </p>
                            <p class="sp-contact-card__detail">
                                <a href="mailto:info@codemaster.tj">info@codemaster.tj</a>
                            </p>
                            <span class="sp-contact-card__label">{{ __('page_contacts_email_label') }}</span>
                        </div>
                    </div>

                    <div class="sp-contact-card">
                        <div class="sp-contact-card__icon sp-contact-card__icon--address">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="sp-contact-card__body">
                            <h3 class="sp-contact-card__title">{{ __('page_contacts_address_title') }}</h3>
                            <p class="sp-contact-card__detail">{{ __('page_contacts_address_line1') }}</p>
                            <p class="sp-contact-card__detail">{{ __('page_contacts_address_line2') }}</p>
                            <span class="sp-contact-card__label">{{ __('page_contacts_address_label') }}</span>
                        </div>
                    </div>

                    <div class="sp-contact-card">
                        <div class="sp-contact-card__icon sp-contact-card__icon--phone">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="sp-contact-card__body">
                            <h3 class="sp-contact-card__title">{{ __('page_contacts_phone_title') }}</h3>
                            <p class="sp-contact-card__detail">
                                <a href="tel:+992372345678">+992 (37) 234-5678</a>
                            </p>
                            <p class="sp-contact-card__detail">{{ __('page_contacts_phone_hours') }}</p>
                            <span class="sp-contact-card__label">{{ __('page_contacts_phone_label') }}</span>
                        </div>
                    </div>

                    <div class="sp-contact-card">
                        <div class="sp-contact-card__icon sp-contact-card__icon--telegram">
                            <i class="fab fa-telegram-plane"></i>
                        </div>
                        <div class="sp-contact-card__body">
                            <h3 class="sp-contact-card__title">{{ __('Telegram') }}</h3>
                            <p class="sp-contact-card__detail">
                                <a href="https://t.me/codemaster_tj" target="_blank" rel="noopener">@codemaster_tj</a>
                            </p>
                            <span class="sp-contact-card__label">{{ __('page_contacts_telegram_label') }}</span>
                        </div>
                    </div>
                </div>

                <div class="sp-social-row reveal-up">
                    <a href="https://github.com/codemaster" target="_blank" rel="noopener" class="sp-social-link sp-social-link--github" title="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="https://t.me/codemaster_tj" target="_blank" rel="noopener" class="sp-social-link sp-social-link--telegram" title="Telegram">
                        <i class="fab fa-telegram-plane"></i>
                    </a>
                    <a href="https://instagram.com/codemaster" target="_blank" rel="noopener" class="sp-social-link sp-social-link--instagram" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://youtube.com/@codemaster" target="_blank" rel="noopener" class="sp-social-link sp-social-link--youtube" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <div class="sp-sticky-col">
                <div class="sp-form-card">
                    <h2 class="sp-form-card__title">
                        <i class="fas fa-pen-fancy"></i>
                        {{ __('page_contacts_form_title') }}
                    </h2>
                    <form class="sp-form" action="#" method="POST">
                        @csrf
                        <div class="sp-form-row">
                            <div class="sp-form-group">
                                <label class="sp-form-label" for="sp-name">{{ __('page_contacts_form_name') }}</label>
                                <input type="text" id="sp-name" name="name" class="sp-form-input" placeholder="{{ __('page_contacts_form_name_placeholder') }}" required>
                            </div>
                            <div class="sp-form-group">
                                <label class="sp-form-label" for="sp-email">{{ __('page_contacts_form_email') }}</label>
                                <input type="email" id="sp-email" name="email" class="sp-form-input" placeholder="you@example.com" required>
                            </div>
                        </div>
                        <div class="sp-form-group">
                            <label class="sp-form-label" for="sp-subject">{{ __('page_contacts_form_subject') }}</label>
                            <input type="text" id="sp-subject" name="subject" class="sp-form-input" placeholder="{{ __('page_contacts_form_subject_placeholder') }}" required>
                        </div>
                        <div class="sp-form-group">
                            <label class="sp-form-label" for="sp-message">{{ __('page_contacts_form_message') }}</label>
                            <textarea id="sp-message" name="message" class="sp-form-textarea" placeholder="{{ __('page_contacts_form_message_placeholder') }}" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="sp-form-btn">
                            <i class="fas fa-paper-plane"></i>
                            {{ __('page_contacts_form_submit') }}
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<hr class="sp-divider">

<section class="sp-section">
    <div class="sp-section__inner">
        <div class="sp-grid-2">

            <div class="sp-hours-card" style="align-self:start">
                <h3 class="sp-hours-card__title">
                    <i class="fas fa-clock"></i>
                    {{ __('page_contacts_hours_title') }}
                </h3>
                <div class="sp-hours-list">
                    <div class="sp-hours-row">
                        <span class="sp-hours-day">{{ __('page_contacts_hours_monday') }}</span>
                        <span class="sp-hours-time sp-hours-time--active">09:00 - 18:00</span>
                    </div>
                    <div class="sp-hours-row">
                        <span class="sp-hours-day">{{ __('page_contacts_hours_tuesday') }}</span>
                        <span class="sp-hours-time sp-hours-time--active">09:00 - 18:00</span>
                    </div>
                    <div class="sp-hours-row">
                        <span class="sp-hours-day">{{ __('page_contacts_hours_wednesday') }}</span>
                        <span class="sp-hours-time sp-hours-time--active">09:00 - 18:00</span>
                    </div>
                    <div class="sp-hours-row">
                        <span class="sp-hours-day">{{ __('page_contacts_hours_thursday') }}</span>
                        <span class="sp-hours-time sp-hours-time--active">09:00 - 18:00</span>
                    </div>
                    <div class="sp-hours-row">
                        <span class="sp-hours-day">{{ __('page_contacts_hours_friday') }}</span>
                        <span class="sp-hours-time sp-hours-time--active">09:00 - 18:00</span>
                    </div>
                    <div class="sp-hours-row">
                        <span class="sp-hours-day">{{ __('page_contacts_hours_saturday') }}</span>
                        <span class="sp-hours-time sp-hours-time--closed">{{ __('page_contacts_hours_closed') }}</span>
                    </div>
                    <div class="sp-hours-row">
                        <span class="sp-hours-day">{{ __('page_contacts_hours_sunday') }}</span>
                        <span class="sp-hours-time sp-hours-time--closed">{{ __('page_contacts_hours_closed') }}</span>
                    </div>
                </div>
            </div>

            <div class="sp-map" style="align-self:start">
                <iframe 
                    src="https://www.openstreetmap.org/export/embed.html?bbox=68.7700%2C38.5570%2C68.7790%2C38.5630&layer=mapnik&marker=38.5598%2C68.7745" 
                    style="width:100%;height:100%;border:0;border-radius:var(--radius-lg);min-height:340px" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    title="{{ __('page_contacts_map_title') }}">
                </iframe>
            </div>

        </div>
    </div>
</section>

<section class="sp-section">
    <div class="sp-section__inner">
        <div class="sp-cta reveal-scale">
            <h2 class="sp-cta__title">{{ __('page_contacts_cta_title_new') }}</h2>
            <p class="sp-cta__desc">{{ __('page_contacts_cta_desc') }}</p>
            <a href="{{ route('courses.index') }}" class="sp-cta__btn">
                <i class="fas fa-arrow-right"></i>
                {{ __('page_contacts_cta_button') }}
            </a>
        </div>
    </div>
</section>

@endsection
