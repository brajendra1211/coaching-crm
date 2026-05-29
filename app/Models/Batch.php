<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'course_id',
        'teacher_id',
        'subject_id',
        'name',
        'code',
        'class_level',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'days',
        'room_no',
        'capacity',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function students()
    {
        return $this->belongsToMany(Student::class, 'batch_students')
            ->withPivot(['status', 'assigned_at'])
            ->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'batch_teachers')
            ->withPivot(['subject_id', 'role', 'status', 'assigned_at'])
            ->withTimestamps();
    }
    public function feePlans()
    {
        return $this->hasMany(BatchFeePlan::class);
    }

    public function activeFeePlan()
    {
        return $this->hasOne(BatchFeePlan::class)
            ->where('status', 'active')
            ->latestOfMany();
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