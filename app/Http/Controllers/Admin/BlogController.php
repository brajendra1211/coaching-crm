<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::query()
            ->orderBy('sort_order', 'asc')
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('slug', 'like', '%' . $request->search . '%')
                    ->orWhere('category', 'like', '%' . $request->search . '%')
                    ->orWhere('author_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $blogs = $query->paginate(15)->withQueryString();

        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        $blog = new Blog([
            'status' => 'draft',
            'published_at' => now(),
            'sort_order' => 0,
        ]);

        return view('admin.blogs.create', compact('blog'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $slug = $this->prepareSlug(
            $request->filled('slug') ? $request->slug : $request->title
        );

        if ($this->isReservedSlug($slug)) {
            return back()
                ->withInput()
                ->withErrors([
                    'slug' => 'This slug is reserved. Please use another URL slug.',
                ]);
        }

        $data['slug'] = $this->generateUniqueSlug($slug);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request
                ->file('featured_image')
                ->store('blogs', 'public');
        }

        if (empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        Blog::create($data);

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog created successfully.');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $this->validatedData($request);

        $slug = $this->prepareSlug(
            $request->filled('slug') ? $request->slug : $request->title
        );

        if ($this->isReservedSlug($slug)) {
            return back()
                ->withInput()
                ->withErrors([
                    'slug' => 'This slug is reserved. Please use another URL slug.',
                ]);
        }

        $data['slug'] = $this->generateUniqueSlug($slug, $blog->id);

        $data['featured_image'] = $blog->featured_image;

        if ($request->has('remove_featured_image')) {
            $this->deleteImage($blog->featured_image);
            $data['featured_image'] = null;
        }

        if ($request->hasFile('featured_image')) {
            $this->deleteImage($blog->featured_image);

            $data['featured_image'] = $request
                ->file('featured_image')
                ->store('blogs', 'public');
        }

        $blog->update($data);

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        $this->deleteImage($blog->featured_image);

        $blog->delete();

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],

            'category' => ['nullable', 'string', 'max:120'],
            'author_name' => ['nullable', 'string', 'max:120'],

            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],

            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'status' => ['required', 'in:draft,active,inactive'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'seo_keywords' => ['nullable', 'string'],
        ]);
    }

    private function prepareSlug(string $value): string
    {
        $slug = Str::slug($value);

        return $slug !== '' ? $slug : 'blog';
    }

    private function isReservedSlug(string $slug): bool
    {
        return in_array($slug, [
            'admin',
            'courses',
            'blogs',
            'lead-submit',
            'storage',
        ], true);
    }

    private function generateUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $baseSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        return Blog::where('slug', $slug)
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists();
    }

    private function deleteImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}