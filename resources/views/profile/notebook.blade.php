@extends('layouts.app')
@section('title', __('Notebook'))

@section('content')
<div style="max-width:800px;margin:0 auto;padding:24px 16px" x-data="notebookApp()">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div>
            <h1 style="font-size:20px;font-weight:800;color:var(--text)">{{ __('Notebook') }}</h1>
            <p style="font-size:13px;color:var(--text-muted);margin-top:2px">{{ __('Your coding notes and insights') }}</p>
        </div>
        <button @click="showNew = !showNew; toggleEditor()" style="padding:8px 16px;border-radius:8px;background:var(--accent);color:white;border:none;font-size:13px;font-weight:600;cursor:pointer">
            <i class="fas fa-plus" style="margin-right:4px"></i> {{ __('New Note') }}
        </button>
    </div>

    <div x-show="showNew" x-transition style="margin-bottom:20px;padding:16px;border-radius:12px;border:1px solid var(--border);background:var(--bg-2)">
        <form action="{{ route('profile.notebook.store') }}" method="POST" onsubmit="syncNotebookContent()">
            @csrf
            <input type="text" name="title" x-model="title" placeholder="{{ __('Title (optional)') }}" style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:13px;margin-bottom:8px;box-sizing:border-box">
            <textarea id="notebook-tinymce" style="width:100%;min-height:200px"></textarea>
            <input type="hidden" name="content" id="notebook-content-hidden" value="">
            <input type="text" name="tags" x-model="tags" placeholder="{{ __('Tags (comma separated)') }}" style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:13px;margin-bottom:12px;box-sizing:border-box;margin-top:8px">
            <div style="display:flex;gap:8px;justify-content:flex-end">
                <button type="button" @click="showNew = false; destroyEditor()" style="padding:8px 16px;border-radius:8px;border:1px solid var(--border);background:transparent;color:var(--text-secondary);font-size:13px;cursor:pointer">{{ __('Cancel') }}</button>
                <button type="submit" style="padding:8px 16px;border-radius:8px;background:var(--accent);color:white;border:none;font-size:13px;font-weight:600;cursor:pointer">{{ __('Save') }}</button>
            </div>
        </form>
    </div>

    <div style="display:grid;gap:12px">
        @forelse($notes as $note)
            <div style="padding:16px;border-radius:12px;border:1px solid var(--border);background:var(--bg-2)">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px">
                    <div>
                        @if($note->title)
                            <div style="font-size:14px;font-weight:700;color:var(--text)">{{ $note->title }}</div>
                        @endif
                        @if($note->problem)
                            <a href="{{ route('problems.show', $note->problem->slug) }}" style="font-size:11px;color:var(--accent);text-decoration:none">{{ $note->problem->title }}</a>
                        @endif
                    </div>
                    <form action="{{ route('profile.notebook.delete', $note->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete this note?') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:12px"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
                <div style="font-size:13px;color:var(--text-secondary);line-height:1.6">{!! Str::limit(strip_tags($note->content), 300) !!}</div>
                @if($note->tags)
                    <div style="display:flex;gap:4px;flex-wrap:wrap;margin-top:8px">
                        @foreach(explode(',', $note->tags) as $tag)
                            <span style="font-size:10px;padding:2px 6px;border-radius:4px;background:var(--accent-glow);color:var(--accent);font-weight:600">{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                @endif
                <div style="font-size:11px;color:var(--text-muted);margin-top:8px">{{ $note->updated_at->diffForHumans() }}</div>
            </div>
        @empty
            <div style="text-align:center;padding:48px;border-radius:14px;border:1px dashed var(--border);background:var(--bg-2)">
                <i class="fas fa-sticky-note" style="font-size:28px;color:var(--text-muted);opacity:.3;margin-bottom:8px;display:block"></i>
                <p style="font-size:14px;color:var(--text-muted)">{{ __('No notes yet') }}</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function notebookApp() {
    return {
        showNew: false,
        title: '',
        content: '',
        tags: '',
        editorInstance: null,

        toggleEditor() {
            if (this.showNew) {
                this.$nextTick(() => this.initEditor());
            } else {
                this.destroyEditor();
            }
        },

        initEditor() {
            if (this.editorInstance) return;
            const isDark = !(document.documentElement.getAttribute('data-theme') || '').includes('light');
            tinymce.init({
                selector: '#notebook-tinymce',
                height: 300,
                skin: isDark ? 'oxide-dark' : 'oxide',
                content_css: isDark ? 'dark' : 'default',
                menubar: false,
                plugins: 'lists link image code codesample fullscreen quickbars',
                toolbar: 'undo redo | blocks | bold italic strikethrough | link image codesample | bullist numlist | code fullscreen',
                codesample_languages: [
                    {text: 'HTML/XML', value: 'markup'},
                    {text: 'JavaScript', value: 'javascript'},
                    {text: 'TypeScript', value: 'typescript'},
                    {text: 'CSS', value: 'css'},
                    {text: 'PHP', value: 'php'},
                    {text: 'Python', value: 'python'},
                    {text: 'Java', value: 'java'},
                    {text: 'C', value: 'c'},
                    {text: 'C++', value: 'cpp'},
                    {text: 'C#', value: 'csharp'},
                    {text: 'Ruby', value: 'ruby'},
                    {text: 'Go', value: 'go'},
                    {text: 'Rust', value: 'rust'},
                ],
                content_style: 'body { font-family: Inter, sans-serif; font-size: 14px; color: ' + (isDark ? '#e2e8f0' : '#1e293b') + '; }',
                setup: (editor) => {
                    this.editorInstance = editor;
                }
            });
        },

        destroyEditor() {
            if (this.editorInstance) {
                tinymce.remove('#notebook-tinymce');
                this.editorInstance = null;
            }
        }
    }
}

function syncNotebookContent() {
    const editor = tinymce.get('notebook-tinymce');
    const hidden = document.getElementById('notebook-content-hidden');
    if (editor && hidden) {
        hidden.value = editor.getContent();
    }
}
</script>
@endpush
@endsection
