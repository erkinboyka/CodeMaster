@extends('layouts.guest')

@section('title', __('Register') . ' - CodeMaster')

@section('content')
<div x-data="{ tab: 'register' }">
    <div class="flex bg-gray-100 rounded-lg p-1 mb-6">
        <button @click="tab = 'login'; window.location='{{ route('login') }}'" :class="tab === 'login' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-2.5 text-sm font-medium rounded-md transition-all duration-200">{{ __('Login') }}</button>
        <button @click="tab = 'register'" :class="tab === 'register' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-2.5 text-sm font-medium rounded-md transition-all duration-200">{{ __('Register') }}</button>
    </div>

    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ __('Create account') }}</h2>
    <p class="text-sm text-gray-500 mb-6">{{ __('Start your learning journey today') }}</p>

    <form method="POST" action="{{ route('register') }}" x-data="{ step: 1 }">
        @csrf
        <div x-show="step === 1">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Full Name') }}</label>
                <div class="relative">
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="John Doe">
                    <i class="fas fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                @error('name')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Email') }}</label>
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="you@example.com">
                    <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                @error('email')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Password') }}</label>
                <div class="relative" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'" name="password" required class="w-full pl-10 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="••••••••">
                    <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <button type="button" @click="show = !show" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @error('password')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Confirm Password') }}</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" required class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="••••••••">
                    <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <button type="button" @click="step = 2" class="w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 shadow-lg shadow-indigo-200 transition-all duration-300">
                {{ __('Next') }} <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>

        <div x-show="step === 2">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('I am a') }}</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="seeker" {{ old('role', 'seeker') === 'seeker' ? 'checked' : '' }} class="peer sr-only">
                        <div class="p-4 border-2 border-gray-200 rounded-xl text-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-gray-300 transition">
                            <i class="fas fa-user-graduate text-2xl text-gray-400 peer-checked:text-indigo-500 mb-2"></i>
                            <p class="text-sm font-medium text-gray-700">{{ __('Job Seeker') }}</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="recruiter" {{ old('role') === 'recruiter' ? 'checked' : '' }} class="peer sr-only">
                        <div class="p-4 border-2 border-gray-200 rounded-xl text-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-gray-300 transition">
                            <i class="fas fa-building text-2xl text-gray-400 peer-checked:text-indigo-500 mb-2"></i>
                            <p class="text-sm font-medium text-gray-700">{{ __('Recruiter') }}</p>
                        </div>
                    </label>
                </div>
                @error('role')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Location') }}</label>
                <div class="relative">
                    <input type="text" name="location" value="{{ old('location') }}" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="Dushanbe, Tajikistan">
                    <i class="fas fa-map-marker-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Country') }}</label>
                <div class="relative">
                    <select name="country" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition appearance-none">
                        <option value="">{{ __('Select country') }}</option>
                        <option value="TJ" {{ old('country') === 'TJ' ? 'selected' : '' }}>Tajikistan</option>
                        <option value="RU" {{ old('country') === 'RU' ? 'selected' : '' }}>Russia</option>
                        <option value="UZ" {{ old('country') === 'UZ' ? 'selected' : '' }}>Uzbekistan</option>
                        <option value="KZ" {{ old('country') === 'KZ' ? 'selected' : '' }}>Kazakhstan</option>
                        <option value="KG" {{ old('country') === 'KG' ? 'selected' : '' }}>Kyrgyzstan</option>
                    </select>
                    <i class="fas fa-globe absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <div class="mb-6" x-data="skillsSelector()">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Skills') }}</label>
                <div class="relative">
                    <input x-model="search" @input="filterSkills()" type="text" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="{{ __('Type to search skills...') }}">
                    <i class="fas fa-code absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="flex flex-wrap gap-2 mt-2">
                    <template x-for="skill in selected" :key="skill">
                        <span class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">
                            <span x-text="skill"></span>
                            <button @click="removeSkill(skill)" class="ml-1.5 text-indigo-500 hover:text-indigo-700">&times;</button>
                        </span>
                    </template>
                </div>
                <div x-show="filtered.length > 0 && search" class="mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-40 overflow-y-auto">
                    <template x-for="skill in filtered" :key="skill">
                        <button type="button" @click="addSkill(skill)" class="w-full text-left px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-600 transition" x-text="skill"></button>
                    </template>
                </div>
                <input type="hidden" name="skills" :value="selected.join(',')">
            </div>

            <div class="flex gap-3 mb-4">
                <button type="button" @click="step = 1" class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </button>
                <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 shadow-lg shadow-indigo-200 transition-all duration-300">
                    {{ __('Create Account') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function skillsSelector() {
    return {
        search: '',
        selected: [],
        allSkills: ['JavaScript', 'TypeScript', 'Python', 'Java', 'C#', 'PHP', 'Laravel', 'Django', 'React', 'Vue', 'Angular', 'Node.js', 'Docker', 'Kubernetes', 'AWS', 'Git', 'SQL', 'MongoDB', 'Redis', 'Linux', 'Figma', 'UI/UX', 'HTML/CSS', 'Tailwind CSS', 'GraphQL', 'REST API', 'Flutter', 'Swift', 'Kotlin', 'Rust', 'Go', 'C++'],
        filtered: [],
        filterSkills() {
            this.filtered = this.allSkills.filter(s => s.toLowerCase().includes(this.search.toLowerCase()) && !this.selected.includes(s));
        },
        addSkill(skill) {
            if (!this.selected.includes(skill)) {
                this.selected.push(skill);
                this.search = '';
                this.filtered = [];
            }
        },
        removeSkill(skill) {
            this.selected = this.selected.filter(s => s !== skill);
        }
    }
}
</script>
@endpush
