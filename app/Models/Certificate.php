<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'certificate_no',
        'student_id',
        'recipient_name',
        'student_code',
        'certificate_title',
        'certificate_type',
        'course_name',
        'class_level',
        'batch_name',
        'issue_date',
        'completion_date',
        'grade',
        'duration',
        'description',
        'remarks',
        'template',
        'signed_by',
        'signature_title',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'completion_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}