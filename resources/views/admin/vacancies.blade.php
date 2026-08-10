@extends('layouts.admin')

@section('title', __('Manage Vacancies') . ' - CodeMaster')
@section('header-title', __('Vacancies Management'))
@section('header-subtitle', __('Manage all job listings'))

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0">
        <div class="flex items-center space-x-3">
            <form action="{{ route('admin.vacancies') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search vacancies...') }}" class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </form>
        </div>
        <a href="{{ route('admin.vacancies.create') }}" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-1"></i>{{ __('Create Vacancy') }}
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Vacancy') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Company') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Salary') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($vacancies as $vacancy)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-800">{{ $vacancy->title }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">{{ $vacancy->company }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $vacancy->type === 'remote' ? 'bg-green-50 text-green-600' : ($vacancy->type === 'hybrid' ? 'bg-yellow-50 text-yellow-600' : 'bg-blue-50 text-blue-600') }}">{{ ucfirst($vacancy->type) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">{{ $vacancy->salary_min }} - {{ $vacancy->salary_max }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.vacancies.edit', $vacancy->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"><i class="fas fa-edit text-sm"></i></a>
                            <form action="{{ route('admin.vacancies.delete', $vacancy->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="fas fa-trash text-sm"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">{{ __('No vacancies found.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-gray-100">
        {{ $vacancies->withQueryString()->links() }}
    </div>
</div>
@endsection
