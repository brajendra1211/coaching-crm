<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    protected $fillable = [
        'lead_id',
        'course_id',
        'admission_no',
        'admission_date',
        'student_name',
        'student_phone',
        'student_email',
        'dob',
        'gender',
        'class_level',
        'course_name',
        'parent_name',
        'parent_relation',
        'parent_phone',
        'parent_email',
        'address',
        'city',
        'state',
        'pincode',
        'previous_school',
        'source',
        'registration_fee',
        'admission_fee',
        'status',
        'notes',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'dob' => 'date',
        'registration_fee' => 'decimal:2',
        'admission_fee' => 'decimal:2',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function parent()
    {
        return $this->hasOne(StudentParent::class);
    }
}