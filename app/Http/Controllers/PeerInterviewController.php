<?php

namespace App\Http\Controllers;

use App\Models\PeerInterviewRoom;
use App\Models\PeerTask;
use App\Models\PeerMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PeerInterviewController extends Controller
{
    public function index()
    {
        $rooms = PeerInterviewRoom::where('host_id', Auth::id())
            ->orWhere('guest_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('interview.peer-index', compact('rooms'));
    }

    public function create()
    {
        $code = PeerInterviewRoom::generateCode();

        $room = PeerInterviewRoom::create([
            'room_code' => $code,
            'host_id' => Auth::id(),
            'host_name' => Auth::user()->name,
            'status' => 'waiting',
            'started_at' => now(),
        ]);

        return redirect()->route('peer.room', $code);
    }

    public function joinForm()
    {
        return view('interview.peer-join');
    }

    public function join(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_code' => 'required|string|size:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $code = strtoupper($request->room_code);

        return DB::transaction(function () use ($code) {
            $room = PeerInterviewRoom::where('room_code', $code)
                ->where('status', 'waiting')
                ->lockForUpdate()
                ->first();

            if (!$room) {
                return redirect()->back()->withInput()->with('error', 'Комната не найдена или уже занята.');
            }

            if ($room->host_id === Auth::id()) {
                return redirect()->route('peer.room', $code);
            }

            $room->update([
                'guest_id' => Auth::id(),
                'guest_name' => Auth::user()->name,
                'status' => 'connected',
            ]);

            return redirect()->route('peer.room', $code);
        });
    }

    public function room($code)
    {
        $room = PeerInterviewRoom::where('room_code', $code)->firstOrFail();

        if (!$room->isParticipant(Auth::id())) {
            abort(403);
        }

        $isHost = $room->isHost(Auth::id());
        $peerName = $isHost ? $room->guest_name : $room->host_name;
        $peerConnected = $room->guest_id !== null;

        $tasks = $room->tasks()->ordered()->get();
        $messages = $room->messages()->with('user')->latest()->limit(100)->get()->reverse();

        return view('interview.peer-room', compact('room', 'isHost', 'peerName', 'peerConnected', 'tasks', 'messages'));
    }

    // ─── TASK CRUD ──────────────────────────────────────────

    public function addTask(Request $request, $code)
    {
        $room = PeerInterviewRoom::where('room_code', $code)->firstOrFail();

        if (!$room->isHost(Auth::id())) {
            return response()->json(['error' => 'Только ведущий может добавлять задачи'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'type' => 'required|in:code,theory,system_design',
            'difficulty' => 'required|in:easy,medium,hard',
            'starter_code' => 'nullable|string',
            'language' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $maxOrder = $room->tasks()->max('sort_order') ?? 0;

        $task = $room->tasks()->create([
            'title' => $request->title,
            'description' => $request->description ?? '',
            'type' => $request->type,
            'difficulty' => $request->difficulty,
            'starter_code' => $request->starter_code ?? '',
            'language' => $request->language ?? 'python',
            'status' => 'active',
            'sort_order' => $maxOrder + 1,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['ok' => true, 'task' => $task]);
    }

    public function updateTask(Request $request, $code, $taskId)
    {
        $room = PeerInterviewRoom::where('room_code', $code)->firstOrFail();

        if (!$room->isParticipant(Auth::id())) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $task = $room->tasks()->where('id', $taskId)->first();

        if (!$task) {
            return response()->json(['error' => 'Задача не найдена'], 404);
        }

        $isHost = $room->isHost(Auth::id());

        if ($isHost) {
            $allowed = ['status', 'score', 'feedback'];
            foreach ($allowed as $field) {
                if ($request->has($field)) {
                    $task->$field = $request->$field;
                }
            }
            $task->save();
        } else {
            if ($request->has('status')) {
                $allowedStatuses = ['skipped'];
                $newStatus = $request->status;
                if (in_array($newStatus, $allowedStatuses)) {
                    $task->status = $newStatus;
                    $task->save();
                }
            } elseif ($request->has('solution')) {
                $task->solution = $request->solution;
                if ($task->status === 'active') {
                    $task->status = 'in_progress';
                }
                $task->save();
            }
        }

        return response()->json(['ok' => true, 'task' => $task]);
    }

    public function deleteTask($code, $taskId)
    {
        $room = PeerInterviewRoom::where('room_code', $code)->firstOrFail();

        if (!$room->isHost(Auth::id())) {
            return response()->json(['error' => 'Только ведущий может удалять задачи'], 403);
        }

        $task = $room->tasks()->where('id', $taskId)->first();

        if (!$task) {
            return response()->json(['error' => 'Задача не найдена'], 404);
        }

        $task->delete();

        return response()->json(['ok' => true, 'deleted_id' => $taskId]);
    }

    public function startTask($code, $taskId)
    {
        $room = PeerInterviewRoom::where('room_code', $code)->firstOrFail();

        if (!$room->isParticipant(Auth::id())) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $task = $room->tasks()->where('id', $taskId)->first();

        if (!$task) {
            return response()->json(['error' => 'Задача не найдена'], 404);
        }

        if ($task->status !== 'active') {
            return response()->json(['error' => 'Задача не в статусе active'], 422);
        }

        $task->update(['status' => 'in_progress']);

        return response()->json(['ok' => true, 'task' => $task]);
    }

    public function submitTask(Request $request, $code, $taskId)
    {
        $room = PeerInterviewRoom::where('room_code', $code)->firstOrFail();

        if (!$room->isParticipant(Auth::id())) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $task = $room->tasks()->where('id', $taskId)->first();

        if (!$task) {
            return response()->json(['error' => 'Задача не найдена'], 404);
        }

        $validator = Validator::make($request->all(), [
            'solution' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $allowedStatuses = ['in_progress', 'active'];
        if (!in_array($task->status, $allowedStatuses)) {
            return response()->json(['error' => 'Задача не может быть отправлена в текущем статусе'], 422);
        }

        $task->update([
            'solution' => $request->solution,
            'status' => 'done',
        ]);

        return response()->json(['ok' => true, 'task' => $task]);
    }

    public function reviewTask(Request $request, $code, $taskId)
    {
        $room = PeerInterviewRoom::where('room_code', $code)->firstOrFail();

        if (!$room->isHost(Auth::id())) {
            return response()->json(['error' => 'Только ведущий может оценивать'], 403);
        }

        $task = $room->tasks()->where('id', $taskId)->first();

        if (!$task) {
            return response()->json(['error' => 'Задача не найдена'], 404);
        }

        $validator = Validator::make($request->all(), [
            'score' => 'required|integer|between:0,10',
            'feedback' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $task->update([
            'score' => $request->score,
            'feedback' => $request->feedback ?? '',
            'status' => 'review',
        ]);

        return response()->json(['ok' => true, 'task' => $task]);
    }

    public function reorderTasks(Request $request, $code)
    {
        $room = PeerInterviewRoom::where('room_code', $code)->firstOrFail();

        if (!$room->isHost(Auth::id())) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        foreach ($request->order as $index => $taskId) {
            $room->tasks()->where('id', $taskId)->update(['sort_order' => $index]);
        }

        return response()->json(['ok' => true]);
    }

    // ─── CODE ───────────────────────────────────────────────

    public function updateCode(Request $request, $code)
    {
        $room = PeerInterviewRoom::where('room_code', $code)->firstOrFail();

        if (!$room->isParticipant(Auth::id())) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'nullable|string',
            'language' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $update = [];
        if ($request->has('code')) $update['code_content'] = $request->code;
        if ($request->has('language')) $update['code_language'] = $request->language;

        $room->update($update);

        return response()->json(['ok' => true]);
    }

    // ─── CHAT ───────────────────────────────────────────────

    public function sendMessage(Request $request, $code)
    {
        $room = PeerInterviewRoom::where('room_code', $code)->firstOrFail();

        if (!$room->isParticipant(Auth::id())) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'text' => 'nullable|string|max:2000',
            'file' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if (!$request->has('text') && !$request->hasFile('file')) {
            return response()->json(['error' => 'Message text or file is required'], 422);
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

            $path = $file->store('chat-files/peer-' . $room->id, 'public');
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

        $message = $room->messages()->create([
            'user_id' => Auth::id(),
            'text' => $request->text ?? '',
            'message_type' => $messageType,
            'file_url' => $fileUrl,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'created_at' => now(),
        ]);

        $message->load('user');

        return response()->json(['ok' => true, 'message' => $message]);
    }

    // ─── SIGNALING ──────────────────────────────────────────

    public function signaling(Request $request, $code)
    {
        $room = PeerInterviewRoom::where('room_code', $code)->firstOrFail();

        if (!$room->isParticipant(Auth::id())) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $isHost = $room->isHost(Auth::id());

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:sdp,ice,chat,end,tasks,code',
                'data' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $type = $request->type;
            $data = $request->data;

            if ($type === 'sdp') {
                $field = $isHost ? 'host_sdp' : 'guest_sdp';
                $room->update([$field => $data]);
            } elseif ($type === 'ice') {
                $field = $isHost ? 'host_ice' : 'guest_ice';
                $existing = $isHost ? $room->host_ice : $room->guest_ice;
                $iceList = is_array($existing) ? $existing : [];
                $iceList[] = $data;
                $room->update([$field => $iceList]);
            } elseif ($type === 'end') {
                $this->endInterview($room);
            } elseif ($type === 'chat') {
                // Legacy: ignore old client-side chat signals
            }

            return response()->json(['ok' => true]);
        }

        // GET polling
        $pollType = $request->query('type', 'all');
        $afterId = (int) $request->query('after_id', 0);

        $response = [
            'status' => $room->status,
            'guest_connected' => $room->guest_id !== null,
        ];

        if ($pollType === 'sdp' || $pollType === 'all') {
            if ($isHost) {
                $response['guest_sdp'] = $room->guest_sdp;
            } else {
                $response['host_sdp'] = $room->host_sdp;
            }
        }

        if ($pollType === 'ice' || $pollType === 'all') {
            if ($isHost) {
                $response['guest_ice'] = $room->guest_ice;
            } else {
                $response['host_ice'] = $room->host_ice;
            }
        }

        if ($pollType === 'tasks' || $pollType === 'all') {
            $response['tasks'] = $room->tasks()->ordered()->get();
        }

        if ($pollType === 'code' || $pollType === 'all') {
            $response['code_content'] = $room->code_content;
            $response['code_language'] = $room->code_language;
        }

        if ($pollType === 'messages' || $pollType === 'all') {
            $msgQuery = $room->messages()->with('user');
            if ($afterId > 0) {
                $msgQuery->where('id', '>', $afterId);
            }
            $response['messages'] = $msgQuery->latest()->limit(50)->get()->reverse()->values();
        }

        if ($pollType === 'summary' || $pollType === 'all') {
            $response['total_score'] = $room->total_score;
            $response['max_score'] = $room->max_score;
            $response['summary'] = $room->summary;
        }

        return response()->json($response);
    }

    // ─── LEAVE / END ────────────────────────────────────────

    public function leave($code)
    {
        $room = PeerInterviewRoom::where('room_code', $code)->firstOrFail();

        if (!$room->isParticipant(Auth::id())) {
            abort(403);
        }

        $this->endInterview($room);

        return redirect()->route('peer.index')->with('success', 'Собеседование завершено.');
    }

    private function endInterview(PeerInterviewRoom $room)
    {
        $totalScore = $room->tasks()->whereNotNull('score')->sum('score');
        $maxScore = $room->tasks()->count() * 10;

        $room->update([
            'status' => 'ended',
            'ended_at' => now(),
            'total_score' => $totalScore,
            'max_score' => $maxScore > 0 ? $maxScore : null,
        ]);
    }
}
