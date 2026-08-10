<?php

namespace App\Http\Controllers;

use App\Models\RoadmapNode;
use App\Models\RoadmapUserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoadmapController extends Controller
{
    public function index()
    {
        $roadmaps = RoadmapNode::select('roadmap_title')
            ->distinct()
            ->pluck('roadmap_title');
        return view('roadmaps.index', compact('roadmaps'));
    }

    public function show($title)
    {
        $nodes = RoadmapNode::with('course', 'roadmapLessons', 'quizQuestions')
            ->where('roadmap_title', $title)
            ->get()
            ->sortBy(fn($n) => [$n->y, $n->x])
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
        $prevRoadmap = $currentIndex > 0 ? $roadmapTitles[$currentIndex - 1] : null;
        $nextRoadmap = $currentIndex < count($roadmapTitles) - 1 ? $roadmapTitles[$currentIndex + 1] : null;

        $roadmap = (object) ['id' => $title, 'title' => $title, 'nodes' => $nodes];

        return view('roadmaps.show', compact('roadmap', 'completedNodeIds', 'totalNodes', 'percent', 'quizData', 'lessonsData', 'prevRoadmap', 'nextRoadmap'));
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
}
