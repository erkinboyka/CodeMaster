<?php

namespace App\Http\Controllers;

use App\Models\UserApplication;
use App\Models\VacancyChat;
use App\Models\VacancyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VacancyChatController extends Controller
{
    public function show($applicationId)
    {
        $application = UserApplication::with(['vacancy', 'user'])
            ->findOrFail($applicationId);

        if ($application->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $messages = VacancyChat::where('application_id', $applicationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        $documents = VacancyDocument::where('application_id', $applicationId)
            ->with('uploader')
            ->latest()
            ->get();

        return view('vacancies.chat', compact('application', 'messages', 'documents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:user_applications,id',
            'message' => 'required|string|max:5000',
        ]);

        $application = UserApplication::findOrFail($validated['application_id']);

        if ($application->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        VacancyChat::create([
            'application_id' => $validated['application_id'],
            'sender_id' => Auth::id(),
            'message_text' => $validated['message'],
        ]);

        return redirect()->route('vacancyChat.show', $validated['application_id']);
    }

    public function uploadDocument(Request $request)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:user_applications,id',
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,txt,png,jpg,jpeg',
        ]);

        $application = UserApplication::findOrFail($validated['application_id']);

        if ($application->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $file = $request->file('file');
        $path = $file->store('chat-documents', 'public');

        VacancyDocument::create([
            'application_id' => $validated['application_id'],
            'uploader_id' => Auth::id(),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
        ]);

        return redirect()->route('vacancyChat.show', $validated['application_id']);
    }
}
