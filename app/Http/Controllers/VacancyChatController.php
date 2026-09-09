<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use App\Models\UserApplication;
use App\Models\VacancyChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VacancyChatController extends Controller
{
    /**
     * Чат доступен соискателю, владельцу вакансии и админу.
     */
    protected function canAccess(UserApplication $application): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        if ($user->id === $application->user_id) return true;
        if ($user->role === 'admin') return true;
        return $application->vacancy && $user->id === $application->vacancy->owner_id;
    }

    public function show(Request $request, $applicationId)
    {
        $application = UserApplication::with(['vacancy', 'user'])
            ->findOrFail($applicationId);

        if (!$this->canAccess($application)) {
            abort(403);
        }

        if ($request->ajax() || $request->wantsJson()) {
            $chats = VacancyChat::where('application_id', $applicationId)
                ->with('sender:id,name,avatar')
                ->orderBy('id')
                ->get()
                ->map(fn($c) => [
                    'id' => $c->id,
                    'sender_id' => $c->sender_id,
                    'message_text' => $c->message_text,
                    'message_type' => $c->message_type,
                    'file_url' => $c->file_url,
                    'file_name' => $c->file_name,
                    'file_type' => $c->file_type,
                    'file_size' => $c->file_size,
                    'sender' => $c->sender ? ['name' => $c->sender->name, 'avatar' => $c->sender->avatar] : null,
                    'created_at' => $c->created_at,
                ]);
            return response()->json(['messages' => $chats]);
        }

        $documents = $application->documents()->latest()->get();

        return view('vacancies.chat', compact('application', 'documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:user_applications,id',
            'message_text' => 'required_without:file|max:5000',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,zip,rar,txt,mp4,mov,webm,m4a,mp3,wav,ogg|max:10240',
        ]);

        $application = UserApplication::with('vacancy')->findOrFail($request->application_id);

        if (!$this->canAccess($application)) {
            abort(403);
        }

        $messageType = 'text';
        $fileUrl = null;
        $fileName = null;
        $fileType = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getMimeType();
            $fileSize = $file->getSize();

            $path = $file->store('chat-files/vacancy-' . $application->id, 'public');
            $fileUrl = Storage::disk('public')->url($path);

            if (str_starts_with($fileType, 'image/')) {
                $messageType = 'image';
            } elseif (str_starts_with($fileType, 'video/')) {
                $messageType = 'video';
            } elseif (str_starts_with($fileType, 'audio/')) {
                $messageType = 'audio';
            } else {
                $messageType = 'file';
            }
        }

        $chat = VacancyChat::create([
            'application_id' => $application->id,
            'sender_id' => Auth::id(),
            'message_text' => $request->message_text ?? '',
            'message_type' => $messageType,
            'file_url' => $fileUrl,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $fileSize,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $chat->id,
                'sender_id' => $chat->sender_id,
                'message_text' => $chat->message_text,
                'message_type' => $chat->message_type,
                'file_url' => $chat->file_url,
                'file_name' => $chat->file_name,
                'file_type' => $chat->file_type,
                'file_size' => $chat->file_size,
                'sender' => [
                    'name' => Auth::user()->name,
                    'avatar' => Auth::user()->avatar,
                ],
                'created_at' => $chat->created_at ?? now(),
            ],
        ]);
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:user_applications,id',
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,zip,rar,txt,mp4,mov,webm,m4a,mp3,wav,ogg|max:10240',
        ]);

        $application = UserApplication::with('vacancy')->findOrFail($request->application_id);

        if (!$this->canAccess($application)) {
            abort(403);
        }

        $file = $request->file('file');
        $path = $file->store('vacancy-docs/' . $application->id, 'public');

        $document = $application->documents()->create([
            'uploader_id' => Auth::id(),
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
        ]);

        return response()->json(['success' => true, 'document' => $document]);
    }
}
