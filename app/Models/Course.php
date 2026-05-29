<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'course_type',
        'class_level',
        'duration',
        'fee',
        'offer_fee',
        'image',
        'short_description',
        'description',
        'subjects',
        'eligibility',
        'features',
        'status',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];
}