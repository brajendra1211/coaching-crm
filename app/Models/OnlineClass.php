<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineClass extends Model
{
    protected $fillable = [
        'batch_id',
        'teacher_id',
        'subject_id',
        'title',
        'class_date',
        'start_time',
        'end_time',
        'platform',
        'meeting_link',
        'description',
        'status',
    ];

    protected $casts = [
        'class_date' => 'date',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}