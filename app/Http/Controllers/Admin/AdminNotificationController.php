<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function notifications(Request $request)
    {
        $query = Notification::with('user')->orderBy('notification_time', 'desc');
        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('message', 'like', '%' . $search . '%', 'and');
        }
        $notifications = $query->paginate(20);
        return view('admin.notifications', compact('notifications'));
    }

    public function createNotification() { return view('admin.notifications_create'); }

    public function storeNotification(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        if (!empty($validated['user_id'])) {
            Notification::create([
                'user_id' => $validated['user_id'],
                'message' => $validated['message'],
                'notification_time' => now(),
            ]);
        } else {
            $now = now();
            $userIds = User::pluck('id')->toArray();
            $chunks = array_chunk($userIds, 500);
            foreach ($chunks as $chunk) {
                $records = array_map(fn($uid) => [
                    'user_id' => $uid,
                    'message' => $validated['message'],
                    'notification_time' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $chunk);
                Notification::insert($records);
            }
        }

        return redirect()->route('admin.notifications')->with('success', 'Notification sent.');
    }

    public function deleteNotification($id) { Notification::findOrFail($id)->delete(); return back()->with('success', 'Deleted.'); }
}
