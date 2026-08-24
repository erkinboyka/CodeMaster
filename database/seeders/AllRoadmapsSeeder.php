<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoadmapNode;
use App\Models\RoadmapLesson;
use App\Models\RoadmapQuizQuestion;

class AllRoadmapsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFrontend();
        $this->seedBackend();
        $this->seedFullstack();
        $this->seedDevOps();
        $this->seedPython();
        $this->seedUIUX();
        $this->seedMobile();
        $this->seedCpp();
    }

    private function buildNodes(string $roadmapTitle, array $data): void
    {
        $nodeOrder = 0;
        $idMap = [];
        foreach ($data as $d) {
            $nodeOrder++;
            $node = RoadmapNode::create([
                'title' => $d['t'],
                'topic' => $d['tp'],
                'course_id' => $d['c'] ?? null,
                'is_exam' => $d['is_exam'] ?? false,
                'roadmap_title' => $roadmapTitle,
                'x' => $d['x'],
                'y' => $d['y'],
                'materials' => $d['m'] ?? [],
                'deps' => null,
            ]);
            $idMap[$nodeOrder] = $node->id;
        }
        $nodeOrder = 0;
        foreach ($data as $d) {
            $nodeOrder++;
            if (!empty($d['d'])) {
                $deps = array_map(fn($dep) => $idMap[$dep] ?? $dep, $d['d']);
                RoadmapNode::where('id', $idMap[$nodeOrder])->update(['deps' => $deps]);
            }
        }
    }

    private function seedLessonsFor(string $roadmapTitle, array $lessons): void
    {
        foreach ($lessons as $title => $items) {
            $node = RoadmapNode::where('roadmap_title', $roadmapTitle)->where('title', $title)->first();
            if (!$node) continue;
            foreach ($items as $item) {
                RoadmapLesson::create(array_merge($item, ['node_id' => $node->id]));
            }
        }
    }

    private function seedQuizzesFor(string $roadmapTitle, array $quizData): void
    {
        foreach ($quizData as $title => $questions) {
            $node = RoadmapNode::where('roadmap_title', $roadmapTitle)->where('title', $title)->first();
            if (!$node) continue;
            foreach ($questions as $q) {
                $opts = $q['options'] ?? [];
                $correct = $q['correct_answer'] ?? $opts[0] ?? '';
                RoadmapQuizQuestion::create([
                    'node_id' => $node->id,
                    'question' => $q['question'],
                    'options' => json_encode($opts),
                    'correct_answer' => $correct,
                ]);
            }
        }
    }

    private function seedExamFor(string $roadmapTitle, array $examQuestions): void
    {
        $node = RoadmapNode::where('roadmap_title', $roadmapTitle)->where('is_exam', true)->first();
        if (!$node) return;
        foreach ($examQuestions as $question => $options) {
            $correct = array_search($options[0], $options);
            RoadmapQuizQuestion::create([
                'node_id' => $node->id,
                'question' => $question,
                'options' => json_encode($options),
                'correct_answer' => $correct,
            ]);
        }
    }
    private function seedFrontend(): void
    {
        $m = fn($l, $u) => ['label' => $l, 'url' => $u];

        RoadmapNode::where('roadmap_title', 'Frontend Developer')->delete();

        $this->buildNodes('Frontend Developer', [
            ['t' => 'Как работает интернет', 'tp' => 'Intro', 'x' => 60, 'y' => 350, 'd' => [], 'm' => [
                $m('MDN: Internet', 'https://developer.mozilla.org/ru/docs/Learn/Common_questions/How_does_the_Internet_work'),
            ]],
            ['t' => 'HTML Основы', 'tp' => 'Language', 'x' => 320, 'y' => 200, 'd' => [1], 'c' => 1],
            ['t' => 'CSS Основы', 'tp' => 'Style', 'x' => 320, 'y' => 350, 'd' => [1], 'c' => 1],
            ['t' => 'Терминал и CLI', 'tp' => 'Tooling', 'x' => 320, 'y' => 500, 'd' => [], 'c' => 12],
            ['t' => 'Структура документа', 'tp' => 'HTML', 'x' => 580, 'y' => 150, 'd' => [2], 'm' => [
                $m('MDN: HTML', 'https://developer.mozilla.org/ru/docs/Learn/HTML'),
            ]],
            ['t' => 'Текст, ссылки и списки', 'tp' => 'HTML', 'x' => 580, 'y' => 300, 'd' => [2], 'm' => [
                $m('MDN: Text fundamentals', 'https://developer.mozilla.org/ru/docs/Learn/HTML/Introduction_to_HTML/HTML_text_fundamentals'),
            ]],
            ['t' => 'Семантический HTML', 'tp' => 'HTML', 'x' => 580, 'y' => 450, 'd' => [5], 'm' => [
                $m('MDN: Semantics', 'https://developer.mozilla.org/ru/docs/Glossary/Semantics'),
            ]],
            ['t' => 'Формы и валидация', 'tp' => 'HTML', 'x' => 580, 'y' => 600, 'd' => [5], 'm' => [
                $m('MDN: Forms', 'https://developer.mozilla.org/ru/docs/Learn/Forms'),
            ]],
            ['t' => 'Таблицы и мета-теги', 'tp' => 'HTML', 'x' => 840, 'y' => 200, 'd' => [5], 'm' => [
                $m('MDN: Tables', 'https://developer.mozilla.org/ru/docs/Learn/HTML/Tables'),
            ]],
            ['t' => 'CSS Селекторы и каскад', 'tp' => 'CSS', 'x' => 840, 'y' => 350, 'd' => [3], 'm' => [
                $m('MDN: CSS Selectors', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_Selectors'),
            ]],
            ['t' => 'Box Model и sizing', 'tp' => 'CSS', 'x' => 840, 'y' => 500, 'd' => [3], 'm' => [
                $m('MDN: Box Model', 'https://developer.mozilla.org/ru/docs/Learn/CSS/Building_blocks/The_box_model'),
            ]],
            ['t' => 'Цвета, фоны и тень', 'tp' => 'CSS', 'x' => 840, 'y' => 650, 'd' => [3], 'm' => [
                $m('MDN: Backgrounds', 'https://developer.mozilla.org/ru/docs/Web/CSS/background'),
            ]],
            ['t' => 'Git Основы', 'tp' => 'Tooling', 'x' => 1100, 'y' => 150, 'd' => [], 'c' => 11],
            ['t' => 'npm и пакеты', 'tp' => 'Tooling', 'x' => 1100, 'y' => 300, 'd' => [13], 'm' => [
                $m('npm Docs', 'https://docs.npmjs.com/'),
            ]],
            ['t' => 'VS Code для фронтенда', 'tp' => 'Tooling', 'x' => 1100, 'y' => 450, 'd' => [13], 'm' => [
                $m('VS Code Docs', 'https://code.visualstudio.com/docs'),
            ]],
            ['t' => 'JavaScript Основы', 'tp' => 'Language', 'x' => 1360, 'y' => 150, 'd' => [13], 'c' => 2],
            ['t' => 'Переменные, типы и операторы', 'tp' => 'Language', 'x' => 1360, 'y' => 300, 'd' => [16], 'm' => [
                $m('JavaScript.info', 'https://javascript.info/'),
            ]],
            ['t' => 'Условия и циклы', 'tp' => 'Language', 'x' => 1360, 'y' => 450, 'd' => [16], 'm' => [
                $m('JS Conditionals', 'https://javascript.info/ifelse'),
            ]],
            ['t' => 'Функции и замыкания', 'tp' => 'Language', 'x' => 1360, 'y' => 600, 'd' => [16], 'm' => [
                $m('JS Functions', 'https://javascript.info/function-basics'),
            ]],
            ['t' => 'Объекты и массивы', 'tp' => 'Language', 'x' => 1620, 'y' => 150, 'd' => [17], 'm' => [
                $m('JS Objects', 'https://javascript.info/object'),
            ]],
            ['t' => 'Прототипы и классы', 'tp' => 'Language', 'x' => 1620, 'y' => 300, 'd' => [17], 'm' => [
                $m('JS Classes', 'https://javascript.info/class'),
            ]],
            ['t' => 'DOM API', 'tp' => 'Browser', 'x' => 1880, 'y' => 150, 'd' => [19], 'm' => [
                $m('MDN: DOM', 'https://developer.mozilla.org/ru/docs/Web/API/Document_Object_Model'),
            ]],
            ['t' => 'События и делегирование', 'tp' => 'Browser', 'x' => 1880, 'y' => 300, 'd' => [19], 'm' => [
                $m('MDN: Events', 'https://developer.mozilla.org/ru/docs/Web/Events'),
            ]],
            ['t' => 'Работа с формами через JS', 'tp' => 'Browser', 'x' => 1880, 'y' => 450, 'd' => [21], 'm' => [
                $m('MDN: Forms', 'https://developer.mozilla.org/ru/docs/Learn/Forms'),
            ]],
            ['t' => 'ES6+ Modern Features', 'tp' => 'Language', 'x' => 1880, 'y' => 600, 'd' => [17], 'm' => [
                $m('ES6 Features', 'https://es6-features.org/'),
            ]],
            ['t' => 'Асинхронность: Promises, async/await', 'tp' => 'Language', 'x' => 2140, 'y' => 150, 'd' => [18], 'm' => [
                $m('MDN: Async', 'https://developer.mozilla.org/ru/docs/Learn/JavaScript/Asynchronous'),
            ]],
            ['t' => 'Git: Ветки, Merge, Rebase', 'tp' => 'Tooling', 'x' => 2140, 'y' => 300, 'd' => [13], 'm' => [
                $m('Git Pro', 'https://git-scm.com/book/en/v2'),
            ]],
            ['t' => 'Fetch API и HTTP-запросы', 'tp' => 'API', 'x' => 2140, 'y' => 450, 'd' => [25], 'm' => [
                $m('MDN: Fetch', 'https://developer.mozilla.org/ru/docs/Web/API/Fetch_API'),
            ]],
            ['t' => 'JSON и работа с данными', 'tp' => 'Data', 'x' => 2140, 'y' => 600, 'd' => [25], 'm' => [
                $m('MDN: JSON', 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Global_Objects/JSON'),
            ]],
            ['t' => 'LocalStorage и Storage API', 'tp' => 'Browser', 'x' => 2400, 'y' => 150, 'd' => [25], 'm' => [
                $m('MDN: Web Storage', 'https://developer.mozilla.org/ru/docs/Web/API/Web_Storage_API'),
            ]],
            ['t' => 'Web Workers и Performance API', 'tp' => 'Browser', 'x' => 2400, 'y' => 300, 'd' => [25], 'm' => [
                $m('MDN: Web Workers', 'https://developer.mozilla.org/ru/docs/Web/API/Web_Workers_API'),
            ]],
            ['t' => 'ES6 Модули и импорты', 'tp' => 'Language', 'x' => 2400, 'y' => 450, 'd' => [25], 'm' => [
                $m('MDN: Modules', 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Guide/Modules'),
            ]],
            ['t' => 'Webpack / Vite / Build Tools', 'tp' => 'Tooling', 'x' => 2400, 'y' => 600, 'd' => [14], 'm' => [
                $m('Webpack Docs', 'https://webpack.js.org/'),
            ]],
            ['t' => 'TypeScript Основы', 'tp' => 'Language', 'x' => 2660, 'y' => 150, 'd' => [16], 'c' => 16],
            ['t' => 'TypeScript: Интерфейсы, Дженерики', 'tp' => 'Language', 'x' => 2660, 'y' => 300, 'd' => [33], 'm' => [
                $m('TS Handbook', 'https://www.typescriptlang.org/docs/handbook/'),
            ]],
            ['t' => 'REST API и HTTP-запросы', 'tp' => 'API', 'x' => 2660, 'y' => 450, 'd' => [27], 'm' => [
                $m('RESTful API', 'https://restfulapi.net/'),
            ]],
            ['t' => 'React: Компоненты и JSX', 'tp' => 'Framework', 'x' => 2660, 'y' => 600, 'd' => [16], 'c' => 14],
            ['t' => 'React Hooks: useState, useEffect', 'tp' => 'Framework', 'x' => 2920, 'y' => 150, 'd' => [36], 'm' => [
                $m('React Docs', 'https://react.dev/'),
            ]],
            ['t' => 'React: Обработка событий', 'tp' => 'Framework', 'x' => 2920, 'y' => 300, 'd' => [36], 'm' => [
                $m('React Events', 'https://react.dev/learn/responding-to-events'),
            ]],
            ['t' => 'React Router', 'tp' => 'Framework', 'x' => 2920, 'y' => 450, 'd' => [37], 'm' => [
                $m('React Router', 'https://reactrouter.com/'),
            ]],
            ['t' => 'State Management', 'tp' => 'Architecture', 'x' => 2920, 'y' => 600, 'd' => [37], 'm' => [
                $m('Redux Toolkit', 'https://redux-toolkit.js.org/'),
            ]],
            ['t' => 'Кастомные хуки', 'tp' => 'Framework', 'x' => 3180, 'y' => 150, 'd' => [37], 'm' => [
                $m('React Custom Hooks', 'https://react.dev/learn/reusing-logic-with-custom-hooks'),
            ]],
            ['t' => 'Vue.js: Основы', 'tp' => 'Framework', 'x' => 3180, 'y' => 300, 'd' => [16], 'm' => [
                $m('Vue Docs', 'https://vuejs.org/'),
            ]],
            ['t' => 'Vue Router и Pinia', 'tp' => 'Framework', 'x' => 3180, 'y' => 450, 'd' => [42], 'm' => [
                $m('Vue Router', 'https://router.vuejs.org/'),
            ]],
            ['t' => 'Unit-тестирование', 'tp' => 'Quality', 'x' => 3180, 'y' => 600, 'd' => [36], 'm' => [
                $m('Jest', 'https://jestjs.io/'),
            ]],
            ['t' => 'E2E тестирование', 'tp' => 'Quality', 'x' => 3440, 'y' => 150, 'd' => [44], 'm' => [
                $m('Cypress', 'https://www.cypress.io/'),
            ]],
            ['t' => 'Next.js / Nuxt: SSR и SSG', 'tp' => 'Framework', 'x' => 3440, 'y' => 300, 'd' => [36], 'm' => [
                $m('Next.js', 'https://nextjs.org/docs'),
            ]],
            ['t' => 'Производительность (CWV)', 'tp' => 'Production', 'x' => 3440, 'y' => 450, 'd' => [46], 'm' => [
                $m('Web.dev', 'https://web.dev/'),
            ]],
            ['t' => 'Web Security: XSS, CSRF', 'tp' => 'Security', 'x' => 3440, 'y' => 600, 'd' => [22], 'm' => [
                $m('OWASP', 'https://owasp.org/'),
            ]],
            ['t' => 'SEO для фронтенда', 'tp' => 'Production', 'x' => 3700, 'y' => 150, 'd' => [46], 'm' => [
                $m('Google SEO', 'https://developers.google.com/search'),
            ]],
            ['t' => 'PWA: Service Workers', 'tp' => 'Advanced', 'x' => 3700, 'y' => 300, 'd' => [30], 'm' => [
                $m('MDN: PWA', 'https://developer.mozilla.org/ru/docs/Web/Progressive_web_apps'),
            ]],
            ['t' => 'CI/CD и автоматизация', 'tp' => 'DevOps', 'x' => 3700, 'y' => 450, 'd' => [14], 'm' => [
                $m('GitHub Actions', 'https://docs.github.com/en/actions'),
            ]],
            ['t' => 'Деплой: Vercel / Netlify', 'tp' => 'Production', 'x' => 3700, 'y' => 600, 'd' => [50], 'm' => [
                $m('Vercel', 'https://vercel.com/docs'),
            ]],
            ['t' => 'Мониторинг ошибок (Sentry)', 'tp' => 'Production', 'x' => 3960, 'y' => 300, 'd' => [50], 'm' => [
                $m('Sentry', 'https://docs.sentry.io/'),
            ]],
            ['t' => 'Финальный экзамен', 'tp' => 'Exam', 'x' => 3960, 'y' => 500, 'd' => [47, 48, 49, 51, 52], 'is_exam' => true],
        ]);

        $this->seedQuizzesFor('Frontend Developer', $this->getFrontendQuizData());
        $this->seedExamFor('Frontend Developer', $this->getFrontendExamData());
    }
    private function seedBackend(): void
    {
        $m = fn($l, $u) => ['label' => $l, 'url' => $u];

        RoadmapNode::where('roadmap_title', 'Backend Developer')->delete();

        $this->buildNodes('Backend Developer', [
            ['t'=>'Основы серверной разработки','tp'=>'Intro','x'=>60,'y'=>350,'d'=>[],'m'=>[
                $m('MDN: Server-side','https://developer.mozilla.org/ru/docs/Learn/Server-side'),
                $m('How web servers work','https://developer.mozilla.org/ru/docs/Learn/Common_questions/What_is_a_web_server'),
            ]],
            ['t'=>'PHP','tp'=>'Language','x'=>320,'y'=>200,'d'=>[1],'c'=>3],
            ['t'=>'MySQL','tp'=>'Database','x'=>320,'y'=>350,'d'=>[1],'c'=>5],
            ['t'=>'HTTP / REST','tp'=>'Protocol','x'=>320,'y'=>500,'d'=>[],'m'=>[
                $m('MDN: HTTP','https://developer.mozilla.org/ru/docs/Web/HTTP'),
                $m('RESTful API Design','https://restfulapi.net/'),
                $m('HTTP status codes','https://httpstatuses.com/'),
            ]],
            ['t'=>'PHP OOP','tp'=>'Language','x'=>580,'y'=>150,'d'=>[2],'m'=>[
                $m('PHP: The Right Way','https://phptherightway.com/'),
                $m('PHP OOP','https://www.php.net/manual/en/language.oop5.php'),
                $m('SOLID Principles','https://scotch.io/bar-talk/sOLID-introduction'),
            ]],
            ['t'=>'SQL Advanced','tp'=>'Database','x'=>580,'y'=>300,'d'=>[3],'m'=>[
                $m('SQL Tutorial','https://www.w3schools.com/sql/'),
                $m('LearnSQL','https://learnsql.com/'),
            ]],
            ['t'=>'Composer / Packages','tp'=>'Tooling','x'=>580,'y'=>450,'d'=>[2],'m'=>[
                $m('Composer Docs','https://getcomposer.org/doc/'),
                $m('Packagist','https://packagist.org/'),
            ]],
            ['t'=>'Linux / Terminal','tp'=>'OS','x'=>580,'y'=>600,'d'=>[],'c'=>12],
            ['t'=>'Laravel','tp'=>'Framework','x'=>840,'y'=>150,'d'=>[4,6],'c'=>4],
            ['t'=>'Eloquent ORM','tp'=>'Framework','x'=>840,'y'=>280,'d'=>[5,8],'m'=>[
                $m('Laravel Eloquent','https://laravel.com/docs/eloquent'),
                $m('Eloquent Relationships','https://laravel.com/docs/eloquent-relationships'),
            ]],
            ['t'=>'Authentication','tp'=>'Security','x'=>840,'y'=>410,'d'=>[8],'m'=>[
                $m('Laravel Sanctum','https://laravel.com/docs/sanctum'),
                $m('Laravel Breeze','https://laravel.com/docs/starter-kits'),
            ]],
            ['t'=>'Migrations & Seeds','tp'=>'Framework','x'=>840,'y'=>540,'d'=>[5,8],'m'=>[
                $m('Laravel Migrations','https://laravel.com/docs/migrations'),
            ]],
            ['t'=>'Базы данных (Advanced)','tp'=>'Database','x'=>1100,'y'=>150,'d'=>[9],'m'=>[
                $m('MySQL Indexing','https://use-the-index-luke.com/'),
                $m('Query Optimization','https://www.mysqltutorial.org/'),
            ]],
            ['t'=>'REST API (Laravel)','tp'=>'API','x'=>1100,'y'=>280,'d'=>[9,10],'m'=>[
                $m('Laravel API Resources','https://laravel.com/docs/eloquent-resources'),
                $m('JSON:API Spec','https://jsonapi.org/'),
            ]],
            ['t'=>'Queue & Jobs','tp'=>'Architecture','x'=>1100,'y'=>410,'d'=>[10],'m'=>[
                $m('Laravel Queues','https://laravel.com/docs/queues'),
                $m('Redis','https://redis.io/docs/'),
            ]],
            ['t'=>'Testing (PHPUnit)','tp'=>'Quality','x'=>1100,'y'=>540,'d'=>[10],'m'=>[
                $m('PHPUnit Manual','https://phpunit.de/'),
                $m('Laravel Testing','https://laravel.com/docs/testing'),
            ]],
            ['t'=>'Redis / Cache','tp'=>'Architecture','x'=>1360,'y'=>150,'d'=>[12],'m'=>[
                $m('Redis University','https://university.redis.com/'),
                $m('Laravel Cache','https://laravel.com/docs/cache'),
            ]],
            ['t'=>'WebSockets','tp'=>'Protocol','x'=>1360,'y'=>280,'d'=>[12],'m'=>[
                $m('Laravel WebSockets','https://beyondco.de/docs/laravel-websockets/'),
                $m('Socket.io','https://socket.io/'),
            ]],
            ['t'=>'Docker для PHP','tp'=>'DevOps','x'=>1360,'y'=>410,'d'=>[13],'c'=>17],
            ['t'=>'CI/CD','tp'=>'DevOps','x'=>1360,'y'=>540,'d'=>[14],'m'=>[
                $m('GitHub Actions','https://docs.github.com/en/actions'),
                $m('GitLab CI','https://docs.gitlab.com/ee/ci/'),
            ]],
            ['t'=>'Performance','tp'=>'Production','x'=>1620,'y'=>200,'d'=>[16],'m'=>[
                $m('Laravel Performance','https://laravel.com/docs/deployment#optimization'),
                $m('New Relic','https://newrelic.com/'),
            ]],
            ['t'=>'Security','tp'=>'Production','x'=>1620,'y'=>350,'d'=>[16],'m'=>[
                $m('OWASP Top 10','https://owasp.org/Top10/'),
                $m('Laravel Security','https://laravel.com/docs/security'),
            ]],
            ['t'=>'Microservices','tp'=>'Architecture','x'=>1620,'y'=>500,'d'=>[17],'is_exam'=>true,'m'=>[
                $m('Microservices.io','https://microservices.io/'),
                $m('Laravel Horizon','https://laravel.com/docs/horizon'),
            ]],
        
        ]);

        $this->seedQuizzesFor('Backend Developer', $this->getBackendQuizData());
        $this->seedExamFor('Backend Developer', $this->getBackendExamData());
    }
    private function seedFullstack(): void
    {
        $m = fn($l, $u) => ['label' => $l, 'url' => $u];

        RoadmapNode::where('roadmap_title', 'Fullstack Developer')->delete();

        $this->buildNodes('Fullstack Developer', [
            ['t'=>'HTML / CSS','tp'=>'Frontend','x'=>60,'y'=>350,'d'=>[],'c'=>1],
            ['t'=>'JavaScript','tp'=>'Language','x'=>60,'y'=>500,'d'=>[],'c'=>2],
            ['t'=>'Responsive Design','tp'=>'CSS','x'=>320,'y'=>200,'d'=>[1],'m'=>[
                $m('MDN: Responsive','https://developer.mozilla.org/ru/docs/Learn/CSS/CSS_layout/Responsive_Design'),
                $m('Flexbox Froggy','https://flexboxfroggy.com/#ru'),
            ]],
            ['t'=>'JavaScript OOP','tp'=>'Language','x'=>320,'y'=>350,'d'=>[2],'m'=>[
                $m('JavaScript.info','https://javascript.info/'),
                $m('ES6 Features','https://es6-features.org/'),
            ]],
            ['t'=>'JS Async / Promises','tp'=>'Language','x'=>320,'y'=>500,'d'=>[2],'m'=>[
                $m('MDN: Async/Await','https://developer.mozilla.org/ru/docs/Learn/JavaScript/Asynchronous'),
            ]],
            ['t'=>'React / Vue','tp'=>'Framework','x'=>580,'y'=>200,'d'=>[3,4],'c'=>14],
            ['t'=>'PHP','tp'=>'Language','x'=>580,'y'=>380,'d'=>[4],'c'=>3],
            ['t'=>'Node.js','tp'=>'Runtime','x'=>580,'y'=>530,'d'=>[5],'c'=>15],
            ['t'=>'TypeScript','tp'=>'Language','x'=>840,'y'=>120,'d'=>[6],'c'=>16],
            ['t'=>'Laravel','tp'=>'Framework','x'=>840,'y'=>300,'d'=>[7],'c'=>4],
            ['t'=>'REST API','tp'=>'API','x'=>840,'y'=>450,'d'=>[7,8],'m'=>[
                $m('RESTful Design','https://restfulapi.net/'),
                $m('JSON:API','https://jsonapi.org/'),
            ]],
            ['t'=>'MySQL','tp'=>'Database','x'=>840,'y'=>600,'d'=>[7],'c'=>5],
            ['t'=>'State Management','tp'=>'Ecosystem','x'=>1100,'y'=>120,'d'=>[9],'m'=>[
                $m('Redux Toolkit','https://redux-toolkit.js.org/'),
                $m('Zustand','https://github.com/pmndrs/zustand'),
            ]],
            ['t'=>'Eloquent ORM','tp'=>'Backend','x'=>1100,'y'=>300,'d'=>[10],'m'=>[
                $m('Laravel Eloquent','https://laravel.com/docs/eloquent'),
            ]],
            ['t'=>'Auth & JWT','tp'=>'Security','x'=>1100,'y'=>450,'d'=>[11],'m'=>[
                $m('Laravel Sanctum','https://laravel.com/docs/sanctum'),
            ]],
            ['t'=>'Git','tp'=>'Tooling','x'=>1100,'y'=>600,'d'=>[],'c'=>11],
            ['t'=>'Testing','tp'=>'Quality','x'=>1360,'y'=>200,'d'=>[13,14],'m'=>[
                $m('Jest','https://jestjs.io/'),
                $m('PHPUnit','https://phpunit.de/'),
                $m('Cypress','https://www.cypress.io/'),
            ]],
            ['t'=>'Docker','tp'=>'DevOps','x'=>1360,'y'=>400,'d'=>[16],'c'=>17],
            ['t'=>'CI/CD','tp'=>'DevOps','x'=>1360,'y'=>550,'d'=>[16,17],'m'=>[
                $m('GitHub Actions','https://docs.github.com/en/actions'),
            ]],
            ['t'=>'Deploy','tp'=>'Production','x'=>1620,'y'=>300,'d'=>[17,18],'m'=>[
                $m('Vercel','https://vercel.com/docs'),
                $m('DigitalOcean','https://www.digitalocean.com/docs'),
            ]],
            ['t'=>'Performance & SEO','tp'=>'Production','x'=>1620,'y'=>480,'d'=>[19],'is_exam'=>true,'m'=>[
                $m('Web.dev','https://web.dev/'),
                $m('Core Web Vitals','https://web.dev/vitals/'),
            ]],
        
        ]);

        $this->seedQuizzesFor('Fullstack Developer', $this->getFullstackQuizData());
        $this->seedExamFor('Fullstack Developer', $this->getFullstackExamData());
    }
    private function seedDevOps(): void
    {
        $m = fn($l, $u) => ['label' => $l, 'url' => $u];

        RoadmapNode::where('roadmap_title', 'DevOps Engineer')->delete();

        $this->buildNodes('DevOps Engineer', [
            ['t'=>'Linux Fundamentals','tp'=>'OS','x'=>60,'y'=>350,'d'=>[],'c'=>12],
            ['t'=>'Git','tp'=>'VCS','x'=>60,'y'=>500,'d'=>[],'c'=>11],
            ['t'=>'Bash Scripting','tp'=>'Scripting','x'=>320,'y'=>200,'d'=>[1],'m'=>[
                $m('Bash Tutorial','https://ryanstutorials.net/bash-scripting-tutorial/'),
                $m('Advanced Bash','https://tldp.org/LDP/abs/html/'),
            ]],
            ['t'=>'Networking','tp'=>'Infrastructure','x'=>320,'y'=>380,'d'=>[1],'m'=>[
                $m('Computer Networking','https://www.youtube.com/watch?v=IPvYjXsTlsY'),
                $m('OSI Model','https://networklessons.com/osi-model'),
            ]],
            ['t'=>'Git Advanced','tp'=>'VCS','x'=>320,'y'=>530,'d'=>[2],'m'=>[
                $m('Git Pro Book','https://git-scm.com/book/en/v2'),
                $m('Atlassian Git Tutorials','https://www.atlassian.com/git/tutorials'),
            ]],
            ['t'=>'Docker','tp'=>'Containers','x'=>580,'y'=>200,'d'=>[3],'c'=>17],
            ['t'=>'Docker Compose','tp'=>'Containers','x'=>580,'y'=>350,'d'=>[6],'m'=>[
                $m('Docker Compose','https://docs.docker.com/compose/'),
            ]],
            ['t'=>'CI/CD Pipelines','tp'=>'Automation','x'=>580,'y'=>500,'d'=>[5],'m'=>[
                $m('GitHub Actions','https://docs.github.com/en/actions'),
                $m('Jenkins','https://www.jenkins.io/'),
                $m('GitLab CI','https://docs.gitlab.com/ee/ci/'),
            ]],
            ['t'=>'Kubernetes','tp'=>'Orchestration','x'=>840,'y'=>200,'d'=>[6,7],'c'=>18],
            ['t'=>'Terraform','tp'=>'IaC','x'=>840,'y'=>380,'d'=>[4,6],'m'=>[
                $m('Terraform Learn','https://developer.hashicorp.com/terraform/tutorials'),
                $m('Terraform Registry','https://registry.terraform.io/'),
            ]],
            ['t'=>'Ansible','tp'=>'IaC','x'=>840,'y'=>530,'d'=>[4],'m'=>[
                $m('Ansible Docs','https://docs.ansible.com/'),
                $m('Ansible Galaxy','https://galaxy.ansible.com/'),
            ]],
            ['t'=>'Monitoring','tp'=>'Observability','x'=>1100,'y'=>150,'d'=>[9],'m'=>[
                $m('Prometheus','https://prometheus.io/docs/'),
                $m('Grafana','https://grafana.com/docs/'),
                $m('ELK Stack','https://www.elastic.co/what-is/elk-stack'),
            ]],
            ['t'=>'Logging (ELK)','tp'=>'Observability','x'=>1100,'y'=>300,'d'=>[9],'m'=>[
                $m('Elasticsearch','https://www.elastic.co/guide/en/elasticsearch/reference/current/'),
                $m('Logstash','https://www.elastic.co/guide/en/logstash/current/'),
            ]],
            ['t'=>'Cloud (AWS/GCP)','tp'=>'Cloud','x'=>1100,'y'=>450,'d'=>[10],'m'=>[
                $m('AWS Free Tier','https://aws.amazon.com/free/'),
                $m('GCP Cloud','https://cloud.google.com/docs'),
            ]],
            ['t'=>'Secrets Management','tp'=>'Security','x'=>1100,'y'=>600,'d'=>[10],'m'=>[
                $m('HashiCorp Vault','https://developer.hashicorp.com/vault/docs'),
            ]],
            ['t'=>'Service Mesh','tp'=>'Architecture','x'=>1360,'y'=>200,'d'=>[11,12],'m'=>[
                $m('Istio','https://istio.io/latest/docs/'),
                $m('Linkerd','https://linkerd.io/2/'),
            ]],
            ['t'=>'GitOps','tp'=>'Workflow','x'=>1360,'y'=>380,'d'=>[12,13],'m'=>[
                $m('ArgoCD','https://argo-cd.readthedocs.io/'),
                $m('Flux','https://fluxcd.io/'),
            ]],
            ['t'=>'SRE Practices','tp'=>'Culture','x'=>1360,'y'=>530,'d'=>[14],'m'=>[
                $m('Google SRE Book','https://sre.google/sre-book/table-of-contents/'),
                $m('SLO/SLA/SLI','https://sre.google/workbook/implementing-slos/'),
            ]],
            ['t'=>'Security Hardening','tp'=>'Security','x'=>1620,'y'=>300,'d'=>[15,16],'m'=>[
                $m('CIS Benchmarks','https://www.cisecurity.org/cis-benchmarks'),
            ]],
            ['t'=>'Chaos Engineering','tp'=>'Reliability','x'=>1620,'y'=>480,'d'=>[16],'is_exam'=>true,'m'=>[
                $m('Chaos Monkey','https://netflix.github.io/chaosmonkey/'),
                $m('Gremlin','https://www.gremlin.com/'),
            ]],
        
        ]);

        $this->seedQuizzesFor('DevOps Engineer', $this->getDevOpsQuizData());
        $this->seedExamFor('DevOps Engineer', $this->getDevOpsExamData());
    }
    private function seedPython(): void
    {
        $m = fn($l, $u) => ['label' => $l, 'url' => $u];

        RoadmapNode::where('roadmap_title', 'Python Developer')->delete();

        $this->buildNodes('Python Developer', [
            ['t'=>'Python Основы','tp'=>'Language','x'=>60,'y'=>350,'d'=>[],'c'=>8],
            ['t'=>'Python OOP','tp'=>'Language','x'=>320,'y'=>250,'d'=>[1],'m'=>[
                $m('Python OOP','https://docs.python.org/3/tutorial/classes.html'),
                $m('Real Python OOP','https://realpython.com/python3-object-oriented-programming/'),
            ]],
            ['t'=>'Standard Library','tp'=>'Language','x'=>320,'y'=>400,'d'=>[1],'m'=>[
                $m('Python Docs','https://docs.python.org/3/library/'),
                $m('Python Module of the Week','https://pymotw.com/3/'),
            ]],
            ['t'=>'Virtual Environments','tp'=>'Tooling','x'=>320,'y'=>550,'d'=>[1],'m'=>[
                $m('venv docs','https://docs.python.org/3/library/venv.html'),
                $m('Poetry','https://python-poetry.org/'),
            ]],
            ['t'=>'Django','tp'=>'Framework','x'=>580,'y'=>200,'d'=>[2],'m'=>[
                $m('Django Tutorial','https://docs.djangoproject.com/en/stable/intro/tutorial01/'),
                $m('Django Girls','https://tutorial.djangogirls.org/'),
            ]],
            ['t'=>'Flask','tp'=>'Framework','x'=>580,'y'=>380,'d'=>[2],'m'=>[
                $m('Flask Tutorial','https://flask.palletsprojects.com/en/3.0.x/tutorial/'),
                $m('Miguel Grinberg Flask','https://blog.miguelgrinberg.com/post/the-flask-mega-tutorial-part-i-hello-world'),
            ]],
            ['t'=>'SQLAlchemy','tp'=>'ORM','x'=>580,'y'=>530,'d'=>[2],'m'=>[
                $m('SQLAlchemy Docs','https://docs.sqlalchemy.org/en/20/'),
            ]],
            ['t'=>'REST API (FastAPI)','tp'=>'API','x'=>840,'y'=>200,'d'=>[4,5],'m'=>[
                $m('FastAPI Tutorial','https://fastapi.tiangolo.com/tutorial/'),
                $m('FastAPI vs Django','https://fastapi.tiangolo.com/#background'),
            ]],
            ['t'=>'Django REST Framework','tp'=>'API','x'=>840,'y'=>350,'d'=>[4],'m'=>[
                $m('DRF Tutorial','https://www.django-rest-framework.org/tutorial/quickstart/'),
            ]],
            ['t'=>'Testing (pytest)','tp'=>'Quality','x'=>840,'y'=>500,'d'=>[3],'m'=>[
                $m('pytest Docs','https://docs.pytest.org/'),
                $m('Real Python Testing','https://realpython.com/pytest-python-testing/'),
            ]],
            ['t'=>'Data Science','tp'=>'Data','x'=>1100,'y'=>150,'d'=>[7],'m'=>[
                $m('NumPy','https://numpy.org/doc/'),
                $m('Pandas','https://pandas.pydata.org/docs/'),
                $m('Matplotlib','https://matplotlib.org/'),
            ]],
            ['t'=>'Machine Learning','tp'=>'AI','x'=>1100,'y'=>300,'d'=>[10],'m'=>[
                $m('Scikit-learn','https://scikit-learn.org/stable/'),
                $m('Kaggle Learn','https://www.kaggle.com/learn'),
            ]],
            ['t'=>'Celery / Async','tp'=>'Architecture','x'=>1100,'y'=>450,'d'=>[8],'m'=>[
                $m('Celery Docs','https://docs.celeryq.dev/'),
            ]],
            ['t'=>'Docker для Python','tp'=>'DevOps','x'=>1100,'y'=>600,'d'=>[9],'c'=>17],
            ['t'=>'ML Frameworks','tp'=>'AI','x'=>1360,'y'=>200,'d'=>[11],'m'=>[
                $m('PyTorch','https://pytorch.org/docs/stable/'),
                $m('TensorFlow','https://www.tensorflow.org/guide'),
            ]],
            ['t'=>'Data Pipelines','tp'=>'Data','x'=>1360,'y'=>380,'d'=>[10,12],'m'=>[
                $m('Apache Airflow','https://airflow.apache.org/docs/'),
                $m('ETL Best Practices','https://www.talend.com/resources/what-is-etl/'),
            ]],
            ['t'=>'Deploy','tp'=>'Production','x'=>1360,'y'=>530,'d'=>[13],'m'=>[
                $m('Gunicorn','https://docs.gunicorn.org/'),
                $m('Nginx + Django','https://docs.djangoproject.com/en/stable/howto/deployment/wsgi/nginx/'),
            ]],
            ['t'=>'Performance','tp'=>'Production','x'=>1620,'y'=>300,'d'=>[14,15],'is_exam'=>true,'m'=>[
                $m('Python Performance','https://realpython.com/python-performance/'),
                $m('Profiling','https://docs.python.org/3/library/profile.html'),
            ]],
        
        ]);

        $this->seedQuizzesFor('Python Developer', $this->getPythonQuizData());
        $this->seedExamFor('Python Developer', $this->getPythonExamData());
    }
    private function seedUIUX(): void
    {
        $m = fn($l, $u) => ['label' => $l, 'url' => $u];

        RoadmapNode::where('roadmap_title', 'UI/UX Designer')->delete();

        $this->buildNodes('UI/UX Designer', [
            ['t'=>'Design Fundamentals','tp'=>'Theory','x'=>60,'y'=>350,'d'=>[],'c'=>13],
            ['t'=>'Figma','tp'=>'Tool','x'=>320,'y'=>200,'d'=>[1],'m'=>[
                $m('Figma Tutorial','https://help.figma.com/hc/en-us/articles/360040318013'),
                $m('Figma YouTube','https://www.youtube.com/results?search_query=figma+tutorial'),
            ]],
            ['t'=>'Color Theory','tp'=>'Theory','x'=>320,'y'=>350,'d'=>[1],'m'=>[
                $m('Color Theory','https://www.canva.com/colors/color-wheel/'),
                $m('Coolors','https://coolors.co/'),
            ]],
            ['t'=>'Typography','tp'=>'Theory','x'=>320,'y'=>500,'d'=>[1],'m'=>[
                $m('Google Fonts','https://fonts.google.com/'),
                $m('Typewolf','https://www.typewolf.com/'),
            ]],
            ['t'=>'Components & Design Systems','tp'=>'Practice','x'=>580,'y'=>150,'d'=>[2],'m'=>[
                $m('Material Design','https://m3.material.io/'),
                $m('Ant Design','https://ant.design/'),
            ]],
            ['t'=>'User Research','tp'=>'UX','x'=>580,'y'=>300,'d'=>[1],'m'=>[
                $m('UX Research','https://www.nngroup.com/articles/which-ux-research-methods/'),
                $m('SurveyMonkey','https://www.surveymonkey.com/'),
            ]],
            ['t'=>'Wireframing','tp'=>'Practice','x'=>580,'y'=>450,'d'=>[2],'m'=>[
                $m('Balsamiq','https://balsamiq.com/wireframes/'),
                $m('Wireframe Examples','https://www.figma.com/community/tag/wireframe'),
            ]],
            ['t'=>'Prototyping','tp'=>'Practice','x'=>840,'y'=>150,'d'=>[4,5],'m'=>[
                $m('Figma Prototyping','https://help.figma.com/hc/en-us/articles/360039822274'),
                $m('InVision','https://www.invisionapp.com/'),
            ]],
            ['t'=>'User Testing','tp'=>'UX','x'=>840,'y'=>300,'d'=>[6],'m'=>[
                $m('Usability Testing','https://www.nngroup.com/articles/usability-testing-101/'),
                $m('UserTesting.com','https://www.usertesting.com/'),
            ]],
            ['t'=>'Accessibility','tp'=>'A11y','x'=>840,'y'=>450,'d'=>[4],'m'=>[
                $m('WCAG','https://www.w3.org/WAI/standards-guidelines/wcag/'),
                $m('A11y Project','https://www.a11yproject.com/'),
            ]],
            ['t'=>'Motion Design','tp'=>'Advanced','x'=>1100,'y'=>200,'d'=>[7],'m'=>[
                $m('Lottie','https://airbnb.io/lottie/'),
                $m('Principle','https://principleformac.com/'),
            ]],
            ['t'=>'Design Tokens','tp'=>'Systems','x'=>1100,'y'=>350,'d'=>[5,8],'m'=>[
                $m('Design Tokens','https://design-tokens.github.io/community-group/format/'),
            ]],
            ['t'=>'Handoff для разработчиков','tp'=>'Workflow','x'=>1100,'y'=>500,'d'=>[7,9],'m'=>[
                $m('Figma Dev Mode','https://www.figma.com/blog/figma-dev-mode/'),
            ]],
            ['t'=>'Design Systems','tp'=>'Advanced','x'=>1360,'y'=>250,'d'=>[10,11],'m'=>[
                $m('Atomic Design','https://atomicdesign.bradfrost.com/'),
                $m('Storybook','https://storybook.js.org/'),
            ]],
            ['t'=>'Portfolio','tp'=>'Career','x'=>1360,'y'=>430,'d'=>[12,13],'is_exam'=>true,'m'=>[
                $m('Behance','https://www.behance.net/'),
                $m('Dribbble','https://dribbble.com/'),
            ]],
        
        ]);

        $this->seedQuizzesFor('UI/UX Designer', $this->getUIUXQuizData());
        $this->seedExamFor('UI/UX Designer', $this->getUIUXExamData());
    }
    private function seedMobile(): void
    {
        $m = fn($l, $u) => ['label' => $l, 'url' => $u];

        RoadmapNode::where('roadmap_title', 'Mobile Developer')->delete();

        $this->buildNodes('Mobile Developer', [
            ['t'=>'JavaScript','tp'=>'Language','x'=>60,'y'=>350,'d'=>[],'c'=>2],
            ['t'=>'React','tp'=>'Framework','x'=>60,'y'=>500,'d'=>[],'c'=>14],
            ['t'=>'React Native','tp'=>'Mobile','x'=>320,'y'=>250,'d'=>[1,2],'c'=>19],
            ['t'=>'Flutter / Dart','tp'=>'Mobile','x'=>320,'y'=>430,'d'=>[],'m'=>[
                $m('Flutter Docs','https://flutter.dev/docs'),
                $m('Dart Tour','https://dart.dev/language'),
                $m('Flutter YouTube','https://www.youtube.com/@flutterdev'),
            ]],
            ['t'=>'Components & Navigation','tp'=>'Mobile','x'=>580,'y'=>150,'d'=>[3],'m'=>[
                $m('RN Navigation','https://reactnavigation.org/'),
                $m('RN Components','https://reactnative.dev/docs/components-and-apis'),
            ]],
            ['t'=>'State Management','tp'=>'Architecture','x'=>580,'y'=>300,'d'=>[3],'m'=>[
                $m('Redux Toolkit','https://redux-toolkit.js.org/'),
                $m('Riverpod (Flutter)','https://riverpod.dev/'),
                $m('Provider (Flutter)','https://pub.dev/packages/provider'),
            ]],
            ['t'=>'Native APIs','tp'=>'Platform','x'=>580,'y'=>450,'d'=>[3,4],'m'=>[
                $m('React Native Bridge','https://reactnative.dev/docs/native-modules-intro'),
                $m('Platform Channels','https://docs.flutter.dev/platform-integration/platform-channels'),
            ]],
            ['t'=>'Firebase','tp'=>'Backend','x'=>840,'y'=>200,'d'=>[5,6],'m'=>[
                $m('Firebase Docs','https://firebase.google.com/docs'),
                $m('Firebase Codelab','https://firebase.google.com/codelabs'),
            ]],
            ['t'=>'REST API / GraphQL','tp'=>'API','x'=>840,'y'=>380,'d'=>[6],'m'=>[
                $m('REST API','https://restfulapi.net/'),
                $m('Apollo GraphQL','https://www.apollographql.com/docs/react/'),
            ]],
            ['t'=>'Offline Storage','tp'=>'Data','x'=>840,'y'=>530,'d'=>[6],'m'=>[
                $m('SQLite','https://www.sqlite.org/'),
                $m('AsyncStorage','https://react-native-community.github.io/async-storage/'),
                $m('Hive (Flutter)','https://docs.hivedb.dev/'),
            ]],
            ['t'=>'Testing','tp'=>'Quality','x'=>1100,'y'=>200,'d'=>[7,8],'m'=>[
                $m('Jest','https://jestjs.io/'),
                $m('Detox','https://wix.github.io/Detox/'),
                $m('Flutter Tests','https://docs.flutter.dev/testing'),
            ]],
            ['t'=>'Push Notifications','tp'=>'Platform','x'=>1100,'y'=>380,'d'=>[7],'m'=>[
                $m('Firebase Messaging','https://firebase.google.com/docs/cloud-messaging'),
                $m('OneSignal','https://onesignal.com/'),
            ]],
            ['t'=>'App Store Deploy','tp'=>'Publishing','x'=>1100,'y'=>530,'d'=>[10],'m'=>[
                $m('App Store Guide','https://developer.apple.com/app-store/review/guidelines/'),
                $m('Google Play Console','https://support.google.com/googleplay/android-developer/answer/9859152'),
            ]],
            ['t'=>'Performance','tp'=>'Production','x'=>1360,'y'=>300,'d'=>[10,11],'m'=>[
                $m('React Native Perf','https://reactnative.dev/docs/performance'),
                $m('Flutter Perf','https://docs.flutter.dev/perf'),
            ]],
            ['t'=>'CI/CD (Fastlane)','tp'=>'DevOps','x'=>1360,'y'=>480,'d'=>[12],'is_exam'=>true,'m'=>[
                $m('Fastlane','https://docs.fastlane.tools/'),
                $m('EAS Build','https://docs.expo.dev/build/introduction/'),
            ]],
        
        ]);

        $this->seedQuizzesFor('Mobile Developer', $this->getMobileQuizData());
        $this->seedExamFor('Mobile Developer', $this->getMobileExamData());
    }
    private function seedCpp(): void
    {
        $m = fn($l, $u) => ['label' => $l, 'url' => $u];

        RoadmapNode::where('roadmap_title', 'C++ Developer')->delete();

        $this->buildNodes('C++ Developer', [
            ['t'=>'C++ Basics','tp'=>'Language','x'=>60,'y'=>350,'d'=>[],'c'=>7],
            ['t'=>'C++ OOP','tp'=>'Language','x'=>320,'y'=>250,'d'=>[1],'m'=>[
                $m('C++ Classes','https://cplusplus.com/doc/tutorial/classes/'),
                $m('CPP OOP','https://www.learncpp.com/cpp-tutorial/classes-and-object-oriented-programming/'),
            ]],
            ['t'=>'Memory Management','tp'=>'Language','x'=>320,'y'=>400,'d'=>[1],'m'=>[
                $m('Smart Pointers','https://www.learncpp.com/cpp-tutorial/smart-pointers/'),
                $m('Memory Model','https://en.cppreference.com/w/cpp/language/memory_model'),
            ]],
            ['t'=>'C++ Templates','tp'=>'Advanced','x'=>320,'y'=>550,'d'=>[1],'m'=>[
                $m('Templates','https://www.learncpp.com/cpp-tutorial/function-templates/'),
                $m('Template Metaprogramming','https://en.wikipedia.org/wiki/Template_metaprogramming'),
            ]],
            ['t'=>'STL','tp'=>'Library','x'=>580,'y'=>150,'d'=>[2],'m'=>[
                $m('STL Containers','https://cplusplus.com/reference/stl/'),
                $m('CppReference','https://en.cppreference.com/'),
            ]],
            ['t'=>'Data Structures','tp'=>'Algorithms','x'=>580,'y'=>300,'d'=>[2],'m'=>[
                $m('Visualgo','https://visualgo.net/'),
                $m('Data Structures','https://www.learncpp.com/cpp-tutorial/compound-data-types/'),
            ]],
            ['t'=>'Algorithms','tp'=>'Algorithms','x'=>580,'y'=>450,'d'=>[2],'m'=>[
                $m('Algorithm Visualizer','https://algorithm-visualizer.org/'),
                $m('CP-Algorithms','https://cp-algorithms.com/'),
            ]],
            ['t'=>'Design Patterns','tp'=>'Architecture','x'=>840,'y'=>150,'d'=>[4],'m'=>[
                $m('GoF Patterns','https://www.oodesign.com/'),
                $m('Refactoring Guru','https://refactoring.guru/design-patterns/cpp'),
            ]],
            ['t'=>'Multithreading','tp'=>'Advanced','x'=>840,'y'=>300,'d'=>[3,5],'m'=>[
                $m('C++ Concurrency','https://www.learncpp.com/cpp-tutorial/introduction-to-multithreading/'),
                $m('std::thread','https://en.cppreference.com/w/cpp/thread/thread'),
            ]],
            ['t'=>'Build Systems','tp'=>'Tooling','x'=>840,'y'=>450,'d'=>[],'m'=>[
                $m('CMake Tutorial','https://cliutils.gitlab.io/modern-cmake/'),
                $m('CMake Docs','https://cmake.org/cmake/help/latest/'),
            ]],
            ['t'=>'STL Algorithms','tp'=>'Library','x'=>1100,'y'=>150,'d'=>[4,5],'m'=>[
                $m('STL Algorithms','https://en.cppreference.com/w/cpp/algorithm'),
            ]],
            ['t'=>'Modern C++ (17/20)','tp'=>'Language','x'=>1100,'y'=>300,'d'=>[7],'m'=>[
                $m('C++17 Features','https://www.learncpp.com/cpp-tutorial/cpp17-specific-features/'),
                $m('C++20 Features','https://en.cppreference.com/w/cpp/20'),
            ]],
            ['t'=>'Game Engines','tp'=>'Domain','x'=>1100,'y'=>450,'d'=>[6,8],'m'=>[
                $m('Unreal Engine','https://docs.unrealengine.com/'),
                $m('SFML','https://www.sfml-dev.org/tutorials/'),
            ]],
            ['t'=>'Competitive Programming','tp'=>'Practice','x'=>1360,'y'=>200,'d'=>[9,10],'m'=>[
                $m('Codeforces','https://codeforces.com/'),
                $m('LeetCode','https://leetcode.com/'),
                $m('AtCoder','https://atcoder.jp/'),
            ]],
            ['t'=>'Open Source Projects','tp'=>'Career','x'=>1360,'y'=>400,'d'=>[10,11],'is_exam'=>true,'m'=>[
                $m('GitHub C++','https://github.com/topics/cpp'),
                $m('Awesome C++','https://github.com/rigtorp/awesome-modern-cpp'),
            ]],
        
        ]);

        $this->seedQuizzesFor('C++ Developer', $this->getCppQuizData());
        $this->seedExamFor('C++ Developer', $this->getCppExamData());
    }

    private function getFrontendQuizData(): array
    {
        return [
        'Как работает интернет' => [
            ['question' => 'Что такое IP-адрес?','options' => ['Уникальный идентификатор устройства в сети','Имя домена','Порт подключения','Тип браузера']],
            ['question' => 'Какой протокол используется для передачи веб-страниц?','options' => ['HTTP/HTTPS','FTP','SMTP','SSH']],
            ['question' => 'Что делает DNS?','options' => ['Преобразует домены в IP-адреса','Шифрует данные','Управляет файлами','Кэширует изображения']],
            ['question' => 'Какой порт по умолчанию использует HTTPS?','options' => ['443','80','8080','21']],
            ['question' => 'Что такое TCP/IP?','options' => ['Набор протоколов для передачи данных в сети','Язык программирования','Тип файла','Операционная система']],
        ],
        'HTML Основы' => [
            ['question' => 'Какой тег используется для создания заголовка первого уровня?','options' => ['<h1>','<head>','<header>','<p>']],
            ['question' => 'Какой тег создаёт абзац текста?','options' => ['<p>','<div>','<span>','<br>']],
            ['question' => 'Что означает DOCTYPE в HTML?','options' => ['Объявление типа документа','Название шрифта','Тип стиля','Имя файла']],
            ['question' => 'Какой тег используется для вставки изображения?','options' => ['<img>','<image>','<src>','<pic>']],
            ['question' => 'Как правильно закрыть самозакрывающийся тег?','options' => ['<br />','<br></br>','<br>','</br>']],
        ],
        'CSS Основы' => [
            ['question' => 'Как подключить CSS файл к HTML?','options' => ['<link rel="stylesheet" href="style.css">','<style href="style.css">','<css src="style.css">','<script src="style.css">']],
            ['question' => 'Какой селектор выбирает элемент по его id?','options' => ['#element','.element','element','@element']],
            ['question' => 'Как изменить цвет текста в CSS?','options' => ['color: red;','text-color: red;','font-color: red;','foreground: red;']],
            ['question' => 'Что такое каскадность в CSS?','options' => ['Приоритет правил при конфликте стилей','Порядок загрузки файлов','Вложенность элементов','Тип селектора']],
            ['question' => 'Какое свойство задаёт отступ элемента от края страницы?','options' => ['margin','padding','border','gap']],
        ],
        'Терминал и CLI' => [
            ['question' => 'Какой командой выводится содержимое директории в Windows?','options' => ['dir','ls','list','show']],
            ['question' => 'Что делает команда cd?','options' => ['Переход в другую директорию','Копирование файлов','Удаление файлов','Просмотр содержимого']],
            ['question' => 'Какой командой создаётся новая директория?','options' => ['mkdir','create','newdir','mkfolder']],
            ['question' => 'Что делает команда cls в Windows?','options' => ['Очищает экран терминала','Удаляет файл','Копирует данные','Запускает программу']],
            ['question' => 'Какой командой удаляется файл?','options' => ['del','rm -rf','erase','remove']],
        ],
        'Структура документа' => [
            ['question' => 'Какая правильная структура HTML-документа?','options' => ['html > head > body','body > head > html','head > body > html','html > body > head']],
            ['question' => 'В каком теге размещается основной контент страницы?','options' => ['<body>','<main>','<content>','<page>']],
            ['question' => 'Где располагаются мета-теги?','options' => ['Внутри <head>','Внутри <body>','Вне <html>','Внутри <meta>']],
            ['question' => 'Какой тег определяет заголовок страницы в браузере?','options' => ['<title>','<head>','<header>','<h1>']],
            ['question' => 'Что такое вложенность элементов в HTML?','options' => ['Расположение одного тега внутри другого','Количество атрибутов','Порядок подключения файлов','Тип документа']],
        ],
        'Текст, ссылки и списки' => [
            ['question' => 'Какой тег создаёт нумерованный список?','options' => ['<ol>','<ul>','<li>','<dl>']],
            ['question' => 'Как создать ссылку в HTML?','options' => ['<a href="url">Текст</a>','<link href="url">Текст</link>','<href url="url">Текст</href>','<url>Текст</url>']],
            ['question' => 'Какой тег создаёт элемент списка?','options' => ['<li>','<list>','<item>','<ls>']],
            ['question' => 'Какой атрибут ссылки открывает её в новой вкладке?','options' => ['target="_blank"','new="true"','open="new"','window="tab"']],
            ['question' => 'Какой тег создаёт горизонтальную линию?','options' => ['<hr>','<line>','<divider>','<separator>']],
        ],
        'Семантический HTML' => [
            ['question' => 'Какой тег обозначает основной контент страницы?','options' => ['<main>','<content>','<body>','<section>']],
            ['question' => 'Какой тег используется для навигационных ссылок?','options' => ['<nav>','<menu>','<links>','<navigation>']],
            ['question' => 'Что такое семантический HTML?','options' => ['Использование тегов по их смыслу','Использование стилей для разметки','Подключение скриптов','Анимация элементов']],
            ['question' => 'Какой тег определяет отдельную секцию контента?','options' => ['<section>','<div>','<block>','<area>']],
            ['question' => 'Какой тег используется для цитат?','options' => ['<blockquote>','<quote>','<cite>','<q>']],
        ],
        'Формы и валидация' => [
            ['question' => 'Какой атрибут тега input запрещает отправку пустого значения?','options' => ['required','mandatory','validate','empty']],
            ['question' => 'Какой тип input используется для ввода email?','options' => ['email','text','mail','address']],
            ['question' => 'Как отправить данные формы на сервер?','options' => ['<form method="post">','<submit>','<send>','<post>']],
            ['question' => 'Какой атрибут определяет максимальную длину текста?','options' => ['maxlength','max-length','limit','maxsize']],
            ['question' => 'Какой тег используется для выпадающего списка?','options' => ['<select>','<dropdown>','<option-list>','<menu>']],
        ],
        'Таблицы и мета-теги' => [
            ['question' => 'Какой тег создаёт строку таблицы?','options' => ['<tr>','<row>','<table-row>','<line>']],
            ['question' => 'Какой тег задаёт заголовок столбца таблицы?','options' => ['<th>','<td>','<header>','<col-head>']],
            ['question' => 'Какой мета-тег задаёт кодировку страницы?','options' => ['<meta charset="UTF-8">','<meta encoding="utf8">','<meta type="charset">','<meta language="ru">']],
            ['question' => 'Какой мета-тег задаёт описание страницы для поисковиков?','options' => ['<meta name="description">','<meta name="keywords">','<meta name="title">','<meta desc="">']],
            ['question' => 'Как объединить ячейки таблицы по вертикали?','options' => ['rowspan="2"','colspan="2"','merge="vertical"','span="row"']],
        ],
        'CSS Селекторы и каскад' => [
            ['question' => 'Какой селектор выбирает все элементы на странице?','options' => ['*','all','every','root']],
            ['question' => 'Как выбрать дочерний элемент?','options' => ['parent > child','parent child','parent::child','parent + child']],
            ['question' => 'Какой селектор имеет приоритет выше: class или id?','options' => ['id','class','tag','*']],
            ['question' => 'Что делает селектор [type="text"]?','options' => ['Выбирает элементы с атрибутом type равным text','Выбирает только текстовые элементы','Создаёт новый тип','Фильтрует контент']],
            ['question' => 'Какой селектор выбирает соседний элемент?','options' => ['+','~','>','::']],
        ],
        'Box Model и sizing' => [
            ['question' => 'Из чего состоит Box Model в CSS?','options' => ['Content, padding, border, margin','Width, height, depth','Top, right, bottom, left','Block, inline, flex']],
            ['question' => 'Что делает box-sizing: border-box?','options' => ['Учитывает padding и border в итоговую ширину','Удаляет отступы','Создаёт рамку','Изменяет цвет']],
            ['question' => 'Какое свойство задаёт внутренний отступ элемента?','options' => ['padding','margin','indent','inner']],
            ['question' => 'Что такое margin collapse?','options' => ['Схлопывание вертикальных отступов','Слияние двух элементов','Удаление отступов','Увеличение ширины']],
            ['question' => 'Какое свойство задаёт ширину блока с учётом padding и border?','options' => ['box-sizing: border-box','width: auto','size: content','overflow: hidden']],
        ],
        'Цвета, фоны и тень' => [
            ['question' => 'Как задать цвет фона элемента?','options' => ['background-color: #fff;','bg-color: white;','color-bg: #fff;','background: white']],
            ['question' => 'Какой функцией создаётся градиент фона?','options' => ['linear-gradient()','gradient()','bg-gradient()','color-gradient()']],
            ['question' => 'Как добавить тень элементу?','options' => ['box-shadow','shadow','element-shadow','drop-shadow']],
            ['question' => 'Какой формат цвета поддерживает прозрачность?','options' => ['rgba()','rgb()','hex','hsl()']],
            ['question' => 'Какое свойство задаёт прозрачность элемента?','options' => ['opacity','transparent','visibility','alpha']],
        ],
        'Git Основы' => [
            ['question' => 'Какой командой инициализируется Git-репозиторий?','options' => ['git init','git start','git create','git new']],
            ['question' => 'Какой командой фиксируются изменения?','options' => ['git commit','git save','git push','git store']],
            ['question' => 'Что делает команда git add?','options' => ['Добавляет файлы в индекс','Удаляет файлы','Переименовывает файлы','Просматривает файлы']],
            ['question' => 'Какой командой просматривается история коммитов?','options' => ['git log','git history','git list','git show']],
            ['question' => 'Что такое .gitignore?','options' => ['Файл со списком игнорируемых файлов','Команда Git','Тип ветки','Репозиторий']],
        ],
        'npm и пакеты' => [
            ['question' => 'Какой командой устанавливается пакет через npm?','options' => ['npm install','npm get','npm add','npm fetch']],
            ['question' => 'Что такое package.json?','options' => ['Файл конфигурации проекта с зависимостями','Файл стилей','Файл изображений','Файл шаблонов']],
            ['question' => 'Как удалить пакет через npm?','options' => ['npm uninstall','npm remove','npm delete','npm erase']],
            ['question' => 'Что делает команда npm init?','options' => ['Создаёт package.json','Устанавливает пакеты','Запускает проект','Удаляет зависимости']],
            ['question' => 'Как установить пакет как зависимость разработки?','options' => ['npm install --save-dev','npm install --dev','npm install --only-dev','npm install -D']],
        ],
        'VS Code для фронтенда' => [
            ['question' => 'Какой горячей клавишей открывается палитра команд?','options' => ['Ctrl+Shift+P','Ctrl+P','Ctrl+N','Ctrl+O']],
            ['question' => 'Какой плагин рекомендуется для подсветки синтаксиса HTML?','options' => ['Prettier','ESLint','GitLens','Live Server']],
            ['question' => 'Как быстро найти файл в проекте?','options' => ['Ctrl+P','Ctrl+F','Ctrl+S','Ctrl+G']],
            ['question' => 'Что такое Emmet в VS Code?','options' => ['Набор сокращений для HTML/CSS','Язык программирования','Фреймворк','Расширение браузера']],
            ['question' => 'Как запустить встроенный терминал?','options' => ['Ctrl+`','Ctrl+T','Ctrl+M','Ctrl+R']],
        ],
        'JavaScript Основы' => [
            ['question' => 'Как объявить переменную в JavaScript?','options' => ['let x = 10;','var x := 10;','x = 10;','dim x = 10;']],
            ['question' => 'Как вывести сообщение в консоль?','options' => ['console.log()','print()','log()','echo()']],
            ['question' => 'Какой тип данных у переменной null?','options' => ['object','null','undefined','boolean']],
            ['question' => 'Что такое выражение в JavaScript?','options' => ['Код, возвращающий значение','Команда вывода','Название переменной','Тип данных']],
            ['question' => 'Какой оператор проверяет равенство без приведения типов?','options' => ['===','==','=','!==']],
        ],
        'Переменные, типы и операторы' => [
            ['question' => 'Какой оператор проверяет строгое равенство?','options' => ['===','==','=','!==']],
            ['question' => 'Какой тип данных представляет целое число?','options' => ['number','int','integer','float']],
            ['question' => 'Что вернёт typeof null?','options' => ['"object"','"null"','"undefined"','"boolean"']],
            ['question' => 'Что такое template literal в JavaScript?','options' => ['Строки в обратных кавычках с интерполяцией','Обычные строки','Массивы','Числа']],
            ['question' => 'Какой оператор используется для присваивания по ссылке объектов?','options' => ['const/let','var','инкремент','деление']],
        ],
        'Условия и циклы' => [
            ['question' => 'Какой оператор используется для условного ветвления?','options' => ['if/else','for','while','switch/case']],
            ['question' => 'Какой цикл выполняется минимум один раз?','options' => ['do...while','for','while','forEach']],
            ['question' => 'Что делает оператор switch?','options' => ['Сравнивает значение с несколькими вариантами','Запускает цикл','Объявляет переменную','Создаёт функцию']],
            ['question' => 'Какой оператор используется для тернарного условия?','options' => ['? :','if/else','&&','||']],
            ['question' => 'Что такое break в цикле?','options' => ['Прерывает выполнение цикла','Пропускает итерацию','Начинает цикл заново','Увеличивает счётчик']],
        ],
        'Функции и замыкания' => [
            ['question' => 'Как объявить функцию в JavaScript?','options' => ['function name() {}','def name() {}','func name() {}','fn name() {}']],
            ['question' => 'Что такое замыкание (closure)?','options' => ['Функция с доступом к внешней области видимости','Вложенный цикл','Объект с методами','Тип переменной']],
            ['question' => 'Как передать аргумент в функцию по значению?','options' => ['Для примитивов значение копируется','Всегда по ссылке','Через глобальную переменную','Невозможно']],
            ['question' => 'Что такое IIFE?','options' => ['Немедленно вызываемая функция','Метод массива','Тип цикла','Оператор присваивания']],
            ['question' => 'Как вернуть значение из функции?','options' => ['return значение;','output значение;','yield значение;','give значение;']],
        ],
        'Объекты и массивы' => [
            ['question' => 'Как создать объект в JavaScript?','options' => ['{}','[]','()','new Object()']],
            ['question' => 'Как получить значение свойства объекта?','options' => ['obj.property или obj["property"]','obj->property','obj::property','obj(property)']],
            ['question' => 'Какой метод добавляет элемент в конец массива?','options' => ['push()','add()','append()','insert()']],
            ['question' => 'Какой метод создаёт новый массив на основе существующего?','options' => ['map()','forEach()','filter()','reduce()']],
            ['question' => 'Что делает деструктуризация объекта?','options' => ['Разбивает объект на переменные','Создаёт копию','Удаляет свойства','Соединяет объекты']],
        ],
        'Прототипы и классы' => [
            ['question' => 'Что такое прототип в JavaScript?','options' => ['Объект, от которого наследуются свойства','Тип данных','Метод массива','Оператор']],
            ['question' => 'Как объявить класс в JavaScript?','options' => ['class Name {}','class Name() {}','new class Name','def class Name']],
            ['question' => 'Что делает ключевое слово extends?','options' => ['Наследует класс от другого','Создаёт новый объект','Удаляет класс','Импортирует модуль']],
            ['question' => 'Что такое конструктор класса?','options' => ['Метод для инициализации объекта','Статический метод','Приватное свойство','Наследуемый метод']],
            ['question' => 'Как вызвать метод родительского класса?','options' => ['super.method()','parent.method()','this.method()','base.method()']],
        ],
        'DOM API' => [
            ['question' => 'Как получить элемент по его id?','options' => ['document.getElementById()','document.querySelector()','document.getElement()','document.find()']],
            ['question' => 'Как изменить текст элемента?','options' => ['element.textContent = "text"','element.text = "text"','element.setContent("text")','element.innerHTML("text")']],
            ['question' => 'Как создать новый HTML-элемент?','options' => ['document.createElement()','new Element()','document.new()','create.node()']],
            ['question' => 'Как добавить элемент в DOM?','options' => ['appendChild()','addNode()','insert()','append()']],
            ['question' => 'Что такое document.querySelector()?','options' => ['Метод выборки элемента по CSS-селектору','Метод создания элемента','Метод удаления элемента','Метод копирования']],
        ],
        'События и делегирование' => [
            ['question' => 'Как добавить обработчик события на элемент?','options' => ['element.addEventListener()','element.on()','element.bind()','element.watch()']],
            ['question' => 'Что такое делегирование событий?','options' => ['Обработка событий на родительском элементе','Создание копии события','Удаление обработчиков','Запуск события вручную']],
            ['question' => 'Как остановить всплытие события?','options' => ['event.stopPropagation()','event.cancel()','event.stop()','event.preventDefault()']],
            ['question' => 'Какой атрибут передаёт объект события?','options' => ['event','e','evt','все варианты верны']],
            ['question' => 'Что такое event.target?','options' => ['Элемент, на котором произошло событие','Родительский элемент','Объект события','Тип события']],
        ],
        'Работа с формами через JS' => [
            ['question' => 'Как получить значение input через JS?','options' => ['input.value','input.getValue()','input.text','input.data']],
            ['question' => 'Как предотвратить стандартную отправку формы?','options' => ['event.preventDefault()','form.stopSubmit()','return false','form.cancel()']],
            ['question' => 'Как получить доступ к элементам формы?','options' => ['form.elements','form.fields','form.items','form.inputs']],
            ['question' => 'Как изменить значение select?','options' => ['select.value = "option"','select.choose()','select.set("option")','select.selected()']],
            ['question' => 'Как проверить валидность формы?','options' => ['form.checkValidity()','form.isValid()','form.validate()','form.check()']],
        ],
        'ES6+ Modern Features' => [
            ['question' => 'Что такое spread оператор (...) в JavaScript?','options' => ['Разворачивает массив/объект на отдельные элементы','Создаёт массив','Удаляет элементы','Соединяет строки']],
            ['question' => 'Что такое деструктуризация?','options' => ['Разбор объекта/массива на переменные','Создание объекта','Удаление свойств','Копирование данных']],
            ['question' => 'Что такое optional chaining (?.)?','options' => ['Безопасный доступ к вложенным свойствам','Создание цепочки объектов','Удаление свойств','Типизация']],
            ['question' => 'Что такое Nullish Coalescing (??)?','options' => ['Оператор, предоставляющий значение по умолчанию при null или undefined','Сложение чисел','Сравнение строк','Присваивание']],
            ['question' => 'Что такое структурное обновление (object spread)?','options' => ['Создание нового объекта с изменёнными свойствами','Удаление объекта','Копирование в другой файл','Создание массива']],
        ],
        'Асинхронность: Promises, async/await' => [
            ['question' => 'Что такое Promise в JavaScript?','options' => ['Объект, представляющий завершение асинхронной операции','Тип переменной','Метод массива','Оператор цикла']],
            ['question' => 'Какой метод запускает async-функцию?','options' => ['await','async','promise','then']],
            ['question' => 'Что делает async/await?','options' => ['Упрощает работу с промисами','Создаёт новый поток','Ускоряет выполнение','Блокирует основной поток']],
            ['question' => 'Какой метод обрабатывает ошибку промиса?','options' => ['.catch()','.error()','.fail()','.reject()']],
            ['question' => 'Что такое Race Condition?','options' => ['Когда два промиса выполняются параллельно и результат зависит от порядка','Ошибка сети','Переполнение памяти','Синтаксическая ошибка']],
        ],
        'Git: Ветки, Merge, Rebase' => [
            ['question' => 'Как создать новую ветку в Git?','options' => ['git branch name','git create branch','git new branch','git switch -c']],
            ['question' => 'Как переключиться на другую ветку?','options' => ['git checkout branch','git switch branch','git go branch','git open branch']],
            ['question' => 'Что делает git merge?','options' => ['Объединяет ветки в текущей ветке','Удаляет ветку','Создаёт новый коммит','Переименовывает ветку']],
            ['question' => 'Что такое rebase в Git?','options' => ['Перенос коммитов из одной ветки в другую','Удаление ветки','Копирование файлов','Создание тега']],
            ['question' => 'Как разрешить конфликт слияния?','options' => ['Вручную отредактировать файл и сделать коммит','Удалить файл','Использовать git push','Перезапустить Git']],
        ],
        'Fetch API и HTTP-запросы' => [
            ['question' => 'Какой метод HTTP используется для получения данных?','options' => ['GET','POST','PUT','DELETE']],
            ['question' => 'Что возвращает fetch()?','options' => ['Promise с объектом Response','Объект данных','Строку JSON','Число']],
            ['question' => 'Как отправить POST-запрос через fetch?','options' => ['fetch(url, {method: "POST"})','fetch.post(url)','fetch.send(url)','fetch.postData(url)']],
            ['question' => 'Что такое Headers в HTTP-запросе?','options' => ['Метаданные запроса или ответа','Тело запроса','URL запроса','Код ответа']],
            ['question' => 'Какой статус-код означает успешный ответ?','options' => ['200','404','500','301']],
        ],
        'JSON и работа с данными' => [
            ['question' => 'Как преобразовать JSON-строку в объект JavaScript?','options' => ['JSON.parse()','JSON.stringify()','JSON.convert()','JSON.toObject()']],
            ['question' => 'Как преобразовать объект в JSON-строку?','options' => ['JSON.stringify()','JSON.parse()','JSON.toString()','JSON.toJSON()']],
            ['question' => 'Что такое JSON?','options' => ['Формат обмена данными','Язык программирования','Тип базы данных','Протокол']],
            ['question' => 'Какой метод преобразует данные в формате JSON в массив?','options' => ['JSON.parse() с массивом','JSON.toArray()','JSON.array()','JSON.collect()']],
            ['question' => 'Какой тип данных не поддерживается JSON?','options' => ['undefined','string','number','boolean']],
        ],
        'LocalStorage и Storage API' => [
            ['question' => 'Как сохранить данные в localStorage?','options' => ['localStorage.setItem("key", "value")','localStorage.save("key", "value")','localStorage.write("key", "value")','localStorage.store("key", "value")']],
            ['question' => 'Как получить данные из localStorage?','options' => ['localStorage.getItem("key")','localStorage.get("key")','localStorage.read("key")','localStorage.fetch("key")']],
            ['question' => 'Какой метод удаляет данные из localStorage?','options' => ['localStorage.removeItem()','localStorage.delete()','localStorage.clear()','localStorage.erase()']],
            ['question' => 'Что такое localStorage?','options' => ['Хранилище данных в браузере с постоянным доступом','Серверное хранилище','Cookie-файл','База данных SQL']],
            ['question' => 'Какой метод полностью очищает localStorage?','options' => ['localStorage.clear()','localStorage.reset()','localStorage.removeAll()','localStorage.wipe()']],
        ],
        'Web Workers и Performance API' => [
            ['question' => 'Что такое Web Worker?','options' => ['Фоновый поток для выполнения тяжёлых задач','Метод оптимизации CSS','Тип события','Асинхронная функция']],
            ['question' => 'Как создать Web Worker?','options' => ['new Worker("script.js")','createWorker()','Worker.spawn()','WebWorker.new()']],
            ['question' => 'Что такое Performance API?','options' => ['Набор инструментов для измерения производительности','Фреймворк для тестирования','Тип данных','Метод рендеринга']],
            ['question' => 'Как измерить время выполнения кода?','options' => ['performance.now()','Date.now()','console.time()','timer.start()']],
            ['question' => 'Какой метод запоминает начало замера времени?','options' => ['performance.mark()','performance.start()','performance.begin()','performance.init()']],
        ],
        'ES6 Модули и импорты' => [
            ['question' => 'Как экспортировать функцию из модуля?','options' => ['export function name() {}','module.export function name()','export default function name()','public function name()']],
            ['question' => 'Как импортировать модуль?','options' => ['import {name} from "./module"','require("./module")','include "./module"','load "./module"']],
            ['question' => 'Что такое default export?','options' => ['Основной экспорт модуля','Экспорт по умолчанию для всех файлов','Приватный экспорт','Стандартный импорт']],
            ['question' => 'Как импортировать всё из модуля?','options' => ['import * as name from "./module"','import all from "./module"','import {everything} from "./module"','import name.* from "./module"']],
            ['question' => 'Где работают ES6 модули?','options' => ['В браузере с type="module" и в Node.js','Только в Node.js','Только в браузере','Везде без ограничений']],
        ],
        'Webpack / Vite / Build Tools' => [
            ['question' => 'Что такое Webpack?','options' => ['Сборщик модулей для JavaScript','Текстовый редактор','Браузер','База данных']],
            ['question' => 'Что делает Vite?','options' => ['Ускоряет разработку через сервер с горячей перезагрузкой','Создаёт документацию','Тестирует код','Деплоит проект']],
            ['question' => 'Что такое entry point в Webpack?','options' => ['Основной файл для начала сборки','Выходной файл','Конфигурация','Плагин']],
            ['question' => 'Что такое loader в Webpack?','options' => ['Преобразует файлы перед сборкой','Удаляет файлы','Создаёт папки','Запускает тесты']],
            ['question' => 'Какой командой запускается dev-сервер Vite?','options' => ['npm run dev','npm run build','npm start','npm run serve']],
        ],
        'TypeScript Основы' => [
            ['question' => 'Что такое TypeScript?','options' => ['Надмножество JavaScript с типизацией','Новый браузер','Фреймворк для CSS','Язык для баз данных']],
            ['question' => 'Как объявить переменную с типом в TypeScript?','options' => ['let x: number = 5;','let x = 5: number;','number x = 5;','var x as number = 5;']],
            ['question' => 'Какой тип данных обозначает произвольный объект?','options' => ['any','object','var','dynamic']],
            ['question' => 'Что такое type alias?','options' => ['Именованный тип','Анонимный тип','Интерфейс','Класс']],
            ['question' => 'Какой файл настраивает TypeScript?','options' => ['tsconfig.json','typescript.config.js','ts.config.json','config.ts']],
        ],
        'TypeScript: Интерфейсы, Дженерики' => [
            ['question' => 'Что такое интерфейс в TypeScript?','options' => ['Описание структуры объекта','Тип переменной','Метод класса','Оператор']],
            ['question' => 'Что такое дженерики (generics)?','options' => ['Параметризованные типы','Обобщённые функции','Массивы','Строки']],
            ['question' => 'Как объявить интерфейс?','options' => ['interface User {}','type User {}','class User {}','struct User {}']],
            ['question' => 'Как использовать дженерик в функции?','options' => ['function id<T>(arg: T): T {}','function id(arg) {}','function id<T> {}','function<T> id() {}']],
            ['question' => 'Что такое keyof в TypeScript?','options' => ['Оператор получения ключей типа','Метод массива','Оператор сравнения','Тип данных']],
        ],
        'REST API и HTTP-запросы' => [
            ['question' => 'Что такое REST API?','options' => ['Архитектурный стиль для веб-сервисов','Тип базы данных','Язык программирования','Протокол шифрования']],
            ['question' => 'Какой HTTP-метод используется для обновления данных?','options' => ['PUT','GET','DELETE','POST']],
            ['question' => 'Что такое эндпоинт в REST API?','options' => ['URL-адрес для доступа к ресурсу','Тип данных','Метод аутентификации','Формат ответа']],
            ['question' => 'Какой статус-код означает "не найдено"?','options' => ['404','200','500','401']],
            ['question' => 'Что такое JSON в контексте REST API?','options' => ['Формат данных для обмена информацией','Протокол безопасности','Тип аутентификации','Формат URL']],
        ],
        'React: Компоненты и JSX' => [
            ['question' => 'Что такое JSX в React?','options' => ['Расширение JavaScript для описания UI','Язык стилей','Тип данных','Библиотека']],
            ['question' => 'Как создаётся функциональный компонент?','options' => ['function Component() {}','class Component {}','React.create()','new Component()']],
            ['question' => 'Как передать пропсы в компонент?','options' => ['<Component name="value" />','<Component props.name="value" />','<Component data="value" />','<Component prop="value" />']],
            ['question' => 'Что такое children в React?','options' => ['Вложенный контент компонента','Родительский компонент','Дочерний элемент','Стили']],
            ['question' => 'Какой ключевой атрибут нужен при рендере списка?','options' => ['key','id','index','name']],
        ],
        'React Hooks: useState, useEffect' => [
            ['question' => 'Что такое useState?','options' => ['Хук для управления состоянием компонента','Метод рендеринга','Тип пропсов','Обработчик событий']],
            ['question' => 'Как использовать useEffect?','options' => ['useEffect(() => {}, [deps])','useEffect(function, deps)','useEffect(callback)','useEffect.run()']],
            ['question' => 'Что делает useEffect с пустым массивом зависимостей?','options' => ['Выполняет эффект один раз при монтировании','Выполняет при каждом рендере','Не выполняет ничего','Удаляет компонент']],
            ['question' => 'Как обновить состояние в useState?','options' => ['setState(newValue) или setState(prev => newValue)','state = newValue','updateState()','this.setState()']],
            ['question' => 'Что такое cleanup в useEffect?','options' => ['Функция очистки при размонтировании','Удаление компонента','Очистка localStorage','Сброс стилей']],
        ],
        'React: Обработка событий' => [
            ['question' => 'Как добавить обработчик клика в React?','options' => ['onClick={() => {}}','onclick={() => {}}','addEventListener()','bindClick()']],
            ['question' => 'Как передать аргумент в обработчик события?','options' => ['onClick={() => handler(arg)}','onClick(handler(arg))','onClick={handler.bind(arg)}','onClick={handler, arg}']],
            ['question' => 'Как предотвратить поведение по умолчанию?','options' => ['event.preventDefault()','event.stopPropagation()','return false','event.cancel()']],
            ['question' => 'Что такое SyntheticEvent в React?','options' => ['Обёртка над нативными событиями браузера','Новый тип события','Кастомный обработчик','Тип данных']],
            ['question' => 'Как обработать изменение в input?','options' => ['onChange={(e) => setValue(e.target.value)}','onInput()','onchange()','watchInput()']],
        ],
        'React Router' => [
            ['question' => 'Что такое React Router?','options' => ['Библиотека для маршрутизации в React-приложении','Тип данных','Метод рендеринга','Обработчик событий']],
            ['question' => 'Как определить маршрут?','options' => ['<Route path="/home" component={Home} />','<Router path="/home"><Home /></Router>','<Path url="/home"><Home /></Path>','<Link to="/home">Home</Link>']],
            ['question' => 'Как создать ссылку в React Router?','options' => ['<Link to="/page">Text</Link>','<a href="/page">Text</a>','<NavLink to="/page">Text</NavLink>','<RouteLink to="/page">Text</RouteLink>']],
            ['question' => 'Что такое useHistory?','options' => ['Хук для навигации между страницами','Метод для работы с историей браузера','Тип маршрута','Обработчик 404']],
            ['question' => 'Как получить параметры URL?','options' => ['useParams()','useQuery()','useSearchParams()','useRoute()']],
        ],
        'State Management' => [
            ['question' => 'Что такое Context API в React?','options' => ['Механизм передачи данных через дерево компонентов','Тип состояния','Метод рендеринга','Обработчик событий']],
            ['question' => 'Что такое Redux?','options' => ['Контейнер состояния для JavaScript-приложений','Тип данных','Метод рендеринга','Библиотека стилей']],
            ['question' => 'Какой метод Redux добавляет новое действие?','options' => ['store.dispatch()','store.add()','store.emit()','store.trigger()']],
            ['question' => 'Что такое reducer в Redux?','options' => ['Функция, определяющая изменение состояния','Метод добавления данных','Тип хранилища','Обработчик ошибок']],
            ['question' => 'Какой хук заменяет connect в Redux?','options' => ['useSelector и useDispatch','useRedux','useState и useEffect','useStore']],
        ],
        'Кастомные хуки' => [
            ['question' => 'Что такое кастомный хук в React?','options' => ['Пользовательская функция с приставкой use','Встроенный метод React','Тип компонента','Обработчик событий']],
            ['question' => 'Какое правило именования кастомных хуков?','options' => ['Должны начинаться с "use"','Должны заканчиваться на "Hook"','Должны быть строчными','Должны содержать "custom"']],
            ['question' => 'Зачем создавать кастомные хуки?','options' => ['Для переиспользования логики между компонентами','Для ускорения рендеринга','Для стилизации компонентов','Для работы с сервером']],
            ['question' => 'Какой хук можно использовать внутри кастомного хука?','options' => ['Любой встроенный хук React','Только useState','Только useEffect','Только useContext']],
            ['question' => 'Можно ли вызывать хуки условно?','options' => ['Нет, только на верхнем уровне','Да, в любом месте','Только в useEffect','Только в return']],
        ],
        'Vue.js: Основы' => [
            ['question' => 'Что такое Vue.js?','options' => ['Прогрессивный фреймворк для создания UI','Библиотека стилей','Серверный фреймворк','Тип данных']],
            ['question' => 'Как определить реактивные данные в Vue 3?','options' => ['ref() и reactive()','data() {}','this.state','useState()']],
            ['question' => 'Как связать данные с шаблоном?','options' => ['{{ variable }}','[[ variable ]]','{ variable }','<%= variable %>']],
            ['question' => 'Что такое computed свойства?','options' => ['Вычисляемые данные, зависящие от реактивных зависимостей','Статические данные','Методы жизненного цикла','Обработчики событий']],
            ['question' => 'Как определить компонент в Vue?','options' => ['export default {template: "", data() {}}','function Component() {}','class Component {}','const Component = new Vue()']],
        ],
        'Vue Router и Pinia' => [
            ['question' => 'Что такое Vue Router?','options' => ['Официальный маршрутизатор для Vue.js','Тип данных','Метод рендеринга','Библиотека стилей']],
            ['question' => 'Как определить маршрут в Vue Router?','options' => ['{path: "/home", component: Home}','<route path="/home"><home /></route>','Router.add("/home", Home)','Vue.route("/home", Home)']],
            ['question' => 'Что такое Pinia?','options' => ['Менеджер состояний для Vue.js','Тип данных','Метод рендеринга','Библиотека стилей']],
            ['question' => 'Как использовать Pinia в компоненте?','options' => ['useStore() из @pinia/core','this.$store','useState()','usePinia()']],
            ['question' => 'Что такое динамические маршруты?','options' => ['Маршруты с параметрами :id','Статические маршруты','Вложенные маршруты','Редиректы']],
        ],
        'Unit-тестирование' => [
            ['question' => 'Что такое unit-тестирование?','options' => ['Тестирование отдельных модулей кода','Тестирование всего приложения','Тестирование интерфейса','Тестирование производительности']],
            ['question' => 'Какой фреймворк популярен для тестирования React?','options' => ['Jest','Django','Laravel','Spring']],
            ['question' => 'Что такое describe() в тестах?','options' => ['Блок для группировки тестов','Функция теста','Ассерция','Настройка окружения']],
            ['question' => 'Что такое expect() в тестах?','options' => ['Проверка результата на соответствие ожиданию','Запуск теста','Настройка мока','Импорт модуля']],
            ['question' => 'Какой метод проверяет, что вызов был совершён?','options' => ['toHaveBeenCalled()','wasCalled()','calledWith()','verifyCall()']],
        ],
        'E2E тестирование' => [
            ['question' => 'Что такое E2E тестирование?','options' => ['Тестирование всего приложения как пользователь','Тестирование отдельных функций','Тестирование стилей','Тестирование сервера']],
            ['question' => 'Какой инструмент автоматизации браузера популярен для E2E?','options' => ['Cypress','Jest','Mocha','Chai']],
            ['question' => 'Как Cypress находит элементы?','options' => ['Через селекторы CSS/XPath','Через IP-адрес','Через URL','Через порт']],
            ['question' => 'Что такое beforeEach() в E2E тестах?','options' => ['Хук, выполняющийся перед каждым тестом','Хук после теста','Функция для ассерций','Настройка сервера']],
            ['question' => 'Как проверить, что элемент виден на странице?','options' => ['should("be.visible")','isVisible()','checkVisible()','expectVisible()']],
        ],
        'Next.js / Nuxt: SSR и SSG' => [
            ['question' => 'Что такое SSR в Next.js?','options' => ['Рендеринг страниц на сервере перед отправкой клиенту','Статическая генерация','Клиентский рендеринг','Кэширование']],
            ['question' => 'Как в Next.js получить данные на сервере для страницы?','options' => ['getServerSideProps()','useEffect()','componentDidMount()','getInitialProps()']],
            ['question' => 'Что такое SSG?','options' => ['Статическая генерация страниц при сборке','Динамический рендеринг','Клиентский рендеринг','API роутинг']],
            ['question' => 'Как определить страницу Next.js?','options' => ['Файл в папке pages/','Файл в папке routes/','Файл в папке components/','Файл в папке views/']],
            ['question' => 'Что такое ISR в Next.js?','options' => ['Инкрементальная статическая регенерация','Интегрированный рендеринг','Интерактивный рендеринг','Импортный рендеринг']],
        ],
        'Производительность (CWV)' => [
            ['question' => 'Что такое Core Web Vitals?','options' => ['Метрики качества пользовательского опыта','Типы стилей','Методы оптимизации','Фреймворки']],
            ['question' => 'Что измеряет LCP?','options' => ['Время загрузки основного контента','Время отклика на взаимодействие','Визуальную стабильность','Количество запросов']],
            ['question' => 'Что такое CLS?','options' => ['Cumulative Layout Shift - совокупное смещение макета','Content Loading Speed','Client Library System','Core Language Syntax']],
            ['question' => 'Как ускорить загрузку изображений?','options' => ['Использовать lazy loading и оптимизированные форматы','Увеличить разрешение','Использовать GIF','Использовать Base64']],
            ['question' => 'Что такое Code Splitting?','options' => ['Разделение кода на части для загрузки по требованию','Удаление неиспользуемого кода','Сжатие файлов','Минификация']],
        ],
        'Web Security: XSS, CSRF' => [
            ['question' => 'Что такое XSS атака?','options' => ['Внедрение вредоносного скрипта в веб-страницу','Атака на сервер','Перехват паролей','DDoS атака']],
            ['question' => 'Как защититься от XSS?','options' => ['Экранирование пользовательского ввода','Использование HTTP','Отключение JavaScript','Использование cookies']],
            ['question' => 'Что такое CSRF атака?','options' => ['Подмена запроса от имени пользователя','Атака на базу данных','Взлом Wi-Fi','Перехват трафика']],
            ['question' => 'Как защититься от CSRF?','options' => ['Использование CSRF-токенов','HTTPS шифрование','CORS заголовки','Rate limiting']],
            ['question' => 'Что такое Content Security Policy?','options' => ['Политика безопасности, ограничивающая источники контента','Метод шифрования','Протокол передачи данных','Тип аутентификации']],
        ],
        'SEO для фронтенда' => [
            ['question' => 'Что такое SEO?','options' => ['Оптимизация для поисковых систем','Тип данных','Фреймворк','Протокол']],
            ['question' => 'Какой мета-тег важен для SEO?','options' => ['<meta name="description">','<meta name="color">','<meta name="font">','<meta name="align">']],
            ['question' => 'Что такое Sitemap?','options' => ['Файл со списком страниц сайта для поисковиков','Карта сайта','Контактная информация','Стили CSS']],
            ['question' => 'Какой атрибут тега a важен для SEO?','options' => ['href с осмысленным текстом ссылки','target="_blank"','rel="nofollow"','download']],
            ['question' => 'Что такое SSR для SEO?','options' => ['Рендеринг на сервере для индексации поисковиками','Клиентский рендеринг','Кэширование','Минификация']],
        ],
        'PWA: Service Workers' => [
            ['question' => 'Что такое PWA?','options' => ['Прогрессивное веб-приложение','Тип базы данных','Серверный фреймворк','Язык программирования']],
            ['question' => 'Что такое Service Worker?','options' => ['Скрипт, работающий в фоне для кэширования и обработки запросов','Обработчик событий','Метод рендеринга','Тип данных']],
            ['question' => 'Как зарегистрировать Service Worker?','options' => ['navigator.serviceWorker.register()','serviceWorker.start()','window.registerSW()','SW.create()']],
            ['question' => 'Что такое манифест PWA?','options' => ['JSON-файл с метаданными приложения','Тип данных','Конфигурация сервера','Файл стилей']],
            ['question' => 'Какое основное преимущество PWA?','options' => ['Работа в оффлайне и быстрая загрузка','Красивый интерфейс','Большая память','Высокая скорость CPU']],
        ],
        'CI/CD и автоматизация' => [
            ['question' => 'Что такое CI/CD?','options' => ['Непрерывная интеграция и доставка кода','Тип данных','Фреймворк','Протокол']],
            ['question' => 'Какой инструмент автоматизации сборки популярен?','options' => ['GitHub Actions','VS Code','Git','npm']],
            ['question' => 'Что такое Pipeline в CI/CD?','options' => ['Последовательность шагов для сборки и деплоя','Тип данных','Метод тестирования','Обработчик ошибок']],
            ['question' => 'Какой файл конфигурирует GitHub Actions?','options' => ['.github/workflows/main.yml','package.json','Makefile','Dockerfile']],
            ['question' => 'Что такое artifact в CI/CD?','options' => ['Результат сборки для деплоя','Исходный код','Тесты','Документация']],
        ],
        'Деплой: Vercel / Netlify' => [
            ['question' => 'Как деплоить React-приложение на Vercel?','options' => ['Через Git-интеграцию или CLI','FTP загрузкой','Через SSH','Ручная загрузка файлов']],
            ['question' => 'Что такое preview deployment в Vercel?','options' => ['Предварительный деплой для проверки изменений','Основной деплой','Локальный сервер','Бэкап']],
            ['question' => 'Какой формат конфигурации использует Netlify?','options' => ['netlify.toml','vercel.json','.env','config.yml']],
            ['question' => 'Что такое переменные окружения в деплое?','options' => ['Настройки, доступные во время выполнения','Типы данных','Методы рендеринга','Обработчики событий']],
            ['question' => 'Как автоматически деплоить при коммите в main?','options' => ['Настроить CI/CD пайплайн','Ручной деплой','FTP клиент','Скопировать файлы']],
        ],
        'Мониторинг ошибок (Sentry)' => [
            ['question' => 'Что такое Sentry?','options' => ['Платформа мониторинга ошибок в реальном времени','Тип базы данных','Фреймворк','Протокол']],
            ['question' => 'Как установить Sentry в React-проект?','options' => ['npm install @sentry/react','npm install sentry','npm install error-monitor','npm install debug']],
            ['question' => 'Что такое breadcrumbs в Sentry?','options' => ['История действий пользователя перед ошибкой','Тип ошибки','Метод исправления','Настройка']],
            ['question' => 'Как Sentry группирует одинаковые ошибки?','options' => ['По fingerprint ошибки','По времени','По IP-адресу','По размеру']],
            ['question' => 'Какой уровень важности ошибки в Sentry?','options' => ['fatal, error, warning, info, debug','high, medium, low','critical, major, minor','1, 2, 3, 4, 5']],
        ],
    
        ];
    }

    private function getFrontendExamData(): array
    {
        return [
        'Какой HTTP-метод используется для удаления данных на сервере?' => ['DELETE','GET','POST','PUT'],
        'Что такое JSX в React?' => ['Расширение JavaScript для описания UI','Язык стилей','Тип данных','Библиотека'],
        'Какой оператор проверяет строгое равенство в JavaScript?' => ['===','==','=','!=='],
        'Что такое замыкание (closure) в JavaScript?' => ['Функция с доступом к внешней области видимости','Вложенный цикл','Объект с методами','Тип переменной'],
        'Какой мета-тег задаёт кодировку HTML-страницы?' => ['<meta charset="UTF-8">','<meta encoding="utf8">','<meta type="charset">','<meta language="ru">'],
        'Какой метод Redux отправляет действие (action)?' => ['store.dispatch()','store.add()','store.emit()','store.trigger()'],
        'Что такое SSR в Next.js?' => ['Рендеринг страниц на сервере перед отправкой клиенту','Статическая генерация','Клиентский рендеринг','Кэширование'],
        'Как предотвратить стандартную отправку формы в JavaScript?' => ['event.preventDefault()','form.stopSubmit()','return false','form.cancel()'],
        'Какой CSS-селектор имеет наивысший приоритет?' => ['#id селектор','.class селектор','tag селектор','* селектор'],
        'Что такое Service Worker в контексте PWA?' => ['Скрипт, работающий в фоне для кэширования запросов','Обработчик событий','Метод рендеринга','Тип данных'],
        'Как создать новую ветку в Git?' => ['git branch name','git create branch','git new branch','git switch -c'],
        'Какой хук React используется для управления состоянием?' => ['useState','useEffect','useContext','useRef'],
        'Как получить данные из localStorage?' => ['localStorage.getItem("key")','localStorage.get("key")','localStorage.read("key")','localStorage.fetch("key")'],
        'Что такое Core Web Vitals?' => ['Метрики качества пользовательского опыта','Типы стилей','Методы оптимизации','Фреймворки'],
        'Какое ключевое слово определяет класс в JavaScript?' => ['class','Class','CLASS','def'],
        'Что такое CORS?' => ['Cross-Origin Resource Sharing - механизм разрешения запросов с других доменов','Тип данных','Метод тестирования','Протокол шифрования'],
        'Как отправить POST-запрос через Fetch API?' => ['fetch(url, {method: "POST"})','fetch.post(url)','fetch.send(url)','fetch.postData(url)'],
        'Что такое TypeScript?' => ['Надмножество JavaScript с типизацией','Новый браузер','Фреймворк для CSS','Язык для баз данных'],
        'Какой атрибут ссылки открывает её в новой вкладке?' => ['target="_blank"','new="true"','open="new"','window="tab"'],
        'Что такое Pinia в контексте Vue.js?' => ['Менеджер состояний для Vue.js','Тип данных','Метод рендеринга','Библиотека стилей'],
        'Как проверить валидность формы через JavaScript?' => ['form.checkValidity()','form.isValid()','form.validate()','form.check()'],
        'Какой метод массива создаёт новый массив на основе условия?' => ['filter()','map()','forEach()','reduce()'],
        'Что такое Webpack?' => ['Сборщик модулей для JavaScript','Текстовый редактор','Браузер','База данных'],
        'Какой статус-код HTTP означает "Unauthorized"?' => ['401','404','500','200'],
        'Что такое деструктуризация в JavaScript?' => ['Разбор объекта/массива на переменные','Создание объекта','Удаление свойств','Копирование данных'],
        'Какой инструмент используется для E2E тестирования?' => ['Cypress','Jest','Mocha','Chai'],
        'Что такое LCP в контексте Core Web Vitals?' => ['Время загрузки основного контента','Время отклика на взаимодействие','Визуальная стабильность','Количество запросов'],
        'Как подключить CSS файл к HTML документу?' => ['<link rel="stylesheet" href="style.css">','<style href="style.css">','<css src="style.css">','<script src="style.css">'],
        'Что такое Context API в React?' => ['Механизм передачи данных через дерево компонентов','Тип состояния','Метод рендеринга','Обработчик событий'],
        'Какой файл конфигурирует TypeScript?' => ['tsconfig.json','typescript.config.js','ts.config.json','config.ts'],
    
        ];
    }

    private function getBackendQuizData(): array
    {
        return [
        'HTTP/HTTPS Протокол' => [
            ['question' => 'Какой порт используется по умолчанию для HTTPS?','options' => ['443','80','8080','8443']],
            ['question' => 'Что означает SSL/TLS в контексте HTTPS?','options' => ['Протокол шифрования для безопасной передачи данных','Протокол маршрутизации пакетов','Метод сжатия данных','Протокол передачи файлов']],
            ['question' => 'Какой HTTP-метод используется для получения ресурса?','options' => ['GET','POST','PUT','DELETE']],
            ['question' => 'Что вернёт код ответа 404?','options' => ['Ресурс не найден','Внутренняя ошибка сервера','Запрос успешно выполнен','Ресурс перемещён навсегда']],
            ['question' => 'Какой заголовок указывает формат тела ответа?','options' => ['Content-Type','Accept','Authorization','Cache-Control']],
        ],
        'Как работает интернет' => [
            ['question' => 'Что делает DNS-сервер?','options' => ['Преобразует доменные имена в IP-адреса','Фильтрует сетевой трафик','Шифрует данные','Управляет базами данных']],
            ['question' => 'Какой протокол используется для передачи веб-страниц?','options' => ['HTTP','FTP','SMTP','SNMP']],
            ['question' => 'Что такое TCP/IP?','options' => ['Семейство протоколов для сетевого взаимодействия','Язык программирования','Операционная система','База данных']],
            ['question' => 'Какой уровень модели OSI соответствует протоколу HTTP?','options' => ['Прикладной (7)','Транспортный (4)','Канальный (2)','Сетевой (3)']],
            ['question' => 'Что такое маршрутизатор?','options' => ['Устройство для пересылки пакетов между сетями','Устройство для хранения данных','Программа для шифрования','Протокол передачи данных']],
        ],
        'Терминал и CLI' => [
            ['question' => 'Какая команда выводит содержимое директории в Linux?','options' => ['ls','cd','pwd','mkdir']],
            ['question' => 'Что делает команда `grep`?','options' => ['Ищет строки по шаблону в файлах','Копирует файлы','Удаляет файлы','Создаёт директории']],
            ['question' => 'Как перенаправить вывод команды в файл?','options' => ['Оператор >','Оператор |','Оператор &','Оператор $']],
            ['question' => 'Какая команда показывает текущую рабочую директорию?','options' => ['pwd','ls','cd','cat']],
            ['question' => 'Что делает флаг `-la` в команде `ls -la`?','options' => ['Показывает все файлы включая скрытые с подробной информацией','Сортирует файлы по размеру','Удаляет файлы','Создаёт новый файл']],
        ],
        'Git Основы' => [
            ['question' => 'Какой командой создаётся новый коммит в Git?','options' => ['git commit','git push','git pull','git add']],
            ['question' => 'Что делает команда `git branch`?','options' => ['Создаёт или перечисляет ветки','Сливает ветки','Удаляет репозиторий','Показывает историю коммитов']],
            ['question' => 'Как отправить изменения на удалённый репозиторий?','options' => ['git push','git pull','git fetch','git clone']],
            ['question' => 'Что такое index (staging area) в Git?','options' => ['Промежуточная область для подготовки коммита','Удалённый репозиторий','Локальная копия проекта','Файл конфигурации']],
            ['question' => 'Какой командой можно посмотреть историю коммитов?','options' => ['git log','git status','git diff','git show']],
        ],
        'Python / Node.js / Go / Java' => [
            ['question' => 'Какой язык используется для написания серверных приложений на Node.js?','options' => ['JavaScript','Python','Go','Java']],
            ['question' => 'В каком языке используется ключевое слово `func` для объявления функций?','options' => ['Go','Python','Java','PHP']],
            ['question' => 'Какой язык компилируется в байт-код для JVM?','options' => ['Java','Go','Python','Node.js']],
            ['question' => 'Какой фреймворк является стандартом для веб-приложений на Python?','options' => ['Django','Express','Gin','Spring']],
            ['question' => 'Какой язык использует встроенный garbage collector и не требует компиляции?','options' => ['Python','Go','Java','C++']],
        ],
        'Типы данных и структуры' => [
            ['question' => 'Какой тип данных в JavaScript может хранить `null`?','options' => ['object','undefined','boolean','number']],
            ['question' => 'Что такое кортеж (tuple) в Python?','options' => ['Неизменяемая упорядоченная коллекция','Изменяемый список','Ассоциативный массив','Множество']],
            ['question' => 'Какой тип данных используется для хранения пар ключ-значение в Python?','options' => ['dict','list','set','tuple']],
            ['question' => 'Что такое структура данных стек (stack)?','options' => ['Структура LIFO — последним пришёл, первым вышел','Структура FIFO — первым пришёл, первым вышел','Двусвязный список','Дерево поиска']],
            ['question' => 'Какой тип данных в Go является массивом фиксированного размера?','options' => ['array','slice','map','struct']],
        ],
        'Функции и ООП' => [
            ['question' => 'Что такое инкапсуляция в ООП?','options' => ['Скрытие внутренней реализации объекта','Наследование свойств родителя','Полиморфизм методов','Создание абстрактных классов']],
            ['question' => 'Что делает ключевое слово `static` в классе PHP?','options' => ['Определяет принадлежность к классу, а не к объекту','Делает метод приватным','Запрещает наследование','Создаёт абстрактный метод']],
            ['question' => 'Что такое полиморфизм?','options' => ['Возможность объектов разных классов иметь одинаковый интерфейс','Скрытие данных','Наследование','Композиция']],
            ['question' => 'Какой принцип ООП подразумевает, что объекты должны быть заменяемы без влияния на программу?','options' => ['Принцип подстановки Лисков','Принцип единственной ответственности','Принцип открытости/закрытости','Принцип инверсии зависимостей']],
            ['question' => 'Что такое абстрактный класс?','options' => ['Класс, который нельзя инстанцировать напрямую','Класс с только приватными методами','Класс без методов','Класс с одним методом']],
        ],
        'Асинхронность и потоки' => [
            ['question' => 'Что такое event loop в Node.js?','options' => ['Цикл обработки асинхронных событий','Поток для выполнения синхронного кода','Менеджер памяти','Обработчик ошибок']],
            ['question' => 'Какой паттерн используется для обработки множества запросов в одном потоке?','options' => ['Event-driven архитектура','Thread-per-request','Multi-processing','Synchronous I/O']],
            ['question' => 'Что такое Promise в JavaScript?','options' => ['Объект, представляющий результат асинхронной операции','Синхронная функция','Переменная','Класс для работы с БД']],
            ['question' => 'Какой модуль Python используется для работы с асинхронным I/O?','options' => ['asyncio','threading','multiprocessing','socket']],
            ['question' => 'Что такое goroutine в Go?','options' => ['Легковесный поток, управляемый runtime Go','Тяжёлый системный поток','Объект класса','Функция обратного вызова']],
        ],
        'REST API: принципы' => [
            ['question' => 'Какой HTTP-метод используется для частичного обновления ресурса?','options' => ['PATCH','GET','DELETE','HEAD']],
            ['question' => 'Что такое идемпотентность в REST?','options' => ['Повторный запрос даёт тот же результат','Запрос возвращает ошибку','Данные шифруются','Создаётся новый ресурс']],
            ['question' => 'Какой код ответа означает успешное создание ресурса?','options' => ['201 Created','200 OK','404 Not Found','500 Internal Server Error']],
            ['question' => 'Какой заголовок используется для передачи токена авторизации?','options' => ['Authorization','Content-Type','Accept','Cache-Control']],
            ['question' => 'Что такое stateless в контексте REST?','options' => ['Сервер не хранит состояние между запросами','Сервер хранит все данные','Клиент хранит состояние','Соединение всегда остаётся активным']],
        ],
        'GraphQL' => [
            ['question' => 'Что такое GraphQL?','options' => ['Язык запросов для API и runtime для выполнения этих запросов','База данных','Фреймворк для фронтенда','Протокол для передачи файлов']],
            ['question' => 'Какой тип операции GraphQL позволяет получить данные?','options' => ['query','mutation','subscription','fragment']],
            ['question' => 'Что такое Schema в GraphQL?','options' => ['Описание типов данных и операций доступных в API','Файл конфигурации','База данных','Клиентская библиотека']],
            ['question' => 'Какой тип операции GraphQL используется для изменения данных?','options' => ['mutation','query','subscription','enum']],
            ['question' => 'В чём главное отличие GraphQL от REST?','options' => ['Клиент запрашивает только нужные данные','GraphQL использует только GET-запросы','GraphQL не поддерживает авторизацию','REST работает быстрее']],
        ],
        'WebSocket и SSE' => [
            ['question' => 'Какой протокол используется для двусторонней связи в реальном времени?','options' => ['WebSocket','HTTP','FTP','SMTP']],
            ['question' => 'Что такое Server-Sent Events (SSE)?','options' => ['Односторонняя потоковая передача данных от сервера клиенту','Двусторонний обмен данными','Протокол шифрования','Метод сжатия данных']],
            ['question' => 'Какой HTTP-метод используется для установки WebSocket-соединения?','options' => ['GET с заголовком Upgrade','POST','PUT','DELETE']],
            ['question' => 'Что происходит при обрыве WebSocket-соединения?','options' => ['Соединение можно восстановить через повторное рукопожатие','Данные теряются навсегда','Сервер автоматически перезапускает соединение','Клиент блокируется']],
            ['question' => 'Для каких задач лучше подходит SSE вместо WebSocket?','options' => ['Для уведомлений и обновлений в реальном времени от сервера','Для чатов с двусторонним обменом','Для онлайн-игр','Для передачи файлов']],
        ],
        'SQL Основы' => [
            ['question' => 'Какой оператор SQL используется для выборки данных?','options' => ['SELECT','INSERT','UPDATE','DELETE']],
            ['question' => 'Что делает оператор JOIN в SQL?','options' => ['Объединяет строки из двух или более таблиц','Удаляет таблицу','Создаёт индекс','Группирует данные']],
            ['question' => 'Как ограничить количество возвращаемых строк в SQL?','options' => ['LIMIT','TOP','ROWNUM','FETCH']],
            ['question' => 'Что такое первичный ключ (PRIMARY KEY)?','options' => ['Уникальный идентификатор записи в таблице','Внешний ключ','Индекс','Ограничение уникальности']],
            ['question' => 'Какой оператор используется для фильтрации групп строк?','options' => ['HAVING','WHERE','GROUP BY','ORDER BY']],
        ],
        'PostgreSQL / MySQL' => [
            ['question' => 'Какой тип данных в PostgreSQL используется для хранения JSON?','options' => ['jsonb','varchar','text','blob']],
            ['question' => 'Что такое транзакция в базе данных?','options' => ['Логическая единица работы, которая должна быть выполнена целиком','Запрос на чтение','Индекс таблицы','Пользователь базы данных']],
            ['question' => 'Какой движок хранения используется в MySQL по умолчанию?','options' => ['InnoDB','MyISAM','MEMORY','ARCHIVE']],
            ['question' => 'Что такое индекс в базе данных?','options' => ['Структура данных для ускорения поиска','Таблица','Представление','Хранимая процедура']],
            ['question' => 'Какой уровень изоляции транзакций обеспечивает защиту от грязного чтения?','options' => ['READ COMMITTED','READ UNCOMMITTED','SERIALIZABLE','REPEATABLE READ']],
        ],
        'NoSQL: MongoDB / Redis' => [
            ['question' => 'Какой тип данных использует MongoDB для хранения документов?','options' => ['BSON (Binary JSON)','CSV','XML','Parquet']],
            ['question' => 'Что такое Redis?','options' => ['Ключ-значение хранилище данных в памяти','Реляционная база данных','Файловая система','Мессенджер']],
            ['question' => 'Какой тип данных Redis поддерживает для хранения списков?','options' => ['List','Set','Hash','Sorted Set']],
            ['question' => 'В чём главное преимущество NoSQL перед SQL?','options' => ['Гибкая схема данных','Строгая типизация','Поддержка транзакций','ACID-гарантии']],
            ['question' => 'Какой метод MongoDB используется для вставки нового документа?','options' => ['insertOne()','selectOne()','updateOne()','deleteOne()']],
        ],
        'ORM: Eloquent / SQLAlchemy' => [
            ['question' => 'Что такое ORM?','options' => ['Объектно-реляционное отображение для работы с БД через объекты','Операционная система','Язык программирования','Протокол передачи данных']],
            ['question' => 'Какой метод Eloquent используется для поиска записи по ID?','options' => ['find()','search()','get()','query()']],
            ['question' => 'Что такое модель в ORM?','options' => ['Класс, представляющий таблицу в базе данных','SQL-запрос','Индекс таблицы','Триггер']],
            ['question' => 'Какой метод SQLAlchemy используется для добавления новой записи?','options' => ['add()','insert()','create()','save()']],
            ['question' => 'Что такое миграция в контексте ORM?','options' => ['Скрипт для изменения структуры базы данных','Метод для запроса данных','Тип соединения','Конфигурационный файл']],
        ],
        'Аутентификация: JWT, OAuth' => [
            ['question' => 'Что такое JWT?','options' => ['JSON Web Token — компактный формат для передачи claims между сторонами','Протокол шифрования','База данных','Язык программирования']],
            ['question' => 'Из скольки частей состоит JWT?','options' => ['Трёх: header, payload, signature','Двух: body, signature','Четырёх: header, body, footer, key','Одной: token']],
            ['question' => 'Что такое OAuth 2.0?','options' => ['Протокол авторизации для предоставления доступа к ресурсам','Протокол шифрования','Метод хеширования паролей','База данных']],
            ['question' => 'Где хранится JWT на клиенте?','options' => ['В localStorage или cookie','В sessionStorage только','На сервере','В переменных окружения']],
            ['question' => 'Что такое refresh token?','options' => ['Токен для получения нового access token без повторной аутентификации','Основной токен доступа','Токен для шифрования','Токен для авторизации']],
        ],
        'Авторизация и RBAC' => [
            ['question' => 'Что такое RBAC?','options' => ['Role-Based Access Control — управление доступом на основе ролей','Метод шифрования','Протокол передачи данных','Тип базы данных']],
            ['question' => 'Чем авторизация отличается от аутентификации?','options' => ['Авторизация определяет, что пользователь может делать','Аутентификация определяет права доступа','Это одно и то же','Авторизация проверяет пароль']],
            ['question' => 'Что такое принцип минимальных привилегий?','options' => ['Пользователь получает только те права, которые необходимы для задачи','Пользователь получает все права','Права не проверяются','Права наследуются от роли']],
            ['question' => 'Что такое ACL (Access Control List)?','options' => ['Список правил, определяющих доступ к ресурсам','Список пользователей','Журнал аудита','Конфигурация сервера']],
            ['question' => 'Какой паттерн используется для проверки прав в каждом запросе?','options' => ['Middleware','Singleton','Factory','Observer']],
        ],
        'Хеширование и соли' => [
            ['question' => 'Зачем используются соли при хешировании паролей?','options' => ['Для предотвращения атак по словарю и rainbow tables','Для ускорения хеширования','Для сжатия данных','Для шифрования']],
            ['question' => 'Какой алгоритм хеширования рекомендуется для паролей?','options' => ['bcrypt','MD5','SHA-1','Base64']],
            ['question' => 'Что такое хеш-функция?','options' => ['Функция, преобразующая входные данные в фиксированную строку','Алгоритм сортировки','Протокол передачи','Метод шифрования']],
            ['question' => 'Почему MD5 не рекомендуется для хеширования паролей?','options' => ['Он быстрый и уязвим для rainbow tables','Он слишком медленный','Он не поддерживает кириллицу','Он генерирует слишком длинные хеши']],
            ['question' => 'Что такое ключевое расширение (key stretching)?','options' => ['Намеренное замедление хеширования для повышения безопасности','Ускорение хеширования','Сжатие данных','Шифрование ключей']],
        ],
        'OWASP Top 10' => [
            ['question' => 'Что такое инъекция SQL?','options' => ['Внедрение вредоносного SQL-кода в запросы','Атака на сеть','Переполнение буфера','XSS-атака']],
            ['question' => 'Что такое XSS (Cross-Site Scripting)?','options' => ['Внедрение вредоносного скрипта в веб-страницы','Атака на базу данных','DDoS-атака','SQL-инъекция']],
            ['question' => 'Что такое CSRF?','options' => ['Подделка межсайтовых запросов для выполнения действий от имени пользователя','Атака на сервер','Перехват трафика','Подмена DNS']],
            ['question' => 'Как предотвратить XSS-атаки?','options' => ['Экранирование пользовательского ввода и вывода','Использование HTTP','Отключение JavaScript','Использование GET-запросов']],
            ['question' => 'Почему опасна аутентификация без проверки CSRF-токена?','options' => ['Может привести к подделке запросов от имени пользователя','Не опасна','Ускоряет работу','Упрощает разработку']],
        ],
        'Кэширование: Redis, Memcached' => [
            ['question' => 'Какова основная цель кэширования?','options' => ['Ускорение доступа к часто используемым данным','Шифрование данных','Сжатие данных','Резервное копирование']],
            ['question' => 'Что такое кэш в контексте веб-приложений?','options' => ['Временное хранилище данных для ускорения доступа','База данных','Файловая система','Сетевой протокол']],
            ['question' => 'Какой паттерн используется для работы с кэшем?','options' => ['Cache-Aside (Lazy Loading)','Singleton','Observer','Factory']],
            ['question' => 'Что такое TTL в контексте кэширования?','options' => ['Time To Live — время жизни записи в кэше','Total Traffic Limit','Transaction Log','Thread Pool Limit']],
            ['question' => 'Какое главное отличие Redis от Memcached?','options' => ['Redis поддерживает больше типов данных и персистентность','Memcached быстрее','Redis не поддерживает кластеризацию','Memcached поддерживает транзакции']],
        ],
        'Очереди: RabbitMQ, Kafka' => [
            ['question' => 'Что такое очередь сообщений?','options' => ['Механизм асинхронного обмена сообщениями между компонентами','База данных','Файловая система','Протокол шифрования']],
            ['question' => 'Что такое producer в контексте очередей?','options' => ['Отправитель сообщений в очередь','Потребитель сообщений','Сервер очереди','Клиент']],
            ['question' => 'Какой протокол использует RabbitMQ?','options' => ['AMQP','HTTP','FTP','SMTP']],
            ['question' => 'Что такое consumer?','options' => ['Потребитель, обрабатывающий сообщения из очереди','Отправитель сообщений','Сервер очереди','Очередь']],
            ['question' => 'Какое главное преимущество Kafka перед RabbitMQ?','options' => ['Возможность воспроизведения сообщений и хранения истории','Простота настройки','Низкое потребление памяти','Отсутствие зависимости']],
        ],
        'Rate Limiting и Throttling' => [
            ['question' => 'Зачем нужен rate limiting?','options' => ['Ограничение количества запросов для защиты от злоупотреблений','Ускорение работы сервера','Сжатие данных','Шифрование трафика']],
            ['question' => 'Что такое throttling?','options' => ['Ограничение скорости обработки запросов','Полная блокировка запросов','Ускорение обработки','Кэширование данных']],
            ['question' => 'Какой алгоритм используется для rate limiting?','options' => ['Token Bucket','Binary Search','Quick Sort','BFS']],
            ['question' => 'Что вернёт сервер при превышении лимита запросов?','options' => ['429 Too Many Requests','200 OK','500 Internal Server Error','404 Not Found']],
            ['question' => 'Где лучше реализовать rate limiting?','options' => ['На уровне API-шлюза или middleware','На уровне базы данных','На уровне фронтенда','На уровне DNS']],
        ],
        'Тестирование: Unit, Integration' => [
            ['question' => 'Что такое unit-тест?','options' => ['Тест отдельного модуля или функции в изоляции','Тест всего приложения','Тест производительности','Тест безопасности']],
            ['question' => 'Какой фреймворк используется для unit-тестов в PHP?','options' => ['PHPUnit','Django','Express','Gin']],
            ['question' => 'Что такое мок (mock) в тестировании?','options' => ['Объект, имитирующий поведение реального компонента','Реальный компонент системы','Файл логов','Конфигурационный файл']],
            ['question' => 'Что такое интеграционный тест?','options' => ['Тест взаимодействия нескольких компонентов системы','Тест одной функции','Тест производительности','Тест нагрузки']],
            ['question' => 'Какой принцип помогает написанию тестируемого кода?','options' => ['Принцип единственной ответственности','Принцип DRY','Принцип KISS','Все варианты верны']],
        ],
        'Логирование и мониторинг' => [
            ['question' => 'Зачем нужно логирование?','options' => ['Для записи событий и отладки приложений','Для шифрования данных','Для сжатия файлов','Для маршрутизации трафика']],
            ['question' => 'Что такое уровень логирования?','options' => ['Классификация важности события: debug, info, warning, error, critical','Формат лога','Расширение файла','Протокол передачи']],
            ['question' => 'Какой инструмент используется для визуализации метрик?','options' => ['Grafana','Git','Docker','Nginx']],
            ['question' => 'Что такое APM?','options' => ['Application Performance Monitoring — мониторинг производительности приложений','Автоматическое управление памятью','Протокол аутентификации','Метод тестирования']],
            ['question' => 'Где лучше хранить логи в продакшене?','options' => ['В централизованной системе типа ELK или Loki','В локальных файлах на сервере','В базе данных приложения','В оперативной памяти']],
        ],
        'Docker основы' => [
            ['question' => 'Что такое Docker-контейнер?','options' => ['Изолированный экземпляр приложения с зависимостями','Виртуальная машина','Файловая система','Сетевой протокол']],
            ['question' => 'Что такое Dockerfile?','options' => ['Файл с инструкциями для сборки Docker-образа','Конфигурация сети','Файл данных','Скрипт деплоя']],
            ['question' => 'Какой командой запускается Docker-контейнер?','options' => ['docker run','docker start','docker build','docker pull']],
            ['question' => 'Что такое Docker Compose?','options' => ['Инструмент для управления многоконтейнерными приложениями','Виртуальная машина','База данных','Сетевой протокол']],
            ['question' => 'Что такое Docker-образ (image)?','options' => ['Шаблон для создания контейнера','Запущенный контейнер','Файл конфигурации','Лог приложения']],
        ],
        'CI/CD пайплайны' => [
            ['question' => 'Что такое CI (Continuous Integration)?','options' => ['Практика частой интеграции кода в основную ветку с автоматическим тестированием','Ручное тестирование','Деплой на продакшен','Мониторинг серверов']],
            ['question' => 'Что такое CD (Continuous Deployment)?','options' => ['Автоматический деплой изменений в production без ручного вмешательства','Компиляция кода','Резервное копирование','Настройка сервера']],
            ['question' => 'Какой инструмент используется для автоматизации CI/CD?','options' => ['GitHub Actions','Git','Docker','Nginx']],
            ['question' => 'Что такое pipeline в CI/CD?','options' => ['Последовательность шагов для сборки, тестирования и деплоя','База данных','Файловая система','Сетевой протокол']],
            ['question' => 'Какое преимущество даёт CI/CD?','options' => ['Ускорение выпуска обновлений и снижение рисков','Увеличение стоимости разработки','Ручное тестирование','Замедление процессов']],
        ],
    
        ];
    }

    private function getBackendExamData(): array
    {
        return [
        'Какой HTTP-статус код означает "Unauthorized"?' => ['401','403','404','500'],
        'Какой метод HTTP используется для удаления ресурса?' => ['DELETE','GET','PUT','PATCH'],
        'Что такое CORS в контексте веб-разработки?' => ['Политика ограничения кросс-доменных запросов','Протокол шифрования','Метод сжатия данных','Тип базы данных'],
        'Какой HTTP-метод является идемпотентным помимо POST?' => ['PUT','GET','DELETE','Все варианты верны'],
        'Какой порт используется по умолчанию для HTTP?' => ['80','443','8080','3000'],
        'Что делает заголовок Content-Type?' => ['Указывает формат данных в теле запроса или ответа','Определяет авторизацию','Управляет кэшированием','Задаёт язык ответа'],
        'Какой код ответа означает "Forbidden"?' => ['403','401','404','400'],
        'Что такое URL-encode?' => ['Кодирование спецсимволов в URL в формат %XX','Декодирование URL','Создание ссылок','Маршрутизация запросов'],
        'Какой оператор SQL используется для удаления таблицы?' => ['DROP TABLE','DELETE TABLE','REMOVE TABLE','TRUNCATE TABLE'],
        'Что такое подзапрос (subquery) в SQL?' => ['Запрос, вложенный внутри другого запроса','Соединение таблиц','Индекс','Представление'],
        'Какой тип данных в SQL хранит большое количество текста?' => ['TEXT','VARCHAR','CHAR','INT'],
        'Что такое транзакция в базе данных?' => ['Логическая единица работы, которая должна быть выполнена целиком','Тип индекса','Формат данных','Протокол'],
        'Какой метод Python используется для чтения файла?' => ['open() с модом r','read_file()','file_get()','load()'],
        'Что такое декоратор в Python?' => ['Функция, модифицирующая поведение другой функции','Класс','Модуль','Переменная'],
        'Какой ключевое слово используется для создания класса в Python?' => ['class','def','struct','type'],
        'Что такое list comprehension в Python?' => ['Способ создания списков с помощью выражения','Цикл','Условие','Функция'],
        'Какой метод Go используется для запуска горутины?' => ['go()','start()','run()','async()'],
        'Что такое interface в Go?' => ['Набор методов, определяющий поведение типа','Класс','Переменная','Константа'],
        'Какой оператор используется для присваивания значений в Go?' => [':=','=','==','=>'],
        'Что такое указатель (pointer) в Go?' => ['Переменная, хранящая адрес другой переменной','Тип данных','Оператор','Ключевое слово'],
        'Какой HTTP-метод используется для получения информации о ресурсе?' => ['HEAD','GET','OPTIONS','TRACE'],
        'Что такое ETag в HTTP?' => ['Идентификатор версии ресурса для условных запросов','Тип данных','Протокол','Заголовок авторизации'],
        'Какой код ответа означает "Moved Permanently"?' => ['301','302','304','307'],
        'Что такое keep-alive в HTTP?' => ['Опция для повторного использования TCP-соединений','Тип шифрования','Метод сжатия','Протокол'],
        'Какой уровень изоляции транзакций самый строгий?' => ['SERIALIZABLE','READ UNCOMMITTED','READ COMMITTED','REPEATABLE READ'],
        'Что такое материализованное представление (materialized view)?' => ['Представление с физически сохранёнными данными','Индекс','Триггер','Процедура'],
        'Какой алгоритм хеширования генерирует 256-битный хеш?' => ['SHA-256','MD5','SHA-1','bcrypt'],
        'Что такое session fixation?' => ['Атака, при которой атакующий фиксирует ID сессии жертвы','Тип шифрования','Метод авторизации','Протокол'],
        'Какой заголовок HTTP предотвращает clickjacking?' => ['X-Frame-Options','X-Content-Type-Options','X-XSS-Protection','Strict-Transport-Security'],
        'Что такое подмена DNS (DNS spoofing)?' => ['Подмена IP-адреса домена в DNS-ответе','DDoS-атака','SQL-инъекция','XSS-атака'],
    
        ];
    }

    private function getFullstackQuizData(): array
    {
        return [
            'Как работает интернет' => [
                ['question' => 'Какой протокол используется для передачи веб-страниц?', 'options' => ['HTTP/HTTPS', 'FTP', 'SMTP', 'SSH']],
                ['question' => 'Что такое DNS?', 'options' => ['Система доменных имён', 'Динамическая серверная сеть', 'Протокол передачи данных', 'Сервис шифрования']],
                ['question' => 'Какой порт по умолчанию используется HTTPS?', 'options' => ['443', '80', '8080', '21']],
                ['question' => 'Что такое IP-адрес?', 'options' => ['Уникальный адрес устройства в сети', 'Имя компьютера', 'Пароль для доступа к сети', 'Тип подключения']],
                ['question' => 'Какой слой модели OSI отвечает за маршрутизацию?', 'options' => ['Сетевой', 'Канальный', 'Транспортный', 'Прикладной']],
            ],
            'Терминал и CLI' => [
                ['question' => 'Какой командой отображается содержимое директории в Windows?', 'options' => ['dir', 'ls', 'pwd', 'cd']],
                ['question' => 'Что делает команда cd ..?', 'options' => ['Переходит в родительскую директорию', 'Переходит в корневую директорию', 'Создаёт новую директорию', 'Удаляет файл']],
                ['question' => 'Какой командой очищает экран терминала?', 'options' => ['clear', 'cls', 'exit', 'reset']],
                ['question' => 'Что такое CLI?', 'options' => ['Командная строка для взаимодействия с ОС', 'Графический интерфейс', 'Браузер для работы с сетью', 'Редактор кода']],
                ['question' => 'Какой командой выводится текущий путь в Linux?', 'options' => ['pwd', 'ls', 'dir', 'path']],
            ],
            'Git Основы' => [
                ['question' => 'Какой командой создаётся новый коммит в Git?', 'options' => ['git commit', 'git push', 'git add', 'git init']],
                ['question' => 'Что такое ветка (branch) в Git?', 'options' => ['Отдельная линия разработки', 'Копия репозитория', 'Файл конфигурации', 'Результат сравнения версий']],
                ['question' => 'Какой командой загружается код на удалённый репозиторий?', 'options' => ['git push', 'git pull', 'git fetch', 'git clone']],
                ['question' => 'Что делает команда git pull?', 'options' => ['Загружает изменения из удалённого репозитория', 'Отправляет изменения в удалённый репозиторий', 'Создаёт новую ветку', 'Удаляет файлы']],
                ['question' => 'Какой файл Git игнорирует автоматически?', 'options' => ['.gitignore', '.env', 'package.json', 'README.md']],
            ],
            'HTML/CSS Основы' => [
                ['question' => 'Какой тег используется для создания абзаца в HTML?', 'options' => ['p', 'div', 'span', 'br']],
                ['question' => 'Что делает свойство display: flex?', 'options' => ['Делает элемент гибким контейнером', 'Скрывает элемент', 'Добавляет тень', 'Меняет цвет фона']],
                ['question' => 'Как подключается внешний CSS файл?', 'options' => ['link тег с атрибутом rel="stylesheet"', 'style тег', 'css тег', 'link тег с атрибутом type']],
                ['question' => 'Какой CSS селектор применяет стиль к элементу с id?', 'options' => ['#id', '.class', 'tag', '*']],
                ['question' => 'Что такое box model в CSS?', 'options' => ['Модель отображения элемента с отступами и рамками', 'Модель данных таблицы', 'Способ центрирования', 'Тип позиционирования']],
            ],
            'JavaScript Основы' => [
                ['question' => 'Как объявляется переменная с блочной областью видимости?', 'options' => ['let', 'var', 'const', 'function']],
                ['question' => 'Что возвращает оператор typeof null?', 'options' => ['object', 'null', 'undefined', 'boolean']],
                ['question' => 'Какой метод добавляет элемент в конец массива?', 'options' => ['push', 'pop', 'shift', 'unshift']],
                ['question' => 'Что такое замыкание (closure)?', 'options' => ['Функция с доступом к внешней области видимости', 'Тип цикла', 'Способ работы с DOM', 'Промис для асинхронности']],
                ['question' => 'Какой метод преобразует JSON строку в объект?', 'options' => ['JSON.parse', 'JSON.stringify', 'JSON.convert', 'JSON.toObject']],
            ],
            'React / Vue / Angular' => [
                ['question' => 'Что такое компонент в React?', 'options' => ['Переиспользуемый элемент интерфейса', 'Стиль для элемента', 'Файл изображения', 'Функция для работы с API']],
                ['question' => 'Какой хук используется для побочных эффектов в React?', 'options' => ['useEffect', 'useState', 'useContext', 'useRef']],
                ['question' => 'Что такое Vue Router?', 'options' => ['Маршрутизатор для навигации', 'Состояние приложения', 'HTTP клиент', 'Библиотека стилей']],
                ['question' => 'Как Angular обрабатывает двустороннюю привязку данных?', 'options' => ['ngModel', 'ngBind', 'ngData', 'ngConnect']],
                ['question' => 'Что такое Virtual DOM?', 'options' => ['Виртуальное представление DOM для оптимизации', 'Браузерный API', 'Способ шифрования', 'Тип базы данных']],
            ],
            'Node.js и npm' => [
                ['question' => 'Что такое Node.js?', 'options' => ['Среда выполнения JavaScript на сервере', 'Браузер', 'База данных', 'Редактор кода']],
                ['question' => 'Какой файл описывает зависимости проекта в Node.js?', 'options' => ['package.json', 'node_modules.json', 'dependencies.json', 'npm.config']],
                ['question' => 'Какой командой устанавливаются зависимости?', 'options' => ['npm install', 'npm start', 'npm run', 'npm create']],
                ['question' => 'Что такое Express.js?', 'options' => ['Веб-фреймворк для Node.js', 'База данных', 'Фронтенд-библиотека', 'Тестовый фреймворк']],
                ['question' => 'Какой модуль используется для работы с файловой системой?', 'options' => ['fs', 'path', 'http', 'url']],
            ],
            'TypeScript' => [
                ['question' => 'Что такое TypeScript?', 'options' => ['Надмножество JavaScript с типами', 'Фреймворк для фронтенда', 'База данных', 'Тестовый фреймворк']],
                ['question' => 'Как объявляется интерфейс в TypeScript?', 'options' => ['interface', 'type', 'class', 'struct']],
                ['question' => 'Что такое any в TypeScript?', 'options' => ['Тип, принимающий любое значение', 'Пустой тип', 'Тип массива', 'Тип функции']],
                ['question' => 'Как указывается тип возвращаемого значения функции?', 'options' => ['после скобок: тип', 'перед функцией', 'внутри функции', 'после ключевого слова return']],
                ['question' => 'Что такое generics в TypeScript?', 'options' => ['Параметризованные типы', 'Обобщённые функции', 'Типы для массивов', 'Способ наследования']],
            ],
            'REST API проектирование' => [
                ['question' => 'Какой HTTP метод используется для обновления ресурса?', 'options' => ['PUT', 'GET', 'DELETE', 'POST']],
                ['question' => 'Что такое REST?', 'options' => ['Архитектурный стиль для API', 'Протокол передачи данных', 'База данных', 'Язык программирования']],
                ['question' => 'Какой код возвращается при успешном создании ресурса?', 'options' => ['201 Created', '200 OK', '404 Not Found', '500 Server Error']],
                ['question' => 'Что такое эндпоинт?', 'options' => ['URL-адрес для доступа к ресурсу', 'Тип данных', 'Формат ответа', 'Способ аутентификации']],
                ['question' => 'Какой HTTP метод используется для удаления ресурса?', 'options' => ['DELETE', 'GET', 'POST', 'PATCH']],
            ],
            'GraphQL' => [
                ['question' => 'Что такое GraphQL?', 'options' => ['Язык запросов для API', 'База данных', 'Фреймворк', 'Протокол передачи файлов']],
                ['question' => 'Какой тип операции используется для получения данных в GraphQL?', 'options' => ['query', 'mutation', 'subscription', 'schema']],
                ['question' => 'Что такое schema в GraphQL?', 'options' => ['Описание структуры данных API', 'Файл стилей', 'Конфигурация сервера', 'База данных']],
                ['question' => 'Какой тип операции используется для изменения данных?', 'options' => ['mutation', 'query', 'subscription', 'fragment']],
                ['question' => 'Что такое resolver в GraphQL?', 'options' => ['Функция, возвращающая данные для поля', 'Тип данных', 'Клиент API', 'Способ аутентификации']],
            ],
            'SQL: PostgreSQL / MySQL' => [
                ['question' => 'Какой оператор используется для выборки данных в SQL?', 'options' => ['SELECT', 'INSERT', 'UPDATE', 'DELETE']],
                ['question' => 'Что такое первичный ключ (primary key)?', 'options' => ['Уникальный идентификатор строки таблицы', 'Внешний ключ', 'Индекс таблицы', 'Тип данных']],
                ['question' => 'Какой оператор используется для фильтрации результатов?', 'options' => ['WHERE', 'SELECT', 'FROM', 'GROUP BY']],
                ['question' => 'Что такое JOIN в SQL?', 'options' => ['Объединение данных из нескольких таблиц', 'Создание новой таблицы', 'Удаление данных', 'Сортировка результатов']],
                ['question' => 'Какой тип данных используется для хранения текста?', 'options' => ['VARCHAR', 'INT', 'BOOLEAN', 'DATE']],
            ],
            'NoSQL: MongoDB / Redis' => [
                ['question' => 'Что такое MongoDB?', 'options' => ['Документоориентированная NoSQL база данных', 'Реляционная база данных', 'Кэш', 'Файловая система']],
                ['question' => 'Какой формат данных использует MongoDB по умолчанию?', 'options' => ['BSON', 'JSON', 'XML', 'CSV']],
                ['question' => 'Что такое Redis?', 'options' => ['Кэширующее хранилище данных в памяти', 'Документоориентированная БД', 'Реляционная БД', 'Файловый сервер']],
                ['question' => 'Какой командой Redis устанавливает значение ключа?', 'options' => ['SET', 'GET', 'DEL', 'KEYS']],
                ['question' => 'Что такое индекс в MongoDB?', 'options' => ['Структура для ускорения запросов', 'Тип документа', 'Коллекция', 'Поле документа']],
            ],
            'ORM: Eloquent / Sequelize' => [
                ['question' => 'Что такое ORM?', 'options' => ['Объектно-реляционное отображение для работы с БД', 'Способ шифрования', 'Формат данных', 'Протокол передачи']],
                ['question' => 'Какой метод Eloquent использует для поиска по ID?', 'options' => ['find', 'where', 'get', 'first']],
                ['question' => 'Что такое миграция в Sequelize?', 'options' => ['Скрипт для изменения структуры БД', 'Файл стилей', 'Метод аутентификации', 'Тип модели']],
                ['question' => 'Какой метод Sequelize создаёт новую запись?', 'options' => ['create', 'save', 'insert', 'add']],
                ['question' => 'Что такое relationship в ORM?', 'options' => ['Связь между моделями', 'Тип данных', 'Функция валидации', 'Способ кэширования']],
            ],
            'Аутентификация: JWT, OAuth' => [
                ['question' => 'Что такое JWT?', 'options' => ['JSON Web Token для аутентификации', 'Тип шифрования', 'Протокол передачи', 'База данных']],
                ['question' => 'Из скольких частей состоит JWT?', 'options' => ['Трёх: header, payload, signature', 'Двух: header, payload', 'Одной: payload', 'Четырёх: header, payload, signature, key']],
                ['question' => 'Что такое OAuth?', 'options' => ['Протокол авторизации для доступа к ресурсам', 'Тип JWT токена', 'Шифрование паролей', 'Формат данных']],
                ['question' => 'Какой HTTP заголовок используется для передачи JWT?', 'options' => ['Authorization: Bearer token', 'X-Auth-Token', 'Cookie', 'Access-Token']],
                ['question' => 'Что такое refresh token?', 'options' => ['Токен для обновления access token', 'Основной токен аутентификации', 'Пароль пользователя', 'Ключ шифрования']],
            ],
            'Кэширование: Redis' => [
                ['question' => 'Зачем используется кэширование?', 'options' => ['Для ускорения доступа к часто запрашиваемым данным', 'Для шифрования данных', 'Для сжатия файлов', 'Для управления пользователями']],
                ['question' => 'Какой тип данных Redis поддерживает для списков?', 'options' => ['List', 'Set', 'Hash', 'String']],
                ['question' => 'Что такое TTL в контексте Redis?', 'options' => ['Время жизни ключа', 'Тип данных', 'Размер памяти', 'Количество подключений']],
                ['question' => 'Какой командой Redis удаляет ключ?', 'options' => ['DEL', 'GET', 'SET', 'EXISTS']],
                ['question' => 'Что такое кэш стратегия "write-through"?', 'options' => ['Запись данных одновременно в кэш и БД', 'Запись только в кэш', 'Запись только в БД', 'Удаление из кэша']],
            ],
            'Тестирование' => [
                ['question' => 'Что такое unit-тест?', 'options' => ['Тест отдельного модуля или функции', 'Тест всего приложения', 'Тест пользовательского интерфейса', 'Тест производительности']],
                ['question' => 'Какой фреймворк используется для тестирования в Jest?', 'options' => ['describe/it/test', 'test/case/assert', 'check/verify/validate', 'run/exec/evaluate']],
                ['question' => 'Что такое mock в тестировании?', 'options' => ['Имитация зависимостей для тестирования', 'Реальный объект', 'Тестовые данные', 'Результат теста']],
                ['question' => 'Какой тип теста проверяет интеграцию модулей?', 'options' => ['Интеграционный тест', 'Unit-тест', 'E2E тест', 'Смок-тест']],
                ['question' => 'Что такое TDD?', 'options' => ['Test Driven Development - разработка через тесты', 'Test Data Distribution', 'Total Debug Detection', 'Test Deployment Design']],
            ],
            'Docker основы' => [
                ['question' => 'Что такое Docker?', 'options' => ['Платформа для контейнеризации приложений', 'Операционная система', 'База данных', 'Фреймворк']],
                ['question' => 'Что такое Dockerfile?', 'options' => ['Файл для сборки Docker-образа', 'Конфигурация контейнера', 'Скрипт запуска', 'Файл зависимостей']],
                ['question' => 'Какой командой создаётся Docker-образ?', 'options' => ['docker build', 'docker run', 'docker pull', 'docker create']],
                ['question' => 'Что такое Docker Hub?', 'options' => ['Реестр Docker-образов', 'Фреймворк для контейнеров', 'Инструмент мониторинга', 'Способ шифрования']],
                ['question' => 'Какой командой запускается контейнер?', 'options' => ['docker run', 'docker build', 'docker start', 'docker exec']],
            ],
            'CI/CD' => [
                ['question' => 'Что такое CI/CD?', 'options' => ['Непрерывная интеграция и доставка', 'Контроль версий', 'Мониторинг приложений', 'Управление пользователями']],
                ['question' => 'Какой инструмент автоматизирует сборку проектов в CI/CD?', 'options' => ['Jenkins', 'VS Code', 'Docker', 'Redis']],
                ['question' => 'Что такое pipeline в CI/CD?', 'options' => ['Последовательность шагов автоматизации', 'Файл конфигурации', 'Репозиторий кода', 'Сервер приложений']],
                ['question' => 'Какой формат конфигурации используется в GitHub Actions?', 'options' => ['YAML', 'JSON', 'XML', 'INI']],
                ['question' => 'Что такое deploy в CI/CD?', 'options' => ['Развёртывание приложения в продакшен', 'Тестирование кода', 'Коммит изменений', 'Код ревью']],
            ],
            'Деплой: VPS / PaaS' => [
                ['question' => 'Что такое VPS?', 'options' => ['Виртуальный приватный сервер', 'Виртуальная файловая система', 'Виртуальный протокол', 'Виртуальный процессор']],
                ['question' => 'Что такое PaaS?', 'options' => ['Платформа как сервис', 'Программа как сервис', 'Протокол как сервис', 'Память как сервис']],
                ['question' => 'Какой сервис предоставляет AWS для хранения файлов?', 'options' => ['S3', 'EC2', 'RDS', 'Lambda']],
                ['question' => 'Что такое Heroku?', 'options' => ['PaaS платформа для деплоя приложений', 'VPS провайдер', 'База данных', 'Фреймворк']],
                ['question' => 'Какой инструмент используется для автоматизации деплоя?', 'options' => ['Ansible', 'VS Code', 'npm', 'Git']],
            ],
            'Мониторинг и логирование' => [
                ['question' => 'Зачем нужен мониторинг?', 'options' => ['Для отслеживания состояния приложений', 'Для шифрования данных', 'Для сжатия файлов', 'Для управления пользователями']],
                ['question' => 'Что такое Grafana?', 'options' => ['Инструмент визуализации метрик', 'База данных', 'Фреймворк', 'Редактор кода']],
                ['question' => 'Какой формат логов самый распространённый?', 'options' => ['JSON', 'XML', 'CSV', 'TXT']],
                ['question' => 'Что такое Prometheus?', 'options' => ['Система сбора и хранения метрик', 'Фреймворк для тестирования', 'База данных', 'Веб-сервер']],
                ['question' => 'Что такое alerting в мониторинге?', 'options' => ['Система оповещений о проблемах', 'Визуализация данных', 'Сбор логов', 'Настройка серверов']],
            ],
        
        ];
    }

    private function getFullstackExamData(): array
    {
        return [
            'Какой HTTP метод идемпотентен и безопасен?' => ['GET', 'POST', 'PUT', 'DELETE'],
            'Какой статус код означает "Unauthorized"?' => ['401', '403', '404', '500'],
            'Что такое CORS?' => ['Cross-Origin Resource Sharing', 'Cache Origin Request System', 'Core Operating Runtime Service', 'Code Optimization and Refactoring System'],
            'Какой HTML тег создаёт нумерованный список?' => ['ol', 'ul', 'li', 'dl'],
            'Что такое Promise в JavaScript?' => ['Объект, представляющий результат асинхронной операции', 'Тип переменной', 'Функция обратного вызова', 'Метод массива'],
            'Какой CSS свойство управляет прозрачностью?' => ['opacity', 'visibility', 'display', 'transparent'],
            'Какой пакетный менеджер используется в Node.js?' => ['npm', 'pip', 'gem', 'composer'],
            'Что такое RESTful API?' => ['API, следующее принципам REST', 'API с GraphQL', 'API для файловой системы', 'API для работы с БД'],
            'Какой SQL оператор объединяет таблицы?' => ['JOIN', 'MERGE', 'UNION', 'COMBINE'],
            'Что такое ORM?' => ['Объектно-реляционное отображение', 'Операционная система', 'Формат данных', 'Протокол сети'],
            'Какой тип токена JWT передаётся в заголовке?' => ['Bearer', 'Basic', 'Token', 'Auth'],
            'Что такое виртуальный DOM?' => ['Легковесное представление реального DOM', 'Браузерный API', 'Фреймворк', 'Тип данных'],
            'Какой командой Git создаётся ветка?' => ['git branch', 'git checkout', 'git switch', 'git merge'],
            'Что такое PostgreSQL?' => ['Реляционная СУБД', 'NoSQL база данных', 'Кэширующая система', 'Файловая система'],
            'Какой HTTP метод частично обновляет ресурс?' => ['PATCH', 'GET', 'POST', 'DELETE'],
            'Что такое Docker image?' => ['Шаблон для создания контейнера', 'Файл конфигурации', 'Скрипт запуска', 'Репозиторий'],
            'Какой код означает "Not Found"?' => ['404', '400', '500', '301'],
            'Что такое middleware?' => ['Промежуточный обработчик запросов', 'Функция для шифрования', 'Тип базы данных', 'Формат данных'],
            'Какой формат данных чаще всего используется в REST API?' => ['JSON', 'XML', 'CSV', 'YAML'],
            'Что такое event loop в Node.js?' => ['Механизм обработки асинхронных операций', 'Тип цикла', 'Фреймворк', 'Метод массива'],
            'Какой SQL оператор удаляет данные?' => ['DELETE', 'REMOVE', 'DROP', 'ERASE'],
            'Что такое ESLint?' => ['Инструмент статического анализа JavaScript', 'Фреймворк для тестирования', 'Редактор кода', 'Пакетный менеджер'],
            'Какой CSS единица измерения относительная?' => ['em', 'px', 'cm', 'mm'],
            'Что такое Type Assertion в TypeScript?' => ['Приведение типа в compile-time', 'Рантайм проверка', 'Импорт модуля', 'Экспорт функции'],
            'Какой метод Express.js создаёт GET маршрут?' => ['app.get()', 'app.post()', 'app.put()', 'app.delete()'],
            'Что такое rate limiting?' => ['Ограничение количества запросов', 'Ограничение скорости сети', 'Ограничение памяти', 'Ограничение подключений'],
            'Какой статус код означает "Created"?' => ['201', '200', '204', '202'],
            'Что такое N+1 проблема?' => ['Неэффективные запросы к БД', 'Ошибка типизации', 'Проблема кэширования', 'Ошибка синтаксиса'],
            'Какой инструмент управляет версиями зависимостей?' => ['package-lock.json', 'package.json', 'node_modules', '.gitignore'],
            'Что такое server-side rendering?' => ['Рендеринг HTML на сервере', 'Рендеринг на клиенте', 'Кэширование данных', 'Оптимизация изображений'],
        
        ];
    }

    private function getDevOpsQuizData(): array
    {
        return [
            'Linux основы' => [
                ['question' => 'Какой командой отображается содержимое директории в Linux?', 'options' => ['ls', 'dir', 'pwd', 'cd']],
                ['question' => 'Какой командой меняется права доступа к файлу?', 'options' => ['chmod', 'chown', 'chgrp', 'chperm']],
                ['question' => 'Что такое PID в Linux?', 'options' => ['Идентификатор процесса', 'Путь к файлу', 'Имя пользователя', 'Тип файла']],
                ['question' => 'Какой командой завершается процесс в Linux?', 'options' => ['kill', 'stop', 'end', 'terminate']],
                ['question' => 'Что такое symlinks в Linux?', 'options' => ['Символические ссылки', 'Системные файлы', 'Скрипты запуска', 'Конфигурационные файлы']],
            ],
            'Сети и DNS' => [
                ['question' => 'Что такое DNS?', 'options' => ['Система доменных имён', 'Протокол передачи файлов', 'Способ шифрования', 'Тип подключения']],
                ['question' => 'Какой порт используется по умолчанию для HTTP?', 'options' => ['80', '443', '8080', '21']],
                ['question' => 'Что такое A-запись в DNS?', 'options' => ['Соответствие домена IPv4 адресу', 'IPv6 адрес', 'Мail сервер', 'Субдомен']],
                ['question' => 'Какой протокол используется для безопасного доступа к сайтам?', 'options' => ['HTTPS', 'HTTP', 'FTP', 'SMTP']],
                ['question' => 'Что такое TTL в DNS?', 'options' => ['Время жизни записи в кэше', 'Тип записи', 'Размер пакета', 'Скорость сети']],
            ],
            'Терминал и Bash' => [
                ['question' => 'Что такое Bash?', 'options' => ['Командная оболочка для Linux', 'Браузер', 'Редактор кода', 'Фреймворк']],
                ['question' => 'Какой оператор перенаправляет вывод в файл?', 'options' => ['>', '>>', '|', '&']],
                ['question' => 'Что такое pipe (|) в Bash?', 'options' => ['Передача вывода одной команды другой', 'Переменная', 'Условный оператор', 'Цикл']],
                ['question' => 'Какой командой выводятся переменные окружения?', 'options' => ['env', 'set', 'printenv', 'export']],
                ['question' => 'Что такое cron в Linux?', 'options' => ['Планировщик задач', 'Файловая система', 'Менеджер пакетов', 'Сервер приложений']],
            ],
            'Git и VCS' => [
                ['question' => 'Что такое VCS?', 'options' => ['Система контроля версий', 'Виртуальная файловая система', 'Способ шифрования', 'Протокол передачи']],
                ['question' => 'Какой командой Git создаётся коммит?', 'options' => ['git commit', 'git push', 'git add', 'git init']],
                ['question' => 'Что такое merge conflict?', 'options' => ['Конфликт при слиянии веток', 'Ошибка компиляции', 'Проблема сети', 'Ошибка типизации']],
                ['question' => 'Какой командой Git отображает историю коммитов?', 'options' => ['git log', 'git history', 'git show', 'git status']],
                ['question' => 'Что такое rebase в Git?', 'options' => ['Перебазирование коммитов', 'Откат изменений', 'Создание ветки', 'Удаление файла']],
            ],
            'Docker основы' => [
                ['question' => 'Что такое Docker контейнер?', 'options' => ['Изолированная среда выполнения приложения', 'Виртуальная машина', 'Файл конфигурации', 'Репозиторий']],
                ['question' => 'Какой файл описывает Docker-образ?', 'options' => ['Dockerfile', 'docker-compose.yml', 'Dockerfile.yml', 'docker.config']],
                ['question' => 'Какой командой удаляются неиспользуемые Docker ресурсы?', 'options' => ['docker system prune', 'docker clean', 'docker remove', 'docker delete']],
                ['question' => 'Что такое Docker volume?', 'options' => ['Постоянное хранилище данных', 'Тип сети', 'Порт контейнера', 'Переменная окружения']],
                ['question' => 'Какой командой просматриваются запущенные контейнеры?', 'options' => ['docker ps', 'docker list', 'docker show', 'docker status']],
            ],
            'Docker Compose' => [
                ['question' => 'Что такое Docker Compose?', 'options' => ['Инструмент для управления мульти-контейнерными приложениями', 'Контейнер', 'Образ', 'Сеть']],
                ['question' => 'Какой файл конфигурации использует Docker Compose?', 'options' => ['docker-compose.yml', 'Dockerfile', 'compose.json', 'docker.yml']],
                ['question' => 'Какой командой запускаются сервисы в Docker Compose?', 'options' => ['docker-compose up', 'docker-compose start', 'docker-compose run', 'docker-compose exec']],
                ['question' => 'Что такое service в Docker Compose?', 'options' => ['Контейнер с определённой ролью', 'Сеть', 'Том', 'Образ']],
                ['question' => 'Какой командой останавливаются сервисы?', 'options' => ['docker-compose down', 'docker-compose stop', 'docker-compose kill', 'docker-compose exit']],
            ],
            'Docker Networking' => [
                ['question' => 'Какой тип сети создёт Docker по умолчанию?', 'options' => ['bridge', 'host', 'none', 'overlay']],
                ['question' => 'Что такое bridge сеть в Docker?', 'options' => ['Сеть для коммуникации контейнеров на одном хосте', 'Сеть для доступа извне', 'Виртуальная сеть для кластеров', 'Сеть для хранения данных']],
                ['question' => 'Какой командой отображаются Docker сети?', 'options' => ['docker network ls', 'docker network list', 'docker network show', 'docker network view']],
                ['question' => 'Что такое port mapping в Docker?', 'options' => ['Сопоставление портов контейнера с портами хоста', 'Тип сети', 'Способ шифрования', 'Настройка DNS']],
                ['question' => 'Какой тип сети нужен для кластера Docker Swarm?', 'options' => ['overlay', 'bridge', 'host', 'none']],
            ],
            'Kubernetes основы' => [
                ['question' => 'Что такое Kubernetes?', 'options' => ['Система оркестрации контейнеров', 'Контейнер', 'Образ Docker', 'Фреймворк']],
                ['question' => 'Что такое kubectl?', 'options' => ['CLI для управления Kubernetes', 'Контейнер', 'Сервис', 'Нода']],
                ['question' => 'Какой файл описывает ресурсы Kubernetes?', 'options' => ['YAML/JSON манифест', 'Dockerfile', 'docker-compose.yml', 'Makefile']],
                ['question' => 'Что такое cluster в Kubernetes?', 'options' => ['Группа нод для управления контейнерами', 'Один контейнер', 'Файл конфигурации', 'Тип сети']],
                ['question' => 'Какой командой выводятся поды в Kubernetes?', 'options' => ['kubectl get pods', 'kubectl list pods', 'kubectl show pods', 'kubectl view pods']],
            ],
            'Pods, Services, Deployments' => [
                ['question' => 'Что такое Pod в Kubernetes?', 'options' => ['Наименьшая единица разворачивания', 'Сервис', 'Кластер', 'Нода']],
                ['question' => 'Что такое Service в Kubernetes?', 'options' => ['Способ доступа к группе подов', 'Тип пода', 'Нода', 'Кластер']],
                ['question' => 'Какой тип Service предоставляет доступ извне кластера?', 'options' => ['LoadBalancer', 'ClusterIP', 'NodePort', 'ExternalName']],
                ['question' => 'Что такое Deployment в Kubernetes?', 'options' => ['Описание желаемого состояния подов', 'Тип сервиса', 'Сеть', 'Хранилище']],
                ['question' => 'Какой командой обновляется Deployment?', 'options' => ['kubectl apply', 'kubectl update', 'kubectl modify', 'kubectl change']],
            ],
            'Helm Charts' => [
                ['question' => 'Что такое Helm Chart?', 'options' => ['Пакет для Kubernetes приложений', 'Docker образ', 'Контейнер', 'Нода']],
                ['question' => 'Какой файл определяет переменные Helm Chart?', 'options' => ['values.yaml', 'config.yaml', 'env.yaml', 'params.yaml']],
                ['question' => 'Какой командой устанавливается Helm Chart?', 'options' => ['helm install', 'helm deploy', 'helm create', 'helm apply']],
                ['question' => 'Что такое release в Helm?', 'options' => ['Экземпляр развёрнутого чарта', 'Версия чарта', 'Тип сервиса', 'Нода']],
                ['question' => 'Какой командой обновляется Helm Chart?', 'options' => ['helm upgrade', 'helm update', 'helm modify', 'helm change']],
            ],
            'CI/CD: GitHub Actions' => [
                ['question' => 'Что такое GitHub Actions?', 'options' => ['Система автоматизации CI/CD', 'Репозиторий', 'CLI инструмент', 'Фреймворк']],
                ['question' => 'Какой файл описывает workflow GitHub Actions?', 'options' => ['YAML файл в .github/workflows', 'Dockerfile', 'Makefile', 'package.json']],
                ['question' => 'Что такое job в GitHub Actions?', 'options' => ['Группа шагов workflow', 'Один шаг', 'Репозиторий', 'Вебхук']],
                ['question' => 'Какой триггер запускает workflow при пуше?', 'options' => ['push', 'pull_request', 'schedule', 'workflow_dispatch']],
                ['question' => 'Что такое secrets в GitHub Actions?', 'options' => ['Хранилище защищённых переменных', 'Тип триггера', 'Шаг workflow', 'Нода']],
            ],
            'CI/CD: GitLab CI / Jenkins' => [
                ['question' => 'Какой файл конфигурации использует GitLab CI?', 'options' => ['.gitlab-ci.yml', '.gitlab.yml', 'gitlab-ci.yaml', '.ci.yml']],
                ['question' => 'Что такое Jenkins Pipeline?', 'options' => ['Скрипт автоматизации сборки и деплоя', 'Репозиторий', 'Контейнер', 'Нода']],
                ['question' => 'Что такое stage в CI/CD pipeline?', 'options' => ['Этап выполнения задач', 'Тип сервера', 'Формат данных', 'Протокол']],
                ['question' => 'Какой инструмент используется для автоматизации сборки в Jenkins?', 'options' => ['Jenkinsfile', 'Dockerfile', 'Makefile', '.gitlab-ci.yml']],
                ['question' => 'Что такое runner в GitLab CI?', 'options' => ['Агент для выполнения задач', 'Браузер', 'Контейнер', 'Нода']],
            ],
            'IaC: Terraform' => [
                ['question' => 'Что такое Terraform?', 'options' => ['Инструмент для Infrastructure as Code', 'Контейнер', 'Фреймворк', 'База данных']],
                ['question' => 'Какой файл описывает инфраструктуру в Terraform?', 'options' => ['.tf файл', 'Dockerfile', 'docker-compose.yml', 'Makefile']],
                ['question' => 'Что такое state в Terraform?', 'options' => ['Текущее состояние инфраструктуры', 'Конфигурация сервера', 'Тип ресурса', 'Провайдер']],
                ['question' => 'Какой командой применяется Terraform конфигурация?', 'options' => ['terraform apply', 'terraform run', 'terraform deploy', 'terraform execute']],
                ['question' => 'Что такое provider в Terraform?', 'options' => ['Плагин для работы с облачными API', 'Тип ресурса', 'Переменная', 'Модуль']],
            ],
            'IaC: Ansible' => [
                ['question' => 'Что такое Ansible?', 'options' => ['Инструмент для управления конфигурацией', 'Контейнер', 'Облачный провайдер', 'Фреймворк']],
                ['question' => 'Что такое playbook в Ansible?', 'options' => ['Скрипт автоматизации задач', 'Контейнер', 'Репозиторий', 'Нода']],
                ['question' => 'Что такое inventory в Ansible?', 'options' => ['Список управляемых хостов', 'Тип playbook', 'Переменная', 'Модуль']],
                ['question' => 'Какой модуль Ansible управляет пакетами в Ubuntu?', 'options' => ['apt', 'yum', 'pip', 'npm']],
                ['question' => 'Что такое role в Ansible?', 'options' => ['Структурированный набор задач', 'Тип хоста', 'Переменная', 'Модуль']],
            ],
            'Облачные провайдеры: AWS / GCP / Azure' => [
                ['question' => 'Что такое AWS EC2?', 'options' => ['Виртуальные серверы', 'Хранилище файлов', 'База данных', 'Сеть']],
                ['question' => 'Что такое S3 в AWS?', 'options' => ['Объектное хранилище', 'Виртуальный сервер', 'База данных', 'Кэш']],
                ['question' => 'Какой сервис Azure аналогичен EC2?', 'options' => ['Virtual Machines', 'Blob Storage', 'Azure SQL', 'Azure Functions']],
                ['question' => 'Что такое GCP Cloud Functions?', 'options' => ['Серверные функции', 'Виртуальные серверы', 'Хранилище', 'Сеть']],
                ['question' => 'Какой сервис AWS используется для балансировки нагрузки?', 'options' => ['Elastic Load Balancing', 'EC2', 'S3', 'RDS']],
            ],
            'Мониторинг: Prometheus + Grafana' => [
                ['question' => 'Что такое Prometheus?', 'options' => ['Система мониторинга и алертинга', 'Фреймворк', 'Контейнер', 'База данных']],
                ['question' => 'Что такое Grafana?', 'options' => ['Инструмент визуализации метрик', 'Система сбора метрик', 'Контейнер', 'Нода']],
                ['question' => 'Что такое query language в Prometheus?', 'options' => ['PromQL', 'SQL', 'GraphQL', 'CSS']],
                ['question' => 'Какой формат метрик использует Prometheus?', 'options' => ['Text-based exposition format', 'JSON', 'XML', 'YAML']],
                ['question' => 'Что такое alertmanager?', 'options' => ['Компонент для управления алертами', 'Визуализация', 'Сбор метрик', 'Контейнер']],
            ],
            'Логирование: ELK / Loki' => [
                ['question' => 'Что такое ELK стек?', 'options' => ['Elasticsearch, Logstash, Kibana', 'Elastic Load Balancing, Kubernetes', 'Environment Linux Kernel', 'Enterprise Log Keeper']],
                ['question' => 'Что такое Elasticsearch?', 'options' => ['Поисковый движок для логов', 'Фреймворк', 'Контейнер', 'Нода']],
                ['question' => 'Что такое Logstash?', 'options' => ['Инструмент для обработки логов', 'Визуализация', 'Поиск', 'Хранение']],
                ['question' => 'Что такое Loki?', 'options' => ['Система агрегации логов от Grafana', 'Поисковый движок', 'Кэш', 'Нода']],
                ['question' => 'Что такое Kibana?', 'options' => ['Инструмент визуализации логов', 'Обработка логов', 'Хранение логов', 'Поиск логов']],
            ],
            'Трейсинг: Jaeger / Zipkin' => [
                ['question' => 'Что такое distributed tracing?', 'options' => ['Отслеживание запросов через микросервисы', 'Логирование ошибок', 'Мониторинг ресурсов', 'Кэширование']],
                ['question' => 'Что такое Jaeger?', 'options' => ['Система distributed tracing', 'Логирование', 'Мониторинг', 'Кэширование']],
                ['question' => 'Что такое span в трейсинге?', 'options' => ['Единица работы в трейсе', 'Тип лога', 'Метрика', 'Контейнер']],
                ['question' => 'Что такое Zipkin?', 'options' => ['Система трейсинга запросов', 'Логирование', 'Мониторинг', 'База данных']],
                ['question' => 'Какой формат используется для передачи трейсинга?', 'options' => ['OpenTracing / OpenTelemetry', 'JSON', 'XML', 'CSV']],
            ],
            'Nginx / HAProxy' => [
                ['question' => 'Что такое Nginx?', 'options' => ['Веб-сервер и обратный прокси', 'База данных', 'Фреймворк', 'Контейнер']],
                ['question' => 'Что такое HAProxy?', 'options' => ['Балансировщик нагрузки', 'Веб-сервер', 'База данных', 'Фреймворк']],
                ['question' => 'Какой файл конфигурации использует Nginx?', 'options' => ['nginx.conf', 'haproxy.cfg', 'config.yml', 'settings.json']],
                ['question' => 'Что такое upstream в Nginx?', 'options' => ['Группа бэкенд-серверов', 'Тип запроса', 'Переменная', 'Директива']],
                ['question' => 'Какой алгоритм используется в HAProxy для балансировки?', 'options' => ['roundrobin', 'leastconn', 'source', 'uri']],
            ],
            'SSL/TLS и сертификаты' => [
                ['question' => 'Что такое SSL/TLS?', 'options' => ['Протокол шифрования для безопасной связи', 'Тип сервера', 'Формат данных', 'Протокол передачи']],
                ['question' => 'Что такое Let\'s Encrypt?', 'options' => ['Бесплатный центр сертификации', 'Тип сертификата', 'Веб-сервер', 'База данных']],
                ['question' => 'Какой файл содержит приватный ключ?', 'options' => ['.key файл', '.crt файл', '.pem файл', '.pub файл']],
                ['question' => 'Что такое Certificate Authority (CA)?', 'options' => ['Организация, выдающая сертификаты', 'Тип сертификата', 'Протокол', 'Нода']],
                ['question' => 'Какой протокол заменил SSL?', 'options' => ['TLS', 'SSH', 'HTTPS', 'SSLv3']],
            ],
            'Бэкапы и восстановление' => [
                ['question' => 'Зачем нужны бэкапы?', 'options' => ['Для восстановления данных после сбоев', 'Для ускорения сервера', 'Для шифрования', 'Для мониторинга']],
                ['question' => 'Что такое 3-2-1 стратегия бэкапов?', 'options' => ['3 копии, 2 носителя, 1 offsite', '3 сервера, 2 сети, 1 файл', '3 файла, 2 папки, 1 диск', '3 дня, 2 недели, 1 месяц']],
                ['question' => 'Какой инструмент создаёт бэкапы в Linux?', 'options' => ['rsync', 'cp', 'mv', 'tar']],
                ['question' => 'Что такое differential backup?', 'options' => ['Бэкап изменений с момента последнего полного бэкапа', 'Полный бэкап', 'Инкрементальный бэкап', 'Зеркалирование']],
                ['question' => 'Какой формат архивации используется в Linux?', 'options' => ['tar', 'zip', 'rar', '7z']],
            ],
            'Scripting: Bash / Python' => [
                ['question' => 'Какой язык лучше подходит для автоматизации серверных задач?', 'options' => ['Bash', 'JavaScript', 'HTML', 'CSS']],
                ['question' => 'Какой командой Python запускается скрипт?', 'options' => ['python script.py', 'run script.py', 'exec script.py', 'start script.py']],
                ['question' => 'Что такое shebang в Bash скриптах?', 'options' => ['Строка #!/bin/bash', 'Комментарий', 'Переменная', 'Функция']],
                ['question' => 'Какой модуль Python используется для работы с HTTP?', 'options' => ['requests', 'http', 'url', 'web']],
                ['question' => 'Что такое cron job в Bash?', 'options' => ['Задача по расписанию', 'Функция', 'Переменная', 'Массив']],
            ],
        
        ];
    }

    private function getDevOpsExamData(): array
    {
        return [
            'Какой командой Linux создаётся новый пользователь?' => ['useradd', 'adduser', 'createuser', 'newuser'],
            'Какой порт по умолчанию использует SSH?' => ['22', '21', '80', '443'],
            'Что такое default gateway?' => ['Маршрутизатор для выхода в другую сеть', 'Главная страница', 'DNS сервер', 'Прокси сервер'],
            'Какой командой проверяется сетевое соединение в Linux?' => ['ping', 'curl', 'wget', 'netstat'],
            'Что такое iptables?' => ['Фаервол для управления сетевым трафиком', 'Редактор текста', 'Файловая система', 'Пакетный менеджер'],
            'Какой файл содержит информацию о пользователях в Linux?' => ['/etc/passwd', '/etc/shadow', '/etc/hosts', '/etc/group'],
            'Что такое namespace в Docker?' => ['Изолированная среда для ресурсов', 'Тип сети', 'Переменная', 'Скрипт'],
            'Какой командой Docker выводит логи контейнера?' => ['docker logs', 'docker output', 'docker show', 'docker print'],
            'Что такое k8s?' => ['Сокращение от Kubernetes', 'Тип Docker контейнера', 'Нода', 'Сервис'],
            'Какой YAML поле определяет образ контейнера в Pod манифесте?' => ['image', 'container', 'docker', 'repo'],
            'Что такое ConfigMap в Kubernetes?' => ['Хранилище конфигурационных данных', 'Тип сервиса', 'Нода', 'Сеть'],
            'Какой командой Helm добавляется репозиторий?' => ['helm repo add', 'helm add repo', 'helm install', 'helm pull'],
            'Что такое GitHub Actions marketplace?' => ['Каталог готовых действий', 'Репозиторий', 'Нода', 'Сервер'],
            'Какой тип триггера GitHub Actions запускается по расписанию?' => ['schedule', 'push', 'pull_request', 'workflow_dispatch'],
            'Что такое Jenkins agent?' => ['Машина для выполнения задач pipeline', 'Браузер', 'Контейнер', 'Репозиторий'],
            'Какой файл содержит переменные окружения в GitLab CI?' => ['variables section в .gitlab-ci.yml', '.env файл', 'docker-compose.yml', 'Makefile'],
            'Что такое Terraform module?' => ['Повторно используемый набор ресурсов', 'Тип провайдера', 'Переменная', 'Команда'],
            'Какой командой Terraform инициализируется проект?' => ['terraform init', 'terraform plan', 'terraform apply', 'terraform create'],
            'Что такое Ansible vault?' => ['Шифрование переменных', 'Тип playbook', 'Модуль', 'Роль'],
            'Какой сервис AWS используется для DNS?' => ['Route 53', 'EC2', 'S3', 'RDS'],
            'Что такое CloudFormation в AWS?' => ['Сервис IaC от AWS', 'База данных', 'Хранилище', 'Сеть'],
            'Какой инструмент визуализирует метрики Prometheus?' => ['Grafana', 'Kibana', 'Jaeger', 'ELK'],
            'Что такое метрика в Prometheus?' => ['Метрика', 'Лог', 'Трейс', 'Конфигурация'],
            'Какой Elasticsearch API используется для поиска?' => ['_search', '_find', '_query', '_get'],
            'Что такое Jaeger agent?' => ['Компонент для сбора трейсов', 'Браузер', 'Контейнер', 'Нода'],
            'Какой алгоритм шифрования используется в TLS 1.3?' => ['AES-GCM', 'DES', 'RC4', 'MD5'],
            'Какой командой проверяется SSL сертификат?' => ['openssl s_client', 'sslcheck', 'certutil', 'curl -I'],
            'Что такое rsync?' => ['Инструмент синхронизации файлов', 'Файловая система', 'Бэкап сервис', 'Протокол'],
            'Какой язык scripting чаще всего автоматизирует DevOps задачи?' => ['Bash/Python', 'JavaScript', 'HTML', 'CSS'],
            'Что такое blue-green deployment?' => ['Стратегия деплоя с двумя идентичными средами', 'Тип контейнера', 'Формат бэкапа', 'Алгоритм балансировки'],
        
        ];
    }

    private function getPythonQuizData(): array
    {
        return [
        'Установка Python и pip' => [
            ['question' => 'Какой командой проверяется установленная версия Python?','options' => ['python --version','python -v','python -version','check python']],
            ['question' => 'Что такое pip?','options' => ['Менеджер пакетов Python','Текстовый редактор','Браузер','Операционная система']],
            ['question' => 'Как установить пакет через pip?','options' => ['pip install имя_пакета','pip add имя_пакета','pip get имя_пакета','pip download имя_пакета']],
            ['question' => 'Какой файл указывает на корень проекта Python?','options' => ['__init__.py','main.py','root.py','config.py']],
            ['question' => 'Где можно скачать Python официально?','options' => ['python.org','pypi.org','github.com','python.com']],
        ],
        'Терминал и CLI' => [
            ['question' => 'Какой командой отображается текущая директория?','options' => ['pwd','dir','where','path']],
            ['question' => 'Как запустить Python скрипт из терминала?','options' => ['python script.py','run script.py','execute script.py','start script.py']],
            ['question' => 'Что делает команда cd?','options' => ['Изменяет текущую директорию','Показывает файлы','Удаляет файлы','Копирует файлы']],
            ['question' => 'Какой оператор перенаправляет вывод в файл?','options' => ['>','>>','|','&']],
            ['question' => 'Как вывести список файлов в директории на Windows?','options' => ['dir','ls','list','files']],
        ],
        'Переменные и типы' => [
            ['question' => 'Как объявить переменную в Python?','options' => ['x = 10','var x = 10','int x = 10','let x = 10']],
            ['question' => 'Какой тип данных у переменной 3.14?','options' => ['float','int','double','decimal']],
            ['question' => 'Как проверить тип переменной?','options' => ['type()','typeof()','checkType()','getType()']],
            ['question' => 'Что вернёт функция type("hello")?','options' => ["<class 'str'>","<class 'text'>","<class 'string'>","<class 'char'>"]],
            ['question' => 'Какой тип данных у значения True?','options' => ['bool','int','str','boolean']],
        ],
        'Операторы и сравнения' => [
            ['question' => 'Что делает оператор ==?','options' => ['Сравнивает значения','Присваивает значение','Сравнивает ссылки','Выполняет побитовое И']],
            ['question' => 'Какой оператор проверяет неравенство?','options' => ['!=','<>','=/=','==']],
            ['question' => 'Что вернёт выражение 5 % 2?','options' => ['1','2','2.5','0']],
            ['question' => 'Какой оператор выполняет возведение в степень?','options' => ['**','^','pow','^^']],
            ['question' => 'Что делает оператор is?','options' => ['Проверяет идентичность объектов','Сравнивает значения','Присваивает переменную','Вызывает функцию']],
        ],
        'Условия: if/elif/else' => [
            ['question' => 'Какой синтаксис условного оператора в Python?','options' => ['if условие:','if (условие) {','when условие:','check условие:']],
            ['question' => 'Что такое elif?','options' => [' Else if — дополнительное условие','Else if — конец условия','Exception if — обработка ошибок','End if — завершение']],
            ['question' => 'Можно ли использовать if без else?','options' => ['Да','Нет','Только с elif','Только в цикле']],
            ['question' => 'Что произойдёт, если условие ложно и нет else?','options' => ['Блок кода пропустится','Программа завершится','Выведется ошибка','Выполнится предыдущий блок']],
            ['question' => 'Как записать несколько условий одновременно?','options' => ['if a > 5 and b < 10:','if a > 5 & b < 10:','if a > 5 && b < 10:','if (a > 5) and (b < 10)']],
        ],
        'Циклы: for, while' => [
            ['question' => 'Какой цикл используется для перебора последовательности?','options' => ['for','while','loop','repeat']],
            ['question' => 'Что делает break в цикле?','options' => ['Прерывает цикл','Переходит к следующей итерации','Завершает программу','Приостанавливает цикл']],
            ['question' => 'Что делает continue в цикле?','options' => ['Переходит к следующей итерации','Прерывает цикл','Возвращает значение','Завершает функцию']],
            ['question' => 'Как создать бесконечный цикл?','options' => ['while True:','for True:','loop True:','while 1=1:']],
            ['question' => 'Что делает функция range(5)?','options' => ['Генерирует числа от 0 до 4','Генерирует числа от 1 до 5','Генерирует числа от 0 до 5','Создает список из 5 элементов']],
        ],
        'Функции' => [
            ['question' => 'Как объявить функцию в Python?','options' => ['def имя_функции():','function имя_функции():','func имя_функции():','void имя_функции():']],
            ['question' => 'Что такое аргументы по умолчанию?','options' => ['Значения, используемые если аргумент не передан','Обязательные параметры','Глобальные переменные','Возвращаемые значения']],
            ['question' => 'Как вернуть значение из функции?','options' => ['return значение','yield значение','output значение','send значение']],
            ['question' => 'Что такое *args в параметрах функции?','options' => ['Позиционные аргументы переменной длины','Именованные аргументы','Обязательные аргументы','Глобальные переменные']],
            ['question' => 'Может ли функция возвращать несколько значений?','options' => ['Да, через кортеж','Нет, только одно','Да, только числа','Нет, только строки']],
        ],
        'Строки и форматирование' => [
            ['question' => 'Как создать многострочную строку?','options' => ['Тройные кавычки """ """','Двойные кавычки','Одинарные кавычки','Оператор +']],
            ['question' => 'Что делает метод .strip()?','options' => ['Удаляет пробелы по краям','Удаляет все пробелы','Заменяет пробелы','Добавляет пробелы']],
            ['question' => 'Как форматировать строку с f-строкой?','options' => ['f-строка: f"Привет, {имя}"','Метод: "Привет, {имя}".format()','Оператор: "Привет, %s" % имя','Все варианты верны']],
            ['question' => 'Какой метод разделяет строку на список?','options' => ['split()','divide()','separate()','break()']],
            ['question' => 'Что вернёт строка "hello".upper()?','options' => ['HELLO','hello','Hello','hELLO']],
        ],
        'Списки и кортежи' => [
            ['question' => 'Как создать пустой список?','options' => ['[] или list()','() или tuple()','{} или dict()','<> или array()']],
            ['question' => 'Чем списки отличаются от кортежей?','options' => ['Списки изменяемы, кортежи нет','Списки неизменяемы','Кортежи изменяемы','Нет отличий']],
            ['question' => 'Как добавить элемент в список?','options' => ['append()','add()','insert()','push()']],
            ['question' => 'Что делает срез list[1:3]?','options' => ['Возвращает элементы с индекса 1 по 2','Возвращает элементы с индекса 1 по 3','Возвращает все элементы','Возвращает последний элемент']],
            ['question' => 'Как получить длину списка?','options' => ['len()','length()','size()','count()']],
        ],
        'Словари и множества' => [
            ['question' => 'Как создать словарь?','options' => ['{"ключ": "значение"}','[ключ, значение]','(ключ, значение)','<ключ, значение>']],
            ['question' => 'Как получить значение по ключу?','options' => ['dict["ключ"]','dict.get("ключ")','Оба варианта верны','dict.key("значение")']],
            ['question' => 'Что такое множество (set)?','options' => ['Неупорядоченная коллекция уникальных элементов','Упорядоченный список','Кортеж с повторениями','Словарь без значений']],
            ['question' => 'Как добавить элемент в множество?','options' => ['add()','append()','insert()','put()']],
            ['question' => 'Может ли ключ словаря быть списком?','options' => ['Нет, ключ должен быть неизменяемым типом','Да, всегда','Только числовой список','Только строковый список']],
        ],
        'Файлы и исключения' => [
            ['question' => 'Как открыть файл для чтения?','options' => ['open("file.txt", "r")','read("file.txt")','file("file.txt")','get("file.txt")']],
            ['question' => 'Что делает блок try/except?','options' => ['Обрабатывает ошибки','Запускает цикл','Объявляет переменные','Форматирует вывод']],
            ['question' => 'Как закрыть файл после работы?','options' => ['close()','end()','stop()','finish()']],
            ['question' => 'Что такое контекстный менеджер with?','options' => ['Автоматически управляет ресурсами','Создаёт новые переменные','Обрабатывает исключения','Запускает цикл']],
            ['question' => 'Какой тип исключения возникает при делении на ноль?','options' => ['ZeroDivisionError','MathError','DivisionError','ArithmeticError']],
        ],
        'ООП: Классы и наследование' => [
            ['question' => 'Как создать класс в Python?','options' => ['class ИмяКласса:','class ИмяКласса(){}','new class ИмяКласса','def class ИмяКласса:']],
            ['question' => 'Что такое __init__?','options' => ['Конструктор класса','Деструктор','Метод класса','Атрибут класса']],
            ['question' => 'Что делает ключевое слово self?','options' => ['Ссылается на текущий экземпляр класса','Создаёт новую переменную','Удаляет объект','Импортирует модуль']],
            ['question' => 'Что такое наследование?','options' => ['Дочерний класс получает атрибуты родительского','Создание нового класса','Удаление класса','Импорт класса']],
            ['question' => 'Что такое полиморфизм?','options' => ['Один метод работает по-разному для разных классов','Создание копий объектов','Удаление методов','Приватные атрибуты']],
        ],
        'Декораторы и генераторы' => [
            ['question' => 'Что такое декоратор?','options' => ['Функция, изменяющая поведение другой функции','Класс для создания объектов','Модуль для импорта','Тип переменной']],
            ['question' => 'Как применить декоратор?','options' => ['@декоратор перед функцией','decorator(функция)','apply(декоратор, функция)','use декоратор']],
            ['question' => 'Что такое генератор?','options' => ['Функция с yield, возвращающая значения по одному','Обычная функция с return','Класс с методами','Модуль для создания данных']],
            ['question' => 'Что делает yield?','options' => ['Приостанавливает выполнение функции и возвращает значение','Завершает функцию','Создаёт переменную','Импортирует модуль']],
            ['question' => 'Как создать генератор列表а?','options' => ['[x**2 for x in range(10)]','generator(x**2 for x in range(10))','create_gen(x**2 for x in range(10))','yield x**2 for x in range(10)']],
        ],
        'Модули и пакеты' => [
            ['question' => 'Как импортировать модуль?','options' => ['import модуль','require модуль','include модуль','using модуль']],
            ['question' => 'Что делает from модуль import функция?','options' => ['Импортирует конкретную функцию из модуля','Создаёт новый модуль','Удаляет модуль','Экспортирует функцию']],
            ['question' => 'Что такое __init__.py?','options' => ['Делает директорию пакетом Python','Запускает тесты','Содержит переменные','Определяет права доступа']],
            ['question' => 'Где хранятся сторонние библиотеки Python?','options' => ['В папке site-packages','В папке modules','В папке lib','В папке packages']],
            ['question' => 'Как установить библиотеку из requirements.txt?','options' => ['pip install -r requirements.txt','pip install requirements.txt','pip get -r requirements.txt','pip load requirements.txt']],
        ],
        'virtualenv и pip' => [
            ['question' => 'Зачем нужен virtualenv?','options' => ['Создаёт изолированное окружение для проекта','Ускоряет работу Python','Управляет пакетами','Тестирует код']],
            ['question' => 'Как создать виртуальное окружение?','options' => ['python -m venv myenv','virtualenv create myenv','pip create env','python --env myenv']],
            ['question' => 'Как активировать виртуальное окружение на Windows?','options' => ['myenv\\Scripts\\activate','source myenv/bin/activate','activate myenv','env start myenv']],
            ['question' => 'Что такое freeze в pip?','options' => ['Экспортирует установленные пакеты в файл','Останавливает установку','Удаляет пакеты','Обновляет pip']],
            ['question' => 'Как деактивировать виртуальное окружение?','options' => ['deactivate','env stop','venv end','pip off']],
        ],
        'Тестирование: pytest' => [
            ['question' => 'Какой префикс у тестовых файлов в pytest?','options' => ['test_','_test','test.','testing_']],
            ['question' => 'Как запустить все тесты?','options' => ['pytest','python -m pytest','run tests','test all']],
            ['question' => 'Что такое fixture в pytest?','options' => ['Функция, предоставляющая данные для тестов','Тестовая функция','Модуль тестов','Класс тестов']],
            ['question' => 'Как проверить, что код вызывает исключение?','options' => ['with pytest.raises(Exception):','assert raises(Exception)','try: except:','check error()']],
            ['question' => 'Что делает декоратор @pytest.mark.parametrize?','options' => ['Запускает тест с разными параметрами','Пропускает тест','Отмечает тест как обязательный','Создаёт фикстуру']],
        ],
        'Логирование: logging' => [
            ['question' => 'Какой уровень логирования самый серьёзный?','options' => ['CRITICAL','ERROR','WARNING','DEBUG']],
            ['question' => 'Как настроить формат логов?','options' => ['logging.basicConfig(format="...")','logging.format("...")','log.setFormat("...")','logger.format("...")']],
            ['question' => 'Что такое handlers в logging?','options' => ['Определяют, куда отправляются логи','Форматируют сообщения','Уровни логирования','Фильтры сообщений']],
            ['question' => 'Какой модуль используется для логирования?','options' => ['logging','log','logger','debug']],
            ['question' => 'Что делает функция logging.debug()?','options' => ['Записывает отладочную информацию','Завершает программу','Выводит сообщение пользователю','Очищает логи']],
        ],
        'Django / Flask / FastAPI' => [
            ['question' => 'Какой фреймворк подходит для простых REST API?','options' => ['FastAPI или Flask','Django только','Только Express','Tornado']],
            ['question' => 'Что такое ORM в контексте веб-фреймворков?','options' => ['Объектно-реляционное отображение для работы с БД','Метод шифрования','Протокол передачи данных','Формат данных']],
            ['question' => 'Какой фреймворк имеет встроенный ORM?','options' => ['Django','Flask','FastAPI','Все']],
            ['question' => 'Что такое middleware?','options' => ['Промежуточный обработчик запросов','База данных','Фронтенд компонент','Тестовый фреймворк']],
            ['question' => 'Какой декоратор определяет маршрут в Flask?','options' => ['@app.route("/")','@get("/")','@url("/")','@path("/")']],
        ],
        'ORM: SQLAlchemy / Django ORM' => [
            ['question' => 'Что делает ORM?','options' => ['Позволяет работать с БД через объекты Python','Управляет сервером','Создаёт HTML','Обрабатывает запросы']],
            ['question' => 'Как создать модель в Django ORM?','options' => ['class MyModel(models.Model):','model MyModel:','create Model MyModel','def model MyModel:']],
            ['question' => 'Что такое миграция в ORM?','options' => ['Изменение структуры базы данных','Экспорт данных','Импорт данных','Очистка базы']],
            ['question' => 'Как выполнить SQL запрос в SQLAlchemy?','options' => ['engine.execute("SELECT...")','db.query("SELECT...")','sql.run("SELECT...")','orm.execute("SELECT...")']],
            ['question' => 'Что такое relationship в ORM?','options' => ['Связь между таблицами базы данных','Тип данных','Функция сортировки','Метод фильтрации']],
        ],
        'Асинхронность: asyncio' => [
            ['question' => 'Как объявить асинхронную функцию?','options' => ['async def my_func():','def async my_func():','await def my_func():','asyncio def my_func():']],
            ['question' => 'Что делает await?','options' => ['Ожидает завершения асинхронной операции','Запускает функцию','Останавливает программу','Удаляет задачу']],
            ['question' => 'Как запустить асинхронную функцию?','options' => ['asyncio.run(my_func())','my_func().start()','run async my_func()','execute my_func()']],
            ['question' => 'Что такое event loop?','options' => ['Цикл обработки асинхронных задач','Бесконечный цикл','Обычный цикл for','Цикл while']],
            ['question' => 'Зачем нужна асинхронность?','options' => ['Для параллельной обработки I/O операций','Для ускорения вычислений','Для создания GUI','Для работы с файлами']],
        ],
        'Data Science: NumPy, Pandas' => [
            ['question' => 'Что такое NumPy?','options' => ['Библиотека для работы с числовыми массивами','База данных','Веб-фреймворк','Текстовый редактор']],
            ['question' => 'Что такое DataFrame в Pandas?','options' => ['Табличная структура данных','Одномерный массив','База данных','График']],
            ['question' => 'Как прочитать CSV файл в Pandas?','options' => ['pd.read_csv("file.csv")','pd.load_csv("file.csv")','pd.open_csv("file.csv")','pd.import_csv("file.csv")']],
            ['question' => 'Что делает метод .describe()?','options' => ['Выводит статистическое описание данных','Описывает данные текстом','Создаёт новую таблицу','Удаляет данные']],
            ['question' => 'Как выбрать столбец в DataFrame?','options' => ['df["столбец"]','df.column("столбец")','df.get("столбец")','df.select("столбец")']],
        ],
        'Machine Learning: scikit-learn' => [
            ['question' => 'Что такое supervised learning?','options' => ['Обучение на размеченных данных','Обучение без разметки','Глубокое обучение','Кластеризация']],
            ['question' => 'Как разделить данные на обучающую и тестовую выборки?','options' => ['train_test_split()','split_data()','divide()','separate()']],
            ['question' => 'Что такое fit() в scikit-learn?','options' => ['Обучает модель на данных','Предсказывает результат','Оценивает модель','Загружает данные']],
            ['question' => 'Что такое feature?','options' => ['Признак или характеристика данных','Целевая переменная','Модель','Алгоритм']],
            ['question' => 'Какой алгоритм подходит для классификации?','options' => ['Random Forest','Linear Regression','K-Means','PCA']],
        ],
    
        ];
    }

    private function getPythonExamData(): array
    {
        return [
        'Какой командой устанавливается пакет через pip?' => ['pip install пакет','pip add пакет','pip get пакет','pip load пакет'],
        'Что вернёт выражение 2 ** 3?' => ['8','6','9','5'],
        'Какой метод добавляет элемент в конец списка?' => ['append()','add()','insert()','push()'],
        'Что делает оператор идентичности is?' => ['Проверяет, ссылаются ли объекты на один и тот же объект','Сравнивает значения','Присваивает переменную','Вызывает метод'],
        'Как объявить функцию с аргументом по умолчанию?' => ['def func(x=5):','def func(x: 5):','def func(x => 5):','def func(x == 5):'],
        'Что такое декоратор @staticmethod?' => ['Метод, не требующий экземпляра класса','Приватный метод','Абстрактный метод','Глобальная функция'],
        'Как создать множество из списка?' => ['set([1,2,3])','list_to_set([1,2,3])','make_set([1,2,3])','convert([1,2,3])'],
        'Что делает метод .items() словаря?' => ['Возвращает пары ключ-значение','Возвращает только ключи','Возвращает только значения','Удаляет элемент'],
        'Какой оператор используется для генерации списка?' => ['[x for x in range(10)]','list(x for x in range(10))','generate(x for x in range(10))','create(x for x in range(10))'],
        'Что такое async/await?' => ['Механизм асинхронного программирования','Способ шифрования','Тип переменной','Метод сортировки'],
        'Как импортировать конкретную функцию из модуля?' => ['from модуль import функция','import модуль.функция','require модуль.функция','use модуль.функция'],
        'Что такое virtualenv?' => ['Инструмент создания изолированного окружения Python','Редактор кода','База данных','Веб-сервер'],
        'Какой уровень логирования используется для отладки?' => ['DEBUG','INFO','WARNING','ERROR'],
        'Что делает pytest.fixture?' => ['Предоставляет данные для тестов','Запускает тесты','Генерирует отчёт','Удаляет тесты'],
        'Какой фреймворк подходит для микросервисов?' => ['FastAPI','Django','Flask','Web2py'],
        'Что такое ORM?' => ['Объектно-реляционное отображение','Метод шифрования','Протокол передачи','Формат данных'],
        'Как создать асинхронную функцию?' => ['async def func():','def async func():','await func():','asyncio func():'],
        'Что делает метод .head() в Pandas?' => ['Возвращает первые 5 строк DataFrame','Возвращает последние строки','Сортирует данные','Удаляет дубликаты'],
        'Как разделить данные для обучения и тестирования?' => ['train_test_split()','split()','divide_data()','separate()'],
        'Что такое feature в машинном обучении?' => ['Признак данных','Целевая переменная','Модель','Алгоритм'],
        'Как записать условие с несколькими проверками?' => ['if a > 5 and b < 10:','if a > 5 & b < 10:','if (a > 5) and (b < 10):','if a > 5 && b < 10:'],
        'Что такое __str__ в Python?' => ['Метод, возвращающий строковое представление объекта','Конструктор','Деструктор','Имя класса'],
        'Какой метод удаляет элемент из списка по индексу?' => ['pop()','remove()','delete()','drop()'],
        'Что такое list comprehension?' => ['Способ создания списков с помощью выражения','Тип переменной','Метод сортировки','Функция для поиска'],
        'Как открыть файл для записи?' => ['open("file.txt", "w")','write("file.txt")','open("file.txt")','create("file.txt")'],
        'Что делает pip freeze?' => ['Выводит список установленных пакетов','Устанавливает пакеты','Удаляет пакеты','Обновляет pip'],
        'Как создать кортеж?' => ['(1, 2, 3)','[1, 2, 3]','{1, 2, 3}','<1, 2, 3>'],
        'Что такое контекстный менеджер?' => ['Объект для управления ресурсами через with','Тип переменной','Функция без параметров','Метод класса'],
        'Какой метод ищет подстроку в строке?' => ['find()','search()','locate()','index_of()'],
        'Как вывести текст в консоль?' => ['print("текст")','echo("текст")','console.log("текст")','System.out.println("текст")'],
    
        ];
    }

    private function getUIUXQuizData(): array
    {
        return [
        'Принципы дизайна' => [
            ['question' => 'Что такое визуальная иерархия?','options' => ['Расположение элементов по важности','Цветовая схема','Шрифтовая пара','Типографика']],
            ['question' => 'Что такое принцип повторения?','options' => ['Использование одинаковых элементов для единообразия','Дублирование контента','Копирование страниц','Повторение анимаций']],
            ['question' => 'Что такое баланс в дизайне?','options' => ['Равномерное распределение визуального веса','Использование только симметрии','Размер элементов','Количество цветов']],
            ['question' => 'Что такое контраст?','options' => ['Различие между элементами для привлечения внимания','Одинаковый цвет','Повторение форм','Размер шрифта']],
            ['question' => 'Что такое выравнивание?','options' => ['Расположение элементов по линиям и сетке','Цвет фона','Размер изображений','Шрифт']],
        ],
        'Теория цвета' => [
            ['question' => 'Что такое цветовой круг?','options' => ['Систематизированное представление цветов','Палитра цветов','Список цветов','Цветовая таблица']],
            ['question' => 'Какие основные цвета в модели RGB?','options' => ['Красный, зелёный, синий','Красный, жёлтый, синий','Чёрный, белый, серый','Оранжевый, фиолетовый, бирюзовый']],
            ['question' => 'Что такое комплементарные цвета?','options' => ['Цвета, расположенные напротив друг друга в цветовом круге','Одинаковые цвета','Смежные цвета','Тёплые цвета']],
            ['question' => 'Что такое насыщенность цвета?','options' => ['Интенсивность цвета','Яркость цвета','Темнота цвета','Прозрачность']],
            ['question' => 'Что такое монохромная цветовая схема?','options' => ['Использование оттенков одного цвета','Использование двух цветов','Использование трёх цветов','Использование чёрно-белых цветов']],
        ],
        'Типографика' => [
            ['question' => 'Что такое sans-serif шрифт?','options' => ['Шрифт без засечек','Шрифт с засечками','Курсивный шрифт','Моноширинный шрифт']],
            ['question' => 'Что такое трекинг в типографике?','options' => ['Расстояние между всеми символами','Расстояние между двумя символами','Высота шрифта','Толщина шрифта']],
            ['question' => 'Что такое кернинг?','options' => ['Расстояние между парой символов','Общее расстояние между буквами','Высота строки','Размер шрифта']],
            ['question' => 'Что такое line-height?','options' => ['Межстрочный интервал','Высота шрифта','Ширина текста','Размер отступа']],
            ['question' => 'Какой шрифт лучше использовать для заголовков?','options' => ['Крупный, контрастный шрифт','Мелкий шрифт','Серый шрифт','Курсив']],
        ],
        'Композиция и сетки' => [
            ['question' => 'Что такое колоночная сетка?','options' => ['Система из колонок для выравнивания элементов','Таблица данных','Список элементов','Цветовая палитра']],
            ['question' => 'Что такое правило третей?','options' => ['Разделение композиции на 9 частей для баланса','Деление на 3 части','Использование 3 цветов','3 шрифта']],
            ['question' => 'Что такое gutter в сетке?','options' => ['Расстояние между колонками','Ширина колонки','Высота строки','Отступ от края']],
            ['question' => 'Что такое white space?','options' => ['Пустое пространство между элементами','Белый цвет фона','Пробел в тексте','Пустая страница']],
            ['question' => 'Какой тип сетки используется для адаптивного дизайна?','options' => ['Fluid grid','Fixed grid','Static grid','Hard grid']],
        ],
        'User Research' => [
            ['question' => 'Что такое User Research?','options' => ['Изучение пользователей и их потребностей','Дизайн интерфейсов','Программирование','Тестирование серверов']],
            ['question' => 'Какой метод сбора данных самый быстрый?','options' => ['Онлайн-опросы','Глубинные интервью','Фокус-группы','Наблюдение']],
            ['question' => 'Что такое A/B тестирование?','options' => ['Сравнение двух вариантов дизайна','Тестирование двух программ','Два цвета в дизайне','Два шрифта']],
            ['question' => 'Что такое usability тест?','options' => ['Проверка удобства использования продукта','Тест скорости','Тест совместимости','Тест производительности']],
            ['question' => 'Зачем нужны интервью с пользователями?','options' => ['Понять их потребности и боли','Узнать их возраст','Проверить их зрение','Измерить скорость чтения']],
        ],
        'Персоны и journey maps' => [
            ['question' => 'Что такое персона в UX?','options' => ['Вымышленный образ типичного пользователя','Реальный пользователь','Дизайнер','Программист']],
            ['question' => 'Что такое journey map?','options' => ['Карта пути пользователя к цели','Карта мира','Схема сервера','План сайта']],
            ['question' => 'Какие элементы включает персона?','options' => ['Цели, потребности, боли, поведение','Только имя и возраст','Только фото','Только должность']],
            ['question' => 'Что такое touchpoint на journey map?','options' => ['Точка контакта пользователя с продуктом','Кнопка на сайте','Цвет интерфейса','Шрифт']],
            ['question' => 'Зачем нужны персоны?','options' => ['Для понимания целевой аудитории','Для украшения презентации','Для написания кода','Для серверной настройки']],
        ],
        'Wireframing' => [
            ['question' => 'Что такое wireframe?','options' => ['Схематичный черновик интерфейса','Готовый дизайн','Прототип','Код']],
            ['question' => 'Какой инструмент лучше всего подходит для wireframing?','options' => ['Бумага и ручка или Figma','Photoshop','Illustrator','Excel']],
            ['question' => 'Что такое low-fidelity wireframe?','options' => ['Простая схема без деталей','Высокодетализированный дизайн','Прототип с анимациями','Готовый продукт']],
            ['question' => 'Что показывает wireframe?','options' => ['Структуру и расположение элементов','Цвета и шрифты','Анимации','Код']],
            ['question' => 'Когда нужно создавать wireframe?','options' => ['На начальном этапе проектирования','После запуска продукта','При написании кода','После тестирования']],
        ],
        'Figma основы' => [
            ['question' => 'Что такое Figma?','options' => ['Облачный инструмент для дизайна интерфейсов','Текстовый редактор','Браузер','Поисковая система']],
            ['question' => 'Что такое frame в Figma?','options' => ['Контейнер для компонентов','Обычный прямоугольник','Текстовый блок','Кнопка']],
            ['question' => 'Что такое auto layout в Figma?','options' => ['Автоматическое расположение элементов','Автоматический цвет','Автоматический шрифт','Автоматическая анимация']],
            ['question' => 'Как добавить компонент в Figma?','options' => ['Ctrl+Alt+K','Ctrl+C','Ctrl+V','Ctrl+Z']],
            ['question' => 'Что такое prototype в Figma?','options' => ['Интерактивная модель для демонстрации','Статичный макет','Исходный код','График']],
        ],
        'Прототипирование в Figma' => [
            ['question' => 'Что такое прототип?','options' => ['Интерактивная модель продукта','Статичный дизайн','Код','Документация']],
            ['question' => 'Как создать переход между экранами?','options' => ['Перетащить стрелку от элемента к экрану','Написать код','Использовать CSS','Добавить ссылку']],
            ['question' => 'Что такое interaction в Figma?','options' => ['Настройка поведения элемента при взаимодействии','Цвет элемента','Размер элемента','Позиция элемента']],
            ['question' => 'Какой тип анимации самый простой?','options' => ['Dissolve','Smart Animate','Move in','Slide in']],
            ['question' => 'Зачем нужен прототип?','options' => ['Для тестирования идеи до разработки','Для написания кода','Для создания сервера','Для хранения данных']],
        ],
        'Дизайн-системы' => [
            ['question' => 'Что такое дизайн-система?','options' => ['Набор правил и компонентов для единообразия','Программа для рисования','База данных','Сервер']],
            ['question' => 'Какие элементы включает дизайн-система?','options' => ['Цвета, шрифты, компоненты, паттерны','Только цвета','Только шрифты','Только кнопки']],
            ['question' => 'Что такое design token?','options' => ['Атомарные значения дизайна','Токен доступа','Пароль','Ключ шифрования']],
            ['question' => 'Зачем нужна дизайн-система?','options' => ['Для ускорения работы и единообразия','Для украшения интерфейса','Для написания кода','Для серверной настройки']],
            ['question' => 'Что такое component library?','options' => ['Библиотека переиспользуемых компонентов','Книга по дизайну','Галерея изображений','Палитра цветов']],
        ],
        'Анимация и микроинтеракции' => [
            ['question' => 'Что такое микроинтеракция?','options' => ['Маленькое действие, отвечающее на действие пользователя','Большая анимация','Переход между страницами','Звуковой эффект']],
            ['question' => 'Что такое easing в анимации?','options' => ['Ускорение и замедление анимации','Начальная позиция','Конечная позиция','Цвет анимации']],
            ['question' => 'Зачем нужны анимации в интерфейсе?','options' => ['Для улучшения UX и обратной связи','Для украшения','Для замедления работы','Для увеличения веса страницы']],
            ['question' => 'Что такое hover-эффект?','options' => ['Изменение элемента при наведении курсора','Изменение при клике','Изменение при скролле','Изменение при загрузке']],
            ['question' => 'Какой тип анимации подходит для загрузки?','options' => ['Spinner или progress bar','Fade out','Scale up','Rotate']],
        ],
        'UX-копирайтинг' => [
            ['question' => 'Что такое UX-копирайтинг?','options' => ['Написание текстов для улучшения пользовательского опыта','Написание статей','Написание книг','Написание кода']],
            ['question' => 'Какой текст на кнопке лучше?','options' => ['Глагол, описывающий действие','Название компании','Слово ОК','Точка']],
            ['question' => 'Что такое microcopy?','options' => ['Короткие тексты в интерфейсе','Длинные статьи','Заголовки статей','Названия компаний']],
            ['question' => 'Как писать сообщения об ошибках?','options' => ['Понятно и с предложением решения','Техническим языком','Коротко и без объяснений','С эмодзи']],
            ['question' => 'Зачем нужен UX-копирайтинг?','options' => ['Для hướngования пользователя','Для SEO-оптимизации','Для наполнения сайта','Для рекламы']],
        ],
        'Доступность (a11y)' => [
            ['question' => 'Что такое a11y?','options' => ['Доступность интерфейса для людей с ограниченными возможностями','Скорость загрузки','Размер шрифта','Цвет интерфейса']],
            ['question' => 'Зачем нужны alt-тексты для изображений?','options' => ['Для скринридеров и SEO','Для ускорения загрузки','Для красоты','Для анимаций']],
            ['question' => 'Какой контраст считается достаточным по WCAG?','options' => ['4.5:1 для обычного текста','2:1','1:1','10:1']],
            ['question' => 'Что такое focus state?','options' => ['Видимый индикатор активного элемента','Цвет фона','Размер шрифта','Позиция элемента']],
            ['question' => 'Какой HTML-тег важен для доступности?','options' => ['<nav> и <main>','<div>','<span>','<p>']],
        ],
        'Тестирование юзабилити' => [
            ['question' => 'Что такое юзабилити-тестирование?','options' => ['Проверка удобства использования продукта','Тестирование скорости','Тестирование серверов','Тестирование кода']],
            ['question' => 'Сколько пользователей достаточно для тестирования?','options' => ['5-8 человек','1 человек','100 человек','50 человек']],
            ['question' => 'Что такое think aloud protocol?','options' => ['Пользователь озвучивает свои мысли во время теста','Молчаливое тестирование','Запись экрана','Аудиозапись']],
            ['question' => 'Где проводить тестирование?','options' => ['В лаборатории или удалённо','Только в офисе','Только на улице','Только дома']],
            ['question' => 'Что фиксируют при тестировании?','options' => ['Задачи, которые пользователь не смог выполнить','Только успехи','Только ошибки','Ничего']],
        ],
        'Design handoff разработчикам' => [
            ['question' => 'Что такое design handoff?','options' => ['Передача дизайна разработчикам','Передача кода дизайнеру','Передача данных','Передача серверов']],
            ['question' => 'Какую документацию нужно подготовить?','options' => ['Спецификации, отступы, размеры','Только макет','Только иконки','Только текст']],
            ['question' => 'Что такое Zeplin или Figma inspect?','options' => ['Инструменты для просмотра CSS-свойств','Редакторы изображений','Браузеры','Поисковые системы']],
            ['question' => 'Что важно указать при передаче?','options' => ['Отступы, размеры, цвета, шрифты','Только цвета','Только шрифты','Только размеры']],
            ['question' => 'Когда начинать handoff?','options' => ['После утверждения финального дизайна','В начале проекта','После запуска','При написании кода']],
        ],
        'React / HTML+CSS для дизайнера' => [
            ['question' => 'Зачем дизайнеру знать HTML?','options' => ['Для понимания структуры веб-страниц','Для написания серверов','Для создания баз данных','Для работы с файлами']],
            ['question' => 'Что такое CSS?','options' => ['Язык стилей для оформления веб-страниц','Язык программирования','База данных','Операционная система']],
            ['question' => 'Что такое React?','options' => ['Библиотека для создания пользовательских интерфейсов','Фреймворк для серверов','База данных','Операционная система']],
            ['question' => 'Что такое Flexbox?','options' => ['Система компоновки в CSS','Язык программирования','Редактор изображений','Браузер']],
            ['question' => 'Какой CSS-свойство управляет внешним видом?','options' => ['style','class','id','type']],
        ],
        'Портфолио и карьера' => [
            ['question' => 'Что должно быть в портфолио дизайнера?','options' => ['Проекты с описанием процесса','Только изображения','Только код','Только резюме']],
            ['question' => 'Как описывать проект в портфолио?','options' => ['Проблема → Решение → Результат','Только название','Только скриншоты','Только ссылка']],
            ['question' => 'Где размещать портфолио?','options' => ['Behance, Dribbble, личный сайт','Только в соцсетях','Только на бумаге','Только в PDF']],
            ['question' => 'Какие навыки важны для дизайнера?','options' => ['UX/UI, коммуникация, аналитика','Только рисование','Только код','Только презентации']],
            ['question' => 'Как подготовиться к собеседованию?','options' => ['Подготовить кейсы и рассказать о процессе','Только надеть костюм','Только взять резюме','Только ответить на вопросы']],
        ],
    
        ];
    }

    private function getUIUXExamData(): array
    {
        return [
        'Какой принцип дизайна предполагает использование пустого пространства?' => ['White space','Контраст','Повторение','Выравнивание'],
        'Какие цвета являются комплементарными в цветовом круге?' => ['Расположенные напротив друг друга','Одинаковые','Смежные','Тёплые'],
        'Что такое sans-serif шрифт?' => ['Шрифт без засечек','Шрифт с засечками','Курсивный шрифт','Моноширинный шрифт'],
        'Какой тип сетки лучше всего подходит для адаптивного дизайна?' => ['Fluid grid','Fixed grid','Static grid','Hard grid'],
        'Что такое A/B тестирование?' => ['Сравнение двух вариантов дизайна','Тестирование двух программ','Два цвета в дизайне','Два шрифта'],
        'Что такое journey map?' => ['Карта пути пользователя к цели','Карта мира','Схема сервера','План сайта'],
        'Что такое wireframe?' => ['Схематичный черновик интерфейса','Готовый дизайн','Прототип','Код'],
        'Как добавить компонент в Figma?' => ['Ctrl+Alt+K','Ctrl+C','Ctrl+V','Ctrl+Z'],
        'Что такое auto layout в Figma?' => ['Автоматическое расположение элементов','Автоматический цвет','Автоматический шрифт','Автоматическая анимация'],
        'Что такое design token?' => ['Атомарные значения дизайна','Токен доступа','Пароль','Ключ шифрования'],
        'Что такое микроинтеракция?' => ['Маленькое действие, отвечающее на действие пользователя','Большая анимация','Переход между страницами','Звуковой эффект'],
        'Что такое easing в анимации?' => ['Ускорение и замедление анимации','Начальная позиция','Конечная позиция','Цвет анимации'],
        'Какой текст на кнопке лучше для UX?' => ['Глагол, описывающий действие','Название компании','Слово ОК','Точка'],
        'Что такое microcopy?' => ['Короткие тексты в интерфейсе','Длинные статьи','Заголовки статей','Названия компаний'],
        'Что такое a11y?' => ['Доступность интерфейса для людей с ограниченными возможностями','Скорость загрузки','Размер шрифта','Цвет интерфейса'],
        'Какой контраст считается достаточным по WCAG для обычного текста?' => ['4.5:1','2:1','1:1','10:1'],
        'Сколько пользователей достаточно для юзабилити-тестирования?' => ['5-8 человек','1 человек','100 человек','50 человек'],
        'Что такое think aloud protocol?' => ['Пользователь озвучивает свои мысли во время теста','Молчаливое тестирование','Запись экрана','Аудиозапись'],
        'Что такое design handoff?' => ['Передача дизайна разработчикам','Передача кода дизайнеру','Передача данных','Передача серверов'],
        'Какую документацию нужно подготовить для handoff?' => ['Спецификации, отступы, размеры','Только макет','Только иконки','Только текст'],
        'Зачем дизайнеру знать HTML?' => ['Для понимания структуры веб-страниц','Для написания серверов','Для создания баз данных','Для работы с файлами'],
        'Что такое CSS?' => ['Язык стилей для оформления веб-страниц','Язык программирования','База данных','Операционная система'],
        'Что такое Flexbox?' => ['Система компоновки в CSS','Язык программирования','Редактор изображений','Браузер'],
        'Что должно быть в портфолио дизайнера?' => ['Проекты с описанием процесса','Только изображения','Только код','Только резюме'],
        'Как описывать проект в портфолио?' => ['Проблема → Решение → Результат','Только название','Только скриншоты','Только ссылка'],
        'Что такое колоночная сетка?' => ['Система из колонок для выравнивания элементов','Таблица данных','Список элементов','Цветовая палитра'],
        'Что такое gutter в сетке?' => ['Расстояние между колонками','Ширина колонки','Высота строки','Отступ от края'],
        'Что такое персона в UX?' => ['Вымышленный образ типичного пользователя','Реальный пользователь','Дизайнер','Программист'],
        'Когда нужно создавать wireframe?' => ['На начальном этапе проектирования','После запуска продукта','При написании кода','После тестирования'],
        'Что такое component library?' => ['Библиотека переиспользуемых компонентов','Книга по дизайну','Галерея изображений','Палитра цветов'],
    
        ];
    }

    private function getMobileQuizData(): array
    {
        return [
            'Язык: Swift / Kotlin / Dart' => [
                ['question' => 'Какой тип переменной используется в Swift для хранения неизменяемых данных?','options' => ['let', 'var', 'const', 'mut']],
                ['question' => 'Как объявить переменную в Kotlin?','options' => ['val / var', 'let / const', 'dim / static', 'def / let']],
                ['question' => 'Какой язык используется для разработки приложений на Flutter?','options' => ['Dart', 'Kotlin', 'Swift', 'JavaScript']],
                ['question' => 'Какой модификатор видимости в Kotlin доступен только внутри класса?','options' => ['private', 'public', 'internal', 'protected']],
                ['question' => 'Какой оператор используется для безопасного вызова в Kotlin?','options' => ['?.', '->', '=>', '!!']],
            ],
            'IDE: Xcode / Android Studio' => [
                ['question' => 'Какая IDE используется для разработки iOS-приложений?','options' => ['Xcode', 'Android Studio', 'Visual Studio', 'IntelliJ IDEA']],
                ['question' => 'Какой эмулятор поставляется с Android Studio?','options' => ['Android Emulator', 'iOS Simulator', 'Genymotion', 'Xamarin']],
                ['question' => 'Какой менеджер пакетов используется в Android Studio?','options' => ['Gradle', 'CocoaPods', 'Maven', 'npm']],
                ['question' => 'Какой язык используется для написания скриптов сборки в Xcode?','options' => ['Swift', 'Kotlin', 'Java', 'Objective-C']],
                ['question' => 'Какой инструмент используется для отладки iOS-приложений?','options' => ['Instruments', 'Logcat', 'Chrome DevTools', 'GDB']],
            ],
            'Основы мобильного UI' => [
                ['question' => 'Какой виджет используется для отображения текста в Flutter?','options' => ['Text', 'Label', 'TextView', 'UILabel']],
                ['question' => 'Какой компонент используется для ввода текста в SwiftUI?','options' => ['TextField', 'EditText', 'Input', 'TextBox']],
                ['question' => 'Какой Layout используется для расположения элементов в строку в Android?','options' => ['LinearLayout', 'RelativeLayout', 'ConstraintLayout', 'FrameLayout']],
                ['question' => 'Какой виджет используется для кнопки в Flutter?','options' => ['ElevatedButton', 'Button', 'FlatButton', 'RaisedButton']],
                ['question' => 'Какой компонент используется для отображения изображения в SwiftUI?','options' => ['Image', 'ImageView', 'Photo', 'Picture']],
            ],
            'Навигация' => [
                ['question' => 'Какой тип навигации используется в SwiftUI для перехода между экранами?','options' => ['NavigationStack', 'UINavigationController', 'Intent', 'Activity']],
                ['question' => 'Какой метод используется для навигации в React Native?','options' => ['navigation.navigate()', 'router.push()', 'intent.addFlags()', 'startActivity()']],
                ['question' => 'Какой компонент используется для навигации в Jetpack Compose?','options' => ['NavController', 'FragmentManager', 'NavigationGraph', 'Intent']],
                ['question' => 'Какой параметр передаётся для навигации с аргументами в Flutter?','options' => ['arguments', 'params', 'data', 'extras']],
                ['question' => 'Какой метод используется для возврата на предыдущий экран в iOS?','options' => ['pop()', 'back()', 'finish()', 'dismiss()']],
            ],
            'State Management' => [
                ['question' => 'Какой класс используется для управления состоянием в SwiftUI?','options' => ['ObservableObject', 'ViewModel', 'State', 'Controller']],
                ['question' => 'Какой провайдер используется для управления состоянием в React Native?','options' => ['Context', 'Redux', 'Provider', 'Bloc']],
                ['question' => 'Какой паттерн используется в Provider для управления состоянием?','options' => ['Provider/Consumer', 'MVC', 'MVVM', 'MVP']],
                ['question' => 'Какой виджет используется для обновления UI при изменении состояния в Flutter?','options' => ['StatefulWidget', 'StatelessWidget', 'Consumer', 'Observer']],
                ['question' => 'Какой метод вызывается при изменении состояния в SwiftUI?','options' => ['objectWillChange.send()', 'notifyListeners()', 'update()', 'onChange()']],
            ],
            'Сетевые запросы' => [
                ['question' => 'Какой класс используется для выполнения HTTP-запросов в Swift?','options' => ['URLSession', 'HttpClient', 'OkHttp', 'Retrofit']],
                ['question' => 'Какой клиент используется для сетевых запросов в Kotlin?','options' => ['Retrofit', 'NSURLSession', 'HttpClient', 'Volley']],
                ['question' => 'Какой формат данных наиболее часто используется в API?','options' => ['JSON', 'XML', 'YAML', 'CSV']],
                ['question' => 'Какой метод HTTP-запроса используется для получения данных?','options' => ['GET', 'POST', 'PUT', 'DELETE']],
                ['question' => 'Какой интерцептор используется для добавления заголовков в Retrofit?','options' => ['OkHttp Interceptor', 'URLProtocol', 'HttpHandler', 'Middleware']],
            ],
            'Локальные данные' => [
                ['question' => 'Какой инструмент используется для хранения данных в SQLite на iOS?','options' => ['Core Data', 'SharedPreferences', 'Realm', 'Room']],
                ['question' => 'Какой API используется для хранения паролей в Android?','options' => ['EncryptedSharedPreferences', 'Keychain', 'UserDefaults', 'localStorage']],
                ['question' => 'Какой формат используется для хранения данных в UserDefaults?','options' => ['Property List', 'JSON', 'XML', 'YAML']],
                ['question' => 'Какой инструмент используется для работы с базой данных в Flutter?','options' => ['sqflite', 'CoreData', 'Room', 'SQLite']],
                ['question' => 'Какой класс используется для работы с ключами в Keychain на iOS?','options' => ['SecItem', 'KeychainStore', 'UserDefaults', 'NSUserDefaults']],
            ],
            'Тестирование' => [
                ['question' => 'Какой фреймворк используется для тестирования в Swift?','options' => ['XCTest', 'JUnit', 'pytest', 'RSpec']],
                ['question' => 'Какой тип теста проверяет отдельный метод или функцию?','options' => ['Unit Test', 'Integration Test', 'UI Test', 'Snapshot Test']],
                ['question' => 'Какой инструмент используется для UI-тестирования в Xcode?','options' => ['XCUITest', 'Espresso', 'Selenium', 'Appium']],
                ['question' => 'Какой фреймворк используется для тестирования в Kotlin?','options' => ['JUnit', 'XCTest', 'Mocha', 'Jest']],
                ['question' => 'Какой тип теста проверяет взаимодействие между компонентами?','options' => ['Integration Test', 'Unit Test', 'UI Test', 'Performance Test']],
            ],
            'Firebase интеграция' => [
                ['question' => 'Какой сервис Firebase используется для аналитики?','options' => ['Firebase Analytics', 'Firebase Crashlytics', 'Firebase Performance', 'Firebase Remote Config']],
                ['question' => 'Какой сервис Firebase используется для хранения файлов?','options' => ['Cloud Storage', 'Firestore', 'Realtime Database', 'Cloud Functions']],
                ['question' => 'Какой сервис Firebase используется для отправки push-уведомлений?','options' => ['Firebase Cloud Messaging', 'Firebase Notifications', 'APNs', 'FCM']],
                ['question' => 'Какой сервис Firebase используется для отслеживания ошибок?','options' => ['Firebase Crashlytics', 'Firebase Analytics', 'Firebase Performance', 'Firebase Test Lab']],
                ['question' => 'Какой сервис Firebase используется для A/B тестирования?','options' => ['Firebase Remote Config', 'Firebase A/B Testing', 'Firebase Predictions', 'Firebase In-App Messaging']],
            ],
            'Push-уведомления' => [
                ['question' => 'Какой сервис используется для push-уведомлений на iOS?','options' => ['APNs', 'FCM', 'GCM', 'WNS']],
                ['question' => 'Какой ключ используется для подписи push-уведомлений в Apple?','options' => ['APNs Auth Key', 'Certificate', 'P8', 'P12']],
                ['question' => 'Какой токен используется для отправки push-уведомлений на Android?','options' => ['Device Token', 'FCM Token', 'Registration ID', 'Push Token']],
                ['question' => 'Какой метод используется для обработки push-уведомлений в фоне на iOS?','options' => ['application(_:didReceiveRemoteNotification:fetchCompletionHandler:)', 'onMessageReceived', 'handlePush', 'processNotification']],
                ['question' => 'Какой тип уведомлений отображается на экране блокировки?','options' => ['Alert', 'Badge', 'Sound', 'Banner']],
            ],
            'Камера и геолокация' => [
                ['question' => 'Какой фреймворк используется для работы с камерой в iOS?','options' => ['AVFoundation', 'CameraX', 'UIImagePickerController', 'PhotoKit']],
                ['question' => 'Какой класс используется для получения геолокации в Android?','options' => ['FusedLocationProviderClient', 'CLLocationManager', 'Geolocation', 'PositionManager']],
                ['question' => 'Какой разрешение необходимо для доступа к камере на Android?','options' => ['CAMERA', 'WRITE_EXTERNAL_STORAGE', 'ACCESS_FINE_LOCATION', 'READ_CONTACTS']],
                ['question' => 'Какой фреймворк используется для работы с камерой в Flutter?','options' => ['camera', 'image_picker', 'photo_view', 'gallery']],
                ['question' => 'Какой метод используется для получения текущего местоположения на iOS?','options' => ['requestLocation()', 'startUpdatingLocation()', 'getLocation()', 'getCurrentLocation()']],
            ],
            'Аутентификация' => [
                ['question' => 'Какой протокол используется для аутентификации в современных приложениях?','options' => ['OAuth 2.0', 'Basic Auth', 'Digest Auth', 'NTLM']],
                ['question' => 'Какой сервис Firebase используется для аутентификации?','options' => ['Firebase Auth', 'Firebase Identity', 'Firebase Access', 'Firebase Login']],
                ['question' => 'Какой токен используется для аутентификации API?','options' => ['JWT', 'Session ID', 'API Key', 'OAuth Token']],
                ['question' => 'Какой метод аутентификации использует биометрию?','options' => ['Biometric Authentication', 'Password', 'PIN', 'Pattern']],
                ['question' => 'Какой сервис используется для аутентификации через Apple ID?','options' => ['Sign in with Apple', 'Apple Auth', 'iCloud Auth', 'Apple ID']],
            ],
            'Публикация в App Store / Play Store' => [
                ['question' => 'Какой файл используется для конфигурации iOS-приложения?','options' => ['Info.plist', 'AndroidManifest.xml', 'build.gradle', 'Podfile']],
                ['question' => 'Какой документ необходимо загрузить в App Store Connect?','options' => ['IPA', 'APK', 'AAB', 'ZIP']],
                ['question' => 'Какой инструмент используется для загрузки приложения в App Store?','options' => ['Transporter', 'Fastlane', 'Xcode', 'Application Loader']],
                ['question' => 'Какой файл используется для подписи Android-приложения?','options' => ['keystore', 'certificate', 'p12', 'mobileprovision']],
                ['question' => 'Какой процесс проверки приложения перед публикацией в App Store?','options' => ['App Review', 'Beta Testing', 'TestFlight', 'Internal Testing']],
            ],
        
        ];
    }

    private function getMobileExamData(): array
    {
        return [
            'Какой язык программирования используется в Flutter?' => ['Dart', 'Kotlin', 'Swift', 'Java'],
            'Какая IDE используется для разработки iOS-приложений?' => ['Xcode', 'Android Studio', 'Visual Studio', 'Eclipse'],
            'Какой компонент используется для ввода текста в SwiftUI?' => ['TextField', 'EditText', 'Input', 'TextBox'],
            'Какой метод используется для навигации в SwiftUI?' => ['NavigationStack', 'UINavigationController', 'Intent', 'Activity'],
            'Какой класс используется для управления состоянием в SwiftUI?' => ['ObservableObject', 'ViewModel', 'State', 'Controller'],
            'Какой класс используется для HTTP-запросов в Swift?' => ['URLSession', 'HttpClient', 'OkHttp', 'Retrofit'],
            'Какой инструмент используется для хранения данных в SQLite на iOS?' => ['Core Data', 'SharedPreferences', 'Realm', 'Room'],
            'Какой фреймворк используется для тестирования в Swift?' => ['XCTest', 'JUnit', 'pytest', 'RSpec'],
            'Какой сервис Firebase используется для аналитики?' => ['Firebase Analytics', 'Firebase Crashlytics', 'Firebase Performance', 'Firebase Remote Config'],
            'Какой сервис используется для push-уведомлений на iOS?' => ['APNs', 'FCM', 'GCM', 'WNS'],
            'Какой фреймворк используется для работы с камерой в iOS?' => ['AVFoundation', 'CameraX', 'UIImagePickerController', 'PhotoKit'],
            'Какой протокол используется для аутентификации в современных приложениях?' => ['OAuth 2.0', 'Basic Auth', 'Digest Auth', 'NTLM'],
            'Какой файл используется для конфигурации iOS-приложения?' => ['Info.plist', 'AndroidManifest.xml', 'build.gradle', 'Podfile'],
            'Какой виджет используется для отображения текста в Flutter?' => ['Text', 'Label', 'TextView', 'UILabel'],
            'Какой Layout используется для расположения элементов в строку в Android?' => ['LinearLayout', 'RelativeLayout', 'ConstraintLayout', 'FrameLayout'],
            'Какой метод вызывается при изменении состояния в SwiftUI?' => ['objectWillChange.send()', 'notifyListeners()', 'update()', 'onChange()'],
            'Какой формат данных наиболее часто используется в API?' => ['JSON', 'XML', 'YAML', 'CSV'],
            'Какой API используется для хранения паролей в Android?' => ['EncryptedSharedPreferences', 'Keychain', 'UserDefaults', 'localStorage'],
            'Какой инструмент используется для UI-тестирования в Xcode?' => ['XCUITest', 'Espresso', 'Selenium', 'Appium'],
            'Какой сервис Firebase используется для хранения файлов?' => ['Cloud Storage', 'Firestore', 'Realtime Database', 'Cloud Functions'],
            'Какой ключ используется для подписи push-уведомлений в Apple?' => ['APNs Auth Key', 'Certificate', 'P8', 'P12'],
            'Какой класс используется для получения геолокации на Android?' => ['FusedLocationProviderClient', 'CLLocationManager', 'Geolocation', 'PositionManager'],
            'Какой токен используется для аутентификации API?' => ['JWT', 'Session ID', 'API Key', 'OAuth Token'],
            'Какой документ необходимо загрузить в App Store Connect?' => ['IPA', 'APK', 'AAB', 'ZIP'],
            'Какой тип теста проверяет отдельный метод или функцию?' => ['Unit Test', 'Integration Test', 'UI Test', 'Snapshot Test'],
            'Какой сервис Firebase используется для отправки push-уведомлений?' => ['Firebase Cloud Messaging', 'Firebase Notifications', 'APNs', 'FCM'],
            'Какой разрешение необходимо для доступа к камере на Android?' => ['CAMERA', 'WRITE_EXTERNAL_STORAGE', 'ACCESS_FINE_LOCATION', 'READ_CONTACTS'],
            'Какой метод аутентификации использует биометрию?' => ['Biometric Authentication', 'Password', 'PIN', 'Pattern'],
            'Какой инструмент используется для загрузки приложения в App Store?' => ['Transporter', 'Fastlane', 'Xcode', 'Application Loader'],
            'Какой тип навигации используется в Jetpack Compose?' => ['NavController', 'FragmentManager', 'NavigationGraph', 'Intent'],
        
        ];
    }

    private function getCppQuizData(): array
    {
        return [
            'Установка компилятора' => [
                ['question' => 'Какой компилятор является стандартом для C++?','options' => ['GCC', 'Clang', 'MSVC', 'Intel ICC']],
                ['question' => 'Какой командой устанавливается GCC на Ubuntu?','options' => ['sudo apt install g++', 'sudo yum install gcc', 'brew install gcc', 'choco install gcc']],
                ['question' => 'Какой флаг указывает стандарт C++ при компиляции?','options' => ['-std=c++17', '-std=c11', '-std=c++', '-std=gnu']],
                ['question' => 'Какой компилятор поставляется с Visual Studio?','options' => ['MSVC', 'GCC', 'Clang', 'Intel ICC']],
                ['question' => 'Какой инструмент используется для управления зависимостями в C++?','options' => ['CMake', 'Make', 'Gradle', 'npm']],
            ],
            'Терминал и сборка' => [
                ['question' => 'Какой командой компилируется C++ файл?','options' => ['g++ file.cpp -o output', 'gcc file.cpp -o output', 'compile file.cpp', 'build file.cpp']],
                ['question' => 'Какой флаг используется для включения отладочной информации?','options' => ['-g', '-O2', '-Wall', '-std=c++17']],
                ['question' => 'Какой командой запускается исполняемый файл на Linux?','options' => ['./output', 'output.exe', 'run output', 'exec output']],
                ['question' => 'Какой файл обычно используется для описания сборки проекта?','options' => ['CMakeLists.txt', 'Makefile', 'build.gradle', 'package.json']],
                ['question' => 'Какой командой очищаются файлы сборки в CMake?','options' => ['cmake --build . --target clean', 'rm -rf build', 'make clean', 'gradle clean']],
            ],
            'Переменные и типы' => [
                ['question' => 'Какой тип данных используется для хранения целых чисел?','options' => ['int', 'float', 'double', 'char']],
                ['question' => 'Какой модификатор делает переменную неизменяемой?','options' => ['const', 'static', 'volatile', 'mutable']],
                ['question' => 'Какой тип данных используется для хранения дробных чисел с двойной точностью?','options' => ['double', 'float', 'int', 'long']],
                ['question' => 'Какой оператор используется для определения размера типа?','options' => ['sizeof', 'size', 'length', 'count']],
                ['question' => 'Какой тип данных используется для хранения символов?','options' => ['char', 'string', 'wchar_t', 'byte']],
            ],
            'Операторы и управление потоком' => [
                ['question' => 'Какой оператор используется для сравнения?','options' => ['==', '=', '!=', '<=']],
                ['question' => 'Какой оператор используется для логического И?','options' => ['&&', '||', '!', '&']],
                ['question' => 'Какой оператор используется для тернарного условия?','options' => ['?:', '??', 'if', '?=']],
                ['question' => 'Какой ключевое слово используется для оператора switch?','options' => ['switch', 'case', 'select', 'match']],
                ['question' => 'Какой оператор используется для выхода из цикла?','options' => ['break', 'continue', 'exit', 'return']],
            ],
            'Функции' => [
                ['question' => 'Какой модификатор позволяет функции работать без создания объекта?','options' => ['static', 'const', 'virtual', 'inline']],
                ['question' => 'Какой оператор используется для определения функции по умолчанию?','options' => ['= default', '= 0', 'override', 'final']],
                ['question' => 'Какой тип возвращаемого значения используется для функций без возврата?','options' => ['void', 'null', 'none', 'empty']],
                ['question' => 'Какой оператор используется для перегрузки функции?','options' => ['operator', 'overload', 'virtual', 'friend']],
                ['question' => 'Какой параметр позволяет передавать переменное количество аргументов?','options' => ['...', 'args', 'params', 'va_list']],
            ],
            'Массивы и указатели' => [
                ['question' => 'Какой оператор используется для получения адреса переменной?','options' => ['&', '*', '@', '#']],
                ['question' => 'Какой оператор используется для разыменования указателя?','options' => ['*', '&', '->', '.']],
                ['question' => 'Какой тип данных используется для хранения адреса в памяти?','options' => ['int*', 'int', 'int&', 'int[]']],
                ['question' => 'Какой оператор используется для создания динамического массива?','options' => ['new', 'malloc', 'alloc', 'create']],
                ['question' => 'Какой оператор используется для освобождения памяти?','options' => ['delete', 'free', 'release', 'destroy']],
            ],
            'Строки и работа с памятью' => [
                ['question' => 'Какой класс используется для работы со строками в C++?','options' => ['std::string', 'String', 'char*', 'CString']],
                ['question' => 'Какой метод возвращает длину строки?','options' => ['size()', 'length()', 'count()', 'len()']],
                ['question' => 'Какой метод используется для конкатенации строк?','options' => ['append()', 'concat()', 'join()', 'merge()']],
                ['question' => 'Какой оператор используется для сравнения строк?','options' => ['==', 'equals()', 'compare()', '===']],
                ['question' => 'Какой метод используется для поиска подстроки?','options' => ['find()', 'search()', 'locate()', 'index()']],
            ],
            'ООП в C++' => [
                ['question' => 'Какое ключевое слово используется для определения класса?','options' => ['class', 'struct', 'object', 'type']],
                ['question' => 'Какой модификатор доступа позволяет обращаться к членам класса из любого места?','options' => ['public', 'private', 'protected', 'internal']],
                ['question' => 'Какой метод вызывается при создании объекта?','options' => ['constructor', 'init', 'create', 'new']],
                ['question' => 'Какой метод вызывается при уничтожении объекта?','options' => ['destructor', 'destroy', 'delete', 'finalize']],
                ['question' => 'Какое ключевое слово используется для наследования?','options' => [':', 'extends', 'implements', 'inherits']],
            ],
            'Наследование и полиморфизм' => [
                ['question' => 'Какое ключевое слово делает функцию виртуальной?','options' => ['virtual', 'abstract', 'interface', 'override']],
                ['question' => 'Какой оператор используется для переопределения виртуальной функции?','options' => ['override', 'virtual', 'new', 'overload']],
                ['question' => 'Какое ключевое слово запрещает наследование от класса?','options' => ['final', 'sealed', 'static', 'const']],
                ['question' => 'Какой тип полиморфизма реализуется через виртуальные функции?','options' => ['динамический полиморфизм', 'шаблонная полиморфия', 'перегрузка', 'неявное преобразование']],
                ['question' => 'Какое ключевое слово используется для чисто виртуальной функции?','options' => ['= 0', '= default', 'pure', 'abstract']],
            ],
            'STL: Контейнеры' => [
                ['question' => 'Какой контейнер используется для динамического массива?','options' => ['std::vector', 'std::array', 'std::list', 'std::deque']],
                ['question' => 'Какой контейнер используется для очереди?','options' => ['std::queue', 'std::stack', 'std::vector', 'std::map']],
                ['question' => 'Какой контейнер используется для ассоциативного массива?','options' => ['std::map', 'std::vector', 'std::list', 'std::set']],
                ['question' => 'Какой контейнер используется для стека?','options' => ['std::stack', 'std::queue', 'std::vector', 'std::deque']],
                ['question' => 'Какой контейнер используется для множества?','options' => ['std::set', 'std::map', 'std::vector', 'std::list']],
            ],
            'STL: Алгоритмы' => [
                ['question' => 'Какой алгоритм используется для сортировки?','options' => ['std::sort', 'std::order', 'std::arrange', 'std::rank']],
                ['question' => 'Какой алгоритм используется для поиска элемента?','options' => ['std::find', 'std::search', 'std::locate', 'std::index']],
                ['question' => 'Какой алгоритм используется для удаления дубликатов?','options' => ['std::unique', 'std::remove', 'std::deduplicate', 'std::filter']],
                ['question' => 'Какой алгоритм используется для реверса элементов?','options' => ['std::reverse', 'std::invert', 'std::flip', 'std::rotate']],
                ['question' => 'Какой алгоритм используется для подсчёта элементов?','options' => ['std::count', 'std::sum', 'std::total', 'std::accumulate']],
            ],
            'Умные указатели' => [
                ['question' => 'Какой умный указатель используется для владения объектом?','options' => ['std::unique_ptr', 'std::shared_ptr', 'std::weak_ptr', 'std::auto_ptr']],
                ['question' => 'Какой умный указатель используется для разделяемого владения?','options' => ['std::shared_ptr', 'std::unique_ptr', 'std::weak_ptr', 'std::auto_ptr']],
                ['question' => 'Какой умный указатель используется для слабых ссылок?','options' => ['std::weak_ptr', 'std::shared_ptr', 'std::unique_ptr', 'std::auto_ptr']],
                ['question' => 'Какой метод создаёт умный указатель?','options' => ['std::make_shared', 'std::create', 'std::new', 'std::alloc']],
                ['question' => 'Какой умный указатель был заменён на std::unique_ptr?','options' => ['std::auto_ptr', 'std::raw_ptr', 'std::dumb_ptr', 'std::old_ptr']],
            ],
            'Многопоточность' => [
                ['question' => 'Какой класс используется для создания потока в C++?','options' => ['std::thread', 'std::process', 'std::task', 'std::worker']],
                ['question' => 'Какой класс используется для синхронизации доступа к данным?','options' => ['std::mutex', 'std::lock', 'std::semaphore', 'std::barrier']],
                ['question' => 'Какой метод присоединяет поток к текущему?','options' => ['join()', 'attach()', 'connect()', 'bind()']],
                ['question' => 'Какой класс используется для условия ожидания?','options' => ['std::condition_variable', 'std::event', 'std::signal', 'std::notify']],
                ['question' => 'Какой класс используется для атомарных операций?','options' => ['std::atomic', 'std::volatile', 'std::sync', 'std::lock']],
            ],
            'Исключения' => [
                ['question' => 'Какой оператор используется для генерации исключения?','options' => ['throw', 'raise', 'error', 'exception']],
                ['question' => 'Какой блок используется для перехвата исключений?','options' => ['catch', 'except', 'handle', 'trap']],
                ['question' => 'Какой класс используется для создания пользовательского исключения?','options' => ['std::exception', 'std::error', 'std::throw', 'std::catch']],
                ['question' => 'Какой метод возвращает сообщение об ошибке?','options' => ['what()', 'message()', 'error()', 'info()']],
                ['question' => 'Какой оператор используется для повторного генерирования исключения?','options' => ['throw', 'rethrow', 'throw_again', 'rethrow()']],
            ],
            'Шаблоны' => [
                ['question' => 'Какое ключевое слово определяет шаблон?','options' => ['template', 'generic', 'typename', 'class']],
                ['question' => 'Какой параметр шаблона используется для типов?','options' => ['typename', 'class', 'type', 'T']],
                ['question' => 'Какой вид шаблонной функции позволяет выводить типы?','options' => ['auto', 'decltype', 'trailing return', 'explicit']],
                ['question' => 'Какой оператор используется для специализации шаблона?','options' => ['template<>', 'specialize', 'override', 'final']],
                ['question' => 'Какой параметр шаблона используется для значений?','options' => ['非类型的', 'typename', 'class', 'auto']],
            ],
            'C++11/14/17/20 фичи' => [
                ['question' => 'Какое ключевое слово используется для auto-вывода типа?','options' => ['auto', 'var', 'let', 'def']],
                ['question' => 'Какой синтаксис используется для лямбда-функции в C++11?','options' => ['[](){}', 'function', 'lambda', '=>']],
                ['question' => 'Какой класс используется для работы с кортежами в C++11?','options' => ['std::tuple', 'std::pair', 'std::array', 'std::variant']],
                ['question' => 'Какой класс используется для работы с variant в C++17?','options' => ['std::variant', 'std::optional', 'std::any', 'std::tuple']],
                ['question' => 'Какой модуль был добавлен в C++20?','options' => ['import', 'module', 'include', 'using']],
            ],
            'Отладка и профилирование' => [
                ['question' => 'Какой инструмент используется для отладки на Linux?','options' => ['GDB', 'LLDB', 'WinDbg', 'Visual Studio Debugger']],
                ['question' => 'Какой флаг GCC включает все предупреждения?','options' => ['-Wall', '-Werror', '-Wextra', '-Wpedantic']],
                ['question' => 'Какой инструмент используется для профилирования времени выполнения?','options' => ['Valgrind', 'GDB', 'strace', 'ltrace']],
                ['question' => 'Какой инструмент используется для поиска утечек памяти?','options' => ['Valgrind', 'GDB', 'AddressSanitizer', 'MemorySanitizer']],
                ['question' => 'Какой флаг GCC включает AddressSanitizer?','options' => ['-fsanitize=address', '-fsanitize=leak', '-fsanitize=undefined', '-fsanitize=memory']],
            ],
            'Сборка мусора vs ручное управление' => [
                ['question' => 'Какой оператор используется для выделения памяти в C++?','options' => ['new', 'malloc', 'alloc', 'create']],
                ['question' => 'Какой оператор используется для освобождения памяти в C++?','options' => ['delete', 'free', 'release', 'destroy']],
                ['question' => 'Какой умный указатель автоматически освобождает память?','options' => ['std::unique_ptr', 'raw pointer', 'char*', 'void*']],
                ['question' => 'Какой метод освобождает память без удаления указателя?','options' => ['release()', 'reset()', 'free()', 'destroy()']],
                ['question' => 'Какой паттерн используется для автоматического управления памятью?','options' => ['RAII', 'GarbageCollector', 'SmartPointer', 'MemoryManager']],
            ],
            'Проект и портфолио' => [
                ['question' => 'Какой файл обычно используется для документации проекта?','options' => ['README.md', 'DOC.md', 'MANUAL.txt', 'HELP.html']],
                ['question' => 'Какой инструмент используется для контроля версий?','options' => ['Git', 'SVN', 'Mercurial', 'Perforce']],
                ['question' => 'Какой сервис используется для хранения кода?','options' => ['GitHub', 'GitLab', 'Bitbucket', 'SourceForge']],
                ['question' => 'Какой файл используется для описания зависимостей проекта?','options' => ['CMakeLists.txt', 'requirements.txt', 'package.json', 'build.gradle']],
                ['question' => 'Какой инструмент используется для непрерывной интеграции?','options' => ['Jenkins', 'GitHub Actions', 'Travis CI', 'CircleCI']],
            ],
        
        ];
    }

    private function getCppExamData(): array
    {
        return [
            'Какой компилятор является стандартом для C++?' => ['GCC', 'Clang', 'MSVC', 'Intel ICC'],
            'Какой командой компилируется C++ файл?' => ['g++ file.cpp -o output', 'gcc file.cpp -o output', 'compile file.cpp', 'build file.cpp'],
            'Какой тип данных используется для хранения целых чисел?' => ['int', 'float', 'double', 'char'],
            'Какой оператор используется для сравнения?' => ['==', '=', '!=', '<='],
            'Какой модификатор позволяет функции работать без создания объекта?' => ['static', 'const', 'virtual', 'inline'],
            'Какой оператор используется для получения адреса переменной?' => ['&', '*', '@', '#'],
            'Какой класс используется для работы со строками в C++?' => ['std::string', 'String', 'char*', 'CString'],
            'Какое ключевое слово используется для определения класса?' => ['class', 'struct', 'object', 'type'],
            'Какое ключевое слово делает функцию виртуальной?' => ['virtual', 'abstract', 'interface', 'override'],
            'Какой контейнер используется для динамического массива?' => ['std::vector', 'std::array', 'std::list', 'std::deque'],
            'Какой алгоритм используется для сортировки?' => ['std::sort', 'std::order', 'std::arrange', 'std::rank'],
            'Какой умный указатель используется для владения объектом?' => ['std::unique_ptr', 'std::shared_ptr', 'std::weak_ptr', 'std::auto_ptr'],
            'Какой класс используется для создания потока в C++?' => ['std::thread', 'std::process', 'std::task', 'std::worker'],
            'Какой оператор используется для генерации исключения?' => ['throw', 'raise', 'error', 'exception'],
            'Какое ключевое слово определяет шаблон?' => ['template', 'generic', 'typename', 'class'],
            'Какое ключевое слово используется для auto-вывода типа?' => ['auto', 'var', 'let', 'def'],
            'Какой инструмент используется для отладки на Linux?' => ['GDB', 'LLDB', 'WinDbg', 'Visual Studio Debugger'],
            'Какой оператор используется для выделения памяти в C++?' => ['new', 'malloc', 'alloc', 'create'],
            'Какой файл обычно используется для документации проекта?' => ['README.md', 'DOC.md', 'MANUAL.txt', 'HELP.html'],
            'Какой модификатор делает переменную неизменяемой?' => ['const', 'static', 'volatile', 'mutable'],
            'Какой оператор используется для логического И?' => ['&&', '||', '!', '&'],
            'Какой параметр позволяет передавать переменное количество аргументов?' => ['...', 'args', 'params', 'va_list'],
            'Какой модификатор доступа позволяет обращаться к членам класса из любого места?' => ['public', 'private', 'protected', 'internal'],
            'Какой оператор используется для переопределения виртуальной функции?' => ['override', 'virtual', 'new', 'overload'],
            'Какой контейнер используется для ассоциативного массива?' => ['std::map', 'std::vector', 'std::list', 'std::set'],
            'Какой умный указатель используется для разделяемого владения?' => ['std::shared_ptr', 'std::unique_ptr', 'std::weak_ptr', 'std::auto_ptr'],
            'Какой класс используется для синхронизации доступа к данным?' => ['std::mutex', 'std::lock', 'std::semaphore', 'std::barrier'],
            'Какой блок используется для перехвата исключений?' => ['catch', 'except', 'handle', 'trap'],
            'Какой параметр шаблона используется для типов?' => ['typename', 'class', 'type', 'T'],
            'Какой инструмент используется для контроля версий?' => ['Git', 'SVN', 'Mercurial', 'Perforce'],
        
        ];
    }
}
