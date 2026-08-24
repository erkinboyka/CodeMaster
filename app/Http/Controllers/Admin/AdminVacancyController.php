<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminVacancyController extends Controller
{
    public function vacancies(Request $request)
    {
        $query = Vacancy::with('vacancySkills');
        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('title', 'like', '%' . $search . '%', 'and');
        }
        $vacancies = $query->latest()->paginate(20);
        return view('admin.vacancies', compact('vacancies'));
    }

    public function createVacancy() { return view('admin.vacancies.create'); }

    public function storeVacancy(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'required|in:remote,office,hybrid',
            'salary_min' => 'required|integer|min:0',
            'salary_max' => 'required|integer|min:0|gte:salary_min',
            'salary_currency' => 'nullable|string|max:3',
            'description' => 'required|string',
            'company_description' => 'nullable|string',
        ]);
        Vacancy::create(array_merge($validated, [
            'owner_id' => Auth::id(),
            'salary_currency' => $validated['salary_currency'] ?? 'TJS',
        ]));
        return redirect()->route('admin.vacancies')->with('success', 'Vacancy created.');
    }

    public function editVacancy($id) { return view('admin.vacancies.edit', ['vacancy' => Vacancy::findOrFail($id)]); }

    public function updateVacancy(Request $request, $id)
    {
        $vacancy = Vacancy::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'required|in:remote,office,hybrid',
            'salary_min' => 'required|integer|min:0',
            'salary_max' => 'required|integer|min:0|gte:salary_min',
            'salary_currency' => 'nullable|string|max:3',
            'description' => 'required|string',
            'company_description' => 'nullable|string',
        ]);
        $validated['salary_currency'] = $validated['salary_currency'] ?? $vacancy->salary_currency;
        $vacancy->update($validated);
        return redirect()->route('admin.vacancies')->with('success', 'Vacancy updated.');
    }

    public function deleteVacancy($id) { Vacancy::findOrFail($id)->delete(); return back()->with('success', 'Vacancy deleted.'); }
}
