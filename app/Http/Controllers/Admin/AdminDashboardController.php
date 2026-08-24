<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Vacancy;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalCourses = Course::count();
        $totalVacancies = Vacancy::count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $blockedUsers = User::where('is_blocked', true)->count();

        return view('admin.dashboard', compact('totalUsers', 'totalCourses', 'totalVacancies', 'newUsersToday', 'blockedUsers'));
    }
}
