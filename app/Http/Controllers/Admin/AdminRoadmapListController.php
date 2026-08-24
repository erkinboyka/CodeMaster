<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoadmapList;
use Illuminate\Http\Request;

class AdminRoadmapListController extends Controller
{
    public function index()
    {
        $roadmaps = RoadmapList::orderBy('title')->paginate(20);
        return view('admin.roadmap_list.index', compact('roadmaps'));
    }

    public function create() { return view('admin.roadmap_list.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:roadmap_list,title',
            'description' => 'nullable|string',
        ]);
        RoadmapList::create($validated);
        return redirect()->route('admin.roadmap-list')->with('success', 'Roadmap created.');
    }

    public function edit($id) { return view('admin.roadmap_list.edit', ['roadmap' => RoadmapList::findOrFail($id)]); }

    public function update(Request $request, $id)
    {
        $roadmap = RoadmapList::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:roadmap_list,title,' . $id,
            'description' => 'nullable|string',
        ]);
        $roadmap->update($validated);
        return redirect()->route('admin.roadmap-list')->with('success', 'Roadmap updated.');
    }

    public function destroy($id)
    {
        RoadmapList::findOrFail($id)->delete();
        return back()->with('success', 'Roadmap deleted.');
    }
}
