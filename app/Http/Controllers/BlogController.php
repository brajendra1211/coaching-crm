<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Course;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::published()
            ->orderByDesc('published_at')
            ->latest()
            ->paginate(9);

        return view('frontend.blogs.index', compact('blogs'));
    }

    public function show(string $slug)
    {
        $blog = Blog::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $blog->increment('views');

        $relatedBlogs = Blog::published()
            ->where('id', '!=', $blog->id)
            ->when($blog->category, function ($query) use ($blog) {
                $query->where('category', $blog->category);
            })
            ->latest()
            ->take(3)
            ->get();

        $courses = Course::where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        return view('frontend.blogs.show', compact('blog', 'relatedBlogs', 'courses'));
    }
}