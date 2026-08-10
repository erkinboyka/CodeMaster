@extends('layouts.app')

@section('title', 'Условия использования' . ' - CodeMaster')

@section('head')
<style>
    .sp-terms-page {
        --sp-section-gap: 2.5rem;
        --sp-card-padding: 2.5rem;
        --sp-toc-width: 260px;
    }

    .sp-terms-hero {
        position: relative;
        padding: 6rem 2rem 4rem;
        background: var(--gradient);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .sp-terms-hero::before,
    .sp-terms-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        filter: blur(100px);
        opacity: 0.4;
        animation: sp-orb-drift 12s ease-in-out infinite alternate;
    }

    .sp-terms-hero::before {
        width: 500px;
        height: 500px;
        background: var(--accent);
        top: -150px;
        left: -100px;
    }

    .sp-terms-hero::after {
        width: 400px;
        height: 400px;
        background: var(--accent-3);
        bottom: -120px;
        right: -80px;
        animation-delay: -4s;
    }

    @keyframes sp-orb-drift {
        0% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(30px, -20px) scale(1.05); }
        100% { transform: translate(-20px, 15px) scale(0.95); }
    }

    .sp-terms-hero-inner {
        position: relative;
        z-index: 2;
        max-width: 720px;
    }

    .sp-terms-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 100px;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        backdrop-filter: blur(8px);
        margin-bottom: 1.5rem;
        animation: sp-badge-in 0.6s ease-out;
    }

    @keyframes sp-badge-in {
        from { opacity: 0; transform: translateY(-12px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .sp-terms-hero h1 {
        color: #fff;
        font-size: clamp(2.2rem, 5vw, 3.5rem);
        font-weight: 800;
        line-height: 1.15;
        margin: 0 0 1rem;
        letter-spacing: -0.03em;
        animation: sp-title-in 0.7s ease-out 0.1s both;
    }

    .sp-terms-hero p {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1.1rem;
        margin: 0;
        animation: sp-title-in 0.7s ease-out 0.25s both;
    }

    @keyframes sp-title-in {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .sp-terms-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        max-width: 1100px;
        margin: 0 auto;
        padding: 3rem 2rem 4rem;
        position: relative;
    }

    @media (min-width: 960px) {
        .sp-terms-layout {
            grid-template-columns: var(--sp-toc-width) 1fr;
            gap: 3rem;
        }
    }

    .sp-terms-toc {
        display: none;
    }

    @media (min-width: 960px) {
        .sp-terms-toc {
            display: block;
            position: sticky;
            top: 6rem;
            align-self: start;
            max-height: calc(100vh - 8rem);
            overflow-y: auto;
        }
    }

    .sp-terms-toc-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        margin: 0 0 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border);
    }

    .sp-terms-toc-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .sp-terms-toc-link {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.55rem 0.85rem;
        border-radius: var(--radius);
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .sp-terms-toc-link:hover {
        color: var(--accent);
        background: var(--bg-elevated);
        border-color: var(--border);
    }

    .sp-terms-toc-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.6rem;
        height: 1.6rem;
        border-radius: var(--radius-sm);
        background: var(--bg-3);
        color: var(--text-muted);
        font-size: 0.72rem;
        font-weight: 700;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .sp-terms-toc-link:hover .sp-terms-toc-num {
        background: var(--accent);
        color: #fff;
    }

    .sp-terms-content {
        display: flex;
        flex-direction: column;
        gap: var(--sp-section-gap);
    }

    .sp-terms-section {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: var(--sp-card-padding);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .sp-terms-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--gradient);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .sp-terms-section:hover {
        border-color: var(--border-hover);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    }

    .sp-terms-section:hover::before {
        opacity: 1;
    }

    .sp-terms-section-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .sp-terms-section-num {
        width: 3.2rem;
        height: 3.2rem;
        border-radius: 50%;
        background: var(--gradient);
        color: #fff;
        font-size: 1.05rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .sp-terms-section-num::after {
        content: '';
        position: absolute;
        inset: -3px;
        border-radius: 50%;
        background: var(--gradient);
        opacity: 0.25;
        z-index: -1;
    }

    .sp-terms-section-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text);
        margin: 0;
        letter-spacing: -0.02em;
    }

    .sp-terms-section-body {
        color: var(--text-secondary);
        font-size: 1rem;
        line-height: 1.75;
    }

    .sp-terms-section-body p {
        margin: 0 0 1rem;
    }

    .sp-terms-section-body p:last-child {
        margin-bottom: 0;
    }

    .sp-terms-section-body ul,
    .sp-terms-section-body ol {
        margin: 0.75rem 0 1rem;
        padding-left: 1.5rem;
    }

    .sp-terms-section-body li {
        margin-bottom: 0.5rem;
        line-height: 1.7;
    }

    .sp-terms-section-body li:last-child {
        margin-bottom: 0;
    }

    .sp-terms-section-body strong {
        color: var(--text);
        font-weight: 600;
    }

    .sp-terms-highlight {
        padding: 1.25rem 1.5rem;
        background: var(--bg-elevated);
        border-left: 3px solid var(--accent);
        border-radius: 0 var(--radius) var(--radius) 0;
        margin: 1rem 0;
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .sp-terms-contact-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-top: 1rem;
    }

    @media (min-width: 540px) {
        .sp-terms-contact-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    .sp-terms-contact-card {
        padding: 1.25rem 1.5rem;
        background: var(--bg-elevated);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        transition: border-color 0.2s ease, transform 0.2s ease;
    }

    .sp-terms-contact-card:hover {
        border-color: var(--border-hover);
        transform: translateY(-2px);
    }

    .sp-terms-contact-label {
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-bottom: 0.4rem;
    }

    .sp-terms-contact-value {
        font-size: 0.95rem;
        color: var(--text);
        font-weight: 500;
    }

    .sp-terms-contact-value a {
        color: var(--accent);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .sp-terms-contact-value a:hover {
        color: var(--accent-2);
    }

    .sp-back-to-top {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        background: var(--accent);
        color: #fff;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        opacity: 0;
        visibility: hidden;
        transform: translateY(12px);
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .sp-back-to-top.visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .sp-back-to-top:hover {
        background: var(--accent-2);
        transform: translateY(-3px);
        box-shadow: 0 6px 28px rgba(0, 0, 0, 0.2);
    }

    .sp-back-to-top svg {
        width: 1.2rem;
        height: 1.2rem;
    }

    .sp-mobile-toc {
        margin-bottom: 1.5rem;
    }

    @media (min-width: 960px) {
        .sp-mobile-toc {
            display: none;
        }
    }

    .sp-mobile-toc-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 1rem 1.25rem;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        color: var(--text);
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: border-color 0.2s ease;
    }

    .sp-mobile-toc-toggle:hover {
        border-color: var(--border-hover);
    }

    .sp-mobile-toc-toggle-icon {
        transition: transform 0.3s ease;
    }

    .sp-mobile-toc-toggle.active .sp-mobile-toc-toggle-icon {
        transform: rotate(180deg);
    }

    .sp-mobile-toc-body {
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.35s ease;
    }

    .sp-mobile-toc-body.open {
        max-height: 600px;
    }

    .sp-mobile-toc-list {
        list-style: none;
        padding: 0.5rem 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .sp-mobile-toc-link {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.6rem 1rem;
        border-radius: var(--radius-sm);
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.15s ease;
    }

    .sp-mobile-toc-link:hover {
        color: var(--accent);
        background: var(--bg-elevated);
    }

    .sp-terms-footer-note {
        text-align: center;
        padding: 2rem 1rem 0;
        color: var(--text-muted);
        font-size: 0.88rem;
    }

    .sp-terms-footer-note strong {
        color: var(--text-secondary);
    }

    .sp-legal-icon {
        display: inline-block;
        margin-right: 0.3rem;
    }
</style>
@endsection

@section('content')
<div class="sp-terms-page">

    <section class="sp-terms-hero">
        <div class="sp-terms-hero-inner">
            <div class="sp-terms-badge">
                <span class="sp-legal-icon">&#9878;</span>
                Правовая информация
            </div>
            <h1>Условия использования</h1>
            <p>Последнее обновление: 10 августа 2026 г.</p>
        </div>
    </section>

    <div class="sp-terms-layout">

        <nav class="sp-terms-toc">
            <p class="sp-terms-toc-title">Содержание</p>
            <ul class="sp-terms-toc-list">
                <li><a href="#section-1" class="sp-terms-toc-link"><span class="sp-terms-toc-num">1</span> Принятие условий</a></li>
                <li><a href="#section-2" class="sp-terms-toc-link"><span class="sp-terms-toc-num">2</span> Аккаунты пользователей</a></li>
                <li><a href="#section-3" class="sp-terms-toc-link"><span class="sp-terms-toc-num">3</span> Использование платформы</a></li>
                <li><a href="#section-4" class="sp-terms-toc-link"><span class="sp-terms-toc-num">4</span> Контент</a></li>
                <li><a href="#section-5" class="sp-terms-toc-link"><span class="sp-terms-toc-num">5</span> Контент пользователей</a></li>
                <li><a href="#section-6" class="sp-terms-toc-link"><span class="sp-terms-toc-num">6</span> Конфиденциальность</a></li>
                <li><a href="#section-7" class="sp-terms-toc-link"><span class="sp-terms-toc-num">7</span> Прекращение доступа</a></li>
                <li><a href="#section-8" class="sp-terms-toc-link"><span class="sp-terms-toc-num">8</span> Ограничение ответственности</a></li>
                <li><a href="#section-9" class="sp-terms-toc-link"><span class="sp-terms-toc-num">9</span> Изменения условий</a></li>
                <li><a href="#section-10" class="sp-terms-toc-link"><span class="sp-terms-toc-num">10</span> Контакты</a></li>
            </ul>
        </nav>

        <div class="sp-terms-content">

            <div class="sp-mobile-toc">
                <button class="sp-mobile-toc-toggle" onclick="this.classList.toggle('active'); this.nextElementSibling.classList.toggle('open')">
                    Содержание
                    <svg class="sp-mobile-toc-toggle-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="sp-mobile-toc-body">
                    <ul class="sp-mobile-toc-list">
                        <li><a href="#section-1" class="sp-mobile-toc-link"><span class="sp-terms-toc-num">1</span> Принятие условий</a></li>
                        <li><a href="#section-2" class="sp-mobile-toc-link"><span class="sp-terms-toc-num">2</span> Аккаунты пользователей</a></li>
                        <li><a href="#section-3" class="sp-mobile-toc-link"><span class="sp-terms-toc-num">3</span> Использование платформы</a></li>
                        <li><a href="#section-4" class="sp-mobile-toc-link"><span class="sp-terms-toc-num">4</span> Контент</a></li>
                        <li><a href="#section-5" class="sp-mobile-toc-link"><span class="sp-terms-toc-num">5</span> Контент пользователей</a></li>
                        <li><a href="#section-6" class="sp-mobile-toc-link"><span class="sp-terms-toc-num">6</span> Конфиденциальность</a></li>
                        <li><a href="#section-7" class="sp-mobile-toc-link"><span class="sp-terms-toc-num">7</span> Прекращение доступа</a></li>
                        <li><a href="#section-8" class="sp-mobile-toc-link"><span class="sp-terms-toc-num">8</span> Ограничение ответственности</a></li>
                        <li><a href="#section-9" class="sp-mobile-toc-link"><span class="sp-terms-toc-num">9</span> Изменения условий</a></li>
                        <li><a href="#section-10" class="sp-mobile-toc-link"><span class="sp-terms-toc-num">10</span> Контакты</a></li>
                    </ul>
                </div>
            </div>

            <article id="section-1" class="sp-terms-section">
                <div class="sp-terms-section-header">
                    <div class="sp-terms-section-num">1</div>
                    <h2 class="sp-terms-section-title">Принятие условий</h2>
                </div>
                <div class="sp-terms-section-body">
                    <p>Добро пожаловать на платформу CodeMaster. Используя наш сайт и сервисы, вы подтверждаете, что ознакомились с настоящими Условиями использования и соглашаетесь с их положениями.</p>
                    <p>Если вы не согласны с каким-либо из условий, пожалуйста, прекратите использование платформы. Продолжая пользоваться нашими сервисами, вы подтверждаете своё согласие с данными условиями.</p>
                    <div class="sp-terms-highlight">
                        <strong>Важно:</strong> Использование платформы лицами младше 18 лет не допускается без согласия родителя или законного представителя.
                    </div>
                </div>
            </article>

            <article id="section-2" class="sp-terms-section">
                <div class="sp-terms-section-header">
                    <div class="sp-terms-section-num">2</div>
                    <h2 class="sp-terms-section-title">Аккаунты пользователей</h2>
                </div>
                <div class="sp-terms-section-body">
                    <p>Для доступа к определённым функциям платформы вам может потребоваться создание аккаунта. При регистрации вы обязуетесь:</p>
                    <ul>
                        <li>Предоставить точную и актуальную информацию</li>
                        <li>Поддерживать конфиденциальность учётных данных</li>
                        <li>Немедленно уведомлять нас о несанкционированном доступе к аккаунту</li>
                        <li>Нести ответственность за все действия, совершённые под вашим аккаунтом</li>
                    </ul>
                    <p>Мы оставляем за собой право заблокировать или удалить аккаунт при нарушении настоящих условий.</p>
                </div>
            </article>

            <article id="section-3" class="sp-terms-section">
                <div class="sp-terms-section-header">
                    <div class="sp-terms-section-num">3</div>
                    <h2 class="sp-terms-section-title">Использование платформы</h2>
                </div>
                <div class="sp-terms-section-body">
                    <p>Платформа предоставляет доступ к образовательным материалам, инструментам программирования и сообществу разработчиков. Вы соглашаетесь использовать платформу только в законных целях.</p>
                    <p>Запрещается:</p>
                    <ul>
                        <li>Использовать платформу для распространения вредоносного программного обеспечения</li>
                        <li>Пытаться получить несанкционированный доступ к системам или данным других пользователей</li>
                        <li>Нарушать работу серверов или инфраструктуры платформы</li>
                        <li>Копировать, модифицировать или распространять контент без разрешения</li>
                        <li>Использовать автоматизированные средства для сбора данных без согласия</li>
                    </ul>
                </div>
            </article>

            <article id="section-4" class="sp-terms-section">
                <div class="sp-terms-section-header">
                    <div class="sp-terms-section-num">4</div>
                    <h2 class="sp-terms-section-title">Контент</h2>
                </div>
                <div class="sp-terms-section-body">
                    <p>Весь контент, размещённый на платформе CodeMaster, включая тексты, изображения, графику, логотипы, иконки, аудио и видеоматериалы, является интеллектуальной собственностью CodeMaster или его лицензиаров и защищён законодательством об авторском праве.</p>
                    <p>Вам предоставляется ограниченная, неисключительная, непередаваемая лицензия на использование контента исключительно в личных и образовательных целях.</p>
                    <div class="sp-terms-highlight">
                        <strong>Обратите внимание:</strong> Любое коммерческое использование контента требует предварительного письменного согласия правообладателя.
                    </div>
                </div>
            </article>

            <article id="section-5" class="sp-terms-section">
                <div class="sp-terms-section-header">
                    <div class="sp-terms-section-num">5</div>
                    <h2 class="sp-terms-section-title">Контент пользователей</h2>
                </div>
                <div class="sp-terms-section-body">
                    <p>Платформа позволяет пользователям размещать собственный контент, включая код, комментарии, проекты и другие материалы. Размещая контент на платформе, вы:</p>
                    <ul>
                        <li>Подтверждаете, что обладаете всеми необходимыми правами на размещаемый контент</li>
                        <li>Предоставляете CodeMaster неисключительную лицензию на использование, воспроизведение и распространение данного контента</li>
                        <li>Гарантируете, что контент не нарушает права третьих лиц</li>
                    </ul>
                    <p>Мы оставляем за собой право удалять любой контент, нарушающий настоящие условия или законодательство.</p>
                </div>
            </article>

            <article id="section-6" class="sp-terms-section">
                <div class="sp-terms-section-header">
                    <div class="sp-terms-section-num">6</div>
                    <h2 class="sp-terms-section-title">Конфиденциальность</h2>
                </div>
                <div class="sp-terms-section-body">
                    <p>Мы серьёзно относимся к защите ваших персональных данных. Сбор, использование и хранение информации регулируется нашей Политикой конфиденциальности.</p>
                    <p>Используя платформу, вы даёте согласие на обработку ваших персональных данных в соответствии с применимым законодательством о защите персональных данных, включая Федеральный закон №152-ФЗ «О персональных данных».</p>
                    <p>Мы implement следующие меры для обеспечения безопасности ваших данных:</p>
                    <ul>
                        <li>Шифрование данных при передаче (TLS/SSL)</li>
                        <li>Регулярный аудит систем безопасности</li>
                        <li>Ограниченный доступ сотрудников к персональным данным</li>
                        <li>Мониторинг и устранение уязвимостей</li>
                    </ul>
                </div>
            </article>

            <article id="section-7" class="sp-terms-section">
                <div class="sp-terms-section-header">
                    <div class="sp-terms-section-num">7</div>
                    <h2 class="sp-terms-section-title">Прекращение доступа</h2>
                </div>
                <div class="sp-terms-section-body">
                    <p>Мы оставляем за собой право приостановить или прекратить ваш доступ к платформе в любое время и по любой причине, включая, но не ограничиваясь:</p>
                    <ul>
                        <li>Нарушение настоящих Условий использования</li>
                        <li>Запросы правоохранительных органов</li>
                        <li>Технические или операционные проблемы</li>
                        <li>Долгое отсутствие активности в аккаунте</li>
                    </ul>
                    <p>При прекращении доступа ваше право на использование контента платформы аннулируется. Вы можете запросить экспорт своих данных в течение 30 дней после прекращения доступа.</p>
                </div>
            </article>

            <article id="section-8" class="sp-terms-section">
                <div class="sp-terms-section-header">
                    <div class="sp-terms-section-num">8</div>
                    <h2 class="sp-terms-section-title">Ограничение ответственности</h2>
                </div>
                <div class="sp-terms-section-body">
                    <p>Платформа и её контент предоставляются «как есть» без каких-либо гарантий. CodeMaster не несёт ответственности за:</p>
                    <ul>
                        <li>Точность, полноту или актуальность контента</li>
                        <li>Решения, принятые на основе информации, полученной с платформы</li>
                        <li>Убытки, возникшие в результате использования или невозможности использования платформы</li>
                        <li>Действия третьих лиц, повлиявшие на ваш доступ к сервису</li>
                    </ul>
                    <div class="sp-terms-highlight">
                        <strong>Максимальная ответственность:</strong> Совокупная ответственность CodeMaster по любому иску не может превышать сумму, уплаченную вами за использование платформы за последние 12 месяцев, или 10 000 рублей, в зависимости от того, какая сумма больше.
                    </div>
                </div>
            </article>

            <article id="section-9" class="sp-terms-section">
                <div class="sp-terms-section-header">
                    <div class="sp-terms-section-num">9</div>
                    <h2 class="sp-terms-section-title">Изменения условий</h2>
                </div>
                <div class="sp-terms-section-body">
                    <p>CodeMaster оставляет за собой право изменять настоящие Условия использования в любое время. При внесении существенных изменений мы уведомим вас следующими способами:</p>
                    <ul>
                        <li>Уведомление по электронной почте, зарегистрированной в аккаунте</li>
                        <li>Публикация объявления на главной странице платформы</li>
                        <li>Обновление даты последней модификации в начале данного документа</li>
                    </ul>
                    <p>Продолжение использования платформы после внесения изменений означает ваше согласие с обновлёнными условиями. Если вы не согласны с изменениями, вы должны прекратить использование платформы.</p>
                </div>
            </article>

            <article id="section-10" class="sp-terms-section">
                <div class="sp-terms-section-header">
                    <div class="sp-terms-section-num">10</div>
                    <h2 class="sp-terms-section-title">Контакты</h2>
                </div>
                <div class="sp-terms-section-body">
                    <p>Если у вас есть вопросы относительно настоящих Условий использования, свяжитесь с нами:</p>
                    <div class="sp-terms-contact-grid">
                        <div class="sp-terms-contact-card">
                            <div class="sp-terms-contact-label">Электронная почта</div>
                            <div class="sp-terms-contact-value"><a href="mailto:legal@codemaster.dev">legal@codemaster.dev</a></div>
                        </div>
                        <div class="sp-terms-contact-card">
                            <div class="sp-terms-contact-label">Поддержка</div>
                            <div class="sp-terms-contact-value"><a href="mailto:support@codemaster.dev">support@codemaster.dev</a></div>
                        </div>
                        <div class="sp-terms-contact-card">
                            <div class="sp-terms-contact-label">Адрес</div>
                            <div class="sp-terms-contact-value">г. Москва, ул. Разработчиков, 42</div>
                        </div>
                        <div class="sp-terms-contact-card">
                            <div class="sp-terms-contact-label">Время ответа</div>
                            <div class="sp-terms-contact-value">В течение 2 рабочих дней</div>
                        </div>
                    </div>
                </div>
            </article>

            <div class="sp-terms-footer-note">
                <p>Документ составлен в соответствии с законодательством Российской Федерации.<br>Последняя редакция: <strong>10 августа 2026 г.</strong></p>
            </div>

        </div>
    </div>

    <button class="sp-back-to-top" id="spBackToTop" aria-label="Наверх" onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
    </button>

</div>

<script>
(function() {
    var btn = document.getElementById('spBackToTop');
    if (!btn) return;

    function onScroll() {
        if (window.scrollY > 400) {
            btn.classList.add('visible');
        } else {
            btn.classList.remove('visible');
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    document.querySelectorAll('a[href^="#section-"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });

                var mobileBody = document.querySelector('.sp-mobile-toc-body');
                var mobileToggle = document.querySelector('.sp-mobile-toc-toggle');
                if (mobileBody && mobileBody.classList.contains('open')) {
                    mobileBody.classList.remove('open');
                    mobileToggle.classList.remove('active');
                }
            }
        });
    });

    var sections = document.querySelectorAll('.sp-terms-section[id]');
    var tocLinks = document.querySelectorAll('.sp-terms-toc-link');

    function highlightToc() {
        var scrollPos = window.scrollY + 120;
        var currentId = '';
        sections.forEach(function(sec) {
            if (sec.offsetTop <= scrollPos) {
                currentId = sec.id;
            }
        });
        tocLinks.forEach(function(link) {
            link.style.color = '';
            link.style.background = '';
            link.style.borderColor = 'transparent';
            if (link.getAttribute('href') === '#' + currentId) {
                link.style.color = 'var(--accent)';
                link.style.background = 'var(--bg-elevated)';
                link.style.borderColor = 'var(--border)';
            }
        });
    }

    window.addEventListener('scroll', highlightToc, { passive: true });
    highlightToc();
})();
</script>
@endsection
