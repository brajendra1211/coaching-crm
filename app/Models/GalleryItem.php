<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = [
        'type',
        'title',
        'category',
        'image',
        'youtube_url',
        'description',
        'status',
        'sort_order',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getYoutubeIdAttribute(): ?string
    {
        return self::extractYoutubeId($this->youtube_url);
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        return $this->youtube_id
            ? 'https://www.youtube.com/embed/' . $this->youtube_id
            : null;
    }

    public function getYoutubeThumbnailUrlAttribute(): ?string
    {
        return $this->youtube_id
            ? 'https://img.youtube.com/vi/' . $this->youtube_id . '/hqdefault.jpg'
            : null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->type === 'youtube') {
            return $this->youtube_thumbnail_url;
        }

        return $this->image_url;
    }

    public static function extractYoutubeId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $url = trim($url);

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/',
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}