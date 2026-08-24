@extends('layouts.admin')

@section('title', __('Edit News') . ' - CodeMaster')
@section('header-title', __('Edit News'))
@section('header-subtitle', $news->title)

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <form action="{{ route('admin.news.update', $news->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div>
                    <label class="admin-label">{{ __('Title') }}</label>
                    <input type="text" name="title" value="{{ old('title', $news->title) }}" required
                           class="admin-input w-full">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="admin-label">{{ __('Excerpt') }}</label>
                    <textarea name="excerpt" rows="2" class="admin-input w-full">{{ old('excerpt', $news->excerpt) }}</textarea>
                </div>

                <div>
                    <label class="admin-label">{{ __('Content') }}</label>
                    <textarea id="news-content-tinymce" style="width:100%;min-height:400px">{{ old('content', $news->content) }}</textarea>
                    <input type="hidden" name="content" id="news-content-hidden" value="{{ old('content', $news->content) }}">
                    @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="admin-label">{{ __('Image URL') }}</label>
                    <input type="text" name="image" value="{{ old('image', $news->image) }}" class="admin-input w-full" placeholder="https://...">
                    @if($news->image)
                    <img src="{{ $news->image }}" class="mt-2 rounded-lg w-full h-32 object-cover">
                    @endif
                </div>

                <div>
                    <label class="admin-label">{{ __('Tags') }}</label>
                    <div id="tags-container" class="flex flex-wrap gap-1.5 mb-2"></div>
                    <input type="text" id="tag-input" class="admin-input w-full" placeholder="{{ __('Type a tag and press Enter') }}">
                    <input type="hidden" name="tags" id="tags-hidden" value="{{ json_encode($newsTags) }}">
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_published" id="is_published" value="1"
                           {{ old('is_published', $news->is_published) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 rounded">
                    <label for="is_published" class="text-sm font-medium text-gray-700">{{ __('Published') }}</label>
                </div>

                <div class="flex items-center gap-3 pt-4">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">
                        <i class="fas fa-save mr-1"></i>{{ __('Update') }}
                    </button>
                    <a href="{{ route('admin.news.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function() {
    const isDark = !(document.documentElement.getAttribute('data-theme') || '').includes('light');
    tinymce.init({
        selector: '#news-content-tinymce',
        height: 500,
        skin: isDark ? 'oxide-dark' : 'oxide',
        content_css: isDark ? 'dark' : 'default',
        menubar: true,
        plugins: 'lists link image code codesample fullscreen quickbars table media',
        toolbar: 'undo redo | blocks | bold italic strikethrough | link image codesample | alignleft aligncenter alignright | bullist numlist | table media | code fullscreen',
        codesample_languages: [
            {text: 'HTML/XML', value: 'markup'}, {text: 'JavaScript', value: 'javascript'},
            {text: 'TypeScript', value: 'typescript'}, {text: 'CSS', value: 'css'},
            {text: 'PHP', value: 'php'}, {text: 'Python', value: 'python'},
            {text: 'Java', value: 'java'}, {text: 'C', value: 'c'}, {text: 'C++', value: 'cpp'},
        ],
        content_style: 'body { font-family: Inter, sans-serif; font-size: 14px; color: ' + (isDark ? '#e2e8f0' : '#1e293b') + '; }',
        setup: (editor) => {
            editor.on('change', () => {
                const hidden = document.getElementById('news-content-hidden');
                if (hidden) hidden.value = editor.getContent();
            });
        }
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        const editor = tinymce.get('news-content-tinymce');
        const hidden = document.getElementById('news-content-hidden');
        if (editor && hidden) {
            hidden.value = editor.getContent();
        }
    });
})();
</script>
<script>
(function() {
    let tags = {!! json_encode($newsTags) !!};
    const container = document.getElementById('tags-container');
    const input = document.getElementById('tag-input');
    const hidden = document.getElementById('tags-hidden');

    function render() {
        container.innerHTML = '';
        tags.forEach((tag, i) => {
            const el = document.createElement('span');
            el.className = 'inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-lg';
            el.innerHTML = tag + ' <button type="button" onclick="removeTag(' + i + ')" class="text-indigo-400 hover:text-indigo-700">&times;</button>';
            container.appendChild(el);
        });
        hidden.value = JSON.stringify(tags);
    }

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = this.value.trim();
            if (val && !tags.includes(val)) {
                tags.push(val);
                render();
            }
            this.value = '';
        }
    });

    window.removeTag = function(i) {
        tags.splice(i, 1);
        render();
    };

    render();
})();
</script>
@endpush
@endsection
