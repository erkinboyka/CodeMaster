<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoadmapNode;

class AllRoadmapsSeeder extends Seeder
{
    public function run(): void
    {
        $m = fn($l, $u) => ['label' => $l, 'url' => $u];

        // ═══════════════════════════════════════════════════════
        // BACKEND DEVELOPER
        // ═══════════════════════════════════════════════════════
        RoadmapNode::where('roadmap_title', 'Backend Developer')->delete();
        $this->seedRoadmap('Backend Developer', [
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

        // ═══════════════════════════════════════════════════════
        // FULLSTACK DEVELOPER
        // ═══════════════════════════════════════════════════════
        RoadmapNode::where('roadmap_title', 'Fullstack Developer')->delete();
        $this->seedRoadmap('Fullstack Developer', [
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

        // ═══════════════════════════════════════════════════════
        // DEVOPS ENGINEER
        // ═══════════════════════════════════════════════════════
        RoadmapNode::where('roadmap_title', 'DevOps Engineer')->delete();
        $this->seedRoadmap('DevOps Engineer', [
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

        // ═══════════════════════════════════════════════════════
        // PYTHON DEVELOPER
        // ═══════════════════════════════════════════════════════
        RoadmapNode::where('roadmap_title', 'Python Developer')->delete();
        $this->seedRoadmap('Python Developer', [
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

        // ═══════════════════════════════════════════════════════
        // UI/UX DESIGNER
        // ═══════════════════════════════════════════════════════
        RoadmapNode::where('roadmap_title', 'UI/UX Designer')->delete();
        $this->seedRoadmap('UI/UX Designer', [
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

        // ═══════════════════════════════════════════════════════
        // MOBILE DEVELOPER
        // ═══════════════════════════════════════════════════════
        RoadmapNode::where('roadmap_title', 'Mobile Developer')->delete();
        $this->seedRoadmap('Mobile Developer', [
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

        // ═══════════════════════════════════════════════════════
        // C++ DEVELOPER
        // ═══════════════════════════════════════════════════════
        RoadmapNode::where('roadmap_title', 'C++ Developer')->delete();
        $this->seedRoadmap('C++ Developer', [
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
    }

    private function seedRoadmap(string $title, array $data): void
    {
        $idMap = [];
        $order = 0;
        foreach ($data as $d) {
            $order++;
            $node = RoadmapNode::create([
                'title' => $d['t'],
                'topic' => $d['tp'],
                'course_id' => $d['c'] ?? null,
                'is_exam' => $d['is_exam'] ?? false,
                'roadmap_title' => $title,
                'x' => $d['x'],
                'y' => $d['y'],
                'materials' => $d['m'] ?? [],
                'deps' => null,
            ]);
            $idMap[$order] = $node->id;
        }

        $order = 0;
        foreach ($data as $d) {
            $order++;
            if (!empty($d['d'])) {
                $deps = array_map(fn($dep) => $idMap[$dep] ?? $dep, $d['d']);
                RoadmapNode::where('id', $idMap[$order])->update(['deps' => $deps]);
            }
        }
    }
}
