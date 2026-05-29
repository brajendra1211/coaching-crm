<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => ['nullable', 'exists:courses,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'class_level' => ['nullable', 'string', 'max:100'],
            'source' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        Lead::create([
            'course_id' => $request->course_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'class_level' => $request->class_level,
            'source' => $request->source,
            'message' => $request->message,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 250),
        ]);

        return back()->with('success', 'Thank you! Your enquiry has been submitted successfully. Our team will contact you soon.');
    }
}