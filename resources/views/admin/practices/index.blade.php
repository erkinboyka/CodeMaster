@extends('layouts.admin')

@section('title', __('Manage Practices') . ' - CodeMaster')
@section('header-title', __('Practices Management'))
@section('header-subtitle', __('Manage all practice tasks on the platform'))

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0">
        <div class="flex items-center space-x-3">
            <form action="{{ route('admin.practices') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search practices...') }}" class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </form>
        </div>
        <p class="text-sm" style="color:var(--text-muted)">{{ __('Create practices from the course edit page.') }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Title') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Language') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Lesson') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Course') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Difficulty') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($practices as $practice)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">{{ $practice->id }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-laptop-code text-orange-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $practice->title }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-50 text-blue-600">{{ $practice->language }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">{{ $practice->lesson->title ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">{{ $practice->lesson->course->title ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                            @if($practice->difficulty === 'easy') bg-green-50 text-green-600
                            @elseif($practice->difficulty === 'medium') bg-yellow-50 text-yellow-600
                            @else bg-red-50 text-red-600 @endif">
                            {{ ucfirst($practice->difficulty) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.practices.edit', $practice->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"><i class="fas fa-edit text-sm"></i></a>
                            <form action="{{ route('admin.practices.delete', $practice->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="fas fa-trash text-sm"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">{{ __('No practices found.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-gray-100">
        {{ $practices->withQueryString()->links() }}
    </div>
</div>
@endsection
