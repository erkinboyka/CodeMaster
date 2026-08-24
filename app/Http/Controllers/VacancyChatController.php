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
    public function show(Request $request, $applicationId)
    {
        $application = UserApplication::with(['vacancy', 'user'])
            ->findOrFail($applicationId);

        if (Auth::id() !== $application->user_id && !Auth::user()->is_admin) {
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
            'file' => 'nullable|file|max:10240',
        ]);

        $application = UserApplication::findOrFail($request->application_id);

        if (Auth::id() !== $application->user_id && !Auth::user()->is_admin) {
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
            'file' => 'required|file|max:10240',
            'description' => 'nullable|string|max:500',
        ]);

        $application = UserApplication::findOrFail($request->application_id);

        if (Auth::id() !== $application->user_id && !Auth::user()->is_admin) {
            abort(403);
        }

        $file = $request->file('file');
        $path = $file->store('vacancy-docs/' . $application->id, 'public');

        $document = $application->documents()->create([
            'user_id' => Auth::id(),
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'document' => $document]);
    }
}
