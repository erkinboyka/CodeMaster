<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\UserCourseProgress;
use App\Models\Notification;
use App\Models\UserApplication;
use App\Models\Certificate;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        $stats = UserCourseProgress::where('user_id', $userId)
            ->selectRaw('
                SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_courses,
                SUM(CASE WHEN completed = 0 THEN 1 ELSE 0 END) as in_progress_courses
            ')
            ->first();

        $completedCourses = $stats->completed_courses ?? 0;
        $inProgressCourses = $stats->in_progress_courses ?? 0;

        $applications = UserApplication::where('user_id', $userId)->count();

        $notifications = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('notification_time', 'desc')
            ->take(10)
            ->get();

        $recentActivity = UserActivity::where('user_id', $userId)
            ->orderBy('activity_time', 'desc')
            ->take(10)
            ->get();

        $certificates = Certificate::where('user_id', $userId)
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $enrolledCourseIds = UserCourseProgress::where('user_id', $userId)
            ->pluck('course_id')
            ->toArray();

        $recommendedCourses = Course::whereNotIn('id', $enrolledCourseIds)
            ->with('lessons')
            ->take(4)
            ->get();

        $inProgressCourseList = UserCourseProgress::where('user_id', $userId)
            ->where('completed', false)
            ->with('course')
            ->take(5)
            ->get();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return view('dashboard', compact(
            'user',
            'completedCourses',
            'inProgressCourses',
            'applications',
            'notifications',
            'recentActivity',
            'certificates',
            'recommendedCourses',
            'inProgressCourseList',
            'unreadCount'
        ));
    }
}
