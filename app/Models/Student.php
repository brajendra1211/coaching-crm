<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'admission_id',
        'course_id',
        'student_code',
        'name',
        'phone',
        'email',
        'dob',
        'gender',
        'class_level',
        'course_name',
        'address',
        'city',
        'state',
        'pincode',
        'photo',
        'status',
        'joining_date',
    ];

    protected $casts = [
        'dob' => 'date',
        'joining_date' => 'date',
    ];

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }

    public function parent()
    {
        return $this->hasOne(StudentParent::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'batch_students')
            ->withPivot(['status', 'assigned_at'])
            ->withTimestamps();
    }
    public function feeAssignments()
    {
        return $this->hasMany(StudentFeeAssignment::class);
    }
    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }
}