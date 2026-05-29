<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Course;
use App\Models\GalleryItem;
use App\Models\SeoLocation;
use App\Models\SeoMeta;
use App\Models\Testimonial;
use App\Models\WebsitePage;
use Illuminate\Http\Response;

class SeoToolController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect();

        $urls->push([
            'loc' => url('/'),
            'lastmod' => now()->toAtomString(),
            'priority' => '1.0',
            'changefreq' => 'daily',
        ]);

        $urls->push([
            'loc' => route('courses.index'),
            'lastmod' => now()->toAtomString(),
            'priority' => '0.9',
            'changefreq' => 'weekly',
        ]);

        if (class_exists(Blog::class)) {
            $urls->push([
                'loc' => route('blogs.index'),
                'lastmod' => now()->toAtomString(),
                'priority' => '0.8',
                'changefreq' => 'weekly',
            ]);
        }

        if (class_exists(GalleryItem::class)) {
            $urls->push([
                'loc' => route('gallery.index'),
                'lastmod' => now()->toAtomString(),
                'priority' => '0.7',
                'changefreq' => 'monthly',
            ]);
        }

        if (class_exists(Testimonial::class)) {
            $urls->push([
                'loc' => route('testimonials.index'),
                'lastmod' => now()->toAtomString(),
                'priority' => '0.7',
                'changefreq' => 'monthly',
            ]);
        }

        Course::where('status', 'active')->get()->each(function ($course) use ($urls) {
            $urls->push([
                'loc' => route('courses.show', $course->slug),
                'lastmod' => optional($course->updated_at)->toAtomString() ?: now()->toAtomString(),
                'priority' => '0.8',
                'changefreq' => 'weekly',
            ]);
        });

        if (class_exists(Blog::class)) {
            Blog::published()->get()->each(function ($blog) use ($urls) {
                $urls->push([
                    'loc' => route('blogs.show', $blog->slug),
                    'lastmod' => optional($blog->updated_at)->toAtomString() ?: now()->toAtomString(),
                    'priority' => '0.7',
                    'changefreq' => 'weekly',
                ]);
            });
        }

        WebsitePage::where('status', 'active')->get()->each(function ($page) use ($urls) {
            $urls->push([
                'loc' => url('/' . $page->slug),
                'lastmod' => optional($page->updated_at)->toAtomString() ?: now()->toAtomString(),
                'priority' => '0.8',
                'changefreq' => 'monthly',
            ]);
        });

        SeoLocation::where('status', 'active')->get()->each(function ($location) use ($urls) {
            $urls->push([
                'loc' => url('/' . $location->slug),
                'lastmod' => optional($location->updated_at)->toAtomString() ?: now()->toAtomString(),
                'priority' => '0.8',
                'changefreq' => 'monthly',
            ]);
        });

        SeoMeta::active()->get()->each(function ($meta) use ($urls) {
            $path = $meta->path === '/' ? '/' : '/' . trim($meta->path, '/');

            $urls->push([
                'loc' => url($path),
                'lastmod' => optional($meta->updated_at)->toAtomString() ?: now()->toAtomString(),
                'priority' => '0.6',
                'changefreq' => 'monthly',
            ]);
        });

        $urls = $urls->unique('loc')->values();

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /admin/login\n";
        $content .= "\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";

        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}