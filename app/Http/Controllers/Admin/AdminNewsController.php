<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminNewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::with('user', 'tags')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $news = $query->paginate(20)->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        $tags = Tag::orderBy('name')->get();

        return view('admin.news.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|string|max:500',
            'is_published' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_published'] = $request->boolean('is_published', true);

        if (empty($validated['excerpt'])) {
            $validated['excerpt'] = Str::limit(strip_tags($validated['content']), 160);
        }

        $tagsData = $validated['tags'] ?? [];
        unset($validated['tags']);

        $news = News::create($validated);

        if (!empty($tagsData)) {
            $tagIds = [];
            foreach ($tagsData as $tagName) {
                $tagName = trim($tagName);
                if ($tagName === '') continue;
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['slug' => Str::slug($tagName)]
                );
                $tagIds[] = $tag->id;
            }
            $news->tags()->sync($tagIds);
        }

        return redirect()->route('admin.news.index')->with('success', 'News created successfully.');
    }

    public function edit(News $news)
    {
        $news->load('tags');
        $tags = Tag::orderBy('name')->get();
        $newsTags = $news->tags->pluck('name')->toArray();

        return view('admin.news.edit', compact('news', 'tags', 'newsTags'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|string|max:500',
            'is_published' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_published'] = $request->boolean('is_published', true);

        if (empty($validated['excerpt'])) {
            $validated['excerpt'] = Str::limit(strip_tags($validated['content']), 160);
        }

        $tagsData = $validated['tags'] ?? [];
        unset($validated['tags']);

        $news->update($validated);

        $tagIds = [];
        foreach ($tagsData as $tagName) {
            $tagName = trim($tagName);
            if ($tagName === '') continue;
            $tag = Tag::firstOrCreate(
                ['name' => $tagName],
                ['slug' => Str::slug($tagName)]
            );
            $tagIds[] = $tag->id;
        }
        $news->tags()->sync($tagIds);

        return redirect()->route('admin.news.index')->with('success', 'News updated successfully.');
    }

    public function destroy(News $news)
    {
        $news->tags()->detach();
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'News deleted successfully.');
    }
}
