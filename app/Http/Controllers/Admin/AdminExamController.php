<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseExam;
use Illuminate\Http\Request;

class AdminExamController extends Controller
{
    public function exams(Request $request)
    {
        $query = CourseExam::with('course');
        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->whereHas('course', fn($q) => $q->where('title', 'like', '%' . $search . '%', 'and'));
        }
        $exams = $query->latest()->paginate(20);
        return view('admin.exams.index', compact('exams'));
    }

    public function createExam() { return view('admin.exams.create', ['courses' => Course::orderBy('title')->get()]); }

    public function storeExam(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'time_limit_minutes' => 'required|integer|min:1',
            'pass_percent' => 'required|integer|min:1|max:100',
            'questions_per_exam' => 'required|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
            'exam_json' => 'nullable|string',
            'question_bank_json' => 'nullable|string',
        ]);
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_options'] = $request->boolean('shuffle_options');
        if (!empty($validated['exam_json'])) $validated['exam_json'] = json_decode($validated['exam_json'], true) ?? [];
        if (!empty($validated['question_bank_json'])) $validated['question_bank_json'] = json_decode($validated['question_bank_json'], true) ?? [];
        CourseExam::create($validated);
        return redirect()->route('admin.exams')->with('success', 'Exam created.');
    }

    public function editExam($id) { return view('admin.exams.edit', ['exam' => CourseExam::findOrFail($id), 'courses' => Course::orderBy('title')->get()]); }

    public function updateExam(Request $request, $id)
    {
        $exam = CourseExam::findOrFail($id);
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'time_limit_minutes' => 'required|integer|min:1',
            'pass_percent' => 'required|integer|min:1|max:100',
            'questions_per_exam' => 'required|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
            'exam_json' => 'nullable|string',
            'question_bank_json' => 'nullable|string',
        ]);
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_options'] = $request->boolean('shuffle_options');
        if (!empty($validated['exam_json'])) $validated['exam_json'] = json_decode($validated['exam_json'], true) ?? [];
        if (!empty($validated['question_bank_json'])) $validated['question_bank_json'] = json_decode($validated['question_bank_json'], true) ?? [];
        $exam->update($validated);
        return redirect()->route('admin.exams')->with('success', 'Exam updated.');
    }

    public function deleteExam($id) { CourseExam::findOrFail($id)->delete(); return back()->with('success', 'Exam deleted.'); }
}
