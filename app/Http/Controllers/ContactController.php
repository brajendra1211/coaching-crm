<?php

namespace App\Http\Controllers;

use App\Models\CoachingSetting;
use App\Models\Course;
use App\Models\Lead;
use App\Models\WebsitePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        $courses = Course::where('status', 'active')
            ->orderBy('title')
            ->get();

        $page = WebsitePage::where('status', 'active')
            ->where(function ($query) {
                $query->where('slug', 'contact')
                    ->orWhere('page_type', 'contact');
            })
            ->orderByRaw("CASE WHEN slug = 'contact' THEN 0 ELSE 1 END")
            ->first();

        return view('frontend.contact', compact('courses', 'page'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['nullable', 'exists:courses,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'class_level' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:1500'],
        ]);

        $lead = Lead::create([
            'course_id' => $data['course_id'] ?? null,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'class_level' => $data['class_level'] ?? null,
            'source' => 'website_contact',
            'message' => $data['message'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 250),
        ]);

        $this->sendEnquiryMail($lead);

        return redirect()
            ->route('contact.index')
            ->with('success', 'Thank you! Your enquiry has been submitted. Our team will contact you shortly.');
    }

    private function sendEnquiryMail(Lead $lead): void
    {
        $setting = CoachingSetting::current();
        $recipient = $setting->enquiry_email ?: $setting->email;

        if (!$recipient) {
            return;
        }

        try {
            Mail::send('emails.contact-enquiry', [
                'lead' => $lead->load('course'),
                'setting' => $setting,
            ], function ($message) use ($lead, $recipient, $setting) {
                $message->to($recipient)
                    ->subject('New Website Enquiry - ' . $lead->name);

                if ($lead->email) {
                    $message->replyTo($lead->email, $lead->name);
                }

                if ($setting->email) {
                    $message->from(config('mail.from.address'), $setting->institute_name ?: config('mail.from.name'));
                }
            });
        } catch (\Throwable $e) {
            Log::warning('Contact enquiry email failed.', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
