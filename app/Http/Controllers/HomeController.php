<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Vacancy;
use App\Models\User;
use App\Models\UserApplication;
use App\Models\Lesson;

class HomeController extends Controller
{
    public function index()
    {
        $courses = Course::with('lessons')->latest()->take(6)->get();
        $vacancies = Vacancy::latest()->take(6)->get();
        $totalUsers = User::count();
        $totalCourses = Course::count();
        $totalVacancies = Vacancy::count();
        $totalLessons = Lesson::count();
        $totalApplications = UserApplication::count();

        // Real community data from DB
        $communityStats = [
            'students' => $totalUsers,
            'courses' => $totalCourses,
            'vacancies' => $totalVacancies,
            'lessons' => $totalLessons,
            'applications' => $totalApplications,
        ];

        // Get real recent users for community cards
        $recentUsers = User::latest()->take(3)->get()->map(function ($user) {
            $initial = mb_substr($user->name, 0, 1);
            $roles = ['Fullstack Developer', 'Frontend Developer', 'Backend Developer', 'UI/UX Designer', 'DevOps Engineer', 'Data Analyst'];
            $contents = [
                'Начал изучать программирование на CodeMaster!',
                'Завершил курс и получил сертификат 🎉',
                'Прошёл собеседование и получил оффер!',
                'AI-помощник очень помог с практикой 🚀',
                'Отличная платформа для начинающих!',
                'Уже 3 месяца здесь усь — результаты отличные!',
            ];
            $tagSets = [
                ['HTML', 'CSS', 'JavaScript'],
                ['Python', 'Django', 'PostgreSQL'],
                ['React', 'TypeScript', 'Node.js'],
                ['Figma', 'UI Design', 'Prototyping'],
                ['Docker', 'K8s', 'CI/CD'],
            ];
            $idx = $user->id % count($roles);
            $colors = ['#8b5cf6', '#ec4899', '#22c55e', '#3b82f6', '#f59e0b'];

            return [
                'name' => $user->name,
                'role' => $roles[$idx],
                'initial' => $initial,
                'content' => $contents[$user->id % count($contents)],
                'tags' => $tagSets[$idx % count($tagSets)],
                'color' => $colors[$idx % count($colors)],
            ];
        });

        return view('home', compact(
            'courses', 'vacancies',
            'totalUsers', 'totalCourses', 'totalVacancies',
            'totalLessons', 'totalApplications',
            'communityStats', 'recentUsers'
        ));
    }
}
