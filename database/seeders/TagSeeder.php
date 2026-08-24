<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'JavaScript', 'slug' => 'javascript', 'posts_count' => 45],
            ['name' => 'React', 'slug' => 'react', 'posts_count' => 32],
            ['name' => 'Laravel', 'slug' => 'laravel', 'posts_count' => 28],
            ['name' => 'Python', 'slug' => 'python', 'posts_count' => 38],
            ['name' => 'PHP', 'slug' => 'php', 'posts_count' => 22],
            ['name' => 'Node.js', 'slug' => 'nodejs', 'posts_count' => 18],
            ['name' => 'TypeScript', 'slug' => 'typescript', 'posts_count' => 25],
            ['name' => 'Docker', 'slug' => 'docker', 'posts_count' => 15],
            ['name' => 'Kubernetes', 'slug' => 'kubernetes', 'posts_count' => 10],
            ['name' => 'DevOps', 'slug' => 'devops', 'posts_count' => 20],
            ['name' => 'Frontend', 'slug' => 'frontend', 'posts_count' => 40],
            ['name' => 'Backend', 'slug' => 'backend', 'posts_count' => 35],
            ['name' => 'CSS', 'slug' => 'css', 'posts_count' => 27],
            ['name' => 'HTML', 'slug' => 'html', 'posts_count' => 24],
            ['name' => 'Git', 'slug' => 'git', 'posts_count' => 12],
            ['name' => 'MySQL', 'slug' => 'mysql', 'posts_count' => 16],
            ['name' => 'PostgreSQL', 'slug' => 'postgresql', 'posts_count' => 14],
            ['name' => 'Java', 'slug' => 'java', 'posts_count' => 21],
            ['name' => 'C++', 'slug' => 'cpp', 'posts_count' => 11],
            ['name' => 'C#', 'slug' => 'csharp', 'posts_count' => 13],
            ['name' => 'UI/UX', 'slug' => 'ui-ux', 'posts_count' => 19],
            ['name' => 'Алгоритмы', 'slug' => 'algorithms', 'posts_count' => 30],
            ['name' => 'Собеседование', 'slug' => 'interview', 'posts_count' => 34],
            ['name' => 'Карьера', 'slug' => 'career', 'posts_count' => 26],
            ['name' => 'Новичкам', 'slug' => 'beginners', 'posts_count' => 42],
            ['name' => 'Проекты', 'slug' => 'projects', 'posts_count' => 17],
            ['name' => 'Code Review', 'slug' => 'code-review', 'posts_count' => 9],
            ['name' => 'Тестирование', 'slug' => 'testing', 'posts_count' => 8],
            ['name' => 'Безопасность', 'slug' => 'security', 'posts_count' => 7],
            ['name' => 'AI/ML', 'slug' => 'ai-ml', 'posts_count' => 23],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['slug' => $tag['slug']], $tag);
        }
    }
}
