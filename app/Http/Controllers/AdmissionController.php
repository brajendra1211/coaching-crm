<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Course;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    public function index()
    {
        $courses = Course::where('status', 'active')
            ->orderBy('title')
            ->get();

        return view('frontend.admission', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['nullable', 'exists:courses,id'],
            'course_name' => ['nullable', 'string', 'max:255'],
            'class_level' => ['nullable', 'string', 'max:255'],
            'student_name' => ['required', 'string', 'max:255'],
            'student_phone' => ['required', 'string', 'max:30'],
            'student_email' => ['nullable', 'email', 'max:255'],
            'dob' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:30'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_relation' => ['nullable', 'string', 'max:100'],
            'parent_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $course = null;

        if (!empty($data['course_id'])) {
            $course = Course::find($data['course_id']);
            $data['course_name'] = $course?->title ?: ($data['course_name'] ?? null);
        }

        $admission = Admission::create(array_merge($data, [
            'admission_no' => $this->generateAdmissionNo(),
            'admission_date' => now(),
            'source' => 'website_admission',
            'status' => 'new',
            'registration_fee' => 0,
            'admission_fee' => 0,
        ]));

        return redirect()
            ->route('admission.index')
            ->with('success', 'Admission application submitted successfully. Your application number is ' . $admission->admission_no . '.');
    }

    private function generateAdmissionNo(): string
    {
        $prefix = 'ADM-' . date('Y') . '-';

        $last = Admission::where('admission_no', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $next = 1;

        if ($last) {
            $number = (int) str_replace($prefix, '', $last->admission_no);
            $next = $number + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
