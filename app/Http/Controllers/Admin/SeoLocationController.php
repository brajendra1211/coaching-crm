<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoLocation;
use App\Models\WebsitePage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeoLocationController extends Controller
{
    public function index()
    {
        $locations = SeoLocation::orderBy('sort_order')
            ->latest()
            ->paginate(15);

        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_name' => ['required', 'string', 'max:255'],
            'focus_keyword' => ['nullable', 'string', 'max:255'],
            'page_title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'cta_title' => ['nullable', 'string', 'max:255'],
            'cta_description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'seo_keywords' => ['nullable', 'string'],
        ]);

        $data = $request->only([
            'location_name',
            'focus_keyword',
            'page_title',
            'short_description',
            'content',
            'cta_title',
            'cta_description',
            'sort_order',
            'seo_title',
            'seo_description',
            'seo_keywords',
        ]);

        $slug = $this->prepareSlug(
            $request->filled('slug') ? $request->slug : $request->page_title
        );

        if ($this->isReservedSlug($slug)) {
            return back()
                ->withInput()
                ->withErrors([
                    'slug' => 'This slug is reserved. Please use another URL slug.',
                ]);
        }

        $data['slug'] = $this->generateUniqueSlug($slug);
        $data['status'] = $request->has('status') ? 'active' : 'inactive';
        $data['sort_order'] = $request->sort_order ?? 0;

        SeoLocation::create($data);

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'SEO location page created successfully.');
    }

    public function edit(SeoLocation $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, SeoLocation $location)
    {
        $request->validate([
            'location_name' => ['required', 'string', 'max:255'],
            'focus_keyword' => ['nullable', 'string', 'max:255'],
            'page_title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'cta_title' => ['nullable', 'string', 'max:255'],
            'cta_description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'seo_keywords' => ['nullable', 'string'],
        ]);

        $data = $request->only([
            'location_name',
            'focus_keyword',
            'page_title',
            'short_description',
            'content',
            'cta_title',
            'cta_description',
            'sort_order',
            'seo_title',
            'seo_description',
            'seo_keywords',
        ]);

        $slug = $this->prepareSlug(
            $request->filled('slug') ? $request->slug : $request->page_title
        );

        if ($this->isReservedSlug($slug)) {
            return back()
                ->withInput()
                ->withErrors([
                    'slug' => 'This slug is reserved. Please use another URL slug.',
                ]);
        }

        $data['slug'] = $this->generateUniqueSlug($slug, $location->id);
        $data['status'] = $request->has('status') ? 'active' : 'inactive';
        $data['sort_order'] = $request->sort_order ?? 0;

        $location->update($data);

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'SEO location page updated successfully.');
    }

    public function destroy(SeoLocation $location)
    {
        $location->delete();

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'SEO location page deleted successfully.');
    }

    private function prepareSlug(string $value): string
    {
        $slug = Str::slug($value);

        return $slug !== '' ? $slug : 'location';
    }

    private function isReservedSlug(string $slug): bool
    {
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
        $existsInLocations = SeoLocation::where('slug', $slug)
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists();

        if ($existsInLocations) {
            return true;
        }

        return WebsitePage::where('slug', $slug)->exists();
    }
}