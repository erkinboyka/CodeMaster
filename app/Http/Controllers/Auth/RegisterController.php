<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'location' => 'nullable|string|max:255',
            'role' => 'nullable|in:seeker,recruiter',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'location' => $validated['location'] ?? null,
            'role' => $validated['role'] ?? 'seeker',
        ]);

        $skillsInput = $request->input('skills');
        if ($skillsInput) {
            $skillNames = is_string($skillsInput) ? array_filter(explode(',', $skillsInput)) : (array) $skillsInput;
            foreach ($skillNames as $skillName) {
                $skillName = trim($skillName);
                if ($skillName) {
                    UserSkill::create([
                        'user_id' => $user->id,
                        'skill_name' => $skillName,
                    ]);
                }
            }
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
