<?php
// Новый дизайн главной страницы в стиле LeetCode/ElectiCode
$lang = currentLang() ?? 'ru';
?>

<div class="cm-main">
    <div class="cm-container">
        <!-- Hero Section -->
        <section class="cm-card cm-animate-slide-up" style="margin-bottom:var(--cm-spacing-2xl);background:linear-gradient(135deg, var(--cm-bg-card) 0%, var(--cm-bg-tertiary) 100%);">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--cm-spacing-xl);align-items:center;">
                <div>
                    <span class="cm-badge cm-badge-new" style="margin-bottom:var(--cm-spacing-md);">
                        <i class="fas fa-sparkles"></i> Бесплатная IT-платформа
                    </span>
                    <h1 class="cm-page-title" style="font-size:48px;line-height:1.2;">
                        Красивый старт в <span style="color:var(--cm-accent-green);">IT</span> вместе с нами
                    </h1>
                    <p style="color:var(--cm-text-secondary);font-size:18px;margin:var(--cm-spacing-lg) 0;">
                        Учись, находи работу и развивай карьеру в IT-сфере на одной платформе
                    </p>
                    <div style="display:flex;gap:var(--cm-spacing-md);margin-top:var(--cm-spacing-xl);">
                        <a href="?action=courses" class="cm-btn cm-btn-primary cm-btn-lg">
                            <i class="fas fa-rocket"></i> Начать бесплатно
                        </a>
                        <a href="?action=vacancies" class="cm-btn cm-btn-secondary cm-btn-lg">
                            <i class="fas fa-briefcase"></i> Смотреть вакансии
                        </a>
                    </div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:120px;color:var(--cm-accent-green);opacity:0.2;">
                        <i class="fas fa-code"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="cm-grid cm-grid-4" style="margin-bottom:var(--cm-spacing-2xl);">
            <div class="cm-stat-card cm-animate-fade">
                <div class="cm-stat-value"><?= number_format($studentsCount ?? 0) ?>+</div>
                <div class="cm-stat-label">Студентов</div>
            </div>
            <div class="cm-stat-card cm-animate-fade" style="animation-delay:0.1s;">
                <div class="cm-stat-value"><?= number_format($coursesCount ?? 0) ?></div>
                <div class="cm-stat-label">Курсов</div>
            </div>
            <div class="cm-stat-card cm-animate-fade" style="animation-delay:0.2s;">
                <div class="cm-stat-value"><?= number_format($vacanciesCount ?? 0) ?></div>
                <div class="cm-stat-label">Вакансий</div>
            </div>
            <div class="cm-stat-card cm-animate-fade" style="animation-delay:0.3s;">
                <div class="cm-stat-value"><?= number_format($avgRating ?? 0, 1) ?></div>
                <div class="cm-stat-label">Рейтинг</div>
            </div>
        </section>

        <!-- Courses Section -->
        <section style="margin-bottom:var(--cm-spacing-2xl);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--cm-spacing-lg);">
                <h2 class="cm-page-title" style="font-size:24px;margin:0;">Новые курсы</h2>
                <a href="?action=courses" class="cm-btn cm-btn-outline cm-btn-sm">
                    Все курсы <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="cm-grid cm-grid-3">
                <?php if (!empty($homeCourses)): ?>
                    <?php foreach (array_slice($homeCourses, 0, 6) as $course): ?>
                    <div class="cm-card cm-animate-slide-up">
                        <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:var(--cm-spacing-md);">
                            <span class="cm-badge cm-badge-easy">
                                <i class="fas fa-signal"></i> <?= htmlspecialchars($course['level'] ?? 'Beginner') ?>
                            </span>
                            <span style="color:var(--cm-text-muted);font-size:12px;">
                                <i class="fas fa-clock"></i> <?= htmlspecialchars($course['lessons_count'] ?? 0) ?> уроков
                            </span>
                        </div>
                        <h3 style="font-size:18px;font-weight:700;margin-bottom:var(--cm-spacing-sm);color:var(--cm-text-primary);">
                            <?= htmlspecialchars($course['title'] ?? 'Курс') ?>
                        </h3>
                        <p style="color:var(--cm-text-secondary);font-size:14px;margin-bottom:var(--cm-spacing-md);">
                            <?= htmlspecialchars(mb_substr($course['description'] ?? '', 0, 100)) ?>...
                        </p>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div class="cm-progress" style="width:120px;">
                                <div class="cm-progress-bar" style="width:<?= rand(10, 90) ?>%;"></div>
                            </div>
                            <a href="?action=course&id=<?= $course['id'] ?? '#' ?>" class="cm-btn cm-btn-primary cm-btn-sm">
                                Открыть
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="cm-card" style="grid-column:1/-1;text-align:center;padding:var(--cm-spacing-xl);">
                        <i class="fas fa-inbox" style="font-size:48px;color:var(--cm-text-muted);margin-bottom:var(--cm-spacing-md);"></i>
                        <p style="color:var(--cm-text-secondary);">Курсы скоро появятся</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Vacancies Section -->
        <section style="margin-bottom:var(--cm-spacing-2xl);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--cm-spacing-lg);">
                <h2 class="cm-page-title" style="font-size:24px;margin:0;">Свежие вакансии</h2>
                <a href="?action=vacancies" class="cm-btn cm-btn-outline cm-btn-sm">
                    Все вакансии <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="cm-table-container">
                <table class="cm-table">
                    <thead>
                        <tr>
                            <th>Позиция</th>
                            <th>Компания</th>
                            <th>Уровень</th>
                            <th>Зарплата</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($homeVacancies)): ?>
                            <?php foreach (array_slice($homeVacancies, 0, 5) as $vacancy): ?>
                            <tr>
                                <td>
                                    <div style="font-weight:600;color:var(--cm-text-primary);">
                                        <?= htmlspecialchars($vacancy['position'] ?? 'Позиция') ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="color:var(--cm-text-secondary);">
                                        <?= htmlspecialchars($vacancy['company'] ?? 'Компания') ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="cm-tag"><?= htmlspecialchars($vacancy['level'] ?? 'Junior') ?></span>
                                </td>
                                <td>
                                    <div style="color:var(--cm-accent-green);font-weight:600;">
                                        <?= !empty($vacancy['salary_from']) ? '$'.number_format($vacancy['salary_from']) : 'По договоренности' ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="?action=vacancy&id=<?= $vacancy['id'] ?? '#' ?>" class="cm-btn cm-btn-outline cm-btn-sm">
                                        Подробнее
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center;padding:var(--cm-spacing-xl);color:var(--cm-text-muted);">
                                    Вакансии скоро появятся
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Features Section -->
        <section class="cm-card" style="margin-bottom:var(--cm-spacing-2xl);">
            <h2 class="cm-page-title" style="text-align:center;margin-bottom:var(--cm-spacing-xl);">Почему CodeMaster?</h2>
            <div class="cm-grid cm-grid-3">
                <div style="text-align:center;padding:var(--cm-spacing-lg);">
                    <div style="width:64px;height:64px;background:rgba(0,191,165,0.1);border-radius:var(--cm-radius-lg);display:flex;align-items:center;justify-content:center;margin:0 auto var(--cm-spacing-md);">
                        <i class="fas fa-database" style="font-size:28px;color:var(--cm-accent-green);"></i>
                    </div>
                    <h3 style="font-weight:700;margin-bottom:var(--cm-spacing-sm);">Реальные данные</h3>
                    <p style="color:var(--cm-text-secondary);font-size:14px;">
                        Актуальные курсы и вакансии из проверенных источников
                    </p>
                </div>
                <div style="text-align:center;padding:var(--cm-spacing-lg);">
                    <div style="width:64px;height:64px;background:rgba(41,98,255,0.1);border-radius:var(--cm-radius-lg);display:flex;align-items:center;justify-content:center;margin:0 auto var(--cm-spacing-md);">
                        <i class="fas fa-gift" style="font-size:28px;color:var(--cm-accent-blue);"></i>
                    </div>
                    <h3 style="font-weight:700;margin-bottom:var(--cm-spacing-sm);">Бесплатный доступ</h3>
                    <p style="color:var(--cm-text-secondary);font-size:14px;">
                        Все основные функции доступны без платной подписки
                    </p>
                </div>
                <div style="text-align:center;padding:var(--cm-spacing-lg);">
                    <div style="width:64px;height:64px;background:rgba(124,58,237,0.1);border-radius:var(--cm-radius-lg);display:flex;align-items:center;justify-content:center;margin:0 auto var(--cm-spacing-md);">
                        <i class="fas fa-route" style="font-size:28px;color:var(--cm-accent-purple);"></i>
                    </div>
                    <h3 style="font-weight:700;margin-bottom:var(--cm-spacing-sm);">Быстрый путь</h3>
                    <p style="color:var(--cm-text-secondary);font-size:14px;">
                        От обучения до работы внутри одной платформы
                    </p>
                </div>
            </div>
        </section>
    </div>
</div>
