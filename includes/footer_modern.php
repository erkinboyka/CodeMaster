<footer class="cm-footer" style="background:var(--bg-secondary);border-top:1px solid var(--border-primary);padding:var(--space-12) 0;margin-top:var(--space-16);">
    <div class="cm-container">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:var(--space-8);">
            <!-- Brand -->
            <div>
                <a href="?action=home" class="cm-logo cm-mb-4">
                    <div class="cm-logo-icon">CM</div>
                    <span>CodeMaster</span>
                </a>
                <p style="color:var(--text-secondary);font-size:0.875rem;line-height:1.8;">
                    Современная платформа для обучения программированию, поиска работы и развития карьеры в IT.
                </p>
                <div class="cm-flex cm-gap-4 cm-mt-6">
                    <a href="#" style="color:var(--text-muted);font-size:1.25rem;" aria-label="Telegram"><i class="fab fa-telegram"></i></a>
                    <a href="#" style="color:var(--text-muted);font-size:1.25rem;" aria-label="GitHub"><i class="fab fa-github"></i></a>
                    <a href="#" style="color:var(--text-muted);font-size:1.25rem;" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" style="color:var(--text-muted);font-size:1.25rem;" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            
            <!-- Links -->
            <div>
                <h4 style="color:var(--text-primary);font-weight:600;margin-bottom:var(--space-4);">Платформа</h4>
                <ul style="list-style:none;padding:0;margin:0;">
                    <li class="cm-mb-2"><a href="?action=courses" style="color:var(--text-secondary);font-size:0.875rem;">Курсы</a></li>
                    <li class="cm-mb-2"><a href="?action=vacancies" style="color:var(--text-secondary);font-size:0.875rem;">Вакансии</a></li>
                    <li class="cm-mb-2"><a href="?action=contests" style="color:var(--text-secondary);font-size:0.875rem;">Соревнования</a></li>
                    <li class="cm-mb-2"><a href="?action=roadmaps" style="color:var(--text-secondary);font-size:0.875rem;">Roadmaps</a></li>
                    <li class="cm-mb-2"><a href="?action=ratings" style="color:var(--text-secondary);font-size:0.875rem;">Рейтинг</a></li>
                </ul>
            </div>
            
            <div>
                <h4 style="color:var(--text-primary);font-weight:600;margin-bottom:var(--space-4);">Сообщество</h4>
                <ul style="list-style:none;padding:0;margin:0;">
                    <li class="cm-mb-2"><a href="?action=community" style="color:var(--text-secondary);font-size:0.875rem;">Форум</a></li>
                    <li class="cm-mb-2"><a href="?action=it_events" style="color:var(--text-secondary);font-size:0.875rem;">IT Events</a></li>
                    <li class="cm-mb-2"><a href="#" style="color:var(--text-secondary);font-size:0.875rem;">Блог</a></li>
                    <li class="cm-mb-2"><a href="#" style="color:var(--text-secondary);font-size:0.875rem;">FAQ</a></li>
                </ul>
            </div>
            
            <div>
                <h4 style="color:var(--text-primary);font-weight:600;margin-bottom:var(--space-4);">Информация</h4>
                <ul style="list-style:none;padding:0;margin:0;">
                    <li class="cm-mb-2"><a href="#" style="color:var(--text-secondary);font-size:0.875rem;">О нас</a></li>
                    <li class="cm-mb-2"><a href="#" style="color:var(--text-secondary);font-size:0.875rem;">Контакты</a></li>
                    <li class="cm-mb-2"><a href="#" style="color:var(--text-secondary);font-size:0.875rem;">Политика конфиденциальности</a></li>
                    <li class="cm-mb-2"><a href="#" style="color:var(--text-secondary);font-size:0.875rem;">Условия использования</a></li>
                </ul>
            </div>
        </div>
        
        <div style="border-top:1px solid var(--border-primary);margin-top:var(--space-12);padding-top:var(--space-6);display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:var(--space-4);">
            <p style="color:var(--text-muted);font-size:0.875rem;">
                &copy; <?= date('Y') ?> CodeMaster. Все права защищены.
            </p>
            <div class="cm-flex cm-gap-6">
                <button onclick="cmToggleTheme()" class="cm-btn cm-btn-secondary cm-btn-sm">
                    <i class="fas fa-moon" data-theme-icon></i>
                    <span>Тема</span>
                </button>
            </div>
        </div>
    </div>
</footer>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
