<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\SeoLocation;
use App\Models\WebsitePage;

class DynamicPageController extends Controller
{
    public function show(string $slug)
    {
        $slug = trim($slug, '/');

        if ($this->isReservedSlug($slug)) {
            abort(404);
        }

        $courses = Course::where('status', 'active')
            ->latest()
            ->take(9)
            ->get();

        $page = WebsitePage::where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if ($page) {
            return view('frontend.pages.show', compact('page', 'courses'));
        }

        $page = SeoLocation::where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if ($page) {
            return view('frontend.locations.show', compact('page', 'courses'));
        }

        abort(404);
    }

    private function isReservedSlug(string $slug): bool
    {
        return in_array($slug, ['admin', 'courses', 'lead-submit', 'storage'], true);
    }
}