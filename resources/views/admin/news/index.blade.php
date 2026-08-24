@extends('layouts.admin')

@section('title', __('News Management') . ' - CodeMaster')
@section('header-title', __('News Management'))
@section('header-subtitle', __('Manage platform news and announcements'))

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0">
        <form action="{{ route('admin.news.index') }}" method="GET" class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search news...') }}" class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </form>
        <a href="{{ route('admin.news.create') }}" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-1"></i>{{ __('Create News') }}
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Title') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Author') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Tags') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Views') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($news as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-800">{{ $item->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($item->excerpt, 60) }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">{{ $item->user->name ?? '—' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach($item->tags->take(3) as $tag)
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-indigo-50 text-indigo-600">{{ $tag->name }}</span>
                            @endforeach
                            @if($item->tags->count() > 3)
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-gray-100 text-gray-500">+{{ $item->tags->count() - 3 }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($item->is_published)
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-50 text-green-600">{{ __('Published') }}</span>
                        @else
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500">{{ __('Draft') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">{{ number_format($item->views_count) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.news.edit', $item->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"><i class="fas fa-edit text-sm"></i></a>
                            <form action="{{ route('admin.news.destroy', $item->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete this news item?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="fas fa-trash text-sm"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">{{ __('No news articles yet.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($news->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $news->links() }}
    </div>
    @endif
</div>
@endsection
