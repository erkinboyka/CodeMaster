<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class News extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'excerpt', 'content',
        'image', 'is_published', 'views_count',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'views_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (News $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
            if (empty($model->excerpt) && !empty($model->content)) {
                $model->excerpt = Str::limit(strip_tags($model->content), 160);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'news_tag', 'news_id', 'tag_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
