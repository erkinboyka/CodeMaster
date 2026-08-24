@extends('layouts.admin')

@section('title', __('Manage Contests') . ' - CodeMaster')
@section('header-title', __('Contests Management'))
@section('header-subtitle', __('Manage all contests on the platform'))

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0">
        <form action="{{ route('admin.contests') }}" method="GET" class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search contests...') }}" class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </form>
        <a href="{{ route('admin.contests.create') }}" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-1"></i>{{ __('Create Contest') }}
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Title') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Difficulty') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Tasks') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Start') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('End') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($contests as $contest)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $contest->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-trophy text-purple-600"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-800">{{ $contest->title }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php $colors = ['easy'=>'green','medium'=>'yellow','hard'=>'red']; @endphp
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-{{ $colors[$contest->difficulty] ?? 'gray' }}-100 text-{{ $colors[$contest->difficulty] ?? 'gray' }}-700">{{ __('difficulty_' . $contest->difficulty) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @php $sColors = ['draft'=>'gray','active'=>'green','finished'=>'red']; @endphp
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-{{ $sColors[$contest->status] ?? 'gray' }}-100 text-{{ $sColors[$contest->status] ?? 'gray' }}-700">{{ $contest->status }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $contest->problems_count }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $contest->start_time?->format('d.m.Y H:i') ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $contest->end_time?->format('d.m.Y H:i') ?? '-' }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.contests.tasks', $contest->id) }}" class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition" title="{{ __('Tasks') }}"><i class="fas fa-list text-sm"></i></a>
                            <a href="{{ route('admin.contests.edit', $contest->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"><i class="fas fa-edit text-sm"></i></a>
                            <form action="{{ route('admin.contests.delete', $contest->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="fas fa-trash text-sm"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-12 text-center text-gray-500">{{ __('No contests found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $contests->withQueryString()->links() }}</div>
</div>
@endsection
