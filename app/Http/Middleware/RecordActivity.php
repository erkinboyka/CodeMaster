<?php

namespace App\Http\Middleware;

use App\Services\GamificationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->recordActivity();
            app(GamificationService::class)->dailyTokenBonus($user);
        }

        return $next($request);
    }
}
