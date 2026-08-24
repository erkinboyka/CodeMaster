<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class AdminCourseController extends Controller
{
    public function courses(Request $request)
    {
        $query = Course::withCount('lessons');
        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('title', 'like', '%' . $search . '%', 'and');
        }
        $courses = $query->latest()->paginate(20);
        return view('admin.courses', compact('courses'));
    }

    public function createCourse() { return view('admin.courses.create'); }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:frontend,backend,design,devops,other',
            'level' => 'required|in:Начальный,Средний,Продвинутый',
            'image_url' => 'nullable|string|max:500',
            'materials_title' => 'nullable|string|max:255',
            'materials_url' => 'nullable|string|max:500',
        ]);
        Course::create($validated);
        return redirect()->route('admin.courses')->with('success', 'Course created.');
    }

    public function editCourse($id)
    {
        $course = Course::with(['lessons' => function ($q) {
            $q->orderBy('order_num');
        }, 'lessons.lessonQuizzes', 'lessons.practiceTasks', 'exams'])->findOrFail($id);
        return view('admin.courses.edit', ['course' => $course]);
    }

    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:frontend,backend,design,devops,other',
            'level' => 'required|in:Начальный,Средний,Продвинутый',
            'image_url' => 'nullable|string|max:500',
            'materials_title' => 'nullable|string|max:255',
            'materials_url' => 'nullable|string|max:500',
        ]);
        $course->update($validated);
        return redirect()->route('admin.courses')->with('success', 'Course updated.');
    }

    public function deleteCourse($id) { Course::findOrFail($id)->delete(); return back()->with('success', 'Course deleted.'); }
}
