<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoachingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CoachingSettingController extends Controller
{
    public function edit()
    {
        $setting = CoachingSetting::current();

        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = CoachingSetting::current();

        $request->validate([
            'institute_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],

            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,ico', 'max:1024'],
            'home_hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'enquiry_email' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'gmail_address' => ['nullable', 'email', 'max:255'],
            'gmail_app_password' => ['nullable', 'string', 'max:255'],

            'address' => ['nullable', 'string'],
            'google_map_url' => ['nullable', 'string'],

            'footer_description' => ['nullable', 'string'],
            'primary_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'accent_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'font_family' => ['nullable', 'string', 'max:80'],
            'home_hero_title' => ['nullable', 'string', 'max:255'],
            'home_hero_highlight' => ['nullable', 'string', 'max:255'],
            'home_hero_subtitle' => ['nullable', 'string'],
            'home_seo_title' => ['nullable', 'string', 'max:255'],
            'home_seo_description' => ['nullable', 'string'],
            'home_seo_keywords' => ['nullable', 'string'],
            'students_count' => ['nullable', 'string', 'max:50'],
            'courses_count' => ['nullable', 'string', 'max:50'],
            'tests_count' => ['nullable', 'string', 'max:50'],
            'rating' => ['nullable', 'string', 'max:50'],

            'facebook_url' => ['nullable', 'string', 'max:255'],
            'instagram_url' => ['nullable', 'string', 'max:255'],
            'youtube_url' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'string', 'max:255'],

            'default_seo_title' => ['nullable', 'string', 'max:255'],
            'default_seo_description' => ['nullable', 'string'],
            'default_seo_keywords' => ['nullable', 'string'],
        ]);

        $data = $request->only([
            'institute_name',
            'tagline',
            'phone',
            'whatsapp',
            'email',
            'enquiry_email',
            'mail_from_name',
            'mail_from_address',
            'gmail_address',
            'address',
            'google_map_url',
            'footer_description',
            'primary_color',
            'secondary_color',
            'accent_color',
            'font_family',
            'home_hero_title',
            'home_hero_highlight',
            'home_hero_subtitle',
            'home_seo_title',
            'home_seo_description',
            'home_seo_keywords',
            'students_count',
            'courses_count',
            'tests_count',
            'rating',
            'facebook_url',
            'instagram_url',
            'youtube_url',
            'linkedin_url',
            'default_seo_title',
            'default_seo_description',
            'default_seo_keywords',
        ]);

        if ($request->filled('gmail_app_password')) {
            $data['gmail_app_password'] = preg_replace('/\s+/', '', (string) $request->gmail_app_password);
        }

        if ($request->hasFile('logo')) {
            if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
                Storage::disk('public')->delete($setting->logo);
            }

            $data['logo'] = $request->file('logo')->store('settings', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon && Storage::disk('public')->exists($setting->favicon)) {
                Storage::disk('public')->delete($setting->favicon);
            }

            $data['favicon'] = $request->file('favicon')->store('settings', 'public');
        }

        if ($request->hasFile('home_hero_image')) {
            if ($setting->home_hero_image && Storage::disk('public')->exists($setting->home_hero_image)) {
                Storage::disk('public')->delete($setting->home_hero_image);
            }

            $data['home_hero_image'] = $request->file('home_hero_image')->store('settings/home-hero', 'public');
        }

        $setting->update($data);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Website settings updated successfully.');
    }
}
