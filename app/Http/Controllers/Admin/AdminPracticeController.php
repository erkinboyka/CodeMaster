<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonPracticeTask;
use Illuminate\Http\Request;

class AdminPracticeController extends Controller
{
    public function practices(Request $request)
    {
        $query = LessonPracticeTask::with('lesson.course');
        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('title', 'like', '%' . $search . '%', 'and');
        }
        $practices = $query->latest()->paginate(20);
        return view('admin.practices.index', compact('practices'));
    }

    public function createPractice($lessonId) { return view('admin.practices.create', ['lesson' => Lesson::findOrFail($lessonId)]); }

    public function storePractice(Request $request, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'language' => 'required|string|max:50',
            'prompt' => 'nullable|string',
            'starter_code' => 'nullable|string',
            'tests_json' => 'nullable|string',
            'expected_output' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'hints' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'test_runner_json' => 'nullable|string',
            'is_required' => 'nullable|boolean',
        ]);
        $validated['lesson_id'] = $lesson->id;
        $validated['is_required'] = $request->boolean('is_required');
        if (!empty($validated['tests_json'])) $validated['tests_json'] = json_decode($validated['tests_json'], true) ?? [];
        if (!empty($validated['test_runner_json'])) $validated['test_runner_json'] = json_decode($validated['test_runner_json'], true) ?? [];
        LessonPracticeTask::create($validated);
        return redirect()->route('admin.courses.edit', $lesson->course_id)->with('success', 'Practice task created.');
    }

    public function editPractice($id) { return view('admin.practices.edit', ['practice' => LessonPracticeTask::findOrFail($id)]); }

    public function updatePractice(Request $request, $id)
    {
        $practice = LessonPracticeTask::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'language' => 'required|string|max:50',
            'prompt' => 'nullable|string',
            'starter_code' => 'nullable|string',
            'tests_json' => 'nullable|string',
            'expected_output' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'hints' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'test_runner_json' => 'nullable|string',
            'is_required' => 'nullable|boolean',
        ]);
        $validated['is_required'] = $request->boolean('is_required');
        if (!empty($validated['tests_json'])) $validated['tests_json'] = json_decode($validated['tests_json'], true) ?? [];
        if (!empty($validated['test_runner_json'])) $validated['test_runner_json'] = json_decode($validated['test_runner_json'], true) ?? [];
        $practice->update($validated);
        return redirect()->route('admin.courses.edit', $practice->lesson->course_id)->with('success', 'Practice task updated.');
    }

    public function deletePractice($id) { $p = LessonPracticeTask::findOrFail($id); $cid = $p->lesson->course_id; $p->delete(); return redirect()->route('admin.courses.edit', $cid)->with('success', 'Practice task deleted.'); }
}
