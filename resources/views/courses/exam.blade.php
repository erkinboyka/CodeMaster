@extends('layouts.app')

@section('title', __('Final Exam') . ' - ' . $course->title)

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4" x-data="examApp()" x-init="init()">
    <a href="{{ route('courses.show', $course->id) }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Course') }}
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('Final Exam') }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $course->title }}</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">{{ __('Time Remaining') }}</div>
                <div class="text-2xl font-mono font-bold" :class="timeLeft <= 60 ? 'text-red-600' : 'text-gray-900'" x-text="formatTime(timeLeft)"></div>
            </div>
        </div>

        <div class="flex items-center space-x-6 text-sm text-gray-600 mb-6">
            <div class="flex items-center">
                <i class="fas fa-clock mr-2 text-indigo-500"></i>
                <span>{{ $exam->time_limit_minutes }} {{ __('minutes') }}</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-indigo-500"></i>
                <span>{{ __('Pass') }}: {{ $exam->pass_percent }}%</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-list-ol mr-2 text-indigo-500"></i>
                <span>{{ count($questions) }} {{ __('questions') }}</span>
            </div>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" :style="{ width: progressPercent + '%' }"></div>
        </div>
    </div>

    <form action="{{ route('courses.exam.submit', $course->id) }}" method="POST" @submit.prevent="submitExam()">
        @csrf

        <input type="hidden" name="question_order" :value="JSON.stringify(questionOrder)">

        <div class="space-y-6">
            @foreach($questions as $index => $question)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-sm font-bold text-indigo-600">{{ $index + 1 }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-900 font-medium mb-4">{{ $question['question'] ?? '' }}</p>
                        <div class="space-y-2">
                            @foreach($question['options'] ?? [] as $optIndex => $option)
                            <label class="flex items-center space-x-3 p-3 rounded-xl border transition cursor-pointer" :class="answers[{{ $index }}] == {{ $optIndex }} ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                <input type="radio" name="answer_{{ $index }}" value="{{ $optIndex }}" x-model="answers[{{ $index }}]" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">{{ $option }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                <span x-text="answeredCount"></span>/{{ count($questions) }} {{ __('answered') }}
            </div>
            <button type="submit" @click="confirmSubmit()" class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 shadow-lg transition-all duration-300">
                <i class="fas fa-paper-plane mr-2"></i>{{ __('Submit Exam') }}
            </button>
        </div>

        <template x-for="(q, i) in questionOrder" :key="i">
            <input type="hidden" :name="'answers[' + i + ']'" :value="answers[i] ?? ''">
        </template>
    </form>
</div>

@push('scripts')
<script>
function examApp() {
    return {
        answers: {},
        questionOrder: @js(array_keys($questions)),
        timeLeft: {{ $exam->time_limit_minutes * 60 }},
        timer: null,

        init() {
            this.timer = setInterval(() => {
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    clearInterval(this.timer);
                    this.submitExam();
                }
            }, 1000);
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0');
        },

        get progressPercent() {
            return Math.round((this.answeredCount / {{ count($questions) }}) * 100);
        },

        get answeredCount() {
            return Object.keys(this.answers).filter(k => this.answers[k] !== null && this.answers[k] !== undefined).length;
        },

        confirmSubmit() {
            if (this.answeredCount < {{ count($questions) }}) {
                if (!confirm('{{ __("You have unanswered questions. Submit anyway?") }}')) {
                    event.preventDefault();
                }
            }
        },

        submitExam() {
            clearInterval(this.timer);
            document.querySelector('form').submit();
        }
    }
}
</script>
@endpush
@endsection
