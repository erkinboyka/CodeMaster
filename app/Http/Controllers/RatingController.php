<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'courses');

        $currentUser = $request->user();

        if ($tab === 'tests') {
            $users = $this->getTestLeaderboard($request);
        } elseif ($tab === 'elo') {
            $users = $this->getEloLeaderboard($request);
        } else {
            $users = $this->getCourseLeaderboard($request);
        }

        return view('ratings.index', compact('users', 'tab', 'currentUser'));
    }

    protected function getCourseLeaderboard(Request $request)
    {
        $query = User::query()
            ->select('users.*')
            ->withCount('certificates')
            ->withCount(['courseProgress as completed_courses_count' => function ($q) {
                $q->where('completed', true);
            }])
            ->withCount(['courseProgress as in_progress_courses_count' => function ($q) {
                $q->where('completed', false);
            }])
            ->where('is_blocked', false);

        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('name', 'like', '%' . $search . '%', 'and');
        }

        $users = $query->orderByDesc('total_xp')
            ->orderByDesc('certificates_count')
            ->paginate(20);

        return $users;
    }

    protected function getTestLeaderboard(Request $request)
    {
        $query = User::query()
            ->select('users.*')
            ->withCount('certificates')
            ->withCount(['practiceSubmissions as practice_passed_count' => function ($q) {
                $q->where('passed', true);
            }])
            ->withCount(['contestSubmissions as contest_passed_count' => function ($q) {
                $q->where('status', 'passed');
            }])
            ->where('is_blocked', false);

        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('name', 'like', '%' . $search . '%', 'and');
        }

        $users = $query->orderByDesc('total_xp')
            ->orderByDesc('practice_passed_count')
            ->paginate(20);

        return $users;
    }

    protected function getEloLeaderboard(Request $request)
    {
        $query = User::query()
            ->select('users.*')
            ->where('is_blocked', false)
            ->where('rating', '>', 1200);

        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('name', 'like', '%' . $search . '%', 'and');
        }

        $users = $query->orderByDesc('rating')
            ->paginate(20);

        return $users;
    }
}
