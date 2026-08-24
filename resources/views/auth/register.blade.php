@extends('layouts.guest')

@section('title', __('Register') . ' - CodeMaster')

@section('content')
<div x-data="registerForm()" class="space-y-6">
    <div>
        <h2 class="auth-title">{{ __('Create account') }}</h2>
        <p class="auth-subtitle">$ npm init <span class="text-[var(--accent)]">future</span></p>
    </div>

    @if($errors->any())
    <div class="auth-alert">
        <i class="fas fa-exclamation-triangle"></i>
        @foreach($errors->all() as $error)
            <span>{{ $error }}</span>
        @endforeach
    </div>
    @endif

    {{-- Step indicator --}}
    <div class="flex items-center gap-3 mb-2">
        <div class="auth-step" :class="step === 1 ? 'active' : (step > 1 ? 'done' : '')">
            <span x-show="step < 2">1</span>
            <i x-show="step > 1" class="fas fa-check"></i>
        </div>
        <div class="flex-1 h-[2px] rounded transition-colors duration-300" :class="step >= 2 ? 'bg-[var(--accent)]' : 'bg-[var(--border)]'"></div>
        <div class="auth-step" :class="step === 2 ? 'active' : ''">2</div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

        {{-- Step 1: Credentials --}}
        <div x-show="step === 1" x-transition>
            <div class="auth-field">
                <label class="auth-label">
                    <span class="auth-label-prefix">></span> {{ __('Full Name') }}
                </label>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon"><i class="fas fa-user"></i></span>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="auth-input" placeholder="John Doe" autocomplete="name">
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-label">
                    <span class="auth-label-prefix">></span> {{ __('Email') }}
                </label>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon">@</span>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="auth-input" placeholder="you@codemaster.dev" autocomplete="email">
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-label">
                    <span class="auth-label-prefix">></span> {{ __('Password') }}
                </label>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                    <input :type="showPw ? 'text' : 'password'" name="password" required
                        class="auth-input" placeholder="••••••••" autocomplete="new-password">
                    <button type="button" @click="showPw = !showPw" class="auth-input-action">
                        <i class="fas" :class="showPw ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                {{-- Password strength --}}
                <div class="flex gap-1 mt-2" x-show="password.length > 0">
                    <template x-for="i in 4" :key="i">
                        <div class="flex-1 h-1 rounded-full transition-all duration-300"
                            :class="i <= strength ? strengthColors[strength] : 'bg-[var(--border)]'"></div>
                    </template>
                    <span class="text-[10px] ml-2 font-mono" :class="strengthColors[strength]" x-text="strengthLabels[strength]"></span>
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-label">
                    <span class="auth-label-prefix">></span> {{ __('Confirm Password') }}
                </label>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon"><i class="fas fa-shield-alt"></i></span>
                    <input type="password" name="password_confirmation" required
                        class="auth-input" placeholder="••••••••" autocomplete="new-password">
                </div>
            </div>

            <div style="height: 8px"></div>

            <button type="button" @click="step = 2" class="auth-submit">
                <span>{{ __('Next') }}</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>

        {{-- Step 2: Profile --}}
        <div x-show="step === 2" x-transition>
            <div class="auth-field">
                <label class="auth-label">
                    <span class="auth-label-prefix">></span> {{ __('I am a') }}
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="auth-role-card">
                        <input type="radio" name="role" value="seeker" {{ old('role', 'seeker') === 'seeker' ? 'checked' : '' }} class="sr-only" x-model="role">
                        <div class="auth-role-inner" :class="role === 'seeker' ? 'active' : ''">
                            <i class="fas fa-code"></i>
                            <span>{{ __('Job Seeker') }}</span>
                        </div>
                    </label>
                    <label class="auth-role-card">
                        <input type="radio" name="role" value="recruiter" {{ old('role') === 'recruiter' ? 'checked' : '' }} class="sr-only" x-model="role">
                        <div class="auth-role-inner" :class="role === 'recruiter' ? 'active' : ''">
                            <i class="fas fa-building"></i>
                            <span>{{ __('Recruiter') }}</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-label">
                    <span class="auth-label-prefix">></span> {{ __('Location') }}
                </label>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon"><i class="fas fa-map-marker-alt"></i></span>
                    <input type="text" name="location" value="{{ old('location') }}"
                        class="auth-input" placeholder="Dushanbe, Tajikistan">
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-label">
                    <span class="auth-label-prefix">></span> {{ __('Country') }}
                </label>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon"><i class="fas fa-globe"></i></span>
                    <select name="country" class="auth-input auth-select">
                        <option value="">{{ __('Select country') }}</option>
                        @foreach(get_countries() as $code => $name)
                            <option value="{{ $code }}" {{ old('country') === $code ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-label">
                    <span class="auth-label-prefix">></span> {{ __('Skills') }}
                </label>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon"><i class="fas fa-terminal"></i></span>
                    <input x-model="skillSearch" @input="filterSkills()" type="text"
                        class="auth-input" placeholder="{{ __('Type to search skills...') }}">
                </div>
                <div class="flex flex-wrap gap-1.5 mt-2">
                    <template x-for="skill in selectedSkills" :key="skill">
                        <span class="auth-skill-tag">
                            <span x-text="skill"></span>
                            <button type="button" @click="removeSkill(skill)">&times;</button>
                        </span>
                    </template>
                </div>
                <div x-show="filteredSkills.length > 0 && skillSearch" class="auth-dropdown">
                    <template x-for="skill in filteredSkills" :key="skill">
                        <button type="button" @click="addSkill(skill)" class="auth-dropdown-item" x-text="skill"></button>
                    </template>
                </div>
                <input type="hidden" name="skills" :value="selectedSkills.join(',')">
            </div>

            <div class="flex gap-3">
                <button type="button" @click="step = 1" class="auth-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <span>{{ __('Back') }}</span>
                </button>
                <button type="submit" class="auth-submit flex-1">
                    <span>{{ __('Create Account') }}</span>
                    <i class="fas fa-rocket"></i>
                </button>
            </div>
        </div>
    </form>

    <p class="auth-footer-text">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="auth-link-bold">{{ __('Sign in') }}</a>
    </p>
</div>
@endsection

@push('scripts')
<script>
function registerForm() {
    return {
        step: 1,
        showPw: false,
        role: '{{ old("role", "seeker") }}',
        password: '',
        skillSearch: '',
        selectedSkills: [],
        filteredSkills: [],
        allSkills: ['JavaScript', 'TypeScript', 'Python', 'Java', 'C#', 'PHP', 'Laravel', 'Django', 'React', 'Vue', 'Angular', 'Node.js', 'Docker', 'Kubernetes', 'AWS', 'Git', 'SQL', 'MongoDB', 'Redis', 'Linux', 'Figma', 'UI/UX', 'HTML/CSS', 'Tailwind CSS', 'GraphQL', 'REST API', 'Flutter', 'Swift', 'Kotlin', 'Rust', 'Go', 'C++'],
        strength: 0,
        strengthColors: { 0: 'bg-[var(--border)]', 1: 'bg-red-500', 2: 'bg-orange-500', 3: 'bg-yellow-500', 4: 'bg-green-500' },
        strengthLabels: { 0: '', 1: 'weak', 2: 'fair', 3: 'good', 4: 'strong' },
        get strengthColors() { return { 0: 'bg-[var(--border)]', 1: 'text-red-500', 2: 'text-orange-500', 3: 'text-yellow-500', 4: 'text-green-500' } },
        filterSkills() {
            const q = this.skillSearch.toLowerCase();
            this.filteredSkills = this.allSkills.filter(s => s.toLowerCase().includes(q) && !this.selectedSkills.includes(s)).slice(0, 8);
        },
        addSkill(skill) {
            if (!this.selectedSkills.includes(skill)) {
                this.selectedSkills.push(skill);
                this.skillSearch = '';
                this.filteredSkills = [];
            }
        },
        removeSkill(skill) {
            this.selectedSkills = this.selectedSkills.filter(s => s !== skill);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var script = document.createElement('script');
    script.src = 'https://www.google.com/recaptcha/api.js?render={{ config("services.recaptcha.site_key") }}';
    script.onload = function() {
        grecaptcha.ready(function() {
            grecaptcha.execute('{{ config("services.recaptcha.site_key") }}', {action: 'register'}).then(function(token) {
                var el = document.getElementById('g-recaptcha-response');
                if (el) el.value = token;
            });
        });
    };
    document.head.appendChild(script);

    document.querySelector('.auth-form').addEventListener('submit', function(e) {
        if (typeof grecaptcha !== 'undefined') {
            e.preventDefault();
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config("services.recaptcha.site_key") }}', {action: 'register'}).then(function(token) {
                    var el = document.getElementById('g-recaptcha-response');
                    if (el) el.value = token;
                    e.target.submit();
                });
            });
        }
    });
});
</script>
@endpush
