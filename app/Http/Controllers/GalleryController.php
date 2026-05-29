<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = GalleryItem::active()
            ->orderBy('sort_order', 'asc')
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $items = $query->paginate(12)->withQueryString();

        $categories = GalleryItem::active()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $featuredItems = GalleryItem::active()
            ->where('is_featured', true)
            ->orderBy('sort_order', 'asc')
            ->latest()
            ->take(6)
            ->get();

        return view('frontend.gallery.index', compact('items', 'categories', 'featuredItems'));
    }
}