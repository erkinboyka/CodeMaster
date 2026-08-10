@extends('layouts.app')

@section('title', __('Chat') . ' - CodeMaster')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="chatApp()">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('vacancies.show', 1) }}" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
                    <span class="text-sm font-bold text-indigo-600">TC</span>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900">TechCorp - Recruitment</h2>
                    <div class="flex items-center space-x-1">
                        <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                        <span class="text-xs text-gray-500">{{ __('Online') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <span class="px-3 py-1 bg-yellow-50 text-yellow-600 text-xs font-medium rounded-full" x-data="{ badge: 'pending' }">
                <i class="fas fa-clock mr-1"></i>
                <span x-text="badge === 'pending' ? '{{ __('Pending Review') }}' : badge === 'accepted' ? '{{ __('Accepted') }}' : '{{ __('Rejected') }}'"></span>
            </span>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="h-[500px] overflow-y-auto p-6 space-y-4 bg-gray-50" id="chat-messages">
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.sender === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.sender === 'user' ? 'bg-indigo-600 text-white rounded-2xl rounded-br-md' : 'bg-white text-gray-800 rounded-2xl rounded-bl-md shadow-sm'" class="px-4 py-3 max-w-[70%]">
                        <p class="text-sm" x-text="msg.text"></p>
                        <p class="text-xs mt-1" :class="msg.sender === 'user' ? 'text-white/60' : 'text-gray-400'" x-text="msg.time"></p>
                        <template x-if="msg.file">
                            <div class="mt-2 p-2 bg-white/10 rounded-lg flex items-center space-x-2">
                                <i class="fas fa-file-alt"></i>
                                <span class="text-xs" x-text="msg.file"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <div class="p-4 border-t border-gray-100 bg-white">
            <form @submit.prevent="sendMessage()" class="flex items-center space-x-3">
                <label class="p-2 text-gray-400 hover:text-indigo-600 cursor-pointer transition">
                    <i class="fas fa-paperclip text-lg"></i>
                    <input type="file" class="hidden" @change="handleFile($event)">
                </label>
                <input x-model="newMessage" type="text" placeholder="{{ __('Type your message...') }}" class="flex-1 px-4 py-2.5 bg-gray-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                <button type="submit" class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white hover:from-indigo-600 hover:to-purple-700 transition">
                    <i class="fas fa-paper-plane text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function chatApp() {
    return {
        newMessage: '',
        messages: [
            { sender: 'company', text: 'Hello! Thank you for applying for the Senior Frontend Developer position. We reviewed your profile and would like to schedule an interview.', time: '10:30 AM', file: null },
            { sender: 'user', text: 'Thank you! I am very interested in the position. When would be a good time for the interview?', time: '10:35 AM', file: null },
            { sender: 'company', text: 'How about tomorrow at 3:00 PM? We will send you a video call link.', time: '10:40 AM', file: null },
            { sender: 'user', text: 'That works for me! Here is my updated resume.', time: '10:45 AM', file: 'resume_updated.pdf' },
            { sender: 'company', text: 'Great! We have received your resume. See you tomorrow at 3:00 PM. Good luck!', time: '10:50 AM', file: null },
        ],
        sendMessage() {
            if (this.newMessage.trim()) {
                this.messages.push({ sender: 'user', text: this.newMessage, time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }), file: null });
                this.newMessage = '';
                this.$nextTick(() => { document.getElementById('chat-messages').scrollTop = document.getElementById('chat-messages').scrollHeight; });
            }
        },
        handleFile(event) {
            const file = event.target.files[0];
            if (file) {
                this.messages.push({ sender: 'user', text: 'Attached file:', time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }), file: file.name });
            }
        }
    }
}
</script>
@endpush
