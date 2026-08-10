@extends('layouts.admin')

@section('title', __('Edit User') . ' - CodeMaster')
@section('header-title', __('Edit User'))
@section('header-subtitle', __('Update user information'))

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('New Password') }} ({{ __('leave blank to keep current') }})</label>
                    <input type="password" name="password" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Role') }}</label>
                    <select name="role" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="seeker" {{ $user->role === 'seeker' ? 'selected' : '' }}>{{ __('Seeker') }}</option>
                        <option value="recruiter" {{ $user->role === 'recruiter' ? 'selected' : '' }}>{{ __('Recruiter') }}</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Location') }}</label>
                    <input type="text" name="location" value="{{ old('location', $user->location) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500">{{ __('Status') }}:</span>
                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $user->is_blocked ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }}">{{ $user->is_blocked ? __('Blocked') : __('Active') }}</span>
                </div>
            </div>
            <div class="flex items-center space-x-3 mt-6">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">{{ __('Update User') }}</button>
                <a href="{{ route('admin.users') }}" class="px-6 py-2.5 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-100 transition">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
