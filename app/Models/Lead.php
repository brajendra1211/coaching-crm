<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'phone',
        'email',
        'class_level',
        'source',
        'message',
        'status',
        'follow_up_date',
        'admin_notes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}