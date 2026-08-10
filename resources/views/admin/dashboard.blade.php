@extends('layouts.admin')

@section('title', __('Admin Dashboard') . ' - CodeMaster')

@section('header-title', __('Admin Dashboard'))
@section('header-subtitle', __('Welcome back'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">{{ __('Admin Dashboard') }}</h1>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ __('Total Users') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book text-purple-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalCourses }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ __('Total Courses') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-briefcase text-green-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalVacancies }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ __('Total Vacancies') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-plus text-yellow-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $newUsersToday }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ __('New Today') }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-4">{{ __('Quick Actions') }}</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.users') }}" class="p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition text-center">
                    <i class="fas fa-users text-indigo-600 text-xl mb-2 block"></i>
                    <span class="text-sm font-medium text-gray-700">{{ __('Manage Users') }}</span>
                </a>
                <a href="{{ route('admin.courses') }}" class="p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition text-center">
                    <i class="fas fa-book text-purple-600 text-xl mb-2 block"></i>
                    <span class="text-sm font-medium text-gray-700">{{ __('Manage Courses') }}</span>
                </a>
                <a href="{{ route('admin.lessons') }}" class="p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition text-center">
                    <i class="fas fa-list text-green-600 text-xl mb-2 block"></i>
                    <span class="text-sm font-medium text-gray-700">{{ __('Manage Lessons') }}</span>
                </a>
                <a href="{{ route('admin.vacancies') }}" class="p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition text-center">
                    <i class="fas fa-briefcase text-yellow-600 text-xl mb-2 block"></i>
                    <span class="text-sm font-medium text-gray-700">{{ __('Manage Vacancies') }}</span>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-4">{{ __('Platform Stats') }}</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ __('Total Users') }}</span>
                    <span class="font-semibold text-gray-800">{{ $totalUsers }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ __('Blocked Users') }}</span>
                    <span class="font-semibold text-red-600">{{ $blockedUsers }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ __('New Users Today') }}</span>
                    <span class="font-semibold text-green-600">{{ $newUsersToday }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
