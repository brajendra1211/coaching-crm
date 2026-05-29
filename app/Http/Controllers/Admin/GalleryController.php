<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = GalleryItem::query()
            ->orderBy('sort_order', 'asc')
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('category', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $items = $query->paginate(15)->withQueryString();

        $categories = GalleryItem::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.gallery.index', compact('items', 'categories'));
    }

    public function create()
    {
        $galleryItem = new GalleryItem([
            'type' => 'image',
            'status' => 'active',
            'sort_order' => 0,
        ]);

        return view('admin.gallery.create', compact('galleryItem'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if ($request->type === 'image') {
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('gallery', 'public');
            }

            $data['youtube_url'] = null;
        }

        if ($request->type === 'youtube') {
            $data['image'] = null;
        }

        $data['is_featured'] = $request->has('is_featured');

        GalleryItem::create($data);

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Gallery item created successfully.');
    }

    public function edit(GalleryItem $galleryItem)
    {
        return view('admin.gallery.edit', compact('galleryItem'));
    }

    public function update(Request $request, GalleryItem $galleryItem)
    {
        $data = $this->validatedData($request, $galleryItem);

        $data['is_featured'] = $request->has('is_featured');

        if ($request->type === 'image') {
            $data['youtube_url'] = null;
            $data['image'] = $galleryItem->image;

            if ($request->has('remove_image')) {
                $this->deleteImage($galleryItem->image);
                $data['image'] = null;
            }

            if ($request->hasFile('image')) {
                $this->deleteImage($galleryItem->image);
                $data['image'] = $request->file('image')->store('gallery', 'public');
            }
        }

        if ($request->type === 'youtube') {
            $this->deleteImage($galleryItem->image);
            $data['image'] = null;
        }

        $galleryItem->update($data);

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Gallery item updated successfully.');
    }

    public function destroy(GalleryItem $galleryItem)
    {
        $this->deleteImage($galleryItem->image);

        $galleryItem->delete();

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Gallery item deleted successfully.');
    }

    private function validatedData(Request $request, ?GalleryItem $galleryItem = null): array
    {
        $rules = [
            'type' => ['required', 'in:image,youtube'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'youtube_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];

        if ($request->type === 'image' && !$galleryItem?->image) {
            $rules['image'] = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];
        }

        if ($request->type === 'youtube') {
            $rules['youtube_url'] = ['required', 'string', 'max:500'];
        }

        $data = $request->validate($rules);

        if ($request->type === 'youtube') {
            $youtubeId = GalleryItem::extractYoutubeId($request->youtube_url);

            if (!$youtubeId) {
                back()
                    ->withInput()
                    ->withErrors(['youtube_url' => 'Please enter a valid YouTube video URL.'])
                    ->throwResponse();
            }
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}