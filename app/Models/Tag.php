<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['name', 'slug', 'posts_count'];

    protected $casts = [
        'posts_count' => 'integer',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(CommunityPost::class, 'community_post_tag', 'tag_id', 'post_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (!$tag->slug) {
                $tag->slug = \Str::slug($tag->name);
            }
        });
    }
}
