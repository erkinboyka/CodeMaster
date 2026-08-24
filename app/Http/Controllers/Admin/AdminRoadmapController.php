<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\RoadmapNode;
use App\Models\RoadmapLesson;
use App\Models\RoadmapQuizQuestion;
use Illuminate\Http\Request;

class AdminRoadmapController extends Controller
{
    public function roadmaps(Request $request)
    {
        $query = RoadmapNode::with('course');
        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('title', 'like', '%' . $search . '%', 'and');
        }
        $roadmaps = $query->latest()->paginate(20);
        return view('admin.roadmaps.index', compact('roadmaps'));
    }

    public function createRoadmap() { return view('admin.roadmaps.create', ['courses' => Course::orderBy('title')->get()]); }

    public function storeRoadmap(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'roadmap_title' => 'nullable|string|max:255',
            'topic' => 'nullable|string|max:255',
            'materials' => 'nullable|string',
            'x' => 'nullable|numeric',
            'y' => 'nullable|numeric',
            'deps' => 'nullable|string',
            'is_exam' => 'nullable|boolean',
        ]);
        $validated['is_exam'] = $request->boolean('is_exam');
        if (!empty($validated['materials'])) $validated['materials'] = json_decode($validated['materials'], true) ?? [];
        if (!empty($validated['deps'])) $validated['deps'] = json_decode($validated['deps'], true) ?? [];
        RoadmapNode::create($validated);
        return redirect()->route('admin.roadmaps')->with('success', 'Roadmap node created.');
    }

    public function editRoadmap($id) { return view('admin.roadmaps.edit', ['roadmap' => RoadmapNode::findOrFail($id), 'courses' => Course::orderBy('title')->get()]); }

    public function updateRoadmap(Request $request, $id)
    {
        $roadmap = RoadmapNode::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'roadmap_title' => 'nullable|string|max:255',
            'topic' => 'nullable|string|max:255',
            'materials' => 'nullable|string',
            'x' => 'nullable|numeric',
            'y' => 'nullable|numeric',
            'deps' => 'nullable|string',
            'is_exam' => 'nullable|boolean',
        ]);
        $validated['is_exam'] = $request->boolean('is_exam');
        if (!empty($validated['materials'])) $validated['materials'] = json_decode($validated['materials'], true) ?? [];
        if (!empty($validated['deps'])) $validated['deps'] = json_decode($validated['deps'], true) ?? [];
        $roadmap->update($validated);
        return redirect()->route('admin.roadmaps')->with('success', 'Roadmap node updated.');
    }

    public function deleteRoadmap($id) { RoadmapNode::findOrFail($id)->delete(); return back()->with('success', 'Roadmap node deleted.'); }

    // ── Roadmap Lessons ──
    public function roadmapLessons($nodeId)
    {
        $node = RoadmapNode::findOrFail($nodeId);
        $lessons = $node->roadmapLessons()->orderBy('order_index')->get();
        return view('admin.roadmaps.lessons', compact('node', 'lessons'));
    }

    public function createRoadmapLesson($nodeId) { return view('admin.roadmaps.lesson_form', ['node' => RoadmapNode::findOrFail($nodeId), 'lesson' => null]); }

    public function storeRoadmapLesson(Request $request, $nodeId)
    {
        $node = RoadmapNode::findOrFail($nodeId);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'materials' => 'nullable|string',
            'order_index' => 'nullable|integer|min:0',
        ]);
        $validated['node_id'] = $node->id;
        if (!empty($validated['materials'])) $validated['materials'] = json_decode($validated['materials'], true) ?? [];
        RoadmapLesson::create($validated);
        return redirect()->route('admin.roadmap.lessons', $node->id)->with('success', 'Roadmap lesson created.');
    }

    public function editRoadmapLesson($id) { $l = RoadmapLesson::findOrFail($id); return view('admin.roadmaps.lesson_form', ['node' => $l->node, 'lesson' => $l]); }

    public function updateRoadmapLesson(Request $request, $id)
    {
        $lesson = RoadmapLesson::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'materials' => 'nullable|string',
            'order_index' => 'nullable|integer|min:0',
        ]);
        if (!empty($validated['materials'])) $validated['materials'] = json_decode($validated['materials'], true) ?? [];
        $lesson->update($validated);
        return redirect()->route('admin.roadmap.lessons', $lesson->node_id)->with('success', 'Roadmap lesson updated.');
    }

    public function deleteRoadmapLesson($id) { $l = RoadmapLesson::findOrFail($id); $nid = $l->node_id; $l->delete(); return redirect()->route('admin.roadmap.lessons', $nid)->with('success', 'Deleted.'); }

    // ── Roadmap Quiz Questions ──
    public function roadmapQuizzes($nodeId)
    {
        $node = RoadmapNode::findOrFail($nodeId);
        $quizzes = $node->quizQuestions;
        return view('admin.roadmaps.quizzes', compact('node', 'quizzes'));
    }

    public function createRoadmapQuiz($nodeId) { return view('admin.roadmaps.quiz_form', ['node' => RoadmapNode::findOrFail($nodeId), 'quiz' => null]); }

    public function storeRoadmapQuiz(Request $request, $nodeId)
    {
        $node = RoadmapNode::findOrFail($nodeId);
        $validated = $request->validate([
            'question' => 'required|string',
            'options' => 'required|string',
            'correct_answer' => 'required|string|max:255',
        ]);
        $validated['node_id'] = $node->id;
        $validated['options'] = json_decode($validated['options'], true) ?? [];
        RoadmapQuizQuestion::create($validated);
        return redirect()->route('admin.roadmap.quizzes', $node->id)->with('success', 'Quiz question created.');
    }

    public function editRoadmapQuiz($id) { $q = RoadmapQuizQuestion::findOrFail($id); return view('admin.roadmaps.quiz_form', ['node' => $q->node, 'quiz' => $q]); }

    public function updateRoadmapQuiz(Request $request, $id)
    {
        $quiz = RoadmapQuizQuestion::findOrFail($id);
        $validated = $request->validate([
            'question' => 'required|string',
            'options' => 'required|string',
            'correct_answer' => 'required|string|max:255',
        ]);
        $validated['options'] = json_decode($validated['options'], true) ?? [];
        $quiz->update($validated);
        return redirect()->route('admin.roadmap.quizzes', $quiz->node_id)->with('success', 'Quiz question updated.');
    }

    public function deleteRoadmapQuiz($id) { $q = RoadmapQuizQuestion::findOrFail($id); $nid = $q->node_id; $q->delete(); return redirect()->route('admin.roadmap.quizzes', $nid)->with('success', 'Deleted.'); }
}
