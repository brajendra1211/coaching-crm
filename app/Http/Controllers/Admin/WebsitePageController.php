<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoLocation;
use App\Models\WebsitePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsitePageController extends Controller
{
    public function index(Request $request)
    {
        $query = WebsitePage::query()
            ->orderBy('sort_order', 'asc')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('page_type')) {
            $query->where('page_type', $request->page_type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('slug', 'like', '%' . $request->search . '%')
                    ->orWhere('hero_title', 'like', '%' . $request->search . '%');
            });
        }

        $pages = $query->paginate(15)->withQueryString();

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'page_type' => ['required', 'string', 'max:100'],

            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string'],
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'content' => ['nullable', 'string'],
            'cta_title' => ['nullable', 'string', 'max:255'],
            'cta_description' => ['nullable', 'string'],

            'sort_order' => ['nullable', 'integer', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'seo_keywords' => ['nullable', 'string'],
        ]);

        $slug = $this->prepareSlug(
            $request->filled('slug') ? $request->slug : $request->title
        );

        if ($this->isReservedSlug($slug, $request->page_type)) {
            return back()
                ->withInput()
                ->withErrors([
                    'slug' => 'This slug is reserved. Please use another URL slug.',
                ]);
        }

        $slug = $this->generateUniqueSlug($slug);

        $heroImagePath = null;

        if ($request->hasFile('hero_image')) {
            $heroImagePath = $request->file('hero_image')->store('website-pages/hero-images', 'public');
        }

        WebsitePage::create([
            'title' => $request->title,
            'slug' => $slug,
            'page_type' => $request->page_type ?: 'default',

            'hero_title' => $request->hero_title,
            'hero_subtitle' => $request->hero_subtitle,
            'hero_image' => $heroImagePath,

            'content' => $request->content,
            'cta_title' => $request->cta_title,
            'cta_description' => $request->cta_description,

            'show_enquiry_form' => $request->has('show_enquiry_form'),
            'status' => $request->has('status') ? 'active' : 'inactive',
            'sort_order' => $request->sort_order ?? 0,

            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
        ]);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Website page created successfully.');
    }

    public function edit(WebsitePage $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, WebsitePage $page)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'page_type' => ['required', 'string', 'max:100'],

            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string'],
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_hero_image' => ['nullable'],

            'content' => ['nullable', 'string'],
            'cta_title' => ['nullable', 'string', 'max:255'],
            'cta_description' => ['nullable', 'string'],

            'sort_order' => ['nullable', 'integer', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'seo_keywords' => ['nullable', 'string'],
        ]);

        $slug = $this->prepareSlug(
            $request->filled('slug') ? $request->slug : $request->title
        );

        if ($this->isReservedSlug($slug, $request->page_type)) {
            return back()
                ->withInput()
                ->withErrors([
                    'slug' => 'This slug is reserved. Please use another URL slug.',
                ]);
        }

        $slug = $this->generateUniqueSlug($slug, $page->id);

        $heroImagePath = $page->hero_image;

        if ($request->has('remove_hero_image')) {
            $this->deleteFileFromPublicDisk($page->hero_image);
            $heroImagePath = null;
        }

        if ($request->hasFile('hero_image')) {
            $this->deleteFileFromPublicDisk($page->hero_image);

            $heroImagePath = $request->file('hero_image')->store('website-pages/hero-images', 'public');
        }

        $page->update([
            'title' => $request->title,
            'slug' => $slug,
            'page_type' => $request->page_type ?: 'default',

            'hero_title' => $request->hero_title,
            'hero_subtitle' => $request->hero_subtitle,
            'hero_image' => $heroImagePath,

            'content' => $request->content,
            'cta_title' => $request->cta_title,
            'cta_description' => $request->cta_description,

            'show_enquiry_form' => $request->has('show_enquiry_form'),
            'status' => $request->has('status') ? 'active' : 'inactive',
            'sort_order' => $request->sort_order ?? 0,

            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
        ]);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Website page updated successfully.');
    }

    public function destroy(WebsitePage $page)
    {
        $this->deleteFileFromPublicDisk($page->hero_image);

        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Website page deleted successfully.');
    }

    private function prepareSlug(string $value): string
    {
        $slug = Str::slug($value);

        return $slug !== '' ? $slug : 'page';
    }

    private function isReservedSlug(string $slug, ?string $pageType = null): bool
    {
        if ($slug === 'courses' && $pageType === 'courses') {
            return false;
        }

        return in_array($slug, [
            'admin',
            'courses',
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
        $existsInPages = WebsitePage::where('slug', $slug)
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists();

        if ($existsInPages) {
            return true;
        }

        return SeoLocation::where('slug', $slug)->exists();
    }

    private function deleteFileFromPublicDisk(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
