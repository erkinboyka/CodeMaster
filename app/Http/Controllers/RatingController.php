<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'courses');
        if (!in_array($tab, ['courses', 'tests', 'elo'], true)) {
            $tab = 'courses';
        }
        $tier = (int) $request->get('tier', 0);
        if ($tier < 0 || $tier > 5) $tier = 0;

        $currentUser = $request->user();

        if ($tab === 'tests') {
            [$users, $stats] = $this->getTestLeaderboard($request, $tier);
        } elseif ($tab === 'elo') {
            [$users, $stats] = $this->getEloLeaderboard($request, $tier);
        } else {
            [$users, $stats] = $this->getCourseLeaderboard($request, $tier);
        }

        return view('ratings.index', compact('users', 'tab', 'tier', 'currentUser', 'stats'));
    }

    /**
     * Тир уровней: 1: 1-4 Начинающий, 2: 5-9 Студент, 3: 10-14 Опытный,
     * 4: 15-29 Продвинутый, 5: 30+ Эксперт. 0 — все.
     */
    protected function applyTier($query, int $tier)
    {
        if ($tier === 5) {
            $query->where('level', '>=', 30);
        } elseif ($tier >= 1) {
            $ranges = [1 => [1, 4], 2 => [5, 9], 3 => [10, 14], 4 => [15, 29]];
            $query->whereBetween('level', $ranges[$tier]);
        }
        return $query;
    }

    /**
     * Базовый запрос с общими фильтрами (бан, тир, поиск, опционально только рейтингованные).
     */
    protected function filteredUsers(Request $request, bool $onlyRanked = false, int $tier = 0)
    {
        $query = User::query()->where('is_blocked', false);

        if ($onlyRanked) {
            $query->where('rating', '>', 1200);
        }

        $this->applyTier($query, $tier);

        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query;
    }

    /**
     * Глобальная статистика по отфильтрованному множеству (а не только по странице).
     */
    protected function boardStats($baseQuery): array
    {
        return [
            'total_xp' => (int) (clone $baseQuery)->sum('total_xp'),
            'certs' => Certificate::whereIn('user_id', (clone $baseQuery)->select('id'))->count(),
        ];
    }

    protected function getCourseLeaderboard(Request $request, int $tier = 0)
    {
        $base = $this->filteredUsers($request, false, $tier);
        $stats = $this->boardStats($base);

        $users = (clone $base)
            ->select('users.*')
            ->withCount('certificates')
            ->withCount(['courseProgress as completed_courses_count' => function ($q) {
                $q->where('completed', true);
            }])
            ->withCount(['courseProgress as in_progress_courses_count' => function ($q) {
                $q->where('completed', false);
            }])
            ->orderByDesc('total_xp')
            ->orderByDesc('certificates_count')
            ->paginate(20)
            ->withQueryString();

        return [$users, $stats];
    }

    protected function getTestLeaderboard(Request $request, int $tier = 0)
    {
        $base = $this->filteredUsers($request, false, $tier);
        $stats = $this->boardStats($base);

        $users = (clone $base)
            ->select('users.*')
            ->withCount('certificates')
            ->withCount(['practiceSubmissions as practice_passed_count' => function ($q) {
                $q->where('passed', true);
            }])
            ->withCount(['contestSubmissions as contest_passed_count' => function ($q) {
                $q->where('status', 'accepted');
            }])
            ->orderByDesc('total_xp')
            ->orderByDesc('practice_passed_count')
            ->paginate(20)
            ->withQueryString();

        return [$users, $stats];
    }

    protected function getEloLeaderboard(Request $request, int $tier = 0)
    {
        $base = $this->filteredUsers($request, true, $tier);
        $stats = $this->boardStats($base);

        $users = (clone $base)
            ->select('users.*')
            ->orderByDesc('rating')
            ->paginate(20)
            ->withQueryString();

        return [$users, $stats];
    }
}
