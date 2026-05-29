<?php

namespace App\Http\Controllers;

use App\Models\Course;

class FrontendController extends Controller
{
    public function home()
    {
        $featuredCourses = Course::where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        $totalCourses = Course::where('status', 'active')->count();

        return view('frontend.home', compact('featuredCourses', 'totalCourses'));
    }
}