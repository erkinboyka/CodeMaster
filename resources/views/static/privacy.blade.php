@extends('layouts.app')

@section('title', 'Политика конфиденциальности' . ' - CodeMaster')

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
            Защита данных
        </div>
        <h1 class="sp-hero-title">Политика конфиденциальности</h1>
        <p class="sp-hero-subtitle">Как мы собираем, используем и защищаем вашу персональную информацию.</p>
        <div class="sp-hero-date">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Дата вступления в силу: 10 августа 2026 г.
        </div>
    </div>
</section>

<div class="sp-layout">
    <nav class="sp-toc">
        <h2 class="sp-toc-title">Содержание</h2>
        <ul class="sp-toc-list">
            <li><a href="#section-1" class="sp-toc-link"><span class="sp-toc-num">1</span> Собираемая информация</a></li>
            <li><a href="#section-2" class="sp-toc-link"><span class="sp-toc-num">2</span> Как мы используем информацию</a></li>
            <li><a href="#section-3" class="sp-toc-link"><span class="sp-toc-num">3</span> Хранение данных</a></li>
            <li><a href="#section-4" class="sp-toc-link"><span class="sp-toc-num">4</span> Обмен информацией</a></li>
            <li><a href="#section-5" class="sp-toc-link"><span class="sp-toc-num">5</span> Cookies</a></li>
            <li><a href="#section-6" class="sp-toc-link"><span class="sp-toc-num">6</span> Ваши права</a></li>
            <li><a href="#section-7" class="sp-toc-link"><span class="sp-toc-num">7</span> Безопасность</a></li>
            <li><a href="#section-8" class="sp-toc-link"><span class="sp-toc-num">8</span> Контакты</a></li>
        </ul>
    </nav>

    <div class="sp-content">
        <section id="section-1" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">1</div>
                <h2 class="sp-section-title">Собираемая информация</h2>
            </div>
            <div class="sp-section-body">
                <p>Мы можем собирать следующие категории персональных данных при использовании нашего сервиса:</p>
                <ul>
                    <li><strong>Регистрационные данные:</strong> имя пользователя, адрес электронной почты и пароль, необходимые для создания и управления учётной записью.</li>
                    <li><strong>Данные профиля:</strong> аватар, описание, ссылки на социальные сети и другая информация, которую вы решите предоставить.</li>
                    <li><strong>Данные об использовании:</strong> информация о взаимодействии с сервисом, включая посещённые страницы, время сессии и действия в интерфейсе.</li>
                    <li><strong>Техническая информация:</strong> IP-адрес, тип и версия браузера, операционная система, идентификаторы устройств.</li>
                </ul>
                <p>Мы не несём ответственности за информацию, которую вы публикуете открыто в рамках функционала сервиса.</p>
            </div>
        </section>

        <section id="section-2" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">2</div>
                <h2 class="sp-section-title">Как мы используем информацию</h2>
            </div>
            <div class="sp-section-body">
                <p>Собранные данные используются исключительно в следующих целях:</p>
                <ul>
                    <li><strong>Предоставление сервиса:</strong> обеспечение корректной работы функционала, авторизация и управление учётной записью.</li>
                    <li><strong>Улучшение качества:</strong> анализ способов использования для оптимизации интерфейса и разработки новых возможностей.</li>
                    <li><strong>Коммуникация:</strong> отправка уведомлений о важных изменениях в сервисе, ответы на ваши обращения.</li>
                    <li><strong>Безопасность:</strong> обнаружение и предотвращение мошеннических действий, защита от несанкционированного доступа.</li>
                    <li><strong>Юридические обязательства:</strong> выполнение требований применимого законодательства.</li>
                </ul>
                <p>Мы не осуществляем автоматизированное принятие решений, оказывающих юридическое или иное существенное воздействие на вас.</p>
            </div>
        </section>

        <section id="section-3" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">3</div>
                <h2 class="sp-section-title">Хранение данных</h2>
            </div>
            <div class="sp-section-body">
                <p>Ваши персональные данные хранятся на защищённых серверах с использованием современных технологий шифрования. Мы принимаем все разумные меры для обеспечения сохранности и конфиденциальности информации.</p>
                <p>Срок хранения данных определяется целями их обработки:</p>
                <ul>
                    <li>Данные учётной записи хранятся на протяжении всего периода использования сервиса и <strong>30 дней</strong> после удаления аккаунта.</li>
                    <li>Логи активности хранятся в течение <strong>90 дней</strong> и далее удаляются в обезличенном виде.</li>
                    <li>Данные, необходимые для выполнения юридических обязательств, могут храниться в установленном законом порядке.</li>
                </ul>
                <p>По истечении сроков хранения данные безвозвратно удаляются или обезличиваются.</p>
            </div>
        </section>

        <section id="section-4" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">4</div>
                <h2 class="sp-section-title">Обмен информацией</h2>
            </div>
            <div class="sp-section-body">
                <p>Мы не продаём и не передаём ваши персональные данные третьим лицам без вашего явного согласия, за исключением следующих случаев:</p>
                <ul>
                    <li><strong>Технические партнёры:</strong> компании, предоставляющие инфраструктуру и сервисы хранения данных, действующие от нашего имени и обязанные соблюдать конфиденциальность.</li>
                    <li><strong>По требованию закона:</strong> если раскрывать данные требуется на основании правового акта, судебного решения или запроса уполномоченного государственного органа.</li>
                    <li><strong>Защита интересов:</strong> для предотвращения угрозы безопасности, мошенничества или нарушения правил использования сервиса.</li>
                </ul>
                <p>В случае реорганизации, слияния или приобретения ваша информация может быть передана правопреемнику с уведомлением.</p>
            </div>
        </section>

        <section id="section-5" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">5</div>
                <h2 class="sp-section-title">Cookies</h2>
            </div>
            <div class="sp-section-body">
                <p>Сервис использует файлы cookie и аналогичные технологии для обеспечения работоспособности, аналитики и персонализации.</p>
                <ul>
                    <li><strong>Строго необходимые cookie:</strong> обеспечивают базовую функциональность, включая аутентификацию и настройки безопасности. Отключение данных cookie невозможно без потери работоспособности.</li>
                    <li><strong>Аналитические cookie:</strong> помогают понять, как пользователи взаимодействуют с сервисом, и используются для улучшения качества.</li>
                    <li><strong>Функциональные cookie:</strong> запоминают ваши предпочтения и настройки для предоставления персонализированного опыта.</li>
                </ul>
                <p>Вы можете управлять настройками cookie через параметры вашего браузера. Обратите внимание, что отключение определённых cookie может повлиять на функциональность сервиса.</p>
            </div>
        </section>

        <section id="section-6" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">6</div>
                <h2 class="sp-section-title">Ваши права</h2>
            </div>
            <div class="sp-section-body">
                <p>В соответствии с применимым законодательством о защите данных вы имеете следующие права:</p>
                <ul>
                    <li><strong>Право на доступ:</strong> получение информации о обрабатываемых персональных данных и копии таких данных.</li>
                    <li><strong>Право на исправление:</strong> требование устранения неточных или неполных персональных данных.</li>
                    <li><strong>Право на удаление:</strong> требование удаления персональных данных при отсутствии правовых основ для их дальнейшей обработки.</li>
                    <li><strong>Право на ограничение:</strong> ограничение обработки персональных данных в определённых случаях.</li>
                    <li><strong>Право на перенос:</strong> получение персональных данных в структурированном, машиночитаемом формате.</li>
                    <li><strong>Право на отзыв согласия:</strong> отмена ранее данного согласия на обработку персональных данных.</li>
                </ul>
                <p>Для реализации ваших прав свяжитесь с нами любым удобным способом, указанным в разделе «Контакты».</p>
            </div>
        </section>

        <section id="section-7" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">7</div>
                <h2 class="sp-section-title">Безопасность</h2>
            </div>
            <div class="sp-section-body">
                <p>Мы серьёзно относимся к защите ваших данных и применяем комплексный подход к обеспечению безопасности:</p>
                <ul>
                    <li><strong>Шифрование:</strong> все данные передаются по защищённому каналу TLS 1.3, а конфиденциальная информация хранится в зашифрованном виде.</li>
                    <li><strong>Контроль доступа:</strong> доступ к персональным данным имеют только авторизованные сотрудники, прошедшие проверку и обучение.</li>
                    <li><strong>Мониторинг:</strong> непрерывное отслеживание инфраструктуры для выявления и реагирования на потенциальные угрозы.</li>
                    <li><strong>Тестирование:</strong> регулярное проведение аудитов безопасности и тестирование на проникновение.</li>
                </ul>
                <p>В случае утечки данных, создающей угрозу вашим правам, мы уведомим вас и соответствующие надзорные органы в установленные законом сроки.</p>
            </div>
        </section>

        <section id="section-8" class="sp-section">
            <div class="sp-section-header">
                <div class="sp-section-num">8</div>
                <h2 class="sp-section-title">Контакты</h2>
            </div>
            <div class="sp-section-body">
                <p>Если у вас возникли вопросы, замечания или запросы, касающиеся настоящей Политики конфиденциальности или обработки ваших персональных данных, пожалуйста, свяжитесь с нами:</p>
                <ul>
                    <li><strong>Электронная почта:</strong> privacy@codemaster.dev</li>
                    <li><strong>Тема письма:</strong> указывайте «Вопрос по конфиденциальности» для ускоренной обработки обращения.</li>
                </ul>
                <p>Мы постараемся ответить на ваш запрос в течение <strong>30 календарных дней</strong> с момента его получения. При необходимости срок ответа может быть продлён с уведомлением.</p>
                <p>Настоящая политика может быть обновлена. При внесении существенных изменений мы уведомим вас по электронной почте или иным доступным способом.</p>
            </div>
        </section>
    </div>
</div>
@endsection
