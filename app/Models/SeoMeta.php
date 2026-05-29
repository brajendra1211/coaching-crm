<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    protected $fillable = [
        'page_name',
        'path',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'robots',
        'schema_json',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return $this->og_image ? asset('storage/' . $this->og_image) : null;
    }

    public function getTwitterImageUrlAttribute(): ?string
    {
        return $this->twitter_image ? asset('storage/' . $this->twitter_image) : null;
    }

    public static function normalizePath(string $path): string
    {
        $path = trim($path);

        if ($path === '' || $path === '/') {
            return '/';
        }

        $path = parse_url($path, PHP_URL_PATH) ?: $path;

        return '/' . trim($path, '/');
    }

    public static function current(): ?self
    {
        $path = self::normalizePath(request()->path() === '/' ? '/' : request()->path());

        return self::active()
            ->where('path', $path)
            ->first();
    }
}