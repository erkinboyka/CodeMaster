@extends('layouts.app')

@section('title', $task->title . ' - Practice - ' . $course->title)

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/dracula.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/python/python.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/clike/clike.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/ruby/ruby.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/go/go.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/yaml/yaml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closetag.min.js"></script>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="practiceApp()">
    <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('courses.index') }}" class="hover:text-indigo-600 transition">{{ __('Courses') }}</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="{{ route('courses.show', $course->id) }}" class="hover:text-indigo-600 transition">{{ $course->title }}</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="{{ route('courses.lesson', [$course->id, $task->lesson_id]) }}" class="hover:text-indigo-600 transition">{{ $task->lesson->title }}</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-900 font-medium">{{ __('Practice') }}</span>
    </nav>

    <div class="grid lg:grid-cols-2 gap-6" style="height: calc(100vh - 180px);">
        {{-- Task Description --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-gray-900">{{ $task->title }}</h1>
                    <div class="flex items-center space-x-3 mt-1">
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $task->difficulty === 'easy' ? 'bg-green-100 text-green-700' : ($task->difficulty === 'hard' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">{{ __('difficulty_' . $task->difficulty) }}</span>
                        <span class="text-xs text-gray-500"><i class="fas fa-clock mr-1"></i>{{ $task->time_limit }} {{ __('min') }}</span>
                        <span class="text-xs text-gray-500"><i class="fas fa-code mr-1"></i>{{ $task->language }}</span>
                    </div>
                </div>
                @if($bestSubmission)
                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full"><i class="fas fa-check-circle mr-1"></i>{{ __('Completed') }}</span>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto p-4 prose prose-sm max-w-none">
                {!! nl2br(e($task->prompt)) !!}
            </div>

            @if($task->hints)
            <div class="border-t border-gray-100 p-4">
                <button @click="showHints = !showHints" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-lightbulb mr-1"></i><span x-text="showHints ? '{{ __("Hide Hints") }}' : '{{ __("Show Hints") }}'"></span>
                </button>
                <div x-show="showHints" x-cloak class="mt-2 bg-yellow-50 rounded-lg p-3 text-sm text-yellow-800">
                    {!! nl2br(e($task->hints)) !!}
                </div>
            </div>
            @endif
        </div>

        {{-- Code Editor --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="p-3 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                <div class="flex items-center space-x-3">
                    <select x-model="language" class="text-sm border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="python">Python</option>
                        <option value="javascript">JavaScript</option>
                        <option value="typescript">TypeScript</option>
                        <option value="php">PHP</option>
                        <option value="c">C</option>
                        <option value="cpp">C++</option>
                        <option value="csharp">C#</option>
                        <option value="java">Java</option>
                        <option value="ruby">Ruby</option>
                        <option value="go">Go</option>
                        <option value="sql">SQL</option>
                        <option value="mysql">MySQL</option>
                        <option value="html">HTML/CSS</option>
                        <option value="yaml">YAML</option>
                        <option value="json">JSON</option>
                        <option value="text">Text</option>
                    </select>
                    <button @click="resetCode()" class="text-sm text-gray-500 hover:text-gray-700">
                        <i class="fas fa-undo mr-1"></i>{{ __('Reset') }}
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="runTests()" :disabled="running" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition disabled:opacity-50">
                        <span x-show="!running"><i class="fas fa-play mr-1"></i>{{ __('Run Tests') }}</span>
                        <span x-show="running"><i class="fas fa-spinner fa-spin mr-1"></i>{{ __('Running...') }}</span>
                    </button>
                    <button @click="submitSolution()" :disabled="running" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition disabled:opacity-50">
                        <i class="fas fa-paper-plane mr-1"></i>{{ __('Submit') }}
                    </button>
                </div>
            </div>

            <div class="flex-1 min-h-0">
                <textarea id="code-editor"></textarea>
            </div>

            {{-- Test Results --}}
            <div x-show="results.length > 0" class="border-t border-gray-100 max-h-48 overflow-y-auto">
                <div class="p-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">
                        <span x-text="passedCount"></span>/<span x-text="totalCount"></span> {{ __('tests passed') }}
                    </span>
                    <span class="text-sm font-bold" :class="allPassed ? 'text-green-600' : 'text-red-600'" x-text="allPassed ? '{{ __("All Tests Passed!") }}' : '{{ __("Some Tests Failed") }}'"></span>
                </div>
                <div class="divide-y divide-gray-100">
                    <template x-for="(result, index) in results" :key="index">
                        <div class="p-3 flex items-start space-x-3" :class="result.passed ? 'bg-green-50' : 'bg-red-50'">
                            <i class="mt-0.5" :class="result.passed ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500'"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium" :class="result.passed ? 'text-green-800' : 'text-red-800'" x-text="'Test ' + (result.test_case || index + 1) + ': ' + (result.description || 'Check output')"></p>
                                <template x-if="!result.passed">
                                    <div class="mt-1 text-xs space-y-1">
                                        <p class="text-gray-600"><span class="font-medium">Input:</span> <code x-text="result.input || '(none)'" class="bg-gray-100 px-1 rounded"></code></p>
                                        <p class="text-gray-600"><span class="font-medium">Expected:</span> <code x-text="result.expected" class="bg-gray-100 px-1 rounded"></code></p>
                                        <p class="text-red-600"><span class="font-medium">Actual:</span> <code x-text="result.output || result.actual || '(empty)'" class="bg-red-100 px-1 rounded"></code></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="starter-code-data" style="display:none">{!! e($task->starter_code ?? '') !!}</div>

@push('scripts')
<script src="{{ asset('js/practice.js') }}"></script>
<script>
function practiceApp() {
    return {
        language: '{{ $task->language }}',
        running: false,
        results: [],
        passedCount: 0,
        totalCount: 0,
        allPassed: false,
        showHints: false,
        starterCode: '',

        resetCode() {
            if (window.codeEditor && window.defaultCodes) {
                window.codeEditor.setValue(this.starterCode || window.defaultCodes[this.language] || '');
            }
        },

        async runTests() {
            this.running = true;
            this.results = [];
            try {
                const response = await fetch('{{ route("courses.practice.run", [$course->id, $task->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ code: window.codeEditor.getValue(), language: this.language })
                });
                const data = await response.json();
                this.results = data.results || [];
                this.passedCount = data.passed_count;
                this.totalCount = data.total_count;
                this.allPassed = data.passed;
            } catch (e) {
                console.error(e);
            }
            this.running = false;
        },

        async submitSolution() {
            await this.runTests();
        }
    }
}
</script>
<style>
    .CodeMirror { height: 100%; font-size: 14px; border: none; }
    .CodeMirror-gutters { background: #282a36; border-right: 1px solid #44475a; }
</style>
@endpush
@endsection
