@props(['context' => 'general', 'contextTitle' => ''])

@php
$aiIcon = '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"><path d="M12 2L4 7v10l8 5 8-5V7l-8-5z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" fill="currentColor" opacity=".7"/><path d="M12 2v7M12 15v7M4 7l6 3M14 12l6 3M4 17l6-3M14 12l6-3" stroke="currentColor" stroke-width="1" opacity=".5"/></svg>';
$aiSuggestions = [
    'course' => [__('ai_assistant_suggestion_course_1'), __('ai_assistant_suggestion_course_2'), __('ai_assistant_suggestion_course_3')],
    'lesson' => [__('ai_assistant_suggestion_lesson_1'), __('ai_assistant_suggestion_lesson_2'), __('ai_assistant_suggestion_lesson_3')],
    'roadmap' => [__('ai_assistant_suggestion_roadmap_1'), __('ai_assistant_suggestion_roadmap_2'), __('ai_assistant_suggestion_roadmap_3')],
    'contest' => [__('ai_assistant_suggestion_contest_1'), __('ai_assistant_suggestion_contest_2'), __('ai_assistant_suggestion_contest_3')],
    'general' => [__('ai_assistant_suggestion_general_1'), __('ai_assistant_suggestion_general_2'), __('ai_assistant_suggestion_general_3')],
];
$aiSuggestionsJson = array_map(
    fn (array $suggestions) => json_encode($suggestions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
    $aiSuggestions
);
@endphp

<div x-data="aiAssistant()" x-init="init()" class="ai-assistant-fab" :class="{ 'ai-open': isOpen }">
    <button @click="toggle()" class="ai-fab-btn" :class="{ 'ai-fab-active': isOpen }" title="AI Assistant">
        <span x-show="!isOpen" class="ai-fab-icon">{!! $aiIcon !!}</span>
        <i x-show="isOpen" class="fas fa-times"></i>
        <span class="ai-fab-pulse" x-show="!isOpen && !replied"></span>
    </button>

    <div x-show="isOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-90 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90 translate-y-4" class="ai-panel" @click.away="isOpen = false">
        <div class="ai-panel-header">
            <div class="ai-panel-title">
                <span class="ai-fab-icon" style="font-size:18px">{!! $aiIcon !!}</span>
                <span>{{ __('ai_assistant_title') }}</span>
                <span class="ai-panel-context">{{ $contextTitle }}</span>
            </div>
            <div class="ai-panel-actions">
                <button @click="clearChat()" class="ai-action-btn" title="{{ __('ai_assistant_clear') }}">
                    <i class="fas fa-broom"></i>
                </button>
            </div>
        </div>

        <div class="ai-panel-messages" x-ref="messagesContainer">
            <template x-if="messages.length === 0">
                <div class="ai-welcome">
                    <div class="ai-welcome-icon">{!! $aiIcon !!}</div>
                    <p class="ai-welcome-title">{{ __('ai_assistant_welcome_title') }}</p>
                    <p class="ai-welcome-desc">{{ __('ai_assistant_welcome_desc', ['context' => $contextTitle ?: $context]) }}</p>
                    <div class="ai-suggestions">
                        <template x-for="s in suggestions" :key="s">
                            <button @click="sendMessage(s)" class="ai-suggestion-btn" x-text="s"></button>
                        </template>
                    </div>
                </div>
            </template>
            <template x-for="(msg, i) in messages" :key="i">
                <div class="ai-msg" :class="msg.role === 'user' ? 'ai-msg-user' : 'ai-msg-ai'">
                    <div class="ai-msg-avatar" x-show="msg.role === 'assistant'">
                        <span class="ai-fab-icon" style="font-size:14px">{!! $aiIcon !!}</span>
                    </div>
                    <div class="ai-msg-content" x-html="formatMessage(msg.content)"></div>
                </div>
            </template>
            <div x-show="loading" class="ai-msg ai-msg-ai">
                <div class="ai-msg-avatar"><span class="ai-fab-icon" style="font-size:14px">{!! $aiIcon !!}</span></div>
                <div class="ai-msg-content ai-typing">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>

        <form @submit.prevent="sendMessage()" class="ai-panel-input">
            <input x-model="input" type="text" placeholder="{{ __('ai_assistant_placeholder') }}" class="ai-input" :disabled="loading" @keydown.enter.prevent="sendMessage()">
            <button type="submit" class="ai-send-btn" :disabled="loading || !input.trim()">
                <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
            </button>
        </form>
    </div>
</div>

<script>
function aiAssistant() {
    return {
        isOpen: false,
        input: '',
        messages: [],
        loading: false,
        replied: false,
        suggestions: [],
        context: @json($context),
        contextTitle: @json($contextTitle),

        init() {
            const ctx = this.context;
            if (ctx === 'course') {
                this.suggestions = {!! $aiSuggestionsJson['course'] !!};
            } else if (ctx === 'lesson') {
                this.suggestions = {!! $aiSuggestionsJson['lesson'] !!};
            } else if (ctx === 'roadmap') {
                this.suggestions = {!! $aiSuggestionsJson['roadmap'] !!};
            } else if (ctx === 'contest') {
                this.suggestions = {!! $aiSuggestionsJson['contest'] !!};
            } else {
                this.suggestions = {!! $aiSuggestionsJson['general'] !!};
            }
        },

        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    if (this.$refs.messagesContainer) {
                        this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                    }
                });
            }
        },

        async sendMessage(text) {
            const msg = text || this.input.trim();
            if (!msg || this.loading) return;

            this.messages.push({ role: 'user', content: msg });
            this.input = '';
            this.loading = true;
            this.replied = true;
            this.scrollBottom();

            try {
                const ctxMsg = '[Context: ' + this.context + ' - ' + this.contextTitle + '] ';
                const res = await fetch('/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: ctxMsg + msg })
                });
                const data = await res.json();
                this.messages.push({ role: 'assistant', content: data.reply || data.message || '{{ __("ai_assistant_error_response") }}' });
            } catch (e) {
                this.messages.push({ role: 'assistant', content: '{{ __("ai_assistant_error_connection") }}' });
            }

            this.loading = false;
            this.scrollBottom();
        },

        async clearChat() {
            try {
                await fetch('/ai/clear', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
            } catch (e) {}
            this.messages = [];
            this.replied = false;
        },

        formatMessage(text) {
            if (!text) return '';
            text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            text = text.replace(/`(.*?)`/g, '<code>$1</code>');
            text = text.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');
            text = text.replace(/\n/g, '<br>');
            return text;
        },

        scrollBottom() {
            this.$nextTick(() => {
                if (this.$refs.messagesContainer) {
                    this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                }
            });
        }
    };
}
</script>
