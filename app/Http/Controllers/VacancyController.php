<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use App\Models\UserApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VacancyController extends Controller
{
    public function index(Request $request)
    {
        $query = Vacancy::with('vacancySkills');

        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('title', 'like', '%' . $search . '%', 'and');
        }

        if ($request->has('location') && $request->location) {
            $location = str_replace(['%', '_'], ['\\%', '\\_'], $request->location);
            $query->where('location', 'like', '%' . $location . '%', 'and');
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('salary_min') && $request->salary_min) {
            $query->where('salary_max', '>=', $request->salary_min);
        }

        if ($request->has('salary_max') && $request->salary_max) {
            $query->where('salary_min', '<=', $request->salary_max);
        }

        if ($request->has('skill') && $request->skill) {
            $query->whereHas('vacancySkills', function ($q) use ($request) {
                $q->where('skill_name', $request->skill);
            });
        }

        $stats = [
            'total' => (clone $query)->count(),
            'companies' => (clone $query)->distinct()->count('company'),
            'remote' => (clone $query)->where('type', 'remote')->count(),
        ];

        $vacancies = $query->latest()->paginate(12)->withQueryString();

        return view('vacancies.index', compact('vacancies', 'stats'));
    }

    public function show($id)
    {
        $vacancy = Vacancy::with(['vacancySkills', 'requirements', 'pluses', 'responsibilities'])->findOrFail($id);

        $application = UserApplication::where('user_id', Auth::id())
            ->where('vacancy_id', $id)
            ->first();

        $hasApplied = $application !== null;
        $applicationId = $application?->id;

        return view('vacancies.show', compact('vacancy', 'hasApplied', 'applicationId'));
    }

    public function apply($id)
    {
        $vacancy = Vacancy::findOrFail($id);

        $user = Auth::user();

        $existing = UserApplication::where('user_id', $user->id)
            ->where('vacancy_id', $id)
            ->exists();

        if ($existing) {
            return back()->withErrors(['error' => 'You have already applied to this vacancy.']);
        }

        UserApplication::create([
            'user_id' => $user->id,
            'vacancy_id' => $id,
            'status' => 'applied',
        ]);

        return back()->with('success', 'Application submitted successfully.');
    }
}
