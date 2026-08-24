<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class AdminLessonController extends Controller
{
    public function lessons(Request $request)
    {
        $query = Lesson::with('course');
        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('title', 'like', '%' . $search . '%', 'and');
        }
        $lessons = $query->latest()->paginate(20);
        return view('admin.lessons', compact('lessons'));
    }

    public function createLesson($courseId) { return view('admin.lessons.create', ['course' => Course::findOrFail($courseId)]); }

    public function storeLesson(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,article,quiz',
            'content' => 'nullable|string',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|max:500',
            'audio_url' => 'nullable|string|max:500',
            'presentation_url' => 'nullable|string|max:500',
            'materials_title' => 'nullable|string|max:255',
            'materials_url' => 'nullable|string|max:500',
            'order_num' => 'required|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'module' => 'nullable|string|max:255',
        ]);
        $validated['course_id'] = $course->id;
        Lesson::create($validated);
        return redirect()->route('admin.courses.edit', $course->id)->with('success', 'Lesson created.');
    }

    public function editLesson($id) { return view('admin.lessons.edit', ['lesson' => Lesson::findOrFail($id)]); }

    public function updateLesson(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,article,quiz',
            'content' => 'nullable|string',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|max:500',
            'audio_url' => 'nullable|string|max:500',
            'presentation_url' => 'nullable|string|max:500',
            'materials_title' => 'nullable|string|max:255',
            'materials_url' => 'nullable|string|max:500',
            'order_num' => 'required|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'module' => 'nullable|string|max:255',
        ]);
        $lesson->update($validated);
        return redirect()->route('admin.courses.edit', $lesson->course_id)->with('success', 'Lesson updated.');
    }

    public function deleteLesson($id) { $l = Lesson::findOrFail($id); $cid = $l->course_id; $l->delete(); return redirect()->route('admin.courses.edit', $cid)->with('success', 'Lesson deleted.'); }
}
