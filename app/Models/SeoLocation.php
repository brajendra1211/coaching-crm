<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoLocation extends Model
{
    protected $fillable = [
        'location_name',
        'focus_keyword',
        'page_title',
        'slug',
        'short_description',
        'content',
        'cta_title',
        'cta_description',
        'status',
        'sort_order',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];
}