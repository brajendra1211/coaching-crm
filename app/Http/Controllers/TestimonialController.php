<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = Testimonial::active()
            ->orderBy('sort_order', 'asc')
            ->latest();

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('course_name', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%')
                    ->orWhere('review', 'like', '%' . $request->search . '%');
            });
        }

        $testimonials = $query->paginate(12)->withQueryString();

        $featuredTestimonials = Testimonial::active()
            ->where('is_featured', true)
            ->orderBy('sort_order', 'asc')
            ->latest()
            ->take(6)
            ->get();

        $averageRating = round(Testimonial::active()->avg('rating') ?: 5, 1);
        $totalTestimonials = Testimonial::active()->count();

        return view('frontend.testimonials.index', compact(
            'testimonials',
            'featuredTestimonials',
            'averageRating',
            'totalTestimonials'
        ));
    }
}