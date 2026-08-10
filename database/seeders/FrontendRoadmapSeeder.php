<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoadmapNode;

class FrontendRoadmapSeeder extends Seeder
{
    public function run(): void
    {
        RoadmapNode::where('roadmap_title', 'Frontend Developer')->delete();

        $m = fn($l, $u) => ['label' => $l, 'url' => $u];

        $data = [
            // ═══ LEVEL 0 ═══
            [
                'title' => 'Основы интернета', 'topic' => 'Networking', 'course_id' => null, 'is_exam' => false,
                'x' => 60, 'y' => 350, 'deps' => [],
                'materials' => [
                    $m('MDN: How the Internet works', 'https://developer.mozilla.org/ru/docs/Learn/Common_questions/How_does_the_Internet_work'),
                    $m('HTTP — что это', 'https://developer.mozilla.org/ru/docs/Web/HTTP/Overview'),
                    $m('DNS в 2 минуты', 'https://www.youtube.com/watch?v=mpQZurAfN_U'),
                ],
            ],
            // ═══ LEVEL 1 ═══
            [
                'title' => 'HTML', 'topic' => 'Markup', 'course_id' => 1, 'is_exam' => false,
                'x' => 320, 'y' => 200, 'deps' => [1],
                'materials' => [],
            ],
            [
                'title' => 'CSS', 'topic' => 'Styling', 'course_id' => 1, 'is_exam' => false,
                'x' => 320, 'y' => 350, 'deps' => [1],
                'materials' => [],
            ],
            [
                'title' => 'Терминал и CLI', 'topic' => 'Tooling', 'course_id' => null, 'is_exam' => false,
                'x' => 320, 'y' => 500, 'deps' => [1],
                'materials' => [
                    $m('Learn the Command Line', 'https://www.codecademy.com/learn/learn-the-command-line'),
                    $m('Linux Journey', 'https://linuxjourney.com/'),
                    $m('Bash Reference', 'https://www.gnu.org/software/bash/manual/'),
                ],
            ],
            // ═══ LEVEL 2 ═══
            [
                'title' => 'HTML5 Семантика', 'topic' => 'HTML', 'course_id' => null, 'is_exam' => false,
                'x' => 580, 'y' => 100, 'deps' => [2],
                'materials' => [
                    $m('MDN: Semantic HTML', 'https://developer.mozilla.org/ru/docs/Glossary/Semantics'),
                    $m('HTML5 Doctor', 'http://html5doctor.com/'),
                    $m('Semantics vs Structure', 'https://www.youtube.com/watch?v=bq8d1Q9x7dc'),
                ],
            ],
            [
                'title' => 'HTML Формы', 'topic' => 'HTML', 'course_id' => null, 'is_exam' => false,
                'x' => 580, 'y' => 200, 'deps' => [2],
                'materials' => [
                    $m('MDN: Forms Guide', 'https://developer.mozilla.org/ru/docs/Learn/Forms'),
                    $m('Form validation', 'https://developer.mozilla.org/ru/docs/Learn/Forms/Form_validation'),
                    $m('HTML Academy: Forms', 'https://htmlacademy.ru/courses/html/forms'),
                ],
            ],
            [
                'title' => 'Таблицы и списки', 'topic' => 'HTML', 'course_id' => null, 'is_exam' => false,
                'x' => 580, 'y' => 300, 'deps' => [2],
                'materials' => [
                    $m('MDN: HTML Tables', 'https://developer.mozilla.org/ru/docs/Learn/HTML/Tables'),
                    $m('MDN: Lists', 'https://developer.mozilla.org/ru/docs/Learn/HTML/HTML_lists'),
                ],
            ],
            [
                'title' => 'CSS Box Model', 'topic' => 'CSS', 'course_id' => null, 'is_exam' => false,
                'x' => 580, 'y' => 400, 'deps' => [3],
                'materials' => [
                    $m('MDN: Box Model', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_Box_Model'),
                    $m('Box Sizing — CSS Tricks', 'https://css-tricks.com/box-sizing/'),
                    $m('Everything you need to know about Box Model', 'https://www.youtube.com/watch?v=rIO5326FgPE'),
                ],
            ],
            [
                'title' => 'CSS Flexbox', 'topic' => 'CSS', 'course_id' => null, 'is_exam' => false,
                'x' => 580, 'y' => 500, 'deps' => [3],
                'materials' => [
                    $m('Flexbox Froggy', 'https://flexboxfroggy.com/#ru'),
                    $m('MDN: Flexbox', 'https://developer.mozilla.org/ru/docs/Learn/CSS/CSS_layout/Flexbox'),
                    $m('CSS-Tricks: Flexbox Guide', 'https://css-tricks.com/snippets/css/a-guide-to-flexbox/'),
                ],
            ],
            [
                'title' => 'Git', 'topic' => 'VCS', 'course_id' => 11, 'is_exam' => false,
                'x' => 580, 'y' => 600, 'deps' => [4],
                'materials' => [],
            ],
            [
                'title' => 'Linux основы', 'topic' => 'OS', 'course_id' => 12, 'is_exam' => false,
                'x' => 580, 'y' => 700, 'deps' => [4],
                'materials' => [],
            ],
            // ═══ LEVEL 3 ═══
            [
                'title' => 'Адаптивный дизайн', 'topic' => 'CSS', 'course_id' => null, 'is_exam' => false,
                'x' => 840, 'y' => 60, 'deps' => [5, 8],
                'materials' => [
                    $m('MDN: Responsive Design', 'https://developer.mozilla.org/ru/docs/Learn/CSS/CSS_layout/Responsive_Design'),
                    $m('A List Apart: Responsive', 'https://alistapart.com/article/responsive-web-design/'),
                    $m('Google: Responsive', 'https://web.dev/responsive-web-design-basics/'),
                ],
            ],
            [
                'title' => 'Доступность (a11y)', 'topic' => 'A11y', 'course_id' => null, 'is_exam' => false,
                'x' => 840, 'y' => 160, 'deps' => [5],
                'materials' => [
                    $m('MDN: Accessibility', 'https://developer.mozilla.org/ru/docs/Learn/Accessibility'),
                    $m('WAI Tutorials', 'https://www.w3.org/WAI/tutorials/'),
                    $m('A11y Project', 'https://www.a11yproject.com/'),
                    $m('WebAIM', 'https://webaim.org/'),
                ],
            ],
            [
                'title' => 'CSS Grid', 'topic' => 'CSS', 'course_id' => null, 'is_exam' => false,
                'x' => 840, 'y' => 260, 'deps' => [9],
                'materials' => [
                    $m('Grid Garden', 'https://cssgridgarden.com/#ru'),
                    $m('MDN: Grid Layout', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_Grid_Layout'),
                    $m('CSS-Tricks: Grid Guide', 'https://css-tricks.com/snippets/css/complete-guide-grid/'),
                ],
            ],
            [
                'title' => 'CSS Анимации', 'topic' => 'CSS', 'course_id' => null, 'is_exam' => false,
                'x' => 840, 'y' => 360, 'deps' => [8],
                'materials' => [
                    $m('MDN: Animations', 'https://developer.mozilla.org/ru/docs/Web/CSS/CSS_Animations'),
                    $m('CSS-Tricks: Transitions', 'https://css-tricks.com/almanac/properties/t/transition/'),
                    $m('Animate.css', 'https://animate.style/'),
                ],
            ],
            [
                'title' => 'Препроцессоры (Sass)', 'topic' => 'CSS', 'course_id' => null, 'is_exam' => false,
                'x' => 840, 'y' => 460, 'deps' => [8],
                'materials' => [
                    $m('Sass Basics', 'https://sass-lang.com/guide'),
                    $m('Sass Playground', 'https://sass-lang.com/playground/'),
                    $m('MDN: Sass', 'https://developer.mozilla.org/ru/docs/Web/CSS/Preprocessor'),
                ],
            ],
            [
                'title' => 'JavaScript Основы', 'topic' => 'Language', 'course_id' => 2, 'is_exam' => false,
                'x' => 840, 'y' => 560, 'deps' => [6, 7],
                'materials' => [],
            ],
            [
                'title' => 'Bash Скрипты', 'topic' => 'Tooling', 'course_id' => null, 'is_exam' => false,
                'x' => 840, 'y' => 660, 'deps' => [11],
                'materials' => [
                    $m('Bash Scripting Tutorial', 'https://ryanstutorials.net/bash-scripting-tutorial/'),
                    $m('Advanced Bash', 'https://tldp.org/LDP/abs/html/'),
                ],
            ],
            // ═══ LEVEL 4 ═══
            [
                'title' => 'Медиа-запросы', 'topic' => 'CSS', 'course_id' => null, 'is_exam' => false,
                'x' => 1100, 'y' => 60, 'deps' => [12],
                'materials' => [
                    $m('MDN: Media Queries', 'https://developer.mozilla.org/ru/docs/Web/CSS/Media_Queries'),
                    $m('Can I Use', 'https://caniuse.com/'),
                ],
            ],
            [
                'title' => 'CSS-in-JS', 'topic' => 'CSS', 'course_id' => null, 'is_exam' => false,
                'x' => 1100, 'y' => 160, 'deps' => [14, 16],
                'materials' => [
                    $m('Styled Components', 'https://styled-components.com/'),
                    $m('Emotion', 'https://emotion.sh/docs/introduction'),
                    $m('CSS-in-JS libs comparison', 'https://github.com/tuchk4/awesome-css-in-js'),
                ],
            ],
            [
                'title' => 'JavaScript OOP', 'topic' => 'Language', 'course_id' => null, 'is_exam' => false,
                'x' => 1100, 'y' => 260, 'deps' => [17],
                'materials' => [
                    $m('MDN: Classes', 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Classes'),
                    $m('MDN: Prototypes', 'https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Global_Objects/Prototype'),
                    $m('JavaScript.info: OOP', 'https://javascript.info/class'),
                ],
            ],
            [
                'title' => 'JavaScript DOM', 'topic' => 'Language', 'course_id' => null, 'is_exam' => false,
                'x' => 1100, 'y' => 360, 'deps' => [17],
                'materials' => [
                    $m('MDN: DOM Introduction', 'https://developer.mozilla.org/ru/docs/Web/API/Document_Object_Model'),
                    $m('JavaScript.info: DOM', 'https://javascript.info/document'),
                    $m('DOM Enlightenment', 'http://domenlightenment.com/'),
                ],
            ],
            [
                'title' => 'ES6+ Фичи', 'topic' => 'Language', 'course_id' => null, 'is_exam' => false,
                'x' => 1100, 'y' => 460, 'deps' => [17],
                'materials' => [
                    $m('ES6 Features', 'https://es6-features.org/'),
                    $m('JavaScript.info: ES6', 'https://javascript.info/destructuring'),
                    $m('Babel: Learn ES2015', 'https://babeljs.io/learn-es2015/'),
                ],
            ],
            [
                'title' => 'Асинхронный JS', 'topic' => 'Language', 'course_id' => null, 'is_exam' => false,
                'x' => 1100, 'y' => 560, 'deps' => [17],
                'materials' => [
                    $m('JavaScript.info: Promises', 'https://javascript.info/promise-basics'),
                    $m('MDN: Async/Await', 'https://developer.mozilla.org/ru/docs/Learn/JavaScript/Asynchronous'),
                    $m('Promise Playground', 'https://promisesaplus.com/'),
                ],
            ],
            [
                'title' => 'Node.js Основы', 'topic' => 'Runtime', 'course_id' => 15, 'is_exam' => false,
                'x' => 1100, 'y' => 660, 'deps' => [18, 23],
                'materials' => [],
            ],
            // ═══ LEVEL 5 ═══
            [
                'title' => 'TypeScript', 'topic' => 'Language', 'course_id' => 16, 'is_exam' => false,
                'x' => 1360, 'y' => 120, 'deps' => [21, 24],
                'materials' => [],
            ],
            [
                'title' => 'React', 'topic' => 'Framework', 'course_id' => 14, 'is_exam' => false,
                'x' => 1360, 'y' => 240, 'deps' => [21, 22, 24],
                'materials' => [],
            ],
            [
                'title' => 'Vue.js', 'topic' => 'Framework', 'course_id' => null, 'is_exam' => false,
                'x' => 1360, 'y' => 360, 'deps' => [21, 22, 24],
                'materials' => [
                    $m('Vue.js Official Guide', 'https://vuejs.org/guide/introduction.html'),
                    $m('Vue Mastery', 'https://www.vuemastery.com/'),
                    $m('Vue School', 'https://vueschool.io/'),
                ],
            ],
            [
                'title' => 'REST API', 'topic' => 'Backend', 'course_id' => null, 'is_exam' => false,
                'x' => 1360, 'y' => 480, 'deps' => [24, 25],
                'materials' => [
                    $m('RESTful API Design', 'https://restfulapi.net/'),
                    $m('MDN: HTTP Methods', 'https://developer.mozilla.org/ru/docs/Web/HTTP/Methods'),
                    $m('JSONPlaceholder', 'https://jsonplaceholder.typicode.com/'),
                    $m('Postman Learning Center', 'https://learning.postman.com/'),
                ],
            ],
            [
                'title' => 'Docker', 'topic' => 'DevOps', 'course_id' => 17, 'is_exam' => false,
                'x' => 1360, 'y' => 600, 'deps' => [25],
                'materials' => [],
            ],
            // ═══ LEVEL 6 ═══
            [
                'title' => 'React Router', 'topic' => 'Ecosystem', 'course_id' => null, 'is_exam' => false,
                'x' => 1620, 'y' => 80, 'deps' => [28],
                'materials' => [
                    $m('React Router Docs', 'https://reactrouter.com/en/main'),
                    $m('React Router Tutorial', 'https://reactrouter.com/en/main/start/tutorial'),
                ],
            ],
            [
                'title' => 'State Management', 'topic' => 'Ecosystem', 'course_id' => null, 'is_exam' => false,
                'x' => 1620, 'y' => 180, 'deps' => [28],
                'materials' => [
                    $m('Redux Toolkit', 'https://redux-toolkit.js.org/'),
                    $m('Zustand', 'https://github.com/pmndrs/zustand'),
                    $m('Pinia (Vue)', 'https://pinia.vuejs.org/'),
                    $m('MobX', 'https://mobx.js.org/'),
                ],
            ],
            [
                'title' => 'Next.js / Nuxt', 'topic' => 'Framework', 'course_id' => null, 'is_exam' => false,
                'x' => 1620, 'y' => 280, 'deps' => [28, 29],
                'materials' => [
                    $m('Next.js Learn', 'https://nextjs.org/learn'),
                    $m('Next.js Docs', 'https://nextjs.org/docs'),
                    $m('Nuxt 3 Docs', 'https://nuxt.com/docs'),
                ],
            ],
            [
                'title' => 'Тестирование', 'topic' => 'Quality', 'course_id' => null, 'is_exam' => false,
                'x' => 1620, 'y' => 400, 'deps' => [28, 29],
                'materials' => [
                    $m('Jest Docs', 'https://jestjs.io/'),
                    $m('Testing Library', 'https://testing-library.com/'),
                    $m('Cypress', 'https://www.cypress.io/'),
                    $m('Vitest', 'https://vitest.dev/'),
                ],
            ],
            [
                'title' => 'Build Tools', 'topic' => 'Tooling', 'course_id' => null, 'is_exam' => false,
                'x' => 1620, 'y' => 500, 'deps' => [28],
                'materials' => [
                    $m('Vite Guide', 'https://vitejs.dev/guide/'),
                    $m('Webpack Docs', 'https://webpack.js.org/'),
                    $m('esbuild', 'https://esbuild.github.io/'),
                ],
            ],
            [
                'title' => 'CI/CD', 'topic' => 'DevOps', 'course_id' => null, 'is_exam' => false,
                'x' => 1620, 'y' => 620, 'deps' => [30],
                'materials' => [
                    $m('GitHub Actions', 'https://docs.github.com/en/actions'),
                    $m('GitLab CI', 'https://docs.gitlab.com/ee/ci/'),
                    $m('Jenkins', 'https://www.jenkins.io/'),
                ],
            ],
            // ═══ LEVEL 7 ═══
            [
                'title' => 'Performance', 'topic' => 'Production', 'course_id' => null, 'is_exam' => false,
                'x' => 1880, 'y' => 160, 'deps' => [34],
                'materials' => [
                    $m('Web.dev Performance', 'https://web.dev/performance/'),
                    $m('Lighthouse', 'https://developer.chrome.com/docs/lighthouse/overview/'),
                    $m('Core Web Vitals', 'https://web.dev/vitals/'),
                    $m('PageSpeed Insights', 'https://pagespeed.web.dev/'),
                ],
            ],
            [
                'title' => 'Web Security', 'topic' => 'Production', 'course_id' => null, 'is_exam' => false,
                'x' => 1880, 'y' => 280, 'deps' => [34],
                'materials' => [
                    $m('OWASP Top 10', 'https://owasp.org/Top10/'),
                    $m('MDN: Web Security', 'https://developer.mozilla.org/ru/docs/Web/Security'),
                    $m('Content Security Policy', 'https://web.dev/content-security-policy/'),
                ],
            ],
            [
                'title' => 'SEO Basics', 'topic' => 'Production', 'course_id' => null, 'is_exam' => false,
                'x' => 1880, 'y' => 400, 'deps' => [34, 35],
                'materials' => [
                    $m('Google SEO Starter', 'https://developers.google.com/search/docs/fundamentals/seo-starter-guide'),
                    $m('MDN: Meta Tags', 'https://developer.mozilla.org/ru/docs/Web/HTML/Element/meta'),
                    $m('Schema.org', 'https://schema.org/'),
                ],
            ],
            [
                'title' => 'PWA', 'topic' => 'Production', 'course_id' => null, 'is_exam' => false,
                'x' => 1880, 'y' => 520, 'deps' => [34],
                'materials' => [
                    $m('PWA Guide', 'https://web.dev/articles/what-are-pwas'),
                    $m('Workbox', 'https://developer.chrome.com/docs/workbox/'),
                    $m('PWA Builder', 'https://www.pwabuilder.com/'),
                ],
            ],
            // ═══ EXAMS ═══
            [
                'title' => 'HTML/CSS Экзамен', 'topic' => 'Exam', 'course_id' => null, 'is_exam' => true,
                'x' => 1880, 'y' => 640, 'deps' => [36, 37, 38],
                'materials' => [],
            ],
        ];

        $idMap = [];
        $order = 0;
        foreach ($data as $d) {
            $order++;
            $node = RoadmapNode::create([
                'title' => $d['title'],
                'topic' => $d['topic'],
                'course_id' => $d['course_id'],
                'is_exam' => $d['is_exam'],
                'roadmap_title' => 'Frontend Developer',
                'x' => $d['x'],
                'y' => $d['y'],
                'materials' => $d['materials'],
                'deps' => null,
            ]);
            $idMap[$order] = $node->id;
        }

        $order = 0;
        foreach ($data as $d) {
            $order++;
            if (!empty($d['deps'])) {
                $deps = array_map(fn($dep) => $idMap[$dep] ?? $dep, $d['deps']);
                RoadmapNode::where('id', $idMap[$order])->update(['deps' => $deps]);
            }
        }
    }
}
