<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $fillable = [
        'receipt_no',
        'student_fee_assignment_id',
        'student_id',
        'batch_id',
        'payment_date',
        'amount',
        'discount_amount',
        'fine_amount',
        'total_before_payment',
        'balance_before_payment',
        'balance_after_payment',
        'payment_mode',
        'transaction_id',
        'notes',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'total_before_payment' => 'decimal:2',
        'balance_before_payment' => 'decimal:2',
        'balance_after_payment' => 'decimal:2',
    ];

    public function assignment()
    {
        return $this->belongsTo(StudentFeeAssignment::class, 'student_fee_assignment_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}