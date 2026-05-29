<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFeeAssignment extends Model
{
    protected $fillable = [
        'student_id',
        'batch_id',
        'batch_fee_plan_id',
        'billing_type',
        'registration_fee',
        'admission_fee',
        'tuition_fee',
        'exam_fee',
        'material_fee',
        'other_fee',
        'total_amount',
        'paid_amount',
        'discount_amount',
        'fine_amount',
        'balance_amount',
        'due_day',
        'next_due_date',
        'assigned_at',
        'status',
    ];

    protected $casts = [
        'registration_fee' => 'decimal:2',
        'admission_fee' => 'decimal:2',
        'tuition_fee' => 'decimal:2',
        'exam_fee' => 'decimal:2',
        'material_fee' => 'decimal:2',
        'other_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'next_due_date' => 'date',
        'assigned_at' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function feePlan()
    {
        return $this->belongsTo(BatchFeePlan::class, 'batch_fee_plan_id');
    }

    public function payments()
    {
        return $this->hasMany(FeePayment::class, 'student_fee_assignment_id');
    }
}