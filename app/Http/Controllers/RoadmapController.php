<?php

namespace App\Http\Controllers;

use App\Models\Roadmap;
use App\Models\RoadmapNode;
use App\Models\RoadmapSection;
use App\Models\RoadmapUserProgress;
use App\Models\StudentCourse;
use App\Models\StepStudent;
use App\Models\CourseStep;
use App\Jobs\GenerateRoadmapJob;
use App\Jobs\GenerateCourseStepsJob;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RoadmapController extends Controller
{
    public function index()
    {
        $roadmaps = Roadmap::with(['owner', 'sections'])
            ->where('is_published', true)
            ->withCount('courses')
            ->latest()
            ->get();

        $legacyRoadmaps = RoadmapNode::select('roadmap_title')
            ->distinct()
            ->pluck('roadmap_title');

        return view('roadmaps.index', compact('roadmaps', 'legacyRoadmaps'));
    }

    public function create()
    {
        return view('roadmaps.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'sometimes|in:frontend,backend,design,devops,other',
            'difficulty' => 'sometimes|in:beginner,intermediate,advanced',
        ]);

        $user = Auth::user();

        $roadmap = Roadmap::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'description' => '',
            'category' => $validated['category'] ?? 'other',
            'difficulty' => $validated['difficulty'] ?? 'beginner',
            'is_published' => false,
            'ai_generated' => true,
        ]);

        GenerateRoadmapJob::dispatch($roadmap->id, $user->id);

        return redirect()->route('roadmaps.generating', $roadmap->slug);
    }

    public function generating($slug)
    {
        $roadmap = Roadmap::where('slug', $slug)->firstOrFail();
        return view('roadmaps.generating', compact('roadmap'));
    }

    public function status($slug)
    {
        $roadmap = Roadmap::where('slug', $slug)->firstOrFail();
        return response()->json([
            'is_published' => $roadmap->is_published,
            'has_error' => !$roadmap->is_published && $roadmap->description === 'generation_error',
            'total_sections' => $roadmap->total_sections,
            'total_courses' => $roadmap->total_courses,
        ]);
    }

    public function generateCourseSteps($courseId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        if ($course->user_id !== $user->id) {
            abort(403);
        }

        if ($course->steps()->count() > 0) {
            return response()->json(['status' => 'already_has_steps', 'steps' => $course->steps()->count()]);
        }

        GenerateCourseStepsJob::dispatch($courseId, $user->id);

        return response()->json(['status' => 'generating']);
    }

    public function courseStepsStatus($courseId)
    {
        $course = Course::findOrFail($courseId);
        $stepsCount = $course->steps()->count();

        return response()->json([
            'steps_count' => $stepsCount,
            'has_steps' => $stepsCount > 0,
            'generation_status' => $course->generation_status,
        ]);
    }

    public function show($slug)
    {
        $roadmap = Roadmap::where('slug', $slug)->first();

        if (!$roadmap) {
            $title = $slug;
            return $this->showLegacy($title);
        }

        $user = Auth::user();

        $courseId = $roadmap->courses()->pluck('courses.id')->first();
        $course = $courseId ? Course::with(['steps' => function ($q) {
            $q->orderBy('sort_order');
        }])->find($courseId) : null;

        $enrolled = false;
        $completedSteps = [];
        $totalSteps = 0;
        $completedCount = 0;
        $percent = 0;

        if ($course) {
            $enrolled = \App\Models\StudentCourse::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->exists();

            $completedSteps = StepStudent::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('is_completed', true)
                ->pluck('step_id')
                ->toArray();

            $totalSteps = $course->steps()->count();
            $completedCount = count(array_intersect($completedSteps, $course->steps->pluck('id')->toArray()));
            $percent = $totalSteps > 0 ? round(($completedCount / $totalSteps) * 100) : 0;
        }

        $parentSteps = $course ? $course->steps->where('type', 'parent')->sortBy('sort_order') : collect();
        $heirSteps = $course ? $course->steps->where('type', 'heir')->groupBy('parent_id') : collect();

        return view('roadmaps.show', compact(
            'roadmap', 'course', 'enrolled', 'completedSteps',
            'totalSteps', 'completedCount', 'percent',
            'parentSteps', 'heirSteps'
        ));
    }

    protected function showLegacy($title)
    {
        $nodes = RoadmapNode::with('course', 'roadmapLessons', 'quizQuestions')
            ->where('roadmap_title', $title)
            ->orderBy('y')
            ->orderBy('x')
            ->get()
            ->values();

        $completedNodeIds = RoadmapUserProgress::where('user_id', Auth::id())
            ->whereIn('node_id', $nodes->pluck('id'))
            ->pluck('node_id')
            ->toArray();

        $totalNodes = $nodes->count();
        $completedCount = count($completedNodeIds);
        $percent = $totalNodes > 0 ? round(($completedCount / $totalNodes) * 100) : 0;

        $quizData = [];
        $lessonsData = [];
        foreach ($nodes as $n) {
            foreach ($n->quizQuestions as $q) {
                $quizData[] = [
                    'node_id' => $n->id,
                    'question' => $q->question,
                    'options' => $q->options,
                    'correct_answer' => $q->correct_answer,
                ];
            }
            foreach ($n->roadmapLessons as $l) {
                $lessonsData[] = [
                    'node_id' => $n->id,
                    'title' => $l->title,
                    'materials' => $l->materials,
                    'description' => $l->description,
                ];
            }
        }

        $roadmapTitles = RoadmapNode::distinct()->pluck('roadmap_title')->filter()->values()->all();
        sort($roadmapTitles);
        $currentIndex = array_search($title, $roadmapTitles);
        $prevRoadmap = ($currentIndex !== false && $currentIndex > 0) ? $roadmapTitles[$currentIndex - 1] : null;
        $nextRoadmap = ($currentIndex !== false && $currentIndex < count($roadmapTitles) - 1) ? $roadmapTitles[$currentIndex + 1] : null;

        $roadmap = (object) ['id' => $title, 'title' => $title, 'nodes' => $nodes, 'is_legacy' => true];

        return view('roadmaps.show', compact(
            'roadmap', 'completedNodeIds', 'totalNodes', 'percent',
            'quizData', 'lessonsData', 'prevRoadmap', 'nextRoadmap'
        ));
    }

    public function completeNode(Request $request)
    {
        $validated = $request->validate([
            'node_id' => 'required|exists:roadmap_nodes,id',
        ]);

        RoadmapUserProgress::firstOrCreate([
            'user_id' => Auth::id(),
            'node_id' => $validated['node_id'],
        ]);

        $node = RoadmapNode::find($validated['node_id']);
        $nodes = RoadmapNode::where('roadmap_title', $node->roadmap_title)->get();
        $totalNodes = $nodes->count();

        $completedCount = RoadmapUserProgress::where('user_id', Auth::id())
            ->whereIn('node_id', $nodes->pluck('id'))
            ->count();

        $percent = $totalNodes > 0 ? round(($completedCount / $totalNodes) * 100) : 0;

        return response()->json([
            'success' => true,
            'percent' => $percent,
            'completed' => $percent >= 100,
        ]);
    }

    public function enroll(Request $request)
    {
        $validated = $request->validate([
            'roadmap_id' => 'required|exists:roadmaps,id',
        ]);

        $user = Auth::user();
        $roadmap = Roadmap::findOrFail($validated['roadmap_id']);

        foreach ($roadmap->courses as $course) {
            $exists = StudentCourse::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->exists();

            if (!$exists) {
                StudentCourse::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ]);
                $course->increment('students_count');
            }
        }

        return back()->with('success', 'Вы записаны на все курсы roadmap!');
    }
}
