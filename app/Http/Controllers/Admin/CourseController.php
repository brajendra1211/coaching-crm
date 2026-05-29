<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::latest()->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:courses,slug'],
            'course_type' => ['nullable', 'string', 'max:255'],
            'class_level' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'offer_fee' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'subjects' => ['nullable', 'string'],
            'eligibility' => ['nullable', 'string'],
            'features' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'seo_keywords' => ['nullable', 'string'],
        ]);

        $data = $request->only([
            'title',
            'course_type',
            'class_level',
            'duration',
            'fee',
            'offer_fee',
            'short_description',
            'description',
            'subjects',
            'eligibility',
            'features',
            'seo_title',
            'seo_description',
            'seo_keywords',
        ]);

        $data['slug'] = $request->filled('slug')
            ? Str::slug($request->slug)
            : $this->generateUniqueSlug($request->title);

        $data['status'] = $request->has('status') ? 'active' : 'inactive';

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        Course::create($data);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course added successfully.');
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('courses', 'slug')->ignore($course->id),
            ],
            'course_type' => ['nullable', 'string', 'max:255'],
            'class_level' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'offer_fee' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'subjects' => ['nullable', 'string'],
            'eligibility' => ['nullable', 'string'],
            'features' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'seo_keywords' => ['nullable', 'string'],
        ]);

        $data = $request->only([
            'title',
            'course_type',
            'class_level',
            'duration',
            'fee',
            'offer_fee',
            'short_description',
            'description',
            'subjects',
            'eligibility',
            'features',
            'seo_title',
            'seo_description',
            'seo_keywords',
        ]);

        $data['slug'] = $request->filled('slug')
            ? Str::slug($request->slug)
            : $this->generateUniqueSlug($request->title, $course->id);

        $data['status'] = $request->has('status') ? 'active' : 'inactive';

        if ($request->hasFile('image')) {
            if ($course->image && Storage::disk('public')->exists($course->image)) {
                Storage::disk('public')->delete($course->image);
            }

            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        $course->update($data);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        if ($course->image && Storage::disk('public')->exists($course->image)) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (
            Course::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}