<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'author_name',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
        'sort_order',
        'views',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'views' => 'integer',
        'sort_order' => 'integer',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image
            ? asset('storage/' . $this->featured_image)
            : null;
    }
}