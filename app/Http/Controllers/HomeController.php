<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Vacancy;
use App\Models\User;
use App\Models\UserApplication;
use App\Models\Lesson;
use App\Models\Review;

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

        $communityStats = [
            'students' => $totalUsers,
            'courses' => $totalCourses,
            'vacancies' => $totalVacancies,
            'lessons' => $totalLessons,
            'applications' => $totalApplications,
        ];

        $reviews = Review::with('user')->where('is_public', true)->latest()->take(6)->get();

        $recentUsers = User::latest()->take(3)->get()->map(function ($user) {
            $initial = mb_substr($user->name, 0, 1);
            $roles = [
                t('home_role_fullstack'),
                t('home_role_frontend'),
                t('home_role_backend'),
                t('home_role_designer'),
                t('home_role_devops'),
                t('home_role_data_analyst'),
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
                'tags' => $tagSets[$idx % count($tagSets)],
                'color' => $colors[$idx % count($colors)],
            ];
        });

        return view('home', compact(
            'courses', 'vacancies',
            'totalUsers', 'totalCourses', 'totalVacancies',
            'totalLessons', 'totalApplications',
            'communityStats', 'recentUsers', 'reviews'
        ));
    }
}
