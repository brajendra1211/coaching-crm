<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\WebsitePage;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('status', 'active')
            ->latest()
            ->paginate(12);

        $page = WebsitePage::where('status', 'active')
            ->where(function ($query) {
                $query->where('slug', 'courses')
                    ->orWhere('page_type', 'courses');
            })
            ->orderByRaw("CASE WHEN slug = 'courses' THEN 0 ELSE 1 END")
            ->first();

        return view('frontend.courses.index', compact('courses', 'page'));
    }

    public function show(string $slug)
    {
        $course = Course::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $relatedCourses = Course::where('status', 'active')
            ->where('id', '!=', $course->id)
            ->latest()
            ->take(3)
            ->get();

        return view('frontend.courses.show', compact('course', 'relatedCourses'));
    }
}
