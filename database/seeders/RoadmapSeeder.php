<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoadmapNode;

class RoadmapSeeder extends Seeder
{
    public function run(): void
    {
        // Backend Developer
        $this->createNodes('Backend Developer', [
            ['title' => 'PHP Basics',          'course_id' => 3,  'topic' => 'Языки',      'x' => 1, 'y' => 1],
            ['title' => 'PHP OOP',             'course_id' => 3,  'topic' => 'Языки',      'x' => 2, 'y' => 1],
            ['title' => 'PHP Exam',            'course_id' => null,'topic' => 'Экзамен',   'x' => 3, 'y' => 1, 'is_exam' => true],
            ['title' => 'MySQL Basics',        'course_id' => 5,  'topic' => 'Базы данных','x' => 1, 'y' => 2],
            ['title' => 'MySQL Advanced',      'course_id' => 5,  'topic' => 'Базы данных','x' => 2, 'y' => 2],
            ['title' => 'PostgreSQL',          'course_id' => 6,  'topic' => 'Базы данных','x' => 3, 'y' => 2],
            ['title' => 'Laravel Basics',      'course_id' => 4,  'topic' => 'Фреймворки', 'x' => 1, 'y' => 3],
            ['title' => 'Laravel Advanced',    'course_id' => 4,  'topic' => 'Фреймворки', 'x' => 2, 'y' => 3],
            ['title' => 'REST APIs',           'course_id' => 4,  'topic' => 'API',         'x' => 3, 'y' => 3],
            ['title' => 'Node.js',             'course_id' => 15, 'topic' => 'JavaScript',  'x' => 1, 'y' => 4],
            ['title' => 'Backend Final Exam',  'course_id' => null,'topic' => 'Экзамен',   'x' => 2, 'y' => 4, 'is_exam' => true],
        ]);

        // Fullstack Developer
        $this->createNodes('Fullstack Developer', [
            ['title' => 'HTML & CSS',          'course_id' => 1,  'topic' => 'Frontend',    'x' => 1, 'y' => 1],
            ['title' => 'JavaScript',          'course_id' => 2,  'topic' => 'Frontend',    'x' => 2, 'y' => 1],
            ['title' => 'React',               'course_id' => 14, 'topic' => 'Frontend',    'x' => 3, 'y' => 1],
            ['title' => 'TypeScript',          'course_id' => 16, 'topic' => 'Frontend',    'x' => 4, 'y' => 1],
            ['title' => 'PHP',                 'course_id' => 3,  'topic' => 'Backend',     'x' => 1, 'y' => 2],
            ['title' => 'MySQL',               'course_id' => 5,  'topic' => 'Backend',     'x' => 2, 'y' => 2],
            ['title' => 'Laravel',             'course_id' => 4,  'topic' => 'Backend',     'x' => 3, 'y' => 2],
            ['title' => 'Node.js',             'course_id' => 15, 'topic' => 'Backend',     'x' => 4, 'y' => 2],
            ['title' => 'Git',                 'course_id' => 11, 'topic' => 'DevOps',      'x' => 1, 'y' => 3],
            ['title' => 'Docker',              'course_id' => 17, 'topic' => 'DevOps',      'x' => 2, 'y' => 3],
            ['title' => 'Fullstack Final Exam','course_id' => null,'topic' => 'Экзамен',   'x' => 3, 'y' => 3, 'is_exam' => true],
        ]);

        // DevOps Engineer
        $this->createNodes('DevOps Engineer', [
            ['title' => 'Git',                 'course_id' => 11, 'topic' => 'Версионирование','x' => 1, 'y' => 1],
            ['title' => 'Git Advanced',        'course_id' => 11, 'topic' => 'Версионирование','x' => 2, 'y' => 1],
            ['title' => 'Linux Basics',        'course_id' => 12, 'topic' => 'ОС',          'x' => 1, 'y' => 2],
            ['title' => 'Bash Scripting',      'course_id' => 12, 'topic' => 'ОС',          'x' => 2, 'y' => 2],
            ['title' => 'Docker',              'course_id' => 17, 'topic' => 'Контейнеризация','x' => 1, 'y' => 3],
            ['title' => 'Docker Compose',      'course_id' => 17, 'topic' => 'Контейнеризация','x' => 2, 'y' => 3],
            ['title' => 'Kubernetes',          'course_id' => 18, 'topic' => 'Оркестрация', 'x' => 1, 'y' => 4],
            ['title' => 'CI/CD Pipelines',     'course_id' => 12, 'topic' => 'Автоматизация','x' => 2, 'y' => 4],
            ['title' => 'DevOps Final Exam',   'course_id' => null,'topic' => 'Экзамен',   'x' => 1, 'y' => 5, 'is_exam' => true],
        ]);

        // Python Developer
        $this->createNodes('Python Developer', [
            ['title' => 'Python Basics',       'course_id' => 8,  'topic' => 'Языки',      'x' => 1, 'y' => 1],
            ['title' => 'Python OOP',          'course_id' => 8,  'topic' => 'Языки',      'x' => 2, 'y' => 1],
            ['title' => 'Python Exam',         'course_id' => null,'topic' => 'Экзамен',   'x' => 3, 'y' => 1, 'is_exam' => true],
            ['title' => 'Django Basics',       'course_id' => 8,  'topic' => 'Фреймворки', 'x' => 1, 'y' => 2],
            ['title' => 'Django Advanced',     'course_id' => 8,  'topic' => 'Фреймворки', 'x' => 2, 'y' => 2],
            ['title' => 'Flask',               'course_id' => 8,  'topic' => 'Фреймворки', 'x' => 3, 'y' => 2],
            ['title' => 'MySQL for Python',    'course_id' => 5,  'topic' => 'Базы данных','x' => 1, 'y' => 3],
            ['title' => 'REST APIs',           'course_id' => 8,  'topic' => 'API',         'x' => 2, 'y' => 3],
            ['title' => 'Python Final Exam',   'course_id' => null,'topic' => 'Экзамен',   'x' => 1, 'y' => 4, 'is_exam' => true],
        ]);

        // UI/UX Designer
        $this->createNodes('UI/UX Designer', [
            ['title' => 'Design Fundamentals',  'course_id' => 13, 'topic' => 'Основы',      'x' => 1, 'y' => 1],
            ['title' => 'Color Theory',         'course_id' => 13, 'topic' => 'Основы',      'x' => 2, 'y' => 1],
            ['title' => 'Typography',           'course_id' => 13, 'topic' => 'Основы',      'x' => 3, 'y' => 1],
            ['title' => 'Figma Basics',         'course_id' => 13, 'topic' => 'Инструменты', 'x' => 1, 'y' => 2],
            ['title' => 'Figma Components',     'course_id' => 13, 'topic' => 'Инструменты', 'x' => 2, 'y' => 2],
            ['title' => 'User Research',        'course_id' => 13, 'topic' => 'UX',          'x' => 1, 'y' => 3],
            ['title' => 'Prototyping',          'course_id' => 13, 'topic' => 'UX',          'x' => 2, 'y' => 3],
            ['title' => 'HTML & CSS Basics',    'course_id' => 1,  'topic' => 'Для дизайнеров','x' => 1, 'y' => 4],
            ['title' => 'Design Final Exam',    'course_id' => null,'topic' => 'Экзамен',   'x' => 2, 'y' => 4, 'is_exam' => true],
        ]);

        // Mobile Developer
        $this->createNodes('Mobile Developer', [
            ['title' => 'JavaScript Basics',   'course_id' => 2,  'topic' => 'Основы',      'x' => 1, 'y' => 1],
            ['title' => 'React Basics',        'course_id' => 14, 'topic' => 'Основы',      'x' => 2, 'y' => 1],
            ['title' => 'React Native',        'course_id' => 19, 'topic' => 'Фреймворки', 'x' => 1, 'y' => 2],
            ['title' => 'Flutter Basics',      'course_id' => 19, 'topic' => 'Фреймворки', 'x' => 2, 'y' => 2],
            ['title' => 'Mobile UI/UX',        'course_id' => 13, 'topic' => 'Дизайн',     'x' => 1, 'y' => 3],
            ['title' => 'REST APIs',           'course_id' => 4,  'topic' => 'Backend',     'x' => 2, 'y' => 3],
            ['title' => 'Mobile Final Exam',   'course_id' => null,'topic' => 'Экзамен',   'x' => 1, 'y' => 4, 'is_exam' => true],
        ]);

        // C++ Developer
        $this->createNodes('C++ Developer', [
            ['title' => 'C++ Basics',          'course_id' => 7,  'topic' => 'Языки',      'x' => 1, 'y' => 1],
            ['title' => 'C++ OOP',             'course_id' => 7,  'topic' => 'Языки',      'x' => 2, 'y' => 1],
            ['title' => 'C++ Templates',       'course_id' => 7,  'topic' => 'Продвинутый','x' => 1, 'y' => 2],
            ['title' => 'STL',                 'course_id' => 7,  'topic' => 'Продвинутый','x' => 2, 'y' => 2],
            ['title' => 'Data Structures',     'course_id' => 7,  'topic' => 'Алгоритмы',  'x' => 1, 'y' => 3],
            ['title' => 'Algorithms',          'course_id' => 7,  'topic' => 'Алгоритмы',  'x' => 2, 'y' => 3],
            ['title' => 'C++ Final Exam',      'course_id' => null,'topic' => 'Экзамен',   'x' => 1, 'y' => 4, 'is_exam' => true],
        ]);
    }

    private function createNodes(string $roadmapTitle, array $nodes): void
    {
        foreach ($nodes as $node) {
            RoadmapNode::create(array_merge($node, [
                'roadmap_title' => $roadmapTitle,
                'materials' => null,
                'deps' => null,
            ]));
        }
    }
}
