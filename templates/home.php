<?php
$homeCourses = $homeCourses ?? [];
$homeVacancies = $homeVacancies ?? [];
$homeCompanies = $homeCompanies ?? [];
$platformReviews = $platformReviews ?? [];
$homeStats = $homeStats ?? [];
$homeLikes = $homeLikes ?? ['course' => ['counts' => [], 'liked' => []], 'vacancy' => ['counts' => [], 'liked' => []], 'review' => ['counts' => [], 'liked' => []]];
$studentsCount = (int) ($homeStats['students_count'] ?? 0);
$coursesCount = (int) ($homeStats['courses_count'] ?? 0);
$vacanciesCount = (int) ($homeStats['vacancies_count'] ?? 0);
$completedCoursesCount = (int) ($homeStats['completed_courses_count'] ?? 0);
$avgRating = (float) ($homeStats['avg_rating'] ?? 0);
$formatCount = static fn($v) => number_format(max(0, (int) $v), 0, '.', ' ');
$formatSalary = static function ($v) {
	$n = (int) $v;
	return $n > 0 ? number_format($n, 0, '.', ' ') : null;
};
$formatDate = static function ($value): string {
	if (empty($value)) {
		return '-';
	}
	$ts = strtotime((string) $value);
	return $ts ? date('d.m.Y', $ts) : '-';
};
$lang = function_exists('currentLang') ? currentLang() : 'ru';
if (!in_array($lang, ['ru', 'en', 'tg'], true)) {
	$lang = 'ru';
}
$langUrl = static function (string $to): string {
	if (function_exists('langUrl')) {
		return langUrl($to);
	}
	$p = $_GET;
	$p['lang'] = $to;
	return '?' . http_build_query($p);
};
require_once __DIR__ . '/../includes/telegram_feed.php';
$homeEventsFeed = tfTelegramFeedGetPosts('iteventstj', 3, 900);
$homeEventsPosts = is_array($homeEventsFeed['posts'] ?? null) ? $homeEventsFeed['posts'] : [];

// Вспомогательная функция для исправления mojibake
function fixMojibake(string $str): string {
    // Если строка выглядит как повреждённая UTF-8 (содержит "Р", "С", и т.п.)
    if (preg_match('/[\x80-\xFF]/', $str)) {
        // Попытка декодировать: предполагаем, что это UTF-8, ошибочно прочитанный как Windows-1252
        return mb_convert_encoding($str, 'UTF-8', 'Windows-1252');
    }
    return $str;
}

// Исправляем массивы с переводами
$t = [
	'ru' => [
		'title' => 'CodeMaster',
		'desc' => 'Бесплатная IT-платформа с актуальными курсами, вакансиями и проверенными отзывами.',
		'free' => 'Бесплатная платформа',
		'courses' => 'Курсы',
		'vacancies' => 'Вакансии',
		'reviews' => 'Отзывы',
		'login' => 'Войти',
		'hero_title_1' => 'Красивый старт в',
		'hero_title_2' => 'IT',
		'hero_title_3' => 'вместе с нами',
		'hero_sub' => 'Учись, находи работу и развивай карьеру в IT-сфере.',
		'start' => 'Начать бесплатно',
		'show_courses' => 'Посмотреть курсы',
		'users' => 'Пользователи',
		'companies' => 'Компании',
		'rating' => 'Средний рейтинг',
		'stat_title' => 'Живая статистика',
		'stat_courses' => 'Курсы',
		'stat_vacancies' => 'Вакансии',
		'stat_completed' => 'Завершено курсов',
		'trusted' => 'Компании с активными вакансиями',
		'features_title' => 'Почему это работает',
		'f1t' => 'Реальные данные',
		'f1d' => 'Главная строится из данных базы в реальном времени.',
		'f2t' => 'Бесплатный доступ',
		'f2d' => 'Чтобы начать, не нужна платная подписка.',
		'f3t' => 'Быстрый путь',
		'f3d' => 'От обучения до работы внутри одной платформы.',
		'new_courses' => 'Новые курсы',
		'all_courses' => 'Все курсы',
		'open_course' => 'Открыть курс',
		'new_vacancies' => 'Новые вакансии',
		'all_vacancies' => 'Все вакансии',
		'open_vacancy' => 'Открыть вакансию',
		'level' => 'Уровень',
		'lessons' => 'Уроки',
		'on_course' => 'Студентов',
		'updated' => 'Обновлено',
		'company' => 'Компания',
		'location' => 'Локация',
		'type' => 'Тип',
		'salary' => 'Зарплата',
		'salary_na' => 'Не указана',
		'latest_reviews' => 'Последние отзывы',
		'review_rating' => 'Рейтинг',
		'no_data' => 'Пока данных нет.',
		'cta_title' => 'CodeMaster — бесплатная платформа',
		'cta_sub' => 'Присоединяйтесь к сообществу профессионалов и начните путь в IT.',
		'create' => 'Создать бесплатный аккаунт',
		'faq_title' => 'Вопросы и ответы',
		'q1' => 'Как зарегистрироваться и начать обучение?',
		'a1' => 'Нажмите «Создать бесплатный аккаунт», заполните профиль и выберите курс. Все основные курсы доступны сразу после регистрации.',
		'q2' => 'Как получить стажировку или работу?',
		'a2' => 'Пройдите релевантные курсы, заполните портфолио в личном кабинете и откликайтесь на вакансии с пометкой «Стажировка» или «Для начинающих». Рекрутеры видят ваш прогресс.',
		'q3' => 'Как добавить компанию или разместить вакансию?',
		'a3' => 'Зарегистрируйтесь как рекрутер и подтвердите компанию. После одобрения админом появится доступ к панели управления вакансиями.',
		'events_title' => 'IT Events TJ',
		'events_desc' => 'Latest posts from Telegram channel t.me/iteventstj',
		'events_alt' => 'IT Events TJ post',
		'stats_visual' => 'Цифры говорят сами за себя',
		'growth' => 'Рост платформы',
		'code_beauty' => 'Красота кода',
		'debugging' => 'Дебаг в 3 ночи'
	],
	'en' => [
		'title' => 'CodeMaster',
		'desc' => 'Free IT platform with up-to-date courses, vacancies and verified reviews.',
		'free' => 'Free platform',
		'courses' => 'Courses',
		'vacancies' => 'Vacancies',
		'reviews' => 'Reviews',
		'login' => 'Log in',
		'hero_title_1' => 'Beautiful start in',
		'hero_title_2' => 'IT',
		'hero_title_3' => 'with our team',
		'hero_sub' => 'Learn, find jobs and grow your career in IT.',
		'start' => 'Start for free',
		'show_courses' => 'View courses',
		'users' => 'Users',
		'companies' => 'Companies',
		'rating' => 'Average rating',
		'stat_title' => 'Live statistics',
		'stat_courses' => 'Courses',
		'stat_vacancies' => 'Vacancies',
		'stat_completed' => 'Completed courses',
		'trusted' => 'Companies with active vacancies',
		'features_title' => 'Why it works',
		'f1t' => 'Real data',
		'f1d' => 'Homepage is built from your DB data in real time.',
		'f2t' => 'Free access',
		'f2d' => 'No paid subscription required to start.',
		'f3t' => 'Fast path',
		'f3d' => 'From learning to jobs inside one platform.',
		'new_courses' => 'New courses',
		'all_courses' => 'All courses',
		'open_course' => 'Open course',
		'new_vacancies' => 'New vacancies',
		'all_vacancies' => 'All vacancies',
		'open_vacancy' => 'Open vacancy',
		'level' => 'Level',
		'lessons' => 'Lessons',
		'on_course' => 'Students',
		'updated' => 'Updated',
		'company' => 'Company',
		'location' => 'Location',
		'type' => 'Type',
		'salary' => 'Salary',
		'salary_na' => 'Not specified',
		'latest_reviews' => 'Latest reviews',
		'review_rating' => 'Rating',
		'no_data' => 'No data yet.',
		'cta_title' => 'ITsphere is free',
		'cta_sub' => 'Join a community of professionals and start your journey in IT.',
		'create' => 'Create free account',
		'faq_title' => 'Frequently Asked Questions',
		'q1' => 'How to register and start learning?',
		'a1' => 'Click "Create free account", complete your profile and choose a course. All core courses are available immediately after registration.',
		'q2' => 'How to get an internship or job?',
		'a2' => 'Complete relevant courses, build your portfolio in the personal cabinet and apply to vacancies marked "Internship" or "For beginners". Recruiters see your progress.',
		'q3' => 'How to add a company or post a vacancy?',
		'a3' => 'Register as a recruiter, verify your company. After admin approval you will get access to the vacancy management panel.',
		'events_title' => 'IT Events TJ',
		'events_desc' => 'Latest posts from Telegram channel t.me/iteventstj',
		'events_alt' => 'IT Events TJ post',
		'stats_visual' => 'Numbers speak for themselves',
		'growth' => 'Platform growth',
		'code_beauty' => 'Beauty of code',
		'debugging' => 'Debugging at 3 AM'
	],
	'tg' => [
		'title' => 'CodeMaster',
		'desc' => 'Платформаи ройгони IT бо курсҳои нав, вакансияҳо ва баррасиҳои тасдиқшуда.',
		'free' => 'Платформаи ройгон',
		'courses' => 'Курсҳо',
		'vacancies' => 'Вакансияҳо',
		'reviews' => 'Баррасиҳо',
		'login' => 'Дохил шудан',
		'hero_title_1' => 'Оғози зебо дар',
		'hero_title_2' => 'IT',
		'hero_title_3' => 'бо мо',
		'hero_sub' => 'Омӯзед, кор ёбед ва карераи худро дар соҳаи IT рушд диҳед.',
		'start' => 'Ройгон оғоз кунед',
		'show_courses' => 'Дидани курсҳо',
		'users' => 'Истифодабарандагон',
		'companies' => 'Ширкатҳо',
		'rating' => 'Рейтинги миёна',
		'stat_title' => 'Омори дар вақти вош',
		'stat_courses' => 'Курсҳо',
		'stat_vacancies' => 'Вакансияҳо',
		'stat_completed' => 'Курсҳои анҷомёфта',
		'trusted' => 'Ширкатҳо бо вакансияҳои фаъёл',
		'features_title' => 'Чаро кор мекунад',
		'f1t' => 'Маълумоти вош',
		'f1d' => 'Саҳифаи асосӣ аз маълумоти база дар вақти вош сохта мешавад.',
		'f2t' => 'Дастрасии ройгон',
		'f2d' => 'Барои оғоз кардан пуликаш лозим нест.',
		'f3t' => 'Роҳи зуд',
		'f3d' => 'Аз омӯзиш то кор дар як платформа.',
		'new_courses' => 'Курсҳои нав',
		'all_courses' => 'Ҳамаи курсҳо',
		'open_course' => 'Курсро кушодан',
		'new_vacancies' => 'Вакансияҳои нав',
		'all_vacancies' => 'Ҳамаи вакансияҳо',
		'open_vacancy' => 'Вакансияро кушодан',
		'level' => 'Сатҳ',
		'lessons' => 'Дарсҳо',
		'on_course' => 'Донишҷӯён',
		'updated' => 'Навсозӣ шудааст',
		'company' => 'Ширкат',
		'location' => 'Ҷойгириш',
		'type' => 'Навъ',
		'salary' => 'Моош',
		'salary_na' => 'Нишона дода нашудааст',
		'latest_reviews' => 'Баррасиҳои охирин',
		'review_rating' => 'Рейтинг',
		'no_data' => 'Ҳоло маълумот нест.',
		'cta_title' => 'CodeMaster — платформаи ройгон',
		'cta_sub' => 'Ба ҷомеаи мутахассисон гамроғ шавед ва роҳи худро дар IT оғоз кунед.',
		'create' => 'Аккаунти ройгон эҷод кунед',
		'faq_title' => 'Саволҳои зуд-зуд',
		'q1' => 'Чӣ тавр сабти ном карда, омӯзишро оғоз кунам?',
		'a1' => '«Аккаунти ройгон эҷод кунед»-ро пахш кунед, профили худро пур кунед ва курсро интихоб намоед. Ҳамаи курсҳои асосӣ пас аз сабти ном дастрас мешаванд.',
		'q2' => 'Чӣ тавр стажировка ё кор пайдо кунам?',
		'a2' => 'Курсҳои мувофиқро гузаред, портфолиоро дар кабинети шахсӣ пур кунед ва ба вакансияҳое, ки «Стажировка» ё «Барои новомӯзон» доранд, дархост диҳед. Рекрутерҳо пешрафти шуморо мебинанд.',
		'q3' => 'Чӣ тавр ширкатро иловa ё вакансияро нашр кунам?',
		'a3' => 'Ҳамчун рекрутер сабти ном шавед ва ширкатро тасдиқ кунед. Пас аз тасдиқи админ дастрасии панели идоракунии вакансияҳо пайдо мешавад.',
		'events_title' => 'IT Events TJ',
		'events_desc' => 'Latest posts from Telegram channel t.me/iteventstj',
		'events_alt' => 'IT Events TJ post',
		'stats_visual' => 'Рақамҳо худ суғон мегӯянд',
		'growth' => 'Рушди платформа',
		'code_beauty' => 'Зебоии код',
		'debugging' => 'Дебаг соати 3 шаб'
	]
];

$homeExtraI18n = [
	'ru' => [
		'events_nav' => 'IT Events TJ',
		'language_select' => 'Выбрать язык',
		'stats_users_growth_title' => 'Рост пользователей',
		'stats_users_growth_sub' => 'за последний квартал',
		'stats_new_courses_title' => 'Новые курсы',
		'stats_new_courses_sub' => 'добавлены в этом году',
		'stats_avg_rating_title' => 'Средний рейтинг',
		'stats_avg_rating_sub' => 'от реальных пользователей',
		'share' => 'Поделиться',
		'events_footer_tip' => 'Latest posts from Telegram channel t.me/iteventstj.',
		'events_more' => 'Open IT Events TJ',
		'reviews_subtitle' => 'Реальные отзывы пользователей CodeMaster',
		'join_students' => 'Присоединяйтесь к %s+ студентам',
		'courses_word' => 'курсов',
		'vacancies_word' => 'вакансий',
		'rating_word' => 'рейтинг',
		'faq_subtitle' => 'Полезная информация о работе с CodeMaster',
		'support_link' => 'Не нашли ответ? Напишите в поддержку',
	],
	'en' => [
		'events_nav' => 'IT Events TJ',
		'language_select' => 'Select language',
		'stats_users_growth_title' => 'User growth',
		'stats_users_growth_sub' => 'over the last quarter',
		'stats_new_courses_title' => 'New courses',
		'stats_new_courses_sub' => 'added this year',
		'stats_avg_rating_title' => 'Average rating',
		'stats_avg_rating_sub' => 'from real users',
		'share' => 'Share',
		'events_footer_tip' => 'Latest posts from Telegram channel t.me/iteventstj.',
		'events_more' => 'Open IT Events TJ',
		'reviews_subtitle' => 'Real reviews from ITsphere users',
		'join_students' => 'Join %s+ students',
		'courses_word' => 'courses',
		'vacancies_word' => 'vacancies',
		'rating_word' => 'rating',
		'faq_subtitle' => 'Useful information about working with ITsphere',
		'support_link' => 'Did not find an answer? Contact support',
	],
	'tg' => [
		'events_nav' => 'IT Events TJ',
		'language_select' => 'Интихоби забон',
		'stats_users_growth_title' => 'Афзоиши корбарон',
		'stats_users_growth_sub' => 'дар семогаи охир',
		'stats_new_courses_title' => 'Курсҳои нав',
		'stats_new_courses_sub' => 'имсол иловавӣ шуданд',
		'stats_avg_rating_title' => 'Рейтинги миёна',
		'stats_avg_rating_sub' => 'аз корбарони вош',
		'share' => 'Мубодила',
		'events_footer_tip' => 'Latest posts from Telegram channel t.me/iteventstj.',
		'events_more' => 'Open IT Events TJ',
		'reviews_subtitle' => 'Баррасиҳои вошгӣ аз корбарони CodeMaster',
		'join_students' => 'Ба %s+ донишҷӯ гамроғ шавед',
		'courses_word' => 'курс',
		'vacancies_word' => 'вакансия',
		'rating_word' => 'рейтинг',
		'faq_subtitle' => 'Маълумоти муфид дар бораи кори CodeMaster',
		'support_link' => 'Ҷавоб наёфтед? Ба дастгирии техникӣ муроҷиат кунед',
	],
];

if (isset($homeExtraI18n[$lang]) && is_array($homeExtraI18n[$lang])) {
	$t[$lang] = array_merge($t[$lang] ?? [], $homeExtraI18n[$lang]);
}
$tr = static fn($k) => $t[$lang][$k] ?? $k;

$companies = !empty($homeCompanies) ? $homeCompanies : ['Google', 'Microsoft', 'Yandex', 'Sber', 'Ozon', 'VK'];

$roleLabel = static function (string $role) use ($lang): string {
	$map = [
		'ru' => ['seeker' => 'Соискатель', 'recruiter' => 'Рекрутер', 'admin' => 'Администратор'],
		'en' => ['seeker' => 'Candidate', 'recruiter' => 'Recruiter', 'admin' => 'Administrator'],
		'tg' => ['seeker' => 'Ҷӯяндаи кор', 'recruiter' => 'Рекрутер', 'admin' => 'Администратор']
	];
	return $map[$lang][$role] ?? $role;
};

$salaryFrom = $lang === 'en' ? 'from' : ($lang === 'tg' ? 'аз' : 'от');
$salaryTo = $lang === 'en' ? 'to' : ($lang === 'tg' ? 'то' : 'до');
$txtLike = $lang === 'en' ? 'Like' : ($lang === 'tg' ? 'Меписандам' : 'Нравится');

if ($lang === 'en') {
	$extraModulesTitle = 'What else is in the project';
	$extraModulesSub = 'These modules already work, but are not highlighted as separate blocks on the homepage yet.';
	$extraModulesOpen = 'Open module';
	$extraModulesLive = 'LIVE';
	$extraModules = [
		['icon' => 'fa-code', 'title' => 'Contests', 'desc' => 'Algorithmic tasks, checks, and leaderboard.', 'href' => '?action=contests'],
		['icon' => 'fa-users', 'title' => 'Community', 'desc' => 'Discussions, questions, and peer support.', 'href' => '?action=community'],
		['icon' => 'fa-robot', 'title' => 'AI Tutor', 'desc' => 'Chat assistant for learning and growth planning.', 'href' => '?action=dashboard#ai-tutor'],
		['icon' => 'fa-route', 'title' => 'Roadmaps', 'desc' => 'Step-by-step paths by career direction.', 'href' => '?action=roadmaps'],
		['icon' => 'fa-user-tie', 'title' => 'Подготовка к интервью', 'desc' => 'Практика вопросов и шаблоны для собеседований.', 'href' => '?action=courses-interview'],
		['icon' => 'fa-code-branch', 'title' => 'Git Trainer', 'desc' => 'Practice Git commands and common workflows.', 'href' => '?action=git-trainer'],
	];
} elseif ($lang === 'tg') {
	$extraModulesTitle = 'Дар лоиҳа чӣ гунаст';
	$extraModulesSub = 'Ин модулҳо аллакай кор мекунанд, аммо дар саҳифаи асосӣ ҳоло нашудаанд.';
	$extraModulesOpen = 'Кушодани модул';
	$extraModulesLive = 'LIVE';
	$extraModules = [
		['icon' => 'fa-code', 'title' => 'Контестҳо', 'desc' => 'Масъалаҳои алгоритмӣ, санҷишҳо ва лидерборд.', 'href' => '?action=contests'],
		['icon' => 'fa-users', 'title' => 'Ҷомеа', 'desc' => 'Муҳокимаҳо, саволҳо ва дастгирии ҳамтоён.', 'href' => '?action=community'],
		['icon' => 'fa-robot', 'title' => 'AI‑мураббӣ', 'desc' => 'Чат‑ассистент барои омӯзиш ва нақшаи рушд.', 'href' => '?action=dashboard#ai-tutor'],
		['icon' => 'fa-route', 'title' => 'Роадмапҳо', 'desc' => 'Роҳҳои шадам-ба-шадам аз рӯи самти касб.', 'href' => '?action=roadmaps'],
		['icon' => 'fa-user-tie', 'title' => 'Омодагӣ ба интервью', 'desc' => 'Машқҳои саволҳо ва шаблонҳо барои сухбат.', 'href' => '?action=courses-interview'],
		['icon' => 'fa-code-branch', 'title' => 'Git Trainer', 'desc' => 'Машқҳои фармонҳои Git ва равандҳои мажмӯъӣ.', 'href' => '?action=git-trainer'],
	];
} else {
	$extraModulesTitle = 'Что ещё есть в проекте';
	$extraModulesSub = 'Эти модули уже работают, но пока не выделены на главной отдельно.';
	$extraModulesOpen = 'Открыть модуль';
	$extraModulesLive = 'LIVE';
	$extraModules = [
		['icon' => 'fa-code', 'title' => 'Контесты', 'desc' => 'Алгоритмические задачи, проверки и лидерборд.', 'href' => '?action=contests'],
		['icon' => 'fa-users', 'title' => 'Сообщество', 'desc' => 'Обсуждения, вопросы и помощь коллег.', 'href' => '?action=community'],
		['icon' => 'fa-robot', 'title' => 'AI‑Наставник', 'desc' => 'Чат‑ассистент для обучения и планирования роста.', 'href' => '?action=dashboard#ai-tutor'],
		['icon' => 'fa-route', 'title' => 'Роадмапы', 'desc' => 'Пошаговые пути по направлениям карьеры.', 'href' => '?action=roadmaps'],
		['icon' => 'fa-user-tie', 'title' => 'Подготовка к интервью', 'desc' => 'Практика вопросов и шаблоны для собеседований.', 'href' => '?action=courses-interview'],
		['icon' => 'fa-code-branch', 'title' => 'Git Trainer', 'desc' => 'Практика команд Git и типовых сценариев.', 'href' => '?action=git-trainer'],
	];
}
?>
<!doctype html>
<html lang="<?= htmlspecialchars($lang) ?>">

<head>
	<?php include __DIR__ . '/../includes/head_meta.php'; ?>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= htmlspecialchars($tr('title')) ?></title>
	<meta name="description" content="<?= htmlspecialchars($tr('desc')) ?>">
	<script src="https://cdn.tailwindcss.com"></script>
	<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<?php include __DIR__ . '/../includes/csrf.php'; ?>
	<script>
		tailwind.config = {
			theme: {
				extend: {
					colors: {
						brand: { 50: '#eef2ff', 100: '#e0e7ff', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca' }
					},
					animation: {
						float: 'float 6s ease-in-out infinite',
						pulseSlow: 'pulse 4s cubic-bezier(0.4,0,0.6,1) infinite',
						fadeIn: 'fadeIn 0.5s ease-in-out',
						codeFlow: 'codeFlow 25s linear infinite',
						nodeFloat: 'nodeFloat 15s ease-in-out infinite',
						sparkles: 'sparkles 3s ease-in-out infinite'
					},
					keyframes: {
						float: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-10px)' } },
						fadeIn: { '0%': { opacity: 0, transform: 'translateY(10px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
						codeFlow: { '0%': { backgroundPosition: '0% 50%' }, '100%': { backgroundPosition: '100% 50%' } },
						nodeFloat: { '0%,100%': { transform: 'translate(0, 0)' }, '25%': { transform: 'translate(15px, -15px)' }, '50%': { transform: 'translate(0px, -30px)' }, '75%': { transform: 'translate(-15px, -15px)' } },
						sparkles: { '0%,100%': { opacity: 0.3, transform: 'scale(0.8)' }, '50%': { opacity: 1, transform: 'scale(1.2)' } }
					},
					boxShadow: {
						soft: '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)',
						card: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
						hover: '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
						glow: '0 0 15px rgba(79, 70, 229, 0.4)'
					}
				}
			}
		}
	</script>
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500&display=swap');

		html,
		body {
			max-width: 100%;
			overflow-x: hidden;
			font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
		}

		.code-font {
			font-family: 'JetBrains Mono', monospace;
		}

		.stat-card {
			transition: all 0.3s ease;
			box-shadow: var(--tw-shadow-card);
		}

		.stat-card:hover {
			transform: translateY(-2px);
			box-shadow: var(--tw-shadow-hover);
		}

		.feature-card {
			transition: all 0.3s ease;
			border-width: 1px;
			border-color: rgba(0, 0, 0, 0.05);
		}

		.feature-card:hover {
			transform: translateY(-3px);
			box-shadow: var(--tw-shadow-soft);
			border-color: rgba(79, 70, 229, 0.2);
		}

		.course-card,
		.vacancy-card,
		.review-card {
			transition: all 0.3s ease;
			box-shadow: var(--tw-shadow-card);
			border-width: 1px;
			border-color: rgba(0, 0, 0, 0.05);
		}

		.course-card:hover,
		.vacancy-card:hover,
		.review-card:hover {
			transform: translateY(-3px);
			box-shadow: var(--tw-shadow-hover);
			border-color: rgba(79, 70, 229, 0.15);
		}

		.accordion-item {
			transition: all 0.3s ease;
			border-width: 1px;
			border-color: rgba(0, 0, 0, 0.08);
		}

		.accordion-item:hover {
			border-color: rgba(79, 70, 229, 0.2);
		}

		.company-badge {
			transition: all 0.2s ease;
		}

		.company-badge:hover {
			transform: translateY(-1px);
			box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
		}

		.hero-stats {
			transition: all 0.4s ease;
		}

		.hero-stats:hover {
			transform: translateY(-2px);
			box-shadow: var(--tw-shadow-soft);
		}

		.btn-primary {
			transition: all 0.2s ease;
			box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.25);
		}

		.btn-primary:hover {
			transform: translateY(-1px);
			box-shadow: 0 6px 8px -1px rgba(79, 70, 229, 0.35);
		}

		.btn-secondary {
			transition: all 0.2s ease;
		}

		.btn-secondary:hover {
			transform: translateY(-1px);
			box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
		}

		.circuit-bg {
			background-image:
				radial-gradient(circle at 10% 20%, rgba(79, 70, 229, 0.05) 0%, transparent 25%),
				radial-gradient(circle at 90% 80%, rgba(79, 70, 229, 0.05) 0%, transparent 25%),
				linear-gradient(45deg, rgba(79, 70, 229, 0.03) 25%, transparent 25%, transparent 50%, rgba(79, 70, 229, 0.03) 50%, rgba(79, 70, 229, 0.03) 75%, transparent 75%, transparent);
			background-size: 40px 40px;
		}

		.code-pattern {
			background:
				linear-gradient(transparent 49.5%, rgba(79, 70, 229, 0.05) 49.5%, rgba(79, 70, 229, 0.05) 50.5%, transparent 50.5%),
				linear-gradient(90deg, transparent 49.5%, rgba(79, 70, 229, 0.05) 49.5%, rgba(79, 70, 229, 0.05) 50.5%, transparent 50.5%);
			background-size: 20px 20px;
			animation: codeFlow 25s linear infinite;
		}

		.sparkle {
			position: absolute;
			width: 8px;
			height: 8px;
			border-radius: 50%;
			background: rgba(255, 255, 255, 0.8);
			box-shadow: 0 0 10px rgba(255, 255, 255, 0.9);
			animation: sparkles 3s ease-in-out infinite;
		}

		.node {
			position: absolute;
			width: 12px;
			height: 12px;
			border-radius: 50%;
			background: rgba(79, 70, 229, 0.7);
			box-shadow: 0 0 15px rgba(79, 70, 229, 0.8);
		}

		.connection {
			position: absolute;
			background: linear-gradient(90deg, rgba(79, 70, 229, 0.2), rgba(79, 70, 229, 0.6), rgba(79, 70, 229, 0.2));
			height: 2px;
		}

		.chart-bar {
			position: relative;
			height: 8px;
			background: rgba(79, 70, 229, 0.1);
			border-radius: 4px;
			overflow: hidden;
		}

		.chart-fill {
			height: 100%;
			background: linear-gradient(90deg, #4f46e5, #8b5cf6);
			border-radius: 4px;
			width: 0;
			transition: width 1.5s ease-out;
		}

		.radial-chart {
			position: relative;
			width: 80px;
			height: 80px;
			border-radius: 50%;
			background: conic-gradient(#4f46e5 0%, #4f46e5 0%, #e0e7ff 0%, #e0e7ff 100%);
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.radial-chart::before {
			content: '';
			position: absolute;
			width: 65%;
			height: 65%;
			background: white;
			border-radius: 50%;
		}

		.radial-value {
			position: relative;
			font-weight: bold;
			color: #4f46e5;
			font-size: 0.875rem;
		}

		.home-floating-bg {
			z-index: 0;
		}

		header:not(.tf-fixed-header),
		section,
		footer {
			position: relative;
			z-index: 1;
		}

		.tf-fixed-header {
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			z-index: 60;
		}
	</style>
</head>

<body class="bg-gray-50 text-gray-800 overflow-x-hidden tf-public-motion">
	<!-- Floating nodes animation -->
	<div class="home-floating-bg fixed inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
		<div class="node" style="top: 15%; left: 10%; animation-delay: 0s;"></div>
		<div class="node" style="top: 45%; left: 85%; animation-delay: 2s;"></div>
		<div class="node" style="top: 75%; left: 25%; animation-delay: 4s;"></div>
		<div class="node" style="top: 30%; left: 60%; animation-delay: 1s;"></div>
		<div class="sparkle" style="top: 20%; left: 30%; animation-delay: 0.5s;"></div>
		<div class="sparkle" style="top: 60%; left: 70%; animation-delay: 1.5s;"></div>
		<div class="sparkle" style="top: 80%; left: 40%; animation-delay: 2.5s;"></div>
	</div>
	<header x-data="{ mobileMenuOpen: false }" data-fixed-header
		class="tf-fixed-header bg-white/95 backdrop-blur-sm border-b border-gray-100 z-50 shadow-sm"
		style="margin-top: 0;">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-16 py-2 flex items-center justify-between gap-3">
			<a href="?action=home" class="flex items-center gap-3 group">
				<div
					class="w-12 h-12 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-500 text-white flex items-center justify-center shadow-md transition-transform group-hover:scale-105 relative overflow-hidden">
					<i class="fas fa-graduation-cap text-xl relative z-10"></i>
					<div
						class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.3),transparent_70%)]">
					</div>
				</div>
				<div>
					<p
						class="font-bold text-xl leading-none bg-clip-text text-transparent bg-gradient-to-r from-brand-600 to-indigo-600">
						CodeMaster</p>
					<p class="text-xs text-gray-500 mt-0.5"><?= htmlspecialchars($tr('free')) ?></p>
				</div>
			</a>
			<nav class="hidden md:flex items-center gap-8 text-sm font-medium">
				<a href="#courses" class="text-gray-700 hover:text-brand-600 transition-colors relative group">
					<?= htmlspecialchars($tr('courses')) ?>
					<span
						class="absolute -bottom-1 left-0 w-0 h-0.5 bg-brand-600 transition-all group-hover:w-full"></span>
				</a>
				<a href="#vacancies" class="text-gray-700 hover:text-brand-600 transition-colors relative group">
					<?= htmlspecialchars($tr('vacancies')) ?>
					<span
						class="absolute -bottom-1 left-0 w-0 h-0.5 bg-brand-600 transition-all group-hover:w-full"></span>
				</a>
				<a href="#reviews" class="text-gray-700 hover:text-brand-600 transition-colors relative group">
					<?= htmlspecialchars($tr('reviews')) ?>
					<span
						class="absolute -bottom-1 left-0 w-0 h-0.5 bg-brand-600 transition-all group-hover:w-full"></span>
				</a>
				<a href="#events" class="text-gray-700 hover:text-brand-600 transition-colors relative group">
					<span class="hidden lg:inline"><?= htmlspecialchars($tr('events_nav')) ?></span>
				</a>
			</nav>
			<div class="flex items-center gap-3">
				<div class="hidden md:block relative">
					<select onchange="window.location.search = this.value"
						aria-label="<?= htmlspecialchars($tr('language_select')) ?>"
						class="appearance-none bg-gray-50 border border-gray-200 rounded-lg text-sm py-1.5 pl-3 pr-8 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
						<option value="<?= htmlspecialchars($langUrl('ru')) ?>" <?= $lang === 'ru' ? 'selected' : '' ?>>
							<?= htmlspecialchars(t('lang_ru', 'Р В РЎС“РЎРѓРЎРѓР С”Р С‘Р в„–')) ?>
						</option>
						<option value="<?= htmlspecialchars($langUrl('en')) ?>" <?= $lang === 'en' ? 'selected' : '' ?>>
							<?= htmlspecialchars(t('lang_en', 'English')) ?>
						</option>
						<option value="<?= htmlspecialchars($langUrl('tg')) ?>" <?= $lang === 'tg' ? 'selected' : '' ?>>
							<?= htmlspecialchars(t('lang_tg', 'Р СћР С•РўВ·Р С‘Р С”РЈР€')) ?>
						</option>
					</select>
					<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
						<i class="fas fa-chevron-down text-xs"></i>
					</div>
				</div>
				<a href="?action=login"
					class="hidden sm:inline-flex bg-brand-600 hover:bg-brand-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold items-center gap-2 shadow-md">
					<i class="fas fa-sign-in-alt"></i>
					<span><?= htmlspecialchars($tr('login')) ?></span>
				</a>
				<button type="button"
					class="md:hidden text-gray-700 hover:text-brand-600 p-2 rounded-lg hover:bg-gray-100"
					@click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen.toString()"
					aria-controls="home-mobile-menu">
					<i class="fas text-xl" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
				</button>
			</div>
		</div>
		<div id="home-mobile-menu" x-show="mobileMenuOpen" x-transition.origin.top.duration.150ms
			class="md:hidden border-t border-gray-200" @keydown.escape.window="mobileMenuOpen = false"
			style="display: none;">
			<div class="px-4 py-3 space-y-2">
				<a href="#courses" @click="mobileMenuOpen = false"
					class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">
					<?= htmlspecialchars($tr('courses')) ?>
				</a>
				<a href="#vacancies" @click="mobileMenuOpen = false"
					class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">
					<?= htmlspecialchars($tr('vacancies')) ?>
				</a>
				<a href="#reviews" @click="mobileMenuOpen = false"
					class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">
					<?= htmlspecialchars($tr('reviews')) ?>
				</a>
				<a href="#events" @click="mobileMenuOpen = false"
					class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">
					<?= htmlspecialchars($tr('events_title')) ?>
				</a>
				<div class="pt-2 border-t border-gray-100 space-y-2">
					<select onchange="window.location.search = this.value"
						aria-label="<?= htmlspecialchars($tr('language_select')) ?>"
						class="w-full appearance-none bg-gray-50 border border-gray-200 rounded-lg text-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
						<option value="<?= htmlspecialchars($langUrl('ru')) ?>" <?= $lang === 'ru' ? 'selected' : '' ?>>
							<?= htmlspecialchars(t('lang_ru', 'Р В РЎС“РЎРѓРЎРѓР С”Р С‘Р в„–')) ?>
						</option>
						<option value="<?= htmlspecialchars($langUrl('en')) ?>" <?= $lang === 'en' ? 'selected' : '' ?>>
							<?= htmlspecialchars(t('lang_en', 'English')) ?>
						</option>
						<option value="<?= htmlspecialchars($langUrl('tg')) ?>" <?= $lang === 'tg' ? 'selected' : '' ?>>
							<?= htmlspecialchars(t('lang_tg', 'Р СћР С•РўВ·Р С‘Р С”РЈР€')) ?>
						</option>
					</select>
					<a href="?action=login" @click="mobileMenuOpen = false"
						class="w-full inline-flex justify-center items-center bg-brand-600 hover:bg-brand-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold shadow-md">
						<i class="fas fa-sign-in-alt mr-2"></i><?= htmlspecialchars($tr('login')) ?>
					</a>
				</div>
			</div>
		</div>
	</header>
	<script>
		(() => {
			const header = document.querySelector('[data-fixed-header]');
			if (!header) return;
			const setOffset = () => {
				document.body.style.paddingTop = `${header.offsetHeight}px`;
			};
			setOffset();
			window.addEventListener('resize', setOffset);
		})();
	</script>
	<section class="relative overflow-hidden bg-gradient-to-br from-brand-50 via-white to-indigo-50/5 py-16 md:py-24">
		<div class="absolute top-10 left-8 w-72 h-72 bg-brand-100 rounded-full blur-3xl opacity-60 animate-pulseSlow">
		</div>
		<div
			class="absolute bottom-10 right-8 w-72 h-72 bg-indigo-100 rounded-full blur-3xl opacity-60 animate-pulseSlow">
		</div>
		<div
			class="absolute top-1/2 left-1/2 w-96 h-96 bg-purple-50 rounded-full blur-3xl opacity-20 transform -translate-x-1/2 -translate-y-1/2">
		</div>
		<!-- Code background pattern -->
		<div class="absolute inset-0 opacity-5 pointer-events-none code-pattern"></div>
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
			<div class="grid lg:grid-cols-2 gap-12 items-center">
				<div class="animate-fadeIn">
					<h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
						<?= htmlspecialchars($tr('hero_title_1')) ?>
						<span
							class="bg-gradient-to-r from-brand-600 to-indigo-600 bg-clip-text text-transparent block md:inline"><?= htmlspecialchars($tr('hero_title_2')) ?></span>
						<?= htmlspecialchars($tr('hero_title_3')) ?>
					</h1>
					<p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-2xl"><?= htmlspecialchars($tr('hero_sub')) ?>
					</p>
					<div class="flex flex-col sm:flex-row gap-4 mb-12">
						<a href="?action=login"
							class="inline-flex justify-center items-center px-7 py-3.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-lg shadow-lg">
							<i class="fas fa-rocket mr-2"></i><?= htmlspecialchars($tr('start')) ?>
						</a>
						<a href="#courses"
							class="btn-secondary inline-flex justify-center items-center px-7 py-3.5 rounded-xl border-2 border-gray-200 text-gray-800 hover:border-brand-500 hover:text-brand-600 font-medium text-lg transition-colors">
							<i class="fas fa-book-open mr-2"></i><?= htmlspecialchars($tr('show_courses')) ?>
						</a>
					</div>
					<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-xl">
						<div class="hero-stats bg-white rounded-2xl p-5 border border-gray-100 text-center">
							<div class="text-3xl font-bold text-brand-600 mb-1"><?= $formatCount($studentsCount) ?>
							</div>
							<div class="text-sm text-gray-600"><?= htmlspecialchars($tr('users')) ?></div>
						</div>
						<div class="hero-stats bg-white rounded-2xl p-5 border border-gray-100 text-center">
							<div class="text-3xl font-bold text-green-600 mb-1"><?= $formatCount(count($companies)) ?>
							</div>
							<div class="text-sm text-gray-600"><?= htmlspecialchars($tr('companies')) ?></div>
						</div>
						<div class="hero-stats bg-white rounded-2xl p-5 border border-gray-100 text-center">
							<div class="text-3xl font-bold text-amber-500 mb-1">
								<?= $avgRating > 0 ? number_format($avgRating, 1) : '0.0' ?>
							</div>
							<div class="text-sm text-gray-600"><?= htmlspecialchars($tr('rating')) ?></div>
						</div>
					</div>
				</div>
				<div
					class="bg-white rounded-2xl border border-gray-100 shadow-soft p-7 animate-float relative overflow-hidden">
					<div class="absolute -right-10 -top-10 w-40 h-40 bg-brand-50 rounded-full blur-2xl opacity-70">
					</div>
					<div class="absolute -left-5 -bottom-5 w-32 h-32 bg-indigo-50 rounded-full blur-2xl opacity-70">
					</div>
					<div class="flex items-center justify-between mb-6 relative z-10">
						<h2 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($tr('stat_title')) ?></h2>
						<div class="w-10 h-10 rounded-full bg-brand-50 flex items-center justify-center text-brand-600">
							<i class="fas fa-chart-line"></i>
						</div>
					</div>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 relative z-10">
						<div
							class="stat-card rounded-xl bg-gradient-to-br from-brand-50 to-white p-5 border border-gray-100">
							<div class="flex items-center text-brand-600 mb-2">
								<i class="fas fa-book text-lg mr-2"></i>
								<span class="text-sm font-medium"><?= htmlspecialchars($tr('stat_courses')) ?></span>
							</div>
							<div class="text-3xl font-bold text-gray-800 mb-2"><?= $formatCount($coursesCount) ?></div>
							<div class="chart-bar mt-2">
								<div class="chart-fill"
									style="width: <?= min(100, max(20, $coursesCount / 100 * 20)) ?>%"></div>
							</div>
						</div>
						<div
							class="stat-card rounded-xl bg-gradient-to-br from-indigo-50 to-white p-5 border border-gray-100">
							<div class="flex items-center text-indigo-600 mb-2">
								<i class="fas fa-briefcase text-lg mr-2"></i>
								<span class="text-sm font-medium"><?= htmlspecialchars($tr('stat_vacancies')) ?></span>
							</div>
							<div class="text-3xl font-bold text-gray-800 mb-2"><?= $formatCount($vacanciesCount) ?>
							</div>
							<div class="chart-bar mt-2">
								<div class="chart-fill"
									style="width: <?= min(100, max(20, $vacanciesCount / 50 * 20)) ?>%"></div>
							</div>
						</div>
						<div
							class="stat-card rounded-xl bg-gradient-to-br from-amber-50 to-white p-5 border border-gray-100 col-span-2">
							<div class="flex items-center text-amber-600 mb-2">
								<i class="fas fa-award text-lg mr-2"></i>
								<span class="text-sm font-medium"><?= htmlspecialchars($tr('stat_completed')) ?></span>
							</div>
							<div class="text-3xl font-bold text-gray-800 mb-2">
								<?= $formatCount($completedCoursesCount) ?>
							</div>
							<div class="chart-bar mt-2">
								<div class="chart-fill"
									style="width: <?= min(100, max(30, $completedCoursesCount / 200 * 30)) ?>%"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="py-6 bg-white border-y border-gray-100">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<p class="text-center text-gray-500 text-sm font-medium mb-4"><?= htmlspecialchars($tr('trusted')) ?></p>
			<div class="flex flex-wrap justify-center gap-3">
				<?php foreach ($companies as $company): ?>
					<div
						class="company-badge px-4 py-2 rounded-xl border border-gray-200 bg-gray-50 text-sm font-medium text-gray-700 hover:bg-white flex items-center gap-2">
						<?php if (in_array($company, ['Google', 'Microsoft', 'Yandex'])): ?>
							<i
								class="fab fa-<?= strtolower($company) === 'google' ? 'google' : (strtolower($company) === 'microsoft' ? 'microsoft' : 'yandex') ?> text-xl"></i>
						<?php else: ?>
							<i class="fas fa-building text-xl text-gray-400"></i>
						<?php endif; ?>
						<?= htmlspecialchars((string) $company) ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<section class="py-16 bg-white border-y border-gray-100">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="text-center max-w-3xl mx-auto mb-10">
				<h2 class="text-3xl md:text-4xl font-bold mb-4"><?= htmlspecialchars($extraModulesTitle) ?></h2>
				<p class="text-lg text-gray-600"><?= htmlspecialchars($extraModulesSub) ?></p>
			</div>
			<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
				<?php foreach ($extraModules as $module): ?>
					<a href="<?= htmlspecialchars((string) $module['href']) ?>"
						class="feature-card rounded-2xl p-6 bg-gradient-to-br from-white to-gray-50 block hover:bg-white">
						<div class="flex items-start justify-between mb-4">
							<div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
								<i class="fas <?= htmlspecialchars((string) $module['icon']) ?> text-lg"></i>
							</div>
							<span
								class="text-xs font-semibold text-brand-600 bg-brand-50 px-2 py-1 rounded-full"><?= htmlspecialchars($extraModulesLive) ?></span>
						</div>
						<h3 class="text-lg font-bold text-gray-900 mb-2"><?= htmlspecialchars((string) $module['title']) ?>
						</h3>
						<p class="text-sm text-gray-600 leading-relaxed mb-4">
							<?= htmlspecialchars((string) $module['desc']) ?>
						</p>
						<div class="inline-flex items-center text-sm font-semibold text-brand-600">
							<?= htmlspecialchars($extraModulesOpen) ?>
							<i class="fas fa-arrow-right ml-2 text-xs"></i>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<!-- Visual Stats Section -->
	<section class="py-16 bg-gradient-to-b from-gray-50 to-white relative overflow-hidden">
		<div class="absolute inset-0 opacity-40">
			<div class="absolute top-20 left-10 w-64 h-64 bg-brand-100 rounded-full blur-3xl"></div>
			<div class="absolute bottom-20 right-10 w-64 h-64 bg-indigo-100 rounded-full blur-3xl"></div>
		</div>
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
			<div class="text-center max-w-3xl mx-auto mb-14 animate-fadeIn">
				<h2 class="text-3xl md:text-4xl font-bold mb-4"><?= htmlspecialchars($tr('stats_visual')) ?></h2>
				<p class="text-lg text-gray-600"><?= htmlspecialchars($tr('growth')) ?></p>
			</div>
			<div class="grid md:grid-cols-3 gap-8 mb-16">
				<div class="bg-white rounded-2xl p-7 border border-gray-100 text-center">
					<div class="radial-chart mx-auto mb-4"
						style="--percentage: <?= min(100, $studentsCount / 10000 * 100) ?>%; background: conic-gradient(#4f46e5 var(--percentage), #e0e7ff var(--percentage));">
						<span class="radial-value"><?= min(100, (int) ($studentsCount / 100)) ?>%</span>
					</div>
					<h3 class="font-bold text-lg mb-1"><?= htmlspecialchars($tr('stats_users_growth_title')) ?></h3>
					<p class="text-gray-600 text-sm"><?= htmlspecialchars($tr('stats_users_growth_sub')) ?></p>
				</div>
				<div class="bg-white rounded-2xl p-7 border border-gray-100 text-center">
					<div class="radial-chart mx-auto mb-4"
						style="--percentage: <?= min(100, $coursesCount / 200 * 100) ?>%; background: conic-gradient(#4f46e5 var(--percentage), #e0e7ff var(--percentage));">
						<span class="radial-value"><?= min(100, (int) ($coursesCount / 2)) ?>%</span>
					</div>
					<h3 class="font-bold text-lg mb-1"><?= htmlspecialchars($tr('stats_new_courses_title')) ?></h3>
					<p class="text-gray-600 text-sm"><?= htmlspecialchars($tr('stats_new_courses_sub')) ?></p>
				</div>
				<div class="bg-white rounded-2xl p-7 border border-gray-100 text-center">
					<div class="radial-chart mx-auto mb-4"
						style="--percentage: <?= min(100, $avgRating / 5 * 100) ?>%; background: conic-gradient(#4f46e5 var(--percentage), #e0e7ff var(--percentage));">
						<span class="radial-value"><?= number_format($avgRating, 1) ?></span>
					</div>
					<h3 class="font-bold text-lg mb-1"><?= htmlspecialchars($tr('stats_avg_rating_title')) ?></h3>
					<p class="text-gray-600 text-sm"><?= htmlspecialchars($tr('stats_avg_rating_sub')) ?></p>
				</div>
			</div>
			<!-- Code beauty illustration -->
			<div class="max-w-4xl mx-auto bg-gray-900 rounded-2xl p-6 md:p-8 overflow-hidden relative">
				<div
					class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_50%_50%,rgba(79,70,229,0.3),transparent_70%)]">
				</div>
				<div class="flex justify-between mb-4">
					<div class="flex space-x-2">
						<span class="w-3 h-3 rounded-full bg-red-500"></span>
						<span class="w-3 h-3 rounded-full bg-yellow-400"></span>
						<span class="w-3 h-3 rounded-full bg-green-500"></span>
					</div>
					<span class="text-gray-500 text-sm">beautiful_code.js</span>
				</div>
				<pre
					class="code-font text-brand-300 text-sm md:text-base leading-relaxed whitespace-pre-wrap break-words"><code><span class="text-amber-300">const</span> <span class="text-green-400">itsphere</span> = {
<span class="text-cyan-300">mission</span>: <span class="text-emerald-300">'Empower IT careers'</span>,
<span class="text-cyan-300">values</span>: [<span class="text-emerald-300">'Free access'</span>, <span class="text-emerald-300">'Real data'</span>, <span class="text-emerald-300">'No hidden fees'</span>],
<span class="text-cyan-300">startJourney</span>: () => {
<span class="text-amber-300">return</span> <span class="text-emerald-300">'Your beautiful IT career begins here'</span>;
}
};
<span class="text-blue-300">console</span>.<span class="text-purple-300">log</span>(itsphere.<span class="text-cyan-300">startJourney</span>());
<span class="text-gray-500">// Output: Your beautiful IT career begins here ?</span></code></pre>
				<div class="mt-4 flex items-center justify-center text-amber-400">
					<i class="fas fa-magic mr-2"></i>
					<span><?= htmlspecialchars($tr('code_beauty')) ?></span>
				</div>
			</div>
		</div>
	</section>
	<section class="py-16 bg-white" id="courses">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="flex items-center justify-between mb-10">
				<h2 class="text-3xl font-bold"><?= htmlspecialchars($tr('new_courses')) ?></h2>
				<a href="?action=courses"
					class="inline-flex items-center text-brand-600 font-bold hover:text-brand-700 transition-colors">
					<?= htmlspecialchars($tr('all_courses')) ?>
					<i class="fas fa-arrow-right ml-2 text-sm"></i>
				</a>
			</div>
			<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-7">
				<?php if (!empty($homeCourses)): ?>
					<?php foreach ($homeCourses as $index => $course): ?>
						<article class="course-card bg-white rounded-2xl p-6 relative overflow-hidden">
							<!-- Decorative corner element -->
							<div class="absolute top-0 right-0 w-24 h-24 bg-brand-50 rounded-bl-full opacity-60"></div>
							<div class="relative z-10">
								<div class="flex items-start justify-between gap-3 mb-4">
									<h3 class="font-bold text-xl leading-tight text-gray-800">
										<?= htmlspecialchars((string) ($course['title'] ?? '')) ?>
									</h3>
									<?php if (!empty($course['level'])): ?>
										<span
											class="text-xs px-3 py-1.5 rounded-full bg-brand-50 text-brand-700 font-medium whitespace-nowrap"><?= htmlspecialchars((string) $course['level']) ?></span>
									<?php endif; ?>
								</div>
								<p class="text-gray-600 mb-5 min-h-[56px] leading-relaxed">
									<?= htmlspecialchars((string) ($course['description'] ?? '')) ?>
								</p>
								<div class="grid grid-cols-2 gap-3 mb-5">
									<div class="flex items-center">
										<div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center mr-3">
											<i class="fas fa-clipboard-list text-brand-600"></i>
										</div>
										<div>
											<p class="text-xs text-gray-500"><?= htmlspecialchars($tr('lessons')) ?></p>
											<p class="font-bold text-gray-800">
												<?= $formatCount((int) ($course['lessons_count'] ?? 0)) ?>
											</p>
										</div>
									</div>
									<div class="flex items-center">
										<div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center mr-3">
											<i class="fas fa-user-graduate text-amber-600"></i>
										</div>
										<div>
											<p class="text-xs text-gray-500"><?= htmlspecialchars($tr('on_course')) ?></p>
											<p class="font-bold text-gray-800">
												<?= $formatCount((int) ($course['students_count'] ?? 0)) ?>
											</p>
										</div>
									</div>
								</div>
								<div class="flex items-center justify-between text-sm text-gray-500 border-t pt-4 mt-2">
									<span><?= htmlspecialchars($tr('updated')) ?>:
										<?= htmlspecialchars($formatDate($course['created_at'] ?? null)) ?></span>
									<a href="?action=get-course&id=<?= (int) ($course['id'] ?? 0) ?>"
										class="text-brand-600 font-medium hover:text-brand-700 flex items-center">
										<?= htmlspecialchars($tr('open_course')) ?>
										<i class="fas fa-arrow-right ml-1 text-xs"></i>
									</a>
								</div>
							</div>
							<!-- Decorative code snippet -->
							<div
								class="absolute bottom-0 left-0 w-full h-12 bg-gradient-to-t from-brand-50 to-transparent flex items-end overflow-hidden">
								<div
									class="code-font text-xs text-brand-400 px-4 pb-1 opacity-70 whitespace-nowrap animate-[codeFlow_40s_linear_infinite]">
								</div>
							</div>
						</article>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="md:col-span-3 p-12 text-center border-2 border-dashed rounded-2xl text-gray-500 bg-gray-50">
						<i class="fas fa-info-circle text-4xl mb-4 block text-gray-300"></i>
						<p class="text-lg"><?= htmlspecialchars($tr('no_data')) ?></p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<section id="vacancies" class="py-16 bg-gradient-to-b from-gray-50 to-white">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="flex items-center justify-between mb-10">
				<h2 class="text-3xl font-bold"><?= htmlspecialchars($tr('new_vacancies')) ?></h2>
				<a href="?action=vacancies"
					class="inline-flex items-center text-brand-600 font-bold hover:text-brand-700 transition-colors">
					<?= htmlspecialchars($tr('all_vacancies')) ?>
					<i class="fas fa-arrow-right ml-2 text-sm"></i>
				</a>
			</div>
			<div class="grid md:grid-cols-2 gap-7">
				<?php if (!empty($homeVacancies)): ?>
					<?php foreach ($homeVacancies as $vacancy): ?>
						<?php
						$min = $formatSalary($vacancy['salary_min'] ?? 0);
						$max = $formatSalary($vacancy['salary_max'] ?? 0);
						$currencyCode = strtoupper((string) ($vacancy['salary_currency'] ?? 'TJS'));
						$currencyLabel = t('currency_' . strtolower($currencyCode), $currencyCode);
						?>
						<article class="vacancy-card rounded-2xl p-6 bg-white relative overflow-hidden">
							<!-- Decorative circuit pattern -->
							<div class="absolute inset-0 opacity-5 circuit-bg pointer-events-none"></div>
							<div class="relative z-10">
								<div class="flex items-start justify-between gap-3 mb-3">
									<h3 class="font-bold text-xl leading-tight text-gray-800">
										<?= htmlspecialchars((string) ($vacancy['title'] ?? '')) ?>
									</h3>
									<?php if (!empty($vacancy['type'])): ?>
										<span
											class="text-xs px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 font-medium whitespace-nowrap"><?= htmlspecialchars((string) $vacancy['type']) ?></span>
									<?php endif; ?>
								</div>
								<div class="flex items-center text-gray-600 mb-4">
									<i class="fas fa-building text-gray-400 mr-2"></i>
									<span><?= htmlspecialchars($tr('company')) ?>: <strong
											class="text-gray-800"><?= htmlspecialchars((string) ($vacancy['company'] ?? '-')) ?></strong></span>
								</div>
								<div class="grid grid-cols-2 gap-3 mb-5 text-sm">
									<div class="flex items-center">
										<div
											class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center mr-3 flex-shrink-0">
											<i class="fas fa-map-marker-alt text-blue-600"></i>
										</div>
										<div>
											<p class="text-xs text-gray-500"><?= htmlspecialchars($tr('location')) ?></p>
											<p class="font-medium text-gray-800">
												<?= htmlspecialchars((string) ($vacancy['location'] ?? '-')) ?>
											</p>
										</div>
									</div>
									<div class="flex items-center">
										<div
											class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center mr-3 flex-shrink-0">
											<i class="fas fa-money-bill-wave text-purple-600"></i>
										</div>
										<div>
											<p class="text-xs text-gray-500"><?= htmlspecialchars($tr('salary')) ?></p>
											<p class="font-bold text-gray-800">
												<?php if ($min !== null || $max !== null): ?>
													<span
														class="text-green-600"><?= $min !== null ? ($salaryFrom . ' ' . $min) : '' ?><?= ($min !== null && $max !== null) ? ' - ' : '' ?><?= $max !== null ? ($salaryTo . ' ' . $max) : '' ?>
														<?= $currencyLabel ?></span>
												<?php else: ?>
													<span class="text-gray-400"><?= htmlspecialchars($tr('salary_na')) ?></span>
												<?php endif; ?>
											</p>
										</div>
									</div>
								</div>
								<div class="flex items-center justify-between text-sm text-gray-500 border-t pt-4 mt-2">
									<span><?= htmlspecialchars($tr('updated')) ?>:
										<?= htmlspecialchars($formatDate($vacancy['created_at'] ?? null)) ?></span>
									<a href="?action=vacancies"
										class="text-brand-600 font-medium hover:text-brand-700 flex items-center">
										<?= htmlspecialchars($tr('open_vacancy')) ?>
										<i class="fas fa-arrow-right ml-1 text-xs"></i>
									</a>
								</div>
							</div>
							<!-- Decorative node connection -->
							<div class="absolute -bottom-3 -right-3 w-16 h-16 bg-indigo-100 rounded-full opacity-30"></div>
						</article>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="md:col-span-2 p-12 text-center border-2 border-dashed rounded-2xl text-gray-500 bg-gray-50">
						<i class="fas fa-info-circle text-4xl mb-4 block text-gray-300"></i>
						<p class="text-lg"><?= htmlspecialchars($tr('no_data')) ?></p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<section id="events" class="py-16 bg-gradient-to-br from-sky-50 to-indigo-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="text-center max-w-3xl mx-auto mb-12 animate-fadeIn">
				<div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-sky-100 text-sky-600 mb-4 mx-auto">
					<i class="fas fa-calendar-days text-2xl"></i>
				</div>
				<h2 class="text-3xl font-bold mb-4"><?= htmlspecialchars($tr('events_title')) ?></h2>
				<p class="text-lg text-gray-600"><?= htmlspecialchars($tr('events_desc')) ?></p>
			</div>
			<?php if (!empty($homeEventsPosts)): ?>
				<div class="grid md:grid-cols-3 gap-6">
					<?php foreach ($homeEventsPosts as $eventPost): ?>
						<?php
						$eventUrl = (string) ($eventPost['url'] ?? 'https://t.me/iteventstj');
						$eventText = (string) ($eventPost['excerpt'] ?? '');
						$eventImage = trim((string) ($eventPost['image'] ?? ''));
						?>
						<article class="bg-white rounded-2xl border border-sky-100 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
							<?php if ($eventImage !== ''): ?>
								<a href="<?= htmlspecialchars($eventUrl) ?>" target="_blank" rel="noopener noreferrer">
									<img src="<?= htmlspecialchars($eventImage) ?>" alt="<?= htmlspecialchars($tr('events_alt')) ?>" class="w-full h-40 object-cover" loading="lazy">
								</a>
							<?php endif; ?>
							<div class="p-5">
								<div class="text-xs font-semibold text-sky-700 mb-2">t.me/iteventstj</div>
								<p class="text-sm text-gray-700 min-h-[72px]">
									<?= htmlspecialchars($eventText !== '' ? $eventText : '...') ?>
								</p>
								<a href="<?= htmlspecialchars($eventUrl) ?>" target="_blank" rel="noopener noreferrer"
									class="inline-flex items-center mt-4 text-sm font-semibold text-sky-700 hover:text-sky-900">
									<?= htmlspecialchars($tr('events_more')) ?>
									<i class="fas fa-arrow-up-right-from-square ml-2 text-xs"></i>
								</a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<div class="max-w-3xl mx-auto bg-white rounded-2xl border border-sky-100 p-8 text-center text-gray-600">
					<?= htmlspecialchars($tr('events_footer_tip')) ?>
				</div>
			<?php endif; ?>
			<div class="mt-10 text-center">
				<a href="https://t.me/iteventstj" target="_blank" rel="noopener noreferrer"
					class="inline-flex items-center px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-xl transition-colors">
					<i class="fab fa-telegram-plane mr-2"></i><?= htmlspecialchars($tr('events_more')) ?>
				</a>
			</div>
		</div>
	</section>
	<section id="reviews" class="py-16 bg-white">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="text-center max-w-3xl mx-auto mb-12 animate-fadeIn">
				<h2 class="text-3xl font-bold mb-4"><?= htmlspecialchars($tr('latest_reviews')) ?></h2>
				<p class="text-lg text-gray-600"><?= htmlspecialchars($tr('reviews_subtitle')) ?></p>
			</div>
			<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-7">
				<?php if (!empty($platformReviews)): ?>
					<?php foreach ($platformReviews as $review): ?>
						<?php
						$reviewRating = (float) ($review['rating'] ?? 0);
						if ($reviewRating < 0) {
							$reviewRating = 0;
						}
						if ($reviewRating > 5) {
							$reviewRating = 5;
						}
						$filledStars = (int) round($reviewRating);
						?>
						<article class="review-card bg-white rounded-2xl p-7 relative overflow-hidden">
							<div
								class="absolute -top-4 -left-4 w-16 h-16 rounded-xl bg-gradient-to-br from-brand-500 to-indigo-600 flex items-center justify-center">
								<i class="fas fa-quote-right text-white text-2xl opacity-80"></i>
							</div>
							<div class="absolute top-0 right-0 w-32 h-32 bg-brand-50 rounded-bl-full opacity-30"></div>
							<div class="relative z-10">
								<div class="flex items-center justify-between mb-4 pt-3">
									<h3 class="font-bold text-lg"><?= htmlspecialchars((string) ($review['name'] ?? '')) ?></h3>
									<span
										class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700 font-medium"><?= htmlspecialchars($roleLabel((string) ($review['role'] ?? ''))) ?></span>
								</div>
								<div class="text-amber-400 mb-4 text-lg">
									<?php for ($i = 1; $i <= 5; $i++): ?>
										<i class="<?= $i <= $filledStars ? 'fas' : 'far' ?> fa-star"></i>
									<?php endfor; ?>
								</div>
								<p class="text-gray-600 mb-5 leading-relaxed min-h-[80px]">
									<?= nl2br(htmlspecialchars((string) ($review['comment'] ?? ''))) ?>
								</p>
								<div class="flex items-center justify-between text-sm text-gray-500 pt-4 border-t">
									<span class="font-medium"><?= htmlspecialchars($tr('review_rating')) ?>: <span
											class="text-brand-600"><?= number_format((float) ($review['rating'] ?? 0), 1) ?></span></span>
									<span><?= htmlspecialchars(date('d.m.Y', strtotime((string) ($review['created_at'] ?? 'now')))) ?></span>
								</div>
							</div>
						</article>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="md:col-span-3 p-12 text-center border-2 border-dashed rounded-2xl text-gray-500 bg-gray-50">
						<i class="fas fa-comment-dots text-4xl mb-4 block text-gray-300"></i>
						<p class="text-lg"><?= htmlspecialchars($tr('no_data')) ?></p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<section class="py-16 bg-gradient-to-r from-brand-600 to-indigo-700 text-white relative overflow-hidden">
		<div class="absolute inset-0 opacity-10">
			<div class="absolute top-10 left-10 w-72 h-72 bg-white rounded-full blur-3xl opacity-20"></div>
			<div class="absolute bottom-10 right-10 w-72 h-72 bg-white rounded-full blur-3xl opacity-20"></div>
			<div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.1),transparent_70%)]">
			</div>
		</div>
		<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
			<div class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm mb-6">
				<i class="fas fa-star text-yellow-300 mr-2"></i>
				<span
					class="font-medium"><?= htmlspecialchars(sprintf($tr('join_students'), $formatCount($studentsCount))) ?></span>
			</div>
			<h2 class="text-3xl md:text-4xl font-bold mb-6"><?= htmlspecialchars($tr('cta_title')) ?></h2>
			<p class="text-xl opacity-95 mb-10 max-w-3xl mx-auto"><?= htmlspecialchars($tr('cta_sub')) ?></p>
			<a href="?action=login"
				class="inline-flex items-center px-8 py-4 rounded-xl bg-white text-brand-700 font-bold text-lg hover:bg-gray-100 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
				<i class="fas fa-user-plus mr-3"></i>
				<?= htmlspecialchars($tr('create')) ?>
			</a>
			<div class="mt-12 flex justify-center flex-wrap gap-8 opacity-90">
				<div class="text-center">
					<div class="text-3xl font-bold"><?= $formatCount($coursesCount) ?></div>
					<div class="text-gray-200"><?= htmlspecialchars($tr('courses_word')) ?></div>
				</div>
				<div class="text-center">
					<div class="text-3xl font-bold"><?= $formatCount($vacanciesCount) ?></div>
					<div class="text-gray-200"><?= htmlspecialchars($tr('vacancies_word')) ?></div>
				</div>
				<div class="text-center">
					<div class="text-3xl font-bold"><?= number_format($avgRating, 1) ?></div>
					<div class="text-gray-200"><?= htmlspecialchars($tr('rating_word')) ?></div>
				</div>
			</div>
			<!-- Decorative binary code background -->
			<div class="mt-16 opacity-20 code-font text-xs select-none whitespace-nowrap overflow-hidden">
				<div class="animate-[codeFlow_60s_linear_infinite]">
					01010100 01100001 01101100 01100101 01101110 01110100 01000110 01101100 01101111 01110111 00100000
					01101001 01110011 00100000 01100110 01110010 01100101 01100101 00100000 01100001 01101110 01100100
					00100000 01100001 01110111 01100101 01110011 01101111 01101101 01100101 00100001
				</div>
			</div>
		</div>
	</section>
	<!-- Р вЂќР С•Р С—Р С•Р В»Р Р…Р С‘РЎвЂљР ВµР В»РЎРЉР Р…РЎвЂ№Р Вµ FAQ -->
	<section class="py-16 bg-gray-50">
		<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ open: null }">
			<div class="text-center mb-12 animate-fadeIn">
				<h2 class="text-3xl font-bold mb-4"><?= htmlspecialchars($tr('faq_title')) ?></h2>
				<p class="text-lg text-gray-600 max-w-2xl mx-auto"><?= htmlspecialchars($tr('faq_subtitle')) ?></p>
			</div>
			<div class="space-y-3">
				<?php for ($i = 1; $i <= 3; $i++): ?>
					<div
						class="accordion-item bg-white rounded-xl overflow-hidden border border-gray-100 transition-shadow hover:shadow-md">
						<button
							class="w-full flex items-center justify-between font-medium text-gray-800 p-5 text-left hover:bg-gray-50 transition-colors"
							@click="open = open === <?= $i ?> ? null : <?= $i ?>"
							:class="open === <?= $i ?> && 'text-brand-600 bg-brand-50'" aria-expanded="open === <?= $i ?>"
							aria-controls="faq-answer-<?= $i ?>">
							<span class="text-lg"><?= htmlspecialchars($tr('q' . $i)) ?></span>
							<i class="fas transition-transform duration-300 ml-3"
								:class="open === <?= $i ?> ? 'fa-chevron-up text-brand-600' : 'fa-chevron-down text-gray-400'"></i>
						</button>
						<div x-show="open === <?= $i ?>" x-collapse.duration.300ms id="faq-answer-<?= $i ?>"
							class="px-5 pb-5 pt-1 text-gray-600 leading-relaxed">
							<?= htmlspecialchars($tr('a' . $i)) ?>
						</div>
					</div>
				<?php endfor; ?>
			</div>
			<div class="mt-8 text-center">
				<a href="?action=support"
					class="inline-flex items-center text-brand-600 font-medium hover:text-brand-700 transition-colors">
					<i class="fas fa-envelope mr-2"></i><?= htmlspecialchars($tr('support_link')) ?>
				</a>
			</div>
		</div>
	</section>
	<?php
	$footerVariant = 'home';
	$footerTr = $tr;
	include 'includes/footer.php';
	?>
	<script>
		// Animate chart fills on scroll
		document.addEventListener('DOMContentLoaded', function () {
			// Animate radial charts
			setTimeout(() => {
				document.querySelectorAll('.radial-chart').forEach(el => {
					const percentage = parseFloat(el.style.getPropertyValue('--percentage'));
					el.style.background = `conic-gradient(#4f46e5 ${percentage}%, #e0e7ff ${percentage}%)`;
				});
			}, 300);
			// Animate bar charts
			const observerOptions = {
				threshold: 0.1
			};
			const observer = new IntersectionObserver((entries) => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						entry.target.style.opacity = 1;
						entry.target.style.transform = 'translateY(0)';
						if (entry.target.classList.contains('chart-fill')) {
							const width = entry.target.style.width;
							entry.target.style.width = '0';
							setTimeout(() => {
								entry.target.style.width = width;
							}, 100);
						}
						observer.unobserve(entry.target);
					}
				});
			}, observerOptions);
			// Observe all cards and charts
			document.querySelectorAll('.course-card, .vacancy-card, .review-card, .feature-card, .stat-card, .hero-stats, .chart-fill').forEach(element => {
				if (!element.classList.contains('chart-fill')) {
					element.style.opacity = 0;
					element.style.transform = 'translateY(20px)';
					element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
				}
				observer.observe(element);
			});
			// Floating nodes animation
			document.querySelectorAll('.node').forEach((node, index) => {
				node.style.animation = `nodeFloat 15s ease-in-out infinite ${index * 2}s`;
			});
			// Sparkles animation
			document.querySelectorAll('.sparkle').forEach((sparkle, index) => {
				sparkle.style.animationDelay = `${index * 0.8}s`;
			});

			// Telegram events are rendered on server side.
		});
	</script>
</body>

</html>


