<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSkill;
use App\Models\UserExperience;
use App\Models\UserEducation;
use App\Models\UserPortfolio;
use App\Models\Certificate;
use App\Models\UserActivity;
use App\Models\UserCourseProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load(['skills', 'experience', 'education', 'portfolio']);

        $user->loadCount('certificates');

        $stats = UserCourseProgress::where('user_id', $user->id)
            ->selectRaw('
                SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_courses,
                COUNT(*) as total_courses
            ')
            ->first();

        $recentActivity = UserActivity::where('user_id', $user->id)
            ->orderByDesc('activity_time')
            ->limit(10)
            ->get();

        $certificates = Certificate::where('user_id', $user->id)
            ->with('course')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('profile.index', compact('user', 'stats', 'recentActivity', 'certificates'));
    }

    public function show($userId)
    {
        $user = User::with(['skills', 'experience', 'education', 'portfolio'])
            ->withCount('certificates')
            ->findOrFail($userId);

        $stats = UserCourseProgress::where('user_id', $user->id)
            ->selectRaw('
                SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_courses,
                COUNT(*) as total_courses
            ')
            ->first();

        $recentActivity = UserActivity::where('user_id', $user->id)
            ->orderByDesc('activity_time')
            ->limit(10)
            ->get();

        $certificates = Certificate::where('user_id', $user->id)
            ->with('course')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('profile.show', compact('user', 'stats', 'recentActivity', 'certificates'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'title' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'github' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return back()->with('success', 'Профиль обновлён.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)],
        ]);

        $user = Auth::user();
        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Пароль обновлён.');
    }

    public function addSkill(Request $request)
    {
        $levelMap = [
            'beginner' => 1,
            'intermediate' => 2,
            'advanced' => 3,
            'expert' => 4,
        ];

        $validated = $request->validate([
            'skill_name' => 'required|string|max:255',
            'skill_level' => 'nullable|in:beginner,intermediate,advanced,expert',
            'category' => 'nullable|in:technical,soft',
        ]);

        $levelValue = $levelMap[$validated['skill_level'] ?? 'beginner'] ?? 1;

        UserSkill::create([
            'user_id' => Auth::id(),
            'skill_name' => $validated['skill_name'],
            'skill_level' => $levelValue,
            'category' => $validated['category'] ?? 'technical',
        ]);

        return back()->with('success', 'Навык добавлен.');
    }

    public function deleteSkill($id)
    {
        UserSkill::where('user_id', Auth::id())->where('id', $id)->delete();
        return back()->with('success', 'Навык удалён.');
    }

    public function addExperience(Request $request)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|string|max:50',
            'end_date' => 'nullable|string|max:50',
            'is_current' => 'nullable|boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_current'] = $validated['is_current'] ?? false;

        if (!empty($validated['is_current'])) {
            $validated['end_date'] = null;
        }

        UserExperience::create($validated);

        return back()->with('success', 'Опыт добавлен.');
    }

    public function updateExperience(Request $request, $id)
    {
        $validated = $request->validate([
            'company' => 'sometimes|string|max:255',
            'position' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|string|max:50',
            'end_date' => 'nullable|string|max:50',
            'is_current' => 'nullable|boolean',
        ]);

        if (!empty($validated['is_current'])) {
            $validated['end_date'] = null;
        }

        UserExperience::where('user_id', Auth::id())->where('id', $id)->update($validated);

        return back()->with('success', 'Опыт обновлён.');
    }

    public function deleteExperience($id)
    {
        UserExperience::where('user_id', Auth::id())->where('id', $id)->delete();
        return back()->with('success', 'Опыт удалён.');
    }

    public function addEducation(Request $request)
    {
        $validated = $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'field' => 'nullable|string|max:255',
            'start_date' => 'required|string|max:50',
            'end_date' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        UserEducation::create($validated);

        return back()->with('success', 'Образование добавлено.');
    }

    public function updateEducation(Request $request, $id)
    {
        $validated = $request->validate([
            'institution' => 'sometimes|string|max:255',
            'degree' => 'sometimes|string|max:255',
            'field' => 'nullable|string|max:255',
            'start_date' => 'sometimes|string|max:50',
            'end_date' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        UserEducation::where('user_id', Auth::id())->where('id', $id)->update($validated);

        return back()->with('success', 'Образование обновлено.');
    }

    public function deleteEducation($id)
    {
        UserEducation::where('user_id', Auth::id())->where('id', $id)->delete();
        return back()->with('success', 'Образование удалено.');
    }

    public function addPortfolio(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url|max:500',
            'github_url' => 'nullable|url|max:500',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'url' => $validated['url'] ?? null,
            'github_url' => $validated['github_url'] ?? null,
            'category' => $validated['category'] ?? null,
        ];

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('portfolios', 'public');
        }

        UserPortfolio::create($data);

        return back()->with('success', 'Проект добавлен.');
    }

    public function updatePortfolio(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url|max:500',
            'github_url' => 'nullable|url|max:500',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
        ]);

        $portfolio = UserPortfolio::where('user_id', Auth::id())->where('id', $id)->firstOrFail();

        if ($request->hasFile('image')) {
            if ($portfolio->image_url) {
                Storage::disk('public')->delete($portfolio->image_url);
            }
            $validated['image_url'] = $request->file('image')->store('portfolios', 'public');
        }

        unset($validated['image']);
        $portfolio->update($validated);

        return back()->with('success', 'Проект обновлён.');
    }

    public function deletePortfolio($id)
    {
        $portfolio = UserPortfolio::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        if ($portfolio->image_url) {
            Storage::disk('public')->delete($portfolio->image_url);
        }
        $portfolio->delete();

        return back()->with('success', 'Проект удалён.');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Аватар обновлён.');
    }

    public function deleteAvatar()
    {
        $user = Auth::user();
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }
        return back()->with('success', 'Аватар удалён.');
    }

    public function verifySkill(Request $request)
    {
        $validated = $request->validate([
            'user_skill_id' => 'required|exists:user_skills,id',
        ]);

        $userSkill = UserSkill::where('user_id', Auth::id())
            ->where('id', $validated['user_skill_id'])
            ->firstOrFail();

        $userSkill->update(['is_verified' => true]);

        return back()->with('success', 'Навык подтверждён.');
    }

    public function reviewPlatform(Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Auth::user()->reviews()->create($validated);

        return back()->with('success', 'Спасибо за отзыв!');
    }
}
