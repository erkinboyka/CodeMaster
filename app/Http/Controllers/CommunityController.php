<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use App\Models\CommunityComment;
use App\Models\CommunityPostLike;
use App\Models\Tag;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'newest');
        $tagSlug = $request->get('tag');
        $problemId = $request->get('problem');

        $query = CommunityPost::with(['user', 'comments.user', 'tags'])
            ->withCount('comments');

        if ($problemId) {
            $query->where('problem_id', $problemId);
        }

        if ($tagSlug) {
            $query->whereHas('tags', function ($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        if ($sort === 'likes') {
            $query->orderByDesc('likes_count');
        } else {
            $query->orderByDesc('created_at');
        }

        $posts = $query->paginate(20)->withQueryString();

        $popularTags = Tag::orderByDesc('posts_count')->take(10)->get();

        $activeTag = $tagSlug ? Tag::where('slug', $tagSlug)->first() : null;

        $latestNews = News::published()
            ->with('user', 'tags')
            ->latest()
            ->take(5)
            ->get();

        return view('community.index', compact('posts', 'sort', 'popularTags', 'activeTag', 'latestNews'));
    }

    public function show($id)
    {
        $post = CommunityPost::with(['user', 'comments.user', 'tags'])
            ->withCount('comments')
            ->findOrFail($id);

        $viewedKey = 'post_viewed_' . $post->id;
        if (!session()->has($viewedKey)) {
            $post->increment('views_count');
            session()->put($viewedKey, true);
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'post' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'likes_count' => $post->likes_count,
                    'views_count' => $post->views_count,
                    'created_at' => $post->created_at->toISOString(),
                    'user' => ['id' => $post->user->id, 'name' => $post->user->name, 'avatar' => $post->user->avatar],
                    'is_owner' => Auth::id() === $post->user_id,
                    'liked' => Auth::check() ? $post->isLikedBy(Auth::id()) : false,
                    'tags' => $post->tags->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug]),
                    'comments' => $post->comments->map(fn($c) => [
                        'id' => $c->id,
                        'content' => $c->content,
                        'created_at' => $c->created_at->toISOString(),
                        'user' => ['id' => $c->user->id, 'name' => $c->user->name, 'avatar' => $c->user->avatar],
                    ]),
                ],
            ]);
        }

        return view('community.show', compact('post'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
            'problem_id' => 'nullable|integer|exists:problems,id',
            'tags' => 'nullable|array|max:5',
            'tags.*' => 'string|max:50',
        ]);

        $post = CommunityPost::create([
            'user_id' => Auth::id(),
            'problem_id' => $validated['problem_id'] ?? null,
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        $this->syncTags($post, $validated['tags'] ?? []);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'post_id' => $post->id]);
        }

        return redirect()->route('community.index');
    }

    public function update(Request $request, $id)
    {
        $post = CommunityPost::findOrFail($id);

        if ($post->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
            'tags' => 'nullable|array|max:5',
            'tags.*' => 'string|max:50',
        ]);

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        $this->syncTags($post, $validated['tags'] ?? []);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('community.index');
    }

    public function destroy(Request $request, $id)
    {
        $post = CommunityPost::findOrFail($id);

        if ($post->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        DB::transaction(function () use ($post) {
            $post->comments()->delete();
            $post->likes()->delete();
            foreach ($post->tags as $tag) {
                $tag->decrement('posts_count');
            }
            $post->tags()->detach();
            $post->delete();
        });

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('community.index');
    }

    public function comment(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:community_posts,id',
            'content' => 'required|string|max:5000',
        ]);

        $comment = CommunityComment::create([
            'post_id' => $validated['post_id'],
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        CommunityPost::where('id', $validated['post_id'])->increment('comments_count');

        $comment->load('user');

        if ($request->wantsJson()) {
            $post = CommunityPost::findOrFail($validated['post_id']);
            return response()->json(['success' => true, 'comment' => $comment, 'comments_count' => $post->fresh()->comments_count]);
        }

        return redirect()->route('community.show', $validated['post_id']);
    }

    public function like($id)
    {
        $post = CommunityPost::findOrFail($id);

        $liked = DB::transaction(function () use ($id, $post) {
            $existing = CommunityPostLike::where('post_id', $id)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $existing->delete();
                $post->decrement('likes_count');
                return false;
            } else {
                CommunityPostLike::create([
                    'post_id' => $id,
                    'user_id' => Auth::id(),
                ]);
                $post->increment('likes_count');
                return true;
            }
        });

        return response()->json([
            'success' => true,
            'likes' => $post->fresh()->likes_count,
            'liked' => $liked,
        ]);
    }

    public function tags()
    {
        $tags = Tag::orderByDesc('posts_count')->take(30)->get();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'tags' => $tags]);
        }

        return view('community.tags', compact('tags'));
    }

    private function syncTags(CommunityPost $post, array $tagNames): void
    {
        $tagIds = [];
        foreach ($tagNames as $name) {
            $name = trim($name);
            if ($name === '') continue;

            $slug = Str::slug($name);
            $tag = Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
            $tagIds[] = $tag->id;
        }

        $oldTagIds = $post->tags()->pluck('tags.id')->toArray();
        $post->tags()->sync($tagIds);

        foreach (array_diff($oldTagIds, $tagIds) as $removedId) {
            Tag::where('id', $removedId)->decrement('posts_count');
        }
        foreach ($tagIds as $addedId) {
            Tag::where('id', $addedId)->increment('posts_count');
        }
    }
}
