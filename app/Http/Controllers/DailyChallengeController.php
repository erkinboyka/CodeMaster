<?php

namespace App\Http\Controllers;

use App\Models\DailyChallenge;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyChallengeController extends Controller
{
    public function index()
    {
        $today = DailyChallenge::today();
        $problem = $today?->problem;
        $recent = DailyChallenge::with('problem')
            ->where('challenge_date', '>=', Carbon::today()->subDays(6))
            ->orderByDesc('challenge_date')
            ->get();

        return view('daily-challenge.index', compact('today', 'problem', 'recent'));
    }
}
