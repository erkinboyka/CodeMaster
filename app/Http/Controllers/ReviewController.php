<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'text' => 'required|string|max:1000',
        ]);

        Review::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'rating' => $request->rating,
                'text' => $request->text,
            ]
        );

        session(['review_last_shown' => now()->toIso8601String()]);

        return back()->with('success', 'Спасибо за отзыв!');
    }

    public function dismiss()
    {
        session(['review_last_shown' => now()->toIso8601String()]);
        return back();
    }
}
