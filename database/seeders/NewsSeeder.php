<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::first();
        }
        if (!$admin) return;

        $items = [
            [
                'title' => 'New Contest: Algorithm Masters 2026',
                'content' => '<p>We are excited to announce the Algorithm Masters 2026 contest! Test your skills against the best developers from around the world.</p><p>The contest starts next Monday and runs for 48 hours. Prizes include premium subscriptions, certificates, and exclusive merch.</p>',
                'image' => 'https://images.unsplash.com/photo-1504639725590-34d0984388bd?w=800',
                'tags' => ['Contest', 'Algorithms', 'Competitive Programming'],
            ],
            [
                'title' => 'Platform Update: Python 3.12 Judge',
                'content' => '<p>We have upgraded our Python judge to support Python 3.12 with the latest features including pattern matching, type parameter syntax, and improved error messages.</p><p>All existing solutions remain fully compatible.</p>',
                'image' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=800',
                'tags' => ['Update', 'Python', 'Judge'],
            ],
            [
                'title' => '50 New Problems Added This Week',
                'content' => '<p>Today we added 50 new algorithmic problems ranging from easy to hard. Topics include Dynamic Programming, Graph Theory, and Advanced Data Structures.</p><p>Happy coding!</p>',
                'image' => 'https://images.unsplash.com/photo-1555949963-ff9fe0c870eb?w=800',
                'tags' => ['Problems', 'Algorithms', 'New'],
            ],
            [
                'title' => 'Community Milestone: 10,000 Users!',
                'content' => '<p>CodeMaster has reached 10,000 registered users! Thank you all for being part of this amazing community. We have big plans for the future including mobile apps and AI-powered tutoring.</p>',
                'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800',
                'tags' => ['Community', 'Milestone'],
            ],
            [
                'title' => 'Interview Prep Series Launch',
                'content' => '<p>Starting next week, we are launching a weekly interview preparation series covering system design, data structures, and behavioral questions from top tech companies like Google, Meta, and Amazon.</p>',
                'image' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=800',
                'tags' => ['Interview', 'Career'],
            ],
        ];

        foreach ($items as $data) {
            $tags = $data['tags'];
            unset($data['tags']);

            $data['user_id'] = $admin->id;
            $data['slug'] = Str::slug($data['title']);
            $data['excerpt'] = Str::limit(strip_tags($data['content']), 160);
            $data['views_count'] = rand(50, 500);

            $slug = $data['slug'];
            $existing = News::where('slug', $slug)->first();
            if ($existing) continue;

            $news = News::create($data);

            foreach ($tags as $tagName) {
                $slug = Str::slug($tagName);
                $tag = Tag::where('slug', $slug)->first()
                    ?? Tag::create(['name' => $tagName, 'slug' => $slug]);
                $news->tags()->attach($tag->id);
            }
        }
    }
}
