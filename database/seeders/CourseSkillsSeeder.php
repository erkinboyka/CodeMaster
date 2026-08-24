<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseSkill;
use Illuminate\Database\Seeder;

class CourseSkillsSeeder extends Seeder
{
    public function run(): void
    {
        $levelMap = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3, 'master' => 4];

        $skillsMap = [
            'HTML+CSS' => [['skill_name' => 'HTML5', 'skill_level' => 3], ['skill_name' => 'CSS3', 'skill_level' => 3], ['skill_name' => 'Flexbox', 'skill_level' => 2], ['skill_name' => 'Grid', 'skill_level' => 2], ['skill_name' => 'Responsive Design', 'skill_level' => 2]],
            'JavaScript' => [['skill_name' => 'JavaScript', 'skill_level' => 3], ['skill_name' => 'ES6+', 'skill_level' => 2], ['skill_name' => 'DOM', 'skill_level' => 2], ['skill_name' => 'Async/Await', 'skill_level' => 2]],
            'PHP' => [['skill_name' => 'PHP', 'skill_level' => 3], ['skill_name' => 'OOP', 'skill_level' => 2], ['skill_name' => 'PDO', 'skill_level' => 2], ['skill_name' => 'Sessions', 'skill_level' => 2]],
            'Laravel' => [['skill_name' => 'Laravel', 'skill_level' => 3], ['skill_name' => 'Eloquent', 'skill_level' => 2], ['skill_name' => 'Blade', 'skill_level' => 2], ['skill_name' => 'Migrations', 'skill_level' => 2], ['skill_name' => 'API', 'skill_level' => 2]],
            'MySQL' => [['skill_name' => 'MySQL', 'skill_level' => 3], ['skill_name' => 'SQL', 'skill_level' => 3], ['skill_name' => 'JOINs', 'skill_level' => 2], ['skill_name' => 'Indexes', 'skill_level' => 2]],
            'PostgreSQL' => [['skill_name' => 'PostgreSQL', 'skill_level' => 3], ['skill_name' => 'CTE', 'skill_level' => 2], ['skill_name' => 'Window Functions', 'skill_level' => 2], ['skill_name' => 'JSONB', 'skill_level' => 2]],
            'C++' => [['skill_name' => 'C++', 'skill_level' => 3], ['skill_name' => 'STL', 'skill_level' => 2], ['skill_name' => 'Templates', 'skill_level' => 2], ['skill_name' => 'Memory Management', 'skill_level' => 2]],
            'Python' => [['skill_name' => 'Python', 'skill_level' => 3], ['skill_name' => 'OOP', 'skill_level' => 2], ['skill_name' => 'Decorators', 'skill_level' => 2], ['skill_name' => 'Asyncio', 'skill_level' => 1]],
            'Java' => [['skill_name' => 'Java', 'skill_level' => 3], ['skill_name' => 'Collections', 'skill_level' => 2], ['skill_name' => 'Streams', 'skill_level' => 2], ['skill_name' => 'Spring Boot', 'skill_level' => 2]],
            'C#' => [['skill_name' => 'C#', 'skill_level' => 3], ['skill_name' => 'LINQ', 'skill_level' => 2], ['skill_name' => 'ASP.NET Core', 'skill_level' => 2], ['skill_name' => 'Entity Framework', 'skill_level' => 2]],
            'Git' => [['skill_name' => 'Git', 'skill_level' => 3], ['skill_name' => 'GitHub', 'skill_level' => 2], ['skill_name' => 'Branching', 'skill_level' => 2]],
            'DevOps' => [['skill_name' => 'CI/CD', 'skill_level' => 3], ['skill_name' => 'Monitoring', 'skill_level' => 2], ['skill_name' => 'Infrastructure', 'skill_level' => 2]],
            'UI/UX Design' => [['skill_name' => 'UI Design', 'skill_level' => 3], ['skill_name' => 'UX Research', 'skill_level' => 2], ['skill_name' => 'Prototyping', 'skill_level' => 2], ['skill_name' => 'Figma', 'skill_level' => 3]],
            'React' => [['skill_name' => 'React', 'skill_level' => 3], ['skill_name' => 'Hooks', 'skill_level' => 2], ['skill_name' => 'Redux', 'skill_level' => 2], ['skill_name' => 'React Router', 'skill_level' => 2]],
            'Node.js' => [['skill_name' => 'Node.js', 'skill_level' => 3], ['skill_name' => 'Express', 'skill_level' => 2], ['skill_name' => 'REST API', 'skill_level' => 2], ['skill_name' => 'WebSocket', 'skill_level' => 1]],
            'TypeScript' => [['skill_name' => 'TypeScript', 'skill_level' => 3], ['skill_name' => 'Generics', 'skill_level' => 2], ['skill_name' => 'Utility Types', 'skill_level' => 2]],
            'Docker' => [['skill_name' => 'Docker', 'skill_level' => 3], ['skill_name' => 'Docker Compose', 'skill_level' => 2], ['skill_name' => 'Dockerfile', 'skill_level' => 2]],
            'Kubernetes' => [['skill_name' => 'Kubernetes', 'skill_level' => 3], ['skill_name' => 'Pods', 'skill_level' => 2], ['skill_name' => 'Services', 'skill_level' => 2], ['skill_name' => 'Deployments', 'skill_level' => 2]],
            'Mobile Development' => [['skill_name' => 'Flutter', 'skill_level' => 2], ['skill_name' => 'Mobile UI', 'skill_level' => 2], ['skill_name' => 'REST API', 'skill_level' => 2]],
            'English A1' => [['skill_name' => 'English Grammar', 'skill_level' => 1], ['skill_name' => 'IT Vocabulary', 'skill_level' => 1]],
        ];

        foreach (Course::all() as $course) {
            $skills = $skillsMap[$course->title] ?? [];
            foreach ($skills as $skill) {
                CourseSkill::create(array_merge($skill, ['course_id' => $course->id]));
            }
        }
    }
}
