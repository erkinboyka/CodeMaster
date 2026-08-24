@extends('layouts.admin')

@section('title', __('Interview Prep Tasks') . ' - CodeMaster')
@section('header-title', __('Interview Prep Tasks'))
@section('header-subtitle', __('Manage interview preparation tasks'))

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0">
        <form action="{{ route('admin.interview-prep') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search...') }}" class="pl-4 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-48">
            <select name="category" class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">{{ __('All Categories') }}</option>
                <option value="algorithms" {{ request('category')==='algorithms'?'selected':'' }}>Algorithms</option>
                <option value="data_structures" {{ request('category')==='data_structures'?'selected':'' }}>Data Structures</option>
                <option value="sql" {{ request('category')==='sql'?'selected':'' }}>SQL</option>
                <option value="general" {{ request('category')==='general'?'selected':'' }}>General</option>
            </select>
            <button type="submit" class="px-3 py-2.5 bg-gray-100 rounded-xl text-sm hover:bg-gray-200 transition"><i class="fas fa-search"></i></button>
        </form>
        <a href="{{ route('admin.interview-prep.create') }}" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-1"></i>{{ __('Create Task') }}
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Title') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Category') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Difficulty') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Time') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Order') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tasks as $task)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $task->id }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $task->title }}</td>
                    <td class="px-6 py-4"><span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">{{ $task->category }}</span></td>
                    <td class="px-6 py-4">
                        @php $colors = ['easy'=>'green','medium'=>'yellow','hard'=>'red']; @endphp
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-{{ $colors[$task->difficulty] }}-100 text-{{ $colors[$task->difficulty] }}-700">{{ __('difficulty_' . $task->difficulty) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $task->time_limit_sec }}s</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $task->sort_order }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.interview-prep.edit', $task->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"><i class="fas fa-edit text-sm"></i></a>
                            <form action="{{ route('admin.interview-prep.destroy', $task->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="fas fa-trash text-sm"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">{{ __('No tasks found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $tasks->withQueryString()->links() }}</div>
</div>
@endsection
