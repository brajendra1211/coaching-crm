<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsitePage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'page_type',
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'content',
        'cta_title',
        'cta_description',
        'show_enquiry_form',
        'status',
        'sort_order',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $casts = [
        'show_enquiry_form' => 'boolean',
    ];
}