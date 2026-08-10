@extends('layouts.admin')

@section('title', __('Create Vacancy') . ' - CodeMaster')
@section('header-title', __('Create Vacancy'))
@section('header-subtitle', __('Add a new job listing'))

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.vacancies') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Vacancies') }}
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.vacancies.store') }}" method="POST">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="company" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Company') }}</label>
                        <input type="text" name="company" id="company" value="{{ old('company') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('company') border-red-500 @enderror" required>
                        @error('company')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Location') }}</label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('location') border-red-500 @enderror" required>
                        @error('location')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Type') }}</label>
                    <select name="type" id="type" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('type') border-red-500 @enderror" required>
                        <option value="remote" {{ old('type') === 'remote' ? 'selected' : '' }}>{{ __('Remote') }}</option>
                        <option value="hybrid" {{ old('type') === 'hybrid' ? 'selected' : '' }}>{{ __('Hybrid') }}</option>
                        <option value="office" {{ old('type') === 'office' ? 'selected' : '' }}>{{ __('Office') }}</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="salary_min" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Salary Min') }}</label>
                        <input type="number" name="salary_min" id="salary_min" value="{{ old('salary_min', 0) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('salary_min') border-red-500 @enderror" required>
                        @error('salary_min')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="salary_max" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Salary Max') }}</label>
                        <input type="number" name="salary_max" id="salary_max" value="{{ old('salary_max', 0) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('salary_max') border-red-500 @enderror" required>
                        @error('salary_max')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="salary_currency" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Currency') }}</label>
                        <select name="salary_currency" id="salary_currency" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="TJS" {{ old('salary_currency', 'TJS') === 'TJS' ? 'selected' : '' }}>TJS</option>
                            <option value="USD" {{ old('salary_currency') === 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="RUB" {{ old('salary_currency') === 'RUB' ? 'selected' : '' }}>RUB</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                    <textarea name="description" id="description" rows="5" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror" required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="company_description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Company Description') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                    <textarea name="company_description" id="company_description" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('company_description') border-red-500 @enderror">{{ old('company_description') }}</textarea>
                    @error('company_description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.vacancies') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">{{ __('Create Vacancy') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
