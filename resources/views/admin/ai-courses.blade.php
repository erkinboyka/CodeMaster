@extends('layouts.admin')

@section('title', 'AI Курсы - CodeMaster')
@section('header-title', 'Управление AI-курсами')
@section('header-subtitle', 'Все AI-курсы на платформе')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0">
        <form action="{{ route('admin.courses') }}" method="GET" class="flex items-center space-x-3">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск курсов..." class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            <select name="type" class="py-2.5 px-4 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Все типы</option>
                <option value="public" {{ request('type') === 'public' ? 'selected' : '' }}>Публичные</option>
                <option value="private" {{ request('type') === 'private' ? 'selected' : '' }}>Приватные</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                <i class="fas fa-filter mr-1"></i>Фильтр
            </button>
        </form>
        <div class="text-sm text-gray-500">
            Всего: <span class="font-semibold text-gray-800">{{ $courses->total() }}</span> курсов
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Курс</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Тема</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Уровень</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Шаги</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Студенты</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Автор</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Дата</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($courses as $course)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-robot text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $course->title }}</p>
                                <p class="text-xs text-gray-400">
                                    @if($course->type === 'public')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-700">Публичный</span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-yellow-100 text-yellow-700">Приватный</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $course->topic ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $course->course_level ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $course->steps_count ?? 0 }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $course->students_count ?? 0 }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $course->owner->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $course->created_at->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('admin.courses.delete', $course->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Удалить курс «{{ addslashes($course->title) }}»?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition">
                                <i class="fas fa-trash mr-1"></i>Удалить
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">
                        <i class="fas fa-robot text-3xl mb-3 block"></i>
                        AI-курсов пока нет
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($courses->hasPages())
    <div class="p-6 border-t border-gray-100">
        {{ $courses->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
