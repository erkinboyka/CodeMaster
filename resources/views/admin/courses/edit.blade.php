@extends('layouts.admin')

@section('title', __('Edit Course') . ' - CodeMaster')
@section('header-title', __('Edit Course'))
@section('header-subtitle', __('Update course information'))

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.courses') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Courses') }}
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $course->title) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="instructor" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Instructor') }}</label>
                    <input type="text" name="instructor" id="instructor" value="{{ old('instructor', $course->instructor) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('instructor') border-red-500 @enderror" required>
                    @error('instructor')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                    <textarea name="description" id="description" rows="5" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror" required>{{ old('description', $course->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="image_url" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Image URL') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                        <input type="text" name="image_url" id="image_url" value="{{ old('image_url', $course->image_url ?? '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('image_url') border-red-500 @enderror">
                        @error('image_url')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Level') }}</label>
                        <select name="level" id="level" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('level') border-red-500 @enderror" required>
                            <option value="">{{ __('Select level') }}</option>
                            <option value="Начальный" {{ old('level', $course->level) === 'Начальный' ? 'selected' : '' }}>{{ __('Beginner') }}</option>
                            <option value="Средний" {{ old('level', $course->level) === 'Средний' ? 'selected' : '' }}>{{ __('Intermediate') }}</option>
                            <option value="Продвинутый" {{ old('level', $course->level) === 'Продвинутый' ? 'selected' : '' }}>{{ __('Advanced') }}</option>
                        </select>
                        @error('level')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="materials_title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Materials Title') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                        <input type="text" name="materials_title" id="materials_title" value="{{ old('materials_title', $course->materials_title ?? '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('materials_title') border-red-500 @enderror">
                        @error('materials_title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="materials_url" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Materials URL') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                        <input type="text" name="materials_url" id="materials_url" value="{{ old('materials_url', $course->materials_url ?? '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('materials_url') border-red-500 @enderror">
                        @error('materials_url')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.courses') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">{{ __('Update Course') }}</button>
            </div>
        </form>
    </div>

    <!-- Lessons -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mt-6">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold" style="color:var(--text)">{{ __('Lessons') }} <span class="text-sm font-normal" style="color:var(--text-muted)">({{ $course->lessons->count() }})</span></h2>
            <a href="{{ route('admin.lessons.create', $course->id) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-1"></i>{{ __('Add Lesson') }}
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Title') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Quizzes') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Practice') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($course->lessons as $lesson)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm" style="color:var(--text-muted)">{{ $lesson->order_num }}</td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium" style="color:var(--text)">{{ $lesson->title }}</p>
                            @if($lesson->module)
                                <p class="text-xs mt-0.5" style="color:var(--text-muted)">{{ $lesson->module }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $lesson->type === 'video' ? 'bg-blue-50 text-blue-600' : ($lesson->type === 'article' ? 'bg-green-50 text-green-600' : 'bg-purple-50 text-purple-600') }}">{{ $lesson->type }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm" style="color:var(--text-muted)">{{ $lesson->lessonQuizzes->count() }}</span>
                            <a href="{{ route('admin.quizzes.create', $lesson->id) }}" class="ml-1 text-indigo-500 hover:text-indigo-700 text-xs"><i class="fas fa-plus"></i></a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm" style="color:var(--text-muted)">{{ $lesson->practiceTasks->count() }}</span>
                            <a href="{{ route('admin.practices.create', $lesson->id) }}" class="ml-1 text-indigo-500 hover:text-indigo-700 text-xs"><i class="fas fa-plus"></i></a>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.lessons.edit', $lesson->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"><i class="fas fa-edit text-sm"></i></a>
                                <form action="{{ route('admin.lessons.delete', $lesson->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="fas fa-trash text-sm"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm" style="color:var(--text-muted)">{{ __('No lessons yet.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Course Exams -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mt-6">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold" style="color:var(--text)">{{ __('Course Exams') }} <span class="text-sm font-normal" style="color:var(--text-muted)">({{ $course->exams->count() }})</span></h2>
            <a href="{{ route('admin.exams.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-1"></i>{{ __('Add Exam') }}
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Time Limit') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Pass %') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Questions') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($course->exams as $exam)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm" style="color:var(--text-muted)">{{ $exam->id }}</td>
                        <td class="px-6 py-4 text-sm" style="color:var(--text-muted)">{{ $exam->time_limit_minutes }} {{ __('min') }}</td>
                        <td class="px-6 py-4 text-sm" style="color:var(--text-muted)">{{ $exam->pass_percent }}%</td>
                        <td class="px-6 py-4 text-sm" style="color:var(--text-muted)">{{ $exam->questions_per_exam }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.exams.edit', $exam->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"><i class="fas fa-edit text-sm"></i></a>
                                <form action="{{ route('admin.exams.delete', $exam->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="fas fa-trash text-sm"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm" style="color:var(--text-muted)">{{ __('No exams yet.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
