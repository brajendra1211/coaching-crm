<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchFeePlan extends Model
{
    protected $fillable = [
        'batch_id',
        'title',
        'billing_type',
        'registration_fee',
        'admission_fee',
        'tuition_fee',
        'exam_fee',
        'material_fee',
        'other_fee',
        'due_day',
        'fine_per_day',
        'effective_from',
        'effective_to',
        'notes',
        'status',
    ];

    protected $casts = [
        'registration_fee' => 'decimal:2',
        'admission_fee' => 'decimal:2',
        'tuition_fee' => 'decimal:2',
        'exam_fee' => 'decimal:2',
        'material_fee' => 'decimal:2',
        'other_fee' => 'decimal:2',
        'fine_per_day' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function assignments()
    {
        return $this->hasMany(StudentFeeAssignment::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->registration_fee
            + (float) $this->admission_fee
            + (float) $this->tuition_fee
            + (float) $this->exam_fee
            + (float) $this->material_fee
            + (float) $this->other_fee;
    }
}