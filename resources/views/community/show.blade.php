@extends('layouts.app')

@section('title', $post->title . ' - CodeMaster')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="postPage()">
    <div class="mb-6">
        <a href="{{ route('community.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm">
            <i class="fas fa-arrow-left mr-1"></i>{{ __('Back to Community') }}
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <div class="flex items-start space-x-4 mb-6">
            <img src="{{ $post->user->avatar_url }}" class="w-12 h-12 rounded-full">
            <div class="flex-1">
                <template x-if="!editing">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $post->title }}</h1>
                        @if($post->tags->count())
                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;margin-bottom:4px">
                            @foreach($post->tags as $tag)
                            <a href="{{ route('community.index', ['tag' => $tag->slug]) }}" style="padding:4px 12px;border-radius:6px;font-size:12px;font-weight:600;background:var(--bg);color:var(--text-muted);border:1px solid var(--border);text-decoration:none;transition:all 0.2s">{{ $tag->name }}</a>
                            @endforeach
                        </div>
                        @endif
                        <div class="flex items-center space-x-3 text-sm text-gray-500">
                            <a href="{{ route('profile.show', $post->user_id) }}" class="hover:text-indigo-600 transition">{{ $post->user->name }}</a>
                            <span>&middot;</span>
                            <span>{{ $post->created_at->diffForHumans() }}</span>
                            <span>&middot;</span>
                            <span><i class="fas fa-eye mr-1"></i>{{ $post->views_count }}</span>
                            <span>&middot;</span>
                            <span><i class="fas fa-comment mr-1"></i>{{ $post->comments_count }}</span>
                        </div>
                    </div>
                </template>
                <template x-if="editing">
                    <div class="space-y-3">
                        <input type="text" x-model="editTitle" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-bold text-lg">
                        <div id="cm-show-edit-wrap">
                            <textarea id="cm-show-edit" style="width:100%;min-height:250px"></textarea>
                        </div>
                        <input type="hidden" id="cm-show-edit-content" value="">
                        <div class="flex space-x-2">
                            <button @click="cancelEdit()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition">{{ __('Cancel') }}</button>
                            <button @click="saveEdit()" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">{{ __('Save') }}</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <template x-if="!editing">
            <div class="prose max-w-none text-gray-700 mb-8">
                {!! clean($post->content) !!}
            </div>
        </template>

        <div class="flex items-center space-x-4 border-t border-gray-100 pt-4">
            <button @click="toggleLike()" class="flex items-center space-x-2 text-sm transition" :class="liked ? 'text-red-500' : 'text-gray-500 hover:text-red-500'">
                <i :class="liked ? 'fas fa-heart' : 'far fa-heart'"></i>
                <span x-text="likes">{{ $post->likes_count }}</span>
            </button>

            @if($post->user_id === Auth::id() || Auth::user()->role === 'admin')
            <div class="flex items-center space-x-2 ml-auto">
                <button @click="startEdit()" class="text-sm text-gray-400 hover:text-indigo-500 transition">
                    <i class="fas fa-edit mr-1"></i>{{ __('Edit') }}
                </button>
                <form action="{{ route('community.destroy', $post->id) }}" method="POST" onsubmit="return confirm('{{ __("Delete this post?") }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm text-gray-400 hover:text-red-500 transition">
                        <i class="fas fa-trash mr-1"></i>{{ __('Delete') }}
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Comments') }} ({{ $post->comments_count }})</h3>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
            <form action="{{ route('community.comment') }}" method="POST">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <textarea name="content" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="{{ __('Write a comment...') }}" required></textarea>
                <div class="flex justify-end mt-3">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-500 transition">{{ __('Post Comment') }}</button>
                </div>
            </form>
        </div>

        @foreach($post->comments as $comment)
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-4">
            <div class="flex items-start space-x-3">
                <img src="{{ $comment->user->avatar_url }}" class="w-8 h-8 rounded-full">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                        <a href="{{ route('profile.show', $comment->user_id) }}" class="font-medium text-gray-900 text-sm hover:text-indigo-600 transition">{{ $comment->user->name }}</a>
                        <span class="text-gray-400 text-xs">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-700 text-sm">{{ $comment->content }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('postPage', () => ({
        editing: false,
        editTitle: @js($post->title),
        editContent: @js($post->content),
        liked: {{ $post->isLikedBy(Auth::id()) ? 'true' : 'false' }},
        likes: {{ $post->likes_count }},
        cmShowEditor: null,

        startEdit() {
            this.editing = true;
            this.$nextTick(() => this.initEditor());
        },

        cancelEdit() {
            this.destroyEditor();
            this.editing = false;
        },

        initEditor() {
            if (this.cmShowEditor) return;
            const self = this;
            const isDark = !(document.documentElement.getAttribute('data-theme') || '').includes('light');
            tinymce.init({
                selector: '#cm-show-edit',
                height: 300,
                skin: isDark ? 'oxide-dark' : 'oxide',
                content_css: isDark ? 'dark' : 'default',
                menubar: false,
                plugins: 'lists link image code codesample fullscreen quickbars',
                toolbar: 'undo redo | blocks | bold italic strikethrough | link image codesample | bullist numlist | code fullscreen',
                codesample_languages: [
                    {text: 'HTML/XML', value: 'markup'}, {text: 'JavaScript', value: 'javascript'},
                    {text: 'TypeScript', value: 'typescript'}, {text: 'CSS', value: 'css'},
                    {text: 'PHP', value: 'php'}, {text: 'Python', value: 'python'},
                    {text: 'Java', value: 'java'}, {text: 'C', value: 'c'}, {text: 'C++', value: 'cpp'},
                ],
                content_style: 'body { font-family: Inter, sans-serif; font-size: 14px; color: ' + (isDark ? '#e2e8f0' : '#1e293b') + '; }',
                setup: (editor) => {
                    self.cmShowEditor = editor;
                    editor.on('init', () => {
                        editor.setContent(self.editContent || '');
                    });
                }
            });
        },

        destroyEditor() {
            if (this.cmShowEditor) {
                tinymce.remove('#cm-show-edit');
                this.cmShowEditor = null;
            }
        },

        async saveEdit() {
            let content = '';
            if (this.cmShowEditor) {
                content = this.cmShowEditor.getContent();
            }
            if (!this.editTitle.trim() || !content.trim()) return;
            try {
                const res = await fetch(`/community/{{ $post->id }}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title: this.editTitle, content: content }),
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        },

        async toggleLike() {
            try {
                const res = await fetch(`/community/{{ $post->id }}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.liked = data.liked;
                    this.likes = data.likes;
                }
            } catch (e) {
                console.error(e);
            }
        }
    }));
});
</script>
@endsection
