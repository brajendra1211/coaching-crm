<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeoMetaController extends Controller
{
    public function index(Request $request)
    {
        $query = SeoMeta::query()
            ->orderBy('sort_order', 'asc')
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('page_name', 'like', '%' . $request->search . '%')
                    ->orWhere('path', 'like', '%' . $request->search . '%')
                    ->orWhere('meta_title', 'like', '%' . $request->search . '%')
                    ->orWhere('meta_description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $seoMetas = $query->paginate(15)->withQueryString();

        return view('admin.seo.index', compact('seoMetas'));
    }

    public function create()
    {
        $seoMeta = new SeoMeta([
            'path' => '/',
            'robots' => 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
            'status' => 'active',
            'sort_order' => 0,
        ]);

        return view('admin.seo.create', compact('seoMeta'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $data['path'] = SeoMeta::normalizePath($data['path']);

        if (SeoMeta::where('path', $data['path'])->exists()) {
            return back()
                ->withInput()
                ->withErrors(['path' => 'SEO meta for this path already exists. Please edit existing record.']);
        }

        if ($request->hasFile('og_image')) {
            $data['og_image'] = $request->file('og_image')->store('seo/og-images', 'public');
        }

        if ($request->hasFile('twitter_image')) {
            $data['twitter_image'] = $request->file('twitter_image')->store('seo/twitter-images', 'public');
        }

        SeoMeta::create($data);

        return redirect()
            ->route('admin.seo.index')
            ->with('success', 'SEO meta created successfully.');
    }

    public function edit(SeoMeta $seoMeta)
    {
        return view('admin.seo.edit', compact('seoMeta'));
    }

    public function update(Request $request, SeoMeta $seoMeta)
    {
        $data = $this->validatedData($request);

        $data['path'] = SeoMeta::normalizePath($data['path']);

        $exists = SeoMeta::where('path', $data['path'])
            ->where('id', '!=', $seoMeta->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['path' => 'SEO meta for this path already exists. Please use another path.']);
        }

        $data['og_image'] = $seoMeta->og_image;
        $data['twitter_image'] = $seoMeta->twitter_image;

        if ($request->has('remove_og_image')) {
            $this->deleteImage($seoMeta->og_image);
            $data['og_image'] = null;
        }

        if ($request->has('remove_twitter_image')) {
            $this->deleteImage($seoMeta->twitter_image);
            $data['twitter_image'] = null;
        }

        if ($request->hasFile('og_image')) {
            $this->deleteImage($seoMeta->og_image);
            $data['og_image'] = $request->file('og_image')->store('seo/og-images', 'public');
        }

        if ($request->hasFile('twitter_image')) {
            $this->deleteImage($seoMeta->twitter_image);
            $data['twitter_image'] = $request->file('twitter_image')->store('seo/twitter-images', 'public');
        }

        $seoMeta->update($data);

        return redirect()
            ->route('admin.seo.index')
            ->with('success', 'SEO meta updated successfully.');
    }

    public function destroy(SeoMeta $seoMeta)
    {
        $this->deleteImage($seoMeta->og_image);
        $this->deleteImage($seoMeta->twitter_image);

        $seoMeta->delete();

        return redirect()
            ->route('admin.seo.index')
            ->with('success', 'SEO meta deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'page_name' => ['nullable', 'string', 'max:255'],
            'path' => ['required', 'string', 'max:255'],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],

            'canonical_url' => ['nullable', 'string', 'max:500'],

            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string'],
            'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'twitter_title' => ['nullable', 'string', 'max:255'],
            'twitter_description' => ['nullable', 'string'],
            'twitter_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'robots' => ['required', 'string', 'max:255'],
            'schema_json' => ['nullable', 'string'],

            'status' => ['required', 'in:active,inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}