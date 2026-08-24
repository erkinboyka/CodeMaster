@extends('layouts.app')

@section('title', $lesson->title . ' - ' . $course->title)

@section('content')
<div class="lesson-page">
    <div class="lesson-breadcrumb">
        <div class="lesson-breadcrumb-inner">
            <a href="{{ route('courses.show', $course->id) }}" class="lesson-breadcrumb-back">
                <i class="fas fa-arrow-left"></i>
            </a>
            <a href="{{ route('courses.index') }}" class="lesson-breadcrumb-link">{{ __('Courses') }}</a>
            <i class="fas fa-chevron-right lesson-breadcrumb-sep"></i>
            <a href="{{ route('courses.show', $course->id) }}" class="lesson-breadcrumb-link">{{ $course->title }}</a>
            <i class="fas fa-chevron-right lesson-breadcrumb-sep"></i>
            <span class="lesson-breadcrumb-current">{{ $lesson->title }}</span>
            <div class="lesson-breadcrumb-right">
                <span class="lesson-breadcrumb-progress">{{ $percent }}%</span>
                @if($nextLesson)
                <a href="{{ route('courses.lesson', [$course->id, $nextLesson->id]) }}" class="lesson-breadcrumb-next">
                    {{ __('Next') }} <i class="fas fa-arrow-right"></i>
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="lesson-layout">
        <aside class="lesson-sidebar reveal-left">
            <div class="lesson-sidebar-header">
                <i class="fas fa-folder-open"></i>
                <span>{{ __('Lessons') }}</span>
                <span class="lesson-sidebar-count">{{ $sortedLessons->count() }}</span>
            </div>
            <div class="lesson-sidebar-list">
                @foreach($sortedLessons as $index => $sl)
                <a href="{{ route('courses.lesson', [$course->id, $sl->id]) }}" class="lesson-sidebar-item {{ $sl->id === $lesson->id ? 'active' : '' }} {{ in_array($sl->id, $completedLessonIds) ? 'completed' : '' }}">
                    <span class="lesson-sidebar-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    @if(in_array($sl->id, $completedLessonIds))
                    <i class="fas fa-check-circle lesson-sidebar-check"></i>
                    @else
                    <i class="far fa-circle lesson-sidebar-dot"></i>
                    @endif
                    <span class="lesson-sidebar-title">{{ $sl->title }}</span>
                </a>
                @endforeach
            </div>
        </aside>

        <main class="lesson-main reveal-right" x-data="lessonApp()">
            <div class="lesson-tabs">
                @if($lesson->presentation_url)
                <button @click="activeTab = 'slides'" :class="activeTab === 'slides' ? 'active' : ''" class="lesson-tab">
                    <i class="fas fa-desktop"></i>
                    <span>{{ __('Slides') }}</span>
                </button>
                @endif
                @if($lesson->content)
                <button @click="activeTab = 'content'" :class="activeTab === 'content' ? 'active' : ''" class="lesson-tab">
                    <i class="fas fa-book-open"></i>
                    <span>{{ __('Theory') }}</span>
                </button>
                @endif
                @if($lesson->materials_url)
                <button @click="activeTab = 'materials'" :class="activeTab === 'materials' ? 'active' : ''" class="lesson-tab">
                    <i class="fas fa-link"></i>
                    <span>{{ __('Materials') }}</span>
                </button>
                @endif
                @if($lesson->practiceTasks->count())
                <button @click="activeTab = 'practice'" :class="activeTab === 'practice' ? 'active' : ''" class="lesson-tab">
                    <i class="fas fa-code"></i>
                    <span>{{ __('Practice') }}</span>
                    <span class="lesson-tab-badge">{{ $lesson->practiceTasks->count() }}</span>
                </button>
                @endif
                @if($lesson->lessonQuizzes->count())
                <button @click="activeTab = 'quiz'" :class="activeTab === 'quiz' ? 'active' : ''" class="lesson-tab">
                    <i class="fas fa-question-circle"></i>
                    <span>{{ __('Quiz') }}</span>
                    <span class="lesson-tab-badge">{{ $lesson->lessonQuizzes->count() }}</span>
                </button>
                @endif
            </div>

            <div class="lesson-content">
                <div x-show="activeTab === 'slides'" x-cloak>
                    @if($lesson->presentation_url)
                    @php $presUrl = str_replace('.pdf', '.html', $lesson->presentation_url); @endphp
                    <div class="ls-wrap">
                        <div class="ls-toolbar">
                            <div class="ls-toolbar-title"><i class="fas fa-desktop"></i> {{ __('Presentation') }}</div>
                            <div class="ls-toolbar-btns">
                                <button onclick="slidePrev()" class="ls-btn" title="←"><i class="fas fa-chevron-left"></i></button>
                                <span id="slideCounter" class="ls-counter">1 / 1</span>
                                <button onclick="slideNext()" class="ls-btn" title="→"><i class="fas fa-chevron-right"></i></button>
                                <button onclick="slideFullscreen()" class="ls-btn" title="F"><i class="fas fa-expand"></i></button>
                            </div>
                        </div>
                        <iframe id="slideFrame" src="{{ $presUrl }}" class="ls-iframe" frameborder="0" onload="initSlideCounter()"></iframe>
                        <div class="ls-hint">
                            <span><kbd>←</kbd> <kbd>→</kbd> {{ __('Navigate') }}</span>
                            <span><kbd>F</kbd> {{ __('Fullscreen') }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                <div x-show="activeTab === 'content'" x-cloak>
                    @if($lesson->content)
                    <div class="lesson-theory-header"><i class="fas fa-file-alt"></i> {{ __('Theory') }}</div>
                    <div class="lesson-theory-content prose prose-indigo max-w-none">{!! $lesson->content !!}</div>
                    @endif
                </div>

                <div x-show="activeTab === 'materials'" x-cloak>
                    @if($lesson->materials_url)
                    <div class="lm-section">
                        <div class="lm-header">
                            <i class="fas fa-link"></i>
                            <span>{{ $lesson->materials_title ?: __('Useful Materials') }}</span>
                        </div>
                        <div class="lm-list">
                            @php $urls = array_filter(array_map('trim', explode("\n", $lesson->materials_url))); @endphp
                            @foreach($urls as $url)
                                @if(strlen($url) > 0)
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="lm-card">
                                    <div class="lm-icon">
                                        @if(str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be'))
                                            <i class="fab fa-youtube" style="color:#ef4444"></i>
                                        @elseif(str_contains($url, 'github.com'))
                                            <i class="fab fa-github" style="color:var(--text)"></i>
                                        @elseif(str_contains($url, 'medium.com') || str_contains($url, 'dev.to'))
                                            <i class="fab fa-medium" style="color:#22c55e"></i>
                                        @elseif(str_contains($url, 'docs.') || str_contains($url, 'documentation'))
                                            <i class="fas fa-book" style="color:var(--accent)"></i>
                                        @else
                                            <i class="fas fa-external-link-alt" style="color:var(--accent)"></i>
                                        @endif
                                    </div>
                                    <div class="lm-info">
                                        <div class="lm-host">{{ parse_url($url, PHP_URL_HOST) ?? $url }}</div>
                                        <div class="lm-path">{{ parse_url($url, PHP_URL_PATH) ?? '/' }}</div>
                                    </div>
                                    <i class="fas fa-arrow-up-right-from-square lm-arrow"></i>
                                </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div x-show="activeTab === 'practice'" x-cloak>
                    @if($lesson->practiceTasks->count())
                    <div class="lesson-section-header"><i class="fas fa-terminal"></i> {{ __('Practice') }} <span class="lesson-section-count">{{ $lesson->practiceTasks->count() }}</span></div>
                    <div class="lesson-practice-list">
                        @foreach($lesson->practiceTasks as $task)
                        <div class="lesson-practice-card {{ $practiceResults[$task->id] ? 'passed' : '' }}">
                            <div class="lesson-practice-top">
                                <span class="lesson-practice-num">{{ str_pad($loop->index + 1, 2, '0') }}</span>
                                <div class="lesson-practice-info">
                                    <h4>{{ $task->title }}</h4>
                                    <p>{{ Str::limit($task->prompt, 150) }}</p>
                                </div>
                                <span class="lesson-difficulty lesson-difficulty--{{ $task->difficulty }}">{{ __('difficulty_' . $task->difficulty) }}</span>
                            </div>
                            <div class="lesson-practice-bottom">
                                @if($practiceResults[$task->id])<span class="lesson-passed-badge"><i class="fas fa-check"></i> {{ __('Passed') }}</span>@endif
                                <a href="{{ route('courses.practice', [$course->id, $task->id]) }}" class="lesson-practice-btn">
                                    <i class="fas fa-code"></i> {{ $practiceResults[$task->id] ? __('Retry') : __('Start') }}
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <div x-show="activeTab === 'quiz'" x-cloak>
                    @if($lesson->lessonQuizzes->count())
                    <div class="lesson-quiz" x-data="quizApp()">
                        <div class="lesson-section-header"><i class="fas fa-question-circle"></i> {{ __('Quiz') }} <span class="lesson-section-count">{{ $lesson->lessonQuizzes->count() }}</span></div>
                        <div x-show="!quizSubmitted" x-cloak>
                            <div class="lesson-quiz-list">
                                @foreach($lesson->lessonQuizzes->sortBy('order_num') as $index => $quiz)
                                <div class="lesson-quiz-item">
                                    <div class="lesson-quiz-num">{{ $index + 1 }}</div>
                                    <div class="lesson-quiz-body">
                                        <p class="lesson-quiz-text">{{ $quiz->question_text }}</p>
                                        <div class="lesson-quiz-options">
                                            @foreach($quiz->options_json as $optIndex => $option)
                                            <label class="lesson-quiz-option">
                                                <input type="radio" name="quiz_{{ $index }}" value="{{ $optIndex }}" x-model="answers[{{ $index }}]">
                                                <span>{{ $option }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button @click="submitQuiz()" :disabled="quizSubmitting" class="lesson-quiz-submit">
                                <span x-show="!quizSubmitting"><i class="fas fa-paper-plane"></i> {{ __('Submit') }}</span>
                                <span x-show="quizSubmitting"><i class="fas fa-spinner fa-spin"></i> {{ __('Checking...') }}</span>
                            </button>
                        </div>
                        <div x-show="quizSubmitted" x-cloak class="lesson-quiz-result">
                            <div class="lesson-quiz-score" :class="quizScore >= 70 ? 'passed' : 'failed'"><span x-text="quizScore + '%'"></span></div>
                            <h3 :class="quizScore >= 70 ? 'text-green-500' : 'text-red-500'" x-text="quizScore >= 70 ? '{{ __('Passed!') }}' : '{{ __('Not Passed') }}'"></h3>
                            <p class="text-sm text-gray-500 mb-6"><span x-text="quizCorrect"></span>/<span x-text="quizTotal"></span></p>
                            <button @click="resetQuiz()" class="lesson-quiz-retry"><i class="fas fa-redo"></i> {{ __('Retake') }}</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="lesson-bottom-nav">
                @if($prevLesson)
                <a href="{{ route('courses.lesson', [$course->id, $prevLesson->id]) }}" class="lesson-nav-btn">
                    <i class="fas fa-arrow-left"></i>
                    <div><small>{{ __('Previous') }}</small><strong>{{ $prevLesson->title }}</strong></div>
                </a>
                @else <div></div> @endif
                @if($nextLesson)
                <a href="{{ route('courses.lesson', [$course->id, $nextLesson->id]) }}" class="lesson-nav-btn lesson-nav-btn--next">
                    <div><small>{{ __('Next Lesson') }}</small><strong>{{ $nextLesson->title }}</strong></div>
                    <i class="fas fa-arrow-right"></i>
                </a>
                @else
                <a href="{{ route('courses.show', $course->id) }}" class="lesson-nav-btn lesson-nav-btn--next">
                    <div><small>{{ __('Done') }}</small><strong>{{ __('Back to Course') }}</strong></div>
                    <i class="fas fa-check-circle"></i>
                </a>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
function lessonApp() {
    return {
        activeTab: '{{ $lesson->type === "quiz" ? "quiz" : ($lesson->presentation_url ? "slides" : ($lesson->content ? "content" : ($lesson->materials_url ? "materials" : "practice"))) }}',
    }
}
function quizApp() {
    return {
        answers: {}, quizSubmitted: false, quizSubmitting: false,
        quizScore: 0, quizCorrect: 0, quizTotal: 0,
        async submitQuiz() {
            this.quizSubmitting = true;
            try {
                const r = await fetch('{{ route("courses.lesson.quiz", [$course->id, $lesson->id]) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({ answers: this.answers })
                });
                const d = await r.json();
                this.quizScore = d.score; this.quizCorrect = d.correct; this.quizTotal = d.total; this.quizSubmitted = true;
            } catch(e) { console.error(e); }
            this.quizSubmitting = false;
        },
        resetQuiz() { this.answers = {}; this.quizSubmitted = false; }
    }
}
function getSlideFrame() { return document.getElementById('slideFrame'); }
function slideNext() { var f = getSlideFrame(); if (f && f.contentWindow) f.contentWindow.postMessage('slide-next', '*'); }
function slidePrev() { var f = getSlideFrame(); if (f && f.contentWindow) f.contentWindow.postMessage('slide-prev', '*'); }
function slideFullscreen() {
    var f = getSlideFrame();
    if (f) { if (f.requestFullscreen) f.requestFullscreen(); else if (f.webkitRequestFullscreen) f.webkitRequestFullscreen(); }
}
function initSlideCounter() {
    var f = getSlideFrame();
    if (f && f.contentWindow) f.contentWindow.postMessage('slide-get-info', '*');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); slideNext(); }
    if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { e.preventDefault(); slidePrev(); }
    if (e.key === 'f' || e.key === 'F') slideFullscreen();
});
window.addEventListener('message', function(e) {
    if (e.data && e.data.type === 'slide-info') {
        var el = document.getElementById('slideCounter');
        if (el) el.textContent = e.data.current + ' / ' + e.data.total;
    }
});
</script>
@include('components.ai-assistant', ['context' => 'lesson', 'contextTitle' => $lesson->title])
@endpush
