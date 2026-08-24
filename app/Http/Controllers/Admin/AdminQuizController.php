<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonQuiz;
use Illuminate\Http\Request;

class AdminQuizController extends Controller
{
    public function quizzes(Request $request)
    {
        $query = LessonQuiz::with('lesson.course');
        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('question_text', 'like', '%' . $search . '%', 'and');
        }
        $quizzes = $query->latest()->paginate(20);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function createQuiz($lessonId) { return view('admin.quizzes.create', ['lesson' => Lesson::findOrFail($lessonId)]); }

    public function storeQuiz(Request $request, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $validated = $request->validate([
            'question_text' => 'required|string',
            'options_json' => 'required|string',
            'correct_option' => 'required|integer|min:0',
            'explanation' => 'nullable|string',
            'order_num' => 'nullable|integer|min:0',
        ]);
        $validated['lesson_id'] = $lesson->id;
        $validated['options_json'] = json_decode($validated['options_json'], true) ?? [];
        LessonQuiz::create($validated);
        return redirect()->route('admin.courses.edit', $lesson->course_id)->with('success', 'Quiz created.');
    }

    public function editQuiz($id) { return view('admin.quizzes.edit', ['quiz' => LessonQuiz::findOrFail($id)]); }

    public function updateQuiz(Request $request, $id)
    {
        $quiz = LessonQuiz::findOrFail($id);
        $validated = $request->validate([
            'question_text' => 'required|string',
            'options_json' => 'required|string',
            'correct_option' => 'required|integer|min:0',
            'explanation' => 'nullable|string',
            'order_num' => 'nullable|integer|min:0',
        ]);
        $validated['options_json'] = json_decode($validated['options_json'], true) ?? [];
        $quiz->update($validated);
        return redirect()->route('admin.courses.edit', $quiz->lesson->course_id)->with('success', 'Quiz updated.');
    }

    public function deleteQuiz($id) { $q = LessonQuiz::findOrFail($id); $cid = $q->lesson->course_id; $q->delete(); return redirect()->route('admin.courses.edit', $cid)->with('success', 'Quiz deleted.'); }
}
