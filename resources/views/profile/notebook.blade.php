@extends('layouts.app')
@section('title', __('Notebook'))

@section('head')
<style>
    .nb-page { background: var(--bg); color: var(--text); position: relative; overflow-x: clip; }
    .nb-page::before { content: ''; position: fixed; inset: 0; pointer-events: none; z-index: 0;
        background:
            radial-gradient(ellipse 45% 32% at 12% 0%, var(--accent-glow) 0%, transparent 60%),
            radial-gradient(ellipse 32% 30% at 92% 88%, rgba(139,92,246,.08) 0%, transparent 60%); }
    .nb-wrap { max-width: 860px; margin: 0 auto; padding: 110px 20px 90px; position: relative; z-index: 1; }
    .nb-head { display: flex; align-items: center; gap: 16px; margin-bottom: 22px;
        opacity: 0; transform: translateY(20px); animation: nbIn .6s cubic-bezier(.16,1,.3,1) forwards; }
    @@keyframes nbIn { to { opacity: 1; transform: none; } }
    .nb-ico { width: 58px; height: 58px; border-radius: 18px; display: flex; align-items: center; justify-content: center;
        font-size: 24px; color: #fff; background: linear-gradient(135deg,var(--accent),#8b5cf6); flex-shrink: 0;
        box-shadow: 0 12px 30px var(--accent-glow-strong); animation: nbFloat 5s ease-in-out infinite; }
    @@keyframes nbFloat { 0%,100% { transform: translateY(0) rotate(-3deg); } 50% { transform: translateY(-6px) rotate(3deg); } }
    .nb-title { font-size: 26px; font-weight: 900; color: var(--text); letter-spacing: -.8px; }
    .nb-sub { font-size: 13px; color: var(--text-muted); margin-top: 3px; }
    .nb-sub b { color: var(--accent); font-family: var(--font-mono); }
    .nb-new-btn { margin-left: auto; display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px;
        border-radius: 14px; background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff; border: none;
        font-size: 13px; font-weight: 800; cursor: pointer; transition: all .25s; flex-shrink: 0;
        box-shadow: 0 8px 24px var(--accent-glow-strong); }
    .nb-new-btn:hover { transform: translateY(-2px) scale(1.02); }
    .nb-search { position: relative; margin-bottom: 20px;
        opacity: 0; transform: translateY(20px); animation: nbIn .6s .1s cubic-bezier(.16,1,.3,1) forwards; }
    .nb-search input { width: 100%; box-sizing: border-box; padding: 13px 18px 13px 46px; border-radius: 14px;
        border: 1px solid var(--border); background: var(--card); color: var(--text); font-size: 13.5px; outline: none;
        transition: all .2s; box-shadow: 0 8px 26px rgba(0,0,0,.08); }
    .nb-search input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
    .nb-search > i { position: absolute; left: 17px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 13px; }
    .nb-editor { margin-bottom: 22px; padding: 20px; border-radius: 20px; border: 1px solid var(--accent-glow-strong);
        background: var(--card); box-shadow: 0 18px 50px rgba(0,0,0,.14); }
    .nb-input { width: 100%; box-sizing: border-box; padding: 12px 16px; border-radius: 12px;
        border: 1px solid var(--border); background: var(--bg-secondary); color: var(--text);
        font-size: 13.5px; margin-bottom: 10px; outline: none; transition: all .2s; }
    .nb-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
    .nb-editor-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 12px; }
    .nb-btn { padding: 10px 22px; border-radius: 11px; font-size: 13px; font-weight: 700; cursor: pointer;
        transition: all .2s; border: 1px solid var(--border); }
    .nb-btn-cancel { background: transparent; color: var(--text-secondary); }
    .nb-btn-cancel:hover { border-color: var(--accent); color: var(--accent); }
    .nb-btn-save { background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff; border: none;
        box-shadow: 0 6px 18px var(--accent-glow-strong); }
    .nb-btn-save:hover { transform: translateY(-1px); }
    .nb-grid { display: grid; gap: 14px; }
    .nb-note { position: relative; padding: 20px 20px 16px; border-radius: 18px; border: 1px solid var(--border);
        background: var(--card); box-shadow: 0 10px 30px rgba(0,0,0,.08); overflow: hidden;
        opacity: 0; transform: translateY(22px);
        transition: opacity .5s ease, transform .5s cubic-bezier(.16,1,.3,1), border-color .25s, box-shadow .25s; }
    .nb-note.in { opacity: 1; transform: none; }
    .nb-note:hover { border-color: var(--accent); box-shadow: 0 16px 44px rgba(0,0,0,.14); }
    .nb-note::before { content: ''; position: absolute; top: 0; left: 22px; right: 22px; height: 3px; border-radius: 3px;
        background: linear-gradient(90deg,var(--accent),#8b5cf6,transparent); opacity: 0; transition: opacity .3s; }
    .nb-note:hover::before { opacity: 1; }
    .nb-pin { position: absolute; top: 14px; right: 16px; width: 12px; height: 12px; border-radius: 50%;
        background: linear-gradient(135deg,var(--accent),#8b5cf6); box-shadow: 0 0 0 4px var(--accent-glow); }
    .nb-note-top { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 8px; padding-right: 20px; }
    .nb-note-title { font-size: 15px; font-weight: 800; color: var(--text); letter-spacing: -.2px; }
    .nb-note-prob { display: inline-flex; align-items: center; gap: 5px; margin-top: 5px; font-size: 11.5px;
        font-weight: 700; color: var(--accent); text-decoration: none; font-family: var(--font-mono); }
    .nb-note-prob:hover { text-decoration: underline; }
    .nb-del { margin-left: auto; flex-shrink: 0; background: none; border: none; color: var(--text-muted);
        cursor: pointer; font-size: 13px; padding: 6px 8px; border-radius: 8px; transition: all .2s; }
    .nb-del:hover { color: #ef4444; background: rgba(239,68,68,.1); transform: scale(1.1); }
    .nb-excerpt { font-size: 13px; color: var(--text-secondary); line-height: 1.65; }
    .nb-tags { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 10px; }
    .nb-tag { font-size: 10.5px; padding: 3px 10px; border-radius: 100px; background: var(--accent-glow);
        color: var(--accent); font-weight: 700; font-family: var(--font-mono); cursor: pointer; border: 1px solid transparent; }
    .nb-tag:hover { border-color: var(--accent-glow-strong); }
    .nb-date { font-size: 11px; color: var(--text-muted); margin-top: 10px; font-family: var(--font-mono);
        display: flex; align-items: center; gap: 6px; }
    .nb-taskbox { margin-top: 12px; border-radius: 14px; border: 1px solid var(--border);
        background: var(--bg-secondary); padding: 12px 14px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .nb-taskbox-info { min-width: 0; flex: 1; }
    .nb-taskbox-t { font-size: 13px; font-weight: 800; color: var(--text); overflow: hidden;
        text-overflow: ellipsis; white-space: nowrap; }
    .nb-taskbox-s { font-size: 11px; color: var(--text-muted); margin-top: 4px; font-family: var(--font-mono);
        display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .nb-diff { font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 2px 8px; border-radius: 6px; }
    .nb-diff.easy { background: rgba(34,197,94,.12); color: #22c55e; }
    .nb-diff.medium { background: rgba(245,158,11,.12); color: #f59e0b; }
    .nb-diff.hard { background: rgba(239,68,68,.12); color: #ef4444; }
    .nb-solved-yes { color: #22c55e; font-weight: 800; }
    .nb-solved-no { color: var(--text-muted); }
    .nb-open { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 12px;
        background: linear-gradient(135deg,var(--accent),#8b5cf6); color: #fff; font-size: 12.5px; font-weight: 800;
        text-decoration: none; flex-shrink: 0; transition: all .25s;
        box-shadow: 0 6px 20px var(--accent-glow-strong); }
    .nb-open:hover { transform: translateY(-2px) scale(1.02); }
    .nb-empty { text-align: center; padding: 70px 24px; border-radius: 20px; border: 1.5px dashed var(--border-hover);
        background: var(--card); }
    .nb-empty i { font-size: 44px; margin-bottom: 14px; display: block; opacity: .25;
        background: linear-gradient(135deg,var(--accent),#8b5cf6); -webkit-background-clip: text;
        -webkit-text-fill-color: transparent; background-clip: text; }
    .nb-empty p { font-size: 14px; color: var(--text-muted); }
    .nb-empty small { font-size: 12px; color: var(--text-muted); opacity: .7; }
    @@media(max-width: 640px) {
        .nb-head { flex-wrap: wrap; }
        .nb-new-btn { width: 100%; justify-content: center; margin-left: 0; }
    }
</style>
@endsection

@section('content')
<div class="nb-page">
<div class="nb-wrap" x-data="notebookApp()">
    <div class="nb-head" style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
        <div class="nb-ico" style="width:58px;height:58px;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:24px;color:#fff;background:linear-gradient(135deg,var(--accent),#8b5cf6);flex-shrink:0;box-shadow:0 12px 30px var(--accent-glow-strong);animation:nbFloat 5s ease-in-out infinite"><i class="fas fa-note-sticky"></i></div>
        <div style="min-width:0">
            <h1 class="nb-title" style="font-size:26px;font-weight:900;color:var(--text);letter-spacing:-.8px;margin:0">{{ __('Notebook') }}</h1>
            <p class="nb-sub" style="font-size:13px;color:var(--text-muted);margin:3px 0 0">{{ __('Your coding notes and insights') }} • <b>{{ $notes->count() }}</b></p>
        </div>
        <button @click="showNew = !showNew; toggleEditor()" class="nb-new-btn">
            <i class="fas fa-plus"></i> {{ __('New Note') }}
        </button>
    </div>

    <div class="nb-search">
        <i class="fas fa-search"></i>
        <input type="text" id="nbSearch" oninput="nbFilter(this.value)" placeholder="{{ __('Search notes...') }}" autocomplete="off">
    </div>

    <div x-show="showNew" x-cloak x-transition style="margin-bottom:20px" class="nb-editor">
        <form action="{{ route('profile.notebook.store') }}" method="POST" onsubmit="syncNotebookContent()">
            @csrf
            <input type="text" name="title" x-model="title" placeholder="{{ __('Title (optional)') }}" class="nb-input">
            <div x-show="editorLoading" style="padding:30px;text-align:center;color:var(--text-muted);font-size:13px"><i class="fas fa-spinner fa-spin" style="margin-right:8px"></i>{{ __('Loading editor...') }}</div>
            <textarea id="notebook-tinymce" style="width:100%;min-height:200px"></textarea>
            <input type="hidden" name="content" id="notebook-content-hidden" value="">
            <input type="text" name="tags" x-model="tags" placeholder="{{ __('Tags (comma separated)') }}" class="nb-input" style="margin-top:10px;margin-bottom:0">
            <div class="nb-editor-actions">
                <button type="button" @click="showNew = false; destroyEditor()" class="nb-btn nb-btn-cancel">{{ __('Cancel') }}</button>
                <button type="submit" class="nb-btn nb-btn-save"><i class="fas fa-check" style="margin-right:6px"></i>{{ __('Save') }}</button>
            </div>
        </form>
    </div>

    <div class="nb-grid" id="nbGrid">
        @forelse($notes as $note)
            <div class="nb-note" data-search="{{ e(strtolower(($note->title ?? '') . ' ' . strip_tags($note->content ?? '') . ' ' . ($note->tags ?? ''))) }}" data-i="{{ $loop->index }}">
                <span class="nb-pin"></span>
                <div class="nb-note-top">
                    <div style="min-width:0">
                        @if($note->title)
                            <div class="nb-note-title">{{ $note->title }}</div>
                        @endif
                        @if($note->problem)
                            <a href="{{ route('problems.show', $note->problem->slug) }}" class="nb-note-prob"><i class="fas fa-link" style="font-size:10px"></i>{{ $note->problem->title }}</a>
                        @endif
                    </div>
                    <form action="{{ route('profile.notebook.delete', $note->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete this note?') }}')" style="margin-left:auto">
                        @csrf @method('DELETE')
                        <button type="submit" class="nb-del"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
                <div class="nb-excerpt">{{ Str::limit(strip_tags($note->content), 300) }}</div>
                @if($note->problem)
                @php $st = $subStats[$note->problem_id] ?? null; @endphp
                <div class="nb-taskbox">
                    <div class="nb-taskbox-info">
                        <div class="nb-taskbox-t">{{ $note->problem->title }}</div>
                        <div class="nb-taskbox-s">
                            <span class="nb-diff {{ $note->problem->difficulty }}">{{ $note->problem->difficulty }}</span>
                            @if($st)
                                <span class="{{ $st['solved'] ? 'nb-solved-yes' : 'nb-solved-no' }}">
                                    @if($st['solved'])<i class="fas fa-check-circle"></i> solved @else unsolved @endif
                                </span>
                                <span>{{ $st['attempts'] }} {{ __('attempts') }}</span>
                                @if($st['last'])<span>• {{ $st['last']->diffForHumans() }}</span>@endif
                            @else
                                <span>{{ __('no attempts yet') }}</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('problems.show', $note->problem->slug) }}" class="nb-open">{{ __('Open task') }}<i class="fas fa-arrow-right"></i></a>
                </div>
                @endif
                @if($note->tags)
                    <div class="nb-tags">
                        @foreach(explode(',', $note->tags) as $tag)
                            <span class="nb-tag" data-tag="{{ e(trim($tag)) }}" onclick="nbTag(this.dataset.tag)">#{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                @endif
                <div class="nb-date"><i class="far fa-clock" style="font-size:10px"></i>{{ $note->updated_at->diffForHumans() }}</div>
            </div>
        @empty
            <div class="nb-empty">
                <i class="fas fa-sticky-note"></i>
                <p>{{ __('No notes yet') }}</p>
                <small>{{ __('Ideas, snippets, gotchas — pin them here') }}</small>
            </div>
        @endforelse
    </div>
</div>
</div>

@push('scripts')
<script>
/* Грузим TinyMCE с fallback-CDN, если head-скрипт не сработал (блокер/сеть).
   В худшем случае остаёмся на обычном textarea — сохранение работает. */
function nbEnsureTiny(cb, tries) {
    if (window.tinymce) { cb(null); return; }
    tries = tries || 0;
    if (tries >= 2) { cb(new Error('tinymce unavailable')); return; }
    var src = tries === 0
        ? 'https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js'
        : 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js';
    var s = document.createElement('script');
    s.src = src;
    s.referrerPolicy = 'origin';
    s.onload = function() { cb(null); };
    s.onerror = function() { nbEnsureTiny(cb, tries + 1); };
    document.head.appendChild(s);
}

function notebookApp() {
    return {
        showNew: false,
        title: '',
        content: '',
        tags: '',
        editorInstance: null,
        editorLoading: false,

        toggleEditor() {
            if (this.showNew) {
                this.$nextTick(() => this.initEditor());
            } else {
                this.destroyEditor();
            }
        },

        initEditor() {
            if (this.editorInstance) return;
            const self = this;
            const isDark = !(document.documentElement.getAttribute('data-theme') || '').includes('light');
            self.editorLoading = true;
            nbEnsureTiny(function(err) {
                self.editorLoading = false;
                if (err || !window.tinymce) {
                    console.error('[notebook] TinyMCE failed to load, using plain textarea');
                    return;
                }
                try {
                    tinymce.init({
                        selector: '#notebook-tinymce',
                        height: 300,
                        skin: isDark ? 'oxide-dark' : 'oxide',
                        content_css: isDark ? 'dark' : 'default',
                        menubar: false,
                        promotion: false,
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
                            self.editorInstance = editor;
                        }
                    });
                } catch (e) {
                    console.error('[notebook] TinyMCE init failed, using plain textarea', e);
                }
            });
        },

        destroyEditor() {
            if (this.editorInstance) {
                try {
                    if (window.tinymce) tinymce.remove('#notebook-tinymce');
                } catch (e) { console.error(e); }
                this.editorInstance = null;
            }
            this.editorLoading = false;
        }
    }
}

function syncNotebookContent() {
    try {
        if (!window.tinymce) return;
        const editor = tinymce.get('notebook-tinymce');
        const hidden = document.getElementById('notebook-content-hidden');
        if (editor && hidden) {
            hidden.value = editor.getContent();
        } else if (hidden) {
            const ta = document.getElementById('notebook-tinymce');
            if (ta) hidden.value = ta.value;
        }
    } catch (e) {
        console.error(e);
    }
}

/* client-side search + tag filter + stagger reveal */
function nbFilter(q) {
    q = (q || '').toLowerCase().trim();
    document.querySelectorAll('#nbGrid .nb-note').forEach(function(card) {
        var hay = card.dataset.search || '';
        card.style.display = (!q || hay.includes(q)) ? '' : 'none';
    });
}
function nbTag(t) {
    var inp = document.getElementById('nbSearch');
    if (inp) { inp.value = t; nbFilter(t); inp.focus(); }
}
(function() {
    var cards = document.querySelectorAll('#nbGrid .nb-note');
    if ('IntersectionObserver' in window && cards.length) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(en) {
                if (en.isIntersecting) {
                    var el = en.target;
                    el.style.transitionDelay = (parseInt(el.dataset.i || 0, 10) % 8 * 0.06) + 's';
                    el.classList.add('in');
                    io.unobserve(el);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
        cards.forEach(function(c) { io.observe(c); });
        setTimeout(function() { cards.forEach(function(c) { c.classList.add('in'); }); }, 4000);
    } else {
        cards.forEach(function(c) { c.classList.add('in'); });
    }
})();
</script>
@endpush
@endsection
