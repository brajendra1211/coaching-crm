<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\SeoLocation;

class LocationPageController extends Controller
{
    public function show(string $slug)
    {
        $page = SeoLocation::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $courses = Course::where('status', 'active')
            ->latest()
            ->take(9)
            ->get();

        return view('frontend.locations.show', compact('page', 'courses'));
    }
}