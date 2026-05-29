<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachingSetting extends Model
{
    protected $fillable = [
        'coaching_id',
        'institute_name',
        'tagline',
        'logo',
        'favicon',
        'phone',
        'whatsapp',
        'email',
        'enquiry_email',
        'mail_from_name',
        'mail_from_address',
        'gmail_address',
        'gmail_app_password',
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
        'home_hero_image',
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
    ];

    protected $casts = [
        'gmail_app_password' => 'encrypted',
    ];

    public static function current(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'institute_name' => 'Edu Institute',
                'tagline' => 'Coaching | Exams | Results',
                'phone' => '9999999999',
                'whatsapp' => '919999999999',
                'email' => 'info@coachingcrm.com',
                'enquiry_email' => 'info@coachingcrm.com',
                'mail_from_name' => 'Edu Institute',
                'mail_from_address' => 'info@coachingcrm.com',
                'address' => 'Greater Noida, Noida Extension',
                'footer_description' => 'Professional coaching for NEET, IIT JEE, board exams and competitive preparation with expert faculty, regular tests and structured learning support.',
                'primary_color' => '#2563eb',
                'secondary_color' => '#7c3aed',
                'accent_color' => '#16a34a',
                'font_family' => 'Arial',
                'home_hero_title' => 'Build Strong Concepts for NEET, IIT JEE & Board Exams',
                'home_hero_highlight' => 'Result-Focused Coaching',
                'home_hero_subtitle' => 'Join structured coaching with expert faculty, regular tests, doubt sessions, study material and personal academic guidance.',
                'students_count' => '500+',
                'courses_count' => '20+',
                'tests_count' => '50+',
                'rating' => '4.8/5',
                'default_seo_title' => 'Best Coaching Institute',
                'default_seo_description' => 'Professional coaching institute for NEET, IIT JEE, Board Exams and competitive preparation.',
            ]
        );
    }
}
