@extends('layouts.admin')

@section('title', __('Roadmap List') . ' - CodeMaster')
@section('header-title', __('Roadmap Catalog'))
@section('header-subtitle', __('Manage roadmap entries'))

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <div></div>
        <a href="{{ route('admin.roadmap-list.create') }}" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-1"></i>{{ __('Create Roadmap') }}
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Title') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Description') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Created') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($roadmaps as $rm)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $rm->id }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $rm->title }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($rm->description, 60) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $rm->created_at?->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.roadmap-list.edit', $rm->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"><i class="fas fa-edit text-sm"></i></a>
                            <form action="{{ route('admin.roadmap-list.destroy', $rm->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="fas fa-trash text-sm"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">{{ __('No roadmaps found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $roadmaps->withQueryString()->links() }}</div>
</div>
@endsection
