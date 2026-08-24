@extends('layouts.admin')

@section('title', __('Manage Users') . ' - CodeMaster')
@section('header-title', __('Users Management'))
@section('header-subtitle', __('Manage all registered users'))

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0">
        <form action="{{ route('admin.users') }}" method="GET" class="flex items-center space-x-3">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search users...') }}" class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            <select name="role" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">{{ __('All Roles') }}</option>
                <option value="seeker" {{ request('role') === 'seeker' ? 'selected' : '' }}>{{ __('Student') }}</option>
                <option value="recruiter" {{ request('role') === 'recruiter' ? 'selected' : '' }}>{{ __('Recruiter') }}</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">{{ __('Filter') }}</button>
        </form>
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <span>{{ __('Showing') }} <strong>{{ $users->firstItem() }}-{{ $users->lastItem() }}</strong> {{ __('of') }} <strong>{{ $users->total() }}</strong></span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('User') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Role') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Joined') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $user->avatar_url }}" class="w-9 h-9 rounded-full">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $user->role === 'admin' ? 'bg-purple-50 text-purple-600' : ($user->role === 'recruiter' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-600') }}">{{ ucfirst($user->role) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="flex items-center space-x-1.5">
                            <span class="w-2 h-2 rounded-full {{ $user->is_blocked ? 'bg-red-400' : 'bg-green-400' }}"></span>
                            <span class="text-xs text-gray-600">{{ $user->is_blocked ? __('Blocked') : __('Active') }}</span>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('profile.show', $user->id) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="{{ __('View') }}">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                            <form action="{{ route('admin.users.toggleBlock', $user->id) }}" method="POST" class="inline">
                                @csrf
                                <button class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="{{ __('Block') }}">
                                    <i class="fas fa-ban text-sm"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="{{ __('Delete') }}">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        {{ __('No users found') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection
