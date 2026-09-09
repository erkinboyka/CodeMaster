@extends('layouts.app')

@section('title', __('Chat') . ' - CodeMaster')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="chatApp()">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('vacancies.show', $application->vacancy_id) }}" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-briefcase text-indigo-600 text-sm"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900">{{ $application->vacancy->title ?? __('Vacancy') }}</h2>
                    <div class="flex items-center space-x-1">
                        <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                        <span class="text-xs text-gray-500">{{ $application->vacancy->company ?? __('Company') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('peer.joinForm') }}" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 text-xs font-medium rounded-full hover:bg-indigo-100 transition" title="{{ __('Join Interview Room') }}">
                <i class="fas fa-video mr-1"></i>{{ __('Interview') }}
            </a>
            <span class="px-3 py-1 bg-yellow-50 text-yellow-600 text-xs font-medium rounded-full">
                <i class="fas fa-clock mr-1"></i>{{ __('Pending Review') }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="h-[500px] overflow-y-auto p-6 space-y-4 bg-gray-50" id="chat-messages" x-ref="chatBox">
            <template x-if="messages.length === 0">
                <div class="flex items-center justify-center h-full text-gray-400">
                    <div class="text-center">
                        <i class="fas fa-comments text-4xl mb-3"></i>
                        <p class="text-sm">{{ __('Start a conversation') }}</p>
                    </div>
                </div>
            </template>
            <template x-for="(msg, index) in messages" :key="msg.id || index">
                <div :class="msg.sender_id == currentUserId ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.sender_id == currentUserId ? 'bg-indigo-600 text-white rounded-2xl rounded-br-md' : 'bg-white text-gray-800 rounded-2xl rounded-bl-md shadow-sm'" class="px-4 py-3 max-w-[70%]">

                        <template x-if="msg.message_type === 'image' && msg.file_url">
                            <div class="mb-2">
                                <img :src="msg.file_url" alt="" class="rounded-lg max-w-full max-h-64 cursor-pointer" @click="openLightbox(msg.file_url)">
                            </div>
                        </template>

                        <template x-if="msg.message_type === 'video' && msg.file_url">
                            <div class="mb-2">
                                <video :src="msg.file_url" controls class="rounded-lg max-w-full max-h-64"></video>
                            </div>
                        </template>

                        <template x-if="msg.message_type === 'audio' && msg.file_url">
                            <div class="mb-2">
                                <audio :src="msg.file_url" controls class="w-full max-w-xs"></audio>
                            </div>
                        </template>

                        <template x-if="msg.message_type === 'file' && msg.file_url">
                            <div class="mb-2 p-2 rounded-lg flex items-center space-x-2" :class="msg.sender_id == currentUserId ? 'bg-white/10' : 'bg-gray-100'">
                                <i class="fas fa-file text-lg" :class="msg.sender_id == currentUserId ? 'text-white/70' : 'text-gray-500'"></i>
                                <div class="flex-1 min-w-0">
                                    <a :href="msg.file_url" target="_blank" class="text-xs font-medium truncate block" :class="msg.sender_id == currentUserId ? 'text-white hover:underline' : 'text-indigo-600 hover:underline'" x-text="msg.file_name || 'File'"></a>
                                    <span class="text-[10px] opacity-60" x-text="formatSize(msg.file_size)"></span>
                                </div>
                                <a :href="msg.file_url" download class="text-xs opacity-60 hover:opacity-100">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </template>

                        <p class="text-sm whitespace-pre-wrap" x-html="linkify(msg.message_text)"></p>
                        <p class="text-xs mt-1" :class="msg.sender_id == currentUserId ? 'text-white/60' : 'text-gray-400'" x-text="formatTime(msg.created_at)"></p>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="previewFile" class="px-4 py-2 border-t border-gray-100 bg-gray-50">
            <div class="flex items-center space-x-2">
                <i class="fas fa-paperclip text-indigo-500"></i>
                <span class="text-sm text-gray-700 truncate flex-1" x-text="previewFile?.name"></span>
                <button @click="previewFile = null" class="text-gray-400 hover:text-red-500"><i class="fas fa-times"></i></button>
            </div>
            <template x-if="previewFile && previewFile.type.startsWith('image/')">
                <img :src="previewUrl" class="mt-2 max-h-32 rounded-lg">
            </template>
        </div>

        <div class="p-4 border-t border-gray-100 bg-white">
            <form @submit.prevent="sendMessage()" class="flex items-center space-x-3">
                <label class="p-2 text-gray-400 hover:text-indigo-600 cursor-pointer transition relative">
                    <i class="fas fa-paperclip text-lg"></i>
                    <input type="file" class="hidden" @change="handleFile($event)" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                </label>
                <label class="p-2 text-gray-400 hover:text-indigo-600 cursor-pointer transition">
                    <i class="fas fa-image text-lg"></i>
                    <input type="file" class="hidden" @change="handleFile($event)" accept="image/*">
                </label>
                <input x-model="newMessage" type="text" placeholder="{{ __('Type your message...') }}" class="flex-1 px-4 py-2.5 bg-gray-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                <button type="submit" :disabled="!newMessage.trim() && !previewFile" class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white hover:from-indigo-600 hover:to-purple-700 transition disabled:opacity-40">
                    <i class="fas fa-paper-plane text-sm"></i>
                </button>
            </form>
        </div>
    </div>

    <div x-show="lightboxUrl" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4" @click.self="lightboxUrl = null">
        <img :src="lightboxUrl" class="max-w-full max-h-full rounded-lg">
        <button @click="lightboxUrl = null" class="absolute top-4 right-4 text-white text-2xl"><i class="fas fa-times"></i></button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function chatApp() {
    return {
        newMessage: '',
        messages: [],
        previewFile: null,
        previewUrl: null,
        lightboxUrl: null,
        currentUserId: {{ Auth::id() }},
        applicationId: {{ $application->id }},

        init() {
            this.loadMessages();
        },

        async loadMessages() {
            try {
                const res = await fetch(`/vacancy-chat/${this.applicationId}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.messages) {
                    this.messages = data.messages;
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (e) {
                console.error(e);
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() && !this.previewFile) return;

            const formData = new FormData();
            formData.append('application_id', this.applicationId);
            if (this.newMessage.trim()) formData.append('message_text', this.newMessage);
            if (this.previewFile) formData.append('file', this.previewFile);

            try {
                const res = await fetch('{{ route("vacancyChat.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });
                const data = await res.json();
                if (data.success) {
                    this.messages.push(data.message);
                    this.newMessage = '';
                    this.previewFile = null;
                    this.previewUrl = null;
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (e) {
                console.error(e);
            }
        },

        handleFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.previewFile = file;
            if (file.type.startsWith('image/')) {
                this.previewUrl = URL.createObjectURL(file);
            }
            event.target.value = '';
        },

        scrollToBottom() {
            const box = this.$refs.chatBox;
            if (box) box.scrollTop = box.scrollHeight;
        },

        formatTime(ts) {
            if (!ts) return '';
            const d = new Date(ts);
            return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        formatSize(bytes) {
            if (!bytes) return '';
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        },

        linkify(text) {
            if (!text) return '';
            var urlRe = /(https?:\/\/[^\s<]+)/g;
            var escaped = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
            return escaped.replace(urlRe, '<a href="$1" target="_blank" rel="noopener" class="underline hover:text-indigo-300">$1</a>');
        },

        openLightbox(url) {
            this.lightboxUrl = url;
        }
    }
}
</script>
@endpush
