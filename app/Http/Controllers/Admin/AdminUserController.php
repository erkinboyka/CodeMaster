<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function users(Request $request)
    {
        $query = User::query();
        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        $users = $query->latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function createUser() { return view('admin.users.create'); }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:seeker,recruiter,admin',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        return redirect()->route('admin.users')->with('success', 'User created.');
    }

    public function editUser($id) { return view('admin.users.edit', ['user' => User::findOrFail($id)]); }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role' => 'sometimes|in:seeker,recruiter,admin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        if (empty($validated['password']) || $validated['password'] === null) unset($validated['password']);
        else $validated['password'] = Hash::make($validated['password']);
        $user->update($validated);
        return redirect()->route('admin.users')->with('success', 'User updated.');
    }

    public function toggleBlock($id)
    {
        if ((int) $id === Auth::id()) {
            return back()->with('error', 'Нельзя заблокировать самого себя.');
        }
        $user = User::findOrFail($id);
        $user->update(['is_blocked' => !$user->is_blocked]);
        return back()->with('success', 'User ' . ($user->is_blocked ? 'blocked' : 'unblocked') . '.');
    }

    public function deleteUser($id)
    {
        if ((int) $id === Auth::id()) {
            return back()->with('error', 'Нельзя удалить свой аккаунт из админки.');
        }
        User::findOrFail($id)->delete();
        return back()->with('success', 'User deleted.');
    }

    public function updateRole(Request $request, $id)
    {
        if ($id == Auth::id()) {
            return back()->with('error', 'Cannot change your own role.');
        }
        $user = User::findOrFail($id);
        $validated = $request->validate(['role' => 'required|in:seeker,recruiter,admin']);
        $user->update(['role' => $validated['role']]);
        return back()->with('success', 'Role updated.');
    }
}
