@extends('layouts.admin')

@section('title', __('Contest Tasks') . ' - CodeMaster')
@section('header-title', __('Tasks: ') . $contest->title)

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <a href="{{ route('admin.contests') }}" class="text-sm text-indigo-600 hover:text-indigo-800"><i class="fas fa-arrow-left mr-1"></i>{{ __('Back') }}</a>
        <a href="{{ route('admin.contests.tasks.create', $contest->id) }}" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-1"></i>{{ __('Add Task') }}
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Title') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Difficulty') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Points') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Language') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Order') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tasks as $task)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $task->id }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $task->title }}</td>
                    <td class="px-6 py-4">
                        @php $colors = ['easy'=>'green','medium'=>'yellow','hard'=>'red']; @endphp
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-{{ $colors[$task->difficulty] }}-100 text-{{ $colors[$task->difficulty] }}-700">{{ __('difficulty_' . $task->difficulty) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $task->points }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $task->language }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $task->order_num }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.contests.tasks.edit', [$contest->id, $task->id]) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"><i class="fas fa-edit text-sm"></i></a>
                            <form action="{{ route('admin.contests.tasks.delete', [$contest->id, $task->id]) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')" class="inline">
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
